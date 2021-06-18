<?php

namespace App\Adm;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'adm_organizations';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_organization';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'organization',
        'acronym',
        'is_deleted',
        'external_id',
        'head_user_id'
    ];
}
