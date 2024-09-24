<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\UserModel;
use App\Models\MessagesModel;
use App\Models\VehicleTypeModel;
use App\Models\BookingMasterModel;
use App\Models\LoadPostRequestModel;
use App\Models\DriverCarRelationModel;

use App\Common\Services\NotificationsService;


use Flash;
use DB;
use Datatables;
use Excel;

class MessagesController extends Controller
{
    public function __construct(UserModel $user,MessagesModel $messages,NotificationsService $notifications_service)
    {
   		$this->UserModel              = $user;
        $this->MessagesModel          = $messages;

        $this->NotificationsService  = $notifications_service;
 		$this->arr_view_data                = [];
        $this->module_title                 = "Messages";
        $this->module_view_folder           = "admin.messages";
        $this->theme_color                  = theme_color();
        $this->admin_panel_slug             = config('app.project.admin_panel_slug');
        $this->module_url_path              = url(config('app.project.admin_panel_slug')."/messages");		

        $this->user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');
        $this->user_profile_base_img_path   = base_path().config('app.project.img_path.user_profile_images');

        $this->login_user_id = 0;
        $this->login_user_name = '';

    }
    public function index(Request $request)
    {
    	$obj_login_user = \Sentinel::check();
    	if($obj_login_user)
    	{
    		$this->login_user_id = isset($obj_login_user->id) ? $obj_login_user->id : 0;

    		$first_name = isset($obj_login_user->first_name) ? $obj_login_user->first_name : '';
    		$last_name = isset($obj_login_user->last_name) ? $obj_login_user->last_name : '';
    		$this->login_user_name = $first_name.' '.$last_name;
    		$this->login_user_name = ($this->login_user_name!=' ') ? $this->login_user_name : '-';
            
            $from_user_profile_image = url('/uploads/default-profile.png');
            if(isset($obj_login_user->profile_image) && $obj_login_user->profile_image!='' && file_exists($this->user_profile_base_img_path.$obj_login_user->profile_image))
            {
                $from_user_profile_image = $this->user_profile_public_img_path.$obj_login_user->profile_image;
            }

    	}
    	
    	$arr_data = [];
    	$user_role = 'user';

    	if($request->has('user_role') && $request->input('user_role')!=''){
    		$user_role = $request->input('user_role');
    	}
        
        if($request->has('user_id') && $request->input('user_id')!='')
        {
            $tmp_id = base64_decode($request->input('user_id'));
            $this->read_unread_message($tmp_id,$this->login_user_id);
        }
        
        $obj_data = $this->UserModel
    						->select('id','first_name','last_name','profile_image','company_name')
    						->whereHas('roles',function($query) use ($user_role){
    							$query->where('slug',$user_role);
    						})
    						->with(['unread_message_count'=>function($query){
                                $query->where('to_user_id',$this->login_user_id);
                            }])
    						->where('id','!=',$this->login_user_id)
    						->get();
     	if($obj_data){
			$arr_data = $obj_data->toArray();
     	}
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            foreach ($arr_data as $key => $value) 
            {
                $arr_data[$key]['message_count'] = 0;
                if(isset($value['unread_message_count']['message_count']) && $value['unread_message_count']['message_count']>0)
                {
                    $arr_data[$key]['message_count'] = $value['unread_message_count']['message_count'];
                }
                unset($arr_data[$key]['unread_message_count']);

            }
        }
        if(count($arr_data)>0)
        {
            usort($arr_data, function ($a, $b) {
              return ($a['message_count']<$b['message_count']);
            });
        }
        $arr_previous_chat = [];
		$from_user_id = $to_user_id = 0;
		$from_user_name = $to_user_name = $to_user_profile_image ='';

		$from_user_name = $this->login_user_name;

		if($request->has('user_id') && $request->input('user_id')!='')
		{
			$user_id = base64_decode($request->input('user_id'));
			if($user_id!=''){
				
				$from_user_id = $this->login_user_id;
				$to_user_id = intval($user_id);
                
                $obj_to_user_data = $this->UserModel
				    						->select('id','first_name','last_name','profile_image','company_name')
				    						->with('roles')
				    						->where('id',$to_user_id)
				    						->first();

                if(isset($obj_to_user_data->roles[0]->slug) && $obj_to_user_data->roles[0]->slug == 'company')
				{
					$to_user_name = isset($obj_to_user_data->company_name) ? $obj_to_user_data->company_name : '';
				}
				else
				{
					$first_name   = isset($obj_to_user_data->first_name) ? $obj_to_user_data->first_name : '';
					$last_name    = isset($obj_to_user_data->last_name) ? $obj_to_user_data->last_name : '';
					$to_user_name = $first_name.' '.$last_name;
                    $to_user_name = ($to_user_name!=' ') ? $to_user_name : '-';
                    $to_user_profile_image = url('/uploads/default-profile.png');
					if(isset($obj_to_user_data->profile_image) && $obj_to_user_data->profile_image!='' && file_exists($this->user_profile_base_img_path.$obj_to_user_data->profile_image))
                    {
                        $to_user_profile_image = $this->user_profile_public_img_path.$obj_to_user_data->profile_image;
                    }

				}
				$arr_previous_chat = $this->get_previous_chat($from_user_id,$to_user_id);
			}	
		}     

