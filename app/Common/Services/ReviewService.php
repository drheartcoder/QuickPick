<?php

namespace App\Common\Services;

use App\Models\ReviewModel;
use App\Models\BookingMasterModel;
use App\Common\Services\CommonDataService;
use App\Common\Services\NotificationsService;


use Validator;

class ReviewService
{
	public function __construct(ReviewModel $review,
                                BookingMasterModel $booking_master,
                                NotificationsService $notifications_service,
                                CommonDataService $common_data_service)
	{
        $this->ReviewModel          = $review;
        $this->BookingMasterModel   = $booking_master;
        $this->CommonDataService    = $common_data_service;
        $this->NotificationsService = $notifications_service;

        $this->review_tag_public_path = url('/').config('app.project.img_path.review_tag');
	}

	public function get_review($user_id)
	{
        $arr_review = [];
		$obj_review = $this->ReviewModel
                                ->with('from_user_details', 'rating_tag_details', 'to_user_details')       
                                ->where('to_user_id',$user_id)
			                     ->orderBy('id','Desc')
			                    ->get();
		if($obj_review)
        {
            $arr_review = $obj_review->toArray();
        }
        return $arr_review;		                    
	}
    public function api_get_review($user_id,$request)
    {
        $arr_review = [];
        $obj_review = $this->ReviewModel
                                ->with(['from_user_details', 'rating_tag_details'])       
                                ->where('to_user_id',$user_id)
                                ->paginate(10);
        if($obj_review)
        {
            $arr_review = $obj_review->toArray();
        }
        return $arr_review;                         
    }
    public function get_review_details($review_id)
    {
    	$obj_review_data = $this->ReviewModel
							->with(['from_user_details', 'rating_tag_details', 'to_user_details']) 
		                    ->where('id',$review_id)
		                    ->first();
        $arr_review = [];
        if($obj_review_data)
        {
            $arr_review = $obj_review_data->toArray(); 
        }                                        
		return $arr_review;
    }
    
    public function store_review($request)
    {
        $from_user_id     = validate_user_jwt_token();
        if ($from_user_id == 0) 
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Invalid driver token';
            return $arr_response;
        }
        
        $arr_rules                 = [];
    	// $arr_rules['to_user_id']   = "required";
        $arr_rules['booking_id']   = "required";
    	$arr_rules['user_type']    = "required";
    	$arr_rules['rating']       = "required";
        $arr_rules['rating_tag_id']= "required";
    	$arr_rules['rating_msg']   = "required";
        
        $validator = Validator::make($request->all(),$arr_rules);
        
        if($validator->fails())
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Please fill all the required field.';
            return $arr_response;
        }

        $user_type     = $request->input('user_type');
        $booking_id    = $request->input('booking_id');
        $rating_tag_id = $request->input('rating_tag_id');
        $rating        = floatval($request->input('rating'));
        $rating_msg    = $request->input('rating_msg');

        $to_user_id    = $this->get_to_user_id_from_booking($booking_id,$user_type);
        $arr_insert =   [
        					'from_user_id'   => $from_user_id,
        					'to_user_id'     => $to_user_id,
        					'user_type'      => $user_type,
                            'booking_id'     => $booking_id,
                            'rating_tag_id'  => $rating_tag_id,
        					'rating'         => $rating,
        					'rating_msg'     => $rating_msg
        				];
        
        $status = $this->ReviewModel->create($arr_insert);
        if($status)
        {
            // $user_details = $this->CommonDataService->get_user_details($to_user_id);

            // $arr_data                 = [];

            // $arr_data['user_id']        = $this->CommonDataService->get_admin_id();
            // $arr_data['user_type']      = 'ADMIN';
            // $arr_data['driver_id']      = $to_user_id;
            // $arr_data['ride_unique_id'] = $ride_unique_id;
            // $arr_data['status']         = 'REQUEST';
            // $arr_data['first_name']     = $user_details['first_name'];
            // $arr_data['last_name']      = $user_details['last_name'];

            // $arr_admin_notification_data = $this->built_notification_data($arr_data,'USER_ADD_REVIEW'); 
            // $this->NotificationsService->store_notification($arr_admin_notification_data);

            // // Send driver notification about review
            // $arr_data['user_id']        = $to_user_id;
            // $arr_data['user_type']      = 'DRIVER';
            // // dd($arr_data);
            // $arr_driver_notification_data = $this->built_notification_data($arr_data,'USER_ADD_REVIEW'); 
            // $this->NotificationsService->store_notification($arr_driver_notification_data);

            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'Review details store successfully.';
            return $arr_response;

        }
        else
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Problem Occurred, While storing review details.';
            return $arr_response;
        }

        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem Occurred, While storing review details.';
        return $arr_response;
    }
    private function get_to_user_id_from_booking($booking_id,$user_type)
    {
        $to_user_id = 0;
        $obj_booking_master = $this->BookingMasterModel
                                            ->select('id','load_post_request_id')
                                            ->whereHas('load_post_request_details',function($query){

                                            })
                                            ->with(['load_post_request_details'=>function($query){
                                                $query->select('id','driver_id','user_id');
                                            }])
                                            ->where('id',$booking_id)
                                            ->first();
        if($obj_booking_master)
        {
            if($user_type == 'USER'){
                $to_user_id = isset($obj_booking_master->load_post_request_details->driver_id) ? $obj_booking_master->load_post_request_details->driver_id :0;
            }
            if($user_type == 'DRIVER'){
                $to_user_id = isset($obj_booking_master->load_post_request_details->user_id) ? $obj_booking_master->load_post_request_details->user_id :0;
            }
        }

        return $to_user_id;
    }
    private function built_notification_data($arr_data,$type)
    {
        $arr_notification = [];
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $first_name = $last_name = $full_name = '';

            $arr_notification['user_id']           = $arr_data['user_id'];
            $arr_notification['is_read']           = 0;
            $arr_notification['is_show']           = 0;
            $arr_notification['user_type']         = $arr_data['user_type'];

            $first_name = isset($arr_data['first_name']) ? $arr_data['first_name'] :'';
            $last_name  = isset($arr_data['last_name']) ? $arr_data['last_name'] :'';
            $full_name  = $first_name.' '.$last_name;
            $full_name  = ($full_name!=' ') ? $full_name : '-';

            $ride_unique_id = isset($arr_data['ride_unique_id']) ? $arr_data['ride_unique_id'] : 0;
            $driver_id = isset($arr_data['driver_id']) ? $arr_data['driver_id'] : 0;

            $arr_notification['notification_type'] = 'Driver Added Review';
            $arr_notification['title']             = $full_name.' added a review for a ride '.$ride_unique_id.' of '.config('app.project.name').'.';
            $arr_notification['view_url']          = '/'.config('app.project.admin_panel_slug')."/driver/review/".base64_encode($arr_data['driver_id']);

        }
        return $arr_notification;
    }
}
?>