@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		font-size: 16px;
	}

	#tablehead> tbody > tr > td :hover {
		cursor: pointer;
		background-color: #e0e0e0;
	}

	#tableblock> tbody > tr > td :hover {
		cursor: pointer;
		background-color: #e0e0e0;
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

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<span class="text-purple"> {{ $title_jp }}</span>
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
							<span style="font-weight: bold; text-align: center;font-size: 15px;">PIC Check</span>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-12">
							<input id="pic_check" style="font-size: 20px; height: 30px; text-align: center;" type="text" class="form-control" value="{{ $name }}" disabled>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 15px;">Check Date</span>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-12">
							<input style="font-size: 20px; height: 30px; text-align: center;" id="check_date" type="text" class="form-control" readonly value="{{ date('Y-m-d h:i:s') }}">
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
							<span style="font-weight: bold; text-align: center;font-size: 15px;">Injection Date</span>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-12">
							<input id="inj_date" style="font-size: 20px; height: 30px; text-align: center;" type="text" class="form-control" value="" disabled>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 15px;">Product Type</span>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-12">
							<input style="font-size: 20px; height: 30px; text-align: center;" id="prod_type" type="text" class="form-control" readonly value="">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row" style="padding-top:10px">
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
			<button class="btn btn-danger" onclick="konfirmasi()" id="selesai_button" style="font-size:35px; width: 100%; font-weight: bold; padding: 0;">
				SELESAI
			</button>
			<button class="btn btn-warning" onclick="reset()" id="reset_button" style="font-size:35px; width: 100%; font-weight: bold; padding: 0;">
				RESET
			</button>
		</div>
	</div>

	<div class="modal fade" id="modalHeadBlock">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div class="col-xs-12">
							<div class="col-xs-6">
								<div class="row">
									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-12">
												<span style="font-weight: bold; font-size: 18px;">Injection Date</span>
											</div>
										</div>
									</div>
									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-12">
												<input id="injection_date" style="font-size: 20px; height: 30px; text-align: center;" type="text" class="form-control" placeholder="Select Injection Date">
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
												<span style="font-weight: bold; font-size: 18px;">Product Type</span>
											</div>
										</div>
									</div>
									<div class="col-xs-12">
										<div class="row">
											<div class="col-xs-12">
												<select class="form-control" style="width: 100%; height: 30px; font-size: 14px; text-align: center;" id="product_type" name="product_type" data-placeholder="Pilih Tipe Produk" required>
									              <option disabled>Pilih Tipe Produk</option>
									              @foreach($product_type as $product_type)
									              	<option value='{{ $product_type }}'>{{ $product_type }}</option>
									              @endforeach
									            </select>
									        </div>
									    </div>
									</div>
								</div>
							</div>
							<div class="col-xs-6" style="padding-top:10px">
								<div class="box">
									<div class="box-body">
										<div class="col-xs-12">
											<span style="font-size: 20px; font-weight: bold;"><center>HEAD</center></span>
										</div>
										<table class="table" id="tablehead">
											<thead>
												<tr>
													<th style="width: 1%;"></th>
												</tr>					
											</thead>
											<tbody>
												<tr>
													<td width="50%" onclick="getData(1)">
														<center>
															<p style="font-size: 1.5vw;">1-4</p>
														</center>
													</td>
													<td width="50%" onclick="getData(2)">
														<center>
															<p style="font-size: 1.5vw;">5-8</p>
														</center>
													</td>
												</tr>	
												<tr>
													<td width="50%" onclick="getData(3)">
														<center>
															<p style="font-size: 1.5vw;">9-12</p>
														</center>
													</td>
													<td width="50%" onclick="getData(4)">
														<center>
															<p style="font-size: 1.5vw;">13-16</p>
														</center>
													</td>
												</tr>
												<tr>
													<td width="50%" onclick="getData(5)">
														<center>
															<p style="font-size: 1.5vw;">17-20</p>
														</center>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="col-xs-6" style="padding-top:10px">
								<div class="box">
									<div class="box-body">
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
															<p style="font-size: 1.5vw;padding: 14px">1-8</p>
														</center>
													</td>
													<td width="50%" onclick="getData2(7)">
														<center>
															<p style="font-size: 1.5vw;padding: 14px">9-16</p>
														</center>
													</td>
												</tr>	
												<tr>
													<td width="50%" onclick="getData2(8)">
														<center>
															<p style="font-size: 1.5vw;padding: 14px">17-24</p>
														</center>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xs-6">
							<div class="col-xs-2">
								<span style="font-weight: bold; font-size: 16px;">Head</span>
							</div>
							<div class="col-xs-8">
								<input type="hidden" id="head_id" style="width: 24%; height: 30px; font-size:20px; text-align: center;" disabled>
								<input type="hidden" id="head_value" style="width: 24%; height: 30px; font-size:20px; text-align: center;" disabled>
								<input type="text" id="head_1" style="width: 24%; height: 30px; font-size: 20px; text-align: center;" disabled>
								<input type="text" id="head_2" style="width: 24%; height: 30px; font-size: 20px; text-align: center;" disabled>
								<input type="text" id="head_3" style="width: 24%; height: 30px; font-size: 20px; text-align: center;" disabled>
								<input type="text" id="head_4" style="width: 24%; height: 30px; font-size: 20px; text-align: center;" disabled>
							</div>
						</div>
						<div class="col-xs-6">
							<div class="col-xs-2">
								<span style="font-weight: bold; font-size: 16px;">Block</span>
							</div>
							<div class="col-xs-8">
								<input type="hidden" id="block_id" style="width: 11%; height: 30px; font-size: 20px; text-align: center;" disabled>
								<input type="hidden" id="block_value" style="width: 30%; height: 30px; font-size: 20px; text-align: center;" disabled>
								<input type="text" id="block_1" style="width: 11%; height: 30px; font-size: 20px; text-align: center;" disabled>
								<input type="text" id="block_2" style="width: 11%; height: 30px; font-size: 20px; text-align: center;" disabled>
								<input type="text" id="block_3" style="width: 11%; height: 30px; font-size: 20px; text-align: center;" disabled>
								<input type="text" id="block_4" style="width: 11%; height: 30px; font-size: 20px; text-align: center;" disabled>
								<input type="text" id="block_5" style="width: 11%; height: 30px; font-size: 20px; text-align: center;" disabled>
								<input type="text" id="block_6" style="width: 11%; height: 30px; font-size: 20px; text-align: center;" disabled>
								<input type="text" id="block_7" style="width: 11%; height: 30px; font-size: 20px; text-align: center;" disabled>
								<input type="text" id="block_8" style="width: 11%; height: 30px; font-size: 20px; text-align: center;" disabled>
							</div>
						</div>
						<div class="col-xs-12">
							<div class="modal-footer">
								{{-- <button type="button" class="btn btn-success" onclick="confirm()" data-dismiss="modal">CONFIRM</button> --}}
								<input type="submit" value="CONFIRM" onclick="confirm()" class="btn btn-success">
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
	$('#injection_date').datepicker({
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

	});

	jQuery.extend(jQuery.expr[':'], {
	    focusable: function (el, index, selector) {
	        return $(el).is('a, button, :input, [tabindex]');
	    }
	});

	$('#modalHeadBlock').on('shown.bs.modal', function () {
			$('#injection_date').focus();
	});

	$(document).on('keypress', 'input,select', function (e) {
	    if (e.which == 13) {
	        e.preventDefault();
	        // Get all focusable elements on the page
	        var $canfocus = $(':focusable');
	        var index = $canfocus.index(document.activeElement) + 1;
	        if (index >= $canfocus.length) index = 0;
	        $canfocus.eq(index).focus();
	    }
	});

	function plusCount(){
		$('#addCount').val(parseInt($('#addCount').val())+1);
	}

	function minusCount(){
		$('#addCount').val(parseInt($('#addCount').val())-1);
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
			$('#head_value').val('8-12');
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
			$('#block_value').val('16-24');
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

	function confirm() {
		if($('#injection_date').val() == '' || $('#head_id').val() == '' || $('#block_id').val() == ''){
			alert('Semua Data Harus Diisi.');
		}else{
			$('#prod_type').val($('#product_type').val());
			$('#inj_date').val($('#injection_date').val());
			$('#modalHeadBlock').modal('hide');
			itemresume1($("#head_id").val(),$("#block_1").val());
			itemresume2($("#head_id").val(),$("#block_2").val());
			itemresume3($("#head_id").val(),$("#block_3").val());
			itemresume4($("#head_id").val(),$("#block_4").val());
			itemresume5($("#head_id").val(),$("#block_5").val());
			itemresume6($("#head_id").val(),$("#block_6").val());
			itemresume7($("#head_id").val(),$("#block_7").val());
			itemresume8($("#head_id").val(),$("#block_8").val());
		}
	}

	function reset(){
		window.location = "{{ url('index/recorder_process_push_block/'.$remark) }}";
	}

	function konfirmasi(){
		var head_id =  $("#head_id").val();
		var block_id =  $("#block_id").val();

		var head_value =  $("#head_value").val();
		var block_value =  $("#block_value").val();

		var check_date = $("#check_date").val();
		var injection_date = $("#inj_date").val();
		var product_type = $("#prod_type").val();
		var pic_check = $("#pic_check").val();

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
				check_date : check_date,
				injection_date : injection_date,
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
				height_ng_value2 : height_ng_value
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
				check_date : check_date,
				injection_date : injection_date,
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
				check_date : check_date,
				injection_date : injection_date,
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
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#1</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_1 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#2</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_2 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#3</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_3 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#4</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_4 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_4+'_'+block+'"></td>';
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
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#1</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_1 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#2</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_2 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#3</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_3 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#4</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_4 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_4+'_'+block+'"></td>';
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
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#1</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_1 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#2</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_2 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#3</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_3 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#4</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_4 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_4+'_'+block+'"></td>';
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
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#1</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_1 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#2</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_2 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#3</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_3 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#4</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_4 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_4+'_'+block+'"></td>';
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
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#1</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_1 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#2</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_2 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#3</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_3 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#4</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_4 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_4+'_'+block+'"></td>';
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
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#1</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_1 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#2</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_2 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#3</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_3 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#4</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_4 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_4+'_'+block+'"></td>';
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
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#1</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_1 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#2</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_2 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#3</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_3 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#4</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_4 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_4+'_'+block+'"></td>';
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
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#1</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_1 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_1+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#2</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_2 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_2+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#3</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_3 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement2_'+result.datas.cavity_3+'_'+block+'"></td>';
				tableData += '</tr>';
				tableData += '<tr>';
				tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>#4</b></td>';
				tableData += '<td style="text-align:right">'+ result.datas.cavity_4 +'</td>';
				tableData += '<td style="text-align:right">'+ block +'</td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="push_pull(this.id)" class="form-control" id="push_pull_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="height: 100%; text-align: center;" id="judgement_'+result.datas.cavity_4+'_'+block+'"></td>';
				tableData += '<td style="padding:0;text-align:right"><input type="text" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="ketinggian(this.id)" class="form-control" id="ketinggian_'+result.datas.cavity_4+'_'+block+'"></td>';
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