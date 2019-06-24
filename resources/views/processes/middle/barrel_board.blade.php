@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	th:hover {
		overflow: visible;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small>WIP Control <span class="text-purple"> 仕掛品管理</span></small>
	</h1>
	<ol class="breadcrumb">
		<li>

		</li>
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="input-group">
				<input type="text" style="text-align: center; font-size: 22px; height: 40px;" class="form-control" id="qr" placeholder="Scan QR Here...">
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
					<i class="glyphicon glyphicon-qrcode"></i>
				</div>
			</div>
		</div>
		<div class="col-xs-12" style="margin-top: 5px;">
			<input type="hidden" id="mrpc" value="{{ $mrpc }}">
			<input type="hidden" id="hpl" value="{{ $hpl }}">
			<table id="ququeTable" class="table table-bordered table-striped" width="100%">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th style="width: 1%; padding: 0px; vertical-align: middle; font-size: 20px">Key</th>
						<th style="width: 3%; padding: 0px; vertical-align: middle; font-size: 20px" id="time1">-</th>
						<th style="width: 3%; padding: 0px; vertical-align: middle; font-size: 20px" id="time2">-</th>
						<th style="width: 3%; padding: 0px; vertical-align: middle; font-size: 20px" id="time3">-</th>
						<th style="width: 3%; padding: 0px; vertical-align: middle; font-size: 20px" id="time4">-</th>
						<th style="width: 3%; padding: 0px; vertical-align: middle; font-size: 20px" id="time5">-</th>
						<th style="width: 3%; padding: 0px; vertical-align: middle; font-size: 20px" id="time6">-</th>
						<th style="width: 3%; padding: 0px; vertical-align: middle; font-size: 20px" id="time7">-</th>
						<th style="width: 3%; padding: 0px; vertical-align: middle; font-size: 20px" id="time8">-</th>
						<th style="width: 3%; padding: 0px; vertical-align: middle; font-size: 20px" id="time9">-</th>
						<th style="width: 3%; padding: 0px; vertical-align: middle; font-size: 20px" id="time10">-</th>
						<th style="width: 3%; padding: 0px; vertical-align: middle; font-size: 20px" id="time11">-</th>
						<th style="width: 3%; padding: 0px; vertical-align: middle; font-size: 20px" id="time12">-</th>
					</tr>
				</thead>
				<tbody id="tableBody" style="font-size: 18px;  font-weight: bold; padding:0;">
				</tbody>
			</table>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var keys = [];
	var month = new Array();
	month[0] = "Jan";
	month[1] = "Feb";
	month[2] = "Mar";
	month[3] = "Apr";
	month[4] = "May";
	month[5] = "Jun";
	month[6] = "Jul";
	month[7] = "Aug";
	month[8] = "Sep";
	month[9] = "Oct";
	month[10] = "Nov";
	month[11] = "Dec";

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('#qr').focus();
		$('#qr').blur();

		keys = ["C-1","C-2","C-3","C-4","C-5","D-1","D-2","D-3","D-4","D-5","E-1","E-2","E-3","E-4","E-5","E-6","E-7","E-8","F-1","F-2","F-3","F-4","G-1","G-2","H-1","H-2","H-3","H-4","H-5","J-1","J-2","J-3","J-4","J-6"];
		create_content_container();
		$('.konten').each(function () {
			keys.push($(this).text());
		});

		setInterval(fillTable, 1000);

		$('#qr').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#qr").val().length > 2){
					scanQr($("#qr").val());
					return false;
				}
				else{
					openErrorGritter('Error!', 'QR code invalid.');
					audio_error.play();
					$("#qr").val("");
				}
			}
		});
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function scanQr(qr){
		data = {
			qr:qr
		}
		$.post('{{ url("scan/middle/barrel") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					$("#qr").val("");
					$("#qr").focus();
				}
				else{
					audio_error.play();
					openErrorGritter('Error!', result.message);
					$("#qr").val("");
					$("#qr").focus();
				}
			}
			else{
				audio_error.play();
				alert('Disconnected from server');
				$("#qr").val("");
				$("#qr").focus();
			}
		});
	}

	function create_content_container() {
		var isi;
		for (var i = 0; i < keys.length; i++) {
			isi +="<tr><td style='vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;' class='konten' id='"+keys[i]+"'>"+keys[i]+"</td>";
			for (var k = 1; k <= 12; k++) {
				isi+="<td id='"+keys[i]+"_"+k+"'>-</td>";
			}
			isi +="</tr>";
		}
		$("#tableBody").append(isi);
	}

	function fillTable(){
		var data = {
			mrpc : $('#mrpc').val(),
			hpl : $('#hpl').val()
		}

		var final;

		$.get('{{ url("fetch/middle/barrel_board") }}', data, function(result, status, xhr){
			// console.log(result.barrel_queues);
			for (var i = 0; i < keys.length; i++) {
				// final += "<tr><td style='vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;' class='konten' id='"+keys[i]+"'>"+keys[i]+"</td>";
				var s = 1;
				var isi  = result.barrel_queues;
				for (var z = 0; z < 12; z++) {
					var num = 0;
					for (var y = 0; y < isi.length; y++) {
						if(keys[i] == isi[y].key)
						{
							if (s <= 12) {
								isi[y].key = "done";
								num = 1;
								final = isi[y].model+
								" <input type='hidden' id='T"+keys[i]+"_"+s+"' class='heads' value='"+isi[y].created_at+"'>";

								$("#"+keys[i]+"_"+s).html(final);
								// console.log("#"+keys[i]+"_"+s+" = "+final);
								s++;
							}
						}
					}
					if(num == 0)
					{
						if (s <= 12) {
							// final = "<td>-</td>";
							// $("#"+keys[i]+"_"+s).html(final);
							// console.log("#"+keys[i]+"_"+s+" "+final);
							s++;
						}
					}
					// console.log(head);
				}
			}
			// $("#tableBody").append(final);

			var len = $('.heads').length;

			var idsw = $('.heads')[0].id;
			var arr = [];
			// console.log(new Date("2019-06-21 10:48:02"));

			for (var j = 0; j < 12; j++) {
				var tmp = [];
				var dt = Date();
				for (var m = 0; m < len; m++) {
					var ids = $('.heads')[m].id;
					var val = $('.heads')[m].value;
					if (ids.substr(5,2) -1 == j) {
						if (new Date(val) < new Date(dt)) {
							var date_tmp = new Date(val);
							var fullDate = `${date_tmp.getDate()}`.padStart(2, '0')+" "
							+month[date_tmp.getMonth()]+" "
							+`${date_tmp.getHours()}`.padStart(2, '0')+":"
							+date_tmp.getMinutes()+":"
							+`${date_tmp.getSeconds()}`.padStart(2, '0');
							tmp = [ids,fullDate];
							dt = val;
						}
					}
					// console.log(ids);
				}
				arr.push(tmp);
				$('#time'+(j+1)).text(tmp[1]);
			}
			$('#qr').focus();
		});
	}

	$('#qr').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#qr").val().length >= 7){
				scanQrCode($('#qr').val());
				return false;
			}
			else{
				openErrorGritter('Error!', 'QR Code Invalid.');
				audio_error.play();
				$("#qr").val("");
				$('#qr').focus();
			}
		}
	});

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '3000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '3000'
		});
	}

	$.date = function(dateObject) {
		var d = new Date(dateObject);
		var day = d.getDate();
		var month = d.getMonth() + 1;
		var year = d.getFullYear();
		if (day < 10) {
			day = "0" + day;
		}
		if (month < 10) {
			month = "0" + month;
		}
		var date = day + "/" + month + "/" + year;

		return date;
	};
</script>
@endsection