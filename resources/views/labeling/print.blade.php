
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
					<td colspan="6" style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;"><img width="80px" src="{{ asset('images/logo_yamaha2.png') }}" alt=""></td>
				</tr>
				<tr>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;" colspan="6">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">Department</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">{{ strtoupper($departments) }}</td>
					<td rowspan="4" colspan="2" style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;"><center><b>{{ $activity_name }}</b></center></td>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;" rowspan="4"><center>Checked<br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif<br>
					{{ $foreman }}<br>Foreman</center></td>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;" rowspan="4"><center>Prepared<br>
						@if($jml_null_leader == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date_leader }}</b>
						@endif<br>
						{{ $leader }}<br>Leader</center></td>
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">Section</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">{{ strtoupper($section) }}</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">Product</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">{{ $product }}</td>
				</tr>
				<!-- <tr>
					<td style="border: 1px solid black;">Periode</td>
					<td style="border: 1px solid black;">{{ $periode }}</td>
				</tr> -->
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">Bulan</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">{{ $monthTitle }}</td>
				</tr>
				<tr>
					<td rowspan="2" style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;text-align: center;font-weight: bold;">No.</td>
					<td rowspan="2" style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;text-align: center;font-weight: bold;">Date</td>
					<td rowspan="2" style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;text-align: center;font-weight: bold;">Nama Mesin</td>
					<td colspan="2" style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;text-align: center;font-weight: bold;">Kondisi Label</td>
					<td rowspan="2" style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;text-align: center;font-weight: bold;">Keterangan</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;text-align: center;font-weight: bold;">Arah Putaran</td>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;text-align: center;font-weight: bold;">Sisa Putaran</td>
				</tr>
				<?php $no = 1; ?>
				@foreach($labeling2 as $labeling)
				<tr>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;text-align: center">{{ $no }}</td>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;text-align: center">{{ $labeling->date }}</td>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;text-align: center">{{ $labeling->nama_mesin }}</td>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;text-align: center"><img width="100px" src="{{ url('/data_file/labeling/'.$labeling->foto_arah_putaran) }}"></td>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;text-align: center"><img width="100px" src="{{ url('/data_file/labeling/'.$labeling->foto_sisa_putaran) }}"></td>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;text-align: center">{{ $labeling->keterangan }}</td>
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



