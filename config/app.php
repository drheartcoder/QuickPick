<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => 'http://localhost',

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    // 'timezone' => 'UTC',
    // 'timezone' => 'Asia/Kolkata',
    'timezone' => 'US/Eastern',
    
    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log settings for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Settings: "single", "daily", "syslog", "errorlog"
    |
    */

    'log' => env('APP_LOG', 'single'),

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Application Service Providers...Cartalyst\Sentinel\Laravel\SentinelServiceProvider::class,
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\HelperServiceProvider::class,
        App\Providers\TwilioRestClientProvider::class,
        Maatwebsite\Excel\ExcelServiceProvider::class,
        Dimsav\Translatable\TranslatableServiceProvider::class,
        Barryvdh\Debugbar\ServiceProvider::class,
        Collective\Html\HtmlServiceProvider::class,
        AdamWathan\BootForms\BootFormsServiceProvider::class,
        Laracasts\Flash\FlashServiceProvider::class,
        Cartalyst\Sentinel\Laravel\SentinelServiceProvider::class,
        Yajra\Datatables\DatatablesServiceProvider::class,
        Intervention\Image\ImageServiceProvider::class,
        //STS\Session\LaravelRawSessionServiceProvider::class
        Tymon\JWTAuth\Providers\JWTAuthServiceProvider::class,
        Laravel\Socialite\SocialiteServiceProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */
    'aliases' => [

        'App'        => Illuminate\Support\Facades\App::class,
        'Artisan'    => Illuminate\Support\Facades\Artisan::class,
        'Auth'       => Illuminate\Support\Facades\Auth::class,
        'Blade'      => Illuminate\Support\Facades\Blade::class,
        'Cache'      => Illuminate\Support\Facades\Cache::class,
        'Config'     => Illuminate\Support\Facades\Config::class,
        'Cookie'     => Illuminate\Support\Facades\Cookie::class,
        'Crypt'      => Illuminate\Support\Facades\Crypt::class,
        'DB'         => Illuminate\Support\Facades\DB::class,
        'Eloquent'   => Illuminate\Database\Eloquent\Model::class,
        'Event'      => Illuminate\Support\Facades\Event::class,
        'File'       => Illuminate\Support\Facades\File::class,
        'Gate'       => Illuminate\Support\Facades\Gate::class,
        'Hash'       => Illuminate\Support\Facades\Hash::class,
        'Lang'       => Illuminate\Support\Facades\Lang::class,
        'Log'        => Illuminate\Support\Facades\Log::class,
        'Mail'       => Illuminate\Support\Facades\Mail::class,
        'Password'   => Illuminate\Support\Facades\Password::class,
        'Queue'      => Illuminate\Support\Facades\Queue::class,
        'Redirect'   => Illuminate\Support\Facades\Redirect::class,
        'Redis'      => Illuminate\Support\Facades\Redis::class,
        'Request'    => Illuminate\Support\Facades\Request::class,
        'Response'   => Illuminate\Support\Facades\Response::class,
        'Route'      => Illuminate\Support\Facades\Route::class,
        'Schema'     => Illuminate\Support\Facades\Schema::class,
        'Session'    => Illuminate\Support\Facades\Session::class,
        'Storage'    => Illuminate\Support\Facades\Storage::class,
        'URL'        => Illuminate\Support\Facades\URL::class,
        'Validator'  => Illuminate\Support\Facades\Validator::class,
        'View'       => Illuminate\Support\Facades\View::class,
        'Form'       => Collective\Html\FormFacade::class,
        'Html'       => Collective\Html\HtmlFacade::class,
        'BootForm'   => AdamWathan\BootForms\Facades\BootForm::class,
        'Flash'      => Laracasts\Flash\Flash::class,
        'Activation' => Cartalyst\Sentinel\Laravel\Facades\Activation::class,
        'Reminder'   => Cartalyst\Sentinel\Laravel\Facades\Reminder::class,
        'Sentinel'   => Cartalyst\Sentinel\Laravel\Facades\Sentinel::class,
        'Datatables' => Yajra\Datatables\Facades\Datatables::class,
        'Image'      => Intervention\Image\Facades\Image::class,
        'Excel'      => Maatwebsite\Excel\Facades\Excel::class,
        'JWTAuth'    => Tymon\JWTAuth\Facades\JWTAuth::class,
        'Socialite' => Laravel\Socialite\Facades\Socialite::class
    ],

    'project' => [
        'name'      =>'QuickPick',
        'img_path'  => [
                        'category'              => '/uploads/categories/',
                        'user_profile_images'   => '/uploads/profile_image/',
                        'testimonial_images'    => '/uploads/testimonial_images/',
                        'news_images'           => '/uploads/news/',
                        'services'              => '/uploads/services/',
                        'business_logo'         => '/uploads/business_logo/',
                        'user_package_images'   => '/uploads/package_images/',
                        'workshop_document'     => '/uploads/workshop_document/',
                        'advertisement'         => '/uploads/advertisement/',
                        'driving_license'       => '/uploads/driving_license/',
                        'vehicle_doc'           => '/uploads/vehicle_doc/',
                        'load_post_img'         => '/uploads/load_post_img/',
                        'driver_deposit_receipt'=> '/uploads/driver_deposit_receipt/',
                        'payment_receipt'       => '/uploads/payment_receipt/',
                        'review_tag'            => '/uploads/review_tag/',
                        'invoice'               => '/uploads/invoice/',
                        'trip_lat_lng'          => '/uploads/trip_lat_lng/',
                        'enterprise_license'    => '/uploads/enterprise_license/',
                        'banner_image'          => '/uploads/banner_image/',


                    ],        

        'one_signal_credentials' => [
                                        'driver_api_key' => 'MGVjOGI3MzYtMmE3Yi00NWNiLWJhMzktNGJhMTNjYjUzYmEy',
                                        'driver_app_id'  => '7cf90ff0-23e2-474b-850a-78c599e9ae39',

                                        'user_api_key'   => 'Y2FjZGFkZWItNWIzYi00ODBjLWE0NDctNGE0YjBmMmZiMTk2',
                                        'user_app_id'    => '43730f9e-4841-4e84-8215-0a964e821087',
                                        
                                        'website_api_key' =>'OWRmYmFiZjYtOTI3NS00N2U5LThkNjktMTMxNDcwZmQ3ZGY1',
                                        'website_app_id' =>'dc02cbd1-5210-4c16-8cc5-3dacd3a18701',
                                    ],     

        'twilio_credentials' => [
                                        'twilio_sid'       => 'AC61ac3fc858aa01a7e5f2db7bc71e2c21',
                                        'twilio_token'     => '259c02f7e11d602186d09e2cf0b2b1c7',
                                        'from_user_mobile' => '+17032152456',
                                    ],     


                       
        'google_map_api_key' => 'AIzaSyASynNXpP9v040cNSh2f_A8XVnPkQ5mUEY',/*AIzaSyD7eV39U3vJG0JYnOntHt3D-oOqTFnzFJo*/
                      
        'admin_panel_slug' => 'admin',
        'company_panel_slug' => 'company',
        'role_slug'        => [
                                'admin_role_slug'           => 'admin',
                                'subadmin_role_slug'        => 'sub_admin',
                                'company_role_slug'         => 'company',
                                'user_role_slug'            => 'user',
                                'driver_role_slug'          => 'driver',
                                'enterprise_admin_role_slug'=>'enterprise_admin',
                                'enterprise_user_role_slug' =>'enterprise_user',
                            ],
                            
        'currency'      =>  '&dollar;',
        'html_currency' =>  '&#36;'                       


        ],
        
];
