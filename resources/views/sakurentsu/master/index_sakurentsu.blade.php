@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<style type="text/css">
  thead>tr>th{
    text-align:center;
  }
  tbody>tr>td{
    text-align:center;
  }
  tfoot>tr>th{
    text-align:center;
  }
  td:hover {
    overflow: visible;
  }
  table.table-bordered{
    border:1px solid black;
  }
  table.table-bordered > thead > tr > th{
    border:1px solid black;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid black;
    padding-top: 0;
    padding-bottom: 0;
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid rgb(211,211,211);
  }

  #table_trial_1 > tbody > tr > th, #table_trial_2 > tbody > tr > th{
    text-align: center;
    vertical-align: middle;
    border: 1px solid black;
    background-color: #a488aa;
  }

  #table_trial_1 > tbody > tr > td{
    padding: 0px;
  }
  #loading { display: none; }
</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    Sakurentsu <span class="text-purple"> {{ $title_jp }}</span>
  </h1>
  <ol class="breadcrumb">
  </ol>
</section>
@endsection

@section('content')
<section class="content">
  @if (session('success'))
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
    {{ session('success') }}
  </div>
  @endif
  @if (session('error'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Error!</h4>
    {{ session('error') }}
  </div>
  @endif

  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 45%; left: 35%;">
      <span style="font-size: 40px">Please wait a moment...<i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>

  <div class="row">
    <div class="col-xs-12" style="padding-right: 0">
      <div class="box box-solid">
        <div class="box-body">
          <div class="row">
            <div class="col-xs-12">
              <table class="table table-bordered" style="width: 100%" id="master">
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th style="width: 1%">Sakurentsu Number</th>
                    <th>Title</th>
                    <th>Applicant</th>
                    <th style="width: 3%">Target Date</th>
                    <th style="width: 3%">Upload Date</th>
                    <th style="width: 1%">File</th>
                    <th style="width: 3%">Translate Date</th>
                    <th style="width: 1%">Status</th>
                    <th style="width: 5%">Action</th>
                  </tr>
                </thead>
                <tbody id="body_master"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalFile">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="sk_num"></h4>
          <div class="modal-body table-responsive no-padding" style="min-height: 100px">
            <table class="table table-hover table-bordered table-striped" id="tableFile">
              <tbody id='bodyFile'></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  var file = [];

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");
    get_data();
  });

  function get_data() {
    $("#body_master").empty();

    body = "";
    $.get('{{ url("fetch/sakurentsu") }}', function(result, status, xhr){
      $.each(result.datas, function(key, value) {   
        body += "<tr>";
        body += "<td>"+value.sakurentsu_number+"</td>";
        body += "<td>"+(value.title || '')+"</td>";
        body += "<td>"+value.applicant+"</td>";
        body += "<td>"+value.target_date+"</td>";
        body += "<td>"+value.upload_date+"</td>";
        body += "<td>"+('<button class="btn btn-xs" onclick="getFileInfo('+key+',\''+value.sakurentsu_number+'\')"><i class="fa fa-paperclip"></i></button>' || '')+"</td>";
        body += "<td>"+(value.translate_date || '')+"</td>";
        body += "<td>"+value.status+"</td>";
        body += "<td>";
        if (value.status == 'approval') {
          body += "<a href='"+"{{ url('index/sakurentsu/detail/') }}/"+value.id+"'><button class='btn btn-xs btn-success'>Approve</button></a>";
        } else {
          body += "-";
        }
        body += "</td>";
        body += "</tr>";

        file.push({'sk_number' : value.sakurentsu_number, 'file' : value.file_translate});
      })
      $("#body_master").append(body);
    })
  }

  function getFileInfo(num, sk_num) {
    $("#sk_num").text(sk_num+" File(s)");
    
    $("#bodyFile").empty();

    body_file = "";
    $.each(file, function(key, value) {  
      if (sk_num == value.sk_number) {
        var obj = JSON.parse(value.file);
        var app = "";

        if (obj) {
          for (var i = 0; i < obj.length; i++) {
           body_file += "<tr>";
           body_file += "<td>";
           body_file += "<a href='../../uploads/sakurentsu/translated/"+obj[i]+"' target='_blank'><i class='fa fa-file-pdf-o'></i> "+obj[i]+"</a>";
           body_file += "</td>";
           body_file += "</tr>";
         }
       }
     }
   });

    $("#bodyFile").append(body_file);

    $("#modalFile").modal('show');
  }

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

  function openErrorGritter(title, message) {
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-danger',
      image: '{{ url("images/image-stop.png") }}',
      sticky: false,
      time: '2000'
    });
  }

  function openSuccessGritter(title, message){
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-success',
      image: '{{ url("images/image-screen.png") }}',
      sticky: false,
      time: '2000'
    });
  }

</script>

@stop