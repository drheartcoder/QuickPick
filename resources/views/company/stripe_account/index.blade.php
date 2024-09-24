@extends('company.layout.master')    

@section('main_content')

    <!-- BEGIN Page Title -->
    <div class="page-title">
        <div>

        </div>
    </div>
    <!-- END Page Title -->

    <!-- BEGIN Breadcrumb -->
    <div id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url($company_panel_slug.'/dashboard') }}">Dashboard</a>
            </li>
            <span class="divider">
                <i class="fa fa-angle-right"></i>
                <i class="fa fa-cc-stripe"></i>
            </span> 
            <li class="active">  {{ isset($page_title)?$page_title:"" }}</li>
        </ul>
    </div>
    <!-- END Breadcrumb -->

    <!-- BEGIN Main Content -->
    
    
    <div class="row">
        <div class="col-md-12">
            <div class="box  {{ $theme_color }}">
                <div class="box-title">
                    <h3><i class="fa fa-cc-stripe"></i> {{ isset($page_title)?$page_title:"" }}</h3>
                    <div class="box-tool">
                    </div>
                </div>
                <div class="box-content">
                    @include('company.layout._operation_status')
                    
                    @if(isset($arr_data['stripe_account_id']) && $arr_data['stripe_account_id']!='')
                        {{-- <div class="alert-box success"><span>DONE: </span>Your account is connected to Stripe</div> --}}
                        <div class="alert alert-success">
                            <button type="button" class="close" style="margin-top: 0px !important;padding: 0px !important;" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <span>DONE: </span>Your account is connected to Stripe
                        </div>

                    @endif


                    <div class="form-group">
                        <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                            <a style="cursor: pointer;" href="{{ $stripe_authorize_url }}?response_type=code&client_id={{ $stripe_client_id }}&scope=read_write&state={{$encrypted_company_id}}&redirect_uri={{$stripe_company_redirect_url}}"><img src="{{url('/images/blue-on-light.png')}}"></a>
                        </div>
                   </div>
                </div>
            </div>
        </div>
    
    <!-- END Main Content --> 
@endsection