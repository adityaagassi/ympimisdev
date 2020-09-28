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
    Knock Down Outputs <span class="text-purple"> ???</span>
    <small>Delivery to Warehouse <span class="text-purple">倉庫送り</span></small>
  </h1>
</section>
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
       <div class="box-header">
        <h3 class="box-title">Knock Downs Receipt <span class="text-purple">?? レシート</span></h3>
      </div>
      <div class="box-body" style="padding-bottom: 30px;">
        <div class="row">
          <div class="col-md-12">
            <div class="input-group col-md-8 col-md-offset-2">
              <div class="input-group-addon" id="icon-serial" style="font-weight: bold">
                <i class="glyphicon glyphicon-barcode"></i>
              </div>
              <input type="text" style="text-align: center; font-size: 22" class="form-control" id="kdo_number_delivery" placeholder="Scan KDO Here..." required>
              <div class="input-group-addon" id="icon-serial">
                <i class="glyphicon glyphicon-ok"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xs-12" style="padding-top: 1%;">
    <div class="nav-tabs-custom">
      <ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
        <li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">KDO Delivery Detail</a></li>
        <li class="vendor-tab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">KDO Delivery</a></li>
      </ul>

      <div class="tab-content">
        <div class="tab-pane active" id="tab_1">
          <table id="kdo_detail" class="table table-bordered table-striped table-hover" style="width: 100%;">
            <thead style="background-color: rgba(126,86,134,.7);">
              <tr>
                <th style="width: 13%">KDO</th>
                <th style="width: 13%">Material Number</th>
                <th style="width: 30%">Material Description</th>
                <th style="width: 5%">Location</th>
                <th style="width: 15%">Received At</th>
                <th style="width: 5%">Quantity</th>
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
              </tr>
            </tfoot>
          </table>
        </div>
        <div class="tab-pane" id="tab_2">
          <table id="kdo_table" class="table table-bordered table-striped table-hover" style="width: 100%;">
            <thead style="background-color: rgba(126,86,134,.7);">
              <tr>
                <th style="width: 10%">KDO</th>
                <th style="width: 10%">Count Item</th>
                <th style="width: 10%">Location</th>
                <th style="width: 10%">Received At</th>
                <th style="width: 8%">Reprint</th>
                <th style="width: 8%">Cancel Delivery</th>
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
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</section>
@stop

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script type="text/javascript">
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  jQuery(document).ready(function() {
    $("#kdo_number_delivery").focus();
    fetchKDO();
    fetchKDODetail();
  })

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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

  $('#kdo_number_delivery').keydown(function(event) {
    if (event.keyCode == 13 || event.keyCode == 9) {
      if($("#kdo_number_delivery").val().length >= 10){
        scan_kdo_delivery();
        return false;
      }
      else{
        openErrorGritter('Error!', 'Nomor KDO tidak sesuai.');
        $("#kdo_number_delivery").val("");
        audio_error.play();
      }
    }
  });

  function fetchKDODetail(){
    var data = {
      status : 2,
    }

    $('#kdo_detail tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
    });
    var table = $('#kdo_detail').DataTable( {
      'paging'        : true,
      'dom': 'Bfrtip',
      'responsive': true,
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
        "url" : "{{ url("fetch/kdo_detail") }}",
        "data" : data,
      },
      "columns": [
      { "data": "kd_number" },
      { "data": "material_number" },
      { "data": "material_description" },
      { "data": "location" },
      { "data": "updated_at" },
      { "data": "quantity" }
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

    $('#kdo_detail tfoot tr').appendTo('#kdo_detail thead');
  }

  function fetchKDO(){
    var data = {
      status : 2,
    }
    $('#kdo_table tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
    });
    var table = $('#kdo_table').DataTable( {
      'paging'        : true,
      'dom': 'Bfrtip',
      'responsive': true,
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
        "url" : "{{ url("fetch/kdo") }}",
        "data" : data,
      },
      "columns": [
      { "data": "kd_number" },
      { "data": "actual_count" },
      { "data": "remark" },
      { "data": "updated_at" },
      { "data": "reprintKDO" },
      { "data": "deleteKDO" }
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

    $('#kdo_table tfoot tr').appendTo('#kdo_table thead');
  }

  function reprintKDO(id) {

    var data = {
      kd_number : id,
      printer : 'FLO Printer LOG'
    }

    $("#loading").show();

    if(confirm("Apakah anda ingin mencetak ulang KDO Number dari "+ id +" ?")){
      $.get('{{ url("fetch/kd_reprint_kdo") }}', data,  function(result, status, xhr){
        if(result.status){
          $("#loading").hide();
          openSuccessGritter('Success', result.message);
        }else{
          $("#loading").hide();
          openErrorGritter('Error!', result.message);
        }

      });
    }else{
      $("#loading").hide();

    }   
  }

  function detailKDO(id){
    alert(id);
  }

  function deleteKDO(id){
    var data = {
      kd_number:id
    }
    $.post('{{ url("delete/kdo_delivery") }}', data, function(result, status, xhr){
      if(result.status){
        openSuccessGritter('Success!', result.message);
        $('#kdo_table').DataTable().ajax.reload();
        $('#kdo_detail').DataTable().ajax.reload();
        $("#kdo_number_delivery").val("");
        $("#kdo_number_delivery").focus();
      }
      else{
        openErrorGritter('Error!', result.message);
        $("#kdo_number_delivery").val("");
        $("#kdo_number_delivery").focus();
      }
    });
  }

  function scan_kdo_delivery(){
   var kd_number  = $("#kdo_number_delivery").val();
   var data = {
    kd_number : kd_number,
    status : 2,
  }

  $.post('{{ url("scan/kd_delivery") }}', data, function(result, status, xhr){
    if(result.status){
      openSuccessGritter('Success!', result.message);
      $('#kdo_table').DataTable().ajax.reload();
      $('#kdo_detail').DataTable().ajax.reload();
      $("#kdo_number_delivery").val("");
      $("#kdo_number_delivery").focus();
    }
    else{
      openErrorGritter('Error!', result.message);
      $("#kdo_number_delivery").val("");
      $("#kdo_number_delivery").focus();
    }
  });
  
}
</script>

@stop