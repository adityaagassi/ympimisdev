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
	#ngList {
		height:480px;
		overflow-y: scroll;
	}
	#ngTemp {
		height:200px;
		overflow-y: scroll;
	}
	#ngAll {
		height:480px;
		overflow-y: scroll;
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
	<input type="hidden" id="loc_spec" value="{{ $loc_spec }}">
	<input type="hidden" id="started_at">
	<div class="row" style="margin-left: 1%; margin-right: 1%;">
		<div class="col-xs-7" style="padding-right: 0; padding-left: 0">
			<div class="col-xs-12" style="padding-bottom: 5px;">
				<div class="row">
					<div class="col-xs-8">
						<div class="row">
							<table class="table table-bordered" style="width: 100%; margin-bottom: 0;">
								<thead>
									<tr>
										<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 16px;" colspan="2">Operator Kensa</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:16px; width: 30%;" id="op">-</td>
										<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 16px;" id="op2"></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-4">
							<div class="input-group">
								<input type="text" style="text-align: center; border-color: black;" class="form-control input-lg" id="tag" name="tag" placeholder="Scan RFID Card..." required>
								<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
									<i class="glyphicon glyphicon-credit-card"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div style="padding-top: 5px;">
				<table style="width: 100%; margin-top: 5px;" border="1">
					<tbody>
						<tr>
							<td style="width: 1%; font-weight: bold; font-size: 16px; background-color: rgb(220,220,220);">Model</td>
							<td id="model" style="width: 2%; font-size: 16px; font-weight: bold; background-color: rgb(100,100,100); color: yellow; border: 1px solid black" colspan="2"></td>
							<td style="width: 1%; font-weight: bold; font-size: 16px; background-color: rgb(220,220,220);">SN</td>
							<td id="serial_number" style="width: 2%; font-weight: bold; font-size: 16px; background-color: rgb(100,100,100); color: yellow; border: 1px solid black"></td>
							<td style="width: 1%; font-weight: bold; font-size: 16px; background-color: rgb(220,220,220);">Loc</td>
							<td id="location_now" style="width: 5%; font-weight: bold; font-size: 16px; background-color: rgb(100,100,100); color: yellow; border: 1px solid black"></td>
							<input type="hidden" id="tag2">
							<input type="hidden" id="serial_number2">
							<input type="hidden" id="model2">
							<input type="hidden" id="location_now2">
							<input type="hidden" id="employee_id">
						</tr>
					</tbody>
				</table>
			</div>
			<div style="padding-top: 5px">
				<table style="width: 100%;padding-top: 5px" border="1">
					<tbody id="details">
					</tbody>
				</table>
			</div>
			<div style="padding-top: 5px">
				<div id="ngTemp">
					<table id="ngTempTable" class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
						<thead>
							<tr>
								<th style="width: 65%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >NG Name</th>
								<th style="width: 65%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Value / Jumlah</th>
							</tr>
						</thead>
						<tbody id="ngTempBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="col-xs-5" style="padding-right: 0;">
			<div id="ngAll">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th style="width: 65%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >NG Name</th>
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
						<tr <?php echo $color ?>>
							<td onclick="showNgDetail('{{ $ng_list->ng_name }}')" style="font-size: 40px;">{{ $ng_list->ng_name }} </td>
						</tr>
						<?php $no+=1; ?>
						@endforeach
					</tbody>
				</table>
			</div>
			<div>
				<center>
					<button style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 32%" onclick="canc()" class="btn btn-danger">CANCEL</button>
					<button id="rework" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 32%" onclick="rework()" class="btn btn-warning">REWORK</button>
					<button id="conf1" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 32%" onclick="conf()" class="btn btn-success">CONFIRM</button>
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

