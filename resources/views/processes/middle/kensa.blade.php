@extends('layouts.display')
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
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding: 0px;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
		vertical-align: middle;
		background-color: rgb(126,86,134);
		color: #FFD700;
	}
	thead {
		background-color: rgb(126,86,134);
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	#loading, #error { display: none; }

</style>
@stop
@section('header')
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0;">
	<input type="hidden" id="loc" value="{{ $loc }}">
	<div class="row" style="margin-left: 1%; margin-right: 1%;">
		<div class="col-xs-6" style="padding-right: 0; padding-left: 0">
			<div class="input-group">
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
					<i class="glyphicon glyphicon-qrcode"></i>
				</div>
				<input type="text" style="text-align: center; border-color: black;" class="form-control" id="tag" name="tag" placeholder="Scan ID Slip..." required>
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
					<i class="glyphicon glyphicon-qrcode"></i>
				</div>
			</div>
			<div style="padding-top: 5px;">
				<table style="width: 100%;" border="1">
					<tbody>
						<tr>
							<td style="width: 1%; font-weight: bold; font-size: 25px; background-color: rgb(220,220,220);">Model</td>
							<td id="model" style="width: 4%; font-size: 25px; font-weight: bold; background-color: rgb(100,100,100); color: yellow;"></td>
							<td style="width: 1%; font-weight: bold; font-size: 25px; background-color: rgb(220,220,220);">Key</td>
							<td id="key" style="width: 4%; font-weight: bold; font-size: 25px; background-color: rgb(100,100,100); color: yellow;"></td>
							<input type="hidden" id="material_tag">
							<input type="hidden" id="material_number">
							<input type="hidden" id="material_quantity">
							<input type="hidden" id="employee_id">
						</tr>
					</tbody>
				</table>
			</div>
			<div style="padding-top: 5px;">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
					<thead>
						<tr>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;" colspan="2">Operator</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:2vw; width: 30%;" id="op">-</td>
							<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 2vw;" id="op2">-</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div style="padding-top: 30px;">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
					<thead>
						<tr>
							<th colspan="3" style="background-color: #ffff66; text-align: center; color: black; font-weight: bold; font-size:2vw;">ALTO</th>
						</tr>
						<tr>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Result</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Not Good</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Rate</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 2vw;" id="ASresult">0</td>
							<td style="background-color: rgb(255,204,255); text-align: center; color: #000000; font-size: 2vw;" id="ASnotGood">0</td>
							<td style="background-color: rgb(255,255,102); text-align: center; color: #000000; font-size: 2vw;" id="ASngRate">0%</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div style="padding-top: 5px;">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
					<thead>
						<tr>
							<th colspan="3" style="background-color: rgb(157, 255, 105); text-align: center; color: black; font-weight: bold; font-size:2vw;">TENOR</th>
						</tr>
						<tr>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Result</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Not Good</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Rate</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 2vw;" id="TSresult">0</td>
							<td style="background-color: rgb(255,204,255); text-align: center; color: #000000; font-size: 2vw;" id="TSnotGood">0</td>
							<td style="background-color: rgb(255,255,102); text-align: center; color: #000000; font-size: 2vw;" id="TSngRate">0%</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="col-xs-6" style="padding-right: 0;">
			<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
				<thead>
					<tr>
						<th style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >#</th>
						<th style="width: 65%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >NG Name</th>
						<th style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >#</th>
						<th style="width: 15%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Count</th>
					</tr>
				</thead>
				<tbody>
					<?php $no = 1; ?>
					@foreach($ng_lists as $nomor => $ng_list)
					<?php if ($no % 2 === 0 ) {
						$color = 'style="background-color: #fffcb7"';
					} else {
						$color = 'style="background-color: #ffd8b7"';
					}
					?>
					<input type="hidden" id="loop" value="{{$loop->count}}">
					<tr <?php echo $color ?>>
						<td id="minus" onclick="minus({{$nomor+1}})" style="background-color: rgb(255,204,255); font-weight: bold; font-size: 45px; cursor: pointer;" class="unselectable">-</td>
						<td id="ng{{$nomor+1}}" style="font-size: 20px;">{{ $ng_list->ng_name }} </td>
						<td id="plus" onclick="plus({{$nomor+1}})" style="background-color: rgb(204,255,255); font-weight: bold; font-size: 45px; cursor: pointer;" class="unselectable">+</td>
						<td style="font-weight: bold; font-size: 45px; background-color: rgb(100,100,100); color: yellow;"><span id="count{{$nomor+1}}">0</span></td>
					</tr>
					<?php $no+=1; ?>
					@endforeach
				</tbody>
			</table>
			<div>
				<center>
					<button style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: yellow; width: 30%" onclick="canc()" class="btn btn-danger">CANCEL</button>
					<button id="conf1" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: yellow; width: 69%" onclick="conf()" class="btn btn-success">CONFIRM</button>
				</center>
			</div>
		</div>
	</div>
	
</section>

<div class="modal fade" id="modalOperator">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<label for="exampleInputEmail1">Employee ID</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID Card" required>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('#operator').val('');
		$('#tag').val('');
	});

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#operator').focus();
	});

	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator").val().length >= 8){
				var data = {
					employee_id : $("#operator").val()
				}
				$.get('{{ url("scan/middle/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#modalOperator').modal('hide');
						$('#op').html(result.employee.employee_id);
						$('#op2').html(result.employee.name);
						$('#employee_id').val(result.employee.employee_id);
						fillResult();
						$('#tag').focus();
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator").val("");
			}			
		}
	});

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag").val().length >= 11){
				scanTag($("#tag").val());
			}
			else{
				openErrorGritter('Error!', 'ID Slip Invalid');
				audio_error.play();
				$("#tag").val("");
			}			
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function fillResult(){
		var data = {
			location: $('#loc').val(),
			employee_id : $("#operator").val(),
		}
		$.get('{{ url("fetch/middle/kensa") }}', data, function(result, status, xhr){
			var asQty = 0, tsQty = 0;

			$.each(result.result, function(index, value) {
				if (value.hpl == 'ASKEY') {
					$('#ASresult').text(value.qty);
					asQty = value.qty;
				} else if (value.hpl == 'TSKEY'){
					$('#TSresult').text(value.qty);
					tsQty = value.qty;
				}
			})

			$.each(result.ng, function(index, value) {
				if (value.hpl == 'ASKEY') {
					$('#ASnotGood').text(value.qty);
					$('#ASngRate').text(Math.round((value.qty/asQty)*100, 2)+'%');
				} else if (value.hpl == 'TSKEY'){
					$('#TSnotGood').text(value.qty);
					$('#TSngRate').text(Math.round((value.qty/tsQty)*100, 2)+'%');
				}

			})
		});
	}

	function disabledButton() {
		if($('#tag').val() != ""){
			var btn = document.getElementById('conf1');
			btn.disabled = true;
			btn.innerText = 'Posting...'
        // alert('aaaa');			
        return false;
    }
}


