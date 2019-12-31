@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Verifikasi Form Ketidaksesuaian Material
    <small>Approval</small>
    <a class="btn btn-success pull-right" href="{{url('index/request_qa/print',$reqid)}}" target="_blank">Export To PDF</a>

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
  @if ($errors->has('password'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
    {{ $errors->first() }}
  </div>   
  @endif
  @if (session('error'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Not Verified!</h4>
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
			<thead>
			@foreach($req as $req)
			<tr>
				<td colspan="2" class="centera" >
					<center><img width="175px" src="{{ asset('images/logo_yamaha2.png') }}" alt="" style="vertical-align: middle !important"></center>
				</td>
				<td colspan="8" style="text-align: center; vertical-align: middle;font-size: 18px;font-weight: bold">Form Laporan Ketidaksesuaian Material</td>
				@if($reqid->approval == Null)
				<td colspan="2" style="border: 1px solid black;vertical-align: middle;"><center>Approval</center></td>
				@endif
			</tr>
		</thead>
		<tbody>
			<form role="form" method="post" action="{{url('index/request_qa/approval/'.$reqid->id)}}">
			<tr>
				<td colspan="10" style="font-size: 18px"><b>Keterangan Umum</b></td>
				@if($reqid->approval == Null)
				<td colspan="2" rowspan="5" style="border: 1px solid black;vertical-align: middle;">
					<center>
						<label class="label label-success"  style="font-size: 100%">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<input type="checkbox" class="minimal-red" name="approve[]" value="1"> Approve
						</label>
					</center>					
				</td>
				@endif
			</tr>
			<tr>
				<td style="border:none;width: 25%">Subject</td>
				<td style="text-align: right;border:none">:</td>
				<td colspan="8" style="border:none"><b>{{ $req->subject }}</b></td>
			</tr>
			<tr>
				<td style="border:none">Tanggal</td>
				<td style="text-align: right;border:none">:</td>
				<td colspan="8" style="border:none"><b><?php echo date('d F Y', strtotime($req->tanggal)) ?></b></td>
			</tr>
			
			<tr>
				<td style="border:none">Section Pelapor</td>
				<td style="text-align: right;border:none">:</td> 
				<td colspan="8" style="border:none"><b>{{ $req->section_from }}</b></td>
			</tr>

			<tr>
				<td style="border:none">Section Yang Dituju</td>
				<td style="text-align: right;border:none">:</td> 
				<td colspan="8" style="border:none"><b>{{ $req->section_to }}</b></td>
			</tr>
			<tr>
				<td colspan="10" style="font-size: 18px;border-top: 1px solid black"><b>Penanganan Oleh Produksi</b></td>
				@if($reqid->approval == Null)
				<td colspan="2" rowspan="5" style="border: 1px solid black;vertical-align: middle;">
					<center>
						<label class="label label-success"  style="font-size: 100%">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<input type="checkbox" class="minimal-red" name="approve[]" value="2"> Approve
						</label>
					</center>
				</td>
				@endif
			</tr>
			<tr>
				<td style="border:none">Target Perhari</td>
				<td style="text-align: right;border:none">:</td> 
				<td colspan="8" style="border:none"><b>{{ $req->target }} Pcs/hari</b></td>
			</tr>
			<tr>
				<td style="border:none">Jumlah Perkiraan Keterlambatan</td>
				<td style="text-align: right;border:none">:</td> 
				<td colspan="8" style="border:none"><b>{{ $req->jumlah }} Pcs</b></td>
			</tr>
			<tr>
				<td style="border:none">Waktu Penanganan Masalah</td>
				<td style="text-align: right;border:none">:</td> 
				<td colspan="8" style="border:none;"><b> {{ $req->waktu }} Menit</b></td>
			</tr>
			<tr>
				<td  style="border:none">Corrective Action Oleh Section Pelapor</td>
				<td style="text-align: right;border:none">:</td> 
				<td colspan="8" style="border:none"><b><?= $req->aksi ?></b></td>
			</tr>
			<?php 
				$jumlahitem = count($items);
			?>
			<tr>
				<td colspan="10" style="font-size: 18px;border-top: 1px solid black"><b>Material / Item</b></td>
				@if($reqid->approval == Null)
				<td colspan="2" rowspan="{{ 3 + $jumlahitem }}" style="border: 1px solid black;vertical-align: middle;">
					<center>
						<label class="label label-success"  style="font-size: 100%">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<input type="checkbox" class="minimal-red" name="approve[]" value="3"> Approve
						</label>
					</center>
				</td>
				@endif
			</tr>
			<tr>
				<td style="border-top: 1px solid black">Nomor Item</td>
				<td colspan="2" style="border-top: 1px solid black">Nama Material</td>
				<td colspan="2" style="border-top: 1px solid black">Supplier</td>
				<td colspan="2" style="border-top: 1px solid black">Jumlah Cek</td>
				<td colspan="2" style="border-top: 1px solid black">Jumlah NG</td>
				<td style="border-top: 1px solid black">% NG</td>
			</tr>
			
			<?php 
			$jumlahitem = count($items);
			if($jumlahitem != 0) { 

			?>
			@foreach($items as $item)
			<tr>
				<td><b>{{$item->item}}</b></td>
				<td colspan="2"><b>{{$item->item_desc}}</b></td>
				<td colspan="2"><b>{{$item->supplier}}</b></td>
				<td colspan="2"><b>{{$item->jml_cek}}</b></td>
				<td colspan="2"><b>{{$item->jml_ng}}</b></td>
				<td><b>{{$item->presentase_ng}}</b></td>
			</tr>
			@endforeach
			<?php }
			else { 
			?>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2"></td>
				<td colspan="2"></td>
				<td colspan="2"></td>
				<td colspan="2"></td>
				<td></td>
			</tr>
			<tr></tr>
			<?php } ?>
			<?php if($jumlahitem != 0) { ?> 
			<tr>
				<td colspan="10"><p style="font-size: 14px">Detail Ketidaksesuaian : </p><b><?= $item->detail ?></b></td>
			</tr>
			<?php } else { ?>
			<tr>
				<td colspan="10">&nbsp;</td>
			</tr>	
			<?php } ?>
			
			<tr>
				<td colspan="6" rowspan="3">&nbsp;</td>
				<td>Pelapor</td>
				<td>Mengetahui</td>
				<td>Mengetahui</td>
				<td>Mengetahui</td>
				@if($reqid->approval == Null)
				<td colspan="2" rowspan="3" style="border: 1px solid black;vertical-align: middle;">
					<center>
						<button class="btn btn-success" type="submit">Submit</button>
					</center>
				</td>
				@endif
				
			</tr>
			<tr>
				<td style="vertical-align: middle;">
				</td>
				<td style="vertical-align: middle;">
				</td>
				<td style="vertical-align: middle;">
				</td>
				<td style="vertical-align: middle;">
				</td>
				<!-- <td colspan="2" rowspan="2" style="vertical-align: middle;">&nbsp;</td> -->
					
				</td>
			</tr>
			<tr>
				<td>Leader</td>
				<td>Foreman</td>
				<td>Chief</td>	
				<td>Manager</td>
				<!-- <td colspan="2"></td> -->
			</tr>
			@endforeach
		</tbody>
		</table>
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
    // setTimeout(function () { window.print(); }, 200);
    function myFunction() {
	  window.print();
	}
	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
	});
</script>
