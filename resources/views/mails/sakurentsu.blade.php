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
	<div>
		@foreach($data as $datas)
			<?php $id = $datas->id ?>
			<?php $sakurentsu_number = $datas->sakurentsu_number ?>
			<?php $applicant = $datas->applicant ?>
			<?php $title_jp = $datas->title_jp ?>
			<?php $target_date = $datas->target_date ?>
			<?php $translator = $datas->translator ?>
			<?php $position = $datas->position ?>
			<?php $status = $datas->status ?>
		@endforeach

		@if($position == "interpreter")

		<h3>Dear Interpreter</h3>

		<p style="font-size: 20px;">A New Sakurentsu Has Been Created<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.
		<br>
		<table style="border:1px solid black; border-collapse: collapse;" width="70%">
			<thead style="background-color: rgb(126,86,134);">
				<tr>
					<th style="width: 2%; border:1px solid black;">Point</th>
					<th style="width: 2%; border:1px solid black;">Content</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="width: 2%; border:1px solid black;">Sakuretsu Number</td>
					<td style="border:1px solid black; text-align: center;">{{$sakurentsu_number}}</td>
				</tr>
				<tr>
					<td style="width: 2%; border:1px solid black;">Title</td>
					<td style="border:1px solid black; text-align: center;">{{$title_jp}}</td>
				</tr>
				<tr>
					<td style="width: 2%; border:1px solid black;">Applicant</td>
					<td style="border:1px solid black; text-align: center;">{{$applicant}}</td>
				</tr>				
				<tr>
					<td style="width: 2%; border:1px solid black;">Date Implementation Target</td>
					<td style="border:1px solid black; text-align: center;"><?php echo date('d F Y', strtotime($target_date)) ?></td></td>
				</tr>
			</tbody>
		</table>

		<br>

		<b>Please Translate Sakurentsu Based On File Attached</b>
		<br>Thank you<br><br>

		Click Here To --> <a href="{{ url("index/sakurentsu/upload_sakurentsu_translate/".$id) }}">Upload Translated Sakurentsu</a>

		<br><br>

		<span style="font-size: 20px">Regards,</span>
		<br><br>

		<span style="font-size: 20px;font-weight: bold">MIRAI - MIS Team</span>

		@elseif($position == "PC")

		<h3>Dear PC Team</h3>

		<p style="font-size: 20px;">A New Sakurentsu Has Been Created & Translated<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.

		<h3>Sakuretsu Number {{$sakurentsu_number}}
		<br>Applicant {{$applicant}}</h3>
		<br>Translator {{$translator}}</h3>

		Please Check This Sakurentsu
		<br>Thank you<br><br>

		<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
		<a href="{{ url("index/sakurentsu/detail/".$id) }}">See Sakurentsu Detail</a>

		<br><br>

		<span style="font-size: 20px">Regards,</span>
		<br><br>

		<span style="font-size: 20px;font-weight: bold">MIRAI - MIS Team</span>

		@endif
			
	</div>
</body>
</html>