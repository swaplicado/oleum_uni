<?php

namespace App\Http\Controllers\Mgr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Database\QueryException;

use App\Uni\SubTopic;

class SubTopicsController extends Controller
{
    public function store(Request $request)
    {
        $subtopic = json_decode($request->subtopic);

        $oSubTopic = new SubTopic();
        
        $oSubTopic->subtopic = $subtopic->subtopic;
        $oSubTopic->hash_id = hash('ripemd160', $oSubTopic->subtopic);
        $oSubTopic->number_questions = $subtopic->number_questions;
        $oSubTopic->is_deleted = false;
        $oSubTopic->topic_id = $subtopic->topic_id;
        $oSubTopic->created_by_id = \Auth::id();
        $oSubTopic->updated_by_id = \Auth::id();

        $oSubTopic->save();

        return json_encode($oSubTopic);
    }

    public function edit(Request $request, $id){
        $success = true;

        try {
            DB::transaction(function () use ($id, $request) {
                $oSubTopic = SubTopic::findOrFail($id);
                $oSubTopic->subtopic = $request->name;
                $oSubTopic->update();
            });
        } catch (QueryException $e) {
            $success = false;
        }

        if ($success) {
            $msg = "Se actualizó el registro con éxito";
            $icon = "success";
        } else {
            $msg = "Error al actualizar el registro";
            $icon = "error";
        }

        return redirect()->back()->with(['message' => $msg, 'icon' => $icon]);
    }

    public function delete($id){
        $success = true;

        try {
            DB::transaction(function () use ($id) {
                $build = DB::table('uni_subtopics as sub')
                            ->leftJoin('uni_questions as q', 'q.subtopic_id', '=', 'sub.id_subtopic')
                            ->where('id_subtopic',$id);

                $isIncurse = DB::table('uni_subtopics as sub')
                                ->leftJoin('uni_topics as top', 'top.id_topic', '=', 'sub.topic_id')
                                ->leftJoin('uni_courses as co', 'co.id_course', '=', 'top.course_id')
                                ->leftJoin('uni_modules as mo', 'mo.id_module', '=', 'co.module_id')
                                ->leftJoin('uni_knowledge_areas as ka', 'ka.id_knowledge_area', '=', 'mo.knowledge_area_id')
                                ->leftJoin('uni_assignments as ag', 'ag.knowledge_area_id', '=', 'ka.id_knowledge_area')
                                ->select('ag.id_assignment','ag.is_over')
                                ->where('sub.id_subtopic',$id)->where('ag.is_over',0)
                                ->get();

                if($isIncurse->isEmpty()){
                    $result = $build->select('sub.id_subtopic')->get();
    
                    DB::table('uni_contents_vs_elements')
                        ->whereIn('subtopic_n_id',$result->pluck('id_subtopic')->toArray())
                        ->delete();
    
                    $build->update(['sub.is_deleted' => 1, 'q.is_deleted' => 1]);
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
            $msg = "El subtema está siendo cursado";
            $icon = "error";
        }

        if ($success) {
            $msg = "Se eliminó el registro con exito";
            $icon = "success";
        }

        return redirect()->back()->with(['message' => $msg, 'icon' => $icon]);
    }
}
