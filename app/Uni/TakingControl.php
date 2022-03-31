<?php

namespace App\Uni;

use Illuminate\Database\Eloquent\Model;

class TakingControl extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'uni_taken_controls';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_taken_control';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'grouper',
        'dtt_take',
        'dtt_end',
        'min_grade',
        'grade',
        'university_points',
        'num_questions',
        'is_evaluation',
        'is_deleted',
        'element_type_id',
        'knowledge_area_n_id',
        'module_n_id',
        'course_n_id',
        'topic_n_id',
        'subtopic_n_id',
        'student_id',
        'assignment_id',
        'status_id'
    ];
}
