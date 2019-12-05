<html>
<head>
	<title>YMPI 情報システム</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="shortcut icon" type="image/x-icon" href="{{ public_path() . '/logo_mirai.png' }}" />
	<link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
</head>
<body>
	<style type="text/css">
		table tr td,
		table tr th{
			font-size: 7pt;
			border: 1px solid black !important;
			border-collapse: collapse;
		}

		.centera{
			text-align: center;
			vertical-align: middle !important;
		}

	</style>
	
 	
	<table class="table table-bordered">
		<thead>
			<tr>
				<td colspan="2" class="centera">
					<img width="100px" src="{{ public_path() . '/waves.jpg' }}" alt="">

				</td>
				<td colspan="5" style="text-align: center; vertical-align: middle;font-size: 14px;font-weight: bold">CORRECTIVE & PREVENTIVE ACTION REQUEST</td>
				<td colspan="2" style="font-size: 9px;">
					No Dokumen : YMPI/QA/FM/988 <br>
					Revisi : 01<br>
					Tanggal : 08 Oktober 2019<br>
				</td>
			</tr>
		</thead>
		<tbody>
			<?php $i=1;
			$jumlahparts = count($parts);

			if($jumlahparts < 2)
				$jumlah = 0;
			else
				$jumlah = 2;
			?>

			@foreach($cpars as $cpar)
			<tr>
				<td rowspan="{{ 12 + $jumlah }}">{{ $i++ }}</td>
				<td colspan="4" style="border: none !important">To : <b>{{$cpar->name}}</b></td>
				<td colspan="4" style="border: none !important; border-right: 1px solid black !important;">CPAR No : <b>{{$cpar->cpar_no}}</b></td>
			</tr>
			<tr>
				<td colspan="4" style="border: none !important">Location : <b>{{$cpar->lokasi}}</b></td>
				<td colspan="4" style="border: none !important; border-right: 1px solid black !important;">Source Of Complaint : <b>{{$cpar->sumber_komplain}}</b></td>
			</tr>
			<tr>
				<td colspan="4" style="border: none !important">Request Date : <b>{{$cpar->tgl_permintaan}}</b></td>
				<td colspan="4" style="border: none !important; border-right: 1px solid black !important;">Department : <b>{{$cpar->department_name}}</b></td>
			</tr>
			<tr>
				<td colspan="4" style="border: none !important">Request Due Date: <b>{{$cpar->tgl_balas}}</b></td>
				<td colspan="4" style="border: none !important; border-right: 1px solid black !important;">
					@if($cpar->destination_code != null)
						Customer : <b>{{$cpar->destination_name}}</b>
					@elseif($cpar->vendor != null)
						Vendor : <b>{{$cpar->vendorname}}</b>
					@endif
				</td>
			</tr>
			<tr>
				<td>Part Item</td>
				<td colspan="2">Part Description</td>
				<td>Invoice No</td>
				<td>Sample Qty</td>
				<td>Defect Qty</td>
				<td>% Defect</td>
				<td></td>
			</tr>

			<?php 
			$jumlahparts = count($parts);
			if($jumlahparts != 0) { 

			?>
			@foreach($parts as $part)
			<tr>
				<td rowspan="2">{{$part->part_item}}</td>
				<td rowspan="2" colspan="2">{{$part->material_description}}</td>
				<td rowspan="2">{{$part->no_invoice}}</td>
				<td rowspan="2">{{$part->sample_qty}}</td>
				<td rowspan="2">{{$part->defect_qty}}</td>
				<td rowspan="2">{{$part->defect_presentase}}</td>
				<td rowspan="2"></td>
			</tr>
			<tr></tr>
			@endforeach
			<?php }
			else { 
			?>
			<tr>
				<td rowspan="2">&nbsp;</td>
				<td rowspan="2" colspan="2"></td>
				<td rowspan="2"></td>
				<td rowspan="2"></td>
				<td rowspan="2"></td>
				<td rowspan="2"></td>
				<td rowspan="2"></td>
			</tr>
			<tr></tr>
			<?php } ?>
			<?php if($jumlahparts != 0) { ?> 
			<tr>
				<td colspan="8"><p style="font-size: 14px">Detail Problem : </p><?= $part->detail_problem ?></td>
			</tr>
			<?php } else { ?>
			<tr>
				<td colspan="8">&nbsp;</td>
			</tr>	
			<?php } ?>
			<!-- <tr><td colspan="8"></td></tr> -->
			<tr>
				<td>Prepared By</td>
				<td>Checked By</td>
				<td>Checked By</td>
				<td>Approved By</td>
				<td>Approved By</td>
				<td>Received By</td>
				<td colspan="2" rowspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td rowspan="2" style="vertical-align: middle;">
					@if($cpar->staff != null)
						{{$cpar->staffname}}
					@elseif($cpar->leader != null)
						{{$cpar->leadername}}
					@else
						&nbsp;
					@endif
				</td>
				<td rowspan="2" style="vertical-align: middle;">
					@if($cpar->checked_chief == "Checked")
						{{$cpar->chiefname}}
					@elseif($cpar->checked_foreman == "Checked")
						{{$cpar->foremanname}}
					@else
						&nbsp;
					@endif
				</td>
				<td rowspan="2" style="vertical-align: middle;">
					@if($cpar->checked_manager == "Checked")
						{{$cpar->managername}}
					@else
						&nbsp;
					@endif
				</td>
				<td rowspan="2" style="vertical-align: middle;">
					@if($cpar->approved_dgm == "Checked")
						{{$cpar->dgmname}}
					@else
						&nbsp;
					@endif
				</td>
				<td rowspan="2" style="vertical-align: middle;">
					@if($cpar->approved_gm == "Checked")
						{{$cpar->gmname}}
					@else
						&nbsp;
					@endif
				</td>
				<td rowspan="2" style="vertical-align: middle;">
					@if($cpar->received_manager == "Received")
						{{$cpar->name}}
					@else
						&nbsp;
					@endif
				</td>
				<!-- <td colspan="2" rowspan="2" style="vertical-align: middle;">&nbsp;</td> -->
					
				</td>
			</tr>
			<tr></tr>
			<tr>
				@if($cpar->kategori == "Internal")
				<td>Leader</td>
				<td>Foreman</td>
				@else
				<td>Staff</td>
				<td>Chief</td>				
				@endif
				<td>Manager</td>
				<td>DGM</td>
				<td>GM</td>
				<td>Manager</td>
				<!-- <td colspan="2"></td> -->
			</tr>
			<tr>
				<td rowspan="2">2</td>
				<td colspan="8">Immediate Action (Filled By QA)</td>
			</tr>
			<tr>
				<td colspan="8"><?= $cpar->tindakan ?></td>
			</tr>
			<tr>
				<td rowspan="2">3</td>
				<td colspan="8">Verification Status</td>
			</tr>
			<tr>
				<td colspan="8">{{$cpar->status_name}}</td>
			</tr>
			<tr>
				<td rowspan="6">4</td>
				<td colspan="8">Cost Estimation</td>
			</tr>
			<tr>
				<td colspan="8"><?= $cpar->cost ?> </td>
			</tr>
			<tr>
				<td>Prepared By</td>
				<td>Checked By</td>
				<td>Known By</td>
				<td colspan="5"></td>
			</tr>
			<tr>
				<td rowspan="2">
					@if($cpar->posisi == "QA" || $cpar->posisi == "QA2" || $cpar->posisi == "QAmanager")
						@if($cpar->staff != null)
							{{$cpar->staffname}}
						@elseif($cpar->leader != null)
							{{$cpar->leadername}}
						@else
							&nbsp;
						@endif
					@endif
				</td>
				<td rowspan="2">
					@if($cpar->posisi == "QA2" || $cpar->posisi == "QAmanager")
						@if($cpar->staff != null)
							{{$cpar->chiefname}}
						@elseif($cpar->leader != null)
							{{$cpar->foremanname}}
						@else
							&nbsp;
						@endif
					@endif
				</td>
				<td rowspan="2">
					@if($cpar->posisi == "QAmanager")
						{{$cpar->managername}}
					@endif
				</td>
				<td colspan="5" rowspan="2"></td>
			</tr>
			<tr></tr>
			<tr>
				<td>Staff</td>
				<td>Chief</td>
				<td>Manager</td>
				<td colspan="5"></td>
			</tr>
			@endforeach
		</tbody>
	</table>
</body>
</html>