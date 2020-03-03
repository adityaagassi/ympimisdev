@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  .col-xs-8{
    padding-top: 5px;
  }
  .col-xs-2{
    padding-top: 5px;
  }
  .col-xs-6{
    padding-top: 5px;
  }
  .col-xs-10{
    padding-top: 5px;
  }
</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    Edit {{ $page }}
    <small>Submit Your Experience Here</small>
  </h1>
  <ol class="breadcrumb">
   {{--  <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="#">Examples</a></li>
    <li class="active">Blank page</li> --}}
  </ol>
</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
  @if ($errors->has('password'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
    {{ $errors->first() }}
  </div>   
  @endif
  @if (session('error'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Error!</h4>
    {{ session('error') }}
  </div>   
  @endif

  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
    <p style="position: absolute; color: White; top: 45%; left: 35%;">
      <span style="font-size: 40px">Loading, mohon tunggu . . . <i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary">
    <div class="box-header with-border">
      {{-- <h3 class="box-title">Create New CPAR</h3> --}}
    </div>  
    <form role="form" method="post" action="{{url('index/qc_report/create_action')}}" enctype="multipart/form-data">
      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="row">
          <div class="col-xs-6 col-sm-6 col-md-6">
            <label for="form_tgl">Tanggal</label>
            <input type="text" id="form" class="form-control" value="{{ $form_failures->tanggal }}" readonly>
            <!-- <input type="hidden" id="form_tgl" class="form-control" value="{{ date('Y-m-d')}}" readonly> -->
          </div>
          <div class="col-xs-6 col-sm-6 col-md-6">
            <label for="form_nik">NIK</label>
            <input type="text" id="form_nik" class="form-control" value="{{$form_failures->employee_id}}" readonly>
          </div>
          <div class="col-xs-6 col-sm-6 col-md-6">
            <label for="form_nama">Nama</label>
            <input type="text" id="form_nama" class="form-control" value="{{$form_failures->employee_name}}" readonly>
          </div>
          <div class="col-xs-6 col-sm-6 col-md-6">
            <label for="form_ket">Section - Department</label>
            <input type="text" id="form_ket" class="form-control" value="{{$form_failures->section}} - {{$form_failures->department}}" readonly>
            <input type="hidden" id="form_sec" class="form-control" value="{{$form_failures->section}}" readonly>
            <input type="hidden" id="form_dept" class="form-control" value="{{$form_failures->department}}" readonly>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-2">
            <label for="form_kategori">Kategori</label>
            <select class="form-control select2" id="form_kategori" data-placeholder='Pilih Kategori'>
              <option value="">&nbsp;</option>
              <option <?php if($form_failures->kategori == "Permasalahan") echo "selected"; ?>>Permasalahan</option>
              <option <?php if($form_failures->kategori == "Kegagalan") echo "selected"; ?>>Kegagalan</option>
            </select>
          </div>
          <div class="col-xs-10">
            <label for="form_judul">Judul Permasalahan / Kegagalan</label>
            <input type="text" id="form_judul" class="form-control" placeholder="Judul Permasalahan / Kegagalan" value="{{$form_failures->judul}}">
          </div>
        </div>
        
        <div class="row">
          <div class="col-xs-6">
            <label for="form_penyebab">Penyebab Permasalahan</label>
            <textarea class="form-control" id="form_penyebab">{{$form_failures->penyebab}}</textarea>
          </div>
          <div class="col-xs-6">
            <label for="form_perbaikan">Penanganan / Perbaikan Yang Dilakukan</label>
            <textarea class="form-control" id="form_perbaikan">{{$form_failures->penanganan}}</textarea>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-6 col-sm-offset-3">
            <label for="form_tindakan">Tindakan Supaya Tidak Terjadi Lagi</label>
            <textarea class="form-control" id="form_tindakan">{{$form_failures->tindakan}}</textarea>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-4 col-sm-offset-5" style="padding-top: 10px">
            <div class="btn-group">
              <a class="btn btn-danger" href="{{ url('index/form_experience') }}">Cancel</a>
            </div>
            <div class="btn-group">
              <button type="button" class="btn btn-primary pull-right" id="form_submit"><i class="fa fa-edit"></i>&nbsp; Submit </button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>

  @endsection

  @section('scripts')
  <script src="{{ url("js/jquery.gritter.min.js") }}"></script>
  <script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $("body").on("click",".btn-danger",function(){ 
          $(this).parents(".control-group").remove();
      });
    });

</script>
  <script>
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    }); 

    jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");
    $("#navbar-collapse").text('');
      $('.select2').select2({
        language : {
          noResults : function(params) {
            return "There is no cpar with status 'close'";
          }
        }
      });
    });

    $(function () {
      $('.select2').select2()
    });

    $("#form_submit").click( function() {
      $("#loading").show();

      if ($("#form_judul").val() == "") {
        $("#loading").hide();
        alert("Kolom Judul Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#form_kategori").val() == "") {
        $("#loading").hide();
        alert("Kolom Kategori Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      var data = {
        id: "{{ $form_failures->id }}",
        employee_id: $("#form_nik").val(),
        employee_name: $("#form_nama").val(),
        tanggal: $("#form_tgl").val(),
        kategori: $("#form_kategori").val(),
        section: $("#form_sec").val(),
        department: $("#form_dept").val(),
        judul: $("#form_judul").val(),
        penyebab: CKEDITOR.instances.form_penyebab.getData(),
        penanganan: CKEDITOR.instances.form_perbaikan.getData(),
        tindakan: CKEDITOR.instances.form_tindakan.getData(),
      };

      $.post('{{ url("index/update/form_experience") }}', data, function(result, status, xhr){
        $("#loading").hide();
        openSuccessGritter("Success","Berhasil Diedit");
        // window.history.go(-1);
        setTimeout(function(){ window.history.back(); }, 2000);
      });

    });

    CKEDITOR.replace('form_penyebab' ,{
      filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}'
    });

    CKEDITOR.replace('form_perbaikan' ,{
      filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}'
    });

    CKEDITOR.replace('form_tindakan' ,{
      filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}'
    });


    function openSuccessGritter(title, message){
      jQuery.gritter.add({
        title: title,
        text: message,
        class_name: 'growl-success',
        image: '{{ url("images/image-screen.png") }}',
        sticky: false,
        time: '3000'
      });
    }
  </script>
@stop

