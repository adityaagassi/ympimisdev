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
      	<table>
			<tbody>
				<tr>
					<td style="border: 1px solid black;" colspan="11" class="head">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td colspan="2" class="head">Department</td>
					<td colspan="2" class="head">{{ $departments }}</td>
					<td class="head" rowspan="8" colspan="5" style="vertical-align: middle"><center><b>{{ $activity_name }}</b></center></td>
					<td class="head" rowspan="8"><center>Checked<br><br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif
						<br><br>
						{{ $foreman }}<br>Foreman</center></td>
					<td class="head" rowspan="8"><center>Prepared<br><br>
						@if($jml_null_leader == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date_leader }}</b>
						@endif
						<br><br>
						{{ $leader }}<br>Leader</center>
					</td>
					@if($jml_null > 0 && $role_code != 'M')
					<td rowspan="8" id="approval1" style="border: 1px solid black;vertical-align: middle"><center>Approval</center></td>
					@endif
				</tr>
				<tr>
					<td colspan="2" class="head">Sub Section</td>
					<td colspan="2" class="head">{{ $subsection }}</td>
				</tr>
				<tr>
					<td colspan="2" class="head">Proses</td>
					<td colspan="2" class="head">{{ $proses }}</td>
				</tr>
				<tr>
					<td colspan="2" class="head">Jenis</td>
					<td colspan="2" class="head">{{ $jenis }}</td>
				</tr>
				<tr>
					<td colspan="2" class="head">Standar Kualitas</td>
					<td colspan="2" class="head">{{ $standar_kualitas }}</td>
				</tr>
				<tr>
					<td colspan="2" class="head">Tool Check</td>
					<td colspan="2" class="head">{{ $tool_check }}</td>
				</tr>
				<tr>
					<td colspan="2" class="head">Jumlah Cek</td>
					<td colspan="2" class="head">{{ $jumlah_cek }}</td>
				</tr>
				<tr>
					<td colspan="2" class="head">Bulan</td>
					<td colspan="2" class="head">{{ $monthTitle }}</td>
				</tr>
				<form role="form" method="post" action="{{url('index/first_product_audit/approval/'.$id.'/'.$id_first_product_audit.'/'.$month)}}">
				@foreach($first_product_audit as $first_product_audit)
				<tr>
				</tr>
				<tr>
					<td class="head" style="vertical-align: middle"><center>Date</center></td>
					<td class="head" style="vertical-align: middle"><center>{{ $first_product_audit->date }}</center></td>
					<td rowspan="2" colspan="9" class="head" style="vertical-align: middle">Note : <?php echo $first_product_audit->note ?></td>
				</tr>
				<tr>
					<td class="head" style="vertical-align: middle"><center>Auditor</center></td>
					<td class="head" style="vertical-align: middle"><center>{{ $first_product_audit->leader }}</center></td>
				</tr>
				<tr>
					<td colspan="11" class="head" style="vertical-align: middle"><center>Picture<br><?php echo $first_product_audit->foto_aktual ?></center></td>
					@if($jml_null > 0 && $role_code != 'M')
					<td id="approval2" class="head" style="border: 1px solid black;vertical-align: middle">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						@if($first_product_audit->approval == Null)
						<label class="label label-success"><input type="checkbox" id="customCheck" name="approve[]" value="{{ $first_product_audit->id_first_product_audit_details }}">Approve</label>
						@endif
					</td>
					@endif
				</tr>
				@endforeach
				@if($jml_null > 0 && $role_code != 'M')
				<tr class="head" id="approval3">
					<td style="border: 1px solid black;" align="right" colspan="12"><button class="btn btn-success" type="submit">Submit</button></td>
				</tr>
				@endif
				</form>
			</tbody>
		</table>
	</div>
  </div>
  @endsection

<script>
    // setTimeout(function () { window.print(); }, 200);
    function myFunction() {
	  window.print();
	}
	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
	});
</script>