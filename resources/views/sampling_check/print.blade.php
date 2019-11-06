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
					<td class="head" rowspan="4"><center>Prepared<br><br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif
						<br><br>
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
				@foreach($samplingCheck as $samplingCheck)
				<tr>
					<?php $point_check = DB::select("select * from sampling_check_details where sampling_check_id = '".$samplingCheck->id_sampling_check."'");
						$jumlah_point_check = count($point_check); ?>
					<td class="head" rowspan="{{ $jumlah_point_check + 1 }}"><center>{{ $samplingCheck->date }}</center></td>
					<td class="head" rowspan="{{ $jumlah_point_check + 1 }}"><center>{{ $samplingCheck->product }}</center></td>
					<td class="head" rowspan="{{ $jumlah_point_check + 1 }}"><center>{{ $samplingCheck->no_seri_part }}</center></td>
					<td class="head" rowspan="{{ $jumlah_point_check + 1 }}"><center>{{ $samplingCheck->jumlah_cek }}</center></td>
					@foreach($point_check as $point_check)
						<tr>
							<td class="head"><?php echo $point_check->point_check ?></td>
						<td class="head"><?php echo $point_check->hasil_check ?></td>
						<td class="head"><img width="200px" src="{{ url('/data_file/sampling_check/'.$point_check->picture_check) }}"></td>
						<td class="head">{{ $point_check->pic_check }}</td>
						<td class="head">{{ $point_check->sampling_by }}</td>
						</tr>
					@endforeach
				</tr>
				@endforeach
			</tbody>
		</table>
		
	
	<script>
    // setTimeout(function () { window.print(); }, 200);
</script>
