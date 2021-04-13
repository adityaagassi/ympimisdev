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
			@foreach($data as $datas)
				<?php $id = $datas->id ?>
				<?php $nama = $datas->nama ?>
				<?php $nik = $datas->nik ?>
				<?php $sub_group = $datas->sub_group ?>
				<?php $ke_sub_group = $datas->ke_sub_group ?>
				<?php $group = $datas->group ?>
				<?php $ke_group = $datas->ke_group ?>
				<?php $seksi = $datas->seksi ?>
				<?php $ke_seksi = $datas->ke_seksi ?>
				<?php $departemen = $datas->departemen ?>
				<?php $jabatan = $datas->jabatan ?>
				<?php $rekomendasi = $datas->rekomendasi ?>
				<?php $tanggal = $datas->tanggal ?>
				<?php $alasan = $datas->alasan ?>
			@endforeach
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">
			<br>(Last Update: {{ date('d-M-Y H:i:s') }})
			</p>
			This is an automatic notification. Please do not reply to this address.

			<h1>Mutasi Satu Departemen : </h1>
              <table style="width: 100%; font-family: arial; border-collapse: collapse; text-align: left;">
              <thead>
              <tr>
              <td colspan="2" style="font-size: 12px;width: 22%; font-weight: bold">Nama</td>
              <td colspan="10" style="font-size: 12px;">: {{$nama}}</td>
              </tr>
              <tr>
                <td colspan="2" style="font-size: 12px;width: 22%; font-weight: bold">NIK</td>
                <td colspan="10" style="font-size: 12px;">: {{$nik}}</td>
              </tr>
              <tr>
                <td colspan="2">&nbsp;</td>
                <td colspan="7" style="text-align: center;font-weight: bold;font-size: 20px"></td>
              </tr>
              <tr>
                <th colspan="3" style="font-size: 12px; background-color: rgb(126,86,134); border: 1px solid black; text-align: center">Detail</th>
                <th colspan="5" style="font-size: 12px; background-color: rgb(126,86,134); border: 1px solid black; text-align: center">Asal</th>
                <th colspan="5" style="font-size: 12px; background-color: rgb(126,86,134); border: 1px solid black ;text-align: center">Tujuan</th>
              </tr>
              <tr align="center">
                <td colspan="3" style="font-size: 12px; border: 1px solid black; font-weight: bold">Sub Group</td>
                <td colspan="5" style="font-size: 12px; border: 1px solid black">{{$sub_group}}</td>
                <td colspan="5" style="font-size: 12px; border: 1px solid black">{{$ke_sub_group}}</td>
              </tr>
              <tr align="center">
                <td colspan="3" style="font-size: 12px; border: 1px solid black; font-weight: bold">Group</td>
                <td colspan="5" style="font-size: 12px; border: 1px solid black">{{$group}}</td>
                <td colspan="5" style="font-size: 12px; border: 1px solid black">{{$ke_group}}</td>
              </tr>
              <tr align="center">
                <td colspan="3" style="font-size: 12px; border: 1px solid black; font-weight: bold">Seksi</td>
                <td colspan="5" style="font-size: 12px; border: 1px solid black">{{$seksi}}</td>
                <td colspan="5" style="font-size: 12px; border: 1px solid black">{{$ke_seksi}}</td>
              </tr>
              <tr align="center">
                <td colspan="3" style="font-size: 12px; border: 1px solid black; font-weight: bold">Departemen</td>
                <td colspan="10" style="font-size: 12px; border: 1px solid black">{{$departemen}}</td>
              </tr>
              <tr align="center">
                <td colspan="3" style="font-size: 12px; border: 1px solid black; font-weight: bold">Jabatan</td>
                <td colspan="10" style="font-size: 12px; border: 1px solid black">{{$jabatan}}</td>
              </tr>
              <tr>
                <td colspan="2">&nbsp;</td>
                <td colspan="11" style="text-align: center;font-weight: bold;font-size: 20px"></td>
              </tr>
              <tr>
                <td colspan="2" style="font-size: 12px;width: 22%; font-weight: bold">Rekomendasi Atasan</td>
                <td colspan="10" style="font-size: 12px; font-weight: bold">: {{$rekomendasi}}</td>
              </tr>
              <tr>
                <td colspan="2" style="font-size: 12px;width: 22%; font-weight: bold">Tanggal Mutasi</td>
                <td colspan="10" style="font-size: 12px;">: {{$tanggal}}</td>
              </tr>
              <tr>
                <td colspan="2" style="font-size: 12px;width: 22%; font-weight: bold">Alasan</td>
                <td colspan="10" style="font-size: 12px;">: {{$alasan}}</td>
              </tr>
            </thead>
        	</table>
          <br>
          <span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
          <a href="{{ url('mutasi/hr') }}">Data Mutasi</a><br>
		</center>
	</div>
</body>
</html>