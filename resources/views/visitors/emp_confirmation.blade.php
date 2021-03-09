@extends('layouts.display')
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
		{{ $page }}<span class="text-purple"> {{ $title_jp }}</span>
	</h1>
</section>
@stop
@section('content')
<section class="content" >
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please Wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12" style="text-align: center;padding-bottom: 10px">
			<div class="row">
				<div class="col-xs-8 col-xs-offset-1" style="padding-right: 0px;padding-left: 0px">
					<input type="text" id="tag" placeholder="Scan ID Card Here . . ." style="width: 100%;font-size: 20px;text-align:center;padding: 10px">
				</div>
				<div class="col-xs-1" style="padding-right: 0px;padding-left: 0px">
					<button class="btn btn-danger" onclick="cancel()" style="width:100%;font-size: 20px;height:50px;font-weight: bold;">
						CLEAR
					</button>
				</div>
				<div class="col-xs-1" style="padding-right: 0px;padding-left: 0px">
					<button class="btn btn-warning" onclick="location.reload()" style="width:100%;font-size: 20px;height:50px;font-weight: bold;">
						REFRESH
					</button>
				</div>
			</div>
			<div class="row" style="padding-top: 10px">
				<!-- <div class="col-xs-2 col-xs-offset-1" style="padding-right: 0px;padding-left: 0px">
					<input type="text" id="employee_id" placeholder="-" style="width: 100%;font-size: 20px;text-align:center;padding: 5px" disabled>
				</div> -->
				<div class="col-xs-8 col-xs-offset-1" style="padding-right: 0px;padding-left: 0px">
					<input type="text" id="employee_id" placeholder="-" style="width: 100%;font-size: 20px;text-align:center;padding: 10px" disabled>
				</div>
				<div class="col-xs-2" style="padding-right: 0px;padding-left: 0px">
					<button class="btn btn-success" onclick="cancel()" style="width:100%;font-size: 20px;height:50px;font-weight: bold;">
						FINISH
					</button>
				</div>
			</div>
		</div>

		<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px">
			<div class="col-md-12">
				<div class="box box-solid">
					<div class="box-body">
						<center><span style="font-size: 25px;text-align: center;font-weight: bold;">VISITOR LISTS</span> </center>
						<table id="tableVisitor" class="table table-bordered table-striped table-hover" style="width: 100%">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 2%">Incoming Date</th>
									<th style="width: 1%">Employee</th>
									<th style="width: 2%">Dept</th>
									<th style="width: 1%">Company</th>
									<th style="width: 1%">Name</th>
									<th style="width: 1%">Total</th>
									<th style="width: 1%">Purpose</th>
									<th style="width: 1%">Status</th>
									<th style="width: 1%">Remark</th>
									<th style="width: 1%">Confirmation</th>
								</tr>
							</thead>
							<tbody id="tableVisitorBody">
							</tbody>
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

	var counter = 0;
	var arrPart = [];
	var intervalCancel;

	jQuery(document).ready(function() {
      	$('body').toggleClass("sidebar-collapse");
      	cancel();
      	// intervalCancel = setInterval(cancel,10000);
	});

	$('#tag').keyup(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			fillTable();
			clearInterval(intervalCancel);
		}
	});

	function fillTable() {
		var data = {
			tag:$('#tag').val()
		}
		$.get('{{ url("fetch/visitor/emp_confirmation") }}', data, function(result, status, xhr){
			if(result.status){
				$('#employee_id').val(result.emp.employee_id +" - "+ result.emp.name);
				$('#tag').prop('disabled',true);

				$('#tableVisitor').DataTable().clear();
				$('#tableVisitor').DataTable().destroy();

				$('#tableVisitorBody').html("");
				var tableVisitor = "";

				$.each(result.visitor, function(key, value){
					if (value.remark == "") {
						var bg = "background-color: rgb(204, 255, 255);";
					}else{
						var bg = "background-color: rgb(255, 204, 255);";
					}
					tableVisitor += "<tr>";
					tableVisitor += "<td>"+value.created_at+"</td>";
					tableVisitor += "<td>"+value.name+"</td>";
					tableVisitor += "<td>"+value.department+"</td>";
					tableVisitor += "<td>"+value.company+"</td>";
					tableVisitor += "<td>"+value.full_name+"</td>";
					tableVisitor += "<td>"+value.total1+" Orang</td>";
					tableVisitor += "<td>"+value.purpose+"</td>";
					tableVisitor += "<td>"+value.status+"</td>";
					if (value.remark == null) {
						tableVisitor += '<td style="'+bg+'"></td>';
						tableVisitor += '<td><a href="javascript:void(0)" data-toggle="modal-default" class="btn btn-sm btn-success" onClick="confirmDitemui(id)" id="'+ value.id + '">Sudah Ditemui</a><a href="javascript:void(0)" data-toggle="modal-default" class="btn btn-sm btn-danger" onClick="confirmBelumDitemui(id)" id="'+ value.id + '">Belum Ditemui</a></td>';
					}
					else{
						tableVisitor += '<td style="'+bg+'">'+ value.remark +'</td>';
					}
					tableVisitor += "</tr>";
				});

				$('#tableVisitorBody').append(tableVisitor);

				var table = $('#tableVisitor').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching': true	,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});
			}else{
				cancel();
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function cancel(){
		$("#tag").removeAttr('disabled');
		$("#tag").val("");
		$("#employee_id").val("-");
		$("#name").val("-");
		$("#tag").focus();
		$('#tableVisitorBody').html("");
		intervalCancel = setInterval(cancel,60000);
	}

	function cancelTag(){
		location.reload();
	}

	function confirmDitemui(id) {
		var id = id;
		var remark = 'Sudah Ditemui';
		var data = {
			id:id,
			remark:remark
		}
		$.post('{{ url("visitor_updateremarkall") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					fillTable();						
					openSuccessGritter('Success!', result.message);
				}
				else{
					openErrorGritter('Error!', result.message);
				}
			}
			else{
				alert("Disconnected from server");
			}
		});
	}

	function confirmBelumDitemui(id) {
		var id = id;
		var remark = 'Belum Ditemui';
		var data = {
			id:id,
			remark:remark
		}
		$.post('{{ url("visitor_updateremarkall") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					fillTable();						
					openSuccessGritter('Success!', result.message);
				}
				else{
					openErrorGritter('Error!', result.message);
				}
			}
			else{
				alert("Disconnected from server");
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