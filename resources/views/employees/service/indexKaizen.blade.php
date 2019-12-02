@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
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
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
		/*padding-left: 0;*/
		/*padding-right: 0;*/
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading { display: none; }
	#tabelDetail > tbody > tr > td {
		text-align: left;
	}
	#tabel_Kz > tbody > tr > td {
		text-align: left;
		vertical-align: top;
		padding: 2px;
	}
	#tabel_Kz > tbody > tr > th {
		padding: 2px;
		background-color: #7e5686;
		color: white;
	}
	#tabel_nilai > tbody > tr > td {
		text-align: left;
	}
	#tabel_assess > tbody > tr > td, #tabel_assess > tbody > tr > th {
		text-align: center;
	}
	#tabel_assess > tbody > tr > th {
		background-color: #7e5686;
		color: white;
	}
	#tabel_nilai_all tbody > tr > th {
		text-align: center;
		background-color: #7e5686;
		color: white;
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
	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('status') }}
	</div>   
	@endif
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-xs-12">
						<div class="col-xs-3">
							<select class="form-control select2" id="stat">
								<option value="">Pilih Status</option>
								<option value="1">Foreman NOT Verified</option>
								<option value="2">Manager NOT Verified</option>
								<option value="3">Foreman Verified</option>
								<option value="4">Manager Verified</option>
								<option value="5">NOT Kaizen</option>
							</select>
						</div>

						<div class="col-xs-3">
							<select class="form-control select2" id="section">
								<option value="">Pilih Area</option>
								@foreach($section as $scc)
								<option value="{{ $scc->section }}">{{ $scc->section }}</option>
								@endforeach
							</select>
						</div>

						<div class="col-xs-3">
							<button id="searching" class="btn btn-success" onclick="cari()">Search</button>
						</div>
					</div>
					<div class="col-xs-12">
						<br>
						<table class="table table-bordered" width="100%" id="tableKaizen">
							<thead style="background-color: rgb(126,86,134); color: #FFD700;">
								<tr>
									<th>Id</th>
									<th>Date</th>
									<th>Creator</th>
									<th>Section</th>
									<th>Title</th>
									<th>Area</th>
									<th>Foreman Status</th>
									<th>Manager Status</th>
									<th>Foreman Point</th>
									<th>Manager Point</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalDetail">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<p style="font-size: 25px; font-weight: bold; text-align: center" id="kz_title"></p>
							<table id="tabelDetail" width="100%">
								<tr>
									<th>NIK/Name </th>
									<td> : </td>
									<td id="kz_nik"></td>
									<th>Date</th>
									<td> : </td>
									<td id="kz_tanggal"></td>
								</tr>
								<tr>
									<th>Section</th>
									<td> : </td>
									<td id="kz_section"></td>
									<th>Area Kaizen</th>
									<td> : </td>
									<td id="kz_area"></td>
								</tr>
								<tr>
									<th>Leader</th>
									<td> : </td>
									<td id="kz_leader"></td>
								</tr>
								<tr>
									<td colspan="6"><hr style="margin: 5px 0px 5px 0px; border-color: black"></td>
								</tr>
							</table>
							<table width="100%" border="1" id="tabel_Kz">
								<tr>
									<th style="border-bottom: 1px solid black" width="50%">BEFORE :</th>
									<th style="border-bottom: 1px solid black; border-left: 1px" width="50%">AFTER :</th>
								</tr>
								<tr>
									<td id="kz_before"></td>
									<td id="kz_after"></td>
								</tr>
							</table>
							<table width="100%" id="tabel_nilai" style="border:1px solid black;">
								<tr>
									<th>Manpower</th>
									<td>0 menit X Rp 500,00</td>
									<td>Rp 0,00 / bulan</td>
								</tr>
								<tr>
									<th>Space</th>
									<td>0 m<sup>2</sup> X Rp 0,00</td>
									<td>Rp 0,00</td>
								</tr>
								<tr>
									<th>Other (Material,listrik, kertas, dll)</th>
									<td>Rp 0</td>
									<td>Rp 0,00</td>
								</tr>
							</table>
							<br>
							<table width="100%" border="1" id="tabel_assess">
								<tr>
									<th colspan="4">TABEL NILAI KAIZEN</th>
								</tr>
								<tr>
									<th width="5%">No</th>
									<th>Kategori</th>
									<th>Foreman / Chief</th>
									<th>Manager</th>
								</tr>
								<tr>
									<th>1</th>
									<th>Estimasi Hasil</th>
									<td id="foreman_point1"></td>
									<td id="manager_point1"></td>
								</tr>
								<tr>
									<th>2</th>
									<th>Ide</th>
									<td id="foreman_point2"></td>
									<td id="manager_point2"></td>
								</tr>
								<tr>
									<th>3</th>
									<th>Implementasi</th>
									<td id="foreman_point3"></td>
									<td id="manager_point3"></td>
								</tr>
								<tr>
									<th colspan="2"> TOTAL</th>
									<td id="foreman_total" style="font-weight: bold;"></td>
									<td id="manager_total" style="font-weight: bold;"></td>
								</tr>
							</table>
							<br>
							<table width="100%" id="tabel_nilai_all" border="1">
								<tr>
									<th>No</th>
									<th>Total Nilai</th>
									<th>Point</th>
									<th>Keterangan</th>
									<th>Reward Aplikasi</th>
								</tr>
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
									<td>350 - 400</td>
									<td>6</td>
									<td>Baik</td>
									<td>Rp 10.000,-</td>
								</tr>

								<tr>
									<td>4</td>
									<td>400 - 450</td>
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
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
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
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var area = "";
	var stat = "";

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	});

	function fill_table(pos, area, stat) {
		$('#tableKaizen').DataTable().destroy();
		var table2 = $('#tableKaizen').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"processing": true,
			"ajax": {
				"type" : "get",
				"data": { position: pos, area: area, status: stat},
				"url" : "{{ url('fetch/kaizen/') }}"
			},
			"columns": [
			{ "data": "id" },
			{ "data": "propose_date" },
			{ "data": "employee_name" },
			{ "data": "section" },
			{ "data": "title" },
			{ "data": "area" },
			{ "data": "fr_stat" },
			{ "data": "mg_stat" },
			{ "data": "fr_point" },
			{ "data": "mg_point" },
			{ "data": "action" }
			],
			"columnDefs": [
			{ "width": "2%", "targets": 0 },
			{ "width": "5%", "targets": 1 },
			{ "width": "13%", "targets": 2 },
			{ "width": "10%", "targets": 3 },
			{ "width": "5%", "targets": [5,6,7,8,9,10] },
			]
		});
	}

	$(window).on('pageshow', function(){
		fill_table('{{ $position->position }}', area, stat);
	});

	function cekDetail(id) {
		data = {
			id:id
		}

		$.get('{{ url("fetch/kaizen/detail") }}', data, function(result) {
			$("#kz_title").text(result.title);
			$("#kz_nik").text(result.employee_id + " / "+ result.employee_name);
			$("#kz_section").text(result.section);
			$("#kz_leader").text(result.leader_name);
			$("#kz_tanggal").text(result.date);
			$("#kz_area").text(result.area);
			$("#kz_before").html(result.condition);
			$("#kz_after").html(result.improvement);
			$("#foreman_point1").text(result.foreman_point_1 * 40);
			$("#foreman_point2").text(result.foreman_point_2 * 30);
			$("#foreman_point3").text(result.foreman_point_3 * 30);
			$("#foreman_total").text((result.foreman_point_1 * 40) + (result.foreman_point_2 * 30) + (result.foreman_point_3 * 30));
			$("#manager_point1").text(result.manager_point_1 * 40);
			$("#manager_point2").text(result.manager_point_2 * 30);
			$("#manager_point3").text(result.manager_point_3 * 30);
			$("#manager_total").text((result.manager_point_1 * 40) + (result.manager_point_2 * 30) + (result.manager_point_3 * 30));
			$("#modalDetail").modal('show');
		})
	}

	function cari() {
		area = $("#section").val();
		stat = $("#stat").val();
		fill_table('{{ $position->position }}',area,stat);
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