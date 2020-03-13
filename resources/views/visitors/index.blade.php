@extends('layouts.visitor')

@section('stylesheets')

@endsection


@section('header')
<section class="content-header">
	<h1>
		<span style="color: white; font-weight: bold; font-size: 28px; text-align: center;">YMPI Visitor Control</span>
		{{-- <small>By Shipment Schedule <span class="text-purple">??????</span></small> --}}
	</h1>
	<ol class="breadcrumb" id="last_update">
	</ol>
</section>
@endsection


@section('content')
<div class="row">
	<div class="col-md-offset-3 col-md-6">
		<br>
		<br>
		<br>
		<br>
		<br>
		<a href="{{ url("visitor_registration") }}" class="btn btn-lg btn-success btn-block" style="font-size: 80px; padding: 0; font-weight: bold;">MASUK</a>
		
		<a href="{{ url("visitor_list") }}" class="btn btn-lg btn-danger btn-block" style="font-size: 80px; padding: 0; font-weight: bold;">KELUAR</a>
		<!-- <a href="{{ url("visitor_getvisitSc") }}" class="btn btn-lg btn-warning btn-block" style="font-size: 80px; padding: 0; font-weight: bold;">KONFIRMASI</a> -->
		<br>
		<br>
		<br>
		<br>
		<br>
	</div>
</div>
@endsection


@section('scripts')

@endsection