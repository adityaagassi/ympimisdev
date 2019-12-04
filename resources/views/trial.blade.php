@extends('layouts.display')
@section('stylesheets')
@stop
@section('header')
@endsection
<style>
	#my_camera{
		width: 320px;
		height: 240px;
		border: 1px solid black;
	}
	#my_camera { display: none; }
</style>
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="col-md-12">
		<div id="my_camera"></div>
		<input type=button value="Configure" onClick="configure()">
		<input type=button value="Take Snapshot" onClick="take_snapshot()">
		<input type=button value="Save Snapshot" onClick="saveSnap()">

		<div id="results1"></div>
		<div id="results"></div>
		<img src="" width="100%" id='dd'>
	</div>
</section>
@stop
@section('scripts')
<script src="{{ url("js/webcam.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	Webcam.set({
		width: 1920,
		height: 1024,
		image_format: 'jpeg',
		jpeg_quality: 100
	});
	Webcam.attach( '#my_camera' );

	function take_snapshot() {
		Webcam.snap( function(data_uri) {
			document.getElementById('results').innerHTML = 
			'<img id="imageprev" width="240" src="'+data_uri+'"/>';
		});
	}

	function saveSnap(){
		var base64image = document.getElementById("imageprev").src;

		var data = {
			image : base64image
		}

		$.post('{{ url("upload/trial") }}', data, function(result, status, xhr){
			document.getElementById("dd").src = result.message;
		});
	}
</script>
@endsection