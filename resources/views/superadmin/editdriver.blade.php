  @extends('layouts.superadmin_master')
  @section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        {{(isset($title)) ? $title : ''}}
        <small>{{(isset($subtitle)) ? $title : ''}}</small>
      </h1>
     <!--  <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
      </ol> -->
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <!--PUT CONETNT HERE  -->
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Quick Example</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
          {!! Form::open(['url' => 'edit-driver','files' => true]) !!}
           {!! Form::token() !!}

              <div class="box-body">
              <div class="col-md-4">
                <div class="form-group">
                  <label for="exampleInputEmail1">First Name</label>
                  {!! Form::text('first_name',$driver->first_name,['class'=>'form-control','placeholder' => 'First Name']) !!}
                  {!! Form::hidden('_id',$driver->_id,['class'=>'form-control','placeholder' => 'First Name']) !!}
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="exampleInputPassword1">Last Name</label>
                  {!! Form::text('last_name',$driver->last_name,['class'=>'form-control','placeholder' => 'Last Name']) !!}
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="exampleInputFile">Email</label>
                  {!! Form::text('email',$driver->email,['class'=>'form-control','placeholder' => 'Email']) !!}
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="exampleInputFile">Date Of Birth</label>
                  {!! Form::text('dob',$driver->dob,['class'=>'form-control datepicker','placeholder' => 'Date of Birth', 'autocomplete' => 'off']) !!}
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="exampleInputFile">Phone</label>
                  {!! Form::text('phone',$driver->phone,['class'=>'form-control','placeholder' => 'Date of Birth']) !!}
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="exampleInputFile">Address</label>
                  {!! Form::textarea('address',$driver->address,['class'=>'form-control','placeholder' => 'Date of Birth', 'rows' => 4]) !!}
                </div>
              </div>
                <div class="col-md-4">
                <div class="form-group countryddwrapper" data-autogetcities="Yes" data-base="{{url('/')}}" >
                    <label for="exampleInputFile ">Country</label>
                       {!! Form::select('country_id',$countries,$driver->country_id,['class'=>'form-control countrydd']) !!}
                </div>
              </div>
               <div class="col-md-4">
                <div class="form-group cityddwrapper">
                    <label for="exampleInputFile ">City</label>
                       {!! Form::select('city_id',[],null,['class'=>'form-control citydd']) !!}
                       {!! Form::hidden('selectedcity',$driver->city_id,['class'=>'form-control selectedcity','placeholder' => 'First Name']) !!}

                </div>
              </div> 
              
               <div class="col-md-4">
                <div class="form-group">
                  <label for="exampleInputFile">Upload Picture *</label>
                  {!! Form::file('profile',null,['class'=>'','placeholder' => 'Profile']) !!}
                </div>
                <img id="imgprview" class="profile-user-img img-responsive pull-left " src="{{isset($driver->image) ? url('/').'/images/driverdocs/'.$driver->image : url('/').'/images/users/no_image.png'}}" alt="User profile picture" style="height: 50px; width:50px;"  >
              </div>

              </div>

              <div class="box-body">
                <h2>Licence Details </h2>
                <div class="col-md-6">
                <div class="form-group">
                  <label for="exampleInputFile">Licence No *</label>
                  {!! Form::text('licence_no',$driver->licence_no,['class'=>'form-control','placeholder' => 'Date of Birth']) !!}
                </div>
              </div>
               
               <div class="col-md-6">
                <div class="form-group">
                  <label for="exampleInputFile">Licence Expiry Date</label>
                  {!! Form::text('licence_expiry_date',$driver->licence_expiry_date,['class'=>'form-control datepicker','placeholder' => 'Date of Birth', 'autocomplete' => 'off']) !!}
                </div>
              </div>
              </div>

              <div class="box-body">
                <h2>Documents </h2>
                <div class="col-md-4">
                <div class="form-group">
                  <label for="exampleInputFile">Driver’s license (PSV) *</label>
                  {!! Form::file('pvc',null,['class'=>'','placeholder' => 'Date of Birth']) !!}
                </div>
                <img id="imgprview" class="profile-user-img img-responsive pull-left " src="{{isset($driver->pvc) ? url('/').'/images/driverdocs/'.$driver->pvc : url('/').'/images/users/no_image.png'}}" alt="User profile picture" style="height: 50px; width:50px;"  >
              </div>
               
               <div class="col-md-4">
                <div class="form-group">
                  <label for="exampleInputFile">Insurance copy *</label>
                  {!! Form::file('insurance',null,['class'=>'form-control datepicker','placeholder' => 'Date of Birth', 'autocomplete' => 'off']) !!}
                </div>
                <img id="imgprview" class="profile-user-img img-responsive pull-left " src="{{isset($driver->insurance) ? url('/').'/images/driverdocs/'.$driver->insurance : url('/').'/images/users/no_image.png'}}" alt="User profile picture" style="height: 50px; width:50px;"  >
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="exampleInputFile">ID copy *</label>
                  {!! Form::file('driverid',null,['class'=>'form-control datepicker','placeholder' => 'Date of Birth', 'autocomplete' => 'off']) !!}
                </div>
                <img id="imgprview" class="profile-user-img img-responsive pull-left " src="{{isset($driver->driverid) ? url('/').'/images/driverdocs/'.$driver->driverid : url('/').'/images/users/no_image.png'}}" alt="User profile picture" style="height: 50px; width:50px;"  >
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="exampleInputFile">Photograph of the car</label>
                  {!! Form::file('car_photo',null,['class'=>'form-control datepicker','placeholder' => 'Date of Birth', 'autocomplete' => 'off']) !!}
                </div>
                <img id="imgprview" class="profile-user-img img-responsive pull-left " src="{{isset($driver->car_photo) ? url('/').'/images/driverdocs/'.$driver->car_photo : url('/').'/images/users/no_image.png'}}" alt="User profile picture" style="height: 50px; width:50px;"  >
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="exampleInputFile">Log book *</label>
                  {!! Form::file('log_book',null,['class'=>'form-control datepicker','placeholder' => 'Date of Birth', 'autocomplete' => 'off']) !!}
                </div>
                <img id="imgprview" class="profile-user-img img-responsive pull-left " src="{{isset($driver->log_book) ? url('/').'/images/driverdocs/'.$driver->log_book : url('/').'/images/users/no_image.png'}}" alt="User profile picture" style="height: 50px; width:50px;"  >
              </div>

              </div>

              <div class="box-body">
                <h2>Driver Bank Details </h2>
                <div class="col-md-4">
                <div class="form-group">
                  <label for="exampleInputFile">Account Name *</label>
                  {!! Form::text('bank_account_name',$driver->bank_account_name,['class'=>'form-control','placeholder' => 'Date of Birth']) !!}
                </div>
              </div>
               <div class="col-md-4">
                <div class="form-group">
                  <label for="exampleInputFile">Bank Name *</label>
                  {!! Form::text('bank_name',$driver->bank_name,['class'=>'form-control','placeholder' => 'Date of Birth']) !!}
                </div>
              </div>
               <div class="col-md-4">
                <div class="form-group">
                  <label for="exampleInputFile">Bank Branch Name *</label>
                  {!! Form::text('bank_branch_name',$driver->bank_branch_name,['class'=>'form-control','placeholder' => 'Date of Birth']) !!}
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="exampleInputFile">Bank Branch Code *</label>
                  {!! Form::text('bank_branch_code',$driver->bank_branch_code,['class'=>'form-control','placeholder' => 'Date of Birth']) !!}
                </div>
              </div>
               <div class="col-md-4">
                <div class="form-group">
                  <label for="exampleInputFile">Bank Account No*</label>
                  {!! Form::text('bank_account_no',$driver->bank_account_no,['class'=>'form-control','placeholder' => 'Date of Birth']) !!}
                </div>
              </div>


              </div>

              

              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            {!! Form::close() !!}

            <input type="hidden" name="page" class="page" value="editdriver">
          </div>
        

        </div>
        
      </div>
      <!-- /.row -->
      <!-- Main row -->
      <!-- /.row (main row) -->
    </section>
    <!-- /.content -->
  </div>
 <script src="{{url('/')}}/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{url('/')}}/https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>

