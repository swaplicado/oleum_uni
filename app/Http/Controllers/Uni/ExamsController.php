<?php

namespace App\Http\Controllers\Uni;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\Uni\SubTopic;
use App\Uni\Topic;
use App\Uni\Course;
use App\Uni\TakingControl;
use App\Uni\TakingSubTopicQuestion;

class ExamsController extends Controller
{
    public function exam($subtopic, $idSubtopicTaken, $takenGrouper)
    {
        $oSubtopic = SubTopic::find($subtopic);
        $oTopic = Topic::find($oSubtopic->topic_id);

        $limit = $oSubtopic->number_questions;
        
        $lQuestions = $this->getQuestions($takenGrouper, $subtopic, $limit);

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
            return redirect()->back()->withError("No existen suficientes preguntas para la evaluación");
        }

        try {
            \DB::beginTransaction();

            //Registrar toma de evaluación y preguntas
            $controller = new TakesController();
            $takeEvaluation = $controller->takeEvaluation($idSubtopicTaken);
            $controller->saveQuestions($takeEvaluation[0], $lQuestions);

            \DB::commit();
        }
        catch (\Throwable $th) {
            \DB::rollBack();
            return redirect()->back()->withError($th->getMessage());
        }

        return view('uni.exams.exam')->with('lQuestions', $lQuestions)
                                        ->with('oTopic', $oTopic)
                                        ->with('oSubtopic', $oSubtopic)
                                        ->with('idSubtopicTaken', $idSubtopicTaken)
                                        ->with('takenGrouper', $takenGrouper)
                                        ->with('takeEvaluation', $takeEvaluation[0])
                                        ->with('idCourse', $oTopic->course_id)
                                        ->with('idAssignment', $takeEvaluation[1])
                                        ->with('sSuccessRoute', 'uni.courses.course') // course parameter
                                        ->with('sFailRoute', 'uni.courses.course.play') // subtopic parameter
                                        ->with('sRecordRoute', 'exam.record.answer')
                                        ->with('sRecordExam', 'exam.record.exam');
    }

    /**
     * Obtener preguntas para la evaluación actual
     *
     * @param [type] $takenGrouper
     * @param [type] $subtopic
     * @return void
     */
    private function getQuestions($takenGrouper, $subtopic, $limit, $idEval = 0)
    {
        /**
         * Consultar preguntas que ya se hicieron en otras evaluaciones
         */
        $lEvaltakes = \DB::table('uni_taken_controls AS tc')
                            ->where('is_deleted', false)
                            ->where('is_evaluation', true)
                            ->where('student_id', \Auth::id())
                            ->where('grouper', $takenGrouper);
        if ($idEval > 0) {
            $lEvaltakes = $lEvaltakes->where('id_taken_control', '>', $idEval);
        }

        $lEvaltakes = $lEvaltakes->orderBy('id_taken_control', 'DESC')
                                    ->pluck('id_taken_control');

        /**
         * Obtener preguntas hechas en otras evaluaciones
         */
        if (count($lEvaltakes) > 0) {
            $ids = \DB::table('uni_taken_questions AS ques')
                                    ->where('is_deleted', false)
                                    ->whereIn('take_control_id', $lEvaltakes)
                                    ->pluck('question_id');
        }
        else {
            $ids = [];
        }
        
        /**
         * Obtención de preguntas para evaluación
         */                                
        $lQuestions = \DB::table('uni_questions AS q')
                            ->where('subtopic_id', $subtopic)
                            ->where('is_deleted', false);

        if (count($ids) > 0) {
            $lQuestions = $lQuestions->whereNotIn('id_question', $ids);
        }
                            
        $lQuestions = $lQuestions->inRandomOrder()
                                    ->take($limit)
                                    ->get();
        
        if (count($lQuestions) < $limit) {
            $idEv = count($lEvaltakes) > 0 ? $lEvaltakes[0] : 0;
            if ($idEv == 0) {
                return [];
            }

            $lQuestions = $this->getQuestions($takenGrouper, $subtopic, $limit, $idEv);
            if (count($lQuestions) < $limit) {
                return [];
            }
        }

        return $lQuestions;
    }

    /**
     * Registra la respuesta del usuario a la pregunta actual
     *
     * @param Request $request
     * @return void
     */
    public function recordAnswer(Request $request)
    {
        $question = json_decode($request->question);

        $lQuestion = \DB::table('uni_taken_questions AS tq')
                            ->where('take_control_id', $question->take_evaluation)
                            ->where('question_id', $question->id_question)
                            ->where('is_deleted', false)
                            ->select('id_question_taken')
                            ->limit(1)
                            ->get();

        $oTakenQ = TakingSubTopicQuestion::find($lQuestion[0]->id_question_taken);

        $oTakenQ->answer_n_id = $question->id_answer;
        $oTakenQ->is_correct = $question->is_correct;

        $oTakenQ->save();

        return json_encode($oTakenQ);
    }

    public function recordExam(Request $request)
    {
        $nQuestions = $request->number_questions;
        $takeEval = $request->take_evaluation;
        $takeSubtopic = $request->take_subtopic;

        $lQuestions = \DB::table('uni_taken_questions AS tq')
                            ->where('take_control_id', $takeEval)
                            ->where('is_deleted', false)
                            ->where('is_correct', true)
                            ->get();

        $correctAnswers = count($lQuestions);

        $config = \App\Utils\Configuration::getConfigurations();

        $scale = $config->grades->scale;

        $oTakeSub = TakingControl::find($takeSubtopic);
        $oTakeEval = TakingControl::find($takeEval);
        $oTakeCourse = TakingControl::where('is_deleted', false)
                                        ->where('element_type_id', config('csys.elem_type.COURSE'))
                                        ->where('grouper', $oTakeSub->grouper)
                                        ->orderBy('id_taken_control', 'DESC')
                                        ->get();

        $approved_grade = $oTakeCourse[0]->min_grade;

        $grade = ($correctAnswers * $scale / $nQuestions);

        try {
            \DB::beginTransaction();

            $oTakeSub->min_grade = $approved_grade;
            $oTakeSub->grade = $grade;
            $oTakeSub->dtt_end = Carbon::now()->toDateTimeString();
            $oTakeSub->status_id = config('csys.take_status.COM');
            $oTakeSub->save();


            $oTakeEval->min_grade = $approved_grade;
            $oTakeEval->grade = $grade;
            $oTakeEval->dtt_end = Carbon::now()->toDateTimeString();
            $oTakeEval->status_id = config('csys.take_status.COM');
            $oTakeEval->save();

            $controller = new TakesController();
            $takeEvaluation = $controller->verifyCompleted($oTakeSub);

            \DB::commit();
        }
        catch (\Throwable $th) {
            \DB::rollBack();
        }

        $response = (object) [
                                'isApproved' => ($grade >= $approved_grade),
                                'grade' => $grade
                            ];

        return json_encode($response);
    }
}
