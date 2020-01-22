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
				<!-- <input type="text" class="form-control pull-right" name="fy" placeholder="Select Fiscal Year"> -->
				<select class="form-control select2" id="fy" name="fy" placeholder="Select Fiscal Year" style="border-color: #605ca8">
					<option></option>
                  @foreach($fiscaly as $fiscal)
                    <option value="{{ $fiscal->fiscal_year }}">{{ $fiscal->fiscal_year }}</option>
                  @endforeach
                </select>
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
                      <th style="vertical-align: middle;width:2%;font-size: 18px">Kategori CPAR</th>
                      <th style="vertical-align: middle;width:2%;font-size: 18px">Tipe CPAR</th>
                      <th style="vertical-align: middle;width:2%;font-size: 18px">Sumber Komplain</th>          
                      <th style="vertical-align: middle;width:14%;font-size: 18px">Deskripsi</th>
                      <th style="vertical-align: middle;width:14%;font-size: 18px">Syarat</th>
                      <th style="vertical-align: middle;width:2%;font-size: 18px">Jumlah</th>
                    </tr>
                  </thead>
                  <tbody>
                  	<tr>
                  		<td rowspan="3">Eksternal</td>
                  		<td rowspan="3">Komplain Parts dan FG yang diproduksi oleh YMPI</td>
                  		<td>NG Jelas</td>
                  		<td>Komplain diterima by email oleh GM dari QA YCJ (Tiap bulan)</td>
                  		<td>Semua</td>
                  		<td><h3 style="margin: 0px;font-size: 2vw;" id='NG'>0</h3></td>
                  	</tr>
                  	<tr>
                  		<td>KD Parts - YMMJ dan XY</td>
                  		<td>Komplain dalam bentuk form zeseisochi atau email yang  diterima  oleh GM dari YMMJ dan XY</td>
                  		<td>Semua</td>
                  		<td><h3 style="margin: 0px;font-size: 2vw;" id='KD'>0</h3></td>
                  	</tr>
                  	<tr>
                  		<td>FG - YMID, Japan, etc</td>
                  		<td>Japan & Other : Komplain dalam bentuk email yang  diterima  oleh GM <br>YMID : Komplain dalam bentuk email yang diterima oleh Manager Logistik</td>
                  		<td>Semua</td>
                  		<td><h3 style="margin: 0px;font-size: 2vw;" id='FG'>0</h3></td>
                  	</tr>
                  	<!-- <tr>
                  		<td>Claim Rate</td>
                  		<td>Komplain diterima by email oleh GM dari QA YCJ (Tiap bulan)</td>
                  		<td>Kasus critical atau kasus terjadi 3x berturut-turut</td>
                  		<td><h3 style="margin: 0px;font-size: 2vw;" id='Claim'>0</h3></td>
                  	</tr> -->
                  	<tr>
                  		<td>Internal</td>
                  		<td>Informasi antar bagian di dalam proses</td>
                  		<td>Ketidaksesuaian Kualitas</td>
                  		<td>Informasi ketidaksesuaian kualitas dari bagian yang menemukan ke sumber masalah (kasus critical : salah spec, salah dimensi, bari, etc)</td>
                  		<td>1. NG critical (misal : tidak ada screw, bari sentan pada recorder, Ketinggian  block recorder melebihi standard, etc)<br>2. NG visual yang menyebabkan recheck pada stock FSTK atau memerlukan repair offline (perlu penambahan manpower atau overtime)</td>
                  		<td><h3 style="margin: 0px;font-size: 2vw;" id='Kualitas'>0</h3></td>
                  	</tr>
                  	<tr>
                  		<td rowspan="2">Supplier</td>
                  		<td rowspan="2">Komplain yang diberikan YMPI ke Supplier</td>
                  		<td>Non YMMJ</td>
                  		<td>Komplain yang diberikan YMPI ke supplier sumber masalah</td>
                  		<td>NG critical atau bila terjadi lot out</td>
                  		<td><h3 style="margin: 0px;font-size: 2vw;" id='NonYMMJ'>0</h3></td>
                  	</tr>
                  	<tr>
                  		<td>YMMJ</td>
                  		<td>Komplain yang diberikan YMPI ke YMMJ untuk parts/material</td>
                  		<td>NG critical, Jumlah NG melebihi 5% atau jumlah NG mengganggu proses harian</td>
                  		<td><h3 style="margin: 0px;font-size: 2vw;" id='YMMJ'>0</h3></td>
                  	</tr>
                  	<tr style="background-color: green">
                  		<td colspan="5"><h3 style="margin: 0"><b>Total</b></h3></td>
                  		<td><h3 style="margin: 0px;font-size: 2.5vw;" id='total_kategori'>0</h3></td>
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
						<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>CPAR On Progress</b></h3>
						{{-- <h3 style="margin-bottom: 0px;font-size: 25px;"><b>(PIANICA)</b></h3> --}}
						<h2 style="margin: 0px;font-size: 4vw;" id='cpar'>0<sup style="font-size: 2vw"> kasus</sup></h2>
					</div>
					<div class="icon">
						<i class="ion ion-stats-bars"></i>
					</div>
				</div>
			</div>
			<div class="col-lg-12 col-xs-12" style="margin-left: 0px; padding: 0px;">
				<!-- small box -->
				<div class="small-box bg-yellow" style="font-size: 30px;font-weight: bold;height: 143px;">
					<div class="inner" style="padding-bottom: 0px;">
						<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>CAR On Progress</b></h3>
						<h2 style="margin: 0px; font-size: 4vw;" id='car'>0<sup style="font-size: 2vw"> kasus</sup></h2>
					</div>
					<div class="icon">
						<i class="ion ion-stats-bars"></i>
					</div>
				</div>
			</div>
			<div class="col-lg-12 col-xs-12" style="margin-left: 0px; padding: 0px;">
				<!-- small box -->
				<div class="small-box bg-blue" style="font-size: 30px;font-weight: bold;height: 143px;">
					<div class="inner" style="padding-bottom: 0px;">
						<h3 style="margin-bottom: 0px;font-size: 1.5vw;"><b>Verification On Progress</b></h3>
						<h2 style="margin: 0px; font-size: 4vw;" id='qa'>0<sup style="font-size: 2vw"> kasus</sup></h2>
					</div>
					<div class="icon">
						<i class="ion ion-stats-bars"></i>
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
		setInterval(drawNumber, 300000);
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

		$.get('{{ url("fetch/cpar/resume") }}', data, function(result, status, xhr){
			if(result.status){
				var total = 0;
				var totalnonymmj = 0;
				var ymmj = 0;

				for(var i = 0; i < result.data_status.length; i++){
					$('#total').append().empty();
					total = result.data_status[i].total;
					ymmj = result.ymmj[i].jumlahymmj;

					totalkasus = total + ymmj;

					$('#total').html(totalkasus+ '<sup style="font-size: 30px"> kasus</sup>');

					$('#cpar').append().empty();
					$('#cpar').html(result.data_status[i].CPAR + '<sup style="font-size: 30px"> kasus</sup>');

					$('#car').append().empty();
					$('#car').html(result.data_status[i].CAR + '<sup style="font-size: 30px"> kasus</sup>');

					$('#qa').append().empty();
					$('#qa').html(result.data_status[i].QA + '<sup style="font-size: 30px"> kasus</sup>');
				}

				for(var i = 0; i < result.kategori.length; i++){
					$('#NG').append().empty();
					$('#NG').html(result.kategori[i].NG);
					var NG = result.kategori[i].NG;

					$('#KD').append().empty();
					$('#KD').html(result.kategori[i].KD);
					var KD = result.kategori[i].KD;

					$('#FG').append().empty();
					$('#FG').html(result.kategori[i].FG);
					var FG = result.kategori[i].FG;

					$('#Claim').append().empty();
					$('#Claim').html(result.kategori[i].Claim);
					var Claim = result.kategori[i].Claim;

					$('#Kualitas').append().empty();
					$('#Kualitas').html(result.kategori[i].Kualitas);
					var Kualitas = result.kategori[i].Kualitas;

					$('#NonYMMJ').append().empty();
					$('#NonYMMJ').html(result.kategori[i].NonYMMJ);
					var NonYMMJ = result.kategori[i].NonYMMJ;

					totalnonymmj = parseInt(NG) + parseInt(KD) + parseInt(FG) + parseInt(Claim) + parseInt(Kualitas) + parseInt(NonYMMJ);
				}

				for(var i = 0; i < result.ymmj.length; i++){
					$('#YMMJ').append().empty();
					$('#YMMJ').html(result.ymmj[i].jumlahymmj);

					ymmj = result.ymmj[i].jumlahymmj;
				}

				var totalfinal = totalnonymmj + ymmj;
				$('#total_kategori').append().empty();
				$('#total_kategori').html(totalfinal + '<sup style="font-size: 30px"> kasus</sup>');


			}

		});

	}
</script>
@endsection