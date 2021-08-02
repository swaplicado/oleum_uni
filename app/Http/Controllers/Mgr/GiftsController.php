<?php

namespace App\Http\Controllers\Mgr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use App\Uni\Gift;

class GiftsController extends Controller
{
    protected $newRoute;
    protected $storeRoute;

    /**
     * Create a new controller instance.
     *
     * @param  UserRepository  $users
     * @return void
     */
    public function __construct()
    {
        $this->newRoute = "gifts.create";
        $this->storeRoute = "gifts.store";
    }

    public function gifts()
    {
        $lGifts = Gift::where('is_deleted', false)
                        ->get();

        $sDate = Carbon::now()->toDateString();

        foreach ($lGifts as $gift) {
            $gift->stk = \DB::table('uni_gifts_stock AS stk')
                                ->selectRaw('
                                            SUM(increment) AS increments, 
                                            SUM(decrement) AS decrements, 
                                            (SUM(increment) - SUM(decrement)) AS d_stk
                                        ')
                                ->where('stk.gift_id', $gift->id_gift)
                                ->where('stk.is_deleted', false)
                                ->where('dt_date', '<=', $sDate)
                                ->groupBy('stk.gift_id')
                                ->first();
        }
        
        return view('mgr.gifts.index')->with('lGifts', $lGifts)
                                    ->with('newRoute', $this->newRoute);        
    }

    public function createGift()
    {
        return view('mgr.gifts.create')->with('storeRoute', $this->storeRoute);
    }

    public function storeGift(Request $request)
    {
        $txtGalleryName = "gifts";

        if($request->hasfile('images'))
        {
            $imageNames = "";
            foreach($request->file('images') as $key => $img)
            {
                $name = $img->getClientOriginalName();

                // you can also use the original name
                $imageName = time().'-'.$img->getClientOriginalName();
                $imageNameArr[] = $imageName;
                // Upload file to public path in images directory

                $img->move(public_path('images/gifts'), $imageName);
                $imageNames = $imageNames."__".'images/gifts/'.$imageName;
            }
        }

        $oGift = new Gift();

        $oGift->code = $request->code;
        $oGift->gift = $request->gift;
        $oGift->description = $request->description;
        $oGift->images = substr($imageNames, 2);
        $oGift->points_value = $request->points;
        $oGift->is_active = true;
        $oGift->is_deleted = false;
        $oGift->created_by_id = \Auth::id();
        $oGift->updated_by_id = \Auth::id();

        $oGift->save();

        return redirect()->route('gifts.index')->with("success","El premio se dio de alta exitosamente.");
    }
}
