<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		td{
			padding-right: 5px;
			padding-left: 5px;
			padding-top: 0px;
			padding-bottom: 0px;
		}
		th{
			padding-right: 5px;
			padding-left: 5px;			
		}
	</style>
</head>
<body>
	<div>
		<center>

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Penerbitan CPAR {{ $data[0]->cpar_no }}<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h1>Komplain : {{$data[0]->judul_komplain}}</h1>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 2%; border:1px solid black;color: white" colspan="4">Issue CPAR {{ $data[0]->cpar_no }}</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 15%; border:1px solid black;">Location</td>
						<td style="width: 35%; border:1px solid black;">{{$data[0]->lokasi}}</td>

						<td style="width: 15%; border:1px solid black;">Source Of Complain</td>
						<td style="width: 35%; border:1px solid black;">{{$data[0]->sumber_komplain}}</td>
					</tr>
					<tr>
						<td style="width: 15%; border:1px solid black;">Issue Date</td>
						<td style="width: 35%; border:1px solid black;"><?php echo date('d F Y', strtotime($data[0]->tgl_permintaan)) ?></td>

						<td style="width: 15%; border:1px solid black;">Department</td>
						<td style="width: 35%; border:1px solid black;">{{$data[0]->department_name}}</td>
					</tr>
					<tr>
						<td style="width: 15%; border:1px solid black;">Request Due Date</td>
						<td style="width: 35%; border:1px solid black;"><?php echo date('d F Y', strtotime($data[0]->tgl_balas)) ?></td>

						@if($data[0]->kategori == "Supplier")
							<td style="width: 15%; border:1px solid black;">Vendor</td>
		                @elseif($data[0]->kategori == "Eksternal")
							<td style="width: 15%; border:1px solid black;">Customer</td>
		                @elseif($data[0]->kategori == "Internal")
							<td style="width: 15%; border:1px solid black;">Penemu NG</td>
		                @endif

		                <?php 
		                    if($data[0]->kategori == "Internal"){
		                      $kategori = $data[0]->penemu_ng;
		                    }
		                    else{
		                      $kategori = '';
		                    }

		                    if($data[0]->kategori == "Eksternal"){
		                      $dest = $data[0]->destination_name;
		                    }
		                    else{
		                      $dest = '';
		                    }

		                    if($data[0]->kategori == "Supplier"){
		                      $vendor = $data[0]->vendorname.' -';
		                    }
		                    else{
		                      $vendor = '';
		                    }
		                  ?>

						<td style="width: 35%;border:1px solid black;"><?= $dest ?> <?= $vendor ?> <?= $kategori ?></td>
					</tr>
				</tbody>
			</table>

			<br>

			<h3 style="text-align: center !important">Detail CPAR {{ $data[0]->cpar_no }}</h3>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black;color: white;" colspan="3">CPAR Information</th>
					</tr>

				</thead>
				<tbody>

					<tr>
						<th style="width: 70%; border:1px solid black;">GMC</th>
						<th style="width: 15%; border:1px solid black;">Sample Qty</th>
						<th style="width: 15%; border:1px solid black;">Defect Qty</th>	
					</tr>

					@foreach($data as $datas)

					<tr>
						<td style="border:1px solid black;">{{$datas->part_item}} - {{$datas->material_description}}</td>
						<td style="border:1px solid black;text-align: center">{{$datas->sample_qty}} pcs</td>
						<td style="border:1px solid black;text-align: center">{{$datas->defect_qty}} pcs</td>
					</tr>

					@endforeach

					<tr style="background-color: #f5eb33">
						<th style="width: 1%; border:1px solid black;color: black;" colspan="3">Problem Detail</th>
					</tr>

					<tr>
						<td colspan="3" style="border:1px solid black;"><?= $data[0]->detail_problem ?><br></td>
					</tr>
				</tbody>
			</table>

			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>			
			
			<a href="http://10.109.52.4/mirai/public/index/qc_report/print_cpar/{{ $data[0]->id }}">Report CPAR</a><br>
			@if($data[0]->posisi == "bagian")
				<a href="{{ url('index/qc_car/detail/'.$data[0]->id_car) }}">See CAR Data</a><br>
			@else
				<a href="{{ url('index/qc_report/verifikasicpar/'.$data[0]->id) }}">CPAR Verification</a><br>
			@endif
		</center>
	</div>
</body>
</html>