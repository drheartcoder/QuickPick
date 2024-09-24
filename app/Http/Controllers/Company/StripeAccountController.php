<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Services\CommonDataService;

use App\Models\UserModel;

use Validator;
use Flash;
use Sentinel;

class StripeAccountController extends Controller
{
	public function __construct(CommonDataService $common_data_service,UserModel $user)
	{
		
		$this->UserModel = $user;
		$this->CommonDataService = $common_data_service;

		$this->arr_view_data           = [];
		$this->module_url_path         = url(config('app.project.company_panel_slug')."/stripe_account");
		
		$this->module_title            = "Company Stripe Account";
		$this->modyle_url_slug         = "Company Stripe Account";
		$this->module_view_folder      = "company.stripe_account";
		$this->theme_color             = theme_color();

		$this->company_id = 0;

        $user = Sentinel::check();
        if($user){
            $this->company_id   = isset($user->id) ? $user->id :0;
            $this->company_name = isset($user->company_name) ? $user->company_name :'';
        }
        
        $this->stripe_client_id     = config('services.stripe_client_id');
        $this->stripe_authorize_url = config('services.stripe_authorize_url');
        $this->stripe_token_url     = config('services.stripe_token_url');

        if(env('IS_LIVE_MODE') == 'YES')
        {
            /*LIVE KEYS*/
            $this->publish_key  = config('services.stripe_live.api_key');
            $this->secret_key   = config('services.stripe_live.api_secret');
        }
        else if(env('IS_LIVE_MODE') == 'NO')
        {
            /*TEST KEYS*/
            $this->publish_key  = config('services.stripe_test.api_key');
            $this->secret_key   = config('services.stripe_test.api_secret');
        }

        $this->stripe_company_redirect_url = url('/company/stripe_account/redirect_from_stripe');
	}

	public function index()
	{
		$arr_data = array();
        
        $obj_data = Sentinel::getUser();
        $arr_data = [];

        if($obj_data)
        {
           $arr_data = $obj_data->toArray();    
        }

		$encrypted_company_id = $this->CommonDataService->encrypt_value($this->company_id);

		$this->arr_view_data['page_title']                      = str_singular($this->module_title);
		$this->arr_view_data['module_title']                    = $this->module_title;
		$this->arr_view_data['module_url_path']                 = $this->module_url_path;
		$this->arr_view_data['theme_color']                     = $this->theme_color;
		$this->arr_view_data['stripe_client_id']                = $this->stripe_client_id;
		$this->arr_view_data['stripe_authorize_url']            = $this->stripe_authorize_url;
		$this->arr_view_data['encrypted_company_id']            = $encrypted_company_id;
		$this->arr_view_data['arr_data']                        = $arr_data;
		$this->arr_view_data['stripe_company_redirect_url']     = $this->stripe_company_redirect_url;

		return view($this->module_view_folder.'.index',$this->arr_view_data);
	}
	public function redirect_from_stripe(Request $request)
    {
    	$arr_response = [];

        if($request->has('error') && $request->input('access_denied'))
        {
            $error_description = $request->get('error_description');
        	
        	Flash::error($error_description);
            return redirect($this->module_url_path);
        }
        $code  = $request->input('code');
        $scope = $request->input('scope');

        if($request->input('code') == '')
        {
        	Flash::error('Stripe code field is empty');
            return redirect($this->module_url_path);

            $arr_response['status']  = 'error';
            $arr_response['msg']     = 'Stripe code field is empty';
            $arr_response['data']    = [];
            return $arr_response;
        }

        if($request->input('scope') == '')
        {
        	Flash::error('Stripe scope field is empty');
            return redirect($this->module_url_path);
        }

        $user_id = $request->input('state');
        if($user_id!='')
        {
            $user_id = $this->CommonDataService->decrypt_value($user_id);
        }

        $arr_user_details = $this->CommonDataService->get_user_details($user_id);
        
        if(sizeof($arr_user_details) == 0)
        {
        	Flash::error('User details not found,Please try again.');
            return redirect($this->module_url_path);
        }

        $arr_token_request_body = [
                                        'grant_type'    => 'authorization_code',
                                        'client_id'     => $this->stripe_client_id,
                                        'code'          => $code,
                                        'client_secret' => $this->secret_key
                                  ];

        $arr_data = $this->check_stripe_user($arr_token_request_body);

        if(isset($arr_data['stripe_user_id']) && $arr_data['stripe_user_id'] != "")
        {
            $arr_update['stripe_account_id']       = isset($arr_data['stripe_user_id']) ? $arr_data['stripe_user_id'] : '';
            $arr_update['stripe_account_response'] = isset($arr_data) ? json_encode($arr_data) : '';

            $status = $this->update_stripe_user_details($arr_update,$user_id);
            if($status)
            {
            	Flash::success('User Stripe account details updated successfully');
            	return redirect($this->module_url_path);
            }
            else
            {
            	Flash::error('Something went wrong,Please try again.');
            	return redirect($this->module_url_path);
            }
        }
        elseif(isset($arr_data['error']) && $arr_data['error'] != "")
        {
            $arr_update['stripe_account_response'] = isset($arr_data) ? json_encode($arr_data) : '';

            $this->update_stripe_user_details($arr_update,$user_id);
            
            $error_description = isset($arr_data['error_description']) ? $arr_data['error_description'] : 'Something went wrong,Please try again.';
            
            Flash::error($error_description);
            return redirect($this->module_url_path);
        }
        else
        {
            $arr_update['stripe_account_response'] = isset($arr_data) ? json_encode($arr_data) : '';
            
            $this->update_stripe_user_details($arr_update,$user_id);
            
            Flash::error('Something went wrong,Please try again.');
            return redirect($this->module_url_path);
        }   
	
		Flash::error('Something went wrong,Please try again.');
        return redirect($this->module_url_path);
    }
    
    private function check_stripe_user($arr_token_request_body)
    {
        $req = curl_init($this->stripe_token_url);
        curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($req, CURLOPT_POST, true );
        curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($arr_token_request_body));
        $respCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
        $resp = curl_exec($req);
        curl_close($req);

        $arr_result = json_decode($resp, true);
        return $arr_result;
    }
    private function update_stripe_user_details($arr_update,$user_id)
    {
        $status = $this->UserModel
                            ->where('id',$user_id)
                            ->update($arr_update);
        if($status){
            return true;
        }
        return false;
    }

}
