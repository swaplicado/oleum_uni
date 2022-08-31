<?php

namespace App\Http\Controllers\Uni;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\Utils\TakeUtils;

use App\Uni\KnowledgeArea;
use App\Uni\Module;
use App\Uni\Assignment;
use App\Uni\Topic;
use App\Uni\SubTopic;
use App\Uni\TakingControl;
use App\Uni\ReviewCfg;
use App\Uni\Review;
use App\Uni\ModuleControl;
class UniversityController extends Controller
{
    public function indexAreas()
    {
        $lAssignments = \DB::table('uni_assignments AS a')
                            ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                            ->where('a.student_id', \Auth::id())
                            ->where('a.is_deleted', false)
                            // ->where('a.is_over', false)
                            ->where('a.dt_assignment', '<=', Carbon::now()->toDateString())
                            ->where('a.dt_end', '>=', Carbon::now()->toDateString())
                            ->get();

        foreach($lAssignments as $assignment){            
            $result = TakeUtils::getlAssignmentPercentCompleted($assignment->id_assignment, $assignment->id_knowledge_area);
            $assignment->completed_percent = $result[0];
        }

        return view('uni.areas.index')->with('lAssignments', $lAssignments)
                                        ->with('lContents', []);
    }

    public function indexModules($assignment = 0, $area = 0)
    {
        $oAssigment = Assignment::find($assignment);

        if(!(Carbon::today()->lte(Carbon::parse($oAssigment->dt_end)))){
            return redirect(route('home'))->with(['message' => 'El cuadrante está cerrado', 'icon' => 'error']);
        }

        $lModules = \DB::table('uni_assignments AS a')
                        ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                        ->join('uni_assignments_module_control as mc', 'mc.assignment_id', '=', 'a.id_assignment')
                        ->join('uni_modules AS m', 'm.id_module', '=', 'mc.module_n_id')
                        ->select('m.*', 'mc.dt_open', 'mc.dt_close','a.id_assignment', 'a.dt_end')
                        ->where('a.id_assignment', $assignment)
                        ->where('a.is_deleted', false)
                        // ->where('a.is_over', false)
                        ->where('a.student_id', \Auth::id())
                        ->where('m.is_deleted', false)
                        ->where('m.knowledge_area_id', $area)
                        ->where('m.elem_status_id', '>', config('csys.elem_status.EDIT'))
                        ->where([['mc.dt_open', '<=', Carbon::today()->toDateString()],['mc.dt_close',  '>=', Carbon::today()->toDateString()]])
                        ->where('mc.is_deleted', 0)
                        ->where('a.dt_assignment', '<=', Carbon::now()->toDateString())
                        ->where('a.dt_end', '>=', Carbon::now()->toDateString())
                        ->groupBy('m.id_module')
                        ->get();

        $oKa = KnowledgeArea::find($area);

        foreach($lModules as $module){
            $result = TakeUtils::getModulePercentCompleted($module->id_module, $assignment);
            $module->completed_percent = $result[0];
        }
        
        return view('uni.modules.index')->with('lModules', $lModules)
                                        ->with('knowledgeArea', $oKa->knowledge_area);
    }

    public function indexCourses($assignment, $module)
    {
        
        $oAssigment = Assignment::find($assignment);
        
        if(!(Carbon::today()->lte(Carbon::parse($oAssigment->dt_end)))){
            return redirect(route('home'))->with(['message' => 'El cuadrante está cerrado', 'icon' => 'error']);
        }
        
        $oModuleControl = ModuleControl::where([
                                            ['assignment_id', $assignment],
                                            ['module_n_id', $module],
                                            ['is_deleted', 0]
                                        ])->first();

        if(Carbon::parse($oModuleControl->dt_close)->lt(Carbon::today())){
            return redirect(route('home'))->with(['message' => 'El módulo está cerrado', 'icon' => 'error']);
        }

        $lCourses = \DB::table('uni_assignments AS a')
                        ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                        ->join('uni_modules AS m', 'ka.id_knowledge_area', '=', 'm.knowledge_area_id')
                        ->join('uni_courses AS c', 'm.id_module', '=', 'c.module_id')
                        ->join('uni_assignments_courses_control as acc', function($join)
                        {
                            $join->on('acc.assignment_id', '=', 'a.id_assignment');
                            $join->on('acc.module_n_id', '=', 'm.id_module');
                            $join->on('acc.course_n_id', '=', 'c.id_course');
                        })
                        ->select('c.*', 'a.id_assignment', 'acc.dt_open', 'acc.dt_close')
                        ->where('a.id_assignment', $assignment)
                        ->where('a.is_deleted', false)
                        // ->where('a.is_over', false)
                        ->where('a.student_id', \Auth::id())
                        ->where('m.is_deleted', false)
                        ->where('c.is_deleted', false)
                        ->where('c.module_id', $module)
                        ->where('c.elem_status_id', '>', config('csys.elem_status.EDIT'))
                        ->where([['acc.dt_open', '<=', Carbon::today()->toDateString()],['acc.dt_close',  '>=', Carbon::today()->toDateString()]])
                        ->where('acc.is_deleted', 0)
                        ->where('a.dt_assignment', '<=', Carbon::now()->toDateString())
                        ->where('a.dt_end', '>=', Carbon::now()->toDateString())
                        ->groupBy('id_course')
                        ->get();

        $oModule = Module::find($module);

        foreach ($lCourses as $course) {
            $course->completed_percent = TakeUtils::getCoursePercentCompleted($course->id_course, \Auth::id(), $course->id_assignment);

            $oContent = \DB::table('uni_contents_vs_elements AS ce')
                            ->join('uni_edu_contents AS c', 'ce.content_id', '=', 'c.id_content')
                            ->where('element_type_id', config('csys.elem_type.COURSE'))
                            ->where('course_n_id', $course->id_course)
                            ->orderBy('order', 'ASC')
                            ->first();

            if ($oContent == null) {
                continue;
            }

            $url = asset($oContent->file_path);
            $path = str_replace("public", "", $url);
            $path = str_replace("storage", "storage/app", $path);
            
            $oContent->view_path = $path;
            $course->cover = $oContent;
        }

        return view('uni.courses.index')->with('lCourses', $lCourses)
                                        ->with('module', $oModule->module);
    }

