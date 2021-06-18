<?php

namespace App\Uni;

use Illuminate\Database\Eloquent\Model;

class EduContent extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'uni_edu_contents';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_content';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'file_name',
        'file_sys_name',
        'file_path',
        'file_type',
        'is_deleted',
        'created_by_id',
        'updated_by_id'
    ];
}
