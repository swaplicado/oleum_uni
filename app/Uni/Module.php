<?php

namespace App\Uni;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'uni_modules';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_module';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'module',
        'hash_id',
        'description',
        'objectives',
        'is_deleted',
        'knowledge_area_id',
        'elem_status_id',
        'sequence_id',
        'created_by_id',
        'updated_by_id'
    ];
}
