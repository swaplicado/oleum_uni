<?php namespace App\Utils;

use Carbon\Carbon;

use App\Uni\Prerequisite;
use App\Uni\PrerequisiteRow;
use App\Uni\TakingControl;
use App\Uni\Assignment;
use App\Uni\KnowledgeArea;
use App\Uni\Module;
use App\Uni\Course;
use App\Uni\Topic;
use App\Uni\SubTopic;

class TakeUtils {

    public static function getTakingCourses($idStudent = null)
    {
        $student = $idStudent == null ? \Auth::id() : $idStudent;

        $lCourses =  \DB::table('uni_taken_controls AS tc')
                            ->join('uni_courses AS co', 'tc.course_n_id', '=', 'co.id_course')
                            ->join('uni_assignments AS a', 'tc.assignment_id', '=', 'a.id_assignment')
                            ->where('tc.student_id', $student)
                            ->where(function ($query) {
                                $query->where('tc.status_id', config('csys.take_status.CUR'))
                                    ->orWhere('tc.status_id', config('csys.take_status.EVA'));
                            })
                            ->where('tc.element_type_id', config('csys.elem_type.COURSE'))
                            ->whereRaw('NOW() BETWEEN a.dt_assignment AND a.dt_end')
                            ->where('tc.is_deleted', false)
                            ->where('co.is_deleted', false)
                            ->select('tc.grouper',
                                        'tc.dtt_take',
                                        'tc.dtt_end',
                                        'tc.min_grade',
                                        'tc.grade',
                                        'tc.university_points',
                                        'tc.status_id',
                                        'tc.assignment_id',
                                        'co.id_course',
                                        'co.course',
                                        'co.course_key',
                                        'co.hash_id',
                                        'co.description',
                                        'co.objectives',
                                        'co.completion_days',
                                        'co.is_deleted',
                                        'co.module_id',
                                        'co.elem_status_id',
                                        'co.sequence_id'
                                    )
                            ->get();

        foreach ($lCourses as $course) {
            $course->contents = \DB::table('uni_contents_vs_elements AS cel')
                                    ->join('uni_edu_contents AS con', 'cel.content_id', '=', 'con.id_content')
                                    ->where('cel.course_n_id', $course->id_course)
                                    ->where('cel.element_type_id', config('csys.elem_type.COURSE'))
                                    ->select('con.*')
                                    ->get();
        }

        return $lCourses;
    }

    public static function validateSubtopicTake($idSubtopic, $idAssignment)
    {
        $oSubTopic = SubTopic::find($idSubtopic);
        $oTopic = Topic::find($oSubTopic->topic_id);

        if ($oTopic->sequence_id == config('csys.sys_sequences.SHUFFLE')) {
            return true;
        }

        $subtopics = SubTopic::where('is_deleted', false)
                                ->where('topic_id', $oSubTopic->topic_id)
                                ->select('id_subtopic')
                                ->orderBy('id_subtopic', 'ASC')
                                ->get();

        if ($subtopics[0]->id_subtopic == $idSubtopic) {
            return true;
        }

        $index = 0;
        while ($subtopics[$index]->id_subtopic != $idSubtopic) {
            if (TakeUtils::isSubtopicApproved($subtopics[$index]->id_subtopic, \Auth::id(), $idAssignment)) {
                $index++;
            }
            else {
                return false;
            }
        }

        return true;
    }

    public static function isSubtopicApproved($idSubtopic, $student, $idAssignment, $withGrade = false)
    {
        $takes = \DB::table('uni_taken_controls AS tc')
                    ->where('tc.student_id', $student)
                    ->where('tc.assignment_id', $idAssignment)
                    ->where('tc.element_type_id', config('csys.elem_type.SUBTOPIC'))
                    ->where('tc.subtopic_n_id', $idSubtopic)
                    ->where('tc.is_deleted', false)
                    ->where('tc.is_evaluation', false)
                    ->orderBy('id_taken_control', 'DESC')
                    ->get();

        if (count($takes) == 0) {
            return $withGrade ? [false, null] : false;
        }

        $approved = $takes[0]->status_id == config('csys.take_status.COM') && $takes[0]->grade >= $takes[0]->min_grade;

        if ($withGrade) {
            return [$approved, $takes[0]->grade];
        }

        return $approved;
    }

