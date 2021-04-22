@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	table > tr:hover {
		background-color: #7dfa8c;
	}

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
		font-size: 0.93vw;
		border:1px solid black;
		padding-top: 5px;
		padding-bottom: 5px;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding-top: 3px;
		padding-bottom: 3px;
		padding-left: 2px;
		padding-right: 2px;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		font-size: 0.8vw;
		border:1px solid black;
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
	}
	#loading, #error { display: none; }
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
	</h1>
</section>
@endsection

@section('content')
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>

	</div>
	<div class="row">
		<div class="col-xs-2" style="padding-right: 0;">
			<button class="btn btn-success" style="width: 100%; margin-bottom: 10px;">Upload Quota</button>
			<button class="btn btn-success" style="width: 100%;" onclick="openModalMenu()">Upload Menu</button>
		</div>
		<div class="col-xs-10">
			<div class="box box-info">				
				<div class="box-header">
					<h3 class="box-title">Unconfirmed Order <span class="text-purple">???</span></h3>
				</div>			
				<div class="box-body">
					<table class="table table-hover table-bordered table-striped" id="tableOrderList">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 3%;">Ordered By</th>
								<th style="width: 3%;">Charged To</th>
								<th style="width: 1%;">Date</th>
								<th style="width: 3%;">Ordered For</th>
								<th style="width: 3%;">Dept</th>
								<th style="width: 1%;">Status</th>
								<th style="width: 1%;">Action</th>
							</tr>
						</thead>
						<tbody id="tableOrderListBody">
						</tbody>
						<tfoot style="background-color: RGB(252, 248, 227);">
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
		</div>
	</div>
	<div class="row">
		<div class="col-xs-4">
			<div class="box box-info">				
				<div class="box-header">
					<h3 class="box-title">Resume Order <span class="text-purple">???</span></h3>
				</div>			
				<div class="box-body">
				</div>
			</div>
		</div>
		<div class="col-xs-8">
			<div class="box box-info">				
				<div class="box-header">
					<h3 class="box-title">Order History <span class="text-purple">???</span></h3>
				</div>			
				<div class="box-body">
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalMenu">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">

			</div>
		</div>
	</div>
</div>

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
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {

	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function openModalMenu(){
		$('#menuPeriod').val("");
		$('#menuAtt').val("");		
		$('#modalMenu').modal('show');
	}

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '5000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '5000'
		});
	}
</script>

@endsection