    public function viewCourse($course, $assignment)
    {
        $oCourse = \DB::table('uni_assignments AS a')
                        ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                        ->join('uni_modules AS m', 'ka.id_knowledge_area', '=', 'm.knowledge_area_id')
                        ->join('uni_courses AS c', 'm.id_module', '=', 'c.module_id')
                        ->join('uni_assignments_courses_control as acc', function($join)
                        {
                            $join->on('acc.assignment_id', '=', 'a.id_assignment');
                            $join->on('acc.module_n_id', '=', 'm.id_module');
                            $join->on('acc.course_n_id', '=', 'c.id_course');
                        })
                        ->select('c.*', 'a.id_assignment', 'acc.dt_open', 'acc.dt_close')
                        ->where('a.is_deleted', false)
                        // ->where('a.is_over', false)
                        ->where('a.student_id', \Auth::id())
                        ->where('m.is_deleted', false)
                        ->where('c.is_deleted', false)
                        ->where('c.id_course', $course)
                        ->where('a.id_assignment', $assignment)
                        ->where('a.dt_assignment', '<=', Carbon::now()->toDateString())
                        ->where('a.dt_end', '>=', Carbon::now()->toDateString())
                        ->first();

        if(!(Carbon::today()->lte(Carbon::parse($oCourse->dt_close)))){
            return redirect(route('home'))->with(['message' => 'El curso está cerrado', 'icon' => 'error']);
        }

        $oAssigment = Assignment::find($assignment);
        
        if(!(Carbon::today()->lte(Carbon::parse($oAssigment->dt_end)))){
            return redirect(route('home'))->with(['message' => 'El cuadrante está cerrado', 'icon' => 'error']);
        }
        
        $oModuleControl = ModuleControl::where([
                                            ['assignment_id', $assignment],
                                            ['module_n_id', $oCourse->module_id],
                                            ['is_deleted', 0]
                                        ])->first();
                                        
        if(Carbon::parse($oModuleControl->dt_close)->lt(Carbon::today())){
            return redirect(route('home'))->with(['message' => 'El módulo está cerrado', 'icon' => 'error']);
        }

        if ($oCourse != null) {
            $oCourse->lTopics = Topic::where('course_id', $oCourse->id_course)
                                        ->where('is_deleted', false)
                                        ->get();
            
            foreach ($oCourse->lTopics as $topic) {
                $topic->lSubtopics = SubTopic::where('topic_id', $topic->id_topic)
                                            ->where('is_deleted', false)
                                            ->get();

                $topic->ended = null;

                $topicCtrls = TakingControl::where('status_id', '=', config('csys.take_status.COM'))
                                                ->where('element_type_id', config('csys.elem_type.TOPIC'))
                                                ->where('topic_n_id', $topic->id_topic)
                                                ->where('student_id', \Auth::id())
                                                ->whereColumn('grade', '>=', 'min_grade')
                                                ->where('is_deleted', false)
                                                ->where('is_evaluation', false)
                                                ->where('assignment_id', $oCourse->id_assignment)
                                                ->orderBy('grade', 'DESC')
                                                ->get();

                if (count($topicCtrls) > 0) {
                    $topic->ended = $topicCtrls;
                }

                foreach ($topic->lSubtopics as $subTopic) {
                    $controls = TakingControl::where('status_id', '=', config('csys.take_status.COM'))
                                                ->where('element_type_id', config('csys.elem_type.SUBTOPIC'))
                                                ->where('subtopic_n_id', $subTopic->id_subtopic)
                                                ->where('student_id', \Auth::id())
                                                ->whereColumn('grade', '>=', 'min_grade')
                                                ->where('is_deleted', false)
                                                ->where('is_evaluation', false)
                                                ->where('assignment_id', $oCourse->id_assignment)
                                                ->orderBy('grade', 'DESC')
                                                ->get();

                    if (count($controls) > 0) {
                        $subTopic->ended = $controls[0];
                    }
                    else {
                        $subTopic->ended = null;
                    }
                }
            }

            $oContent = \DB::table('uni_contents_vs_elements AS ce')
                            ->join('uni_edu_contents AS c', 'ce.content_id', '=', 'c.id_content')
                            ->where('element_type_id', config('csys.elem_type.COURSE'))
                            ->where('course_n_id', $oCourse->id_course)
                            ->orderBy('order', 'ASC')
                            ->first();

            if ($oContent != null) {
                $url = asset($oContent->file_path);
                $path = str_replace("public", "", $url);
                $path = str_replace("storage", "storage/app", $path);
                
                $oContent->view_path = $path;
                $oCourse->cover = $oContent;
            }

        }
        else {
            return;
        }

        $enableReview = false;
        $lReviews = [];
        $aGrade = TakeUtils::isCourseApproved($oCourse->id_course, \Auth::id(), $assignment, true);

        if (! $aGrade[0]) {
            $module = Module::find($oCourse->module_id);
            $valid = TakeUtils::validatePrerequisites(config('csys.elem_type.AREA'), $module->knowledge_area_id);

            if (strlen($valid) > 0) {
                return redirect()->back()->withError($valid);
            }

            $valid = TakeUtils::validatePrerequisites(config('csys.elem_type.MODULE'), $oCourse->module_id);

            if (strlen($valid) > 0) {
                return redirect()->back()->withError($valid);
            }

            $valid = TakeUtils::validatePrerequisites(config('csys.elem_type.COURSE'), $oCourse->id_course);

            if (strlen($valid) > 0) {
                return redirect()->back()->withError($valid);
            }
        }
        else {
            $lReviews = Review::where('review_type_id', 2)
                            ->where('reference_id', $oCourse->id_course)
                            ->where('is_deleted', false)
                            ->where('student_by_id', \Auth::id())
                            ->get();
            
            $enableReview = count($lReviews) == 0;

            if ($enableReview) {
                $lReviews = ReviewCfg::where('showed_type_id', 2)
                                    ->where('showed_reference_id', $oCourse->id_course)
                                    ->where('is_deleted', false)
                                    ->get();
            }
        }
        
        //Inserción o actualización de la tabla toma de curso
        $controller = new TakesController();
        $takeGrouper = $controller->takeCourse($oCourse->id_course, $oCourse->university_points, $oCourse->id_assignment, $aGrade[0]);

        return view('uni.courses.course')->with('oCourse', $oCourse)
                                        ->with('idAssignment', $oCourse->id_assignment)
                                        ->with('aGrade', $aGrade)
                                        ->with('enableReview', $enableReview)
                                        ->with('lReviews', $lReviews)
                                        ->with('takeGrouper', $takeGrouper)
                                        ->with('Module', $oCourse->module_id)
                                        ->with('Assignment', $assignment);
    }

