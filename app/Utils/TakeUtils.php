<?php namespace App\Utils;

use Carbon\Carbon;

use App\Uni\TakingControl;
use App\Uni\KnowledgeArea;
use App\Uni\Module;
use App\Uni\Assignment;
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
}