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
td{
    overflow:hidden;
    text-overflow: ellipsis;
  }
#loading, #error { display: none; }
</style>
@endsection
@section('header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content-header">
  <h1>
    List of {{ $page }}s
    <small>Validate Your CPAR</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{ url("index/qc_report/create")}}" class="btn btn-primary btn-sm" style="color:white">Create {{ $page }}</a></li>
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
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Filter <span class="text-purple">CPAR</span></h3>
        </div>
        <div class="box-body">
          <input type="hidden" value="{{csrf_token()}}" name="_token" />
          <div class="col-md-12 col-md-offset-3">
            <div class="col-md-3">
              <div class="form-group">
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="bulandari" name="bulandari" placeholder="Bulan Dari">
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="bulanke" name="bulanke" placeholder="Bulan Ke">
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="col-md-4">
              <div class="form-group">
                <select class="form-control select2" data-placeholder="Select Kategori" name="kategori" id="kategori" style="width: 100%;">
                  <option></option>
                  <option value="Eksternal">Eksternal</option>
                  <option value="Internal">Internal</option>
                  <option value="Supplier">Supplier</option>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <select class="form-control select2" data-placeholder="Select Departemen" name="department_id" id="department_id" style="width: 100%;">
                  <option></option>
                  @foreach($departments as $department)
                  <option value="{{ $department->id }}">{{ $department->department_name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <select class="form-control select2" data-placeholder="Select CPAR Status" name="status_code" id="status_code" style="width: 100%;">
                  <option></option>
                  @foreach($statuses as $status)
                  <option value="{{ $status->status_code }}">{{ $status->status_code }} - {{ $status->status_name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          <div class="col-md-12 col-md-offset-5">
            <div class="form-group">
              <a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
              <button id="search" onClick="fillCPARDetail()" class="btn btn-primary">Search</button>
            </div>
          </div>
          <div class="box-body" style="overflow-x: scroll;">
          <table id="example1" class="table table-bordered table-striped table-hover" >
            <thead style="background-color: rgba(126,86,134,.7);">
              <tr>
                <th>No CPAR</th>
                <th>Kategori</th> 
                <th>Manager</th>    
                <th>Lokasi</th>
                <th>Tgl Permintaan</th>
                <th>Tgl Balas</th>
                <th>Judul Komplain</th>
                <th>Departemen</th>
                <th>Sumber Komplain</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <!-- @foreach($cpars as $cpar)
              <tr>
                <td>{{$cpar->cpar_no}}</td>
                <td>{{$cpar->name}}</td>
                <td style="width: 10%">{{$cpar->lokasi}}</td>
                <td>{{$cpar->tgl_permintaan}}</td>
                <td>{{$cpar->tgl_balas}}</td>
                <td>{{$cpar->via_komplain}}</td>
                <td>{{$cpar->department_name}}</td>
                <td>{{$cpar->sumber_komplain}}</td>
                <td><label class="label label-success">{{$cpar->status_name}}</label></td>
                <td  style="width: 10%">
                  <center>
                    <a href="{{url('index/qc_report/update', $cpar['id'])}}" class="btn btn-warning btn-xs">Edit</a>
                    <a href="javascript:void(0)" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("index/qc_report/delete") }}', '{{ $cpar['cpar_no'] }}', '{{ $cpar['id'] }}');">
                      Delete
                    </a>
                  </center>
                </td>
              </tr>
              @endforeach -->
            </tbody>
            <tfoot>
              <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
              </tr>
            </tfoot>
          </table>
        </div>
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
        Are you sure want to delete this CPAR?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
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

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");
    fillCPARDetail();
    $("#navbar-collapse").text('');
    $('#bulandari').datepicker({
        format: "yyyy-mm",
        startView: "months", 
        minViewMode: "months",
        autoclose: true
      });
      $('#bulanke').datepicker({
        format: "yyyy-mm",
        startView: "months", 
        minViewMode: "months",
        autoclose: true
      });
      $('.select2').select2({
        language : {
          noResults : function(params) {
            return "There is no cpar with status 'close'";
          }
        }
      });
    });


  function clearConfirmation(){
    location.reload(true);
  }

  function fillCPARDetail(){
    $('#example1').DataTable().destroy();
    var bulandari = $('#bulandari').val();
    var bulanke = $('#bulanke').val();
    var kategori = $('#kategori').val();
    var department_id = $('#department_id').val();
    var status_code = $('#status_code').val();
    var data = {
      bulandari:bulandari,
      bulanke:bulanke,
      department_id:department_id,
      status_code:status_code,
      kategori:kategori
    }
    $('#example1 tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
    } );
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
      'paging': true,
      'lengthChange': true,
      'searching': true,
      'ordering': true,
      'order': [],
      'info': true,
      'autoWidth': true,
      "sPaginationType": "full_numbers",
      "bJQueryUI": true,
      "bAutoWidth": false,
      "processing": true,
        // "serverSide": true,
        "ajax": {
          "type" : "post",
          "url" : "{{ url("index/qc_report/filter_cpar") }}",
          "data" : data,
        },
        "columns": [
          { "data": "cpar_no" },
          { "data": "kategori" },
          { "data": "name" },
          { "data": "lokasi" },
          { "data": "tgl_permintaan" },
          { "data": "tgl_balas" },
          { "data": "judul_komplain" },
          { "data": "department_name" , "width": "15%"},
          { "data": "sumber_komplain" },
          { "data": "status_name" },
          { "data": "action", "width": "10%"}
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
      } );

      $('#example1 tfoot tr').appendTo('#example1 thead');
  }

   function deleteConfirmation(id) {
    jQuery('#modalDeleteButton').attr("href", '{{ url("index/qc_report/delete") }}'+'/'+id);
  }

  

</script>

@stop