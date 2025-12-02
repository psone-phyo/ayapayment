<?php

namespace AyaPayment;

use Illuminate\Support\ServiceProvider;

class AyaPaymentServiceProvider extends ServiceProvider
{
    public function register()
    {
        // merge config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/ayapayment.php',
            'ayapayment'
        );

        // bind service
        $this->app->singleton('ayapayment', function () {
            return new AyaPaymentService();
        });
    }

    public function boot()
    {
        // publish config
        $this->publishes([
            __DIR__ . '/../config/ayapayment.php' => config_path('ayapayment.php'),
        ], 'ayapayment-config');
    }
}
