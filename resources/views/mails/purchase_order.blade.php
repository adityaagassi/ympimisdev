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
		<center>
			@foreach($data as $datas)
				<?php $id = $datas->id ?>
				<?php $posisi = $datas->posisi ?>
				<?php $no_po = $datas->no_po ?>
				<?php $tgl_po = $datas->tgl_po ?>
				<?php $supplier_code = $datas->supplier_code ?>
				<?php $supplier_name = $datas->supplier_name ?>
				<?php $material = $datas->material ?>
				<?php $vat = $datas->vat ?>
				<?php $currency = $datas->currency ?>
				<?php $transportation = $datas->transportation ?>
				<?php $buyer_name = $datas->buyer_name ?>
			@endforeach

			@if($posisi == "manager_pch")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Purchase Order <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h2>Purchase Order (PO) Nomor {{$no_po}}</h2>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 2%; border:1px solid black;">Point</th>
						<th style="width: 2%; border:1px solid black;">Content</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 2%; border:1px solid black;">Buyer</td>
						<td style="border:1px solid black; text-align: center;"><?= $buyer_name ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Tanggal</td>
						<td style="border:1px solid black; text-align: center;"><?php echo date('d F Y', strtotime($tgl_po)) ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Supplier</td>
						<td style="border:1px solid black; text-align: center;"><?= $supplier_code ?> - <?= $supplier_name ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Material</td>
						<td style="border:1px solid black; text-align: center;"><?= $material ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">VAT</td>
						<td style="border:1px solid black; text-align: center;"><?= $vat ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Transportation</td>
						<td style="border:1px solid black; text-align: center;"><?= $transportation ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Currency</td>
						<td style="border:1px solid black; text-align: center;"><?= $currency ?></td>
					</tr>
				</tbody>
			</table>
			<br>

			<br>
			<span style="font-weight: bold;"><i>Apakah anda ingin menyetujui PO ini ?</i></span><br>
			<a style="background-color: green; width: 50px;" href="{{ url("purchase_order/approvemanager/".$id) }}">&nbsp;&nbsp;&nbsp; Ya &nbsp;&nbsp;&nbsp;</a>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a style="background-color: orange; width: 50px;" href="{{ url("purchase_order/reject/".$id) }}">&nbsp; Tidak &nbsp;</a><br>

			@elseif($posisi == "gm_pch")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Purchase Order <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h2>Purchase Order (PO) Nomor {{$no_po}}</h2>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 2%; border:1px solid black;">Point</th>
						<th style="width: 2%; border:1px solid black;">Content</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 2%; border:1px solid black;">Buyer</td>
						<td style="border:1px solid black; text-align: center;"><?= $buyer_name ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Tanggal</td>
						<td style="border:1px solid black; text-align: center;"><?php echo date('d F Y', strtotime($tgl_po)) ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Supplier</td>
						<td style="border:1px solid black; text-align: center;"><?= $supplier_code ?> - <?= $supplier_name ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Material</td>
						<td style="border:1px solid black; text-align: center;"><?= $material ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">VAT</td>
						<td style="border:1px solid black; text-align: center;"><?= $vat ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Transportation</td>
						<td style="border:1px solid black; text-align: center;"><?= $transportation ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Currency</td>
						<td style="border:1px solid black; text-align: center;"><?= $currency ?></td>
					</tr>
				</tbody>
			</table>
			<br>

			<br>
			<span style="font-weight: bold;"><i>Apakah anda ingin menyetujui PO ini ?</i></span><br>
			<a style="background-color: green; width: 50px;" href="{{ url("purchase_order/approvegm/".$id) }}">&nbsp;&nbsp;&nbsp; Ya &nbsp;&nbsp;&nbsp;</a>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a style="background-color: orange; width: 50px;" href="{{ url("purchase_order/reject/".$id) }}">&nbsp; Tidak &nbsp;</a><br>
			
			@elseif($posisi == "pch")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Purchase Order <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h2>Purchase Order (PO) Nomor {{$no_po}} <br> Telah Berhasil Di Diverifikasi</h2>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="http://172.17.128.4/mirai/purchase_order/report/{{ $id }}">Cek PO</a>
			<br>
			<a href="http://172.17.128.4/mirai/public/purchase_order">List PO</a>


			<!-- Tolak -->

			@elseif($posisi == "staff_pch")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Purchase Order Tidak Disetujui<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
			<br>
			<h2>Purchase Order (PO) Nomor {{$no_po}} Telah Ditolak</h2>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="http://172.17.128.4/mirai/public/purchase_order/detail/{{ $id }}">PO Detail</a>

			@endif
			
		</center>
	</div>
</body>
</html>