@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
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
	background-color: white;
}
table.table-bordered > thead > tr > th{
	border:1px solid black;
}
table.table-bordered > tbody > tr > td{
	border:1px solid rgb(211,211,211);
	padding-top: 0px;
	padding-bottom: 0px;
}
table.table-bordered > tfoot > tr > th{
	border:1px solid rgb(211,211,211);
}
#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header" >
	<h1>
		{{ $page }}<span class="text-purple"> {{ $title_jp }}</span>
	</h1>
</section>
@stop
@section('content')
<section class="content" >
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-5" style="text-align: center;margin-left: 0px">
			<div class="col-xs-12">
				<div class="row">
					<div class="col-xs-12">
						<div class="row">
							<center><span style="font-weight: bold; font-size: 18px; color: white">Pilih Produk</span></center>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="row">
							@foreach($product_type as $product_type)
							<div class="col-xs-4" style="padding-top: 5px">
								<center><button class="btn btn-primary" id="{{$product_type}}" style="width: 170px;font-size: 15px" onclick="getProduct(this.id)">
									{{$product_type}}
								</button></center>
							</div>
				            @endforeach
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-7">
			<div class="col-xs-12">
				<div class="row">
					<center><span style="font-weight: bold;font-size: 15px;color: white;">Produk</span></center>
					<input type="text" disabled id="product" value="RC" style="width: 100%;font-size: 30px;text-align: center;">
				</div>
			</div>
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
			<div class="col-xs-12">
				<div class="row">
					<button class="btn btn-success" style="font-weight: bold;font-size: 30px;width: 100%;margin-top: 15px" onclick="initialize()">
						INISIALISASI
					</button>
				</div>
			</div>
		</div>
		<div class="col-xs-12" style="padding-top: 15px">
			<div class="box box-solid">
				<div class="box-body">
					<center><span style="font-size: 25px;text-align: center;font-weight: bold;">INITIALIZE HISTORY</span> </center>
					<table id="tableHistory" class="table table-bordered table-striped table-hover" style="width: 100%">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%">Product</th>
								<th style="width: 2%">Material</th>
								<th style="width: 2%">Part</th>
								<th style="width: 1%">Loc</th>
								<th style="width: 1%">Status</th>
								<th style="width: 2%">At</th>
							</tr>
						</thead>
						<tbody id="tableHistoryBody">
						</tbody>
						<tfoot style="background-color: RGB(252, 248, 227);">
							<tr>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>