    public function playSubtopic($subtopic = 0, $takeGrouper = 0, $idAssignment = 0)
    {
        if (! TakeUtils::validateSubtopicTake($subtopic, $idAssignment)) {
            return redirect()->back()->withError('No puedes iniciar este subtema sin antes terminar el anterior.');
        }

        $lContents = \DB::table('uni_contents_vs_elements AS ce')
                            ->join('uni_edu_contents AS c', 'ce.content_id', '=', 'c.id_content')
                            ->where('element_type_id', 5)
                            ->where('subtopic_n_id', $subtopic)
                            ->orderBy('order', 'ASC')
                            ->get();

        if(is_null($lContents) || count($lContents) < 1){
            return redirect()->back()->withError('No se ha asignado contenido al subtema.');
        }
        
        $oSubtopic = SubTopic::find($subtopic);

        foreach ($lContents as $oContent) {
            $url = asset($oContent->file_path);
            $path = str_replace("public", "", $url);
            $path = str_replace("storage", "storage/app", $path);

            if ($oContent->file_extension == 'txt') {
                $path = file_get_contents($path);
            }
            
            $oContent->view_path = json_encode($path);
        }

        $aGrade = [false, null];
        if (count($lContents) > 0) {
            //Inserción o actualización de la tabla de toma de contenido
            $controller = new TakesController();
            $aGrade = TakeUtils::isSubtopicApproved($oSubtopic->id_subtopic, \Auth::id(), $idAssignment, true);

            $idSubtopicTaken = $controller->takeSubtopic($takeGrouper, $oSubtopic, $idAssignment, $aGrade[0]);
            $iContent = $controller->takeContent($idSubtopicTaken, false);
        }

        return view('uni.courses.play.view')->with('lContents', $lContents)
                                            ->with('iContent', $iContent)
                                            ->with('oSubtopic', $oSubtopic)
                                            ->with('aGrade', $aGrade)
                                            ->with('takeGrouper', $takeGrouper)
                                            ->with('idSubtopicTaken', $idSubtopicTaken)
                                            ->with('registryContentRoute', 'take.content');
    }
}
