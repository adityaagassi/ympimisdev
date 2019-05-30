<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body >
	<style type="text/css">
		table {
			border-collapse: collapse;
		}
		.name {
			
			font-family: 'arial';
			-moz-transform:scale(1.3,1);


		}
		.product {
			font-size: 12pt;
			font-family: 'arial';
		}

		.kiri {
			font-size: 11pt;
			font-family: 'arial narrow';
			font-weight: bold;
		}
		.bawah {
			font-size: 12pt;
			font-family: 'arial';
			font-weight: bold;
		}

	</style>
<!-- <div style="width: 30%">
	Product Name :<br>
	Yas-280//ID
	JAN/EAN 

</div> -->

@php
include(app_path() . '\barcode\barcode.php');
@endphp



<table border="0" style="margin-left: 22px">
	
	<input type="text" name="rem" id="rem" value="{{$remark}}" hidden="">
	@foreach($barcode as $nomor => $barcode)
	<input type="text" name="codesn" id="codesn" value="{{$barcode->serial_number}}" hidden="">
	<input type="text" name="codemodel" id="codemodel" value="{{$barcode->model}}" hidden="">
	<input type="text" name="codegmc" id="codegmc" value="{{$barcode->finished}}" hidden="">
	<input type="text" name="codejan" id="codejan" value="{{$barcode->janean}}" hidden="">
	<input type="text" name="codeupc" id="codeupc" value="{{$barcode->upc}}" hidden="">
	<input type="text" name="codeday" id="codeday" value="{{$barcode->date_code}}" hidden="">
	<input type="text" name="codej" id="codej" value="{{$barcode->remark}}" hidden="">
	@endforeach
	<tr>
		<td colspan="3"><p class="product">Product Name :</p></td>
	</tr>
	<tr>
		<td colspan="3" class="name" align="center" id="model" style="font-size: 38pt;">YAS-280//ID </td>
	</tr>
	<tr>
		<td colspan="3" >&nbsp;<br></td>
	</tr>	
	<tr>
		<td class="kiri" align="RIGHT">JAN/EAN</td>
		<td align="left"> <img id="JAN" src=""> </td>
		<td ></td>
	</tr>
	<tr>
		<td class="kiri" align="RIGHT">UPC</td>
		<td align="left"><img id ="upc" src=""></td>
		<td ></td>
	</tr>
	<tr>
		<td class="kiri" align="RIGHT">GMC</td>
		<td align="left"><img id="gmc" src=""></td>
		<td ></td>
	</tr>
	<tr>
		<td></td>
		<td colspan="2"><BR><BR><BR><BR><BR>
			@php
			$p = 'images/QC_BAR.jpg';
			@endphp
			<table width="100%" border="0">
				<tr>
					<td align="RIGHT" class="bawah" width="20%"></td>
					<td align="left" class="bawah" width="50%"><a id="day"></a></td>
					<td align="RIGHT" class="bawah" width="10%"><a id="japan"></a></td>
					<td align="RIGHT" class="bawah"><img src="{{ url($p) }}" width="120px"></td>
				</tr>
			</table>
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
		// window.print();
		//var sn = $('#codesn').val();
  		//var rem = $('#rem').val();
  		//if (rem =="JR" || rem =="NJR") {
  		//	window.open('{{ url("index/label_kecil") }}'+'/'+sn+'/RP', '_blank');
  		//}else if(rem =="J" || rem =="NJ"){
  		//	window.open('{{ url("index/label_kecil") }}'+'/'+sn+'/P', '_blank');
  		//}else{
  			// alert("asas")
  		//}
  		
		//window.close();

		 defineCustomPaperSize();
		 printWindow(window, 'Label Besar');

		
	});
	function day() {
		var days = $('#codeday').val();
		$('#day').text(days);

		var japans = $('#codej').val();
		$('#japan').text(japans);

		var models = $('#codemodel').val();
		$('#model').text(models);		

		var panjang = (models.length - 11)*2;
		var	ukuran = 38;
		if (models.length > 11) {
			$('#model').css({"font-size":ukuran-panjang+"pt"});
		}
		// alert(panjang);
	}
	function jan() {
		var jan = $('#codejan').val();
		var url1 = "{{url('/app/barcode/')}}";
		var url2 ="/barcode.php?f=svg&s=ean-13&th=15&ts=12&w=300&h=105&pv=20&ph=0";
		var code ="&d="+jan;
		var janfix = url1.replace("/public","");
		if (jan !="-") {
			$("#JAN").attr("src",janfix+url2+code);
		}else{
			$("#JAN").attr("src","");
			$("#JAN").css({"visibility":"hidden"});
		}
	}

	function upc() {
		var upc = $('#codeupc').val(); 
		var url1 = "{{url('/app/barcode/')}}";
		var url2 ="/barcode.php?f=svg&s=upc-a&th=15&ts=12&w=300&h=105&pv=20&ph=0";
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
		var url2 ="/barcode.php?f=svg&s=code-128b&th=15&ts=12&w=300&h=105&pv=20&ph=2";
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
	"marginLeft": 2,
	"marginRight": 0,
	"marginTop": 0,
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
	 "printerName" : "SATO LM408e" 
};


function defineCustomPaperSize() {
  console.log("Define custom paper size", false);
  jsPrintSetup.definePaperSize(101, 101, 'Custom Size 1', 'Custom Size 1', 'My Test Custom Size 1', 101.6, 152.4, jsPrintSetup.kPaperSizeInches);
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
  		console.log("before print: "+what, true);
  	});
  	win.addEventListener("afterprint", function(event) {
  		console.log("after print: "+what, true);
  		var sn = $('#codesn').val();
  		var rem = $('#rem').val();
  		if (rem =="JR" || rem =="NJR") {
  			window.open('{{ url("index/label_kecil") }}'+'/'+sn+'/RP', '_blank');
  		}else if(rem =="J" || rem =="NJ"){
  			window.open('{{ url("index/label_kecil") }}'+'/'+sn+'/P', '_blank');
  		}else{
  			// alert("asas")
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