    public static function isTopicApproved($idTopic, $student, $idAssignment, $withGrade = false)
    {
        $takes = \DB::table('uni_taken_controls AS tc')
                    ->where('tc.student_id', $student)
                    ->where('tc.assignment_id', $idAssignment)
                    ->where('tc.element_type_id', config('csys.elem_type.TOPIC'))
                    ->where('tc.topic_n_id', $idTopic)
                    ->where('tc.is_deleted', false)
                    ->where('tc.is_evaluation', false)
                    ->orderBy('id_taken_control', 'DESC')
                    ->get();

        if (count($takes) == 0) {
            return $withGrade ? [false, null] : false;
        }

        $approved = $takes[0]->status_id == config('csys.take_status.COM') && $takes[0]->grade >= $takes[0]->min_grade;

        if ($withGrade) {
            return [$approved, $takes[0]->grade];
        }

        return $approved;
    }

    public static function isCourseApproved($idCourse, $student, $idAssignment, $withGrade = false)
    {
        $takes = \DB::table('uni_taken_controls AS tc')
                    ->where('tc.student_id', $student)
                    ->where('tc.assignment_id', $idAssignment)
                    ->where('tc.element_type_id', config('csys.elem_type.COURSE'))
                    ->where('tc.course_n_id', $idCourse)
                    ->where('tc.is_deleted', false)
                    ->where('tc.is_evaluation', false)
                    ->orderBy('id_taken_control', 'DESC')
                    ->get();

        if (count($takes) == 0) {
            return $withGrade ? [false, null] : false;
        }

        $approved = $takes[0]->status_id == config('csys.take_status.COM') && $takes[0]->grade >= $takes[0]->min_grade;

        if ($withGrade) {
            return [$approved, $takes[0]->grade];
        }

        return $approved;
    }

    public static function isModuleApproved($idModule, $student, $idAssignment, $withGrade = false)
    {
        $takes = \DB::table('uni_taken_controls AS tc')
                    ->where('tc.student_id', $student)
                    ->where('tc.assignment_id', $idAssignment)
                    ->where('tc.element_type_id', config('csys.elem_type.MODULE'))
                    ->where('tc.module_n_id', $idModule)
                    ->where('tc.is_deleted', false)
                    ->where('tc.is_evaluation', false)
                    ->orderBy('id_taken_control', 'DESC')
                    ->get();

        if (count($takes) == 0) {
            return $withGrade ? [false, null] : false;
        }

        $approved = $takes[0]->status_id == config('csys.take_status.COM') && $takes[0]->grade >= $takes[0]->min_grade;

        if ($withGrade) {
            return [$approved, $takes[0]->grade];
        }

        return $approved;
    }

    public static function isAreaApproved($idArea, $student, $idAssignment, $withGrade = false)
    {
        $takes = \DB::table('uni_taken_controls AS tc')
                    ->where('tc.student_id', $student)
                    ->where('tc.assignment_id', $idAssignment)
                    ->where('tc.element_type_id', config('csys.elem_type.AREA'))
                    ->where('tc.knowledge_area_n_id', $idArea)
                    ->where('tc.is_deleted', false)
                    ->where('tc.is_evaluation', false)
                    ->orderBy('id_taken_control', 'DESC')
                    ->get();

        if (count($takes) == 0) {
            return $withGrade ? [false, null] : false;
        }

        $approved = $takes[0]->status_id == config('csys.take_status.COM') && $takes[0]->grade >= $takes[0]->min_grade;

        if ($withGrade) {
            return [$approved, $takes[0]->grade];
        }

        return $approved;
    }

