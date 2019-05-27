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
			-moz-transform:scale(1,1.4);
		}


	</style>

@php
include(app_path() . '\barcode\barcode.php');
@endphp

<br><br><br><br>
<br><br><br><br>
<table border="0" style="margin-left: 22px">	
	@foreach($barcode as $nomor => $barcode)	
	<input type="text" name="codemodel" id="codemodel" value="{{$barcode->model}}" hidden="">
	@endforeach	
	<tr>
		<td colspan="3" class="name" align="center" id="model" style="font-size: 75pt;">YAS-280//ID </td>
	</tr>	
</table>
</body>
</html>
<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<script>
	jQuery(document).ready(function() {		
		day();		
		printWindow(window, 'Label Besar');
				
	});
	function day() {
		var models = $('#codemodel').val();
		$('#model').text(models);		
		var panjang = (models.length - 11)*2;
		var	ukuran = 38;
		if (models.length > 11) {
			$('#model').css({"font-size":ukuran-panjang+"pt"});
		}		
	}
	


// default print settings
var printSettings = {
	"printSilent": true

	, "printerName" : "Microsoft XPS Document Writer" 
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
  		console.log("after print: "+what, true);
  		
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