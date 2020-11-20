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
	
	table.table-bordered > tbody > tr:hover > td {
		background-color: #FFD700;
		cursor: pointer;
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

				<select class="form-control select2" id="no_po" name="no_po" style="width: 100%;" data-placeholder="Pilih Nomor PO" onchange="cekPO()">
	              <option value=""></option>
	              @foreach($po_detail as $po)
	              <option value="{{ $po->no_po }}">{{ $po->no_po }}</option>
	              @endforeach
	            </select>

				<!-- <div class="input-group input-group-lg">
					<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: none; font-size: 18px;">
						<i class="fa fa-qrcode"></i>
					</div>
					<input type="text" class="form-control" placeholder="Masukkan Nomor PO" id="no_po">
					<span class="input-group-btn">
						<button style="font-weight: bold;" onclick="cekPO()" class="btn btn-success btn-flat"></i>&nbsp;&nbsp;Submit</button>
					</span>
				</div> -->

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
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">SURAT JALAN</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">PRINT</th>
					</tr>
				</thead>
				<tbody id="po_body">
				</tbody>
			</table>
		</div>

		<div class="col-xs-12" style="padding: 0px;" id="confirm">
			<br>
			<div class="col-xs-6 pull-right" align="right" style="padding: 0px;">
				<span style="font-weight: bold; font-size: 20px;color: white">Item Print : </span>
				<span id="picked" style="font-weight: bold; font-size: 24px; color: red;">0</span>
				<button type="button" style="font-size:20px; height: 40px; font-weight: bold; padding: 15%; padding-top: 0px; padding-bottom: 0px;" onclick="printLabel(this)" class="btn btn-primary"><i class="fa fa-print"></i> PRINT</button>
			</div>
			<div class="col-xs-3 pull-right" align="right" style="padding: 0px;">
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

	$('.select2').select2({
	  	allowClear: true,
	  	dropdownAutoWidth : true
	});

	$('#no_po').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			var id = $("#no_po").val();
			checkCode(id);

		}
	});

	var total;

	function cekPO(){
		var id = $("#no_po").val();
		checkCode(id);
	}



	function checkCode(code) {

		var data = {
			no_po : code
		}

		$.get('{{ url("fetch/warehouse/print_equipment") }}', data, function(result, status, xhr){

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
								'date_receive' : value.date_receive,
								'surat_jalan' : value.surat_jalan
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

		$('input:checkbox').prop('checked', false);
		$('#picked').html(0);
		total = 0;


		var body = '';
		var css = 'style="background-color: #000000;"';

		$("#po_body").empty();

		var num = '';
		for (var i = 0; i < list.length; i++) {
			var css = 'style="padding: 10px; text-align: center; color: #000000; font-size: 15px;"';
			var id = 'id="'+list[i].id+'"';

			num++;
			body += '<tr '+id+'>';
			body += '<td '+id+' '+css+' onclick="countPicked(this)">'+num+'</td>';
			body += '<td '+id+' '+css+' onclick="countPicked(this)">'+list[i].no_po+'</td>';
			body += '<td '+id+' '+css+' onclick="countPicked(this)">'+list[i].no_item+'</td>';
			body += '<td '+id+' '+css+' onclick="countPicked(this)">'+list[i].nama_item+'</td>';
			body += '<td '+id+' '+css+' onclick="countPicked(this)">'+list[i].qty+'</td>';
			body += '<td '+id+' '+css+' onclick="countPicked(this)">'+list[i].qty_receive+'</td>';				
			body += '<td '+id+' '+css+' onclick="countPicked(this)">'+list[i].date_receive+'</td>';				
			body += '<td '+id+' '+css+' onclick="countPicked(this)">'+list[i].surat_jalan+'</td>';				
			body += '<td '+id+' onclick="countPicked(this)"><input type="checkbox" name="print" id="print_'+list[i].id+'"></td>';
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

	function printLabel(element){
		var tag = [];
		$("input[type=checkbox]:checked").each(function() {
			var id_print = this.id.split("_");
			tag.push(id_print[1]);
		});

		if(tag.length < 1){
			alert("Item Picked 0");
			return false;
		}

		for (var i = 0; i < tag.length; i++) {	
			// console.log(tag[i]);
			// console.log(tag.length);
			print_label(tag[i], ('Print Item'+tag[i]));
		}

	}

	function print_label(id,windowName) {
		newwindow = window.open('{{ url("print/warehouse/label") }}'+'/'+id, windowName, 'height=250,width=450');

		if (window.focus) {
			newwindow.focus();
		}

		return false;
	}


	function countPicked(element){

		var id = $(element).attr("id");
		// console.log(id);

		var checkDisabled = $('#print_'+id).prop("disabled");
		// console.log(checkDisabled);

		if(checkDisabled == undefined){

		}
		else{
			var checkVal = $('#print_'+id).is(":checked");
			// console.log(checkVal);
			if(checkVal) {
				total--;
				$('#print_'+ String(id)).prop('checked', false);

			}else{
				total++;
				$('#print_'+ String(id)).prop('checked', true);
			}


		}
		// console.log(total);
		
		$("#picked").html(total);
	}	

	function canc(){
		$('#no_po').val("");
		$('#no_po').focus();
		$('#no_po').blur();

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