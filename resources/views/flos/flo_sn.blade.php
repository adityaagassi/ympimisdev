@extends('layouts.master')
@section('header')
<section class="content-header">
	<h1>
		Final Line Outputs
		<small>Band Instrument</small>
	</h1>
	<ol class="breadcrumb">
		{{-- <li><a href="{{ url("create/destination")}}" class="btn btn-primary btn-sm" style="color:white">Create {{ $page }}</a></li> --}}
	</ol>
</section>
@stop

@section('content')

<section class="content">
{{-- 	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('status') }}
	</div>   
	@endif --}}
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-danger">
				<div class="box-header">
					<h3 class="box-title">FLO <i class="fa fa-angle-right"></i> Print</h3>
				</div>
				<!-- /.box-header -->
				<form class="form-horizontal" role="form" method="post" action="{{url('print/flo_sn')}}">
					<div class="box-body">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="form-group">
							<label for="material_number" class="col-sm-1 control-label">Material</label>

							<div class="col-md-4">
								<select class="form-control select2" name="material_number" style="width: 100%;" data-placeholder="Choose a Material..." id="material_number" required>
									<option value=""></option>
									@foreach($materials as $material)
									<option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
									@endforeach
								</select>

							</div>
							<button type="submit" class="btn btn-danger col-sm-14"><i class="fa fa-print"></i>&nbsp;&nbsp;Print FLO</button>
						</div>
						<!-- /.box-body -->
					</div>
				</form>
				<!-- /.box -->
			</div>
			<!-- /.col -->
		</div>

		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">FLO <i class="fa fa-angle-right"></i> Fulfillment</h3>
				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<div class="form-group">
					</div>
				</div>
				<!-- /.box-body -->
			</div>
			<!-- /.box -->
		</div>
		<!-- /.col -->
	</div>
	<!-- /.row -->

</section>


</section>
@stop

@section('scripts')
<script>
	$(function () {
    //Initialize Select2 Elements
    $('.select2').select2()
})	

</script>
@stop