        $page_title                                     = "Manage ".$this->module_title;
        $this->arr_view_data['page_title']              = $page_title;
        $this->arr_view_data['module_title']            = "Manage ".$this->module_title;
        $this->arr_view_data['module_url_path']         = $this->module_url_path;
        $this->arr_view_data['theme_color']             = $this->theme_color;
        $this->arr_view_data['user_role']               = $user_role;
        $this->arr_view_data['login_user_id']           = $this->login_user_id;
        $this->arr_view_data['arr_data']                = $arr_data;
        $this->arr_view_data['arr_previous_chat']       = $arr_previous_chat;
        $this->arr_view_data['from_user_id']            = $from_user_id;
        $this->arr_view_data['to_user_id']              = $to_user_id;
        $this->arr_view_data['from_user_name']          = $from_user_name;
        $this->arr_view_data['to_user_name']            = $to_user_name;
        $this->arr_view_data['to_user_profile_image']   = $to_user_profile_image;
        $this->arr_view_data['from_user_profile_image'] = $from_user_profile_image;

     	// dd($from_user_id,$from_user_name,$to_user_id,$to_user_name);
     	$this->arr_view_data['user_profile_public_img_path'] = $this->user_profile_public_img_path;
     	$this->arr_view_data['user_profile_base_img_path']   = $this->user_profile_base_img_path;

     	

        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }
    public function store_chat(Request $request)
    {
    	$arr_create = [
                        'request_id'   => 0,
                        'from_user_id' => $request->input('from_user_id'),
                        'to_user_id'   => $request->input('to_user_id'),
                        'message'      => $request->input('message'),
                        'is_read'      => 0,
                      ];

        $obj_chat = $this->MessagesModel->create($arr_create);

        $user_type   = $request->input('user_role');
        $to_user_id = $request->input('to_user_id');

        $obj_data = $this->UserModel->select('id','first_name','last_name')->where('id',$to_user_id)->first();

        $first_name         = isset($obj_data->first_name) ? $obj_data->first_name : '';
        $last_name          = isset($obj_data->last_name) ? $obj_data->last_name : '';
        $full_name          = $first_name.' '.$last_name;
        $full_name          = ($full_name!=' ') ? $full_name : '';

        $message = trim($request->input('message'));

        $title = 'New message from QuickPick Admin ('.$full_name.') :'."\n".$message;
        
        $arr_notification_data = 
                                        [
                                            'title'             => $title,
                                            'notification_type' => 'DATABASE_MESSAGE',
                                            'enc_user_id'       => $to_user_id,
                                            'user_type'         => $user_type
                                        ];

        $this->NotificationsService->send_on_signal_notification($arr_notification_data);
        
        $arr_response['status'] = 'success';
        $arr_response['msg'] = 'message send successfully.';
        $arr_response['id'] = isset($obj_chat->id) ? $obj_chat->id :0;

        return $arr_response;
    }
    
    public function get_current_chat_messages(Request $request)
    {
        $from_user_id = $request->input('from_user_id');
        $to_user_id   = $request->input('to_user_id');

        $this->read_unread_message($to_user_id,$from_user_id);
        
        $select_query = '';

        $select_query = "SELECT id,request_id,from_user_id as from_user_id,to_user_id as to_user_id,message,DATE_FORMAT(created_at,'%d %b, %h:%i %p') as date  FROM message WHERE 
                                    (from_user_id = ".$from_user_id." AND to_user_id = ".$to_user_id." )
                                    OR
                                    (from_user_id = ".$to_user_id." AND to_user_id = ".$from_user_id." )  ORDER BY id ASC";   
        
        $arr_chat_details = [];
        if($select_query!='')
        {
            $obj_chat_details =  \DB::select($select_query);

            if(isset($obj_chat_details) && sizeof($obj_chat_details)>0){
                $arr_chat_details = json_decode(json_encode($obj_chat_details), true);
            }
        }
        
        $arr_response = [];

        if(isset($arr_chat_details) && sizeof($arr_chat_details)>0)
        {
            $arr_response['status'] = 'success';
            $arr_response['msg'] = 'chat details available';
            $arr_response['data'] = $arr_chat_details;
            return $arr_response;
        }
        
        $arr_response['status'] = 'error';
        $arr_response['msg'] = 'chat details not available';
        $arr_response['data'] = $arr_chat_details;
        return $arr_response;

    }

    public function get_previous_chat($from_user_id,$to_user_id)
    {
        $select_query = '';

        if($from_user_id!='' &&$to_user_id!='')
        {
            $select_query = "SELECT id,request_id,from_user_id as from_user_id,to_user_id as to_user_id,message,DATE_FORMAT(created_at,'%d %b, %h:%i %p') as date  FROM message WHERE 
                                        (from_user_id = ".$from_user_id." AND to_user_id = ".$to_user_id." )
                                        OR
                                        (from_user_id = ".$to_user_id." AND to_user_id = ".$from_user_id." )  ORDER BY id ASC";   
            
            $arr_chat_details = [];
            if($select_query!='')
            {
                $obj_chat_details =  \DB::select($select_query);

                if(isset($obj_chat_details) && sizeof($obj_chat_details)>0){
                    $arr_chat_details = json_decode(json_encode($obj_chat_details), true);
                }
            }
            return $arr_chat_details;
        }
        return [];
    }
    public function read_unread_message($from_user_id = false,$to_user_id= false)
    {
        if($from_user_id!=false && $to_user_id!=false)
        {
            return $this->MessagesModel
                            ->where('from_user_id',$from_user_id)
                            ->where('to_user_id',$to_user_id)
                            ->update(['is_read'=>'1']);
        }
        return true;
    }
}
