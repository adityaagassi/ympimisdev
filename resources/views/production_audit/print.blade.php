
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
				<!-- <tr>
					<td colspan="7" style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;"><img width="80px" src="{{ asset('images/logo_yamaha2.png') }}" alt=""></td>
				</tr> -->
				<tr>
					<td style="border: 1px solid black;" colspan="7">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td rowspan="4" style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;text-align:center;width: 2%"><img width="120px" src="{{ asset('images/logo_yamaha2.png') }}" alt=""></td>
					<td style="border: 1px solid black;">Department</td>
					<td style="border: 1px solid black;" >{{ strtoupper($departments) }}</td>
					<td rowspan="4" colspan="3" style="padding: 15px;border: 1px solid black;vertical-align: middle;"><center><b>{{ $activity_name }}</b></center></td>
					<td rowspan="4" style="border: 1px solid black;vertical-align: middle;"><center>Mengetahui<br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif
						<br>{{ $foreman }}
						<br>Foreman</center>
					</td>
				</tr>
				<tr>
					<td>Product</td>
					<td >{{ $product }}</td>
				</tr>
				<tr>
					<td>Proses</td>
					<td >{{ $proses }}</td>
				</tr>
				<tr>
					<td>Month</td>
					<td >{{ $monthTitle }}</td>
				</tr>
				<tr style="text-align: center;">
					<td style="font-weight: bold;font-size: 12px;width: 2%">Point Check</td>
					<td style="font-weight: bold;font-size: 12px">Cara Cek</td>
					<td style="font-weight: bold;font-size: 12px">Date</td>
					<td style="font-weight: bold;font-size: 12px">Foto Kondisi Aktual</td>
					<td style="font-weight: bold;font-size: 12px">Kondisi</td>
					<td style="font-weight: bold;font-size: 12px">PIC</td>
					<td style="font-weight: bold;font-size: 12px">Auditor</td>
				</tr>
				@foreach($production_audit as $production_audit)
				<tr style="text-align: center;">
					<td style="height: 150px"><?php echo $production_audit->point_check ?></td>
					<td><?php echo $production_audit->cara_cek ?></td>
					<td><?php echo $production_audit->date ?></td>
					<td><img width="150px" src="{{ url('/data_file/'.$production_audit->foto_kondisi_aktual) }}"></td>
					<td>@if($production_audit->kondisi == "Good")
			              <label class="label label-success">{{$production_audit->kondisi}}</label>
			            @else
			              <label class="label label-danger">{{$production_audit->kondisi}}</label>
			            @endif
		        	</td>
					<td>{{ $production_audit->pic_name }}</td>
					<td>{{ $production_audit->auditor_name }}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
</header>
<main>
</main>
</body>
</html>