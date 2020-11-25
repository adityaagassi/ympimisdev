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
			<p style="font-size: 18px;">Unanswered HRq Question and Answer (Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
			<br>
			<table style="border-color: black; width: 50%;">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th colspan="2" style="background-color: #9f84a7">Unanswered Question</th>
					</tr>
					<tr style="color: white; background-color: #7e5686">
						<th style="width: 1%; border:1px solid black;">Category</th>
						<th style="width: 1%; border:1px solid black;">Total</th>
					</tr>
				</thead>
				<tbody>
					@foreach($data as $val)
					<tr>
						<td>{{ $val->category }}</td>
						<td>{{ $val->unanswer }}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="http://10.109.52.4/mirai/public/index/qnaHR">HR Q&A</a><br>
		</center>
	</div>
</body>
</html>