<?php

namespace App\Uni;

use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'uni_versions';

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
        'version',
        'hash_id',
        'is_deleted',
        'element_type_id',
        'element_id',
        'created_by_id',
        'updated_by_id'
    ];
}
