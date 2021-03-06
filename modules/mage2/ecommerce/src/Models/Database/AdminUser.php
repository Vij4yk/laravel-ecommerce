<?php

namespace Mage2\Ecommerce\Models\Database;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Mage2\Ecommerce\Notifications\ResetPassword;
use Laravel\Passport\HasApiTokens;

class AdminUser extends Authenticatable
{
    use Notifiable, HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'role_id', 'is_super_admin'
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
     * Send the password reset notification.
     *
     * @param  string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }



    public function getFullNameAttribute()
    {
        return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }


    public function hasPermission($routeName)
    {
        if ($this->is_super_admin) {
            return true;
        }

        $role = $this->role;

        if ($role->permissions->pluck('name')->contains($routeName) == false) {
            return false;
        }
        return true;
    }
}
