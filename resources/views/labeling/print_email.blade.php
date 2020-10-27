@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Approval {{ $activity_name }} - {{ $leader }}
    @if($jml_null > 0)
    <label class="label label-success pull-right"><input type="checkbox" onclick="checkAll(this.checked)">Approve All</label>
    @endif
    <a style="margin-right: 10px" class="btn btn-info pull-right" href="{{url('index/labeling/print_labeling/'.$id.'/'.$month)}}">Cetak / Save PDF</a>
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
  @if ($errors->has('password'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
    {{ $errors->first() }}
  </div>   
  @endif
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-solid">
      <div class="box-body">
		<table class="table" style="border: 1px solid black;font-size: 15px">
			<tbody>
				<tr>
					<td colspan="12" style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;"><img width="80px" src="{{ asset('images/logo_yamaha2.png') }}" alt=""></td>
				</tr>
				<tr>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;" colspan="12">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">Department</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">{{ strtoupper($departments) }}</td>
					<td rowspan="4" colspan="2" style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;"><center><b>{{ $activity_name }}</b></center></td>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;" rowspan="4"><center>Checked<br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif<br>
					{{ $foreman }}<br>Foreman</center></td>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;" rowspan="4"><center>Prepared<br>
						@if($jml_null_leader == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date_leader }}</b>
						@endif<br>
						{{ $leader }}<br>Leader</center>
					</td>
					@if($jml_null > 0)
					<td rowspan="6" id="approval1" style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;"><center>Approval</center></td>
					@endif
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">Section</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">{{ strtoupper($section) }}</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">Product</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">{{ $product }}</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">Bulan</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">{{ $monthTitle }}</td>
				</tr>
				<tr>
					<td rowspan="2" style="border: 1px solid black;vertical-align: middle;padding-top:0px;padding-bottom:0px;text-align: center;font-weight: bold;">No.</td>
					<td rowspan="2" style="border: 1px solid black;vertical-align: middle;padding-top:0px;padding-bottom:0px;text-align: center;font-weight: bold;">Date</td>
					<td rowspan="2" style="border: 1px solid black;vertical-align: middle;padding-top:0px;padding-bottom:0px;text-align: center;font-weight: bold;">Nama Mesin</td>
					<td colspan="2" style="border: 1px solid black;vertical-align: middle;padding-top:0px;padding-bottom:0px;text-align: center;font-weight: bold;">Kondisi Label</td>
					<td rowspan="2" style="border: 1px solid black;vertical-align: middle;padding-top:0px;padding-bottom:0px;text-align: center;font-weight: bold;">Keterangan</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;vertical-align: middle;padding-top:0px;padding-bottom:0px;text-align: center;font-weight: bold;">Arah Putaran</td>
					<td style="border: 1px solid black;vertical-align: middle;padding-top:0px;padding-bottom:0px;text-align: center;font-weight: bold;">Sisa Putaran</td>
				</tr>
				<?php $no = 1; ?>
				<form role="form" method="post" action="{{url('index/labeling/approval/'.$id.'/'.$month)}}">
				@foreach($labeling2 as $labeling)
				<tr>
					<td style="border: 1px solid black;vertical-align: middle;padding-top:0px;padding-bottom:0px;text-align: center;">{{ $no }}</td>
					<td style="border: 1px solid black;vertical-align: middle;padding-top:0px;padding-bottom:0px;text-align: center;">{{ $labeling->date }}</td>
					<td style="border: 1px solid black;vertical-align: middle;padding-top:0px;padding-bottom:0px;text-align: center;">{{ $labeling->nama_mesin }}</td>
					<td style="border: 1px solid black;vertical-align: middle;padding-top:0px;padding-bottom:0px;text-align: center;"><img width="100px" src="{{ url('/data_file/labeling/'.$labeling->foto_arah_putaran) }}"></td>
					<td style="border: 1px solid black;vertical-align: middle;padding-top:0px;padding-bottom:0px;text-align: center;"><img width="100px" src="{{ url('/data_file/labeling/'.$labeling->foto_sisa_putaran) }}"></td>
					<td style="border: 1px solid black;vertical-align: middle;padding-top:0px;padding-bottom:0px;text-align: center;">{{ $labeling->keterangan }}</td>
					@if($jml_null > 0)
					<td id="approval2" class="head" style="border: 1px solid black;vertical-align: middle">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						@if($labeling->approval == Null)
						<label class="label label-success"><input type="checkbox" id="customCheck" name="approve[]" value="{{ $labeling->id_labeling }}">Approve</label>
						@endif
					</td>
					@endif
				</tr>
				<?php $no++ ?>
				@endforeach
				@if($jml_null > 0)
				<tr class="head" id="approval3">
					<td style="border: 1px solid black;" align="right" colspan="7"><button class="btn btn-success" type="submit">Approve</button></td>
				</tr>
				@endif
				</form>
			</tbody>
		</table>
	</div>
  </div>
  @endsection
<style>
.table {
  border: 1px solid black;
  border-collapse: collapse;
  font-family:"Arial";
  padding: 5px;
}
#table2 {
	border: 1px solid black;
  border-collapse: collapse;
  font-family:"Arial";
  padding: 5px;
}
</style>
<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<script>
    // setTimeout(function () { window.print(); }, 200);
    function myFunction() {
	  window.print();
	}

	function checkAll(isChecked){
		if(isChecked){
			$(':checkbox').attr('checked',true);
		}
		else{
			$(':checkbox').attr('checked',false);
		}
	}
	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
	});
</script>
