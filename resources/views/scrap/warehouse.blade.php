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
  }
  tbody>tr>td{
    text-align:center;
  }
  tfoot>tr>th{
    text-align:center;
  }
  th:hover {
    overflow: visible;
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
    border:1px solid black;
    vertical-align: middle;
    padding:0;
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid black;
    padding:0;
  }
  td{
    overflow:hidden;
    text-overflow: ellipsis;
  }

  .table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
    background-color: #ffd8b7;
  }

  .table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
    background-color: #FFD700;
  }
  #loading, #error { display: none; }
</style>
@stop

@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box" style="background-color: #ffffff">
       <div class="box-header">
        <h3 class="box-title">Scrap Warehouse</h3>
      </div>
      <div class="box-body" style="padding-bottom: 30px;">
        <div class="row">
          <div class="col-md-12">
            <div class="input-group col-md-8 col-md-offset-2">
              <div class="input-group-addon" id="icon-serial" style="font-weight: bold">
                <i class="glyphicon glyphicon-barcode"></i>
              </div>
              <input type="hidden" value="{{csrf_token()}}" name="_token" />
              <input type="text" style="text-align: center; font-size: 30px; height: 75px" class="form-control" id="slip_scrap_number" placeholder="Scan Scrap Slip Here..." required>
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
      <div class="col-md-12" style="margin-left: 0px;margin-right: 0px;padding-bottom: 0px;padding-left: 0px">
          <div class="col-xs-2" style="padding-left: 0;">
              <div class="input-group date">
                  <div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From">
              </div>
          </div>
          <div class="col-xs-2" style="padding-left: 0;">
              <div class="input-group date">
                  <div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Date To" onchange="fetchScrapDetail()">
              </div>
          </div> 
          <!-- <div class="col-xs-2" style="padding-left: 0;">
              <button class="btn btn-success pull-left" onclick="fetchScrapDetail()" style="font-weight: bold;">
                    Search
              </button>
          </div> -->
      </div>

<div class="row">
  <div class="col-xs-12" style="padding-top: 1%;">
    <!-- <button style="margin: 1%;" class="btn btn-info pull-right" onClick="refreshTable()"><i class="fa fa-refresh"></i> Refresh Tabel Scrap</button> -->

    <div class="nav-tabs-custom">
      <ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
        <li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Scrap List Detail</a></li>
        <!-- <li class="vendor-tab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">KDO Delivery</a></li> -->
      </ul>

      <div class="tab-content" style="background-color: #ffffff">
        <div class="tab-pane active" id="tab_1">
          <table id="scrap_detail" class="table table-bordered table-striped table-hover" style="width: 100%;">
            <thead style="background-color: rgb(126,86,134); color: #FFD700;">
              <tr>
                <th>Slip Number</th>
                <th>Description</th>
                <th>Category</th>
                <th>Issue Location</th>
                <th>Quantity</th>
                <th>Reason</th>
                <!-- <th>Category Reason</th> -->
                <th>Received</th>
              </tr>
            </thead>
            <!-- <tbody id='bodyDetail'></tbody> -->
            <tbody>
            </tbody>
            <tfoot>
              <tr style="color: black">
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
    $('body').toggleClass("sidebar-collapse");
    $("#slip_scrap_number").focus();
    fetchScrapDetail();
    $("#resume_closure").hide();
    $('.datepicker').datepicker({
        autoclose: true,
        format: "yyyy-mm-dd",
        todayHighlight: true,
        // startView: "months", 
        // minViewMode: "months",
        autoclose: true,
       });
      $('#tanggal').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd',
        todayHighlight: true
       });
  })

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
  var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');


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

  $('#slip_scrap_number').keydown(function(event) {
    if (event.keyCode == 13 || event.keyCode == 9) {

    number = $("#slip_scrap_number").val().replace(/[^0-9]/gi, '');

      if(number.length == 8){
        scanScrap();
        return false;
      }
      else{
        openErrorGritter('Error!', 'Nomor Slip Tidak Sesuai.');
        $("#slip_scrap_number").val('');
        audio_error.play();
      }
    }
  });


  function fetchScrapDetail(){
    var data = {
      status : 2,
      date_from:$('#date_from').val(),
      date_to:$('#date_to').val()
    }
    $('#scrap_detail').DataTable().clear();
    $('#scrap_detail').DataTable().destroy();

    $('#scrap_detail tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input id="pencarian" style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
    });

    var table = $('#scrap_detail').DataTable( {
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
        "url" : "{{ url("fetch/scrap_detail") }}",
        "data" : data,
      },
      "columns": [
      { "data": "slip" },
      { "data": "material_description" },
      { "data": "category" },
      { "data": "issue_location" },
      { "data": "quantity" },
      { "data": "reason" },
      // { "data": "category_reason" },
      { "data": "created_at" }
      ]
    });

    table.columns().every( function () {
      var that = this;
      $( '#pencarian', this.footer() ).on( 'keyup change', function () {
        if ( that.search() !== this.value ) {
          that
          .search( this.value )
          .draw();
        }
      });
    });

    $('#scrap_detail tfoot tr').appendTo('#scrap_detail thead');
  }

  function refreshTable(){
    // $('#scrap_detail').DataTable().ajax.reload();
    fetchScrapDetail();
  }

  // var currClosureID = '';
  function reset(){
    $('#slip_scrap_number').val("");
  }


  function scanScrap(){
    // var number  = $("#slip_scrap_number").val();
    number = $("#slip_scrap_number").val().replace(/[^0-9]/gi, '');
    var data = {
      number : number
    }

    $.get('{{ url("scan/scrap_warehouse") }}', data,  function(result, status, xhr){
      if(result.status){
        openSuccessGritter('Success!', result.message);
        audio_ok.play();
        $("#slip_scrap_number").val("");
        $("#slip_scrap_number").focus();
        fetchScrapDetail();
      }else{
        openErrorGritter('Error!', result.message);
        audio_error.play();
        $("#slip_scrap_number").val("");
        $("#slip_scrap_number").focus();
        fetchScrapDetail();
      }
    });
  }
</script>

@stop