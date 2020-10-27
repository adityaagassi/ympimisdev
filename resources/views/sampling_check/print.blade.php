
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
					<td colspan="9" style="padding-top: 0px;padding-bottom: 0px;">
						<img style="width: 80px" src="{{ asset('images/logo_yamaha2.png') }}" alt="">
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;" colspan="9" class="head">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">Department</td>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ strtoupper($departments) }}</td>
					<td class="head" rowspan="4" colspan="4" style="padding: 15px;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;"><center><b>{{ $activity_name }}</b></center></td>
					<td class="head" rowspan="4" style="padding-top: 0px;padding-bottom: 0px;"><center>Checked<br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif
						<br>
						{{ $foreman }}<br>Foreman</center></td>
				</tr>
				<tr>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">Section</td>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ strtoupper($section) }}</td>
				</tr>
				<tr>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">Sub Section</td>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ strtoupper($subsection) }}</td>
				</tr>
				<tr>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">Bulan</td>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ $monthTitle }}</td>
				</tr>
				<tr style="text-align: center;font-weight: bold;">
					<td class="head"><center>Date</center></td>
					<td class="head"><center>Product</center></td>
					<td class="head"><center>No. Seri / Part</center></td>
					<td class="head"><center>Jumlah Cek</center></td>
					<td class="head"><center>Point Check</center></td>
					<td class="head"><center>Hasil Check</center></td>
					<td class="head"><center>Picture Check</center></td>
					<td class="head"><center>PIC Check</center></td>
					<td class="head"><center>Sampling By</center></td>
				</tr>
				<?php $index = 0 ?>
				@foreach($samplingCheck as $samplingCheck)
				<?php for ($i=0; $i < $sampling_point_count[$index]; $i++) { ?>
					<?php for ($j=0; $j < count($sampling_point[$i]); $j++) { ?>
					<tr style="text-align: center;">
						<td class="head" style="vertical-align: middle">{{ $samplingCheck->date }}</td>
						<td style="vertical-align: middle" class="head">{{ $samplingCheck->product }}</td>
						<td style="vertical-align: middle" class="head">{{ $samplingCheck->no_seri_part }}</td>
						<td style="vertical-align: middle" class="head">{{ $samplingCheck->jumlah_cek }}</td>
						<td class="head" style="border: 1px solid black;vertical-align: middle"><?php echo $sampling_point[$i][$j]->point_check ?></td>
						<td class="head" style="border: 1px solid black;vertical-align: middle"><?php echo $sampling_point[$i][$j]->hasil_check ?></td>
						<td class="head" style="border: 1px solid black;vertical-align: middle"><img width="150px" src="{{ url('/data_file/sampling_check/'.$sampling_point[$i][$j]->picture_check) }}"></td>
						<td class="head" style="border: 1px solid black;vertical-align: middle">{{ $sampling_point[$i][$j]->pic_check }}</td>
						<td class="head" style="border: 1px solid black;vertical-align: middle">{{ $sampling_point[$i][$j]->sampling_by }}</td>
					</tr>
					<?php } ?>
				<?php } ?>
				<?php $index++ ?>
				@endforeach
			</tbody>
		</table>
</header>
<main>
</main>
</body>
</html>