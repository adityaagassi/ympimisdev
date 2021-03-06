@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

	table{
		padding: 0px;
		color: black;
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
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	.nav-tabs-custom > ul.nav.nav-tabs {
		display: table;
		width: 100%;
		table-layout: fixed;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li {
		float: none;
		display: table-cell;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li > a {
		text-align: center;
	}
	.vendor-tab{
		width:100%;
	}
	.dataTables_filter {
		float: left !important;
	}

	.button-right{
		float: right; !important;
	}

	#loading, #error { display: none; }
	.disabled {
		pointer-events: none;
		cursor: default;
	}
</style>
@stop
@section('header')
<section class="content-header">
	<div class="row">
		<div class="col-xs-12 col-md-9 col-lg-9">
			<h3 style="margin-top: 0px;">{{ $title }}<span class="text-purple"> {{ $title_jp }}</span></h3>
		</div>
		<div class="col-xs-12 col-md-3 col-lg-3" >
			<div class="input-group date">
				<div class="input-group-addon bg-green">
					<i class="fa fa-calendar"></i>
				</div>
				<input style="text-align: center;" type="text" class="form-control datepicker" onchange="monthChange()" name="month" id="month" placeholder="Select Month" readonly>
			</div>
		</div>
	</div>
</section>
@stop
@section('content')
<section class="content" style="padding-top: 0;">

	@foreach(Auth::user()->role->permissions as $perm)
	@php
	$navs[] = $perm->navigation_code;
	@endphp
	@endforeach

	@if (session('error'))
	<input type="text" id="msg_error" value="{{ session('error') }}" hidden>
	@endif

	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="text-align: center; position: absolute; color: white; top: 45%; left: 40%;">
			<span style="font-size: 50px;">Please wait ... </span><br>
			<span style="font-size: 50px;"><i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<!-- <div class="col-xs-12">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
					{{-- <li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Progress Input</a></li> --}}
					<li class="vendor-tab active"><a href="#tab_2" data-toggle="tab" id="tab_header_2">Progress Input By Location</a></li>
					{{-- <li class="vendor-tab"><a href="#tab_4" data-toggle="tab" id="tab_header_4">Progress Audit</a></li> --}}
					<li class="vendor-tab"><a href="#tab_5" data-toggle="tab" id="tab_header_5">Progress Audit By Location</a></li>
					{{-- <li class="vendor-tab"><a href="#tab_6" data-toggle="tab" id="tab_header_6">Progress Input By Store</a></li> --}}
					<li class="vendor-tab"><a href="#tab_3" data-toggle="tab" id="tab_header_3">Progress Input By Sub Store</a></li>
				</ul>
		
				<div class="tab-content">
					{{-- <div class="tab-pane active" id="tab_1">
						<div id="container"></div>
					</div> --}}
					<div class="tab-pane active" id="tab_2">
						<div id="container0"></div>				
					</div>
					<div class="tab-pane" id="tab_3">
						<div id="container5"></div>				
					</div>
					{{-- <div class="tab-pane" id="tab_4">
						<div id="container3"></div>				
					</div> --}}
					<div class="tab-pane" id="tab_5">
						<div id="container4"></div>				
					</div>
					<div class="tab-pane" id="tab_6">
						<div id="container6"></div>				
					</div>
				</div>
			</div>
		</div> -->


		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-12 col-md-6 col-lg-6 col-md-offset-3 col-lg-offset-3" style="text-align: center;">
					<span style="font-size: 20px; color: purple;"><i class="fa fa-angle-double-down"></i> Klik Disini Untuk Melihat Hasil Input <i class="fa fa-angle-double-down"></i></span>
					<a id="monitoring" onclick="monitoring()" class="btn btn-default btn-block" style="font-size: 15px; border-color: purple;">Stocktaking Monitoring</a>
				</div>
			</div>
		</div>


		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-12 col-md-3 col-lg-3" style="text-align: center;">
					@if(in_array('S36', $navs))
					
					<span style="font-size: 20px; color: black;"><i class="fa fa-angle-double-down"></i> Master <i class="fa fa-angle-double-down"></i></span>

					<a href="javascript:void(0)" data-toggle="modal" data-target="#importBomModal" class="btn btn-default btn-block" style="border-color: black; font-size: 15px;">Upload Bom Output</a>
					<a href="javascript:void(0)" data-toggle="modal" data-target="#importMPDLModal" class="btn btn-default btn-block" style="border-color: black; font-size: 15px;">Upload Material Plant Data List</a>
					<a href="javascript:void(0)" data-toggle="modal" data-target="#importModal" class="btn btn-default btn-block" style="border-color: black; font-size: 15px;">Upload Storage Loc Stock</a>
					{{-- <a href="" class="btn btn-default btn-block" style="border-color: black; font-size: 15px;">Master Storage Location</a> --}}
					{{-- <a href="{{ url("index/stocktaking/calendar") }}" class="btn btn-default btn-block" style="border-color: black; font-size: 15px;">Stocktaking Calendar</a> --}}

					<a href="{{ url("index/stocktaking/stocktaking_list") }}" class="btn btn-default btn-block" style="border-color: black; font-size: 15px; color:white; background-color: #616161;">Master Stocktaking List</a>
					{{-- <a href="" class="btn btn-default btn-block" style="border-color: black; font-size: 15px; color:white; background-color: #616161;">Master Item Silver</a> --}}
					@endif
					{{-- <a href="{{ url("index/stocktaking/material_forecast") }}" class="btn btn-default btn-block" style="border-color: black; font-size: 15px; color:white; background-color: #616161;">Master Material Forecast</a> --}}


				</div>
				<div class="col-xs-12 col-md-3 col-lg-3" style="text-align: center;">
					<span style="font-size: 20px; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>

					<a id="manage_store" href="{{ url("index/stocktaking/summary_new") }}" class="btn btn-default btn-block" style="font-size: 15px; border-color: green;">Print Summary Of Counting</a>

					{{-- <a id="no_use" href="{{ secure_url("index/stocktaking/no_use_new") }}" class="btn btn-default btn-block" style="font-size: 15px; border-color: green; background-color: #ffce5c;">Input No Use</a> --}}

					<a id="input_pi" href="{{ secure_url('index/stocktaking/count_new') }}" class="btn btn-default btn-block" style="font-size: 15px; border-color: green; background-color: #ccff90;">Input Physical Inventory (PI)</a>

					<a id="audit1" href="{{ secure_url("index/stocktaking/audit_new/"."1") }}" class="btn btn-default btn-block" style="font-size: 15px; border-color: green; background-color: #ccff90;">Audit Internal</a>				

					@if(in_array('S36', $navs))
					<a id="revise" href="{{ secure_url("index/stocktaking/revise_new") }}" class="btn btn-default btn-block" style="font-size: 15px; border-color: green;">Revise Physical Inventory (PI)</a>
					@endif

					<a id="check_new" href="{{ url("index/stocktaking/check_input_new") }}" class="btn btn-default btn-block" style="font-size: 15px; border-color: green;">Check Input</a>

				</div>
				<div class="col-xs-12 col-md-3 col-lg-3" style="text-align: center;">
					<span style="font-size: 20px; color: purple;"><i class="fa fa-angle-double-down"></i> Result <i class="fa fa-angle-double-down"></i></span>
					<!-- <a id="monitoring" onclick="monitoring()" class="btn btn-default btn-block" style="font-size: 15px; border-color: purple;">Stocktaking Monitoring</a> -->
					@if(in_array('S36', $navs))
					<a id="breakdown" data-toggle="modal" data-target="#modalBreakdown" class="btn btn-default btn-block" style="font-size: 15px; border-color: purple;">Breakdown Physical Inventory (PI)</a>
					@endif
					<a id="unmatch" onclick="unmatch()" class="btn btn-default btn-block" style="font-size: 15px; border-color: purple; background-color: #e040fb;">Unmatch Check</a>
					{{-- <a id="" href="{{ url("index/stocktaking/inquiry") }}" class="btn btn-default btn-block" style="font-size: 15px; border-color: purple;">Inquiry</a> --}}
					{{-- <a id="" href="{{ url("index/stocktaking/variance_report") }}" class="btn btn-default btn-block" style="font-size: 15px; border-color: purple; background-color: #e040fb;">Variance Report</a> --}}


					<form method="GET" action="{{ url("export/stocktaking/inquiry_new") }}">
						<input type="text" name="month_inquiry" id="month_inquiry" placeholder="Select Month" hidden>
						<button id="inquiry" type="submit" class="btn btn-default btn-block" style="margin-top: 5px; font-size: 15px; border-color: purple;">Inquiry</button>
					</form>
					<form method="GET" action="{{ url("export/stocktaking/variance") }}">
						<input type="text" name="month_variance" id="month_variance" placeholder="Select Month" hidden>
						<button id="variance" type="submit" class="btn btn-default btn-block" style="margin-top: 5px; font-size: 15px; border-color: purple; background-color: #e040fb;">Variance Report</button>
					</form>

					{{-- <a id="" href="{{ url("") }}" class="btn btn-default btn-block" style="font-size: 15px; border-color: purple;">Official Variance Report</a> --}}
				</div>
				<div class="col-xs-12 col-md-3 col-lg-3" style="text-align: center;">
					@if(in_array('S36', $navs))
					<span style="font-size: 20px; color: red;"><i class="fa fa-angle-double-down"></i> Final <i class="fa fa-angle-double-down"></i></span>
					<a id="upload_sap" onclick="uploadSap()" class="btn btn-default btn-block" style="font-size: 15px; border-color: red;">Upload Textfile to SAP</a>
					{{-- <a id="export_log" onclick="exportLog()" class="btn btn-default btn-block" style="font-size: 15px; border-color: red; background-color: #ff5252;">End Stocktaking</a> --}}
					@endif

				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalVariance">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding" style="min-height: 100px">
						<button type="button" class="btn btn-danger button-right" data-dismiss="modal">Close&nbsp;&nbsp;<i class="fa fa-close"></i></button>

						<table class="table table-hover table-bordered table-striped" id="tableVariance">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>Plnt</th>
									<th>Group</th>
									<th>Location</th>
									<th>Percentage</th>
								</tr>
							</thead>
							<tbody id="bodyVariance">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalAudit">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding" style="min-height: 100px">
						<button type="button" class="btn btn-danger button-right" data-dismiss="modal">Close&nbsp;&nbsp;<i class="fa fa-close"></i></button>

						<table class="table table-hover table-bordered table-striped" id="tableAudit">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>Group</th>
									<th>Location</th>
									<th>Store</th>
								</tr>
							</thead>
							<tbody id="bodyAudit">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalInput">
		<div class="modal-dialog modal-lg" style="width: 90%;">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding" style="min-height: 100px">
						<button type="button" class="btn btn-danger button-right" data-dismiss="modal">Close&nbsp;&nbsp;<i class="fa fa-close"></i></button>

						<table class="table table-hover table-bordered table-striped" id="tableInput">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>Group</th>
									<th>Location</th>
									<th>Store</th>
									<th>Category</th>
									<th>Material</th>
									<th>Description</th>
									<th>Qty</th>
									<th>Audit 1</th>
									{{-- <th>Audit 2</th> --}}
									<th>PI</th>
								</tr>
							</thead>
							<tbody id="bodyInput">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalInputNew">
		<div class="modal-dialog modal-lg" style="width: 90%;">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding" style="min-height: 100px">
						<button type="button" class="btn btn-danger button-right" data-dismiss="modal">Close&nbsp;&nbsp;<i class="fa fa-close"></i></button>

						<table class="table table-hover table-bordered table-striped" id="tableInputNew">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>Group</th>
									<th>Location</th>
									<th>Store</th>
									<th>Sub Store</th>
									<th>Category</th>
									<th>Material</th>
									<th>Description</th>
									<th>Qty</th>
									<th>Audit 1</th>
									{{-- <th>Audit 2</th> --}}
									<th>PI</th>
								</tr>
							</thead>
							<tbody id="bodyInputNew">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalVariance">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding" style="min-height: 100px">
						<button type="button" class="btn btn-danger button-right" data-dismiss="modal">Close&nbsp;&nbsp;<i class="fa fa-close"></i></button>

						<table class="table table-hover table-bordered table-striped" id="tableVariance">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>Plnt</th>
									<th>Group</th>
									<th>Location</th>
									<th>Percentage</th>
								</tr>
							</thead>
							<tbody id="bodyVariance">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalVariance">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding" style="min-height: 100px">
						<button type="button" class="btn btn-danger button-right" data-dismiss="modal">Close&nbsp;&nbsp;<i class="fa fa-close"></i></button>

						<table class="table table-hover table-bordered table-striped" id="tableVariance">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>Plnt</th>
									<th>Group</th>
									<th>Location</th>
									<th>Percentage</th>
								</tr>
							</thead>
							<tbody id="bodyVariance">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalMonth">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div class="form-group">
							<label for="exampleInputEmail1">Month</label>
							<div class="input-group date">
								<div class="input-group-addon bg-green">
									<i class="fa fa-calendar"></i>
								</div>
								<input style="text-align: center;" type="text" class="form-control datepicker" onchange="monthChange()" name="month" id="month" placeholder="Select Month">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalBreakdown">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a;">
						<h2 style="text-align: center; margin: 2%; font-weight: bold;">Breakdown PI</h2>
					</div>

					<div class="col-xs-12" style="margin-top: 3%;">
						<div class="form-group">
							<label>Select Group</label><br>

							<label><input type="checkbox" class="minimal" id="ASSEMBLY">&nbsp;&nbsp;Assembly</label><br>
							<label><input type="checkbox" class="minimal" id="ST">&nbsp;&nbsp;Surface Treatment</label><br>
							<label><input type="checkbox" class="minimal" id="WELDING">&nbsp;&nbsp;Welding</label><br>
							<label><input type="checkbox" class="minimal" id="PP">&nbsp;&nbsp;Parts Process</label><br>
							<label><input type="checkbox" class="minimal" id="EI">&nbsp;&nbsp;Educational Instrument</label><br>
							<label><input type="checkbox" class="minimal" id="WAREHOUSE">&nbsp;&nbsp;Warehouse</label><br>
							<label><input type="checkbox" class="minimal" id="FG">&nbsp;&nbsp;Finished Goods</label><br>
							<label><input type="checkbox" class="minimal" id="SUBCONT">&nbsp;&nbsp;Subcont</label>
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="countPI()"><i class="fa fa-play"></i>Start</button>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id ="importForm" method="post" action="{{ url('import/material/storage') }}" enctype="multipart/form-data">
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Import Storage Location</h4>
					Format: [Material Number][Material Description][SLoc][Unrestricted][Download Date][Download Time]<br>
					Sample: <a href="{{ url('download/manual/import_storage_location_stock.txt') }}">import_storage_location_stock.txt</a> Code: #Truncate
				</div>
				<div class="modal-body">
					Select Date:
					<input type="text" class="form-control" id="date_stock" name="date_stock" style="width:25%;"><br>
					<input type="file" name="storage_location_stock" id="storage_location_stock" accept="text/plain">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button id="modalImportButton" type="submit" class="btn btn-success" onclick="loadingPage()">Import</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="importBomModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id ="importFormBom" method="post" enctype="multipart/form-data">
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Import Bom Output</h4>
					Format: [Material Parent][Material Child][Usage][Divider][Uom]<br>
					Sample: <a href="{{ url('download/manual/import_bom_output.xlsx') }}">import_bom_output.xlsx</a> Code: #Truncate
				</div>
				<div class="modal-body">
					<input type="file" name="bom" id="bom" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button id="modalImportButtonBom" type="submit" class="btn btn-success">Import</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="importMPDLModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id ="importFormMpdl" method="post" action="{{ url('import/material/mpdl') }}" enctype="multipart/form-data">
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Import MPDL</h4>
					Format: [Material Number][Material Description][Bun[Spt]][SLoc][Valcl][Std Price]<br>
					Sample: <a href="{{ url('download/manual/import_mpdl.xlsx') }}">import_mpdl.xlsx</a> Code: #Truncate
				</div>
				<div class="modal-body">
					<input type="file" name="mpdl" id="mpdl" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button id="modalImportButtonMpdl" type="submit" class="btn btn-success" onclick="loadingPage()">Import</button>
				</div>
			</form>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/icheck.min.js")}}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});


	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('#date_stock').datepicker({
			autoclose: true,
			todayHighlight: true
		});

		$('input[type="checkbox"].minimal').iCheck({
			checkboxClass: 'icheckbox_minimal-blue'
		});

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m') ?>
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
			endDate: '<?php echo $tgl_max ?>'	
		});

		if($('#month').val() == ''){

			var monthArr = ['01','02','03','04','05','06','07','08','09','10','11','12',] 
			
			var now = new Date();
			var month = monthArr[now.getMonth()];
			var year = now.getFullYear();

			$('#month').val(year +'-'+ month);
			// $('#month').val('2020-11');
		}

		monthChange();

	});

	function loadingPage(){
		$("#loading").show();
	}

	$('#importFormBom').on('submit', function(event){
		event.preventDefault();
		var formdata = new FormData(this);

		$("#loading").show();

		$.ajax({
			url:"{{ url('import/material/bom') }}",
			method:'post',
			data:formdata,
			dataType:"json",
			processData: false,
			contentType: false,
			cache: false,
			success:function(result, status, xhr){
				if(result.status){
					$('#bom').val('');
					$('#importBomModal').modal('hide');
					openSuccessGritter('Success', result.message);
					$("#loading").hide();
				}else{
					openErrorGritter('Error!', result.message);
					$("#loading").hide();
				}

			},
			error: function(result, status, xhr){
				$("#loading").hide();				
				openErrorGritter('Error!', 'Fatal Error');
			}
		});
	});

	$('#importFormMpdl').on('submit', function(event){
		event.preventDefault();
		var formdata = new FormData(this);

		$("#loading").show();

		$.ajax({
			url:"{{ url('import/material/mpdl') }}",
			method:'post',
			data:formdata,
			dataType:"json",
			processData: false,
			contentType: false,
			cache: false,
			success:function(result, status, xhr){
				if(result.status){
					$('#mpdl').val('');
					$('#importMPDLModal').modal('hide');
					openSuccessGritter('Success', result.message);
					$("#loading").hide();
				}else{
					openErrorGritter('Error!', result.message);
					$("#loading").hide();
				}

			},
			error: function(result, status, xhr){
				$("#loading").hide();				
				openErrorGritter('Error!', 'Fatal Error');
			}
		});
	});

	function uploadSap() {
		$("#loading").show();

		var month = $('#month').val();

		var data = {
			month : month
		}

		$.get('{{ url("export/stocktaking/upload_sap") }}', data, function(result, status, xhr){
			if(result.status){
				$("#loading").hide();
				openSuccessGritter('Success', 'Export Log Success');
			}else{
				$("#loading").hide();
				openErrorGritter('Error', 'Export Log Failed');
			}

		});
	}

	function exportLog() {
		$("#loading").show();

		var month = $('#month').val();

		var data = {
			month : month
		}

		$.get('{{ url("export/stocktaking/log") }}', data, function(result, status, xhr){
			if(result.status){
				$("#loading").hide();
				monthChange();
				openSuccessGritter('Success', 'Export Log Success');
			}else{
				$("#loading").hide();
				openErrorGritter('Error', 'Export Log Failed');
			}

		});
	}

	function unmatch(){
		var month = $('#month').val();
		window.open('{{ url("index/stocktaking/unmatch/") }}'+'/'+month, '_blank');
	}

	function monitoring(){
		var month = $('#month').val();
		window.open('{{ url("index/stocktaking/monitoring") }}', '_Self');
	}

	function monthChange(){
		var month = $('#month').val();
		// var month = '2020-11';

		$('#month_inquiry').val(month);
		$('#month_variance').val(month);
		$('#month_official_variance').val(month);

		var data = {
			month : month
		}

		// $('#month_text').text(bulanText(month));
		$('#modalMonth').modal('hide');

		$.get('{{ url("fetch/stocktaking/check_month") }}', data, function(result, status, xhr){
			if(result.status){
				$('#inquiry').removeClass('disabled');
				$('#variance').removeClass('disabled');

				if(result.data.status == 'finished'){
					$('#manage_store').addClass('disabled');
					$('#summary_of_counting').addClass('disabled');
					$('#no_use').addClass('disabled');
					$('#input_pi').addClass('disabled');
					$('#audit1').addClass('disabled');
					$('#check_new').addClass('disabled');
					$('#audit2').addClass('disabled');
					$('#breakdown').addClass('disabled');
					$('#unmatch').addClass('disabled');
					$('#revise').addClass('disabled');
					$('#upload_sap').addClass('disabled');
					$('#export_log').addClass('disabled');
					$('#check_new').addClass('disabled');
				}else{
					$('#manage_store').removeClass('disabled');
					$('#summary_of_counting').removeClass('disabled');
					$('#no_use').removeClass('disabled');
					$('#input_pi').removeClass('disabled');
					$('#audit1').removeClass('disabled');
					$('#check_new').removeClass('disabled');
					$('#audit2').removeClass('disabled');
					$('#breakdown').removeClass('disabled');
					$('#unmatch').removeClass('disabled');
					$('#revise').removeClass('disabled');
					$('#upload_sap').removeClass('disabled');
					$('#export_log').removeClass('disabled');
					$('#check_new').removeClass('disabled');

				}
				// $('#month_text').text(bulanText(month));
				$('#modalMonth').modal('hide');

			}else{
				// $('#month_text').text(bulanText(month));
				$('#modalMonth').modal('hide');
				openErrorGritter('Error', result.message);

				$('#manage_store').addClass('disabled');
				$('#summary_of_counting').addClass('disabled');
				$('#no_use').addClass('disabled');
				$('#input_pi').addClass('disabled');
				$('#audit1').addClass('disabled');
				$('#check_new').addClass('disabled');
				$('#audit2').addClass('disabled');
				$('#breakdown').addClass('disabled');
				$('#unmatch').addClass('disabled');
				$('#revise').addClass('disabled');
				$('#upload_sap').addClass('disabled');
				$('#export_log').addClass('disabled');
				$('#inquiry').addClass('disabled');
				$('#variance').addClass('disabled');
			}

		});
	}

	function bulanText(param){

		var index = param.split('-');
		var bulan = parseInt(index[1]);
		var tahun = parseInt(index[0]);
		var bulanText = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

		return bulanText[bulan-1]+" "+tahun;
	}

	$("#modalBreakdown").on("hidden.bs.modal", function () {
		$('#ASSEMBLY').iCheck('uncheck');
		$('#ST').iCheck('uncheck');
		$('#WELDING').iCheck('uncheck');
		$('#PP').iCheck('uncheck');
		$('#EI').iCheck('uncheck');
		$('#WAREHOUSE').iCheck('uncheck');
		$('#FG').iCheck('uncheck');
	});


	function countPI() {
		$("#loading").show();
		var group = [];

		if($('#ASSEMBLY').is(":checked")){
			group.push('ASSEMBLY');
		}
		if($('#ST').is(":checked")){
			group.push('ST');
		}
		if($('#WELDING').is(":checked")){
			group.push('WELDING');
		}
		if($('#PP').is(":checked")){
			group.push('PP');
		}
		if($('#EI').is(":checked")){
			group.push('EI');
		}
		if($('#WAREHOUSE').is(":checked")){
			group.push('WAREHOUSE');
		}
		if($('#FG').is(":checked")){
			group.push('FINISHED GOODS');
		}
		if($('#SUBCONT').is(":checked")){
			group.push('SUBCONT');
		}


		if(group.length > 0){
			var data = {
				group : group
			}

			$.post('{{ url("index/stocktaking/count_pi_new") }}', data, function(result, status, xhr){
				if(result.status){
					$("#loading").hide();
					$("#modalBreakdown").modal('hide');

					$('#ASSEMBLY').iCheck('uncheck');
					$('#ST').iCheck('uncheck');
					$('#WELDING').iCheck('uncheck');
					$('#PP').iCheck('uncheck');
					$('#EI').iCheck('uncheck');
					$('#WAREHOUSE').iCheck('uncheck');
					$('#FG').iCheck('uncheck');
					$('#SUBCONT').iCheck('uncheck');

					// variance();
					openSuccessGritter('Success', result.message);
				}else{
					$("#loading").hide();
					openErrorGritter('Error', result.message);
				}

			});
		}else{
			$("#loading").hide();
			openErrorGritter('Error', 'Select Group');
		}		
	}

	function exportInquiry() {
		$.get('{{ url("export/stocktaking/inquiry") }}', function(result, status, xhr){});
	}

	function exportVariance() {
		$.get('{{ url("export/stocktaking/variance") }}', function(result, status, xhr){});
	}

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}

	function getActualFullDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '4000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '4000'
		});
	}

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	Highcharts.theme = {
		colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
		'#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
		chart: {
			backgroundColor: {
				linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
				stops: [
				[0, '#2a2a2b'],
				[1, '#3e3e40']
				]
			},
			style: {
				fontFamily: 'sans-serif'
			},
			plotBorderColor: '#606063'
		},
		title: {
			style: {
				color: '#E0E0E3',
				textTransform: 'uppercase',
				fontSize: '20px'
			}
		},
		subtitle: {
			style: {
				color: '#E0E0E3',
				textTransform: 'uppercase'
			}
		},
		xAxis: {
			gridLineColor: '#707073',
			labels: {
				style: {
					color: '#E0E0E3'
				}
			},
			lineColor: '#707073',
			minorGridLineColor: '#505053',
			tickColor: '#707073',
			title: {
				style: {
					color: '#A0A0A3'

				}
			}
		},
		yAxis: {
			gridLineColor: '#707073',
			labels: {
				style: {
					color: '#E0E0E3'
				}
			},
			lineColor: '#707073',
			minorGridLineColor: '#505053',
			tickColor: '#707073',
			tickWidth: 1,
			title: {
				style: {
					color: '#A0A0A3'
				}
			}
		},
		tooltip: {
			backgroundColor: 'rgba(0, 0, 0, 0.85)',
			style: {
				color: '#F0F0F0'
			}
		},
		plotOptions: {
			series: {
				dataLabels: {
					color: 'white'
				},
				marker: {
					lineColor: '#333'
				}
			},
			boxplot: {
				fillColor: '#505053'
			},
			candlestick: {
				lineColor: 'white'
			},
			errorbar: {
				color: 'white'
			}
		},
		legend: {
			itemStyle: {
				color: '#E0E0E3'
			},
			itemHoverStyle: {
				color: '#FFF'
			},
			itemHiddenStyle: {
				color: '#606063'
			}
		},
		credits: {
			style: {
				color: '#666'
			}
		},
		labels: {
			style: {
				color: '#707073'
			}
		},

		drilldown: {
			activeAxisLabelStyle: {
				color: '#F0F0F3'
			},
			activeDataLabelStyle: {
				color: '#F0F0F3'
			}
		},

		navigation: {
			buttonOptions: {
				symbolStroke: '#DDDDDD',
				theme: {
					fill: '#505053'
				}
			}
		},

		rangeSelector: {
			buttonTheme: {
				fill: '#505053',
				stroke: '#000000',
				style: {
					color: '#CCC'
				},
				states: {
					hover: {
						fill: '#707073',
						stroke: '#000000',
						style: {
							color: 'white'
						}
					},
					select: {
						fill: '#000003',
						stroke: '#000000',
						style: {
							color: 'white'
						}
					}
				}
			},
			inputBoxBorderColor: '#505053',
			inputStyle: {
				backgroundColor: '#333',
				color: 'silver'
			},
			labelStyle: {
				color: 'silver'
			}
		},

		navigator: {
			handles: {
				backgroundColor: '#666',
				borderColor: '#AAA'
			},
			outlineColor: '#CCC',
			maskFill: 'rgba(255,255,255,0.1)',
			series: {
				color: '#7798BF',
				lineColor: '#A6C7ED'
			},
			xAxis: {
				gridLineColor: '#505053'
			}
		},

		scrollbar: {
			barBackgroundColor: '#808083',
			barBorderColor: '#808083',
			buttonArrowColor: '#CCC',
			buttonBackgroundColor: '#606063',
			buttonBorderColor: '#606063',
			rifleColor: '#FFF',
			trackBackgroundColor: '#404043',
			trackBorderColor: '#404043'
		},

		legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
		background2: '#505053',
		dataLabelsColor: '#B0B0B3',
		textColor: '#C0C0C0',
		contrastTextColor: '#F0F0F3',
		maskColor: 'rgba(255,255,255,0.3)'
	};
	Highcharts.setOptions(Highcharts.theme);


</script>
@endsection