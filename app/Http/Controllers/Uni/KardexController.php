<?php

namespace App\Http\Controllers\Uni;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
            $area->promedio = number_format($promedio / (count($area->modules) > 0 ? count($area->modules) : 1), 2);
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

    public function Reports()
    {
        return view('reports');
    }

    public function indexReport()
    {
        $lAreas =  \DB::table('uni_knowledge_areas')
                        // ->where('is_deleted', false)
                        ->select('id_knowledge_area as id', 'knowledge_area as name', 'is_deleted')
                        ->orderBy('name')
                        ->get();

        foreach($lAreas as $area){
            if($area->is_deleted == 1){
                $area->name = $area->name.' (Eliminado)';
            }
        }

        $lModules = \DB::table('uni_modules')
                        // ->where('is_deleted', false)
                        ->select('id_module as id', 'module as name', 'is_deleted')
                        ->orderBy('name')
                        ->get();
        
        foreach($lModules as $module){
            if($module->is_deleted == 1){
                $module->name = $module->name.' (Eliminado)';
            }
        }
        
        $lCourses = \DB::table('uni_courses')
                        // ->where('is_deleted', false)
                        ->select('id_course as id', 'course as name', 'is_deleted')
                        ->orderBy('name')
                        ->get();

        foreach($lCourses as $course){
            if($course->is_deleted == 1){
                $course->name = $course->name.' (Eliminado)';
            }
        }

        $lTopics = \DB::table('uni_topics')
                        // ->where('is_deleted', false)
                        ->select('id_topic as id', 'topic as name', 'is_deleted')
                        ->orderBy('name')
                        ->get();
        
        foreach($lTopics as $topic){
            if($topic->is_deleted == 1){
                $topic->name = $topic->name.' (Eliminado)';
            }
        }

        $lSubtopics = \DB::table('uni_subtopics')
                        // ->where('is_deleted', false)
                        ->select('id_subtopic as id', 'subtopic as name', 'is_deleted')
                        ->orderBy('name')
                        ->get();
        
        foreach($lSubtopics as $subtopic){
            if($subtopic->is_deleted == 1){
                $subtopic->name = $subtopic->name.' (Eliminado)';
            }
        }

        $lOrganizations = \DB::table('adm_organizations')
                            ->where('is_deleted', false)
                            ->select('id_organization as id', 'organization as name')
                            ->orderBy('name')
                            ->get();

        $lCompany = \DB::table('adm_companies')
                            ->where('is_deleted', false)
                            ->select('id_company as id', 'company as name')
                            ->orderBy('name')
                            ->get();
        
        $lBranches = \DB::table('adm_branches')
                            ->where('is_deleted', false)
                            ->select('id_branch as id', 'branch as name')
                            ->orderBy('name')
                            ->get();

        $lDepartments = \DB::table('adm_departments')
                            ->where('is_deleted', false)
                            ->select('id_department as id', 'department as name')
                            ->orderBy('name')
                            ->get();

        $lJobs = \DB::table('adm_jobs')
                            ->where('is_deleted', false)
                            ->select('id_job as id', 'job as name')
                            ->orderBy('job')
                            ->get();

        $lStudent = \DB::table('users')
                            ->where('is_deleted', false)
                            ->where('is_active', true)
                            ->where('user_type_id', 1)
                            ->select('id', 'full_name as name')
                            ->orderBy('full_name')
                            ->get();
        

        return view('uni.kardex.indexReport',  
                    ['lAreas' => $lAreas, 'lModules' => $lModules, 'lCourses' => $lCourses,
                    'lTopics' => $lTopics, 'lSubtopics' => $lSubtopics, 'lOrganizations' => $lOrganizations,
                    'lCompany' => $lCompany, 'lBranches' => $lBranches, 'lDepartments' => $lDepartments,
                    'lJobs' => $lJobs, 'lStudent' => $lStudent]);
    }

    public function generateReport(Request $request)
    {
        $lResult = \DB::table('uni_assignments AS a')
                        ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                        ->join('uni_modules AS m', 'ka.id_knowledge_area', '=', 'm.knowledge_area_id')
                        ->join('uni_courses AS c', 'm.id_module', '=', 'c.module_id')
                        ->leftJoin('users as u', 'u.id', '=', 'a.student_id')
                        ->where('c.elem_status_id', '>=', config('csys.elem_status.EDIT'))
                        ->where('a.dt_assignment', '>=', $request->calendarStart)
                        ->where('a.dt_end', '<=', $request->calendarEnd);
        
        switch ($request->tipo_elemento) {
            case 'competencia':
                $lResult = $lResult->select('c.*', 'a.*', 'u.username as student', 'ka.knowledge_area')
                        ->where('a.knowledge_area_id', $request->elemento)
                        ->where('m.is_deleted', false)
                        ->where('c.is_deleted', false);
                break;
            case 'modulo':
                $lResult = $lResult->select('c.*', 'a.*', 'u.username as student', 'ka.knowledge_area')
                        ->where('c.module_id', $request->elemento)
                        ->where('c.is_deleted', false);
                break;
            case 'curso':
                $lResult = $lResult->select('c.*', 'a.*', 'u.username as student', 'ka.knowledge_area')
                        ->where('c.id_course', $request->elemento);
                break;
            case 'tema':
                $lResult = $lResult->join('uni_topics AS t', 't.course_id', '=', 'c.id_course')
                        ->select('c.*', 'a.*', 'u.username as student', 'ka.knowledge_area')
                        ->where('t.id_topic', $request->elemento);
                break;
            case 'subtema':
                $lResult = $lResult->join('uni_topics AS t', 't.course_id', '=', 'c.id_course')
                        ->join('uni_subtopics AS st', 'st.topic_id', '=', 't.id_topic')
                        ->select('c.*', 'a.*', 'u.username as student', 'ka.knowledge_area')
                        ->where('st.id_subtopic', $request->elemento);
                break;
            case 'todo':
                $lResult = $lResult->select('c.*', 'a.*', 'u.username as student', 'u.job_id', 'ka.knowledge_area')
                            ->where('a.is_deleted', false)
                            ->where('m.is_deleted', false)
                            ->where('c.is_deleted', false);
                break;
            default:
                break;
        }
        
        switch ($request->type_level) {
            case 'organizacion':
                $lResult = $lResult->join('adm_branches as ab', 'ab.id_branch', '=', 'u.branch_id');
                $lResult = $lResult->join('adm_companies as ac', 'ab.company_id', '=', 'ac.id_company');
                $lResult = $lResult->join('adm_organizations as ao', 'ac.organization_id', '=', 'ao.id_organization');
                $lResult = $lResult->where('ao.id_organization', $request->level);
                break;
            case 'empresa':
                $lResult = $lResult->join('adm_branches as ab', 'ab.id_branch', '=', 'u.branch_id');
                $lResult = $lResult->join('adm_companies as ac', 'ab.company_id', '=', 'ac.id_company');
                $lResult = $lResult->where('ac.id_company', $request->level);
                break;
            case 'sucursal':
                $lResult = $lResult->where('u.branch_id', $request->level);
                break;
            case 'departamento':
                $lResult = $lResult->join('adm_jobs as aj', 'aj.id_job', '=', 'u.job_id');
                $lResult = $lResult->join('adm_departments as ad', 'aj.department_id', '=', 'ad.id_department');
                $lResult = $lResult->where('ad.id_department', $request->level);
                break;
            case 'puesto':
                $lResult = $lResult->where('u.job_id', $request->level);
                break;
            case 'estudiante':
                $lResult = $lResult->where('a.student_id', $request->level);
                break;
            default:
                break;
        }
        $lResult = $lResult->get();
        $max_questions = 0;
        foreach ($lResult as $course) {
            $course->lTopics = \DB::table('uni_topics AS t')
                                    ->where('t.course_id', $course->id_course);
                                    
            switch ($request->tipo_elemento) {
                case 'tema':
                    $course->lTopics = $course->lTopics->get();
                    break;
                case 'subtema':
                    $course->lTopics = $course->lTopics->get();
                    break;
                default:
                    $course->lTopics = $course->lTopics->where('t.is_deleted', false)->get();
                    break;
            }

            $course->control = \DB::table('uni_taken_controls')
                            ->where('course_n_id', $course->id_course)
                            ->where('student_id', $course->student_id)
                            ->where('is_deleted', false)
                            ->latest()
                            ->first();

            $questions = 0;
            $vecesTomado = 0;
            $lQuestions = new Collection();
            foreach ($course->lTopics as $topic) {
                $topic->lSubTopics = \DB::table('uni_subtopics AS s')
                                        ->where('s.topic_id', $topic->id_topic);
                
                switch ($request->tipo_elemento) {
                    case 'subtema':
                        $topic->lSubTopics = $topic->lSubTopics->get();
                        break;
                    default:
                        $topic->lSubTopics = $topic->lSubTopics->where('s.is_deleted', false)->get();
                        break;
                }                        

                foreach($topic->lSubTopics as $subtopic){
                    $control = \DB::table('uni_taken_controls')
                                    ->where('subtopic_n_id', $subtopic->id_subtopic)
                                    ->where('student_id', $course->student_id)
                                    ->where('assignment_id', $course->id_assignment)
                                    ->where('is_evaluation', true)
                                    ->where('is_deleted', false)
                                    ->orderBy('id_taken_control')
                                    ->get();

                    if(count($control) > 0){
                        $successControl = $control->last();
                        $oQuestions = \DB::table('uni_questions AS q')
                                        ->leftJoin('uni_taken_questions as tq', 'tq.question_id', '=', 'q.id_question')
                                        ->where('tq.take_control_id', $successControl->id_taken_control)
                                        ->where('subtopic_id', $subtopic->id_subtopic)
                                        ->where('q.is_deleted', false)
                                        ->where('tq.is_deleted', false)
                                        ->select('q.*', 'tq.is_correct', 'tq.take_control_id')
                                        ->orderBy('q.id_question')
                                        ->get();
                        $lQuestions = $lQuestions->merge($oQuestions);
                        $questions = $questions + count($oQuestions);
                        $vecesTomado = $vecesTomado + (count($control) > 1 ? count($control) - 1 : count($control));
                        
                    }else{
                        $subtopic->lQuestions = null;
                    }
                }
            }
            $course->questions = $lQuestions;
            $course->n_taken = $vecesTomado;
            $max_questions < $questions ? $max_questions = $questions : '';
        }
        
        return view('uni.kardex.generatedReport', ['areas' => $lResult, 'max_questions' => $max_questions]);
    }
}
