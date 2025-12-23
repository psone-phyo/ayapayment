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
        $this->app->singleton(AyaGatewayService::class, function () {
            return new AyaGatewayService();
        });

        $this->app->singleton(AyaPayService::class, function () {
            return new AyaPayService();
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
