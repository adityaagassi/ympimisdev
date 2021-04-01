@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		font-size: 1.2vw;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		font-size: 1vw;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: white; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<input type="hidden" id="location" value="label_verification">
		<input type="hidden" id="proses" value="label_verification">
		<input type="hidden" id="employee_id">
		<input type="hidden" id="material_number">
		
		<div class="col-xs-2 pull-left" style="padding: 0px;margin-left: 20px">
			<div class="input-group date">
				<div class="input-group-addon bg-green">
					<i class="fa fa-calendar"></i>
				</div>

				<input type="text" name="date_receive" id="date_receive" class="form-control datepicker" placeholder="Choose Receive Date" onchange="selectDate()"  style="width: 100%;">
			</div>
		</div>

		<div class="col-xs-12" id="label">
			<table id="LabelTable" class="table table-bordered table-stripped">
				<thead style="background-color: #3f51b5;color: white">
					<tr>
						<th colspan="6" style="font-size: 2vw;">OPERATOR <span id="data_op"></span></th>
					</tr>
					<tr>
						<th style="width: 1%; font-size: 1.5vw;">#</th>
						<th style="width: 5%; font-size: 1.5vw;">Material</th>
						<th style="width: 5%; font-size: 1.5vw;">Standard Photo</th>
						<th style="width: 5%; font-size: 1.5vw;">Photo</th>
						<th style="width: 1%; font-size: 1.5vw;">Label QR</th>
					</tr>
				</thead>
				<tbody id="LabelTableBody" style="background-color: white;">
				</tbody>
			</table>
			<button class="btn btn-success" style="width: 100%;font-size: 25px" onclick="save()"><i class="fa fa-check"></i> Submit</button>
		</div>
	</div>
</section>

