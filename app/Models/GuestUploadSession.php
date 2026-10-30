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
        return $this->expires_at->isPast() || $this->status === 'expired';
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
