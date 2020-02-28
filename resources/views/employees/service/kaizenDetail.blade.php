@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style type="text/css">
	.kotak {
		border : 1px solid #ebedeb;
		background-color: #ebedeb;
		border-radius: 5px;
		padding: 5px;
		display: inline-block;
		width: 49%;
		vertical-align: top;
		margin-bottom: 5px;
	}
	hr {
		margin: 2px 0 2px 0;
		border-top: solid black 1px;
	}
	.table-bordered > thead > tr > th {
		text-align: center;
		vertical-align: middle;
	}
	.table-bordered > tbody > tr > td {
		vertical-align: middle;
	}

	#tabel_nilai > tbody > tr > td {
		padding-top: 0px;
		padding-bottom: 0px;
		border: 1px solid black;
		text-align: center;
	}

	#tabel_nilai > thead > tr > th {
		border: 1px solid black;
		background-color: #7e5686;
		color: white;
	}

	#tabel_nilai_all > tbody > tr > td {
		padding-top: 0px;
		padding-bottom: 0px;
	}

	#assess_table > tbody > tr > td {
		text-align: center;
	}
	.kiri {
		text-align: left !important;
	}

	.radio	{
		margin: 0px !important;
	}

	.nilai {
		font-size: 40px;
	}
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small>
			<span> {{ $title_jp }}</span>
		</small>
	</h1>
</section>
@endsection

