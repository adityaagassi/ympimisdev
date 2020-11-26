@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Approval {{ $activity_name }} - {{ $leader }}
    <a style="margin-right: 10px" class="btn btn-info pull-right" href="{{url('index/audit_report_activity/print_audit_report/'.$id.'/'.$month)}}">Cetak / Save PDF</a>
    <!-- <button class="btn btn-primary pull-right" onclick="myFunction()">Print</button> -->
  </h1>
  <ol class="breadcrumb">
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
  <div class="box box-solid">
      <div class="box-body">
		<table class="table" style="font-size: 15px">
			<tbody>
				<tr>
					<td colspan="9" style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;"><img width="80px" src="{{ public_path('images/logo_yamaha2.png') }}" alt=""></td>
				</tr>
				<tr>
					<td colspan="9" style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
					@if($jml_null > 0 && $role_code != 'M')
					<td rowspan="6" class="head" id="approval1" style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px;"><center>Approval</center></td>
					@endif
				</tr>
				<tr>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">Department</td>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ strtoupper($departments) }}</td>
					<td class="head" rowspan="3" colspan="3" style="padding-top: 0px;padding-bottom: 0px;vertical-align: middle;"><center><b>{{ $activity_name }}</b></center></td>
					<td class="head" rowspan="3" style="padding-top: 0px;padding-bottom: 0px;"><center>Checked<br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif
						<br>
						{{ $foreman }}<br>Foreman</center></td>
					<td class="head" rowspan="3" style="padding-top: 0px;padding-bottom: 0px;"><center>Prepared<br>
						@if($approval_leader != Null)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date_leader }}</b>
						@endif
						<br>
						{{ $leader }}<br>Leader</center></td>
				</tr>
				<tr>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">Section</td>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ strtoupper($section) }}</td>
				</tr>
				<tr>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">Month</td>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ $monthTitle }}</td>
				</tr>
				<tr>
					<td class="head" rowspan="2"><center><b>No.</b></center></td>
					<td class="head" rowspan="2"><center><b>Date</b></center></td>
					<td class="head" rowspan="2"><center><b>Nama Dokumen</b></center></td>
					<td class="head" rowspan="2"><center><b>No. Dokumen</b></center></td>
					<td class="head" colspan='3'><center><b>Hasil Audit IK</b></center></td>
					<td class="head" colspan="2" rowspan="2"><center><b>Operator</b></center></td>
				</tr>
				<tr>
					<td class="head"><center><b>Kesesuaian dengan Aktual Proses</b></center></td>
					<td class="head"><center><b>Kelengkapan Point Safety</b></center></td>
					<td class="head"><center><b>Kesesuaian QC Kouteihyo</b></center></td>
				</tr>
				<form role="form" method="post" action="{{url('index/audit_report_activity/approval/'.$id)}}">
				<?php $no = 1 ?>
				@foreach($laporanAktivitas as $laporanAktivitas)
				<tr>
					<td rowspan="3" class="head"><center>{{ $no }}</center></td>
					<td rowspan="3" class="head"><center>{{ $laporanAktivitas->date }}</center></td>
					<td rowspan="3" class="head"><center>{{ $laporanAktivitas->nama_dokumen }}</center></td>
					<td rowspan="3" class="head"><center>{{ $laporanAktivitas->no_dokumen }}</center></td>
					<td class="head" style="text-align: center;"><?php echo $laporanAktivitas->kesesuaian_aktual_proses ?></td>
					<td rowspan="3" class="head" style="text-align: center;">{{ $laporanAktivitas->kelengkapan_point_safety }}</td>
					<td rowspan="3" class="head" style="text-align: center;">{{ $laporanAktivitas->kesesuaian_qc_kouteihyo }}</td>
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
					<td class="head" align="right" colspan="10"><button class="btn btn-success" type="submit">Approve</button></td>
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