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
			font-weight: bold;
			margin: 0px;
			position: fixed;
			top: 0px;
			left: 0px;
			right: 0px;
			height: 50px;
			max-width: 120px;
			text-align: left;
		}
		.crop {
			position: fixed;
			top: 16px;
			/*left: px;*/
		}
		.kiri {
			font-size: 10pt;
			font-family: 'arial';
			margin: 0px;
			position: fixed;
			top: 44px;
			left: 0px;
			right: 0px;
			height: 50px;
			max-width: 120px;
			text-align: center;
		}
		.tengah {
			font-size: 7pt;
			font-family: 'arial';
			margin: 0px;
			position: fixed;
			top: 57px;
			left: 0px;
			right: 0px;
			height: 50px;
			max-width: 120px;
			text-align: center;
		}

		.bawah {
			font-size: 7pt;
			font-family: 'arial';
			margin: 0px;
			position: fixed;
			top: 80px;
			left: 0px;
			right: 0px;
			height: 50px;
			max-width: 120px;
			text-align: center;
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


	<table border="0" style="margin: 0px;">
		<input type="text" name="material_number" id="material_number" value="{{$knock_down_detail->material_number}}" hidden="">
		<input type="text" name="material_description" id="material_description" value="{{$knock_down_detail->material_description}}" hidden="">
		<input type="text" name="quantity" id="quantity" value="{{$knock_down_detail->quantity}}" hidden="">
		
		<tr>		
			<td align="left">
				<p class="product">GMC: </p>
				<img class="crop" id="128" src="">
				<p class="kiri" id="print_material_number">ZQ66151</p>
			</td>
		</tr>	
		<tr>
			<td align="left"><p class="tengah" id="print_material_description">CL221 24 CONNECTING ARM (PACKED) PI</p></td>
		</tr>
		<tr>
			<td align="left"><p class="bawah" id="print_quantity">QTY: 100PC(s)</p></td>
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
		var material_number = $('#material_number').val();
		var material_description = $('#material_description').val();
		var quantity = $('#quantity').val();
		var url1 = "{{url('/app/barcode/')}}";
		var url2 ="/barcode.php?f=svg&s=code-128&w=100&h=27&p=0&wq=0";
		var code ="&d="+ material_number;
		var janfix = url1.replace("/public","");
		$("#128").attr("src",janfix+url2+code);

		$('#print_material_number').text(material_number);


		if(material_description.length > 35){
			$('#print_material_description').css('font-size', '6pt');
			$('#print_material_description').css('font-weight', 'bold');
		}else{
			$('#print_material_description').css('font-size', '7pt');
		}
		$('#print_material_description').text(material_description);
		


		$('#print_quantity').text("QTY: "+ quantity +"PC(s)");
	}
// default print settings

function defineCustomPaperSize() {
	console.log("Define custom paper size", false);
	jsPrintSetup.definePaperSize(101, 101, 'Custom Size 1', 'Custom Size 1', 'My Test Custom Size 1', 33, 25, jsPrintSetup.kPaperSizeInches);
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
	"marginLeft": 1,
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
	"printerName" : "SATO CG408"
};

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
		 // window.print();

		 defineCustomPaperSize();
		 console.log("before print: "+what, true);
		});
  	win.addEventListener("afterprint", function(event) {

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


// window.print();
</script>