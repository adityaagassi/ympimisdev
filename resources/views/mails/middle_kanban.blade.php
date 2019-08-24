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
			<p style="font-size: 18px;">Total kanban di WIP lebih dari 4 Hari</p>
			<?php
			foreach($data['jml'] as $col){
			?>
			<p style="font-weight: bold;">Total Kanban: <?php echo $col->jml; ?></p>
			<?php } ?>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="70%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black;">#</th>
						<th style="width: 1%; border:1px solid black;">Tag</th>
						<th style="width: 2%; border:1px solid black;">Material</th>
						<th style="width: 2%; border:1px solid black;">Model</th>
						<th style="width: 2%; border:1px solid black;">Key</th>
						<th style="width: 2%; border:1px solid black;">Surface</th>
						<th style="width: 3%; border:1px solid black;">Qty</th>
						<th style="width: 4%; border:1px solid black;">Created At</th>
						<th style="width: 3%; border:1px solid black;">Remark</th>
					</tr>
				</thead>
				<tbody>

					<?php
					$i = 1;
					foreach($data['kanban'] as $col){
					?>
					<tr>
						<td style="border:1px solid black;"><?php echo $i++; ?></td>
						<td style="border:1px solid black;"><?php echo $col->tag; ?></td>
						<td style="border:1px solid black;"><?php echo $col->material_number; ?></td>
						<td style="border:1px solid black;"><?php echo $col->model; ?></td>
						<td style="border:1px solid black;"><?php echo $col->key; ?></td>
						<td style="border:1px solid black;"><?php echo $col->surface; ?></td>
						<td style="border:1px solid black; text-align: right;"><?php echo $col->quantity; ?></td>
						<td style="border:1px solid black; text-align: right;"><?php echo $col->created_at; ?></td>
						<td style="border:1px solid black;text-align: center;"><?php if($col->remark){echo $col->remark;}else{echo "-";} ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			{{-- <br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="http://localhost/ympimisdev/public/index/middle/barrel_board/barrel-sx">SX Key Barrel Board</a> --}}
		</center>
	</div>
</body>
</html>