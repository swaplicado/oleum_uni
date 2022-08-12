<?php

namespace App\Http\Controllers\Mgr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

use App\Mail\KnowledgeAreaAssignment;
use App\Utils\TakeUtils;
use App\Uni\AssignmentControl;
use App\Uni\Assignment;
use App\Uni\KnowledgeArea;
use App\Uni\ModuleControl;
use App\Uni\CourseControl;
use App\Adm\Organization;
use App\Adm\Company;
use App\Adm\Branch;
use App\Adm\Department;
use App\Adm\Job;
use App\User;

class AssignmentsController extends Controller
{
    protected $newRoute;
    protected $storeRoute;

    /**
     * Create a new controller instance.
     *     
     * @return void
     */
    public function __construct()
    {
        $this->newRoute = "assignments.create";
        $this->storeRoute = "assignments.store";
        $this->deleteRoute = "assignments.delete";
    }

     /**
     * Show the application index.
     *   id_assignment
     *   is_deleted
     *   dt_assignment
     *   dt_end
     *   is_over
     *   knowledge_area_id
     *   student_id
     *   control_id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $aDates = $request->daterange == null ? 
                        \App\Utils\DateUtils::getCurrentMonth(Carbon::now()) : 
                        \App\Utils\DateUtils::getDates($request->daterange);

        $lAssignments = \DB::table('uni_assignments AS a')
                        ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                        ->join('users AS u', 'a.student_id', '=', 'u.id')
                        ->join('adm_jobs AS j', 'u.job_id', '=', 'j.id_job')
                        ->join('adm_departments AS d', 'j.department_id', '=', 'd.id_department')
                        ->select('a.*', 'ka.knowledge_area AS ka', 'u.full_name AS student', 'd.department')
                        ->where(function ($q) use ($aDates) {
                            $q->whereBetween('a.dt_assignment', [$aDates[0]->format('Y-m-d'), $aDates[1]->format('Y-m-d')])
                                ->orWhereBetween('a.dt_end', [$aDates[0]->format('Y-m-d'), $aDates[1]->format('Y-m-d')]);
                        })
                        ->where('a.is_deleted', false)
                        ->where('ka.is_deleted', false)
                        ->where('u.is_deleted', false)
                        ->get();

        foreach ($lAssignments as $element) {
            if ($element->is_over) {
                $aGrade = TakeUtils::isAreaApproved($element->knowledge_area_id, $element->student_id, $element->id_assignment, true);
                $element->grade = $aGrade[1];
            }
            else {
                $element->grade = 0;
            }
        }

        $sFilterDate = $aDates[0]->format('d-m-Y').' - '.$aDates[1]->format('d-m-Y');
            
        return view("mgr.assignments.index")->with('title', "Todas las asignaciones")
                                            ->with('daterange', $sFilterDate)
                                            ->with('newRoute', $this->newRoute)
                                            ->with('updateRoute', 'assignments.updateassignment')
                                            ->with('deleteRoute', 'assignments.delete')
                                            ->with('lAssignments', $lAssignments);
    }

    public function create(Request $request)
    {
        $lKAreas = KnowledgeArea::where('is_deleted', false)
                                // ->where('elem_status_id', 2)
                                ->select('knowledge_area as text', 'id_knowledge_area as id')
                                ->get();
        $lKAreas->prepend([ 'id' => '', 'text' => '']);

        $lAssignBy = [
                        (object) [ 'id' => 6, 'text' => 'Estudiante'],
                        (object) [ 'id' => 5, 'text' => 'Puesto'],
                        (object) [ 'id' => 4, 'text' => 'Departamento'],
                        (object) [ 'id' => 3, 'text' => 'Sucursal'],
                        (object) [ 'id' => 2, 'text' => 'Empresa'],
                        (object) [ 'id' => 1, 'text' => 'Organización'],
                    ];

        $lOrganizations = Organization::where('is_deleted', false)
                                        ->select('id_organization as id', \DB::raw("CONCAT(organization,' - ',acronym) AS text"))
                                        ->orderBy('organization', 'ASC')
                                        ->get();
        $lOrganizations->prepend([ 'id' => '', 'text' => '']);

        $lCompanies = Company::where('is_deleted', false)
                                        ->select('id_company as id', \DB::raw("CONCAT(company,' - ',acronym) AS text"))
                                        ->orderBy('company', 'ASC')
                                        ->get();
        $lCompanies->prepend([ 'id' => '', 'text' => '']);

        $lBranches = Branch::where('is_deleted', false)
                                        ->select('id_branch as id', \DB::raw("CONCAT(branch,' - ',acronym) AS text"))
                                        ->orderBy('branch', 'ASC')
                                        ->get();
        $lBranches->prepend([ 'id' => '', 'text' => '']);

        $lDepartments = Department::where('is_deleted', false)
                                        ->select('id_department as id', \DB::raw("CONCAT(department,' - ',acronym) AS text"))
                                        ->orderBy('department', 'ASC')
                                        ->get();
        $lDepartments->prepend([ 'id' => '', 'text' => '']);

        $lJobs = Job::where('is_deleted', false)
                                        ->select('id_job as id', \DB::raw("CONCAT(job,' - ',acronym) AS text"))
                                        ->orderBy('job', 'ASC')
                                        ->get();
        $lJobs->prepend([ 'id' => '', 'text' => '']);
                                        
        $lStudents = User::where('is_deleted', false)
                                        ->select('id', \DB::raw("CONCAT(full_name,' - ',num_employee) AS text"))
                                        ->where('id','!=',1)
                                        ->orderBy('full_name', 'ASC')
                                        ->get();
        $lStudents->prepend([ 'id' => '', 'text' => '']);

        $routeDurationDays = route('assignments.getDurationDays');

        return view("mgr.assignments.create")->with('title', "Asignación de cuadrante")
                                            ->with('lKAreas', $lKAreas)
                                            ->with('lAssignBy', $lAssignBy)
                                            ->with('lOrganizations', $lOrganizations)
                                            ->with('lCompanies', $lCompanies)
                                            ->with('lBranches', $lBranches)
                                            ->with('lDepartments', $lDepartments)
                                            ->with('lJobs', $lJobs)
                                            ->with('lStudents', $lStudents)
                                            ->with('studentsRoute', 'assignments.getstudents')
                                            ->with('indexRoute', 'assignments.index')
                                            ->with('storeRoute', $this->storeRoute)
                                            ->with('durationRoute', $routeDurationDays);
    }

    public function getStudents(Request $request)
    {
        switch ($request->assignment_by) {
            case 6:
                $lStudents = User::where('is_deleted', false)
                                    ->where('id', $request->student)
                                    ->select('id', 'num_employee', 'full_name')
                                    ->orderBy('full_name', 'ASC')
                                    ->get();
                break;
            case 5:
                $lStudents = User::where('is_deleted', false)
                                    ->where('job_id', $request->job)
                                    ->select('id', 'num_employee', 'full_name')
                                    ->orderBy('full_name', 'ASC')
                                    ->get();
                break;
            case 4:
                $lStudents = \DB::table('users AS u')
                                ->join('adm_jobs AS j', 'u.job_id', '=', 'j.id_job')
                                ->where('j.department_id', $request->department)
                                ->where('u.is_deleted', false)
                                ->select('u.id', 'u.num_employee', 'u.full_name')
                                ->orderBy('full_name', 'ASC')
                                ->get();
                break;
            case 3:
                $lStudents = User::where('is_deleted', false)
                                    ->where('branch_id', $request->branch)
                                    ->select('id', 'num_employee', 'full_name')
                                    ->orderBy('full_name', 'ASC')
                                    ->get();
                break;
            case 2:
                $lStudents = \DB::table('users AS u')
                                ->join('adm_branches AS b', 'u.branch_id', '=', 'b.id_branch')
                                ->where('b.company_id', $request->company)
                                ->where('u.is_deleted', false)
                                ->select('u.id', 'u.num_employee', 'u.full_name')
                                ->orderBy('full_name', 'ASC')
                                ->get();
                break;
            case 1:
                $lStudents = \DB::table('users AS u')
                                ->join('adm_branches AS b', 'u.branch_id', '=', 'b.id_branch')
                                ->join('adm_companies AS c', 'b.company_id', '=', 'c.id_company')
                                ->where('c.organization_id', $request->organization)
                                ->where('u.is_deleted', false)
                                ->select('u.id', 'u.num_employee', 'u.full_name')
                                ->orderBy('full_name', 'ASC')
                                ->get();
                break;
            
            default:
                $lStudents = [];
                break;
        }

        return json_encode($lStudents);
    }

    public function store(Request $request)
    {
        $oAControl = new AssignmentControl();

        $oAControl->is_deleted = false;
        $oAControl->dt_assignment = $request->dt_start;
        $oAControl->dt_end = $request->dt_end;
        $oAControl->knowledge_area_id = $request->ka_id;

        $lStudents = [];
        switch ($request->assignment_by) {
            case 6:
                $oAControl->student_n_id = $request->student;
                break;
            case 5:
                $oAControl->job_n_id = $request->job;
                break;
            case 4:
                $oAControl->department_n_id = $request->department;
                break;
            case 3:
                $oAControl->branch_n_id = $request->branch;
                break;
            case 2:
                $oAControl->company_n_id = $request->company;
                break;
            case 1:
                $oAControl->organization_n_id = $request->organization;
                break;
            
            default:
                # code...
                break;
        }

        $oAControl->created_by_id = \Auth::id();
        $oAControl->updated_by_id = \Auth::id();

        try {
            \DB::beginTransaction();
    
            $oAControl->save();
            
            $assignments = json_decode($request->assignments);

            $lAssigns = [];
            foreach ($assignments as $assignment) {
                $oAssignment = new Assignment(((array) $assignment));
                $oAssignment->control_id = $oAControl->id_control;
                $oAssignment->created_by_id = \Auth::id();
                $oAssignment->updated_by_id = \Auth::id();

                $oAssignment->save();

                $lModules = \DB::table('uni_modules')
                                ->where([['is_deleted', 0],['knowledge_area_id', $oAssignment->knowledge_area_id]])
                                ->orderBy('id_module')
                                ->get();
                    
                foreach($lModules as $module){
                    //crea modulo control
                    $dates = $this->getDatesModule($lModules, $module, $oAssignment->dt_assignment);
                    $closeDate = Carbon::parse($dates[0]);
                    $openDate = Carbon::parse($dates[1]);
                    $assignDate = Carbon::parse($oAssignment->dt_assignment);
                    $assignDateEnd = Carbon::parse($oAssignment->dt_end);

                    if($closeDate->gt($assignDateEnd)){
                        \DB::rollBack();
                        
                        return json_encode([
                            'success' => false,
                            'message' => 'El rango de fechas de los módulos es superior al rango de fecha del cuadrante',
                            'icon' => 'error']);
                    }

                    $oModuleControl = new ModuleControl();
                    $oModuleControl->assignment_id = $oAssignment->id_assignment;
                    $oModuleControl->dt_close = $closeDate->format('Y-m-d');
                    $oModuleControl->dt_open = $openDate->format('Y-m-d');
                    $oModuleControl->module_n_id = $module->id_module;
                    $oModuleControl->student_id = $oAssignment->student_id;
                    $oModuleControl->is_deleted = false;
                    $oModuleControl->created_by = \Auth::id();
                    $oModuleControl->updated_by = \Auth::id();

                    $oModuleControl->save();

                    $lCourses = \DB::table('uni_courses')
                                    ->where([['is_deleted', 0], ['module_id', $module->id_module]])
                                    ->get();

                    foreach($lCourses as $course){
                        $courseDates = $this->getDatesCourse($lCourses, $course, $oModuleControl->dt_open);
                        $courseCloseDate = Carbon::parse($courseDates[0]);
                        $courseOpenDate = Carbon::parse($courseDates[1]);

                        if($courseCloseDate->gt($closeDate)){
                            \DB::rollBack();
                            
                            return json_encode([
                                'success' => false,
                                'message' => 'El rango de fechas de los cursos es superior al rango de fecha del módulo '.$module->module,
                                'icon' => 'error']);
                        }

                        $oCourseControl = new CourseControl();
                        $oCourseControl->assignment_id = $oAssignment->id_assignment;
                        $oCourseControl->dt_close = $courseCloseDate->format('Y-m-d');
                        $oCourseControl->dt_open = $courseOpenDate->format('Y-m-d');
                        $oCourseControl->course_n_id = $course->id_course;
                        $oCourseControl->module_n_id = $course->module_id;
                        $oCourseControl->student_id = $oAssignment->student_id;
                        $oCourseControl->is_deleted = false;
                        $oCourseControl->created_by = \Auth::id();
                        $oCourseControl->updated_by = \Auth::id();

                        $oCourseControl->save();
                    }
                }


                $lAssigns[] = $oAssignment;
            }

            \DB::commit();

            foreach ($lAssigns as $assignment) {
                $student = User::find($assignment->student_id);
                if (strlen($student->email) == 0) {
                    continue;
                }

                $rec = [];
                $ua = [];
                $ua['email'] = $student->email;
                $ua['name'] = $student->full_name;

                $rec[] = (object) $ua;

                Mail::to($rec)->send(new KnowledgeAreaAssignment($assignment->id_assignment));
            }
        }
        catch (\Throwable $th) {
            \DB::rollBack();
        }

    }

    public function updateAssignment(Request $request)
    {
        $oAssignment = Assignment::find($request->id_assignment);

        $oAssignment->dt_assignment = $request->dt_assignment;
        $oAssignment->dt_end = $request->dt_end;
        $oAssignment->updated_by_id = \Auth::id();

        $oAssignment->save();

        return;
    }

    public function delete($id)
    {
        try {
            \DB::beginTransaction();

            Assignment::where('id_assignment', $id)
                    ->update(['is_deleted' => true, 'updated_by_id' => \Auth::id()]);

            ModuleControl::where('assignment_id', $id)
                        ->update(['is_deleted' => true, 'updated_by' => \Auth::id()]);

            CourseControl::where('assignment_id', $id)
                        ->update(['is_deleted' => true, 'updated_by' => \Auth::id()]);

            \DB::commit();
        }
        catch (\Throwable $th) {
            \DB::rollBack();
        }

        return json_encode("OK");
    }

    public function getDatesModule($lModules, $module, $assign_date){
        $days = $module->completion_days;
        $oModule = $module;
        if (!is_null($oModule->pre_module_id)) {
            foreach($lModules as $m){
                $oModule = $lModules->where('id_module', $oModule->pre_module_id)->first();
                $days = $days + $oModule->completion_days;
                if(is_null($oModule->pre_module_id)){
                    break;
                }
            }
        }

        $closeDate = Carbon::parse($assign_date);
        $closeDate->addDays($days);
        $openDate = clone $closeDate;
        $closeDate->subDay();
        $openDate->subDays($module->completion_days);

        return [$closeDate->format('Y-m-d'), $openDate->format('Y-m-d')];
    }

    public function getDatesCourse($lCourses, $course, $oModuleControl_dt_open){
        $days = $course->completion_days;
        $oCourse = $course;
        if (!is_null($oCourse->pre_course_id)) {
            foreach($lCourses as $c){
                $oCourse = $lCourses->where('id_course', $oCourse->pre_course_id)->first();
                $days = $days + $oCourse->completion_days;
                if(is_null($oCourse->pre_course_id)){
                    break;
                }
            }
        }

        $closeDate = Carbon::parse($oModuleControl_dt_open);
        $closeDate->addDays($days);
        $openDate = clone $closeDate;
        $closeDate->subDay();
        $openDate->subDays($course->completion_days);

        return [$closeDate->format('Y-m-d'), $openDate->format('Y-m-d')];
    }

    public function indexAssignmentModules($id){
        $lModules = \DB::table('uni_assignments_module_control as amc')
                        ->leftJoin('uni_modules as m', 'm.id_module', '=', 'amc.module_n_id')
                        ->leftJoin('users as u', 'u.id', '=', 'amc.student_id')
                        ->select('amc.*', 'm.module', 'u.full_name as student')
                        ->where([['amc.assignment_id', $id], ['amc.is_deleted', 0]])
                        ->get();

        return view('mgr.assignments.modulesAssignments')->with('lModules', $lModules)
                                                        ->with('updateModule', route('assignments.modules.update', ['id' => $id]));
    }

    public function indexAssignmentCourses($id, $idModule){
        $lCourses = \DB::table('uni_assignments_courses_control as acc')
                        ->leftJoin('uni_courses as c', 'c.id_course', '=', 'acc.course_n_id')
                        ->leftJoin('users as u', 'u.id', '=', 'acc.student_id')
                        ->select('acc.*', 'c.course', 'u.full_name as student')
                        ->where([['acc.assignment_id', $id], ['acc.is_deleted', 0]])
                        ->where('acc.module_n_id', $idModule)
                        ->get();

        return view('mgr.assignments.coursesAssignments')->with('lCourses', $lCourses)
                                                        ->with('updateCourse', route('assignments.courses.update', ['id' => $id]));
    }

    public function updateAssignmentModule(Request $request)
    {
        $moduleControl = ModuleControl::find($request->id_assignment);

        $oAssigment = Assignment::find($moduleControl->assignment_id);

        $dtOpenModule = Carbon::parse($request->dt_assignment);
        $dtOpenAssigment = Carbon::parse($oAssigment->dt_assigment);
        $dtEndModule = Carbon::parse($request->dt_end);
        $dtEndAssigment = Carbon::parse($oAssigment->dt_end);

        if($dtEndModule->gt($dtEndAssigment) || $dtOpenModule->lt($dtOpenAssigment) || $dtOpenModule->gt($dtEndModule)){
            return json_encode([
                'success' => false,
                'message' => 'El rango de fecha seleccionado no se encuentra entre el rango de fecha del cuadrante',
                'icon' => 'error']);
        }


        $moduleControl->dt_open =  $request->dt_assignment;
        $moduleControl->dt_close =  $request->dt_end;

        $moduleControl->update();

        return;
    }

    public function updateAssignmentCourse(Request $request)
    {
        $courseControl = CourseControl::find($request->id_assignment);

        $oAssigment = ModuleControl::where([['assignment_id', $courseControl->assignment_id], ['module_n_id', $courseControl->module_n_id], ['is_deleted', 0]])->first();

        $dtOpenCourse = Carbon::parse($request->dt_assignment);
        $dtOpenAssigment = Carbon::parse($oAssigment->dt_open);
        $dtEndCourse = Carbon::parse($request->dt_end);
        $dtEndAssigment = Carbon::parse($oAssigment->dt_close);

        if($dtEndCourse->gt($dtEndAssigment) || $dtOpenCourse->lt($dtOpenAssigment) || $dtOpenCourse->gt($dtEndCourse)){
            return json_encode([
                'success' => false,
                'message' => 'El rango de fecha seleccionado no se encuentra entre el rango de fecha del módulo',
                'icon' => 'error']);
        }


        $courseControl->dt_open =  $request->dt_assignment;
        $courseControl->dt_close =  $request->dt_end;

        $courseControl->update();

        return;
    }

    public function getDurationDays(Request $request){
        $lModules = \DB::table('uni_modules AS mo')
                    ->join('uni_knowledge_areas AS ka', 'mo.knowledge_area_id', '=', 'ka.id_knowledge_area')
                    ->join('sys_element_status AS es', 'mo.elem_status_id', '=', 'es.id_element_status')
                    ->join('sys_sequences AS seq', 'mo.sequence_id', '=', 'seq.id_sequence')
                    ->select([
                            'mo.id_module as id',
                            'mo.module as text',
                            'mo.completion_days'
                            ])
                    ->where('mo.is_deleted', 0)
                    ->where('ka.is_deleted', 0)
                    ->where('knowledge_area_id', $request->ka)
                    ->get();

        $days = 0;
        foreach($lModules as $module){
            $days = $days + $module->completion_days;
        }

        return json_encode($days);
    }
}
