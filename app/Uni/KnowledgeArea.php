<?php

namespace App\Uni;

use Illuminate\Database\Eloquent\Model;

class KnowledgeArea extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'uni_knowledge_areas';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_knowledge_area';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'knowledge_area',
        'hash_id',
        'description',
        'objectives',
        'has_document',
        'is_deleted',
        'elem_status_id',
        'sequence_id',
        'created_by_id',
        'updated_by_id'
    ];
}
