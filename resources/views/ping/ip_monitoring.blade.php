@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:none;
		background-color: rgba(126,86,134);
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
	.content{
		color: white;
		font-weight: bold;
	}
	.patient-duration{
		margin: 0px;
		padding: 0px;
	}

	.ada{
		background-color: rgba(118,255,3,.65);
	}
	.tidak-ada{
		background-color: rgba(255,0,0,.85);
	}

	.server {
		width: 100px;
		height: 160px;
		background-color: rgba(57,73,171 ,.6);
		border-radius: 15px;
		margin-top: 15px;
		display: inline-block;
		border: 2px solid white;
	}

	.server img {
		width: 85px;
		height: 110px;
		margin-top: 10px; 
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
		font-size: 15px;
		vertical-align: top;
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple"> {{ $title_jp }}</span></small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-xs-12">	
			<?php $i = 0; foreach ($ip as $ip){ ?>

			<div class="col-md-2 col-sm-3 col-xs-6">
		          <div class="info-box" id="box_{{ $ip->remark }}">
		            <span class="info-box-icon" style="height: 100px"><img src="{{ url('images', $ip->image) }}" style="padding: 10px"></span>

		            <div class="info-box-content" > <!-- style="color: #333" -->
		              <span class="info-box-text" style="font-size: 1vw">{{ $ip->remark }}</span>
		              <span class="info-box-number" style="font-size: 1vw">{{ $ip->ip }}</span>

		              <div class="progress">
		                <div class="progress-bar" style="width: 100%"></div>
		              </div>
		              <span class="progress-description" id="status_{{ $ip->remark }}">Good </span> <span id="time_{{ $ip->remark }}"> </span> ms
		            </div>
		          </div>
		          <!-- /.info-box -->
		        </div>

				<!-- <div class="col-xs-4" style="padding: 0px;">
					<div class="info-box bg-green">
			            <span class="info-box-icon">							
			            	
			            </span>

			            <div class="info-box-content">
			              <span class="info-box-text">{{ $ip->remark }}</span>
			              <span class="info-box-number">{{ $ip->ip }}</span>

			              <div class="progress">
			                <div class="progress-bar" style="width: 100%"></div>
			              </div>
			              <span class="progress-description" id="status">Good</span>
			            </div>
		            </div>	
				</div> -->
			<?php $i++; } ?>
			
			
		</div>

	</div>

</section>

@endsection
@section('scripts')
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		fetchip();
		setInterval(fetchip, 10000);

	});

	function fetchip(){
		$.get('{{ url("fetch/display/ip") }}', function(result, status, xhr){
			if(result.status){
				$.each(result.data, function(key, value){
					var url = '{{ url("fetch/display/fetch_hit") }}'+'/'+value.ip;
					$.get(url, function(result, status, xhr){
						var time;

						if (result.sta == 0) {
							if (result.output.length == 8) {
								timearray = /time\=(.*)?ms|time\<(.*)?ms /g.exec(result.output[2]);
								// (?<=This is)(.*)(?=sentence)
								console.log(timearray);
								if(timearray[1] != undefined){
									time = timearray[1];
								}else if(timearray[2] != undefined){
									time = timearray[2];
								}
								status = "Alive";
							}
							else{
								time = 0;
								status = "Host Unreachable";
							}
						}
						else{
							status = "Timed Out";
							time = 0;
						}
						
						var data = {
							ip : value.ip,
							remark : value.remark,
							hasil_hit : time,
							status : status
						}

						$.post('{{ url("post/display/ip_log") }}', data, function(result, status, xhr){
							if(result.status){
								openSuccessGritter("Success","IP Log Created");
							} else {
								// audio_error.play();
								openErrorGritter('Error',result.message);
							}
						});

						if (true) {}

						$('#status_'+value.remark).append().empty();
						$('#status_'+value.remark).html(status);

						$('#time_'+value.remark).append().empty();
						$('#time_'+value.remark).html(time);

						if(status == "Alive") {
							$("#box_"+value.remark).addClass("bg-green");	
							$("#box_"+value.remark).removeClass('bg-orange');	
							$("#box_"+value.remark).removeClass('bg-red');						
						}
						else if(status == "Host Unreachable"){
							$("#box_"+value.remark).addClass("bg-orange");
							$("#box_"+value.remark).removeClass('bg-green');	
							$("#box_"+value.remark).removeClass('bg-red');	
						}
						else if(status == "Timed Out"){
							$("#box_"+value.remark).addClass("bg-red");
							$("#box_"+value.remark).removeClass('bg-green');	
							$("#box_"+value.remark).removeClass('bg-orange');	
						}


					});

				});
			}
		});
	}

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