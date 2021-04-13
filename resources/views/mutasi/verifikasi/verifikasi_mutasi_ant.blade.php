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
    Check & Verifikasi {{ $page }}
  </h1>
  <ol class="breadcrumb">
 </ol>
</section>
@endsection
@section('content')
<section class="content">
  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
      <p style="position: absolute; color: White; top: 45%; left: 35%;">
        <span style="font-size: 40px">Loading, mohon tunggu . . . <i class="fa fa-spin fa-refresh"></i></span>
      </p>
  </div>
    <?php $user = STRTOUPPER(Auth::user()->username)?>
  <!-- SELECT2 EXAMPLE -->
  @if(
      (($user == $resumes[0]->chief_or_foreman_asal || Auth::user()->role_code == "MIS") && $resumes[0]->app_ca == null && $resumes[0]->posisi == "chf_asal") 
      || (($user == $resumes[0]->manager_asal || Auth::user()->role_code == "MIS") && $resumes[0]->app_ma == null && $resumes[0]->posisi == "mgr_asal")
      || (($user == $resumes[0]->dgm_asal || Auth::user()->role_code == "MIS") && $resumes[0]->app_da == null && $resumes[0]->posisi == "dgm_asal")
      || (($user == $resumes[0]->gm_asal || Auth::user()->role_code == "MIS") && $resumes[0]->app_ga == null && $resumes[0]->posisi == "gm_asal")
      || (($user == $resumes[0]->chief_or_foreman_tujuan || Auth::user()->role_code == "MIS") && $resumes[0]->app_ct == null && $resumes[0]->posisi == "chf_tujuan") 
      || (($user == $resumes[0]->manager_tujuan || Auth::user()->role_code == "MIS") && $resumes[0]->app_mt == null && $resumes[0]->posisi == "mgr_tujuan")
      || (($user == $resumes[0]->dgm_tujuan || Auth::user()->role_code == "MIS") && $resumes[0]->app_dt == null && $resumes[0]->posisi == "dgm_tujuan")
      || (($user == $resumes[0]->gm_tujuan || Auth::user()->role_code == "MIS") && $resumes[0]->app_gt == null && $resumes[0]->posisi == "gm_tujuan")
      || (($user == $resumes[0]->manager_hrga || Auth::user()->role_code == "MIS") && $resumes[0]->app_m == null && $resumes[0]->posisi == "mgr_hrga")
      || (($user == $resumes[0]->direktur_hr || Auth::user()->role_code == "MIS") && $resumes[0]->app_dir == null && $resumes[0]->posisi == "dir_hr")
      )
    <div class="box box-primary">
    <div class="box-body"> 
        <table class="table table-bordered">
          <tr id="show-att">
            <td>
              <table style="width: 100%; font-family: arial; border-collapse: collapse; text-align: left;">
            <thead>
              <tr>
                <td colspan="13" style="font-weight: bold;font-size: 10px">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
              </tr>
              <tr>
                <td colspan="13"><br></td>
              </tr>       
              <tr>
                <td colspan="13" style="text-align: center;font-weight: bold;font-size: 20px">FORM MUTASI</td>
              </tr>
              <tr>
                <td colspan="13" style="text-align: center;font-weight: bold;font-size: 20px">(Antar Departemen)</td>
              </tr>
              <tr>
                <td colspan=5><br></td>
              </tr> 
              <tr>
          <td colspan="2" style="font-size: 12px;width: 22%; font-weight: bold">Nama</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->nama }}</td>
        </tr>
        <tr>
          <td colspan="2" style="font-size: 12px;width: 22%; font-weight: bold">NIK</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->nik }}</td>
        </tr>
        <tr>
          <td colspan="2">&nbsp;</td>
          <td colspan="7" style="text-align: center;font-weight: bold;font-size: 20px"></td>
        </tr>
        <tr>
          <th colspan="3" style="font-size: 12px; background-color: green; border: 1px solid black; text-align: center; ">Detail</th>
          <th colspan="5" style="font-size: 12px; background-color: green; border: 1px solid black; text-align: center">Asal</th>
          <th colspan="5" style="font-size: 12px; background-color: green; border: 1px solid black; text-align: center">Tujuan</th>
        </tr>
        <tr align="center">
          <td colspan="3" style="font-size: 12px; border: 1px solid black; font-weight: bold">Sub Group</td>
          <td colspan="5" style="font-size: 12px; border: 1px solid black; ">{{ $resumes[0]->sub_group }}</td>
          <td colspan="5" style="font-size: 12px; border: 1px solid black">{{ $resumes[0]->ke_sub_group }}</td>
        </tr>
        <tr align="center">
          <td colspan="3" style="font-size: 12px; border: 1px solid black; font-weight: bold">Group</td>
          <td colspan="5" style="font-size: 12px; border: 1px solid black">{{ $resumes[0]->group }}</td>
          <td colspan="5" style="font-size: 12px; border: 1px solid black">{{ $resumes[0]->ke_group }}</td>
        </tr>
        <tr align="center">
          <td colspan="3" style="font-size: 12px; border: 1px solid black; font-weight: bold">Seksi</td>
          <td colspan="5" style="font-size: 12px; border: 1px solid black">{{ $resumes[0]->seksi }}</td>
          <td colspan="5" style="font-size: 12px; border: 1px solid black">{{ $resumes[0]->ke_seksi }}</td>
        </tr>
        <tr align="center">
          <td colspan="3" style="font-size: 12px; border: 1px solid black; font-weight: bold">Departemen</td>
          <td colspan="5" style="font-size: 12px; border: 1px solid black">{{ $resumes[0]->departemen }}</td>
          <td colspan="5" style="font-size: 12px; border: 1px solid black">{{ $resumes[0]->ke_departemen }}</td>
        </tr>
        <tr align="center">
          <td colspan="3" style="font-size: 12px; border: 1px solid black; font-weight: bold">Jabatan</td>
          <td colspan="10" style="font-size: 12px; border: 1px solid black">{{ $resumes[0]->jabatan }}</td>
        </tr>
        <tr>
          <td colspan="2">&nbsp;</td>
          <td colspan="11" style="text-align: center;font-weight: bold;font-size: 20px"></td>
        </tr>
        <!-- <tr>
          <td colspan="2" style="font-size: 12px;width: 22%">Sub Group</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->sub_group }}</td>
        </tr>
        <tr>
          <td colspan="2" style="font-size: 12px;width: 22%">Group</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->group }}</td>
        </tr>
        <tr>
          <td colspan="2" style="font-size: 12px;width: 22%">Seksi</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->seksi }}</td>
        </tr>
        <tr>
          <td colspan="2" style="font-size: 12px;width: 22%">Departemen</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->departemen }}</td>
        </tr>
        <tr>
          <td colspan="2" style="font-size: 12px;width: 22%">Jabatan</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->jabatan }}</td>
        </tr>
        <tr>
          <td colspan="2" style="font-size: 12px;width: 22%">Rekomendasi Atasan</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->rekomendasi }}</td>
        </tr> -->
        <!-- <tr>
          <td colspan="2">&nbsp;</td>
          <td colspan="7" style="text-align: center;font-weight: bold;font-size: 20px"></td>
        </tr> -->
        <!-- <tr>
          <td colspan="2" style="font-size: 12px;width: 22%">Sub Group</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->ke_sub_group }}</td>
        </tr>
        <tr>
          <td colspan="2" style="font-size: 12px;width: 22%">Group</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->ke_group }}</td>
        </tr>
        <tr>
          <td colspan="2" style="font-size: 12px;width: 22%">Seksi</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->ke_seksi }}</td>
        </tr>
        <tr>
          <td colspan="2" style="font-size: 12px;width: 22%">Departemen</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->ke_departemen }}</td>
        </tr>
        <tr>
          <td colspan="2" style="font-size: 12px;width: 22%">Jabatan</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->ke_jabatan }}</td>
        </tr> -->
        <tr>
          <td colspan="2" style="font-size: 12px;width: 22%; font-weight: bold">Rekomendasi Atasan</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->rekomendasi }}</td>
        </tr> 
        <tr>
          <td colspan="2" style="font-size: 12px;width: 22%; font-weight: bold">Tanggal Mutasi</td>
          <td colspan="10" style="font-size: 12px;">: <?= date('d-M-Y', strtotime($resumes[0]->tanggal)) ?></td>
        </tr>
        <tr>
          <td colspan="2" style="font-size: 12px;width: 22%; font-weight: bold">Alasan</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->alasan }}</td>
        </tr>
            </thead>
        </table>
    <br>
        <table style="width: 100%; font-family: arial; border-collapse: collapse; ">
            <tr style="background-color: rgb(126,86,134);">
                <th colspan="1" style="font-size: 9px; width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">10</th>
                <th colspan="1" style="font-size: 9px; width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">9</th>
                <th colspan="1" style="font-size: 9px; width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">8</th>
                <th colspan="1" style="font-size: 9px; width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">7</th> 
                <th colspan="1" style="font-size: 9px; width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">6</th>
                <th colspan="1" style="font-size: 9px; width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">5</th>
                <th colspan="1" style="font-size: 9px; width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">4</th>
                <th colspan="1" style="font-size: 9px; width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">3</th>
                <th colspan="1" style="font-size: 9px; width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">2</th> 
                <th colspan="1" style="font-size: 9px; width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">1</th>
                </tr>
                <tr style="background-color: rgb(126,86,134);">
                <th colspan="2" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">HR</th>
                <th colspan="4" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">Tujuan</th>
                <th colspan="4" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">Asal</th>
            </tr>
            <tr style="background-color: rgb(126,86,134);">
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">Direktur</th>
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">Manager</th>
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">GM</th>
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">DGM</th>
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">Manager</th>
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">Chief/Foreman</th>
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">GM</th>
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">DGM</th>
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">Manager</th> 
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">Chief/Foreman</th>
            </tr>
            <tr>
                <td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $resumes[0]->nama_direktur_hr }}</td>
                <td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $resumes[0]->nama_manager }}</td>
                <td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $resumes[0]->nama_gm_tujuan }}</td>
                <td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $resumes[0]->nama_dgm_tujuan }}</td>
                <td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $resumes[0]->nama_manager_tujuan }}</td>
                <td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $resumes[0]->nama_chief_tujuan }}</td>
                <td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $resumes[0]->nama_gm_asal }}</td>
                <td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $resumes[0]->nama_dgm_asal }}</td>
                <td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $resumes[0]->nama_manager_asal }}</td>
                <td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $resumes[0]->nama_chief_asal }}</td>
            </tr>
            <tr>
                <td style="width: 6%; border:1px solid black; text-align: center;"><?=$resumes[0]->date_direktur_hr?></td>
                <td style="width: 6%; border:1px solid black; text-align: center;"><?=$resumes[0]->date_manager_hrga?></td>
                <td style="width: 6%; border:1px solid black; text-align: center;"><?=$resumes[0]->date_gm_tujuan?></td>
                <td style="width: 6%; border:1px solid black; text-align: center;"><?=$resumes[0]->date_dgm_tujuan?></td>
                <td style="width: 6%; border:1px solid black; text-align: center;"><?=$resumes[0]->date_manager_tujuan?></td>
                <td style="width: 6%; border:1px solid black; text-align: center;"><?=$resumes[0]->date_atasan_tujuan?></td>
                <td style="width: 6%; border:1px solid black; text-align: center;"><?=$resumes[0]->date_gm_asal?></td>
                <td style="width: 6%; border:1px solid black; text-align: center;"><?=$resumes[0]->date_dgm_asal?></td>
                <td style="width: 6%; border:1px solid black; text-align: center;"><?=$resumes[0]->date_manager_asal?></td>
                <td style="width: 6%; border:1px solid black; text-align: center;"><?=$resumes[0]->date_atasan_asal?></td>
            </tr>
          </table>
            </td>

            <td colspan="3" style="font-size: 16px;width: 25%;">
              <div class="col-md-12">
                <div class="panel panel-default">
                  <input type="hidden" value="{{csrf_token()}}" name="_token" />
                  <input type="hidden"  name="approve" id="approve" value="1" />
                  <div class="panel-heading">Approval : </div>
                  <div class="panel-body center-text"  style="padding: 20px">
                    @if(($user == $resumes[0]->chief_or_foreman_asal || Auth::user()->role_code == "MIS") && $resumes[0]->app_ca == null && $resumes[0]->posisi == "chf_asal")
                    <a href="{{url('approvechief_or_foremanasal/'.$resumes[0]->id)}}" style="color: white"><button class="btn btn-success col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px">Verifikasi</button></a>
                    @elseif(($user == $resumes[0]->manager_asal || Auth::user()->role_code == "MIS") && $resumes[0]->app_ma == null && $resumes[0]->posisi == "mgr_asal")
                    <a href="{{url('approve_manager_asal/'.$resumes[0]->id)}}" style="color: white"><button class="btn btn-success col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px">Verifikasi</button></a>
                    @elseif(($user == $resumes[0]->dgm_asal || Auth::user()->role_code == "MIS") && $resumes[0]->app_da == null && $resumes[0]->posisi == "dgm_asal")
                    <a href="{{url('approve_dgm_asal/'.$resumes[0]->id)}}" style="color: white"><button class="btn btn-success col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px">Verifikasi</button></a>
                    @elseif(($user == $resumes[0]->gm_asal || Auth::user()->role_code == "MIS") && $resumes[0]->app_ga == null && $resumes[0]->posisi == "gm_asal")
                    <a href="{{url('approve_gm_asal/'.$resumes[0]->id)}}" style="color: white"><button class="btn btn-success col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px">Verifikasi</button></a>
                    @elseif(($user == $resumes[0]->chief_or_foreman_tujuan || Auth::user()->role_code == "MIS") && $resumes[0]->app_ct == null && $resumes[0]->posisi == "chf_tujuan")
                    <a href="{{url('approvechief_or_foremantujuan/'.$resumes[0]->id)}}" style="color: white"><button class="btn btn-success col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px">Verifikasi</button></a>
                    @elseif(($user == $resumes[0]->manager_tujuan || Auth::user()->role_code == "MIS") && $resumes[0]->app_mt == null && $resumes[0]->posisi == "mgr_tujuan")
                    <a href="{{url('approve_manager_tujuan/'.$resumes[0]->id)}}" style="color: white"><button class="btn btn-success col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px">Verifikasi</button></a>
                    @elseif(($user == $resumes[0]->dgm_tujuan || Auth::user()->role_code == "MIS") && $resumes[0]->app_dt == null && $resumes[0]->posisi == "dgm_tujuan")
                    <a href="{{url('approve_dgm_tujuan/'.$resumes[0]->id)}}" style="color: white"><button class="btn btn-success col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px">Verifikasi</button></a>
                    @elseif(($user == $resumes[0]->gm_tujuan || Auth::user()->role_code == "MIS") && $resumes[0]->app_gt == null && $resumes[0]->posisi == "gm_tujuan")
                    <a href="{{url('approve_gm_tujuan/'.$resumes[0]->id)}}" style="color: white"><button class="btn btn-success col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px">Verifikasi</button></a>
                    @elseif(($user == $resumes[0]->manager_hrga || Auth::user()->role_code == "MIS") && $resumes[0]->app_m == null && $resumes[0]->posisi == "mgr_hrga")
                    <a href="{{url('approvemanager_hrga/'.$resumes[0]->id)}}" style="color: white"><button class="btn btn-success col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px">Verifikasi</button></a>
                    @elseif(($user == $resumes[0]->direktur_hr || Auth::user()->role_code == "MIS") && $resumes[0]->app_dir == null && $resumes[0]->posisi == "dir_hr")
                    <a href="{{url('approvedirektur_hr/'.$resumes[0]->id)}}" style="color: white"><button class="btn btn-success col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px">Verifikasi</button></a>
                    @else
                    <button class="btn btn-danger col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px"><a href="" style="color: white">Verifikasi</a></button>
                    @endif
                    <br>
                    <br>
                    <a href="{{url('rejectedantar_departemen/'.$resumes[0]->id)}}" style="color: white"><button class="btn btn-danger col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px">Rejected</button></a>
                  </div>
                </div>
              </div>
            </td>
          </tr>
        </table>
    </div>
  </div>
  @else
 <div class="box box-primary">
    <div class="box-body">
        <table class="table table-bordered">
          <tr id="show-att">
            <td>
              <table style="width: 100%; font-family: arial; border-collapse: collapse; text-align: left;">
            <thead>
              <tr>
                <td colspan="13" style="font-weight: bold;font-size: 10px">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
              </tr>
              <tr>
                <td colspan="13"><br></td>
              </tr>       
              <tr>
                <td colspan="13" style="text-align: center;font-weight: bold;font-size: 20px">FORM MUTASI</td>
              </tr>
              <tr>
                <td colspan="13" style="text-align: center;font-weight: bold;font-size: 20px">(Antar Departemen)</td>
              </tr>
              <tr>
                <td colspan="13"><br></td>
              </tr> 
              <tr>
          <td colspan="2" style="font-size: 12px;width: 22%; font-weight: bold">Nama</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->nama }}</td>
        </tr>
        <tr>
          <td colspan="2" style="font-size: 12px;width: 22%; font-weight: bold">NIK</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->nik }}</td>
        </tr>
        <tr>
          <td colspan="2">&nbsp;</td>
          <td colspan="7" style="text-align: center;font-weight: bold;font-size: 20px"></td>
        </tr>
        <tr>
          <th colspan="3" style="font-size: 12px; background-color: green; border: 1px solid black; text-align: center; ">Detail</th>
          <th colspan="5" style="font-size: 12px; background-color: green; border: 1px solid black; text-align: center">Asal</th>
          <th colspan="5" style="font-size: 12px; background-color: green; border: 1px solid black; text-align: center">Tujuan</th>
        </tr>
        <tr align="center">
          <td colspan="3" style="font-size: 12px; border: 1px solid black; font-weight: bold">Sub Group</td>
          <td colspan="5" style="font-size: 12px; border: 1px solid black; ">{{ $resumes[0]->sub_group }}</td>
          <td colspan="5" style="font-size: 12px; border: 1px solid black">{{ $resumes[0]->ke_sub_group }}</td>
        </tr>
        <tr align="center">
          <td colspan="3" style="font-size: 12px; border: 1px solid black; font-weight: bold">Group</td>
          <td colspan="5" style="font-size: 12px; border: 1px solid black">{{ $resumes[0]->group }}</td>
          <td colspan="5" style="font-size: 12px; border: 1px solid black">{{ $resumes[0]->ke_group }}</td>
        </tr>
        <tr align="center">
          <td colspan="3" style="font-size: 12px; border: 1px solid black; font-weight: bold">Seksi</td>
          <td colspan="5" style="font-size: 12px; border: 1px solid black">{{ $resumes[0]->seksi }}</td>
          <td colspan="5" style="font-size: 12px; border: 1px solid black">{{ $resumes[0]->ke_seksi }}</td>
        </tr>
        <tr align="center">
          <td colspan="3" style="font-size: 12px; border: 1px solid black; font-weight: bold">Departemen</td>
          <td colspan="5" style="font-size: 12px; border: 1px solid black">{{ $resumes[0]->departemen }}</td>
          <td colspan="5" style="font-size: 12px; border: 1px solid black">{{ $resumes[0]->ke_departemen }}</td>
        </tr>
        <tr align="center">
          <td colspan="3" style="font-size: 12px; border: 1px solid black; font-weight: bold">Jabatan</td>
          <td colspan="10" style="font-size: 12px; border: 1px solid black">{{ $resumes[0]->jabatan }}</td>
        </tr>
        <tr>
          <td colspan="2">&nbsp;</td>
          <td colspan="11" style="text-align: center;font-weight: bold;font-size: 20px"></td>
        </tr>
        <!-- <tr>
          <td colspan="2" style="font-size: 12px;width: 22%">Sub Group</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->sub_group }}</td>
        </tr>
        <tr>
          <td colspan="2" style="font-size: 12px;width: 22%">Group</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->group }}</td>
        </tr>
        <tr>
          <td colspan="2" style="font-size: 12px;width: 22%">Seksi</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->seksi }}</td>
        </tr>
        <tr>
          <td colspan="2" style="font-size: 12px;width: 22%">Departemen</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->departemen }}</td>
        </tr>
        <tr>
          <td colspan="2" style="font-size: 12px;width: 22%">Jabatan</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->jabatan }}</td>
        </tr>
        <tr>
          <td colspan="2" style="font-size: 12px;width: 22%">Rekomendasi Atasan</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->rekomendasi }}</td>
        </tr> -->
        <!-- <tr>
          <td colspan="2">&nbsp;</td>
          <td colspan="7" style="text-align: center;font-weight: bold;font-size: 20px"></td>
        </tr> -->
        <!-- <tr>
          <td colspan="2" style="font-size: 12px;width: 22%">Sub Group</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->ke_sub_group }}</td>
        </tr>
        <tr>
          <td colspan="2" style="font-size: 12px;width: 22%">Group</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->ke_group }}</td>
        </tr>
        <tr>
          <td colspan="2" style="font-size: 12px;width: 22%">Seksi</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->ke_seksi }}</td>
        </tr>
        <tr>
          <td colspan="2" style="font-size: 12px;width: 22%">Departemen</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->ke_departemen }}</td>
        </tr>
        <tr>
          <td colspan="2" style="font-size: 12px;width: 22%">Jabatan</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->ke_jabatan }}</td>
        </tr> -->
        <tr>
          <td colspan="2" style="font-size: 12px;width: 22%; font-weight: bold">Rekomendasi Atasan</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->rekomendasi }}</td>
        </tr> 
        <tr>
          <td colspan="2" style="font-size: 12px;width: 22%; font-weight: bold">Tanggal Mutasi</td>
          <td colspan="10" style="font-size: 12px;">: <?= date('d-M-Y', strtotime($resumes[0]->tanggal)) ?></td>
        </tr>
        <tr>
          <td colspan="2" style="font-size: 12px;width: 22%; font-weight: bold">Alasan</td>
          <td colspan="10" style="font-size: 12px;">: {{ $resumes[0]->alasan }}</td>
        </tr>
            </thead>
        </table>
    <br>
        <table style="width: 100%; font-family: arial; border-collapse: collapse; ">
            <tr style="background-color: rgb(126,86,134);">
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">10</th>
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">9</th>
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">8</th>
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">7</th> 
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">6</th>
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">5</th>
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">4</th>
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">3</th>
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">2</th> 
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">1</th>
            </tr>
            <tr style="background-color: rgb(126,86,134);">
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">Direktur HRGA</th>
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">Manager HRGA</th>
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">GM Tujuan</th>
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">DGM Tujuan</th>
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">Manager Tujuan</th>
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">Chief/Foreman Tujuan</th>
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">GM Asal</th>
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">DGM Asal</th>
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">Manager Asal</th> 
                <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">Chief/Foreman Asal</th>
            </tr>
            <tr>
                <td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $resumes[0]->nama_direktur_hr }}</td>
                <td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $resumes[0]->nama_manager }}</td>
                <td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $resumes[0]->nama_gm_tujuan }}</td>
                <td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $resumes[0]->nama_dgm_tujuan }}</td>
                <td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $resumes[0]->nama_manager_tujuan }}</td>
                <td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $resumes[0]->nama_chief_tujuan }}</td>
                <td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $resumes[0]->nama_gm_asal }}</td>
                <td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $resumes[0]->nama_dgm_asal }}</td>
                <td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $resumes[0]->nama_manager_asal }}</td>
                <td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $resumes[0]->nama_chief_asal }}</td>
            </tr>
            <tr>
                <td style="width: 6%; border:1px solid black; text-align: center;"><?=$resumes[0]->date_direktur_hr?></td>
                <td style="width: 6%; border:1px solid black; text-align: center;"><?=$resumes[0]->date_manager_hrga?></td>
                <td style="width: 6%; border:1px solid black; text-align: center;"><?=$resumes[0]->date_gm_tujuan?></td>
                <td style="width: 6%; border:1px solid black; text-align: center;"><?=$resumes[0]->date_dgm_tujuan?></td>
                <td style="width: 6%; border:1px solid black; text-align: center;"><?=$resumes[0]->date_manager_tujuan?></td>
                <td style="width: 6%; border:1px solid black; text-align: center;"><?=$resumes[0]->date_atasan_tujuan?></td>
                <td style="width: 6%; border:1px solid black; text-align: center;"><?=$resumes[0]->date_gm_asal?></td>
                <td style="width: 6%; border:1px solid black; text-align: center;"><?=$resumes[0]->date_dgm_asal?></td>
                <td style="width: 6%; border:1px solid black; text-align: center;"><?=$resumes[0]->date_manager_asal?></td>
                <td style="width: 6%; border:1px solid black; text-align: center;"><?=$resumes[0]->date_atasan_asal?></td>
            </tr>
          </table>
          </tr>
        </table>
    </div>
  </div>
  @endif  
@endsection


@section('scripts')

<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<script>
    $(document).ready(function() {
      $("body").on("click",".btn-danger",function(){ 
      $(this).parents(".control-group").remove();
      });
      $('body').toggleClass("sidebar-collapse");
      var id = "{{ $mutasi->id }}";

});
    function loading(){
      $("#loading").show();
    }


    // document.getElementById("myForm").addEventListener("submit", loading);
    
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