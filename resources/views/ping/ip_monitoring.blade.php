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
		padding-top: 1px;
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

			<div class="col-xs-12" style="padding: 5px">	
				<form method="GET" action="{{ action('PingController@indexIpMonitoring') }}">
					<div class="col-md-2" style="padding: 0">
				           <select class="form-control select2" multiple="multiple" id="locationSelect" data-placeholder="Select Locations" onchange="changeLocation()" style="width: 100%;"> 	
				            	@foreach($location as $loc)
				            	<option value="{{$loc->location}}">{{ trim($loc->location, "'")}}</option>
				           		@endforeach
				           </select>
						   <input type="text" name="location" id="location" hidden>	
				      </div>

					<div class="col-xs-2">
						<button class="btn btn-success" type="submit"><i class="fa fa-search"></i> Search</button>
					</div>
					<div class="pull-right" id="loc" style="margin: 0px;padding-top: 0px;padding-right: 20px;font-size: 1.6vw;"></div>
				</form>
			 </div>

			<div id="ip_list">
			</div>

			<!-- <?php $i = 0; foreach ($ip as $ip){ ?>

				<div class="col-md-3 col-sm-3 col-xs-6" style="padding: 5px">
					<div class="info-box" id="box_{{ $ip->remark }}">
						<span class="info-box-icon" style="height:108px">
							<img src="{{ url('images/ping', $ip->image) }}" style="padding: 10px">
						</span>
						<div class="info-box-content" > 
							<span class="info-box-text">{{ $ip->remark }}</span>
							<span class="info-box-number">{{ $ip->ip }}</span>
							<div class="progress">
								<div class="progress-bar" style="width: 100%"></div>
							</div>
							<span class="progress-description" id="status_{{ $ip->remark }}">Good</span>  <span id="time_{{ $ip->remark }}"> </span> ms
						</div>
					</div>
				</div>

		    <?php $i++; } ?> -->
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
		        <!-- <div class="col-md-2 col-sm-3 col-xs-6">
		        	<div class="info-box" style=" display: flex;">
		        		<div class="col-sm-5" style="background-color: black;  opacity: 0.5;">
		        			<img src="{{ url('images/ping', $ip->image) }}" style="padding: 10px">
		        		</div>
		        		<div class="col-sm-7">
		        			d 	<br>
		        			d 	<br>
		        			d 	<br>
		        			d 	<br>
		        			d 	<br>
		        		</div>
		        	</div> -->
		        	<!-- /.info-box
		        </div> -->

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
			setInterval(fetchip, 45000);

    		$('.select2').select2();
		});

		function changeLocation(){
			$("#location").val($("#locationSelect").val());
		}

		function fetchip(){

			var location = "{{$_GET['location']}}";

			var data = {
				location:location
			}

			$.get('{{ url("fetch/display/ip") }}', data, function(result, status, xhr){
				if(result.status){

					var title = result.title;
					$('#loc').html('<b style="color:white">'+ title +'</b>');
					$('#ip_list').html("");
					
					$.each(result.data, function(key, value){

						var divdata = $("<div class='col-md-3 col-sm-3 col-xs-6' style='padding: 5px'><div class='info-box' id='box_"+value.remark+"'><span class='info-box-icon' style='height:108px'><img src='{{ url('images/ping') }}/"+value.image+"' style='padding: 10px'></span><div class='info-box-content'><span class='info-box-text'>"+value.remark+"</span><span class='info-box-number'>"+value.ip+"</span><div class='progress'><div class='progress-bar' style='width: 100%'></div></div><span class='progress-description' id='status_"+value.remark+"'>Good</span>  <span id='time_"+value.remark+"'> </span> ms</div></div></div>");

						$('#ip_list').append(divdata);

						var url = '{{ url("fetch/display/fetch_hit") }}'+'/'+value.ip;

						$.get(url, function(result, status, xhr){
							var time;

							if (result.sta == 0) {
								if (result.output.length == 8) {
									timearray = /time\=(.*)?ms|time\<(.*)?ms /g.exec(result.output[2]);
								// (?<=This is)(.*)(?=sentence)
								// console.log(timearray);
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
								// openSuccessGritter("Success","IP Log Created");
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