    public static function isSomeAreaApproved($idArea, $student)
    {
        $takes = \DB::table('uni_taken_controls AS tc')
                    ->where('tc.student_id', $student)
                    ->where('tc.element_type_id', config('csys.elem_type.AREA'))
                    ->where('tc.knowledge_area_n_id', $idArea)
                    ->where('tc.is_deleted', false)
                    ->where('tc.is_evaluation', false)
                    ->orderBy('id_taken_control', 'DESC')
                    ->get();

        if (count($takes) == 0) {
            return false;
        }

        $approved = $takes[0]->status_id == config('csys.take_status.COM') && $takes[0]->grade >= $takes[0]->min_grade;

        return $approved;
    }

    public static function isSomeModuleApproved($idModule, $student)
    {
        $takes = \DB::table('uni_taken_controls AS tc')
                    ->where('tc.student_id', $student)
                    ->where('tc.element_type_id', config('csys.elem_type.MODULE'))
                    ->where('tc.module_n_id', $idModule)
                    ->where('tc.is_deleted', false)
                    ->where('tc.is_evaluation', false)
                    ->orderBy('id_taken_control', 'DESC')
                    ->get();

        if (count($takes) == 0) {
            return false;
        }

        $approved = $takes[0]->status_id == config('csys.take_status.COM') && $takes[0]->grade >= $takes[0]->min_grade;

        return $approved;
    }

    public static function isSomeCourseApproved($idCourse, $student)
    {
        $takes = \DB::table('uni_taken_controls AS tc')
                    ->where('tc.student_id', $student)
                    ->where('tc.element_type_id', config('csys.elem_type.COURSE'))
                    ->where('tc.course_n_id', $idCourse)
                    ->where('tc.is_deleted', false)
                    ->where('tc.is_evaluation', false)
                    ->orderBy('id_taken_control', 'DESC')
                    ->get();

        if (count($takes) == 0) {
            return false;
        }

        $approved = $takes[0]->status_id == config('csys.take_status.COM') && $takes[0]->grade >= $takes[0]->min_grade;

        return $approved;
    }

    public static function getlAssignmentPercentCompleted($idAsigment, $idArea, $iStudent = 0)
    {
        $assig_percent = 0;
        $tot_mod = 0;

        $student = $iStudent == 0 ? \Auth::id() : $iStudent;

        $lModules = \DB::table('uni_assignments AS a')
                        ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                        ->join('uni_modules AS m', 'ka.id_knowledge_area', '=', 'm.knowledge_area_id')
                        ->select('m.*', 'a.id_assignment', 'a.dt_end')
                        ->where('a.id_assignment', $idAsigment)
                        ->where('a.is_deleted', false)
                        ->where('a.student_id', $student)
                        ->where('m.is_deleted', false)
                        ->where('m.knowledge_area_id', $idArea)
                        ->get();
        foreach($lModules as $module){
            $result = TakeUtils::getModulePercentCompleted($module->id_module, $idAsigment);
            $module->completed_percent = number_format($result[0]);
            $module->grade = TakeUtils::isCourseApproved($module->id_module, $iStudent, $module->id_assignment, true);
            $module->courses = $result[1];
            $assig_percent = $assig_percent + $module->completed_percent;
            $tot_mod = $tot_mod + 1;
            $end_courses = 0;
            $promedio = 0;
            foreach($module->courses as $course){
                if($course->completed_percent == 100){
                    $end_courses = $end_courses + 1;
                }
                is_null($course->grade[1]) ? $course->grade[1] = 0 : '';
                $promedio = $promedio + $course->grade[1];
            }
            $tot_courses = count($module->courses) == 0 ? 1 : count($module->courses);
            $module->promedio = number_format($promedio / $tot_courses, 2);
            $module->end_courses = $end_courses;
        }
        $tot_mod == 0 ? $tot_mod = 1 : '';
        $completed_percent = $assig_percent/$tot_mod;

        return [$completed_percent, $lModules];
    }

