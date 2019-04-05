@extends('layouts.display')

@section('stylesheets')

@endsection


@section('header')
<section class="content-header">
	<h1>
		<span style="color: white; font-weight: bold; font-size: 28px; text-align: center;">YMPI Visitor Registration</span>
		{{-- <small>By Shipment Schedule <span class="text-purple">??????</span></small> --}}
	</h1>
	<ol class="breadcrumb" id="last_update">
	</ol>
</section>
@endsection

@section('content')
<div class="row">
	<div class="col-xs-offset-2 col-xs-8">
		<div class="box">
			<div class="box-header with-border">
				<h3 class="box-title">Registration Form</h3>
			</div>
			<form class="form-horizontal">
				<div class="box-body">
					<div class="form-group">
						<label for="inputEmail3" class="col-sm-2 control-label">Company</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="company" placeholder="Enter Company Name">
						</div>
					</div>
					<div class="form-group">
						<label for="inputEmail3" class="col-sm-2 control-label">Purpose</label>
						<div class="col-sm-9">
							<textarea class="form-control" id="purpose" placeholder="Enter Purposes"></textarea>
						</div>
					</div>
					<div class="form-group">
						<label for="inputEmail3" class="col-sm-2 control-label">Status</label>
						<div class="col-sm-9">
							<select class="form-control">
								<option> </option>
								<option value="Visitor">Visitor</option>
								<option value="Subcontract">Subcontract</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="inputEmail3" class="col-sm-2 control-label">ID/Name</label>
						<input type="text" name="lop" id="lop" value="1" hidden>
						<div class="col-sm-3" style="padding-right: 0;">
							<input type="text" class="form-control" id="visitor_id" placeholder="No. KTP/SIM" required>
						</div>
						<div class="col-sm-4" style="padding-left: 1; padding-right: 0;">
							<input type="text" class="form-control" id="visitor_name" placeholder="Full Name" required>
						</div>
						<div class="col-sm-2">
							<button class="btn btn-success" onclick='tambah("tambah","lop");'><i class='fa fa-plus' ></i></button>
						</div>
					</div>
				</div>
				<div class="box-footer">
					<button type="submit" class="btn btn-default">Cancel</button>
					<button type="submit" class="btn btn-info pull-right">Register</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection


@section('scripts')

@endsection