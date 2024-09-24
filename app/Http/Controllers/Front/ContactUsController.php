<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\ContactEnquiryModel;
use App\Models\SiteSettingModel;
use App\Models\StaticPageModel;
use App\Common\Services\EmailService;
use Validator;
use Flash;

class ContactUsController extends Controller
{
    public function __construct(ContactEnquiryModel $contact_enquiry,SiteSettingModel $site_setting, StaticPageModel $static_page,EmailService $email_service) 
    {
        $this->arr_view_data       = [];
        $this->ContactEnquiryModel = $contact_enquiry;
        $this->SiteSettingModel    = $site_setting;
        $this->StaticPageModel     = $static_page;
        $this->EmailService        = $email_service;
    }
    public function index()
    {
    	$arr_site_setting = [];

    	$obj_site_setting = $this->SiteSettingModel
    									->select('site_address','site_contact_number','site_email_address')
    										->first();
    	if($obj_site_setting){
    		$arr_site_setting = $obj_site_setting->toArray();
    	}
    	
        $obj_data = $this->StaticPageModel->where('page_slug','contact-us')->first();
        
        $this->arr_view_data['page_details'] = $obj_data; 

    	$this->arr_view_data['page_title']            = ' Contact-Us';
    	$this->arr_view_data['arr_site_setting'] = $arr_site_setting;
    	
        return view('front.contact_us',$this->arr_view_data);
    }
    public function store_contact_enquiry(Request $request)
    {
        $arr_response = [];

        $arr_rules['first_name'] = "required";
        $arr_rules['last_name']  = "required";
        $arr_rules['email']      = "required";
        $arr_rules['phone']      = "required";
        $arr_rules['address']    = "required|max:1000";
        $arr_rules['subject']    = "required|max:1000";
        
        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {
        	$arr_response['status'] = 'error';
        	$arr_response['msg']    = 'Please fill all required fields.';
        	return response()->json($arr_response);     
        }
        
        $arr_data               = array();
        $arr_data['first_name'] = $request->input('first_name');
        $arr_data['last_name']  = $request->input('last_name');
        $arr_data['email']      = $request->input('email');
        $arr_data['phone']      = $request->input('phone');
        $arr_data['subject']    = $request->input('subject');
        $arr_data['address']    = $request->input('address');

		$status = $this->ContactEnquiryModel->create($arr_data);
        if($status)      
        {
            $arr_mail_data = $this->built_mail_data($arr_data);
            $this->EmailService->send_mail($arr_mail_data);
            $arr_response['status'] = 'success';
        	$arr_response['msg']    = 'Contact enquiry request successfully sent to admin,will get back to you shortly.';
        	return response()->json($arr_response);     
        }
        else
        {
        	$arr_response['status'] = 'error';
        	$arr_response['msg']    = 'Problem Occurred, While sending contact enquiry,Please try again.';
        	return response()->json($arr_response);
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem Occurred, While sending contact enquiry,Please try again.';
        return response()->json($arr_response);
    }
    private function built_mail_data($arr_data)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                    'FULL_NAME'  => $arr_data['first_name'].' '.$arr_data['last_name'],
                                    'EMAIL'      => $arr_data['email'],
                                    'CONTACT_NO' => $arr_data['phone'],
                                    'ADDRESS'    => $arr_data['address'],
                                    'MESSAGE'    => $arr_data['subject']
                                ];


            $arr_mail_data                      = [];
            $arr_mail_data['email_template_id'] = '17';
            $arr_mail_data['arr_built_content'] = $arr_built_content;
            $arr_mail_data['user']              = $arr_data;

            return $arr_mail_data;
        }
        return FALSE;
    }
}
