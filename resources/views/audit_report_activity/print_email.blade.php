<title>YMPI 情報システム</title>
<link rel="shortcut icon" type="image/x-icon" href="{{ url("logo_mirai.png")}}" />
<style>
table, th, td {
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
.head{
	border: 1px solid black;
}
.label {
	color: white;
	padding: 8px;
	font-family: Arial;
}
.success {background-color: #4CAF50;} /* Green */
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
					<td class="head" rowspan="4" colspan="3" style="padding: 15px;"><center><b>{{ $activity_name }}</b></center></td>
					<td class="head" rowspan="4"><center>Checked<br><br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif
						<br><br>
						{{ $foreman }}<br>Foreman</center></td>
					<td class="head" rowspan="4"><center>Prepared<br><br>
						{{-- @if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif --}}
						<br><br>
						{{ $leader }}<br>Leader</center></td>
					@if($jml_null > 0)
					<td rowspan="7" class="head" id="approval1"><center>Approval</center></td>
					@endif
				</tr>
				<tr>
					<td colspan="2" class="head">Section</td>
					<td colspan="2" class="head">{{ $section }}</td>
				</tr>
				<tr>
					<td colspan="2" class="head">Nama PIC</td>
					<td colspan="2" class="head">{{ $leader }}</td>
				</tr>
				<tr>
					<td colspan="2" class="head">Month</td>
					<td colspan="2" class="head">{{ $monthTitle }}</td>
				</tr>
				<tr>
					<td class="head" colspan='9'></td>
				</tr>
				<tr>
					<td class="head" rowspan="2"><center>No.</center></td>
					<td class="head" rowspan="2"><center>Date</center></td>
					<td class="head" rowspan="2"><center>Nama Dokumen</center></td>
					<td class="head" rowspan="2"><center>No. Dokumen</center></td>
					<td class="head" colspan='3'><center>Hasil Audit IK</center></td>
					<td class="head" colspan="2"><center>Sosialisasi</center></td>
				</tr>
				<tr>
					<td class="head"><center>Kesesuaian dengan Aktual Proses</center></td>
					<td class="head"><center>Kelengkapan Point Safety</center></td>
					<td class="head"><center>Kesesuaian QC Kouteihyo</center></td>
					<td class="head"><center>Nama Operator</center></td>
					<td class="head"><center>Operator Sign</center></td>
				</tr>
				<form role="form" method="post" action="{{url('index/audit_report_activity/approval/'.$id)}}">
				<?php $no = 1 ?>
				@foreach($laporanAktivitas as $laporanAktivitas)
				<tr>
					<td class="head"><center>{{ $no }}</center></td>
					<td class="head"><center>{{ $laporanAktivitas->date }}</center></td>
					<td class="head"><center>{{ $laporanAktivitas->nama_dokumen }}</center></td>
					<td class="head"><center>{{ $laporanAktivitas->no_dokumen }}</center></td>
					<td class="head"><center><?php echo $laporanAktivitas->kesesuaian_aktual_proses ?></center></td>
					<td class="head"><center>{{ $laporanAktivitas->kelengkapan_point_safety }}</center></td>
					<td class="head"><center>{{ $laporanAktivitas->kesesuaian_qc_kouteihyo }}</center></td>
					<td class="head"><center>{{ $laporanAktivitas->operator }}</center></td>
					<td class="head"><center>{{ $laporanAktivitas->operator_sign }}</center></td>
					@if($jml_null > 0)
					<td class="head" id="approval2">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						@if($laporanAktivitas->approval == Null)
						<div class="custom-control custom-checkbox">
						    <span class="label success"><input type="checkbox" class="custom-control-input" id="customCheck" name="approve[]" value="{{ $laporanAktivitas->id_audit_report }}">
						    <label class="custom-control-label" for="customCheck">Approve</label>
						    </span>
						</div>
						@endif
					</td>
					@endif
				</tr>
				<?php $no++ ?>
				@endforeach
				@if($jml_null > 0)
				<tr id="approval3">
					<td class="head" align="right" colspan="10"><button class="label success" type="submit">Submit</button></td>
				</tr>
				@endif
				</form>
			</tbody>
		</table>
		
	
	<script>
    // setTimeout(function () { window.print(); }, 200);
</script>
