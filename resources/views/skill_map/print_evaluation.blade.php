<!DOCTYPE html>
<html>
<head>
	<title>YMPI 情報システム</title>
	<!-- <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> -->
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
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
			padding: 5px;
		}

		.centera{
			text-align: center;
			vertical-align: middle !important;
		}

		@font-face {
	      font-family: Calibri;
	      font-style: normal;
	      font-weight: 400;
	    }

	    * {
	      font-family: Calibri;
	    }

	    input[type=radio] { display: inline; }
		input[type=radio]:before { font-family: DejaVu Sans; }


	    /*@font-face {
		  font-family:"ヒラギノ角ゴ Pro W3", "Hiragino Kaku Gothic Pro",Osaka, "メイリオ", Meiryo, "ＭＳ Ｐゴシック", "MS PGothic", sans-serif;
	      font-style: normal;
	      font-weight: 400;
	    }*/


	    .droid {
	        font-family: ipag;
	    }


		/*@import url('https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin-ext');*/


		@page { }
        .footer { position: fixed; left: 0px; bottom: -30px; right: 0px; height: 170px;text-align: center;}
        .footer .pagenum:before { content: counter(page); }
	</style>
</head>

<body>
	<header>
		<table style="width: 100%; border-collapse: collapse; text-align: left;">
			<thead>
				<tr>
					<td colspan="10" style="font-size: 14px">PT. Yamaha Musical Products Indonesia</td>
				</tr>
				<tr>
					<td colspan="10"><br></td>
				</tr>
				<tr>
					<td colspan="10" style="text-align: center;font-size: 20px;font-weight: bold">EVALUASI SKILL OPERATOR</td>
				</tr>
				<tr>
					<td colspan="10"><br></td>
				</tr>
				<tr>
					<td colspan="10"><br></td>
				</tr>
				<tr>
					<td style="font-size: 13px;width: 16%">Tanggal Evaluasi</td>
					<td colspan="9" style="font-size: 13px;color: blue;font-weight: bold">: <?= date('d M Y', strtotime($detail['created_at'])) ?></td>
				</tr>

				<tr>
					<td style="font-size: 13px;width: 16%">Proses Pekerjaan</td>
					<td colspan="9" style="font-size: 13px;color: blue;font-weight: bold">: {{$detail['process']}}</td>
				</tr>
				<tr>
					<td colspan="10"></td>
				</tr>
			</thead>
		</table>
	</header>

	<main style="padding-top: 20px">
		<table style="table-layout: fixed; width: 100%; border-collapse: collapse;font-size: 11px" id="isi"  border="1">
			<thead>
				<tr>
					<th rowspan="3"><center>No.</center></th>
					<th rowspan="3"><center>NIK</center></th>
					<th rowspan="3"><center>Nama Operator</center></th>
					<th colspan="6"><center>Parameter Penilaian</center></th>
					<th rowspan="3"><center>Rata-Rata Nilai Evaluasi</center></th>
				</tr>
				<tr>
					<th><center>1</center></th>
					<th><center>2</center></th>
					<th><center>3</center></th>
					<th><center>4</center></th>
					<th><center>5</center></th>
					<th><center>6</center></th>
				</tr>
				<tr>
					<th><center>Bisa Mengerti & Melaksanakan Sesuai Urutan IK</center></th>
					<th><center>Kualitas Hasil Sesuai Standard Kualitas Proses</center></th>
					<th><center>Dapat Menyelesaikan Pekerjaan Sesuai Standard Waktu</center></th>
					<th><center>Pemahaman Jishu Hozen</center></th>
					<th><center>Pemahaman Potensi Bahaya Tempat Kerja</center></th>
					<th><center>Pemahaman Handling Bahan Kimia</center></th>
				</tr>
			</thead>
			<tbody>
				<?php $no = 1; ?>
				<tr>
					<td><center>{{$no}}</center></td>
					<td><center>{{$detail['employee_id']}}</center></td>
					<td><center>{{$detail['name']}}</center></td>
					<td><center>{{$detail['evaluation_value'][0]}}</center></td>
					<td><center>{{$detail['evaluation_value'][1]}}</center></td>
					<td><center>{{$detail['evaluation_value'][2]}}</center></td>
					<td><center>{{$detail['evaluation_value'][3]}}</center></td>
					<td><center>{{$detail['evaluation_value'][4]}}</center></td>
					<td><center>{{$detail['evaluation_value'][5]}}</center></td>
					<td><center>{{$detail['average']}}</center></td>
				</tr>
				<?php $no++; ?>
			</tbody>
		</table>
		<table style="table-layout: fixed;width: 100%; font-family: arial; border-collapse: collapse; text-align: center;font-size: 12px;padding-top: 20px">
			<thead>
				<tr>
					<th style="border: 1px solid black;width: 10%">Skor</th>
					<th style="border: 1px solid black;width: 60%">Level Skill</th>
					<th rowspan="5" style="width: 1%"></th>
					<th style="border: 1px solid black">Check By</th>
					<th style="border: 1px solid black">Prepared By</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="border: 1px solid black">1</td>
					<td style="border: 1px solid black">Bisa bekerja untuk mencapai mutu level standar, sambil menerima bimbingan</td>
					<td style="border: 1px solid black" rowspan="2"></td>
					<td style="border: 1px solid black" rowspan="2"></td>
				</tr>
				<tr>
					<td style="border: 1px solid black">2</td>
					<td style="border: 1px solid black">Bisa bekerja untuk mencapai mutu & efisiensi level standar, tapi memerlukan bimbingan sesuai kondisi </td>
				</tr>
				<tr>
					<td style="border: 1px solid black">3</td>
					<td style="border: 1px solid black">Bisa bekerja untuk mencapai mutu & efisiensi level standar, ditambah bisa menilai kondisi abnormal</td>
					<td style="border: 1px solid black">Foreman</td>
					<td style="border: 1px solid black">Leader</td>
				</tr>
				<tr>
					<td style="border: 1px solid black">4</td>
					<td style="border: 1px solid black">Mengetahui dengan baik isi pekerjaan, bisa membimbing dan menangani di saat darurat</td>
					<td></td>
					<td></td>
				</tr>
			</tbody>
		</table>
	</main>
	<footer>
		<div class="footer">
			
	    </div>
	</footer>
</body>
</html>