<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Services\CommonDataService;
use App\Common\Services\LoadPostRequestService;

use Twilio\Rest\Client;

class LoadPostRequestController extends Controller
{
    public function __construct(
                                    CommonDataService 			   $common_data_service,                 
                                    LoadPostRequestService 	   $load_post_request_service
                               )   
    {
        $this->CommonDataService      = $common_data_service;
        $this->LoadPostRequestService = $load_post_request_service;
    }
    
    public function store_load_post_request(Request $request)
    {
        $arr_response = $this->LoadPostRequestService->store_load_post_request($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    /*when driver accept the request then this method will call*/
    public function process_load_post_request(Request $request,Client $client)
    {
        $arr_response = $this->LoadPostRequestService->process_load_post_request($request,$client);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    
    public function process_new_load_post_request(Request $request)
    {
        $arr_response = $this->LoadPostRequestService->process_new_load_post_request($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    
    public function load_post_details(Request $request)
    {
        $arr_response = $this->LoadPostRequestService->load_post_details($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    
    public function driver_details_on_tap(Request $request)
    {
        $arr_response = $this->LoadPostRequestService->driver_details_on_tap($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    
    public function repost_load_post_request(Request $request)
    {
        $arr_response = $this->LoadPostRequestService->repost_load_post_request($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    public function pending_load_post(Request $request)
    {
        $arr_response = $this->LoadPostRequestService->pending_load_post($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    
    public function ongoing_trips(Request $request)
    {
        $arr_response = $this->LoadPostRequestService->ongoing_trips($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    
    public function pending_trips(Request $request)
    {
        $arr_response = $this->LoadPostRequestService->pending_trips($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }

    public function completed_trips(Request $request)
    {
        $arr_response = $this->LoadPostRequestService->completed_trips($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }   
    
    public function canceled_trips(Request $request)
    {
        $arr_response = $this->LoadPostRequestService->canceled_trips($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }   
    
    public function trip_details(Request $request)
    {
        $arr_response = $this->LoadPostRequestService->trip_details($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    public function track_driver(Request $request)
    {
        $arr_response = $this->LoadPostRequestService->track_driver($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    public function process_trip_status(Request $request,Client $client)
    {
        $arr_response = $this->LoadPostRequestService->process_trip_status($request,$client);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    public function payment_receipt(Request $request)
    {
        $arr_response = $this->LoadPostRequestService->payment_receipt($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    
    public function cancel_pending_load_post(Request $request)
    {
        $arr_response = $this->LoadPostRequestService->cancel_pending_load_post($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    public function load_post_all_driver_details(Request $request)
    {
        $arr_response = $this->LoadPostRequestService->load_post_all_driver_details($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
}
