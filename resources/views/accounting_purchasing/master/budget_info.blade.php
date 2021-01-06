@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
	  width: 100%;
	  padding: 3px;
	  box-sizing: border-box;
	}
	thead>tr>th{
	  text-align:center;
	  overflow:hidden;
	  padding: 3px;
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
	  border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
	  border:1px solid rgb(211,211,211);
	  padding-top: 0;
	  padding-bottom: 0;
	  vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
	  border:1px solid rgb(211,211,211);
	}
	td{
	    overflow:hidden;
	    text-overflow: ellipsis;
	  }
	#tdhover:hover {
	    /*cursor: pointer;*/
	    background-color: #4caf50;
	    color: white;
	  }
	#loading { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		Budget Information <span class="text-purple">{{ $title_jp }}</span>
	</h1>
	<ol class="breadcrumb">
		<li>
			<!-- <a href="{{ url("index/budget/create")}}" class="btn btn-md bg-purple" style="color:white"><i class="fa fa-plus"></i> Create New budget</a> -->
		</li>
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if (session('success'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('success') }}
	</div>   
	@endif
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row" style="margin-top: 5px">
		<div class="col-xs-12">
			<div class="box no-border" style="margin-bottom: 5px;">
				<div class="box-header">
					<h3 class="box-title">Detail Filters<span class="text-purple"> フィルター詳細</span></span></h3>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<div class="col-md-2">
							<div class="form-group">
								<label>Periode</label>
								<select class="form-control select2" multiple="multiple" id='periode' data-placeholder="Select Periode" onchange="fetchTable()" style="width: 100%;">
									<option>FY196</option>
									<option>FY197</option>
								</select>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label>Bulan</label>
								<select class="form-control select2" multiple="multiple" id='bulan' data-placeholder="Select Bulan" onchange="fetchTable()" style="width: 100%;">
									<option>Jan</option>
									<option>Feb</option>
									<option>Mar</option>
									<option>Apr</option>
									<option>May</option>
									<option>Jun</option>
									<option>Jul</option>
									<option>Aug</option>
									<option>Sep</option>
									<option>Oct</option>
									<option>Nov</option>
									<option>Des</option>
								</select>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label>Category</label>
								<select class="form-control select2" multiple="multiple" id='category' data-placeholder="Select Category" onchange="fetchTable()" style="width: 100%;">
									<option value="Expenses">Expenses</option>
									<option value="Fixed Asset">Fixed Asset</option>
								</select>
							</div>
						</div>
						@if(Auth::user()->role_code == "MIS" || Auth::user()->role_code == "ACC" || Auth::user()->role_code == "ACC-SPL")
				        <div class="col-md-2">
							<div class="form-group">
								<label>Department</label>
				              	<select class="form-control select2" multiple="multiple" id="department" data-placeholder="Select Department" onchange="fetchTable()" style="width: 100%;border-color: #605ca8" >
				                  @foreach($department as $dept)
				                    <option value="{{ $dept->department }}">{{ $dept->department }}</option>
				                  @endforeach
				                </select>
							</div>
				        </div>
				        @else
				           <select class="form-control select2 hideselect" multiple="multiple" id="department" data-placeholder="Select Department" onchange="fetchTable()" style="border-color: #605ca8">
				             <option value="{{$emp_dept->department}}" selected="">{{$emp_dept->department}}</option>
				           </select>
				        @endif
						<div class="col-md-4">
							<div class="form-group">
								<!-- <div class="col-md-4" style="padding-right: 0;">
									<label style="color: white;"> x</label>
									<button class="btn btn-primary form-control" onclick="fetchTable()"><i class="fa fa-search"></i> Search</button>
								</div> -->
								<div class="col-md-4" style="padding-right: 0;">
									<label style="color: white;"> x</label>
									<button class="btn btn-danger form-control" onclick="clearSearch()"><i class="fa fa-close"></i> Clear</button>
								</div>

								<?php if(Auth::user()->role_code == "MIS" || Auth::user()->role_code == "ACC" || Auth::user()->role_code == "ACC-SPL") { ?>
								<div class="col-md-4">
									<label style="color: white;"> x</label><br>
									<button class="btn btn-success " data-toggle="modal"  data-target="#upload_budget" style="margin-right: 5px">
										<i class="fa fa-upload"></i>&nbsp;&nbsp;Upload Budget
									</button>
								</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="box no-border">
						<!-- <div class="box-header">
							<button class="btn btn-success" data-toggle="modal" data-target="#importModal" style="width: 
							16%">Import</button>
						</div> -->
						<div class="box-body" style="padding-top: 0;">
							<table id="budgetTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>Periode</th>
										<th>Budget No</th>
										<th>Description</th>
										<th>Account Name</th>
										<th>Category</th>
										<th>Amount ($)</th>
										<th>Purchase requisition ($)</th>
										<th>Investment ($)</th>
										<th>Purchase Order ($)</th>
										<!-- <th>Transfer ($)</th> -->
										<th>Actual ($)</th>
										<th>Ending ($)</th>
										<th>Detail</th>
									</tr>
								</thead>
								<tbody id="tablebudget">
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
					                <!-- <th></th> -->
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
</section>


<div class="modal fade" id="ViewModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Detail Budget <span id="budget_no"></span></h4>
      </div>
      <div class="modal-body">
        <div class="box-body">
        	<input type="hidden" value="{{csrf_token()}}" name="_token" />
        	<div class="col-md-12">
	          	<table class="table table-striped text-center">
	          		<!-- <tr>
	          			<th>Bulan</th>
	          			<th>Budget Awal</th>
	          			<th>Budget After Adjustment</th>
	          			<th>Sisa Budget</th>
	          			<th>April</th>
	          			<th>Mei</th>
	          			<th>Juni</th>
	          			<th>Juli</th>
	          			<th>Agustus</th>
	          			<th>September</th>
	          			<th>Oktober</th>
	          			<th>November</th>
	          			<th>Desember</th>
	          			<th>Januari</th>
	          			<th>Februari</th>
	          			<th>Maret</th>
	          		</tr>
	          		<tr>
	          			<th>
	          				Budget Awal
	          			</th>
	          			<td>
	          				<label id="budget_awal4" name="budget_awal4"></label>
	          			</td>
	          			<td>
	          				<label id="budget_awal5" name="budget_awal5"></label>
	          			</td>
	          			<td>
	          				<label id="budget_awal6" name="budget_awal6"></label>
	          			</td>
	          			<td>
	          				<label id="budget_awal7" name="budget_awal7"></label>
	          			</td>
	          			<td>
	          				<label id="budget_awal8" name="budget_awal8"></label>
	          			</td>
	          			<td>
	          				<label id="budget_awal9" name="budget_awal9"></label>
	          			</td>
	          			<td>
	          				<label id="budget_awal10" name="budget_awal10"></label>
	          			</td>
	          			<td>
	          				<label id="budget_awal11" name="budget_awal11"></label>
	          			</td>
	          			<td>
	          				<label id="budget_awal12" name="budget_awal12"></label>
	          			</td>
	          			<td>
	          				<label id="budget_awal1" name="budget_awal1"></label>
	          			</td>
	          			<td>
	          				<label id="budget_awal2" name="budget_awal2"></label>
	          			</td>
	          			<td>
	          				<label id="budget_awal3" name="budget_awal3"></label>
	          			</td>
	          		</tr>
	          		<tr>
	          			<th>
	          				Budget After Adjustment
	          			</th>
	          			<td>
	          				<label id="budget_adj4" name="budget_adj4"></label>
	          			</td>
	          			<td>
	          				<label id="budget_adj5" name="budget_adj5"></label>
	          			</td>
	          			<td>
	          				<label id="budget_adj6" name="budget_adj6"></label>
	          			</td>
	          			<td>
	          				<label id="budget_adj7" name="budget_adj7"></label>
	          			</td>
	          			<td>
	          				<label id="budget_adj8" name="budget_adj8"></label>
	          			</td>
	          			<td>
	          				<label id="budget_adj9" name="budget_adj9"></label>
	          			</td>
	          			<td>
	          				<label id="budget_adj10" name="budget_adj10"></label>
	          			</td>
	          			<td>
	          				<label id="budget_adj11" name="budget_adj11"></label>
	          			</td>
	          			<td>
	          				<label id="budget_adj12" name="budget_adj12"></label>
	          			</td>
	          			<td>
	          				<label id="budget_adj1" name="budget_adj1"></label>
	          			</td>
	          			<td>
	          				<label id="budget_adj2" name="budget_adj2"></label>
	          			</td>
	          			<td>
	          				<label id="budget_adj3" name="budget_adj3"></label>
	          			</td>
	          		</tr>
	          		<tr>
	          			<th>
	          				Budget Sisa
	          			</th>
	          			<td>
	          				<label id="budget_sisa4" name="budget_sisa4"></label>
	          			</td>
	          			<td>
	          				<label id="budget_sisa5" name="budget_sisa5"></label>
	          			</td>
	          			<td>
	          				<label id="budget_sisa6" name="budget_sisa6"></label>
	          			</td>
	          			<td>
	          				<label id="budget_sisa7" name="budget_sisa7"></label>
	          			</td>
	          			<td>
	          				<label id="budget_sisa8" name="budget_sisa8"></label>
	          			</td>
	          			<td>
	          				<label id="budget_sisa9" name="budget_sisa9"></label>
	          			</td>
	          			<td>
	          				<label id="budget_sisa10" name="budget_sisa10"></label>
	          			</td>
	          			<td>
	          				<label id="budget_sisa11" name="budget_sisa11"></label>
	          			</td>
	          			<td>
	          				<label id="budget_sisa12" name="budget_sisa12"></label>
	          			</td>
	          			<td>
	          				<label id="budget_sisa1" name="budget_sisa1"></label>
	          			</td>
	          			<td>
	          				<label id="budget_sisa2" name="budget_sisa2"></label>
	          			</td>
	          			<td>
	          				<label id="budget_sisa3" name="budget_sisa3"></label>
	          			</td>
	          		</tr> -->

	          		<tr>
	          			<th>Bulan</th>
	          			<th>Budget Simulasi</th>
	          			<th>Penggunaan Budget</th>
	          			<th style="background-color: #ff7043">Sisa Budget</th>
	          		</tr>
	          		<tr>
	          			<td>
	          				April
	          			</td>
	          			<!-- <td>
	          				<label id="budget_awal4" name="budget_awal4"></label>
	          			</td> -->
	          			<td>
	          				<label id="budget_adj4" name="budget_adj4"></label>
	          			</td>
	          			<td>
	          				<label id="budget_penggunaan4" name="budget_penggunaan4"></label>
	          			</td>
	          			<td class="sisa">
	          				<label id="budget_sisa4" name="budget_sisa4"></label>
	          			</td>
	          		</tr>
	          		<tr>
	          			<td>
	          				Mei
	          			</td>
	          			<!-- <td>
	          				<label id="budget_awal5" name="budget_awal5"></label>
	          			</td> -->
	          			<td>
	          				<label id="budget_adj5" name="budget_adj5"></label>
	          			</td>
	          			<td>
	          				<label id="budget_penggunaan5" name="budget_penggunaan5"></label>
	          			</td>
	          			<td class="sisa">
	          				<label id="budget_sisa5" name="budget_sisa5"></label>
	          			</td>
	          		</tr>
	          		<tr>
	          			<td>
	          				Juni
	          			</td>
	          			<!-- <td>
	          				<label id="budget_awal6" name="budget_awal6"></label>
	          			</td> -->
	          			<td>
	          				<label id="budget_adj6" name="budget_adj6"></label>
	          			</td>
	          			<td>
	          				<label id="budget_penggunaan6" name="budget_penggunaan6"></label>
	          			</td>
	          			<td class="sisa">
	          				<label id="budget_sisa6" name="budget_sisa6"></label>
	          			</td>
	          		</tr>
	          		<tr>
	          			<td>
	          				Juli
	          			</td>
	          			<!-- <td>
	          				<label id="budget_awal7" name="budget_awal7"></label>
	          			</td> -->
	          			<td>
	          				<label id="budget_adj7" name="budget_adj7"></label>
	          			</td>
	          			<td>
	          				<label id="budget_penggunaan7" name="budget_penggunaan7"></label>
	          			</td>
	          			<td class="sisa">
	          				<label id="budget_sisa7" name="budget_sisa7"></label>
	          			</td>
	          		</tr>
	          		<tr>
	          			<td>
	          				Agustus
	          			</td>
	          			<!-- <td>
	          				<label id="budget_awal8" name="budget_awal8"></label>
	          			</td> -->
	          			<td>
	          				<label id="budget_adj8" name="budget_adj8"></label>
	          			</td>
	          			<td>
	          				<label id="budget_penggunaan8" name="budget_penggunaan8"></label>
	          			</td>
	          			<td class="sisa">
	          				<label id="budget_sisa8" name="budget_sisa8"></label>
	          			</td>
	          		</tr>
	          		<tr>
	          			<td>
	          				September
	          			</td>
	          			<!-- <td>
	          				<label id="budget_awal9" name="budget_awal9"></label>
	          			</td> -->
	          			<td>
	          				<label id="budget_adj9" name="budget_adj9"></label>
	          			</td>
	          			<td>
	          				<label id="budget_penggunaan9" name="budget_penggunaan9"></label>
	          			</td>
	          			<td class="sisa">
	          				<label id="budget_sisa9" name="budget_sisa9"></label>
	          			</td>
	          		</tr>
	          		<tr>
	          			<td>
	          				Oktober
	          			</td>
	          			<!-- <td>
	          				<label id="budget_awal10" name="budget_awal10"></label>
	          			</td> -->
	          			<td>
	          				<label id="budget_adj10" name="budget_adj10"></label>
	          			</td>
	          			<td>
	          				<label id="budget_penggunaan10" name="budget_penggunaan10"></label>
	          			</td>
	          			<td class="sisa">
	          				<label id="budget_sisa10" name="budget_sisa10"></label>
	          			</td>
	          		</tr>
	          		<tr>
	          			<td>
	          				November
	          			</td>
	          			<!-- <td>
	          				<label id="budget_awal11" name="budget_awal11"></label>
	          			</td> -->
	          			<td>
	          				<label id="budget_adj11" name="budget_adj11"></label>
	          			</td>
	          			<td>
	          				<label id="budget_penggunaan11" name="budget_penggunaan11"></label>
	          			</td>
	          			<td class="sisa">
	          				<label id="budget_sisa11" name="budget_sisa11"></label>
	          			</td>
	          		</tr>
	          		<tr>
	          			<td>
	          				December
	          			</td>
	          			<!-- <td>
	          				<label id="budget_awal12" name="budget_awal12"></label>
	          			</td> -->
	          			<td>
	          				<label id="budget_adj12" name="budget_adj12"></label>
	          			</td>
	          			<td>
	          				<label id="budget_penggunaan12" name="budget_penggunaan12"></label>
	          			</td>
	          			<td class="sisa">
	          				<label id="budget_sisa12" name="budget_sisa12"></label>
	          			</td>
	          		</tr>
	          		<tr>
	          			<td>
	          				Januari
	          			</td>
	          			<!-- <td>
	          				<label id="budget_awal1" name="budget_awal1"></label>
	          			</td> -->
	          			<td>
	          				<label id="budget_adj1" name="budget_adj1"></label>
	          			</td>
	          			<td>
	          				<label id="budget_penggunaan13" name="budget_penggunaan13"></label>
	          			</td>
	          			<td class="sisa">
	          				<label id="budget_sisa1" name="budget_sisa1"></label>
	          			</td>
	          		</tr>
	          		<tr>
	          			<td>
	          				Februari
	          			</td>
	          			<!-- <td>
	          				<label id="budget_awal2" name="budget_awal2"></label>
	          			</td> -->
	          			<td>
	          				<label id="budget_adj2" name="budget_adj2"></label>
	          			</td>
	          			<td>
	          				<label id="budget_penggunaan14" name="budget_penggunaan14"></label>
	          			</td>
	          			<td class="sisa">
	          				<label id="budget_sisa2" name="budget_sisa2"></label>
	          			</td>
	          		</tr>
	          		<tr>
	          			<td>
	          				Maret
	          			</td>
	          			<!-- <td>
	          				<label id="budget_awal3" name="budget_awal3"></label>
	          			</td> -->
	          			<td>
	          				<label id="budget_adj3" name="budget_adj3"></label>
	          			</td>
	          			<td>
	          				<label id="budget_penggunaan15" name="budget_penggunaan15"></label>
	          			</td>
	          			<td class="sisa">
	          				<label id="budget_sisa3" name="budget_sisa3"></label>
	          			</td>
	          		</tr>

	          		<tr>
	          			<td style="background-color: #00a65a;color: white;border-top: 0">
	          				Total
	          			</td>
	          			<td style="background-color: #00a65a;color: white;border-top: 0">
	          				<label id="budget_adj_total" name="budget_adj_total"></label>
	          			</td>
	          			<td style="background-color: #00a65a;color: white;border-top: 0">
	          				<label id="budget_penggunaan_total" name="budget_penggunaan_total"></label>
	          			</td>
	          			<td style="background-color: #00a65a;color: white;border-top: 0">
	          				<label id="budget_sisa_total" name="budget_sisa_total"></label>
	          			</td>
	          		</tr>
	          	</table>
	          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<div class="modal modal-default fade" id="upload_budget">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<form id="importForm" method="post" enctype="multipart/form-data" autocomplete="off">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">Upload Confirmation</h4>
						Format: <i class="fa fa-arrow-down"></i> Seperti yang Tertera Pada Attachment Dibawah ini <i class="fa fa-arrow-down"></i><br>
						Sample: <a href="{{ url('uploads/budget/sample/budget(200728_09.58).xlsx') }}">budget(200728_09.58).xlsx</a>
					</div>
					<div class="modal-body">
						Upload Excel file here:<span class="text-red">*</span>
						<input type="file" name="upload_file" id="upload_file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						<button id="modalImportButton" type="submit" class="btn btn-success">Upload</button>
					</div>
				</form>
			</div>
		</div>
	</div>


	<div class="modal fade" id="myModal">
	    <div class="modal-dialog" style="width:1250px;">
	      <div class="modal-content">
	        <div class="modal-header">
	          <h4 style="float: right;" id="modal-title"></h4>
	          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
	          <br><h4 class="modal-title" id="judul_table"></h4>
	        </div>
	        <div class="modal-body">
	          <div class="row">
	            <div class="col-md-12">
	              <table id="tableResult" class="table table-striped table-bordered table-hover" style="width: 100%;"> 
	                <thead style="background-color: rgba(126,86,134,.7);">
	                  <tr>
	                    <th width="10%">Budget</th>
	                    <th width="10%">Month</th>
	                    <th width="30%">Category Number</th>
	                    <th width="30%">Detail Item</th>
	                    <th width="10%">Status</th>
	                    <th width="10%">Amount ($)</th>
	                  </tr>
	                </thead>
	                <tbody id="tableBodyResult">
	                </tbody>
	                <tfoot style="background-color: RGB(252, 248, 227);">
	                <th></th>
	                <th></th>
	                <th></th>
	                <th></th>
	                <th>Total</th>
	                <th id="resultTotal"></th>
	              </tfoot>
	              </table>
	            </div>
	          </div>
	        </div>
	        <div class="modal-footer">
	          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
	        </div>
	      </div>
	    </div>
	  </div>


@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	// var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('.select2').select2();
    	$('.hideselect').next(".select2-container").hide();
		fetchTable();
		$('body').toggleClass("sidebar-collapse");
	});

	function clearSearch(){
		location.reload(true);
	}

	function loadingPage(){
		$("#loading").show();
	}

	function fetchTable(){
		var bulan = $('#bulan').val();
		var periode = $('#periode').val();
		var category = $('#category').val();
	    var department = $('#department').val();

		var data = {
			bulan:bulan,
			periode:periode,
			category:category,
		    department: department,
		}

	    $.get('{{ url("fetch/budget/info") }}', data, function(result, status, xhr){
	      if(xhr.status == 200){
	        if(result.status){

	       	  $('#budgetTable').DataTable().clear();
			  $('#budgetTable').DataTable().destroy();

	          $("#tablebudget").find("td").remove();  
	          $('#tablebudget').html("");

	          var table = "";

	          $.each(result.datas, function(key, value) {

	              var ending = parseFloat(value.amount) - (parseFloat(value.PR) + parseFloat(value.Investment) + parseFloat(value.PO) + parseFloat(value.Actual)) + parseFloat(value.Transfer);

	              table += '<tr>';
	              table += '<td>'+value.periode+'</td>';
	              table += '<td>'+value.budget_no+'</td>';
	              table += '<td>'+value.description+'</td>';
	              table += '<td>'+value.account_name+'</td>';
	              table += '<td>'+value.category+'</td>';
	              table += '<td>'+value.amount.toLocaleString()+'</td>';
	              table += '<td id="tdhover" style="cursor:pointer" onclick="detail_budget(\''+value.budget_no+'\',\'PR\')">'+value.PR.toLocaleString()+'</td>';
	              table += '<td id="tdhover" style="cursor:pointer" onclick="detail_budget(\''+value.budget_no+'\',\'Investment\')">'+value.Investment.toLocaleString()+'</td>';
	              table += '<td id="tdhover" style="cursor:pointer" onclick="detail_budget(\''+value.budget_no+'\',\'PO\')">'+value.PO.toLocaleString()+'</td>';
	              // table += '<td id="tdhover" style="cursor:pointer" onclick="detail_budget(\''+value.budget_no+'\',\'Transfer\')">'+value.Transfer.toLocaleString()+'</td>';
	              table += '<td id="tdhover" style="cursor:pointer" onclick="detail_budget(\''+value.budget_no+'\',\'Actual\')">'+value.Actual.toLocaleString()+'</td>';
	              if (ending >= 0) {
	              	table += '<td style="color:blue">'+ending.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })+'</td>';                
	              }

	              table += '<td><button class="btn btn-md btn-warning" data-toggle="tooltip" title="Details" onclick="modalView(\''+value.budget_no+'\')"><i class="fa fa-eye"></i></button></td>';  

	              table += '</tr>';
	          })

	          $('#tablebudget').append(table);

	          $('#budgetTable tfoot th').each( function () {
		        var title = $(this).text();
			        $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
			      } );
			      var table = $('#budgetTable').DataTable({
			        'dom': 'Bfrtip',
			        'responsive':true,
			        'lengthMenu': [
			        [ 5, 10, 25, -1 ],
			        [ '5 rows', '10 rows', '25 rows', 'Show all' ]
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
			        },
			        'paging': true,
			        'lengthChange': true,
			        'pageLength': 15,
			        'searching': true,
			        'ordering': true,
			        'order': [],
			        'info': true,
			        'autoWidth': true,
			        "sPaginationType": "full_numbers",
			        "bJQueryUI": true,
			        "bAutoWidth": false,
			        "processing": true
			      });
	        }
	        else{
		      alert('Attempt to retrieve data failed');
		    }
	      }

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

		    $('#budgetTable tfoot tr').appendTo('#budgetTable thead');
	    });
	  }

	function fetchTable2(){
		$('#budgetTable').DataTable().destroy();
		
		$('#budgetTable tfoot th').each( function () {
	      var title = $(this).text();
	      $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
	    } );

		var table = $('#budgetTable').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			"pageLength": 25,
			'buttons': {
				// dom: {
				// 	button: {
				// 		tag:'button',
				// 		className:''
				// 	}
				// },
				buttons:[
				{
					extend: 'pageLength',
					className: 'btn btn-default',
					// text: '<i class="fa fa-print"></i> Show',
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
				}
				]
			},
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/budget/info") }}",
				"data" : data
			},
			"columns": [
				{ "data": "periode", "width":"5%"},
				{ "data": "budget_no", "width":"10%"},
				{ "data": "description", "width":"20%"},
				{ "data": "account_name"},
				{ "data": "category"},
				{ "data": "amount"},
				{ "data": "action"}
			]
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
		
      	$('#budgetTable tfoot tr').appendTo('#budgetTable thead');
	}


	function modalView(id) {
	    $("#ViewModal").modal("show");
	    var data = {
	      id:id
	    };

	    $.get('{{ url("budget/detail") }}', data, function(result, status, xhr){

	    	// $(".sisa").css({backgroundColor:'#ff7043', color:'#fff', borderTop:'0'});

	    	$("#budget_penggunaan4").text('$0');
	    	$("#budget_penggunaan5").text('$0');
	    	$("#budget_penggunaan6").text('$0');
	    	$("#budget_penggunaan7").text('$0');
	    	$("#budget_penggunaan8").text('$0');
	    	$("#budget_penggunaan9").text('$0');
	    	$("#budget_penggunaan10").text('$0');
	    	$("#budget_penggunaan11").text('$0');
	    	$("#budget_penggunaan12").text('$0');
	    	$("#budget_penggunaan13").text('$0');
	    	$("#budget_penggunaan14").text('$0');
	    	$("#budget_penggunaan15").text('$0');

	    	var d = new Date();
	    	var n = d.getMonth();

	    	// if () {}

	    	// $("#budget_awal4").text('$'+result.datas.apr_budget_awal);
	    	// $("#budget_awal5").text('$'+result.datas.may_budget_awal);
	    	// $("#budget_awal6").text('$'+result.datas.jun_budget_awal);
	    	// $("#budget_awal7").text('$'+result.datas.jul_budget_awal);
	    	// $("#budget_awal8").text('$'+result.datas.aug_budget_awal);
	    	// $("#budget_awal9").text('$'+result.datas.sep_budget_awal);
	    	// $("#budget_awal10").text('$'+result.datas.oct_budget_awal);
	    	// $("#budget_awal11").text('$'+result.datas.nov_budget_awal);
	    	// $("#budget_awal12").text('$'+result.datas.dec_budget_awal);
	    	// $("#budget_awal1").text('$'+result.datas.jan_budget_awal);
	    	// $("#budget_awal2").text('$'+result.datas.feb_budget_awal);
	    	// $("#budget_awal3").text('$'+result.datas.mar_budget_awal);

	    	// var budget_awal_total = result.datas.apr_budget_awal + result.datas.may_budget_awal + result.datas.jun_budget_awal + result.datas.jul_budget_awal + result.datas.aug_budget_awal + result.datas.sep_budget_awal + result.datas.oct_budget_awal + result.datas.nov_budget_awal + result.datas.dec_budget_awal + result.datas.jan_budget_awal + result.datas.feb_budget_awal + result.datas.mar_budget_awal;

	    	// $("#budget_awal_total").text('$'+budget_awal_total.toFixed(2));

	    	$("#budget_adj4").text('$'+result.datas.apr_after_adj);
	    	$("#budget_adj5").text('$'+result.datas.may_after_adj);
	    	$("#budget_adj6").text('$'+result.datas.jun_after_adj);
	    	$("#budget_adj7").text('$'+result.datas.jul_after_adj);
	    	$("#budget_adj8").text('$'+result.datas.aug_after_adj);
	    	$("#budget_adj9").text('$'+result.datas.sep_after_adj);
	    	$("#budget_adj10").text('$'+result.datas.oct_after_adj);
	    	$("#budget_adj11").text('$'+result.datas.nov_after_adj);
	    	$("#budget_adj12").text('$'+result.datas.dec_after_adj);
	    	$("#budget_adj1").text('$'+result.datas.jan_after_adj);
	    	$("#budget_adj2").text('$'+result.datas.feb_after_adj);
	    	$("#budget_adj3").text('$'+result.datas.mar_after_adj);

	    	var budget_adj_total = result.datas.apr_after_adj + result.datas.may_after_adj + result.datas.jun_after_adj + result.datas.jul_after_adj + result.datas.aug_after_adj + result.datas.sep_after_adj + result.datas.oct_after_adj + result.datas.nov_after_adj + result.datas.dec_after_adj + result.datas.jan_after_adj + result.datas.feb_after_adj + result.datas.mar_after_adj;

	    	$("#budget_adj_total").text('$'+budget_adj_total.toFixed(2));

	    	var budget_penggunaan_total = 0;

			$.each(result.data_penggunaan, function(key, value) {
		    	var total_penggunaan = value.PR + value.Investment + value.PO + value.Actual;
		    	$("#budget_penggunaan"+value.month_number).text('$'+total_penggunaan.toFixed(2));
		    	budget_penggunaan_total = budget_penggunaan_total + total_penggunaan;
	        })
	    
	    	$("#budget_penggunaan_total").text('$'+budget_penggunaan_total.toFixed(2));

	    	$("#budget_sisa4").text('$'+result.datas.apr_sisa_budget);
	    	$("#budget_sisa5").text('$'+result.datas.may_sisa_budget);
	    	$("#budget_sisa6").text('$'+result.datas.jun_sisa_budget);
	    	$("#budget_sisa7").text('$'+result.datas.jul_sisa_budget);
	    	$("#budget_sisa8").text('$'+result.datas.aug_sisa_budget);
	    	$("#budget_sisa9").text('$'+result.datas.sep_sisa_budget);
	    	$("#budget_sisa10").text('$'+result.datas.oct_sisa_budget);
	    	$("#budget_sisa11").text('$'+result.datas.nov_sisa_budget);
	    	$("#budget_sisa12").text('$'+result.datas.dec_sisa_budget);
	    	$("#budget_sisa1").text('$'+result.datas.jan_sisa_budget);
	    	$("#budget_sisa2").text('$'+result.datas.feb_sisa_budget);
	    	$("#budget_sisa3").text('$'+result.datas.mar_sisa_budget);

	    	var budget_sisa_total = result.datas.apr_sisa_budget + result.datas.may_sisa_budget + result.datas.jun_sisa_budget + result.datas.jul_sisa_budget + result.datas.aug_sisa_budget + result.datas.sep_sisa_budget + result.datas.oct_sisa_budget + result.datas.nov_sisa_budget + result.datas.dec_sisa_budget + result.datas.jan_sisa_budget + result.datas.feb_sisa_budget + result.datas.mar_sisa_budget;

	    	$("#budget_sisa_total").text('$'+budget_sisa_total.toFixed(2));

	    })
	  }

	  $("form#importForm").submit(function(e) {
		if ($('#upload_file').val() == '') {
			openErrorGritter('Error!', 'You need to select file');
			return false;
		}

		$("#loading").show();

		e.preventDefault();    
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("import/budget") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {
				if(result.status){
					$("#loading").hide();
					$("#upload_file").val('');
					$('#upload_budget').modal('hide');
					openSuccessGritter('Success', result.message);
					fetchTable();
				}else{
					$("#loading").hide();
					openErrorGritter('Error!', result.message);
				}
			},
			error: function(result, status, xhr){
				$("#loading").hide();
				
				openErrorGritter('Error!', result.message);
			},
			cache: false,
			contentType: false,
			processData: false
		});
	});

	  function detail_budget(budget,status){
	    $("#myModal").modal("show");

	    var data = {
	        budget:budget,
	        status:status,
	    }

	    $("#loading").show();
	    $.get('{{ url("fetch/budget/detail_table") }}', data, function(result, status, xhr) {


	      $("#loading").hide();
	      if(result.status){
	        $('#tableResult').DataTable().clear();
	        $('#tableResult').DataTable().destroy();
	        $('#tableBodyResult').html("");

	        var tableData = "";
	        var total = 0;
	        var count = 1;
	        
	        $.each(result.datas, function(key, value) {
	          tableData += '<tr>';

	          if (value.status == "PR" || value.status == "Investment") {
	            tableData += '<td>'+ value.budget +'</td>';
	            tableData += '<td>'+ value.budget_month +'</td>';
	            tableData += '<td>'+ value.category_number +'</td>';
	            tableData += '<td>'+ value.no_item +'</td>';
	            tableData += '<td>'+ value.status+ '</td>';
	            tableData += '<td>'+ value.amount.toLocaleString() +'</td>'; 
	            total += parseFloat(value.amount);           
	          }

	          else if(value.status == "PO"){
	          	tableData += '<td>'+ value.budget +'</td>';
	            tableData += '<td>'+ value.budget_month_po +'</td>';
	            tableData += '<td> Nomor PR/Inv : '+ value.category_number+ ' <br> Nomor PO : '+ value.po_number +'</td>';
	            tableData += '<td>'+ value.no_item +'</td>';
	            tableData += '<td>'+ value.status+ '</td>';
	            tableData += '<td>'+ value.amount_po.toLocaleString() +'</td>';
	            total += parseFloat(value.amount_po);
	          }

	          else if(value.status == "Transfer"){
	          	tableData += '<td>'+ value.budget_from +'</td>';
	            tableData += '<td>'+ value.request_date +'</td>';
	            tableData += '<td>-</td>';
	            tableData += '<td>'+ value.budget_from +' -> '+value.budget_to+'</td>';
	            tableData += '<td>Transfer Budget</td>';
	            tableData += '<td>'+ value.amount +'</td>';
	            total += parseFloat(value.amount);
	          }

	          else if(value.status == "Actual"){
	         //  	if (value.description == null) {
		        //   	tableData += '<td>'+ value.budget +'</td>';
		        //     tableData += '<td>'+ value.budget_month_receive +'</td>';
		        //     tableData += '<td> Nomor PR/Inv : '+ value.category_number+ ' <br> Nomor PO : '+ value.po_number +'</td>';
		        //     tableData += '<td>'+ value.no_item +'</td>';
		        //     tableData += '<td>'+ value.status+ '</td>';
		        //     tableData += '<td>'+ value.amount_receive.toLocaleString() +'</td>';
	         //    	total += parseFloat(value.amount_receive);
	         //  	}

	         //    if(value.description != null){
		        //   	tableData += '<td>'+ value.budget_no +'</td>';
		        //     tableData += '<td>'+ value.month_date +'</td>';
		        //     tableData += '<td>-</td>';
		        //     tableData += '<td>'+value.description+'</td>';
		        //     tableData += '<td>Actual Non PO</td>';
		        //     tableData += '<td>'+ value.local_amount.toLocaleString() +'</td>';
		        //     total += parseFloat(value.local_amount);
		        // }

		        tableData += '<td>'+ value.budget_no +'</td>';
	            tableData += '<td>'+ value.month_date +'</td>';
	            tableData += '<td> - </td>';
	            tableData += '<td>'+ value.description +'</td>';
	            tableData += '<td>'+ value.status +'</td>';
	            tableData += '<td>'+ value.amount.toLocaleString() +'</td>';
            	total += parseFloat(value.amount);


	          }

	          tableData += '</tr>';
	          count += 1;
	        });

	        $('#tableBodyResult').append(tableData);
	        $('#resultTotal').html('');
	        $('#resultTotal').append(total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));


	        $('#tableResult').DataTable({
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
		          // text: '<i class="fa fa-print"></i> Show',
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
		      },
		      'paging': true,
		      'lengthChange': true,
		      'searching': true,
		      'ordering': true,
		      'order': [],
		      'info': true,
		      'autoWidth': true,
		      "sPaginationType": "full_numbers",
		      "bJQueryUI": true,
		      "bAutoWidth": false,
		      "processing": true,
		    });
	      }
	      else{
	        alert('Attempt to retrieve data failed');
	      }

	    });

	    $('#judul_table').append().empty();
	    $('#judul_table').append('<center><b>'+status+' Budget '+budget+'</center></b>');
	    
	  }

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
          time: '2000'
        });
    }
</script>
@endsection