    public static function getModulePercentCompleted($idModule, $idAssigment, $iStudent = 0)
    {
        $module_percent = 0;
        $tot_cour = 0;
        $student = $iStudent == 0 ? \Auth::id() : $iStudent;

        $lCourses = \DB::table('uni_assignments AS a')
                        ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                        ->join('uni_modules AS m', 'ka.id_knowledge_area', '=', 'm.knowledge_area_id')
                        ->join('uni_courses AS c', 'm.id_module', '=', 'c.module_id')
                        ->select('c.*', 'a.id_assignment')
                        ->where('a.id_assignment', $idAssigment)
                        ->where('a.is_deleted', false)
                        ->where('a.student_id', $student)
                        ->where('m.is_deleted', false)
                        ->where('c.is_deleted', false)
                        ->where('c.module_id', $idModule)
                        ->where('c.elem_status_id', '>=', config('csys.elem_status.EDIT'))
                        ->get();

        foreach ($lCourses as $course) {
            $course->grade = TakeUtils::isCourseApproved($course->id_course, $student, $course->id_assignment, true);
            $course->completed_percent = number_format(TakeUtils::getCoursePercentCompleted($course->id_course, $student, $course->id_assignment));
            
            $course->lTopics = \DB::table('uni_topics AS t')
                                    ->where('t.is_deleted', false)
                                    ->where('t.course_id', $course->id_course)
                                    ->get();

            foreach ($course->lTopics as $topic) {
                $topic->grade = TakeUtils::isTopicApproved($topic->id_topic, $student, $course->id_assignment, true);

                $topic->lSubTopics = \DB::table('uni_subtopics AS s')
                                        ->where('s.is_deleted', false)
                                        ->where('s.topic_id', $topic->id_topic)
                                        ->get();

                foreach ($topic->lSubTopics as $subtopic) {
                    $subtopic->grade = TakeUtils::isSubtopicApproved($subtopic->id_subtopic, $student, $course->id_assignment, true);
                }
            }

            $tot_cour = $tot_cour + 1;
            $module_percent = $module_percent + $course->completed_percent;
        }

        $tot_cour == 0 ? $tot_cour = 1 : '';

        $completed_percent = $module_percent/$tot_cour;

        return [$completed_percent, $lCourses];
    }

    public static function getCoursePercentCompleted($iCourse, $student, $idAssignment)
    {
        $subtopics = \DB::table('uni_topics AS top')
                        ->join('uni_subtopics AS sub', 'top.id_topic', '=', 'sub.topic_id')
                        ->where('top.course_id', $iCourse)
                        ->where('top.is_deleted', false)
                        ->where('sub.is_deleted', false)
                        ->pluck('sub.id_subtopic');

        $approved = 0;
        foreach ($subtopics as $idSub) {
            if (TakeUtils::isSubtopicApproved($idSub, $student, $idAssignment)) {
                $approved++;
            }
        }

        $total = count($subtopics);

        return $total == 0 ? 0 : ($approved * 100 / $total);
    }

    public static function getAreaPercentCompleted($iArea, $student, $idAssignment)
    {
        $subtopics = TakeUtils::getSubTopicsOfArea($iArea);

        $approved = 0;
        foreach ($subtopics as $idSub) {
            if (TakeUtils::isSubtopicApproved($idSub, $student, $idAssignment)) {
                $approved++;
            }
        }

        $total = count($subtopics);

        return $total == 0 ? 0 : ($approved * 100 / $total);
    }

    public static function getSubTopicsOfArea($iArea)
    {
        $subtopics = \DB::table('uni_topics AS top')
                        ->join('uni_subtopics AS sub', 'top.id_topic', '=', 'sub.topic_id')
                        ->join('uni_courses AS cou', 'top.course_id', '=', 'cou.id_course')
                        ->join('uni_modules AS mo', 'cou.module_id', '=', 'mo.id_module')
                        ->where('mo.knowledge_area_id', $iArea)
                        ->where('top.is_deleted', false)
                        ->where('sub.is_deleted', false)
                        ->where('cou.is_deleted', false)
                        ->where('mo.is_deleted', false)
                        ->pluck('sub.id_subtopic');

        return ($subtopics);
    }

