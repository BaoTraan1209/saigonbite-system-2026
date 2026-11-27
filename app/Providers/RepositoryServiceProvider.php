<?php

namespace App\Providers;

use App\Repositories\CatalogRepository;
use App\Repositories\CustomerAddressRepository;
use App\Repositories\Contracts\CatalogRepositoryInterface;
use App\Repositories\Contracts\CustomerAddressRepositoryInterface;
use App\Repositories\Contracts\HubRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\HubRepository;
use App\Repositories\OrderRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CatalogRepositoryInterface::class, CatalogRepository::class);
        $this->app->bind(CustomerAddressRepositoryInterface::class, CustomerAddressRepository::class);
        $this->app->bind(HubRepositoryInterface::class, HubRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
    }
}
