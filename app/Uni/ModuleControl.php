<?php

namespace App\Uni;

use Illuminate\Database\Eloquent\Model;

class ModuleControl extends Model
{
    protected $table = 'uni_assignments_module_control';
    protected $primaryKey = 'id_module_control';

    protected $fillable = [
        'assignment_id',
        'dt_open',
        'dt_close',
        'module_n_id',
        'student_id',
        'is_deleted',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
