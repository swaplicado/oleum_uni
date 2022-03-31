<?php

namespace App\Http\Controllers\Uni;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Mail\Questions;
use Illuminate\Support\Facades\Mail;

class NotificationsController extends Controller
{
    public function sendQuestion(Request $request)
    {
        $rec = [];
        $ua = [];
        $ua['email'] = env('QUESTIONS_MAIL');
        $ua['name'] = env('QUESTIONS_NAME');

        $rec[] = (object) $ua;

        Mail::to($rec)->send(new Questions($request->name_student, $request->question));

        return redirect()->back()->with('success', 'Pregunta enviada correctamente');
    }
}
