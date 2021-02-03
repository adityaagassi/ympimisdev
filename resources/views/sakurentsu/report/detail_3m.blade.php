@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link href="{{ url("css/dropzone.min.css") }}" rel="stylesheet">
<link href="{{ url("css/basic.min.css") }}" rel="stylesheet">
<style type="text/css">
  td:hover {
    overflow: visible;
  }
  table > tbody > tr > td {
    padding: 2px;
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

  #table_disribusi > tbody > tr > th{
    text-align: center;
    vertical-align: middle;
    border: 1px solid black;
    padding: 2px;
  }

  #table_disribusi > tbody > tr > td{
    padding: 1vw 1vw 0 1vw;
    vertical-align: top;
    text-align: left;
    border: 1px solid black;
  }

  #table_document > tbody > tr > th{
    text-align: center;
    vertical-align: middle;
    border: 1px solid black;
    padding: 2px;
  }

  #table_document > tbody > tr > td{
    vertical-align: middle;
    text-align: left;
    border: 1px solid black;
  }

  h3 {
    margin-top: 10px;
    margin-bottom: 5px;
  }

  #loading { display: none; }

  .btn-upload {
    display: none;
  }

  .round {
    border: 2px solid red;
    border-radius: 10px;
  }
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
  <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <div class="col-xs-12" style="background-color: white; padding: 15px; width: 98%; left: 1%;">

      <div class="col-xs-12">
        <table style="width: 100%; background-color: white; border: 2px solid black" border="1">
          <!-- ###################################################         HEAD         ############################################# -->
          <tr>
            <td colspan="8"></td>
            <td>
              No. Dok. <br>
              Tanggal <br>
              Rev <br>
            </td>
            <td colspan="2" width="8%">
              : YMPI/STD/FM/037 <br>
              : 01 Sept 2020 <br>
              : 22 <br>
            </td>
          </tr>
          <tr>
            <td colspan="6" rowspan="2" style="font-size: 18pt;" style="width: 30%">
              <center>
                <b>
                  3Ｍ変更 申請書 (Form Aplikasi Perubahan 3M) <br>
                  3Ｍ変更 連絡通報 (Form Informasi Perubahan 3M)
                </b>
              </center>
            </td>
            <td colspan="5" style="font-size: 11pt">
              <center>
                <b>
                  回覧 / 配付　（下記レ印の所）   <br>                                  
                  Sirkulasi/Distribusi (Beri centang dibawah ini)
                </b>
              </center>
            </td>
          </tr>
          <tr>
            <td width="10%"><b>・YMPI <br></b></td>
            <td width="12%"><b>YMMJ <br></b></td>
            <td colspan="2" style="width: 1%">
              <b>
                アコ開発部　B&O開発グループ  <br> 
                Div.Acoustic Development B&O Development Group <br></td>
              </b>
              <td width="12%"><b>アコ生産 <br> Accoustic Prod <br></b></td>
            </tr>
            <tr>

            </tr>
            <tr>
              <td colspan="2">
                通報発行 <br>
                Report Issued <br>
                年月日 <br>    
                Date 
              </td>
              <td colspan="4">
                No. <span style="font-weight: bold; font-size: 17px">{{ $data->form_number }}</span><br>
                <?php if ($data->date) $date = explode('-', $data->date); else $date = '---'?>                
                Tahun 年　<span style="font-weight: bold; font-size: 15pt">{{ $date[0] }}</span>　Bulan 月  <span style="font-weight: bold; font-size: 15pt">{{ $date[1] }}</span>   Hari 日      <span style="font-weight: bold; font-size: 15pt">{{ $date[2] }}</span>
              </td>
              <td rowspan="3" style="width: 10%; vertical-align: top; font-size: 10pt">
                <input type="checkbox"> 生産部 <br>
                Prod. Div <br>
                <input type="checkbox"> 生産技術部 <br>
                PE Div <br>
                <input type="checkbox"> 生産支援部 <br>
                Prod. Support div
              </td>
              <td rowspan="5" style="width: 10%; vertical-align: top; font-size: 10pt">
                <input type="checkbox"> 管理室 <br>
                Control Division <br>
                <input type="checkbox"> QC技術課 <br>
                QC Engineering Department <br>
                <input type="checkbox"> 購買課 <br>
                Purchasing Department <br>
                <input type="checkbox"> 生産管理課 <br>
                Production Control Department <br>
                <input type="checkbox"> 部品生産課 <br>
                Parts Prod Department <br>
                <input type="checkbox"> 木管生産課 <br>
                BI Prod Department
              </td>
              <td rowspan="3" colspan="2" style="width: 10%; vertical-align: top; font-size: 10pt">
                <input type="checkbox"> 木管楽器チーム <br>
                Wind Musical Instrument Team <br>
                <input type="checkbox"> 金管楽器チーム <br>
                Brass Musical Instrument Team <br>
                <input type="checkbox"> CAPチーム <br>
                CAP Team
              </td>
              <td rowspan="3" style="width: 10%; vertical-align: top; font-size: 10pt">
                <input type="checkbox"> 生産企画G <br>
                Prod Planning G <br>
                <input type="checkbox"> 生産技術G <br>
                Prod Engineering G <br>
                <input type="checkbox"> 部品調達G <br>
                Parts Sourcing G <br>
                <input type="checkbox"> 木材調達G <br>
                Wood Sourcing G <br>
                <input type="checkbox"> 購買管理G <br>
                Purchasing Control Group
              </td>
            </tr>
            <tr style="font-size: 8pt; text-align: center;">
            <!-- <td width="10%">
              Leader dept BI <br> (PresDir)
            </td>
            <td width="10%">
              Pihak yang Bertanggung Jawab
            </td>
            <td width="10%">
              Pihak yang Bertanggung Jawab
            </td>
            <td colspan="3" style="width: 10%">
              Reception Sign
            </td> -->

            <td colspan="6">
              <table style="margin: 0px; font-size: 8pt; width: 100%" border="1">
                <tr id="table_master_sign">
                  <td style="width: 20%">
                    生産部門長 <br>
                    Leader dept BI (PresDir)
                  </td>
                  <td style="width: 20%">
                    開発責任者 <br>
                    Pihak yang Bertanggung Jawab
                  </td>
                  <td style="width: 20%">
                    開発責任者 <br>
                    Pihak yang Bertanggung Jawab
                  </td>
                  <td style="width: 20%">
                    開発責任者 <br>
                    Pihak yang Bertanggung Jawab
                  </td>
                  <td>
                    受付印 <br>
                    Reception Sign
                  </td>
                </tr>
              </table>
            </td>

          </tr>
          <tr style="font-size: 8pt;">
            <!-- <td style="vertical-align: bottom;" id="sign_presdir">
              <br><br><br><br> Date: <br> Approval
            </td>
            <td style="vertical-align: bottom;" id="sign_gm_prod">
              <br><br><br><br> Date: <br> GM Produksi
            </td>
            <td style="vertical-align: bottom;" id="sign_dgm_prod">
              <br><br><br><br> Date: <br> DGM Produksi
            </td>
            <td colspan="3">

            </td> -->

            <td colspan="6">
              <table style="margin: 0px; font-size: 9pt; width: 100%" border="1">
                <tr id="table_master_name">
                 <td style="vertical-align: bottom; width: 20%" id="sign_presdir">
                   <br><br><br><br> Date: <br> 承認 Approval
                 </td>
                 <td style="vertical-align: bottom; width: 20%" id="sign_gm_prod_supp">
                  <br><br><br><br> Date: <br> 生産支援部長 GM Prod.Support 
                </td>
                <td style="vertical-align: bottom; width: 20%" id="sign_gm_prod">
                  <br><br><br><br> Date: <br> 生産部長 GM Produksi 
                </td>
                <td style="vertical-align: bottom; width: 20%" id="sign_dgm_prod">
                  <br><br><br><br> Date: <br> 生産副部長 DGM Produksi
                </td>
                <td>
                  <div style="border: 2px solid red; height: 100%; color: red; display: none" id="sign_std"></div>
                </td>
              </tr>
            </table>
          </td>

        </tr>
        <tr>
          <td colspan="6" rowspan="2">
            <table style="margin: 0px; font-size: 9pt; width: 100%;">
              <tr id="table_sign_list">
              </tr>
            </table>
          </td>
          <td rowspan="2" style="font-size: 10pt; vertical-align: top">
            <b>
              ・海外工場 <br>
              Overseas factory <br><br>
              <input type="checkbox"> XY
            </b>
          </td>
          <td colspan="2" rowspan="2"></td>
          <td style="font-size: 10pt">
            <b>
              <input type="checkbox"> 生産本部生産技術・化学技術G <br>
              Prod. Group Prod. Engineering-Chemical Engineering G 
            </b>
          </td>
        </tr>
        <tr>
          <td style="font-size: 10pt">
            ※品質保証部門は配布必須 <br>
            <input type="checkbox"> Perlu distribusi bagian QA
          </td>
        </tr>
        <!-- <tr><td style="border: none" colspan="9">&nbsp;</td></tr> -->
        <!-- ###################################################         BODY         ############################################# -->
        <tr>
          <td rowspan="2" style="font-size: 9pt;">
            製品名 <br>
            Nama Produk <br>
            設備名 <br>
            Nama Mesin
          </td>
          <td colspan="3" rowspan="2" width="40%"><center><?= $data->product_name ?></center></td>
          <td rowspan="2" style="font-size: 9pt;" width="10%">
            工程名 <br>
            Nama Proses
          </td>
          <td colspan="4" rowspan="2" width="40%"><center><?= $data->proccess_name ?></center></td>
          <td colspan="2" style="vertical-align: top; text-align: center; font-size: 8pt; width: 8%">
            班　名 <br>
            Nama Unit
          </td>
        </tr>
        <tr>
          <td colspan="2" style="vertical-align: bottom; text-align: center; font-size: 10pt;">
            <?= $data->unit ?>
            <span class="pull-right">
              班 <br>
              Unit
            </span>
          </td>
        </tr>
        <tr>
          <td rowspan="2" style="font-size: 9pt;">
            件名 <br>
            Judul
          </td>
          <td colspan="8" rowspan="2">
            <center>
              <?= $data->title ?>
            </center>
          </td>
          <td colspan="2" style="font-size: 9pt; text-align: center">
            3M変更区分 <br>
            Klasifikasi Perubahan 3M
          </td>
        </tr>
        <tr style="font-size: 9pt; text-align: center">
          <td colspan="2" rowspan="2">
            <div id="Metode" <?php if($data->category == "Metode") echo "class='round'" ?> >1．  工法-Metode </div>
            <div id="Material" <?php if($data->category == "Material") echo "class='round'" ?>>2．  材料-Material </div>
            <div id="Mesin" <?php if($data->category == "Mesin") echo "class='round'" ?>>3．  設備-Mesin </div>
          </td>
        </tr>
        <tr>
          <td colspan="9" style="border-bottom: none">
            <b>
              【　変更内容・変更理由　】 <br>
              Isi & Alasan Perubahan
            </b>
          </td>
        </tr>
        <tr>
          <td colspan="9" style="border-top: none; border-bottom: none"></td>
          <td colspan="2" style="font-size: 9pt; text-align: center;">
            作業者変更は課内で記録 <br>
            Perubahan Operator Dilakukan Record Internal
          </td>
        </tr>
        <tr>
          <td colspan="11" style="border-top: none"><?= $data->reason ?></td>
        </tr>
        <tr>
          <td colspan="11">
            <p>
              <b>
                【　変更することによるメリット　】 <br>
                Keuntungan perubahan
              </b>
            </p>
            <?= $data->benefit ?>
          </td>
        </tr>
        <tr>
          <td colspan="11">
            <p>
              <b>
                【　事前の品質確認　（日時・方法・数量・確認者等）　】 <br>
                Pengecekan kualitas sebelumnya (Tgl・metode・jumlah・pengecek,dll) <br>
              </b>
            </p>
            <?= $data->check_before ?>
          </td>
        </tr>
        <tr>
          <td colspan="11">
            <p>
              <b>
                【　開始日・切替予定日　※事後申請となった場合はその理由　】 <br>
                Tanggal mulai・Tgl rencana perubahan   ※alasan bila menjadi after request <br>
              </b>
            </p>
            <?= $data->started_date ?>
          </td>
        </tr>
        <tr>
          <td colspan="11">
            <p>
              <b>
                【　特記事項　】 <br>
                Item khusus <br>
              </b>
            </p>
            <?= $data->special_item ?>
          </td>
        </tr>
        <tr>
          <td colspan="11">
            <p>
              <b>
                【　BOM変更　】 <br>
                Perubahan BOM <br>
              </b>
            </p>
            <?php $bom_jp = ["Ada" => "有り", "Tidak Ada" => "無し"] ?>
            <?= $data->bom_change ?> <?php echo " ".$bom_jp[$data->bom_change] ?>
          </td>
        </tr>
      </table>

      <!--  //////////////////////////////////////////         TABEL DOKUMEN                 ////////////////////////////////////////// -->
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
      <!-- ////////////////////////////     NOTE     ///////////////////////-->
      <tr>
        <td style="text-align: center;" rowspan="2">備考 <br> Note</td>
        <td colspan="7">
          ※事後申請案件で内容に疑義がある場合、決裁者からの対応指示事項（⇒後日、発議責任者は対応結果を同欄に記入 <br>
          Pada subjek after request, bila ada hal yang kurang jelas pada isi, lihat poin instruksi penanganan oleh approver (⇒selanjutnya, pihak yang bertanggung jawab akan mencantumkan hasil penanganannya di kolom yang sama)
        </td>
        <td colspan="2" style="text-align: center; border-bottom: none">
          完了承認(ライン長) <br>
          Completion Sign (Line Leader)
        </td>
      </tr>
      <tr>
        <td colspan="7">
          <table style="float: right;" width="40%">
            <tr>
              <td>【　YCJへの報告の必要性　】</td>
              <td><input type="checkbox"> はい</td>
              <td><input type="checkbox"> いいえ</td>
            </tr>
            <tr>
              <td>Tingkat kebutuhan informasi ke YCJ</td>
              <td> Ya</td>
              <td> Tidak</td>
            </tr>
          </table>
        </td>
        <td colspan="2" style="vertical-align: bottom; text-align: center; border-top: none">
          GM (DGM)
        </td>
      </tr>
      <tr>
        <td colspan="8" style="font-size: 9pt">
          発議課作成（原則スタッフが記入する） →　事務局(受付印)　→　部門長確認　→　事務局=No.付後台帳登録し,連絡通報を配付（原本事務局保管） <br>
          membuat (Input oleh staf)→Sekretariat (stamp penerimaan)→Pengecekan GM→sekretariat= setelah pemberian no. diregistrasikan ke log book, distribusikan ke contact report (dokumen asli disimpan sekretariat)
        </td>
        <td colspan="2" style="font-size: 9pt">
          Div. pengusul membuat
        </td>
      </tr>
      <tr>
        <td colspan="3">
          Diterjemahkan oleh : <span id="translator">{{ $data->translator }}</span>
        </td>
      </tr>
    </table>
    <br>
    <?php if(Request::segment(5) && (Request::segment(5) == "view" || Request::segment(5) == "implement") && isset($implement)) { ?>
      <table border="1" style="width: 100%">
        <tr>
          <td width="20%">
            <img src="{{ url("waves.jpg") }}" style="width: 100%">
          </td>
          <th style="font-size: 20px">
            <center>
              3M IMPLEMENTATION REPORT <br>
              (Machine, Material, Methode/Mechanism)
            </center>
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
          <td colspan="2">{{ $implement->form_number }}</td>
        </tr>
        <tr>
          <td style="font-weight: bold;">Department</td>
          <td colspan="2">{{ $implement->section }}</td>
        </tr>
        <tr>
          <td style="font-weight: bold;">Name</td>
          <td colspan="2">{{ $implement->name }}</td>
        </tr>
        <tr>
          <td style="font-weight: bold;">Date issued 3M</td>
          <td colspan="2">{{ $implement->date }}</td>
        </tr>
        <tr>
          <td style="font-weight: bold;">Title</td>
          <td colspan="2">{{ $implement->title }}</td>
        </tr>
        <tr>
          <td style="font-weight: bold;">Isi Perubahan & Alasan Perubahan</td>
          <td colspan="2"></td>
        </tr>
        <tr>
          <td colspan="3"><?= $implement->reason ?></td>
        </tr>
        <tr>
          <td style="font-weight: bold;">Tanggal Rencana Perubahan</td>
          <td colspan="2">{{ $implement->started_date }}</td>
        </tr>
        <tr>
          <td style="font-weight: bold;">Tanggal Aktual Perubahan<span class="text-red">*</span></td>
          <td colspan="2">
            <input type="text" name="tgl_aktual" id="tgl_aktual" class="form-control" value="{{ $implement->actual_date }}" readonly></td>
          </tr>
          <tr>
            <td colspan="3" style="font-weight: bold;">Data - data Pengecekan Implementasi</td>
          </tr>
          <tr>
            <td style="font-weight: bold;">Tanggal Pengecekan<span class="text-red">*</span></td>
            <td colspan="2">
              <input type="text" name="tgl_cek" id="tgl_cek" class="form-control" value="{{ $implement->check_date }}" readonly>
            </td>
          </tr>
          <tr>
            <td style="font-weight: bold;">Yang Melakukan Pengecekan<span class="text-red">*</span></td>
            <td colspan="2">
              <input type="text" name="pic_cek" id="pic_cek" class="form-control" value="{{ $implement->checker }}" readonly>
            </td>
          </tr>
          <tr>
            <td colspan="3" style="font-weight: bold;">Dokumen / Alat yang berhubungan dengan implementasi</td>
          </tr>
          <tr>
            <td colspan="3">
              <div class="form-group">
                <label>Lampiran <span class="text-purple"></span></label> <br>
                <?php 
                $atts = str_replace('"', '', $implement->att);
                $atts = str_replace('[', '', $atts);
                $atts = str_replace(']', '', $atts);
                $atts_arr = explode(',', $atts);

                foreach ($atts_arr as $att) {
                  echo "<a href='".url('/uploads/sakurentsu/three_m/att/')."/".$att."' target='_blank'>".$att."</a><br>";
                }
                ?>
              </div>
            </td>
          </tr>
        </table>

      <?php } ?>
      <br>
      <?php if( !Request::segment(5)) { ?>
        <button class="btn btn-primary" style="width: 100%; font-size: 15pt" onclick="openSignModal()"><i class="fa fa-eyedropper"></i>&nbsp; Sign</button><br><br>
        <button class="btn btn-success" onclick="unsigned()"><i class="fa fa-envelope-o"></i>&nbsp; Send Email to Unsigning User</button>
      <?php } else if(Request::segment(5) && Request::segment(5) == "presdir") {?>
        <button class="btn btn-success" style="width: 100%; font-size: 15pt" onclick="approve('presdir', 'PI1301001')"><i class="fa fa-check"></i>&nbsp; Approve</button>
      <?php } else if(Request::segment(5) && Request::segment(5) == "dgm") {?>
        <button class="btn btn-success" style="width: 100%; font-size: 15pt" onclick="approve('dgm', 'PI0109004')"><i class="fa fa-check"></i>&nbsp; Approve</button>
      <?php } else if(Request::segment(5) && Request::segment(5) == "gm") {?>
        <button class="btn btn-success" style="width: 100%; font-size: 15pt" onclick="approve('gm', 'PI1206001')"><i class="fa fa-check"></i>&nbsp; Approve</button>
      <?php } else if(Request::segment(5) && Request::segment(5) == "finish") {?>
        <button class="btn btn-success" style="width: 100%; font-size: 15pt" onclick="receive('std')"><i class="fa fa-check"></i>&nbsp; Receive Standardization</button>
      <?php } else if(Request::segment(5) && Request::segment(5) == "implement") {?>
        <button class="btn btn-success" style="width: 100%; font-size: 15pt" onclick="implement()"><i class="fa fa-check"></i>&nbsp; Verify </button>
      <?php } else if(Request::segment(5) && Request::segment(5) == "sign") { ?>
        <button class="btn btn-success" style="width: 100%; font-size: 15pt" onclick="approve('department', '')"><i class="fa fa-check"></i>&nbsp; Approve</button>

      <?php } else if(Request::segment(5) && Request::segment(5) == "view") { ?>
        <!-- <button class="btn btn-success" style="width: 100%; font-size: 15pt" onclick="approve('gm', 'PI1206001')"><i class="fa fa-check"></i>&nbsp; Approve</button> -->
      <?php } ?>
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
          <iframe id="my_iframe" name="my_iframe" height="0" width="0" frameborder="0" scrolling="yes"></iframe>

          <form method="post" enctype="multipart/form-data" action="{{ url("upload/sakurentsu/3m/document") }}" id="form_upload">
            <label>Upload file(s)</label>
            <input type="file" name="doc_upload" id="doc_upload" multiple="">
            <input type="hidden" name="text_doc_upload" id="text_doc_upload">
            <input type="hidden" name="id_doc_upload" id="id_doc_upload">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <br>
            <button class="btn btn-success btn-sm" style="width: 100%" type="button"><i class="fa fa-plus"></i>&nbsp; Upload</button>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalSign">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Sign Area</h4>
        <div class="modal-body table-responsive no-padding" style="min-height: 100px">
          <input type="text" id="input_sign" class="form-control form-lg" placeholder="Scan ID CARD here">
          <br>
          <table class="table " id="tableSign">
          </table>

          <button class="btn btn-success btn-sm" style="width: 100%" type="button" onclick="save_sign()"><i class="fa fa-check"></i>&nbsp; Save</button>

        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_email">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Emailing Require Document(s)</h4>
        <div class="modal-body table-responsive no-padding" style="min-height: 100px">
          <table class="table table-hover table-bordered table-striped" id="tableDetail">
            <thead>
              <tr>
                <th>Document Name</th>
                <th>Note</th>
                <th>Target Date</th>
                <th>PIC</th>
              </tr>
            </thead>
            <tbody id="bodyDetail">
            </tbody>
          </table>

          <button class="btn btn-danger pull-left"><i class="fa fa-close"></i>&nbsp; Cancel</button>
          <button class="btn btn-primary pull-right" onclick="sendMail()"><i class="fa fa-envelope"></i>&nbsp; Send Mail(s)</button>
        </div>
      </div>
    </div>
  </div>
