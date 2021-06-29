@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  thead input {
    width: 100%;
    padding: 3px;
    box-sizing: border-box;
  }
  thead>tr>th{
    text-align:center;
    overflow:hidden;
  }
  tbody>tr>td{
    text-align:center;
  }
  tfoot>tr>th{
    text-align:center;
  }
  th:hover {
    overflow: visible;
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
    vertical-align: middle;
    padding:0;
    font-size: 13px;
    text-align: center;
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid black;
    padding:0;
  }
  td{
    overflow:hidden;
    text-overflow: ellipsis;
  }

  .table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
    background-color: #ffd8b7;
  }

  .table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
    background-color: #FFD700;
  }
  #loading, #error { display: none; }
  
</style>
@endsection

@section('header')
<section class="content-header">
  <h1>
    {{ $title }} <span class="text-purple"> {{ $title_jp }} </span>
  </h1>
  <ol class="breadcrumb">
  </ol>
</section>
@stop

@section('content')
<section class="content">
  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 45%; left: 35%;">
      <span style="font-size: 40px">Waiting, Please Wait <i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>

  <div class="col-xs-12">
    <div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
      <div class="box-body">
        <div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
          <div class="col-xs-12" style="background-color:  #bb8fce ;padding-left: 5px;padding-right: 5px;height:40px;vertical-align: middle;" align="center">
            <span style="font-size: 25px;color: black;width: 25%;">HUMAN RESOURCE DEPARTMENT</span>
            <span style="font-size: 25px;color: black;width: 25%;">人事部</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xs-3">  
    <div class="box-body" align="center">
      <button id="click_tp" onclick="DivTunjanganPekerjaan()" class="btn btn-success" style="font-weight: bold; font-size: 15px; width: 100%;">Pengajuan Tunjangan Pekerjaan<br>雇用手当申請書</button><br><br>
      <button id="click_simpati" onclick="DivUangSimpati()" class="btn btn-warning" style="font-weight: bold; font-size: 15px; width: 100%;">Pengajuan Uang Simpati<br>お見舞いのお金</button><br><br>
      <button id="click_tk" onclick="DivTunjanganKeluarga()" class="btn btn-success" style="font-weight: bold; font-size: 15px; width: 100%;">Pengajuan Tunjangan Keluarga<br>家族手当の申請</button>
    </div>
  </div>

  <div class="col-xs-9" id="tunjangan_pekerjaan" style="display: none">
    <div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
      <div class="box-body">
        <div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
          <div class="col-xs-12" style="background-color: #e8daef;padding-left: 5px;padding-right: 5px;height:35px;vertical-align: middle;" align="center">
            <span style="font-size: 25px;color: black;width: 25%;">Pengajuan Tunjangan Pekerjaan</span>
            <span style="font-size: 25px;color: black;width: 25%;">雇用手当申請書</span>
          </div>
          <br><br>

          <div class="col-md-12">

            <div class="col-xs-12" style="background-color: #ffd8b7;padding-left: 5px;padding-right: 5px;height:30px;vertical-align: middle;" align="center">
              <span style="font-size: 20px;color: black;width: 25%;">Perubahan Karyawan Yang Mendapatkan Tunjangan Proses Kerja Rutin</span>
            </div>

            <div class="col-xs-12">
                <span style="font-weight: bold; font-size: 16px;">No :<span class="text-red">*</span></span>
                <input type="text" class="form-control" id="urut_tp" name="urut_tp" style="width: 20%; height: 30px" value="" readonly="">
            </div>

            <div class="col-md-5">
              <div class="form-group">
              <label>Department<span class="text-red">*</span></label>
              <select class="form-control select2" id="department_tp" name="department_tp" data-placeholder='Pilih Department' style="width: 100%" onchange ="SelectSection()">
                  <option value="">&nbsp;</option>
                  @foreach($department as $dept)
                  <option value="{{$dept->department}}">{{$dept->department}}</option>
                  @endforeach
              </select>
              </div>  
            </div>
            <div class="col-md-4">
              <div class="form-group">
              <label>Section<span class="text-red">*</span></label>
              <select class="form-control select2" id="section_tp" name="section_tp" data-placeholder='Pilih Section' style="width: 100%">
              </select>
              </div>  
            </div>
            <div class="col-md-3">
              <div class="form-group">
              <label>Bulan<span class="text-red">*</span></label>
              <div class="input-group date">
                <div class="input-group-addon bg-green" style="border: none;">
                <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control datepicker" id="bulan_tp" placeholder="Pilih Bulan">
              </div>
              </div>  
            </div>
        </div>

        <div class="col-md-12">
          <button class="btn btn-sm btn-success pull-right" onclick="add_item()"><i class="fa fa-plus"></i>&nbsp; Add</button>
          <table class="table">
              <thead>
                   <tr>
                        <th style="width: 50%">NIK / Nama</th>
                        <th style="width: 10%">IN / OUT</th>
                        <th style="width: 20%">Tanggal</th>
                        <th style="width: 10%">Keterangan</th>
                        <th style="width: 10%">#</th>
                   </tr>
              </thead>
              <tbody id="body_add">
              </tbody>
           </table>
           <div class="col-md-12" style="margin-bottom : 5px">
            <button id="kirim_tp" class="btn btn-info" style="font-weight: bold; font-size: 15px; width: 100%;" onclick="save_item()">Kirim Pengajuan<br>提出物を提出する</button>
          </div>
        </div>

      </div>
    </div>
  </div>
