<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use App\Models\UserModel;
use App\Models\VehicleModel;
use App\Models\VehicleTypeModel;
use App\Models\DepositMoneyModel;
use App\Models\BookingMasterModel;
use App\Models\LoadPostRequestModel;

use DB;
use Flash;
use Sentinel;
use Datatables;

class MyEarningController extends Controller
{
    
    public function __construct( 

    							 UserModel $user,
                                 VehicleModel $vehicle,
                                 VehicleTypeModel $vehicle_type,
                                 DepositMoneyModel $driver_deposit,
                                 BookingMasterModel $booking_master,
                                 LoadPostRequestModel $load_post_request
    						   )
    {

        
        
        $this->UserModel            = $user;
        $this->VehicleModel         = $vehicle;
        $this->VehicleTypeModel     = $vehicle_type;
        $this->DepositMoneyModel    = $driver_deposit;
        $this->BookingMasterModel   = $booking_master;
        $this->LoadPostRequestModel = $load_post_request;

        $this->arr_view_data             = [];
        $this->module_title              = "My Earning";
        $this->module_view_folder        = "company.my_earning";
        $this->theme_color               = theme_color();
        $this->company_panel_slug        = config('app.project.company_panel_slug');
        $this->module_url_path           = url(config('app.project.company_panel_slug')."/my_earning");
        $this->receipt_image_public_path = url('/').config('app.project.img_path.payment_receipt');
        $this->receipt_image_base_path   = base_path().config('app.project.img_path.payment_receipt');

        $this->company_id = 0;
        $this->company_name = '';

        $user = Sentinel::check();
        if($user){
            $this->company_id   = isset($user->id) ? $user->id :0;
            $this->company_name = isset($user->company_name) ? $user->company_name :'';
        }
    } 

