@extends('layouts.display')
@section('stylesheets')
@stop
@section('header')
@endsection
<style type="text/css">
</style>
@section('content')
<section class="content" style="padding-top: 0;">
	<textarea></textarea>
	<button class="btn btn-danger" onclick="mesin()">Mesin</button>
	<button class="btn btn-warning" onclick="textarrange()">Text Trial Arrange</button>

</section>
@stop
@section('scripts')
<script src="{{ url("js/jquery.marquee.min.js")}}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>

	jQuery(document).ready(function() {
		drawTable2();
	});	

	function textarrange(){

	}

	function mesin(){
		$.get("{{ 'http://172.17.129.99/zed/dashboard/getData' }}", function(result, status, xhr){
			console.log(result);
		});
	}


</script>
@endsection