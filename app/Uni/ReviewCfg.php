<?php

namespace App\Uni;

use Illuminate\Database\Eloquent\Model;

class ReviewCfg extends Model
{
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'uni_review_cfgs';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_configuration';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_deleted',
        'review_type_id',
        'reference_id',
        'created_by_id',
        'updated_by_id'
    ];
}
