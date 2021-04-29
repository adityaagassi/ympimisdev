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
			border: 2px solid black;
			padding: 0px;
		}

		p {
			padding: 0px;
			margin: 0px;
		}

		.gmc {
			font-size: 12pt;
		}

		.desc {
			font-size: 10pt;
		}

		.qr {
			font-size: 8pt;
		}

		#kd_number {
			font-size: 11pt;
			font-weight: normal;
			padding-bottom: 3pt;			
		}

		#barcode {
			padding-top: 5pt;
		}

	</style>
	<?php 
	include public_path(). "/qr_generator/qrlib.php"; 

	QRcode::png(Request::segment(3), public_path().'/mis-qr.png');
	?>

	<table style="margin-top: 1px; width: 350px;">
		<tr height="">
			<th rowspan="3" style=" width: 1%; white-space: nowrap;">
				<img src="{{ url("mis-qr.png")}}" style="width: 50px; height: 50px;"> <br>
				<p id="ids">{{ Request::segment(3) }}</p>
			</th>
			<th width="100px" class="desc" id="device_category"></th>
		</tr>
		<tr>
			<th style="padding-top: 0px; padding-bottom: 0px;" width="100px" class="desc" id="location"></th>
		</tr>
		<tr>
			<th style="padding-top: 0px; padding-bottom: 0px;" width="100px" class="desc" id="dept">MIS - Dept</th>
		</tr>
	</table>



</body>
</html>
<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<script>
	jQuery(document).ready(function() {		
		initialize();
		defineCustomPaperSize();

		// setTimeout(function() {
		// 	printWindow(window, 'Label Kecil');
		// }, 3000)

	});

	function initialize() {
		// var kd_number = $('#input_kd_number').val();
		// var material_number = $('#input_material_number').val();
		// var material_description = $('#input_material_description').val();
		// var quantity = $('#input_quantity').val();
		// var kd_name = $('#input_kd_name').val();
		// var xy = $('#input_xy').val();
		// var mj = $('#input_mj').val();

		// $('#material_number').text(material_number);
		// $('#material_description').text(material_description);
		// $('#quantity').text(quantity +" PC(s)");
		// $('#kd_name').text(kd_name);
		// $('#xy').text(xy);
		// $('#mj').text(mj);
		// $('#kd_number').text(kd_number);

		// var url1 = "{{url('/app/barcode/')}}";
		// var url2 ="/barcode.php?f=svg&s=code-128&w=225&h=52&p=0&wq=0";
		// var code ="&d="+ kd_number;
		// var janfix = url1.replace("/public","");
		// $('#barcode').attr("src", janfix + url2 + code);

		var data = {
			id : '{{ Request::segment(3) }}'
		}

		$.get('{{ url("fetch/inventory_mis") }}', data, function(result) {
			$("#device_category").text(result.inventory.device);
			$("#location").text(result.inventory.location);
		})

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
		"marginLeft": 0,
		"marginRight": 0,
		"marginTop": 0,
		"marginBottom": 0,
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
				console.log("after print: "+what, true);

				// setTimeout(tutup,1000);
				
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