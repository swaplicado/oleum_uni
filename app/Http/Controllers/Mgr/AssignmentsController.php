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
                                        ->orderBy('organization', 'ASC')
                                        ->get();

        $lCompanies = Company::where('is_deleted', false)
                                        ->select('id_company', 'company', 'acronym')
                                        ->orderBy('company', 'ASC')
                                        ->get();

        $lBranches = Branch::where('is_deleted', false)
                                        ->select('id_branch', 'branch', 'acronym')
                                        ->orderBy('branch', 'ASC')
                                        ->get();

        $lDepartments = Department::where('is_deleted', false)
                                        ->select('id_department', 'department', 'acronym')
                                        ->orderBy('department', 'ASC')
                                        ->get();

        $lJobs = Job::where('is_deleted', false)
                                        ->select('id_job', 'job', 'acronym')
                                        ->orderBy('job', 'ASC')
                                        ->get();
                                        
        $lStudents = User::where('is_deleted', false)
                                        ->select('id', 'full_name', 'num_employee')
                                        ->orderBy('full_name', 'ASC')
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

            $lAssigns = [];
            foreach ($assignments as $assignment) {
                $oAssignment = new Assignment(((array) $assignment));
                $oAssignment->control_id = $oAControl->id_control;
                $oAssignment->created_by_id = \Auth::id();
                $oAssignment->updated_by_id = \Auth::id();

                $oAssignment->save();

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
        Assignment::where('id_assignment', $id)
            ->update(['is_deleted' => true,
            'updated_by_id' => \Auth::id()]);

        return json_encode("OK");
    }

}
