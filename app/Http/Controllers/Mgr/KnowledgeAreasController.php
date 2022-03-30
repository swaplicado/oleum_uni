<?php

namespace App\Http\Controllers\Mgr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

use App\Sys\Sequence;
use App\Uni\KnowledgeArea;

class KnowledgeAreasController extends Controller
{
    protected $newRoute;
    protected $storeRoute;
    protected $updateRoute;

    /**
     * Create a new controller instance.
     *     
     * @return void
     */
    public function __construct()
    {
        $this->newRoute = "kareas.create";
        $this->storeRoute = "kareas.store";
        $this->updateRoute = "kareas.update";
    }

    /**
     * Show the application index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $title = 'Áreas de competencia';

        $lAreas = \DB::table('uni_knowledge_areas AS ka')
                    ->join('sys_element_status AS es', 'ka.elem_status_id', '=', 'es.id_element_status')
                    ->join('sys_sequences AS seq', 'ka.sequence_id', '=', 'seq.id_sequence')
                    ->select(['ka.id_knowledge_area',
                            'ka.knowledge_area',
                            'ka.description',
                            'ka.is_deleted',
                            'es.code AS status_code',
                            'seq.code AS seq_code',
                            ])
                    ->where('ka.is_deleted', 0)
                            ->get();

        return view('mgr.kareas.index')->with('title', $title)
                                        ->with('newRoute', $this->newRoute)
                                        ->with('lAreas', $lAreas);
    }

    public function create(Request $request)
    {
        $title = "Crear área de competencia";

        $seq = Sequence::selectRaw('CONCAT(code, " - ", sequence) AS seq, id_sequence')
                        ->get();

        return view('mgr.kareas.create')->with('title', $title)
                                        ->with('storeRoute', $this->storeRoute)
                                        ->with('sequences', $seq);
    }

    public function store(Request $request)
    {
        try {
            $ka = new KnowledgeArea();

            $ka->knowledge_area = $request->name;
            $ka->hash_id = hash('ripemd160', $ka->knowledge_area);
            $ka->description = $request->description;
            $ka->objectives = $request->objectives;
            $ka->has_document = isset($request->has_document);
            $ka->is_deleted = 0;
            $ka->elem_status_id = config('csys.elem_status.NEW');
            $ka->sequence_id = $request->sequence;
            $ka->created_by_id = \Auth::id();
            $ka->updated_by_id = \Auth::id();

            $ka->save();
        }
        catch (\Throwable $th) {
            return back()->withError($th->getMessage())->withInput();
        }

        return redirect()->route('kareas.index')->with('success', 'El registro se creó correctamente.');
    }

    public function edit($id)
    {
        $oKa = KnowledgeArea::find($id);

        $title = "Crear área de competencia";

        $seq = Sequence::selectRaw('CONCAT(code, " - ", sequence) AS seq, id_sequence')
                        ->get();

        return view('mgr.kareas.edit')->with('title', $title)
                                    ->with('updateRoute', $this->updateRoute)
                                    ->with('sequences', $seq)
                                    ->with('oKa', $oKa);
    }

    function update(Request $request, $id)
    {
        try {
            $oKa = KnowledgeArea::find($id);

            $oKa->knowledge_area = $request->name;
            $oKa->description = $request->description;
            $oKa->objectives = $request->objectives;
            $oKa->sequence_id = $request->sequence;
            $oKa->has_document = isset($request->has_document);
            $oKa->updated_by_id = \Auth::id();
    
            $oKa->save();
        }
        catch (\Throwable $th) {
            return back()->withError($th->getMessage())->withInput();
        }
        
        return redirect()->route('kareas.index')->with('success', 'El registro se actualizó correctamente.');
    }
}