</div>

</section>
@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script src="{{ url("js/dropzone.min.js") }}"></script>
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>

<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  var file = [];
  var emp_sign = [];
  var dept = [];
  var date = "";

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");

    $('input[type="radio"]').prop('checked', false);
    
    filltableSign();
    fillData();
  });

  function filltableSign() {
    var sign_user = <?php echo json_encode($sign_user); ?>;
    var signed = <?php echo json_encode($signed_user); ?>;
    var sign_gm = <?php echo json_encode($sign_gm); ?>;
    var datas = <?php echo json_encode($data); ?>;


    if (datas.remark == "7" || datas.remark == "8") {
      $("#sign_std").show();
      $("#sign_std").html('<center><span style="font-size: 13pt; vertical-align: top">Received Standardization</span> <br> 18  Jan 2021</center>');
    }
    

    var s = [];
    var emp_id = [];
    var app_date = [];

    $.each(signed, function(key, value) {
      s.push(value.department);
      emp_id.push(value.approver_id);
      app_date.push(value.approve_date);
      date = value.approve_date;

      if (value.department == null) {
        if (value.position == "Deputy General Manager" && value.division == "Production Division") {
          $("#sign_dgm_prod").html("<center><img width='70' src='{{ url('files/ttd') }}/"+value.approver_id+".png' style='padding: 0'></center><br> Date: "+date+" <br> 生産副部長 DGM Produksi");
        } else if (value.position == "General Manager" && value.division == "Production Division") {
          $("#sign_gm_prod").html("<center><img width='70' src='{{ url('files/ttd') }}/"+value.approver_id+".png' style='padding: 0'></center><br> Date: "+date+" <br> 生産部長 GM Produksi");
        } else if (value.position == "General Manager" && value.division == "Production Support") {
         $("#sign_dgm_prod").html("<center><img width='70' src='{{ url('files/ttd') }}/"+value.approver_id+".png' style='padding: 0'></center><br> Date: "+date+" <br> 生産副部長 DGM Produksi");
         $("#sign_gm_prod_supp").html("<center><img width='70' src='{{ url('files/ttd') }}/"+value.approver_id+".png' style='padding: 0'></center><br> Date: "+date+" <br> 生産支援部長 GM Prod.Support");
       } else if (value.position == "President Director") {
        $("#sign_presdir").html("<center><img width='70' src='{{ url('files/ttd') }}/"+value.approver_id+".png' style='padding: 0'></center><br> Date: "+date+" <br> 承認 Approval");
      }
    }
  })

    $("#table_sign_list").empty();

    body_sign = "";
    // $.each(signed, function(key2, value2) {
      $.each(sign_user, function(key, value) {
        if (value.department != null) {
          if(dept.indexOf(value.department) === -1){
            // if (value2.department == value.department) {
              if(jQuery.inArray(value.department, s) !== -1) {
                var index = s.indexOf(value.department);

                body_sign += "<td>Confirm <br><img width='70' src='{{ url('files/ttd') }}/"+emp_id[index]+".png' style='padding: 0'><br> Date "+app_date[index]+" <br> Manager.Chief</td>";
                
              } else {
                body_sign += "<td>Confirm <br><br><br><br><br><br> Date <br> Manager.Chief</td>";
              }

              dept[dept.length] = value.department;
            // } else {

            // }
          }
        }
      })
    // })
    console.log(signed);
    stat_pic = 0;
    arr_pic = [];
    $.each(signed, function(key, value) {
      if (value.status == 'pic') {
        stat_pic = 1;
        arr_pic.push({'emp_id' : value.approver_id, 'date' : value.approve_date});
      }
    })

    if (stat_pic == 0) {
      body_sign += "<td>Initiative PIC <br><br><br><br><br><br> Date <br> Manager.Chief</td>";
    } else {
      body_sign += "<td>Initiative PIC <br><img width='70' src='{{ url('files/ttd') }}/"+arr_pic[0].emp_id+".png' style='padding: 0'><br> Date "+arr_pic[0].date+" <br> Manager.Chief</td>";
    }

    $("#table_sign_list").append(body_sign);
  }


  function fillData() {
    var docs = <?php echo json_encode($docs); ?>;

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
              $("#pic_"+num).text(value1.department_shortname);
            }
          }
        })

        if (stat == 0) {
          radios.filter('[value=NO]').prop('checked', true);
          radios.filter('[value=NO]').parent().parent().css("border", "2px solid red");
          radios.filter('[value=NO]').parent().parent().css("border-radius", "5px");
        }
      }
    })
  }

  $(".btn-upload").click(function() {
    var ido = $(this).attr('id').split('_')[1];


    var text = $("#head_"+ido).text();

    var data = {
      id : "{{ Request::segment(4) }}",
      doc_desc : text
    }

    $("#doc_desc").text(text);
    $("#text_doc_upload").val(text);
    $("#id_doc_upload").val("{{ Request::segment(4) }}");

    $.get('{{ url("fetch/sakurentsu/3m/document") }}', data, function(result, status, xhr){
      $("#bodyFile").empty();
      if (result.status) {
        body_file = "";
        $.each(result.docs, function(key, value) {  
          var obj = JSON.parse(value.file_name); 
          $.each(obj, function(index, val) {  
            body_file += "<tr>";
            body_file += "<td>";
            body_file += "<a href='"+"{{ url('uploads/sakurentsu/three_m/doc/') }}/"+val+"' target='_blank'><i class='fa fa-file-pdf-o'></i> "+val+"</a>";
            body_file += "</td>";
            body_file += "</tr>";
          })
        });

        $("#bodyFile").append(body_file);

        $("#modalFile").modal('show');
      } else {
        $("#modalFile").modal('show');
      }
    })
  });

  $('.doc').change(function(){
    if ($(this).val() == "NEED") {
      $("#"+$(this).attr('name')).css('display', 'block');
    } else {
      $("#"+$(this).attr('name')).css('display', 'none');
    }
  });

  function getFileInfo(num, sk_num) {
    $("#sk_num").text(sk_num+" File(s)");

    $("#bodyFile").empty();

    body_file = "";
    $.each(file, function(key, value) {  
      if (sk_num == value.sk_number) {
        var obj = JSON.parse(value.file);
        var app = "";

        if (obj) {
          for (var i = 0; i < obj.length; i++) {
           body_file += "<tr>";
           body_file += "<td>";
           body_file += "<a href='../../uploads/sakurentsu/translated/"+obj[i]+"' target='_blank'><i class='fa fa-file-pdf-o'></i> "+obj[i]+"</a>";
           body_file += "</td>";
           body_file += "</tr>";
         }
       }
     }
   });

    $("#bodyFile").append(body_file);

    $("#modalFile").modal('show');
  }

  function do_upload()
  {
    document.getElementById('form_upload').target = 'my_iframe';
    document.getElementById('form_upload').submit();
  }


  function getdata(doc_name) {
   var data = {
    id : "{{ Request::segment(5) }}",
    doc_desc : doc_name
  }

  $.get('{{ url("fetch/sakurentsu/3m/document") }}', data, function(result, status, xhr){
    $("#bodyFile").empty();
    if (result.status) {

      body_file = "";
      $.each(result.docs, function(key, value) {
       body_file += "<tr>";
       body_file += "<td>";
       body_file += "<a href='"+value.file_name+"' target='_blank'><i class='fa fa-file-pdf-o'></i> "+value.document_name+"</a>";
       body_file += "</td>";
       body_file += "</tr>";
     });

      $("#bodyFile").append(body_file);
    }
  })
}

