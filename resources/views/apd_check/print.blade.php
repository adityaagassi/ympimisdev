
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
    <table style="width: 100%; border-collapse: collapse;" >
			<tbody>
				<tr>
					<td colspan="10" style="padding-top: 0px;padding-bottom: 0px;">
						<img style="width: 80px" src="{{ asset('images/logo_yamaha2.png') }}" alt="">
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;" colspan="10" class="head" style="padding-top: 0px;padding-bottom: 0px;">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">Department</td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ strtoupper($departments) }}</td>
					<td class="head" rowspan="3" colspan="6" style="padding: 15px;vertical-align: middle"><center><b>{{ $activity_name }}</b></center></td>
					<td class="head" rowspan="3" style="padding-top: 0px;padding-bottom: 0px;"><center>Checked<br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif
						<br>
						{{ $foreman }}<br>Foreman</center>
					</td>
					<td class="head" rowspan="3" style="padding-top: 0px;padding-bottom: 0px;"><center>Prepared<br>
						@if($jml_null_leader == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date_leader }}</b>
						@endif
						<br>
						{{ $leader }}<br>Leader</center>
					</td>
				</tr>
				<tr>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">Section</td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ strtoupper($subsection) }}</td>
				</tr>
				<tr>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">Bulan</td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ $monthTitle }}</td>
				</tr>
				<tr>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;font-weight: bold;"><center>Tanggal</center></td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;font-weight: bold;"><center>Nama Operator</center></td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;font-weight: bold;"><center>Proses</center></td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;font-weight: bold;"><center>Jenis APD</center></td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;font-weight: bold;"><center>Kondisi</center></td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;font-weight: bold;" colspan="4"><center>Foto Aktual</center></td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;font-weight: bold;"><center>Dicek Oleh</center></td>
				</tr>
				@foreach($apd_check as $apd_check)
				<tr>
					<td class="head" style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px;"><center>{{ $apd_check->date }}</center></td>
					<td style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px;" class="head"><center>{{ $apd_check->pic }}</center></td>
					<td style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px;" class="head"><center>{{ $apd_check->proses }}</center></td>
					<td style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px;" class="head"><center>{{ $apd_check->jenis_apd }}</center></td>
					<td style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px;" class="head"><center>{{ $apd_check->kondisi }}</center></td>
					<td style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px;text-align: center;" class="head" colspan="4"><?php echo  $apd_check->foto_aktual ?></td>
					<td style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px;" class="head"><center>{{ $apd_check->leader }}</center></td>
				</tr>
				@endforeach
			</tbody>
		</table>
</header>
<main>
</main>
</body>
</html>