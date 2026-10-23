<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    protected $fillable = [
        'reserve_id',
        'settle_number',
        'folio_number',
        'guest_id',
        'room_id',
        'registration_type_id',
        'reservation_mode_id',
        'company_id',
        'new_company_name',
        'new_company_address',
        'new_company_gstin',
        'new_company_phone',
        'plan_id',
        'checkin_date',
        'checkin_time',
        'reserve_date',
        'reserve_time',
        'checkout_date',
        'checkout_time',
        'total_nights',
        'room_rate',
        'total_amount',
        'discount_percentage',
        'discount_amount',
        'gst_type',
        'gst_percentage_id',
        'gst_percentage',
        'gst_amount',
        'payable_amount',
        'advance_amount',
        'balance',
        'status',
        'primary_booking_id',
        'remarks',
    ];

    /**
     * Backward-compatible folio_number accessor and mutator mapped to settle_number
     */
    public function getFolioNumberAttribute()
    {
        return $this->attributes['settle_number'] ?? ($this->attributes['folio_number'] ?? null);
    }

    public function setFolioNumberAttribute($value)
    {
        $this->attributes['settle_number'] = $value;
    }

    protected $casts = [
        'checkin_date' => 'date',
        'reserve_date' => 'date',
        'checkout_date' => 'date',
        'total_nights' => 'integer',
        'room_rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'discount_percentage' => 'float',
        'discount_amount' => 'decimal:2',
        'gst_percentage' => 'float',
        'gst_amount' => 'decimal:2',
        'payable_amount' => 'decimal:2',
        'advance_amount' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    /**
     * Backward-compatible reserve_date accessors/mutators
     */
    public function getReserveDateAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value) : ($this->attributes['checkin_date'] ? \Carbon\Carbon::parse($this->attributes['checkin_date']) : null);
    }

    public function getReserveTimeAttribute($value)
    {
        return $value ?: ($this->attributes['checkin_time'] ?? null);
    }

    /**
     * Guest delegation accessors
     */
    public function getGuestNameAttribute()
    {
        return $this->guest?->guest_name;
    }

    public function getTitleAttribute()
    {
        return $this->guest?->title;
    }

    public function getNationalityAttribute()
    {
        return $this->guest?->nationality;
    }

    public function getIdCardTypeAttribute()
    {
        return $this->guest?->idCardType;
    }

    public function getIdCardNumberAttribute()
    {
        return $this->guest?->id_card_number;
    }

    public function getMobileAttribute()
    {
        return $this->guest?->mobile;
    }

    public function getEmailAttribute()
    {
        return $this->guest?->email;
    }

    public function getCityAttribute()
    {
        return $this->guest?->city;
    }

    public function getGuestAddressAttribute()
    {
        return $this->guest?->guest_address;
    }

    public function getHasPrivilegeCardAttribute()
    {
        return (bool)($this->guest?->has_privilege_card);
    }

    public function getPrivilegeCardNoAttribute()
    {
        return $this->guest?->privilege_card_no;
    }

    public function getPaymentModeAttribute()
    {
        return $this->payments()->latest()->first()?->paymentMode;
    }

    public function getPaymentRemarksAttribute()
    {
        return $this->payments()->latest()->first()?->payment_remarks ?? $this->remarks;
    }

    /**
     * Generate auto Reserve ID (e.g., 830\2026-2027)
     */
    public static function generateNextReserveId(): string
    {
        $currentMonth = (int)date('n');
        $currentYear = (int)date('Y');
        
        $finYear = ($currentMonth >= 4) 
            ? $currentYear . '-' . ($currentYear + 1) 
            : ($currentYear - 1) . '-' . $currentYear;

        $latest = self::where('reserve_id', 'like', "%\\{$finYear}")
            ->orWhere('reserve_id', 'like', "%/{$finYear}")
            ->orderBy('id', 'desc')
            ->first();

        $nextNum = 830;
        if ($latest && preg_match('/^(\d+)/', $latest->reserve_id, $matches)) {
            $nextNum = ((int)$matches[1]) + 1;
        } else {
            $count = self::count();
            $nextNum = max(830, 829 + $count + 1);
        }

        return "{$nextNum}\\{$finYear}";
    }

    /**
     * Generate Settle Number (e.g., SET-101-492)
     */
    public static function generateSettleNumber(?string $roomNumber = null): string
    {
        $room = $roomNumber ?: 'G';
        $rand = rand(100, 999);
        return "SET-{$room}-{$rand}";
    }

    /**
     * Backward-compatible alias for generateFolioNumber
     */
    public static function generateFolioNumber(?string $roomNumber = null): string
    {
        return self::generateSettleNumber($roomNumber);
    }

    /* -------------------------------------------------------------------------- */
    /*                                Relationships                               */
    /* -------------------------------------------------------------------------- */

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class, 'guest_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function registrationType(): BelongsTo
    {
        return $this->belongsTo(RegistrationType::class, 'registration_type_id');
    }

    public function reservationMode(): BelongsTo
    {
        return $this->belongsTo(ReservationMode::class, 'reservation_mode_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function gstPercentage(): BelongsTo
    {
        return $this->belongsTo(GstPercentage::class, 'gst_percentage_id');
    }

    public function primaryBooking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'primary_booking_id');
    }

    public function additionalBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'primary_booking_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'booking_id');
    }

    public function settlements(): HasMany
    {
        return $this->hasMany(Settlement::class, 'booking_id');
    }

    public function checkouts(): HasMany
    {
        return $this->hasMany(Checkout::class, 'booking_id');
    }

    public function latestCheckout(): HasOne
    {
        return $this->hasOne(Checkout::class, 'booking_id')->latestOfMany();
    }
}
