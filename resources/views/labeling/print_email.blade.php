@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Print {{ $activity_name }} - {{ $departments }}
    <small></small>
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
  @if ($errors->has('password'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
    {{ $errors->first() }}
  </div>   
  @endif
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary">
    {{-- <div class="box-header with-border">
      <h3 class="box-title">Detail User</h3>
    </div>   --}}
      <div class="box-body">
		<table class="table" style="border: 1px solid black;">
			<tbody>
				<tr>
					<td colspan="12" style="border: 1px solid black;"><img width="80px" src="{{ asset('images/logo_yamaha2.png') }}" alt=""></td>
				</tr>
				<tr>
					<td style="border: 1px solid black;vertical-align: middle;" colspan="12">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;">Department</td>
					<td style="border: 1px solid black;">{{ $departments }}</td>
					<td rowspan="5" colspan="2" style="border: 1px solid black;padding: 15px;vertical-align: middle;"><center><b>{{ $activity_name }}</b></center></td>
					<td style="border: 1px solid black;vertical-align: middle;" rowspan="5"><center>Checked<br><br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif<br><br>
					{{ $foreman }}<br>Foreman</center></td>
					<td style="border: 1px solid black;vertical-align: middle;" rowspan="5"><center>Prepared<br><br>
						@if($jml_null_leader == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date_leader }}</b>
						@endif<br><br>
						{{ $leader }}<br>Leader</center>
					</td>
					@if($jml_null > 0)
					<td rowspan="7" id="approval1" style="border: 1px solid black;vertical-align: middle"><center>Approval<br><label class="label label-success"><input type="checkbox" onclick="checkAll(this.checked)">Check All</label></center></td>
					@endif
				</tr>
				<tr>
					<td style="border: 1px solid black;">Section</td>
					<td style="border: 1px solid black;">{{ $section }}</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;">Product</td>
					<td style="border: 1px solid black;">{{ $product }}</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;">Periode</td>
					<td style="border: 1px solid black;">{{ $periode }}</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;">Month</td>
					<td style="border: 1px solid black;">{{ $monthTitle }}</td>
				</tr>
				<tr>
					<td rowspan="2" style="border: 1px solid black;vertical-align: middle;"><center>No.</center></td>
					<td rowspan="2" style="border: 1px solid black;vertical-align: middle;"><center>Date</center></td>
					<td rowspan="2" style="border: 1px solid black;vertical-align: middle;"><center>Nama Mesin</center></td>
					<td colspan="2" style="border: 1px solid black;vertical-align: middle;"><center>Kondisi Label</center></td>
					<td rowspan="2" style="border: 1px solid black;vertical-align: middle;"><center>Keterangan</center></td>
				</tr>
				<tr>
					<td style="border: 1px solid black;vertical-align: middle;"><center>Arah Putaran</center></td>
					<td style="border: 1px solid black;vertical-align: middle;"><center>Sisa Putaran</center></td>
				</tr>
				<?php $no = 1; ?>
				<form role="form" method="post" action="{{url('index/labeling/approval/'.$id.'/'.$month)}}">
				@foreach($labeling2 as $labeling)
				<tr>
					<td style="border: 1px solid black;vertical-align: middle;"><center>{{ $no }}</center></td>
					<td style="border: 1px solid black;vertical-align: middle;"><center>{{ $labeling->date }}</center></td>
					<td style="border: 1px solid black;vertical-align: middle;"><center>{{ $labeling->nama_mesin }}</center></td>
					<td style="border: 1px solid black;vertical-align: middle;"><center><img width="200px" src="{{ url('/data_file/labeling/'.$labeling->foto_arah_putaran) }}"></center></td>
					<td style="border: 1px solid black;vertical-align: middle;"><center><img width="200px" src="{{ url('/data_file/labeling/'.$labeling->foto_sisa_putaran) }}"></center></td>
					<td style="border: 1px solid black;vertical-align: middle;"><center>{{ $labeling->keterangan }}</center></td>
					@if($jml_null > 0)
					<td id="approval2" class="head" style="border: 1px solid black;vertical-align: middle">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						@if($labeling->approval == Null)
						<label class="label label-success"><input type="checkbox" id="customCheck" name="approve[]" value="{{ $labeling->id_labeling }}">Approve</label>
						@endif
					</td>
					@endif
				</tr>
				<?php $no++ ?>
				@endforeach
				@if($jml_null > 0)
				<tr class="head" id="approval3">
					<td style="border: 1px solid black;" align="right" colspan="7"><button class="btn btn-success" type="submit">Submit</button></td>
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
</script>
