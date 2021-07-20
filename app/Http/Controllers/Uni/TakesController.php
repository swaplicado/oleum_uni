<?php

namespace App\Http\Controllers\Uni;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\Utils\TakeUtils;

use App\Uni\TakingControl;
use App\Uni\TakingContent;
use App\Uni\TakingSubTopicQuestion;
use App\Uni\Assignment;
use App\Uni\KnowledgeArea;
use App\Uni\Module;
use App\Uni\Course;
use App\Uni\Topic;
use App\Uni\SubTopic;

class TakesController extends Controller
{
    public function takeCourse($course, $points)
    {
        return $this->processTake($course, $points);
    }

    private function processTake($idCourse, $points)
    {
        $oCourse = Course::find($idCourse);
        $oModule = Module::find($oCourse->module_id);

        $lTake = TakingControl::where('status_id', '<=', config('csys.take_status.CUR'))
                                ->where('element_type_id', config('csys.elem_type.AREA'))
                                ->where('knowledge_area_n_id', $oModule->knowledge_area_id)
                                ->where('student_id', \Auth::id())
                                ->orderBy('dtt_take', 'DESC')
                                ->get();

        if (count($lTake) == 0) {
            
            $oTakeArea = new TakingControl();
            
            // Crear toma de área con agrupador
            $oTakeArea->grouper = hash('ripemd160', Carbon::now()->toDateTimeString());
            $oTakeArea->dtt_take = Carbon::now()->toDateTimeString();
            $oTakeArea->dtt_end = null;
            $oTakeArea->min_grade = 0;
            $oTakeArea->grade = 0;
            $oTakeArea->university_points = 0;
            $oTakeArea->num_questions = 0;
            $oTakeArea->is_evaluation = false;
            $oTakeArea->is_deleted = false;
            $oTakeArea->element_type_id = config('csys.elem_type.AREA');
            $oTakeArea->knowledge_area_n_id = $oModule->knowledge_area_id;
            $oTakeArea->module_n_id = null;
            $oTakeArea->course_n_id = null;
            $oTakeArea->topic_n_id = null;
            $oTakeArea->subtopic_n_id = null;
            $oTakeArea->student_id = \Auth::id();
            $oTakeArea->status_id = config('csys.take_status.CUR');

            // Crear toma de modulo con agrupador de área
            $oTakeModule = clone $oTakeArea;
            $oTakeModule->element_type_id = config('csys.elem_type.MODULE');
            $oTakeModule->knowledge_area_n_id = null;
            $oTakeModule->module_n_id = $oCourse->module_id;

            // Crear toma de curso
            $config = \App\Utils\Configuration::getConfigurations();

            $oTakeCourse = clone $oTakeModule;
            $oTakeCourse->min_grade = $config->grades->approved;
            $oTakeCourse->university_points = $points;
            $oTakeCourse->element_type_id = config('csys.elem_type.COURSE');
            $oTakeCourse->knowledge_area_n_id = null;
            $oTakeCourse->module_n_id = null;
            $oTakeCourse->course_n_id = $idCourse;

            try {
                \DB::beginTransaction();
    
                $oTakeArea->save();
                $oTakeModule->save();
                $oTakeCourse->save();

                \DB::commit();

                return $oTakeArea->grouper;

            } catch (\Throwable $th) {
                \DB::rollBack();
            }

        }
        else {
            return $lTake[0]->grouper;
        }
    }

    public function takeSubtopic($takeGrouper, $oSubtopic)
    {
        // Validar si ya existe la toma de subtema
        $lTake = TakingControl::where('status_id', '<=', config('csys.take_status.EVA'))
                                ->where('element_type_id', config('csys.elem_type.SUBTOPIC'))
                                ->where('subtopic_n_id', $oSubtopic->id_subtopic)
                                ->where('grouper', $takeGrouper)
                                ->where('is_deleted', false)
                                ->where('is_evaluation', false)
                                ->where('student_id', \Auth::id())
                                ->orderBy('dtt_take', 'DESC')
                                ->get();

        if (count($lTake) > 0) {
            return $lTake[0]->id_taken_control;
        }

        // Validar si ya existe la toma de TEMA
        $lTake = TakingControl::where('status_id', '<=', config('csys.take_status.CUR'))
                                ->where('element_type_id', config('csys.elem_type.TOPIC'))
                                ->where('topic_n_id', $oSubtopic->topic_id)
                                ->where('grouper', $takeGrouper)
                                ->where('student_id', \Auth::id())
                                ->orderBy('dtt_take', 'DESC')
                                ->get();
        
        if (count($lTake) == 0) {
            // Crear toma de tema con agrupador
            $oTakeTopic = new TakingControl();

            $oTakeTopic->grouper = $takeGrouper;
            $oTakeTopic->dtt_take = Carbon::now()->toDateTimeString();
            $oTakeTopic->dtt_end = null;
            $oTakeTopic->min_grade = 0;
            $oTakeTopic->grade = 0;
            $oTakeTopic->university_points = 0;
            $oTakeTopic->num_questions = 0;
            $oTakeTopic->is_evaluation = false;
            $oTakeTopic->is_deleted = false;
            $oTakeTopic->element_type_id = config('csys.elem_type.TOPIC');
            $oTakeTopic->knowledge_area_n_id = null;
            $oTakeTopic->module_n_id = null;
            $oTakeTopic->course_n_id = null;
            $oTakeTopic->topic_n_id = $oSubtopic->topic_id;
            $oTakeTopic->subtopic_n_id = null;
            $oTakeTopic->student_id = \Auth::id();
            $oTakeTopic->status_id = config('csys.take_status.CUR');

            $oTakeTopic->save();
        }

        // si la toma no existe, crearla
        $oTakeSubtopic = new TakingControl();
            
        // Crear toma de subtema con agrupador
        $oTakeSubtopic->grouper = $takeGrouper;
        $oTakeSubtopic->dtt_take = Carbon::now()->toDateTimeString();
        $oTakeSubtopic->dtt_end = null;
        $oTakeSubtopic->min_grade = 0;
        $oTakeSubtopic->grade = 0;
        $oTakeSubtopic->university_points = 0;
        $oTakeSubtopic->num_questions = 0;
        $oTakeSubtopic->is_evaluation = false;
        $oTakeSubtopic->is_deleted = false;
        $oTakeSubtopic->element_type_id = config('csys.elem_type.SUBTOPIC');
        $oTakeSubtopic->knowledge_area_n_id = null;
        $oTakeSubtopic->module_n_id = null;
        $oTakeSubtopic->course_n_id = null;
        $oTakeSubtopic->topic_n_id = null;
        $oTakeSubtopic->subtopic_n_id = $oSubtopic->id_subtopic;
        $oTakeSubtopic->student_id = \Auth::id();
        $oTakeSubtopic->status_id = config('csys.take_status.CUR');

        $oTakeSubtopic->save();

        return $oTakeSubtopic->id_taken_control;
    }

