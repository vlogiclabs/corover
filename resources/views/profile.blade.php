  @if($userdata->role == 1)
    @extends('layouts.superadmin_master')
  @elseif($userdata->role == 2)
    @extends('layouts.admin_master')
  @elseif($userdata->role == 3)
    @extends('layouts.user_master')
  @endif
  
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
          <div class="col-lg-12">
            <div class="row">
        <div class="col-md-3">

          <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="{{isset($userdata->image) ? url('/').'/images/users/'.$userdata->image : url('/').'/images/users/no_image.png'}}" alt="User profile picture">

              <h3 class="profile-username text-center">{{$userdata->first_name .' '. $userdata->last_name}}</h3>

              <p class="text-muted text-center">Software Engineer</p>

              <!-- <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Followers</b> <a class="pull-right">1,322</a>
                </li>
                <li class="list-group-item">
                  <b>Following</b> <a class="pull-right">543</a>
                </li>
                <li class="list-group-item">
                  <b>Friends</b> <a class="pull-right">13,287</a>
                </li>
              </ul> -->

              <!-- <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a> -->
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

          <!-- About Me Box -->
          <!-- <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">About Me</h3>
            </div>
            
            <div class="box-body">
              <strong><i class="fa fa-book margin-r-5"></i> Education</strong>

              <p class="text-muted">
                {{(isset($userdata->education) && !empty($userdata->education)) ? $userdata->education : 'Not Specified'}}
              </p>

              <hr>

              <strong><i class="fa fa-map-marker margin-r-5"></i> Location</strong>

              <p class="text-muted">
                {{(isset($userdata->location) && !empty($userdata->location)) ? $userdata->location : 'Not Specified'}}
              </p>

              <hr>
              <hr>

              
            </div>
            
          </div> -->
          <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#activity" data-toggle="tab" aria-expanded="true">Profile Photo</a></li>
              <!-- <li class="active"><a href="#timeline" data-toggle="tab" aria-expanded="false">Timeline</a></li> -->
              <!-- <li class=""><a href="#settings" data-toggle="tab" aria-expanded="false">Other Information</a></li> -->
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="activity">
                <!-- Post -->
               <!--  <div class="col-lg-12"> -->
                <div class="row">
                 <img id="imgprview" class="profile-user-img img-responsive img-circle " src="{{isset($userdata->image) ? url('/').'/images/users/'.$userdata->image : url('/').'/images/users/no_image.png'}}" alt="User profile picture">
                  {!! Form::open(['url' => 'profile' ,'files' => true]) !!}
                     {!! Form::token() !!}

                        <div class="box-body">
                          <div class="form-group">
                            <label for="exampleInputEmail1">Select Image</label>
                            {!! Form::file('image',['class'=>'image-file']) !!}
                          </div>
                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer">
                          <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                      {!! Form::close() !!}
                   
                </div>
                  
                <!-- </div> -->
                <!-- /.post -->
              </div>
              <!-- /.tab-pane -->

              <!-- /.tab-pane -->

              
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>

          </div>




        
        <!-- ./col -->
      </div>
      <!-- /.row -->
      <!-- Main row -->








      <!-- /.row (main row) -->

    </section>
    <!-- /.content -->
  </div>
  <script src="bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<script src="js/common.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
  $.widget.bridge('uibutton', $.ui.button);
</script>

<!-- Bootstrap 3.3.6 -->
<script src="bower_components/AdminLTE/bootstrap/js/bootstrap.min.js"></script>
<!-- Morris.js charts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<!-- <script src="bower_components/AdminLTE/plugins/morris/morris.min.js"></script> -->
<!-- Sparkline -->
<script src="bower_components/AdminLTE/plugins/sparkline/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="bower_components/AdminLTE/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="bower_components/AdminLTE/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- jQuery Knob Chart -->
<script src="bower_components/AdminLTE/plugins/knob/jquery.knob.js"></script>
<!-- daterangepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="bower_components/AdminLTE/plugins/daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="bower_components/AdminLTE/plugins/datepicker/bootstrap-datepicker.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="bower_components/AdminLTE/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="bower_components/AdminLTE/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="bower_components/AdminLTE/plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="bower_components/AdminLTE/dist/js/app.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!-- <script src="bower_components/AdminLTE/dist/js/pages/dashboard.js"></script> -->
<!-- AdminLTE for demo purposes -->
<script src="bower_components/AdminLTE/dist/js/demo.js"></script>
  @stop