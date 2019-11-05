<title>YMPI 情報システム</title>
<link rel="shortcut icon" type="image/x-icon" href="{{ url("logo_mirai.png")}}" />
<style>
	.table{
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
	.peserta {
		border: 1px solid black;
		width:50%;
		text-align:center;
	}
	.bodytraining{
		padding-left:100px;
	}
	p {
		display: block;
		margin-top: 0;
		margin-bottom: 0;
		margin-left: 0;
		margin-right: 0;
	}
	@media print {
		body {-webkit-print-color-adjust: exact;}
	}
</style>
		<table class="table">
			<tbody>
				{{-- <tr style="border:0px;">
					<td colspan="6"><img width="80px" src="{{ asset('images/logo_yamaha2.png') }}" alt=""></td>
				</tr> --}}
				<tr>
					<td colspan="6">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td colspan="2" class="head">Department</td>
					<td colspan="2" class="head">{{ $departments }}</td>
					<td class="head" rowspan="4" colspan="4" style="padding: 15px;"><center><b>{{ $activity_name }}</b></center></td>
					<td class="head" rowspan="4"><center>Prepared<br><br><br><br>
						{{ $leader }}<br>Leader</center></td>
				</tr>
				<tr>
					<td colspan="2" class="head">Section</td>
					<td colspan="2" class="head">{{ $section }}</td>
				</tr>
				<tr>
					<td colspan="2" class="head">Sub Section</td>
					<td colspan="2" class="head">{{ $subsection }}</td>
				</tr>
				<tr>
					<td colspan="2" class="head">Month</td>
					<td colspan="2" class="head">{{ $month }}</td>
				</tr>
				<tr>
					<td class="head">Date</td>
					<td class="head">Product</td>
					<td class="head">No. Seri / Part</td>
					<td class="head">Jumlah Cek</td>
					<td class="head">Point Check</td>
					<td class="head">Hasil Check</td>
					<td class="head">Picture Check</td>
					<td class="head">PIC Check</td>
					<td class="head">Sampling By</td>
				</tr>
				@foreach($samplingCheck as $samplingCheck)
				<tr>
					<td class="head">{{ $samplingCheck->date }}</td>
					<td class="head">{{ $samplingCheck->product }}</td>
					<td class="head">{{ $samplingCheck->no_seri_part }}</td>
					<td class="head">{{ $samplingCheck->jumlah_cek }}</td>
					<td class="head"><?php echo $samplingCheck->point_check ?></td>
					<td class="head"><?php echo $samplingCheck->hasil_check ?></td>
					<td class="head"><img width="200px" src="{{ url('/data_file/sampling_check/'.$samplingCheck->picture_check) }}"></td>
					<td class="head">{{ $samplingCheck->pic_check }}</td>
					<td class="head">{{ $samplingCheck->sampling_by }}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
		
	
	<script>
    // setTimeout(function () { window.print(); }, 200);
</script>
