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
                            ->where('tc.student_id', $student)
                            ->where(function ($query) {
                                $query->where('tc.status_id', config('csys.take_status.CUR'))
                                    ->orWhere('tc.status_id', config('csys.take_status.EVA'));
                            })
                            ->where('tc.element_type_id', config('csys.elem_type.COURSE'))
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
                        return "Debes aprobar antes la competencia: ".$pre->knowledge_area.".";
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
}