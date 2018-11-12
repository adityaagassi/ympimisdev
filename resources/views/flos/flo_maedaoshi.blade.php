@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style>
</style>
@endsection
@section('header')
<section class="content-header">
	<h1>
		Maedaoshi <span class="text-purple">?????</span>
	</h1>
	<ol class="breadcrumb">
		<li>
			<button href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#reprintModal">
				<i class="fa fa-print"></i>&nbsp;&nbsp;Reprint Maedaoshi
			</button>
		</li>
	</ol>
</section>
@endsection

@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">Scan Maedaoshi<span class="text-purple"> ??????????</span></h3>
				</div>
				<div class="box-body">
					<div class="col-md-3">
						<i style="font-weight: bold">Inner Box</i>
						<div class="input-group col-md-12">
							<div class="input-group-addon" id="icon-material">
								<i class="glyphicon glyphicon-barcode"></i>
							</div>
							<input type="text" style="text-align: center" class="form-control" id="maedaoshiMaterialNumber" name="material_number" placeholder="Material Number" required>
						</div>
						&nbsp;
						<div class="input-group col-md-12">
							<div class="input-group-addon" id="icon-serial">
								<i class="glyphicon glyphicon-barcode"></i>
							</div>
							<input type="text" style="text-align: center" class="form-control" id="maedaoshiSerialNumber" name="serial_number" placeholder="Serial Number" required>
						</div>
						<br>
						<i style="font-weight: bold" id='icon-box2'>Outer Box</i>
						<div class="input-group col-md-12">
							<div class="input-group-addon" id="icon-material2">
								<i class="glyphicon glyphicon-barcode"></i>
							</div>
							<input type="text" style="text-align: center" class="form-control" id="maedaoshiMaterialNumber2" name="material_number2" placeholder="Material Number" required>
						</div>
						&nbsp;
						<div class="input-group col-md-12">
							<div class="input-group-addon" id="icon-serial2">
								<i class="glyphicon glyphicon-barcode"></i>
							</div>
							<input type="text" style="text-align: center" class="form-control" id="maedaoshiSerialNumber2" name="serial_number2" placeholder="Serial Number" required>
						</div>
					</div>
					<div class="col-md-9">
						<div class="input-group col-md-8 col-md-offset-2">
							<div class="input-group-addon" id="icon-serial" style="font-weight: bold">Maedaoshi
							</div>
							<input type="text" style="text-align: center; font-size: 22" class="form-control" id="maedaoshi" name="maedaoshi" placeholder="Not Available" required>
							<div class="input-group-addon" id="icon-serial">
								<i class="glyphicon glyphicon-lock"></i>
							</div>
						</div>
						&nbsp;
						<table id="maedaoshiTable" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th style="font-size: 14">Serial</th>
									<th style="font-size: 14">Material</th>
									<th style="font-size: 14">Description</th>
									<th style="font-size: 14">Qty</th>
									<th style="font-size: 14">Del.</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="box box-danger">
				<div class="box-header">
					<h3 class="box-title">Scan After Maedaoshi<span class="text-purple"> ??????????</span></h3>
				</div>
				<div class="box-body">
					<div class="col-md-3">
						<i style="font-weight: bold">Inner Box</i>
						<div class="input-group col-md-12">
							<div class="input-group-addon" id="icon-material">
								<i class="glyphicon glyphicon-barcode"></i>
							</div>
							<input type="text" style="text-align: center" class="form-control" id="MaterialNumber" name="material_number" placeholder="Material Number" required>
						</div>
						&nbsp;
						<div class="input-group col-md-12">
							<div class="input-group-addon" id="icon-serial">
								<i class="glyphicon glyphicon-barcode"></i>
							</div>
							<input type="text" style="text-align: center" class="form-control" id="SerialNumber" name="serial_number" placeholder="Serial Number" required>
						</div>
						<br>
						<i style="font-weight: bold" id='icon-box2'>Outer Box</i>
						<div class="input-group col-md-12">
							<div class="input-group-addon" id="icon-material2">
								<i class="glyphicon glyphicon-barcode"></i>
							</div>
							<input type="text" style="text-align: center" class="form-control" id="MaterialNumber2" name="material_number2" placeholder="Material Number" required>
						</div>
						&nbsp;
						<div class="input-group col-md-12">
							<div class="input-group-addon" id="icon-serial2">
								<i class="glyphicon glyphicon-barcode"></i>
							</div>
							<input type="text" style="text-align: center" class="form-control" id="SerialNumber2" name="serial_number2" placeholder="Serial Number" required>
						</div>
					</div>
					<div class="col-md-9">
						<div class="input-group col-md-8 col-md-offset-2">
							<div class="input-group-addon" id="icon-serial" style="font-weight: bold">Maedaoshi
							</div>
							<input type="text" style="text-align: center; font-size: 22" class="form-control" id="FLONumber" name="FLONumber" placeholder="Not Available" required>
							<div class="input-group-addon" id="icon-serial">
								<i class="glyphicon glyphicon-lock"></i>
							</div>
						</div>
						&nbsp;
						<table id="FLOTable" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th style="font-size: 14">Serial</th>
									<th style="font-size: 14">Material</th>
									<th style="font-size: 14">Description</th>
									<th style="font-size: 14">Qty</th>
									<th style="font-size: 14">Del.</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	
</script>
@endsection