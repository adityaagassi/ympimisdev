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
  <div class="box box-solid">
      <div class="box-body">
      	<table class="table">
			<tbody>
				<tr>
					<td style="border: 1px solid black;" colspan="10" class="head">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td class="head" style="vertical-align: middle;">Department</td>
					<td class="head" style="vertical-align: middle;">{{ $departments }}</td>
					<td class="head" rowspan="2" colspan="4" style="padding: 15px;vertical-align: middle"><center><b>{{ $activity_name }}</b></center></td>
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
					@if($jml_null > 0 && $role_code != 'M')
						<td rowspan="3" id="approval1"><center>Approval</center></td>
					@endif
				</tr>
				<tr>
					<td class="head" style="vertical-align: middle;">Month</td>
					<td class="head" style="vertical-align: middle;">{{ $monthTitle }}</td>
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
				<form role="form" method="post" action="{{url('index/ng_finding/approval/'.$id.'/'.$month)}}">
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
					@if($jml_null > 0 && $role_code != 'M')
					<td id="approval2" style="vertical-align: middle;">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						@if($ng_finding->approval == Null)
						<label class="label label-success"><input type="checkbox" class="minimal-red" name="approve[]" value="{{ $ng_finding->id_ng_finding }}">
						    Approve</label>
						@endif
					</td>
					@endif
				</tr>
				@endforeach
				@if($jml_null > 0 && $role_code != 'M')
				<tr id="approval3">
					<td align="right" colspan="10"><button class="btn btn-success" type="submit">Submit</button></td>
				</tr>
				@endif
				</form>
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