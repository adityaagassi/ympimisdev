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
          <!-- <form id="create_form" enctype="multipart/form-data"> -->
            <div class="row">
              <div class="col-xs-7">
                <div class="form-group">
                  <label for="date">Judul <span class="text-purple">件名</span></label>
                  <input type="hidden" value="{{csrf_token()}}" name="_token" />
                  <?php if(isset($judul)) { ?>
                    <input type="hidden" name="sakurentsu_number" value="{{ $judul->sakurentsu_number }}">
                  <?php } else { ?>
                    <input type="hidden" name="sakurentsu_number" value="">
                  <?php } ?>
                  <input type="text" class="form-control input-lg" id="title_name" placeholder="Title">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-4">
                <div class="form-group">
                  <label for="date">Nama Produk / Nama Mesin <span class="text-purple">製品名 / 設備名</span></label>
                  <input type="text" class="form-control" name="product_name" id="product_name" placeholder="Input Product Name / Machine Name">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-4">
                <div class="form-group">
                  <label for="date">Nama Proses <span class="text-purple">工程名</span></label>
                  <input type="text" class="form-control" name="proccess_name" id="proccess_name" placeholder="Input Process Name">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-4">
                <div class="form-group">
                  <label for="date">Nama Unit <span class="text-purple">班　名</span></label>
                  <input type="text" class="form-control" name="unit_name" id="unit_name" placeholder="Input Unit Name">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-4">
                <div class="form-group">
                  <p><b>Klasifikasi Perubahan 3M <span class="text-purple">3M変更区分</span></b></p>
                  <label class="radio-inline">
                    <input type="radio" name="category" value="Metode">Metode <span class="text-purple">工法</span>
                  </label>
                  <label class="radio-inline">
                    <input type="radio" name="category" value="Material">Material <span class="text-purple">材料</span>
                  </label>
                  <label class="radio-inline">
                    <input type="radio" name="category" value="Mesin">Mesin <span class="text-purple">設備</span>
                  </label>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-6">
                <div class="form-group">
                  <label for="related_department">Related Department <span class="text-purple">関係部門</span></label>
                  <select class="form-control" name="related_department[]" id="related_department" data-placeholder="Select Related Department" multiple="">
                    @foreach($departemen as $dpr)
                    <option value="{{ $dpr->department }}">{{ $dpr->department }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-12">
                <div class="form-group">
                  <label>Isi dan Alasan Perubahan <span class="text-purple">変更内容・変更理由</span></label>
                  <textarea id="isi" name="isi"></textarea>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-12">
                <div class="form-group">
                  <label>Keuntungan Perubahan <span class="text-purple">変更することによるメリット</span></label>
                  <textarea id="keuntungan" name="keuntungan"></textarea>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-12">
                <div class="form-group">
                  <label>Pengecekan kualitas sebelumnya (Tgl・metode・jumlah・pengecek,dll) <span class="text-purple">事前の品質確認　（日時・方法・数量・確認者等）</span></label>
                  <textarea id="kualitas_before" name="kualitas_before"></textarea>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-12">
                <div class="form-group">
                  <label>Tanggal mulai・Tgl rencana perubahan <span class="text-purple">開始日・切替予定日</span> <br> ※alasan bila menjadi after request <span class="text-purple">※事後申請となった場合はその理由</span></label>
                  <input type="text" class="form-control" name="tgl_rencana" id="tgl_rencana" placeholder="Input Planned Start Date">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-12">
                <div class="form-group">
                  <label>Item khusus <span class="text-purple">特記事項</span></label>
                  <textarea id="item_khusus" name="item_khusus"></textarea>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-12">
                <div class="form-group">
                  <label>Lampiran <span class="text-purple">添付</span></label>
                  <p><button type="button" class="btn btn-success btn-xs" onclick="add_att()"><i class="fa fa-plus"></i></button></p>
                  <table>
                    <tr id="lampiran_div">
                      <td><input name="file[]" type="file" id="lampiran" class="lampiran" multiple></td>
                      <td></td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-12">
                <button class="btn btn-success pull-right" style="margin-top: 5px" id="save_3m" onclick="save_3m()"><i class="fa fa-check"></i>&nbsp; SAVE 3M</button>
              </div>
            </div>

            <!-- </form> -->
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalFile">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="sk_num"></h4>
            <div class="modal-body table-responsive no-padding" style="min-height: 100px">
              <table class="table table-hover table-bordered table-striped" id="tableFile">
                <tbody id='bodyFile'></tbody>
              </table>
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

      $("#related_department").select2();

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

      CKEDITOR.replace('item_khusus' ,{
        filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
        height: 150
      });

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

    function save_3m() {
      product = $("#product_name").val();
      proccess = $("#proccess_name").val();
      title = $("#title_name").val();
      unit_name = $("#unit_name").val();
      category = $("input[name='category']:checked").val();
      content = CKEDITOR.instances.isi.getData();
      benefit = CKEDITOR.instances.keuntungan.getData();
      kualitas_before = CKEDITOR.instances.kualitas_before.getData();
      planned_date = $("#tgl_rencana").val();
      special_item = CKEDITOR.instances.item_khusus.getData();
      related_department = $("#related_department").val();
      sakurentsu_number = $("#sk_number").val();

      console.log(title);

      var files = [];
      var file_datas = [];


      var formData = new FormData();
      formData.append('product', product);
      formData.append('proccess', proccess);
      formData.append('title', title);
      formData.append('unit_name', unit_name);
      formData.append('category', category);
      formData.append('content', content);
      formData.append('benefit', benefit);
      formData.append('kualitas_before', kualitas_before);
      formData.append('planned_date', planned_date);
      formData.append('special_item', special_item);
      formData.append('sakurentsu_number', sakurentsu_number);
      formData.append('related_department', related_department);
      formData.append('file_datas', $("#lampiran").prop('files')[0]);

      $.each($('input[name="file[]"]'),function(i, obj) {
        $.each(obj.files,function(j,file){
          formData.append('file_datas['+i+']['+j+']', file);
        })
      });


      var url = "{{ url('post/sakurentsu/3m_form')}}";
      $("#loading").show();

      $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        success: function (response) {
          console.log(response.status);
          $("#loading").hide();
          openSuccessGritter('Success', '3M Has Been Created Successfully');
          // setTimeout( function() {window.location.replace("{{ url('index/sakurentsu/list_3m') }}")}, 2000);

        },
        error: function (response) {
          console.log(response.message);
          $("#loading").hide();
          openErrorGritter('Error', '');
        },
        contentType: false,
        processData: false
      });
    };

    function add_att() {
      isi =  '<tr><td style="padding-right:3px"><input name="file[]" type="file" class="lampiran" multiple></td><td><button type="button" class="btn btn-danger btn-xs" onclick="delete_file(this)"><i class="fa fa-close"></i></button></td></tr>';

      $("#lampiran").after(isi);
    }

    function delete_file(elem) {
      $(elem).closest("tr").remove();
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