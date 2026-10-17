<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Guest extends Model
{
    use HasFactory;

    protected $table = 'guests';

    protected $fillable = [
        'title_id',
        'guest_name',
        'guest_address',
        'nationality_id',
        'city',
        'mobile',
        'email',
        'id_card_type_id',
        'id_card_number',
        'has_privilege_card',
        'privilege_card_no',
        'company_id',
        'new_company_name',
        'new_company_address',
        'new_company_gstin',
        'new_company_phone',
    ];

    protected $casts = [
        'has_privilege_card' => 'boolean',
    ];

    /**
     * Backward-compatible delegation to latestBooking
     */
    public function getReserveIdAttribute()
    {
        return $this->latestBooking?->reserve_id;
    }

    public function getFolioNumberAttribute()
    {
        return $this->latestBooking?->folio_number;
    }

    public function getStatusAttribute()
    {
        return $this->latestBooking?->status ?? 'Confirmed';
    }

    public function getRoomIdAttribute()
    {
        return $this->latestBooking?->room_id;
    }

    public function getRoomAttribute()
    {
        return $this->latestBooking?->room;
    }

    public function getReserveDateAttribute()
    {
        return $this->latestBooking?->reserve_date;
    }

    public function getReserveTimeAttribute()
    {
        return $this->latestBooking?->reserve_time;
    }

    public function getCheckoutDateAttribute()
    {
        return $this->latestBooking?->checkout_date;
    }

    public function getCheckoutTimeAttribute()
    {
        return $this->latestBooking?->checkout_time;
    }

    public function getTotalNightsAttribute()
    {
        return $this->latestBooking?->total_nights ?? 1;
    }

    public function getRoomRateAttribute()
    {
        return $this->latestBooking?->room_rate ?? 0.00;
    }

    public function getTotalAmountAttribute()
    {
        return $this->latestBooking?->total_amount ?? 0.00;
    }

    public function getDiscountPercentageAttribute()
    {
        return $this->latestBooking?->discount_percentage ?? 0.00;
    }

    public function getDiscountAmountAttribute()
    {
        return $this->latestBooking?->discount_amount ?? 0.00;
    }

    public function getGstTypeAttribute()
    {
        return $this->latestBooking?->gst_type ?? 'exclusive';
    }

    public function getGstPercentageAttribute()
    {
        return $this->latestBooking?->gst_percentage ?? 0.00;
    }

    public function getGstAmountAttribute()
    {
        return $this->latestBooking?->gst_amount ?? 0.00;
    }

    public function getPayableAmountAttribute()
    {
        return $this->latestBooking?->payable_amount ?? 0.00;
    }

    public function getAdvanceAmountAttribute()
    {
        return $this->latestBooking?->advance_amount ?? 0.00;
    }

    public function getBalanceAttribute()
    {
        return $this->latestBooking?->balance ?? 0.00;
    }

    public function getRegistrationTypeAttribute()
    {
        return $this->latestBooking?->registrationType;
    }

    public function getReservationModeAttribute()
    {
        return $this->latestBooking?->reservationMode;
    }

    public function getPlanAttribute()
    {
        return $this->latestBooking?->plan;
    }

    public function getPaymentModeAttribute()
    {
        return $this->payments()->latest()->first()?->paymentMode;
    }

    /**
     * Generate auto Reserve ID (forwards to Booking)
     */
    public static function generateNextReserveId(): string
    {
        return Booking::generateNextReserveId();
    }

    /**
     * Generate Folio Number (forwards to Booking)
     */
    public static function generateFolioNumber(?string $roomNumber = null): string
    {
        return Booking::generateFolioNumber($roomNumber);
    }

    /* -------------------------------------------------------------------------- */
    /*                                Relationships                               */
    /* -------------------------------------------------------------------------- */

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'guest_id');
    }

    public function latestBooking(): HasOne
    {
        return $this->hasOne(Booking::class, 'guest_id')->latestOfMany();
    }

    public function currentBooking(): HasOne
    {
        return $this->hasOne(Booking::class, 'guest_id')
            ->whereIn('status', ['Confirmed', 'Arrived', 'Checked In', 'Stay Over'])
            ->latestOfMany();
    }

    public function activeBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'guest_id')
            ->whereIn('status', ['Confirmed', 'Arrived', 'Checked In', 'Stay Over']);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'guest_id');
    }

    public function settlements(): HasMany
    {
        return $this->hasMany(Settlement::class, 'guest_id');
    }

    public function checkouts(): HasMany
    {
        return $this->hasMany(Checkout::class, 'guest_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function title(): BelongsTo
    {
        return $this->belongsTo(Title::class, 'title_id');
    }

    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class, 'nationality_id');
    }

    public function idCardType(): BelongsTo
    {
        return $this->belongsTo(IdCardType::class, 'id_card_type_id');
    }
}
