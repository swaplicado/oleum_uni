<?php

namespace App\Http\Controllers\Mgr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Database\QueryException;

use App\Sys\Sequence;
use App\Uni\KnowledgeArea;
use App\Uni\Module;
use App\Uni\Course;
use App\Uni\Topic;
use App\Uni\SubTopic;

use App\Utils\assignmentsUtils;

class TopicsController extends Controller
{
    protected $newRoute;
    protected $storeRoute;
    protected $storeSubRoute;

    /**
     * Create a new controller instance.
     *
     * @param  UserRepository  $users
     * @return void
     */
    public function __construct()
    {
        $this->newRoute = "topics.create";
        $this->storeRoute = "topics.store";
        $this->storeSubRoute = "subtopics.store";
    }

    /**
     * Show the application index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request, $courseId)
    {
        $title = 'Temas y Subtemas';

        $lTopics = \DB::table('uni_topics AS top')
                    ->join('uni_courses AS co', 'top.course_id', '=', 'co.id_course')
                    ->join('sys_sequences AS seq', 'co.sequence_id', '=', 'seq.id_sequence')                    
                    ->where('top.is_deleted',0)
                    ->select(['top.id_topic',
                            'top.topic',
                            'top.course_id',
                            'top.is_deleted',
                            'seq.code AS seq_code',
                            'co.course'
                            ]);

        if (isset($courseId) && $courseId > 0) {
            $lTopics = $lTopics->where('co.id_course', $courseId);
        }

        $lTopics = $lTopics->get();

        foreach ($lTopics as $topic) {
            $topic->lSubtopics = SubTopic::where([['topic_id', $topic->id_topic],['is_deleted',0]])->get();
        }

        $seq = Sequence::selectRaw('CONCAT(code, " - ", sequence) AS seq, id_sequence')
                        ->get();

        $lAreas = \DB::table('uni_knowledge_areas AS ka')
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

        return view('mgr.topics.index')->with('title', $title)
                                        ->with('storeRoute', $this->storeRoute)
                                        ->with('storeSubRoute', $this->storeSubRoute)
                                        ->with('courseId', $courseId)
                                        ->with('sequences', $seq)
                                        ->with('lTopics', $lTopics)
                                        ->with('lAreas', $lAreas);
    }

    public function store(Request $request)
    {
        $topic = json_decode($request->topic);
        try {
            \DB::beginTransaction();
            $session = \DB::connection('mongodb')->getMongoClient()->startSession();
            $session->startTransaction();
    
            $oTopic = new Topic();
            
            $oTopic->topic = $topic->topic;
            $oTopic->hash_id = hash('ripemd160', $oTopic->topic);
            $oTopic->is_deleted = false;
            $oTopic->course_id = $topic->course_id;
            $oTopic->sequence_id = $topic->secuence_id;
            $oTopic->created_by_id = \Auth::id();
            $oTopic->updated_by_id = \Auth::id();
    
            $oTopic->save();
    
            assignmentsUtils::createTopicsMongo($oTopic);

            \DB::commit();
            $session->commitTransaction();
        } catch (\Throwable $th) {
            \DB::rollBack();
            $session->abortTransaction();

            $oTopic = null;
        }

        return json_encode($oTopic);
    }

    public function edit(Request $request, $id){
        $success = true;

        try {
            \DB::beginTransaction();
            $session = \DB::connection('mongodb')->getMongoClient()->startSession();
            $session->startTransaction();

            $oTopic = Topic::findOrFail($id);
            $oTopic->topic = $request->name;
            $oTopic->update();

            assignmentsUtils::upTopicsMongo($oTopic);

            \DB::commit();
            $session->commitTransaction();

        } catch (\Throwable $th) {
            \DB::rollBack();
            $session->abortTransaction();
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
                $build = DB::table('uni_topics as top')
                            ->leftJoin('uni_subtopics as sub', 'sub.topic_id', '=', 'top.id_topic')
                            ->leftJoin('uni_questions as q', 'q.subtopic_id', '=', 'sub.id_subtopic')
                            ->where('id_topic',$id);

                $isIncurse = DB::table('uni_topics as top')
                                ->leftJoin('uni_courses as co', 'co.id_course', '=', 'top.course_id')
                                ->leftJoin('uni_modules as mo', 'mo.id_module', '=', 'co.module_id')
                                ->leftJoin('uni_knowledge_areas as ka', 'ka.id_knowledge_area', '=', 'mo.knowledge_area_id')
                                ->leftJoin('uni_assignments as ag', 'ag.knowledge_area_id', '=', 'ka.id_knowledge_area')
                                ->select('ag.id_assignment','ag.is_over')
                                ->where('top.id_topic',$id)->where('ag.is_over',0)
                                ->get();
                
                if($isIncurse->isEmpty()){
                    $result = $build->select('sub.id_subtopic')->get();

                    DB::table('uni_contents_vs_elements')
                        ->whereIn('subtopic_n_id',$result->pluck('id_subtopic')->toArray())
                        ->delete();

                    $build->update(['top.is_deleted' => 1, 'sub.is_deleted' => 1, 'q.is_deleted' => 1]);
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
            $msg = "El tema está siendo cursado";
            $icon = "error";
        }

        if ($success) {
            $msg = "Se eliminó el registro con éxito";
            $icon = "success";
        }

        return redirect()->back()->with(['message' => $msg, 'icon' => $icon]);
    }
}