<!-- </form> -->
</div>

  <div class="col-xs-9" id="uang_simpati" style="display: none">
  <form id ="AddUangSimpati" name="AddUangSimpati" method="post" action="{{ url('human_resource/add/uang_simpati') }}" enctype="multipart/form-data">
  <input type="hidden" value="{{csrf_token()}}" name="_token" />
    <div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
      <div class="box-body">
        <div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
          <div class="col-xs-12" style="background-color: #e8daef;padding-left: 5px;padding-right: 5px;height:35px;vertical-align: middle;" align="center">
            <span style="font-size: 25px;color: black;width: 25%;">Pengajuan Uang Simpati</span>
            <span style="font-size: 25px;color: black;width: 25%;">お見舞いのお金</span>
          </div>
          <br><br>
          
          <div class="col-md-6">
            <div class="form-group">
              <label>NIK<span class="text-red">*</span></label>
              <select class="form-control select2" id="employee_id_us" name="employee_id_us" data-placeholder='Pilih NIK Atau Nama' style="width: 100%" onchange="checkEmpUs(this.value)">
                  <option value="">&nbsp;</option>
                  @foreach($user as $row)
                  <option value="{{$row->employee_id}}">{{$row->employee_id}} - {{$row->name}}</option>
                  @endforeach
              </select>
            </div>
            <div class="form-group" id="pemohonan">
              <label id="label_section">Sub Group<span class="text-red">*</span></label>
              <input type="text" class="form-control" id="sub_group_us" name="sub_group_us" readonly>
            </div>
            <div class="form-group">
              <label id="label_group">Group<span class="text-red">*</span></label>
              <input type="text" class="form-control" id="group_us" name="group_us" readonly>
            </div>
            <div class="form-group">
              <label id="label_section">Seksi<span class="text-red">*</span></label>
              <input type="text" class="form-control" id="section_us" name="section_us" readonly>
            </div>
            <div class="form-group">
              <label id="labeldept">Department<span class="text-red">*</span></label>
              <input type="text" class="form-control" id="department_us" name="department_us" readonly>
            </div>
            <div class="form-group">
              <label id="labelposition">Jabatan<span class="text-red">*</span></label>
              <input type="text" class="form-control" id="position_us" name="position_us" readonly>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group"><br>
              <span>Dengan ini mengajukan permohonan untuk mendapatkan Uang Simpati bentuk : </span><br><br>
              <div class="col-md-6">
                <input type="radio" name="permohonan_us" value="Uang Simpati Pernikahan"> Uang Simpati Pernikahan<br>  
                <input type="radio" name="permohonan_us" value="Uang Simpati Kelahiran"> Uang Simpati Kelahiran<br>  
              </div>
              <div class="col-md-6">
                <input type="radio" name="permohonan_us" value="Uang Simpati Kematian"> Uang Simpati Kematian<br>  
                <input type="radio" name="permohonan_us" value="Uang Simpati Musibah"> Uang Simpati Musibah<br> 
              </div>
            </div>
            <br><br><br><br>
            <div class="form-group">
              <span>Untuk Keperluan tersebut, bersama ini saya lampirkan : </span><br>
              <div class="col-md-12">
                <span style="font-weight: bold; font-size: 16px;">Surat Nikah<span class="text-red">*</span></span>
                <input type="file" class="form-control-file" id="surat_nikah_us" name="surat_nikah_us">
              </div><br><br><br>
              <div class="col-md-12">
                <span style="font-weight: bold; font-size: 16px;">Akte Kelahiran / Surat Kenal Lahir<span class="text-red">*</span></span>
                <input type="file" class="form-control-file" id="surat_akte_us" name="surat_akte_us">
              </div><br><br><br>
              <div class="col-md-12">
                <span style="font-weight: bold; font-size: 16px;">Surat Kematian<span class="text-red">*</span></span>
                <input type="file" class="form-control-file" id="surat_kematian_us" name="surat_kematian_us">
              </div><br><br><br>
              <div class="col-md-12">
                <span style="font-weight: bold; font-size: 16px;">Surat Keterangan Lain<span class="text-red">*</span></span>
                <input type="file" class="form-control-file" id="surat_lain_us" name="surat_lain_us">
              </div>
            </div>
          </div>

          <div class="col-md-12" style="margin-bottom : 5px">
            <button id="kirim_us" class="btn btn-info" style="font-weight: bold; font-size: 15px; width: 100%;" type="submit">Kirim Pengajuan<br>提出物を提出する</button>
          </div>

        </div>
      </div>
    </div>
  </form>
  </div>

  <div class="col-xs-9" id="tunjangan_keluarga" style="display: none">
  <form id ="AddUangSimpati" name="AddUangSimpati" method="post" action="{{ url('human_resource/add/uang_keluarga') }}" enctype="multipart/form-data">
  <input type="hidden" value="{{csrf_token()}}" name="_token" />
    <div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
      <div class="box-body">
        <div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
          <div class="col-xs-12" style="background-color: #e8daef;padding-left: 5px;padding-right: 5px;height:35px;vertical-align: middle;" align="center">
            <span style="font-size: 25px;color: black;width: 25%;">Tunjangan Keluarga</span>
            <span style="font-size: 25px;color: black;width: 25%;">家族手当</span>
          </div>
          <br><br>
          
          <div class="col-md-6">
            <div class="form-group">
              <label>NIK<span class="text-red">*</span></label>
              <select class="form-control select2" id="employee_id_tk" name="employee_id_tk" data-placeholder='Pilih NIK Atau Nama' style="width: 100%" onchange="checkEmpTk(this.value)">
                  <option value="">&nbsp;</option>
                  @foreach($user as $row)
                  <option value="{{$row->employee_id}}">{{$row->employee_id}} - {{$row->name}}</option>
                  @endforeach
              </select>
            </div>
            <div class="form-group">
              <label id="label_section">Sub Group<span class="text-red">*</span></label>
              <input type="text" class="form-control" id="sub_group_tk" name="sub_group_tk" readonly>
            </div>
            <div class="form-group">
              <label id="label_group">Group<span class="text-red">*</span></label>
              <input type="text" class="form-control" id="group_tk" name="group_tk" readonly>
            </div>
            <div class="form-group">
              <label id="label_section">Seksi<span class="text-red">*</span></label>
              <input type="text" class="form-control" id="section_tk" name="section_tk" readonly>
            </div>
            <div class="form-group">
              <label id="labeldept">Department<span class="text-red">*</span></label>
              <input type="text" class="form-control" id="department_tk" name="department_tk" readonly>
            </div>
            <div class="form-group">
              <label id="labelposition">Jabatan<span class="text-red">*</span></label>
              <input type="text" class="form-control" id="position_tk" name="position_tk" readonly>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group"><br>
              <span>Dengan ini mengajukan permohonan untuk mendapatkan Tunjangan Keluarga, yaitu : </span><br>
            </div>
            <div class="col-md-6">
              <input type="checkbox" id="isteri" name="isteri_tk" value="Tunjangan Isteri"> Tunjangan Isteri
            </div>
            <div class="col-md-1">
              <input type="checkbox"><br>  
            </div>
            <div class="col-md-5"> 
              <input type="text" id="anak_tk" name="anak_tk" placeholder="Tunjangan Anak Ke "><br>  
            </div>
            <br><br><br><br>
            <div class="form-group">
              <span>Untuk Keperluan tersebut, bersama ini saya lampirkan : </span><br>
              <div class="col-md-12">
                <span style="font-weight: bold; font-size: 16px;">Surat Nikah<span class="text-red">*</span></span>
                <input type="file" class="form-control-file" id="surat_nikah_tk" name="surat_nikah_tk">
              </div><br><br><br>
              <div class="col-md-12">
                <span style="font-weight: bold; font-size: 16px;">Akte Kelahiran / Surat Kenal Lahir<span class="text-red">*</span></span>
                <input type="file" class="form-control-file" id="surat_akte_tk" name="surat_akte_tk">
              </div><br><br><br>
              <div class="col-md-12">
                <span style="font-weight: bold; font-size: 16px;">Surat Keterangan Lain<span class="text-red">*</span></span>
                <input type="file" class="form-control-file" id="surat_lain_tk" name="surat_lain_tk">
              </div>
            </div>
          </div>

          <div class="col-md-12" style="margin-bottom : 5px">
            <button id="kirim_tk" class="btn btn-info" style="font-weight: bold; font-size: 15px; width: 100%;">Kirim Pengajuan<br>提出物を提出する</button>
          </div>

        </div>
      </div>
    </div>
  </form>
  </div>
