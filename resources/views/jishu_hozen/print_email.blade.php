@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Cetak {{ $activity_name }} - {{ $leader }}
    <a class="btn btn-info pull-right" href="{{url('index/jishu_hozen/print_jishu_hozen/'.$id.'/'.$jishu_hozen_id.'/'.$month)}}">Cetak / Save PDF</a>
  </h1>
  <ol class="breadcrumb">
  </ol>
  <style>
	table, th, td {
	  border: 1px solid black;
	  border-collapse: collapse;
	  font-family:"Arial";
	  padding: 5px;
	  vertical-align:middle;
	  font-size: 10px
	}
	@media print {
		body {-webkit-print-color-adjust: exact;}
	}
   </style>
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
      	<table style="width: 100%">
			<tbody>
				<tr>
					<td style="border: 1px solid black;" colspan="8"  class="head">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td colspan="2" class="head">Department</td>
					<td colspan="2" class="head">{{ strtoupper($departments) }}</td>
					<td class="head" rowspan="4" colspan="2" style="vertical-align: middle"><center><b>{{ $activity_name }}</b></center></td>
					<td class="head" rowspan="4"><center>Checked<br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif
						<br>
						{{ $foreman }}<br>Foreman</center></td>
					<td class="head" rowspan="4"><center>Prepared<br>
						@if($jml_null_leader == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date_leader }}</b>
						@endif
						<br>
						{{ $leader }}<br>Leader</center>
					</td>
					@if($approval == Null && $role_code != 'M')
					<td rowspan="4" id="approval1" style="border: 1px solid black;vertical-align: middle;"><center>Approval</center></td>
					@endif
				</tr>
				<tr>
					<td colspan="2" class="head">Sub Section</td>
					<td colspan="2" class="head">{{ strtoupper($subsection) }}</td>
				</tr>
				<tr>
					<td colspan="2" class="head">Bulan</td>
					<td colspan="2" class="head">{{ $monthTitle }}</td>
				</tr>
				<tr>
					<td colspan="2" class="head">Date</td>
					<td colspan="2" class="head">{{ $date }}</td>
				</tr>
				<form role="form" method="post" action="{{url('index/jishu_hozen/approval/'.$id.'/'.$jishu_hozen_id.'/'.$month)}}">
				@foreach($jishu_hozen as $jishu_hozen)
				<tr>
					<td class="head" colspan="8" style="vertical-align: middle"><center>Picture<br><?php echo $jishu_hozen->foto_aktual ?></center></td>
					@if($jishu_hozen->approval == Null && $role_code != 'M')
						<td id="approval2" style="border-top: 1px solid black;padding-top:50px">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<label class="label label-success"><input type="checkbox" class="minimal-red" name="approve[]" value="{{ $jishu_hozen->id }}">
							    Approve</label>
						</td>
					@endif
				</tr>
				@endforeach
				@if($jishu_hozen->approval == Null && $role_code != 'M')
				<tr id="approval3">
					<td align="right" colspan="9"><button class="btn btn-success" type="submit">Approve</button></td>
				</tr>
				@endif
				</form>
			</tbody>
		</table>
	</div>
  </div>
  @endsection
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