
<!DOCTYPE html>
<html>
<head>
  <title>YMPI 情報システム</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <style type="text/css">
    body{
      font-size: 10px;
      font-family: Calibri, sans-serif; 
    }

    #isi > thead > tr > td {
      text-align: center;
    }

    #isi > tbody > tr > td {
	/*      text-align: left;
	padding-left: 5px;*/
	text-align: center
	}

	table, th, td {
	  border: 1px solid black;
	  border-collapse: collapse;
	  vertical-align:middle;
	}

@page { }
.footer { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 200px;text-align: center;}
.footer .pagenum:before { content: counter(page); }
</style>
</head>
<body>
  <header>
    <table style="width: 100%; border-collapse: collapse; text-align: left;" >
			<tbody>
				<tr>
					<td colspan="8" style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;"><img width="80px" src="{{ public_path('images/logo_yamaha2.png') }}" alt=""></td>
				</tr>
				<tr>
					<td colspan="8" style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">Department</td>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ strtoupper($departments) }}</td>
					<td class="head" rowspan="3" colspan="2" style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px;"><center><b>{{ $activity_name }}</b></center></td>
					<td class="head" rowspan="3" style="padding-top: 0px;padding-bottom: 0px;"><center>Checked<br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif
						<br>
						{{ $foreman }}<br>Foreman</center></td>
					<td class="head" rowspan="3" style="padding-top: 0px;padding-bottom: 0px;"><center>Prepared<br>
						@if($approval_leader != Null)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date_leader }}</b>
						@endif
						<br>
						{{ $leader }}<br>Leader</center></td>
				</tr>
				<tr>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">Section</td>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ strtoupper($section) }}</td>
				</tr>
				<tr>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">Bulan</td>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ $monthTitle }}</td>
				</tr>
				<tr>
					<td class="head" rowspan="2"><center><b>No.</b></center></td>
					<td class="head" rowspan="2"><center><b>Date</b></center></td>
					<td class="head" rowspan="2"><center><b>Nama Dokumen</b></center></td>
					<td class="head" rowspan="2"><center><b>No. Dokumen</b></center></td>
					<td class="head" colspan='3'><center><b>Hasil Audit IK</b></center></td>
					<td class="head" rowspan="2"><center><b>Operator</b></center></td>
				</tr>
				<tr>
					<td class="head"><center><b>Kesesuaian dengan Aktual Proses</b></center></td>
					<td class="head"><center><b>Kelengkapan Point Safety</b></center></td>
					<td class="head"><center><b>Kesesuaian QC Kouteihyo</b></center></td>
				</tr>
				<?php $no = 1 ?>
				@foreach($laporanAktivitas as $laporanAktivitas)
				<tr>
					<td class="head" rowspan="3" style="padding-top: 0px;padding-bottom: 0px;"><center>{{ $no }}</center></td>
					<td class="head" rowspan="3" style="padding-top: 0px;padding-bottom: 0px;"><center>{{ $laporanAktivitas->date }}</center></td>
					<td class="head" rowspan="3" style="padding-top: 0px;padding-bottom: 0px;"><center>{{ $laporanAktivitas->nama_dokumen }}</center></td>
					<td class="head" rowspan="3" style="padding-top: 0px;padding-bottom: 0px;"><center>{{ $laporanAktivitas->no_dokumen }}</center></td>
					<td class="head" style="text-align: center;padding-top: 0px;padding-bottom: 0px;"><?php echo $laporanAktivitas->kesesuaian_aktual_proses ?></td>
					<td class="head" rowspan="3" style="text-align: center;padding-top: 0px;padding-bottom: 0px;">{{ $laporanAktivitas->kelengkapan_point_safety }}</td>
					<td class="head" rowspan="3" style="text-align: center;padding-top: 0px;padding-bottom: 0px;">{{ $laporanAktivitas->kesesuaian_qc_kouteihyo }}</td>
					<td class="head" rowspan="3" style="padding-top: 0px;padding-bottom: 0px;"><center>{{ $laporanAktivitas->operator }}</center></td>
				</tr>
				<tr>
					<td style="padding-top: 0px;padding-bottom: 0px;">Tindakan Perbaikan : {{ $laporanAktivitas->tindakan_perbaikan }}</td>
				</tr>
				<tr>
					<td style="padding-top: 0px;padding-bottom: 0px;">Target : {{ $laporanAktivitas->target }}</td>
				</tr>
				<?php $no++ ?>
				@endforeach
				
			</tbody>
		</table>
</header>
<main>
</main>
</body>
</html>



