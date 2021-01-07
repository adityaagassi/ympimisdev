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
			<p style="font-size: 18px;">User documents that are about to expire</p>
			<p style="font-weight: bold;">Total Document: {{ $data['jml'] }}</p>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="70%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black;">#</th>
						<th style="width: 1%; border:1px solid black;">Category</th>
						<th style="width: 1%; border:1px solid black;">Document No.</th>
						<th style="width: 2%; border:1px solid black;">Employee ID</th>
						<th style="width: 2%; border:1px solid black;">Name</th>
						<th style="width: 2%; border:1px solid black;">Valid To</th>
						<th style="width: 2%; border:1px solid black;">Valid From</th>
						<th style="width: 2%; border:1px solid black;">Condition</th>
						<th style="width: 4%; border:1px solid black;">Active</th>
					</tr>
				</thead>
				<tbody>

					@php $i = 1; @endphp
					
					@foreach($data['user_documents'] as $col)
					<tr>
						<td style="border:1px solid black;">{{ $i++ }}</td>
						<td style="border:1px solid black;">{{ $col->category }}</td>
						<td style="border:1px solid black;">{{ $col->document_number }}</td>
						<td style="border:1px solid black;">{{ $col->employee_id }}</td>
						<td style="border:1px solid black;">{{ $col->name }}</td>
						<td style="border:1px solid black;">{{ $col->valid_from }}</td>
						<td style="border:1px solid black;">{{ $col->valid_to }}</td>
						<td style="border:1px solid black;">{{ $col->condition }}</td>
						<td style="border:1px solid black; text-align: right;">{{ $col->diff }} Days Remaining</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			<br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url("user_document") }}">Users Document Details</a><br>

		</center>
	</div>
</body>
</html>