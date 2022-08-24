<?php namespace App\Utils;

use Carbon\Carbon;
use App\Uni\Assignment;
use App\Uni\CourseControl;
use App\Uni\ModuleControl;
use App\Uni\mQuadrant;
use App\Uni\mModule;
use App\Uni\mCourse;
use App\Uni\mTopic;
use App\Uni\mSubtopic;
use App\Uni\mTakedExams;

class assignmentsUtils {
    public static function upTakedExams($otakedExams, $lQuestions){
        $mTakedExam = mTakedExams::where('assignment_id', (Integer)$otakedExams->assignment_id)
                                ->where('subtopic_id', (Integer)$otakedExams->subtopic_n_id)
                                ->where('student_id', (Integer)$otakedExams->student_id)
                                ->where('is_delete', 0)
                                ->first();

        if(!is_null($mTakedExam)){
            $mTakedExam->grade = $otakedExams->grade;
            $mTakedExam->num_taked = $mTakedExam->num_taked + 1;
            $mTakedExam->date_taked = $otakedExams->dtt_take;
            $mTakedExam->element_body = json_encode($lQuestions, JSON_UNESCAPED_UNICODE);
            $mTakedExam->is_delete = $otakedExams->is_deleted;
            $mTakedExam->update();
        }else{
            $mTakedExam = new mTakedExams();

            $mTakedExam->assignment_id = $otakedExams->assignment_id;
            $mTakedExam->student_id = $otakedExams->student_id;
            $mTakedExam->subtopic_id = $otakedExams->subtopic_n_id;
            $mTakedExam->grade = $otakedExams->grade;
            $mTakedExam->num_taked = 1;
            $mTakedExam->date_taked = $otakedExams->dtt_take;
            $mTakedExam->take_control_id = $otakedExams->id_taken_control;
            $mTakedExam->element_body = json_encode($lQuestions, JSON_UNESCAPED_UNICODE);
            $mTakedExam->is_delete = $otakedExams->is_deleted;
            $mTakedExam->save();
        }
    }

    public static function upQuestions($question){
        $course_id = \DB::table('uni_questions as q')
                        ->join('uni_subtopics as sub', 'sub.id_subtopic', '=', 'q.subtopic_id')
                        ->join('uni_topics as top', 'sub.topic_id', '=', 'top.id_topic')
                        ->where('q.id_question', (Integer)$question->id_question)
                        ->where('top.is_deleted', 0)
                        ->value('top.course_id');

        $lmCourses = mCourse::where('course_id', (Integer)$course_id)
                            ->where('dt_end', '>=', Carbon::now()->toDateString())
                            ->where('is_delete', 0)
                            ->get();

        foreach($lmCourses as $mCourse){
            $takes = \DB::table('uni_taken_controls')
                                ->where([
                                    ['assignment_id', (Integer)$mCourse->assignment_id],
                                    ['subtopic_n_id', (Integer)$question->subtopic_id]
                                ])
                                ->get()
                                ->last();

            if(!is_null($takes)){
                $approved = $takes->grade >= $takes->min_grade;
            }else{
                $approved = false;
            }

            if(!$approved){
                $mSubtopic = mSubtopic::where([
                                        ['assignment_id', $mCourse->assignment_id],
                                        ['subtopic_id', (Integer)$question->subtopic_id]
                                    ])
                                    ->first();
    
                $lQuestions = \DB::table('uni_questions as q')
                                ->where([['is_deleted', 0], ['subtopic_id', $question->subtopic_id]])
                                ->get();
    
                foreach($lQuestions as $question){
                    $lAnswers = \DB::table('uni_answers')
                                    ->where([['is_deleted', 0],['question_id', $question->id_question]])
                                    ->get();
    
                    $question->answers = json_encode($lAnswers, JSON_UNESCAPED_UNICODE);
                }

                if(!is_null($mSubtopic)){
                    $mSubtopic->questions = json_encode($lQuestions, JSON_UNESCAPED_UNICODE);
                    $mSubtopic->update();
                }
            }
        }
    }

