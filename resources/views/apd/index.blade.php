@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">

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
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple"> ??</span></small>
		<button class="btn btn-success pull-right" data-toggle="modal"  data-target="#pick-modal" style="margin-right: 5px">
			<i class="fa fa-plus-square"></i>&nbsp;&nbsp;Ambil APD
		</button>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
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

	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-9 pull-left">
			<div class="col-xs-12">		
				<div class="col-xs-3">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border: none;">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="datefrom" placeholder="Select Date From">
					</div>
				</div>
				<div class="col-xs-3">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border: none;">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="dateto" placeholder="Select Date To">
					</div>
				</div>
				<div class="col-xs-1">
					<button class="btn btn-success" type="submit">Update Chart</button>
				</div>
			</div>
			<div class="col-xs-12">
				<div id="container1" style="margin: 0 auto"></div>
			</div>
		</div>
		<div class="col-xs-3 pull-right" id="stock"></div>
	</div>

	<div class="modal modal-default fade" id="pick-modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a;">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">AMBIL APD</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />

								<div class="form-group row" align="right">
									<label class="col-sm-4">APD<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<select class="form-control select2" data-placeholder="Pilih APD" id="apd" style="width: 100%">
											<option value=""></option>
											@foreach($apds as $apd) 
											<option value="{{ $apd }}">{{ $apd }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Jumlah<span class="text-red">*</span></label>
									<div class="col-sm-3">
										<input type="number" class="form-control" id="quantity" placeholder="Jumlah" required>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Tag<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="tag" placeholder="Scan ID Card" required>
									</div>
								</div>

								<div id="employee">
									<div class="form-group row" align="right">
										<label class="col-sm-4">NIK<span class="text-red">*</span></label>
										<div class="col-sm-5">
											<input type="text" class="form-control" id="employee_id" readonly>
										</div>
									</div>

									<div class="form-group row" align="right">
										<label class="col-sm-4">Nama<span class="text-red">*</span></label>
										<div class="col-sm-5">
											<input type="text" class="form-control" id="name" readonly>
										</div>
									</div>	
								</div>

								

							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="save()"><i class="fa fa-save"></i> Submit</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="detail-modal" style="color: black;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" style="text-transform: uppercase; text-align: center;"><b>Detail Masker N95</b></h4>
					<h5 class="modal-title" style="text-align: center;" id="judul-detail"></h5>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<table id="detail" class="table table-striped table-bordered" style="width: 100%;"> 
								<thead id="detail-head" style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>Picked at</th>
										<th>Employee ID</th>
										<th>Nama</th>
										<th>Department</th>
										<th>Lokasi Masker</th>
										<th>Pemberi</th>
										<th>Qty</th>
									</tr>
								</thead>
								<tbody id="detail-body">
								</tbody>
							</table>
						</div>

					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
				</div>
			</div>
		</div>
	</div>




</section>
@endsection
@section('scripts')

<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/highstock.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	
	jQuery(document).ready(function() {
		$('.select2').select2();
		$('#employee').hide();

		drawChart();

	});


	$('#pick-modal').on('hidden.bs.modal', function () {
		$('#apd').prop('selectedIndex', 0).change();
		$('#quantity').val('');
		$('#tag').val('');
		$('#employee_id').val('');
		$('#name').val('');
		$('#employee').hide();
	});



	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($('#tag').val().length > 9 ){
				var data = {
					employee_id : $("#tag").val()
				}

				$.get('{{ url("scan/welding/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#employee_id').val(result.employee.employee_id);
						$('#name').val(result.employee.name);
						$('#employee').show();			
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#tag').val('');
					}
				});
			}
		}
	});

	function save(){
		if($('#tag').val() == ""){
			openErrorGritter('Error!', 'Tag is empty');
			audio_error.play();
			$("#tag").val("");
			$("#tag").focus();

			return false;
		}

		var apd = $('#apd').val();
		var quantity = $('#quantity').val();
		var employee_id = $('#employee_id').val();		

		var data = {
			apd: apd,
			quantity: quantity,
			employee_id: employee_id,
		}

		$("#loading").show();

		$.post('{{ url("input/apd") }}', data, function(result, status, xhr){
			if(result.status){
				$("#loading").hide();

				openSuccessGritter('Success!', result.message);
				$('#apd').prop('selectedIndex', 0).change();
				$('#quantity').val('');
				$('#tag').val('');
				$('#employee_id').val('');
				$('#name').val('');
				$('#employee').hide();

				$('#pick-modal').modal('hide');
				drawChart();

			}
			else{
				$("#loading").hide();
				audio_error.play();
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function drawChart() {
		var datefrom = $("#datefrom").val();
		var dateto = $("#dateto").val();

		var data = {
			datefrom:datefrom,
			dateto:dateto,
		}

		$.get('{{ url("fetch/apd") }}', data, function(result, status, xhr) {
			if(result.status){
				var department = [];
				var qty = [];
				for (i = 0; i < result.dept.length; i++) {
					department.push(result.dept[i].department);
					qty.push(parseInt(result.dept[i].quantity));
				}

				Highcharts.chart('container1', {
					chart: {
						backgroundColor: {
							linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
							stops: [
							[0, '#ecf0f5']
							]
						},
						type: 'column'
					},
					title: {
						text: "Penggunaan Masker N95"
					},
					subtitle: {
						text: result.datefrom +' ~ '+ result.dateto,
						style: {
							fontSize: '1vw',
						}
					},							
					xAxis: {
						categories: department,
					},
					yAxis: {
						title: {
							text: "Employee(s)"
						}
					},
					tooltip: {
						shared: true,
					},
					credits: {
						enabled: false
					},
					legend: {
						enabled: false
					},
					plotOptions: {
						series:{
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '15px'
								}
							},
							animation: false,
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										showDetail(this.category,  result.datefrom, result.dateto);
									}
								}
							},
						}
					},
					series: 
					[{
						name: 'Department',
						data: qty,
						colorByPoint: true,
					}]
				});



				$('#stock').html("");
				var div = '';
				for (i = 0; i < result.stock.length; i++) {
					div += '<div class="small-box bg-green" style="font-size: 30px; font-weight: bold; height: 80px; margin-bottom: 5px;">';
					div += '<div class="inner" style="padding-bottom: 0px;">';
					div += '<h3 style="margin-bottom: 0px;font-size: 1.5vw;"><b>'+ result.stock[i].location +'</b></h3>';
					div += '<h2 style="margin: 0px; font-size: 2.5vw;">'+ result.stock[i].quantity +'<sup style="font-size: 1vw">PC(s)</sup></h2>';
					div += '</div>';
					div += '</div>';
				}
				$('#stock').append(div);

			}

		});

	}

	function showDetail(department, datefrom, dateto) {
		var data = {
			department : department,
			datefrom : datefrom,
			dateto : dateto
		}

		$.get('{{ url("fetch/apd_detail") }}', data, function(result, status, xhr){
			if(result.status){
				$('#detail-modal').modal('show');

				$('#detail').DataTable().clear();
				$('#detail').DataTable().destroy();
				$('#detail-body').html("");

				$('#judul-detail').append().empty();
				$('#judul-detail').append('<b>'+ department +' on '+ datefrom +' ~ '+ dateto +'</b>');

				var body = '';
				for (var i = 0; i < result.detail.length; i++) {
					body += '<tr>';
					body += '<td>'+ result.detail[i].created_at +'</td>';
					body += '<td>'+ result.detail[i].employee_id +'</td>';
					body += '<td>'+ result.detail[i].name +'</td>';
					body += '<td>'+ result.detail[i].department +'</td>';
					body += '<td>'+ result.detail[i].location +'</td>';
					body += '<td>'+ result.detail[i].leader +'</td>';
					body += '<td>'+ result.detail[i].quantity +'</td>';
					body += '</tr>';
				}

				$('#detail-body').append(body);
				$('#detail').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
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
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching': true,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});
			}
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