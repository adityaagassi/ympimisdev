<title>YMPI 情報システム</title>
<link rel="shortcut icon" type="image/x-icon" href="{{ url("logo_mirai.png")}}" />
<style>
.table{
	width:100%;
}
table, th, td {
  border-collapse: collapse;
  font-family:"Arial";
  padding: 5px;
}
.head {
	border: 1px solid black;
}
.peserta {
	border: 1px solid black;
	width:50%;
	text-align:center;
}
.bodytraining{
	padding-left:100px;
}
p {
  display: block;
  margin-top: 0;
  margin-bottom: 0;
  margin-left: 0;
  margin-right: 0;
}
@media print {
	body {-webkit-print-color-adjust: exact;}
}
</style>
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
		<tr>
			<td class="bodytraining" colspan="6">
				Picture : 
			</td>
		</tr>
		<tr>
			<td></td>
		@foreach($trainingPicture as $trainingPicture)
			<td class="head" width="100px"><img width="100px" src="{{ url('/data_file/training/'.$trainingPicture->picture) }}"></td>
		@endforeach
			<td></td>
		</tr>
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
			<td></td>
		</tr>
		<?php $no = 1 ?>
		@foreach($trainingParticipant as $trainingParticipant)
			<tr>
				<td></td>
				<td class="head">{{ $no }}</td>
				<td class="head">{{ $trainingParticipant->participant_name }}</td>
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
	</tbody>
</table>
<script>
    setTimeout(function () { window.print(); }, 200);
</script>
