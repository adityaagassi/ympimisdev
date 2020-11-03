@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">
<style type="text/css">
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		/* display: none; <- Crashes Chrome on hover */
		-webkit-appearance: none;
		margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
	}

	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-4" style="padding: 0 0 0 10px;">
			<table style="width: 100%; text-align: center; background-color: orange; font-weight: bold; font-size: 1.5vw;" border="1">
				<thead>
					<tr>
						<th colspan="3" style="text-align: center;">Operator Kensa Jig</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 2%;" id="op_id">-</td>
						<td style="width: 8%;" id="op_name">-</td>
						<td style="width: 2%;" rowspan="2"><button class="btn btn-warning" style="width:100%;font-weight: bold;font-size: 14px" onclick="location.reload()">CHANGE</button></td>
					</tr>
				</tbody>
			</table>
			<div class="col-xs-12" style="padding: 0px;">
				<div class="input-group">
					<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
						<i class="glyphicon glyphicon-qrcode"></i>
					</div>
					<input type="text" style="text-align: center; border-color: black;font-size: 20px" class="form-control" id="tagJig" name="tagJig" placeholder="Scan Tag Jig" required disabled>
					<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
						<i class="glyphicon glyphicon-qrcode"></i>
					</div>
				</div>
			</div>
			<table style="width: 100%; text-align: center; background-color: #00c0ef; font-weight: bold; font-size: 1.5vw;" border="1">
				<thead>
					<tr>
						<th colspan="3" style="text-align: center;">Informasi Jig</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 3%;" id="jigID">-</td>
						<td style="width: 3%;" id="jigIndex">-</td>
						<td style="width: 8%;" id="jigName">-</td>
					</tr>
				</tbody>
			</table>
			<table style="width: 100%; background-color: white; font-weight: bold; font-size: 1.3vw;" border="1" id="drawingTable">
				<thead>
					<th style="background-color:rgb(126,86,134);color:white;text-align: center; width: 1%; color: #FFD700;">Drawing List</th>
				</thead>
				<tbody id="drawingTableBody">
					<tr>
						<td style="padding-bottom: 5px" id="drawing"></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-xs-8" style="padding: 0 10px 0 10px;">
			<table style="width: 100%; background-color: rgb(236, 240, 245); font-weight: bold; font-size: 1.3vw;" border="1" id="checkTable">
				<thead style="background-color: rgb(126,86,134); color: #FFD700;">
					<tr>
						<th style="text-align: center; width: 1%;">#</th>
						<th style="text-align: center; width: 9%;">Point Check</th>
						<th style="text-align: center; width: 3%;">Part</th>
						<th style="text-align: center; width: 3%;">Jig Alias</th>
						<th style="text-align: center; width: 2%;">Min</th>
						<th style="text-align: center; width: 2%;">Max</th>
						<th style="text-align: center; width: 2%;">Value</th>
						<th style="text-align: center; width: 2%;">Result</th>
					</tr>
				</thead>
				<tbody id="checkTableBody" style="background-color: rgb(236, 240, 245)">
				</tbody>
			</table>
			<div class="col-xs-6" style="padding-left: 0;padding-right: 5px;padding-top: 5px">
				<button class="btn btn-danger" style="width: 100%; font-size: 2vw; font-weight: bold; padding:0;" onclick="cancelProcess()">CANCEL</button>
			</div>
			<div class="col-xs-6" style="padding-right: 0;padding-left: 5px;padding-top: 5px">
				<button class="btn btn-success" onclick="confirmKensa()" style="width: 100%; font-size: 2vw; font-weight: bold; padding:0;">CONFIRM</button>
			</div>
		</div>
		<div class="col-xs-12" style="margin-top: 5px;padding-top: 5px">
			<div id="drawingDetail">
				
			</div>
		</div>
	</div>