<div class="modal fade" id="modalOperator">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive">
					<div class="form-group">
						<div style="background-color: #9c27b0;color: white;">
							<center>
								<h3>LABEL VERIFICATION</h3>
							</center>
						</div>
						<label for="exampleInputEmail1">Employee ID</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID Card" required>
						<br><br>
						<a href="{{ url("/index/reed") }}" class="btn btn-warning" style="width: 100%; font-size: 1vw; font-weight: bold;"><i class="fa fa-arrow-left"></i> Ke Halaman Reed</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		clearAll();
		$('#startInj').prop('disabled', true);

		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});

		$('#modalOperator').on('shown.bs.modal', function () {
			$('#operator').focus();
		});

		$('.datepicker').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			todayHighlight: true
		});
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function clearAll(){
		$('#label').hide();

		$('#employee_id').val('');
		$('#operator').val('');
	}


	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator").val().length == 9){
				var data = {
					employee_id : $("#operator").val()
				}

				$.get('{{ url("scan/reed/operator") }}', data, function(result, status, xhr){
					if(result.status){
						$('#employee_id').val(result.employee.employee_id);
						$('#data_op').text(" ("+result.employee.employee_id+" - "+result.employee.name+")")
						openSuccessGritter('Success!', result.message);
						$('#modalOperator').modal('hide');
						$('#operator').remove();
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator').val('');
					}
				});
				
			}else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator").val("");
			}
		}
	});


	function selectDate(){
		var date_receive = $('#date_receive').val();

		if(date_receive == ''){
			return false;
		}

		var data = {
			date_receive : date_receive
		}

		$.get('{{ url("fetch/reed/label_verification") }}', data, function(result, status, xhr){
			if(result.status){
				$('#label').show();
				$('#LabelTableBody').html("");


				var pickingData = "";

				$.each(result.order, function(key, value){

					$("#material_number").val(value.material_number);

					var loop = value.bag_quantity;

					for (var i = 1; i <= loop; i++) {

						if (i == 1) {
							pickingData += '<tr class="baris">';
							pickingData += '<td id='+i+' style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+i+'</td>';
							pickingData += '<td rowspan="'+loop+'" style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+value.material_number+' - '+value.material_description+'</td>';
							pickingData += '<td rowspan="'+loop+'" style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;"><img src="{{url("files/reed/foto_standard.jpg")}}" width="150px"></td>';
							pickingData += '<td id="foto_'+i+'" style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px; background-color"><input type="file" style="display:none" onchange="readURL(this,\''+i+'\');" id="file_'+i+'" class="file"><button class="btn btn-primary btn-lg" id="btnImage_'+i+'" value="Photo" onclick="buttonImage(this)">Photo Bag '+i+'</button><img width="150px" id="blah_'+i+'" src="" style="display: none" alt="your image" /></td>';
							pickingData += '<td id="input_'+i+'" style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px; background-color"><input style="text-align: center;" type="text" onkeydown="checkQr(id);" id="qr_'+i+'" class="input_qr"></td>';
							pickingData += '</tr>';
						}
						else{
							pickingData += '<tr class="baris">';
							pickingData += '<td id='+i+' style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+i+'</td>';
							pickingData += '<td id="foto_'+i+'" style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px; background-color"><input type="file" style="display:none" onchange="readURL(this,\''+i+'\');" id="file_'+i+'" class="file"><button class="btn btn-primary btn-lg" id="btnImage_'+i+'" value="Photo" onclick="buttonImage(this)">Photo Bag '+i+'</button><img width="150px" id="blah_'+i+'" src="" style="display: none" alt="your image" /></td>';
							pickingData += '<td id="input_'+i+'" style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px; background-color"><input style="text-align: center;" type="text" onkeydown="checkQr(id);" id="qr_'+i+'" class="input_qr"></td>';
							pickingData += '</tr>';
						}

					}

				});

				$('#LabelTableBody').append(pickingData);


			}else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
			}
		});
	}

	function checkQr(id) {
		var qr = $('#'+id).val();
		var material_number = $('#material_number').val();

		if(qr.length >= 7){
			if(qr == material_number){
				openSuccessGritter('Success!', '');
				$('#'+id).prop('readonly', true);
			}else{
				openErrorGritter('Error!', '');
				$('#'+id).val('');
				$('#'+id).focus();
			}
		}
	}

	function buttonImage(elem) {
		$(elem).closest("td").find("input").click();
		// console.log(input);
	}

	function readURL(input,idfile) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				var img = $(input).closest("td").find("img");
				$(img).show();
				$(img)
				.attr('src', e.target.result);
			};

			reader.readAsDataURL(input.files[0]);
		}

		$(input).closest("td").find("button").hide();
		// $('#btnImage'+idfile).hide();
	}

	function save() {
		if (confirm('Apakah Anda yakin?')) {
			var insert = true;
			$('.file').each(function(i, obj) {
				var a = i + 1;
				if($('#file_'+a).prop('files')[0] == undefined){
					insert = false;
				}
			})

			$('.input_qr').each(function(i, obj) {
				var a = i + 1;
				if($('#qr_'+a).val() == ''){
					insert = false;
				}
			})

			if (insert) {
				var file = [];
				var len = $('.baris').length;

				var formData = new FormData();
				formData.append('jumlah', len);	
				formData.append('employee_id', $('#employee_id').val());	
				formData.append('date_receive', $('#date_receive').val());				
				formData.append('material_number', $('#material_number').val());

				$('.file').each(function(i, obj) {
					formData.append('file_datas_'+i, $(this).prop('files')[0]);
					var file=$(this).val().replace(/C:\\fakepath\\/i, '').split(".");

					formData.append('extension_'+i, file[1]);
					formData.append('foto_name_'+i, file[0]);
				})

				$.ajax({
					url:"{{ url('post/reed/label_verification') }}",
					method:"POST",
					data:formData,
					dataType:'JSON',
					contentType: false,
					cache: false,
					processData: false,
					success: function (response) {
						$("#loading").hide();
						$("#blah").hide();
						$("#btnImage").show();
						openSuccessGritter("Success", "Data Berhasil Disimpan");
						location.reload();
					},
					error: function (response) {
						console.log(response.message);
					},
				})
			}
			else{
				openErrorGritter("Error", "Mohon Lengkapi Foto & Scan QR Label Saat Verifikasi");
			}
			
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

</script>
@endsection

