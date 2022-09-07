<?php

namespace App\Adm;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Areas extends Model
{
    protected $table = 'adm_areas';
    protected $primaryKey = 'id_area';

    protected $fillable = [
        'area',
        'father_area_id',
        'is_deleted',
        'created_by_id',
        'updated_by_id',
    ];

    public function parent()
    {
        return $this->belongsTo('App\Adm\Areas', 'father_area_id')->where('is_deleted', 0);
    }

    public function children()
    {
        return $this->hasMany('App\Adm\Areas', 'father_area_id')->where('is_deleted', 0);
    }

    public function getChildrens(){
        $child = $this->children()->get();
        foreach($child as $c){
            $c->child = $c->getChildrens();
        }
        return $child;
    }

    public function getArrayChilds(){
        $arrayChilds = [];
        foreach($this->child as $c){
            array_push($arrayChilds, [$c->id_area]);
            array_push($arrayChilds, $c->getArrayChilds());
        }
        $arrayChilds = Arr::collapse($arrayChilds);
        return $arrayChilds;
    }
}
