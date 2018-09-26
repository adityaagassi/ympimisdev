@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
@stop
@section('header')
<section class="content-header">
	<h1>
		Final Line Outputs
		<small>Containers Stuffing</small>
	</h1>
	<ol class="breadcrumb">
		<li>
			<button href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#reprintModal">
				<i class="fa fa-print"></i>&nbsp;&nbsp;Reprint FLO
			</button>
		</li>
	</ol>
</section>
@stop

@section('content')
<section class= "content">
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Success!</h4>
		{{ session('status') }}
	</div>   
	@endif
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">Finished Goods Export</h3>
				</div>
				<div class="box-body">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="row">
						<div class="col-md-12">
							<div class="input-group col-md-4 col-md-offset-4">
								<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
									<i class="glyphicon glyphicon-list-alt"></i>
								</div>
								<input type="text" style="text-align: center; font-size: 22" class="form-control" id="invoice_number" name="invoice_number" placeholder="Invoice Number..." required>
							</div>
							<br>
							<div class="input-group col-md-4 col-md-offset-4">
								<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
									<i class="fa fa-bus"></i>
								</div>
								<input type="text" style="text-align: center; font-size: 22" class="form-control" id="container_number" name="container_number" placeholder="Container Number..." required>
							</div>
							<br>
							<div class="input-group col-md-8 col-md-offset-2">
								<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
									<i class="glyphicon glyphicon-barcode"></i>
								</div>
								<input type="text" style="text-align: center; font-size: 22" class="form-control" id="flo_number_settlement" name="flo_number_settlement" placeholder="Scan FLO Here..." required>
								<div class="input-group-addon" id="icon-serial">
									<i class="glyphicon glyphicon-ok"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>	
	</div>
</section>
@stop