<?php

namespace App\Http\Controllers\Mgr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Uni\Prerequisite;
use App\Uni\PrerequisiteRow;
use App\Uni\KnowledgeArea;
use App\Uni\Module;
use App\Uni\Course;

class PrerequisitesController extends Controller
{
    public function getPreData(Request $request)
    {
        $lPres = Prerequisite::where('element_type_id', $request->id_type)
                                ->where('is_deleted', false);

        switch ($request->id_type) {
            case config('csys.elem_type.AREA'):
                $lPres = $lPres->where('knowledge_area_n_id', $request->id_reference);
                break;
            case config('csys.elem_type.MODULE'):
                $lPres = $lPres->where('module_n_id', $request->id_reference);
                break;
            case config('csys.elem_type.COURSE'):
                $lPres = $lPres->where('course_n_id', $request->id_reference);
                break;
            
            default:
                # code...
                break;
        }

        $oPre = $lPres->first();

        $lAreas = KnowledgeArea::select('id_knowledge_area', 'knowledge_area')->where('is_deleted', false)->get();
        $lModules = Module::select('id_module', 'module')->where('is_deleted', false)->get();
        $lCourses = Course::select('id_course', 'course', 'course_key')->where('is_deleted', false)->get();

        if ($oPre == null) {
            $response = (object) [
                'oPre' => null,
                'lPres' => null,
                'lAreas' => $lAreas,
                'lModules' => $lModules,
                'lCourses' => $lCourses,
            ];
    
            return json_encode($response);
        }

        $lPres = \DB::table('uni_prerequisites_rows AS pr')
                            ->join('sys_element_types AS et', 'pr.element_type_id', '=', 'et.id_element_type')
                            ->leftJoin('uni_knowledge_areas AS a', 'pr.knowledge_area_n_id', '=', 'a.id_knowledge_area')
                            ->leftJoin('uni_modules AS m', 'pr.module_n_id', '=', 'm.id_module')
                            ->leftJoin('uni_courses AS c', 'pr.course_n_id', '=', 'c.id_course')
                            ->where('pr.is_deleted', false)
                            ->where('pr.prerequisite_id', $oPre->id_prerequisite)
                            ->get();

        $response = (object) [
            'oPre' => $oPre,
            'lPres' => $lPres,
            'lAreas' => $lAreas,
            'lModules' => $lModules,
            'lCourses' => $lCourses,
        ];

        return json_encode($response);
    }

    public function storePreRequisite(Request $request)
    {
        $jRow =  (array) (json_decode($request->row));
        $row = new PrerequisiteRow($jRow);
        
        $oPrerequisite = null;
        if (! $request->id_prerequisite > 0) {
            $oPrerequisite = new Prerequisite();
            
            $oPrerequisite->is_deleted = false;
            $oPrerequisite->element_type_id = $request->elem_type_id;

            $oPrerequisite->knowledge_area_n_id = null;
            $oPrerequisite->module_n_id = null;
            $oPrerequisite->course_n_id = null;
            $oPrerequisite->topic_n_id = null;
            $oPrerequisite->subtopic_n_id = null;

            switch ($request->elem_type_id) {
                case config('csys.elem_type.AREA'):
                    $oPrerequisite->knowledge_area_n_id = $request->elem_reference_id;
                    break;
                case config('csys.elem_type.MODULE'):
                    $oPrerequisite->module_n_id = $request->elem_reference_id;
                    break;
                case config('csys.elem_type.COURSE'):
                    $oPrerequisite->course_n_id = $request->elem_reference_id;
                    break;
                
                default:
                    # code...
                    break;
            }

            $oPrerequisite->created_by_id = \Auth::id();
            $oPrerequisite->updated_by_id = \Auth::id();

            $oPrerequisite->save();
        }

        if ($oPrerequisite != null) {
            $row->prerequisite_id = $oPrerequisite->id_prerequisite;
        }
        
        $row->save();

        $lPres = \DB::table('uni_prerequisites_rows AS pr')
                            ->join('sys_element_types AS et', 'pr.element_type_id', '=', 'et.id_element_type')
                            ->leftJoin('uni_knowledge_areas AS a', 'pr.knowledge_area_n_id', '=', 'a.id_knowledge_area')
                            ->leftJoin('uni_modules AS m', 'pr.module_n_id', '=', 'm.id_module')
                            ->leftJoin('uni_courses AS c', 'pr.course_n_id', '=', 'c.id_course')
                            ->where('pr.is_deleted', false)
                            ->where('pr.prerequisite_id', $row->prerequisite_id)
                            ->get();

        return json_encode($lPres);
    }

    public function deleteRow(Request $request)
    {
        PrerequisiteRow::where('id', $request->id_prerequisite_row)->update(['is_deleted' => true]);

        $lPres = \DB::table('uni_prerequisites_rows AS pr')
                            ->join('sys_element_types AS et', 'pr.element_type_id', '=', 'et.id_element_type')
                            ->leftJoin('uni_knowledge_areas AS a', 'pr.knowledge_area_n_id', '=', 'a.id_knowledge_area')
                            ->leftJoin('uni_modules AS m', 'pr.module_n_id', '=', 'm.id_module')
                            ->leftJoin('uni_courses AS c', 'pr.course_n_id', '=', 'c.id_course')
                            ->where('pr.is_deleted', false)
                            ->where('pr.prerequisite_id', $request->prerequisite_id)
                            ->get();

        return json_encode($lPres);
    }
}
