<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use App\Models\UserModel;
use App\Models\VehicleModel;
use App\Models\DriverCarRelationModel;
use App\Models\DriverCarRelationHistoryModel;

use App\Common\Traits\MultiActionTrait;
use App\Common\Services\CommonDataService;
use App\Common\Services\NotificationsService;

use DB;
use Flash;
use Validator;

class VehicleController extends Controller
{
    use MultiActionTrait;

    public function __construct(
                                    UserModel $user,
                                    VehicleModel $vehicle,
                                    DriverCarRelationModel $driver_car_relation,
                                    DriverCarRelationHistoryModel $driver_car_relation_history,
                                    CommonDataService $common_data_service,
                                    NotificationsService $notifications_service
                                )
    {
        $this->UserModel                     = $user;
        $this->VehicleModel                  = $vehicle;
        $this->BaseModel                     = $this->VehicleModel;
        $this->DriverCarRelationModel        = $driver_car_relation;
        $this->DriverCarRelationHistoryModel = $driver_car_relation_history;
        $this->NotificationsService          = $notifications_service;
        $this->CommonDataService             = $common_data_service;
        $this->arr_view_data                 = [];
        $this->module_title                  = "Vehicle";
        $this->module_view_folder            = "admin.vehicle";
        $this->theme_color                   = theme_color();
        $this->admin_panel_slug              = config('app.project.admin_panel_slug');
        $this->module_url_path               = url(config('app.project.admin_panel_slug')."/vehicle");

        $this->vehicle_doc_public_path = url('/').config('app.project.img_path.vehicle_doc');
        $this->vehicle_doc_base_path   = base_path().config('app.project.img_path.vehicle_doc');

        $this->driving_license_public_path = url('/').config('app.project.img_path.driving_license');
        $this->driving_license_base_path   = base_path().config('app.project.img_path.driving_license');

    } 

