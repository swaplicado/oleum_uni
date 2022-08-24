<?php

namespace App\Uni;

use Jenssegers\Mongodb\Eloquent\Model;

class mTakedExams extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'm_taked_exams';
    protected $primaryKey = '_id';

    protected $fillable = [
        'assignment_id',
        'student_id',
        'subtopic_id',
        'take_control_id',
        'grade',
        'num_taked',
        'element_body',
        'is_delete',
    ];
}
