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
#loading, #error { display: none; }
</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    List of {{ $page }}s
    <span class="text-purple"> 作業者のリスト</span>
    <a onclick="addOP()" class="btn btn-primary btn-sm pull-right" style="color:white">Tambah Operator</a>
  </h1>
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
  <div class="row">
    <div class="col-xs-12">
      <div class="box box-solid">
        <div class="box-body">
          <table id="example1" class="table table-bordered table-striped table-hover">
            <thead style="background-color: rgb(126,86,134); color: #FFD700;">
              <tr>
                <th>NIK</th>
                <th>Nama Karyawan</th>
                <th>Bagian</th>
                <th>Tag</th>
                <th>Line</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
             
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="modal fade in" id="modalEdit">
  <form id ="importForm" name="importForm" method="post" action="{{ url('update/Op') }}">
  <input type="hidden" value="{{csrf_token()}}" name="_token" />
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <center><h4 class="modal-title" id="modalDetailTitle" style="background-color: rgb(126,86,134);color: white;font-weight: bold;font-size: 25px">Edit Operator</h4></center>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="col-md-10">
              <div class="form-group" id="modalDetailBodyEditHeader">
                
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success">Update</button>
      </div>
    </div>
  </div>
</form>
</div>

<div class="modal fade in" id="modalAdd">
  <form id ="importForm" name="importForm" method="post" action="{{ url('add/Op') }}">
  <input type="hidden" value="{{csrf_token()}}" name="_token" />
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <center><h4 class="modal-title" id="modalDetailTitle" style="background-color: rgb(126,86,134);color: white;font-weight: bold;font-size: 25px">Tambah Operator</h4></center>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
              <div class="form-group row">
                <input type="text" name="cat" id="cat" value="" hidden>
                <input type="text" name="loc" id="loc" value="" hidden>
                <input type="text" name="id" value="" hidden>
                <label class="col-xs-4" style="text-align:right">Employee ID<span class="text-red">*</span></label>
                <div class="col-sm-5">
                  <select class="form-control select2" style="width: 100%;" id="nik" name="nik" data-placeholder="Pilih Operator . . .">
                    <option value=""></option>
                    @foreach($employees as $employees)
                    <option value="{{ $employees->employee_id }}">{{ $employees->employee_id }} - {{ $employees->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-4" style="text-align:right">Line<span class="text-red">*</span></label>
                <div class="col-sm-5">
                  <select class="form-control select2" style="width: 100%;" id="line" name="line" data-placeholder="Pilih Line . . ." required="">
                    <option value=""></option>
                    @foreach($lines as $lines1)
                    <option value="{{ $lines1 }}">{{ $lines1 }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-4" style="text-align:right">Bagian<span class="text-red">*</span></label>
                <div class="col-sm-5">
                  <select class="form-control select2" style="width: 100%;" id="bagian" name="bagian" data-placeholder="Pilih Bagian . . ." required>>@foreach($bagians as $bagians1)<option value="{{ $bagians1 }}">{{ $bagians1 }}</option>@endforeach</select>
                </div>
              </div>
              <!-- <div class="form-group row">
                <label class="col-sm-4" style="text-align:right">Tag</label>
                <div class="col-sm-5">
                  <input class="form-control" style="width: 100%;" id="tag" name="tag" placeholder="Scan Tag RFID . . ." value="">
                </div>
              </div> -->
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success"><i class="fa fa-plus"></i> Add</button>
      </div>
    </div>
  </div>
</form>
</div>

<div class="modal modal-danger fade" id="modaldeleteOP" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Konfirmasi Hapus Data</h4>
        </div>
        <div class="modal-body">
          Apakah anda yakin ingin menghapus Operator Ini ?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
          <a id="a" name="modalbuttondelete" type="button"  onclick="delete_op(this.id)" class="btn btn-danger">Yes</a>
        </div>
      </div>
    </div>
  </div>


@stop

@section('scripts')
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
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
  jQuery(document).ready(function() { 
    $('body').toggleClass("sidebar-collapse");
    fillexample1();
    $('.select2').select2({
      dropdownAutoWidth : true,
      width: '100%',
    });
    clearAll();
  });
  

function fillexample1(){
  var table = $('#example1').DataTable({
    'dom': 'Bfrtip',
    'responsive': true,
    'lengthMenu': [
    [ 10, 25, 50, -1 ],
    [ '10 rows', '25 rows', '50 rows', 'Show all' ]
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
    'paging'        : true,
    'lengthChange'  : true,
    'searching'     : true,
    'ordering'      : true,
    'info'        : true,
    'order'       : [],
    'autoWidth'   : true,
    "sPaginationType": "full_numbers",
    "bJQueryUI": true,
    "bAutoWidth": false,
    "processing": true,
    "serverSide": true,
    "ajax": {
      "type" : "get",
      "url" : "{{ url("index/FillOp") }}",
    },
    "columns": [    
    { "data": "nik"},
    { "data": "nama"},
    { "data": "bagian"},
    { "data": "tag"},
    { "data": "line"},    
    { "data": "action"}
      ]
    });
}

function clearAll() {
  $('#employee_id').val('').trigger('change');
  $('#bagian').val('').trigger('change');
  $('#line').val('').trigger('change');
}

function addOP() {
  $('#modalAdd').modal('show');
}

function editop(id){
    var data = {
      id : id
    }
    $.get('{{ url("edit/Op") }}', data, function(result, status, xhr){
      if(xhr.status == 200){
        if(result.status){
          $('#modalDetailBodyEdit').html('');
          $('#modalDetailBodyEditHeader').html('');
          
          $.each(result.id_op, function(key, value) {
            
            $('#modalDetailBodyEditHeader').append('<input type="text" name="cat" id="cat" value="'+ value.line +'" hidden><input type="text" name="loc" id="loc" value="'+ value.bagian +'" hidden><input type="text" name="id" value="'+ value.id +'" hidden><label>NIK<span class="text-red">*</span></label><input class="form-control" style="width: 100%;" id="nik" name="nik" data-placeholder="Input a NIK..." required readonly="" value="'+ value.nik +'"><label>Nama<span class="text-red">*</span></label><input class="form-control" style="width: 100%;" id="nama" name="nama" data-placeholder="Input a NAMA..." required readonly="" value="'+ value.nama +'"><label>TAG<span class="text-red">*</span></label><input class="form-control" style="width: 100%;" id="tag" name="tag" data-placeholder="Input a TAG..." value="'+ value.tag +'"><label>Line<span class="text-red">*</span></label><select class="form-control select2" style="width: 100%;" id="line" name="line" data-placeholder="Choose a Line..."><option></option>@foreach($lines as $lines)<option value="{{ $lines }}">{{ $lines }}</option> @endforeach</select></div><div class="form-group"><label>Bagian<span class="text-red">*</span></label><select class="form-control select2" style="width: 100%;" id="bagian" name="bagian" data-placeholder="Choose a Bagian..." required>>@foreach($bagians as $bagians)<option value="{{ $bagians }}">{{ $bagians }}</option>@endforeach</select></div><div class="form-group">').find('.select2').select2();
           
          });    

          var cat = $('#cat').val();;
          var loc = $('#loc').val();;
          $("#line").val(cat).trigger("change");
          $("#bagian").val(loc).trigger("change");
          $('#modalEdit').modal('show');
          
        }
        else{
          alert('Attempt to retrieve data failed');
        }
      }
      else{
        alert('Disconnected from server');
      }
    });
}

  function deleteop(id) {
    $('[name=modalbuttondelete]').attr("id",id);
  }

  function delete_op(id){

    var data = {
      id:id,
    }

    $("#loading").show();

    $.post('{{ url("delete/Op") }}', data, function(result, status, xhr){
      if (result.status == true) {
            openSuccessGritter("Success","Data Berhasil Diupdate");
            $("#loading").hide();
            setTimeout(function(){  window.location.reload() }, 2500);
      }
      else{
        openErrorGritter("Success","Data Gagal Diupdate");
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
          time: '2000'
        });
    }

</script>

@stop