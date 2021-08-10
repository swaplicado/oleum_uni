<?php

namespace App\Http\Controllers\Mgr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\Uni\GiftStock;

class GiftsStockController extends Controller
{
    public function create($movClass, $idGift)
    {
        $movTypes = \DB::table('sys_stk_mov_types AS st')
                            ->where('st.mov_class', $movClass)
                            ->get();

        return view('mgr.gifts.createstk')->with('movTypes', $movTypes)
                                        ->with('idGift', $idGift)
                                        ->with('movClass', $movClass)
                                        ->with('storeRoute', 'giftstk.store');
    }

    public function store(Request $request)
    {
        $oStock = new GiftStock($request->all());
        
        $oStock->dt_date = Carbon::now()->toDateString();
        $oStock->increment = $request->mov_class == 'mov_in' ? $request->quantity : 0;
        $oStock->decrement = $request->mov_class == 'mov_out' ? $request->quantity : 0;    
        $oStock->is_deleted = false;
        $oStock->student_n_id = null;
        $oStock->created_by_id = \Auth::id();
        $oStock->updated_by_id = \Auth::id();

        $oStock->save();

        return redirect()->route('gifts.index')->with("success", "Alta de premio exitosa");
    }
}
