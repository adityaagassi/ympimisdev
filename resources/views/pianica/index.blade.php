@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Pianica<span class="text-purple"> ピアニカ</span>
		{{-- <small>Flute <span class="text-purple"> ??? </span></small> --}}
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 3vw; color: green;"><i class="fa fa-angle-double-down"></i> Master <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/Op") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Master Operator </a>
			<a href="{{ url("index/Op_Code") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Master Code Operator</a>

			<span style="font-size: 3vw; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/Bensuki") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Bentsuki-Benage </a>
			<a href="{{ url("index/Pureto") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Pureto </a>
			<a href="{{ url("index/KensaAwal") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Kensa Awal </a>
			<a href="{{ url("index/KensaAkhir") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Kensa Akhir </a>
			<a href="{{ url("index/KakuningVisual") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Kakunin Visual </a>
			{{-- <a href="{{ url("/index/repairFl") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;">Return/Repair</a> --}}
			
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 3vw; color: red;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/display_pn_ng_rate?tanggal=&location=") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;">NG by Operator</a>
			<a href="{{ url("index/display_pn_ng_trends?location=") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;">Daily NG by Operator</a>
			<a href="{{ url("index/display_daily_pn_ng?location=") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;">Daily NG</a>
			<a href="{{ url('index/skill_map','pn-assy') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;">Skill Map</a>
			
		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 3vw;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("/index/DisplayPN") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Display</a>
			<a href="{{ url("/index/reportBensuki") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Report Bentsuki</a>			  
			<a href="{{ url("/index/reportAwal") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Report Kensa Awal All Line</a>
			<a href="{{ url("/index/reportAwalLine") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Report Kensa Awal / Line</a>
			<a href="{{ url("/index/reportAkhir") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Report Kensa Akhir All Line</a>
			<a href="{{ url("/index/reportAkhirLine") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Report Kensa Akhir / Line</a>
			<a href="{{ url("/index/reportVisual") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Report Kakunin Visual All Line</a>
			<a href="{{ url("/index/record") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Pianica Inventories</a>

			<a href="{{ url("/index/reportDayAwal") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Monthly Report </a>

			<a href="{{ url("/index/reportSpotWelding") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Daily Report Spot Welding</a>

			<a href="{{ url("/index/reportKensaAwalDaily") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Daily Report Kensa Awal</a>

			<a href="{{ url("/index/reportKensaAkhirDaily") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Daily Report Kensa Akhir</a>

			<a href="{{ url("/index/reportVisualDaily") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Daily Report Kakunin Visual</a>


		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		
	});
</script>
@endsection