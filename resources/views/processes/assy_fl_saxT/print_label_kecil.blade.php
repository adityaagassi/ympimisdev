<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body >
	<style type="text/css">
		.product {
			font-size: 9pt;
			font-family: 'arial';
			margin: 0px;
			position: fixed;
			top: 1px;
			left: 10px;
			right: 0px;
			height: 50px;

		}


		.crop {
			
			position: fixed;
			top: 15px;
			

		}

		.kiri {
			font-size: 9.0pt;
			font-family: 'arial';
			
			position: fixed;
			top: 55px;
			left: 10px;
			right: 0px;
			height: 50px;
			margin: 0px;
		}
		.bawah {
			font-size: 9.3pt;
			font-family: 'arial';
			margin: 0px;
			position: fixed;
			top: 73px;
			left: 92px;
			right: 0px;
			height: 50px;
		}

		td{
			padding: 0px;
			margin: 0px;
			vertical-align: text-bottom;
		}

		

	</style>


	@php
	include(app_path() . '\barcode\barcode.php');
	@endphp


	<table border="0" style="margin: 0px">
		<!-- <input type="text" name="rem" id="rem" value="{{$remark}}" >		 -->
		<input type="text" name="codemodel" id="codemodel" value="{{$sn}}" hidden="">
		
		@foreach($barcode as $nomor => $barcode)
		<input type="text" name="codeday" id="codeday" value="{{$barcode->date_code}}" hidden="">
		@endforeach
		<tr>		
			<td align="left" colspan="2">
				<p class="product">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SAX</p>
				<img class="crop" id="128" src="">
				<p class="kiri">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SER.&nbsp;&nbsp;(21)<a id="serial">N85550</a></p>
			</td>

		</tr>	
		<tr>
			<td width="57%"></td>
			<td width="48%"><p class="bawah" id="day"></p></td>
		</tr> 

	</table>

</body>
</html>
<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<script>
	jQuery(document).ready(function() {		
		jan();
		 // window.print();

		 defineCustomPaperSize();
		 printWindow(window, 'Label Kecil');
		 // window.close();
		});

	function jan() {
		var jan = $('#codemodel').val();
		var sn = jan.substr(2, 6);
		var url1 = "{{url('/app/barcode/')}}";
		var url2 ="/barcode.php?f=svg&s=code-128&w=150&h=40&p=0&wq=0";
		var code ="&d="+jan;
		var janfix = url1.replace("/public","");
		$("#128").attr("src",janfix+url2+code);
		var day	=$('#codeday').val();
		$('#day').text(day);
		$('#serial').text(sn.toUpperCase());
	}

		// default print settings

		function defineCustomPaperSize() {
			console.log("Define custom paper size", false);
			jsPrintSetup.definePaperSize(101, 101, 'Custom Size 1', 'Custom Size 1', 'My Test Custom Size 1', 52.8, 24.8, jsPrintSetup.kPaperSizeInches);
  // w, h
  console.log(JSON.stringify(jsPrintSetup.getPaperSizeDataByID(101), null, "\t"), true);
}
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
	"marginLeft": 5,
	"marginRight": 0,
	"marginTop": 0,
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
	"printerName" : "SATO CX400" 
};

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
  		var sn = $('#codemodel').val();
  		var rem = $('#rem').val();
  		console.log("after print: "+what, true);
  		// if(rem =="J" || rem =="NJ"){
  			window.open('{{ url("index/label_des") }}'+'/'+sn,'_blank');
  		// }
  		
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


// window.print();
</script>