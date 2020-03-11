@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

  thead>tr>th{
    font-size: 16px;
  }

  input[type=number] {
    -moz-appearance:textfield; /* Firefox */
  }

  #loading { display: none; }

  .col-xs-2{
    padding-top: 5px;
  }
  .col-xs-3{
    padding-top: 5px;
  }
  .col-xs-5{
    padding-top: 5px;
  }
  .col-xs-6{
    padding-top: 5px;
  }
  .col-xs-7{
    padding-top: 5px;
  }
  .col-xs-8{
    padding-top: 5px;
  }

</style>
@stop
@section('header')
<section class="content-header">
  <h1>
    Create {{ $page }}
    <!-- <small>Create CPAR</small> -->
  </h1>
  <ol class="breadcrumb">
   {{--  <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="#">Examples</a></li>
    <li class="active">Blank page</li> --}}
  </ol>
</section>
@endsection
@section('content')
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
    <form role="form">
      <div class="box-body">
      	<input type="hidden" value="{{csrf_token()}}" name="_token" />
        
        <div class="row" align="left">
          <div class="col-xs-6 col-sm-6 col-md-6">
            <label for="tgl">Tanggal</label>
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control" placeholder="" value="<?= date('d F Y') ?>" disabled>
                <input type="hidden" class="form-control" id="cpar_tgl" name="cpar_tgl" placeholder="" value="<?= date('Y-m-d') ?>">
              </div>
          </div>
          <div class="col-xs-6 col-sm-6 col-md-6">
              <label for="subject">Identitas<span class="text-red">*</span></label>
              <input type="text" id="subject" class="form-control" value="{{$employee->employee_id}} - {{$employee->name}}" readonly>
              <input type="hidden" id="cpar_nik" class="form-control" value="{{$employee->employee_id}}" readonly>
          </div>
        </div>

        <div  class="row" align="left">
          <div class="col-xs-6 col-sm-6 col-md-6">
            <label for="subject">Kategori<span class="text-red">*</span></label>
             <select class="form-control select2" style="width: 100%;" id="cpar_kategori" name="cpar_kategori" data-placeholder="Pilih Kategori" required>
                <option></option>
                <option value="Critical">Critical (Berhubungan Dengan Safety, Fungsi Dan Ketidaksesuaian Dimensi/Design)</option>
                <option value="Major">Major (Berhubungan Dengan Visual pada Area yang langsung dapat terlihat)</option>
                <option value="Minor">Minor (Berhubungan Dengan Visual pada Area tidak langsung dapat terlihat)</option>
            </select>
          </div>
          <div class="col-xs-6 col-sm-6 col-md-6">
            <label for="subject">Judul Komplain<span class="text-red">*</span></label>
            <input type="text" class="form-control" name="cpar_judul" id="cpar_judul" placeholder="Judul Ketidaksesuaian" required="">
          </div>
        </div>

        <div class="row" align="left">
          <div class="col-xs-6 col-sm-6 col-md-6">
            <label for="section_from">Section From<span class="text-red">*</span></label>
            <select class="form-control select2" style="width: 100%;" id="cpar_secfrom" name="cpar_secfrom" data-placeholder="Pilih Section Pelapor" required>
                <option></option>
                @foreach($sections as $section)
                @if($section->group == null)
                <option value="{{ $section->department }}_{{ $section->section }}">{{ $section->department }} - {{ $section->section }}</option>
                @else
                <option value="{{ $section->section }}_{{ $section->group }}">{{ $section->section }} - {{ $section->group }}</option>
                @endif
                @endforeach
            </select>
          </div>
          <div class="col-xs-6 col-sm-6 col-md-6">
            <label for="section_to">Section To<span class="text-red">*</span></label>
            <select class="form-control select2" style="width: 100%;" id="cpar_secto" name="cpar_secto" data-placeholder="Pilih Section" required>
                <option></option>
                @foreach($sections as $section)
                @if($section->group == null)
                <option value="{{ $section->department }}_{{ $section->section }}">{{ $section->department }} - {{ $section->section }}</option>
                @else
                <option value="{{ $section->section }}_{{ $section->group }}">{{ $section->section }} - {{ $section->group }}</option>
                @endif
                @endforeach
            </select>
          </div>
        </div>

        <div class="row" align="left" style="padding-top: 10px">
          <div class="col-sm-4 col-sm-offset-5">
            <div class="btn-group">
              <a class="btn btn-danger" href="{{ url('index/cpar') }}">Cancel</a>
            </div>
            <div class="btn-group">
              <button type="button" class="btn btn-primary pull-right" id="form_submit"><i class="fa fa-edit"></i>&nbsp; Submit </button>
              <!-- <button type="submit" class="btn btn-primary col-sm-14">Submit</button> -->
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>

  @endsection

  @section('scripts')
  <script src="{{ url("js/jquery.gritter.min.js") }}"></script>

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

    $(function () {
      $('.select2').select2()
    });

    $("#form_submit").click( function() {
      $("#loading").show();

      if ($("#cpar_nik").val() == "") {
        $("#loading").hide();
        alert("NIK tidak ada");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#cpar_kategori").val() == "") {
        $("#loading").hide();
        alert("Kolom Kategori Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#cpar_judul").val() == "") {
        $("#loading").hide();
        alert("Kolom Judul Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#cpar_secfrom").val() == "") {
        $("#loading").hide();
        alert("Kolom Section Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#cpar_secto").val() == "") {
        $("#loading").hide();
        alert("Kolom Section Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      var data = {
        tanggal: $("#cpar_tgl").val(),
        employee_id: $("#cpar_nik").val(),
        kategori: $("#cpar_kategori").val(),
        judul: $("#cpar_judul").val(),
        secfrom: $("#cpar_secfrom").val(),
        secto: $("#cpar_secto").val()
      };

      $.post('{{ url("post/cpar/create") }}', data, function(result, status, xhr){
        $("#loading").hide();
        openSuccessGritter("Success","CPAR Antar Departemen Berhasil Dibuat");
        setTimeout(function(){  window.location = "{{url('index/cpar/detail')}}/"+result.datas; }, 1000);
      });
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

