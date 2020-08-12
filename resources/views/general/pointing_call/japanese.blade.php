@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	.dot {
		height: 5%;
		width: 5%;
		position: absolute;
		z-index: 10;
	}
	#loading { display: none; }
</style>
@stop
@section('header')
@endsection
@section('content')
<input type="hidden" id="location" value="{{ $location }}">
<input type="hidden" id="default_language" value="{{ $default_language }}">
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i><br>Loading...</span>
			</center>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		fetchPoint();
	});

	function fetchPoint(){
		var location = $('#location').val();
		var data = {
			location: location
		}

		$.get('{{ url("fetch/general/pointing_call") }}', data, function(result, status, xhr){
			if(result.status){
				$('.content').html('');

				var pic_data = '';
				pic_data += '<div class="col-xs-12" style="padding-bottom: 10px;">';
				pic_data += '<center>';
				$.each(result.pics, function(key, value){
					if(value.remark == 1){
						pic_data += '<button class="btn btn-lg" style="border-color: black; width: 18%; font-weight: bold; background-color: orange; padding: 2px 5px 2px 5px;">'+value.point_description+'<br>'+value.point_description_jp+'</button>';
					}
					else{
						pic_data += '<button class="btn btn-lg" style="border-color: black; width: 18%; font-weight: bold; background-color: white; padding: 2px 5px 2px 5px;">'+value.point_description+'<br>'+value.point_description_jp+'</button>';
					}
				});
				pic_data += '</center>';
				pic_data += '</div>';
				$('.content').append(pic_data);

				var count = 1;
				if($('#default_language').val() == 'jp'){
					var image_data = '';

					$.each(result.pointing_calls, function(key, value){
						image_data += '<div class="row" style="position:relative" id="'+value.point_title+'" name="'+count+'" tabindex="1">';
						image_data += '<img src="{{ asset('images/pointing_calls') }}/'+value.point_title+'_jp.jpg" style="width: 100%;">';
						image_data += '</div>';
						count += 1;
					});
					image_data += '<div style="height: 1200px;"></div>'
					$('.content').append(image_data);

					$.each(result.pointing_calls, function(key, value){
						var point_data = '';

						if(value.point_title == 'diamond'){
							point_data += '<div id="dot_diamond" class="dot">';
							point_data += '<img src="{{url("/images/pointing_calls/arrow.gif")}}" width="100%">';
							point_data += '</div>';
							$('#diamond').append(point_data);

							if(value.point_no == 1){
								var x = $('#diamond').width() / 3;
								var y = $('#diamond').height() * 10.3;
							}

							if(value.point_no == 2){
								var x = $('#diamond').width() / 2.6;
								var y = $('#diamond').height() * 3.3;
							}

							if(value.point_no == 3){
								var x = $('#diamond').width() / 2.6;
								var y = $('#diamond').height() * 4;
							}

							if(value.point_no == 4){
								var x = $('#diamond').width() / 2.6;
								var y = $('#diamond').height() * 4.7;
							}

							if(value.point_no == 5){
								var x = $('#diamond').width() / 2.6;
								var y = $('#diamond').height() * 5.5;
							}

							if(value.point_no == 6){
								var x = $('#diamond').width() / 6.8;
								var y = $('#diamond').height() * 7;
							}

							if(value.point_no == 7){
								var x = $('#diamond').width() / 6.8;
								var y = $('#diamond').height() * 8;
							}

							if(value.point_no == 8){
								var x = $('#diamond').width() / 6.8;
								var y = $('#diamond').height() * 9;
							}

							if(value.point_no == 9){
								var x = $('#diamond').width() / 1.55;
								var y = $('#diamond').height() * 6.6;
							}

							if(value.point_no == 10){
								var x = $('#diamond').width() / 1.55;
								var y = $('#diamond').height() * 7.3;
							}

							if(value.point_no == 11){
								var x = $('#diamond').width() / 1.55;
								var y = $('#diamond').height() * 8.1;
							}

							if(value.point_no == 12){
								var x = $('#diamond').width() / 1.55;
								var y = $('#diamond').height() * 8.9;
							}

							if(value.point_no == 13){
								var x = $('#diamond').width() / 1.55;
								var y = $('#diamond').height() * 9.7;
							}

							var div = document.getElementById('dot_diamond');
							div.style.left = x + 'px';
							div.style.top = y + 'px';
						}

						if(value.point_title == '10_komitmen'){
							point_data += '<div id="dot_10_komitmen" class="dot">';
							point_data += '<img src="{{url("/images/pointing_calls/arrow.gif")}}" width="100%">';
							point_data += '</div>';
							$('#10_komitmen').append(point_data);

							if(value.point_no == 1){
								var x = $('#diamond').width() / 18.5;
								var y = $('#diamond').height() * 1.5;
							}

							if(value.point_no == 2){
								var x = $('#diamond').width() / 18.5;
								var y = $('#diamond').height() * 4;
							}

							if(value.point_no == 3){
								var x = $('#diamond').width() / 18.5;
								var y = $('#diamond').height() * 4.7;
							}

							if(value.point_no == 4){
								var x = $('#diamond').width() / 18.5;
								var y = $('#diamond').height() * 5.5;
							}

							if(value.point_no == 5){
								var x = $('#diamond').width() / 18.5;
								var y = $('#diamond').height() * 6.2;
							}

							if(value.point_no == 6){
								var x = $('#diamond').width() / 18.5;
								var y = $('#diamond').height() * 7.2;
							}

							if(value.point_no == 7){
								var x = $('#diamond').width() / 18.5;
								var y = $('#diamond').height() * 8.2;
							}

							if(value.point_no == 8){
								var x = $('#diamond').width() / 18.5;
								var y = $('#diamond').height() * 9.1;
							}

							if(value.point_no == 9){
								var x = $('#diamond').width() / 18.5;
								var y = $('#diamond').height() * 10.4;
							}

							if(value.point_no == 10){
								var x = $('#diamond').width() / 18.5;
								var y = $('#diamond').height() * 11.3;
							}

							var div = document.getElementById('dot_10_komitmen');
							div.style.left = x + 'px';
							div.style.top = y + 'px';
						}

						if(value.point_title == 'k3'){
							point_data += '<div id="dot_k3" class="dot">';
							point_data += '<img src="{{url("/images/pointing_calls/arrow.gif")}}" width="100%">';
							point_data += '</div>';
							$('#k3').append(point_data);

							if(value.point_no == 1){
								var x = $('#k3').width() / 18.5;
								var y = $('#k3').height() * 10.5;
							}

							if(value.point_no == 2){
								var x = $('#k3').width() / 18.5;
								var y = $('#k3').height() * 15.5;
							}

							if(value.point_no == 3){
								var x = $('#k3').width() / 18.5;
								var y = $('#k3').height() * 21.5;
							}

							if(value.point_no == 4){
								var x = $('#k3').width() / 18.5;
								var y = $('#k3').height() * 26;
							}

							if(value.point_no == 5){
								var x = $('#k3').width() / 18.5;
								var y = $('#k3').height() * 31;
							}

							if(value.point_no == 6){
								var x = $('#k3').width() / 18.5;
								var y = $('#k3').height() * 37;
							}

							if(value.point_no == 7){
								var x = $('#k3').width() / 18.5;
								var y = $('#k3').height() * 41.2;
							}

							if(value.point_no == 8){
								var x = $('#k3').width() / 18.5;
								var y = $('#k3').height() * 45.8;
							}

							if(value.point_no == 9){
								var x = $('#k3').width() / 18.5;
								var y = $('#k3').height() * 51;
							}

							if(value.point_no == 10){
								var x = $('#k3').width() / 18.5;
								var y = $('#k3').height() * 55.5;
							}

							if(value.point_no == 11){
								var x = $('#k3').width() / 18.5;
								var y = $('#k3').height() * 60;
							}

							if(value.point_no == 12){
								var x = $('#k3').width() / 18.5;
								var y = $('#k3').height() * 64.6;
							}

							var div = document.getElementById('dot_k3');
							div.style.left = x + 'px';
							div.style.top = y + 'px';
						}

						if(value.point_title == '6_pasal'){
							point_data += '<div id="dot_6_pasal" class="dot">';
							point_data += '<img src="{{url("/images/pointing_calls/arrow.gif")}}" width="100%">';
							point_data += '</div>';
							$('#6_pasal').append(point_data);

							if(value.point_no == 1){
								var x = $('#6_pasal').width() / 12;
								var y = $('#6_pasal').height() * 14;
							}

							if(value.point_no == 2){
								var x = $('#6_pasal').width() / 12;
								var y = $('#6_pasal').height() * 20.5;
							}

							if(value.point_no == 3){
								var x = $('#6_pasal').width() / 12;
								var y = $('#6_pasal').height() * 29;
							}

							if(value.point_no == 4){
								var x = $('#6_pasal').width() / 12;
								var y = $('#6_pasal').height() * 37;
							}

							if(value.point_no == 5){
								var x = $('#6_pasal').width() / 12;
								var y = $('#6_pasal').height() * 46;
							}

							if(value.point_no == 6){
								var x = $('#6_pasal').width() / 12;
								var y = $('#6_pasal').height() * 55;
							}

							var div = document.getElementById('dot_6_pasal');
							div.style.left = x + 'px';
							div.style.top = y + 'px';
						}

						if(value.point_title == 'komitmen'){
							point_data += '<div id="dot_komitmen" class="dot">';
							point_data += '<img src="{{url("/images/pointing_calls/arrow.gif")}}" width="100%">';
							point_data += '</div>';
							$('#komitmen').append(point_data);

							if(value.point_no == 1){
								var x = $('#komitmen').width() / 100;
								var y = $('#komitmen').height() * 25;
							}

							if(value.point_no == 2){
								var x = $('#komitmen').width() / 100;
								var y = $('#komitmen').height() * 30;
							}

							if(value.point_no == 3){
								var x = $('#komitmen').width() / 100;
								var y = $('#komitmen').height() * 35;
							}

							if(value.point_no == 4){
								var x = $('#komitmen').width() / 100;
								var y = $('#komitmen').height() * 40;
							}

							if(value.point_no == 5){
								var x = $('#komitmen').width() / 100;
								var y = $('#komitmen').height() * 45;
							}

							if(value.point_no == 6){
								var x = $('#komitmen').width() / 100;
								var y = $('#komitmen').height() * 50;
							}

							if(value.point_no == 7){
								var x = $('#komitmen').width() / 100;
								var y = $('#komitmen').height() * 55;
							}

							if(value.point_no == 8){
								var x = $('#komitmen').width() / 100;
								var y = $('#komitmen').height() * 60;
							}

							if(value.point_no == 9){
								var x = $('#komitmen').width() / 100;
								var y = $('#komitmen').height() * 65;
							}

							if(value.point_no == 10){
								var x = $('#komitmen').width() / 100;
								var y = $('#komitmen').height() * 70;
							}

							var div = document.getElementById('dot_komitmen');
							div.style.left = x + 'px';
							div.style.top = y + 'px';
						}

						if(value.point_title == 'janji'){
							point_data += '<div id="dot_janji" class="dot">';
							point_data += '<img src="{{url("/images/pointing_calls/arrow.gif")}}" width="100%">';
							point_data += '</div>';
							$('#janji').append(point_data);

							if(value.point_no == 1){
								var x = -5;
								var y = $('#janji').height() * 19;
							}

							if(value.point_no == 2){
								var x = -5;
								var y = $('#janji').height() * 23;
							}

							if(value.point_no == 3){
								var x = -5;
								var y = $('#janji').height() * 28;
							}

							if(value.point_no == 4){
								var x = -5;
								var y = $('#janji').height() * 33;
							}

							if(value.point_no == 5){
								var x = -5;
								var y = $('#janji').height() * 37.5;
							}

							if(value.point_no == 6){
								var x = -5;
								var y = $('#janji').height() * 42;
							}

							if(value.point_no == 7){
								var x = -5;
								var y = $('#janji').height() * 46.5;
							}

							if(value.point_no == 8){
								var x = -5;
								var y = $('#janji').height() * 51;
							}

							if(value.point_no == 9){
								var x = -5;
								var y = $('#janji').height() * 55.5;
							}

							if(value.point_no == 10){
								var x = -5;
								var y = $('#janji').height() * 60;
							}

							if(value.point_no == 11){
								var x = -5;
								var y = $('#janji').height() * 65;
							}

							if(value.point_no == 12){
								var x = -5;
								var y = $('#janji').height() * 70;
							}

							var div = document.getElementById('dot_janji');
							div.style.left = x + 'px';
							div.style.top = y + 'px';
						}

						if(value.point_title == 'budaya'){
							point_data += '<div id="dot_budaya" class="dot">';
							point_data += '<img src="{{url("/images/pointing_calls/arrow.gif")}}" width="100%">';
							point_data += '</div>';
							$('#budaya').append(point_data);

							if(value.point_no == 1){
								var x = -5;
								var y = $('#budaya').height() * 3;
							}

							if(value.point_no == 2){
								var x = -5;
								var y = $('#budaya').height() * 5;
							}

							if(value.point_no == 3){
								var x = -5;
								var y = $('#budaya').height() * 7.5;
							}

							if(value.point_no == 4){
								var x = -5;
								var y = $('#budaya').height() * 9.5;
							}

							if(value.point_no == 5){
								var x = -5;
								var y = $('#budaya').height() * 11.8;
							}

							if(value.point_no == 6){
								var x = -5;
								var y = $('#budaya').height() * 14;
							}

							if(value.point_no == 7){
								var x = -5;
								var y = $('#budaya').height() * 18;
							}

							if(value.point_no == 8){
								var x = -5;
								var y = $('#budaya').height() * 20;
							}

							if(value.point_no == 9){
								var x = -5;
								var y = $('#budaya').height() * 22;
							}

							if(value.point_no == 10){
								var x = -5;
								var y = $('#budaya').height() * 24.5;
							}

							if(value.point_no == 11){
								var x = -5;
								var y = $('#budaya').height() * 29;
							}

							if(value.point_no == 12){
								var x = -5;
								var y = $('#budaya').height() * 32;
							}

							if(value.point_no == 13){
								var x = -5;
								var y = $('#budaya').height() * 34.5;
							}

							if(value.point_no == 14){
								var x = -5;
								var y = $('#budaya').height() * 38;
							}

							var div = document.getElementById('dot_budaya');
							div.style.left = x + 'px';
							div.style.top = y + 'px';
						}
					});
}

var curr = 0;
$(function() {
	$(document).keydown(function(e) {
		switch(e.which) {
			case 13:

			var c = curr+1;

			if (c == 1){
				$(window).scrollTop($(".main-header").offset().top);
			}
			else{
				$(window).scrollTop($("[name='"+c+"']").offset().top);				
			}

			curr += 1;

			if(curr == count-1){
				curr = 0;
			}

			break;
		}
	});
});				
}
else{
	$('#loading').show();
}
});
}
</script>
@endsection