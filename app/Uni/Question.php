<?php

namespace App\Uni;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'uni_questions';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_question';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'question',
        'number_answers',
        'answer_feedback',
        'is_deleted',
        'answer_id',
        'subtopic_id',
        'created_by_id',
        'updated_by_id'
    ];

    public function answers()
    {
        return $this->hasMany('App\Uni\Answer', 'question_id', 'id_question');
    }
}
