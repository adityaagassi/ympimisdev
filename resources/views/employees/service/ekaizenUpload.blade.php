@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<span class="text-purple"> ({{ $title_jp }})</span>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="box">
		<div class="box-body">
			<div class="col-xs-6">
				<form method="POST" action="{{url('post/upload_kaizen')}}" enctype="multipart/form-data">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<select class="form-control select2" id="employee_id" data-placeholder="Select Employee" name="employee_id" required>
						<option value="">Select Employee</option>
						@foreach($employees as $emp)
						<option value="{{ $emp->employee_id }}">{{ $emp->employee_id }} - {{ $emp->name }}</option>
						@endforeach
					</select>
					<input id="fileupload" type="file" multiple="multiple" class="form-control" name="fileupload[]"  style="margin-top: 2vw">
					<!-- <input id="ckfinder-input-1" type="text" style="width:60%"> -->
					<!-- <button id="ckfinder-popup-1" class="button-a button-a-background">Browse Server</button> -->
					<button type="submit" class="btn btn-success" style="margin-top: 2vw"><i class="fa fa-upload"></i> UPLOAD</button>
				</form>
			</div>
			<div class="col-xs-6">
				<b>Live Preview</b>
				<br>
				<br>
				<div id="dvPreview">
				</div>
			</div>
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
<!-- <script src="{{ url("ckfinder/ckfinder.js") }}"></script> -->
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var button1 = document.getElementById( 'ckfinder-popup-1' );

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.select2').select2();

		// getEmployees();
	});

	// button1.onclick = function() {
	// 	selectFileWithCKFinder( 'ckfinder-input-1' );
	// };

	// function selectFileWithCKFinder( elementId ) {
	// 	CKFinder.popup( {
	// 		chooseFiles: true,
	// 		width: 800,
	// 		height: 600,
	// 		onInit: function( finder ) {
	// 			finder.on( 'files:choose', function( evt ) {
	// 				var file = evt.data.files.first();
	// 				var output = document.getElementById( elementId );
	// 				output.value = file.getUrl();
	// 			} );

	// 			finder.on( 'file:choose:resizedImage', function( evt ) {
	// 				var output = document.getElementById( elementId );
	// 				output.value = evt.data.resizedUrl;
	// 			} );
	// 		}
	// 	} );
	// }

	// function getEmployees() {
	// 	$("#employee_id").empty();
	// 	$.get('{{ url("fetch/upload_kaizen/image") }}', function(result, status, xhr){
	// 		var emp = "";

	// 		$.each(result.employees, function(index, value){
	// 			emp += "<option value='"+value.employee_id+"'>"+value.employee_id+" - "+value.name+"</option>";
	// 		})

	// 		$("#employee_id").append(emp);
	// 	})
	// }

	$(function () {
		$("#fileupload").change(function () {
			if (typeof (FileReader) != "undefined") {
				var dvPreview = $("#dvPreview");
				dvPreview.html("");
				var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.jpg|.jpeg|.gif|.png|.bmp)$/;
				$($(this)[0].files).each(function () {
					var file = $(this);
					if (regex.test(file[0].name.toLowerCase())) {
						var reader = new FileReader();
						reader.onload = function (e) {
							var img = $("<img />");
							img.attr("style", "width: 200px");
							img.attr("src", e.target.result);
							img.attr("title", 'dada');
							dvPreview.append(img);
						}
						reader.readAsDataURL(file[0]);
					} else {
						alert(file[0].name + " is not a valid image file.");
						dvPreview.html("");
						return false;
					}
				});
			} else {
				alert("This browser does not support HTML5 FileReader.");
			}
		});
	});

</script>
@endsection