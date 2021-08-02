<?php

namespace App\Uni;

use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'uni_gifts';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_gift';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'gift',
        'description',
        'images',
        'points_value',
        'created_by_id',
        'updated_by_id'
    ];
}
