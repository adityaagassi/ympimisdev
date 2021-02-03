@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link href="{{ url("css/dropzone.min.css") }}" rel="stylesheet">
<link href="{{ url("css/basic.min.css") }}" rel="stylesheet">
<style type="text/css">
  thead>tr>th{
    text-align:center;
  }
  tbody>tr>td{
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
    background-color: #a488aa;
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
    background-color: #a488aa;
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

  span.select2-selection__choice__remove {
    display: none !important;
  }

  .btn-upload {
    display: none;
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
    <div class="col-xs-12" style="padding-right: 0">
      <div class="box box-solid">
        <div class="box-body">
          <div class="row">
            <div class="col-xs-4">
              <div class="form-group">
                <label for="sk_number">Sakurentsu Number <span class="text-purple">作連通の番号</span></label>
                <?php if(isset($judul)) { ?>
                  <input type="text" class="form-control" id="sk_number" readonly="" value="{{ $judul->sakurentsu_number }}">
                <?php } else { ?>
                  <input type="text" class="form-control" id="sk_number" readonly="" value="">
                <?php } ?>
              </div>
            </div>

            <div class="col-xs-5">
              <div class="form-group">
                <label for="title">Sakurentsu Title <span class="text-purple">作連通の表題</span></label>
                <?php if(isset($judul)) { ?>
                  <input type="text" class="form-control" id="title" readonly="" value="{{ $judul->title }}">
                <?php } else { ?>
                  <input type="text" class="form-control" id="title" readonly="" value="">
                <?php } ?>
              </div>
            </div>

            <div class="col-xs-3">
              <div class="form-group">
                <label for="target">Target Date <span class="text-purple">締切</span></label>
                <?php if(isset($judul)) { ?>
                  <input type="text" class="form-control" id="target" readonly="" value="{{ $judul->tgl_target }}">
                <?php } else { ?>
                  <input type="text" class="form-control" id="target" readonly="" value="">
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="box box-solid">
        <div class="box-body">
          <div class="row">
            <div class="col-xs-12">
              <center>
                <h4>Form Aplikasi Perubahan 3M <span class="text-purple">( 3Ｍ変更 申請書 )</span></h4>
                <h4>Form Informasi Perubahan 3M <span class="text-purple">( 3Ｍ変更 連絡通報 )</span></h4>
              </center>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-9">
              <div class="form-group">
                <label for="date"><span class="text-red">*</span>Judul <span class="text-purple">件名</span></label>
                <input type="hidden" value="{{csrf_token()}}" name="_token" />
                <input type="text" class="form-control input-lg" id="title_name" readonly="">
                <input type="text" class="form-control input-lg" id="title_name_trans" placeholder="Translate Title" name="title_name_trans">
                <input type="hidden" class="form-control" id="id" name="id">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-4">
              <div class="form-group">
                <label for="date"><span class="text-red">*</span>Nama Produk / Nama Mesin <span class="text-purple">製品名 / 設備名</span></label>
                <input type="text" class="form-control" id="product_name" placeholder="Input Product Name / Machine Name" name="product_name">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-4">
              <div class="form-group">
                <label for="date"><span class="text-red">*</span>Nama Proses <span class="text-purple">工程名</span></label>
                <input type="text" class="form-control" id="proccess_name" placeholder="Input Process Name" name="proccess_name">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-4">
              <div class="form-group">
                <label for="date"><span class="text-red">*</span>Nama Unit <span class="text-purple">班　名</span></label>
                <input type="text" class="form-control" id="unit_name" placeholder="Input Unit Name" name="unit_name">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-5">
              <div class="form-group">
                <p><b>Klasifikasi Perubahan 3M <span class="text-purple">3M変更区分</span></b></p>
                <label class="radio-inline">
                  <input type="radio" name="category" value="Metode" disabled>Metode <span class="text-purple">工法</span>
                </label>
                <label class="radio-inline">
                  <input type="radio" name="category" value="Material" disabled>Material <span class="text-purple">材料</span>
                </label>
                <label class="radio-inline">
                  <input type="radio" name="category" value="Mesin" disabled>Mesin <span class="text-purple">設備</span>
                </label>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-4">
              <div class="form-group">
                <label for="related_department">Related Department <span class="text-purple">関係部門</span></label>
                <select class="form-control select2" id="related_department" name="related_department[]" data-placeholder="Select Related Department" multiple="">
                  @foreach($departemen as $dpr)
                  <option value="{{ $dpr->department }}" disabled>{{ $dpr->department }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-12">
              <div class="form-group">
                <label><span class="text-red">*</span>Isi dan Alasan Perubahan <span class="text-purple">変更内容・変更理由</span></label>
                <textarea id="isi" name="isi"></textarea>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-12">
              <div class="form-group">
                <label><span class="text-red">*</span>Keuntungan Perubahan <span class="text-purple">変更することによるメリット</span></label>
                <textarea id="keuntungan" name="keuntungan"></textarea>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-12">
              <div class="form-group">
                <label><span class="text-red">*</span>Pengecekan kualitas sebelumnya (Tgl・metode・jumlah・pengecek,dll) <span class="text-purple">事前の品質確認　（日時・方法・数量・確認者等）</span></label>
                <textarea id="kualitas_before" name="kualitas_before"></textarea>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-12">
              <div class="form-group">
                <label><span class="text-red">*</span>Tanggal mulai・Tgl rencana perubahan <span class="text-purple">開始日・切替予定日</span> <br> ※alasan bila menjadi after request <span class="text-purple">※事後申請となった場合はその理由</span></label>
                <input type="text" class="form-control" id="tgl_rencana" placeholder="Input Planned Start Date" name="tgl_rencana">
              </div>
            </div>
          </div>

          <!-- <div class="row">
            <div class="col-xs-12">
              <div class="form-group">
                <label>Item khusus <span class="text-purple">特記事項</span></label>
                <textarea id="item_khusus" name="item_khusus"></textarea>
              </div>
            </div>
          </div> -->

            <!-- <div class="row">
              <div class="col-xs-12">
                <div class="form-group">
                  <p><b>Perubahan Bom <span class="text-purple">BOM変更</span></b></p>
                  <label class="radio-inline">
                    <input type="radio" name="bom_change" value="Ada" readonly>Ada 有り
                  </label>
                  <label class="radio-inline">
                    <input type="radio" name="bom_change" value="Tidak Ada" readonly>Tidak Ada 無し
                  </label>
                </div>
              </div>
            </div> -->

            <!-- <div class="row">
              <div class="col-xs-12">
                <div class="form-group">
                  <label>Lampiran <span class="text-purple"></span></label>
                  
                  <input name="file[]" type="file" id="lampiran" multiple >
                  
                </div>
              </div>
            </div> -->

            <div class="row">
              <div class="col-xs-12">
                <button type="submit" class="btn btn-success pull-right" style="margin-top: 5px" id="save_3m" onclick="save_3m()"><i class="fa fa-check"></i>&nbsp; Save Translation & Email</button>
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

    jQuery(document).ready(function() {
      $('body').toggleClass("sidebar-collapse");

      $('input[type="radio"]').prop('checked', false);

      fillData();

      $(".select2").select2();
      $(".select3").select2();

      $(".datepicker").datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd',
        todayHighlight: true
      });

      CKEDITOR.replace('isi' ,{
        filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
        height: 300
      });

      CKEDITOR.replace('keuntungan' ,{
        filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
        height: 150
      });

      CKEDITOR.replace('kualitas_before' ,{
        filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
        height: 100
      });

    });

    function fillData() {
      var datas = <?php echo json_encode($tiga_m); ?>;

      $("#title_name").val(datas.title);
      $("#id").val(datas.id);
      $("#product_name").val(datas.product_name);
      $("#proccess_name").val(datas.proccess_name);
      $("#unit_name").val(datas.unit);
      $("input[name='category'][value='"+datas.category+"']").prop('checked', true);

      if (datas.related_department) {
        $.each(datas.related_department.split(","), function(index,value){

          $("#related_department option[value='" + value + "']").prop("selected", true);
        });
      }

      $("#isi").val(datas.reason);
      $("#keuntungan").val(datas.benefit);
      $("#kualitas_before").val(datas.check_before);
      $("#tgl_rencana").val(datas.started_date);
    }

    function save_3m() {
      var data = {
        title_name : $("#title_name_trans").val(),
        product_name : $("#product_name").val(),
        proccess_name : $("#proccess_name").val(),
        unit_name : $("#unit_name").val(),
        isi : CKEDITOR.instances.isi.getData(),
        keuntungan : $("#keuntungan").val(),
        kualitas_before : CKEDITOR.instances.kualitas_before.getData(),
        tgl_rencana : $("#tgl_rencana").val(),
        // item_khusus : CKEDITOR.instances.item_khusus.getData(),
        id : $("#id").val()
      }


      $.post('{{ url("post/sakurentsu/3m/translate") }}', data, function(result, status, xhr){
        if (result.status) {
          openSuccessGritter('Success', '3M Has Been Translated');
          setTimeout( function() {window.location.replace("{{ url('index/sakurentsu/list_sakurentsu_translate') }}")}, 2000);
        } else {
          openErrorGritter('Error', result.message);
        }
      });

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


  <script>
    var msg = '{{Session::get('alert')}}';
    var exist = '{{Session::has('alert')}}';
    if(exist){
     window.parent.getdata("{{Session::get('doc_name')}}");
   }
 </script>
 @stop