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
						{{ $leader }}<br>Leader</center></td>
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
					<td colspan="11" class="head" style="vertical-align: middle"><center>Picture<br><img width="100%" src="{{ url('/data_file/cek_produk_pertama/'.$first_product_audit->foto_aktual) }}"></center></td>
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