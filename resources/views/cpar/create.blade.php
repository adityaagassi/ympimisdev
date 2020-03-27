@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

  thead>tr>th{
    font-size: 16px;
  }

    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
      /* display: none; <- Crashes Chrome on hover */
      -webkit-appearance: none;
      margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
    }

    input[type=number] {
      -moz-appearance:textfield; /* Firefox */
    }
    input[type="radio"] {
    }

    #loading { display: none; }


    .radio {
      display: inline-block;
      position: relative;
      padding-left: 25px;
      margin-bottom: 12px;
      cursor: pointer;
      font-size:    ;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
    }

    /* Hide the browser's default radio button */
    .radio input {
      position: absolute;
      opacity: 0;
      cursor: pointer;
    }

    /* Create a custom radio button */
    .checkmark {
      position: absolute;
      top: 0;
      left: 0;
      height: 20px;
      width: 20px;
      background-color: #ccc;
      /*border-radius: 50%;*/
    }

    /* On mouse-over, add a grey background color */
    .radio:hover input ~ .checkmark {
      background-color: #ccc;
    }

    /* When the radio button is checked, add a blue background */
    .radio input:checked ~ .checkmark {
      background-color: #2196F3;
    }

    /* Create the indicator (the dot/circle - hidden when not checked) */
    .checkmark:after {
      content: "";
      position: absolute;
      display: none;
    }

    /* Show the indicator (dot/circle) when checked */
    .radio input:checked ~ .checkmark:after {
      display: block;
    }

    /* Style the indicator (dot/circle) */
    .radio .checkmark:after {
      top: 7px;
      left: 7px;
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: white;
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
    Buat {{ $page }}
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
  <div class="box box-primary" style="margin-bottom: 10px">
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
              <input type="hidden" id="cpar_pos" class="form-control" value="{{$employee->position}}" readonly>
          </div>
        </div>

        <div  class="row" align="left">
          <div class="col-xs-6 col-sm-6 col-md-6">
            <label for="subject">Kategori Komplain<span class="text-red">*</span></label>
             <select class="form-control select2" style="width: 100%;" id="cpar_kategori" name="cpar_kategori" data-placeholder="Pilih Kategori" onchange="selectkomplain()" required>
                <option></option>
                <option value="Service">Service</option>
                <option value="Kualitas">Kualitas</option>
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
            <select class="form-control select2" style="width: 100%;" id="cpar_secfrom" name="cpar_secfrom" data-placeholder="Pilih Section Pelapor" required readonly="">
                @foreach($secfrom as $sec)
                @if($sec->group == null)
                <option value="{{ $sec->department }}_{{ $sec->section }}">{{ $sec->department }} - {{ $sec->section }}</option>
                @else
                <option value="{{ $sec->group }}_{{ $sec->section }}">{{ $sec->section }} - {{ $sec->group }}</option>
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
                <option value="{{ $section->group }}_{{ $section->section }}">{{ $section->section }} - {{ $section->group }}</option>
                @endif
                @endforeach
            </select>
          </div>
        </div>

        <div class="row" align="left" id="kat_komplain" style="margin-top: 15px">
          <div class="col-xs-12 col-sm-12 col-md-12" >
            <!-- <label for="Audit">Kualifikasi Form Ke QA<span class="text-red">*</span></label> -->
            <span class="pull-left" style="font-weight: bold; background-color: yellow; color: rgb(255,0,0);">&#8650; Form Ketidaksesuaian Kualitas akan dikirim Ke QA Jika Terdapat Salah Satu kondisi Dibawah. Jika tidak, akan dikirim ke departemen bersangkutan&nbsp;&nbsp;&#8650;</span><br><br>
            <div style="height: 40px;padding-right: 10px;padding-left: 10px;margin-bottom: 10px">       
              <span style="vertical-align: middle;line-height: 20px">
                <b><i class="fa fa-arrow-right"></i> Temuan NG Jelas</b>
              </span>
              <label class="radio pull-right" style="margin-top: 5px;right: 0;position: relative">Tidak
                <input type="radio" checked="checked" id="keterangan1" name="keterangan1" value="0">
                <span class="checkmark"></span>
              </label>
              &nbsp;&nbsp;
              <label class="radio pull-right" style="margin-top: 5px;right: 0;position: relative;margin-left: 5px;margin-right: 10px">Iya
                <input type="radio" id="keterangan1" name="keterangan1" value="1">
                <span class="checkmark"></span>
              </label>
              <br>
              Contoh : Zagane Lepas, Kunci Belum Solder, Salah Pasang Small Cover, Bari Recorder, Reed Pianica Tidak Keluar
              <br><br>
            </div>
            <div style="height: 60px;padding-right: 10px;padding-left: 10px;">       
              <span style="vertical-align: middle;line-height: 20px;">
                <b><i class="fa fa-arrow-right"></i> Temuan NG Memerlukan Recheck FSTK atau Repair "Off line"</b>
              </span>
              <label class="radio pull-right" style="margin-top: 5px;right: 0;position: relative">Tidak
                <input type="radio" checked="checked" id="keterangan2" name="keterangan2" value="0">
                <span class="checkmark"></span>
              </label>
              &nbsp;&nbsp;
              <label class="radio pull-right" style="margin-top: 5px;right: 0;position: relative;margin-left: 5px;margin-right: 10px">Iya
                <input type="radio" id="keterangan2" name="keterangan2" value="1">
                <span class="checkmark"></span>
              </label>
              <br>
              (Repair khusus diluar repair normal yang memelukan man power khusus atau waktu tambahan)<br>
              Contoh : FL C-6 Siage Aus, Sax Ligature Screw Nami Senban
            </div>
            <!-- <span class="pull-left" style="font-weight: bold; background-color: yellow; color: rgb(255,0,0);">&nbsp;&nbsp;&nbsp;&nbsp;Note :&nbsp;&nbsp;&nbsp;</span><br> -->
              

              
          </div>  
        </div>
                
        <div class="row" align="left" style="padding-top: 30px">
          <div class="col-sm-4 col-sm-offset-5">
            <div class="btn-group">
              <a class="btn btn-danger" href="{{ url('index/form_ketidaksesuaian') }}">Cancel</a>
            </div>
            <div class="btn-group">
              <button type="button" class="btn btn-primary pull-right" id="form_submit"><i class="fa fa-edit"></i>&nbsp; Submit </button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>

  

  <div class="box box-primary" style="border-top-color: orange" id="noteimportant">
      <input type="hidden" value="{{csrf_token()}}" name="_token" />
      <div class="callout callout" style="background-color: #fbc02d;border-left: 0;color: black">
        <h4><i class="fa fa-bullhorn"></i> Catatan!</h4>
        <p>
          <b>Kategori Komplain Service</b> merupakan komplain yang berhubungan dengan pelayanan antar departemen, Meliputi <b>Keterlambatan Material dan Kesalahan Pelayanan</b>. Contoh : 
          <br>&emsp; - Kesalahan Pelayanan Material Dari Gudang ke Produksi 
          <br>&emsp; - Keterlambatan Material Dari Vendor
          <br>&emsp; - Jig dari Workshop Tidak Sesuai Dengan WJO
          <br>&emsp; - Material Indirect yang Datang Tidak Sesuai Dengan PO
        </p>
        <p><b>Kategori Komplain Kualitas</b> merupakan komplain yang berhubungan ketidaksesuaian kualitas material atau proses produksi. Contoh: 
          <br>&emsp; - 
          <br>&emsp; - 

        </p>

        <!-- sesuai dengan standar dan ketentuan dari masing - masing departemen.</p> -->

    </div>
  </div>

  @endsection

  @section('scripts')
  <script src="{{ url("js/jquery.gritter.min.js") }}"></script>

  <script type="text/javascript">
    $(document).ready(function() {
      $("body").on("click",".btn-danger",function(){ 
          $(this).parents(".control-group").remove();
      });

      $("#kat_komplain").hide();


      
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

      var komplain = document.getElementById("cpar_kategori");
      var getkomplain = komplain.options[komplain.selectedIndex].value;
      var kate;

      if (getkomplain == "Kualitas"){
        
        var ket1 = document.querySelector('input[name="keterangan1"]:checked').value;
        var ket2 = document.querySelector('input[name="keterangan2"]:checked').value;

        if (ket1 == "1" || ket2 == "1") {
          kate = "qa";
        } else {
          kate = "";
        }

      }else{
        kate = "";
      }

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
        position: $("#cpar_pos").val(),
        kategori: $("#cpar_kategori").val()+kate,
        judul: $("#cpar_judul").val(),
        secfrom: $("#cpar_secfrom").val(),
        secto: $("#cpar_secto").val()
      };

      $.post('{{ url("post/form_ketidaksesuaian/create") }}', data, function(result, status, xhr){
        if(result.status){   
          $("#loading").hide();
          openSuccessGritter("Success","CPAR Berhasil Dibuat");
          setTimeout(function(){  window.location = "{{url('index/form_ketidaksesuaian/detail')}}/"+result.datas; }, 1000);
        }
        else{
          openErrorGritter('Error!', result.message);
        }
      });
    });

    function selectkomplain() {
      var komplain = document.getElementById("cpar_kategori");
      var getkomplain = komplain.options[komplain.selectedIndex].value;
    
      if (getkomplain == "Kualitas"){
        $("#kat_komplain").show();

      }else{
        $("#kat_komplain").hide();
      }
    }


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

  </script>
@stop

