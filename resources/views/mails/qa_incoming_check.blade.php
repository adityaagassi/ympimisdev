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
					@foreach($data as $col2)
						<?php $material_number = $col2->material_number ?>
						<?php $material_description = $col2->material_description ?>
						<?php $invoice = $col2->invoice; ?>
						<?php $vendor = $col2->vendor; ?>
						<?php $qty_rec = $col2->qty_rec ?>
						<?php $qty_check = $col2->qty_check ?>
						<?php $ng_ratio = $col2->ng_ratio ?>
						<?php $status_lot = $col2->status_lot ?>
						<?php $report_evidence = $col2->report_evidence ?>
						<?php $created = $col2->created ?>
						<?php $ng_name = $col2->ng_name ?>
						<?php $ng_qty = $col2->ng_qty ?>
						<?php $status_ng = $col2->status_ng ?>
						<?php $note_ng = $col2->note_ng ?>
						<?php $report_evidence = $col2->report_evidence ?>
					@endforeach
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Report Material Lot Out (ロットアウト品の報告)</p>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead>
					<tr>
						<th style="width: 2%; border:1px solid black;background-color: rgb(126,86,134);">Material</th>
						<th style="width: 2%; border:1px solid black;background-color: rgb(126,86,134);">Invoice</th>
						<th colspan="2" style="width: 2%; border:1px solid black;background-color: rgb(126,86,134);">Vendor</th>
					</tr>
					<tr>
						<td style="border:1px solid black; text-align: center;">{{$material_number}}<br>{{$material_description}}</td>
						<td style="border:1px solid black; text-align: center;">{{$invoice}}</td>
						<td colspan="2" style="border:1px solid black; text-align: center;">{{$vendor}}</td>
					</tr>
					<tr>
						<th style="width: 2%; border:1px solid black;background-color: rgb(126,86,134);">Quantity Receive</th>
						<th style="width: 2%; border:1px solid black;background-color: rgb(126,86,134);">Quantity Check</th>
						<th style="width: 2%; border:1px solid black;background-color: rgb(126,86,134);">NG Ratio</th>
						<th style="width: 2%; border:1px solid black;background-color: rgb(126,86,134);">Status Lot</th>
					</tr>
					<tr>
						<td style="border:1px solid black; text-align: center;">{{$qty_rec}} Pc(s)</td>
						<td style="border:1px solid black; text-align: center;">{{$qty_check}} Pc(s)</td>
						<td style="border:1px solid black; text-align: center;">{{$ng_ratio}} %</td>
						<td style="border:1px solid black; text-align: center;">{{$status_lot}}</td>
					</tr>
				</thead>
			</table>
			<table style="border:1px solid black; border-collapse: collapse;margin-top: 20px" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black;">#</th>
						<th style="width: 2%; border:1px solid black;">NG Name</th>
						<th style="width: 2%; border:1px solid black;">Qty NG</th>
						<th style="width: 2%; border:1px solid black;">Status</th>
						<th style="width: 2%; border:1px solid black;">Note</th>
					</tr>
				</thead>
				<tbody>
					<?php if (count($ng_name) > 0){ ?>
						<?php $ng_names = explode('_', $ng_name); ?>
						<?php $ng_qtys = explode('_', $ng_qty); ?>
						<?php $status_ngs = explode('_', $status_ng); ?>
						<?php $note_ngs = ""; ?>
						<?php if ($note_ng != null){
							$note_ngs = explode('_', $note_ng);
						} ?>
					<?php } ?>
					<?php for ($i=0; $i < count($ng_names); $i++) { ?>
						<tr>
							<td style="border:1px solid black; text-align: center;">{{$i+1}}</td>
							<td style="border:1px solid black; text-align: center;">{{$ng_names[$i]}}</td>
							<td style="border:1px solid black; text-align: center;">{{$ng_qtys[$i]}}</td>
							<td style="border:1px solid black; text-align: center;">{{$status_ngs[$i]}}</td>
							<?php if ($note_ngs != ""){ ?>
								<td style="border:1px solid black; text-align: center;">{{$note_ngs[$i]}}</td>
							<?php }else{ ?>
								<td style="border:1px solid black; text-align: center;"></td>
							<?php } ?>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<table style="border:1px solid black; border-collapse: collapse;margin-top: 20px" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th>Evidence</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="border:1px solid black; text-align: center;"><?php echo $report_evidence ?></td>
					</tr>
				</tbody>
			</table>
		</center>
	</div>
</body>
</html>