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
  <div class="box box-primary">
    {{-- <div class="box-header with-border">
      <h3 class="box-title">Detail User</h3>
    </div>   --}}
      <div class="box-body">
		<table class="table" style="border: 1px solid black;">
			<tbody>
				<tr>
					<td colspan="6" style="border: 1px solid black;"><img width="80px" src="{{ asset('images/logo_yamaha2.png') }}" alt=""></td>
				</tr>
				<tr>
					<td style="border: 1px solid black;vertical-align: middle;" colspan="6">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;">Department</td>
					<td style="border: 1px solid black;">{{ $departments }}</td>
					<td rowspan="4" colspan="2" style="border: 1px solid black;padding: 15px;vertical-align: middle;"><center><b>{{ $activity_name }}</b></center></td>
					<td style="border: 1px solid black;vertical-align: middle;" rowspan="4"><center>Checked<br><br>
						@if($training->approval != Null)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $training->approved_date }}</b>
						@endif<br><br>
					{{ $training->foreman }}<br>Foreman</center></td>
					<td style="border: 1px solid black;vertical-align: middle;" rowspan="4"><center>Prepared<br><br>
						@if($training->approval_leader != Null)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $training->approved_date_leader }}</b>
						@endif<br><br>
						{{ $training->leader }}<br>Leader</center></td>
					@if($training->approval == Null && $role_code != 'M')
					<td rowspan="5" id="approval1" style="border: 1px solid black;vertical-align: middle;"><center>Approval</center></td>
					@endif
				</tr>
				<tr>
					<td style="border: 1px solid black;">Section</td>
					<td style="border: 1px solid black;">{{ $training->section }}</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;">Product</td>
					<td style="border: 1px solid black;">{{ $training->product }}</td>
				</tr>
				<tr style="border: 1px solid black;">
					<td style="border: 1px solid black;">Proses</td>
					<td style="border: 1px solid black;">{{ $training->periode }}</td>
				</tr>
			</tbody>
		</table>
		<table class="table" id="table2">
			<tbody>
				<form role="form" method="post" action="{{url('index/training_report/approval/'.$id)}}">
				<tr>
					<td style="border-top: 1px solid black;padding-top:50px">Tanggal</td>
					<td style="border-top: 1px solid black;text-align: right;padding-top:50px">:</td>
					<td style="border-top: 1px solid black;padding-top:50px">{{ $training->date }}</td>
					<td id="approval2" style="border-top: 1px solid black"></td>
					@if($training->approval == Null && $role_code != 'M')
						<td id="approval2" style="border-top: 1px solid black;padding-top:50px">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<label class="label label-success"><input type="checkbox" class="minimal-red" name="approve[]" value="1">
							    Approve</label>
						</td>
					@endif
				</tr>
				<tr>
					<td class="bodytraining" width="10%">Waktu</td>
					<td width="15%" style=" text-align: right">:</td>
					<td width="50%" style=""><?php 
					$timesplit=explode(':',$training->time);
					$min=($timesplit[0]*60)+($timesplit[1])+($timesplit[2]>30?1:0); ?>
				{{$min.' Menit'}}</td>
					<td id="approval2"></td>
					@if($training->approval == Null && $role_code != 'M')
						<td id="approval2">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<label class="label label-success"><input type="checkbox" class="minimal-red" name="approve[]" value="2">
							    Approve</label>
						</td>
					@endif
				</tr>
				<tr>
					<td class="bodytraining" width="10%">Trainer</td>
					<td width="15%" style=" text-align: right">:</td>
					<td width="50%" style="">{{ $training->trainer }}</td>
					<td id="approval2"></td>
					@if($training->approval == Null && $role_code != 'M')
						<td id="approval2">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<label class="label label-success"><input type="checkbox" class="minimal-red" name="approve[]" value="3">
							    Approve</label>
						</td>
					@endif
				</tr>
				<tr>
					<td class="bodytraining" width="10%">Tema</td>
					<td width="15%" style=" text-align: right">:</td>
					<td width="50%" style="">{{ $training->theme }}</td>
					<td id="approval2"></td>
					@if($training->approval == Null && $role_code != 'M')
						<td id="approval2">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<label class="label label-success"><input type="checkbox" class="minimal-red" name="approve[]" value="4">
							    Approve</label>
						</td>
					@endif
				</tr>
				<tr>
					<td class="bodytraining" width="10%">Tujuan</td>
					<td width="15%" style=" text-align: right">:</td>
					<td width="50%" style="">{{ $training->tujuan }}</td>
					<td id="approval2"></td>
					@if($training->approval == Null && $role_code != 'M')
						<td id="approval2">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<label class="label label-success"><input type="checkbox" class="minimal-red" name="approve[]" value="5">
							    Approve</label>
						</td>
					@endif
				</tr>
				<tr>
					<td class="bodytraining" width="10%">Standard</td>
					<td width="15%" style=" text-align: right">:</td>
					<td width="50%" style="">{{ $training->standard }}</td>
					<td id="approval2"></td>
					@if($training->approval == Null && $role_code != 'M')
						<td id="approval2">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<label class="label label-success"><input type="checkbox" class="minimal-red" name="approve[]" value="6">
							    Approve</label>
						</td>
					@endif
				</tr>
				<tr>
					<td class="bodytraining" width="10%">Isi Training</td>
					<td width="15%" style=" text-align: right">:</td>
					<td width="50%" style=""><?php echo $training->isi_training ?></td>
					<td id="approval2"></td>
					@if($training->approval == Null && $role_code != 'M')
						<td id="approval2">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<label class="label label-success"><input type="checkbox" class="minimal-red" name="approve[]" value="7">
							    Approve</label>
						</td>
					@endif
				</tr>
				<tr>
					<td></td>
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
					<td></td>
					<td style="border: 1px solid black;">No.</td>
					<td style="border: 1px solid black;">Nama Peserta</td>
					<td style="border: 1px solid black;">Attendance</td>
					<td></td>
				</tr>
				<?php $no = 1 ?>
				@foreach($trainingParticipant as $trainingParticipant)
				<tr>
					<td></td>
					<td style="border: 1px solid black;">{{ $no }}</td>
					<td style="border: 1px solid black;">{{ $trainingParticipant->name }}</td>
					<td style="border: 1px solid black;">{{ $trainingParticipant->participant_absence }}</td>
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
				<tr>
					<td></td>
				</tr>
				<tr>
					<td class="bodytraining" colspan="4">
						Picture : 
					</td>
					@if($training->approval == Null && $role_code != 'M')
						<td id="approval2">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<label class="label label-success"><input type="checkbox" class="minimal-red" name="approve[]" value="10">
							    Approve</label>
						</td>
					@endif
				</tr>
				@if($training->approval == Null && $role_code != 'M')
				<tr id="approval3">
					<td align="right" colspan="5"><button class="btn btn-success" type="submit">Submit</button></td>
				</tr>
				@endif
				</form>
			</tbody>
		</table>
		<center>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="border: 1px solid black;vertical-align: middle;">
				@foreach($trainingPicture as $trainingPicture)
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
					<img width="250px" src="{{ url('/data_file/training/'.$trainingPicture->picture) }}">
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
