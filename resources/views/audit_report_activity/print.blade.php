@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Print {{ $activity_name }} - {{ $departments }}
    <small>it all starts here</small>
    <button class="btn btn-primary pull-right" onclick="myFunction()">Print</button>
  </h1>
  <ol class="breadcrumb">
    {{-- <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="#">Examples</a></li>
    <li class="active">Blank page</li> --}}
  </ol>
</section>
<style type="text/css">
	@media print {
	.table {-webkit-print-color-adjust: exact;}
	#approval1 {
	    display: none;
	  }
	  #approval2 {
	    display: none;
	  }
	  #approval3 {
	    display: none;
	  }
</style>
@endsection
@section('content')
<section class="content">
  @if (session('status'))
		<div class="alert alert-success alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
			{{ session('status') }}
		</div>   
	@endif
	@if (session('error'))
		<div class="alert alert-warning alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4> Warning!</h4>
			{{ session('error') }}
		</div>   
	@endif
  <div class="box box-primary">
      <div class="box-body">
		<table class="table">
			<tbody>
				<tr>
					<td colspan="9" style="border: 1px solid black;">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td colspan="2" class="head">Department</td>
					<td colspan="2" class="head">{{ $departments }}</td>
					<td class="head" rowspan="4" colspan="2" style="padding: 15px;vertical-align: middle;"><center><b>{{ $activity_name }}</b></center></td>
					<td class="head" rowspan="4"><center>Checked<br><br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif
						<br><br>
						{{ $foreman }}<br>Foreman</center></td>
					<td class="head" rowspan="4"><center>Prepared<br><br>
						@if($approval_leader != Null)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date_leader }}</b>
						@endif
						<br><br>
						{{ $leader }}<br>Leader</center></td>
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
					<td class="head" colspan='8'></td>
				</tr>
				<tr>
					<td class="head" rowspan="2"><center>No.</center></td>
					<td class="head" rowspan="2"><center>Date</center></td>
					<td class="head" rowspan="2"><center>Nama Dokumen</center></td>
					<td class="head" rowspan="2"><center>No. Dokumen</center></td>
					<td class="head" colspan='3'><center>Hasil Audit IK</center></td>
					<td class="head"><center>Sosialisasi</center></td>
				</tr>
				<tr>
					<td class="head"><center>Kesesuaian dengan Aktual Proses</center></td>
					<td class="head"><center>Kelengkapan Point Safety</center></td>
					<td class="head"><center>Kesesuaian QC Kouteihyo</center></td>
					<td class="head"><center>Nama Operator</center></td>
				</tr>
				<?php $no = 1 ?>
				@foreach($laporanAktivitas as $laporanAktivitas)
				<tr>
					<td class="head" rowspan="3"><center>{{ $no }}</center></td>
					<td class="head" rowspan="3"><center>{{ $laporanAktivitas->date }}</center></td>
					<td class="head" rowspan="3"><center>{{ $laporanAktivitas->nama_dokumen }}</center></td>
					<td class="head" rowspan="3"><center>{{ $laporanAktivitas->no_dokumen }}</center></td>
					<td class="head"><?php echo $laporanAktivitas->kesesuaian_aktual_proses ?></td>
					<td class="head" rowspan="3">{{ $laporanAktivitas->kelengkapan_point_safety }}</td>
					<td class="head" rowspan="3">{{ $laporanAktivitas->kesesuaian_qc_kouteihyo }}</td>
					<td class="head" rowspan="3"><center>{{ $laporanAktivitas->operator }}</center></td>
				</tr>
				<tr>
					<td>Tindakan Perbaikan : {{ $laporanAktivitas->tindakan_perbaikan }}</td>
				</tr>
				<tr>
					<td>Target : {{ $laporanAktivitas->target }}</td>
				</tr>
				<?php $no++ ?>
				@endforeach
				
			</tbody>
		</table>
	</div>
  </div>
</section>
  @endsection
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
  font-family:"Arial";
  padding: 5px;
  vertical-align:middle;
}
@media print {
	body {-webkit-print-color-adjust: exact;}
}
</style>
<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<script>
    // setTimeout(function () { window.print(); }, 200);
    function myFunction() {
	  window.print();
	}
	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
	});
</script>
