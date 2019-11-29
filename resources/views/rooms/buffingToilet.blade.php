@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	.gambar {
		width: 200px;
		height: 350px;
		background-color: white;
		border-radius: 15px;
		margin-left: 30px;
		margin-top: 15px;
		display: inline-block;
		border: 2px solid white;
	}

	.gambar img {
		max-width:87%; 
		height:auto;
		display: block;
		margin-left: auto;
		margin-right: auto;
		vertical-align:middle;
	}

	.content-wrapper {
		padding: 0px !important;
	}

	.text_stat {
		color: white;
		text-align: center;
		font-weight: bold;
		font-size: 30px;
		vertical-align: top;
	}
</style>
@endsection

@section('content')
<section class="content" style="padding-top: 0px">
	<div class="row">
		<div class="col-xs-12" style="padding: 0 0 15px 45px;">
			<?php $male = 1; $female = 1;  for ($i=0; $i < 11; $i++) { 
				if ($i == 8) { ?>
				<div class="gambar" style="background-color: transparent; border: 0px"></div>	
				<?php } ?>

				<div class="gambar" id="gambar_<?php echo $i?>">
					<?php 
					if ($i > 7) {
						echo '<img src="'.url("images/Gents.png").'" id="male_'.$male.'">';
						echo "<p class='text_stat' id='text_".$i."'></p>";
						echo "<p class='text_stat'><label id='minutes".$i."'>--</label>:<label id='seconds".$i."'>--</label></p>";
						$male += 1;
					} else {
						echo '<img src="'.url("images/Ladies.png").'" id="female_'.$female.'">';
						echo "<p class='text_stat' id='text_".$i."'></p>";
						echo "<p class='text_stat'><label id='minutes".$i."'>--</label>:<label id='seconds".$i."'>--</label></p>";
						$female += 1;
					}
					?>
				</div>
				<?php } ?>
			</div>
		</div>
	</section>

</div>

@stop

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		getToiletStatus();
		setTime();

		setInterval(getToiletStatus, 1000);
		setInterval(setTime, 1000);
	});


	var toilet = [];
	var totalSeconds = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];


	function setTime() {
		for (var i = 0; i < toilet.length; i++) {	
			if(toilet[i]){
				totalSeconds[i]++;
				document.getElementById("seconds"+i).innerHTML = pad(totalSeconds[i] % 60);
				document.getElementById("minutes"+i).innerHTML = pad(parseInt(totalSeconds[i] / 60));
			}else{
				document.getElementById("seconds"+i).innerHTML = '--';
				document.getElementById("minutes"+i).innerHTML = '--';
			}
		}
	}


	function pad(val) {
		var valString = val + "";
		if (valString.length < 2) {
			return "0" + valString;
		} else {
			return valString;
		}
	}


	function getToiletStatus() {
		$location = 'buffing';
		$data = {
			location : $location
		}


		$.get('{{ url("fetch/room/toilet") }}', $data,  function(result, status, xhr){
			toilet = [];
			$.each(result.datas, function(index, value){
				if (value == 1) {
					$("#gambar_"+index).css('background','rgb(240, 41, 61)');
					$("#text_"+index).text("OCCUPIED");
					toilet.push(true);					

				} else if (value == 0) {
					$("#gambar_"+index).css('background','rgb(50,205,50)');
					$("#text_"+index).text("VACANT");
					toilet.push(false);
					totalSeconds[index] = 0;

				}
			})
		})
	}
</script>
@stop