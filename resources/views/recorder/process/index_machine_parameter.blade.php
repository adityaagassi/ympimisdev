@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black !important;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
		padding-top: 0;
		padding-bottom: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
	#tabledesign {
		border: 1px solid black;
		vertical-align: middle;
		text-align: center;
		font-size: 12px;
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{$page}} - First Shot Approval <small><span class="text-purple">?? ～ 初物検査</span></small>
		<!-- <small> <span class="text-purple">??</span></small> -->
		<button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#create-modal">
           <!-- <i class="fa fa-edit"></i> -->
           Create Parameter
        </button>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if (session('status'))
		<div class="alert alert-success alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
			{{ session('status') }}
		</div>   
	@endif
	@if (session('error'))
		<div class="alert alert-warning alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4> Warning!</h4>
			{{ session('error') }}
		</div>   
	@endif
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-body">
					<div class="col-xs-12">
						<div class="col-xs-12">
							<div class="box-header">
								<h3 class="box-title">Filter</h3>
							</div>
							<form role="form" method="post" action="{{url('index/filter_machine_parameter')}}">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="col-md-6 col-md-offset-3">
									<div class="col-md-6">
										<div class="form-group">
											<label for="">Date From</label>
											<div class="input-group date">
												<div class="input-group-addon bg-white">
													<i class="fa fa-calendar"></i>
												</div>
												<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From" autocomplete="off">
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="">Date To</label>
											<div class="input-group date">
												<div class="input-group-addon bg-white">
													<i class="fa fa-calendar"></i>
												</div>
												<input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Date To" autocomplete="off">
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-6 col-md-offset-3">
									<div class="col-md-12">
										<div class="form-group">
											<label for="">Machine</label>
											<select class="form-control select2" multiple="multiple" name="mesin_filter" id='mesin_filter' data-placeholder="Select Mesin" style="width: 100%;">
												<option value=""></option>
												@foreach($mesin as $mesin)
													<option value="{{$mesin}}">{{$mesin}}</option>
												@endforeach
											</select>
										</div>
									</div>
								</div>
								<div class="col-md-6 col-md-offset-3">
									<div class="col-md-12">
										<div class="form-group pull-right">
											<a href="{{ url('index/injeksi') }}" class="btn btn-warning">Back</a>
											<a href="{{ url('index/machine_parameter') }}" class="btn btn-danger">Clear</a>
											<button type="submit" class="btn btn-primary col-sm-14">Search</button>
										</div>
									</div>
								</div>
							</form>
						</div>
						<div class="col-xs-6">
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<div class="box">
								<div class="box-body" style="overflow-x: scroll;">
									<table id="example1" class="table table-bordered table-striped table-hover">
										<thead style="background-color: rgba(126,86,134,.7);">
											<tr>
												<th rowspan="3">Check Date</th>
												<th rowspan="3">Reason</th>
												<th rowspan="3">Product</th>
												<th rowspan="3">Machine</th>
												<th rowspan="3">Molding</th>
												<th>NH</th>
												<th>H1</th>
												<th>H2</th>
												<th>H3</th>
												<th>Dryer</th>
												<th colspan="2">MTC</th>
												<th colspan="2">Chiller</th>
												<th>Clamp</th>
												<th>PH4</th>
												<th>PH3</th>
												<th>PH2</th>
												<th>PH1</th>
												<th>TRH3</th>
												<th>TRH2</th>
												<th>TRH1</th>
												<th>VH</th>
												<th>PI</th>
												<th>LS10 BB</th>
												<th>VI5</th>
												<th>VI4</th>
												<th>VI3</th>
												<th>VI2</th>
												<th>VI1</th>
												<th>LS4</th>
												<th>LS4D</th>
												<th>LS4C</th>
												<th>LS4B</th>
												<th>LS4A</th>
												<th>LS5</th>
												<th>VE1</th>
												<th>VE2</th>
												<th>VR</th>
												<th>LS31A</th>
												<th>LS31</th>
												<th>SRN</th>
												<th>RPM</th>
												<th>BP</th>
												<th>TR1 INJ</th>
												<th>TR3 COOL</th>
												<th>TR4 INT</th>
												<th>Min. Cush</th>
												<th>FILL</th>
												<th>Circle Time</th>
												<th rowspan="3">Action</th>
											</tr>
											<tr>
												<th colspan="4">Header / ??</th>
												<th>??</th>
												<th colspan="2">??</th>
												<th colspan="2">??</th>
												<th>??</th>
												<th colspan="4">Pressure Hold / ??</th>
												<th colspan="3">Pressure Hold Time / ??</th>
												<th>Velocity PH / ??</th>
												<th>??</th>
												<th>??</th>
												<th colspan="5">Velocity Injection / ??</th>
												<th colspan="6">Length of Stroke / ??</th>
												<th colspan="3">??</th>
												<th colspan="2">??</th>
												<th colspan="2">Screw / ??</th>
												<th>??</th>
												<th colspan="3">Timer / ??</th>
												<th>??</th>
												<th>??</th>
												<th>??</th>
											</tr>
											<tr>
												<th colspan="4">°C</th>
												<th>°C</th>
												<th>°C</th>
												<th>MPa / bar</th>
												<th>°C</th>
												<th>MPa / bar</th>
												<th>kN</th>
												<th colspan="4">% / MPa</th>
												<th colspan="3">Sec</th>
												<th>mm/sec</th>
												<th>MPa</th>
												<th>mm</th>
												<th colspan="5">% / mm / sec</th>
												<th colspan="6">mm</th>
												<th colspan="3">mm / sec</th>
												<th colspan="2">mm</th>
												<th colspan="2">% / min<sup>-1</sup></th>
												<th>MPa</th>
												<th colspan="3">Sec</th>
												<th>mm</th>
												<th>Sec</th>
												<th>Sec</th>
											</tr>
										</thead>
										<tbody>
											@foreach($parameter as $parameter)
											<tr>
												<td>{{ $parameter->check_date }}</td>
												<td>{{ $parameter->reason }}</td>
												<td>{{ $parameter->product_type }}</td>
												<td>{{ $parameter->mesin }}</td>
												<td>{{ $parameter->molding }}</td>
												<td>{{$parameter->nh}}</td>
												<td>{{$parameter->h1}}</td>
												<td>{{$parameter->h2}}</td>
												<td>{{$parameter->h3}}</td>
												<td>{{$parameter->dryer}}</td>
												<td>{{$parameter->mtc_temp}}</td>
												<td>{{$parameter->mtc_press}}</td>
												<td>{{$parameter->chiller_temp}}</td>
												<td>{{$parameter->chiller_press}}</td>
												<td>{{$parameter->clamp}}</td>
												<td>{{$parameter->ph4}}</td>
												<td>{{$parameter->ph3}}</td>
												<td>{{$parameter->ph2}}</td>
												<td>{{$parameter->ph1}}</td>
												<td>{{$parameter->trh3}}</td>
												<td>{{$parameter->trh2}}</td>
												<td>{{$parameter->trh1}}</td>
												<td>{{$parameter->vh}}</td>
												<td>{{$parameter->pi}}</td>
												<td>{{$parameter->ls10}}</td>
												<td>{{$parameter->vi5}}</td>
												<td>{{$parameter->vi4}}</td>
												<td>{{$parameter->vi3}}</td>
												<td>{{$parameter->vi2}}</td>
												<td>{{$parameter->vi1}}</td>
												<td>{{$parameter->ls4}}</td>
												<td>{{$parameter->ls4d}}</td>
												<td>{{$parameter->ls4c}}</td>
												<td>{{$parameter->ls4b}}</td>
												<td>{{$parameter->ls4a}}</td>
												<td>{{$parameter->ls5}}</td>
												<td>{{$parameter->ve1}}</td>
												<td>{{$parameter->ve2}}</td>
												<td>{{$parameter->vr}}</td>
												<td>{{$parameter->ls31a}}</td>
												<td>{{$parameter->ls31}}</td>
												<td>{{$parameter->srn}}</td>
												<td>{{$parameter->rpm}}</td>
												<td>{{$parameter->bp}}</td>
												<td>{{$parameter->tr1inj}}</td>
												<td>{{$parameter->tr3cool}}</td>
												<td>{{$parameter->tr4int}}</td>
												<td>{{$parameter->mincush}}</td>
												<td>{{$parameter->fill}}</td>
												<td>{{$parameter->circletime}}</td>
												<td><center>
														<button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit-modal" onclick="edit_parameter('{{ url("index/push_block_recorder/update_parameter") }}','{{ $parameter->id }}');">
											               <i class="fa fa-edit"></i>
											            </button>
											            <a href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("index/push_block_recorder/delete_parameter") }}','{{ $parameter->reason }} - {{ $parameter->product_type }} - {{ $parameter->mesin }} - {{ $parameter->molding }}','{{ $parameter->id }}');">
															<i class="fa fa-trash"></i>
														</a>
													</center>
												</td>
											</tr>
											@endforeach
										</tbody>
										<tfoot>
											<tr>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
											</tr>
										</tfoot>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
			</div>
			<div class="modal-body">
				Are you sure delete?
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="create-modal">
  <div class="modal-dialog modal-lg" style="width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" align="center"><b>Create Parameter</b></h4>
      </div>
      <div class="modal-body">
      	<div class="box-body">
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	            <div class="form-group">
	              <!-- <input type="hidden" name="url_edit" id="url_edit" class="form-control"> -->
	              <label for="">Check Date</label>
				  <input type="text" name="check_date" id="check_date" class="form-control" readonly required="required" title="" value="{{ date('Y-m-d H:i:s') }}" readonly>
	            </div>
	            <div class="form-group">
	              <label for="">Reason</label>
				  <select name="reason" id="reason" class="form-control" data-placeholder="Choose a Reason ..." required="required" style="width: 100%;">
				  	
				  	<option value="MOLD CHANGE">MOLD CHANGE</option>
				  	<option value="COLOR CHANGE">COLOR CHANGE</option>
				  	<option value="TROUBLESHOOTING">TROUBLESHOOTING</option>
				  </select>
	            </div>
	            <div class="form-group">	              
	              <label for="">Product</label>
				  <select name="product_type" id="product_type" class="form-control" required="required" data-placeholder="Choose a Product ..." style="width: 100%;">
				  	
				  	@foreach($product_type as $product_type)
				  	<option value="{{$product_type}}">{{$product_type}}</option>
				  	@endforeach
				  </select>
	            </div>     
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            	<div class="form-group">	              
	              <label for="">Remark</label>
				  <input type="text" name="push_block_code" id="push_block_code" class="form-control" readonly required="required" title="" value="First Shot Approval" readonly>
	            </div>
	            <div class="form-group">	              
	              <label for="">Mesin</label>
				  <select name="mesin" id="mesin" class="form-control" required="required" data-placeholder="Choose a Machine ..." style="width: 100%;">
				  	
				  	@foreach($mesin2 as $mesin2)
				  	<option value="{{$mesin2}}">{{$mesin2}}</option>
				  	@endforeach
				  </select>
	            </div>
	            <div class="form-group">	              
	              <label for="">Molding</label>
				  <select name="molding" id="molding" class="form-control" required="required" data-placeholder="Choose a Molding ..." style="width: 100%;">
				  	@foreach($molding as $molding)
				  		<option value="{{$molding}}">{{$molding}}</option>
				  	@endforeach
				  </select>
	            </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="overflow-x: scroll;">
            	<table class="table table-bordered">
					<thead>
						<tr>
							<th rowspan="3" id="tabledesign">Note</th>
							<th id="tabledesign">NH</th>
							<th id="tabledesign">H1</th>
							<th id="tabledesign">H2</th>
							<th id="tabledesign">H3</th>
							<th id="tabledesign">Dryer</th>
							<th id="tabledesign" colspan="2">MTC</th>
							<th id="tabledesign" colspan="2">Chiller</th>
							<th id="tabledesign">Clamp</th>
							<th id="tabledesign">PH4</th>
							<th id="tabledesign">PH3</th>
							<th id="tabledesign">PH2</th>
							<th id="tabledesign">PH1</th>
							<th id="tabledesign">TRH3</th>
							<th id="tabledesign">TRH2</th>
							<th id="tabledesign">TRH1</th>
							<th id="tabledesign">VH</th>
							<th id="tabledesign">PI</th>
							<th id="tabledesign">LS10 BB</th>
							<th id="tabledesign">VI5</th>
							<th id="tabledesign">VI4</th>
							<th id="tabledesign">VI3</th>
							<th id="tabledesign">VI2</th>
							<th id="tabledesign">VI1</th>
							<th id="tabledesign">LS4</th>
							<th id="tabledesign">LS4D</th>
							<th id="tabledesign">LS4C</th>
							<th id="tabledesign">LS4B</th>
							<th id="tabledesign">LS4A</th>
							<th id="tabledesign">LS5</th>
							<th id="tabledesign">VE1</th>
							<th id="tabledesign">VE2</th>
							<th id="tabledesign">VR</th>
							<th id="tabledesign">LS31A</th>
							<th id="tabledesign">LS31</th>
							<th id="tabledesign">SRN</th>
							<th id="tabledesign">RPM</th>
							<th id="tabledesign">BP</th>
							<th id="tabledesign">TR1 INJ</th>
							<th id="tabledesign">TR3 COOL</th>
							<th id="tabledesign">TR4 INT</th>
							<th id="tabledesign">Min. Cush</th>
							<th id="tabledesign">FILL</th>
							<th id="tabledesign">Circle Time</th>
						</tr>
						<tr>
							<th id="tabledesign" colspan="4">Header / ??</th>
							<th id="tabledesign">??</th>
							<th id="tabledesign" colspan="2">??</th>
							<th id="tabledesign" colspan="2">??</th>
							<th id="tabledesign">??</th>
							<th id="tabledesign" colspan="4">Pressure Hold / ??</th>
							<th id="tabledesign" colspan="3">Pressure Hold Time / ??</th>
							<th id="tabledesign">Velocity PH / ??</th>
							<th id="tabledesign">??</th>
							<th id="tabledesign">??</th>
							<th id="tabledesign" colspan="5">Velocity Injection / ??</th>
							<th id="tabledesign" colspan="6">Length of Stroke / ??</th>
							<th id="tabledesign" colspan="3">??</th>
							<th id="tabledesign" colspan="2">??</th>
							<th id="tabledesign" colspan="2">Screw / ??</th>
							<th id="tabledesign">??</th>
							<th id="tabledesign" colspan="3">Timer / ??</th>
							<th id="tabledesign">??</th>
							<th id="tabledesign">??</th>
							<th id="tabledesign">??</th>
						</tr>
						<tr>
							<th id="tabledesign" colspan="4">°C</th>
							<th id="tabledesign">°C</th>
							<th id="tabledesign">°C</th>
							<th id="tabledesign">MPa / bar</th>
							<th id="tabledesign">°C</th>
							<th id="tabledesign">MPa / bar</th>
							<th id="tabledesign">kN</th>
							<th id="tabledesign" colspan="4">% / MPa</th>
							<th id="tabledesign" colspan="3">Sec</th>
							<th id="tabledesign">mm/sec</th>
							<th id="tabledesign">MPa</th>
							<th id="tabledesign">mm</th>
							<th id="tabledesign" colspan="5">% / mm / sec</th>
							<th id="tabledesign" colspan="6">mm</th>
							<th id="tabledesign" colspan="3">mm / sec</th>
							<th id="tabledesign" colspan="2">mm</th>
							<th id="tabledesign" colspan="2">% / min<sup>-1</sup></th>
							<th id="tabledesign">MPa</th>
							<th id="tabledesign" colspan="3">Sec</th>
							<th id="tabledesign">mm</th>
							<th id="tabledesign">Sec</th>
							<th id="tabledesign">Sec</th>
						</tr>
					</thead>
					<tbody id="bodyLastParameter">
						<td id="tabledesign">New</td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_nh" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_h1" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_h2" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_h3" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_dryer" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_mtc_temp" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_mtc_press" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_chiller_temp" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_chiller_press" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_clamp" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph4" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph3" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph2" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph1" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_trh3" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_trh2" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_trh1" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vh" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_pi" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls10" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi5" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi4" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi3" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi2" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi1" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4d" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4c" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4b" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4a" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls5" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ve1" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ve2" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vr" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls31a" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls31" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_srn" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_rpm" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_bp" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_tr1inj" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_tr3cool" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_tr4int" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_mincush" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_fill" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_circletime" class="form-control"></td>
					</tbody>
				</table>
            </div>
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          	<div class="modal-footer">
	            <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
	            <input type="submit" value="Create" onclick="create()" class="btn btn-primary">
	          </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="edit-modal">
  <div class="modal-dialog modal-lg" style="width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" align="center"><b>Edit Parameter</b></h4>
      </div>
      <div class="modal-body">
      	<div class="box-body">
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	            <div class="form-group">
	              <input type="hidden" name="url_edit" id="url_edit" class="form-control">
	              <label for="">Check Date</label>
				  <input type="text" name="editcheck_date" id="editcheck_date" class="form-control" readonly required="required" title="" readonly>
	            </div>
	            <div class="form-group">
	              <label for="">Reason</label>
				  <select name="editreason" id="editreason" class="form-control" data-placeholder="Choose a Reason ..." required="required" style="width: 100%;">
				  	<option value="MOLD CHANGE">MOLD CHANGE</option>
				  	<option value="COLOR CHANGE">COLOR CHANGE</option>
				  	<option value="TROUBLESHOOTING">TROUBLESHOOTING</option>
				  </select>
	            </div>
	            <div class="form-group">	              
	              <label for="">Product</label>
				  <select name="editproduct_type" id="editproduct_type" class="form-control" required="required" data-placeholder="Choose a Product ..." style="width: 100%;">
				  	@foreach($product_type2 as $product_type2)
				  	<option value="{{$product_type2}}">{{$product_type2}}</option>
				  	@endforeach
				  </select>
	            </div>     
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            	<div class="form-group">	              
	              <label for="">Remark</label>
				  <input type="text" name="editpush_block_code" id="editpush_block_code" class="form-control" readonly required="required" title="" value="First Shot Approval" readonly>
	            </div>
	            <div class="form-group">	              
	              <label for="">Mesin</label>
				  <select name="editmesin" id="editmesin" class="form-control" required="required" data-placeholder="Choose a Machine ..." style="width: 100%;">
				  	@foreach($mesin3 as $mesin3)
				  	<option value="{{$mesin3}}">{{$mesin3}}</option>
				  	@endforeach
				  </select>
	            </div>
	            <div class="form-group">	              
	              <label for="">Molding</label>
				  <select name="editmolding" id="editmolding" class="form-control" required="required" data-placeholder="Choose a Molding ..." style="width: 100%;">
				  	@foreach($molding2 as $molding2)
				  		<option value="{{$molding2}}">{{$molding2}}</option>
				  	@endforeach
				  </select>
	            </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="overflow-x: scroll;">
            	<table class="table table-bordered">
					<thead>
						<tr>
							<th rowspan="3" id="tabledesign">Note</th>
							<th id="tabledesign">NH</th>
							<th id="tabledesign">H1</th>
							<th id="tabledesign">H2</th>
							<th id="tabledesign">H3</th>
							<th id="tabledesign">Dryer</th>
							<th id="tabledesign" colspan="2">MTC</th>
							<th id="tabledesign" colspan="2">Chiller</th>
							<th id="tabledesign">Clamp</th>
							<th id="tabledesign">PH4</th>
							<th id="tabledesign">PH3</th>
							<th id="tabledesign">PH2</th>
							<th id="tabledesign">PH1</th>
							<th id="tabledesign">TRH3</th>
							<th id="tabledesign">TRH2</th>
							<th id="tabledesign">TRH1</th>
							<th id="tabledesign">VH</th>
							<th id="tabledesign">PI</th>
							<th id="tabledesign">LS10 BB</th>
							<th id="tabledesign">VI5</th>
							<th id="tabledesign">VI4</th>
							<th id="tabledesign">VI3</th>
							<th id="tabledesign">VI2</th>
							<th id="tabledesign">VI1</th>
							<th id="tabledesign">LS4</th>
							<th id="tabledesign">LS4D</th>
							<th id="tabledesign">LS4C</th>
							<th id="tabledesign">LS4B</th>
							<th id="tabledesign">LS4A</th>
							<th id="tabledesign">LS5</th>
							<th id="tabledesign">VE1</th>
							<th id="tabledesign">VE2</th>
							<th id="tabledesign">VR</th>
							<th id="tabledesign">LS31A</th>
							<th id="tabledesign">LS31</th>
							<th id="tabledesign">SRN</th>
							<th id="tabledesign">RPM</th>
							<th id="tabledesign">BP</th>
							<th id="tabledesign">TR1 INJ</th>
							<th id="tabledesign">TR3 COOL</th>
							<th id="tabledesign">TR4 INT</th>
							<th id="tabledesign">Min. Cush</th>
							<th id="tabledesign">FILL</th>
							<th id="tabledesign">Circle Time</th>
						</tr>
						<tr>
							<th id="tabledesign" colspan="4">Header / ??</th>
							<th id="tabledesign">??</th>
							<th id="tabledesign" colspan="2">??</th>
							<th id="tabledesign" colspan="2">??</th>
							<th id="tabledesign">??</th>
							<th id="tabledesign" colspan="4">Pressure Hold / ??</th>
							<th id="tabledesign" colspan="3">Pressure Hold Time / ??</th>
							<th id="tabledesign">Velocity PH / ??</th>
							<th id="tabledesign">??</th>
							<th id="tabledesign">??</th>
							<th id="tabledesign" colspan="5">Velocity Injection / ??</th>
							<th id="tabledesign" colspan="6">Length of Stroke / ??</th>
							<th id="tabledesign" colspan="3">??</th>
							<th id="tabledesign" colspan="2">??</th>
							<th id="tabledesign" colspan="2">Screw / ??</th>
							<th id="tabledesign">??</th>
							<th id="tabledesign" colspan="3">Timer / ??</th>
							<th id="tabledesign">??</th>
							<th id="tabledesign">??</th>
							<th id="tabledesign">??</th>
						</tr>
						<tr>
							<th id="tabledesign" colspan="4">°C</th>
							<th id="tabledesign">°C</th>
							<th id="tabledesign">°C</th>
							<th id="tabledesign">MPa / bar</th>
							<th id="tabledesign">°C</th>
							<th id="tabledesign">MPa / bar</th>
							<th id="tabledesign">kN</th>
							<th id="tabledesign" colspan="4">% / MPa</th>
							<th id="tabledesign" colspan="3">Sec</th>
							<th id="tabledesign">mm/sec</th>
							<th id="tabledesign">MPa</th>
							<th id="tabledesign">mm</th>
							<th id="tabledesign" colspan="5">% / mm / sec</th>
							<th id="tabledesign" colspan="6">mm</th>
							<th id="tabledesign" colspan="3">mm / sec</th>
							<th id="tabledesign" colspan="2">mm</th>
							<th id="tabledesign" colspan="2">% / min<sup>-1</sup></th>
							<th id="tabledesign">MPa</th>
							<th id="tabledesign" colspan="3">Sec</th>
							<th id="tabledesign">mm</th>
							<th id="tabledesign">Sec</th>
							<th id="tabledesign">Sec</th>
						</tr>
					</thead>
					<tbody id="bodyLastParameter">
						<td id="tabledesign">Last</td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_nh" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_h1" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_h2" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_h3" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_dryer" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_mtc_temp" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_mtc_press" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_chiller_temp" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_chiller_press" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_clamp" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ph4" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ph3" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ph2" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ph1" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_trh3" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_trh2" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_trh1" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_vh" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_pi" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ls10" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_vi5" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_vi4" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_vi3" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_vi2" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_vi1" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ls4" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ls4d" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ls4c" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ls4b" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ls4a" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ls5" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ve1" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ve2" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_vr" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ls31a" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_ls31" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_srn" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_rpm" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_bp" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_tr1inj" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_tr3cool" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_tr4int" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_mincush" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_fill" class="form-control"></td>
						<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="edit_circletime" class="form-control"></td>
					</tbody>
				</table>
            </div>
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          	<div class="modal-footer">
	            <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
	            <input type="submit" value="Update" onclick="update()" class="btn btn-primary">
	          </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</section>
