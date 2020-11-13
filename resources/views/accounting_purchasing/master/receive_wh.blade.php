@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">

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
	#master:hover {
		cursor: pointer;
	}
	#master {
		font-size: 17px;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
		color: white;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding-top: 9px;
		padding-bottom: 9px;
		vertical-align: middle;
		background-color: white;
	}
	thead {
		background-color: rgb(126,86,134);
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	#loading, #error { display: none; }

	#no_po {
		text-align: center;
		font-weight: bold;
	}
	#lot {
		text-align: center;
		font-weight: bold;
	}
	#z1 {
		text-align: center;
		font-weight: bold;
	}
	#total {
		text-align: center;
		font-weight: bold;
	}
	#progress-text {
		text-align: center;
		font-weight: bold;
		font-size: 1.5vw;
		color: #fff;
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

	<div class="row" style="margin-left: 1%; margin-right: 1%;" id="main">
		<div class="col-xs-6 col-xs-offset-3" style="padding-left: 0px;">
			<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
				<div class="input-group input-group-lg">
					<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: none; font-size: 18px;">
						<i class="fa fa-qrcode"></i>
					</div>
					<input type="text" class="form-control" placeholder="Masukkan Nomor PO" id="no_po">
					<span class="input-group-btn">
						<button style="font-weight: bold;" onclick="cekPO()" class="btn btn-success btn-flat"></i>&nbsp;&nbsp;Submit</button>
					</span>
				</div>
			</div>
		</div>

		<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-top: 0%;">
			<table class="table table-bordered" id="store_table">
				<thead>
					<tr>
						<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 25px;" colspan="9" id='po_title'>Nomor PO</th>
					</tr>
					<tr>
						<th width="1%" style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">Nomor</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">NO PO</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">NO ITEM</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">DETAIL ITEM</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">QTY</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">QTY RECEIVE</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">RECEIVE DATE</th>
					</tr>
				</thead>
				<tbody id="po_body">
				</tbody>
			</table>
		</div>

		<div class="col-xs-12" style="padding: 0px;" id="confirm">
			<div class="col-xs-3 pull-right" align="right" style="padding: 0px;">
				<button type="button" style="font-size:20px; height: 40px; font-weight: bold; padding: 15%; padding-top: 0px; padding-bottom: 0px;" onclick="conf()" class="btn btn-success">SUBMIT</button>
			</div>
		</div>
	</div>
</section>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/jsQR.js")}}"></script>
<script src="{{ url("js/jquery.numpad.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var vdo;
	
	jQuery(document).ready(function() {

		$('#no_po').blur();

		$('#confirm').hide();

	});

	$('#no_po').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			var id = $("#no_po").val();
			checkCode(id);

		}
	});



	function cekPO(){
		var id = $("#no_po").val();
		checkCode(id);
	}



	function checkCode(code) {

		var data = {
			no_po : code
		}

		$.get('{{ url("fetch/warehouse/equipment") }}', data, function(result, status, xhr){

			if (result.status) {
				if(result.datas.length > 0){
					$("#po_body").append().empty();
					list = [];


					$.each(result.datas, function(index, value){

						$(".modal-backdrop").remove();
						var fillList = true;

						if(fillList){

							list.push({
								'id' : value.id, 
								'no_po' : value.no_po, 
								'no_item' : value.no_item, 
								'nama_item' : value.nama_item, 
								'qty' : value.qty,
								'qty_receive' : value.qty_receive,
								'date_receive' : value.date_receive
							});
						}
					});

					canc();
					fillStore();

					// console.log(list);
					// console.log(list.length);

				} else {
					canc();
					openErrorGritter('Error', 'Nomor PO Tidak Terdaftar');
				}			


			} else {
				canc();
				openErrorGritter('Error', 'Nomor PO Tidak Terdaftar');
			}

			$(".modal-backdrop").remove();
		});

	}


	function fillStore(){
		$("#po_body").empty();
		// console.log(list);
		var body = '';
		var num = '';
		for (var i = 0; i < list.length; i++) {
			var css = 'style="padding: 0px; text-align: center; color: #000000; font-size: 15px;"';
			num++;
			body += '<tr>';
			body += '<td '+css+'>'+num+'</td>';
			body += '<td '+css+'>'+list[i].no_po+'</td>';
			body += '<td '+css+'>'+list[i].no_item+'</td>';
			body += '<td '+css+'>'+list[i].nama_item+'</td>';
			body += '<td '+css+'>'+list[i].qty+'</td>';

			if (list[i].qty_receive == 0) {
				body += '<td style="padding:2px;text-align:left"> <input type="text" name="qty_receive_'+list[i].id+'" id="qty_receive_'+list[i].id+'" class="form-control qty" placeholder="Qty Receive"> </td>';
			}
			else if (list[i].qty != list[i].qty_receive) {
				body += '<td style="padding:2px;text-align:left">Inputted : '+list[i].qty_receive+' | <input type="text" name="qty_receive_'+list[i].id+'" id="qty_receive_'+list[i].id+'" class="form-control qty" placeholder="Qty Receive"> </td>';	
			}
			else{
				body += '<td '+css+'>'+list[i].qty_receive+'</td>';				
			}

			body += '<td '+css+'><input type="text" class="form-control pull-right datepicker dt" id="date_receive_'+list[i].id+'" name="date_receive_'+list[i].id+'" placeholder="Date Receive"></td>';

			body += '</tr>';

		}
		$("#po_body").append(body);

		if(list.length > 0){
			$('#confirm').show();
		}

		$('.datepicker').datepicker({
			autoclose: true,
			todayHighlight: true,
			format: "yyyy-mm-dd",
			orientation: 'bottom auto',
		});

	}

	function canc(){
		$('#no_po').val("");
		$('#no_po').focus();
		$('#no_po').blur();

	}

	function conf() {
		$("#loading").show();

		var arr_params = [];

		$('.qty').each(function(index, value) {
			ids = $(this).attr('id').split('_');
			arr_params.push({'id' : ids[2], 'qty' : $(this).val(), 'date' : $('#date_receive_'+ids[2]).val()});
		});

		var data = {
			item : arr_params
		}

		if(confirm("Data akan simpan oleh sistem.\nData tidak dapat dikembalikan.")){

			$.post('{{ url("fetch/warehouse/update_receive") }}', data, function(result, status, xhr){
				if (result.status) {
					openSuccessGritter('Success', result.message);

					$("#po_body").empty();
					$('#confirm').hide();
					$("#loading").hide();
					
					list = [];
				}else{
					$("#loading").hide();
					openErrorGritter('Error', result.message);
				}
			});
		}else{
			$("#loading").hide();
		}
		
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '4000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '4000'
		});
	}

</script>
@endsection