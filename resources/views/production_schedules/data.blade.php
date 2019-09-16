@extends('layouts.master')
@section('stylesheets')
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	input {
		line-height: 24px;
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
	#loading, #error { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		{{$title}} <span class="text-purple">{{$title_jp}}</span>
	</h1>
	<ol class="breadcrumb" id="last_update"></ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-3">
							<div class="form-group">
								<label>Date From</label>
								<input class="form-control datepicker" name="dateFrom" id='dateFrom' placeholder="Select Date From" style="width: 100%;">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Date To</label>
								<input class="form-control datepicker" name="dateTo" id='dateTo' placeholder="Select Date To" style="width: 100%;">
							</div>
						</div>
					</div>
					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-6">
							<div class="form-group">
								<select class="form-control select2" multiple="multiple" data-placeholder="Select Origin Group" name="origin_group" id="origin_group" style="width: 100%;">
									<option></option>
									@foreach($origin_groups as $origin_group)
									<option value="{{ $origin_group->origin_group_code }}">{{ $origin_group->origin_group_code }} - {{ $origin_group->origin_group_name }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-6">
							<div class="form-group">
								<select class="form-control select2" multiple="multiple" data-placeholder="Select Model" id="model" style="width: 100%;">
									<option></option>
									@foreach($models as $model)
									<option value="{{ $model->model }}">{{ $model->model }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-6">
							<div class="form-group">
								<select class="form-control select2" multiple="multiple" id="material_number" style="width: 100%;" data-placeholder="Choose a Material Number..." required>
									<option value=""></option>
									@foreach($materials as $material)
									<option value="{{ $material->material_number }}">{{ $material->material_number }}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group pull-right">
								<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
								<button id="search" onClick="fillTable()" class="btn btn-primary"><span class="fa fa-search"></span> Search</button>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<table id="productionScheduleTable" class="table table-bordered table-striped table-hover" style="width: 100%;">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%;">Date</th>
										<th style="width: 2%;">Material Number</th>
										<th style="width: 10%;">Material Description</th>
										<th style="width: 1%;">Daily Plan</th>
										<th style="width: 1%;">Plan Acc<br>(A)</th>
										<th style="width: 1%;">Packing Acc<br>(B)</th>
										<th style="width: 1%;">Diff<br>(B-A)</th>
										<th style="width: 1%;">Actual Delivery Acc<br>(C)</th>
										<th style="width: 1%;">Diff<br>(C-A)</th>
									</tr>
								</thead>
								<tbody id="tableBody">
								</tbody>
								<!-- <tfoot style="background-color: RGB(252, 248, 227);">
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
								</tfoot> -->
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

@endsection


@section('scripts')
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
{{-- <script src="{{ url("js/pdfmake.min.js")}}"></script> --}}
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('.select2').select2();

		$('.datepicker').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
		})

		// fillTable();
	});

	function clearConfirmation(){
		location.reload(true);
	}

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}

	function getActualFullDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
	}

	function fillTable(){
		if ($('#dateTo').val() != "" && $('#dateFrom').val() != "") {
			var tmpTo = $('#dateTo').val().split("-")[1];
			var tmpFrom = $('#dateFrom').val().split("-")[1];

			if ($('#dateFrom').val().split("-")[2] > $('#dateTo').val().split("-")[2]) {
				alert("Date Range Invalid");
				return false;
			}

			if (tmpTo != tmpFrom) {
				alert("Date From and Date To must on same month");
				return false;
			} else {
				dateTo = $('#dateTo').val();
				dateFrom = $('#dateFrom').val();
			}
		} else if($('#dateTo').val() == "" || $('#dateFrom').val() == ""){
			alert("Date From and Date To must be filled");
			return false;
		} else {
			alert("Date From and Date To must be filled");
			return false;
		}

		if ($('#origin_group').val() != "") {
			originGroupCode = $('#origin_group').val();
		} else {
			originGroupCode = "";
		}

		if ($('#material_number').val() != "") {
			material = $('#material_number').val();
		} else {
			material = "";
		}

		if ($('#model').val() != "") {
			model = $('#model').val();
		} else {
			model = "";
		}

		var data = {
			dateFrom:dateFrom,
			dateTo:dateTo
		}

		$.get('{{ url("fetch/fg_production_schedule") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					$('#last_update').html('<b>Last Updated: '+ getActualFullDate() +'</b>');
					$('#productionScheduleTable').DataTable().clear();
					$('#productionScheduleTable').DataTable().destroy();
					$('#tableBody').html("");

					var tableData = '';
					var qty = 0;
					var matnum = "";
					var pkg = 0;
					var deliv = 0;
					var arr_deliv = [];
					var arr_pkg = [];
					var pkg_tmp = 0;
					var qty_tmp = 0;
					var deliv_tmp = 0;
					var arr_datas = [];					

					$.each(result.production_sch, function(key, value) {
						var deliv_s = 0;
						var pkg_s = 0;
						var stat = 0;
						var stat_d = 0;
						var stat_pkg = 0;
						arr_pkg = [];
						arr_deliv = [];

						if (typeof result.production_sch[key+1] !== 'undefined') {
							if (result.production_sch[key].material_number == result.production_sch[key+1].material_number) {
								qty += value.quantity;
								qty_tmp = qty;
							} else {
								qty += value.quantity;
								qty_tmp = qty;
								qty = 0;
							}
						} else {
							qty = value.quantity;
							qty_tmp = qty;
						}

						$.each(result.packing, function(key2, value2) {
							if (typeof result.packing[key2+1] !== 'undefined') {
								if (result.packing[key2].material_number == result.packing[key2+1].material_number) {
									pkg += value2.packing;
									arr_pkg.push([value2.date, value2.material_number, pkg]);
								} else {
									pkg += value2.packing;
									arr_pkg.push([value2.date, value2.material_number, pkg]);
									pkg = 0;
								}
							} else {
								if (result.packing[key2].material_number == result.packing[key2-1].material_number) {
									pkg += value2.packing;
									arr_pkg.push([value2.date, value2.material_number, pkg]);
								} else {
									pkg = value2.packing;
									arr_pkg.push([value2.date, value2.material_number, pkg]);
								}
							}
						})

						pkg = 0;

						for (var i = 0; i < arr_pkg.length; i++) {
							if (arr_pkg[i][1] == value.material_number && arr_pkg[i][0] == value.due_date) {
								pkg_s = arr_pkg[i][2];
								pkg_tmp = pkg_s;
								stat_pkg = 1;
							} else {
								if (stat_pkg == 0) {
									if(arr_pkg.indexOf(value.material_number) !== -1){
										pkg_s = pkg_tmp;
									} else {
										pkg_s = 0;
									}
								}
							}
						}

						
						$.each(result.deliv, function(key3, value3) {
							if (typeof result.deliv[key3+1] !== 'undefined') {
								if (result.deliv[key3].material_number == result.deliv[key3+1].material_number) {
									deliv += value3.deliv;
									arr_deliv.push([value3.date, value3.material_number, deliv]);
								} else {
									deliv += value3.deliv;
									arr_deliv.push([value3.date, value3.material_number, deliv]);
									deliv = 0;
								}
							} else {
								if (result.deliv[key3].material_number == result.deliv[key3-1].material_number) {
									deliv += value3.deliv;
									arr_deliv.push([value3.date, value3.material_number, deliv]);
								} else {
									deliv = value3.deliv;
									arr_deliv.push([value3.date, value3.material_number, deliv]);
								}
							}
						})

						deliv = 0;

						for (var i = 0; i < arr_deliv.length; i++) {
							if (arr_deliv[i][1] == value.material_number && arr_deliv[i][0] == value.due_date) {
								deliv_s = arr_deliv[i][2];
								deliv_tmp = deliv_s;
								stat_d = 1;
							} else {
								if (stat_d == 0) {
									if(arr_deliv.indexOf(value.material_number) !== -1){
										deliv_s = deliv_tmp;
									} else {
										deliv_s = 0;
									}
								}
							}
						}

						arr_datas.push({due_date: value.due_date, material_number: value.material_number, mat_desc: value.material_description, qty: qty_tmp, pkg: pkg_s, diff1 : (pkg_s - qty_tmp), deliv: deliv_s, diff2: (deliv_s - qty_tmp), model: value.model, origin_group: value.origin_group_code, plan_act: value.quantity});
					});

					console.table(arr_datas);
					var number = 0;

					$.each(arr_datas, function(key5, value5) {
						if (typeof arr_datas[key5-1] !== 'undefined') {
							if (arr_datas[key5-1].material_number != arr_datas[key5].material_number) {
								number += 1;
							}
						} else {
							number = 1;
						}

						if (value5.due_date.split("-")[2] >= dateFrom.split("-")[2]) {
							//JIKA FILTER ORIGIN GROUP
							if (originGroupCode != "") {
								if (originGroupCode.indexOf(value5.origin_group) !== -1) {
									status1 = true;
								} else {
									status1 = false;
								}
							} else {
								status1 = true;
							}

							//JIKA FILTER MATERIAL
							if (material != "") {
								if (material.indexOf(value5.material_number) !== -1) {
									status2 = true;
								} else {
									status2 = false;
								} 
							} else {
								status2 = true;
							}

							//JIKA FILTER MODEL
							if (model != "") {
								if (model.indexOf(value5.model) !== -1) {
									status3 = true;
								} else {
									status3 = false;
								} 
							} else {
								status3 = true;
							}

							if (status1 == true && status2 == true && status3 == true) {

								if (number %2 === 0) {
									color = 'style = "background-color:#fffcb7"';
								} else {
									color = 'style = "background-color:#ffd8b7"';
								}

								tableData += '<tr '+color+'>';
								tableData += '<td>'+ value5.due_date +'</td>';
								tableData += '<td >'+ value5.material_number +'</td>';
								tableData += '<td >'+ value5.mat_desc +'</td>';
								tableData += '<td>'+ value5.plan_act +'</td>';
								tableData += '<td>'+ value5.qty +'</td>';
								tableData += '<td>'+ value5.pkg +'</td>';

								if ( value5.diff1 <  0 ) {
									warna1 = 'style = "background-color:RGB(255,204,255)"';
								}
								else
								{
									warna1 = 'style = "background-color:RGB(204,255,255)"';
								}

								tableData += '<td '+warna1+'>'+ value5.diff1 +'</td>';
								tableData += '<td>'+ value5.deliv +'</td>';

								if ( value5.diff2 <  0 ) {
									warna2 = 'style = "background-color:RGB(255,204,255)"';
								}
								else
								{
									warna2 = 'style = "background-color:RGB(204,255,255)"';
								}

								tableData += '<td '+warna2+'>'+ value5.diff2 +'</td>';
								tableData += '</tr>';
							}
						}
					})

					$('#tableBody').append(tableData);
					$('#productionScheduleTable').DataTable({
						'dom': 'Bfrtip',
						'responsive': true,
						'lengthMenu': [
						[ 10, 25, 50, -1 ],
						[ '10 rows', '25 rows', '50 rows', 'Show all' ]
						],
						"pageLength": 25,
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
						'searching': true,
						'ordering': true,
						'order': [],
						'info': true,
						'autoWidth': true,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false
					});
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				alert('Disconnected from server');
			}
		});

}


</script>
@endsection