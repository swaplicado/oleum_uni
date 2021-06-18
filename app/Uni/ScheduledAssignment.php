<?php

namespace App\Uni;

use Illuminate\Database\Eloquent\Model;

class ScheduledAssignment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'uni_scheduled_assignments';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_schedule';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dt_start',
        'dt_end',
        'is_always',
        'is_deleted',
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
