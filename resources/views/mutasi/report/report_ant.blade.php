<!DOCTYPE html>
<html>
<head>
	<title>YMPI 情報システム</title>
	<!-- <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> -->
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, user-scalable=yes, initial-scale=1.0" name="viewport">
	<style type="text/css">
		body{
			font-size: 10px;
		}

		#isi > thead > tr > td {
			text-align: center;
		}

		#isi > tbody > tr > td {
			text-align: left;
			padding-left: 5px;
		}

		/*@font-face {
	      font-family: 'Firefly Sung';
	      font-style: normal;
	      font-weight: 400;
	    }
	    * {
	      font-family: Firefly Sung, DejaVu Sans, sans-serif;
	    }*/

	    * {
	      font-family: arial;
	    }

	    .page-break {
			page-break-after: always;
		}

		@page { }
        .footer { position: fixed; left: 0px; bottom: 100px; right: 0px; height: 130px;text-align: center;}
        .footer .pagenum:before { content: counter(page); }
	</style>
</head>

<body>

	<header>
		<table style="width: 100%; font-family: arial; border-collapse: collapse; text-align: left;">
			<thead>
				<tr>
					<td colspan="12" style="font-weight: bold;font-size: 8px">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
					<td>
					@if ($pr[0]->status == "Rejected")
					<span style="font-weight: bold; background-color: red;color: white;float: right; font-size: 15px"><i>Rejected</i></span>
					@elseif ($pr[0]->status == "All Approved")
					<span style="font-weight: bold; background-color: green;color: white;float: right; font-size: 15px"><i>Approved</i></span>
					@endif</td>
				</tr>
				<tr>
					<td colspan="12"><br></td>
				</tr>				
				<tr>
					<td colspan="2">&nbsp;</td>
					<td colspan="8" style="text-align: center;font-weight: bold;font-size: 20px">FORM MUTASI</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
					<td colspan="8" style="text-align: center;font-weight: bold;font-size: 20px">(Antar Departemen)</td>
				</tr>
				<tr>
					<td colspan="12"><br></td>
				</tr>	
				<tr>
					<td colspan="2" style="font-size: 12px;width: 22%; font-weight: bold">Nama</td>
					<td colspan="10" style="font-size: 12px;">: {{ $pr[0]->nama }}</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 12px;width: 22%; font-weight: bold">NIK</td>
					<td colspan="10" style="font-size: 12px;">: {{ $pr[0]->nik }}</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
					<td colspan="7" style="text-align: center;font-weight: bold;font-size: 20px"></td>
				</tr>
				<tr align="center">
					<th colspan="3" style="font-size: 12px; background-color: green; border: 1px solid black">Detail</th>
					<th colspan="5" style="font-size: 12px; background-color: green; border: 1px solid black">Asal</th>
					<th colspan="5" style="font-size: 12px; background-color: green; border: 1px solid black">Tujuan</th>
				</tr>
				<tr align="center">
					<td colspan="3" style="font-size: 12px; border: 1px solid black; font-weight: bold">Sub Group</td>
					<td colspan="5" style="font-size: 12px; border: 1px solid black">{{ $pr[0]->sub_group }}</td>
					<td colspan="5" style="font-size: 12px; border: 1px solid black">{{ $pr[0]->ke_sub_group }}</td>
				</tr>
				<tr align="center">
					<td colspan="3" style="font-size: 12px; border: 1px solid black; font-weight: bold">Group</td>
					<td colspan="5" style="font-size: 12px; border: 1px solid black">{{ $pr[0]->group }}</td>
					<td colspan="5" style="font-size: 12px; border: 1px solid black">{{ $pr[0]->ke_group }}</td>
				</tr>
				<tr align="center">
					<td colspan="3" style="font-size: 12px; border: 1px solid black; font-weight: bold">Seksi</td>
					<td colspan="5" style="font-size: 12px; border: 1px solid black">{{ $pr[0]->seksi }}</td>
					<td colspan="5" style="font-size: 12px; border: 1px solid black">{{ $pr[0]->ke_seksi }}</td>
				</tr>
				<tr align="center">
					<td colspan="3" style="font-size: 12px; border: 1px solid black; font-weight: bold">Departemen</td>
					<td colspan="5" style="font-size: 12px; border: 1px solid black">{{ $pr[0]->departemen }}</td>
					<td colspan="5" style="font-size: 12px; border: 1px solid black">{{ $pr[0]->ke_departemen }}</td>
				</tr>
				<tr align="center">
					<td colspan="3" style="font-size: 12px; border: 1px solid black; font-weight: bold">Jabatan</td>
					<td colspan="10" style="font-size: 12px; border: 1px solid black">{{ $pr[0]->jabatan }}</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
					<td colspan="11" style="text-align: center;font-weight: bold;font-size: 20px"></td>
				</tr>
				<!-- <tr>
					<td colspan="2" style="font-size: 12px;width: 22%">Sub Group</td>
					<td colspan="10" style="font-size: 12px;">: {{ $pr[0]->sub_group }}</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 12px;width: 22%">Group</td>
					<td colspan="10" style="font-size: 12px;">: {{ $pr[0]->group }}</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 12px;width: 22%">Seksi</td>
					<td colspan="10" style="font-size: 12px;">: {{ $pr[0]->seksi }}</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 12px;width: 22%">Departemen</td>
					<td colspan="10" style="font-size: 12px;">: {{ $pr[0]->departemen }}</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 12px;width: 22%">Jabatan</td>
					<td colspan="10" style="font-size: 12px;">: {{ $pr[0]->jabatan }}</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 12px;width: 22%">Rekomendasi Atasan</td>
					<td colspan="10" style="font-size: 12px;">: {{ $pr[0]->rekomendasi }}</td>
				</tr> -->
				<!-- <tr>
					<td colspan="2">&nbsp;</td>
					<td colspan="7" style="text-align: center;font-weight: bold;font-size: 20px"></td>
				</tr> -->
				<!-- <tr>
					<td colspan="2" style="font-size: 12px;width: 22%">Sub Group</td>
					<td colspan="10" style="font-size: 12px;">: {{ $pr[0]->ke_sub_group }}</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 12px;width: 22%">Group</td>
					<td colspan="10" style="font-size: 12px;">: {{ $pr[0]->ke_group }}</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 12px;width: 22%">Seksi</td>
					<td colspan="10" style="font-size: 12px;">: {{ $pr[0]->ke_seksi }}</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 12px;width: 22%">Departemen</td>
					<td colspan="10" style="font-size: 12px;">: {{ $pr[0]->ke_departemen }}</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 12px;width: 22%">Jabatan</td>
					<td colspan="10" style="font-size: 12px;">: {{ $pr[0]->ke_jabatan }}</td>
				</tr> -->
				<tr>
					<td colspan="2" style="font-size: 12px;width: 22%; font-weight: bold">Rekomendasi Atasan</td>
					<td colspan="10" style="font-size: 12px;">: {{ $pr[0]->rekomendasi }}</td>
				</tr> -->
				<tr>
					<td colspan="2" style="font-size: 12px;width: 22%; font-weight: bold">Tanggal Mutasi</td>
					<td colspan="10" style="font-size: 12px;">: <?= date('d-M-Y', strtotime($pr[0]->tanggal)) ?></td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 12px;width: 22%; font-weight: bold">Alasan</td>
					<td colspan="10" style="font-size: 12px;">: {{ $pr[0]->alasan }}</td>
				</tr>
			</thead>
		</table>
		<br>
		<table style="width: 100%; font-family: arial; border-collapse: collapse; ">
				<tr style="background-color: rgb(126,86,134);">
						<th colspan="1" style="font-size: 9px; width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">10</th>
                        <th colspan="1" style="font-size: 9px; width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">9</th>
                        <th colspan="1" style="font-size: 9px; width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">8</th>
                        <th colspan="1" style="font-size: 9px; width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">7</th> 
                        <th colspan="1" style="font-size: 9px; width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">6</th>
                        <th colspan="1" style="font-size: 9px; width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">5</th>
                        <th colspan="1" style="font-size: 9px; width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">4</th>
                        <th colspan="1" style="font-size: 9px; width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">3</th>
                        <th colspan="1" style="font-size: 9px; width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">2</th> 
                        <th colspan="1" style="font-size: 9px; width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">1</th>
				</tr>
				<tr style="background-color: rgb(126,86,134);">
						<th colspan="2" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">HR</th>
						<th colspan="4" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">Tujuan</th>
						<th colspan="4" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">Asal</th>
				</tr>
				<tr style="background-color: rgb(126,86,134);">
						<th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">Direktur</th>
						<th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">Manager</th>
						<th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">GM</th>
						<th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">DGM</th>
						<th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">Manager</th>
						<th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">Chief/Foreman</th>
                        <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">GM</th>
                        <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">DGM</th>
                        <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">Manager</th> 
                        <th colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black; text-align: center;">Chief/Foreman</th>
				</tr>
				<tr>
						<td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $pr[0]->nama_direktur_hr }}</td>
						<td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $pr[0]->nama_manager }}</td>
						<td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $pr[0]->nama_gm_tujuan }}</td>
						<td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $pr[0]->nama_dgm_tujuan }}</td>
						<td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $pr[0]->nama_manager_tujuan }}</td>
						<td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $pr[0]->nama_chief_tujuan }}</td>
						<td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $pr[0]->nama_gm_asal }}</td>
						<td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $pr[0]->nama_dgm_asal }}</td>
						<td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $pr[0]->nama_manager_asal }}</td>
						<td style="height: 40px; width: 6%; border:1px solid black; text-align: center; font-weight: bold">{{ $pr[0]->nama_chief_asal }}</td>
				</tr>
				<tr>
						<td style="width: 6%; border:1px solid black; text-align: center;">{{$pr[0]->date_direktur_hr}}</td>
						<td style="width: 6%; border:1px solid black; text-align: center;">{{$pr[0]->date_manager_hrga}}</td>
						<td style="width: 6%; border:1px solid black; text-align: center;">{{$pr[0]->date_gm_tujuan}}</td>
						<td style="width: 6%; border:1px solid black; text-align: center;">{{$pr[0]->date_dgm_tujuan}}</td>
						<td style="width: 6%; border:1px solid black; text-align: center;">{{$pr[0]->date_manager_tujuan}}</td>
						<td style="width: 6%; border:1px solid black; text-align: center;">{{$pr[0]->date_atasan_tujuan}}</td>
						<td style="width: 6%; border:1px solid black; text-align: center;">{{$pr[0]->date_gm_asal}}</td>
						<td style="width: 6%; border:1px solid black; text-align: center;">{{$pr[0]->date_dgm_asal}}</td>
						<td style="width: 6%; border:1px solid black; text-align: center;">{{$pr[0]->date_manager_asal}}</td>
						<td style="width: 6%; border:1px solid black; text-align: center;">{{$pr[0]->date_atasan_asal}}</td>
				</tr>
			</table>
	</header>
</body>
</html>