    public static function createSubtopicMongo($subtopic){
        $course_id = \DB::table('uni_subtopics as sub')
                        ->join('uni_topics as top', 'sub.topic_id', '=', 'top.id_topic')
                        ->where('sub.id_subtopic', (Integer)$subtopic->id_subtopic)
                        ->where('top.is_deleted', 0)
                        ->value('top.course_id');

        $lmCourses = mCourse::where('course_id', (Integer)$course_id)
                            ->where('dt_end', '>=', Carbon::now()->toDateString())
                            ->where('is_delete', 0)
                            ->get();

        foreach($lmCourses as $mCourse){
            $takes = \DB::table('uni_taken_controls')
                                ->where([
                                    ['assignment_id', $mCourse->assignment_id],
                                    ['course_n_id', (Integer)$course_id]
                                ])
                                ->get()
                                ->last();

            if(!is_null($takes)){
                $approved = $takes->grade >= $takes->min_grade;
            }else{
                $approved = false;
            }

            if(!$approved){
                $mSubtopic = new mSubtopic();
                $mSubtopic->assignment_id = $mCourse->assignment_id;
                $mSubtopic->student_id = $mCourse->student_id;
                $mSubtopic->subtopic_id = (Integer)$subtopic->id_subtopic;
                $mSubtopic->topic_id = (Integer)$subtopic->topic_id;
                $mSubtopic->element_body = json_encode($subtopic, JSON_UNESCAPED_UNICODE);
                $mSubtopic->questions = [];
                $mSubtopic->grade = 0;
                $mSubtopic->is_delete = 0;
                $mSubtopic->save();
            }
        }
    }

    public static function upSubtopicMongo($subtopic){
        $course_id = \DB::table('uni_subtopics as sub')
                        ->join('uni_topics as top', 'sub.topic_id', '=', 'top.id_topic')
                        ->where('sub.id_subtopic', (Integer)$subtopic->id_subtopic)
                        ->where('top.is_deleted', 0)
                        ->value('top.course_id');

        $lmCourses = mCourse::where('course_id', (Integer)$course_id)
                            ->where('dt_end', '>=', Carbon::now()->toDateString())
                            ->where('is_delete', 0)
                            ->get();

        foreach($lmCourses as $mCourse){
            $takes = \DB::table('uni_taken_controls')
                                ->where([
                                    ['assignment_id', $mCourse->assignment_id],
                                    ['subtopic_n_id', $subtopic->id_subtopic]
                                ])
                                ->get()
                                ->last();

            if(!is_null($takes)){
                $approved = $takes->grade >= $takes->min_grade;
            }else{
                $approved = false;
            }

            if(!$approved){
                $mSubtopic = mSubtopic::where([
                                        ['assignment_id', $mCourse->assignment_id],
                                        ['topic_id', (Integer)$subtopic->topic_id]
                                    ])
                                    ->first();
    
                $lQuestions = \DB::table('uni_questions as q')
                                ->where([['is_deleted', 0], ['subtopic_id', $subtopic->id_subtopic]])
                                ->get();
    
                foreach($lQuestions as $question){
                    $lAnswers = \DB::table('uni_answers')
                                    ->where([['is_deleted', 0],['question_id', $question->id_question]])
                                    ->get();
    
                    $question->answers = json_encode($lAnswers, JSON_UNESCAPED_UNICODE);
                }
    
                $mSubtopic->element_body = json_encode($subtopic, JSON_UNESCAPED_UNICODE);
                $mSubtopic->questions = json_encode($lQuestions, JSON_UNESCAPED_UNICODE);
                $mSubtopic->update();
            }
        }
    }

    public static function createTopicsMongo($topic){
        $lmCourses = mCourse::where('course_id', (Integer)$topic->course_id)
                            ->where('dt_end', '>=', Carbon::now()->toDateString())
                            ->where('is_delete', 0)
                            ->get();
        
        foreach($lmCourses as $mCourse){
            $takes = \DB::table('uni_taken_controls')
                                ->where([
                                    ['assignment_id', $mCourse->assignment_id],
                                    ['course_n_id', $topic->course_id]
                                ])
                                ->get()
                                ->last();

            if(!is_null($takes)){
                $approved = $takes->grade >= $takes->min_grade;
            }else{
                $approved = false;
            }

            if(!$approved){
                $mTopic = new mTopic();
                $mTopic->assignment_id = $mCourse->assignment_id;
                $mTopic->student_id = $mCourse->student_id;
                $mTopic->topic_id = $topic->id_topic;
                $mTopic->course_id = $mCourse->course_id;
                $mTopic->element_body = json_encode($topic, JSON_UNESCAPED_UNICODE);
                $mTopic->grade = 0;
                $mTopic->is_delete = 0;
                $mTopic->save();
            }
        }
    }

