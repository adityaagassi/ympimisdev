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

		<p style="font-size: 20px;">Payment Request <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.

		<h2>Payment Request {{$data[0]->kind_of}} <?= date('d M y', strtotime($data[0]->payment_date)) ?></h2>

		<table width="80%">
			<tbody>
				<tr>
					<td style="width: 25%; ">Vendor</td>
					<td>: <?= $data[0]->supplier_code ?> - <?= $data[0]->supplier_name ?></td></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Amount</td>
					<td>: Rp. <?= number_format($data[0]->amount ,2,",",".");?> </td>	
				</tr>
				<tr>
					<td style="width: 25%; ">Payment Term</td>
					<td>: <?= $data[0]->payment_term ?></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Due Date</td>
					<td>: <?= date('d-M-y', strtotime($data[0]->payment_due_date)) ?></td>
				</tr>
			</tbody>
		</table>
		<br><br>

		<span style="font-weight: bold;font-size: 18px"><i>Do you want to Approve This Payment Request?</i></span>
		<br><br>

		@if($data[0]->posisi == "manager")
		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("payment_request/approvemanager/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		@elseif($data[0]->posisi == "dgm")
		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("payment_request/approvedgm/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		@endif
		<a style="background-color: red; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("payment_request/reject/".$data[0]->id) }}">&nbsp; Reject &nbsp;</a>

		<br><br>

		<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
		<a href="{{ url('payment_request/monitoring') }}">Payment Request Monitoring</a>

		<br><br>

		<span style="font-size: 20px">Best Regards,</span>
		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">


		@elseif($data[0]->posisi == "gm") <!-- General Manager -->

		<p style="font-size: 20px;">Payment Request <br> (支払リクエスト) <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>

		This is an automatic notification. Please do not reply to this address.<br>
		自動通知です。返事しないでください。<br>

		<h2>Payment Request {{$data[0]->kind_of}} <?= date('d M y', strtotime($data[0]->payment_date)) ?></h2>

		<table width="80%">
			<tbody>
				<tr>
					<td style="width: 25%; ">Vendor (業者)</td>
					<td>: <?= $data[0]->supplier_code ?> - <?= $data[0]->supplier_name ?></td></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Amount (数量)</td>
					<td>: Rp. <?= number_format($data[0]->amount ,2,",",".");?> </td>	
				</tr>
				<tr>
					<td style="width: 25%; ">Payment Term (支払条件)</td>
					<td>: <?= $data[0]->payment_term ?></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Payment Due Date (納期)</td>
					<td>: <?= date('d-M-y', strtotime($data[0]->payment_due_date)) ?></td>
				</tr>
			</tbody>
		</table>
		<br><br>
		<span style="font-weight: bold;"><i>Do you want to Approve This Payment Request?<br>こちらの購入依頼を承認しますか</i></span>
		<br><br>

		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("payment_request/approvegm/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<a style="background-color: red; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("payment_request/reject/".$data[0]->id) }}">&nbsp; Reject (却下）&nbsp;</a>

		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">
		

		@elseif($data[0]->posisi == "acc")

		<p style="font-size: 18px;">Payment Request <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.

		<h2>Payment Request {{$data[0]->kind_of}} <?= date('d M y', strtotime($data[0]->payment_date)) ?></h2>

		<table width="80%">
			<tbody>
				<tr>
					<td style="width: 25%; ">Vendor</td>
					<td>: <?= $data[0]->supplier_code ?> - <?= $data[0]->supplier_name ?></td></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Amount</td>
					<td>: Rp. <?= number_format($data[0]->amount ,2,",",".");?> </td>	
				</tr>
				<tr>
					<td style="width: 25%; ">Payment Term</td>
					<td>: <?= $data[0]->payment_term ?></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Due Date</td>
					<td>: <?= date('d-M-y', strtotime($data[0]->payment_due_date)) ?></td>
				</tr>
			</tbody>
		</table>
		<br><br>

		<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here To</i> &#8650;</span><br>
		<a href="{{ url('payment_request/receiveacc/'.$data[0]->id) }}">Receive Payment Request</a><br>
		<br><br>

		<span style="font-size: 20px">Best Regards,</span>
		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">
			

		<!-- Tolak -->
		@elseif($data[0]->posisi == "user")

		<p style="font-size: 18px;">Payment Request Not Approved<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.
		<br>
		<h2>Payment Request {{$data[0]->kind_of}} <?= date('d M y', strtotime($data[0]->payment_date)) ?> Not Approved</h2>
		
		<?php if ($data[0]->alasan != null) { ?>
			<h3>Reason :<h3>
			<h3>
				<?= $data[0]->alasan ?>	
			</h3>
		<?php } ?>

		<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
		
		<a href="{{ url('report/payment_request/'.$data[0]->id) }}">Payment Request Check</a>
		<br>
		<a href="{{url('payment_request')}}">Payment Request List</a>

		<br><br>

		<span style="font-size: 20px">Best Regards,</span>
		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">

		@endif
		</center>
	</div>
</body>
</html>