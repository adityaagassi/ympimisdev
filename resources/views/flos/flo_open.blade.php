@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	#tableBodyList > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
		color: black;
		font-weight: bold;
	}

	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	table {
		table-layout:fixed;
		color: white;
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
		-webkit-appearance: none;
		margin: 0;
	}

	input[type=number] {
		-moz-appearance:textfield;
	}
	
	#loading { display: none; }

	.content{
		color: white;
	}
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
<section class="content" style="padding-top: 0px;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12" style="padding: 0px;">
			<div class="col-xs-3" style="padding-right: 0; color:black;">
				<select class="form-control select2" multiple="multiple" id='hpl' id='hpl' data-placeholder="Select Products" style="width: 100%;">
					@foreach($hpls as $hpl)
					<option value="{{ $hpl->hpl }}">FG - {{ $hpl->hpl }}</option>
					@endforeach
				</select>
			</div>
			<div class="col-xs-2">
				<button class="btn btn-success" onclick="fillTableList()">Update Table</button>
			</div>
			<div class="pull-right" style="margin: 0px; padding: 10px; font-size: 1vw; color: white;">
				<p style="color: white; font-weight: bold">Last Update at <span id="update_at"></span></p>
			</div>


		</div>


		<div class="col-xs-12" style="margin-top: 1%;">
			<table class="table table-hover table-bordered" id="tableList">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th style="width: 15%;">Stuffing Date</th>
						<th style="width: 15%;">Destination</th>
						<th style="width: 10%;">HPL</th>
						<th style="width: 10%;">GMC</th>
						<th style="width: 30%;">Description</th>
						<th style="width: 10%;">Plan</th>
						<th style="width: 10%;">Actual</th>
						<th style="width: 10%;">Diff.</th>
					</tr>					
				</thead>
				<tbody id="tableBodyList">
				</tbody>

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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('.select2').select2();
		
		fillTableList();
		setInterval(fillTableList, 10000);

	});


	function fillTableList(){
		var hpl = $('#hpl').val();

		var data = {
			hpl:hpl,
		}

		console.log(hpl);


		if(hpl.length > 0){
			$.get('{{ url("fetch/flo_open") }}', data,  function(result, status, xhr){
				$('#tableList').DataTable().clear();
				$('#tableList').DataTable().destroy();
				$('#tableBodyList').html("");

				$('#update_at').text(result.date);

				var tableData = "";
				var total_target = 0;
				$.each(result.data, function(key, value) {
					tableData += '<tr onclick="fillField(id)">';
					tableData += '<td>'+ value.st_date +'</td>';
					tableData += '<td>'+ value.destination_shortname +'</td>';
					tableData += '<td>'+ value.hpl +'</td>';
					tableData += '<td>'+ value.material_number +'</td>';
					tableData += '<td>'+ value.material_description +'</td>';
					tableData += '<td>'+ value.quantity +'</td>';
					tableData += '<td>'+ value.quantity +'</td>';
					tableData += '<td>'+ value.quantity +'</td>';
					tableData += '</tr>';
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