
<html>
<head>
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset("favicon.png")}}" />
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>MIS Development</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="{{ asset("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
  <link rel="stylesheet" href="{{ asset("bower_components/font-awesome/css/font-awesome.min.css")}}">
  <link rel="stylesheet" href="{{ asset("bower_components/Ionicons/css/ionicons.min.css")}}">
  <link rel="stylesheet" href="{{ asset("bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css")}}">
  <link rel="stylesheet" href="{{ asset("bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css")}}">
  <link rel="stylesheet" href="{{ asset("plugins/iCheck/all.css")}}">
  <link rel="stylesheet" href="{{ asset("bower_components/select2/dist/css/select2.min.css")}}">
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
  <script src="{{ asset("bower_components/jquery/dist/jquery.min.js")}}"></script>
  <script src="{{ asset("bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>
  <script src="{{ asset("bower_components/datatables.net/js/jquery.dataTables.min.js")}}"></script>
  <script src="{{ asset("bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js")}}"></script>
  <script src="{{ asset("bower_components/select2/dist/js/select2.full.min.js")}}"></script>
  <script src="{{ asset("bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js")}}"></script>
  <script src="{{ asset("bower_components/jquery-slimscroll/jquery.slimscroll.min.js")}}"></script>
  <script src="{{ asset("plugins/iCheck/icheck.min.js")}}"></script>
  <script src="{{ asset("bower_components/fastclick/lib/fastclick.js")}}"></script>
  <script src="{{ asset("dist/js/adminlte.min.js")}}"></script>
  <script src="{{ asset("dist/js/demo.js")}}"></script>
  @yield('scripts')
</body>
</html>