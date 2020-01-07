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
	<?php $sub_section = $row->sub_section ?>
	<?php $item_name = $row->item_name ?>
	<?php $quantity = $row->quantity ?>
	<?php $request_date = $row->request_date ?>
	<?php $type = $row->type ?>
	<?php $material = $row->material ?>
	<?php $problem_description = $row->problem_description ?>
	<?php $pemohon = $row->name ?>
	@endforeach
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Urgent Workshop Job Order</p>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<tbody>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(126,86,134);">Order No.</td>
						<td style="width: 2%; border:1px solid black;">{{ $order_no }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(126,86,134);">Pemohon</td>
						<td style="width: 2%; border:1px solid black;">{{ $pemohon }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(126,86,134);">Bagian</td>
						<td style="width: 2%; border:1px solid black;">{{ $sub_section }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(126,86,134);">Nama Barang</td>
						<td style="width: 2%; border:1px solid black;">{{ $item_name }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(126,86,134);">Jumlah</td>
						<td style="width: 2%; border:1px solid black;">{{ $quantity }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(126,86,134);">Jenis Pekerjaan</td>
						<td style="width: 2%; border:1px solid black;">{{ $type }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(126,86,134);">Material</td>
						<td style="width: 2%; border:1px solid black;">{{ $material }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(126,86,134);">Uraian Permintaan</td>
						<td style="width: 2%; border:1px solid black;">{{ $problem_description }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(126,86,134);">Target Selesai</td>
						<td style="width: 2%; border:1px solid black;">{{ $request_date }}</td>
					</tr>
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold;"><i>Apakah anda menyetujui permohonan ini sebagai WJO Urgent?</i></span><br>
			<a style="background-color: green; width: 50px;" href="{{ url("update/workshop/approve_urgent/".$order_no) }}">&nbsp;&nbsp;&nbsp; Ya &nbsp;&nbsp;&nbsp;</a>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a style="background-color: orange; width: 50px;" href="{{ url("update/workshop/reject_urgent/".$order_no) }}">&nbsp; Tidak &nbsp;</a><br>
		</center>
	</div>
</body>
</html>