    public function index()
    {
        $arr_company_balance_information = $this->get_company_balance_information();

        $this->arr_view_data['page_title']                      = "Manage ".str_plural($this->module_title);
        $this->arr_view_data['module_title']                    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']                 = $this->module_url_path;
        $this->arr_view_data['theme_color']                     = $this->theme_color;
        $this->arr_view_data['arr_company_balance_information'] = $arr_company_balance_information;
 
        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    public function get_records(Request $request)
    {
        $obj_bookings        =  $this->get_booking_details($request);
        
        $json_result     = Datatables::of($obj_bookings);

        $json_result     = $json_result->blacklist(['id']);
        $current_context = $this;
        $json_result    = $json_result
                                    ->editColumn('driver_name',function($data) use ($current_context)
                                    {
                                        $driver_name       = isset($data->driver_name) ? $data->driver_name :'';
                                        return  $data->driver_name;
                                    })

                                    ->editColumn('fare_charge',function($data) use ($current_context)
                                    {
                                        $fare_charge = 0;

                                        $is_individual_vehicle       = isset($data->is_individual_vehicle) ? $data->is_individual_vehicle :0;
                                        $driver_fare_charge          = isset($data->driver_fare_charge) ? floatval($data->driver_fare_charge) :0.00;
                                        $admin_per_kilometer_charge  = isset($data->admin_per_kilometer_charge) ? floatval($data->admin_per_kilometer_charge) :0.00;

                                        if($is_individual_vehicle == '1'){
                                            $fare_charge  = $driver_fare_charge;
                                        }

                                        if($is_individual_vehicle == '0'){
                                            $fare_charge  = $admin_per_kilometer_charge;
                                            $fare_charge  = number_format($fare_charge,2);
                                        }
                                        $fare_charge = '<i class="fa fa-usd"> </i> '.$fare_charge;
                                        return $fare_charge;
                                        
                                    })
                                    ->editColumn('distance',function($data) use ($current_context)
                                    {                 
                                        $distance = isset($data->distance) ? $data->distance:0;
                                        $distance = $distance.' <strong>Miles</strong>';
                                        return  $distance;                                        
                                    })
                                    ->editColumn('total_amount',function($data) use ($current_context)
                                    {                 
                                        $total_amount = isset($data->total_amount) ? $data->total_amount:0;
                                        $total_amount = number_format($total_amount,2);
                                        $total_amount = '<i class="fa fa-usd"> </i> '.$total_amount;
                                        return  $total_amount;                                        
                                    })
                                    ->editColumn('applied_promo_code_charge',function($data) use ($current_context)
                                    {                 
                                        $applied_promo_code_charge    = isset($data->applied_promo_code_charge) ? $data->applied_promo_code_charge:0;
                                        $user_bonus_points_usd_amount = isset($data->user_bonus_points_usd_amount) ? $data->user_bonus_points_usd_amount:0;
                                        $total_discount_amount        = ($applied_promo_code_charge + $user_bonus_points_usd_amount);
                                        $total_discount_amount        = number_format($total_discount_amount,2);

                                        $applied_promo_code_charge = '<i class="fa fa-usd"> </i> '.$total_discount_amount;
                                        return  $applied_promo_code_charge;                                    
                                    })
                                    ->editColumn('total_charge',function($data) use ($current_context)
                                    {                 
                                        $total_charge = isset($data->total_charge) ? $data->total_charge:0;
                                        $total_charge = number_format($total_charge,2);
                                        $total_charge = '<i class="fa fa-usd"> </i> '.$total_charge;
                                        return  $total_charge;                                        
                                    })
                                    ->editColumn('driver_amount',function($data) use ($current_context)
                                    {     
                                        $arr_booking = [];
                                        if($data)
                                        {
                                            $arr_booking = collect($data)->toArray();
                                        }
                                        $arr_data = filter_completed_trip_details($arr_booking);
                                        $driver_amount = isset($arr_data['driver_earning_amount']) ? $arr_data['driver_earning_amount']:0;
                                        $driver_amount = number_format($driver_amount,2);
                                        $driver_amount = '<i class="fa fa-usd"> </i> '.$driver_amount;
                                        return  $driver_amount;                                        
                                    })
                                     ->editColumn('company_amount',function($data) use ($current_context)
                                    {     
                                        $arr_booking = [];
                                        if($data)
                                        {
                                            $arr_booking = collect($data)->toArray();
                                        }
                                        $arr_data = filter_completed_trip_details($arr_booking);
                                       
                                        $company = isset($arr_data['company_earning_amount']) ? $arr_data['company_earning_amount']:0;
                                        $company = number_format($company,2);
                                        $company = '<i class="fa fa-usd"> </i> '.$company;
                                        return  $company;                                        
                                    })
                                    ->editColumn('admin_amount',function($data) use ($current_context)
                                    { 
                                        $arr_booking = [];
                                        if($data)
                                        {
                                            $arr_booking = collect($data)->toArray();
                                        }
                                        
                                        $arr_data = filter_completed_trip_details($arr_booking);
                                        $admin_amount = isset($arr_data['admin_earning_amount']) ? $arr_data['admin_earning_amount']:0;
                                        $admin_amount = number_format($admin_amount,2);
                                        $admin_amount = '<i class="fa fa-usd"> </i> '.$admin_amount;
                                        return  $admin_amount;                                        
                                    })
                                    ->editColumn('payment_status',function($data) use ($current_context)
                                    {
                                        $payment_status = '';
                                        if($data->payment_status == 'PENDING')
                                        {
                                            $payment_status = '<span class="badge badge-warning" style="width:100px">Pending</span>';
                                        }
                                        else if($data->payment_status == 'SUCCESS')
                                        {
                                            $payment_status = '<span class="badge badge-success" style="width:100px">Success</span>';
                                        }
                                        else if($data->payment_status == 'FAILED')
                                        {
                                            $payment_status = '<span class="badge badge-important" style="width:100px">Failed</span>';
                                        }
                                        return  $payment_status;
                                    })
                                    ->editColumn('build_action_btn',function($data) use ($current_context)
                                    {                 
                                        $build_view_action = '<a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip" href="#" data-id="'.$data->id.'" onclick="earning_details(this); " data-toggle="modal" title="View"><i class="fa fa-eye" ></i></a>';
                                        return  $build_view_action;                                        
                                    })
                                ->make(true);

        $build_result = $json_result->getData();

        return response()->json($build_result);
    }    

    private function get_booking_details($request)
    {     
        $booking_details                    = $this->BookingMasterModel->getTable();
        $prefixed_booking_details           = DB::getTablePrefix().$this->BookingMasterModel->getTable();

        $load_post_request_details          = $this->LoadPostRequestModel->getTable();
        $prefixed_load_post_request_details = DB::getTablePrefix().$this->LoadPostRequestModel->getTable();
        
        $user_details                       = $this->UserModel->getTable();
        $prefixed_user_details              = DB::getTablePrefix().$this->UserModel->getTable();

        $vehicle_details                    = $this->VehicleModel->getTable();
        $prefixed_vehicle_details           = DB::getTablePrefix().$this->VehicleModel->getTable();

        $vehicle_type_details               = $this->VehicleTypeModel->getTable();
        $prefixed_type_vehicle_details      = DB::getTablePrefix().$this->VehicleTypeModel->getTable();

        $obj_user = DB::table($booking_details)
                                ->select(DB::raw(   

                                                    $prefixed_booking_details.".id ,".
                                                    "CONCAT(".$prefixed_user_details.".first_name,' ',".$prefixed_user_details.".last_name) as driver_name,".
                                                    "company.company_name as company_name,".
                                                    $prefixed_booking_details.".booking_unique_id,".
                                                    "DATE_FORMAT(".$prefixed_booking_details.".booking_date,'%d %b %Y') as booking_date,".
                                                    $prefixed_booking_details.".is_promo_code_applied,".
                                                    $prefixed_booking_details.".promo_code,".
                                                    $prefixed_booking_details.".promo_percentage,".
                                                    $prefixed_booking_details.".promo_max_amount,".
                                                    $prefixed_booking_details.".applied_promo_code_charge,".
                                                    $prefixed_booking_details.".is_company_driver,".
                                                    $prefixed_booking_details.".is_individual_vehicle,".
                                                    $prefixed_booking_details.".starting_price,".
                                                    $prefixed_booking_details.".per_miles_price,".
                                                    $prefixed_booking_details.".per_minute_price,".
                                                    $prefixed_booking_details.".minimum_price,".
                                                    $prefixed_booking_details.".cancellation_base_price,".
                                                    $prefixed_booking_details.".admin_driver_percentage,".
                                                    $prefixed_booking_details.".admin_company_percentage,".
                                                    $prefixed_booking_details.".individual_driver_percentage,".
                                                    $prefixed_booking_details.".company_driver_percentage,".
                                                    $prefixed_booking_details.".is_bonus_used,".
                                                    $prefixed_booking_details.".admin_referral_points,".
                                                    $prefixed_booking_details.".admin_referral_points_price_per_usd,".
                                                    $prefixed_booking_details.".user_bonus_points,".
                                                    $prefixed_booking_details.".user_bonus_points_usd_amount,".
                                                    $prefixed_booking_details.".distance,".
                                                    $prefixed_booking_details.".total_charge,".
                                                    $prefixed_booking_details.".total_amount,".
                                                    $prefixed_booking_details.".admin_amount,".
                                                    $prefixed_booking_details.".company_amount,".
                                                    $prefixed_booking_details.".admin_driver_amount,".
                                                    $prefixed_booking_details.".company_driver_amount,".
                                                    $prefixed_booking_details.".individual_driver_amount,".
                                                    $prefixed_booking_details.".admin_payment_status,".
                                                    $prefixed_booking_details.".booking_status,".
                                                    $prefixed_booking_details.".payment_status"
                                                ))
                                ->join($prefixed_load_post_request_details,$prefixed_load_post_request_details.'.id','=',$booking_details.'.load_post_request_id')
                                ->join($prefixed_user_details,$user_details.'.id','=',$prefixed_load_post_request_details.'.driver_id')                               
                                ->leftjoin("users AS company", "company.id", '=', $user_details.'.company_id')
                                ->where($booking_details.'.booking_status','COMPLETED')
                                
                                ->where($user_details.'.company_id',$this->company_id)
                                ->where($user_details.'.is_company_driver','1')
                                ->orderBy($booking_details.'.created_at','DESC');
        /* ---------------- Filtering Logic ----------------------------------*/                    

        $arr_search_column = $request->input('column_filter');
        
        if(isset($arr_search_column['booking_unique_id']) && $arr_search_column['booking_unique_id']!="")
        {
            $search_term      = $arr_search_column['booking_unique_id'];
            $obj_user        = $obj_user->where('booking_unique_id','LIKE', '%'.$search_term.'%');
        }

        if(isset($arr_search_column['booking_date']) && $arr_search_column['booking_date']!="")
        {
            $search_term     = $arr_search_column['booking_date'];
            $obj_user        = $obj_user->having('booking_date','LIKE', '%'.$search_term.'%');
        }

        if(isset($arr_search_column['user_name']) && $arr_search_column['user_name']!="")
        {
            $search_term      = $arr_search_column['user_name'];
            $obj_user = $obj_user->having('user_name','LIKE', '%'.$search_term.'%');
        }

        if(isset($arr_search_column['driver_name']) && $arr_search_column['driver_name']!="")
        {
            $search_term      = $arr_search_column['driver_name'];
            $obj_user = $obj_user->having('driver_name','LIKE', '%'.$search_term.'%');
        }
        if(isset($arr_search_column['payment_status']) && $arr_search_column['payment_status']!="")
        {
            $ride_status      = $arr_search_column['payment_status'];
            $obj_user        = $obj_user->where($booking_details.'.payment_status', $ride_status);
        }
        return $obj_user;
    } 

    public function earning_info(Request $request)
    {
        $id          = $request->input('id');

        $arr_bookings = [];

        $obj_bookings   = $this->BookingMasterModel
                                    ->with(['load_post_request_details'=>function($query){
                                        $query->with(['driver_details'=>function($query){
                                            $query->select('id','company_id','first_name','last_name','email','mobile_no','profile_image','is_company_driver','company_name','country_code');
                                            $query->with('driver_car_details.vehicle_details','company_details');

                                        }]);
                                        $query->with(['user_details'=>function($query){
                                            $query->select('id','first_name','last_name','email','country_code','mobile_no','profile_image');
                                        }]);
                                        $query->with('vehicle_details.vehicle_type_details');

                                    }])
                                    ->where('id',$id)
                                    ->first();

        if($obj_bookings)
        {
            $arr_bookings = $obj_bookings->toArray();
        }    
        $arr_data = filter_completed_trip_details($arr_bookings);
        $build_html = '';

        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Booking ID : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_data['booking_unique_id']) ? $arr_data['booking_unique_id'] : '';
            $build_html .=  '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';

            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Booking Date : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_data['booking_date']) ? $arr_data['booking_date'] : '';
            $build_html .=  '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';

            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Ride Start Date : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_data['start_datetime']) ? $arr_data['start_datetime'] : '';
            $build_html .=  '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';
            
            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Ride End Date : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_data['end_datetime']) ? $arr_data['end_datetime'] : '';
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';

            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">User Name : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_data['user_name']) ? $arr_data['user_name'] : '';
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';


            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">User Contact No / Email : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_data['user_country_code']) ? $arr_data['user_country_code'] : '';
            $build_html .=  isset($arr_data['user_contact_no']) ? $arr_data['user_contact_no'] : '';
            
            if(isset($arr_data['user_email']) && $arr_data['user_email']!='')
            {
                $build_html .=  ' / ';
                $build_html .=  isset($arr_data['user_email']) ? $arr_data['user_email'] : '';
            }
            
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';

            $driver_name  = isset($arr_data['driver_name']) ? $arr_data['driver_name'] : '';
            $company_name = isset($arr_data['company_name']) ? $arr_data['company_name'] : '';
            $driver_name  = $driver_name.' ('.$company_name.')';

            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Driver Name : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  $driver_name;
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';

            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Driver Contact No / Email : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_data['driver_country_code']) ? $arr_data['driver_country_code'] : '';
            $build_html .=  isset($arr_data['driver_contact_no']) ? $arr_data['driver_contact_no'] : '';
            
            if(isset($arr_data['driver_email']) && $arr_data['driver_email']!='')
            {
                $build_html .=  ' / ';
                $build_html .=  isset($arr_data['driver_email']) ? $arr_data['driver_email'] : '';
            }
            
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';

            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Pick Up Location : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_data['pickup_location']) ? $arr_data['pickup_location'] : '';
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';

            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Drop Up Location : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  isset($arr_data['drop_location']) ? $arr_data['drop_location'] : '';
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';

            if(isset($arr_data['booking_status']) && $arr_data['booking_status'] == 'CANCEL_BY_USER')
            {
                $build_html .=  '<div class="review-detais">';
                $build_html .=  '<div class="boldtxts">Cancellation Base Price : </div>';
                $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                $build_html .=  isset($arr_data['cancellation_base_price']) ? number_format($arr_data['cancellation_base_price'],2) : '0.0';
                $build_html .= '</div>';
                $build_html .=  '<div class="clearfix"></div>';
                $build_html .=  '</div>';
            }
            
            if(isset($arr_data['booking_status']) && $arr_data['booking_status'] == 'COMPLETED')
            {

                $build_html .=  '<div class="review-detais">';
                $build_html .=  '<div class="boldtxts">Total Traveled Distance : </div>';
                $build_html .=  '<div class="rightview-txt">';
                $build_html .=  isset($arr_data['distance']) ? number_format($arr_data['distance'],2) : '0.0';
                $build_html .= ' <strong>Miles</strong></div>';
                $build_html .=  '<div class="clearfix"></div>';
                $build_html .=  '</div>';

                $build_html .=  '<div class="review-detais">';
                $build_html .=  '<div class="boldtxts">Vehicle Owner : </div>';
                $build_html .=  '<div class="rightview-txt">';
                $build_html .=  isset($arr_data['vehicle_owner']) ? $arr_data['vehicle_owner'] : '';
                $build_html .= '</div>';
                $build_html .=  '<div class="clearfix"></div>';
                $build_html .=  '</div>';

                if(isset($arr_data['is_bonus_used']) && $arr_data['is_bonus_used'] == 'YES')
                {
                    $build_html .=  '<div class="review-detais">';
                    $build_html .=  '<div class="boldtxts">Per USD Bonus point Charge : </div>';
                    $build_html .=  '<div class="rightview-txt"></i> ';
                    $build_html .=  isset($arr_data['admin_referral_points_price_per_usd']) ? intval($arr_data['admin_referral_points_price_per_usd']) : '0';
                    $build_html .= ' <strong>Points</strong> </div>';
                    $build_html .=  '<div class="clearfix"></div>';
                    $build_html .=  '</div>';

                    $build_html .=  '<div class="review-detais">';
                    $build_html .=  '<div class="boldtxts">Bonus Points Used : </div>';
                    $build_html .=  '<div class="rightview-txt"></i> ';
                    $build_html .=  isset($arr_data['user_bonus_points']) ? intval($arr_data['user_bonus_points']) : '0';
                    $build_html .= ' <strong>Points</strong> </div>';
                    $build_html .=  '<div class="clearfix"></div>';
                    $build_html .=  '</div>';
                }

                if(isset($arr_data['is_individual_vehicle']) && $arr_data['is_individual_vehicle'] == '1')
                {
                    $build_html .=  '<div class="review-detais">';
                    $build_html .=  '<div class="boldtxts">Commssion % from Individual Driver : </div>';
                    $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                    $build_html .=  isset($arr_data['individual_driver_percentage']) ? number_format($arr_data['individual_driver_percentage'],2) : '0.0';
                    $build_html .= '</div>';
                    $build_html .=  '<div class="clearfix"></div>';
                    $build_html .=  '</div>';
                }
                elseif(isset($arr_data['is_individual_vehicle']) && $arr_data['is_individual_vehicle'] == '0')
                {
                    if(isset($arr_data['is_company_driver']) && $arr_data['is_company_driver'] == '1')
                    {
                        $build_html .=  '<div class="review-detais">';
                        $build_html .=  '<div class="boldtxts">Admin Commssion % from company : </div>';
                        $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                        $build_html .=  isset($arr_data['admin_company_percentage']) ? number_format($arr_data['admin_company_percentage'],2) : '0.0';
                        $build_html .= '</div>';
                        $build_html .=  '<div class="clearfix"></div>';
                        $build_html .=  '</div>';

                        $build_html .=  '<div class="review-detais">';
                        $build_html .=  '<div class="boldtxts">Company Commssion % to driver : </div>';
                        $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                        $build_html .=  isset($arr_data['company_driver_percentage']) ? number_format($arr_data['company_driver_percentage'],2) : '0.0';
                        $build_html .= '</div>';
                        $build_html .=  '<div class="clearfix"></div>';
                        $build_html .=  '</div>';

                               
                    }
                    elseif(isset($arr_data['is_company_driver']) && $arr_data['is_company_driver'] == '0')
                    {
                        $build_html .=  '<div class="review-detais">';
                        $build_html .=  '<div class="boldtxts">Commssion % to Admin Driver : </div>';
                        $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                        $build_html .=  isset($arr_data['admin_driver_percentage']) ? number_format($arr_data['admin_driver_percentage'],2) : '0.0';
                        $build_html .= '</div>';
                        $build_html .=  '<div class="clearfix"></div>';
                        $build_html .=  '</div>';
                    }
                }

                $build_html .=  '<div class="review-detais">';
                $build_html .=  '<div class="boldtxts">Total Amount : </div>';
                $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                $build_html .=  isset($arr_data['total_amount']) ? number_format($arr_data['total_amount'],2) : '0.0';
                $build_html .= '</div>';
                $build_html .=  '<div class="clearfix"></div>';
                $build_html .=  '</div>';
      
                $build_html .=  '<div class="review-detais">';
                $build_html .=  '<div class="boldtxts">Discount Amount : </div>';
                $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                $build_html .=  isset($arr_data['applied_promo_code_charge']) ? number_format($arr_data['applied_promo_code_charge'],2) : '0.0';
                $build_html .= '</div>';
                $build_html .=  '<div class="clearfix"></div>';
                $build_html .=  '</div>';

                if(isset($arr_data['is_bonus_used']) && $arr_data['is_bonus_used'] == 'YES')
                {
                    $build_html .=  '<div class="review-detais">';
                    $build_html .=  '<div class="boldtxts">Bonus Points Amount Applied : </div>';
                    $build_html .=  '<div class="rightview-txt"></i> <i class="fa fa-usd"></i> ';
                    $build_html .=  isset($arr_data['user_bonus_points_usd_amount']) ? number_format($arr_data['user_bonus_points_usd_amount'],2) : '0.0';
                    $build_html .= '</div>';
                    $build_html .=  '<div class="clearfix"></div>';
                    $build_html .=  '</div>';
                }
        

                $build_html .=  '<div class="review-detais">';
                $build_html .=  '<div class="boldtxts">User Paid Amount : </div>';
                $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                $build_html .=  isset($arr_data['total_charge']) ? number_format($arr_data['total_charge'],2) : '0.0';
                $build_html .= '</div>';
                $build_html .=  '<div class="clearfix"></div>';
                $build_html .=  '</div>';
        
                if(isset($arr_data['is_individual_vehicle']) && $arr_data['is_individual_vehicle'] == '1')
                {
                    $build_html .=  '<div class="review-detais">';
                    $build_html .=  '<div class="boldtxts">Admin Commission Amount : </div>';
                    $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                    $build_html .=  isset($arr_data['admin_earning_amount']) ? number_format($arr_data['admin_earning_amount'],2) : '0.0';
                    $build_html .= '</div>';
                    $build_html .=  '<div class="clearfix"></div>';
                    $build_html .=  '</div>';

                    $build_html .=  '<div class="review-detais">';
                    $build_html .=  '<div class="boldtxts">Driver Earning Amount : </div>';
                    $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                    $build_html .=  isset($arr_data['driver_earning_amount']) ? number_format($arr_data['driver_earning_amount'],2) : '0.0';
                    $build_html .= '</div>';
                    $build_html .=  '<div class="clearfix"></div>';
                    $build_html .=  '</div>';

                }
                elseif(isset($arr_data['is_individual_vehicle']) && $arr_data['is_individual_vehicle'] == '0')
                {
                    if(isset($arr_data['is_company_driver']) && $arr_data['is_company_driver'] == '1')
                    {
                        $build_html .=  '<div class="review-detais">';
                        $build_html .=  '<div class="boldtxts">Admin Commission Amount : </div>';
                        $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                        $build_html .=  isset($arr_data['admin_earning_amount']) ? number_format($arr_data['admin_earning_amount'],2) : '0.0';
                        $build_html .= '</div>';
                        $build_html .=  '<div class="clearfix"></div>';
                        $build_html .=  '</div>';

                        $build_html .=  '<div class="review-detais">';
                        $build_html .=  '<div class="boldtxts">Company Earning Amount : </div>';
                        $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                        $build_html .=  isset($arr_data['company_earning_amount']) ? number_format($arr_data['company_earning_amount'],2) : '0.0';
                        $build_html .= '</div>';
                        $build_html .=  '<div class="clearfix"></div>';
                        $build_html .=  '</div>';

                        $build_html .=  '<div class="review-detais">';
                        $build_html .=  '<div class="boldtxts">Company Driver Earning Amount : </div>';
                        $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                        $build_html .=  isset($arr_data['driver_earning_amount']) ? number_format($arr_data['driver_earning_amount'],2) : '0.0';
                        $build_html .= '</div>';
                        $build_html .=  '<div class="clearfix"></div>';
                        $build_html .=  '</div>';

                        $build_html .=  '<div class="review-detais">';
                        $build_html .=  '<div class="boldtxts">Total Company Earning Amount : </div>';
                        $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                        $build_html .=  isset($arr_data['total_company_earning_amount']) ? number_format($arr_data['total_company_earning_amount'],2) : '0.0';
                        $build_html .= '</div>';
                        $build_html .=  '<div class="clearfix"></div>';
                        $build_html .=  '</div>';
                    }
                    elseif(isset($arr_data['is_company_driver']) && $arr_data['is_company_driver'] == '0')
                    {
                        $build_html .=  '<div class="review-detais">';
                        $build_html .=  '<div class="boldtxts">Admin Earning Amount : </div>';
                        $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                        $build_html .=  isset($arr_data['admin_earning_amount']) ? number_format($arr_data['admin_earning_amount'],2) : '0.0';
                        $build_html .= '</div>';
                        $build_html .=  '<div class="clearfix"></div>';
                        $build_html .=  '</div>';

                        $build_html .=  '<div class="review-detais">';
                        $build_html .=  '<div class="boldtxts">Driver Commission Amount : </div>';
                        $build_html .=  '<div class="rightview-txt"> <i class="fa fa-usd"></i> ';
                        $build_html .=  isset($arr_data['driver_earning_amount']) ? number_format($arr_data['driver_earning_amount'],2) : '0.0';
                        $build_html .= '</div>';
                        $build_html .=  '<div class="clearfix"></div>';
                        $build_html .=  '</div>';
                    }
                }             
            }

            $payment_status = '';
            if(isset($arr_data['payment_status']) && $arr_data['payment_status'] == 'PENDING')
            {
                $payment_status = '<span class="badge badge-warning" style="width:100px">Pending</span>';
            }
            else if(isset($arr_data['payment_status']) && $arr_data['payment_status'] == 'SUCCESS')
            {
                $payment_status = '<span class="badge badge-success" style="width:100px">Success</span>';
            }
            else if(isset($arr_data['payment_status']) && $arr_data['payment_status'] == 'FAILED')
            {
                $payment_status = '<span class="badge badge-important" style="width:100px">Failed</span>';
            }

            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Payment Status : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  $payment_status;
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';


            $booking_status = '';
            if(isset($arr_data['booking_status']) && $arr_data['booking_status'] == 'TO_BE_PICKED')
            {
                $booking_status = '<span class="badge badge-info" style="width:100px">To be picked</span>';
            }
            else if(isset($arr_data['booking_status']) && $arr_data['booking_status'] == 'IN_TRANSIT')
            {
                $booking_status = '<span class="badge badge-warning" style="width:100px">In transit</span>';
            }
            else if(isset($arr_data['booking_status']) && $arr_data['booking_status'] == 'COMPLETED')
            {
                $booking_status = '<span class="badge badge-success" style="width:100px">Completed</span>';
            }
            else if(isset($arr_data['booking_status']) && $arr_data['booking_status'] == 'CANCEL_BY_USER')
            {
                $booking_status = '<span class="badge badge-important" style="width:100px">Cancel by user</span>';
            }
            else if(isset($arr_data['booking_status']) && $arr_data['booking_status'] == 'CANCEL_BY_DRIVER')
            {
                $booking_status = '<span class="badge badge-important" style="width:100px">Cancel by driver</span>';
            }
            

            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Booking status : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  $booking_status;
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';

            $payment_type = '<span class="badge badge-warning" style="width:100px">NA</span>';

            if(isset($arr_data['payment_type']) && $arr_data['payment_type'] == 'STRIPE')
            {
                $payment_type = '<span class="badge badge-success" style="width:100px">Stripe</span>';
            }
            else if(isset($arr_data['payment_type']) && $arr_data['payment_type'] == 'PAYPAL')
            {
                $payment_type = '<span class="badge badge-info" style="width:100px">Paypal</span>';
            }
            
            $build_html .=  '<div class="review-detais">';
            $build_html .=  '<div class="boldtxts">Payment Type : </div>';
            $build_html .=  '<div class="rightview-txt">';
            $build_html .=  $payment_type;
            $build_html .= '</div>';
            $build_html .=  '<div class="clearfix"></div>';
            $build_html .=  '</div>';
        }

        $arr_response = [];

        $arr_response['status']         = 'error';
        $arr_response['generated_html'] = '';
        
        if($build_html!='')
        {
            $arr_response['status']         = 'success';
            $arr_response['generated_html'] = $build_html;
        }

        return response()->json($arr_response); 
    }

    private function get_company_balance_information()
    {
        $company_total_amount     = 0;
        $company_paid_amount      = 0;
        $company_unpaid_amount    = 0;

        $arr_result = [];
        
        $arr_result['company_total_amount']     = $company_total_amount;
        $arr_result['company_paid_amount']      = $company_paid_amount;
        $arr_result['company_unpaid_amount']    = $company_unpaid_amount;

        $total_driver_earning_amount = 0;

        $obj_company_account_balance = $this->BookingMasterModel
                                                ->whereHas('load_post_request_details',function($query) {
                                                    $query->whereHas('driver_details',function($query){
                                                        $query->where('company_id',$this->company_id);
                                                        $query->where('is_company_driver','1');
                                                    });
                                                })           
                                                ->where('booking_status','COMPLETED')
                                                ->get();
        $arr_admin_account_balance = [];
        if($obj_company_account_balance)
        {
            $arr_company_account_balance = $obj_company_account_balance->toArray();
        }

        if(isset($arr_company_account_balance) && sizeof($arr_company_account_balance)>0)
        {
            foreach ($arr_company_account_balance as $key => $value) 
            {
                $booking_status           = isset($value['booking_status']) ? $value['booking_status'] :'';
                $company_amount           = isset($value['company_amount']) ? floatval($value['company_amount']):0;
                $company_driver_amount    = isset($value['company_driver_amount']) ? floatval($value['company_driver_amount']):0;
                $is_company_driver        = isset($value['is_company_driver']) ? $value['is_company_driver']:0;
                $is_individual_vehicle    = isset($value['is_individual_vehicle']) ? $value['is_individual_vehicle']:0;

                $company_earning_amount = 0;
                
                if($is_individual_vehicle == '0')
                {    
                    if($is_company_driver == '1')
                    {
                        $company_earning_amount = ($company_amount + $company_driver_amount);    
                    }
                }
                $company_total_amount  = (floatval($company_total_amount) + floatval($company_earning_amount));
            }
        }
       
        /*company earning amount from admin*/ 
        $obj_company_paid_amount = $this->DepositMoneyModel
                                                ->select('id','to_user_id','amount_paid','status')
                                                ->where([
                                                            'to_user_id'   => $this->company_id,
                                                            'to_user_type' => 'COMPANY',
                                                            'status'       => 'SUCCESS'
                                                        ])
                                                ->get();
        $arr_company_paid_amount =[];
        if($obj_company_paid_amount)
        {
            $arr_company_paid_amount = $obj_company_paid_amount->toArray();
        }
        if(isset($arr_company_paid_amount) && sizeof($arr_company_paid_amount)>0)
        {
            foreach ($arr_company_paid_amount as $key => $value) 
            {
                $amount_paid = isset($value['amount_paid']) ? $value['amount_paid'] :0;

                $company_paid_amount = (floatval($company_paid_amount) + floatval($amount_paid));
            }
        } 
        
        /*company driver earning amount from admin*/ 
        $obj_company_driver_paid_amount = $this->DepositMoneyModel
                                                ->select('id','from_user_id','amount_paid','status')
                                                ->where([
                                                            'from_user_id'   => $this->company_id,
                                                            'from_user_type' => 'COMPANY',
                                                            'status'         => 'SUCCESS'
                                                        ])
                                                ->get();

        $arr_company_driver_paid_amount =[];
        if($obj_company_driver_paid_amount)
        {
            $arr_company_driver_paid_amount = $obj_company_driver_paid_amount->toArray();
        }

        if(isset($arr_company_driver_paid_amount) && sizeof($arr_company_driver_paid_amount)>0)
        {
            foreach ($arr_company_driver_paid_amount as $key => $value) 
            {
                $amount_paid = isset($value['amount_paid']) ? $value['amount_paid'] :0;

                $company_paid_amount = (floatval($company_paid_amount) + floatval($amount_paid));
            }
        }

        if($company_total_amount>$company_paid_amount)
        {
            $company_unpaid_amount = (floatval($company_total_amount) - floatval($company_paid_amount));
            $company_unpaid_amount = $company_unpaid_amount;
        }
        
        $arr_result['company_total_amount']    = $company_total_amount;
        $arr_result['company_paid_amount']     = $company_paid_amount;
        $arr_result['company_unpaid_amount']   = $company_unpaid_amount;

        return $arr_result;
    }

}