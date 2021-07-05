@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<style type="text/css">
tbody>tr:hover {
	cursor: pointer;
	background-color: #7dfa8c;
}
tbody>tr>td{
	text-align:center;
	padding: 10px 5px 10px 5px;
}
table.table-bordered{
	border:1px solid black;
	vertical-align: middle;
}
table.table-bordered > thead > tr > th{
	border:1px solid black;
	vertical-align: middle;
}
table.table-bordered > tbody > tr > td{
	border:1px solid black;
	vertical-align: middle;
	font-size: 1vw;
	height: 70px;
	padding:  2px 5px 2px 5px;
}
table.table-bordered > tbody > tr:hover {
	cursor: pointer;
	background-color: #7dfa8c;
}
.crop2 {
	overflow: hidden;
}
.crop2 img {
	height: 70px;
	margin: -5% 0 0 0 !important;
}
#loading { display: none; }
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
		<a href="{{ url("/index/ticket/mis") }}" class="btn btn-success pull-right" style="margin-left: 5px; width: 10%;"><i class="fa fa-pencil-square-o"></i> Buat Ticket</a>
		{{-- <a href="{{ url("/index/ticket/borrow") }}" class="btn btn-success pull-right" style="margin-left: 5px; width: 10%;"><i class="fa fa-pencil-square-o"></i> Pinjam Laptop</a> --}}
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
		<div class="col-xs-3" id="ticket_pics">

		</div>
		<div class="col-xs-9">
			<div id="container" style="width: 100%; height: 40vh; margin-bottom: 10px; border: 1px solid black;"></div>
			<table id="ticketTable" class="table table-bordered table-striped table-hover">
				<thead style="background-color: #605ca8; color: white;">
					<tr>
						<th style="width: 0.1%;">ID</th>
						<th style="width: 0.1%;">Department</th>
						<th style="width: 1.2%;">Title</th>
						<th style="width: 1.2%;">Approval</th>
						<th style="width: 0.1%;">Status</th>
						<th style="width: 0.1%;">Progress</th>
					</tr>
				</thead>
				<tbody id="ticketTableBody">
				</tbody>
			</table>
		</div>
	</div>
