<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\User;

class KnowledgeAreaAssignment extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($idAssignment)
    {
        $this->idAssignment = $idAssignment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $oAssignment = \DB::table('uni_assignments AS a')
                        ->join('uni_knowledge_areas AS ka', 'a.knowledge_area_id', '=', 'ka.id_knowledge_area')
                        ->join('users AS u', 'a.student_id', '=', 'u.id')
                        ->where('id_assignment', $this->idAssignment)
                        ->first();
        
        if ($oAssignment == null) {
            return;
        }

        $oStudent = User::find($oAssignment->student_id);

        return $this->from(env('MAIL_FROM_ADDRESS'))
                        ->subject('Competencia nueva por cursar')
                        ->view('mails.adm.assignment')
                        ->with('oAssignment', $oAssignment)
                        ->with('oStudent', $oStudent);
    }
}
