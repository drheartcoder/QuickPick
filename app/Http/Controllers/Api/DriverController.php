<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\UserModel;

use App\Common\Services\ReviewService;
use App\Common\Services\DriverService;

use Validator;
use Sentinel;


use Twilio\Rest\Client;
class DriverController extends Controller
{
    public function __construct(
                                    UserModel $user,                 
                                    ReviewService $review_service,
                                    DriverService $driver_service
                               )   
    {
        $this->UserModel         = $user;
        $this->ReviewService     = $review_service;
        $this->DriverService     = $driver_service;

        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');
        $this->user_profile_base_img_path  = base_path().config('app.project.img_path.user_profile_images');
    }
    
    public function update_lat_lng(Request $request,Client $client)
    {
        $arr_response = $this->DriverService->update_lat_lng($request,$client);
        
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    public function update_availability_status(Request $request)
    {
        $arr_response = $this->DriverService->update_availability_status($request);
        return $this->build_response($arr_response['status'],$arr_response['msg']);
    }
    public function get_driver_availability_status(Request $request)
    {
        $arr_response = $this->DriverService->get_driver_availability_status($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    
    public function get_vehicle_details(Request $request)
    {
        $arr_response = $this->DriverService->get_vehicle_details($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    
    public function update_vehicle_details(Request $request)
    {
        $arr_response = $this->DriverService->update_vehicle_details($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    
    public function get_driver_fair_charge(Request $request)
    {
        $arr_response = $this->DriverService->get_driver_fair_charge($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }  
    public function send_driver_fair_charge(Request $request)
    {
        $arr_response = $this->DriverService->send_driver_fair_charge($request);
        return $this->build_response($arr_response['status'],$arr_response['msg']);
    }  

    public function get_driver_deposit_money(Request $request)
    {
        $arr_response = $this->DriverService->get_driver_deposit_money($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    } 

    public function process_deposit_money_request(Request $request)
    {
        $arr_response = $this->DriverService->process_deposit_money_request($request);
        return $this->build_response($arr_response['status'],$arr_response['msg']);
    } 

    public function store_driver_deposit(Request $request)
    {
        $arr_response = $this->DriverService->store_driver_deposit($request);
        return $this->build_response($arr_response['status'],$arr_response['msg']);
    } 
    public function get_earning(Request $request)
    {
        $arr_response = $this->DriverService->get_earning($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }  
    public function get_total_earning(Request $request)
    {
        $arr_response = $this->DriverService->get_total_earning($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    public function change_ride_status(Request $request)
    {
        $arr_response = $this->DriverService->change_ride_status($request);
        return $this->build_response($arr_response['status'],$arr_response['msg']);
    }
    public function get_driver_details(Request $request)
    {
        $arr_response = $this->DriverService->get_driver_details($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['arr_data']);
    }
    public function change_availability_status(Request $request)
    {
        $arr_response = $this->DriverService->change_availability_status($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'], $arr_response['arr_data']);
    }
    public function get_driver_payment_history(Request $request)
    {
        $arr_response = $this->DriverService->get_driver_payment_history($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'], $arr_response['arr_data']);
    }
    public function payment_status(Request $request)
    {
        $arr_response = $this->DriverService->payment_status($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],  $arr_response['arr_data']);
    }
}
