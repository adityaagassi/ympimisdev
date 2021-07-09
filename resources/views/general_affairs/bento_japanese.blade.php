@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("bower_components/fullcalendar/dist/fullcalendar.min.css")}}">
<link rel="stylesheet" href="{{ url("bower_components/fullcalendar/dist/fullcalendar.print.min.css")}}" media="print">
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
	font-size: 0.8vw;
	border:1px solid black;
	padding-top: 5px;
	padding-bottom: 5px;
	vertical-align: middle;
}
table.table-bordered > tbody > tr > td{
	border:1px solid black;
	padding-top: 5px;
	padding-bottom: 5px;
	padding-left: 2px;
	padding-right: 2px;
	vertical-align: middle;
}
table.table-bordered > tfoot > tr > th{
	font-size: 0.8vw;
	border:1px solid black;
	padding-top: 0;
	padding-bottom: 0;
	vertical-align: middle;
}

input[type=checkbox] {
	transform: scale(1.7);
	cursor: pointer;
}

#loading, #error { display: none; }
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{-- <img src="{{ asset('images/flag/jp.png') }}" style="height: 30px; border: 1px solid black;">  --}}
		{{ $title_jp }}
		{{-- <small><span class="text-purple">{{ $title_jp }}</span></small> --}}
	</h1>
</section>
@endsection

