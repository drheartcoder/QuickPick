 @extends('front.layout.master')                

    @section('main_content')

    <div class="blank-div"></div>
    <div class="email-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="headding-text-bredcr">
                       Manage Enterprise Users
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="bredcrum-right">
                        <a href="{{ $module_url_path }}" class="bredcrum-home"> Dashboard </a>
                        <span class="arrow-righ"><i class="fa fa-angle-right"></i></span>
                        Manage Enterprise Users
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- {{dd($arr_data)}} --}}


        <!--dashboard page start-->
    <div class="main-wrapper">
        <div class="container-fluid">
            <div class="row">
                @include('front.enterprise_admin.left_bar')
                <div class="col-sm-9 col-md-10 col-lg-10">
                    <div class="manage-add"> 
                        <a href="{{url(config('app.project.role_slug.enterprise_admin_role_slug').'/add_users')}}" class="green-btn chan-right">Add User</a>
                    </div>
                    @include('front.layout._operation_status')
                    <div class="manage-users">

                                <div class="transactions-table table-responsive">
                                    <table id="example" class="table">
                                        <thead >
                                            <tr class="table-row heading">
                                                <th class="table-cell">Reg. Date</th>                                            
                                                <th class="table-cell">Full Name</th>
                                                <th class="table-cell">Email</th>
                                                <th class="table-cell">Mobile No</th>
                                                <th class="table-cell">Address</th>
                                                <th class="table-cell">DOB</th>
                                                <th class="table-cell">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($arr_data) && count($arr_data)>0) 
                                                @foreach($arr_data as $key=>$data)
                                                    <?php
                                                        $enc_id = isset($data['id']) ? base64_encode($data['id']) : 0;
                                                        $edit_url = url(config('app.project.role_slug.enterprise_admin_role_slug').'/edit_user?enc_id='.$enc_id);
                                                        $status_url = url(config('app.project.role_slug.enterprise_admin_role_slug').'/change_status?enc_id='.$enc_id);
                                                    ?>
                                                    <tr>
                                                        <td class="table-cell transaction-id">{{ isset($data['created_at']) ? date('d M Y',strtotime($data['created_at'])) : '-' }}</td>
                                                        <td class="table-cell transaction-date">{{ isset($data['first_name']) ? $data['first_name'] : ''}} {{ isset($data['last_name']) ? $data['last_name'] : ''}}</td>
                                                        <td class="table-cell transaction-date">{{ isset($data['email']) ? $data['email'] : ''}}</td>
                                                        <td class="table-cell transaction-date">{{ isset($data['country_code']) ? $data['country_code'] : ''}}{{ isset($data['mobile_no']) ? $data['mobile_no'] : ''}}</td>
                                                        <td class="table-cell transaction-date">{{ isset($data['address']) ? $data['address'] : ''}}</td>
                                                        <td class="table-cell transaction-date">{{ isset($data['dob']) ? date('d M Y',strtotime($data['dob'])) : '-' }}</td>
                                                        <td class="table-cell transaction-date">

                                                            <a href="{{$edit_url}}" title="Edit Enterprise User"> <i class="fa fa-pencil"></i></a>
                                                            &nbsp;&nbsp;
                                                            @if(isset($data['is_active']) && $data['is_active'] == '1')
                                                                <a href="{{$status_url}}" onclick="return confirm_action(this,event,'Do you really want to deactivate this record ?')" title="Deactivate" ><i class="fa fa-unlock"></i></a>
                                                            @elseif(isset($data['is_active']) && $data['is_active'] == '0')
                                                                <a href="{{$status_url}}" onclick="return confirm_action(this,event,'Do you really want to activate this record ?')" title="Activate" ><i class="fa fa-lock"></i></a>
                                                            @endif

                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                               

                                <div class="clearfix"></div>
       
                    </div>
                </div>

            </div>
        </div>
    </div>

{{-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script> --}}

<link rel="stylesheet" type="text/css" href="{{url('/css/front/dataTables.min.css')}}">
<script type="text/javascript" src="{{url('/js/front/dataTables.min.js')}}"></script>

<script type="text/javascript">
    
    $(document).ready(function() {
        $('#example').DataTable();
    });

</script>
@stop