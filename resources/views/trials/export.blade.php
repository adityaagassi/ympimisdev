@extends('layouts.master')
@section('header')
<section class="content-header">
	<h1>
		List of Trials
		<small>it all starts here</small>
	</h1>
	<ol class="breadcrumb">
		{{-- <li><a href="{{ url("create/origin_group")}}" class="btn btn-primary btn-sm" style="color:white">Create Trials</a></li> --}}
	</ol>
</section>
@endsection

@section('content')


<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-info">
				<div class="box-body">



					<form class="form-horizontal" role="form" method="post" action="{{url('export/trial')}}">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="box-body">
							<div class="form-group">
								<label for="production_date" class="col-sm-2 control-label">Date</label>
								<div class="col-sm-3">
									<div class="input-group col-md-12">
										<input type="date" class="form-control" name="production_date" id="production_date" placeholder="Date">
										<div class="input-group-addon" id="icon-material">
											<i class="fa fa-calendar"></i>
										</div>
									</div>
									<br>
									<button type="submit" class="btn btn-info pull-right"><i class="fa fa-download"></i>&nbsp;Export</button>
								</div>
							</div>
						</div>
					</form>




				</div>
			</div>
		</div>
	</div>
</section>
@endsection