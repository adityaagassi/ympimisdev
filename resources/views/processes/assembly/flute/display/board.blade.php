@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	table.table-bordered{
		border:1px solid rgb(150,150,150);
	}
	table.table-bordered > thead > tr > th{
		border:1px solid rgb(150,150,150);
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		border-top: 2px solid white;
		vertical-align: middle;
		text-align: center;
		padding:1px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(150,150,150);
		padding:0;
	}
	table.table-bordered > tbody > tr > td > p{
		color: #abfbff;
	}
	.content{
		color: white;
		font-weight: bold;
	}

	hr {
		margin: 0px;
	}

	.akan {
		/*width: 50px;
		height: 50px;*/
		-webkit-animation: akan 1s infinite;  /* Safari 4+ */
		-moz-animation: akan 1s infinite;  /* Fx 5+ */
		-o-animation: akan 1s infinite;  /* Opera 12+ */
		animation: akan 1s infinite;  /* IE 10+, Fx 29+ */
	}
	
	@-webkit-keyframes akan {
		0%, 49% {
			background: rgba(0, 0, 0, 0);
			/*opacity: 0;*/
		}
		50%, 100% {
			background-color: rgb(243, 156, 18);
		}
	}

	.sedang {
		/*width: 50px;
		height: 50px;*/
		-webkit-animation: sedang 1s infinite;  /* Safari 4+ */
		-moz-animation: sedang 1s infinite;  /* Fx 5+ */
		-o-animation: sedang 1s infinite;  /* Opera 12+ */
		animation: sedang 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes sedang {
		0%, 49% {
			background: rgba(0, 0, 0, 0);
		}
		50%, 100% {
			background-color: #4ff05a;
		}
	}
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">
	<h1>
		<span class="text-yellow">
			{{ $title }}
		</span>
		<small>
			<span style="color: #FFD700;"> {{ $title_jp }}</span>
		</small>
	</h1>
</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding: 0px;">
	<input type="hidden" value="{{ $loc }}" id="loc">
	<div class="row" style="padding-left: 10px;padding-right: 10px">
		<div class="col-xs-6">
			<table id="assemblyTable" class="table table-bordered">
				<thead style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;">
					<tr>
						<th style="width: 0.66%; padding: 0;">WS</th>
						<th style="width: 0.66%; padding: 0;">Operator</th>
						<th style="width: 0.66%; padding: 0; background-color:#4ff05a;">Sedang</th>
					</tr>
				</thead>
				<tbody id="assemblyTableBody">
				</tbody>
				<tfoot>
				</tfoot>
			</table>
		</div>
		<div class="col-xs-6">
			<!-- <div class="box box-solid">
				<div class="box-body">
					<div id="container2" style="width:100%; height:200px;"></div>
				</div>
			</div>
			<div class="box box-solid">
				<div class="box-body">
					<div id="container" style="width:100%; height:200px;"></div>
				</div>
			</div>
			<div class="box box-solid">
				<div class="box-body">
					<div id="container3" style="width:100%; height:200px;"></div>
				</div>
			</div> -->
			<div class="box box-solid">
				<div class="box-body">
					<div id="container2" style="width:100%; height:310px;"></div>
					<div id="container" style="width:100%; height:310px;"></div>
					<div id="container3" style="width:100%; height:310px;"></div>
				</div>
			</div>
		</div>
	</div>

	<!-- <div class="modal fade" id="myModal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header" style="color: black; padding-bottom: : 0px;">
					<h4 style="float: right;" id="modal-title"></h4>
					<h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
					<br><h4 class="modal-title" id="judul_table"></h4>
				</div>
				<div class="modal-body" style="padding-top: 0px;">
					<div class="row">
						<div class="col-md-12">
							<table id="tableDetail" class="table table-bordered" style="width: 100%;">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 15%;">No.</th>
										<th style="width: 15%;">WS</th>
										<th style="width: 25%;">Material Number</th>
										<th style="width: 45%;">Material Description</th> 
									</tr>
								</thead>
								<tbody id="bodyTableDetail" style="color: black">
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
				</div>
			</div>
		</div>
	</div> -->
</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		fetchTable();
		setInterval(fetchTable, 1000);
		fillChartActualNgByOp();
		setInterval(function(){
			fillChartActualNgByOp();
		}, 60000);
		fillChartActualNg();
		setInterval(function(){
			fillChartActualNg();
		}, 60000);
	});

	var akan_assy = [];
	var akan_assy_kosong = [];
	var sedang = [];
	var sedang_kosong = [];

	var totalAkan = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
	var totalAkanKosong = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];

	var totalSedang = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
	var totalSedangKosong = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];


	function setTimeSedang(index) {
		if(sedang[index]){
			totalSedang[index]++;
			return pad(parseInt(totalSedang[index] / 3600)) + ':' + pad(parseInt((totalSedang[index] % 3600) / 60)) + ':' + pad((totalSedang[index] % 3600) % 60);
		}else{
			return '';
		}
	}

	function setTimeSedangKosong(index) {
		if(sedang_kosong[index]){
			totalSedangKosong[index]++;
			return pad(parseInt(totalSedangKosong[index] / 3600)) + ':' + pad(parseInt((totalSedangKosong[index] % 3600) / 60)) + ':' + pad((totalSedangKosong
				[index] % 3600) % 60);
		}else{
			return '';
		}
	}

	function setTimeAkan(index) {
		if(akan_wld[index]){
			totalAkan[index]++;
			return pad(parseInt(totalAkan[index] / 3600)) + ':' + pad(parseInt((totalAkan[index] % 3600) / 60)) + ':' + pad((totalAkan[index] % 3600) % 60);
		}else{
			return '';
		}
	}

	function setTimeAkanKosong(index) {
		if(akan_wld_kosong[index]){
			totalAkanKosong[index]++;
			return pad(parseInt(totalAkanKosong[index] / 3600)) + ':' + pad(parseInt((totalAkanKosong[index] % 3600) / 60)) + ':' + pad((totalAkanKosong[index] % 3600) % 60);
		}else{
			return '';
		}
	}


	function pad(val) {
		var valString = val + "";
		if (valString.length < 2) {
			return "0" + valString;
		} else {
			return valString;
		}
	}

	function fetchTable(){
		var loc = $('#loc').val();

		var data = {
			loc : loc
		}

		$.get('{{ url("fetch/assembly/board") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					akan_wld = [];
					akan_wld_kosong = [];
					sedang = [];
					sedang_kosong = [];


					$('#assemblyTableBody').html("");
					var assemblyTableBody = "";
					var i = 0;
					var color2 = "";
					var color3 = "";
					var colorShift = "";

					$.each(result.boards, function(index, value){
						if (i % 2 === 0 ) {
							if (value.employee_id) {
								color = '';

								if (value.akan == "<br>")
									color2 = 'class="akan"';
								else
									color2 = 'style="color:#ffd03a"';


								if (value.sedang == "<br>")
									color3 = 'class="sedang"';
								else
									color3 = 'style="color:#a4fa98"';
							}
							else {
								color = '';
								color2 = '';
								color3 = '';
							}
						} else {
							if (value.employee_id) {
								color = 'style="background-color: #575c57"';

								if (value.akan == "<br>")
									color2 = 'class="akan"';
								else
									color2 = 'style="color:#ffd03a"';

								if (value.sedang == "<br>")
									color3 = 'class="sedang"';
								else
									color3 = 'style="color:#a4fa98"';
							}
							else {
								color = 'style="background-color: #575c57"';
								color2 = '';
								color3 = '';
							}
						}


						if (value.sedang != "<br>") {
							sedang_time = value.sedang_time;
							var sedang2 = value.sedang;

							sedang.push(true);
							sedang_kosong.push(false);
							totalSedangKosong[index] = 0;


						} else {
							var sedang2 = "";
							sedang_time = "";

							sedang.push(false);
							sedang_kosong.push(true);
							totalSedang[index] = 0;

						}

						var color4 = 'style="color: red;"';

						var timeada = setTimeSedang(index);
						var timekosong = setTimeSedangKosong(index);

						var ng = [];

						if (timeada == '') {
							var percent = (0 / value.std_time) * 100;
						}else{
							var percent = (hmsToSecondsOnly(timeada) / value.std_time) * 100;
							// var percent = 200;
						}

						if (value.employee_id == null) {
							assemblyTableBody += '<tr '+color+'>';
							assemblyTableBody += '<td height="5%" style="font-size:20px">'+value.ws+'</td>';
							assemblyTableBody += '<td style="font-size:20px">Not Found</td>';
							assemblyTableBody += '<td '+color3+'>'+sedang2+'<br>'+timeada+timekosong+'</td>';
						}else{
							assemblyTableBody += '<tr '+color+'>';
							assemblyTableBody += '<td height="5%" style="font-size:20px">'+value.ws+'</td>';
							assemblyTableBody += '<td style="font-size:20px">'+value.employee_id+'<br>'+value.employee_name.split(' ').slice(0,2).join(' ')+'</td>';
							if (percent >= 100) {
								assemblyTableBody += '<td '+color4+'>'+sedang2+'<br>'+timeada+timekosong;
								assemblyTableBody += '<div class="progress-group">';
								assemblyTableBody += '<div class="progress" style="background-color: #212121; height: 20px; border: 1px solid; padding: 0px; margin: 0px;">';
								assemblyTableBody += '<div class="progress-bar progress-bar-danger progress-bar-striped" id="progress_bar_'+index+'" style="font-size: 12px; padding-top: 1%;width:'+parseFloat(percent)+'%;"></div>';
								assemblyTableBody += '</div>';
								assemblyTableBody += '</div>';
								assemblyTableBody += '</td>';
							}else{
								assemblyTableBody += '<td '+color3+'>'+sedang2+'<br>'+timeada+timekosong;
								assemblyTableBody += '<div class="progress-group">';
								assemblyTableBody += '<div class="progress" style="background-color: #212121; height: 20px; border: 1px solid; padding: 0px; margin: 0px;">';
								assemblyTableBody += '<div class="progress-bar progress-bar-success progress-bar-striped" id="progress_bar_'+index+'" style="font-size: 12px; padding-top: 0.5%;width:'+parseFloat(percent)+'%"></div>';
								assemblyTableBody += '</div>';
								assemblyTableBody += '</div>';
								assemblyTableBody += '</td>';
							}
						}
						assemblyTableBody += '</tr>';

						i += 1;

						data2 = {
							employee_id: value.employee_id
						}
					});

					$('#assemblyTableBody').append(assemblyTableBody);
				}
				else{
					alert('Attempt to retrieve data failed.');
				}
			}
		});
	}

