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

        $cc = [];
        $ucca = [];
        $ucca['email'] = env('QUESTIONS_MAIL_DEV');
        $ucca['name'] = env('QUESTIONS_MAIL_DEV_NAME');

        $cc[] = (object) $ucca;

        Mail::to($rec)
            ->cc($cc)
            ->send(new Questions(\Auth::user()->full_name, $request->question));

        return redirect()->back()->with('success', 'Pregunta enviada correctamente');
    }
}
