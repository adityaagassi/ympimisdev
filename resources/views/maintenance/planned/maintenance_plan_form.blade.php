@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:center;
	}
	tbody>tr>th{
		text-align:center;
		background-color: #dcdcdc;
		border: 1px solid black !important;
		font-weight: bold;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
		color: white;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding-top: 9px;
		padding-bottom: 9px;
		vertical-align: middle;
		color: yellow;
		/*background-color: white;*/
	}
	thead {
		background-color: rgb(126,86,134);
	}

	.nmpd-grid {border: none; padding: 20px;}
	.nmpd-grid>tbody>tr>td {border: none;}

	/* Chrome, Safari, Edge, Opera */
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}

	/* Firefox */
	input[type=number] {
		-moz-appearance: textfield;
		font-weight: bold;
		font-size: 20px;
	}

	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<input type="hidden" id="green">
	<h1>
		{{ $page }}
	</h1>
</section>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">	
	<div class="row" style="margin-left: 1%; margin-right: 1%;">
		<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
			<p style="position: absolute; color: White; top: 45%; left: 35%;">
				<span style="font-size: 40px">Loading, mohon tunggu..<i class="fa fa-spin fa-refresh"></i></span>
			</p>
		</div>

		<div class="col-xs-12" style="padding-right: 0; padding-left: 0;">
			<table class="table table-bordered" style="width: 100%; margin-bottom: 2%;">
				<thead>
					<tr>
						<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 18px;" colspan="2">Informasi Umum</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;" id="op"></td>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 20px;" id="op2"></td>
					</tr>

					<tr>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">Item Cek</td>
						<td style="background-color: rgb(204,255,255); text-align: center; color: #000000;">
							<select class="select2" data-placeholder="Pilih Kategori Mesin" style="width: 20%" id="item_cat" onchange="getMachineByCat(this)">
								<option value=""></option>
							</select>

							<select class="select2" data-placeholder="Pilih Item Cek" style="width: 70%" id="item_check" onchange="get_period(this)">
								<option value=""></option>
							</select>
						</td>
					</tr>

					<tr>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">Periode Cek</td>
						<td style="background-color: rgb(204,255,255); text-align: center; color: #000000;">
							<select class="select2" data-placeholder="Pilih Periode Cek" style="width: 20%" id="cek_period" onchange="check_change(this)">
								<option value=""></option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>

			<br>
	<!-- 		<table class="table table-bordered" style="width: 100%; margin-bottom: 2%;">
				<thead>
					<tr>
						<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 18px;">Kategori Planned</th>
						<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 18px;">Nama Item</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<select class="select2 form-control" id="category" onchange="get_item(this)">
								<option></option>
								<option value="utility" >Utility</option>
								<option value="machine">Machine (MP)</option>
							</select>
						</td>
						<td>
							<select class="select2 form-control" id="item_name">
							</select>
						</td>
					</tr>
					<tr>
						<td><label>Tanggal&nbsp; : &nbsp;</label><label><?php echo date("d F Y"); ?></label></td>
						<td><label>Aktual&nbsp; : &nbsp;</label><center><input type="text" class="form-control" placeholder="Jumlah Pengecekan" style="width: 30%" id="quantity"></center></td>
					</tr>
				</tbody>
			</table>

			<button class="btn btn-success pull-right" onclick="save()"><i class="fa fa-check"></i> Save</button> -->

		</div>

		<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
			<table class="table table-bordered" style="width: 100%; margin-bottom: 0px">
				<thead>
					<tr>
						<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 18px;" colspan="5">Daftar Cek</th>
					</tr>
					<tr>
						<th>ITEM CHECK</th>
						<th>SUBSTANCE</th>
						<th>REMARK</th>
						<th colspan="2">AKSI</th>
					</tr>
				</thead>
				<tbody id="body_check_list">
				</tbody>
			</table>
			<br>
			<button class="btn btn-success" style="width: 100%; display: none" id="btn_check" onclick="check()"><i class="fa fa-check"></i> CHECKED</button>
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
						<label for="exampleInputEmail1">Operator</label>
						<select class="form-control" id="operator" style="width: 100%; text-align: center;" onchange="getval(this)" data-placeholder="Pilih Operator">
							@foreach($mtc_op as $mtc)
							<option value="{{ $mtc->employee_id }}">{{ $mtc->name }}</option>
							@endforeach
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/jquery.numpad.js") }}"></script>

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	var arr_item = [];
	var item_ctg = [];
	var machine_check_list = [];

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('#operator').val('');

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		$('.select2').select2();
		$('#operator').select2();

		arr_item = <?php echo json_encode($item_check); ?>;
		console.log(arr_item);

		get_cat();
	})

	function get_cat() {
		tmp_arr = [];
		tmp_var = "";

		tmp_var += "<option value=''></option>";

		$.each(arr_item, function(index, value){
			if(tmp_arr.indexOf(value.category) === -1){
				tmp_arr[tmp_arr.length] = value.category;

				tmp_var += "<option value='"+value.category+"'>"+value.category+"</option>";
			}
		})

		$("#item_cat").append(tmp_var);
	}

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#operator').focus();
	});

	function getval(elem) {
		$('#modalOperator').modal('hide');
		$('#op').text(elem.value);
		$('#op2').text($("#operator option:selected").text());
	}

	function getMachineByCat(elem) {
		$("#item_check").empty();

		var item_op1 = "";
		item_op1 += "<option value=''></option>";

		$.each(arr_item, function(index, value){
			if (value.category == $("#item_cat").val()) {
				item_op1 += "<option value='"+value.machine_name+"'>"+value.machine_id+" - "+value.description+" - "+value.area+"</option>";
			}
		})

		item_op1 += "<option></option>";


		$("#item_check").append(item_op1);
	}

	function get_period(elem) {
		var data = {
			item_no : $(elem).val()
		};

		$.get('{{ url("fetch/maintenance/plan/checkList") }}', data, function(result, status, xhr) {
			var period = [];
			var prd = "";
			var item = "";
			$("#cek_period").empty();

			machine_check_list = [];

			for (var i = 0; i < result.datas.length; i++) {
				prd = result.datas[i].remark;

				machine_check_list.push(result.datas[i]);
				if(period.indexOf(prd) === -1){
					period[period.length] = prd;
				}
			}

			item += "<option value='' data-placeholder='pilih periode check'></option>";

			$.each(period, function(index, value){
				item += "<option value='"+value+"'>"+value+"</option>";
			})

			$("#cek_period").append(item);
		})

	}

	function check_change(elem) {
		var data = {
			item_no : $("#item_check").val(),
		};

		var period = $(elem).val();
		$("#body_check_list").empty();
		var body = "";

		$.each(machine_check_list, function(index, value){
			if (value.remark == period) {
				body += "<tr>";
				body += "<td id='item_"+value.id+"'>"+value.item_check+"</td>";				
				body += "<td id='substance_"+value.id+"'>"+value.substance+"</td>";
				body += "<td>"+value.remark+"</td>";
				body += "<td style='padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 20px;'>";
				if (value.essay_category == "1") {
					body += '<input id="qty_'+value.id+'" style="text-align: center;" type="number" class="form-control numpad" placeholder="value" onchange="fill_value(this)"><span style="display:none" id="min_'+value.id+'">'+value.lower_limit+'</span><span style="display:none" id="max_'+value.id+'">'+value.upper_limit+'</span><input type="checkbox" style="display:none" class="check" id="check_'+value.id+'">';
				} else {
					body += "<div class='radio'><label><input type='radio' class='check' name='nm_"+value.id+"' id='check_"+value.id+"'>OK</label></div></td>";
				}

				body += "<td style='padding: 0px; background-color: #ffccff; text-align: center; color: #000000; font-size: 20px;'><div class='radio'><label><input type='radio' class='check' name='nm_"+value.id+"' id='ng_"+value.id+"'>NG</label></div></td>";

				body += "</tr>";
			}
		})

		$("#body_check_list").append(body);

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		$("#btn_check").show();
	}

	// ===========================================================================

	function get_item(elem) {
		var opsi = "";
		$("#item_name").empty();

		$.each(arr_item, function(index, value){
			if ($(elem).val() == value.category) {
				opsi += "<option value='"+value.id+"'>"+value.item_check+"</option>";
			}
		})

		$("#item_name").append(opsi);
	}

	// function save() {
	// 	var data = {
	// 		plan_id : $("#item_name").val(),
	// 		qty : $("#quantity").val()
	// 	}

	// 	$.post('{{ url("post/maintenance/pm/daily") }}', data, function(result, status, xhr) {
	// 		if (result.status) {
	// 			$("#quantity").val("");
	// 			openSuccessGritter("Success", "Daily Planned Has Been Updated");
	// 		} else {
	// 			openErrorGritter("Error", result.message);
	// 		}
	// 	})
	// }

	function fill_value(elem) {
		var ido = $(elem).attr('id');
		ido = ido.split('_')[1];

		if ($("#min_"+ido).text() != "null" && $("#max_"+ido).text() != "null") {
			if ($(elem).val() >= parseInt($("#min_"+ido).text()) && $(elem).val() <= parseInt($("#max_"+ido).text())) {
				//TRUE
				$("#check_"+ido).attr('checked','true');
				console.log('1');
			} else {
				$("#check_"+ido).removeAttr('checked');
				console.log('0');
			}
		} else {
			$("#check_"+ido).attr('checked','true');
		}
	}

	function check() {
		var cek = 0;
		$(".numpad").each(function() {
			if ($(this).val() == "") {
				cek = 1;
				return false;
			}
		});

		if (cek == 1) {
			openErrorGritter('Gagal', 'Terdapat Kolom Kosong.');
		} else {

			var arr_params = [];
			$(".check").each(function() {
				var ids = $(this).attr('id');
				ids = ids.split('_')[1];
				if ($(this).is(':checked')) {
					cek = 1;
				} else {
					cek = 0;
				}

				val = ( $("#qty_"+ids).val() || '-');
				arr_params.push([$("#item_"+ids).text(), $("#substance_"+ids).text(), $("#cek_period").val(), cek, val]);
			});

			var data = {
				item_check : $("#item_check").val(),
				check_list : arr_params,
				operator : $("#op").text()
			}
			$("#loading").show();

			$.post('{{ url("post/maintenance/pm/check") }}', data, function(result, status, xhr) {
				$("#loading").hide();
				if (result.status) {
					openSuccessGritter('Success', 'Cek Berhasil');

					$("#body_check_list").empty();
					$("#cek_period").val("").trigger('change.select2');
					$("#btn_check").hide();
				} else {
					openErrorGritter('Error', result.message);
				}
			})
		}
	}

	function unique(list) {
		var result = [];
		$.each(list, function(i, e) {
			if ($.inArray(e, result) == -1) result.push(e);
		});
		return result;
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