</section>
@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
{{-- <script src="{{ url("js/pdfmake.min.js")}}"></script> --}}
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
  var no = 2;
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

     var no = 1;
     var arr_employee_id_tp = [];
     var arr_in_out_tp = [];
     var arr_tanggal_tp = [];
     var arr_keterangan_tp = [];

  jQuery(document).ready(function() {
     $('body').toggleClass("sidebar-collapse");
     Home();
     $('.select2').select2({
      allowClear : true
    });
  });

  $('.datepicker').datepicker({
    autoclose: true,
    format: "yyyy-mm",
    todayHighlight: true,
    startView: "months", 
    minViewMode: "months",
    autoclose: true,
   });

  $('#tanggal').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd',
    todayHighlight: true
  });
  


  function openSuccessGritter(title, message){
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-success',
      image: '{{ url("images/image-screen.png") }}',
      sticky: false,
      time: '4000'
    });
  }

  function openErrorGritter(title, message) {
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-danger',
      image: '{{ url("images/image-stop.png") }}',
      sticky: false,
      time: '4000'
    });
  }


  function Home(){
    $("#tunjangan_pekerjaan").hide();
    $("#uang_simpati").hide();
    $("#tunjangan_keluarga").hide();
  }

  function DivTunjanganPekerjaan(){
    $("#tunjangan_pekerjaan").show();
    $("#uang_simpati").hide();
    $("#tunjangan_keluarga").hide();
  }

  function SelectSection(){
    var data = {
        department_tp:$('#department_tp').val()
      }

      $.get('{{ url("human_resource/get_section") }}',data, function(result, status, xhr){
          if(result.status){
            $('#section_tp').show();
            $('#section_tp').html("");
            var sections = "";
            sections += '<option value="">&nbsp;</option>';
            $.each(result.section, function(key, value) {
                sections += '<option value="'+value.section+'">'+value.section+'</option>';
            });

            $('#section_tp').append(sections);
          }
      });
  }

  function DivUangSimpati(){
    $("#uang_simpati").show();
    $("#tunjangan_pekerjaan").hide();
    $("#tunjangan_keluarga").hide();
  }

  function checkEmpUs(value) {
    var data = {
        employee_id_us:$('#employee_id_us').val()
      }

      $.get('{{ url("human_resource/get_employee") }}',data, function(result, status, xhr){
          if(result.status){
          
            $('#employee_id_us').show();
            $('#sub_group_us').show();
            $('#group_us').show();
            $('#section_us').show();
            $('#department_us').show();
            $('#position_us').show();

            $.each(result.employee, function(key, value) {
                $('#employee_id_us').val(value.employee_id);
                $('#sub_group_us').val(value.sub_group);
                $('#group_us').val(value.group);
                $('#section_us').val(value.section);
                $('#department_us').val(value.department);
                $('#position_us').val(value.position);
            });
          }
      });         
    }

  function DivTunjanganKeluarga(){
    $("#tunjangan_keluarga").show();
    $("#tunjangan_pekerjaan").hide();
    $("#uang_simpati").hide();
  }

  function checkEmpTk(value) {
    var data = {
        employee_id_tk:$('#employee_id_tk').val()
      }

      $.get('{{ url("human_resource/get_employee") }}',data, function(result, status, xhr){
          if(result.status){
          
            $('#employee_id_tk').show();
            $('#sub_group_tk').show();
            $('#group_tk').show();
            $('#section_tk').show();
            $('#department_tk').show();
            $('#position_tk').show();

            $.each(result.employee, function(key, value) {
                $('#employee_id_tk').val(value.employee_id);
                $('#sub_group_tk').val(value.sub_group);
                $('#group_tk').val(value.group);
                $('#section_tk').val(value.section);
                $('#department_tk').val(value.department);
                $('#position_tk').val(value.position);
            });
          }
      });         
    }

    function add_item() {
          var bodi = "";
          var employee_id_tp = "";
          var in_out_tp = "";
          var tanggal_tp = "";
          var keterangan_tp = "";

          employee_id_tp += "<option value=''></option>";
          in_out_tp += "<option value=''></option>";
          keterangan_tp += "<option value=''></option>";
  

          bodi += '<tr id="'+no+'" class="item">';
          bodi += '<td>';
          bodi += '<select class="form-control select2" id="employee_id_tp_'+no+'" name="employee_id_tp_'+no+'" data-placeholder="Pilih NIK Atau Nama" style="width: 100%"><option value="">&nbsp;</option>@foreach($user as $row)<option value="{{$row->employee_id}}">{{$row->employee_id}} - {{$row->name}}</option>@endforeach</select>';
          bodi += '</td>';

          bodi += '<td>';
          bodi += '<select class="form-control select2" id="in_out_tp_'+no+'" name="employee_id_tk_'+no+'" data-placeholder="IN / OUT" style="width: 100%"><option value="">&nbsp;</option><option value="IN">IN</option><option value="OUT">OUT</option></select>';
          bodi += '</td>';

          bodi += '<td>';
          bodi += '<div class="input-group date"><div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;"><i class="fa fa-calendar"></i></div><input type="text" class="form-control datepicker" id="tanggal_'+no+'" name="tanggal_'+no+'" placeholder="Received Date"></div>';
          bodi += '</td>';

          bodi += '<td>';
          bodi += '<input type="text" class="form-control" id="keterangan_'+no+'" name="employee_id_tk_'+no+'">'
          bodi += '</td>';

          bodi += '<td><button class="btn btn-sm btn-danger" onclick="delete_item('+no+')"><i class="fa fa-trash"></i></button></td>';

          bodi += '</tr>';

          $("#body_add").append(bodi);

          $.each(arr_employee_id_tp, function(index, value){
               employee_id_tp += "<option value='"+value+"'>"+value+"</option>";
          })

          $.each(arr_in_out_tp, function(index, value){
               in_out_tp += "<option value='"+value+"'>"+value+"</option>";
          })

          $.each(arr_tanggal_tp, function(index, value){
               tanggal_tp += "<option value='"+value+"'>"+value+"</option>";
          })

          $.each(arr_keterangan_tp, function(index, value){
               keterangan_tp += "<option value='"+value+"'>"+value+"</option>";
          })

          no++;
          $('.select2').select2({
            allowClear : true
          });

          $(".datepicker").datepicker({
              format: 'yyyy-mm-dd',
              autoclose: true,
              todayHighlight: true,
          });
     }

     function save_item() {
          arr_params = [];

          $('.item').each(function(index, value) {
               var ido = $(this).attr('id');
                arr_params.push({'employee_id_tp' : $("#employee_id_tp_"+ido).val(), 'in_out_tp' : $("#in_out_tp_"+ido).val(), 'tanggal_tp' : $("#tanggal_"+ido).val(), 'keterangan_tp' : $("#keterangan_"+ido).val()});
          });

          var data = {
               item : arr_params,
               department_tp : $('#department_tp').val(),
               section_tp : $('#section_tp').val().split('_')[0],
               bulan_tp : $('#bulan_tp').val()
          }

          $.post('{{ url("human_resource/add/uang_pekerjaan") }}', data, function(result, status, xhr) {
               openSuccessGritter('Success','Pengajuan Disimpan');
                location.reload(true);
          })
     }

     function delete_item(no) {
          $("#"+no).remove();
     }


</script>

@endsection