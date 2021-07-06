<?php

namespace App\Http\Controllers\Uni;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Uni\KnowledgeArea;
use App\Uni\Module;
use App\Uni\Assignment;
use App\Uni\Topic;
use App\Uni\SubTopic;

class UniversityController extends Controller
{
    public function indexAreas()
    {
        $lAssignments = \DB::table('uni_assignments AS a')
                            ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                            ->where('a.student_id', \Auth::id())
                            ->where('a.is_deleted', false)
                            ->where('a.is_over', false)
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
                        ->select('c.*')
                        ->where('a.is_deleted', false)
                        ->where('a.is_over', false)
                        ->where('a.student_id', \Auth::id())
                        ->where('m.is_deleted', false)
                        ->where('c.module_id', $module)
                        ->get();

        $oModule = Module::find($module);

        return view('uni.courses.index')->with('lCourses', $lCourses)
                                        ->with('module', $oModule->module);
    }

    public function viewCourse($course = 0)
    {
        $courses = \DB::table('uni_assignments AS a')
                        ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                        ->join('uni_modules AS m', 'ka.id_knowledge_area', '=', 'm.knowledge_area_id')
                        ->join('uni_courses AS c', 'm.id_module', '=', 'c.module_id')
                        ->select('c.*')
                        ->where('a.is_deleted', false)
                        ->where('a.is_over', false)
                        ->where('a.student_id', \Auth::id())
                        ->where('m.is_deleted', false)
                        ->where('c.id_course', $course)
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
            }
        }
        else {
            return;
        }

        return view('uni.courses.course')->with('oCourse', $oCourse);
    }

    public function playSubtopic($subtopic = 0)
    {
        $lContents = \DB::table('uni_contents_vs_elements AS ce')
                            ->join('uni_edu_contents AS c', 'ce.content_id', '=', 'c.id_content')
                            ->where('element_type_id', 5)
                            ->where('element_id', $subtopic)
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

        return view('uni.courses.play.view')->with('lContents', $lContents)
                                            ->with('oSubtopic', $oSubtopic);
    }
}
