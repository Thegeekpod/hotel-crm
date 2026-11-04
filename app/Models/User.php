<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @property int $id
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
#[Fillable(['name', 'email', 'phone', 'password', 'role', 'assigned_sections', 'status'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail, PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

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
            'assigned_sections' => 'array',
        ];
    }

    /**
     * Booted model events
     */
    protected static function booted(): void
    {
        static::creating(function ($user) {
            if (empty($user->email)) {
                $cleanPhone = preg_replace('/[^0-9]/', '', $user->phone ?? Str::random(8));
                $user->email = "user_{$cleanPhone}@hotelsagarsonnet.local";
            }
        });
    }

    /**
     * Check if the user is a super admin
     */
    public function isAdmin(): bool
    {
        return strtolower($this->role ?? '') === 'admin' 
            || strtolower($this->email ?? '') === 'admin@hotelsagarsonnet.com';
    }

    /**
     * Check if user has permission to access a section
     */
    public function hasSection(string $section): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $sec = strtolower(trim($section));
        $assigned = array_map(fn($s) => strtolower(trim($s)), $this->assigned_sections ?? []);
        
        return in_array($sec, $assigned);
    }

    /**
     * Get all allowed sections
     */
    public function getAllowedSections(): array
    {
        if ($this->isAdmin()) {
            return ['frontoffice', 'pos', 'stores', 'banquet', 'accounts', 'admin'];
        }

        return array_map(fn($s) => strtolower(trim($s)), $this->assigned_sections ?? []);
    }

    /**
     * Get default redirect route based on permissions
     */
    public function getDefaultRoute(): string
    {
        if ($this->isAdmin()) {
            return 'admin.dashboard';
        }

        $sections = $this->getAllowedSections();
        if (in_array('frontoffice', $sections)) {
            return 'frontoffice.dashboard';
        }
        if (in_array('pos', $sections)) {
            return 'pos.index';
        }
        if (in_array('stores', $sections)) {
            return 'stores.index';
        }
        if (in_array('banquet', $sections)) {
            return 'banquet.index';
        }
        if (in_array('accounts', $sections)) {
            return 'accounts.index';
        }

        return 'admin.login';
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }
}
