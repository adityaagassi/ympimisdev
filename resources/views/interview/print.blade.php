@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Print {{ $activity_name }} - {{ $departments }}
    <small>Paper Orientation = Landscape</small>
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
					<td style="border: 1px solid black;" colspan="2">Department</td>
					<td style="border: 1px solid black;" colspan="3">{{ $departments }}</td>
					<td rowspan="5" colspan="5" style="border: 1px solid black;padding: 15px;vertical-align: middle;"><center><b>{{ $activity_name }}</b></center></td>
					<td style="border: 1px solid black;vertical-align: middle;" rowspan="5"><center>Checked<br><br>
						@if($interview->approval != Null)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $interview->approved_date }}</b>
						@endif<br><br><br>
					{{ $interview->foreman }}<br>Foreman</center></td>
					<td style="border: 1px solid black;vertical-align: middle;" rowspan="5"><center>Prepared<br><br>
						@if($interview->approval_leader != Null)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $interview->approved_date_leader }}</b>
						@endif<br><br><br>
						{{ $interview->leader }}<br>Leader</center></td>
				</tr>
				<tr>
					<td style="border: 1px solid black;" colspan="2">Section</td>
					<td style="border: 1px solid black;" colspan="3">{{ $interview->section }}</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;" colspan="2">Sub Section</td>
					<td style="border: 1px solid black;" colspan="3">{{ $interview->subsection }}</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;" colspan="2">Periode</td>
					<td style="border: 1px solid black;" colspan="3">{{ $interview->periode }}</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;" colspan="2">Date</td>
					<td style="border: 1px solid black;" colspan="3">{{ $interview->date }}</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;"><center>No.</center></td>
					<td style="border: 1px solid black;">Nama</td>
					<td style="border: 1px solid black; font-size: 15px;"><center>Filosofi Yamaha</center></td>
					<td style="border: 1px solid black; font-size: 15px;"><center>Aturan K3 Yamaha</center></td>
					<td style="border: 1px solid black; font-size: 15px;"><center>10 Komitmen Berkendara</center></td>
					<td style="border: 1px solid black; font-size: 15px;"><center>Kebijakan Mutu</center></td>
					<td style="border: 1px solid black; font-size: 15px;"><center>5 Dasar Tindakan Bekerja</center></td>
					<td style="border: 1px solid black; font-size: 15px;"><center>6 Pasal Keselamatan Lalu Lintas</center></td>
					<td style="border: 1px solid black; font-size: 15px;"><center>Budaya Kerja YMPI</center></td>
					<td style="border: 1px solid black; font-size: 15px;"><center>5S</center></td>
					<td style="border: 1px solid black; font-size: 15px;"><center>Komitmen Hotel Konsep</center></td>
					<td style="border: 1px solid black; font-size: 15px;"><center>Janji Tindakan Dasar</center></td>
				</tr>
				<?php $no = 1; ?>
				@foreach($interviewDetail as $detail)
				<tr>
					<td style="border: 1px solid black;">{{ $no }}</td>
					<td style="border: 1px solid black;">{{ $detail->name }}</td>
					<td style="border: 1px solid black;font-size: 15px;">
						@if($detail->filosofi_yamaha == 'OK')
							<center><label class="label label-success">O</label></center>
						@elseif($detail->filosofi_yamaha == 'OK (Kurang Lancar)')
							<center><label class="label label-warning">X</label></center>
						@else
							<center><label class="label label-danger">-</label></center>
						@endif
					</td>
					<td style="border: 1px solid black;font-size: 15px;">
						@if($detail->aturan_k3 == 'OK')
							<center><label class="label label-success">O</label></center>
						@elseif($detail->aturan_k3 == 'OK (Kurang Lancar)')
							<center><label class="label label-warning">X</label></center>
						@else
							<center><label class="label label-danger">-</label></center>
						@endif
					</td>
					<td style="border: 1px solid black;font-size: 15px;">
						@if($detail->komitmen_berkendara == 'OK')
							<center><label class="label label-success">O</label></center>
						@elseif($detail->komitmen_berkendara == 'OK (Kurang Lancar)')
							<center><label class="label label-warning">X</label></center>
						@else
							<center><label class="label label-danger">-</label></center>
						@endif
					</td>
					<td style="border: 1px solid black;font-size: 15px;">
						@if($detail->kebijakan_mutu == 'OK')
							<center><label class="label label-success">O</label></center>
						@elseif($detail->kebijakan_mutu == 'OK (Kurang Lancar)')
							<center><label class="label label-warning">X</label></center>
						@else
							<center><label class="label label-danger">-</label></center>
						@endif
					</td>
					<td style="border: 1px solid black;font-size: 15px;">
						@if($detail->dasar_tindakan_bekerja == 'OK')
							<center><label class="label label-success">O</label></center>
						@elseif($detail->dasar_tindakan_bekerja == 'OK (Kurang Lancar)')
							<center><label class="label label-warning">X</label></center>
						@else
							<center><label class="label label-danger">-</label></center>
						@endif
					</td>
					<td style="border: 1px solid black;font-size: 15px;">
						@if($detail->enam_pasal_keselamatan == 'OK')
							<center><label class="label label-success">O</label></center>
						@elseif($detail->enam_pasal_keselamatan == 'OK (Kurang Lancar)')
							<center><label class="label label-warning">X</label></center>
						@else
							<center><label class="label label-danger">-</label></center>
						@endif
					</td>
					<td style="border: 1px solid black;font-size: 15px;">
						@if($detail->budaya_kerja == 'OK')
							<center><label class="label label-success">O</label></center>
						@elseif($detail->budaya_kerja == 'OK (Kurang Lancar)')
							<center><label class="label label-warning">X</label></center>
						@else
							<center><label class="label label-danger">-</label></center>
						@endif
					</td>
					<td style="border: 1px solid black;font-size: 15px;">
						@if($detail->budaya_5s == 'OK')
							<center><label class="label label-success">O</label></center>
						@elseif($detail->budaya_5s == 'OK (Kurang Lancar)')
							<center><label class="label label-warning">X</label></center>
						@else
							<center><label class="label label-danger">-</label></center>
						@endif
					</td>
					<td style="border: 1px solid black;font-size: 15px;">
						@if($detail->komitmen_hotel_konsep == 'OK')
							<center><label class="label label-success">O</label></center>
						@elseif($detail->komitmen_hotel_konsep == 'OK (Kurang Lancar)')
							<center><label class="label label-warning">X</label></center>
						@else
							<center><label class="label label-danger">-</label></center>
						@endif
					</td>
					<td style="border: 1px solid black;font-size: 15px;">
						@if($detail->janji_tindakan_dasar == 'OK')
							<center><label class="label label-success">O</label></center>
						@elseif($detail->janji_tindakan_dasar == 'OK (Kurang Lancar)')
							<center><label class="label label-warning">X</label></center>
						@else
							<center><label class="label label-danger">-</label></center>
						@endif
					</td>
				</tr>
				<?php $no++ ?>
				@endforeach
				<tr>
					<td colspan="2">Keterangan :</td>
					<td style="vertical-align: middle;"><center><label class="label label-success">OK</label></center></td>
					<td style="vertical-align: middle;"><center><label class="label label-warning">OK (Kurang Lancar)</label></center></td>
					<td style="vertical-align: middle;"><center><label class="label label-danger">Not OK</label></center></td>
				</tr>
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
</script>
