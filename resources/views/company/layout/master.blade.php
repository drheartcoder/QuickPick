<!-- HEader -->        
@include('company.layout._header')    
        
<!-- BEGIN Sidebar -->
@include('company.layout._sidebar')
<!-- END Sidebar -->

<!-- BEGIN Content -->
<div id="main-content">
    @yield('main_content')
</div>
    <!-- END Main Content -->

<!-- Footer -->        
@include('company.layout._footer')    
                
              