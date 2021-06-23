<?php

namespace App\Http\Controllers\Mgr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Sys\Sequence;
use App\Uni\KnowledgeArea;
use App\Uni\Module;
use App\Uni\Course;
use App\Uni\Topic;

class TopicsController extends Controller
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
        $this->newRoute = "topics.create";
        $this->storeRoute = "topics.store";
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
            $lTopics = $lTopics->where('module_id', $courseId);
        }

        $lTopics = $lTopics->get();

        return view('mgr.topics.index')->with('title', $title)
                                        ->with('newRoute', $this->newRoute)
                                        ->with('courseId', $courseId)
                                        ->with('lTopics', $lTopics);
    }
}
