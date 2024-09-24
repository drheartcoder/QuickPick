<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Services\ReviewService;
use App\Common\Services\AuthService;
use App\Common\Services\StripeService;

use App\Models\UserModel;

use Validator;
use Sentinel;

class AuthController extends Controller
{
    public function __construct(
                                    ReviewService $review_service,
                                    AuthService   $auth_service,
                                    StripeService $stripe_service
                                )
    {
        $this->ReviewService = $review_service;
        $this->AuthService   = $auth_service;
        $this->StripeService = $stripe_service;
    }
    
    public function store(Request $request)
    {
        $arr_response = $this->AuthService->store($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }

    public function update_driver_vehicle_details(Request $request)
    {
        $arr_response = $this->AuthService->update_driver_vehicle_details($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    
    public function process_login(Request $request)
    {
        $arr_response = $this->AuthService->process_login($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);   
    }

    public function login_facebook(Request $request)
    {
        $arr_response = $this->AuthService->login_facebook($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }

    public function register_facebook(Request $request)
    {
        $arr_response = $this->AuthService->register_facebook($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }

    public function verify_otp(Request $request)
    {
        $arr_response = $this->AuthService->verify_otp($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }

    public function resend_otp(Request $request)
    {
        $arr_response = $this->AuthService->resend_otp($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }

    public function reset_password(Request $request)
    {
        $arr_response = $this->AuthService->reset_password($request);
        return $this->build_response($arr_response['status'],$arr_response['msg']);
    }

    public function forget_password(Request $request)
    {
        $arr_response = $this->AuthService->forget_password($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }

    public function change_password(Request $request)
    {
        $arr_response = $this->AuthService->change_password($request);
        return $this->build_response($arr_response['status'],$arr_response['msg']);
    }

    public function get_profile(Request $request)
    {
        $arr_response = $this->AuthService->get_profile($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }

    public function update_profile(Request $request)
    {
        $arr_response = $this->AuthService->update_profile($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }

    public function verify_mobile_number(Request $request)
    {
        $arr_response = $this->AuthService->verify_mobile_number($request);
        return $this->build_response($arr_response['status'],$arr_response['msg']);
    }

    public function update_mobile_no(Request $request)
    {
        $arr_response = $this->AuthService->update_mobile_no($request);
        return $this->build_response($arr_response['status'],$arr_response['msg']);
    }
    
    public function get_notification(Request $request)
    {
        $arr_response = $this->AuthService->get_notification($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    
    public function store_review(Request $request)
    {
        $arr_response = $this->ReviewService->store_review($request);
        return $this->build_response($arr_response['status'],$arr_response['msg']);
    }
    public function get_review(Request $request)
    {
        $arr_response = $this->AuthService->get_review($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    
    public function get_card_details(Request $request)
    {
        $arr_response = $this->AuthService->get_card_details($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    public function store_card_details(Request $request)
    {
        $user_id     = validate_user_jwt_token();
        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            $arr_response['data']    = [];
            return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
        }

        if ($request->input('stripe_token') == '') 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid Stripe token';
            $arr_response['data']    = [];
            return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
        }

        $stripe_token = $request->input('stripe_token');
            
        $arr_customer = [];
        $arr_customer['user_id']      = $user_id;
        $arr_customer['stripe_token'] = $stripe_token;

        $arr_response = $this->StripeService->register_customer($arr_customer);

        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    public function delete_card(Request $request)
    {
        $user_id     = validate_user_jwt_token();
        if ($user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid user token';
            $arr_response['data']    = [];
            return $this->build_response($arr_response['status']);
        }

        if ($request->input('card_id') == '') 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Card identifier not found.';
            $arr_response['data']    = [];
            return $this->build_response($arr_response['status']);
        }
        $arr_card = [];
        $arr_card['user_id'] = $user_id;
        $arr_card['card_id'] = $request->input('card_id');

        $arr_response = $this->StripeService->delete_card($arr_card);
        return $this->build_response($arr_response['status'],$arr_response['msg']);   
    }
    
    public function get_bonus_points(Request $request)
    {
        $arr_response = $this->AuthService->get_bonus_points($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
    
    public function get_review_details(Request $request)
    {
        $arr_response = $this->AuthService->get_review_details($request);
        return $this->build_response($arr_response['status'],$arr_response['msg'],$arr_response['data']);
    }
}
