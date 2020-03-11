@extends('layouts.master')
@section('stylesheets')
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
  </h1>
  <ol class="breadcrumb">
    <li><a onclick="addOP()" class="btn btn-primary btn-sm" style="color:white">Create {{ $page }}</a></li>
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
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-body">
          <table id="example1" class="table table-bordered table-striped table-hover">
            <thead style="background-color: rgba(126,86,134,.7);">
              <tr>
                <th>Nik</th>
                <th>Nama</th>
                <th>Bagian</th>
                <th>Tag</th>
                <th>Line</th>                
                <th>Edit</th>
                {{-- <th>Hapus</th> --}}
              </tr>
            </thead>
            <tbody>
             
            </tbody>
            <tfoot>
              <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                {{-- <th></th> --}}
              </tr>
            </tfoot>
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
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Edit Operator</h4>
        <br>
        <h4 class="modal-title" id="modalDetailTitle"></h4>
        <div class="row">
          <div class="col-md-12">
            <div class="col-md-10">
              <div class="form-group" id="modalDetailBodyEditHeader">
                
              </div>
            </div>
        
          </div>
        </div>

        <div id="tambah2">
        <input type="text" name="lop2" id="lop2" value="1" hidden="">
        </div>
        
      </div>
      <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-warning">Update</button>
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
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Add Operator</h4>
        <br>
        <h4 class="modal-title" id="modalDetailTitle"></h4>
        <div class="row">
          <div class="col-md-12">
            <div class="col-md-10">
              <div class="form-group" id="modalDetailBodyEditHeaders">
                <input type="text" name="cat" id="cat" value="" hidden>
                <input type="text" name="loc" id="loc" value="'+ value.bagian +'" hidden>
                <input type="text" name="id" value="'+ value.id +'" hidden>
                <label>TAG</label>
                <input class="form-control" style="width: 100%;" id="tag" name="tag" data-placeholder="Input a TAG..." value="">
                <label>Nama<span class="text-red">*</span></label>
                <input class="form-control" style="width: 100%;" id="nama" name="nama" data-placeholder="Input a NAMA..." required value="">
                <label>NIK<span class="text-red">*</span></label>
                <input class="form-control" style="width: 100%;" id="nik" name="nik" data-placeholder="Input a NIK..." required value="">
                <label>Line</label>
                <select class="form-control select2" style="width: 100%;" id="line" name="line" data-placeholder="Choose a Line..."><option></option>@foreach($lines as $lines1)<option value="{{ $lines1 }}">{{ $lines1 }}</option> @endforeach</select></div><div class="form-group">
                  <label>Bagian<span class="text-red">*</span></label><select class="form-control select2" style="width: 100%;" id="bagian" name="bagian" data-placeholder="Choose a Bagian..." required>>@foreach($bagians as $bagians1)<option value="{{ $bagians1 }}">{{ $bagians1 }}</option>@endforeach</select></div><div class="form-group">
              </div>
            </div>
        
          </div>
        </div>

        <div id="tambah2">
        <input type="text" name="lop2" id="lop2" value="1" hidden="">
        </div>
        
      </div>
      <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-warning">Add</button>
              </div>
    </div>
  </div>
</form>
</div>


@stop

@section('scripts')
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
  jQuery(document).ready(function() { 
    fillexample1();
    $('.select2').select2({
      dropdownAutoWidth : true,
      width: '100%',
    });
  });
  
  function deleteConfirmation(url, name, id) {
    jQuery('.modal-body').text("Are you sure want to delete '" + name + "'");
    jQuery('#modalDeleteButton').attr("href", url+'/'+id);
  }

function fillexample1(){
  $('#example1 tfoot th').each( function () {
    var title = $(this).text();
    $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
  });
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
    { "data": "edit"}
    // { "data": "hapus"}
      ]
    });

  table.columns().every( function () {
    var that = this;

    $( 'input', this.footer() ).on( 'keyup change', function () {
      if ( that.search() !== this.value ) {
        that
        .search( this.value )
        .draw();
      }
    } );
  });

  $('#example1 tfoot tr').appendTo('#example1 thead');
}

function addOP() {
  $('#modalAdd').modal('show');
}

function editop(id){
    var data = {
      id : id
    }
    $.get('{{ url("edit/Op") }}', data, function(result, status, xhr){
      console.log(status);
      console.log(result);
      console.log(xhr);
      if(xhr.status == 200){
        if(result.status){
          $('#modalDetailBodyEdit').html('');
          $('#modalDetailBodyEditHeader').html('');
          
          $.each(result.id_op, function(key, value) {
            
            $('#modalDetailBodyEditHeader').append('<input type="text" name="cat" id="cat" value="'+ value.line +'" hidden><input type="text" name="loc" id="loc" value="'+ value.bagian +'" hidden><input type="text" name="id" value="'+ value.id +'" hidden><label>TAG<span class="text-red">*</span></label><input class="form-control" style="width: 100%;" id="tag" name="tag" data-placeholder="Input a TAG..." required value="'+ value.tag +'"><label>Nama<span class="text-red">*</span></label><input class="form-control" style="width: 100%;" id="nama" name="nama" data-placeholder="Input a NAMA..." required value="'+ value.nama +'"><label>NIK<span class="text-red">*</span></label><input class="form-control" style="width: 100%;" id="nik" name="nik" data-placeholder="Input a NIK..." required value="'+ value.nik +'"><label>Line<span class="text-red">*</span></label><select class="form-control select2" style="width: 100%;" id="line" name="line" data-placeholder="Choose a Line..."><option></option>@foreach($lines as $lines)<option value="{{ $lines }}">{{ $lines }}</option> @endforeach</select></div><div class="form-group"><label>Bagian<span class="text-red">*</span></label><select class="form-control select2" style="width: 100%;" id="bagian" name="bagian" data-placeholder="Choose a Bagian..." required>>@foreach($bagians as $bagians)<option value="{{ $bagians }}">{{ $bagians }}</option>@endforeach</select></div><div class="form-group">').find('.select2').select2();
           
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
</script>

@stop