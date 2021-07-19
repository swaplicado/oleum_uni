<?php

namespace App\Uni;

use Illuminate\Database\Eloquent\Model;

class TakingSubTopicQuestion extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'uni_taken_questions';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_question_taken';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_correct',
        'is_deleted',
        'take_control_id',
        'question_id',
        'answer_n_id'
    ];

}
