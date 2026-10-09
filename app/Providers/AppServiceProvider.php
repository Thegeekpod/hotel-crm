<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use App\Models\Floor;
use App\Models\RoomCategory;
use App\Models\Company;
use App\Models\IdCardType;
use App\Models\ReservationMode;
use App\Models\PaymentMode;
use App\Models\RegistrationType;
use App\Models\Title;
use App\Models\Nationality;
use App\Models\Room;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        View::composer(['frontoffice.*', 'frontoffice.layouts.app'], function ($view) {
            if (!isset($view->floors)) {
                $view->with('floors', Floor::where('status', 'Active')->orderBy('floor', 'asc')->get());
            }
            if (!isset($view->categories)) {
                $view->with('categories', RoomCategory::where('status', 'Active')->get());
            }
            if (!isset($view->companies)) {
                $view->with('companies', Company::where('status', 'Active')->get());
            }
            if (!isset($view->idCardTypes)) {
                $view->with('idCardTypes', IdCardType::where('status', 'Active')->get());
            }
            if (!isset($view->reservationModes)) {
                $view->with('reservationModes', ReservationMode::where('status', 'Active')->get());
            }
            if (!isset($view->paymentModes)) {
                $view->with('paymentModes', PaymentMode::where('status', 'Active')->get());
            }
            if (!isset($view->registrationTypes)) {
                $view->with('registrationTypes', RegistrationType::where('status', 'Active')->get());
            }
            if (!isset($view->titles)) {
                $view->with('titles', Title::where('status', 'Active')->get());
            }
            if (!isset($view->nationalities)) {
                $view->with('nationalities', Nationality::where('status', 'Active')->get());
            }
            if (!isset($view->allRoomsData)) {
                $rooms = Room::with(['floorRelation', 'categoryRelation'])->orderByRaw('CAST(room_number AS UNSIGNED) ASC, room_number ASC')->get();
                $allRoomsList = $rooms->map(function ($r) {
                    return [
                        'id' => $r->id,
                        'room' => (string)$r->room_number,
                        'floor_id' => $r->floor_id ?? ($r->floorRelation?->id),
                        'floor' => (string)($r->floorRelation?->floor ?? '1'),
                        'floor_name' => $r->floorRelation?->name ?? ('Floor ' . ($r->floorRelation?->floor ?? '1')),
                        'category_id' => $r->category_id ?? ($r->categoryRelation?->id),
                        'category' => $r->categoryRelation?->name ?? 'Deluxe',
                        'rate' => (float)$r->rate,
                        'status' => $r->status === 'Inactive' ? 'blocked' : 'available',
                    ];
                });
                $view->with('allRoomsData', $allRoomsList);
            }
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
