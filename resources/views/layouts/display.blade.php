<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>MIS Development</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
  <link rel="stylesheet" href="{{ url("bower_components/font-awesome/css/font-awesome.min.css")}}">
  <link rel="stylesheet" href="{{ url("bower_components/Ionicons/css/ionicons.min.css")}}">
  <link rel="stylesheet" href="{{ url("bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css")}}">
  <link rel="stylesheet" href="{{ url("bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css")}}">
  <link rel="stylesheet" href="{{ url("plugins/iCheck/all.css")}}">
  <link rel="stylesheet" href="{{ url("bower_components/select2/dist/css/select2.min.css")}}">
  <link rel="stylesheet" href="{{ url("dist/css/AdminLTE.min.css")}}">
  <link rel="stylesheet" href="{{ url("dist/css/skins/skin-purple.css")}}">
  <link rel="stylesheet" href="{{ url("fonts/SourceSansPro.css")}}">
  <link rel="stylesheet" href="{{ url("css/buttons.dataTables.min.css")}}">
  <link rel="stylesheet" href="{{ url("plugins/pace/pace.min.css")}}">
  @yield('stylesheets')
</head>
<body class="hold-transition skin-purple layout-top-nav">
  <div class="wrapper">
    <header class="main-header">
      <nav class="navbar navbar-static-top">
        <div class="container">
          <div class="navbar-header">
            <a href="/index2.html" class="navbar-brand"><b>Admin</b>LTE</a>
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
              <i class="fa fa-bars"></i>
            </button>
          </div>
        </div>
      </nav>
    </header>
    <div class="content-wrapper">
      <div class="container">
        <section class="content-header">
         @yield('header')
       </section>
       <section class="content">
         @yield('content')
       </section>
     </div>
   </div>
   @include('layouts.footer')
 </div>
 <script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
 <script src="{{ url("bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>
 <script src="{{ url("bower_components/datatables.net/js/jquery.dataTables.min.js")}}"></script>
 <script src="{{ url("bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js")}}"></script>
 <script src="{{ url("bower_components/select2/dist/js/select2.full.min.js")}}"></script>
 <script src="{{ url("bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js")}}"></script>
 <script src="{{ url("bower_components/jquery-slimscroll/jquery.slimscroll.min.js")}}"></script>
 <script src="{{ url("plugins/iCheck/icheck.min.js")}}"></script>
 <script src="{{ url("bower_components/fastclick/lib/fastclick.js")}}"></script>
 <script src="{{ url("bower_components/PACE/pace.min.js")}}"></script>
 <script src="{{ url("dist/js/adminlte.min.js")}}"></script>
 <script src="{{ url("dist/js/demo.js")}}"></script>
 <script>$(document).ajaxStart(function() { Pace.restart(); });</script>
 @yield('scripts')
</body>
</html>
