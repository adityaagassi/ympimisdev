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
		<a href="{{ url("/index/ticket/mis") }}" class="btn btn-success pull-right" style="margin-left: 5px; width: 10%;"><i class="fa fa-pencil-square-o"></i> Create Ticket</a>
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
		<div class="xol-xs-8">
			<center>
				<span style="font-weight: bold; color: purple; font-size: 18px;">MIS TICKETING SYSTEM</span><br>
				<span style="font-weight: bold; font-size: 16px;">{{ $data['ticket']->ticket_id }}</span><span style="font-weight: bold; font-size: 16px; color: green;"> (Fully Approved)</span>
			</center>
			<br>
			<table style="width: 100%;">
				<tbody>
					<tr>
						<td style="padding: 0; vertical-align: top; width: 3%;">
							<table>
								<tbody>
									<tr>
										<td style="font-weight: bold;">Pemohon</td>
										<td>:</td>
										<td>{{ $data['ticket']->user->username }} - {{ $data['ticket']->user->name }}</td>
									</tr>
									<tr>
										<td style="font-weight: bold;">Department</td>
										<td>:</td>
										<td>{{ $data['ticket']->department }}</td>
									</tr>
								</tbody>
							</table>
						</td>
						<td style="padding: 0; vertical-align: top; text-align: right; width: 1%;">
							<table>
								<tbody>
									<tr>
										<td style="font-weight: bold;">No Dok.</td>
										<td style="font-weight: bold;">:</td>
										<td>YMPI/MIS/FM/002</td>
									</tr>
									<tr>
										<td style="font-weight: bold;">No Rev.</td>
										<td>:</td>
										<td>01</td>
									</tr>
									<tr>
										<td style="font-weight: bold;">Tanggal</td>
										<td style="font-weight: bold;">:</td>
										<td>08 April 2021</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<br>
			<table>
				<tbody>
					<tr>
						<td style="font-weight: bold;">Jenis Permintaan</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ $data['ticket']->category }}</td>
					</tr>
					<tr>
						<td style="font-weight: bold;">Prioritas</td>
						<td style="font-weight: bold;">:</td>
						<td>
							@if($data['ticket']->priority == 'Very High')
							<span style="background-color: #ff6090; font-weight: bold;">&nbsp;&nbsp;&nbsp;{{ $data['ticket']->priority }}&nbsp;&nbsp;&nbsp;</span>
							@elseif($data['ticket']->priority == 'High')
							<span style="background-color: #ffee58; font-weight: bold;">&nbsp;&nbsp;&nbsp;{{ $data['ticket']->priority }}&nbsp;&nbsp;&nbsp;</span>
							@else
							<span style="background-color: #ccff90; font-weight: bold;">&nbsp;&nbsp;&nbsp;{{ $data['ticket']->priority }}&nbsp;&nbsp;&nbsp;</span>
							@endif
						</td>
					</tr>
					@if($data['ticket']->priority == 'High' || $data['ticket']->priority == 'Very High')
					<tr>
						<td style="font-weight: bold;">Alasan Prioritas</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ $data['ticket']->priority_reason }}</td>
					</tr>
					@endif
					<tr>
						<td style="font-weight: bold;">Judul</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ $data['ticket']->case_title }}</td>
					</tr>
					<tr>
						<td style="font-weight: bold;">Penjelasan</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ $data['ticket']->case_description }}</td>
					</tr>
					<tr>
						<td style="font-weight: bold;">Permintaan Pengerjaan</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ date('d F Y', strtotime($data['ticket']->due_date_from)) }} s/d {{ date('d F Y', strtotime($data['ticket']->due_date_to)) }}</td>
					</tr>
					@if($data['ticket']->document != '-')
					<tr>
						<td style="font-weight: bold;">Digitalisasi Dokumen</td>
						<td style="font-weight: bold;">:</td>
						<td>{{ $data['ticket']->document }}</td>
					</tr>
					@endif
				</tbody>
			</table>
			<br>
			<table style="border:1px solid black;border-collapse: collapse;" width="100%">
				<thead>
					<tr>
						<th style="border:1px solid black; width: 50%;">Kondisi Sekarang</th>
						<th style="border:1px solid black; width: 50%;">Kondisi Yang Diharapkan</th>						
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="border:1px solid black; vertical-align: top;"><?= $data['ticket']->case_before ?></td>
						<td style="border:1px solid black; vertical-align: top;"><?= $data['ticket']->case_after ?></td>
					</tr>
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold;">TARGET</span> 
			<table style="border:1px solid black; border-collapse: collapse;" width="100%">
				<thead>
					<tr>
						<th style="border:1px solid black; width: 1%;">Kategori</th>
						<th style="border:1px solid black; width: 6%;">Penjelasan</th>
						<th style="border:1px solid black; width: 1%;">Nominal (USD)</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$total_amount = 0;
					for ($i=0; $i < count($data['costdown']); $i++) { 
						print_r('<tr>');
						print_r('<td style="border: 1px solid black;">'.$data['costdown'][$i]['category'].'</td>');
						print_r('<td style="border: 1px solid black;">'.$data['costdown'][$i]['cost_description'].'</td>');
						print_r('<td style="border: 1px solid black; text-align: right;">'.$data['costdown'][$i]['cost_amount'].'</td>');
						print_r('</tr>');
						$total_amount += $data['costdown'][$i]['cost_amount'];
					}?>
				</tbody>
				<tfoot>
					<tr>
						<th style="border: 1px solid black;">Total</th>
						<th style="border: 1px solid black;"></th>
						<th style="border: 1px solid black; text-align: right;"><?php echo($total_amount) ?></th>
					</tr>
				</tfoot>
			</table>
			<br>
			<table style="border: 1px solid black; border-collapse: collapse; width: 60%;" align="right">
				<thead>
					<tr>
						<?php
						for ($i=0; $i < count($data['approver']); $i++) {
							print_r('<th style="border: 1px solid black; width: 1%;">'.$data['approver'][$i]['remark'].'</th>');
						}?>
					</tr>
					<tr>
						<?php
						for ($i=0; $i < count($data['approver']); $i++) {
							print_r('<th style="border: 1px solid black; width: 1%; height: 50px;">'.$data['approver'][$i]['status'].'<br>'.$data['approver'][$i]['approved_at'].'</th>');
						}?>
					</tr>
					<tr>
						<?php
						for ($i=0; $i < count($data['approver']); $i++) {
							print_r('<th style="border: 1px solid black; width: 1%;">'.$data['approver'][$i]['approver_name'].'</th>');
						}?>
					</tr>
				</thead>
			</table>
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

	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

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