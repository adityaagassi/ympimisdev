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
                <th>Sheet No.</th>
                <th>Container No.</th>
                <th>Seal No.</th>
                <th>No. Pol</th>
                <th>Dest</th>
                <th>Invoice</th>
                <th>Invoice Date</th>

                <!-- <th>On or About</th> --><th>Stuffing Date</th>
                <th>Payment</th>
                {{-- <th>From</th> --}}
                <th>To</th>
                <th>Carrier</th>
                <th>Action</th>
                <th >View</th>
                <th>Check</th>
              </tr>
            </thead>
            <tbody>
             @foreach($time as $nomor => $time)
             <tr id="{{$time->id_checkSheet}}">
              <td style="font-size: 14">{{$time->id_checkSheet}}</td>
              <td style="font-size: 14">{{$time->countainer_number}}</td>
              <td style="font-size: 14">{{$time->seal_number}}</td>
              <td style="font-size: 14">{{$time->no_pol}}</td>
              <td style="font-size: 14">{{$time->destination}}</td>
              <td style="font-size: 14">{{$time->invoice}}</td>
              <td style="font-size: 14">{{$time->Stuffing_date}}</td>            
              <td style="font-size: 14">{{$time->etd_sub}}</td>
              <td style="font-size: 14">{{$time->payment}}</td>
              {{-- <td style="font-size: 14">{{$time->shipped_from}}</td> --}}
              <td style="font-size: 14">{{$time->shipped_to}}</td>
              <td style="font-size: 14">@if(isset($time->shipmentcondition->shipment_condition_name)){{$time->shipmentcondition->shipment_condition_name}}@else Not registered @endif</td>
             <td>
              @if($time->status != 1) 
              <a href="javascript:void(0)" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#editModal" onclick="editConfirmation('{{ url("edit/CheckSheet") }}', '{{ $time['destination'] }}', '{{ $time['id_checkSheet'] }}');">Edit</a>


              <a href="javascript:void(0)" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("delete/CheckSheet") }}', '{{ $time['destination'] }}', '{{ $time['id'] }}');">Delete</a>
              <p id="id_checkSheet_mastera{{$nomor + 1}}" hidden>{{$time->id_checkSheet}}</p>
              @else
              @endif
            </td>
           <!-- <td>@if($time->status == 1)            
            <span class="label label-success">Checked</span>
            @else
            <span class="label label-warning">Unchecked</span>
            @endif
          </td> -->
          <td><a class="btn btn-info btn-xs" href="{{url('show/CheckSheet', $time['id'])}}">View</a>
            <p id="id_checkSheet_master{{$nomor + 1}}" hidden>{{$time->id_checkSheet}}</p>
          </td>
          <td>
            @if($time->status == 1)            
            <span data-toggle="tooltip"  class="badge bg-green"><i class="fa fa-fw fa-check"></i></span>
            @else
            @if($time->destination != "XYMI")
            <a class="btn btn-warning btn-xs" href="{{url('check/CheckSheet', $time['id'])}}">Check</a>
            @else
            <a class="btn btn-warning btn-xs" href="{{url('checkmarking/CheckSheet', $time['id'])}}">Check</a>
            @endif
            @endif

          </td>

        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          {{-- <th></th> --}}
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
          Format: [Destination][Invoice][GMC][Goods][Marking No][Package Qty][Package Set][Qty Qty][Qty Set]<br>
          Sample: <a href="{{ url('download/manual/import_check_sheet_detail.txt') }}">import_check_sheet_detail.txt</a> Code: #Add
        </div>
        
        <div class="modal-body col-xs-12">
         <div class="col-xs-4">
          <label>CONSIGNEE & ADDRESS</label>
          <input type="text" name="destination" class="form-control" id="destination" required>
          <label>NO POL</label>
          <input type="text" name="nopol" class="form-control" id="nopol" required>
          <label>CONTAINER NO.</label>
          <input type="text" name="countainer_number" class="form-control" id="countainer_number" required>
          <label>SEAL NO</label>
          <input type="text" name="seal_number" class="form-control" id="seal_number">
          <BR>
          <center><input type="file" name="check_sheet_import" id="InputFile" accept="text/plain" required></center>
        </div>

        <div class="col-xs-4">
          <label>SHIPPED FROM</label>
          <input type="text" name="shipped_from" class="form-control" id="shipped_from" value="SURABAYA" readonly>
          <label>SHIPPED TO</label>
          <input type="text" name="shipped_to" class="form-control" id="shipped_to" required>
          <label>CARRIER</label>

          <select class="form-control select2" name="carier" id="carier"  data-placeholder="a" style="width: 100%;" >

            @foreach($carier as $nomor => $carier)
            <option value="{{ $carier->shipment_condition_code }}" > {{$carier->shipment_condition_name}}</option>
            @endforeach
          </select>

          <label>ON OR ABOUT</label>
          <input type="text" name="etd_sub" class="form-control" ID= "etd_sub" required>
        </div>

        <div class="col-xs-4">
          <label>INVOICE NO.</label>
          <input type="text" name="invoice" class="form-control" id="invoice" required>
          <label>DATE</label>
          <input type="text" name="Stuffing_date" class="form-control" id="Stuffing_date" required>
          <label>PAYMENT</label>
          <select class="form-control select2" name="payment" id="payment"  data-placeholder="Choose a Payment ..." style="width: 100%;" >

            <option value="T/T REMITTANCE">T/T REMITTANCE</option>
            <option value="D/P AT SIGHT">D/P AT SIGHT</option>
            <option value="D/A 60 DAYS AFTER BL DATE">D/A 60 DAYS AFTER BL DATE</option>
          </select>
          <label>SHIPPER</label>
          <input type="text" name="" class="form-control" value="PT. YMPI" readonly>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button id="modalImportButton" type="submit" class="btn btn-success">Import</button>
      </div>
    </form>
  </div>
