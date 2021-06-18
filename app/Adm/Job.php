<?php

namespace App\Adm;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'adm_jobs';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_job';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'job',
        'acronym',
        'num_positions',
        'hierarchical_level',
        'is_deleted',
        'external_id',
        'department_id'
    ];
}
