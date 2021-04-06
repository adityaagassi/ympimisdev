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
			<p style="font-size: 18px;font-weight: bold;">Your employees have already met visitors.</p>
			<p style="font-size: 15px;">Please click the confirm button to confirm the information below.</p>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<tbody>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(56, 181, 14);">Employee</td>
						<td style="width: 2%; border:1px solid black;">{{$data[0]['employees']}}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(56, 181, 14);">Department</td>
						<td style="width: 2%; border:1px solid black;">{{$data[0]['department']}}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(56, 181, 14);">Visitor Company</td>
						<td style="width: 2%; border:1px solid black;">{{$data[0]['company']}}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(56, 181, 14);">Visitor Name</td>
						<td style="width: 2%; border:1px solid black;">{{$data[0]['nama']}}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(56, 181, 14);">Confirmed At</td>
						<td style="width: 2%; border:1px solid black;">{{$data[0]['confirmed_at']}}</td>
					</tr>
				</tbody>
			</table>
		</center>
	</div>
</body>
</html>