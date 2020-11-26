@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">

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
				<div class="input-group input-group-lg">
					<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: none; font-size: 18px;">
						<i class="fa fa-qrcode"></i>
					</div>
					<input type="text" class="form-control" placeholder="Scan Barcode" id="no_po">
					<span class="input-group-btn">
						<button style="font-weight: bold;" onclick="cekPO()" class="btn btn-success btn-flat"></i>&nbsp;&nbsp;Submit</button>
					</span>
				</div>
			</div>
		</div>

		<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-top: 0%;">
			<input type="hidden" name="count_po" id="count_po">
			
			<div class="col-xs-2 pull-left" style="padding: 0px;">
				<input type="text" name="tanggal_terima" id="tanggal_terima" class="form-control datepicker" placeholder="Pilih Tanggal Pengiriman Barang">
			</div>

			<div class="col-xs-2" style="padding:0;">
				<input type="text" id="date_terima" name="date_terima" class="form-control timepicker" value="06:00">
			</div>

			<table class="table table-bordered" id="store_table">
				<thead>
					<tr>
						<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 25px;" colspan="9">Kirim Barang</th>
					</tr>
					<tr>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">NO PO</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">NO ITEM</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">DETAIL ITEM</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">QTY RECEIVE</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">RECEIVE DATE</th>
					</tr>
				</thead>
				<tbody id="po_body">
				</tbody>
			</table>
		</div>

		<div class="col-xs-12" style="padding: 0px;" id="confirm">
			<br>
			<div class="col-xs-6 pull-right" align="right" style="padding: 0px;">
				<button type="button" style="font-size:20px; height: 40px; font-weight: bold; padding: 15%; padding-top: 0px; padding-bottom: 0px;" onclick="conf()" class="btn btn-success"><i class="fa fa-check"></i> SAVE</button>
			</div>
		</div>
	</div>



	<div class="row" style="margin-top: 200px;margin-left: 1%;margin-right: 1%">
	    <div class="col-xs-12" style="padding:0">
	      <div class="box">
	        <div class="box-body">
	           <div class="box-header">
	              <h3 style="margin:0">Report Receive</h3>
	            </div>
	            <table id="tableHasil" class="table table-bordered table-striped table-hover">
	              <thead style="background-color: rgba(126,86,134,.7);">
	                <tr>
	                  <th>No</th>
	                  <th>Receive Report Date</th>
	                  <th>Print Receive Report</th>	                  
	                </tr>
	              </thead>
	              <tbody id="tableBodyHasil">
	              </tbody>
	            </table>
	          </div>
	        </div>
	    </div>
	  </div>
	</section>