function hmsToSecondsOnly(str) {
    var p = str.split(':'),
        s = 0, m = 1;

    while (p.length > 0) {
        s += m * parseInt(p.pop(), 10);
        m *= 60;
    }

    return s;
}

function fillChartActualNgByOp() {
	var loc = $('#loc').val();

		var data = {
			loc : loc
		}

		$.get('{{ url("fetch/assembly/board") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					var title_location = 'Qty NG By Operator';
					var title_location2 = 'Production Result By Operator';
					// var data = result.chartData;
					var xAxis = []
					, ngname = []
					, ngqty = []
					, xAxis2 = []
					, perolehan = []

					for (i = 0; i < result.boards.length; i++) {
						if (result.boards[i].ng_name != null) {
							var ng = result.boards[i].ng_name.split(",");
							var qty = result.boards[i].qty_ng.split(",");
							xAxis.push(result.boards[i].employee_name.split(' ').slice(0,1).join(' '));
							xAxis2.push(result.boards[i].employee_name.split(' ').slice(0,1).join(' '));
							var jumlahng = 0;
							for(var j = 0;j<ng.length;j++){
								ngname.push(ng[j]);
								jumlahng = jumlahng + parseInt(qty[j]);
							}
							ngqty.push(jumlahng);
							perolehan.push(result.boards[i].perolehan);
						}
					}
					Highcharts.chart('container', {
						colors: ['rgba(248,161,63,1)','rgba(126,86,134,.9)'],
						chart: {
							type: 'column',
							backgroundColor: null
						},
						title: {
							text: '<span style="color:black;">'+title_location+'</span><br><span style="color:#6070C0;">??</span>'
						},
						exporting: { enabled: false },
						xAxis: {
							tickInterval:  1,
							overflow: true,
							categories: xAxis,
							labels:{
								rotation: -45,
								style: {
							        color: '#000'
							      }
							},
							min: 0					
						},
						yAxis: {
							min: 1,
							title: {
								text: '<span style="color:black;">PC(s)</span>'
							},
							labels:{
								style: {
							        color: '#000'
							      }
							},
							type:'logarithmic'
						},
						credits:{
							enabled: false
						},
						legend: {
							enabled: false
						},
						tooltip: {
							shared: true,
							style: {
						      color: '#000'
						    }
						},
						plotOptions: {
							series:{
								minPointLength: 5,
								pointPadding: 0.2,
								groupPadding: 0.3,
								borderWidth: 0.93,
								animation:false,
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										fontSize: '1vw',
										color:'#000',
										shadow: false,
										borderWidth: 0,
									}
								},
							},
							column: {
								grouping: false,
								shadow: false,
								borderWidth: 0.93,
							}
						},
						series: [{
							name: 'NG',
							data: ngqty,
							pointPadding: 0.05
						}]
					});

					Highcharts.chart('container3', {
						colors: ['rgba(248,161,63,1)','rgba(126,86,134,.9)'],
						chart: {
							type: 'column',
							backgroundColor: null
						},
						title: {
							text: '<span style="color:black;">'+title_location2+'</span><br><span style="color:#6070C0;">??</span>'
						},
						exporting: { enabled: false },
						xAxis: {
							tickInterval:  1,
							overflow: true,
							categories: xAxis2,
							labels:{
								rotation: -45,
								style: {
							        color: '#000'
							      }
							},
							min: 0					
						},
						yAxis: {
							min: 1,
							title: {
								text: '<span style="color:black;">Set(s)</span>'
							},
							labels:{
								style: {
							        color: '#000'
							      }
							},
							type:'logarithmic'
						},
						credits:{
							enabled: false
						},
						legend: {
							enabled: false
						},
						tooltip: {
							shared: true,
							style: {
						      color: '#000'
						    }
						},
						plotOptions: {
							series:{
								minPointLength: 5,
								pointPadding: 0.2,
								groupPadding: 0.3,
								borderWidth: 0.93,
								animation:false,
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										fontSize: '1vw',
										color:'#000',
										shadow: false,
										borderWidth: 0,
									}
								},
							},
							column: {
								grouping: false,
								shadow: false,
								borderWidth: 0.93,
							}
						},
						series: [{
							name: 'Perolehan',
							data: perolehan,
							pointPadding: 0.05,
							color:'#009688'
						}]
					});
				}
				else{
					alert('Attempt to retrieve data failed.');
				}
			}
		});
	var locations = $('#loc').val().split("-");
	var data = {
		location:locations[0]
	}
}

