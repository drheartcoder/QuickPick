<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Services\ReviewService;
use App\Common\Services\UserService;

use Validator;
use Sentinel;

class UserController extends Controller
{
    public function __construct(
                                    ReviewService $review_service,
                                    UserService  $user_service
                               )
    {
        $this->ReviewService = $review_service;
        $this->UserService  = $user_service;
    }

    public function store_booking_details(Request $request)
    {
        $arr_response = $this->UserService->store_booking_details($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
   
    public function rider_send_driver_request(Request $request)
    {
        $arr_response = $this->UserService->rider_send_driver_request($request);
        return $this->build_response($arr_response['status'],$arr_response['msg']);
    } 
    public function get_family_members_info(Request $request)
    {
        $arr_response = $this->UserService->get_family_members_info($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }

    /*public function get_ride_request(Request $request)
    {
        $arr_response = $this->UserService->get_ride_request($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['arr_data']);
    }  */  

    public function get_all_drivers(Request $request)
    {
        $arr_response = $this->UserService->get_all_drivers($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }

    public function get_booking_details(Request $request)
    {
        $arr_response = $this->UserService->get_booking_details($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['arr_data']);
    }    

 /* public function process_login(Request $request)
    {
        $arr_response = $this->UserService->process_login($request);

    }*/

    public function get_booking_history(Request $request)
    {
        $arr_response = $this->UserService->get_booking_history($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['arr_data']);
    }

    
    
}