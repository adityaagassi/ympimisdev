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
      	<table class="table" style="border: 1px solid black;">
			@foreach($cars as $car)
		<thead>
			<tr>
				<td colspan="2" rowspan="3" class="centera">
					<!-- <img width="80px" src="{{ asset('images/logo_yamaha2.png') }}" alt=""> -->
					<img width="200px" src="{{ asset('waves.jpg') }}" alt="">
				</td>
				<td colspan="6" rowspan="3" class="centera" style="font-size: 25px;font-weight: bold">CORRECTIVE ACTION REPORT</td>
				<td class="centera" width="10%">Approved By</td>
				<td class="centera" width="10%">Approved By</td>
				<td class="centera" width="10%">Approved By</td>
			</tr>
			<tr>
				<td class="centera">
					@if($car->approved_gm == "Checked")
						{{$car->gmname}}
					@else
						&nbsp;
					@endif
				</td>
				<td class="centera">
					@if($car->approved_dgm == "Checked")
						{{$car->dgmname}}
					@else
						&nbsp;
					@endif
				</td>
				<td class="centera">
					@if($car->checked_manager == "Checked")
						{{$car->managername}}
					@else
						&nbsp;
					@endif
				</td>
			</tr>
			<tr>
				<td class="centera">GM</td>
				<td class="centera">DGM</td>
				<td class="centera">Manager</td>
			</tr>
		</thead>
		<tbody>
			 <?php 
	          $tinjauan = $car->tinjauan; 
	          
	          if($tinjauan != NULL){
	            $split = explode(",", $tinjauan);
	            $hitungsplit = count($split);
	          }else{
	            $split = 0;
	          }
	        ?>
			<tr>
				<td colspan="2" width="20%">
					Kategori Komplain : {{ $car->kategori }}
				</td>
				<td colspan="2" width="20%">
					Departemen : {{ $car->department_name }}
				</td>
				<td colspan="2" width="20%">
					Section : {{ $car->section }}
				</td>
				<td colspan="2" width="20%">
					Date : <?php echo date('d F Y', strtotime($car->tgl_car)) ?>
				</td>
				<td colspan="3" width="20%">
					Location : {{ $car->lokasi }}			
				</td>
			</tr>
			<tr>
				<td colspan="2" width="20%">Tinjauan 4M : </td>
				<td colspan="2" width="20%" class="" style="font-size: 16px">Man <input type="checkbox" class="centera" style="font-size: 14px;margin: 0" 
				<?php
					foreach ($split as $key) {
		                if ($key == 1) {
		                  echo 'checked';
		                }
		            } ?>>
				</td>
				<td colspan="2" width="20%" class="" style="font-size: 16px">Material <input type="checkbox" class="centera" style="font-size: 14px;margin: 0" 
				<?php
					foreach ($split as $key) {
		                if ($key == 2) {
		                  echo 'checked';
		                }
		            } ?>>
				</td>
				<td colspan="2" width="20%" class="" style="font-size: 16px">Machine <input type="checkbox" class="centera" style="font-size: 14px;margin: 0" 
				<?php
					foreach ($split as $key) {
		                if ($key == 3) {
		                  echo 'checked';
		                }
		            } ?>>
				</td>
				<td colspan="3" width="20%" class="" style="font-size: 16px">Method <input type="checkbox" class="centera" style="font-size: 14px;margin: 0" 
				<?php
					foreach ($split as $key) {
		                if ($key == 4) {
		                  echo 'checked';
		                }
		            } ?>>
		        </td>	
			</tr>
			<tr style="page-break-inside:avoid">
				<td colspan="11"><b style="font-size: 20px">Description</b> : <?= $car->deskripsi ?></td>
				<!-- <td rowspan="2" colspan="3" class="centera" style="font-weight: bold;font-size: 12px">Tinjauan 4M </td> -->
			</tr>
			<tr style="page-break-inside:avoid">
				<td colspan="11"><b style="font-size: 20px">A. Immediately Action</b> : <?= $car->tindakan ?></td>
			</tr>
			<tr style="page-break-inside:avoid">
				<td colspan="11"><b style="font-size: 20px">B. Possibility Cause</b> : <?= $car->penyebab ?></td>
			</tr>
			<tr style="page-break-inside:avoid">
				<td colspan="11"><b style="font-size: 20px">C. Corrective Action</b> : <?= $car->perbaikan ?></td>
			</tr>
			 <?php if ($car->file != null){ ?>
			<tr style="page-break-inside:avoid">
				<td colspan="11"><b style="font-size: 20px">Terlampir</b> : 
					<?php $data = json_decode($car->file);
                    for ($i = 0; $i < count($data); $i++) { ?>
                            <a href="{{ url('/files/car/'.$data[$i]) }}" class="fa fa-paperclip"> {{$data[$i]}}</a> &nbsp;
                  <?php } ?>         
              </td>
			</tr>
			<?php } ?>
			<tr>
				<td colspan="9"></td>
				<td class="centera">Prepared</td>
				<td class="centera">Checked</td>
			</tr>
			<tr>
				<td rowspan="2" colspan="9"></td>
				<td rowspan="2" class="centera">
					@if($car->pic != null)
						{{$car->picname}}
					@else
						&nbsp;
					@endif
				</td>
				<td rowspan="2" class="centera">
					@if($car->checked_chief == "Checked")
						{{$car->chiefname}}
					@elseif($car->checked_foreman == "Checked")
						{{$car->foremanname}}
					@elseif($car->checked_coordinator == "Checked")
						{{$car->coordinatorname}}
					@else
						&nbsp;
					@endif
				</td>
			</tr>
			<tr></tr>
			<tr>
				<td colspan="9"></td>			
				@if($car->kategori == "Internal")
					<td class="centera">Leader</td>
					<td class="centera">Foreman</td>
				@else
					<td class="centera">Staff</td>
					<td class="centera">Chief</td>				
				@endif
			</tr>
			<tr style="page-break-inside:avoid">
				<td colspan="11">
					<b style="font-size: 20px">D. QA Verification</b> 
				</td>
			</tr>

			<?php for ($i=0; $i < count($verifikasi); $i++) { ?>
			
			<tr>
				<td colspan="2">
					<p style="font-size: 18px">Tanggal : <?= $verifikasi[$i]->tanggal ?></p>
					<p style="font-size: 18px">Status : <?= $verifikasi[$i]->status ?></p>
				</td>
				<td colspan="9">
					<p style="font-size: 18px">Verifikasi <?= $i+1 ?> : <?= $verifikasi[$i]->keterangan ?></p>
				</td>
			</tr>

			<?php } ?>

			<tr>
				<td colspan="8"></td>
				<td>Prepared By</td>
				<td>Checked By</td>
				<td>Checked By</td>
			</tr>
			<tr>
				<td colspan="8" rowspan="2"></td>
				<td rowspan="2">
					@if($car->posisi_cpar == "QA" || $car->posisi_cpar == "QA2" || $car->posisi_cpar == "QAmanager" || $car->posisi_cpar == "QAFIX")
						@if($car->staff != null)
							{{$car->staffqaname}}
						@elseif($car->leader != null)
							{{$car->leaderqaname}}
						@else
							&nbsp;
						@endif
					@endif
				</td>
				<td rowspan="2">
					@if($car->posisi_cpar == "QA2" || $car->posisi_cpar == "QAmanager" || $car->posisi_cpar == "QAFIX")
						@if($car->staff != null)
							{{$car->chiefqaname}}
						@elseif($car->leader != null)
							{{$car->foremanqaname}}
						@else
							&nbsp;
						@endif
					@endif
				</td>
				<td rowspan="2">
					@if($car->posisi_cpar == "QAmanager" || $car->posisi_cpar == "QAFIX")
						{{$car->managerqaname}}
					@else
						&nbsp;
					@endif
				</td>
			</tr>
			<tr></tr>
			<tr>
				<td colspan="8"></td>
				<td>Staff</td>
				<td>Chief</td>
				<td>Manager</td>
			</tr>
			
			<!-- <tr>
				<td colspan="10"></td>
			</tr>
			<tr>
				<td rowspan="2" colspan="6" class="centera" style="font-weight: bold;font-size: 20px">Verification Result</td>
				<td rowspan="2" class="centera">Dept In Charge</td>
				<td colspan="3" class="centera">QA</td>
			</tr>
			<tr>
				<td class="centera">Verified</td>
				<td class="centera">Checked</td>
				<td class="centera">Approved</td>
			</tr>
			<tr>
				<td colspan="2">Date Of Verification:</td>
				<td>Tanggal</td>
				<td colspan="3">Comment</td>
				<td rowspan="2"></td>
				<td rowspan="2"></td>
				<td rowspan="2"></td>
				<td rowspan="2"></td>
			</tr>
			<tr>
				<td colspan="2">Status</td>
				<td>Open</td>
				<td colspan="3"></td>
			</tr>
			<tr>
				<td colspan="6"></td>
				<td class="centera">Manager</td>
				<td class="centera">QA Staff</td>
				<td class="centera">QA Chief</td>
				<td class="centera">QA Manager</td>
			</tr> -->
		</tbody>
		@endforeach
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
    function myFunction() {
	  window.print();
	}
</script>
