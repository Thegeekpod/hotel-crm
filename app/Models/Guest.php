<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $table = 'guests';

    protected $fillable = [
        'reserve_id',
        'registration_type_id',
        'reservation_mode_id',
        'company_id',
        'new_company_name',
        'new_company_address',
        'new_company_gstin',
        'new_company_phone',
        'reserve_date',
        'reserve_time',
        'title_id',
        'guest_name',
        'guest_address',
        'nationality_id',
        'city',
        'mobile',
        'email',
        'dob',
        'anniversary',
        'status',
        'has_privilege_card',
        'privilege_card_no',
        'room_id',
        'id_card_type_id',
        'id_card_number',
        'payment_mode_id',
        'advance_amount',
        'payment_remarks',
        'primary_guest_id',
        'folio_number',
        'balance',
    ];

    protected $casts = [
        'reserve_date' => 'date',
        'dob' => 'date',
        'anniversary' => 'date',
        'has_privilege_card' => 'boolean',
        'advance_amount' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

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
     * Generate Folio Number (e.g., FOL-101-492)
     */
    public static function generateFolioNumber(?string $roomNumber = null): string
    {
        $room = $roomNumber ?: 'G';
        $rand = rand(100, 999);
        return "FOL-{$room}-{$rand}";
    }

    public function registrationType()
    {
        return $this->belongsTo(RegistrationType::class, 'registration_type_id');
    }

    public function reservationMode()
    {
        return $this->belongsTo(ReservationMode::class, 'reservation_mode_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function title()
    {
        return $this->belongsTo(Title::class, 'title_id');
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class, 'nationality_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function idCardType()
    {
        return $this->belongsTo(IdCardType::class, 'id_card_type_id');
    }

    public function paymentMode()
    {
        return $this->belongsTo(PaymentMode::class, 'payment_mode_id');
    }

    public function primaryGuest()
    {
        return $this->belongsTo(Guest::class, 'primary_guest_id');
    }

    public function additionalGuests()
    {
        return $this->hasMany(Guest::class, 'primary_guest_id');
    }
}
