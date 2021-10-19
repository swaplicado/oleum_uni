<?php

namespace App\Uni;

use Illuminate\Database\Eloquent\Model;

class ReviewType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'uni_review_types';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_review_type';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code_type',
        'review_type',
        'is_deleted'
    ];
}
