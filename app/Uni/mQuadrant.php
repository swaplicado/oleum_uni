<?php

namespace App\Uni;

use Jenssegers\Mongodb\Eloquent\Model;

class mQuadrant extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'm_taked_quadrants';
    protected $primaryKey = '_id';

    protected $fillable = [
        'assignment_id',
        'student_id',
        'quadrant_id',
        'element_body',
        'grade',
        'is_delete',
        'dt_open',
        'dt_end',
    ];
}
