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

 <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
    <p style="position: absolute; color: White; top: 45%; left: 35%;">
      <span style="font-size: 40px">Loading, mohon tunggu . . . <i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>

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
     <div class="content-wrapper" style="background-color: #ecf0f5; padding-top: 0px;">
      <section class="content">
        @if($invest->posisi != "user")
        <form role="form" method="post" id="formNote" action="{{url('investment/comment/'.$invest->id)}}">
          <div class="col-xs-12 " style="text-align: center;" id="show">
            <h1><i class="fa fa-file-text-o"></i> {{ $invest->reff_number }}</h1>
            <p>
              <input type="hidden" value="{{csrf_token()}}" name="_token" />
              <div class="col-xs-12">
                <h2>Give Question To Applicant :</h2>
              </div>

              <div class="col-xs-12">
                <textarea class="form-control" id="question" name="question"></textarea>
                <input type="hidden" class="form-control" id="posisi" name="posisi" value="{{ $invest->posisi }}" required=""></textarea>
              </div>

              <div class="col-xs-12">
                  <br>
                  <button class="btn btn-success btn-lg" type="Submit">Submit & Send Email</button>
                  <a class="btn btn-danger btn-lg" type="button" onclick="reset();">Reset</a>
              </div>
            </p>
          </div>
        </form>
        @else
          <form role="form" method="post" id="formNote" action="{{url('investment/comment/'.$invest->id)}}">
          <div class="col-xs-12" style="text-align: center;" id="show">
            <h1><i class="fa fa-file-text-o"></i> {{ $invest->reff_number }}</h1>

            <p>
              <input type="hidden" value="{{csrf_token()}}" name="_token" />

              <div class="col-xs-12">
                <h2>Question : <br><br><?= $invest->comment_note ?></h2>
              </div>


              <div class="col-xs-12">
                <h2>Give Response :</h2>
              </div>

              <div class="col-xs-12">
                <textarea class="form-control" id="answer" name="answer"></textarea>
                <input type="hidden" class="form-control" id="posisi" name="posisi" value="{{ $invest->posisi }}" required=""></textarea>
              </div>

              <div class="col-xs-12">
                  <br>
                  <button class="btn btn-success btn-lg" type="Submit">Submit & Send Email</button>
                  <a class="btn btn-danger btn-lg" type="button" onclick="resetanswer();">Reset</a>
              </div>
            </p>
          </div>
        </form>
        @endif
      </section>
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
  <script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
  {{-- <script src="{{ url("bower_components/PACE/pace.min.js")}}"></script> --}}
  <script src="{{ url("dist/js/adminlte.min.js")}}"></script>
  <script src="{{ url("dist/js/demo.js")}}"></script>
  {{-- <script>$(document).ajaxStart(function() { Pace.restart(); });</script> --}}
  @yield('scripts')


  <script type="text/javascript">
      
      $('#show').show();
      $('#hide').hide();

      $("#formNote").submit(function(){
        $("#loading").show();
        this.submit();
      });

      function ShowHide(){
        $('#show').hide();
        $('#hide').show();
      }

      function reset(){
          $("#question").html(CKEDITOR.instances.question.setData(""));
      }

      function resetanswer(){
          $("#answer").html(CKEDITOR.instances.question.setData(""));
      }

      CKEDITOR.replace('question' ,{
        filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
        height: '250px'
      });

      CKEDITOR.replace('answer' ,{
        filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
        height: '250px'
      });

  </script>
</body>
</html>
