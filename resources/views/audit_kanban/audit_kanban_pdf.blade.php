<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		table, td, th {
			border: 1px solid black;
		}

		table {
			width: 100%;
			border-collapse: collapse;
		}

	</style>
</head>
<body style="font-family: calibri;">
	<div>
		<center>
			<span style="font-weight: bold; font-size: 20px;">
				Check List Audit Kanban Daily<br>
				かんばん監査のチェックリスト
			</span>
		</center>
		<br>
		<table style="border: 0px; width: 50%;">
			<tr>
				<th style="text-align: left; border: 0px; width: 20%;"><span>Month 月</span></th>
				<th style="text-align: right; border: 0px; width: 1%;"><span>:</span></th>
				<th style="text-align: right; border: 0px; width: 79%; text-align: left;"><span>&nbsp;{{ date('Y', strtotime($month)) }}年 {{ date('m', strtotime($month)) }}月</span></th>
			</tr>
			<tr>
				<th style="text-align: left; border: 0px; width: 20%;"><span>Area　エリア</span></th>
				<th style="text-align: right; border: 0px; width: 1%"><span>: </span></th>
				<th style="text-align: right; border: 0px; width: 79%; text-align: left;"><span>&nbsp;{{ $activity->remark }}</span></th>
			</tr>
			<tr>
				<th style="text-align: left; border: 0px; width: 20%;"><span>Nama KK　名称</span></th>
				<th style="text-align: right; border: 0px; width: 1%"><span>:</span></th>
				<th style="text-align: right; border: 0px; width: 79%; text-align: left;"><span>&nbsp;{{ $activity->leader_dept }}</span></th>
			</tr>
		</table>
		<br>
	</div>
	<table>
		<thead style="height: 50px;">
			<tr>
				<th>NO<br>番</th>
				<th width="13%">Poin Audit<br>監査箇所</th>
				@for($j = 0; $j < count($weekly_calendar); $j++)
				@if($weekly_calendar[$j]->remark == 'H')
				<th style="background-color: rgba(80,80,80,0.3)">{{ date('d', strtotime($weekly_calendar[$j]->week_date)) }}</th>
				@else
				<th>{{ date('d', strtotime($weekly_calendar[$j]->week_date)) }}</th>
				@endif
				@endfor
			</tr>
		</tead>
		<tbody style="font-size: 12px;">
			@php $array = 1; @endphp

			@for($i = 0; $i < count($point_check); $i++)
			<tr style="height: 45px;">
				<td style="padding-right: 2px; padding-left: 2px; width: 1%; text-align: center;">{{ $point_check[$i]->point_check_index }}</td>
				<td style="padding-right: 2px; padding-left: 2px; width: 7%; ">{{ $point_check[$i]->point_check_name }}<br>{{ $point_check[$i]->point_check_jp }}</td>

				@for($j = 0; $j < count($weekly_calendar); $j++)
				@php $is_fill = false; @endphp
				@for($k = 0; $k < count($audit_kanban); $k++)
				@if($audit_kanban[$k]->check_date == $weekly_calendar[$j]->week_date && $audit_kanban[$k]->point_check_id == $point_check[$i]->point_check_index)
				@php $is_fill = true; @endphp
				@if($weekly_calendar[$j]->remark == 'H')
				<td style="background-color: rgba(80,80,80,0.3); width: 1%; text-align: center;"></td>
				@else
				@if($audit_kanban[$k]->condition == 'OK')
				<td style="width: 1%; text-align: center;">&#9711;</td>
				@elseif($audit_kanban[$k]->condition == 'NG')
				<td style="width: 1%; text-align: center;">&#9747;</td>
				@else
				<td style="width: 1%; text-align: center;">&#9866;</td>
				@endif
				@endif
				@endif
				@endfor
				@if(!$is_fill)
				@if($weekly_calendar[$j]->remark == 'H')
				<td style="background-color: rgba(80,80,80,0.3); width: 1%; text-align: center;"></td>
				@else
				@php $quantity = false; @endphp
				<td style="width: 1%; text-align: center;"></td>
				@endif	
				@endif
				@php
				@endphp
				@endfor
			</tr>
			@endfor

			<tr style="font-size: 16px; font-weight: bold;">
				<td colspan="2" style="padding : 8px; width: 1%; text-align: center;">Total Skor</td>
				@for($j = 0; $j < count($percentage); $j++)
				@if($percentage[$j]->remark == 'H')
				<th style="background-color: rgba(80,80,80,0.3)"></th>
				@else
				@if($percentage[$j]->audit > 0)
				<th style="width: 1%; text-align: center;">{{ $percentage[$j]->audit }}</th>
				@else
				<th style="width: 1%; text-align: center;"></th>
				@endif
				@endif
				@endfor				
			</tr>
			<tr style="font-size: 16px; font-weight: bold;">
				<td colspan="2" style="padding : 8px; width: 1%; text-align: center;">Persentase Kesesuaian</td>
				@for($j = 0; $j < count($percentage); $j++)
				@if($percentage[$j]->remark == 'H')
				<th style="background-color: rgba(80,80,80,0.3)"></th>
				@else
				@if($percentage[$j]->percentage > 0)
				<th style="width: 1%; text-align: center;">{{ round($percentage[$j]->percentage) }}%</th>
				@else
				<th style="width: 1%; text-align: center;"></th>
				@endif
				@endif
				@endfor
			</tr>
		</tbody>
	</table>
	
</body>
</html>