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
		height: 40px;
		background-color: rgba(0,0,0,0);
		border: 2px solid white;
	}
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12">
			<div class="form-group">
				<label class="col-sm-1" style="font-size: 25px">DATE :</label>

				<div class="col-sm-2">
					<input type="text" class="form-control datepicker" id="date" placeholder="Enter Date . . ." style="background-color: rgba(0,0,0,0); color: white;font-size: 25px; text-align: center" onchange="fillTable()">
				</div>
			</div>

			<table id="stuffingTable" class="table table-bordered">
				<thead style="background-color: rgb(112, 112, 112); color: rgb(255,255,2555); font-size: 24px;">
					<tr>
						{{-- <th style="width: 2%;">Status</th> --}}
						<th style="width: 1%;">#</th>
						<th style="width: 2%;">Status</th>
						<th style="width: 1%;">Container ID</th>
						<th style="width: 5%;">Port<br>(港先)</th>
						<th style="width: 5%;">Loading Time<br>(Target Max.60 Minutes)</th>
						<th style="width: 3%;">Start</th>
						<th style="width: 3%;">End</th>
					</tr>
				</thead>
				<tbody id="stuffingTableBody" style="font-size: 26px">
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
		// setInterval(fillTable, 10000);

		$('#date').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd"
		});

	});

	$.time = function(dateObject) {
		var d = new Date(dateObject);

		var h = d.getHours();
		var i = d.getMinutes() + 1;
		var s = d.getSeconds();

		var time = h + ":" + i + ":" + s;

		return time;
	};

	function fillTable(){
		var data = {
			date: $("#date").val()
		}

		$.get('{{ url("fetch/display/stuffing_progress") }}', data, function(result, status, xhr){

			if(result.status){
				var stuffingTableBody = "";
				$('#stuffingTableBody').html("");

				$.each(result.stuffing_progress, function(index, value){
					var dif = "";
					var start = "-";
					var finish = "-";
					var cls = "";
					var cls2 = "";
					var reason = "";

					if (value.stats == "LOADING") {
						
						var d2 = new Date();
						
						var d1 = new Date(value.start_stuffing);
						dif = diff_minutes(d1, d2);
						cls = "active";
						cls2 = "progress-bar-striped";

						start = d1.getHours() + ":" + ('0' + d1.getMinutes()).slice(-2);
					} else if (value.stats == "DEPARTED") {
						var d2 = new Date(value.finish_stuffing);
						var d1 = new Date(value.start_stuffing);
						dif = diff_minutes(d1, d2);

						start = d1.getHours() + ":" + ('0' + d1.getMinutes()).slice(-2);
						finish = d2.getHours() + ":" + ('0' + d1.getMinutes()).slice(-2);
					}

					var progress = dif / 60 * 100;
					var style = 'green';
					var st = '';

					if (progress > 100) { 
						progress = 100; style = 'red'; 

						if (value.reason) {
							reason = "* "+value.reason;
						}
					}

					if(progress == 0) {dif = "";}

					stuffingTableBody += "<tr>";
					stuffingTableBody += "<td>"+parseInt(index+1)+"</td>";
					stuffingTableBody += "<td>"+value.stats+"</td>";
					stuffingTableBody += "<td>"+value.id_checkSheet.substr(2,7)+"</td>";
					stuffingTableBody += "<td>"+value.destination+"</td>";
					stuffingTableBody += '<td style="line-height:1"><div class="progress '+cls+'">';
					stuffingTableBody += '<div class="progress-bar progress-bar-'+style+' '+cls2+'" role="progressbar" aria-valuenow="'+dif+'" aria-valuemin="0" aria-valuemax="60" style="width: '+progress+'%"; >';
					stuffingTableBody += "<span style='line-height:36px; font-size:30px';>"+dif+"</span>";
					stuffingTableBody += '</div></div><span style="color:#dd4b39; font-size:20px; font-weight: normal;">'+reason+'</span></td>';
					stuffingTableBody += "<td>"+start+"</td>";
					stuffingTableBody += "<td>"+finish+"</td>";
					stuffingTableBody += "</tr>";
				});

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

	function diff_minutes(dt2, dt1) 
	{
		var diff =(dt2.getTime() - dt1.getTime()) / 1000;
		diff /= 60;
		return Math.abs(Math.round(diff));
	}
</script>
@endsection