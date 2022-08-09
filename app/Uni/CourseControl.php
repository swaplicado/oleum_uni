<?php

namespace App\Uni;

use Illuminate\Database\Eloquent\Model;

class CourseControl extends Model
{
    protected $table =  "uni_assignments_courses_control";
    protected $primaryKey = "id_course_control";

    protected $fillable = [
        'assignment_id',
        'dt_open',
        'dt_close',
        'course_n_id',
        'module_n_id',
        'student_id',
        'is_deleted',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
