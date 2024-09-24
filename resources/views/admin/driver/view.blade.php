@extends('admin.layout.master')                
@section('main_content')


<div class="page-title">
	<div></div>
</div>

<div id="breadcrumbs">
	<ul class="breadcrumb">
		<li>
			<i class="fa fa-home"> </i>
			<a href="{{ url($admin_panel_slug.'/dashboard') }}">Dashboard</a>
		</li>
		<span class="divider">
			<i class="fa fa-angle-right"></i>
			<i class="fa fa-car faa-vertical animated-hover"></i>

			<a href="{{ url($module_url_path) }}" class="call_loader">{{ $module_title or ''}} </a>
		</span> 
		<span class="divider">
			<i class="fa fa-angle-right"></i>
			<i class="fa fa-eye"></i>
		</span> 
		<li class="active">   {{ $page_title or '' }}
		</li>
	</ul>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="box ">
			<div class="box-title">
				<h3>
					<i class="fa fa-eye"></i> {{ $page_title or '' }} 
				</h3>
				<div class="box-tool"></div>
			</div>
			<div class="box-content">
				<?php

				$first_name    = isset($arr_user['first_name']) ?$arr_user['first_name']:"";
				$last_name     = isset($arr_user['last_name']) ?$arr_user['last_name']:"";
				$name = $first_name.' '.$last_name;

				$dob  = isset($arr_user['dob']) ? date('d m Y',strtotime($arr_user['dob'])) : "-";

				$country_code  = isset($arr_user['country_code']) ?$arr_user['country_code']:"";
				$contact_number= isset($arr_user['mobile_no']) ?$arr_user['mobile_no']:"";
				$email         = isset($arr_user['email']) ?$arr_user['email']:"";
				$profile_image = isset($arr_user['profile_image']) ?$arr_user['profile_image']:"";

				$driving_license = isset($arr_user['driving_license']) ?$arr_user['driving_license']:"";

				$country       = isset($arr_user['country_details']['country_name']) ?$arr_user['country_details']['country_name']:"";
				$state         = isset($arr_user['state_details']['state_name']) ?$arr_user['state_details']['state_name']:"";
				$city          = isset($arr_user['city_details']['city_name']) ?$arr_user['city_details']['city_name']:"";
				$address       = isset($arr_user['address']) ?$arr_user['address']:"";
				$post_code     = isset($arr_user['post_code']) ?$arr_user['post_code']:""; 				?>
				
				<div class="box">
					<div class="box-content studt-padding">
						<div class="row">
							<div class="col-md-8">
								<h3>Personal Information</h3>
								<br>
								<table class="table table-bordered">
									<tbody>
										<tr>
											<th width="30%">Profile Image</th>
											<td>
												<img alt="pic" src="{{ get_resized_image($profile_image,config('app.project.img_path.user_profile_images') , 150,200) }}" />
											</td>
										</tr>

										<tr>
											<th>Driving License</th>
											<td>
												@if($driving_license!='' && file_exists($driving_license_base_path.$driving_license))
													<a href="{{$driving_license_public_path}}{{$driving_license}}" download> Download </a>
												@else
													<a href="javascript:void(0);"> Download </a>
												@endif
												{{-- <img alt="pic" src="{{ get_resized_image($profile_image,config('app.project.img_path.user_profile_images') , 150,200) }}" /> --}}
										</td>
									</tr>

									<tr>
										<th>Name</th>
										<td>{{$name or ''}}</td>
									</tr> 

									<tr>
										<th>Email</th>
										<td>{{$email or ''}}</td>
									</tr>
									
									<tr>
										<th>Mobile Number</th>
										<td>{{$country_code.' '.$contact_number}}</td>
									</tr>

									<tr>
										<th>Address</th>
										<td>{{ $address or '' }}</td>
									</tr>

									<tr>
										<th>Post Code</th>
										<td>{{$post_code or '-'}}</td>
									</tr>
									
									<tr>
										<th>Date of Birth</th>
										<td>{{$dob or '-'}}</td>
									</tr>


								</tbody>
								<tfoot>
									<tr>
										<td align="center" colspan="2"><a class="btn" href="{{ url($module_url_path) }}">Back</a></td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
