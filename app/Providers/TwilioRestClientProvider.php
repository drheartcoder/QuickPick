<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Twilio\Rest\Client;

class TwilioRestClientProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            Client::class, function ($app) {
                $accountSid = config('app.project.twilio_credentials.twilio_sid');
                $authToken  = config('app.project.twilio_credentials.twilio_token');
                return new Client($accountSid, $authToken);
            }
        );
    }
}
