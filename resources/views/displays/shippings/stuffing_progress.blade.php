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
		font-weight: bold;
		font-size: 38px;
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
				<thead style="background-color: rgb(112, 112, 112); color: rgb(255,255,2555); font-size: 30px;">
					<tr>
						<th style="width: 2%;">Status</th>
						<th style="width: 1%;">Cont. ID</th>
						<th style="width: 2%;">Destination</th>
						<th style="width: 1%;">By</th>
						<th style="width: 2%;">Plan</th>
						<th style="width: 2%;">Actual</th>
						<th style="width: 5%;">Progress</th>
						<th style="width: 1%;">Start</th>
						<th style="width: 1%;">Finish</th>
					</tr>
				</thead>
				<tbody id="stuffingTableBody" style="font-size: 30px">
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
						<th style="width: 1%;">TRUCK</th>
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
				$('#stuffingTableBody').html("");
				var stuffingTableBody = "";

				$.each(result.stuffing_progress, function(index, value){
					var status = "";
					if(value.status == 1){
						status = "DEPARTED";
						size=30;
						size2=35;
						style = "style='background-color:rgb(6, 115, 82); color:white; font-size:"+size+"px'";
					}
					else if(value.total_actual > 0){
						status = "LOADING";
						size=50;
						size2=35;
						style = "style='background-color:yellow; color:rgb(242, 81, 22); font-size:"+size+"px'";
					}
					else{
						status = "-";					
						style = "";
						size=30;
						size2=35;
					}
					var progress = ((value.total_actual/value.total_plan)*100).toFixed(2)+'%';

					if(((value.total_actual/value.total_plan)*100).toFixed(2) >= 100) var kelas = "progress-bar-success"; else if(((value.total_actual/value.total_plan)*100).toFixed(2) >= 50) var kelas = "progress-bar-warning"; else var kelas = "progress-bar-danger";

					stuffingTableBody += "<tr "+style+">";
					stuffingTableBody += "<td>"+status+"</td>";
					stuffingTableBody += "<td>"+value.id_checkSheet+"</td>";
					stuffingTableBody += "<td>"+value.destination+"</td>";
					stuffingTableBody += "<td>"+value.shipment_condition_name+"</td>";
					stuffingTableBody += "<td>"+value.total_plan+"</td>";
					stuffingTableBody += "<td>"+value.total_actual+"</td>";
					stuffingTableBody += "<td>";
					stuffingTableBody += "<div class='progress active' style='height: "+size2+"px; margin-top:0;'>";
					stuffingTableBody += "<div class='progress-bar "+kelas+" progress-bar-striped' role='progressbar' aria-valuemin='0' aria-valuemax='100' style='width: "+progress+"; line-height: "+size2+"px;'>";
					stuffingTableBody += "<span style='font-weight: bold; font-size: "+size2+"px; color: black; vertical-align: middle;'>"+progress+"</span>";
					stuffingTableBody += "</div>";
					stuffingTableBody += "</div>";
					stuffingTableBody += "</td>";
					stuffingTableBody += "<td>"+value.started_at+"</td>";
					stuffingTableBody += "<td>"+value.finished_at+"</td>";
					stuffingTableBody += "</tr>";
				});
				$('#stuffingTableBody').append(stuffingTableBody);

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
					$('#stuffingTableBody').append(stuffingTableBody);
				}
			}
			else{
				alert('Attempt to retrieve data failed.');
			}
		});
	}
</script>
@endsection