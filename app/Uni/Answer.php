<?php

namespace App\Uni;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'uni_answers';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_answer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'answer',
        'is_deleted',
        'content_n_id',
        'question_id'
    ];

    public function question()
    {
        return $this->belongsTo('App\Uni\Question', 'id_question', 'question_id');
    }
}
