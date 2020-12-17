@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	#tableBodyList > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

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
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}

	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}

	input[type=number] {
		-moz-appearance:textfield;
	}
	
	#loading { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple"> {{ $title_jp }}</span></small>
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
	<input type="hidden" id="location" value="{{ $location }}">
	<div class="row">
		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-7">
					<div class="box box-danger">
						<div class="box-body">
							<table class="table table-hover table-bordered table-striped" id="tableList">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 15%;">Stuffing Date</th>
										<th style="width: 10%;">Material</th>
										<th style="width: 60%;">Description</th>
										<th style="width: 10%;">Destination</th>
										<th style="width: 5%;">Total Target</th>
										<th style="width: 5%;">Actual Packing</th>
										<th style="width: 5%;">Sisa Target</th>
									</tr>					
								</thead>
								<tbody id="tableBodyList">
								</tbody>
								<tfoot style="background-color: rgb(252, 248, 227);">
									<tr>
										<th colspan="4" style="text-align:center;">Total:</th>
										<th></th>
										<th></th>
										<th></th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
				<div class="col-xs-5" style="margin-top: 3%;">
					<input type="hidden" id="shipment_id">

					<div class="row">
						<div class="col-xs-6">
							<span style="font-weight: bold; font-size: 16px;">KDO Number:</span>
							<input type="text" id="kd_number" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
						</div>
						<div class="col-xs-6" style="padding-top: 3.9%;">
							<button class="btn btn-primary" id="reprint" onclick="reprint()" style="font-size: 2.5vw; width: 100%; font-weight: bold; padding: 0;" disabled>
								REPRINT KDO
							</button>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-6">
							<span style="font-weight: bold; font-size: 16px;">Stuffing Date:</span>
							<input type="text" id="st_date" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
						</div>
						<div class="col-xs-6">
							<span style="font-weight: bold; font-size: 16px;">Destination:</span>
							<input type="text" id="destination"  style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Material Number:</span>
							<input type="text" id="material_number" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Material Description:</span>
							<input type="text" id="material_description" style="width: 100%; height: 50px; font-size: 25px; text-align: center;" disabled>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-6">
							<span style="font-weight: bold; font-size: 16px;">Qty Packing:</span>
							<input type="text" id="qty_packing" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
						</div>
						<div class="col-xs-6" style="padding-top: 3.9%;">
							<button class="btn btn-success" onclick="print()" style="font-size: 2.5vw; width: 100%; font-weight: bold; padding: 0;">
								CONFIRM
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
					<li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">KDO Detail</a></li>
					<li class="vendor-tab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">KDO</a></li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="tab_1">
						<table id="kdo_detail" class="table table-bordered table-striped table-hover" style="width: 100%;">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 2%">KD Number</th>
									<th style="width: 2%">Material Number</th>
									<th style="width: 5%">Material Description</th>
									<th style="width: 2%">Location</th>
									<th style="width: 1%">Qty</th>
									<th style="width: 2%">Stuffing Date</th>
									<th style="width: 2%">Destination</th>
									<th style="width: 3%">Created At</th>
									{{-- <th style="width: 1%">Reprint</th> --}}
									{{-- <th style="width: 1%">Delete</th> --}}
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
									{{-- <th></th> --}}
									{{-- <th></th> --}}
								</tr>
							</tfoot>
						</table>
					</div>
					<div class="tab-pane" id="tab_2">
						<table id="kdo_table" class="table table-bordered table-striped table-hover" style="width: 100%;">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 1%">KDO</th>
									<th style="width: 1%">Count Item</th>
									<th style="width: 1%">Location</th>
									<th style="width: 1%">Created At</th>
									<th style="width: 1%">Reprint KDO</th>
									{{-- <th style="width: 1%">Cancel</th> --}}
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
									{{-- <th></th> --}}
								</tr>
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
		$('body').toggleClass("sidebar-collapse");
		
		fillTableList();
		fillTable();
		fillTableDetail();

	});


	function deleteKDODetail(id){
		if(confirm("Apa anda yakin akan menghapus data?")){
			$("#loading").show();
			var data = {
				id:id
			}
			$.post('{{ url("delete/kdo_detail") }}', data, function(result, status, xhr){
				if(result.status){
					fillTableList();
					$('#kdo_table').DataTable().ajax.reload();
					$('#kdo_detail').DataTable().ajax.reload();
					$("#loading").hide();
					openSuccessGritter('Success!', result.message);
				}
				else{
					$("#loading").hide();
					openErrorGritter('Error!', result.message);
				}
			});
		}
		else{
			$("#loading").hide();				
		}
	}

	function deleteKDO(id){
		if(confirm("Apa anda yakin akan menghapus data?")){
			$("#loading").show();
			var data = {
				kd_number:id
			}
			$.post('{{ url("delete/kdo") }}', data, function(result, status, xhr){
				if(result.status){
					fillTableList();
					$('#kdo_table').DataTable().ajax.reload();
					$('#kdo_detail').DataTable().ajax.reload();
					$("#loading").hide();
					openSuccessGritter('Success!', result.message);
				}
				else{
					$("#loading").hide();
					openErrorGritter('Error!', result.message);
				}
			});
		}
		else{
			$("#loading").hide();				
		}
	}

	function fillTableDetail(){
		var location = "{{ $location }}";

		var data = {
			status : 1,
			remark : location
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
			{ "data": "quantity" },
			{ "data": "st_date" },
			{ "data": "destination_shortname" },
			{ "data": "updated_at" }
			// { "data": "reprintKDO" },
			// { "data": "deleteKDO" }
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

	function fillTable(){
		var location = "{{ $location }}";

		var data = {
			status : 1,
			remark : location
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
			{ "data": "reprintKDO" }
			// { "data": "deleteKDO" }
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


	function reprintKDO(kd_number){

		printLabel(null, kd_number, ('print'+kd_number) );

	}

	function reprint() {
		var shipment_id = $("#shipment_id").val();
		var kd_number = $("#kd_number").val();

		printLabel(shipment_id, kd_number, ('print'+kd_number) );
	}

	function printLabel(shipment_id, kd_number, windowName) {
		newwindow = window.open('{{ url("index/print_label_mpro/")}}'+'/'+shipment_id+'/'+kd_number, windowName, 'height=550,width=650');

		if (window.focus) {
			newwindow.focus();
		}

		return false;
	}


	function print() {
		var shipment_id = $("#shipment_id").val();
		var kd_number = $("#kd_number").val();
		var material_number = $("#material_number").val();
		var quantity = $("#qty_packing").val();
		var location = "{{ $location }}";

		var data = {
			shipment_id : shipment_id,
			kd_number : kd_number,
			material_number : material_number,
			quantity : quantity,
			location : location,
		}

		if(material_number == ''){
			alert("Material belum dipilih");
			return false;
		}


		$("#loading").show();
		$.post('{{ url("fetch/kd_print_mpro") }}', data,  function(result, status, xhr){
			if(result.status){

				if(kd_number == ''){
					printLabel(shipment_id, result.kd_number, ('print'+kd_number));
				}

				$('#kd_number').val('');
				$('#shipment_id').val('');
				$('#st_date').val('');
				$('#destination').val('');
				$('#material_number').val('');
				$('#material_description').val('');
				$('#qty_packing').val('');

				$("#loading").hide();

				fillTableList();
				$('#kdo_table').DataTable().ajax.reload();
				$('#kdo_detail').DataTable().ajax.reload();
				openSuccessGritter('Success', result.message);
			}else{
				$("#loading").hide();
				openErrorGritter('Error!', result.message);
			}

		});


	}

	function fillField(param) {
		$("#loading").show();

		var data = param.split('_');
		var id = data[0];
		var lot_completion = data[1];

		var st_date = $('#'+param).find('td').eq(0).text();
		var material_number = $('#'+param).find('td').eq(1).text();
		var material_description = $('#'+param).find('td').eq(2).text();
		var destination = $('#'+param).find('td').eq(3).text();
		var target = $('#'+param).find('td').eq(6).text();

		$('#shipment_id').val(id);
		$('#st_date').val(st_date);
		$('#destination').val(destination);
		$('#material_number').val(material_number);
		$('#material_description').val(material_description);

		if((target/lot_completion) >= 1){
			$('#qty_packing').val(lot_completion);
		}else{
			$('#qty_packing').val(target);
		}

		var data = {
			shipment_schedule_id : id
		}

		$.get('{{ url("fetch/check_kd") }}', data, function(result, status, xhr){
			if(result.status){
				$("#loading").hide();
				$('#kd_number').val(result.kd_number);
				$('#reprint').prop('disabled', false);
			}else{
				$("#loading").hide();
				$('#kd_number').val('');
				$('#reprint').prop('disabled', true);
			}

		});
	}


	function fillTableList(){

		$.get('{{ url("fetch/kd_new/".$location) }}',  function(result, status, xhr){
			$('#tableList').DataTable().clear();
			$('#tableList').DataTable().destroy();
			$('#tableBodyList').html("");

			var tableData = "";
			var total_target = 0;
			$.each(result.target, function(key, value) {
				tableData += '<tr id="'+value.id+'_'+value.lot_completion+'" onclick="fillField(id)">';
				tableData += '<td>'+ value.st_date +'</td>';
				tableData += '<td>'+ value.material_number +'</td>';
				tableData += '<td>'+ value.material_description +'</td>';
				tableData += '<td>'+ value.destination_shortname +'</td>';
				tableData += '<td>'+ value.quantity +'</td>';
				tableData += '<td>'+ value.actual_quantity +'</td>';
				tableData += '<td>'+ value.target +'</td>';
				tableData += '</tr>';
				total_target += value.target;
			});
			$('#tableBodyList').append(tableData);


			$('#tableList').DataTable({
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
				"footerCallback": function (tfoot, data, start, end, display) {
					var intVal = function ( i ) {
						return typeof i === 'string' ?
						i.replace(/[\$%,]/g, '')*1 :
						typeof i === 'number' ?
						i : 0;
					};
					var api = this.api();
					var totalPlan = api.column(4).data().reduce(function (a, b) {
						return intVal(a)+intVal(b);
					}, 0)
					$(api.column(4).footer()).html(totalPlan);

					var api = this.api();
					var totalPlan = api.column(6).data().reduce(function (a, b) {
						return intVal(a)+intVal(b);
					}, 0)
					$(api.column(6).footer()).html(totalPlan);
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