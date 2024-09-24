@extends('front.layout.master')                

@section('main_content')

 <div class="blank-div"></div>
<div class="email-block">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="headding-text-bredcr">
                       Privacy Policy
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                        <a href="{{ url('/')}}" class="bredcrum-home"> Home </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span>
                        Privacy Policy
                    </div>
                </div>
            </div>
        </div>
    </div>

{!! $page_details->page_desc !!}

@stop