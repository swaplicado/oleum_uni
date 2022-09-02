<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Sys\SyncController;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Utils\TakeUtils;
use App\Uni\Carousel;
use App\Uni\EduContent;
use App\Uni\ElementContent;

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
                            ->leftJoin('uni_contents_vs_elements as ce', 'ce.knowledge_area_n_id', '=', 'ka.id_knowledge_area')
                            ->where('a.student_id', \Auth::id())
                            ->where('a.is_deleted', false)
                            ->where('a.is_closed', 0)
                            ->where('a.dt_assignment', '<=', Carbon::now()->toDateString())
                            ->where('a.dt_end', '>=', Carbon::today()->toDateString())
                            ->get();

        foreach($lAssignments as $area){
            $result = TakeUtils::getlAssignmentPercentCompleted($area->id_assignment, $area->id_knowledge_area, \Auth::id());
            $area->completed_percent = number_format($result[0]);

            $oContent = EduContent::find($area->content_id);
            if(!is_null($oContent)){
                $url = asset($oContent->file_path);
                $path = str_replace("public", "", $url);
                $path = str_replace("storage", "storage/app", $path);
            }else{
                $path = "img/uvaeth_logo.jpg";
            }
            $area->content_path = $path;
        }
        $lCourses = TakeUtils::getTakingCourses(\Auth::id());

        foreach ($lCourses as $course) {
            $course->completed_percent = TakeUtils::getCoursePercentCompleted($course->id_course, \Auth::id(), $course->assignment_id);

            $content_id = \DB::table('uni_contents_vs_elements as ce') 
                            ->where('course_n_id', $course->id_course)
                            ->value('id');

            $oContent = EduContent::find($content_id);
            if(!is_null($oContent)){
                if($oContent->file_type == "image"){
                    $url = asset($oContent->file_path);
                    $path = str_replace("public", "", $url);
                    $path = str_replace("storage", "storage/app", $path);
                }else{
                    $path = "img/uvaeth_logo.jpg";    
                }
            }else{
                $path = "img/uvaeth_logo.jpg";
            }
            $course->content_path = $path;
        }

        return view('home')->with('title', $title)
                            ->with('lCarousel', $lCarousel)
                            ->with('lCourses', $lCourses)
                            ->with('lAssignments', $lAssignments);
    }
}
