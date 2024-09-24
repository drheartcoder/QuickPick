<?php 
namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\UserModel;
use App\Models\BookingMasterModel;

use App\Common\Services\NotificationsService; 
use App\Common\Services\EmailService; 

use Validator;
use Session;
use Input;
use Auth;
 
class CommonDataController extends Controller
{
    public function __construct(
                                    UserModel $user,
                                    BookingMasterModel $booking_master,
                                    NotificationsService $notifications_service,
                                    EmailService $emailservice
                                )
    {   
        $this->UserModel            = $user;
        $this->BookingMasterModel   = $booking_master;
        $this->NotificationsService = $notifications_service;
        $this->EmailService         = $emailservice;

        $this->driving_license_public_path = url('/').config('app.project.img_path.driving_license');
        $this->driving_license_base_path   = base_path().config('app.project.img_path.driving_license');

        $this->vehicle_doc_public_path = url('/').config('app.project.img_path.vehicle_doc');
        $this->vehicle_doc_base_path   = base_path().config('app.project.img_path.vehicle_doc');

        $this->invoice_public_img_path = url('/').config('app.project.img_path.invoice');
        $this->invoice_base_img_path   = base_path().config('app.project.img_path.invoice');

    }
    public function download_document(Request $request)
    {
        $driver_id     = base64_decode($request->input('driver_id'));
        $vehicle_id    = base64_decode($request->input('vehicle_id'));
        $document_type = $request->input('document_type');

        if($document_type == 'driving_license')
        {
            $obj_driver_data = $this->UserModel->where('id',$driver_id)->first();
            if($obj_driver_data)
            {
                if(isset($obj_driver_data->driving_license) && $obj_driver_data->driving_license!=''){
                    $tmp_driving_license = isset($obj_driver_data->driving_license) ? $obj_driver_data->driving_license :'';
                    if(file_exists($this->driving_license_base_path.$tmp_driving_license))
                    {
                        $driving_license = $this->driving_license_base_path.$tmp_driving_license;
                        return \Response::download($driving_license);    
                    }   
                }
            }
        }
        \Flash::error('Document details not found,Please try again.');
        return redirect()->back();

    }
    public function send_invoice_email(Request $request)
    {
        $booking_id   = $request->input('booking_id');
        $type         = $request->input('type');
        $request_type = $request->input('request_type');

        if($type == 'BOTH')
        {
            /*send invoice email to user*/
            $arr_user_invoice_email_data = $this->built_user_invoice_email_data($booking_id); 
            $this->EmailService->send_mail_with_attachments($arr_user_invoice_email_data);
            
            /*send invoice email to driver*/
            $arr_driver_invoice_email_data = $this->built_driver_invoice_email_data($booking_id); 
            $this->EmailService->send_mail_with_attachments($arr_driver_invoice_email_data);
        }
        else if($type == 'USER')
        {
            /*send invoice email to user*/
            $arr_user_invoice_email_data = $this->built_user_invoice_email_data($booking_id); 
            $this->EmailService->send_mail_with_attachments($arr_user_invoice_email_data);
        }
        else if($type == 'DRIVER')
        {
            /*send invoice email to driver*/
            $arr_driver_invoice_email_data = $this->built_driver_invoice_email_data($booking_id); 
            $this->EmailService->send_mail_with_attachments($arr_driver_invoice_email_data);
        }
        if($request_type == 'API')
        {
            return response()->json(['status'=>'success','msg'=>'Invoice email sent successfully.']);
        }

        \Flash::success('Invoice email sent successfully.');
        return redirect()->back();
    }
    private function built_user_invoice_email_data($booking_id)
    {
        $arr_booking_details = $this->get_booking_details($booking_id);
        
        $arr_data = filter_completed_trip_details($arr_booking_details);
        
        $distance = isset($arr_data['distance']) ? number_format($arr_data['distance']) :'0';
        $distance = $distance.' Miles';
        
        $time_taken = isset($arr_data['total_minutes_trip']) ? number_format($arr_data['total_minutes_trip']) : '0';
        $time_taken = $time_taken.' Min.';

        $discount_amount = isset($arr_data['applied_promo_code_charge']) ? number_format($arr_data['applied_promo_code_charge']) : '0';
        $discount_amount = $discount_amount.' USD';

        $total_amount = isset($arr_data['total_charge']) ? number_format($arr_data['total_charge']) : '0';
        $total_amount = $total_amount.' USD';

        $invoice_attachment = '';
        $invoice_attachment_name = 'TRIP_INVOICE_'.$booking_id.'.pdf';
        
        if(file_exists($this->invoice_base_img_path.$invoice_attachment_name))
        {
            $invoice_attachment = $this->invoice_base_img_path.$invoice_attachment_name;
        }

        $arr_built_content = [

            'FULL_NAME'            => isset($arr_data['user_name']) ? $arr_data['user_name'] : '',
            'BOOKING_ID'           => isset($arr_data['booking_unique_id']) ? $arr_data['booking_unique_id'] : '',
            'PICKUP_LOCATION'      => isset($arr_data['pickup_location']) ? $arr_data['pickup_location'] : '',
            'DROP_LOCATION'        => isset($arr_data['drop_location']) ? $arr_data['drop_location'] :'',
            'DISTANCE'             => $distance,
            'TIME_TAKEN'           => $time_taken,
            'DISCOUNT_AMOUNT'      => $discount_amount,
            'TOTAL_AMOUNT'         => $total_amount,
            'PROJECT_NAME'         => config('app.project.name')];

            $arr_mail_data                      = [];
            $arr_mail_data['email_template_id'] = '19';
            $arr_mail_data['arr_built_content'] = $arr_built_content;
            $arr_mail_data['attachment']        = $invoice_attachment;
            $arr_mail_data['user']              = ['email'=>isset($arr_data['user_email']) ? $arr_data['user_email'] : ''];

        return $arr_mail_data;
    }

