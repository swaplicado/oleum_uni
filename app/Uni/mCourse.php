<?php

namespace App\Uni;

use Jenssegers\Mongodb\Eloquent\Model;

class mCourse extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'm_taked_courses';
    protected $primaryKey = '_id';

    protected $fillable = [
        'assignment_id',
        'student_id',
        'course_id',
        'module_id',
        'element_body',
        'grade',
        'dt_open',
        'dt_create',
        'dt_end',
    ];
}