    public static function upTopicsMongo($topic){
        $lmCourses = mCourse::where('course_id', $topic->course_id)
                            ->where('dt_end', '>=', Carbon::now()->toDateString())
                            ->where('is_delete', 0)
                            ->get();
        
        foreach($lmCourses as $mCourse){
            $takes = \DB::table('uni_taken_controls')
                                ->where([
                                    ['assignment_id', $mCourse->assignment_id],
                                    ['course_n_id', $topic->course_id]
                                ])
                                ->get()
                                ->last();

            if(!is_null($takes)){
                $approved = $takes->grade >= $takes->min_grade;
            }else{
                $approved = false;
            }

            if(!$approved){
                $mTopic = mTopic::where([
                                    ['assignment_id', $mCourse->assignment_id],
                                    ['course_id', $mCourse->course_id]
                                ])->first();
    
                $mTopic->element_body = json_encode($topic, JSON_UNESCAPED_UNICODE);
                $mTopic->update();
            }
        }
    }

    public static function createCoursesMongo($oAssignment, $module_id, $course, $oCourseControl){
        $mCourse = new mCourse();
        $mCourse->assignment_id = $oAssignment->id_assignment;
        $mCourse->student_id = $oAssignment->student_id;
        $mCourse->course_id = $course->id_course;
        $mCourse->module_id = $module_id;
        $mCourse->element_body = json_encode($course, JSON_UNESCAPED_UNICODE);;
        $mCourse->grade = 0;
        $mCourse->is_delete = 0;
        $mCourse->dt_open = $oCourseControl->dt_open;
        $mCourse->dt_end = $oCourseControl->dt_close;
        $mCourse->save();
    }

    public static function upCourseMongo($oAssignment, $module_id, $course, $oCourseControl){
        $mCourse = mCourse::where([
            ['assignment_id', $oAssignment->id_assignment],
            ['module_id', $module_id],
            ['course_id', $course->id_course],
            ['is_delete', 0]
        ])->first();

        $mCourse->element_body = json_encode($course, JSON_UNESCAPED_UNICODE);
        $mCourse->dt_open = $oCourseControl->dt_open;
        $mCourse->dt_end = $oCourseControl->dt_close;
        $mCourse->update();
    }

    public static function createModulesMongo($oAssignment, $module, $oModuleControl){
        $mModule = new mModule();
        $mModule->assignment_id = $oAssignment->id_assignment;
        $mModule->student_id = $oAssignment->student_id;
        $mModule->module_id = $module->id_module;
        $mModule->quadrant_id = (Integer) $oAssignment->knowledge_area_id;
        $mModule->element_body = json_encode($module, JSON_UNESCAPED_UNICODE);
        $mModule->grade = 0;
        $mModule->is_delete = 0;
        $mModule->dt_open = $oModuleControl->dt_open;
        $mModule->dt_end = $oModuleControl->dt_close;
        $mModule->save();
    }

    public static function upModulesMongo($oAssignment, $module, $oModuleControl){
        $mModule = mModule::where([
                                ['assignment_id', $oAssignment->id_assignment],
                                ['module_id', $module->id_module],
                                ['is_delete', 0]
                            ])->first();

        $mModule->element_body = json_encode($module, JSON_UNESCAPED_UNICODE);
        $mModule->dt_open = $oModuleControl->dt_open;
        $mModule->dt_end = $oModuleControl->dt_close;
        $mModule->update();
    }

    public static function getModuleAssignments($ka_id){
        $lAssignments = \DB::table('uni_assignments')
                            ->where('knowledge_area_id', $ka_id)
                            ->where('is_deleted', 0)
                            // ->where('dt_assignment', '<=', Carbon::now()->toDateString())
                            ->where('dt_end', '>=', Carbon::now()->toDateString())
                            ->get();

        return count($lAssignments);
    }