@endsection
@section('scripts')
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
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
		$('#no_po').focus();
		$('#confirm').hide();

		$('.timepicker').timepicker({
			showInputs: false,
			showMeridian: false,
			defaultTime: '0:00',
		});

		$('.datepicker').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			todayHighlight: true
		});

    	fillTableResult();
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

	var list = [];


	function checkCode(code) {

		// console.log(code);

		var code_split = code.split("_")

		var data = {
			no_po : code_split[0],
			no_item : code_split[1],
			qty : code_split[2]
		}

		$.get('{{ url("fetch/warehouse/cetak_bukti") }}', data, function(result, status, xhr){

			if (result.status) {
				if(result.datas != null){


					$(".modal-backdrop").remove();
					var fillList = true;

					for (var i = 0; i < list.length; i++) {
						if(result.datas.id == list[i].id){
							canc();
							openErrorGritter('Error', 'Sudah Pernah di Scan<br>Cek Tabel');
							fillList = false;
						}
					}

					if(fillList){
						list.push({
							'id' : result.datas.id, 
							'no_po' : result.datas.no_po, 
							'no_item' : result.datas.no_item,
							'nama_item' : result.datas.nama_item,
							'qty_receive' : result.datas.qty_receive,
							'date_receive' : result.datas.date_receive
						});

						canc();
						fillStore();
					}


					// console.log(list);
					// console.log(list[i].id);

				} else {
					canc();
					openErrorGritter('Error', 'Data Tidak Ditemukan');
				}			


			} else {
				canc();
				openErrorGritter('Error', 'Data Tidak Ditemukan');
			}

			$(".modal-backdrop").remove();
		});

	}


	function fillStore(){


		$('input:checkbox').prop('checked', false);
		$('#picked').html(0);

		var body = '';
		var css = 'style="background-color: #000000;"';

		$("#po_body").empty();

		var num = '';
		var index = 0;
		for (var i = 0; i < list.length; i++) {
			var css = 'style="padding: 10px; text-align: center; color: #000000; font-size: 15px;"';
			// var id = 'id="'+list[i].id+'"';

			num++;
			body += '<tr>';
			body += '<input type="hidden" val="'+list[i].id+'"  id="id_'+index+'">';
			body += '<td '+css+'><input type="hidden" value="'+list[i].no_po+'"  id="no_po_'+index+'">'+list[i].no_po+'</td>';
			body += '<td '+css+'><input type="hidden" value="'+list[i].no_item+'"  id="no_item_'+index+'">'+list[i].no_item+'</td>';
			body += '<td '+css+'><input type="hidden" value="'+list[i].nama_item+'"  id="nama_item_'+index+'">'+list[i].nama_item+'</td>';
			body += '<td '+css+'><input type="hidden" value="'+list[i].qty_receive+'"  id="qty_receive_'+index+'">'+list[i].qty_receive+'</td>';
			body += '<td '+css+'><input type="hidden" value="'+list[i].date_receive+'"  id="date_receive_'+index+'">'+list[i].date_receive+'</td>';					
			body += '</tr>';
			index++;

		}
		$("#po_body").append(body);
		$('#count_po').val(index);

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
	}

	function conf() {
		$("#loading").show();

		var arr_params = [];
		var tanggal_terima = $('#tanggal_terima').val();
		var date_terima = $('#date_terima').val();

		for (var i = 0;i < $('#count_po').val(); i++) {
			arr_params.push({
				'no_po' : $('#no_po_'+i).val(), 
				'no_item' : $('#no_item_'+i).val(), 
				'nama_item' : $('#nama_item_'+i).val(),
				'qty_receive' : $('#qty_receive_'+i).val(),
				'date_receive' : $('#date_receive_'+i).val(),
			});
		}

		var data = {
			receive : arr_params,
			tanggal_terima : tanggal_terima,
			date_terima : date_terima

		}

		if(confirm("Apakah anda yakin ingin mencetak bukti penerimaan ini?")){

			$.post('{{ url("fetch/warehouse/create_bukti") }}', data, function(result, status, xhr){
				if (result.status) {
					openSuccessGritter('Success', result.message);

					$("#po_body").empty();
					$('#confirm').hide();
					$("#loading").hide();
					
					list = [];
					fillTableResult();
				}else{
					$("#loading").hide();
					openErrorGritter('Error', result.message);
				}
			});
		}else{
			$("#loading").hide();
		}
		
	}


	function fillTableResult() {
	    $.get('{{ url("index/warehouse/report_bukti") }}', function(result, status, xhr) {

	    if(result.status){
	      $('#tableHasil').DataTable().clear();
	      $('#tableHasil').DataTable().destroy();
	      $('#tableBodyHasil').html("");
	      var tableIsi = "";
	      var count = 1;
	      
	      $.each(result.receive, function(key, value) {

	      	var sub = value.id_print.split("_");

	        tableIsi += '<tr>';
	        tableIsi += '<td width="5%" style="padding:5px">'+ count +'</td>';
	        tableIsi += '<td width="30%" style="padding:5px">'+ sub[0] +' '+ sub[1] +'</td>';
	        tableIsi += '<td><a target="_blank" class="btn btn-danger btn-sm" href="{{ url("index/warehouse/report_bukti") }}/'+value.id_print+'"><i class="fa fa-file-pdf-o"></i> Report Receive</a></td>';

	        tableIsi += '</tr>';
	        count += 1;
	      });

	      $('#tableBodyHasil').append(tableIsi);

	      var table2 = $('#tableHasil').DataTable({
	          'dom': 'Bfrtip',
	          'responsive':true,
	          'lengthMenu': [
	          [ 5, 10, 25, -1 ],
	          [ '5 rows', '10 rows', '25 rows', 'Show all' ]
	          ],
	          'buttons': {
	            buttons:[
	            {
	              extend: 'pageLength',
	              className: 'btn btn-default',
	            },
	            {
	            extend: 'copy',
	            className: 'btn btn-success',
	            text: '<i class="fa fa-copy"></i> Copy',
	            exportOptions: {
	              columns: ':not(.notexport)'
	            }
	            },
	            {
	              extend: 'excel',
	              className: 'btn btn-info',
	              text: '<i class="fa fa-file-excel-o"></i> Excel',
	              exportOptions: {
	                columns: ':not(.notexport)'
	              }
	            },
	            {
	              extend: 'print',
	              className: 'btn btn-warning',
	              text: '<i class="fa fa-print"></i> Print',
	              exportOptions: {
	                columns: ':not(.notexport)'
	              }
	            },
	            ]
	          },
	          'paging': true,
	          'lengthChange': true,
	          'pageLength': 5,
	          'searching': false,
	          'ordering': true,
	          'order': [],
	          'info': true,
	          'autoWidth': true,
	          "sPaginationType": "full_numbers",
	          "bJQueryUI": true,
	          "bAutoWidth": false,
	          "processing": true
	        });
	      }
	      else{
	        alert('Attempt to retrieve data failed');
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