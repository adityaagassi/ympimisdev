@extends('layouts.display')
@section('stylesheets')
{{-- <link href="{{ url("css/jquery-ui.css") }}" rel="stylesheet"> --}}
<style type="text/css">
	.progres-bar {
		font-size: 20px;
		height: 50px;
	}

	.picker {
		text-align: center;
	}
	.button {
		position: absolute;
		top: 50%;
	}
	.nav-tabs-custom > ul.nav.nav-tabs {
		display: table;
		width: 100%;
		table-layout: fixed;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li {
		float: none;
		display: table-cell;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li > a {
		text-align: center;
	}
	.vendor-tab{
		width:100%;
	}
	.btn-active {
		border: 5px solid rgb(255,77,77) !important;
	}
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
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
</style>
@stop
@section('header')
{{-- <section class="content-header">
	<h1>
		Production Result <span class="text-purple">生産実績</span>
		<small>By Shipment Schedule <span class="text-purple">??????</span></small>
	</h1>
	<ol class="breadcrumb" id="last_update">
	</ol>
</section> --}}
@stop
@section('content')
<section class="content" style="padding-top: 0">
	<!-- <div class="col-xs-10">
		<div class="col-md-12 picker" id="weekResult">
		</div>
		<div class="col-md-12">
		</div>
		<div class="col-md-12 picker" id="dateResult" style="margin: 1;">
		</div>
		<div class="col-md-12">
			<br>
		</div>
	</div> -->
	<div class="col-xs-3" style="margin-left: 2%; margin: 0.5%;">
		<!-- <div class="row">
			<span class="text-red"><i class="fa fa-info-circle"></i> Select Other Date</span>
		</div> -->
		<div class="row">
			<div class="col-xs-8">
				<div class="row">
					<div class="input-group date">
						<div class="input-group-addon bg-olive">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control pull-right" id="datepicker" name="datepicker">
					</div>
				</div>
			</div>
			<div class="col-xs-2">
				<button id="search" onClick="searchDate()" class="btn bg-olive">Search</button>
			</div>
		</div>
	</div>
	{{-- <div class="col-xs-2">
		<span class="text-red"><i class="fa fa-info-circle"></i> Select Other Date</span>
	</div> --}}
	<div class="row">
		<input type="hidden" name="dateHidden" value="{{ date('Y-m-d') }}" id="dateHidden">
		<div class="col-md-12">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
					<li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Production Result<br><span class="text-purple">生産実績</span></a></li>
					<li class="vendor-tab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">BI Production Accuracy<br><span class="text-purple">BI週次出荷</span></a></li>
					<li class="vendor-tab"><a href="#tab_3" data-toggle="tab" id="tab_header_3">BI Weekly Shipment<br><span class="text-purple">BI週次出荷</span></a></li>
					{{-- <li class="vendor-tab"><a href="#tab_4" data-toggle="tab" id="tab_header_4">EI Production Result<br><span class="text-purple">EI生産実績</span></a></li> --}}
					<li class="vendor-tab"><a href="#tab_5" data-toggle="tab" id="tab_header_5">EI Production Accuracy<br><span class="text-purple">EI週次出荷</span></a></li>
					<li class="vendor-tab"><a href="#tab_6" data-toggle="tab" id="tab_header_6">EI Weekly Shipment<br><span class="text-purple">EI週次出荷</span></a></li>
					<li class="vendor-tab"><a href="#tab_7" data-toggle="tab" id="tab_header_7">Chorei Text<br><span class="text-purple">朝礼用の生産報告文章</span></a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="tab_1" style="height: 580px;">
						<div class="col-md-12">
							<div class="progress-group" id="progress_div">
								<div class="progress" style="height: 50px; border-style: solid;border-width: 1px;padding: 1px; border-color: #d3d3d3;">
									<span class="progress-text" id="progress_text_production1" style="font-size: 25px; padding-top: 10px;"></span>
									<div class="progress-bar progress-bar-success progress-bar-striped" id="progress_bar_production1" style="font-size: 30px; padding-top: 10px;"></div>
								</div>
							</div>
						</div>
						<div id="container1" style="width:100%; height:530px;"></div>
					</div>
					<div class="tab-pane" id="tab_2" style="height: 580px;">
						<div class="col-md-12">
							<div class="progress-group" id="progress_div">
								<div class="progress" style="height: 50px; border-style: solid;border-width: 1px;padding: 1px; border-color: #d3d3d3;">
									<span class="progress-text" id="progress_text_production2" style="font-size: 25px; padding-top: 10px;"></span>
									<div class="progress-bar progress-bar-success progress-bar-striped" id="progress_bar_production2" style="font-size: 30px; padding-top: 10px;"></div>
								</div>
							</div>
						</div>
						<div id="container2" style="width:100%; height:530px;"></div>
					</div>
					<div class="tab-pane" id="tab_3" style="height: 580px;">
						<div class="col-md-12">
							<div class="progress-group" id="progress_div">
								<div class="progress" style="height: 50px; border-style: solid;border-width: 1px;padding: 1px; border-color: #d3d3d3;">
									<span class="progress-text" id="progress_text_week1" style="font-size: 25px; padding-top: 10px;"></span>
									<div class="progress-bar progress-bar-success progress-bar-striped active" id="progress_bar_week1" style="font-size: 30px; padding-top: 10px;"></div>
								</div>
							</div>
						</div>
						<div id="container3" style="width:100%; height:530px;"></div>
					</div>
					{{-- <div class="tab-pane" id="tab_4">
						<div id="container4" style="width:100%; height:520px;"></div>
					</div> --}}
					<div class="tab-pane" id="tab_5" style="height: 580px;">
						<div class="col-md-12">
							<div class="progress-group" id="progress_div">
								<div class="progress" style="height: 50px; border-style: solid;border-width: 1px;padding: 1px; border-color: #d3d3d3;">
									<span class="progress-text" id="progress_text_production3" style="font-size: 25px; padding-top: 10px;"></span>
									<div class="progress-bar progress-bar-success progress-bar-striped" id="progress_bar_production3" style="font-size: 30px; padding-top: 10px;"></div>
								</div>
							</div>
						</div>
						<div id="container5" style="width:100%; height:530px;"></div>
					</div>
					<div class="tab-pane" id="tab_6" style="height: 580px;">
						<div class="col-md-12">
							<div class="progress-group" id="progress_div">
								<div class="progress" style="height: 50px; border-style: solid;border-width: 1px;padding: 1px; border-color: #d3d3d3;">
									<span class="progress-text" id="progress_text_week2" style="font-size: 25px; padding-top: 10px;"></span>
									<div class="progress-bar progress-bar-success progress-bar-striped" id="progress_bar_week2" style="font-size: 30px; padding-top: 10px;"></div>
								</div>
							</div>
						</div>
						<div id="container6" style="width:100%; height:530px;"></div>
					</div>
					<div class="tab-pane" id="tab_7">
						<table style="width: 100%;">
							<tbody>
								<tr>
									<td style="text-align: left; width: 40%;">
										<p>
											<span style="font-weight: bold; font-size: 16px;">Ohayou Gozaimasu</span>
											<br>
											<span style="font-size: 16px;">Selamat pagi</span>
										</p>
										<p>
											<span style="font-weight: bold; font-size: 16px;"><span style="color: red;" id="production_month_jp">????</span>-gatsu <span style="color: red;" id="production_date_jp">????</span>-nichi made no, seisan no kekka wo hokoku shimasu.</span>
											<br>
											<span style="font-size: 16px;">Diinformasikan hasil produksi sampai tanggal <span style="color: red;" id="production_date_id">????</span></span>
										</p>
										<p>
											<span style="font-weight: bold; font-size: 16px;">Aitemu goto no seisan no kekka wa:</span>
											<br>
											<span style="font-size: 16px;">Hasil kesesuaian per item sebagai berikut :</span>
										</p>
										<p>
											<span style="font-weight: bold; font-size: 16px;">Furuuto wa mainasu <span style="color: red;" id="fl_minus_jp">????</span> setto, purasu <span style="color: red;" id="fl_plus_jp">????</span> setto desu.</span>
											<br>
											<span style="font-size: 16px;">FL minus <span style="color: red;" id="fl_minus_id">????</span> set, plus <span style="color: red;" id="fl_plus_id">????</span> set.</span>
										</p>
										<p>
											<span style="font-weight: bold; font-size: 16px;">Kurarinetto wa mainasu <span style="color: red;" id="cl_minus_jp">????</span> setto, purasu <span style="color: red;" id="cl_plus_jp">????</span> setto desu.</span>
											<br>
											<span style="font-size: 16px;">CL minus <span style="color: red;" id="cl_minus_id">????</span> Set, plus <span style="color: red;" id="cl_plus_id">????</span> set.</span>
										</p>
										<p>
											<span style="font-weight: bold; font-size: 16px;">Aruto Sakkusu wa mainasu <span style="color: red;" id="as_minus_jp">????</span> setto, purasu <span style="color: red;" id="as_plus_jp">????</span> setto desu.</span>
											<br>
											<span style="font-size: 16px;">AS minus <span style="color: red;" id="as_minus_id">????</span> Set, plus <span style="color: red;" id="as_plus_id">????</span> set.</span>
										</p>
										<p>
											<span style="font-weight: bold; font-size: 16px;">Tena Sakkusu wa mainasu <span style="color: red;" id="ts_minus_jp">????</span> setto, purasu <span style="color: red;" id="ts_plus_jp">????</span> setto desu.</span>
											<br>
											<span style="font-size: 16px;">TS minus <span style="color: red;" id="ts_minus_id">????</span> Set, plus <span style="color: red;" id="ts_plus_id">????</span> set.</span>
										</p>
										<p>
											<span style="font-weight: bold; font-size: 16px;">Pianica wa mainasu <span style="color: red;" id="pn_minus_jp">????</span> setto, purasu <span style="color: red;" id="pn_plus_jp">????</span> setto desu.</span>
											<br>
											<span style="font-size: 16px;">PN minus <span style="color: red;" id="pn_minus_id">????</span> Set, plus <span style="color: red;" id="pn_plus_id">????</span> set.</span>
										</p>
										<p>
											<span style="font-weight: bold; font-size: 16px;">Rekoda wa mainasu <span style="color: red;" id="rc_minus_jp">????</span> setto, purasu <span style="color: red;" id="rc_plus_jp">????</span> setto desu.</span>
											<br>
											<span style="font-size: 16px;">RC minus <span style="color: red;" id="rc_minus_id">????</span> Set, plus <span style="color: red;" id="rc_plus_id">????</span> set.</span>
										</p>
										<p>
											<span style="font-weight: bold; font-size: 16px;">Venova wa mainasu <span style="color: red;" id="vn_minus_jp">????</span> setto, purasu <span style="color: red;" id="vn_plus_jp">????</span> setto desu.</span>
											<br>
											<span style="font-size: 16px;">VN minus <span style="color: red;" id="vn_minus_id">????</span> Set, plus <span style="color: red;" id="vn_plus_id">????</span> set.</span>
										</p>
									</td>
									<td style="text-align: left; width: 40%;">
										<p>
											<span style="font-weight: bold; font-size: 16px;"><span style="color: red;" id="export_week_jp">????</span> shuume no// shuuji shukka wo// tassei suru tameni, <span style="color: red;" id="export_month_jp">????</span>-gatsu <span style="color: red;" id="export_date_jp">????</span>-nichi made no // souko e no// shukka kekka wo// houkokushimasu:</span>
											<br>
											<span style="font-size: 16px;">Pengiriman ke gudang sampai tanggal <span style="color: red;" id="export_date_id">????</span> untuk pencapaian target weekly export <span style="color: red;" id="export_week_id">????</span> sebagai berikut:</span>
										</p>
										<p>
											<span style="font-weight: bold; font-size: 16px;">Furuuto wa: <span style="color: red;" id="fl_jp">????</span> paasento</span>
											<br>
											<span style="font-size: 16px;">Flute FG: <span style="color: red;" id="fl_id">????</span> %</span>
										</p>
										<p>
											<span style="font-weight: bold; font-size: 16px;">Kurarinetto wa: <span style="color: red;" id="cl_jp">????</span> paasento</span>
											<br>
											<span style="font-size: 16px;">Clarinet FG: <span style="color: red;" id="cl_id">????</span> %</span>
										</p>
										<p>
											<span style="font-weight: bold; font-size: 16px;">Aruto Sakkusu wa: <span style="color: red;" id="as_jp">????</span> paasento</span>
											<br>
											<span style="font-size: 16px;">Alto Saxophone FG: <span style="color: red;" id="as_id">????</span> %</span>
										</p>
										<p>
											<span style="font-weight: bold; font-size: 16px;">Tena Sakkusu wa: <span style="color: red;" id="ts_jp">????</span> paasento</span>
											<br>
											<span style="font-size: 16px;">Tenor Saxophone FG: <span style="color: red;" id="ts_id">????</span> %</span>
										</p>
										<p>
											<span style="font-weight: bold; font-size: 16px;">Pianika wa: <span style="color: red;" id="pn_jp">????</span> paasento</span>
											<br>
											<span style="font-size: 16px;">Pianica FG: <span style="color: red;" id="pn_id">????</span> %</span>
										</p>
										<p>
											<span style="font-weight: bold; font-size: 16px;">Rekoda wa: <span style="color: red;" id="rc_jp">????</span> paasento</span>
											<br>
											<span style="font-size: 16px;">Recorder FG: <span style="color: red;" id="rc_id">????</span> %</span>
										</p>
										<p>
											<span style="font-weight: bold; font-size: 16px;">Venova wa: <span style="color: red;" id="vn_jp">????</span> paasento</span>
											<br>
											<span style="font-size: 16px;">Venova FG: <span style="color: red;" id="vn_id">????</span> %</span>
										</p>
									</td>
									<td style="text-align: left; width: 20%;">
										<p style="font-weight: bold;">
											*Note : Jika CLARINET FG Sesuai Target<br>
											KURARINETTO WA KEIKAKU DOORI DESU.<br>
											CLARINET FG SESUAI DENGAN TARGET.<br>
										</p>
										<p style="font-weight: bold;">
											*Note : Jika 100%<br>
											KANSEIHIN WA // ZENBU HYAKU PASSSENTO DESU.<br>
											SEMUA FG 100%<br>
										</p>
										<textarea style="width: 100%; height: 200px;">
										</textarea>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>			
		</div>
	</div>
</section>

<div class="modal fade" id="modalResult">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="modalResultTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center>
						<i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i></center>
						<table class="table table-hover table-bordered table-striped" id="tableResult">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>Material</th>
									<th>Description</th>
									<th>Quantity</th>
								</tr>
							</thead>
							<tbody id="modalResultBody">
							</tbody>
							<tfoot style="background-color: RGB(252, 248, 227);">
								<th>Total</th>
								<th></th>
								<th id="modalResultTotal"></th>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>



	@endsection
	@section('scripts')
	<script src="{{ url("js/highcharts.js")}}"></script>
	<script src="{{ url("js/exporting.js")}}"></script>
	<script src="{{ url("js/export-data.js")}}"></script>
	{{-- <script src="{{ url("js/jquery-ui.js")}}"></script> --}}
	<script>
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		jQuery(document).ready(function() {
			$('#datepicker').datepicker({
				autoclose: true,
				todayHighlight: true
			});
			$('body').toggleClass("sidebar-collapse");
			// fillWeek();
			// fillDate();
			fillChart($('#dateHidden').val());
			setInterval(function(){
				fillChart($('#dateHidden').val());
			}, 10000);
		});
		var req = "";

		function canc(){
			req.abort();
		}		

		var th = ['','thousand','million', 'billion','trillion'];

		var dg = ['zero','one','two','three','four', 'five','six','seven','eight','nine']; var tn = ['ten','eleven','twelve','thirteen', 'fourteen','fifteen','sixteen', 'seventeen','eighteen','nineteen']; var tw = ['twenty','thirty','forty','fifty', 'sixty','seventy','eighty','ninety']; function toWords(s){s = s.toString(); s = s.replace(/[\, ]/g,''); if (s != parseFloat(s)) return 'not a number'; var x = s.indexOf('.'); if (x == -1) x = s.length; if (x > 15) return 'too big'; var n = s.split(''); var str = ''; var sk = 0; for (var i=0; i < x; i++) {if ((x-i)%3==2) {if (n[i] == '1') {str += tn[Number(n[i+1])] + ' '; i++; sk=1;} else if (n[i]!=0) {str += tw[n[i]-2] + ' ';sk=1;}} else if (n[i]!=0) {str += dg[n[i]] +' '; if ((x-i)%3==0) str += 'hundred ';sk=1;} if ((x-i)%3==1) {if (sk) str += th[(x-i-1)/3] + ' ';sk=0;}} if (x != s.length) {var y = s.length; str += 'point '; for (var i=x+1; i<y; i++) str += dg[n[i]] +' ';} return str.replace(/\s+/g,' ');}


		$(function() {
			$(document).keydown(function(e) {
				switch(e.which) {
					case 48:
					location.reload(true);
					break;
					case 49:
					$("#tab_header_1").click()
					break;
					case 50:
					$("#tab_header_2").click()
					break;
					case 51:
					$("#tab_header_3").click()
					break;
					case 52:
					$("#tab_header_5").click()
					break;
					case 53:
					$("#tab_header_6").click()
					break;
					case 54:
					$("#tab_header_7").click()
					break;
				}
			});
		});

		function searchDate(){
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
				var date = year + "-" + month + "-" + day;

				return date;
			};


			var date = $.date($('#datepicker').val());

			if($('#datepicker').val() != 0){
				fillChart(date);
			}
		}

		function fillWeek(){
			$.get('{{ url("fetch/daily_production_result_week") }}', function(result, status, xhr){
				req = xhr;
				if(xhr.status == 200){
					if(result.status){
						$('#weekResult').html('');
						var weekData = '';
						$.each(result.weekData, function(key, value) {
							weekData += '<button type="button" class="btn bg-purple btn-lg" id="' + value.week_name + '" onClick="fillDate(id)">' + value.week + '</button>&nbsp;';
						});
						$('#weekResult').append(weekData);
					}
					else{
						alert('Attempt to retrieve data failed');
					}
				}
				else{
					alert('Disconnected from server');
				}
			});
		}

		function fillDate(id){
			$("#weekResult .btn").removeClass("btn-active");
			$("#"+id+"").addClass("btn-active");
			var data = {
				week:id,
			}
			$.get('{{ url("fetch/daily_production_result_date") }}', data, function(result, status, xhr){
				if(xhr.status == 200){
					if(result.status){
						$('#dateResult').html('');
						var dateData = '';
						$.each(result.dateData, function(key, value) {
							dateData += '<button type="button" class="btn bg-olive" id="' + value.week_date + '" onClick="fillChart(id)">' + value.week_date_name + '</button>&nbsp;';
						});
						$('#dateResult').append(dateData);
					}
					else{
						alert('Attempt to retrieve data failed');
					}
				}
				else{
					alert('Disconnected from server');
				}			
			});		
		}

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

		function fillChart(id){
			var now = new Date();
			var now_tgl = addZero(now.getFullYear())+'-'+addZero(now.getMonth()+1)+'-'+addZero(now.getDate());
			var req = new Date(id);
			var req_tgl = addZero(req.getFullYear())+'-'+addZero(req.getMonth()+1)+'-'+addZero(req.getDate());

			if(id != 0){
				$('#dateHidden').val(id);
			}
			var date = id;
			var data = {
				date:date,
			};

			$.get('{{ url("fetch/daily_production_result") }}', data, function(result, status, xhr){
				if(xhr.status == 200){
					if(result.status){
						$('#production_month_jp').text(result.now.substring(5,7));
						$('#production_date_jp').text(result.now.substring(8,10));
						$('#production_date_id').text(result.dateTitle);

						$('#export_week_jp').text(result.weekTitle.replace(/[^0-9\.]/g, ''));
						$('#export_month_jp').text(result.now.substring(5,7));
						$('#export_date_jp').text(result.now.substring(8,10));
						$('#export_date_id').text(result.dateTitle);
						$('#export_week_id').text(result.weekTitle);

						$('#fl_jp').text("????");
						$('#fl_id').text("????");
						$('#cl_jp').text("????");
						$('#cl_id').text("????");
						$('#as_jp').text("????");
						$('#as_id').text("????");
						$('#ts_jp').text("????");
						$('#ts_id').text("????");
						$('#vn_jp').text("????");
						$('#vn_id').text("????");
						$('#rc_jp').text("????");
						$('#rc_id').text("????");
						$('#pn_jp').text("????");
						$('#pn_id').text("????");


						// Progres bar hari kerja/minggu
						for (var i = 1; i < 3; i++) {
							var persen = 0;

							if(req.getDay() == 0){
								persen = 20;
								$('#progress_bar_week'+i).css('font-size', '25px');
								$('#progress_bar_week'+i).addClass('active');		
							}
							else if(req.getDay() == 1){
								persen = 40;
								$('#progress_bar_week'+i).css('font-size', '25px');
								$('#progress_bar_week'+i).addClass('active');		
							}
							else if(req.getDay() == 2){
								persen = 60;
								$('#progress_bar_week'+i).css('font-size', '30px');
								$('#progress_bar_week'+i).addClass('active');
							}
							else if(req.getDay() == 3){
								persen = 80;
								$('#progress_bar_week'+i).css('font-size', '30px');
								$('#progress_bar_week'+i).addClass('active');
							}
							else if(req.getDay() == 4){
								persen = 100;
								$('#progress_bar_week'+i).css('font-size', '30px');
								$('#progress_bar_week'+i).removeClass('active');
							}
							else if(req.getDay() == 5){
								persen = 20;
								$('#progress_bar_week'+i).css('font-size', '30px');
								$('#progress_bar_week'+i).addClass('active');
							}	
							else if(req.getDay() == 6){
								persen = 20;
								$('#progress_bar_week'+i).css('font-size', '30px');
								$('#progress_bar_week'+i).addClass('active');
							}

							if(persen <= 20){
								$('#progress_bar_week'+i).html("Working Time : "+persen+"%");
							}
							else{
								$('#progress_bar_week'+i).html("Week's Working Time : "+persen+"%");
							}

							$('#progress_bar_week'+i).css('width', persen+'%');
							$('#progress_bar_week'+i).css('color', 'white');
							$('#progress_bar_week'+i).css('font-weight', 'bold');
							
						}

						// Progres bar jam kerja/hari
						for (var i = 1; i < 4; i++) {
							if(now_tgl == req_tgl){
								if(now.getHours() < 7){
									$('#progress_bar_production'+i).append().empty();
									$('#progress_text_production'+i).html("Today's Working Time : 0%");
									$('#progress_bar_production'+i).css('width', '0%');
									$('#progress_bar_production'+i).css('color', 'white');
									$('#progress_bar_production'+i).css('font-weight', 'bold');
								}
								else if((now.getHours() >= 16) && (now.getDay() != 5)){
									$('#progress_text_production'+i).append().empty();
									$('#progress_bar_production'+i).html("Today's Working Time : 100%");
									$('#progress_bar_production'+i).css('width', '100%');
									$('#progress_bar_production'+i).css('color', 'white');
									$('#progress_bar_production'+i).css('font-weight', 'bold');
									$('#progress_bar_production'+i).removeClass('active');
								}
								else if(now.getDay() == 5){
									$('#progress_text_production'+i).append().empty();
									var total = 570;
									var now_menit = ((now.getHours()-7)*60) + now.getMinutes();
									var persen = (now_menit/total) * 100;
									if(now.getHours() >= 7 && now_menit < total){
										if(persen > 24){
											if(persen > 32){
												$('#progress_bar_production'+i).html("Today's Working Time : "+persen.toFixed(2)+"%");
											}
											else{
												$('#progress_bar_production'+i).html("Working Time : "+persen.toFixed(2)+"%");
											}	
										}
										else{
											$('#progress_bar_production'+i).html(persen.toFixed(2)+"%");
										}
										$('#progress_bar_production'+i).css('width', persen+'%');
										$('#progress_bar_production'+i).addClass('active');

									}
									else if(now_menit >= total){
										$('#progress_bar_production'+i).html("Today's Working Time : 100%");
										$('#progress_bar_production'+i).css('width', '100%');
										$('#progress_bar_production'+i).removeClass('active');

									}
									$('#progress_bar_production'+i).css('color', 'white');
									$('#progress_bar_production'+i).css('font-weight', 'bold');
								}
								else{
									$('#progress_text_production'+i).append().empty();
									var total = 540;
									var now_menit = ((now.getHours()-7)*60) + now.getMinutes();
									var persen = (now_menit/total) * 100;
									if(now.getHours() >= 7 && now_menit < total){
										if(persen > 24){
											if(persen > 32){
												$('#progress_bar_production'+i).html("Today's Working Time : "+persen.toFixed(2)+"%");
											}
											else{
												$('#progress_bar_production'+i).html("Working Time : "+persen.toFixed(2)+"%");
											}	
										}
										else{
											$('#progress_bar_production'+i).html(persen.toFixed(2)+"%");
										}
										$('#progress_bar_production'+i).css('width', persen+'%');
										$('#progress_bar_production'+i).addClass('active');
									}
									else if(now_menit >= total){
										$('#progress_bar_production'+i).html("Today's Working Time : 100%");
										$('#progress_bar_production'+i).css('width', '100%');
										$('#progress_bar_production'+i).removeClass('active');
									}
									
									$('#progress_bar_production'+i).css('font-weight', 'bold');
									$('#progress_bar_production'+i).addClass('active');
								}
							}
							else if(now > req){
								$('#progress_text_production'+i).append().empty();
								$('#progress_bar_production'+i).html("Today's Working Time : 100%");
								$('#progress_bar_production'+i).css('width', '100%');
								$('#progress_bar_production'+i).css('color', 'white');
								$('#progress_bar_production'+i).css('font-weight', 'bold');
								$('#progress_bar_production'+i).removeClass('active');
							}
							else{
								$('#progress_bar_production'+i).append().empty();
								$('#progress_text_production'+i).html("Today's Working Time : 0%");
								$('#progress_bar_production'+i).css('width', '0%');
								$('#progress_bar_production'+i).css('color', 'white');
								$('#progress_bar_production'+i).css('font-weight', 'bold');
							}							
						}
						
						$('#last_update').html('<b>Last Updated: '+ getActualFullDate() +'</b>');
						var data = result.chartResult1;
						var xAxis = []
						, planCount = []
						, actualCount = []
						, xAxisEI = []
						, planCountEI = []
						, actualCountEI = []

						$("#dateResult .btn").removeClass("btn-active");
						$("#"+date+"").addClass("btn-active");

						for (i = 0; i < data.length; i++) {
						// if(jQuery.inArray(data[i].hpl, ['CLFG', 'ASFG', 'TSFG', 'FLFG']) !== -1){
							xAxis.push(data[i].hpl);
							planCount.push(data[i].plan);
							actualCount.push(data[i].actual);							
						// }
						// if(jQuery.inArray(data[i].hpl, ['RC', 'VENOVA', 'PN']) !== -1){
						// 	xAxisEI.push(data[i].hpl);
						// 	planCountEI.push(data[i].plan);
						// 	actualCountEI.push(data[i].actual);
						// }
					}

					var yAxisLabels = [0,25,50,75,110];
					Highcharts.chart('container1', {
						colors: ['rgba(255, 0, 0, 0.25)','rgba(75, 30, 120, 0.70)'],
						chart: {
							type: 'column',
							backgroundColor: null
						},
						legend: {
							enabled:true,
							itemStyle: {
								fontSize:'20px',
								font: '20pt Trebuchet MS, Verdana, sans-serif',
								color: '#000000'
							}
						},
						credits: {
							enabled: false
						},
						title: {
							text: '<span style="font-size: 2vw;">Production Result ('+ result.dateTitle +')</span><br><span style="color: rgba(96, 92, 168);"> On '+ result.week +' ('+ result.week_min_max[0].min_date +'-'+ result.week_min_max[0].max_date +')</span>',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: xAxis,
							labels: {
								style: {
									color: 'rgba(75, 30, 120)',
									fontSize: '30px',
									fontWeight: 'bold'
								}
							}
						},
						yAxis: {
							tickPositioner: function() {
								return yAxisLabels;
							},
							labels: {
								enabled:false
							},
							min: 0,
							title: {
								text: ''
							},
							stackLabels: {
								format: 'Total: {total:,.0f}set(s)',
								enabled: true,
								style: {
									fontWeight: 'bold',
									color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
								}
							}
						},
						tooltip: {
							headerFormat: '<b>{point.x}</b><br/>',
							pointFormat: '{series.name}: {point.y}set(s) {point.percentage:.0f}%'
						},
						plotOptions: {
							column: {
								minPointLength: 1,
								pointPadding: 0.2,
								size: '95%',
								borderWidth: 0,
								events: {
									legendItemClick: function () {
										return false; 
									}
								},
								animation:{
									duration:0
								}
							},
							series: {
								// pointPadding: 0.7,
								groupPadding: -0.2,
								// pointWidth: 200,
								shadow: false,
								borderColor: '#303030',
								cursor: 'pointer',
								stacking: 'percent',
								point: {
									events: {
										click: function () {
											modalResult(this.category, this.series.name, result.now, result.first, result.last);
										}
									}
								},
								dataLabels: {
									format: '{point.percentage:.0f}%',
									enabled: true,
									color: '#000000',
									style: {
										textOutline: false,
										fontWeight: 'bold',
										fontSize: '3vw'
									}
								}
							}
						},
						series: [{
							name: 'Plan',
							data: planCount
						}, {
							name: 'Actual',
							data: actualCount
						}]
					});

					// Highcharts.chart('container4', {
					// 	colors: ['rgba(255, 0, 0, 0.25)','rgba(75, 30, 120, 0.70)'],
					// 	chart: {
					// 		type: 'column',
					// 		backgroundColor: null
					// 	},
					// 	legend: {
					// 		enabled:true,
					// 		itemStyle: {
					// 			fontSize:'20px',
					// 			font: '20pt Trebuchet MS, Verdana, sans-serif',
					// 			color: '#000000'
					// 		}
					// 	},
					// 	credits: {
					// 		enabled: false
					// 	},
					// 	title: {
					// 		text: '<span style="font-size: 30px;">Production Result</span><br><span style="color: rgba(96, 92, 168);">'+ result.week +'</span> (<span style="color: rgba(61, 153, 112);">'+ result.dateTitle +'</span>)'
					// 		// style: {
					// 		// 	fontSize: '30px',
					// 		// 	fontWeight: 'bold'
					// 		// }
					// 	},
					// 	xAxis: {
					// 		categories: xAxisEI,
					// 		labels: {
					// 			style: {
					// 				color: 'rgba(75, 30, 120)',
					// 				fontSize: '20px',
					// 				fontWeight: 'bold'
					// 			}
					// 		}
					// 	},
					// 	yAxis: {
					// 		tickPositioner: function() {
					// 			return yAxisLabels;
					// 		},
					// 		labels: {
					// 			enabled:false
					// 		},
					// 		min: 0,
					// 		title: {
					// 			text: ''
					// 		},
					// 		stackLabels: {
					// 			format: 'Total: {total:,.0f}set(s)',
					// 			enabled: true,
					// 			style: {
					// 				fontWeight: 'bold',
					// 				color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
					// 			}
					// 		}
					// 	},
					// 	tooltip: {
					// 		headerFormat: '<b>{point.x}</b><br/>',
					// 		pointFormat: '{series.name}: {point.y}set(s) {point.percentage:.0f}%'
					// 	},
					// 	plotOptions: {
					// 		column: {
					// 			minPointLength: 1,
					// 			pointPadding: 0.2,
					// 			size: '95%',
					// 			borderWidth: 0,
					// 			events: {
					// 				legendItemClick: function () {
					// 					return false; 
					// 				}
					// 			},
					// 			animation:{
					// 				duration:0
					// 			}
					// 		},
					// 		series: {
					// 			pointPadding: 0.95,
					// 			groupPadding: 0.95,
					// 			borderWidth: 0.95,
					// 			shadow: false,
					// 			borderColor: '#303030',
					// 			cursor: 'pointer',
					// 			stacking: 'percent',
					// 			point: {
					// 				events: {
					// 					click: function () {
					// 						modalResult(this.category, this.series.name, result.now, result.first, result.last);
					// 					}
					// 				}
					// 			},
					// 			dataLabels: {
					// 				format: '{point.percentage:.0f}%',
					// 				enabled: true,
					// 				color: '#000000',
					// 				style: {
					// 					textOutline: false,
					// 					fontWeight: 'bold',
					// 					fontSize: '30px'
					// 				}
					// 			}
					// 		}
					// 	},
					// 	series: [{
					// 		name: 'Plan',
					// 		data: planCountEI
					// 	}, {
					// 		name: 'Actual',
					// 		data: actualCountEI
					// 	}]
					// });

					var data2 = result.chartResult2;
					var xAxis2 = []
					, plusCount = []
					, minusCount = []
					, xAxis2EI = []
					, plusCountEI = []
					, minusCountEI = []

					for (i = 0; i < data2.length; i++) {
						if(jQuery.inArray(data2[i].hpl, ['CLFG', 'ASFG', 'TSFG', 'FLFG']) !== -1){
							xAxis2.push(data2[i].hpl);
							plusCount.push(data2[i].plus);
							minusCount.push(data2[i].minus);
						}
						if(jQuery.inArray(data2[i].hpl, ['VENOVA', 'RC', 'PN']) !== -1){
							xAxis2EI.push(data2[i].hpl);
							plusCountEI.push(data2[i].plus);
							minusCountEI.push(data2[i].minus);
						}
						if(data2[i].hpl == "FLFG"){
							$('#fl_minus_jp').text(data2[i].minus);
							$('#fl_plus_jp').text(data2[i].plus);
							$('#fl_minus_id').text(data2[i].minus);
							$('#fl_plus_id').text(data2[i].plus);
						}
						if(data2[i].hpl == "CLFG"){
							$('#cl_minus_jp').text(data2[i].minus);
							$('#cl_plus_jp').text(data2[i].plus);
							$('#cl_minus_id').text(data2[i].minus);
							$('#cl_plus_id').text(data2[i].plus);
						}
						if(data2[i].hpl == "ASFG"){
							$('#as_minus_jp').text(data2[i].minus);
							$('#as_plus_jp').text(data2[i].plus);
							$('#as_minus_id').text(data2[i].minus);
							$('#as_plus_id').text(data2[i].plus);
						}
						if(data2[i].hpl == "TSFG"){
							$('#ts_minus_jp').text(data2[i].minus);
							$('#ts_plus_jp').text(data2[i].plus);
							$('#ts_minus_id').text(data2[i].minus);
							$('#ts_plus_id').text(data2[i].plus);
						}
						if(data2[i].hpl == "VENOVA"){
							$('#vn_minus_jp').text(data2[i].minus);
							$('#vn_plus_jp').text(data2[i].plus);
							$('#vn_minus_id').text(data2[i].minus);
							$('#vn_plus_id').text(data2[i].plus);
						}
						if(data2[i].hpl == "RC"){
							$('#rc_minus_jp').text(data2[i].minus);
							$('#rc_plus_jp').text(data2[i].plus);
							$('#rc_minus_id').text(data2[i].minus);
							$('#rc_plus_id').text(data2[i].plus);
						}
						if(data2[i].hpl == "PN"){
							$('#pn_minus_jp').text(data2[i].minus);
							$('#pn_plus_jp').text(data2[i].plus);
							$('#pn_minus_id').text(data2[i].minus);
							$('#pn_plus_id').text(data2[i].plus);
						}
					}

					Highcharts.chart('container2', {
						colors: ['rgba(75, 30, 120, 0.60)', 'rgba(255, 0, 0, 0.60)'],
						chart: {
							type: 'column'
						},
						title: {
							text: '<span style="font-size: 2vw;">Production Accuracy ('+ result.dateTitle +')</span><br><span style="color: rgba(96, 92, 168);"> On '+ result.week +' ('+ result.week_min_max[0].min_date +'-'+ result.week_min_max[0].max_date +')</span>',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: xAxis2,
							labels: {
								style: {
									color: 'rgba(75, 30, 120)',
									fontSize: '30px',
									fontWeight: 'bold'
								}
							}
						},
						yAxis: {
							title: {
								text: 'Set(s)'
							}
						},
						legend: {
							enabled:true,
							itemStyle: {
								fontSize:'20px',
								font: '20pt Trebuchet MS, Verdana, sans-serif',
								color: '#000000'
							}
						},
						credits: {
							enabled: false
						},
						plotOptions: {
							column: {
								// minPointLength: 2,
								pointPadding: 0,
								size: '100%',
								borderWidth: 1
							},
							series: {
								groupPadding: 0.1,
								borderColor: '#303030',
								cursor: 'pointer',
								dataLabels: {
									enabled: true,
									format: '{point.y:,.0f}',
									style:{
										fontSize:'3vw',
										color:'black',
										textOutline: false
									}
								},
								animation:{
									duration:0
								},
								point: {
									events: {
										click: function () {
											modalAccuracy(this.category, this.series.name, result.now, result.first, result.last);
										}
									}
								},
							}
						},
						series: [{
							name: 'Plus',
							data: plusCount
						}, {
							name: 'Minus',
							data: minusCount
						}]
					});

					Highcharts.chart('container5', {
						colors: ['rgba(75, 30, 120, 0.60)', 'rgba(255, 0, 0, 0.60)'],
						chart: {
							type: 'column'
						},
						title: {
							text: '<span style="font-size: 2vw;">Production Result ('+ result.dateTitle +')</span><br><span style="color: rgba(96, 92, 168);"> On '+ result.week +' ('+ result.week_min_max[0].min_date +'-'+ result.week_min_max[0].max_date +')</span>',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: xAxis2EI,
							labels: {
								style: {
									color: 'rgba(75, 30, 120)',
									fontSize: '30px',
									fontWeight: 'bold'
								}
							}
						},
						yAxis: {
							title: {
								text: 'Set(s)'
							}
						},
						legend: {
							enabled:true,
							itemStyle: {
								fontSize:'20px',
								font: '20pt Trebuchet MS, Verdana, sans-serif',
								color: '#000000'
							}
						},
						credits: {
							enabled: false
						},
						plotOptions: {
							column: {
								// minPointLength: 2,
								pointPadding: 0,
								size: '100%',
								borderWidth: 1
							},
							series: {
								groupPadding: 0.1,
								borderColor: '#303030',
								cursor: 'pointer',
								dataLabels: {
									enabled: true,
									format: '{point.y:,.0f}',
									style:{
										fontSize:'3vw',
										color:'black',
										textOutline: false
									}
								},
								animation:{
									duration:0
								},
								point: {
									events: {
										click: function () {
											modalAccuracy(this.category, this.series.name, result.now, result.first, result.last);
										}
									}
								},
							}
						},
						series: [{
							name: 'Plus',
							data: plusCountEI
						}, {
							name: 'Minus',
							data: minusCountEI
						}]
					});

					var data3 = result.chartResult3;
					var xAxis3 = []
					, planBLCount = []
					, actualBLCount = []
					, xAxis3EI = []
					, planBLCountEI = []
					, actualBLCountEI = []

					for (i = 0; i < data3.length; i++) {
						if(jQuery.inArray(data3[i].hpl, ['CLFG', 'ASFG', 'TSFG', 'FLFG']) !== -1){
							xAxis3.push(data3[i].hpl);
							planBLCount.push(data3[i].prc_plan);
							actualBLCount.push(data3[i].prc_actual);
						}
						if(jQuery.inArray(data3[i].hpl, ['VENOVA', 'RC', 'PN']) !== -1){
							xAxis3EI.push(data3[i].hpl);
							planBLCountEI.push(data3[i].prc_plan);
							actualBLCountEI.push(data3[i].prc_actual);
						}

						if(data3[i].hpl == "FLFG"){
							$('#fl_jp').text(Math.round(data3[i].prc_actual*100));
							$('#fl_id').text(Math.round(data3[i].prc_actual*100));
						}
						if(data3[i].hpl == "CLFG"){
							$('#cl_jp').text(Math.round(data3[i].prc_actual*100));
							$('#cl_id').text(Math.round(data3[i].prc_actual*100));
						}
						if(data3[i].hpl == "ASFG"){
							$('#as_jp').text(Math.round(data3[i].prc_actual*100));
							$('#as_id').text(Math.round(data3[i].prc_actual*100));
						}
						if(data3[i].hpl == "TSFG"){	
							$('#ts_jp').text(Math.round(data3[i].prc_actual*100));
							$('#ts_id').text(Math.round(data3[i].prc_actual*100));
						}
						if(data3[i].hpl == "VENOVA"){
							$('#vn_jp').text(Math.round(data3[i].prc_actual*100));
							$('#vn_id').text(Math.round(data3[i].prc_actual*100));
						}
						if(data3[i].hpl == "RC"){
							$('#rc_jp').text(Math.round(data3[i].prc_actual*100));
							$('#rc_id').text(Math.round(data3[i].prc_actual*100));
						}
						if(data3[i].hpl == "PN"){
							$('#pn_jp').text(Math.round(data3[i].prc_actual*100));
							$('#pn_id').text(Math.round(data3[i].prc_actual*100));
						}
					}

					Highcharts.chart('container3', {
						colors: ['rgba(255, 0, 0, 0.15)','rgba(255, 69, 0, 0.70)'],
						chart: {
							type: 'column',
							backgroundColor: null
						},
						legend: {
							enabled:true,
							itemStyle: {
								fontSize:'20px',
								font: '20pt Trebuchet MS, Verdana, sans-serif',
								color: '#000000'
							}
						},
						credits: {
							enabled: false
						},
						title: {
							text: '<span style="font-size: 2vw;">Weekly Shipment ETD YMPI</span><br><span style="color: rgba(96, 92, 168);"> On '+ result.week +' ('+ result.week_min_max[0].min_date +'-'+ result.week_min_max[0].max_date +')</span>',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: xAxis3,
							labels: {
								style: {
									color: 'rgba(75, 30, 120)',
									fontSize: '30px',
									fontWeight: 'bold'
								}
							}
						},
						yAxis: {
							tickPositioner: function() {
								return yAxisLabels;
							},
							labels: {
								enabled:false
							},
							min: 0,
							title: {
								text: ''
							}
							// stackLabels: {
							// 	format: 'Total: {total:,.0f}set(s)',
							// 	enabled: true,
							// 	style: {
							// 		fontWeight: 'bold',
							// 		color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
							// 	}
							// }
						},
						tooltip: {
							headerFormat: '<b>{point.x}</b><br/>',
							pointFormat: '{series.name}: {point.percentage:.0f}%'
						},
						plotOptions: {
							column: {
								minPointLength: 1,
								pointPadding: 0.2,
								size: '95%',
								borderWidth: 0,
								events: {
									legendItemClick: function () {
										return false; 
									}
								}
							},
							series: {
								animation:{
									duration:0
								},
								// pointPadding: 0.95,
								groupPadding: -0.2,
								// borderWidth: 0.95,
								shadow: false,
								borderColor: '#303030',
								cursor: 'pointer',
								stacking: 'percent',
								point: {
									events: {
										click: function () {
											modalBL(this.category , this.series.name, result.weekTitle, result.now);
										}
									}
								},
								dataLabels: {
									format: '{point.percentage:.0f}%',
									enabled: true,
									color: '#000000',
									style: {
										textOutline: false,
										fontWeight: 'bold',
										fontSize: '3vw'
									}
								}
							}
						},
						series: [{
							name: 'Plan',
							data: planBLCount
						}, {
							name: 'Actual',
							data: actualBLCount
						}]
					});

					Highcharts.chart('container6', {
						colors: ['rgba(255, 0, 0, 0.15)','rgba(255, 69, 0, 0.70)'],
						chart: {
							type: 'column',
							backgroundColor: null
						},
						legend: {
							enabled:true,
							itemStyle: {
								fontSize:'20px',
								font: '20pt Trebuchet MS, Verdana, sans-serif',
								color: '#000000'
							}
						},
						credits: {
							enabled: false
						},
						title: {
							text: '<span style="font-size: 2vw;">Weekly Shipment ETD YMPI</span><br><span style="color: rgba(96, 92, 168);"> On '+ result.week +' ('+ result.week_min_max[0].min_date +'-'+ result.week_min_max[0].max_date +')</span>',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: xAxis3EI,
							labels: {
								style: {
									color: 'rgba(75, 30, 120)',
									fontSize: '30px',
									fontWeight: 'bold'
								}
							}
						},
						yAxis: {
							tickPositioner: function() {
								return yAxisLabels;
							},
							labels: {
								enabled:false
							},
							min: 0,
							title: {
								text: ''
							}
							// stackLabels: {
							// 	format: 'Total: {total:,.0f}set(s)',
							// 	enabled: true,
							// 	style: {
							// 		fontWeight: 'bold',
							// 		color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
							// 	}
							// }
						},
						tooltip: {
							headerFormat: '<b>{point.x}</b><br/>',
							pointFormat: '{series.name}: {point.percentage:.0f}%'
						},
						plotOptions: {
							column: {
								minPointLength: 1,
								pointPadding: 0.2,
								size: '95%',
								borderWidth: 0,
								events: {
									legendItemClick: function () {
										return false; 
									}
								}
							},
							series: {
								animation:{
									duration:0
								},
								// pointPadding: 0.95,
								groupPadding: -0.2,
								// borderWidth: 0.95,
								shadow: false,
								borderColor: '#303030',
								cursor: 'pointer',
								stacking: 'percent',
								point: {
									events: {
										click: function () {
											modalBL(this.category , this.series.name, result.weekTitle, result.now);
										}
									}
								},
								dataLabels: {
									format: '{point.percentage:.0f}%',
									enabled: true,
									color: '#000000',
									style: {
										textOutline: false,
										fontWeight: 'bold',
										fontSize: '3vw'
									}
								}
							}
						},
						series: [{
							name: 'Plan',
							data: planBLCountEI
						}, {
							name: 'Actual',
							data: actualBLCountEI
						}]
					});
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				alert('Disconnected from server');
			}			
		});	
}

function modalResult(hpl, name, now, first, last){
	$('#modalResult').modal('show');
	$('#loading').show();
	$('#modalResultTitle').hide();
	$('#tableResult').hide();

	var data = {
		hpl:hpl,
		name:name,
		now:now,
		first:first,
		last:last,
	}
	$.get('{{ url("fetch/production_result_modal") }}', data, function(result, status, xhr){
		if(xhr.status == 200){
			if(result.status){
				$('#modalResultTitle').html('');
				$('#modalResultTitle').html('Detail of '+ hpl +' '+ name);
				$('#modalResultBody').html('');
				var resultData = '';
				var resultTotal = 0;
				$.each(result.resultData, function(key, value) {
					resultData += '<tr>';
					resultData += '<td>'+ value.material_number +'</td>';
					resultData += '<td>'+ value.material_description +'</td>';
					resultData += '<td>'+ value.quantity.toLocaleString() +'</td>';
					resultData += '</tr>';
					resultTotal += value.quantity;
				});
				$('#modalResultBody').append(resultData);
				$('#modalResultTotal').html('');
				$('#modalResultTotal').append(resultTotal.toLocaleString());

				$('#loading').hide();
				$('#modalResultTitle').show();
				$('#tableResult').show();
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		}
		else{
			alert('Disconnected from server');
		}
	});
}

function modalAccuracy(hpl, name, now, first, last){
	$('#modalResult').modal('show');
	$('#loading').show();
	$('#modalResultTitle').hide();
	$('#tableResult').hide();
	var data = {
		hpl:hpl,
		name:name,
		now:now,
		first:first,
		last:last,
	}
	$.get('{{ url("fetch/production_accuracy_modal") }}', data, function(result, status, xhr){
		console.log(status);
		console.log(result);
		console.log(xhr);
		if(xhr.status == 200){
			if(result.status){
				$('#modalResultTitle').html('');
				$('#modalResultTitle').html('Detail of '+ hpl +' '+ name);
				$('#modalResultBody').html('');
				var accuracyData = '';
				var accuracyTotal = 0;
				$.each(result.accuracyData, function(key, value) {
					if(name == 'Minus' && value.minus < 0){
						accuracyData += '<tr>';
						accuracyData += '<td>'+ value.material_number +'</td>';
						accuracyData += '<td>'+ value.material_description +'</td>';
						accuracyData += '<td>'+ value.minus.toLocaleString() +'</td>';
						accuracyData += '</tr>';
						accuracyTotal += value.minus;
					}
					if(name == 'Plus' && value.plus > 0){
						accuracyData += '<tr>';
						accuracyData += '<td>'+ value.material_number +'</td>';
						accuracyData += '<td>'+ value.material_description +'</td>';
						accuracyData += '<td>'+ value.plus.toLocaleString() +'</td>';
						accuracyData += '</tr>';
						accuracyTotal += value.plus;
					}
				});
				$('#modalResultBody').append(accuracyData);
				$('#modalResultTotal').html('');
				$('#modalResultTotal').append(accuracyTotal.toLocaleString());

				$('#loading').hide();
				$('#modalResultTitle').show();
				$('#tableResult').show();
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		}
		else{
			alert('Disconnected from server');
		}
	});
}

function modalBL(hpl, name, week, date){
	$('#modalResult').modal('show');
	$('#loading').show();
	$('#modalResultTitle').hide();
	$('#tableResult').hide();
	var data = {
		hpl:hpl,
		name:name,
		week:'W'+week.substring(5),
		date:date,
	}
	$.get('{{ url("fetch/production_bl_modal") }}', data, function(result, status, xhr){
		console.log(status);
		console.log(result);
		console.log(xhr);
		if(xhr.status == 200){
			if(result.status){
				$('#modalResultTitle').html('');
				$('#modalResultTitle').html('Detail of '+ hpl +' '+ name);
				$('#modalResultBody').html('');
				var blData = '';
				var blTotal = 0;
				$.each(result.blData, function(key, value) {
					blData += '<tr>';
					blData += '<td>'+ value.material_number +'</td>';
					blData += '<td>'+ value.material_description +'</td>';
					blData += '<td>'+ value.quantity.toLocaleString() +'</td>';
					blData += '</tr>';
					blTotal += value.quantity;
				});
				$('#modalResultBody').append(blData);
				$('#modalResultTotal').html('');
				$('#modalResultTotal').append(blTotal.toLocaleString());
				
				$('#loading').hide();
				$('#modalResultTitle').show();
				$('#tableResult').show();
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		}
		else{
			alert('Disconnected from server');
		}
	});
}

</script>
@endsection