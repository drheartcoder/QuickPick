<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\DepositMoneyModel;
use App\Models\BookingMasterModel;

use Validator;
use Flash;
use Sentinel;

class DepositMoneyController extends Controller
{

	public function __construct(DepositMoneyModel $deposit_money,BookingMasterModel $booking_master)
	{
		
		$this->DepositMoneyModel  = $deposit_money;
		$this->BaseModel          = $this->DepositMoneyModel;
		$this->BookingMasterModel = $booking_master;

		$this->arr_view_data           = [];
		$this->module_url_path         = url(config('app.project.company_panel_slug')."/deposit_money");
		
		$this->module_title            = "Company Deposit Money";
		$this->modyle_url_slug         = "Company Deposit Money";
		$this->module_view_folder      = "company.deposit_money";
		$this->theme_color             = theme_color();

		$this->company_id = 0;

        $user = Sentinel::check();
        if($user){
            $this->company_id   = isset($user->id) ? $user->id :0;
            $this->company_name = isset($user->company_name) ? $user->company_name :'';
        }

        $this->receipt_image_public_path    = url('/').config('app.project.img_path.payment_receipt');
        $this->receipt_image_base_path      = base_path().config('app.project.img_path.payment_receipt');


	}

	public function index()
	{
		$arr_company_balance_information = $this->get_company_balance_information();

		$arr_deposit_money = [];

        $obj_company_deposit_money = $this->DepositMoneyModel
                                                ->with('booking_master_details','to_user_details')
                                                ->where('to_user_id',$this->company_id)
                                                ->where('to_user_type','COMPANY')
                                                ->orderBy('id','DESC')
                                                ->get();


        $arr_company_deposit_money = [];
        if($obj_company_deposit_money){
            $arr_company_deposit_money = $obj_company_deposit_money->toArray();
        } 

        $obj_company_driver_deposit_money = $this->DepositMoneyModel
                                                ->with('booking_master_details','to_user_details')
                                                ->where('from_user_id',$this->company_id)
                                                ->where('from_user_type','COMPANY')
                                                ->orderBy('id','DESC')
                                                ->get();

        $arr_company_driver_deposit_money =[];
        if($obj_company_driver_deposit_money)
        {
            $arr_company_driver_deposit_money = $obj_company_driver_deposit_money->toArray();
        }

        $arr_deposit_money = array_merge($arr_company_deposit_money,$arr_company_driver_deposit_money);

		$this->arr_view_data['page_title']                      = str_singular($this->module_title);
		$this->arr_view_data['module_title']                    = $this->module_title;
		$this->arr_view_data['module_url_path']                 = $this->module_url_path;
		$this->arr_view_data['theme_color']                     = $this->theme_color;
		$this->arr_view_data['arr_deposit_money']       = $arr_deposit_money;
		$this->arr_view_data['arr_company_balance_information'] = $arr_company_balance_information;
		$this->arr_view_data['receipt_image_public_path']       = $this->receipt_image_public_path;
		$this->arr_view_data['receipt_image_base_path']         = $this->receipt_image_base_path;

		return view($this->module_view_folder.'.index',$this->arr_view_data);
	}

	public function change_status(Request $request)
	{
		$id = $request->input('enc_id');
		$type = $request->input('type');

		$id = base64_decode($id);	
		
		if($id == '')
		{
			Flash::error('Problem Occured, While Updating Status,Please try again');  
			return redirect()->back();	
		}
		if($type == 'approve' || $type == 'reject')
		{
			
			$status = '';
			if($type == 'approve'){
				$status = 'APPROVED';
			}
			else if($type == 'reject'){
				$status = 'REJECTED';
			}

			$result = $this->DepositMoneyModel->where('id',$id)->update(['status' => $status]);
			if($result)
			{
				Flash::success(str_singular($this->module_title).' Updated Successfully'); 
				return redirect()->back();	
			}
			else
			{
				Flash::error('Problem Occured, While Updating '.str_singular($this->module_title));  
				return redirect()->back();	
			} 	

		}

		Flash::error('Something went wrong cannot update details,Please try again.');  
		return redirect()->back();	
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
