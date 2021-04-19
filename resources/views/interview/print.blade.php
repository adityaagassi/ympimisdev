
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
  	<?php $file = url("/data_file/segitiga.png"); ?>
    <table style="width: 100%; border-collapse: collapse;" >
			<tbody>
				<tr>
					<td colspan="11" style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;"><img width="80px" src="{{ public_path('images/logo_yamaha2.png') }}" alt=""></td>
				</tr>
				<tr>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;" colspan="11">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;" colspan="2">Department</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;" colspan="3">{{ strtoupper($departments) }}</td>
					<td rowspan="5" colspan="4" style="border: 1px solid black;padding: 15px;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;"><center><b>{{ $activity_name }}</b></center></td>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;" rowspan="5"><center>Checked<br>
						@if($interview->approval != Null)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $interview->approved_date }}</b>
						@endif<br>
					{{ $interview->foreman }}</center></td>
					<td style="border: 1px solid black;vertical-align: middle;padding-top: 0px;padding-bottom: 0px;" rowspan="5"><center>Prepared<br>
						@if($interview->approval_leader != Null)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $interview->approved_date_leader }}</b>
						@endif<br>
						{{ $interview->leader }}</center></td>
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;" colspan="2">Section</td>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;" colspan="3">{{ strtoupper($interview->section) }}</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;" colspan="2">Group</td>
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
							<center><label class="label label-warning"><img width="20px" src="{{ url('/data_file/segitiga.png') }}"></label></center>
						@elseif($detail->filosofi_yamaha == 'Not OK')
							<center><label class="label label-danger">X</label></center>
						@else
							<center><?php $nilai = explode("_",$detail->filosofi_yamaha);
			                		if ($nilai[1] == 100) {
			                			echo '<center><label class="label label-success">O</label></center>';
			                		}else if ($nilai[1] < 100 && $nilai[1] > 0) {
			                			echo '<center><label class="label label-warning"><img width="20px" src="'.$file.'"></label></center>';
			                			$nilaipoint = explode(",", $nilai[0]);
			                			// echo "Point = ".$nilai[0];
			                			$hapal = [];
			                			$tidak_hapal = [];
			                			for($i = 0; $i < count($poinalldiamond) ; $i++) {
			                				if (in_array($poinalldiamond[$i]->point_no,$nilaipoint)) {
			                					array_push($hapal, $poinalldiamond[$i]->point_no);
			                				}else{
			                					array_push($tidak_hapal, $poinalldiamond[$i]->point_no);
			                				}
			                			}
			                			echo "Hapal = ".join(",",$hapal)."<br>";
			                			echo "Tidak Hapal = ".join(",",$tidak_hapal);
			                		}else{
			                			echo '<center><label class="label label-danger">X</label></center>';
			                		}
			                		 ?></center>
						@endif
					</td>
					<td style="border: 1px solid black;font-size: 10px;">
						@if($detail->aturan_k3 == 'OK')
							<center><label class="label label-success">O</label></center>
						@elseif($detail->aturan_k3 == 'OK (Kurang Lancar)')
							<center><label class="label label-warning"><img width="20px" src="{{ url('/data_file/segitiga.png') }}"></label></center>
						@elseif($detail->aturan_k3 == 'Not OK')
							<center><label class="label label-danger">X</label></center>
						@else
							<center><?php $nilai = explode("_",$detail->aturan_k3);
			                		if ($nilai[1] == 100) {
			                			echo '<center><label class="label label-success">O</label></center>';
			                		}else if ($nilai[1] < 100 && $nilai[1] > 0) {
			                			echo '<center><label class="label label-warning"><img width="20px" src="'.$file.'"></label></center>';
			                			$nilaipoint = explode(",", $nilai[0]);
			                			// echo "Point = ".$nilai[0];
			                			$hapal = [];
			                			$tidak_hapal = [];
			                			for($i = 0; $i < count($poinallk3) ; $i++) {
			                				if (in_array($poinallk3[$i]->point_no,$nilaipoint )) {
			                					array_push($hapal, $poinallk3[$i]->point_no);
			                				}else{
			                					array_push($tidak_hapal, $poinallk3[$i]->point_no);
			                				}
			                			}
			                			echo "Point = ".join(",",$hapal)."<br>";
			                			echo "Point = ".join(",",$tidak_hapal);
			                		}else{
			                			echo '<center><label class="label label-danger">X</label></center>';
			                		}
			                		 ?></center>
						@endif
					</td>
					<td style="border: 1px solid black;font-size: 10px;">
						@if($detail->komitmen_berkendara == 'OK')
							<center><label class="label label-success">O</label></center>
						@elseif($detail->komitmen_berkendara == 'OK (Kurang Lancar)')
							<center><label class="label label-warning"><img width="20px" src="{{ url('/data_file/segitiga.png') }}"></label></center>
						@elseif($detail->komitmen_berkendara == 'Not OK')
							<center><label class="label label-danger">X</label></center>
						@else
							<center><?php $nilai = explode("_",$detail->komitmen_berkendara);
			                		if ($nilai[1] == 100) {
			                			echo '<center><label class="label label-success">O</label></center>';
			                		}else if ($nilai[1] < 100 && $nilai[1] > 0) {
			                			echo '<center><label class="label label-warning"><img width="20px" src="'.$file.'"></label></center>';
			                			// echo "Point = ".$nilai[0];
			                			$nilaipoint = explode(",", $nilai[0]);
			                			// echo "Point = ".$nilai[0];
			                			$hapal = [];
			                			$tidak_hapal = [];
			                			for($i = 0; $i < count($poinall10_komitmen) ; $i++) {
			                				if (in_array($poinall10_komitmen[$i]->point_no,$nilaipoint)) {
			                					array_push($hapal, $poinall10_komitmen[$i]->point_no);
			                				}else{
			                					array_push($tidak_hapal, $poinall10_komitmen[$i]->point_no);
			                				}
			                			}
			                			echo "Hapal = ".join(",",$hapal)."<br>";
			                			echo "Tidak Hapal = ".join(",",$tidak_hapal);
			                		}else{
			                			echo '<center><label class="label label-danger">X</label></center>';
			                		}
			                		 ?></center>
						@endif
					</td>
					<td style="border: 1px solid black;font-size: 10px;">
						@if($detail->kebijakan_mutu == 'OK')
							<center><label class="label label-success">O</label></center>
						@elseif($detail->kebijakan_mutu == 'OK (Kurang Lancar)')
							<center><label class="label label-warning"><img width="20px" src="{{ url('/data_file/segitiga.png') }}"></label></center>
						@elseif($detail->kebijakan_mutu == 'Not OK')
							<center><label class="label label-danger">X</label></center>
						@else
							<center><?php $nilai = explode("_",$detail->kebijakan_mutu);
			                		if ($nilai[1] == 100) {
			                			echo '<center><label class="label label-success">O</label></center>';
			                		}else if ($nilai[1] < 100 && $nilai[1] > 0) {
			                			echo '<center><label class="label label-warning"><img width="20px" src="'.$file.'"></label></center>';
			                			$nilaipoint = explode(",", $nilai[0]);
			                			// echo "Point = ".$nilai[0];
			                			$hapal = [];
			                			$tidak_hapal = [];
			                			for($i = 0; $i < count($poinallslogan_mutu) ; $i++) {
			                				if (in_array($poinallslogan_mutu[$i]->point_no,$nilaipoint)) {
			                					array_push($hapal, $poinallslogan_mutu[$i]->point_no);
			                				}else{
			                					array_push($tidak_hapal, $poinallslogan_mutu[$i]->point_no);
			                				}
			                			}
			                			echo "Hapal = ".join(",",$hapal)."<br>";
			                			echo "Tidak Hapal = ".join(",",$tidak_hapal);
			                		}else{
			                			echo '<center><label class="label label-danger">X</label></center>';
			                		}
			                		 ?></center>
						@endif
					</td>
					<td style="border: 1px solid black;font-size: 10px;">
						@if($detail->enam_pasal_keselamatan == 'OK')
							<center><label class="label label-success">O</label></center>
						@elseif($detail->enam_pasal_keselamatan == 'OK (Kurang Lancar)')
							<center><label class="label label-warning"><img width="20px" src="{{ url('/data_file/segitiga.png') }}"></label></center>
						@elseif($detail->enam_pasal_keselamatan == 'Not OK')
							<center><label class="label label-danger">X</label></center>
						@else
							<center><?php $nilai = explode("_",$detail->enam_pasal_keselamatan);
			                		if ($nilai[1] == 100) {
			                			echo '<center><label class="label label-success">O</label></center>';
			                		}else if ($nilai[1] < 100 && $nilai[1] > 0) {
			                			echo '<center><label class="label label-warning"><img width="20px" src="'.$file.'"></label></center>';
			                			// echo "Point = ".$nilai[0];
			                			$nilaipoint = explode(",", $nilai[0]);
			                			// echo "Point = ".$nilai[0];
			                			$hapal = [];
			                			$tidak_hapal = [];
			                			for($i = 0; $i < count($poinall6_pasal) ; $i++) {
			                				if (in_array($poinall6_pasal[$i]->point_no,$nilaipoint)) {
			                					array_push($hapal, $poinall6_pasal[$i]->point_no);
			                				}else{
			                					array_push($tidak_hapal, $poinall6_pasal[$i]->point_no);
			                				}
			                			}
			                			echo "Hapal = ".join(",",$hapal)."<br>";
			                			echo "Tidak Hapal = ".join(",",$tidak_hapal);
			                		}else{
			                			echo '<center><label class="label label-danger">X</label></center>';
			                		}
			                		 ?></center>
						@endif
					</td>
					<td style="border: 1px solid black;font-size: 10px;">
						@if($detail->budaya_kerja == 'OK')
							<center><label class="label label-success">O</label></center>
						@elseif($detail->budaya_kerja == 'OK (Kurang Lancar)')
							<center><label class="label label-warning"><img width="20px" src="{{ url('/data_file/segitiga.png') }}"></label></center>
						@elseif($detail->budaya_kerja == 'Not OK')
							<center><label class="label label-danger">X</label></center>
						@else
							<center><?php $nilai = explode("_",$detail->budaya_kerja);
			                		if ($nilai[1] == 100) {
			                			echo '<center><label class="label label-success">O</label></center>';
			                		}else if ($nilai[1] < 100 && $nilai[1] > 0) {
			                			echo '<center><label class="label label-warning"><img width="20px" src="'.$file.'"></label></center>';
			                			// echo "Point = ".$nilai[0];
			                			$nilaipoint = explode(",", $nilai[0]);
			                			// echo "Point = ".$nilai[0];
			                			$hapal = [];
			                			$tidak_hapal = [];
			                			for($i = 0; $i < count($poinallbudaya) ; $i++) {
			                				if (in_array($poinallbudaya[$i]->point_no,$nilaipoint)) {
			                					array_push($hapal, $poinallbudaya[$i]->point_no);
			                				}else{
			                					array_push($tidak_hapal, $poinallbudaya[$i]->point_no);
			                				}
			                			}
			                			echo "Hapal = ".join(",",$hapal)."<br>";
			                			echo "Tidak Hapal = ".join(",",$tidak_hapal);
			                		}else{
			                			echo '<center><label class="label label-danger">X</label></center>';
			                		}
			                		 ?></center>
						@endif
					</td>
					<td style="border: 1px solid black;font-size: 10px;">
						@if($detail->budaya_5s == 'OK')
							<center><label class="label label-success">O</label></center>
						@elseif($detail->budaya_5s == 'OK (Kurang Lancar)')
							<center><label class="label label-warning"><img width="20px" src="{{ url('/data_file/segitiga.png') }}"></label></center>
						@elseif($detail->budaya_5s == 'Not OK')
							<center><label class="label label-danger">X</label></center>
						@else
							<center><?php $nilai = explode("_",$detail->budaya_5s);
			                		if ($nilai[1] == 100) {
			                			echo '<center><label class="label label-success">O</label></center>';
			                		}else if ($nilai[1] < 100 && $nilai[1] > 0) {
			                			echo '<center><label class="label label-warning"><img width="20px" src="'.$file.'"></label></center>';
			                			// echo "Point = ".$nilai[0];
			                			$nilaipoint = explode(",", $nilai[0]);
			                			// echo "Point = ".$nilai[0];
			                			$hapal = [];
			                			$tidak_hapal = [];
			                			for($i = 0; $i < count($poinall5s) ; $i++) {
			                				if (in_array($poinall5s[$i]->point_no,$nilaipoint)) {
			                					array_push($hapal, $poinall5s[$i]->point_no);
			                				}else{
			                					array_push($tidak_hapal, $poinall5s[$i]->point_no);
			                				}
			                			}
			                			echo "Hapal = ".join(",",$hapal)."<br>";
			                			echo "Tidak Hapal = ".join(",",$tidak_hapal);
			                		}else{
			                			echo '<center><label class="label label-danger">X</label></center>';
			                		}
			                		 ?></center>
						@endif
					</td>
					<td style="border: 1px solid black;font-size: 10px;">
						@if($detail->komitmen_hotel_konsep == 'OK')
							<center><label class="label label-success">O</label></center>
						@elseif($detail->komitmen_hotel_konsep == 'OK (Kurang Lancar)')
							<center><label class="label label-warning"><img width="20px" src="{{ url('/data_file/segitiga.png') }}"></label></center>
						@elseif($detail->komitmen_hotel_konsep == 'Not OK')
							<center><label class="label label-danger">X</label></center>
						@else
							<center><?php $nilai = explode("_",$detail->komitmen_hotel_konsep);
			                		if ($nilai[1] == 100) {
			                			echo '<center><label class="label label-success">O</label></center>';
			                		}else if ($nilai[1] < 100 && $nilai[1] > 0) {
			                			echo '<center><label class="label label-warning"><img width="20px" src="'.$file.'"></label></center>';
			                			// echo "Point = ".$nilai[0];
			                			$nilaipoint = explode(",", $nilai[0]);
			                			// echo "Point = ".$nilai[0];
			                			$hapal = [];
			                			$tidak_hapal = [];
			                			for($i = 0; $i < count($poinallkomitmen) ; $i++) {
			                				if (in_array($poinallkomitmen[$i]->point_no,$nilaipoint)) {
			                					array_push($hapal, $poinallkomitmen[$i]->point_no);
			                				}else{
			                					array_push($tidak_hapal, $poinallkomitmen[$i]->point_no);
			                				}
			                			}
			                			echo "Hapal = ".join(",",$hapal)."<br>";
			                			echo "Tidak Hapal = ".join(",",$tidak_hapal);
			                		}else{
			                			echo '<center><label class="label label-danger">X</label></center>';
			                		}
			                		 ?></center>
						@endif
					</td>
					<td style="border: 1px solid black;font-size: 10px;">
						@if($detail->janji_tindakan_dasar == 'OK')
							<center><label class="label label-success">O</label></center>
						@elseif($detail->janji_tindakan_dasar == 'OK (Kurang Lancar)')
							<center><label class="label label-warning"><img width="20px" src="{{ url('/data_file/segitiga.png') }}"></label></center>
						@elseif($detail->janji_tindakan_dasar == 'Not OK')
							<center><label class="label label-danger">X</label></center>
						@else
							<center><?php $nilai = explode("_",$detail->janji_tindakan_dasar);
			                		if ($nilai[1] == 100) {
			                			echo '<center><label class="label label-success">O</label></center>';
			                		}else if ($nilai[1] < 100 && $nilai[1] > 0) {
			                			echo '<center><label class="label label-warning"><img width="20px" src="'.$file.'"></label></center>';
			                			// echo "Point = ".$nilai[0];
			                			$hapal = [];
			                			$tidak_hapal = [];
			                			for($i = 0; $i < count($poinalljanji) ; $i++) {
			                				if (in_array($poinalljanji[$i]->point_no,$nilaipoint)) {
			                					array_push($hapal, $poinalljanji[$i]->point_no);
			                				}else{
			                					array_push($tidak_hapal, $poinalljanji[$i]->point_no);
			                				}
			                			}
			                			echo "Hapal = ".join(",",$hapal)."<br>";
			                			echo "Tidak Hapal = ".join(",",$tidak_hapal);
			                		}else{
			                			echo '<center><label class="label label-danger">X</label></center>';
			                		}
			                		 ?></center>
						@endif
					</td>
				</tr>
				<?php $no++ ?>
				@endforeach
			</tbody>
		</table>
		<div class="col-xs-12" style="border:1px solid black;">
			<center style="vertical-align: middle;font-size: 15px">Foto Interview</center>
		</div>
		<div class="col-xs-12" style="padding-top: 20px;border:1px solid black">
			@foreach($interviewPicture as $interviewPicture)
				<img width="120px" style="padding-left: 2px" src="{{ url('/data_file/interview/'.$interviewPicture->picture) }}" alt="">
			@endforeach
		</div>
</header>
<main>
</main>
</body>
</html>
