<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FutureBookingRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'future_booking_request:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify drivers and user for future booking';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $service_class =  \App\Common\Services\Cron\FutureBookingRequestService::class;
        $service_class =  app($service_class);
        $service_class->check_future_bookings();
    }
}
