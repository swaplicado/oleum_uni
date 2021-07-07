<?php

namespace App\Http\Controllers\Mgr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Uni\AssignmentControl;
use App\Uni\Assignment;
use App\Uni\KnowledgeArea;
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
    public function index()
    {
        $lAssignments = \DB::table('uni_assignments AS a')
                        ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                        ->join('users AS u', 'a.student_id', '=', 'u.id')
                        ->select('a.*', 'ka.knowledge_area AS ka', 'u.full_name AS student')
                        ->whereRaw('NOW() between a.dt_assignment AND a.dt_end')
                        ->get();

        return view("mgr.assignments.index")->with('title', "Todas las asignaciones")
                                            ->with('newRoute', $this->newRoute)
                                            ->with('updateRoute', 'assignments.updateassignment')
                                            ->with('lAssignments', $lAssignments);
    }

    public function create(Request $request)
    {
        $lKAreas = KnowledgeArea::where('is_deleted', false)
                                // ->where('elem_status_id', 2)
                                ->select('knowledge_area', 'id_knowledge_area')
                                ->get();
        $lAssignBy = [
                        (object) [ 'id' => 6, 'text' => 'Estudiante'],
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
                                        
        $lStudents = User::where('is_deleted', false)
                                        ->select('id', 'full_name', 'num_employee')
                                        ->get();

        return view("mgr.assignments.create")->with('title', "Asignación de área")
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
                                            ->with('storeRoute', $this->storeRoute);
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
                                ->select('u.id', 'u.num_employee', 'u.full_name')
                                ->orderBy('full_name', 'ASC')
                                ->get();
                break;
            case 1:
                $lStudents = \DB::table('users AS u')
                                ->join('adm_branches AS b', 'u.branch_id', '=', 'b.id_branch')
                                ->join('adm_companies AS c', 'b.company_id', '=', 'c.id_company')
                                ->where('c.organization_id', $request->organization)
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

            foreach ($assignments as $assignment) {
                $oAssignment = new Assignment(((array) $assignment));
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

    public function updateAssignment(Request $request)
    {
        $oAssignment = Assignment::find($request->id_assignment);

        $oAssignment->dt_assignment = $request->dt_assignment;
        $oAssignment->dt_end = $request->dt_end;
        $oAssignment->updated_by_id = \Auth::id();

        $oAssignment->save();

        return;
    }

}