    public static function getCourseAssignments($module_id){
        $ka_id = \DB::table('uni_modules')
                        ->where('id_module', $module_id)
                        ->where('is_deleted', 0)
                        ->pluck('id_module');

        $lAssignments = \DB::table('uni_assignments')
                            ->where('knowledge_area_id', $ka_id)
                            ->where('dt_assignment', '<=', Carbon::now()->toDateString())
                            ->where('dt_end', '>=', Carbon::now()->toDateString())
                            ->get();

        return count($lAssignments);
    }

    public static function setModuleAssignments($ka_id){
        $lAssignments = \DB::table('uni_assignments')
                            ->where('knowledge_area_id', $ka_id)
                            ->where('is_deleted', 0)
                            ->where('dt_assignment', '<=', Carbon::now()->toDateString())
                            ->where('dt_end', '>=', Carbon::now()->toDateString())
                            ->get();
        
        foreach($lAssignments as $assign){
            $lModules = \DB::table('uni_modules')
                            ->where([['is_deleted', 0],['knowledge_area_id', $assign->knowledge_area_id]])
                            ->orderBy('id_module')
                            ->get();
            
            foreach($lModules as $module){
                $dates = assignmentsUtils::getDatesModule($lModules, $module, $assign->dt_assignment);
                $closeDate = Carbon::parse($dates[0]);
                $openDate = Carbon::parse($dates[1]);
                $assignDate = Carbon::parse($assign->dt_assignment);
                $assignDateEnd = Carbon::parse($assign->dt_end);

                if($closeDate->gt($assignDateEnd)){
                    $oAssignment = Assignment::find($assign->id_assignment);
                    $oAssignment->dt_end = $closeDate->format('Y-m-d');
                    $oAssignment->update();
                }

                $oModuleControl = ModuleControl::where([
                                                    ['assignment_id', $assign->id_assignment],
                                                    ['module_n_id', $module->id_module],
                                                    ['is_deleted', 0],
                                                ])
                                                ->first();

                if(!is_null($oModuleControl)){
                    if($closeDate->gte(Carbon::now()->toDateString())){
                        $oModuleControl->dt_close = $closeDate->format('Y-m-d');
                        $oModuleControl->dt_open = $openDate->format('Y-m-d');
                        $oModuleControl->update();

                        assignmentsUtils::upModulesMongo($assign, $module, $oModuleControl);
                        assignmentsUtils::setCoursesAssignment($assign, $oModuleControl);
                    }
                }else{
                    $oModuleControl = new ModuleControl();
                    $oModuleControl->assignment_id = $assign->id_assignment;
                    $oModuleControl->dt_close = $closeDate->format('Y-m-d');
                    $oModuleControl->dt_open = $openDate->format('Y-m-d');
                    $oModuleControl->module_n_id = $module->id_module;
                    $oModuleControl->student_id = $assign->student_id;
                    $oModuleControl->is_deleted = false;
                    $oModuleControl->created_by = \Auth::id();
                    $oModuleControl->updated_by = \Auth::id();

                    $oModuleControl->save();

                    assignmentsUtils::createModulesMongo($assign, $module, $oModuleControl);
                }
            }
        }
    }

    public static function setCoursesAssignment($assign, $moduleControl){
        $lCourses = \DB::table('uni_courses')
                        ->where([['is_deleted', 0], ['module_id', $moduleControl->module_n_id]])
                        ->get();

        foreach($lCourses as $course){
            $courseDates = assignmentsUtils::getDatesCourse($lCourses, $course, $moduleControl->dt_open);
            $courseCloseDate = Carbon::parse($courseDates[0]);
            $courseOpenDate = Carbon::parse($courseDates[1]);

            if($courseCloseDate->gt($moduleControl->dt_close)){
                $oModule = ModuleControl::find($moduleControl->id_module_control);
                $oModule->dt_close = $courseCloseDate->format('Y-m-d');
                $oModule->update();
            }

            $oCourseControl = CourseControl::where([
                                                ['assignment_id', $assign->id_assignment],
                                                ['course_n_id', $course->id_course],
                                                ['is_deleted', 0],
                                            ])
                                            ->first();

            if(!is_null($oCourseControl)){
                $oCourseControl->dt_close = $courseCloseDate->format('Y-m-d');
                $oCourseControl->dt_open = $courseOpenDate->format('Y-m-d');
                $oCourseControl->update();

                assignmentsUtils::upCourseMongo($assign, $moduleControl->module_n_id, $course, $oCourseControl);
            }else{
                $oCourseControl = new CourseControl();
                $oCourseControl->assignment_id = $assign->id_assignment;
                $oCourseControl->dt_close = $courseCloseDate->format('Y-m-d');
                $oCourseControl->dt_open = $courseOpenDate->format('Y-m-d');
                $oCourseControl->course_n_id = $course->id_course;
                $oCourseControl->module_n_id = $moduleControl->module_n_id;
                $oCourseControl->student_id = $assign->student_id;
                $oCourseControl->is_deleted = false;
                $oCourseControl->created_by = \Auth::id();
                $oCourseControl->updated_by = \Auth::id();

                $oCourseControl->save();

                assignmentsUtils::createCoursesMongo($assign, $moduleControl->module_n_id, $course, $oCourseControl);
            }
        }
    }

