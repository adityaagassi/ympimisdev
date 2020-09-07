<!DOCTYPE html>
<html>
<head>
</head>
<body >
	<style type="text/css">
		table {
			border-collapse: collapse;
		}
		.name {
			font-family: 'arial';
			/*-moz-transform:scale(1.3,1);*/
		}
		.product {
			font-size: 12pt;
			font-family: 'arial';
		}

		.kiri {
			font-size: 6pt;
			font-family: 'arial';
			/*font-weight: bold;*/
		}
		.bawah {
			font-size: 12pt;
			font-family: 'arial';
			font-weight: bold;
		}

	</style>

	@php
	include(app_path() . '\barcode\barcode.php');
	@endphp



	<table border="0" style="margin-left: 10px;">

		<input type="text" name="rem" id="rem" value="{{$remark}}" hidden="">
		@foreach($barcode as $nomor => $barcode)
		<input type="text" name="codesn" id="codesn" value="{{$barcode->serial_number}}" hidden="">
		<input type="text" name="codemodel" id="codemodel" value="{{$barcode->model}}" hidden="">
		<input type="text" name="codegmc" id="codegmc" value="{{$barcode->finished}}" hidden="">
		<input type="text" name="codejan" id="codejan" value="{{$barcode->janean}}" hidden="">
		<input type="text" name="codeupc" id="codeupc" value="{{$barcode->upc}}" hidden="">
		{{-- <input type="text" name="codeday" id="codeday" value="{{$barcode->date_code}}" hidden=""> a--}}
		<input type="text" name="codej" id="codej" value="{{$barcode->remark}}" hidden="">
		@endforeach

		{{-- @foreach($date as $nomor => $date)
		<input type="text" name="codeday" id="codeday" value="{{$date->date_code}}" hidden="">
		@endforeach --}}


		<tr>
			<td colspan="3" class="name" align="left" id="model" style="font-size: 14pt;padding-left: 25px; font-weight: bold; ">  </b></td>
		</tr>

		<tr>

			<td class="kiri" align="left"> 
				<img id="JAN" src="" style="position: fixed; top: 30px;left: 137px;height:50px;">
				<img id ="upc_jan2" src="" style="position: fixed; top: 30px;left: 137px;height:90px; width: 187.5px;">

				<b style="margin-left: 30px; position: fixed; top: 30px;left: 130px;right: 0px;height: 50px;" id="janT"> JAN/EAN </b>
				<b style="margin-left: 30px; position: fixed; top: 40px;left: 130px;right: 0px;height: 50px;" id="upc_janT2">JAN/EAN</b>

				<br>
				<img id ="upc" src="" style="position: fixed; top: 75px;left: 137px;height:50px;">
				<img id ="upc_jan" src="" style="position: fixed; top: 30px;left: 137px;height:90px; width: 187.5px;">

				<b style="margin-left: 30px; position: fixed; top: 75px;left: 130px;right: 0px;height: 50px;" id="upcT">UPC </b>
				<b style="margin-left: 30px; position: fixed; top: 40px;left: 130px;right: 0px;height: 50px;" id="upc_janT">UPC </b>
			</td>

			<td  class="kiri">
				<img id="gmc" src="" style="position: fixed; top: 43px; left: 30px;height:55px; width: 130px;">
				<img id ="upc_jangmc" src="" style="position: fixed; top: 25px;left: 30px;height:100px; width: 130px;">
				<b  id="gmcT" style="margin-left: 33px; position: fixed; top: 35px;left: 10px;right: 0px;height: 50px;">GMC </b>
				<b id="upc_jangmcT" style="margin-left: 33px; position: fixed; top: 40px;left: 10px;right: 0px;height: 50px;">GMC </b>
			</td>
		</tr>



	</table>