<div class="modal fade" id="modalNg">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<h4 id="judul_ng">NG List</h4>
					<input type="hidden" id="loop" value="'+indexNg+'">
					<div id="ngList">
						<table id="ngDetail" class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
							<thead>
								<tr>
									<th style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >#</th>
									<th style="width: 65%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >NG Name</th>
									<th style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >#</th>
									<th style="width: 15%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Count</th>
								</tr>
							</thead>
							<tbody id="ngDetailBody">
							</tbody>
						</table>
						<button id="confNg" style="width: 100%; margin-top: 10px; font-size: 3vw; padding:0; font-weight: bold; border-color: black; color: white;" onclick="confNgTemp()" class="btn btn-success">CONFIRM</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
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

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			getHeader(this.value);
		}
	});

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#operator').focus();
	});

	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($('#operator').val().length > 9 ){
				var data = {
					employee_id : $("#operator").val()
				}

				$.get('{{ url("scan/assembly/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						// fetchResult(result.employee.employee_id)
						$('#modalOperator').modal('hide');
						$('#op').html(result.employee.employee_id);
						$('#op2').html(result.employee.name);
						$('#employee_id').val(result.employee.employee_id);
						$('#tag').focus();
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator').val('');
					}
				});
			}
		}
	});

	function fetchResult(employee_id){
		var location = $('#loc').val();
		var data = {
			employee_id:employee_id,
			location:location
		}
		$.get('{{ url("fetch/welding/kensa_result") }}', data, function(result, status, xhr){
			var pctgAS = (result.ngs[0].askey/result.oks[0].askey)*100
			var pctgTS = (result.ngs[0].tskey/result.oks[0].tskey)*100
			var pctgZ = (result.ngs[0].z/result.oks[0].z)*100

			$('#result1').text(result.oks[0].askey);
			$('#result2').text(result.ngs[0].askey);
			$('#result3').text(pctgAS.toFixed(2)+'%');
			$('#result4').text(result.oks[0].tskey);
			$('#result5').text(result.ngs[0].tskey);
			$('#result6').text(pctgTS.toFixed(2)+'%');
			$('#result7').text(result.oks[0].z);
			$('#result8').text(result.ngs[0].z);
			$('#result9').text(pctgZ.toFixed(2)+'%');
		});
	}

	function getHeader(tag) {
		var location = $('#loc').val();
		var data = {
			tag : tag,
			location : location
		}

		var tableData = "";

		$.get('{{ url("scan/assembly/kensa") }}', data, function(result, status, xhr){
			if(result.status){
				$("#model").text(result.details.model);
				$("#serial_number").text(result.details.serial_number);
				$("#model2").val(result.details.model);
				$("#serial_number2").val(result.details.serial_number);
				$("#tag2").val(tag);
				$("#location_now").text("{{$loc2}}");
				$("#location_now2").val("{{$loc2}}");

				tableData += '<tr>';
				$.each(result.details2, function(key, value) {
					if (key%2 == 0) {
						var color = 'style="width: 1%; font-weight: bold; font-size: 16px; background-color: #ffff66;"';
					}else{
						var color = 'style="width: 2%; font-weight: bold; font-size: 16px; background-color: #ccffff; border: 1px solid black"';
					}
					tableData += '<td '+color+'>'+ value.location +'</td>';
				});
				tableData += '</tr>';
				tableData += '<tr>';
				$.each(result.details2, function(key2, value2) {
					tableData += '<td style="width: 2%; font-weight: bold; font-size: 16px; background-color: rgb(100,100,100); color: yellow; border: 1px solid black">'+ value2.name +'</td>';
				});
				tableData += '</tr>';

				$('#details').append(tableData);

				$('#started_at').val(result.started_at);				

				$("#tag").prop('disabled', true);
				fetchNgTemp();
				openSuccessGritter('Success', result.message);
			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);
				$('#model').text("");
				$('#key').text("");
				$("#tag").val("");
				$("#tag").focus();
			}
		});
	}

	function showNgDetail(ng_name) {
		if($('#serial_number').text() == ""){
			audio_error.play();
			openErrorGritter('Error!', 'Scan RFID first.');
			$("#tag").val("");
			$("#tag").focus();
		}
		else{
			var btn = document.getElementById('confNg');
			btn.disabled = false;
			btn.innerText = 'Confirm'

			var location = '{{$loc_spec}}';
			var data = {
				ng_name:ng_name,
				location:location
			}
			$('#modalNg').modal('show');
			var bodyDetail = "";
			$('#ngDetailBody').html("");
			var index = 1;
			var indexNg = 1;

			$.get('{{ url("fetch/assembly/ng_detail") }}', data, function(result, status, xhr){
				$.each(result.ng_detail, function(key, value) {
					if (index % 2 == 0) {
						var color = 'style="background-color: #fffcb7"';
					}else{
						var color = 'style="background-color: #ffd8b7"'
					}
					index++;
					bodyDetail += "<tr "+color+">";
					bodyDetail += '<td id="minus" onclick="minus('+index+')" style="background-color: rgb(255,204,255); font-weight: bold; font-size: 45px; cursor: pointer;" class="unselectable">-</td>';
					if (value.ng_name == value.ng_detail) {
						bodyDetail += '<td style="font-size: 20px;" id="ng'+index+'">'+value.ng_detail+'</td>';
					}else{
						bodyDetail += '<td style="font-size: 20px;" id="ng'+index+'">'+value.ng_name+' - '+value.ng_detail+'</td>';
					}
					bodyDetail += '<td id="plus" onclick="plus('+index+')" style="background-color: rgb(204,255,255); font-weight: bold; font-size: 45px; cursor: pointer;" class="unselectable">+</td>';
					bodyDetail += '<td style="font-weight: bold; font-size: 45px; background-color: rgb(100,100,100); color: yellow;"><span id="count'+index+'">0</span></td>';
					bodyDetail += "</tr>";
					indexNg++;
					$('#judul_ng').html(value.ng_name);
				});

				$('#loop').val(indexNg);
				$('#ngDetailBody').append(bodyDetail);
			});
		}
	}

	function fetchNgTemp() {
		var tag = $('#tag2').val();
		var employee_id = $('#employee_id').val();
		var serial_number = $('#serial_number2').val();
		var model = $('#model2').val();

		var data = {
			tag:tag,
			employee_id:employee_id,
			serial_number:serial_number,
			model:model
		}

		var bodyNgTemp = "";
		$('#ngTempBody').html("");
		var index = 1;

		$.get('{{ url("fetch/assembly/ng_temp") }}', data, function(result, status, xhr){
			$.each(result.ng_temp, function(key, value) {
				if (index % 2 == 0) {
					var color = 'style="background-color: #fffcb7"';
				}else{
					var color = 'style="background-color: #ffd8b7"'
				}
				index++;
				bodyNgTemp += "<tr "+color+">";
				bodyNgTemp += '<td style="font-size: 20px;">'+value.ng_name+'</td>';
				bodyNgTemp += '<td style="font-size: 20px;">'+value.value_atas+'</td>';
				bodyNgTemp += "</tr>";
			});

			$('#ngTempBody').append(bodyNgTemp);
		});
	}

	function confNgTemp() {
		var loop = $('#loop').val();
		// var total = 0;
		var count_ng = 0;
		var ng = [];
		var count_text = [];
		for (var i = 1; i <= loop; i++) {
			if($('#count'+i).text() > 0){
				ng.push([$('#ng'+i).text(), $('#count'+i).text()]);
				count_text.push('#count'+i);
				count_ng += 1;
			}
		}

		var data = {
			tag : $('#tag2').val(),
			employee_id : $('#employee_id').val(),
			serial_number : $('#serial_number2').val(),
			model : $('#model2').val(),
			location : $('#location_now2').val(),
			started_at : $('#started_at').val(),
			ng: ng,
			count_text: count_text,
			origin_group_code : '041'
		}

		$.post('{{ url("input/assembly/ng_temp") }}', data, function(result, status, xhr){
			if(result.status){
				var btn = document.getElementById('confNg');
				btn.disabled = true;
				btn.innerText = 'Posting...'
				$('#modalNg').modal('hide');
				fetchNgTemp();
				openSuccessGritter('Success!', result.message);
			}
			else{
				var btn = document.getElementById('confNg');
				btn.disabled = false;
				btn.innerText = 'CONFIRM';
				audio_error.play();
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function disabledButton() {
		if($('#tag').val() != ""){
			var btn = document.getElementById('conf1');
			btn.disabled = true;
			btn.innerText = 'Posting...'
			return false;
		}
	}

	function disabledButtonRework() {
		if($('#tag').val() != ""){
			var btn = document.getElementById('rework');
			btn.disabled = true;
			btn.innerText = 'Posting...'	
			return false;
		}
	}

	function rework(){
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
			started_at: $('#started_at').val(),
			ng: ng,
			count_text: count_text,
			// total_ng: total,
		}
		disabledButtonRework();

		$.post('{{ url("input/welding/rework") }}', data, function(result, status, xhr){
			if(result.status){
				var btn = document.getElementById('rework');
				btn.disabled = false;
				btn.innerText = 'REWORK';
				openSuccessGritter('Success!', result.message);
				for (var i = 1; i <= loop; i++) {
					$('#count'+i).text(0);
				}
				$('#attention_point').html("");
				$('#check_point').html("");
				$('#model').text("");
				$('#key').text("");
				$('#material_tag').val("");
				$('#material_number').val("");
				$('#material_quantity').val("");
				$('#opwelding').val("");
				$('#tag').val("");
				$('#tag').prop('disabled', false);
				$('#tag').focus();	

				fetchResult($('#op').text());

			}
			else{
				var btn = document.getElementById('rework');
				btn.disabled = false;
				btn.innerText = 'REWORK';
				audio_error.play();
				openErrorGritter('Error!', result.message);
				$("#tag").val("");
				$("#tag").focus();
			}
		});
	}

	function conf(){
		if($('#tag').val() == ""){
			openErrorGritter('Error!', 'Tag is empty');
			audio_error.play();
			$("#tag").val("");

			return false;
		}

		disabledButton();

		var data = {
			tag : $('#tag2').val(),
			employee_id : $('#employee_id').val(),
			serial_number : $('#serial_number2').val(),
			model : $('#model2').val(),
			location : $('#location_now2').val(),
			started_at : $('#started_at').val(),
			origin_group_code : '041'
		}

		$.post('{{ url("input/assembly/kensa") }}', data,function(result, status, xhr){
			if(result.status){
				var btn = document.getElementById('conf1');
				btn.disabled = false;
				btn.innerText = 'CONFIRM';
				openSuccessGritter('Success!', result.message);
				$('#model').text("");
				$('#serial_number').text("");
				$('#location_now').text("");
				$('#details').text("");
				$('#tag').val("");
				$('#tag').prop('disabled', false);
				$('#tag').focus();
				deleteNgTemp();
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
		$('#model').text("");
		$('#serial_number').text("");
		$('#location_now').text("");
		$('#details').text("");
		$('#tag').val("");
		$('#tag').prop('disabled', false);
		$('#tag').focus();
		deleteNgTemp();
	}

	function deleteNgTemp() {
		var tag = $('#tag2').val();
		var employee_id = $('#employee_id').val();
		var serial_number = $('#serial_number2').val();
		var model = $('#model2').val();

		var data = {
			tag:tag,
			employee_id:employee_id,
			serial_number:serial_number,
			model:model
		}

		$.get('{{ url("delete/assembly/delete_ng_temp") }}', data, function(result, status, xhr){
			if (result.status) {
				fetchNgTemp();
				openSuccessGritter('Success',result.message);
			}else{
				openErrorGritter('Error!','Temp Not Found');
			}
		});
	}

	function plus(id){
		var count = $('#count'+id).text();
		if($('#serial_number').text() != ""){
			$('#count'+id).text(parseInt(count)+1);
		}
		else{
			audio_error.play();
			openErrorGritter('Error!', 'Scan RFID first.');
			$("#tag").val("");
			$("#tag").focus();
		}
	}

	function minus(id){
		var count = $('#count'+id).text();
		if($('#serial_number').text() != ""){
			if(count > 0)
			{
				$('#count'+id).text(parseInt(count)-1);
			}
		}
		else{
			audio_error.play();
			openErrorGritter('Error!', 'Scan RFID first.');
			$("#tag").val("");
			$("#tag").focus();
		}
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
