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
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Japanese Food Order <span style="color: purple;">和食弁当の予約</span><br>
			This is an automatic notification. Please do not reply to this address.</p>
			Your order has been created, please wait for approval. <span style="color: purple;">予約完了です。ご確認をお待ちください。</span>
			<br>
			<table style="border:1px solid black;border-collapse: collapse;" width="40%">
				<thead style="background-color: #fdd835">
					{{-- <tr>
						<th style="width: 1%; border:1px solid black;" colspan="2">Order Information<br><span style="color: purple;">予約の情報</span></th>
					</tr> --}}
					<tr>
						<th style="width: 3%; border:1px solid black;">Ordered By<br><span style="color: purple;">予約者</span></th>
						{{-- <th style="width: 3%; border:1px solid black;">Charged To<br><span style="color: purple;">請求先</span></th> --}}
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 6%; border:1px solid black; text-align: left !important;">{{ $data[0]["order_by"] }} - {{ $data[0]["order_by_name"] }}</td>
						{{-- <td style="width: 6%; border:1px solid black; text-align: left !important;">{{ $data[0]['charge_to'] }}<br>{{ $data[0]['charge_to_name'] }}</td> --}}
					</tr>			
				</tbody>
			</table>
			<br>
			<table style="border:1px solid black;border-collapse: collapse;" width="60%">
				<thead style="background-color: #63ccff">
					<tr>
						<th style="width: 1%; border:1px solid black;" colspan="4">Order List<br><span style="color: purple;">予約内容</span></th>
					</tr>
					<tr>
						<th style="width: 1%; border:1px solid black;">#</th>
						<th style="width: 1%; border:1px solid black;">Ordered For ID<br><span style="color: purple;">予約対象者</span></th>
						<th style="width: 1%; border:1px solid black;">Ordered For Name<br><span style="color: purple;">予約対象者</span></th>
						<th style="width: 1%; border:1px solid black;">Due Date<br><span style="color: purple;">日付</span></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$id = "";
					for ($i=0; $i < count($data); $i++) { 
						$id .= $data[$i]['id']."-";
						print_r ('<tr>
							<td style="border:1px solid black; width:1%;">'.($i+1).'</td>
							<td style="border:1px solid black; width:1%;">'.$data[$i]['employee_id'].'</td>
							<td style="border:1px solid black; width:8%;">'.$data[$i]['employee_name'].'</td>
							<td style="border:1px solid black; width:3%;">'.$data[$i]['due_date'].'</td>
							</tr>');
					}
					?>
				</tbody>
			</table>

			<br><br>
			<span style="font-weight: bold;"><i>For YMPI General Affair PIC</i></span>
			<br><br>
			<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("approve/ga_control/bento/".$id) }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a style="background-color: red; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("reject/ga_control/bento/".$id) }}">&nbsp; Reject (却下）&nbsp;</a>
		</center>
	</div>
</body>
</html>