</section>
@endsection
@section('scripts')
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script src="{{ url('js/dataTables.buttons.min.js')}}"></script>
<script src="{{ url('js/buttons.flash.min.js')}}"></script>
<script src="{{ url('js/jszip.min.js')}}"></script>
<script src="{{ url('js/vfs_fonts.js')}}"></script>
<script src="{{ url('js/buttons.html5.min.js')}}"></script>
<script src="{{ url('js/buttons.print.min.js')}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
      $('body').toggleClass("sidebar-collapse");
      reset();
      fetchHistory();
	});

	function getProduct(product) {
		$('#product').val(product);
		resetProduct();
		if (product.match(/YRS/gi)) {
			$('#divyrs').show();
			$('#divyrf').hide();

			$('#tag_head').removeAttr('disabled');
			$('#tag_middle').removeAttr('disabled');
			$('#tag_foot').removeAttr('disabled');
			$('#tag_block').removeAttr('disabled');

			$('#tag_head').focus();
		}
		if(product.match(/YRF/gi)){
			$('#divyrs').hide();
			$('#divyrf').show();

			$('#tag_head_yrf').removeAttr('disabled');
			$('#tag_body_yrf').removeAttr('disabled');
			$('#tag_stopper_yrf').removeAttr('disabled');

			$('#tag_head_yrf').focus();
		}
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

	function reset() {
		$('#product').val('RC');
		$('#tag_head').val('');
		$('#tag_middle').val('');
		$('#tag_foot').val('');
		$('#tag_block').val('');
		$('#divyrs').hide();
		$('#divyrf').hide();

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

	$('#tag_head').keyup(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag_head").val().length == 10){
				var data = {
					tag : $("#tag_head").val(),
					type : 'head'
				}
				$.get('{{ url("scan/recorder/kensa_initial") }}', data, function(result, status, xhr){
					if(result.status){
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
				$.get('{{ url("scan/recorder/kensa_initial") }}', data, function(result, status, xhr){
					if(result.status){
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
				$.get('{{ url("scan/recorder/kensa_initial") }}', data, function(result, status, xhr){
					if(result.status){
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
				$.get('{{ url("scan/recorder/kensa_initial") }}', data, function(result, status, xhr){
					if(result.status){
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
				$.get('{{ url("scan/recorder/kensa_initial") }}', data, function(result, status, xhr){
					if(result.status){
						$('#material_number_head_yrf').text(result.tag.material_number);
						$('#part_name_head_yrf').text(result.tag.part_name);
						$('#part_type_head_yrf').text(result.tag.part_type);
						$('#color_head_yrf').text(result.tag.color);
						$('#cavity_head_yrf').text(result.tag.cavity);
						$('#location_head_yrf').text(result.tag.location);
						$('#tag_head_yrf').prop('disabled',true);
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
				$.get('{{ url("scan/recorder/kensa_initial") }}', data, function(result, status, xhr){
					if(result.status){
						$('#material_number_body_yrf').text(result.tag.material_number);
						$('#part_name_body_yrf').text(result.tag.part_name);
						$('#part_type_body_yrf').text(result.tag.part_type);
						$('#color_body_yrf').text(result.tag.color);
						$('#cavity_body_yrf').text(result.tag.cavity);
						$('#location_body_yrf').text(result.tag.location);
						$('#tag_body_yrf').prop('disabled',true);
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
				$.get('{{ url("scan/recorder/kensa_initial") }}', data, function(result, status, xhr){
					if(result.status){
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

	function initialize() {
		$('#loading').show();
		var product = $('#product').val();
		if (product.match(/YRS/gi)) {
			if ($('#tag_head').val() == '' || $('#tag_middle').val() == '' || $('#tag_foot').val() == '' || $('#tag_block').val() == '') {
				alert('Semua Data Harus Diisi.');
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
				}
			}
		}
		if(product.match(/YRF/gi)){
			if ($('#tag_head_yrf').val() == "" || $('#tag_body_yrf').val() == "" || $('#tag_stopper_yrf').val() == "") {
				alert('Semua Data Harus Diisi');
			}else{
				var tag_head_yrf = $('#tag_head_yrf').val();
				var tag_body_yrf = $('#tag_body_yrf').val();
				var tag_stopper_yrf = $('#tag_stopper_yrf').val();

				var material_number_head_yrf = $('#material_number_head_yrf').text("");
				var part_name_head_yrf = $('#part_name_head_yrf').text("");
				var part_type_head_yrf = $('#part_type_head_yrf').text("");
				var color_head_yrf = $('#color_head_yrf').text("");
				var cavity_head_yrf = $('#cavity_head_yrf').text("");
				var location_head_yrf = $('#location_head_yrf').text("");

				var material_number_body_yrf = $('#material_number_body_yrf').text("");
				var part_name_body_yrf = $('#part_name_body_yrf').text("");
				var part_type_body_yrf = $('#part_type_body_yrf').text("");
				var color_body_yrf = $('#color_body_yrf').text("");
				var cavity_body_yrf = $('#cavity_body_yrf').text("");
				var location_body_yrf = $('#location_body_yrf').text("");

				var material_number_stopper_yrf = $('#material_number_stopper_yrf').text("");
				var part_name_stopper_yrf = $('#part_name_stopper_yrf').text("");
				var part_type_stopper_yrf = $('#part_type_stopper_yrf').text("");
				var color_stopper_yrf = $('#color_stopper_yrf').text("");
				var cavity_stopper_yrf = $('#cavity_stopper_yrf').text("");
				var location_stopper_yrf = $('#location_stopper_yrf').text("");

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
				}
			}
		}

		$.post('{{ url("input/recorder/kensa_initial") }}', data, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				reset();
				fetchHistory();
				openSuccessGritter('Success','Inisialisasi Kensa Berhasil');
			}
			else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error', result.message);
			}
		});
	}

	function fetchHistory() {
		$.get('{{ url("fetch/recorder/kensa_initial") }}', function(result, status, xhr){
			if(result.status){
				$('#tableHistory').DataTable().clear();
				$('#tableHistory').DataTable().destroy();
				$('#tableHistoryBody').html("");
				var tableData = "";
				if (result.datas.length > 0) {
					$.each(result.datas, function(key, value) {
						tableData += '<tr>';
						tableData += '<td>'+ value.product +'</td>';
						tableData += '<td>'+ value.material_number +'<br>'+value.mat_desc+'</td>';
						tableData += '<td>'+ value.part_kensa +'<br>'+ value.typepart +' - '+ value.color +'<br>'+ value.cavity +'</td>';
						tableData += '<td>'+ value.location +'</td>';
						tableData += '<td>'+ value.status +'</td>';
						tableData += '<td>'+ value.kensa_created +'</td>';
						tableData += '</tr>';
					});
				}
				$('#tableHistoryBody').append(tableData);

				$('#tableHistory tfoot th').each(function(){
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				});
				
				var table = $('#tableHistory').DataTable({
					'dom': 'Bfrtip',
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
						}
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching'   	: true,
					'ordering'		: true,
					'order': [],
					'info'       	: true,
					'autoWidth'		: false,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
				});
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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
</script>
@endsection