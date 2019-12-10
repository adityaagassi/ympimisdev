<!DOCTYPE html>
<html>
<head>
  <link rel="shortcut icon" type="image/x-icon" href="{{ url("logo_mirai.png")}}" />
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <title>YMPI 情報システム</title>
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
  {{-- <link rel="stylesheet" href="{{ url("plugins/pace/pace.min.css")}}"> --}}
  @yield('stylesheets')
</head>


<body class="hold-transition skin-purple layout-top-nav">
  <div class="wrapper">
    <header class="main-header" >
      <nav class="navbar navbar-static-top">
        {{-- <div class="container"> --}}
          <div class="navbar-header">
            <a href="{{ url("/home") }}" class="logo">
              @if($page == "Employment Services")
              <span style="font-size: 35px"><img src="{{ url("images/logo_mirai_bundar.png")}}" height="45px" style="margin-bottom: 6px;">&nbsp;<b>HR-Qu</b></span>
              @else
              <span style="font-size: 35px"><img src="{{ url("images/logo_mirai_bundar.png")}}" height="45px" style="margin-bottom: 6px;">&nbsp;<b>M I R A I</b></span>
              @endif
            </a>
          </div>
          <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
            <ul class="nav navbar-nav">
              <li>
                <a style="font-size: 20px; font-weight: bold;" class="text-yellow">
                  <?php if (isset($title)) {
                   echo $title;
                 } ?>
               </a>
             </li>
           </ul>
         </div>
        
      </nav>
    </header>
    <div class="content-wrapper" style="background-color: rgb(60,60,60); padding-top: 10px;">
      @yield('content')
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
  {{-- <script src="{{ url("bower_components/PACE/pace.min.js")}}"></script> --}}
  <script src="{{ url("dist/js/adminlte.min.js")}}"></script>
  <script src="{{ url("dist/js/demo.js")}}"></script>
  {{-- <script>$(document).ajaxStart(function() { Pace.restart(); });</script> --}}
  @yield('scripts')
</body>
</html>
