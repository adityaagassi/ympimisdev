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
		Print Label Saxophone<span class="text-purple"> サックスラベル印刷 </span>
		<small>Serial Number <span class="text-purple"> 製番</span></small>
	</h1>
	<ol class="breadcrumb">
		<li>
			<button href="javascript:void(0)" class="btn btn-info btn-sm" data-toggle="modal" data-target="#reprintModal">
				<i class="fa fa-print"></i>&nbsp;&nbsp;Reprint
			</button>
			<a href="{{ url("/stamp/resumes_sx") }}" class="btn btn-primary btn-sm" style="color:white"><i class="fa fa-calendar-check-o "></i>&nbsp;Record</a>
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
						
{{-- 
						<div class="col-xs-2">
							<center>
								<span style="font-size: 20px;">Total Production Alto:</span>
							</center>
							<table id="planTable2" name="planTable2" class="table table-bordered table-hover table-striped">
								<thead style="background-color: rgba(126,86,134,.7);">
									<th>Model</th>
									<th>Production</th>

								</thead>
								<tbody id="planTableBody2">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
									<th>Total</th>
									<th></th>
									
								</tfoot>
							</table>
						</div> --}}
						{{-- <div class="col-xs-2">
							<center>
								<span style="font-size: 20px;">Total Production Tenor:</span>
							</center>
							<table id="planTable" name="planTable" class="table table-bordered table-hover table-striped">
								<thead style="background-color: rgba(126,86,134,.7);">
									<th>Model</th>
									<th>Production</th>

								</thead>
								<tbody id="planTableBody">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
									<th>Total</th>
									<th></th>
									
								</tfoot>
							</table>							
						</div> --}}
						
						<div class="col-xs-4">
							<center>
								<span style="font-size: 20px;">Total Production :</span>
							</center>
							<table id="planTable" name="planTable" class="table table-bordered table-hover table-striped">
								<thead style="background-color: rgba(126,86,134,.7);">
									<th>Model</th>
									<th>Plan</th>
									<th>Actual</th>
									<th>Diff</th>
								</thead>
								<tbody id="planTableBody">
								</tbody>
								<tfoot>
									<th>Total</th>
									<th></th>
									<th></th>
									<th></th>						
									
								</tfoot>
							</table>							
						</div>
						<div class="col-xs-3">
							<center>
								<span style="font-size: 24px">Last Print:</span><br>
								<input id="lastCounter" type="text" style="font-weight: bold; background-color: rgb(255,255,204); width: 100%; text-align: center; font-size: 4vw" disabled>
								<input id="model" type="text" style="font-weight: bold; background-color: rgb(255,127,80);; width: 100%; text-align: center; font-size: 2vw" value="YTS" disabled>

								<span style="font-size: 24px">Input SN:</span><br>
								<input id="sn" type="text" style="font-weight: bold;  width: 100%; text-align: center; font-size: 4vw"  onkeyup="getserial()">
								<input id="snmodel" type="text" style="font-weight: bold; background-color: rgb(255,127,80);; width: 100%; text-align: center; font-size: 2vw" value="Not Found" disabled>
								<span style="font-size: 24px">Select Model</span><b id="japan" style="font-size: 24px"></b><br>
								<center>
									<div id="listModel">
									</div><br>
									<div id="listModel2">
									</div>
								</center>
								<input type="text" id="japan2" hidden="">
								<input type="text" id="gmc" hidden="">
							{{-- 	<button id="btnprint"  style="font-weight: bold; width: 100%; text-align: center; font-size: 4vw;" class="btn btn-primary" onclick="print('update');" disabled><i class="fa fa-print"></i>&nbsp;&nbsp;Print</button>
								<button id="btnprint2"  style="font-weight: bold; width: 100%; text-align: center; font-size: 4vw;display: none;" class="btn btn-info" onclick="print('update');" disabled><i class="fa fa-print"></i>&nbsp;Reprint</button>
								<button id="btnprintmodal"  style="font-weight: bold; width: 100%; text-align: center; font-size: 4vw; display: none;" class="btn btn-warning" onclick="modalshow();" disabled>Show Model</button> --}}

							</center>
						</div>
						<div class="col-xs-5">
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
	<div class="modal-dialog ">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Edit Stamp</h4>
			</div>
			<div class="modal-body">

				<input type="text" style="font-weight: bold; background-color: rgb(255,255,204);; width: 100%; text-align: center; font-size: 4vw"  name="serialNumberText" id="serialNumberText"  disabled><br>
				<input type="text" style="font-weight: bold; background-color: rgb(255,127,80);; width: 100%; text-align: center; font-size: 2vw"  name="modelText" id="modelText"  disabled>
				<input type="hidden"  name="idStamp" id="idStamp"><br><br>
				<center>
					<div id="listModel3">
					</div><br>
					<div id="listModel4">
					</div>
				</center>
			</div>
			<div class="modal-footer">
				{{-- <button type="button" onclick="destroyStamp()" class="btn btn-danger pull-left">Delete</button> --}}
				<button type="button" onclick="updateStamp()" class="btn btn-primary">Confirm</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modela">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Choose a Model</h4>
				</div>
				<div class="modal-body">
					<input id="modelmoddal" type="text" style="font-weight: bold; background-color: rgb(255,127,80);; width: 100%; text-align: center; font-size: 2vw" value="Model" disabled><br><br>
					<center>
						<div id="listModel2">
						</div><br>
						<div id="listModel">
						</div>
					</center>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" onclick="print('new')">Print</button>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>

	<div class="modal fade" id="modelreprint2">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Choose a Model</h4>
					</div>
					<div class="modal-body">
						<input type="text" style="font-weight: bold; background-color: rgb(255,255,204);; width: 100%; text-align: center; font-size: 4vw"  name="serialNumberText" id="serialNumberText2"  disabled><br>
						<input type="text" style="font-weight: bold; background-color: rgb(255,127,80);; width: 100%; text-align: center; font-size: 2vw"  name="modelText" id="modelText2"  disabled>
						<BR><BR>
						<center>
							<button class="btn btn-lg btn-success" onclick="repintBesar();">Label Besar</button>
							<button class="btn btn-lg btn-success" onclick="repintKecil();">Label Kecil</button>
							<button class="btn btn-lg btn-primary" onclick="reprintAll();">Label Besar + Kecil</button>
						</center>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
						{{-- <button type="button" class="btn btn-primary" onclick="print('new')">Print</button> --}}


					</div>
				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>

		<div class="modal modal-default fade" id="reprintModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="titleModal">Reprint Stamp</h4>
					</div>
					<form class="form-horizontal" role="form" method="post" action="{{url('reprint/stamp2')}}">
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
				fillPlan3();
				 // fillPlan2();
				 // fillPlan();
				fillResult();

			});

			var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

			function insert(num){
				document.form.textview.value = document.form.textview.value+num;
			}

			function fillPlan3(){
				$.get('{{ url("fetch/fetch_plan_labelsax") }}', function(result, status, xhr){
					console.log(status);
					console.log(result);
					console.log(xhr);
					if(xhr.status = 200){
						if(result.status){
							$('#planTable').DataTable().destroy();
							$('#planTableBody').html("");
							var tableData = '';
							$.each(result.tableData, function(key, value) {
								var diff = '';
								diff = value.act-(value.plan-(value.debt));
								tableData += '<tr>';
								tableData += '<td style="width: 49%">'+ value.model +'</td>';			
								tableData += '<td style="width: 12%">'+ (value.plan-(value.debt)) +'</td>';
								tableData += '<td style="width: 12%">'+ value.act +'</td>';
								tableData += '<td style="width: 12%">'+ diff +'</td>';
								tableData += '</tr>';
							});
							$('#planTableBody').append(tableData);
							$('#planTable').DataTable({
								
								"paging": false,
								'searching': false,
								'order':[[1, "asc"]],
								'info': false,
								"footerCallback": function (tfoot, data, start, end, display) {
									var intVal = function ( i ) {
										return typeof i === 'string' ?
										i.replace(/[\$,]/g, '')*1 :
										typeof i === 'number' ?
										i : 0;
									};
									var api = this.api();

									var total_plan = api.column(1).data().reduce(function (a, b) {
										return intVal(a)+intVal(b);
									}, 0)
									$(api.column(1).footer()).html(total_plan.toLocaleString());

									var total_act = api.column(2).data().reduce(function (a, b) {
										return intVal(a)+intVal(b);
									}, 0)
									$(api.column(2).footer()).html(total_act.toLocaleString());



									var total_diff = api.column(3).data().reduce(function (a, b) {
										return intVal(a)+intVal(b);
									}, 0)
									$(api.column(3).footer()).html(total_diff.toLocaleString());

								},
								"columnDefs": [{
									"targets": 3,
									"createdCell": function (td, cellData, rowData, row, col) {
										if ( cellData <  3 ) {
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

			function fillPlan(){
				$.get('{{ url("stamp/fetchStampPlansax3") }}'+'/YTS', function(result, status, xhr){
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

								planData += '<td>'+ value.actual +'</td>';

								planData += '</tr>';
							});
							$('#planTableBody').append(planData);

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

								},
								"columnDefs": [ {
									"targets": 1,
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

			function fillPlan2(){
				$.get('{{ url("stamp/fetchStampPlansax3") }}'+'/YAS', function(result, status, xhr){
					console.log(status);
					console.log(result);
					console.log(xhr);
					if(xhr.status = 200){
						if(result.status){
							$('#planTable2').DataTable().destroy();
							$('#planTableBody2').html("");
							var planData = '';
							$.each(result.planData, function(key, value) {
								planData += '<tr>';
								planData += '<td>'+ value.model +'</td>';

								planData += '<td>'+ value.actual +'</td>';

								planData += '</tr>';
							});
							$('#planTableBody2').append(planData);

							$('#planTable2').DataTable({
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

								},
								"columnDefs": [ {
									"targets": 1,
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


			function fillResult(){
				$.get('{{ url("stamp/fetchResult") }}'+'/YTS3', function(result, status, xhr){
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


			function getmodels() {
				var sn = $("#serialNumberText").val();
				var data ={
					sn:sn,
					log:'3',
				};
				$.get('{{ url("index/getModel") }}', data, function(result, status, xhr){
					console.log(status);
					console.log(result);
					console.log(xhr);
					if(xhr.status == 200){
						if(result.status){

							$('#listModel3').html("");
							var planData = '';
							$.each(result.planData, function(key, value) {
									if (value.remark=="J") {
									planData += '<button type="button" class="btn bg-blue btn-lg" style="margin-top: 2px; margin-left: 1px; margin-right: 1px; width: 32%; font-size: 0.8vw" id="'+value.material_number+'" name="'+value.material_description+'" onclick="model(name,id,\'J\');japan(\'(Japan)\')">'+value.material_description+'<br>Japan'+'</button>';
								}
								// else if(value.remark=="S") {
								// 	planData += '<button type="button" class="btn bg-olive btn-lg" style="margin-top: 2px; margin-left: 1px; margin-right: 1px; width: 32%; font-size: 0.8vw" id="'+value.material_number+'" name="'+value.material_description+'" onclick="model(name,id,\'NJ\');japan(\'(Silver)\')">'+value.material_description+'<br>Silver'+'</button>';	
								// }else {
								// 	planData += '<button type="button" class="btn btn-warning btn-lg" style="margin-top: 2px; margin-left: 1px; margin-right: 1px; width: 32%; font-size: 0.8vw" id="'+value.material_number+'" name="'+value.material_description+'" onclick="model(name,id,\'NJ\');japan(\'(Lacquering)\')">'+value.material_description+'<br>Lacquering'+'</button>';	
								// }

								else {
									planData += '<button type="button" class="btn btn-olive btn-lg" style="margin-top: 2px; margin-left: 1px; margin-right: 1px; width: 32%; font-size: 0.8vw" id="'+value.material_number+'" name="'+value.material_description+'" onclick="model(name,id,\'NJ\');japan(\'()\')">'+value.material_description+'<br>'+'</button>';	
								}						
							});
							$('#listModel3').append(planData);	
						}
					}
				});
			}

	// reprint 

	function getserial() {
		var sn = $("#sn").val();
		var data ={
			sn:sn,
			log:'2',
			
		};
		var data2 ={
			sn2:sn,
			code:'2',
			origin:'043',
		}
		if (sn.length == 8) {
			// $("#snmodel").val('Found');
			$.get('{{ url("index/getModel") }}', data, function(result, status, xhr){
				console.log(status);
				console.log(result);
				console.log(xhr);
				if(xhr.status == 200){
					if(result.status){
						$('#listModel').html("");
						$('#listModel2').html("");
						var planData = '';
						$.each(result.planData, function(key, value) {
								if (value.remark=="J") {
								planData += '<button type="button" class="btn bg-blue btn-lg" style="margin-top: 2px; margin-left: 1px; margin-right: 1px; width: 32%; font-size: 0.8vw" id="'+value.material_number+'" name="'+value.material_description+'" onclick="model(name,id,\'J\');japan(\'(Japan)\')">'+value.material_description+'<br>Japan'+'</button>';
							}
							// else if(value.remark=="S"){
							// 	planData += '<button type="button" class="btn bg-olive btn-lg" style="margin-top: 2px; margin-left: 1px; margin-right: 1px; width: 32%; font-size: 0.8vw" id="'+value.material_number+'" name="'+value.material_description+'" onclick="model(name,id,\'NJ\');japan(\'(Silver)\')">'+value.material_description+'<br>Silver'+'</button>';	
							// }else {
							// 	planData += '<button type="button" class="btn btn-warning btn-lg" style="margin-top: 2px; margin-left: 1px; margin-right: 1px; width: 32%; font-size: 0.8vw" id="'+value.material_number+'" name="'+value.material_description+'" onclick="model(name,id,\'NJ\');japan(\'(Lacquering)\')">'+value.material_description+'<br>Lacquering'+'</button>';	
							// }	

							else {
									planData += '<button type="button" class="btn bg-olive btn-lg" style="margin-top: 2px; margin-left: 1px; margin-right: 1px; width: 32%; font-size: 0.8vw" id="'+value.material_number+'" name="'+value.material_description+'" onclick="model(name,id,\'NJ\');japan(\'\')">'+value.material_description+'<br>'+'</button>';	
								}					
						});						
						$('#listModel').append(planData);
						$('#listModel2').append('<button id="btnprint"  style="font-weight: bold; width: 100%; text-align: center; font-size: 4vw;" class="btn btn-primary" onclick="print(\'update\');" disabled><i class="fa fa-print"></i>&nbsp;&nbsp;Print</button><button id="btnprint2"  style="font-weight: bold; width: 100%; text-align: center; font-size: 4vw;display: none;" class="btn btn-info" onclick="print(\'reupdate\');" disabled><i class="fa fa-print"></i>&nbsp;Reprint</button>');



						//fill serial

						$.get('{{ url("index/get_sn2") }}', data2, function(result, status, xhr){
							console.log(status);
							console.log(result);
							console.log(xhr);
							if(xhr.status == 200){
								if(result.status){
						// alert(result.message);
						if(result.message =="2"){
							$('#btnprint2').css({'display':'block'});
							$('#btnprint').css({'display':'none'});	
							$('#btnprint2').prop('disabled',false);						
						}else{
							$('#btnprint').css({'display':'block'});	
							$('#btnprint2').css({'display':'none'});
						}
						//modal	
						$('#snmodel').val(result.model);
						$('#btnprint').prop('disabled',false);	
						$('#btnprintmodal').css({'display':'none'});

					}
					else{
						$('#snmodel').val('Not Found');
						$('#btnprint').prop('disabled',true);
						//modal
						$('#btnprint').css({'display':'none'});
						$('#btnprintmodal').css({'display':'block'});
						$('#btnprintmodal').prop('disabled',false);
						$('#btnprint2').css({'display':'none'});

					}
				}
				else{
					alert("Disconnected from server");
				}
			});

						//end serial 


					}
					else{
						audio_error.play();
						alert('Attempt to retrieve data failed');

					}
				}
				else{
					alert("Disconnected from server");
				}
			});
		}else{
			$("#snmodel").val('Not Found');
			$('#btnprint').prop('disabled',true);
			$('#btnprintmodal').prop('disabled',true);
			$('#btnprint2').prop('disabled',true);	
		}
	}



	function print(status){	
		$("#modela").modal('hide');
		var	status = status;
		var sn = $("#sn").val();
		var snmodel = $("#snmodel").val();
		var jpn = $("#japan2").val();
		var gmc = $("#gmc").val();
		// alert(status);
		var data ={
			sn:sn,
			code:'2',
			origin:'043',
			status:status,
			snmodel:snmodel,
			jpn:jpn,
		}
		if (status =="update") {
			$.post('{{ url("index/print_sax2") }}', data, function(result, status, xhr){
				console.log(status);
				console.log(result);
				console.log(xhr);
				if(xhr.status == 200){
					if(result.status){
						openSuccessGritter('Success', result.message);
						$("#lastCounter").val(sn);
						$("#model").val(snmodel);						
						$("#sn").val('');
						$("#snmodel").val('');
						$('#sn').focus();
						fillResult();
						// fillPlan();
						// fillPlan2();
						fillPlan3();
						$('#btnprint').prop('disabled',true);
						$('#btnprintmodal').prop('disabled',true);
						$('#btnprint2').prop('disabled',true);

						window.open('{{ url("index/label_besar") }}'+'/'+sn+'/'+gmc+'/'+jpn, '_blank');
						// window.open('{{ url("index/label_des") }}'+'/'+sn, '_blank');

					}
					else{
						audio_error.play();
						alert(result.message);
						$("#sn").val('');
						$("#snmodel").val('');
						$('#sn').focus();
						$('#btnprint').prop('disabled',true);
						$('#btnprintmodal').prop('disabled',true);
						$('#btnprint2').prop('disabled',true);
					}
				}
				else{
					audio_error.play();
					alert('Disconnected from server');
				}
			});
		}else{
			// alert("as")
			$('#modelText2').val($('#snmodel').val());
			$('#serialNumberText2').val($('#sn').val());
			$('#modelreprint2').modal('show');
		}

	}

	function modalshow() {
		$("#modela").modal('show');
		
	}

	function model(id,name,japans){
		$('#modelmoddal').val(id);
		$('#snmodel').val(id);
		$('#modelText').val(id);
		$('#gmc').val(name);
		$('#japan2').val(japans);
	}

	function editStamp(id){
		var data = {
			id:id
		}
		$.get('{{ url("edit/stampLabel") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#modelText').val(result.stamp.model);
					$('#serialNumberText').val(result.stamp.serial_number);
					$('#idStamp').val(result.stamp.id);
					getmodels();
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

	function updateStamp(){
		var id = $('#serialNumberText').val();
		var model = $('#modelText').val();
		var jpn = $("#japan2").val();
		alert(jpn);
		var data = {
			id:id,
			model:model,
			originGroupCode:'043',
			processCode:'2',
			jpn:jpn,
		}
		$.post('{{ url("edit/stampLabel") }}', data, function(result, status, xhr){
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
					
					// fillPlan();
					// fillPlan2();

					fillPlan3();
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
		var id = $('#serialNumberText').val();
		var model = $('#modelText').val();
		var data = {
			id:id,
			model:model,
			originGroupCode:'043',
			processCode:'2',
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
						
						// fillPlan();
						// fillPlan2();
						fillPlan3();
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

	function japan(id) {
		$('#japan').text(id)
	}

	function reprintAll() {
		var sn = $('#sn').val();
		var data = {
			sn:sn,
		}

		$.get('{{ url("index/getdatareprintAll") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					// alert(result.reprint[0].material_number)
					
					window.open('{{ url("index/label_besar") }}'+'/'+result.reprint[0].serial_number+'/'+result.reprint[0].material_number+'/'+result.reprint[0].status+'R', '_blank');
					
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

	function repintKecil() {
		var sn = $('#sn').val();
		window.open('{{ url("index/label_kecil") }}'+'/'+sn+'/RP', '_blank');
	}

	function repintBesar() {
			var sn = $('#sn').val();
		var data = {
			sn:sn,
		}

		$.get('{{ url("index/getdatareprintAll") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					// alert(result.reprint[0].material_number)
					
					window.open('{{ url("index/label_besar") }}'+'/'+result.reprint[0].serial_number+'/'+result.reprint[0].material_number+'/'+result.reprint[0].status+'RB', '_blank');
					
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

</script>
@endsection