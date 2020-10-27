
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
    <table style="width: 100%; border-collapse: collapse; " >
			<tbody>
				<tr>
					<td colspan="5" style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;"><img width="80px" src="{{ asset('images/logo_yamaha2.png') }}" alt=""></td>
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;" colspan="5" class="head">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">Department</td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ strtoupper($departments) }}</td>
					<td class="head" rowspan="8" style="vertical-align: middle"><center><b>{{ $activity_name }}</b></center></td>
					<td class="head" rowspan="8" style="padding-top: 0px;padding-bottom: 0px;"><center>Checked<br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif
						<br>
						{{ $foreman }}<br>Foreman</center></td>
					<td class="head" rowspan="8" style="padding-top: 0px;padding-bottom: 0px;"><center>Prepared<br>
						@if($jml_null_leader == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date_leader }}</b>
						@endif
						<br>
						{{ $leader }}<br>Leader</center></td>
				</tr>
				<tr>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">Sub Section</td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ strtoupper($subsection) }}</td>
				</tr>
				<tr>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">Proses</td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ $proses }}</td>
				</tr>
				<tr>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">Jenis</td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ $jenis }}</td>
				</tr>
				<tr>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">Standar Kualitas</td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ $standar_kualitas }}</td>
				</tr>
				<tr>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">Tool Check</td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ $tool_check }}</td>
				</tr>
				<tr>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">Jumlah Cek</td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ $jumlah_cek }}</td>
				</tr>
				<tr>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">Bulan</td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ $monthTitle }}</td>
				</tr>
				<tr>
					<th style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px;font-weight: bold;"><center>Date</center></th>
					<th style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px;font-weight: bold;"><center>Judgement</center></th>
					<th style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px;font-weight: bold;"><center>Note</center></th>
					<th style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px;font-weight: bold;"><center>PIC</center></th>
					<th style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px;font-weight: bold;"><center>Auditor</center></th>
				</tr>
				@foreach($first_product_audit as $first_product_audit)
				<tr>
					<td class="head" style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px;"><center>{{ $first_product_audit->date }}</center></td>
					<td class="head" style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px;"><center>{{ $first_product_audit->judgement }}</center></td>
					<td class="head" style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px;"><center><?php echo $first_product_audit->note ?></center></td>
					<td class="head" style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px;"><center>{{ $first_product_audit->pic }}</center></td>
					<td class="head" style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px;"><center>{{ $first_product_audit->auditor }}</center></td>
				</tr>
				@endforeach
			</tbody>
		</table>
</header>
<main>
</main>
</body>
</html>



