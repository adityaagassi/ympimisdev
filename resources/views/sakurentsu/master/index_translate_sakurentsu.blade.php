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
    {{ $title }} <span class="text-purple"> {{ $title_jp }}</span>
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
    <div class="col-xs-12">
      <div class="box box-solid">
        <div class="box-header">
          <center><h3 class="box-title">Sakurentsu</h3></center>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-xs-12">
              <table class="table table-bordered" style="width: 100%" id="master">
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th style="width: 1%">Sakurentsu Number</th>
                    <th>Title</th>
                    <th>Applicant</th>
                    <th style="width: 15%">Target Date</th>
                    <th style="width: 15%">Upload Date</th>
                    <!-- <th style="width: 1%">File</th> -->
                    <th style="width: 5%">Upload File(s)</th>
                  </tr>
                </thead>
                <tbody id="body_master"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xs-12">
      <div class="box box-solid">
        <div class="box-header">
          <center><h3 class="box-title">3M</h3></center>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-xs-12">
              <table class="table table-bordered" style="width: 100%" id="secondary">
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th style="width: 1%">Id</th>
                    <th>Title</th>
                    <th>Applicant</th>
                    <th style="width: 15%">Product Name</th>
                    <th style="width: 15%">Proccess Name</th>
                    <th style="width: 5%">Category</th>
                    <th style="width: 15%">Created At</th>
                    <th style="width: 5%">Translate</th>
                  </tr>
                </thead>
                <tbody id="body_secondary"></tbody>
              </table>
            </div>
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
    $('#master').DataTable().clear();
    $('#master').DataTable().destroy();
    $("#body_master").empty();

    $('#secondary').DataTable().clear();
    $('#secondary').DataTable().destroy();
    $("#body_secondary").empty();

    body = "";
    body_sec = "";
    $.get('{{ url("fetch/sakurentsu/translate") }}', function(result, status, xhr){
      // --------------------------------- SAKURENTSU ------------------------------------------
      $.each(result.datas, function(key, value) {   
        body += "<tr>";
        body += "<td>"+value.sakurentsu_number+"</td>";
        body += "<td>"+(value.title_jp || '')+"</td>";
        body += "<td>"+value.applicant+"</td>";
        body += "<td>"+value.tgl_target+"</td>";
        body += "<td>"+value.tgl_upload+"</td>";
        body += "<td><a href='"+"{{ url('index/sakurentsu/upload_sakurentsu_translate/') }}/"+value.id+"' class='btn btn-success btn-xs'> translate</a></td>";
        body += "</tr>";

        file.push({'sk_number' : value.sakurentsu_number, 'file' : value.file});
      })

      $("#body_master").append(body);

      var table = $('#master').DataTable({
        'dom': 'Bfrtip',
        'responsive':true,
        'lengthMenu': [
        [ 10, 25, 50, -1 ],
        [ '10 rows', '25 rows', '50 rows', 'Show all' ]
        ],
        'buttons': {
          buttons:[
          {
            extend: 'pageLength',
            className: 'btn btn-default',
          },
          ]
        },
        'paging': true,
        'lengthChange': true,
        'searching': true,
        'ordering': true,
        'info': true,
        'autoWidth': true,
        "sPaginationType": "full_numbers",
        "bJQueryUI": true,
        "bAutoWidth": false,
        "processing": true,
      });      



      // ---------------------------- 3M ----------------------

      $.each(result.tiga_em, function(key, value) {   
        body_sec += "<tr>";
        body_sec += "<td>"+value.id+"</td>";
        body_sec += "<td>"+value.title+"</td>";
        body_sec += "<td>"+value.applicant+"</td>";
        body_sec += "<td>"+value.product_name+"</td>";
        body_sec += "<td>"+value.proccess_name+"</td>";
        body_sec += "<td>"+value.category+"</td>";
        body_sec += "<td>"+value.created_at+"</td>";
        body_sec += "<td><a href='"+"{{ url('index/sakurentsu/3m/translate/') }}/"+value.id+"' class='btn btn-success btn-xs'> translate</a></td>";
        body_sec += "</tr>";

        file.push({'sk_number' : value.sakurentsu_number, 'file' : value.file});
      })

      $("#body_secondary").append(body_sec);

      var table = $('#secondary').DataTable({
        'dom': 'Bfrtip',
        'responsive':true,
        'lengthMenu': [
        [ 10, 25, 50, -1 ],
        [ '10 rows', '25 rows', '50 rows', 'Show all' ]
        ],
        'buttons': {
          buttons:[
          {
            extend: 'pageLength',
            className: 'btn btn-default',
          },
          ]
        },
        'paging': true,
        'lengthChange': true,
        'searching': true,
        'ordering': true,
        'info': true,
        'autoWidth': true,
        "sPaginationType": "full_numbers",
        "bJQueryUI": true,
        "bAutoWidth": false,
        "processing": true,
      }); 
    })
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