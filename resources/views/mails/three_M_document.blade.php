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
			padding: 3px;
		}
	</style>
</head>
<body>
	<div style="width: 700px;">
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">3M Document(s) Requirement (Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
			<br>
			<table style="border: hidden;">
				<tr>
					<th style="text-align: left">3M Title : </th>
					<td>{{ $data['tiga_m']->title }}</td>
				</tr>
				<tr>
					<th style="text-align: left">Product Name : </th>
					<td>{{ $data['tiga_m']->product_name }}</td>
				</tr>
				<tr>
					<th style="text-align: left">Proccess Name : </th>
					<td>{{ $data['tiga_m']->proccess_name }}</td>
				</tr>
				<tr>
					<th style="text-align: left">Unit Name : </th>
					<td>{{ $data['tiga_m']->unit }}</td>
				</tr>
				<tr>
					<th style="text-align: left">3M Category : </th>
					<td>{{ $data['tiga_m']->category }}</td>
				</tr>
				<tr>
					<th style="text-align: left">Related Department : </th>
					<td>{{ $data['tiga_m']->related_department }}</td>
				</tr>
			</table>
			<br>
			<table style="border-color: black; width: 80%;">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th colspan="5" style="background-color: #9f84a7">Document List</th>
					</tr>
					<tr style="color: white; background-color: #7e5686">
						<th style="width: 1%; border:1px solid black;">No</th>
						<th style="width: 15%; border:1px solid black;">PIC</th>
						<th style="width: 20%; border:1px solid black;">Document Name</th>
						<th style="border:1px solid black;">Note</th>
						<th style="width: 10%; border:1px solid black;">Target Date</th>
					</tr>
				</thead>
				<tbody>
					<?php $num = 1; ?>
					@foreach($data['documents'] as $doc)
					<tr>
						<td>{{ $num }}</td>
						<td>{{ $doc->pic }}</td>
						<td>{{ $doc->document_name }}</td>
						<td>{{ $doc->document_description }}</td>
						<td>{{ $doc->target }}</td>
					</tr>
					<?php $num++; ?>
					@endforeach
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url('index/sakurentsu/3m/document/upload/'.$data['form_id']) }}">Upload Document</a><br>
		</center>
	</div>
</body>
</html>