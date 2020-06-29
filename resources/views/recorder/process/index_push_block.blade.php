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

	#tableblock> tbody > tr > td :hover {
		cursor: pointer;
		/*background-color: #e0e0e0;*/
	}

	#tableResume1 > tbody> tr > td,#tableResume1 > thead > tr > th {
		 border: 1px solid black;
	}
	#tableResume2 > tbody> tr > td,#tableResume2 > thead > tr > th {
		 border: 1px solid black;
	}
	#tableResume3 > tbody> tr > td,#tableResume3 > thead > tr > th {
		 border: 1px solid black;
	}
	#tableResume4 > tbody> tr > td,#tableResume4 > thead > tr > th {
		 border: 1px solid black;
	}
	#tableResume5 > tbody> tr > td,#tableResume5 > thead > tr > th {
		 border: 1px solid black;
	}
	#tableResume6 > tbody> tr > td,#tableResume6 > thead > tr > th {
		 border: 1px solid black;
	}
	#tableResume7 > tbody> tr > td,#tableResume7 > thead > tr > th {
		 border: 1px solid black;
	}
	#tableResume8 > tbody> tr > td,#tableResume8 > thead > tr > th {
		 border: 1px solid black;
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

	#loading { display: none; }

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
		{{ $title }}
		<small><span class="text-purple"> {{ $title_jp }}</span></small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<input type="hidden" id="data" value="data">
	<div class="row">
		<div class="col-xs-6">
			<div class="row">
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-12">
							<table class="table table-bordered">
								<tr>
									<td colspan="2" class="label-primary" style="font-weight: bold; text-align: center;font-size: 17px;">
										PIC
									</td>
									<td colspan="2" class="label-success" id="pic_check" style="font-weight: bold; text-align: center;font-size: 17px;">
										{{$name}}
									</td>
								</tr>
								<tr>
									<td class="label-primary" style="font-weight: bold; text-align: center;font-size: 17px;">
										Check Date
									</td>
									<td class="label-primary" id="check_date" style="font-weight: bold; text-align: center;font-size: 17px;">
										{{ date('Y-m-d H:i:s') }}
									</td>
									<td class="label-success" style="font-weight: bold; text-align: center;font-size: 17px;">
										Product
									</td>
									<td class="label-success" id="prod_type" style="font-weight: bold; text-align: center;font-size: 17px;">
									</td>
									<input type="hidden" id="push_block_id_gen">
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
									<td class="label-primary" style="font-weight: bold; text-align: center;font-size: 17px;">
										Tanggal Injeksi Head
									</td>
									<td class="label-success" id="injection_date_head_fix" style="font-weight: bold; text-align: center;font-size: 17px;">
									</td>
									<td class="label-primary" style="font-weight: bold; text-align: center;font-size: 17px;">
										Mesin Injeksi Head
									</td>
									<td class="label-success" id="mesin_head" style="font-weight: bold; text-align: center;font-size: 17px;">
									</td>
								</tr>
								<tr>
									<td class="label-primary" style="font-weight: bold; text-align: center;font-size: 17px;">
										Tanggal Injeksi Block
									</td>
									<td class="label-success" id="injection_date_block_fix" style="font-weight: bold; text-align: center;font-size: 17px;">
									</td>
									<td class="label-primary" style="font-weight: bold; text-align: center;font-size: 17px;">
										Mesin Injeksi Block
									</td>
									<td class="label-success" id="mesin_block" style="font-weight: bold; text-align: center;font-size: 17px;">
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row" style="padding-top:0px">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-body">
					<div class="col-xs-3" style="padding:0">
						<div class="col-xs-12">
							<span style="font-size: 20px; font-weight: bold;"><center></center></span>
						</div>
						<table class="table table-hover table-striped table-bordered" id="tableResume1">
							<thead>
								<tr>
									<th style="width: 1%;font-size: 12px;color:white;background-color:#605ca8"><center>No.</center></th>
									<th style="width: 2%;font-size: 12px;color:white;background-color:#605ca8"><center>H</center></th>
									<th style="width: 2%;font-size: 12px;color:white;background-color:#605ca8"><center>B</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Push Pull</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Jdgmnt</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Tinggi</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Jdgmnt</center></th>
								</tr>
							</thead>
							<tbody id="tableBodyResume1">
							</tbody>
						</table>
					</div>
					<div class="col-xs-3" style="padding:0">
						<div class="col-xs-12">
							<span style="font-size: 20px; font-weight: bold;"><center></center></span>
						</div>
						<table class="table table-hover table-striped table-bordered" id="tableResume2">
							<thead>
								<tr>
									<th style="width: 1%;font-size: 12px;color:white;background-color:#605ca8"><center>No.</center></th>
									<th style="width: 2%;font-size: 12px;color:white;background-color:#605ca8"><center>H</center></th>
									<th style="width: 2%;font-size: 12px;color:white;background-color:#605ca8"><center>B</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Push Pull</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Jdgmnt</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Tinggi</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Jdgmnt</center></th>
								</tr>
							</thead>
							<tbody id="tableBodyResume2">
							</tbody>
						</table>
					</div>
					<div class="col-xs-3" style="padding:0">
						<div class="col-xs-12">
							<span style="font-size: 20px; font-weight: bold;"><center></center></span>
						</div>
						<table class="table table-hover table-striped table-bordered" id="tableResume3">
							<thead>
								<tr>
									<th style="width: 1%;font-size: 12px;color:white;background-color:#605ca8"><center>No.</center></th>
									<th style="width: 2%;font-size: 12px;color:white;background-color:#605ca8"><center>H</center></th>
									<th style="width: 2%;font-size: 12px;color:white;background-color:#605ca8"><center>B</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Push Pull</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Jdgmnt</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Tinggi</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Jdgmnt</center></th>
								</tr>
							</thead>
							<tbody id="tableBodyResume3">
							</tbody>
						</table>
					</div>
					<div class="col-xs-3" style="padding:0">
						<div class="col-xs-12">
							<span style="font-size: 20px; font-weight: bold;"><center></center></span>
						</div>
						<table class="table table-hover table-striped table-bordered" id="tableResume4">
							<thead>
								<tr>
									<th style="width: 1%;font-size: 12px;color:white;background-color:#605ca8"><center>No.</center></th>
									<th style="width: 2%;font-size: 12px;color:white;background-color:#605ca8"><center>H</center></th>
									<th style="width: 2%;font-size: 12px;color:white;background-color:#605ca8"><center>B</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Push Pull</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Jdgmnt</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Tinggi</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Jdgmnt</center></th>
								</tr>
							</thead>
							<tbody id="tableBodyResume4">
							</tbody>
						</table>
					</div>
					<div class="col-xs-3" style="padding:0">
						<div class="col-xs-12">
							<span style="font-size: 20px; font-weight: bold;"><center></center></span>
						</div>
						<table class="table table-hover table-striped table-bordered" id="tableResume5">
							<thead>
								<tr>
									<th style="width: 1%;font-size: 12px;color:white;background-color:#605ca8"><center>No.</center></th>
									<th style="width: 2%;font-size: 12px;color:white;background-color:#605ca8"><center>H</center></th>
									<th style="width: 2%;font-size: 12px;color:white;background-color:#605ca8"><center>B</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Push Pull</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Jdgmnt</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Tinggi</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Jdgmnt</center></th>
								</tr>
							</thead>
							<tbody id="tableBodyResume5">
							</tbody>
						</table>
					</div>
					<div class="col-xs-3" style="padding:0">
						<div class="col-xs-12">
							<span style="font-size: 20px; font-weight: bold;"><center></center></span>
						</div>
						<table class="table table-hover table-striped table-bordered" id="tableResume6">
							<thead>
								<tr>
									<th style="width: 1%;font-size: 12px;color:white;background-color:#605ca8"><center>No.</center></th>
									<th style="width: 2%;font-size: 12px;color:white;background-color:#605ca8"><center>H</center></th>
									<th style="width: 2%;font-size: 12px;color:white;background-color:#605ca8"><center>B</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Push Pull</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Jdgmnt</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Tinggi</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Jdgmnt</center></th>
								</tr>
							</thead>
							<tbody id="tableBodyResume6">
							</tbody>
						</table>
					</div>
					<div class="col-xs-3" style="padding:0">
						<div class="col-xs-12">
							<span style="font-size: 20px; font-weight: bold;"><center></center></span>
						</div>
						<table class="table table-hover table-striped table-bordered" id="tableResume7">
							<thead>
								<tr>
									<th style="width: 1%;font-size: 12px;color:white;background-color:#605ca8"><center>No.</center></th>
									<th style="width: 2%;font-size: 12px;color:white;background-color:#605ca8"><center>H</center></th>
									<th style="width: 2%;font-size: 12px;color:white;background-color:#605ca8"><center>B</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Push Pull</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Jdgmnt</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Tinggi</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Jdgmnt</center></th>
								</tr>
							</thead>
							<tbody id="tableBodyResume7">
							</tbody>
						</table>
					</div>
					<div class="col-xs-3" style="padding:0">
						<div class="col-xs-12">
							<span style="font-size: 20px; font-weight: bold;"><center></center></span>
						</div>
						<table class="table table-hover table-striped table-bordered" id="tableResume8">
							<thead>
								<tr>
									<th style="width: 1%;font-size: 12px;color:white;background-color:#605ca8"><center>No.</center></th>
									<th style="width: 2%;font-size: 12px;color:white;background-color:#605ca8"><center>H</center></th>
									<th style="width: 2%;font-size: 12px;color:white;background-color:#605ca8"><center>B</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Push Pull</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Jdgmnt</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Tinggi</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Jdgmnt</center></th>
								</tr>
							</thead>
							<tbody id="tableBodyResume8">
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

	<div class="modal fade" id="modalHeadBlock">
		<div class="modal-dialog modal-lg" style="width: 1200px">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div class="col-xs-12">
							<div class="col-xs-6">
								<div class="row">
									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-12">
												<center><span style="font-weight: bold; font-size: 18px;">Tanggal Injeksi Head</span></center>
											</div>
										</div>
									</div>
									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-12">
												<input id="injection_date_head" style="font-size: 20px; height: 40px; text-align: center;" type="text" class="form-control" placeholder="Tanggal Injeksi Head">
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
												<center><span style="font-weight: bold; font-size: 18px;">Tanggal Injeksi Block</span></center>
											</div>
										</div>
									</div>
									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-12">
												<input id="injection_date_block" style="font-size: 20px; height: 40px; text-align: center;" type="text" class="form-control" placeholder="Tanggal Injeksi Block">
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xs-6" id="mesin_head_choice" style="padding-top: 20px">
								<div class="row">
									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-12">
												<center><span style="font-weight: bold; font-size: 18px;">Mesin Injeksi Head</span></center>
											</div>
										</div>
									</div>
									<div class="col-xs-12">
											@foreach($mesin as $mesin)
											<div class="col-xs-2" style="padding-top: 5px">
												<center><button class="btn btn-success" id="{{$mesin}}" style="width: 50px;font-size: 15px" onclick="getMesinHead(this.id)">
													{{$mesin}}
												</button></center>
											</div>
											@endforeach
									</div>
								</div>
							</div>
							<div class="col-xs-6" id="mesin_head_fix" style="padding-top: 20px">
								<div class="row">
									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-12">
												<center><span style="font-weight: bold; font-size: 18px;">Mesin Injeksi Head</span></center>
											</div>
										</div>
									</div>
									<div class="col-xs-12">
											<button class="btn btn-success" id="mesin_head_fix2" style="width: 100%;font-size: 20px;font-weight: bold;" onclick="changeMesinHead()">
												#0
											</button>
									</div>
								</div>
							</div>
							<div class="col-xs-6" id="mesin_block_choice" style="padding-top: 20px">
								<div class="row">
									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-12">
												<center><span style="font-weight: bold; font-size: 18px;">Mesin Injeksi Block</span></center>
											</div>
										</div>
									</div>
									<div class="col-xs-12">
											@foreach($mesin2 as $mesin2)
											<div class="col-xs-2" style="padding-top: 5px">
												<center><button class="btn btn-warning" id="{{$mesin2}}" style="width: 50px;font-size: 15px" onclick="getMesinBlock(this.id)">
													{{$mesin2}}
												</button></center>
											</div>
											@endforeach
									</div>
								</div>
							</div>
							<div class="col-xs-6" id="mesin_block_fix" style="padding-top: 20px">
								<div class="row">
									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-12">
												<center><span style="font-weight: bold; font-size: 18px;">Mesin Injeksi Block</span></center>
											</div>
										</div>
									</div>
									<div class="col-xs-12">
											<button class="btn btn-warning" id="mesin_block_fix2" style="width: 100%;font-size: 20px;font-weight: bold;" onclick="changeMesinBlock()">
												#0
											</button>
									</div>
								</div>
							</div>
							<div class="col-xs-12" id="product_choice" style="padding-top: 20px">
								<div class="row">
									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-12">
												<center><span style="font-weight: bold; font-size: 18px;">Type Produk</span></center>
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
												<center><span style="font-weight: bold; font-size: 18px;">Type Produk</span></center>
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
							<div class="col-xs-6" style="padding-top:10px">
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
											<td width="50%" onclick="getData(1)">
												<center>
													<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
														1-4
													</button>
												</center>
											</td>
											<td width="50%" onclick="getData(2)">
												<center>
													<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
														5-8
													</button>
												</center>
											</td>
										</tr>	
										<tr>
											<td width="50%" onclick="getData(3)">
												<center>
													<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
														9-12
													</button>
												</center>
											</td>
											<td width="50%" onclick="getData(4)">
												<center>
													<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
														13-16
													</button>
												</center>
											</td>
										</tr>
										<tr>
											<td width="50%" onclick="getData(5)">
												<center>
													<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
														17-20
													</button>
												</center>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="col-xs-6" style="padding-top:10px">
								<div class="col-xs-12">
									<span style="font-size: 20px; font-weight: bold;"><center>BLOCK</center></span>
								</div>
								<table class="table" id="tableblock">
									<thead>
										<tr>
											<th style="width: 1%;"></th>
										</tr>					
									</thead>
									<tbody>
										<tr>
											<td width="50%" onclick="getData2(6)">
												<center>
													<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
														1-8
													</button>
												</center>
											</td>
											<td width="50%" onclick="getData2(7)">
												<center>
													<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
														9-16
													</button>
												</center>
											</td>
										</tr>	
										<tr>
											<td width="50%" onclick="getData2(8)">
												<center>
													<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
														17-24
													</button>
												</center>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<div class="col-xs-6" style="padding-top: 0px">
							<div class="col-xs-12">
								<input type="hidden" id="head_id" style="width: 24%; height: 30px; font-size:20px; text-align: center;" disabled>
								<input type="hidden" id="head_value" style="width: 24%; height: 30px; font-size:20px; text-align: center;" disabled>
								<table class="table table-bordered">
									<tr>
										<td>
											<input type="text" id="head_1" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
										<td>
											<input type="text" id="head_2" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
										<td>
											<input type="text" id="head_3" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
										<td>
											<input type="text" id="head_4" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
									</tr>
								</table>
							</div>
						</div>
						<div class="col-xs-6">
							<div class="col-xs-12">
								<input type="hidden" id="block_id" style="width: 11%; height: 30px; font-size: 20px; text-align: center;" disabled>
								<input type="hidden" id="block_value" style="width: 30%; height: 30px; font-size: 20px; text-align: center;" disabled>
								<table class="table table-bordered">
									<tr>
										<td>
											<input type="text" id="block_1" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
										<td>
											<input type="text" id="block_2" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
										<td>
											<input type="text" id="block_3" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
										<td>
											<input type="text" id="block_4" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
										<td>
											<input type="text" id="block_5" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
										<td>
											<input type="text" id="block_6" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
										<td>
											<input type="text" id="block_7" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
										<td>
											<input type="text" id="block_8" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
									</tr>
								</table>
							</div>
						</div>
						<div class="col-xs-12" style="padding-top:10px" id="reason">
							<div class="col-xs-12">
								<span style="font-size: 20px; font-weight: bold;"><center>REASON</center></span>
							</div>
							<table class="table" id="tablereason" style="padding-top: 0px">
								<thead>
									<tr>
										<th style="width: 1%;"></th>
									</tr>					
								</thead>
								<tbody>
									<tr>
										<td width="20%" onclick="getDataReason('MOLD CHANGE')">
											<center>
												<button class="btn btn-danger" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
													MOLD CHANGE
												</button>
											</center>
										</td>
										<td width="20%" onclick="getDataReason('COLOR CHANGE')">
											<center>
												<button class="btn btn-danger" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
													COLOR CHANGE
												</button>
											</center>
										</td>
										<td width="20%" onclick="getDataReason('TROUBLESHOOTING')">
											<center>
												<button class="btn btn-danger" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
													TROUBLESHOOTING
												</button>
											</center>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="col-xs-12" style="padding-top:10px" id="reason_fix">
							<div class="col-xs-12">
								<span style="font-size: 20px; font-weight: bold;"><center>REASON</center></span>
							</div>
							<button class="btn btn-danger" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;" id="reason_fix2" onclick="getDataReason2()">
								REASON
							</button>
						</div>
						<div class="col-xs-12" style="padding-top:10px" id="molding">
							<div class="col-xs-12">
								<span style="font-size: 20px; font-weight: bold;"><center>MOLDING</center></span>
							</div>
							<table class="table" id="tablemolding" style="padding-top: 0px">
								<tbody>
									<tr>
										<td width="20%" onclick="getDataMolding('HJ 01')">
											<center>
												<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
													HJ 01
												</button>
											</center>
										</td>
										<td width="20%" onclick="getDataMolding('HJ 02')">
											<center>
												<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
													HJ 02
												</button>
											</center>
										</td>
										<td width="20%" onclick="getDataMolding('HJ 03')">
											<center>
												<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
													HJ 03
												</button>
											</center>
										</td>
										<td width="20%" onclick="getDataMolding('HJ 04')">
											<center>
												<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
													HJ 04
												</button>
											</center>
										</td>
										<td width="20%" onclick="getDataMolding('HJ 05')">
											<center>
												<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
													HJ 05
												</button>
											</center>
										</td>
									</tr>
									<tr>
										<td width="20%" onclick="getDataMolding('BL 01')">
											<center>
												<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
													BL 01
												</button>
											</center>
										</td>
										<td width="20%" onclick="getDataMolding('BL 02')">
											<center>
												<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
													BL 02
												</button>
											</center>
										</td>
										<td width="20%" onclick="getDataMolding('BL 03')">
											<center>
												<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
													BL 03
												</button>
											</center>
										</td>
										<td width="20%" onclick="getDataMolding('BL 04')">
											<center>
												<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;">
													BL 04
												</button>
											</center>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="col-xs-12" style="padding-top:10px" id="molding_fix">
							<div class="col-xs-12">
								<span style="font-size: 20px; font-weight: bold;"><center>MOLDING</center></span>
							</div>
							<button class="btn btn-info" style="width: 100%;height: 40px;font-size: 1.5vw;font-weight: bold;" id="molding_fix2" onclick="getDataMolding2()">
								HJ
							</button>
						</div>
						<div class="col-xs-12" id="mesin_parameter" style="padding-top: 20px">
							<div class="row">
								<div class="col-xs-12">
									<div class="row">
										<div class="col-xs-12">
											<center><span style="font-weight: bold; font-size: 18px;">MESIN PARAMETER</span></center>
										</div>
									</div>
								</div>
								<div class="col-xs-12">
										@foreach($mesin3 as $mesin3)
										<div class="col-xs-2" style="padding-top: 5px">
											<center><button class="btn btn-primary" id="{{$mesin3}}" style="width: 120px;font-size: 15px" onclick="getMesinParameter(this.id)">
												{{$mesin3}}
											</button></center>
										</div>
										@endforeach
								</div>
							</div>
						</div>
						<div class="col-xs-12" id="mesin_parameter_fix" style="padding-top: 20px">
							<div class="row">
								<div class="col-xs-12">
									<div class="row">
										<div class="col-xs-12">
											<center><span style="font-weight: bold; font-size: 18px;">MESIN PARAMETER</span></center>
										</div>
									</div>
								</div>
								<div class="col-xs-12">
										<button class="btn btn-primary" id="mesin_parameter_fix2" style="width: 100%;font-size: 20px;font-weight: bold;" onclick="getMesinParameter2()">
											#0
										</button>
								</div>
							</div>
						</div>
						<div class="col-xs-12" style="padding-top: 20px;overflow-x: scroll;" id="lastParameter">
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
								</tbody>
							</table>
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
	$('#injection_date_head').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
      todayHighlight: true
    });

    $('#injection_date_block').datepicker({
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
		$('#modalHeadBlock').modal({
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
		$('#mesin_head_fix').hide();
		$('#mesin_block_fix').hide();
		$('#molding_fix').hide();
		$('#reason_fix').hide();
		$('#mesin_parameter_fix').hide();

		var remark = '{{ $remark }}';

		if (remark == 'After Injection') {
			$('#molding').hide();
			$('#reason').hide();
			$('#mesin_parameter').hide();
			$('#lastParameter').hide();
		}
	});

	// jQuery.extend(jQuery.expr[':'], {
	//     focusable: function (el, index, selector) {
	//         return $(el).is('a, button, :input, [tabindex]');
	//     }
	// });

	$('#modalHeadBlock').on('shown.bs.modal', function () {
			$('#injection_date_head').focus();
	});

	// $(document).on('keypress', 'input,select', function (e) {
	//     if (e.which == 13) {
	//         e.preventDefault();
	//         // Get all focusable elements on the page
	//         var $canfocus = $(':focusable');
	//         var index = $canfocus.index(document.activeElement) + 1;
	//         if (index >= $canfocus.length) index = 0;
	//         $canfocus.eq(index).focus();
	//     }
	// });

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

	function getMesinHead(mesin) {
		$('#mesin_head_choice').hide();
		$('#mesin_head_fix').show();
		$('#mesin_head_fix2').html(mesin);
	}

	function changeMesinHead() {
		$('#mesin_head_choice').show();
		$('#mesin_head_fix').hide();
		$('#mesin_head_fix2').html('#0');
	}

	function getMesinBlock(mesin) {
		$('#mesin_block_choice').hide();
		$('#mesin_block_fix').show();
		$('#mesin_block_fix2').html(mesin);
	}

	function changeMesinBlock() {
		$('#mesin_block_choice').show();
		$('#mesin_block_fix').hide();
		$('#mesin_block_fix2').html('#0');
	}

	function getDataMolding(mold) {
		$('#molding_fix').show();
		$('#molding').hide();
		$('#molding_fix2').html(mold);
	}

	function getDataMolding2(mold) {
		$('#molding_fix').hide();
		$('#molding').show();
		$('#molding_fix2').html('HJ');
	}

	function getDataReason(reason) {
		$('#reason_fix').show();
		$('#reason').hide();
		$('#reason_fix2').html(reason);
	}

	function getDataReason2() {
		$('#reason_fix').hide();
		$('#reason').show();
		$('#reason_fix2').html('REASON');
	}

	function getMesinParameter(mesin_parameter) {
		$('#mesin_parameter_fix').show();
		$('#mesin_parameter').hide();
		$('#mesin_parameter_fix2').html(mesin_parameter);

		data = {
			mesin : mesin_parameter,
			remark: '{{$remark}}'
		}

		$.get('{{ url("index/fetch_mesin_parameter") }}', data, function(result, status, xhr){
			if(result.status){
				var tableData = "";
				$('#bodyLastParameter').append().empty();
				if (result.detail == null) {
					tableData += '<td id="tabledesign" colspan="46">Data Belum Ada.<br><a class="btn btn-primary" target="_blank" href="{{ url("index/machine_parameter") }}">Isi Data Di Sini</a></td>';
					tableData += '<td hidden><input type="hidden" id="nh" value="Tidak Ada Data"></td>';
				}else{
					tableData += '<tr>';
					tableData += '<td id="tabledesign">Last</td>';
					tableData += '<td id="tabledesign">'+result.detail.nh+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.h1+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.h2+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.h3+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.dryer+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.mtc_temp+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.mtc_press+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.chiller_temp+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.chiller_press+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.clamp+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.ph4+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.ph3+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.ph2+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.ph1+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.trh3+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.trh2+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.trh1+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.vh+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.pi+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.ls10+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.vi5+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.vi4+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.vi3+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.vi2+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.vi1+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.ls4+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.ls4d+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.ls4c+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.ls4b+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.ls4a+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.ls5+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.ve1+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.ve2+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.vr+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.ls31a+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.ls31+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.srn+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.rpm+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.bp+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.tr1inj+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.tr3cool+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.tr4int+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.mincush+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.fill+'</td>';
					tableData += '<td id="tabledesign">'+result.detail.circletime+'</td>';
					tableData += '</tr>';

					tableData += '<tr hidden>';
					tableData += '<td><input type="hidden" id="nh" value="'+result.detail.nh+'">';
					// tableData += '<input type="hidden" id="h1" value="'+result.detail.h1+'">';
					// tableData += '<input type="hidden" id="h2" value="'+result.detail.h2+'">';
					// tableData += '<input type="hidden" id="h3" value="'+result.detail.h3+'">';
					// tableData += '<input type="hidden" id="dryer" value="'+result.detail.dryer+'">';
					// tableData += '<input type="hidden" id="mtc_temp" value="'+result.detail.mtc_temp+'">';
					// tableData += '<input type="hidden" id="mtc_press" value="'+result.detail.mtc_press+'">';
					// tableData += '<input type="hidden" id="chiller_temp" value="'+result.detail.chiller_temp+'">';
					// tableData += '<input type="hidden" id="chiller_press" value="'+result.detail.chiller_press+'">';
					// tableData += '<input type="hidden" id="clamp" value="'+result.detail.clamp+'">';
					// tableData += '<input type="hidden" id="ph4" value="'+result.detail.ph4+'">';
					// tableData += '<input type="hidden" id="ph3" value="'+result.detail.ph3+'">';
					// tableData += '<input type="hidden" id="ph2" value="'+result.detail.ph2+'">';
					// tableData += '<input type="hidden" id="ph1" value="'+result.detail.ph1+'">';
					// tableData += '<input type="hidden" id="trh3" value="'+result.detail.trh3+'">';
					// tableData += '<input type="hidden" id="trh2" value="'+result.detail.trh2+'">';
					// tableData += '<input type="hidden" id="trh1" value="'+result.detail.trh1+'">';
					// tableData += '<input type="hidden" id="vh" value="'+result.detail.vh+'">';
					// tableData += '<input type="hidden" id="pi" value="'+result.detail.pi+'">';
					// tableData += '<input type="hidden" id="ls10" value="'+result.detail.ls10+'">';
					// tableData += '<input type="hidden" id="vi5" value="'+result.detail.vi5+'">';
					// tableData += '<input type="hidden" id="vi4" value="'+result.detail.vi4+'">';
					// tableData += '<input type="hidden" id="vi3" value="'+result.detail.vi3+'">';
					// tableData += '<input type="hidden" id="vi2" value="'+result.detail.vi2+'">';
					// tableData += '<input type="hidden" id="vi1" value="'+result.detail.vi1+'">';
					// tableData += '<input type="hidden" id="ls4" value="'+result.detail.ls4+'">';
					// tableData += '<input type="hidden" id="ls4d" value="'+result.detail.ls4d+'">';
					// tableData += '<input type="hidden" id="ls4c" value="'+result.detail.ls4c+'">';
					// tableData += '<input type="hidden" id="ls4b" value="'+result.detail.ls4b+'">';
					// tableData += '<input type="hidden" id="ls4a" value="'+result.detail.ls4a+'">';
					// tableData += '<input type="hidden" id="ls5" value="'+result.detail.ls5+'">';
					// tableData += '<input type="hidden" id="ve1" value="'+result.detail.ve1+'">';
					// tableData += '<input type="hidden" id="ve2" value="'+result.detail.ve2+'">';
					// tableData += '<input type="hidden" id="vr" value="'+result.detail.vr+'">';
					// tableData += '<input type="hidden" id="ls31a" value="'+result.detail.ls31a+'">';
					// tableData += '<input type="hidden" id="ls31" value="'+result.detail.ls31+'">';
					// tableData += '<input type="hidden" id="srn" value="'+result.detail.srn+'">';
					// tableData += '<input type="hidden" id="rpm" value="'+result.detail.rpm+'">';
					// tableData += '<input type="hidden" id="bp" value="'+result.detail.bp+'">';
					// tableData += '<input type="hidden" id="tr1inj" value="'+result.detail.tr1inj+'">';
					// tableData += '<input type="hidden" id="tr3cool" value="'+result.detail.tr3cool+'">';
					// tableData += '<input type="hidden" id="tr4int" value="'+result.detail.tr4int+'">';
					// tableData += '<input type="hidden" id="mincush" value="'+result.detail.mincush+'">';
					// tableData += '<input type="hidden" id="fill" value="'+result.detail.fill+'">';
					// tableData += '<input type="hidden" id="circletime" value="'+result.detail.circletime+'">';
					tableData += '</td>';
					tableData += '</tr>';

					tableData += '<tr>';
					tableData += '<td id="tabledesign">New</td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_nh" class="form-control" value="'+result.detail.nh+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_h1" class="form-control" value="'+result.detail.h1+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_h2" class="form-control" value="'+result.detail.h2+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_h3" class="form-control" value="'+result.detail.h3+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_dryer" class="form-control" value="'+result.detail.dryer+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_mtc_temp" class="form-control" value="'+result.detail.mtc_temp+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_mtc_press" class="form-control" value="'+result.detail.mtc_press+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_chiller_temp" class="form-control" value="'+result.detail.chiller_temp+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_chiller_press" class="form-control" value="'+result.detail.chiller_press+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_clamp" class="form-control" value="'+result.detail.clamp+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph4" class="form-control" value="'+result.detail.ph4+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph3" class="form-control" value="'+result.detail.ph3+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph2" class="form-control" value="'+result.detail.ph2+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ph1" class="form-control" value="'+result.detail.ph1+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_trh3" class="form-control" value="'+result.detail.trh3+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_trh2" class="form-control" value="'+result.detail.trh2+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_trh1" class="form-control" value="'+result.detail.trh1+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vh" class="form-control" value="'+result.detail.vh+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_pi" class="form-control" value="'+result.detail.pi+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls10" class="form-control" value="'+result.detail.ls10+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi5" class="form-control" value="'+result.detail.vi5+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi4" class="form-control" value="'+result.detail.vi4+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi3" class="form-control" value="'+result.detail.vi3+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi2" class="form-control" value="'+result.detail.vi2+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vi1" class="form-control" value="'+result.detail.vi1+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4" class="form-control" value="'+result.detail.ls4+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4d" class="form-control" value="'+result.detail.ls4d+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4c" class="form-control" value="'+result.detail.ls4c+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4b" class="form-control" value="'+result.detail.ls4b+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls4a" class="form-control" value="'+result.detail.ls4a+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls5" class="form-control" value="'+result.detail.ls5+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ve1" class="form-control" value="'+result.detail.ve1+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ve2" class="form-control" value="'+result.detail.ve2+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_vr" class="form-control" value="'+result.detail.vr+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls31a" class="form-control" value="'+result.detail.ls31a+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_ls31" class="form-control" value="'+result.detail.ls31+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_srn" class="form-control" value="'+result.detail.srn+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_rpm" class="form-control" value="'+result.detail.rpm+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_bp" class="form-control" value="'+result.detail.bp+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_tr1inj" class="form-control" value="'+result.detail.tr1inj+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_tr3cool" class="form-control" value="'+result.detail.tr3cool+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_tr4int" class="form-control" value="'+result.detail.tr4int+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_mincush" class="form-control" value="'+result.detail.mincush+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_fill" class="form-control" value="'+result.detail.fill+'"></td>';
					tableData += '<td id="tabledesign" style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;padding-left: 0px;padding-right: 0px" id="input_circletime" class="form-control" value="'+result.detail.circletime+'"></td>';
					tableData += '</tr>';
				}
				$('#bodyLastParameter').append(tableData);
			}
			else{
				openErrorGritter('Error!', 'Data Parameter Belum Ada.');
			}
		});
	}

	function getMesinParameter2() {
		$('#bodyLastParameter').append().empty();
		$('#mesin_parameter_fix').hide();
		$('#mesin_parameter').show();
		$('#mesin_parameter_fix2').html('#0');
	}

	function getData(no_cavity){
		var data = {
			no_cavity : no_cavity,
			type : 'head',
		}

		if (no_cavity == 1) {
			$('#head_value').val('1-4');
		}else if (no_cavity == 2) {
			$('#head_value').val('5-8');
		}else if (no_cavity == 3) {
			$('#head_value').val('9-12');
		}else if (no_cavity == 4) {
			$('#head_value').val('13-16');
		}else if (no_cavity == 5) {
			$('#head_value').val('17-20');
		}

		$.get('{{ url("index/fetch_push_block") }}', data, function(result, status, xhr){
			if(result.status){
				$('#head_id').val(result.id);
				$('#head_1').val(result.cavity_1);
				$('#head_2').val(result.cavity_2);
				$('#head_3').val(result.cavity_3);
				$('#head_4').val(result.cavity_4);
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function getData2(no_cavity){
		var data = {
			no_cavity : no_cavity,
			type : 'head',
		}

		if (no_cavity == 6) {
			$('#block_value').val('1-8');
		}else if (no_cavity == 7) {
			$('#block_value').val('9-16');
		}else if (no_cavity == 8) {
			$('#block_value').val('17-24');
		}

		$.get('{{ url("index/fetch_push_block") }}', data, function(result, status, xhr){
			if(result.status){
				$('#block_id').val(result.id);
				$('#block_1').val(result.cavity_1);
				$('#block_2').val(result.cavity_2);
				$('#block_3').val(result.cavity_3);
				$('#block_4').val(result.cavity_4);
				$('#block_5').val(result.cavity_5);
				$('#block_6').val(result.cavity_6);
				$('#block_7').val(result.cavity_7);
				$('#block_8').val(result.cavity_8);
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function create_parameter() {
		var data = {
			pic_check : $("#pic_check").text().trim(),
			push_block_code : '{{$remark}}',
			push_block_id_gen : $('#push_block_id_gen').val(),
			reason : $('#reason_fix2').text().trim(),
			check_date : $("#check_date").text().trim(),
			product_type : $("#product_fix2").text().trim(),
			mesin : $("#mesin_parameter_fix2").text().trim(),
			molding : $("#molding_fix2").text().trim(),
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

		// console.log(data);

		$.post('{{ url("index/push_block_recorder/create_parameter") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success', result.message);
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function confirm() {
		if ('{{$remark}}' == 'First Shot Approval') {
			if($('#injection_date_head').val() == '' || $('#injection_date_block').val() == '' || $('#head_id').val() == '' || $('#block_id').val() == '' || $('#mesin_head_fix2').text().trim() == '#0' || $('#mesin_block_fix2').text().trim() == '#0' || $('#product_fix2').text().trim() == 'YRS' || $('#molding_fix2').text().trim() == 'HJ' || $('#reason_fix2').text().trim() == 'REASON' || $('#mesin_parameter_fix2').text().trim() == '#0' || $('#nh').val() == 'Tidak Ada Data'){
				alert('Semua Data Harus Diisi.');
			}else{
				$('#prod_type').html($('#product_fix2').text());
				$('#injection_date_head_fix').html($('#injection_date_head').val());
				$('#injection_date_block_fix').html($('#injection_date_block').val());
				$('#mesin_head').html($('#mesin_head_fix2').text());
				$('#mesin_block').html($('#mesin_block_fix2').text());
				$('#modalHeadBlock').modal('hide');
				itemresume1($("#head_id").val(),$("#block_1").val());
				itemresume2($("#head_id").val(),$("#block_2").val());
				itemresume3($("#head_id").val(),$("#block_3").val());
				itemresume4($("#head_id").val(),$("#block_4").val());
				itemresume5($("#head_id").val(),$("#block_5").val());
				itemresume6($("#head_id").val(),$("#block_6").val());
				itemresume7($("#head_id").val(),$("#block_7").val());
				itemresume8($("#head_id").val(),$("#block_8").val());
				get_temp();
				setInterval(update_temp,60000);
			}
		}else{
			if($('#injection_date_head').val() == '' || $('#injection_date_block').val() == '' || $('#head_id').val() == '' || $('#block_id').val() == '' || $('#mesin_head_fix2').text() == '#0' || $('#mesin_block_fix2').text() == '#0' || $('#product_fix2').text() == 'YRS'){
				alert('Semua Data Harus Diisi.');
			}else{
				$('#prod_type').html($('#product_fix2').text());
				$('#injection_date_head_fix').html($('#injection_date_head').val());
				$('#injection_date_block_fix').html($('#injection_date_block').val());
				$('#mesin_head').html($('#mesin_head_fix2').text());
				$('#mesin_block').html($('#mesin_block_fix2').text());
				$('#modalHeadBlock').modal('hide');
				itemresume1($("#head_id").val(),$("#block_1").val());
				itemresume2($("#head_id").val(),$("#block_2").val());
				itemresume3($("#head_id").val(),$("#block_3").val());
				itemresume4($("#head_id").val(),$("#block_4").val());
				itemresume5($("#head_id").val(),$("#block_5").val());
				itemresume6($("#head_id").val(),$("#block_6").val());
				itemresume7($("#head_id").val(),$("#block_7").val());
				itemresume8($("#head_id").val(),$("#block_8").val());
				get_temp();
				setInterval(update_temp,60000);
			}
		}
	}

	function reset(){
		window.location = "{{ url('index/recorder_process_push_block/'.$remark) }}";
	}

	function create_temp() {
		var push_block_code = '{{ $remark }}';
		if (push_block_code == 'After Injection') {
			var front = 'AI';
		}else{
			var front = 'FSA';
		}
		var check_date = $("#check_date").text();
		var injection_date_head = $("#injection_date_head_fix").text();
		var injection_date_block = $("#injection_date_block_fix").text();
		var mesin_head = $("#mesin_head").text();
		var mesin_block = $("#mesin_block").text();
		var product_type = $("#prod_type").text();
		var pic_check = $("#pic_check").text();

		var array_head = [];
		var array_block = [];
		var array_head2 = [];
		var array_block2 = [];

		for(var i = 1; i <= 4; i++){
			for(var j = 1; j <= 4; j++){
				array_head.push($("#head_"+[j]).val());
				array_block.push($("#block_"+[i]).val());
			}
		}
		for(var k = 5; k <= 8; k++){
			for(var l = 1; l <= 4; l++){
				array_head2.push($("#head_"+[l]).val());
				array_block2.push($("#block_"+[k]).val());
			}
		}

		var data = {
			push_block_code : push_block_code,
			check_date : check_date,
			injection_date_head : injection_date_head,
			injection_date_block : injection_date_block,
			mesin_head : mesin_head,
			mesin_block : mesin_block,
			pic_check : pic_check,
			product_type : product_type,
			head : array_head,
			block : array_block
		}
		// console.table(data);
		$.post('{{ url("index/push_block_recorder/create_temp") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success', result.message);
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
		
		var data2 = {
			push_block_code : push_block_code,
			check_date : check_date,
			injection_date_head : injection_date_head,
			injection_date_block : injection_date_block,
			mesin_head : mesin_head,
			mesin_block : mesin_block,
			pic_check : pic_check,
			product_type : product_type,
			head : array_head2,
			block : array_block2
		}
		$.post('{{ url("index/push_block_recorder/create_temp") }}', data2, function(result, status, xhr){
			if(result.status){
				$('#push_block_id_gen').val(result.push_block_id_gen);
				openSuccessGritter('Success', result.message);
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function get_temp() {
		var array_head = [];
		var array_block = [];

		for(var i = 1; i <= 8; i++){
			array_block.push($("#block_"+[i]).val());
		}
		for(var j = 1; j <= 4; j++){
			array_head.push($("#head_"+[j]).val());
		}

		var remark = '{{$remark}}';
		
		var data = {
			array_head : array_head,
			array_block : array_block,
			remark : '{{$remark}}',
			product_type:$("#prod_type").text()
		}

		$.get('{{ url("index/push_block_recorder/get_temp") }}',data,  function(result, status, xhr){
			if(result.status){
				if(result.datas.length != 0){
					$.each(result.datas, function(key, value) {
						$("#push_pull_"+value[0].head+"_"+value[0].block).val(value[0].push_pull);
						$("#ketinggian_"+value[0].head+"_"+value[0].block).val(value[0].ketinggian);
						$("#judgement_"+value[0].head+"_"+value[0].block).html(value[0].judgement);
						$("#judgement2_"+value[0].head+"_"+value[0].block).html(value[0].judgement2);
						if (value[0].judgement == 'NG') {
							document.getElementById("judgement_"+value[0].head+"_"+value[0].block).style.backgroundColor = "#ff4f4f";
						}else if(value[0].judgement == 'OK'){
							document.getElementById("judgement_"+value[0].head+"_"+value[0].block).style.backgroundColor = "#7fff6e";
						}
						if (value[0].judgement2 == 'NG') {
							document.getElementById("judgement2_"+value[0].head+"_"+value[0].block).style.backgroundColor = "#ff4f4f";
						}else if(value[0].judgement2 == 'OK'){
							document.getElementById("judgement2_"+value[0].head+"_"+value[0].block).style.backgroundColor = "#7fff6e";
						}
						$("#prod_type").html(value[0].product_type);
						$("#check_date").html(value[0].check_date);
						$('#injection_date_head_fix').html(value[0].injection_date_head);
						$('#injection_date_block_fix').html(value[0].injection_date_block);
						$('#mesin_head').html(value[0].mesin_head);
						$('#push_block_id_gen').val(value[0].push_block_id_gen);
						$('#mesin_block').html(value[0].mesin_block);
						$("#notes").html(value[0].notes);
					});
					openSuccessGritter('Success!', result.message);
				}else{
					create_temp();
					if (remark == 'First Shot Approval') {
						create_parameter();
					}
				}
			}
			else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
				
			}
		});
	}

	function update_temp(){
		var head_id =  $("#head_id").val();
		var block_id =  $("#block_id").val();

		var notes =  $("#notes").val();

		var head_value =  $("#head_value").val();
		var block_value =  $("#block_value").val();

		var check_date = $("#check_date").text();
		var injection_date_head = $("#injection_date_head_fix").text();
		var injection_date_block = $("#injection_date_block_fix").text();
		var mesin_head = $("#mesin_head").text();
		var mesin_block = $("#mesin_block").text();
		var product_type = $("#prod_type").text();
		var pic_check = $("#pic_check").text();

		var array_head = [];
		var array_block = [];
		var array_head2 = [];
		var array_block2 = [];

		var push_pull = [];
		var judgement = [];
		var push_pull2 = [];
		var judgement2 = [];

		var ketinggian = [];
		var judgementketinggian = [];
		var ketinggian2 = [];
		var judgementketinggian2 = [];

		var push_block_code = '{{ $remark }}';

		for(var i = 1; i <= 4; i++){
			for(var j = 1; j <= 4; j++){
				array_head.push($("#head_"+[j]).val());
				array_block.push($("#block_"+[i]).val());
				push_pull.push($("#push_pull_"+$("#head_"+[j]).val()+"_"+$("#block_"+[i]).val()).val());
				judgement.push($("#judgement_"+$("#head_"+[j]).val()+"_"+$("#block_"+[i]).val()).text());
				ketinggian.push($("#ketinggian_"+$("#head_"+[j]).val()+"_"+$("#block_"+[i]).val()).val());
				judgementketinggian.push($("#judgement2_"+$("#head_"+[j]).val()+"_"+$("#block_"+[i]).val()).text());
			}
		}
		for(var k = 5; k <= 8; k++){
			for(var l = 1; l <= 4; l++){
				array_head2.push($("#head_"+[l]).val());
				array_block2.push($("#block_"+[k]).val());
				push_pull2.push($("#push_pull_"+$("#head_"+[l]).val()+"_"+$("#block_"+[k]).val()).val());
				judgement2.push($("#judgement_"+$("#head_"+[l]).val()+"_"+$("#block_"+[k]).val()).text());
				ketinggian2.push($("#ketinggian_"+$("#head_"+[l]).val()+"_"+$("#block_"+[k]).val()).val());
				judgementketinggian2.push($("#judgement2_"+$("#head_"+[l]).val()+"_"+$("#block_"+[k]).val()).text());
			}
		}

			var data = {
				push_block_code : push_block_code,
				check_date : check_date,
				injection_date_head : injection_date_head,
				injection_date_block : injection_date_block,
				mesin_head : mesin_head,
				mesin_block : mesin_block,
				pic_check : pic_check,
				product_type : product_type,
				head : array_head,
				block : array_block,
				push_pull : push_pull,
				judgement : judgement,
				ketinggian : ketinggian,
				judgementketinggian : judgementketinggian,
				notes : notes
			}
			$.post('{{ url("index/push_block_recorder/update_temp") }}', data, function(result, status, xhr){
				if(result.status){
					// openSuccessGritter('Success', result.message);
				}
				else{
					// openErrorGritter('Error!', result.message);
				}
			});
			var data2 = {
				push_block_code : push_block_code,
				check_date : check_date,
				injection_date_head : injection_date_head,
				injection_date_block : injection_date_block,
				mesin_head : mesin_head,
				mesin_block : mesin_block,
				pic_check : pic_check,
				product_type : product_type,
				head : array_head2,
				block : array_block2,
				push_pull : push_pull2,
				judgement : judgement2,
				ketinggian : ketinggian2,
				judgementketinggian : judgementketinggian2,
				notes : notes
			}
			$.post('{{ url("index/push_block_recorder/update_temp") }}', data2, function(result, status, xhr){
				if(result.status){
					// openSuccessGritter('Success', result.message);
				}
				else{
					// openErrorGritter('Error!', result.message);
				}
			});
	}

	function konfirmasi(){
		var head_id =  $("#head_id").val();
		var block_id =  $("#block_id").val();

		var notes =  $("#notes").val();

		var push_block_id_gen =  $("#push_block_id_gen").val();

		var head_value =  $("#head_value").val();
		var block_value =  $("#block_value").val();

		var check_date = $("#check_date").text();
		var injection_date_head = $("#injection_date_head_fix").text();
		var injection_date_block = $("#injection_date_block_fix").text();
		var mesin_head = $("#mesin_head").text();
		var mesin_block = $("#mesin_block").text();
		var product_type = $("#prod_type").text();
		var pic_check = $("#pic_check").text();

		var array_head = [];
		var array_block = [];
		var array_head2 = [];
		var array_block2 = [];

		var push_pull = [];
		var judgement = [];
		var push_pull2 = [];
		var judgement2 = [];

		var ketinggian = [];
		var judgementketinggian = [];
		var ketinggian2 = [];
		var judgementketinggian2 = [];

		var status_false = 0;

		var push_pull_ng_name = [];
		var height_ng_name = [];
		var push_pull_ng_value = [];
		var height_ng_value = [];

		var push_block_code = '{{ $remark }}';

		for(var i = 1; i <= 4; i++){
			for(var j = 1; j <= 4; j++){
				array_head.push($("#head_"+[j]).val());
				array_block.push($("#block_"+[i]).val());
				push_pull.push($("#push_pull_"+$("#head_"+[j]).val()+"_"+$("#block_"+[i]).val()).val());
				judgement.push($("#judgement_"+$("#head_"+[j]).val()+"_"+$("#block_"+[i]).val()).text());
				ketinggian.push($("#ketinggian_"+$("#head_"+[j]).val()+"_"+$("#block_"+[i]).val()).val());
				judgementketinggian.push($("#judgement2_"+$("#head_"+[j]).val()+"_"+$("#block_"+[i]).val()).text());
				if($("#push_pull_"+$("#head_"+[j]).val()+"_"+$("#block_"+[i]).val()).val() == '' || $("#ketinggian_"+$("#head_"+[j]).val()+"_"+$("#block_"+[i]).val()).val() == ''){
					status_false++;
				}

				if ($("#judgement_"+$("#head_"+[j]).val()+"_"+$("#block_"+[i]).val()).text() == 'NG') {
					push_pull_ng_name.push($("#head_"+[j]).val()+"-"+$("#block_"+[i]).val());
					push_pull_ng_value.push($("#push_pull_"+$("#head_"+[j]).val()+"_"+$("#block_"+[i]).val()).val());
				}
				if ($("#judgement2_"+$("#head_"+[j]).val()+"_"+$("#block_"+[i]).val()).text() == 'NG') {
					height_ng_name.push($("#head_"+[j]).val()+"-"+$("#block_"+[i]).val());
					height_ng_value.push($("#ketinggian_"+$("#head_"+[j]).val()+"_"+$("#block_"+[i]).val()).val());
				}
			}
		}
		for(var k = 5; k <= 8; k++){
			for(var l = 1; l <= 4; l++){
				array_head2.push($("#head_"+[l]).val());
				array_block2.push($("#block_"+[k]).val());
				push_pull2.push($("#push_pull_"+$("#head_"+[l]).val()+"_"+$("#block_"+[k]).val()).val());
				judgement2.push($("#judgement_"+$("#head_"+[l]).val()+"_"+$("#block_"+[k]).val()).text());
				ketinggian2.push($("#ketinggian_"+$("#head_"+[l]).val()+"_"+$("#block_"+[k]).val()).val());
				judgementketinggian2.push($("#judgement2_"+$("#head_"+[l]).val()+"_"+$("#block_"+[k]).val()).text());
				if($("#push_pull_"+$("#head_"+[l]).val()+"_"+$("#block_"+[k]).val()).val() == '' || $("#ketinggian_"+$("#head_"+[l]).val()+"_"+$("#block_"+[k]).val()).val() == ''){
					status_false++;
				}

				if ($("#judgement_"+$("#head_"+[l]).val()+"_"+$("#block_"+[k]).val()).text() == 'NG') {
					push_pull_ng_name.push($("#head_"+[l]).val()+"-"+$("#block_"+[k]).val());
					push_pull_ng_value.push($("#push_pull_"+$("#head_"+[l]).val()+"_"+$("#block_"+[k]).val()).val());
				}

				if ($("#judgement2_"+$("#head_"+[l]).val()+"_"+$("#block_"+[k]).val()).text() == 'NG') {
					height_ng_name.push($("#head_"+[l]).val()+"-"+$("#block_"+[k]).val());
					height_ng_value.push($("#ketinggian_"+$("#head_"+[l]).val()+"_"+$("#block_"+[k]).val()).val());
				}
			}
		}
		if(status_false > 0){
			alert('Semua Data Harus Diisi');
		}
		else{
			$('#selesai_button').prop('disabled', true);
			if (push_pull_ng_name.join() == '') {
				push_pull_ng_name.push('OK');
			}
			else{
				push_pull_ng_name.join();
			}

			if (push_pull_ng_value.join() == '') {
				push_pull_ng_value.push('OK');
			}
			else{
				push_pull_ng_value.join();
			}

			if (height_ng_name.join() == '') {
				height_ng_name.push('OK');
			}
			else{
				height_ng_name.join();
			}

			if (height_ng_value.join() == '') {
				height_ng_value.push('OK');
			}
			else{
				height_ng_value.join();
			}
			// console.log(push_pull_ng_name.join());
			// console.log(push_pull_ng_value.join());
			// console.log(height_ng_name.join());
			// console.log(height_ng_value.join());

			var data3 = {
				remark : push_block_code,
				push_block_id_gen : push_block_id_gen,
				check_date : check_date,
				injection_date_head : injection_date_head,
				injection_date_block : injection_date_block,
				mesin_head : mesin_head,
				mesin_block : mesin_block,
				pic_check : pic_check,
				product_type : product_type,
				head : head_value,
				block : block_value,
				push_pull_ng_name : push_pull_ng_name.join(),
				push_pull_ng_value : push_pull_ng_value.join(),
				height_ng_name : height_ng_name.join(),
				height_ng_value : height_ng_value.join(),
				push_pull_ng_name2 : push_pull_ng_name,
				push_pull_ng_value2 : push_pull_ng_value,
				height_ng_name2 : height_ng_name,
				height_ng_value2 : height_ng_value,
				notes : notes
			}
			// console.log(data3);

			$.post('{{ url("index/push_block_recorder_resume/create_resume") }}', data3, function(result, status, xhr){
				if(result.status){
					// alert('Pengisian Selesai. Tekan OK untuk menutup.');
					// window.close();
					openSuccessGritter('Success', result.message);
				}
				else{
					openErrorGritter('Error!', result.message);
				}
			});

			var data = {
				push_block_code : push_block_code,
				push_block_id_gen : push_block_id_gen,
				check_date : check_date,
				injection_date_head : injection_date_head,
				injection_date_block : injection_date_block,
				mesin_head : mesin_head,
				mesin_block : mesin_block,
				pic_check : pic_check,
				product_type : product_type,
				head : array_head,
				block : array_block,
				push_pull : push_pull,
				judgement : judgement,
				ketinggian : ketinggian,
				judgementketinggian : judgementketinggian
			}
			// console.table(data);
			$.post('{{ url("index/push_block_recorder/create") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success', result.message);
				}
				else{
					openErrorGritter('Error!', result.message);
				}
			});
			var data2 = {
				push_block_code : push_block_code,
				push_block_id_gen : push_block_id_gen,
				check_date : check_date,
				injection_date_head : injection_date_head,
				injection_date_block : injection_date_block,
				mesin_head : mesin_head,
				mesin_block : mesin_block,
				pic_check : pic_check,
				product_type : product_type,
				head : array_head2,
				block : array_block2,
				push_pull : push_pull2,
				judgement : judgement2,
				ketinggian : ketinggian2,
				judgementketinggian : judgementketinggian2
			}
			$.post('{{ url("index/push_block_recorder/create") }}', data2, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success', result.message);
					alert('Pengisian Selesai. Tekan OK untuk menutup.');
					window.close();
				}
				else{
					openErrorGritter('Error!', result.message);
				}
			});
			// $('#reset_button').show();
			// $('#selesai_button').hide();
		}
	}

	function push_pull(id) {
		var batas_bawah = '{{ $batas_bawah }}';
		var batas_atas = '{{ $batas_atas }}';
		// console.log(id);
		if(id.length == 13){
			push_block = id.substr(id.length - 3);
		}
		else if (id.length == 14){
			push_block = id.substr(id.length - 4);
		}
		else if (id.length == 15){
			push_block = id.substr(id.length - 5);
		}
		var id2 = '#judgement_'+push_block;
		var id3 = 'judgement_'+push_block;
		var x = document.getElementById(id).value;
		if(x == ''){
			document.getElementById(id).style.backgroundColor = "#ff4f4f";
		}
		else{
			document.getElementById(id).style.backgroundColor = "#7fff6e";
		}
		if(parseFloat(x) < parseFloat(batas_bawah) || parseFloat(x) > parseFloat(batas_atas)){
			$(id2).html('NG');
			document.getElementById(id3).style.backgroundColor = "#ff4f4f";
		}
		else{
			$(id2).html('OK');
			document.getElementById(id3).style.backgroundColor = "#7fff6e";
		}
	}

	function ketinggian(id) {
		var batas_tinggi = '{{ $batas_tinggi }}';
		// console.log(id);
		if(id.length == 14){
			push_block = id.substr(id.length - 3);
		}
		else if (id.length == 15){
			push_block = id.substr(id.length - 4);
		}
		else if (id.length == 16){
			push_block = id.substr(id.length - 5);
		}
		// console.log(push_block);
		var id2 = '#judgement2_'+push_block;
		var id3 = 'judgement2_'+push_block;
		var x = document.getElementById(id).value;
		if(x == ''){
			document.getElementById(id).style.backgroundColor = "#ff4f4f";
		}
		else{
			document.getElementById(id).style.backgroundColor = "#7fff6e";
		}
		if(parseFloat(x) <= parseFloat(batas_tinggi)){
			$(id2).html('OK');
			document.getElementById(id3).style.backgroundColor = "#7fff6e";
		}
		else{
			$(id2).html('NG');
			document.getElementById(id3).style.backgroundColor = "#ff4f4f";
		}
	}

	function itemresume1(head_id,block){
		var data = {
			head_id : head_id,
			// block : block
		}
		$.get('{{ url("index/fetchResume") }}', data, function(result, status, xhr){
			$('#tableResume1').DataTable().clear();
			$('#tableResume1').DataTable().destroy();
			$('#tableBodyResume1').html("");
			// console.log(result.datas)
			var tableData = "";
			// var count = 1;
			// console.log(result.datas.cavity_1);
			// $.each(result.datas, function(key, value) {
				// console.log(value.cavity_1);
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>1</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_1 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>2</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_2 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>3</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_3 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>4</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_4 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '</tr>';

				// count += 1;
			// });
			$('#tableBodyResume1').append(tableData);
		});
	}

	function itemresume2(head_id,block){
		var data = {
			head_id : head_id,
			// block : block
		}

		$.get('{{ url("index/fetchResume") }}', data, function(result, status, xhr){
			$('#tableResume2').DataTable().clear();
			$('#tableResume2').DataTable().destroy();
			$('#tableBodyResume2').html("");
			// console.log(result.datas)
			var tableData = "";
			// var count = 1;
			// console.log(result.datas.cavity_1);
			// $.each(result.datas, function(key, value) {
				// console.log(value.cavity_1);
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>1</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_1 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>2</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_2 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>3</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_3 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>4</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_4 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '</tr>';

				// count += 1;
			// });
			$('#tableBodyResume2').append(tableData);
		});
	}

	function itemresume3(head_id,block){
		var data = {
			head_id : head_id,
			// block : block
		}

		$.get('{{ url("index/fetchResume") }}', data, function(result, status, xhr){
			$('#tableResume3').DataTable().clear();
			$('#tableResume3').DataTable().destroy();
			$('#tableBodyResume3').html("");
			// console.log(result.datas)
			var tableData = "";
			// var count = 1;
			// console.log(result.datas.cavity_1);
			// $.each(result.datas, function(key, value) {
				// console.log(value.cavity_1);
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>1</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_1 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>2</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_2 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>3</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_3 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>4</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_4 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '</tr>';

				// count += 1;
			// });
			$('#tableBodyResume3').append(tableData);
		});
	}

	function itemresume4(head_id,block){
		var data = {
			head_id : head_id,
			// block : block
		}

		$.get('{{ url("index/fetchResume") }}', data, function(result, status, xhr){
			$('#tableResume4').DataTable().clear();
			$('#tableResume4').DataTable().destroy();
			$('#tableBodyResume4').html("");
			// console.log(result.datas)
			var tableData = "";
			var count = 1;
			// console.log(result.datas.cavity_1);
			// $.each(result.datas, function(key, value) {
				// console.log(value.cavity_1);
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>1</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_1 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>2</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_2 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>3</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_3 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>4</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_4 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '</tr>';

				count += 1;
			// });
			$('#tableBodyResume4').append(tableData);
		});
	}

	function itemresume5(head_id,block){
		var data = {
			head_id : head_id,
			// block : block
		}

		$.get('{{ url("index/fetchResume") }}', data, function(result, status, xhr){
			$('#tableResume5').DataTable().clear();
			$('#tableResume5').DataTable().destroy();
			$('#tableBodyResume5').html("");
			// console.log(result.datas)
			var tableData = "";
			var count = 1;
			// console.log(result.datas.cavity_1);
			// $.each(result.datas, function(key, value) {
				// console.log(value.cavity_1);
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>1</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_1 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>2</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_2 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>3</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_3 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>4</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_4 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '</tr>';

				count += 1;
			// });
			$('#tableBodyResume5').append(tableData);
		});
	}

	function itemresume6(head_id,block){
		var data = {
			head_id : head_id,
			// block : block
		}

		$.get('{{ url("index/fetchResume") }}', data, function(result, status, xhr){
			$('#tableResume6').DataTable().clear();
			$('#tableResume6').DataTable().destroy();
			$('#tableBodyResume6').html("");
			// console.log(result.datas)
			var tableData = "";
			var count = 1;
			// console.log(result.datas.cavity_1);
			// $.each(result.datas, function(key, value) {
				// console.log(value.cavity_1);
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>1</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_1 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>2</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_2 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>3</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_3 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>4</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_4 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '</tr>';

				count += 1;
			// });
			$('#tableBodyResume6').append(tableData);
		});
	}

	function itemresume7(head_id,block){
		var data = {
			head_id : head_id,
			// block : block
		}

		$.get('{{ url("index/fetchResume") }}', data, function(result, status, xhr){
			$('#tableResume7').DataTable().clear();
			$('#tableResume7').DataTable().destroy();
			$('#tableBodyResume7').html("");
			// console.log(result.datas)
			var tableData = "";
			var count = 1;
			// console.log(result.datas.cavity_1);
			// $.each(result.datas, function(key, value) {
				// console.log(value.cavity_1);
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>1</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_1 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>2</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_2 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>3</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_3 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>4</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_4 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '</tr>';

				count += 1;
			// });
			$('#tableBodyResume7').append(tableData);
		});
	}

	function itemresume8(head_id,block){
		var data = {
			head_id : head_id,
			// block : block
		}

		$.get('{{ url("index/fetchResume") }}', data, function(result, status, xhr){
			$('#tableResume8').DataTable().clear();
			$('#tableResume8').DataTable().destroy();
			$('#tableBodyResume8').html("");
			// console.log(result.datas)
			var tableData = "";
			var count = 1;
			// console.log(result.datas.cavity_1);
			// $.each(result.datas, function(key, value) {
				// console.log(value.cavity_1);
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>1</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_1 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>2</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_2 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>3</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_3 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>4</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_4 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '</tr>';

				count += 1;
			// });
			$('#tableBodyResume8').append(tableData);
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