<?php

namespace App\Http\Controllers;

use App\Adm\Job;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    private $lJobs;

    public function index()
    {
        $lUsers = \DB::table('users AS u')
                    ->leftJoin('adm_areas as a', 'a.id_area', '=', 'u.area_id')
                    ->select('u.*', 'a.area')
                    ->where('u.is_deleted', false)
                    ->get();

        $areas = \DB::table('adm_areas')
                    ->where('is_deleted', 0)
                    ->select('id_area as id', 'area as text')
                    ->get();

        return view('users')->with('lUsers', $lUsers)
                            ->with('mailroute', 'users.update.mail')
                            ->with('userroute', 'users.update.username')
                            ->with('passroute', 'users.reset.pass')
                            ->with('areas', $areas);
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
        $areaId = \DB::table('adm_departments as d')
                        ->join('adm_jobs as j', 'j.department_id', '=', 'd.id_department')
                        ->where('j.id_job', $this->lJobs[$jUser->siie_job_id])
                        ->where('j.is_deleted', 0)
                        ->value('d.area_id');

        $oUsr = User::find($id);

        if ($oUsr->area_id > 0) {
            $areaId = $oUsr->area_id;
        }
        else if (is_null($areaId)) {
            $config = \App\Utils\Configuration::getConfigurations();
            $areaId =  $config->defFunctArea;
        }

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
                                'job_id' => $this->lJobs[$jUser->siie_job_id],
                                'area_id' => $areaId,
                            ]
                        );
    }

    private function insertUser($jUser)
    {
        if (! $jUser->is_active || $jUser->is_deleted) {
            return;
        }

        $name = str_replace([' LA ', ' DE ', ' LOS ', ' DEL ', ' LAS ', ' EL ', ], ' ', $jUser->firstname);
        $lastname1 = str_replace([' LA ', ' DE ', ' LOS ', ' DEL ', ' LAS ', ' EL ', ], ' ', $jUser->lastname1);
        $lastname2 = str_replace([' LA ', ' DE ', ' LOS ', ' DEL ', ' LAS ', ' EL ', ], ' ', $jUser->lastname2);
        // $usernameTmp = strtolower($jUser->num_employee.'.'.$jUser->lastname1.'.'.$jUser->lastname2);
        
        $names = explode(' ', $name);
        $lastname1s = explode(' ', $lastname1);
        $lastname2s = explode(' ', $lastname2);

        $usr = [];
        if (count($names) > 0 && count($lastname1s) > 0) {
            $usernameTmp = strtolower($names[0].'.'.$lastname1s[0]);
            $username = $this->getUserName($usernameTmp);
            $usr = User::where('username', $username)->first();
        }
        
        if ($usr != null) {
            if (count($names) > 1) {
                $usernameTmp = strtolower($names[1].'.'.$lastname1s[0]);
                $username = $this->getUserName($usernameTmp);
                $usr = User::where('username', $username)->first();
            }

            if ($usr != null) {
                if (count($lastname2s) > 0) {
                    $usernameTmp = strtolower($names[0].'.'.$lastname2s[0]);
                    $username = $this->getUserName($usernameTmp);
                    $usr = User::where('username', $username)->first();
                }
            }

            if ($usr != null) {
                $usernameTmp = strtolower($jUser->lastname1.'.'.$jUser->num_employee);
                $username = $this->getUserName($usernameTmp);
                $usr = User::where('username', $username)->first();

                if ($usr != null) {
                    return;
                }
            }
        }

        $areaId = \DB::table('adm_departments as d')
                        ->join('adm_jobs as j', 'j.department_id', '=', 'd.id_department')
                        ->where('j.id_job', $this->lJobs[$jUser->siie_job_id])
                        ->where('j.is_deleted', 0)
                        ->value('d.area_id');

        if (is_null($areaId)) {
            $config = \App\Utils\Configuration::getConfigurations();
            $areaId =  $config->defFunctArea;
        }

        $oUser = new User();

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
        $oUser->area_id = $areaId;
        $oUser->user_type_id = 1;
        $oUser->created_by_id = 1;
        $oUser->updated_by_id = 1;

        $oUser->save();
    }

    private function getUserName($usernameTmp)
    {
        $username = str_replace(['ñ', 'Ñ'], 'n', $usernameTmp);
        $username = str_replace('-', '', $username);
        $username = str_replace(' ', '', $username);

        return $username;
    }

    public function updateUserArea(Request $request){
        try {
            \DB::beginTransaction();
                $user = User::findOrFail($request->id_user);
                $user->area_id = $request->area_id;
                $user->update();
            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollback();
            return json_encode(['success' => false, 'message' => 'Error al actualizar el registro']);
        }
        return json_encode(['success' => true, 'message' => 'Registro actualizadó con exitó']);
    }
}
