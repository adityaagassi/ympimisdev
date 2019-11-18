<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		table {
			border-collapse: collapse;
		}
		table, th, td {
			border: 1px solid black;
		}
		td {
			padding-top: 0px;
			padding-bottom: 0px;
			padding-left: 3px;
			padding-right: 3px;
		}
	</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">User Documents lebih dari remider</p>
			<p style="font-weight: bold;">Total Document: <?php echo $data['jml']; ?></p>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="70%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black;">#</th>
						<th style="width: 1%; border:1px solid black;">Category</th>
						<th style="width: 1%; border:1px solid black;">Document Number</th>
						<th style="width: 2%; border:1px solid black;">Employee ID</th>
						<th style="width: 2%; border:1px solid black;">Name</th>
						<th style="width: 2%; border:1px solid black;">Valid From</th>
						<th style="width: 2%; border:1px solid black;">Valid To</th>
						<th style="width: 2%; border:1px solid black;">Condition</th>
						<th style="width: 4%; border:1px solid black;">Validity Days</th>
					</tr>
				</thead>
				<tbody>

					<?php
					$i = 1;
					foreach($data['user_documents'] as $col){
					?>
					<tr>
						<td style="border:1px solid black;"><?php echo $i++; ?></td>
						<td style="border:1px solid black;"><?php echo $col->category; ?></td>
						<td style="border:1px solid black;"><?php echo $col->document_number; ?></td>
						<td style="border:1px solid black;"><?php echo $col->employee_id; ?></td>
						<td style="border:1px solid black;"><?php echo $col->name; ?></td>
						<td style="border:1px solid black;"><?php echo $col->valid_from; ?></td>
						<td style="border:1px solid black;"><?php echo $col->valid_to; ?></td>
						<td style="border:1px solid black;"><?php echo $col->condition; ?></td>
						<td style="border:1px solid black; text-align: right;"><?php echo $col->diff; ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="http://172.17.128.4/miraidev/public/index/user_document">Users Document Details</a>
		</center>
	</div>
</body>
</html>