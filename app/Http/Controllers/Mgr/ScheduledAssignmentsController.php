<?php

namespace App\Http\Controllers\Mgr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use App\User;
use App\Adm\Organization;
use App\Adm\Company;
use App\Adm\Branch;
use App\Adm\Department;
use App\Adm\Job;

use App\Uni\AssignmentControl;
use App\Uni\Assignment;
use App\Uni\ScheduledAssignment;
use App\Uni\KnowledgeArea;

class ScheduledAssignmentsController extends Controller
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
        $this->newRoute = "assignments.scheduled.create";
        $this->storeRoute = "assignments.scheduled.store";
    }

    public function index()
    {
        $lScheduled = \DB::table('uni_scheduled_assignments AS sa')
                            ->join('uni_knowledge_areas AS ka', 'sa.knowledge_area_id', '=', 'ka.id_knowledge_area')
                            ->leftJoin('adm_organizations AS org', 'sa.organization_n_id', '=', 'org.id_organization')
                            ->leftJoin('adm_companies AS com', 'sa.company_n_id', '=', 'com.id_company')
                            ->leftJoin('adm_branches AS bra', 'sa.branch_n_id', '=', 'bra.id_branch')
                            ->leftJoin('adm_departments AS dept', 'sa.department_n_id', '=', 'dept.id_department')
                            ->leftJoin('adm_jobs AS jo', 'sa.job_n_id', '=', 'jo.id_job')
                            ->where('sa.is_deleted', false)
                            ->get();

        $title = 'Asignaciones de competencias programadas';

        return view('mgr.assignments.scheduled.index')->with('title', $title)
                                                        ->with('lScheduled', $lScheduled)
                                                        ->with('newRoute', $this->newRoute);
    }

    public function create()
    {
        $lKAreas = KnowledgeArea::where('is_deleted', false)
                                // ->where('elem_status_id', 2)
                                ->select('knowledge_area', 'id_knowledge_area')
                                ->get();

        $lAssignBy = [
                        // (object) [ 'id' => 6, 'text' => 'Estudiante'],
                        (object) [ 'id' => 5, 'text' => 'Puesto'],
                        (object) [ 'id' => 4, 'text' => 'Departamento'],
                        (object) [ 'id' => 3, 'text' => 'Sucursal'],
                        (object) [ 'id' => 2, 'text' => 'Empresa'],
                        (object) [ 'id' => 1, 'text' => 'Organización'],
                    ];

        $lOrganizations = Organization::where('is_deleted', false)
                                        ->select('id_organization', 'organization', 'acronym')
                                        ->get();

        $lCompanies = Company::where('is_deleted', false)
                                        ->select('id_company', 'company', 'acronym')
                                        ->get();

        $lBranches = Branch::where('is_deleted', false)
                                        ->select('id_branch', 'branch', 'acronym')
                                        ->get();

        $lDepartments = Department::where('is_deleted', false)
                                        ->select('id_department', 'department', 'acronym')
                                        ->get();

        $lJobs = Job::where('is_deleted', false)
                                        ->select('id_job', 'job', 'acronym')
                                        ->get();
                                        
        // $lStudents = User::where('is_deleted', false)
        //                                 ->select('id', 'full_name', 'num_employee')
        //                                 ->get();

        return view("mgr.assignments.scheduled.create")->with('title', "Programar asignación")
                                                    ->with('storeRoute', $this->storeRoute)
                                                    ->with('lKAreas', $lKAreas)
                                                    ->with('lAssignBy', $lAssignBy)
                                                    ->with('lOrganizations', $lOrganizations)
                                                    ->with('lCompanies', $lCompanies)
                                                    ->with('lBranches', $lBranches)
                                                    ->with('lDepartments', $lDepartments)
                                                    ->with('lJobs', $lJobs);
    }

    public function store(Request $request)
    {
        $oSchAssignment = new ScheduledAssignment();

        $oSchAssignment->is_deleted = false;
        $oSchAssignment->dt_start = $request->dt_start;
        $oSchAssignment->dt_end = $request->dt_end;
        $oSchAssignment->num_days = $request->num_days;
        $oSchAssignment->knowledge_area_id = $request->ka_id;
        $oSchAssignment->is_always = false;
        $oSchAssignment->is_deleted = false;

        switch ($request->assignment_by) {
            case 6:
                $oSchAssignment->student_n_id = $request->student;
                break;
            case 5:
                $oSchAssignment->job_n_id = $request->job;
                break;
            case 4:
                $oSchAssignment->department_n_id = $request->department;
                break;
            case 3:
                $oSchAssignment->branch_n_id = $request->branch;
                break;
            case 2:
                $oSchAssignment->company_n_id = $request->company;
                break;
            case 1:
                $oSchAssignment->organization_n_id = $request->organization;
                break;
            
            default:
                # code...
                break;
        }

        $oSchAssignment->created_by_id = \Auth::id();
        $oSchAssignment->updated_by_id = \Auth::id();

        $oSchAssignment->save();

        $this->processAssignmentSchedule(false);

        return redirect()->route('assignments.scheduled.index')->with("success","¡Se programó con éxito!");
    }

    public function processAssignmentSchedule($bRedirect = true)
    {
        $lScheduled = \DB::table('uni_scheduled_assignments AS sa')
                            ->where('is_deleted', false)
                            ->where('dt_start', '<=', Carbon::now()->toDateString())
                            ->where('dt_end', '>=', Carbon::now()->toDateString())
                            ->get();

        foreach ($lScheduled as $oSch) {
            //Obtener estudiantes según la programación
            if ($oSch->organization_n_id > 0) {
                $lStudents = \DB::table('users AS u')
                                    ->join('adm_branches AS b', 'u.branch_id', '=', 'b.id_branch')
                                    ->join('adm_companies AS c', 'b.company_id', '=', 'c.id_company')
                                    ->where('c.organization_id', $oSch->organization_n_id)
                                    ->select('u.id', 'u.num_employee', 'u.full_name')
                                    ->orderBy('full_name', 'ASC')
                                    ->get();
            }
            else if ($oSch->company_n_id > 0) {
                $lStudents = \DB::table('users AS u')
                                    ->join('adm_branches AS b', 'u.branch_id', '=', 'b.id_branch')
                                    ->where('b.company_id', $oSch->company_n_id)
                                    ->select('u.id', 'u.num_employee', 'u.full_name')
                                    ->orderBy('full_name', 'ASC')
                                    ->get();
            }
            else if ($oSch->branch_n_id > 0) {
                $lStudents = User::where('is_deleted', false)
                                        ->where('branch_id', $oSch->branch_n_id)
                                        ->select('id', 'num_employee', 'full_name')
                                        ->orderBy('full_name', 'ASC')
                                        ->get();
            }
            else if ($oSch->department_n_id > 0) {
                $lStudents = \DB::table('users AS u')
                                    ->join('adm_jobs AS j', 'u.job_id', '=', 'j.id_job')
                                    ->where('j.department_id', $oSch->department_n_id)
                                    ->select('u.id', 'u.num_employee', 'u.full_name')
                                    ->orderBy('full_name', 'ASC')
                                    ->get();
            }
            else if ($oSch->job_n_id > 0) {
                $lStudents = User::where('is_deleted', false)
                                    ->where('job_id', $oSch->job_n_id)
                                    ->select('id', 'num_employee', 'full_name')
                                    ->orderBy('full_name', 'ASC')
                                    ->get();
            }
            else if ($oSch->student_n_id > 0) {
                $lStudents = User::where('is_deleted', false)
                                    ->where('id', $oSch->student_n_id)
                                    ->select('id', 'num_employee', 'full_name')
                                    ->orderBy('full_name', 'ASC')
                                    ->get();
            }

            // consultar cuáles empleados son candidatos a la asignación
            $lToAssign = [];
            foreach ($lStudents as $student) {
                $scheduledAssignments = \DB::table('uni_assignments AS a')
                                    ->join('uni_assignments_control AS ac', 'a.control_id', '=', 'ac.id_control')
                                    ->where('ac.scheduled_n_id', $oSch->id_scheduled)
                                    ->where('a.is_deleted', false)
                                    ->where('ac.is_deleted', false)
                                    ->where('a.student_id', $student->id);

                $currentAssigments = \DB::table('uni_assignments AS a')
                                    ->join('uni_assignments_control AS ac', 'a.control_id', '=', 'ac.id_control')
                                    ->where('a.knowledge_area_id', $oSch->knowledge_area_id)
                                    ->where('a.dt_assignment', '<=', Carbon::now()->toDateString())
                                    ->where('a.dt_end', '>=', Carbon::now()->toDateString())
                                    ->where('a.is_deleted', false)
                                    ->where('ac.is_deleted', false)
                                    ->where('a.student_id', $student->id);

                $assignments = $scheduledAssignments->union($currentAssigments)->get();

                if (count($assignments) == 0) {
                    $lToAssign[] = $student;
                }
            }

            if (count($lToAssign) == 0) {
                continue;
            }

            //asignar área de competencia
            $oAControl = new AssignmentControl();

            $oAControl->is_deleted = false;
            $oAControl->dt_assignment = Carbon::now()->toDateString();
            $oAControl->dt_end = Carbon::now()->addDays($oSch->num_days)->toDateString();
            $oAControl->knowledge_area_id = $oSch->knowledge_area_id;
            $oAControl->organization_n_id = $oSch->organization_n_id;
            $oAControl->company_n_id = $oSch->company_n_id;
            $oAControl->branch_n_id = $oSch->branch_n_id;
            $oAControl->department_n_id = $oSch->department_n_id;
            $oAControl->job_n_id = $oSch->job_n_id;
            $oAControl->student_n_id = $oSch->student_n_id;
            $oAControl->scheduled_n_id = $oSch->id_scheduled;
            $oAControl->created_by_id = \Auth::id();
            $oAControl->updated_by_id = \Auth::id();

            try {
                \DB::beginTransaction();
        
                $oAControl->save();

                foreach ($lToAssign as $student) {
                    $oAssignment = new Assignment();

                    $oAssignment->is_deleted = false;
                    $oAssignment->dt_assignment = $oAControl->dt_assignment;
                    $oAssignment->dt_end = $oAControl->dt_end;
                    $oAssignment->is_over = false;
                    $oAssignment->knowledge_area_id = $oSch->knowledge_area_id;
                    $oAssignment->student_id = $student->id;
                    $oAssignment->control_id = $oAControl->id_control;
                    $oAssignment->created_by_id = \Auth::id();
                    $oAssignment->updated_by_id = \Auth::id();

                    $oAssignment->save();
                }
    
                \DB::commit();
            }
            catch (\Throwable $th) {
                \DB::rollBack();
            }
        }

        if ($bRedirect) {
            return redirect()->back()->with("success","¡Se programó con éxito!");
        }
    }
}
