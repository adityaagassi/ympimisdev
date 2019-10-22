@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
thead>tr>th{
	text-align:center;
}
tbody>tr>td{
	text-align:center;
}
tfoot>tr>th{
	text-align:center;
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
	border:1px solid rgb(211,211,211);
}
table.table-bordered > tfoot > tr > th{
	border:1px solid rgb(211,211,211);
}
#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $page }}<span class="text-purple"> {{ $jpn }}</span>
		{{-- <small>Flute <span class="text-purple"> ??? </span></small> --}}
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">

		<div class="col-xs-12" style="text-align: center;">
			<div class="input-group col-md-12">
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; font-size: 3vw; border-color: red;">
					<i class="glyphicon glyphicon-barcode"></i>
				</div>
				<input type="text" style="text-align: center; border-color: red; font-size: 3vw; height: 70px" class="form-control" id="serialNumber" name="serialNumber" placeholder="Tag Material Number Here..." required>
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; font-size: 3vw; border-color: red;">
					<i class="glyphicon glyphicon-barcode"></i>
				</div>
			</div>
			<br>
		</div>

		<div class="col-xs-12" style="text-align: center;">
			<div class="col-md-6">	<span style="font-size: 24px;">Log Transaction:</span>
				<table id="resultTable" class="table table-bordered table-striped table-hover" style="width: 100%">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 15%">GMC</th>
									<th style="width: 40%">PART</th>
									<th style="width: 10%">QTY</th>
									<th style="width: 35%">In Time</th>
								</tr>
							</thead>
							<tbody id="resultTableBody">
							</tbody>
							
						</table>
			</div>

			<div class="col-md-6" style="padding-top: 9px;"> 
				<span style="font-size: 24px;">Transaction:</span> 
				<table id="datascan" class="table table-bordered table-striped table-hover" style="width: 100%;">
                <thead style="background-color: rgba(126,86,134,.7);">
                 <tr>
                  <th style="width: 5%;">GMC</th>
                  <th style="width: 17%;">PART</th>
                  <th style="width: 6%;">QTY</th>  
                  <th style="width: 6%; ">#</th>                 
                </tr>
            </thead >
              
            <tbody id="datascan2">
            	<tr id="datascan3">
            		<td colspan="3"></td>
            		<td><button class="btn btn-success" onclick="sendPart();">Submit</button></td>
            	</tr>
			</tbody>
            </table>
				
			</div>
		</div>

		<div class="col-xs-12" style="text-align: center;">
			<div class="input-group col-md-12">
				<div class="box box-danger">
					<div class="box-body">
						<table id="returnTable" class="table table-bordered table-striped table-hover" style="width: 100%">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>GMC</th>
									<th>PART</th>
									<th>Stock</th>
									<th>In Part</th>
									<th>Out Part</th>
									<th>Total</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
							<tfoot style="background-color: RGB(252, 248, 227);">
								<tr>
									<th>Total</th>
									<th></th>
									<th style="text-align: center;"></th>
									<th></th>
									<th></th>
									<th></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
		<input type="text" name="gmcPost" id="gmcPost" value="" hidden="">
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

	var counter = 0;
	var arrPart = [];

	jQuery(document).ready(function() {
		fillResult();

      $('body').toggleClass("sidebar-collapse");
		$("#serialNumber").val("");
		$('#serialNumber').focus();
		fetchReturnTable();
		$('#serialNumber').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#serialNumber").val().length == 10){
					scanSerialNumber();
					return false;
				}
				else{
					openErrorGritter('Error!', 'Serial number invalid.');
					audio_error.play();
					$("#serialNumber").val("");
					$("#serialNumber").focus();
				}
			}
		});
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function scanSerialNumber(){


		var serialNumber = $("#serialNumber").val();
		var data = {
			serialNumber : serialNumber,
			
		}
		$.post('{{ url("scan/part_injeksi") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){

		for (var z = 0; z < arrPart.length; z++) {
            if (arrPart[z] == result.part[0].gmc) {
              openErrorGritter("Error!", "Part Already Inserted");
              	$("#serialNumber").val("");
				$("#serialNumber").focus();
              return false;
            }
          }

					$("#serialNumber").val("");
					$("#serialNumber").focus();
					openSuccessGritter('Success!', result.message);
					
					var tableBody = "";
					tableBody += '<tr id="'+counter+'">';
			          tableBody += '<td>'+result.part[0].gmc+'</td>';
			          tableBody += '<td>'+result.part[0].part_name+'</td>';
			          tableBody += '<td> '+result.part[0].capacity+'</td>';
			          tableBody += '<td onclick="deleteRow(this, '+counter+')" style="background-color: red">Cancel</td>';
			          tableBody += '</tr>';

			          counter++;

			          arrPart.push(result.part[0].gmc);
			          console.log(arrPart);
			          $("#gmcPost").val(arrPart);

					// $('#datascan2').append(tableBody);

					$(tableBody).insertBefore("#datascan3");

					$('#returnTable').DataTable().ajax.reload();
				}
				else{
					openErrorGritter('Error!', result.message);
					audio_error.play();
				}
			}
			else{
				alert('Disconnected from server');
			}
		});
	}

	function deleteRow(elem,i)
	{	
	var gmc = $("#"+i+" td:nth-child(1)").text();
	arrPart.splice(parseInt(arrPart.indexOf(gmc)),1);	
	$(elem).parent('tr').remove();
	console.log(arrPart);
	$("#gmcPost").val(arrPart);
	}

	function sendPart() {

		var gmc = $('#gmcPost').val();
	
		var data = {
			gmc:arrPart,
			process:'OUT'
			
		}
		$.get('{{ url("send/Part") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){

					$("#datascan2").empty();

					$("#serialNumber").val("");
					$("#serialNumber").focus();
					openSuccessGritter('Success!', result.message);
					arrPart = [];
					fillResult();
					fetchReturnTable();
					var tableBody = "";
					tableBody += '<tr id="datascan3">';
			        tableBody += '<td colspan="3"></td>';
			        tableBody += '<td><button class="btn btn-success" onclick="sendPart();">Submit</button></td>';
			        tableBody += '</tr>';
			        $('#datascan2').append(tableBody);

				}
				else{
					audio_error.play();
					
				}
			}
			else{
				audio_error.play();
				alert('Disconnected from sever');
			}
		});
	
	}

	function fetchReturnTable(){
		$('#returnTable').DataTable().destroy();
		$('#returnTable').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
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
			"footerCallback": function (tfoot, data, start, end, display) {
				var intVal = function ( i ) {
					return typeof i === 'string' ?
					i.replace(/[\$%,]/g, '')*1 :
					typeof i === 'number' ?
					i : 0;
				};
				var api = this.api();
				var stock = api.column(2, { page: 'current'}).data().reduce(function (a, b) {
					return intVal(a)+intVal(b);
				}, 0)
				$(api.column(2).footer()).html(stock.toLocaleString());

				var inq = api.column(3, { page: 'current'}).data().reduce(function (a, b) {
					return intVal(a)+intVal(b);
				}, 0)
				$(api.column(3).footer()).html(inq.toLocaleString());

				var outq = api.column(4, { page: 'current'}).data().reduce(function (a, b) {
					return intVal(a)+intVal(b);
				}, 0)
				$(api.column(4).footer()).html(outq.toLocaleString());

				var all = api.column(5, { page: 'current'}).data().reduce(function (a, b) {
					return intVal(a)+intVal(b);
				}, 0)
				$(api.column(5).footer()).html(all.toLocaleString());
			},
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'language': { 'search': "" },
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/InOutpart") }}",
			},
			"columns": [
			{ "data": "gmc" },
			{ "data": "part_name" },
			{ "data": "stock" },
			{ "data": "stock_in" },
			{ "data": "stock_out" },
			{ "data": "total" }
			]
		});
	}

	function fillResult(){
		var data = {
			proces:'OUT'
		}
		$.get('{{ url("get/Inpart") }}',data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			$('#resultTable').DataTable().destroy();
			if(xhr.status == 200){
				if(result.status){
					$('#resultTableBody').html("");
					var resultData = '';
					$.each(result.part, function(key, value){
						resultData += '<tr>';
						resultData += '<td>'+ value.gmc +'</td>';
						resultData += '<td>'+ value.part +'</td>';
						resultData += '<td>'+ value.total +'</td>';
						resultData += '<td>'+ value.tgl_in +'</td>';
						
						resultData += '</tr>';
					});
					$('#resultTableBody').append(resultData);
					$('#resultTable').DataTable({
						"sDom": '<"top"i>rt<"bottom"flp><"clear">',
						'paging'      	: true,
						'lengthChange'	: false,
						'searching'   	: true,
						'ordering'		: false,
						'info'       	: false,
						'autoWidth'		: false,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						
					});
				}
				else{
					audio_error.play();
					alert('Attempt to retrieve data');
				}
			}
			else{
				audio_error.play();
				alert('Disconnected from server');
			}
		});
	}

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