<?php

namespace App\Uni;

use Illuminate\Database\Eloquent\Model;

class PointsControl extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'uni_points_control';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_points_control';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dt_date',
        'increment',
        'decrement',
        'comments',
        'is_deleted',
        'mov_class',
        'mov_type_id',
        'taken_control_n_id',
        'gift_stk_n_id',
        'student_id',
        'created_by_id',
        'updated_by_id'
    ];
}
