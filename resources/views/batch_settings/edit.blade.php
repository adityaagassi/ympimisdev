[
@extends('layouts.master')
@section('stylesheets')
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
@endsection
@section('header')
<section class="content-header">
	<h1>
		Edit {{ $page }}
		<small>it all starts here</small>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@endsection
@section('content')
<section class="content">
	@if ($errors->has('password'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Alert!</h4>
		{{ $errors->first() }}
	</div>   
	@endif
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>
	@endif
	<div class="box box-primary">
		<div class="box-header with-border">
		</div>  
		<form role="form" class="form-horizontal form-bordered" method="post" action="{{url('edit/batch_setting', $batch_setting->id)}}">

			<div class="box-body">
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="form-group row" align="right">
					<label class="col-sm-4">Batch Time</label>
					<div class="col-sm-4">
						<div class="input-group">
							<input type="text" name="batch_time" class="form-control timepicker" value="{{ date('H:i', strtotime($batch_setting->batch_time)) }}">
							<span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
						</div>
					</div>
				</div>
				<div class="form-group row" align="right">
					<label class="col-sm-4">Upload</label>
					<div class="col-sm-1">
						<input id="toggle_lock" name="upload" data-toggle="toggle" data-on="ON" data-off="OFF" type="checkbox" value="1" {{ $batch_setting->upload == 1 ? 'checked' : '' }} >
					</div>
				</div>
				<div class="form-group row" align="right">
					<label class="col-sm-4">Download</label>
					<div class="col-sm-1">
						<input id="toggle_lock" name="download" data-toggle="toggle" data-on="ON" data-off="OFF" type="checkbox" value="1" {{ $batch_setting->download == 1 ? 'checked' : '' }}>
					</div>
				</div>
				<div class="form-group row" align="right">
					<label class="col-sm-4">Remark</label>
					<div class="col-sm-4">
						<input type="text" class="form-control" name="remark" placeholder="Enter Remark" value="{{$batch_setting->remark}}">
					</div>
				</div>
				<div class="col-sm-4 col-sm-offset-6">
					<div class="btn-group">
						<a class="btn btn-danger" href="{{ url('index/batch_setting') }}">Cancel</a>
					</div>
					<div class="btn-group">
						<button type="submit" class="btn btn-primary col-sm-14">Submit</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</section>


@endsection

@section('scripts')
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/bootstrap-toggle.min.js") }}"></script>
<script>
	$(function () {
		$('.select2').select2()
	});

	$('.timepicker').timepicker({
		showInputs: false,
		showMeridian: false,
		defaultTime: '0:00',
	});
</script>
@stop

]