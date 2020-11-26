<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		td{
			padding-right: 5px;
			padding-left: 5px;
			padding-top: 0px;
			padding-bottom: 0px;
		}
		th{
			padding-right: 5px;
			padding-left: 5px;			
		}
	</style>
</head>
<body>
	@foreach($data as $row)
	<?php $cat = $row->cat ?>
	<?php $department = $row->department ?>
	<?php $vendor = $row->vendor ?>
	<?php $description = $row->description ?>
	<?php $valid_from = $row->valid_from ?>
	<?php $valid_to = $row->valid_to ?>
	<?php $total_validity = $row->total_validity ?>
	<?php $status = $row->status ?>
	<?php $remark = $row->remark ?>
	<?php $created_by = $row->created_by ?>
	<?php $name = $row->name ?>
	<?php $created_at = $row->updated_at ?>
	@endforeach
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			@if($cat == 'new_agreement')
			<h2>A New Agreement Has Been Made</h2>
			@elseif($cat == 'update_agreement')
			<h2>An Agreement Has Been Updated</h2>
			@elseif($cat == 'notif_agreement')
			<h2>An Agreement Will Expire Soon</h2>
			@endif
			This is an automatic notification. Please do not reply to this address.<br>
			自動通知です。返事しないでください。
			<h3 style="font-size: 18px;">{{ $vendor }}</h3>
			<p style="font-size: 18px;">{{ $description }}</p>
			<h3>Validity Period:</h3>
			<p style="font-size: 18px;">{{ date('d F Y', strtotime($valid_from)) }} to {{ date('d F Y', strtotime($valid_to)) }}<br>
			{{ $total_validity }} Day(s)</p>
			<h3>PIC:</h3>
			<p style="font-size: 18px;">{{ $department }}<br>
			{{ $created_by }} - {{ $name }}</p>
			<br>
			<br>
			@if($cat == 'notif_agreement')
			<p style="font-size: 18px;">Please check and decide whether the agreement will be renewed or terminated.h</p>
			<a href="{{url('index/general/agreement')}}">Click This To Update Agreements</a><br>
			@else
			<a href="{{url('index/general/agreement')}}">Click This To Check Agreements</a><br>
			@endif
		</center>
	</div>
</body>
</html>