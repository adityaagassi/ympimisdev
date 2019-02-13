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
    <small>it all starts here</small>
  </h1>
  <ol class="breadcrumb">

    <li>
      <a data-toggle="modal" data-target="#importModal" class="btn btn-success btn-sm" style="color:white">Import {{ $page }}s</a>
      &nbsp;
      <a href="{{ url("create/weekly_calendar")}}" class="btn btn-primary btn-sm" style="color:white">Create {{ $page }}</a>
    </li>
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
        <div class="box-body">
          <table id="example1" class="table table-bordered table-striped table-hover">
            <thead style="background-color: rgba(126,86,134,.7);">
              <tr>
                <th>Fiscal Year</th>
                <th>Week Name</th>
                <th>Date From</th>
                <th>Date To</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($weekly_calendars as $weekly_calendar)
              <tr>
                <td style="width: 10%">{{$weekly_calendar->fiscal_year}}</td>
                <td style="width: 10%">{{$weekly_calendar->week_name}}</td>
                <td>{{date('d-M-Y', strtotime($weekly_calendar->date_from))}}</td>
                <td>{{date('d-M-Y', strtotime($weekly_calendar->date_to))}}</td>
                <td>
                  <center>
                    <a class="btn btn-info btn-xs" href="{{url('show/weekly_calendar/' . $weekly_calendar->week_name . '/' . $weekly_calendar->fiscal_year)}}">View</a>
                    <a href="{{url('edit/weekly_calendar/' . $weekly_calendar->week_name . '/' . $weekly_calendar->fiscal_year)}}" class="btn btn-warning btn-xs">Edit</a>
                    <a href="javascript:void(0)" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("destroy/weekly_calendar") }}', '{{$weekly_calendar->week_name}}', '{{ $weekly_calendar->fiscal_year }}');">
                      Delete
                    </a>
                  </center>
                </td>
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
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
</section>

<div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
      </div>
      <div class="modal-body" id="modalDeleteBody">
        Are you sure delete?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id ="importForm" method="post" action="{{ url('import/weekly_calendar') }}" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Import Confirmation</h4>
          Format: [FY][Week][Date From][Date To]<br>
          Sample: <a href="{{ url('download/manual/import_weekly_calendar.txt') }}">import_weekly_calendar.txt</a> Code: #Add
        </div>
        <div class="">
          <div class="modal-body">
            <center><input type="file" name="weekly_calendar" id="InputFile" accept="text/plain"></center>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button id="modalImportButton" type="submit" class="btn btn-success">Import</button>
          </div>
        </form>
      </div>
    </div>
  </div>
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
  jQuery(document).ready(function() {
    $('#example1 tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
    } );
    var table = $('#example1').DataTable({
     "order": [],
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
    }
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

  });
  $(function () {
    
    $('#example2').DataTable({
      'paging'      : true,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false
    })
  })
  function deleteConfirmation(url, week_name, fiscal_year) {
    jQuery('#modalDeleteBody').text("Are you sure want to delete '" + name + "'");
    jQuery('#modalDeleteButton').attr("href", url+'/'+week_name+'/'+fiscal_year);
  }
</script>

@stop