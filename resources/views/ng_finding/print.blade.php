@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Print {{ $activity_name }} - {{ $leader }}
    <button class="btn btn-primary pull-right" onclick="myFunction()">Print</button>
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
  <div class="box box-solid">
      <div class="box-body">
      	<table class="table">
			<tbody>
				<tr>
					<td style="border: 1px solid black;" colspan="10" class="head">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td class="head" style="vertical-align: middle;">Department</td>
					<td class="head" colspan="2" style="vertical-align: middle;">{{ strtoupper($departments) }}</td>
					<td class="head" rowspan="2" colspan="3" style="font-size:30px;padding: 10px;vertical-align: middle"><center><b>{{ $activity_name }}</b></center></td>
					<td class="head" rowspan="2"><center>Checked<br><br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif
						<br><br>
						{{ $foreman }}<br>Foreman</center>
					</td>
					<td class="head" rowspan="2"><center>Prepared<br><br>
						@if($jml_null_leader == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date_leader }}</b>
						@endif
						<br><br>
						{{ $leader }}<br>Leader</center>
					</td>
				</tr>
				<tr>
					<td class="head" style="vertical-align: middle;">Bulan</td>
					<td class="head" colspan="2" style="vertical-align: middle;">{{ $monthTitle }}</td>
				</tr>
				<tr>
					<td class="head"><center><b>Date</b></center></td>
					<td class="head"><center><b>GMC</b></center></td>
					<td class="head"><center><b>Description</b></center></td>
					<td class="head"><center><b>Qty</b></center></td>
					<td class="head"><center><b>Finder</b></center></td>
					<td class="head"><center><b>Picture</b></center></td>
					<td class="head"><center><b>Defect</b></center></td>
					<td class="head"><center><b>Checked By QA</b></center></td>
				</tr>
				@foreach($ng_finding as $ng_finding)
				<tr>
					<td class="head" style="vertical-align: middle"><center>{{ $ng_finding->date }}</center></td>
					<td style="vertical-align: middle" class="head"><center><?php echo $ng_finding->material_number ?></center></td>
					<td style="vertical-align: middle" class="head"><center><?php echo $ng_finding->material_description ?></center></td>
					<td style="vertical-align: middle" class="head"><center><?php echo  $ng_finding->quantity ?></center></td>
					<td style="vertical-align: middle" class="head"><center><?php echo  $ng_finding->finder ?></center></td>
					<?php if(strpos($ng_finding->picture, '<p>') !== false){ ?>
						<td style="vertical-align: middle" class="head"><center><?php echo  $ng_finding->picture ?></center></td>
					<?php }else{ ?>
						<td style="vertical-align: middle" class="head"><center><img width="200px" src="{{ url('/data_file/ng_finding/'.$ng_finding->picture) }}"></center></td>
					<?php } ?>
					<td style="vertical-align: middle" class="head"><center><?php echo  $ng_finding->defect ?></center></td>
					<td style="vertical-align: middle" class="head"><center><?php echo  $ng_finding->checked_qa ?></center></td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
  </div>
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