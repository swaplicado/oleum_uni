<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Sys\SyncController;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Utils\TakeUtils;
use App\Uni\Carousel;

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

        $sync = new SyncController();
        $sync->toSynchronize(false);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $title = "¡Bienvenid@ ".(\Auth::user()->names)."!";

        $lCarousel = Carousel::where('is_deleted', false)->where('is_active', true)->get();

        $lAssignments = \DB::table('uni_assignments AS a')
                            ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                            ->where('a.student_id', \Auth::id())
                            ->where('a.is_deleted', false)
                            ->where('a.is_over', false)
                            ->get();

        $lCourses = TakeUtils::getTakingCourses(\Auth::id());

        foreach ($lCourses as $course) {
            $course->completed_percent = TakeUtils::getCoursePercentCompleted($course->id_course, \Auth::id(), $course->assignment_id);
        }

        return view('home')->with('title', $title)
                            ->with('lCarousel', $lCarousel)
                            ->with('lCourses', $lCourses)
                            ->with('lAssignments', $lAssignments);
    }
}
