@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	input {
		line-height: 22px;
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
		font-size: 18px;
		padding-top: 1px;
		padding-bottom: 1px;
		border:1px solid black;
		background-color: rgba(126,86,134);
	}
	table.table-bordered > tbody > tr > td{
		font-size: 16px;
		border:1px solid black;
		padding-top: 3px;
		padding-bottom: 3px;
		background-color: #8CD790;
		color: #000;
	}
	table.table-bordered > tfoot > tr > th{
		font-size: 16px;
		border:1px solid black;
		background-color: #ffffc2;
	}
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="padding-bottom: 5px;">
			<div class="row">
				<div class="col-xs-5">
					<span style="color: white; font-size: 1.7vw; font-weight: bold;"><i class="fa fa-caret-right"></i> Resume </span>
					<button class="btn btn-success pull-right" onclick="refreshData()"><i class="fa fa-refresh"></i> Perbaharui Data</button>
				</div>
			</div>
		</div>
		<div class="col-xs-12" style="padding-bottom: 5px;">
			<div class="row">
				<div class="col-xs-5">
					<table class="table table-bordered" id="tableResume" style="margin-bottom: 5px;">
						<thead>
							<tr>
								<th style="color:white;width: 10%; font-size: 1.2vw; text-align: center; vertical-align: middle;">Department</th>
								<th style="color:white;width: 1%; font-size: 1.2vw; text-align: center; vertical-align: middle;">Count Person</th>
								<th style="color:white;width: 1%; font-size: 1.2vw; text-align: center; vertical-align: middle;">Count Upload</th>
							</tr>					
						</thead>
						<tbody id="tableNoCheckBody">
							<?php 
							$total_person = 0;
							$total_upload = 0;
							?>
							@foreach($mosaics as $mosaic)
							<?php
							$total_person = $total_person+$mosaic->count_person;
							$total_upload = $total_upload+$mosaic->count_upload;
							?>
							<tr>
								<td style="text-align: left;">{{ $mosaic->department }}</td>
								<td style="text-align: right;">{{ $mosaic->count_person }}</td>
								<td style="text-align: right;">{{ $mosaic->count_upload }}</td>
							</tr>
							@endforeach
						</tbody>
						<tfoot style="background-color: RGB(252, 248, 227);">
							<tr>
								<th style=" font-size: 1.5vw;">Total</th>
								<th style=" font-size: 1.5vw;">{{ $total_person }}</th>
								<th style=" font-size: 1.5vw;">{{ $total_upload }}</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/accessibility.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		
	});

	function refreshData(){
		location.reload(true);	
	}
</script>
@endsection