    public function takeSubtopicContent(Request $request)
    {
        return $this->takeContent($request->take_control, $request->is_close, $request->content);
    }

    public function takeContent($takeControl, $isClose, $content = 0)
    {
        $lastContentTake = \DB::table('uni_taken_controls AS tc')
                                ->join('uni_taken_contents AS tco', 'tc.id_taken_control', '=', 'tco.take_control_id')
                                ->where('tc.student_id', \Auth::id())
                                ->where('tco.take_control_id', $takeControl)
                                ->select('tco.id_content_taken')
                                ->whereNull('tco.dtt_end')
                                ->orderBy('tco.dtt_take', 'DESC')
                                ->get();

        if (count($lastContentTake) > 0) {
            $iLastTake = $lastContentTake[0]->id_content_taken; 
            $oLastTake = TakingContent::find($iLastTake);
            
            if ($content == 0) {
                return $oLastTake->content_id;
            }

            $oLastTake->dtt_end = Carbon::now()->toDateTimeString();
            $oLastTake->save();
        }
        else if ($content == 0) {
            return 0;
        }

        if ($isClose) {
            return 0;
        }

        $oTake = new TakingContent();
    
        $oTake->dtt_take = Carbon::now()->toDateTimeString();
        $oTake->dtt_end = null;
        $oTake->is_deleted = false;
        $oTake->take_control_id = $takeControl;
        $oTake->content_id = $content;

        $oTake->save();

        return 0;
    }

    public function takeEvaluation($takeControl)
    {
        $oTakeControl = TakingControl::find($takeControl);

        $oTakeControlEval = $oTakeControl->replicate();
        $oTakeControlEval->id_taken_control = 0;
        $oTakeControlEval->is_evaluation = true;
        $oTakeControlEval->dtt_take = Carbon::now()->toDateTimeString();
        $oTakeControlEval->dtt_end = null;
        $oTakeControlEval->is_deleted = false;
        $oTakeControlEval->status_id = config('csys.take_status.EVA');

        $oTakeControlEval->save();

        return $oTakeControlEval->id_taken_control;
    }

    public function saveQuestions($takeEvaluation, $lQuestions)
    {
        foreach ($lQuestions as $question) {
            $oTakenQuestion = new TakingSubTopicQuestion();
    
            $oTakenQuestion->is_correct = false;
            $oTakenQuestion->is_deleted = false;
            $oTakenQuestion->take_control_id = $takeEvaluation;
            $oTakenQuestion->question_id = $question->id_question;
            $oTakenQuestion->answer_n_id = null;

            $oTakenQuestion->save();
        }
    }

    public function verifyCompleted($oTakeSubtopic)
    {
        $oSubtopic = SubTopic::find($oTakeSubtopic->subtopic_n_id);

        $lSubtopics = \DB::table('uni_subtopics AS sub')
                            ->where('topic_id', $oSubtopic->topic_id)
                            ->where('is_deleted', false)
                            ->get();

        $subIds = $lSubtopics->pluck('id_subtopic');
        
        $takes = \DB::table('uni_taken_controls AS tc')
                            ->where('grouper', $oTakeSubtopic->grouper)
                            ->where('is_deleted', false)
                            ->where('is_evaluation', false)
                            ->where('student_id', \Auth::id())
                            ->where('status_id', '=', config('csys.take_status.COM'))
                            ->whereColumn('grade', '>=', 'min_grade')
                            ->where('element_type_id', config('csys.elem_type.SUBTOPIC'))
                            ->whereIn('subtopic_n_id', $subIds)
                            ->get();

        if (count($takes) == count($lSubtopics)) {
            $sum = 0;
            foreach ($takes as $take) {
                $sum += $take->grade;
            }

            $grade = $sum / count($takes);

            $topicTake = \DB::table('uni_taken_controls AS tc')
                            ->where('grouper', $oTakeSubtopic->grouper)
                            ->where('is_deleted', false)
                            ->where('is_evaluation', false)
                            ->where('student_id', \Auth::id())
                            ->where('element_type_id', config('csys.elem_type.TOPIC'))
                            ->where('topic_n_id', $oSubtopic->topic_id)
                            ->take(1)
                            ->get();
            
            TakingControl::where('id_taken_control', $topicTake[0]->id_taken_control)
                            ->update([
                                        'dtt_end' => Carbon::now()->toDateTimeString(),
                                        'status_id' => (config('csys.take_status.COM')),
                                        'grade' => $grade,
                                        'min_grade' => $oTakeSubtopic->min_grade,
                                    ]);
        }
    }
}
