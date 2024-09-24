<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\VehicleModel;
use App\Models\DriverCarRelationModel;
use App\Models\BookingMasterModel;
use App\Models\DriverCarRelationHistoryModel;
use App\Common\Services\CommonDataService;
use App\Common\Services\NotificationsService;

use Flash;

class DriverCarController extends Controller
{
    public function __construct(
    								VehicleModel $vehicle,
                                    DriverCarRelationModel $driver_car_relation,
                                    BookingMasterModel $booking_master,
                                    CommonDataService $common_data_service,
                                    NotificationsService $notifications_service,
                                    DriverCarRelationHistoryModel $driver_car_relation_history
                                )
    {
    	$this->VehicleModel 				 = $vehicle;
        $this->DriverCarRelationModel 		 = $driver_car_relation;
        $this->BookingMasterModel            = $booking_master;
        $this->CommonDataService             = $common_data_service;
        $this->NotificationsService          = $notifications_service;
        $this->DriverCarRelationHistoryModel = $driver_car_relation_history;
        $this->arr_view_data                 = [];
        $this->module_title                  = "Driver Vehicle";
        $this->module_view_folder            = "admin.driver_car";
        $this->theme_color                   = theme_color();
        $this->admin_panel_slug              = config('app.project.admin_panel_slug');
        $this->module_url_path               = url(config('app.project.admin_panel_slug')."/driver_vehicle");
    } 
    public function index(Request $request)
    {   
        $vehicles_type = 'admin';
        if($request->has('vehicles_type') && $request->input('vehicles_type')!=''){
            $vehicles_type = $request->input('vehicles_type');
        }
        $obj_data = $this->DriverCarRelationModel
                                ->whereHas('driver_details',function($query) use($vehicles_type){
                                    if($vehicles_type == 'admin' || $vehicles_type == 'individual')
                                    {
                                        $query->where('is_company_driver','0');
                                    }
                                    if($vehicles_type == 'company' )
                                    {
                                        $query->where('is_company_driver','1');
                                    }
                                    // $query->where('is_deleted','0');
                                    // $query
                                })
                                ->select('id','driver_id','vehicle_id','is_car_assign','is_individual_vehicle')
                                ->with(['driver_details','vehicle_details.vehicle_type_details','vehicle_details.vehicle_brand_details','vehicle_details.vehicle_model_details']);

        if($vehicles_type!='' && $vehicles_type == 'admin'){

            $obj_data = $obj_data->where('is_individual_vehicle','0'); /*is_individual_vehicle == 0 means its admin car*/
        }
        else if($vehicles_type!='' && $vehicles_type == 'individual'){

            $obj_data = $obj_data->where('is_individual_vehicle','1'); /*is_individual_vehicle == 1 means its individual car*/
        }
        else{

            $obj_data = $obj_data->where('is_individual_vehicle','0'); /*is_individual_vehicle == 0 means its admin car*/
        } 

        $obj_data = $obj_data->orderBy('id','DESC')
                             ->get();

    	$arr_data = [];
    	if($obj_data)
    	{
    		$arr_data = $obj_data->toArray();
            //dd($arr_data);
    	}

		$this->arr_view_data['page_title']      = "Manage ".str_plural($this->module_title);
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['arr_data']        = $arr_data;
        $this->arr_view_data['vehicles_type']   = $vehicles_type;

        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }
    public function remove_car($enc_id)
    {
    	$enc_id = base64_decode($enc_id);
    	
    	$obj_data = $this->DriverCarRelationModel
    							->select('id','driver_id','vehicle_id','is_car_assign')
    							->where('id',$enc_id)
    							->first();
    	
    	if($obj_data!=null)
    	{
    		$driver_id  = isset($obj_data->driver_id) ? $obj_data->driver_id :0;

            if($this->check_driver_current_trip($driver_id) == false)
            {
                $vehicle_id = isset($obj_data->vehicle_id) ? $obj_data->vehicle_id :0;

        		$arr_history = 
        						[
        							'driver_id'  => $driver_id,
        							'vehicle_id' => $vehicle_id,
        							'status' 	 => 'REMOVE'
        						];

        		$this->DriverCarRelationHistoryModel->create($arr_history);
        		$obj_data->vehicle_id    = 0;
        		$obj_data->is_car_assign = 0;
        		$obj_data->save();

        		Flash::success('Car Successfully removed for driver.');
            }
            else
            {
                Flash::error('Driver is currently in busy in trip,cannot remove assigned vehicle');
            }
    	}
    	else
    	{
    		Flash::error('Problem Occurred, While Updating removing assigned car for driver.');
    	}
    	return redirect()->back();
    }
    public function assign_car(Request $request)
    {
        $driver_car_id = base64_decode($request->input('driver_car_id'));
    	$vehicle_id    = $request->input('vehicle_id');

    	if($driver_car_id == '' || $vehicle_id == '')
    	{
    		Flash::error('Something went wrong,cannot assgin car please try again.');
    	}

    	$obj_data = $this->DriverCarRelationModel
    								->where('id',$driver_car_id)
    								->first();

        //dd($obj_data->driver_id);


    	if($obj_data)
    	{
            $user_id = isset($obj_data->driver_id)  ? $obj_data->driver_id :0;
            
            if($this->check_driver_current_trip($user_id) == false)
            {
        		if((isset($obj_data->vehicle_id) && isset($obj_data->is_car_assign)) && ($obj_data->vehicle_id!=0 && $obj_data->is_car_assign==1) )
        		{
        			/*If already has previous car then maintain history as remove*/
        			$arr_history = 
        						[
        							'driver_id'  => isset($obj_data->driver_id)  ? $obj_data->driver_id :0,
        							'vehicle_id' => isset($obj_data->vehicle_id) ? $obj_data->vehicle_id :0,
        							'status' 	 => 'REMOVE'
        						];

        			$this->DriverCarRelationHistoryModel->create($arr_history);

        		}
        		
        		$obj_data->vehicle_id    = $vehicle_id;
        		$obj_data->is_car_assign = 1;
        		$staus = $obj_data->save();
        		if($staus)
        		{
        			/*new car assign maintain in history*/
        			$arr_history = 
        						[
        							'driver_id'  => isset($obj_data->driver_id)  ? $obj_data->driver_id :0,
        							'vehicle_id' => $vehicle_id,
        							'status' 	 => 'ASSIGN'
        						];

        			$this->DriverCarRelationHistoryModel->create($arr_history);

          
                    $arr_user_data      = $this->CommonDataService->get_user_details($user_id);
                    $arr_vehicle_info   = $this->CommonDataService->get_vehicle_details($vehicle_id);

                    $arr_data_info = array_merge($arr_user_data,$arr_vehicle_info['0']);

                    $arr_notification_data = $this->built_notification_data_info($arr_data_info); 
                    $this->NotificationsService->store_notification($arr_notification_data);

                    //send one signal notification to driver
                     $arr_notification_data = 
                                            [
                                                'title'             => 'Admin Assigned car to you',
                                                'notification_type' => 'CAR_ASSIGN',
                                                'enc_user_id'       => $user_id,
                                                'user_type'         => 'DRIVER',

                                            ];
                   $this->NotificationsService->send_on_signal_notification($arr_notification_data);

        			Flash::success('Vehicle Successfully assigned to driver.');
        		}	
        		else
        		{
        			Flash::error('Something went wrong,cannot assgin car please try again.');
        		}
            }
            else
            {
                Flash::error('Driver is currently in busy in trip,cannot remove assigned or update vehicle');
            }
    	}
    	else
    	{
			Flash::error('Something went wrong,cannot assgin car please try again.');
    	}
    	return redirect()->back();

    }
    public function get_cars(Request $request)
    {

	    $arr_data = $arr_result = [];
        $id = base64_decode($request->input('enc_id'));
        
        $obj_driver_car_relation = $this->DriverCarRelationModel
        											->select('vehicle_id')
        											->where('vehicle_id','!=',0)
        											->where('is_car_assign',1)
        											->get();
        
        $arr_tmp_vehicle_id = [];
        if($obj_driver_car_relation)
        {
        	$arr_tmp_vehicle_id = $obj_driver_car_relation->toArray();
    	}
    	$arr_vehicle_id = [];

		if(isset($arr_tmp_vehicle_id) && sizeof($arr_tmp_vehicle_id)>0)
		{
			foreach ($arr_tmp_vehicle_id as $key => $value) 
			{
				$arr_vehicle_id[] = isset($value['vehicle_id'])?$value['vehicle_id']:0;
			}
		}

		$obj_data = $this->VehicleModel
        					->select('id','vehicle_type_id','vehicle_number')
        					->with('vehicle_type_details')
        					->whereNotIn('id',$arr_vehicle_id)
                            ->where(['is_active'=>1,'is_company_vehicle'=>0,'is_individual_vehicle'=>0])
                            ->get();
        
        if($obj_data)
        {
            $arr_data = $obj_data->toArray();
        }

        if(isset($arr_data) && sizeof($arr_data)>0)
        {
        	foreach ($arr_data as $key => $value) 
        	{

        		$id = isset($value['id']) ? $value['id'] :0;

        		$vehicle_number        = isset($value['vehicle_number']) ? $value['vehicle_number'] :'';
        		
        		
                $vehicle_type        = isset($value['vehicle_type_details']['vehicle_type']) ? $value['vehicle_type_details']['vehicle_type'] :'';
        		
                $vehicle_full_name 	 = $vehicle_type.' ('.$vehicle_number.')';
        		
        		$arr_tmp['id']                = $id;
        		$arr_tmp['vehicle_full_name'] = $vehicle_full_name;
        		array_push($arr_result, $arr_tmp);
        	}
        }
        if(sizeof($arr_result)>0)
        {
			return response()->json(['status'=>'success','arr_vehicle'=> $arr_result]);
        }

        return response()->json(['status'=>'error']);
    }

