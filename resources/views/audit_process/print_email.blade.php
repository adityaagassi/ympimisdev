@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Approval {{ $activity_name }} - {{ $leader }}
    <a style="margin-right: 10px" class="btn btn-info pull-right" href="{{url('index/audit_process/print_audit_process/'.$id.'/'.$month)}}">Cetak / Save PDF</a>
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
  <div class="box box-solid">
      <div class="box-body">
      	<table>
			<tbody>
				<tr>
					<td colspan="10" style="padding-top: 0px;padding-bottom: 0px;">
						<img style="width: 80px" src="{{ asset('images/logo_yamaha2.png') }}" alt="">
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;" colspan="10" class="head">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td colspan="2" class="head">Department</td>
					<td colspan="2" class="head">{{ strtoupper($departments) }}</td>
					<td class="head" rowspan="4" colspan="5" style="vertical-align: middle"><center><b>{{ $activity_name }}</b></center></td>
					<td class="head" rowspan="4"><center>Checked<br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif
						<br>
						{{ $foreman }}<br>Foreman</center>
					</td>
					@if($jml_null > 0 && $role_code != 'M')
					<td rowspan="7" id="approval1" style="border: 1px solid black;vertical-align: middle"><center>Approval<br><label class="label label-success"><input type="checkbox" onclick="checkAll(this.checked)">Check All</label></center></td>
					@endif
				</tr>
				<tr>
					<td colspan="2" class="head">Section</td>
					<td colspan="2" class="head">{{ strtoupper($section) }}</td>
				</tr>
				<tr>
					<td colspan="2" class="head">Product</td>
					<td colspan="2" class="head">{{ $product }}</td>
				</tr>
				<tr>
					<td colspan="2" class="head">Bulan</td>
					<td colspan="2" class="head">{{ $monthTitle }}</td>
				</tr>
				<tr style="font-weight: bold;">
					<td rowspan="2" class="head" style="vertical-align: middle"><center>No.</center></td>
					<td rowspan="2" class="head" style="vertical-align: middle"><center>Tanggal</center></td>
					<td rowspan="2" class="head" style="vertical-align: middle"><center>Nama Proses</center></td>
					<td rowspan="2" class="head" style="vertical-align: middle"><center>Operator</center></td>
					<td rowspan="2" class="head" style="vertical-align: middle"><center>Auditor</center></td>
					<td colspan="4" class="head" style="vertical-align: middle"><center>Point Audit</center></td>
					<td rowspan="2" class="head" style="vertical-align: middle"><center>Keterangan</center></td>
				</tr>
				<tr style="font-weight: bold;">
					<td class="head" style="vertical-align: middle"><center>Cara Proses</center></td>
					<td class="head" style="vertical-align: middle"><center>Kondisi Cara Proses</center></td>
					<td class="head" style="vertical-align: middle"><center>Pemahaman</center></td>
					<td class="head" style="vertical-align: middle"><center>Kondisi Pemahaman</center></td>
				</tr>
				<?php $no = 1 ?>
				<form role="form" method="post" action="{{url('index/audit_process/approval/'.$id.'/'.$month)}}">
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
					@if($jml_null > 0 && $role_code != 'M')
					<td id="approval2" class="head" style="border: 1px solid black;vertical-align: middle">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						@if($audit_process->approval == Null)
						<label class="label label-success"><input type="checkbox" id="customCheck" name="approve[]" value="{{ $audit_process->id_audit_process }}">Approve</label>
						@endif
					</td>
					@endif
				</tr>
				<?php $no++ ?>
				@endforeach
				@if($jml_null > 0 && $role_code != 'M')
				<tr class="head" id="approval3">
					<td style="border: 1px solid black;" align="right" colspan="11"><button class="btn btn-success" type="submit">Approve</button></td>
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
	function checkAll(isChecked){
		if(isChecked){
			$(':checkbox').attr('checked',true);
		}
		else{
			$(':checkbox').attr('checked',false);
		}
	}
	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
	});
</script>