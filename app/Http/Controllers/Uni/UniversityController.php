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

class UniversityController extends Controller
{
    public function indexAreas()
    {
        $lAssignments = \DB::table('uni_assignments AS a')
                            ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                            ->where('a.student_id', \Auth::id())
                            ->where('a.is_deleted', false)
                            ->where('a.is_over', false)
                            ->where('a.dt_assignment', '<=', Carbon::now()->toDateString())
                            ->where('a.dt_end', '>=', Carbon::now()->toDateString())
                            ->get();

        return view('uni.areas.index')->with('lAssignments', $lAssignments)
                                        ->with('lContents', []);
    }

    public function indexModules($area = 0)
    {
        $lModules = \DB::table('uni_assignments AS a')
                        ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                        ->join('uni_modules AS m', 'ka.id_knowledge_area', '=', 'm.knowledge_area_id')
                        ->select('m.*')
                        ->where('a.is_deleted', false)
                        ->where('a.is_over', false)
                        ->where('a.student_id', \Auth::id())
                        ->where('m.is_deleted', false)
                        ->where('m.knowledge_area_id', $area)
                        ->where('a.dt_assignment', '<=', Carbon::now()->toDateString())
                        ->where('a.dt_end', '>=', Carbon::now()->toDateString())
                        ->get();

        $oKa = KnowledgeArea::find($area);

        return view('uni.modules.index')->with('lModules', $lModules)
                                        ->with('knowledgeArea', $oKa->knowledge_area);
    }

    public function indexCourses($module = 0)
    {
        $lCourses = \DB::table('uni_assignments AS a')
                        ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                        ->join('uni_modules AS m', 'ka.id_knowledge_area', '=', 'm.knowledge_area_id')
                        ->join('uni_courses AS c', 'm.id_module', '=', 'c.module_id')
                        ->select('c.*', 'a.id_assignment')
                        ->where('a.is_deleted', false)
                        ->where('a.is_over', false)
                        ->where('a.student_id', \Auth::id())
                        ->where('m.is_deleted', false)
                        ->where('c.module_id', $module)
                        ->where('a.dt_assignment', '<=', Carbon::now()->toDateString())
                        ->where('a.dt_end', '>=', Carbon::now()->toDateString())
                        ->get();

        $oModule = Module::find($module);

        foreach ($lCourses as $course) {
            $course->percent_completed = TakeUtils::getCoursePercentCompleted($course->id_course, \Auth::id(), $course->id_assignment);
        }

        return view('uni.courses.index')->with('lCourses', $lCourses)
                                        ->with('module', $oModule->module);
    }

    public function viewCourse($course = 0)
    {
        $courses = \DB::table('uni_assignments AS a')
                        ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                        ->join('uni_modules AS m', 'ka.id_knowledge_area', '=', 'm.knowledge_area_id')
                        ->join('uni_courses AS c', 'm.id_module', '=', 'c.module_id')
                        ->select('c.*', 'a.id_assignment')
                        ->where('a.is_deleted', false)
                        ->where('a.is_over', false)
                        ->where('a.student_id', \Auth::id())
                        ->where('m.is_deleted', false)
                        ->where('c.id_course', $course)
                        ->where('a.dt_assignment', '<=', Carbon::now()->toDateString())
                        ->where('a.dt_end', '>=', Carbon::now()->toDateString())
                        ->take(1)
                        ->get();

        if (count($courses) == 1) {
            $oCourse = $courses[0];

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
        }
        else {
            return;
        }

        //Inserción o actualización de la tabla toma de curso
        $controller = new TakesController();
        $takeGrouper = $controller->takeCourse($oCourse->id_course, $oCourse->university_points, $oCourse->id_assignment);

        return view('uni.courses.course')->with('oCourse', $oCourse)
                                        ->with('idAssignment', $oCourse->id_assignment)
                                        ->with('takeGrouper', $takeGrouper);
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

        if (count($lContents) > 0) {
            //Inserción o actualización de la tabla de toma de contenido
            $controller = new TakesController();

            $idSubtopicTaken = $controller->takeSubtopic($takeGrouper, $oSubtopic, $idAssignment);
            $iContent = $controller->takeContent($idSubtopicTaken, false);
        }

        return view('uni.courses.play.view')->with('lContents', $lContents)
                                            ->with('iContent', $iContent)
                                            ->with('oSubtopic', $oSubtopic)
                                            ->with('takeGrouper', $takeGrouper)
                                            ->with('idSubtopicTaken', $idSubtopicTaken)
                                            ->with('registryContentRoute', 'take.content');
    }
}
