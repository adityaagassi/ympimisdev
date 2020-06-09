@extends('layouts.master')
@section('stylesheets')
<style type="text/css">	
	thead>tr>th{
		text-align:center;
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
	#loading { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple"> {{ $title_jp }}</span></small>
	</h1>

	<ol class="breadcrumb">
		<li>
			<div class="col-xs-7" style="padding-right: 0;">
				<input type="text" class="form-control pull-right" id="datepicker" name="datepicker">
			</div>
			<div class="col-xs-2">
				<button class="btn btn-info" onclick="fetchChart()">Update Chart</button>
			</div>
		</li>
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="col-sx-12">
				<span style="font-weight: bold; font-size: 2vw;"><i class="fa fa-arrow-down"></i>右下端の各地域のグラフを一つにまとめたもの</span>
				<div id="new_infected" style="margin-bottom: 20px;"></div>
			</div>	
			<input type="hidden" id="date_now">
			<input type="hidden" id="date_yesterday">

			{{-- INDONESIA --}}

			<div class="col-xs-12" style="margin-bottom: 10px;">
				<center>
					<button class="btn btn-lg" style="width: 15%; border-color: black; font-weight: bold; background-color: white;" onclick="info('id')" id="btn_id">INDONESIA<br>インドネシア</button>
					<button class="btn btn-lg" style="width: 15%; border-color: black; font-weight: bold; background-color: white;" onclick="info('jkt')" id="btn_jkt">JAKARTA<br>ジャカルタ</button>
					<button class="btn btn-lg" style="width: 15%; border-color: black; font-weight: bold; background-color: white;" onclick="info('bks')" id="btn_bks">BEKASI<br>ベカシ’</button>
					<button class="btn btn-lg" style="width: 15%; border-color: black; font-weight: bold; background-color: white;" onclick="info('sby')" id="btn_sby">SURABAYA<br>スラバヤ</button>
					<button class="btn btn-lg" style="width: 15%; border-color: black; font-weight: bold; background-color: white;" onclick="info('psr')" id="btn_psr">PASURUAN<br>パスルアン</button>
				</center>
			</div>

			<div class="col-xs-12" style="padding-left: 0" id="indonesia">
				<div class="box">
					<div class="box-header with-border">
						<h4 class="box-title">
							<i class="fa fa-square"></i><b>「インドネシア国全体」</b>
						</h4>
					</div>
					<div class="box-body">
						<table class="table table-bordered" style="margin-bottom: 10px;">
							<thead>
								<tr>
									<th style="width:5%;">現在感染者数<br>(Genzai kansen-shasū)<br><br>Number Currently infected</th>
									<th style="width:5%;">新規感染者数<br>(Shinki kansenshasū)<br><br>Number of new infections</th>
									<th style="width:5%;">累計感染者数<br>(Ruikei kansenshasū)<br><br>Cumulative number of infected people</th>
								</tr>
								<tr>
									<th id="id_curr" style="background-color: rgb(255, 242, 204)">Not Updated</th>
									<th id="id_new">Not Updated</th>
									<th id="id_cum" style="background-color: rgb(255, 242, 204)">Not Updated</th>
								</tr>
								<tr>
									<th style="width:5%;">前日比-<br>(Zenjitsu-hi)<br><br>rasio hari sebelumnya</th>
									<th style="width:5%;">前日比-<br>(Zenjitsu-hi)<br><br>rasio hari sebelumnya</th>
									<th style="width:5%;"></th>
								</tr>
								<tr>
									<th id="id_rasio_1">Not Updated</th>
									<th id="id_rasio_2">Not Updated</th>
									<th></th>
								</tr>
								<tr>
									<th style="width:5%;">死亡者数<br>(Shibōshasū)<br><br>Number of deaths</th>
									<th style="width:5%;"></th>
									<th style="width:5%;">退院者数<br>(Taiin shasū)<br><br>Number of discharge</th>
								</tr>
								<tr>
									<th id="id_death" style="background-color: rgb(255, 242, 204)">Not Updated</th>
									<th></th>
									<th id="id_discharge" style="background-color: rgb(255, 217, 102)">Not Updated</th>
								</tr>
							</thead>
						</table>


						<div class="nav-tabs-custom">
							<ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
								<li class="vendor-tab active"><a href="#tab_id_1" data-toggle="tab" id="tab_header_id_1">
								現在</a>
							</li>
							<li class="vendor-tab"><a href="#tab_id_2" data-toggle="tab" id="tab_header_id_2">
							新規</a>
						</li>
						<li class="vendor-tab"><a href="#tab_id_3" data-toggle="tab" id="tab_header_id_3">
						累計</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="tab_id_1">
						<div style="background-color: #ff7043; font-weight: bold; font-size: 1.5vw; text-align: center;">
							現在の感染者数推移　（累計感染者数－回復者数－死亡者数）
						</div>
						<div id="id_infected" style="margin-bottom: 20px;"></div>
					</div>

					<div class="tab-pane" id="tab_id_2">
						<div style="background-color: #3f51b5; font-weight: bold; font-size: 1.5vw; text-align: center; color: white;">
							新規感染者数
						</div>
						<div id="id_new_infected" style="margin-bottom: 20px;"></div>
					</div>

					<div class="tab-pane" id="tab_id_3">
						<div style="background-color: #ffff00; font-weight: bold; font-size: 1.5vw; text-align: center;">
							過去1週間の10万人あたりの新規感染者数
						</div>
						<div id="id_series" style="margin-bottom: 20px;"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{{-- JAKARTA --}}

<div class="col-xs-12" style="padding-left: 0" id="jakarta">
	<div class="box">
		<div class="box-header with-border">
			<h4 class="box-title">
				<i class="fa fa-square"></i><b>「ジャカルタ特別州」（YI・YMMI、駐在員居住地区）</b>
			</h4>
		</div>
		<div class="box-body">
			<table class="table table-bordered" style="margin-bottom: 10px;">
				<thead>
					<tr>
						<th style="width:5%;">現在感染者数<br>(Genzai kansen-shasū)<br><br>Number Currently infected</th>
						<th style="width:5%;">新規感染者数<br>(Shinki kansenshasū)<br><br>Number of new infections</th>
						<th style="width:5%;">累計感染者数<br>(Ruikei kansenshasū)<br><br>Cumulative number of infected people</th>
					</tr>
					<tr>
						<th id="jkt_curr" style="background-color: rgb(255, 242, 204)">Not Updated</th>
						<th id="jkt_new">Not Updated</th>
						<th id="jkt_cum" style="background-color: rgb(255, 242, 204)">Not Updated</th>
					</tr>
					<tr>
						<th style="width:5%;">前日比-<br>(Zenjitsu-hi)<br><br>rasio hari sebelumnya</th>
						<th style="width:5%;">前日比-<br>(Zenjitsu-hi)<br><br>rasio hari sebelumnya</th>
						<th style="width:5%;"></th>
					</tr>
					<tr>
						<th id="jkt_rasio_1">Not Updated</th>
						<th id="jkt_rasio_2">Not Updated</th>
						<th></th>
					</tr>
					<tr>
						<th style="width:5%;">死亡者数<br>(Shibōshasū)<br><br>Number of deaths</th>
						<th style="width:5%;"></th>
						<th style="width:5%;">退院者数<br>(Taiin shasū)<br><br>Number of discharge</th>
					</tr>
					<tr>
						<th id="jkt_death" style="background-color: rgb(255, 242, 204)">Not Updated</th>
						<th></th>
						<th id="jkt_discharge" style="background-color: rgb(255, 217, 102)">Not Updated</th>
					</tr>
				</thead>
			</table>

			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
					<li class="vendor-tab active"><a href="#tab_jkt_1" data-toggle="tab" id="tab_header_jkt_1">
					現在</a>
				</li>
				<li class="vendor-tab"><a href="#tab_jkt_2" data-toggle="tab" id="tab_header_jkt_2">
				新規</a>
			</li>
			<li class="vendor-tab"><a href="#tab_jkt_3" data-toggle="tab" id="tab_header_jkt_3">
			累計</a>
		</li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="tab_jkt_1">
			<div style="background-color: #ff7043; font-weight: bold; font-size: 1.5vw; text-align: center;">
				現在の感染者数推移　（累計感染者数－回復者数－死亡者数）
			</div>
			<div id="jkt_infected" style="margin-bottom: 20px;"></div>
		</div>

		<div class="tab-pane" id="tab_jkt_2">
			<div style="background-color: #3f51b5; font-weight: bold; font-size: 1.5vw; text-align: center; color: white;">
				新規感染者数
			</div>
			<div id="jkt_new_infected" style="margin-bottom: 20px;"></div>
		</div>

		<div class="tab-pane" id="tab_jkt_3">
			<div style="background-color: #ffff00; font-weight: bold; font-size: 1.5vw; text-align: center;">
				過去1週間の10万人あたりの新規感染者数
			</div>
			<div id="jkt_series" style="margin-bottom: 20px;"></div>
		</div>
	</div>
</div>
</div>
</div>
</div>

{{-- BEKASI --}}

<div class="col-xs-12" style="padding-left: 0" id="bekasi">
	<div class="box">
		<div class="box-header with-border">
			<h4 class="box-title">
				<i class="fa fa-square"></i><b>「ブカシ県＋ブカシ市」（YMMA・YMPA地区）</b>
			</h4>
		</div>
		<div class="box-body">
			<table class="table table-bordered" style="margin-bottom: 10px;">
				<thead>
					<tr>
						<th style="width:5%;">現在感染者数<br>(Genzai kansen-shasū)<br><br>Number Currently infected</th>
						<th style="width:5%;">新規感染者数<br>(Shinki kansenshasū)<br><br>Number of new infections</th>
						<th style="width:5%;">累計感染者数<br>(Ruikei kansenshasū)<br><br>Cumulative number of infected people</th>
					</tr>
					<tr>
						<th id="bks_curr" style="background-color: rgb(255, 242, 204)">Not Updated</th>
						<th id="bks_new">Not Updated</th>
						<th id="bks_cum" style="background-color: rgb(255, 242, 204)">Not Updated</th>
					</tr>
					<tr>
						<th style="width:5%;">前日比-<br>(Zenjitsu-hi)<br><br>rasio hari sebelumnya</th>
						<th style="width:5%;">前日比-<br>(Zenjitsu-hi)<br><br>rasio hari sebelumnya</th>
						<th style="width:5%;"></th>
					</tr>
					<tr>
						<th id="bks_rasio_1">Not Updated</th>
						<th id="bks_rasio_2">Not Updated</th>
						<th></th>
					</tr>
					<tr>
						<th style="width:5%;">死亡者数<br>(Shibōshasū)<br><br>Number of deaths</th>
						<th style="width:5%;"></th>
						<th style="width:5%;">退院者数<br>(Taiin shasū)<br><br>Number of discharge</th>
					</tr>
					<tr>
						<th id="bks_death" style="background-color: rgb(255, 242, 204)">Not Updated</th>
						<th></th>
						<th id="bks_discharge" style="background-color: rgb(255, 217, 102)">Not Updated</th>
					</tr>
				</thead>
			</table>

			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
					<li class="vendor-tab active"><a href="#tab_bks_1" data-toggle="tab" id="tab_header_bks_1">
					現在</a>
				</li>
				<li class="vendor-tab"><a href="#tab_bks_2" data-toggle="tab" id="tab_header_bks_2">
				新規</a>
			</li>
			<li class="vendor-tab"><a href="#tab_bks_3" data-toggle="tab" id="tab_header_bks_3">
			累計</a>
		</li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="tab_bks_1">
			<div style="background-color: #ff7043; font-weight: bold; font-size: 1.5vw; text-align: center;">
				現在の感染者数推移　（累計感染者数－回復者数－死亡者数）
			</div>
			<div id="bks_infected" style="margin-bottom: 20px;"></div>
		</div>

		<div class="tab-pane" id="tab_bks_2">
			<div style="background-color: #3f51b5; font-weight: bold; font-size: 1.5vw; text-align: center; color: white;">
				新規感染者数
			</div>
			<div id="bks_new_infected" style="margin-bottom: 20px;"></div>
		</div>

		<div class="tab-pane" id="tab_bks_3">
			<div style="background-color: #ffff00; font-weight: bold; font-size: 1.5vw; text-align: center;">
				過去1週間の10万人あたりの新規感染者数
			</div>
			<div id="bks_series" style="margin-bottom: 20px;"></div>
		</div>
	</div>
</div>
</div>
</div>
</div>

{{-- SURABAYA --}}

<div class="col-xs-12" style="padding-left: 0" id="surabaya">
	<div class="box">
		<div class="box-header with-border">
			<h4 class="box-title">
				<i class="fa fa-square"></i><b>「スラバヤ市」（駐在員居住地区）</b>
			</h4>
		</div>
		<div class="box-body">
			<table class="table table-bordered" style="margin-bottom: 10px;">
				<thead>
					<tr>
						<th style="width:5%;">現在感染者数<br>(Genzai kansen-shasū)<br><br>Number Currently infected</th>
						<th style="width:5%;">新規感染者数<br>(Shinki kansenshasū)<br><br>Number of new infections</th>
						<th style="width:5%;">累計感染者数<br>(Ruikei kansenshasū)<br><br>Cumulative number of infected people</th>
					</tr>
					<tr>
						<th id="sby_curr" style="background-color: rgb(255, 242, 204)">Not Updated</th>
						<th id="sby_new">Not Updated</th>
						<th id="sby_cum" style="background-color: rgb(255, 242, 204)">Not Updated</th>
					</tr>
					<tr>
						<th style="width:5%;">前日比-<br>(Zenjitsu-hi)<br><br>rasio hari sebelumnya</th>
						<th style="width:5%;">前日比-<br>(Zenjitsu-hi)<br><br>rasio hari sebelumnya</th>
						<th style="width:5%;"></th>
					</tr>
					<tr>
						<th id="sby_rasio_1">Not Updated</th>
						<th id="sby_rasio_2">Not Updated</th>
						<th></th>
					</tr>
					<tr>
						<th style="width:5%;">死亡者数<br>(Shibōshasū)<br><br>Number of deaths</th>
						<th style="width:5%;"></th>
						<th style="width:5%;">退院者数<br>(Taiin shasū)<br><br>Number of discharge</th>
					</tr>
					<tr>
						<th id="sby_death" style="background-color: rgb(255, 242, 204)">Not Updated</th>
						<th></th>
						<th id="sby_discharge" style="background-color: rgb(255, 217, 102)">Not Updated</th>
					</tr>
				</thead>
			</table>

			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
					<li class="vendor-tab active"><a href="#tab_sby_1" data-toggle="tab" id="tab_header_sby_1">
					現在</a>
				</li>
				<li class="vendor-tab"><a href="#tab_sby_2" data-toggle="tab" id="tab_header_sby_2">
				新規</a>
			</li>
			<li class="vendor-tab"><a href="#tab_sby_3" data-toggle="tab" id="tab_header_sby_3">
			累計</a>
		</li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="tab_sby_1">
			<div style="background-color: #ff7043; font-weight: bold; font-size: 1.5vw; text-align: center;">
				現在の感染者数推移　（累計感染者数－回復者数－死亡者数）
			</div>
			<div id="sby_infected" style="margin-bottom: 20px;"></div>
		</div>

		<div class="tab-pane" id="tab_sby_2">
			<div style="background-color: #3f51b5; font-weight: bold; font-size: 1.5vw; text-align: center; color: white;">
				新規感染者数
			</div>
			<div id="sby_new_infected" style="margin-bottom: 20px;"></div>
		</div>

		<div class="tab-pane" id="tab_sby_3">
			<div style="background-color: #ffff00; font-weight: bold; font-size: 1.5vw; text-align: center;">
				過去1週間の10万人あたりの新規感染者数
			</div>
			<div id="sby_series" style="margin-bottom: 20px;"></div>
		</div>
	</div>
</div>
</div>
</div>
</div>

{{-- PASURUAN --}}

<div class="col-xs-12" style="padding-left: 0" id="pasuruan">
	<div class="box">
		<div class="box-header with-border">
			<h4 class="box-title">
				<i class="fa fa-square"></i><b>「パスルアン県＋パスルアン市」（YMPI・YEMI地区）</b>
			</h4>
		</div>
		<div class="box-body">
			<table class="table table-bordered" style="margin-bottom: 10px;">
				<thead>
					<tr>
						<th style="width:5%;">現在感染者数<br>(Genzai kansen-shasū)<br><br>Number Currently infected</th>
						<th style="width:5%;">新規感染者数<br>(Shinki kansenshasū)<br><br>Number of new infections</th>
						<th style="width:5%;">累計感染者数<br>(Ruikei kansenshasū)<br><br>Cumulative number of infected people</th>
					</tr>
					<tr>
						<th id="psr_curr" style="background-color: rgb(255, 242, 204)">Not Updated</th>
						<th id="psr_new">Not Updated</th>
						<th id="psr_cum" style="background-color: rgb(255, 242, 204)">Not Updated</th>
					</tr>
					<tr>
						<th style="width:5%;">前日比-<br>(Zenjitsu-hi)<br><br>rasio hari sebelumnya</th>
						<th style="width:5%;">前日比-<br>(Zenjitsu-hi)<br><br>rasio hari sebelumnya</th>
						<th style="width:5%;"></th>
					</tr>
					<tr>
						<th id="psr_rasio_1">Not Updated</th>
						<th id="psr_rasio_2">Not Updated</th>
						<th></th>
					</tr>
					<tr>
						<th style="width:5%;">死亡者数<br>(Shibōshasū)<br><br>Number of deaths</th>
						<th style="width:5%;"></th>
						<th style="width:5%;">退院者数<br>(Taiin shasū)<br><br>Number of discharge</th>
					</tr>
					<tr>
						<th id="psr_death" style="background-color: rgb(255, 242, 204)">Not Updated</th>
						<th></th>
						<th id="psr_discharge" style="background-color: rgb(255, 217, 102)">Not Updated</th>
					</tr>
				</thead>
			</table>

			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
					<li class="vendor-tab active"><a href="#tab_psr_1" data-toggle="tab" id="tab_header_psr_1">
					現在</a>
				</li>
				<li class="vendor-tab"><a href="#tab_psr_2" data-toggle="tab" id="tab_header_psr_2">
				新規</a>
			</li>
			<li class="vendor-tab"><a href="#tab_psr_3" data-toggle="tab" id="tab_header_psr_3">
			累計</a>
		</li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="tab_psr_1">
			<div style="background-color: #ff7043; font-weight: bold; font-size: 1.5vw; text-align: center;">
				現在の感染者数推移　（累計感染者数－回復者数－死亡者数）
			</div>
			<div id="psr_infected" style="margin-bottom: 20px;"></div>
		</div>

		<div class="tab-pane" id="tab_psr_2">
			<div style="background-color: #3f51b5; font-weight: bold; font-size: 1.5vw; text-align: center; color: white;">
				新規感染者数
			</div>
			<div id="psr_new_infected" style="margin-bottom: 20px;"></div>
		</div>

		<div class="tab-pane" id="tab_psr_3">
			<div style="background-color: #ffff00; font-weight: bold; font-size: 1.5vw; text-align: center;">
				過去1週間の10万人あたりの新規感染者数
			</div>
			<div id="psr_series" style="margin-bottom: 20px;"></div>
		</div>
	</div>
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
<script src="{{ url("js/highstock.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('#datepicker').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		fetchChart();

		$('#btn_id').css("background-color", "orange");
		$('#jakarta').hide();
		$('#bekasi').hide();
		$('#surabaya').hide();
		$('#pasuruan').hide();
	});

	function info(id){
		if(id == 'id'){
			$('#btn_id').css("background-color", "orange");
			$('#btn_jkt').css("background-color", "white");
			$('#btn_bks').css("background-color", "white");
			$('#btn_sby').css("background-color", "white");
			$('#btn_psr').css("background-color", "white");
			$('#indonesia').show();
			$('#jakarta').hide();
			$('#bekasi').hide();
			$('#surabaya').hide();
			$('#pasuruan').hide();
		}
		else if(id == 'jkt'){
			$('#btn_id').css("background-color", "white");
			$('#btn_jkt').css("background-color", "orange");
			$('#btn_bks').css("background-color", "white");
			$('#btn_sby').css("background-color", "white");
			$('#btn_psr').css("background-color", "white");
			$('#indonesia').hide();
			$('#jakarta').show();
			$('#bekasi').hide();
			$('#surabaya').hide();
			$('#pasuruan').hide();
		}
		else if(id == 'bks'){
			$('#btn_id').css("background-color", "white");
			$('#btn_jkt').css("background-color", "white");
			$('#btn_bks').css("background-color", "orange");
			$('#btn_sby').css("background-color", "white");
			$('#btn_psr').css("background-color", "white");
			$('#indonesia').hide();
			$('#jakarta').hide();
			$('#bekasi').show();
			$('#surabaya').hide();
			$('#pasuruan').hide();
		}
		else if(id == 'sby'){
			$('#btn_id').css("background-color", "white");
			$('#btn_jkt').css("background-color", "white");
			$('#btn_bks').css("background-color", "white");
			$('#btn_sby').css("background-color", "orange");
			$('#btn_psr').css("background-color", "white");
			$('#indonesia').hide();
			$('#jakarta').hide();
			$('#bekasi').hide();
			$('#surabaya').show();
			$('#pasuruan').hide();
		}
		else if(id == 'psr'){
			$('#btn_id').css("background-color", "white");
			$('#btn_jkt').css("background-color", "white");
			$('#btn_bks').css("background-color", "white");
			$('#btn_sby').css("background-color", "white");
			$('#btn_psr').css("background-color", "orange");
			$('#indonesia').hide();
			$('#jakarta').hide();
			$('#bekasi').hide();
			$('#surabaya').hide();
			$('#pasuruan').show();
		}
	}

	function fetchChart(){
		$('#loading').show();
		var date_now = $('#datepicker').val();
		var data = {
			date_now:date_now
		}
		$.get('{{ url("fetch/corona_information") }}', data, function(result, status, xhr){
			if(result.status){
				var newIndonesiaSeries = [];
				var newJakartaSeries = [];
				var newBekasiSeries = [];
				var newSurabayaSeries = [];
				var newPasuruanSeries = [];

				var id_infected = [];
				var id_new = [];

				var jkt_infected = [];
				var jkt_new = [];

				var bks_infected = [];
				var bks_new = [];

				var sby_infected = [];
				var sby_new = [];

				var psr_infected = [];
				var psr_new = [];

				$.each(result.corona_informations, function(key, value) {
					var num_infected = (value.past_week_infected*(100000/value.population)).toFixed(2);

					if(value.area == 'Indonesia'){
						newIndonesiaSeries.push([Date.parse(value.date), (parseFloat(num_infected) || 0)]);
						id_infected.push([Date.parse(value.date), (parseFloat(value.acc_infected) || 0)]);
						id_new.push([Date.parse(value.date), (parseFloat(value.new_infected) || 0)]);
					}
					if(value.area == 'Jakarta'){
						newJakartaSeries.push([Date.parse(value.date), (parseFloat(num_infected) || 0)]);
						jkt_infected.push([Date.parse(value.date), (parseFloat(value.acc_infected) || 0)]);
						jkt_new.push([Date.parse(value.date), (parseFloat(value.new_infected) || 0)]);
					}
					if(value.area == 'Bekasi'){
						newBekasiSeries.push([Date.parse(value.date), (parseFloat(num_infected) || 0)]);
						bks_infected.push([Date.parse(value.date), (parseFloat(value.acc_infected) || 0)]);
						bks_new.push([Date.parse(value.date), (parseFloat(value.new_infected) || 0)]);
					}
					if(value.area == 'Surabaya'){
						newSurabayaSeries.push([Date.parse(value.date), (parseFloat(num_infected) || 0)]);
						sby_infected.push([Date.parse(value.date), (parseFloat(value.acc_infected) || 0)]);
						sby_new.push([Date.parse(value.date), (parseFloat(value.new_infected) || 0)]);
					}
					if(value.area == 'Pasuruan'){
						newPasuruanSeries.push([Date.parse(value.date), (parseFloat(num_infected) || 0)]);
						psr_infected.push([Date.parse(value.date), (parseFloat(value.acc_infected) || 0)]);
						psr_new.push([Date.parse(value.date), (parseFloat(value.new_infected) || 0)]);
					}
				});

				$.each(result.detail_yesterday, function(key, value) {
					if(value.area == 'Indonesia'){
						$('#id_rasio_1').text(value.curr_infected.toLocaleString());
						$('#id_rasio_2').text(value.new_infected.toLocaleString());
					}
					if(value.area == 'Jakarta'){
						$('#jkt_rasio_1').text(value.curr_infected.toLocaleString());
						$('#jkt_rasio_2').text(value.new_infected.toLocaleString());
					}
					if(value.area == 'Bekasi'){
						$('#bks_rasio_1').text(value.curr_infected.toLocaleString());
						$('#bks_rasio_2').text(value.new_infected.toLocaleString());
					}
					if(value.area == 'Surabaya'){
						$('#sby_rasio_1').text(value.curr_infected.toLocaleString());
						$('#sby_rasio_2').text(value.new_infected.toLocaleString());
					}
					if(value.area == 'Pasuruan'){
						$('#psr_rasio_1').text(value.curr_infected.toLocaleString());
						$('#psr_rasio_2').text(value.new_infected.toLocaleString());
					}
				});

				$.each(result.detail_now, function(key, value){

					if(value.area == 'Indonesia'){
						$('#id_curr').text(value.curr_infected.toLocaleString());
						$('#id_new').text(value.new_infected.toLocaleString());
						$('#id_cum').text(value.acc_infected.toLocaleString());
						$('#id_death').text(value.acc_dead.toLocaleString());
						$('#id_discharge').text(value.acc_recovered.toLocaleString());
					}
					if(value.area == 'Jakarta'){
						$('#jkt_curr').text(value.curr_infected.toLocaleString());
						$('#jkt_new').text(value.new_infected.toLocaleString());
						$('#jkt_cum').text(value.acc_infected.toLocaleString());
						$('#jkt_death').text(value.acc_dead.toLocaleString());
						$('#jkt_discharge').text(value.acc_recovered.toLocaleString());
					}
					if(value.area == 'Bekasi'){
						$('#bks_curr').text(value.curr_infected.toLocaleString());
						$('#bks_new').text(value.new_infected.toLocaleString());
						$('#bks_cum').text(value.acc_infected.toLocaleString());
						$('#bks_death').text(value.acc_dead.toLocaleString());
						$('#bks_discharge').text(value.acc_recovered.toLocaleString());
					}
					if(value.area == 'Surabaya'){
						$('#sby_curr').text(value.curr_infected.toLocaleString());
						$('#sby_new').text(value.new_infected.toLocaleString());
						$('#sby_cum').text(value.acc_infected.toLocaleString());
						$('#sby_death').text(value.acc_dead.toLocaleString());
						$('#sby_discharge').text(value.acc_recovered.toLocaleString());
					}
					if(value.area == 'Pasuruan'){
						$('#psr_curr').text(value.curr_infected.toLocaleString());
						$('#psr_new').text(value.new_infected.toLocaleString());
						$('#psr_cum').text(value.acc_infected.toLocaleString());
						$('#psr_death').text(value.acc_dead.toLocaleString());
						$('#psr_discharge').text(value.acc_recovered.toLocaleString());
					}
				});

				window.chart = Highcharts.stockChart('new_infected', {
					rangeSelector: {
						selected: 0
					},
					chart: {
						type: 'line'
					},
					scrollbar:{
						enabled:false
					},
					navigator:{
						enabled:false
					},
					title: {
						text: '過去1週間の100,000人あたりの新規感染者数',
						style: {
							fontSize: '24px'
						}
					},
					xAxis: {
						type: 'datetime',
						tickInterval: 24 * 3600 * 1000,
						scrollbar: {
							enabled: true
						}
					},
					yAxis: {
						min:0,
						title: {
							text: null
						}
					},
					plotOptions: {
						line: {
							dataLabels: {
								enabled: false
							},
							enableMouseTracking: true
						}
					},
					credits: {
						enabled : false
					},
					legend: {
						enabled: true,
						layout: 'vertical',
						align: 'right',
						verticalAlign: 'middle'
					},
					series: [{
						name: '"Indonesia"',
						data: newIndonesiaSeries,
						lineWidth: 1,
						marker: {
							enabled: true,
							radius: 3
						}
					},{
						name: '"Special State of Jakarta" (YI / YMMI, resident residence district)',
						data: newJakartaSeries,
						lineWidth: 1,
						marker: {
							enabled: true,
							radius: 3
						}
					},{
						name: '"Bekasi Prefecture + Bekasi City" (YMMA / YMPA area)',
						data: newBekasiSeries,
						lineWidth: 1,
						marker: {
							enabled: true,
							radius: 3
						}
					},{
						name: '"Surabaya City" (residential district)',
						data: newSurabayaSeries,
						lineWidth: 1,
						marker: {
							enabled: true,
							radius: 3
						}
					},{
						name: '"Pasuruan + Pasuruan City" (YMPI / YEMI area)',
						data: newPasuruanSeries,
						lineWidth: 1,
						marker: {
							enabled: true,
							radius: 3
						}
					}]
				});

			//INDONESIA

			window.chart2_id = Highcharts.stockChart('id_infected', {
				rangeSelector: {
					selected: 0
				},
				chart: {
					type: 'column'
				},
				scrollbar:{
					enabled:false
				},
				navigator:{
					enabled:false
				},
				title: {
					text: 'インドネシア国全体」現在の感染者数推移',
					style: {
						fontSize: '24px'
					}
				},
				xAxis: {
					type: 'datetime',
					tickInterval: 24 * 3600 * 1000,
					scrollbar: {
						enabled: true
					}
				},
				yAxis: {
					min:0,
					title: {
						text: null
					}
				},
				plotOptions: {
					series: {
						dataLabels: {
							enabled: true
						},
						enableMouseTracking: true,
						color: '#ff7043'
					}
				},
				credits: {
					enabled : false
				},
				legend: {
					enabled: false,
					layout: 'vertical',
					align: 'right',
					verticalAlign: 'middle'
				},
				series: [{
					name: '"Indonesia"',
					data: id_infected	
				}]
			});

			window.chart3_id = Highcharts.stockChart('id_new_infected', {
				rangeSelector: {
					selected: 0
				},
				chart: {
					type: 'column'
				},
				scrollbar:{
					enabled:false
				},
				navigator:{
					enabled:false
				},
				title: {
					text: '「インドネシア国全体」新規感染者数',
					style: {
						fontSize: '24px'
					}
				},
				xAxis: {
					type: 'datetime',
					tickInterval: 24 * 3600 * 1000,
					scrollbar: {
						enabled: true
					}
				},
				yAxis: {
					min:0,
					title: {
						text: null
					}
				},
				plotOptions: {
					series: {
						dataLabels: {
							enabled: true
						},
						enableMouseTracking: true,
						color: '#3f51b5'
					}
				},
				credits: {
					enabled : false
				},
				legend: {
					enabled: false,
					layout: 'vertical',
					align: 'right',
					verticalAlign: 'middle'
				},
				series: [{
					name: '"Indonesia"',
					data: id_new	
				}]
			});

			window.chart4_id = Highcharts.stockChart('id_series', {
				rangeSelector: {
					selected: 0
				},
				chart: {
					type: 'line'
				},
				scrollbar:{
					enabled:false
				},
				navigator:{
					enabled:false
				},
				title: {
					text: 'インドネシア国全体」過去1週間の100,000人あたりの新規感染者数',
					style: {
						fontSize: '24px'
					}
				},
				xAxis: {
					type: 'datetime',
					tickInterval: 24 * 3600 * 1000,
					scrollbar: {
						enabled: true
					}
				},
				yAxis: {
					min:0,
					title: {
						text: null
					}
				},
				plotOptions: {
					line: {
						dataLabels: {
							enabled: false
						},
						enableMouseTracking: true
					}
				},
				credits: {
					enabled : false
				},
				legend: {
					enabled: false,
					layout: 'vertical',
					align: 'right',
					verticalAlign: 'middle'
				},
				series: [{
					name: '"Indonesia"',
					data: newIndonesiaSeries,
					lineWidth: 1,
					marker: {
						enabled: true,
						radius: 3
					}
				}]
			});

			// JAKARTA

			window.chart2_jkt = Highcharts.stockChart('jkt_infected', {
				rangeSelector: {
					selected: 0
				},
				chart: {
					type: 'column'
				},
				scrollbar:{
					enabled:false
				},
				navigator:{
					enabled:false
				},
				title: {
					text: 'ジャカルタ特別州」現在の感染者数推移',
					style: {
						fontSize: '24px'
					}
				},
				xAxis: {
					type: 'datetime',
					tickInterval: 24 * 3600 * 1000,
					scrollbar: {
						enabled: true
					}
				},
				yAxis: {
					min:0,
					title: {
						text: null
					}
				},
				plotOptions: {
					series: {
						dataLabels: {
							enabled: true
						},
						enableMouseTracking: true,
						color: '#ff7043'
					}
				},
				credits: {
					enabled : false
				},
				legend: {
					enabled: false,
					layout: 'vertical',
					align: 'right',
					verticalAlign: 'middle'
				},
				series: [{
					name: '"Jakarta"',
					data: jkt_infected	
				}]
			});

			window.chart3_jkt = Highcharts.stockChart('jkt_new_infected', {
				rangeSelector: {
					selected: 0
				},
				chart: {
					type: 'column'
				},
				scrollbar:{
					enabled:false
				},
				navigator:{
					enabled:false
				},
				title: {
					text: '「ジャカルタ特別州」新規感染者数',
					style: {
						fontSize: '24px'
					}
				},
				xAxis: {
					type: 'datetime',
					tickInterval: 24 * 3600 * 1000,
					scrollbar: {
						enabled: true
					}
				},
				yAxis: {
					min:0,
					title: {
						text: null
					}
				},
				plotOptions: {
					series: {
						dataLabels: {
							enabled: true
						},
						enableMouseTracking: true,
						color: '#3f51b5'
					}
				},
				credits: {
					enabled : false
				},
				legend: {
					enabled: false,
					layout: 'vertical',
					align: 'right',
					verticalAlign: 'middle'
				},
				series: [{
					name: '"Jakarta"',
					data: jkt_new	
				}]
			});

			window.chart4_jkt = Highcharts.stockChart('jkt_series', {
				rangeSelector: {
					selected: 0
				},
				chart: {
					type: 'line'
				},
				scrollbar:{
					enabled:false
				},
				navigator:{
					enabled:false
				},
				title: {
					text: '「ジャカルタ特別州」過去1週間の100,000人あたりの新規感染者数',
					style: {
						fontSize: '24px'
					}
				},
				xAxis: {
					type: 'datetime',
					tickInterval: 24 * 3600 * 1000,
					scrollbar: {
						enabled: true
					}
				},
				yAxis: {
					min:0,
					title: {
						text: null
					}
				},
				plotOptions: {
					line: {
						dataLabels: {
							enabled: false
						},
						enableMouseTracking: true
					}
				},
				credits: {
					enabled : false
				},
				legend: {
					enabled: false,
					layout: 'vertical',
					align: 'right',
					verticalAlign: 'middle'
				},
				series: [{
					name: '"Jakarta"',
					data: newJakartaSeries,
					lineWidth: 1,
					marker: {
						enabled: true,
						radius: 3
					}
				}]
			});

			// BEKASI

			window.chart2_bks = Highcharts.stockChart('bks_infected', {
				rangeSelector: {
					selected: 0
				},
				chart: {
					type: 'column'
				},
				scrollbar:{
					enabled:false
				},
				navigator:{
					enabled:false
				},
				title: {
					text: '「ブカシ県＋ブカシ市」現在の感染者数推移',
					style: {
						fontSize: '24px'
					}
				},
				xAxis: {
					type: 'datetime',
					tickInterval: 24 * 3600 * 1000,
					scrollbar: {
						enabled: true
					}
				},
				yAxis: {
					min:0,
					title: {
						text: null
					}
				},
				plotOptions: {
					series: {
						dataLabels: {
							enabled: true
						},
						enableMouseTracking: true,
						color: '#ff7043'
					}
				},
				credits: {
					enabled : false
				},
				legend: {
					enabled: false,
					layout: 'vertical',
					align: 'right',
					verticalAlign: 'middle'
				},
				series: [{
					name: '"Bekasi"',
					data: bks_infected	
				}]
			});

			window.chart3_bks = Highcharts.stockChart('bks_new_infected', {
				rangeSelector: {
					selected: 0
				},
				chart: {
					type: 'column'
				},
				scrollbar:{
					enabled:false
				},
				navigator:{
					enabled:false
				},
				title: {
					text: '「ブカシ県＋ブカシ市」新規感染者数',
					style: {
						fontSize: '24px'
					}
				},
				xAxis: {
					type: 'datetime',
					tickInterval: 24 * 3600 * 1000,
					scrollbar: {
						enabled: true
					}
				},
				yAxis: {
					min:0,
					title: {
						text: null
					}
				},
				plotOptions: {
					series: {
						dataLabels: {
							enabled: true
						},
						enableMouseTracking: true,
						color: '#3f51b5'
					}
				},
				credits: {
					enabled : false
				},
				legend: {
					enabled: false,
					layout: 'vertical',
					align: 'right',
					verticalAlign: 'middle'
				},
				series: [{
					name: '"Bekasi"',
					data: bks_new	
				}]
			});

			window.chart4_bks = Highcharts.stockChart('bks_series', {
				rangeSelector: {
					selected: 0
				},
				chart: {
					type: 'line'
				},
				scrollbar:{
					enabled:false
				},
				navigator:{
					enabled:false
				},
				title: {
					text: '「ブカシ県＋ブカシ市」過去1週間の100,000人あたりの新規感染者数',
					style: {
						fontSize: '24px'
					}
				},
				xAxis: {
					type: 'datetime',
					tickInterval: 24 * 3600 * 1000,
					scrollbar: {
						enabled: true
					}
				},
				yAxis: {
					min:0,
					title: {
						text: null
					}
				},
				plotOptions: {
					line: {
						dataLabels: {
							enabled: false
						},
						enableMouseTracking: true
					}
				},
				credits: {
					enabled : false
				},
				legend: {
					enabled: false,
					layout: 'vertical',
					align: 'right',
					verticalAlign: 'middle'
				},
				series: [{
					name: '"Bekasi"',
					data: newBekasiSeries,
					lineWidth: 1,
					marker: {
						enabled: true,
						radius: 3
					}
				}]
			});

			// SURABAYA

			window.chart2_sby = Highcharts.stockChart('sby_infected', {
				rangeSelector: {
					selected: 0
				},
				chart: {
					type: 'column'
				},
				scrollbar:{
					enabled:false
				},
				navigator:{
					enabled:false
				},
				title: {
					text: '「スラバヤ市」現在の感染者数推移',
					style: {
						fontSize: '24px'
					}
				},
				xAxis: {
					type: 'datetime',
					tickInterval: 24 * 3600 * 1000,
					scrollbar: {
						enabled: true
					}
				},
				yAxis: {
					min:0,
					title: {
						text: null
					}
				},
				plotOptions: {
					series: {
						dataLabels: {
							enabled: true
						},
						enableMouseTracking: true,
						color: '#ff7043'
					}
				},
				credits: {
					enabled : false
				},
				legend: {
					enabled: false,
					layout: 'vertical',
					align: 'right',
					verticalAlign: 'middle'
				},
				series: [{
					name: '"Surabaya"',
					data: sby_infected	
				}]
			});

			window.chart3_sby = Highcharts.stockChart('sby_new_infected', {
				rangeSelector: {
					selected: 0
				},
				chart: {
					type: 'column'
				},
				scrollbar:{
					enabled:false
				},
				navigator:{
					enabled:false
				},
				title: {
					text: '「スラバヤ市」新規感染者数',
					style: {
						fontSize: '24px'
					}
				},
				xAxis: {
					type: 'datetime',
					tickInterval: 24 * 3600 * 1000,
					scrollbar: {
						enabled: true
					}
				},
				yAxis: {
					min:0,
					title: {
						text: null
					}
				},
				plotOptions: {
					series: {
						dataLabels: {
							enabled: true
						},
						enableMouseTracking: true,
						color: '#3f51b5'
					}
				},
				credits: {
					enabled : false
				},
				legend: {
					enabled: false,
					layout: 'vertical',
					align: 'right',
					verticalAlign: 'middle'
				},
				series: [{
					name: '"Surabaya"',
					data: sby_new	
				}]
			});

			window.chart4_sby = Highcharts.stockChart('sby_series', {
				rangeSelector: {
					selected: 0
				},
				chart: {
					type: 'line'
				},
				scrollbar:{
					enabled:false
				},
				navigator:{
					enabled:false
				},
				title: {
					text: '「スラバヤ市」過去1週間の100,000人あたりの新規感染者数',
					style: {
						fontSize: '24px'
					}
				},
				xAxis: {
					type: 'datetime',
					tickInterval: 24 * 3600 * 1000,
					scrollbar: {
						enabled: true
					}
				},
				yAxis: {
					min:0,
					title: {
						text: null
					}
				},
				plotOptions: {
					line: {
						dataLabels: {
							enabled: false
						},
						enableMouseTracking: true
					}
				},
				credits: {
					enabled : false
				},
				legend: {
					enabled: false,
					layout: 'vertical',
					align: 'right',
					verticalAlign: 'middle'
				},
				series: [{
					name: '"Surabaya"',
					data: newSurabayaSeries,
					lineWidth: 1,
					marker: {
						enabled: true,
						radius: 3
					}
				}]
			});

			// PASURUAN

			window.chart2_psr = Highcharts.stockChart('psr_infected', {
				rangeSelector: {
					selected: 0
				},
				chart: {
					type: 'column'
				},
				scrollbar:{
					enabled:false
				},
				navigator:{
					enabled:false
				},
				title: {
					text: '「パスルアン県＋パスルアン市」現在の感染者数推移',
					style: {
						fontSize: '24px'
					}
				},
				xAxis: {
					type: 'datetime',
					tickInterval: 24 * 3600 * 1000,
					scrollbar: {
						enabled: true
					}
				},
				yAxis: {
					min:0,
					title: {
						text: null
					}
				},
				plotOptions: {
					series: {
						dataLabels: {
							enabled: true
						},
						enableMouseTracking: true,
						color: '#ff7043'
					}
				},
				credits: {
					enabled : false
				},
				legend: {
					enabled: false,
					layout: 'vertical',
					align: 'right',
					verticalAlign: 'middle'
				},
				series: [{
					name: '"Pasuruan"',
					data: psr_infected	
				}]
			});

			window.chart3_psr = Highcharts.stockChart('psr_new_infected', {
				rangeSelector: {
					selected: 0
				},
				chart: {
					type: 'column'
				},
				scrollbar:{
					enabled:false
				},
				navigator:{
					enabled:false
				},
				title: {
					text: '「パスルアン県＋パスルアン市」新規感染者数',
					style: {
						fontSize: '24px'
					}
				},
				xAxis: {
					type: 'datetime',
					tickInterval: 24 * 3600 * 1000,
					scrollbar: {
						enabled: true
					}
				},
				yAxis: {
					min:0,
					title: {
						text: null
					}
				},
				plotOptions: {
					series: {
						dataLabels: {
							enabled: true
						},
						enableMouseTracking: true,
						color: '#3f51b5'
					}
				},
				credits: {
					enabled : false
				},
				legend: {
					enabled: false,
					layout: 'vertical',
					align: 'right',
					verticalAlign: 'middle'
				},
				series: [{
					name: '"Pasuruan"',
					data: psr_new	
				}]
			});

			window.chart4_psr = Highcharts.stockChart('psr_series', {
				rangeSelector: {
					selected: 0
				},
				chart: {
					type: 'line'
				},
				scrollbar:{
					enabled:false
				},
				navigator:{
					enabled:false
				},
				title: {
					text: '「パスルアン県＋パスルアン市」過去1週間の100,000人あたりの新規感染者数',
					style: {
						fontSize: '24px'
					}
				},
				xAxis: {
					type: 'datetime',
					tickInterval: 24 * 3600 * 1000,
					scrollbar: {
						enabled: true
					}
				},
				yAxis: {
					min:0,
					title: {
						text: null
					}
				},
				plotOptions: {
					line: {
						dataLabels: {
							enabled: false
						},
						enableMouseTracking: true
					}
				},
				credits: {
					enabled : false
				},
				legend: {
					enabled: false,
					layout: 'vertical',
					align: 'right',
					verticalAlign: 'middle'
				},
				series: [{
					name: '"Pasuruan"',
					data: newPasuruanSeries,
					lineWidth: 1,
					marker: {
						enabled: true,
						radius: 3
					}
				}]
			});
			$('#loading').hide();
		}
		else{
			$('#loading').hide();
			alert('Attempt to retrieve data failed.');
		}

	});
}

</script>
@endsection