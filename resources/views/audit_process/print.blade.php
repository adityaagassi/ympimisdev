
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
					<img style="width: 80px" src="{{ public_path('images/logo_yamaha2.png') }}" alt="">
				</td>
			</tr>
			<tr>
				<td style="border: 1px solid black;" colspan="10" class="head">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
			</tr>
			<tr>
				<td colspan="2" class="head">Department</td>
				<td colspan="2" class="head">{{ strtoupper($departments) }}</td>
				<td class="head" rowspan="4" colspan="5" style="vertical-align: middle"><center><b>{{ $activity_name }}</b></center></td>
				<td class="head" rowspan="4"><center>Checked<br>
					@if($jml_null == 0)
						<b style='color:green'>Approved</b><br>
						<b style='color:green'>{{ $approved_date }}</b>
					@endif
					<br>
					{{ $foreman }}<br>Foreman</center></td>
			</tr>
			<tr>
				<td colspan="2" class="head">Section</td>
				<td colspan="2" class="head">{{ strtoupper($section) }}</td>
			</tr>
			<tr>
				<td colspan="2" class="head">Product</td>
				<td colspan="2" class="head">{{ $product }}</td>
			</tr>
			<tr>
				<td colspan="2" class="head">Bulan</td>
				<td colspan="2" class="head">{{ $monthTitle }}</td>
			</tr>
			<tr>
				<td rowspan="2" class="head" style="vertical-align: middle"><center>No.</center></td>
				<td rowspan="2" class="head" style="vertical-align: middle"><center>Tanggal</center></td>
				<td rowspan="2" class="head" style="vertical-align: middle"><center>Nama Proses</center></td>
				<td rowspan="2" class="head" style="vertical-align: middle"><center>Operator</center></td>
				<td rowspan="2" class="head" style="vertical-align: middle"><center>Auditor</center></td>
				<td colspan="4" class="head" style="vertical-align: middle"><center>Point Audit</center></td>
				<td rowspan="2" class="head" style="vertical-align: middle"><center>Keterangan</center></td>
			</tr>
			<tr>
				<td class="head" style="vertical-align: middle"><center>Cara Proses</center></td>
				<td class="head" style="vertical-align: middle"><center>Kondisi Cara Proses</center></td>
				<td class="head" style="vertical-align: middle"><center>Pemahaman</center></td>
				<td class="head" style="vertical-align: middle"><center>Kondisi Pemahaman</center></td>
			</tr>
			<?php $no = 1 ?>
			@foreach($audit_process as $audit_process)
			<tr>
				<td class="head" style="vertical-align: middle"><center>{{ $no }}</center></td>
				<td class="head" style="vertical-align: middle"><center>{{ $audit_process->date }}</center></td>
				<td style="vertical-align: middle" class="head"><center>{{ $audit_process->proses }}</center></td>
				<td style="vertical-align: middle" class="head"><center>{{ $audit_process->operator }}</center></td>
				<td style="vertical-align: middle" class="head"><center>{{ $audit_process->auditor }}</center></td>
				<td style="vertical-align: middle" class="head"><?php echo $audit_process->cara_proses ?></td>
				<td style="vertical-align: middle" class="head"><center>{{ $audit_process->kondisi_cara_proses }}</center></td>
				<td style="vertical-align: middle" class="head"><?php echo $audit_process->pemahaman ?></td>
				<td style="vertical-align: middle" class="head"><center>{{ $audit_process->kondisi_pemahaman }}</center></td>
				<td style="vertical-align: middle" class="head"><center>{{ $audit_process->keterangan }}</center></td>
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