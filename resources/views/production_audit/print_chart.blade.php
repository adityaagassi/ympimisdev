<title>YMPI 情報システム</title>
<link rel="shortcut icon" type="image/x-icon" href="{{ url("logo_mirai.png")}}" />
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
  font-family:"Arial";
  padding: 5px;
}
@media print {
	body {-webkit-print-color-adjust: exact;}
	#approval1 {
	    display: none;
	  }
	  #approval2 {
	    display: none;
	  }
	  #approval3 {
	    display: none;
	  }
}
.label {
	color: white;
	padding: 8px;
	font-family: Arial;
}
.success {background-color: #4CAF50;} /* Green */
</style>
<table class="table table-bordered table-hover">
	<tbody>
		<tr>
			<td colspan="6">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
		</tr>
		<tr>
			<td rowspan="5" style="width: 20px"><center><img width="80px" src="{{ asset('images/logo_yamaha2.png') }}" alt=""></center></td>
		</tr>
		<tr>
			<td>Department</td>
			<td>{{ $departments }}</td>
			<td rowspan="4" colspan="2" style="padding: 15px;"><center><b>{{ $activity_name }}</b></center></td>
			<td rowspan="4"><center>Mengetahui<br><br>
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
			<td>{{ $product }}</td>
		</tr>
		<tr>
			<td>Proses</td>
			<td>{{ $proses }}</td>
		</tr>
		<tr>
			<td>Date</td>
			<td>{{ $date_audit }}</td>
		</tr>
		<tr>
			<td>Point Check</td>
			<td>Cara Cek</td>
			<td>Foto Kondisi Aktual</td>
			<td>Kondisi (OK / NG)</td>
			<td>PIC</td>
			<td>Auditor</td>
		</tr>
		@foreach($production_audit as $production_audit)
		<tr>
			<td><?php echo $production_audit->point_check ?></td>
			<td><?php echo $production_audit->cara_cek ?></td>
			<td><img width="200px" src="{{ url('/data_file/'.$production_audit->foto_kondisi_aktual) }}"></td>
			<td>@if($production_audit->kondisi == "Good")
	              {{$production_audit->kondisi}}
	            @else
	              {{$production_audit->kondisi}}
	            @endif
        	</td>
			<td>{{ $production_audit->pic_name }}</td>
			<td>{{ $production_audit->auditor_name }}</td>
		</tr>
		@endforeach
	</tbody>
</table>
<script>
    // setTimeout(function () { window.print(); }, 200);
</script>