function modal_email() {
  $("#modal_email").modal('show');

  $("#bodyDetail").empty();
  body = "";

  $('.doc').each(function(index, value) {
    var ido = $(this).attr('name');
    var num = ido.split('_')[1];

    if ($(this).is(':checked') && $('input[name="'+ido+'"]:checked').val() == "NEED") {    

      body += "<tr>";

      body += "<td>"+$("input[name='doc_name_"+num+"']").val()+"</td>";
      body += "<td>"+$("input[name='doc_note_"+num+"']").val()+"</td>";
      body += "<td>"+$("input[name='doc_target_"+num+"']").val()+"</td>";
      body += "<td>"+$("#doc_pic_"+num+" option:selected").val()+"</td>";
      body += "</tr>";
    }
  });

  $("#bodyDetail").append(body);
}

function sendMail() {
  var data = {
    three_m_id : $("#id").val()
  }

  $.post('{{ url("mail/sakurentsu/3m/document") }}', data, function(result, status, xhr){
    if (result.status) {

    }
  })
}

function openSignModal() {
  $("#modalSign").modal('show');
  // $("#input_sign").focus();
}

$('#modalSign').on('shown.bs.modal', function () {
  $('#input_sign').focus();
}) 

$('#input_sign').keyup(function(e){
  if(e.keyCode == 13)
  {
    if(jQuery.inArray($(this).val(), emp_sign) !== -1) {
      openErrorGritter('Error', "Employee Already Approve");
      $('#input_sign').val('');
      return false;
    } 
    body = "";

    var data = {
      employee_tag : $(this).val(),
      dept_list : dept,
      form_id : "{{ Request::segment(4) }}"
    }

    $.get('{{ url("get/sakurentsu/3m") }}', data, function(result, status, xhr){
      if (result.status) {
        openSuccessGritter('Success', '');

        body += "<tr>";
        body += "<td>"+result.data[0].name+"<input type='hidden' class='input_sign' id='"+result.data[0].employee_id+"' value='"+result.data[0].employee_id+"'></td>";
        body += "<td style='color: green'><i class='fa fa-check'></i></td>";
        body += "</tr>";

        $("#tableSign").append(body);

        $('#input_sign').val('');

        emp_sign.push(result.data.tag);

      } else {
        openErrorGritter('Error', result.message);
        $('#input_sign').val('');
      }
    })
    
  }
});

