<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\User;

class UsersController extends Controller
{
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
}
