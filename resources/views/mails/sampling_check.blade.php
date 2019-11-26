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
					@foreach($data as $col2)
						<?php $activity_name = $col2->activity_name ?>
						<?php $department_name = $col2->department_name ?>
						<?php $activity_list_id = $col2->activity_list_id ?>
						<?php $section = $col2->section ?>
						<?php $subsection = $col2->subsection ?>
						<?php $date = $col2->date ?>
						<?php $product = $col2->product ?>
						<?php $month = date("F", strtotime($col2->month)); ?>
					@endforeach
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<?php $i = 1; ?>
			@foreach($data as $col)
			<p style="font-size: 18px;">{{ $activity_name }} ({{ $department_name }}) <br>on {{ $col->date }}<br>Section {{ $section }}<br>Sub Section {{ $subsection }}<br>Product {{ $product }}<br>with Serial Number {{ $col->no_seri_part }} <br> (Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black;">#</th>
						<th style="width: 2%; border:1px solid black;">Jumlah Check</th>
						<th style="width: 2%; border:1px solid black;">Point Check</th>
						<th style="width: 2%; border:1px solid black;">Hasil Check</th>
						<th style="width: 2%; border:1px solid black;">PIC Check</th>
						<th style="width: 2%; border:1px solid black;">Sampling By</th>
					</tr>
				</thead>
				<tbody>
					<?php $point_check = DB::select("select * from sampling_check_details where sampling_check_id = '".$col->id_sampling_check."'");
						$jumlah_point_check = count($point_check); ?>
					@foreach($point_check as $point_check)
					<tr>
						<td style="border:1px solid black; text-align: center;">{{$i}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->jumlah_cek}}</td>
						<td style="border:1px solid black; text-align: center;"><?php echo $point_check->point_check ?></td>
						<td style="border:1px solid black; text-align: center;"><?php echo $point_check->hasil_check ?></td>
						<td style="border:1px solid black; text-align: center;">{{$point_check->pic_check}}</td>
						<td style="border:1px solid black; text-align: center;">{{$point_check->sampling_by}}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			<?php $i++; ?>
			@endforeach
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="http://172.17.128.4/mirai/public/index/sampling_check/print_sampling_email/{{ $activity_list_id }}/{{ $subsection }}/{{ substr($date,0,7) }}">See Sampling Check Data / Approval Data</a><br>
			<a href="http://172.17.128.4/mirai/public/index/sampling_check/report_sampling_check/8">Sampling Check Monitoring</a>
		</center>
	</div>
</body>
</html>