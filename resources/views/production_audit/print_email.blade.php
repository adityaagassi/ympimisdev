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
					<td style="border: 1px solid black;" colspan="6">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td rowspan="5" style="width: 20px;vertical-align: middle;"><center><img width="175px" src="{{ asset('images/logo_yamaha2.png') }}" alt=""></center></td>
				</tr>
				<tr>
					<td style="border: 1px solid black;">Department</td>
					<td style="border: 1px solid black;">{{ $departments }}</td>
					<td rowspan="4" colspan="2" style="padding: 15px;border: 1px solid black;vertical-align: middle;"><center><b>{{ $activity_name }}</b></center></td>
					<td rowspan="4" style="border: 1px solid black;vertical-align: middle;"><center>Mengetahui<br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif
						<br>{{ $foreman }}
						<br>Foreman</center>
					</td>
						@if($jml_null > 0)
						<td rowspan="5" id="approval1"><center>Approval</center></td>
						@endif
					</tr>
					<tr>
						<td>Product</td>
						<td>{{ $product }}</td>
					</tr>
					<tr>
						<td>Proses</td>
						<td>{{ $proses }}</td>
					</tr>
					<tr>
						<td>Date</td>
						<td>{{ $date_audit }}</td>
					</tr>
					<tr>
						<td>Point Check</td>
						<td>Cara Cek</td>
						<td>Foto Kondisi Aktual</td>
						<td>Kondisi (OK / NG)</td>
						<td>PIC</td>
						<td>Auditor</td>
					</tr>
					<form role="form" method="post" action="{{url('index/production_audit/approval/'.$id)}}">
					@foreach($production_audit as $production_audit)
					<tr>
						<td><?php echo $production_audit->point_check ?></td>
						<td><?php echo $production_audit->cara_cek ?></td>
						<td><img width="200px" src="{{ url('/data_file/'.$production_audit->foto_kondisi_aktual) }}"></td>
						<td>@if($production_audit->kondisi == "Good")
				              <label class="label label-success">{{$production_audit->kondisi}}</label>
				            @else
				              <label class="label label-danger">{{$production_audit->kondisi}}</label>
				            @endif
			        	</td>
						<td>{{ $production_audit->pic_name }}</td>
						<td>{{ $production_audit->auditor_name }}</td>
						@if($jml_null > 0)
						<td id="approval2">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							@if($production_audit->approval == Null)
							<label><input type="checkbox" class="minimal-red" name="approve[]" value="{{ $production_audit->id_production_audit }}">
							    Approve</label>
							@endif
						</td>
						@endif
					</tr>
					@endforeach
					@if($jml_null > 0)
					<tr id="approval3">
						<td align="right" colspan="7"><button class="btn btn-success" type="submit">Submit</button></td>
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
}
@media print {
	body {-webkit-print-color-adjust: exact;}
}
</style>
<script>
    // setTimeout(function () { window.print(); }, 200);
    function myFunction() {
	  window.print();
	}
</script>
