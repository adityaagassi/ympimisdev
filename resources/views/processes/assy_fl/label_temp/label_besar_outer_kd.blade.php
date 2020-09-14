<!DOCTYPE html>
<html>
<head>

</head>
<body >
	<style type="text/css">
		table {
			border-collapse: collapse;
			/*border: dashed;*/
		}
		.name {			
			font-family: 'arial' !important;
			/*-moz-transform:scale(1.3,1);*/
		}
		.product {
			font-size: 12pt;
			font-family: 'arial' !important;
		}

		.kiri {
			font-size: 6pt;
			font-family: 'arial' !important;
			/*font-weight: bold;*/
		}
		.bawah {
			font-size: 12pt;
			font-family: 'arial' !important;
			font-weight: bold;
		}

	</style>

	@php
	include(app_path() . '\barcode\barcode.php');
	@endphp

	<table border="0" style="margin-left: 22px">

		<input type="text" name="rem" id="rem" value="{{$remark}}" hidden="">
		@foreach($barcode as $nomor => $barcode)
		{{-- <input type="text" name="codesn" id="codesn" value="{{$barcode->serial_number}}" hidden=""> --}}
		<input type="text" name="codemodel" id="codemodel" value="{{ str_replace(' ', '&nbsp;', $barcode->model) }}" hidden="">
		<input type="text" name="codegmc" id="codegmc" value="{{$barcode->finished}}" hidden="">
		<input type="text" name="codejan" id="codejan" value="{{$barcode->janean}}" hidden="">
		<input type="text" name="codeupc" id="codeupc" value="{{$barcode->upc}}" hidden="">
		{{-- <input type="text" name="codeday" id="codeday" value="{{$barcode->date_code}}" hidden=""> a--}}
		<input type="text" name="codej" id="codej" value="{{$barcode->remark}}" hidden="">
		@endforeach

		@foreach($date as $nomor => $date) 
		<input type="text" name="codeday" id="codeday" value="{{$date->date_code}}" hidden="">
		@endforeach

		<tr>
			<td colspan="3" class="name" align="left" style="font-size: 12pt;">Product Name:</td>
		</tr>
		<tr>
			<td colspan="3" class="name" align="left" id="model" style="font-size: 40pt;"></td>
		</tr>
		<tr>
			<td colspan="3" class="name" align="left" style="height: 10pt;"></td>
		</tr>		
		<tr>
			<td class="kiri" align="right"> 
				<p id="JAN-text" style="font-size: 12pt;" id="janT"> JAN/EAN </p>
			</td>
			<td class="kiri" align="right"> 
				<img id="JAN" src="" style="height:80px;">
			</td>
		</tr>
		<tr>
			<td colspan="3" class="name" align="left" style="height: 10pt;"></td>
		</tr>
		<tr>
			<td class="kiri" align="right"> 
				<p id="upc-text" style="font-size: 12pt;" id="janT"> UPC </p>
			</td>
			<td class="kiri" align="right"> 
				<img id="upc" src="" style="height:80px;">
			</td>
		</tr>
		<tr>
			<td colspan="3" class="name" align="left" style="height: 10pt;"></td>
		</tr>
		<tr>
			<td class="kiri" align="right"> 
				<p style="font-size: 12pt;" id="janT"> GMC </p>
			</td>
			<td class="kiri" align="right"> 
				<img id="gmc" src="" style="height:80px;">
			</td>
		</tr>
		<tr>
			<td colspan="3" class="name" align="left" style="height: 50pt;"></td>
		</tr>
		<tr>
			<td class="kiri" style="position: fixed; top:480px; left: 120px;"> 
				<p style="font-size: 12pt;" id="day"></p>
			</td>
			<td class="kiri" style="position: fixed;  top:480px; left: 240px;"> 
				<img id="gmc" src="{{ asset("/images/QC_BAR.jpg")}}" style="height:50px;">
			</td>
		</tr>
		<tr>
			<td colspan="3" class="kiri" style="position: fixed; top:20px; left: -75px;"> 
				<p id="kd_text"></p>
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
		
		defineCustomPaperSize();
		printWindow(window, 'Label Besar');

		
	});

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
		var	ukuran = 38;
		if (models.length > 11) {
			$('#model').css('font-size', (ukuran-panjang) + 'pt');
		}


		if(models.includes("FHJ-200U//02")){
			$('#model').text('FHJ-200U//02');
			$('#model').css('font-size', '36pt');
			$('#model').css('visibility', 'hidden');

			$('#kd_text').text(models);
			$('#kd_text').css({"font-size":"30px","-moz-transform":"scale(0.6,2)","padding-left":"0px"});
		}

	}

	function jan() {
		var jan = $('#codejan').val();
		var url1 = "{{url('/app/barcode/')}}";
		var url2 ="/barcode.php?f=svg&s=ean-13&th=20&ts=20&w=300&h=100&pv=20&ph=0&wm=2&wq=2";
		var code ="&d="+jan;
		var janfix = url1.replace("/public","");
		if (jan != "-") {
			$("#JAN").attr("src",janfix+url2+code);
		}else{
			$("#JAN").attr('src',janfix+url2+code);
			$("#JAN-text").css('visibility', 'hidden');
			$("#JAN").css('visibility', 'hidden');
		} 
	}

	function upc() {
		var upc = $('#codeupc').val(); 
		var url1 = "{{url('/app/barcode/')}}";
		var url2 ="/barcode.php?f=svg&s=upc-a&th=20&ts=20&w=300&h=100&pv=20&ph=0&wm=2&wq=2";
		var code ="&d="+upc;
		var janfix = url1.replace("/public","");
		if (upc != "-") {
			$("#upc").attr("src",janfix+url2+code);
		}else{
			$("#upc").attr("src",janfix+url2+code);
			$("#upc-text").css('visibility', 'hidden');
			$("#upc").css('visibility', 'hidden');
		}
	}

	function gmc() {
		var gmc = $('#codegmc').val();
		var url1 = "{{url('/app/barcode/')}}";
		var url2 ="/barcode.php?f=svg&s=code-128b&th=20&ts=18&w=240&h=80&pv=20&ph=2&wm=1&wq=1";
		var code ="&d="+gmc;
		var janfix = url1.replace("/public","");
		$("#gmc").attr("src",janfix+url2+code);
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
	"marginLeft": 0,
	"marginRight": 0,
	"marginTop": 3,
	"marginBottom": 0,
	"scaling": 1,
	"title": "",
	"docURL": "",
	"headerStrLeft": "",
	"headerStrCenter": "",
	"headerStrRight": "",
	"footerStrLeft": "",
	"footerStrCenter": "",
	"footerStrRight": "",
	"printerName" : "SATO LC408e" 
};


function defineCustomPaperSize() {
	console.log("Define custom paper size", false);
	jsPrintSetup.definePaperSize(101, 101, 'Custom Size 1', 'Custom Size 1', 'My Test Custom Size 1', 75.9, 35.0, jsPrintSetup.kPaperSizeInches);
  // w, h
  console.log(JSON.stringify(jsPrintSetup.getPaperSizeDataByID(101), null, "\t"), true);
}

function tutup() {
	window.close();
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

  		setTimeout(tutup,3000);
		// window.close();
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