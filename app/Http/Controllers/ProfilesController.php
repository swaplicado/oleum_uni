<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfilesController extends Controller
{
    public function myProfile()
    {
        return view('profile');
    }

    public function changePassword()
    {
        return view('changepass');
    }

    public function updatePassword(Request $request)
    {
        if (!(\Hash::check($request->get('current_password'), \Auth::user()->password))) {
            // The passwords matches
            return redirect()->back()->with("error","La contraseña actual no coincide con la contraseña que introdujiste. Por favor intenta de nuevo.");
        }

        if ($request->get('confirmed_new_password') != $request->get('new_password')) {
            // The passwords matches
            return redirect()->back()->with("error","La confirmación de contraseña no coincide con la contraseña nueva. Por favor intenta de nuevo.");
        }

        if(strcmp($request->get('current_password'), $request->get('new_password')) == 0){
            //Current password and new password are same
            return redirect()->back()->with("error","La contraseña nueva no puede ser la misma que la anterior. Elige una contraseña diferente.");
        }

        $validatedData = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:4',
            'confirmed_new_password' => 'required|string|min:4',
        ]);

        //Change Password
        $user = \Auth::user();
        $user->password = bcrypt($request->get('new_password'));
        $user->save();

        return redirect()->route('home')->with("success","La contraseña ha sido actualizada con éxito!");
    }

    public function changeAvatar()
    {
        // dd(config('cuni'));
        $images = config('cuni.images');

        return view('avatars')->with('images', $images);
    }

    public function updateAvatar(Request $request)
    {
        $user = \Auth::user();
        $user->profile_picture = $request->image_path;

        $user->save();

        return redirect()->route('profile')->with("success", "Tu avatar ha sido actualizado.");
    }
}
