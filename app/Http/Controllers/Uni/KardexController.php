<?php

namespace App\Http\Controllers\Uni;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\Utils\TakeUtils;

use App\Uni\TakingControl;
use App\Uni\TakingContent;
use App\Uni\TakingSubTopicQuestion;
use App\Uni\Assignment;
use App\Uni\KnowledgeArea;
use App\Uni\Module;
use App\Uni\Course;
use App\Uni\Topic;
use App\Uni\SubTopic;

class KardexController extends Controller
{
    public function index(Request $request)
    {
        $areas = \DB::table('uni_assignments AS a')
                    ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                    ->where('a.student_id', \Auth::id())
                    ->where('a.is_deleted', false)
                    ->orderBy('a.dt_assignment', 'DESC')
                    ->get();

        return view('uni.kardex.index')->with('areas', $areas);
    }

    public function kardexModules($area = 0)
    {
        $lModules = \DB::table('uni_assignments AS a')
                        ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                        ->join('uni_modules AS m', 'ka.id_knowledge_area', '=', 'm.knowledge_area_id')
                        ->select('m.*', 'a.*')
                        ->where('a.is_deleted', false)
                        ->where('a.is_over', false)
                        ->where('a.student_id', \Auth::id())
                        ->where('m.is_deleted', false)
                        ->where('m.knowledge_area_id', $area)
                        ->where('a.dt_assignment', '<=', Carbon::now()->toDateString())
                        ->where('a.dt_end', '>=', Carbon::now()->toDateString())
                        ->get();

        $oKa = KnowledgeArea::find($area);

        return view('uni.kardex.modules')->with('lModules', $lModules)
                                            ->with('knowledgeArea', $oKa->knowledge_area);
    }

    public function kardexCourses($module = 0)
    {
        $lCourses = \DB::table('uni_assignments AS a')
                        ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                        ->join('uni_modules AS m', 'ka.id_knowledge_area', '=', 'm.knowledge_area_id')
                        ->join('uni_courses AS c', 'm.id_module', '=', 'c.module_id')
                        ->select('c.*', 'a.*')
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
            $course->grade = TakeUtils::isCourseApproved($course->id_course, \Auth::id(), $course->id_assignment, true);

            $course->lTopics = \DB::table('uni_topics AS t')
                                    ->where('t.is_deleted', false)
                                    ->where('t.course_id', $course->id_course)
                                    ->get();

            foreach ($course->lTopics as $topic) {
                $topic->grade = TakeUtils::isTopicApproved($topic->id_topic, \Auth::id(), $course->id_assignment, true);

                $topic->lSubTopics = \DB::table('uni_subtopics AS s')
                                        ->where('s.is_deleted', false)
                                        ->where('s.topic_id', $topic->id_topic)
                                        ->get();

                foreach ($topic->lSubTopics as $subtopic) {
                    $subtopic->grade = TakeUtils::isSubtopicApproved($subtopic->id_subtopic, \Auth::id(), $course->id_assignment, true);
                }
            }
        }

        return view('uni.kardex.courses')->with('lCourses', $lCourses)
                                            ->with('oModule', $oModule);
    }
}
