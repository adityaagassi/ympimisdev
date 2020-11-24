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
			@if($data[0]->posisi == "manager_pch")

			<p style="font-size: 18px;">Request Purchase Order (PO)<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h2>Purchase Order (PO) {{$data[0]->no_po}}</h2>

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
						<td style="border:1px solid black; text-align: left !important;"><?= $data[0]->buyer_name ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">PO Date</td>
						<td style="border:1px solid black; text-align: left !important;"><?php echo date('d F Y', strtotime($data[0]->tgl_po)) ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Supplier</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $data[0]->supplier_code ?> - <?= $data[0]->supplier_name ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Budget No</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $data[0]->budget_item ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Currency</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $data[0]->currency ?></td>
					</tr>
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold;"><i>Do you want to Approve this PO Request?</i></span><br>
			<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("purchase_order/approvemanager/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a style="background-color: red; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("purchase_order/reject/".$data[0]->id) }}">&nbsp; Reject &nbsp;</a>

			<br>
			<br>
			<br>

			<h3 style="text-align: left !important">Detail PO {{$data[0]->no_po}}</h3>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: #f5eb33">
					<tr>
						<th style="width: 3%; border:1px solid black;">Nama Item</th>
						<th style="width: 1%; border:1px solid black;">Delivery Date</th>
						<th style="width: 1%; border:1px solid black;">Qty</th>
						<th style="width: 1%; border:1px solid black;">Uom</th>
						<th style="width: 1%; border:1px solid black;">Price</th>
						<th style="width: 1%; border:1px solid black;">Amount</th>
					</tr>
				</thead>
				<tbody>
					@foreach($data as $datas)
					<tr>
						<td style="border:1px solid black; text-align: left !important;"><?= $datas->nama_item ?></td></td>
						<td style="border:1px solid black; text-align: center;"><?= $datas->delivery_date ?></td>
						<td style="border:1px solid black; text-align: center;"><?= $datas->qty ?></td>
						<td style="border:1px solid black; text-align: center;"><?= $datas->uom ?></td>
						 <?php
			                if($datas->goods_price != "0" || $datas->goods_price != 0){
			                    $amount = $datas->goods_price * $datas->qty;                    
			                }else{
			                    $amount = $datas->service_price * $datas->qty; 
			                }
			            ?>
			             <?php
			                if($datas->goods_price != "0" || $datas->goods_price != 0){ ?>
								<td style="border:1px solid black; text-align: center;"><?= $datas->goods_price ?></td>
			                <?php } else { ?>
								<td style="border:1px solid black; text-align: center;"><?= $datas->service_price ?></td>
			                <?php } ?>
						<td style="border:1px solid black; text-align: center;"><?= $amount ?></td>
					</tr>
					@endforeach
				</tbody>
			</table>


			<br><br><br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url('purchase_order/monitoring') }}">Purchase Order (PO) Monitoring</a>

			<br><br>

			<span style="font-size: 20px">Best Regards,</span>
			<br><br>

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">

			@elseif($data[0]->posisi == "dgm_pch")

			<p style="font-size: 18px;">Request Purchase Order (PO)<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h2>Purchase Order (PO) {{$data[0]->no_po}}</h2>

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
						<td style="border:1px solid black; text-align: left !important;"><?= $data[0]->buyer_name ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">PO Date</td>
						<td style="border:1px solid black; text-align: left !important;"><?php echo date('d F Y', strtotime($data[0]->tgl_po)) ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Supplier</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $data[0]->supplier_code ?> - <?= $data[0]->supplier_name ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Budget No</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $data[0]->budget_item ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Currency</td>
						<td style="border:1px solid black; text-align: left !important;"><?= $data[0]->currency ?></td>
					</tr>
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold;"><i>Do you want to Approve this PO Request ?</i></span><br>
			<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("purchase_order/approvedgm/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a style="background-color: red; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("purchase_order/reject/".$data[0]->id) }}">&nbsp; Reject &nbsp;</a>

			<br>
			<br>
			<br>

			<h3 style="text-align: left !important">Detail PO {{$data[0]->no_po}}</h3>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: #f5eb33">
					<tr>
						<th style="width: 3%; border:1px solid black;">Nama Item</th>
						<th style="width: 1%; border:1px solid black;">Delivery Date</th>
						<th style="width: 1%; border:1px solid black;">Qty</th>
						<th style="width: 1%; border:1px solid black;">Uom</th>
						<th style="width: 1%; border:1px solid black;">Price</th>
						<th style="width: 1%; border:1px solid black;">Amount</th>
					</tr>
				</thead>
				<tbody>
					@foreach($data as $datas)
					<tr>
						<td style="border:1px solid black; text-align: left !important;"><?= $datas->nama_item ?></td></td>
						<td style="border:1px solid black; text-align: center;"><?= $datas->delivery_date ?></td>
						<td style="border:1px solid black; text-align: center;"><?= $datas->qty ?></td>
						<td style="border:1px solid black; text-align: center;"><?= $datas->uom ?></td>
						 <?php
			                if($datas->goods_price != "0" || $datas->goods_price != 0){
			                    $amount = $datas->goods_price * $datas->qty;                    
			                }else{
			                    $amount = $datas->service_price * $datas->qty; 
			                }
			            ?>
			             <?php
			                if($datas->goods_price != "0" || $datas->goods_price != 0){ ?>
								<td style="border:1px solid black; text-align: center;"><?= $datas->goods_price ?></td>
			                <?php } else { ?>
								<td style="border:1px solid black; text-align: center;"><?= $datas->service_price ?></td>
			                <?php } ?>
						<td style="border:1px solid black; text-align: center;"><?= $amount ?></td>
					</tr>
					@endforeach
				</tbody>
			</table>

			<br><br><br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url('purchase_order/monitoring') }}">Purchase Order (PO) Monitoring</a>

			<br><br>

			<span style="font-size: 20px">Best Regards,</span>

			<br><br>

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">

			@elseif($data[0]->posisi == "gm_pch")

			<p style="font-size: 18px;">Request Purchase Order (PO)<br>発注申請<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>

			This is an automatic notification. Please do not reply to this address.<br>
			自動通知です。返事しないでください。<br>

			<h2>Purchase Order (発注依頼) {{$data[0]->no_po}}</h2>

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
						<td style="border:1px solid black; text-align:left !important;"><?= $data[0]->buyer_name ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">PO Date (作成日付)</td>
						<td style="border:1px solid black; text-align:left !important;"><?php echo date('d F Y', strtotime($data[0]->tgl_po)) ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Supplier (サプライヤー)</td>
						<td style="border:1px solid black; text-align:left !important;"><?= $data[0]->supplier_code ?> - <?= $data[0]->supplier_name ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Budget No (予算番号)</td>
						<td style="border:1px solid black; text-align:left !important;"><?= $data[0]->budget_item ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Currency (通貨)</td>
						<td style="border:1px solid black; text-align:left !important;"><?= $data[0]->currency ?></td>
					</tr>
				</tbody>
			</table>
			<br>

			<br>
			<span style="font-weight: bold;"><i>Do you want to Approve this PO Request ?<br>(こちらに発注を承認しますか)?</i></span><br>
			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("purchase_order/approvegm/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;" href="{{ url("purchase_order/reject/".$data[0]->id) }}">&nbsp; Reject (却下) &nbsp;</a><br>

			
			<br><br><br>

			<h3 style="text-align: left !important">購入指示の内訳 {{$data[0]->no_po}}</h3>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: #f5eb33">
					<tr>
						<th style="width: 3%; border:1px solid black;">Nama Item (アイテム名)</th>
						<th style="width: 1%; border:1px solid black;">Delivery Date (納期)</th>
						<th style="width: 1%; border:1px solid black;">Qty (数量)</th>
						<th style="width: 1%; border:1px solid black;">Uom (単位)</th>
						<th style="width: 1%; border:1px solid black;">Price (値段)</th>
						<th style="width: 1%; border:1px solid black;">Amount (総額)</th>
					</tr>
				</thead>
				<tbody>
					@foreach($data as $datas)
					<tr>
						<td style="border:1px solid black; text-align: left !important;"><?= $datas->nama_item ?></td></td>
						<td style="border:1px solid black; text-align: center;"><?= $datas->delivery_date ?></td>
						<td style="border:1px solid black; text-align: center;"><?= $datas->qty ?></td>
						<td style="border:1px solid black; text-align: center;"><?= $datas->uom ?></td>
						 <?php
			                if($datas->goods_price != "0" || $datas->goods_price != 0){
			                    $amount = $datas->goods_price * $datas->qty;                    
			                }else{
			                    $amount = $datas->service_price * $datas->qty; 
			                }
			            ?>
			             <?php
			                if($datas->goods_price != "0" || $datas->goods_price != 0){ ?>
								<td style="border:1px solid black; text-align: center;"><?= $datas->goods_price ?></td>
			                <?php } else { ?>
								<td style="border:1px solid black; text-align: center;"><?= $datas->service_price ?></td>
			                <?php } ?>
						<td style="border:1px solid black; text-align: center;"><?= $amount ?></td>
					</tr>
					@endforeach
				</tbody>
			</table>

			<br><br><br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url('purchase_order/monitoring') }}">Purchase Order (PO) Monitoring</a>
			
			<br><br>

			<span style="font-size: 20px">Best Regards,</span>
			<br><br>

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">

			@elseif($data[0]->posisi == "pch")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Request Purchase Order (PO)<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h2>Purchase Order (PO) {{$data[0]->no_po}} <br> Telah Berhasil Di Diverifikasi</h2>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url('purchase_order/report/'.$data[0]->id) }}">Cek PO</a>
			<br>
			<a href="{{url('purchase_order')}}">List PO</a>


			<!-- Tolak -->

			@elseif($data[0]->posisi == "staff_pch")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Request Purchase Order (PO) Not Approved<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
			<br>
			<h2>Purchase Order (PO) {{$data[0]->no_po}} Not Approved</h2>
			
			<?php if ($data[0]->reject != null) { ?>
				<h3>Reason :<h3>
				<h3>
					<?= $data[0]->reject ?>	
				</h3>
			<?php } ?>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>

			<a href="{{ url('purchase_order/report/'.$data[0]->id) }}">Cek PO</a>
			<br>
			<a href="{{url('purchase_order')}}">List PO</a>

			@endif
			
		</center>
	</div>
</body>
</html>