</div>
</div>

<div class="modal  fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel"><p id="myModalLabelt">Edit Confirmation</p></h4>
      </div>
      <div class="modal-body" id="modalDeleteBody">
        <form id ="Editform" method="post" action="" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="modal-body col-xs-12">
         <div class="col-xs-4">
          <label>CONSIGNEE & ADDRESS</label>
          <input type="text" name="destinationE" class="form-control" id="destinationE" required>
          <label>NO POL</label>
          <input type="text" name="nopolE" class="form-control" id="nopolE" required>
          <label>CONTAINER NO.</label>
          <input type="text" name="countainer_numberE" class="form-control" id="countainer_numberE" required>
          <label>SEAL NO</label>
          <input type="text" name="seal_numberE" class="form-control" id="seal_numberE" required>

        </div>

        <div class="col-xs-4">
          <label>SHIPPED FROM</label>
          <input type="text" name="shipped_from" class="form-control" id="shipped_from" value="SURABAYA" readonly>
          <label>SHIPPED TO</label>
          <input type="text" name="shipped_toE" class="form-control" id="shipped_toE" required>
          <label>CARRIER</label>

          <select class="form-control select2" name="carierE" id="carierE"  data-placeholder="aaaaa" style="width: 100%;" >

            @foreach($carier1 as $nomor => $carier)
            <option value="{{ $carier->shipment_condition_code }}" > {{$carier->shipment_condition_name}}</option>
            @endforeach
          </select>

          <label>ON OR ABOUT</label>
          <input type="text" name="etd_subE" class="form-control" ID= "etd_subE" required>
        </div>

        <div class="col-xs-4">
          <label>INVOICE NO.</label>
          <input type="text" name="invoiceE" class="form-control" id="invoiceE" required>
          <label>DATE</label>
          <input type="text" name="Stuffing_dateE" class="form-control" id="Stuffing_dateE" required>
          <label>PAYMENT</label>
          <select class="form-control select2" name="paymentE" id="paymentE"  data-placeholder="Choose a Payment ..." style="width: 100%;" >

             <option value="T/T REMITTANCE">T/T REMITTANCE</option>
            <option value="D/P AT SIGHT">D/P AT SIGHT</option>
            <option value="D/A 60 DAYS AFTER BL DATE">D/A 60 DAYS AFTER BL DATE</option>
          </select>
          <label>SHIPPER</label>
          <input type="text" name="" class="form-control" value="PT. YMPI" readonly>
        </div>
      </div>
      <input type="text" name="id_chek" id="id_chek" hidden>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button id="modaleditButton" type="submit" class="btn btn-success">Edit</button>
      </div>
    </form>
      </div>      
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
  $('#etd_sub').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd',
  })
  $('#Stuffing_date').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd',
  })
  $('#etd_subE').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd',
  })
  $('#Stuffing_dateE').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd',
  })
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

  function editConfirmation(url, name, id) {

   
    jQuery('#modaleditButton').attr("href", url+'/'+id);

    var cont;
    var seal;
    var pol;
    var dest;
    var inv;
    var invd;
    var date;
    var pay;
    var to;
    var carier;
    var id_chek;
    id_chek = $("#"+id+" td:nth-child(1)").text();
    cont = $("#"+id+" td:nth-child(2)").text();
    seal = $("#"+id+" td:nth-child(3)").text();
    pol = $("#"+id+" td:nth-child(4)").text();
    dest = $("#"+id+" td:nth-child(5)").text();
    inv = $("#"+id+" td:nth-child(6)").text();
    invd = $("#"+id+" td:nth-child(7)").text();
    date = $("#"+id+" td:nth-child(8)").text();
    pay = $("#"+id+" td:nth-child(9)").text();
    to = $("#"+id+" td:nth-child(10)").text();
    carier = $("#"+id+" td:nth-child(11)").text();
    if (carier =="SEA"){
      carier = "C1";
    }else if(carier =="AIR"){
      carier = "C2";
    }else{
      carier = "TR";
    }
    
    document.getElementById("countainer_numberE").value = cont; 
    document.getElementById("seal_numberE").value = seal;
    document.getElementById("nopolE").value = pol;
    document.getElementById("invoiceE").value = inv;
    document.getElementById("destinationE").value = dest;
    document.getElementById("shipped_toE").value = to;
    document.getElementById("Stuffing_dateE").value = invd;
    document.getElementById("etd_subE").value = date;
    document.getElementById("id_chek").value = id_chek;
    document.getElementById("myModalLabelt").innerHTML = "Edit Confirmation "+id_chek;

    
    $("#carierE option[value='"+carier+"']").prop('selected', true);
    $("#paymentE option[value='"+pay+"']").prop('selected', true);
    $('#Editform').attr('action', url+'/'+id);
    
    
    
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