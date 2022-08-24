<?php

namespace App\Uni;

use Jenssegers\Mongodb\Eloquent\Model;

class mModule extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'm_taked_modules';
    protected $primaryKey = '_id';

    protected $fillable = [
        'assignment_id',
        'student_id',
        'module_id',
        'quadrant_id',
        'element_body',
        'grade',
        'is_delete',
        'dt_open',
        'dt_end',
    ];
}
