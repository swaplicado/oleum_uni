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
use App\Uni\mQuadrant;
use App\Uni\mModule;
use App\Uni\mCourse;
use App\Uni\mTopic;
use App\Uni\mSubtopic;
use App\Uni\mTakedExams;
use App\Adm\Areas;
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

            foreach ($area->modules as $module) {
                if ($module->completed_percent == 100) {
                    $end_modules = $end_modules + 1;
                }

                $module->promedio = is_null($module->promedio) ? 0 : $module->promedio;
                $promedio = $promedio + $module->promedio;
            }

            $area->promedio = number_format($promedio / (count($area->modules) > 0 ? count($area->modules) : 1), 2);
            $area->end_modules = $end_modules;
            $dt_in = new \DateTime($area->dt_assignment);
            $dt_end = new \DateTime($area->dt_end);
            $area->duracion = ($dt_in->diff($dt_end))->format('%d días');

            if (($area->dt_assignment <= Carbon::now()->toDateString()) && ($area->dt_end >= Carbon::now()->toDateString()) && ($area->is_closed == 0)) {
                $area->is_active = true;
            }
            else {
                $area->is_active = false;
            }
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
            $areaId =  \DB::table('adm_areas_users')
                            ->where('head_user_id', \Auth::id())
                            ->value('area_id');
                            
            $group = Areas::find($areaId);

            $group->child = $group->getChildrens();

            $arrayAreas = $group->getArrayChilds();

            $lStudentsByArea = \DB::table('adm_areas as a')
                                    ->join('users as u', 'u.area_id', '=', 'a.id_area')
                                    ->select('u.id')
                                    ->whereIn('a.id_area', $arrayAreas);

            $lStudentsByDept = \DB::table('adm_departments AS d')
                                    ->join('adm_jobs AS j', 'd.id_department', '=', 'j.department_id')
                                    ->join('users AS u', 'j.id_job', '=', 'u.job_id')
                                    ->select('u.id')
                                    ->where('d.is_deleted', false)
                                    ->where('j.is_deleted', false)
                                    ->where('u.is_deleted', false)
                                    ->where('u.is_active', true)
                                    ->where('d.head_user_n_id', \Auth::id());

            $lStudentsByBranch = \DB::table('adm_branches AS b')
                                    ->join('users AS u', 'b.id_branch', '=', 'u.branch_id')
                                    ->select('u.id')
                                    ->where('b.is_deleted', false)
                                    ->where('u.is_deleted', false)
                                    ->where('u.is_active', true)
                                    ->where('b.head_user_id', \Auth::id());

            $lStudentsByCompany = \DB::table('adm_companies AS c')
                                    ->join('adm_branches AS b', 'c.id_company', '=', 'b.company_id')
                                    ->join('users AS u', 'b.id_branch', '=', 'u.branch_id')
                                    ->select('u.id')
                                    ->where('b.is_deleted', false)
                                    ->where('u.is_deleted', false)
                                    ->where('u.is_active', true)
                                    ->where('c.head_user_id', \Auth::id());

            $lStudentsByOrg = \DB::table('adm_organizations AS o')
                                    ->join('adm_companies AS c', 'o.id_organization', '=', 'c.organization_id')
                                    ->join('adm_branches AS b', 'c.id_company', '=', 'b.company_id')
                                    ->join('users AS u', 'b.id_branch', '=', 'u.branch_id')
                                    ->select('u.id')
                                    ->where('b.is_deleted', false)
                                    ->where('u.is_deleted', false)
                                    ->where('u.is_active', true)
                                    ->where('o.head_user_id', \Auth::id());

            $aStudentsAux = $lStudentsByDept->union($lStudentsByBranch)
                                        ->union($lStudentsByCompany)
                                        ->union($lStudentsByOrg)
                                        ->distinct()
                                        ->pluck('u.id');
            
            $aStudentsArea = $lStudentsByArea->union($lStudentsByBranch)
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

            $aStudentsArea = $aStudentsAux;
        }

        // $lStudents = \DB::table('adm_departments AS d')
        //                     ->join('adm_jobs AS j', 'd.id_department', '=', 'j.department_id')
        //                     ->join('users AS u', 'j.id_job', '=', 'u.job_id')
        //                     ->whereIn('u.id', $aStudentsAux)
        //                     ->get();

        $lStudents = \DB::table('users AS u')
                            ->leftJoin('adm_areas as a', 'u.area_id', '=', 'a.id_area')
                            ->whereIn('u.id', $aStudentsArea)
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
                                    ->where('a.is_closed', 0)
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
                        ->select('id_knowledge_area as id', 'knowledge_area as text', 'is_deleted')
                        ->orderBy('text')
                        ->get();

        foreach($lAreas as $area){
            if($area->is_deleted == 1){
                $area->text = $area->text.' (Eliminado)';
            }
        }

        $lAreas->prepend([ 'id' => '', 'text' => '']);

        $lModules = \DB::table('uni_modules')
                        // ->where('is_deleted', false)
                        ->select('id_module as id', 'module as text', 'is_deleted')
                        ->orderBy('text')
                        ->get();
        
        foreach($lModules as $module){
            if($module->is_deleted == 1){
                $module->text = $module->text.' (Eliminado)';
            }
        }

        $lModules->prepend([ 'id' => '', 'text' => '']);
        
        $lCourses = \DB::table('uni_courses')
                        // ->where('is_deleted', false)
                        ->select('id_course as id', 'course as text', 'is_deleted')
                        ->orderBy('text')
                        ->get();

        foreach($lCourses as $course){
            if($course->is_deleted == 1){
                $course->text = $course->text.' (Eliminado)';
            }
        }

        $lCourses->prepend([ 'id' => '', 'text' => '']);

        $lTopics = \DB::table('uni_topics')
                        // ->where('is_deleted', false)
                        ->select('id_topic as id', 'topic as text', 'is_deleted')
                        ->orderBy('text')
                        ->get();
        
        foreach($lTopics as $topic){
            if($topic->is_deleted == 1){
                $topic->text = $topic->text.' (Eliminado)';
            }
        }

        $lTopics->prepend([ 'id' => '', 'text' => '']);

        $lSubtopics = \DB::table('uni_subtopics')
                        // ->where('is_deleted', false)
                        ->select('id_subtopic as id', 'subtopic as text', 'is_deleted')
                        ->orderBy('text')
                        ->get();
        
        foreach($lSubtopics as $subtopic){
            if($subtopic->is_deleted == 1){
                $subtopic->text = $subtopic->text.' (Eliminado)';
            }
        }

        $lSubtopics->prepend([ 'id' => '', 'text' => '']);

        $lOrganizations = \DB::table('adm_organizations')
                            ->where('is_deleted', false)
                            ->select('id_organization as id', 'organization as text')
                            ->orderBy('text')
                            ->get();

        $lOrganizations->prepend([ 'id' => '', 'text' => '']);

        $lCompany = \DB::table('adm_companies')
                            ->where('is_deleted', false)
                            ->select('id_company as id', 'company as text')
                            ->orderBy('text')
                            ->get();

        $lCompany->prepend([ 'id' => '', 'text' => '']);
        
        $lBranches = \DB::table('adm_branches')
                            ->where('is_deleted', false)
                            ->select('id_branch as id', 'branch as text')
                            ->orderBy('text')
                            ->get();

        $lBranches->prepend([ 'id' => '', 'text' => '']);

        $lDepartments = \DB::table('adm_departments')
                            ->where('is_deleted', false)
                            ->select('id_department as id', 'department as text')
                            ->orderBy('text')
                            ->get();

        $lDepartments->prepend([ 'id' => '', 'text' => '']);

        $lJobs = \DB::table('adm_jobs')
                            ->where('is_deleted', false)
                            ->select('id_job as id', 'job as text')
                            ->orderBy('text')
                            ->get();

        $lJobs->prepend([ 'id' => '', 'text' => '']);

        $lStudent = \DB::table('users')
                            ->where('is_deleted', false)
                            ->where('is_active', true)
                            ->where('id','!=',1)
                            ->select('id', \DB::raw("CONCAT(full_name,' - ',num_employee) AS text"))
                            ->orderBy('text')
                            ->get();
        
        $lStudent->prepend([ 'id' => '', 'text' => '']);

        return view('uni.kardex.indexReport',  
                    ['lAreas' => $lAreas, 'lModules' => $lModules, 'lCourses' => $lCourses,
                    'lTopics' => $lTopics, 'lSubtopics' => $lSubtopics, 'lOrganizations' => $lOrganizations,
                    'lCompany' => $lCompany, 'lBranches' => $lBranches, 'lDepartments' => $lDepartments,
                    'lJobs' => $lJobs, 'lStudent' => $lStudent]);
    }

    public function generateMReporte(Request $request){
        switch ($request->type_level) {
            case 'organizacion':
                $lUsers = \DB::table('users as u')
                            ->join('adm_branches as ab', 'ab.id_branch', '=', 'u.branch_id')
                            ->join('adm_companies as ac', 'ab.company_id', '=', 'ac.id_company')
                            ->join('adm_organizations as ao', 'ac.organization_id', '=', 'ao.id_organization')
                            ->where('ao.id_organization', $request->level)
                            ->pluck('u.id');
                break;
            case 'empresa':
                $lUsers = \DB::table('users as u') //no hay forma actual de obtener usuarios por empresa, se obtienen todos los usuarios en su lugar
                            ->join('adm_branches as ab', 'ab.id_branch', '=', 'u.branch_id')
                            ->join('adm_companies as ac', 'ab.company_id', '=', 'ac.id_company')
                            ->where('ac.id_company', $request->level)
                            ->pluck('u.id');
                break;
            case 'sucursal':
                $lUsers = \DB::table('users as u')
                            ->where('u.branch_id', $request->level)
                            ->pluck('u.id');
                break;
            case 'departamento':
                $lUsers = \DB::table('users as u')
                            ->join('adm_jobs as aj', 'aj.id_job', '=', 'u.job_id')
                            ->join('adm_departments as ad', 'aj.department_id', '=', 'ad.id_department')
                            ->where('ad.id_department', $request->level)
                            ->pluck('u.id');
                break;
            case 'puesto':
                $lUsers = \DB::table('users as u')
                            ->where('u.job_id', $request->level)
                            ->pluck('u.id');
                break;
            case 'estudiante':
                $lUsers = \DB::table('users as u')
                            ->where('u.id', $request->level)
                            ->pluck('u.id')
                            ->toArray();
                break;
            default:
                break;
        }
        $max_questions = 0;
        switch ($request->tipo_elemento) {
            case 'cuadrante':
                $lAssignments = \DB::table('uni_assignments')
                                    ->where('knowledge_area_id', $request->elemento)
                                    ->whereIn('student_id', $lUsers)
                                    ->where('is_deleted', 0)
                                    ->whereBetween('dt_assignment', [$request->calendarStart, $request->calendarEnd])
                                    ->pluck('id_assignment')
                                    ->toArray();

                $mQuadrants = mQuadrant::whereIn('assignment_id', $lAssignments)->get();
                foreach($mQuadrants as $quadrantkey => $quadrant){
                    
                    $mModules = mModule::where('assignment_id', (Integer)$mQuadrants[$quadrantkey]->assignment_id)
                                        ->where('quadrant_id', (Integer)$mQuadrants[$quadrantkey]->quadrant_id)
                                        ->get();

                    foreach($mModules as $modulekey => $module){
                        $mCourses = mCourse::where('assignment_id', (Integer)$mQuadrants[$quadrantkey]->assignment_id)
                                            ->where('module_id', (Integer)$mModules[$modulekey]->module_id)
                                            ->get();

                        foreach($mCourses as $courseKey => $course){
                            $mTopics = mTopic::where('assignment_id', (Integer)$mQuadrants[$quadrantkey]->assignment_id)
                                            ->where('course_id', (Integer)$mCourses[$courseKey]->course_id)
                                            ->get();

                            foreach($mTopics as $topicKey => $topic){
                                $mSubtopic = mSubtopic::where('assignment_id', (Integer)$mQuadrants[$quadrantkey]->assignment_id)
                                                    ->where('topic_id', (Integer)$mTopics[$topicKey]->topic_id)
                                                    ->get();

                                foreach($mSubtopic as $subtopicKey => $subtopic){
                                    $mExam = mTakedExams::where('assignment_id', (Integer)$mQuadrants[$quadrantkey]->assignment_id)
                                                        ->where('subtopic_id', (Integer)$mSubtopic[$subtopicKey]->subtopic_id)
                                                        ->first();

                                    $num_questions = ((object)json_decode($mSubtopic[$subtopicKey]->element_body, JSON_UNESCAPED_UNICODE))->number_questions;
                                    $max_questions < $num_questions ? $max_questions = $num_questions : '';
                                    if(!is_null($mExam)){
                                        $oExam = (object)$mExam->getAttributes();
                                        $mSubtopic[$subtopicKey] = (object)json_decode($mSubtopic[$subtopicKey]->element_body, JSON_UNESCAPED_UNICODE);
                                        $mSubtopic[$subtopicKey]->exam = $oExam;
                                        $mSubtopic[$subtopicKey]->exam->takedQuestions = json_decode($oExam->element_body, JSON_UNESCAPED_UNICODE);
                                    } else {
                                        $mSubtopic[$subtopicKey] = (object)json_decode($mSubtopic[$subtopicKey]->element_body, JSON_UNESCAPED_UNICODE);
                                        $mSubtopic[$subtopicKey]->exam = null;
                                    }

                                }

                                $mTopics[$topicKey] = (object)json_decode($topic->element_body, JSON_UNESCAPED_UNICODE);
                                $mTopics[$topicKey]->subtopics = $mSubtopic;
                            }

                            $mCourses[$courseKey] = (object)json_decode($course->element_body, JSON_UNESCAPED_UNICODE);
                            $mCourses[$courseKey]->topics = $mTopics;
                        }

                        $mModules[$modulekey] = (object)json_decode($module->element_body, JSON_UNESCAPED_UNICODE);
                        $mModules[$modulekey]->courses = $mCourses;
                    }

                    $student = \DB::table('adm_departments as d')
                                        ->join('adm_jobs as j', 'j.department_id', '=', 'd.id_department')
                                        ->join('users as u', 'u.job_id', '=', 'j.id_job')
                                        ->where('u.id', $mQuadrants[$quadrantkey]->student_id)
                                        ->select('u.full_name', 'd.department')
                                        ->first();

                    $mQuadrants[$quadrantkey] = (object)json_decode($quadrant->element_body, JSON_UNESCAPED_UNICODE);
                    $mQuadrants[$quadrantkey]->modules = $mModules;
                    $mQuadrants[$quadrantkey]->student_name = $student->full_name;
                    $mQuadrants[$quadrantkey]->student_department = $student->department;
                }
                break;
            case 'modulo':
                $lAssignments = \DB::table('uni_assignments as a')
                                    ->join('uni_modules as m', 'm.knowledge_area_id', '=', 'a.knowledge_area_id')
                                    ->where('m.id_module', $request->elemento)
                                    ->whereIn('a.student_id', $lUsers)
                                    ->whereBetween('dt_assignment', [$request->calendarStart, $request->calendarEnd])
                                    ->pluck('id_assignment')
                                    ->toArray();

                $mQuadrants = mQuadrant::whereIn('assignment_id', $lAssignments)->get();
                foreach($mQuadrants as $quadrantkey => $quadrant){
                    $mModules = mModule::where('assignment_id', (Integer)$mQuadrants[$quadrantkey]->assignment_id)
                                        ->where('quadrant_id', (Integer)$mQuadrants[$quadrantkey]->quadrant_id)
                                        ->where('module_id', (Integer)$request->elemento)
                                        ->get();

                    foreach($mModules as $modulekey => $module){
                        $mCourses = mCourse::where('assignment_id', (Integer)$mQuadrants[$quadrantkey]->assignment_id)
                                            ->where('module_id', (Integer)$mModules[$modulekey]->module_id)
                                            ->get();

                        foreach($mCourses as $courseKey => $course){
                            $mTopics = mTopic::where('assignment_id', (Integer)$mQuadrants[$quadrantkey]->assignment_id)
                                            ->where('course_id', (Integer)$mCourses[$courseKey]->course_id)
                                            ->get();

                            foreach($mTopics as $topicKey => $topic){
                                $mSubtopic = mSubtopic::where('assignment_id', (Integer)$mQuadrants[$quadrantkey]->assignment_id)
                                                    ->where('topic_id', (Integer)$mTopics[$topicKey]->topic_id)
                                                    ->get();

                                foreach($mSubtopic as $subtopicKey => $subtopic){
                                    $mExam = mTakedExams::where('assignment_id', (Integer)$mQuadrants[$quadrantkey]->assignment_id)
                                                        ->where('subtopic_id', (Integer)$mSubtopic[$subtopicKey]->subtopic_id)
                                                        ->first();

                                    $num_questions = ((object)json_decode($mSubtopic[$subtopicKey]->element_body, JSON_UNESCAPED_UNICODE))->number_questions;
                                    $max_questions < $num_questions ? $max_questions = $num_questions : '';
                                    if(!is_null($mExam)){
                                        $oExam = (object)$mExam->getAttributes();
                                        $mSubtopic[$subtopicKey] = (object)json_decode($mSubtopic[$subtopicKey]->element_body, JSON_UNESCAPED_UNICODE);
                                        $mSubtopic[$subtopicKey]->exam = $oExam;
                                        $mSubtopic[$subtopicKey]->exam->takedQuestions = json_decode($oExam->element_body, JSON_UNESCAPED_UNICODE);
                                    } else {
                                        $mSubtopic[$subtopicKey] = (object)json_decode($mSubtopic[$subtopicKey]->element_body, JSON_UNESCAPED_UNICODE);
                                        $mSubtopic[$subtopicKey]->exam = null;
                                    }

                                }

                                $mTopics[$topicKey] = (object)json_decode($topic->element_body, JSON_UNESCAPED_UNICODE);
                                $mTopics[$topicKey]->subtopics = $mSubtopic;
                            }

                            $mCourses[$courseKey] = (object)json_decode($course->element_body, JSON_UNESCAPED_UNICODE);
                            $mCourses[$courseKey]->topics = $mTopics;
                        }

                        $mModules[$modulekey] = (object)json_decode($module->element_body, JSON_UNESCAPED_UNICODE);
                        $mModules[$modulekey]->courses = $mCourses;
                    }
                    $student = \DB::table('adm_departments as d')
                                    ->join('adm_jobs as j', 'j.department_id', '=', 'd.id_department')
                                    ->join('users as u', 'u.job_id', '=', 'j.id_job')
                                    ->where('u.id', $mQuadrants[$quadrantkey]->student_id)
                                    ->select('u.full_name', 'd.department')
                                    ->first();

                    $mQuadrants[$quadrantkey] = (object)json_decode($quadrant->element_body, JSON_UNESCAPED_UNICODE);
                    $mQuadrants[$quadrantkey]->modules = $mModules;
                    $mQuadrants[$quadrantkey]->student_name = $student->full_name;
                    $mQuadrants[$quadrantkey]->student_department = $student->department;
                }
                break;
            case 'curso':
                $lAssignments = \DB::table('uni_assignments as a')
                                    ->join('uni_modules as m', 'm.knowledge_area_id', '=', 'a.knowledge_area_id')
                                    ->join('uni_courses as c', 'c.module_id', '=', 'm.id_module')
                                    ->where('c.id_course', $request->elemento)
                                    ->whereIn('a.student_id', $lUsers)
                                    ->whereBetween('dt_assignment', [$request->calendarStart, $request->calendarEnd])
                                    ->pluck('id_assignment')
                                    ->toArray();

                $module_id = \DB::table('uni_modules as m')
                                ->join('uni_courses as c', 'c.module_id', '=', 'm.id_module')
                                ->where('c.id_course', $request->elemento)
                                ->value('id_module');

                $mQuadrants = mQuadrant::whereIn('assignment_id', $lAssignments)->get();
                foreach($mQuadrants as $quadrantkey => $quadrant){
                    $mModules = mModule::where('assignment_id', (Integer)$mQuadrants[$quadrantkey]->assignment_id)
                                        ->where('quadrant_id', (Integer)$mQuadrants[$quadrantkey]->quadrant_id)
                                        ->where('module_id', (Integer)$module_id)
                                        ->get();

                    foreach($mModules as $modulekey => $module){
                        $mCourses = mCourse::where('assignment_id', (Integer)$mQuadrants[$quadrantkey]->assignment_id)
                                            ->where('module_id', (Integer)$mModules[$modulekey]->module_id)
                                            ->where('course_id', (Integer)$request->elemento)
                                            ->get();

                        foreach($mCourses as $courseKey => $course){
                            $mTopics = mTopic::where('assignment_id', (Integer)$mQuadrants[$quadrantkey]->assignment_id)
                                            ->where('course_id', (Integer)$mCourses[$courseKey]->course_id)
                                            ->get();

                            foreach($mTopics as $topicKey => $topic){
                                $mSubtopic = mSubtopic::where('assignment_id', (Integer)$mQuadrants[$quadrantkey]->assignment_id)
                                                    ->where('topic_id', (Integer)$mTopics[$topicKey]->topic_id)
                                                    ->get();

                                foreach($mSubtopic as $subtopicKey => $subtopic){
                                    $mExam = mTakedExams::where('assignment_id', (Integer)$mQuadrants[$quadrantkey]->assignment_id)
                                                        ->where('subtopic_id', (Integer)$mSubtopic[$subtopicKey]->subtopic_id)
                                                        ->first();

                                    $num_questions = ((object)json_decode($mSubtopic[$subtopicKey]->element_body, JSON_UNESCAPED_UNICODE))->number_questions;
                                    $max_questions < $num_questions ? $max_questions = $num_questions : '';
                                    if(!is_null($mExam)){
                                        $oExam = (object)$mExam->getAttributes();
                                        $mSubtopic[$subtopicKey] = (object)json_decode($mSubtopic[$subtopicKey]->element_body, JSON_UNESCAPED_UNICODE);
                                        $mSubtopic[$subtopicKey]->exam = $oExam;
                                        $mSubtopic[$subtopicKey]->exam->takedQuestions = json_decode($oExam->element_body, JSON_UNESCAPED_UNICODE);
                                    } else {
                                        $mSubtopic[$subtopicKey] = (object)json_decode($mSubtopic[$subtopicKey]->element_body, JSON_UNESCAPED_UNICODE);
                                        $mSubtopic[$subtopicKey]->exam = null;
                                    }

                                }

                                $mTopics[$topicKey] = (object)json_decode($topic->element_body, JSON_UNESCAPED_UNICODE);
                                $mTopics[$topicKey]->subtopics = $mSubtopic;
                            }

                            $mCourses[$courseKey] = (object)json_decode($course->element_body, JSON_UNESCAPED_UNICODE);
                            $mCourses[$courseKey]->topics = $mTopics;
                        }

                        $mModules[$modulekey] = (object)json_decode($module->element_body, JSON_UNESCAPED_UNICODE);
                        $mModules[$modulekey]->courses = $mCourses;
                    }

                    $student = \DB::table('adm_departments as d')
                                        ->join('adm_jobs as j', 'j.department_id', '=', 'd.id_department')
                                        ->join('users as u', 'u.job_id', '=', 'j.id_job')
                                        ->where('u.id', $mQuadrants[$quadrantkey]->student_id)
                                        ->select('u.full_name', 'd.department')
                                        ->first();

                    $mQuadrants[$quadrantkey] = (object)json_decode($quadrant->element_body, JSON_UNESCAPED_UNICODE);
                    $mQuadrants[$quadrantkey]->modules = $mModules;
                    $mQuadrants[$quadrantkey]->student_name = $student->full_name;
                    $mQuadrants[$quadrantkey]->student_department = $student->department;
                }
                break;
            default:
                break;
        }

        return view('uni.kardex.generatedReport', ['areas' => $mQuadrants, 'max_questions' => $max_questions]);
    }

    public function generateReport(Request $request)
    {
        $lResult = \DB::table('uni_assignments AS a')
                        ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                        ->join('uni_modules AS m', 'ka.id_knowledge_area', '=', 'm.knowledge_area_id')
                        ->join('uni_courses AS c', 'm.id_module', '=', 'c.module_id')
                        ->leftJoin('users as u', 'u.id', '=', 'a.student_id')
                        ->where('c.elem_status_id', '>', config('csys.elem_status.EDIT'))
                        ->whereBetween('a.dt_assignment', [$request->calendarStart, $request->calendarEnd])
                        ->whereBetween('a.dt_end', [$request->calendarStart, $request->calendarEnd]);
                        // ->where('a.dt_assignment', '>=', $request->calendarStart)
                        // ->where('a.dt_end', '<=', $request->calendarEnd);
        
        switch ($request->tipo_elemento) {
            case 'cuadrante':
                $lResult = $lResult->select('c.*', 'a.*', 'u.full_name as student', 'ka.knowledge_area')
                        ->where('a.knowledge_area_id', $request->elemento)
                        ->where('m.is_deleted', false)
                        ->where('c.is_deleted', false);
                break;
            case 'modulo':
                $lResult = $lResult->select('c.*', 'a.*', 'u.full_name as student', 'ka.knowledge_area')
                        ->where('c.module_id', $request->elemento)
                        ->where('c.is_deleted', false);
                break;
            case 'curso':
                $lResult = $lResult->select('c.*', 'a.*', 'u.full_name as student', 'ka.knowledge_area')
                        ->where('c.id_course', $request->elemento);
                break;
            case 'tema':
                $lResult = $lResult->join('uni_topics AS t', 't.course_id', '=', 'c.id_course')
                        ->select('c.*', 'a.*', 'u.full_name as student', 'ka.knowledge_area')
                        ->where('t.id_topic', $request->elemento);
                break;
            case 'subtema':
                $lResult = $lResult->join('uni_topics AS t', 't.course_id', '=', 'c.id_course')
                        ->join('uni_subtopics AS st', 'st.topic_id', '=', 't.id_topic')
                        ->select('c.*', 'a.*', 'u.full_name as student', 'ka.knowledge_area')
                        ->where('st.id_subtopic', $request->elemento);
                break;
            case 'todo':
                $lResult = $lResult->select('c.*', 'a.*', 'u.full_name as student', 'u.job_id', 'ka.knowledge_area')
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
                            ->where('assignment_id', $course->id_assignment)
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
                                        ->orderBy('tq.id_question_taken', 'ASC')
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
