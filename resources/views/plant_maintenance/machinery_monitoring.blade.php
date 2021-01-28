@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	table.table-bordered{
		border:6px solid rgb(60,60,60);
		text-align: center;
		vertical-align: middle;
	}
	table.table-bordered > thead > tr > th{
		border:6px solid rgb(60,60,60);
		text-align: center;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:6px solid rgb(60,60,60);
		text-align: center;
		vertical-align: middle;
	}
	.content{
		color: black;
		font-weight: bold;
	}
	.progress {
		background-color: rgba(0,0,0,0);
	}

	.hd {
		padding-top: 0px !important;
		padding-bottom: 0px !important;
	}
	th > span {
		font-size: 0.9vw;
		animation: blinker 1s linear infinite;
	}

	@keyframes blinker {
		50% {
			opacity: 0;
		}
	}
</style>
@stop
@section('header')
@endsection
<style type="text/css">
</style>
@section('content')
<section class="content" style="padding-top: 0;">

	<div class="col-xs-12" style="padding: 0px; margin-top: 0;">
		<form method="GET" action="{{ url("/machinery_monitoring") }}" style="margin-bottom: 0px;">
			<div class="col-xs-2">
				<div class="form-group">
					<select class="form-control select2" multiple="multiple" id="mesinSelect" data-placeholder="Select Machines" onchange="change()">
						<option value="machining">Machining</option>
						<option value="press">Press</option>
						<option value="injeksi">Injeksi</option>
						<option value="senban">Senban</option>
						<option value="zpro">Zpro</option>
					</select>
					<input type="text" name="mesin" id="mesin" hidden>
				</div>
			</div>
			<div class="col-xs-1">
				<div class="form-group">
					<button class="btn btn-success" type="submit">Show</button>
				</div>
			</div>
		</form>
		
		<div class="col-xs-3 pull-right" style="padding:0px; border:2px solid #fff; color:#fff">
			<div class="col-xs-2" style="padding:0px;">
				<div class="col-xs-8 pull-right" style="padding:0px;">Ket :</div>
			</div>		
			<div class="col-xs-4" style="padding:0px;">
				<div class="col-xs-12" style="padding:0px;">
					<span><i class="fa fa-fw fa-square" style="color: #f24b4b;"></i>Trouble</span>
				</div>
				<div class="col-xs-12" style="padding:0px;">
					<span><i class="fa fa-fw fa-square" style="color: #3366cc;"></i>Setup</span>
				</div>

			</div>
			<div class="col-xs-3" style="padding:0px;">
				<div class="col-xs-12" style="padding:0px;">
					<span><i class="fa fa-fw fa-square" style="color: #00a65a;"></i>Process</span>
				</div>
				<div class="col-xs-12" style="padding:0px;">
					<span><i class="fa fa-fw fa-square" style="color: #FCF33A;"></i> Idle 1</span>
				</div>
			</div>
			<div class="col-xs-3 pull-right" style="padding:0px;">
				<div class="col-xs-12" style="padding:0px;">
					<span><i class="fa fa-fw fa-square" style="color: #fcfdff;"></i>Idle 2</span>
				</div>
				<div class="col-xs-12" style="padding:0px;">
					<span><i class="fa fa-fw fa-square" style="color: #000;"></i>Off</span>
				</div>
			</div>


		</div>

	</div>
	
	

	<div class="row">
		<div id="machining" class="col-xs-12" style="padding: 0px; margin-top: 5px;">
			<div class="col-xs-1" style="padding: 0px;">
				<table class="table table-bordered" style="margin:0">
					<thead><tr><th style="background-color: #fff;" class="hd">Machining All <br><span>&nbsp;</span></th></tr></thead>
					<tbody><tr><th style="background-color: #fff; text-align: center; vertical-align: middle; height: 105px;">Status</th></tr></tbody>
				</table>
			</div>
			<div class="col-xs-11" style="padding: 0px; overflow-x: auto;">
				<table id="table_machining" class="table table-bordered" style="margin:0; width: 100%; color: #000">
					<thead id="head_machining">
					</thead>
					<tbody id="body_machining">
					</tbody>
				</table>	
			</div>
		</div>

		<div id="press" class="col-xs-12" style="padding: 0px; margin-top: 15px;">
			<div class="col-xs-1" style="padding: 0px;">
				<table class="table table-bordered" style="margin:0">
					<thead><tr><th style="background-color: #fff;" class="hd">Press All <br><span>&nbsp;</span></th></tr></thead>
					<tbody><tr><th style="background-color: #fff; text-align: center; vertical-align: middle; height: 105px;">Status</th></tr></tbody>
				</table>
			</div>
			<div class="col-xs-8" style="padding: 0px; overflow-x: auto;">
				<table id="table_press" class="table table-bordered" style="margin:0; width: 100%; color: #000">
					<thead id="head_press">
					</thead>
					<tbody id="body_press">
					</tbody>
				</table>	
			</div>
		</div>

		<div id="injeksi" class="col-xs-12" style="padding: 0px; margin-top: 15px;">
			<div class="col-xs-1" style="padding: 0px;">
				<table class="table table-bordered" style="margin:0">
					<thead><tr><th style="background-color: #fff;" class="hd">Injeksi All <br><span>&nbsp;</span></th></tr></thead>
					<tbody><tr><th style="background-color: #fff; text-align: center; vertical-align: middle; height: 105px;">Status</th></tr></tbody>
				</table>
			</div>
			<div class="col-xs-11" style="padding: 0px; overflow-x: auto;">
				<table id="table_injeksi" class="table table-bordered" style="margin:0; width: 100%; color: #000">
					<thead id="head_injeksi">
					</thead>
					<tbody id="body_injeksi">
					</tbody>
				</table>	
			</div>
		</div>

		<div id="senban" class="col-xs-12" style="padding: 0px; margin-top: 15px;">
			<div class="col-xs-1" style="padding: 0px;">
				<table class="table table-bordered" style="margin:0">
					<thead><tr><th style="background-color: #fff;" class="hd">Senban All <br><span>&nbsp;</span></th></tr></thead>
					<tbody><tr><th style="background-color: #fff; text-align: center; vertical-align: middle; height: 105px;">Status</th></tr></tbody>
				</table>
			</div>
			<div class="col-xs-11" style="padding: 0px; overflow-x: auto;">
				<table id="table_senban" class="table table-bordered" style="margin:0; width: 100%; color: #000">
					<thead id="head_senban">
					</thead>
					<tbody id="body_senban">
					</tbody>
				</table>	
			</div>
		</div>

		<div id="zpro" class="col-xs-12" style="padding: 0px; margin-top: 15px;">
			<div class="col-xs-1" style="padding: 0px;">
				<table class="table table-bordered" style="margin:0">
					<thead><tr><th style="background-color: #fff;" class="hd">Zpro All <br><span>&nbsp;</span></th></tr></thead>
					<tbody><tr><th style="background-color: #fff; text-align: center; vertical-align: middle; height: 105px;">Status</th></tr></tbody>
				</table>
			</div>
			<div class="col-xs-4" style="padding: 0px; overflow-x: auto;">
				<table id="table_zpro" class="table table-bordered" style="margin:0; width: 100%; color: #000">
					<thead id="head_zpro">
					</thead>
					<tbody id="body_zpro">
					</tbody>
				</table>	
			</div>
		</div>

	</div>


