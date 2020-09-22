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
			<?php $no_pr = $datas->no_pr ?>
			<?php $emp_name = $datas->emp_name ?>
			<?php $department = $datas->department ?>
			<?php $note = $datas->note ?>
			<?php $no_budget = $datas->no_budget ?>
			<?php $submission_date = $datas->submission_date ?>
			<?php $posisi = $datas->posisi ?>
			<?php $alasan = $datas->alasan ?>
		@endforeach

		@if($posisi == "staff")

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
		<p style="font-size: 18px;">Request Purchase Requisition (PR) <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.

		<h2>Purchase Requisition (PR) {{$no_pr}}</h2>

		<table style="border:1px solid black; border-collapse: collapse;" width="80%">
			<thead style="background-color: rgb(126,86,134);">
				<tr>
					<th style="width: 2%; border:1px solid black;">Point</th>
					<th style="width: 2%; border:1px solid black;">Content</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="width: 2%; border:1px solid black;">Date</td>
					<td style="border:1px solid black; text-align: center;"><?php echo date('d F Y', strtotime($submission_date)) ?></td></td>
				</tr>
				<tr>
					<td style="width: 2%; border:1px solid black;">User</td>
					<td style="border:1px solid black; text-align: center;"><?= $emp_name ?></td>
				</tr>
				<tr>
					<td style="width: 2%; border:1px solid black;">Department</td>
					<td style="border:1px solid black; text-align: center;"><?= $department ?></td>
				</tr>
				<tr>
					<td style="width: 2%; border:1px solid black;">No Budget</td>
					<td style="border:1px solid black; text-align: center;"><?= $no_budget ?></td>
				</tr>
			</tbody>
		</table>
		<br>

		<span style="font-weight: bold; background-color: orange;">&#8650; <i>Klik disini untuk</i> &#8650;</span><br>
		<a href="http://172.17.128.4/mirai/public/purchase_requisition/check/{{ $id }}">Check PR</a><br>

		@elseif($posisi == "manager" || $posisi == "dgm")

		<!-- <img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""> -->
		<p style="font-size: 20px;">Request Purchase Requisition (PR) <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.

		<h2>Purchase Requisition (PR) {{$no_pr}}</h2>

		<table style="border:1px solid black; border-collapse: collapse;" width="80%">
			<thead style="background-color: rgb(126,86,134);">
				<tr>
					<th style="width: 2%; border:1px solid black;">Point</th>
					<th style="width: 2%; border:1px solid black;">Content</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="width: 2%; border:1px solid black;">Submission Date</td>
					<td style="border:1px solid black; text-align: center;"><?php echo date('d F Y', strtotime($submission_date)) ?></td></td>
				</tr>
				<tr>
					<td style="width: 2%; border:1px solid black;">User</td>
					<td style="border:1px solid black; text-align: center;"><?= $emp_name ?></td>
				</tr>
				<tr>
					<td style="width: 2%; border:1px solid black;">Department</td>
					<td style="border:1px solid black; text-align: center;"><?= $department ?></td>
				</tr>
				<tr>
					<td style="width: 2%; border:1px solid black;">Budget No</td>
					<td style="border:1px solid black; text-align: center;"><?= $no_budget ?></td>
				</tr>
			</tbody>
		</table>
		<br>
		<span style="font-weight: bold;font-size: 18px"><i>Do you want to Approve This PR Request?</i></span>
		<br><br>

		@if($posisi == "manager")
		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("purchase_requisition/approvemanager/".$id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		@elseif($posisi == "dgm")
		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("purchase_requisition/approvedgm/".$id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		@endif
		<a style="background-color: red; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("purchase_requisition/reject/".$id) }}">&nbsp; Reject &nbsp;</a>

		<br><br>

		<span style="font-size: 20px">Best Regards,</span>
		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">


		@elseif($posisi == "gm") <!-- General Manager -->

		<p style="font-size: 20px;">Request Purchase Requisition (PR) <br>購入依頼の申請 <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>

		This is an automatic notification. Please do not reply to this address.<br>
		自動通知です。返事しないでください。<br>

		<h2>Purchase Requisition (PR) {{$no_pr}}</h2>

		<table style="border-collapse: collapse;" width="80%">
			<thead style="background-color: rgb(126,86,134);">
				<tr>
					<th style="width: 2%; border:1px solid black;">Point</th>
					<th style="width: 2%; border:1px solid black;">Content</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="width: 2%; border:1px solid black;">Submission Date (作成日付)</td>
					<td style="border:1px solid black; text-align: center;"><?php echo date('d F Y', strtotime($submission_date)) ?></td></td>
				</tr>
				<tr>
					<td style="width: 2%; border:1px solid black;">User (ユーザー)</td>
					<td style="border:1px solid black; text-align: center;"><?= $emp_name ?></td>
				</tr>
				<tr>
					<td style="width: 2%; border:1px solid black;">Department (部門)</td>
					<td style="border:1px solid black; text-align: center;"><?= $department ?></td>
				</tr>
				<tr>
					<td style="width: 2%; border:1px solid black;">Budget No (予算番号)</td>
					<td style="border:1px solid black; text-align: center;"><?= $no_budget ?></td>
				</tr>

			</tbody>
		</table>
		<br>
		<span style="font-weight: bold;"><i>Do you want to Approve This PR Request?<br>こちらの購入依頼を承認しますか</i></span>
		<br><br>
		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("purchase_requisition/approvegm/".$id) }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<a style="background-color: red; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("purchase_requisition/reject/".$id) }}">&nbsp; Reject (却下）&nbsp;</a>
		

		@elseif($posisi == "pch")

		<p style="font-size: 18px;">Request Purchase Requisition (PR) <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.

		<h2>Purchase Requisition (PR) {{$no_pr}}</h2>

		<table style="border:1px solid black; border-collapse: collapse;" width="80%">
			<thead style="background-color: rgb(126,86,134);">
				<tr>
					<th style="width: 2%; border:1px solid black;">Point</th>
					<th style="width: 2%; border:1px solid black;">Content</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="width: 2%; border:1px solid black;">Submission Date</td>
					<td style="border:1px solid black; text-align: center;"><?php echo date('d F Y', strtotime($submission_date)) ?></td></td>
				</tr>
				<tr>
					<td style="width: 2%; border:1px solid black;">User</td>
					<td style="border:1px solid black; text-align: center;"><?= $emp_name ?></td>
				</tr>
				<tr>
					<td style="width: 2%; border:1px solid black;">Department</td>
					<td style="border:1px solid black; text-align: center;"><?= $department ?></td>
				</tr>
				<tr>
					<td style="width: 2%; border:1px solid black;">Budget No</td>
					<td style="border:1px solid black; text-align: center;"><?= $no_budget ?></td>
				</tr>
			</tbody>
		</table>
		<br>

		<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here To</i> &#8650;</span><br>
		<a href="http://172.17.128.4/mirai/public/purchase_requisition/check/{{ $id }}">Check & Verifikasi PR oleh Purchasing</a><br>
		<br><br>

		<span style="font-size: 20px">Best Regards,</span>
		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">
			

		<!-- Tolak -->
		@elseif($posisi == "user")

		<p style="font-size: 18px;">PR Request Not Approved<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.
		<br>
		<h2>Purchase Requisition (PR) {{$no_pr}} Not Approved</h2>
		
		<?php if ($alasan != null) { ?>
			<h3>Reason :<h3>
			<h3>
				<?= $alasan ?>	
			</h3>
		<?php } ?>

		<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
		
		<a href="http://172.17.128.4/mirai/public/purchase_requisition/report/{{ $id }}">PR Check</a>
		<br>
		<a href="http://172.17.128.4/mirai/public/purchase_requisition">PR List</a>

		<br><br>

		<span style="font-size: 20px">Best Regards,</span>
		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">

		@endif
			
			
		</center>
	</div>
</body>
</html>