<?php

namespace App\Http\Controllers\Mgr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use DB;
use Illuminate\Database\QueryException;

use App\Sys\Sequence;
use App\Uni\KnowledgeArea;
use App\Uni\EduContent;
use App\Uni\ElementContent;

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
        $title = 'Cuadrantes';

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
        $title = "Crear un Cuadrante";

        $seq = Sequence::selectRaw('CONCAT(code, " - ", sequence) AS seq, id_sequence')
                        ->get();
                
        $lContents = EduContent::where('is_deleted', false)
                        ->whereIn('file_type', ['image'])
                        ->get();
        
        foreach ($lContents as $content) {
            $content->f_type = $content->file_type == 'image' ? 'Imagen' : '';
        }

        return view('mgr.kareas.create')->with('title', $title)
                                        ->with('storeRoute', $this->storeRoute)
                                        ->with('sequences', $seq)
                                        ->with('lContents', $lContents);
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

            if($request->cuadrante_cover != 0){
                $elem = new ElementContent();

                $elem->order = 1;
                $elem->content_id = $request->cuadrante_cover;
                $elem->element_type_id = config('csys.elem_type.AREA');
                $elem->knowledge_area_n_id = $ka->id_knowledge_area;
                $elem->created_by_id = \Auth::id();
                $elem->updated_by_id = \Auth::id();

                $elem->save();
            }
        }
        catch (\Throwable $th) {
            return back()->withError($th->getMessage())->withInput();
        }

        return redirect()->route('kareas.index')->with('success', 'El registro se creó correctamente.');
    }

    public function edit($id)
    {
        $oKa = KnowledgeArea::find($id);

        $title = "Crear un Cuadrante";

        $seq = Sequence::selectRaw('CONCAT(code, " - ", sequence) AS seq, id_sequence')
                        ->get();

        $lContents = EduContent::where('is_deleted', false)
                        ->whereIn('file_type', ['image'])
                        ->get();
        
        foreach ($lContents as $content) {
            $content->f_type = $content->file_type == 'image' ? 'Imagen' : '';
        }

        $oCovert = ElementContent::where('knowledge_area_n_id', $oKa->id_knowledge_area)
                                    ->where('element_type_id', config('csys.elem_type.AREA'))
                                    ->orderBy('created_at', 'DESC')
                                    ->first();

        if ($oCovert != null) {
            $oCover = EduContent::find($oCovert->content_id);
        }
        else {
            $oCover = null;
        }

        return view('mgr.kareas.edit')->with('title', $title)
                                    ->with('updateRoute', $this->updateRoute)
                                    ->with('sequences', $seq)
                                    ->with('oKa', $oKa)
                                    ->with('lContents', $lContents)
                                    ->with('oCover', $oCover);
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

            ElementContent::where('element_type_id', config('csys.elem_type.AREA'))
                            ->where('knowledge_area_n_id', $oKa->id_knowledge_area)
                            ->delete();

            if($request->cuadrante_cover != 0){
                $elem = new ElementContent();
    
                $elem->order = 1;
                $elem->content_id = $request->cuadrante_cover;
                $elem->element_type_id = config('csys.elem_type.AREA');
                $elem->knowledge_area_n_id = $oKa->id_knowledge_area;
                $elem->created_by_id = \Auth::id();
                $elem->updated_by_id = \Auth::id();
    
                $elem->save();
            }
        }
        catch (\Throwable $th) {
            return back()->withError($th->getMessage())->withInput();
        }
        
        return redirect()->route('kareas.index')->with('success', 'El registro se actualizó correctamente.');
    }

    public function updateStatus(Request $request){
        try {
            $oka = \DB::table('uni_knowledge_areas as ar')
                        ->leftJoin('uni_modules as mo', function ($join) {
                            $join->on('mo.knowledge_area_id','=','ar.id_knowledge_area')
                                ->where('mo.is_deleted', 0)
                                ->select('mo.elem_status_id');
                        })
                        ->leftJoin('uni_courses as co', function ($join) {
                            $join->on('co.module_id','=','mo.id_module')
                                ->where('co.is_deleted', 0)
                                ->select('co.elem_status_id');
                        })
                        ->where('ar.id_knowledge_area',$request->row_id)
                        ->where('ar.is_deleted',0)
                        ->update(['ar.elem_status_id' => (Integer)$request->estatus,
                                'mo.elem_status_id' => (Integer)$request->estatus,
                                'co.elem_status_id' => (Integer)$request->estatus]);
        }
        catch (\Throwable $th) {
            return back()->withError($th->getMessage())->withInput();
        }
        
        return redirect()->route('kareas.index')->with('success', 'El registro se actualizó correctamente.');
    }

    public function delete($id){
        $success = true;

        try {
            DB::transaction(function () use ($id) {
                $build = DB::table('uni_knowledge_areas as ka')
                            ->leftJoin('uni_modules as mo', 'mo.knowledge_area_id', '=', 'ka.id_knowledge_area')
                            ->leftJoin('uni_courses as co', 'co.module_id', '=', 'mo.id_module')
                            ->leftJoin('uni_topics as top', 'top.course_id', '=', 'co.id_course')
                            ->leftJoin('uni_subtopics as sub', 'sub.topic_id', '=', 'top.id_topic')
                            ->leftJoin('uni_questions as q', 'q.subtopic_id', '=', 'sub.id_subtopic')
                            ->where('ka.id_knowledge_area',$id);

                $isIncurse = DB::table('uni_knowledge_areas as ka')
                            ->leftJoin('uni_assignments as ag', 'ag.knowledge_area_id', '=', 'ka.id_knowledge_area')
                            ->select('ag.id_assignment','ag.is_over')
                            ->where('ka.id_knowledge_area',$id)->where('ag.is_over',0)
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

                    $build->update(['ka.is_deleted' => 1,'mo.is_deleted' => 1, 'co.is_deleted' => 1, 'top.is_deleted' => 1, 'sub.is_deleted' => 1, 'q.is_deleted' => 1]);
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
            $msg = "El area está siendo cursado";
            $icon = "error";
        }

        if ($success) {
            $msg = "Se eliminó el registro con éxito";
            $icon = "success";
        }

        return redirect()->back()->with(['message' => $msg, 'icon' => $icon]);
    }
}
