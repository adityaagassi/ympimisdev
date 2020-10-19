<!-- <?php var_dump($countdate) ?> -->
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
      <tbody style="font-size: 10px">
		<tr>
			<td colspan="{{$countdate+1}}" style="border: 1px solid black;"><img width="80px" src="{{ asset('images/logo_yamaha2.png') }}" alt=""></td>
		</tr>
		<tr>
			<td style="border: 1px solid black;vertical-align: middle;" colspan="{{$countdate+1}}">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
		</tr>
		<?php $colspan = $countdate - 16 ?>
		<tr>
			<td style="border: 1px solid black;">Department</td>
			<td style="border: 1px solid black;" colspan="2">{{ strtoupper($departments) }}</td>
			<td rowspan="3" colspan="13" style="border: 1px solid black;padding: 15px;vertical-align: middle;"><center><b>{{ $activity_name }}</b></center></td>
			<td style="border: 1px solid black;vertical-align: middle;" rowspan="3"><center>Checked<br>
				@if($jml_null == 0)
					<b style='color:green'>Approved</b><br>
					<b style='color:green'>{{ $approved_date }}</b>
				@endif<br>
			{{ $foreman }}<br>Foreman</center></td>
			<td style="border: 1px solid black;vertical-align: middle;" rowspan="3" colspan="{{ $colspan }}"><center>Prepared<br>
				@if($jml_null_leader == 0)
					<b style='color:green'>Approved</b><br>
					<b style='color:green'>{{ $approved_date_leader }}</b>
				@endif<br>
				{{ $leader }}<br>Leader</center></td>
		</tr>
		<tr>
			<td style="border: 1px solid black;">Subsection</td>
			<td style="border: 1px solid black;" colspan="2">{{ strtoupper($subsection) }}</td>
		</tr>
		<tr>
			<td style="border: 1px solid black;">Month</td>
			<td style="border: 1px solid black;" colspan="2">{{ $monthTitle }}</td>
		</tr>
		 <tr>
			<td style="border: 1px solid black;"><center>Point Check / Date</center></td>
			@foreach($date as $date)
			<td style="border: 1px solid black;"><center>{{ substr($date->week_date,-2) }}</center></td>
			<?php $datenow[] = $date->week_date ?>
			@endforeach
		</tr>
		@foreach($point_check as $point_check)
				<tr>
					<td style="border: 1px solid black;"><center>{{ $point_check->point_check }}</center></td>
					<?php
					for($i = 0;$i<count($datenow);$i++){ ?>
						<?php $condition = DB::SELECT("select area_checks.`condition`,pic,date,area_checks.id as id_area_check
							from weekly_calendars
							left join area_checks on area_checks.date = week_date
							LEFT JOIN area_check_points on area_check_points.id = area_checks.area_check_point_id
							where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$month."'
							and area_checks.date = '".$datenow[$i]."'
							and area_check_points.id = '".$point_check->id."'
							and area_checks.activity_list_id = '".$id."'
						  and area_checks.deleted_at is null
							and week_date not in (select tanggal from ftm.kalender)"); ?>
						<td style="border: 1px solid black;"><center><?php for($j = 0;$j < count($condition) ; $j++){
							if($condition[$j]->condition == 'Good'){
								echo '<label class="label label-success">O</label>';
							}
							else{
								echo '<label class="label label-danger">X</label>';
							}
						} ?></center></td>
					<?php } ?>
				</tr>
				@endforeach
				<tr>
					<td style="border: 1px solid black;"><center>PIC Check</center></td>
					<?php
					for($i = 0;$i<count($datenow);$i++){ ?>
						<?php $condition = DB::SELECT("select area_checks.`condition`,pic,date,area_checks.id as id_area_check
							from weekly_calendars
							left join area_checks on area_checks.date = week_date
							LEFT JOIN area_check_points on area_check_points.id = area_checks.area_check_point_id
							where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$month."'
							and area_checks.date = '".$datenow[$i]."'
							and area_check_points.id = '".$point_check->id."'
							and area_checks.activity_list_id = '".$id."'
						  and area_checks.deleted_at is null
							and week_date not in (select tanggal from ftm.kalender)"); ?>
						<td style="border: 1px solid black;"><center><?php for($j = 0;$j < count($condition) ; $j++){
							echo $condition[$j]->pic;
						} ?></center></td>
					<?php } ?>
				</tr>
    </tbody>
  </table>
</header>
<main>
</main>
</body>
</html>



