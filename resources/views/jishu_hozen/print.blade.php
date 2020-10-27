
<!DOCTYPE html>
<html>
<head>
  <link rel="shortcut icon" type="image/x-icon" href="{{ url("logo_mirai.png")}}" />
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="width=device-width, user-scalable=yes, initial-scale=1.0" name="viewport">
  <title>YMPI 情報システム</title>
  <style type="text/css">
    body{
      /*font-size: 10px;*/
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

table > tbody > tr > td{
  border: 1px solid black;
  border-collapse: collapse;
  padding: 5px;
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
					<td colspan="8" style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;"><img width="80px" src="{{ asset('images/logo_yamaha2.png') }}" alt=""></td>
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;" colspan="8"  class="head">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">Department</td>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ strtoupper($departments) }}</td>
					<td class="head" rowspan="4" colspan="2" style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px;"><center><b>{{ $activity_name }}</b></center></td>
					<td class="head" rowspan="4" style="padding-top: 0px;padding-bottom: 0px;"><center>Checked<br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif
						<br>
						{{ $foreman }}<br>Foreman</center></td>
					<td class="head" rowspan="4" style="padding-top: 0px;padding-bottom: 0px;"><center>Prepared<br>
						@if($jml_null_leader == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date_leader }}</b>
						@endif
						<br>
						{{ $leader }}<br>Leader</center></td>
				</tr>
				<tr>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">Sub Section</td>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ strtoupper($subsection) }}</td>
				</tr>
				<tr>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">Bulan</td>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ $monthTitle }}</td>
				</tr>
				<tr>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">Date</td>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ $date }}</td>
				</tr>
				@foreach($jishu_hozen as $jishu_hozen)
				<tr>
					<td class="head" colspan="8" style="vertical-align: middle"><center>Picture<br><?php echo $jishu_hozen->foto_aktual ?></center></td>
				</tr>
				@endforeach
			</tbody>
		</table>
</header>
<main>
</main>
</body>
</html>