</section>
@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		fetchMonitoring();
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function detailTicket(ticket_id){
		window.open('{{ url("index/ticket/detail") }}'+'/'+ticket_id, '_blank');
	}

	function fetchMonitoring(){
		var pics = <?php echo json_encode($pics); ?>;

		$('#ticket_pics').html("");
		var ticketPics = "";

		$.each(pics, function(key, value){
			ticketPics += '<div class="box box-widget widget-user-2" style="border: 1px solid black;">';
			ticketPics += '<div class="widget-user-header bg-purple" style="height: 120px;">';
			ticketPics += '<div class="widget-user-image crop2">';
			ticketPics += '<img src="{{ url('images/avatar/') }}'+'/'+value.pic_id+'.jpg'+'" alt="">';
			ticketPics += '<h3 class="widget-user-username">'+value.pic_name+'</h3>';
			ticketPics += '<h5 class="widget-user-desc">'+value.pic_id+' ('+value.pic_position+')</h5>';
			ticketPics += '</div>';
			ticketPics += '</div>';
			ticketPics += '<div class="box-footer no-padding">';
			ticketPics += '<ul class="nav nav-stacked" id="pic_'+value.pic_id+'">';
			ticketPics += '</ul>';
			ticketPics += '</div>';
			ticketPics += '</div>';
		});
		$('#ticket_pics').append(ticketPics);

		$.get('{{ url("fetch/ticket/monitoring") }}', function(result, status, xhr){			
			if(result.status){

				$('#ticketTableBody').html('');
				var ticketTableBody = "";
				var xCategories = [];

				ticket_approvers = result.ticket_approvers;
				approver_count = [];

				ticket_approvers.reduce(function (res, value) {
					if (!res[value.ticket_id]) {
						res[value.ticket_id] = {
							count: 0,
							ticket_id: value.ticket_id
						};
						approver_count.push(res[value.ticket_id])
					}
					res[value.ticket_id].count += 1
					return res;
				}, {});

				$.each(result.tickets, function(key, value){

					if(value.pic_id && (value.status == 'Waiting' || value.status == 'InProgress' || value.status == 'OnHold')){
						var stacked = "";
						stacked += '<li>';
						stacked += '<table style="width: 100%;">';
						stacked += '<tbody>';
						stacked += '<tr onclick="detailTicket(\''+value.ticket_id+'\')">';
						stacked += '<td style="width: 40%; font-weight: bold;">'+value.ticket_id+'</td>';
						if(value.status == 'Waiting'){
							stacked += '<td style="width: 40%; font-weight: bold;"><span class="label" style="color: black; background-color: yellow; border: 1px solid black;">Waiting</span></td>';
						}
						else if(value.status == 'InProgress'){
							stacked += '<td style="width: 40%; font-weight: bold;"><span class="label" style="color: black; background-color: #aee571; border: 1px solid black;">InProgress</span></td>';
						}
						else{
							stacked += '<td style="width: 40%; font-weight: bold;"><span class="label" style="color: black; background-color: #e0e0e0; border: 1px solid black;">OnHold</span></td>';
						}
						stacked += '<td style="width: 20%; font-weight: bold;">'+value.progress+'%</td>';
						stacked += '</tr>';
						stacked += '</tbody>';
						stacked += '</table>';
						stacked += '</li>';
						$('#pic_'+value.pic_id).append(stacked);
					}
					if(value.status != 'Finished' || value.status != 'Rejected'){
						var cnt = 0;
						ticketTableBody += '<tr>';
						ticketTableBody += '<td onclick="detailTicket(\''+value.ticket_id+'\')">'+value.ticket_id+'</td>';
						ticketTableBody += '<td onclick="detailTicket(\''+value.ticket_id+'\')">'+value.department_shortname+'</td>';
						ticketTableBody += '<td onclick="detailTicket(\''+value.ticket_id+'\')">'+value.case_title+'</td>';
						ticketTableBody += '<td>';
						for(var i = 0; i < result.ticket_approvers.length; i++){
							if(result.ticket_approvers[i].ticket_id == value.ticket_id){
								cnt += 1;
								if(result.ticket_approvers[i].status == 'Approved'){
									ticketTableBody += '<span class="label" style="color: black; background-color: #aee571; border: 1px solid black;">'+result.ticket_approvers[i].approver_name+'</span>';
								}
								else{
									ticketTableBody += '<a href="{{ url('approval/ticket') }}/ticket_id='+result.ticket_approvers[i].ticket_id+'&code='+result.ticket_approvers[i].remark+'&approver_id='+result.ticket_approvers[i].approver_id+'" class="label" style="color: black; background-color: #e53935; border: 1px solid black;">'+result.ticket_approvers[i].approver_name+'</a>';
								}
								for(var j = 0; j < approver_count.length; j++){
									if(approver_count[j].ticket_id == result.ticket_approvers[i].ticket_id){
										if(cnt < approver_count[j].count){
											ticketTableBody += '&nbsp;<i class="fa fa-caret-right"></i>&nbsp;';
										}
									}
								}
							}
						}
						ticketTableBody += '</td>';
						if(value.status == 'Approval'){
							ticketTableBody += '<td font-weight: bold;"><span class="label" style="color: black; background-color: white; border: 1px solid black;">'+value.status+'</span></td>';
						}
						else if(value.status == 'Waiting'){
							ticketTableBody += '<td font-weight: bold;"><span class="label" style="color: black; background-color: yellow; border: 1px solid black;">'+value.status+'</span></td>';
						}
						else if(value.status == 'InProgress'){
							ticketTableBody += '<td font-weight: bold;"><span class="label" style="color: black; background-color: #aee571; border: 1px solid black;">'+value.status+'</span></td>';
						}
						else if(value.status == 'OnHold'){
							ticketTableBody += '<td font-weight: bold;"><span class="label" style="color: black; background-color: #e0e0e0; border: 1px solid black;">'+value.status+'</span></td>';
						}
						else{
							ticketTableBody += '<td font-weight: bold;"><span class="label" style="color: black; background-color: #e0e0e0; border: 1px solid black;">'+value.status+'</span></td>';
						}
						ticketTableBody += '<td onclick="detailTicket(\''+value.ticket_id+'\')">'+value.progress+'%</td>';
						ticketTableBody += '</tr>';						
					}
				});

$('#ticketTableBody').append(ticketTableBody);

var approval = [];
var waiting = [];
var inprogress = [];
var onhold = [];
var rejected = [];
var finished = [];


$.each(result.counts, function(key, value){
	if(jQuery.inArray(value.department_shortname, xCategories) === -1){
		xCategories.push(value.department_shortname);
	}
	if(value.status == 'Approval'){
		approval.push(value.cnt);
	}
	if(value.status == 'Waiting'){
		waiting.push(value.cnt);
	}
	if(value.status == 'InProgress'){
		inprogress.push(value.cnt);
	}
	if(value.status == 'OnHold'){
		onhold.push(value.cnt);
	}
	if(value.status == 'Rejected'){
		rejected.push(value.cnt);
	}
	if(value.status == 'Finished'){
		finished.push(value.cnt);
	}
});

Highcharts.chart('container', {
	chart: {

	},
	title: {
		text: 'Ticket Monitoring Chart'
	},
	credits: {
		enabled: false
	},
	xAxis: {
		tickInterval: 1,
		gridLineWidth: 1,
		categories: xCategories,
		crosshair: true
	},
	yAxis: [{
		title: {
			text: ''
		}
	}],
	legend: {
		borderWidth: 1
	},
	tooltip: {
		headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
		pointFormat: '<tr><td style="color:{series.color};padding:0;text-shadow: -1px 0 #909090, 0 1px #909090, 1px 0 #909090, 0 -1px #909090;font-size: 16px;font-weight:bold;">{series.name}: </td>' +
		'<td style="padding:0;font-size:16px;"><b>{point.y:.1f}</b></td></tr>',
		footerFormat: '</table>',
		shared: true,
		useHTML: true
	},
	plotOptions: {
		column: {
			stacking: 'normal',
			pointPadding: 0.93,
			groupPadding: 0.93,
			borderWidth: 0.8,
			borderColor: '#212121'
		}
	},
	series: [{
		name: 'Approval',
		type: 'column',
		stack: 'Stock',
		data: approval,
		color: 'white'
	}, {
		name: 'Waiting',
		type: 'column',
		stack: 'Stock',
		data: waiting,
		color: '#ffeb3b'
	}, {
		name: 'InProgress',
		type: 'column',
		stack: 'Stock',
		data: inprogress,
		color: '#aee571'
	}, {
		name: 'OnHold',
		type: 'column',
		stack: 'Stock',
		data: onhold,
		color: '#e0e0e0'
	}, {
		name: 'Rejected',
		type: 'column',
		stack: 'Stock',
		data: rejected,
		color: '#e53935'
	}, {
		name: 'Finished',
		type: 'column',
		stack: 'Stock',
		data: finished,
		color: '#f9a825'
	}]
});
}
else{
	alert('Unidentified Error '+result.message);
	audio_error.play();
	return false;
}
});
}

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