    private function built_driver_invoice_email_data($booking_id)
    {
        $arr_booking_details = $this->get_booking_details($booking_id);
        
        $arr_data = filter_completed_trip_details($arr_booking_details);

        $distance = isset($arr_data['distance']) ? number_format($arr_data['distance']) :'0';
        $distance = $distance.' Miles';
        
        $time_taken = isset($arr_data['total_minutes_trip']) ? number_format($arr_data['total_minutes_trip']) : '0';
        $time_taken = $time_taken.' Min.';

        $discount_amount = isset($arr_data['applied_promo_code_charge']) ? number_format($arr_data['applied_promo_code_charge']) : '0';
        $discount_amount = $discount_amount.' USD';

        $total_amount = isset($arr_data['total_charge']) ? number_format($arr_data['total_charge']) : '0';
        $total_amount = $total_amount.' USD';

        $invoice_attachment = '';
        $invoice_attachment_name = 'TRIP_INVOICE_'.$booking_id.'.pdf';
        
        if(file_exists($this->invoice_base_img_path.$invoice_attachment_name))
        {
            $invoice_attachment = $this->invoice_base_img_path.$invoice_attachment_name;
        }

        $arr_built_content = [

            'FULL_NAME'            => isset($arr_data['driver_name']) ? $arr_data['driver_name'] : '',
            'BOOKING_ID'           => isset($arr_data['booking_unique_id']) ? $arr_data['booking_unique_id'] : '',
            'PICKUP_LOCATION'      => isset($arr_data['pickup_location']) ? $arr_data['pickup_location'] : '',
            'DROP_LOCATION'        => isset($arr_data['drop_location']) ? $arr_data['drop_location'] :'',
            'DISTANCE'             => $distance,
            'TIME_TAKEN'           => $time_taken,
            'DISCOUNT_AMOUNT'      => $discount_amount,
            'TOTAL_AMOUNT'         => $total_amount,
            'PROJECT_NAME'         => config('app.project.name')];

            $arr_mail_data                      = [];
            $arr_mail_data['email_template_id'] = '20';
            $arr_mail_data['arr_built_content'] = $arr_built_content;
            $arr_mail_data['attachment']        = $invoice_attachment;
            $arr_mail_data['user']              = ['email'=>isset($arr_data['driver_email']) ? $arr_data['driver_email'] : ''];

        return $arr_mail_data;
    }

