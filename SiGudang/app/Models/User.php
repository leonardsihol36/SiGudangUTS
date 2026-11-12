<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 't036_users';

    protected $fillable = [
        'role_id',
        'username',
        'name',
        'email',
        'password',
        'profile_photo',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function mutasi(): HasMany
    {
        return $this->hasMany(MutasiStok::class, 'user_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    public function isManajer(): bool
    {
        return $this->role && $this->role->name === 'Manajer';
    }

    public function isStafGudang(): bool
    {
        return $this->role && $this->role->name === 'Staf Gudang';
    }

    public function isAuditor(): bool
    {
        return $this->role && $this->role->name === 'Auditor';
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return $this->role && in_array($this->role->name, $roles);
        }

        return $this->role && $this->role->name === $roles;
    }

    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo && file_exists(public_path($this->profile_photo))) {
            return asset($this->profile_photo);
        }

        // Professional SVG fallback data URL
        $initials = strtoupper(substr($this->name, 0, 2));
        return "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&background=FB8C00&color=ffffff&bold=true";
    }
}
