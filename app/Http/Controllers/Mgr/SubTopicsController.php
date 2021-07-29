<?php

namespace App\Http\Controllers\Mgr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
}
