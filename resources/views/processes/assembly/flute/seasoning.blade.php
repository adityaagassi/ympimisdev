@extends('layouts.display')
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
	padding-top: 0px;
	padding-bottom: 0px;
}
table.table-bordered > tfoot > tr > th{
	border:1px solid rgb(211,211,211);
}
#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header" >
	<h1>
		{{ $page }} - {{ $location }}<span class="text-purple"> {{ $title_jp }}</span>
	</h1>
</section>
@stop
@section('content')
<section class="content" >
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<input type="hidden" id="employee_id">
	<div class="row">
				<div class="col-xs-12" style="text-align: center;">
				<div class="row">
					<div class="col-xs-10 col-xs-offset-1" style="padding-right: 0px;padding-left: 0px">
						<input type="text" id="operator_name" placeholder="Operator" style="width: 100%;font-size: 17px;text-align:center;padding: 10px">
					</div>
				</div>
			</div>
			<div class="col-xs-12" style="text-align: center;">
				<div class="row">
					<div class="col-xs-8 col-xs-offset-1" style="padding-right: 0px;padding-left: 0px">
						<input type="text" id="tag_product" placeholder="Scan Kanban Here . . ." style="width: 100%;font-size: 20px;text-align:center;padding: 10px">
					</div>
					<div class="col-xs-1" style="padding-right: 0px;padding-left: 0px">
						<button class="btn btn-danger" onclick="cancel()" style="width:100%;font-size: 20px;font-weight: bold;">
							CANCEL
						</button>
					</div>
					<div class="col-xs-1" style="padding-right: 0px;padding-left: 0px">
						<button class="btn btn-warning" onclick="location.reload()" style="width:100%;font-size: 20px;font-weight: bold;">
							REFRESH
						</button>
					</div>
				</div>
			</div>

		<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;padding-top: 10px">
			<div class="col-md-12">
				<div class="box box-solid">
					<div class="box-body">
						<center><span style="font-size: 25px;text-align: center;font-weight: bold;">DETAILS</span> </center>
						<table id="tableHistory" class="table table-bordered table-striped table-hover" style="width: 100%">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 1%">Serial Number</th>
									<th style="width: 1%">Model</th>
									<th style="width: 2%">By</th>
									<th style="width: 1%">At</th>
								</tr>
							</thead>
							<tbody id="tableHistoryBody">
							</tbody>
							<tfoot style="background-color: RGB(252, 248, 227);">
								<tr>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalOperator">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div class="form-group">
							<label for="exampleInputEmail1">Employee ID</label>
							<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID Card" required>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalCompletion">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12">
						<center><h3 style="font-weight: bold;background-color: rgb(126,86,134); color: #FFD700;padding-top: 10px;padding-bottom: 10px;font-size: 30px">CEK DATA TRANSAKSI</h3></center>
					</div>
					<div class="modal-body" id="tableCompletion">

					</div>
					<div class="col-xs-12">
						<button class="btn btn-primary btn-block" style="font-weight: bold;font-size: 25px" onclick="completion()">
							PROSES TRANSAKSI
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalDetail">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body" id="tableDetail">

					</div>
					<div class="col-md-12">
						<!-- <span style="font-size: 24px;">NG List:</span>  -->
						<table id="resultNGDetail" class="table table-bordered table-striped table-hover" style="width: 100%;">
				            <thead style="background-color: rgb(126,86,134); color: #FFD700;">
				            	<tr>
				            		<th colspan="2" style="font-size: 20px;padding: 0px">NG LIST</th>
				            	</tr>
				                <tr>
				                  <th style="width: 17%;padding: 0px">NG</th>
				                  <th style="width: 5%;padding: 0px">Quantity</th>
				                </tr>
				            </thead >
				            <tbody id="resultNGDetailBody">
							</tbody>
			            </table>
					</div>
					<div class="col-xs-12">
						<button class="btn btn-danger pull-right" style="font-weight: bold;" data-dismiss="modal">
							CLOSE
						</button>
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
		$('#resultScanBody').html("");
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});

      $('body').toggleClass("sidebar-collapse");
		$("#tag_product").val("");
		
		$("#operator").val("");
		$('#operator').focus();
		$("#employee_id").val("");
		$("#operator_name").val("-");
		fillResult();
	});

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#operator').focus();
	});

	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator").val().length >= 8){
				var data = {
					employee_id : $("#operator").val()
				}
				
				$.get('{{ url("scan/assembly/operator_kensa") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#modalOperator').modal('hide');
						$('#operator_name').val(result.employee.employee_id+' - '+result.employee.name);
						$('#operator_name').prop('disabled',true);
						$('#employee_id').val(result.employee.employee_id);
						$('#tag_product').focus();
						fillResult();
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator").val("");
			}
		}
	});

	$('#tag_product').keyup(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag_product").val().length >= 8){
				var data = {
					tag : $("#tag_product").val(),
					location : '{{$loc}}',
					employee_id: $('#employee_id').val()
				}
				
				$.post('{{ url("input/assembly/seasoning") }}', data, function(result, status, xhr){
					if(result.status){
						$('#tag_product').val('');
						$('#tag_product').focus();
						fillResult();
						openSuccessGritter('Success!', result.message);
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#tag_product').val('');
						$('#tag_product').focus();
						fillResult();
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Tag Product Invalid.');
				audio_error.play();
				$("#operator").val("");
			}
		}
	});

	function cancel() {
		$('#tag_product').val('');
		$('#tag_product').focus();
	}

	function fillResult() {
		var data = {
			location:'{{$loc}}'
		}
		$.get('{{ url("fetch/assembly") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableHistory').DataTable().clear();
				$('#tableHistory').DataTable().destroy();
				$('#tableHistoryBody').html("");
				var tableData = "";
				if (result.assembly.length > 0) {
					// console.table(result.data);
					$.each(result.assembly, function(key, value) {
						tableData += '<tr>';
						tableData += '<td>'+ value.serial_number +'</td>';
						tableData += '<td>'+ value.model +'</td>';
						tableData += '<td>'+ value.employee_id +'<br>'+ value.name +'</td>';
						tableData += '<td>'+ value.start_at +'</td>';						
						tableData += '</tr>';
					});
				}
				$('#tableHistoryBody').append(tableData);

				$('#tableHistory tfoot th').each(function(){
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				});
				
				var table = $('#tableHistory').DataTable({
					'dom': 'Bfrtip',
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
						}
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching'   	: true,
					'ordering'		: true,
					'order': [],
					'info'       	: true,
					'autoWidth'		: false,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					// "infoCallback": function( settings, start, end, max, total, pre ) {
					// 	return "<b>Total "+ total +" pc(s)</b>";
					// }
				});
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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