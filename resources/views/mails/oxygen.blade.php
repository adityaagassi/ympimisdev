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
			<p style="font-size: 20px; font-weight: bold;">Notifikasi Kadar Oksigen dibawah <b>95</b></p>
			This is an automatic notification. Please do not reply to this address.
			<p style="padding: 0px !important; font-size: 15px"></p>
			<table style="border:1px solid black; border-collapse: collapse;" width="70%">
				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">NIK</th>
					<td style="width: 50%; border:1px solid black;">{{ $data->employee_id }}</td>
				</tr>
				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Nama Karyawan</th>
					<td style="width: 50%; border:1px solid black;">{{ $data->name }}</td>
				</tr>
				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Section Karyawan</th>
					<td style="width: 50%; border:1px solid black;">{{ $data->section }}</td>
				</tr>
				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Kadar Oksigen</th>
					<td style="width: 50%; border:1px solid black;">{{ $data->remark }}</td>
				</tr>
				<tr>
					<th style="width: 50%; border:1px solid black; background-color: #c2ff7d;">Denyut Nadi</th>
					<td style="width: 50%; border:1px solid black;">{{ $data->remark2 }}</td>
				</tr>
			</table>
			<br>
			<br>
			<p style="font-size: 20px;font-weight: bold; color: #f5624e">
				Mohon untuk dipastikan kembali, <br>
				Jika terdapat kesalahan input atau pengecekkan belum akurat, mohon diulang kembali. <br>
				Jika kadar oksigen memang di bawah 95, mohon untuk diarahkan ke Klinik YMPI.
			</p>
			
		</center>
	</div>
</body>
</html>