<input type="hidden" id="employee_id">
<input type="hidden" id="jig_id">
<input type="hidden" id="jig_index">
<input type="hidden" id="started_at">
<input type="hidden" id="check_index">
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
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});

		$('#modalOperator').on('shown.bs.modal', function () {
			$('#operator').focus();
		});

		$('#operator').val("");
		$('#tagJig').prop('disabled',true);
		$('#tagJig').val('');
	});

	$('#tagJig').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tagJig").val().length == 10){
				var data = {
					tag : $("#tagJig").val(),
					category : 'KENSA'
				}
				$.get('{{ url("scan/welding/jig") }}', data, function(result, status, xhr){
					if(result.status){
						$('#jigID').text(result.jig.jig_id);
						$('#jigIndex').text(result.jig.jig_index);
						$('#jig_id').val(result.jig.jig_id);
						$('#jig_index').val(result.jig.jig_index);
						$('#jigName').text(result.jig.jig_name);
						$('#started_at').val(result.started_at);
						$('#tagJig').prop('disabled',true);
						fetchJigCheck(result.jig.jig_id,result.jig.jig_index);
						fetchDrawingList(result.jig.jig_id);
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#tagJig').val('');
						$('#tagJig').focus();
					}
				});
			}
			else{
				audio_error.play();
				openErrorGritter('Error', 'Tag Tidak Ditemukan');
				$('#tagJig').val('');
				$('#tagJig').focus();
			}
		}
	});

	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator").val().length >= 8){
				var data = {
					employee_id : $("#operator").val()
				}

				$.get('{{ url("scan/welding/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#modalOperator').modal('hide');
						$('#op_id').html(result.employee.employee_id);
						$('#op_name').html(result.employee.name.split(' ').slice(0,2).join(' '));
						$('#employee_id').val(result.employee.employee_id);
						$('#tagJig').removeAttr('disabled');
						$('#tagJig').focus();
						// setInterval(focusTag, 1000);
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator').val('');
					}
				});
			}
			else{
				audio_error.play();
				openErrorGritter('Error!', 'Employee ID Invalid.');
				$("#operator").val("");
			}			
		}
	});

	function fetchJigCheck(jig_id,jig_index) {
		var data = {
			jig_id : jig_id,
			jig_index : jig_index
		}
		$.get('{{ url("fetch/welding/jig_check") }}', data, function(result, status, xhr){
			if(result.status){
				var checkTable = "";
				$('#checkTableBody').empty();
				var valueCheck = [];
				var index = 1;
				var check_index = 0;
				$.each(result.jig_check, function(key, value) {
					checkTable += '<tr style="padding:2px" id="check_index_'+value.check_index+'">';
					checkTable += '<td><center>'+index+'</center></td>';
					checkTable += '<td><center id="check_name_'+value.check_index+'">'+value.check_name+'</center></td>';
					checkTable += '<td><center id="part_'+value.check_index+'">'+value.jig_child+'</center></td>';
					checkTable += '<td><center id="jig_alias_'+value.check_index+'">'+value.jig_alias+'</center></td>';
					checkTable += '<td><center id="lower_'+value.check_index+'">'+value.lower_limit+'</center></td>';
					checkTable += '<td><center id="upper_'+value.check_index+'">'+value.upper_limit+'</center></td>';
					checkTable += '<td><input type="number" id="value_'+value.check_index+'" class="numpad" style="width:100%;background-color:rgb(236, 240, 245);text-align: center;" onchange="checkPoint(this.value,\''+value.check_index+'\')" value="'+value.upper_limit+'"></td>';
					checkTable += '<td><center id="result_'+value.check_index+'">OK</center></td>';
					checkTable += '</tr>';
					valueCheck.push(value.check_index);
					check_index = value.check_index;
					index++;
				});

				$('#check_index').val(check_index);

				$('#checkTableBody').append(checkTable);

				for(var i = 0; i< valueCheck.length;i++){
					var id = "#value_"+valueCheck[i];
					$(id).numpad({
						target: $("#value_"+valueCheck[i]),
						hidePlusMinusButton : true,
						decimalSeparator : '.'
					});
				}
			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);
			}
		});
	}

	function checkPoint(value,index) {
		var upper = $('#upper_'+index).html();
		var lower = $('#lower_'+index).html();

		var id = 'check_index_'+index;
		var id2 = 'value_'+index;

		if (value != "" || parseFloat(value) != 0) {
			if (parseFloat(value) > parseFloat(upper) || parseFloat(value) <= parseFloat(lower)) {
				document.getElementById(id).style.backgroundColor = "#ff8282";
				document.getElementById(id2).style.backgroundColor = "#ff8282";
				$('#result_'+index).html('NG');
			}else{
				document.getElementById(id).style.backgroundColor = "#7fff6e";
				document.getElementById(id2).style.backgroundColor = "#7fff6e";
				$('#result_'+index).html('OK');
			}
		}
	}

	function cancelProcess() {
		$('#tagJig').removeAttr('disabled');
		$('#tagJig').val('');
		$('#tagJig').focus();
		$('#jigID').html("-");
		$('#jigIndex').html("-");
		$('#jigName').html("-");
		$('#jig_id').val("");
		$('#jig_index').val("");
		$('#started_at').val("");
		$('#checkTableBody').empty();
		$('#drawing').empty();
		$('#drawingDetail').empty();
	}

	function fetchDrawingList(jig_id) {
		var data = {
			jig_id : jig_id
		}
		$.get('{{ url("fetch/welding/drawing_list") }}', data, function(result, status, xhr){
			if(result.status){
				var drawing = "";
				$('#drawing').empty();
				var index = 1;
				$.each(result.drawing, function(key, value) {
					var jig_childs = value.jig_child.split('-');
					var jig_names = value.jig_name.split(' ');
					drawing += '<div class="col-xs-4" style="padding-left:5px;padding-right:5px;margin-top:5px"><button class="btn btn-warning" onclick="fetchDrawing(\''+value.jig_parent+'\',\''+value.file_name+'\',\''+result.file_path+'\')" style="height:40px;width:100%;white-space: normal;padding:0px"><b>'+jig_childs[2]+'-'+jig_childs[3]+'<br>'+jig_names[3]+' '+jig_names[4]+'</b></button></div>';
					index++;
				});

				$('#drawing').append(drawing);
			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);
			}
		});
	}

	function fetchDrawing(jig_parent,file_name,file_path) {
		$('#drawingDetail').empty();
		// console.log(file_name);
		if (file_name === "") {
			$('#drawingDetail').append('Drawing Tidak Tersedia.');
		}else{
			if(file_name.includes('.pdf')){
				$('#drawingDetail').append("<embed src='"+ file_path +"/"+ jig_parent +"/"+ file_name +"' type='application/pdf' width='100%' height='600px'>");
				console.log("<embed src='"+ file_path +"/"+ jig_parent +"/"+ file_name +"' type='application/pdf' width='100%' height='600px'>");
			}
		}
	}

	function confirmKensa() {
		$('#loading').show();
		var operator_id = $('#employee_id').val();
		var jig_id = $('#jig_id').val();
		var jig_index = $('#jig_index').val();
		var started_at = $('#started_at').val();
		var check_indexes = $('#check_index').val();

		var check_index = [];
		var check_name = [];
		var upper_limit = [];
		var lower_limit = [];
		var value = [];
		var result = [];
		var jig_child = [];
		var jig_alias = [];

		for(var i = 1; i <= check_indexes;i++ ){
			check_index.push(i);
			check_name.push($('#check_name_'+i).text());
			upper_limit.push($('#upper_'+i).text());
			lower_limit.push($('#lower_'+i).text());
			value.push($('#value_'+i).val());
			result.push($('#result_'+i).text());
			jig_child.push($('#part_'+i).text());
			jig_alias.push($('#jig_alias_'+i).text());
		}

		var data = {
			operator_id:operator_id,
			jig_id:jig_id,
			jig_index:jig_index,
			started_at:started_at,
			check_indexes:check_indexes,
			check_index:check_index,
			check_name:check_name,
			upper_limit:upper_limit,
			lower_limit:lower_limit,
			value:value,
			result:result,
			jig_child:jig_child,
			jig_alias:jig_alias,
		}

		$.post('{{ url("input/welding/kensa_jig") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
				$('#loading').hide();
				cancelProcess();
			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);
				$('#loading').hide();
			}
		});
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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
</script>
@endsection