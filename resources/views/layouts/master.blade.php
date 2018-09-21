
<html>
<head>
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset("favicon.png")}}" />
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>MIS Development</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="{{ asset("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset("bower_components/font-awesome/css/font-awesome.min.css")}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{ asset("bower_components/Ionicons/css/ionicons.min.css")}}">
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset("bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css")}}">
  <!-- bootstrap datepicker -->
  <link rel="stylesheet" href="{{ asset("bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css")}}">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="{{ asset("plugins/iCheck/all.css")}}">
  <!-- Select2 -->
  <link rel="stylesheet" href="{{ asset("bower_components/select2/dist/css/select2.min.css")}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset("dist/css/AdminLTE.min.css")}}">

  <link rel="stylesheet" href="{{ asset("dist/css/skins/_all-skins.min.css")}}">

  <link rel="stylesheet" href="{{ asset("fonts/SourceSansPro.css")}}">

  @yield('stylesheets')
</head>
<body class="hold-transition skin-purple sidebar-mini">
  <div class="wrapper">
    @include('layouts.header')
    @include('layouts.leftbar')
    <div class="content-wrapper">
      @yield('header')
      @yield('content')
    </div>
    @include('layouts.footer')
  </div>

  <!-- jQuery 3 -->
  <script src="{{ asset("bower_components/jquery/dist/jquery.min.js")}}"></script>
  <!-- Bootstrap 3.3.7 -->
  <script src="{{ asset("bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>
  <!-- DataTables -->
  <script src="{{ asset("bower_components/datatables.net/js/jquery.dataTables.min.js")}}"></script>
  <script src="{{ asset("bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js")}}"></script>
  <!-- Select2 -->
  <script src="{{ asset("bower_components/select2/dist/js/select2.full.min.js")}}"></script>
  <!-- bootstrap datepicker -->
  <script src="{{ asset("bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js")}}"></script>
  <!-- SlimScroll -->
  <script src="{{ asset("bower_components/jquery-slimscroll/jquery.slimscroll.min.js")}}"></script>
  <!-- iCheck 1.0.1 -->
  <script src="{{ asset("plugins/iCheck/icheck.min.js")}}"></script>
  <!-- FastClick -->
  <script src="{{ asset("bower_components/fastclick/lib/fastclick.js")}}"></script>
  <!-- AdminLTE App -->
  <script src="{{ asset("dist/js/adminlte.min.js")}}"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="{{ asset("dist/js/demo.js")}}"></script>
  @yield('scripts')
</body>
</html>