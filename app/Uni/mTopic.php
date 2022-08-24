<?php

namespace App\Uni;

use Jenssegers\Mongodb\Eloquent\Model;

class mTopic extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'm_taked_topics';
    protected $primaryKey = '_id';

    protected $fillable = [
        'assignment_id',
        'student_id',
        'topic_id',
        'course_id',
        'element_body',
        'grade',
        'is_delete'
    ];
}
