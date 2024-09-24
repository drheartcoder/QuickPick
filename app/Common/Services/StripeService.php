<?php 

namespace App\Common\Services;
use Illuminate\Http\Request;

use \Stripe\Stripe as Stripe;
use \Stripe\Token as Token;
use \Stripe\Charge as Charge;
use \Stripe\Customer as Customer;
use \Stripe\Account as Account;
use \Stripe\Plan as Plan;
use \Stripe\Refund as Refund;
use \Stripe\Subscription as Subscription;
use \Stripe\Event as Event;
use \Stripe\Invoice as Invoice;
use \Stripe\retrieve as Retrive;
use \Stripe\Transfer as Transfer;

use App\Models\UserModel;
use App\Models\CardDetailsModel;
use App\Models\BookingMasterModel;
use App\Models\LoadPostRequestModel;


class StripeService
{                 
	public function __construct(UserModel $user,CardDetailsModel $card_details,BookingMasterModel $booking_master,LoadPostRequestModel $load_post_request)
	{

		$this->UserModel            = $user;
		$this->CardDetailsModel     = $card_details;
		$this->BookingMasterModel   = $booking_master;
		$this->LoadPostRequestModel = $load_post_request;

		if(env('IS_LIVE_MODE') == 'YES')
		{
			/*LIVE KEYS*/
			$this->publish_key  = config('services.stripe_live.api_key');
			$this->secret_key   = config('services.stripe_live.api_secret');
		}
		else if(env('IS_LIVE_MODE') == 'NO')
		{
			/*TEST KEYS*/
			$this->publish_key  = config('services.stripe_test.api_key');
			$this->secret_key   = config('services.stripe_test.api_secret');
		}

		$this->stripe 	    = Stripe::setApiKey($this->secret_key);
	}
	
	

