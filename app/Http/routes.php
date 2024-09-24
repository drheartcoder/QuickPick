<?php
/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/



/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/
/*------------------For designing purpose -designers---------------------------------*/

/*----------------------------------------------------------------------------------------
	Front Roles
----------------------------------------------------------------------------------------*/



Route::group(array('prefix' => '/','middleware'=>['front','web']), function()
{

	$route_slug       = "";
	$module_controller = "Front\HomeController@";

	Route::get('',['as' => $route_slug.'index',  'uses' => $module_controller.'index']);

	Route::get('genrate_pdf',['as' => $route_slug.'genrate_pdf',  'uses' => $module_controller.'genrate_pdf']);
	
	Route::get('how_it_works',['as' => $route_slug.'how_it_works',  'uses' => $module_controller.'how_it_works']);

	Route::get('our_fleet',['as' => $route_slug.'our_fleet',  'uses' => $module_controller.'our_fleet']);

	Route::get('join_our_fleet',['as' => $route_slug.'join_our_fleet',  'uses' => $module_controller.'join_our_fleet']);

	Route::get('contact_us',['as' => $route_slug.'contact_us',  'uses' => 'Front\ContactUsController@index']);

	Route::get('terms_and_conditions',['as' => $route_slug.'terms_and_conditions',  'uses' => $module_controller.'terms_and_conditions']);

	Route::get('policy',['as' => $route_slug.'policy',  'uses' => $module_controller.'policy']);
	
	/*Route::get('how_it_works_drive',['as' => $route_slug.'how_it_works_driver',  'uses' => $module_controller.'how_it_works_driver']);

	Route::get('how_it_works_ride',['as' => $route_slug.'how_it_works_user',  'uses' => $module_controller.'how_it_works_user']);*/


	Route::post('store_contact_enquiry',['as' => $route_slug.'store_contact_enquiry',  'uses' => 'Front\ContactUsController@store_contact_enquiry']);

	Route::post('need_delivery', ['as' => $route_slug.'need_delivery',  'uses' => 'Front\HomeController@need_delivery']);

	Route::post('store_subscriber',['as' => $route_slug.'store_subscriber',  'uses' => $module_controller.'store_subscriber']);


	Route::get('help',['as' => $route_slug.'help',  'uses' => $module_controller.'help']);
	
	Route::get('help_details',['as' => $route_slug.'help_details',  'uses' => $module_controller.'help_details']);

	Route::get('fare_estimate',['as' => $route_slug.'fare_estimate',  'uses' => $module_controller.'fare_estimate']);


	Route::get('chat',['as' => $route_slug.'chat',  'uses' => $module_controller.'chat']);

	Route::get('about_us',['as' => $route_slug.'about_us',  'uses' => $module_controller.'about_us']);
	
	Route::get('coming_soon',['as' => $route_slug.'coming_soon',  'uses' => $module_controller.'coming_soon']);

	//Route::get('change_file_contents',['as' => $route_slug.'index',  'uses' => $module_controller.'change_file_contents']);
	
	$module_controller = 'Front\AuthController@';
	$module_slug       = 'process_';

	
	Route::get('/login',						['as' 	 => $module_slug.'login', 
								  				'uses' 	 => $module_controller.'index']);

	Route::post('/process_login',	  			['as' 	 => $module_slug.'process_login',
												'uses' 	 => $module_controller.'process_login']);

	Route::get('/register',						['as' 	 => $module_slug.'register', 
								  				'uses' 	 => $module_controller.'register']);

	Route::post('/process_register',			['as' 	 => $module_slug.'process_register', 
								  				'uses' 	 => $module_controller.'process_register']);

	Route::get('/register_enterprise_admin',	['as' 	 => $module_slug.'register_enterprise_admin', 
								  				'uses' 	 => $module_controller.'register_enterprise_admin']);
	
	Route::post('/process_enterprise_admin_register',	['as' 	 => $module_slug.'process_enterprise_admin_register', 
								  				'uses' 	 => $module_controller.'process_enterprise_admin_register']);	
	
	Route::get('/forget_password',			    ['as' 	 => $module_slug.'forget_password', 
								  				'uses' 	 => $module_controller.'forget_password']);

	Route::post('/process_forget_password',		['as'    => $module_slug.'process_forget_password', 
												'uses' 	 => $module_controller.'process_forget_password']);
	
	Route::get('/verify_otp',	  				['as' 	 => $module_slug.'verify_otp',
												'uses' 	 => $module_controller.'verify_otp']);
	
	Route::get('/resend_otp',					['as' 	 => $module_slug.'resend_otp', 
								  				'uses'   => $module_controller.'resend_otp']);	

	Route::post('/process_verify_otp',	  		['as' 	 => $module_slug.'process_verify_otp',
												'uses' 	 => $module_controller.'process_verify_otp']);

	Route::get('/reset_password',				['as' 	 => $module_slug.'reset_password', 
								  				'uses' 	 => $module_controller.'reset_password']);

	Route::post('/process_reset_password',		['as' 	 => $module_slug.'process_reset_password', 
								  				'uses' 	 => $module_controller.'process_reset_password']);

	Route::get('/redirect_to_facebook',			['as' 	 => $module_slug.'redirect_to_facebook', 
								  				'uses' 	 => $module_controller.'redirect_to_facebook']);
	
	Route::get('/login_facebook',				['as' 	 => $module_slug.'login_facebook', 
								  				'uses' 	 => $module_controller.'login_facebook']);

	Route::post('/process_facebook_register',	['as' 	 => $module_slug.'process_facebook_register', 
								  				'uses' 	 => $module_controller.'process_facebook_register']);

	Route::get('/new_driver_vehicle',			['as' 	 => $module_slug.'new_driver_vehicle', 
								  				'uses' 	 => $module_controller.'new_driver_vehicle']);
	
	Route::get('/not_assigned_driver_vehicle',			['as' 	 => $module_slug.'not_assigned_driver_vehicle', 
								  				'uses' 	 => $module_controller.'not_assigned_driver_vehicle']);

	Route::post('/update_not_assigned_driver_vehicle_details',	['as' 	 => $module_slug.'update_not_assigned_driver_vehicle_details', 
								  				'uses' 	 => $module_controller.'update_not_assigned_driver_vehicle_details']);	
	
	Route::post('/update_driver_vehicle_details',	['as' 	 => $module_slug.'update_driver_vehicle_details', 
								  				'uses' 	 => $module_controller.'update_driver_vehicle_details']);	
	
	Route::post('/update_driver_previous_vehicle_details',	['as' 	 => $module_slug.'update_driver_previous_vehicle_details', 
								  				'uses' 	 => $module_controller.'update_driver_previous_vehicle_details']);	

	Route::post('/update_admin_driver_previous_vehicle_details',	['as' 	 => $module_slug.'update_admin_driver_previous_vehicle_details', 
								  				'uses' 	 => $module_controller.'update_admin_driver_previous_vehicle_details']);	

	Route::get('/logout',			['as' 	 => $module_slug.'logout', 
								  				'uses' 	 => $module_controller.'logout']);


	Route::group(array('middleware'=>'front_auth_user'), function (){

		Route::group(array('prefix' => '/user'), function()
		{
			$module_controller = 'Front\UserController@';
			$module_slug       = 'process_';

			Route::get('/dashboard',		['as' 	 => $module_slug.'dashboard', 
									  	'uses' 	 => $module_controller.'index']);

			Route::get('/my_profile_edit',	['as' 	 => $module_slug.'my_profile_edit', 
									  	'uses' 	 => $module_controller.'my_profile_edit']);

			Route::post('/update_profile',	['as' 	 => $module_slug.'update_profile', 
									  	'uses' 	 => $module_controller.'update_profile']);

			Route::get('/my_profile_view',	['as' 	 => $module_slug.'my_profile_view', 
									  	'uses' 	 => $module_controller.'my_profile_view']);

			Route::post('/verify_mobile_number',		['as' 	 => $module_slug.'verify_mobile_number', 
									 				'uses' 	 => $module_controller.'verify_mobile_number']);
		
			Route::post('/update_mobile_no',			['as' 	 => $module_slug.'update_mobile_no', 
									 				'uses' 	 => $module_controller.'update_mobile_no']);
			
			Route::get('/my_booking',		['as' 	 => $module_slug.'my_booking', 
									  	'uses' 	 => $module_controller.'my_booking']);

			Route::get('/booking_details',		['as' 	 => $module_slug.'booking_details', 
									  	'uses' 	 => $module_controller.'booking_details']);
			
			Route::get('/payment',			['as' 	 => $module_slug.'payment', 
									  	'uses' 	 => $module_controller.'payment']);

			Route::get('/payment/add_card',		['as' 	 => $module_slug.'add_card', 
									  			'uses' 	 => $module_controller.'add_card']);

			Route::post('/payment/store',	['as' 	 => $module_slug.'store', 
									  			'uses' 	 => $module_controller.'store']);

			Route::get('/payment/delete_card',	['as' 	 => $module_slug.'delete_card', 
									  				'uses' 	 => $module_controller.'delete_card']);

			Route::get('/change_password',	['as' 	 => $module_slug.'change_password', 
									  			'uses' 	 => $module_controller.'change_password']);

			Route::post('/update_password',	['as' 	 => $module_slug.'update_password', 
									  			'uses' 	 => $module_controller.'update_password']);

			Route::get('/notification',		['as' 	 => $module_slug.'notification', 
									  		'uses' 	 => $module_controller.'notification']);

			Route::get('/review_rating',	['as' 	 => $module_slug.'review_rating', 
									  			'uses' 	 => $module_controller.'review_rating']);

			Route::get('/messages',	['as' 	 => $module_slug.'messages', 
									  			'uses' 	 => $module_controller.'messages']);

			Route::post('/store_chat',	['as' 	 => $module_slug.'store_chat', 
									  			'uses' 	 => $module_controller.'store_chat']);

			Route::get('/get_current_chat_messages',	['as' 	 => $module_slug.'get_current_chat_messages', 
									  			'uses' 	 => $module_controller.'get_current_chat_messages']);
			
			Route::get('/delivery_request',		['as' 	 => $module_slug.'delivery_request', 
									  	'uses' 	 => $module_controller.'delivery_request']);

			Route::get('/track_driver',		['as' 	 => $module_slug.'track_driver', 
									  	'uses' 	 => $module_controller.'track_driver']);

			Route::get('/track_trip',		['as' 	 => $module_slug.'track_trip', 
									  	'uses' 	 => $module_controller.'track_trip']);
			
			Route::get('/track_live_trip',		['as' 	 => $module_slug.'track_live_trip', 
									  	'uses' 	 => $module_controller.'track_live_trip']);
			
			Route::post('/process_cancel_trip',		['as' 	 => $module_slug.'process_cancel_trip', 
									  	'uses' 	 => $module_controller.'process_cancel_trip']);			

			Route::get('/pending_load_post',		['as' 	 => $module_slug.'pending_load_post', 
									  	'uses' 	 => $module_controller.'pending_load_post']);

			Route::post('/accept_load_post',		['as' 	 => $module_slug.'accept_load_post', 
									  	'uses' 	 => $module_controller.'accept_load_post']);

			Route::get('/reject_load_post',		['as' 	 => $module_slug.'reject_load_post', 
									  	'uses' 	 => $module_controller.'reject_load_post']);
			
			Route::get('/cancel_pending_load_post',		['as' 	 => $module_slug.'cancel_pending_load_post', 
									  	'uses' 	 => $module_controller.'cancel_pending_load_post']);

			Route::get('/download_invoice',		['as' 	 => $module_slug.'download_invoice', 
									  	'uses' 	 => $module_controller.'download_invoice']);

			Route::post('/store_load_post_request',		['as' 	 => $module_slug.'store_load_post_request', 
									  				'uses' 	 => $module_controller.'store_load_post_request']);

			Route::get('/book_driver_request',		['as' 	 => $module_slug.'book_driver_request', 
									  	'uses' 	 => $module_controller.'book_driver_request']);

			Route::post('/process_to_book_driver',		['as' 	 => $module_slug.'process_to_book_driver', 
									  	'uses' 	 => $module_controller.'process_to_book_driver']);
		});

	
		Route::group(array('prefix' => '/driver'), function()
		{
			$module_controller = 'Front\DriverController@';
			$module_slug       = 'driver_';

			Route::get('/dashboard',	['as' 	 => $module_slug.'dashboard', 
									  	'uses' 	 => $module_controller.'index']);

			Route::get('/my_profile_edit',	['as' 	 => $module_slug.'my_profile_edit', 
									  	'uses' 	 => $module_controller.'my_profile_edit']);

			Route::post('/update_profile',	['as' 	 => $module_slug.'update_profile', 
									  	'uses' 	 => $module_controller.'update_profile']);

			Route::get('/my_profile_view',	['as' 	 => $module_slug.'my_profile_view', 
									  	'uses' 	 => $module_controller.'my_profile_view']);

			Route::post('/verify_mobile_number',		['as' 	 => $module_slug.'verify_mobile_number', 
									 				'uses' 	 => $module_controller.'verify_mobile_number']);
		
			Route::post('/update_mobile_no',			['as' 	 => $module_slug.'update_mobile_no', 
									 				'uses' 	 => $module_controller.'update_mobile_no']);

			Route::get('/my_job',			['as' 	 => $module_slug.'my_job', 
									  	'uses' 	 => $module_controller.'my_job']);

			Route::get('/request_list',			['as' 	 => $module_slug.'request_list', 
									  	'uses' 	 => $module_controller.'request_list']);

			Route::get('/job_details',			['as' 	 => $module_slug.'job_details', 
									  	'uses' 	 => $module_controller.'job_details']);

			Route::get('/vehicle',			['as' 	 => $module_slug.'vehicle', 
									  	'uses' 	 => $module_controller.'vehicle']);

			Route::get('/vehicle_edit',		['as' 	 => $module_slug.'vehicle_edit', 
									  	'uses' 	 => $module_controller.'vehicle_edit']);

			Route::post('/vehicle_update',		['as' 	 => $module_slug.'vehicle_update', 
									  	'uses' 	 => $module_controller.'vehicle_update']);

			Route::get('/my_earning',		['as' 	 => $module_slug.'my_earning', 
									  	'uses' 	 => $module_controller.'my_earning']);

			Route::get('/change_password',	['as' 	 => $module_slug.'change_password', 
									  			'uses' 	 => $module_controller.'change_password']);

			Route::post('/update_password',	['as' 	 => $module_slug.'update_password', 
									  			'uses' 	 => $module_controller.'update_password']);

			Route::get('/notification',		['as' 	 => $module_slug.'notification', 
									  		'uses' 	 => $module_controller.'notification']);

			Route::get('/review_rating',		['as' 	 => $module_slug.'review_rating', 
									  			'uses' 	 => $module_controller.'review_rating']);

			Route::get('/messages',	['as' 	 => $module_slug.'messages', 
									  			'uses' 	 => $module_controller.'messages']);

			Route::post('/store_chat',	['as' 	 => $module_slug.'store_chat', 
									  			'uses' 	 => $module_controller.'store_chat']);

			Route::get('/get_current_chat_messages',	['as' 	 => $module_slug.'get_current_chat_messages', 
									  			'uses' 	 => $module_controller.'get_current_chat_messages']);
			
			Route::get('/redirect_from_stripe',	['as' 	 => $module_slug.'redirect_from_stripe', 
									  			'uses' 	 => $module_controller.'redirect_from_stripe']);
			
			Route::get('/load_post_details',		['as' 	 => $module_slug.'load_post_details', 
									  	'uses' 	 => $module_controller.'load_post_details']);
			

			Route::get('/track_trip',		['as' 	 => $module_slug.'track_trip', 
									  	'uses' 	 => $module_controller.'track_trip']);
			
			Route::get('/track_live_trip',		['as' 	 => $module_slug.'track_live_trip', 
									  	'uses' 	 => $module_controller.'track_live_trip']);
			
			Route::post('/process_cancel_trip',		['as' 	 => $module_slug.'process_cancel_trip', 
									  	'uses' 	 => $module_controller.'process_cancel_trip']);			

			Route::get('/accept_pending_load_post',		['as' 	 => $module_slug.'accept_pending_load_post', 
									  	'uses' 	 => $module_controller.'accept_pending_load_post']);
			
			Route::get('/cancel_pending_load_post',		['as' 	 => $module_slug.'cancel_pending_load_post', 
									  	'uses' 	 => $module_controller.'cancel_pending_load_post']);
			
			Route::get('/download_invoice',		['as' 	 => $module_slug.'download_invoice', 
									  	'uses' 	 => $module_controller.'download_invoice']);

			// Route::post('/update_lat_lng',			['as' 	=> $module_slug.'update_lat_lng', 
			// 		                                'uses' 	=> $module_controller.'update_lat_lng']);

			// Route::post('/update_availability_status',['as' 	=> $module_slug.'update_availability_status', 
			// 		                                'uses' 	=> $module_controller.'update_availability_status']);		

			// Route::get('/get_driver_availability_status',['as' 	=> $module_slug.'get_driver_availability_status', 
			// 		                                'uses' 	=> $module_controller.'get_driver_availability_status']);		

			// Route::get('/get_vehicle_details',			['as' 	=> $module_slug.'get_vehicle_details', 
			// 		                                'uses' 	=> $module_controller.'get_vehicle_details']);		

			// Route::post('/update_vehicle_details',			['as' 	=> $module_slug.'update_vehicle_details', 
			// 		                                'uses' 	=> $module_controller.'update_vehicle_details']);	

			// Route::get('/get_driver_fair_charge',	['as' 	=> $module_slug.'get_driver_fair_charge',
			// 										'uses' 	=> $module_controller.'get_driver_fair_charge']);

			// Route::post('/send_driver_fair_charge',	['as' 	=> $module_slug.'send_driver_fair_charge',
			// 										'uses' 	=> $module_controller.'send_driver_fair_charge']);

			// Route::get('/get_driver_deposit_money',	['as' 	=> $module_slug.'get_driver_deposit_money',
			// 										'uses' 	=> $module_controller.'get_driver_deposit_money']);

			// Route::post('/process_deposit_money_request',	['as' 	=> $module_slug.'process_deposit_money_request',
			// 										'uses' 	=> $module_controller.'process_deposit_money_request']);

			// Route::post('/store_driver_deposit',	['as' 	=> $module_slug.'store_driver_deposit',
			// 										'uses' 	=> $module_controller.'store_driver_deposit']);

			// Route::get('/get_earning',				['as' 	=> $module_slug.'get_earning',
			// 										'uses' 	=> $module_controller.'get_earning']);

			// Route::get('/get_total_earning',		['as' 	=> $module_slug.'get_total_earning',
			// 										'uses' 	=> $module_controller.'get_total_earning']);

			// Route::post('/change_ride_status',		['as' 	=> $module_slug.'change_ride_status',
			// 										'uses' 	=> $module_controller.'change_ride_status']);
				
			// Route::get('/get_driver_details',       ['as' 	=> $module_slug.'get_driver_details', 
			// 				                        'uses'  => $module_controller.'get_driver_details']);

			// Route::post('/change_availability_status',['as' => $module_slug.'change_availability_status', 
			// 			                              'uses' => $module_controller.'change_availability_status']);

			// Route::post('/get_driver_payment_history',	['as' 	=> $module_slug.'get_driver_payment_history', 
			// 		                                'uses' 	=> $module_controller.'get_driver_payment_history']);

			// Route::post('/payment_status',			['as' 	=> $module_slug.'payment_status', 
			// 		                                'uses' 	=> $module_controller.'payment_status']);
		});
		
		Route::group(array('prefix' => '/enterprise_admin'), function()
		{
			$module_controller = 'Front\EnterpriseAdminController@';
			$module_slug       = 'enterprise_admin_';

			Route::get('/dashboard',		[	'as' 	 => $module_slug.'dashboard', 
									  			'uses' 	 => $module_controller.'index']);

			Route::get('/my_profile_edit',	[	'as' 	 => $module_slug.'my_profile_edit', 
									  			'uses' 	 => $module_controller.'my_profile_edit']);

			Route::post('/update_profile',	[	'as' 	 => $module_slug.'update_profile', 
									  			'uses' 	 => $module_controller.'update_profile']);

			Route::get('/my_profile_view',	[	'as' 	 => $module_slug.'my_profile_view', 
									  			'uses' 	 => $module_controller.'my_profile_view']);

			Route::post('/verify_mobile_number',[   'as' 	 => $module_slug.'verify_mobile_number', 
									 			'uses' 	 => $module_controller.'verify_mobile_number']);
		
			Route::post('/update_mobile_no',[	'as' 	 => $module_slug.'update_mobile_no', 
									 			'uses' 	 => $module_controller.'update_mobile_no']);

			Route::get('/change_password',	[	'as' 	 => $module_slug.'change_password', 
									  			'uses' 	 => $module_controller.'change_password']);

			Route::post('/update_password',	[	'as' 	 => $module_slug.'update_password', 
									  			'uses' 	 => $module_controller.'update_password']);

			Route::get('/manage_users',		[	'as' 	 => $module_slug.'manage_users', 
									  			'uses' 	 => $module_controller.'manage_users']);

			Route::get('/add_users',		[	'as' 	 => $module_slug.'add_users', 
									  			'uses' 	 => $module_controller.'add_users']);

			Route::post('/store_enterprise_user',[	'as' 	 => $module_slug.'store_enterprise_user', 
									  			'uses' 	 => $module_controller.'store_enterprise_user']);
			
			Route::get('/edit_user',		[	'as' 	 => $module_slug.'edit_user', 
									  			'uses' 	 => $module_controller.'edit_user']);			
			
			Route::post('/update_enterprise_user',[	'as' 	 => $module_slug.'update_enterprise_user', 
									  			'uses' 	 => $module_controller.'update_enterprise_user']);
			
			Route::get('/change_status',		[	'as' 	 => $module_slug.'change_status', 
									  			'uses' 	 => $module_controller.'change_status']);			


			/*Route::get('/notification',		['as' 	 => $module_slug.'notification', 
									  		'uses' 	 => $module_controller.'notification']);

			Route::get('/my_job',			['as' 	 => $module_slug.'my_job', 
									  	'uses' 	 => $module_controller.'my_job']);

			Route::get('/request_list',			['as' 	 => $module_slug.'request_list', 
									  	'uses' 	 => $module_controller.'request_list']);

			Route::get('/job_details',			['as' 	 => $module_slug.'job_details', 
									  	'uses' 	 => $module_controller.'job_details']);

			Route::get('/vehicle',			['as' 	 => $module_slug.'vehicle', 
									  	'uses' 	 => $module_controller.'vehicle']);

			Route::get('/vehicle_edit',		['as' 	 => $module_slug.'vehicle_edit', 
									  	'uses' 	 => $module_controller.'vehicle_edit']);

			Route::post('/vehicle_update',		['as' 	 => $module_slug.'vehicle_update', 
									  	'uses' 	 => $module_controller.'vehicle_update']);

			Route::get('/my_earning',		['as' 	 => $module_slug.'my_earning', 
									  	'uses' 	 => $module_controller.'my_earning']);

			Route::get('/change_password',	['as' 	 => $module_slug.'change_password', 
									  			'uses' 	 => $module_controller.'change_password']);

			Route::post('/update_password',	['as' 	 => $module_slug.'update_password', 
									  			'uses' 	 => $module_controller.'update_password']);

			Route::get('/notification',		['as' 	 => $module_slug.'notification', 
									  		'uses' 	 => $module_controller.'notification']);

			Route::get('/review_rating',		['as' 	 => $module_slug.'review_rating', 
									  			'uses' 	 => $module_controller.'review_rating']);

			Route::get('/messages',	['as' 	 => $module_slug.'messages', 
									  			'uses' 	 => $module_controller.'messages']);

			Route::post('/store_chat',	['as' 	 => $module_slug.'store_chat', 
									  			'uses' 	 => $module_controller.'store_chat']);

			Route::get('/get_current_chat_messages',	['as' 	 => $module_slug.'get_current_chat_messages', 
									  			'uses' 	 => $module_controller.'get_current_chat_messages']);
			
			Route::get('/redirect_from_stripe',	['as' 	 => $module_slug.'redirect_from_stripe', 
									  			'uses' 	 => $module_controller.'redirect_from_stripe']);
			
			Route::get('/load_post_details',		['as' 	 => $module_slug.'load_post_details', 
									  	'uses' 	 => $module_controller.'load_post_details']);
			

			Route::get('/track_trip',		['as' 	 => $module_slug.'track_trip', 
									  	'uses' 	 => $module_controller.'track_trip']);
			
			Route::get('/track_live_trip',		['as' 	 => $module_slug.'track_live_trip', 
									  	'uses' 	 => $module_controller.'track_live_trip']);
			
			Route::post('/process_cancel_trip',		['as' 	 => $module_slug.'process_cancel_trip', 
									  	'uses' 	 => $module_controller.'process_cancel_trip']);			

			Route::get('/accept_pending_load_post',		['as' 	 => $module_slug.'accept_pending_load_post', 
									  	'uses' 	 => $module_controller.'accept_pending_load_post']);
			
			Route::get('/cancel_pending_load_post',		['as' 	 => $module_slug.'cancel_pending_load_post', 
									  	'uses' 	 => $module_controller.'cancel_pending_load_post']);
			
			Route::get('/download_invoice',		['as' 	 => $module_slug.'download_invoice', 
									  	'uses' 	 => $module_controller.'download_invoice']);*/

		});

	});
	
	Route::group(array('prefix' => '/ride'), function()
	{
		$module_controller = 'Front\RideController@';
		$module_slug       = 'user_';

		Route::post('/get_fair_estimate',		['as' 	 => $module_slug.'get_fair_estimate', 
								  				'uses' 	 => $module_controller.'get_fair_estimate']);
	});
	
});

