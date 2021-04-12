@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet">
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

	.buttonclass {
	  top: 0;
	  left: 0;
	  transition: all 0.15s linear 0s;
	  position: relative;
	  display: inline-block;
	  padding: 15px 25px;
	  background-color: #ffe800;
	  text-transform: uppercase;
	  color: #404040;
	  font-family: arial;
	  letter-spacing: 1px;
	  box-shadow: -6px 6px 0 #404040;
	  text-decoration: none;
	  cursor: pointer;
	}
	.buttonclass:hover {
	  top: 3px;
	  left: -3px;
	  box-shadow: -3px 3px 0 #404040;
	  color: white
	}
	.buttonclass:hover::after {
	  top: 1px;
	  left: -2px;
	  width: 4px;
	  height: 4px;
	}
	.buttonclass:hover::before {
	  bottom: -2px;
	  right: 1px;
	  width: 4px;
	  height: 4px;
	}
	.buttonclass::after {
	  transition: all 0.15s linear 0s;
	  content: "";
	  position: absolute;
	  top: 2px;
	  left: -4px;
	  width: 8px;
	  height: 8px;
	  background-color: #404040;
	  transform: rotate(45deg);
	  z-index: -1;
	}
	.buttonclass::before {
	  transition: all 0.15s linear 0s !important;
	  content: "";
	  position: absolute;
	  bottom: -4px;
	  right: 2px;
	  width: 8px;
	  height: 8px;
	  background-color: #404040;
	  transform: rotate(45deg) !important;
	  z-index: -1 !important;
	}

	a.buttonclass {
	  position: relative;
	}

	a:active.buttonclass {
	  top: 6px;
	  left: -6px;
	  box-shadow: none;
	}
	a:active.buttonclass:before {
	  bottom: 1px;
	  right: 1px;
	}
	a:active.buttonclass:after {
	  top: 1px;
	  left: 1px;
	}

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{$title}} <small><span class="text-purple">{{$title_jp}}</span></small>
	</h1>

</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: white; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please wait . . . <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	@if (session('status'))
		<div class="alert alert-success alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
			{{ session('status') }}
		</div>   
	@endif
	@if (session('error'))
		<div class="alert alert-warning alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4> Warning!</h4>
			{{ session('error') }}
		</div>   
	@endif
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
					<form method="GET" action="{{ url('excel/qa/report/incoming') }}">
					<h4>Filter</h4>
					<div class="row">
						<div class="col-md-4 col-md-offset-2">
							<span style="font-weight: bold;">Date From</span>
							<div class="form-group">
								<div class="input-group date">
									<div class="input-group-addon bg-white">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From" autocomplete="off">
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<span style="font-weight: bold;">Date To</span>
							<div class="form-group">
								<div class="input-group date">
									<div class="input-group-addon bg-white">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker" id="date_to"name="date_to" placeholder="Select Date To" autocomplete="off">
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4 col-md-offset-2">
							<span style="font-weight: bold;">Location</span>
							<div class="form-group">
								<select class="form-control select2" multiple="multiple" id="locationSelect" data-placeholder="Select Location" onchange="changeLocation()" style="width: 100%;color: black !important"> 
									@foreach($location as $location)
									<?php $locs = explode("_", $location) ?>
									<option value="{{$locs[0]}}">{{$locs[1]}}</option>
									@endforeach
								</select>
								<input type="text" name="location" id="location" style="color: black !important" hidden>
							</div>
						</div>
						<div class="col-md-4">
							<span style="font-weight: bold;">Inspection Level</span>
							<div class="form-group">
								<select class="form-control select2" multiple="multiple" id='inspectionLevelSelect' onchange="changeInspectionLevel()" data-placeholder="Select Inspection Level" style="width: 100%;color: black !important">
									@foreach($inspection_levels as $inspection_level)
									<option value="{{$inspection_level->inspection_level}}">{{$inspection_level->inspection_level}}</option>
									@endforeach
								</select>
								<input type="text" name="inspection_level" id="inspection_level" style="color: black !important" hidden>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4 col-md-offset-2">
							<span style="font-weight: bold;">Vendor</span>
							<div class="form-group">
								<select class="form-control select2" multiple="multiple" id="vendorSelect" data-placeholder="Select Vendors" onchange="changeVendor()" style="width: 100%;color: black !important"> 
									@foreach($vendors as $vendor)
									<option value="{{$vendor->vendor}}">{{$vendor->vendor}}</option>
									@endforeach
								</select>
								<input type="text" name="vendor" id="vendor" style="color: black !important" hidden>
							</div>
						</div>
						<div class="col-md-4">
							<span style="font-weight: bold;">Material</span>
							<div class="form-group">
								<select class="form-control select2" multiple="multiple" id='materialSelect' onchange="changeMaterial()" data-placeholder="Select Material" style="width: 100%;color: black !important">
									@foreach($materials as $material)
									<option value="{{$material->material_number}}">{{$material->material_number}} - {{$material->material_description}}</option>
									@endforeach
								</select>
								<input type="text" name="material" id="material" style="color: black !important" hidden>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-8 col-md-offset-4">
							<div class="col-md-12">
								<div class="form-group pull-left">
									<a href="{{ url('index/qa') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/qa/report/incoming') }}" class="btn btn-danger">Clear</a>
									<a class="btn btn-primary col-sm-14" href="javascript:void(0)" onclick="fillList()">Search</a>
									<button class="btn btn-success"><i class="fa fa-download"></i> Export Excel</button>
								</div>
							</div>
						</div>
					</div>
					</form>
					<div class="col-xs-12">
						<div class="row" id="divTable">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection


