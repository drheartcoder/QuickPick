<?php

namespace App\Common\Services;

use App\Models\EmailTemplateModel;

use \Session;
use \Mail;

class EmailService
{
	public function __construct(EmailTemplateModel $email)
	{
		$this->EmailTemplateModel = $email;
		$this->BaseModel          = $this->EmailTemplateModel;
	}

	public function send_mail($arr_mail_data = FALSE)
	{
		if(isset($arr_mail_data) && sizeof($arr_mail_data)>0)
		{
			$arr_email_template = [];
			$obj_email_template = $this->EmailTemplateModel
										->with(['translations' => function ($query) {
											$query->where('locale','en');
										}])
										->whereHas('translations' , function ($query) {
											$query->where('locale','en');
										})
										->where('id',$arr_mail_data['email_template_id'])
										->first();
			
			if($obj_email_template)
	      	{
	        	$arr_email_template = $obj_email_template->toArray();
	        	$user               = $arr_mail_data['user'];
	        	
	        	if(isset($arr_email_template['translations'][0]['template_html']))
	        	{
		        	$content = $arr_email_template['translations'][0]['template_html'];
		        	
		        	if(isset($arr_mail_data['arr_built_content']) && sizeof($arr_mail_data['arr_built_content'])>0)
		        	{
		        		foreach($arr_mail_data['arr_built_content'] as $key => $data)
		        		{
		        			$content = str_replace("##".$key."##",$data,$content);
		        		}
		        	}

		        	$content = view('email.front_general',compact('content'))->render();
		        	$content = html_entity_decode($content);
		        	
		        	$send_mail = Mail::send(array(),array(), function($message) use($user,$arr_email_template,$content){
			        	$name = isset($user['first_name']) ? $user['first_name']:"";
				        $message->from($arr_email_template['template_from_mail'], $arr_email_template['template_from']);
				        // $message->to($user['email'], $name )
				        $message->to('rsn.navale@mailinator.com', $name )
						          ->subject($arr_email_template['translations'][0]['template_subject'])
						          ->setBody($content, 'text/html');
			        });

			        return $send_mail;
		        }
	        }
	    }
	    return false;    
	}

	public function send_custom_template_mail($arr_mail_data = false)
	{
		if(isset($arr_mail_data) && sizeof($arr_mail_data)>0)
		{
			$email_template_name = isset($arr_mail_data['email_template_name']) ? $arr_mail_data['email_template_name'] : '';			
			if($email_template_name!='')
			{
				$content = view('email.'.$email_template_name,compact('content'))->render();
	        	$content = html_entity_decode($content);
	        	
	        	$email_from       = isset($arr_mail_data['email_from']) ? $arr_mail_data['email_from'] : '';
	        	$email_to         = isset($arr_mail_data['user']['email']) ? $arr_mail_data['user']['email'] : '';
	        	$template_subject = isset($arr_mail_data['template_subject']) ? $arr_mail_data['template_subject'] : '';

	        	if($email_from!='' && $template_subject!='' && $email_to!=''){

		        	$send_mail = Mail::send(array(),array(), function($message) use($email_from,$template_subject,$email_to,$content){
				        $message->from($email_from, config('app.project.name'));
				        $message->to($email_to)
				        //$message->to('dilipp@webwingtechnologies.com')
				        //$message->to('shankar@webwingtechnologies.com')
						// $message->to('rsn.navale@gmail.com')

						          ->subject($template_subject)
						          ->setBody($content, 'text/html');
			        });
					return $send_mail;
	        	}

			}
	    }
	    return false;    
	}

	public function send_mail_with_attachments($arr_mail_data = FALSE)
	{
		if(isset($arr_mail_data) && sizeof($arr_mail_data)>0)
		{

			//dd($arr_mail_data, $arr_mail_data['attachment']);
			$arr_email_template = [];
			$attachment = [];
			$obj_email_template = $this->EmailTemplateModel
										->with(['translations' => function ($query) {
											$query->where('locale','en');
										}])
										->whereHas('translations' , function ($query) {
											$query->where('locale','en');
										})
										->where('id',$arr_mail_data['email_template_id'])
										->first();
			
			if($obj_email_template)
	      	{
	        	$arr_email_template = $obj_email_template->toArray();
	        	$user               = $arr_mail_data['user'];
	        	
	        	if(isset($arr_email_template['translations'][0]['template_html']))
	        	{
		        	$content = $arr_email_template['translations'][0]['template_html'];
		        	
		        	if(isset($arr_mail_data['arr_built_content']) && sizeof($arr_mail_data['arr_built_content'])>0)
		        	{
		        		foreach($arr_mail_data['arr_built_content'] as $key => $data)
		        		{
		        			$content = str_replace("##".$key."##",$data,$content);
		        		}
		        	}

		        	$attachment = $arr_mail_data['attachment'];
		        	$content = view('email.front_general',compact('content'))->render();
		        	$content = html_entity_decode($content);

		        	/*$send_mail = Mail::queue(array(),array(), function($message) use($user,$arr_email_template,$content,$attachment){
			        	$name = isset($user['first_name']) ? $user['first_name']:"";
				        $message->from($arr_email_template['template_from_mail'], $arr_email_template['template_from']);
				        $message->to($user['email'], $name );
				        $message->attach($attachment)
						          ->subject($arr_email_template['translations'][0]['template_subject'])
						          ->setBody($content, 'text/html');
			        });*/

		        	$send_mail = Mail::send(array(),array(), function($message) use($user,$arr_email_template,$content,$attachment){
			        	$name = isset($user['first_name']) ? $user['first_name']:"";
				        $message->from($arr_email_template['template_from_mail'], $arr_email_template['template_from']);
				        $message->to($user['email'], $name );
				        $message->attach($attachment)
						          ->subject($arr_email_template['translations'][0]['template_subject'])
						          ->setBody($content, 'text/html');
			        });

			        return $send_mail;
		        }
	        }
	    }
	    return false;    
	}

}

?>