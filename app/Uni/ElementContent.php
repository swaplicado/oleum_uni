<?php

namespace App\Uni;

use Illuminate\Database\Eloquent\Model;

class ElementContent extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'uni_contents_vs_elements';

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
        'order',
        'content_id',
        'element_type_id',
        'knowledge_artea_n_id',
        'module_n_id',
        'course_n_id',
        'topic_n_id',
        'subtopic_n_id',
        'created_by_id',
        'updated_by_id'
    ];
}
