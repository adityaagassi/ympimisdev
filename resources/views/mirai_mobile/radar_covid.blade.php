@extends('layouts.display_2')

@section('stylesheets')
<style type="text/css">
	content, html, body {
		height: 100%;
	}
	body {
		margin: 0px;
		background-color: #333333;
	}
	.navbare {
		overflow: hidden;
		position: fixed;
		top: 0;
		width: 100%;
		background-color: #605ca8;
		z-index: 100;
		padding: 5px 0 5px 0;
	}

	#schedule {
		overflow-y: scroll;
		height: 610px;
		width: 1000px;
		/*margin-top: -60px;*/
		/*zoom: 2;
		-moz-transform: scale(2);
		-moz-transform-origin: 0 0;*/
		position:absolute; 
		clip:rect(0px,1110px,800px,50px);
		top:-50px; left:-50px;
	}
	
</style>
@endsection

@section('content')

<iframe src="https://radarcovid19.jatimprov.go.id/" width="100%" height="100%" id="covid"></iframe>
</object>
<div class="navbare">
	<center><span style="color: white; font-size: 4vw; font-weight: bold;">RADAR COVID 19</span></center>
</div>

@endsection

<script type="text/javascript">
	window.setInterval("reloadIFrame();", 1800000);

	function reloadIFrame() {
		document.getElementById("covid").src="https://radarcovid19.jatimprov.go.id/";	}

	</script>