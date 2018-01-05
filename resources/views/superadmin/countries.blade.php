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
          <div class="col-lg-12 col-md-12">
            <table class="tablesaw table-striped table-hover table-bordered table tablesaw-columntoggle" data-tablesaw-mode="columntoggle" id="table-4226">
              <thead>
                <tr>
                  <th scope="col" data-tablesaw-sortable-col="" data-tablesaw-priority="persist">Name</th>
                  <th scope="col" data-tablesaw-sortable-col="" data-tablesaw-sortable-default-col="" data-tablesaw-priority="3" class="tablesaw-priority-3">ISo Code</th>
                  <th scope="col" data-tablesaw-sortable-col="" data-tablesaw-priority="2" class="tablesaw-priority-2">Actions</th>
                </tr>
              </thead>
              <tbody>
              @foreach($admins as $admin)
                <tr>
                  <td class="title"><a href="javascript:void(0)">{{$admin->name}}</a></td>
                  <td class="tablesaw-priority-3">{{$admin->iso}}</td>
                  <td class="tablesaw-priority-2">
                     <a href="{{url('/')}}/delete-country" class="__del" data-origin='{{$admin->_id}}'><i class="fa fa-times m-l-5 m-r-5"></i></a> 
                     <a href="{{url('/')}}/edit-country/{{$admin->_id}}" class="edit-admin"><i class="fa fa-edit m-l-5 m-r-5"></i></a> 
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>

          </div>

        
        <!-- ./col -->
      </div>
      <!-- /.row -->
      <!-- Main row -->








      <!-- /.row (main row) -->

    </section>
    <!-- /.content -->
  </div>
  <script src="{{url('/')}}/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<script src="{{url('/')}}/js/common.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
  $.widget.bridge('uibutton', $.ui.button);
</script>

<!-- Bootstrap 3.3.6 -->
<script src="{{url('/')}}/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js"></script>
<!-- Morris.js charts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<!-- <script src="{{url('/')}}/bower_components/AdminLTE/plugins/morris/morris.min.js"></script> -->
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