    public function index(Request $request)
    {   
        $vehicles_type = 'admin';
        if($request->has('vehicles_type') && $request->input('vehicles_type')!=''){
            $vehicles_type = $request->input('vehicles_type');
        }
    	$arr_vehicle = [];
        $obj_vehicle = $this->VehicleModel;
                                if($vehicles_type!='' && $vehicles_type == 'admin'){
                                    $obj_vehicle = $obj_vehicle->where('is_company_vehicle','0'); /*is_individual_vehicle == 0 means its admin car*/
                                    $obj_vehicle = $obj_vehicle->where('is_individual_vehicle','0'); /*is_individual_vehicle == 0 means its admin car*/
                                }
                                else if($vehicles_type!='' && $vehicles_type == 'individual'){
                                    $obj_vehicle = $obj_vehicle->where('is_individual_vehicle','1'); /*is_individual_vehicle == 1 means its individual car*/
                                    // $obj_vehicle = $obj_vehicle->where('is_deleted','0'); /*is_individual_vehicle == 1 means its individual car*/
                                }
                                else{
                                    $obj_vehicle = $obj_vehicle->where('is_individual_vehicle','0'); /*is_individual_vehicle == 0 means its admin car*/
                                }   
                                // ->select('id','vehicle_type_id','vehicle_name','vehicle_model_name','vehicle_number','is_active')
        $obj_vehicle = $obj_vehicle->with(['vehicle_type_details','driver_car_details'=>function($query){
                                                $query->with(['driver_details'=>function($query){
                                                    // $query->where('is_deleted','0');
                                                }]);
                                        },'vehicle_brand_details','vehicle_model_details'])
                                // ->with(['vehicle_type_details', 'driver_car_details', 'driver_car_details.driver_details'])
                                ->orderBy('id','DESC')
                                ->get();
                                

    	if($obj_vehicle)
    	{
    		$arr_vehicle = $obj_vehicle->toArray();
    	}
        
        $this->arr_view_data['page_title']              = "Manage ".str_plural($this->module_title);
        $this->arr_view_data['module_title']            = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']         = $this->module_url_path;
        $this->arr_view_data['theme_color']             = $this->theme_color;
        $this->arr_view_data['arr_vehicle']             = $arr_vehicle;
        $this->arr_view_data['vehicles_type']           = $vehicles_type;
        $this->arr_view_data['vehicle_doc_public_path'] = $this->vehicle_doc_public_path;
        $this->arr_view_data['vehicle_doc_base_path']   = $this->vehicle_doc_base_path;

        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    public function create()
    {
        $slug = "driver";
        $arr_drivers = array();

        $arr_vehicle_types = $this->CommonDataService->get_vehicle_types();
        $get_vehicle_brands = $this->CommonDataService->get_vehicle_brand();
        $obj_drivers = $this->UserModel
                            ->select('id','first_name','last_name')
                            ->whereHas('roles', function($q) use ($slug){
                                $q->where("slug",$slug);
                            })
                            ->whereHas('driver_car_relations' , function($q1) use ($slug) {
                                        $q1->where('is_car_assign', 0);
                                        $q1->where('is_individual_vehicle', '0');

                                    })
                            ->with([ 'roles'=>function($q) use ($slug){
                                        $q->where("slug", $slug);
                                    },
                                    'driver_car_relations' => function($q1) use ($slug){
                                        $q1->where('is_car_assign', 0);
                                        $q1->where('is_individual_vehicle', '0');
                                    }])
                            ->where('is_company_driver','0')
                            ->where('is_deleted','0')
                            ->get();

        if($obj_drivers)
        {
            $arr_drivers = $obj_drivers->toArray();
        }

        $this->arr_view_data['page_title']        = "Create ".str_singular( $this->module_title);
        $this->arr_view_data['module_title']      = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']   = $this->module_url_path;
        $this->arr_view_data['theme_color']       = $this->theme_color;
        $this->arr_view_data['arr_vehicle_types'] = $arr_vehicle_types;
        $this->arr_view_data['arr_vehicle_brands'] = $get_vehicle_brands;
        $this->arr_view_data['arr_drivers'] = $arr_drivers;

        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }
    
    public function store(Request $request)
    {
    	$arr_rules = [];

        $arr_rules['vehicle_type']                = "required";
        $arr_rules['vehicle_brand']               = "required";
        $arr_rules['vehicle_model']               = "required";
        $arr_rules['vehicle_number']              = "required";

        $validator = Validator::make($request->all(),$arr_rules);
        
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $arr_insert                                = [];
        $arr_insert['vehicle_type_id']             = $request->input('vehicle_type');
        $arr_insert['vehicle_brand_id']            = $request->input('vehicle_brand');
        $arr_insert['vehicle_model_id']            = $request->input('vehicle_model');
        $arr_insert['vehicle_number']              = $request->input('vehicle_number');
        $arr_insert['is_active']                   = 1;
        $arr_insert['is_verified']                 = 1;
	
        if($request->hasFile('vehicle_image'))
        {
            $vehicle_image = $request->input('vehicle_image');
            $file_extension = strtolower($request->file('vehicle_image')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
            {
                $vehicle_image = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('vehicle_image')->move($this->vehicle_doc_base_path, $vehicle_image);
                if($isUpload)
                {    
                    $arr_insert['vehicle_image'] = $vehicle_image;
                }
            }
        }

        if($request->hasFile('registration_doc'))
        {
            $registration_doc = $request->file('registration_doc');

            $file_extension = strtolower($request->file('registration_doc')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
            {
                $registration_doc = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('registration_doc')->move($this->vehicle_doc_base_path, $registration_doc);
                if($isUpload)
                {    
                    $arr_insert['registration_doc'] = $registration_doc;
                }
              
            }
        }
        
        if($request->hasFile('proof_of_inspection'))
        {
            $proof_of_inspection = $request->file('proof_of_inspection');

            $file_extension = strtolower($request->file('proof_of_inspection')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
            {
                $proof_of_inspection = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('proof_of_inspection')->move($this->vehicle_doc_base_path, $proof_of_inspection);
                if($isUpload)
                {    
                    $arr_insert['proof_of_inspection_doc'] = $proof_of_inspection;
                }
              
            }
        }

        if($request->hasFile('insurance_doc'))
        {
            $insurance_doc = $request->file('insurance_doc');
            $file_extension = strtolower($request->file('insurance_doc')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
            {
                $insurance_doc = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('insurance_doc')->move($this->vehicle_doc_base_path, $insurance_doc);
                if($isUpload)
                {    
                    $arr_insert['insurance_doc'] = $insurance_doc;
                }
               // dd($insurance_doc);
            }
        }

        if($request->hasFile('driving_doc'))
        {
            $driving_doc = $request->file('driving_doc');
            $file_extension = strtolower($request->file('driving_doc')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
            {
                $driving_doc = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('driving_doc')->move($this->vehicle_doc_base_path, $driving_doc);
                if($isUpload)
                {    
                    $arr_insert['dmv_driving_record'] = $driving_doc;
                }
               // dd($driving_doc);
            }
        }

        if($request->hasFile('usdot_doc'))
        {
            $usdot_doc = $request->file('usdot_doc');
            $file_extension = strtolower($request->file('usdot_doc')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
            {
                $usdot_doc = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('usdot_doc')->move($this->vehicle_doc_base_path, $usdot_doc);
                if($isUpload)
                {    
                    $arr_insert['usdot_doc'] = $usdot_doc;
                }
            }
        }

        if($request->hasFile('mc_doc'))
        {
            $mc_doc = $request->file('mc_doc');
            $file_extension = strtolower($request->file('mc_doc')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
            {
                $mc_doc = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('mc_doc')->move($this->vehicle_doc_base_path, $mc_doc);
                if($isUpload)
                {    
                    $arr_insert['mc_doc'] = $mc_doc;
                }
            }
        }

        $arr_insert['is_vehicle_image_verified']           = 'APPROVED';
        $arr_insert['is_registration_doc_verified']        = 'APPROVED';
        $arr_insert['is_insurance_doc_verified']           = 'APPROVED';
        $arr_insert['is_proof_of_inspection_doc_verified'] = 'APPROVED';
        $arr_insert['is_dmv_driving_record_verified']      = 'APPROVED';
        $arr_insert['is_usdot_doc_verified']               = 'APPROVED';
        $arr_insert['is_mcdoc_doc_verified']               = 'APPROVED';
        
        $status = $this->VehicleModel->create($arr_insert);

		if($status)
        {
            if($request->input('driver_id') != "")
            {
                $obj_data = $this->DriverCarRelationModel
                                    ->where('driver_id',$request->input('driver_id'))
                                    ->first();
                if($obj_data)
                {
                    $vehicle_id = isset($status->id) ? $status->id : 0;
                    
                    $obj_data->vehicle_id    = $vehicle_id;
                    $obj_data->is_car_assign = 1;
                    $obj_staus = $obj_data->save();
                    if($obj_staus)
                    {
                        /*new car assign maintain in history*/
                        $arr_history = 
                                    [
                                        'driver_id'  => $request->input('driver_id'),
                                        'vehicle_id' => $vehicle_id,
                                        'status'     => 'ASSIGN'
                                    ];

                        $this->DriverCarRelationHistoryModel->create($arr_history);

                        $arr_user_data      = $this->CommonDataService->get_user_details($request->input('driver_id'));
                        $arr_vehicle_info   = $this->CommonDataService->get_vehicle_details($vehicle_id);

                        $arr_data_info = array_merge($arr_user_data,$arr_vehicle_info['0']);
                       // dd($arr_data_details);

                        $arr_notification_data = $this->built_notification_data_info($arr_data_info); 
                        $this->NotificationsService->store_notification($arr_notification_data);

                        Flash::success('Vehicle Successfully assigned to driver.');
                    }   
                    else
                    {
                        Flash::error('Something went wrong,cannot assgin car please try again.');
                    }
                }
                else
                {
                    Flash::error('Something went wrong,cannot assgin car please try again.');
                }
            }
            Flash::success(str_singular($this->module_title).' Created Successfully');
        }
        else
        {
            Flash::error('Problem Occurred, While Creating '.str_singular($this->module_title));
        }

        return redirect()->back();
    }
    public function view($enc_id)
    {
        $id = base64_decode($enc_id);

        $arr_data = [];

        $obj_user = $this->VehicleModel
                                // ->select('id','vehicle_type_id','vehicle_name','vehicle_model_name','vehicle_number')
                                ->with(['driver_car_details.driver_details','vehicle_type_details','vehicle_brand_details','vehicle_model_details'])
                                ->where('id',$id)
                                ->first();
        if($obj_user)
        {
            $arr_data = $obj_user->toArray();
        }


        $this->arr_view_data['page_title']                   = "View ".str_singular($this->module_title);
        $this->arr_view_data['module_title']                 = str_plural($this->module_title);
        $this->arr_view_data['theme_color']                  = $this->theme_color;
        $this->arr_view_data['module_url_path']              = $this->module_url_path;
        $this->arr_view_data['enc_id']                       = $enc_id;
        $this->arr_view_data['arr_data']                     = $arr_data;

        $this->arr_view_data['vehicle_doc_public_path'] = $this->vehicle_doc_public_path;
        $this->arr_view_data['vehicle_doc_base_path']   = $this->vehicle_doc_base_path;

        $this->arr_view_data['driving_license_public_path'] = $this->driving_license_public_path;
        $this->arr_view_data['driving_license_base_path']   = $this->driving_license_base_path;

        return view($this->module_view_folder.'.view', $this->arr_view_data);    
    }
    public function change_document_status(Request $request)
    {
        $driver_id     = base64_decode($request->input('driver_id'));
        $vehicle_id    = base64_decode($request->input('vehicle_id'));
        $document_type = $request->input('type');
        $status        = $request->input('status');

        if(($driver_id == '') || ($vehicle_id == '') || ($document_type == '') || ($status == ''))
        {
            Flash::error('Something went wrong,cannot change status,Please try again.');
            return redirect()->back();
        }
        
        $is_all_document_verified = 'NO';
        
        $is_driving_license_verified = $is_vehicle_image_verified = $is_registration_doc_verified = $is_insurance_doc_verified = $is_proof_of_inspection_doc_verified =  $is_dmv_driving_record_verified = $is_usdot_doc_verified =  'NO';

        $status_change_flag = false;
        
        $notification_title = '';

        if($document_type == 'driving_license')
        {
            $obj_data = $this->UserModel->where('id',$driver_id)->first();
            if($obj_data)
            {
                if($status == 'approved')
                {
                    $notification_title = 'Your driving license approved by QuickPick Admin.';
                    $obj_data->is_driving_license_verified = 'APPROVED';
                    $obj_data->save();
                    $status_change_flag = true;
                }
                else if($status == 'rejected')
                {
                    $notification_title = 'Your driving license rejected by QuickPick Admin,Please upload it again.';
                    $obj_data->is_driving_license_verified = 'REJECTED';
                    $obj_data->save();
                    $status_change_flag = true;
                }
            }
        }

        if($document_type == 'vehicle_image')
        {
            $obj_data = $this->VehicleModel->where('id',$vehicle_id)->first();
            if($obj_data)
            {
                if($status == 'approved')
                {
                    $notification_title = 'Your driving license approved by QuickPick Admin.';
                    $obj_data->is_vehicle_image_verified = 'APPROVED';
                    $obj_data->save();
                    $status_change_flag = true;
                }
                else if($status == 'rejected')
                {
                    $notification_title = 'Your vehicle image rejected by QuickPick Admin,Please upload it again.';
                    $obj_data->is_vehicle_image_verified = 'REJECTED';
                    $obj_data->save();
                    $status_change_flag = true;
                }
            }
        }

        if($document_type == 'registration_doc')
        {
            $obj_data = $this->VehicleModel->where('id',$vehicle_id)->first();
            if($obj_data)
            {
                if($status == 'approved')
                {
                    $notification_title = 'Your registration document approved by QuickPick Admin.';
                    $obj_data->is_registration_doc_verified = 'APPROVED';
                    $obj_data->save();
                    $status_change_flag = true;
                }
                else if($status == 'rejected')
                {
                    $notification_title = 'Your registration document rejected by QuickPick Admin,Please upload it again.';
                    $obj_data->is_registration_doc_verified = 'REJECTED';
                    $obj_data->save();
                    $status_change_flag = true;
                }
            }
        }

        if($document_type == 'insurance_doc')
        {
            $obj_data = $this->VehicleModel->where('id',$vehicle_id)->first();
            if($obj_data)
            {
                if($status == 'approved')
                {
                    $notification_title = 'Your insurance document approved by QuickPick Admin.';
                    $obj_data->is_insurance_doc_verified = 'APPROVED';
                    $obj_data->save();
                    $status_change_flag = true;
                }
                else if($status == 'rejected')
                {
                    $notification_title = 'Your insurance document rejected by QuickPick Admin,Please upload it again.';
                    $obj_data->is_insurance_doc_verified = 'REJECTED';
                    $obj_data->save();
                    $status_change_flag = true;
                }
            }
        }

        if($document_type == 'proof_of_inspection_doc')
        {
            $obj_data = $this->VehicleModel->where('id',$vehicle_id)->first();
            if($obj_data)
            {
                if($status == 'approved')
                {
                    $notification_title = 'Your proof of inspection document approved by QuickPick Admin.';
                    $obj_data->is_proof_of_inspection_doc_verified = 'APPROVED';
                    $obj_data->save();
                    $status_change_flag = true;
                }
                else if($status == 'rejected')
                {
                    $notification_title = 'Your proof of inspection document rejected by QuickPick Admin,Please upload it again.';
                    $obj_data->is_proof_of_inspection_doc_verified = 'REJECTED';
                    $obj_data->save();
                    $status_change_flag = true;
                }
            }
        }

        if($document_type == 'dmv_driving_record')
        {
            $obj_data = $this->VehicleModel->where('id',$vehicle_id)->first();
            if($obj_data)
            {
                if($status == 'approved')
                {
                    $notification_title = 'Your DMV driving record approved by QuickPick Admin.';
                    $obj_data->is_dmv_driving_record_verified = 'APPROVED';
                    $obj_data->save();
                    $status_change_flag = true;
                }
                else if($status == 'rejected')
                {
                    $notification_title = 'Your DMV driving record rejected by QuickPick Admin,Please upload it again.';
                    $obj_data->is_dmv_driving_record_verified = 'REJECTED';
                    $obj_data->save();
                    $status_change_flag = true;
                }
            }
        }
        if($document_type == 'usdot_doc')
        {
            $obj_data = $this->VehicleModel->where('id',$vehicle_id)->first();
            if($obj_data)
            {
                if($status == 'approved')
                {
                    $notification_title = 'Your USDOT document approved by QuickPick Admin.';
                    $obj_data->is_usdot_doc_verified = 'APPROVED';
                    $obj_data->save();
                    $status_change_flag = true;
                }
                else if($status == 'rejected')
                {
                    $notification_title = 'Your USDOT document rejected by QuickPick Admin,Please upload it again.';
                    $obj_data->is_usdot_doc_verified = 'REJECTED';
                    $obj_data->save();
                    $status_change_flag = true;
                }
            }
        }

        if($document_type == 'mc_doc')
        {
            $obj_data = $this->VehicleModel->where('id',$vehicle_id)->first();
            if($obj_data)
            {
                if($status == 'approved')
                {
                    $notification_title = 'Your MCDOC document approved by QuickPick Admin.';
                    $obj_data->is_mcdoc_doc_verified = 'APPROVED';
                    $obj_data->save();
                    $status_change_flag = true;
                }
                else if($status == 'rejected')
                {
                    $notification_title = 'Your MCDOC document rejected by QuickPick Admin,Please upload it again.';
                    $obj_data->is_mcdoc_doc_verified = 'REJECTED';
                    $obj_data->save();
                    $status_change_flag = true;
                }
            }
        }

        // $obj_user_data = $this->UserModel->where('id',$driver_id)->first();
        // if($obj_user_data)
        // {
        //     if(isset($obj_data->is_driving_license_verified) && $obj_data->is_driving_license_verified == 'APPROVED')
        //     {
        //         $is_driving_license_verified = 'YES';
        //     }
        // }

        // $obj_vehicle = $this->VehicleModel->where('id',$vehicle_id)->first();
        // if($obj_vehicle)
        // {
        //     if(isset($obj_vehicle->is_vehicle_image_verified) && $obj_vehicle->is_vehicle_image_verified == 'APPROVED')
        //     {
        //         $is_vehicle_image_verified = 'YES';
        //     }
        //     if(isset($obj_vehicle->is_registration_doc_verified) && $obj_vehicle->is_registration_doc_verified == 'APPROVED')
        //     {
        //         $is_registration_doc_verified = 'YES';
        //     }

        //     if(isset($obj_vehicle->is_insurance_doc_verified) && $obj_vehicle->is_insurance_doc_verified == 'APPROVED')
        //     {
        //         $is_insurance_doc_verified = 'YES';
        //     }

        //     if(isset($obj_vehicle->is_proof_of_inspection_doc_verified) && $obj_vehicle->is_proof_of_inspection_doc_verified == 'APPROVED')
        //     {
        //         $is_proof_of_inspection_doc_verified = 'YES';
        //     }

        //     if(isset($obj_vehicle->is_dmv_driving_record_verified) && $obj_vehicle->is_dmv_driving_record_verified == 'APPROVED')
        //     {
        //         $is_dmv_driving_record_verified = 'YES';
        //     }

        //     if(isset($obj_vehicle->is_usdot_doc_verified) && $obj_vehicle->is_usdot_doc_verified == 'APPROVED')
        //     {
        //         $is_usdot_doc_verified = 'YES';
        //     }
        // }

        // if( 
        //     $is_driving_license_verified          == 'YES' && 
        //     $is_vehicle_image_verified            == 'YES' &&
        //     $is_registration_doc_verified         == 'YES' &&
        //     $is_insurance_doc_verified            == 'YES' &&
        //     $is_proof_of_inspection_doc_verified  == 'YES' &&
        //     $is_dmv_driving_record_verified       == 'YES' &&
        //     $is_usdot_doc_verified                == 'YES' 
        //    )
        // {
        //     $is_all_document_verified = 'YES';
        // }
        // /*if all documents are approved then make chane  status of vehicle*/
        // if($is_all_document_verified == 'YES')
        // {
        //     $this->VehicleModel->where('id',$vehicle_id)->update(['is_verified'=>'1']);
        // }
        // else 
        // {
        //     $this->VehicleModel->where('id',$vehicle_id)->update(['is_verified'=>'0']);
        // }

        if($status_change_flag)
        {
            $status_msg = '';
            $notification_type = '';
            if($status == 'approved')
            {
                $status_msg = 'Document approved Successfully.';
                $notification_type = 'DOCUMENT_APPROVED';
            }
            else if($status == 'rejected')
            {
                $status_msg = 'Document rejected Successfully.';
                $notification_type = 'DOCUMENT_REJECTED';
            }

            //send one signal notification to driver
            $arr_notification_data = 
                                    [
                                        'title'             => $notification_title,
                                        'notification_type' => $notification_type,
                                        'enc_user_id'       => $driver_id,
                                        'user_type'         => 'DRIVER',

                                    ];
            $this->NotificationsService->send_on_signal_notification($arr_notification_data);

            Flash::success($status_msg);
        }
        else
        {
            $status_msg = '';
            if($status == 'approved')
            {
                $status_msg = 'Problem Occured, while approving document.';
            }
            else if($status == 'rejected')
            {
                $status_msg = 'Problem Occured, while rejecting document.';
            }
            Flash::error($status_msg);
        }
        return redirect()->back();
    }
    public function edit($enc_id)
    {
        $slug = "driver";
        $id = base64_decode($enc_id);

        $arr_data = [];

        $obj_user = $this->VehicleModel
                                // ->select('id','vehicle_type_id','vehicle_name','vehicle_model_name','vehicle_number')
                                ->with(['driver_car_details.driver_details'])
                                ->where('id',$id)
                                ->first();
        if($obj_user)
        {
            $arr_data = $obj_user->toArray();
        }
        // dd($arr_data);
        $arr_vehicle_types = $this->CommonDataService->get_vehicle_types();
        $get_vehicle_brands = $this->CommonDataService->get_vehicle_brand();
        //dd($get_vehicle_brands);

        $obj_drivers = $this->UserModel
                            ->select('id','first_name','last_name')
                            ->whereHas('roles', function($q) use ($slug){
                                $q->where("slug",$slug);
                            })
                            ->whereHas('driver_car_relations' , function($q1) use ($slug) {
                                        $q1->where('is_car_assign', 0);
                                        $q1->where('is_individual_vehicle', '0');
                                    })
                            ->with([ 'roles'=>function($q) use ($slug){
                                        $q->where("slug", $slug);
                                    },
                                    'driver_car_relations' => function($q1) use ($slug){
                                        $q1->where('is_car_assign', 0);
                                        $q1->where('is_individual_vehicle', '0');
                                    }])
                            ->where('is_company_driver','0')
                            ->where('is_deleted','0')
                            ->get();

        if($obj_drivers)
        {
            $arr_drivers = $obj_drivers->toArray();
        }

        $this->arr_view_data['edit_mode']                    = TRUE;
        $this->arr_view_data['page_title']                   = "Edit ".str_singular($this->module_title);
        $this->arr_view_data['module_title']                 = str_plural($this->module_title);
        $this->arr_view_data['theme_color']                  = $this->theme_color;
        $this->arr_view_data['module_url_path']              = $this->module_url_path;
        $this->arr_view_data['enc_id']                       = $enc_id;
        $this->arr_view_data['arr_data']                     = $arr_data;
        $this->arr_view_data['arr_vehicle_types']            = $arr_vehicle_types;
        $this->arr_view_data['arr_vehicle_brands']           = $get_vehicle_brands;
        $this->arr_view_data['arr_drivers']                  = $arr_drivers;

        return view($this->module_view_folder.'.edit', $this->arr_view_data);    
    }

    public function update(Request $request)
    {
    	$arr_rules = [];

        $arr_rules['vehicle_type']                = "required";
        $arr_rules['vehicle_brand']               = "required";
        $arr_rules['vehicle_model']               = "required";
        $arr_rules['vehicle_number']              = "required";

        $validator = Validator::make($request->all(),$arr_rules);
        
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
        // dd($request->input());
        $enc_id = base64_decode($request->input('enc_id'));
        
        $arr_update                                = [];
        $arr_insert['vehicle_type_id']             = $request->input('vehicle_type');
        $arr_insert['vehicle_brand_id']            = $request->input('vehicle_brand');
        $arr_insert['vehicle_model_id']            = $request->input('vehicle_model');
        $arr_insert['vehicle_number']              = $request->input('vehicle_number');
      
        $obj_prev_vehicle = $this->VehicleModel->where('id',$enc_id)->first();

        if($request->hasFile('vehicle_image'))
        {
            $vehicle_image = $request->input('vehicle_image');
            $file_extension = strtolower($request->file('vehicle_image')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
            {
                $vehicle_image = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('vehicle_image')->move($this->vehicle_doc_base_path , $vehicle_image);
                if($isUpload){    
                    if(isset($obj_prev_vehicle->vehicle_image) && $obj_prev_vehicle->vehicle_image!=''){
                        if(file_exists($this->vehicle_doc_base_path.$obj_prev_vehicle->vehicle_image)){
                            @unlink($this->vehicle_doc_base_path.$obj_prev_vehicle->vehicle_image);
                        }
                    }
                    $arr_update['vehicle_image'] = $vehicle_image;
                }
            }
        }

        if($request->hasFile('registration_doc'))
        {
            $registration_doc = $request->input('registration_doc');
            $file_extension = strtolower($request->file('registration_doc')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
            {
                $registration_doc = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('registration_doc')->move($this->vehicle_doc_base_path , $registration_doc);
                if($isUpload){    
                    if(isset($obj_prev_vehicle->registration_doc) && $obj_prev_vehicle->registration_doc!=''){
                        if(file_exists($this->vehicle_doc_base_path.$obj_prev_vehicle->registration_doc)){
                            @unlink($this->vehicle_doc_base_path.$obj_prev_vehicle->registration_doc);
                        }
                    }
                    $arr_update['registration_doc'] = $registration_doc;
                }
                
            }
        }

        if($request->hasFile('proof_of_inspection'))
        {
            $proof_of_inspection = $request->file('proof_of_inspection');

            $file_extension = strtolower($request->file('proof_of_inspection')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
            {
                $proof_of_inspection = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('proof_of_inspection')->move($this->vehicle_doc_base_path, $proof_of_inspection);
                if($isUpload)
                {    
                    $arr_update['proof_of_inspection_doc'] = $proof_of_inspection;
                }
              
            }
        }

        if($request->hasFile('insurance_doc'))
        {
            $insurance_doc = $request->input('insurance_doc');
            $file_extension = strtolower($request->file('insurance_doc')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
            {
                $insurance_doc = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('insurance_doc')->move($this->vehicle_doc_base_path , $insurance_doc);
                if($isUpload)
                {    
                    if(isset($obj_prev_vehicle->insurance_doc) && $obj_prev_vehicle->insurance_doc!=''){
                        if(file_exists($this->vehicle_doc_base_path.$obj_prev_vehicle->insurance_doc)){
                            @unlink($this->vehicle_doc_base_path.$obj_prev_vehicle->insurance_doc);
                        }
                    }
                    $arr_update['insurance_doc'] = $insurance_doc;
                }
                
            }
        }

        if($request->hasFile('driving_doc'))
        {
            $driving_doc = $request->file('driving_doc');
            $file_extension = strtolower($request->file('driving_doc')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
            {
                $driving_doc = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('driving_doc')->move($this->vehicle_doc_base_path, $driving_doc);
                if($isUpload)
                {    
                    $arr_update['dmv_driving_record'] = $driving_doc;
                }
               // dd($driving_doc);
            }
        }

        if($request->hasFile('usdot_doc'))
        {
            $usdot_doc = $request->file('usdot_doc');
            $file_extension = strtolower($request->file('usdot_doc')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
            {
                $usdot_doc = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('usdot_doc')->move($this->vehicle_doc_base_path, $usdot_doc);
                if($isUpload)
                {     
                    $arr_update['usdot_doc'] = $usdot_doc;
                }
               // dd($usdot_doc);
            }
        }

        if($request->hasFile('mc_doc'))
        {
            $mc_doc = $request->file('mc_doc');
            $file_extension = strtolower($request->file('mc_doc')->getClientOriginalExtension());
            if(in_array($file_extension,['png','jpg','jpeg','pdf']))
            {
                $mc_doc = time().uniqid().'.'.$file_extension;
                $isUpload = $request->file('mc_doc')->move($this->vehicle_doc_base_path, $mc_doc);
                if($isUpload)
                {     
                    $arr_update['mc_doc'] = $mc_doc;
                }
               // dd($usdot_doc);
            }
        }
        
        $status = $this->VehicleModel->where('id',$enc_id)->update($arr_update);
        if($status)
        {
            if($request->input('driver_id') != "")
            {
                $obj_data = $this->DriverCarRelationModel
                                    ->where('driver_id',$request->input('driver_id'))
                                    ->first();
                if($obj_data)
                {
                    $obj_data->vehicle_id    = $enc_id;
                    $obj_data->is_car_assign = 1;
                    $obj_staus = $obj_data->save();
                    if($obj_staus)
                    {
                        if($request->input('old_driver_id'))
                        {
                            $old_driver_id  = $request->input('old_driver_id');
                            $vehicle_id     = $enc_id;

                            $arr_history = 
                                            [
                                                'driver_id'  => $old_driver_id,
                                                'vehicle_id' => $enc_id,
                                                'status'     => 'REMOVE'
                                            ];

                            $this->DriverCarRelationHistoryModel->create($arr_history);

                            $obj_old_data = $this->DriverCarRelationModel
                                            ->where('driver_id', $old_driver_id)
                                            ->first();
                            $obj_old_data->vehicle_id    = 0;
                            $obj_old_data->is_car_assign = 0;
                            $obj_old_data->save();
                        }
                        
                        /*new car assign maintain in history*/
                        $arr_history = 
                                    [
                                        'driver_id'  => $request->input('driver_id'),
                                        'vehicle_id' => $enc_id,
                                        'status'     => 'ASSIGN'
                                    ];

                        $this->DriverCarRelationHistoryModel->create($arr_history);

                        $arr_user_data      = $this->CommonDataService->get_user_details($request->input('driver_id'));
                        $arr_vehicle_info   = $this->CommonDataService->get_vehicle_details($enc_id);

                        if(isset($arr_vehicle_info[0]))
                        {
                            $arr_data_info = array_merge($arr_user_data,$arr_vehicle_info[0]);
                            $arr_notification_data = $this->built_notification_data_info($arr_data_info); 
                            $this->NotificationsService->store_notification($arr_notification_data);   
                        }


                        Flash::success('Vehicle Successfully assigned to driver.');
                    }   
                    else
                    {
                        Flash::error('Something went wrong,cannot assgin car please try again.');
                    }
                }
                else
                {
                    Flash::error('Something went wrong,cannot assgin car please try again.');
                }
            }
            Flash::success(str_singular($this->module_title).' Updated Successfully');
        }
        else
        {
            Flash::error('Problem Occurred, While Updating '.str_singular($this->module_title));
        }
        return redirect()->back();
    }
    public function built_mail_data($arr_data)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
            $arr_built_content = [
                                  'FIRST_NAME'       => $arr_data['first_name'],
                                  'LAST_NAME'        => $arr_data['last_name'],
                                  'EMAIL'            => $arr_data['email'],
                                  'PASSWORD'         => $arr_data['password'],
                                  'PROJECT_NAME'     => config('app.project.name')];

            $arr_mail_data                      = [];
            $arr_mail_data['email_template_id'] = '8';
            $arr_mail_data['arr_built_content'] = $arr_built_content;
            $arr_mail_data['user']              = $arr_data;

            return $arr_mail_data;
        }
        return FALSE;
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

                $arr_notification['user_id']           = $this->CommonDataService->get_admin_id();
                $arr_notification['is_read']           = 0;
                $arr_notification['is_show']           = 0;
                $arr_notification['user_type']         = 'DRIVER';
                $arr_notification['notification_type'] = 'Car Assign';
                $arr_notification['title']             = 'You have been assigned for'.$vehicle_name.' car and Car number is'.$vehicle_number;
                $arr_notification['view_url']          = '/'.config('app.project.admin_panel_slug')."/vehicle"; //$this->module_url_path;
        }
        return $arr_notification;
    }

    public function verify_vehicle($enc_id = FALSE)
    {
        if(!$enc_id)
        {
            return redirect()->back();
        }
        $enc_id = base64_decode($enc_id);
        $obj_data = $this->BaseModel->with('driver_car_details')->where('id',$enc_id)->first();

        if($obj_data)
        {
            $status = $obj_data->update(['is_verified'=>1]);
            $driver_id = isset($obj_data->driver_car_details->driver_id)?$obj_data->driver_car_details->driver_id : 0;
            if($driver_id!=0){
                //send one signal notification to driver
                $arr_notification_data = 
                                        [
                                            'title'             => 'QuickPick Admin verify your vehicle details',
                                            'notification_type' => 'VEHICLE_VERIFY',
                                            'enc_user_id'       => $driver_id,
                                            'user_type'         => 'DRIVER',

                                        ];
                $this->NotificationsService->send_on_signal_notification($arr_notification_data);
            }
            Flash::success($this->module_title. ' verified Successfully');
        }
        else
        {
            Flash::error('Problem Occured While '.$this->module_title.' verification ');
        }

        return redirect()->back();
    }
    
    public function unverify_vehicle($enc_id = FALSE)
    {
        if(!$enc_id)
        {
            return redirect()->back();
        }
        $enc_id = base64_decode($enc_id);
        $obj_data = $this->BaseModel->with('driver_car_details')->where('id',$enc_id)->first();
        
        if($obj_data)
        {
            $status = $obj_data->update(['is_verified'=>0]);
            
            $driver_id = isset($obj_data->driver_car_details->driver_id)?$obj_data->driver_car_details->driver_id : 0;
            if($driver_id!=0){
                //send one signal notification to driver
                $arr_notification_data = 
                                        [
                                            'title'             => 'QuickPick Admin unverify your vehicle details',
                                            'notification_type' => 'VEHICLE_UNVERIFY',
                                            'enc_user_id'       => $driver_id,
                                            'user_type'         => 'DRIVER',

                                        ];
                $this->NotificationsService->send_on_signal_notification($arr_notification_data);
            }
            Flash::success($this->module_title. ' unverified Successfully');
        }
        else
        {
            Flash::error('Problem Occured While '. $this->module_title .' unverification ');
        }

        return redirect()->back();
    }

    public function multi_action(Request $request)
    {
        $arr_rules = array();
        $arr_rules['multi_action'] = "required";
        $arr_rules['checked_record'] = "required";

        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {
            Flash::error('Please Select '.$this->module_title.' To Perform Multi Actions');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $multi_action = $request->input('multi_action');
        $checked_record = $request->input('checked_record');

        /* Check if array is supplied*/
        if(is_array($checked_record) && sizeof($checked_record)<=0)
        {
            Flash::error('Problem Occurred, While Doing Multi Action');
            return redirect()->back();
        }

        foreach ($checked_record as $key => $record_id) 
        {  
            if($multi_action=="delete")
            {
               $this->perform_delete(base64_decode($record_id));    
               Flash::success($this->module_title.' Deleted Successfully'); 
            } 
            elseif($multi_action=="activate")
            {
               $this->perform_activate(base64_decode($record_id)); 
               Flash::success($this->module_title.' Activated Successfully'); 
            }
            elseif($multi_action=="deactivate")
            {
               $this->perform_deactivate(base64_decode($record_id));    
               Flash::success($this->module_title.' Deactivated Successfully');  
            }
        }

        return redirect()->back();
    }

    public function activate($enc_id = FALSE)
    {
        if(!$enc_id)
        {
            return redirect()->back();
        }

        if($this->perform_activate(base64_decode($enc_id)))
        {
            Flash::success($this->module_title. ' Activated Successfully');
        }
        else
        {
            Flash::error('Problem Occured While '.$this->module_title.' Activation ');
        }

        return redirect()->back();
    }

    public function deactivate($enc_id = FALSE)
    {
        if(!$enc_id)
        {
            return redirect()->back();
        }

        if($this->perform_deactivate(base64_decode($enc_id)))
        {
            Flash::success($this->module_title. ' Deactivated Successfully');
        }
        else
        {
            Flash::error('Problem Occured While '. $this->module_title .' Deactivation ');
        }

        return redirect()->back();
    }

    public function delete($enc_id = FALSE)
    {
        if(!$enc_id)
        {
            return redirect()->back();
        }

        if($this->perform_delete(base64_decode($enc_id)))
        {
            Flash::success($this->module_title.' Deleted Successfully');
        }
        else
        {
            Flash::error('Problem Occured While '.$this->module_title.' Deletion ');
        }

        return redirect()->back();
    }


    public function perform_activate($id)
    {
        $static_page = $this->BaseModel->where('id',$id)->first();
        
        if($static_page)
        {

            return $static_page->update(['is_active'=>1]);
        }

        return FALSE;
    }

    public function perform_deactivate($id)
    {
        $static_page = $this->BaseModel->where('id',$id)->first();
        
        if($static_page)
        {
            return $static_page->update(['is_active'=>0]);
        }

        return FALSE;
    }

    public function perform_delete($id)
    {
        $delete = $this->BaseModel->where('id',$id)->delete();
        
        if($delete)
        {
            $obj_driver_car_relation = $this->DriverCarRelationModel->where('vehicle_id',$id)->first();
            if($obj_driver_car_relation){
                $obj_driver_car_relation->vehicle_id    = 0;
                $obj_driver_car_relation->is_car_assign = 0;
                $status = $obj_driver_car_relation->save();
            }
            return TRUE;
        }

        return FALSE;
    }
    

}
