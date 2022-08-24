<?php

namespace App\Uni;

use Jenssegers\Mongodb\Eloquent\Model;

class mSubtopic extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'm_taked_subtopics';
    protected $primaryKey = '_id';

    protected $fillable = [
        'assignment_id',
        'student_id',
        'subtopic_id',
        'topic_id',
        'element_body',
        'grade',
        'is_delete'
    ];
}
