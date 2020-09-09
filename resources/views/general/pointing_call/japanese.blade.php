@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	.dot {
		height: 5%;
		width: 5%;
		position: absolute;
		z-index: 10;
	}
	.content-wrapper {
		background-color: white !important;
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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
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
		$(document).bind("contextmenu",function(e){
			return false;
		}); 
	});

	function clearConfirmation(){
		location.reload(true);		
	}

	function editPIC(id, point_title, location){
		$('#loading').show();
		var data = {
			id:id,
			point_title:point_title,
			location:location
		}
		$.post('{{ url("edit/general/pointing_call_pic") }}', data, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				clearConfirmation();
			}
			else{
				$('#loading').hide();
				alert(result.message);
			}
		});
	}

	function fetchPoint(){
		var location = $('#location').val();
		var data = {
			location: location
		}

		$.get('{{ url("fetch/general/pointing_call") }}', data, function(result, status, xhr){
			if(result.status){
				$('.content').html('');

				var pic_data = '';
				pic_data += '<div class="col-xs-12" style="padding-bottom: 10px;" id="pic_cok">';
				pic_data += '<center>';
				$.each(result.pics, function(key, value){
					if(value.remark == 1){
						pic_data += '<button onCLick="editPIC(\''+value.id+'\''+','+'\''+value.point_title+'\''+','+'\''+value.location+'\')" class="btn btn-lg" style="border-color: black; width: 18%; font-weight: bold; background-color: orange; padding: 2px 5px 2px 5px; margin-left: 5px;">'+value.point_description+'<br>'+value.point_description_jp+'</button>';
					}
					else{
						pic_data += '<button onCLick="editPIC(\''+value.id+'\''+','+'\''+value.point_title+'\''+','+'\''+value.location+'\')" class="btn btn-lg" style="border-color: black; width: 18%; font-weight: bold; background-color: white; padding: 2px 5px 2px 5px; margin-left: 5px;">'+value.point_description+'<br>'+value.point_description_jp+'</button>';
					}
				});
				pic_data += '</center>';
				pic_data += '</div>';
				$('.content').append(pic_data);

				var h = $('#pic_cok').height()+$('.navbar-header').height();

				var count = 1;
				var image_data = '';

				$.each(result.pointing_calls, function(key, value){
					image_data += '<div class="row" id="'+value.point_title+'" name="'+count+'" tabindex="1">';
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
							var x = 50;
							var y = 330;
						}

						if(value.point_no == 2){
							var x = 50;
							var y = 392;
						}

						if(value.point_no == 3){
							var x = 50;
							var y = 455;
						}

						if(value.point_no == 4){
							var x = 50;
							var y = 518;
						}

						if(value.point_no == 5){
							var x = 50;
							var y = 578;
						}

						if(value.point_no == 6){
							var x = 50;
							var y = 665;
						}

						if(value.point_no == 7){
							var x = 50;
							var y = 745;
						}

						if(value.point_no == 8){
							var x = 50;
							var y = 825;
						}

						if(value.point_no == 9){
							var x = 50;
							var y = 925;
						}

						if(value.point_no == 10){
							var x = 50;
							var y = 1010;
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
							var x = 50;
							var y = 270;
						}

						if(value.point_no == 2){
							var x = 50;
							var y = 360;
						}

						if(value.point_no == 3){
							var x = 50;
							var y = 485;
						}

						if(value.point_no == 4){
							var x = 50;
							var y = 575;
						}

						if(value.point_no == 5){
							var x = 50;
							var y = 665;
						}

						if(value.point_no == 6){
							var x = 50;
							var y = 785;
						}

						if(value.point_no == 7){
							var x = 50;
							var y = 875;
						}

						if(value.point_no == 8){
							var x = 50;
							var y = 965;
						}

						if(value.point_no == 9){
							var x = 50;
							var y = 1055;
						}

						if(value.point_no == 10){
							var x = 50;
							var y = 1145;
						}

						if(value.point_no == 11){
							var x = 50;
							var y = 1235;
						}

						if(value.point_no == 12){
							var x = 50;
							var y = 1325;
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
							var x = 90;
							var y = 330;
						}

						if(value.point_no == 2){
							var x = 90;
							var y = 455;
						}

						if(value.point_no == 3){
							var x = 90;
							var y = 620;
						}

						if(value.point_no == 4){
							var x = 90;
							var y = 790;
						}

						if(value.point_no == 5){
							var x = 90;
							var y = 955;
						}

						if(value.point_no == 6){
							var x = 90;
							var y = 1120;
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
							var x = 0;
							var y = 560;
						}

						if(value.point_no == 2){
							var x = 0;
							var y = 655;
						}

						if(value.point_no == 3){
							var x = 0;
							var y = 750;
						}

						if(value.point_no == 4){
							var x = 0;
							var y = 855;
						}

						if(value.point_no == 5){
							var x = 0;
							var y = 950;
						}

						if(value.point_no == 6){
							var x = 0;
							var y = 1050;
						}

						if(value.point_no == 7){
							var x = 0;
							var y = 1150;
						}

						if(value.point_no == 8){
							var x = 0;
							var y = 1245;
						}

						if(value.point_no == 9){
							var x = 0;
							var y = 1345;
						}

						if(value.point_no == 10){
							var x = 0;
							var y = 1445;
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
							var y = 440;
						}

						if(value.point_no == 2){
							var x = -5;
							var y = 535;
						}

						if(value.point_no == 3){
							var x = -5;
							var y = 630;
						}

						if(value.point_no == 4){
							var x = -5;
							var y = 720;
						}

						if(value.point_no == 5){
							var x = -5;
							var y = 810;
						}

						if(value.point_no == 6){
							var x = -5;
							var y = 900;
						}

						if(value.point_no == 7){
							var x = -5;
							var y = 990;
						}

						if(value.point_no == 8){
							var x = -5;
							var y = 1080;
						}

						if(value.point_no == 9){
							var x = -5;
							var y = 1170;
						}

						if(value.point_no == 10){
							var x = -5;
							var y = 1260;
						}

						if(value.point_no == 11){
							var x = -5;
							var y = 1350;
						}

						if(value.point_no == 12){
							var x = -5;
							var y = 1440;
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
							var y = 125;
						}

						if(value.point_no == 2){
							var x = -5;
							var y = 165;
						}

						if(value.point_no == 3){
							var x = -5;
							var y = 205;
						}

						if(value.point_no == 4){
							var x = -5;
							var y = 250;
						}

						if(value.point_no == 5){
							var x = -5;
							var y = 295;
						}

						if(value.point_no == 6){
							var x = -5;
							var y = 340;
						}

						if(value.point_no == 7){
							var x = -5;
							var y = 410;
						}

						if(value.point_no == 8){
							var x = -5;
							var y = 455;
						}

						if(value.point_no == 9){
							var x = -5;
							var y = 500;
						}

						if(value.point_no == 10){
							var x = -5;
							var y = 540;
						}

						if(value.point_no == 11){
							var x = -5;
							var y = 620;
						}

						if(value.point_no == 12){
							var x = -5;
							var y = 695;
						}

						if(value.point_no == 13){
							var x = -5;
							var y = 740;
						}

						if(value.point_no == 14){
							var x = -5;
							var y = 810;
						}

						var div = document.getElementById('dot_budaya');
						div.style.left = x + 'px';
						div.style.top = y + 'px';
					}

					for (var i = 2; i <= count; i++) {
						$("[name='"+i+"']").hide();	
					}
				});

var curr = 0;
$(function() {
	$(document).keydown(function(e) {
		switch(e.which) {
			case 13:

			var c = curr+1;

			for (var i = 1; i <= count; i++) {
				$("[name='"+i+"']").hide();	
			}

			$("[name='"+c+"']").show();	

			curr += 1;

			if(curr == count-1){
				curr = 0;
			}

			break;
		}
	});
});

$(function() {
	$(document).mousedown(function(e) {
		switch(e.which) {
			case 3:

			var c = curr+1;

			for (var i = 1; i <= count; i++) {
				$("[name='"+i+"']").hide();	
			}

			$("[name='"+c+"']").show();	

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