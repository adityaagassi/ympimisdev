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
		/*display: inline-block;*/
		overflow: block;
		text-overflow: ellipsis;
		white-space: nowrap;

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
				<div class="col-xs-7" style="padding-right: 0px;">
					<div class="box box-danger">
						<div class="box-body">
							<table class="table table-hover table-bordered table-striped" id="tableList">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 15%;">Due Date</th>
										<th style="width: 10%;">Material</th>
										<th style="width: 40%;">Description</th>
										<th style="width: 15%;">Category</th>
										<th style="width: 5%;">Surface</th>
										<th style="width: 5%;">Target</th>
										<th style="width: 5%;">Lot</th>
										<th style="width: 5%;">Box</th>
									</tr>					
								</thead>
								<tbody id="tableBodyList">
								</tbody>
								<tfoot style="background-color: rgb(252, 248, 227);">
									<tr>
										<th colspan="5" style="text-align:center;">Total:</th>
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
					<div class="row">
						<input type="hidden" id="production_id">
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Export Plan:</span>
							<input type="text" id="export_plan" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
						</div>
						<div class="col-xs-6">
							<span style="font-weight: bold; font-size: 16px;">Due Date:</span>
						</div>
						<div class="col-xs-6">
							<span style="font-weight: bold; font-size: 16px;">Category:</span>
						</div>
						<div class="col-xs-6">
							<input type="text" id="due_date" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
						</div>
						<div class="col-xs-6">
							<input type="text" id="category"  style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
						</div>						
						<div class="col-xs-6">
							<span style="font-weight: bold; font-size: 16px;">Material Number:</span>
						</div>
						<div class="col-xs-6">
							<span style="font-weight: bold; font-size: 16px;">Surface:</span>
						</div>
						<div class="col-xs-6">
							<input type="text" id="material_number" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
						</div>
						<div class="col-xs-6">
							<input type="text" id="surface"  style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
						</div>
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Material Description:</span>
							<input type="text" id="material_description" style="width: 100%; height: 50px; font-size: 25px; text-align: center;" disabled>
						</div>
						<div class="col-xs-6">
							<span style="font-weight: bold; font-size: 16px;">Qty Packing:</span>
							<input type="text" id="qty_packing" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
						</div>
						<div class="col-xs-6" style="padding-top: 3.9%;">
							<button class="btn btn-primary" onclick="print()" style="font-size: 2.5vw; width: 100%; font-weight: bold; padding: 0;">
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
					<li class="vendor-tab"><a href="#tab_1" data-toggle="tab" id="tab_header_1">KDO Detail</a></li>
					<li class="vendor-tab active"><a href="#tab_2" data-toggle="tab" id="tab_header_2">KDO Closure</a></li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane" id="tab_1">
						<table id="kdo_detail" class="table table-bordered table-striped table-hover" style="width: 100%;">
							<thead style="background-color: #e9ecef;">
								<tr>
									<th style="width: 2%">KD Number</th>
									<th style="width: 2%">Material Number</th>
									<th style="width: 5%">Material Description</th>
									<th style="width: 2%">Location</th>
									<th style="width: 1%">Qty</th>
									<th style="width: 3%">Created At</th>
									<th style="width: 1%">Reprint</th>
									<!-- <th style="width: 1%">Delete</th> -->
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
									<!-- <th></th> -->
								</tr>
							</tfoot>
						</table>
					</div>

					<div class="tab-pane active" id="tab_2">
						<div class="col-xs-12" style="margin-bottom: 3%">
							<div class="input-group col-xs-8 col-xs-offset-2">
								<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
									<i class="glyphicon glyphicon-barcode"></i>
								</div>
								<input type="text" style="text-align: center; font-size: 22px" class="form-control" id="kdo_number_closure" placeholder="Scan KDO Here..." required>
								<div class="input-group-addon" id="icon-serial">
									<i class="glyphicon glyphicon-ok"></i>
								</div>
							</div>
						</div>

						<table id="kdo_closure" class="table table-bordered table-striped table-hover" style="width: 100%;">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 2%">KD Number</th>
									<th style="width: 2%">Material Number</th>
									<th style="width: 5%">Material Description</th>
									<th style="width: 2%">Location</th>
									<th style="width: 1%">Qty</th>
									<th style="width: 3%">Created At</th>
									<th style="width: 1%">Reprint</th>
									<th style="width: 1%">Delete</th>
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
		$("#kdo_number_closure").focus();
		$('#export_plan').css({"background-color":"inherit"});


		fillTableList();
		fillTableDetail();
		fillTableClosure();

	});

	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');


	$('#kdo_number_closure').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#kdo_number_closure").val().length >= 10){
				scanKdoClosure();
			}else{
				openErrorGritter('Error!', 'Nomor KDO tidak sesuai.');
				$("#kdo_number_closure").val("");
				$("#kdo_number_closure").focus();
				audio_error.play();
			}
		}
	});


	function scanKdoClosure() {
		var kd_number = $("#kdo_number_closure").val();

		data = {
			kd_number : kd_number
		}

		$("#loading").show();

		$.post('{{ url("scan/kd_closure") }}', data, function(result, status, xhr){
			if(result.status){
				$("#kdo_number_closure").val("");
				$("#kdo_number_closure").focus();

				$('#kdo_detail').DataTable().ajax.reload();
				$('#kdo_closure').DataTable().ajax.reload();

				audio_ok.play();
				$("#loading").hide();
				openSuccessGritter('Success!', result.message);


			}else{
				audio_error.play();
				$("#loading").hide();
				openErrorGritter('Error!', result.message);
				
				$("#kdo_number_closure").val("");
				$("#kdo_number_closure").focus();
			}
		});
		
	}



	function printLabelSubassy(kd_detail,windowName) {
		newwindow = window.open('{{ url("index/print_label_subassy/") }}'+'/'+kd_detail, windowName, 'height=250,width=450');

		if (window.focus) {
			newwindow.focus();
		}

		return false;
	}

	function reprintKDODetail(id){
		var data = id.split('+');
		var kd_detail = data[0];
		var location = "{{ $location }}";

		printLabelSubassy(kd_detail, ('reprint'+kd_detail));
		openSuccessGritter('Success!', "Reprint Success");
	}

	function deleteKDODetail(id){
		if(confirm("Apa anda yakin akan menghapus data?")){
			$("#loading").show();
			var data = {
				id:id
			}
			$.post('{{ url("delete/kdo_detail") }}', data, function(result, status, xhr){
				if(result.status){
					fillTableList();
					$('#kdo_detail').DataTable().ajax.reload();
					$('#kdo_closure').DataTable().ajax.reload();
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

		var data = {
			status : 0,
			remark : "{{ $location }}"
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
			{ "data": "updated_at" },
			{ "data": "reprintKDO" },
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

	function fillTableClosure(){

		var data = {
			status : 1,
			remark : "{{ $location }}"
		}

		$('#kdo_closure tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
		});
		var table = $('#kdo_closure').DataTable( {
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

		$('#kdo_closure tfoot tr').appendTo('#kdo_closure thead');
	}


	function print() {
		var production_id = $("#production_id").val();
		var quantity = $("#qty_packing").val();
		var location = "{{ $location }}";

		var data = {
			production_id : production_id,
			quantity : quantity,
			location : location
		}

		if(production_id == ''){
			alert("Material belum dipilih");
			return false;
		}

		$("#loading").show();
		$.post('{{ url("fetch/kd_print_subassy_new_single") }}', data,  function(result, status, xhr){
			if(result.status){
				var id = result.knock_down_detail.id;

				printLabelSubassy(id, ('print'+id));

				$('#production_id').val('');
				$('#due_date').val('');
				$('#category').val('');
				$('#material_number').val('');
				$('#surface').val('');
				$('#material_description').val('');
				$('#qty_packing').val('');
				$('#export_plan').val('');
				$('#export_plan').css({"background-color":"inherit"});
				
				fillTableList();
				$('#kdo_detail').DataTable().ajax.reload();
				$('#kdo_closure').DataTable().ajax.reload();

				$("#loading").hide();
				openSuccessGritter('Success', result.message);
			}else{
				$("#loading").hide();
				openErrorGritter('Error!', result.message);
			}

		});
	}

	function fillField(param) {
		var data = param.split('_');
		var id = data[0];

		var due_date = $('#'+param).find('td').eq(0).text();
		var material_number = $('#'+param).find('td').eq(1).text();
		var material_description = $('#'+param).find('td').eq(2).text();
		var hpl = $('#'+param).find('td').eq(3).text();
		var surface = $('#'+param).find('td').eq(4).text();
		var target = $('#'+param).find('td').eq(5).text();
		var box = $('#'+param).find('td').eq(7).text();
		var lot_completion = data[1];

		$('#production_id').val(id);
		$('#due_date').val(due_date);
		$('#category').val(hpl);
		$('#material_number').val(material_number);
		$('#surface').val(surface);
		$('#material_description').val(material_description);

		if(box >= 1){
			$('#qty_packing').val(lot_completion);
		}else{
			$('#qty_packing').val(target);
		}

		var data = {
			material_number : material_number
		}

		$("#loading").show();

		$.get('{{ url("fetch/check_export_kd") }}', data, function(result, status, xhr){
			if(result.status){
				$("#loading").hide();
				$('#export_plan').val(result.data.destination_shortname + '  -  ' + result.date);
				$('#export_plan').css({"background-color":"#9ede73"});
			}else{
				$("#loading").hide();
				$('#export_plan').val('Maedoshi');
				$('#export_plan').css({"background-color":"#e48900"});
			}

		});
	}

	function fillTableList(){

		$.get('{{ url("fetch/kd/".$location) }}',  function(result, status, xhr){
			$('#tableList').DataTable().clear();
			$('#tableList').DataTable().destroy();
			$('#tableBodyList').html("");

			var tableData = "";
			$.each(result.target, function(key, value) {
				tableData += '<tr id="'+value.id+'_'+value.lot_completion+'" onclick="fillField(id)">';
				tableData += '<td>'+ value.due_date +'</td>';
				tableData += '<td>'+ value.material_number +'</td>';
				tableData += '<td>'+ value.material_description +'</td>';
				tableData += '<td>'+ value.hpl +'</td>';
				tableData += '<td>'+ (value.surface || '') +'</td>';
				tableData += '<td>'+ value.target +'</td>';
				tableData += '<td>'+ value.lot_completion +'</td>';
				tableData += '<td>'+ value.box +'</td>';
				tableData += '</tr>';
			});
			$('#tableBodyList').append(tableData);

			var table = $('#tableList').DataTable({
				'stateSave': true,
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
					var totalPlan = api.column(5).data().reduce(function (a, b) {
						return intVal(a)+intVal(b);
					}, 0)
					$(api.column(5).footer()).html(totalPlan);

					var totalPlan = api.column(7).data().reduce(function (a, b) {
						return intVal(a)+intVal(b);
					}, 0)
					$(api.column(7).footer()).html(totalPlan);
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
				"bSortCellsTop": true,
				"bFilter": true,
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