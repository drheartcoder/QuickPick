<?php

namespace App\Common\Services;

use App\Models\NotificationsModel;

class NotificationsService
{
    public function __construct(NotificationsModel $notifications)
    {
        $this->DriverOneSignalApiKey = config('app.project.one_signal_credentials.driver_api_key');
        $this->DriverOneSignalAppId  = config('app.project.one_signal_credentials.driver_app_id');

        $this->UserOneSignalApiKey   = config('app.project.one_signal_credentials.user_api_key');
        $this->UserOneSignalAppId    = config('app.project.one_signal_credentials.user_app_id');

        $this->WebOneSignalApiKey   = config('app.project.one_signal_credentials.website_api_key');
        $this->WebOneSignalAppId    = config('app.project.one_signal_credentials.website_app_id');

        $this->NotificationsModel = $notifications;
    }
    public function store_notification($arr_data)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
        	$this->NotificationsModel->create($arr_data);
        	return true;
        }
        return false;
    }
    public function update_notification_status($notification_id)
    {
        $this->NotificationsModel->where('id',$notification_id)->update(['is_read'=>1]);
        return true;
    }
    public function get_unread_notification($arr_where)
    {
        $arr_notifications = [];
        if(isset($arr_where) && sizeof($arr_where)>0)
        {
            $obj_notifications = NotificationsModel::select('id','notification_type','title','view_url')
                                                    ->where($arr_where)
                                                    ->orderBy('id','DESC')
                                                    ->get();
            if($obj_notifications)
            {
                $arr_notifications = $obj_notifications->toArray();
            }
            return $arr_notifications;
        }
        return $arr_notifications;
    }
    
    public function send_on_signal_notification($arr_notification_data)
    {                                           
        if(isset($arr_notification_data)){
            
            $user_type = isset($arr_notification_data['user_type']) ? $arr_notification_data['user_type'] :'';
            
            if($user_type == '')
            {
                return FALSE;
            }

            $OneSignalAppId = $OneSignalApiKey = '';
            
            if($user_type == 'USER')
            {
                $OneSignalAppId  = $this->UserOneSignalAppId;
                $OneSignalApiKey = $this->UserOneSignalApiKey;
            }

            if($user_type == 'DRIVER')
            {
                $OneSignalAppId  = $this->DriverOneSignalAppId;
                $OneSignalApiKey = $this->DriverOneSignalApiKey;
            }

            if($user_type == 'WEB')
            {
                $OneSignalAppId  = $this->WebOneSignalAppId;
                $OneSignalApiKey = $this->WebOneSignalApiKey;
            }


            $custom_data =  [
                                "app_id"            => $OneSignalAppId ,
                                "record_id"         => isset($arr_notification_data['record_id']) ? intval($arr_notification_data['record_id']) :0,
                                "is_admin_assign"   => isset($arr_notification_data['is_admin_assign']) ? $arr_notification_data['is_admin_assign'] :'0',
                                "notification_type" => isset($arr_notification_data['notification_type']) ? $arr_notification_data['notification_type'] :''
                            ];
            $title       = isset($arr_notification_data['title']) ? $arr_notification_data['title'] :'';
            $enc_user_id = isset($arr_notification_data['enc_user_id']) ? $arr_notification_data['enc_user_id'] :0;

          
            $filters     = array();
            $filters = array(array("field" => "tag", "key" => "active_user_id", "relation" => "=", "value" => $enc_user_id));
            
            if ($OneSignalAppId!='' && $OneSignalApiKey!='')
            {
                $fields = array(    
                                    'app_id'            => $OneSignalAppId,
                                    'headings'          => array("en" => 'Quick-Pick'),
                                    'filters'           => $filters,
                                    'data'              => $custom_data,
                                    'contents'          => array("en" => $title),
                                    'content_available' => true,
                                    'ios_badgeType'     => 'Increase',
                                    'ios_badgeCount'    => '1',
                                    'priority'          => 10,
                                );
                 
                $fields = json_encode($fields);
                 
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8','Authorization: Basic '.$OneSignalApiKey));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                $response = curl_exec($ch);
                
                curl_close($ch);
                return true;

            }
        }
        return true;
    }
}