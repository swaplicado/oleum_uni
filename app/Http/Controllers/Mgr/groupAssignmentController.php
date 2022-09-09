<?php

namespace App\Http\Controllers\Mgr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Utils\assignmentsUtils;
use App\Uni\Module;
use App\Uni\AssignmentControl;
use App\Uni\Assignment;
use App\Uni\ModuleControl;
use App\Uni\CourseControl;

class groupAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $aDates = $request->daterange == null ? 
                        \App\Utils\DateUtils::getCurrentMonth(Carbon::now()) : 
                        \App\Utils\DateUtils::getDates($request->daterange);

        $lAssignments = \DB::table('uni_assignments_control as ac')
                            ->leftJoin('adm_organizations as o', 'o.id_organization', '=', 'ac.organization_n_id')
                            ->leftJoin('adm_companies as c', 'c.id_company', '=', 'ac.company_n_id')
                            ->leftJoin('adm_branches as b', 'b.id_branch', '=', 'ac.branch_n_id')
                            ->leftJoin('adm_departments as d', 'd.id_department', '=', 'ac.department_n_id')
                            ->leftJoin('adm_areas as a', 'a.id_area', '=', 'ac.area_n_id')
                            ->leftJoin('adm_jobs as j', 'j.id_job', '=', 'ac.job_n_id')
                            ->leftJoin('uni_knowledge_areas as ka', 'ka.id_knowledge_area', '=', 'ac.knowledge_area_id')
                            ->select(
                                'ac.*',
                                'o.organization',
                                'c.company',
                                'b.branch',
                                'd.department',
                                'a.area as admArea',
                                'j.job',
                                'ka.knowledge_area as karea',
                                )
                            ->where('ac.is_deleted', 0)
                            ->where('ac.student_n_id', null)
                            ->whereBetween('ac.dt_assignment', [$aDates[0]->format('Y-m-d'), $aDates[1]->format('Y-m-d')])
                            ->get();

        foreach($lAssignments as $assign){
            if(!is_null($assign->organization_n_id)){
                $assign->type = 'Organización';
                $assign->name = $assign->organization;

            }else if(!is_null($assign->company_n_id)){
                $assign->type = 'Empresa';
                $assign->name = $assign->company;

            }else if(!is_null($assign->branch_n_id)){
                $assign->type = 'Sucursal';
                $assign->name = $assign->branch;

            }else if(!is_null($assign->department_n_id)){
                $assign->type = 'Departamento';
                $assign->name = $assign->department;

            }else if(!is_null($assign->area_n_id)){
                $assign->type = 'Área';
                $assign->name = $assign->admArea;

            }else if(!is_null($assign->job_n_id)){
                $assign->type = 'Puesto';
                $assign->name = $assign->job;
            }
        }

        $sFilterDate = $aDates[0]->format('d-m-Y').' - '.$aDates[1]->format('d-m-Y');

        return view('mgr.assignments.groupAssignments')->with('lAssignments', $lAssignments)
                                                        ->with('daterange', $sFilterDate);
    }

    public function getModules(Request $request){
        $lModules = Module::where('knowledge_area_id', $request->kaId)
                            ->where('is_deleted', 0)
                            ->get();

        foreach($lModules as $module){
            $dates = assignmentsUtils::getDatesModule($lModules, $module, $request->dateIni);
            $module->dt_ini = $dates[1];
            $module->dt_end = $dates[0];
            $module->courses = $module->courses()->get();
            $module->havePreCourses = false;

            foreach($module->courses as $course){
                $courseDates = assignmentsUtils::getDatesCourse($module->courses, $course, $module->dt_ini);
                $course->dt_ini = $courseDates[1];
                $course->dt_end = $courseDates[0];

                !is_null($course->pre_course_id) ? $module->havePreCourses = true : '';
            }
        }

        return json_encode($lModules);
    }

    public function updateAssign(Request $request){
        if(Carbon::parse($request->dateIni)->gte(Carbon::parse($request->dateEnd))){
            return json_encode(['success' => false,  'message' => 'La fecha de inicio de la asignación debe ser menor a la fecha de cierre del mismo',
                                    'icon' => 'error'
                                    ]);
        }

        foreach($request->lModules as $module){
            $module = (object)$module;
            foreach($module->courses as $course){
                $course = (object)$course;
                if(Carbon::parse($course->dt_end)->gt(Carbon::parse($module->dt_end))){
                    return json_encode(['success' => false,  'message' => 'La fecha de cierre del curso "'.
                                        $course->course.
                                        '" es mayor a la fecha de cierre del módulo "'.
                                        $module->module.'"',
                                        'icon' => 'error'
                                        ]);
                }
                if(Carbon::parse($course->dt_ini)->lt(Carbon::parse($module->dt_ini))){
                    return json_encode(['success' => false,  'message' => 'La fecha de inicio del curso "'.
                                        $course->course.
                                        '" es menor a la fecha de inicio del módulo "'.
                                        $module->module.'"',
                                        'icon' => 'error'
                                        ]);
                }
                if(Carbon::parse($course->dt_ini)->gte(Carbon::parse($course->dt_end))){
                    return json_encode(['success' => false,  'message' => 'La fecha de inicio del curso "'.
                                        $course->course.
                                        '" debe ser menor a la fecha de cierre del mismo',
                                        'icon' => 'error'
                                        ]);
                }
            }
            if(Carbon::parse($module->dt_end)->gt(Carbon::parse($request->dateEnd))){
                return json_encode(['success' => false,  'message' => 'La fecha de cierre del módulo "'.
                                    $module->module.
                                    '" es mayor a la fecha de cierre de la asignación',
                                    'icon' => 'error'
                                    ]);
            }
            if(Carbon::parse($module->dt_ini)->lt(Carbon::parse($request->dateIni))){
                return json_encode(['success' => false,  'message' => 'La fecha de inicio del módulo "'.
                                    $module->module.
                                    '" es menor a la fecha de inicio de la asignación',
                                    'icon' => 'error'
                                    ]);
            }
            if(Carbon::parse($module->dt_ini)->gte(Carbon::parse($module->dt_end))){
                return json_encode(['success' => false,  'message' => 'La fecha de inicio del módulo "'.
                                    $module->module.
                                    '" debe ser menor a la fecha de cierre del mismo',
                                    'icon' => 'error'
                                    ]);
            }
        }

        try {
            \DB::beginTransaction();
            $assignControl = AssignmentControl::findOrFail($request->assign_id);
            $assignControl->dt_assignment = $request->dateIni;
            $assignControl->dt_end = $request->dateEnd;
            $assignControl->updated_by_id = \Auth::id();
            $assignControl->update();

            $lAssignments = Assignment::where('control_id', $assignControl->id_control)
                                        ->where('is_deleted', 0)
                                        ->get();

            foreach($lAssignments as $assign){
                $assign->dt_assignment = $assignControl->dt_assignment;
                $assign->dt_end = $assignControl->dt_end;
                $assign->is_closed = 0;
                $assign->updated_by_id = \Auth::id();
                $assign->update();
                
                foreach($request->lModules as $module){
                    $module = (object)$module;
                    $oModuleControl = ModuleControl::where('assignment_id', $assign->id_assignment)
                                                    ->where('module_n_id', $module->id_module)
                                                    ->where('is_deleted', 0)
                                                    ->first();

                    if(!is_null($oModuleControl)){
                        $oModuleControl->dt_open = $module->dt_ini;
                        $oModuleControl->dt_close = $module->dt_end;
                        $oModuleControl->is_closed = 0;
                        $oModuleControl->updated_by = \Auth::id();
                        $oModuleControl->update();
    
                        foreach($module->courses as $course){
                            $course = (object)$course;
                            
                            $oCourseControl = CourseControl::where('assignment_id', $assign->id_assignment)
                                                            ->where('assignment_module_id', $oModuleControl->id_module_control)
                                                            ->where('course_n_id', $course->id_course)
                                                            ->where('is_deleted', 0)
                                                            ->update([
                                                                'is_closed' => 0,
                                                                'dt_open' => $course->dt_ini,
                                                                'dt_close' => $course->dt_end,
                                                                'updated_by' => \Auth::id(),
                                                            ]);
                        }
                    }
                }
            }
            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            return json_encode(['success' => false, 'message' => 'Error al actualizar el registro', 'icon' => 'error']);
        }

        return json_encode(['success' => true]);
    }

    public function deleteAssign(Request $request){
        try {
            \DB::beginTransaction();
            $assignControl = AssignmentControl::findOrFail($request->assign_id);
            $assignControl->is_deleted = 1;
            $assignControl->updated_by_id = \Auth::id();
            $assignControl->update();

            $lAssignments = Assignment::where('control_id', $assignControl->id_control)
                                        ->where('is_deleted', 0)
                                        ->get();

            foreach($lAssignments as $assign){
                $assign->is_deleted = 1;
                $assign->updated_by_id = \Auth::id();
                $assign->update();
                
                $lModulesControl = ModuleControl::where('assignment_id', $assign->id_assignment)
                                                ->where('is_deleted', 0)
                                                ->get();

                foreach($lModulesControl as $module){
                    $module->is_deleted = 1;
                    $module->updated_by = \Auth::id();
                    $module->update();

                    $lCoursesControl = CourseControl::where('assignment_id', $assign->id_assignment)
                                                    ->where('assignment_module_id', $module->id_module_control)
                                                    ->where('is_deleted', 0)
                                                    ->get();

                    foreach($lCoursesControl as $course){
                        $course->is_deleted = 1;
                        $course->updated_by = \Auth::id();
                        $course->update();
                    }
                }
            }
            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            return json_encode(['success' => false, 'message' => 'Error al eliminar el registro', 'icon' => 'error']);
        }

        return json_encode(['success' => true]);
    }
}
