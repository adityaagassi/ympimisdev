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

<div class="modal fade" id="modalImage">
	<div class="modal-dialog" style="width: 90%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body" id="modalImageBody">

			</div>
		</div>
	</div>
</div>

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

	function modalImage(id){
		$('#modalImageBody').html('');
		$('#modalImageBody').append('<img src="{{ asset('images/pointing_calls') }}/'+id+'.jpg" style="width: 100%;">');
		$('#modalImage').modal('show');
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
						pic_data += '<button onClick="editPIC(\''+value.id+'\''+','+'\''+value.point_title+'\''+','+'\''+value.location+'\')" class="btn btn-lg" style="border-color: black; width: 18%; font-weight: bold; background-color: orange; padding: 2px 5px 2px 5px; margin-left: 5px;">'+value.point_description+'<br>'+value.point_description_jp+'</button>';
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
				$('.content').append(image_data);

				var navigation_data = '';

				navigation_data += '<div class="col-xs-12" style="position: absolute; top: 1650px;"><center>';
				navigation_data += '<a style="margin: 5px;" href="javascript:void(0)" onclick="modalImage(\'alur_pasca_kecelakaan_jp\');" class="btn btn-lg btn-success">Alur Pelaporan Pasca Kecelakaan<br>交通事故の報告のフロー</a>';
				navigation_data += '<a style="margin: 5px;" href="javascript:void(0)" onclick="modalImage(\'iso9001_jp.jpg\');" class="btn btn-lg btn-success">Target Kualitas FY197<br>197期　品質目標</a>';
				navigation_data += '<a style="margin: 5px;" href="javascript:void(0)" onclick="modalImage(\'petunjuk_alarm_kebakaran_jp\');" class="btn btn-lg btn-success">Panduan Kondisi Alarm Kebakaran<br>火災警報が鳴った場合のガイドライン</a>';
				navigation_data += '<a style="margin: 5px;" href="javascript:void(0)" onclick="modalImage(\'petunjuk_keadaan_emergency_jp\');" class="btn btn-lg btn-success">Panduan Kondisi Darurat<br>火災発生のガイドライン</a>';
				navigation_data += '<a style="margin: 5px;" href="javascript:void(0)" onclick="modalImage(\'slogan_mutu_jp\');" class="btn btn-lg btn-success">Slogan Mutu<br>品質スローガン</a>';
				navigation_data += '<a style="margin: 5px;" href="javascript:void(0)" onclick="modalImage(\'susunan_organisasi_jp\');" class="btn btn-lg btn-success">Safety Comitee<br>安全委員会</a>';
				navigation_data += '<a style="margin: 5px;" href="javascript:void(0)" onclick="modalImage(\'zero_complain_jp\');" class="btn btn-lg btn-success">Zero Complain<br>ゼロ・クレーム</a>';
				navigation_data += '</center></div>';

				$('.content').append(navigation_data);

				$.each(result.pointing_calls, function(key, value){
					var point_data = '';

					if(value.point_title == 'diamond'){
						point_data += '<div id="dot_diamond" class="dot">';
						point_data += '<img src="{{url("/images/pointing_calls/arrow.gif")}}" width="100%">';
						point_data += '</div>';
						$('#diamond').append(point_data);

						if(value.point_no == 1){
							var x = 400;
							var y = 930;
						}

						if(value.point_no == 2){
							var x = 400;
							var y = 340;
						}

						if(value.point_no == 3){
							var x = 400;
							var y = 400;
						}

						if(value.point_no == 4){
							var x = 400;
							var y = 460;
						}

						if(value.point_no == 5){
							var x = 400;
							var y = 520;
						}

						if(value.point_no == 6){
							var x = 150;
							var y = 650;
						}

						if(value.point_no == 7){
							var x = 150;
							var y = 740;
						}

						if(value.point_no == 8){
							var x = 150;
							var y = 820;
						}

						if(value.point_no == 9){
							var x = 700;
							var y = 620;
						}

						if(value.point_no == 10){
							var x = 700;
							var y = 680;
						}

						if(value.point_no == 11){
							var x = 700;
							var y = 745;
						}

						if(value.point_no == 12){
							var x = 700;
							var y = 810;
						}

						if(value.point_no == 13){
							var x = 700;
							var y = 880;
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
							var x = 60;
							var y = 390;
						}

						if(value.point_no == 2){
							var x = 60;
							var y = 452;
						}

						if(value.point_no == 3){
							var x = 60;
							var y = 515;
						}

						if(value.point_no == 4){
							var x = 60;
							var y = 578;
						}

						if(value.point_no == 5){
							var x = 60;
							var y = 638;
						}

						if(value.point_no == 6){
							var x = 60;
							var y = 725;
						}

						if(value.point_no == 7){
							var x = 60;
							var y = 805;
						}

						if(value.point_no == 8){
							var x = 60;
							var y = 885;
						}

						if(value.point_no == 9){
							var x = 60;
							var y = 985;
						}

						if(value.point_no == 10){
							var x = 60;
							var y = 1070;
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
							var y = 330;
						}

						if(value.point_no == 2){
							var x = 50;
							var y = 420;
						}

						if(value.point_no == 3){
							var x = 50;
							var y = 540;
						}

						if(value.point_no == 4){
							var x = 50;
							var y = 635;
						}

						if(value.point_no == 5){
							var x = 50;
							var y = 725;
						}

						if(value.point_no == 6){
							var x = 50;
							var y = 845;
						}

						if(value.point_no == 7){
							var x = 50;
							var y = 935;
						}

						if(value.point_no == 8){
							var x = 50;
							var y = 1025;
						}

						if(value.point_no == 9){
							var x = 50;
							var y = 1115;
						}

						if(value.point_no == 10){
							var x = 50;
							var y = 1205;
						}

						if(value.point_no == 11){
							var x = 50;
							var y = 1295;
						}

						if(value.point_no == 12){
							var x = 50;
							var y = 1385;
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
							var y = 390;
						}

						if(value.point_no == 2){
							var x = 90;
							var y = 515;
						}

						if(value.point_no == 3){
							var x = 90;
							var y = 680;
						}

						if(value.point_no == 4){
							var x = 90;
							var y = 845;
						}

						if(value.point_no == 5){
							var x = 90;
							var y = 1015;
						}

						if(value.point_no == 6){
							var x = 90;
							var y = 1180;
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
							var y = 620;
						}

						if(value.point_no == 2){
							var x = 0;
							var y = 715;
						}

						if(value.point_no == 3){
							var x = 0;
							var y = 810;
						}

						if(value.point_no == 4){
							var x = 0;
							var y = 915;
						}

						if(value.point_no == 5){
							var x = 0;
							var y = 1010;
						}

						if(value.point_no == 6){
							var x = 0;
							var y = 1110;
						}

						if(value.point_no == 7){
							var x = 0;
							var y = 1210;
						}

						if(value.point_no == 8){
							var x = 0;
							var y = 1305;
						}

						if(value.point_no == 9){
							var x = 0;
							var y = 1405;
						}

						if(value.point_no == 10){
							var x = 0;
							var y = 1505;
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
							var y = 500;
						}

						if(value.point_no == 2){
							var x = -5;
							var y = 595;
						}

						if(value.point_no == 3){
							var x = -5;
							var y = 670;
						}

						if(value.point_no == 4){
							var x = -5;
							var y = 780;
						}

						if(value.point_no == 5){
							var x = -5;
							var y = 870;
						}

						if(value.point_no == 6){
							var x = -5;
							var y = 960;
						}

						if(value.point_no == 7){
							var x = -5;
							var y = 1050;
						}

						if(value.point_no == 8){
							var x = -5;
							var y = 1140;
						}

						if(value.point_no == 9){
							var x = -5;
							var y = 1230;
						}

						if(value.point_no == 10){
							var x = -5;
							var y = 1320;
						}

						if(value.point_no == 11){
							var x = -5;
							var y = 1410;
						}

						if(value.point_no == 12){
							var x = -5;
							var y = 1490;
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
							var y = 185;
						}

						if(value.point_no == 2){
							var x = -5;
							var y = 225;
						}

						if(value.point_no == 3){
							var x = -5;
							var y = 265;
						}

						if(value.point_no == 4){
							var x = -5;
							var y = 310;
						}

						if(value.point_no == 5){
							var x = -5;
							var y = 355;
						}

						if(value.point_no == 6){
							var x = -5;
							var y = 400;
						}

						if(value.point_no == 7){
							var x = -5;
							var y = 470;
						}

						if(value.point_no == 8){
							var x = -5;
							var y = 515;
						}

						if(value.point_no == 9){
							var x = -5;
							var y = 560;
						}

						if(value.point_no == 10){
							var x = -5;
							var y = 600;
						}

						if(value.point_no == 11){
							var x = -5;
							var y = 680;
						}

						if(value.point_no == 12){
							var x = -5;
							var y = 755;
						}

						if(value.point_no == 13){
							var x = -5;
							var y = 800;
						}

						if(value.point_no == 14){
							var x = -5;
							var y = 870;
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