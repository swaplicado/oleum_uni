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
                            ]);

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

        return redirect()->route('modules.index', $oModule->knowledge_area_id);
    }
}