	/* create customer  */
	public function register_customer(Array $arr_data = [])
	{
		$arr_response = [];

		if(isset($arr_data['stripe_token']) == false || $arr_data['stripe_token'] == '')
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = "Stripe Token is Missing";
			$arr_response['data']    = [];
			return $arr_response;
		}
		if(isset($arr_data['user_id']) == false || $arr_data['user_id'] == '')
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = "User Token is Missing";
			$arr_response['data']    = [];
			return $arr_response;
		}

		$user_id  	  =  isset($arr_data['user_id']) ? $arr_data['user_id'] : 0;
		
		$stripe_token =  isset($arr_data['stripe_token']) ? $arr_data['stripe_token'] :'';

		$arr_user_details = $this->get_user_details($user_id);
		
		if(isset($arr_user_details) && sizeof($arr_user_details)>0)
		{
			if($arr_user_details['stripe_customer_id'] && $arr_user_details['stripe_customer_id']!='')
			{
				$stripe_customer_id = isset($arr_user_details['stripe_customer_id']) ? $arr_user_details['stripe_customer_id'] :'';	
				try 
				{
					$obj_stripe_token      = Token::retrieve($stripe_token);
					$obj_existing_customer = Customer::retrieve($stripe_customer_id);

					if((isset($obj_existing_customer) && sizeof($obj_existing_customer)>0) && (isset($obj_stripe_token) && sizeof($obj_stripe_token)>0))
					{
						$email       = isset($arr_user_details['email']) ? $arr_user_details['email']:'';
						$description = $this->make_user_description($arr_user_details);
						if($email!=''){
							$obj_existing_customer->email       = $email;
						}
						$obj_existing_customer->description = $description;
						

						$arr_existing_card['user_id'] 	                = $user_id;
						$arr_existing_card['unique_number_identifier'] = isset($obj_stripe_token->card->fingerprint) ? $obj_stripe_token->card->fingerprint :'';

						$is_existing_card  = $this->check_existing_card_details($arr_existing_card);
						/*$is_existing_card == false then add new card details in our database as well as on stripe account other wise just up date customer details*/

						if($is_existing_card==false)
						{
						 	$obj_card = $obj_existing_customer->sources->create(array("source" => $stripe_token));
						 	
						 	$obj_existing_customer->save();
						 	$masked_card_number = '';
							if(isset($obj_card->last4) && $obj_card->last4!=''){
								$masked_card_number = 'XXXXXXXXXXXX'.$obj_card->last4;
							}

							$arr_card_details 							  = [];
							$arr_card_details['user_id'] 			      = $user_id;
							$arr_card_details['card_id'] 			      = isset($obj_card->id) ? $obj_card->id :'';
							$arr_card_details['src_token'] 			      = $stripe_token;
							$arr_card_details['masked_card_number']       = $masked_card_number;
							$arr_card_details['unique_number_identifier'] = isset($obj_card->fingerprint) ? $obj_card->fingerprint :'';
							$arr_card_details['brand'] 					  = isset($obj_card->brand) ? $obj_card->brand :'';
							$arr_card_details['payment_method'] 	      = 'stripe';

							$insert_card = $this->CardDetailsModel->create($arr_card_details);
							
							if($insert_card)
							{
								$arr_response['status']  = 'success';
								$arr_response['msg'] 	 = 'Card added successfully.';
								$arr_response['data']    = array(
																	'card_id'=>isset($insert_card->id) ? $insert_card->id :0,
																	'masked_card_number'=>$masked_card_number
																);
								return $arr_response;
							}
							else
							{
								$arr_response['status'] = 'error';
								$arr_response['msg']    = "Problem occured, while registering card, Please try again.";
								$arr_response['data']    = [];
								return $arr_response;		
							}
						}
						else
						{
							$obj_existing_customer->save();
							$arr_response['status']  = 'success';
							$arr_response['msg'] 	 = 'Card added successfully.';
							$arr_response['data']    = array(
																'card_id'=>isset($is_existing_card->id) ? $is_existing_card->id :0,
																'masked_card_number'=>isset($is_existing_card->masked_card_number) ? $is_existing_card->masked_card_number :'',

															);
							return $arr_response;
						}
					}
					else
					{
						$arr_response['status'] = 'error';
						$arr_response['msg']    = "Problem occured, while retrieving existing stripe customer,Please try again.";
						$arr_response['data']    = [];
						return $arr_response;		
					}
				} 
				catch (\Exception $e) 
				{
					if($e->getCode() == 0)
					{
						/*if error code is 0 then make user tables stripe_customer_id to empty*/
						$arr_stripe_customer['user_id'] 	= $user_id;
						$arr_stripe_customer['customer_id'] = '';
						$this->update_stripe_customer_id($arr_stripe_customer);
					}
					$arr_response['status'] = 'error';
					$arr_response['msg']    = $e->getMessage();
					$arr_response['data']    = [];
					return $arr_response;		
				}
			}
			else
			{
				$email       = isset($arr_user_details['email']) ? $arr_user_details['email']:'';
				$description = $this->make_user_description($arr_user_details);

				try {
					
					$obj_new_customer = Customer::create(array(
													   "email"       => $email,
													   "description" => $description,
													   "source"      => $stripe_token
												    ));

					
					if(isset($obj_new_customer) && sizeof($obj_new_customer)>0)
					{	
						$arr_stripe_customer['user_id'] 	= $user_id;
						$arr_stripe_customer['customer_id'] = isset($obj_new_customer->id) ? $obj_new_customer->id :'';

						/*update customer id in users table*/
						$is_stripe_customer_id_updated = $this->update_stripe_customer_id($arr_stripe_customer);
						if($is_stripe_customer_id_updated)
						{
							/*if user is using existing card then then return card details tables primary key for future reference*/
							$arr_existing_card['user_id'] 	                = $user_id;
							$arr_existing_card['unique_number_identifier'] = isset($obj_new_customer->sources->data[0]->fingerprint) ? $obj_new_customer->sources->data[0]->fingerprint :'';

							$is_existing_card  = $this->check_existing_card_details($arr_existing_card);
							
							if($is_existing_card==false)
							{
							    $masked_card_number = '';
								if(isset($obj_new_customer->sources->data[0]->last4) && $obj_new_customer->sources->data[0]->last4!=''){
									$masked_card_number = 'XXXXXXXXXXXX'.$obj_new_customer->sources->data[0]->last4;
								}

								$arr_card_details 							  = [];
								$arr_card_details['user_id'] 			      = $user_id;
								$arr_card_details['card_id'] 			      = isset($obj_new_customer->sources->data[0]->id) ? $obj_new_customer->sources->data[0]->id :'';
								$arr_card_details['src_token'] 			      = $stripe_token;
								$arr_card_details['masked_card_number']       = $masked_card_number;
								$arr_card_details['unique_number_identifier'] = isset($obj_new_customer->sources->data[0]->fingerprint) ? $obj_new_customer->sources->data[0]->fingerprint :'';
								$arr_card_details['brand'] 					  = isset($obj_new_customer->sources->data[0]->brand) ? $obj_new_customer->sources->data[0]->brand :'';
								$arr_card_details['payment_method'] 	      = 'stripe';

								$insert_card = $this->CardDetailsModel->create($arr_card_details);
								
								if($insert_card)
								{
									$arr_response['status']  = 'success';
									$arr_response['msg'] 	 = 'Stripe card details successfully added.';
									$arr_response['data']    = array(
																	'card_id'=>isset($insert_card->id) ? $insert_card->id :0,
																	'masked_card_number'=>$masked_card_number
																);
									return $arr_response;
								}
								else
								{
									$arr_response['status'] = 'error';
									$arr_response['msg']    = "Problem occured, while registering card, Please try again.";
									$arr_response['data']    = [];
									return $arr_response;		
								}
							}
							else
							{
								$arr_response['status']  = 'success';
								$arr_response['msg'] 	 = 'Stripe card details successfully added.';
								$arr_response['data']    = array(
																'card_id'=>isset($is_existing_card->id) ? $is_existing_card->id :0,
																'masked_card_number'=>isset($is_existing_card->masked_card_number) ? $is_existing_card->masked_card_number :'',

															);
								return $arr_response;
							}
						}
						else
						{
							$arr_response['status'] = 'error';
							$arr_response['msg']    = "Problem occured, while processing stripe customer id,Please try again.";
							$arr_response['data']    = [];
							return $arr_response;		
						}
					}
					else
					{
						$arr_response['status'] = 'error';
						$arr_response['msg']    = "Problem occured, while registering new strip customer.";
						$arr_response['data']    = [];
						return $arr_response;
					}
				}
				catch(\Exception $e){
					$arr_response['status'] = 'error';
					$arr_response['msg']    = $e->getMessage();
					$arr_response['data']   = [];
					return $arr_response;

				}
			}
		}
		else
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = "Something went wrong, user details not found , Please try again.";
			$arr_response['data']    = [];
			return $arr_response;
		}		
	}
	
	public function charge_customer(Array $arr_data = [])
	{
		$arr_response = [];
		$arr_response['status'] = 'error';
		$arr_response['msg']    = 'Problem occured while doing online payment,Please try again.';

		$user_id           = isset($arr_data['user_id']) ? $arr_data['user_id'] :0;
		$card_id           = isset($arr_data['card_id']) ? $arr_data['card_id'] :0;
		$total_charge      = isset($arr_data['total_charge']) ? $arr_data['total_charge'] :0;
		$booking_id        = isset($arr_data['booking_id']) ? $arr_data['booking_id'] :'';
		$booking_unique_id = isset($arr_data['booking_unique_id']) ? $arr_data['booking_unique_id'] :'';

		$arr_card_details = $this->get_card_details($user_id,$card_id);
		
		if(isset($arr_card_details) && sizeof($arr_card_details)>0)
		{
			$stripe_customer_id = isset($arr_card_details['stripe_customer_id']) ? $arr_card_details['stripe_customer_id'] :'';
			$stripe_card_id = isset($arr_card_details['card_id']) ? $arr_card_details['card_id'] :'';
			if($stripe_customer_id!='' && $stripe_card_id!='')
			{	
				$charge_description = "Quickpick Charge for completed trip - ".$booking_unique_id;
				
				$arr_metadata = 
								[
									'booking_id'        => $booking_id,
									'booking_unique_id' => $booking_unique_id,
									'first_name'        => isset($arr_card_details['first_name']) ? $arr_card_details['first_name'] :'',
									'last_name'         => isset($arr_card_details['last_name']) ? $arr_card_details['last_name'] :'',
									'email'             => isset($arr_card_details['email']) ? $arr_card_details['email'] :'',
									'mobile_no'         => isset($arr_card_details['mobile_no']) ? $arr_card_details['mobile_no'] :'',
								];
				try 
				{
					$obj_stripe_user = Customer::retrieve($stripe_customer_id);
					$obj_stripe_user->default_source=$stripe_card_id;
					$obj_stripe_user->save();

					if($obj_stripe_user)
					{
						$obj_charge = Charge::create(array
													(
													  "amount"      => $total_charge,
													  "currency"    => 'usd',	
													  "customer"    => $stripe_customer_id,
													  "description" => $charge_description,
													  "metadata"    => $arr_metadata,
													));

						if(isset($obj_charge->captured) && $obj_charge->captured==true)
						{
							$arr_response['status']       = 'success';
							$arr_response['msg']          = 'payment successfully done';
							$arr_response['payment_data'] = json_encode($obj_charge);
							return $arr_response;
						}
					}

				} 
				catch (\Exception $e) 
				{
					$arr_response['status']       = 'error';
					$arr_response['msg']          = $e->getMessage();
					$arr_response['payment_data'] = json_encode($e);
					return $arr_response;
				}
			}
			else
			{
				$arr_response['status'] = 'error';
				$arr_response['msg']    = 'Stripe user details or stripe card details not found';
				return $arr_response;
			}
		}
		else
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Stripe user details or stripe card details not found.';
			return $arr_response;
		}
		return $arr_response;
	}
	
	public function charge_customer_initial_payment(Array $arr_data = [])
	{
		$arr_response = [];
		$arr_response['status'] = 'error';
		$arr_response['msg']    = 'Problem occured while doing initial payment,Please try again.';

		$user_id           = isset($arr_data['user_id']) ? $arr_data['user_id'] :0;
		$card_id           = isset($arr_data['card_id']) ? $arr_data['card_id'] :0;
		$total_charge      = isset($arr_data['total_charge']) ? $arr_data['total_charge'] :0;

		$arr_card_details = $this->get_card_details($user_id,$card_id);
		
		if(isset($arr_card_details) && sizeof($arr_card_details)>0)
		{
			$stripe_customer_id = isset($arr_card_details['stripe_customer_id']) ? $arr_card_details['stripe_customer_id'] :'';
			$stripe_card_id     = isset($arr_card_details['card_id']) ? $arr_card_details['card_id'] :'';

			if($stripe_customer_id!='' && $stripe_card_id!='')
			{	
				$charge_description = "Quickpick Initial Payment Charge for customer - ".$user_id;
				
				$arr_metadata = 
								[
									'customer_id'       => $user_id,
									'first_name'        => isset($arr_card_details['first_name']) ? $arr_card_details['first_name'] :'',
									'last_name'         => isset($arr_card_details['last_name']) ? $arr_card_details['last_name'] :'',
									'email'             => isset($arr_card_details['email']) ? $arr_card_details['email'] :'',
									'mobile_no'         => isset($arr_card_details['mobile_no']) ? $arr_card_details['mobile_no'] :'',
								];

				try 
				{
					$obj_stripe_user = Customer::retrieve($stripe_customer_id);
					$obj_stripe_user->default_source=$stripe_card_id;
					$obj_stripe_user->save();

					if($obj_stripe_user)
					{
						$obj_charge = Charge::create(array
													(
													  "amount"      => $total_charge,
													  "currency"    => 'usd',	
													  "customer"    => $stripe_customer_id,
													  "description" => $charge_description,
													  "metadata"    => $arr_metadata,
													));

						if(isset($obj_charge->captured) && $obj_charge->captured==true)
						{
							$arr_response['status']       = 'success';
							$arr_response['msg']          = 'payment successfully done';
							$arr_response['charge_id']    = isset($obj_charge->id) ? $obj_charge->id : '';
							return $arr_response;
						}
					}

				} 
				catch (\Exception $e) 
				{
					$arr_response['status']       = 'error';
					$arr_response['msg']          = $e->getMessage();
					$arr_response['payment_data'] = json_encode($e);
					return $arr_response;
				}
			}
			else
			{
				$arr_response['status'] = 'error';
				$arr_response['msg']    = 'Stripe user details or stripe card details not found';
				return $arr_response;
			}
		}
		else
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Stripe user details or stripe card details not found.';
			return $arr_response;
		}
		return $arr_response;
	}
	
	public function refund_customer_initial_payment($charge_id)
	{
		$arr_response = [];
		$arr_response['status'] = 'error';
		$arr_response['msg']    = 'Problem occured while refund initial payment,Please try again.';

		if($charge_id!='')
		{
			try 
			{
				$obj_refund = Refund::create(array(
									  "charge" => $charge_id
									));

				if(isset($obj_refund->status) && $obj_refund->status=='succeeded')
				{
					$arr_response['status']       = 'success';
					$arr_response['msg']          = 'Initial payment amount refund successfully,';
					return $arr_response;
				}

			} 
			catch (\Exception $e) 
			{
				$arr_response['status']       = 'error';
				$arr_response['msg']          = $e->getMessage();
				$arr_response['payment_data'] = json_encode($e);
				return $arr_response;
			}
		}
		return $arr_response;

	}

	public function make_driver_payment(Array $arr_data = [])
	{
		$arr_response = [];
		$arr_response['status'] = 'error';
		$arr_response['msg']    = 'Problem occured while doing online payment,Please try again.';

		$driver_id                = isset($arr_data['driver_id']) ? $arr_data['driver_id'] :0;
		$driver_stripe_account_id = isset($arr_data['driver_stripe_account_id']) ? $arr_data['driver_stripe_account_id'] :'';
		$booking_id               = isset($arr_data['booking_id']) ? $arr_data['booking_id'] :'';
		$booking_unique_id        = isset($arr_data['booking_unique_id']) ? $arr_data['booking_unique_id'] :'';
		$driver_earning_amount    = isset($arr_data['driver_earning_amount']) ? $arr_data['driver_earning_amount'] :0;
		$total_driver_cent_charge = isset($arr_data['total_driver_cent_charge']) ? $arr_data['total_driver_cent_charge'] :0;

		if($driver_stripe_account_id!='')
		{	
			$description = "Quickpick Payment for completed trip - ".$booking_unique_id;
			
			$arr_metadata = 
							[
								'booking_id'        => $booking_id,
								'booking_unique_id' => $booking_unique_id,
								'first_name'        => isset($arr_data['driver_first_name']) ? $arr_data['driver_first_name'] :'',
								'last_name'         => isset($arr_data['driver_last_name']) ? $arr_data['driver_last_name'] :'',
								'email'             => isset($arr_data['driver_email']) ? $arr_data['driver_email'] :'',
								'mobile_no'         => isset($arr_data['driver_mobile_no']) ? $arr_data['driver_mobile_no'] :'',
								'description'       => $description
							];
			try 
			{
				$obj_stripe_account = Account::retrieve($driver_stripe_account_id);
				
				if($obj_stripe_account)
				{
					$obj_transfer = Transfer::create(array
												(
												  "amount"      => $total_driver_cent_charge,
												  "currency"    => 'usd',	
												  "destination" => $driver_stripe_account_id,
												  "metadata"    => $arr_metadata,
												));

					if(isset($obj_transfer->id) && $obj_transfer->id!='')
					{
						$arr_response['status']       = 'success';
						$arr_response['msg']          = 'payment done successfully';
						$arr_response['payment_data'] = json_encode($obj_transfer);
						return $arr_response;
					}
					else
					{
						$arr_response['status']       = 'error';
						$arr_response['msg']          = 'payment not transfer to destination account.';
						$arr_response['payment_data'] = json_encode($obj_transfer);
						return $arr_response;
					}
				}
				else
				{
					$arr_response['status']       = 'error';
					$arr_response['msg']          = 'Account details not found.';
					$arr_response['payment_data'] = json_encode($obj_stripe_account);
					return $arr_response;
				}
			} 
			catch (\Exception $e) 
			{
				$arr_response['status']       = 'error';
				$arr_response['msg']          = $e->getMessage();
				$arr_response['payment_data'] = json_encode($e);
				return $arr_response;
			}
		}
		else
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Driver Stripe account details id is empty,cannot process.';
			return $arr_response;
		}
		return $arr_response;
	}
	
	public function make_company_payment(Array $arr_data = [])
	{
		$arr_response = [];
		$arr_response['status'] = 'error';
		$arr_response['msg']    = 'Problem occured while doing online payment,Please try again.';

		$company_id                = isset($arr_data['company_id']) ? $arr_data['company_id'] :0;
		$company_stripe_account_id = isset($arr_data['company_stripe_account_id']) ? $arr_data['company_stripe_account_id'] :'';
		$booking_id               = isset($arr_data['booking_id']) ? $arr_data['booking_id'] :'';
		$booking_unique_id        = isset($arr_data['booking_unique_id']) ? $arr_data['booking_unique_id'] :'';
		$company_earning_amount    = isset($arr_data['company_earning_amount']) ? $arr_data['company_earning_amount'] :0;
		$total_company_cent_charge = isset($arr_data['total_company_cent_charge']) ? $arr_data['total_company_cent_charge'] :0;

		if($company_stripe_account_id!='')
		{	
			$description = "Quickpick Payment for completed trip - ".$booking_unique_id;
			
			$arr_metadata = 
							[
								'booking_id'        => $booking_id,
								'company_id'        => $company_id,
								'booking_unique_id' => $booking_unique_id,
								'company_name'      => isset($arr_data['company_name']) ? $arr_data['company_name'] :'',
								'company_email'     => isset($arr_data['company_email']) ? $arr_data['company_email'] :'',
								'company_mobile_no' => isset($arr_data['company_mobile_no']) ? $arr_data['company_mobile_no'] :'',
								'description'       => $description
							];
			try 
			{
				$obj_stripe_account = Account::retrieve($company_stripe_account_id);
				
				if($obj_stripe_account)
				{
					$obj_transfer = Transfer::create(array
												(
												  "amount"      => $total_company_cent_charge,
												  "currency"    => 'usd',	
												  "destination" => $company_stripe_account_id,
												  "metadata"    => $arr_metadata,
												));

					if(isset($obj_transfer->id) && $obj_transfer->id!='')
					{
						$arr_response['status']       = 'success';
						$arr_response['msg']          = 'payment done successfully';
						$arr_response['payment_data'] = json_encode($obj_transfer);
						return $arr_response;
					}
					else
					{
						$arr_response['status']       = 'error';
						$arr_response['msg']          = 'payment not transfer to destination account.';
						$arr_response['payment_data'] = json_encode($obj_transfer);
						return $arr_response;
					}
				}
				else
				{
					$arr_response['status']       = 'error';
					$arr_response['msg']          = 'Account details not found.';
					$arr_response['payment_data'] = json_encode($obj_stripe_account);
					return $arr_response;
				}
			} 
			catch (\Exception $e) 
			{
				$arr_response['status']       = 'error';
				$arr_response['msg']          = $e->getMessage();
				$arr_response['payment_data'] = json_encode($e);
				return $arr_response;
			}
		}
		else
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Company Stripe account details id is empty,cannot process.';
			return $arr_response;
		}
		return $arr_response;
	}

	public function check_stripe_account_exist($driver_stripe_account_id)
	{
		$arr_response = [];
		if($driver_stripe_account_id!='')
		{
			try 
			{
				$obj_stripe_account = Account::retrieve($driver_stripe_account_id);
				
				if($obj_stripe_account)
				{
					$arr_response['status']       = 'success';
					$arr_response['msg']          = 'stripe account id found';
					$arr_response['data'] 		  = array('stripe_account_id'=>$driver_stripe_account_id);
					return $arr_response;	
				}
				else
				{
					$arr_response['status'] = 'error';
					$arr_response['msg']    = 'Stripe Account details not found.';
					$arr_response['data'] 	= [];
					return $arr_response;
				}
			} 
			catch (\Exception $e) 
			{
				$arr_response['status']   = 'error';
				$arr_response['msg']      = $e->getMessage();
				$arr_response['data']     = [];
				return $arr_response;
			}
		}

		$arr_response['status']   = 'error';
		$arr_response['msg']      = 'Something went wrong,Please try again.';
		$arr_response['data']     = [];
		return $arr_response;
	}
	public function validate_card(Array $arr_data = [])
	{
		$arr_response = [];

		if(isset($arr_data['card_id']) == false || $arr_data['card_id'] == '')
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = "Stripe card details are Missing";
			$arr_response['data']    = [];
			return $arr_response;
		}
		if(isset($arr_data['user_id']) == false || $arr_data['user_id'] == '')
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = "User Token is Missing";
			$arr_response['data']   = [];
			return $arr_response;
		}

		$user_id  	= isset($arr_data['user_id']) ? $arr_data['user_id'] : 0;
		$card_id    = isset($arr_data['card_id']) ? $arr_data['card_id'] :'';
		$arr_card_details = $this->get_card_details($user_id,$card_id);

		if(isset($arr_card_details) && sizeof($arr_card_details)>0)
		{
			$stripe_customer_id = isset($arr_card_details['stripe_customer_id']) ? $arr_card_details['stripe_customer_id'] :'';
			$stripe_card_id     = isset($arr_card_details['card_id']) ? $arr_card_details['card_id'] :'';

			try
			{
				$obj_customer = Customer::retrieve($stripe_customer_id);
				if(sizeof($obj_customer)>0)
				{
					$obj_card = $obj_customer->sources->retrieve($stripe_card_id);
					if(sizeof($obj_card)>0)
					{
						$arr_response['status'] = 'success';
						$arr_response['msg']    = 'Card details found successfully.';
						$arr_response['data']   = [];
						return $arr_response;			
					}
					else
					{
						$arr_response['status'] = 'error';
						$arr_response['msg']    = 'Card details not found,Please try again.';
						$arr_response['data']   = [];
						return $arr_response;			
					}
				}
				else
				{
					$arr_response['status'] = 'error';
					$arr_response['msg']    = 'User & Card not found,Please try again.';
					$arr_response['data']   = [];
					return $arr_response;			
				}
			}
			catch (\Exception $e) 
			{
				$arr_response['status'] = 'error';
				$arr_response['msg']    = $e->getMessage();
				$arr_response['data']   = [];
				return $arr_response;
			}
		}
		else
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'User & Card details not found,Please try again.';
			$arr_response['data']   = [];
			return $arr_response;			
		}

		$arr_response['status'] = 'error';
		$arr_response['msg']    = 'Something went wrong,Please try again.';
		$arr_response['data']   = [];
		return $arr_response;			
	}

	public function delete_card($arr_data)
	{
		$user_id  	= isset($arr_data['user_id']) ? $arr_data['user_id'] : 0;
		$card_id    = isset($arr_data['card_id']) ? $arr_data['card_id'] :'';

		$arr_card_details = $this->get_card_details($user_id,$card_id);
		
		if(isset($arr_card_details) && sizeof($arr_card_details)>0)
		{
			$is_load_posted = $this->check_posted_load_again_card($user_id,$card_id);
			if($is_load_posted>0)
			{
				$arr_response['status'] = 'error';
				$arr_response['msg']    = 'You cannot delete card, Shipment post request is in progress.';
				return $arr_response;			
			}

			$is_ongoing_trip = $this->check_ongoing_trip_again_card($user_id,$card_id);
			if($is_ongoing_trip>0)
			{
				$arr_response['status'] = 'error';
				$arr_response['msg']    = 'You cannot delete card, trip booking is in progress.';
				return $arr_response;			
			}
			
			$stripe_customer_id = isset($arr_card_details['stripe_customer_id']) ? $arr_card_details['stripe_customer_id'] :'';
			$stripe_card_id     = isset($arr_card_details['card_id']) ? $arr_card_details['card_id'] :'';

			try
			{
				$obj_customer = Customer::retrieve($stripe_customer_id);
				$obj_card     = $obj_customer->sources->retrieve($stripe_card_id)->delete();

				$status = 	$this->CardDetailsModel->where('id',$card_id)->delete();
				if($status)
				{
					$arr_response['status'] = 'success';
					$arr_response['msg']    = 'Card deleted successfully.';
					return $arr_response;			
				}
				else
				{
					$arr_response['status'] = 'error';
					$arr_response['msg']    = 'Problem occured while deleting card.';
					return $arr_response;			
				}

		
			}
			catch (\Exception $e) 
			{
				$status = 	$this->CardDetailsModel->where('id',$card_id)->delete();
				if($status)
				{
					$arr_response['status'] = 'success';
					$arr_response['msg']    = 'Card deleted successfully.';
					return $arr_response;			
				}
				else
				{
					$arr_response['status'] = 'error';
					$arr_response['msg']    = 'Problem occured while deleting card.';
					return $arr_response;			
				}
				$arr_response['status'] = 'error';
				$arr_response['msg']    = 'Problem occured while deleting card.';
				return $arr_response;			
			}
		}
		else
		{
			$arr_response['status'] = 'error';
			$arr_response['msg']    = 'Card details not found,Please try again.';
			return $arr_response;
		}
		$arr_response['status'] = 'error';
		$arr_response['msg']    = 'Something went wrong,Please try again.';
		return $arr_response;
		
	}
	
	public function check_existing_card_details($arr_existing_card)
	{
		$user_id 	                = isset($arr_existing_card['user_id']) ? $arr_existing_card['user_id'] :0;
		$unique_number_identifier   = isset($arr_existing_card['unique_number_identifier']) ? $arr_existing_card['unique_number_identifier'] :'';

		$obj_card_details   = $this->CardDetailsModel
									->where('user_id',$user_id)
					   			    ->where('payment_method','stripe')
								    ->where('unique_number_identifier',$unique_number_identifier)
								    ->first();

		if(isset($obj_card_details) && $obj_card_details!=false)
		{
			return $obj_card_details;
		}
		else
		{
			return false;
		}
	}

	private function get_user_details($user_id)
	{
		$arr_user = [];

		$obj_user = $this->UserModel
								->select('id','first_name','last_name','email','mobile_no','stripe_customer_id')
								->where('id',$user_id)
								->first();
		if($obj_user!=false){
			$arr_user = $obj_user->toArray();
		}
		return $arr_user;		
	}
	
	private function make_user_description($arr_user_details)
	{
		$user_description = '';
		$first_name = isset($arr_user_details['first_name']) ? $arr_user_details['first_name'] :'';
		$last_name  = isset($arr_user_details['last_name']) ? $arr_user_details['last_name'] :'';
		$mobile_no  = isset($arr_user_details['mobile_no']) ? $arr_user_details['mobile_no'] :'';

		$full_name  = $first_name.' '.$last_name;
		$full_name  = ($full_name!=' ') ? $full_name :'-';

		$user_description .= $full_name;
		if($mobile_no!='')
		{
			$user_description.=	' ('.$mobile_no.')';
		}
		$user_description.=	' Quick-Pick Customer';
		return $user_description;
	}
	
	private function update_stripe_customer_id($arr_stripe_customer)
	{
		$user_id     = isset($arr_stripe_customer['user_id']) ? $arr_stripe_customer['user_id'] :0;
		$customer_id = isset($arr_stripe_customer['customer_id']) ? $arr_stripe_customer['customer_id'] :'';

		$update_status = $this->UserModel
									->where('id',$user_id)
									->update(['stripe_customer_id' => $customer_id]);

		if($update_status){
			return true;
		}
		return false;
	}

	private function get_card_details($user_id,$card_id)
	{
		$arr_card_details = [];
		$obj_card_details = $this->CardDetailsModel
										->select('user_id','card_id','src_token','masked_card_number','unique_number_identifier','brand','payment_method')
										->whereHas('user_details',function($query)use($user_id){
											$query->where('id',$user_id);
										})
										->with(['user_details'=>function($query)use($user_id){
											$query->select('id','stripe_customer_id','first_name','last_name','email','mobile_no');
											$query->where('id',$user_id);
										}])
										->where('id',$card_id)
										->first();
		if($obj_card_details)
		{
			$arr_card_details = $obj_card_details->toArray();
			
			if(isset($arr_card_details) && sizeof($arr_card_details)>0)
			{
				$arr_card_details['stripe_customer_id'] = isset($arr_card_details['user_details']['stripe_customer_id']) ? $arr_card_details['user_details']['stripe_customer_id'] :'';
				$arr_card_details['first_name']         = isset($arr_card_details['user_details']['first_name']) ? $arr_card_details['user_details']['first_name'] :'';
				$arr_card_details['last_name']          = isset($arr_card_details['user_details']['last_name']) ? $arr_card_details['user_details']['last_name'] :'';
				$arr_card_details['email']              = isset($arr_card_details['user_details']['email']) ? $arr_card_details['user_details']['email'] :'';
				$arr_card_details['mobile_no']          = isset($arr_card_details['user_details']['mobile_no']) ? $arr_card_details['user_details']['mobile_no'] :'';
				
				unset($arr_card_details['user_details']);
			}
		}
		return $arr_card_details;
	}
	private function check_posted_load_again_card($user_id,$card_id)
	{
		$obj_exist_count = $this->LoadPostRequestModel
									->whereIn('request_status',['USER_REQUEST','REJECT_BY_USER','ACCEPT_BY_DRIVER','REJECT_BY_DRIVER','TIMEOUT'])
									->where('user_id',$user_id)
									->where('card_id',$card_id)
									->count();

		return $obj_exist_count;	
	}
	private function check_ongoing_trip_again_card($user_id,$card_id)
	{
		$obj_exist_count = $this->BookingMasterModel
									->whereHas('load_post_request_details',function($query) use($user_id){
										$query->where('user_id',$user_id);
									})
									->whereIn('booking_status',['TO_BE_PICKED','IN_TRANSIT'])
									->where('card_id',$card_id)
									->count();

		return $obj_exist_count;
	}

}
?>