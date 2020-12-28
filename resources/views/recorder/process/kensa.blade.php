@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	th:hover {
		overflow: visible;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding: 0px;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
		vertical-align: middle;
		background-color: rgb(126,86,134);
		color: #FFD700;
	}
	thead {
		background-color: rgb(126,86,134);
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}

	#ngList1 {
		height:420px;
		overflow-y: scroll;
		-ms-overflow-style: none;  /* IE and Edge */
  		scrollbar-width: none;
	}
	#ngList2 {
		height:420px;
		overflow-y: scroll;
		-ms-overflow-style: none;  /* IE and Edge */
  		scrollbar-width: none;
	}
	#ngList3 {
		height:420px;
		overflow-y: scroll;
		-ms-overflow-style: none;  /* IE and Edge */
  		scrollbar-width: none;
	}
	#ngList4 {
		height:420px;
		overflow-y: scroll;
		-ms-overflow-style: none;  /* IE and Edge */
  		scrollbar-width: none;
	}
	#loading, #error { display: none; }
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		/* display: none; <- Crashes Chrome on hover */
		-webkit-appearance: none;
		margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
	}

	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}
</style>
@stop
@section('header')
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<input type="hidden" id="loc" value="{{ $title }} {{$title_jp}} }">
	
	<div class="row" style="padding-left: 10px; padding-right: 10px;">
		<div class="col-xs-6" style="padding-right: 0; padding-left: 0">
			<table class="table table-bordered" style="width: 100%; margin-bottom: -1px;" border="1">
				<thead>
					<tr>
						<th style="background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;width: 1%">Employee ID</th>
						<th style="background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;width: 3%">Nama<input type="hidden" id="employee_id"></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="background-color: #BDE0FE; text-align: center; color: #352e44;font-size:15px;" id="op">-</td>
						<td style="background-color: #BDE0FE; text-align: center; color: #352e44; font-size: 15px;" id="op2">-</td>
					</tr>
				</tbody>
			</table>
			<table class="table table-bordered" style="width: 100%; margin-bottom: 1px;" border="1">
				<tbody>
					<tr>
						<td style="background-color: #4f4f4f; text-align: center; color: yellow; font-size: 1.5vw;font-weight: bold;" id="product_fix3">-</td>
					</tr>
				</tbody>
			</table>
			<div class="col-xs-12" style="padding: 0px;padding-top: 10px">
				<input type="hidden" id="start_time">
			</div>
		</div>

		<div class="col-xs-6" style="padding-right: 0;">
			<table class="table table-bordered" style="width: 100%; margin-bottom: 1px;" border="1">
				<thead>
					<tr>
						<th style="background-color: rgb(220,220,220); text-align: center; color: #000; padding:0;font-size: 15px;">Kode Kensa</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="background-color: #BDE0FE; text-align: center; color: #352e44; font-size:30px;font-weight: bold;" id="kensa_code">-</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-xs-12" style="padding-right: 0;padding-left: 0">
			<div class="col-xs-3" style="padding-right: 1px;padding-left: 1px" id="divHead">
				<div class="col-xs-12" style="padding-right: 0;padding-left: 0">
					<center style="background-color: rgb(220,220,220);font-size: 20px;font-weight: bold;">HEAD</center>
				</div>
				<div class="col-xs-12" id="ngList1" style="padding-right: 0;padding-left: 0">
					<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th style="width: 17%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >#</th>
							<th style="width: 30%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >NG</th>
							<th style="width: 17%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >#</th>
							<th style="width: 15%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Count</th>
						</tr>
					</thead>
					<tbody>
						<?php $no = 1; ?>
						@foreach($ng_lists1 as $nomor => $ng_list)
						<?php if ($no % 2 === 0 ) {
							$color = 'style="background-color: #fffcb7"';
						} else {
							$color = 'style="background-color: #ffd8b7"';
						}
						?>
						<input type="hidden" id="loopHead" value="{{$loop->count}}">
						<tr <?php echo $color ?>>
							<td id="minusHead" onclick="minusHead({{$nomor+1}})" style="background-color: rgb(255,204,255); font-weight: bold; font-size: 30px; cursor: pointer;" class="unselectable">-</td>
							<td id="ngHead{{$nomor+1}}" style="font-size: 15px;">{{ $ng_list->ng_name }}</td>
							<td id="plusHead" onclick="plusHead({{$nomor+1}})" style="background-color: rgb(204,255,255); font-weight: bold; font-size: 30px; cursor: pointer;" class="unselectable">+</td>
							<td style="font-weight: bold; font-size: 30px; background-color: rgb(100,100,100); color: yellow;"><span id="countHead{{$nomor+1}}">0</span></td>
						</tr>
						<?php $no+=1; ?>
						@endforeach
					</tbody>
				</table>
				</div>
				
			</div>
			<div class="col-xs-3" style="padding-right: 1px;padding-left: 1px" id="divMiddleBody">
				<div class="col-xs-12" style="padding-right: 0;padding-left: 0">
					<center style="background-color: rgb(220,220,220);color:black;font-size: 20px;font-weight: bold;">MIDDLE / BODY</center>
				</div>
				<div class="col-xs-12" id="ngList2" style="padding-right: 0;padding-left: 0">
					<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th style="width: 17%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >#</th>
							<th style="width: 30%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >NG</th>
							<th style="width: 17%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >#</th>
							<th style="width: 15%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Count</th>
						</tr>
					</thead>
					<tbody>
						<?php $no = 1; ?>
						@foreach($ng_lists2 as $nomor => $ng_list)
						<?php if ($no % 2 === 0 ) {
							$color = 'style="background-color: #fffcb7"';
						} else {
							$color = 'style="background-color: #ffd8b7"';
						}
						?>
						<input type="hidden" id="loopMiddleBody" value="{{$loop->count}}">
						<tr <?php echo $color ?>>
							<td id="minusMiddleBody" onclick="minusMiddleBody({{$nomor+1}})" style="background-color: rgb(255,204,255); font-weight: bold; font-size: 30px; cursor: pointer;" class="unselectable">-</td>
							<td id="ngMiddleBody{{$nomor+1}}" style="font-size: 15px;">{{ $ng_list->ng_name }}</td>
							<td id="plusMiddleBody" onclick="plusMiddleBody({{$nomor+1}})" style="background-color: rgb(204,255,255); font-weight: bold; font-size: 30px; cursor: pointer;" class="unselectable">+</td>
							<td style="font-weight: bold; font-size: 30px; background-color: rgb(100,100,100); color: yellow;"><span id="countMiddleBody{{$nomor+1}}">0</span></td>
						</tr>
						<?php $no+=1; ?>
						@endforeach
					</tbody>
				</table>
				</div>
			</div>
			<div class="col-xs-3" style="padding-right: 1px;padding-left: 1px" id="divFootStopper">
				<div class="col-xs-12" style="padding-right: 0;padding-left: 0">
					<center style="background-color: rgb(220,220,220);color:black;font-size: 20px;font-weight: bold;">FOOT / STOPPER</center>
				</div>
				<div class="col-xs-12" id="ngList3" style="padding-right: 0;padding-left: 0">
					<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th style="width: 17%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >#</th>
							<th style="width: 30%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >NG</th>
							<th style="width: 17%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >#</th>
							<th style="width: 15%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Count</th>
						</tr>
					</thead>
					<tbody>
						<?php $no = 1; ?>
						@foreach($ng_lists3 as $nomor => $ng_list)
						<?php if ($no % 2 === 0 ) {
							$color = 'style="background-color: #fffcb7"';
						} else {
							$color = 'style="background-color: #ffd8b7"';
						}
						?>
						<input type="hidden" id="loopFootStopper" value="{{$loop->count}}">
						<tr <?php echo $color ?>>
							<td id="minusFootStopper" onclick="minusFootStopper({{$nomor+1}})" style="background-color: rgb(255,204,255); font-weight: bold; font-size: 30px; cursor: pointer;" class="unselectable">-</td>
							<td id="ngFootStopper{{$nomor+1}}" style="font-size: 15px;">{{ $ng_list->ng_name }}</td>
							<td id="plusFootStopper" onclick="plusFootStopper({{$nomor+1}})" style="background-color: rgb(204,255,255); font-weight: bold; font-size: 30px; cursor: pointer;" class="unselectable">+</td>
							<td style="font-weight: bold; font-size: 30px; background-color: rgb(100,100,100); color: yellow;"><span id="countFootStopper{{$nomor+1}}">0</span></td>
						</tr>
						<?php $no+=1; ?>
						@endforeach
					</tbody>
				</table>
				</div>
			</div>
			<div class="col-xs-3" style="padding-right: 1px;padding-left: 1px" id="divBlock">
				<div class="col-xs-12" style="padding-right: 0;padding-left: 0">
					<center style="background-color: rgb(220,220,220);color:black;font-size: 20px;font-weight: bold;">BLOCK</center>
				</div>
				<div class="col-xs-12" id="ngList4" style="padding-right: 0;padding-left: 0">
					<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th style="width: 17%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >#</th>
							<th style="width: 30%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >NG</th>
							<th style="width: 17%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >#</th>
							<th style="width: 15%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Count</th>
						</tr>
					</thead>
					<tbody>
						<?php $no = 1; ?>
						@foreach($ng_lists4 as $nomor => $ng_list)
						<?php if ($no % 2 === 0 ) {
							$color = 'style="background-color: #fffcb7"';
						} else {
							$color = 'style="background-color: #ffd8b7"';
						}
						?>
						<input type="hidden" id="loopBlock" value="{{$loop->count}}">
						<tr <?php echo $color ?>>
							<td id="minusBlock" onclick="minusBlock({{$nomor+1}})" style="background-color: rgb(255,204,255); font-weight: bold; font-size: 30px; cursor: pointer;" class="unselectable">-</td>
							<td id="ngBlock{{$nomor+1}}" style="font-size: 15px;">{{ $ng_list->ng_name }}</td>
							<td id="plusBlock" onclick="plusBlock({{$nomor+1}})" style="background-color: rgb(204,255,255); font-weight: bold; font-size: 30px; cursor: pointer;" class="unselectable">+</td>
							<td style="font-weight: bold; font-size: 30px; background-color: rgb(100,100,100); color: yellow;"><span id="countBlock{{$nomor+1}}">0</span></td>
						</tr>
						<?php $no+=1; ?>
						@endforeach
					</tbody>
				</table>
				</div>
			</div>
		</div>

		<div class="col-xs-12" style="padding-left: 0;padding-right: 0;padding-top: 20px">
			<div class="col-xs-6" style="padding-left: 0;padding-right: 10px">
				<button class="btn btn-warning" style="width: 100%;font-size: 30px;font-weight: bold;" onclick="gantiProduk()">
					GANTI PRODUK
				</button>
			</div>
			<div class="col-xs-6" style="padding-left: 10px;padding-right: 0">
				<button class="btn btn-success" style="width: 100%;font-size: 30px;font-weight: bold;" onclick="selesaiProses()">
					SELESAI PROSES
				</button>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalOperator">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<label for="exampleInputEmail1">Employee ID</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID Card" required>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalProduct">
	<div class="modal-dialog modal-lg" style="width: 800px">
		<div class="modal-content">
			<div class="modal-header"><center> <b id="statusa" style="font-size: 2vw"></b> </center>
				<div class="modal-body table-responsive no-padding">
					<div class="col-xs-12" id="product_choice" style="padding-top: 20px">
						<div class="row">
							<div class="col-xs-12">
								<center><span style="font-weight: bold; font-size: 18px;">Pilih Produk</span></center>
							</div>
							<div class="col-xs-12" id="product_btn">
								<div class="row">
									@foreach($product_type as $product_type)
									<div class="col-xs-2 col-sm-2 col-md-3" style="padding-top: 5px">
										<center>
											<button class="btn btn-primary" id="{{$product_type}}" style="width: 120px;font-size: 10px;font-weight: bold;" onclick="getProduct(this.id)">{{$product_type}}</button>
										</center>
									</div>
									@endforeach
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12" id="product_fix" style="padding-top: 20px">
						<div class="row">
							<div class="col-xs-12">
								<center><span style="font-weight: bold; font-size: 18px;">Pilih Produk</span></center>
							</div>
							<div class="col-xs-12" style="padding-top: 10px">
								<button class="btn btn-primary" id="product_fix2" style="width: 100%;font-size: 20px;font-weight: bold;" onclick="changeProduct()">
									RC
								</button>
							</div>
						</div>
					</div>
					<div class="col-xs-12" id="divtag" style="padding-top: 10px">
						<div id="divyrs" class="row">
							<div class="col-xs-3">
								<center><span style="font-weight: bold;font-size: 15px;color:white">Tag Head</span></center>
								<input type="text" disabled id="tag_head" onclick="" placeholder="Tag Head" style="width: 100%;font-size: 20px;text-align: center;">
								<div class="col-xs-12" style="padding-top: 10px">
									<div class="row">
										<table class="table table-bordered table-striped" style="margin-bottom: 0px" style="margin-bottom: 0px">
											<tr>
												<td style="font-weight: bold;">Material</td>
												<td id="material_number_head"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Part Name</td>
												<td id="part_name_head"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Part Type</td>
												<td id="part_type_head"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Color</td>
												<td id="color_head"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Cavity</td>
												<td id="cavity_head"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Location</td>
												<td id="location_head"></td>
											</tr>
										</table>
										<button class="btn btn-danger" onclick="cancelTag('head')" style="width: 100%;font-weight: bold;margin-left: 0px;font-size: 15px">
											CANCEL
										</button>
									</div>
								</div>
							</div>
							<div class="col-xs-3">
								<center><span style="font-weight: bold;font-size: 15px;color:white">Tag Middle</span></center>
								<input type="text" disabled id="tag_middle" placeholder="Tag Middle" style="width: 100%;font-size: 20px;text-align: center;">
								<div class="col-xs-12" style="padding-top: 10px">
									<div class="row">
										<table class="table table-bordered table-striped" style="margin-bottom: 0px">
											<tr>
												<td style="font-weight: bold;">Material</td>
												<td id="material_number_middle"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Part Name</td>
												<td id="part_name_middle"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Part Type</td>
												<td id="part_type_middle"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Color</td>
												<td id="color_middle"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Cavity</td>
												<td id="cavity_middle"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Location</td>
												<td id="location_middle"></td>
											</tr>
										</table>
										<button class="btn btn-danger" onclick="cancelTag('middle')" style="width: 100%;font-weight: bold;margin-left: 0px;font-size: 15px">
											CANCEL
										</button>
									</div>
								</div>
							</div>
							<div class="col-xs-3">
								<center><span style="font-weight: bold;font-size: 15px;color:white">Tag Foot</span></center>
								<input type="text" disabled id="tag_foot" placeholder="Tag Foot" style="width: 100%;font-size: 20px;text-align: center;">
								<div class="col-xs-12" style="padding-top: 10px">
									<div class="row">
										<table class="table table-bordered table-striped" style="margin-bottom: 0px">
											<tr>
												<td style="font-weight: bold;">Material</td>
												<td id="material_number_foot"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Part Name</td>
												<td id="part_name_foot"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Part Type</td>
												<td id="part_type_foot"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Color</td>
												<td id="color_foot"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Cavity</td>
												<td id="cavity_foot"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Location</td>
												<td id="location_foot"></td>
											</tr>
										</table>
										<button class="btn btn-danger" onclick="cancelTag('foot')" style="width: 100%;font-weight: bold;margin-left: 0px;font-size: 15px">
											CANCEL
										</button>
									</div>
								</div>
							</div>
							<div class="col-xs-3">
								<center><span style="font-weight: bold;font-size: 15px;color:white">Tag Block</span></center>
								<input type="text" disabled id="tag_block" placeholder="Tag Block" style="width: 100%;font-size: 20px;text-align: center;">
								<div class="col-xs-12" style="padding-top: 10px">
									<div class="row">
										<table class="table table-bordered table-striped" style="margin-bottom: 0px">
											<tr>
												<td style="font-weight: bold;">Material</td>
												<td id="material_number_block"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Part Name</td>
												<td id="part_name_block"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Part Type</td>
												<td id="part_type_block"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Color</td>
												<td id="color_block"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Cavity</td>
												<td id="cavity_block"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Location</td>
												<td id="location_block"></td>
											</tr>
										</table>
										<button class="btn btn-danger" onclick="cancelTag('block')" style="width: 100%;font-weight: bold;margin-left: 0px;font-size: 15px">
											CANCEL
										</button>
									</div>
								</div>
							</div>
						</div>
						<div id="divyrf" class="row">
							<div class="col-xs-4">
								<center><span style="font-weight: bold;font-size: 15px;color:white">Tag Head</span></center>
								<input type="text" disabled id="tag_head_yrf" placeholder="Tag Head YRF" style="width: 100%;font-size: 20px;text-align: center;">
								<div class="col-xs-12" style="padding-top: 10px">
									<div class="row">
										<table class="table table-bordered table-striped" style="margin-bottom: 0px">
											<tr>
												<td style="font-weight: bold;">Material</td>
												<td id="material_number_head_yrf"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Part Name</td>
												<td id="part_name_head_yrf"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Part Type</td>
												<td id="part_type_head_yrf"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Color</td>
												<td id="color_head_yrf"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Cavity</td>
												<td id="cavity_head_yrf"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Location</td>
												<td id="location_head_yrf"></td>
											</tr>
										</table>
										<button class="btn btn-danger" onclick="cancelTag('head_yrf')" style="width: 100%;font-weight: bold;margin-left: 0px;font-size: 15px">
											CANCEL
										</button>
									</div>
								</div>
							</div>
							<div class="col-xs-4">
								<center><span style="font-weight: bold;font-size: 15px;color:white">Tag Body</span></center>
								<input type="text" disabled id="tag_body_yrf" placeholder="Tag Body YRF" style="width: 100%;font-size: 20px;text-align: center;">
								<div class="col-xs-12" style="padding-top: 10px">
									<div class="row">
										<table class="table table-bordered table-striped" style="margin-bottom: 0px">
											<tr>
												<td style="font-weight: bold;">Material</td>
												<td id="material_number_body_yrf"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Part Name</td>
												<td id="part_name_body_yrf"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Part Type</td>
												<td id="part_type_body_yrf"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Color</td>
												<td id="color_body_yrf"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Cavity</td>
												<td id="cavity_body_yrf"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Location</td>
												<td id="location_body_yrf"></td>
											</tr>
										</table>
										<button class="btn btn-danger" onclick="cancelTag('body_yrf')" style="width: 100%;font-weight: bold;margin-left: 0px;font-size: 15px">
											CANCEL
										</button>
									</div>
								</div>
							</div>
							<div class="col-xs-4">
								<center><span style="font-weight: bold;font-size: 15px;color:white">Tag Stopper</span></center>
								<input type="text" disabled id="tag_stopper_yrf" placeholder="Tag Stopper YRF" style="width: 100%;font-size: 20px;text-align: center;">
								<div class="col-xs-12" style="padding-top: 10px">
									<div class="row">
										<table class="table table-bordered table-striped" style="margin-bottom: 0px">
											<tr>
												<td style="font-weight: bold;">Material</td>
												<td id="material_number_stopper_yrf"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Part Name</td>
												<td id="part_name_stopper_yrf"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Part Type</td>
												<td id="part_type_stopper_yrf"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Color</td>
												<td id="color_stopper_yrf"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Cavity</td>
												<td id="cavity_stopper_yrf"></td>
											</tr>
											<tr>
												<td style="font-weight: bold;">Location</td>
												<td id="location_stopper_yrf"></td>
											</tr>
										</table>
										<button class="btn btn-danger" onclick="cancelTag('stopper_yrf')" style="width: 100%;font-weight: bold;margin-left: 0px;font-size: 15px">
											CANCEL
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12" style="padding-top: 20px">
						<div class="row">
							<div class="modal-footer">
								<button onclick="saveProduct()" class="btn btn-success btn-block pull-right" style="font-size: 30px;font-weight: bold;">
									CONFIRM
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	
	jQuery(document).ready(function() {
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('#operator').val('');
		clearAll();
		reset();
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#operator').focus();
	});

	$('#modalProduct').on('shown.bs.modal', function () {
		// $('#operator').focus();
	});

	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator").val().length >= 8){
				var data = {
					employee_id : $("#operator").val()
				}
				
				$.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#modalOperator').modal('hide');
						$('#modalProduct').modal('show');
						$('#op').html(result.employee.employee_id);
						$('#op2').html(result.employee.name);
						$('#employee_id').val(result.employee.employee_id);
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator").val("");
			}			
		}
	});

	function clearAll() {
		$('#product_choice').show();
		$('#product_fix').hide();
	}

	function getProduct(value) {
		$('#product_choice').hide();
		$('#product_fix').show();
		$('#product_fix2').html(value);
		resetProduct();
		if (value.match(/YRS/gi)) {
			$('#divyrs').show();
			$('#divyrf').hide();

			$('#tag_head').removeAttr('disabled');
			$('#tag_middle').removeAttr('disabled');
			$('#tag_foot').removeAttr('disabled');
			$('#tag_block').removeAttr('disabled');

			$('#tag_head').focus();
		}
		if(value.match(/YRF/gi)){
			$('#divyrs').hide();
			$('#divyrf').show();

			$('#tag_head_yrf').removeAttr('disabled');
			$('#tag_body_yrf').removeAttr('disabled');
			$('#tag_stopper_yrf').removeAttr('disabled');

			$('#tag_head_yrf').focus();
		}
	}

	function changeProduct(value) {
		$('#product_choice').show();
		$('#product_fix').hide();
		$('#product_fix2').html('RC');
		reset();
	}

	function reset() {
		$('#tag_head').val('');
		$('#tag_middle').val('');
		$('#tag_foot').val('');
		$('#tag_block').val('');
		$('#divyrs').hide();
		$('#divyrf').hide();

		$('#product_fix2').html('RC');
		$('#product_fix3').html('-');
		$('#kensa_code').html('-');

		$('#tag_head').prop('disabled',true);
		$('#tag_middle').prop('disabled',true);
		$('#tag_foot').prop('disabled',true);
		$('#tag_block').prop('disabled',true);

		$('#tag_head_yrf').prop('disabled',true);
		$('#tag_body_yrf').prop('disabled',true);
		$('#tag_stopper_yrf').prop('disabled',true);

		$('#material_number_head').text("");
		$('#part_name_head').text("");
		$('#part_type_head').text("");
		$('#color_head').text("");
		$('#cavity_head').text("");
		$('#location_head').text("");

		$('#material_number_middle').text("");
		$('#part_name_middle').text("");
		$('#part_type_middle').text("");
		$('#color_middle').text("");
		$('#cavity_middle').text("");
		$('#location_middle').text("");

		$('#material_number_foot').text("");
		$('#part_name_foot').text("");
		$('#part_type_foot').text("");
		$('#color_foot').text("");
		$('#cavity_foot').text("");
		$('#location_foot').text("");

		$('#material_number_block').text("");
		$('#part_name_block').text("");
		$('#part_type_block').text("");
		$('#color_block').text("");
		$('#cavity_block').text("");
		$('#location_block').text("");

		$('#material_number_head_yrf').text("");
		$('#part_name_head_yrf').text("");
		$('#part_type_head_yrf').text("");
		$('#color_head_yrf').text("");
		$('#cavity_head_yrf').text("");
		$('#location_head_yrf').text("");

		$('#material_number_body_yrf').text("");
		$('#part_name_body_yrf').text("");
		$('#part_type_body_yrf').text("");
		$('#color_body_yrf').text("");
		$('#cavity_body_yrf').text("");
		$('#location_body_yrf').text("");

		$('#material_number_stopper_yrf').text("");
		$('#part_name_stopper_yrf').text("");
		$('#part_type_stopper_yrf').text("");
		$('#color_stopper_yrf').text("");
		$('#cavity_stopper_yrf').text("");
		$('#location_stopper_yrf').text("");

		var jumlah_ng_head = '{{$nomor+1}}';
		for (var i = 1; i <= jumlah_ng_head; i++ ) {
			if($('#countHead'+i).text() != 0){
				$('#countHead'+i).html('0');
			}
		}

		var jumlah_ng_middle = '{{$nomor+1}}';
		for (var i = 1; i <= jumlah_ng_middle; i++ ) {
			if($('#countMiddleBody'+i).text() != 0){
				$('#countMiddleBody'+i).html('0');
			}
		}

		var jumlah_ng_foot = '{{$nomor+1}}';
		for (var i = 1; i <= jumlah_ng_foot; i++ ) {
			if($('#countFootStopper'+i).text() != 0){
				$('#countFootStopper'+i).html('0');
			}
		}

		var jumlah_ng_block = '{{$nomor+1}}';
		for (var i = 1; i <= jumlah_ng_block; i++ ) {
			if($('#countBlock'+i).text() != 0){
				$('#countBlock'+i).html('0');
			}
		}
	}

	function resetProduct() {
		$('#tag_head').val("");
		$('#tag_middle').val("");
		$('#tag_foot').val("");
		$('#tag_block').val("");

		$('#tag_head_yrf').val("");
		$('#tag_body_yrf').val("");
		$('#tag_stopper_yrf').val("");

		$('#material_number_head').text("");
		$('#part_name_head').text("");
		$('#part_type_head').text("");
		$('#color_head').text("");
		$('#cavity_head').text("");
		$('#location_head').text("");

		$('#material_number_middle').text("");
		$('#part_name_middle').text("");
		$('#part_type_middle').text("");
		$('#color_middle').text("");
		$('#cavity_middle').text("");
		$('#location_middle').text("");

		$('#material_number_foot').text("");
		$('#part_name_foot').text("");
		$('#part_type_foot').text("");
		$('#color_foot').text("");
		$('#cavity_foot').text("");
		$('#location_foot').text("");

		$('#material_number_block').text("");
		$('#part_name_block').text("");
		$('#part_type_block').text("");
		$('#color_block').text("");
		$('#cavity_block').text("");
		$('#location_block').text("");

		$('#material_number_head_yrf').text("");
		$('#part_name_head_yrf').text("");
		$('#part_type_head_yrf').text("");
		$('#color_head_yrf').text("");
		$('#cavity_head_yrf').text("");
		$('#location_head_yrf').text("");

		$('#material_number_body_yrf').text("");
		$('#part_name_body_yrf').text("");
		$('#part_type_body_yrf').text("");
		$('#color_body_yrf').text("");
		$('#cavity_body_yrf').text("");
		$('#location_body_yrf').text("");

		$('#material_number_stopper_yrf').text("");
		$('#part_name_stopper_yrf').text("");
		$('#part_type_stopper_yrf').text("");
		$('#color_stopper_yrf').text("");
		$('#cavity_stopper_yrf').text("");
		$('#location_stopper_yrf').text("");
	}

	function cancelTag(type) {
		$('#tag_'+type).removeAttr('disabled');
		$('#tag_'+type).val('');
		$('#tag_'+type).focus();
		$('#material_number_'+type).html('');
		$('#part_name_'+type).html('');
		$('#part_type_'+type).html('');
		$('#color_'+type).html('');
		$('#cavity_'+type).html('');
		$('#location_'+type).html('');
	}

	$('#tag_head').keyup(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag_head").val().length == 10){
				var data = {
					tag : $("#tag_head").val(),
					type : 'head'
				}
				$.get('{{ url("scan/recorder/kensa") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success','Tag Ditemukan');
						$('#material_number_head').text(result.tag.material_number);
						$('#part_name_head').text(result.tag.part_name);
						$('#part_type_head').text(result.tag.part_type);
						$('#color_head').text(result.tag.color);
						$('#cavity_head').text(result.tag.cavity);
						$('#location_head').text(result.tag.location);
						$('#tag_head').prop('disabled',true);
						$('#tag_middle').focus();
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#tag_head').val('');
						$('#tag_head').focus();
					}
				});
			}
			else{
				audio_error.play();
				openErrorGritter('Error', 'Tag Tidak Ditemukan');
				$('#tag_head').val('');
				$('#tag_head').focus();
			}	
		}
	});

	$('#tag_middle').keyup(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag_middle").val().length == 10){
				var data = {
					tag : $("#tag_middle").val(),
					type : 'middle'
				}
				$.get('{{ url("scan/recorder/kensa") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success','Tag Ditemukan');
						$('#material_number_middle').text(result.tag.material_number);
						$('#part_name_middle').text(result.tag.part_name);
						$('#part_type_middle').text(result.tag.part_type);
						$('#color_middle').text(result.tag.color);
						$('#cavity_middle').text(result.tag.cavity);
						$('#location_middle').text(result.tag.location);
						$('#tag_middle').prop('disabled',true);
						$('#tag_foot').focus();
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#tag_middle').val('');
						$('#tag_middle').focus();
					}
				});
			}
			else{
				audio_error.play();
				openErrorGritter('Error', 'Tag Tidak Ditemukan');
				$('#tag_middle').val('');
				$('#tag_middle').focus();
			}	
		}
	});

	$('#tag_foot').keyup(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag_foot").val().length == 10){
				var data = {
					tag : $("#tag_foot").val(),
					type : 'foot'
				}
				$.get('{{ url("scan/recorder/kensa") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success','Tag Ditemukan');
						$('#material_number_foot').text(result.tag.material_number);
						$('#part_name_foot').text(result.tag.part_name);
						$('#part_type_foot').text(result.tag.part_type);
						$('#color_foot').text(result.tag.color);
						$('#cavity_foot').text(result.tag.cavity);
						$('#location_foot').text(result.tag.location);
						$('#tag_foot').prop('disabled',true);
						$('#tag_block').focus();
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#tag_foot').val('');
						$('#tag_foot').focus();
					}
				});
			}
			else{
				audio_error.play();
				openErrorGritter('Error', 'Tag Tidak Ditemukan');
				$('#tag_foot').val('');
				$('#tag_foot').focus();
			}	
		}
	});

	$('#tag_block').keyup(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag_block").val().length == 10){
				var data = {
					tag : $("#tag_block").val(),
					type : 'block'
				}
				$.get('{{ url("scan/recorder/kensa") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success','Tag Ditemukan');
						$('#material_number_block').text(result.tag.material_number);
						$('#part_name_block').text(result.tag.part_name);
						$('#part_type_block').text(result.tag.part_type);
						$('#color_block').text(result.tag.color);
						$('#cavity_block').text(result.tag.cavity);
						$('#location_block').text(result.tag.location);
						$('#tag_block').prop('disabled',true);
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#tag_block').val('');
						$('#tag_block').focus();
					}
				});
			}
			else{
				audio_error.play();
				openErrorGritter('Error', 'Tag Tidak Ditemukan');
				$('#tag_block').val('');
				$('#tag_block').focus();
			}	
		}
	});

	$('#tag_head_yrf').keyup(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag_head_yrf").val().length == 10){
				var data = {
					tag : $("#tag_head_yrf").val(),
					type : 'head_yrf'
				}
				$.get('{{ url("scan/recorder/kensa") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success','Tag Ditemukan');
						$('#material_number_head_yrf').text(result.tag.material_number);
						$('#part_name_head_yrf').text(result.tag.part_name);
						$('#part_type_head_yrf').text(result.tag.part_type);
						$('#color_head_yrf').text(result.tag.color);
						$('#cavity_head_yrf').text(result.tag.cavity);
						$('#location_head_yrf').text(result.tag.location);
						$('#tag_head_yrf').prop('disabled',true);
						$('#tag_body_yrf').focus();
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#tag_head_yrf').val('');
						$('#tag_head_yrf').focus();
					}
				});
			}
			else{
				audio_error.play();
				openErrorGritter('Error', 'Tag Tidak Ditemukan');
				$('#tag_head_yrf').val('');
				$('#tag_head_yrf').focus();
			}	
		}
	});

	$('#tag_body_yrf').keyup(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag_body_yrf").val().length == 10){
				var data = {
					tag : $("#tag_body_yrf").val(),
					type : 'body_yrf'
				}
				$.get('{{ url("scan/recorder/kensa") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success','Tag Ditemukan');
						$('#material_number_body_yrf').text(result.tag.material_number);
						$('#part_name_body_yrf').text(result.tag.part_name);
						$('#part_type_body_yrf').text(result.tag.part_type);
						$('#color_body_yrf').text(result.tag.color);
						$('#cavity_body_yrf').text(result.tag.cavity);
						$('#location_body_yrf').text(result.tag.location);
						$('#tag_body_yrf').prop('disabled',true);
						$('#tag_stopper_yrf').focus();
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#tag_body_yrf').val('');
						$('#tag_body_yrf').focus();
					}
				});
			}
			else{
				audio_error.play();
				openErrorGritter('Error', 'Tag Tidak Ditemukan');
				$('#tag_body_yrf').val('');
				$('#tag_body_yrf').focus();
			}	
		}
	});

	$('#tag_stopper_yrf').keyup(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag_stopper_yrf").val().length == 10){
				var data = {
					tag : $("#tag_stopper_yrf").val(),
					type : 'stopper_yrf'
				}
				$.get('{{ url("scan/recorder/kensa") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success','Tag Ditemukan');
						$('#material_number_stopper_yrf').text(result.tag.material_number);
						$('#part_name_stopper_yrf').text(result.tag.part_name);
						$('#part_type_stopper_yrf').text(result.tag.part_type);
						$('#color_stopper_yrf').text(result.tag.color);
						$('#cavity_stopper_yrf').text(result.tag.cavity);
						$('#location_stopper_yrf').text(result.tag.location);
						$('#tag_stopper_yrf').prop('disabled',true);
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#tag_stopper_yrf').val('');
						$('#tag_stopper_yrf').focus();
					}
				});
			}
			else{
				audio_error.play();
				openErrorGritter('Error', 'Tag Tidak Ditemukan');
				$('#tag_stopper_yrf').val('');
				$('#tag_stopper_yrf').focus();
			}	
		}
	});

	function saveProduct() {
		$('#loading').show();
		var product = $('#product_fix2').text();
		var empty = 0;
		if ($('#product_fix2').text() == 'RC') {
			empty++;
		}
		if (product.match(/YRS/gi)) {
			if ($('#tag_head').val() == '' || $('#tag_middle').val() == '' || $('#tag_foot').val() == '' || $('#tag_block').val() == '' || $('#product_fix2').text() == 'RC') {
				empty++;
			}else{
				var tag_head = $('#tag_head').val();
				var tag_middle = $('#tag_middle').val();
				var tag_foot = $('#tag_foot').val();
				var tag_block = $('#tag_block').val();

				var material_number_head = $('#material_number_head').text();
				var part_name_head = $('#part_name_head').text();
				var part_type_head = $('#part_type_head').text();
				var color_head = $('#color_head').text();
				var cavity_head = $('#cavity_head').text();
				var location_head = $('#location_head').text();

				var material_number_middle = $('#material_number_middle').text();
				var part_name_middle = $('#part_name_middle').text();
				var part_type_middle = $('#part_type_middle').text();
				var color_middle = $('#color_middle').text();
				var cavity_middle = $('#cavity_middle').text();
				var location_middle = $('#location_middle').text();

				var material_number_foot = $('#material_number_foot').text();
				var part_name_foot = $('#part_name_foot').text();
				var part_type_foot = $('#part_type_foot').text();
				var color_foot = $('#color_foot').text();
				var cavity_foot = $('#cavity_foot').text();
				var location_foot = $('#location_foot').text();

				var material_number_block = $('#material_number_block').text();
				var part_name_block = $('#part_name_block').text();
				var part_type_block = $('#part_type_block').text();
				var color_block = $('#color_block').text();
				var cavity_block = $('#cavity_block').text();
				var location_block = $('#location_block').text();

				var employee_id = $('#employee_id').val();

				var data = {
					product:product,
					tag_head:tag_head,
					tag_middle:tag_middle,
					tag_foot:tag_foot,
					tag_block:tag_block,
					material_number_head:material_number_head,
					part_name_head:part_name_head,
					part_type_head:part_type_head,
					color_head:color_head,
					cavity_head:cavity_head,
					location_head:location_head,
					material_number_middle:material_number_middle,
					part_name_middle:part_name_middle,
					part_type_middle:part_type_middle,
					color_middle:color_middle,
					cavity_middle:cavity_middle,
					location_middle:location_middle,
					material_number_foot:material_number_foot,
					part_name_foot:part_name_foot,
					part_type_foot:part_type_foot,
					color_foot:color_foot,
					cavity_foot:cavity_foot,
					location_foot:location_foot,
					material_number_block:material_number_block,
					part_name_block:part_name_block,
					part_type_block:part_type_block,
					color_block:color_block,
					cavity_block:cavity_block,
					location_block:location_block,
					employee_id:employee_id,
				}
			}
		}
		if(product.match(/YRF/gi)){
			if ($('#tag_head_yrf').val() == "" || $('#tag_body_yrf').val() == "" || $('#tag_stopper_yrf').val() == "" || $('#product_fix2').text() == 'RC') {
				empty++;
			}else{
				var tag_head_yrf = $('#tag_head_yrf').val();
				var tag_body_yrf = $('#tag_body_yrf').val();
				var tag_stopper_yrf = $('#tag_stopper_yrf').val();

				var material_number_head_yrf = $('#material_number_head_yrf').text();
				var part_name_head_yrf = $('#part_name_head_yrf').text();
				var part_type_head_yrf = $('#part_type_head_yrf').text();
				var color_head_yrf = $('#color_head_yrf').text();
				var cavity_head_yrf = $('#cavity_head_yrf').text();
				var location_head_yrf = $('#location_head_yrf').text();

				var material_number_body_yrf = $('#material_number_body_yrf').text();
				var part_name_body_yrf = $('#part_name_body_yrf').text();
				var part_type_body_yrf = $('#part_type_body_yrf').text();
				var color_body_yrf = $('#color_body_yrf').text();
				var cavity_body_yrf = $('#cavity_body_yrf').text();
				var location_body_yrf = $('#location_body_yrf').text();

				var material_number_stopper_yrf = $('#material_number_stopper_yrf').text();
				var part_name_stopper_yrf = $('#part_name_stopper_yrf').text();
				var part_type_stopper_yrf = $('#part_type_stopper_yrf').text();
				var color_stopper_yrf = $('#color_stopper_yrf').text();
				var cavity_stopper_yrf = $('#cavity_stopper_yrf').text();
				var location_stopper_yrf = $('#location_stopper_yrf').text();

				var employee_id = $('#employee_id').val();

				var data = {
					product:product,
					tag_head_yrf:tag_head_yrf,
					tag_body_yrf:tag_body_yrf,
					tag_stopper_yrf:tag_stopper_yrf,
					material_number_head_yrf:material_number_head_yrf,
					part_name_head_yrf:part_name_head_yrf,
					part_type_head_yrf:part_type_head_yrf,
					color_head_yrf:color_head_yrf,
					cavity_head_yrf:cavity_head_yrf,
					location_head_yrf:location_head_yrf,
					material_number_body_yrf:material_number_body_yrf,
					part_name_body_yrf:part_name_body_yrf,
					part_type_body_yrf:part_type_body_yrf,
					color_body_yrf:color_body_yrf,
					cavity_body_yrf:cavity_body_yrf,
					location_body_yrf:location_body_yrf,
					material_number_stopper_yrf:material_number_stopper_yrf,
					part_name_stopper_yrf:part_name_stopper_yrf,
					part_type_stopper_yrf:part_type_stopper_yrf,
					color_stopper_yrf:color_stopper_yrf,
					cavity_stopper_yrf:cavity_stopper_yrf,
					location_stopper_yrf:location_stopper_yrf,
					employee_id:employee_id,
				}
			}
		}

		if (empty == 0) {
			$.post('{{ url("input/recorder/kensa_product") }}', data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					$('#product_fix3').html($('#product_fix2').text());
					$('#modalProduct').modal('hide');
					$('#kensa_code').html(result.kensa_code);
					if (result.product.match(/YRS/gi)) {
						$('#divBlock').show();
						$('#divHead').prop('class','col-xs-3');
						$('#divMiddleBody').prop('class','col-xs-3');
						$('#divFootStopper').prop('class','col-xs-3');
						$('#divBlock').prop('class','col-xs-3');
					}else if(result.product.match(/YRF/gi)){
						$('#divBlock').hide();
						$('#divHead').prop('class','col-xs-4');
						$('#divMiddleBody').prop('class','col-xs-4');
						$('#divFootStopper').prop('class','col-xs-4');
					}
					// reset();
					clearAll();
					$('#start_time').val(getActualFullDate());
					openSuccessGritter('Success','Memulai Kensa');
				}
				else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error', result.message);
				}
			});
		}else{
			$('#loading').hide();
			audio_error.play();
			alert('Semua Data Harus Diisi');
		}
	}

	function plusHead(id){
		var count = $('#countHead'+id).text();
		if($('#start_time').val() == ""){
			audio_error.play();
			openErrorGritter('Error!', 'Process Not Started.');
		}else{
			$('#countHead'+id).text(parseInt(count)+1);
		}
	}

	function minusHead(id){
		var count = $('#countHead'+id).text();
		if($('#start_time').val() == ""){
			audio_error.play();
			openErrorGritter('Error!', 'Process Not Started.');
		}else{
			if(count > 0)
			{
				$('#countHead'+id).text(parseInt(count)-1);
			}
		}
	}

	function plusMiddleBody(id){
		var count = $('#countMiddleBody'+id).text();
		if($('#start_time').val() == ""){
			audio_error.play();
			openErrorGritter('Error!', 'Process Not Started.');
		}else{
			$('#countMiddleBody'+id).text(parseInt(count)+1);
		}
	}

	function minusMiddleBody(id){
		var count = $('#countMiddleBody'+id).text();
		if($('#start_time').val() == ""){
			audio_error.play();
			openErrorGritter('Error!', 'Process Not Started.');
		}else{
			if(count > 0)
			{
				$('#countMiddleBody'+id).text(parseInt(count)-1);
			}
		}
	}

	function plusFootStopper(id){
		var count = $('#countFootStopper'+id).text();
		if($('#start_time').val() == ""){
			audio_error.play();
			openErrorGritter('Error!', 'Process Not Started.');
		}else{
			$('#countFootStopper'+id).text(parseInt(count)+1);
		}
	}

	function minusFootStopper(id){
		var count = $('#countFootStopper'+id).text();
		if($('#start_time').val() == ""){
			audio_error.play();
			openErrorGritter('Error!', 'Process Not Started.');
		}else{
			if(count > 0)
			{
				$('#countFootStopper'+id).text(parseInt(count)-1);
			}
		}
	}

	function plusBlock(id){
		var count = $('#countBlock'+id).text();
		if($('#start_time').val() == ""){
			audio_error.play();
			openErrorGritter('Error!', 'Process Not Started.');
		}else{
			$('#countBlock'+id).text(parseInt(count)+1);
		}
	}

	function minusBlock(id){
		var count = $('#countBlock'+id).text();
		if($('#start_time').val() == ""){
			audio_error.play();
			openErrorGritter('Error!', 'Process Not Started.');
		}else{
			if(count > 0)
			{
				$('#countBlock'+id).text(parseInt(count)-1);
			}
		}
	}

	function gantiProduk() {
		if (confirm('Apakah Anda yakin akan ganti produk?')) {
			$("#modalProduct").modal('show');
			reset();
		}
	}

	function selesaiProses() {
		if (confirm('Apakah Anda yakin akan mengakhiri proses?')) {
			var start_time = $('#start_time').val();
			var employee_id = $('#employee_id').val();
			var serial_number = $('#kensa_code').text();
			var product = $('#product_fix3').text();

			if (product.match(/YRS/gi)) {
				var ng_name_head = [];
				var ng_count_head = [];
				var jumlah_ng_head = '{{$nomor+1}}';
				for (var i = 1; i <= jumlah_ng_head; i++ ) {
					if($('#countHead'+i).text() != 0){
						ng_name_head.push($('#ngHead'+i).text());
						ng_count_head.push($('#countHead'+i).text());
					}
				}

				var ng_name_middle = [];
				var ng_count_middle = [];
				var jumlah_ng_middle = '{{$nomor+1}}';
				for (var i = 1; i <= jumlah_ng_middle; i++ ) {
					if($('#countMiddleBody'+i).text() != 0){
						ng_name_middle.push($('#ngMiddleBody'+i).text());
						ng_count_middle.push($('#countMiddleBody'+i).text());
					}
				}

				var ng_name_foot = [];
				var ng_count_foot = [];
				var jumlah_ng_foot = '{{$nomor+1}}';
				for (var i = 1; i <= jumlah_ng_foot; i++ ) {
					if($('#countFootStopper'+i).text() != 0){
						ng_name_foot.push($('#ngFootStopper'+i).text());
						ng_count_foot.push($('#countFootStopper'+i).text());
					}
				}

				var ng_name_block = [];
				var ng_count_block = [];
				var jumlah_ng_block = '{{$nomor+1}}';
				for (var i = 1; i <= jumlah_ng_block; i++ ) {
					if($('#countBlock'+i).text() != 0){
						ng_name_block.push($('#ngBlock'+i).text());
						ng_count_block.push($('#countBlock'+i).text());
					}
				}

				var data = {
					start_time:start_time,
					employee_id:employee_id,
					serial_number:serial_number,
					product:product,
					ng_name_head:ng_name_head.join(),
					ng_name_middle:ng_name_middle.join(),
					ng_name_foot:ng_name_foot.join(),
					ng_name_block:ng_name_block.join(),
					ng_count_head:ng_count_head.join(),
					ng_count_middle:ng_count_middle.join(),
					ng_count_foot:ng_count_foot.join(),
					ng_count_block:ng_count_block.join(),
				}
			}else if(product.match(/YRF/gi)){
				var ng_name_head_yrf = [];
				var ng_count_head_yrf = [];
				var jumlah_ng_head_yrf = '{{$nomor+1}}';
				for (var i = 1; i <= jumlah_ng_head_yrf; i++ ) {
					if($('#countHead'+i).text() != 0){
						ng_name_head_yrf.push($('#ngHead'+i).text());
						ng_count_head_yrf.push($('#countHead'+i).text());
					}
				}

				var ng_name_body_yrf = [];
				var ng_count_body_yrf = [];
				var jumlah_ng_body_yrf = '{{$nomor+1}}';
				for (var i = 1; i <= jumlah_ng_body_yrf; i++ ) {
					if($('#countMiddleBody'+i).text() != 0){
						ng_name_body_yrf.push($('#ngMiddleBody'+i).text());
						ng_count_body_yrf.push($('#countMiddleBody'+i).text());
					}
				}

				var ng_name_stopper_yrf = [];
				var ng_count_stopper_yrf = [];
				var jumlah_ng_stopper_yrf = '{{$nomor+1}}';
				for (var i = 1; i <= jumlah_ng_stopper_yrf; i++ ) {
					if($('#countFootStopper'+i).text() != 0){
						ng_name_stopper_yrf.push($('#ngFootStopper'+i).text());
						ng_count_stopper_yrf.push($('#countFootStopper'+i).text());
					}
				}

				var data = {
					start_time:start_time,
					employee_id:employee_id,
					serial_number:serial_number,
					product:product,
					ng_name_head_yrf:ng_name_head_yrf.join(),
					ng_name_body_yrf:ng_name_body_yrf.join(),
					ng_name_stopper_yrf:ng_name_stopper_yrf.join(),
					ng_count_head_yrf:ng_count_head_yrf.join(),
					ng_count_body_yrf:ng_count_body_yrf.join(),
					ng_count_stopper_yrf:ng_count_stopper_yrf.join(),
				}
			}


			$.post('{{ url("input/recorder/kensa") }}', data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					alert('Kensa Selesai');
					openSuccessGritter('Success','Kensa Selesai');
					location.reload();
				}
				else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error', result.message);
				}
			});
		}
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
			time: '3000'
		});
	}

	$.date = function(dateObject) {
		var d = new Date(dateObject);
		var day = d.getDate();
		var month = d.getMonth() + 1;
		var year = d.getFullYear();
		if (day < 10) {
			day = "0" + day;
		}
		if (month < 10) {
			month = "0" + month;
		}
		var date = day + "/" + month + "/" + year;

		return date;
	};

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}

	function getActualFullDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return year + "-" + month + "-" + day + " " + h + ":" + m + ":" + s;
	}
</script>
@endsection