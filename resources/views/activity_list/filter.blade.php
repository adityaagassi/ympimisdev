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
    {{ $act_name }} {{$frekuensi}} - {{ $dept_name }}
    <!-- <small>it all starts here</small> -->
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{ url("index/production_report/index/".$id)}}" class="btn btn-warning btn-sm" style="color:white">Back</a>&nbsp
      <!-- <a href="{{ url("index/activity_list/create_by_department/".$id.'/'.$no)}}" class="btn btn-primary btn-sm" style="color:white">Create {{ $page }}</a> -->
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
  <div class="row">
    <div class="col-xs-12">
      <div class="box box-solid">
        <div class="box-body" style="overflow-x: scroll;">
          <center>
            @foreach($activity_list as $activity_list)
          <a class="btn btn-primary" href="{{url('index/production_report/activity/'.$activity_list->id)}}" style="margin-bottom: 10px;white-space: normal;width: 200px;font-size: 17px">{{$activity_list->activity_name}}<br><b style="font-size: 15px">{{$activity_list->leader_dept}}</b></a> <br>
          @if($activity_list->activity_type == "Audit")
          <a class="btn btn-info" href="{{url('index/point_check_audit/index/'.$activity_list->id)}}" style="margin-bottom: 10px;white-space: normal;width: 200px;font-size: 17px">Point Check Master</a><br>
          @endif
          @if($activity_list->activity_type == "Laporan Aktivitas")
          <a class="btn btn-info" style="margin-bottom: 10px;white-space: normal;width: 200px;font-size: 17px" href="{{url('index/audit_guidance/index/'.$activity_list->id)}}">Schedule Audit IK</a> <br>
          @endif
          @if($activity_list->activity_type == "Cek Area")
          <a class="btn btn-info" style="margin-bottom: 10px;white-space: normal;width: 200px;font-size: 17px" href="{{url('index/area_check_point/index/'.$activity_list->id)}}">Point Check</a> <br>
          @endif
          @if($activity_list->activity_type == "Jishu Hozen")
          <a class="btn btn-info" style="margin-bottom: 10px;white-space: normal;width: 200px;font-size: 17px" href="{{url('index/jishu_hozen_point/index/'.$activity_list->id)}}">Master Pengecekan</a> <br>
          @endif
          @endforeach
          </center>
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
          Are you sure delete?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
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

      $('body').toggleClass("sidebar-collapse");

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
    function deleteConfirmation(url, name, id,department_id,no) {
      jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
      jQuery('#modalDeleteButton').attr("href", url+'/'+id +'/'+department_id+'/'+no);
    }
  </script>

  @stop