@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-xs-12">
						<div class="row">
							<div class="col-xs-12" style="font-size: 18px; margin-bottom: 8px; padding: 0px 5px 0px 5px">
								<div id="judul" style="text-align: center; border-radius: 2px; font-weight: bold;"></div>
								<div id="propose" style="text-align: center; font-size: 15px"></div>
								<table width="100%">
									<tr>
										<td width="10%"><b>NIK/ Name</b></td>
										<td>:</td>
										<td><span id="nama"></span></td>
										<td width="10%"><b>Date</b></td>
										<td>:</td>
										<td><span id="tgl"></span></td>
									</tr>
									<tr>
										<td width="10%"><b>Section</b></td>
										<td>:</td>
										<td><span id="bagian"></span></td>
										<td width="10%"><b>Area Kaizen</b></td>
										<td>:</td>
										<td><span id="lokasi"></span></td>
									</tr>
									<tr>
										<td width="10%"><b>Leader</b></td>
										<td>:</td>
										<td><span id="leader"></span></td>
									</tr>
								</table>			
							</div>

							<div class="col-xs-6" style="padding: 0px 5px 0px 5px">
								<div style="border: 1px solid black; padding: 2px 5px 2px 5px; font-weight: bold; background-color: #7e5686; color: white;">BEFORE :</div>
								<div style="border: 1px solid black; padding: 2px 5px 2px 5px; border-radius: 1px" id="before"></div>
							</div>
							<div class="col-xs-6" style="padding: 0px 5px 0px 5px">
								<div style="border: 1px solid black; padding: 2px 5px 2px 5px; font-weight: bold; background-color: #7e5686; color: white;">AFTER :</div>
								<div style="border: 1px solid black; padding: 2px 5px 2px 5px; border-radius: 1px" id="after"></div>
							</div>
							<!-- <div class="kotak">
								<div style="font-size: 20px"><b>Before : </b><hr></div>
								<div id="before"></div>
							</div>
							<div class="kotak">
								<div style="font-size: 20px"><b>After : </b><hr></div>
								<div id="after"></div>
							</div> -->
							<div class="col-xs-12" style="padding: 0px 5px 0px 5px">
								<br>
								<b>Estimasi Hasil :</b>
								<table width="100%" id="tableEstimasi" class="table table-bordered" style="font-size: 15px;">
								</table>
							</div>
							<div class="col-xs-12">
								<b>Note :</b>
								<table width="100%" class="table table-bordered" style="font-size: 15px;">
									<thead>
										<tr><th width="50%">Foreman</th><th width="50%">Manager</th></tr>
									</thead>
									<tbody>
										<tr>
											<td id="note_foreman"></td>
											<td id="note_manager"></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<center><input type="checkbox" checked data-toggle="toggle" data-on="Kaizen" data-off="Not Kaizen" data-onstyle="success" data-offstyle="danger" data-width="300" id="kz_stat"></center>
					</div>
				</div>
			</div>

			<div class="box box-solid" id="box_nilai">
				<div class="box-body">
					<div class="col-xs-12">
						<table width="100%" id="tabel_nilai" class="table table-bordered">
							<thead>
								<tr>
									<th colspan="7"><b>TABEL PERHITUNGAN PENILAIAN KAIZEN</b></th>
								</tr>
								<tr>
									<th width="3%">No.</th>
									<th>Kategori</th>
									<th>Bobot</th>
									<th>Nilai</th>
									<th>Kriteria Penilaian</th>
									<th width="10%">Foreman/Chief</th>
									<th width="10%">Manager</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td rowspan="8">1</td>
									<td rowspan="8">Estimasi Hasil</td>
									<td rowspan="8">40</td>
									<td><div class="radio"><label><input type="radio" value="8" name="r_nilai1"> 8</label></div></td>
									<td class="kiri">Cost Down per month ( > Rp 10.000.000,-)</td>
									<td rowspan="8" id="r_foreman1" class="nilai"></td>
									<td rowspan="8" id="r_manager1" class="nilai"></td>
								</tr>
								<tr>
									<td><div class="radio"><label><input type="radio" value="7" name="r_nilai1"> 7</label></div></td>
									<td class="kiri">Cost Down per month (Rp 5.000.000,- s/d Rp 10.000.000)</td>
								</tr>
								<tr>
									<td><div class="radio"><label><input type="radio" value="6" name="r_nilai1"> 6</label></div></td>
									<td class="kiri">Cost Down per month (Rp 1.000.000,- s/d Rp 5.000.000)</td>
								</tr>
								<tr>
									<td><div class="radio"><label><input type="radio" value="5" name="r_nilai1"> 5</label></div></td>
									<td class="kiri">Cost Down per month (Rp 600.000,- s/d Rp 1.000.000)</td>
								</tr>
								<tr>
									<td><div class="radio"><label><input type="radio" value="4" name="r_nilai1"> 4</label></div></td>
									<td class="kiri">Cost Down per month (Rp 300.000,- s/d Rp 600.000)</td>
								</tr>
								<tr>
									<td><div class="radio"><label><input type="radio" value="3" name="r_nilai1"> 3</label></div></td>
									<td class="kiri">Cost Down per month (Rp 100.000,- s/d Rp 300.000)</td>
								</tr>
								<tr>
									<td><div class="radio"><label><input type="radio" value="2" name="r_nilai1"> 2</label></div></td>
									<td class="kiri">Cost Down per month (Rp 50.000,-  s/d Rp 100.000)</td>
								</tr>
								<tr>
									<td><div class="radio"><label><input type="radio" value="1" name="r_nilai1"> 1</label></div></td>
									<td class="kiri">Cost Down per month (Rp 0,- s/d Rp 50.000,-)</td>
								</tr>

								<tr>
									<td rowspan="5">2</td>
									<td rowspan="5">Ide</td>
									<td rowspan="5">30</td>
									<td><div class="radio"><label><input type="radio" value="5" name="r_nilai2"> 5</label></div></td>
									<td class="kiri">Ide Original dan perlu waktu untuk design alat > 1 bulan</td>
									<td rowspan="5" id="r_foreman2" class="nilai"></td>
									<td rowspan="5" id="r_manager2" class="nilai"></td>
								</tr>
								<tr>
									<td><div class="radio"><label><input type="radio" value="4" name="r_nilai2"> 4</label></div></td>
									<td class="kiri">Ide Original dan perlu waktu untuk design 1 minggu s/d 1 bulan</td>
								</tr>
								<tr>
									<td><div class="radio"><label><input type="radio" value="3" name="r_nilai2"> 3</label></div></td>
									<td class="kiri">Ide Original dan perlu waktu untuk design s/d 1 minggu</td>
								</tr>
								<tr>
									<td><div class="radio"><label><input type="radio" value="2" name="r_nilai2"> 2</label></div></td>
									<td class="kiri">Ide tidak Original tetapi belum pernah dilakukan di YMPI</td>
								</tr>
								<tr>
									<td><div class="radio"><label><input type="radio" value="1" name="r_nilai2"> 1</label></div></td>
									<td class="kiri">Ide tidak Original dan pernah dilakukan di YMPI</td>
								</tr>

								<tr>
									<td rowspan="5">3</td>
									<td rowspan="5">Implementasi</td>
									<td rowspan="5">30</td>
									<td><div class="radio"><label><input type="radio" value="5" name="r_nilai3"> 5</label></div></td>
									<td class="kiri">Mudah diterapkan (dalam waktu < 1 minggu)</td>
									<td rowspan="5" id="r_foreman3" class="nilai"></td>
									<td rowspan="5" id="r_manager3" class="nilai"></td>
								</tr>
								<tr>
									<td><div class="radio"><label><input type="radio" value="4" name="r_nilai3"> 4</label></div></td>
									<td class="kiri">Bisa diterapkan (dalam waktu 1 minggu s/d 1 bulan)</td>
								</tr>
								<tr>
									<td><div class="radio"><label><input type="radio" value="3" name="r_nilai3"> 3</label></div></td>
									<td class="kiri">Agak sulit diterapkan (dalam waktu 1 s/d 2 bulan)</td>
								</tr>
								<tr>
									<td><div class="radio"><label><input type="radio" value="2" name="r_nilai3"> 2</label></div></td>
									<td class="kiri">Sangat sulit diterapkan (dalam waktu 2 s/d 3 bulan)</td>
								</tr>
								<tr>
									<td><div class="radio"><label><input type="radio" value="1" name="r_nilai3"> 1</label></div></td>
									<td class="kiri">Tidak mungkin diterapkan (dalam waktu > 3 bulan)</td>
								</tr>
							</tbody>
							<tfoot>
								<tr style="background-color: #cb89d9;">
									<th colspan="5" style="text-align: right; font-size: 35px">Total</th>
									<th id="total_foreman" style="text-align: center; font-size: 35px">0</th>
									<th id="total_manager" style="text-align: center; font-size: 35px">0</th>
								</tr>
								<tr>
									<th colspan="7"><button class="btn btn-success" onclick="cek_penilaian()" style="width: 100%; font-weight: bold; font-size: 20px"><i class="fa fa-pencil"></i>&nbsp; Make Assessment</button></th>
								</tr>
							</tfoot>
						</table>
						Note :
						<textarea class="form-control" placeholder="Write a note. . ." id="catatan"></textarea>
						<button class="btn btn-primary" id="confirmCatatan" onclick="confirmCatatan()"><i class="fa fa-paper-plane-o"></i> Send</button>
					</div>
				</div>
			</div>

			<div class="box box-solid" id="box_nilai2">
				<div class="box-body">
					<div class="col-xs-12">
						<table width="100%" id="tabel_nilai_all" class="table table-bordered">
							<thead>
								<tr>
									<th>No</th>
									<th>Total Nilai</th>
									<th>Point</th>
									<th>Keterangan</th>
									<th>Reward Aplikasi</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>1</td>
									<td><300</td>
									<td>2</td>
									<td>Kurang</td>
									<td>Rp 2.000,-</td>
								</tr>

								<tr>
									<td>2</td>
									<td>300 - 350</td>
									<td>4</td>
									<td>Cukup</td>
									<td>Rp 5.000,-</td>
								</tr>

								<tr>
									<td>3</td>
									<td>351 - 400</td>
									<td>6</td>
									<td>Baik</td>
									<td>Rp 10.000,-</td>
								</tr>

								<tr>
									<td>4</td>
									<td>401 - 450</td>
									<td>8</td>
									<td>Sangat Baik</td>
									<td>Rp 25,000,-</td>
								</tr>

								<tr>
									<td>5</td>
									<td>> 450</td>
									<td>10</td>
									<td>Potensi Excellent</td>
									<td>Rp 50,000,-</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="col-xs-12" id="submit_button">
				<button class="btn btn-success" style="width: 100%" onclick="cek_kaizen()"><i class="fa fa-check"></i> SUBMIT</button>
			</div>

			<div class="modal fade" id="modalPenilaian">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-body">
							<div class="row">
								<div class="col-xs-12">
									<p style="font-size: 25px; font-weight: bold;">Are you sure to assess this Kaizen with :</p>
									<table class="table" width="100%" id="assess_table">
										<tr>
											<th></th>
											<th width="15%" style="text-align: center">Bobot</th>
											<th width="2%"></th>
											<th width="15%" style="text-align: center">Nilai</th>
											<th width="25%" style="text-align: center">Bobot x Nilai</th>
										</tr>
										<tr>
											<th>Estimasi Hasil</th>
											<td>40</td>
											<td>X</td>
											<td id="sub_asses_1"></td>
											<td id="asses_1" style="font-size: 20px"></td>
										</tr>
										<tr>
											<th>Ide</th>
											<td>30</td>
											<td>X</td>
											<td id="sub_asses_2"></td>
											<td id="asses_2" style="font-size: 20px"></td>
										</tr>
										<tr>
											<th>Implementasi</th>
											<td>30</td>
											<td>X</td>
											<td id="sub_asses_3"></td>
											<td id="asses_3" style="font-size: 20px"></td>
										</tr>
										<tr>
											<th colspan="4" style="text-align: right; font-size: 30px">Total</th>
											<th id="assess_tot" style="text-align: center; font-size: 30px; border: 1px solid black"></th>
										</tr>
									</table>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<form method="post" action="{{ url('assess/kaizen') }}">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
								<input type="hidden" name="id" value="<?php echo Request::segment(4) ?>">
								<input type="hidden" name="nilai1">
								<input type="hidden" name="nilai2">
								<input type="hidden" name="nilai3">
								<input type="hidden" name="category" value="<?php echo Request::segment(5) ?>">
								<button type="submit" class="btn btn-success pull-left"><i class="fa fa-check"></i> YES</button>
							</form>
							<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> NO</button>
						</div>
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>


			<div class="modal modal-danger fade" id="modalKaizen">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-body">
							<div class="row">
								<div class="col-xs-12">
									<p style="font-size: 25px; font-weight: bold;">Are you sure this isn't kaizen?</p>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-success pull-left" data-dismiss="modal" onclick="notKaizen()"><i class="fa fa-check"></i> YES</button>
							<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> NO</button>
						</div>
					</div>
				</div>
			</div>

		</div>
	</section>
	@endsection

	@section('scripts')
	<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
	<script src="{{ url("js/bootstrap-toggle.min.js") }}"></script>
	<script>

		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		var total1 = 0, total2 = 0, total3 = 0;
		var kode = "";

		jQuery(document).ready(function() {
			$('body').toggleClass("sidebar-collapse");

			getKaizen("{{ Request::segment(4) }}");

			if ("{{ Request::segment(5) }}" == "foreman") {
				kode = "foreman";
			} else if ("{{ Request::segment(5) }}" == "manager") {
				kode = "manager";
			}

			var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
		});

		$(window).on('pageshow', function(){
			if($("#kz_stat").prop("checked") == true){
				$("#box_nilai").show();
				$("#box_nilai2").show();
				$("#submit_button").hide();
			}else{
				$("#box_nilai").hide();
				$("#box_nilai2").hide();
				$("#submit_button").show();
			}
		});

		$("#kz_stat").change(function () {
			if($(this).prop("checked") == true){
				$("#box_nilai").show();
				$("#box_nilai2").show();
				$("#submit_button").hide();
			}else{
				$("#box_nilai").hide();
				$("#box_nilai2").hide();
				$("#submit_button").show();
			}
		})

		$("input[name='r_nilai1']").change(function(){
			var nilai1 = $("input[name='r_nilai1']:checked").val();
			$("#r_"+kode+"1").html(parseInt(nilai1) * 40);
			$("#sub_asses_1").html(parseInt(nilai1));
			$("#asses_1").html(parseInt(nilai1) * 40);
			$("input[name='nilai1']").val(nilai1);
			total1 = parseInt(nilai1) * 40;

			$("#total_"+kode).html(total1 + total2 + total3);
			$("#assess_tot").html(total1 + total2 + total3);
		});

		$("input[name='r_nilai2']").change(function(){
			var nilai2 = $("input[name='r_nilai2']:checked").val();
			$("#r_"+kode+"2").html(parseInt(nilai2) * 30);
			$("#sub_asses_2").html(parseInt(nilai2));
			$("#asses_2").html(parseInt(nilai2) * 30);
			$("input[name='nilai2']").val(nilai2);
			total2 = parseInt(nilai2) * 30;

			$("#total_"+kode).html(total1 + total2 + total3);
			$("#assess_tot").html(total1 + total2 + total3);
		});

		$("input[name='r_nilai3']").change(function(){
			var nilai3 = $("input[name='r_nilai3']:checked").val();
			$("#r_"+kode+"3").html(parseInt(nilai3) * 30);
			$("#sub_asses_3").html(parseInt(nilai3));
			$("#asses_3").html(parseInt(nilai3) * 30);
			$("input[name='nilai3']").val(nilai3);
			total3 = parseInt(nilai3) * 30;

			$("#total_"+kode).html(total1 + total2 + total3);
			$("#assess_tot").html(total1 + total2 + total3);
		});

		function confirmCatatan(){
			var note = $('#catatan').val();

			if (note == "") {
				openErrorGritter('Failed!' ,'Note cannot be empty');
				return false;
			}

			data = {
				id : "{{ Request::segment(4) }}",
				catatan : note,
				from : "{{ Request::segment(5) }}"
			}
			$.post('{{ url("input/kaizen/detail/note") }}', data, function(result) {
				if(result.status){
					openSuccessGritter('Success!', result.message);
					setTimeout(function(){ window.history.back(); }, 2000);
				}
				else{
					openErrorGritter('Error!', result.message);
				}
			});
		}

		function getKaizen(id) {
			data = {
				id: id
			}

			var max = [];

			$.get('{{ url("fetch/kaizen/detail") }}', data, function(result) {
				$("#judul").html(result.datas[0].title);
				$("#lokasi").html(result.datas[0].area);
				$("#propose").html(result.datas[0].purpose);				
				$("#tgl").html(result.datas[0].date);
				$("#nama").html(result.datas[0].employee_id+" "+result.datas[0].employee_name);
				$("#bagian").html(result.datas[0].section);
				$("#leader").html(result.datas[0].leader_name);
				$("#before").html(result.datas[0].condition);
				$("#after").html(result.datas[0].improvement);
				$("#note_foreman").html(result.datas[0].foreman_note);
				$("#note_manager").html(result.datas[0].manager_note);

				$('#catatan').val(result.datas[0].remark);
				$("#tableEstimasi").empty();
				bd = "";
				tot = 0;
				if (result.datas[0].cost_name) {
					$.each(result.datas, function(index, value){
						bd += "<tr>";
						var unit = "";

						if (value.cost_name == "Manpower") {
							unit = "menit";
							sub_tot = (value.sub_total_cost * 20);
							tot += sub_tot;
						} else if (value.cost_name == "Tempat") {
							unit = value.unit+"<sup>2</sup>";
							sub_tot = parseInt(value.sub_total_cost);
							tot += sub_tot;
						}
						else {
							unit = value.frequency;
							sub_tot = value.sub_total_cost;
							tot += sub_tot;
						}

						bd += "<th>"+value.cost_name+"</th>";
						bd += "<td><b>"+value.cost+"</b> "+unit+" X <b>Rp "+value.std_cost+",-</b></td>";
						bd += "<td><b>Rp "+sub_tot.toLocaleString('es-ES')+",- / bulan</b></td>";
						bd += "</tr>";
					});

					bd += "<tr style='font-size: 18px;'>";
					bd += "<th colspan='2' style='text-align: right;padding-right:5px'>Total</th>";
					bd += "<td><b>Rp "+tot.toLocaleString('es-ES')+",-</b></td>";
					bd += "</tr>";

					$("#tableEstimasi").append(bd);
				}

				// $("#mp_point").html("<b>"+result.datas.mp_point+"</b> menit X <b>Rp "+ 500+",00</b>");
				// $("#total_mp_point").html("<b>Rp "+result.datas.mp_point * 500 + ",00</b> / bulan");
				// $("#space_point").html("<b>"+result.datas.space_point+"</b> m<sup>2</sup> X <b>Rp "+0+",00");
				// $("#total_space_point").html("<b>Rp "+result.datas.space_point+",00</b>");
				// $("#other_point").html("<b>Rp "+result.datas.other_point+"</b>");
				// $("#total_other_point").html("<b>Rp "+result.datas.other_point+",00</b>");
				// var tot = (result.datas.mp_point * 500) + (result.datas.space_point * 0) + result.datas.other_point;
				// $("#total_point").html("<b>Rp "+tot+",00");
				max.push($("#before").height());
				max.push($("#after").height());

				mx = (Math.max(...max));

				$("#before").height(mx);
				$("#after").height(mx);

				if ("{{ Request::segment(5) }}" == "manager") {
					$("#r_foreman1").html(result.datas[0].foreman_point_1 * 40);
					$("#r_foreman2").html(result.datas[0].foreman_point_2 * 30);
					$("#r_foreman3").html(result.datas[0].foreman_point_3 * 30);

					$("#total_foreman").html((result.datas[0].foreman_point_1 * 40) + (result.datas[0].foreman_point_2 * 30) + (result.datas[0].foreman_point_3 * 30));
				}
			})
		}

		function cek_penilaian() {
			$('#modalPenilaian').modal({
				backdrop: 'static',
				keyboard: false
			})
		}

		function cek_kaizen() {
			$('#modalKaizen').modal({
				backdrop: 'static',
				keyboard: false
			})
		}

		function nilai() {
			var data = {
				id : "{{ Request::segment(4) }}",
				nilai1 : $("#sub_asses_1").html(),
				nilai2 : $("#sub_asses_2").html(),
				nilai3 : $("#sub_asses_3").html(),
				category : "{{ Request::segment(5) }}"
			}

			$.post('{{ url("assess/kaizen") }}', data, function(result) {
				window.history.go(-1);
			})
		}

		function notKaizen() {
			var data = {
				id : "{{ Request::segment(4) }}",
				category : "{{ Request::segment(5) }}"
			}
			$.post('{{ url("assess/kaizen") }}', data, function(result) {
				window.history.go(-1);
			})
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
