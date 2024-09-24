<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\VehicleModel;
use App\Models\DriverCarRelationModel;
use App\Models\DriverCarRelationHistoryModel;
use App\Common\Services\CommonDataService;
use App\Common\Services\NotificationsService;

use Sentinel;
use Flash;


class DriverCarController extends Controller
{
    public function __construct(
    								VehicleModel $vehicle,
                                    CommonDataService $common_data_service,
                                    NotificationsService $notifications_service,
                                    DriverCarRelationModel $driver_car_relation,
                                    DriverCarRelationHistoryModel $driver_car_relation_history
                                )
    {

    	$this->VehicleModel 				 = $vehicle;
        $this->CommonDataService             = $common_data_service;
        $this->NotificationsService          = $notifications_service;
        $this->DriverCarRelationModel 		 = $driver_car_relation;
        $this->DriverCarRelationHistoryModel = $driver_car_relation_history;
        $this->arr_view_data                 = [];
        $this->module_title                  = "Driver Vehicles";
        $this->module_view_folder            = "company.driver_car";
        $this->theme_color                   = theme_color();
        $this->admin_panel_slug              = config('app.project.company_panel_slug');
        $this->module_url_path               = url(config('app.project.company_panel_slug')."/driver_vehicle");
        
        $this->company_id = 0;

        $user = Sentinel::check();
        if($user){
            $this->company_id = isset($user->id) ? $user->id :0;
        }
    }

    public function index(Request $request)
    {
        $company_id = $this->company_id;
        $obj_data = $this->DriverCarRelationModel
    							->select('id','driver_id','vehicle_id','is_car_assign')
    							->with(['driver_details' => function ($query) use($company_id){
                                                                    $query->where('company_id', $company_id);
                                                                }])
                                ->whereHas('driver_details' , function ($query) use($company_id){
                                                                    $query->where('company_id', $company_id);
                                                                })
                                ->with('vehicle_details.vehicle_type_details','vehicle_details.vehicle_brand_details','vehicle_details.vehicle_model_details')
    							->orderBy('id','DESC')
    							->get();

    	$arr_data = [];
    	if($obj_data)
    	{
    		$arr_data = $obj_data->toArray();
    	}

		$this->arr_view_data['page_title']      = "Manage ".str_plural($this->module_title);
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['arr_data']     	= $arr_data;

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

    		Flash::success('Vehicle Successfully removed for driver.');
    	}
    	else
    	{
    		Flash::error('Problem Occurred, While Updating removing assigned vehicle for driver.');
    	}
    	return redirect()->back();
    }
    public function assign_car(Request $request)
    {
    	$driver_car_id = base64_decode($request->input('driver_car_id'));
    	$vehicle_id    = $request->input('vehicle_id');



    	if($driver_car_id == '' || $vehicle_id == '')
    	{
    		Flash::error('Something went wrong,cannot assgin vehicle please try again.');
    	}

    	$obj_data = $this->DriverCarRelationModel
    								->where('id',$driver_car_id)
    								->first();
    	if($obj_data)
    	{

            $user_id = isset($obj_data->driver_id)  ? $obj_data->driver_id :0;

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

                $arr_notification_data = $this->built_notification_data($arr_data_info); 
                $this->NotificationsService->store_notification($arr_notification_data);

                //send one signal notification to driver
                $arr_notification_data = 
                                        [
                                            'title'             => 'Company Assigned car to you',
                                            'notification_type' => 'CAR_ASSIGN',
                                            'enc_user_id'       => $user_id,
                                            'user_type'         => 'DRIVER',

                                        ];
               $this->NotificationsService->send_on_signal_notification($arr_notification_data);


    			Flash::success('Vehicle Successfully assigned to driver.');
    		}	
    		else
    		{
    			Flash::error('Something went wrong,cannot assgin vehicle please try again.');
    		}
    	}
    	else
    	{
			Flash::error('Something went wrong,cannot assgin vehicle please try again.');
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
                            ->where('is_active',1)
                            ->where(['is_active'=>1,'is_company_vehicle'=>1,'is_individual_vehicle'=>0])
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
        		$vehicle_model_name  = isset($value['vehicle_model_name']) ? $value['vehicle_model_name'] :'';
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

    private function built_notification_data_info($arr_data_info)
    {
            $arr_notification = [];
            if(isset($arr_data_info) && sizeof($arr_data_info)>0)
            {
                $vehicle_name   = isset($arr_data_info['vehicle_name']) ? $arr_data_info['vehicle_name'] :'';
                $vehicle_number = isset($arr_data_info['vehicle_number']) ? $arr_data_info['vehicle_number'] :'';
                $first_name     = isset($arr_data_info['first_name']) ? $arr_data_info['first_name'] :'';
                $last_name      = isset($arr_data_info['last_name']) ? $arr_data_info['last_name'] :'';
                $full_name      = $first_name.' '.$last_name;
                $full_name      = ($full_name!=' ') ? $full_name : '-';

                $arr_notification['user_id']           = $arr_data_info['id'];
                $arr_notification['is_read']           = 0;
                $arr_notification['is_show']           = 0;
                $arr_notification['user_type']         = 'DRIVER';

                $arr_notification['notification_type'] = 'Vehicle Assign';
                $arr_notification['title']             = 'You have been assigned to a vehicle '.$vehicle_name.' with number '.$vehicle_number;
                $arr_notification['view_url']          = '/'.config('app.project.admin_panel_slug')."/driver_vehicle"; //$this->module_url_path;

        }
        return $arr_notification;
    }

    private function built_notification_data($arr_data_info)
    {
            $arr_notification = [];
            if(isset($arr_data_info) && sizeof($arr_data_info)>0)
            {   
                $vehicle_name   = isset($arr_data_info_info['vehicle_name']) ? $arr_data_info_info['vehicle_name'] :'';
                $vehicle_number = isset($arr_data_info_info['vehicle_number']) ? $arr_data_info_info['vehicle_number'] :'';
                $first_name     = isset($arr_data_info['first_name']) ? $arr_data_info['first_name'] :'';
                $last_name      = isset($arr_data_info['last_name']) ? $arr_data_info['last_name'] :'';
                $full_name      = $first_name.' '.$last_name;
                $full_name      = ($full_name!=' ') ? $full_name : '-';

                $arr_notification['user_id']           = $this->CommonDataService->get_admin_id();
                $arr_notification['is_read']           = 0;
                $arr_notification['is_show']           = 0;
                $arr_notification['user_type']         = 'ADMIN';
                $arr_notification['notification_type'] = 'Vehicle Assign By Company';
                $arr_notification['title']             = 'Driver '.$full_name.' assigned to vehicle '.$vehicle_name.' by Company';
                $arr_notification['view_url']          = '/'.config('app.project.admin_panel_slug')."/driver_vehicle"; //$this->module_url_path;

        }
        return $arr_notification;
    }
}
