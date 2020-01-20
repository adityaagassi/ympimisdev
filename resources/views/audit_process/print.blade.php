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
					<td style="border: 1px solid black;" colspan="10" class="head">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td colspan="2" class="head">Department</td>
					<td colspan="2" class="head">{{ $departments }}</td>
					<td class="head" rowspan="5" colspan="5" style="vertical-align: middle"><center><b>{{ $activity_name }}</b></center></td>
					<td class="head" rowspan="5"><center>Checked<br><br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif
						<br><br>
						{{ $foreman }}<br>Foreman</center></td>
				</tr>
				<tr>
					<td colspan="2" class="head">Section</td>
					<td colspan="2" class="head">{{ $section }}</td>
				</tr>
				<tr>
					<td colspan="2" class="head">Product</td>
					<td colspan="2" class="head">{{ $product }}</td>
				</tr>
				<tr>
					<td colspan="2" class="head">Periode</td>
					<td colspan="2" class="head">{{ $periode }}</td>
				</tr>
				<tr>
					<td colspan="2" class="head">Month</td>
					<td colspan="2" class="head">{{ $monthTitle }}</td>
				</tr>
				<tr>
					<td rowspan="2" class="head" style="vertical-align: middle"><center>No.</center></td>
					<td rowspan="2" class="head" style="vertical-align: middle"><center>Tanggal</center></td>
					<td rowspan="2" class="head" style="vertical-align: middle"><center>Nama Proses</center></td>
					<td rowspan="2" class="head" style="vertical-align: middle"><center>Operator</center></td>
					<td rowspan="2" class="head" style="vertical-align: middle"><center>Auditor</center></td>
					<td colspan="4" class="head" style="vertical-align: middle"><center>Point Audit</center></td>
					<td rowspan="2" class="head" style="vertical-align: middle"><center>Keterangan</center></td>
				</tr>
				<tr>
					<td class="head" style="vertical-align: middle"><center>Cara Proses</center></td>
					<td class="head" style="vertical-align: middle"><center>Kondisi Cara Proses</center></td>
					<td class="head" style="vertical-align: middle"><center>Pemahaman</center></td>
					<td class="head" style="vertical-align: middle"><center>Kondisi Pemahaman</center></td>
				</tr>
				<?php $no = 1 ?>
				@foreach($audit_process as $audit_process)
				<tr>
					<td class="head" style="vertical-align: middle"><center>{{ $no }}</center></td>
					<td class="head" style="vertical-align: middle"><center>{{ $audit_process->date }}</center></td>
					<td style="vertical-align: middle" class="head"><center>{{ $audit_process->proses }}</center></td>
					<td style="vertical-align: middle" class="head"><center>{{ $audit_process->operator }}</center></td>
					<td style="vertical-align: middle" class="head"><center>{{ $audit_process->auditor }}</center></td>
					<td style="vertical-align: middle" class="head"><?php echo $audit_process->cara_proses ?></td>
					<td style="vertical-align: middle" class="head"><center>{{ $audit_process->kondisi_cara_proses }}</center></td>
					<td style="vertical-align: middle" class="head"><?php echo $audit_process->pemahaman ?></td>
					<td style="vertical-align: middle" class="head"><center>{{ $audit_process->kondisi_pemahaman }}</center></td>
					<td style="vertical-align: middle" class="head"><center>{{ $audit_process->keterangan }}</center></td>
				</tr>
				<?php $no++ ?>
				@endforeach
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