    public function check_driver_current_trip($driver_id)
    {
        $arr_booking_master = [];
        $obj_booking_master = $this->BookingMasterModel
                                                        ->select('id','booking_status')
                                                        ->whereHas('load_post_request_details',function($query) use($driver_id){
                                                            $query->where('driver_id',$driver_id);
                                                        })
                                                        ->whereIn('booking_status',['TO_BE_PICKED','IN_TRANSIT'])
                                                        ->orderBy('id','DESC')
                                                        ->first();
        if($obj_booking_master){
            return true;
        }
        return false;
    }
    private function built_notification_data_info($arr_data_info)
    {
            $arr_notification = [];
            if(isset($arr_data_info) && sizeof($arr_data_info)>0)
            {
                $vehicle_name = isset($arr_data_info['vehicle_name']) ? $arr_data_info['vehicle_name'] :'';
                $vehicle_number = isset($arr_data_info['vehicle_number']) ? $arr_data_info['vehicle_number'] :'';
                $first_name = isset($arr_data_info['first_name']) ? $arr_data_info['first_name'] :'';
                $last_name  = isset($arr_data_info['last_name']) ? $arr_data_info['last_name'] :'';
                $full_name  = $first_name.' '.$last_name;
                $full_name  = ($full_name!=' ') ? $full_name : '-';

                //$arr_notification['user_id']           = $this->CommonDataService->get_admin_id();
                $arr_notification['user_id']           = $arr_data_info['id'];
                $arr_notification['is_read']           = 0;
                $arr_notification['is_show']           = 0;
                $arr_notification['user_type']         = 'DRIVER';
                $arr_notification['notification_type'] = 'Car Assign';
                $arr_notification['title']             = 'You have been assigned for'.$vehicle_name.' car and Car number is'.$vehicle_number;
                $arr_notification['view_url']          = '/'.config('app.project.admin_panel_slug')."/driver_vehicle"; //$this->module_url_path;

        }
        return $arr_notification;
    }
}
