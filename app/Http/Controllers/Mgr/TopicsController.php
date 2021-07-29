<?php

namespace App\Http\Controllers\Mgr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Sys\Sequence;
use App\Uni\KnowledgeArea;
use App\Uni\Module;
use App\Uni\Course;
use App\Uni\Topic;
use App\Uni\SubTopic;

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
            $topic->lSubtopics = SubTopic::where('topic_id', $topic->id_topic)->get();
        }

        $seq = Sequence::selectRaw('CONCAT(code, " - ", sequence) AS seq, id_sequence')
                        ->get();

        return view('mgr.topics.index')->with('title', $title)
                                        ->with('storeRoute', $this->storeRoute)
                                        ->with('storeSubRoute', $this->storeSubRoute)
                                        ->with('courseId', $courseId)
                                        ->with('sequences', $seq)
                                        ->with('lTopics', $lTopics);
    }

    public function store(Request $request)
    {
        $topic = json_decode($request->topic);

        $oTopic = new Topic();
        
        $oTopic->topic = $topic->topic;
        $oTopic->hash_id = hash('ripemd160', $oTopic->topic);
        $oTopic->is_deleted = false;
        $oTopic->course_id = $topic->course_id;
        $oTopic->sequence_id = $topic->secuence_id;
        $oTopic->created_by_id = \Auth::id();
        $oTopic->updated_by_id = \Auth::id();

        $oTopic->save();

        return json_encode($oTopic);
    }
}
