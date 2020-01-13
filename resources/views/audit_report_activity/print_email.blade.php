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
					<td class="head" rowspan="4" colspan="3" style="padding: 15px;vertical-align: middle;"><center><b>{{ $activity_name }}</b></center></td>
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
					@if($jml_null > 0 && $role_code != 'M')
					<td rowspan="7" class="head" id="approval1" style="vertical-align: middle;"><center>Approval</center></td>
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
					<td class="head" colspan="2" rowspan="2"><center>Nama Operator</center></td>
				</tr>
				<tr>
					<td class="head"><center>Kesesuaian dengan Aktual Proses</center></td>
					<td class="head"><center>Kelengkapan Point Safety</center></td>
					<td class="head"><center>Kesesuaian QC Kouteihyo</center></td>
				</tr>
				<form role="form" method="post" action="{{url('index/audit_report_activity/approval/'.$id)}}">
				<?php $no = 1 ?>
				@foreach($laporanAktivitas as $laporanAktivitas)
				<tr>
					<td rowspan="3" class="head"><center>{{ $no }}</center></td>
					<td rowspan="3" class="head"><center>{{ $laporanAktivitas->date }}</center></td>
					<td rowspan="3" class="head"><center>{{ $laporanAktivitas->nama_dokumen }}</center></td>
					<td rowspan="3" class="head"><center>{{ $laporanAktivitas->no_dokumen }}</center></td>
					<td class="head"><center><?php echo $laporanAktivitas->kesesuaian_aktual_proses ?></center></td>
					<td rowspan="3" class="head"><center>{{ $laporanAktivitas->kelengkapan_point_safety }}</center></td>
					<td rowspan="3" class="head"><center>{{ $laporanAktivitas->kesesuaian_qc_kouteihyo }}</center></td>
					<td rowspan="3" class="head" colspan="2"><center>{{ $laporanAktivitas->operator }}</center></td>
					@if($jml_null > 0 && $role_code != 'M')
					<td rowspan="3" class="head" id="approval2">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						@if($laporanAktivitas->approval == Null)
						    <label class="label label-success"><input type="checkbox" id="customCheck" name="approve[]" value="{{ $laporanAktivitas->id_audit_report }}">Approve
						    </label>
						@endif
					</td>
					@endif
				</tr>
				<tr>
					<td>Tindakan Perbaikan : {{ $laporanAktivitas->tindakan_perbaikan }}</td>
				</tr>
				<tr>
					<td>Target : {{ $laporanAktivitas->target }}</td>
				</tr>
				<?php $no++ ?>
				@endforeach
				@if($jml_null > 0 && $role_code != 'M')
				<tr id="approval3">
					<td class="head" align="right" colspan="10"><button class="btn btn-success" type="submit">Submit</button></td>
				</tr>
				@endif
				</form>
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