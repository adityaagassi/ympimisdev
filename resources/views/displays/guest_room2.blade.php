<style type="text/css">
	.content{
		color: white;
		font-weight: bold;
	}
	#loading, #error { display: none; }

	.loading {
		margin-top: 8%;
		position: absolute;
		left: 50%;
		top: 50%;
		-ms-transform: translateY(-50%);
		transform: translateY(-50%);
	}
	.body{
		font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, 'sans-serif';
		padding-top: 0px !important;
		padding-bottom: 0px !important;
	}

	@media screen and (max-width: 500px) { 
	  div{width:70%;} 
	}
	@media screen and (max-device-width: 800px) {
	  html { background: url(Big-City-Life.jpg) #000 no-repeat center center fixed; }
	  #bgvid { display: none; }
	}

	video { 
	  position: fixed;
	  top: 50%;
	  left: 50%;
	  min-width: 100%;
	  min-height: 100%;
	  width: auto;
	  height: auto;
	  z-index: -100;
	  transform: translateX(-50%) translateY(-50%);
	  background: url('Big-City-Life.jpg') no-repeat;
	  background-size: cover;
	  transition: 1s opacity;
	}

</style>
<video autoplay id="bgvid" loop>
	<source src="{{url('vid/wave_visual_thumb.mp4')}}" type="video/mp4">
</video>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<body class="body">
	{{var_dump($weather)}}
	<div class="row" style="padding-bottom: 0px;">
		<div class="col-xs-4">
			<h1 id="jam" style="margin-top: 0px;font-size: 10em;font-weight: bold;margin-bottom: -20px;color: white; "></h1>
			<span id="tanggal" style="font-size: 3.2em;color: white;font-weight: bold;"></span>
		</div>
	    <div class="col-xs-4" style="width: 220px;padding-top: 50px;padding-left: 20px" id="weather1">
	    	<a href='https://www.accuweather.com/en/id/pasuruan/203183/weather-forecast/203183' class='aw-widget-legal'></a><div id='awcc1535343665265' class='aw-widget-current'  data-locationkey='203183' data-unit='c' data-language='id' data-useip='false' data-uid='awcc1535343665265'></div><script type="text/javascript" src="https://oap.accuweather.com/launch.js"></script>
	    </div>

	    <div class="col-xs-4" style="width: 220px;padding-left: 20px" id="weather2">
	    	<a href='https://www.accuweather.com/en/id/jakarta/208971/weather-forecast/208971' class='aw-widget-legal'></a><div id='awcc1535343722607' class='aw-widget-current'  data-locationkey='208971' data-unit='c' data-language='id' data-useip='false' data-uid='awcc1535343722607'></div><script type="text/javascript" src="https://oap.accuweather.com/launch.js"></script>
	    </div>

	    <div class="col-xs-4" style="width: 220px;padding-left: 20px" id="weather3">
	    	<a href='https://www.accuweather.com/en/jp/hamamatsu-shi/226090/weather-forecast/226090' class='aw-widget-legal'></a><div id='awcc1535343845965' class='aw-widget-current'  data-locationkey='226090' data-unit='c' data-language='id' data-useip='false' data-uid='awcc1535343845965'></div><script type="text/javascript" src="https://oap.accuweather.com/launch.js"></script>
	    </div>
	</div>
</body>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var myvar = setInterval(waktu,1000);

	jQuery(document).ready(function(){

		$('#tanggal').html('{{$dateTitle}}');

		setInterval(resetWeather,3600000);

		var vid = document.getElementById("bgvid");
		vid.play();
	});

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}
	
	function getActualFullDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
	}

	function resetWeather() {
		window.location.reload();
	}
 
	function waktu() {
		var waktu = new Date();
		document.getElementById("jam").innerHTML = addZero(waktu.getHours())+':'+addZero(waktu.getMinutes());
	}
</script>