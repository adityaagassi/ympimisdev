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
		@if($data[0]->posisi == "staff")

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
		<a href="http://172.17.128.4/mirai/public/purchase_requisition/check/{{ $data[0]->id }}">Check PR</a><br>

		@elseif($data[0]->posisi == "manager" || $data[0]->posisi == "dgm")

		<!-- <img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""> -->
		<p style="font-size: 20px;">Request Purchase Requisition (PR) <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.

		<h2>Purchase Requisition (PR) {{ $data[0]->no_pr }}</h2>

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
			</tbody>
		</table>
		<br><br>

		<table style="border:1px solid black; border-collapse: collapse;" width="80%">
			<thead style="background-color: rgb(126,86,134);">
				<tr>
					<th style="width: 2%; border:1px solid black;">Item Desc</th>
					<th style="width: 2%; border:1px solid black;">Peruntukan</th>
					<th style="width: 2%; border:1px solid black;">Stok</th>
					<th style="width: 2%; border:1px solid black;">Kebutuhan</th>
				</tr>
			</thead>
			<tbody>
				@foreach($data as $datas)
				<tr>
					<td style="border:1px solid black; text-align: center;"><?= $datas->item_desc ?></td></td>
					<td style="border:1px solid black; text-align: center;"><?= $datas->peruntukan ?></td>
					<td style="border:1px solid black; text-align: center;"><?= $datas->item_stock ?></td>
					<td style="border:1px solid black; text-align: center;"><?= $datas->kebutuhan ?></td>
				</tr>
				@endforeach
			</tbody>
		</table>
		<br>
		<span style="font-weight: bold;font-size: 18px"><i>Do you want to Approve This PR Request?</i></span>
		<br><br>

		@if($data[0]->posisi == "manager")
		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("purchase_requisition/approvemanager/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		@elseif($data[0]->posisi == "dgm")
		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("purchase_requisition/approvedgm/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		@endif
		<a style="background-color: red; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("purchase_requisition/reject/".$data[0]->id) }}">&nbsp; Reject &nbsp;</a>

		<br><br><br>

		<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
		<a href="{{ url('purchase_requisition/monitoring') }}">Purchase Requisition (PR) Monitoring</a>

		<br><br>

		<span style="font-size: 20px">Best Regards,</span>
		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">


		@elseif($data[0]->posisi == "gm") <!-- General Manager -->

		<p style="font-size: 20px;">Request Purchase Requisition (PR) <br>購入依頼の申請 <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>

		This is an automatic notification. Please do not reply to this address.<br>
		自動通知です。返事しないでください。<br>

		<h2>Purchase Requisition (購入申請) {{ $data[0]->no_pr }}</h2>

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
			</tbody>
		</table>
		<br><br>

		<table style="border:1px solid black; border-collapse: collapse;" width="80%">
			<thead style="background-color: rgb(126,86,134);">
				<tr>
					<th style="width: 2%; border:1px solid black;">Item Desc (品名)</th>
					<th style="width: 2%; border:1px solid black;">Peruntukan (目的)</th>
					<th style="width: 2%; border:1px solid black;">Stok (在庫)</th>
					<th style="width: 2%; border:1px solid black;">Kebutuhan (必要数)</th>
				</tr>
			</thead>
			<tbody>
				@foreach($data as $datas)
				<tr>
					<td style="border:1px solid black; text-align: center;"><?= $datas->item_desc ?></td></td>
					<td style="border:1px solid black; text-align: center;"><?= $datas->peruntukan ?></td>
					<td style="border:1px solid black; text-align: center;"><?= $datas->item_stock ?></td>
					<td style="border:1px solid black; text-align: center;"><?= $datas->kebutuhan ?></td>
				</tr>
				@endforeach
			</tbody>
		</table>
		<br>
		<span style="font-weight: bold;"><i>Do you want to Approve This PR Request?<br>こちらの購入依頼を承認しますか</i></span>
		<br><br>
		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("purchase_requisition/approvegm/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<a style="background-color: red; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("purchase_requisition/reject/".$data[0]->id) }}">&nbsp; Reject (却下）&nbsp;</a>


		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">
		

		@elseif($data[0]->posisi == "pch")

		<p style="font-size: 18px;">Request Purchase Requisition (PR) <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.

		<h2>Purchase Requisition (PR) {{ $data[0]->no_pr }}</h2>

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
			</tbody>
		</table>
		<br><br>

		<table style="border:1px solid black; border-collapse: collapse;" width="80%">
			<thead style="background-color: rgb(126,86,134);">
				<tr>
					<th style="width: 2%; border:1px solid black;">Item Desc</th>
					<th style="width: 2%; border:1px solid black;">Peruntukan</th>
					<th style="width: 2%; border:1px solid black;">Stok</th>
					<th style="width: 2%; border:1px solid black;">Kebutuhan</th>
				</tr>
			</thead>
			<tbody>
				@foreach($data as $datas)
				<tr>
					<td style="border:1px solid black; text-align: center;"><?= $datas->item_desc ?></td></td>
					<td style="border:1px solid black; text-align: center;"><?= $datas->peruntukan ?></td>
					<td style="border:1px solid black; text-align: center;"><?= $datas->item_stock ?></td>
					<td style="border:1px solid black; text-align: center;"><?= $datas->kebutuhan ?></td>
				</tr>
				@endforeach
			</tbody>
		</table>
		<br>

		<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here To</i> &#8650;</span><br>
		<a href="http://172.17.128.4/mirai/public/purchase_requisition/check/{{ $data[0]->id }}">Check & Verifikasi PR oleh Purchasing</a><br>
		<br><br>

		<span style="font-size: 20px">Best Regards,</span>
		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">
			

		<!-- Tolak -->
		@elseif($data[0]->posisi == "user")

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
		
		<a href="http://172.17.128.4/mirai/public/purchase_requisition/report/{{ $data[0]->id }}">PR Check</a>
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