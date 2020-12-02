
<!DOCTYPE html>
<html>
<head>
  <link rel="shortcut icon" type="image/x-icon" href="{{ url("logo_mirai.png")}}" />
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="width=device-width, user-scalable=yes, initial-scale=1.0" name="viewport">
  <title>YMPI 情報システム</title>
  <style type="text/css">
    body{
      font-size: 10px;
      font-family: Calibri, sans-serif; 
    }

    #isi > thead > tr > td {
      text-align: center;
    }

    #isi > tbody > tr > td {
/*      text-align: left;
padding-left: 5px;*/
text-align: center
}

table {
  border: 1px solid black;
  border-collapse: collapse;
  padding: 5px;
}
.table2 {
  border: 1px solid black;
  border-collapse: collapse;
  padding: 5px;
}

@page { }
.footer { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 200px;text-align: center;}
.footer .pagenum:before { content: counter(page); }
</style>
</head>
<body>
  <header>
    <table style="width: 100%; border-collapse: collapse; text-align: left;" >
		<tbody style="font-size: 10px">
			<tr>
				<td colspan="5" style="border: 1px solid black" style=""><img width="80px" src="{{ asset('images/logo_yamaha2.png') }}" alt=""></td>
			</tr>
			<tr>
				<td colspan="5" style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px" >PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
			</tr>
			<tr>
				<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px" width="1%">Department</td>
				<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px">{{ strtoupper($departments) }}</td>
				<td rowspan="3"  style="border: 1px solid black;vertical-align: middle;font-size: 15px;padding-top: 0px;padding-bottom: 0px"><center><b>{{ $activity_name }}</b></center></td>
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
			</tr>
			<tr>
				<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px" width="1%">Section</td>
				<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px">{{ strtoupper($training->section) }}</td>
			</tr>
			<tr>
				<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px" width="1%">Product</td>
				<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px">{{ $training->product }}</td>
			</tr>
			<tr>
				<td colspan="5" style="padding-top: 20px;padding-bottom: 0px;">
					<table style="border: 0px">
						<tr>
							<td style="vertical-align: top">Tanggal</td>
							<td style="padding-left: 25px">:</td>
							<td>{{ $training->date }}</td>
						</tr>
						<tr>
							<td>Waktu</td>
							<td style="padding-left: 25px">:</td>
							<td> <?php $timesplit=explode(':',$training->time);
						$min=($timesplit[0]*60)+($timesplit[1])+($timesplit[2]>30?1:0); ?>
					{{$min.' Menit'}}</td>
						</tr>
						<tr>
							<td>Trainer</td>
							<td style="padding-left: 25px">:</td>
							<td>{{ $training->trainer }}</td>
						</tr>
						<tr>
							
							<td>Tema</td>
							<td style="padding-left: 25px">:</td>
							<td>{{ $training->theme }}</td>
						</tr>
						<tr>
							
							<td>Tujuan</td>
							<td style="padding-left: 25px">:</td>
							<td>{{ $training->tujuan }}</td>
						</tr>
						<tr>
							
							<td>Standard</td>
							<td style="padding-left: 25px">:</td>
							<td>{{ $training->standard }}</td>
						</tr>
						<tr>
							
							<td style="vertical-align: top">Isi Training</td>
							<td style="vertical-align: top;padding-left: 25px">:</td>
							<td><?php echo $training->isi_training ?></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="5" style="padding-left: 10px">Peserta Training : </td>
			</tr>
			<tr>
				<td colspan="5">
					<table style="width: 100%; border-collapse: collapse; text-align: center;padding-left: 80px;padding-right: 80px" >
						<tr>
							<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;font-weight: bold;text-align: center;" width="1%">No.</td>
							<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;font-weight: bold;text-align: center;">Nama Peserta</td>
							<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;font-weight: bold;text-align: center;">Kehadiran</td>
						</tr>
						<?php $no = 1 ?>
						@foreach($trainingParticipant as $trainingParticipant)
						<tr>
							<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;" width="1%">{{ $no }}</td>
							<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">{{ $trainingParticipant->name }}</td>
							<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;">{{ $trainingParticipant->participant_absence }}</td>
						</tr>
						<?php $no++ ?>
						@endforeach
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="5" style="padding-left: 10px">
					Catatan : 
				</td>
			</tr>
			<tr>
				<td colspan="5" style="padding-left: 10px;padding-right: 10px">
					<?php echo  $training->notes ?> 
				</td>
			</tr>
			<tr>
				<td colspan="5" style="padding-right: 10px;padding-left: 10px">
					Foto Training : 
				</td>
			</tr>
			<tr>
				<td colspan="5" style="padding-right: 10px;padding-left: 10px">
					<div class="col-xs-12" style="vertical-align: middle;">
			@foreach($trainingPicture as $trainingPicture)
			<div class="col-xs-3">
				<img width="150px" src="{{ url('/data_file/training/'.$trainingPicture->picture) }}" style="display: inline-block;">
			</div>
			@endforeach
		</div>
				</td>
			</tr>
		</tbody>
	</table>
	<center>
		
	</center>
</header>
<main>
</main>
</body>
</html>
