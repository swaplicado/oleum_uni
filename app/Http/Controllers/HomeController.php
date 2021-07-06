<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $title = "¡Bienvenido ".(\Auth::user()->names)."!";

        $lAssignments = \DB::table('uni_assignments AS a')
                            ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                            ->where('a.student_id', \Auth::id())
                            ->where('a.is_deleted', false)
                            ->where('a.is_over', false)
                            ->get();

        

        return view('home')->with('title', $title)
                            ->with('lAssignments', $lAssignments);
    }
}
