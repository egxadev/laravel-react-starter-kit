<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Ramsey\Uuid\Uuid;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    public static function booted()
    {
        static::creating(function ($model) {
            $model->id = Uuid::uuid4();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Returns an array of permissions for the user with the permission name as key and value as true.
     *
     * @return \Illuminate\Support\Collection<string, bool>
     */
    public function getPermissionArray()
    {
        return $this->getAllPermissions()->mapWithKeys(function ($pr) {
            return [$pr['name'] => true];
        });
    }
    /**
     * Returns a collection of {@see \App\Models\SocialAccount} that the user has.
     *
     * @return \Illuminate\Database\Eloquent\Collection<\App\Models\SocialAccount>
     */
    public function socialAccounts()
    {
    return $this->hasMany(SocialAccount::class);
    }
}
