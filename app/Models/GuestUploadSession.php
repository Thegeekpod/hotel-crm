<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GuestUploadSession extends Model
{
    use HasFactory;

    protected $table = 'guest_upload_sessions';

    protected $fillable = [
        'token',
        'reserve_id',
        'room_number',
        'guest_name',
        'mobile',
        'id_card_type',
        'id_card_number',
        'documents',
        'status',
        'expires_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'documents' => 'array',
    ];

    /**
     * Determine if this scan upload link has expired.
     */
    public function isExpired(): bool
    {
        if ($this->status === 'expired') {
            return true;
        }
        if (!$this->expires_at) {
            return false;
        }
        return $this->expires_at->getTimestamp() <= now()->getTimestamp();
    }

    /**
     * Get remaining validity in seconds (0 if expired).
     */
    public function getRemainingSeconds(): int
    {
        if ($this->isExpired() || !$this->expires_at) {
            return 0;
        }
        return max(0, $this->expires_at->getTimestamp() - now()->getTimestamp());
    }

    /**
     * Create a fresh 5-minute upload session for a guest.
     */
    public static function createSession(?string $guestName = null, ?string $mobile = null, ?string $room = null, ?string $reserveId = null): self
    {
        return static::create([
            'token' => Str::random(40),
            'guest_name' => $guestName,
            'mobile' => $mobile,
            'room_number' => $room,
            'reserve_id' => $reserveId,
            'documents' => [],
            'status' => 'pending',
            'expires_at' => now()->addMinutes(5),
        ]);
    }
}
