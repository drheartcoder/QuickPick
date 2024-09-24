<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Services\CommonDataService;
use App\Common\Services\LoadPostRequestService;

class RideController extends Controller
{
    public function __construct(
                                    CommonDataService 			   $common_data_service,                 
                                    LoadPostRequestService 	   $load_post_request_service
                               )   
    {
        $this->CommonDataService      = $common_data_service;
        $this->LoadPostRequestService = $load_post_request_service;
    }
    public function get_fair_estimate(Request $request)
    {   
    	$arr_response = $this->LoadPostRequestService->get_fair_estimate($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
}
