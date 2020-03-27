@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<!-- <script src="{{ asset('/ckeditor/ckeditor.js') }}"></script> -->
<style type="text/css">
  thead input {
    width: 100%;
    padding: 3px;
    box-sizing: border-box;
  }
  input[type=checkbox] {
    transform: scale(1.25);
  }
  thead>tr>th{
    /*text-align:center;*/
    background-color: #7e5686;
    color: white;
    border: none;
    border:1px solid black;
    border-bottom: 1px solid black !important;
  }
  tbody>tr>td{
    /*text-align:center;*/
    border: 1px solid black;
  }
  tfoot>tr>th{
    /*text-align:center;*/
  }
  td:hover {
    overflow: visible;
  }
  table.table-hover > tbody > tr > td{
    border:1px solid #eeeeee;
  }

  table.table-bordered{
    border:1px solid black;
  }
  table.table-bordered > thead > tr > th{
    border:1px solid black;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid rgb(211,211,211);
    padding-top: 0;
    padding-bottom: 0;
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid rgb(211,211,211);
  }
  .isi{
    background-color: #f5f5f5;
    color: black;
    padding: 10px;
  }
  #loading, #error { display: none; }
</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    {{ $page }}
    <small>Verifikasi Penanganan Oleh Bagian</small>
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
  @if (session('status'))
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
    {{ session('status') }}
  </div>   
  @endif
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
    <h4><i class="icon fa fa-ban"></i> Not Verified!</h4>
    {{ session('error') }}
  </div>   
  @endif
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary">
      <div class="box-body">

        <?php $user = STRTOUPPER(Auth::user()->username); ?>

        <a class="btn btn-warning btn-md" data-toggle="tooltip" title="Lihat Report" href="{{url('index/form_ketidaksesuaian/print', $verifikasi['id'])}}" target="_blank" style="margin-right: 5px;width: 150px">Preview Report PDF</a>

        @if($verifikasi->posisi == 'verif')

        <?php if ($user == $verifikasi->chief || $user == $verifikasi->foreman || $user == $verifikasi->manager || Auth::user()->role_code == "MIS") { ?>

        <input type="checkbox" checked data-toggle="toggle" data-on="Verified" data-off="Not Verified" data-onstyle="primary" data-offstyle="danger" data-width="300" id="stat">
        <button id="submit_verified" class="btn btn-success" onclick="close_car()" style="width: 20%"><i class="fa fa-check"></i> Verifikasi</button>
        <button id="submit_not_verified" class="btn btn-danger" onclick="reject_car()" style="width: 20%"><i class="fa fa-close"></i> Reject</button>
        
        <textarea class="form-control" placeholder="Berikan alasan tidak menyetujui penanganan ini. . ." id="catatan" style="margin-top: 10px"></textarea>

        <?php } else { ?>

        <div class="btn btn-primary col-sm-12" href="" style="width: 100%; font-weight: bold; font-size: 20px;margin-top: 10px;margin-bottom: 10px">Verifikasi Hanya Bisa dilakukan oleh Chief / Foreman / Manager Terkait Saja</div>

        <?php } ?>

        @elseif($verifikasi->posisi == 'close')

        <div class="btn btn-success col-sm-12" href="" style="width: 100%; font-weight: bold; font-size: 20px;margin-top: 10px;margin-bottom: 10px">Form Ketidaksesuaian Sudah Close</div>

        @endif

        <table class="table" style="border: 1px solid black;margin-top: 10px">
            <thead>
            <tr>
              <th colspan="2" class="centera" >
                <center><img width="150px" src="{{ asset('images/logo_yamaha3.png') }}" alt="" style="vertical-align: middle !important"></center>
              </th>
              <th colspan="8" style="text-align: center; vertical-align: middle;font-size: 18px;font-weight: bold">Form Laporan Ketidaksesuaian</th>
            </tr>
          </thead>
          <tbody>
            <form role="form" method="post" action="{{url('index/form_ketidaksesuaian/approvalcar/'.$verifikasi->id)}}">
           
            <tr>
              <td colspan="2" style="border:none;width: 25%">Judul Komplain</td>
              <td colspan="2" style="text-align: right;border:none">:</td>
              <td colspan="6" style="border:none"><b><?= strtoupper($verifikasi->judul) ?> (<?= $verifikasi->kategori ?>)</b></td>
            </tr>
            <tr>
              <td colspan="2"style="border:none">Tanggal Form Dibuat</td>
              <td colspan="2" style="text-align: right;border:none">:</td>
              <td colspan="6" style="border:none"><b><?php echo date('d F Y', strtotime($verifikasi->tanggal)) ?></b></td>
            </tr>
            <tr>
              <td colspan="2"style="border:none">Tanggal Penanganan Dibuat</td>
              <td colspan="2" style="text-align: right;border:none">:</td>
              <td colspan="6" style="border:none"><b><?php echo date('d F Y', strtotime($verifikasi->tanggal_car)) ?></b></td>
            </tr>
            <?php 
              $secfrom = explode('_',$verifikasi->section_from);
              $secto = explode('_',$verifikasi->section_to);
            ?>
            <tr>
              <td colspan="2" style="border:none">Section Pelapor</td>
              <td colspan="2" style="text-align: right;border:none">:</td> 
              <td colspan="6" style="border:none"><b><?= $secfrom[0]; ?> - <?= $secfrom[1] ?></b></td>
            </tr>

            <tr>
              <td colspan="2"style="border:none">Section Yang Dituju</td>
              <td colspan="2" style="text-align: right;border:none">:</td> 
              <td colspan="6" style="border:none"><b><?= $secto[0]; ?> - <?= $secto[1] ?></b></td>
            </tr>
            <?php 
              $jumlahitem = count($items);
            ?>

            <tr>
              <td colspan="10" style="font-size: 20px;border-top: 1px solid black;background-color: #eeeeee"><b>Deskripsi Permasalahan</b></td>
            </tr>
            
            <tr>
              <td colspan="10" style="border:none">
                @if($verifikasi->deskripsi_car != "") 
                  <?= $verifikasi->deskripsi_car ?>
                @else
                  -
                @endif  
              </td>
            </tr>

            <tr>
              <td colspan="10" style="font-size: 20px;border-top: 1px solid black;background-color: #eeeeee"><b>Penanganan Yang Dilakukan</b></td>
            </tr>

            <tr>
              <td colspan="10" style="border:none">
                @if($verifikasi->penanganan_car != "") 
                  <?= $verifikasi->penanganan_car ?>
                @else
                  -
                @endif  
              </td>
            </tr>

            <tr>
              <td colspan="7" rowspan="3" style="border-top: 1px solid black">&nbsp;</td>
              <td style="border-top: 1px solid black">PIC</td>
              <td style="border-top: 1px solid black">Mengetahui</td>
              <td style="border-top: 1px solid black">Mengetahui</td>              
            </tr>
            <tr>
              <td style="vertical-align: middle;">
                @if($verifikasi->posisi == "dept" || $verifikasi->posisi == "deptcf" || $verifikasi->posisi == "deptm")
                  {{$pic}}
                @elseif($verifikasi->approvalcf == "Approved" || $verifikasi->approvalm == "Approved")
                  {{$pic}}
                @else

                @endif
              </td>
              <td style="vertical-align: middle;">
                @if($verifikasi->approvalcf_car == "Approved" || $verifikasi->approvalm_car == "Approved")
                  {{$cfcar}}
                @else

                @endif
              </td>
              <td style="vertical-align: middle;">
                @if($verifikasi->approvalm_car == "Approved")
                  {{$mcar}}
                @else
                  
                @endif
              </td>
            </tr>
            <tr>
              <td>Leader / Staff</td>
              <td>Foreman / Chief</td>
              <td>Manager</td>
            </tr>
          </tbody>
       </table>
      </div>
    </form>
  </div>

  <div class="modal modal-success fade" id="modalClose">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-body">
          <div class="row">
            <div class="col-xs-12">
              <p style="font-size: 25px; font-weight: bold;">Apakah Anda ingin menutup kasus ini?</p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success pull-left" data-dismiss="modal" onclick="confirmClose()"><i class="fa fa-check"></i> YES</button>
          <button type="button" class="btn btn-primary pull-right" data-dismiss="modal"><i class="fa fa-close"></i> NO</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal modal-danger fade" id="modalReject">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-body">
          <div class="row">
            <div class="col-xs-12">
              <p style="font-size: 25px; font-weight: bold;">Apakah Anda yakin tidak menyetujui ini?</p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success pull-left" data-dismiss="modal" onclick="confirmReject()"><i class="fa fa-check"></i> YES</button>
          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> NO</button>
        </div>
      </div>
    </div>
  </div>


