
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
    <table style="width: 100%; border-collapse: collapse;" >
			<tbody>
				<tr>
					<td colspan="12" style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;"><img width="80px" src="{{ public_path('images/logo_yamaha2.png') }}" alt=""></td>
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
						{{ $interview->leader }}<br>Leader</center></td>
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
					<td style="border: 1px solid black;font-weight: bold;"><center>No.</center></td>
					<td style="border: 1px solid black;font-weight: bold;text-align: center;">Nama</td>
					<td style="border: 1px solid black;font-weight: bold; font-size: 10px;"><center>Filosofi Yamaha</center></td>
					<td style="border: 1px solid black;font-weight: bold; font-size: 10px;"><center>Aturan K3 Yamaha</center></td>
					<td style="border: 1px solid black;font-weight: bold; font-size: 10px;"><center>10 Komitmen Berkendara</center></td>
					<td style="border: 1px solid black;font-weight: bold; font-size: 10px;"><center>Kebijakan Mutu</center></td>
					<td style="border: 1px solid black;font-weight: bold; font-size: 10px;"><center>5 Dasar Tindakan Bekerja</center></td>
					<td style="border: 1px solid black;font-weight: bold; font-size: 10px;"><center>6 Pasal Keselamatan Lalu Lintas</center></td>
					<td style="border: 1px solid black;font-weight: bold; font-size: 10px;"><center>Budaya Kerja YMPI</center></td>
					<td style="border: 1px solid black;font-weight: bold; font-size: 10px;"><center>5S</center></td>
					<td style="border: 1px solid black;font-weight: bold; font-size: 10px;"><center>Komitmen Hotel Konsep</center></td>
					<td style="border: 1px solid black;font-weight: bold; font-size: 10px;"><center>Janji Tindakan Dasar</center></td>
				</tr>
				<?php $no = 1; ?>
				@foreach($interviewDetail as $detail)
				<tr>
					<td style="border: 1px solid black;text-align: center;">{{ $no }}</td>
					<td style="border: 1px solid black;">{{ $detail->name }}</td>
					<td style="border: 1px solid black;font-size: 10px;">
						@if($detail->filosofi_yamaha == 'OK')
							<center><label class="label label-success">O</label></center>
						@elseif($detail->filosofi_yamaha == 'OK (Kurang Lancar)')
							<center><label class="label label-warning">X</label></center>
						@else
							<center><label class="label label-danger">-</label></center>
						@endif
					</td>
					<td style="border: 1px solid black;font-size: 10px;">
						@if($detail->aturan_k3 == 'OK')
							<center><label class="label label-success">O</label></center>
						@elseif($detail->aturan_k3 == 'OK (Kurang Lancar)')
							<center><label class="label label-warning">X</label></center>
						@else
							<center><label class="label label-danger">-</label></center>
						@endif
					</td>
					<td style="border: 1px solid black;font-size: 10px;">
						@if($detail->komitmen_berkendara == 'OK')
							<center><label class="label label-success">O</label></center>
						@elseif($detail->komitmen_berkendara == 'OK (Kurang Lancar)')
							<center><label class="label label-warning">X</label></center>
						@else
							<center><label class="label label-danger">-</label></center>
						@endif
					</td>
					<td style="border: 1px solid black;font-size: 10px;">
						@if($detail->kebijakan_mutu == 'OK')
							<center><label class="label label-success">O</label></center>
						@elseif($detail->kebijakan_mutu == 'OK (Kurang Lancar)')
							<center><label class="label label-warning">X</label></center>
						@else
							<center><label class="label label-danger">-</label></center>
						@endif
					</td>
					<td style="border: 1px solid black;font-size: 10px;">
						@if($detail->dasar_tindakan_bekerja == 'OK')
							<center><label class="label label-success">O</label></center>
						@elseif($detail->dasar_tindakan_bekerja == 'OK (Kurang Lancar)')
							<center><label class="label label-warning">X</label></center>
						@else
							<center><label class="label label-danger">-</label></center>
						@endif
					</td>
					<td style="border: 1px solid black;font-size: 10px;">
						@if($detail->enam_pasal_keselamatan == 'OK')
							<center><label class="label label-success">O</label></center>
						@elseif($detail->enam_pasal_keselamatan == 'OK (Kurang Lancar)')
							<center><label class="label label-warning">X</label></center>
						@else
							<center><label class="label label-danger">-</label></center>
						@endif
					</td>
					<td style="border: 1px solid black;font-size: 10px;">
						@if($detail->budaya_kerja == 'OK')
							<center><label class="label label-success">O</label></center>
						@elseif($detail->budaya_kerja == 'OK (Kurang Lancar)')
							<center><label class="label label-warning">X</label></center>
						@else
							<center><label class="label label-danger">-</label></center>
						@endif
					</td>
					<td style="border: 1px solid black;font-size: 10px;">
						@if($detail->budaya_5s == 'OK')
							<center><label class="label label-success">O</label></center>
						@elseif($detail->budaya_5s == 'OK (Kurang Lancar)')
							<center><label class="label label-warning">X</label></center>
						@else
							<center><label class="label label-danger">-</label></center>
						@endif
					</td>
					<td style="border: 1px solid black;font-size: 10px;">
						@if($detail->komitmen_hotel_konsep == 'OK')
							<center><label class="label label-success">O</label></center>
						@elseif($detail->komitmen_hotel_konsep == 'OK (Kurang Lancar)')
							<center><label class="label label-warning">X</label></center>
						@else
							<center><label class="label label-danger">-</label></center>
						@endif
					</td>
					<td style="border: 1px solid black;font-size: 10px;">
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
					<td colspan="12" style="padding: 10px 10px">Foto :</td>
				</tr>
				<tr>
					<td colspan="12">
						<div class="col-xs-12" style="padding: 10px 10px 10px 10px">
							@foreach($interviewPicture as $interviewPicture)
								<div class="col-xs-4">
									<img width="150px" src="{{ url('/data_file/interview/'.$interviewPicture->picture) }}" alt="">
								</div>
							@endforeach
						</div>
					</td>
				</tr>
				<!-- <tr>
					<td colspan="2" style="border-top: 1px solid black">Keterangan :</td>
					<td style="vertical-align: middle;border-top: 1px solid black"><center><label class="label label-success">OK</label></center></td>
					<td style="vertical-align: middle;border-top: 1px solid black"><center><label class="label label-warning">OK (Kurang Lancar)</label></center></td>
					<td style="vertical-align: middle;border-top: 1px solid black"><center><label class="label label-danger">Not OK</label></center></td>
					<td colspan="8" style="border-top: 1px solid black"></td>
				</tr> -->
			</tbody>
		</table>
</header>
<main>
</main>
</body>
</html>
