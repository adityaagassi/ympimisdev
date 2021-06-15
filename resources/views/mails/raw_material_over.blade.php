<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		table {
			border-collapse: collapse;
		}
		table, th, td {
			border: 1px solid black;
		}
		td {
			padding-top: 0px;
			padding-bottom: 0px;
			padding-left: 3px;
			padding-right: 3px;
		}
	</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 20px;">Raw Material Over Plan Usage On {{ $data['month_text'] }}<br>{{ $data['start_date'] }} - {{ $data['end_date'] }}</p>
			
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="90%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th rowspan="2" style="width: 5%; border:1px solid black;">Material</th>
						<th rowspan="2" style="width: 15%; border:1px solid black;">Description</th>
						<th rowspan="2" style="width: 5%; border:1px solid black;">UoM</th>
						<th rowspan="2" style="width: 5%; border:1px solid black;">Plan Usage</th>
						<th colspan="6" style="width: 60%; border:1px solid black;">Delivery</th>
						<th rowspan="2" style="width: 5%; border:1px solid black;">Total</th>
						<th rowspan="2" style="width: 5%; border:1px solid black;">Status</th>
					</tr>
					<tr>
						<th style="width: 10%; border:1px solid black; background-color: rgb(126,86,134);">Part Process</th>
						<th style="width: 10%; border:1px solid black; background-color: rgb(126,86,134);">Body Process</th>
						<th style="width: 10%; border:1px solid black; background-color: rgb(126,86,134);">Welding</th>
						<th style="width: 10%; border:1px solid black; background-color: rgb(126,86,134);">Surface Treatement</th>
						<th style="width: 10%; border:1px solid black; background-color: rgb(126,86,134);">Assembly</th>
						<th style="width: 10%; border:1px solid black; background-color: rgb(126,86,134);">EDIN</th>
					</tr>
				</thead>
				<tbody>					
					@foreach($data['overs'] as $over)
					<tr>
						<td style="border:1px solid black; text-align: center;">{{ $over->material_number }}</td>
						<td style="border:1px solid black;">{{ $over->material_description }}</td>
						<td style="border:1px solid black; text-align: center;">{{ $over->bun }}</td>
						<td style="border:1px solid black; text-align: right;">{{ round($over->usage, 3) }}</td>

						@php $is_filled = false; @endphp
						@foreach($data['details'] as $detail)
						@if($over->material_number == $detail->material_number && $detail->department_group == 'PP')
						<td style="border:1px solid black; text-align: right;">{{ round($detail->quantity, 3) }}</td>
						@php $is_filled = true; @endphp
						@endif
						@endforeach
						@if(!$is_filled)
						<td style="border:1px solid black; text-align: right;">0</td>
						@endif

						@php $is_filled = false; @endphp
						@foreach($data['details'] as $detail)
						@if($over->material_number == $detail->material_number && $detail->department_group == 'BP')
						<td style="border:1px solid black; text-align: right;">{{ round($detail->quantity, 3) }}</td>
						@php $is_filled = true; @endphp
						@endif
						@endforeach
						@if(!$is_filled)
						<td style="border:1px solid black; text-align: right;">0</td>
						@endif

						@php $is_filled = false; @endphp
						@foreach($data['details'] as $detail)
						@if($over->material_number == $detail->material_number && $detail->department_group == 'WELDING')
						<td style="border:1px solid black; text-align: right;">{{ round($detail->quantity, 3) }}</td>
						@php $is_filled = true; @endphp
						@endif
						@endforeach
						@if(!$is_filled)
						<td style="border:1px solid black; text-align: right;">0</td>
						@endif

						@php $is_filled = false; @endphp
						@foreach($data['details'] as $detail)
						@if($over->material_number == $detail->material_number && $detail->department_group == 'ST')
						<td style="border:1px solid black; text-align: right;">{{ round($detail->quantity, 3) }}</td>
						@php $is_filled = true; @endphp
						@endif
						@endforeach
						@if(!$is_filled)
						<td style="border:1px solid black; text-align: right;">0</td>
						@endif

						@php $is_filled = false; @endphp
						@foreach($data['details'] as $detail)
						@if($over->material_number == $detail->material_number && $detail->department_group == 'ASSEMBLY')
						<td style="border:1px solid black; text-align: right;">{{ round($detail->quantity, 3) }}</td>
						@php $is_filled = true; @endphp
						@endif
						@endforeach
						@if(!$is_filled)
						<td style="border:1px solid black; text-align: right;">0</td>
						@endif

						@php $is_filled = false; @endphp
						@foreach($data['details'] as $detail)
						@if($over->material_number == $detail->material_number && $detail->department_group == 'EI')
						<td style="border:1px solid black; text-align: right;">{{ round($detail->quantity, 3) }}</td>
						@php $is_filled = true; @endphp
						@endif
						@endforeach
						@if(!$is_filled)
						<td style="border:1px solid black; text-align: right;">0</td>
						@endif


						<td style="border:1px solid black; text-align: right;">{{ $over->quantity }}</td>
						<td style="border:1px solid black; text-align: center;">Over Usage</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			<br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url("index/material/material_monitoring/direct") }}">Raw Material Monitoring (素材監視)</a><br>

		</center>
	</div>
</body>
</html>