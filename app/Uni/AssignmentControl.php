<?php

namespace App\Uni;

use Illuminate\Database\Eloquent\Model;

class AssignmentControl extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'uni_assignments_control';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_control';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_deleted',
        'dt_assignment',
        'dt_end',
        'knowledge_area_id',
        'organization_n_id',
        'company_n_id',
        'branch_n_id',
        'department_n_id',
        'job_n_id',
        'student_n_id',
        'created_by_id',
        'updated_by_id'
    ];
}
