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

	#tableResumeHead> tbody > tr :hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	#tableResumeFoot> tbody > tr :hover {
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
		<div class="col-xs-6" style="padding-right: 5px">
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
								</tr>
								<tr>
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
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-6" style="padding-left: 5px">
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
										Tanggal Injeksi Head
									</td>
									<td class="label-success" id="injection_date_head_fix" style="font-weight: bold; text-align: center;font-size: 15px;">
									</td>
									<td class="label-primary" style="font-weight: bold; text-align: center;font-size: 15px;">
										Mesin Injeksi Head
									</td>
									<td class="label-success" id="mesin_head" style="font-weight: bold; text-align: center;font-size: 15px;">
									</td>
								</tr>
								<tr>
									<td class="label-primary" style="font-weight: bold; text-align: center;font-size: 15px;">
										Tanggal Injeksi Foot
									</td>
									<td class="label-success" id="injection_date_foot_fix" style="font-weight: bold; text-align: center;font-size: 15px;">
									</td>
									<td class="label-primary" style="font-weight: bold; text-align: center;font-size: 15px;">
										Mesin Injeksi Foot
									</td>
									<td class="label-success" id="mesin_foot" style="font-weight: bold; text-align: center;font-size: 15px;">
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
	<div class="row" style="padding-top:0px;">
		<div class="col-xs-6" style="padding-right:5px;">
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-xs-12" style="padding:0">
						<div class="col-xs-12" style="padding-top: 0px">
							<span style="font-size: 20px; font-weight: bold;"><center>HEAD - MIDDLE</center></span>
						</div>
						<input type="hidden" id="check_type_head" value="HJ-MJ">
						<table class="table table-hover table-striped table-bordered" id="tableResumeHead">
							<thead>
								<tr>
									<th style="width: 1%;font-size: 12px;color:white;background-color:#605ca8"><center>No.</center></th>
									<th style="width: 2%;font-size: 12px;color:white;background-color:#605ca8"><center>Middle</center></th>
									<th style="width: 2%;font-size: 12px;color:white;background-color:#605ca8"><center>Head</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Torque 1</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Torque 2</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Torque 3</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Average</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Judgement</center></th>
								</tr>
							</thead>
							<tbody id="tableBodyResumeHead">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-6" style="padding-left:5px;">
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-xs-12" style="padding:0">
						<div class="col-xs-12" style="padding-top: 0px">
							<span style="font-size: 20px; font-weight: bold;"><center>MIDDLE - FOOT</center></span>
						</div>
						<input type="hidden" id="check_type_foot" value="MJ-FJ">
						<table class="table table-hover table-striped table-bordered" id="tableResumeFoot">
							<thead>
								<tr>
									<th style="width: 1%;font-size: 12px;color:white;background-color:#605ca8"><center>No.</center></th>
									<th style="width: 2%;font-size: 12px;color:white;background-color:#605ca8"><center>Middle</center></th>
									<th style="width: 2%;font-size: 12px;color:white;background-color:#605ca8"><center>Foot</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Torque 1</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Torque 2</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Torque 3</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Average</center></th>
									<th style="width: 3%;font-size: 12px;color:white;background-color:#605ca8"><center>Judgement</center></th>
								</tr>
							</thead>
							<tbody id="tableBodyResumeFoot">
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
							<div class="col-xs-4">
								<div class="col-xs-12">
									<div class="row">
										<div class="col-xs-12">
											<center><span style="font-weight: bold; font-size: 18px;">Head</span></center>
										</div>
									</div>
								</div>
								<div class="input-group col-md-12">
									<div class="input-group-addon" id="icon-serial" style="font-weight: bold; font-size: 1.5vw;">
										<i class="glyphicon glyphicon-credit-card"></i>
									</div>
									<input type="text" style="text-align: center; font-size: 1.5vw; height: 50px" class="form-control" id="tag_head" name="tag_head" placeholder="Scan Tag Head Here ..." required>
									<input type="hidden" id="material_number_head">
									<div class="input-group-addon" id="icon-serial" style="font-weight: bold; font-size: 1.5vw;">
										<i class="glyphicon glyphicon-credit-card"></i>
									</div>
								</div>
								<div class="col-xs-12" style="padding-top: 10px;padding-left: 0px;padding-right: 0px">
									<table class="table table-bordered">
										<tr>
											<td style="width: 50%">
												Injection Date Head
											</td>
											<td id="injection_date_head">
											</td>
										</tr>
										<tr>
											<td>
												Mesin Head
											</td>
											<td id="mesin_head_fix2">
											</td>
										</tr>
										<tr>
											<td>
												Cavity Head
											</td>
											<td id="head_value">
											</td>
										</tr>
										<tr>
											<td>
												Product
											</td>
											<td id="product_fix2">
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div class="col-xs-4">
								<div class="col-xs-12">
									<div class="row">
										<div class="col-xs-12">
											<center><span style="font-weight: bold; font-size: 18px;">Middle</span></center>
										</div>
									</div>
								</div>
								<div class="input-group col-md-12">
									<div class="input-group-addon" id="icon-serial" style="font-weight: bold; font-size: 1.5vw;">
										<i class="glyphicon glyphicon-credit-card"></i>
									</div>
									<input type="text" style="text-align: center; font-size: 1.5vw; height: 50px" class="form-control" id="tag_middle" name="tag_middle" placeholder="Scan Tag Middle Here ..." required disabled>
									<input type="hidden" id="material_number_middle">
									<div class="input-group-addon" id="icon-serial" style="font-weight: bold; font-size: 1.5vw;">
										<i class="glyphicon glyphicon-credit-card"></i>
									</div>
								</div>
								<div class="col-xs-12" style="padding-top: 10px;padding-left: 0px;padding-right: 0px">
									<table class="table table-bordered">
										<tr>
											<td style="width: 50%">
												Injection Date Middle
											</td>
											<td id="injection_date_middle">
											</td>
										</tr>
										<tr>
											<td>
												Mesin Middle
											</td>
											<td id="mesin_middle_fix2">
											</td>
										</tr>
										<tr>
											<td>
												Cavity Middle
											</td>
											<td id="middle_value">
											</td>
										</tr>
										<tr>
											<td>
												Product
											</td>
											<td id="product_fix3">
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div class="col-xs-4">
								<div class="col-xs-12">
									<div class="row">
										<div class="col-xs-12">
											<center><span style="font-weight: bold; font-size: 18px;">Foot</span></center>
										</div>
									</div>
								</div>
								<div class="input-group col-md-12">
									<div class="input-group-addon" id="icon-serial" style="font-weight: bold; font-size: 1.5vw;">
										<i class="glyphicon glyphicon-credit-card"></i>
									</div>
									<input type="text" style="text-align: center; font-size: 1.5vw; height: 50px" class="form-control" id="tag_foot" name="tag_foot" placeholder="Scan Tag Foot Here ..." required disabled>
									<input type="hidden" id="material_number_foot">
									<div class="input-group-addon" id="icon-serial" style="font-weight: bold; font-size: 1.5vw;">
										<i class="glyphicon glyphicon-credit-card"></i>
									</div>
								</div>
								<div class="col-xs-12" style="padding-top: 10px;padding-left: 0px;padding-right: 0px">
									<table class="table table-bordered">
										<tr>
											<td style="width: 50%">
												Injection Date Foot
											</td>
											<td id="injection_date_foot">
											</td>
										</tr>
										<tr>
											<td>
												Mesin Foot
											</td>
											<td id="mesin_foot_fix2">
											</td>
										</tr>
										<tr>
											<td>
												Cavity Foot
											</td>
											<td id="foot_value">
											</td>
										</tr>
										<tr>
											<td>
												Product
											</td>
											<td id="product_fix4">
											</td>
										</tr>
									</table>
								</div>
							</div>
						<div class="col-xs-4"  id="head_fix">
							<div class="col-xs-12">
								<input type="hidden" id="head_id" style="width: 11%; height: 30px; font-size: 20px; text-align: center;" disabled>
								<input type="hidden" id="head_value" style="width: 30%; height: 30px; font-size: 20px; text-align: center;" disabled>
								<table class="table table-bordered">
									<tr>
										<td>
											<input type="text" id="head_fix_1" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
										<td>
											<input type="text" id="head_fix_2" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
										<td>
											<input type="text" id="head_fix_3" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
										<td>
											<input type="text" id="head_fix_4" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
									</tr>
								</table>
							</div>
						</div>
						<div class="col-xs-4" style="padding-top: 0px" id="middle_fix">
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
						<div class="col-xs-4" id="foot_fix">
							<div class="col-xs-12">
								<input type="hidden" id="foot_id" style="width: 11%; height: 30px; font-size: 20px; text-align: center;" disabled>
								<input type="hidden" id="foot_value" style="width: 30%; height: 30px; font-size: 20px; text-align: center;" disabled>
								<table class="table table-bordered">
									<tr>
										<td>
											<input type="text" id="foot_fix_1" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
										<td>
											<input type="text" id="foot_fix_2" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
										<td>
											<input type="text" id="foot_fix_3" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
										<td>
											<input type="text" id="foot_fix_4" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
										<td>
											<input type="text" id="foot_fix_5" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
										<td>
											<input type="text" id="foot_fix_6" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" disabled>
										</td>
									</tr>
								</table>
							</div>
						</div>
						<div class="col-xs-12" style="padding-top: 20px">
							<div class="modal-footer">
								<button onclick="mulaiProses()" class="btn btn-success" style="width: 100%;font-size: 40px;font-weight: bold;">
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
	// $('#injection_date_middle').datepicker({
 //      autoclose: true,
 //      format: 'yyyy-mm-dd',
 //      todayHighlight: true
 //    });

 //    $('#injection_date_head').datepicker({
 //      autoclose: true,
 //      format: 'yyyy-mm-dd',
 //      todayHighlight: true
 //    });

 //    $('#injection_date_foot').datepicker({
 //      autoclose: true,
 //      format: 'yyyy-mm-dd',
 //      todayHighlight: true
 //    });

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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
		$('#mesin_middle_fix').hide();
		$('#mesin_head_fix').hide();
		$('#mesin_foot_fix').hide();

		$('#head_id').val("");
		$('#head_fix_1').val("");
		$('#head_fix_2').val("");
		$('#head_fix_3').val("");
		$('#head_fix_4').val("");
		$('#middle_id').val("");
		$('#middle_fix_1').val("");
		$('#middle_fix_2').val("");
		$('#middle_fix_3').val("");
		$('#middle_fix_4').val("");
		$('#foot_id').val("");
		$('#foot_fix_1').val("");
		$('#foot_fix_2').val("");
		$('#foot_fix_3').val("");
		$('#foot_fix_4').val("");
		$('#foot_fix_5').val("");
		$('#foot_fix_6').val("");
		$("#tag_middle").val("");
		$("#tag_foot").val("");
	});

	$('#modalMiddleHeadFoot').on('shown.bs.modal', function () {
		$("#tag_head").val("");
		$('#tag_head').focus();
	});

	$('#tag_head').keyup(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag_head").val().length >= 7){
				var data = {
					tag : $("#tag_head").val(),
					type : 'head',
					check : 'torque',
				}

				$.get('{{ url("scan/recorder") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', 'Scan Tag Success');
						$('#tag_head').prop('disabled',true);

						$.each(result.data, function(key, value) {
							var mesin = value.mesin.split(' ');
							$('#injection_date_head').html(value.injection_date);
							$('#mesin_head_fix2').html('#'+mesin[1]);
							$('#head_value').html(value.cavity);
							$('#product_fix2').html(value.part_name);
							$('#material_number_head').val(value.material_number);
							getData4(value.cavity,'head');
						})

						$("#tag_middle").val("");
						$("#tag_middle").removeAttr("disabled");
						$("#tag_middle").focus();
					}
					else{
						openErrorGritter('Error!', 'Tag Invalid');
						audio_error.play();
						$("#tag_head").val("");
						$("#tag_head").focus();
						$('#injection_date_head').html("");
						$('#mesin_head_fix2').html("");
						$('#head_value').html("");
						$('#product_fix2').html("");
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Tag Invalid');
				audio_error.play();
				$("#tag_head").val("");
				$("#tag_head").focus();
				$('#injection_date_head').html("");
				$('#mesin_head_fix2').html("");
				$('#head_value').html("");
				$('#product_fix2').html("");
			}			
		}
	});

	$('#tag_middle').keyup(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag_middle").val().length >= 7){
				var data = {
					tag : $("#tag_middle").val(),
					type : 'middle',
					check : 'torque',
				}

				$.get('{{ url("scan/recorder") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', 'Scan Tag Success');
						$('#tag_middle').prop('disabled',true);

						$.each(result.data, function(key, value) {
							var mesin = value.mesin.split(' ');
							$('#injection_date_middle').html(value.injection_date);
							$('#mesin_middle_fix2').html('#'+mesin[1]);
							$('#middle_value').html(value.cavity);
							$('#product_fix3').html(value.part_name);
							$('#material_number_middle').val(value.material_number);
							getData4(value.cavity,'middle');
						})

						$("#tag_foot").val("");
						$("#tag_foot").removeAttr("disabled");
						$("#tag_foot").focus();
					}
					else{
						openErrorGritter('Error!', 'Tag Invalid');
						audio_error.play();
						$("#tag_middle").val("");
						$("#tag_middle").focus();
						$('#injection_date_middle').html("");
						$('#mesin_middle_fix2').html("");
						$('#middle_value').html("");
						$('#product_fix3').html("");
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Tag Invalid');
				audio_error.play();
				$("#tag_middle").val("");
				$("#tag_middle").focus();
				$('#injection_date_middle').html("");
				$('#mesin_middle_fix2').html("");
				$('#middle_value').html("");
				$('#product_fix3').html("");
			}			
		}
	});

	$('#tag_foot').keyup(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag_foot").val().length >= 7){
				var data = {
					tag : $("#tag_foot").val(),
					type : 'foot',
					check : 'torque',
				}

				$.get('{{ url("scan/recorder") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', 'Scan Tag Success');
						$('#tag_foot').prop('disabled',true);

						$.each(result.data, function(key, value) {
							var mesin = value.mesin.split(' ');
							$('#injection_date_foot').html(value.injection_date);
							$('#mesin_foot_fix2').html('#'+mesin[1]);
							$('#foot_value').html(value.cavity);
							$('#product_fix4').html(value.part_name);
							$('#material_number_foot').val(value.material_number);
							getData4(value.cavity,'foot');
						})
					}
					else{
						openErrorGritter('Error!', 'Tag Invalid');
						audio_error.play();
						$("#tag_foot").val("");
						$("#tag_foot").focus();
						$('#injection_date_foot').html("");
						$('#mesin_foot_fix2').html("");
						$('#foot_value').html("");
						$('#product_fix4').html("");
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Tag Invalid');
				audio_error.play();
				$("#tag_foot").val("");
				$("#tag_foot").focus();
				$('#injection_date_foot').html("");
				$('#mesin_foot_fix2').html("");
				$('#foot_value').html("");
				$('#product_fix4').html("");
			}			
		}
	});

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

	function getMesinFoot(mesin) {
		$('#mesin_foot_choice').hide();
		$('#mesin_foot_fix').show();
		$('#mesin_foot_fix2').html(mesin);
	}

	function changeMesinFoot() {
		$('#mesin_foot_choice').show();
		$('#mesin_foot_fix').hide();
		$('#mesin_foot_fix2').html('#0');
	}

	function getData4(cavity,type){
		var data = {
			cavity : cavity,
			type : type,
		}

		$.get('{{ url("fetch/fetch_cavity") }}', data, function(result, status, xhr){
			if(result.status){
				if (type === 'head') {
					$('#head_id').val(result.id);
					$('#head_fix_1').val(result.cavity_1);
					$('#head_fix_2').val(result.cavity_2);
					$('#head_fix_3').val(result.cavity_3);
					$('#head_fix_4').val(result.cavity_4);
				}else if(type === 'middle'){
					$('#middle_id').val(result.id);
					$('#middle_fix_1').val(result.cavity_1);
					$('#middle_fix_2').val(result.cavity_2);
					$('#middle_fix_3').val(result.cavity_3);
					$('#middle_fix_4').val(result.cavity_4);
				}else if(type === 'foot'){
					$('#foot_id').val(result.id);
					$('#foot_fix_1').val(result.cavity_1);
					$('#foot_fix_2').val(result.cavity_2);
					$('#foot_fix_3').val(result.cavity_3);
					$('#foot_fix_4').val(result.cavity_4);
					$('#foot_fix_5').val(result.cavity_5);
					$('#foot_fix_6').val(result.cavity_6);
				}
			}
			else{
				openErrorGritter('Error!', 'Tag Invalid');
				audio_error.play();
				if (type === 'head') {
					$("#tag_head").removeAttr('disabled');
					$("#tag_head").val("");
					$("#tag_head").focus();
					$('#injection_date_head').html("");
					$('#mesin_head_fix2').html("");
					$('#head_value').html("");
					$('#product_fix2').html("");
				}else if(type === 'middle'){
					$("#tag_middle").removeAttr('disabled');
					$("#tag_middle").val("");
					$("#tag_middle").focus();
					$('#injection_date_middle').html("");
					$('#mesin_middle_fix2').html("");
					$('#middle_value').html("");
					$('#product_fix3').html("");
				}else if(type === 'foot'){
					$("#tag_foot").removeAttr('disabled');
					$("#tag_foot").val("");
					$("#tag_foot").focus();
					$('#injection_date_foot').html("");
					$('#mesin_foot_fix2').html("");
					$('#foot_value').html("");
					$('#product_fix4').html("");
				}
			}
		});
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
		var type = 'head';
		var data = {
			no_cavity : no_cavity,
			type : type,
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
		}else if (no_cavity == 9) {
			$('#head_value').val('1-6');
		}else if (no_cavity == 10) {
			$('#head_value').val('11-16');
		}else if (no_cavity == 11) {
			$('#head_value').val('1-4');
		}else if (no_cavity == 12) {
			$('#head_value').val('5-8');
		}else if (no_cavity == 13) {
			$('#head_value').val('01-04');
		}

		$.get('{{ url("index/fetch_push_block") }}', data, function(result, status, xhr){
			if(result.status){
				$('#head_id').val(result.id);
				$('#head_fix_1').val(result.cavity_1);
				$('#head_fix_2').val(result.cavity_2);
				$('#head_fix_3').val(result.cavity_3);
				$('#head_fix_4').val(result.cavity_4);
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function getData3(no_cavity){
		var type = 'foot';
		var data = {
			no_cavity : no_cavity,
			type : type,
		}

		if (no_cavity == 1) {
			$('#foot_value').val('1-4');
		}else if (no_cavity == 2) {
			$('#foot_value').val('5-8');
		}else if (no_cavity == 3) {
			$('#foot_value').val('9-12');
		}else if (no_cavity == 4) {
			$('#foot_value').val('13-16');
		}else if (no_cavity == 5) {
			$('#foot_value').val('17-20');
		}else if (no_cavity == 9) {
			$('#foot_value').val('1-6');
		}else if (no_cavity == 10) {
			$('#foot_value').val('11-16');
		}else if (no_cavity == 11) {
			$('#foot_value').val('1-4');
		}else if (no_cavity == 12) {
			$('#foot_value').val('5-8');
		}else if (no_cavity == 13) {
			$('#foot_value').val('01-04');
		}

		$.get('{{ url("index/fetch_push_block") }}', data, function(result, status, xhr){
			if(result.status){
				$('#foot_id').val(result.id);
				$('#foot_fix_1').val(result.cavity_1);
				$('#foot_fix_2').val(result.cavity_2);
				$('#foot_fix_3').val(result.cavity_3);
				$('#foot_fix_4').val(result.cavity_4);
				$('#foot_fix_5').val(result.cavity_5);
				$('#foot_fix_6').val(result.cavity_6);
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function mulaiProses() {
		if ($('#product_fix2').text() == "YRF-21K//ID" || $('#product_fix2').text() ==  "YRF-21//ID" || $('#product_fix2').text() == "YRF-21 (FSA)" || $('#product_fix2').text() == "YRF21") {
			if($('#tag_head').val() == '' || $('#tag_middle').val() == ''){
				alert('Semua Data Harus Diisi.');
			}else{
				$('#prod_type').html($('#product_fix2').text());
				$('#injection_date_middle_fix').html($('#injection_date_middle').text());
				$('#injection_date_head_fix').html($('#injection_date_head').text());
				$('#mesin_middle').html($('#mesin_middle_fix2').text());
				$('#mesin_head').html($('#mesin_head_fix2').text());
				$('#modalMiddleHeadFoot').modal('hide');
				itemresumehead($("#middle_id").val(),$("#head_id").val());
				get_temp();
				setInterval(update_temp,30000);
			}
		}else{
			if($('#tag_head').val() == '' || $('#tag_middle').val() == '' || $('#tag_foot').val() == ''){
				alert('Semua Data Harus Diisi.');
			}else{
				$('#prod_type').html($('#product_fix2').text());
				$('#injection_date_middle_fix').html($('#injection_date_middle').text());
				$('#injection_date_head_fix').html($('#injection_date_head').text());
				$('#injection_date_foot_fix').html($('#injection_date_foot').text());
				$('#mesin_middle').html($('#mesin_middle_fix2').text());
				$('#mesin_head').html($('#mesin_head_fix2').text());
				$('#mesin_foot').html($('#mesin_foot_fix2').text());
				$('#modalMiddleHeadFoot').modal('hide');
				itemresumehead($("#middle_id").val(),$("#head_id").val());
				itemresumefoot($("#middle_id").val(),$("#foot_id").val());
				get_temp();
				setInterval(update_temp,30000);
			}
		}
	}

	function create_temp(){

		var check_date = $("#check_date").text();
		var check_type_head = $("#check_type_head").val();
		var check_type_foot = $("#check_type_foot").val();
		var injection_date_middle = $("#injection_date_middle_fix").text();
		var injection_date_head = $("#injection_date_head_fix").text();
		var injection_date_foot = $("#injection_date_foot_fix").text();
		var mesin_middle = $("#mesin_middle").text();
		var mesin_head = $("#mesin_head").text();
		var mesin_foot = $("#mesin_foot").text();
		var product_type = $("#prod_type").text();
		var pic_check = $("#pic_check").text();
		var push_block_code = '{{ $remark }}';

		if ($('#product_fix2').text() == "YRF-21K//ID" || $('#product_fix2').text() == "YRF-21//ID" || $('#product_fix2').text() == "YRF-21 (FSA)" || $('#product_fix2').text() == "YRF21") {

			var array_middlehm = [];
			var array_head = [];
			var array_middlehm2 = [];
			var array_head2 = [];

			var array_middlemf = [];
			var array_foot = [];
			var array_middlemf2 = [];
			var array_foot2 = [];

			indexHead = 2;

			for(var i = 1; i <= 2; i++){
				array_middlehm.push($("#middle_fix_"+[i]).val());
			}
			for(var j = 1; j <= indexHead; j++){
				array_head.push($("#head_fix_"+[j]).val());
			}

			var indexhm = 1;
			for(var i=0;i<2;i++){
				for(var j=0;j<indexHead;j++){
					array_middlehm2.push(array_middlehm[i]);
					array_head2.push(array_head[j]);
					indexhm++;
				}
			}

			var data = {
				push_block_code : push_block_code,
				check_date : check_date,
				check_type : check_type_head,
				injection_date_middle : injection_date_middle,
				injection_date_head_foot : injection_date_head,
				mesin_middle : mesin_middle,
				mesin_head_foot : mesin_head,
				pic_check : pic_check,
				product_type : product_type,
				middle : array_middlehm2,
				head_foot : array_head2
			}
			$.post('{{ url("index/push_block_recorder/create_temp_torque") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success', result.message);
				}
				else{
					openErrorGritter('Error!', result.message);
				}
			});
		}else{
			var array_middlehm = [];
			var array_head = [];
			var array_middlehm2 = [];
			var array_head2 = [];

			var array_middlemf = [];
			var array_foot = [];
			var array_middlemf2 = [];
			var array_foot2 = [];

			indexHead = 4;

			if ($('#foot_id').val() == 9 || $('#foot_id').val() == 10) {
				indexFoot = 6;
			}else{
				indexFoot = 4;
			}

			for(var i = 1; i <= 4; i++){
				array_middlehm.push($("#middle_fix_"+[i]).val());
			}
			for(var j = 1; j <= indexHead; j++){
				array_head.push($("#head_fix_"+[j]).val());
			}

			var indexhm = 1;
			for(var i=0;i<4;i++){
				for(var j=0;j<indexHead;j++){
					array_middlehm2.push(array_middlehm[i]);
					array_head2.push(array_head[j]);
					indexhm++;
				}
			}

			for(var i = 1; i <= 4; i++){
				array_middlemf.push($("#middle_fix_"+[i]).val());
			}
			for(var j = 1; j <= indexFoot; j++){
				array_foot.push($("#foot_fix_"+[j]).val());
			}

			var indexmf = 1;
			for(var i=0;i<4;i++){
				for(var j=0;j<indexFoot;j++){
					array_middlemf2.push(array_middlemf[i]);
					array_foot2.push(array_foot[j]);
					indexmf++;
				}
			}
			//HEAD
			var data = {
				push_block_code : push_block_code,
				check_date : check_date,
				check_type : check_type_head,
				injection_date_middle : injection_date_middle,
				injection_date_head_foot : injection_date_head,
				mesin_middle : mesin_middle,
				mesin_head_foot : mesin_head,
				pic_check : pic_check,
				product_type : product_type,
				middle : array_middlehm2,
				head_foot : array_head2
			}
			$.post('{{ url("index/push_block_recorder/create_temp_torque") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success', result.message);
				}
				else{
					openErrorGritter('Error!', result.message);
				}
			});

			//FOOT
			var data2 = {
				push_block_code : push_block_code,
				check_date : check_date,
				check_type : check_type_foot,
				injection_date_middle : injection_date_middle,
				injection_date_head_foot : injection_date_foot,
				mesin_middle : mesin_middle,
				mesin_head_foot : mesin_foot,
				pic_check : pic_check,
				product_type : product_type,
				middle : array_middlemf2,
				head_foot : array_foot2
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
		if ($('#product_fix2').text() == "YRF-21K//ID" || $('#product_fix2').text() == "YRF-21//ID" || $('#product_fix2').text() == "YRF-21 (FSA)" || $('#product_fix2').text() == "YRF21") {
			var array_middlehm = [];
			var array_head = [];
			var array_middlehm2 = [];
			var array_head2 = [];

			var array_middlemf = [];
			var array_foot = [];
			var array_middlemf2 = [];
			var array_foot2 = [];

			var check_type_head = $("#check_type_head").val();
			var check_type_foot = $("#check_type_foot").val();

			indexHead = 2;
			indexFoot = 2;

			for(var i = 1; i <= 2; i++){
				array_middlehm.push($("#middle_fix_"+[i]).val());
			}
			for(var j = 1; j <= indexHead; j++){
				array_head.push($("#head_fix_"+[j]).val());
			}
			
			var indexhm = 1;
			for(var i=0;i<2;i++){
				for(var j=0;j<indexHead;j++){
					array_middlehm2.push(array_middlehm[i]);
					array_head2.push(array_head[j]);
					indexhm++;
				}
			}

			for(var i = 1; i <= 2; i++){
				array_middlemf.push($("#middle_fix_"+[i]).val());
			}
			for(var j = 1; j <= indexFoot; j++){
				array_foot.push($("#foot_fix_"+[j]).val());
			}
			
			var indexmf = 1;
			for(var i=0;i<2;i++){
				for(var j=0;j<indexFoot;j++){
					array_middlemf2.push(array_middlemf[i]);
					array_foot2.push(array_foot[j]);
					indexmf++;
				}
			}

			//HEAD
			var data = {
				array_middle : array_middlehm2,
				array_head_foot : array_head2,
				remark : '{{$remark}}',
				product_type : $("#prod_type").text(),
				indexHeadFoot:indexHead,
				check_type:check_type_head
			}

			$.get('{{ url("index/push_block_recorder/get_temp_torque") }}',data,  function(result, status, xhr){
				if(result.status){
					if(result.datas.length != 0){
						index = 1;
						$.each(result.datas, function(key, value) {
							$('#torquehm_1_'+index).val(value.torque1);
							$('#torquehm_2_'+index).val(value.torque2);
							$('#torquehm_3_'+index).val(value.torque3);
							$('#averagehm_'+index).html(value.torqueavg);
							$('#judgementhm_'+index).html(value.judgement);
							$("#prod_type").html(value.product_type);
							$("#check_date").html(value.check_date);
							$('#injection_date_middle_fix').html(value.injection_date_middle);
							$('#injection_date_head_fix').html(value.injection_date_head_foot);
							$('#mesin_middle').html(value.mesin_middle);
							$('#mesin_head').html(value.mesin_head_foot);
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
			var array_middlehm = [];
			var array_head = [];
			var array_middlehm2 = [];
			var array_head2 = [];

			var array_middlemf = [];
			var array_foot = [];
			var array_middlemf2 = [];
			var array_foot2 = [];

			var check_type_head = $("#check_type_head").val();
			var check_type_foot = $("#check_type_foot").val();

			indexHead = 4;

			if ($('#foot_id').val() == 9 || $('#foot_id').val() == 10) {
				indexFoot = 6;
			}else{
				indexFoot = 4;
			}

			for(var i = 1; i <= 4; i++){
				array_middlehm.push($("#middle_fix_"+[i]).val());
			}
			for(var j = 1; j <= indexHead; j++){
				array_head.push($("#head_fix_"+[j]).val());
			}
			
			var indexhm = 1;
			for(var i=0;i<4;i++){
				for(var j=0;j<indexHead;j++){
					array_middlehm2.push(array_middlehm[i]);
					array_head2.push(array_head[j]);
					indexhm++;
				}
			}

			for(var i = 1; i <= 4; i++){
				array_middlemf.push($("#middle_fix_"+[i]).val());
			}
			for(var j = 1; j <= indexFoot; j++){
				array_foot.push($("#foot_fix_"+[j]).val());
			}
			
			var indexmf = 1;
			for(var i=0;i<4;i++){
				for(var j=0;j<indexFoot;j++){
					array_middlemf2.push(array_middlemf[i]);
					array_foot2.push(array_foot[j]);
					indexmf++;
				}
			}

			//HEAD
			var data = {
				array_middle : array_middlehm2,
				array_head_foot : array_head2,
				remark : '{{$remark}}',
				product_type : $("#prod_type").text(),
				indexHeadFoot:indexHead,
				check_type:check_type_head
			}

			$.get('{{ url("index/push_block_recorder/get_temp_torque") }}',data,  function(result, status, xhr){
				if(result.status){
					if(result.datas.length != 0){
						index = 1;
						$.each(result.datas, function(key, value) {
							$('#torquehm_1_'+index).val(value.torque1);
							$('#torquehm_2_'+index).val(value.torque2);
							$('#torquehm_3_'+index).val(value.torque3);
							$('#averagehm_'+index).html(value.torqueavg);
							$('#judgementhm_'+index).html(value.judgement);
							$("#prod_type").html(value.product_type);
							$("#check_date").html(value.check_date);
							$('#injection_date_middle_fix').html(value.injection_date_middle);
							$('#injection_date_head_fix').html(value.injection_date_head_foot);
							$('#mesin_middle').html(value.mesin_middle);
							$('#mesin_head').html(value.mesin_head_foot);
							$("#notes").val(value.notes);
							index++;
							// console.table(index);
						});
						openSuccessGritter('Success!', result.message);
					}else{
						// create_temp();
					}
				}
				else{
					openErrorGritter('Error!', result.message);
					audio_error.play();
					
				}
			});

			//FOOT
			var data = {
				array_middle : array_middlemf2,
				array_head_foot : array_foot2,
				remark : '{{$remark}}',
				product_type : $("#prod_type").text(),
				indexHeadFoot:indexFoot,
				check_type:check_type_foot
			}

			$.get('{{ url("index/push_block_recorder/get_temp_torque") }}',data,  function(result, status, xhr){
				if(result.status){
					if(result.datas.length != 0){
						index = 1;
						$.each(result.datas, function(key, value) {
							$('#torquemf_1_'+index).val(value.torque1);
							$('#torquemf_2_'+index).val(value.torque2);
							$('#torquemf_3_'+index).val(value.torque3);
							$('#averagemf_'+index).html(value.torqueavg);
							$('#judgementmf_'+index).html(value.judgement);
							$("#prod_type").html(value.product_type);
							$("#check_date").html(value.check_date);
							$('#injection_date_middle_fix').html(value.injection_date_middle);
							$('#injection_date_foot_fix').html(value.injection_date_head_foot);
							$('#mesin_middle').html(value.mesin_middle);
							$('#mesin_foot').html(value.mesin_head_foot);
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

		var check_type_head =  $("#check_type_head").val();
		var check_type_foot =  $("#check_type_foot").val();

		if ($('#product_fix2').text() == "YRF-21K//ID" || $('#product_fix2').text() == "YRF-21//ID" || $('#product_fix2').text() == "YRF-21 (FSA)" || $('#product_fix2').text() == "YRF21") {
			var array_middlehm = [];
			var array_head = [];

			var torquehm_1 = [];
			var torquehm_2 = [];
			var torquehm_3 = [];
			var averagehm = [];
			var judgementhm = [];

			indexHead = 2;
			indexFoot = 2;

			var indexhm = 1;
			for(var i=0;i<2;i++){
				for(var j=0;j<indexHead;j++){
					array_middlehm.push($('#middlehm_'+indexhm).text());
					array_head.push($('#head_'+indexhm).text());
					if ($('#averagehm_'+indexhm).text() == "") {
						averagehm.push(null);
					}else{
						averagehm.push(parseFloat($('#averagehm_'+indexhm).text()));
					}
					if ($('#torquehm_1_'+indexhm).val() == "") {
						torquehm_1.push(null);
					}else{
						torquehm_1.push(parseFloat($('#torquehm_1_'+indexhm).val()));
					}
					if ($('#torquehm_2_'+indexhm).val() == "") {
						torquehm_2.push(null);
					}else{
						torquehm_2.push(parseFloat($('#torquehm_2_'+indexhm).val()));
					}
					if ($('#torquehm_3_'+indexhm).val() == "") {
						torquehm_3.push(null);
					}else{
						torquehm_3.push(parseFloat($('#torquehm_3_'+indexhm).val()));
					}
					if ($('#judgementhm_'+indexhm).text() == "") {
						judgementhm.push(null);
					}else{
						judgementhm.push($('#judgementhm_'+indexhm).text());
					}
					indexhm++;
				}
			}

			//HEAD
			var data = {
				push_block_code : push_block_code,
				middle : array_middlehm,
				head_foot : array_head,
				check_type : check_type_head,
				torque_1 : torquehm_1,
				torque_2 : torquehm_2,
				torque_3 : torquehm_3,
				average : averagehm,
				judgement : judgementhm,
				notes:notes
			}
			$.post('{{ url("index/push_block_recorder/update_temp_torque") }}', data, function(result, status, xhr){
				if(result.status){
					// openSuccessGritter('Success', result.message);
				}
				else{
					// openErrorGritter('Error!', result.message);
				}
			});
		}else{
			var array_middlehm = [];
			var array_head = [];

			var array_middlemf = [];
			var array_foot = [];

			var torquehm_1 = [];
			var torquehm_2 = [];
			var torquehm_3 = [];
			var averagehm = [];
			var judgementhm = [];

			var torquemf_1 = [];
			var torquemf_2 = [];
			var torquemf_3 = [];
			var averagemf = [];
			var judgementmf = [];

			indexHead = 4;

			if ($('#foot_id').val() == 9 || $('#foot_id').val() == 10) {
				indexFoot = 6;
			}else{
				indexFoot = 4;
			}

			var indexhm = 1;
			for(var i=0;i<4;i++){
				for(var j=0;j<indexHead;j++){
					array_middlehm.push($('#middlehm_'+indexhm).text());
					array_head.push($('#head_'+indexhm).text());
					if ($('#averagehm_'+indexhm).text() == "") {
						averagehm.push(null);
					}else{
						averagehm.push(parseFloat($('#averagehm_'+indexhm).text()));
					}
					if ($('#torquehm_1_'+indexhm).val() == "") {
						torquehm_1.push(null);
					}else{
						torquehm_1.push(parseFloat($('#torquehm_1_'+indexhm).val()));
					}
					if ($('#torquehm_2_'+indexhm).val() == "") {
						torquehm_2.push(null);
					}else{
						torquehm_2.push(parseFloat($('#torquehm_2_'+indexhm).val()));
					}
					if ($('#torquehm_3_'+indexhm).val() == "") {
						torquehm_3.push(null);
					}else{
						torquehm_3.push(parseFloat($('#torquehm_3_'+indexhm).val()));
					}
					if ($('#judgementhm_'+indexhm).text() == "") {
						judgementhm.push(null);
					}else{
						judgementhm.push($('#judgementhm_'+indexhm).text());
					}
					indexhm++;
				}
			}

			var indexmf = 1;
			for(var i=0;i<4;i++){
				for(var j=0;j<indexFoot;j++){
					array_middlemf.push($('#middlemf_'+indexmf).text());
					array_foot.push($('#foot_'+indexmf).text());
					if ($('#averagemf_'+indexmf).text() == "") {
						averagemf.push(null);
					}else{
						averagemf.push(parseFloat($('#averagemf_'+indexmf).text()));
					}
					if ($('#torquemf_1_'+indexmf).val() == "") {
						torquemf_1.push(null);
					}else{
						torquemf_1.push(parseFloat($('#torquemf_1_'+indexmf).val()));
					}
					if ($('#torquemf_2_'+indexmf).val() == "") {
						torquemf_2.push(null);
					}else{
						torquemf_2.push(parseFloat($('#torquemf_2_'+indexmf).val()));
					}
					if ($('#torquemf_3_'+indexmf).val() == "") {
						torquemf_3.push(null);
					}else{
						torquemf_3.push(parseFloat($('#torquemf_3_'+indexmf).val()));
					}
					if ($('#judgementmf_'+indexmf).text() == "") {
						judgementmf.push(null);
					}else{
						judgementmf.push($('#judgementmf_'+indexmf).text());
					}
					indexmf++;
				}
			}

			//HEAD
			var data = {
				push_block_code : push_block_code,
				middle : array_middlehm,
				head_foot : array_head,
				check_type : check_type_head,
				torque_1 : torquehm_1,
				torque_2 : torquehm_2,
				torque_3 : torquehm_3,
				average : averagehm,
				judgement : judgementhm,
				notes:notes
			}
			$.post('{{ url("index/push_block_recorder/update_temp_torque") }}', data, function(result, status, xhr){
				if(result.status){
					// openSuccessGritter('Success', result.message);
				}
				else{
					// openErrorGritter('Error!', result.message);
				}
			});

			//FOOT
			var data = {
				push_block_code : push_block_code,
				middle : array_middlemf,
				head_foot : array_foot,
				check_type : check_type_foot,
				torque_1 : torquemf_1,
				torque_2 : torquemf_2,
				torque_3 : torquemf_3,
				average : averagemf,
				judgement : judgementmf,
				notes:notes
			}
			$.post('{{ url("index/push_block_recorder/update_temp_torque") }}', data, function(result, status, xhr){
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
		var check_type_head = $("#check_type_head").val();
		var check_type_foot = $("#check_type_foot").val();
		var injection_date_middle = $("#injection_date_middle_fix").text();
		var injection_date_head = $("#injection_date_head_fix").text();
		var injection_date_foot = $("#injection_date_foot_fix").text();
		var mesin_middle = $("#mesin_middle").text();
		var mesin_head = $("#mesin_head").text();
		var mesin_foot = $("#mesin_foot").text();
		var product_type = $("#prod_type").text();
		var pic_check = $("#pic_check").text();
		var push_block_code = '{{ $remark }}';

		if ($('#product_fix2').text() == "YRF-21K//ID" || $('#product_fix2').text() == "YRF-21//ID" || $('#product_fix2').text() == "YRF-21 (FSA)" || $('#product_fix2').text() == "YRF21") {
			var tag_head = $("#tag_head").val();
			var tag_middle = $("#tag_middle").val();

			var material_number_head = $("#material_number_head").val();
			var material_number_middle = $("#material_number_middle").val();

			var array_middlehm = [];
			var array_head = [];

			var torquehm_1 = [];
			var torquehm_2 = [];
			var torquehm_3 = [];
			var averagehm = [];
			var judgementhm = [];

			var status_falsehm = 0;

			var indexHead = 2;
			var indexFoot = 2;

			var indexhm = 1;
			for(var i=0;i<2;i++){
				for(var j=0;j<indexHead;j++){
					array_middlehm.push($('#middlehm_'+indexhm).text());
					array_head.push($('#head_'+indexhm).text());
					torquehm_1.push(parseFloat($('#torquehm_1_'+indexhm).val()));
					torquehm_2.push(parseFloat($('#torquehm_2_'+indexhm).val()));
					torquehm_3.push(parseFloat($('#torquehm_3_'+indexhm).val()));
					if ($('#averagehm_'+indexhm).text() == "") {
						averagehm.push(parseFloat(0));
					}else{
						averagehm.push(parseFloat($('#averagehm_'+indexhm).text()));
					}
					if ($('#judgementhm_'+indexhm).text() == "") {
						judgementhm.push(parseFloat(0));
					}else{
						judgementhm.push($('#judgementhm_'+indexhm).text());
					}
					if ($('#torquehm_1_'+indexhm).val() == "" || $('#torquehm_2_'+indexhm).val() == "" || $('#torquehm_3_'+indexhm).val() == "" || $('#averagehm_'+indexhm).text() == ""|| $('#judgementhm_'+indexhm).text() == "") {
						status_falsehm++;
					}
					indexhm++;
				}
			}

			if(status_falsehm > 0){
				alert('Semua Data Harus Diisi');
			}
			else{
				$('#selesai_button').prop('disabled', true);

				$('#loading').show();

				//HEAD
				var data = {
					push_block_code : push_block_code,
					check_date : check_date,
					check_type : check_type_head,
					injection_date_middle : injection_date_middle,
					injection_date_head_foot : injection_date_head,
					mesin_middle : mesin_middle,
					mesin_head_foot : mesin_head,
					pic_check : pic_check,
					product_type : product_type,
					middle : array_middlehm,
					head_foot : array_head,
					torque_1 : torquehm_1,
					torque_2 : torquehm_2,
					torque_3 : torquehm_3,
					average : averagehm,
					judgement : judgementhm,
					notes:notes,
					tag_head:tag_head,
					tag_middle:tag_middle,
					tag_foot:tag_foot,
					material_number_head:material_number_head,
					material_number_middle:material_number_middle,
					material_number_foot:material_number_foot,
				}
				$.post('{{ url("index/push_block_recorder/create_torque") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success', result.message);
						$('#loading').hide();
						alert('Pengisian Selesai. Tutup halaman ini. Jika akan melakukan Return Material, silahkan ke menu Return Material.');
						location.reload();
					}
					else{
						openErrorGritter('Error!', result.message);
					}
				});
			}
		}else{
			var tag_head = $("#tag_head").val();
			var tag_middle = $("#tag_middle").val();
			var tag_foot = $("#tag_foot").val();

			var material_number_head = $("#material_number_head").val();
			var material_number_middle = $("#material_number_middle").val();
			var material_number_foot = $("#material_number_foot").val();

			var array_middlehm = [];
			var array_middlemf = [];
			var array_head = [];
			var array_foot = [];

			var torquehm_1 = [];
			var torquehm_2 = [];
			var torquehm_3 = [];
			var averagehm = [];
			var judgementhm = [];

			var torquemf_1 = [];
			var torquemf_2 = [];
			var torquemf_3 = [];
			var averagemf = [];
			var judgementmf = [];

			var status_falsehm = 0;
			var status_falsemf = 0;

			var indexHead = 4;

			if ($('#foot_id').val() == 9 || $('#foot_id').val() == 10) {
				var indexFoot = 6;
			}else{
				var indexFoot = 4;
			}

			var indexhm = 1;
			for(var i=0;i<4;i++){
				for(var j=0;j<indexHead;j++){
					array_middlehm.push($('#middlehm_'+indexhm).text());
					array_head.push($('#head_'+indexhm).text());
					torquehm_1.push(parseFloat($('#torquehm_1_'+indexhm).val()));
					torquehm_2.push(parseFloat($('#torquehm_2_'+indexhm).val()));
					torquehm_3.push(parseFloat($('#torquehm_3_'+indexhm).val()));
					if ($('#averagehm_'+indexhm).text() == "") {
						averagehm.push(parseFloat(0));
					}else{
						averagehm.push(parseFloat($('#averagehm_'+indexhm).text()));
					}
					if ($('#judgementhm_'+indexhm).text() == "") {
						judgementhm.push(parseFloat(0));
					}else{
						judgementhm.push($('#judgementhm_'+indexhm).text());
					}
					if ($('#torquehm_1_'+indexhm).val() == "" || $('#torquehm_2_'+indexhm).val() == "" || $('#torquehm_3_'+indexhm).val() == "" || $('#averagehm_'+indexhm).text() == ""|| $('#judgementhm_'+indexhm).text() == "") {
						status_falsehm++;
					}
					indexhm++;
				}
			}

			var indexmf = 1;
			for(var i=0;i<4;i++){
				for(var j=0;j<indexFoot;j++){
					array_middlemf.push($('#middlemf_'+indexmf).text());
					array_foot.push($('#foot_'+indexmf).text());
					torquemf_1.push(parseFloat($('#torquemf_1_'+indexmf).val()));
					torquemf_2.push(parseFloat($('#torquemf_2_'+indexmf).val()));
					torquemf_3.push(parseFloat($('#torquemf_3_'+indexmf).val()));
					if ($('#averagemf_'+indexmf).text() == "") {
						averagemf.push(parseFloat(0));
					}else{
						averagemf.push(parseFloat($('#averagemf_'+indexmf).text()));
					}
					if ($('#judgementmf_'+indexmf).text() == "") {
						judgementmf.push(parseFloat(0));
					}else{
						judgementmf.push($('#judgementmf_'+indexmf).text());
					}
					if ($('#torquemf_1_'+indexmf).val() == "" || $('#torquemf_2_'+indexmf).val() == "" || $('#torquemf_3_'+indexmf).val() == "" || $('#averagemf_'+indexmf).text() == ""|| $('#judgementmf_'+indexmf).text() == "") {
						status_falsemf++;
					}
					indexmf++;
				}
			}

			if(status_falsehm > 0 || status_falsemf > 0){
				alert('Semua Data Harus Diisi');
			}
			else{
				$('#selesai_button').prop('disabled', true);

				$('#loading').show();

				//HEAD
				var data = {
					push_block_code : push_block_code,
					check_date : check_date,
					check_type : check_type_head,
					injection_date_middle : injection_date_middle,
					injection_date_head_foot : injection_date_head,
					mesin_middle : mesin_middle,
					mesin_head_foot : mesin_head,
					pic_check : pic_check,
					product_type : product_type,
					middle : array_middlehm,
					head_foot : array_head,
					torque_1 : torquehm_1,
					torque_2 : torquehm_2,
					torque_3 : torquehm_3,
					average : averagehm,
					judgement : judgementhm,
					notes:notes,
					tag_head:tag_head,
					tag_middle:tag_middle,
					tag_foot:tag_foot,
					material_number_head:material_number_head,
					material_number_middle:material_number_middle,
					material_number_foot:material_number_foot,
				}
				$.post('{{ url("index/push_block_recorder/create_torque") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success', result.message);
						// alert('Pengisian Selesai.');
						// location.reload();
					}
					else{
						openErrorGritter('Error!', result.message);
					}
				});

				//FOOT
				var data2 = {
					push_block_code : push_block_code,
					check_date : check_date,
					check_type : check_type_foot,
					injection_date_middle : injection_date_middle,
					injection_date_head_foot : injection_date_foot,
					mesin_middle : mesin_middle,
					mesin_head_foot : mesin_foot,
					pic_check : pic_check,
					product_type : product_type,
					middle : array_middlemf,
					head_foot : array_foot,
					torque_1 : torquemf_1,
					torque_2 : torquemf_2,
					torque_3 : torquemf_3,
					average : averagemf,
					judgement : judgementmf,
					notes:notes,
					tag_head:tag_head,
					tag_middle:tag_middle,
					tag_foot:tag_foot,
					material_number_head:material_number_head,
					material_number_middle:material_number_middle,
					material_number_foot:material_number_foot,
				}
				$.post('{{ url("index/push_block_recorder/create_torque") }}', data2, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success', result.message);
						$('#loading').hide();
						alert('Pengisian Selesai. Tutup halaman ini. Jika akan melakukan Return Material, silahkan ke menu Return Material.');
						location.reload();
					}
					else{
						openErrorGritter('Error!', result.message);
					}
				});
			}
		}
	}

	function torquehm(id) {
		var batas_bawah_hm = '{{$batas_bawah_hm}}';
		var batas_atas_hm = '{{$batas_atas_hm}}';
		var batas_bawah_mf = '{{$batas_bawah_mf}}';
		var batas_atas_mf ='{{$batas_atas_mf}}';

		if (id.length > 12) {
			torques = id.substr(id.length - 2);
		}else{
			torques = id.substr(id.length - 1);
		}
		var tr1 = 'torquehm_1_'+torques;
		var tr2 = 'torquehm_2_'+torques;
		var tr3 = 'torquehm_3_'+torques;
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
		var avg_id = '#averagehm_'+torques;
		var avg_id2 = 'averagehm_'+torques;
		var judgement_id = '#judgementhm_'+torques;
		var judgement_id2 = 'judgementhm_'+torques;
		if (parseFloat(avg) < parseFloat(batas_bawah_hm) || parseFloat(avg) > parseFloat(batas_atas_hm)) {
			document.getElementById(judgement_id2).style.backgroundColor = "#ff4f4f"; //red
			$(judgement_id).html('NG');
		}else{
			document.getElementById(judgement_id2).style.backgroundColor = "#7fff6e"; //green
			$(judgement_id).html('OK');
		}
		$(avg_id).html(avg.toFixed(2));
	}

	function itemresumehead(middle_id,head_foot_id){
		var data = {
			middle_id : middle_id,
			head_foot_id : head_foot_id,
		}
		$.get('{{ url("index/fetchResumeTorqueAi") }}', data, function(result, status, xhr){
			$('#tableResumeHead').DataTable().clear();
			$('#tableResumeHead').DataTable().destroy();
			$('#tableBodyResumeHead').html("");
			var tableData = "";
			if ($('#product_fix2').text() == "YRF-21K//ID" || $('#product_fix2').text() == "YRF-21//ID" || $('#product_fix2').text() == "YRF-21 (FSA)" || $('#product_fix2').text() == "YRF21") {
				indexHeadFoot = 2;
			}else{
				if (result.detail_head_foot.cavity_5 == null) {
					indexHeadFoot = 4;
				}else{
					indexHeadFoot = 6;
				}
			}
			var array_middle = Object.values(result.cav_middle);
			var array_head_foot = Object.values(result.cav_head_foot);
			var index = 1;
			if ($('#product_fix2').text() == 'YRF-21K//ID' || $('#product_fix2').text() == 'YRF-21//ID' || $('#product_fix2').text() == "YRF-21 (FSA)" || $('#product_fix2').text() == "YRF21") {
				for(var i=0;i<2;i++){
					for(var j=0;j<indexHeadFoot;j++){
						tableData += '<tr>';
						tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>'+index+'</b></td>';	
						tableData += '<td style="text-align:center;" id="middlehm_'+index+'">'+ array_middle[i] +'</td>';
						tableData += '<td style="text-align:center;" id="head_'+index+'">'+ array_head_foot[j] +'</td>';
						tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="torquehm(this.id)" class="form-control" id="torquehm_1_'+index+'"></td>';
						tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="torquehm(this.id)" class="form-control" id="torquehm_2_'+index+'"></td>';
						tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="torquehm(this.id)" class="form-control" id="torquehm_3_'+index+'"></td>';
						tableData += '<td style="text-align:center;" id="averagehm_'+index+'"></td>';
						tableData += '<td style="text-align:center;" id="judgementhm_'+index+'"></td>';
						tableData += '</tr>';
						index++;
					}
				}
			}
			else{
				for(var i=0;i<4;i++){
					for(var j=0;j<indexHeadFoot;j++){
						tableData += '<tr>';
						tableData += '<td style="text-align:right;background-color:#605ca8;color:white"><b>'+index+'</b></td>';	
						tableData += '<td style="text-align:center;" id="middlehm_'+index+'">'+ array_middle[i] +'</td>';
						tableData += '<td style="text-align:center;" id="head_'+index+'">'+ array_head_foot[j] +'</td>';
						tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="torquehm(this.id)" class="form-control" id="torquehm_1_'+index+'"></td>';
						tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="torquehm(this.id)" class="form-control" id="torquehm_2_'+index+'"></td>';
						tableData += '<td style="padding:0;text-align:right"><input type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="torquehm(this.id)" class="form-control" id="torquehm_3_'+index+'"></td>';
						tableData += '<td style="text-align:center;" id="averagehm_'+index+'"></td>';
						tableData += '<td style="text-align:center;" id="judgementhm_'+index+'"></td>';
						tableData += '</tr>';
						index++;
					}
				}
			}
			$('#tableBodyResumeHead').append(tableData);
		});
	}

	function torquemf(id) {
		var batas_bawah_hm = '{{$batas_bawah_hm}}';
		var batas_atas_hm = '{{$batas_atas_hm}}';
		var batas_bawah_mf = '{{$batas_bawah_mf}}';
		var batas_atas_mf ='{{$batas_atas_mf}}';

		if (id.length > 12) {
			torques = id.substr(id.length - 2);
		}else{
			torques = id.substr(id.length - 1);
		}
		var tr1 = 'torquemf_1_'+torques;
		var tr2 = 'torquemf_2_'+torques;
		var tr3 = 'torquemf_3_'+torques;
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
		var avg_id = '#averagemf_'+torques;
		var avg_id2 = 'averagemf_'+torques;
		var judgement_id = '#judgementmf_'+torques;
		var judgement_id2 = 'judgementmf_'+torques;
		if (parseFloat(avg) < parseFloat(batas_bawah_mf) || parseFloat(avg) > parseFloat(batas_atas_mf)) {
			document.getElementById(judgement_id2).style.backgroundColor = "#ff4f4f"; //red
			$(judgement_id).html('NG');
		}else{
			document.getElementById(judgement_id2).style.backgroundColor = "#7fff6e"; //green
			$(judgement_id).html('OK');
		}
		$(avg_id).html(avg.toFixed(2));
	}

	function itemresumefoot(middle_id,head_foot_id){
		var data = {
			middle_id : middle_id,
			head_foot_id : head_foot_id,
		}
		$.get('{{ url("index/fetchResumeTorqueAi") }}', data, function(result, status, xhr){
			$('#tableResumeFoot').DataTable().clear();
			$('#tableResumeFoot').DataTable().destroy();
			$('#tableBodyResumeFoot').html("");
			var tableData = "";
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
					tableData += '<td style="text-align:center;" id="middlemf_'+index+'">'+ array_middle[i] +'</td>';
					tableData += '<td style="text-align:center;" id="foot_'+index+'">'+ array_head_foot[j] +'</td>';
					tableData += '<td style="padding:0;text-align:right"><input value="" type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="torquemf(this.id)" class="form-control" id="torquemf_1_'+index+'"></td>';
					tableData += '<td style="padding:0;text-align:right"><input value="" type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="torquemf(this.id)" class="form-control" id="torquemf_2_'+index+'"></td>';
					tableData += '<td style="padding:0;text-align:right"><input value="" type="number" style="font-size: 15px; height: 100%; text-align: center;" onkeyup="torquemf(this.id)" class="form-control" id="torquemf_3_'+index+'"></td>';
					tableData += '<td style="text-align:center;" id="averagemf_'+index+'"></td>';
					tableData += '<td style="text-align:center;" id="judgementmf_'+index+'"></td>';
					tableData += '</tr>';
					index++;
				}
			}
			$('#tableBodyResumeFoot').append(tableData);
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