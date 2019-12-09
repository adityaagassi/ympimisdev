@extends('layouts.visitor')
@section('stylesheets')
@endsection
@section('header')
<section class="content-header">
	<h1>
		<span style="color: white; font-weight: bold; font-size: 28px; text-align: center;">RECORDER REPAIR</span>
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
		<a href="{{ url("index/recorder_repair/tarik") }}" class="btn btn-lg btn-danger btn-block" style="font-size: 70px; padding: 0; font-weight: bold;">TARIK DARI WH</a>
		<a href="{{ url("index/recorder_repair/selesai") }}" class="btn btn-lg btn-warning btn-block" style="font-size: 70px; padding: 0; font-weight: bold;">SELESAI</a>
		<a href="{{ url("index/recorder_repair/kembali") }}" class="btn btn-lg btn-success btn-block" style="font-size: 70px; padding: 0; font-weight: bold;">KEMBALI KE WH</a>
		<br>
		<br>
		<a href="{{ url("index/recorder_repair/resume") }}" class="btn btn-lg btn-info btn-block" style="font-size: 70px; padding: 0; font-weight: bold;">RESUME</a>

		
	</div>
</div>
@endsection


@section('scripts')

@endsection