    private function get_booking_details($booking_id)
    {
        $arr_booking_master = [];
        $obj_booking_master = $this->BookingMasterModel
                                            // ->with(['load_post_request_details'=> function($query){
                                            //         $query->select('id','user_id','driver_id','pickup_location','drop_location','pickup_lat','pickup_lng','drop_lat','drop_lng','request_status','load_post_image');
                                            //         $query->with(['driver_details'=>function($query){
                                            //                     $query->select('id','first_name','last_name','email','mobile_no','profile_image','stripe_account_id','company_id','is_company_driver');
                                            //                     $query->with('company_details');
                                            //                 }]);
                                            //         $query->with(['user_details'=>function($query){
                                            //                     $query->select('id','first_name','last_name','email','mobile_no','profile_image');
                                            //                 }]);
                                            // }])
                                            ->with(['load_post_request_details'=> function($query){
                                                    $query->select('id','user_id','driver_id','vehicle_id','pickup_location','drop_location','pickup_lat','pickup_lng','drop_lat','drop_lng','request_status','load_post_image');
                                                    $query->with(['driver_details'=>function($query){
                                                                $query->select('id','first_name','last_name','email','mobile_no','profile_image','stripe_account_id','company_id','is_company_driver');
                                                                $query->with('company_details');
                                                            }]);
                                                    $query->with(['user_details'=>function($query){
                                                                $query->select('id','first_name','last_name','email','mobile_no','profile_image');
                                                            }]);
                                                    $query->with(['vehicle_details'=>function($query){
                                                                $query->with('vehicle_type_details');
                                                            }]);
                                                    $query->with(['load_post_request_package_details'=>function($query){
                                                            }]);
                                                    
                                            }])
                                            ->where('id',$booking_id)
                                            ->first();
        if($obj_booking_master)
        {
            $arr_booking_master = $obj_booking_master->toArray();
        }
        return $arr_booking_master;
        
    }
    public function change_notification_status(Request $request)
    {
        $notification_id = $request->input('notification_id');
        $notification_id = base64_decode($notification_id);

        $this->NotificationsService->update_notification_status($notification_id);
        return response()->json(['status'=>'success']);
    }
    public function get_new_notification(Request $request)
    {
        $user_id   = base64_decode($request->input('user_id'));
        $user_type = $request->input('user_role');

        $arr_where = 
                        [
                            'user_id'   => $user_id,
                            'user_type' => $user_type,
                            'is_read'   => 0
                        ];

        $arr_notifications = $this->NotificationsService->get_unread_notification($arr_where);
        
        $unread_message_count = get_unread_message_count($user_id);

        return response()->json(['status'=>'success','arr_notifications' => $arr_notifications,'unread_message_count'=>$unread_message_count]);

    }
    public function send_notification(Request $request)
    {
        $enc_user_type     = $request->input('enc_user_type');
        $enc_user_id       = $request->input('enc_user_id');
        $notification_type = $request->input('notification_type');
        $message           = $request->input('message');

        if($notification_type == 'single'){
            
            $enc_user_id = base64_decode($enc_user_id);

            /*Send on signal notification to user that driver accepted your request*/
            $arr_notification_data = 
                                    [
                                        'title'             => trim($message),
                                        'record_id'         => 0,
                                        'enc_user_id'       => $enc_user_id,
                                        'notification_type' => 'PROMOCODE',
                                        'user_type'         => $enc_user_type,
                                    ];

            $this->NotificationsService->send_on_signal_notification($arr_notification_data);

            \Flash::success('Notification successfully broadcast to user.');
            return redirect()->back();

        }
        else if($notification_type == 'multiple'){
            
            $arr_enc_user = explode(',',$enc_user_id);
            if(isset($arr_enc_user) && sizeof($arr_enc_user)>0)
            {
                foreach ($arr_enc_user as $key => $value) 
                {
                    $enc_tmp_id = base64_decode($value);
                    
                    $arr_notification_data = 
                                            [
                                                'title'             => trim($message),
                                                'record_id'         => 0,
                                                'enc_user_id'       => $enc_tmp_id,
                                                'notification_type' => 'PROMOCODE',
                                                'user_type'         => $enc_user_type,
                                            ];
                    $this->NotificationsService->send_on_signal_notification($arr_notification_data);

                }
            }
            \Flash::success('Notification successfully broadcast to users.');
            return redirect()->back();
        }
        \Flash::success('Notification successfully Broadcast to users.');
        return redirect()->back();
    }   
}