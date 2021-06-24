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
	<?php $order_no = $row->order_no ?>
	<?php $section = $row->section ?>
	<?php $target_date = $row->target_date ?>
	<?php $type = $row->type ?>
	<?php $danger = $row->danger ?>
	<?php $category = $row->category ?>
	<?php $machine_condition = $row->machine_condition ?>
	<?php $machine_temp = $row->machine_temp ?>
	<?php $machine_remark = $row->machine_remark ?>
	<?php $machine_desc = $row->machine_desc ?>
	<?php $description = $row->description ?>
	<?php $safety_note = $row->safety_note ?>
	<?php $target_date = $row->target_date ?>
	<?php $pemohon = $row->name ?>
	@endforeach
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 20px;">Urgent Maintenance Job Order (SPK)</p>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<tbody>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(56, 181, 14);">Order No.</td>
						<td style="width: 2%; border:1px solid black;">{{ $order_no }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(56, 181, 14);">Pemohon</td>
						<td style="width: 2%; border:1px solid black;">{{ $pemohon }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(56, 181, 14);">Bagian</td>
						<td style="width: 2%; border:1px solid black;">{{ $section }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(56, 181, 14);">Jenis Pekerjaan</td>
						<td style="width: 2%; border:1px solid black;">{{ $type }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(56, 181, 14);">Kategori</td>
						<td style="width: 2%; border:1px solid black;">{{ $category }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(56, 181, 14);">Kondisi Mesin</td>
						<td style="width: 2%; border:1px solid black;">{{ $machine_condition }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(56, 181, 14);">Nama Mesin</td>
						<td style="width: 2%; border:1px solid black;">
							<?php 
							if ($machine_temp == 'Lain - lain') {
								echo isset($machine_remark) ? $machine_remark : '';
							} else {
								echo isset($machine_desc) ? $machine_desc : '';
							}
							?>
						</td>
					</tr>

					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(56, 181, 14);">Uraian Permintaan</td>
						<td style="width: 2%; border:1px solid black;">{{ $description }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(56, 181, 14);">Catatan Safety</td>
						<td style="width: 2%; border:1px solid black;">{{ $safety_note }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(56, 181, 14);">Target Selesai</td>
						<td style="width: 2%; border:1px solid black;">{{ $target_date }}</td>
					</tr>
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Klik disini untuk</i> &#8650;</span><br>
			<a style="background-color: orange; width: 50px;" href="{{ url("index/maintenance/list_spk") }}">&nbsp; Lihat Detail &nbsp;</a><br>
		</center>
	</div>
</body>
</html>