<?php

namespace App\Http\Controllers\Mgr;

use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Uni\PointsControl;

class PointsController extends Controller
{
    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        $lPoints = \DB::table('uni_points_control AS pts')
                        ->join('users AS u', 'pts.student_id', '=', 'u.id')
                        ->join('sys_points_mov_types AS pt', 'pts.mov_type_id', '=', 'pt.id_mov_type')
                        ->leftJoin('uni_taken_controls AS ctrls', 'pts.taken_control_n_id', '=', 'ctrls.id_taken_control')
                        ->leftJoin('uni_courses AS cou', 'ctrls.course_n_id', '=', 'cou.id_course')
                        ->leftJoin('uni_gifts_stock AS stk', 'pts.gift_stk_n_id', '=', 'stk.id_stock')
                        ->leftJoin('uni_gifts AS g', 'stk.gift_id', '=', 'g.id_gift')
                        ->selectRaw('pts.*, movement_type, course, gift, u.*, SUM(pts.increment) AS t_increments, SUM(pts.decrement) AS t_decrements')
                        ->where('pts.is_deleted', false)
                        ->where('u.is_deleted', false)
                        ->where('u.is_active', true)
                        ->where('pts.dt_date', '<=', Carbon::now()->toDateString())
                        ->groupBy('pts.student_id')
                        ->get();

        $inMovTypes = \DB::table('sys_points_mov_types')->where('mov_class', 'mov_in')->get();
        $outMovTypes = \DB::table('sys_points_mov_types')->where('mov_class', 'mov_out')->get();

        $title = "Control de puntos";

        return view('mgr.points.index')->with('lPoints', $lPoints)
                                        ->with('inMovTypes', $inMovTypes)
                                        ->with('outMovTypes', $outMovTypes)
                                        ->with('title', $title)
                                        ->with('sStoreRoute', 'points.store')
                                        ->with('sGetRoute', 'points.detail');
    }

    /**
     * Undocumented function
     *
     * @param integer $idStudent
     * @return void
     */
    public function getDetail($idStudent = 0)
    {
        $lPoints = \DB::table('uni_points_control AS pts')
                        ->join('users AS u', 'pts.student_id', '=', 'u.id')
                        ->join('sys_points_mov_types AS pt', 'pts.mov_type_id', '=', 'pt.id_mov_type')
                        ->leftJoin('uni_taken_controls AS ctrls', 'pts.taken_control_n_id', '=', 'ctrls.id_taken_control')
                        ->leftJoin('uni_courses AS cou', 'ctrls.course_n_id', '=', 'cou.id_course')
                        ->leftJoin('uni_gifts_stock AS stk', 'pts.gift_stk_n_id', '=', 'stk.id_stock')
                        ->leftJoin('uni_gifts AS g', 'stk.gift_id', '=', 'g.id_gift')
                        ->selectRaw('pts.*, movement_type, course, gift, u.*');

        if ($idStudent > 0) {
            $lPoints = $lPoints->where('pts.student_id', $idStudent);
        }

        $lPoints = $lPoints->where('pts.is_deleted', false)
                            ->where('u.is_deleted', false)
                            ->where('u.is_active', true)
                            ->where('pts.dt_date', '<=', Carbon::now()->toDateString())
                            ->orderBy('pts.dt_date', 'ASC')
                            ->orderBy('pts.id_points_control', 'ASC')
                            ->get();

        $i = 1;
        foreach ($lPoints as $pointRow) {
            $pointRow->index = $i;
            $i++;
        }

        return json_encode($lPoints);
    }

    public function store(Request $request)
    {
        $mov = new PointsControl();

        $mov->dt_date = Carbon::now()->toDateString();

        if ($request->mov_class == '1') {
            $mov->increment = $request->points;
            $mov->decrement = 0;
            $mov->mov_class = 'mov_in';
        }
        else {
            $mov->increment = 0;
            $mov->decrement = $request->points;
            $mov->mov_class = 'mov_out';
        }

        $mov->comments = $request->comments;
        $mov->is_deleted = false;
        $mov->mov_type_id = $request->mov_type;
        $mov->taken_control_n_id = null;
        $mov->gift_stk_n_id = null;
        $mov->student_id = $request->id_student;
        $mov->created_by_id = \Auth::id();
        $mov->updated_by_id = \Auth::id();

        $mov->save();

        return json_encode($mov);
    }
}
