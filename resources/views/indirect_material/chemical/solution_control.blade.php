@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	table {
		table-layout:fixed;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	td:hover {
		overflow: visible;
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
	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}
	#loading, #error { display: none; }

	.selected {
		background: gold !important;
	}
	/* Chrome, Safari, Edge, Opera */
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}

	/* Firefox */
	input[type=number] {
		-moz-appearance: textfield;
	}
</style>
@stop

@section('header')
<section class="content-header">
	@foreach(Auth::user()->role->permissions as $perm)
	@php
	$navs[] = $perm->navigation_code;
	@endphp
	@endforeach

	
	<button class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#add_result" style="margin-right: 5px">
		<i class="fa fa-plus"></i>&nbsp;&nbsp;Add Production Result
	</button>
	<h1>
		{{ $title }} <span class="text-purple">{{ $title_jp }}</span>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="row" style="margin:0px;">
				<div class="col-xs-2">
					<div class="input-group date">
						<div class="input-group-addon bg-blue" style="border: none;">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="datefrom" placeholder="Date From">
					</div>
				</div>
				<div class="col-xs-2">
					<div class="input-group date">
						<div class="input-group-addon bg-blue" style="border: none;">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="dateto" placeholder="Date To">
					</div>
				</div>
				<div class="col-xs-4" style="color: black;">
					<div class="form-group">
						<select class="form-control select2" id='larutan_id' data-placeholder="Select Larutan" style="width: 100%;">
							<option value="">Select Larutan</option>
							@foreach($solutions as $solution)
							<option style="text-transform: capitalize;" value="{{ $solution->id }}">{{ $solution->section }} - {{ $solution->location }} - {{ $solution->solution_name }}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="col-xs-1">
					<button class="btn btn-primary" type="submit" onclick="drawChart()">Update Chart</button>
				</div>

				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
			</div>
		</div>
		<div class="col-xs-12" id="chart">
			<div class="nav-tabs-custom">
				<div class="tab-content">
					<div id="container" style="height: 500px;"></div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="add_result">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h4 class="modal-title">
						Add Production Result
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />

								<div class="form-group row" align="right">
									<label class="col-sm-3">Date<span class="text-red">*</span></label>
									<div class="col-sm-4" align="left">
										<div class="input-group date">
											<div class="input-group-addon bg-green" style="border: none;">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control dateinput" id="date" placeholder="Select Date">
										</div>
									</div>	
								</div>
								
								<div class="form-group row" align="right">
									<label class="col-sm-3">Larutan<span class="text-red">*</span></label>
									<div class="col-sm-8" align="left">
										<select class="form-control select3" data-placeholder="Select Larutan" id="add_larutan_id" style="width: 100%">
											<option value="">Select Solution</option>
											@foreach($solutions as $solution)
											<option style="text-transform: capitalize;" value="{{ $solution->id }}">{{ $solution->section }} - {{ $solution->location }} - {{ $solution->solution_name }}</option>
											@endforeach
											
										</select>
									</div>	
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-3">Material<span class="text-red">*</span></label>
									<div class="col-sm-4" align="left">
										<select class="form-control select3" data-placeholder="Select Material" id="add_material" style="width: 100%">
											<option value="">Select Solution</option>
											@foreach($convertions as $convertion)
											<option style="text-transform: capitalize;" value="{{ $convertion->id }}(ime){{ $convertion->material }}">{{ $convertion->material }}</option>
											@endforeach

										</select>
									</div>
									<div class="col-sm-2" style="padding-left: 0px;" align="left">
										<input class="form-control" type="number" id="add_qty" placeholder="Qty">
									</div>
									<div class="col-sm-2" align="left" style="padding-left: 0px;">
										<button class="btn btn-primary" onclick="add()">
											&nbsp;&nbsp;&nbsp; Tambah &nbsp;&nbsp;&nbsp;
										</button>
									</div>

									{{-- <label class="col-sm-9 col-sm-offset-3" style="text-align: left;">Selain WST, material pilih "<span class="text-red"><b><i>All Materials</i></b></span>"</label> --}}

								</div>


								<div class="col-sm-8 col-sm-offset-3" style="padding-left: 10px;">
									<span style="font-weight: bold; font-size: 1vw;">Production Result<span class="text-red">*</span></span>
									<table class="table table-hover table-bordered table-striped" id="tableAdd">
										<thead style="background-color: rgba(126,86,134,.7);">
											<tr>
												<th style="width: 50%;">material</th>
												<th style="width: 30%;">qty</th>
												<th style="width: 20%;">#</th>

											</tr>
										</thead>
										<tbody id="tableAddBody">
										</tbody>
									</table>
								</div>



							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button class="btn btn-success" onclick="addResult()"> Submit</button>
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
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script src="{{ url("js/icheck.min.js")}}"></script>
<script src="{{ url("js/highstock.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.select2').select2();

		$('.datepicker').datepicker({
			autoclose: true,
			todayHighlight: true,
			format: 'yyyy-mm-dd'
		});

		$('.dateinput').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			todayHighlight: true,
			format: 'yyyy-mm-dd',
			startDate: '<?php echo $tgl_max ?>'
		});

		$('#chart').hide();
		
	});

	function clearConfirmation(){
		location.reload(true);		
	}


	$(function () {
		$('.select3').select2({
			dropdownParent: $('#add_result')
		});
	})

	$("#add_result").on("hidden.bs.modal", function () {
		$('#date').val('');		
		$("#add_larutan_id").prop('selectedIndex', 0).change();
		$("#add_material").prop('selectedIndex', 0).change();
		$('#add_qty').val('');
		$('#tableAddBody').html('');

		materials = [];

	});

	var materials = [];

	function add(){
		if($('#add_material').val() != "" && $('#add_qty').val() != ""){

			var id = $('#add_larutan_id').val();

			var data = {
				id : id
			}

			$.get('{{ url("fetch/chm_check_result") }}', data, function(result, status, xhr){

				var add_material = $('#add_material').val();
				var quantity = $('#add_qty').val();
				var material = add_material.split('(ime)');

				// if(material[1] != 'All Materials'){
				// 	if(result.data.target_uom != 'DM2'){
				// 		openErrorGritter('Error!', 'Selain WST, material pilih "All Materials"');
				// 		return false;
				// 	}
				// }

				tableData = "";

				tableData += "<tr id='rowAdd"+material[0]+"'>";
				tableData += '<td>'+material[1]+'</td>';
				tableData += '<td>'+quantity+'</td>';
				tableData += "<td><a href='javascript:void(0)' onclick='remAdd(id)' id='"+material[0]+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";
				tableData += '</tr>';


				materials.push([ material[0] , material[1], quantity]);

				$('#tableAddBody').append(tableData);
				$("#add_material").prop('selectedIndex', 0).change();
				$('#add_qty').val('');

			});

		}else{
			openErrorGritter('Error!', 'Pilih Material dan Input Qty terlebih dahulu');
		}
	}

	function remAdd(id) {
		$('#rowAdd'+id).remove();

		for (var i = 0; i < materials.length; i++) {
			if(materials[i][0] == id){
				materials.splice(i, 1);
			}
		}
	}


	function addResult() {
		var date = $('#date').val();
		var larutan = $('#add_larutan_id').val();

		if(date == '' || larutan == ''){
			openErrorGritter('Error', 'Tanggal dan larutan harus di isi');
			return false;
		}

		if(materials.length < 1){
			openErrorGritter('Error', 'Material belum dipilih');
			return false;
		}

		var data = {
			date : date,
			larutan : larutan,
			materials : materials
		}

		$("#loading").show();

		$.post('{{ url("input/chm_production_result") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success', result.message);

				$('#add_result').modal('hide');
				
				$('#date').val('');		
				// $("#add_larutan_id").prop('selectedIndex', 0).change();
				$("#add_material").prop('selectedIndex', 0).change();
				$('#add_qty').val('');
				$('#tableAddBody').html('');
				
				$("#loading").hide();

				materials = [];

				drawChart();

			}else{
				openErrorGritter('Error', result.message);
				$("#loading").hide();

			}
		});

	}

	function drawChart() {

		var larutan = $('#larutan_id').val();
		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();

		if(larutan == ''){
			var larutan = $('#add_larutan_id').val();
		}
		
		var data = {
			larutan:larutan,
			datefrom:datefrom,
			dateto:dateto
		}

		$.get('{{ url("fetch/chm_solution_control") }}', data, function(result, status, xhr) {			
			if(result.status){
				$('#chart').show();

				var date = [];
				var accumulative = [];
				var target_max = [];
				var target_warning = [];

				for(var i = 0; i < result.data.length; i++){
					date.push(result.data[i].date);
					accumulative.push([Date.parse(date[i]), result.data[i].accumulative]);
					target_max.push([Date.parse(date[i]), result.data[i].target_max]);
					target_warning.push([Date.parse(date[i]), result.data[i].target_warning]);
				}


				var chart = Highcharts.stockChart('container', {
					rangeSelector: {
						selected: 0
					},
					chart: {
						type: 'line'
					},
					scrollbar:{
						enabled:false
					},
					navigator:{
						enabled:false
					},
					title: {
						text: 'Trend of Producing Materials in Larutan ' + result.location.solution_name,
						style: {
							fontSize: '1.5vw'
						}
					},
					subtitle: {
						text: result.location.department+' ~ '+result.location.location,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						type: 'datetime',
						tickInterval: 24 * 3600 * 1000,
						scrollbar: {
							enabled: true
						}
					},
					yAxis: {
						title: {
							text: null
						},
						tickPositioner: function () {
							var positions = [],
							// tick = Math.floor(this.dataMin),
							tick = 0,
							increment = Math.ceil((this.dataMax - this.dataMin) / 8);

							if (this.dataMax !== null && this.dataMin !== null) {
								for (tick; tick - increment <= this.dataMax; tick += increment) {
									positions.push(tick);
								}
							}
							return positions;
						}
					},
					plotOptions: {
						line: {
							dataLabels: {
								enabled: false
							},
							enableMouseTracking: true,
							connectNulls: true,
							lineWidth: 3,
							point: {
								events: {
									click: function (event) {
										// alert(event.point.name);

									}
								}
							}
						}
						
					},
					credits: {
						enabled : false
					},
					legend: {
						enabled: true,
						layout: 'vertical',
						align: 'right',
						verticalAlign: 'middle'
					},
					series: [{
						name: 'Target Max',
						data: target_max,
						marker: {
							enabled: false
						},
						color: '#000',

					},{
						name: 'Target Warning',
						data: target_warning,
						marker: {
							enabled: false
						},
						color: '#000',
						dashStyle: 'Dash'
					},{
						name: 'Total Produksi',
						data: accumulative,
						marker: {
							enabled: true,
							radius: 3
						},
						color: '#f44336'
					}]
				});
			}

		});
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

</script>
@endsection

