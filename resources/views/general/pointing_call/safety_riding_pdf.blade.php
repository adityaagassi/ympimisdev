<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		table, td, th {
			border: 1px solid black;
		}

		table {
			width: 100%;
			border-collapse: collapse;
		}

	</style>
</head>
<body style="font-family: calibri;">
	<div>
		<center>
			<span style="font-weight: bold; font-size: 20px;">
				{{ date('Y', strtotime($safety_ridings[0]->period)) }}年 {{ date('m', strtotime($safety_ridings[0]->period)) }}月 Catatan Record Penerapan 『Janji Safety Riding』
			</span>
		</center>
		<br>
		<table style="border: 0px;">
			<tr>
				<th style="text-align: left; border: 0px;">
					<span>
						① Perkirakan waktu untuk tiba dengan selamat di tempat tujuan. (Mari berangkat kerja lebih awal.)
						<br>
						② Marilah patuhi aturan berlalu lintas demi orang-orang tercinta kita.
					</span>
				</th>
				<th style="text-align: right; border: 0px;">
					<span>
						No Dok. : YMPI/STD/FK3/054<br>
						Rev		: 00<br>
						Tanggal	: 01 April 2015
					</span>
				</th>
			</tr>
		</table>
		<br>
		<br>
		<table style="width: 50%;">
			<thead>
				<tr>
					<th rowspan="2" style="width: 3%;">{{ $safety_ridings[0]->department }}</th>
					<th style="width: 1%;">Manager</th>
					<th style="width: 1%;">Chief</th>
					<th style="width: 1%;">Staff</th>
				</tr>
				<tr>
					<th style="height: 40px;">{{ isset($manager->name) == true ? $manager->name : '-' }}</th>
					<th style="height: 40px;">{{ isset($chief->name) == true ? $chief->name : '-' }}</th>
					<th style="height: 40px;">{{ $safety_ridings[0]->name }}</th>
				</tr>
			</thead>
		</table>
		<br>
	</div>
	<table>
		<thead style="height: 50px;">
			<tr>
				<th>#</th>
				<th>Nama</th>
				<th>Janji Safety Riding</th>
				@foreach($weekly_calendars as $weekly_calendar)
				@if($weekly_calendar->remark == 'H')
				<th style="background-color: rgba(80,80,80,0.3)">{{ date('d', strtotime($weekly_calendar->week_date)) }}</th>
				@else
				<th>{{ date('d', strtotime($weekly_calendar->week_date)) }}</th>
				@endif
				@endforeach
			</tr>
		</tead>
		<tbody>
			<?php
			$count = 1;
			?>
			@foreach($safety_ridings as $safety_riding)
			<tr style="height: 45px;">
				<td style="padding-right: 2px; padding-left: 2px; width: 1%; text-align: center;">{{ $count }}</td>
				<td style="padding-right: 2px; padding-left: 2px; width: 7%;">{{ $safety_riding->employee_name }}</td>
				<td style="padding-right: 2px; padding-left: 2px; width: 12%;">{{ $safety_riding->safety_riding }}</td>
				@foreach($weekly_calendars as $weekly_calendar)
				@if(date('Y-m-d', strtotime($weekly_calendar->week_date)) > date('Y-m-d'))
				@if($weekly_calendar->remark == 'H')
				<td style="background-color: rgba(80,80,80,0.3); width: 1%;"></td>
				@else
				<td style="width: 1%;"></td>
				@endif
				@else
				@if($weekly_calendar->remark == 'H')
				<td style="text-align: center; background-color: rgba(80,80,80,0.3); width: 1%;">&check;</td>
				@else
				<td style="text-align: center; width: 1%;">&check;</td>
				@endif
				@endif
				@endforeach
			</tr>
			<?php
			$count += 1;
			?>
			@endforeach
		</tbody>
	</table>
</body>
</html>