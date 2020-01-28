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
			<p style="font-size: 18px;">NG Report of Push Pull & Camera Stamp Check Recorder リコーダープッシュプールチェック</p>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<tbody>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(126,86,134);">Location</td>
						<td style="width: 2%; border:1px solid black;">{{ $data['remark'] }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(126,86,134);">Color</td>
						<td style="width: 2%; border:1px solid black;">{{ $data['model'] }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(126,86,134);">Checked At</td>
						<td style="width: 2%; border:1px solid black;">{{ $data['checked_at'] }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(126,86,134);">Value</td>
						<td style="width: 2%; border:1px solid black;">{{ $data['value'] }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(126,86,134);">Judgement</td>
						<td style="width: 2%; border:1px solid black;">{{ $data['judgement'] }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(126,86,134);">PIC Check</td>
						<td style="width: 2%; border:1px solid black;">{{ $data['pic_check'] }}</td>
					</tr>
				</tbody>
			</table>
			<br>
		</center>
	</div>
</body>
</html>