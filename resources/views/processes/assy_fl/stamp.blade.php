@extends('layouts.master')
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
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	table {
		table-layout:fixed;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid black;
		/*margin-top:20px;*/
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
<section class="content-header">
	<h1>
		Stamp Flute<span class="text-purple"> フルートの刻印</span>
		<small>Serial Number <span class="text-purple"> 通し番号</span></small>
	</h1>
	<ol class="breadcrumb">
		<li>
			<a href="{{ url("/index/displayWipFl") }}" class="btn btn-warning btn-sm" style="color:white"><i class="fa fa-television "></i>&nbsp;Display</a>
			<button href="javascript:void(0)" class="btn btn-info btn-sm" data-toggle="modal" data-target="#reprintModal">
				<i class="fa fa-print"></i>&nbsp;&nbsp;Reprint
			</button>
			<a href="javascript:void(0)" onclick="adjust()" class="btn btn-danger btn-sm" style="color:white"><i class="fa fa-edit "></i>&nbsp;Adjust Serial</a>
			<a href="{{ url("/stamp/resumes") }}" class="btn btn-primary btn-sm" style="color:white"><i class="fa fa-calendar-check-o "></i>&nbsp;Stamp Record</a>
		</li>
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Success!</h4>
		{{ session('status') }}
	</div>   
	@endif
	<div class="row">
		<div class="col-md-12">
			<div class="box box-primary">
				<div class="box-body">
					<input type="hidden" value="{{ Auth::user()->role_code }}" id="role_code" />
					<div class="row">
						<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
							<p style="position: absolute; color: White; top: 45%; left: 35%;">
								<span style="font-size: 40px">Loading, please wait...<i class="fa fa-spin fa-refresh"></i></span>
							</p>
						</div>
						<div id="error" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(255,102,102); z-index: 30001; opacity: 0.8;">
							<p id="pError" style="position: absolute; color: White; top: 45%; left: 35%;">
								
							</p>
						</div>
						<!-- <div class="col-xs-5">
							<center>
								<span style="font-size: 24px;">Schedule:</span>
							</center>
							<table id="planTable" name="planTable" class="table table-bordered table-hover table-striped">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th rowspan="2">Model</th>
										<th rowspan="2">MTD (H-1)</th>
										<th rowspan="2">Target Packing</th>
										<th rowspan="2">Act Packing</th>
										<th colspan="2" width="15%">Stock</th>
										<th rowspan="2">Target SubAssy (H)</th>
										<th rowspan="2">Stamping</th>
										<th rowspan="2">Target SubAssy (H+1 Full)</th> -->
										<!-- <th>Diff</th> -->
									<!-- </tr>
									<tr>
										<th>WIP</th>
										<th>NG</th>
									</tr>
								</thead>
								<tbody id="planTableBody">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
									<tr>
										<th>Total</th>
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
						</div> -->
						<div class="col-xs-6">
							<center>
								<span style="font-size: 24px">Last Counter:</span><br>
								<input id="nextCounter" type="hidden" style="font-weight: bold; background-color: rgb(255,255,204); width: 100%; text-align: center; font-size: 4vw" disabled>
								<input id="lastCounter" type="text" style="font-weight: bold; background-color: rgb(255,255,204); width: 100%; text-align: center; font-size: 4vw" disabled>
								<input type="button" style="width: 49%; margin-top: 5px;" class="btn btn-danger" value="MINUS" id="minus" onclick="adjustSerial(id)">
								<input type="button" style="width: 49%; margin-top: 5px;" class="btn btn-danger" value="PLUS" id="plus" onclick="adjustSerial(id)">
								<span style="font-size: 24px">Model:</span><br>
								<input id="model" type="text" style="font-weight: bold; background-color: rgb(255,127,80);; width: 100%; text-align: center; font-size: 4vw" value="YFL" disabled>

								<button type="button" style="width: 40%; margin-top: 5px;" class="btn btn-info" id="FG" onclick="category(id)"> FG</button>
								<INPUT type="TEXT" style="width: 15%; height:  100%; margin-top: 5px; font-weight: bold; background-color: rgb(255,255,204); border : solid 2px; text-align: center; " class="btn"   value="FG" name="category" id="category" readonly>
								<button type="button" style="width: 40%; margin-top: 5px;" class="btn btn-info" id="KD" onclick="category(id)"> KD</button>
								{{-- <input type="hidden" class="form-control" value="fg" name="category" id="category"> --}}<br><br>
								<div id="listModel">
								</div>
							</center>
						</div>
						<div class="col-xs-6">
							<center>
								<span style="font-size: 24px;">Result:</span>
							</center>
							<table id="resultTable" name="resultTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<th style="width: 20%">Serial Number</th>
									<th style="width: 25%">Model</th>
									<th style="width: 40%">Stamped At</th>
									<th style="width: 15%">#</th>
								</thead>
								<tbody id="resultTableBody">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="editModal">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Edit Stamp</h4>
			</div>
			<div class="modal-body">
				<div class="input-group">
					<input type="text" style="text-align: center;" class="form-control" name="serialNumberText" id="serialNumberText" disabled>
					<input type="text" style="text-align: center;" class="form-control" name="modelText" id="modelText">
					<input type="hidden" class="form-control" name="idStamp" id="idStamp">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" onclick="destroyStamp()" class="btn btn-danger pull-left">Delete</button>
				<button type="button" onclick="updateStamp()" class="btn btn-primary">Confirm</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="adjustModal">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Edit Stamp</h4>
			</div>
			<div class="modal-body">
				<div class="input-group">
					<div class="row">
						<div class="col-md-12">
							<label>Prefix</label>
							<input type="text" style="text-align: center;" class="form-control" name="prefix" id="prefix">
						</div>
						<div class="col-md-12">
							<label>Last Index</label>
							<input type="text" style="text-align: center;" class="form-control" name="lastIndex" id="lastIndex">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" onclick="updateSerial()" class="btn btn-primary">Confirm</button>
			</div>
		</div>
	</div>
</div>

<div class="modal modal-default fade" id="reprintModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="titleModal">Reprint Stamp</h4>
			</div>
			<form class="form-horizontal" role="form" method="post" action="{{url('reprint/stamp')}}">
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="modal-body" id="messageModal">
					<label>Serial Number</label>
					<select class="form-control select2" name="stamp_number_reprint" style="width: 100%;" data-placeholder="Choose a Serial Number ..." id="stamp_number_reprint" required>
						<option value=""></option>
						@foreach($model2 as $model)
						<option value="{{ $model->serial_number }}">{{ $model->serial_number }}</option>
						@endforeach
					</select>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button id="modalReprintButton" type="submit" class="btn btn-danger"><i class="fa fa-print"></i>&nbsp; Reprint</button>
				</div>
			</form>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$(function () {
			$('.select2').select2()
		});
		
		

		$('body').toggleClass("sidebar-collapse");
		fillPlan();
		fillSerialNumber();
		fillResult();
		// stamp();
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function insert(num){
		document.form.textview.value = document.form.textview.value+num;
	}

	function fillPlan(){
		$.get('{{ url("stamp/fetchPlan") }}'+'/YFL', function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status = 200){
				if(result.status){
					$('#planTable').DataTable().destroy();
					$('#planTableBody').html("");
					var planData = '';
					$.each(result.planData, function(key, value) {
						planData += '<tr>';
						planData += '<td>'+ value.model +'</td>';
						planData += '<td>'+ value.plan +'</td>';
						planData += '<td>'+ value.actual +'</td>';
						planData += '<td>'+ (value.actual-value.plan) +'</td>';
						planData += '</tr>';
					});
					$('#planTableBody').append(planData);
					$('#listModel').html("");
					$.unique(result.model.map(function (d) {
						$('#listModel').append('<button type="button" class="btn bg-olive btn-lg" style="margin-top: 3px; margin-left: 2px; margin-right: 2px; width: 19%; font-size: 1vw" id="'+d.model+'" onclick="model(id)">'+d.model+'</button>');
					}));
					$('#planTable').DataTable({
						'paging': false,
						'lengthChange': false,
						'searching': false,
						'ordering': false,
						'order': [],
						'info': false,
						'autoWidth': true,
						"footerCallback": function (tfoot, data, start, end, display) {
							var intVal = function ( i ) {
								return typeof i === 'string' ?
								i.replace(/[\$,]/g, '')*1 :
								typeof i === 'number' ?
								i : 0;
							};
							var api = this.api();
							var total_diff = api.column(1).data().reduce(function (a, b) {
								return intVal(a)+intVal(b);
							}, 0)
							$(api.column(1).footer()).html(total_diff.toLocaleString());

							var total_actual = api.column(2).data().reduce(function (a, b) {
								return intVal(a)+intVal(b);
							}, 0)
							$(api.column(2).footer()).html(total_actual.toLocaleString());

							var total_plan = api.column(3).data().reduce(function (a, b) {
								return intVal(a)+intVal(b);
							}, 0)
							$(api.column(3).footer()).html(total_plan.toLocaleString());
						},
						"columnDefs": [ {
							"targets": 3,
							"createdCell": function (td, cellData, rowData, row, col) {
								if ( cellData <  0 ) {
									$(td).css('background-color', 'RGB(255,204,255)')
								}
								else
								{
									$(td).css('background-color', 'RGB(204,255,255)')
								}
							}
						}]
					});
				}
				else{
					audio_error.play();
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				audio_error.play();
				alert('Disconnected from server');
			}
		});
	}

	function fillSerialNumber(){
		var originGroupCode = '041';
		var data = {
			originGroupCode:originGroupCode,
		}
		$.get('{{ url("stamp/fetchSerialNumber") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$("#lastCounter").val(result.lastCounter);
					$("#nextCounter").val(result.nextCounter);
					stamp();
				}
				else{
					audio_error.play();
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				audio_error.play();
				alert('Disconnected from server');
			}
		});
	}

	function model(id){
		// $('#model').val(id);
		document.getElementById("model").value = id;
	}

	function adjust(){
		var data = {
			originGroupCode:'041'
		}
		$.get('{{ url("stamp/adjust") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#prefix').val(result.prefix);
					$('#lastIndex').val(result.lastIndex);
					$('#adjustModal').modal('show');
				}
				else{
					audio_error.play();
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				audio_error.play();
				alert('Disconnected from server');
			}
		});
	}

	function updateSerial(){
		var prefix = $('#prefix').val();
		var lastIndex = $('#lastIndex').val();
		var data = {
			prefix:prefix,
			lastIndex:lastIndex,
			originGroupCode:'041'
		}
		$.post('{{ url("stamp/adjust") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#prefix').val("");
					$('#lastIndex').val("");
					$('#adjustModal').modal('hide');
					openSuccessGritter('Success!', result.message);
					fillSerialNumber();
				}
				else{
					audio_error.play();
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				audio_error.play();
				alert('Disconnected from server');
			}
		});
	}

	function adjustSerial(id){
		var data ={
			adjust:id,
			originGroupCode:'041'
		}
		$.post('{{ url("stamp/adjustSerial") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					fillSerialNumber();
					openSuccessGritter('Success!', result.message);
				}
				else{
					audio_error.play();
					alert('Attempt to retrieve data failed');
				}
			}
			else{
			}
		});
	}

	function fillResult(){
		$.get('{{ url("stamp/fetchResult") }}'+'/YFL', function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			$('#resultTable').DataTable().destroy();
			if(xhr.status == 200){
				if(result.status){
					$('#resultTableBody').html("");
					var resultData = '';
					$.each(result.resultData, function(key, value){
						resultData += '<tr>';
						resultData += '<td>'+ value.serial_number +'</td>';
						resultData += '<td>'+ value.model +'</td>';
						resultData += '<td>'+ value.created_at +'</td>';
						resultData += '<td><button class="btn btn-xs btn-danger" id="'+value.id+'" onclick="editStamp(id)"><span class="fa fa-edit"></span></button></td>';
						resultData += '</tr>';
					});
					$('#resultTableBody').append(resultData);
					$('#resultTable').DataTable({
						"sDom": '<"top"i>rt<"bottom"flp><"clear">',
						'paging'      	: true,
						'lengthChange'	: false,
						'searching'   	: true,
						'ordering'		: false,
						'info'       	: true,
						'autoWidth'		: false,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						"infoCallback": function( settings, start, end, max, total, pre ) {
							return "<b>Total "+ total +" pc(s)</b>";
						}
					});
				}
				else{
					audio_error.play();
					alert('Attempt to retrieve data');
				}
			}
			else{
				audio_error.play();
				alert('Disconnected from server');
			}
		});
	}

	function stamp(){
		var model = $('#model').val();
		var category = $('#category').val();
		var serialNumber = $('#nextCounter').val();
		var originGroupCode = '041';
		var processCode = '1';
		var manPower = '27';
		var data = {
			model:model,
			category:category,
			serialNumber:serialNumber,
			originGroupCode:originGroupCode,
			processCode:processCode,
			manPower:manPower
		}
		$.get('{{ url("stamp/stamp") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					if(result.statusCode == 'stamp'){
						fillResult();
						fillPlan();
						fillSerialNumber();
						openSuccessGritter('Success!', result.message);
					}
					// setTimeout(stamp, 200);
					stamp();
				}
				else{
					audio_error.play();
					$("#pError").html('<span style="font-size: 40px"><i class="fa fa-unlink"></i> '+ result.message +'</span>');
					$("#error").show();
				}
			}
			else{
				audio_error.play();
				alert('Disconnected from sever');
			}
		});
	}

	function editStamp(id){
		var data = {
			id:id
		}
		$.get('{{ url("edit/stamp") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#modelText').val(result.logProcess.model);
					$('#serialNumberText').val(result.logProcess.serial_number);
					$('#idStamp').val(result.logProcess.id);
					$('#editModal').modal('show');
				}
				else{
					audio_error.play();
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				audio_error.play();
				alert('Disconnected from sever');
			}
		});
	}

	function destroyStamp(){
		var id = $('#idStamp').val();
		var model = $('#modelText').val();
		var data = {
			id:id,
			model:model,
			originGroupCode:'041',
			processCode:'1',
		}
		if(confirm("Are you sure you want to delete this data?")){
			$.post('{{ url("destroy/stamp") }}', data, function(result, status, xhr){
				console.log(status);
				console.log(result);
				console.log(xhr);
				if(xhr.status == 200){
					if(result.status){
						$('#idStamp').val('');
						$('#modelText').val('');
						$('#serialNumberText').val('');
						$('#editModal').modal('hide');
						openSuccessGritter('Success!', result.message);					
						fillResult();
						fillPlan();
					}
					else{
						audio_error.play();
						alert('Attempt to retrieve data failed');	
					}
				}
				else{
					audio_error.play();
					alert('Disconnected from sever');
				}
			});
		}
	}

	function updateStamp(){
		var id = $('#idStamp').val();
		var model = $('#modelText').val();
		var data = {
			id:id,
			model:model,
			originGroupCode:'041',
			processCode:'1',
		}
		$.post('{{ url("edit/stamp") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#idStamp').val('');
					$('#modelText').val('');
					$('#serialNumberText').val('');
					$('#editModal').modal('hide');
					openSuccessGritter('Success!', result.message);					
					fillResult();
					fillPlan();
				}
				else{
					audio_error.play();
					alert('Attempt to retrieve data failed');	
				}
			}
			else{
				audio_error.play();
				alert('Disconnected from sever');
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
			time: '3000'
		});
	}
	function category(id){
		$("#category").val(id);
	}


	function fillPlannew(){
		$.get('{{ url("fetch/fetchResultFlnew") }}', function(result, status, xhr){
			if(xhr.status = 200){
				if(result.status){
					$('#planTable').DataTable().destroy();
					$('#planTableBody').html("");
					var planData = '';
					var totalTarget = '';
					var totalSubassy = '';
					
					$.each(result.planData, function(key, value) {
						// alert(value.planh2 );
						
						totalTarget = value.plan;
						totalSubassy = (((totalTarget + (-value.debt)) - value.actual) - (value.total_return - value.total_ng)) ;
						var h2 = Math.round(value.planh2);
						if (totalSubassy < 0) {
							totalSubassy = 0;
							h2 = Math.round(value.planh2) - (value.total_stamp - value.actual);
						}
						if (h2 < 0) {
							h2 = 0;
						}
						planData += '<tr>';
						planData += '<td>'+ value.model3 +'</td>';
						planData += '<td>'+ value.debt +'</td>';						
						planData += '<td>'+ totalTarget +'</td>';
						planData += '<td>'+ value.actual +'</td>';
						planData += '<td>'+ value.total_return +'</td>';
						planData += '<td>'+ value.total_ng +'</td>';
						planData += '<td>'+ totalSubassy +'</td>';
						planData += '<td>'+ value.total_stamp +'</td>';
						planData += '<td>'+ h2 +'</td>';
						planData += '</tr>';
					});
					// $('#planTableBody').append(planData);
					$('#listModel').html("");
					$.unique(result.model.map(function (d) {
						$('#listModel').append('<button type="button" class="btn bg-olive btn-lg" style="margin-top: 3px; margin-left: 2px; margin-right: 2px; width: 19%; font-size: 1vw" id="'+d.model+'" onclick="model(id)">'+d.model+'</button>');
					}));
					$('#planTable').DataTable({
						'paging': false,
						'lengthChange': false,
						'searching': false,
						'ordering': false,
						'order': [],
						'info': false,
						'autoWidth': true,
						"footerCallback": function (tfoot, data, start, end, display) {
							var intVal = function ( i ) {
								return typeof i === 'string' ?
								i.replace(/[\$,]/g, '')*1 :
								typeof i === 'number' ?
								i : 0;
							};
							var api = this.api();
							
							var total_actual = api.column(7).data().reduce(function (a, b) {
								return intVal(a)+intVal(b);
							}, 0)
							$(api.column(7).footer()).html(total_actual.toLocaleString());

						},
						"columnDefs": [  {
							"targets": 6,
							"createdCell": function (td, cellData, rowData, row, col) {


								if ( parseInt(rowData[7]) < parseInt(rowData[6])  ) {
									$(td).css('background-color', 'RGB(255,204,255)')
								}
								else
								{
									$(td).css('background-color', 'RGB(204,255,255)')
								}
							}
						},
						{
							"targets": 8,
							"createdCell": function (td, cellData, rowData, row, col) {


								if ( parseInt(rowData[6]) >= 0  && parseInt(rowData[8]) > 0) {
									if (parseInt(rowData[6]) <= 0) {
										$(td).css('background-color', 'RGB(255,204,255)')
									}

									
								}
								else
								{
										// $(td).css('background-color', 'RGB(204,255,255)')
									}
								}
							}]
						});
				}
				else{
					audio_error.play();
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				audio_error.play();
				alert('Disconnected from server');
			}
		});
	}
</script>
@endsection