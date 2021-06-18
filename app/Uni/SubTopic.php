<?php

namespace App\Uni;

use Illuminate\Database\Eloquent\Model;

class SubTopic extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'uni_subtopics';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_subtopic';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subtopic',
        'hash_id',
        'number_questions',
        'is_deleted',
        'topic_id',
        'created_by_id',
        'updated_by_id'
    ];
}
