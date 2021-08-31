<?php

namespace App\Http\Controllers\Uni;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

use App\Mail\GiftExchanged;
use App\Uni\PointsControl;
use App\Uni\GiftStock;
use App\Uni\Gift;

class ShopController extends Controller
{
    public function index()
    {
        $sDate = Carbon::now()->toDateString();

        $lStock = \DB::table('uni_gifts_stock AS stk')
                    ->join('uni_gifts AS g', 'stk.gift_id', '=', 'g.id_gift')
                    ->selectRaw('
                                SUM(increment) AS increments, 
                                SUM(decrement) AS decrements, 
                                (SUM(increment) - SUM(decrement)) AS d_stk,
                                stk.gift_id,
                                g.points_value,
                                g.gift,
                                g.description,
                                g.images
                            ')
                    ->where('stk.is_deleted', false)
                    ->where('g.is_deleted', false)
                    ->where('g.is_active', true)
                    ->where('dt_date', '<=', $sDate)
                    ->groupBy('stk.gift_id')
                    ->get();

        foreach ($lStock as $oStock) {
            $images = $oStock->images;
            $aImages = explode("__", $images);

            $oStock->lImages = $aImages;
        }

        $oPoints = PointsControl::where('student_id', \Auth::id())
                        ->selectRaw('
                            SUM(increment) AS increments, 
                            SUM(decrement) AS decrements, 
                            (SUM(increment) - SUM(decrement)) AS points
                        ')
                        ->where('is_deleted', false)
                        ->where('dt_date', '<=', $sDate)
                        ->groupBy('student_id')
                        ->first();


        return view('uni.shop.index')->with('lStock', $lStock)
                                    ->with('oPoints', $oPoints);
    }

    public function exchange(Request $request)
    {
        $idGift = $request->id_gift;
        $sDate = Carbon::now()->toDateString();

        $oStock = \DB::table('uni_gifts_stock AS stk')
                    ->join('uni_gifts AS g', 'stk.gift_id', '=', 'g.id_gift')
                    ->selectRaw('
                                SUM(increment) AS increments, 
                                SUM(decrement) AS decrements, 
                                (SUM(increment) - SUM(decrement)) AS d_stk,
                                stk.gift_id,
                                g.points_value,
                                g.gift,
                                g.description,
                                g.images
                            ')
                    ->where('stk.gift_id', $idGift)
                    ->where('stk.is_deleted', false)
                    ->where('g.is_deleted', false)
                    ->where('g.is_active', true)
                    ->where('dt_date', '<=', $sDate)
                    ->groupBy('stk.gift_id')
                    ->first();

        if ($oStock == null) {
            return redirect()->route('shop')->withError("Ya no hay premios disponibles.");
        }

        $oPoints = PointsControl::where('student_id', \Auth::id())
                        ->selectRaw('
                            SUM(increment) AS increments, 
                            SUM(decrement) AS decrements, 
                            (SUM(increment) - SUM(decrement)) AS points
                        ')
                        ->where('is_deleted', false)
                        ->where('dt_date', '<=', $sDate)
                        ->groupBy('student_id')
                        ->first();

        if ($oPoints->points < $oStock->points_value) {
            return redirect()->route('shop')->withError("Los puntos que tienes no son suficientes para canjear este premio.");
        }

        try {
            \DB::beginTransaction();

            $stock = new GiftStock();
            $stock->dt_date = $sDate;
            $stock->increment = 0;
            $stock->decrement = 1;
            $stock->comments = "";
            $stock->is_deleted = false;
            $stock->mov_class = "mov_out";
            $stock->mov_type_id = 3;
            $stock->gift_id = $idGift;
            $stock->student_n_id = \Auth::id();
            $stock->created_by_id = \Auth::id();
            $stock->updated_by_id = \Auth::id();

            $stock->save();

            $points = new PointsControl();
            $points->dt_date = $sDate;
            $points->increment = 0;
            $points->decrement = $oStock->points_value;
            $points->comments = "";
            $points->is_deleted = false;
            $points->mov_class = "mov_out";
            $points->mov_type_id = 4;
            $points->taken_control_n_id = null;
            $points->gift_stk_n_id = $stock->id_stock;
            $points->student_id = \Auth::id();
            $points->created_by_id = \Auth::id();
            $points->updated_by_id = \Auth::id();

            $points->save();

            // Notificación
            $this->sendExchangeNotification($idGift, $sDate);

            \DB::commit();
        }
        catch (\Throwable $th) {
            \DB::rollBack();

            return redirect()->route('shop')->withError($th->getMessage());
        }

        return redirect()->route('shop')->with("success", "Canjeaste tu premio, acude con el administrador de la tienda.");
    }

    private function sendExchangeNotification($idGift, $sDate)
    {
        $oPoints = PointsControl::where('student_id', \Auth::id())
                        ->selectRaw('
                            SUM(increment) AS increments, 
                            SUM(decrement) AS decrements, 
                            (SUM(increment) - SUM(decrement)) AS points
                        ')
                        ->where('is_deleted', false)
                        ->where('dt_date', '<=', $sDate)
                        ->groupBy('student_id')
                        ->first();

        $rec = [];
        if (strlen(\Auth::user()->email) > 0) {
            $ua = [];
            $ua['email'] = \Auth::user()->email;
            $ua['name'] = \Auth::user()->full_name;

            $rec[] = (object) $ua;
        }

        $rec[] = env('MAIL_EXCHANGE_NOTIF');

        Mail::to($rec)->send(new GiftExchanged($idGift, $oPoints->points));

        return;
    }

    public function preview()
    {
        return view('mails.adm.giftexchanged');
    }
}