</section>
@stop
@section('scripts')
<script src="{{ url("js/jquery.marquee.min.js")}}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>

	jQuery(document).ready(function(){
		mesin1();
		mesin2();

		// setInterval(mesin1, 1000);
		// setInterval(mesin2, 1000);
		
		show();

		$('.select2').select2();

	});	


	function change(){
		$("#mesin").val($("#mesinSelect").val());
	}

	function show(){
		var mesin = "{{$_GET['mesin']}}".split(',');

		$("#machining").hide();
		$("#press").hide();
		$("#injeksi").hide();
		$("#senban").hide();
		$("#zpro").hide();

		for (var i = 0; i < mesin.length; i++) {
			if(mesin[i] == 'machining'){			
				$("#machining").show();
			}else if(mesin[i] == 'press'){
				$("#press").show();
			}else if(mesin[i] == 'injeksi'){
				$("#injeksi").show();
			}else if(mesin[i] == 'senban'){
				$("#senban").show();
			}else if(mesin[i] == 'zpro'){
				$("#zpro").show();
			}
		}
	}

	var audio_error = new Audio('{{ url("sounds/alarm_error.mp3") }}');

	function mesin1(){
		$.get("{{ 'http://10.109.52.7/zed/dashboard/getDataSystem/' }}", function(result, status, xhr){
			// console.log(result);

			var mesin = result.split('(ime)');
			
			//Zpro
			var zpro = [
			['85','WCut#1'],
			['84','WCut#2'],
			['98','WCut#3'],
			['89','Shinogi'],
			['94','MC1st#1'],
			['86','MC1st#2'],
			['95','MC2nd#1']];

			var mesin_split = [];
			for (var i = 0; i < mesin.length; i++) {
				mesin_split.push(mesin[i].split('#'));
			}

			$('#head_zpro').append().empty();
			$('#body_zpro').append().empty();

			var head = '<tr>';
			var body = '<tr>';
			for (var i = 0; i < zpro.length; i++) {
				for (var j = 0; j < mesin_split.length; j++) {
					if(zpro[i][0] == mesin_split[j][0]){

						var merah = mesin_split[j][4].split(':');
						var merah_time = (parseInt(merah[0])*3600) + (parseInt(merah[1])*60) + parseInt(merah[2]);
						var mr_to_h = merah_time / 60 /60;
						var putih = mesin_split[j][8].split(':');
						var putih_time = (parseInt(putih[0])*3600) + (parseInt(putih[1])*60) + parseInt(putih[2]);
						var mr_to_p = putih_time / 60 /60;

						//head
						if (mesin_split[j][1] == 0){//merah
							head += '<th class="hd" style="background-color: #f24b4b; width:6%;">'+zpro[i][1].replace("MC ","")+'<br><span>'+mr_to_h.toFixed(1)+' H</span></th>';
							audio_error.play();
						}else if(mesin_split[j][1] == 1){//hijau
							head += '<th class="hd" style="background-color: #00a65a; width:6%;">'+zpro[i][1].replace("MC ","")+'<br><span>&nbsp;</span></th>';
						}else if(mesin_split[j][1] == 2){//kuning
							head += '<th class="hd" style="background-color: #FCF33A; width:6%;">'+zpro[i][1].replace("MC ","")+'<br><span>&nbsp;</span></th>';
						}else if(mesin_split[j][1] == 3){//biru
							head += '<th class="hd" style="background-color: #3366cc; width:6%;">'+zpro[i][1].replace("MC ","")+'<br><span>&nbsp;</span></th>';
						}else if(mesin_split[j][1] == 4){//putih
							head += '<th class="hd" style="background-color: #fcfdff; width:6%;">'+zpro[i][1].replace("MC ","")+'<br><span>'+mr_to_p.toFixed(1)+' H</span></th>';
						}else if(mesin_split[j][1] == 5){//hitam	
							head += '<th class="hd" style="background-color: #000; color: #fff; width:6%;">'+zpro[i][1].replace("MC ","")+'<br><span>&nbsp;</span></th>';
						}

						//body
						body += '<td style="padding: 0px;">';
						var hijau = mesin_split[j][5].split(':');
						var kuning = mesin_split[j][6].split(':');
						var biru = mesin_split[j][7].split(':');
						var hitam = mesin_split[j][9].split(':');

						var hijau_time = (parseInt(hijau[0])*3600) + (parseInt(hijau[1])*60) + parseInt(hijau[2]);
						var kuning_time = (parseInt(kuning[0])*3600) + (parseInt(kuning[1])*60) + parseInt(kuning[2]);
						var biru_time = (parseInt(biru[0])*3600) + (parseInt(biru[1])*60) + parseInt(biru[2]);
						var hitam_time = (parseInt(hitam[0])*3600) + (parseInt(hitam[1])*60) + parseInt(hitam[2]);
						var total_time = merah_time + hijau_time + kuning_time + biru_time + putih_time + hitam_time;


						body += '<div style="height: 100px;">';
						body += '<div style="background-color: #f24b4b; height: '+ (merah_time/total_time)*100 +'%;"></div>';
						body += '<div style="background-color: #00a65a; height: '+ (hijau_time/total_time)*100 +'%;"></div>';
						body += '<div style="background-color: #FCF33A; height: '+ (kuning_time/total_time)*100 +'%;"></div>';
						body += '<div style="background-color: #3366cc; height: '+ (biru_time/total_time)*100 +'%;"></div>';
						body += '<div style="background-color: #fcfdff; height: '+ (putih_time/total_time)*100 +'%;"></div>';
						body += '<div style="background-color: #000; height: '+ (hitam_time/total_time)*100 +'%;"></div>';
						body += '</div>';

						body += '</td>';				
					}
				}
			}

			head += '</tr>';
			body += '</tr>';

			$('#head_zpro').append(head);
			$('#body_zpro').append(body);

		});
	}

	function mesin2(){
		$.get("{{ 'http://10.109.52.7/zed/dashboard/getData' }}", function(result, status, xhr){

			var mesin = result.split('(ime)');

			//Machining
			var machining_data = [
			['1','MC 1st#6',6],
			['2','MC 1st#4',4],
			['3','MC 1st#3',3],
			['4','MC 1st#5',5],
			['5','MC 1st#1',1],
			['6','MC 1st#2',2],
			['7','MC 1st#7',7],
			['8','MC 1st#9',9],
			['9','MC 1st#8',8],
			['10','MC 1st#10',10],
			['11','MC 1st#11',11],
			['12','MC 1st#12',12],
			['13','MC 2nd#1',13],
			['14','MC 2nd#2',14],
			['15','MC 2nd#3',15],
			['16','MC 2nd#4',16],
			['17','MC 2nd#5',17],
			['18','MC 2nd#6',18],
			['19','MC 2nd#7',19],
			['20','MC 2nd#8',20],
			['21','MC 2nd#9',21],
			['22','MC 2nd#10',22]];

			var machining = machining_data.sort(function(a, b){return a[2] - b[2]});
			
			var mesin_split = [];
			for (var i = 0; i < mesin.length; i++) {
				mesin_split.push(mesin[i].split('#'));
			}

			$('#head_machining').append().empty();
			$('#body_machining').append().empty();

			var head = '<tr>';
			var body = '<tr>';
			for (var i = 0; i < machining.length; i++) {
				for (var j = 0; j < mesin_split.length; j++) {
					if(machining[i][0] == mesin_split[j][0]){

						var merah = mesin_split[j][4].split(':');
						var merah_time = (parseInt(merah[0])*3600) + (parseInt(merah[1])*60) + parseInt(merah[2]);
						var mr_to_h = merah_time / 60 /60;
						var putih = mesin_split[j][8].split(':');
						var putih_time = (parseInt(putih[0])*3600) + (parseInt(putih[1])*60) + parseInt(putih[2]);
						var mr_to_p = putih_time / 60 /60;


						//head
						if (mesin_split[j][1] == 0){//merah
							head += '<th class="hd" style="background-color: #f24b4b; width:5%;">'+machining[i][1].replace("MC ","")+'<br><span>'+mr_to_h.toFixed(1)+' H</span></th>';
							audio_error.play();

						}else if(mesin_split[j][1] == 1){//hijau
							head += '<th class="hd" style="background-color: #00a65a; width:5%;">'+machining[i][1].replace("MC ","")+'<br><span>&nbsp;</span></th>';
						}else if(mesin_split[j][1] == 2){//kuning
							head += '<th class="hd" style="background-color: #FCF33A; width:5%;">'+machining[i][1].replace("MC ","")+'<br><span>&nbsp;</span></th>';
						}else if(mesin_split[j][1] == 3){//biru
							head += '<th class="hd" style="background-color: #3366cc; width:5%;">'+machining[i][1].replace("MC ","")+'<br><span>&nbsp;</span></th>';
						}else if(mesin_split[j][1] == 4){//putih
							head += '<th class="hd" style="background-color: #fcfdff; width:5%;">'+machining[i][1].replace("MC ","")+'<br><span>'+mr_to_p.toFixed(1)+' H</span></th>';
						}else if(mesin_split[j][1] == 5){//hitam	
							head += '<th class="hd" style="background-color: #000; color: #fff; width:5%;">'+machining[i][1].replace("MC ","")+'<br><span>&nbsp;</span></th>';
						}

						//body
						body += '<td style="padding: 0px;">';
						var hijau = mesin_split[j][5].split(':');
						var kuning = mesin_split[j][6].split(':');
						var biru = mesin_split[j][7].split(':');
						var hitam = mesin_split[j][9].split(':');

						var hijau_time = (parseInt(hijau[0])*3600) + (parseInt(hijau[1])*60) + parseInt(hijau[2]);
						var kuning_time = (parseInt(kuning[0])*3600) + (parseInt(kuning[1])*60) + parseInt(kuning[2]);
						var biru_time = (parseInt(biru[0])*3600) + (parseInt(biru[1])*60) + parseInt(biru[2]);
						var hitam_time = (parseInt(hitam[0])*3600) + (parseInt(hitam[1])*60) + parseInt(hitam[2]);
						var total_time = merah_time + hijau_time + kuning_time + biru_time + putih_time + hitam_time;


						body += '<div style="height: 100px;">';
						body += '<div style="background-color: #f24b4b; height: '+ (merah_time/total_time)*100 +'%;"></div>';
						body += '<div style="background-color: #00a65a; height: '+ (hijau_time/total_time)*100 +'%;"></div>';
						body += '<div style="background-color: #FCF33A; height: '+ (kuning_time/total_time)*100 +'%;"></div>';
						body += '<div style="background-color: #3366cc; height: '+ (biru_time/total_time)*100 +'%;"></div>';
						body += '<div style="background-color: #fcfdff; height: '+ (putih_time/total_time)*100 +'%;"></div>';
						body += '<div style="background-color: #000; height: '+ (hitam_time/total_time)*100 +'%;"></div>';
						body += '</div>';

						body += '</td>';				
					}
				}
			}

			head += '</tr>';
			body += '</tr>';

			$('#head_machining').append(head);
			$('#body_machining').append(body);


			//Press
			var press_data = [
			['100', 'K-Mkp',1],
			['99', 'K-Nkp',2],
			['63', 'K-Nuki',3],
			['75', 'Kom#1',4],
			['76', 'Kom#2',5],
			['77', 'Kom#3',6],
			['78', 'Kom#4',7],
			['79', 'Kom#5',8],
			['81', 'Amd-PC',9],
			['80', 'Amd#1',10],
			['69', 'Amd#2',11],
			['70', 'Amd#3',12],
			['71', 'Amd#4',13],
			['72', 'Amd#5',14],
			['73', 'Amd#6',15],
			['74', 'Amd#7',16]];

			$('#head_press').append().empty();
			$('#body_press').append().empty();

			var head = '<tr>';
			var body = '<tr>';
			for (var i = 0; i < press_data.length; i++) {
				for (var j = 0; j < mesin_split.length; j++) {
					if(press_data[i][0] == mesin_split[j][0]){

						var merah = mesin_split[j][4].split(':');
						var merah_time = (parseInt(merah[0])*3600) + (parseInt(merah[1])*60) + parseInt(merah[2]);
						var mr_to_h = merah_time / 60 /60;
						var putih = mesin_split[j][8].split(':');
						var putih_time = (parseInt(putih[0])*3600) + (parseInt(putih[1])*60) + parseInt(putih[2]);
						var mr_to_p = putih_time / 60 /60;

						//head
						if (mesin_split[j][1] == 0){//merah
							head += '<th class="hd" style="background-color: #f24b4b; width:5%;">'+press_data[i][1]+'<br><span>'+mr_to_h.toFixed(1)+' H</span></th>';
							audio_error.play();

						}else if(mesin_split[j][1] == 1){//hijau
							head += '<th class="hd" style="background-color: #00a65a; width:5%;">'+press_data[i][1]+'<br><span>&nbsp;</span></th>';
						}else if(mesin_split[j][1] == 2){//kuning
							head += '<th class="hd" style="background-color: #FCF33A; width:5%;">'+press_data[i][1]+'<br><span>&nbsp;</span></th>';
						}else if(mesin_split[j][1] == 3){//biru
							head += '<th class="hd" style="background-color: #3366cc; width:5%;">'+press_data[i][1]+'<br><span>&nbsp;</span></th>';
						}else if(mesin_split[j][1] == 4){//putih
							head += '<th class="hd" style="background-color: #fcfdff; width:5%;">'+press_data[i][1]+'<br><span>'+mr_to_p.toFixed(1)+' H</span></th>';
						}else if(mesin_split[j][1] == 5){//hitam	
							head += '<th class="hd" style="background-color: #000; color: #fff; width:5%;">'+press_data[i][1]+'<br><span>&nbsp;</span></th>';
						}

						//body
						body += '<td style="padding: 0px;">';
						var hijau = mesin_split[j][5].split(':');
						var kuning = mesin_split[j][6].split(':');
						var biru = mesin_split[j][7].split(':');
						var hitam = mesin_split[j][9].split(':');

						var hijau_time = (parseInt(hijau[0])*3600) + (parseInt(hijau[1])*60) + parseInt(hijau[2]);
						var kuning_time = (parseInt(kuning[0])*3600) + (parseInt(kuning[1])*60) + parseInt(kuning[2]);
						var biru_time = (parseInt(biru[0])*3600) + (parseInt(biru[1])*60) + parseInt(biru[2]);
						var hitam_time = (parseInt(hitam[0])*3600) + (parseInt(hitam[1])*60) + parseInt(hitam[2]);
						var total_time = merah_time + hijau_time + kuning_time + biru_time + putih_time + hitam_time;


						body += '<div style="height: 100px;">';
						body += '<div style="background-color: #f24b4b; height: '+ (merah_time/total_time)*100 +'%;"></div>';
						body += '<div style="background-color: #00a65a; height: '+ (hijau_time/total_time)*100 +'%;"></div>';
						body += '<div style="background-color: #FCF33A; height: '+ (kuning_time/total_time)*100 +'%;"></div>';
						body += '<div style="background-color: #3366cc; height: '+ (biru_time/total_time)*100 +'%;"></div>';
						body += '<div style="background-color: #fcfdff; height: '+ (putih_time/total_time)*100 +'%;"></div>';
						body += '<div style="background-color: #000; height: '+ (hitam_time/total_time)*100 +'%;"></div>';
						body += '</div>';

						body += '</td>';				
					}
				}
			}

			head += '</tr>';
			body += '</tr>';

			$('#head_press').append(head);
			$('#body_press').append(body);


			//Injeksi
			var injeksi_data = [
			['47','Inj#1'],
			['82','Inj#2'],
			['57','Inj#3'],
			['58','Inj#4'],
			['59','Inj#5'],
			['60','Inj#6'],
			['61','Inj#7'],
			['83','Inj#8'],
			['62','Inj#9'],
			['91','Inj#10'],
			['64','Inj#11'],
			['92','Inj#12'],
			['67','Inj#13'],
			['68','Inj#14'],
			['65','Inj#15'],
			['93','Inj#16'],
			['96','Inj#17'],
			['66','Inj#18'],
			['97','Inj#19']];


			$('#head_injeksi').append().empty();
			$('#body_injeksi').append().empty();

			var head = '<tr>';
			var body = '<tr>';
			for (var i = 0; i < injeksi_data.length; i++) {
				for (var j = 0; j < mesin_split.length; j++) {
					if(injeksi_data[i][0] == mesin_split[j][0]){

						var merah = mesin_split[j][4].split(':');
						var merah_time = (parseInt(merah[0])*3600) + (parseInt(merah[1])*60) + parseInt(merah[2]);
						var mr_to_h = merah_time / 60 /60;
						var putih = mesin_split[j][8].split(':');
						var putih_time = (parseInt(putih[0])*3600) + (parseInt(putih[1])*60) + parseInt(putih[2]);
						var mr_to_p = putih_time / 60 /60;


						//head
						if (mesin_split[j][1] == 0){//merah
							head += '<th class="hd" style="background-color: #f24b4b; width:5%;">'+injeksi_data[i][1]+'<br><span>'+mr_to_h.toFixed(1)+' H</span></th>';
							audio_error.play();

						}else if(mesin_split[j][1] == 1){//hijau
							head += '<th class="hd" style="background-color: #00a65a; width:5%;">'+injeksi_data[i][1]+'<br><span>&nbsp;</span></th>';
						}else if(mesin_split[j][1] == 2){//kuning
							head += '<th class="hd" style="background-color: #FCF33A; width:5%;">'+injeksi_data[i][1]+'<br><span>&nbsp;</span></th>';
						}else if(mesin_split[j][1] == 3){//biru
							head += '<th class="hd" style="background-color: #3366cc; width:5%;">'+injeksi_data[i][1]+'<br><span>&nbsp;</span></th>';
						}else if(mesin_split[j][1] == 4){//putih
							head += '<th class="hd" style="background-color: #fcfdff; width:5%;">'+injeksi_data[i][1]+'<br><span>'+mr_to_p.toFixed(1)+' H</span></th>';
						}else if(mesin_split[j][1] == 5){//hitam	
							head += '<th class="hd" style="background-color: #000; color: #fff; width:5%;">'+injeksi_data[i][1]+'<br><span>&nbsp;</span></th>';
						}

						//body
						body += '<td style="padding: 0px;">';
						var hijau = mesin_split[j][5].split(':');
						var kuning = mesin_split[j][6].split(':');
						var biru = mesin_split[j][7].split(':');
						var hitam = mesin_split[j][9].split(':');

						var hijau_time = (parseInt(hijau[0])*3600) + (parseInt(hijau[1])*60) + parseInt(hijau[2]);
						var kuning_time = (parseInt(kuning[0])*3600) + (parseInt(kuning[1])*60) + parseInt(kuning[2]);
						var biru_time = (parseInt(biru[0])*3600) + (parseInt(biru[1])*60) + parseInt(biru[2]);
						var hitam_time = (parseInt(hitam[0])*3600) + (parseInt(hitam[1])*60) + parseInt(hitam[2]);
						var total_time = merah_time + hijau_time + kuning_time + biru_time + putih_time + hitam_time;


						body += '<div style="height: 100px;">';
						body += '<div style="background-color: #f24b4b; height: '+ (merah_time/total_time)*100 +'%;"></div>';
						body += '<div style="background-color: #00a65a; height: '+ (hijau_time/total_time)*100 +'%;"></div>';
						body += '<div style="background-color: #FCF33A; height: '+ (kuning_time/total_time)*100 +'%;"></div>';
						body += '<div style="background-color: #3366cc; height: '+ (biru_time/total_time)*100 +'%;"></div>';
						body += '<div style="background-color: #fcfdff; height: '+ (putih_time/total_time)*100 +'%;"></div>';
						body += '<div style="background-color: #000; height: '+ (hitam_time/total_time)*100 +'%;"></div>';
						body += '</div>';

						body += '</td>';				
					}
				}
			}

			head += '</tr>';
			body += '</tr>';

			$('#head_injeksi').append(head);
			$('#body_injeksi').append(body);



			//Senban
			var senban_data = [
			['50','LT#1'],
			['29','LT#2'],
			['30','LT#3'],
			['31','LT#4'],
			['32','LT#5'],
			['51','LT#6'],
			['53','LT#7'],
			['54','LT#8'],
			['55','LT#9'],
			['56','LT#10'],
			['52','LT#11'],
			['48','LT#12'],
			['49','LT#13'],
			['45','LT#14'],
			['46','LT#15'],
			['43','LT#16'],
			['44','LT#17'],
			['33','LT#18'],
			['34','LT#19'],
			['35','LT#20'],
			['36','LT#21'],
			['37','LT#22'],
			['38','LT#23'],
			['39','LT#24'],
			['40','LT#25'],
			['41','LT#26'],
			['42','LT#27'],
			['26','LT#28'],
			['25','LT#29'],
			['24','LT#30'],
			['23','LT#31'],];

			$('#head_senban').append().empty();
			$('#body_senban').append().empty();

			var head = '<tr>';
			var body = '<tr>';
			for (var i = 0; i < senban_data.length; i++) {
				for (var j = 0; j < mesin_split.length; j++) {
					if(senban_data[i][0] == mesin_split[j][0]){

						var merah = mesin_split[j][4].split(':');
						var merah_time = (parseInt(merah[0])*3600) + (parseInt(merah[1])*60) + parseInt(merah[2]);
						var mr_to_h = merah_time / 60 /60;
						var putih = mesin_split[j][8].split(':');
						var putih_time = (parseInt(putih[0])*3600) + (parseInt(putih[1])*60) + parseInt(putih[2]);
						var mr_to_p = putih_time / 60 /60;

						//head
						if (mesin_split[j][1] == 0){//merah
							head += '<th class="hd" style="background-color: #f24b4b; width:5%;">'+senban_data[i][1]+'<br><span>'+mr_to_h.toFixed(1)+' H</span></th>';
							audio_error.play();

						}else if(mesin_split[j][1] == 1){//hijau
							head += '<th class="hd" style="background-color: #00a65a; width:5%;">'+senban_data[i][1]+'<br><span>&nbsp;</span></th>';
						}else if(mesin_split[j][1] == 2){//kuning
							head += '<th class="hd" style="background-color: #FCF33A; width:5%;">'+senban_data[i][1]+'<br><span>&nbsp;</span></th>';
						}else if(mesin_split[j][1] == 3){//biru
							head += '<th class="hd" style="background-color: #3366cc; width:5%;">'+senban_data[i][1]+'<br><span>&nbsp;</span></th>';
						}else if(mesin_split[j][1] == 4){//putih
							head += '<th class="hd" style="background-color: #fcfdff; width:5%;">'+senban_data[i][1]+'<br><span>'+mr_to_p.toFixed(1)+' H</span></th>';
						}else if(mesin_split[j][1] == 5){//hitam	
							head += '<th class="hd" style="background-color: #000; color: #fff; width:5%;">'+senban_data[i][1]+'<br><span>&nbsp;</span></th>';
						}

						//body
						body += '<td style="padding: 0px;">';
						var hijau = mesin_split[j][5].split(':');
						var kuning = mesin_split[j][6].split(':');
						var biru = mesin_split[j][7].split(':');
						var hitam = mesin_split[j][9].split(':');

						var hijau_time = (parseInt(hijau[0])*3600) + (parseInt(hijau[1])*60) + parseInt(hijau[2]);
						var kuning_time = (parseInt(kuning[0])*3600) + (parseInt(kuning[1])*60) + parseInt(kuning[2]);
						var biru_time = (parseInt(biru[0])*3600) + (parseInt(biru[1])*60) + parseInt(biru[2]);
						var hitam_time = (parseInt(hitam[0])*3600) + (parseInt(hitam[1])*60) + parseInt(hitam[2]);
						var total_time = merah_time + hijau_time + kuning_time + biru_time + putih_time + hitam_time;


						body += '<div style="height: 100px;">';
						body += '<div style="background-color: #f24b4b; height: '+ (merah_time/total_time)*100 +'%;"></div>';
						body += '<div style="background-color: #00a65a; height: '+ (hijau_time/total_time)*100 +'%;"></div>';
						body += '<div style="background-color: #FCF33A; height: '+ (kuning_time/total_time)*100 +'%;"></div>';
						body += '<div style="background-color: #3366cc; height: '+ (biru_time/total_time)*100 +'%;"></div>';
						body += '<div style="background-color: #fcfdff; height: '+ (putih_time/total_time)*100 +'%;"></div>';
						body += '<div style="background-color: #000; height: '+ (hitam_time/total_time)*100 +'%;"></div>';
						body += '</div>';

						body += '</td>';				
					}
				}
			}

			head += '</tr>';
			body += '</tr>';

			$('#head_senban').append(head);
			$('#body_senban').append(body);

		});

}

function secondsToDhms(seconds) {
	seconds = Number(seconds);
	var d = Math.floor(seconds / (3600*24));
	var h = Math.floor(seconds % (3600*24) / 3600);
	var m = Math.floor(seconds % 3600 / 60);
	var s = Math.floor(seconds % 60);

	var dDisplay = d > 0 ? d + (d == 1 ? " day, " : " days, ") : "";
	var hDisplay = h > 0 ? h + (h == 1 ? " hour, " : " hours, ") : "";
	var mDisplay = m > 0 ? m + (m == 1 ? " minute, " : " minutes, ") : "";
	var sDisplay = s > 0 ? s + (s == 1 ? " second" : " seconds") : "";
	return dDisplay + hDisplay + mDisplay + sDisplay;
}

</script>
@endsection