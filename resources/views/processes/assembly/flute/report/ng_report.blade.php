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
		{{$title}} <small><span class="text-purple">{{$title_jp}}</span></small>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-header">
					<h3 class="box-title">NG Report Filters</h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-3">
							<div class="form-group">
								<label>Date From</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="datefrom" name="datefrom" placeholder="Date From">
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Date To</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="dateto" name="dateto"  placeholder="Date To">
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-6">
							<div class="form-group">
								<select class="form-control select2" data-placeholder="Select Process" name="process" id="process" style="width: 100%;">
									<option value=""></option>
									@foreach($flow as $flow) 
									<option value="{{ $flow->process }}">{{ $flow->process }}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group pull-right">
								<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
								<button id="search" onClick="fillData()" class="btn btn-primary">Search</button>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<table id="tableNgReport" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>Serial Number</th>
										<th>Model</th>
										<th>Process</th>
										<th>NG Name</th>
										<th>NG Detail</th>
										<th>Kunci</th>
										<th>Nilai</th>
										<th>Lokasi NG</th>
										<th>PIC Check</th>
										<th>PIC Produksi</th>
										<th>Repair Status</th>
										<th>PIC Repair</th>
										<th>Key Decision</th>
										<th>Created At</th>
									</tr>
								</thead>
								<tbody id="bodyTableNgReport">
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
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script src="{{ url('js/dataTables.buttons.min.js')}}"></script>
<script src="{{ url('js/buttons.flash.min.js')}}"></script>
<script src="{{ url('js/jszip.min.js')}}"></script>
{{-- <script src="{{ url('js/pdfmake.min.js')}}"></script> --}}
<script src="{{ url('js/vfs_fonts.js')}}"></script>
<script src="{{ url('js/buttons.html5.min.js')}}"></script>
<script src="{{ url('js/buttons.print.min.js')}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('#datefrom').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		$('#dateto').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		$('.select2').select2({
			language : {
				noResults : function(params) {
					return "There is no flo with status 'close'";
				}
			}
		});
		fillData();
	});

	

	function clearConfirmation(){
		location.reload(true);
	}

	function fillData(){
		$('#loading').show();
		$('#flo_detail_table').DataTable().destroy();
		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();
		var code = $('#process').val();

		var proces = '{{$process}}';

		url	= '{{ url("fetch/assembly/ng_report") }}'+'/'+proces;
		
		var data = {
			datefrom:datefrom,
			dateto:dateto,
			process:code
		}
		$.get(url,data, function(result, status, xhr){
			if(result.status){
				$('#tableNgReport').DataTable().clear();
				$('#tableNgReport').DataTable().destroy();
				$('#bodyTableNgReport').html("");
				var tableData = "";
				$.each(result.ng_report, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ value.serial_number +'</td>';
					tableData += '<td>'+ value.model +'</td>';
					tableData += '<td>'+ value.location +'</td>';
					var ng = value.ng_name.split(' - ');
					tableData += '<td>'+ ng[0] +'</td>';
					tableData += '<td>'+ ng[1] +'</td>';
					tableData += '<td>'+ value.ongko +'</td>';
					if (value.value_bawah == null) {
						tableData += '<td></td>';
					}else{
						tableData += '<td>'+value.value_atas+' - '+value.value_bawah+'</td>';
					}
					tableData += '<td>'+ (value.value_lokasi || "") +'</td>';
					tableData += '<td>'+ value.checked_by +'</td>';
					if (value.operator_id_details != null && value.operator_id_log == null) {
						var opid = value.operator_id_details.split(',');
						tableData += '<td>'+ opid.join('<br>') +'</td>';
					}else if (value.operator_id_details == null && value.operator_id_log != null) {
						var opid = value.operator_id_log.split(',');
						tableData += '<td>'+ opid.join('<br>') +'</td>';
					}else{
						tableData += '<td></td>';
					}
					tableData += '<td>'+ (value.repair_status || "") +'</td>';
					tableData += '<td>'+ (value.repaires || "") +'</td>';
					tableData += '<td>'+ (value.decision || "Tidak Ganti Kunci") +'</td>';
					tableData += '<td>'+ value.created +'</td>';
					tableData += '</tr>';
				});
				$('#bodyTableNgReport').append(tableData);

				var table = $('#tableNgReport').DataTable({
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
					'searching': true,
					"processing": true,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});
				
				$('#tableNgReport tfoot th').each(function(){
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
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
				} );

				$('#tableNgReport tfoot tr').appendTo('#tableNgReport thead');

				$('#loading').hide();

			}
			else{
				alert('Attempt to retrieve data failed');
				$('#loading').hide();
			}
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

	function openInfoGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-info',
			image: '{{ url("images/image-unregistered.png") }}',
			sticky: false,
			time: '2000'
		});
	}
</script>
@endsection