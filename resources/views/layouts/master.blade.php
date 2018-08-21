<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>MIS Development</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
   <link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ url("bower_components/font-awesome/css/font-awesome.min.css")}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{ url("bower_components/Ionicons/css/ionicons.min.css")}}">
  <!-- daterange picker -->
  <link rel="stylesheet" href="{{ url("bower_components/bootstrap-daterangepicker/daterangepicker.css")}}">
  <!-- bootstrap datepicker -->
  <link rel="stylesheet" href="{{ url("bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css")}}">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="{{ url("plugins/iCheck/all.css")}}">
  <!-- Bootstrap Color Picker -->
  <link rel="stylesheet" href="{{ url("bower_components/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css")}}">
  <!-- Bootstrap time Picker -->
  <link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
  <!-- Select2 -->
  <link rel="stylesheet" href="{{ url("bower_components/select2/dist/css/select2.min.css")}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ url("dist/css/AdminLTE.min.css")}}">
    <!-- DataTables -->
  <link rel="stylesheet" href="{{ url("bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css")}}">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="{{ url("dist/css/skins/_all-skins.min.css")}}">
   <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
   <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

<!-- Google Font -->
<link rel="stylesheet"
href="{{ url("https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic")}}">

@yield('stylesheets')
</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
<body class="hold-transition skin-purple sidebar-mini">
  <div class="wrapper">
    @include('layouts.header')
    @include('layouts.leftbar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      @yield('header')
      <!-- Main content -->
      <section class="content container-fluid">

      <!--------------------------
        | Your Page Content Here |
        -------------------------->
        @yield('content')

      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    @include('layouts.footer')
  </div>
  <!-- ./wrapper -->

  <!-- REQUIRED JS SCRIPTS -->

  <!-- jQuery 3 -->
<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ url("bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>
<!-- Select2 -->
<script src="{{ url("bower_components/select2/dist/js/select2.full.min.js")}}"></script>
<!-- InputMask -->
<script src="{{ url("plugins/input-mask/jquery.inputmask.js")}}"></script>
<script src="{{ url("plugins/input-mask/jquery.inputmask.date.extensions.js")}}"></script>
<script src="{{ url("plugins/input-mask/jquery.inputmask.extensions.js")}}"></script>
<!-- date-range-picker -->
<script src="{{ url("bower_components/moment/min/moment.min.js")}}"></script>
<script src="{{ url("bower_components/bootstrap-daterangepicker/daterangepicker.js")}}"></script>
<!-- bootstrap datepicker -->
<script src="{{ url("bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js")}}"></script>
<!-- bootstrap color picker -->
<script src="{{ url("bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js")}}"></script>
<!-- bootstrap time picker -->
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<!-- SlimScroll -->
<script src="{{ url("bower_components/jquery-slimscroll/jquery.slimscroll.min.js")}}"></script>
<!-- iCheck 1.0.1 -->
<script src="{{ url("plugins/iCheck/icheck.min.js")}}"></script>
<!-- FastClick -->
<script src="{{ url("bower_components/fastclick/lib/fastclick.js")}}"></script>
<!-- AdminLTE App -->
{{-- <script src="{{ url("dist/js/adminlte.min.js")}}"></script> --}}
<!-- AdminLTE for demo purposes -->
<script src="{{ url("dist/js/demo.js")}}"></script>
<!-- DataTables -->
<script src="{{ url("bower_components/datatables.net/js/jquery.dataTables.min.js")}}"></script>
<script src="{{ url("bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js")}}"></script>
  <!-- AdminLTE App -->
  {{-- <script src="{{ url("dist/js/adminlte.min.js")}}"></script> --}}
  @yield('scripts')
<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. -->
   </body>
   </html>