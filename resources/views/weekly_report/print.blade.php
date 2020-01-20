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
  @if ($errors->has('password'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
    {{ $errors->first() }}
  </div>   
  @endif
  <div class="box box-primary">
      <div class="box-body">
      	<table class="table">
			<tbody>
				<tr>
					<td style="border: 1px solid black;" colspan="10" class="head">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td class="head">Department</td>
					<td class="head">{{ $departments }}</td>
					<td class="head" rowspan="3" colspan="2" style="padding: 15px;vertical-align: middle"><center><b>{{ $activity_name }}</b></center></td>
					<td class="head" rowspan="3"><center>Checked<br><br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif
						<br><br>
						{{ $foreman }}<br>Foreman</center>
					</td>
					<td class="head" rowspan="3"><center>Prepared<br><br>
						@if($jml_null_leader == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date_leader }}</b>
						@endif
						<br><br>
						{{ $leader }}<br>Leader</center>
					</td>
				</tr>
				<tr>
					<td class="head">Sub Section</td>
					<td class="head">{{ $subsection }}</td>
				</tr>
				<tr>
					<td class="head">Month</td>
					<td class="head">{{ $monthTitle }}</td>
				</tr>
				<tr>
					<td class="head"><center><b>Date</b></center></td>
					<td class="head"><center><b>Tinjauan 4M</b></center></td>
					<td class="head"><center><b>Problem / Activity</b></center></td>
					<td class="head"><center><b>Report Action</b></center></td>
					<td class="head"><center><b>Foto Aktual</b></center></td>
					<td class="head"><center><b>Checked By</b></center></td>
				</tr>
				@foreach($weekly_report as $weekly_report)
				<?php $type = [] ?>
				<tr>
					<td class="head" style="vertical-align: middle"><center>{{ $weekly_report->date }}</center></td>
					<td style="vertical-align: middle" class="head"><center><?php $tinjauan = explode(',', $weekly_report->report_type);
												for ($i = 0; $i < count($tinjauan); $i++) {
												 	if($tinjauan[$i] == 1){
												 		$type[] = 'Man';
												 	}elseif ($tinjauan[$i] == 2) {
												 		$type[] = 'Machine';
												 	}elseif ($tinjauan[$i] == 3) {
												 		$type[] = 'Material';
												 	}elseif ($tinjauan[$i] == 4) {
												 		$type[] = 'Method';
												 	}elseif ($tinjauan[$i] == 5) {
												 		$type[] = 'Other';
												 	}
												 }
												 echo implode(' , ', $type);
												 ?></center></td>
					<td style="vertical-align: middle" class="head"><?php echo $weekly_report->problem ?></td>
					<td style="vertical-align: middle" class="head"><?php echo $weekly_report->action ?></td>
					<td style="vertical-align: middle" class="head"><?php echo  $weekly_report->foto_aktual ?></td>
					<td style="vertical-align: middle" class="head"><center>{{ $weekly_report->leader }}</center></td>
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