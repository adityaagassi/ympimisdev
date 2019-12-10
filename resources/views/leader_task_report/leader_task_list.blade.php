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
    Leader Task Report of {{ $leader_name }} on {{ $monthTitle }}
    <small>it all starts here</small>
  </h1>
  <ol class="breadcrumb">
    {{-- <li></li> --}}
    <li><a href="{{ url("index/leader_task_report/index/".$id)}}" class="btn btn-warning btn-sm" style="color:white">Back</a></li>
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
          <div class="col-xs-12">
            <div class="box-header">
              <h3 class="box-title">Filter <span class="text-purple">Leader Task Report</span></h3>
            </div>
            <form role="form" method="post" action="{{url('index/leader_task_report/filter_leader_task/'.$id.'/'.$leader_name)}}">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <div class="col-md-12 col-md-offset-4">
              <div class="col-md-3">
                <div class="form-group">
                  <label>Date</label>
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control pull-right" id="date" name="date" autocomplete="off" placeholder="Choose a Date">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12 col-md-offset-4">
              <div class="col-md-3">
                <div class="form-group pull-right">
                  <a href="{{ url("index/leader_task_report/index/".$id)}}" class="btn btn-warning">Back</a>
                  <a href="{{ url('index/leader_task_report/leader_task_list/'.$id.'/'.$leader_name) }}" class="btn btn-danger">Clear</a>
                  <button type="submit" class="btn btn-primary col-sm-14">Search</button>
                </div>
              </div>
            </div>
            </form>
          </div>
          <table id="example1" class="table table-bordered table-striped table-hover">
            <thead style="background-color: rgba(126,86,134,.7);">
              <tr>
                <th>Activity Name</th>
                <th>Activity Type</th>
                <th>Details</th>                
              </tr>
            </thead>
            <tbody>
              @foreach($activity_list as $activity_list)
              <tr>
                <td>{{$activity_list->activity_name}}</td>
                <td>{{$activity_list->activity_type}}</td>
                <td>
                  @if($activity_list->link != null)
                    <a target="_blank" class="btn btn-primary btn-sm" href="{{url("$activity_list->link")}}">Details</a>
                  @else
                    <a class="btn btn-primary btn-sm" href="">Data Tidak Tersedia</a>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <th></th>
                <th></th>
                <th></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </section>

  @stop

  @section('scripts')
  <script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
  <script src="{{ url("js/buttons.flash.min.js")}}"></script>
  <script src="{{ url("js/jszip.min.js")}}"></script>
  <script src="{{ url("js/vfs_fonts.js")}}"></script>
  <script src="{{ url("js/buttons.html5.min.js")}}"></script>
  <script src="{{ url("js/buttons.print.min.js")}}"></script>
  <script>
    $('#date').datepicker({
      autoclose: true,
      format: "yyyy-mm",
      startView: "months", 
      minViewMode: "months",
      autoclose: true,
    });
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
  </script>

  @stop