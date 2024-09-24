<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;


use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\UserModel;
use App\Models\VehicleModel;
use App\Models\BookingMasterModel;

use DB;
use Sentinel;

class DashboardController extends Controller
{
	public function __construct(UserModel 		    $user,
							 	VehicleModel        $vehicle_model,
							 	BookingMasterModel  $booking_model)
	{
		// dd('in constr');
		$this->arr_view_data      = [];
		$this->module_title       = "Dashboard";
		$this->UserModel          = $user;
		$this->VehicleModel  	   = $vehicle_model;
		$this->BookingMasterModel  	   = $booking_model;
		$this->module_view_folder = "company.dashboard";
		$this->admin_url_path     = url(config('app.project.company_panel_slug'));

		$this->company_id = 0;

        $user = Sentinel::check();
        if($user){
            $this->company_id   = isset($user->id) ? $user->id :0;
        }
	}
   
    public function index(Request $request)
    {
    	$arr_tile_color  = array('tile-red','tile-green','tile-magenta','');

        $current_year    = date('Y');
        $to_date         = date('Y-m-d');

    	$vehicle_count =  $driver_count = $booking_count =
    	$today_driver_count = $monthly_driver_count = $yearly_driver_count =  0;
		
		$vehicle_count  	   =  $this->VehicleModel->where(['is_company_vehicle'=>'1','company_id'=>$this->company_id])->count();

		 $driver_count 	       = $this->UserModel
			    						  ->whereHas('roles',function($query) {
				                                $query->where(function($query) {
				                                    $query->where('slug','=','driver');
				                                });
				                                })
			    						  ->where('is_company_driver','1')
			    						  ->where('company_id',$this->company_id)
			    						  ->count();


		   $today_driver_count 	   = $this->UserModel
			    						  ->whereHas('roles',function($query) {
				                                $query->where(function($query) {
				                                    $query->where('slug','=','driver');
				                                });
				                                })
			    						   ->where('is_company_driver','1')
			    						  ->whereDate('created_at','=',date("Y-m-d"))
			    						  ->count();

		  $monthly_driver_count 	   = $this->UserModel
			    						  ->whereHas('roles',function($query) {
				                                $query->where(function($query) {
				                                    $query->where('slug','=','driver');
				                                });
				                                })
			    						   ->where('is_company_driver','1')
			    						   ->whereDate('created_at','>=',\Carbon\Carbon::now()->startOfMonth())
	                                  	   ->whereDate('created_at','<=',$to_date)
			    						   ->count();

		  $yearly_driver_count 	   = $this->UserModel
			    						  ->whereHas('roles',function($query) {
				                                $query->where(function($query) {
				                                    $query->where('slug','=','driver');
				                                });
				                                })
			    						   ->where('is_company_driver','1')
			    						   ->whereYear('created_at','=',$current_year)
			    						   ->count();

	     $booking_count 		   =  $this->BookingMasterModel
		                              ->whereHas('load_post_request_details',function($query) {
		                                  $query->whereHas('driver_details',function($query){
		                                      $query->where('company_id',$this->company_id);
		                                      $query->where('is_company_driver','1');
		                                  });
		                              })   
		                              ->with(['load_post_request_details'=>function($query){
		                                  $query->with(['driver_details'=>function($query){
		                                      $query->select('id','company_id','is_company_driver');
		                                      $query->where('company_id',$this->company_id);
		                                      $query->where('is_company_driver','1');

		                                  }]);
		                              }])->count();



        
                                


        $arr_encoded_user_drivers_details = $this->get_latest_user_drivers_company_details();
	    $arr_encoded_ride_details = $this->get_latest_ride_details();
	   // dd($arr_encoded_user_drivers_details);
    	$this->arr_view_data['arr_encoded_ride_details'] = $arr_encoded_ride_details;
    	$this->arr_view_data['arr_encoded_user_drivers_details'] = $arr_encoded_user_drivers_details;
    	$this->arr_view_data['page_title']            = $this->module_title;
    	$this->arr_view_data['admin_url_path']        = $this->admin_url_path;
    	$this->arr_view_data['arr_tile_color']        = $arr_tile_color;
    	$this->arr_view_data['vehicle_count']         = $vehicle_count;
    	$this->arr_view_data['driver_count']          = $driver_count;
    	$this->arr_view_data['today_driver_count']    = $today_driver_count;
    	$this->arr_view_data['monthly_driver_count']  = $monthly_driver_count;
    	$this->arr_view_data['yearly_driver_count']   = $yearly_driver_count;
    	$this->arr_view_data['booking_count']         = $booking_count;
    	$this->arr_view_data['arr_final_tile']        = $this->built_dashboard_tiles($request);

    	return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

    public function built_dashboard_tiles($request)
    {
    	/*------------------------------------------------------------------------------
    	| Note: Directly Use icon name - like, fa fa-user and use directly - 'user'
    	------------------------------------------------------------------------------*/
		
		$arr_final_tile = [];

		$user = Sentinel::check();
      
  	    $admin_type = "";
	    
  	    if($user)
  	    {
  	    		$arr_current_user_access = $request->user()->permissions;
       

		  	   
					$arr_final_tile[] = ['module_slug'  => 'account_settings',
										  'css_class'   => 'cogs',
										  'module_title'=> 'Account Settings'];
				
		  	    	$arr_final_tile[] = ['module_slug'  => 'site_settings',
										  'css_class'   => 'wrench',
										  'module_title'=> 'Site Settings'];
					
					$arr_final_tile[] = ['module_slug'  => 'admin_users',
										  'css_class'   => 'user-secret',
										  'module_title'=> 'Admin Users'];	
				
					$arr_final_tile[] = ['module_slug'  => 'contact_enquiry',
										  'css_class'   => 'info-circle',
										  'module_title'=> 'Contact Enquiries'];	
				
					$arr_final_tile[] = ['module_slug'  => 'static_pages',
										  'css_class'   => 'sitemap',
										  'module_title'=> 'CMS'];
				
					$arr_final_tile[] = ['module_slug'  => 'email_template',
										  'css_class'   => 'envelope',
										  'module_title'=> 'Email Templates'];
				
					$arr_final_tile[] = ['module_slug'  => 'assigned_area',
										  'css_class'   => 'map-marker',
										  'module_title'=> 'Assigned Area'];
				
		  	    	$arr_final_tile[] = ['module_slug'  => 'vehicle',
										  'css_class'   => 'car',
										  'module_title'=> 'Vehicle'];
				
		  	    	$arr_final_tile[] = ['module_slug'  => 'rider',
										  'css_class'   => 'users',
										  'module_title'=> 'Rider'];
				
		  	    	$arr_final_tile[] = ['module_slug'  => 'driver',
										  'css_class'   => 'users',
										  'module_title'=> 'Driver'];
				
		  	    	$arr_final_tile[] = ['module_slug'  => 'advertisement',
										  'css_class'   => 'newspaper-o',
										  'module_title'=> 'Advertisement'];
				
		  	    	$arr_final_tile[] = ['module_slug'  => 'promo_offer',
										  'css_class'   => 'gift',
										  'module_title'=> 'Promo Offer'];
				
		  	    	$arr_final_tile[] = ['module_slug'  => 'driver_vehicle',
										  'css_class'   => 'car',
										  'module_title'=> 'Driver Vehicle'];
				
		  	    	$arr_final_tile[] = ['module_slug'  => 'admin_commission',
										  'css_class'   => 'file-text-o',
										  'module_title'=> 'Admin Commission' ];
								
		}		
		return 	$arr_final_tile;						  
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
            $arr_current = array(['Months',  'Drivers']);

            foreach ($arr_months as $key => $value) 
            {
                $first_day_this_month = date('Y-m-01',strtotime($value));
                $last_day_this_month  = date('Y-m-t',strtotime($value));

                $users_count           = 0;
                
                $drivers_count           = $this->UserModel
                                        ->whereHas('roles',function($query) {
                                            $query->where(function($query) {
                                                $query->where('slug','=','driver');
                                            });
                                        })
                                        ->whereRaw("DATE(created_at) >= '".$first_day_this_month."'")
                                        ->whereRaw("DATE(created_at) <= '".$last_day_this_month."'")
                                        ->where('company_id',$this->company_id)
                                        ->count();

                $company_count           =0;


                $formated_date = date('F-Y',strtotime($value));
                $arr_current[]= array($formated_date, $drivers_count );
                $arr_user_drivers_details = $arr_current;
            }

            return json_encode($arr_user_drivers_details); 
        }
    }
    public function get_latest_ride_details()
    { 
        $arr_days = $arr_latest_ride_details = [];
        $company_id = $this->company_id;
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
                                               ->whereHas('load_post_request_details',function($query)use($company_id){

                                               		$query->whereHas('driver_details',function($q)use($company_id){
                                               			$q->where('company_id',$company_id);

                                               		});
                                                })
                                               ->with(['load_post_request_details'=>function($query)use($company_id){

                                               		$query->with(['driver_details'=>function($q)use($company_id){
                                               			$q->where('company_id',$company_id);

                                               		}]);
                                                }])
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

