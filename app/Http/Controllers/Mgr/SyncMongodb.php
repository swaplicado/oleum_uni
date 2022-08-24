<?php

namespace App\Http\Controllers\Mgr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Uni\mQuadrant;
use App\Uni\mModule;
use App\Uni\mCourse;
use App\Uni\mTopic;
use App\Uni\mSubtopic;
use App\Uni\mTakedExams;

class SyncMongodb extends Controller
{
    public function syncronizer(){
        $out = new \Symfony\Component\Console\Output\ConsoleOutput();
        try {
            $session = \DB::connection('mongodb')->getMongoClient()->startSession();
            $session->startTransaction();
    
            $lAssignments = \DB::table('uni_assignments')
                                ->where('is_deleted', 0)
                                ->get();
    
            foreach ($lAssignments as $oAssignment){
                $lModules = \DB::table('uni_modules as m')
                                ->join('uni_assignments_module_control as amc', 'amc.module_n_id', '=', 'm.id_module')
                                ->where('amc.assignment_id', $oAssignment->id_assignment)
                                ->where('amc.is_deleted', 0)
                                ->where('m.is_deleted', 0)
                                ->select('m.*', 'amc.dt_open', 'amc.dt_close')
                                ->get();
    
                $quadrant = \DB::table('uni_knowledge_areas')
                                ->where('id_knowledge_area', $oAssignment->knowledge_area_id)
                                ->first();
                            
                $oQuadrant = json_encode($quadrant, JSON_UNESCAPED_UNICODE);
                
                $mQuadrant = new mQuadrant();
                $mQuadrant->assignment_id = $oAssignment->id_assignment;
                $mQuadrant->student_id = $oAssignment->student_id;
                $mQuadrant->quadrant_id = $oAssignment->knowledge_area_id;
                $mQuadrant->element_body = $oQuadrant;
                $mQuadrant->grade = 0;
                $mQuadrant->is_delete = 0;
                $mQuadrant->dt_open = $oAssignment->dt_assignment;
                $mQuadrant->dt_end = $oAssignment->dt_end;
                $mQuadrant->save();
    
                foreach($lModules as $module){
                    $mModule = new mModule();
                    $mModule->assignment_id = $oAssignment->id_assignment;
                    $mModule->student_id = $oAssignment->student_id;
                    $mModule->module_id = $module->id_module;
                    $mModule->quadrant_id = (Integer) $oAssignment->knowledge_area_id;
                    $mModule->grade = 0;
                    $mModule->is_delete = 0;
                    $mModule->dt_open = $module->dt_open;
                    $mModule->dt_end = $module->dt_close;
                    
                    unset($module->dt_open);
                    unset($module->dt_close);
                    $oModule = json_encode($module, JSON_UNESCAPED_UNICODE);
                    $mModule->element_body = $oModule;
                    $mModule->save();

                    $lCourses = \DB::table('uni_courses as c')
                                    ->join('uni_assignments_courses_control as acc', 'acc.course_n_id', '=', 'c.id_course')
                                    // ->join('uni_assignments_module_control as amc', 'amc.module_n_id', '=', 'acc.module_n_id')
                                    ->where('acc.assignment_id', $oAssignment->id_assignment)
                                    ->where('acc.module_n_id', $module->id_module)
                                    ->where('acc.is_deleted', 0)
                                    ->where('c.is_deleted', 0)
                                    ->get();

                    foreach($lCourses as $course){
                        $mCourse = new mCourse();
                        $mCourse->assignment_id = $oAssignment->id_assignment;
                        $mCourse->student_id = $oAssignment->student_id;
                        $mCourse->course_id = $course->id_course;
                        $mCourse->module_id = $module->id_module;
                        $mCourse->grade = 0;
                        $mCourse->is_delete = 0;
                        $mCourse->dt_open = $course->dt_open;
                        $mCourse->dt_end = $course->dt_close;
                        
                        unset($course->dt_open);
                        unset($course->dt_close);
                        $oCourse = json_encode($course, JSON_UNESCAPED_UNICODE);
                        $mCourse->element_body = $oCourse;
                        $mCourse->save();
    
                        $lTopics = \DB::table('uni_topics')
                                        ->where([['is_deleted', 0], ['course_id', $course->id_course]])
                                        ->get();

                        foreach($lTopics as $topic){
                            $lSubtopics = \DB::table('uni_subtopics')
                                                ->where([['is_deleted', 0], ['topic_id', $topic->id_topic]])
                                                ->get();
    
                            $oTopic = json_encode($topic, JSON_UNESCAPED_UNICODE);
    
                            $mTopic = new mTopic();
                            $mTopic->assignment_id = $oAssignment->id_assignment;
                            $mTopic->student_id = $oAssignment->student_id;
                            $mTopic->topic_id = $topic->id_topic;
                            $mTopic->course_id = $course->id_course;
                            $mTopic->element_body = $oTopic;
                            $mTopic->grade = 0;
                            $mTopic->is_delete = 0;
                            $mTopic->save();
    
                            foreach($lSubtopics as $subtopic){
                                $out->write('['.
                                        $oAssignment->student_id.', '.
                                        $oAssignment->id_assignment.', '.
                                        $quadrant->id_knowledge_area.', '.
                                        $module->id_module.', '.
                                        $course->id_course.', '.
                                        $subtopic->id_subtopic.
                                        '] : ');

                                $lQuestions = \DB::table('uni_questions as q')
                                                    ->where([['is_deleted', 0], ['subtopic_id', $subtopic->id_subtopic]])
                                                    ->get();
    
                                foreach($lQuestions as $question){
                                    $lAnswers = \DB::table('uni_answers')
                                                    ->where([['is_deleted', 0],['question_id', $question->id_question]])
                                                    ->get();
                                    $question->answers = json_encode($lAnswers, JSON_UNESCAPED_UNICODE);
                                }
    
                                $oQuestions = json_encode($lQuestions, JSON_UNESCAPED_UNICODE);
                                $oSubtopic = json_encode($subtopic, JSON_UNESCAPED_UNICODE);
    
                                $mSubtopic = new mSubtopic();
                                $mSubtopic->assignment_id = $oAssignment->id_assignment;
                                $mSubtopic->student_id = $oAssignment->student_id;
                                $mSubtopic->subtopic_id = $subtopic->id_subtopic;
                                $mSubtopic->topic_id = $topic->id_topic;
                                $mSubtopic->element_body = $oSubtopic;
                                $mSubtopic->questions = $oQuestions;
                                $mSubtopic->grade = 0;
                                $mSubtopic->is_delete = 0;
                                $mSubtopic->save();

                                $takenControl = \DB::table('uni_taken_controls')
                                                    ->where('assignment_id', $oAssignment->id_assignment)
                                                    ->where('subtopic_n_id', $subtopic->id_subtopic)
                                                    ->where('is_deleted', 0)
                                                    ->where('is_evaluation', 1)
                                                    ->get();

                                                    
                                if(!is_null($takenControl)){
                                    if(count($takenControl) > 0){
                                        $num_taked = count($takenControl);
                                        $taken_exam = $takenControl->last();
                                        $takenQuestions = \DB::table('uni_taken_questions AS tq')
                                                            ->join('uni_questions as q', 'tq.question_id', '=', 'q.id_question')
                                                            ->where('take_control_id', $taken_exam->id_taken_control)
                                                            ->where('tq.is_deleted', 0)
                                                            ->where('q.is_deleted', 0)
                                                            ->select('tq.*', 'q.question')
                                                            ->get();
    
                                        $mTakedExam = new mTakedExams();
                            
                                        $mTakedExam->assignment_id = $taken_exam->assignment_id;
                                        $mTakedExam->student_id = $taken_exam->student_id;
                                        $mTakedExam->subtopic_id = $taken_exam->subtopic_n_id;
                                        $mTakedExam->grade = $taken_exam->grade;
                                        $mTakedExam->num_taked = $num_taked;
                                        $mTakedExam->date_taked = $taken_exam->updated_at;
                                        $mTakedExam->take_control_id = $taken_exam->id_taken_control;
                                        $mTakedExam->element_body = json_encode($takenQuestions, JSON_UNESCAPED_UNICODE);
                                        $mTakedExam->is_delete = $taken_exam->is_deleted;
                                        $mTakedExam->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $session->commitTransaction();
            dd("Fin de la sincronización");
        } catch (\Throwable $th) {
            $session->abortTransaction();
            dd($th);
        }
    }
}
