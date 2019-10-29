<html>
<head>
	<title>laporan PDF</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
</head>
<body>
	<style type="text/css">
		table tr td,
		table tr th{
			font-size: 6pt;
			border: 1px solid black !important;
			border-collapse: collapse;
		}

	</style>
	
 	
	<table class="table table-bordered">
		<thead>
			<tr>
				<td colspan="2" style="text-align: center; vertical-align: middle;">
					<img width="80px" src="{{ public_path() . '/waves.jpg' }}" alt="">

				</td>
				<td colspan="5" style="text-align: center; vertical-align: middle;font-size: 14px;font-weight: bold">CORRECTIVE & PREVENTIVE ACTION REQUEST</td>
				<td colspan="2" style="font-size: 9px;">
					No Dokumen : YMPI/QA/FM/988 <br>
					Revisi : 01<br>
					Tanggal : 8 Oktober 2019<br>
				</td>
			</tr>
		</thead>
		<tbody>
			@php $i=1 @endphp
			@foreach($cpars as $cpar)
			<tr>
				<td rowspan="11">{{ $i++ }}</td>	
				<td colspan="5" style="border: none !important">To : <b>{{$cpar->name}}</b></td>
				<td colspan="3" style="border: none !important; border-right: 1px solid black !important;">CPAR No : <b>{{$cpar->cpar_no}}</b></td>
			</tr>
			<tr>
				<td colspan="5" style="border: none !important">Location : <b>{{$cpar->lokasi}}</b></td>
				<td colspan="3" style="border: none !important; border-right: 1px solid black !important;">Information of Complaint Via : <b>{{$cpar->via_komplain}}</b></td>
			</tr>
			<tr>
				<td colspan="5" style="border: none !important">Request Date : <b>{{$cpar->tgl_permintaan}}</b></td>
				<td colspan="3" style="border: none !important; border-right: 1px solid black !important;">Source Of Complaint : <b>{{$cpar->sumber_komplain}}</b></td>
			</tr>
			<tr>
				<td colspan="5" style="border: none !important">Request Due Date: <b>{{$cpar->tgl_balas}}</b></td>
				<td colspan="3" style="border: none !important; border-right: 1px solid black !important;">Department : <b>{{$cpar->department_name}}</b></td>
			</tr>
			<tr>
				<td>Part Item</td>
				<td>Part Description</td>
				<td>Invoice No</td>
				<td>ETA Qty</td>
				<td>Sample Qty</td>
				<td>Detail Problem</td>
				<td>Defect Qty</td>
				<td>% Defect</td>
			</tr>

			<?php if(count($parts) != 0) { ?>
			@foreach($parts as $part)
			<tr>
				<?php if ($part != null) { ?>
					<td rowspan="2">{{$part->part_item}}</td>
					<td rowspan="2">Part Description</td>
					<td rowspan="2">{{$part->no_invoice}}</td>
					<td rowspan="2">{{$part->lot_qty}}</td>
					<td rowspan="2">{{$part->sample_qty}}</td>
					<td rowspan="2" width="80"></td>
					<td rowspan="2">{{$part->defect_qty}}</td>
					<td rowspan="2">{{$part->defect_presentase}}</td>
				<?php } else { ?>
					<td colspan="8"></td>
				<?php } ?>
			</tr>
			@endforeach
			<?php } else { ?>
			<tr>
				<td rowspan="2">&nbsp;</td>
				<td rowspan="2"></td>
				<td rowspan="2"></td>
				<td rowspan="2"></td>
				<td rowspan="2"></td>
				<td rowspan="2"></td>
				<td rowspan="2"></td>
				<td rowspan="2"></td>
			</tr>
			<?php } ?>
			

			<tr></tr>
			<tr>
				<td colspan="8">&nbsp;</td>
			</tr>
			<!-- <tr><td colspan="8"></td></tr> -->
			<tr>
				<td>Prepared By</td>
				<td>Checked By</td>
				<td>Checked By</td>
				<td>Approved By</td>
				<td>Approved By</td>
				<td>Received By</td>
				<td>Received By</td>
				<td>Received By</td>
			</tr>
			<tr>
				<td rowspan="2">&nbsp;</td>
				<td rowspan="2">&nbsp;</td>
				<td rowspan="2">&nbsp;</td>
				<td rowspan="2">&nbsp;</td>
				<td rowspan="2">&nbsp;</td>
				<td rowspan="2">&nbsp;</td>
				<td rowspan="2">&nbsp;</td>
				<td rowspan="2">&nbsp;</td>
			</tr>
			<tr></tr>
			<tr>
				<td rowspan="4">2</td>
				<td colspan="8">Immediate Action (Filled By QA)</td>
			</tr>
			<tr>
				<td colspan="8" rowspan="3">&nbsp;</td>
			</tr>
			<tr></tr>
			<tr></tr>
			<tr>
				<td rowspan="4">3</td>
				<td colspan="8">Possibility Cause & Corrective Action</td>
			</tr>
			<tr>
				<td colspan="8" rowspan="3">&nbsp;</td>
			</tr>
			<tr></tr>
			<tr></tr>
			<tr>
				<td rowspan="2">4</td>
				<td colspan="8">Verifikasi Status</td>
			</tr>
			<tr>
				<td colspan="8">Open / Closed</td>
			</tr>
			<tr>
				<td rowspan="9">5</td>
				<td colspan="8">Cost Estimation : PT YMPI / Customer</td>
			</tr>
			<tr>
				<td colspan="8">Inspection Cost : </td>
			</tr>
			<tr>
				<td colspan="8">Repair Cost : </td>
			</tr>
			<tr>
				<td colspan="8">Analysis Cost : </td>
			</tr>
			<tr>
				<td colspan="8">Other : </td>
			</tr>
			<tr><td colspan="8">&nbsp;</td></tr>
			<tr>
				<td>Prepared By</td>
				<td>Checked By</td>
				<td>Approved By</td>
				<td>Approved By</td>
				<td>Approved By</td>
				<td colspan="3"></td>
				
			</tr>
			<tr>
				<td rowspan="2">&nbsp;</td>
				<td rowspan="2">&nbsp;</td>
				<td rowspan="2">&nbsp;</td>
				<td rowspan="2">&nbsp;</td>
				<td rowspan="2">&nbsp;</td>
				<td colspan="3" rowspan="2"></td>
			</tr>
			<tr></tr>
			@endforeach
		</tbody>
	</table>
</body>
</html>