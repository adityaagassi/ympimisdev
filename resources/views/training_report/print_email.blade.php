@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Approval {{ $activity_name }} - {{ $leader }}
    <a class="btn btn-info pull-right" href="{{url('index/training_report/print/'.$id)}}">Cetak / Save PDF</a>
    <!-- <button class="btn btn-primary pull-right" onclick="myFunction()">Print / Save PDF</button> -->
  </h1>
  <ol class="breadcrumb">
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
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-solid">
      <div class="box-body">
		<table class="table" style="border: 1px solid black;">
			<tbody style="font-size: 10px">
				<tr>
					<td colspan="6" style="border: 1px solid black;"><img width="80px" src="{{ asset('images/logo_yamaha2.png') }}" alt=""></td>
				</tr>
				<tr>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px" colspan="6">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px">Department</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px">{{ strtoupper($departments) }}</td>
					<td rowspan="3" colspan="2" style="border: 1px solid black;vertical-align: middle;font-size: 15px"><center><b>{{ $activity_name }}</b></center></td>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px" rowspan="3"><center>Checked<br>
						@if($training->approval != Null)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $training->approved_date }}</b>
						@endif<br>
					{{ $training->foreman }}<br>Foreman</center></td>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px" rowspan="3"><center>Prepared<br>
						@if($training->approval_leader != Null)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $training->approved_date_leader }}</b>
						@endif<br>
						{{ $training->leader }}<br>Leader</center></td>
					@if($training->approval == Null && $role_code != 'M')
					<td rowspan="5" id="approval1" style="border: 1px solid black;vertical-align: middle;"><center>Approval</center></td>
					@endif
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px">Section</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px">{{ strtoupper($training->section) }}</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px">Product</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px">{{ $training->product }}</td>
				</tr>
			</tbody>
		</table>
		<table class="table" id="table2">
			<tbody style="font-size: 10px">
				<form role="form" method="post" action="{{url('index/training_report/approval/'.$id)}}">
				<tr>
					<td style="border-top: 1px solid black;padding-top: 20px;padding-bottom: 0px">Tanggal</td>
					<td style="border-top: 1px solid black;text-align: right;padding-top: 20px;padding-bottom: 0px">:</td>
					<td style="border-top: 1px solid black;padding-top: 20px;padding-bottom: 0px">{{ $training->date }}</td>
					<td id="approval2" style="border-top: 1px solid black"></td>
					@if($training->approval == Null && $role_code != 'M')
						<td id="approval2" style="border-top: 1px solid black;padding-top: 20px;padding-bottom: 0px">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<label class="label label-success"><input type="checkbox" class="minimal-red" name="approve[]" value="1">
							    Approve</label>
						</td>
					@endif
				</tr>
				<tr>
					<td class="bodytraining" width="10%" style="padding-top: 0px;padding-bottom: 0px">Waktu</td>
					<td width="15%" style=" text-align: right;padding-top: 0px;padding-bottom: 0px">:</td>
					<td width="50%" style="padding-top: 0px;padding-bottom: 0px"><?php 
					$timesplit=explode(':',$training->time);
					$min=($timesplit[0]*60)+($timesplit[1])+($timesplit[2]>30?1:0); ?>
				{{$min.' Menit'}}</td>
					<td id="approval2" style="padding-top: 0px;padding-bottom: 0px"></td>
					@if($training->approval == Null && $role_code != 'M')
						<td id="approval2" style="padding-top: 0px;padding-bottom: 0px">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<label class="label label-success"><input type="checkbox" class="minimal-red" name="approve[]" value="2">
							    Approve</label>
						</td>
					@endif
				</tr>
				<tr>
					<td class="bodytraining" width="10%" style="padding-top: 0px;padding-bottom: 0px">Trainer</td>
					<td width="15%" style=" text-align: right;padding-top: 0px;padding-bottom: 0px">:</td>
					<td width="50%" style="padding-top: 0px;padding-bottom: 0px">{{ $training->trainer }}</td>
					<td id="approval2" style="padding-top: 0px;padding-bottom: 0px"></td>
					@if($training->approval == Null && $role_code != 'M')
						<td id="approval2" style="padding-top: 0px;padding-bottom: 0px">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<label class="label label-success"><input type="checkbox" class="minimal-red" name="approve[]" value="3">
							    Approve</label>
						</td>
					@endif
				</tr>
				<tr>
					<td class="bodytraining" width="10%" style="padding-top: 0px;padding-bottom: 0px">Tema</td>
					<td width="15%" style=" text-align: right;padding-top: 0px;padding-bottom: 0px">:</td>
					<td width="50%" style="padding-top: 0px;padding-bottom: 0px">{{ $training->theme }}</td>
					<td id="approval2" style="padding-top: 0px;padding-bottom: 0px"></td>
					@if($training->approval == Null && $role_code != 'M')
						<td id="approval2" style="padding-top: 0px;padding-bottom: 0px">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<label class="label label-success"><input type="checkbox" class="minimal-red" name="approve[]" value="4">
							    Approve</label>
						</td>
					@endif
				</tr>
				<tr>
					<td class="bodytraining" width="10%" style="padding-top: 0px;padding-bottom: 0px">Tujuan</td>
					<td width="15%" style=" text-align: right;padding-top: 0px;padding-bottom: 0px">:</td>
					<td width="50%" style="padding-top: 0px;padding-bottom: 0px">{{ $training->tujuan }}</td>
					<td id="approval2" style="padding-top: 0px;padding-bottom: 0px"></td>
					@if($training->approval == Null && $role_code != 'M')
						<td id="approval2" style="padding-top: 0px;padding-bottom: 0px">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<label class="label label-success"><input type="checkbox" class="minimal-red" name="approve[]" value="5">
							    Approve</label>
						</td>
					@endif
				</tr>
				<tr>
					<td class="bodytraining" width="10%" style="padding-top: 0px;padding-bottom: 0px">Standard</td>
					<td width="15%" style=" text-align: right;padding-top: 0px;padding-bottom: 0px">:</td>
					<td width="50%" style="padding-top: 0px;padding-bottom: 0px">{{ $training->standard }}</td>
					<td id="approval2" style="padding-top: 0px;padding-bottom: 0px"></td>
					@if($training->approval == Null && $role_code != 'M')
						<td id="approval2" style="padding-top: 0px;padding-bottom: 0px">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<label class="label label-success"><input type="checkbox" class="minimal-red" name="approve[]" value="6">
							    Approve</label>
						</td>
					@endif
				</tr>
				<tr>
					<td class="bodytraining" width="10%" style="padding-top: 0px;padding-bottom: 0px">Isi Training</td>
					<td width="15%" style=" text-align: right;padding-top: 0px;padding-bottom: 0px">:</td>
					<td width="50%" style="padding-top: 0px;padding-bottom: 0px"><?php echo $training->isi_training ?></td>
					<td id="approval2" style="padding-top: 0px;padding-bottom: 0px"></td>
					@if($training->approval == Null && $role_code != 'M')
						<td id="approval2" style="padding-top: 0px;padding-bottom: 0px">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<label class="label label-success"><input type="checkbox" class="minimal-red" name="approve[]" value="7">
							    Approve</label>
						</td>
					@endif
				</tr>
				<tr>
					<td class="bodytraining" colspan="4">
						Peserta Training : 
					</td>
					@if($training->approval == Null && $role_code != 'M')
						<td id="approval2">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<label class="label label-success"><input type="checkbox" class="minimal-red" name="approve[]" value="8">
							    Approve</label>
						</td>
					@endif
				</tr>
				<tr>
					<td style="padding-top: 0px;padding-bottom: 0px;font-weight: bold;text-align: center;"></td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;font-weight: bold;text-align: center;">No.</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;font-weight: bold;text-align: center;">Nama Peserta</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;font-weight: bold;text-align: center;">Kehadiran</td>
					<td style="padding-top: 0px;padding-bottom: 0px;font-weight: bold;text-align: center;"></td>
				</tr>
				<?php $no = 1 ?>
				@foreach($trainingParticipant as $trainingParticipant)
				<tr>
					<td style="padding-top: 0px;padding-bottom: 0px"></td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px">{{ $no }}</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px">{{ $trainingParticipant->name }}</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px">{{ $trainingParticipant->participant_absence }}</td>
					<td style="padding-top: 0px;padding-bottom: 0px"></td>
				</tr>
				<?php $no++ ?>
				@endforeach
				<tr>
					<td class="bodytraining" colspan="6">
						Catatan : 
					</td>
				</tr>
				<tr>
					<td class="bodytraining" colspan="4">
						<?php echo  $training->notes ?> 
					</td>
					@if($training->approval == Null && $role_code != 'M')
						<td id="approval2">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<label class="label label-success"><input type="checkbox" class="minimal-red" name="approve[]" value="9">
							    Approve</label>
						</td>
					@endif
				</tr>
				@if($training->approval == Null && $role_code != 'M')
				<tr id="approval3">
					<td align="right" colspan="5"><button class="btn btn-success" type="submit">Approve</button></td>
				</tr>
				@endif
				</form>
			</tbody>
		</table>
		<center>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="border: 1px solid black;vertical-align: middle;">
				@foreach($trainingPicture as $trainingPicture)
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
					<img width="200px" src="{{ url('/data_file/training/'.$trainingPicture->picture) }}">
				</div>
				@endforeach
			</div>
		</center>
	</div>
  </div>
</section>
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
