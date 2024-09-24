<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title></title>
   </head>
   
   {{-- {{dump($arr_trip_data)}} --}}

   <body style="background:#f1f1f1;font-size:12px; font-family:Open Sans; line-height:14px; color:#666;">
      <div style="max-width:750px;width:100%;margin:0 auto;">
         <div>
            <table width="100%">
               <tr>
                  <td bgcolor="#FFFFFF">
                     <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;border-spacing:0">
                        <tr>
                           <td width="40%" style="text-align: left;padding-left:20px"><img src="{{url('/images/logo.png')}}" alt="logo" style="width: 130px;"/></td>
                           <td width="60%" style="text-align: right;padding-right:20px">
                              <div style="font-size: 12px;font-weight: 600;color: #333;margin:0;padding:0; line-height:10px;"> {{isset($arr_trip_data['booking_date']) ? $arr_trip_data['booking_date'] : ''}} </div>
                              <div style="font-size: 12px;font-weight: 600;color: #333;margin:0;padding:0; line-height:10px;">Booking ID: {{isset($arr_trip_data['booking_unique_id']) ? $arr_trip_data['booking_unique_id'] : ''}}</div>
                           </td>
                        </tr>
                        <tr>
                           <td colspan="2" style="border-bottom:1px solid #ccc;"></td>
                        </tr>
                        <tr>
                           <td colspan="2" style="text-align: center;width: 100%;">
                              <div style="font-size: 25px;font-weight: bold;color: #000;"><span>&#36;</span>{{isset($arr_trip_data['total_charge']) ? number_format($arr_trip_data['total_charge'],2) : '0'}}</div>
                              <div style="font-size: 11px;color: #555;text-align: center;">
                                 {{isset($arr_trip_data['booking_unique_id']) ? $arr_trip_data['booking_unique_id'] : ''}}
                              </div>
                              <div style="font-size: 12px;color: #333;font-weight: 600;">Thanks for choosing {{config('app.project.name')}}</div>
                           </td>
                        </tr>
                        <tr>
                           <td colspan="2"></td>
                        </tr>
                        <tr>
                           <td colspan="2">
                           		<table>
                           			<tr>
                           				<td style="font-size: 14px;font-weight: bold;color: #333;text-align: left;">Bill Details</td>
                           			</tr>
                           		</table>
                           </td>
                        </tr>
                        <tr>
                           <td colspan="2" ></td>
                        </tr>
                        <tr width="100%;">
                           <td colspan="2" width="100%;">
                              <table width="100%;" cellpadding="10" style="border: 1px solid #ddd;padding:0px;margin:0;border-collapse: collapse;border-spacing:0"  >
                                 <tr style="background-color: #efefef;">
                                    <th style="border: none;text-align: left;padding:15px;font-weight: 600; color:#333;font-size:12px;">Total Fare</th>
                                    <th style="border: none;text-align: right;padding: 15px;font-weight: 600; color:#333;font-size:12px;"><span>&#36;</span>{{isset($arr_trip_data['total_amount']) ? number_format($arr_trip_data['total_amount'],2) : '0'}}</th>
                                 </tr>
                                 <tr>
                                    <td style="border: none;text-align: left;padding: 15px;font-size:12px;">Discount Amount</td>
                                    <td style="border: none;text-align: right;padding:15px;font-size:12px;"><span>&#36;</span>{{isset($arr_trip_data['applied_promo_code_charge']) ? number_format($arr_trip_data['applied_promo_code_charge'],2) : '0'}}</td>
                                 </tr>
                                 <tr style="background-color: #f1f1f1;">
                                    <td style="border: none;text-align: left;padding: 15px; font-size:16px; color:#333; font-weight:bold;font-size:12px;">Total Bill</td>
                                    <td style="border: none;text-align: right;padding: 15px; font-size:16px; color:#333; font-weight: bold;font-size:12px;"><span>&#36;</span>{{isset($arr_trip_data['total_charge']) ? number_format($arr_trip_data['total_charge'],2) : '0'}}</td>
                                 </tr>
                              </table>
                           </td>
                        </tr>
                        <tr>
                           <td height="10"></td>
                        </tr>
                        <tr>
                           <td colspan="2" style="font-size: 14px;font-weight: bold;color: #333;margin: 20px 0 50px;text-align: left;">
                           		<table>
                           			<tr>
                           				<td>Ride Details</td>
							   		</tr>
							     </table>
                           </td>
                        </tr>
                        <tr>
                           <td colspan="2" ></td>
                        </tr>
                        <tr>
                           <td style="width:60%;">
                              <table style="background-color: #efefef;" cellpadding="8">
                                 <tr>
                                    <td style="width:20%"><img src="{{ isset($arr_trip_data['driver_profile_image']) ? $arr_trip_data['driver_profile_image'] : url('uploads/default-profile.png')}}" alt="" style="width: 40px;"/></td>
                                    <td style="width:40%">
                                       <table cellpadding="3">
                                          <tr>
                                             <td style="font-size: 11px;font-weight: bold;color: #333;text-transform: uppercase;">Driver Name</td>
                                          </tr>
                                          <tr>
                                             <td style="font-size: 10px;color: #333;">{{ isset($arr_trip_data['driver_name']) ? $arr_trip_data['driver_name'] : ''}}</td>
                                          </tr>
                                       </table>
                                    </td>
                                    <td style="width:40%">
                                       <table cellpadding="3">
                                          <tr>
                                             <td style="font-size: 11px;font-weight: bold;color: #333;text-transform: uppercase;">Vehicle Type</td>
                                          </tr>
                                          <tr>
                                             <td style="font-size: 10px;color: #333;">{{isset($arr_trip_data['vehicle_type']) ? $arr_trip_data['vehicle_type'] : ''}}</td>
                                          </tr>
                                       </table>
                                    </td>
                                 </tr>
                              </table>
                              <div style="height:10px"></div>
                              <table>
                              	<tr>
                              		<td>
									  <table style="border:1px solid #ccc" cellpadding="10">
										 <tr>
											<td>
											   <table cellpadding="5">
												  <tr>
													 <td style="font-weight:bold;">Start Ride : {{ isset($arr_trip_data['start_time']) ? $arr_trip_data['start_time'] : ''}}</td>
												  </tr>
												  <tr>
													 <td style="font-size:11px;line-height:15px">{{ isset($arr_trip_data['pickup_location']) ? $arr_trip_data['pickup_location'] : ''}} </td>
												  </tr>
											   </table>
											</td>
										 </tr>
										 <tr>
											<td>
											   <table cellpadding="5">
												  <tr>
													 <td style="font-weight:bold;">End Ride : {{ isset($arr_trip_data['end_time']) ? $arr_trip_data['end_time'] : ''}}</td>
												  </tr>
												  <tr>
													 <td style="font-size:11px;line-height:15px">{{ isset($arr_trip_data['drop_location']) ? $arr_trip_data['drop_location'] : ''}}</td>
												  </tr>
											   </table>
											</td>
										 </tr>
									  </table>
                              		</td>
                              	</tr>
                              </table>
                           </td>
                           <td style="vertcal-align:top;width:40%">
                              <table>
                                 <tr>
                                    <td style="width:5%"></td>
                                    <td style="width:95%">
                                       @if(isset($tcpdf) && count($tcpdf)>0)
                                          <?php 
                                                
                                                $map_image_url = isset($arr_trip_data['map_image_url']) ? $arr_trip_data['map_image_url'] : url('uploads/default-image.jpg');
                                                if($tcpdf){
                                                   $tcpdf->Image($map_image_url,130, 141, 70,76, '', '', '', false, 300);
                                                }
                                          ?>
                                       @else
                                          <img src="{{url('uploads/default-image.jpg')}}" alt="" style="display:block;vertcal-align:top;width:200px;margin-left:20px;"/>
                                       @endif
                                    </td>
                                 </tr>
                              </table>
                           </td>
                        </tr>
                        <tr>
                           <td colspan="2" ></td>
                        </tr>
                        <tr>
                           <td colspan="2" ></td>
                        </tr>
                        <tr>
                           <td colspan="2" style="font-size: 14px;font-weight: bold;color: #333;text-align: left;">
                           		<table>
                           			<tr>
                           				<td>Payment</td>
							   		</tr>
							  	 </table>
                           </td>
                        </tr>
                        <tr>
                           <td colspan="2" height="5"></td>
                        </tr>
                        <tr >
                           <td colspan="2">
                              <table cellpadding="10" style="background-color: #efefef;">
                                 <tr>
                                    <td style="color: #333;font-weight:bold;font-size: 11px;"><img  src="images/money.png" alt="" style="width: 20px;display: inline-block;vertical-align: middle;"/><span style="display: inline-block;vertical-align: middle;padding-left: 10px;"> Paid Amount</span></td>
                                    <td style="color: #333;font-weight: bold;font-size: 14px;text-align: right;"><span>&#36;</span>{{isset($arr_trip_data['total_charge']) ? number_format($arr_trip_data['total_charge'],2) : '0'}}</td>
                                 </tr>
                              </table>
                           </td>
                        </tr>
                        <tr>
                           <td colspan="2" ></td>
                        </tr>
                        <tr>
                           <td colspan="2" ></td>
                        </tr>
                        <tr>
                           <td colspan="2" ></td>
                        </tr>
                        <tr>
                           <td colspan="2" ></td>
                        </tr>
                        <tr>
                           <td colspan="2" ></td>
                        </tr>
                        <tr>
                           <td colspan="2" style="font-size: 14px;font-weight: bold;color: #333;text-align: left;">
                           
                           <table>
                           			<tr>
                           				<td>Trip Details</td>
							   		</tr>
							  	</table>
                           </td>
                        </tr>
                        
                        <tr>
                           <td colspan="2">
                              <table cellpadding="10">
                                 <tr>
                                    <td style="text-align: center;"><img src="{{ isset($arr_trip_data['start_trip_image']) ? $arr_trip_data['start_trip_image'] : url('uploads/default-image.jpg') }}" alt="" style="display:block;vertcal-align:top;width:340px;margin-left:20px;height:230px;"/></td>
                                    <td style="text-align: center;"><img src="{{ isset($arr_trip_data['invoice_image']) ? $arr_trip_data['invoice_image'] : url('uploads/default-image.jpg') }}" alt="" style="display:block;vertcal-align:top;width:340px;margin-left:20px;height:230px;"/></td>
                                 </tr>
                                 <tr>
                                    <td style="text-align: center; font-size:14px; color:#333;font-size:11px;">Load Image</td>
                                   <!-- <td style="text-align: center; font-size:14px; color:#333;font-size:11px;">Signature Image</td>-->
                                    <td style="text-align: center; font-size:14px; color:#333;font-size:11px;">Invoice Image</td>
                                 </tr>
                              </table>
                           </td>
                        </tr>
                        <tr>
                           <td colspan="2" ></td>
                        </tr>
                       <tr>
                           <td colspan="2" style="font-size: 14px;font-weight: bold;color: #333;text-align: left;">
                           		<table>
                           			<tr>
                           				<td>Customer/Package Details</td>
							   		</tr>
							  	</table>
                           </td>
                        </tr>
                        <tr>
                           <td colspan="2" ></td>
                        </tr>
                        <tr>
                           <td width="100%;" colspan="2">
                              <table width="100%;" cellpadding="10" style="border: 1px solid #ddd;padding:0px;margin:0">
                                 <tr style="background-color: #efefef;">
                                    <th style="border: none;text-align: left;padding:15px;font-weight: 600; color:#333;font-size:12px;">Customer Name</th>
                                    <th style="border: none;text-align: right;padding: 15px;font-weight: 600; color:#333;font-size:12px;">{{(isset($arr_trip_data['user_name']) && $arr_trip_data['user_name']!='') ? $arr_trip_data['user_name'] : '-'}}</th>
                                 </tr>
                                 <tr>
                                    <td style="border: none;text-align: left;padding: 15px;font-size:12px;">Package Type</td>
                                    <td style="border: none;text-align: right;padding:15px;font-size:12px;">{{(isset($arr_trip_data['package_type']) && $arr_trip_data['package_type']!='') ? $arr_trip_data['package_type'] : '-'}}</td>
                                 </tr>
                                 
                                 @if(isset($arr_trip_data['package_type']) && $arr_trip_data['package_type']!='PALLET')
                                    <tr style="background-color: #f1f1f1;">
                                       <td style="border: none;text-align: left;padding: 15px; font-size:16px; color:#333; font-size:12px;">Weight</td>
                                       <td style="border: none;text-align: right;padding: 15px; font-size:16px; color:#333; font-size:12px;">{{(isset($arr_trip_data['package_weight']) && $arr_trip_data['package_weight']!='') ? number_format($arr_trip_data['package_weight'],2) : '-'}} Pounds</td>
                                    </tr>

                                    <tr>
                                       <td style="border: none;text-align: left;padding: 15px; font-size:16px; color:#333; font-size:12px;">Length</td>
                                       <td style="border: none;text-align: right;padding: 15px; font-size:16px; color:#333; font-size:12px;">{{(isset($arr_trip_data['package_length']) && $arr_trip_data['package_length']!='') ? number_format($arr_trip_data['package_length'],2) : '-'}} ft</td>
                                    </tr>

                                    <tr style="background-color: #f1f1f1;">
                                       <td style="border: none;text-align: left;padding: 15px; font-size:16px; color:#333; font-size:12px;">Width</td>
                                       <td style="border: none;text-align: right;padding: 15px; font-size:16px; color:#333; font-size:12px;">{{(isset($arr_trip_data['package_breadth']) && $arr_trip_data['package_breadth']!='') ? number_format($arr_trip_data['package_breadth'],2) : '-'}} ft</td>
                                    </tr>

                                    <tr>
                                       <td style="border: none;text-align: left;padding: 15px; font-size:16px; color:#333; font-size:12px;">Height</td>
                                       <td style="border: none;text-align: right;padding: 15px; font-size:16px; color:#333; font-size:12px;">{{(isset($arr_trip_data['package_height']) && $arr_trip_data['package_height']!='') ? number_format($arr_trip_data['package_height'],2) : '-'}} ft</td>
                                    </tr>
                                 @endif

                                 <tr style="background-color: #f1f1f1;">
                                    <td style="border: none;text-align: left;padding: 15px; font-size:16px; color:#333; font-size:12px;">Quantity</td>
                                    <td style="border: none;text-align: right;padding: 15px; font-size:16px; color:#333; font-size:12px;">{{(isset($arr_trip_data['package_quantity']) && $arr_trip_data['package_quantity']!='') ? $arr_trip_data['package_quantity'] : '-'}}</td>
                                 </tr>
                              </table>
                           </td>
                        </tr>
                        <tr>
                           <td colspan="2" ></td>
                        </tr>
                         
                        <tr>
                           <td colspan="2" style="font-size: 14px;font-weight: bold;color: #333;text-align: left;">
                           		<table>
                           			<tr>
                           				<td>Receiver Details</td>
							   		</tr>
							  	</table>
                           </td>
                        </tr>
                        {{-- {{dd($arr_trip_data)}} --}}
                        
                        <tr>
                           <td colspan="2">
								<table cellpadding="10">
									<tr>
										<td width="70%;">
											 <table width="100%;" cellpadding="10" style="border: 1px solid #ddd;padding:0px;margin:0">
												 <tr style="background-color: #efefef;">
													<th style="border: none;text-align: left;padding:15px;font-weight: 600; color:#333;font-size:12px;">PO No.</th>
													<th style="border: none;text-align: right;padding: 15px;font-weight: 600; color:#333;font-size:12px;">{{(isset($arr_trip_data['po_no']) && $arr_trip_data['po_no']!='') ? $arr_trip_data['po_no'] : '-'}}</th>
												 </tr>
												 <tr>
													<td style="border: none;text-align: left;padding: 15px;font-size:12px;">Receiver Name</td>
													<td style="border: none;text-align: right;padding:15px;font-size:12px;">{{(isset($arr_trip_data['receiver_name']) && $arr_trip_data['receiver_name']!='') ? $arr_trip_data['receiver_name'] : '-'}}</td>
												 </tr>
												 <tr style="background-color: #f1f1f1;">
													<td style="border: none;text-align: left;padding: 15px; font-size:16px; color:#333; font-size:12px;">Contact No.</td>
													<td style="border: none;text-align: right;padding: 15px; font-size:16px; color:#333; font-size:12px;">{{(isset($arr_trip_data['receiver_no']) && $arr_trip_data['receiver_no']!='') ? $arr_trip_data['receiver_no'] : '-'}}</td>
												 </tr>
                                     <tr style="background-color: #f1f1f1;">
                                       <td style="border: none;text-align: left;padding: 15px; font-size:16px; color:#333; font-size:12px;">Apt/Suite/Unit.</td>
                                       <td style="border: none;text-align: right;padding: 15px; font-size:16px; color:#333; font-size:12px;">{{(isset($arr_trip_data['app_suite']) && $arr_trip_data['app_suite']!='') ? $arr_trip_data['app_suite'] : '-'}}</td>
                                     </tr>
											  </table>
										</td>
										<td width="30%;">
											<img src="{{ isset($arr_trip_data['end_trip_image']) ? $arr_trip_data['end_trip_image'] : url('uploads/default-image.jpg') }}" alt="" style="display:block;vertcal-align:top;width:150px;margin-left:20px;"/>
										</td>
									</tr>
								</table>
                           </td>
                           
                        </tr>
                        <tr>
                           <td colspan="2" ></td>
                        </tr>
                       <tr>
                       		<td colspan="2" style="text-align:center;font-size:11px;" width="100%">Copyright © {{date('Y')}} {{config('app.project.name')}} LLC.</td>
                       </tr>
                        
                     </table>
                  </td>
               </tr>
            </table>
         </div>
      </div>
   </body>
</html>