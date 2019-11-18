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
<table class="table">
	<tbody>
		<tr style="border:0px;">
			<td colspan="6"><img width="80px" src="{{ asset('images/logo_yamaha2.png') }}" alt=""></td>
		</tr>
		<tr>
			<td class="head" colspan="6">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
		</tr>
		<tr>
			<td class="head">Department</td>
			<td class="head">{{ $departments }}</td>
			<td class="head" rowspan="4" colspan="2" style="padding: 15px;"><center><b>{{ $activity_name }}</b></center></td>
			<td class="head" rowspan="4"><center>Checked<br><br><br><br>
				{{ $training->foreman }}<br>Foreman</center></td>
				<td class="head" rowspan="4"><center>Prepared<br><br><br><br>
					{{ $training->leader }}<br>Leader</center></td>
				</tr>
				<tr>
					<td class="head">Section</td>
					<td>{{ $training->section }}</td>
				</tr>
				<tr>
					<td class="head">Product</td>
					<td class="head">{{ $training->product }}</td>
				</tr>
				<tr>
					<td class="head">Proses</td>
					<td class="head">{{ $training->periode }}</td>
				</tr>
				
			</tbody>
		</table>
		<table class="table">
			<tbody class="head">
				<tr>
					<td class="bodytraining" width="10%" style="padding-top:50px">Tanggal</td>
					<td width="15%" style=" text-align: right;padding-top:50px">:</td>
					<td width="50%" style="padding-top:50px">{{ $training->date }}</td>
				</tr>
				<tr>
					<td class="bodytraining" width="10%">Waktu</td>
					<td width="15%" style=" text-align: right">:</td>
					<td width="50%" style=""><?php 
					$timesplit=explode(':',$training->time);
					$min=($timesplit[0]*60)+($timesplit[1])+($timesplit[2]>30?1:0); ?>
				{{$min.' Menit'}}</td>
			</tr>
			<tr>
				<td class="bodytraining" width="10%">Trainer</td>
				<td width="15%" style=" text-align: right">:</td>
				<td width="50%" style="">{{ $training->trainer }}</td>
			</tr>
			<tr>
				<td class="bodytraining" width="10%">Tema</td>
				<td width="15%" style=" text-align: right">:</td>
				<td width="50%" style="">{{ $training->theme }}</td>
			</tr>
			<tr>
				<td class="bodytraining" width="10%">Tujuan</td>
				<td width="15%" style=" text-align: right">:</td>
				<td width="50%" style="">{{ $training->tujuan }}</td>
			</tr>
			<tr>
				<td class="bodytraining" width="10%">Standard</td>
				<td width="15%" style=" text-align: right">:</td>
				<td width="50%" style="">{{ $training->standard }}</td>
			</tr>
			<tr>
				<td class="bodytraining" width="10%">Isi Training</td>
				<td width="15%" style=" text-align: right">:</td>
				<td width="50%" style=""><?php echo $training->isi_training ?></td>
			</tr>
			{{-- <td></td> --}}
			{{-- <td></td> --}}
			<tr>
				<td></td>
			</tr>
			<tr>
				<td class="bodytraining" colspan="6">
					Peserta Training : 
				</td>
			</tr>
			<tr>
				<td></td>
				<td class="head">No.</td>
				<td class="head">Nama Peserta</td>
				<td class="head">Attendance</td>
				<td></td>
			</tr>
			<?php $no = 1 ?>
			@foreach($trainingParticipant as $trainingParticipant)
			<tr>
				<td></td>
				<td class="head">{{ $no }}</td>
				<td class="head">{{ $trainingParticipant->participant_name }}</td>
				<td class="head">{{ $trainingParticipant->participant_absence }}</td>
				<td></td>
			</tr>
			<?php $no++ ?>
			@endforeach
			<tr>
				<td></td>
			</tr>
			<tr>
				<td class="bodytraining" colspan="6">
					Catatan : 
				</td>
			</tr>
			<tr>
				<td class="bodytraining" colspan="6">
					<?php echo  $training->notes ?> 
				</td>
			</tr>
			<tr>
				<td></td>
			</tr>
			<tr>
				<td class="bodytraining" colspan="6">
					Picture : 
				</td>
			</tr>
		</tbody>
	</table>
	<center><div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 head">
		@foreach($trainingPicture as $trainingPicture)
		<img width="250px" src="{{ url('/data_file/training/'.$trainingPicture->picture) }}">
		@endforeach
	</div></center>
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
.table2 {
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
