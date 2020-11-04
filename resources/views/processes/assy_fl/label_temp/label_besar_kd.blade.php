<!DOCTYPE html>
<html>
<head>
</head>
<body >
	<style type="text/css">
		table {
			border-collapse: collapse;
		}
		.kiri {
			font-size: 25pt;
			font-family: 'arial';
			/*font-weight: bold;*/
		}
	</style>

	@php
	include(app_path() . '\barcode\barcode.php');
	@endphp

	<input type="text" name="codegmc" id="codegmc" value="{{$barcode}}" hidden="">


	<table id="tabel" border="0" style="margin-left: 0px;">
		<tr>
			<td>
				<img id="gmc" src="" style="position: fixed; top: 15px; left: 45px; width: 250px;">
			</td>
			<td  class="kiri">
				<p id="gmc_text" src="" style="position: fixed; top: 92px; left: 95px; margin: 0px;"></p>
			</td>
		</tr>
	</table>

</body>
</html>
<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<script>
	jQuery(document).ready(function() {
		
		gmc();

		defineCustomPaperSize();
		setTimeout(function() {
			printWindow(window, 'Label Besar');
		}, 2000);

		
	});

	function gmc() {
		var gmc = $('#codegmc').val();
		var url1 = "{{url('/app/barcode/')}}";
		var url2 ="/barcode.php?f=svg&s=code-128&w=340&h=110&p=0&wq=0";
		var code ="&d="+gmc;
		var janfix = url1.replace("/public","");
		$("#gmc").attr("src",janfix+url2+code);
		$("#gmc_text").text(gmc);
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
		"marginLeft": -10,
		"marginRight": 0,
		"marginTop": -3,
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
		"printerName" : "SATO CG408TT" 
	};


	function defineCustomPaperSize() {
		console.log("Define custom paper size", false);
		jsPrintSetup.definePaperSize(101, 101, 'Custom Size 1', 'Custom Size 1', 'My Test Custom Size 1', 76.0, 34.5, jsPrintSetup.kPaperSizeInches);
		console.log(JSON.stringify(jsPrintSetup.getPaperSizeDataByID(101), null, "\t"), true);
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
				gmc();
				console.log("before print: "+what, true);
			});
			win.addEventListener("afterprint", function(event) {
				console.log("after print: "+what, true);
				var sn = $('#codesn').val();
				var rem = $('#rem').val();

				console.log(rem);

				setTimeout(tutup,1000);	
				

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