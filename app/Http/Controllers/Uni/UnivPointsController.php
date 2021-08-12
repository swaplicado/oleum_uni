<?php

namespace App\Http\Controllers\Uni;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use App\Uni\PointsControl;

class UnivPointsController extends Controller
{
    public function registryPoints($points, $controlId)
    {
        $oControl = new PointsControl();

        $oControl->dt_date = Carbon::now()->toDateString();
        $oControl->increment = $points;
        $oControl->decrement = 0;
        $oControl->comments = "";
        $oControl->is_deleted = false;
        $oControl->mov_class = 'mov_in';
        $oControl->mov_type_id = 1;
        $oControl->taken_control_n_id = $controlId;
        $oControl->gift_stk_n_id  = null;
        $oControl->student_id = \Auth::id();
        $oControl->created_by_id = \Auth::id();
        $oControl->updated_by_id = \Auth::id();

        $oControl->save();

        return $oControl;
    }
}
