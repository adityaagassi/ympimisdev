@extends('layouts.visitor')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
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
<section class="content-header" style="text-align: center;">
	
	
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<center><span style="font-size: 3vw; color: #00a65a;font-weight: bold;"><i class="fa fa-angle-double-down"></i> KEMBALI KE WAREHOUSE <i class="fa fa-angle-double-down"></i></span></center>
	</div>
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="input-group col-md-12" style="text-align: center;">
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; font-size: 3vw; border-color: red;">
					<i class="glyphicon glyphicon-barcode"></i>
				</div>
				<input type="text" style="text-align: center; border-color: red; font-size: 3vw; height: 70px" class="form-control" id="serialNumber" name="serialNumber" placeholder="Scan Serial Number Here" required>
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; font-size: 3vw; border-color: red;">
					<i class="glyphicon glyphicon-barcode"></i>
				</div>
			</div>

		</div>
	</div>
	<br>
	<div class="row">
		<div class="col-xs-12" style="text-align: center;">
			<div class="input-group col-md-8 col-md-offset-2">
				<div class="box box-danger">
					<div class="box-body">
						<table id="kembali" class="table table-bordered table-striped table-hover" style="width: 100%">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>Serial Number</th>
									<th>Material Number</th>
									<th>Origin Group</th>
									<th>FLO Number</th>
									<th>Quantity</th>
									<th>Packed at</th>
									<th>Status</th>
									<th>Returned_at</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
							<tfoot>
							</tfoot>
						</table>
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

	jQuery(document).ready(function() {
		$("#serialNumber").val("");
		$('#serialNumber').focus();
		fetchTabelKembali();
		$('#serialNumber').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#serialNumber").val().length >= 8){
					scanSerialNumber();
					return false;
				}
				else{
					openErrorGritter('Error!', 'Serial number invalid.');
					audio_error.play();
					$("#serialNumber").val("");
					$("#serialNumber").focus();
				}
			}
		});

	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function scanSerialNumber(){
		var serialNumber = $("#serialNumber").val();
		
		var data = {
			serialNumber : serialNumber
		}

		console.table(data);

		$.post('{{ url("scan/flute_repair/kembali") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					$("#serialNumber").val("");
					$("#serialNumber").focus();
					openSuccessGritter('Success!', result.message);
					$('#kembali').DataTable().ajax.reload();
				}
				else{
					openErrorGritter('Error!', result.message);
					audio_error.play();
					$("#serialNumber").val("");
					$("#serialNumber").focus();
				}
			}
			else{
				alert('Disconnected from server');
			}
		});

	}

	function fetchTabelKembali(){
		$('#kembali').DataTable().destroy();
		$('#kembali').DataTable({
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
			},
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'language': { 'search': "" },
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/flute_repair/kembali") }}",
			},
			"columns": [
			{ "data": "serial_number" },
			{ "data": "material_number" },
			{ "data": "origin_group_code" },
			{ "data": "flo_number" },
			{ "data": "quantity" },
			{ "data": "packed_at" },
			{ "data": "status" },
			{ "data": "updated_at" },
			]
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
</script>
@endsection