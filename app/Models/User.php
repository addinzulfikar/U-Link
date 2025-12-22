<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    const ROLE_USER = 'user';

    const ROLE_ADMIN_TOKO = 'admin_toko';

    const ROLE_SUPER_ADMIN = 'super_admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'umkm_id',
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
     * Check if user is a regular user
     */
    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    /**
     * Check if user is an admin toko (UMKM)
     */
    public function isAdminToko(): bool
    {
        return $this->role === self::ROLE_ADMIN_TOKO;
    }

    /**
     * Check if user is a super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    /**
     * Get the UMKM owned by this user (for admin_toko)
     */
    public function umkm()
    {
        return $this->hasOne(Umkm::class, 'owner_user_id');
    }

    /**
     * Get the UMKM this user is associated with (for regular users)
     */
    public function assignedUmkm()
    {
        return $this->belongsTo(Umkm::class, 'umkm_id');
    }

    /**
     * Get the reviews written by this user
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the favorites of this user
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Check if this user can chat with another user
     */
    public function canChatWith(User $targetUser): bool
    {
        // Super admin can chat with everyone
        if ($this->isSuperAdmin()) {
            return true;
        }

        // If target is super admin, all roles can chat with them
        if ($targetUser->isSuperAdmin()) {
            return true;
        }

        // Regular user can only chat with their assigned UMKM's admin
        if ($this->isUser()) {
            return $targetUser->isAdminToko() 
                && $this->umkm_id 
                && $targetUser->umkm 
                && $this->umkm_id === $targetUser->umkm->id;
        }

        // Admin toko can chat with users under their UMKM
        if ($this->isAdminToko()) {
            if ($targetUser->isUser()) {
                return $this->umkm 
                    && $targetUser->umkm_id 
                    && $this->umkm->id === $targetUser->umkm_id;
            }
            // Admin toko cannot chat with other admin toko
            return false;
        }

        return false;
    }

    /**
     * Get list of users this user can chat with
     */
    public function getAllowedChatUsers()
    {
        // Super admin can chat with everyone
        if ($this->isSuperAdmin()) {
            return User::where('id', '!=', $this->id)->get();
        }

        // Regular user can only see their assigned UMKM's admin
        if ($this->isUser()) {
            if (!$this->umkm_id) {
                return collect();
            }
            
            return User::where('role', self::ROLE_ADMIN_TOKO)
                ->whereHas('umkm', function ($query) {
                    $query->where('id', $this->umkm_id);
                })
                ->get()
                ->merge(
                    User::where('role', self::ROLE_SUPER_ADMIN)->get()
                );
        }

        // Admin toko can see users under their UMKM and super admins
        if ($this->isAdminToko() && $this->umkm) {
            return User::where('umkm_id', $this->umkm->id)
                ->where('role', self::ROLE_USER)
                ->get()
                ->merge(
                    User::where('role', self::ROLE_SUPER_ADMIN)->get()
                );
        }

        return collect();
    }
}
