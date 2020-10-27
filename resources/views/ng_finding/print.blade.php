
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
  padding: 5px;
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
			<tbody style="font-size: 10px">
				<tr>
					<td colspan="8" style="border: 1px solid black;"><img width="80px" src="{{ asset('images/logo_yamaha2.png') }}" alt=""></td>
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px" colspan="8" class="head">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td class="head" style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px">Department</td>
					<td class="head" colspan="2" style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px">{{ strtoupper($departments) }}</td>
					<td class="head" rowspan="2" colspan="3" style="font-size:15px;vertical-align: middle;padding-top: 0px;padding-bottom: 0px"><center><b>{{ $activity_name }}</b></center></td>
					<td class="head" rowspan="2" style="padding-top: 0px;padding-bottom: 0px"><center>Checked<br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif
						<br>
						{{ $foreman }}<br>Foreman</center>
					</td>
					<td class="head" rowspan="2" style="padding-top: 0px;padding-bottom: 0px"><center>Prepared<br>
						@if($jml_null_leader == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date_leader }}</b>
						@endif
						<br>
						{{ $leader }}<br>Leader</center>
					</td>
				</tr>
				<tr>
					<td class="head" style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px">Bulan</td>
					<td class="head" colspan="2" style="vertical-align: middle;padding-top: 0px;padding-bottom: 0pxs">{{ $monthTitle }}</td>
				</tr>
				<tr>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px"><center><b>Date</b></center></td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px"><center><b>GMC</b></center></td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px"><center><b>Description</b></center></td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px"><center><b>Qty</b></center></td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px"><center><b>Finder</b></center></td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px"><center><b>Picture</b></center></td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px"><center><b>Defect</b></center></td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px"><center><b>Checked By QA</b></center></td>
				</tr>
				@foreach($ng_finding as $ng_finding)
				<tr style="font-size: 10px">
					<td class="head" style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px"><center>{{ $ng_finding->date }}</center></td>
					<td style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px" class="head"><center><?php echo $ng_finding->material_number ?></center></td>
					<td style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px" class="head"><center><?php echo $ng_finding->material_description ?></center></td>
					<td style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px" class="head"><center><?php echo  $ng_finding->quantity ?></center></td>
					<td style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px" class="head"><center><?php echo  $ng_finding->finder ?></center></td>
					<?php if(strpos($ng_finding->picture, '<p>') !== false){ ?>
						<td style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px" class="head"><center><?php echo  $ng_finding->picture ?></center></td>
					<?php }else{ ?>
						<td style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px" class="head"><center><img width="200px" src="{{ url('/data_file/ng_finding/'.$ng_finding->picture) }}"></center></td>
					<?php } ?>
					<td style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px" class="head"><center><?php echo  $ng_finding->defect ?></center></td>
					<td style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px" class="head"><center><?php echo  $ng_finding->checked_qa ?></center></td>
				</tr>
				@endforeach
			</tbody>
		</table>
</header>
<main>
</main>
</body>
</html>



