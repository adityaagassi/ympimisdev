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

		@page { margin: 50px 50px; }
		.header { position: fixed; left: 0px; top: -100px; right: 0px; height: 100px; text-align: center; }
		.footer { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 50px;text-align: center;}
		.footer .pagenum:before { content: counter(page); }

	</style>
	
	<div class="footer">

		Page <span class="pagenum"></span>
	</div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<td colspan="10" style="text-align: center; vertical-align: middle;font-size: 14px;font-weight: bold">OFFICIAL VARIANCE STOCK TAKING APRIL 2020	</td>
			</tr>
			<tr style="font-weight: bold;">
				<td>Plant</td>
				<td>Group</td>
				<td>Loc</td>
				<td>Sum of pi_amt</td>
				<td>Sum of book_amt</td>
				<td>Sum of diff_amt</td>
				<td>Sum of var_amt(-)</td>
				<td>Sum of var_amt(+)</td>
				<td>Sum of var_amt_abs</td>
				<td>Percentage</td>
			</tr>
		</thead>
		<tbody>

			

			@php

			$_8190 = 0;
			$_8191 = 0;
			$assembly = 0;
			$ei = 0;
			$pp = 0;
			$st = 0;
			$warehouse = 0;
			$welding = 0;
			$fg = 0;

			$row_8190 = true;
			$row_8191 = true;
			$row_assembly = true;
			$row_ei = true;
			$row_pp = true;
			$row_st = true;
			$row_warehouse = true;
			$row_welding = true;
			$row_fg = true;

			foreach ($variances as $tr) {
				if($tr->plnt == '8190') {
					$_8190++;
				}else if($tr->plnt == '8191'){
					$_8191++;
				}

				if($tr->group == 'ASSEMBLY') {
					$assembly++;
				}elseif ($tr->group == 'EI') {
					$ei++;
				}elseif ($tr->group == 'PP') {
					$pp++;
				}elseif ($tr->group == 'ST') {
					$st++;
				}elseif ($tr->group == 'WAREHOUSE') {
					$warehouse++;
				}elseif ($tr->group == 'WELDING') {
					$welding++;
				}elseif ($tr->group == 'FINISHED GOODS') {
					$fg++;
				}	
			}

			@endphp


			@foreach($variances as $tr)
			@if($tr->group == 'ASSEMBLY')
			<tr>
				@if($row_8190)
				{{-- <td rowspan="{{ $_8190 }}">8190</td> --}}
				<td rowspan="7">8190</td>
				@php $row_8190 = false; @endphp
				@endif

				@if($row_assembly)
				<td rowspan="{{ $assembly }}">{{ $tr->group }}</td>
				@php $row_assembly = false; @endphp
				@endif

				<td>{{ $tr->location }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_pi_amt, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_book_amt, 2) }}</td>
				
				<td style="text-align: right;">
					@php
					if($tr->sumof_diff_amt < 0){
						echo '(' . abs(round($tr->sumof_diff_amt, 2)) . ')';
					}else{
						echo round($tr->sumof_diff_amt, 2);
					}					
					@endphp
				</td>
				<td style="text-align: right;">{{ round($tr->sumof_var_amt_min, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_var_amt_plus, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_var_amt_abs, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->percentage,2) }}%</td>
			</tr>
			@endif
			@endforeach


			{{-- @foreach($variances as $tr)
			@if($tr->group == 'EI')
			<tr>
				@if($row_ei)
				<td rowspan="{{ $ei }}">{{ $tr->group }}</td>
				@php $row_ei = false; @endphp
				@endif

				<td>{{ $tr->location }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_pi_amt, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_book_amt, 2) }}</td>
				
				<td style="text-align: right;">
					@php
					if($tr->sumof_diff_amt < 0){
						echo '(' . abs(round($tr->sumof_diff_amt, 2)) . ')';
					}else{
						echo round($tr->sumof_diff_amt, 2);
					}					
					@endphp
				</td>
				<td style="text-align: right;">{{ round($tr->sumof_var_amt_min, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_var_amt_plus, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_var_amt_abs, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->percentage,2) }}%</td>
			</tr>
			@endif
			@endforeach

			@foreach($variances as $tr)
			@if($tr->group == 'PP')
			<tr>
				@if($row_pp)
				<td rowspan="{{ $pp }}">{{ $tr->group }}</td>
				@php $row_pp = false; @endphp
				@endif

				<td>{{ $tr->location }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_pi_amt, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_book_amt, 2) }}</td>
				
				<td style="text-align: right;">
					@php
					if($tr->sumof_diff_amt < 0){
						echo '(' . abs(round($tr->sumof_diff_amt, 2)) . ')';
					}else{
						echo round($tr->sumof_diff_amt, 2);
					}					
					@endphp
				</td>
				<td style="text-align: right;">{{ round($tr->sumof_var_amt_min, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_var_amt_plus, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_var_amt_abs, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->percentage,2) }}%</td>
			</tr>
			@endif
			@endforeach

			@foreach($variances as $tr)
			@if($tr->group == 'ST')
			<tr>
				@if($row_st)
				<td rowspan="{{ $st }}">{{ $tr->group }}</td>
				@php $row_st = false; @endphp
				@endif

				<td>{{ $tr->location }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_pi_amt, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_book_amt, 2) }}</td>
				
				<td style="text-align: right;">
					@php
					if($tr->sumof_diff_amt < 0){
						echo '(' . abs(round($tr->sumof_diff_amt, 2)) . ')';
					}else{
						echo round($tr->sumof_diff_amt, 2);
					}					
					@endphp
				</td>
				<td style="text-align: right;">{{ round($tr->sumof_var_amt_min, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_var_amt_plus, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_var_amt_abs, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->percentage,2) }}%</td>
			</tr>
			@endif
			@endforeach

			@foreach($variances as $tr)
			@if($tr->group == 'WAREHOUSE')
			<tr>
				@if($row_warehouse)
				<td rowspan="{{ $warehouse }}">{{ $tr->group }}</td>
				@php $row_warehouse = false; @endphp
				@endif

				<td>{{ $tr->location }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_pi_amt, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_book_amt, 2) }}</td>
				
				<td style="text-align: right;">
					@php
					if($tr->sumof_diff_amt < 0){
						echo '(' . abs(round($tr->sumof_diff_amt, 2)) . ')';
					}else{
						echo round($tr->sumof_diff_amt, 2);
					}					
					@endphp
				</td>
				<td style="text-align: right;">{{ round($tr->sumof_var_amt_min, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_var_amt_plus, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_var_amt_abs, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->percentage,2) }}%</td>
			</tr>
			@endif
			@endforeach

			@foreach($variances as $tr)
			@if($tr->group == 'WELDING')
			<tr>
				@if($row_welding)
				<td rowspan="{{ $welding }}">{{ $tr->group }}</td>
				@php $row_welding = false; @endphp
				@endif

				<td>{{ $tr->location }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_pi_amt, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_book_amt, 2) }}</td>
				
				<td style="text-align: right;">
					@php
					if($tr->sumof_diff_amt < 0){
						echo '(' . abs(round($tr->sumof_diff_amt, 2)) . ')';
					}else{
						echo round($tr->sumof_diff_amt, 2);
					}					
					@endphp
				</td>
				<td style="text-align: right;">{{ round($tr->sumof_var_amt_min, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_var_amt_plus, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_var_amt_abs, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->percentage,2) }}%</td>
			</tr>
			@endif
			@endforeach

			@foreach($variances as $tr)
			@if($tr->group == 'FINISHED GOODS')
			<tr>
				@if($row_8191)
				<td rowspan="{{ $_8191 }}">8191</td>
				@php $row_8191 = false; @endphp
				@endif

				@if($row_fg)
				<td rowspan="{{ $fg }}">{{ $tr->group }}</td>
				@php $row_fg = false; @endphp
				@endif

				<td>{{ $tr->location }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_pi_amt, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_book_amt, 2) }}</td>
				
				<td style="text-align: right;">
					@php
					if($tr->sumof_diff_amt < 0){
						echo '(' . abs(round($tr->sumof_diff_amt, 2)) . ')';
					}else{
						echo round($tr->sumof_diff_amt, 2);
					}					
					@endphp
				</td>
				<td style="text-align: right;">{{ round($tr->sumof_var_amt_min, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_var_amt_plus, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->sumof_var_amt_abs, 2) }}</td>
				<td style="text-align: right;">{{ round($tr->percentage,2) }}%</td>
			</tr>
			@endif
			@endforeach --}}


			<tr>
				<td colspan="10"></td>
			</tr>
			
			<tr>
				<td colspan="5" rowspan="3" style="border-top: 0px;"></td>
				<td>GM Production</td>
				<td>DGM Production</td>
				<td>Manager PC</td>
				<td>Prepared by</td>
				<td rowspan="3" style="border-top: 0px;"></td>
			</tr>

			<tr>
				<td><br><br><br></td>
				<td><br><br><br></td>
				<td><br><br><br></td>
				<td><br><br><br></td>
			</tr>
			
			<tr>
				<td style="vertical-align: middle;">Yukitaka Hayakawa</td>
				<td style="vertical-align: middle;">Budhi Apriyanto</td>
				<td style="vertical-align: middle;">Mei Rahayu</td>
				<td style="vertical-align: middle;">Silvy Firliany</td>
			</tr>

		</tbody>
	</table>
</body>
</html>