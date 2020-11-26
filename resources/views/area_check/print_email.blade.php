@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Approval {{ $activity_name }} - {{ $leader }}
    <a class="btn btn-info pull-right" style="margin-right: 10px" href="{{url('index/area_check/print_area_check/'.$id.'/'.$month)}}">Cetak / Save PDF</a>
    @if($jml_null > 0 && $role_code != 'M')
	<label id="approval1" class="label label-success pull-right"><input type="checkbox" onclick="checkAll(this.checked)">Approve All</label>
	@endif
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
	  #approval4 {
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
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-solid">
    {{-- <div class="box-header with-border">
      <h3 class="box-title">Detail User</h3>
    </div>   --}}
      <div class="box-body" style="overflow-x: scroll;">
		<table class="table" style="border: 1px solid black;">
			<tbody>
				<tr>
					<td colspan="{{ $countdate+1 }}" style="border: 1px solid black;"><img width="80px" src="{{ public_path('images/logo_yamaha2.png') }}" alt=""></td>
				</tr>
				<tr>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px" colspan="{{ $countdate+1 }}">PT. YAMAHA MUSICAL PRODUCTS INDONESIA
					</td>
				</tr>
				<?php $colspan = $countdate - 16 ?>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px">Department</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px" colspan="2">{{ strtoupper($departments) }}</td>
					<td rowspan="3" colspan="16" style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;vertical-align: middle;"><center><b>{{ $activity_name }}</b></center></td>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px" rowspan="3"><center>Checked<br>
						@if($jml_null == 0)
							<b style='color:green !important'>Approved</b><br>
							<b style='color:green !important'>{{ $approved_date }}</b>
						@endif<br>
					{{ $foreman }}<br>Foreman</center></td>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px" rowspan="3" colspan="{{ $colspan }}"><center>Prepared<br>
						@if($jml_null_leader == 0)
							<b style='color:green !important'>Approved</b><br>
							<b style='color:green !important'>{{ $approved_date_leader }}</b>
						@endif<br>
						{{ $leader }}<br>Leader</center></td>
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px">Subsection</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px" colspan="2">{{ strtoupper($subsection) }}</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px">Bulan</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px" colspan="2">{{ $monthTitle }}</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;"><center>Point Check / Date</center></td>
					@foreach($date as $date)
					<td style="border: 1px solid black;"><center>{{ substr($date->week_date,-2) }}</center></td>
					<?php $datenow[] = $date->week_date ?>
					@endforeach
				</tr>
				<form role="form" method="post" action="{{url('index/area_check/approval/'.$id.'/'.$month)}}">
				@foreach($point_check as $point_check)
				<tr>
					<td style="border: 1px solid black;"><center>{{ $point_check->point_check }}</center></td>
					<?php
					for($i = 0;$i<count($datenow);$i++){ ?>
						<?php $condition = DB::SELECT("select area_checks.`condition`,pic,date,area_checks.id as id_area_check,approval
							from weekly_calendars
							left join area_checks on area_checks.date = week_date
							LEFT JOIN area_check_points on area_check_points.id = area_checks.area_check_point_id
							where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$month."'
							and area_checks.date = '".$datenow[$i]."'
							and area_check_points.id = '".$point_check->id."'
							and area_checks.activity_list_id = '".$id."'
						  and area_checks.deleted_at is null
							and week_date not in (select tanggal from ftm.kalender)"); ?>
						<td style="border: 1px solid black;"><center><?php for($j = 0;$j < count($condition) ; $j++){ ?>
							<?php if($condition[$j]->condition == 'Good'){
								echo '<label class="label label-success">O</label>';
							}
							else{
								echo '<label class="label label-danger">X</label>';
							} ?>
							<br>
							@if($jml_null > 0 && $role_code != 'M')
								<input type="hidden" id="approval2" value="{{csrf_token()}}" name="_token" />
								@if($condition[$j]->approval == Null)
								<label id="approval4" class="label label-success"><input type="checkbox" id="customCheck" name="approve[]" value="{{ $condition[$j]->id_area_check }}"></label>
								@endif
							@endif
						<?php } ?></center></td>
					<?php } ?>
				</tr>
				@endforeach
				<tr>
					<td style="border: 1px solid black;"><center>PIC Check</center></td>
					<?php
					for($i = 0;$i<count($datenow);$i++){ ?>
						<?php $condition = DB::SELECT("select area_checks.`condition`,pic,date,area_checks.id as id_area_check,approval
							from weekly_calendars
							left join area_checks on area_checks.date = week_date
							LEFT JOIN area_check_points on area_check_points.id = area_checks.area_check_point_id
							where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$month."'
							and area_checks.date = '".$datenow[$i]."'
							and area_check_points.id = '".$point_check->id."'
							and area_checks.activity_list_id = '".$id."'
						  and area_checks.deleted_at is null
							and week_date not in (select tanggal from ftm.kalender)"); ?>
						<td style="border: 1px solid black;"><center><?php for($j = 0;$j < count($condition) ; $j++){ ?>
							<?php echo $condition[$j]->pic; ?>
						<?php } ?></center></td>
					<?php } ?>
				</tr>
				@if($jml_null > 0 && $role_code != 'M')
				<tr class="head" id="approval3">
					<td style="border: 1px solid black;" colspan="{{ $countdate+1 }}"><center><button class="btn btn-success" type="submit">Approve</button></center></td>
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
	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
	});
	function checkAll(isChecked){
		if(isChecked){
			$(':checkbox').attr('checked',true);
		}
		else{
			$(':checkbox').attr('checked',false);
		}
	}
</script>
