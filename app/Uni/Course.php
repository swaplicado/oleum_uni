<?php

namespace App\Uni;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'uni_courses';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_course';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'course',
        'course_key',
        'hash_id',
        'description',
        'objectives',
        'completion_days',
        'university_points',
        'is_deleted',
        'module_id',
        'elem_status_id',
        'sequence_id',
        'created_by_id',
        'updated_by_id'
    ];
}
