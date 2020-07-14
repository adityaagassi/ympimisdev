@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  
  .col-xs-2 {
    padding-top: 5px;
  }
  .col-xs-10 {
    padding-top: 5px;
  }
  .col-xs-3 {
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
  input[type=checkbox] {
    transform: scale(1.5);
  }

</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    Edit {{ $page }}
    <small><b>E</b>lectronic-<b>I</b>nternal <b>R</b>equest <b>C</b>orrective <b>A</b>ction</small>

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
      {{-- <h3 class="box-title">Create New</h3> --}}
    </div>  
    <form role="form">
      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="row">
          <div class="col-xs-6 col-sm-6 col-md-6">
              <label for="form_identitas">Auditor / Penemu Masalah</label>
              <input type="text" id="form_identitas" class="form-control" value="{{$audits->auditor}} - {{$audits->auditor_name}}" readonly>
              <input type="hidden" id="auditor" class="form-control" value="{{$audits->auditor}}" readonly>
              <input type="hidden" id="auditor_name" class="form-control" value="{{$audits->auditor_name}}" readonly>
          </div>
          <div class="col-xs-2 col-sm-2 col-md-2">
            <label for="auditor_tgl">Tanggal Terbit</label>
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control datepicker" id="auditor_tgl" placeholder="Masukkan Tanggal Kejadian" value="<?= date('d F Y', strtotime($audits->auditor_date)) ?>" readonly="">
            </div>
          </div>
          <div class="col-xs-2 col-sm-2 col-md-2">
            <label for="auditor_jenis">Jenis</label>
            <input type="text" class="form-control" id="auditor_jenis" placeholder="Masukkan Kategori" value="<?= $audits->auditor_jenis ?>" readonly="">          
          </div>
          <div class="col-xs-2 col-sm-2 col-md-2">
            <label for="auditor_lokasi">Lokasi</label>
            <input type="text" class="form-control" id="auditor_lokasi" placeholder="Masukkan Lokasi" value="<?= $audits->auditor_lokasi ?>" readonly="">          
          </div>
        </div>

        <div class="row">
          <div class="col-xs-2">
            <label for="auditor_kategori">Kategori</label>
            <select class="form-control select2" name="auditor_kategori" id="auditor_kategori" style="width: 100%;" data-placeholder="Pilih Kategori" required>
              <option value=""></option>
              <option value="ISO 9001" <?php if($audits->auditor_kategori == "ISO 9001") echo "selected"; ?>>ISO 9001</option>
              <option value="ISO 14001" <?php if($audits->auditor_kategori == "ISO 14001") echo "selected"; ?>>ISO 14001</option>
              <option value="ISO 45001" <?php if($audits->auditor_kategori == "ISO 45001") echo "selected"; ?>>ISO 45001</option>
            </select>
          </div>
          <div id="syarat">
            <div class="col-xs-2">
              <label for="auditor_persyaratan">Persyaratan</label>
              <div class="input-group" style="margin-top: 5px">
                <label class="checkbox-inline">
                  <input type="checkbox" class="auditor_persyaratanCheckbox" name="auditor_persyaratan" value="1" id="auditor_persyaratan" 
                  <?php $auditor = explode(',',$audits->auditor_persyaratan);
                    foreach ($auditor as $key) {
                      if ($key == 1) {
                        echo 'checked';
                      }
                    }?>>Prosedur (Manual)
                </label>
              </div>
            </div>
            <div class="col-xs-2">
              <label for="auditor_persyaratan"></label>
              <div class="input-group" style="margin-top: 10px">
                <label class="checkbox-inline">
                  <input type="checkbox" class="auditor_persyaratanCheckbox" name="auditor_persyaratan" value="2" id="auditor_persyaratan"
                  <?php foreach ($auditor as $key) {
                      if ($key == 2) {
                        echo 'checked';
                      }
                    }?>>Standart Produk/Spesifikasi
                </label>
              </div>
            </div>
            <div class="col-xs-2">
              <label for="auditor_persyaratan"></label>
              <div class="input-group" style="margin-top: 10px">
                <label class="checkbox-inline">
                  <input type="checkbox" class="auditor_persyaratanCheckbox" name="auditor_persyaratan" value="3" id="auditor_persyaratan"
                  <?php foreach ($auditor as $key) {
                      if ($key == 3) {
                        echo 'checked';
                      }
                    }?>>Persyaratan Pelanggan
              </div>
            </div>
            <div class="col-xs-2">
              <label for="auditor_persyaratan"></label>
              <div class="input-group" style="margin-top: 10px">
                <label class="checkbox-inline">
                  <input type="checkbox" class="auditor_persyaratanCheckbox" name="auditor_persyaratan" value="4" id="auditor_persyaratan"
                  <?php foreach ($auditor as $key) {
                      if ($key == 4) {
                        echo 'checked';
                      }
                    }?>>Keputusan Top Manajemen
              </div>
            </div>
            <div class="col-xs-2">
              <label for="auditor_persyaratan"></label>
              <div class="input-group" style="margin-top: 10px">
                <label class="checkbox-inline">
                  <input type="checkbox" class="auditor_persyaratanCheckbox" name="auditor_persyaratan" value="5" id="auditor_persyaratan"
                  <?php foreach ($auditor as $key) {
                      if ($key == 5) {
                        echo 'checked';
                      }
                    }?>>Peraturan
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-xs-6">
            <label for="auditor_permasalahan">Uraian Permasalahan</label>
            <textarea class="form-control" id="auditor_permasalahan">{{$audits->auditor_permasalahan}}</textarea>
          </div>
          <div class="col-xs-6">
            <label for="auditor_penyebab">Penyebab Permasalahan</label>
            <textarea class="form-control" id="auditor_penyebab">{{$audits->auditor_penyebab}}</textarea>
          </div>
        </div>
        
        <div class="row">
          <div class="col-xs-6">
            <label for="auditor_bukti">Bukti Temuan (Yang Mendukung Uraian Permasalahan)</label>
            <textarea class="form-control" id="auditor_bukti">{{$audits->auditor_bukti}}</textarea>
          </div>

          <div class="col-xs-6">
            <label for="auditee">Pilih Auditee</label>
              <select class="form-control select2" data-placeholder="Pilih Karyawan" name="auditee" id="auditee" style="width: 100% height: 35px; font-size: 15px;" onchange="selectemployee()" required>
                  <option value=""></option>
                  @foreach($leaders as $leader)
                  @if($leader->employee_id == $audits->auditee)
                  <option value="{{ $leader->employee_id }}" selected="">{{ $leader->employee_id }} - {{ $leader->name }} - {{ $leader->section }}</option>
                  @else
                  <option value="{{ $leader->employee_id }}">{{ $leader->employee_id }} - {{ $leader->name }} - {{ $leader->section }}</option>
                  @endif
                  @endforeach
              </select>

              <input type="hidden" class="form-control" name="auditee_name" id="auditee_name" placeholder="auditee_name" value="{{$audits->auditee_name}}" required>

            <br><br>

            <label for="auditee">Due Date</label>
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control datepicker2" id="auditee_due_date" placeholder="Masukkan Due Date" value="{{$audits->auditee_due_date}}">
            </div>

            <br>

            <label for="auditee">Nomor Audit CPAR</label>
              <input type="text" class="form-control" id="audit_no" placeholder="Audit CPAR" value="{{$audits->audit_no}}" required readonly>
            </div>

          </div>
        </div>

        @if($audits->posisi != "auditor_final")

        <div class="row" style="padding: 20px">
          <div class="col-sm-4 col-sm-offset-5" style="padding-top: 5px">
            <div class="btn-group">
              <a class="btn btn-danger" href="{{ url('index/audit_iso') }}">Cancel</a>
            </div>
            <div class="btn-group">
              <button type="button" class="btn btn-primary pull-right" id="form_submit"><i class="fa fa-edit"></i>&nbsp; Save </button>
            </div>
            @if($audits->posisi == "auditor" && $audits->status == "commended")
              <div class="btn-group">
                <button class="btn btn-md btn-success pull-right" data-toggle="tooltip" title="Send Email" style="margin-right:5px;"  onclick="sendEmail({{$audits->id}})"><i class="fa fa-envelope"></i> Kirim Email Ke Standarisasi</button>
              </div>
              @endif
          </div>
        </div>


        @elseif($audits->posisi == "auditor_final")

        <hr>

        <div class="box-body">

          <div class="row">
            <div class="col-xs-6">
              <label for="auditor_catatan">Catatan Efektifitas Penyelesaian Masalah</label>
              <textarea class="form-control" id="auditor_catatan">{{$audits->auditor_catatan}}</textarea>
            </div>
            <div class="col-xs-6">
              <label for="auditor_manfaat">Manfaat Bagi Perusahaan</label>
              <textarea class="form-control" id="auditor_manfaat">{{$audits->auditor_manfaat}}</textarea>
            </div>
          </div>

          <div class="row">
            <div class="col-sm-4 col-sm-offset-5" style="padding-top: 10px">
              <div class="btn-group">
                <a class="btn btn-danger" href="{{ url('index/audit_iso') }}">Cancel</a>
              </div>
              <div class="btn-group">
                <button type="button" class="btn btn-primary pull-right" id="form_submit2"><i class="fa fa-edit"></i>&nbsp; Save </button>
              </div>

              
            </div>
          </div>

        </div>

        @endif

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

    $('.datepicker').datepicker({
      format: "dd/mm/yyyy",
      autoclose: true,
      todayHighlight: true
    });

    $('.datepicker2').datepicker({
      format: "yyyy-mm-dd",
      autoclose: true,
      todayHighlight: true
    });

    function selectemployee(){
      var auditee = document.getElementById("auditee").value;

      $.ajax({
         url: "{{ url('index/audit_iso/get_nama') }}?auditee=" +auditee, 
         type : 'GET', 
         success : function(data){
            var obj = jQuery.parseJSON(data);
            $('#auditee_name').val(obj[0].name);
         }
      });      
    }

    $("#form_submit").click( function() {
      $("#loading").show();

      if (CKEDITOR.instances.auditor_permasalahan.getData() == "") {
        $("#loading").hide();
        alert("Kolom Permasalahan Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if (CKEDITOR.instances.auditor_penyebab.getData() == "") {
        $("#loading").hide();
        alert("Kolom Penyebab Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if (CKEDITOR.instances.auditor_bukti.getData() == "") {
        $("#loading").hide();
        alert("Kolom Bukti Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#auditee").val() == "") {
        $("#loading").hide();
        alert("Kolom Auditee Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#auditee_due_date").val() == "") {
        $("#loading").hide();
        alert("Kolom Auditee Due Date Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      var type = [];
      var tinjauan;
      $("input[name='auditor_persyaratan']:checked").each(function (i) {
          type[i] = $(this).val();
      });

      var data = {
        id: "{{ $audits->id }}",
        auditor_lokasi: $("#auditor_lokasi").val(),
        auditor_kategori: $("#auditor_kategori").val(),
        auditor_persyaratan: type.join(),
        auditor_permasalahan: CKEDITOR.instances.auditor_permasalahan.getData(),
        auditor_penyebab: CKEDITOR.instances.auditor_penyebab.getData(),
        auditor_bukti: CKEDITOR.instances.auditor_bukti.getData(),
        auditee: $("#auditee").val(),
        auditee_name: $("#auditee_name").val(),
        auditee_due_date: $("#auditee_due_date").val()
      };

      $.post('{{ url("post/audit_iso/detail") }}', data, function(result, status, xhr){
        if(result.status == true){    
          $("#loading").hide();
          openSuccessGritter("Success","Berhasil Diedit");
          location.reload();
        }
         else {
        $("#loading").hide();
          openErrorGritter('Error!', result.datas);
        }
      });

    });


    $("#form_submit2").click( function() {
      $("#loading").show();

      if ($("#auditor_catatan").val() == "") {
        $("#loading").hide();
        alert("Kolom Catatan Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#auditor_manfaat").val() == "") {
        $("#loading").hide();
        alert("Kolom Manfaat Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      var data = {
        id: "{{ $audits->id }}",
        auditor_catatan: $("#auditor_catatan").val(),
        auditor_manfaat: $("#auditor_manfaat").val()
      };

      $.post('{{ url("post/audit_iso/detail_last") }}', data, function(result, status, xhr){
        if(result.status == true){    
          $("#loading").hide();
          openSuccessGritter("Success","Berhasil Diedit");
          location.reload();
        }
         else {
        $("#loading").hide();
          openErrorGritter('Error!', result.datas);
        }
      });

    });

    CKEDITOR.replace('auditor_permasalahan' ,{
      filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
      height: '200px'
    });

    CKEDITOR.replace('auditor_penyebab' ,{
      filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
      height: '200px'
    });

    CKEDITOR.replace('auditor_bukti' ,{
      filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
      height: '200px'
    });

    function sendEmail(id) {
      var data = {
        id:id
      };

      if (!confirm("Apakah anda yakin ingin mengirim Form Audit ke Standarisasi?")) {
        return false;
      }

      $.get('{{ url("index/audit_iso/sendemail") }}', data, function(result, status, xhr){

        openSuccessGritter("Success","Email Has Been Sent");
        setTimeout(function(){  window.location.reload() }, 3000);
      })
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

