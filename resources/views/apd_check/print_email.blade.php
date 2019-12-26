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
					<td style="border: 1px solid black;" colspan="10" class="head">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td colspan="2" class="head">Department</td>
					<td colspan="3" class="head">{{ $departments }}</td>
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
					@if($jml_null > 0 && $role_code != 'M')
						<td rowspan="4" id="approval1"><center>Approval</center></td>
					@endif
				</tr>
				<tr>
					<td colspan="2" class="head">Sub Section</td>
					<td colspan="3" class="head">{{ $subsection }}</td>
				</tr>
				<tr>
					<td colspan="2" class="head">Month</td>
					<td colspan="3" class="head">{{ $monthTitle }}</td>
				</tr>
				<tr>
					<td class="head"><center>Date</center></td>
					<td class="head"><center>Nama</center></td>
					<td class="head"><center>Proses</center></td>
					<td class="head"><center>Jenis APD</center></td>
					<td class="head"><center>Kondisi</center></td>
					<td class="head" colspan="3"><center>Foto Aktual</center></td>
					<td class="head"><center>Checked By</center></td>
				</tr>
				<form role="form" method="post" action="{{url('index/apd_check/approval/'.$id.'/'.$month)}}">
				@foreach($apd_check as $apd_check)
				<tr>
					<td class="head" style="vertical-align: middle"><center>{{ $apd_check->date }}</center></td>
					<td style="vertical-align: middle" class="head"><center>{{ $apd_check->pic }}</center></td>
					<td style="vertical-align: middle" class="head"><center>{{ $apd_check->proses }}</center></td>
					<td style="vertical-align: middle" class="head"><center>{{ $apd_check->jenis_apd }}</center></td>
					<td style="vertical-align: middle" class="head"><center>{{ $apd_check->kondisi }}</center></td>
					<td style="vertical-align: middle" class="head" colspan="3"><center><?php echo  $apd_check->foto_aktual ?></center></td>
					<td style="vertical-align: middle" class="head"><center>{{ $apd_check->leader }}</center></td>
					@if($jml_null > 0 && $role_code != 'M')
					<td id="approval2" style="vertical-align: middle;">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						@if($apd_check->approval == Null)
						<label class="label label-success"><input type="checkbox" class="minimal-red" name="approve[]" value="{{ $apd_check->id_apd_check }}">
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