</body>
</html>
<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<script>
	jQuery(document).ready(function() {
		
		
		jan();
		upc();
		gmc();
		day();
		jumlah();

		// setTimeout(asd,2000);


		defineCustomPaperSize();
		printWindow(window, 'Label Besar');

		
	});

	function asd() {
		window.open('{{ url("index/label_kecil") }}'+'/21N37897/RP', '_blank')
	}
	function day() {
		var days = $('#codeday').val();
		$('#day').text(days);

		var japans = $('#codej').val();
		if (japans=="J") {
			$('#japan').text(japans);
		}

		var models = $('#codemodel').val();
		$('#model').text(models);		

		var panjang = (models.length - 11)*2;
		var	ukuran = 20;

	}
	function jan() {
		var jan = $('#codejan').val();
		var url1 = "{{url('/app/barcode/')}}";
		var url2 ="/barcode.php?f=svg&s=ean-13&th=20&ts=20&w=300&h=80&pv=20&ph=0&wm=2&wq=2";
		var code ="&d="+jan;
		var janfix = url1.replace("/public","");
		if (jan !="-") {
			$("#JAN").attr("src",janfix+url2+code);
		}else{
			$("#JAN").attr("src",janfix+url2+code);
			$("#JAN").css({"visibility":"hidden"});
		}
	}

	function upc() {
		var upc = $('#codeupc').val(); 
		var url1 = "{{url('/app/barcode/')}}";
		var url2 ="/barcode.php?f=svg&s=upc-a&th=20&ts=20&w=300&h=80&pv=20&ph=0&wm=2&wq=2";
		var code ="&d="+upc;
		var janfix = url1.replace("/public","");
		if (upc !="-") {
			$("#upc").attr("src",janfix+url2+code);
		}else{
			$("#upc").attr("src",janfix+url2+code);
			$("#upc").css({"visibility":"hidden"});
		}
	}

	function gmc() {
		var gmc = $('#codegmc').val();
		var url1 = "{{url('/app/barcode/')}}";
		var url2 ="/barcode.php?f=svg&s=code-128b&th=23&ts=20&w=280&h=100&pv=20&ph=0";
		var code ="&d="+gmc;
		var janfix = url1.replace("/public","");
		$("#gmc").attr("src",janfix+url2+code);
	}

	function jumlah() {
		var upc = $('#codeupc').val(); 
		var jan = $('#codejan').val();
		var gmc = $('#codegmc').val();


		var url1 = "{{url('/app/barcode/')}}";
		var url2upc ="/barcode.php?f=svg&s=upc-a&th=20&ts=20&w=300&h=80&pv=0&ph=0&tf='Bahnschrift'&wm=2&wq=2";
		var url2jan ="/barcode.php?f=svg&s=ean-13&th=20&ts=20&w=300&h=80&pv=0&ph=0&tf='Bahnschrift'&wm=2&wq=2";
		var url2gmc ="/barcode.php?f=svg&s=code-128b&th=23&ts=20&w=280&h=100&pv=0&ph=2";
		var codeupc ="&d="+upc;
		var codejan ="&d="+jan;
		var codegmc ="&d="+gmc;
		var janfix = url1.replace("/public","");

		if (upc =="-" ) {
			$("#upcT").css({"display":"none"});
			
		}
		if (jan =="-") {
			$("#janT").css({"display":"none"});
		}
		if (upc !="-" && jan =="-") {
			$("#upc_jan2").css({"visibility":"hidden"});
			$("#upc").css({"visibility":"hidden"});
			$("#JAN").css({"visibility":"hidden"});
			$("#gmc").css({"visibility":"hidden"});			
			$("#upcT").css({"display":"none"});
			$("#gmcT").css({"display":"none"});
			$("#upc_gmcT").css({"display":"block"});
			$("#upc_janT").css({"display":"block"});
			$("#upc_janT2").css({"display":"none"});
			$("#upc_jan").attr("src",janfix+url2upc+codeupc);
			$("#upc_jangmc").attr("src",janfix+url2gmc+codegmc);
		}
		if (jan !="-" && upc =="-") {
			$("#upc_jan").css({"visibility":"hidden"});
			$("#upc").css({"visibility":"hidden"});
			$("#JAN").css({"visibility":"hidden"});
			$("#gmc").css({"visibility":"hidden"});		
			$("#janT").css({"display":"none"});
			$("#gmcT").css({"display":"none"});
			$("#upc_gmcT").css({"display":"block"});
			$("#upc_janT").css({"display":"none"});
			$("#upc_janT2").css({"display":"block"});
			$("#upc_jan2").attr("src",janfix+url2jan+codejan);
			$("#upc_jangmc").attr("src",janfix+url2gmc+codegmc);
		}

		if (jan !="-" && upc !="-") {
			$("#upc_jangmcT").css({"display":"none"});
			$("#upc_janT").css({"display":"none"});
			$("#upc_janT2").css({"display":"none"});
			$("#upc_jan2").css({"visibility":"hidden"});
			$("#upc_jan").css({"visibility":"hidden"});
			$("#upc_jangmc").css({"visibility":"hidden"});		
		}

		if (jan =="-" && upc =="-") {
			$("#upc_jan2").css({"visibility":"hidden"});
			$("#upc_jan").css({"visibility":"hidden"});
			$("#upc_gmcT").css({"display":"none"});
			$("#gmcT").css({"display":"none"});
			$("#gmc").css({"visibility":"hidden"});
			$("#upc_janT").css({"display":"none"});
			$("#upc_janT2").css({"display":"none"});
			$("#upc_jangmc").attr("src",janfix+url2gmc+codegmc);
		}
	}


