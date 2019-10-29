<style>
table{
	width:100%;
}
table, th, td {
  border-collapse: collapse;
  font-family:"Arial";
  padding: 5px;
}
.head {
	border: 1px solid black;
}
.bodytraining{
	padding-top:50px;
	padding-left:50px;
}
@media print {
	body {-webkit-print-color-adjust: exact;}
}
</style>
<table class="table table-bordered table-hover">
	<tbody>
		<tr style="border:0px;">
			<td class="head" colspan="6"><img width="80px" src="{{ asset('images/logo_yamaha2.png') }}" alt=""></td>
		</tr>
		<tr>
			<td class="head" colspan="6">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
		</tr>
		<tr>
			<td class="head">Department</td>
			<td class="head">{{ $departments }}</td>
			<td class="head" rowspan="4" colspan="2" style="padding: 15px;"><center><b>{{ $activity_name }}</b></center></td>
			<td class="head" rowspan="4"><center>Checked<br><br><br><br>
				{{ $training->foreman }}<br>Foreman</center></td>
			<td class="head" rowspan="4"><center>Prepared<br><br><br><br>
				{{ $training->leader }}<br>Leader</center></td>
		</tr>
		<tr>
			<td class="head">Section</td>
			<td>{{ $training->section }}</td>
		</tr>
		<tr>
			<td class="head">Product</td>
			<td class="head">{{ $training->product }}</td>
		</tr>
		<tr>
			<td class="head">Proses</td>
			<td class="head">{{ $training->periode }}</td>
		</tr>
		<tr class="head">
			<td class="bodytraining" width="10%">Tanggal<br>Waktu<br>Trainer<br>Tema<br>Isi Training<br>Tujuan<br>Standard</td>
			<td width="15%" style="padding-top:50px;">:</td>
			<td width="50%" style="padding-top:50px;">{{ $training->date }}</td>
		</tr>
	</tbody>
</table>
<script>
    // setTimeout(function () { window.print(); }, 200);
</script>