    /**
     * Valida que el usuario haya cursado todos los requisitos previos a este
     *
     * @param int config('csys.elem_type.AREA'), config('csys.elem_type.MODULE'), config('csys.elem_type.COURSE')
     * @param [type] $idElement
     * @return void
     */
    public static function validatePrerequisites($elementType, $idElement)
    {
        $lPres = Prerequisite::where('element_type_id', $elementType)
                                ->where('is_deleted', false);

        switch ($elementType) {
            case config('csys.elem_type.AREA'):
                $lPres = $lPres->where('knowledge_area_n_id', $idElement);
                break;
            case config('csys.elem_type.MODULE'):
                $lPres = $lPres->where('module_n_id', $idElement);
                break;
            case config('csys.elem_type.COURSE'):
                $lPres = $lPres->where('course_n_id', $idElement);
                break;
            
            default:
                # code...
                break;
        }

        $oPre = $lPres->first();

        if ($oPre == null) {
            return "";
        }

        $lPres = \DB::table('uni_prerequisites_rows AS pr')
                        ->join('sys_element_types AS et', 'pr.element_type_id', '=', 'et.id_element_type')
                        ->leftJoin('uni_knowledge_areas AS a', 'pr.knowledge_area_n_id', '=', 'a.id_knowledge_area')
                        ->leftJoin('uni_modules AS m', 'pr.module_n_id', '=', 'm.id_module')
                        ->leftJoin('uni_courses AS c', 'pr.course_n_id', '=', 'c.id_course')
                        ->where('pr.is_deleted', false)
                        ->where('pr.prerequisite_id', $oPre->id_prerequisite)
                        ->get();

        foreach ($lPres as $pre) {
            switch ($pre->id_element_type) {
                case config('csys.elem_type.AREA'):
                    $approved = TakeUtils::isSomeAreaApproved($pre->id_knowledge_area, \Auth::id());
                    if (! $approved) {
                        return "Debes aprobar antes el cuadrante: ".$pre->knowledge_area.".";
                    }
                    break;
                case config('csys.elem_type.MODULE'):
                    $approved = TakeUtils::isSomeModuleApproved($pre->id_module, \Auth::id());
                    if (! $approved) {
                        return "Debes aprobar antes el módulo: ".$pre->module.".";
                    }
                    break;
                case config('csys.elem_type.COURSE'):
                    $approved = TakeUtils::isSomeCourseApproved($pre->id_course, \Auth::id());
                    if (! $approved) {
                        return "Debes aprobar antes el curso: ".$pre->course.".";
                    }
                    break;
                
                default:
                    # code...
                    break;
            }
        }

        return "";
    }

    public static function courseAttempts($idCourse, $idAssignment)
    {
        $subtopics = \DB::table('uni_topics AS top')
                    ->join('uni_subtopics AS sub', 'top.id_topic', '=', 'sub.topic_id')
                    ->join('uni_courses AS cou', 'top.course_id', '=', 'cou.id_course')
                    ->where('sub.is_deleted', false)
                    ->where('top.is_deleted', false)
                    ->where('cou.id_course', $idCourse)
                    ->select('sub.*')
                    ->get();

        $attemps = 0;
        foreach ($subtopics as $oSub) {
            $response = TakeUtils::subtopicAttempts($oSub->id_subtopic, $idAssignment);
            if (! $response[0]) {
                return [false, 0];
            }
            $attemps += $response[1];
        }

        return [true, count($subtopics) == $attemps];
    }

    public static function subtopicAttempts($idSubtopic, $idAssignment)
    {
        $attemps = \DB::table('uni_taken_controls AS tc')
                        ->where('subtopic_n_id', $idSubtopic)
                        ->where('assignment_id', $idAssignment)
                        ->where('is_evaluation', true)
                        ->where('is_deleted', false)
                        ->orderBy('id_taken_control', 'ASC');
        
        $attempsApproved = clone $attemps;
        $attempsApproved = $attempsApproved->whereColumn('grade', '>=', 'min_grade')
                                            ->where('status_id', 7)
                                            ->get();

        $attempsNoApproved = clone $attemps;

        $attempsNoApproved = $attempsNoApproved->where('status_id', 6)
                                                ->get();
        
        $response = [];
        $response[] = count($attempsApproved) > 0;
        $response[] = $response[0] ? (count($attempsNoApproved) + 1) : count($attempsNoApproved);

        return $response;
    }
}