function save_sign() {
  var sign = [];
  $(".input_sign").each(function(index, value) {
    sign.push($(this).val());
  })

  if (sign.length <= 0) {
    openErrorGritter('Error', 'Please Scan First');
    return false;
  }

  var data = {
    sign : sign,
    form_id : "{{ Request::segment(4) }}"
  }

  $.post('{{ url("post/sakurentsu/3m/sign") }}', data, function(result, status, xhr){
    if (result.status) {
      openSuccessGritter('Success','Sign has been Saved');
      $("#tableSign").empty();
    } else {

    }
  })
}

function unsigned() {
  var data = {
    form_id : "{{ Request::segment(4) }}"
  }

  if(confirm('Are you sure want to email to unsigned User?')) {
    $.get('{{ url("email/sakurentsu/3m/unsigned") }}', data, function(result, status, xhr){

    })
  }

}

function approve(position, sign) {
  var sign_user = sign;
  if (position == 'department') {
    var AuthUser = "{{{ (Auth::user()) ? Auth::user()->username : null }}}";
    if (!AuthUser) {
      window.open('{{ url("/") }}', '_blank');
      return false;
    } else {
      sign_user = AuthUser;
    }
  }

  var data = {
    form_id : "{{ Request::segment(4) }}",
    position : position,
    sign : [sign_user]
  }

  $.post('{{ url("post/sakurentsu/3m/sign") }}', data, function(result, status, xhr){
    if (result.status) {
      openSuccessGritter('Success','Sign has been Saved');
      if ("{{Request::segment(5)}}" == "sign" || "{{Request::segment(5)}}" == "dgm" || "{{Request::segment(5)}}" == "gm") {
        setTimeout( function() {window.location.replace("{{ url('detail/sakurentsu/3m/'.Request::segment(4).'/sign') }}")}, 2000);
      } else if("{{Request::segment(5)}}" == "presdir"){
        setTimeout( function() {window.location.replace("{{ url('detail/sakurentsu/3m/'.Request::segment(4).'/view') }}")}, 2000);
      }
    } else {
      openErrorGritter('Error', result.message);
    }
  })
}

function receive(position) {
  var data = {
    form_id : "{{ Request::segment(4) }}",
    date : date
  }

  $.post('{{ url("post/sakurentsu/3m/receive_std") }}', data, function(result, status, xhr){
    if (result.status) {
      openSuccessGritter('Success','3M has been Received');
      setTimeout( function() {window.location.replace("{{ url('detail/sakurentsu/3m/'.Request::segment(4).'/view') }}")}, 2000);
    } else {
      openErrorGritter('Error','');
    }
  })
}

function implement() {

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