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
		{{ $page }} - {{ $status }}<span class="text-purple"> {{ $title_jp }}</span>
		{{-- <small>Flute <span class="text-purple"> ??? </span></small> --}}
	</h1>
</section>
@stop
@section('content')
<section class="content" >
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-6" style="text-align: center;">
			<div class="input-group col-md-12">
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; font-size: 2vw; border-color: red;">
					<i class="glyphicon glyphicon-barcode"></i>
				</div>
				<input type="text" style="text-align: center; border-color: red; font-size: 2vw; height: 50px" class="form-control" id="tag_product" name="tag_product" placeholder="Scan Tag Here ..." required>
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; font-size: 2vw; border-color: red;">
					<i class="glyphicon glyphicon-barcode"></i>
				</div>
			</div>
			<div class="col-md-12" style="padding-top: 20px;">
				<span style="font-size: 24px;">Transaction:</span> 
				<table id="resultScan" class="table table-bordered table-striped table-hover" style="width: 100%;">
		            <thead style="background-color: rgba(126,86,134,.7);">
		                <tr>
		                  <th style="width: 5%;">Material Number</th>
		                  <th style="width: 17%;">Part Name</th>
		                  <th style="width: 17%;">Part Type</th>
		                  <th style="width: 17%;">Color</th>
		                  <th style="width: 6%;">Qty</th>
		                  <th style="width: 6%;">Status</th>
		                </tr>
		            </thead >
		              
		            <tbody id="resultScanBody">
					</tbody>
	            </table>
			</div>
		</div>

		<div class="col-xs-6" style="padding-left: 0px">
			<div class="col-md-12">
				<div class="box box-solid">
					<div class="box-body">
						<span style="font-size: 20px;text-align: left;">Transaction History</span> 
						<table id="tableHistory" class="table table-bordered table-striped table-hover" style="width: 100%">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>Part Name</th>
									<th>Part Code - Color</th>
									<th>Qty</th>
									<th>Loc</th>
									<th>Created At</th>
								</tr>
							</thead>
							<tbody id="tableHistoryBody">
							</tbody>
							<tfoot style="background-color: RGB(252, 248, 227);">
								<tr>
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
		$("#tag_product").val("");
		$('#tag_product').focus();
	});

	$('#tag_product').keyup(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag_product").val().length >= 7){
				var data = {
					tag : $("#tag_product").val(),
					status : '{{$status}}',
				}

				var bodyScan = "";
				$('#resultScanBody').html("");
				var statustransaction = '{{$status}}';

				$.get('{{ url("scan/tag_product") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', 'Scan Tag Success');
						$('#tag_product').prop('disabled',true);
						bodyScan += '<tr>';
						bodyScan += '<td id="material_number">'+result.data.material_number+'</td>';
						bodyScan += '<td id="part_name">'+result.data.part_name+'</td>';
						bodyScan += '<td id="part_type">'+result.data.part_type+'</td>';
						bodyScan += '<td id="color">'+result.data.color+'</td>';
						bodyScan += '<td id="qty">'+result.data.shot+'</td>';
						bodyScan += '<td id="status">'+statustransaction+'</td>';
						bodyScan += '<tr>';
						bodyScan += '<tr>';
						bodyScan += '<td colspan="6" style="padding:10px"><button class="btn btn-danger pull-left" onclick="cancel()">CANCEL</button><button class="btn btn-success pull-right" onclick="completion()">SUBMIT</button></td>';
						bodyScan += '<tr>';

						$('#resultScanBody').append(bodyScan);
					}
					else{
						openErrorGritter('Error!', 'Tag Invalid');
						audio_error.play();
						$("#tag_product").val("");
						$("#tag_product").focus();
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Tag Invalid');
				audio_error.play();
				$("#tag_product").val("");
				$("#tag_product").focus();
			}			
		}
	});

	function completion() {
		$('#loading').show();
		var data = {
			tag:$('#tag_product').val(),
			material_number:$('#material_number').text(),
			part_name:$('#part_name').text(),
			part_type:$('#part_type').text(),
			color:$('#color').text(),
			qty:$('#qty').text(),
			status:$('#status').text(),
		}

		$.post('{{ url("index/injection/completion") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', 'Transaction Success');
				$('#loading').hide();
				$('#resultScanBody').html("");
				fillResult();
				$('#tag_product').removeAttr("disabled");
				$("#tag_product").val("");
				$("#tag_product").focus();
			}
			else{
				openErrorGritter('Error!', 'Upload Failed.');
				audio_error.play();
			}
		});
	}

	function cancel(){
		$('#resultScanBody').html("");
		$('#tag_product').removeAttr("disabled");
		$('#tag_product').val("");
		$('#tag_product').focus();
	}

	function fillResult() {
		var data = {
			status:'{{$status}}'
		}
		$.get('{{ url("fetch/injection/transaction") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableHistory').DataTable().clear();
				$('#tableHistory').DataTable().destroy();
				$('#tableHistoryBody').html("");
				var tableData = "";
				if (result.data.length > 0) {
					$.each(result.data, function(key, value) {
						tableData += '<tr>';
						tableData += '<td>'+ value.part_name +'</td>';
						tableData += '<td>'+ value.part_code +' - '+ value.color +'</td>';
						tableData += '<td>'+ value.quantity +'</td>';
						tableData += '<td>'+ value.location +'</td>';
						tableData += '<td>'+ value.created_at +'</td>';
						tableData += '</tr>';
					});
				}
				$('#tableHistoryBody').append(tableData);

				$('#tableHistory tfoot th').each(function(){
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				});
				
				var table = $('#tableHistory').DataTable({
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
					}
				});
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

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