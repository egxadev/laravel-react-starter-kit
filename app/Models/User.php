<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Concerns\Auditable;
use App\Concerns\Filterable;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Ramsey\Uuid\Uuid;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property string $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password', 'created_by', 'updated_by', 'deleted_by'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use Auditable, Filterable, HasFactory, HasRoles, Notifiable, PasskeyAuthenticatable, SoftDeletes, TwoFactorAuthenticatable;

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * Searchable and sortable columns for Filterable concern.
     *
     * @var array<string>
     */
    protected array $searchable = ['name', 'email', 'created_at'];

    public static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Uuid::uuid4();
            }
        });
    }

    /**
     * Create user and assign roles atomically.
     *
     * @param  array<string, mixed>  $attributes
     * @param  array<string>|string  $roles
     */
    public static function createWithRoles(array $attributes, array|string $roles): self
    {
        return DB::transaction(function () use ($attributes, $roles) {
            $user = static::create([
                'name' => $attributes['name'],
                'email' => $attributes['email'],
                'password' => $attributes['password'],
            ]);

            $user->assignRole($roles);

            return $user;
        });
    }

    /**
     * Update user attributes and sync roles atomically.
     *
     * @param  array<string, mixed>  $attributes
     * @param  array<string>|string  $roles
     */
    public function updateWithRoles(array $attributes, array|string $roles): self
    {
        return DB::transaction(function () use ($attributes, $roles) {
            $data = [
                'name' => $attributes['name'],
                'email' => $attributes['email'],
            ];

            if (! empty($attributes['password'])) {
                $data['password'] = $attributes['password'];
            }

            $this->update($data);
            $this->syncRoles($roles);

            return $this;
        });
    }

    public static function findOrCreateFromSocialite(object $socialUser, string $provider): self
    {
        $socialAccount = SocialAccount::where('provider_id', $socialUser->getId())
            ->where('provider_name', $provider)
            ->first();

        if ($socialAccount) {
            return $socialAccount->user;
        }

        return DB::transaction(function () use ($socialUser, $provider) {
            $user = static::firstOrCreate(
                ['email' => $socialUser->getEmail()],
                ['name' => $socialUser->getName()],
            );

            $user->socialAccounts()->create([
                'provider_id' => $socialUser->getId(),
                'provider_name' => $provider,
            ]);

            return $user;
        });
    }

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
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * Returns an array of permissions for the user with the permission name as key and value as true.
     *
     * @return Collection<string, bool>
     */
    public function getPermissionArray(): Collection
    {
        return $this->getAllPermissions()->mapWithKeys(function ($pr) {
            return [$pr->name => true];
        });
    }

    /**
     * Returns a collection of {@see SocialAccount} that the user has.
     *
     * @return \Illuminate\Database\Eloquent\Collection<SocialAccount>
     */
    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }
}
