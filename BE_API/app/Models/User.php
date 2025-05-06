<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\University;
use App\Models\UserFile;

class User extends Authenticatable
{
    use Notifiable, UsesUuid, HasApiTokens;

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'email',
        'password',
        'username',
        'image_url',
        'phone',
        'university_id',
        'role',
        'approval'
    ];

    protected $hidden = [
        'password',
    ];

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function files()
    {
        return $this->hasMany(UserFile::class);
    }

    public function photoFile()
    {
        return $this->hasOne(UserFile::class)->whereHas('fileCategory', function ($query) {
            $query->where('name', 'Photo');
        });
    }
}
