@extends('layouts.display_2')

@section('stylesheets')
<style type="text/css">
	content, html, body {
		height: 100%;
	}
	body {
		margin: 0px;
	}
	.navbare {
		overflow: hidden;
		position: fixed;
		top: 0;
		width: 100%;
		background-color: #605ca8;
		z-index: 100;
	}

	#schedule {
		overflow-y: scroll;
		height: 610px;
		/*margin-top: -60px;*/
		zoom: 1.5;
		-moz-transform: scale(1.5);
		-moz-transform-origin: 0 0;
		position:absolute; 
		clip:rect(100px,1110px,800px,50px);
		top:-73px; left:-80px;
	}
	
</style>
@endsection

@section('content')
<div class="navbare">
	<center><span style="color: white; font-size: 4vw; font-weight: bold;">MEETING ROOM 1</span></center>
</div>
<center>
	<iframe src="https://outlook.office365.com/calendar/view/day/" width="100%" id="schedule"></iframe>
	<!-- <object type="text/html" data="https://outlook.office365.com/calendar/view/day/" width="800px" height="600px" style="overflow:auto;border:5px ridge blue"> -->
	</object>
</center>
@endsection

<!-- <div id="content">
	<div style="height:800px;">
		<iframe id="optomaFeed" src="http://www.optomausa.com/company/press-releases/" scrolling="no"
		frameborder="0" height="100%" width="100%" style="position:absolute; clip:rect(190px,1100px,800px,250px);
		top:-160px; left:-160px;"></iframe>
	</div>
</div> -->