@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	.video-container { 
		position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; 
		} 
	.video-container iframe, .video-container object, .video-container embed { 
		position: absolute; 
		top: 0; 
		left: 0; 
		width: 100%; 
		height: 400px; 
	}
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding: 0">
	<div class="row" style="padding: 0">
		<div class="col-xs-12">
			<div class="row">
				<h2 style="color: white;margin: 0"><center>Video Tutorial Stocktaking 6 Bulanan</center></h2><br>
				<embed src="" style="width: 500px"></embed>
				<div class="video-container">
					<iframe width="560" height="315" src="{{url('vid/sosialisasi_6_bulanan.mp4')}}" frameborder="0" allowfullscreen></iframe>
				</div>
			</div>
		</div>
	</div>

</section>
@endsection
@section('scripts')
<script src="{{ url("js/highstock.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
	});



</script>
@endsection