@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('.select2').select2({
			language : {
				noResults : function(params) {
					return "There is no date";
				}
			}
		});

		fillList();

		$('body').toggleClass("sidebar-collapse");
	});
	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	function changeVendor() {
		$("#vendor").val($("#vendorSelect").val());
	}

	function changeMaterial() {
		$("#material").val($("#materialSelect").val());
	}

	function changeLocation() {
		$("#location").val($("#locationSelect").val());
	}

	function changeInspectionLevel() {
		$("#inspection_level").val($("#inspectionLevelSelect").val());
	}

	function initiateTable() {
		$('#divTable').html("");
		var tableData = "";
		tableData += "<table id='example1' class='table table-bordered table-striped table-hover'>";
		tableData += '<thead style="background-color: rgba(126,86,134,.7);">';
		tableData += '<tr>';
		tableData += '<th rowspan="2">Loc</th>';
		tableData += '<th rowspan="2">Date</th>';
		tableData += '<th rowspan="2">Inspector</th>';
		tableData += '<th rowspan="2">Vendor</th>';
		tableData += '<th rowspan="2">Invoice</th>';
		tableData += '<th rowspan="2">Inspection Level</th>';
		tableData += '<th rowspan="2">Lot Number</th>';
		tableData += '<th rowspan="2">Material</th>';
		tableData += '<th rowspan="2">Desc</th>';
		tableData += '<th rowspan="2">Qty Rec</th>';
		tableData += '<th rowspan="2">Qty Check</th>';
		tableData += '<th rowspan="2">Defect</th>';
		tableData += '<th colspan="3">Jumlah NG</th>';
		tableData += '<th rowspan="2">Note</th>';
		tableData += '<th rowspan="2">NG Ratio</th>';
		tableData += '<th rowspan="2">Lot Status</th>';
		tableData += '</tr>';
		tableData += '<tr>';
		tableData += '<th>Repair</th>';
		tableData += '<th>Return</th>';
		tableData += '<th>Scrap</th>';
		tableData += '</tr>';
		tableData += '</thead>';
		tableData += '<tbody id="example1Body">';
		tableData += "</tbody>";
		// tableData += "<tfoot>";
		// tableData += "<tr>";
		// tableData += "<th></th>";
		// tableData += "<th></th>";
		// tableData += "<th></th>";
		// tableData += "<th></th>";
		// tableData += "<th></th>";
		// tableData += "<th></th>";
		// tableData += "<th></th>";
		// tableData += "<th></th>";
		// tableData += "<th></th>";
		// tableData += "<th></th>";
		// tableData += "<th></th>";
		// tableData += "<th></th>";
		// tableData += "<th></th>";
		// tableData += "<th></th>";
		// tableData += "<th></th>";
		// tableData += "<th></th>";
		// tableData += "<th></th>";
		// tableData += "</tr>";
		tableData += "</tfoot>";
		tableData += "</table>";
		$('#divTable').append(tableData);
	}

	function fillList(){
	$('#loading').show();
	var date_from = $('#date_from').val();
	var date_to = $('#date_to').val();
	var vendor = $('#vendor').val();
	var material = $('#material').val();
	var location = $('#location').val();
	var inspection_level = $('#inspection_level').val();

	var data = {
		date_from:date_from,
		date_to:date_to,
		vendor:vendor,
		material:material,
		location:location,
		inspection_level:inspection_level,
	}
	$.get('{{ url("fetch/qa/report/incoming") }}',data, function(result, status, xhr){
			if(result.status){

				initiateTable();
				
				var tableData = "";
				var index = 0;
				
				$.each(result.datas, function(key, value) {
					if (value.location == 'wi1') {
			  			var loc = 'Woodwind Instrument (WI) 1';
			  		}else if (value.location == 'wi2') {
			  			var loc = 'Woodwind Instrument (WI) 2';
			  		}else if(value.location == 'ei'){
			  			var loc = 'Educational Instrument (EI)';
			  		}else if(value.location == 'sx'){
			  			var loc = 'Saxophone Body';
			  		}else if (value.location == 'cs'){
			  			var loc = 'Case';
			  		}else if(value.location == 'ps'){
			  			var loc = 'Pipe Silver';
			  		}
			  		var jumlah = 0;

			  		if (value.ng_name != null) {
						var ng_name = value.ng_name.split('_');
						var ng_qty = value.ng_qty.split('_');
						var status_ng = value.status_ng.split('_');
						if (value.note_ng != null) {
							var note_ng = value.note_ng.split('_');
						}else{
							var note_ng = "";
						}
						jumlah = ng_name.length;
					}else{
						jumlah = 1;
					}

					
					tableData += '<tr>';
					tableData += '<td rowspan="'+jumlah+'">'+ loc +'</td>';
					tableData += '<td rowspan="'+jumlah+'">'+ value.created +'</td>';
					tableData += '<td rowspan="'+jumlah+'">'+ value.employee_id +'<br>'+ value.name +'</td>';
					tableData += '<td rowspan="'+jumlah+'">'+ value.vendor +'</td>';
					tableData += '<td rowspan="'+jumlah+'">'+ value.invoice +'</td>';
					tableData += '<td rowspan="'+jumlah+'">'+ value.inspection_level +'</td>';
					tableData += '<td rowspan="'+jumlah+'">'+ value.lot_number +'</td>';
					tableData += '<td rowspan="'+jumlah+'">'+ value.material_number +'</td>';
					tableData += '<td rowspan="'+jumlah+'">'+ value.material_description +'</td>';
					tableData += '<td rowspan="'+jumlah+'">'+ value.qty_rec +'</td>';
					tableData += '<td rowspan="'+jumlah+'">'+ value.qty_check +'</td>';
					if (value.ng_name != null) {
						tableData += '<td>';
						tableData += '<span class="label label-danger">'+ng_name[0]+'</span><br>';
						tableData += '</td>';
						if (status_ng[0] == 'Repair') {
							tableData += '<td>';
							tableData += '<span class="label label-danger">'+ng_qty[0]+'</span><br>';
							tableData += '</td>';
							tableData += '<td>';
							tableData += '</td>';
							tableData += '<td>';
							tableData += '</td>';
						}else if (status_ng[0] == 'Return') {
							tableData += '<td>';
							tableData += '</td>';
							tableData += '<td>';
							tableData += '<span class="label label-danger">'+ng_qty[0]+'</span><br>';
							tableData += '</td>';
							tableData += '<td>';
							tableData += '</td>';
						}else if (status_ng[0] == 'Scrap') {
							tableData += '<td>';
							tableData += '</td>';
							tableData += '<td>';
							tableData += '</td>';
							tableData += '<td>';
							tableData += '<span class="label label-danger">'+ng_qty[0]+'</span><br>';
							tableData += '</td>';
						}
						if (note_ng.length > 0) {
							tableData += '<td>';
							tableData += '<span class="label label-danger">'+note_ng[0]+'</span><br>';
							tableData += '</td>';
						}else{
							tableData += '<td>';
							tableData += '</td>';
						}
					}else{
						tableData += '<td>';
						tableData += '</td>';
						tableData += '<td>';
						tableData += '</td>';
						tableData += '<td>';
						tableData += '</td>';
						tableData += '<td>';
						tableData += '</td>';
						tableData += '<td>';
						tableData += '</td>';
					}
					tableData += '<td style="vertical-align:middle" rowspan="'+jumlah+'">'+ value.ng_ratio.toFixed(2) +'</td>';
					tableData += '<td style="vertical-align:middle" rowspan="'+jumlah+'">'+ value.status_lot +'</td>';
					tableData += '</tr>';
					if (value.ng_name != null) {
						for (var i = 1 ;i < ng_name.length; i++) {
							tableData += '<tr>';
							tableData += '<td>';
							tableData += '<span class="label label-danger">'+ng_name[i]+'</span><br>';
							tableData += '</td>';
							if (status_ng[i] == 'Repair') {
								tableData += '<td>';
								tableData += '<span class="label label-danger">'+ng_qty[i]+'</span><br>';
								tableData += '</td>';
								tableData += '<td>';
								tableData += '</td>';
								tableData += '<td>';
								tableData += '</td>';
							}else if (status_ng[i] == 'Return') {
								tableData += '<td>';
								tableData += '</td>';
								tableData += '<td>';
								tableData += '<span class="label label-danger">'+ng_qty[i]+'</span><br>';
								tableData += '</td>';
								tableData += '<td>';
								tableData += '</td>';
							}else if (status_ng[i] == 'Scrap') {
								tableData += '<td>';
								tableData += '</td>';
								tableData += '<td>';
								tableData += '</td>';
								tableData += '<td>';
								tableData += '<span class="label label-danger">'+ng_qty[i]+'</span><br>';
								tableData += '</td>';
							}
							if (note_ng.length > 0) {
								tableData += '<td>';
								tableData += '<span class="label label-danger">'+note_ng[i]+'</span><br>';
								tableData += '</td>';
							}else{
								tableData += '<td>';
								tableData += '</td>';
							}
							tableData += '</tr>';
						}
					}

					index++;
				});
				$('#example1Body').append(tableData);
				$('#loading').hide();

			}
			else{
				alert('Attempt to retrieve data failed');
				$('#loading').hide();
			}
		});
	}

	// function exportExcel() {
	// 	var date_from = $('#date_from').val();
	// 	var date_to = $('#date_to').val();
	// 	var vendor = $('#vendor').val();
	// 	var material = $('#material').val();
	// 	var location = $('#location').val();
	// 	var inspection_level = $('#inspection_level').val();

	// 	// var data = {
	// 	// 	date_from:date_from,
	// 	// 	date_to:date_to,
	// 	// 	vendor:vendor,
	// 	// 	material:material,
	// 	// 	location:location,
	// 	// 	inspection_level:inspection_level,
	// 	// }
	// 	if (date_from != "") {
	// 		date_from = $('#date_from').val();
	// 	}else{
	// 		date_from = "kosong";
	// 	}

	// 	if (date_to != "") {
	// 		date_to = $('#date_to').val();
	// 	}else{
	// 		date_to = "kosong";
	// 	}

	// 	if (vendor != "") {
	// 		vendor = $('#vendor').val();
	// 	}else{
	// 		vendor = "kosong";
	// 	}

	// 	if (material != "") {
	// 		material = $('#material').val();
	// 	}else{
	// 		material = "kosong";
	// 	}

	// 	if (location != "") {
	// 		location = $('#location').val();
	// 	}else{
	// 		location = "kosong";
	// 	}

	// 	if (inspection_level !="") {
	// 		inspection_level = $('#inspection_level').val();
	// 	}else{
	// 		inspection_level = "kosong";
	// 	}
	// 	var url = '{{url("excel/qa/report/incoming")}}';
	// 	window.location = url;
	// 	// console.log('{{url("excel/qa/report/incoming")}}'+'/'+data);

	// 	// $.get('{{ url("excel/qa/report/incoming") }}',data, function(result, status, xhr){
	// 	// });
	// }

	// $("form#exportForm").submit(function(e) {
	// 	// if ($('#file').val() == '') {
	// 	// 	openErrorGritter('Error!', 'You need to select file');
	// 	// 	return false;
	// 	// }

	// 	$("#loading").show();

	// 	e.preventDefault();    
	// 	var date_from = $('#date_from').val();
	// 	var date_to = $('#date_to').val();
	// 	var vendor = $('#vendor').val();
	// 	var material = $('#material').val();
	// 	var location = $('#location').val();
	// 	var inspection_level = $('#inspection_level').val();

	// 	var formData = new FormData();
	// 	formData.append('date_from', date_from);
	// 	formData.append('date_to', date_to);
	// 	formData.append('vendor', vendor);
	// 	formData.append('material', material);
	// 	formData.append('location', location);
	// 	formData.append('inspection_level', inspection_level);
	// 	if (formData.length == 0) {
	// 		$.ajax({
	// 			url: '{{ url("excel/qa/report/incoming") }}',
	// 			type: 'GET',
	// 			success: function (result, status, xhr) {
	// 				if(result.message){
	// 					$("#loading").hide();
	// 					openSuccessGritter('Success', result.message);

	// 				}else{
	// 					$("#loading").hide();
	// 					openErrorGritter('Error!', result.message);
	// 				}
	// 			},
	// 			error: function(result, status, xhr){
	// 				$("#loading").hide();
					
	// 				openErrorGritter('Error!', result.message);
	// 			},
	// 			cache: false,
	// 			contentType: false,
	// 			processData: false
	// 		});
	// 	}else{
	// 		$.ajax({
	// 			url: '{{ url("excel/qa/report/incoming") }}',
	// 			type: 'GET',
	// 			data: formData,
	// 			success: function (result, status, xhr) {
	// 				if(result.message){
	// 					$("#loading").hide();
	// 					openSuccessGritter('Success', result.message);

	// 				}else{
	// 					$("#loading").hide();
	// 					openErrorGritter('Error!', result.message);
	// 				}
	// 			},
	// 			error: function(result, status, xhr){
	// 				$("#loading").hide();
					
	// 				openErrorGritter('Error!', result.message);
	// 			},
	// 			cache: false,
	// 			contentType: false,
	// 			processData: false
	// 		});
	// 	}

		
	// });

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
</script>
@endsection