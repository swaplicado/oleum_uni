<?php

namespace App\Uni;

use Illuminate\Database\Eloquent\Model;

class PrerequisiteRow extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'uni_prerequisites_rows';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_deleted',
        'prerequisite_id',
        'element_type_id',
        'knowledge_area_n_id',
        'module_n_id',
        'course_n_id',
        'topic_n_id',
        'subtopic_n_id'
    ];
}
