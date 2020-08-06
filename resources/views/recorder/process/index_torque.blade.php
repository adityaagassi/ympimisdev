@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		font-size: 16px;
	}

	#tablehead> tbody > tr > td :hover {
		cursor: pointer;
		/*background-color: #e0e0e0;*/
	}

	#tablemiddle> tbody > tr > td :hover {
		cursor: pointer;
		/*background-color: #e0e0e0;*/
	}

	#tablefoot> tbody > tr > td :hover {
		cursor: pointer;
		/*background-color: #e0e0e0;*/
	}

	#tableResume > tbody> tr > td,#tableResume > thead > tr > th {
		 border: 1px solid black;
	}

	#tableResume> tbody > tr > td :hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		/* display: none; <- Crashes Chrome on hover */
		-webkit-appearance: none;
		margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
	}

	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}
	input[type="radio"] {
	}

	#loading { display: none; }


	.radio {
	  display: inline-block;
	  position: relative;
	  padding-left: 35px;
	  margin-bottom: 12px;
	  cursor: pointer;
	  font-size: 16px;
	  -webkit-user-select: none;
	  -moz-user-select: none;
	  -ms-user-select: none;
	  user-select: none;
	}

	/* Hide the browser's default radio button */
	.radio input {
	  position: absolute;
	  opacity: 0;
	  cursor: pointer;
	}

	/* Create a custom radio button */
	.checkmark {
	  position: absolute;
	  top: 0;
	  left: 0;
	  height: 25px;
	  width: 25px;
	  background-color: #eee;
	  border-radius: 50%;
	}

	/* On mouse-over, add a grey background color */
	.radio:hover input ~ .checkmark {
	  background-color: #ccc;
	}

	/* When the radio button is checked, add a blue background */
	.radio input:checked ~ .checkmark {
	  background-color: #2196F3;
	}

	/* Create the indicator (the dot/circle - hidden when not checked) */
	.checkmark:after {
	  content: "";
	  position: absolute;
	  display: none;
	}

	/* Show the indicator (dot/circle) when checked */
	.radio input:checked ~ .checkmark:after {
	  display: block;
	}

	/* Style the indicator (dot/circle) */
	.radio .checkmark:after {
	 	top: 9px;
		left: 9px;
		width: 8px;
		height: 8px;
		border-radius: 50%;
		background: white;
	}

	.content{
		padding-top: 0px;
	}

	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple"> {{ $title_jp }}</span></small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<input type="hidden" id="data" value="data">
	<div class="row">
		<div class="col-xs-6">
			<div class="row">
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-12">
							<table class="table table-bordered">
								<tr>
									<td class="label-primary" style="font-weight: bold; text-align: center;font-size: 15px;">
										PIC
									</td>
									<td class="label-success" id="pic_check" style="font-weight: bold; text-align: center;font-size: 15px;">
										{{$name}}
									</td>
									<td class="label-primary" style="font-weight: bold; text-align: center;font-size: 15px;">
										Check Date
									</td>
									<td class="label-success" id="check_date" style="font-weight: bold; text-align: center;font-size: 15px;">
										{{ date('Y-m-d H:i:s') }}
									</td>
								</tr>
								<tr>
									<td class="label-primary" style="font-weight: bold; text-align: center;font-size: 15px;">
										Product
									</td>
									<td class="label-success" id="prod_type" style="font-weight: bold; text-align: center;font-size: 15px;">
									</td>
									<td class="label-primary" style="font-weight: bold; text-align: center;font-size: 15px;">
										Tipe Pengecekan
									</td>
									<td class="label-success" id="check_type" style="font-weight: bold; text-align: center;font-size: 15px;">
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-6">
			<div class="row">
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-12">
							<table class="table table-bordered">
								<tr>
									<td class="label-primary" style="font-weight: bold; text-align: center;font-size: 15px;">
										Tanggal Injeksi Middle
									</td>
									<td class="label-success" id="injection_date_middle_fix" style="font-weight: bold; text-align: center;font-size: 15px;">
									</td>
									<td class="label-primary" style="font-weight: bold; text-align: center;font-size: 15px;">
										Mesin Injeksi Middle
									</td>
									<td class="label-success" id="mesin_middle" style="font-weight: bold; text-align: center;font-size: 15px;">
									</td>
								</tr>
								<tr>
									<td class="label-primary" style="font-weight: bold; text-align: center;font-size: 15px;">
										Tanggal Injeksi Head / Foot
									</td>
									<td class="label-success" id="injection_date_head_foot_fix" style="font-weight: bold; text-align: center;font-size: 15px;">
									</td>
									<td class="label-primary" style="font-weight: bold; text-align: center;font-size: 15px;">
										Mesin Injeksi Head / Foot
									</td>
									<td class="label-success" id="mesin_head_foot" style="font-weight: bold; text-align: center;font-size: 15px;">
									</td>
								</tr>
								<tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row" style="padding-top:0px">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-xs-12" style="padding:0">
						<div class="col-xs-12">
							<span style="font-size: 20px; font-weight: bold;"><center></center></span>
						</div>
						<table class="table table-hover table-striped table-bordered" id="tableResume">
							<thead>
								<tr>
									<th style="width: 1%;font-size: 12px;color:white;background-color:#605ca8"><center>No.</center></th>
									<th style="width: 2%;font-size: 12px;color:white;background-color:#605ca8"><center>Middle</center></th>
									<th style="width: 2%;font-size: 12px;color:white;background-color:#605ca8"><center>Head / Foot</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Torque 1</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Torque 2</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Torque 3</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Average</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Judgement</center></th>
								</tr>
							</thead>
							<tbody id="tableBodyResume">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<textarea name="notes" id="notes" style="width: 100%;text-align: center;font-size: 20px;vertical-align: middle;" placeholder="Notes"></textarea>
		</div>
	</div>
	<div class="row" style="padding-top: 20px">
		<div class="col-xs-12">
			<button class="btn btn-danger" onclick="konfirmasi()" id="selesai_button" style="font-size:35px; width: 100%; font-weight: bold; padding: 0;">
				SELESAI PROSES
			</button>
			<button class="btn btn-warning" onclick="reset()" id="reset_button" style="font-size:35px; width: 100%; font-weight: bold; padding: 0;">
				RESET
			</button>
		</div>
	</div>

	<div class="modal fade" id="modalMiddleHeadFoot">
		<div class="modal-dialog modal-lg" style="width: 1200px">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div class="col-xs-12">
							<div class="col-xs-12" id="product_choice" style="padding-top: 20px">
								<div class="row">
									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-12">
												<center><span style="font-weight: bold; font-size: 18px;">Tipe Produk</span></center>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-12">
											@foreach($product_type as $product_type)
											<div class="col-xs-3" style="padding-top: 5px">
												<center><button class="btn btn-primary" id="{{$product_type}}" style="width: 180px;font-size: 15px" onclick="getProduct(this.id)">
													{{$product_type}}
												</button></center>
											</div>
								            @endforeach
										</div>
								    </div>
								</div>
							</div>
							<div class="col-xs-12" id="product_fix" style="padding-top: 20px">
								<div class="row">
									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-12">
												<center><span style="font-weight: bold; font-size: 18px;">Tipe Produk</span></center>
											</div>
										</div>
									</div>
									<div class="col-xs-12" style="padding-top: 10px">
										<button class="btn btn-primary" id="product_fix2" style="width: 100%;font-size: 20px;font-weight: bold;" onclick="changeProduct()">
											YRS
										</button>
									</div>
								</div>
							</div>
							<div class="col-xs-12" id="check_type_choice" style="padding-top: 20px">
								<div class="row">
									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-12">
												<center><span style="font-weight: bold; font-size: 18px;">Tipe Pengecekan</span></center>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-12">
											<div class="col-xs-6" style="padding-top: 5px">
												<center><button class="btn btn-danger" id="HJ-MJ" style="width: 100%;font-size: 20px" onclick="getCheckType(this.id)">
													<b>HJ-MJ</b>
												</button></center>
											</div>
											<div class="col-xs-6" style="padding-top: 5px">
												<center><button class="btn btn-danger" id="MJ-FJ" style="width: 100%;font-size: 20px" onclick="getCheckType(this.id)">
													<b>MJ-FJ</b>
												</button></center>
											</div>
										</div>
								    </div>
								</div>
							</div>
							<div class="col-xs-12" id="check_type_fix" style="padding-top: 20px">
								<div class="row">
									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-12">
												<center><span style="font-weight: bold; font-size: 18px;">Tipe Pengecekan</span></center>
											</div>
										</div>
									</div>
									<div class="col-xs-12" style="padding-top: 10px">
										<button class="btn btn-danger" id="check_type_fix2" style="width: 100%;font-size: 20px;font-weight: bold;" onclick="changeCheckType()">
											TORQUE
										</button>
									</div>
								</div>
							</div>
							<div class="col-xs-6" style="padding-top: 20px" id="tanggal_injeksi_middle">
								<div class="row">
									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-12">
												<center><span style="font-weight: bold; font-size: 18px;">Tanggal Injeksi Middle</span></center>
											</div>
										</div>
									</div>
									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-12">
												<input id="injection_date_middle" style="font-size: 20px; height: 40px; text-align: center;" type="text" class="form-control" placeholder="Tanggal Injeksi Middle">
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xs-6" style="padding-top: 20px" id="tanggal_injeksi_head_foot">
								<div class="row">
									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-12">
												<center><span style="font-weight: bold; font-size: 18px;">Tanggal Injeksi Head / Foot</span></center>
											</div>
										</div>
									</div>
									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-12">
												<input id="injection_date_head_foot" style="font-size: 20px; height: 40px; text-align: center;" type="text" class="form-control" placeholder="Tanggal Injeksi Head / Foot">
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xs-6" id="mesin_middle_choice" style="padding-top: 20px">
								<div class="row">
									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-12">
												<center><span style="font-weight: bold; font-size: 18px;">Mesin Injeksi Middle</span></center>
											</div>
										</div>
									</div>
									<div class="col-xs-12">
											@foreach($mesin as $mesin)
											<div class="col-xs-2" style="padding-top: 5px">
												<center><button class="btn btn-success" id="{{$mesin}}" style="width: 50px;font-size: 15px" onclick="getMesinMiddle(this.id)">
													{{$mesin}}
												</button></center>
											</div>
											@endforeach
									</div>
								</div>
							</div>
							<div class="col-xs-6" id="mesin_middle_fix" style="padding-top: 20px">
								<div class="row">
									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-12">
												<center><span style="font-weight: bold; font-size: 18px;">Mesin Injeksi Middle</span></center>
											</div>
										</div>
									</div>
									<div class="col-xs-12">
											<button class="btn btn-success" id="mesin_middle_fix2" style="width: 100%;font-size: 20px;font-weight: bold;" onclick="changeMesinMiddle()">
												#0
											</button>
									</div>
								</div>
							</div>
							<div class="col-xs-6" id="mesin_head_foot_choice" style="padding-top: 20px">
								<div class="row">
									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-12">
												<center><span style="font-weight: bold; font-size: 18px;">Mesin Injeksi Head / Foot</span></center>
											</div>
										</div>
									</div>
									<div class="col-xs-12">
											@foreach($mesin2 as $mesin2)
											<div class="col-xs-2" style="padding-top: 5px">
												<center><button class="btn btn-warning" id="{{$mesin2}}" style="width: 50px;font-size: 15px" onclick="getMesinHeadFoot(this.id)">
													{{$mesin2}}
												</button></center>
											</div>
											@endforeach
									</div>
								</div>
							</div>
							<div class="col-xs-6" id="mesin_head_foot_fix" style="padding-top: 20px">
								<div class="row">
									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-12">
												<center><span style="font-weight: bold; font-size: 18px;">Mesin Injeksi Head / Foot</span></center>
											</div>
										</div>
									</div>
									<div class="col-xs-12">
											<button class="btn btn-warning" id="mesin_head_foot_fix2" style="width: 100%;font-size: 20px;font-weight: bold;" onclick="changeMesinHeadFoot()">
												#0
											</button>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xs-12" style="padding-top: 20px">
							<div class="col-xs-6" style="padding-top:10px" id="middle_choice">
								<div class="col-xs-12">
									<span style="font-size: 20px; font-weight: bold;"><center>MIDDLE</center></span>
								</div>
								<table class="table" id="tablemiddle">
									<thead>
										<tr>
											<th style="width: 1%;"></th>
										</tr>					
									</thead>
									<tbody>
										<tr>
											<td width="50%" onclick="getData(14)">
												<center>
													<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
														1-4
													</button>
												</center>
											</td>
											<td width="50%" onclick="getData(15)">
												<center>
													<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
														5-8
													</button>
												</center>
											</td>
										</tr>	
										<tr>
											<td width="50%" onclick="getData(16)">
												<center>
													<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
														9-12
													</button>
												</center>
											</td>
											<td width="50%" onclick="getData(17)">
												<center>
													<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
														13-16
													</button>
												</center>
											</td>
										</tr>
										<tr>
											<td width="50%" onclick="getData(19)">
												<center>
													<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
														21-22
													</button>
												</center>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="col-xs-6" style="padding-top:10px" id="head_choice">
								<div class="col-xs-12">
									<span style="font-size: 20px; font-weight: bold;"><center>HEAD</center></span>
								</div>
								<table class="table" id="tablehead" style="padding-top: 0px">
									<thead>
										<tr>
											<th style="width: 1%;"></th>
										</tr>					
									</thead>
									<tbody>
										<tr>
											<td width="50%" onclick="getData2(1)">
												<center>
													<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
														1-4
													</button>
												</center>
											</td>
											<td width="50%" onclick="getData2(2)">
												<center>
													<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
														5-8
													</button>
												</center>
											</td>
										</tr>	
										<tr>
											<td width="50%" onclick="getData2(3)">
												<center>
													<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
														9-12
													</button>
												</center>
											</td>
											<td width="50%" onclick="getData2(4)">
												<center>
													<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
														13-16
													</button>
												</center>
											</td>
										</tr>
										<tr>
											<td width="50%" onclick="getData2(5)">
												<center>
													<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
														17-20
													</button>
												</center>
											</td>
											<td width="50%" onclick="getData2(18)">
												<center>
													<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
														21-22
													</button>
												</center>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="col-xs-6" style="padding-top:10px" id="foot_choice">
								<div class="col-xs-12">
									<span style="font-size: 20px; font-weight: bold;"><center>FOOT</center></span>
								</div>
								<table class="table" id="tablefoot">
									<thead>
										<tr>
											<th style="width: 1%;"></th>
										</tr>					
									</thead>
									<tbody>
										<tr>
											<td width="50%" onclick="getData2(9)">
												<center>
													<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
														1-6
													</button>
												</center>
											</td>
											<td width="50%" onclick="getData2(10)">
												<center>
													<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
														11-16
													</button>
												</center>
											</td>
										</tr>	
										<tr>
											<td width="50%" onclick="getData2(11)">
												<center>
													<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
														1-4
													</button>
												</center>
											</td>
											<td width="50%" onclick="getData2(12)">
												<center>
													<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
														5-8
													</button>
												</center>
											</td>
										</tr>
										<tr>
											<td width="50%" onclick="getData2(13)">
												<center>
													<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
														01-04
													</button>
												</center>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<div class="col-xs-6" style="padding-top: 0px" id="middle_fix">
							<div class="col-xs-12">
								<input type="hidden" id="middle_id" style="width: 24%; height: 30px; font-size:20px; text-align: center;" disabled>
								<input type="hidden" id="middle_value" style="width: 24%; height: 30px; font-size:20px; text-align: center;" disabled>
								<table class="table table-bordered">
									<tr>
										<td>
											<input type="text" id="middle_fix_1" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
										<td>
											<input type="text" id="middle_fix_2" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
										<td>
											<input type="text" id="middle_fix_3" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
										<td>
											<input type="text" id="middle_fix_4" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
									</tr>
								</table>
							</div>
						</div>
						<div class="col-xs-6"  id="head_foot_fix">
							<div class="col-xs-12">
								<input type="hidden" id="head_foot_id" style="width: 11%; height: 30px; font-size: 20px; text-align: center;" disabled>
								<input type="hidden" id="head_foot_value" style="width: 30%; height: 30px; font-size: 20px; text-align: center;" disabled>
								<table class="table table-bordered">
									<tr>
										<td>
											<input type="text" id="head_foot_fix_1" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
										<td>
											<input type="text" id="head_foot_fix_2" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
										<td>
											<input type="text" id="head_foot_fix_3" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
										<td>
											<input type="text" id="head_foot_fix_4" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
										<td>
											<input type="text" id="head_foot_fix_5" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
										<td>
											<input type="text" id="head_foot_fix_6" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
									</tr>
								</table>
							</div>
						</div>
						<div class="col-xs-12" style="padding-top: 20px">
							<div class="modal-footer">
								<button onclick="confirm()" class="btn btn-success" style="width: 100%;font-size: 40px;font-weight: bold;">
									MULAI PROSES
								</button>
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
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	$('#injection_date_middle').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
      todayHighlight: true
    });

    $('#injection_date_head_foot').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
      todayHighlight: true
    });

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('#modalMiddleHeadFoot').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('body').toggleClass("sidebar-collapse");
		$('.select2').select2({
        language : {
          noResults : function(params) {
            	return "There is no cpar with status 'close'";
	        }
	      }
	    });
		$('#reset_button').hide();
		$('#product_fix').hide();
		$('#check_type_fix').hide();
		$('#mesin_middle_fix').hide();
		$('#mesin_head_foot_fix').hide();
		$('#tanggal_injeksi_middle').hide();
		$('#tanggal_injeksi_head_foot').hide();
		$('#mesin_middle_choice').hide();
		$('#mesin_head_foot_choice').hide();
		$('#head_choice').hide();
		$('#foot_choice').hide();
		$('#middle_choice').hide();
		$('#middle_fix').hide();
		$('#head_foot_fix').hide();
	});

	$('#modalMiddleHeadFoot').on('shown.bs.modal', function () {
	});

	function getCheckType(check_type) {
		$('#check_type_choice').hide();
		$('#check_type_fix').show();
		$('#check_type_fix2').html(check_type);
		if (check_type === 'HJ-MJ') {
			$('#head_choice').show();
			$('#foot_choice').hide();
		}else{
			$('#head_choice').hide();
			$('#foot_choice').show();
		}
		$('#tanggal_injeksi_middle').show();
		$('#tanggal_injeksi_head_foot').show();
		$('#mesin_middle_choice').show();
		$('#mesin_head_foot_choice').show();
		$('#middle_choice').show();
		$('#middle_fix').show();
		$('#head_foot_fix').show();
	}

	function changeCheckType() {
		$('#check_type_choice').show();
		$('#check_type_fix').hide();
		$('#check_type_fix2').html('TORQUE');
		$('#head_choice').hide();
		$('#foot_choice').hide();
		$('#middle_choice').hide();
		$('#middle_fix').hide();
		$('#head_foot_fix').hide();
		$('#tanggal_injeksi_middle').hide();
		$('#tanggal_injeksi_head_foot').hide();
		$('#mesin_middle_choice').hide();
		$('#mesin_head_foot_choice').hide();
		$('#mesin_middle_fix').hide();
		$('#mesin_head_foot_fix').hide();

		$('#head_foot_value').val("");
		$('#head_foot_fix_id').val("");
		$('#head_foot_fix_1').val("");
		$('#head_foot_fix_2').val("");
		$('#head_foot_fix_3').val("");
		$('#head_foot_fix_4').val("");
		$('#head_foot_fix_5').val("");
		$('#head_foot_fix_6').val("");

		$('#middle_value').val("");
		$('#middle_id').val("");
		$('#middle_fix_1').val("");
		$('#middle_fix_2').val("");
		$('#middle_fix_3').val("");
		$('#middle_fix_4').val("");
	}

	function getProduct(product) {
		$('#product_choice').hide();
		$('#product_fix').show();
		$('#product_fix2').html(product);
	}

	function changeProduct() {
		$('#product_choice').show();
		$('#product_fix').hide();
		$('#product_fix2').html('YRS');
	}

	function getMesinMiddle(mesin) {
		$('#mesin_middle_choice').hide();
		$('#mesin_middle_fix').show();
		$('#mesin_middle_fix2').html(mesin);
	}

	function changeMesinMiddle() {
		$('#mesin_middle_choice').show();
		$('#mesin_middle_fix').hide();
		$('#mesin_middle_fix2').html('#0');
	}

	function getMesinHeadFoot(mesin) {
		$('#mesin_head_foot_choice').hide();
		$('#mesin_head_foot_fix').show();
		$('#mesin_head_foot_fix2').html(mesin);
	}

	function changeMesinHeadFoot() {
		$('#mesin_head_foot_choice').show();
		$('#mesin_head_foot_fix').hide();
		$('#mesin_head_foot_fix2').html('#0');
	}

	function getData(no_cavity){
		var data = {
			no_cavity : no_cavity,
			type : 'middle',
		}

		if (no_cavity == 14) {
			$('#middle_value').val('1-4');
		}else if (no_cavity == 15) {
			$('#middle_value').val('5-8');
		}else if (no_cavity == 16) {
			$('#middle_value').val('9-12');
		}else if (no_cavity == 17) {
			$('#middle_value').val('13-16');
		}

		$.get('{{ url("index/fetch_push_block") }}', data, function(result, status, xhr){
			if(result.status){
				$('#middle_id').val(result.id);
				$('#middle_fix_1').val(result.cavity_1);
				$('#middle_fix_2').val(result.cavity_2);
				$('#middle_fix_3').val(result.cavity_3);
				$('#middle_fix_4').val(result.cavity_4);
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function getData2(no_cavity){
		if ($('#check_type_fix2').text() == 'HJ-MJ') {
			var type = 'head';
		}else{
			var type = 'foot';
		}
		var data = {
			no_cavity : no_cavity,
			type : type,
		}

		if (no_cavity == 1) {
			$('#head_foot_value').val('1-4');
		}else if (no_cavity == 2) {
			$('#head_foot_value').val('5-8');
		}else if (no_cavity == 3) {
			$('#head_foot_value').val('9-12');
		}else if (no_cavity == 4) {
			$('#head_foot_value').val('13-16');
		}else if (no_cavity == 5) {
			$('#head_foot_value').val('17-20');
		}else if (no_cavity == 9) {
			$('#head_foot_value').val('1-6');
		}else if (no_cavity == 10) {
			$('#head_foot_value').val('11-16');
		}else if (no_cavity == 11) {
			$('#head_foot_value').val('1-4');
		}else if (no_cavity == 12) {
			$('#head_foot_value').val('5-8');
		}else if (no_cavity == 13) {
			$('#head_foot_value').val('01-04');
		}

		$.get('{{ url("index/fetch_push_block") }}', data, function(result, status, xhr){
			if(result.status){
				$('#head_foot_id').val(result.id);
				$('#head_foot_fix_1').val(result.cavity_1);
				$('#head_foot_fix_2').val(result.cavity_2);
				$('#head_foot_fix_3').val(result.cavity_3);
				$('#head_foot_fix_4').val(result.cavity_4);
				$('#head_foot_fix_5').val(result.cavity_5);
				$('#head_foot_fix_6').val(result.cavity_6);
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function confirm() {
		if($('#injection_date_middle').val() == '' || $('#injection_date_head_foot').val() == '' || $('#middle_id').val() == '' || $('#head_foot_id').val() == '' || $('#mesin_middle_fix2').text() == '#0' || $('#mesin_head_foot_fix2').text() == '#0' || $('#product_fix2').text() == 'YRS'){
			alert('Semua Data Harus Diisi.');
		}else{
			$('#prod_type').html($('#product_fix2').text());
			$('#injection_date_middle_fix').html($('#injection_date_middle').val());
			$('#injection_date_head_foot_fix').html($('#injection_date_head_foot').val());
			$('#mesin_middle').html($('#mesin_middle_fix2').text());
			$('#mesin_head_foot').html($('#mesin_head_foot_fix2').text());
			$('#check_type').html($('#check_type_fix2').text());
			$('#modalMiddleHeadFoot').modal('hide');
			itemresume($("#middle_id").val(),$("#head_foot_id").val());
			get_temp();
			setInterval(update_temp,60000);
		}
	}

	function create_temp(){

		var check_date = $("#check_date").text();
		var check_type = $("#check_type").text();
		var injection_date_middle = $("#injection_date_middle_fix").text();
		var injection_date_head_foot = $("#injection_date_head_foot_fix").text();
		var mesin_middle = $("#mesin_middle").text();
		var mesin_head_foot = $("#mesin_head_foot").text();
		var product_type = $("#prod_type").text();
		var pic_check = $("#pic_check").text();
		var push_block_code = '{{ $remark }}';

		if ($('#product_fix2').text() == "YRF-21K//ID" || $('#product_fix2').text() == "YRF-21//ID" || $('#product_fix2').text() == "YRF-21 (FSA)") {
			var array_middle = [];
			var array_head_foot = [];
			var array_middle2 = [];
			var array_head_foot2 = [];

			var status_false = 0;

			indexHeadFoot = 2;

			for(var i = 1; i <= 2; i++){
				array_middle.push($("#middle_fix_"+[i]).val());
			}
			for(var j = 1; j <= indexHeadFoot; j++){
				array_head_foot.push($("#head_foot_fix_"+[j]).val());
			}

			var index = 1;
			for(var i=0;i<2;i++){
				for(var j=0;j<indexHeadFoot;j++){
					array_middle2.push(array_middle[i]);
					array_head_foot2.push(array_head_foot[j]);
					index++;
				}
			}

			var data2 = {
				push_block_code : push_block_code,
				check_date : check_date,
				check_type : check_type,
				injection_date_middle : injection_date_middle,
				injection_date_head_foot : injection_date_head_foot,
				mesin_middle : mesin_middle,
				mesin_head_foot : mesin_head_foot,
				pic_check : pic_check,
				product_type : product_type,
				middle : array_middle2,
				head_foot : array_head_foot2
			}
			$.post('{{ url("index/push_block_recorder/create_temp_torque") }}', data2, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success', result.message);
				}
				else{
					openErrorGritter('Error!', result.message);
				}
			});
		}else{
			var array_middle = [];
			var array_head_foot = [];
			var array_middle2 = [];
			var array_head_foot2 = [];

			var status_false = 0;

			if ($('#head_foot_id').val() == 9 || $('#head_foot_id').val() == 10) {
				indexHeadFoot = 6;
			}else{
				indexHeadFoot = 4;
			}

			for(var i = 1; i <= 4; i++){
				array_middle.push($("#middle_fix_"+[i]).val());
			}
			for(var j = 1; j <= indexHeadFoot; j++){
				array_head_foot.push($("#head_foot_fix_"+[j]).val());
			}

			var index = 1;
			for(var i=0;i<4;i++){
				for(var j=0;j<indexHeadFoot;j++){
					array_middle2.push(array_middle[i]);
					array_head_foot2.push(array_head_foot[j]);
					index++;
				}
			}

			var data2 = {
				push_block_code : push_block_code,
				check_date : check_date,
				check_type : check_type,
				injection_date_middle : injection_date_middle,
				injection_date_head_foot : injection_date_head_foot,
				mesin_middle : mesin_middle,
				mesin_head_foot : mesin_head_foot,
				pic_check : pic_check,
				product_type : product_type,
				middle : array_middle2,
				head_foot : array_head_foot2
			}
			$.post('{{ url("index/push_block_recorder/create_temp_torque") }}', data2, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success', result.message);
				}
				else{
					openErrorGritter('Error!', result.message);
				}
			});
		}
	}

	function get_temp() {
		if ($('#product_fix2').text() == "YRF-21K//ID" || $('#product_fix2').text() == "YRF-21//ID" || $('#product_fix2').text() == "YRF-21 (FSA)") {
			var array_middle = [];
			var array_head_foot = [];
			var array_middle2 = [];
			var array_head_foot2 = [];

			var check_type = $("#check_type").text();
			indexHeadFoot = 2;

			for(var i = 1; i <= 2; i++){
				array_middle.push($("#middle_fix_"+[i]).val());
			}
			for(var j = 1; j <= indexHeadFoot; j++){
				array_head_foot.push($("#head_foot_fix_"+[j]).val());
			}
			
			var index = 1;
			for(var i=0;i<2;i++){
				for(var j=0;j<indexHeadFoot;j++){
					array_middle2.push(array_middle[i]);
					array_head_foot2.push(array_head_foot[j]);
					index++;
				}
			}

			var data = {
				array_middle : array_middle2,
				array_head_foot : array_head_foot2,
				remark : '{{$remark}}',
				product_type : $("#prod_type").text(),
				indexHeadFoot:indexHeadFoot,
				check_type:check_type
			}

			$.get('{{ url("index/push_block_recorder/get_temp_torque") }}',data,  function(result, status, xhr){
				if(result.status){
					if(result.datas.length != 0){
						index = 1;
						$.each(result.datas, function(key, value) {
							$('#torque_1_'+index).val(value.torque1);
							$('#torque_2_'+index).val(value.torque2);
							$('#torque_3_'+index).val(value.torque3);
							$('#average_'+index).html(value.torqueavg);
							$('#judgement_'+index).html(value.judgement);
							$("#prod_type").html(value.product_type);
							$("#check_date").html(value.check_date);
							$("#check_type").html(value.check_type);
							$('#injection_date_middle_fix').html(value.injection_date_middle);
							$('#injection_date_head_foot_fix').html(value.injection_date_head_foot);
							$('#mesin_middle').html(value.mesin_middle);
							$('#mesin_head_foot').html(value.mesin_head_foot);
							$("#notes").val(value.notes);
							index++;
							// console.table(index);
						});
						openSuccessGritter('Success!', result.message);
					}else{
						create_temp();
					}
				}
				else{
					openErrorGritter('Error!', result.message);
					audio_error.play();
					
				}
			});
		}else{
			var array_middle = [];
			var array_head_foot = [];
			var array_middle2 = [];
			var array_head_foot2 = [];

			var check_type = $("#check_type").text();

			if ($('#head_foot_id').val() == 9 || $('#head_foot_id').val() == 10) {
				indexHeadFoot = 6;
			}else{
				indexHeadFoot = 4;
			}

			for(var i = 1; i <= 4; i++){
				array_middle.push($("#middle_fix_"+[i]).val());
			}
			for(var j = 1; j <= indexHeadFoot; j++){
				array_head_foot.push($("#head_foot_fix_"+[j]).val());
			}
			
			var index = 1;
			for(var i=0;i<4;i++){
				for(var j=0;j<indexHeadFoot;j++){
					array_middle2.push(array_middle[i]);
					array_head_foot2.push(array_head_foot[j]);
					index++;
				}
			}

			var data = {
				array_middle : array_middle2,
				array_head_foot : array_head_foot2,
				remark : '{{$remark}}',
				product_type : $("#prod_type").text(),
				indexHeadFoot:indexHeadFoot,
				check_type:check_type
			}

			$.get('{{ url("index/push_block_recorder/get_temp_torque") }}',data,  function(result, status, xhr){
				if(result.status){
					if(result.datas.length != 0){
						index = 1;
						$.each(result.datas, function(key, value) {
							$('#torque_1_'+index).val(value.torque1);
							$('#torque_2_'+index).val(value.torque2);
							$('#torque_3_'+index).val(value.torque3);
							$('#average_'+index).html(value.torqueavg);
							$('#judgement_'+index).html(value.judgement);
							$("#prod_type").html(value.product_type);
							$("#check_date").html(value.check_date);
							$("#check_type").html(value.check_type);
							$('#injection_date_middle_fix').html(value.injection_date_middle);
							$('#injection_date_head_foot_fix').html(value.injection_date_head_foot);
							$('#mesin_middle').html(value.mesin_middle);
							$('#mesin_head_foot').html(value.mesin_head_foot);
							$("#notes").val(value.notes);
							index++;
							// console.table(index);
						});
						openSuccessGritter('Success!', result.message);
					}else{
						create_temp();
					}
				}
				else{
					openErrorGritter('Error!', result.message);
					audio_error.play();
					
				}
			});
		}
	}

	function update_temp(){
		var notes =  $("#notes").val();
		var push_block_code = '{{ $remark }}';
		var check_type = $("#check_type").text();

		if ($('#product_fix2').text() == "YRF-21K//ID" || $('#product_fix2').text() == "YRF-21//ID" || $('#product_fix2').text() == "YRF-21 (FSA)") {
			var array_middle = [];
			var array_head_foot = [];

			var torque_1 = [];
			var torque_2 = [];
			var torque_3 = [];
			var average = [];
			var judgement = [];

			var status_false = 0;
			indexHeadFoot = 2;			

			var index = 1;
			for(var i=0;i<2;i++){
				for(var j=0;j<indexHeadFoot;j++){
					array_middle.push($('#middle_'+index).text());
					array_head_foot.push($('#head_foot_'+index).text());
					if ($('#average_'+index).text() == "") {
						average.push(null);
					}else{
						average.push(parseFloat($('#average_'+index).text()));
					}
					if ($('#torque_1_'+index).val() == "") {
						torque_1.push(null);
					}else{
						torque_1.push(parseFloat($('#torque_1_'+index).val()));
					}
					if ($('#torque_2_'+index).val() == "") {
						torque_2.push(null);
					}else{
						torque_2.push(parseFloat($('#torque_2_'+index).val()));
					}
					if ($('#torque_3_'+index).val() == "") {
						torque_3.push(null);
					}else{
						torque_3.push(parseFloat($('#torque_3_'+index).val()));
					}
					if ($('#judgement_'+index).text() == "") {
						judgement.push(null);
					}else{
						judgement.push($('#judgement_'+index).text());
					}
					index++;
				}
			}

			var data2 = {
				push_block_code : push_block_code,
				middle : array_middle,
				head_foot : array_head_foot,
				check_type : check_type,
				torque_1 : torque_1,
				torque_2 : torque_2,
				torque_3 : torque_3,
				average : average,
				judgement : judgement,
				notes:notes
			}
			$.post('{{ url("index/push_block_recorder/update_temp_torque") }}', data2, function(result, status, xhr){
				if(result.status){
					// openSuccessGritter('Success', result.message);
				}
				else{
					// openErrorGritter('Error!', result.message);
				}
			});
		}else{
			var array_middle = [];
			var array_head_foot = [];

			var torque_1 = [];
			var torque_2 = [];
			var torque_3 = [];
			var average = [];
			var judgement = [];

			var status_false = 0;

			if ($('#head_foot_id').val() == 9 || $('#head_foot_id').val() == 10) {
				indexHeadFoot = 6;
			}else{
				indexHeadFoot = 4;
			}

			var index = 1;
			for(var i=0;i<4;i++){
				for(var j=0;j<indexHeadFoot;j++){
					array_middle.push($('#middle_'+index).text());
					array_head_foot.push($('#head_foot_'+index).text());
					if ($('#average_'+index).text() == "") {
						average.push(null);
					}else{
						average.push(parseFloat($('#average_'+index).text()));
					}
					if ($('#torque_1_'+index).val() == "") {
						torque_1.push(null);
					}else{
						torque_1.push(parseFloat($('#torque_1_'+index).val()));
					}
					if ($('#torque_2_'+index).val() == "") {
						torque_2.push(null);
					}else{
						torque_2.push(parseFloat($('#torque_2_'+index).val()));
					}
					if ($('#torque_3_'+index).val() == "") {
						torque_3.push(null);
					}else{
						torque_3.push(parseFloat($('#torque_3_'+index).val()));
					}
					if ($('#judgement_'+index).text() == "") {
						judgement.push(null);
					}else{
						judgement.push($('#judgement_'+index).text());
					}
					index++;
				}
			}

			var data2 = {
				push_block_code : push_block_code,
				middle : array_middle,
				head_foot : array_head_foot,
				check_type : check_type,
				torque_1 : torque_1,
				torque_2 : torque_2,
				torque_3 : torque_3,
				average : average,
				judgement : judgement,
				notes:notes
			}
			$.post('{{ url("index/push_block_recorder/update_temp_torque") }}', data2, function(result, status, xhr){
				if(result.status){
					// openSuccessGritter('Success', result.message);
				}
				else{
					// openErrorGritter('Error!', result.message);
				}
			});
		}
	}

	function konfirmasi(){

		var notes =  $("#notes").val();

		var check_date = $("#check_date").text();
		var check_type = $("#check_type").text();
		var injection_date_middle = $("#injection_date_middle_fix").text();
		var injection_date_head_foot = $("#injection_date_head_foot_fix").text();
		var mesin_middle = $("#mesin_middle").text();
		var mesin_head_foot = $("#mesin_head_foot").text();
		var product_type = $("#prod_type").text();
		var pic_check = $("#pic_check").text();
		var push_block_code = '{{ $remark }}';

		if ($('#product_fix2').text() == "YRF-21K//ID" || $('#product_fix2').text() == "YRF-21//ID" || $('#product_fix2').text() == "YRF-21 (FSA)") {
			var array_middle = [];
			var array_head_foot = [];

			var torque_1 = [];
			var torque_2 = [];
			var torque_3 = [];
			var average = [];
			var judgement = [];

			var status_false = 0;
			indexHeadFoot = 2;

			var index = 1;
			for(var i=0;i<2;i++){
				for(var j=0;j<indexHeadFoot;j++){
					array_middle.push($('#middle_'+index).text());
					array_head_foot.push($('#head_foot_'+index).text());
					torque_1.push(parseFloat($('#torque_1_'+index).val()));
					torque_2.push(parseFloat($('#torque_2_'+index).val()));
					torque_3.push(parseFloat($('#torque_3_'+index).val()));
					if ($('#average_'+index).text() == "") {
						average.push(parseFloat(0));
					}else{
						average.push(parseFloat($('#average_'+index).text()));
					}
					if ($('#judgement_'+index).text() == "") {
						judgement.push(parseFloat(0));
					}else{
						judgement.push($('#judgement_'+index).text());
					}
					if ($('#torque_1_'+index).val() == "" || $('#torque_2_'+index).val() == "" || $('#torque_3_'+index).val() == "" || $('#average_'+index).text() == ""|| $('#judgement_'+index).text() == "") {
						status_false++;
					}
					index++;
				}
			}

			if(status_false > 0){
				alert('Semua Data Harus Diisi');
			}
			else{
				$('#loading').show();
				$('#selesai_button').prop('disabled', true);
				var data2 = {
					push_block_code : push_block_code,
					check_date : check_date,
					check_type : check_type,
					injection_date_middle : injection_date_middle,
					injection_date_head_foot : injection_date_head_foot,
					mesin_middle : mesin_middle,
					mesin_head_foot : mesin_head_foot,
					pic_check : pic_check,
					product_type : product_type,
					middle : array_middle,
					head_foot : array_head_foot,
					torque_1 : torque_1,
					torque_2 : torque_2,
					torque_3 : torque_3,
					average : average,
					judgement : judgement,
					notes:notes
				}
				$.post('{{ url("index/push_block_recorder/create_torque") }}', data2, function(result, status, xhr){
					if(result.status){
						$('#loading').hide();
						openSuccessGritter('Success', result.message);
						alert('Pengisian Selesai.');
						location.reload();
					}
					else{
						openErrorGritter('Error!', result.message);
					}
				});
			}
		}else{
			var array_middle = [];
			var array_head_foot = [];

			var torque_1 = [];
			var torque_2 = [];
			var torque_3 = [];
			var average = [];
			var judgement = [];

			var status_false = 0;

			if ($('#head_foot_id').val() == 9 || $('#head_foot_id').val() == 10) {
				indexHeadFoot = 6;
			}else{
				indexHeadFoot = 4;
			}

			var index = 1;
			for(var i=0;i<4;i++){
				for(var j=0;j<indexHeadFoot;j++){
					array_middle.push($('#middle_'+index).text());
					array_head_foot.push($('#head_foot_'+index).text());
					torque_1.push(parseFloat($('#torque_1_'+index).val()));
					torque_2.push(parseFloat($('#torque_2_'+index).val()));
					torque_3.push(parseFloat($('#torque_3_'+index).val()));
					if ($('#average_'+index).text() == "") {
						average.push(parseFloat(0));
					}else{
						average.push(parseFloat($('#average_'+index).text()));
					}
					if ($('#judgement_'+index).text() == "") {
						judgement.push(parseFloat(0));
					}else{
						judgement.push($('#judgement_'+index).text());
					}
					if ($('#torque_1_'+index).val() == "" || $('#torque_2_'+index).val() == "" || $('#torque_3_'+index).val() == "" || $('#average_'+index).text() == ""|| $('#judgement_'+index).text() == "") {
						status_false++;
					}
					index++;
				}
			}

			if(status_false > 0){
				alert('Semua Data Harus Diisi');
			}
			else{
				$('#loading').show();
				$('#selesai_button').prop('disabled', true);
				var data2 = {
					push_block_code : push_block_code,
					check_date : check_date,
					check_type : check_type,
					injection_date_middle : injection_date_middle,
					injection_date_head_foot : injection_date_head_foot,
					mesin_middle : mesin_middle,
					mesin_head_foot : mesin_head_foot,
					pic_check : pic_check,
					product_type : product_type,
					middle : array_middle,
					head_foot : array_head_foot,
					torque_1 : torque_1,
					torque_2 : torque_2,
					torque_3 : torque_3,
					average : average,
					judgement : judgement,
					notes:notes
				}
				$.post('{{ url("index/push_block_recorder/create_torque") }}', data2, function(result, status, xhr){
					if(result.status){
						$('#loading').hide();
						openSuccessGritter('Success', result.message);
						alert('Pengisian Selesai.');
						location.reload();
					}
					else{
						openErrorGritter('Error!', result.message);
					}
				});
			}
		}
	}

	function torque(id) {
		var batas_bawah_hm = '{{$batas_bawah_hm}}';
		var batas_atas_hm = '{{$batas_atas_hm}}';
		var batas_bawah_mf = '{{$batas_bawah_mf}}';
		var batas_atas_mf ='{{$batas_atas_mf}}';

		if (id.length > 10) {
			torques = id.substr(id.length - 2);
		}else{
			torques = id.substr(id.length - 1);
		}
		var tr1 = 'torque_1_'+torques;
		var tr2 = 'torque_2_'+torques;
		var tr3 = 'torque_3_'+torques;
		var tr1value = document.getElementById(tr1).value;
		var tr2value = document.getElementById(tr2).value;
		var tr3value = document.getElementById(tr3).value;
		if (tr1value == "") {
			var tr1value = 0;
		}
		if (tr2value == "") {
			var tr2value = 0;
		}
		if (tr3value == "") {
			var tr3value = 0;
		}
		var avg = (parseFloat(tr1value)+parseFloat(tr2value)+parseFloat(tr3value))/3;
		var avg_id = '#average_'+torques;
		var avg_id2 = 'average_'+torques;
		var judgement_id = '#judgement_'+torques;
		var judgement_id2 = 'judgement_'+torques;
		if ($('#check_type_fix2').text() == 'HJ-MJ') {
			if (parseFloat(avg) < parseFloat(batas_bawah_hm) || parseFloat(avg) > parseFloat(batas_atas_hm)) {
				document.getElementById(judgement_id2).style.backgroundColor = "#ff4f4f"; //red
				$(judgement_id).html('NG');
			}else{
				document.getElementById(judgement_id2).style.backgroundColor = "#7fff6e"; //green
				$(judgement_id).html('OK');
			}
		}else{
			if (parseFloat(avg) < parseFloat(batas_bawah_mf) || parseFloat(avg) > parseFloat(batas_atas_mf)) {
				document.getElementById(judgement_id2).style.backgroundColor = "#ff4f4f"; //red
				$(judgement_id).html('NG');
			}else{
				document.getElementById(judgement_id2).style.backgroundColor = "#7fff6e"; //green
				$(judgement_id).html('OK');
			}
		}
		$(avg_id).html(avg);
	}

	function itemresume(middle_id,head_foot_id){
		var data = {
			middle_id : middle_id,
			head_foot_id : head_foot_id,
		}
		$.get('{{ url("index/fetchResumeTorque") }}', data, function(result, status, xhr){
			$('#tableResume').DataTable().clear();
			$('#tableResume').DataTable().destroy();
			$('#tableBodyResume').html("");
			var tableData = "";
			if ($('#product_fix2').text() == "YRF-21K//ID" || $('#product_fix2').text() == "YRF-21//ID" || $('#product_fix2').text() == "YRF-21 (FSA)") {
				if(result.detail_head_foot.cavity_3 == null){
					indexHeadFoot = 2;
				}else if (result.detail_head_foot.cavity_5 == null) {
					indexHeadFoot = 4;
				}else{
					indexHeadFoot = 6;
				}
				var array_middle = Object.values(result.cav_middle);
				var array_head_foot = Object.values(result.cav_head_foot);
				var index = 1;
				for(var i=0;i<2;i++){
					for(var j=0;j<indexHeadFoot;j++){
						tableData += '<tr>';
						tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>'+index+'</b></td>';	
						tableData += '<td style="text-align:center;" id="middle_'+index+'">'+ array_middle[i] +'</td>';
						tableData += '<td style="text-align:center;" id="head_foot_'+index+'">'+ array_head_foot[j] +'</td>';
						tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="torque(this.id)" class="form-control" id="torque_1_'+index+'"></td>';
						tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="torque(this.id)" class="form-control" id="torque_2_'+index+'"></td>';
						tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="torque(this.id)" class="form-control" id="torque_3_'+index+'"></td>';
						tableData += '<td style="text-align:center;" id="average_'+index+'"></td>';
						tableData += '<td style="text-align:center;" id="judgement_'+index+'"></td>';
						tableData += '</tr>';
						index++;
					}
				}
			}else{
				if (result.detail_head_foot.cavity_5 == null) {
					indexHeadFoot = 4;
				}else{
					indexHeadFoot = 6;
				}
				var array_middle = Object.values(result.cav_middle);
				var array_head_foot = Object.values(result.cav_head_foot);
				var index = 1;
				for(var i=0;i<4;i++){
					for(var j=0;j<indexHeadFoot;j++){
						tableData += '<tr>';
						tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>'+index+'</b></td>';	
						tableData += '<td style="text-align:center;" id="middle_'+index+'">'+ array_middle[i] +'</td>';
						tableData += '<td style="text-align:center;" id="head_foot_'+index+'">'+ array_head_foot[j] +'</td>';
						tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="torque(this.id)" class="form-control" id="torque_1_'+index+'"></td>';
						tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="torque(this.id)" class="form-control" id="torque_2_'+index+'"></td>';
						tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="torque(this.id)" class="form-control" id="torque_3_'+index+'"></td>';
						tableData += '<td style="text-align:center;" id="average_'+index+'"></td>';
						tableData += '<td style="text-align:center;" id="judgement_'+index+'"></td>';
						tableData += '</tr>';
						index++;
					}
				}
			}
			$('#tableBodyResume').append(tableData);
		});
	}

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '2000'
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