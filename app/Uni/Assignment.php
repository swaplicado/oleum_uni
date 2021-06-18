<?php

namespace App\Uni;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'uni_assignments';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_assignment';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_deleted',
        'dt_assignment',
        'dt_end',
        'is_over',
        'knowledge_area_id',
        'student_id',
        'control_id',
        'created_by_id',
        'updated_by_id'
    ];
}
