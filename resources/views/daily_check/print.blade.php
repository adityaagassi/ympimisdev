
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
					<td colspan="6" style="border: 1px solid black;"><img width="80px" src="{{ asset('images/logo_yamaha2.png') }}" alt=""></td>
				</tr>
				<tr>
					<td style="border: 1px solid black;vertical-align: middle;" colspan="6">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">Department</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">{{ strtoupper($departments) }}</td>
					<td rowspan="3" colspan="2" style="border: 1px solid black;padding: 15px;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;"><center><b>{{ $activity_name }}</b></center></td>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;" rowspan="3"><center>Checked<br><br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif<br>
					{{ $foreman }}<br>Foreman</center></td>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;" rowspan="3"><center>Prepared<br><br>
						@if($jml_null_leader == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date_leader }}</b>
						@endif<br>
						{{ $leader }}<br>Leader</center></td>
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">Product</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">{{ $product }}</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">Month</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">{{ $monthTitle }}</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;font-weight: bold;"><center>No.</center></td>
					<td style="border: 1px solid black;font-weight: bold;"><center>Production Date</center></td>
					<td style="border: 1px solid black;font-weight: bold;"><center>Check Date</center></td>
					<td style="border: 1px solid black;font-weight: bold;"><center>Serial Number</center></td>
					<td style="border: 1px solid black;font-weight: bold;"><center>Condition</center></td>
					<td style="border: 1px solid black;font-weight: bold;"><center>Keterangan</center></td>
				</tr>
				<?php $no = 1; ?>
				@foreach($daily_check as $daily_check)
				<tr>
					<td style="border: 1px solid black;"><center>{{ $no }}</center></td>
					<td style="border: 1px solid black;"><center>{{ $daily_check->production_date }}</center></td>
					<td style="border: 1px solid black;"><center>{{ $daily_check->check_date }}</center></td>
					<td style="border: 1px solid black;"><center>{{ $daily_check->serial_number }}</center></td>
					<td style="border: 1px solid black;"><center>{{ $daily_check->condition }}</center></td>
					<td style="border: 1px solid black;"><center>{{ $daily_check->keterangan }}</center></td>
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