// default print settings
var printSettings = {
	"printSilent": true,
	"shrinkToFit": true,
	"unwriteableMarginLeft": 0,
	"unwriteableMarginRight": 0,
	"unwriteableMarginTop": 0,
	"unwriteableMarginBottom": 0,
	"edgeLeft": 0,
	"edgeRight": 0,
	"edgeTop": 0,
	"edgeBottom": 0,
	"marginLeft": -10,
	"marginRight": 0,
	"marginTop": -3,
	"marginBottom": 0,
	// "scaling": 1,
	"title": "",
	"docURL": "",
	"headerStrLeft": "",
	"headerStrCenter": "",
	"headerStrRight": "",
	"footerStrLeft": "",
	"footerStrCenter": "",
	"footerStrRight": "",
	"printerName" : "SATO CG408TT" 
};


function defineCustomPaperSize() {
	console.log("Define custom paper size", false);
	jsPrintSetup.definePaperSize(101, 101, 'Custom Size 1', 'Custom Size 1', 'My Test Custom Size 1', 76.0, 34.5, jsPrintSetup.kPaperSizeInches);
  // w, h
  console.log(JSON.stringify(jsPrintSetup.getPaperSizeDataByID(101), null, "\t"), true);
}

function printWindow(win, what) {
  // jsPrintSetup messages  
  function jspListener(event) {
  	console.log('event.data:'+JSON.stringify(event.data));
  	if (event.source == win 
  		&& event.data.source && event.data.source == "jsPrintSetup"
  		) {
  		if (event.data.message == "job_start") {  
  			console.log(what+" Job "+event.data.jobId+" started");
  			console.log(what+" Job "+event.data.jobId+" started", true);
  		} else if (event.data.message == "job_progress") {
  			console.log(what+" Job "+event.data.jobId+" progress:"+event.data.progress);
  			console.log(what+" Job "+event.data.jobId+" progress:"+event.data.progress, true);
  		} else if (event.data.message == "job_error") {
  			console.log(what+" Job "+event.data.jobId+" error:"+event.data.statusMessage);
  			console.log(what+" Job "+event.data.jobId+" error:"+event.data.statusMessage, true);
  		} else if (event.data.message == "job_rejected") {
  			console.log(what+" Job "+event.data.jobId+" rejected.");
  			console.log(what+" Job "+event.data.jobId+" rejected.", true);
  		} else if (event.data.message == "job_submited") {
  			console.log(what+" Job "+event.data.jobId+" submited.");
  			console.log(what+" Job "+event.data.jobId+" submited.", true);
  		} else if (event.data.message == "job_complete") {
  			console.log(what+" Job "+event.data.jobId+" completed.");
  			console.log(what+" Job "+event.data.jobId+" completed.", true);
  		} else if (event.data.message == "jsp_permission") {
  			console.log(what+" jsPrintSetup accessEnabled:"+event.data.accessEnabled+" permission:"+event.data.permission);
  			console.log(what+" jsPrintSetup accessEnabled:"+event.data.accessEnabled+" permission:"+event.data.permission, true);
  		} else {
  			console.log(what+" Unknown message:"+event.data.message);
  			console.log(what+" Unknown message:"+event.data.message, true);
  		}
  	}
  }
  if (typeof(win.privListenersAdded) === "undefined") {
  	win.privListenersAdded = true;    
  	win.addEventListener("message", jspListener);

  	win.addEventListener("beforeprint", function(event) {
  		jan();
  		upc();
  		gmc();
  		day();
  		jumlah();
  		console.log("before print: "+what, true);
  	});
  	win.addEventListener("afterprint", function(event) {
  		console.log("after print: "+what, true);
  		var sn = $('#codesn').val();
  		var rem = $('#rem').val();

  		console.log(rem);

  		if (rem == "P") {
  			window.open('{{ url("index/fl_label_kecil") }}'+'/'+sn+'/P', '_blank');
  		}

  		window.close();

  	});
  }
  
  win.jsPrintSetup.print(printSettings).then(
  	(jobId) => {
  		console.log(what+" Print job for submitted with id:"+jobId);
  		console.log(what+" Print job for submitted with id:"+jobId, true);
  		checkJobInfo(what, win,jobId);

  		setTimeout(() => {checkJobInfo(what, win, jobId);}, 5000);
  	}
  	, (err) => {
  		console.log(what+" Pint job rejected:"+err);
  		console.log(what+" Pint job rejected:"+err, true);
  	}
  	);
} 

function checkJobInfo(what, win, jobId) {
	var jobInfo = win.jsPrintSetup.getJobInfo(jobId);
	console.log(what+ " Async Checking Ifo for Job:"+jobId, true);
	if (jobInfo) {
  //  jobInfo_1 = JSON.parse(jobInfo);
  console.log("----- job info:"+JSON.stringify(jobInfo));
  console.log(JSON.stringify(jobInfo, null, "\t"), true);
} else {
	console.log("----- Can't find jobInfo for jobId:"+jobId);
	console.log("Can't find jobInfo for jobId:"+jobId, true);
}
}


</script>