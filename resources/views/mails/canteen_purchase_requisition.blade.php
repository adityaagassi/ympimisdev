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

		@if($data[0]->posisi == "manager")

		<!-- <img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""> -->
		<p style="font-size: 20px;">Request Canteen Purchase Requisition (PR) <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.

		<h2>Canteen Purchase Requisition (PR) {{ $data[0]->no_pr }}</h2>

		<table width="80%">
			<tbody>
				<tr>
					<td style="width: 25%; ">Submission Date</td>
					<td>: <?php echo date('d M Y', strtotime($data[0]->submission_date)) ?></td></td>
				</tr>
				<tr>
					<td style="width: 25%; ">User</td>
					<td>: <?= $data[0]->emp_name ?></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Department</td>
					<td>: <?= $data[0]->department ?></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Budget No</td>
					<td>: <?= $data[0]->no_budget ?></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Note</td>
					<td>: <?= $data[0]->note ?></td>
				</tr>
			</tbody>
		</table>
		<br><br>

		<table style="border:1px solid black; border-collapse: collapse;" width="80%">
			<thead style="background-color: rgb(126,86,134);">
				<tr>
					<th style="width: 3%; border:1px solid black;">Item Desc</th>
					<th style="width: 1%; border:1px solid black;">Qty</th>
					<th style="width: 1%; border:1px solid black;">Uom</th>
					<th style="width: 1%; border:1px solid black;">Price</th>
					<th style="width: 1%; border:1px solid black;">Amount</th>
				</tr>
			</thead>
			<tbody>
				@foreach($data as $datas)
				<tr>
					<td style="border:1px solid black; text-align: left !important;"><?= $datas->item_desc ?></td></td>
					<td style="border:1px solid black; text-align: center;"><?= $datas->item_qty ?></td>
					<td style="border:1px solid black; text-align: center;"><?= $datas->item_uom ?></td>
					<td style="border:1px solid black; text-align: center;"><?= number_format($datas->item_price,2,',','.') ?></td>
					<td style="border:1px solid black; text-align: center;"><?= number_format($datas->item_amount,2,',','.') ?></td>
				</tr>
				@endforeach
			</tbody>
		</table>
		<br>
		<span style="font-weight: bold;font-size: 18px"><i>Do you want to Approve This PR Request?</i></span>
		<br><br>

		@if($data[0]->posisi == "manager")
		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("canteen_purchase_requisition/approvemanager/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		@endif
		<a style="background-color: red; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("canteen_purchase_requisition/reject/".$data[0]->id) }}">&nbsp; Reject &nbsp;</a>

		<br><br><br>

		<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
		<a href="{{ url('canteen_purchase_requisition/monitoring') }}">Canteen Purchase Requisition (PR) Monitoring</a>

		<br><br>

		<span style="font-size: 20px">Best Regards,</span>
		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">


		@elseif($data[0]->posisi == "gm") <!-- General Manager -->

		<p style="font-size: 20px;">Request Canteen Purchase Requisition (PR) <br>購入依頼の申請 <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>

		This is an automatic notification. Please do not reply to this address.<br>
		自動通知です。返事しないでください。<br>

		<h2>Canteen Purchase Requisition (購入申請) {{ $data[0]->no_pr }}</h2>

		<table width="80%">
			<tbody>
				<tr>
					<td style="width: 30%; ">Submission Date (作成日付)</td>
					<td>: <?php echo date('d M Y', strtotime($data[0]->submission_date)) ?></td></td>
				</tr>
				<tr>
					<td style="width: 30%; ">User (ユーザー)</td>
					<td>: <?= $data[0]->emp_name ?></td>
				</tr>
				<tr>
					<td style="width: 30%; ">Department (部門)</td>
					<td>: <?= $data[0]->department ?></td>
				</tr>
				<tr>
					<td style="width: 30%; ">Budget No (予算番号)</td>
					<td>: <?= $data[0]->no_budget ?></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Note</td>
					<td>: <?= $data[0]->note ?></td>
				</tr>
			</tbody>
		</table>
		<br><br>

		<table style="border:1px solid black; border-collapse: collapse;" width="80%">
			<thead style="background-color: rgb(126,86,134);">
				<tr>
					<th style="width: 3%; border:1px solid black;">Item Desc</th>
					<th style="width: 1%; border:1px solid black;">Qty</th>
					<th style="width: 1%; border:1px solid black;">Uom</th>
					<th style="width: 1%; border:1px solid black;">Price</th>
					<th style="width: 1%; border:1px solid black;">Amount</th>
				</tr>
			</thead>
			<tbody>
				@foreach($data as $datas)
				<tr>
					<td style="border:1px solid black; text-align: left !important;"><?= $datas->item_desc ?></td></td>
					<td style="border:1px solid black; text-align: center;"><?= $datas->item_qty ?></td>
					<td style="border:1px solid black; text-align: center;"><?= $datas->item_uom ?></td>
					<td style="border:1px solid black; text-align: center;"><?= number_format($datas->item_price,2,",",".") ?></td>
					<td style="border:1px solid black; text-align: center;"><?= number_format($datas->item_amount,2,",",".") ?></td>
				</tr>
				@endforeach
			</tbody>
		</table>
		<br>
		<span style="font-weight: bold;"><i>Do you want to Approve This PR Request?<br>こちらの購入依頼を承認しますか</i></span>
		<br><br>
		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("canteen_purchase_requisition/approvegm/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<a style="background-color: red; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("canteen_purchase_requisition/reject/".$data[0]->id) }}">&nbsp; Reject (却下）&nbsp;</a>


		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">
		

		@elseif($data[0]->posisi == "pch")

		<p style="font-size: 18px;">Request Canteen Purchase Requisition (PR) <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.

		<h2>Canteen Purchase Requisition (PR) {{ $data[0]->no_pr }}</h2>

		<table width="80%">
			<tbody>
				<tr>
					<td style="width: 25%; ">Submission Date</td>
					<td>: <?php echo date('d M Y', strtotime($data[0]->submission_date)) ?></td></td>
				</tr>
				<tr>
					<td style="width: 25%; ">User</td>
					<td>: <?= $data[0]->emp_name ?></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Department</td>
					<td>: <?= $data[0]->department ?></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Budget No</td>
					<td>: <?= $data[0]->no_budget ?></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Note</td>
					<td>: <?= $data[0]->note ?></td>
				</tr>
			</tbody>
		</table>
		<br><br>

		<table style="border:1px solid black; border-collapse: collapse;" width="80%">
			<thead style="background-color: rgb(126,86,134);">
				<tr>
					<th style="width: 3%; border:1px solid black;">Item Desc</th>
					<th style="width: 1%; border:1px solid black;">Qty</th>
					<th style="width: 1%; border:1px solid black;">Uom</th>
					<th style="width: 1%; border:1px solid black;">Price</th>
					<th style="width: 1%; border:1px solid black;">Amount</th>
				</tr>
			</thead>
			<tbody>
				@foreach($data as $datas)
				<tr>
					<td style="border:1px solid black; text-align: left !important;"><?= $datas->item_desc ?></td></td>
					<td style="border:1px solid black; text-align: center;"><?= $datas->item_qty ?></td>
					<td style="border:1px solid black; text-align: center;"><?= $datas->item_uom ?></td>
					<td style="border:1px solid black; text-align: center;"><?= number_format($datas->item_price,2,",",".") ?></td>
					<td style="border:1px solid black; text-align: center;"><?= number_format($datas->item_amount,2,",",".") ?></td>
				</tr>
				@endforeach
			</tbody>
		</table>
		<br>

		<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here To</i> &#8650;</span><br>
		<a href="{{ url('canteen_purchase_requisition/check/'.$data[0]->id) }}">Check & Verifikasi PR oleh Purchasing</a><br>
		<br><br>

		<span style="font-size: 20px">Best Regards,</span>
		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">
			

		<!-- Tolak -->
		@elseif($data[0]->posisi == "user")

		<p style="font-size: 18px;">PR Request Canteen Not Approved<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.
		<br>
		<h2>Canteen Purchase Requisition (PR) {{ $data[0]->no_pr }} Not Approved</h2>
		
		<?php if ($data[0]->alasan != null) { ?>
			<h3>Reason :<h3>
			<h3>
				<?= $data[0]->alasan ?>	
			</h3>
		<?php } ?>

		<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
		
		<a href="{{ url('canteen_purchase_requisition/report/'.$data[0]->id) }}">PR Check</a>
		<br>
		<a href="{{url('canteen_purchase_requisition')}}">PR List</a>

		<br><br>

		<span style="font-size: 20px">Best Regards,</span>
		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">

		@endif
			
			
		</center>
	</div>
</body>
</html>