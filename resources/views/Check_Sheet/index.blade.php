@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style>
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
}
table.table-bordered > tfoot > tr > th{
  border:1px solid rgb(211,211,211);
}
#loading, #error { display: none; }
</style>
@stop
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
      <!-- <a href="{{ url("create/Standard_time")}}" class="btn btn-primary btn-sm" style="color:white">Create {{ $page }}</a> -->
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
       {{--  <div class="box-header">
        <h3 class="box-title">Data Table With Full Features</h3>
      </div> --}}
      <!-- /.box-header -->
      <div class="box-body">
        <div class="table-responsive">
        <table id="example1" class="table table-bordered table-striped">
          <thead style="background-color: rgba(126,86,134,.7);">
            <tr>
              <th>Check Sheet Expore No.</th>
              <th>Container No.</th>
              <th>Destination</th>
              <th>Invoice</th>
              <th>Invoice Date</th>
              <th>Seal No.</th>
              <th>On or About</th>
              <th>Payment</th>
              <th>Shipped From</th>
              <th>Shipped To</th>
              <th>Carrier</th>
              <th>Status</th>
              <th >View</th>
              <th>Check</th>
            </tr>
          </thead>
          <tbody>
           @foreach($time as $nomor => $time)
           <tr>
            <td style="font-size: 14">{{$time->id_checkSheet}}</td>
            <td style="font-size: 14">{{$time->countainer_number}}</td>
            <td style="font-size: 14">{{$time->destination}}</td>
            <td style="font-size: 14">{{$time->invoice}}</td>
            <td style="font-size: 14">{{$time->Stuffing_date}}</td>
            <td style="font-size: 14">{{$time->seal_number}}</td>
            <td style="font-size: 14">{{$time->etd_sub}}</td>
            <td style="font-size: 14">{{$time->payment}}</td>
            <td style="font-size: 14">{{$time->shipped_from}}</td>
            <td style="font-size: 14">{{$time->shipped_to}}</td>
            <td style="font-size: 14">
             @if(isset($time->shipmentcondition->shipment_condition_name))
              {{$time->carier}} - {{$time->shipmentcondition->shipment_condition_name}}
             @else
             {{$time->carier}} - Not registered
             @endif
           </td>
           <td>@if($time->status == 1)            
            <span class="label label-success">Checked</span>
            @else
            <span class="label label-warning">Unchecked</span>
            @endif
           </td>
           <td><a class="btn btn-info btn-xs" href="{{url('show/CheckSheet', $time['id'])}}">View</a>
            <p id="id_checkSheet_master{{$nomor + 1}}" hidden>{{$time->id_checkSheet}}</p>
           </td>
           <td>
            @if($time->status == 1)            
            <span data-toggle="tooltip"  class="badge bg-green"><i class="fa fa-fw fa-check"></i></span>
            @else
            <a class="btn btn-warning btn-xs" href="{{url('check/CheckSheet', $time['id'])}}">Check</a>
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
      <form id ="importForm" method="post" action="{{ url('import/CheckSheet') }}" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Import Confirmation</h4>
          Format: [Code Container][Destination][Invoice][Container Number][Seal Number][Shipped From][Shipped To][Carier][Payment][On Or About][Invoice Date]-[Code Container][Destination][Invoice][GMC][Goods][Marking No][Package Qty][Package Set][Qty Qty][Qty Set]<br>
          Sample: <a href="{{ url('download/manual/import_check_sheet_master_detail.txt') }}">import_check_sheet_master_detail.txt</a> Code: #Add
        </div>
        <div class="">
          <div class="modal-body">
            <center><input type="file" name="check_sheet_import" id="InputFile" accept="text/plain"></center>
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
    $(document).ready(function () {
$('body').toggleClass("sidebar-collapse");
})
    $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
    $('#example1 tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="3"/>' );
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

  function addInspection(id){
    var a = id;
      var id =document.getElementById("id_checkSheet_master"+a).innerHTML;
      // var id2 =document.getElementById("id_checkSheet_master_id").innerHTML;
      // var a = "check/CheckSheet/{"+id2+"}";
      // alert(a)
      var data = {
        
        id:id,
      }
      // $.get('{{ url("check/CheckSheet") }}',id2, function(result, status, xhr){
      // });
      $.post('{{ url("add/CheckSheet") }}', data, function(result, status, xhr){
        console.log(status);
        console.log(result);
        console.log(xhr);
      });
    }
</script>

@stop