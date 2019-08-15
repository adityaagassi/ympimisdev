@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	
</style>
@stop
@section('header')
{{-- <section class="content-header" style="padding-top: 0; padding-bottom: 0;">
</section> --}}
@endsection
@section('content')
<section class="content" style="padding: 0px;">
	<a href="{{ url('tes') }}" class="btn">asdfasdas</a>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
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

</script>
@endsection

