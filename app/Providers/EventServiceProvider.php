<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\NewValues' => [
            'App\Listeners\InsertValues',
            //'App\Listeners\VerifyConstraints',
        ],
        'App\Events\BeforeValuesInserted' => [
            'App\Listeners\RemoveOldValue',
        ],
        'App\Events\CheckConstraints' => [
            'App\Listeners\CheckDisplay',
        ],
        'App\Events\CheckProfiles' => [
            'App\Listeners\VerfiyOnlineProfile',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
