@extends('front.layout.master')                

@section('main_content')

 <div class="blank-div"></div>
 <div class="email-block change">
        <div class="container">
           
           <div class="headding-text-bredcr">
                       Terms &amp; Conditions
                    </div> 
                    
                    
            <!--<div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="headding-text-bredcr">
                       Terms &amp; Conditions
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                        <a href="{{ url('/')}}" class="bredcrum-home"> Home </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span>
                        Terms &amp; Conditions
                    </div>
                </div>
            </div>-->
        </div>
    </div>

{!! isset($page_details->page_desc) ? $page_details->page_desc : '' !!}

@stop