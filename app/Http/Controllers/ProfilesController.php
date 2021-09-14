<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;

use App\User;
use App\Uni\PointsControl;

class ProfilesController extends Controller
{
    public function myProfile()
    {
        $idBoss = \Auth::user()->job->department->head_user_n_id;

        $boss = $idBoss == null ? null : User::find($idBoss);

        $sDate = Carbon::now()->toDateString();

        $lPoints = \DB::table('uni_points_control AS pts')
                            ->join('sys_points_mov_types AS pt', 'pts.mov_type_id', '=', 'pt.id_mov_type')
                            ->leftJoin('uni_taken_controls AS ctrls', 'pts.taken_control_n_id', '=', 'ctrls.id_taken_control')
                            ->leftJoin('uni_courses AS cou', 'ctrls.course_n_id', '=', 'cou.id_course')
                            ->leftJoin('uni_gifts_stock AS stk', 'pts.gift_stk_n_id', '=', 'stk.id_stock')
                            ->leftJoin('uni_gifts AS g', 'stk.gift_id', '=', 'g.id_gift')
                            ->select('pts.*', 'movement_type', 'course', 'gift')
                            ->where('pts.student_id', \Auth::id())
                            ->where('pts.is_deleted', false)
                            ->where('pts.dt_date', '<=', $sDate)
                            ->get();

        $i = 1;
        foreach ($lPoints as $pointRow) {
            $pointRow->index = $i;
            $i++;
        }

        $oPoints = PointsControl::where('student_id', \Auth::id())
                        ->selectRaw('
                            SUM(increment) AS increments, 
                            SUM(decrement) AS decrements, 
                            (SUM(increment) - SUM(decrement)) AS points
                        ')
                        ->where('is_deleted', false)
                        ->where('dt_date', '<=', $sDate)
                        ->groupBy('student_id')
                        ->first();

        return view('profile')->with('boss', $boss)
                                ->with('lPoints', $lPoints)
                                ->with('oPoints', $oPoints);
    }

    public function changePassword()
    {
        return view('changepass');
    }

    public function updatePassword(Request $request)
    {
        if (! (\Hash::check($request->get('current_password'), \Auth::user()->password))) {
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
        ],
        [
            'current_password.required' => 'La contraseña actual es obligatoria',
            'new_password.required' => 'La contraseña nueva es obligatoria'
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

        for ($i=2; $i <= 31; $i++) { 
            $images[] = (object) ['name' => 'avatar_'.$i, 'route' => 'img/profiles/avatar'.$i.'.png'];
        }

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