function conf(){
	if($('#tag').val() == ""){
		openErrorGritter('Error!', 'Tag is empty');
		audio_error.play();
		$("#tag").val("");
		$("#tag").focus();

		return false;
	}



	var tag = $('#tag_material').val();
	var loop = $('#loop').val();
		// var total = 0;
		var count_ng = 0;
		var ng = [];
		var count_text = [];
		for (var i = 1; i <= loop; i++) {
			if($('#count'+i).text() > 0){
				ng.push([$('#ng'+i).text(), $('#count'+i).text()]);
				count_text.push('#count'+i);
				// total += parseInt($('#count'+i).text());
				count_ng += 1;
			}
		}

		var data = {
			loc: $('#loc').val(),
			tag: $('#material_tag').val(),
			material_number: $('#material_number').val(),
			quantity: $('#material_quantity').val(),
			employee_id: $('#employee_id').val(),
			ng: ng,
			count_text: count_text,
			// total_ng: total,
		}
		disabledButton();

		$.post('{{ url("input/middle/kensa") }}', data, function(result, status, xhr){
			
			
			if(result.status){
				var btn = document.getElementById('conf1');
				btn.disabled = false;
				btn.innerText = 'CONFIRM';
				openSuccessGritter('Success!', result.message);
				for (var i = 1; i <= loop; i++) {
					$('#count'+i).text(0);
				}
				$('#model').text("");
				$('#key').text("");
				$('#material_tag').val("");
				$('#material_number').val("");
				$('#material_quantity').val("");
				$('#tag').val("");
				$('#tag').prop('disabled', false);
				fillResult();
				$('#tag').focus();
				
				
			}
			else{
				var btn = document.getElementById('conf1');
				btn.disabled = false;
				btn.innerText = 'CONFIRM';
				audio_error.play();
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function canc(){
		var loop = $('#loop').val();
		for (var i = 1; i <= loop; i++) {
			$('#count'+i).text(0);
		};
		$('#model').text("");
		$('#key').text("");
		$('#material_tag').val("");
		$('#material_number').val("");
		$('#material_quantity').val("");
		$('#tag').val("");
		$('#tag').prop('disabled', false);
		$('#tag').focus();

	}

	function plus(id){
		var count = $('#count'+id).text();
		if($('#key').text() != ""){
			$('#count'+id).text(parseInt(count)+1);
		}
		else{
			audio_error.play();
			openErrorGritter('Error!', 'Scan material first.');
			$("#tag").val("");
			$("#tag").focus();
		}
	}

	function minus(id){
		var count = $('#count'+id).text();
		if($('#key').text() != ""){
			if(count > 0)
			{
				$('#count'+id).text(parseInt(count)-1);
			}
		}
		else{
			audio_error.play();
			openErrorGritter('Error!', 'Scan material first.');
			$("#tag").val("");
			$("#tag").focus();
			$('#tag').blur();
		}
	}

	function scanTag(tag){
		$('#tag').prop('disabled', true);
		var data = {
			tag:tag,
			loc:$('#loc').val()
		}
		$.get('{{ url("scan/middle/kensa") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
				$('#model').text(result.middle_inventory.model);
				$('#key').text(result.middle_inventory.key);
				$('#material_tag').val(result.middle_inventory.tag);
				$('#material_number').val(result.middle_inventory.material_number);
				$('#material_quantity').val(result.middle_inventory.quantity);
			}
			else{
				$('#tag').prop('disabled', false);
				openErrorGritter('Error!', result.message);
				audio_error.play();
				$("#tag").val("");
			}
		});
	}

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