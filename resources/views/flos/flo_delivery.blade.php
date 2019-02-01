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
    Final Line Outputs <span class="text-purple">ファイナルライン出力</span>
    <small>Delivery to Warehouse <span class="text-purple">倉庫送り</span></small>
  </h1>
  <ol class="breadcrumb">
    <li>
      <button href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#reprintModal">
        <i class="fa fa-print"></i>&nbsp;&nbsp;Reprint FLO
      </button>
    </li>
  </ol>
</section>
@endsection

@section('content')
<section class="content">
  @if (session('error'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Error!</h4>
    {{ session('error') }}
  </div>   
  @endif
  @if (session('status'))
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Success!</h4>
    {{ session('status') }}
  </div>   
  @endif
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
       <div class="box-header">
        <h3 class="box-title">Finished Goods Receipt <span class="text-purple">完成品レシート</span></h3>
      </div>
      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="row">
          <div class="col-md-12">
            <div class="input-group col-md-8 col-md-offset-2">
              <div class="input-group-addon" id="icon-serial" style="font-weight: bold">
                <i class="glyphicon glyphicon-barcode"></i>
              </div>
              <input type="text" style="text-align: center; font-size: 22" class="form-control" id="flo_number_settlement" name="flo_number_settlement" placeholder="Scan FLO Here..." required>
              <div class="input-group-addon" id="icon-serial">
                <i class="glyphicon glyphicon-ok"></i>
              </div>
            </div>
            <br>
            <table id="flo_table" class="table table-bordered table-striped table-hover" style="width: 100%;">
              <thead style="background-color: rgba(126,86,134,.7);">
                <tr>
                  <th style="width: 10%">FLO</th>
                  <th style="width: 10%">Dest.</th>
                  <th style="width: 5%">Ship. Date</th>
                  <th style="width: 5%">By</th>
                  <th style="width: 5%">Material</th>
                  <th style="width: 30%">Description</th>
                  <th style="width: 10%">Qty</th>
                  <th style="width: 20%">Scan Date</th>
                  <th style="width: 5%">Cancel</th>
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
</div>

<div class="modal modal-default fade" id="reprintModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="titleModal">Reprint FLO</h4>
      </div>
      <form class="form-horizontal" role="form" method="post" action="{{url('reprint/flo')}}">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="modal-body" id="messageModal">
          <label>FLO Number</label>
          <select class="form-control select2" name="flo_number_reprint" style="width: 100%;" data-placeholder="Choose a FLO..." id="flo_number_reprint" required>
            <option value=""></option>
            @foreach($flos as $flo)
            <option value="{{ $flo->flo_number }}">{{ $flo->flo_number }} || {{ $flo->shipmentschedule->material_number }} || {{ $flo->shipmentschedule->material->material_description }}</option>
            @endforeach
          </select>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button id="modalReprintButton" type="submit" class="btn btn-danger"><i class="fa fa-print"></i>&nbsp; Reprint</button>
        </div>
      </form>
    </div>
  </div>
</div>

</section>
@stop

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
  jQuery(document).ready(function() {
    $(function () {
      $('.select2').select2()
    });

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    fillFloTableSettlement();

    refresh();

    var delay = (function(){
      var timer = 0;
      return function(callback, ms){
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
      };
    })();

    $("#flo_number_settlement").on("input", function() {
     delay(function(){
       if ($("#flo_number_settlement").val().length < 10) {
         $("#flo_number_settlement").val("");
       }
     }, 100 );
   });

    $('#flo_number_settlement').keydown(function(event) {
      if (event.keyCode == 13 || event.keyCode == 9) {
        if($("#flo_number_settlement").val().length > 7){
          scanFloNumber();
          return false;
        }
        else{
          openErrorGritter('Error!', 'FLO number invalid.');
          $("#flo_number_settlement").val("");
          audio_error.play();
        }
      }
    });

  });

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

  function scanFloNumber(){
    var flo_number = $("#flo_number_settlement").val();
    var data = {
      flo_number : flo_number,
      status : '2',
    }
    $.post('{{ url("scan/flo_settlement") }}', data, function(result, status, xhr){
      console.log(status);
      console.log(result);
      console.log(xhr);
      if(xhr.status == 200){
        if(result.status){
          openSuccessGritter('Success!', result.message);
          $('#flo_table').DataTable().ajax.reload();
          $("#flo_number_settlement").val("");
          $('#flo_detail_table').DataTable().destroy();
          $('#flo_number').val("");
          refresh();
          $("#flo_number_settlement").focus();
        }
        else{
          openErrorGritter('Error!', result.message);
          $("#flo_number_settlement").val("");
          audio_error.play();          
        }
      }
      else{
        openErrorGritter('Error!', 'Disconnected from server');
        $("#flo_number_settlement").val("");
        audio_error.play();
      }

    });
  }

  function fillFloTableSettlement(){
    var data = {
      status : '2',
    }
    $('#flo_table tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
    });
    var table = $('#flo_table').DataTable( {
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
        "type" : "post",
        "url" : "{{ url("index/flo") }}",
        "data" : data,
      },
      "columns": [
      { "data": "flo_number" },
      { "data": "destination_shortname" },
      { "data": "st_date" },
      { "data": "shipment_condition_name" },
      { "data": "material_number" },
      { "data": "material_description" },
      { "data": "actual" },
      { "data": "updated_at" },
      { "data": "action" }
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
      });
    });

    $('#flo_table tfoot tr').appendTo('#flo_table thead');
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

  function openSuccessGritter(title, message){
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-success',
      image: '{{ url("images/image-screen.png") }}',
      sticky: false,
      time: '2000'
    });
  }

  function openInfoGritter(title, message){
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-info',
      image: '{{ url("images/image-unregistered.png") }}',
      sticky: false,
      time: '2000'
    });
  }

  function cancelConfirmation(id){
    var flo_number = $("#flo_number_settlement").val(); 
    var data = {
      id: id,
      flo_number : flo_number,
      status : '2',
    };
    if(confirm("Are you sure you want to cancel this settlement?")){
      $.post('{{ url("cancel/flo_settlement") }}', data, function(result, status, xhr){
        if(xhr.status == 200){
          if(result.status){
            openSuccessGritter('Success!', result.message);
            $('#flo_table').DataTable().ajax.reload();
            $("#flo_number_settlement").val("");
            $("#flo_number_settlement").focus();          
          }
          else{
            openErrorGritter('Error!', result.message);
            audio_error.play();
          }
        }
        else{
          openErrorGritter('Error!', 'Disconnected from server');
          audio_error.play();
        }
      });
    }
    else{
      return false;
    }
  }

  function refresh(){
    $('#flo_number_settlement').val('');
    $('#flo_number_settlement').focus();
  }

</script>

@stop