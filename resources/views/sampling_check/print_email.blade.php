@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Approval {{ $activity_name }} - {{ $leader }}
    <a style="margin-right: 10px" class="btn btn-info pull-right" href="{{url('index/sampling_check/print_sampling/'.$id.'/'.$month)}}">Cetak / Save PDF</a>
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
		<table style="width: 100%; border-collapse: collapse;" >
			<tbody>
				<tr>
					<td colspan="10" style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">
						<img style="width: 80px" src="{{ asset('images/logo_yamaha2.png') }}" alt="">
					</td>
				</tr>
				<tr>
					<td colspan="10" style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">Department</td>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ strtoupper($departments) }}</td>
					@if($jml_null > 0 && $role_code != 'M')
					<td class="head" rowspan="5" id="approval1" style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px;"><center>Approval</center></td>
					@endif
					<td class="head" rowspan="4" colspan="4" style="padding: 15px;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;"><center><b>{{ $activity_name }}</b></center></td>
					<td class="head" rowspan="4" style="padding-top: 0px;padding-bottom: 0px;"><center>Checked<br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif
						<br>
						{{ $foreman }}<br>Foreman</center>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">Section</td>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ strtoupper($section) }}</td>
				</tr>
				<tr>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">Sub Section</td>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ strtoupper($subsection) }}</td>
				</tr>
				<tr>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">Bulan</td>
					<td colspan="2" class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ $monthTitle }}</td>
				</tr>
				<tr style="font-weight: bold;">
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
				<form role="form" method="post" action="{{url('index/sampling_check/approval/'.$id.'/'.substr($date, 0, 7))}}">
				@foreach($samplingCheck as $samplingCheck)
				<tr>
					<?php $point_check = DB::select("select * from sampling_check_details where sampling_check_id = '".$samplingCheck->id_sampling_check."'");
						$jumlah_point_check = count($point_check); ?>
					<td class="head" style="vertical-align: middle" rowspan="{{ $jumlah_point_check + 1 }}"><center>{{ $samplingCheck->date }}</center></td>
					<td class="head" style="vertical-align: middle" rowspan="{{ $jumlah_point_check + 1 }}"><center>{{ $samplingCheck->product }}</center></td>
					<td class="head" style="vertical-align: middle" rowspan="{{ $jumlah_point_check + 1 }}"><center>{{ $samplingCheck->no_seri_part }}</center></td>
					<td class="head" style="vertical-align: middle" rowspan="{{ $jumlah_point_check + 1 }}"><center>{{ $samplingCheck->jumlah_cek }}</center></td>
					@if($jml_null > 0 && $role_code != 'M')
					<td id="approval2" class="head" rowspan="{{ $jumlah_point_check + 1 }}" style="border: 1px solid black;vertical-align: middle">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						@if($samplingCheck->approval == Null)
						<label class="label label-success"><input type="checkbox" id="customCheck" name="approve[]" value="{{ $samplingCheck->id_sampling_check }}">Approve</label>
						@endif
					</td>
					@endif
					@foreach($point_check as $point_check)
						<tr style="text-align: center;">
							<td class="head" style="border: 1px solid black;vertical-align: middle"><?php echo $point_check->point_check ?></td>
							<td class="head" style="border: 1px solid black;vertical-align: middle"><?php echo $point_check->hasil_check ?></td>
							<td class="head" style="border: 1px solid black;vertical-align: middle"><img width="200px" src="{{ url('/data_file/sampling_check/'.$point_check->picture_check) }}"></td>
							<td class="head" style="border: 1px solid black;vertical-align: middle"><center>{{ $point_check->pic_check }}</center></td>
							<td class="head" style="border: 1px solid black;vertical-align: middle"><center>{{ $point_check->sampling_by }}</center></td>
						</tr>
					@endforeach
				</tr>
				@endforeach
				@if($jml_null > 0 && $role_code != 'M')
				<tr class="head" id="approval3">
					<td align="right" colspan="5"><button class="btn btn-success" type="submit">Approve</button></td>
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
