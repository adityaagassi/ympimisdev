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
		{{ $title }}
		<small><span class="text-purple"> {{ $title_jp }}</span></small>
		<!-- <button class="btn btn-info pull-right" data-toggle="modal"  data-target="#create_modal" style="margin-right: 5px">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Tambahkan Data
		</button> -->
	</h1>

	<ol class="breadcrumb">
		<li>
		</li>
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading . . . <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-xs-12">
						<span style="font-weight: bold;">Month From</span>
					</div>
					<div class="col-xs-3">
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control pull-right" id="month" placeholder="Pilih Bulan">
						</div>
					</div>
					<div class="col-xs-4">
						<button id="search" onClick="getData()" class="btn btn-primary">Search</button>
					</div>
				</div>
			</div>
			<div class="box box-solid">
				<div class="box-header">
				</div>
				<div class="box-body" style="padding-top: 0;">
					<table id="jigTableResume" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgb(126,86,134); color: #FFD700;">
							<tr>
								<th style="width: 1%">#</th>
								<th style="width: 1%">Jig ID</th>
								<th style="width: 3%">Jig Name</th>
								<th style="width: 2%">Start</th>
								<th style="width: 2%">Finish</th>
								<th style="width: 2%">Operator</th>
								<th style="width: 1%">Result</th>
								<!-- <th style="width: 1%">Status</th> -->
								<th style="width: 1%">Action</th>
							</tr>
						</thead>
						<tbody id="bodyJigTableResume">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalDetail">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="modalDetailTitle"></h4>
					<div class="modal-body table-responsive no-padding" style="min-height: 100px">
						<!-- <center><h3 style="font-weight: bold;" id="judul_detail"></h3></center> -->
						<div class="col-md-12" id="bodyDetail">
				          
				        </div>
					</div>
				</div>
				<div class="modal-footer">
		          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
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
<script src="{{ url('js/vfs_fonts.js')}}"></script>
<script src="{{ url('js/buttons.html5.min.js')}}"></script>
<script src="{{ url('js/buttons.print.min.js')}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		getData();
		$('#month').val("");
		$('#month').datepicker({
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true

		});
	});

	function getData() {
		var data = {
			month:$('#month').val()
		}
		$.get('{{ url("fetch/welding/kensa_jig_report") }}',data, function(result, status, xhr){
			if(result.status){
				$('#jigTableResume').DataTable().clear();
				$('#jigTableResume').DataTable().destroy();
				$('#bodyJigTableResume').empty();
				var jigtable = '';
				var index = 1;

				$.each(result.jig_report, function(key, value) {
					jigtable += '<tr>';
					jigtable += '<td>'+index+'</td>';
					jigtable += '<td>'+value.jig_id+'</td>';
					jigtable += '<td>'+value.jig_name+'</td>';
					jigtable += '<td>'+value.started_at+'</td>';
					jigtable += '<td>'+value.finished_at+'</td>';
					jigtable += '<td>'+value.operator+'</td>';
					jigtable += '<td><span class="label label-success">'+value.result+' - '+value.status+'</span></td>';
					jigtable += '<td><button onclick="showModal(\''+value.jig_id+'\',\''+value.started_at+'\',\''+value.finished_at+'\')" class="btn btn-primary btn-sm">Detail</button></td>';
					index++;
				});

				$('#bodyJigTableResume').append(jigtable);

				var table = $('#jigTableResume').DataTable({
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
					'processing': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});
			}else{
				alert('Retireve Data Failed');
			}
		});
	}

	function showModal(jig_id,started_at,finished_at) {
		$('#loading').show();
		var data = {
			jig_id:jig_id,
			started_at:started_at,
			finished_at:finished_at,
		}

		$.get('{{ url("fetch/welding/detail_kensa_jig_report") }}',data, function(result, status, xhr){
			if(result.status){
				var detail = "";
				var jig_judul = "";
				$('#bodyDetail').empty();
				var index = 1;

				$('#judul_detail').html(result.judul);

	        	detail += '<div class="box-body">';
		        detail += '<table class="table table-bordered" style="font-size:15px">';
		        detail += '<thead>';
		        detail += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#7e5686;color:white;font-size:20px">';
		        detail += '<th colspan="11" id="jig_judul"></th>';
		        detail += '</tr>';
		        detail += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">';
		        detail += '<th>No.</th>';
		        detail += '<th>Jig ID</th>';
		        detail += '<th>Jig Name</th>';
		        detail += '<th>Jig Point Check</th>';
		        detail += '<th>Jig Part</th>';
		        detail += '<th>Lower</th>';
		        detail += '<th>Upper</th>';
		        detail += '<th>Value</th>';
		        detail += '<th>Result</th>';
		        detail += '<th>Status</th>';
		        detail += '<th>Operator</th>';
		        detail += '</tr>';
		        detail += '</thead>';
		        var index2 = 1;
				$.each(result.detail, function(key2, value2) {
					if (value2.result == 'NG') {
						var color = '#ffbaba';
					}else{
						var color = '#f2f2f2';
					}
					detail += '<tbody style="border:1px solid black">';
					detail += '<tr style="border:1px solid black;padding:2px;background-color:'+color+'">';
					detail += '<td style="border:1px solid black;padding:2px">'+index2+'</td>';
					detail += '<td style="border:1px solid black;padding:2px">'+value2.jig_id+'</td>';
					detail += '<td style="border:1px solid black;padding:2px">'+value2.jig_name+'</td>';
					detail += '<td style="border:1px solid black;padding:2px">'+value2.check_name+'</td>';
					detail += '<td style="border:1px solid black;padding:2px">'+value2.jig_child+'</td>';
					detail += '<td style="border:1px solid black;padding:2px">'+value2.lower_limit+'</td>';
					detail += '<td style="border:1px solid black;padding:2px">'+value2.upper_limit+'</td>';
					detail += '<td style="border:1px solid black;padding:2px">'+value2.value+'</td>';
					detail += '<td style="border:1px solid black;padding:2px">'+value2.result+'</td>';
					if (value2.status ==  null) {
						detail += '<td style="border:1px solid black;padding:2px">OK</td>';
					}else{
						detail += '<td style="border:1px solid black;padding:2px">'+value2.status+'</td>';
					}
					detail += '<td style="border:1px solid black;padding:2px">'+value2.name+'</td>';
					detail += '</tr>';
					detail += '</tbody>';
					index2++;

					jig_judul = value2.jig_id+' - '+value2.jig_name;
					index++;
				});
				detail += '</table>';
				detail += '</div>';	

				$('#bodyDetail').append(detail);
				$('#modalDetail').modal('show');
				$('#jig_judul').html(jig_judul);
				$('#loading').hide();
			}
			else{
				alert('Attempt to retrieve data failed');
				$('#loading').hide();
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
@endsection