@endsection


@section('scripts')
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('.select2').select2({
			language : {
				noResults : function(params) {
					return "There is no date";
				}
			}
		});
		$('body').toggleClass("sidebar-collapse");

		// $('.select2').select2({
		// 	dropdownParent: $('#create-modal')
		// });
	});
	$('.datepicker').datepicker({
		autoclose: true,
		format: "yyyy-mm-dd",
		autoclose: true,
		todayHighlight: true
	});

	function changeMesin() {
		$("#judgement").val($("#judgementSelect").val());
	}
</script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '3000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '3000'
		});
	}
	jQuery(document).ready(function() {
		$('#example1 tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
		} );
		var table = $('#example1').DataTable({
			"order": [],
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			'buttons': {
				buttons:[
				{
					extend: 'pageLength',
					className: 'btn btn-default',
				},
				{
					extend: 'copy',
					className: 'btn btn-success',
					text: '<i class="fa fa-copy"></i> Copy',
					exportOptions: {
						columns: ':not(.notexport)'
					}
				},
				{
					extend: 'excel',
					className: 'btn btn-info',
					text: '<i class="fa fa-file-excel-o"></i> Excel',
					exportOptions: {
						columns: ':not(.notexport)'
					}
				},
				{
					extend: 'print',
					className: 'btn btn-warning',
					text: '<i class="fa fa-print"></i> Print',
					exportOptions: {
						columns: ':not(.notexport)'
					}
				},
				]
			}
		});

		table.columns().every( function () {
			var that = this;

			$( 'input', this.footer() ).on( 'keyup change', function () {
				if ( that.search() !== this.value ) {
					that
					.search( this.value )
					.draw();
				}
			} );
		} );

		$('#example1 tfoot tr').appendTo('#example1 thead');

	});

	function edit_parameter(url,id) {
    	$.ajax({
                url: "{{ route('recorder.get_parameter') }}?id=" + id,
                method: 'GET',
                success: function(data) {
                  	var json = data;
                  	var data = data.data;
                  	$("#url_edit").val(url+'/'+id);
                  	$("#editcheck_date").val(data.check_date);
                  	$("#editreason").val(data.reason).change();
                  	$("#editproduct_type").val(data.product_type);
                	$("#editmesin").val(data.mesin);
                  	$("#editmolding").val(data.molding);
                  	$('#edit_nh').val(data.nh);
					$('#edit_h1').val(data.h1);
					$('#edit_h2').val(data.h2);
					$('#edit_h3').val(data.h3);
					$('#edit_dryer').val(data.dryer);
					$('#edit_mtc_temp').val(data.mtc_temp);
					$('#edit_mtc_press').val(data.mtc_press);
					$('#edit_chiller_temp').val(data.chiller_temp);
					$('#edit_chiller_press').val(data.chiller_press);
					$('#edit_clamp').val(data.clamp);
					$('#edit_ph4').val(data.ph4);
					$('#edit_ph3').val(data.ph3);
					$('#edit_ph2').val(data.ph2);
					$('#edit_ph1').val(data.ph1);
					$('#edit_trh3').val(data.trh3);
					$('#edit_trh2').val(data.trh2);
					$('#edit_trh1').val(data.trh1);
					$('#edit_vh').val(data.vh);
					$('#edit_pi').val(data.pi);
					$('#edit_ls10').val(data.ls10);
					$('#edit_vi5').val(data.vi5);
					$('#edit_vi4').val(data.vi4);
					$('#edit_vi3').val(data.vi3);
					$('#edit_vi2').val(data.vi2);
					$('#edit_vi1').val(data.vi1);
					$('#edit_ls4').val(data.ls4);
					$('#edit_ls4d').val(data.ls4d);
					$('#edit_ls4c').val(data.ls4c);
					$('#edit_ls4b').val(data.ls4b);
					$('#edit_ls4a').val(data.ls4a);
					$('#edit_ls5').val(data.ls5);
					$('#edit_ve1').val(data.ve1);
					$('#edit_ve2').val(data.ve2);
					$('#edit_vr').val(data.vr);
					$('#edit_ls31a').val(data.ls31a);
					$('#edit_ls31').val(data.ls31);
					$('#edit_srn').val(data.srn);
					$('#edit_rpm').val(data.rpm);
					$('#edit_bp').val(data.bp);
					$('#edit_tr1inj').val(data.tr1inj);
					$('#edit_tr3cool').val(data.tr3cool);
					$('#edit_tr4int').val(data.tr4int);
					$('#edit_mincush').val(data.mincush);
					$('#edit_fill').val(data.fill);
					$('#edit_circletime').val(data.circletime);
                }
            });
      // jQuery('#formedit2').attr("action", url+'/'+interview_id+'/'+detail_id);
      // console.log($('#formedit2').attr("action"));
    }

    function push_pull() {
		var batas_bawah = '3';
		var batas_atas = '17';

		var x = document.getElementById('push_pull').value;
		if(x == ''){
			document.getElementById('push_pull').style.backgroundColor = "#ff4f4f";
		}
		else{
			document.getElementById('push_pull').style.backgroundColor = "#7fff6e";
		}
		if(parseFloat(x) < parseFloat(batas_bawah) || parseFloat(x) > parseFloat(batas_atas)){
			$('#judgement_push_pull').val('NG');
			document.getElementById('judgement_push_pull').style.backgroundColor = "#ff4f4f";
			document.getElementById('judgement_push_pull').style.color = "#fff";
		}
		else{
			$('#judgement_push_pull').val('OK');
			document.getElementById('judgement_push_pull').style.backgroundColor = "#7fff6e";
			document.getElementById('judgement_push_pull').style.color = "#000";
		}
	}

	function ketinggian() {
		var batas_tinggi = '0.2';

		var x = document.getElementById('ketinggian').value;
		if(x == ''){
			document.getElementById('ketinggian').style.backgroundColor = "#ff4f4f";
		}
		else{
			document.getElementById('ketinggian').style.backgroundColor = "#7fff6e";
		}
		if(parseFloat(x) > parseFloat(batas_tinggi)){
			$('#judgement2').val('NG');
			document.getElementById('judgement2').style.backgroundColor = "#ff4f4f";
			document.getElementById('judgement2').style.color = "#fff";
		}
		else{
			$('#judgement2').val('OK');
			document.getElementById('judgement2').style.backgroundColor = "#7fff6e";
			document.getElementById('judgement2').style.color = "#000";
		}
	}

	function update(){
		var url = $('#url_edit').val();

		var data = {
			push_block_code : 'First Shot Approval',
			// push_block_id_gen : $('#push_block_id_gen').val(),
			reason : $('#editreason').val(),
			product_type : $("#editproduct_type").val(),
			mesin : $("#editmesin").val(),
			molding : $("#editmolding").val(),
			nh : $('#edit_nh').val(),
			h1 : $('#edit_h1').val(),
			h2 : $('#edit_h2').val(),
			h3 : $('#edit_h3').val(),
			dryer : $('#edit_dryer').val(),
			mtc_temp : $('#edit_mtc_temp').val(),
			mtc_press : $('#edit_mtc_press').val(),
			chiller_temp : $('#edit_chiller_temp').val(),
			chiller_press : $('#edit_chiller_press').val(),
			clamp : $('#edit_clamp').val(),
			ph4 : $('#edit_ph4').val(),
			ph3 : $('#edit_ph3').val(),
			ph2 : $('#edit_ph2').val(),
			ph1 : $('#edit_ph1').val(),
			trh3 : $('#edit_trh3').val(),
			trh2 : $('#edit_trh2').val(),
			trh1 : $('#edit_trh1').val(),
			vh : $('#edit_vh').val(),
			pi : $('#edit_pi').val(),
			ls10 : $('#edit_ls10').val(),
			vi5 : $('#edit_vi5').val(),
			vi4 : $('#edit_vi4').val(),
			vi3 : $('#edit_vi3').val(),
			vi2 : $('#edit_vi2').val(),
			vi1 : $('#edit_vi1').val(),
			ls4 : $('#edit_ls4').val(),
			ls4d : $('#edit_ls4d').val(),
			ls4c : $('#edit_ls4c').val(),
			ls4b : $('#edit_ls4b').val(),
			ls4a : $('#edit_ls4a').val(),
			ls5 : $('#edit_ls5').val(),
			ve1 : $('#edit_ve1').val(),
			ve2 : $('#edit_ve2').val(),
			vr : $('#edit_vr').val(),
			ls31a : $('#edit_ls31a').val(),
			ls31 : $('#edit_ls31').val(),
			srn : $('#edit_srn').val(),
			rpm : $('#edit_rpm').val(),
			bp : $('#edit_bp').val(),
			tr1inj : $('#edit_tr1inj').val(),
			tr3cool : $('#edit_tr3cool').val(),
			tr4int : $('#edit_tr4int').val(),
			mincush : $('#edit_mincush').val(),
			fill : $('#edit_fill').val(),
			circletime : $('#edit_circletime').val()
		}
		// console.table(data);
		
		$.post(url, data, function(result, status, xhr){
			if(result.status){
				$("#edit-modal").modal('hide');
				// $('#example1').DataTable().ajax.reload();
				// $('#example2').DataTable().ajax.reload();
				openSuccessGritter('Success','Parameter has been updated');
				location.reload();
			} else {
				audio_error.play();
				openErrorGritter('Error','Update Push Pull Recorder Check Failed');
			}
		});
	}

	function create() {
		var data = {
			push_block_code : 'First Shot Approval',
			// push_block_id_gen : $('#push_block_id_gen').val(),
			reason : $('#reason').val(),
			check_date : $("#check_date").val(),
			product_type : $("#product_type").val(),
			mesin : $("#mesin").val(),
			molding : $("#molding").val(),
			nh : $('#input_nh').val(),
			h1 : $('#input_h1').val(),
			h2 : $('#input_h2').val(),
			h3 : $('#input_h3').val(),
			dryer : $('#input_dryer').val(),
			mtc_temp : $('#input_mtc_temp').val(),
			mtc_press : $('#input_mtc_press').val(),
			chiller_temp : $('#input_chiller_temp').val(),
			chiller_press : $('#input_chiller_press').val(),
			clamp : $('#input_clamp').val(),
			ph4 : $('#input_ph4').val(),
			ph3 : $('#input_ph3').val(),
			ph2 : $('#input_ph2').val(),
			ph1 : $('#input_ph1').val(),
			trh3 : $('#input_trh3').val(),
			trh2 : $('#input_trh2').val(),
			trh1 : $('#input_trh1').val(),
			vh : $('#input_vh').val(),
			pi : $('#input_pi').val(),
			ls10 : $('#input_ls10').val(),
			vi5 : $('#input_vi5').val(),
			vi4 : $('#input_vi4').val(),
			vi3 : $('#input_vi3').val(),
			vi2 : $('#input_vi2').val(),
			vi1 : $('#input_vi1').val(),
			ls4 : $('#input_ls4').val(),
			ls4d : $('#input_ls4d').val(),
			ls4c : $('#input_ls4c').val(),
			ls4b : $('#input_ls4b').val(),
			ls4a : $('#input_ls4a').val(),
			ls5 : $('#input_ls5').val(),
			ve1 : $('#input_ve1').val(),
			ve2 : $('#input_ve2').val(),
			vr : $('#input_vr').val(),
			ls31a : $('#input_ls31a').val(),
			ls31 : $('#input_ls31').val(),
			srn : $('#input_srn').val(),
			rpm : $('#input_rpm').val(),
			bp : $('#input_bp').val(),
			tr1inj : $('#input_tr1inj').val(),
			tr3cool : $('#input_tr3cool').val(),
			tr4int : $('#input_tr4int').val(),
			mincush : $('#input_mincush').val(),
			fill : $('#input_fill').val(),
			circletime : $('#input_circletime').val()
		}

		console.log(data);

		$.post('{{ url("index/push_block_recorder/create_parameter") }}', data, function(result, status, xhr){
			if(result.status){
				$('#create-modal').modal('hide');
				location.reload();
				openSuccessGritter('Success', result.message);
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function deleteConfirmation(url, name,id) {
		jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
		jQuery('#modalDeleteButton').attr("href", url+'/'+id);
	}
</script>
@endsection