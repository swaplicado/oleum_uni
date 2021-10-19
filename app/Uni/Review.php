<?php

namespace App\Uni;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'uni_reviews';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_review';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'stars',
        'review_n_comments',
        'is_deleted',
        'review_type_id',
        'reference_id',
        'student_by_id'
    ];
}
