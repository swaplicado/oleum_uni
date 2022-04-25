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
    public function index($student = 0)
    {
        $iStudent = $student == 0 ? \Auth::id() : $student;

        $areas = \DB::table('uni_assignments AS a')
                    ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                    ->where('a.student_id', $iStudent)
                    ->where('a.is_deleted', false)
                    ->orderBy('a.dt_assignment', 'DESC')
                    ->get();

        foreach ($areas as $area) {
            $area->grade = TakeUtils::isAreaApproved($area->id_knowledge_area, $iStudent, $area->id_assignment, true);
            $result = TakeUtils::getlAssignmentPercentCompleted($area->id_assignment, $area->id_knowledge_area, $iStudent);
            $area->completed_percent = number_format($result[0]);
            $area->modules = $result[1];
            $end_modules = 0;
            $promedio = 0;
            foreach($area->modules as $module){
                if($module->completed_percent == 100){
                    $end_modules = $end_modules + 1;
                }
                is_null($module->promedio) ? $module->promedio = 0 : '';
                $promedio = $promedio + $module->promedio;
            }
            $area->promedio = number_format($promedio / count($area->modules), 2);
            $area->end_modules = $end_modules;
            $dt_in = new \DateTime($area->dt_assignment);
            $dt_end = new \DateTime($area->dt_end);
            $area->duracion = ($dt_in->diff($dt_end))->format('%d días');
        }

        return view('uni.kardex.index')->with('areas', $areas)->with('student', $iStudent);
    }

    public function kardexModules($area, $asssignment, $student = 0)
    {
        $iStudent = $student == 0 ? \Auth::id() : $student;

        $lModules = \DB::table('uni_assignments AS a')
                        ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                        ->join('uni_modules AS m', 'ka.id_knowledge_area', '=', 'm.knowledge_area_id')
                        ->select('m.*', 'a.*')
                        ->where('a.is_deleted', false)
                        // ->where('a.is_over', false)
                        ->where('a.student_id', $iStudent)
                        ->where('m.is_deleted', false)
                        ->where('m.knowledge_area_id', $area)
                        ->where('a.id_assignment', $asssignment)
                        // ->where('a.dt_assignment', '<=', Carbon::now()->toDateString())
                        // ->where('a.dt_end', '>=', Carbon::now()->toDateString())
                        ->get();

        foreach ($lModules as $module) {
            $module->grade = TakeUtils::isCourseApproved($module->id_module, $iStudent, $module->id_assignment, true);
        }

        $oKa = KnowledgeArea::find($area);

        return view('uni.kardex.modules')->with('lModules', $lModules)
                                            ->with('knowledgeArea', $oKa->knowledge_area)
                                            ->with('student', $iStudent);
    }

    public function kardexCourses($module, $asssignment, $student = 0)
    {
        $iStudent = $student == 0 ? \Auth::id() : $student;

        $lCourses = \DB::table('uni_assignments AS a')
                        ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                        ->join('uni_modules AS m', 'ka.id_knowledge_area', '=', 'm.knowledge_area_id')
                        ->join('uni_courses AS c', 'm.id_module', '=', 'c.module_id')
                        ->select('c.*', 'a.*')
                        ->where('a.is_deleted', false)
                        // ->where('a.is_over', false)
                        ->where('a.student_id', $iStudent)
                        ->where('m.is_deleted', false)
                        ->where('c.module_id', $module)
                        ->where('a.id_assignment', $asssignment)
                        // ->where('a.dt_assignment', '<=', Carbon::now()->toDateString())
                        // ->where('a.dt_end', '>=', Carbon::now()->toDateString())
                        ->get();

        $oModule = Module::find($module);

        foreach ($lCourses as $course) {
            $course->percent_completed = TakeUtils::getCoursePercentCompleted($course->id_course, $iStudent, $course->id_assignment);
            $course->grade = TakeUtils::isCourseApproved($course->id_course, $iStudent, $course->id_assignment, true);

            $course->lTopics = \DB::table('uni_topics AS t')
                                    ->where('t.is_deleted', false)
                                    ->where('t.course_id', $course->id_course)
                                    ->get();

            foreach ($course->lTopics as $topic) {
                $topic->grade = TakeUtils::isTopicApproved($topic->id_topic, $iStudent, $course->id_assignment, true);

                $topic->lSubTopics = \DB::table('uni_subtopics AS s')
                                        ->where('s.is_deleted', false)
                                        ->where('s.topic_id', $topic->id_topic)
                                        ->get();

                foreach ($topic->lSubTopics as $subtopic) {
                    $subtopic->grade = TakeUtils::isSubtopicApproved($subtopic->id_subtopic, $iStudent, $course->id_assignment, true);
                }
            }
        }

        return view('uni.kardex.courses')->with('lCourses', $lCourses)
                                            ->with('oModule', $oModule)
                                            ->with('student', $iStudent);
    }

    public function indexHead()
    {
        if (\Auth::user()->user_type_id <= 2) {
            $lStudentsByDept = \DB::table('adm_departments AS d')
                                    ->join('adm_jobs AS j', 'd.id_department', '=', 'j.department_id')
                                    ->join('users AS u', 'j.id_job', '=', 'u.job_id')
                                    ->select('u.id')
                                    ->where('d.is_deleted', false)
                                    ->where('j.is_deleted', false)
                                    ->where('u.is_deleted', false)
                                    ->where('d.head_user_n_id', \Auth::id());

            $lStudentsByBranch = \DB::table('adm_branches AS b')
                                    ->join('users AS u', 'b.id_branch', '=', 'u.branch_id')
                                    ->select('u.id')
                                    ->where('b.is_deleted', false)
                                    ->where('u.is_deleted', false)
                                    ->where('b.head_user_id', \Auth::id());

            $lStudentsByCompany = \DB::table('adm_companies AS c')
                                    ->join('adm_branches AS b', 'c.id_company', '=', 'b.company_id')
                                    ->join('users AS u', 'b.id_branch', '=', 'u.branch_id')
                                    ->select('u.id')
                                    ->where('b.is_deleted', false)
                                    ->where('u.is_deleted', false)
                                    ->where('c.head_user_id', \Auth::id());

            $lStudentsByOrg = \DB::table('adm_organizations AS o')
                                    ->join('adm_companies AS c', 'o.id_organization', '=', 'c.organization_id')
                                    ->join('adm_branches AS b', 'c.id_company', '=', 'b.company_id')
                                    ->join('users AS u', 'b.id_branch', '=', 'u.branch_id')
                                    ->select('u.id')
                                    ->where('b.is_deleted', false)
                                    ->where('u.is_deleted', false)
                                    ->where('o.head_user_id', \Auth::id());


            $aStudentsAux = $lStudentsByDept->union($lStudentsByBranch)
                                        ->union($lStudentsByCompany)
                                        ->union($lStudentsByOrg)
                                        ->distinct()
                                        ->pluck('u.id');
        }
        else {
            $aStudentsAux = \DB::table('users AS u')
                                ->where('u.is_deleted', false)
                                ->where('u.is_active', true)
                                ->pluck('u.id');
        }

        $lStudents = \DB::table('adm_departments AS d')
                            ->join('adm_jobs AS j', 'd.id_department', '=', 'j.department_id')
                            ->join('users AS u', 'j.id_job', '=', 'u.job_id')
                            ->whereIn('u.id', $aStudentsAux)
                            ->get();

        foreach ($lStudents as $student) {

            /**
             * Asignaciones totales
             */

            $lAssignments = \DB::table('uni_assignments AS a')
                                ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                                ->where('a.student_id', $student->id)
                                ->where('a.is_deleted', false)
                                ->orderBy('a.dt_assignment', 'DESC')
                                ->get();

            $nApproved = 0;
            $nTakenTotal = 0;
            $nSumGrades = 0;
            foreach ($lAssignments as $element) {
                $element->isAreaApproved = TakeUtils::isAreaApproved($element->id_knowledge_area, $student->id, $element->id_assignment, true);

                if ($element->isAreaApproved[0]) {
                    $nApproved++;
                }
                if ($element->isAreaApproved[1] != null && $element->isAreaApproved[1] > 0) {
                    $nSumGrades += $element->isAreaApproved[1];
                    $nTakenTotal++;
                }
            }

            $student->generalAverage = $nTakenTotal == 0 ? 0 : $nSumGrades/$nTakenTotal;
            $student->nTotalAssignments= count($lAssignments);
            $student->nTotalApprovedAssignments = $nApproved;

            /**
             * Asignaciones actuales
             */

            $lAssignmentsCurrent = \DB::table('uni_assignments AS a')
                                    ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                                    ->where('a.student_id', $student->id)
                                    ->where('a.is_deleted', false)
                                    ->where('a.dt_assignment', '<=', Carbon::now()->toDateString())
                                    ->where('a.dt_end', '>=', Carbon::now()->toDateString())
                                    ->orderBy('a.dt_assignment', 'DESC')
                                    ->get();

            $currentSubtopics = [];
            $currentApproved = 0;
            foreach ($lAssignmentsCurrent as $curElement) {
                $lSubs = TakeUtils::getSubTopicsOfArea($curElement->id_knowledge_area);

                foreach ($lSubs as $idSub) {
                    if (TakeUtils::isSubtopicApproved($idSub, $student->id, $curElement->id_assignment)) {
                        $currentApproved++;
                    }
                }

                $currentSubtopics = array_merge($currentSubtopics, $lSubs->toArray());
            }

            $total = count($currentSubtopics);

            $student->nTotalCurrentAssignments = count($lAssignmentsCurrent);
            $student->currentAdvancePercent = $total == 0 ? 0 : ($currentApproved * 100 / $total);
        }

        return view('uni.kardex.head')->with('lStudents', $lStudents);
    }

    public function generateReport(Request $request)
    {
        return view('uni.kardex.report');
    }
}
