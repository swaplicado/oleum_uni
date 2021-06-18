<?php

namespace App\Uni;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'uni_topics';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_topic';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'topic',
        'course_key',
        'hash_id',
        'is_deleted',
        'course_id',
        'sequence_id',
        'created_by_id',
        'updated_by_id'
    ];
}
