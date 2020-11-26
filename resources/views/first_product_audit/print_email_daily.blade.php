@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Approval {{ $activity_name }} - {{ $leader }}
    <a style="margin-right: 10px" class="btn btn-info pull-right" href="{{url('index/first_product_audit/print_first_product_audit_daily/'.$id.'/'.$id_first_product_audit.'/'.$month)}}">Cetak / Save PDF</a>
    <!-- <button class="btn btn-primary pull-right" onclick="myFunction()">Print</button> -->
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
      	<table style="width: 100%; border-collapse: collapse; text-align: left;" >
			<tbody>
				<tr>
					<td colspan="11" style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;"><img width="80px" src="{{ public_path('images/logo_yamaha2.png') }}" alt=""></td>
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;" colspan="11" class="head">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td  class="head" style="padding-top: 0px;padding-bottom: 0px;">Department</td>
					<td  class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ strtoupper($departments) }}</td>
					<td class="head" rowspan="8"  style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px;"><center><b>{{ $activity_name }}</b></center></td>
					<td class="head" rowspan="8" style="padding-top: 0px;padding-bottom: 0px;"><center>Checked<br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif
						<br>
						{{ $foreman }}<br>Foreman</center></td>
					<td class="head" rowspan="8" style="padding-top: 0px;padding-bottom: 0px;"><center>Prepared<br>
						@if($jml_null_leader == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date_leader }}</b>
						@endif
						<br>
						{{ $leader }}<br>Leader</center>
					</td>
					@if($jml_null > 0)
					<td rowspan="8" id="approval1" style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;"><center>Approval</center></td>
					@endif
				</tr>
				<tr>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">Sub Section</td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ strtoupper($subsection) }}</td>
				</tr>
				<tr>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">Proses</td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ $proses }}</td>
				</tr>
				<tr>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">Jenis</td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ $jenis }}</td>
				</tr>
				<tr>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">Standar Kualitas</td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ $standar_kualitas }}</td>
				</tr>
				<tr>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">Tool Check</td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ $tool_check }}</td>
				</tr>
				<tr>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">Jumlah Cek</td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ $jumlah_cek }}</td>
				</tr>
				<tr>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">Bulan</td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ $monthTitle }}</td>
				</tr>
				<tr>
					<th style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px;"><center>Date</center></th>
					<th style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px;"><center>Judgement</center></th>
					<th style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px;"><center>Note</center></th>
					<th style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px;"><center>PIC</center></th>
					<th style="vertical-align: middle;padding-top: 0px;padding-bottom: 0px;"><center>Auditor</center></th>
				</tr>
				<form role="form" method="post" action="{{url('index/first_product_audit/approval_daily/'.$id.'/'.$id_first_product_audit.'/'.$month)}}">
				@foreach($first_product_audit as $first_product_audit)
				<tr>
					<td class="head" style="vertical-align: middle;"><center>{{ $first_product_audit->date }}</center></td>
					<td class="head" style="vertical-align: middle;"><center>{{ $first_product_audit->judgement }}</center></td>
					<td class="head" style="vertical-align: middle;"><center><?php echo $first_product_audit->note ?></center></td>
					<td class="head" style="vertical-align: middle;">{{ $first_product_audit->pic }}</td>
					<td class="head" style="vertical-align: middle;">{{ $first_product_audit->auditor }}</td>
					@if($jml_null > 0)
					<td id="approval2" class="head" style="border: 1px solid black;vertical-align: middle">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						@if($first_product_audit->approval == Null)
						<label class="label label-success"><input type="checkbox" id="customCheck" name="approve[]" value="{{ $first_product_audit->id_first_product_audit_details }}">Approve</label>
						@endif
					</td>
					@endif
				</tr>
				@endforeach
				@if($jml_null > 0)
				<tr class="head" id="approval3">
					<td style="border: 1px solid black;" align="right" colspan="12"><button class="btn btn-success" type="submit">Approve</button></td>
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