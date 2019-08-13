@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	table.table-bordered{
		border:1px solid rgb(150,150,150);
	}
	table.table-bordered > thead > tr > th{
		border:1px solid rgb(150,150,150);
		text-align: center;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;
		text-align: center;
		padding:5px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(150,150,150);
		padding:0;
	}
	.content{
		color: white;
	}
	.progress {
		height: 20px;
	}
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12">
			<table id="stuffingTable" class="table table-bordered">
				<thead style="background-color: rgb(112, 112, 112); color: rgb(255,255,2555); font-size: 28px;">
					<tr>
						{{-- <th style="width: 2%;">Status</th> --}}
						<th style="width: 1%;">ID</th>
						<th style="width: 2%;">Destination</th>
						<th style="width: 2%;">By</th>
						<th style="width: 2%;">Plan</th>
						<th style="width: 2%;">Actual</th>
						<th style="width: 7%;">Progress</th>
						<th style="width: 1%;">Start</th>
						<th style="width: 1%;">Finish</th>
						<th style="width: 3%;">Note</th>
					</tr>
				</thead>
				<tbody id="stuffingTableBody" style="font-size: 26px">
				</tbody>
				<tfoot>
				</tfoot>
			</table>
			<table id="resumeTable" class="table table-bordered pull-right" style="width: 45%;">
				<thead style="background-color: rgb(112, 112, 112); color: rgb(255,255,2555); font-size: 20px;">
					<tr>
						<th style="width: 1%;" rowspan="2">Next Shipment<br>ETD YMPI</th>
						<th style="width: 1%;" colspan="3">Total Container</th>
						<th style="width: 1%;" rowspan="2">Shipping Qty</th>
					</tr>
					<tr>
						<th style="width: 1%;">SEA</th>
						<th style="width: 1%;">AIR</th>
						<th style="width: 1%;">LAND</th>
					</tr>
				</thead>
				<tbody id="resumeTableBody" style="font-size: 20px">
				</tbody>
				<tfoot>
				</tfoot>
			</table>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		fillTable();
		setInterval(fillTable, 10000);
	});

	function fillTable(){
		$.get('{{ url("fetch/display/stuffing_progress") }}', function(result, status, xhr){
			if(result.status){
				var stuffingTableBody = "";
				$('#stuffingTableBody').html("");


				if(result.stuffing_progress.length != 0){
					stuffingTableBody += '<tr style="background-color: rgb(255,255,204); color: red;">';
					stuffingTableBody += '<td colspan="9" style="padding: 0;">LOADING</td>';
					stuffingTableBody += '</tr>';
				}
				$.each(result.stuffing_progress, function(index, value){
					if(value.stats == 'LOADING'){
						var progress = ((value.total_actual/value.total_plan)*100).toFixed(2)+'%';
						stuffingTableBody += '<tr style="background-color: rgb(255,255,204); color: red;">';
						stuffingTableBody += '<td>'+value.id_checkSheet.substr(2,7)+'</td>';
						stuffingTableBody += '<td>'+value.destination+'</td>';
						stuffingTableBody += '<td>'+value.shipment_condition_name+'</td>';
						stuffingTableBody += '<td>'+value.total_plan+'</td>';
						stuffingTableBody += '<td>'+value.total_actual+'</td>';
						stuffingTableBody += '<td>';
						stuffingTableBody += '<div class="progress active" style="height: 35px;">';
						stuffingTableBody += '<div class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar" aria-valuenow="'+progress+'" aria-valuemin="0" aria-valuemax="100" style="width: '+progress+'; font-size: 30px; font-weight: bold; line-height: 32px;">'+progress+'';
						stuffingTableBody += '</div>';
						stuffingTableBody += '</div>';
						stuffingTableBody += '</td>';
						stuffingTableBody += '<td>'+value.started_at+'</td>';
						stuffingTableBody += '<td>-</td>';
						var reason = '-';
						if(value.reason != null){
							var reason = value.reason;
						}
						stuffingTableBody += '<td style="font-size: 16px; padding: 0;">'+reason+'</td>';
						stuffingTableBody += '</tr>';
					}
				});

				if(result.stuffing_progress.length != 0){
					stuffingTableBody += '<tr style="background-color: rgb(255,204,255); color: black;">';
					stuffingTableBody += '<td colspan="9" style="padding: 0;">INSPECTION</td>';
					stuffingTableBody += '</tr>';
				}
				$.each(result.stuffing_progress, function(index, value){
					if(value.stats == 'INSPECTION'){
						var progress = ((value.total_actual/value.total_plan)*100).toFixed(2)+'%';
						stuffingTableBody += '<tr style="background-color: rgb(255,204,255); color: black;">';
						stuffingTableBody += '<td>'+value.id_checkSheet.substr(2,7)+'</td>';
						stuffingTableBody += '<td>'+value.destination+'</td>';
						stuffingTableBody += '<td>'+value.shipment_condition_name+'</td>';
						stuffingTableBody += '<td>'+value.total_plan+'</td>';
						stuffingTableBody += '<td>'+value.total_actual+'</td>';
						stuffingTableBody += '<td>';
						stuffingTableBody += '<div class="progress active" style="height: 35px;">';
						stuffingTableBody += '<div class="progress-bar progress-bar-yellow progress-bar-striped" role="progressbar" aria-valuenow="'+progress+'" aria-valuemin="0" aria-valuemax="100" style="width: '+progress+'; font-size: 30px; font-weight: bold; line-height: 32px;">'+progress+'';
						stuffingTableBody += '</div>';
						stuffingTableBody += '</div>';
						stuffingTableBody += '</td>';
						stuffingTableBody += '<td>'+value.started_at+'</td>';
						stuffingTableBody += '<td>-</td>';
						var reason = '-';
						if(value.reason != null){
							var reason = value.reason;
						}
						stuffingTableBody += '<td style="font-size: 16px; padding: 0;">'+reason+'</td>';
						stuffingTableBody += '</tr>';
					}
				});

				if(result.stuffing_progress.length != 0){
					stuffingTableBody += '<tr>';
					stuffingTableBody += '<td colspan="9" style="padding: 0;">WAITING</td>';
					stuffingTableBody += '</tr>';
				}
				$.each(result.stuffing_progress, function(index, value){
					if(value.stats == '-'){
						var progress = ((value.total_actual/value.total_plan)*100).toFixed(2)+'%';
						stuffingTableBody += '<tr>';
						stuffingTableBody += '<td>'+value.id_checkSheet.substr(2,7)+'</td>';
						stuffingTableBody += '<td>'+value.destination+'</td>';
						stuffingTableBody += '<td>'+value.shipment_condition_name+'</td>';
						stuffingTableBody += '<td>'+value.total_plan+'</td>';
						stuffingTableBody += '<td>'+value.total_actual+'</td>';
						stuffingTableBody += '<td>'+progress+'</td>';
						stuffingTableBody += '<td>'+value.started_at+'</td>';
						stuffingTableBody += '<td>-</td>';
						var reason = '-';
						if(value.reason != null){
							var reason = value.reason;
						}
						stuffingTableBody += '<td style="font-size: 16px; padding: 0;">'+reason+'</td>';
						stuffingTableBody += '</tr>';
					}
				});

				if(result.stuffing_progress.length != 0){
					stuffingTableBody += '<tr style="background-color: rgb(204,255,255); color: green;">';
					stuffingTableBody += '<td colspan="9" style="padding: 0;">DEPARTED</td>';
					stuffingTableBody += '</tr>';
				}
				$.each(result.stuffing_progress, function(index, value){
					if(value.stats == 'DEPARTED'){
						var progress = ((value.total_actual/value.total_plan)*100).toFixed(2)+'%';
						stuffingTableBody += '<tr style="background-color: rgb(204,255,255); color: green;">';
						stuffingTableBody += '<td>'+value.id_checkSheet.substr(2,7)+'</td>';
						stuffingTableBody += '<td>'+value.destination+'</td>';
						stuffingTableBody += '<td>'+value.shipment_condition_name+'</td>';
						stuffingTableBody += '<td>'+value.total_plan+'</td>';
						stuffingTableBody += '<td>'+value.total_actual+'</td>';
						stuffingTableBody += '<td>';
						stuffingTableBody += '<div class="progress" style="height: 35px;">';
						stuffingTableBody += '<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="'+progress+'" aria-valuemin="0" aria-valuemax="100" style="width: '+progress+'; font-size: 30px; font-weight: bold; line-height: 32px;">'+progress+'';
						stuffingTableBody += '</div>';
						stuffingTableBody += '</div>';
						stuffingTableBody += '</td>';
						stuffingTableBody += '<td>'+value.started_at+'</td>';
						stuffingTableBody += '<td>'+value.finished_at+'</td>';
						var reason = '-';
						if(value.reason != null){
							var reason = value.reason;
						}
						stuffingTableBody += '<td style="font-size: 16px; padding: 0;">'+reason+'</td>';
						stuffingTableBody += '</tr>';
					}
				});

				// $('#stuffingTableBody').append(stuffingTableBody);

				$('#resumeTableBody').html("");
				var resumeTableBody = "";

				$.each(result.stuffing_resume, function(index, value){
					resumeTableBody += "<tr>";
					resumeTableBody += "<td>"+value.stuffing_date+"</td>";
					resumeTableBody += "<td>"+value.sea+"</td>";
					resumeTableBody += "<td>"+value.air+"</td>";
					resumeTableBody += "<td>"+value.truck+"</td>";
					resumeTableBody += "<td>"+value.total_plan+"</td>";
					resumeTableBody += "</tr>";
				});
				$('#resumeTableBody').append(resumeTableBody);

				if(result.stuffing_progress.length == 0){
					stuffingTableBody += "<tr>";
					stuffingTableBody += "<td colspan='9'>There is no shipping schedule today</td>";
					stuffingTableBody += "</tr>";
				}
				$('#stuffingTableBody').append(stuffingTableBody);
			}
			else{
				alert('Attempt to retrieve data failed.');
			}
		});
}
</script>
@endsection