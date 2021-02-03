@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<link href="{{ url("css/dropzone.min.css") }}" rel="stylesheet">
<link href="{{ url("css/basic.min.css") }}" rel="stylesheet">
<style type="text/css">
  thead>tr>th{
    text-align:center;
  }
  tbody>tr>td{
    text-align:left;
    padding: 2px;
  }
  tbody>tr>th{
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
    border:1px solid green;
    padding-top: 0;
    padding-bottom: 0;
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid rgb(211,211,211);
  }

  #table_document > tbody > tr > th{
    text-align: center;
    vertical-align: middle;
    border: 1px solid black;
    padding: 2px;
    background-color: #a57cc2;
  }

  #table_document > tbody > tr > td{
    vertical-align: middle;
    text-align: left;
    border: 1px solid black;
  }

  .btn-upload {
    display: none;
  }

  .card-div {
    border: 1px solid #ddd;
    display: inline-block;
    padding: 5px;
    border-radius: 5px;
    margin-left: 2px;
    margin-right: 2px;
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
    <div class="col-xs-12" style="padding-right: 0">
      <div class="box box-solid">
        <div class="box-body">
          <div class="row">
            <div class="col-xs-12">
              <table border="1" style="width: 100%">
                <tr>
                  <td width="20%">
                    <img src="{{ url("waves.jpg") }}" style="width: 100%">
                  </td>
                  <th style="font-size: 20px">
                    3M IMPLEMENTATION REPORT <br>
                    (Machine, Material, Methode/Mechanism)
                  </th>
                  <td width="15%">
                    <table style="font-weight: bold;">
                      <tr><td>No. Dok</td><td>: YMPI/STD/FM/049</td></tr>
                      <tr><td>Tgl</td><td>: 20/11/2014</td></tr>
                      <tr><td>Rev</td><td>: 02</td></tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">No Reff. 3M</td>
                  <td colspan="2">{{ $tiga_m->form_number }}</td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Section</td>
                  <td colspan="2">{{ $proposer[0]->remark }}</td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Name</td>
                  <td colspan="2">{{ $proposer[0]->name }}</td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Date issued 3M</td>
                  <td colspan="2">{{ $tiga_m->date }}</td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Title</td>
                  <td colspan="2">{{ $tiga_m->title }}</td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Isi Perubahan & Alasan Perubahan</td>
                  <td colspan="2"></td>
                </tr>
                <tr>
                  <td colspan="3"><?= $tiga_m->reason ?></td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Tanggal Rencana Perubahan</td>
                  <td colspan="2">{{ $tiga_m->started_date }}</td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Tanggal Aktual Perubahan<span class="text-red">*</span></td>
                  <?php if (Request::segment(6)) { ?>
                    <td colspan="2"><input type="text" name="tgl_aktual" id="tgl_aktual" class="form-control datepicker" placeholder="Pilih Tanggal Aktual Implementasi" value="{{ $implement->actual_date }}" readonly></td>
                  <?php } else { ?>
                    <td colspan="2"><input type="text" name="tgl_aktual" id="tgl_aktual" class="form-control datepicker" placeholder="Pilih Tanggal Aktual Implementasi"></td>
                  <?php } ?>

                </tr>
                <tr>
                  <td colspan="3" style="font-weight: bold;">Data - data Pengecekan Implementasi</td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Tanggal Pengecekan<span class="text-red">*</span></td>
                  <?php if (Request::segment(6)) { ?>
                    <td colspan="2"><input type="text" name="tgl_cek" id="tgl_cek" class="form-control datepicker" placeholder="Pilih Tanggal Pengecekan" value="{{ $implement->check_date }}" readonly></td>
                  <?php } else { ?>
                    <td colspan="2"><input type="text" name="tgl_cek" id="tgl_cek" class="form-control datepicker" placeholder="Pilih Tanggal Pengecekan"></td>
                  <?php } ?>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Yang Melakukan Pengecekan<span class="text-red">*</span></td>
                  <td colspan="2">
                    <?php if (Request::segment(6)) { ?>
                      <input type="text" class="form-control" value="{{ $implement->checker }}" readonly>
                    <?php } else { ?>
                      <select name="pic_cek" id="pic_cek" class="form-control select2" data-placeholder="Pilih PIC Pengecekan" style="width: 100%">
                        <option value=""></option>
                        @foreach($employee as $emps)
                        <option value="{{ $emps->name }}">{{ $emps->name }} ( {{ $emps->position }} )</option>
                        @endforeach
                      </select>
                    <?php } ?>
                  </td>
                </tr>
                <tr>
                  <td colspan="3" style="font-weight: bold;">Dokumen / Alat yang berhubungan dengan implementasi</td>
                </tr>
                <tr>
                  <td colspan="3">
                    <div class="form-group">
                      <label>Lampiran</label> <br>
                      <div id="lampiran_box">
                      </div>
                      <br>
                      <?php if (!Request::segment(6)) { ?>
                        <p><button type="button" class="btn btn-success btn-xs" onclick="add_att()"><i class="fa fa-plus"></i> &nbsp; Tambah</button></p>
                      <?php } else { 
                        $atts = str_replace('"', '', $implement->att);
                        $atts = str_replace('[', '', $atts);
                        $atts = str_replace(']', '', $atts);
                        $atts_arr = explode(',', $atts);

                        foreach ($atts_arr as $att) {
                          echo "<a href='".url('/uploads/sakurentsu/three_m/att/')."/".$att."' target='_blank'>".$att."</a><br>";
                        }
                        ?>
                      <?php } ?>
                      <table>
                        <tr id="lampiran_div">

                        </tr>
                      </table>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td colspan="3">
                    <table style="width: 100%; background-color: white; border: 2px solid black" id="table_document">
                      <tr>
                        <th colspan="10" style="font-size: 14px; padding-top: 3px; padding-bottom: 3px;">
                          関　連　資　料　が　あ　る　場　合　は　添　付　す　る　事　　（試作作業連絡通報・作業連絡通報・その他関連資料）<br>
                          Lampirkan bila ada dokumen terkait (Form informasi trial・Form informasi kerja・dokumen terkait lainnya)
                        </th>
                      </tr>
                      <tr>
                        <th colspan="3">チェックリスト(発議部門記入) <br> Ceklist (Input dept. inisiatif)</th>
                        <th colspan="2">チェック <br> Cek</th>
                        <th>備考（特記事項　等）<br> Note (Note khusus,dll)</th>
                        <th style="width: 10%">完了期日 <br> Target Selesai</th>
                        <th style="width: 10%">完了日 <br> Tanggal Selesai</th>
                        <th style="width: 6%">確認者 <br> PIC</th>
                        <th style="width: 6%">#</th>
                      </tr>
                      <tr>
                        <td style="width: 4%; text-align: center">1</td>
                        <td style="padding-left: 2px; width: 15%">品質確認</td>
                        <td style="padding-left: 2px; width: 15%" id="head_1" class="head_doc">Pengecekan Kualitas</td>
                        <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" name="doc_1" disabled class="doc" value="NEED">要 NEED</label></div></td>
                        <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" name="doc_1" disabled class="doc"  value="NO">不要 NO</label></div></td>
                        <td>
                          <!-- note dokumen -->
                          <p id='note_1'></p>
                        </td>
                        <td>
                          <!-- target selesai -->
                          <p id='target_1'></p>
                        </td>
                        <td> 
                          <!-- tanggal selesai -->
                          <p id='selesai_1'></p>
                        </td>
                        <td>
                          <!-- pic -->
                          <p id='pic_1'></p>
                        </td>
                        <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_1"><i class="fa fa-folder-open"></i>&nbsp;File(s)</button></center></td>
                      </tr>
                      <tr>
                        <td style="text-align: center;">2</td>
                        <td style="padding-left: 2px; width: 15%">コスト確認</td>
                        <td style="padding-left: 2px; width: 15%" id="head_2" class="head_doc">Pengecekan Cost</td>
                        <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" name="doc_2" disabled class="doc" value="NEED">要 NEED</label></div></td>
                        <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" name="doc_2" disabled class="doc" value="NO">不要 NO</label></div></td>
                        <td>
                          <!-- note dokumen -->
                          <p id='note_2'></p>
                        </td>
                        <td>
                          <!-- target selesai -->
                          <p id='target_2'></p>
                        </td>
                        <td> 
                          <!-- tanggal selesai -->
                          <p id='selesai_2'></p>
                        </td>
                        <td>
                          <!-- pic -->
                          <p id='pic_2'></p>
                        </td>
                        <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_2"><i class="fa fa-folder-open"></i>&nbsp;File(s)</button></center></td>
                      </tr>
                      <tr>
                        <td style="text-align: center;">3</td>
                        <td style="padding-left: 2px; width: 15%">関連工程への影響調査</td>
                        <td style="padding-left: 2px; width: 15%" id="head_3" class="head_doc">Investigasi Efek ke Proses Terkait</td>
                        <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" name="doc_3" disabled class="doc" value="NEED">要 NEED</label></div></td>
                        <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" name="doc_3" disabled class="doc" value="NO">不要 NO</label></div></td>
                        <td>
                          <!-- note dokumen -->
                          <p id='note_3'></p>
                        </td>
                        <td>
                          <!-- target selesai -->
                          <p id='target_3'></p>
                        </td>
                        <td> 
                          <!-- tanggal selesai -->
                          <p id='selesai_3'></p>
                        </td>
                        <td>
                          <!-- pic -->
                          <p id='pic_3'></p>
                        </td>
                        <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_3"><i class="fa fa-folder-open"></i>&nbsp;File(s)</button></center></td>
                      </tr>



                      <tr>
                        <th colspan="3">他部門依頼文書 <br> Dokumen Request Dept. Lain</th>
                        <th colspan="2">新設or改訂 <br> New or Revise</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                      </tr>
                      <tr>
                        <td style="text-align: center;">4</td>
                        <td style="padding-left: 2px; width: 15%">製造仕様書の変更</td>
                        <td style="padding-left: 2px; width: 15%" id="head_4" class="head_doc">Perubahan Spec Produksi</td>
                        <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" name="doc_4" disabled class="doc" value="NEED">要 NEED</label></div></td>
                        <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" name="doc_4" disabled class="doc" value="NO">不要 NO</label></div></td>
                        <td>
                          <!-- note dokumen -->
                          <p id='note_4'></p>
                        </td>
                        <td>
                          <!-- target selesai -->
                          <p id='target_4'></p>
                        </td>
                        <td> 
                          <!-- tanggal selesai -->
                          <p id='selesai_4'></p>
                        </td>
                        <td>
                          <!-- pic -->
                          <p id='pic_4'></p>
                        </td>
                        <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_4"><i class="fa fa-folder-open"></i>&nbsp;File(s)</button></center></td>
                      </tr>
                      <tr>
                        <td style="text-align: center;">5</td>
                        <td style="padding-left: 2px; width: 15%">加工図</td>
                        <td style="padding-left: 2px; width: 15%" id="head_5" class="head_doc">Drawing Proses</td>
                        <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" name="doc_5" disabled class="doc" value="NEED">要 NEED</label></div></td>
                        <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" name="doc_5" disabled class="doc" value="NO">不要 NO</label></div></td>
                        <td>
                          <!-- note dokumen -->
                          <p id='note_5'></p>
                        </td>
                        <td>
                          <!-- target selesai -->
                          <p id='target_5'></p>
                        </td>
                        <td> 
                          <!-- tanggal selesai -->
                          <p id='selesai_5'></p>
                        </td>
                        <td>
                          <!-- pic -->
                          <p id='pic_5'></p>
                        </td>
                        <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_5"><i class="fa fa-folder-open"></i>&nbsp;File(s)</button></center></td>
                      </tr>
                      <tr>
                        <td style="text-align: center;">6</td>
                        <td style="padding-left: 2px; width: 15%">検査仕様書</td>
                        <td style="padding-left: 2px; width: 15%" id="head_6" class="head_doc">Form Spec Kensa</td>
                        <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" name ="doc_6" disabled class="doc" value="NEED">要 NEED</label></div></td>
                        <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" name="doc_6" disabled class="doc" value="NO">不要 NO</label></div></td>
                        <td>
                          <!-- note dokumen -->
                          <p id='note_6'></p>
                        </td>
                        <td>
                          <!-- target selesai -->
                          <p id='target_6'></p>
                        </td>
                        <td> 
                          <!-- tanggal selesai -->
                          <p id='selesai_6'></p>
                        </td>
                        <td>
                          <!-- pic -->
                          <p id='pic_6'></p>
                        </td>
                        <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_6"><i class="fa fa-folder-open"></i>&nbsp;File(s)</button></center></td>
                      </tr>
                      <tr>
                        <td style="text-align: center;">7</td>
                        <td style="padding-left: 2px; width: 15%">設備/治工具図</td>
                        <td style="padding-left: 2px; width: 15%" id="head_7" class="head_doc">Drawing Tool Jig/Mesin</td>
                        <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" name="doc_7" disabled class="doc" value="NEED">要 NEED</label></div></td>
                        <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" name="doc_7" disabled class="doc" value="NO">不要 NO</label></div></td>
                        <td>
                          <!-- note dokumen -->
                          <p id='note_7'></p>
                        </td>
                        <td>
                          <!-- target selesai -->
                          <p id='target_7'></p>
                        </td>
                        <td> 
                          <!-- tanggal selesai -->
                          <p id='selesai_7'></p>
                        </td>
                        <td>
                          <!-- pic -->
                          <p id='pic_7'></p>
                        </td>
                        <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_7"><i class="fa fa-folder-open"></i>&nbsp;File(s)</button></center></td>
                      </tr>



                      <tr>
                        <th colspan="3">自部門管理文書 <br> Dokumen Kontrol Dept Internal</th>
                        <th colspan="2">新設or改訂 <br> New or Revise</th>
                        <th>文書Ｎｏ.  <br> Nomor Dokumen</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                      </tr>
                      <tr>
                        <td style="text-align: center;">8</td>
                        <td style="padding-left: 2px; width: 15%">ＱＣ工程表（製造工程管理表）</td>
                        <td style="padding-left: 2px; width: 15%" id="head_8" class="head_doc">QC Kouteihyou (Production Process Control List)</td>
                        <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" name ="doc_8" disabled class="doc" value="NEED">要 NEED</label></div></td>
                        <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio"
                         name="doc_8" disabled class="doc" value="NO">不要 NO</label></div>
                       </td> 
                       <td>
                        <!-- note dokumen -->
                        <p id='note_8'></p>
                      </td>
                      <td>
                        <!-- target selesai -->
                        <p id='target_8'></p>
                      </td>
                      <td> 
                        <!-- tanggal selesai -->
                        <p id='selesai_8'></p>
                      </td>
                      <td>
                        <!-- pic -->
                        <p id='pic_8'></p>
                      </td>
                      <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_8"><i class="fa fa-folder-open"></i>&nbsp;File(s)</button></center></td>
                    </tr>
                    <tr>
                      <td style="text-align: center;">9</td>
                      <td style="padding-left: 2px; width: 15%">作業基準書</td>
                      <td style="padding-left: 2px; width: 15%" id="head_9" class="head_doc">IK Proses</td>
                      <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio"  name="doc_9" disabled class="doc" value="NEED">要 NEED</label></div></td>
                      <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" disabled name="doc_9" class="doc" value="NO">不要 NO</label></div></td>
                      <td>
                        <!-- note dokumen -->
                        <p id='note_9'></p>
                      </td>
                      <td>
                        <!-- target selesai -->
                        <p id='target_9'></p>
                      </td>
                      <td> 
                        <!-- tanggal selesai -->
                        <p id='selesai_9'></p>
                      </td>
                      <td>
                        <!-- pic -->
                        <p id='pic_9'></p>
                      </td>
                      <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_9"><i class="fa fa-folder-open"></i>&nbsp;File(s)</button></center></td>
                    </tr>
                    <tr>
                      <td style="text-align: center;">10</td>
                      <td style="padding-left: 2px; width: 15%">検査基準書</td>
                      <td style="padding-left: 2px; width: 15%" id="head_10" class="head_doc">IK Kensa</td>
                      <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" name="doc_10" disabled class="doc" value="NEED">要 NEED</label></div></td>
                      <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" disabled name="doc_10" class="doc" value="NO">不要 NO</label></div></td>
                      <td>
                        <!-- note dokumen -->
                        <p id='note_10'></p>
                      </td>
                      <td>
                        <!-- target selesai -->
                        <p id='target_10'></p>
                      </td>
                      <td> 
                        <!-- tanggal selesai -->
                        <p id='selesai_10'></p>
                      </td>
                      <td>
                        <!-- pic -->
                        <p id='pic_10'></p>
                      </td>
                      <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_10"><i class="fa fa-folder-open"></i>&nbsp;File(s)</button></center></td>
                    </tr>
                    <tr>
                      <td style="text-align: center;">11</td>
                      <td style="padding-left: 2px; width: 15%">設備点検基準書</td>
                      <td style="padding-left: 2px; width: 15%" id="head_11" class="head_doc">IK Pengecekan Mesin</td>
                      <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" name="doc_11" disabled class="doc" value="NEED">要 NEED</label></div></td>
                      <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" disabled name="doc_11" class="doc" value="NO">不要 NO</label></div></td>
                      <td>
                        <!-- note dokumen -->
                        <p id='note_11'></p>
                      </td>
                      <td>
                        <!-- target selesai -->
                        <p id='target_11'></p>
                      </td>
                      <td> 
                        <!-- tanggal selesai -->
                        <p id='selesai_11'></p>
                      </td>
                      <td>
                        <!-- pic -->
                        <p id='pic_11'></p>
                      </td>
                      <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_11"><i class="fa fa-folder-open"></i>&nbsp;File(s)</button></center></td>
                    </tr>
                    <tr>
                      <td style="text-align: center;">12</td>
                      <td style="padding-left: 2px; width: 15%">治工具点検基準書</td>
                      <td style="padding-left: 2px; width: 15%" id="head_12" class="head_doc">IK pengecekan Tool Jig</td>
                      <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" name="doc_12" disabled class="doc" value="NEED">要 NEED</label></div></td>
                      <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" disabled name="doc_12" class="doc" value="NO">不要 NO</label></div></td>
                      <td>
                        <!-- note dokumen -->
                        <p id='note_12'></p>
                      </td>
                      <td>
                        <!-- target selesai -->
                        <p id='target_12'></p>
                      </td>
                      <td> 
                        <!-- tanggal selesai -->
                        <p id='selesai_12'></p>
                      </td>
                      <td>
                        <!-- pic -->
                        <p id='pic_12'></p>
                      </td>
                      <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_12"><i class="fa fa-folder-open"></i>&nbsp;File(s)</button></center></td>
                    </tr>


                    <tr>
                      <th colspan="6">その他 <br> DLL</th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                    </tr>
                    <tr>
                      <td style="text-align: center;">13</td>
                      <td style="padding-left: 2px; width: 15%">協力工場品質取決</td>
                      <td style="padding-left: 2px; width: 15%" id="head_13" class="head_doc">Kesepakatan Kualitas Vendor</td>
                      <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" name="doc_13" disabled class="doc" value="NEED">要 NEED</label></div></td>
                      <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" disabled name="doc_13" class="doc" value="NO">不要 NO</label></div></td>
                      <td>
                        <!-- note dokumen -->
                        <p id='note_13'></p>
                      </td>
                      <td>
                        <!-- target selesai -->
                        <p id='target_13'></p>
                      </td>
                      <td> 
                        <!-- tanggal selesai -->
                        <p id='selesai_13'></p>
                      </td>
                      <td>
                        <!-- pic -->
                        <p id='pic_13'></p>
                      </td>
                      <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_13"><i class="fa fa-folder-open"></i>&nbsp;File(s)</button></center></td>
                    </tr>
                    <tr>
                      <td style="text-align: center;">14</td>
                      <td style="padding-left: 2px; width: 15%">製品仕様確認（SPEC）</td>
                      <td style="padding-left: 2px; width: 15%" id="head_14" class="head_doc">Pengecekan spec produk (SPEC)</td>
                      <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" name="doc_14" disabled class="doc" value="NEED">要 NEED</label></div></td>
                      <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" disabled name="doc_14" class="doc" value="NO">不要 NO</label></div></td>
                      <td>
                        <!-- note dokumen -->
                        <p id='note_14'></p>
                      </td>
                      <td>
                        <!-- target selesai -->
                        <p id='target_14'></p>
                      </td>
                      <td> 
                        <!-- tanggal selesai -->
                        <p id='selesai_14'></p>
                      </td>
                      <td>
                        <!-- pic -->
                        <p id='pic_14'></p>
                      </td>
                      <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_14"><i class="fa fa-folder-open"></i>&nbsp;File(s)</button></center></td>
                    </tr>
                    <tr>
                      <td style="text-align: center;">15</td>
                      <td style="padding-left: 2px; width: 15%">薬品確認（MSDS等）</td>
                      <td style="padding-left: 2px; width: 15%" id="head_15" class="head_doc">Pengecekan chemical (MSDS)</td>
                      <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" name="doc_15" disabled class="doc" value="NEED">要 NEED</label></div></td>
                      <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" disabled name="doc_15" class="doc" value="NO">不要 NO</label></div></td>
                      <td>
                        <!-- note dokumen -->
                        <p id='note_15'></p>
                      </td>
                      <td>
                        <!-- target selesai -->
                        <p id='target_15'></p>
                      </td>
                      <td> 
                        <!-- tanggal selesai -->
                        <p id='selesai_15'></p>
                      </td>
                      <td>
                        <!-- pic -->
                        <p id='pic_15'></p>
                      </td>
                      <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_15"><i class="fa fa-folder-open"></i>&nbsp;File(s)</button></center></td>
                    </tr>
                    <tr>
                      <td style="text-align: center;">16</td>
                      <td style="padding-left: 2px; width: 15%">リスクアセスメント</td>
                      <td style="padding-left: 2px; width: 15%" id="head_16" class="head_doc">Risk assesment</td>
                      <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" name="doc_16" disabled class="doc" value="NEED">要 NEED</label></div></td>
                      <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" disabled name="doc_16" class="doc" value="NO">不要 NO</label></div></td>
                      <td>
                        <!-- note dokumen -->
                        <p id='note_16'></p>
                      </td>
                      <td>
                        <!-- target selesai -->
                        <p id='target_16'></p>
                      </td>
                      <td> 
                        <!-- tanggal selesai -->
                        <p id='selesai_16'></p>
                      </td>
                      <td>
                        <!-- pic -->
                        <p id='pic_16'></p>
                      </td>
                      <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_16"><i class="fa fa-folder-open"></i>&nbsp;File(s)</button></center></td>
                    </tr>
                    <tr>
                      <td style="text-align: center;">17</td>
                      <td style="padding-left: 2px; width: 15%">環境影響（ISO14001）</td>
                      <td style="padding-left: 2px; width: 15%" id="head_17" class="head_doc">Efek lingkungan (ISO 14001)</td>
                      <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" name="doc_17" disabled class="doc" value="NEED">要 NEED</label></div></td>
                      <td style="text-align: center; width: 6%; font-size: 12px; font-weight:bold"><div><label class="radio-inline"><input type="radio" disabled name="doc_17" class="doc" value="NO">不要 NO</label></div></td>
                      <td>
                        <!-- note dokumen -->
                        <p id='note_17'></p>
                      </td>
                      <td>
                        <!-- target selesai -->
                        <p id='target_17'></p>
                      </td>
                      <td> 
                        <!-- tanggal selesai -->
                        <p id='selesai_17'></p>
                      </td>
                      <td>
                        <!-- pic -->
                        <p id='pic_17'></p>
                      </td>
                      <td><center><button class="btn btn-xs btn-upload btn-primary" type="button" id="doc_17"><i class="fa fa-folder-open"></i>&nbsp;File(s)</button></center></td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <?php if (Request::segment(6) && Request::segment(6) == 'proposer') { ?>
                  <td colspan="3">
                    <button class="btn btn-success btn-lg" style="width: 100%" onclick="verifikasi('proposer', '')"><i class="fa fa-check"></i> &nbsp;VERIFY</button>
                  </td>
                <?php } else if (Request::segment(6) && Request::segment(6) == 'verify') { ?>
                  <td colspan="3">
                    <button class="btn btn-success btn-lg" style="width: 100%" onclick="verifikasi('department', '')"><i class="fa fa-check"></i> &nbsp;VERIFY</button>
                  </td>
                <?php } else if (Request::segment(6) && Request::segment(6) == 'dgm') { ?>
                  <td colspan="3">
                    <button class="btn btn-success btn-lg" style="width: 100%" onclick="verifikasi('dgm', 'PI0109004')"><i class="fa fa-check"></i> &nbsp;VERIFY</button>
                  </td>
                <?php } else if (Request::segment(6) && Request::segment(6) == 'gm') { ?>
                  <td colspan="3">
                    <button class="btn btn-success btn-lg" style="width: 100%" onclick="verifikasi('gm', 'PI1206001')"><i class="fa fa-check"></i> &nbsp;APPROVE</button>
                  </td>
                <?php } else if (Request::segment(6) && Request::segment(6) == 'std') { ?>
                  <td colspan="3">
                    <button class="btn btn-success btn-lg" style="width: 100%" onclick="verifikasi('std', 'PI0904001')"><i class="fa fa-check"></i> &nbsp;RECEIVE</button>
                  </td>
                <?php } else if (Request::segment(6) && Request::segment(6) == 'view') { ?>
                  <td colspan="3">
                    <table style="width: 100%" border="1">
                      <?php 
                      $appr = $imp_sign;
                      $department = explode(',', $tiga_m->related_department);
                      $heads = "";
                      $ttd = "";
                      $namas = "";
                      $dates = "";
                      $prop = array();

                      foreach ($department as $dept) {
                        $stat = 0;
                        foreach ($appr as $apr) {
                          if ($apr->approver_department == $dept && is_null($apr->remark)) {
                            $heads .= '<td style="width: 10%; font-weight:bold; background-color: #a57cc2"><center>Check By</center></td>';
                            $ttd .= '<td style="width: 10%"><center><img width="70" src="'.url("files/ttd").'/'.$apr->approver_id.'.png" style="padding: 0"></center></td>';
                            $namas .= '<td style="width: 10%; text-align: center">'.$apr->approver_name.'</td>';
                            $app_date2 = explode(' ', $apr->approve_at)[0];
                            $dates .= '<td style="width: 10%">Date:&nbsp;&nbsp; '.$app_date2.'</td>';
                            $stat = 1;
                          } else if ($apr->remark && empty($prop)) {
                            $app_date = explode(' ', $apr->approve_at)[0];
                            array_push($prop, [$apr->approver_id, $apr->approver_name, $app_date]);
                          }
                        }

                        if ($stat == 0) {
                          $heads .= '<td style="width: 10%; font-weight:bold; background-color: #a57cc2"><center>Check By</center></td>';
                          $ttd .= '<td style="width: 10%"><br><br><br></td>';
                          $namas .= '<td style="width: 10%">&nbsp;</td>';
                          $dates .= '<td style="width: 10%">Date: </td>';
                        }

                      }

                      $heads .= '<td style="width: 10%; font-weight:bold; background-color: #a57cc2"><center>Made By Proposer</center></td>';
                      $ttd .= '<td style="width: 10%"><center><img width="70" src="'.url("files/ttd").'/'.$prop[0][0].'.png" style="padding: 0"></center></td>';
                      $namas .= '<td style="width: 10%; text-align:center">'.$prop[0][1].'</td>';
                      $dates .= '<td style="width: 10%">Date:&nbsp;&nbsp; '.$prop[0][2].'</td>';

                      ?>

                      <tr><?php echo $heads ?></tr>
                      <tr><?php echo $ttd ?></tr>
                      <tr><?php echo $namas ?></tr>
                      <tr><?php echo $dates ?></tr>
                    </table>
                  </td>
                <?php } else { ?>
                  <td colspan="3">
                    <button class="btn btn-success btn-lg" style="width: 100%" onclick="save_send()"><i class="fa fa-check"></i> &nbsp;SIMPAN & KIRIM EMAIL KE DEPARTEMEN TERKAIT</button>
                  </td>
                <?php } ?>
              </tr>
              <tr>
                <td colspan="3">&nbsp;</td>
              </tr>
              <?php if (Request::segment(6) && Request::segment(6) == 'view') { ?>
                <tr>
                  <td colspan="3">
                    <table style="width: 100%" border="1">
                      <tr>
                        <td rowspan="3">
                          Status : <br>
                          <input type="checkbox" name="check1" value="CLose">Close
                          <input type="checkbox" name="check1" value="Open">Open
                        </td>
                        <td style="background-color: #a57cc2; font-weight: bold; text-align: center;">Stamp date by STD</td>
                        <td style="background-color: #a57cc2; font-weight: bold; text-align: center;">Received</td>
                        <td style="background-color: #a57cc2; font-weight: bold; text-align: center;">Approved by</td>
                        <td style="background-color: #a57cc2; font-weight: bold; text-align: center;">Checked by</td>
                      </tr>
                      <tr>
                        <td rowspan="2" id="stamp_std"><br><br><br></td>
                        <td id="sign_std"><br><br><br></td>
                        <td id="sign_gm"><br><br><br></td>
                        <td id="sign_dgm"><br><br><br></td>
                      </tr>
                      <tr>
                        <td><center>Standardization</center></td>
                        <td><center>GM Production</center></td>
                        <td><center>DGM Production</center></td>
                      </tr>
                      <tr>
                        <td colspan="2"></td>
                        <td>Date : </td>
                        <td>Date : </td>
                        <td>Date : </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              <?php } ?>

            </table>
          </div>
        </div>
      </div>
    </div>
  </div>


  <div class="modal fade" id="modalFile">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="doc_desc"></h4>
          <div class="modal-body table-responsive no-padding" style="min-height: 100px">
            <table class="table table-hover table-bordered table-striped" id="tableFile">
              <tbody id='bodyFile'></tbody>
            </table>
            <!-- <iframe id="my_iframe" name="my_iframe" height="0" width="0" frameborder="0" scrolling="yes"></iframe> -->

           <!--  <form method="post" enctype="multipart/form-data" action="{{ url("upload/sakurentsu/3m/document") }}" id="form_upload">
              <label>Upload file(s)</label>
              <input type="file" name="doc_upload" id="doc_upload" multiple="">
              <input type="hidden" name="text_doc_upload" id="text_doc_upload">
              <input type="hidden" name="id_doc_upload" id="id_doc_upload">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <br>
              <button class="btn btn-success btn-sm" style="width: 100%" type="button"><i class="fa fa-plus"></i>&nbsp; Upload</button>

            </form> -->
            <button class="btn btn-danger btn-sm pull-right"><i class="fa fa-close"></i>&nbsp; Close</button>
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
<script src="{{ url("js/dropzone.min.js") }}"></script>
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

    $(".select2").select2();


    $('.datepicker').datepicker({
      autoclose: true,
      format: "yyyy-mm-dd",
      todayHighlight: true
    });
  });

  $( window ).on( "load", function() {
   $("input:radio").prop("checked", false);

   fillData();   

   // stamp_std
   // sign_std
   // sign_gm
   // sign_dgm

 });

  function add_att() {
    isi =  '<tr><td style="padding-right:3px"><input name="file[]" type="file" class="lampiran" multiple></td><td><button type="button" class="btn btn-danger btn-xs" onclick="delete_file(this)"><i class="fa fa-close"></i></button></td></tr>';

    $("#lampiran_div").append(isi);
  }

  function delete_file(elem) {
    $(elem).closest("tr").remove();
  }

  function fillData() {
    var docs = <?php echo json_encode($doc_tiga_m); ?>;
    var att = <?php echo json_encode($tiga_m) ?>;

    $('.head_doc').each(function(index, value2) {
      var stat = 0;
      var ido = $(this).attr('id');
      var num = ido.split('_')[1];
      var radios = $("input[name='doc_"+num+"']");

      if (docs.length > 0) {
        $.each(docs, function(key, value1) {
          if (value2.textContent == value1.document_name) {
            if(radios.is(':checked') === false) {
              stat = 1;
              radios.filter('[value=NEED]').prop('checked', true);
              radios.filter('[value=NEED]').parent().parent().css("border", "2px solid red");
              radios.filter('[value=NEED]').parent().parent().css("border-radius", "5px");
              $("#doc_"+num).css('display', 'block');
              $("#note_"+num).text(value1.document_description);
              $("#target_"+num).text(value1.target);
              $("#selesai_"+num).text(value1.finish);
              $("#pic_"+num).text(value1.pic);
            }
          }
        })
      }
      if (stat == 0) {
        radios.filter('[value=NO]').prop('checked', true);
        radios.filter('[value=NO]').parent().parent().css("border", "2px solid red");
        radios.filter('[value=NO]').parent().parent().css("border-radius", "5px");

      }
    })

    $("#lampiran_box").empty();

    if (att.att) {
      att_arr = att.att.split("|");
      var att = "";

      $.each(att_arr, function(key, value) {
        att += '<a href="'+'{{ url("/uploads/sakurentsu/three_m/att/") }}/'+value+'" target="_blank">';
        att += '<div class="card-div">';
        att += '<i class="fa fa-file-o"></i> &nbsp;'+value;
        att += '</div></a>';
      })

      $("#lampiran_box").append(att);
    }
  }

  $(".btn-upload").click(function() {
    var ido = $(this).attr('id').split('_')[1];
    var text = $("#head_"+ido).text();

    var data = {
      id : "{{ Request::segment(5) }}",
      doc_desc : text
    }

    $("#doc_desc").text(text);
    $("#text_doc_upload").val(text);
    $("#id_doc_upload").val("{{ Request::segment(5) }}");

    $.get('{{ url("fetch/sakurentsu/3m/document") }}', data, function(result, status, xhr){
      $("#bodyFile").empty();
      if (result.status) {
        body_file = "";
        console.log(result.docs);
        $.each(result.docs, function(key, value) {  
          body_file += "<tr>";
          body_file += "<td>";
          body_file += "<a href='"+"{{ url('uploads/sakurentsu/three_m/doc/') }}/"+value.file_name+"' target='_blank'><i class='fa fa-file-pdf-o'></i> "+value.file_name+"</a>";
          body_file += "</td>";
          body_file += "</tr>";
        });

        $("#bodyFile").append(body_file);

        $("#modalFile").modal('show');
      } else {
        $("#modalFile").modal('show');
      }
    })
  });

  function save_send() {
    if (confirm('Apakah anda yakin untuk menyimpan form implementasi ini dan mengirimnya ke departemen terkait?')) {
      var formData = new FormData();
      formData.append('id', '{{ Request::segment(5) }}');
      formData.append('actual_date', $("#tgl_aktual").val());
      formData.append('check_date', $("#tgl_cek").val());
      formData.append('checker', $("#pic_cek").val());

      $.each($('input[name="file[]"]'),function(i, obj) {
        $.each(obj.files,function(j,file){
          formData.append('file['+i+']['+j+']', file);
        })
      });

      var url = "{{ url('post/sakurentsu/3m/implement')}}";

      $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        success: function (response) {
          openSuccessGritter('Success', 'Form Implementasi Berhasil dibuat dan dikirimkan');
          // setTimeout( function() {window.location.replace("{{ url('index/sakurentsu/list_3m') }}")}, 3000);
        },
        error: function (response) {
          openErrorGritter('Error', response.message);
          console.log(response.message);
        },
        contentType: false,
        processData: false
      });
    }
  }

  function verifikasi(position, sign) {
    var sign_user = sign;
    if (position == 'department' || position == 'proposer') {
      var AuthUser = "{{{ (Auth::user()) ? Auth::user()->username : null }}}";
      if (!AuthUser) {
        window.open('{{ url("/") }}', '_blank');
        return false;
      } else {
        sign_user = AuthUser;
      }
    }

    var data = {
      form_id : "{{ Request::segment(5) }}",
      position : position,
      sign : [sign_user]
    }

    $.post('{{ url("post/sakurentsu/3m/implementation/sign") }}', data, function(result, status, xhr){
      if (result.status) {
        openSuccessGritter('Success','Sign has been Saved');

        setTimeout( function() {window.location.replace("{{ url('index/sakurentsu/3m/implement/'.Request::segment(5).'/view') }}")}, 2000);
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
