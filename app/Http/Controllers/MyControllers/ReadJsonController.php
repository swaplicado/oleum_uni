<?php

namespace App\Http\Controllers\MyControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReadJsonController extends Controller
{
    public function index(){
        return view('Myviews/UploadJson');
    }

    public function readUploadFile(Request $request) {
        $file = $request->file('JsonFile');
        $string = file_get_contents($file->getRealPath());
        $json_a = json_decode($string);
        $emp= $json_a->Employee;
        $dep=$json_a->Dept;
        $pos=$json_a->Position;

        dd($emp, $dep, $pos);
    }
}
