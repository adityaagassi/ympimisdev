@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
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
		padding-top: 0;
		padding-bottom: 0;
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
		{{$title}}<small><span class="text-purple">{{$title_jp}}</span></small>
		<!-- <small> <span class="text-purple">??</span></small> -->
	</h1>
	<ol class="breadcrumb">
	</ol>
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
		<div class="alert alert-warning alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4> Warning!</h4>
			{{ session('error') }}
		</div>   
	@endif
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Updating, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-xs-12">
						<div class="col-xs-3">
						</div>
						<div class="col-xs-6">
							<div class="box-header">
								<h3 class="box-title">Filter</h3>
							</div>
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<div class="col-md-12">
								<div class="col-md-6">
									<div class="form-group">
										<label for="">Check Date From</label>
										<div class="input-group date">
											<div class="input-group-addon bg-white">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From" autocomplete="off">
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="">Check Date To</label>
										<div class="input-group date">
											<div class="input-group-addon bg-white">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Date To" autocomplete="off">
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-12">
								<div class="col-md-12">
									<div class="form-group pull-right">
										<button onclick="location.reload()" style="margin-right: 10px" class="btn btn-danger">Clear</button>
										<button type="submit" onclick="fetchReportCdm()" class="btn btn-primary col-sm-14">Search</button>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xs-3">
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<table id="tableReportKensa" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th width="1%">#</th>
										<th width="1%">Kensa Code</th>
										<th width="1%">Product</th>
										<th width="1%">Material</th>
										<th width="2%">Desc</th>
										<th width="1%">Cav</th>
										<th width="2%">Start</th>
										<th width="2%">Finish</th>
										<th width="3%">NG</th>
										<th width="3%">By</th>
										<th width="3%">At</th>
									</tr>
								</thead>
								<tbody id="bodyReportKensa">
								</tbody>
								<tfoot>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
								</tfoot>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('.select2').select2({
			language : {
				noResults : function(params) {
					return "There is no date";
				}
			}
		});
		$('body').toggleClass("sidebar-collapse");
	});
	$('.datepicker').datepicker({
		autoclose: true,
		format: "yyyy-mm-dd",
		autoclose: true,
		todayHighlight: true
	});

	function changeMesin() {
		$("#machine").val($("#machineSelect").val());
	}

	function fetchReportCdm() {
		var data = {
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
		}

		$.get('{{ url("fetch/recorder/kensa_report") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
				$('#tableReportKensa').DataTable().clear();
				$('#tableReportKensa').DataTable().destroy();
				$('#bodyReportKensa').html("");
				var tableData = "";
				var count = 1;
				$.each(result.datas, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ count +'</td>';
					tableData += '<td>'+ value.serial_number +'</td>';
					tableData += '<td>'+ value.product +'</td>';
					tableData += '<td>'+ value.material_number +'</td>';
					tableData += '<td>'+ value.part_name +'</td>';
					tableData += '<td>'+ value.cavity +'</td>';
					tableData += '<td>'+ value.start_time +'</td>';
					tableData += '<td>'+ value.end_time +'</td>';
					ng_arr = value.ng_name.split(',');
					qty_arr = value.ng_count.split(',');

					tableData += '<td>';
					for(var i = 0; i < ng_arr.length; i++){
						tableData += ng_arr[i] +' = '+ qty_arr[i]+'<br>';
					}
					tableData += '</td>';
					tableData += '<td>'+ value.employee_id +' - '+ value.name +'</td>';
					tableData += '<td>'+ value.created +'</td>';
					tableData += '</tr>';

					count++;
				});
				$('#bodyReportKensa').append(tableData);

				$('#tableReportKensa tfoot th').each( function () {
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
				});
				var table = $('#tableReportKensa').DataTable({
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
						}
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching': true	,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});

				table.columns().every( function () {
					var that = this;

					$( 'input', this.footer() ).on( 'keyup change', function () {
						if ( that.search() !== this.value ) {
							that
							.search( this.value )
							.draw();
						}
					} );
				});

				$('#tableReportKensa tfoot tr').appendTo('#tableReportKensa thead');
			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);
				$('#tag').val('');
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

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '3000'
		});
	}
</script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
@endsection