    public static function getDatesModule($lModules, $module, $assign_date){
        $days = $module->completion_days;
        $oModule = $module;
        if (!is_null($oModule->pre_module_id)) {
            foreach($lModules as $m){
                $oModule = $lModules->where('id_module', $oModule->pre_module_id)->first();
                $days = $days + $oModule->completion_days;
                if(is_null($oModule->pre_module_id)){
                    break;
                }
            }
        }

        $closeDate = Carbon::parse($assign_date);
        $closeDate->addDays($days);
        $openDate = clone $closeDate;
        $closeDate->subDay();
        $openDate->subDays($module->completion_days);

        return [$closeDate->format('Y-m-d'), $openDate->format('Y-m-d')];
    }

    public static function getDatesCourse($lCourses, $course, $oModuleControl_dt_open){
        $days = $course->completion_days;
        $oCourse = $course;
        if (!is_null($oCourse->pre_course_id)) {
            foreach($lCourses as $c){
                $oCourse = $lCourses->where('id_course', $oCourse->pre_course_id)->first();
                $days = $days + $oCourse->completion_days;
                if(is_null($oCourse->pre_course_id)){
                    break;
                }
            }
        }

        $closeDate = Carbon::parse($oModuleControl_dt_open);
        $closeDate->addDays($days);
        $openDate = clone $closeDate;
        $closeDate->subDay();
        $openDate->subDays($course->completion_days);

        return [$closeDate->format('Y-m-d'), $openDate->format('Y-m-d')];
    }

    public static function getQuadrantFromCourse($course_id){
        $quadrant = \DB::table('uni_courses as c')
                        ->join('uni_modules as m', 'm.id_module', '=', 'c.module_id')
                        ->join('uni_knowledge_areas as ka', 'ka.id_knowledge_area', '=', 'm.knowledge_area_id')
                        ->where('c.id_course', $course_id)
                        ->where('m.is_deleted', 0)
                        ->where('ka.is_deleted', 0)
                        ->value('ka.id_knowledge_area');

        return $quadrant;
    }

    public static function getCuadranteIdFromModule($module_id){
        $cuadrante = \DB::table('uni_modules as m')
                        ->join('uni_knowledge_areas as ka', 'ka.id_knowledge_area', '=', 'm.knowledge_area_id')
                        ->select('ka.id_knowledge_area')
                        ->where('m.id_module', $module_id)
                        ->where('ka.is_deleted', 0)
                        ->value('ka.id_knowledge_area');

        return $cuadrante;
    }

    public static function validateTotalDaysInModule($module_id){
        $moduleDays = \DB::table('uni_modules')
                        ->where('id_module', $module_id)
                        ->value('completion_days');

        $lCourses = \DB::table('uni_courses')
                        ->where('module_id', $module_id)
                        ->get();
        
        $preCourses = $lCourses->unique('pre_course_id')->sortByDesc('pre_course_id');
        $days = 0;
        foreach($preCourses as $course){
            $days = $days + $course->completion_days;
        }

        $maxDays = $lCourses->where('pre_course_id', null)->max('completion_days');
        if($maxDays > $days){
            $days = $maxDays;
        }

        return $days <= $moduleDays;
    }
}