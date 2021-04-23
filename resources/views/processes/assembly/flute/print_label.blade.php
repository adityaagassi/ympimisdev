@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
		padding: 0;
	}
	tfoot>tr>th{
		text-align:center;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	table {
		table-layout:fixed;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid black;
		/*margin-top:20px;*/
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	.btn{
		white-space: normal;
		word-wrap: break-word;
	}
	.test{
		height:60px;

	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Print Label FL<span class="text-purple"> ??? </span>
		<small>Serial Number <span class="text-purple"> 製番</span></small>
	</h1>
	<ol class="breadcrumb">
		<li>
			<button class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal-reprint">
				<i class="fa fa-print"></i>&nbsp;&nbsp;Reprint
			</button>
			{{-- <a href="{{ url("/index/assembly/stamp_record") }}" class="btn btn-primary btn-sm" style="color:white"><i class="fa fa-calendar-check-o "></i>&nbsp;Record</a> --}}
		</li>
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Success!</h4>
		{{ session('status') }}
	</div>   
	@endif
	<div class="row">
		<div class="col-md-12">
			<div class="box box-primary">
				<div class="box-body">
					<input type="hidden" value="{{ Auth::user()->role_code }}" id="role_code" />
					<div class="row">
						<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
							<p style="position: absolute; color: White; top: 45%; left: 35%;">
								<span style="font-size: 40px">Loading, please wait...<i class="fa fa-spin fa-refresh"></i></span>
							</p>
						</div>
						<div id="error" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(255,102,102); z-index: 30001; opacity: 0.8;">
							<p id="pError" style="position: absolute; color: White; top: 45%; left: 35%;">
								
							</p>
						</div>
						

						<div class="col-xs-4">
							<center>
								<span style="font-size: 20px;">Total Production :</span>
							</center>
							<table id="table-model" class="table table-bordered table-hover table-striped">
								<thead style="background-color: rgba(126,86,134,.7);">
									<th>Model</th>
									<th>Production</th>

								</thead>
								<tbody id="body-model">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
									<th>Total</th>
									<th id="foot-model"></th>
								</tfoot>
							</table>
						</div> 
						
						
						<div class="col-xs-3">
							<center>
								<span style="font-size: 24px">Last Print:</span><br>
								<input id="last_serial_number" type="text" style="font-weight: bold; background-color: rgb(255,255,204); width: 100%; text-align: center; font-size: 3vw" disabled>
								<input id="last_model" type="text" style="background-color: rgb(255,127,80); width: 100%; text-align: center; font-size: 2vw" disabled>

								<span style="font-size: 24px">Tap Tag RFID:</span><br>
								<input id="tag" type="text" style="font-weight: bold; width: 100%; text-align: center; font-size: 2vw">
								<input id="serial_number" type="text" style="font-weight: bold; background-color: rgb(255,255,204); width: 100%; text-align: center; font-size: 3vw; border-bottom: 0px;" disabled>
								<input id="model" type="text" style="font-weight: bold; background-color: rgb(255,127,80); width: 100%; text-align: center; font-size: 2vw" disabled>
								

								<span style="font-size: 24px">Select Model</span><b id="japan" style="font-size: 24px"></b><br>
								<center>
									<div id="listModel">
									</div><br>
									<div id="button">
									</div>
								</center>
								<input type="text" id="japan2" hidden="">
								<input type="text" id="gmc"  value="" hidden="">

							</center>
						</div>

						<div class="col-xs-5">
							<center>
								<span style="font-size: 24px;">Result:</span>
							</center>
							<table id="table-result" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<th style="width: 20%">Serial Number</th>
									<th style="width: 25%">Model</th>
									<th style="width: 40%">Printed At</th>
								</thead>
								<tbody id="body-model">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
								</tfoot>
							</table>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modal-print">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h2 class="modal-title">Print Label</h2>
				</div>
				<div class="modal-body">
					<input type="text" style="font-weight: bold; background-color: rgb(255,255,204);; width: 100%; text-align: center; font-size: 4vw"  id="print_serial_number"  disabled><br>
					<input type="text" style="font-weight: bold; background-color: rgb(255,127,80);; width: 100%; text-align: center; font-size: 2vw"  id="print_model"  disabled>
					<BR><BR>
					<center>
						<button class="btn btn-lg btn-success" onclick="printAll();">Print All Labels</button>
						<br><br>
						<button class="btn btn-lg btn-success" onclick="printOuter();">Label Outer</button>		
					</center>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modal-reprint">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h2 class="modal-title">Reprint Label</h2>
				</div>
				<div class="modal-body">
					<center><span style="font-size: 24px">Ketik Serial Number:</span></center><br>
					<input type="text" style="font-weight: bold; background-color: rgb(255,255,204);; width: 100%; text-align: center; font-size: 4vw"  id="reprint_serial_number" ><br>
					<input type="text" style="font-weight: bold; background-color: rgb(255,127,80);; width: 100%; text-align: center; font-size: 2vw"  id="reprint_model"  disabled>
					<input type="text" id="reprint_gmc"  hidden>
					<BR><BR>
					<div id="reprint-button">
						<center>
							<button class="btn btn-lg btn-primary" onclick="reprintBesar();">Label GMC</button>
							<button class="btn btn-lg btn-primary" onclick="reprintKecil();">Label No.Seri</button>
							<button class="btn btn-lg btn-primary" onclick="reprintDeskripsi();">Label Deskripsi</button>
							<br><br>
							<button id="reprint-carb" class="btn btn-lg btn-primary" onclick="reprintCARB();">Label CARB</button>
							<button class="btn btn-lg btn-primary" onclick="reprintOuter();">Label Outer</button>
							<br><br>
							<button type="button" class="btn btn-danger" onclick="cancelReprint()">Cancel</button>
						</center>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="print_kd">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Print Label KD</h4>
				</div>
				<div class="modal-body">
					<input type="text" style="font-weight: bold; background-color: rgb(255,255,204);; width: 100%; text-align: center; font-size: 4vw" id="kd_gmc"  disabled><br>
					<input type="text" style="font-weight: bold; background-color: rgb(255,127,80);; width: 100%; text-align: center; font-size: 2vw" id="kd_desc"  disabled>
					<BR><BR>
					<center>
						<div id='all'>
							<button class="btn btn-lg btn-success btn-kd-desc" onclick="printKdDes();">Label Deskripsi</button>
							<button class="btn btn-lg btn-success btn-kd-besar" onclick="printKdBesar();">Label GMC</button>
							<button class="btn btn-lg btn-success btn-kd-carb" onclick="printKdCarb();">Label CARB</button>
							<button class="btn btn-lg btn-success btn-kd-outer" onclick="printKdOuter();">Label Outer</button>
						</div>
						<div id='partial'>
							<button class="btn btn-lg btn-success btn-kd-desc" onclick="printKdDes();">Label Deskripsi</button>
							<button class="btn btn-lg btn-success btn-kd-besar" onclick="printKdBesar();">Label GMC</button>
						</div>

					</center>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
				</div>
			</div>
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

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$(function () {
			$('.select2').select2()
		});

		$('#tag').focus();
		$('#tag').val('');
		$('#serial_number').val('');
		$('#model').val('');
		$('#gmc').val('');

		fillModelResult();
		fillResult();

	});

	function fillModelResult() {
		$.get('{{ url("fetch/assembly/flute/fillModelResult") }}', function(result, status, xhr){
			if (result.status) {

				$("#body-model").empty();
				$("#foot-model").empty();

				var body = '';
				var quantity = 0;
				for (var i = 0; i < result.data.length; i++) {
					body += '<tr>';
					body += '<td>'+result.data[i].model+'</td>';
					body += '<td>'+result.data[i].quantity+'</td>';
					body += '</tr>';
					quantity += result.data[i].quantity;
				}

				$("#body-model").append(body);
				$("#foot-model").html(quantity);
				
			}
		});
	}

	function fillResult() {
		$('#table-result').DataTable().destroy();

		$('#table-result').DataTable({
			"sDom": '<"top"i>rt<"bottom"flp><"clear">',
			'paging'      	: true,
			'lengthChange'	: false,
			'searching'   	: true,
			'ordering'		: false,
			'info'       	: true,
			'autoWidth'		: false,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"infoCallback": function( settings, start, end, max, total, pre ) {
				return "<b>Total "+ total +" pc(s)</b>";
			},
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/assembly/flute/fillResult") }}"
			},
			"columns": [
			{ "data": "serial_number"},
			{ "data": "model"},
			{ "data": "created_at"}
			]
		});
	}

	$('#print_kd').on('hidden.bs.modal', function (e) {
		cancel();
	})

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			var tag = $("#tag").val();

			if(tag.length == 10){
				tapTag();
			}else if(tag.length == 7){
				var data ={
					gmc:tag
				};

				$.get('{{ url("fetch/check_kd_gmc") }}', data, function(result, status, xhr){
					if(result.status){

						$("#kd_gmc").val(result.material.material_number);
						$("#kd_desc").val(result.material.material_description);

						if(result.material.material_number == 'ZE92410'){
							$("#all").css({"display":"block"});
							$("#partial").css({"display":"none"});
						}else{
							$("#all").css({"display":"none"});
							$("#partial").css({"display":"block"});
						}
						$('#print_kd').modal('show');


					}
				});
			}else{
				$("#tag").val('');
				openErrorGritter('Error', 'Tag Invalid');
			}

		}
	});

	function tapTag(){
		var tag = $("#tag").val();

		var data ={
			tag : tag,
			origin_group : '041'
		};


		$.get('{{ url("fetch/assembly/flute/fetchCheckTag") }}', data, function(result, status, xhr){
			if (result.status) {

				cancel();

				$('#serial_number').val(result.data.serial_number);
				$('#model').val(result.data.model);

				$('#listModel').html("");
				var planData = '';
				for (var i = 0; i < result.model.length; i++) {
					if(result.model[i].remark=="J") {
						planData += '<button type="button" class=" test btn bg-blue btn-lg" style="margin-top: 2px; margin-left: 1px; margin-right: 1px; width: 32%; font-size: 0.8vw" id="'+result.model[i].material_number+'" name="'+result.model[i].material_description+'" onclick="model(name,id,\'J\');japan(\'(Japan)\')">'+result.model[i].material_description+'<br>Japan'+'</button>';
					}else{
						planData += '<button type="button" class=" test btn bg-olive btn-lg" style="margin-top: 2px; margin-left: 1px; margin-right: 1px; width: 32%; font-size: 0.8vw" id="'+result.model[i].material_number+'" name="'+result.model[i].material_description+'" onclick="model(name,id,\'NJ\');japan(\'\')">'+result.model[i].material_description+'<br>'+'</button>';	
					}
				}
				$('#listModel').append(planData);

				var button = '<button id="btnprint" style="font-weight: bold; width: 100%; text-align: center; font-size: 3vw;" class="btn btn-primary" onclick="print();"><i class="fa fa-print"></i>&nbsp;&nbsp;Print</button><br><br>';
				button += '<button style="font-weight: bold; width: 100%; text-align: center; font-size: 3vw;" class="btn btn-danger" onclick="cancel();"><i class="fa fa-close"></i>&nbsp;&nbsp;Cancel</button>';
				$('#button').append(button);


			}else{
				$("#tag").val('');
				openErrorGritter('Error', result.message);
			}
		});
	}

	function print(){
		var serial_number = $("#serial_number").val();
		var model = $("#model").val();
		var jpn = $("#japan2").val();
		var gmc = $("#gmc").val();



		if(gmc){
			$('#print_serial_number').val(serial_number);
			$('#print_model').val(model);
			$('#modal-print').modal('show');

			console.log(model);
		}else{
			alert('Pilih model dahulu');			
		}
	}

	function printOuter(){
		var serial_number = $("#serial_number").val();
		var gmc = $("#gmc").val();

		window.open('{{ url("index/assembly/flute/label_outer") }}'+'/'+serial_number+'/'+gmc+'/P', '_blank');
		lastPrint();
	}

	function printAll(){
		var serial_number = $("#serial_number").val();
		var gmc = $("#gmc").val();

		window.open('{{ url("index/assembly/flute/label_besar") }}'+'/'+serial_number+'/'+gmc+'/P', '_blank');

		setTimeout(function() {
			printCARB(serial_number);
		}, 5000);

		lastPrint();
	}

	function lastPrint(){
		var serial_number = $("#serial_number").val();
		var model = $("#model").val();

		$('#last_serial_number').val(serial_number);
		$('#last_model').val(model);

		fillModelResult();
		fillResult();
	}

	function model(name,id,japan){
		$('#model').val(name);
		$('#gmc').val(id);
		$('#japan2').val(japan);
	}

	function japan(id) {
		$('#japan').text(id)
	}

	function cancel(){
		$('#tag').focus();
		$('#tag').val('');
		$('#serial_number').val('');
		$('#model').val('');
		$("#japan").val('');
		$("#japan2").val('');
		$("#gmc").val('');


		$('#listModel').html("");
		$('#button').html("");
	}

	$("#modal-print").on("hidden.bs.modal", function () {
		cancel();
	});

	$("#modal-reprint").on("shown.bs.modal", function () {
		$('#reprint_serial_number').focus();
		$('#reprint_serial_number').val('');
		$('#reprint_model').val('');
		$('#reprint-button').hide();
	});

	$("#modal-reprint").on("hidden.bs.modal", function () {
		$('#reprint_serial_number').val('');
		$('#reprint_model').val('');
		cancel();
		$('#tag').focus();
		
	});

	$('#reprint_serial_number').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			reprint();
		}
	});

	function cancelReprint(){
		$('#reprint_serial_number').focus();
		$('#reprint_serial_number').val('');
		$('#reprint_model').val('');
		$('#reprint-button').hide();
	}

	function reprint() {
		var serial_number = $("#reprint_serial_number").val();

		var data ={
			serial_number : serial_number,
			origin_group : '041'
		};


		$.get('{{ url("fetch/assembly/flute/fetchCheckReprint") }}', data, function(result, status, xhr){
			if (result.status) {

				$('#reprint_model').val(result.log.model);
				$('#reprint_gmc').val(result.log.material_number);
				$('#reprint-button').show();

				$('#reprint-carb').hide();
				if(result.log.model == 'YFL-212U//ID'){
					$('#reprint-carb').show();
				}

			}else{
				$('#reprint_serial_number').val('');
				$('#reprint_model').val('');
				openErrorGritter('Error', result.message);
			}
		});
		
	}

	function reprintOuter() {
		var serial_number = $('#reprint_serial_number').val();
		var gmc = $('#reprint_gmc').val();

		window.open('{{ url("index/assembly/flute/label_outer") }}'+'/'+serial_number+'/'+gmc+'/RP', '_blank');
		
	}

	function reprintBesar() {
		var serial_number = $('#reprint_serial_number').val();
		var gmc = $('#reprint_gmc').val();

		window.open('{{ url("index/assembly/flute/label_besar") }}'+'/'+serial_number+'/'+gmc+'/RP', '_blank');

	}

	function reprintKecil() {
		var serial_number = $('#reprint_serial_number').val();

		window.open('{{ url("index/assembly/flute/label_kecil") }}'+'/'+serial_number+'/RP', '_blank');

	}

	function reprintDeskripsi() {
		var serial_number = $('#reprint_serial_number').val();

		window.open('{{ url("index/assembly/flute/label_deskripsi") }}'+'/'+serial_number+'/RP', '_blank');

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

	function reprintCARB() {
		var sn = $('#reprint_serial_number').val();
		var data = {
			sn:sn
		}

		$.get('{{ url("fetch/check_carb_new") }}', data, function(result, status, xhr){
			if(result.status){
				if(result.model.model == 'YFL-212U//ID'){
					window.open('{{ url("index/fl_label_carb") }}'+'/'+sn, '_blank');
				}else{
					audio_error.play();
					alert('Bukan YFL-212U//ID');
				}
			}
			else{
				audio_error.play();
				alert('Attempt to retrieve data failed');
			}
		});

	}

	function printCARB(sn) {
		var model = $("#model").val();

		if(model == 'YFL-212U//ID'){
			window.open('{{ url("index/fl_label_carb") }}'+'/'+sn, '_blank');
		}

	}

	function printKdDes() {
		var gmc = $('#kd_gmc').val();
		$('.btn-kd-desc').prop('disabled', true);
		
		setTimeout(function() {
			window.open('{{ url("index/kd_label_des_fl") }}'+'/'+gmc, '_blank');
			$('.btn-kd-desc').prop('disabled', false);
		}, 1000);
	}

	function printKdBesar() {
		var gmc = $('#kd_gmc').val();
		$('.btn-kd-besar').prop('disabled', true);

		setTimeout(function() {
			window.open('{{ url("index/kd_label_besar_fl") }}'+'/'+gmc, '_blank');
			$('.btn-kd-besar').prop('disabled', false);
		}, 1000);
	}

	function printKdCarb() {
		var gmc = $('#kd_gmc').val();
		$('.btn-kd-carb').prop('disabled', true);

		setTimeout(function() {
			window.open('{{ url("index/kd_label_carb_fl") }}'+'/'+gmc, '_blank');
			$('.btn-kd-carb').prop('disabled', false);
		}, 1000);
	}

	function printKdOuter() {
		var gmc = $('#kd_gmc').val();
		$('.btn-kd-outer').prop('disabled', true);

		setTimeout(function() {
			window.open('{{ url("index/kd_label_besar_outer_fl") }}'+'/'+gmc, '_blank');
			$('.btn-kd-outer').prop('disabled', false);
		}, 1000);
	}

</script>
@endsection