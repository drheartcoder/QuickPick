<?php
use App\Models\UserModel;
use Tymon\JWTAuth\Exceptions\JWTException;


use App\Models\BookingMasterModel;
use App\Models\LoadPostRequestModel;
use App\Models\NotificationsModel;
use App\Models\ReviewModel;
use App\Models\MessagesModel;


	function validate_user_jwt_token()
	{
		try
		{
			$user  = JWTAuth::parseToken()->authenticate(); 

			if ($user && isset($user->id)) 
			{
				return $user->id;
			}
			else
			{
				return 0;
			}
		}
		catch(JWTException $e)
		{
			return 0;
		}
		return 0;
	}

  function get_jwt_user_details()
  {
    try
    {
      $user  = JWTAuth::parseToken()->authenticate(); 
      
      if (isset($user) && count($user)>0) 
      {
        return $user->toArray();
      }
      else
      {
        return [];
      }
    }
    catch(JWTException $e)
    {
      return [];
    }
    return [];
  }

  function validate_user_login_id()
  {
      try
      {
          $obj_user =  \Sentinel::check();
          if($obj_user!=false)
          {
              if($obj_user->inRole('user') || $obj_user->inRole('driver') || $obj_user->inRole('enterprise_admin'))
              {
                  return isset($obj_user->id) ? $obj_user->id : 0;
              }
          }
          return 0;
      } 
      catch(\Exception $e)
      {
        return 0;
      }
      return 0;
  }

  function get_login_user_details()
  {
      try
      {
          $obj_user =  \Sentinel::check();
          if($obj_user!=false)
          {
              if($obj_user->inRole('user') || $obj_user->inRole('driver') || $obj_user->inRole('enterprise_admin'))
              {
                  return (isset($obj_user) && count($obj_user)>0) ? $obj_user->toArray() : [];
              }
          }
          return [];
      } 
      catch(\Exception $e)
      {
        return [];
      }
      return [];
  }

  function get_right_bar_trip_list()
  {
      $user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');
      $user_profile_base_img_path  = base_path().config('app.project.img_path.user_profile_images');

      $enc_user_id = validate_user_login_id();
      // dd($enc_user_id);
      $arr_pending_trips = $arr_ongoing_trips = [];
      $obj_ongoing_trips  = BookingMasterModel::
                                    select('id','load_post_request_id','booking_unique_id','booking_date','booking_status','total_charge','updated_at')
                                    ->whereHas('load_post_request_details',function($query) use($enc_user_id) {
                                            $query->where('request_status','ACCEPT_BY_USER');
                                            $query->where('user_id',$enc_user_id);
                                    })
                                    ->with(['load_post_request_details'=> function($query) use($enc_user_id) {
                                            $query->select('id','user_id','driver_id','pickup_location','drop_location','pickup_lat','pickup_lng','drop_lat','drop_lng','request_status','load_post_image');
                                            $query->where('request_status','ACCEPT_BY_USER');
                                            $query->where('user_id',$enc_user_id);
                                             $query->with(['driver_details'=>function($query){
                                                        $query->select('id','first_name','last_name','email','country_code','mobile_no','profile_image');
                                                    }]);
                                            $query->with(['load_post_request_package_details'=>function($query){
                                                    }]);
                                    }])
                                    ->whereIn('booking_status',['TO_BE_PICKED','IN_TRANSIT'])
                                    ->get();
      if($obj_ongoing_trips)
      {
        $arr_ongoing_trips = $obj_ongoing_trips->toArray();
      }
      
      // dump($arr_ongoing_trips);

      if(isset($arr_ongoing_trips) && sizeof($arr_ongoing_trips)>0){
        foreach ($arr_ongoing_trips as $key => $value) 
        {
            $arr_ongoing_trips[$key]['booking_status'] = isset($value['booking_status']) ? $value['booking_status'] : '';
            $arr_ongoing_trips[$key]['booking_date']   = isset($value['booking_date']) ? date('d M Y',strtotime($value['booking_date'])) : '';
            $arr_ongoing_trips[$key]['first_name']     = isset($value['load_post_request_details']['driver_details']['first_name']) ? $value['load_post_request_details']['driver_details']['first_name'] :'';
            $arr_ongoing_trips[$key]['last_name']      = isset($value['load_post_request_details']['driver_details']['last_name']) ? $value['load_post_request_details']['driver_details']['last_name'] :'';
            $profile_image = url('/uploads/default-profile.png');
            if(isset($value['load_post_request_details']['driver_details']['profile_image']) && $value['load_post_request_details']['driver_details']['profile_image']!=''){
                if(file_exists($user_profile_base_img_path.$value['load_post_request_details']['driver_details']['profile_image'])){
                    $profile_image = $user_profile_public_img_path.$value['load_post_request_details']['driver_details']['profile_image'];
                }
            }
            $arr_ongoing_trips[$key]['profile_image']     = $profile_image;

            $country_code   = isset($value['load_post_request_details']['driver_details']['country_code']) ? $value['load_post_request_details']['driver_details']['country_code'] : '';
            $mobile_no      = isset($value['load_post_request_details']['driver_details']['mobile_no']) ? $value['load_post_request_details']['driver_details']['mobile_no'] : '';
            $full_mobile_no = $country_code.''.$mobile_no;
            $full_mobile_no = ($full_mobile_no!='')?$full_mobile_no :'';
            
            $arr_ongoing_trips[$key]['mobile_no']         = $full_mobile_no;
            $arr_ongoing_trips[$key]['pickup_location']   = isset($value['load_post_request_details']['pickup_location']) ? $value['load_post_request_details']['pickup_location'] :'';
            $arr_ongoing_trips[$key]['drop_location']     = isset($value['load_post_request_details']['drop_location']) ? $value['load_post_request_details']['drop_location'] :'';
            $arr_ongoing_trips[$key]['pickup_lat']        = isset($value['load_post_request_details']['pickup_lat']) ? doubleval($value['load_post_request_details']['pickup_lat']) :doubleval(0.0);
            $arr_ongoing_trips[$key]['pickup_lng']        = isset($value['load_post_request_details']['pickup_lng']) ? doubleval($value['load_post_request_details']['pickup_lng']) :doubleval(0.0);
            $arr_ongoing_trips[$key]['drop_lat']          = isset($value['load_post_request_details']['drop_lat']) ? doubleval($value['load_post_request_details']['drop_lat']) :doubleval(0.0);
            $arr_ongoing_trips[$key]['drop_lng']          = isset($value['load_post_request_details']['drop_lng']) ? doubleval($value['load_post_request_details']['drop_lng']) :doubleval(0.0);
    
            unset($arr_ongoing_trips[$key]['review_details']);
            unset($arr_ongoing_trips[$key]['load_post_request_details']);
        }
      }
    
      $obj_pending_trips  = LoadPostRequestModel::
                                        with('load_post_request_package_details')
                                        ->select("id","user_id","driver_id","date","pickup_location","drop_location","pickup_lat","pickup_lng","drop_lat","drop_lng","request_status","is_future_request","is_request_process")
                                        ->whereIn('request_status',['USER_REQUEST','TIMEOUT','ACCEPT_BY_DRIVER','REJECT_BY_DRIVER','REJECT_BY_USER'])
                                        ->where('user_id',$enc_user_id)
                                        ->orderBy('id','DESC')
                                        ->get();

      if($obj_pending_trips)
      {
        $arr_pending_trips = $obj_pending_trips->toArray();
      }

      if(isset($arr_pending_trips) && sizeof($arr_pending_trips)>0)
      {
          foreach ($arr_pending_trips as $key => $value) 
          {
              $booking_status = isset($value['request_status']) ? $value['request_status'] : '';
              $is_future_request = isset($value['is_future_request']) ? $value['is_future_request'] : '0';
              $is_request_process = isset($value['is_request_process']) ? $value['is_request_process'] : '0';

              $first_name = '';
              if($booking_status == 'NEW_REQUEST'){
                  $first_name = 'Please Select Driver';
              }
              else if($booking_status == 'USER_REQUEST' && $is_future_request == '0'){
                  $first_name = 'Waiting for Driver to Accept';
              }
              else if($booking_status == 'USER_REQUEST' && $is_future_request == '1'){
                if($is_request_process == '0'){
                  $first_name = 'Future Booking Request';
                }
                else if($is_request_process == '1'){
                  $first_name = 'Waiting for Driver to Accept';
                }
              }
              else if($booking_status == 'ACCEPT_BY_DRIVER'){
                  $first_name = 'Please respond to driver';
              }
              else if($booking_status == 'REJECT_BY_DRIVER'){
                  $first_name = 'Waiting for Driver to Accept';
              }
              else if($booking_status == 'REJECT_BY_USER'){
                  $first_name = 'Waiting for Driver to Accept';
              }
              else if($booking_status == 'TIMEOUT'){
                  $first_name = 'Waiting for Driver to Accept';
              }

              $profile_image                                   = url('/uploads/listing-default-logo.png');
              $arr_pending_trips[$key]['id']                   = isset($value['id']) ? $value['id'] :0;
              $arr_pending_trips[$key]['load_post_request_id'] = isset($value['id']) ? $value['id'] :'';
              $arr_pending_trips[$key]['driver_id']            = isset($value['driver_id']) ? $value['driver_id'] :0;
              $arr_pending_trips[$key]['booking_unique_id']    = '';
              $arr_pending_trips[$key]['booking_status']       = $booking_status;
              $arr_pending_trips[$key]['booking_date']         = isset($value['date']) ? date('d M Y',strtotime($value['date'])) : '';
              $arr_pending_trips[$key]['first_name']           = $first_name;
              $arr_pending_trips[$key]['last_name']            = '';
              $arr_pending_trips[$key]['profile_image']        = $profile_image;
              $arr_pending_trips[$key]['mobile_no']            = '';
              $arr_pending_trips[$key]['pickup_location']      = isset($value['pickup_location']) ? $value['pickup_location'] :'';
              $arr_pending_trips[$key]['drop_location']        = isset($value['drop_location']) ? $value['drop_location'] :'';
              $arr_pending_trips[$key]['pickup_lat']           = isset($value['pickup_lat']) ? doubleval($value['pickup_lat']) :doubleval(0.0);
              $arr_pending_trips[$key]['pickup_lng']           = isset($value['pickup_lng']) ? doubleval($value['pickup_lng']) :doubleval(0.0);
              $arr_pending_trips[$key]['drop_lat']             = isset($value['drop_lat']) ? doubleval($value['drop_lat']) :doubleval(0.0);
              $arr_pending_trips[$key]['drop_lng']             = isset($value['drop_lng']) ? doubleval($value['drop_lng']) :doubleval(0.0);
              
              unset($arr_pending_trips[$key]['date']);
              unset($arr_pending_trips[$key]['load_post_request_package_details']);
           
          }
      }

      $arr_result['arr_ongoing_trips'] = $arr_ongoing_trips;
      $arr_result['arr_pending_trips'] = $arr_pending_trips;
      return $arr_result;
  }
  function get_onsignal_login_user_details()
  {
      $arr_login_user_details = [];
      $user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');
      $user_profile_base_img_path  = base_path().config('app.project.img_path.user_profile_images');
      try {
          $user = \Sentinel::check();
          if($user)
          {   
              $user_type                           = '';
              $arr_login_user_details['user_id']   = isset($user->id) ? $user->id :0;
              $arr_login_user_details['email']     = isset($user->email) ? $user->email :'';
              $arr_login_user_details['mobile_no'] = isset($user->mobile_no) ? $user->mobile_no :'';
              
              if(isset($user->profile_image) && $user->profile_image!='' && file_exists($user_profile_base_img_path.$user->profile_image))
              {
                  $arr_login_user_details['profile_image'] = $user_profile_public_img_path.$user->profile_image;
              }
              
              $arr_login_user_details['via_social'] = isset($user->via_social) ? $user->via_social :'0';

              $first_name = isset($user->first_name) ? $user->first_name :'';
              $last_name  = isset($user->last_name) ? $user->last_name :'';
              $full_name  = $first_name.' '.$last_name;
              $full_name  = ($full_name!=' ')?$full_name : '-';

              $arr_login_user_details['full_name'] = $full_name;

              if($user->inRole('user'))
              {   
                  $user_type = 'user';
              }
              else if($user->inRole('driver'))
              {   
                  $user_type = 'driver';
              }
              
              $arr_login_user_details['user_type'] = $user_type;
              $arr_login_user_details['oneSignalAppId'] = config('app.project.one_signal_credentials.website_app_id');
          }  
      } 
      catch(\Exception $e)
      {
        return $arr_login_user_details;
      }
      return $arr_login_user_details;
  }

    function getDateFormat($date)
    {
        if($date!="0000-00-00" || $date=null)
        {
            $new_date_format = $date;
            $new_date_format = date_create($new_date_format);
            $new_date_format = date_format($new_date_format,'d-m-Y');
            return ($new_date_format);    
        }
        else
        {
            return "";
        }
        
    }

    function get_unread_message_count($to_user_id = false)
    {
      return MessagesModel::where('to_user_id',$to_user_id)->where('is_read','0')->count();

    }

    function get_trip($trip_type,$user_type)
    {
        $enc_user_id     = validate_user_login_id();

        if($trip_type!='' && $enc_user_id!='')
        {

          if($trip_type == 'PENDING')
          {
              $obj_trips  = LoadPostRequestModel::
                                          with('load_post_request_package_details')
                                          ->select("id","date","pickup_location","drop_location","pickup_lat","pickup_lng","drop_lat","drop_lng","load_post_image")
                                          ->whereIn('request_status',['USER_REQUEST','TIMEOUT','REJECT_BY_DRIVER'])
                                          ->where('user_id',$enc_user_id)
                                          ->orderBy('id','DESC')
                                          ->count();

              return $obj_trips;  
          }

          $obj_trips  = BookingMasterModel::select('id','load_post_request_id','booking_unique_id','booking_date','booking_status','total_charge')
                                    ->whereHas('load_post_request_details',function($query) use($enc_user_id,$user_type) {
                                            // $query->whereHas('driver_details',function($query){
                                            // });
                                            // $query->whereHas('user_details',function($query){
                                            // });
                                            // $query->whereHas('driver_current_location_details',function($query){
                                            // });
                                            $query->where('request_status','ACCEPT_BY_USER');
                                            if($user_type == 'USER'){
                                                $query->where('user_id',$enc_user_id);
                                            }
                                            else if($user_type == 'DRIVER'){
                                                $query->where('driver_id',$enc_user_id);
                                            }
                                            
                                    })
                                    ->with(['load_post_request_details'=> function($query) use($enc_user_id,$user_type) {
                                            $query->select('id','user_id','driver_id','pickup_location','drop_location','pickup_lat','pickup_lng','drop_lat','drop_lng','request_status','load_post_image');
                                            $query->where('request_status','ACCEPT_BY_USER');
                                            //$query->where('driver_id',$enc_user_id);
                                            
                                            if($user_type == 'USER'){
                                                $query->where('user_id',$enc_user_id);
                                            }
                                            else if($user_type == 'DRIVER'){
                                                $query->where('driver_id',$enc_user_id);
                                            }

                                            $query->with(['driver_details'=>function($query){
                                                        $query->withTrashed();
                                                        $query->select('id','first_name','last_name','email','country_code','mobile_no','profile_image');
                                                    }]);
                                            $query->with(['user_details'=>function($query){
                                                        $query->withTrashed();
                                                        $query->select('id','first_name','last_name','email','country_code','mobile_no','profile_image');
                                                    }]);
                                            $query->with(['driver_current_location_details'=>function($query){
                                                        $query->select('id','driver_id','status','current_latitude','current_longitude');
                                                    },'load_post_request_package_details']);
                                    },'review_details'=>function($query) use($enc_user_id){
                                        $query->where('from_user_id',$enc_user_id);
                                    }]);

              if($trip_type == 'ONGOING')
              {
                  $obj_trips = $obj_trips->whereIn('booking_status',['TO_BE_PICKED','IN_TRANSIT']);
              }
              else if($trip_type == 'COMPLETED')
              {
                  $obj_trips = $obj_trips->where('booking_status','COMPLETED');
              }
              else if($trip_type == 'CANCELED')
              {
                  $obj_trips = $obj_trips->whereIn('booking_status',['CANCEL_BY_USER','CANCEL_BY_DRIVER','CANCEL_BY_ADMIN']);
                  $obj_trips = $obj_trips->count();

                  $obj_cancel_load_post  = LoadPostRequestModel::
                                            with(['load_post_request_package_details',
                                                    'driver_details'=>function($query){
                                                            $query->select('id','first_name','last_name','email','country_code','mobile_no','profile_image');
                                                        },
                                                    'user_details'=>function($query){
                                                            $query->select('id','first_name','last_name','email','country_code','mobile_no','profile_image');
                                                        },
                                                    'driver_current_location_details'=>function($query){
                                                            $query->select('id','driver_id','status','current_latitude','current_longitude');
                                                        }
                                                    ])
                                                ->select("id","user_id","driver_id","date as booking_date","pickup_location","drop_location","pickup_lat","pickup_lng","drop_lat","drop_lng","load_post_image","request_status as booking_status");

                  if($user_type == 'USER')
                  {
                      $obj_cancel_load_post = $obj_cancel_load_post->where('user_id',$enc_user_id);
                      $obj_cancel_load_post = $obj_cancel_load_post->whereIn('request_status',['CANCEL_BY_USER','CANCEL_BY_ADMIN']);
                  }
                  if($user_type == 'DRIVER')
                  {
                      $obj_cancel_load_post = $obj_cancel_load_post
                                                      ->whereHas('load_post_request_history_details',function($query) use ($enc_user_id) {
                                                          $query->where('driver_id',$enc_user_id);
                                                          $query->whereIn('status',['REJECT_BY_DRIVER','REJECT_BY_USER']);
                                                      }); 
                  }
                  $obj_cancel_load_post = $obj_cancel_load_post
                                            ->count();

                  return $obj_cancel_load_post + $obj_trips;
              }
              $obj_trips = $obj_trips->count();

              return $obj_trips;
        }
        else
        {
            return 0;
        }
        
    }

    function get_notification($user_type)
    {
        $user_id     = validate_user_login_id();
        
        if($user_type!='' && $user_id!='')
        {
            $arr_notifications = [];

            $obj_notifications = NotificationsModel::select('id','notification_type','title','description','created_at')
                                ->where('user_id',$user_id);
                          // ->where('is_read',0)
            
            if($user_type=="USER")
            {
              $obj_notifications = $obj_notifications->where('user_type','RIDER');
            }

            if($user_type=="DRIVER")
            {
              $obj_notifications = $obj_notifications->where('user_type','DRIVER');
            }
      
          if($obj_notifications)
          {
            $arr_notifications = $obj_notifications->count();
              return ($arr_notifications);    
          }

        }
        else
        {
            return "";
        }
        
    }

    function get_review_ratings()
    {
        $user_id     = validate_user_login_id();
        
        if($user_id!='')
        {
            $arr_review = [];
            $obj_review = ReviewModel::with('from_user_details', 'rating_tag_details', 'to_user_details')       
                                       ->where('to_user_id',$user_id)
                                       ->orderBy('id','Desc')
                                       ->get();
            if($obj_review)
            {
                return $obj_review->count();
            }

        }
        return 0;
        
        
    }


  function get_admin_id()
  {
    $obj_user = UserModel::select('id')
                          ->whereHas('roles',function($query){
                              $query->where('slug','admin');
                          })
                          ->first();

    return isset($obj_user->id) ? $obj_user->id :0;

  }

  function get_admin_details()
  {
    $obj_user = UserModel::select('id','first_name','last_name','profile_image')
                          ->whereHas('roles',function($query){
                              $query->where('slug','admin');
                          })
                          ->first();

    return isset($obj_user) && count($obj_user)>0 ? $obj_user->toArray() :[];

  }

	function company_profile($company_id)
	{
	
	    $obj_company_profile = UserModel::where('id',$company_id)->first();
			  	
	    $arr_user_profile = [];

	    if($obj_company_profile)
	    {
	    	$arr_user_profile = $obj_company_profile->toArray();
	    }

	    return $arr_user_profile;
	}

	function dateDifference($date1,$date2)
	{
		/*$date1= date('Y-m-d H:i:s');
		$date2="2017-02-21 14:30:00";*/
		$week ='';

		$date1=strtotime($date1);
		$date2=strtotime($date2); 
		$diff = abs($date1 - $date2);
		
		$day = $diff/(60*60*24); /* in day*/
		$week = $diff/(60*60*168); /* in week*/

		$dayFix = floor($day);
		$weekFix = floor($week);

		$dayPen = $day - $dayFix;

		$str = '';
		if($dayPen > 0)
		{
			$hour = $dayPen*(24); /* in hour (1 day = 24 hour)*/
			$hourFix = floor($hour);
			$hourPen = $hour - $hourFix;
			if($hourPen > 0)
			{
				$min = $hourPen*(60); /* in hour (1 hour = 60 min)*/
				$minFix = floor($min);
				$minPen = $min - $minFix;
				if($minPen > 0)
				{
					$sec = $minPen*(60); /* in sec (1 min = 60 sec)*/
					$secFix = floor($sec);
					$str = 'just now';
				}
			}
		}
		if(!empty($minFix))
		{
			$suffix = ' min ago';
			if($minFix > 1){
				$suffix = ' mins ago';
			}
			if($minFix > 0)
			{
				$str = $minFix.$suffix;
			}
		}

		if(!empty($hourFix))
		{
			$suffix = ' hour ago';
			if($hourFix > 1){
				$suffix = ' hours ago';
			}
			if($hourFix > 0)
				$str = $hourFix.$suffix;	
		}

		if(!empty($dayFix))
		{
			$suffix = ' day ago';
			if($dayFix > 1){
				$suffix = ' days ago';
			}
			if($dayFix > 0)
				$str = $dayFix.$suffix;	
		}
		if(!empty($weekFix))
		{
			$suffix = ' weeks ago';
			if($weekFix > 1){
				$suffix = ' weeks ago';
			}
			if($weekFix > 0)
				$str = $weekFix.$suffix;	
		}

		//echo '>>'.$dayFix;
		//exit;
		
		/*if($secFix > 0)
		$str .= $secFix." sec ";*/
		return $str;
	}

	function get_time_difference($datetime1 = false,$datetime2 = false)
  {
      $time = "";
      if($datetime1!=false && $datetime2!=false)
      {
         $interval = $datetime1->diff($datetime2);

         if(isset($interval) && $interval!=null)
         {
            if($interval->h==0 && $interval->i==0)
            {
               if($interval->s < 60)                       /*While 60 sec less then it will get second*/
               {
                  $time = $interval->s.' Second ago';
               }
            }

            if($interval->i!=0 && $interval->i < 60)      /*While 60 min less then it will get min*/
            {
                   $time = $interval->i.' Minute ago';
            }

            if($interval->d==0)
            {
                if($interval->h!=0 && $interval->i <= 60)    /*While 60 min less then it will get hour*/
               {
                  $time = $interval->h.' Hour ago';
               }
            }
            else                                            /*While day 1 then it will get day*/
            {
                  $time = $interval->d.' Day ago';
            }

            if($interval->m>0)                              /*While month getting 1 then it will get month*/
            {
                  $time = $interval->m.' Month ago';
            }

            if($interval->y>0)                              /*While year getting 1 then it will get year*/
            {
                  $time = $interval->y.' Year ago';
            }
         }
      }
      return $time;
  }  

  function getLocationInfoByIp($ip){

      if($ip!='')
      {
        $ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));    

        if($ip_data->geoplugin_countryCode && $ip_data->geoplugin_countryCode != null){
            return $ip_data->geoplugin_countryCode;
        }
        return 'US';

      }
      return 'US';
  }

  function filter_completed_trip_details($arr_bookings,$type = false)
  {
    // dd($arr_bookings);
      $load_post_img_public_path    = url('/').config('app.project.img_path.load_post_img');
      $load_post_img_base_path      = base_path().config('app.project.img_path.load_post_img');

      $user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');
      $user_profile_base_img_path   = base_path().config('app.project.img_path.user_profile_images');

      $arr_data = [];

      $arr_data['booking_id']           = isset($arr_bookings['id'])  ? $arr_bookings['id']  :"";
      $arr_data['booking_unique_id']    = isset($arr_bookings['booking_unique_id'])  ? $arr_bookings['booking_unique_id']  :"";
      $arr_data['booking_date']         = isset($arr_bookings['booking_date'])  ? date('d M Y',strtotime($arr_bookings['booking_date']))  :"";
      
      $arr_data['start_time']       = isset($arr_bookings['start_datetime'])  ? date('h:i A',strtotime($arr_bookings['start_datetime']))  :"";
      $arr_data['end_time']         = isset($arr_bookings['end_datetime'])  ? date('h:i A',strtotime($arr_bookings['end_datetime']))  :"";

      $arr_data['start_datetime']       = isset($arr_bookings['start_datetime'])  ? date('d M Y / h i A',strtotime($arr_bookings['start_datetime']))  :"";
      $arr_data['end_datetime']         = isset($arr_bookings['end_datetime'])  ? date('d M Y / h i A',strtotime($arr_bookings['end_datetime']))  :"";
      
      $arr_data['total_minutes_trip']   = isset($arr_bookings['total_minutes_trip'])  ? $arr_bookings['total_minutes_trip'] :"";

      $arr_data['booking_status']       = isset($arr_bookings['booking_status'])  ? $arr_bookings['booking_status']  :"";
      $arr_data['payment_status']       = isset($arr_bookings['payment_status'])  ? $arr_bookings['payment_status']  :"";
      $arr_data['admin_payment_status'] = isset($arr_bookings['admin_payment_status'])  ? $arr_bookings['admin_payment_status']  :"";
      $arr_data['payment_type']         = isset($arr_bookings['payment_type'])  ? $arr_bookings['payment_type']  :"";

      
      $map_image_url    = url('uploads/default-image.jpg');
      $start_trip_image = $end_trip_image = $invoice_image = url('uploads/default-load-image.jpg');
      
      if(isset($arr_bookings['start_trip_image']) && $arr_bookings['start_trip_image']!='')
      { 
          if(file_exists($load_post_img_base_path.$arr_bookings['start_trip_image']))
          {
             $start_trip_image = $load_post_img_public_path.$arr_bookings['start_trip_image'];
          }
      }

      if(isset($arr_bookings['end_trip_image']) && $arr_bookings['end_trip_image']!='')
      { 
          if(file_exists($load_post_img_base_path.$arr_bookings['end_trip_image']))
          {
             $end_trip_image = $load_post_img_public_path.$arr_bookings['end_trip_image'];
          }
      }

      if(isset($arr_bookings['invoice_image']) && $arr_bookings['invoice_image']!='')
      { 
          if(file_exists($load_post_img_base_path.$arr_bookings['invoice_image']))
          {
             $invoice_image = $load_post_img_public_path.$arr_bookings['invoice_image'];
          }
      }

      $pickup_lat = isset($arr_bookings['load_post_request_details']['pickup_lat'])  ? $arr_bookings['load_post_request_details']['pickup_lat']  :"";
      $pickup_lng = isset($arr_bookings['load_post_request_details']['pickup_lng'])  ? $arr_bookings['load_post_request_details']['pickup_lng']  :"";
      $drop_lat   = isset($arr_bookings['load_post_request_details']['drop_lat'])  ? $arr_bookings['load_post_request_details']['drop_lat']  :"";
      $drop_lng   = isset($arr_bookings['load_post_request_details']['drop_lng'])  ? $arr_bookings['load_post_request_details']['drop_lng']  :"";

      $origin      = $pickup_lat.','.$pickup_lng;
      $destination = $drop_lat.','.$drop_lng;
      
      if($type!=false && $type == 'genrate_pdf'){
        if($origin!='' && $destination!='')
        {
          $map_image_url = getStaticGmapURLForDirection($origin, $destination);
        }
      }
      
      $arr_data['map_image_url']    = $map_image_url;
      $arr_data['start_trip_image'] = $start_trip_image;
      $arr_data['end_trip_image']   = $end_trip_image;
      $arr_data['invoice_image']    = $invoice_image;

      $arr_data['package_type']     = isset($arr_bookings['load_post_request_details']['load_post_request_package_details']['package_type']) ? $arr_bookings['load_post_request_details']['load_post_request_package_details']['package_type'] : '-';
      $arr_data['package_length']   = isset($arr_bookings['load_post_request_details']['load_post_request_package_details']['package_length']) ? $arr_bookings['load_post_request_details']['load_post_request_package_details']['package_length'] : '-';
      $arr_data['package_breadth']  = isset($arr_bookings['load_post_request_details']['load_post_request_package_details']['package_breadth']) ? $arr_bookings['load_post_request_details']['load_post_request_package_details']['package_breadth'] : '-';
      $arr_data['package_height']   = isset($arr_bookings['load_post_request_details']['load_post_request_package_details']['package_height']) ? $arr_bookings['load_post_request_details']['load_post_request_package_details']['package_height'] : '-';
      $arr_data['package_volume']   = isset($arr_bookings['load_post_request_details']['load_post_request_package_details']['package_volume']) ? $arr_bookings['load_post_request_details']['load_post_request_package_details']['package_volume'] : '-';
      $arr_data['package_weight']   = isset($arr_bookings['load_post_request_details']['load_post_request_package_details']['package_weight']) ? $arr_bookings['load_post_request_details']['load_post_request_package_details']['package_weight'] : '-';
      $arr_data['package_quantity'] = isset($arr_bookings['load_post_request_details']['load_post_request_package_details']['package_quantity']) ? $arr_bookings['load_post_request_details']['load_post_request_package_details']['package_quantity'] : '-';

      $arr_data['po_no']              = isset($arr_bookings['po_no']) ? $arr_bookings['po_no'] : '';
      $arr_data['receiver_name']      = isset($arr_bookings['receiver_name']) ? $arr_bookings['receiver_name'] : '';
      $arr_data['receiver_no']        = isset($arr_bookings['receiver_no']) ? $arr_bookings['receiver_no'] : '';
      $arr_data['app_suite']          = isset($arr_bookings['app_suite']) ? $arr_bookings['app_suite'] : '';

      $tmp_user_profile_image= isset($arr_bookings['load_post_request_details']['user_details']['profile_image']) ? $arr_bookings['load_post_request_details']['user_details']['profile_image'] :"";
			$user_first_name   = isset($arr_bookings['load_post_request_details']['user_details']['first_name']) ? $arr_bookings['load_post_request_details']['user_details']['first_name'] :"";
			$user_last_name    = isset($arr_bookings['load_post_request_details']['user_details']['last_name'])  ? $arr_bookings['load_post_request_details']['user_details']['last_name']  :"";
			
      $tmp_driver_profile_image= isset($arr_bookings['load_post_request_details']['driver_details']['profile_image']) ? $arr_bookings['load_post_request_details']['driver_details']['profile_image'] :"";
      $driver_first_name = isset($arr_bookings['load_post_request_details']['driver_details']['first_name']) ? $arr_bookings['load_post_request_details']['driver_details']['first_name'] :"";
			$driver_last_name  = isset($arr_bookings['load_post_request_details']['driver_details']['last_name'])  ? $arr_bookings['load_post_request_details']['driver_details']['last_name']  :"";

      $user_profile_image   = url('uploads/default-profile.png');
      $driver_profile_image = url('uploads/default-profile.png');

      if(isset($tmp_user_profile_image) && $tmp_user_profile_image!='')
      { 
          if(file_exists($user_profile_base_img_path.$tmp_user_profile_image))
          {
             $user_profile_image = $user_profile_public_img_path.$tmp_user_profile_image;
          }
      }

      if(isset($tmp_driver_profile_image) && $tmp_driver_profile_image!='')
      { 
          if(file_exists($user_profile_base_img_path.$tmp_driver_profile_image))
          {
             $driver_profile_image = $user_profile_public_img_path.$tmp_driver_profile_image;
          }
      }

      $arr_data['user_profile_image']   = $user_profile_image;
      $arr_data['driver_profile_image'] = $driver_profile_image;
      $arr_data['user_name']            = $user_first_name." ".$user_last_name;
      $arr_data['user_contact_no']      = isset($arr_bookings['load_post_request_details']['user_details']['mobile_no']) ? $arr_bookings['load_post_request_details']['user_details']['mobile_no'] :"";
      $arr_data['user_country_code']    = isset($arr_bookings['load_post_request_details']['user_details']['country_code']) ? $arr_bookings['load_post_request_details']['user_details']['country_code'] :"";
      $arr_data['user_email']           = isset($arr_bookings['load_post_request_details']['user_details']['email']) ? $arr_bookings['load_post_request_details']['user_details']['email'] :"";
      $arr_data['driver_name']          = $driver_first_name." ".$driver_last_name;
      $arr_data['driver_country_code']  = isset($arr_bookings['load_post_request_details']['driver_details']['country_code']) ? $arr_bookings['load_post_request_details']['driver_details']['country_code'] :"";
      $arr_data['driver_contact_no']    = isset($arr_bookings['load_post_request_details']['driver_details']['mobile_no']) ? $arr_bookings['load_post_request_details']['driver_details']['mobile_no'] :"";
      $arr_data['driver_email']         = isset($arr_bookings['load_post_request_details']['driver_details']['email']) ? $arr_bookings['load_post_request_details']['driver_details']['email'] :"";
      $arr_data['pickup_location']      = isset($arr_bookings['load_post_request_details']['pickup_location']) ? $arr_bookings['load_post_request_details']['pickup_location'] :"";
      $arr_data['pickup_lat']           = isset($arr_bookings['load_post_request_details']['pickup_lat']) ? floatval($arr_bookings['load_post_request_details']['pickup_lat']) :"";
      $arr_data['pickup_lng']           = isset($arr_bookings['load_post_request_details']['pickup_lng']) ? floatval($arr_bookings['load_post_request_details']['pickup_lng']) :"";
      $arr_data['drop_location']        = isset($arr_bookings['load_post_request_details']['drop_location'])? $arr_bookings['load_post_request_details']['drop_location'] :"";
      $arr_data['drop_lat']             = isset($arr_bookings['load_post_request_details']['drop_lat']) ? floatval($arr_bookings['load_post_request_details']['drop_lat']) :"";
      $arr_data['drop_lng']             = isset($arr_bookings['load_post_request_details']['drop_lng']) ? floatval($arr_bookings['load_post_request_details']['drop_lng']) :"";
      $arr_data['vehicle_name']         = isset($arr_bookings['load_post_request_details']['vehicle_details']['vehicle_name']) ? $arr_bookings['load_post_request_details']['vehicle_details']['vehicle_name'] :"";
      $arr_data['vehicle_number']       = isset($arr_bookings['load_post_request_details']['vehicle_details']['vehicle_number']) ? $arr_bookings['load_post_request_details']['vehicle_details']['vehicle_number'] :"";
      $arr_data['vehicle_model']        = isset($arr_bookings['load_post_request_details']['vehicle_details']['vehicle_model_name']) ? $arr_bookings['load_post_request_details']['vehicle_details']['vehicle_model_name'] :"";
      $arr_data['vehicle_type']         = isset($arr_bookings['load_post_request_details']['vehicle_details']['vehicle_type_details']['vehicle_type']) ? $arr_bookings['load_post_request_details']['vehicle_details']['vehicle_type_details']['vehicle_type'] :"";
      $arr_data['coordinates']          = isset($arr_bookings['booking_master_coordinate_details']['coordinates']) ? $arr_bookings['booking_master_coordinate_details']['coordinates']:"";
      $arr_data['map_image']            = isset($arr_bookings['booking_master_coordinate_details']['map_image']) ? $arr_bookings['booking_master_coordinate_details']['map_image']:"";

      //values which are required to do calculation.

      $is_promo_code_applied     = isset($arr_bookings['is_promo_code_applied']) ? $arr_bookings['is_promo_code_applied'] :'';
      $promo_code                = isset($arr_bookings['promo_code']) ? $arr_bookings['promo_code']:"";
      $promo_percentage          = isset($arr_bookings['promo_percentage']) ? floatval($arr_bookings['promo_percentage']):0.00;
      $promo_max_amount          = isset($arr_bookings['promo_max_amount']) ? floatval($arr_bookings['promo_max_amount']):0.00;
      $applied_promo_code_charge = isset($arr_bookings['applied_promo_code_charge']) ? floatval($arr_bookings['applied_promo_code_charge']):0.00;
      $is_company_driver         = isset($arr_bookings['is_company_driver']) ? $arr_bookings['is_company_driver']:0;

      //bonus point values
      $is_bonus_used                       = isset($arr_bookings['is_bonus_used']) ? $arr_bookings['is_bonus_used']:'';
      $admin_referral_points               = isset($arr_bookings['admin_referral_points']) ? $arr_bookings['admin_referral_points']:0;
      $admin_referral_points_price_per_usd = isset($arr_bookings['admin_referral_points_price_per_usd']) ? $arr_bookings['admin_referral_points_price_per_usd']:0;
      $user_bonus_points                   = isset($arr_bookings['user_bonus_points']) ? $arr_bookings['user_bonus_points']:0;
      $user_bonus_points_usd_amount        = isset($arr_bookings['user_bonus_points_usd_amount']) ? $arr_bookings['user_bonus_points_usd_amount']:0;

      $company_name ='';

      if($is_company_driver == '1')
      {
        $company_name  = isset($arr_bookings['load_post_request_details']['driver_details']['company_details']['company_name']) ? $arr_bookings['load_post_request_details']['driver_details']['company_details']['company_name'] :"";
      }
      else if($is_company_driver == '0'){ 
        $company_name  = config('app.project.name');
      }
      $arr_data['company_name']           = $company_name;


      $admin_driver_percentage      = isset($arr_bookings['admin_driver_percentage']) ? floatval($arr_bookings['admin_driver_percentage']):0;
      $admin_company_percentage     = isset($arr_bookings['admin_company_percentage']) ? floatval($arr_bookings['admin_company_percentage']):0;
      $individual_driver_percentage = isset($arr_bookings['individual_driver_percentage']) ? floatval($arr_bookings['individual_driver_percentage']):0;
      $company_driver_percentage    = isset($arr_bookings['company_driver_percentage']) ? floatval($arr_bookings['company_driver_percentage']):0;
     
      $is_individual_vehicle       = isset($arr_bookings['is_individual_vehicle']) ? $arr_bookings['is_individual_vehicle']:0;
      $distance                    = isset($arr_bookings['distance']) ? floatval($arr_bookings['distance']) :0;
      $total_charge                = isset($arr_bookings['total_charge']) ? $arr_bookings['total_charge']:0;
      $total_amount                = isset($arr_bookings['total_amount']) ? floatval($arr_bookings['total_amount']):0.00;


      //create flag to intimate admin that promo amount is greater than earning amount
      
      $is_admin_earning_less_than_promo_value = false;

      $admin_earning_amount = $driver_earning_amount = $company_earning_amount = $total_company_earning_amount = 0;

      $vehicle_owner = '';

      if(isset($arr_bookings['booking_status']) && $arr_bookings['booking_status'] == 'CANCEL_BY_USER')
      {
        $admin_earning_amount = $total_amount;
      }
      else
      {
          $admin_earning_amount = isset($arr_bookings['admin_amount']) ? floatval($arr_bookings['admin_amount']):0.00;

          if($is_individual_vehicle == '1')
          {
              $vehicle_owner = 'Individual Vehicle';
              $driver_earning_amount = isset($arr_bookings['individual_driver_amount']) ? floatval($arr_bookings['individual_driver_amount']):0.00;
          }
          else if($is_individual_vehicle == '0')
          {     
              //company drivers not having their own vehicles if driver for company then appy company commission amount
              if($is_company_driver == '1')
              {
                  $vehicle_owner = $company_name.' Vehicle';
                  $driver_earning_amount  = isset($arr_bookings['company_driver_amount']) ? floatval($arr_bookings['company_driver_amount']):0.00;
                  $company_earning_amount = isset($arr_bookings['company_amount']) ? floatval($arr_bookings['company_amount']):0.00;
                  $total_company_earning_amount = floatval($driver_earning_amount) + floatval($company_earning_amount);

              }
              if($is_company_driver == '0')
              {
                  $vehicle_owner = $company_name.' Vehicle';
                  $driver_earning_amount = isset($arr_bookings['admin_driver_amount']) ? floatval($arr_bookings['admin_driver_amount']):0.00;
              }
          }
      }
      
      $arr_data['is_promo_code_applied']                  = isset($arr_bookings['is_promo_code_applied']) ? $arr_bookings['is_promo_code_applied'] :'';
      $arr_data['promo_code']                             = isset($arr_bookings['promo_code']) ? $arr_bookings['promo_code']:"";
      $arr_data['promo_percentage']                       = isset($arr_bookings['promo_percentage']) ? floatval($arr_bookings['promo_percentage']):0.00;
      $arr_data['promo_max_amount']                       = isset($arr_bookings['promo_max_amount']) ? floatval($arr_bookings['promo_max_amount']):0.00;
      $arr_data['applied_promo_code_charge']              = isset($arr_bookings['applied_promo_code_charge']) ? floatval($arr_bookings['applied_promo_code_charge']):0.00;
      $arr_data['is_company_driver']                      = isset($arr_bookings['is_company_driver']) ? $arr_bookings['is_company_driver']:0;
      $arr_data['is_individual_vehicle']                  = isset($arr_bookings['is_individual_vehicle']) ? $arr_bookings['is_individual_vehicle']:0;
      $arr_data['starting_price']                         = isset($arr_bookings['starting_price']) ? floatval($arr_bookings['starting_price']):0;
      $arr_data['per_miles_price']                        = isset($arr_bookings['per_miles_price']) ? floatval($arr_bookings['per_miles_price']):0;
      $arr_data['per_minute_price']                       = isset($arr_bookings['per_minute_price']) ? floatval($arr_bookings['per_minute_price']):0;
      $arr_data['minimum_price']                          = isset($arr_bookings['minimum_price']) ? floatval($arr_bookings['minimum_price']):0;
      $arr_data['cancellation_base_price']                = isset($arr_bookings['cancellation_base_price']) ? floatval($arr_bookings['cancellation_base_price']):0;
      $arr_data['admin_driver_percentage']                = isset($arr_bookings['admin_driver_percentage']) ? floatval($arr_bookings['admin_driver_percentage']):0;
      $arr_data['admin_company_percentage']               = isset($arr_bookings['admin_company_percentage']) ? floatval($arr_bookings['admin_company_percentage']):0;
      $arr_data['individual_driver_percentage']           = isset($arr_bookings['individual_driver_percentage']) ? floatval($arr_bookings['individual_driver_percentage']):0;
      $arr_data['company_driver_percentage']              = isset($arr_bookings['company_driver_percentage']) ? floatval($arr_bookings['company_driver_percentage']):0;
      $arr_data['is_base_price_applied']                  = isset($arr_bookings['is_base_price_applied']) ? $arr_bookings['is_base_price_applied']:'';
      $arr_data['is_bonus_used']                          = isset($arr_bookings['is_bonus_used']) ? $arr_bookings['is_bonus_used']:'';
      $arr_data['admin_referral_points']                  = isset($arr_bookings['admin_referral_points']) ? $arr_bookings['admin_referral_points']:0;
      $arr_data['admin_referral_points_price_per_usd']    = isset($arr_bookings['admin_referral_points_price_per_usd']) ? $arr_bookings['admin_referral_points_price_per_usd']:0;
      $arr_data['user_bonus_points']                      = isset($arr_bookings['user_bonus_points']) ? $arr_bookings['user_bonus_points']:0;
      $arr_data['user_bonus_points_usd_amount']           = isset($arr_bookings['user_bonus_points_usd_amount']) ? $arr_bookings['user_bonus_points_usd_amount']:0;
      $arr_data['distance']                               = isset($arr_bookings['distance']) ? floatval($arr_bookings['distance']) :0;
      $arr_data['total_charge']                           = isset($arr_bookings['total_charge']) ? $arr_bookings['total_charge']:0;
      $arr_data['total_amount']                           = isset($arr_bookings['total_amount']) ? floatval($arr_bookings['total_amount']):0.00;
      $arr_data['admin_amount']                           = isset($arr_bookings['admin_amount']) ? floatval($arr_bookings['admin_amount']):0.00;
      $arr_data['company_amount']                         = isset($arr_bookings['company_amount']) ? floatval($arr_bookings['company_amount']):0.00;
      $arr_data['admin_driver_amount']                    = isset($arr_bookings['admin_driver_amount']) ? floatval($arr_bookings['admin_driver_amount']):0.00;
      $arr_data['company_driver_amount']                  = isset($arr_bookings['company_driver_amount']) ? floatval($arr_bookings['company_driver_amount']):0.00;
      $arr_data['individual_driver_amount']               = isset($arr_bookings['individual_driver_amount']) ? floatval($arr_bookings['individual_driver_amount']):0.00;
      $arr_data['is_admin_earning_less_than_promo_value'] = $is_admin_earning_less_than_promo_value;
      $arr_data['vehicle_owner']                          = $vehicle_owner;
      $arr_data['admin_earning_amount']                   = $admin_earning_amount;
      $arr_data['driver_earning_amount']                  = $driver_earning_amount;
      $arr_data['company_earning_amount']                 = $company_earning_amount;
      $arr_data['total_company_earning_amount']           = $total_company_earning_amount;

      return $arr_data;
  }

  function getStaticGmapURLForDirection($origin, $destination, $size = "680x575") {
                                
      $icon_path = url('/assets/node_assets/images/pointer.png');
      $markers   = array();
      $markers[] = "markers=icon:" .$icon_path. urlencode("|") . $origin;
      $markers[] = "markers=icon:" .$icon_path. urlencode("|") . $destination;

      $url = "https://maps.googleapis.com/maps/api/directions/json?key=AIzaSyASynNXpP9v040cNSh2f_A8XVnPkQ5mUEY&origin=$origin&destination=$destination";

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POST, false);
      $result = curl_exec($ch);

      curl_close($ch);
      $googleDirection = json_decode($result, true);
      
      $polyline ='';
      if(isset($googleDirection['routes'][0]['overview_polyline']['points'])){
          $polyline = urlencode($googleDirection['routes'][0]['overview_polyline']['points']);
      }
      $markers = implode($markers, '&');
      return "https://maps.googleapis.com/maps/api/staticmap?key=AIzaSyASynNXpP9v040cNSh2f_A8XVnPkQ5mUEY&size=$size&maptype=roadmap&path=enc:$polyline&$markers";
  }


  function get_specific_user_users_for_applozic_chat($login_user_id,$login_user_role)
  {
      $arr_users = [];

      $obj_users = UserModel::select('id','first_name','last_name','company_name','profile_image','email','mobile_no')
                              ->with('roles')
                              ->where('id','!=',$login_user_id);

      if($login_user_role == 'company')
      {
        $obj_users = $obj_users
                        ->where(function($query) use ($login_user_id) {
                            $query->where(['company_id'=>$login_user_id,'is_company_driver'=>'1']);  
                            $query->orwhere(['id'=>get_admin_id()]);  

                        });
      }
      $obj_users = $obj_users->get();
      
      if($obj_users)
      { 
        $arr_users = $obj_users->toArray();
      }

      $arr_login_user_details = [];

      $user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');
      $user_profile_base_img_path  = base_path().config('app.project.img_path.user_profile_images');

      if(isset($arr_users) && sizeof($arr_users)>0)
      {
          foreach ($arr_users as $key => $value) 
          {
              $user_type = '';
              
              $arr_tmp                = [];
              $arr_tmp['userId']      = '0';
              $arr_tmp['displayName'] = '';
              $arr_tmp['imageLink']   = '';

              $arr_tmp['userId']   = isset($value['id']) ? strval($value['id']) :'0';
              
              if(isset($value['profile_image']) && $value['profile_image']!='' && file_exists($user_profile_base_img_path.$value['profile_image']))
              {
                  $arr_tmp['imageLink'] = $user_profile_public_img_path.$value['profile_image'];
              }
              if(isset($value['roles'][0]['slug']) && $value['roles'][0]['slug'] == 'admin')
              {   
                  $user_type = 'admin';
                  $arr_tmp['displayName']     = config('app.project.name').':Admin';
              }
              else if(isset($value['roles'][0]['slug']) && $value['roles'][0]['slug'] == 'driver')
              {   
                  $user_type = 'driver';
                  $first_name = isset($value['first_name']) ? $value['first_name'] :'';
                  $last_name  = isset($value['last_name']) ? $value['last_name'] :'';
                  $full_name  = $first_name.' '.$last_name;
                  $full_name  = ($full_name!=' ') ? $full_name :'';
                  $arr_tmp['displayName']     = $full_name;
              }
              else if(isset($value['roles'][0]['slug']) && $value['roles'][0]['slug'] == 'user')
              {   
                  $user_type = 'user';
                  $first_name = isset($value['first_name']) ? $value['first_name'] :'';
                  $last_name  = isset($value['last_name']) ? $value['last_name'] :'';
                  $full_name  = $first_name.' '.$last_name;
                  $full_name  = ($full_name!=' ') ? $full_name :'';
                  $arr_tmp['displayName']     = $full_name;
              }
              else if(isset($value['roles'][0]['slug']) && $value['roles'][0]['slug'] == 'company')
              {   
                  $user_type = 'company';
                  $company_name = (isset($value['company_name']) && $value['company_name']!='') ? $value['company_name'] : '';
                  $full_name    = 'Comapny:Admin';
                  if($company_name!='')
                  {
                      $full_name    = $company_name.':Admin';
                  }

                  $arr_tmp['displayName']     = $full_name;
              }
              array_push($arr_login_user_details, $arr_tmp); 
          }
      }
      return json_encode($arr_login_user_details);
  }

  function get_specific_users_for_applozic_chat_drivers_and_users($login_user_id,$login_user_role){
    // dd($login_user_id,$login_user_role);
      $arr_tmp_ids = get_current_trip_chat_list_users_id($login_user_id,$login_user_role);
      
      $arr_users = [];

      $obj_users = UserModel::select('id','first_name','last_name','company_name','profile_image','email','mobile_no')
                              ->with('roles')
                              ->where('id','!=',$login_user_id)
                              ->whereIn('id',$arr_tmp_ids);

      $obj_users = $obj_users->get();
      
      if($obj_users)
      { 
        $arr_users = $obj_users->toArray();
      }

      $arr_login_user_details = [];

      $user_profile_public_img_path = url('/').config('app.project.img_path.user_profile_images');
      $user_profile_base_img_path  = base_path().config('app.project.img_path.user_profile_images');

      if(isset($arr_users) && sizeof($arr_users)>0)
      {
          foreach ($arr_users as $key => $value) 
          {
              $user_type = '';
              
              $arr_tmp                = [];
              $arr_tmp['userId']      = '0';
              $arr_tmp['displayName'] = '';
              $arr_tmp['imageLink']   = '';

              $arr_tmp['userId']   = isset($value['id']) ? strval($value['id']) :'0';
              
              if(isset($value['profile_image']) && $value['profile_image']!='' && file_exists($user_profile_base_img_path.$value['profile_image']))
              {
                  $arr_tmp['imageLink'] = $user_profile_public_img_path.$value['profile_image'];
              }
              if(isset($value['roles'][0]['slug']) && $value['roles'][0]['slug'] == 'admin')
              {   
                  $user_type = 'admin';
                  $arr_tmp['displayName']     = config('app.project.name').':Admin';
              }
              else if(isset($value['roles'][0]['slug']) && $value['roles'][0]['slug'] == 'driver')
              {   
                  $user_type = 'driver';
                  $first_name = isset($value['first_name']) ? $value['first_name'] :'';
                  $last_name  = isset($value['last_name']) ? $value['last_name'] :'';
                  $full_name  = $first_name.' '.$last_name;
                  $full_name  = ($full_name!=' ') ? $full_name :'';
                  $arr_tmp['displayName']     = $full_name;
              }
              else if(isset($value['roles'][0]['slug']) && $value['roles'][0]['slug'] == 'user')
              {   
                  $user_type = 'user';
                  $first_name = isset($value['first_name']) ? $value['first_name'] :'';
                  $last_name  = isset($value['last_name']) ? $value['last_name'] :'';
                  $full_name  = $first_name.' '.$last_name;
                  $full_name  = ($full_name!=' ') ? $full_name :'';
                  $arr_tmp['displayName']     = $full_name;
              }
              else if(isset($value['roles'][0]['slug']) && $value['roles'][0]['slug'] == 'company')
              {   
                  $user_type = 'company';
                  $company_name = (isset($value['company_name']) && $value['company_name']!='') ? $value['company_name'] : '';
                  $full_name    = 'Comapny:Admin';
                  if($company_name!='')
                  {
                      $full_name    = $company_name.':Admin';
                  }

                  $arr_tmp['displayName']     = $full_name;
              }
              array_push($arr_login_user_details, $arr_tmp); 
          }
      }
      return json_encode($arr_login_user_details);
  }
	
  function get_current_trip_chat_list_users_id($login_user_id,$login_user_role)
  {
        $arr_booking = [];

        $obj_booking = BookingMasterModel::select('id','load_post_request_id','booking_status')
                                ->whereHas('load_post_request_details',function($query)use($login_user_id,$login_user_role){
                                    if($login_user_role == 'USER')
                                    {
                                        $query->where('user_id',$login_user_id);
                                    }
                                    if($login_user_role == 'DRIVER')
                                    {
                                        $query->where('driver_id',$login_user_id);
                                    }
                                })
                                ->with(['load_post_request_details'=>function($query){
                                    $query->select('id','user_id','driver_id');
                                }])
                                ->whereIn('booking_status',['TO_BE_PICKED','IN_TRANSIT'])
                                ->get();

        if($obj_booking)
        {
            $arr_booking = $obj_booking->toArray();
        }

        $arr_tmp_ids = [];
        
        $arr_tmp_ids[] = get_admin_id();

        if(isset($arr_booking) && sizeof($arr_booking)>0){
            foreach ($arr_booking as $key => $value) 
            {
                if($login_user_role == 'USER')
                {
                    if(isset($value['load_post_request_details']['driver_id']) && $value['load_post_request_details']['driver_id']!='')
                    {
                        $arr_tmp_ids[] = $value['load_post_request_details']['driver_id'];
                    }
                }
                if($login_user_role == 'DRIVER')
                {
                    if(isset($value['load_post_request_details']['user_id']) && $value['load_post_request_details']['user_id']!='')
                    {
                        $arr_tmp_ids[] = $value['load_post_request_details']['user_id'];
                    }
                }
            }
        }
        
        return $arr_tmp_ids;
  }