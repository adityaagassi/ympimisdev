@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	#listTableBody > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}
	table.table-bordered{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;

	}
	#loading { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <span class="text-purple"> {{ $title_jp }} </span>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i></span>
			</center>
		</div>
	</div>
	<div class="box no-border" style="margin-bottom: 5px;">
		<div class="box-header">
			<h3 class="box-title">Detail Filters<span class="text-purple"> フィルター詳細</span></span></h3>
		</div>
		<div class="row">
			<div class="col-xs-12">
				
				<div class="col-md-3">
					<div class="form-group">
						<label>Keyword</label>
						<input type="text" class="form-control" id="keyword2" placeholder="Masukkan Kata Kunci">
					</div>
				</div>

				
				<div class="col-md-3">
					<div class="form-group">
						<div class="col-md-6" style="padding-right: 0;">
							<label style="color: white;"> x</label>
							<button class="btn btn-primary form-control" onclick="fetchTable()">Search</button>
						</div>
						<div class="col-md-6" style="padding-right: 0;">
							<label style="color: white;"> x</label>
							<button class="btn btn-danger form-control" onclick="clearSearch()">Clear</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="box-body">
			<table id="resumeTable" class="table table-bordered table-striped table-hover" style="margin-bottom: 20px;">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th style="width: 14%; text-align: center; font-size: 1.5vw;">Total</th>
						<th style="width: 14%; text-align: center; font-size: 1.5vw;">Today Target</th>
						<th style="width: 14%; text-align: center; font-size: 1.5vw;">Total Konfirmasi Today</th>
						<th style="width: 14%; text-align: center; font-size: 1.5vw;">Total Kirim PO Today</th>
						<th style="width: 14%; text-align: center; font-size: 1.5vw;">Over</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td id="count_all" style="text-align: center; font-size: 1.8vw; font-weight: bold;"></td>
						<td id="count_terminated" style="text-align: center; font-size: 1.8vw; font-weight: bold;"></td>
						<td id="count_inuse" style="text-align: center; font-size: 1.8vw; font-weight: bold;"></td>
						<td id="count_ok" style="background-color: #aee571; text-align: center; font-size: 1.8vw; font-weight: bold;"></td>
						<td id="count_expired" style="background-color: #e53935; text-align: center; font-size: 1.8vw; font-weight: bold;"></td>
					</tr>
				</tbody>
			</table>
			<table id="listTable" class="table table-bordered table-striped table-hover">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th style="">#</th>
						<th style="">Vendor</th>
						<th style="">PO Number</th>
						<th style="">Delivery</th>
						<th style="">Kirim PO</th>
						<th style="">Konfirmasi</th>
						<th style="">Reminder</th>
					</tr>
				</thead>
				<tbody id="listTableBody">
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
					</tr>
				</tfoot>
			</table>
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
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
		});
		$('.select2').select2();
		fetchTable();

    	$('body').toggleClass("sidebar-collapse");
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function fetchTable(){
		$('#loading').show();
		$.get('{{ url("fetch/general/agreement") }}', function(result, status, xhr){
			if(result.status){
				$('#listTable').DataTable().clear();
				$('#listTable').DataTable().destroy();				
				$('#listTableBody').html("");
				var listTableBody = "";
				var count_all = 0;
				var count_terminated = 0;
				var count_inuse = 0;
				var count_ok = 0;
				var count_expired = 0;

				$.each(result.agreements, function(key, value){
					if(value.status == 'In Use'){
						listTableBody += '<tr>';
					}
					if(value.status == 'Terminated'){
						listTableBody += '<tr style="background-color: RGB(255,204,255);">';
					}
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:0.1%;">'+parseInt(key+1)+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:1%;">'+value.department_shortname+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:3%;">'+value.vendor+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:0.5%;">'+value.valid_from+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:0.5%;">'+value.valid_to+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:0.5%;">'+value.status+'</td>';
					
					count_all += 1;
					if(value.status == "In Use"){
						count_inuse += 1;
					}
					if(value.status == "Terminated"){
						count_terminated += 1;
					}
					if(value.validity <= 0){
						if(value.status == "In Use"){
							count_expired += 1;
						}
						listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:2%; background-color: #e53935;">'+value.validity+' Day(s)</td>';			
					}
					else if(value.validity <= 30){
						if(value.status == "In Use"){
							count_30 += 1;
						}
						listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:2%; background-color: #f9a825;">'+value.validity+' Day(s)</td>';			
					}
					else if(value.validity <= 90){
						if(value.status == "In Use"){
							count_90 += 1;
						}
						listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:2%; background-color: #ffeb3b;">'+value.validity+' Day(s)</td>';			
					}
					else
					{
						if(value.status == "In Use"){
							count_ok += 1;
						}
						listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:2%; background-color: #aee571;">'+value.validity+' Day(s)</td>';			
					}
					listTableBody += '</tr>';
				});

				$('#listTableBody').append(listTableBody);

				$('#count_all').text(count_all);
				$('#count_terminated').text(count_terminated);
				$('#count_inuse').text(count_inuse);
				$('#count_ok').text(count_ok);
				$('#count_expired').text(count_expired);

				$('#listTable tfoot th').each( function () {
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				} );

				var table = $('#listTable').DataTable({
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

				$('#listTable tfoot tr').appendTo('#listTable thead');

				$('#loading').hide();

			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);
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

