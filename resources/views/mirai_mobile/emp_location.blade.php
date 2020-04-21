@extends('layouts.visitor')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
	}
	tbody>tr>th{
		text-align:center;
		background-color: #757575;
		color: white;
	}
	tfoot>tr>th{
		text-align:center;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid rgb(150,150,150) !important;
		font-size: 14px;
		padding: 4px;
		color: white;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
		border-collapse: collapse;
		padding:5px;
		vertical-align: middle;
		color: white;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}

	#loading, #error { display: none; }
	h2{
		font-size: 70px;
		font-weight: bold;
	}

	.td:hover {
		background-color: white;
		color: black;
	}

	
</style>
@stop
@section('header')
<section class="content-header" style="text-align: center;">

</section>
@stop
@section('content')
<section class="content" style="padding-top: 0px;">
	<div class="row" style="margin-bottom: 1%;">
		<div class="col-xs-3">
			<div class="input-group date">
				<div class="input-group-addon bg-olive" style="border: none;">
					<i class="fa fa-calendar"></i>
				</div>
			</div>
		</div>
		<div class="col-xs-2">
			<button id="search" onClick="drawNumber()" class="btn bg-olive">Search</button>
		</div>
		<div class="col-xs-3 pull-right">
			<p class="pull-right" id="last_update"></p>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-9 col-md-9" style="margin-left: 0px; padding: 0px;">
			<div class="col-lg-12" style="margin-bottom: 1%;">
				<div class="table-responsive">
					<table class="table table-bordered" style="background-color: #212121">
						<thead style="background-color: #757575">
							<tr>
								<th style="vertical-align: middle;width:5%;font-size: 18px" id="dept_head">Departemen</th>
							</tr>
						</thead>
						<tbody id="tableBodyResult">
							<tr>
								<th><i class="fa fa-spinner fa-pulse"></i> Loading . . .</th>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="col-lg-12" style="margin-bottom: 1%;">
				<div id="container2" style="width: 100%;"></div>
			</div>  
		</div>
		<div class="col-lg-3 col-xs-12" style="margin-left: 0px;">
			<div class="col-lg-12 col-xs-12" style="margin-left: 0px; padding: 0px;">
				<!-- small box -->
				<div class="small-box bg-green" style="font-size: 30px;font-weight: bold;height: 153px;">
					<div class="inner" style="padding-bottom: 0px;">
						<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>TOTAL</b></h3>
						<h2 style="margin: 0px;font-size: 4vw;" id='total'>0<sup style="font-size: 2vw"> kasus</sup></h2>
					</div>
					<div class="icon">
						<i class="ion ion-stats-bars"></i>
					</div>
				</div>
			</div>
			<div class="col-lg-12 col-xs-12" style="margin-left: 0px; padding: 0px;">
				<div class="small-box bg-red" style="font-size: 30px;font-weight: bold;height: 153px;">
					<div class="inner" style="padding-bottom: 0px;">
						<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>Lokasi Tidak Sama</b></h3>
						{{-- <h3 style="margin-bottom: 0px;font-size: 25px;"><b>(PIANICA)</b></h3> --}}
						<h2 style="margin: 0px;font-size: 4vw;" id='lokasi_tidak_sama'>0<sup style="font-size: 2vw"> kasus</sup></h2>
					</div>
					<div class="icon">
						<i class="fa fa-times"></i>
					</div>
				</div>
			</div>
			<div class="col-lg-12 col-xs-12" style="margin-left: 0px; padding: 0px;">
				<!-- small box -->
				<div class="small-box bg-yellow" style="font-size: 30px;font-weight: bold;height: 143px;">
					<div class="inner" style="padding-bottom: 0px;">
						<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>Lokasi Sama</b></h3>
						<h2 style="margin: 0px; font-size: 4vw;" id='lokasi_sama'>0<sup style="font-size: 2vw"> kasus</sup></h2>
					</div>
					<div class="icon">
						<i class="fa fa-check"></i>
					</div>
				</div>
			</div>
		</div>
		
	</div>

	<div class="modal fade in" id="modalDetail" >
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span></button>
						<h4 class="modal-title" id="modalTitle"></h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<table class="table">
								<thead>
									<tr>
										<th>ID Karyawan</th>
										<th>Nama Karyawan</th>
										<th>Departemen</th>
										<th>Kota Abasensi</th>
										<th>Kota Domisili</th>
									</tr>
								</thead>
								<tbody id="body_detail">
								</tbody>
							</table>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
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
	<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
	<script src="{{ url("js/highcharts.js")}}"></script>
	<script src="{{ url("js/exporting.js")}}"></script>
	<script src="{{ url("js/export-data.js")}}"></script>
	<script>
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		jQuery(document).ready(function() {
			$('#datepicker').datepicker({
				autoclose: true,
				todayHighlight: true,
				format: "dd-mm-yyyy"
			});
			$('#last_update').html('<i class="fa fa-clock-o"></i> Last Seen: '+ getActualFullDate());
			$('#last_update').css('color','white');
			$('#last_update').css('font-weight','bold');

			drawNumber();
		});

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
			return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
		}

		function drawNumber(){

			var fy = $('#fy').val();

			var data = {
				fy: fy,
			};

			$.get('{{ url("fetch/mirai_mobile/report_location") }}', data, function(result, status, xhr){
				$("#tableBodyResult").empty();
				if(result.status){
					var date = [];
					$.each(result.period, function(key, value) {
						$("#dept_head").after("<th style='vertical-align: middle;width:2%;font-size: 18px'>"+value.week_date+"</th>");
						date.push(value.week_date);
					})
					body = "";

					var arr = [];
					var jumlah = [];

					$.each(result.emp_location, function(key, value) {
						arr.push(value.department);
					});

					arr = unique(arr);

					$.each(arr, function(key, value) {
						body += "<tr>";
						body += "<th>"+value+"</th>";
						for(var j = date.length-1;j>=0;j--){

							var jml = "-";
							$.each(result.emp_location, function(key, value2) {
								if (value == value2.department) {
									if (date[j] == value2.answer_date) {
										jml = value2.jumlah;
									}
								}
							});

							body += "<td class='td' onClick='detail(\""+value+"\",\""+date[j]+"\")'>"+jml+"</td>";

						}
						body += "</tr>";
					})

					$("#tableBodyResult").append(body);
				}

			});

		}

		function detail(department, tanggal) {
			var data = {
				department : department,
				date : tanggal
			}

			$("#modalTitle").text(department+" | "+tanggal);

			$("#modalDetail").modal("show");

			body = "";
			$.get('{{ url("fetch/mirai_mobile/report_location/detail") }}', data, function(result, status, xhr){
				$("#body_detail").empty();
				$.each(result.location_detail, function(key, value) {
					body += "<tr>";
					body += "<td>"+value.employee_id+"</td>";
					body += "<td>"+value.name+"</td>";
					body += "<td>"+value.department+"</td>";
					body += "<td>"+value.city+"</td>";
					body += "<td>"+value.kota+"</td>";
					body += "</tr>";
				})

				$("#body_detail").append(body);
			})
		}

		function unique(list) {
			var result = [];
			$.each(list, function(i, e) {
				if ($.inArray(e, result) == -1) result.push(e);
			});
			return result;
		}
	</script>
	@endsection