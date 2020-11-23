<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body >
	<style type="text/css">
		table {
			font-family: 'calibri';
			border-collapse: collapse;
			padding: 0px;
		}

		table, th, td {
			border: 0px solid;
			padding: 1.5px;
		}

		p {
			padding: 0px;
			margin: 0px;
		}

		.top {
			font-size: 8pt;
			text-align: center;
		}

		.bottom {
			font-size: 7pt;
			text-align: center;
			-moz-transform:scale(1.2,1);
		}

		.left {
			font-size: 8pt;
			text-align: left;
		}

		.text {
			font-size: 8pt;
			text-align: left;
		}

		.desc {
			font-size: 7pt;
			text-align: left;
		}

		#barcode {
			padding-top: 1pt;
		}

	</style>

	@php
	include(app_path() . '\barcode\barcode.php');
	@endphp
	
	<input type="hidden" id="param_kode_item" value="{{ $kode_item }}">
	<input type="hidden" id="param_description" value="{{ $description }}">
	<input type="hidden" id="param_po" value="{{ $po }}">
	<input type="hidden" id="param_date" value="{{ date('d-M-Y', strtotime($date)) }}">
	<input type="hidden" id="param_qty" value="{{ $quantity }}">

	<table style="width: 220px;">
		<tr height="15px">
			<th width="220px" class="top" colspan="6">Yamaha Musical Products Indonesia (WH)</th>
		</tr>
		<tr height="10px">
			<th width="50" class="left">Desc </th>
			<th class="text">:</th>
			<th class="desc" id="desc" colspan="4"></th>
		</tr>
		<tr height="10px">
			<th class="left">No. PO </th>
			<th class="text">:</th>
			<th class="text" id="po" colspan="4"></th>
		</tr>
		<tr height="10px">
			<th class="left">Tgl Dtg </th>
			<th class="text">:</th>
			<th class="text" id="date"></th>
			<th class="text">Qty</th>
			<th class="text">:</th>
			<th class="text" id="quantity"></th>
		</tr>
		<tr height="20px">
			<th colspan="6">
				<img id="barcode" src="" style="margin-top: 0.5%;">
				<p class="bottom" id="po_code_item"></p>
			</th>
		</tr>
	</table>



</body>
</html>
<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<script>
	jQuery(document).ready(function() {		
		initialize();
		defineCustomPaperSize();

		setTimeout(function() {
			printWindow(window, 'Label Kecil');
		}, 1000)

	});

	function initialize() {
		var kode_item = $('#param_kode_item').val();
		var desc = $('#param_description').val();
		var po = $('#param_po').val();
		var date = $('#param_date').val();
		var qty = $('#param_qty').val();
		

		$('#desc').text(desc);
		$('#po').text(po);
		$('#date').text(date);
		$('#quantity').text(qty);
		$('#po_code_item').text(po+'_'+kode_item+'_'+qty);

		var url1 = "{{url('/app/barcode/')}}";
		var url2 ="/barcode.php?f=svg&s=code-128&w=200&h=30&p=0&wq=0";
		var code ="&d="+ po+'_'+kode_item+'_'+qty;
		var janfix = url1.replace("/public","");
		$('#barcode').attr("src", janfix + url2 + code);

	}

	function defineCustomPaperSize() {
		console.log("Define custom paper size", false);
		jsPrintSetup.definePaperSize(101, 101, 'Custom Size 1', 'Custom Size 1', 'My Test Custom Size 1', 33, 25, jsPrintSetup.kPaperSizeInches);
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
		"marginLeft": 2,
		"marginRight": 0,
		"marginTop": 0,
		"marginBottom": 0,
		"numCopies": 1,
		"title": "",
		"docURL": "",
		"headerStrLeft": "",
		"headerStrCenter": "",
		"headerStrRight": "",
		"footerStrLeft": "",
		"footerStrCenter": "",
		"footerStrRight": "",
		"printerName" : "SATO CG412 (Copy 1)"
	};

	function tutup() {
		window.close();
	}

	function printWindow(win, what) {
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

				defineCustomPaperSize();
				console.log("before print: "+what, true);
			});
			win.addEventListener("afterprint", function(event) {

				setTimeout(tutup,2000);

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
			console.log("----- job info:"+JSON.stringify(jobInfo));
			console.log(JSON.stringify(jobInfo, null, "\t"), true);
		} else {
			console.log("----- Can't find jobInfo for jobId:"+jobId);
			console.log("Can't find jobInfo for jobId:"+jobId, true);
		}
	} 


</script>