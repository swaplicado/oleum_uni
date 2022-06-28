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
        $title = "¡Bienvenid@ ".ucwords(strtolower((\Auth::user()->names)))."!";

        $lCarousel = \DB::table('uni_carousel AS c')
                            ->leftJoin('uni_edu_contents AS ec', 'c.content_n_id', '=', 'ec.id_content')
                            ->where('c.is_deleted', false)
                            ->where('c.is_active', true)
                            ->get();

        foreach ($lCarousel as $video) {
            if ($video->content_n_id == null) {
                continue;
            }
            
            $url = asset($video->file_path);
            $path = str_replace("public", "", $url);
            $path = str_replace("storage", "storage/app", $path);

            $video->path = $path;
        }

        $lAssignments = \DB::table('uni_assignments AS a')
                            ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                            ->where('a.student_id', \Auth::id())
                            ->where('a.is_deleted', false)
                            ->whereRaw('NOW() BETWEEN a.dt_assignment AND a.dt_end')
                            ->get();
        foreach($lAssignments as $area){
            $result = TakeUtils::getlAssignmentPercentCompleted($area->id_assignment, $area->id_knowledge_area, \Auth::id());
            $area->completed_percent = number_format($result[0]);
        }
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
