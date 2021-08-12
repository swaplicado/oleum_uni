<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\User;
use App\Adm\Job;

class UsersController extends Controller
{
    private $lJobs;    

    public function index()
    {
        $lUsers = \DB::table('users AS u')->where('is_deleted', false)->get();

        return view('users')->with('lUsers', $lUsers)
                            ->with('mailroute', 'users.update.mail')
                            ->with('userroute', 'users.update.username')
                            ->with('passroute', 'users.reset.pass');
    }

    public function resetPassword(Request $request)
    {
        $id = $request->id_user;
        $pass = $request->new_pss;

        \DB::table('users')
            ->where('id', $id)
            ->update(['password' => Hash::make($pass)]);

        return "OK";
    }

    public function updateEmail(Request $request)
    {
        $id = $request->id_user;
        $mail = $request->mail;

        \DB::table('users')
            ->where('id', $id)
            ->update(['email' => $mail]);

        return "OK";
    }

    public function updateUsername(Request $request)
    {
        $id = $request->id_user;
        $username = $request->username;

        $user = \DB::table('users')
                    ->where('username', $username)
                    ->where('id', '<>', $id)
                    ->get();

        if (count($user) > 0) {
            return "ERROR";
        }

        \DB::table('users')
            ->where('id', $id)
            ->update(['username' => $username]);

        return "OK";
    }

    public function saveUsersFromJSON($lUsers)
    {
        $lUnivUsers = User::pluck('id', 'external_id');

        $this->lJobs = Job::pluck('id_job', 'external_id');

        foreach ($lUsers as $jUser) {
            try {
                if (isset($lUnivUsers[$jUser->id_employee])) {
                    $id = $lUnivUsers[$jUser->id_employee];
                    $this->updUser($jUser, $id);
                }
                else {
                    $this->insertUser($jUser);
                }
            }
            catch (\Throwable $th) { }
        }
    }

    private function updUser($jUser, $id)
    {
        User::where('id', $id)
                    ->update(
                            [
                                'num_employee' => $jUser->num_employee,
                                'first_name' => $jUser->lastname1,
                                'last_name' => $jUser->lastname2,
                                'names' => $jUser->firstname,
                                'full_name' => $jUser->lastname1.' '.$jUser->lastname2.', '.$jUser->firstname,
                                'is_active' => $jUser->is_active,
                                'is_deleted' => $jUser->is_deleted,
                                'job_id' => $this->lJobs[$jUser->siie_job_id]
                            ]
                        );
    }

    private function insertUser($jUser)
    {
        if (! $jUser->is_active || $jUser->is_deleted) {
            return;
        }

        $oUser = new User();

        $usernameTmp = strtolower($jUser->num_employee.'.'.$jUser->lastname1.'.'.$jUser->lastname2);
        $username = str_replace(['ñ', 'Ñ'], 'n', $usernameTmp);
        $username = str_replace('-', '', $username);
        $oUser->username = $username;
        $oUser->password = bcrypt($username);
        $oUser->email = $jUser->email;
        $oUser->num_employee = $jUser->num_employee;
        $oUser->first_name = $jUser->lastname1;
        $oUser->last_name = $jUser->lastname2;
        $oUser->names = $jUser->firstname;
        $oUser->full_name = $jUser->lastname1.' '.$jUser->lastname2.', '.$jUser->firstname;
        $oUser->profile_picture = "img/profiles/profile.png";
        $oUser->is_active = $jUser->is_active;
        $oUser->is_deleted = $jUser->is_deleted;
        $oUser->external_id = $jUser->id_employee;
        $oUser->job_id = $this->lJobs[$jUser->siie_job_id];
        $oUser->branch_id = 1;
        $oUser->user_type_id = 1;
        $oUser->created_by_id = 1;
        $oUser->updated_by_id = 1;

        $oUser->save();
    }
}
