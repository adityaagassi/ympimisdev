@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Approval {{ $activity_name }} - {{ $leader }}
    <a class="btn btn-info pull-right" href="{{url('index/interview/print_interview/'.$interview_id)}}">Cetak / Save PDF</a>
    <!-- <button class="btn btn-primary pull-right" onclick="myFunction()">Print</button> -->
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
			<tbody>
				<tr>
					<td colspan="12" style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;"><img width="80px" src="{{ asset('images/logo_yamaha2.png') }}" alt=""></td>
				</tr>
				<tr>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;" colspan="12">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;" colspan="2">Department</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;" colspan="3">{{ strtoupper($departments) }}</td>
					<td rowspan="5" colspan="5" style="border: 1px solid black;padding: 15px;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;"><center><b>{{ $activity_name }}</b></center></td>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;" rowspan="5"><center>Checked<br>
						@if($interview->approval != Null)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $interview->approved_date }}</b>
						@endif<br>
					{{ $interview->foreman }}<br>Foreman</center></td>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;" rowspan="5"><center>Prepared<br>
						@if($interview->approval_leader != Null)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $interview->approved_date_leader }}</b>
						@endif<br>
						{{ $interview->leader }}<br>Leader</center>
					</td>
					@if($interview->approval == Null && $role_code != 'M')
						<td rowspan="6" id="approval1" style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;"><center>Approval</center></td>
					@endif
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;" colspan="2">Section</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;" colspan="3">{{ strtoupper($interview->section) }}</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;" colspan="2">Sub Section</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;" colspan="3">{{ strtoupper($interview->subsection) }}</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;" colspan="2">Periode</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;" colspan="3">{{ $interview->periode }}</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;" colspan="2">Date</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;" colspan="3">{{ $interview->date }}</td>
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
				<form role="form" method="post" action="{{url('index/interview/approval/'.$interview_id)}}">
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
					@if($interview->approval == Null && $role_code != 'M')
						<td id="approval2" style="border: 1px solid black;">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<label class="label label-success"><input type="checkbox" class="minimal-red" name="approve[]">
							    Approve</label>
						</td>
					@endif
				</tr>
				<?php $no++ ?>
				@endforeach
				<tr>
					<td colspan="6">Foto :</td>
				</tr>
				<tr>
					<td colspan="6">
				@foreach($interviewPicture as $interviewPicture)
					@if($interviewPicture->extension == 'jpg' || $interviewPicture->extension == 'png' || $interviewPicture->extension == 'jpeg' || $interviewPicture->extension == 'JPG')
                		<a target="_blank" href="{{ url('/data_file/interview/'.$interviewPicture->picture) }}" class="btn"><img width="200px" src="{{ url('/data_file/interview/'.$interviewPicture->picture) }}"></a>
                	@else
                		<a target="_blank" href="{{ url('/data_file/interview/'.$interviewPicture->picture) }}" class="btn"><img width="100px" src="{{ url('/images/file.png') }}"></a>
                	@endif
					<!-- <img width="200px" src="{{ url('/data_file/interview/'.$interviewPicture->picture) }}" alt=""> -->
				@endforeach
					</td>
				</tr>
				<tr>
					<td colspan="2" style="border-top: 1px solid black">Keterangan :</td>
					<td style="vertical-align: middle;border-top: 1px solid black"><center><label class="label label-success">OK</label></center></td>
					<td style="vertical-align: middle;border-top: 1px solid black"><center><label class="label label-warning">OK (Kurang Lancar)</label></center></td>
					<td style="vertical-align: middle;border-top: 1px solid black"><center><label class="label label-danger">Not OK</label></center></td>
					@if($interview->approval == Null && $role_code != 'M')
					<td align="right" colspan="8" style="border-top: 1px solid black"><button class="btn btn-success" type="submit">Approve</button></td>
					@endif
				</tr>
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
