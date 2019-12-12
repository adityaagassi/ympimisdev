@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Print {{ $activity_name }} - {{ $departments }}
    <small>Page Orientation : Landscape</small>
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
      <div class="box-body" style="overflow-x: scroll;">
		<table class="table" style="border: 1px solid black;">
			<tbody>
				<tr>
					<td colspan="{{ $countdate+1 }}" style="border: 1px solid black;"><img width="80px" src="{{ asset('images/logo_yamaha2.png') }}" alt=""></td>
				</tr>
				<tr>
					<td style="border: 1px solid black;vertical-align: middle;" colspan="{{ $countdate+1 }}">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<?php $colspan = $countdate - 16 ?>
				<tr>
					<td style="border: 1px solid black;">Department</td>
					<td style="border: 1px solid black;" colspan="2">{{ $departments }}</td>
					<td rowspan="3" colspan="16" style="border: 1px solid black;padding: 15px;vertical-align: middle;"><center><b>{{ $activity_name }}</b></center></td>
					<td style="border: 1px solid black;vertical-align: middle;" rowspan="3"><center>Checked<br><br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif<br>
					{{ $foreman }}<br>Foreman</center></td>
					<td style="border: 1px solid black;vertical-align: middle;" rowspan="3" colspan="{{ $colspan }}"><center>Prepared<br><br>
						@if($jml_null_leader == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date_leader }}</b>
						@endif<br>
						{{ $leader }}<br>Leader</center></td>
				</tr>
				<tr>
					<td style="border: 1px solid black;">Subsection</td>
					<td style="border: 1px solid black;" colspan="2">{{ $subsection }}</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;">Month</td>
					<td style="border: 1px solid black;" colspan="2">{{ $monthTitle }}</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;"><center>Point Check / Date</center></td>
					@foreach($date as $date)
					<td style="border: 1px solid black;"><center>{{ substr($date->week_date,-2) }}</center></td>
					<?php $datenow[] = $date->week_date ?>
					@endforeach
				</tr>
				@foreach($point_check as $point_check)
				<tr>
					<td style="border: 1px solid black;"><center>{{ $point_check->point_check }}</center></td>
					<?php
					for($i = 0;$i<count($datenow);$i++){ ?>
						<?php $condition = DB::SELECT("select area_checks.`condition`,pic,date,area_checks.id as id_area_check
							from weekly_calendars
							left join area_checks on area_checks.date = week_date
							LEFT JOIN area_check_points on area_check_points.id = area_checks.area_check_point_id
							where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$month."'
							and area_checks.date = '".$datenow[$i]."'
							and area_check_points.id = '".$point_check->id."'
							and area_checks.activity_list_id = '".$id."'
						  and area_checks.deleted_at is null
							and week_date not in (select tanggal from ftm.kalender)"); ?>
						<td style="border: 1px solid black;"><center><?php for($j = 0;$j < count($condition) ; $j++){
							if($condition[$j]->condition == 'Good'){
								echo '<label class="label label-success">-</label>';
							}
							else{
								echo '<label class="label label-danger">-</label>';
							}
						} ?></center></td>
					<?php } ?>
				</tr>
				@endforeach
				<tr>
					<td style="border: 1px solid black;"><center>PIC Check</center></td>
					<?php
					for($i = 0;$i<count($datenow);$i++){ ?>
						<?php $condition = DB::SELECT("select area_checks.`condition`,pic,date,area_checks.id as id_area_check
							from weekly_calendars
							left join area_checks on area_checks.date = week_date
							LEFT JOIN area_check_points on area_check_points.id = area_checks.area_check_point_id
							where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$month."'
							and area_checks.date = '".$datenow[$i]."'
							and area_check_points.id = '".$point_check->id."'
							and area_checks.activity_list_id = '".$id."'
						  and area_checks.deleted_at is null
							and week_date not in (select tanggal from ftm.kalender)"); ?>
						<td style="border: 1px solid black;"><center><?php for($j = 0;$j < count($condition) ; $j++){
							echo $condition[$j]->pic;
						} ?></center></td>
					<?php } ?>
				</tr>
				<tr>
					<td style="border: 1px solid black;" colspan="{{ $countdate+1 }}"><center>Keterangan : </center></td>
				</tr>
				<tr>
					<td style="border: 1px solid black;" colspan="{{ $countdate+1 }}"><center><label class="label label-success">Good<label></center></td>
				</tr>
				<tr>
					<td style="border: 1px solid black;" colspan="{{ $countdate+1 }}"><center><label class="label label-danger">Not Good<label></center></td>
				</tr>
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