function fillChartActualNg() {
	var loc = $('#loc').val();

		var data = {
			loc : loc
		}

		$.get('{{ url("fetch/assembly/board") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					var title_location = 'Qty NG';
					// var data = result.chartData;

					var totalng = 0;
					var xAxis = []
					, ngname = []
					, ngqty = []
					
					for (i = 0; i < result.ng.length; i++) {
						xAxis.push(result.ng[i].ng_name);
						ngqty.push(result.ng[i].qty_ng);
					}
					// console.log(ngqty);
					Highcharts.chart('container2', {
						colors: ['rgba(248,161,63,1)','rgba(126,86,134,.9)'],
						chart: {
							type: 'column',
							backgroundColor: null
						},
						title: {
							text: '<span style="color:black;">'+title_location+'</span><br><span style="color:#6070C0;">??</span>'
						},
						exporting: { enabled: false },
						xAxis: {
							tickInterval:  1,
							overflow: true,
							categories: xAxis,
							labels:{
								rotation: -45,
								style: {
							        color: '#000'
							      }
							},
							min: 0					
						},
						yAxis: {
							min: 1,
							title: {
								text: '<span style="color:black;">PC(s)</span>'
							},
							labels:{
								style: {
							        color: '#000'
							      }
							},
							type:'logarithmic'
						},
						credits:{
							enabled: false
						},
						legend: {
							enabled: false
						},
						tooltip: {
							shared: true,
							style: {
						      color: '#000'
						    }
						},
						plotOptions: {
							series:{
								minPointLength: 5,
								pointPadding: 0.2,
								groupPadding: 0.3,
								borderWidth: 0.93,
								animation:false,
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										fontSize: '1vw',
										color:'#000',
										shadow: false,
										borderWidth: 0,
									}
								},
							},
							column: {
								grouping: false,
								shadow: false,
								borderWidth: 0.93,
							}
						},
						series: [{
							name: 'NG',
							data: ngqty,
							pointPadding: 0.05,
							color:'#cddc39'
						}]
					});
				}
				else{
					alert('Attempt to retrieve data failed.');
				}
			}
		});
	var locations = $('#loc').val().split("-");
	var data = {
		location:locations[0]
	}
}

var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

function openSuccessGritter(title, message){
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-success',
		image: '{{ url("images/image-screen.png") }}',
		sticky: false,
		time: '3000'
	});
}

function openErrorGritter(title, message) {
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-danger',
		image: '{{ url("images/image-stop.png") }}',
		sticky: false,
		time: '3000'
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
	return year + "-" + month + "-" + day + " " + h + ":" + m + ":" + s;
}

function getActualTime() {
	var d = new Date();
	var h = addZero(d.getHours());
	var m = addZero(d.getMinutes());
	var s = addZero(d.getSeconds());
	return h + ":" + m + ":" + s;
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
</script>
@endsection