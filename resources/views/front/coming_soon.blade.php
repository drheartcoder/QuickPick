<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="author" content="" />
    <title>Coming Soon</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{url('/images/favicon-48x48.png')}}">
    <!-- ======================================================================== -->
    <!--<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">-->
    <!-- Bootstrap CSS -->
    <link href="{{ url('/')}}/css/front/bootstrap.css" rel="stylesheet" type="text/css" />
    <!--font-awesome-css-start-here-->
    <link href="{{ url('/')}}/css/front/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!--Custom Css-->
    <link href="{{ url('/')}}/css/front/quick-pick.css" rel="stylesheet" type="text/css" />

    <!--Main JS-->
    <script type="text/javascript" src="{{ url('/')}}/js/front/jquery-1.11.3.min.js"></script>

<style>
    body{background-image:url({{url('/images/bg-404.jpg')}});background-repeat:no-repeat;background-size:cover;background-attachment: fixed;background-position: center center;}
    body:before{position: fixed;content: "";width: 100%;height: 100%;top: 0;left: 0;background-image: url({{url('/images/404-banner-bitted.png')}});
 display: block;}
</style>

</head>
  
  
   <body>
      <div class="container">
          <div class="wrapper-404 comming">
             <div class="direction"></div>
             <div class="comming-page-logo">
                 <a href="{{ url('/')}}"> <img src="{{url('images/logo.png')}}" alt="logo"/></a>
             </div>
             <div class="comming-page-content">
                  <h1><span>We're</span> Coming Soon...</h1>
                  {{-- <h4>Oops...Page Not Found !</h4> --}}
                  {{-- <h5>We're sorry, but page you were looking for doesnt exist.</h5> --}}
                  <div class="index-fore-btn-main">
                  <a href="{{ url('/')}}" class="back-btn-foure">Go Back To Homepage</a>
                  </div>
              </div>
          </div>
      </div>

   </body>
   </html>