@section('content')
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-header">
					<div class="input-group">
						<span class="input-group-addon" style="background-color: yellow; font-weight: bold;">月選択</span>
						<input type="text" class="form-control datepicker" id="selectMonth" placeholder="Select Month" onchange="fetchOrder();" value="{{ $mon }}" style="width: 10%;">
					</div>
					<center>
						<span style="font-weight: bold; font-size: 2vw;" id="period"></span>
					</center>
				</div>
				<div class="box-body">
					<table class="table table-hover table-bordered" id="tableOrder" style="margin-bottom: 20px;">
					</table>
					{{-- <button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 30%;">RESET<br>??</button> --}}
					{{-- <button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;" onclick="saveOrder()">SAVE<br>??</button> --}}

					<center>
						{{-- <span style="font-weight: bold; font-size: 1.1vw;">*Note 備考:</span><br> --}}
						<span style="background-color: white; color: black; font-weight: bold; font-size: 1.1vw; border: 1px solid black;">&nbsp;&nbsp;注文なし&nbsp;&nbsp;</span>
						<span style="background-color: yellow; color: black; font-weight: bold; font-size: 1.1vw; border: 1px solid black;">&nbsp;&nbsp;待機中&nbsp;&nbsp;</span>
						<span style="background-color: #ccff90; color: black; font-weight: bold; font-size: 1.1vw; border: 1px solid black;">&nbsp;&nbsp;承認済み&nbsp;&nbsp;</span>
						{{-- <span style="background-color: #ff6090; color: black; font-weight: bold; font-size: 1.1vw; border: 1px solid black;">&nbsp;&nbsp;却下&nbsp;&nbsp;</span> --}}
						{{-- <span style="background-color: black; color: white; font-weight: bold; font-size: 1.1vw; border: 1px solid black;">&nbsp;&nbsp;取消済み&nbsp;&nbsp;</span> --}}
						{{-- <span style="background-color: #ffee58; font-weight: bold; font-size: 1.1vw; border: 1px solid black;">&nbsp;&nbsp;改訂１&nbsp;&nbsp;</span> --}}
						{{-- <span style="background-color: #29b6f6; font-weight: bold; font-size: 1.1vw; border: 1px solid black;">&nbsp;&nbsp;改訂２以降&nbsp;&nbsp;</span> --}}
					</center>
					@if(Auth::user()->role_code == 'GA' || Auth::user()->role_code == 'MIS')
					<button class="btn btn-success" style="width: 100%; font-weight: bold; font-size: 2vw; margin-top: 20px;" onclick="approveOrder()">
						CONFIRM ORDER
					</button>
					@endif
				</div>
			</div>	
		</div>
	</div>
</section>

<div class="modal fade" id="modalLocation" data-backdrop="static">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-6">
					{{-- <img src="{{ asset('images/bg2.jpg') }}" style="height: 170px; border: 1px solid black;"> --}}
					<button style="border: 1px solid black; height: 170px; font-size: 3vw; font-weight: bold; width: 100%;" onclick="confirmOrder(id)" id="YEMI">YEMI</button>
				</div>
				<div class="col-xs-6">
					<button class="btn" style="border: 1px solid black; height: 170px; font-size: 3vw; font-weight: bold; width: 100%;" onclick="confirmOrder(id)" id="YMPI">YMPI</button>
				</div>
			</div>
		</div>
	</div>
</div>

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
		fetchOrder();
		$('#selectMonth').datepicker({
			autoclose: true,
			format: "MM yyyy",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var createOrder = "";
	var createEmployeeId = "";
	var createDueDate = "";
	var createLocation = "";
	var createEmail = "";
	var createEmployeeName = "";

	function approveOrder(){
		if(confirm("Apakah anda yakin akan mengkonfirmasi semua order?")){
			$('#loading').show();
			var month = $('#selectMonth').val();
			var data = {
				month:month
			}			
			$.post('{{ url("approve/ga_control/bento_japanese") }}', data, function(result, status, xhr){
				if(result.status){
					fetchOrder();
					$('#loading').hide();
				}
				else{
					openErrorGritter(result.message);
					$('#loading').hide();
					audio_error.play();
					return false;
				}
			});
		}
		else{
			return false;
		}
	}

	function confirmOrder(id){
		createLocation = id;
		var data = {
			order:createOrder,
			employee_id:createEmployeeId,
			due_date:createDueDate,
			location:createLocation,
			employee_name:createEmployeeName,
			email:createEmail
		}
		$.post('{{ url("input/ga_control/bento_japanese") }}', data, function(result, status, xhr){
			if(result.status){
				$('#modalLocation').modal('hide');
				// openSuccessGritter(result.message);
				fetchOrder();
				return false;
			}
			else{
				openErrorGritter(result.message);
				audio_error.play();
				return false;
			}
		});
	}

	function editOrder(val, id){
		console.log(val);
		if ($('#'+id).is(":checked")){
			createOrder = 'true';
		}
		else{
			createOrder = 'false';
		}

		str = val.split('~');
		createEmployeeId = str[0];
		createDueDate = str[1];
		createEmployeeName = str[3];
		createEmail = str[4];

		if(str[2] == 'BOTH'){
			if(createOrder == 'true'){
				$('#modalLocation').modal('show');	
			}
			else{
				confirmOrder(str[2]);				
			}
		}
		else{
			confirmOrder(str[2]);
		}
	}

	function fetchOrder(){
		var month = $('#selectMonth').val();
		var data = {
			month:month
		}
		$.get('{{ url("fetch/ga_control/bento_japanese") }}', data, function(result, status, xhr){
			if(result.status){
				$('#period').text(result.y+"年 "+result.m+"月");
				$('#tableOrder').html("");
				var tableOrder = "";	
				var employee_id = [];
				var username = "{{ Auth::user()->username }}";
				var ins = false;

				tableOrder += '<thead style="background-color: #63ccff;">';
				tableOrder += '<tr>';
				tableOrder += '<th style="width: 8%; font-size: 1.05vw;">氏名</th>';
				$.each(result.calendars, function(key, value){
					if(value.remark == 'H'){
						tableOrder += '<th style="vertical-align: top; width: 1%; background-color: grey; font-size: 1.05vw;">'+value.header+'<br>休日</th>';
					}
					else{
						tableOrder += '<th style="vertical-align: top; width: 1%; font-size: 1.05vw;">'+value.header+'</th>';
					}
				});
				tableOrder += '</thead>';
				tableOrder += '<tbody>';
				tableOrder += '</tr>';
				var cnt = 1;
				$.each(result.japaneses, function(key, value){
					tableOrder += '<tr>';
					if(value.employee_id == username){
						tableOrder += '<td style="font-weight: bold; background-color: yellow; font-size: 1.1vw;">'+value.employee_name+'<br>'+value.employee_name_jp+'</td>';
					}
					else{
						tableOrder += '<td>'+value.employee_name+'<br>'+value.employee_name_jp+'</td>';
					}
					for(var i = 0; i < result.calendars.length; i++){
						var check = '';
						var startDate =  new Date();
						var endDate = new Date(result.calendars[i].due_date);
						if(value.employee_id != username){
							check = 'disabled';
						}
						if("{{ Auth::user()->role_code }}" == 'MIS' || "{{ Auth::user()->role_code }}" == 'GA'){
							check = '';
						}
						if(endDate < startDate || result.calendars[i].menu == null){
							check = 'disabled';
						}
						ins = false;
						for(var j = 0; j < result.bento_lists.length; j++){
							if(result.calendars[i].due_date == result.bento_lists[j].due_date && value.employee_id == result.bento_lists[j].employee_id && result.calendars[i].remark != 'H' && result.bento_lists[j].status != 'Cancelled' && result.bento_lists[j].status != 'Rejected'){
								var color = "";
								if(result.bento_lists[j].status == 'Approved'){
									color = 'background-color: #ccff90;';
								}
								// if(result.bento_lists[j].revise == 1){
								// 	color = 'background-color: #ffee58;';
								// }
								// if(result.bento_lists[j].revise >= 2){
								// 	color = 'background-color: #29b6f6;';
								// }
								if(result.bento_lists[j].status == 'Waiting'){
									color = 'background-color: yellow;';
								}
								tableOrder += '<td style="'+color+'">';
								tableOrder += '<input type="checkbox" onchange="editOrder(value, id);" value="'+value.employee_id+'~'+result.calendars[i].due_date+'~'+value.location+'" id="cb_'+cnt+'" checked '+check+'>';
								if(value.location == 'BOTH'){
									tableOrder += '<br><span style="font-weight: bold; font-size: 0.8vw;">'+result.bento_lists[j].location+'</span>';
								}
								tableOrder += '</td>';
								ins = true;
							}
						}
						if(ins == false && result.calendars[i].remark != 'H'){
							tableOrder += '<td style=""><input type="checkbox" onchange="editOrder(value, id);" value="'+value.employee_id+'~'+result.calendars[i].due_date+'~'+value.location+'~'+value.employee_name+'~'+value.email+'" id="cb_'+cnt+'" '+check+'></td>';
						}
						if(ins == false && result.calendars[i].remark == 'H'){
							tableOrder += '<td style="background-color: grey;"></td>';							
						}
						cnt += 1;
					}
					tableOrder += '</tr>';
				});
				tableOrder += '</tbody>';

				$('#tableOrder').append(tableOrder);
			}
			else{
				openErrorGritter(result.message);
				audio_error.play();
				return false;
			}
		});
	}

	function truncate(str, n){
		return (str.length > n) ? str.substr(0, n-1) + '&hellip;' : str;
	};

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '5000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '5000'
		});
	}
</script>

@endsection