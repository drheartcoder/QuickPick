<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\PromoOfferModel;
use App\Common\Traits\MultiActionTrait;

use Flash;
use Validator;

class PromoOfferController extends Controller
{
    use MultiActionTrait;
    public function __construct(
    								PromoOfferModel $PromoOfferModel
    							)
    {
        $this->PromoOfferModel 		 = $PromoOfferModel;
        $this->BaseModel             = $this->PromoOfferModel;

        $this->arr_view_data                = [];
        $this->module_title                 = "Promo Offer";
        $this->module_view_folder           = "admin.promo_offer";
        $this->theme_color                  = theme_color();
        $this->admin_panel_slug   			= config('app.project.admin_panel_slug');
        $this->module_url_path              = url(config('app.project.admin_panel_slug')."/promo_offer");
    } 
            
    public function index(Request $request)
    {   
		$this->arr_view_data['page_title']      = "Manage ".str_plural($this->module_title);
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        $arr_data = $this->PromoOfferModel->orderBy('id','DESC')->get()->toArray();  
        $this->arr_view_data['arr_data']     = $arr_data;
        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }
    public function create()
    {

    	$this->arr_view_data['page_title']      = "Add ".str_plural($this->module_title);
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        return view($this->module_view_folder.'.create', $this->arr_view_data);
    }
    public function store(Request $request)
    {
    	$arr_rules = [];

        $arr_rules['code_type']              = "required";
        $arr_rules['validity_from']          = "required";
        $arr_rules['validity_to']            = "required";
        $arr_rules['percentage']             = "required";
        $arr_rules['max_amount']             = "required";
        $arr_rules['code']                   = "required";
        $arr_rules['promo_code_usage_limit'] = "required";

        $validator = Validator::make($request->all(),$arr_rules);
    	
    	if($validator->fails())
    	{
    		return redirect()->back()->withErrors($validator)->withInput($request->all());
    	}

        $code = $request->input('code');
        
        $code_existance = $this->PromoOfferModel->where('code',$code)->first();

        if(count($code_existance)>0)
        {
            Flash::error('This code is already used');
            return redirect()->back()->withInput($request->all());
        }

        $percentage             = floatval($request->input('percentage'));
        $max_amount             = floatval($request->input('max_amount'));
        $promo_code_usage_limit = intval($request->input('promo_code_usage_limit'));

        $arr_data                           = [];
        $arr_data['code_type']              = $request->input('code_type');
        $arr_data['validity_from']          = $request->input('validity_from');
        $arr_data['validity_to']            = $request->input('validity_to');
        $arr_data['percentage']             = $percentage;
        $arr_data['max_amount']             = $max_amount;
        $arr_data['promo_code_usage_limit'] = $promo_code_usage_limit;
        $arr_data['code']                   = trim($request->input('code'));
        $arr_data['is_active']              = 1;
       	
        //dd($arr_data);
        $result = $this->PromoOfferModel->create($arr_data);
    	if($result)
    	{
        	Flash::success(str_singular($this->module_title).' Created Successfully');
    	}
    	else
    	{
    		Flash::error('Problem Occurred, While Creating '.str_singular($this->module_title));
    	}

    	return redirect()->back();
    }
    public function edit($enc_id)
    {
        $promo_id = base64_decode($enc_id); 
        $this->arr_view_data['page_title']      = "Edit ".str_plural($this->module_title);
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;

        $arr_data = $this->PromoOfferModel->where('id',$promo_id)->first();  
        $this->arr_view_data['arr_data']     = $arr_data;
        $this->arr_view_data['promo_id']     = $promo_id;
        return view($this->module_view_folder.'.edit', $this->arr_view_data);
    }
    public function update(Request $request)
    {
        $arr_rules                           = [];
        $arr_rules['code_type']              = "required";
        $arr_rules['validity_from']          = "required";
        $arr_rules['validity_to']            = "required";
        $arr_rules['percentage']             = "required";
        $arr_rules['max_amount']             = "required";
        $arr_rules['code']                   = "required";
        $arr_rules['promo_code_usage_limit'] = "required";

        $validator = Validator::make($request->all(),$arr_rules);
        
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $percentage             = floatval($request->input('percentage'));
        $max_amount             = floatval($request->input('max_amount'));
        $promo_code_usage_limit = intval($request->input('promo_code_usage_limit'));

        $arr_data                           = [];
        $arr_data['code_type']              = $request->input('code_type');
        $arr_data['validity_from']          = $request->input('validity_from');
        $arr_data['validity_to']            = $request->input('validity_to');
        $arr_data['percentage']             = $percentage;
        $arr_data['max_amount']             = $max_amount;
        $arr_data['promo_code_usage_limit'] = $promo_code_usage_limit;
        $arr_data['code']                   = trim($request->input('code'));

        $result = $this->PromoOfferModel
                        ->where('id',$request->input('promo_id'))
                        ->update($arr_data);
        if($result)
        {
            Flash::success(str_singular($this->module_title).' Updated Successfully');
        }
        else
        {
            Flash::error('Problem Occurred, While Creating '.str_singular($this->module_title));
        }

        return redirect($this->module_url_path);
    }
    function code_existence(Request $request)
    {
        $code = trim($request->input("code"));
        $arr_codes = $this->PromoOfferModel
                                    ->select('code')
                                    ->whereDate("validity_to",">=",date("Y-m-d"))
                                    ->where("code",$code)
                                    ->get()
                                    ->toArray();  
        if(count($arr_codes)>0)
        {
            echo "error";
            return;
        }
        else    
        {
            echo "success";
            return;
        }

    }
}	
