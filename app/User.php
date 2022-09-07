<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\CanResetPassword;
// use App\Notifications\VerifyEmail;
use App\Notifications\PasswordReset;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    use HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'password',
        'email',
        'num_employee',
        'first_name',
        'last_name',
        'names',
        'full_name',
        'profile_picture',
        'is_active',
        'is_deleted',
        'external_id',
        'job_id',
        'branch_id',
        'area_id',
        'user_type_id',
        'created_by_id',
        'updated_by_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * Obtiene el objeto puesto (Job) asociado al empleado
     *
     * @return Adm/Job
     */
    public function job()
    {
        return $this->belongsTo('App\Adm\Job', 'job_id', 'id_job');
    }

    /**
     * Obtiene el objeto Sucursal (Branch) asociado al empleado
     *
     * @return Adm/Branch
     */
    public function branch()
    {
        return $this->belongsTo('App\Adm\Branch', 'branch_id', 'id_branch');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordReset($token));
    }
}
