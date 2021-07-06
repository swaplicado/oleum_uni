<?php

namespace App\Http\Controllers\Uni;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Uni\SubTopic;
use App\Uni\Topic;
use App\Uni\Course;

class ExamsController extends Controller
{
    public function exam($subtopic = 0)
    {
        $oSubtopic = SubTopic::find($subtopic);
        $oTopic = Topic::find($oSubtopic->topic_id);

        if (session()->has('questions')) {
            $questions = session('questions');
            $ids = collect($questions)->pluck('id_question');
        }
        else {
            $ids = [];
        }

        $limit = $oSubtopic->number_questions - count($ids);
        
        $lQuestions = \DB::table('uni_questions AS q')
                            ->where('subtopic_id', $subtopic)
                            ->where('is_deleted', false)
                            ->whereNotIn('id_question', $ids)
                            ->take($limit)
                            ->get();

        foreach ($lQuestions as $question) {
            $correct = \DB::table('uni_answers AS a')
                            ->where('id_answer', $question->answer_id)
                            ->where('is_deleted', false)
                            ->take(1);

            $question->lAnswers = \DB::table('uni_answers AS a')
                                        ->where('question_id', $question->id_question)
                                        ->where('id_answer', '<>', $question->answer_id)
                                        ->where('is_deleted', false)
                                        ->take($question->number_answers - 1)
                                        ->union($correct)
                                        ->inRandomOrder()
                                        ->get();

            $question->feedback = "Lorem ipsum dolor sit amet consectetur adipisicing elit. Provident laudantium amet est beatae temporibus? 
                                    Nobis obcaecati ipsam consectetur at veritatis possimus ea saepe, autem cumque iste eos odit, accusamus adipisci!";
        }

        return view('uni.exams.exam')->with('lQuestions', $lQuestions)
                                    ->with('oTopic', $oTopic)
                                    ->with('oSubtopic', $oSubtopic)
                                    ->with('sRecordRoute', 'exam.recordanswer');
    }

    public function recordAnswer(Request $request)
    {
        $questionId = $request->id_question;
        $answerId = $request->id_answer;

        $question = (object) [
                                'id_question' => $questionId,
                                'id_answer' => $answerId,
                            ];

        if (session()->has('questions')) {
            $questions = session('questions');
        }
        else {
            $questions = [];
        }

        $questions[] = $question;

        session(['questions' => $questions]);

        return json_encode($questions);
    }
}
