<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\Auditable;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;
    use Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    // Define role constants
    const ROLE_ADMIN = 'admin';
    const ROLE_PROJECT_MANAGER = 'project_manager';
    const ROLE_VIEWER = 'viewer';

    // Helper methods for role checking
    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    public function isProjectManager(): bool
    {
        return $this->hasRole(self::ROLE_PROJECT_MANAGER);
    }

    public function isViewer(): bool
    {
        return $this->hasRole(self::ROLE_VIEWER);
    }

    public function canManageProjects(): bool
    {
        return $this->hasAnyRole([self::ROLE_ADMIN, self::ROLE_PROJECT_MANAGER]);
    }

    public function canViewOnly(): bool
    {
        return $this->hasRole(self::ROLE_VIEWER) && !$this->hasAnyRole([self::ROLE_ADMIN, self::ROLE_PROJECT_MANAGER]);
    }
}
