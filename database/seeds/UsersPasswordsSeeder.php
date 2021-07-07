<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersPasswordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lUsers = User::where('password', "")->select('id', 'username')->get();

        foreach ($lUsers as $user) {
            \DB::table('users')
                    ->where('id', $user->id)
                    ->update(['password' => bcrypt($user->username)]);
        }
    }
}
