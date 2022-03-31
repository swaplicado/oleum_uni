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
    public function takeCourse($course, $points, $idAssignment, $bApproved = false)
    {
        return $this->processTake($course, $points, $idAssignment, $bApproved);
    }

    private function processTake($idCourse, $points, $idAssignment, $bApproved)
    {
        $oCourse = Course::find($idCourse);
        $oModule = Module::find($oCourse->module_id);

        $lTake = TakingControl::where('status_id', '<=', !$bApproved ? config('csys.take_status.CUR') : config('csys.take_status.COM'))
                                ->where('element_type_id', config('csys.elem_type.AREA'))
                                ->where('knowledge_area_n_id', $oModule->knowledge_area_id)
                                ->where('student_id', \Auth::id())
                                ->where('assignment_id', $idAssignment)
                                ->orderBy('dtt_take', 'DESC')
                                ->get();

        $config = \App\Utils\Configuration::getConfigurations();

        $grouper = "";
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
            $oTakeArea->assignment_id = $idAssignment;

            // Crear toma de modulo con agrupador de área
            $oTakeModule = clone $oTakeArea;
            $oTakeModule->element_type_id = config('csys.elem_type.MODULE');
            $oTakeModule->knowledge_area_n_id = null;
            $oTakeModule->module_n_id = $oCourse->module_id;

            $$oTakeModule = new TakingControl($oTakeModule->toArray());

            // Crear toma de curso
            $oTakeCourse = clone $oTakeModule;
            $oTakeCourse->min_grade = $config->grades->approved;
            $oTakeCourse->university_points = $points;
            $oTakeCourse->element_type_id = config('csys.elem_type.COURSE');
            $oTakeCourse->knowledge_area_n_id = null;
            $oTakeCourse->module_n_id = null;
            $oTakeCourse->course_n_id = $idCourse;

            $oTakeCourse = new TakingControl($oTakeCourse->toArray());

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
            $lTakeModule = TakingControl::where('status_id', '<=', !$bApproved ? config('csys.take_status.CUR') : config('csys.take_status.COM'))
                                ->where('element_type_id', config('csys.elem_type.MODULE'))
                                ->where('student_id', \Auth::id())
                                ->where('assignment_id', $idAssignment)
                                ->where('module_n_id', $oCourse->module_id)
                                ->orderBy('dtt_take', 'DESC')
                                ->get();

            if (count($lTakeModule) == 0) {
                $oTakeArea = TakingControl::where('element_type_id', config('csys.elem_type.AREA'))
                                ->where('knowledge_area_n_id', $oModule->knowledge_area_id)
                                ->where('student_id', \Auth::id())
                                ->where('assignment_id', $idAssignment)
                                ->orderBy('dtt_take', 'DESC')
                                ->first();

                if ($oTakeArea == null) {
                    return null;
                }

                // Crear toma de modulo con agrupador de área
                $oTakeModule = clone $oTakeArea;
                $oTakeModule->dtt_take = Carbon::now()->toDateTimeString();
                $oTakeModule->dtt_end = null;
                $oTakeModule->element_type_id = config('csys.elem_type.MODULE');
                $oTakeModule->knowledge_area_n_id = null;
                $oTakeModule->module_n_id = $oCourse->module_id;
                $oTakeModule->assignment_id = $idAssignment;

                $oTakeModule = new TakingControl($oTakeModule->toArray());

                // Crear toma de curso
                $oTakeCourse = clone $oTakeModule;
                $oTakeCourse->dtt_take = Carbon::now()->toDateTimeString();
                $oTakeCourse->dtt_end = null;
                $oTakeCourse->min_grade = $config->grades->approved;
                $oTakeCourse->university_points = $points;
                $oTakeCourse->element_type_id = config('csys.elem_type.COURSE');
                $oTakeCourse->knowledge_area_n_id = null;
                $oTakeCourse->module_n_id = null;
                $oTakeCourse->course_n_id = $idCourse;
                $oTakeCourse->assignment_id = $idAssignment;

                $oTakeCourse = new TakingControl($oTakeCourse->toArray());

                try {
                    \DB::beginTransaction();
        
                    $oTakeModule->save();
                    $oTakeCourse->save();
    
                    \DB::commit();

                    return $oTakeModule->grouper;
                }
                catch (\Throwable $th) {
                    \DB::rollBack();
                }

            }
            else {
                $lTake = TakingControl::where('status_id', '<=', !$bApproved ? config('csys.take_status.CUR') : config('csys.take_status.COM'))
                                ->where('element_type_id', config('csys.elem_type.COURSE'))
                                ->where('student_id', \Auth::id())
                                ->where('assignment_id', $idAssignment)
                                ->where('course_n_id', $idCourse)
                                ->orderBy('dtt_take', 'DESC')
                                ->get();

                if (count($lTake) == 0) {
                    $oTakeArea = TakingControl::where('element_type_id', config('csys.elem_type.AREA'))
                                ->where('knowledge_area_n_id', $oModule->knowledge_area_id)
                                ->where('student_id', \Auth::id())
                                ->where('assignment_id', $idAssignment)
                                ->orderBy('dtt_take', 'DESC')
                                ->first();

                    // Crear toma de curso
                    $oTakeCourse = clone $oTakeArea;
                    $oTakeCourse->dtt_take = Carbon::now()->toDateTimeString();
                    $oTakeCourse->dtt_end = null;
                    $oTakeCourse->min_grade = $config->grades->approved;
                    $oTakeCourse->university_points = $points;
                    $oTakeCourse->element_type_id = config('csys.elem_type.COURSE');
                    $oTakeCourse->knowledge_area_n_id = null;
                    $oTakeCourse->module_n_id = null;
                    $oTakeCourse->course_n_id = $idCourse;
                    $oTakeCourse->assignment_id = $idAssignment;

                    $oTakeCourse = new TakingControl($oTakeCourse->toArray());

                    try {
                        \DB::beginTransaction();
            
                        $oTakeCourse->save();
        
                        \DB::commit();

                        return $oTakeCourse->grouper;
                    }
                    catch (\Throwable $th) {
                        \DB::rollBack();
                    }
                }
                else {
                    $oTakeArea = $lTake[0];
                    $grouper = $oTakeArea->grouper;

                    return $grouper;
                }
            }

            $oTakeArea = $lTake[0];
            $grouper = $oTakeArea->grouper;

            return $grouper;
        }
    }

    public function takeSubtopic($takeGrouper, $oSubtopic, $idAssignment, $bApproved = false)
    {
        // Validar si ya existe la toma de subtema
        $lTake = TakingControl::where('status_id', '<=', !$bApproved ? config('csys.take_status.EVA') : config('csys.take_status.COM'))
                                ->where('element_type_id', config('csys.elem_type.SUBTOPIC'))
                                ->where('subtopic_n_id', $oSubtopic->id_subtopic)
                                ->where('grouper', $takeGrouper)
                                ->where('assignment_id', $idAssignment)
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
                                ->where('assignment_id', $idAssignment)
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
            $oTakeTopic->assignment_id = $idAssignment;

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
        $oTakeSubtopic->assignment_id = $idAssignment;

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

        return [$oTakeControlEval->id_taken_control, $oTakeControlEval->assignment_id];
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

    /**
     * Verifica si los elementos han sido completados
     *
     * @param TakingControl $oTakeSubtopic
     * 
     * @return oCompleted [subtopic, topic, course, module, area]
     */
    public function verifyCompleted($oTakeSubtopic)
    {
        $oCompleted = (object) [ 'subtopic' => false, 
                        'topic' => false, 
                        'course' => false,
                        'module' => false,
                        'area' => false,
                        'points' => 0 ];

        $oSubtopic = SubTopic::find($oTakeSubtopic->subtopic_n_id);

        $lSubtopics = \DB::table('uni_subtopics AS sub')
                            ->where('topic_id', $oSubtopic->topic_id)
                            ->where('is_deleted', false)
                            ->get();

        $subIds = $lSubtopics->pluck('id_subtopic');
        
        $takes = \DB::table('uni_taken_controls AS tc')
                            ->where('grouper', $oTakeSubtopic->grouper)
                            ->where('assignment_id', $oTakeSubtopic->assignment_id)
                            ->where('is_deleted', false)
                            ->where('is_evaluation', false)
                            ->where('student_id', \Auth::id())
                            ->where('status_id', '=', config('csys.take_status.COM'))
                            ->whereColumn('grade', '>=', 'min_grade')
                            ->where('element_type_id', config('csys.elem_type.SUBTOPIC'))
                            ->whereIn('subtopic_n_id', $subIds)
                            ->orderBy('id_taken_control', 'DESC')
                            ->get();

        $oCompleted->subtopic = true;

        if (count($takes) == count($lSubtopics)) {
            $sum = 0;
            foreach ($takes as $take) {
                $sum += $take->grade;
            }

            $grade = $sum / count($takes);

            $topicTake = \DB::table('uni_taken_controls AS tc')
                            ->where('grouper', $oTakeSubtopic->grouper)
                            ->where('assignment_id', $oTakeSubtopic->assignment_id)
                            ->where('is_deleted', false)
                            ->where('is_evaluation', false)
                            ->where('student_id', \Auth::id())
                            ->where('element_type_id', config('csys.elem_type.TOPIC'))
                            ->where('topic_n_id', $oSubtopic->topic_id)
                            ->orderBy('id_taken_control', 'DESC')
                            ->take(1)
                            ->get();
            
            TakingControl::where('id_taken_control', $topicTake[0]->id_taken_control)
                            ->update([
                                        'dtt_end' => Carbon::now()->toDateTimeString(),
                                        'status_id' => (config('csys.take_status.COM')),
                                        'grade' => $grade,
                                        'min_grade' => $oTakeSubtopic->min_grade,
                                    ]);

            $oCompleted->topic = true;

            $oTopic = Topic::find($oSubtopic->topic_id);

            $completed = $this->verifyCourse($oTopic->course_id, \Auth::id(), $oTakeSubtopic->grouper, $oTakeSubtopic->assignment_id, $oTakeSubtopic->min_grade);

            if ($completed) {
                
                $oCourse = Course::find($oTopic->course_id);

                $oCompleted->course = true;
                $oCompleted->has_points = $oCourse->has_points;
                $oCompleted->points = $oCourse->has_points ? $oCourse->university_points : 0;

                $moduleCompleted = $this->verifyModule($oCourse->module_id, \Auth::id(), $oTakeSubtopic->grouper, $oTakeSubtopic->assignment_id, $oTakeSubtopic->min_grade);

                if ($moduleCompleted) {
                    $oCompleted->module = true;
                    
                    $oModule = Module::find($oCourse->module_id);
    
                    $areaCompleted = $this->verifyArea($oModule->knowledge_area_id, \Auth::id(), $oTakeSubtopic->grouper, $oTakeSubtopic->assignment_id, $oTakeSubtopic->min_grade);
                    $oCompleted->area = $areaCompleted;
                }
            }
        }

        return $oCompleted;
    }

    public function verifyCourse($idCourse, $idStudent, $grouper, $assignment, $minGrade)
    {
        $lTopics = \DB::table('uni_topics AS top')
                            ->where('course_id', $idCourse)
                            ->where('is_deleted', false)
                            ->get();

        $sum = 0;
        foreach ($lTopics as $oTopic) {
            $grade = TakeUtils::isTopicApproved($oTopic->id_topic, $idStudent, $assignment, true);

            if ($grade[0]) {
                $sum += $grade[1];
            }
            else {
                return false;
            }
        }

        $courseGrade = count($lTopics) == 0 ? 0 : ($sum / count($lTopics));

        $courseTake = \DB::table('uni_taken_controls AS tc')
                        ->where('grouper', $grouper)
                        ->where('assignment_id', $assignment)
                        ->where('is_deleted', false)
                        ->where('is_evaluation', false)
                        ->where('student_id', $idStudent)
                        ->where('element_type_id', config('csys.elem_type.COURSE'))
                        ->where('course_n_id', $idCourse)
                        ->orderBy('id_taken_control', 'DESC')
                        ->first();
        
        TakingControl::where('id_taken_control', $courseTake->id_taken_control)
                        ->update([
                                    'dtt_end' => Carbon::now()->toDateTimeString(),
                                    'status_id' => (config('csys.take_status.COM')),
                                    'grade' => $courseGrade,
                                    'min_grade' => $minGrade,
                                    ]);
        
        return true;
    }

    public function verifyModule($idModule, $idStudent, $grouper, $assignment, $minGrade)
    {
        $lCourses = \DB::table('uni_courses AS co')
                            ->where('module_id', $idModule)
                            ->where('is_deleted', false)
                            ->get();

        $sum = 0;
        foreach ($lCourses as $oCourse) {
            $grade = TakeUtils::isCourseApproved($oCourse->id_course, $idStudent, $assignment, true);

            if ($grade[0]) {
                $sum += $grade[1];
            }
            else {
                return false;
            }
        }

        $moduleGrade = count($lCourses) == 0 ? 0 : ($sum / count($lCourses));

        $moduleTake = \DB::table('uni_taken_controls AS tc')
                        ->where('grouper', $grouper)
                        ->where('assignment_id', $assignment)
                        ->where('is_deleted', false)
                        ->where('is_evaluation', false)
                        ->where('student_id', $idStudent)
                        ->where('element_type_id', config('csys.elem_type.MODULE'))
                        ->where('module_n_id', $idModule)
                        ->orderBy('id_taken_control', 'DESC')
                        ->first();
        
        TakingControl::where('id_taken_control', $moduleTake->id_taken_control)
                        ->update([
                                    'dtt_end' => Carbon::now()->toDateTimeString(),
                                    'status_id' => (config('csys.take_status.COM')),
                                    'grade' => $moduleGrade,
                                    'min_grade' => $minGrade,
                                    ]);
        
        return true;
    }

    public function verifyArea($idArea, $idStudent, $grouper, $assignment, $minGrade)
    {
        $lModules = \DB::table('uni_modules AS mo')
                            ->where('knowledge_area_id', $idArea)
                            ->where('is_deleted', false)
                            ->get();

        $sum = 0;
        foreach ($lModules as $oModule) {
            $grade = TakeUtils::isModuleApproved($oModule->id_module, $idStudent, $assignment, true);

            if ($grade[0]) {
                $sum += $grade[1];
            }
            else {
                return false;
            }
        }

        $areaGrade = count($lModules) == 0 ? 0 : ($sum / count($lModules));

        $areaTake = \DB::table('uni_taken_controls AS tc')
                        ->where('grouper', $grouper)
                        ->where('assignment_id', $assignment)
                        ->where('is_deleted', false)
                        ->where('is_evaluation', false)
                        ->where('student_id', $idStudent)
                        ->where('element_type_id', config('csys.elem_type.AREA'))
                        ->where('knowledge_area_n_id', $idArea)
                        ->orderBy('id_taken_control', 'DESC')
                        ->first();
        
        TakingControl::where('id_taken_control', $areaTake->id_taken_control)
                        ->update([
                                    'dtt_end' => Carbon::now()->toDateTimeString(),
                                    'status_id' => (config('csys.take_status.COM')),
                                    'grade' => $areaGrade,
                                    'min_grade' => $minGrade,
                                    ]);

        Assignment::where('id_assignment', $assignment)
                    ->update(['is_over' => true]);
        
        return true;
    }
}
