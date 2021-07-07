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

        session()->forget('questions');

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
                            ->where('is_deleted', false);

        if (count($ids) > 0) {
            $lQuestions = $lQuestions->whereNotIn('id_question', $ids);
        }
                            
        $lQuestions = $lQuestions->inRandomOrder()
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
                                        ->inRandomOrder()
                                        ->take($question->number_answers - 1)
                                        ->union($correct)
                                        ->inRandomOrder()
                                        ->get();

            if ($question->answer_feedback == "") {
                $question->answer_feedback = "Lorem ipsum dolor sit amet consectetur adipisicing elit. Provident laudantium amet est beatae temporibus? 
                                        Nobis obcaecati ipsam consectetur at veritatis possimus ea saepe, autem cumque iste eos odit, accusamus adipisci!";
            }
        }

        if (count($lQuestions) == 0) {
            return redirect()->back();
        }

        return view('uni.exams.exam')->with('lQuestions', $lQuestions)
                                    ->with('oTopic', $oTopic)
                                    ->with('oSubtopic', $oSubtopic)
                                    ->with('idCourse', $oTopic->course_id)
                                    ->with('sSuccessRoute', 'uni.courses.course') // course parameter
                                    ->with('sFailRoute', 'uni.courses.course.play') // subtopic parameter
                                    ->with('sRecordRoute', 'exam.recordanswer');
    }

    public function recordAnswer(Request $request)
    {
        $questions = json_decode($request->questions);

        if ($questions != null && count($questions) > 0) {
            session(['questions' => $questions]);
        }

        $config = \App\Utils\Configuration::getConfigurations();

        $scale = $config->grades->scale;
        $approved_grade = $config->grades->approved;

        $nQuestions = count($questions);
        $correctAnswers = 0;
        foreach ($questions as $question) {
            if ($question->is_correct) {
                $correctAnswers++;
            }
        }

        // nQuestions => scale
        // correctAnswers => ?

        $grade = ($correctAnswers * $scale / $nQuestions);

        $response = (object) [
                                'isApproved' => ($grade >= $approved_grade),
                                'grade' => $grade
                            ];

        return json_encode($response);
    }
}
