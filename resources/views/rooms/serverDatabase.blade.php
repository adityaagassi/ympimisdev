@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
  .content-header {
    background-color: #61258e !important;
    padding: 10px;
    color: white;
  }

  .content-header > h1 {
    margin: 0;
    font-size: 120px;
    font-weight: bold;
  }

  .content-wrapper{
  	padding: 0 !important;
  }


</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding: 0">
	<div class="row" style="padding: 0">
		<div class="col-xs-12">
			<div class="row">
				<div class="col-md-12 content-header" style="text-align: center">
	          		<h1>DATABASE STATUS</h1>
				</div>
				<!-- <img src="{{url('images/ping/database.png')}}" width="28%" style="vertical-align: top;margin-top:5px;"> 
				<img src="{{url('images/ping/database.png')}}" width="28%" style="vertical-align: top;margin-top:5px;"> 
				<img src="{{url('images/ping/database.png')}}" width="28%" style="vertical-align: top;margin-top:5px;"> 
 -->
				<div class="col-md-12" style="padding-top: 20px">
					<div class="col-md-2 content-header text-center" style="background-color: #357a38 !important;border: 5px solid white">
		          		<h1><i class="fa fa-check-circle-o"></i></h1>
					</div>
					<div class="col-md-10 content-header" style="background-color: transparent !important;border: 5px solid white">
		          		<h1>MIRAI DB</h1>
					</div>
				</div>
				<div class="col-md-12" style="padding-top: 20px">
					<div class="col-md-2 content-header text-center" style="background-color: #357a38 !important;border: 5px solid white">
		          		<h1><i class="fa fa-check-circle-o"></i></h1>
					</div>
					<div class="col-md-10 content-header" style="background-color: transparent !important;border: 5px solid white">
		          		<h1>Sunfish DB</h1>
					</div>
				</div>

				<div class="col-md-12" style="padding-top: 20px">
					<div class="col-md-2 content-header text-center" style="background-color: #357a38 !important;border: 5px solid white">
		          		<h1><i class="fa fa-check-circle-o"></i></h1>
					</div>
					<div class="col-md-10 content-header" style="background-color: transparent !important;border: 5px solid white">
		          		<h1>MIRAI LOCAL DB</h1>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/highstock.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){

	});

</script>
@endsection