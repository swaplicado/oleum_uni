<?php

namespace App\Uni;

use Illuminate\Database\Eloquent\Model;

class TakingContent extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'uni_taken_contents';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_content_taken';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dtt_take',
        'dtt_end',
        'is_deleted',
        'course_taken_id',
        'subtopic_id',
        'student_id',
        'content_id'
    ];
}