/*---------------------------------------------------------------------------------------
	End
-----------------------------------------------------------------------------------------*/


/*admin_panel_slug = admin*/
$admin_path = config('app.project.admin_panel_slug');
$company_path = config('app.project.company_panel_slug');

Route::group(['middleware' => ['web']], function ()  use($admin_path, $company_path) 
{
	/* Admin Routes */

	Route::group(array('prefix' => '/common'), function()
	{
		Route::get('/get_subcategories', 			[ 'as'=>'', 'uses'=>'Common\CommonDataController@get_subcategories']);			
		Route::get('/get_states', 	     			[ 'as'=>'', 'uses'=>'Common\LocationController@get_states']);			
		Route::get('/get_cities',        			[ 'as'=>'', 'uses'=>'Common\LocationController@get_cities']);			
		Route::get('/get_new_notification',   		[ 'as'=>'', 'uses'=>'Common\CommonDataController@get_new_notification']);			
		Route::get('/change_notification_status',   [ 'as'=>'', 'uses'=>'Common\CommonDataController@change_notification_status']);		
		Route::post('/send_notification',   [ 'as'=>'', 'uses'=>'Common\CommonDataController@send_notification']);		
		Route::get('/download_document',   [ 'as'=>'', 'uses'=>'Common\CommonDataController@download_document']);		
		Route::get('/send_invoice_email',   [ 'as'=>'', 'uses'=>'Common\CommonDataController@send_invoice_email']);		

	});

	Route::group(['prefix' => $admin_path,'middleware'=>['admin']], function () 
	{

		$module_permission = "module_permission:";

		$route_slug        = "admin_auth_";
		$module_controller = "Admin\AuthController@";
		
	   /*----------------------------------------------------------------------------------------
			Admin Home Route  
		----------------------------------------------------------------------------------------*/

		Route::any('/',              													[	'as'	=> $route_slug.'login', 
																							'uses'	=> $module_controller.'login']);	
		
		Route::get('login',          													[	'as'	=> $route_slug.'login',         
																							'uses'	=> $module_controller.'login']);	
		
		Route::post('process_login',  													[	'as'	=> $route_slug.'process_login',
																							'uses'	=> $module_controller.'process_login']);	
		
		Route::get('change_password', 													[	'as'	=> $route_slug.'change_password',
																							'uses'	=> $module_controller.'change_password']);	
		
		Route::get('edit_profile', 													    [	'as'	=> $route_slug.'edit_profile',
																							'uses'	=> $module_controller.'edit_profile']);	

		Route::post('update_password',													[	'as'	=> $route_slug.'change_password' ,
																							'uses'	=> $module_controller.'update_password']);	
		
		Route::post('process_forgot_password',											[	'as'	=> $route_slug.'forgot_password',
																							'uses'	=> $module_controller.'process_forgot_password']);
		
		Route::get('validate_admin_reset_password_link/{enc_id}/{enc_reminder_code}', 	[	'as'	=> $route_slug.'validate_admin_reset_password_link',
																							'uses'	=> $module_controller.'validate_reset_password_link']);
		
		Route::post('reset_password',													[	'as'	=> $route_slug.'reset_passsword',
																							'uses'	=> $module_controller.'reset_password']);


		Route::get('/get_users/{user_type}',						[	'as'		=> $route_slug.'get_users',
															'uses'		=>'Admin\DashboardController@get_users']);	

		
		/*----------------------------------------------------------------------------------------
			Dashboard  
		----------------------------------------------------------------------------------------*/

		Route::get('/dashboard',						[	'as'		=> $route_slug.'dashboard',
															'uses'		=>'Admin\DashboardController@index']);	
 
		Route::get('/logout',   						[	'as'		=> $route_slug.'logout',
															'uses'		=> $module_controller.'logout']);	



		/*----------------------------------------------------------------------------------------
			Admin Profile  
		----------------------------------------------------------------------------------------*/

		$account_setting_controller = "Admin\ProfileController@";
		$account_settings_slug = "profile";
	

		Route::get('profile', 													       [	'as'	=> $account_settings_slug.'profile',
																							'uses'	=> $account_setting_controller.'index']);	

		Route::post('/profile/update', 													[	'as'	=> $account_settings_slug.'update_profile',
																							'uses'	=> $account_setting_controller.'update']);	

		
		Route::group(['prefix'=>'messages'],function() use ($module_permission)
		{
			$route_slug       = "messages";
			$module_controller = "Admin\MessagesController@";
			$module_slug       = "messages";

			Route::get('/',                        [  		'as'		=> $route_slug.'index',
															'uses'		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);	

			Route::post('/store_chat',               [  		'as'		=> $route_slug.'index',
															'uses'		=> $module_controller.'store_chat',
															'middleware'=> $module_permission.$module_slug.'.list']);	

			Route::get('/get_current_chat_messages',   [  		'as'		=> $route_slug.'index',
															'uses'		=> $module_controller.'get_current_chat_messages',
															'middleware'=> $module_permission.$module_slug.'.list']);	

		});
		/*----------------------------------------------------------------------------------------
			Restricted Area 
		----------------------------------------------------------------------------------------*/

		Route::group(['prefix'=>'assigned_area'],function() use ($module_permission)
		{
			$route_slug       = "admin_as_signedarea";
			$module_controller = "Admin\AssignedAreaController@";
			$module_slug       = "assigned_area";

			Route::get('/',                        [  		'as'		=> $route_slug.'index',
															'uses'		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);	

			Route::post('store',                   [		'as'		=> $route_slug.'store',
															'uses'		=> $module_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.store']);	

			Route::get('/fetch_stored_zone/{enc_id}', [		'as'		=> $route_slug.'subcategory_index',
															'uses'		=> $module_controller.'fetch_stored_zone'
															]);		

			Route::get('create/{enc_id?}',         [		'as'		=> $route_slug.'create',
															'uses'		=> $module_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);	

			

			Route::get('edit/{enc_id}',            [		'as'		=> $route_slug.'edit',
															'uses'		=> $module_controller.'edit',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::post('update',                  [		'as'		=> $route_slug.'update',
										         	 		'uses'		=> $module_controller.'update',
										         	 		'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('delete/{enc_id}',          [		'as'		=> $route_slug.'delete',
															'uses'		=> $module_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.delete']);	

			Route::get('delete_area/{enc_id}',          		[		'as'		=> $route_slug.'delete_area',
															'uses'		=> $module_controller.'delete_area',
															'middleware'=> $module_permission.$module_slug.'.delete_area']);	


			Route::get('activate/{enc_id}',        [		'as'		=> $route_slug.'activate',
															'uses'		=> $module_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('deactivate/{enc_id}',      [		'as'		=> $route_slug.'deactivate',
															'uses'		=> $module_controller.'deactivate',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action',            [		'as'		=> $route_slug.'multi_action',
															'uses'		=> $module_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);	
			
			Route::get('/view_map/{enc_id}',             	[		'as' 		=> $route_slug.'view_map',
															'uses' 		=> $module_controller.'view_map',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/create_existing',             	[		'as' 		=> $route_slug.'create_existing',
															'uses' 		=> $module_controller.'create_existing',
															'middleware'=> $module_permission.$module_slug.'.list']);

		});

		Route::group(['prefix'=>'restricted_area'],function() use ($module_permission)
		{
			$route_slug       = "admin_restricted_area";
			$module_controller = "Admin\RestrictedAreaController@";
			$module_slug       = "restricted_area";

			Route::get('/',                        [  		'as'		=> $route_slug.'index',
															'uses'		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);	

			Route::post('store',                   [		'as'		=> $route_slug.'store',
															'uses'		=> $module_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.store']);	

			Route::get('/fetch_stored_zone/{enc_id}', [		'as'		=> $route_slug.'subcategory_index',
															'uses'		=> $module_controller.'fetch_stored_zone'
															]);		

			Route::get('create/{enc_id?}',         [		'as'		=> $route_slug.'create',
															'uses'		=> $module_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);	

			

			Route::get('edit/{enc_id}',            [		'as'		=> $route_slug.'edit',
															'uses'		=> $module_controller.'edit',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::post('update',                  [		'as'		=> $route_slug.'update',
										         	 		'uses'		=> $module_controller.'update',
										         	 		'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('delete/{enc_id}',          [		'as'		=> $route_slug.'delete',
															'uses'		=> $module_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.delete']);	

			Route::get('delete_area/{enc_id}',          		[		'as'		=> $route_slug.'delete_area',
															'uses'		=> $module_controller.'delete_area',
															'middleware'=> $module_permission.$module_slug.'.delete_area']);	


			Route::get('activate/{enc_id}',        [		'as'		=> $route_slug.'activate',
															'uses'		=> $module_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('deactivate/{enc_id}',      [		'as'		=> $route_slug.'deactivate',
															'uses'		=> $module_controller.'deactivate',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action',            [		'as'		=> $route_slug.'multi_action',
															'uses'		=> $module_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);	
			
			Route::get('/view_map/{enc_id}',             	[		'as' 		=> $route_slug.'view_map',
															'uses' 		=> $module_controller.'view_map',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/create_existing',             	[		'as' 		=> $route_slug.'create_existing',
															'uses' 		=> $module_controller.'create_existing',
															'middleware'=> $module_permission.$module_slug.'.list']);

		});

		/*----------------------------------------------------------------------------------------
			Restricted Area  (as per changes restricted area is renamed to assigned area)
		----------------------------------------------------------------------------------------*/

		Route::group(['prefix'=>'booking_summary'],function() use ($module_permission)
		{
			$route_slug       = "booking_summary";
			$module_controller = "Admin\BookingSummaryController@";
			$module_slug       = "booking_summary";

			Route::get('/',                        [  		'as'		=> $route_slug.'index',
															'uses'		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);	

			Route::get('/get_records',				[		'as' 		=> $route_slug.'get_records',
													   		'uses'		=> $module_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);	

			Route::get('/view/{enc_id}',			[		'as' 		=> $route_slug.'view',
													   		'uses'		=> $module_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.view']);

			Route::get('/generate_excel',			[		'as' 		=> $route_slug.'generate_excel',
													   		'uses'		=> $module_controller.'generate_excel',
															'middleware'=> $module_permission.$module_slug.'.generate_excel']);
		});

		Route::group(['prefix'=>'assistant'],function() use ($module_permission)
		{
			$route_slug       = "assistant";
			$module_controller = "Admin\AssistantController@";
			$module_slug       = "assistant";

			Route::get('/',                        [  		'as'		=> $route_slug.'index',
															'uses'		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);	

			Route::get('/get_records',				[		'as' 		=> $route_slug.'get_records',
													   		'uses'		=> $module_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);	

			Route::get('/view/{enc_id}',			[		'as' 		=> $route_slug.'view',
													   		'uses'		=> $module_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.view']);

			Route::get('/search_nearby_drivers',	[		'as' 		=> $route_slug.'search_nearby_drivers',
													   		'uses'		=> $module_controller.'search_nearby_drivers',
															'middleware'=> $module_permission.$module_slug.'.search_nearby_drivers']);

			Route::get('/assign_driver',			[		'as' 		=> $route_slug.'assign_driver',
													   		'uses'		=> $module_controller.'assign_driver',
															'middleware'=> $module_permission.$module_slug.'.assign_driver']);
		});
	   /*----------------------------------------------------------------------------------------
			Admin Report
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix'=>'/report'), function () use ($module_permission)
		{
			$route_slug       = "admin_report_";
			$module_slug       = "report";
			$route_controller = "Admin\ReportController@";

			Route::get('/',					  			[	'as' 		=> $route_slug.'index',
													   		'uses'		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/get_user_records',				[		'as' 		=> $route_slug.'get_user_records',
													   		'uses'		=> $route_controller.'get_user_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/get_driver_records',			[	'as' 		=> $route_slug.'get_driver_records',
													   		'uses'		=> $route_controller.'get_driver_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/get_booking_records',			[	'as' 		=> $route_slug.'get_booking_records',
													   		'uses'		=> $route_controller.'get_booking_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

			
			Route::get('/generate_user_excel',			[	'as' 		=> $route_slug.'generate_user_excel',
													   		'uses'		=> $route_controller.'generate_user_excel',
															'middleware'=> $module_permission.$module_slug.'.list']);


			Route::get('/generate_driver_excel',		[	'as' 		=> $route_slug.'generate_driver_excel',
													   		'uses'		=> $route_controller.'generate_driver_excel',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/generate_excel',		[	'as' 		=> $route_slug.'generate_excel',
													   		'uses'		=> $route_controller.'generate_excel',
															'middleware'=> $module_permission.$module_slug.'.list']);
		});
		
		

		/*----------------------------------------------------------------------------------------
			Contact Enquiry 
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix'=>'/contact_enquiry'), function () use ($module_permission)
		{
			$route_slug       = "admin_contact_enquiry_";
			$module_slug       = "contact_enquiry";
			$route_controller = "Admin\ContactEnquiryController@";

			Route::get('/',					  		[		'as' 		=> $route_slug.'index',
													   		'uses'		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/view/{enc_id}',	   		[		'as' 		=> $route_slug.'details',
															'uses'		=> $route_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('delete/{enc_id}',	   		[		'as' 		=> $route_slug.'delete',
															'uses'		=> $route_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.delete']);

			Route::get('create_email/{enc_id}',	   	[		'as' 		=> $route_slug.'create_email',
															'uses'		=> $route_controller.'create_email',
															'middleware'=> $module_permission.$module_slug.'.create_email']);

			Route::post('multi_action',		   		[		'as'		=> $route_slug.'multi_action',
															'uses'		=> $route_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);	
			Route::post('send_email',	   			[		'as' 		=> $route_slug.'send_email',
															'uses'		=> $route_controller.'send_email',
															'middleware'=> $module_permission.$module_slug.'.send_email']);

		});

		/*----------------------------------------------------------------------------------------
			Need Delivery 
		----------------------------------------------------------------------------------------*/
		Route::group(array('prefix'=>'/need_delivery'), function () use ($module_permission)
		{
			$route_slug       = "admin_need_delivery_";
			$module_slug       = "need_delivery";
			$route_controller = "Admin\NeedDeliveryController@";

			Route::get('/',					  		[		'as' 		=> $route_slug.'index',
													   		'uses'		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/view/{enc_id}',	   		[		'as' 		=> $route_slug.'details',
															'uses'		=> $route_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('delete/{enc_id}',	   		[		'as' 		=> $route_slug.'delete',
															'uses'		=> $route_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.delete']);

			Route::get('create_email/{enc_id}',	   	[		'as' 		=> $route_slug.'create_email',
															'uses'		=> $route_controller.'create_email',
															'middleware'=> $module_permission.$module_slug.'.create_email']);

			Route::post('multi_action',		   		[		'as'		=> $route_slug.'multi_action',
															'uses'		=> $route_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);	
			Route::post('send_email',	   			[		'as' 		=> $route_slug.'send_email',
															'uses'		=> $route_controller.'send_email',
															'middleware'=> $module_permission.$module_slug.'.send_email']);
		});

		/*----------------------------------------------------------------------------------------
			subscriber 
		----------------------------------------------------------------------------------------*/
		Route::group(array('prefix'=>'/subscriber'), function () use ($module_permission)
		{
			$route_slug       = "admin_subscriber_";
			$module_slug       = "subscriber";
			$route_controller = "Admin\SubscriberController@";

			Route::get('/',	[	'as' 		=> $route_slug.'index',
								'uses'		=> $route_controller.'index',
								'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('activate/{enc_id}',			[		'as' 		=> $route_slug.'activate',
															'uses'		=> $route_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('deactivate/{enc_id}',		[		'as' 		=> $route_slug.'deactivate',
											  				'uses' 		=> $route_controller.'deactivate',
											  				'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action',				[		'as'		=> $route_slug.'multi_action',
															'uses' 		=> $route_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);
			
		});

		/*----------------------------------------------------------------------------------------
			Notifications
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix'=>'/notification'), function () use ($module_permission)
		{
			$route_slug       = "admin_notification_";
			$module_slug       = "notification";
			$route_controller = "Admin\NotificationsController@";

			Route::get('/',					  		[		'as' 		=> $route_slug.'index',
													   		'uses'		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);
			

		});

		/*----------------------------------------------------------------------------------------
			Track Booking 
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix'=>'/track_booking'), function () use ($module_permission)
		{
			$route_slug       = "admin_track_booking_";
			$module_slug       = "track_booking";
			$route_controller = "Admin\TrackBookingController@";

			Route::get('/',					  		[		'as' 		=> $route_slug.'index',
													   		'uses'		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/booking_history',			[		'as' 		=> $route_slug.'booking_history',
													   		'uses'		=> $route_controller.'booking_history',
															'middleware'=> $module_permission.$module_slug.'.list']);


			Route::get('/get_records',				[		'as' 		=> $route_slug.'get_records',
													   		'uses'		=> $route_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/view',			           [		'as' 		=> $route_slug.'view',
													   		'uses'		=> $route_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.view']);

			
			Route::get('/available_driver',			[		'as' 		=> $route_slug.'available_driver',
													   		'uses'		=> $route_controller.'available_driver',
															'middleware'=> $module_permission.$module_slug.'.list']);


			Route::get('/track_current_booking',			['as' 		=> $route_slug.'track_current_booking',
													   		'uses'		=> $route_controller.'track_current_booking',
															'middleware'=> $module_permission.$module_slug.'.list']);		
			
			Route::get('/assign_request_to_driver',			['as' 		=> $route_slug.'assign_request_to_driver',
													   		'uses'		=> $route_controller.'assign_request_to_driver',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::post('/cancel_request',					[	'as'		=> $route_slug.'cancel_request',
																'uses'		=>$route_controller.'cancel_request',
															'middleware'=> $module_permission.$module_slug.'.list']);
			
			Route::get('/cancel_trip_request/{enc_id}',		[	'as'		=> $route_slug.'cancel_trip_request',
																'uses'		=>$route_controller.'cancel_trip_request',
															'middleware'=> $module_permission.$module_slug.'.list']);

		});


		/*----------------------------------------------------------------------------------------
			Request List
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix'=>'/request_list'), function () use ($module_permission)
		{

			$route_slug       = "admin_track_booking_";
			$module_slug       = "track_booking";
			$route_controller = "Admin\TrackBookingController@";

			Route::get('/',									[	'as'		=> $route_slug.'request_list',
															'uses'		=>$route_controller.'request_list',
															'middleware'=> $module_permission.$module_slug.'.list']);
			


			Route::get('/get_records_request_list',			[	'as'		=> $route_slug.'get_records',
																'uses'		=>$route_controller.'get_records_request_list',
															'middleware'=> $module_permission.$module_slug.'.list']);	


			Route::get('/booking_info',						[	'as'		=> $route_slug.'booking_info',
																'uses'		=>$route_controller.'booking_info',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::post('/cancel_request',					[	'as'		=> $route_slug.'cancel_request',
																'uses'		=>$route_controller.'cancel_request',
															'middleware'=> $module_permission.$module_slug.'.list']);


			Route::post('multi_action',      				[	'as'		=> $route_slug.'multi_action',
																'uses'		=> $route_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.list']);	
			
			 
		});

		/*----------------------------------------------------------------------------------------
			Request List
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix'=>'/future_booking'), function () use ($module_permission)
		{

			$route_slug       = "admin_track_booking_";
			$module_slug       = "track_booking";
			$route_controller = "Admin\TrackBookingController@";

			Route::get('/',									[	'as'		=> $route_slug.'future_booking',
															'uses'		=>$route_controller.'future_booking',
															'middleware'=> $module_permission.$module_slug.'.list']);
			


			Route::get('/get_records_future_list',			[	'as'		=> $route_slug.'get_records_future_list',
																'uses'		=>$route_controller.'get_records_future_list',
															'middleware'=> $module_permission.$module_slug.'.list']);	


			Route::get('/future_booking_info',				[	'as'		=> $route_slug.'booking_info',
																'uses'		=>$route_controller.'booking_info',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::post('/cancel_request',					[	'as'		=> $route_slug.'cancel_request',
																'uses'		=>$route_controller.'cancel_request',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::post('multi_action',      				[	'as'		=> $route_slug.'multi_action',
																'uses'		=> $route_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.list']);	

 		});


		/*----------------------------------------------------------------------------------------
			Faq 
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/faq'), function() use ($module_permission)
		{
			$route_slug       = 'admin_faq_';
			$route_controller = 'Admin\FAQController@';
			$module_slug       = "faq";

			Route::get('/',							[		'as'		=> $route_slug.'index', 
															'uses' 		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);
			
			Route::get('/create',					[		'as' 		=> $route_slug.'create', 
								  							'uses' 		=> $route_controller.'create',
								  							'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('/store',					[		'as' 		=> $route_slug.'store', 
								  							'uses' 		=> $route_controller.'store',
								  							'middleware'=> $module_permission.$module_slug.'.create']);

			Route::get('/edit/{enc_id}',			[		'as' 		=> $route_slug.'edit', 
															'uses'		=> $route_controller.'edit',
										 					'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('/update/{enc_id}',			[		'as' 		=> $route_slug.'update', 
										 					'uses' 		=> $route_controller.'update',
										 					'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('/delete/{enc_id}',			[		'as' 		=> $route_slug.'edit', 
										   					'uses' 		=> $route_controller.'delete',
										   					'middleware'=> $module_permission.$module_slug.'.delete']);

			Route::get('activate/{enc_id}',			[		'as' 		=> $route_slug.'activate',
															'uses'		=> $route_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('deactivate/{enc_id}',		[		'as' 		=> $route_slug.'deactivate',
											  				'uses' 		=> $route_controller.'deactivate',
											  				'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action',				[		'as'		=> $route_slug.'multi_action',
															'uses' 		=> $route_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);	 		
		});

		/*----------------------------------------------------------------------------------------
			 User
		----------------------------------------------------------------------------------------*/
		
		Route::group(array('prefix' => '/users'), function() use ($module_permission)
		{
			$route_slug       = "users";
			$module_controller = "Admin\UsersController@";
			$module_slug       = "users";

			Route::get('/',							[		'as' 		=> $route_slug.'index',
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('create/',					[		'as'		=> $route_slug.'create',
															'uses' 		=> $module_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('store/',					[		'as' 		=> $route_slug.'store',
															'uses' 		=> $module_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::get('view/{enc_id}',				[		'as' 		=> $route_slug.'view',
															'uses' 		=> $module_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('edit/{enc_id}',				[		'as' 		=> $route_slug.'edit',
															'uses' 		=> $module_controller.'edit',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('update',					[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'update',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('activate/{enc_id}',			[		'as' 		=> $route_slug.'activate',
															'uses' 		=> $module_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('deactivate/{enc_id}',		[		'as'		=> $route_slug.'deactivate',
															'uses'		=> $module_controller.'deactivate',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action', 			[		'as' 		=> $route_slug.'multi_action',
															'uses' 		=> $module_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('delete/{enc_id}',			[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.delete']);
			
			Route::get('reset_password/{enc_id}',			[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'reset_password',
															'middleware'=> $module_permission.$module_slug.'.reset_password']);
			
			Route::post('process_reset_password',			['as'=> $route_slug.'process_reset_password',
															'uses' 		=> $module_controller.'process_reset_password',
															'middleware'=> $module_permission.$module_slug.'.process_reset_password']);

			Route::get('/get_records',				[		'as' 		=> $route_slug.'get_records',
															'uses' 		=> $module_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/get_promo_codes',				[		'as' 		=> $route_slug.'get_promo_codes',
															'uses' 		=> $module_controller.'get_promo_codes',
															'middleware'=> $module_permission.$module_slug.'.list']);			
			// Route::get('/review/{enc_id}', 					[		'as' 		=> $route_slug.'review',
			// 												'uses' 		=> 'Admin\NormalRiderReviewController@index',
			// 												'middleware'=> $module_permission.$module_slug.'.list']);

			// Route::get('/review/view/{enc_id}', 				[		'as' 		=> $route_slug.'review',
			// 												'uses' 		=> 'Admin\NormalRiderReviewController@view',
			// 												'middleware'=> $module_permission.$module_slug.'.list']);			
		});


		/*----------------------------------------------------------------------------------------
			Enterprise Admin 
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/enterprise_admin'), function() use ($module_permission)
		{
			$route_slug       = "enterprise_admin";
			$module_controller = "Admin\EnterpriseAdminController@";
			$module_slug       = "enterprise_admin";

			Route::get('/',							[		'as' 		=> $route_slug.'index',
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('create/',					[		'as'		=> $route_slug.'create',
															'uses' 		=> $module_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('store/',					[		'as' 		=> $route_slug.'store',
															'uses' 		=> $module_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::get('view/{enc_id}',				[		'as' 		=> $route_slug.'view',
															'uses' 		=> $module_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('edit/{enc_id}',				[		'as' 		=> $route_slug.'edit',
															'uses' 		=> $module_controller.'edit',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('update',					[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'update',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('activate/{enc_id}',			[		'as' 		=> $route_slug.'activate',
															'uses' 		=> $module_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('deactivate/{enc_id}',		[		'as'		=> $route_slug.'deactivate',
															'uses'		=> $module_controller.'deactivate',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action', 			[		'as' 		=> $route_slug.'multi_action',
															'uses' 		=> $module_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('delete/{enc_id}',			[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.delete']);
			
			Route::get('reset_password/{enc_id}',			[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'reset_password',
															'middleware'=> $module_permission.$module_slug.'.reset_password']);
			
			Route::post('process_reset_password',			['as'=> $route_slug.'process_reset_password',
															'uses' 		=> $module_controller.'process_reset_password',
															'middleware'=> $module_permission.$module_slug.'.process_reset_password']);

			Route::get('/get_records',				[		'as' 		=> $route_slug.'get_records',
															'uses' 		=> $module_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('deposit_receipt/{enc_id}',	[		'as' 		=> $route_slug.'deposit_receipt',
															'uses' 		=> $module_controller.'deposit_receipt',
															'middleware'=> $module_permission.$module_slug.'.deposit_receipt']);

			Route::any('make_payment',		       [		'as' 		=> $route_slug.'make_payment',
															'uses' 		=> $module_controller.'make_payment',
															'middleware'=> $module_permission.$module_slug.'.make_payment']);		

			Route::any('make_driver_payment',		['as' 		=> $route_slug.'make_driver_payment',
															'uses' 		=> $module_controller.'make_driver_payment',
															'middleware'=> $module_permission.$module_slug.'.make_driver_payment']);		
		});

		/*----------------------------------------------------------------------------------------
			Company 
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/company'), function() use ($module_permission)
		{
			$route_slug       = "company";
			$module_controller = "Admin\CompanyController@";
			$module_slug       = "company";

			Route::get('/',							[		'as' 		=> $route_slug.'index',
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('create/',					[		'as'		=> $route_slug.'create',
															'uses' 		=> $module_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('store/',					[		'as' 		=> $route_slug.'store',
															'uses' 		=> $module_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::get('view/{enc_id}',				[		'as' 		=> $route_slug.'view',
															'uses' 		=> $module_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('edit/{enc_id}',				[		'as' 		=> $route_slug.'edit',
															'uses' 		=> $module_controller.'edit',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('update',					[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'update',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('activate/{enc_id}',			[		'as' 		=> $route_slug.'activate',
															'uses' 		=> $module_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('deactivate/{enc_id}',		[		'as'		=> $route_slug.'deactivate',
															'uses'		=> $module_controller.'deactivate',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action', 			[		'as' 		=> $route_slug.'multi_action',
															'uses' 		=> $module_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('delete/{enc_id}',			[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.delete']);
			
			Route::get('reset_password/{enc_id}',			[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'reset_password',
															'middleware'=> $module_permission.$module_slug.'.reset_password']);
			
			Route::post('process_reset_password',			['as'=> $route_slug.'process_reset_password',
															'uses' 		=> $module_controller.'process_reset_password',
															'middleware'=> $module_permission.$module_slug.'.process_reset_password']);

			Route::get('/get_records',				[		'as' 		=> $route_slug.'get_records',
															'uses' 		=> $module_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('deposit_receipt/{enc_id}',	[		'as' 		=> $route_slug.'deposit_receipt',
															'uses' 		=> $module_controller.'deposit_receipt',
															'middleware'=> $module_permission.$module_slug.'.deposit_receipt']);

			Route::any('make_payment',		       [		'as' 		=> $route_slug.'make_payment',
															'uses' 		=> $module_controller.'make_payment',
															'middleware'=> $module_permission.$module_slug.'.make_payment']);		

			Route::any('make_driver_payment',		['as' 		=> $route_slug.'make_driver_payment',
															'uses' 		=> $module_controller.'make_driver_payment',
															'middleware'=> $module_permission.$module_slug.'.make_driver_payment']);		
		});

		/*----------------------------------------------------------------------------------------
			Driver 
		----------------------------------------------------------------------------------------*/
		
		Route::group(array('prefix' => '/driver'), function() use ($module_permission)
		{
			$route_slug        = "driver";
			$module_controller = "Admin\DriverController@";
			$module_slug       = "driver";

			Route::get('/',							[		'as' 		=> $route_slug.'index',
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('create/',					[		'as'		=> $route_slug.'create',
															'uses' 		=> $module_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('store/',					[		'as' 		=> $route_slug.'store',
															'uses' 		=> $module_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::get('view/{enc_id}',				[		'as' 		=> $route_slug.'view',
															'uses' 		=> $module_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('edit/{enc_id}',				[		'as' 		=> $route_slug.'edit',
															'uses' 		=> $module_controller.'edit',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('update',					[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'update',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('activate/{enc_id}',			[		'as' 		=> $route_slug.'activate',
															'uses' 		=> $module_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('deactivate/{enc_id}',		[		'as'		=> $route_slug.'deactivate',
															'uses'		=> $module_controller.'deactivate',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('approve/{enc_id}',			[		'as' 		=> $route_slug.'approve',
															'uses' 		=> $module_controller.'approve',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('unapprove/{enc_id}',		[		'as'		=> $route_slug.'unapprove',
															'uses'		=> $module_controller.'unapprove',
															'middleware'=> $module_permission.$module_slug.'.update']);


			Route::get('reset_password/{enc_id}',			[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'reset_password',
															'middleware'=> $module_permission.$module_slug.'.reset_password']);
			
			Route::post('process_reset_password',			['as'=> $route_slug.'process_reset_password',
															'uses' 		=> $module_controller.'process_reset_password',
															'middleware'=> $module_permission.$module_slug.'.process_reset_password']);
			

			Route::post('multi_action', 			[		'as' 		=> $route_slug.'multi_action',
															'uses' 		=> $module_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('delete/{enc_id}',			[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.delete']);
			
			Route::get('/get_records',				[		'as' 		=> $route_slug.'get_records',
															'uses' 		=> $module_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);


			Route::get('/review/{enc_id}', 			[		'as' 		=> $route_slug.'review',
															'uses' 		=> 'Admin\DriverReviewController@index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/review/view/{enc_id}', 	[		'as' 		=> $route_slug.'review',
															'uses' 		=> 'Admin\DriverReviewController@view',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('fair_charge_request/{enc_id}',	[	'as' 		=> $route_slug.'fair_charge_request',
															'uses' 		=> $module_controller.'fair_charge_request',
															'middleware'=> $module_permission.$module_slug.'.fair_charge_request']);

			Route::get('change_request_status/{enc_id}/{type}', [		'as' 		=> $route_slug.'change_request_status',
																	    'uses' 		=> $module_controller.'change_request_status',
																	    'middleware'=> $module_permission.$module_slug.'.change_request_status']);
															
			Route::get('deposit_receipt/{enc_id}',				[		'as' 		=> $route_slug.'deposit_receipt',
																		'uses' 		=> $module_controller.'deposit_receipt',
																		'middleware'=> $module_permission.$module_slug.'.deposit_receipt']);


			Route::any('make_payment',				           [		'as' 		=> $route_slug.'make_payment',
																		'uses' 		=> $module_controller.'make_payment',
																		'middleware'=> $module_permission.$module_slug.'.make_payment']);

			Route::get('driver_receipt_details/{enc_id}',		[		'as' 		=> $route_slug.'driver_receipt_details',
																		'uses' 		=> $module_controller.'driver_receipt_details',
																		'middleware'=> $module_permission.$module_slug.'.driver_receipt_details']);

			Route::get('map',									[		'as' 		=> $route_slug.'map',
																		'uses' 		=> $module_controller.'map',
																		'middleware'=> $module_permission.$module_slug.'.map']);

			Route::get('driver_earning/{enc_id}',				[		'as' 		=> $route_slug.'driver_earning',
																		'uses' 		=> $module_controller.'driver_earning',
																		'middleware'=> $module_permission.$module_slug.'.driver_earning']);

			Route::get('/get_earning_records',					[		'as' 		=> $route_slug.'get_earning_records',
													   					'uses'		=> $module_controller.'get_earning_records',
																		'middleware'=> $module_permission.$module_slug.'.get_earning_records']);


			Route::get('/earning_info',							[		'as' 		=> $route_slug.'earning_info',
																   		'uses'		=> $module_controller.'earning_info',
																		'middleware'=> $module_permission.$module_slug.'earning_info.']);

			Route::any('make_driver_payment',		['as' 		=> $route_slug.'make_driver_payment',
															'uses' 		=> $module_controller.'make_driver_payment',
															'middleware'=> $module_permission.$module_slug.'.make_driver_payment']);		

		});
		
		Route::group(array('prefix' => '/driver_vehicle'), function() use ($module_permission)
		{
			$route_slug        = "driver_vehicle";
			$module_controller = "Admin\DriverCarController@";
			$module_slug       = "driver_car";

			Route::get('/',							[		'as' 		=> $route_slug.'index',
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('remove_car/{enc_id}',		[		'as' 		=> $route_slug.'remove_car',
															'uses' 		=> $module_controller.'remove_car',
															'middleware'=> $module_permission.$module_slug.'.remove_car']);	


			Route::get('get_cars',					[		'as' 		=> $route_slug.'get_cars',
															'uses' 		=> $module_controller.'get_cars',
															'middleware'=> $module_permission.$module_slug.'.get_cars']);	


			Route::post('assign_car/',				[		'as' 		=> $route_slug.'assign_car',
															'uses' 		=> $module_controller.'assign_car',
															'middleware'=> $module_permission.$module_slug.'.assign_car']);
		});

		/*----------------------------------------------------------------------------------------
			package type 
		----------------------------------------------------------------------------------------*/
		
		Route::group(array('prefix' => '/package_type'), function() use ($module_permission)
		{
			$route_slug       = "package_type";
			$module_controller = "Admin\PackageTypeController@";
			$module_slug       = "package_type";

			Route::get('/',							[		'as' 		=> $route_slug.'index',
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('create/',					[		'as'		=> $route_slug.'create',
															'uses' 		=> $module_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('store/',					[		'as' 		=> $route_slug.'store',
															'uses' 		=> $module_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::get('edit/{enc_id}',				[		'as' 		=> $route_slug.'edit',
															'uses' 		=> $module_controller.'edit',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('update',					[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'update',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('activate/{enc_id}',			[		'as' 		=> $route_slug.'activate',
															'uses' 		=> $module_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('deactivate/{enc_id}',		[		'as'		=> $route_slug.'deactivate',
															'uses'		=> $module_controller.'deactivate',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action', 			[		'as' 		=> $route_slug.'multi_action',
															'uses' 		=> $module_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('delete/{enc_id}',			[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.delete']);
			
		});

		/*----------------------------------------------------------------------------------------
			Vehicle 
		----------------------------------------------------------------------------------------*/
		
		Route::group(array('prefix' => '/vehicle_type'), function() use ($module_permission)
		{
			$route_slug       = "vehicle_type";
			$module_controller = "Admin\VehicleTypeController@";
			$module_slug       = "vehicle_type";

			Route::get('/',							[		'as' 		=> $route_slug.'index',
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('create/',					[		'as'		=> $route_slug.'create',
															'uses' 		=> $module_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('store/',					[		'as' 		=> $route_slug.'store',
															'uses' 		=> $module_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::get('view/{enc_id}',				[		'as' 		=> $route_slug.'view',
															'uses' 		=> $module_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('edit/{enc_id}',				[		'as' 		=> $route_slug.'edit',
															'uses' 		=> $module_controller.'edit',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('update',					[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'update',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('activate/{enc_id}',			[		'as' 		=> $route_slug.'activate',
															'uses' 		=> $module_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('deactivate/{enc_id}',		[		'as'		=> $route_slug.'deactivate',
															'uses'		=> $module_controller.'deactivate',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action', 			[		'as' 		=> $route_slug.'multi_action',
															'uses' 		=> $module_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('delete/{enc_id}',			[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.delete']);
			
			Route::get('/get_records',				[		'as' 		=> $route_slug.'get_records',
															'uses' 		=> $module_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);


			// Route::get('/review/{enc_id}', 			[		'as' 		=> $route_slug.'review',
			// 												'uses' 		=> 'Admin\SuperRiderReviewController@index',
			// 												'middleware'=> $module_permission.$module_slug.'.list']);

			// Route::get('/review/view/{enc_id}', 	[		'as' 		=> $route_slug.'review',
			// 												'uses' 		=> 'Admin\SuperRiderReviewController@view',
			// 												'middleware'=> $module_permission.$module_slug.'.list']);			
		});

		/*----------------------------------------------------------------------------------------
			Vehicle 
		----------------------------------------------------------------------------------------*/
		
		Route::group(array('prefix' => '/vehicle'), function() use ($module_permission)
		{
			$route_slug       = "vehicle";
			$module_controller = "Admin\VehicleController@";
			$module_slug       = "vehicle";

			Route::get('/',							[		'as' 		=> $route_slug.'index',
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('create/',					[		'as'		=> $route_slug.'create',
															'uses' 		=> $module_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('store/',					[		'as' 		=> $route_slug.'store',
															'uses' 		=> $module_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::get('view/{enc_id}',				[		'as' 		=> $route_slug.'view',
															'uses' 		=> $module_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('edit/{enc_id}',				[		'as' 		=> $route_slug.'edit',
															'uses' 		=> $module_controller.'edit',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('update',					[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'update',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('activate/{enc_id}',			[		'as' 		=> $route_slug.'activate',
															'uses' 		=> $module_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('deactivate/{enc_id}',		[		'as'		=> $route_slug.'deactivate',
															'uses'		=> $module_controller.'deactivate',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action', 			[		'as' 		=> $route_slug.'multi_action',
															'uses' 		=> $module_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('delete/{enc_id}',			[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.delete']);
			
			Route::get('/get_records',				[		'as' 		=> $route_slug.'get_records',
															'uses' 		=> $module_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);


			Route::get('/document_status',				[	'as' 		=> $route_slug.'document_status',
															'uses' 		=> $module_controller.'change_document_status',
															'middleware'=> $module_permission.$module_slug.'.list']);

			
			// Route::get('/review/{enc_id}', 					[		'as' 		=> $route_slug.'review',
			// 												'uses' 		=> 'Admin\SuperRiderReviewController@index',
			// 												'middleware'=> $module_permission.$module_slug.'.list']);

			// Route::get('/review/view/{enc_id}', 				[		'as' 		=> $route_slug.'review',
			// 												'uses' 		=> 'Admin\SuperRiderReviewController@view',
			// 												'middleware'=> $module_permission.$module_slug.'.list']);			

			Route::get('verify_vehicle/{enc_id}',	[		'as'		=> $route_slug.'verify_vehicle',
															'uses'		=> $module_controller.'verify_vehicle',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('unverify_vehicle/{enc_id}',[		'as'		=> $route_slug.'unverify_vehicle',
															'uses'		=> $module_controller.'unverify_vehicle',
															'middleware'=> $module_permission.$module_slug.'.update']);
		});
		

		/*----------------------------------------------------------------------------------------
			Static Pages - CMS
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/static_pages'), function() use ($module_permission)
		{
			$route_slug        = "static_pages_";
			$module_controller = "Admin\StaticPageController@";
			$module_slug       = "static_pages";

			Route::get('/', 				 		[		'as'	 	=> $route_slug.'manage',
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('create',			 		[		'as' 		=> $route_slug.'create',
															'uses'		=> $module_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::get('edit/{enc_id}',		 		[		'as' 		=> $route_slug.'edit',
															'uses' 		=> $module_controller.'edit',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::any('store',				 		[		'as' 		=> $route_slug.'store',
															'uses' 		=> $module_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);


			Route::post('update',	 				[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'update',
															'middleware'=> $module_permission.$module_slug.'.update']);


			Route::get('delete/{enc_id}',	 		[		'as' 		=> $route_slug.'delete',
															'uses' 		=> $module_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.detete']);


			Route::get('activate/{enc_id}',  		[		'as' 		=> $route_slug.'activate',
															'uses'		=> $module_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);


			Route::get('deactivate/{enc_id}',		[		'as'		=> $route_slug.'deactivate',
															'uses' 		=> $module_controller.'deactivate',
															'middleware'=> $module_permission.$module_slug.'.update']);


			Route::post('multi_action',		 		[		'as' 		=> $route_slug.'multi_action',
															'uses' 		=> $module_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);	
		});


		/*----------------------------------------------------------------------------------------
			Admin Reviews Tags
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/review_tag'), function() use ($module_permission)
		{
			$route_slug        = "static_pages_";
			$module_controller = "Admin\ReviewTagController@";
			$module_slug       = "review_tag";

			Route::get('/', 				 		[		'as'	 	=> $route_slug.'manage',
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('create',			 		[		'as' 		=> $route_slug.'create',
															'uses'		=> $module_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::get('edit/{enc_id}',		 		[		'as' 		=> $route_slug.'edit',
															'uses' 		=> $module_controller.'edit',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::any('store',				 		[		'as' 		=> $route_slug.'store',
															'uses' 		=> $module_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);


			Route::post('update/{enc_id}',			[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'update',
															'middleware'=> $module_permission.$module_slug.'.update']);


			Route::get('delete/{enc_id}',	 		[		'as' 		=> $route_slug.'delete',
															'uses' 		=> $module_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.detete']);


			Route::get('activate/{enc_id}',  		[		'as' 		=> $route_slug.'activate',
															'uses'		=> $module_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);


			Route::get('deactivate/{enc_id}',		[		'as'		=> $route_slug.'deactivate',
															'uses' 		=> $module_controller.'deactivate',
															'middleware'=> $module_permission.$module_slug.'.update']);


			Route::post('multi_action',		 		[		'as' 		=> $route_slug.'multi_action',
															'uses' 		=> $module_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);	
		});

		
		/*---------------------------------------------------------------------------------------
		|	Email Template
		-----------------------------------------------------------------------------------------*/

		Route::group(array('prefix' => '/email_template'), function() use ($module_permission)
		{
			$route_slug        = "admin_email_template_";
			$module_controller = "Admin\EmailTemplateController@";
			$module_slug	   = 'email_template';

			Route::get('create',					[		'as'		=> $route_slug.'create',
								 						 	'uses' 		=> $module_controller.'create',
								 						 	'middleware'=> $module_permission.$module_slug.'.create']);


			Route::post('store/',					[		'as' 		=> $route_slug.'store',
			 					  							'uses' 		=> $module_controller.'store',
			 					  							'middleware'=> $module_permission.$module_slug.'.create']);


			Route::get('edit/{enc_id}',				[		'as' 		=> $route_slug.'edit',
				 											'uses' 		=> $module_controller.'edit',
				 											'middleware'=> $module_permission.$module_slug.'.update']);


			Route::get('view/{enc_id}/{act_lng}',	[		'as' 		=> $route_slug.'edit',
				 											'uses' 		=> $module_controller.'view',
				 											'middleware'=> $module_permission.$module_slug.'.list']);


			Route::post('update/{enc_id}',			[		'as'		=> $route_slug.'update',
										   					'uses' 		=> $module_controller.'update',
										   					'middleware'=> $module_permission.$module_slug.'.update']);


			Route::get('/',							[		'as' 		=> $route_slug.'index', 
															'uses' 		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);
		});

		
		/*----------------------------------------------------------------------------------------
			Site Settings
		----------------------------------------------------------------------------------------*/
		$module_slug ='site_settings';

		Route::get('site_settings', 				[		'as' 		=> 'site_settings',
															'uses' 		=> 'Admin\SiteSettingController@index',
															'middleware'=> $module_permission.$module_slug.'.update']);

		Route::post('site_settings/update/{enc_id}',[		'as' 		=> 'site_settings',
																'uses' 		=> 'Admin\SiteSettingController@update',
																'middleware'=> $module_permission.$module_slug.'.update']);


		/*----------------------------------------------------------------------------------------
			admin commission
		----------------------------------------------------------------------------------------*/
		$module_slug ='admin_commission';

		Route::get('admin_commission', 				[		'as' 		=> 'admin_commission',
															'uses' 		=> 'Admin\AdminCommissionController@index',
															'middleware'=> $module_permission.$module_slug.'.update']);

		Route::post('admin_commission/update',     	[		'as' 		=> 'admin_commission',
															'uses' 		=> 'Admin\AdminCommissionController@update',
															'middleware'=> $module_permission.$module_slug.'.update']);




		/*----------------------------------------------------------------------------------------
			admin bonus
		----------------------------------------------------------------------------------------*/
		$module_slug ='admin_bonus';

		Route::get('admin_bonus', 					[		'as' 		=> 'admin_bonus',
															'uses' 		=> 'Admin\AdminBonusController@index',
															'middleware'=> $module_permission.$module_slug.'.update']);

		Route::post('admin_bonus/update',     		[		'as' 		=> 'admin_bonus',
															'uses' 		=> 'Admin\AdminBonusController@update',
															'middleware'=> $module_permission.$module_slug.'.update']);



		/*----------------------------------------------------------------------------------------
			My Earning
		----------------------------------------------------------------------------------------*/
		
		Route::group(['prefix'=>'my_earning'],function() use ($module_permission)
		{
			$route_slug        = "admin_my_earning_";
			$module_controller = "Admin\MyEarningController@";
			$module_slug	   = "my_earning";


			Route::get('/get_records',				[		'as' 		=> $route_slug.'get_records',
													   		'uses'		=> $module_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/earning_info',				[		'as' 		=> $route_slug.'earning_info',
													   		'uses'		=> $module_controller.'earning_info',
															'middleware'=> $module_permission.$module_slug.'earning_info.']);
																			
			
			Route::get('/',                       	[		'as'		=> $route_slug.'index',		 	  
															'uses'		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.index']);
		});

		/*----------------------------------------------------------------------------------------
		    Promo Offer
		----------------------------------------------------------------------------------------*/

		Route::group(['prefix'=>'promo_offer'],function() use ($module_permission)
		{
			$route_slug        = "promo_offer";
			$module_controller = "Admin\PromoOfferController@";
			$module_slug	   = "promo_offer";

			Route::get('/',                       	[		'as'		=> $route_slug.'index',		 	  
															'uses'		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('create',                  	[		'as'		=> $route_slug.'create',		 	 
														 	'uses'		=> $module_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('store',                  	[		'as'		=> $route_slug.'store',	 	 	 
														 	'uses'		=> $module_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::get('edit/{enc_id}',           	[		'as'		=> $route_slug.'edit',
															'uses'		=> $module_controller.'edit',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('update',        	[		'as'		=> $route_slug.'update',		 	 
															'uses' 		=> $module_controller.'update',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('activate/{enc_id}',       	[		'as'		=> $route_slug.'activate',	 	
														  	'uses'		=> $module_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('deactivate/{enc_id}',     	[		'as'		=> $route_slug.'deactivate',	
														 	'uses'		=> $module_controller.'deactivate',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action',           	[		'as'		=> $route_slug.'multi_action',
															'uses'		=> $module_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);
	

			Route::get('delete/{enc_id}',	 		[		'as' 		=> $route_slug.'delete',
															'uses' 		=> $module_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.detete']);	

			Route::post('code_existence',	 		[		'as' 		=> $route_slug.'code_existence',
															'uses' 		=> $module_controller.'code_existence']);																											
		});

		/*----------------------------------------------------------------------------------------
		    Promo Offer
		----------------------------------------------------------------------------------------*/

		Route::group(['prefix'=>'promotional_offer'],function() use ($module_permission)
		{
			$route_slug        = "promotional_offer";
			$module_controller = "Admin\PromotionalOfferController@";
			$module_slug	   = "promotional_offer";

			Route::get('/',                       	[		'as'		=> $route_slug.'index',		 	  
															'uses'		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('create',                  	[		'as'		=> $route_slug.'create',		 	 
														 	'uses'		=> $module_controller.'create',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('store',                  	[		'as'		=> $route_slug.'store',	 	 	 
														 	'uses'		=> $module_controller.'store',
															'middleware'=> $module_permission.$module_slug.'.create']);

			Route::get('edit/{enc_id}',           	[		'as'		=> $route_slug.'edit',
															'uses'		=> $module_controller.'edit',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('update',        	[		'as'		=> $route_slug.'update',		 	 
															'uses' 		=> $module_controller.'update',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('activate/{enc_id}',       	[		'as'		=> $route_slug.'activate',	 	
														  	'uses'		=> $module_controller.'activate',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('deactivate/{enc_id}',     	[		'as'		=> $route_slug.'deactivate',	
														 	'uses'		=> $module_controller.'deactivate',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action',           	[		'as'		=> $route_slug.'multi_action',
															'uses'		=> $module_controller.'multi_action',
															'middleware'=> $module_permission.$module_slug.'.update']);
	

			Route::get('delete/{enc_id}',	 		[		'as' 		=> $route_slug.'delete',
															'uses' 		=> $module_controller.'delete',
															'middleware'=> $module_permission.$module_slug.'.detete']);	

			Route::post('code_existence',	 		[		'as' 		=> $route_slug.'code_existence',
															'uses' 		=> $module_controller.'code_existence']);																											
		});

	});	    

	/* Company Routes */


	Route::group(['prefix' => $company_path,'middleware'=>['company']], function () 
	{
		$route_slug       = "";
		$module_permission = "module_permission:";
		$module_controller = "Company\AuthController@";


			Route::get('edit_profile', 													    [	'as'	=> $route_slug.'edit_profile',
																								'uses'	=> $module_controller.'edit_profile']);	

			Route::get('change_password', 													[	'as'	=> $route_slug.'change_password',
																								'uses'	=> $module_controller.'change_password']);	

			Route::post('update_password',													[	'as'	=> $route_slug.'change_password' ,
																								'uses'	=> $module_controller.'update_password']);	

			Route::post('process_forgot_password',											[	'as'	=> $route_slug.'forgot_password',
																								'uses'	=> $module_controller.'process_forgot_password']);
			
			Route::post('reset_password',													[	'as'	=> $route_slug.'reset_passsword',
																								'uses'	=> $module_controller.'reset_password']);


			Route::get('/get_users/{user_type}',											[	'as'		=> $route_slug.'get_users',
																								'uses'		=>'Company\DashboardController@get_users']);	

			
			/*----------------------------------------------------------------------------------------
				Dashboard  
			----------------------------------------------------------------------------------------*/

			Route::get('/dashboard',						[	'as'		=> $route_slug.'dashboard',
																'uses'		=>'Company\DashboardController@index']);	
			
			Route::get('/logout',   						[	'as'		=> $route_slug.'logout',
																'uses'		=> $module_controller.'logout']);


			/*----------------------------------------------------------------------------------------
				Company Profile  
			----------------------------------------------------------------------------------------*/

			$account_setting_controller = "Company\ProfileController@";
			$account_settings_slug = "profile";
		

			Route::get('profile', 													       [	'as'	=> $account_settings_slug.'profile',
																								'uses'	=> $account_setting_controller.'index']);	

			Route::post('/profile/update', 													[	'as'	=> $account_settings_slug.'update_profile',
																								'uses'	=> $account_setting_controller.'update']);	


			
			/*----------------------------------------------------------------------------------------
			admin commission
			----------------------------------------------------------------------------------------*/
			$module_slug ='company_commission';

			Route::get('company_commission', 				[		'as' 		=> 'company_commission',
																'uses' 		=> 'Company\CompanyCommissionController@index',
																'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('company_commission/update',     	[		'as' 		=> 'company_commission',
																'uses' 		=> 'Company\CompanyCommissionController@update',
																'middleware'=> $module_permission.$module_slug.'.update']);


			$module_slug ='deposit_money';

			Route::get('deposit_money', 				[		'as' 		=> 'deposit_money',
																'uses' 		=> 'Company\DepositMoneyController@index',
																'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('deposit_money/change_status',     	[		'as' 		=> 'deposit_money',
																'uses' 		=> 'Company\DepositMoneyController@change_status',
																'middleware'=> $module_permission.$module_slug.'.change_status']);

			
			$module_slug ='stripe_account';

			Route::get('stripe_account', 				[		'as' 		=> 'stripe_account',
																'uses' 		=> 'Company\StripeAccountController@index',
																'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('stripe_account/change_status',     	[		'as' 		=> 'stripe_account',
																'uses' 		=> 'Company\StripeAccountController@change_status',
																'middleware'=> $module_permission.$module_slug.'.change_status']);

			
			Route::get('stripe_account/redirect_from_stripe',     	[		'as' 		=> 'stripe_account',
																'uses' 		=> 'Company\StripeAccountController@redirect_from_stripe',
																'middleware'=> $module_permission.$module_slug.'.redirect_from_stripe']);

			
			Route::group(['prefix'=>'messages'],function() use ($module_permission)
			{
				$route_slug       = "messages";
				$module_controller = "Company\MessagesController@";
				$module_slug       = "messages";

				Route::get('/',                        [  		'as'		=> $route_slug.'index',
																'uses'		=> $module_controller.'index',
																'middleware'=> $module_permission.$module_slug.'.list']);	

				Route::post('/store_chat',               [  		'as'		=> $route_slug.'index',
																'uses'		=> $module_controller.'store_chat',
																'middleware'=> $module_permission.$module_slug.'.list']);	

				Route::get('/get_current_chat_messages',   [  		'as'		=> $route_slug.'index',
																'uses'		=> $module_controller.'get_current_chat_messages',
																'middleware'=> $module_permission.$module_slug.'.list']);	

			});
				
			/*----------------------------------------------------------------------------------------
		 Company Report
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix'=>'/report'), function () use ($module_permission)
		{
			$route_slug       = "company_report_";
			$module_slug       = "report";
			$route_controller = "Company\ReportController@";

			Route::get('/',					  			[	'as' 		=> $route_slug.'index',
													   		'uses'		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/get_user_records',				[		'as' 		=> $route_slug.'get_user_records',
													   		'uses'		=> $route_controller.'get_user_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/get_driver_records',			[	'as' 		=> $route_slug.'get_driver_records',
													   		'uses'		=> $route_controller.'get_driver_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/get_booking_records',			[	'as' 		=> $route_slug.'get_booking_records',
													   		'uses'		=> $route_controller.'get_booking_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

			
			Route::get('/generate_user_excel',			[	'as' 		=> $route_slug.'generate_user_excel',
													   		'uses'		=> $route_controller.'generate_user_excel',
															'middleware'=> $module_permission.$module_slug.'.list']);


			Route::get('/generate_driver_excel',		[	'as' 		=> $route_slug.'generate_driver_excel',
													   		'uses'		=> $route_controller.'generate_driver_excel',
															'middleware'=> $module_permission.$module_slug.'.list']);
														
			Route::get('/generate_excel',			[		'as' 		=> $route_slug.'generate_excel',
														   		'uses'		=> $route_controller.'generate_excel',
																'middleware'=> $module_permission.$module_slug.'.list']);

		});

			

		/*----------------------------------------------------------------------------------------
			Restricted Area  (as per changes restricted area is renamed to assigned area)
		----------------------------------------------------------------------------------------*/

		Route::group(['prefix'=>'booking_summary'],function() use ($module_permission)
		{
			$route_slug       = "booking_summary";
			$module_controller = "Company\BookingSummaryController@";
			$module_slug       = "booking_summary";

			Route::get('/',                        [  		'as'		=> $route_slug.'index',
															'uses'		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);	

			Route::get('/get_records',				[		'as' 		=> $route_slug.'get_records',
													   		'uses'		=> $module_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);	

			Route::get('/view/{enc_id}',			[		'as' 		=> $route_slug.'view',
													   		'uses'		=> $module_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.view']);

			Route::get('/generate_excel',			[		'as' 		=> $route_slug.'generate_excel',
													   		'uses'		=> $module_controller.'generate_excel',
															'middleware'=> $module_permission.$module_slug.'.generate_excel']);

		});

		/*----------------------------------------------------------------------------------------
			Track Booking 
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix'=>'/track_booking'), function () use ($module_permission)
		{
			$route_slug       = "admin_track_booking_";
			$module_slug       = "track_booking";
			$route_controller = "Company\TrackBookingController@";

			Route::get('/',					  		[		'as' 		=> $route_slug.'index',
													   		'uses'		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/booking_history',			[		'as' 		=> $route_slug.'booking_history',
													   		'uses'		=> $route_controller.'booking_history',
															'middleware'=> $module_permission.$module_slug.'.list']);


			Route::get('/get_records',				[		'as' 		=> $route_slug.'get_records',
													   		'uses'		=> $route_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/view',			           [		'as' 		=> $route_slug.'view',
													   		'uses'		=> $route_controller.'view',
															'middleware'=> $module_permission.$module_slug.'.view']);

			
			Route::get('/available_driver',			[		'as' 		=> $route_slug.'available_driver',
												   		'uses'		=> $route_controller.'available_driver',
														'middleware'=> $module_permission.$module_slug.'.list']);


			Route::get('/track_current_booking',			['as' 		=> $route_slug.'track_current_booking',
												   		'uses'		=> $route_controller.'track_current_booking',
														'middleware'=> $module_permission.$module_slug.'.list']);


		});		

		/*----------------------------------------------------------------------------------------
			Notifications
		----------------------------------------------------------------------------------------*/

		Route::group(array('prefix'=>'/notification'), function () use ($module_permission)
		{
			$route_slug       = "notification_";
			$module_slug      = "notification";
			$route_controller = "Company\NotificationsController@";

			Route::get('/',					  		[		'as' 		=> $route_slug.'index',
													   		'uses'		=> $route_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.list']);
			

		});

		// /*----------------------------------------------------------------------------------------
		// 	Driver 
		// ----------------------------------------------------------------------------------------*/
		
		Route::group(array('prefix' => '/driver'), function() use ($module_permission)
		{
			$route_slug        = "driver";
			$module_controller = "Company\DriverController@";
			$module_slug       = "driver";

			Route::get('/',							[			'as' 		=> $route_slug.'index',
																'uses' 		=> $module_controller.'index',
																'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('create/',					[			'as'		=> $route_slug.'create',
																'uses' 		=> $module_controller.'create',
																'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('store/',					[			'as' 		=> $route_slug.'store',
																'uses' 		=> $module_controller.'store',
																'middleware'=> $module_permission.$module_slug.'.create']);

			Route::get('view/{enc_id}',				[			'as' 		=> $route_slug.'view',
																'uses' 		=> $module_controller.'view',
																'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('edit/{enc_id}',				[			'as' 		=> $route_slug.'edit',
																'uses' 		=> $module_controller.'edit',
																'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('update',					[			'as' 		=> $route_slug.'update',
																'uses' 		=> $module_controller.'update',
																'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('activate/{enc_id}',			[			'as' 		=> $route_slug.'activate',
																'uses' 		=> $module_controller.'activate',
																'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('deactivate/{enc_id}',		[			'as'		=> $route_slug.'deactivate',
																'uses'		=> $module_controller.'deactivate',
																'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action', 			[			'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'multi_action',
																'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('delete/{enc_id}',			[			'as' 		=> $route_slug.'update',
																'uses' 		=> $module_controller.'delete',
																'middleware'=> $module_permission.$module_slug.'.delete']);
			
			Route::get('/get_records',				[			'as' 		=> $route_slug.'get_records',
																'uses' 		=> $module_controller.'get_records',
																'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('approve/{enc_id}',			[		'as' 		=> $route_slug.'approve',
															'uses' 		=> $module_controller.'approve',
															'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('unapprove/{enc_id}',		[		'as'		=> $route_slug.'unapprove',
															'uses'		=> $module_controller.'unapprove',
															'middleware'=> $module_permission.$module_slug.'.update']);


			Route::get('reset_password/{enc_id}',			[		'as' 		=> $route_slug.'update',
															'uses' 		=> $module_controller.'reset_password',
															'middleware'=> $module_permission.$module_slug.'.reset_password']);
			
			Route::post('process_reset_password',			['as'=> $route_slug.'process_reset_password',
															'uses' 		=> $module_controller.'process_reset_password',
															'middleware'=> $module_permission.$module_slug.'.process_reset_password']);

			Route::get('/review/{enc_id}', 			[			'as' 		=> $route_slug.'review',
																'uses' 		=> 'Company\DriverReviewController@index',
																'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/review/view/{enc_id}', 	[			'as' 		=> $route_slug.'review',
																'uses' 		=> 'Company\DriverReviewController@view',
																'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('fair_charge_request/{enc_id}',	[		'as' 		=> $route_slug.'fair_charge_request',
																'uses' 		=> $module_controller.'fair_charge_request',
																'middleware'=> $module_permission.$module_slug.'.fair_charge_request']);

			Route::get('change_request_status/{enc_id}/{type}', [		'as' 		=> $route_slug.'change_request_status',
																	    'uses' 		=> $module_controller.'change_request_status',
																	    'middleware'=> $module_permission.$module_slug.'.change_request_status']);

			Route::post('make_payment',					[			'as' 		=> $route_slug.'make_payment',
																'uses' 		=> $module_controller.'make_payment',
																'middleware'=> $module_permission.$module_slug.'.make_payment']);
			
															
			Route::get('deposit_receipt/{enc_id}',	[			'as' 		=> $route_slug.'deposit_receipt',
																'uses' 		=> $module_controller.'deposit_receipt',
																'middleware'=> $module_permission.$module_slug.'.deposit_receipt']);


			Route::get('driver_receipt_details/{enc_id}',[		'as' 		=> $route_slug.'driver_receipt_details',
																'uses' 		=> $module_controller.'driver_receipt_details',
																'middleware'=> $module_permission.$module_slug.'.driver_receipt_details']);

			Route::get('map',						[			'as' 		=> $route_slug.'map',
																'uses' 		=> $module_controller.'map',
																'middleware'=> $module_permission.$module_slug.'.map']);

			Route::get('/get_earning_records',	   [ 		'as' 		=> $route_slug.'get_earning_records',
													   			'uses'		=> $module_controller.'get_earning_records',
																'middleware'=> $module_permission.$module_slug.'.get_earning_records']);

			Route::get('driver_earning/{enc_id}',	[			'as' 		=> $route_slug.'driver_earning',
																'uses' 		=> $module_controller.'driver_earning',
																'middleware'=> $module_permission.$module_slug.'.driver_earning']);

			Route::get('/earning_info',				[			'as' 		=> $route_slug.'earning_info',
													   			'uses'		=> $module_controller.'earning_info',
																'middleware'=> $module_permission.$module_slug.'earning_info.']);
		});
		
		Route::group(array('prefix' => '/driver_vehicle'), function() use ($module_permission)
		{
			$route_slug        = "driver_vehicle";
			$module_controller = "Company\DriverCarController@";
			$module_slug       = "driver_car";

			Route::get('/',							[			'as' 		=> $route_slug.'index',
																'uses' 		=> $module_controller.'index',
																'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('remove_car/{enc_id}',		[			'as' 		=> $route_slug.'remove_car',
																'uses' 		=> $module_controller.'remove_car',
																'middleware'=> $module_permission.$module_slug.'.remove_car']);	


			Route::get('get_cars',					[			'as' 		=> $route_slug.'get_cars',
																'uses' 		=> $module_controller.'get_cars',
																'middleware'=> $module_permission.$module_slug.'.get_cars']);	


			Route::post('assign_car/',				[			'as' 		=> $route_slug.'assign_car',
																'uses' 		=> $module_controller.'assign_car',
																'middleware'=> $module_permission.$module_slug.'.assign_car']);
		});

		// /*----------------------------------------------------------------------------------------
		// 	Vehicle 
		// ----------------------------------------------------------------------------------------*/
		
		Route::group(array('prefix' => '/vehicle'), function() use ($module_permission)
		{
			$route_slug       = "vehicle";
			$module_controller = "Company\VehicleController@";
			$module_slug       = "vehicle";

			Route::get('/',							[			'as' 		=> $route_slug.'index',
																'uses' 		=> $module_controller.'index',
																'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('create/',					[			'as'		=> $route_slug.'create',
																'uses' 		=> $module_controller.'create',
																'middleware'=> $module_permission.$module_slug.'.create']);

			Route::post('store/',					[			'as' 		=> $route_slug.'store',
																'uses' 		=> $module_controller.'store',
																'middleware'=> $module_permission.$module_slug.'.create']);

			Route::get('view/{enc_id}',				[			'as' 		=> $route_slug.'view',
																'uses' 		=> $module_controller.'view',
																'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('edit/{enc_id}',				[			'as' 		=> $route_slug.'edit',
																'uses' 		=> $module_controller.'edit',
																'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('update',					[			'as' 		=> $route_slug.'update',
																'uses' 		=> $module_controller.'update',
																'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('activate/{enc_id}',			[			'as' 		=> $route_slug.'activate',
																'uses' 		=> $module_controller.'activate',
																'middleware'=> $module_permission.$module_slug.'.update']);	

			Route::get('deactivate/{enc_id}',		[			'as'		=> $route_slug.'deactivate',
																'uses'		=> $module_controller.'deactivate',
																'middleware'=> $module_permission.$module_slug.'.update']);

			Route::post('multi_action', 			[			'as' 		=> $route_slug.'multi_action',
																'uses' 		=> $module_controller.'multi_action',
																'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('delete/{enc_id}',			[			'as' 		=> $route_slug.'update',
																'uses' 		=> $module_controller.'delete',
																'middleware'=> $module_permission.$module_slug.'.delete']);
			
			Route::get('/get_records',				[			'as' 		=> $route_slug.'get_records',
																'uses' 		=> $module_controller.'get_records',
																'middleware'=> $module_permission.$module_slug.'.list']);
			
			Route::get('/document_status',				[	'as' 		=> $route_slug.'document_status',
															'uses' 		=> $module_controller.'change_document_status',
															'middleware'=> $module_permission.$module_slug.'.list']);
			Route::get('verify_vehicle/{enc_id}',	[		'as'		=> $route_slug.'verify_vehicle',
															'uses'		=> $module_controller.'verify_vehicle',
															'middleware'=> $module_permission.$module_slug.'.update']);

			Route::get('unverify_vehicle/{enc_id}',[		'as'		=> $route_slug.'unverify_vehicle',
															'uses'		=> $module_controller.'unverify_vehicle',
															'middleware'=> $module_permission.$module_slug.'.update']);

		});	

		// /*----------------------------------------------------------------------------------------
		// 	My Earning
		// ----------------------------------------------------------------------------------------*/
		
		Route::group(['prefix'=>'my_earning'],function() use ($module_permission)
		{
			$route_slug        = "my_earning_";
			$module_controller = "Company\MyEarningController@";
			$module_slug	   = "my_earning";

			Route::get('/get_records',				[		'as' 		=> $route_slug.'get_records',
													   		'uses'		=> $module_controller.'get_records',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/earning_info',				[		'as' 		=> $route_slug.'earning_info',
													   		'uses'		=> $module_controller.'earning_info',
															'middleware'=> $module_permission.$module_slug.'earning_info.']);

			Route::get('/{approve}/{enc_id}',		[		'as' 		=> $route_slug.'status',
													   		'uses'		=> $module_controller.'status',
															'middleware'=> $module_permission.$module_slug.'.list']);

			Route::get('/{unapprove}/{enc_id}',		[		'as' 		=> $route_slug.'status',
													   		'uses'		=> $module_controller.'status',
															'middleware'=> $module_permission.$module_slug.'.list']);
			
			Route::get('/',                       	[		'as'		=> $route_slug.'index',		 	  
															'uses'		=> $module_controller.'index',
															'middleware'=> $module_permission.$module_slug.'.index']);
		});




	});


});

/*------------------------------------------
| Front Route starts Here
--------------------------------------------*/	

// Route::group(array('middleware'=>'api_auth_user'), function () use($route_slug)

Route::group(['prefix'=>'api'] ,function () 
{
	$module_controller = 'Api\AuthController@';
	$module_slug       = 'process_';

	
	Route::post('/store',						['as' 	 => $module_slug.'store', 
								  				'uses' 	 => $module_controller.'store']);

	Route::post('/update_driver_vehicle_details',['as' 	 => $module_slug.'update_driver_vehicle_details', 
								  				'uses' 	 => $module_controller.'update_driver_vehicle_details']);
	

	Route::post('/process_login',	  			['as' 	 => $module_slug.'process_login',
												'uses' 	 => $module_controller.'process_login']);
	
	Route::post('/forget_password',				['as'    => $module_slug.'forget_password', 
												'uses' 	 => $module_controller.'forget_password']);
	
	Route::post('/verify_otp',	  				['as' 	 => $module_slug.'verify_otp',
												'uses' 	 => $module_controller.'verify_otp']);

	Route::post('/resend_otp',					['as' 	 => $module_slug.'resend_otp', 
								  				'uses'   => $module_controller.'resend_otp']);	

	Route::post('/login_facebook',				['as' 	 => $module_slug.'login_facebook', 
								  				'uses' 	 => $module_controller.'login_facebook']);

	Route::post('/register_facebook',			['as' 	 => $module_slug.'register_facebook', 
								  				'uses' 	 => $module_controller.'register_facebook']);


	Route::group(array('prefix' => '/common_data'), function()
	{
		$module_controller = 'Api\CommonDataController@';
		$module_slug       = 'common_data_';


		Route::get('/get_promotional_offers',	['as' 	 => $module_slug.'get_promotional_offers', 
												'uses' 	 => $module_controller.'get_promotional_offers']);

		Route::get('/get_review_tags',			['as' 	 => $module_slug.'get_review_tags', 
												'uses' 	 => $module_controller.'get_review_tags']);

		Route::get('/get_vehicle_type',			['as' 	 => $module_slug.'get_vehicle_type', 
												'uses' 	 => $module_controller.'get_vehicle_type']);

		Route::get('/get_package_type',			['as' 	 => $module_slug.'get_package_type', 
												'uses' 	 => $module_controller.'get_package_type']);

		Route::get('/get_vehicle_brand',			['as' 	 => $module_slug.'get_vehicle_brand', 
												'uses' 	 => $module_controller.'get_vehicle_brand']);

		Route::get('/get_vehicle_model',			['as' 	 => $module_slug.'get_vehicle_model', 
												'uses' 	 => $module_controller.'get_vehicle_model']);

		Route::get('/get_vehicle_year',			['as' 	 => $module_slug.'get_vehicle_year', 
												'uses' 	 => $module_controller.'get_vehicle_year']);
		
		Route::get('/get_terms_and_conditions',			[ 'as' 	 => $module_slug.'get_terms_and_conditions', 
									  			  'uses' 	 => $module_controller.'get_terms_and_conditions']);


		Route::get('/apply_promo_code',			[ 'as' 	 => $module_slug.'apply_promo_code', 
									  			  'uses' 	 => $module_controller.'apply_promo_code']);

		Route::get('/about_us',			[ 'as' 	 => $module_slug.'about_us', 
									  			  'uses' 	 => $module_controller.'about_us']);

		Route::get('/help',			[ 'as' 	 => $module_slug.'help', 
									  			  'uses' 	 => $module_controller.'help']);

		Route::get('/terms_and_conditions',			[ 'as' 	 => $module_slug.'terms_and_conditions', 
									  			  'uses' 	 => $module_controller.'terms_and_conditions']);

		Route::get('/policy',			[ 'as' 	 => $module_slug.'policy', 
									  			  'uses' 	 => $module_controller.'policy']);

		Route::post('/check_trip',			[ 'as' 	 => $module_slug.'check_trip', 
									  			  'uses' 	 => $module_controller.'check_trip']);

		
		Route::get('/get_current_trip_users',		[ 'as' 	 => $module_slug.'get_current_trip_users', 
									  			  'uses' 	 => $module_controller.'get_current_trip_users']);

		
		Route::get('/check_latest_accepted_load_post',			[ 'as' 	 => $module_slug.'check_latest_accepted_load_post', 
									  			  'uses' 	 => $module_controller.'check_latest_accepted_load_post']);


		Route::get('/check_driver_latest_trip',		[ 'as' 	 => $module_slug.'check_driver_latest_trip', 
									  			  'uses' 	 => $module_controller.'check_driver_latest_trip']);

		Route::get('/check_user_login_status',		[ 'as' 	 => $module_slug.'check_user_login_status', 
									  			  'uses' 	 => $module_controller.'check_user_login_status']);
		
		Route::get('/check_driver_stripe_account_id',			[ 'as' 	 => $module_slug.'check_driver_stripe_account_id', 
									  			  'uses' 	 => $module_controller.'check_driver_stripe_account_id']);
									  			  		
		Route::get('/redirect_from_stripe',			[ 'as' 	 => $module_slug.'redirect_from_stripe', 
									  			  'uses' 	 => $module_controller.'redirect_from_stripe']);
		
		Route::get('/get_chat_list',			[ 'as' 	 => $module_slug.'get_chat_list', 
									  			  'uses' 	 => $module_controller.'get_chat_list']);

		Route::get('/get_chat_details',			[ 'as' 	 => $module_slug.'get_chat_details', 
									  			  'uses' 	 => $module_controller.'get_chat_details']);

		Route::post('/store_message',			[ 'as' 	 => $module_slug.'store_message', 
									  			  'uses' 	 => $module_controller.'store_message']);

		Route::post('store_contact_enquiry',		['as' => $module_slug.'store_contact_enquiry',  'uses' => 'Front\ContactUsController@store_contact_enquiry']);


		

	});

	Route::group(array('middleware'=>'api_auth_user'), function (){

		$module_controller = 'Api\AuthController@';
		$module_slug       = 'process_';

		Route::post('/reset_password',				['as' 	 => $module_slug.'reset_password', 
								  				'uses' 	 => $module_controller.'reset_password']);

		Route::post('/change_password',				['as'    => $module_slug.'change_password', 
													'uses' 	 => $module_controller.'change_password']);	


		Route::get('/get_profile',					['as' 	 => $module_slug.'get_profile', 
									  				'uses' 	 => $module_controller.'get_profile']);

		Route::post('/update_profile',				['as' 	 => $module_slug.'update_profile', 
									  				'uses' 	 => $module_controller.'update_profile']);

		Route::post('/verify_mobile_number',		['as' 	 => $module_slug.'verify_mobile_number', 
									 				'uses' 	 => $module_controller.'verify_mobile_number']);
		
		Route::post('/update_mobile_no',			['as' 	 => $module_slug.'update_mobile_no', 
									 				'uses' 	 => $module_controller.'update_mobile_no']);

		Route::get('/get_notification',				['as' 	 => $module_slug.'get_notification', 
									  				'uses' 	 => $module_controller.'get_notification']);
		
		Route::post('/store_review',				['as' 	 => $module_slug.'store_review', 
									  				'uses'   => $module_controller.'store_review']);
		
		Route::get('/get_review',					['as' 	 => $module_slug.'get_review', 
									  				'uses' 	 => $module_controller.'get_review']);

		Route::get('/get_card_details',				['as' 	 => $module_slug.'get_card_details', 
									  				'uses' 	 => $module_controller.'get_card_details']);

		Route::post('/store_card_details',			['as' 	 => $module_slug.'store_card_details', 
									  				'uses' 	 => $module_controller.'store_card_details']);
		
		Route::get('/delete_card',				    ['as' 	 => $module_slug.'delete_card', 
									  				'uses' 	 => $module_controller.'delete_card']);

		Route::get('/get_bonus_points',				['as' 	 => $module_slug.'get_bonus_points', 
									  				'uses' 	 => $module_controller.'get_bonus_points']);
			

		// Route::get('/get_review_details',			['as' 	 => $module_slug.'get_review_details', 
		// 							  				'uses' 	 => $module_controller.'get_review_details']);


		// Route::get('/encrypt_value',				['as' 	 => $module_slug.'encrypt_value', 
		// 							  				'uses' 	 => 'Api\CommonDataController@encrypt_value']);

		// Route::get('/decrypt_value',				['as' 	 => $module_slug.'decrypt_value', 
		// 							 				'uses' 	 => 'Api\CommonDataController@decrypt_value']);

		
		Route::group(array('prefix' => '/load_post'), function()
		{
			$module_controller = 'Api\LoadPostRequestController@';
			$module_slug       = 'user_';

			Route::post('/store_load_post_request',		['as' 	 => $module_slug.'store_load_post_request', 
									  				'uses' 	 => $module_controller.'store_load_post_request']);

			Route::post('/process_load_post_request',	['as' 	 => $module_slug.'process_load_post_request', 
									  			        'uses'  => $module_controller.'process_load_post_request']);

			Route::post('/process_new_load_post_request',	['as' 	 => $module_slug.'process_new_load_post_request', 
									  			        'uses'  => $module_controller.'process_new_load_post_request']);

			Route::post('/load_post_details',	['as' 	 => $module_slug.'load_post_details', 
									  			        'uses'  => $module_controller.'load_post_details']);

			Route::post('/driver_details_on_tap',	['as' 	 => $module_slug.'driver_details_on_tap', 
									  			        'uses'  => $module_controller.'driver_details_on_tap']);
			
			Route::post('/repost_load_post_request',	['as' 	 => $module_slug.'repost_load_post_request', 
									  			        'uses'  => $module_controller.'repost_load_post_request']);

			Route::get('/pending_load_post',		['as' 	 => $module_slug.'pending_load_post', 
									  			        'uses'  => $module_controller.'pending_load_post']);

			Route::get('/ongoing_trips',		['as' 	 => $module_slug.'ongoing_trips', 
									  			        'uses'  => $module_controller.'ongoing_trips']);

			Route::get('/pending_trips',		['as' 	 => $module_slug.'pending_trips', 
									  			        'uses'  => $module_controller.'pending_trips']);
			
			Route::get('/completed_trips',		['as' 	 => $module_slug.'completed_trips', 
									  			        'uses'  => $module_controller.'completed_trips']);
			
			Route::get('/canceled_trips',		['as' 	 => $module_slug.'canceled_trips', 
									  			        'uses'  => $module_controller.'canceled_trips']);

			
			Route::get('/trip_details',		['as' 	 => $module_slug.'trip_details', 
									  			        'uses'  => $module_controller.'trip_details']);

			Route::get('/track_driver',				['as' 	 => $module_slug.'track_driver', 
									  			        'uses'  => $module_controller.'track_driver']);

			Route::post('/process_trip_status',	['as' 	 => $module_slug.'process_trip_status', 
									  			        'uses'  => $module_controller.'process_trip_status']);

			Route::post('/payment_receipt',	['as' 	 => $module_slug.'payment_receipt', 
									  			        'uses'  => $module_controller.'payment_receipt']);
			
			
			Route::get('/cancel_pending_load_post',		['as' 	 => $module_slug.'cancel_pending_load_post', 
									  			        'uses'  => $module_controller.'cancel_pending_load_post']);

			Route::get('/load_post_all_driver_details',		['as' 	 => $module_slug.'load_post_all_driver_details', 
									  			        'uses'  => $module_controller.'load_post_all_driver_details']);
			
		});
		
		Route::group(array('prefix' => '/driver'), function()
		{
			$module_controller = 'Api\DriverController@';
			$module_slug       = 'driver_';

			Route::post('/update_lat_lng',			['as' 	=> $module_slug.'update_lat_lng', 
					                                'uses' 	=> $module_controller.'update_lat_lng']);

			Route::post('/update_availability_status',['as' 	=> $module_slug.'update_availability_status', 
					                                'uses' 	=> $module_controller.'update_availability_status']);		

			Route::get('/get_driver_availability_status',['as' 	=> $module_slug.'get_driver_availability_status', 
					                                'uses' 	=> $module_controller.'get_driver_availability_status']);		

			Route::get('/get_vehicle_details',			['as' 	=> $module_slug.'get_vehicle_details', 
					                                'uses' 	=> $module_controller.'get_vehicle_details']);		

			Route::post('/update_vehicle_details',			['as' 	=> $module_slug.'update_vehicle_details', 
					                                'uses' 	=> $module_controller.'update_vehicle_details']);	

			Route::get('/get_driver_fair_charge',	['as' 	=> $module_slug.'get_driver_fair_charge',
													'uses' 	=> $module_controller.'get_driver_fair_charge']);

			Route::post('/send_driver_fair_charge',	['as' 	=> $module_slug.'send_driver_fair_charge',
													'uses' 	=> $module_controller.'send_driver_fair_charge']);

			Route::get('/get_driver_deposit_money',	['as' 	=> $module_slug.'get_driver_deposit_money',
													'uses' 	=> $module_controller.'get_driver_deposit_money']);

			Route::post('/process_deposit_money_request',	['as' 	=> $module_slug.'process_deposit_money_request',
													'uses' 	=> $module_controller.'process_deposit_money_request']);

			Route::post('/store_driver_deposit',	['as' 	=> $module_slug.'store_driver_deposit',
													'uses' 	=> $module_controller.'store_driver_deposit']);

			Route::get('/get_earning',				['as' 	=> $module_slug.'get_earning',
													'uses' 	=> $module_controller.'get_earning']);

			Route::get('/get_total_earning',		['as' 	=> $module_slug.'get_total_earning',
													'uses' 	=> $module_controller.'get_total_earning']);

			Route::post('/change_ride_status',		['as' 	=> $module_slug.'change_ride_status',
													'uses' 	=> $module_controller.'change_ride_status']);
				
			Route::get('/get_driver_details',       ['as' 	=> $module_slug.'get_driver_details', 
							                        'uses'  => $module_controller.'get_driver_details']);

			Route::post('/change_availability_status',['as' => $module_slug.'change_availability_status', 
						                              'uses' => $module_controller.'change_availability_status']);

			Route::post('/get_driver_payment_history',	['as' 	=> $module_slug.'get_driver_payment_history', 
					                                'uses' 	=> $module_controller.'get_driver_payment_history']);

			Route::post('/payment_status',			['as' 	=> $module_slug.'payment_status', 
					                                'uses' 	=> $module_controller.'payment_status']);

		});
	});

});


// Route::get('/driver_deposite', function () {
//     $exitCode = Artisan::call('driver_deposite:schedule');
// });

Route::get('/store_ride_map_image', function () {
    $exitCode = Artisan::call('store_ride_map_image:schedule');
});													

Route::get('/future_booking_request', function () {
    $exitCode = \Artisan::call('future_booking_request:schedule');
});													

Route::get('cache_clear', function () {
		\Artisan::call('cache:clear');
			//  Clears route cache
		\Artisan::call('route:clear');
		\Cache::flush();
		\Artisan::call('optimize');
		exec('composer dump-autoload');

		dd("Cache cleared!");
	});