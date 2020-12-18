@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
thead>tr>th{
	text-align:center;
	vertical-align: middle;
}
tbody>tr>td{
	text-align:center;
}
tfoot>tr>th{
	text-align:center;
	vertical-align: middle;
}
td:hover {
	overflow: visible;
}
table.table-bordered{
	border:1px solid black;
	padding: 1px;
}
table.table-bordered > thead > tr > th{
	border:1px solid black;
	vertical-align: middle;
}
table.table-bordered > tbody > tr > td{
	border:1px solid rgb(211,211,211);
	padding-top: 0px;
	padding-bottom: 0px;
}
table.table-bordered > tfoot > tr > th{
	border:1px solid rgb(211,211,211);
}
#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header" >
	<h1>
		{{ $page }}<span class="text-purple"> {{ $title_jp }}</span>
	</h1>
</section>
@stop
@section('content')
<section class="content" >
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please Wait... <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12" style="text-align: center;">
			<div class="row">
				<div class="col-md-6 col-md-offset-2">
					<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
						<div class="input-group input-group-lg">
							<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: none; font-size: 18px;">
								<i class="fa fa-credit-card-alt"></i>
							</div>
							<input type="text" class="form-control" style="text-align: center;" placeholder="SCAN RFID" id="tag_product">
							<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: none; font-size: 18px;">
								<i class="fa fa-credit-card-alt"></i>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
					<button class="btn btn-danger" id="btn_clear" style="font-weight: bold;font-size: 20px;width: 100%;height: 47px" disabled onclick="clearAll()">
						CLEAR
					</button>
				</div>
			</div>
		</div>

		<div class="col-xs-4" style="padding-left: 0px;padding-right: 0px">
			<div class="col-md-12" style="padding-right: 2px">
				<div class="box box-solid">
					<div class="box-body">
						<center style="background-color: #33d6ff;border-bottom: 3px solid black;border-top:0px;border-left:0px;border-right:0px;padding: 4px"><span style="font-size: 25px;text-align: center;font-weight: bold;">RESIN</span></center>
						<table id="tableResin" class="table table-bordered table-striped table-hover" style="width: 100%;padding-top: 0px">
							
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-4" style="padding-left: 0px;padding-right: 0px">
			<div class="col-md-12" style="padding-right: 2px;padding-left: 2px">
				<div class="box box-solid">
					<div class="box-body">
						<center style="background-color: #33ff92;border-bottom: 3px solid black;border-top:0px;border-left:0px;border-right:0px;padding: 4px"><span style="font-size: 25px;text-align: center;font-weight: bold;">MOLDING</span></center>
						<table id="tableMolding" class="table table-bordered table-striped table-hover" style="width: 100%;padding-top: 0px">
							
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-4" style="padding-left: 0px;padding-right: 0px">
			<div class="col-md-12" style="padding-left: 2px">
				<div class="box box-solid">
					<div class="box-body">
						<center style="background-color: #ffd333;border-bottom: 3px solid black;border-top:0px;border-left:0px;border-right:0px;padding: 4px"><span style="font-size: 25px;text-align: center;font-weight: bold;">INJECTION</span></center>
						<table id="tableInjection" class="table table-bordered table-striped table-hover" style="width: 100%;padding-top: 0px">

						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-4" style="padding-left: 0px;padding-right: 0px">
			<div class="col-md-12" style="padding-right: 2px">
				<div class="box box-solid">
					<div class="box-body">
						<center style="background-color: #ff6e6e;border-bottom: 3px solid black;border-top:0px;border-left:0px;border-right:0px;padding: 4px"><span style="font-size: 25px;text-align: center;font-weight: bold;">NG LIST</span></center>
						<table id="tableNG" class="table table-bordered table-striped table-hover" style="width: 100%;padding-top: 0px">

						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-8" style="padding-left: 0px;padding-right: 0px">
			<div class="col-md-12" style="padding-left: 2px">
				<div class="box box-solid">
					<div class="box-body">
						<center style="background-color: #cc6eff;border-bottom: 3px solid black;border-top:0px;border-left:0px;border-right:0px;padding: 4px"><span style="font-size: 25px;text-align: center;font-weight: bold;">TRANSACTION</span></center>
						<table id="tableTransaction" class="table table-bordered table-striped table-hover" style="width: 100%;padding-top: 0px">
							
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
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
      $('body').toggleClass("sidebar-collapse");
		clearAll();
	});

	function clearAll() {
		$('#tag_product').removeAttr('disabled');
		$('#tag_product').val("");
		$('#tag_product').focus();
		$('#btn_clear').prop('disabled',true);
		$('#tableInjection').html('');
		$('#tableMolding').html('');
		$('#tableResin').html('');
		$('#tableNG').html('');
		$('#tableTransaction').html('');
		$('#tableInjection').prop('style','width: 100%;padding-top: 0px');
		$('#tableMolding').prop('style','width: 100%;padding-top: 0px');
		$('#tableResin').prop('style','width: 100%;padding-top: 0px');
	}

	$('#tag_product').keyup(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			$('#loading').show();
			if($("#tag_product").val().length >= 7){
				var data = {
					tag : $("#tag_product").val(),
				}
				
				$.get('{{ url("fetch/injection/traceability") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success','Tag Found');
						$('#tag_product').prop('disabled',true);
						$('#btn_clear').removeAttr('disabled');

						var tableInjection = "";
						$('#tableInjection').html('');

						$.each(result.injection_process, function(key, value) {
							tableInjection += '<thead style="vertical-align:middle">';
							tableInjection += '<tr>';
							tableInjection += '<th style="width: 1%;font-weight: bold;border-top:0px;padding:0px">Material</th>';
							tableInjection += '<th style="width: 1%;border-top:0px;padding:0px">'+value.material_number+'<br>'+value.mat_desc+'</th>';
							tableInjection += '</tr>';
							tableInjection += '<tr>';
							tableInjection += '<th style="width: 1%;font-weight: bold">Cavity</th>';
							tableInjection += '<th style="width: 1%">'+value.cavity+'</th>';
							tableInjection += '</tr>';
							tableInjection += '<tr>';
							tableInjection += '<th style="width: 1%;font-weight: bold">No. Kanban</th>';
							tableInjection += '<th style="width: 1%">'+value.no_kanban+'</th>';
							tableInjection += '</tr>';
							tableInjection += '<tr>';
							tableInjection += '<th style="width: 1%;font-weight: bold">Mulai Injeksi Kanban</th>';
							tableInjection += '<th style="width: 1%">'+value.start_time+'</th>';
							tableInjection += '</tr>';
							tableInjection += '<tr>';
							tableInjection += '<th style="width: 1%;font-weight: bold">Selesai Injeksi Kanban</th>';
							tableInjection += '<th style="width: 1%">'+value.end_time+'</th>';
							tableInjection += '</tr>';
							tableInjection += '<th style="width: 1%;font-weight: bold">Mesin</th>';
							tableInjection += '<th style="width: 1%">'+value.mesin+'</th>';
							tableInjection += '</tr>';
							tableInjection += '<tr>';
							tableInjection += '<th style="width: 1%;font-weight: bold">Quantity</th>';
							tableInjection += '<th style="width: 1%">'+value.qty+'</th>';
							tableInjection += '</tr>';
							tableInjection += '<tr>';
							tableInjection += '<th style="width: 1%;font-weight: bold">Operator</th>';
							tableInjection += '<th style="width: 1%">'+value.employee_id+'<br>'+value.name+'</th>';
							tableInjection += '</tr>';
							tableInjection += '</thead>';
						});

						$('#tableInjection').append(tableInjection);
						$('#tableInjection').prop('style','width: 100%;height:320px;padding-top: 0px');

						var tableMolding = "";
						$('#tableMolding').html('');

						$.each(result.molding, function(key, value) {
							tableMolding += '<thead style="vertical-align:middle">';
							tableMolding += '<tr>';
							tableMolding += '<th style="width: 1%;font-weight: bold;border-top:0px">Molding</th>';
							tableMolding += '<th style="width: 1%;border-top:0px">'+value.part+'</th>';
							tableMolding += '</tr>';
							tableMolding += '<tr>';
							tableMolding += '<th style="width: 1%;font-weight: bold">Last Shot Saat Pasang</th>';
							tableMolding += '<th style="width: 1%">'+value.last_shot_pasang+'</th>';
							tableMolding += '</tr>';
							tableMolding += '<tr>';
							tableMolding += '<th style="width: 1%;font-weight: bold">Last Shot Sebelum Injeksi</th>';
							tableMolding += '<th style="width: 1%">'+value.last_shot_running+'</th>';
							tableMolding += '</tr>';
							tableMolding += '<tr>';
							tableMolding += '<th style="width: 1%;font-weight: bold">Mulai Pasang Molding</th>';
							tableMolding += '<th style="width: 1%">'+value.start_time+'</th>';
							tableMolding += '</tr>';
							tableMolding += '<tr>';
							tableMolding += '<th style="width: 1%;font-weight: bold">Selesai Pasang Molding</th>';
							tableMolding += '<th style="width: 1%">'+value.end_time+'</th>';
							tableMolding += '</tr>';
							tableMolding += '<tr>';
							tableMolding += '<th style="width: 1%;font-weight: bold">Note</th>';
							tableMolding += '<th style="width: 1%">'+value.note+'</th>';
							tableMolding += '</tr>';
							tableMolding += '<tr>';
							tableMolding += '<th style="width: 1%;font-weight: bold">Operator</th>';
							tableMolding += '<th style="width: 1%">'+value.pic+'</th>';
							tableMolding += '</tr>';
							tableMolding += '</thead>';
						});

						$('#tableMolding').append(tableMolding);
						$('#tableMolding').prop('style','width: 100%;height:320px;padding-top: 0px');

						var tableResin = "";
						$('#tableResin').html('');

						$.each(result.dryer, function(key, value) {
							tableResin += '<thead style="vertical-align:middle">';
							tableResin += '<tr>';
							tableResin += '<th style="width: 1%;font-weight: bold;border-top:0px">Material</th>';
							tableResin += '<th style="width: 1%;border-top:0px">'+value.material_number+'<br>'+value.material_description+'</th>';
							tableResin += '</tr>';
							tableResin += '<tr>';
							tableResin += '<th style="width: 1%;font-weight: bold">Dryer</th>';
							tableResin += '<th style="width: 1%">'+value.dryer+'</th>';
							tableResin += '</tr>';
							tableResin += '<tr>';
							tableResin += '<th style="width: 1%;font-weight: bold">Lot</th>';
							tableResin += '<th style="width: 1%">'+value.lot_number+'</th>';
							tableResin += '</tr>';
							tableResin += '<tr>';
							tableResin += '<th style="width: 1%;font-weight: bold">Warna</th>';
							tableResin += '<th style="width: 1%">'+value.color+'</th>';
							tableResin += '</tr>';
							tableResin += '<tr>';
							tableResin += '<th style="width: 1%;font-weight: bold">Quantity</th>';
							tableResin += '<th style="width: 1%">'+value.qty+'</th>';
							tableResin += '</tr>';
							tableResin += '<tr>';
							tableResin += '<th style="width: 1%;font-weight: bold">Waktu Pengisian</th>';
							tableResin += '<th style="width: 1%">'+value.created_at+'</th>';
							tableResin += '</tr>';
							tableResin += '<tr>';
							tableResin += '<th style="width: 1%;font-weight: bold">Operator</th>';
							tableResin += '<th style="width: 1%">'+value.employee_id+'<br>'+value.name+'</th>';
							tableResin += '</tr>';
							tableResin += '</thead>';
						});

						$('#tableResin').append(tableResin);
						$('#tableResin').prop('style','width: 100%;height:320px;padding-top: 0px');

						var tableNG = "";
						$('#tableNG').html('');

						tableNG += '<thead style="vertical-align:middle">';
						tableNG += '<tr style="border-bottom:2px solid red">';
						tableNG += '<th style="width: 1%;font-weight: bold;border-top:0px;font-size:20px">Nama NG</th>';
						tableNG += '<th style="width: 1%;border-top:0px;font-size:20px">Jumlah</th>';
						tableNG += '</tr>';
						$.each(result.injection_process, function(key, value) {
							if (value.ng_name != null) {
								ng_arr = value.ng_name.split(',');
								qty_arr = value.ng_count.split(',');

								for(var i = 0; i < ng_arr.length; i++){
									tableNG += '<tr>';
									tableNG += '<th>'+ng_arr[i]+'</th>';
									tableNG += '<th>'+qty_arr[i]+'</th>';
									tableNG += '</tr>';
								}
							}
						});
						tableNG += '</thead>';	

						$('#tableNG').append(tableNG);

						var tableTransaction = "";
						$('#tableTransaction').html('');

						tableTransaction += '<thead style="vertical-align:middle">';
						tableTransaction += '<tr style="border-bottom:2px solid red">';
						tableTransaction += '<th style="width: 1%;font-weight: bold;border-top:0px;font-size:20px">Material</th>';
						tableTransaction += '<th style="width: 1%;border-top:0px;font-size:20px">Loc</th>';
						tableTransaction += '<th style="width: 1%;border-top:0px;font-size:20px">Qty</th>';
						tableTransaction += '<th style="width: 1%;border-top:0px;font-size:20px">Status</th>';
						tableTransaction += '<th style="width: 1%;border-top:0px;font-size:20px">By</th>';
						tableTransaction += '<th style="width: 1%;border-top:0px;font-size:20px">At</th>';
						tableTransaction += '</tr>';
						$.each(result.transaction, function(key, value) {
							tableTransaction += '<tr>';
							tableTransaction += '<th>'+value.material_number+'<br>'+value.mat_desc+'</th>';
							tableTransaction += '<th>'+value.location+'</th>';
							tableTransaction += '<th>'+value.quantity+'</th>';
							tableTransaction += '<th>'+value.status+'</th>';
							tableTransaction += '<th>'+value.employee_id+'<br>'+value.name+'</th>';
							tableTransaction += '<th>'+value.created_at+'</th>';
							tableTransaction += '</tr>';
						});
						tableTransaction += '</thead>';	

						$('#tableTransaction').append(tableTransaction);

						$('#loading').hide();
					}
					else{
						openErrorGritter('Error!', 'Tag Not Found');
						audio_error.play();
						$("#tag_product").val("");
						$("#tag_product").focus();
						$('#btn_clear').prop('disabled',true);
						$('#loading').hide();
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Tag Invalid');
				audio_error.play();
				$("#tag_product").val("");
				$("#tag_product").focus();
				$('#loading').hide();
			}			
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '2000'
		});
	}

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '2000'
		});
	}
</script>
@endsection