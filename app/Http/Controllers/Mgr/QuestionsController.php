<?php

namespace App\Http\Controllers\Mgr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Uni\Question;
use App\Uni\Answer;
use App\Uni\SubTopic;

class QuestionsController extends Controller
{
     /**
     * Show the application index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request, $idTopic = 0, $idSubtopic = 0)
    {
        $lQuestions = \DB::table('uni_questions AS q')
                        ->join('uni_subtopics AS s', 'q.subtopic_id', '=', 's.id_subtopic')
                        ->where('q.subtopic_id', $idSubtopic)
                        ->where('q.is_deleted', false)
                        ->selectRaw("(SELECT answer FROM uni_answers WHERE id_answer = q.answer_id) AS answer_text, q.*")
                        ->get();

        $oSubtopic = SubTopic::find($idSubtopic);

        return view("mgr.topics.subtopics.questions")->with('title', "Preguntas de subtema")
                                                    ->with('storeRoute', 'questions.store')
                                                    ->with('sGetQuestion', 'questions.getquestion')
                                                    ->with('updateRoute', 'questions.update')
                                                    ->with('deleteQuestionRoute', 'questions.delete')
                                                    ->with('delAnswerRoute', 'questions.delanswer')
                                                    ->with('oSubtopic', $oSubtopic)
                                                    ->with('idTopic', $idTopic)
                                                    ->with('idSubtopic', $idSubtopic)
                                                    ->with('lQuestions', $lQuestions);
    }

    public function getQuestion(Request $request) {
        $oQuestion = Question::find($request->question);

        $oQuestion->lAnswers = Answer::where('question_id', $request->question)
                                        ->where('is_deleted', false)
                                        ->get();

        return json_encode($oQuestion);
    }

    public function store(Request $request)
    {
        $question = json_decode($request->question);

        $oQuestion = new Question();

        $oQuestion->question = $question->question;
        $oQuestion->number_answers = $question->number_answers;
        $oQuestion->answer_feedback = $question->answer_feedback;
        $oQuestion->is_deleted = false;
        $oQuestion->answer_id = 0;
        $oQuestion->subtopic_id = $question->subtopic_id;
        $oQuestion->created_by_id = \Auth::id();
        $oQuestion->updated_by_id = \Auth::id();

        $oQuestion->save();

        $answer_id = $this->saveAnswers($oQuestion, $question->lAnswers, $question->answer);

        $oQuestion->answer_id = $answer_id;

        $oQuestion->save();

        $oQuestion->answer_text = (Answer::where('id_answer', $answer_id)->select('answer')->get())[0]->answer;

        $oQuestion->lAnswers = Answer::where('question_id', $oQuestion->id_question)
                                        ->where('is_deleted', false)
                                        ->get();

        return json_encode($oQuestion);
    }

    public function update(Request $request)
    {
        $question = json_decode($request->question);        

        try {
            \DB::beginTransaction();
            
            $oQuestion = Question::find($question->id_question);

            $answer_id = $this->saveAnswers($oQuestion, $question->lAnswers, $question->answer);

            $oQuestion->question = $question->question;
            $oQuestion->number_answers = $question->number_answers;
            $oQuestion->answer_id = $answer_id;
            $oQuestion->updated_by_id = \Auth::id();

            $oQuestion->save();

            \DB::commit();

        }
        catch (\Throwable $th) {
            \DB::rollBack();
            return $th;
        }

        $oQuestion->answer_text = (Answer::where('id_answer', $answer_id)->select('answer')->get())[0]->answer;
        $oQuestion->lAnswers = Answer::where('question_id', $oQuestion->id_question)
                                        ->where('is_deleted', false)
                                        ->get();

        return json_encode($oQuestion);
    }

    private function saveAnswers($oQuestion, $answers, $idAux)
    {
        $answerId = 0;
        if (count($answers) == 0) {
            return $answerId;
        }

        foreach ($answers as $answer) {
            if ($answer->id_answer > 0) {
                $oAnswer = Answer::find($answer->id_answer);
            }
            else {
                $oAnswer = new Answer();
                $oAnswer->is_deleted = false;
            }
            
            $oAnswer->content_n_id = null;
            $oAnswer->answer = $answer->answer;

            $oQuestion->answers()->save($oAnswer);

            if ($idAux == $answer->id_aux) {
                $answerId = $oAnswer->id_answer;
            }
        }

        return $answerId;
    }

    /**
     * Set b_del = true to Question
     *
     * @param Request $request
     * @return void
     */
    public function delete(Request $request)
    {
        $res = Question::where('id_question', $request->question)->update(['is_deleted' => true]);

        return json_encode($res);
    }

    /**
     * * Set b_del = true to Answer
     *
     * @param Request $request
     * @return void
     */
    public function deleteAnswer(Request $request)
    {
        $id = $request->answer;

        $res = Answer::where('id_answer', $id)->update(['is_deleted' => true]);

        return json_encode($res);
    }
}