@endsection


@section('scripts')
<script src="{{ url("js/bootstrap-toggle.min.js") }}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
  $(document).ready(function() {

    $("body").on("click",".btn-danger",function(){ 
      $(this).parents(".control-group").remove();
    });
    $('body').toggleClass("sidebar-collapse");
  });

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

    $(window).on('pageshow', function(){
      if($("#stat").prop("checked") == true){
        $("#submit_verified").show();
        $("#submit_not_verified").hide();
        $("#catatan").hide();
      }else{
        $("#submit_not_verified").show();
        $("#submit_verified").hide();
        $("#catatan").show();
      }
    });

    $("#stat").change(function () {
      if($(this).prop("checked") == true){
        $("#submit_verified").show();
        $("#submit_not_verified").hide();
        $("#catatan").hide();
      }else{
        $("#submit_not_verified").show();
        $("#submit_verified").hide();
        $("#catatan").show();
      }
    });

    function close_car() {
      $('#modalClose').modal({
        backdrop: 'static',
        keyboard: false
      })
    }

    function confirmClose() {
      var data = {
        id : "{{$verifikasi->id}}"
      }
      $.post('{{ url("index/form_ketidaksesuaian/close") }}', data, function(result) {
        openSuccessGritter('Success!', result.message);
        setTimeout(function(){ location.reload(); }, 1000);
      })
    }


    function reject_car() {
      $('#modalReject').modal({
        backdrop: 'static',
        keyboard: false
      })
    }

    function confirmReject(){
      var note = $('#catatan').val();

      if (note == "") {
        openErrorGritter('Failed!' ,'Kolom Catatan Tidak Boleh Kosong');
        return false;
      }

      data = {
        id : "{{$verifikasi->id}}",
        catatan : note
      }

      $.post('{{ url("index/form_ketidaksesuaian/reject") }}', data, function(result) {
        if(result.status == true){
          openSuccessGritter('Success!', result.message);
          setTimeout(function(){ location.reload(); }, 1000);
        }
        else{
          openErrorGritter('Error!', result.message);
        }
      });
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
        time: '3000'
      });
    }


  </script>
  @stop