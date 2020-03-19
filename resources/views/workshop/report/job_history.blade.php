@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/fixedcolumns/3.3.0/css/fixedColumns.dataTables.min.css" rel="stylesheet">
<style type="text/css">
	/*thead>tr>th{
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
	}*/

	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > th{
		border:1px solid rgb(211,211,211);
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
		text-align: center;
	}

	#div {
		max-width: 100%;
		max-height: 500px;
		overflow: scroll;
		position: relative;
	}

	table {
		position: relative;
	}

	td,
	th {
		padding: 0.25em;
	}

	#tabel thead th {
		position: -webkit-sticky; /* for Safari */
		position: sticky;
		top: 0;
		background: #000;
		color: #FFF;
	}

	thead th:first-child {
		left: 0;
		z-index: 1;
	}

	tbody th {
		position: -webkit-sticky; /* for Safari */
		position: sticky;
		left: 0;
		background: #FFF;
		border-right: 1px solid #CCC;
	}


	#loading, #error { 
		display: none;
	}

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		<form method="GET" action="{{ url("excel/workshop/job_history") }}">
		{{ $title }}
		<small><span class="text-purple"> {{ $title_jp }}</span></small>
			<button class="btn btn-success pull-right" type="submit" style="margin-left: 20px"><i class="fa  fa-file-excel-o"></i> Excel</button>
			<div class="input-group date pull-right col-xs-2">
				<div class="input-group-addon bg-default">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control datepicker" id="mon" placeholder="Pilih Bulan" onchange="get_data()" name="mon">
			</div>
		</form>
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

	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<center><h1 style="margin-top: 0px">Operator Working Time <span id="title_mon"></span> (minutes)</h1></center>
					<div id="div">
						<table class="table table-bordered" id="tabel">
							<thead>
								<tr>
									<th width='20%'></th>
									<?php 
									foreach ($process as $pcs) {
										echo "<th>".$pcs->process_name."</th>";
									} 
									?>
								</tr>
							</thead>
							<tbody id="tbody">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

</section>

@endsection
@section('scripts')
<!-- <script src="https://code.jquery.com/jquery-3.3.1.js"></script> -->
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/3.3.0/js/dataTables.fixedColumns.min.js"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<!-- <script src="{{ url("js/dataTables.buttons.min.js")}}"></script> -->
<!-- <script src="{{ url("js/buttons.flash.min.js")}}"></script> -->
<!-- <script src="{{ url("js/jszip.min.js")}}"></script> -->
<!-- <script src="{{ url("js/vfs_fonts.js")}}"></script> -->
<!-- <script src="{{ url("js/buttons.html5.min.js")}}"></script> -->
<!-- <script src="{{ url("js/buttons.print.min.js")}}"></script> -->
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>

<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		var table = $('#example').DataTable( {
			scrollY:        "300px",
			scrollX:        true,
			scrollCollapse: true,
			paging:         false,
			fixedColumns:   true
		} );

		get_data();

	});

	function get_data() {
		var data = {
			mon: $('#mon').val()
		}
		var body = "";
		var stat = 0;

		$.get('{{ url("fetch/workshop/job_history") }}', data,  function(result, status, xhr){
			$.each(result.datas, function(index, value){
				if (typeof result.datas[index+1] === 'undefined') {
					if (value.waktu == '0.00') time = '-'; else time = value.waktu;
					body += "<td>"+time+"</td></tr>";
				} else {
					if (result.datas[index].operator_id.toUpperCase() != result.datas[index+1].operator_id.toUpperCase()) {
						if (value.waktu == '0.00') time = '-'; else time = value.waktu;

						body += "<td>"+time+"</td></tr>";
						stat = 0;
					} else {
						if (stat == 0) {
							if (value.waktu == '0.00') time = '-'; else time = value.waktu;

							body += "<tr><th>"+value.operator_name+"</th>";
							body += "<td>"+time+"</td>";
							stat = 1;
						} else {
							if (value.waktu == '0.00') time = '-'; else time = value.waktu;

							body += "<td>"+time+"</td>";
						}
					}
				}
			})
			$("#tbody").html("");
			$("#title_mon").text(result.param);
			$("#tbody").append(body);
		})
	}

	$('.datepicker').datepicker({
		autoclose: true,
		format: "yyyy-mm",
		viewMode: "months", 
		minViewMode: "months"
	});

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