@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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
	#master {
		font-size: 17px;
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
		background-color: white;
	}
	thead {
		background-color: rgb(126,86,134);
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	#loading, #error { display: none; }

	.kedip {
		/*width: 50px;
		height: 50px;*/
		-webkit-animation: pulse 1s infinite;  /* Safari 4+ */
		-moz-animation: pulse 1s infinite;  /* Fx 5+ */
		-o-animation: pulse 1s infinite;  /* Opera 12+ */
		animation: pulse 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes pulse {
		0%, 49% {
			background-color: #00a65a;
			color: white;
		}
		50%, 100% {
			background-color: #ffffff;
			color: #444;
		}
	}

	.foto {
		opacity: 0;
		/*position: absolute;*/
		/*display: none;*/
		visibility: hidden;
		z-index: -1;
	}

	.txt_foto:hover, .txt_reset:hover {
		cursor: pointer;
	}

	/* Chrome, Safari, Edge, Opera */
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}

	/* Firefox */
	input[type=number] {
		-moz-appearance: textfield;
	}

</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<input type="hidden" id="order_no">
	<input type="hidden" id="operator_id" value="{{ $employee_id }}">
	<div class="row" style="margin-left: 1%; margin-right: 1%;">
		<div class="col-xs-12" style="padding-right: 0; padding-left: 0;">
			<table class="table table-bordered" style="width: 100%; margin-bottom: 2%;">
				<thead>
					<tr>
						<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 18px;" colspan="2">Operator</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;" id="op">{{ $employee_id }}</td>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 20px;" id="op2">{{ $name }}</td>
					</tr>
				</tbody>
			</table>

			<table class="table table-bordered" style="width: 100%" id="table_master">
				<thead>
					<tr>
						<th width="5%">Nomor SPK</th>
						<th width="25%">Bagian</th>
						<th width="5%">Jenis Pekerjaan</th>
						<th width="50%">Deskripsi</th>
						<th width="5%">Prioritas</th>
						<th width="10%">Start</th>
						<th width="5%">Action</th>
					</tr>
				</thead>
				<tbody id="master">
				</tbody>
			</table>
		</div>

		<div class="modal fade" id="modalWork" style="color: black;">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<div class="col-xs-12" style="background-color: #3c8dbc;">
							<h1 style="text-align: center; margin:5px; font-weight: bold;">Detail SPK</h1>
						</div>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-xs-6">
								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%;">Nomor SPK</label>
									<div class="col-xs-7" align="left">
										<input type="text" class="form-control" id="spk_work" readonly>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%;">Pekerjaan</label>
									<div class="col-xs-7" align="left">
										<input type="text" class="form-control" id="pekerjaan_work" readonly>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%;">Prioritas</label>
									<div class="col-xs-7" align="left" id="prioritas_work">
									</div>
								</div>
							</div>

							<div class="col-xs-6">
								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%;">Tanggal Pengajuan</label>
									<div class="col-xs-7" align="left">
										<input type="text" class="form-control" id="tanggal_work" readonly>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%;">Nama Pengajuan</label>
									<div class="col-xs-7" align="left">
										<input type="text" class="form-control" id="nama_work" readonly>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%;">Bagian Pengaju</label>
									<div class="col-xs-7" align="left">
										<input type="text" class="form-control" id="bagian_work" readonly>
									</div>
								</div>
							</div>

							<div class="col-xs-12">
								<div class="form-group row" align="right">
									<label class="col-xs-2" style="margin-top: 1%;">Tanggal Target</label>
									<div class="col-xs-2" align="left">
										<input type="text" class="form-control" id="target_work" readonly>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-2" style="margin-top: 1%;">Deskripsi Pekerjaan</label>
									<div class="col-xs-9" align="left">
										<textarea class="form-control" id="desc_work" readonly></textarea>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-2" style="margin-top: 1%;">Catatan Safety</label>
									<div class="col-xs-9" align="left">
										<textarea class="form-control" id="safety_work" readonly></textarea>
									</div>
								</div>
							</div>

							<div class="col-xs-12">
								<button type="button" class="btn btn-danger pull-left" data-dismiss='modal'><i class="fa fa-close"></i> Close</button>
								<button type="button" class="btn btn-success pull-right" onclick="startWork()"><i class="fa fa-thumbs-up"></i> Kerjakan</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="modalAfterWork" style="color: black;">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<div class="col-xs-12" style="background-color: #3c8dbc;">
							<h1 style="text-align: center; margin:5px; font-weight: bold;">Laporan SPK</h1>
						</div>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-xs-6">
								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%;">Nomor SPK</label>
									<div class="col-xs-7" align="left">
										<input type="text" class="form-control" id="spk_detail" readonly>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%;">Pekerjaan</label>
									<div class="col-xs-7" align="left">
										<input type="text" class="form-control" id="pekerjaan_detail" readonly>
									</div>
								</div>
							</div>

							<div class="col-xs-6">
								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%;">Tanggal Pengajuan</label>
									<div class="col-xs-7" align="left">
										<input type="text" class="form-control" id="tanggal_detail" readonly>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4" style="margin-top: 1%;">Bagian Pengaju</label>
									<div class="col-xs-7" align="left">
										<input type="text" class="form-control" id="bagian_detail" readonly>
									</div>
								</div>
							</div>
							<div class="col-xs-12"><hr style="margin-top: 10px; margin-bottom: 10px"></div>
							<div class="col-xs-12">
								<div class="form-group row" align="right">
									<label class="col-xs-2" style="margin-top: 1%;">Penyebab<span class="text-red">*</span></label>
									<div class="col-xs-10" align="left">
										<textarea class="form-control" id="penyebab_detail" placeholder="Isikan Penyebab Kerusakan"></textarea>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-2" style="margin-top: 1%;">Penanganan<span class="text-red">*</span></label>
									<div class="col-xs-10" align="left">
										<textarea class="form-control" id="penanganan_detail" placeholder="Isikan Penanganan yang dilakukan"></textarea>
									</div>
								</div>

								<!-- <div class="form-group row" align="right">
									<label class="col-xs-2" style="margin-top: 1%;">Spare Part</label>
									<div class="col-xs-5" align="left">
										<select id="part_detail_1" class="form-control select3" data-placeholder="Pilih Spare Part yang Digunakan" style="width: 100%" onchange="changepart(this)">
										</select>
									</div>
									<div class="col-xs-3" align="left">
										<div class="input-group">
											<span class="input-group-addon"><b>Qty</b></span>
											<input type="number" id="part_qty_1" class="form-control">
										</div>
									</div>
									<div class="col-xs-2" align="left">
										<button class="btn btn-success spare_part" onclick="add_part()" id="btn_1"><i class="fa fa-plus"></i></button>
									</div>


									<div id="sp_other">
									</div>
								</div> -->

								<div class="form-group row" align="right">
									<label class="col-xs-2" style="margin-top: 1%;">Foto</label>
									<div class="col-xs-10" align="left">
										<div id="box">
											<img src="" id="profile-img1" style="max-width: 33%" />
											<img src="" id="profile-img2" style="max-width: 33%" />
											<img src="" id="profile-img3" style="max-width: 33%" />
										</div>
										<label class="text-red pull-right txt_reset" onclick="reset()"><i class="fa fa-refresh"> Reset</i></label>
										<label for="foto1" class="text-green txt_foto" id="txt_foto1"><i class="fa fa-plus"></i> Tambah</label>
										<input type="file" name="foto" id="foto1" class="foto">

										<label for="foto2" class="text-green txt_foto" id="txt_foto2"><i class="fa fa-plus"></i> Tambah</label>
										<input type="file" name="foto" id="foto2" class="foto">

										<label for="foto3" class="text-green txt_foto" id="txt_foto3"><i class="fa fa-plus"></i> Tambah</label>
										<input type="file" name="foto" id="foto3" class="foto">
									</div>
								</div>
							</div>
							<div class="col-xs-12">
								<button type="button" class="btn btn-warning pull-left" onclick="noPart()"><i class="fa fa-exclamation-circle"></i> Vendor</button>
								<button type="button" class="btn btn-warning pull-left" onclick="noPart()"><i class="fa fa-exclamation-circle"></i> masih WJO</button>
								<button type="button" class="btn btn-primary pull-left" onclick="noPart()"><i class="fa fa-exclamation-circle"></i> Part Tidak Ada</button>
								<button type="button" class="btn btn-success pull-right" onclick="postFinish()"><i class="fa fa-thumbs-up"></i> Selesai</button>
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
	<script>
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		var start_working = [];
		// var part_list = [];
		var no = 1;

		jQuery(document).ready(function() {
			// $('.select3').select2({
			// 	dropdownParent: $('#modalAfterWork'),
			// 	allowClear: true,
			// 	minimumInputLength: 3
			// });

			get_spk();
			// get_parts();
			$("#txt_foto2").hide();
			$("#txt_foto3").hide();
		});

		function get_spk() {
			$("#master").empty();
			var body = "";

			$.get('{{ url("fetch/maintenance/spk") }}', function(result, status, xhr){
				$.each(result.datas, function(index, value){
					body += "<tr>";
					body += "<td>"+value.order_no+"</td>";
					body += "<td>"+value.section+"</td>";
					body += "<td>"+value.type+"</td>";
					body += "<td>"+value.description+"</td>";

					if(value.priority == 'Urgent'){
						var priority = '<span style="font-size: 13px;" class="label label-danger">Urgent</span>';
					}else{
						var priority = '<span style="font-size: 13px;" class="label label-default">Normal</span>';
					}
					body += "<td>"+priority+"</td>";

					op = [];
					var start_actual = "";
					var stat = 0;

					$.each(result.proses_log, function(index2, value2){
						if (value.order_no == value2.order_no) {
							if (value2.operator_id == "{{ Auth::user()->username }}") {
								op = [];
								op.push(value2.start_actual);
								start_actual = value2.start_actual;
								stat = 1;
							} else {
								if (stat == 0) {
									op.push(value2.name);
								}
							}

						}
					})

					body += "<td>"+op+"</td>";


					if (start_actual != "") {
						// body += "<td style='background-color: #fffcb7'>"+start_actual+"</td>";
						body += "<td><button class='btn btn-danger' onclick='modalAfterWork(\""+value.order_no+"\",\""+$("#op").text()+"\",\""+value.type+" - "+value.category+"\",\""+value.request_date+"\",\""+value.section+"\")'><i class='fa fa-thumbs-up'></i> Selesai</button></td>";
					} else {
						// start_working.push(["", ""]);
						// body += "<td>-</td>";
						body += "<td><button class='btn btn-success' onclick='modalWork(\""+value.order_no+"\",\""+value.type+" - "+value.category+"\",\""+value.request_date+"\",\""+value.section+"\",\""+value.name+"\",\""+value.target_date+"\",\""+value.description+"\",\""+value.safety_note+"\",\""+value.priority+"\")'><i class='fa fa-gears'></i> Kerjakan</button></td>";
					}

					body += "</tr>";
				})
				$('#table_master').DataTable().clear();
				$('#table_master').DataTable().destroy();

				$("#master").append(body);

				var table = $('#table_master').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
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
						]
					},
					'order': [5, 'desc'],
					'paging': true,
					'lengthChange': true,
					'searching': false,
					'ordering': true,
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true,
				});

				// setTime();
				// setInterval(setTime, 1000);
			})
		}


		function setTime() {
			for (var i = 0; i < start_working.length; i++) {
				if (start_working[i][0] != "") {
					if (start_working[i][1] != "") {
						var duration = diff_seconds(start_working[i][1], start_working[i][0]);
						document.getElementById("hours"+i).innerHTML = pad(parseInt(duration / 3600));
						document.getElementById("minutes"+i).innerHTML = pad(parseInt((duration % 3600) / 60));
						document.getElementById("seconds"+i).innerHTML = pad(duration % 60);
					} else {
						var duration = diff_seconds(new Date(), start_working[i][0]);
						document.getElementById("hours"+i).innerHTML = pad(parseInt(duration / 3600));
						document.getElementById("minutes"+i).innerHTML = pad(parseInt((duration % 3600) / 60));
						document.getElementById("seconds"+i).innerHTML = pad(duration % 60);
					}
				}
			}
		}

		function modalWork(order_no, pekerjaan, request_date, bagian, nama, target_date, desc, safety_note, priority) {
			$("#modalWork").modal('show');

			$("#spk_work").val(order_no);
			$("#pekerjaan_work").val(pekerjaan);
			$("#tanggal_work").val(request_date);

			if(priority == 'Urgent'){
				var prioritas = '<span style="font-size: 13px;" class="label label-danger">Urgent</span>';
			}else{
				var prioritas = '<span style="font-size: 13px;" class="label label-default">Normal</span>';
			}

			$("#prioritas_work").html(prioritas);
			$("#bagian_work").val(bagian);
			$("#nama_work").val(nama);
			$("#target_work").val(target_date);
			$("#desc_work").text(desc);

			if (safety_note != 'null') {
				$("#safety_work").text(safety_note);
			}
		}

		function startWork() {
			var data = {
				order_no : $("#spk_work").val()
			};

			$.get('{{ url("work/maintenance/spk") }}', data, function(result, status, xhr){
				openSuccessGritter('Success', '');

				$("#modalWork").modal('hide');
				get_spk();
			})
		}

		// function changepart(elem) {
		// 	// console.log($(elem).val());
		// 	var ido = $(elem).attr("id");
		// 	tmp_ido = ido.split("_");

		// 	status = "";

		// 	$.each(part_list, function(index, value){
		// 		if (value.part_number == $(elem).val()) {
		// 			status = value.stock;
		// 		}
		// 	})
		// }

		function modalAfterWork(order_no, operator_id, pekerjaan, request_date, bagian) {
			$("#modalAfterWork").modal('show');

			$("#spk_detail").val(order_no);
			$("#pekerjaan_detail").val(pekerjaan);
			$("#tanggal_detail").val(request_date);
			$("#bagian_detail").val(bagian);
		}

		function postFinish() {
			var penyebab = $("#penyebab_detail").val();
			var penanganan = $("#penanganan_detail").val();
			var spk_detail = $("#spk_detail").val();

			if (penyebab == "" || penanganan == "") {
				openErrorGritter('Error', 'Ada Kolom yang Kosong');
				return false;
			}

			var foto = [];
			var part = [];

			$('#box > img').each(function () {
				foto.push($(this).attr("src"));
			});

			// $('.spare_part').each(function(index, value) {
			// 	ids = $(this).attr("id");
			// 	tmp_ids = ids.split('_')[1];

			// 	if ($("#part_detail_"+tmp_ids).val() != "") {
			// 		// if ($("#part_qty_"+tmp_ids).val() > $("#part_stock_"+tmp_ids).val()) {
			// 		// 	openErrorGritter('Fail', 'Melebihi Stok');
			// 		// 	return false;	
			// 		// }

			// 		part.push({'part_number' : $("#part_detail_"+tmp_ids).val(), 'qty' : $("#part_qty_"+tmp_ids).val()});
			// 	}
			// });

			var data = {
				order_no : spk_detail,
				penyebab : penyebab,
				penanganan : penanganan,
				// spare_part : part,
				foto : foto
			}

			if ($("#profile-img1").attr("src") != "" || $("#profile-img2").attr("src") != "" || $("#profile-img3").attr("src") != "") {
				$.post('{{ url("report/maintenance/spk") }}', data, function(result, status, xhr){
					if (result.status) {
						openSuccessGritter('Success', 'SPK Terselesaikan');
						$("#modalAfterWork").modal('hide');
						get_spk();
					} else {
						openErrorGritter('Error', result.message);
					}
				})
			} else {
				openErrorGritter('Fail', 'Foto Harap Diisi');
				return false;
			}
		}

		function noPart() {
			var spk_detail = $("#spk_detail").val();
			var part_detail = $("#part_detail").val();

			var data = {
				order_no : spk_detail,
				part : part_detail
			}

			$.get('{{ url("report/maintenance/spk/nopart") }}', data, function(result, status, xhr){
				if (result.status) {
					openSuccessGritter('Success', 'Part Tidak Ada');
				} else {
					openErrorGritter('Error', result.message);
				}
			})
		}

		// function get_parts() {
		// 	var option_part = "";
		// 	option_part += '<option></option>';

		// 	$.get('{{ url("fetch/maintenance/inven/list") }}', function(result, status, xhr){
		// 		$.each(result.inventory, function(index, value){
		// 			part_list.push({'part_number' : value.part_number,'spare_part' : value.part_name+' - '+value.specification, 'stock' : value.stock});

		// 			option_part += "<option value='"+value.part_number+"'>"+value.part_name+' - '+value.specification+"</option>";
		// 		});

		// 		console.log(part_list);
		// 		$("#part_detail_1").append(option_part);

		// 	})
		// }

		// function add_part() {
		// 	no++;
		// 	var input_part = "";
		// 	var option_part = "";

		// 	input_part += "<div id='row_"+no+"' class='spare_part'>";
		// 	input_part += "<label class='col-xs-2' style='margin-top: 1%;'></label>";
		// 	input_part += "<div class='col-xs-5' align='left' style='margin-top: 1%;'>";
		// 	input_part += '<select id="part_detail_'+no+'" class="form-control select3" data-placeholder="Pilih Spare Part yang Digunakan" style="width: 100%" onchange="changepart(this)"></select></div>';
		// 	input_part += '<div class="col-xs-3" align="left" style="margin-top: 1%;">';
		// 	input_part += '<div class="input-group">';
		// 	input_part += '<span class="input-group-addon"><b>Qty</b></span>';
		// 	input_part += '<input type="number" id="part_qty_'+no+'" class="form-control">';
		// 	input_part += '</div></div>';
		// 	input_part += '<div class="col-xs-2" align="left" style="margin-top: 1%;">';
		// 	input_part += '<button class="btn btn-danger" onclick="remove_part(this)"><i class="fa fa-minus"></i></button></div></div>';

		// 	option_part += '<option></option>';
		// 	$.each(part_list, function(index, value){
		// 		option_part += "<option value='"+value.part_number+"'>"+value.spare_part+"</option>";
		// 	});

		// 	$("#sp_other").append(input_part);
		// 	$("#part_detail_"+no).append(option_part);

		// 	$(function () {
		// 		$('.select3').select2({
		// 			dropdownParent: $('#modalAfterWork'),
		// 			allowClear: true,
		// 			minimumInputLength: 3
		// 		});
		// 	})
		// }

		// function remove_part(elem) {
		// 	var dd = $(elem).parent().parent().attr("id");
		// 	// console.log(dd);
		// 	$("#"+dd).remove();
		// }

		function readURL(input) {

			if (input.files && input.files[0]) {

				var reader = new FileReader();

				num = input.id.replace(/[^\d]+/, '');

				reader.onload = function (e) {

					$('#profile-img'+num).attr('src', e.target.result);

				}

				reader.readAsDataURL(input.files[0]);

			}

		}

		$("#foto1, #foto2, #foto3").change(function(){
			readURL(this);

			$(".txt_foto").hide();

			var num = $(this).attr('id').replace(/[^\d]+/, '');			

			if ($("#profile-img"+(parseInt(num)+1)).attr("src") == "") {
				$("#txt_foto"+(parseInt(num)+1)).show();
			}
		});

		$("#profile-img1").click(function() {
			$("input[id='foto1']").click();
		});

		$("#profile-img2").click(function() {
			$("input[id='foto2']").click();
		});

		$("#profile-img3").click(function() {
			$("input[id='foto3']").click();
		});

		function reset() {
			$("#profile-img1").attr("src", "");
			$("#profile-img2").attr("src", "");
			$("#profile-img3").attr("src", "");

			$(".txt_foto").hide();
			$("#txt_foto1").show();
		}

		var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

		function openSuccessGritter(title, message){
			jQuery.gritter.add({
				title: title,
				text: message,
				class_name: 'growl-success',
				image: '{{ url("images/image-screen.png") }}',
				sticky: false,
				time: '4000'
			});
		}

		function openErrorGritter(title, message) {
			jQuery.gritter.add({
				title: title,
				text: message,
				class_name: 'growl-danger',
				image: '{{ url("images/image-stop.png") }}',
				sticky: false,
				time: '4000'
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

		function pad(val) {
			var valString = val + "";
			if (valString.length < 2) {
				return "0" + valString;
			} else {
				return valString;
			}
		}

		function diff_seconds(dt2, dt1){
			var diff = (dt2.getTime() - dt1.getTime()) / 1000;
			return Math.abs(Math.round(diff));
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