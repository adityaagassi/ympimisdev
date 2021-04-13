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

@section('content')
<section class="content">
  <!-- <div class="row">
    <div class="col-xs-12">
      <div class="box">
       <div class="box-header">
        <h3 class="box-title">HR</h3>
      </div>

      <div class="box-body" style="padding-bottom: 30px;">
        <div class="row">
          <div class="col-md-12">
            <div class="input-group col-md-8 col-md-offset-2">
              <div class="input-group-addon" id="icon-serial" style="font-weight: bold">
                <i class="glyphicon glyphicon-barcode"></i>
              </div>
              <input type="text" style="text-align: center; font-size: 22" class="form-control" id="slip_scrap_number" placeholder="Scan Scrap Slip Here..." required>
              <div class="input-group-addon" id="icon-serial">
                <i class="glyphicon glyphicon-ok"></i>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>
</div> -->

	<div class="row">
		<div class="col-xs-12">
			<div class="box no-border" style="margin-bottom: 5px;">
				<div class="box-header" style="margin-top: 10px">
					<h3 class="box-title">Detail Filters<span class="text-purple"> フィルター詳細</span></span></h3>
				</div>
				<div class="row">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<form method="GET" action="{{ url("excel/mutasi_ant/hr") }}">
						<div class="col-xs-12">
							<!-- <div class="col-md-2">
								<div class="form-group">
									<label>Date From</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="datefrom" name="datefrom">
									</div>
								</div>
							</div> -->
							<div class="col-md-2">
								<div class="form-group">
									<label>Date Mutation</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="dateto" name="dateto">
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<div class="col-md-4" style="padding-right: 0;">
										<label style="color: white;"> x</label>
										<button type="button" class="btn btn-primary form-control" onclick="fetchScrapDetail()">Search</button>
									</div>
									<div class="col-md-4" style="padding-right: 0;">
										<label style="color: white;"> x</label>
										<button type="button" class="btn btn-danger form-control" onclick="clearConfirmation()">Clear</button>
									</div>
									<div class="col-md-4" style="padding-right: 0;">
										<label style="color: white;"> x</label>
										<button type="submit" class="btn btn-success form-control"><i class="fa fa-download"></i> Export Excel</button>
									</div>
								</div>
							</div>
							
						</div>
					</form>

				</div>
			</div>

<div class="row">
  <div class="col-xs-12" style="padding-top: 1%;">
    <!-- <button style="margin: 1%;" class="btn btn-info pull-right" onClick="refreshTable()"><i class="fa fa-refresh"></i> Refresh Tabel Scrap</button> -->

    <div class="nav-tabs-custom">
      <ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
        <li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Antar Departemen</a></li>
        <!-- <li class="vendor-tab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">Antar Departemen</a></li> -->
      </ul>

      <div class="tab-content">
        <div class="tab-pane active" id="tab_1">
          <table id="scrap_detail" class="table table-bordered table-striped table-hover" style="width: 100%;">
            <thead style="background-color: rgba(126,86,134,.7);">
              <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama</th>
                <th>Carer Transition</th>
                <th>Career Trans Type</th>
                <th>Tanggal Mutasi</th>
                <th>Position</th>
              </tr>
            </thead>
            <!-- <tbody id='bodyDetail'></tbody> -->
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
    // fillTable();
    $("#resume_closure").hide();
  })

  	$('#datefrom').datepicker({
		autoclose: true,
		todayHighlight: true
	});
	$('#dateto').datepicker({
		autoclose: true,
		format: 'yyyy-mm-dd',
		todayHighlight: true
	});

	$('.datepicker').datepicker({
		autoclose: true,
		format: 'yyyy-mm-dd',
		todayHighlight: true
	});

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
      if($("#slip_scrap_number").val().length == 11){
        scanScrap();
        return false;
      }
      else{
        openErrorGritter('Error!', 'Nomor Slip Tidak Sesuai.');
        $("#slip_scrap_number").val("");
        audio_error.play();
      }
    }
  });


  function fetchScrapDetail(){
  	$('#scrap_detail').DataTable().clear();
	$('#scrap_detail').DataTable().destroy();
    var data = {
      status : 2,
    }

    $('#scrap_detail tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
    });
    var dateto = $('#dateto').val();
    
    var data = {
			dateto:dateto,
		}

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
        }
        // {
        //   extend: 'copy',
        //   className: 'btn btn-success',
        //   text: '<i class="fa fa-copy"></i> Copy',
        //   exportOptions: {
        //     columns: ':not(.notexport)'
        //   }
        // },
        // {
        //   extend: 'excel',
        //   className: 'btn btn-info',
        //   text: '<i class="fa fa-file-excel-o"></i> Excel',
        //   exportOptions: {
        //     columns: ':not(.notexport)'
        //   }
        // },
        // {
        //   extend: 'print',
        //   className: 'btn btn-warning',
        //   text: '<i class="fa fa-print"></i> Print',
        //   exportOptions: {
        //     columns: ':not(.notexport)'
        //   }
        // },
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
        "url" : "{{ url("fetch/mutasi_ant/hr") }}",
        "data" : data,
      },
      "columns": [
      { "data": "no" },
      { "data": "nik" },
      { "data": "nama" },
      { "data": "CareerTransition" },
      { "data": "CareerTransType" },
      { "data": "tanggal" },
      { "data": "ke_jabatan" }
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

    $('#scrap_detail tfoot tr').appendTo('#scrap_detail thead');
  }

  function refreshTable(){
    $('#scrap_detail').DataTable().ajax.reload();
  }

  function clearConfirmation(){
		location.reload(true);		
	}

  // var currClosureID = '';


  function scanScrap(){
    var number  = $("#slip_scrap_number").val();
    var data = {
      number : number
    }

    $.post('{{ url("scan/scrap_warehouse") }}', data,  function(result, status, xhr){
      if(result.status){
        $("#slip_scrap_number").val("");
        $("#slip_scrap_number").focus();
        $('#scrap_detail').DataTable().ajax.reload();
        audio_ok.play();
        openSuccessGritter('Success!', result.message);
      }else{
        openErrorGritter('Error!', result.message);
        audio_error.play();
        $("#slip_scrap_number").val("");
        $("#slip_scrap_number").focus();
      }
    });
  }
</script>

@stop