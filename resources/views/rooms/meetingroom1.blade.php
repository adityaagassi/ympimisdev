@extends('layouts.display_2')

@section('stylesheets')
<style type="text/css">
	content, html, body {
		height: 100%;
	}
	body {
		margin: 0px;
	}
	iframe {
		/*overflow-y: scroll;*/
		height:610px;
	}
</style>
@endsection

@section('content')
<center>
	<span style="color: white; font-size: 4vw; font-weight: bold;">MEETING ROOM 1</span>
	<iframe src="https://outlook.office365.com/calendar/view/day/" width="100%" id="schedule" scrolling="yes"></iframe>
</center>
@endsection
