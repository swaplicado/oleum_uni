<?php

namespace App\Http\Controllers\Mgr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Sys\Sequence;
use App\Uni\KnowledgeArea;
use App\Uni\Module;

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
                            'ka.knowledge_area'
                            ])
                    ->where('mo.is_deleted', 0)
                    ->where('ka.is_deleted', 0);

        if (isset($request->ka) && $request->ka > 0) {
            $lModules = $lModules->where('knowledge_area_id', $request->ka);
        }

        $lModules = $lModules->get();

        return view('mgr.modules.index')->with('title', $title)
                                        ->with('newRoute', $this->newRoute)
                                        ->with('kArea', $request->ka)
                                        ->with('lModules', $lModules);
    }

    public function create(Request $request, $knowledgeAreaId)
    {
        $oKa = KnowledgeArea::find($knowledgeAreaId);
        $title = "Crear módulo para ".$oKa->knowledge_area;

        $seq = Sequence::selectRaw('CONCAT(code, " - ", sequence) AS seq, id_sequence')
                        ->get();

        return view('mgr.modules.create')->with('title', $title)
                                        ->with('storeRoute', $this->storeRoute)
                                        ->with('kArea', $knowledgeAreaId)
                                        ->with('sequences', $seq);
    }

    public function store(Request $request)
    {
        try {
            $oModule = new Module();

            $oModule->module = $request->module;
            $oModule->hash_id = hash('ripemd160', $oModule->module);
            $oModule->description = $request->description;
            $oModule->objectives = $request->objectives;
            $oModule->has_document = isset($request->has_document);
            $oModule->is_deleted = 0;
            $oModule->knowledge_area_id = $request->ka_id;;
            $oModule->elem_status_id = config('csys.elem_status.NEW');
            $oModule->sequence_id = $request->sequence;
            $oModule->created_by_id = \Auth::id();
            $oModule->updated_by_id = \Auth::id();

            $oModule->save();
        }
        catch (\Throwable $th) {
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

        return view('mgr.modules.edit')->with('title', $title)
                                        ->with('updateRoute', $this->updateRoute)
                                        ->with('sequences', $seq)
                                        ->with('oModule', $oModule);
    }

    public function update(Request $request, $id)
    {
        try {
            $oModule = Module::find($id);

            $oModule->module = $request->module;
            $oModule->description = $request->description;
            $oModule->objectives = $request->objectives;
            $oModule->sequence_id = $request->sequence;
            $oModule->has_document = isset($request->has_document);
            $oModule->updated_by_id = \Auth::id();

            $oModule->save();
        }
        catch (\Throwable $th) {
            return back()->withError($th->getMessage())->withInput();
        }

        return redirect()->route('modules.index', $oModule->knowledge_area_id)->with('success', 'Módulo actualizado correctamente.');
    }

    public function updateStatus(Request $request)
    {
        try {
            $oModule = Module::find($request->row_id);
            $oMo = \DB::table('uni_modules as mo')
                        ->join('uni_courses as co', function ($join) {
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
}
