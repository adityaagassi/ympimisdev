@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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
    Verifikasi {{ $page }}
    <small>Verifikasi Form</small>
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

        <?php $user = STRTOUPPER(Auth::user()->username)?>

        <table class="table" style="border: 1px solid black;">
            <thead>
            <tr>
              <th colspan="2" class="centera" >
                <center><img width="150px" src="{{ asset('images/logo_yamaha3.png') }}" alt="" style="vertical-align: middle !important"></center>
              </th>
              <th colspan="8" style="text-align: center; vertical-align: middle;font-size: 22px;font-weight: bold">CPAR Audit Internal Standarisasi</th>
              @if($audit->posisi == "std" && $audit->status == "cpar")
              <th colspan="2" style="border: 1px solid black;vertical-align: middle;font-size: 20px"><center>Approval</center></th>
              @endif
            </tr>
          </thead>
          <tbody>

            <?php 
              $syarat = $audit->auditor_persyaratan; 
              
              if($syarat != NULL){
                $split = explode(",", $syarat);
                $hitungsplit = count($split);
              }else{
                $split = 0;
              }
            ?>

            <form role="form" method="post" action="{{url('index/audit_iso/approval/'.$audit->id)}}">
            <tr>
              <td colspan="10" style="font-size: 20px;border-top: 1px solid black;background-color: #eeeeee"><b>Auditor - Auditee</b></td>

              @if($audit->posisi == "std" && $audit->status == "cpar")
              <td colspan="2" rowspan="5" style="border: 1px solid black;vertical-align: middle;">
                <center>
                  <label class="label label-success"  style="font-size: 1.4em">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                    <input type="checkbox" class="minimal-red" name="approve[]" value="1">   Approve
                  </label>
                </center>         
              </td>
              @endif
            </tr>
            <tr>
              <td colspan="1" style="border:none;width: 10%">Nama Auditor</td>
              <td colspan="1" style="text-align: right;border:none;width:1%">:</td>
              <td colspan="3" style="border:none;width: 30%"><b><?= $audit->auditor_name ?></b></td>

              <td colspan="1" style="border:none;width: 10%">Nama Auditee</td>
              <td colspan="1" style="text-align: right;border:none;width:1%">:</td>
              <td colspan="3" style="border:none;width: 30%"><b><?= $audit->auditee_name ?></b></td>
            </tr>
            <tr>
              <td colspan="1"style="border:none;width: 10%">Tanggal Terbit</td>
              <td colspan="1" style="text-align: right;border:none;width:1%">:</td>
              <td colspan="3" style="border:none;width: 30%"><b><?= date('d F Y', strtotime($audit->auditor_date)) ?></b></td>

              <td colspan="1" style="border:none;width: 10%">Target Selesai</td>
              <td colspan="1" style="text-align: right;border:none;width:1%">:</td>
              <td colspan="3" style="border:none;width: 30%"><b><?= date('d F Y', strtotime($audit->auditee_due_date)) ?></b></td>
            </tr>
            <tr>
              <td colspan="1"style="border:none">Kategori</td>
              <td colspan="1" style="text-align: right;border:none;width:1%">:</td>
              <td colspan="3" style="border:none"><b><?= $audit->auditor_kategori ?> - <?= $audit->audit_no ?></b></td>

              <td colspan="1" style="border:none;width: 10%">Lokasi</td>
              <td colspan="1" style="text-align: right;border:none;width:1%">:</td>
              <td colspan="3" style="border:none"><b><?= $audit->auditor_lokasi ?></b></td>
            </tr>
            <tr>
              <td colspan="1" style="border:none;width: 10%">Persyaratan</td>
              <td colspan="1" style="text-align: right;border:none;width:1%">:</td>
              <td colspan="1" style="border:none">
                  <div class="input-group" style="margin-top: 5px">
                    <label class="checkbox-inline">
                      <input type="checkbox" class="auditor_persyaratanCheckbox" name="auditor_persyaratan" value="1" id="auditor_persyaratan" disabled=""
                      <?php
                        foreach ($split as $key) {
                              if ($key == 1) {
                                echo 'checked';
                              }
                          } ?>>Prosedur (Manual)
                    </label>
                  </div>
              </td>
              <td colspan="2" style="border:none">
                  <div class="input-group" style="margin-top: 5px">
                    <label class="checkbox-inline">
                      <input type="checkbox" class="auditor_persyaratanCheckbox" name="auditor_persyaratan" value="2" id="auditor_persyaratan" disabled=""  
                      <?php
                        foreach ($split as $key) {
                              if ($key == 2) {
                                echo 'checked';
                              }
                          } ?>>Standart Produk/Spesifikasi
                    </label>
                  </div>
              </td>
              <td colspan="1" style="border:none">    
                  <div class="input-group" style="margin-top: 5px">
                    <label class="checkbox-inline">
                      <input type="checkbox" class="auditor_persyaratanCheckbox" name="auditor_persyaratan" value="3" id="auditor_persyaratan" disabled="" 
                      <?php
                        foreach ($split as $key) {
                              if ($key == 3) {
                                echo 'checked';
                              }
                          } ?>>Persyaratan Pelanggan
                    </label>
                  </div>
                </td>
              <td colspan="2" style="border:none">    
                  <div class="input-group" style="margin-top: 5px">
                    <label class="checkbox-inline">
                      <input type="checkbox" class="auditor_persyaratanCheckbox" name="auditor_persyaratan" value="4" id="auditor_persyaratan" disabled="" 
                      <?php
                        foreach ($split as $key) {
                              if ($key == 4) {
                                echo 'checked';
                              }
                          } ?>>Keputusan Top Manajemen
                    </label>
                  </div>
                </td>

              <td colspan="1" style="border:none">    
                  <div class="input-group" style="margin-top: 5px">
                    <label class="checkbox-inline">
                      <input type="checkbox" class="auditor_persyaratanCheckbox" name="auditor_persyaratan" value="5" id="auditor_persyaratan" disabled="" 
                      <?php
                        foreach ($split as $key) {
                              if ($key == 5) {
                                echo 'checked';
                              }
                          } ?>>Peraturan
                    </label>
                  </div>
                </td>
            </tr>
            <tr>
              <td colspan="10" style="font-size: 20px;border-top: 1px solid black;background-color: #eeeeee"><b>Isi</b></td>
              @if($audit->posisi == "std" && $audit->status == "cpar")
              <td colspan="2" rowspan="2" style="border: 1px solid black;vertical-align: middle;">
                <center>
                  <label class="label label-success"  style="font-size: 1.4em">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                    <input type="checkbox" class="minimal-red" name="approve[]" value="2">   Approve
                  </label>
                </center>
              </td>
              @endif
            </tr>
            <tr>
              <td colspan="1" style="border:none">Uraian Permasalahan</td>
              <td colspan="1" style="text-align: right;border:none;width:1%">:</td> 
              <td colspan="8" style="border:none">
                <b>
                  <?= $audit->auditor_permasalahan ?>
                </b>  
              </td>
            </tr>
            <tr>
              <td colspan="1" style="border-top: 1px solid black !important;border: none">Bukti Temuan</td>
              <td colspan="1" style="text-align: right;border-top: 1px solid black !important;border: none;width:1%">:</td> 
              <td colspan="8" style="border-top: 1px solid black !important;border:none;">
                <b>
                  <?= $audit->auditor_bukti ?>
                </b>  
              </td>
              @if($audit->posisi == "std" && $audit->status == "cpar")
              <td colspan="2" style="border: 1px solid black;vertical-align: middle;">
                <center>
                  <label class="label label-success"  style="font-size: 1.4em">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                    <input type="checkbox" class="minimal-red" name="approve[]" value="2">   Approve
                  </label>
                </center>
              </td>
              @endif
            </tr>
            <tr>
              <td colspan="1" style="border-top: 1px solid black !important;border: none">Penyebab</td>
              <td colspan="1" style="text-align: right;border-top: 1px solid black !important;border: none;width:1%">:</td> 
              <td colspan="8" style="border-top: 1px solid black !important;border:none;">
                <b>
                  <?= $audit->auditor_penyebab ?>
                </b>  
              </td>
              @if($audit->posisi == "std" && $audit->status == "cpar")
              <td colspan="2" style="border: 1px solid black;vertical-align: middle;">
                <center>
                  <label class="label label-success"  style="font-size: 1.4em">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                    <input type="checkbox" class="minimal-red" name="approve[]" value="2">   Approve
                  </label>
                </center>
              </td>
              @endif
            </tr>
            <tr>
              <td colspan="7" rowspan="3" style="border: 1px solid black"> </td>
            </tr>
            <tr>
              <td style="vertical-align: middle;border: 1px solid black">
                @if($audit->posisi == "auditor" || $audit->posisi == "std")
                  <?= $audit->auditor_name ?>
                @endif
              </td>
              <td style="vertical-align: middle;border: 1px solid black">
              </td>
              <td style="vertical-align: middle;border: 1px solid black">
              </td>
              @if($audit->posisi == "std" && $audit->status == "cpar")
              <td colspan="2" rowspan="2" style="border: 1px solid black;vertical-align: middle;padding: 0">
                <center>
                  <button class="btn btn-success" type="submit" style="font-weight: bold;">Approve</button>
                  <a data-toggle="modal" data-target="#comment{{$audit->id}}" class="btn btn-warning" href="" style="font-weight: bold; ">Comment</a>
                  <a data-toggle="modal" data-target="#reject{{$audit->id}}" class="btn btn-danger" href="" style="font-weight: bold; ">Reject</a>
                </center>
              </td>
              @endif
            </tr>
            <tr>
              <td>Auditor</td>
              <td>Auditee</td>
              <td>Standarisasi</td>
            </tr>
          </tbody>
       </table>
        
      </div>
    </form>
  </div>

  <div class="modal modal-warning fade" id="comment{{$audit->id}}" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form role="form" method="post" action="{{url('index/audit_iso/comment/'.$audit->id)}}">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Komentar</h4>
          </div>
          <div class="modal-body">
            <div class="box-body">
                <input type="hidden" value="{{csrf_token()}}" name="_token" />
                <h4>Berikan Komentar Pada Form Audit ini</h4>
                <textarea class="form-control" required="" name="alasan" style="height: 250px;"></textarea> 
                *Form Akan Dikirim kembali ke Pembuat Audit CPAR Untuk Diperbaiki
            </div>    
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-outline">Comment</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal modal-danger fade" id="reject{{$audit->id}}" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form role="form" method="post" action="{{url('index/audit_iso/reject/'.$audit->id)}}">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Reject</h4>
          </div>
          <div class="modal-body">
            <div class="box-body">
                <input type="hidden" value="{{csrf_token()}}" name="_token" />
                <h4>Berikan Alasan Menolak CPAR ini</h4>
                <textarea class="form-control" required="" name="alasan" style="height: 250px;"></textarea> 
                *Form Akan Dikirim kembali ke Pembuat Audit CPAR
            </div>    
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-outline">Reject</a>
          </div>
        </form>
      </div>
    </div>
  </div>


@endsection


@section('scripts')

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