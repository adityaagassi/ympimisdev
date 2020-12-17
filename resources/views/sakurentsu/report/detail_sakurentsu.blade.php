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
    <div class="col-xs-6" style="padding-right: 0">
      <div class="box box-solid">
        <div class="box-header">
          <h3 class="box-title"><span class="text-purple">Detail Sakurentsu</span></h3>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-xs-12">
              <form class="form-horizontal">
                <div class="box-body">
                  <div class="form-group">
                    <label for="sk_number" class="col-sm-4 control-label">Sakuretsu Number</label>

                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="sk_number" readonly="">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="sk_title" class="col-sm-4 control-label">Title</label>

                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="sk_title" readonly="">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="sk_target" class="col-sm-4 control-label">Target Date</label>

                    <div class="col-sm-4">
                      <input type="text" class="form-control" id="sk_target" readonly="">
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="sk_translate" class="col-sm-4 control-label">Translate Date</label>

                    <div class="col-sm-4">
                      <input type="text" class="form-control" id="sk_translate" readonly="">
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-4 control-label">File</label>

                    <div class="col-sm-8" id="sk_file">

                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-4 control-label">Sakurentsu Type<span class="text-red">*</span></label>

                    <div class="col-sm-8">
                      <select class="select2" data-placeholder="Select Type of Sakurentsu" id="select_form" style="width: 100%">
                        <option value=""></option>
                        <option value="Trial Request">Trial Request</option>
                        <option value="3M">3M</option>
                        <option value="Information">Information</option>
                        <option value="Not Related">Not Related</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-4 control-label">Related Department<span class="text-red">*</span></label>

                    <div class="col-sm-8">
                      <select data-placeholder="Select Related Department" id="select_dept_form" style="width: 100%" multiple="">
                        <option value=""></option>
                        @foreach($depts as $dept)
                        <option value="{{ $dept->department_name }}">{{ $dept->department_name }}</option>
                        @endforeach                        
                      </select>
                    </div>
                  </div>

                  <button type="button" class="btn btn-success pull-right" onclick="submit_sk()"><i class="fa fa-check"></i> ACCEPT</button>
                </div>
              </form>
            </div>

          </div>
        </div>
      </div>
    </div>
    <div class="col-xs-6" style="padding: 0">
      <div class="col-xs-12">
        <div class="box box-solid">
          <div class="box-header">
            <h3 class="box-title"><span class="text-purple">Detail Sakurentsu (Original Version)</span></h3><br>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-xs-12">
                <form class="form-horizontal">
                  <div class="box-body">
                    <div class="form-group">
                      <label for="sk_number_jp" class="col-sm-4 control-label">Sakuretsu Number</label>

                      <div class="col-sm-8">
                        <input type="text" class="form-control" id="sk_number_jp" readonly="">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="sk_title_jp" class="col-sm-4 control-label">Title</label>

                      <div class="col-sm-8">
                        <input type="text" class="form-control" id="sk_title_jp" readonly="">
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="sk_upload" class="col-sm-4 control-label">Upload Date</label>

                      <div class="col-sm-4">
                        <input type="text" class="form-control" id="sk_upload" readonly="">
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-4 control-label">File</label>

                      <div class="col-sm-8" id="sk_file_jp">

                      </div>
                    </div>
                  </div>
                </form>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- <div class="col-xs-12" style="padding: 0">
      <div class="col-xs-12">
        <div class="box box-solid">
          <div class="box-header">
            <h3 class="box-title" id="form_title">Form</h3><br>
          </div>
          <div class="box-body">

            <h4 style="font-weight: bold;">I. PENGAJUAN TRIAL</h4>
            <form class="form-horizontal">
              <label>Kepada Yth.</label>
              <div class="row">
                <div class="col-xs-4">
                  <div class="form-group">
                    <label for="name" class="col-sm-4 control-label">Nama</label>

                    <div class="col-sm-8">
                      <select class="select2" id="select_nama" data-placeholder="Pilih Nama" style="width: 100%">
                        <option value=""></option>
                        <option value="PI2002021 - Muhammad Nasiqul Ibat">Muhammad Nasiqul Ibat</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="department" class="col-sm-4 control-label">Departemen</label>

                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="department" readonly="">
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="subSection" class="col-sm-4 control-label">Sub Section</label>

                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="subSection" readonly="">
                    </div>
                  </div>
                </div>

                <div class="col-xs-4">
                  <div class="form-group">
                    <label for="material" class="col-sm-4 control-label">Nama APD / Material</label>

                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="material">
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="request_date" class="col-sm-4 control-label">Tanggal Pengajuan</label>

                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="request_date">
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="trial_date" class="col-sm-4 control-label">Tanggal Trial</label>

                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="trial_date">
                    </div>
                  </div>
                </div>

                <div class="col-xs-4">
                  <div class="form-group">
                    <label for="ref_number" class="col-sm-4 control-label">No. Referensi</label>

                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="ref_number">
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="qty_material" class="col-sm-4 control-label">Total APD / Material</label>

                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="qty_material">
                    </div>
                  </div>
                </div>

                <div class="col-xs-12">
                  <table style="width: 100%" class="table table-bordered" id="table_trial_1">
                    <tr>
                      <th colspan="2">KONDISI</th>
                      <th rowspan="2" style="width: 40%">TUJUAN TRIAL</th>
                    </tr>
                    <tr>
                      <th style="width: 30%">SEBELUMNYA</th>
                      <th style="width: 30%">TRIAL</th>
                    </tr>
                    <tr>
                      <td><textarea class="form-control" id="sebelum" placeholder="Masukkan kondisi sebelum trial"></textarea></td>
                      <td><textarea class="form-control" id="trial" placeholder="Masukkan kondisi trial"></textarea></td>
                      <td><textarea class="form-control" id="trial_purpose" placeholder="Masukkan tujuan trial"></textarea></td>
                    </tr>
                  </table>

                  <a href="#" class="btn btn-primary btn-lg" style="width: 100%;margin-bottom: 10px" data-toggle="modal" data-target="#modalCreate"><b><i class="fa fa-plus"></i> Tambah Material</b></a>

                  <table style="width: 100%" class="table table-bordered" id="table_trial_2">
                    <tr>
                      <th>No.</th>
                      <th>Nama Material</th>
                      <th>Jumlah</th>
                      <th>Lokasi / Area Trial</th>
                      <th>Keterangan / Spesifikasi</th>
                    </tr>
                  </table>
                </div>
              </div>
            </form>            
          </div>
        </div>
      </div>
    </div> -->
  </div>

  <div class="modal fade" id="modalCreate">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title" id="modalDetailTitle"></h4>
          <div class="modal-body table-responsive no-padding" style="min-height: 100px">
            <center>
              <i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
            </center>
            <table class="table table-hover table-bordered table-striped" id="tableModal">
              <thead style="background-color: rgba(126,86,134,.7);">
                <tr>
                  <th>Material</th>
                  <th>Description</th>
                  <th>Loc</th>
                  <th>PI</th>
                  <th>Book</th>
                  <th>Diff</th>
                  <th>Diff Abs</th>
                </tr>
              </thead>
              <tbody id="modalDetailBody">
              </tbody>
              <tfoot style="background-color: RGB(252, 248, 227);">
                <th>Total</th>
                <th></th>
                <th></th>
                <th id="modalDetailTotal1"></th>
                <th id="modalDetailTotal2"></th>
                <th id="modalDetailTotal3"></th>
                <th id="modalDetailTotal4"></th>
              </tfoot>
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

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");

    getDatas();

    $('.select2').select2();
    $('#select_dept_form').select2();

    $("#select_form").val("").trigger("change");
    $("#select_dept_form").val("").trigger("change");

    // CKEDITOR.replace('sebelum' ,{
    //   filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    // });

    // CKEDITOR.replace('trial' ,{
    //   filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    // });

    // CKEDITOR.replace('trial_purpose' ,{
    //   filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    // });
  });

  
  $("#select_form").on("change",function(){
   $("#form_title").text(this.value+" Form");
 });

  function getDatas() {
    var id = "{{ Request::segment(4) }}";

    var data = {
      id : id
    }

    $.get('{{ url("fetch/sakurentsu/type/") }}', data, function(result, status, xhr){
      var obj = JSON.parse(result.datas.file_translate);
      var app = "";

      $.each(obj, function(key, value) {          
        app += "<a href='"+'{{ url("uploads/sakurentsu/translated/") }}'+"/"+value+"' target='_blank'><i class='fa fa-file-pdf-o'></i> "+value+"</a><br>";
      })

      var obj2 = JSON.parse(result.datas.file);
      var app2 = "";

      $.each(obj2, function(key, value) {          
        app2 += "<a href='"+'{{ url("uploads/sakurentsu/") }}'+"/"+value+"' target='_blank'><i class='fa fa-file-pdf-o'></i> "+value+"</a><br>";
      })

      $("#sk_number").val(result.datas.sakurentsu_number);
      $("#sk_title").val(result.datas.title);
      $("#sk_target").val(result.datas.target_date);
      $("#sk_upload").val(result.datas.upload_date);
      $("#sk_translate").val(result.datas.translate_date);
      $("#sk_file").append(app);

      $("#sk_number_jp").val(result.datas.sakurentsu_number);
      $("#sk_title_jp").val(result.datas.title_jp);
      $("#sk_file_jp").append(app2);
    })
    
  }

  function submit_sk() {
    var cat = $("#select_form").val();
    var dept = $("#select_dept_form").val();
    var sort_dept = $('#select_dept_form').select2('data').text;
    var sk_number = $("#sk_number").val();

    var data = {
      sk_number : sk_number,
      ctg : cat,
      dept : dept,
      sort_dept : sort_dept
    }

    $.post('{{ url("post/sakurentsu/type") }}', data, function(result, status, xhr){
      if (result.status) {
        openSuccessGritter('Success', '');
        $("#select_form").val("").trigger("change");
        $("#select_dept_form").val("").trigger("change");
      } else {
        openErrorGritter('Error', result.message);
      }
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