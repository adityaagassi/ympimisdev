<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
	</style>
</head>
<body>
	<div style="width: 700px;">
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br><p style="font-size: 18px; font-weight: bold;">E-mail otomatis informasi kekurangan ekspor H-2 ETD YMPI<br>Shipment Date: {{ date('l, d M Y', strtotime($data[0]->st_date)) }}</p>
			<table style="border:1px solid black; border-collapse: collapse;">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 3%; border:1px solid black;">Cat.</th>
						<th style="width: 5%; border:1px solid black;">Material</th>
						<th style="width: 20%; border:1px solid black;">Deskripsi</th>
						<th style="width: 7%; border:1px solid black;">Dest.</th>
						<th style="width: 4%; border:1px solid black;">Plan</th>
						<th style="width: 4%; border:1px solid black;">Actual</th>
						<th style="width: 4%; border:1px solid black;">Diff</th>
					</tr>
				</thead>
				<tbody>
					@foreach($data as $col)
					@if($col->diff < 0)
					<tr>
						<td style="border:1px solid black;">{{$col->hpl}}</td>
						<td style="border:1px solid black;">{{$col->material_number}}</td>
						<td style="border:1px solid black;">{{$col->material_description}}</td>
						<td style="border:1px solid black;">{{$col->destination_shortname}}</td>
						<td style="border:1px solid black; text-align: right;">{{$col->plan}}</td>
						<td style="border:1px solid black; text-align: right;">{{$col->actual}}</td>
						<td style="border:1px solid black; text-align: right;">{{$col->diff}}</td>
					</tr>
					@endif
					@endforeach
				</tbody>
			</table>
			This is an automatic notification. Please do not reply to this address.
		</center>
	</div>
</body>
</html>