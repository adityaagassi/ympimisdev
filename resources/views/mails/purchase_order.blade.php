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
				<?php $remark = $datas->remark ?>
				<?php $budget = $datas->budget_item ?>
				<?php $amount = $datas->amount ?>
				<?php $alasan = $datas->reject ?>
			@endforeach

			@if($posisi == "manager_pch")

			<p style="font-size: 18px;">Request Purchase Order (PO)<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h2>Purchase Order (PO) {{$no_po}}</h2>

			<table style="border:1px solid black; border-collapse: collapse;" width="60%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 2%; border:1px solid black;">Point</th>
						<th style="width: 4%; border:1px solid black;">Content</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 2%; border:1px solid black;">Buyer</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $buyer_name ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">PO Date</td>
						<td style="border:1px solid black; text-align: left !important;"><?php echo date('d F Y', strtotime($tgl_po)) ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Supplier</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $supplier_code ?> - <?= $supplier_name ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Budget No</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $budget ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Currency</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $currency ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">PO Amount</td>
						<td style="border:1px solid black; text-align: left !important;">
						@if($currency == "USD")
							$
						@elseif($currency == "JPY")
							¥
						@elseif($currency == "IDR")
							Rp.
						@endif
						<?= number_format($amount,2,",",".") ?>
							
						</td>
					</tr>
				</tbody>
			</table>
			<br>

			<br>
			<span style="font-weight: bold;"><i>Do you want to Approve this PO Request?</i></span><br>
			<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("purchase_order/approvemanager/".$id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a style="background-color: red; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("purchase_order/reject/".$id) }}">&nbsp; Reject &nbsp;</a>


			<br><br><br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url('purchase_order/monitoring') }}">Purchase Order (PO) Monitoring</a>

			<br><br>

			<span style="font-size: 20px">Best Regards,</span>
			<br><br>

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">

			@elseif($posisi == "dgm_pch")

			<p style="font-size: 18px;">Request Purchase Order (PO)<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h2>Purchase Order (PO) {{$no_po}}</h2>

			<table style="border:1px solid black; border-collapse: collapse;" width="60%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 2%; border:1px solid black;">Point</th>
						<th style="width: 4%; border:1px solid black;">Content</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 2%; border:1px solid black;">Buyer</td>
						<td style="border:1px solid black; text-align:left !important;"><?= $buyer_name ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">PO Date</td>
						<td style="border:1px solid black; text-align:left !important;"><?php echo date('d F Y', strtotime($tgl_po)) ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Supplier</td>
						<td style="border:1px solid black; text-align:left !important;"><?= $supplier_code ?> - <?= $supplier_name ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Budget No</td>
						<td style="border:1px solid black; text-align:left !important;"><?= $budget ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Currency</td>
						<td style="border:1px solid black; text-align:left !important;"><?= $currency ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">PO Amount</td>
						<td style="border:1px solid black; text-align:left !important;"> 
						@if($currency == "USD")
							$
						@elseif($currency == "JPY")
							¥
						@elseif($currency == "IDR")
							Rp.
						@endif
						<?= number_format($amount,2,",",".") ?>
							
						</td>
					</tr>
				</tbody>
			</table>
			<br>

			<br>
			<span style="font-weight: bold;"><i>Do you want to Approve this PO Request ?</i></span><br>
			<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("purchase_order/approvedgm/".$id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a style="background-color: red; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("purchase_order/reject/".$id) }}">&nbsp; Reject &nbsp;</a>

			<br><br><br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url('purchase_order/monitoring') }}">Purchase Order (PO) Monitoring</a>

			<br><br>

			<span style="font-size: 20px">Best Regards,</span>
			<br><br>

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">

			@elseif($posisi == "gm_pch")

			<p style="font-size: 18px;">Request Purchase Order (PO)<br>発注申請<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>

			This is an automatic notification. Please do not reply to this address.<br>
			自動通知です。返事しないでください。<br>

			<h2>Purchase Order (発注依頼) {{$no_po}}</h2>

			<table style="border:1px solid black; border-collapse: collapse;" width="60%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 2%; border:1px solid black;">Point</th>
						<th style="width: 4%; border:1px solid black;">Content</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 2%; border:1px solid black;">Buyer (購入担当者)</td>
						<td style="border:1px solid black; text-align:left !important;"><?= $buyer_name ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">PO Date (作成日付)</td>
						<td style="border:1px solid black; text-align:left !important;"><?php echo date('d F Y', strtotime($tgl_po)) ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Supplier (サプライヤー)</td>
						<td style="border:1px solid black; text-align:left !important;"><?= $supplier_code ?> - <?= $supplier_name ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Budget No (予算番号)</td>
						<td style="border:1px solid black; text-align:left !important;"><?= $budget ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Currency (通貨)</td>
						<td style="border:1px solid black; text-align:left !important;"><?= $currency ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">PO Amount (全額)</td>
						<td style="border:1px solid black; text-align:left !important;"> 
						@if($currency == "USD")
							$
						@elseif($currency == "JPY")
							¥
						@elseif($currency == "IDR")
							Rp.
						@endif
						<?= number_format($amount,2,",",".") ?>
							
						</td>
					</tr>
				</tbody>
			</table>
			<br>

			<br>
			<span style="font-weight: bold;"><i>Do you want to Approve this PO Request ?<br>(こちらに発注を承認しますか)?</i></span><br>
			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("purchase_order/approvegm/".$id) }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("purchase_order/reject/".$id) }}">&nbsp; Reject (却下) &nbsp;</a><br>

			
			<br><br><br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url('purchase_order/monitoring') }}">Purchase Order (PO) Monitoring</a>
			
			<br><br>

			<span style="font-size: 20px">Best Regards,</span>
			<br><br>

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">

			@elseif($posisi == "pch")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Request Purchase Order (PO)<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h2>Purchase Order (PO) {{$no_po}} <br> Telah Berhasil Di Diverifikasi</h2>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url('purchase_order/report/'.$id) }}">Cek PO</a>
			<br>
			<a href="{{url('purchase_order')}}">List PO</a>


			<!-- Tolak -->

			@elseif($posisi == "staff_pch")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Request Purchase Order (PO) Not Approved<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
			<br>
			<h2>Purchase Order (PO) {{$no_po}} Not Approved</h2>
			
			<?php if ($alasan != null) { ?>
				<h3>Reason :<h3>
				<h3>
					<?= $alasan ?>	
				</h3>
			<?php } ?>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>

			<a href="{{ url('purchase_order/report/'.$id) }}">Cek PO</a>
			<br>
			<a href="{{url('purchase_order')}}">List PO</a>

			@endif
			
		</center>
	</div>
</body>
</html>