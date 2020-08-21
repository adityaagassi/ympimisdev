@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

	.clapclap {
		/*width: 50px;
		height: 50px;*/
		-webkit-animation: clapclap 1s infinite;  /* Safari 4+ */
		-moz-animation: clapclap 1s infinite;  /* Fx 5+ */
		-o-animation: clapclap 1s infinite;  /* Opera 12+ */
		animation: clapclap 1s infinite;  /* IE 10+, Fx 29+ */
	}
	
	@-webkit-keyframes clapclap {
		0%, 49% {
			background: rgba(0, 0, 0, 0);
			/opacity: 0;/
		}
		50%, 100% {
			background-color: rgb(230, 230, 230);
		}
	}

	tbody>tr>td{
		text-align:center;
		vertical-align: middle;
		font-weight: bold;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	#loading { display: none; }
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-7">
			<div class="row">
				<center>
					<div class="col-xs-12" id="visitor_appeal" style="font-size: 7vw; padding-right: 0; font-weight: bold;"></div>
					<div class="col-xs-12" id="count_detail" style="color: white; font-weight: bold; font-size: 2vw;"></div>
					<div class="col-xs-12" id="visitor_count" style="font-size: 24vw; font-weight: bold;"></div>
				</center>
			</div>
		</div>
		<div class="col-xs-5" id="police_image">
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
		setInterval(fetchVisitorCount, 800);
	});

	function fetchVisitorCount(){
		$.get('{{ url("fetch/general/omi_visitor") }}', function(result, status, xhr){
			if(result.status){
				var visitor_count = "";
				var count_detail = "";
				var visitor_appeal = "";
				var police_image = "";
				$('#visitor_count').html("");
				$('#count_detail').html("");
				$('#visitor_appeal').html("");
				$('#police_image').html("");

				var count = result.visitors.length;

				if(count >= 17){
					visitor_count = '<span style="background-color: #ff1744;">&nbsp;'+count+'&nbsp;</span>';
					count_detail = '<span style="color: #ff1744;"><i class="fa fa-arrow-up"></i> Jumlah Pengunjung Melebihi Ketentuan <i class="fa fa-arrow-up"></i></span>';
					visitor_appeal = '<span style="color: #ff1744;" class="clapclap">DILARANG MASUK</span>';
					police_image = '<center><img style="height: 700px;" src="{{ url('images/omi/police01_b_09.png') }}"></center>';
				}
				else{
					visitor_count = '<span style="background-color: #ccff90;">&nbsp;'+count+'&nbsp;</span>';
					count_detail = '<span style="color:#ccff90;"><i class="fa fa-arrow-down"></i> Jumlah Pengunjung Dibawah Ketentuan <i class="fa fa-arrow-down"></i></span>';
					visitor_appeal = '<span style="color: #ccff90;">BOLEH MASUK</span>';
					police_image = '<center><img style="height: 700px;" src="{{ url('images/omi/police01_b_10.png') }}"></center>';
				}

				$('#visitor_count').append(visitor_count);
				$('#count_detail').append(count_detail);
				$('#visitor_appeal').append(visitor_appeal);
				$('#police_image').append(police_image);
			}
			else{
				openErrorGritter('Error', 'Attempt to retrieve data failed.');
			}
		});
	}
	
	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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