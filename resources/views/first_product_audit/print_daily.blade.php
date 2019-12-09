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
  @if ($errors->has('password'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
    {{ $errors->first() }}
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
					<td class="head">Department</td>
					<td class="head">{{ $departments }}</td>
					<td class="head" rowspan="8" style="vertical-align: middle"><center><b>{{ $activity_name }}</b></center></td>
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
						{{ $leader }}<br>Leader</center></td>
				</tr>
				<tr>
					<td class="head">Sub Section</td>
					<td class="head">{{ $subsection }}</td>
				</tr>
				<tr>
					<td class="head">Proses</td>
					<td class="head">{{ $proses }}</td>
				</tr>
				<tr>
					<td class="head">Jenis</td>
					<td class="head">{{ $jenis }}</td>
				</tr>
				<tr>
					<td class="head">Standar Kualitas</td>
					<td class="head">{{ $standar_kualitas }}</td>
				</tr>
				<tr>
					<td class="head">Tool Check</td>
					<td class="head">{{ $tool_check }}</td>
				</tr>
				<tr>
					<td class="head">Jumlah Cek</td>
					<td class="head">{{ $jumlah_cek }}</td>
				</tr>
				<tr>
					<td class="head">Bulan</td>
					<td class="head">{{ $monthTitle }}</td>
				</tr>
				<tr>
					<th style="vertical-align: middle"><center>Date</center></th>
					<th style="vertical-align: middle"><center>Judgement</center></th>
					<th style="vertical-align: middle"><center>Note</center></th>
					<th style="vertical-align: middle"><center>PIC</center></th>
					<th style="vertical-align: middle"><center>Auditor</center></th>
				</tr>
				@foreach($first_product_audit as $first_product_audit)
				<tr>
					<td class="head" style="vertical-align: middle"><center>{{ $first_product_audit->date }}</center></td>
					<td class="head" style="vertical-align: middle"><center>{{ $first_product_audit->judgement }}</center></td>
					<td class="head" style="vertical-align: middle"><center><?php echo $first_product_audit->note ?></center></td>
					<td class="head" style="vertical-align: middle">{{ $first_product_audit->pic }}</td>
					<td class="head" style="vertical-align: middle">{{ $first_product_audit->auditor }}</td>
				</tr>
				@endforeach
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
</script>