<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->


<!-- Bootstrap 3.3.6 -->
<script src="{{url('/')}}/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js"></script>
<!-- Morris.js charts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="{{url('/')}}/bower_components/AdminLTE/plugins/morris/morris.min.js"></script>
<!-- Sparkline -->
<script src="{{url('/')}}/bower_components/AdminLTE/plugins/sparkline/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="{{url('/')}}/bower_components/AdminLTE/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="{{url('/')}}/bower_components/AdminLTE/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- jQuery Knob Chart -->
<script src="{{url('/')}}/bower_components/AdminLTE/plugins/knob/jquery.knob.js"></script>
<!-- daterangepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="{{url('/')}}/bower_components/AdminLTE/plugins/daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="{{url('/')}}/bower_components/AdminLTE/plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="{{url('/')}}/js/datepicker.js"></script>
<script src="{{url('/')}}/js/getcities.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="{{url('/')}}/bower_components/AdminLTE/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="{{url('/')}}/bower_components/AdminLTE/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="{{url('/')}}/bower_components/AdminLTE/plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="{{url('/')}}/bower_components/AdminLTE/dist/js/app.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!-- <script src="{{url('/')}}/bower_components/AdminLTE/dist/js/pages/dashboard.js"></script> -->
<!-- AdminLTE for demo purposes -->
<script src="{{url('/')}}/bower_components/AdminLTE/dist/js/demo.js"></script>
    @stop