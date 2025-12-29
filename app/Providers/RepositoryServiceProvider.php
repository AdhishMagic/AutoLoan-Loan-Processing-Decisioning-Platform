<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts\CreditRepositoryInterface;
use App\Repositories\Contracts\KycRepositoryInterface;
use App\Repositories\Contracts\LoanRepositoryInterface;
use App\Repositories\CreditRepository;
use App\Repositories\KycRepository;
use App\Repositories\LoanRepository;
use Illuminate\Support\ServiceProvider;

final class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LoanRepositoryInterface::class, LoanRepository::class);
        $this->app->bind(KycRepositoryInterface::class, KycRepository::class);
        $this->app->bind(CreditRepositoryInterface::class, CreditRepository::class);
    }
}
