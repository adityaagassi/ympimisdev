@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<style type="text/css">
thead input {
  width: 100%;
  padding: 3px;
  box-sizing: border-box;
}
thead>tr>th{
  text-align:center;
  overflow:hidden;
  padding: 3px;
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
  border:1px solid rgb(211,211,211);
  padding-top: 0;
  padding-bottom: 0;
}
table.table-bordered > tfoot > tr > th{
  border:1px solid rgb(211,211,211);
}
td {
  overflow:hidden;
  text-overflow: ellipsis;
}
#tableBodyResult > tr:hover {
  cursor: pointer;
  background-color: #7dfa8c;
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
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content-header">
  <h1>
    List Question Audit
    <small><b>E</b>lectronic-<b>I</b>nternal <b>R</b>equest <b>C</b>orrective <b>A</b>ction</small>
  </h1>
  <ol class="breadcrumb">
    <button class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#create-modal" style="margin-right: 5px">
      <i class="fa fa-plus"></i>&nbsp;&nbsp;Add Point Question
    </button>
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

  @if (session('error'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Error!</h4>
    {{ session('error') }}
  </div>   
  @endif
  <div class="row">
    <div class="col-xs-12">
      <div class="box box-solid">
        <div class="box-body">
          <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <table id="tableResult" class="table table-bordered table-striped table-hover">
              <thead style="background-color: rgb(126,86,134); color: #FFD700;">
                <tr>
                  <th>Nomor</th>
                  <th>Kategori</th>
                  <th>Lokasi</th>
                  <th>Klausul / Nomor</th>
                  <th>Judul</th> 
                  <th>Question</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="tableBodyResult">
              </tbody>
            </table>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
      </div>
      <div class="modal-body">
        Are you sure want to delete this?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
      </div>
    </div>
  </div>
</div>

<div class="modal modal-default fade" id="create-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">
              &times;
            </span>
          </button>
          <h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Add Point Question</h1>
        </div>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-12">
            <div class="box-body">
              <input type="hidden" value="{{csrf_token()}}" name="_token" />
              <div class="form-group row" align="right">
                <label class="col-sm-2">Kategori<span class="text-red">*</span></label>
                <div class="col-sm-9" align="left">
                  <input type="inputkategori" class="form-control" id="inputkategori" placeholder="Kategori" value="{{$kategori}}" required disabled>
                </div>
              </div>
              <div class="form-group row" align="right">
                <label class="col-sm-2">Lokasi<span class="text-red">*</span></label>
                <div class="col-sm-9" align="left">
                  <input type="inputlokasi" class="form-control" id="inputlokasi" placeholder="Kategori" value="{{$lokasi}}" required disabled>
                </div>
              </div>
              <div class="form-group row" align="right">
                <label class="col-sm-2">Klausul / Nomor<span class="text-red">*</span></label>
                <div class="col-sm-9">
                  <input type="inputklausul" class="form-control" id="inputklausul" placeholder="Input Klausul (Contoh : 1.1)" required>
                </div>
              </div>
              <div class="form-group row" align="right">
                <label class="col-sm-2">Judul<span class="text-red">*</span></label>
                <div class="col-sm-9">
                  <input type="inputpoint_judul" class="form-control" id="inputpoint_judul" placeholder="Input Judul" required>
                </div>
              </div>
              <div class="form-group row" align="right">
                <label class="col-sm-2">Point Question<span class="text-red">*</span></label>
                <div class="col-sm-9">
                  <textarea id="inputpoint_question" class="form-control" style="height: 200px;" name="inputpoint_question"></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
        <button class="btn btn-success" onclick="addPointQuestion()"><i class="fa fa-plus"></i> Add Point Question</button>
      </div>
    </div>
  </div>
</div>

<div class="modal modal-default fade" id="edit-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">
              &times;
            </span>
          </button>
          <h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit Point Question</h1>
        </div>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-12">
            <div class="box-body">
              <input type="hidden" value="{{csrf_token()}}" name="_token" />
              <div class="form-group row" align="right">
                <input type="hidden" id="id_point_check">
                <label class="col-sm-2">Kategori<span class="text-red">*</span></label>
                <div class="col-sm-9" align="left">
                  <input type="editkategori" class="form-control" id="editkategori" placeholder="Kategori" value="{{$kategori}}" required disabled>
                </div>
              </div>
              <div class="form-group row" align="right">
                <label class="col-sm-2">Lokasi<span class="text-red">*</span></label>
                <div class="col-sm-9" align="left">
                  <input type="editlokasi" class="form-control" id="editlokasi" placeholder="Kategori" value="{{$lokasi}}" required disabled>
                </div>
              </div>
              <div class="form-group row" align="right">
                <label class="col-sm-2">Klausul / Nomor<span class="text-red">*</span></label>
                <div class="col-sm-9">
                  <input type="editklausul" class="form-control" id="editklausul" placeholder="Edit Klausul (Contoh : 1.1)" required>
                </div>
              </div>
              <div class="form-group row" align="right">
                <label class="col-sm-2">Judul<span class="text-red">*</span></label>
                <div class="col-sm-9">
                  <input type="editpoint_judul" class="form-control" id="editpoint_judul" placeholder="Edit Judul" required>
                </div>
              </div>
              <div class="form-group row" align="right">
                <label class="col-sm-2">Point Question<span class="text-red">*</span></label>
                <div class="col-sm-9">
                  <textarea id="editpoint_question" class="form-control" style="height: 200px;" name="editpoint_question"></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
        <button class="btn btn-success" onclick="updatePointCheck()"><i class="fa fa-plus"></i> Update Point Question</button>
      </div>
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

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");
    fillTable();
    $("#navbar-collapse").text('');
    $('#tgl').datepicker({
        <?php $tgl_max = date('Y-m-d') ?>
        format: "yyyy-mm-dd",
        autoclose: true,
        todayHighlight: true,
        endDate: '<?php echo $tgl_max ?>'
      });
      $('.select2').select2({
        language : {
          noResults : function(params) {
            return "There is no list";
        }
      }
    });

    CKEDITOR.replace('inputpoint_question' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });

    CKEDITOR.replace('editpoint_question' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });

    emptyAll();
  });

  function emptyAll() {
    var kategori = '{{$kategori}}';
    var lokasi = '{{$lokasi}}';

    $('#inputklausul').val('');
    $('#inputlokasi').val(lokasi);
    $('#inputkategori').val(kategori);
    $('#inputpoint_judul').val('');
    $("#inputpoint_question").html(CKEDITOR.instances.inputpoint_question.setData(''));

    $('#editklausul').val('');
    $('#editlokasi').val(lokasi);
    $('#editkategori').val(kategori);
    $('#editpoint_judul').val('');
    $("#editpoint_question").html(CKEDITOR.instances.editpoint_question.setData(''));
  }

  function fillTable() {

    var data = {
        kategori:'{{$kategori}}',
        lokasi:'{{$lokasi}}',
    }

    $.get('{{ url("index/audit_iso/fetch_point_audit") }}', data, function(result, status, xhr) {

    if(result.status){
      $('#tableResult').DataTable().clear();
      $('#tableResult').DataTable().destroy();
      $('#tableBodyResult').html("");
      var tableData = "";
      var count = 1;
      
      $.each(result.lists, function(key, value) {
        tableData += '<tr>';
        tableData += '<td width="5%">'+ count +'</td>';
        tableData += '<td width="10%">'+ value.kategori +'</td>';
        tableData += '<td>'+ value.lokasi +'</td>';
        tableData += '<td>'+ value.klausul +'</td>';
        tableData += '<td>'+ value.point_judul +'</td>';
        tableData += '<td>'+ value.point_question+ '</td>';
        tableData += '<td>';
        tableData += '<a style="margin-right: 2%; padding: 3%; padding-top: 1%; padding-bottom: 1%; margin-top: 2%; margin-bottom: 2%;" type="button" class="btn btn-warning" data-toggle="modal" data-target="#edit-modal" onclick="editPointCheck(\''+value.id+'\');">Edit</a>';
        tableData += '<a style="padding: 3%; padding-top: 1%; padding-bottom: 1%; margin-top: 2%; margin-bottom: 2%;" href="" class="btn btn-danger" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation(\''+value.klausul+' - '+value.point_judul+'\',\''+value.id+'\');">Delete</a>';
        tableData += '</td>';

        tableData += '</tr>';
        count += 1;
      });

      $('#tableBodyResult').append(tableData);
      var table = $('#tableResult').DataTable({
        'dom': 'Bfrtip',
        'responsive':true,
        'lengthMenu': [
        [ 5, 10, 25, -1 ],
        [ '5 rows', '10 rows', '25 rows', 'Show all' ]
        ],
        'buttons': {
          buttons:[
          {
            extend: 'pageLength',
            className: 'btn btn-default',
          },
          {
          extend: 'copy',
          className: 'btn btn-success',
          text: '<i class="fa fa-copy"></i> Copy',
          exportOptions: {
            columns: ':not(.notexport)'
          }
          },
          {
            extend: 'excel',
            className: 'btn btn-info',
            text: '<i class="fa fa-file-excel-o"></i> Excel',
            exportOptions: {
              columns: ':not(.notexport)'
            }
          },
          {
            extend: 'print',
            className: 'btn btn-warning',
            text: '<i class="fa fa-print"></i> Print',
            exportOptions: {
              columns: ':not(.notexport)'
            }
          },
          ]
        },
        'paging': true,
        'lengthChange': true,
        'pageLength': 15,
        'searching': true,
        'ordering': false,
        'order': [],
        'info': true,
        'autoWidth': true,
        "sPaginationType": "full_numbers",
        "bJQueryUI": true,
        "bAutoWidth": false,
        "processing": true,

      });
    }
    else{
      alert('Attempt to retrieve data failed');
    }

  });
  }

  function addPointQuestion() {
    var kategori = '{{$kategori}}';
    var lokasi = '{{$lokasi}}';

    var data = {
      kategori:kategori,
      lokasi:lokasi,
      klausul:$('#inputklausul').val(),
      point_judul:$('#inputpoint_judul').val(),
      point_question:CKEDITOR.instances.inputpoint_question.getData()
    }

    $.post('{{ url("input/audit_iso/point_check") }}', data, function(result, status, xhr){
      if(result.status){
        $("#create-modal").modal('hide');
        emptyAll();
        fillTable();
        openSuccessGritter('Success','Success Add Point Question');
      } else {
        audio_error.play();
        openErrorGritter('Error','Add Point Question Failed');
      }
    });
  }

  function editPointCheck(id) {
    var data = {
      id:id
    }

    $.get('{{ url("fetch/audit_iso/get_point_check") }}', data, function(result, status, xhr){
      if(result.status){
        $('#editklausul').val(result.lists.klausul);
        $('#editpoint_judul').val(result.lists.point_judul);
        $("#editpoint_question").html(CKEDITOR.instances.editpoint_question.setData(result.lists.point_question));
        $('#id_point_check').val(id);
      } else {
        audio_error.play();
        openErrorGritter('Error','Get Point Question Failed');
      }
    });
  }

  function updatePointCheck() {
    var kategori = '{{$kategori}}';
    var lokasi = '{{$lokasi}}';

    var data = {
      kategori:kategori,
      lokasi:lokasi,
      klausul:$('#editklausul').val(),
      point_judul:$('#editpoint_judul').val(),
      point_question:CKEDITOR.instances.editpoint_question.getData(),
      id:$('#id_point_check').val(),
    }

    $.post('{{ url("update/audit_iso/point_check") }}', data, function(result, status, xhr){
      if(result.status){
        $("#edit-modal").modal('hide');
        emptyAll();
        fillTable();
        openSuccessGritter('Success','Success Update Point Question');
      } else {
        audio_error.play();
        openErrorGritter('Error','Update Point Question Failed');
      }
    });
  }

  function deleteConfirmation(name,id) {
    var kategori = '{{$kategori}}';
    var lokasi = '{{$lokasi}}';
    var url = '{{ url("index/audit_iso/destroy_point_check") }}';
    jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
    jQuery('#modalDeleteButton').attr("href", url+'/'+id+'/'+kategori+'/'+lokasi);
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