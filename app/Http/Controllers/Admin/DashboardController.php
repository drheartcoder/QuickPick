<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;


use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\UserModel;
use App\Models\VehicleModel;
use App\Models\VehicleTypeModel;
use App\Models\ContactEnquiryModel;
use App\Models\EmailTemplateModel;
use App\Models\BookingMasterModel;




use DB;
use Sentinel;
use Flash;
use Validator;
use Datatables;

class DashboardController extends Controller
{
	public function __construct(UserModel 		    $user,
								ContactEnquiryModel $contact_enquiry,
								EmailTemplateModel  $email_template,
							 	VehicleModel        $vehicle_model,
							 	BookingMasterModel  $booking_model
                                )
	{
		$this->arr_view_data       = [];
		$this->module_title        = "Dashboard";
		$this->UserModel           = $user;
		$this->ContactEnquiryModel = $contact_enquiry;
		$this->EmailTemplateModel  = $email_template;
		$this->VehicleModel  	   = $vehicle_model;
		$this->BookingMasterModel  = $booking_model;
         $this->theme_color        = theme_color();
		$this->module_view_folder = "admin.dashboard";
		$this->admin_url_path     = url(config('app.project.admin_panel_slug'));
        
	}

   
    public function index(Request $request)
    {
        $arr_encoded_user_drivers_details = $this->get_latest_user_drivers_company_details();
	    $arr_encoded_ride_details = $this->get_latest_ride_details();
	    $arr_dashboard_counts = $this->get_dashboard_count();

        $this->arr_view_data['page_title']                       = $this->module_title;
        $this->arr_view_data['admin_url_path']                   = $this->admin_url_path;
        $this->arr_view_data['arr_dashboard_counts']             = $arr_dashboard_counts;
        
        $this->arr_view_data['arr_encoded_user_drivers_details'] = $arr_encoded_user_drivers_details;
        $this->arr_view_data['arr_encoded_ride_details']         = $arr_encoded_ride_details;


    	return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

    private function get_dashboard_count()
    {

    	$users_count 	       = $this->UserModel
										->whereHas('roles',function($query) {
			                                $query->where(function($query) {
			                                    $query->where('slug','=','user');
			                                });
			                            })
		    						  ->count();

		$drivers_count 	       = $this->UserModel
										->whereHas('roles',function($query) {
			                                $query->where(function($query) {
			                                    $query->where('slug','=','driver');
			                                });
			                            })
		    						  ->count();

		$company_count 	       = $this->UserModel
										->whereHas('roles',function($query) {
			                                $query->where(function($query) {
			                                    $query->where('slug','=','company');
			                                });
			                            })
		    						  ->count();

		$booking_count 	  	   = $this->BookingMasterModel
		                              ->whereHas('load_post_request_details',function($query) {
		                              	$query->whereHas('driver_details',function($query){
		                                      $query->where('is_company_driver','0');
		                                });
		                              })->count();
		
		$arr_dashboard_count = 
    							[
    								'users_count'               => $users_count,
    								'drivers_count'             => $drivers_count,
    								'company_count'             => $company_count,
    								'verified_vehicle_count'    => $this->VehicleModel->where('is_verified','1')->count(),
    								'unverified_vehicle_count'  => $this->VehicleModel->where('is_verified','0')->count(),
    								'booking_count'             => $booking_count,
    								'contact_enquiry_count'     => $this->ContactEnquiryModel->count(),
    								'email_template_count'      => $this->EmailTemplateModel->count(),
    							];
    	
    	return $arr_dashboard_count;						
    }
   
    public function get_latest_user_drivers_company_details()
    { 
        $arr_months = $arr_user_drivers_details = [];

        for ($i = 0; $i < 6; $i++) {
            $arr_months[] = date('Y-m-d', strtotime("-$i month"));
        }
        if(isset($arr_months) && sizeof($arr_months)>0)
        {
            $arr_current = array();
            $arr_current = array(['Months', 'Users', 'Drivers','Companies']);

            foreach ($arr_months as $key => $value) 
            {
                $first_day_this_month = date('Y-m-01',strtotime($value));
                $last_day_this_month  = date('Y-m-t',strtotime($value));

                $users_count           = $this->UserModel
                                        ->whereHas('roles',function($query) {
                                            $query->where(function($query) {
                                                $query->where('slug','=','user');
                                            });
                                        })
                                        ->whereRaw("DATE(created_at) >= '".$first_day_this_month."'")
                                        ->whereRaw("DATE(created_at) <= '".$last_day_this_month."'")
                                        ->count();
                
                $drivers_count           = $this->UserModel
                                        ->whereHas('roles',function($query) {
                                            $query->where(function($query) {
                                                $query->where('slug','=','driver');
                                            });
                                        })
                                        ->whereRaw("DATE(created_at) >= '".$first_day_this_month."'")
                                        ->whereRaw("DATE(created_at) <= '".$last_day_this_month."'")
                                        ->count();

                $company_count           = $this->UserModel
                                        ->whereHas('roles',function($query) {
                                            $query->where(function($query) {
                                                $query->where('slug','=','company');
                                            });
                                        })
                                        ->whereRaw("DATE(created_at) >= '".$first_day_this_month."'")
                                        ->whereRaw("DATE(created_at) <= '".$last_day_this_month."'")
                                        ->count();


                $formated_date = date('F-Y',strtotime($value));
                $arr_current[]= array($formated_date,$users_count,$drivers_count,$company_count);
                $arr_user_drivers_details = $arr_current;
            }

            return json_encode($arr_user_drivers_details); 
        }
    }

    public function get_latest_ride_details()
    { 
        $arr_days = $arr_latest_ride_details = [];

        for ($i = 0; $i < 7; $i++) {
            $arr_days[] = date('Y-m-d', strtotime("-$i days"));
        }
        if(isset($arr_days) && sizeof($arr_days)>0)
        {
            $arr_current = array();
            $arr_current = array(['Day', 'Rides']);

            foreach ($arr_days as $key => $value) 
            {
                $booking_count  = $this->BookingMasterModel
                                                ->whereHas('load_post_request_details',function($query) {
                                                })
                                                ->whereRaw("DATE(booking_date) = '".$value."'")
                                                ->count();

                $formated_date = date('d M Y',strtotime($value));
                $arr_current[]= array($formated_date,$booking_count);
                $arr_latest_ride_details = $arr_current;
            }
        }
        return json_encode($arr_latest_ride_details); 
    }


}

