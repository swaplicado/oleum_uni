<?php

namespace App\Http\Controllers\Mgr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Database\QueryException;

use App\Sys\Sequence;
use App\Uni\KnowledgeArea;
use App\Uni\Module;

use App\Utils\assignmentsUtils;
class ModulesController extends Controller
{
    protected $newRoute;
    protected $storeRoute;
    protected $updateRoute;

    /**
     * Create a new controller instance.
     *
     * @param  UserRepository  $users
     * @return void
     */
    public function __construct()
    {
        $this->newRoute = "modules.create";
        $this->storeRoute = "modules.store";
        $this->updateRoute = "modules.update";
    }

    /**
     * Show the application index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $title = 'Módulos';

        $lModules = \DB::table('uni_modules AS mo')
                    ->join('uni_knowledge_areas AS ka', 'mo.knowledge_area_id', '=', 'ka.id_knowledge_area')
                    ->join('sys_element_status AS es', 'mo.elem_status_id', '=', 'es.id_element_status')
                    ->join('sys_sequences AS seq', 'mo.sequence_id', '=', 'seq.id_sequence')
                    ->select(['mo.id_module',
                            'mo.module',
                            'mo.description',
                            'mo.knowledge_area_id',
                            'mo.is_deleted',
                            'es.code AS status_code',
                            'seq.code AS seq_code',
                            'ka.knowledge_area',
                            'mo.completion_days'
                            ])
                    ->where('mo.is_deleted', 0)
                    ->where('ka.is_deleted', 0);

        if (isset($request->ka) && $request->ka > 0) {
            $lModules = $lModules->where('knowledge_area_id', $request->ka);
        }

        $lModules = $lModules->get();

        $lCuadrantes = \DB::table('uni_knowledge_areas AS ka')
                            ->join('sys_element_status AS es', 'ka.elem_status_id', '=', 'es.id_element_status')
                            ->join('sys_sequences AS seq', 'ka.sequence_id', '=', 'seq.id_sequence')
                            ->select(['ka.id_knowledge_area',
                                    'ka.knowledge_area_title',
                                    'ka.knowledge_area',
                                    'ka.description',
                                    'ka.is_deleted',
                                    'es.code AS status_code',
                                    'seq.code AS seq_code',
                                    ])
                            ->where('ka.is_deleted', 0)
                            ->get();

        return view('mgr.modules.index')->with('title', $title)
                                        ->with('newRoute', $this->newRoute)
                                        ->with('kArea', $request->ka)
                                        ->with('lCuadrantes', $lCuadrantes)
                                        ->with('lModules', $lModules);
    }

    public function create(Request $request, $knowledgeAreaId)
    {
        $oKa = KnowledgeArea::find($knowledgeAreaId);
        $title = "Crear módulo para ".$oKa->knowledge_area;

        $seq = Sequence::selectRaw('CONCAT(code, " - ", sequence) AS seq, id_sequence')
                        ->get();

        $lModules = \DB::table('uni_modules')
                        ->where([['is_deleted', 0],['knowledge_area_id', $oKa->id_knowledge_area]])
                        ->select('id_module', 'module')
                        ->get();

        return view('mgr.modules.create')->with('title', $title)
                                        ->with('storeRoute', $this->storeRoute)
                                        ->with('kArea', $knowledgeAreaId)
                                        ->with('sequences', $seq)
                                        ->with('lModules', $lModules);
    }

    public function store(Request $request)
    {
        try {
            \DB::beginTransaction();
            $oModule = new Module();

            $oModule->module = $request->module;
            $oModule->hash_id = hash('ripemd160', $oModule->module);
            $oModule->description = $request->description;
            $oModule->objectives = $request->objectives;
            $oModule->completion_days = $request->completion_days;
            $oModule->pre_module_id = $request->pre_module;
            $oModule->has_document = isset($request->has_document);
            $oModule->is_deleted = 0;
            $oModule->knowledge_area_id = $request->ka_id;;
            $oModule->elem_status_id = config('csys.elem_status.NEW');
            $oModule->sequence_id = $request->sequence;
            $oModule->created_by_id = \Auth::id();
            $oModule->updated_by_id = \Auth::id();

            $oModule->save();

            if(assignmentsUtils::getAssignmentsByKa($request->ka_id) > 0){
                assignmentsUtils::setModuleAssignments($request->ka_id);
            }
            \DB::commit();
        }
        catch (\Throwable $th) {
            \DB::rollBack();
            return back()->withError($th->getMessage())->withInput();
        }

        return redirect()->route('modules.index', $oModule->knowledge_area_id)->with('success', 'Módulo creado correctamente.');
    }

    public function edit(Request $request, $id)
    {
        $oModule = Module::find($id);
        $title = "Editar módulo ".$oModule->module;

        $seq = Sequence::selectRaw('CONCAT(code, " - ", sequence) AS seq, id_sequence')
                        ->get();

        $lModules = \DB::table('uni_modules')
                        ->where([['is_deleted', 0],['knowledge_area_id', $oModule->knowledge_area_id],['id_module', '!=', $id]])
                        ->select('id_module', 'module')
                        ->get();

        return view('mgr.modules.edit')->with('title', $title)
                                        ->with('updateRoute', $this->updateRoute)
                                        ->with('sequences', $seq)
                                        ->with('oModule', $oModule)
                                        ->with('lModules', $lModules);
    }

    public function update(Request $request, $id)
    {
        try {
            \DB::beginTransaction();
            $oModule = Module::find($id);

            $oModule->module = $request->module;
            $oModule->description = $request->description;
            $oModule->objectives = $request->objectives;
            $oModule->completion_days = $request->completion_days;
            $oModule->pre_module_id = $request->pre_module;
            $oModule->sequence_id = $request->sequence;
            $oModule->has_document = isset($request->has_document);
            $oModule->updated_by_id = \Auth::id();

            $oModule->save();

            if(assignmentsUtils::getAssignmentsByKa($request->ka_id) > 0){
                assignmentsUtils::setModuleAssignments($request->ka_id);
            }
            \DB::commit();
        }
        catch (\Throwable $th) {
            \DB::rollBack();
            return back()->withError($th->getMessage())->withInput();
        }

        return redirect()->route('modules.index', $oModule->knowledge_area_id)->with('success', 'Módulo actualizado correctamente.');
    }

    public function updateStatus(Request $request)
    {
        try {
            $oModule = Module::find($request->row_id);
            $oMo = \DB::table('uni_modules as mo')
                        ->leftJoin('uni_courses as co', function ($join) {
                            $join->on('co.module_id','=','mo.id_module')
                                ->where('co.is_deleted', 0)
                                ->select('co.elem_status_id');
                        })
                        ->where('mo.id_module',$request->row_id)
                        ->where('mo.is_deleted', 0)
                        ->update(['mo.elem_status_id' => (Integer)$request->estatus,
                                'co.elem_status_id' => (Integer)$request->estatus]);
        }
        catch (\Throwable $th) {
            return back()->withError($th->getMessage())->withInput();
        }

        return redirect()->route('modules.index', $oModule->knowledge_area_id)->with('success', 'Módulo actualizado correctamente.');
    }

    public function delete($id){
        $success = true;

        try {
            DB::transaction(function () use ($id) {
                $build = DB::table('uni_modules as mo')
                            ->leftJoin('uni_courses as co', 'co.module_id', '=', 'mo.id_module')
                            ->leftJoin('uni_topics as top', 'top.course_id', '=', 'co.id_course')
                            ->leftJoin('uni_subtopics as sub', 'sub.topic_id', '=', 'top.id_topic')
                            ->leftJoin('uni_questions as q', 'q.subtopic_id', '=', 'sub.id_subtopic')
                            ->where('mo.id_module',$id);


                $isIncurse = DB::table('uni_modules as mo')
                            ->leftJoin('uni_knowledge_areas as ka', 'ka.id_knowledge_area', '=', 'mo.knowledge_area_id')
                            ->leftJoin('uni_assignments as ag', 'ag.knowledge_area_id', '=', 'ka.id_knowledge_area')
                            ->select('ag.id_assignment','ag.is_over')
                            ->where('mo.id_module',$id)->where('ag.is_over',0)
                            ->get();
                
                if($isIncurse->isEmpty()){
                    $result = $build->select('co.id_course','sub.id_subtopic')->get();

                    DB::table('uni_contents_vs_elements')
                        ->where(function($query) use ($result) {
                            $query->whereIn('subtopic_n_id',$result->pluck('id_subtopic')->toArray());
                        })->orWhere(function($query) use ($result) {
                                $query->whereIn('course_n_id',$result->pluck('id_course')->toArray());
                        })
                        ->delete();
                        
                    $build->update(['mo.is_deleted' => 1, 'co.is_deleted' => 1, 'top.is_deleted' => 1, 'sub.is_deleted' => 1, 'q.is_deleted' => 1]);
                }else{
                    throw new \Exception('area en curso');
                }
            });
        } catch (QueryException $e) {
            $success = false;
            $msg = "Error al eliminar el registro";
            $icon = "error";
        } catch (\Exception $e) {
            $success = false;
            $msg = "El módulo está siendo cursado";
            $icon = "error";
        }

        if ($success) {
            $msg = "Se eliminó el registro con éxito";
            $icon = "success";
        }

        return redirect()->back()->with(['message' => $msg, 'icon' => $icon]);
    }
}
