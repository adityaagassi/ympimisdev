@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		font-size: 16px;
	}
	#tableBodyList > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
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
		<div class="col-xs-5">
			<div class="box">
				<div class="box-body">
					<span style="font-size: 20px; font-weight: bold;">ITEM LIST:</span>
					<table class="table table-hover table-striped" id="tableList">
						<thead>
							<tr>
								<th style="width: 1%;">#</th>
								<th style="width: 2%;">Material Number</th>
								<th style="width: 5%;">Description</th>
								<th style="width: 1%;">Category</th>
							</tr>					
						</thead>
						<tbody id="tableBodyList">
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="col-xs-7">
			<div class="row">
				<input type="hidden" id="id_silver">
				<div class="col-xs-12">
					<span style="font-weight: bold; font-size: 16px;">Material Number:</span>
					<input type="text" id="material_number" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
				</div>
				<div class="col-xs-12">
					<span style="font-weight: bold; font-size: 16px;">Material Description:</span>
					<input type="text" id="material_description" style="width: 100%; height: 50px; font-size: 24px; text-align: center;" disabled>
				</div>
				<div class="col-xs-12">
					<span style="font-weight: bold; font-size: 16px;">Category:</span>
					<input type="text" id="category" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
				</div>
				<div class="col-xs-12">
					<span style="font-weight: bold; font-size: 16px;">PI Count:</span>
				</div>	
				<div class="col-xs-12">
					<input id="countMaterial" style="font-size: 50px; height: 60px; text-align: center;" type="text" class="form-control" value="0" disabled>
				</div>
				<div class="col-xs-12">
					<span style="font-weight: bold; font-size: 16px;">Add Count:</span>
				</div>
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-7">
							<div class="input-group">
								<div class="input-group-btn">
									<button type="button" class="btn btn-danger" style="font-size: 35px; height: 60px; text-align: center;"><span class="fa fa-minus" onclick="minusCount()"></span></button>
								</div>
								<!-- /btn-group -->
								<input id="addCount" style="font-size: 50px; height: 60px; text-align: center;" type="text" class="form-control" value="0">
								<div class="input-group-btn">
									<button type="button" class="btn btn-success" style="font-size: 35px; height: 60px; text-align: center;"><span class="fa fa-plus" onclick="plusCount()"></span></button>
								</div>
							</div>
						</div>
						<div class="col-xs-5" style="padding-bottom: 10px;">
							<button class="btn btn-primary" onclick="confirmCount()" style="font-size: 40px; width: 100%; font-weight: bold; padding: 0;">
								CONFIRM
							</button>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<div class="box">
						<div class="box-body">
							<span style="font-size: 20px; font-weight: bold;">RESUME ({{ date('d-M-Y') }})</span>
							<table class="table table-hover table-striped table-bordered" id="tableResume">
								<thead>
									<tr>
										<th style="width: 1%;">#</th>
										<th style="width: 2%;">Material Number</th>
										<th style="width: 5%;">Description</th>
										<th style="width: 1%;">Category</th>
										<th style="width: 1%; text-align:right;">PI Count</th>
										<th style="width: 1%; text-align:right;">Final Count</th>
									</tr>
								</thead>
								<tbody id="tableBodyResume">
								</tbody>
							</table>
						</div>
					</div>
					<button class="btn btn-danger" onclick="finalConfirm()" style="font-size: 40px; width: 100%; font-weight: bold; padding: 0;">
						FINAL CONFIRMATION
					</button>
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
		itemList();
		itemResume();
	});

	function plusCount(){
		$('#addCount').val(parseInt($('#addCount').val())+1);
	}

	function minusCount(){
		$('#addCount').val(parseInt($('#addCount').val())-1);
	}

	function finalConfirm(){
		$("#loading").show();
		var data = {
			location : $("#location").val()
		}
		if(confirm("Data hari ini akan dihitung oleh sistem.\nData tidak akan dapat dikembalikan.")){
			$.post('{{ url("input/stocktaking/silver_final") }}', data, function(result, status, xhr){
				if(result.status){
					itemResume();					
					openSuccessGritter('Success', result.message);
					$("#loading").hide();
				}
				else{
					openErrorGritter('Error!', result.message);
					$("#loading").hide();
				}
			});
		}
	}

	function itemResume(){
		var data = {
			location : $("#location").val()
		}

		$.get('{{ url("fetch/stocktaking/silver_resume") }}', data, function(result, status, xhr){
			$('#tableBodyResume').html("");
			var tableData = "";
			var count = 1;
			$.each(result.lists, function(key, value) {
				tableData += '<tr>';
				tableData += '<td>'+ count +'</td>';
				tableData += '<td>'+ value.material_number +'</td>';
				tableData += '<td>'+ value.material_description +'</td>';
				if(value.category == 'SINGLE'){
					tableData += '<td style="background-color: rgb(250,250,210); font-weight: bold;">'+ value.category +'</td>';
				}
				else{
					tableData += '<td style="background-color: rgb(135,206,250); font-weight: bold;">'+ value.category +'</td>';
				}
				tableData += '<td style="text-align: right; font-weight: bold;">'+ value.quantity_check +'</td>';
				tableData += '<td style="text-align: right; font-weight: bold;">'+ value.quantity_final +'</td>';
				tableData += '</tr>';

				count += 1;
			});
			$('#tableBodyResume').append(tableData);
		});
	}

	function confirmCount(){
		$("#loading").show();
		var data = {
			id : $("#id_silver").val(),
			count : parseFloat($("#countMaterial").val())+parseFloat($("#addCount").val()),
		}
		$.post('{{ url("input/stocktaking/silver_count") }}', data, function(result, status, xhr){
			if(result.status){
				$('#id_silver').val("");
				$('#material_number').val("");
				$('#material_description').val("");
				$('#category').val("");
				$('#countMaterial').val("0");
				$('#addCount').val("0");
				itemResume();
				openSuccessGritter('Success', result.message);
				$("#loading").hide();
			}
			else{
				openErrorGritter('Error!', result.message);
				$('#countMaterial').val("0");
				$('#addCount').val("0");
				$("#loading").hide();
			}
		});
	}

	function fetchCount(id){
		var data = {
			id : id,
		}
		$.get('{{ url("fetch/stocktaking/silver_count") }}', data, function(result, status, xhr){
			if(result.status){
				$('#id_silver').val(result.count.id);
				$('#material_number').val(result.count.material_number);
				$('#material_description').val(result.count.material_description);
				$('#category').val(result.count.category);
				if(result.count.category == 'SINGLE'){
					$('#category').css('background-color', 'rgb(250,250,210)');
				}
				else{
					$('#category').css('background-color', 'rgb(135,206,250)');
				}
				$('#countMaterial').val(result.count.quantity_check);
				$('#addCount').val("0");
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function itemList(){
		var data = {
			location : $("#location").val()
		}

		$.get('{{ url("fetch/stocktaking/silver_list") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableList').DataTable().clear();
				$('#tableList').DataTable().destroy();
				$('#tableBodyList').html("");
				var tableData = "";
				var count = 1;
				$.each(result.lists, function(key, value) {
					tableData += '<tr onclick="fetchCount(\''+value.id+'\')">';
					tableData += '<td>'+ count +'</td>';
					tableData += '<td>'+ value.material_number +'</td>';
					tableData += '<td>'+ value.material_description +'</td>';
					if(value.category == 'SINGLE'){
						tableData += '<td style="background-color: rgb(250,250,210); font-size:20px; font-weight: bold; text-align: center;">'+ value.category +'</td>';
					}
					else{
						tableData += '<td style="background-color: rgb(135,206,250); font-size:20px; font-weight: bold; text-align: center;">'+ value.category +'</td>';
					}
					tableData += '</tr>';

					count += 1;
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
							// dom: {
							// 	button: {
							// 		tag:'button',
							// 		className:''
							// 	}
							// },
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
						'pageLength': 20,
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
			else{
				alert('Attempt to retrieve data failed');
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