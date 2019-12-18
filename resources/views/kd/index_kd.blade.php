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
		/* display: none; <- Crashes Chrome on hover */
		-webkit-appearance: none;
		margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
	}

	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
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
		<div class="col-xs-6">
			<div class="box box-danger">
				<div class="box-body">
					<table class="table table-hover table-striped" id="tableList">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 20%;">Material</th>
								<th style="width: 65%;">Description</th>
								<th style="width: 15%;">Sisa Target</th>
							</tr>					
						</thead>
						<tbody id="tableBodyList">
						</tbody>
						<tfoot style="background-color: rgb(252, 248, 227);">
							<tr>
								<th colspan="2" style="text-align:center;">Total:</th>
								<th></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
		<div class="col-xs-6">
			<div class="row">
				<input type="hidden" id="id_silver">
				

				<div class="col-xs-6">
					<span style="font-weight: bold; font-size: 16px;">Material Number:</span>
				</div>
				<div class="col-xs-6">
					<span style="font-weight: bold; font-size: 16px;">Quantity:</span>
				</div>
				<div class="col-xs-6">
					<input type="text" id="material_number" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
				</div>
				<div class="col-xs-6">
					<input type="text" id="quantity"  style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
				</div>
				<div class="col-xs-12">
					<span style="font-weight: bold; font-size: 16px;">Material Description:</span>
					<input type="text" id="material_description" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
				</div>
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-6">
							<span style="font-weight: bold; font-size: 16px;">Actual Count:</span>
							<input type="text" id="actual_count" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
						</div>
						<div class="col-xs-6" style="padding-bottom: 10px;">
							<br>
							<button class="btn btn-primary" onclick="print()" style="font-size: 40px; width: 100%; font-weight: bold; padding: 0;">
								CONFIRM
							</button>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<span style="font-size: 20px; font-weight: bold;">PACKED LIST:</span>
					<table class="table table-hover" id="tablePack">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 5%;">No</th>
								<th style="width: 20%;">Material Number</th>
								<th style="width: 65%;">Material Description</th>
								<th style="width: 10%;">Quantity</th>
							</tr>					
						</thead>
						<tbody id="tableBodyPack">
						</tbody>
						<tfoot id="tableFootPack" style="background-color: rgb(252, 248, 227);">
						</tfoot>
					</table>
					<button class="btn btn-success" onclick="showPrint()" style="font-size: 40px; width: 100%; font-weight: bold; padding: 0;">
						<i class="fa fa-print"></i> PRINT KDO NUMBER 
					</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="print_kdo_modal">
		<div class="modal-dialog modal-xs">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h4 class="modal-title">
						Print KDO Number
					</h4>
				</div>
				<div class="modal-body">
					<div class="modal-body">
						<h5>Are you sure print KDO Number ?</h5>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button class="btn btn-success" onclick="forcePrint()"><span><i class="fa fa-print"></i> Print</span></button>
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
		fillTablePack();
	});

	function showPrint() {
		$("#print_kdo_modal").modal('show');
	}

	function forcePrint() {
		var location = "{{ $location }}";
		
		var data = {
			location : location,
		}

		$("#print_kdo_modal").modal('hide');
		$("#loading").show();
		$.post('{{ url("fetch/kd_force_print_zpro") }}', data,  function(result, status, xhr){
			if(result.status){
				$("#loading").hide();
				$('#actual_count').val(result.actual_count);
				fillTableList();
				fillTablePack();
				openSuccessGritter('Success', result.message);
			}else{
				$("#loading").hide();
				openErrorGritter('Error!', result.message);
			}

		});
	}

	function print() {
		var material_number = $("#material_number").val();
		var quantity = $("#quantity").val();
		var material_description = $("#material_description").val();
		var location = "{{ $location }}";
		
		var data = {
			material_number : material_number,
			quantity : quantity,
			material_description : material_description,
			location : location,
		}

		$("#loading").show();
		$.post('{{ url("fetch/kd_print_zpro") }}', data,  function(result, status, xhr){
			if(result.status){
				$("#loading").hide();
				$('#actual_count').val(result.actual_count);
				fillTableList();
				fillTablePack();
				openSuccessGritter('Success', result.message);
			}else{
				$("#loading").hide();
				openErrorGritter('Error!', result.message);
			}

		});


	}

	function fillField(param) {
		var location = "{{ $location }}";

		data = {
			material_number: param,
			location : location,
		}

		$.get('{{ url("fetch/kd_detail") }}', data,  function(result, status, xhr){
			if(result.status){
				$('#actual_count').val(result.actual_count);
				$('#material_number').val(result.detail[0].material_number);
				$('#quantity').val(result.detail[0].lot_completion);
				$('#material_description').val(result.detail[0].material_description);
			}
		});
	}

	function fillTablePack(){
		$.get('{{ url("fetch/kd_pack/".$location) }}',  function(result, status, xhr){
			if(result.status){
				$('#tableBodyPack').append().empty();
				$('#tableFootPack').append().empty();

				var tableData = "";
				var tableFoot = "";

				var total_qty = 0;
				var count = 0;
				$.each(result.pack, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ ++count +'</td>';
					tableData += '<td>'+ value.material_number +'</td>';
					tableData += '<td>'+ value.material_description +'</td>';
					tableData += '<td>'+ value.quantity +'</td>';
					tableData += '</tr>';
					total_qty += value.quantity;
				});
				$('#tableBodyPack').append(tableData);

				tableFoot += '<tr>';
				tableFoot += '<th colspan="3" style="text-align:center;">Total:</th>';
				tableFoot += '<th>'+ total_qty +'</th>';
				tableFoot += '</tr>';
				$('#tableFootPack').append(tableFoot);
			}

		});
	}


	function fillTableList(){

		$.get('{{ url("fetch/kd/".$location) }}',  function(result, status, xhr){
			$('#tableList').DataTable().clear();
			$('#tableList').DataTable().destroy();
			$('#tableBodyList').html("");

			var tableData = "";
			var total_target = 0;
			$.each(result.target, function(key, value) {
				tableData += '<tr onclick="fillField(\''+value.material_number+'\')">';
				tableData += '<td>'+ value.material_number +'</td>';
				tableData += '<td>'+ value.material_description +'</td>';
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
					var totalPlan = api.column(2).data().reduce(function (a, b) {
						return intVal(a)+intVal(b);
					}, 0)
					$(api.column(2).footer()).html(totalPlan.toLocaleString());
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