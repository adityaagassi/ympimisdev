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
				<thead style="background-color: rgb(112, 112, 112); color: rgb(255,255,2555); font-size: 24px;">
					<tr>
						{{-- <th style="width: 2%;">Status</th> --}}
						<th style="width: 1%;">#</th>
						<th style="width: 2%;">Status</th>
						<th style="width: 2%;">Progress<br>(進捗)</th>
						<th style="width: 1%;">Container ID</th>
						<th style="width: 5%;">Port<br>(港先)</th>
						<th style="width: 3%;">Shipping Qty<br>(出荷予定台数)</th>
						<th style="width: 3%;">Loading Qty<br>(積み込み台数)</th>
						<th style="width: 1%;">Time</th>
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
						<th style="width: 1%;" rowspan="2">Shipping Qty<br>(出荷予定台数)</th>
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

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}

	$.time = function(dateObject) {
		var d = new Date(dateObject);

		var h = addZero(d.getHours());
		var i = addZero(d.getMinutes() + 1);
		var s = addZero(d.getSeconds());

		var time = h + ":" + i + ":" + s;

		return time;
	};

	function fillTable(){
		var data = {
			date: ""
		}
		
		$.get('{{ url("fetch/display/stuffing_progress") }}', data, function(result, status, xhr){

			if(result.status){
				var stuffingTableBody = "";
				$('#stuffingTableBody').html("");

				$.each(result.stuffing_progress, function(index, value){
					var status = "";
					var finished = "-";
					if(value.stats == "DEPARTED"){
						size=22;
						style = "style='background-color:rgb(6, 115, 82); color:white; font-size:"+size+"px'";
						finished = value.finished_at;
					}
					if(value.stats == "LOADING"){
						size=48;
						style = "style='background-color:yellow; color: #cc1305; font-size:"+size+"px'";
						finished = "-";
					}
					if(value.stats == "-"){				
						size=28;
						style = "style='background-color:white; color: black; font-size:"+size+"px'";
						finished = "-";
					}
					var progress = ((value.total_actual/value.total_plan)*100).toFixed(2)+'%';

					stuffingTableBody += "<tr "+style+">";
					stuffingTableBody += "<td>"+parseInt(index+1)+"</td>";
					stuffingTableBody += "<td>"+value.stats+"</td>";
					stuffingTableBody += "<td>"+progress+"</td>";
					stuffingTableBody += "<td>"+value.id_checkSheet.substr(2,7)+"</td>";
					stuffingTableBody += "<td>"+value.destination+"</td>";
					stuffingTableBody += "<td>"+value.total_plan+"</td>";
					stuffingTableBody += "<td>"+value.total_actual+"</td>";
					if(value.start_stuffing != null){
						stuffingTableBody += "<td>"+$.time(Date.parse(value.start_stuffing))+"</td>";
					}
					else{
						stuffingTableBody += "<td>-</td>";						
					}
					stuffingTableBody += "</tr>";

				});

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