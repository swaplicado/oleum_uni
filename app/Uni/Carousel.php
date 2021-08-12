<?php

namespace App\Uni;

use Illuminate\Database\Eloquent\Model;

class Carousel extends Model
{
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'uni_carousel';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_slide';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'text',
        'text_color',
        'url',
        'image',
        'is_active',
        'is_deleted',
        'created_by_id',
        'updated_by_id'
    ];
}
