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
			<p style="font-size: 18px;">NG Report of Height Gauge Check Recorder (??) on {{ $data['push_block_code'] }}</p>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black;">#</th>
						<th style="width: 2%; border:1px solid black;">Check Date</th>
						<th style="width: 2%; border:1px solid black;">Injection Date</th>
						<th style="width: 2%; border:1px solid black;">PIC</th>
						<th style="width: 2%; border:1px solid black;">Product</th>
						<th style="width: 2%; border:1px solid black;">Head - Block</th>
						<th style="width: 2%; border:1px solid black;">Push Pull</th>
					</tr>
				</thead>
				<tbody>
					<?php for ($i=0; $i < count($data['push_pull_ng_name']); $i++) { ?>
						<tr>
							<td style="border:1px solid black; text-align: center;">{{ $i+1 }}</td>
							<td style="border:1px solid black; text-align: center;">{{ $data['check_date'] }}</td>
							<td style="border:1px solid black; text-align: center;">{{ $data['injection_date'] }}</td>
							<td style="border:1px solid black; text-align: center;">{{ $data['pic_check'] }}</td>
							<td style="border:1px solid black; text-align: center;">{{ $data['product_type'] }}</td>
							<td style="border:1px solid black; text-align: center;">{{ $data['push_pull_ng_name'][$i] }}</td>
							<td style="border:1px solid black; text-align: center;">{{ $data['push_pull_ng_value'][$i] }}</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
		</center>
	</div>
</body>
</html>