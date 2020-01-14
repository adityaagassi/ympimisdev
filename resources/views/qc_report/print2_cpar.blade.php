@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    <!-- <small>it all starts here</small> -->
    <button class="btn btn-primary pull-right" onclick="myFunction()">Print</button>
    <br>
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
	}

		table tr td,
		table tr th{
			font-size: 12pt;
			border: 1px solid black !important;
			border-collapse: collapse;
		}
		.centera{
			text-align: center;
			vertical-align: middle !important;
		}
		.square {
			height: 5px;
			width: 5px;
			border: 1px solid black;
			background-color: transparent;
		}
		table {
			page-break-inside: avoid;
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
      	<table class="table table-bordered" style="table-layout: fixed">
			<thead>
				<tr>
					<td colspan="3" class="centera" >
						<img width="120" src="{{ public_path() . '/waves.jpg' }}" alt="" style="vertical-align: middle !important">
					</td>
					<td colspan="5" style="text-align: center; vertical-align: middle;font-size: 22px;font-weight: bold">CORRECTIVE & PREVENTIVE ACTION REQUEST</td>
					<td colspan="2" style="font-size: 14px;">
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
				else if($jumlahparts == 2)
					$jumlah = 2;
				else if($jumlahparts == 3)
					$jumlah = 4;
				else if($jumlahparts == 4)
					$jumlah = 6;
				else if($jumlahparts == 5)
					$jumlah = 8;
				else if($jumlahparts == 6)
					$jumlah = 10;
				else if($jumlahparts == 7)
					$jumlah = 12;
				else if($jumlahparts == 8)
					$jumlah = 14;
				?>

				@foreach($cpars as $cpar)
				<tr>
					<td rowspan="{{ 12 + $jumlah }}" style="width: 5%">{{ $i++ }}</td>
					<td colspan="5" style="border: none !important">To : <b>{{$cpar->name}}</b></td>
					<td colspan="4" style="border: none !important; border-right: 1px solid black !important;">CPAR No : <b>{{$cpar->cpar_no}}</b></td>
				</tr>
				<tr>
					<td colspan="5" style="border: none !important">Location : <b>{{$cpar->lokasi}}</b></td>
					<td colspan="4" style="border: none !important; border-right: 1px solid black !important;">Source Of Complaint : <b>{{$cpar->sumber_komplain}}</b></td>
				</tr>
				<tr>
					<td colspan="5" style="border: none !important">Issue Date : <b><?php echo date('d F Y', strtotime($cpar->tgl_permintaan)) ?></b></td>
					<td colspan="4" style="border: none !important; border-right: 1px solid black !important;">Department : <b>{{$cpar->department_name}}</b></td>
				</tr>
				<tr>
					<td colspan="5" style="border: none !important">Request Due Date : <b><?php echo date('d F Y', strtotime($cpar->tgl_balas)) ?></b><br>(CPAR Return to QA)</td>
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
					<td>Invoice / Lot No</td>
					<td>Sample / Check Qty</td>
					<td>Defect Qty</td>
					<td>% Defect</td>
					<td colspan="2"></td>
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
					<td rowspan="2" colspan="2"></td>
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
					<td rowspan="2" colspan="2"></td>
				</tr>
				<tr></tr>
				<?php } ?>
				<?php if($jumlahparts != 0) { ?> 
				<tr>
					<td colspan="9"><p style="font-size: 12px">Detail Problem : </p><?= $part->detail_problem ?></td>
				</tr>
				<?php } else { ?>
				<tr>
					<td colspan="9">&nbsp;</td>
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
					<td colspan="3" rowspan="4">&nbsp;</td>
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
					<td>Staff / Leader</td>
					<td>Chief / Foreman</td>
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
					<td colspan="9">Immediate Action (Filled By QA)</td>
				</tr>
				<tr>
					<td colspan="9"><?= $cpar->tindakan ?></td>
				</tr>
				<tr>
					<td rowspan="2">3</td>
					<td colspan="9">Verification Status</td>
				</tr>
				<tr>
					<td colspan="9">{{$cpar->status_name}}</td>
				</tr>
				<tr>
					<td rowspan="6">4</td>
					<td colspan="9">Cost Estimation</td>
				</tr>
				<tr>
					<td colspan="9"><?= $cpar->cost ?> </td>
				</tr>
				<tr>
					<td>Prepared By</td>
					<td>Checked By</td>
					<td>Known By</td>
					<td colspan="6"></td>
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
						@else
							&nbsp;
						@endif
					</td>
					<td colspan="6" rowspan="2"></td>
				</tr>
				<tr></tr>
				<tr>
					<td>Staff</td>
					<td>Chief</td>
					<td>Manager</td>
					<td colspan="6"></td>
				</tr>
				@endforeach
			</tbody>
		</table>
		<div class="col-md-12" style="text-align: right;">
			<span style="font-size: 20px">No FM : YMPI/QA/FM/899</span>
		</div>
	</div>
  </div>
  @endsection
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
  font-family:"Arial";
  padding: 5px;
}
@media print {
	body {-webkit-print-color-adjust: exact;}
}
</style>
<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<script>
	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
	});
 //    function myFunction() {
	//   window.print();
	// }
</script>
