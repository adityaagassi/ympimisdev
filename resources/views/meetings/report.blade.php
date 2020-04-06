<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		body{
			font-size: 10px;
		}
	</style>
</head>

<body>
	<header>
		<table style="width: 100%; font-family: arial; border-collapse: collapse; text-align: left;">
			<thead>
				<tr>
					<td rowspan="3" style="height: 14px; width: 3%; text-align: center; font-weight: bold; border-top:1px solid black; border-left:1px solid black; border-right:1px solid black; font-size: 16px;">DAFTAR HADIR</td>
					<td style="width: 3%; text-align: center; border:1px solid black;">Tanggal & Waktu</td>
					<td style="width: 6%; text-align: center; border:1px solid black;">Kegiatan / Acara</td>
					<td style="width: 1%; text-align: center; border:1px solid black;">Penyelenggara</td>
				</tr>
				<tr>
					<td style="height: 50px; border:1px solid black; text-align: center;">{{ date("d M Y H:i", strtotime($reports[0]->start_time)) }}<br>s/d<br>{{ date("d M Y H:i", strtotime($reports[0]->end_time)) }}</td>
					<td style="border:1px solid black; text-align: center;">{{ $reports[0]->subject }}</td>
					<td style="border:1px solid black;"></td>
				</tr>
				<tr>
					<td style="height: 14px; border-right:1px solid black;">Dok. No: YMPI/PGA/FL/012</td>
					<td style="border-right:1px solid black;">Revisi No:03</td>
					<td style="border-right:1px solid black;">{{ $reports[0]->organizer_id }}</td>
				</tr>
			</thead>
		</table>
	</header>
	<main>
		<table style="width: 100%; font-family: arial; border-collapse: collapse; text-align: center;">
			<thead>
				<tr>
					<td style="height: 12px; width:1%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">No</td>
					<td style="width:3%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">NIK</td>
					<td style="width:5%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Nama Peserta</td>
					<td style="width:4%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Dept</td>
					<td style="width:2%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Kehadiran</td>
					<td style="width:4%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Keterangan</td>
				</tr>
			</thead>
			<tbody>
				<?php $no = 1; ?>
				@foreach($reports as $report)
				<tr>
					<td style="height: 26px; border: 1px solid black;">{{ $no }}</td>
					<td style="border: 1px solid black;">{{ $report->employee_id }}</td>
					<td style="border: 1px solid black;">{{ $report->name }}</td>
					<td style="border: 1px solid black;">{{ $report->department }}</td>
					<td style="border: 1px solid black;">Hadir</td>
					<td style="border: 1px solid black;"></td>
				</tr>
				<?php $no++; ?>
				@endforeach
			</tbody>
		</table>
	</main>
</body>
</html>