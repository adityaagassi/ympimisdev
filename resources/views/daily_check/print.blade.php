@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Print {{ $activity_name }} - {{ $departments }}
    <small></small>
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
  @if ($errors->has('password'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
    {{ $errors->first() }}
  </div>   
  @endif
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary">
    {{-- <div class="box-header with-border">
      <h3 class="box-title">Detail User</h3>
    </div>   --}}
      <div class="box-body">
		<table class="table" style="border: 1px solid black;">
			<tbody>
				<tr>
					<td colspan="12" style="border: 1px solid black;"><img width="80px" src="{{ asset('images/logo_yamaha2.png') }}" alt=""></td>
				</tr>
				<tr>
					<td style="border: 1px solid black;vertical-align: middle;" colspan="12">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;">Department</td>
					<td style="border: 1px solid black;">{{ $departments }}</td>
					<td rowspan="3" colspan="2" style="border: 1px solid black;padding: 15px;vertical-align: middle;"><center><b>{{ $activity_name }}</b></center></td>
					<td style="border: 1px solid black;vertical-align: middle;" rowspan="3"><center>Checked<br><br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif<br>
					{{ $foreman }}<br>Foreman</center></td>
					<td style="border: 1px solid black;vertical-align: middle;" rowspan="3"><center>Prepared<br><br>
						@if($jml_null_leader == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date_leader }}</b>
						@endif<br>
						{{ $leader }}<br>Leader</center></td>
				</tr>
				<tr>
					<td style="border: 1px solid black;">Product</td>
					<td style="border: 1px solid black;">{{ $product }}</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;">Month</td>
					<td style="border: 1px solid black;">{{ $monthTitle }}</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;"><center>No.</center></td>
					<td style="border: 1px solid black;"><center>Production Date</center></td>
					<td style="border: 1px solid black;"><center>Check Date</center></td>
					<td style="border: 1px solid black;"><center>Serial Number</center></td>
					<td style="border: 1px solid black;"><center>Condition</center></td>
					<td style="border: 1px solid black;"><center>Keterangan</center></td>
				</tr>
				<?php $no = 1; ?>
				@foreach($daily_check as $daily_check)
				<tr>
					<td style="border: 1px solid black;"><center>{{ $no }}</center></td>
					<td style="border: 1px solid black;"><center>{{ $daily_check->production_date }}</center></td>
					<td style="border: 1px solid black;"><center>{{ $daily_check->check_date }}</center></td>
					<td style="border: 1px solid black;"><center>{{ $daily_check->serial_number }}</center></td>
					<td style="border: 1px solid black;"><center>{{ $daily_check->condition }}</center></td>
					<td style="border: 1px solid black;"><center>{{ $daily_check->keterangan }}</center></td>
				</tr>
				<?php $no++ ?>
				@endforeach
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
	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
	});
</script>
