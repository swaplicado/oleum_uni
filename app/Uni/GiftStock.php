<?php

namespace App\Uni;

use Illuminate\Database\Eloquent\Model;

class GiftStock extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'uni_gifts_stock';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_stock';

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
        'gift_id',
        'student_n_id',
        'created_by_id',
        'updated_by_id'
    ];
}
