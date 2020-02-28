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

	.ww-informers-box-854754{-webkit-animation-name:ww-informers54;animation-name:ww-informers54;-webkit-animation-duration:1.5s;animation-duration:1.5s;white-space:nowrap;overflow:hidden;-o-text-overflow:ellipsis;text-overflow:ellipsis;font-size:12px;font-family:Arial;line-height:18px;text-align:center}
	@-webkit-keyframes ww-informers54{0%,80%{opacity:0}100%{opacity:1}}@keyframes ww-informers54{0%,80%{opacity:0}100%{opacity:1}}

</style>
<video autoplay id="bgvid" loop>
	<source src="{{url('vid/wave_visual_thumb.mp4')}}" type="video/mp4">
</video>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<body class="body">
	<div class="row" style="padding-bottom: 0px;">
		<div class="col-xs-4">
			<h1 id="jam" style="margin-top: 0px;font-size: 10em;font-weight: bold;margin-bottom: -20px;color: white; "></h1>
			<span id="tanggal" style="font-size: 3.2em;color: white;font-weight: bold;"></span>
		</div>
	    <div class="col-xs-4" style="width: 250px;margin-top: 50px;margin-left: 20px;background-color: white">
	    	<span style="font-weight: bold;font-size: 20px;color: black;width: 100%;padding-left: 10px">Pasuruan, ID</span>
	    	<div id="e761385a5815e733b988da40edee7ac0" class="ww-informers-box-854753"><p class="ww-informers-box-854754"><a href="https://world-weather.info/forecast/indonesia/pasuruan/month/"></a><br><a href="https://world-weather.info/">world-weather.info</a></p></div><script type="text/javascript" charset="utf-8" src="https://world-weather.info/wwinformer.php?userid=e761385a5815e733b988da40edee7ac0"></script>
	    </div>
	    <br>
	    <div class="col-xs-4" style="width: 250px;margin-left: 20px;background-color: white">
	    	<span style="font-weight: bold;font-size: 20px;color: black;width: 100%;padding-left: 10px">Jakarta, ID</span>
	    	<div id="364858afaf5629894b5970c4aa120860" class="ww-informers-box-854753"><p class="ww-informers-box-854754"><a href="https://world-weather.info/forecast/indonesia/jakarta/">Detailed forecast</a><br><a href="https://world-weather.info/">world-weather.info</a></p></div><script type="text/javascript" charset="utf-8" src="https://world-weather.info/wwinformer.php?userid=364858afaf5629894b5970c4aa120860"></script>
	    </div>
	    <br>
	    <div class="col-xs-4" style="width: 250px;margin-left: 20px;background-color: white">
	    	<span style="font-weight: bold;font-size: 20px;color: black;width: 100%;padding-left: 10px">Hamamatsu, JP</span>
	    	<div id="a83dd8d114fa24f9350a290451ff8936" class="ww-informers-box-854753"><p class="ww-informers-box-854754"><a href="https://world-weather.info/forecast/japan/hamamatsu/">Hamamatsu - weather</a><br><a href="https://world-weather.info/forecast/usa/montpelier/">Weather Forecast in Montpelier</a></p></div><script type="text/javascript" charset="utf-8" src="https://world-weather.info/wwinformer.php?userid=a83dd8d114fa24f9350a290451ff8936"></script>
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