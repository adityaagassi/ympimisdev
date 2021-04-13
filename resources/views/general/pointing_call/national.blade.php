@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	.content-wrapper {
		background-color: white !important;
		padding-top: 0 !important;
	}
	#loading { display: none; }
</style>
@stop
@section('header')
@endsection
@section('content')
<input type="hidden" id="location" value="{{ $location }}">
<input type="hidden" id="default_language" value="{{ $default_language }}">
<section class="content" style="padding-top: 50px;" id="coba">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: white; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<input type="hidden" id="location" value="{{ $location }}">
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
		$('body').addClass('fixed');
		fetchPoint();
		// $(document).bind("contextmenu",function(e){
		// 	return false;
		// }); 
	});

	function fetchPoint(){
		var location = $('#location').val();
		var data = {
			location:location
		}
		$.get('{{ url("fetch/general/pointing_call") }}', data, function(result, status, xhr){
			if(result.status){
				$('.content').html("");
				var count = 1;
				var image_data = "";
				var h = "";

				if(window.innerHeight > window.innerWidth){
					h = "100%";
				}
				else{
					h = "150vh";
				}

				$.each(result.pointing_calls, function(key, value){
					image_data += '<div class="row" id="'+value.point_title+'" name="'+count+'" tabindex="1" style="height: 100%;">';
					image_data += '<input type="hidden" name="inp_'+count+'" value="'+value.point_no/value.point_max+'">';
					image_data += '<center><img src="{{ asset('images/pointing_calls/national') }}/'+value.point_title+'_'+value.point_no+'.jpg" style="height: '+h+';"></center>';
					image_data += '</div>';
					count += 1;
				});

				$('.content').append(image_data);

				if($("[name='inp_1']").val() > 0.5){
					window.scrollTo(0, document.body.scrollHeight);	
				}
				if($("[name='inp_1']").val() <= 0.5){
					window.scrollTo(0, 0);
				}

				for(var i = 2; i <= count; i++){
					$("[name='"+i+"']").hide();	
				}

				var curr = 1;

				$('img').click(function() {
					if(curr == 1){
						curr = count;
					}

					if(curr <= 0){
						curr = 1;
					}

					var c = curr--;

					for (var i = 1; i <= count; i++) {
						$("[name='"+i+"']").hide();	
					}

					$("[name='"+curr+"']").show();


					curr = curr--;

					if($("[name='inp_"+curr+"']").val() > 0.5){
						window.scrollTo(0, document.body.scrollHeight);	
					}
					if($("[name='inp_"+curr+"']").val() <= 0.5){
						window.scrollTo(0, 0);
					}
				});

				$('img').bind("contextmenu",function(e){
					var c;

					if(curr == count-1){
						curr = 1;
						c = 1;
					}else{
						c = curr++;
					}

					for (var i = 1; i <= count; i++) {
						$("[name='"+i+"']").hide();	
					}

					$("[name='"+curr+"']").show();

					if($("[name='inp_"+curr+"']").val() > 0.5){
						window.scrollTo(0, document.body.scrollHeight);	
					}
					if($("[name='inp_"+curr+"']").val() <= 0.5){
						window.scrollTo(0, 0);
					}
					return false;
				}); 

				$(function() {
					$(document).keydown(function(e) {
						switch(e.which) {
							case 39:

							var c;

							if(curr == count-1){
								curr = 1;
								c = 1;
							}else{
								c = curr++;
							}

							for (var i = 1; i <= count; i++) {
								$("[name='"+i+"']").hide();	
							}

							$("[name='"+curr+"']").show();

							if($("[name='inp_"+curr+"']").val() > 0.5){
								window.scrollTo(0, document.body.scrollHeight);	
							}
							if($("[name='inp_"+curr+"']").val() <= 0.5){
								window.scrollTo(0, 0);
							}

							break;

							case 37:
							if(curr == 1){
								curr = count;
							}

							if(curr <= 0){
								curr = 1;
							}

							var c = curr--;

							for (var i = 1; i <= count; i++) {
								$("[name='"+i+"']").hide();	
							}

							$("[name='"+curr+"']").show();


							curr = curr--;

							if($("[name='inp_"+curr+"']").val() > 0.5){
								window.scrollTo(0, document.body.scrollHeight);	
							}
							if($("[name='inp_"+curr+"']").val() <= 0.5){
								window.scrollTo(0, 0);
							}

							break;

						}
					});
				});
			}
			else{
				alert('Unidentified ERROR!')
			}
		});
	}

</script>
@endsection