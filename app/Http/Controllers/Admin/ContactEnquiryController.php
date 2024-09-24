<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ContactEnquiryModel;


use App\Common\Traits\MultiActionTrait;
use App\Common\Services\EmailService;

use Session;
use Validator;
use Flash;

class ContactEnquiryController extends Controller
{
    use MultiActionTrait;

	public function __construct(ContactEnquiryModel $contact_enquiry,
								EmailService $email_service)  
	{
        $this->arr_view_data 		= [];
		$this->ContactEnquiryModel 	= $contact_enquiry;

        $this->BaseModel            = $this->ContactEnquiryModel;
        $this->EmailService 		= $email_service;

		$this->module_url_path 		= url(config('app.project.admin_panel_slug')."/contact_enquiry");
        $this->module_view_folder   = "admin.contact_enquiry";
        $this->module_title         = "Contact Inquiry";
        $this->theme_color          = theme_color();
	}

	public function index() 
	{	
		$arr_contact_enquiry = array();
		$obj_contact_enquiry = $this->BaseModel->orderBy('id','DESC')->get();

		if($obj_contact_enquiry != FALSE)
		{
			$arr_contact_enquiry = $obj_contact_enquiry->toArray();
		}

        
		$this->arr_view_data['arr_contact_enquiry'] = $arr_contact_enquiry;
        $this->arr_view_data['page_title'] 			= "Manage ".str_singular($this->module_title);
        $this->arr_view_data['module_title'] 		= str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] 	= $this->module_url_path;

        return view($this->module_view_folder.'.index',$this->arr_view_data);
	}

	public function view($enc_id)
	{
		$id = base64_decode($enc_id);

        $view_enquiry = $this->BaseModel->where('id',$id)->update(['is_view'=>'1']);  
       
		$arr_contact_enquiry_details = array();
		$obj_contact_enquiry 		 = $this->BaseModel->where('id','=',$id)->first();
		if($obj_contact_enquiry != FALSE)
		{
			$arr_contact_enquiry_details = $obj_contact_enquiry->toArray();
		}

		$this->arr_view_data['arr_contact_enquiry'] = $arr_contact_enquiry_details;
        $this->arr_view_data['page_title'] 			= "View ".str_singular($this->module_title);
        $this->arr_view_data['module_title'] 		= str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] 	= $this->module_url_path;

        return view($this->module_view_folder.'.view',$this->arr_view_data);
	}
	public function create_email($enc_id=FALSE)
	{	
		if($enc_id!="")
		{
			$user_id = base64_decode($enc_id);
 			
 			$email_data = $this->BaseModel
 							->select("first_name","last_name","email")
 							->where("id",$user_id)
 							->first(); 

			$this->arr_view_data['email_data'] 			= $email_data;

        	$this->arr_view_data['page_title'] 			= "Create Mail ".str_singular($this->module_title);
        	$this->arr_view_data['module_title'] 		= str_plural($this->module_title);
        	$this->arr_view_data['module_url_path'] 	= $this->module_url_path;	
        	$this->arr_view_data['theme_color'] 	= $this->theme_color;

        	return view($this->module_view_folder.'.send_mail',$this->arr_view_data);
		} 			
	}

	public function send_email(Request $request)
	{
		$arr_rules = [];

        $arr_rules['email_body'] = "required";

    	$validator = Validator::make($request->all(),$arr_rules);
    	
    	if($validator->fails())
    	{
    		return redirect()->back()->withErrors($validator)->withInput($request->all());
    	}
		$email_body 	      	= $request->input('email_body');
		$email_to	  		 	= $request->input('email_to');
        
        $arr_mail_data = $this->built_mail_data($email_body,$email_to); 
        $email_status  = $this->EmailService->send_mail($arr_mail_data);
        
        if($email_status)
        {
        	Flash::success('Contact enquiry reply mail is sent.');
            return redirect()->back();
        }
        else
        {
        	Flash::success('Problem occured while sending email.');
            return redirect()->back();
        }
	}

	public function built_mail_data($email_body,$email_to)
    {
        $arr_built_content = [
                              	'EMAIL_DATA'       => $email_body,
                             ];
        $arr_mail_data                      = [];
        $arr_mail_data['email_template_id'] = '2';
        $arr_mail_data['arr_built_content'] = $arr_built_content;
        $arr_mail_data['user']              = ['email' => $email_to];

        return $arr_mail_data;
    }
}
