@extends('layouts.master')
@section('stylesheets')
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
		padding: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		Leave Control <span class="text-purple"> japanese</span>
		{{-- <small>Based on ETD YMPI <span class="text-purple">YMPIのETDベース</span></small> --}}
	</h1>
	<ol class="breadcrumb" id="last_update">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box">
				<div class="box-body">
					asdas
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalResult">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="modalResultTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center>
						<i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
					</center>
					<table class="table table-hover table-bordered table-striped" id="tableModal">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th>Material</th>
								<th>Description</th>
								<th>Dest.</th>
								<th>Plan</th>
								<th>Actual</th>
								<th>Diff</th>
							</tr>
						</thead>
						<tbody id="modalResultBody">
						</tbody>
						<tfoot style="background-color: RGB(252, 248, 227);">
							<th>Total</th>
							<th></th>
							<th></th>
							<th id="modalResultTotal1"></th>
							<th id="modalResultTotal2"></th>
							<th id="modalResultTotal3"></th>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@section('scripts')
{{-- <script src="{{ url("js/highcharts.js")}}"></script> --}}
<script src="{{ url("js/highstock.js")}}"></script>
{{-- <script src="{{ url("js/annotations.js")}}"></script> --}}
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
		$('body').toggleClass("sidebar-collapse");
		fillChart();
	});

	var interval;
	var statusx = "idle";

	$(document).on('mousemove keyup keypress',function(){
		clearTimeout(interval);
		settimeout();
		statusx = "active";
	})

	function settimeout(){
		interval=setTimeout(function(){
			statusx = "idle";
			fillChart()
		},60000)
	}

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}

	function getActualFullDate(){
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
	}

	function fillChart(){

	}
</script>
@endsection