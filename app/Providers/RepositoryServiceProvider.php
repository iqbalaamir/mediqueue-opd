<?php

namespace App\Providers;

use App\Repositories\Contracts\AppointmentRepositoryInterface;
use App\Repositories\Contracts\CityRepositoryInterface;
use App\Repositories\Contracts\DoctorRepositoryInterface;
use App\Repositories\Contracts\DoctorSlotRepositoryInterface;
use App\Repositories\Contracts\HospitalRepositoryInterface;
use App\Repositories\Eloquent\AppointmentRepository;
use App\Repositories\Eloquent\CityRepository;
use App\Repositories\Eloquent\DoctorRepository;
use App\Repositories\Eloquent\DoctorSlotRepository;
use App\Repositories\Eloquent\HospitalRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    protected array $repositories = [
        CityRepositoryInterface::class => CityRepository::class,
        HospitalRepositoryInterface::class => HospitalRepository::class,
        DoctorRepositoryInterface::class => DoctorRepository::class,
        DoctorSlotRepositoryInterface::class => DoctorSlotRepository::class,
        AppointmentRepositoryInterface::class => AppointmentRepository::class,
    ];

    public function register(): void
    {
        foreach ($this->repositories as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }
    }

    public function boot(): void
    {
        //
    }
}
