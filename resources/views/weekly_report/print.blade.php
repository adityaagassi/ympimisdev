
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
				<td colspan="6" style="padding-top: 0px;padding-bottom: 0px;">
					<img style="width: 80px" src="{{ asset('images/logo_yamaha2.png') }}" alt="">
				</td>
			</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;" colspan="6" class="head">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">Department</td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ strtoupper($departments) }}</td>
					<td class="head" rowspan="3" colspan="2" style="padding: 16px;vertical-align: middle"><center><b>{{ $activity_name }}</b></center></td>
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
					<td class="head"><center><b>Tanggal</b></center></td>
					<td class="head"><center><b>Tinjauan 4M</b></center></td>
					<td class="head"><center><b>Aktivitas</b></center></td>
					<td class="head"><center><b>Action</b></center></td>
					<td class="head" colspan="2"><center><b>Foto Aktual</b></center></td>
				</tr>
				@foreach($weekly_report as $weekly_report)
				<?php $type = [] ?>
				<tr>
					<td class="head" style="vertical-align: middle"><center>{{ $weekly_report->date }}</center></td>
					<td style="vertical-align: middle" class="head"><center><?php $tinjauan = explode(',', $weekly_report->report_type);
						for ($i = 0; $i < count($tinjauan); $i++) {
						 	if($tinjauan[$i] == 1){
						 		$type[] = 'Man';
						 	}elseif ($tinjauan[$i] == 2) {
						 		$type[] = 'Machine';
						 	}elseif ($tinjauan[$i] == 3) {
						 		$type[] = 'Material';
						 	}elseif ($tinjauan[$i] == 4) {
						 		$type[] = 'Method';
						 	}elseif ($tinjauan[$i] == 5) {
						 		$type[] = 'Other';
						 	}
						 }
						 echo implode(' , ', $type);
						 ?></center></td>
					<td style="vertical-align: middle;text-align: center;" class="head"><?php echo $weekly_report->problem ?></td>
					<td style="vertical-align: middle;text-align: center;" class="head"><?php echo $weekly_report->action ?></td>
					<td style="vertical-align: middle;text-align: center;" colspan="2" class="head"><?php echo  $weekly_report->foto_aktual ?></td>
				</tr>
				@endforeach
			</tbody>
		</table>
</header>
<main>
</main>
</body>
</html>