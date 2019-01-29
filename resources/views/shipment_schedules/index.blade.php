@extends('layouts.master')
@section('stylesheets')
<style type="text/css">
thead input {
  width: 100%;
  padding: 3px;
  box-sizing: border-box;
}
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
      <a href="{{ url("create/shipment_schedule")}}" class="btn btn-primary btn-sm" style="color:white">Create {{ $page }}</a>
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
          <table id="example1" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Ship. Month</th>
                <th>Ship. Week</th>
                <th>Sales Order</th>
                <th>Ship. Cond.</th>
                <th>Dest</th>
                <th>Material Number</th>
                <th>Description</th>
                <th>HPL</th>
                <th>Ship. Date</th>
                <th>B/L Date</th>
                <th>Qty</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($shipment_schedules as $shipment_schedule)
              <tr>
                <td style="width: 5%">{{ date('M-Y', strtotime($shipment_schedule->st_month))}}</td>
                <td style="width: 5%">
                  @if(isset($shipment_schedule->weeklycalendar->week_name))
                  {{$shipment_schedule->weeklycalendar->week_name}}
                  @else
                  Not registered
                  @endif
                </td>
                <td style="width: 5%">{{$shipment_schedule->sales_order}}</td>
                <td style="width: 5%">
                  @if(isset($shipment_schedule->shipmentcondition->shipment_condition_name))
                  {{$shipment_schedule->shipmentcondition->shipment_condition_name}}
                  @else
                  {{$shipment_schedule->shipment_condition_code}} - Not registered
                  @endif
                </td>
                <td style="width: 5%">
                  @if(isset($shipment_schedule->destination->destination_shortname))
                  {{$shipment_schedule->destination->destination_shortname}}
                  @else
                  {{$shipment_schedule->destination_code}} - Not registered
                  @endif
                </td>
                <td style="width: 5%">{{$shipment_schedule->material_number}}</td>
                <td>
                  @if(isset($shipment_schedule->material->material_description))
                  {{$shipment_schedule->material->material_description}}
                  @else
                  Not registered
                  @endif
                </td>
                <td style="width: 5%">{{$shipment_schedule->hpl}}</td>
                <td style="width: 8%">{{date('d-M-Y', strtotime($shipment_schedule->st_date))}}</td>
                <td style="width: 8%">{{date('d-M-Y', strtotime($shipment_schedule->bl_date))}}</td>
                <td style="width: 5%">{{$shipment_schedule->quantity}}</td>
                <td>
                  <center>
                    <a class="btn btn-info btn-xs" href="{{url('show/shipment_schedule', $shipment_schedule['id'])}}">View</a>
                    <a href="{{url('edit/shipment_schedule', $shipment_schedule['id'])}}" class="btn btn-warning btn-xs">Edit</a>
                    <a href="javascript:void(0)" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("destroy/shipment_schedule") }}', '{{$shipment_schedule->material_number}}', '{{ $shipment_schedule['id'] }}');">
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
      <form id ="importForm" method="post" action="{{ url('import/shipment_schedule') }}" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Import Confirmation</h4>
          Format: [Shipment Month][Sales Order][Shipment Condition Code][Destination Code][Material Number][HPL][Shipment Date][BL Date][Quantity]<br>
          Sample: <a href="{{ url('download/manual/import_shipment_schedule.txt') }}">import_shipment_schedule.txt</a> Code: #Add
        </div>
        <div class="">
          <div class="modal-body">
            <center><input type="file" name="shipment_schedule" id="InputFile" accept="text/plain"></center>
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
    $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="6"/>' );
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
 function deleteConfirmation(url, name, id) {
  jQuery('#modalDeleteBody').text("Are you sure want to delete '" + name + "'");
  jQuery('#modalDeleteButton').attr("href", url+'/'+id);
}
</script>

@stop