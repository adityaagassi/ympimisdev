@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	#stuffingTable tbody > tr {
		cursor: pointer;
	}
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
		padding:2px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
		padding:0;
		vertical-align: middle;
		text-align: center;
	}
	.content{
		color: white;
		font-weight: bold;
	}
	.progress {
		background-color: rgba(0,0,0,0);
	}
	.aa {
  	text-decoration-color: pink !important;
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
				<thead style="background-color: rgb(112, 112, 112); color: rgb(255,255,2555); font-size: 20px;">
					<tr>
						{{-- <th style="width: 2%;">Status</th> --}}
						<th style="width: 1%;" rowspan="2">#</th>
						<th style="width: 2%;" rowspan="2">Status</th>
						<th style="width: 2%;" rowspan="2">Progress<br>(進捗)</th>
						<th style="width: 1%;" rowspan="2">Container ID</th>
						<th style="width: 4%;" rowspan="2">Port<br>(港先)</th>
						<th style="width: 3%;" rowspan="2">Shipping Qty<br>(出荷予定台数)</th>
						<th style="width: 3%;" rowspan="2">Loading Qty<br>(積み込み台数)</th>
						<th style="width: 10%;" rowspan="2">Loading Time (Target Max.60 Minutes)</th>
						<th style="width: 2%;" colspan="3">Loading</th>
					</tr>
					<tr>
						<th style="width: 1%">Start</th>
						<th style="width: 1%">End</th>
						<th style="width: 1%">Departure</th>
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

	<!-- start modal -->
	<div class="modal fade" id="myModal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 style="float: right;" id="modal-title"></h4>
					<h4 class="modal-title" style="color: black;"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<table id="tabel_detail" class="table table-striped table-bordered" style="width: 100%;"> 
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>Container ID</th>
										<th>Invoice</th>
										<th>GMC</th>
										<th>Goods</th>
										<th>Plan Qty</th>
										<th>Act Qty</th>
										<th>Diff</th>
										<th>Persen</th>
									</tr>
								</thead>
								<tbody id="detailTableBody" style="color: black;">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);color: black;">
									<tr>
										<th colspan="4">Total:</th>
										<th id="total_plan"></th>
										<th id="total_actual"></th>
										<th id="total_diff"></th>
										<th id="total_persen"></th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- end modal -->

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

		$('#date').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});
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
		var i = addZero(d.getMinutes());

		var time = h + ":" + i;

		return time;
	};

	function showModal(container_id){
		var data = {
			id: container_id
		}

		$('#myModal').modal('show');

		$.get('{{ url("fetch/display/stuffing_detail") }}', data, function(result, status, xhr){
			if(result.status){
				$('#detailTableBody').append().empty();
				$('#total_plan').append().empty();
				$('#total_actual').append().empty();
				$('#total_diff').append().empty();
				$('#total_persen').append().empty();

				var detailTableBody = "";
				// var detailTableFoot = "";
				var sum_plan = 0;
				var sum_act = 0;
				var sum_diff = 0;
				for (var i = 0; i < result.stuffing_detail.length; i++) {
					detailTableBody += "<tr>";
					detailTableBody += "<td>"+result.stuffing_detail[i].id_checkSheet+"</td>";
					detailTableBody += "<td>"+result.stuffing_detail[i].invoice+"</td>";
					detailTableBody += "<td>"+result.stuffing_detail[i].gmc+"</td>";
					detailTableBody += "<td>"+result.stuffing_detail[i].goods+"</td>";
					detailTableBody += "<td>"+result.stuffing_detail[i].plan_loading+"</td>";
					detailTableBody += "<td>"+result.stuffing_detail[i].actual_loading+"</td>";
					var diff = parseInt(result.stuffing_detail[i].plan_loading) - parseInt(result.stuffing_detail[i].actual_loading);
					var persen = (parseInt(result.stuffing_detail[i].actual_loading) / parseInt(result.stuffing_detail[i].plan_loading))*100;
					detailTableBody += "<td>"+diff+"</td>";
					detailTableBody += "<td>"+persen.toFixed(2)+" %</td>";
					detailTableBody += "</tr>";
					sum_plan += parseInt(result.stuffing_detail[i].plan_loading);
					sum_act += parseInt(result.stuffing_detail[i].actual_loading);
					sum_diff += diff;
				}
				$('#detailTableBody').append(detailTableBody);
				$('#total_plan').append(sum_plan);
				$('#total_actual').append(sum_act);
				$('#total_diff').append(sum_diff);
				$('#total_persen').append(((sum_act/sum_plan)*100).toFixed(2)+" %");
			}
		});
	}

	function fillTable(){
		var data = {
			date: $("#date").val()
		}
		
		$.get('{{ url("fetch/display/stuffing_progress") }}', data, function(result, status, xhr){

			if(result.status){
				var stuffingTableBody = "";
				$('#stuffingTableBody').html("");

				$.each(result.stuffing_progress, function(index, value){
					var status = "";
					var start = "-";
					var finish = "-";
					var depart = "-";
					var cls = "";
					var cls2 = "";
					var reason = "";
					var color = "white";

					var garis = ""

					if(value.stats == "DEPARTED" ){
						size=20;
						if (value.deleted_at != "-") {
						
						garis ="style='text-decoration: line-through; text-decoration-thickness: 3px;text-decoration-color: red;'";
						
						}
						style = "style='background-color:rgb(6, 115, 82); color:white; font-size:"+size+"px'";
						

						var d3 = new Date(value.status);
						var d2 = new Date(value.finish_stuffing);
						var d1 = new Date(value.start_stuffing);
						dif = diff_minutes(d1, d2);

						start = d1.getHours() + ":" + ('0' + d1.getMinutes()).slice(-2);
						finish = d2.getHours() + ":" + ('0' + d2.getMinutes()).slice(-2);
						depart = d3.getHours() + ":" + ('0' + d3.getMinutes()).slice(-2);
						rs_color = "#ffff00";
					}
					if(value.stats == "LOADING"){
						cls = "active";
						cls2 = "progress-bar-striped";

						var d2 = new Date();
						if (value.total_plan == value.total_actual) {
							d2 = new Date(value.finish_stuffing);
							cls = "";
							cls2 = "";
							finish = d2.getHours() + ":" + ('0' + d2.getMinutes()).slice(-2);
						}

						var d1 = new Date(value.start_stuffing);
						dif = diff_minutes(d1, d2);

						start = d1.getHours() + ":" + ('0' + d1.getMinutes()).slice(-2);

						size=40;

						if (value.deleted_at != "-") {
						
						garis ="style='text-decoration: line-through; text-decoration-thickness: 3px;text-decoration-color: red;'";
						
						}
						style = "style='background-color:yellow; color: #cc1305; font-size:"+size+"px';";
						
						rs_color = "#dd4b39";
					}
					if(value.stats == "-"){				
						size=25;

						if (value.deleted_at != "-") {
						
						garis ="style='text-decoration: line-through; text-decoration-thickness: 3px;text-decoration-color: red;'";
						
						}
						style = "style='background-color:white; color: black; font-size:"+size+"px';";
					
						rs_color = "#dd4b39";
						dif = 0;
						color = "black";
					}
				

					var progress = ((value.total_actual/value.total_plan)*100).toFixed(2)+'%';

					var style2 = 'green';
					var progress2 = dif / 60 * 100;
					if (progress2 > 100) { 
						progress2 = 100; style2 = 'red'; 

						if (value.reason) {
							reason = "* "+value.reason;
						}
					}

					if(progress2 == 0) {dif = "";}

					stuffingTableBody += "<tr onClick='showModal(\""+value.id_checkSheet+"\")' "+style+">";
					stuffingTableBody += "<td "+garis+">"+parseInt(index+1)+"</td>";
					stuffingTableBody += "<td "+garis+">"+value.stats+"</td>";
					stuffingTableBody += "<td "+garis+">"+progress+"</td>";
					stuffingTableBody += "<td "+garis+">"+value.id_checkSheet.substr(2,7)+"</td>";
					stuffingTableBody += "<td "+garis+">"+value.destination+"</td>";
					stuffingTableBody += "<td "+garis+">"+value.total_plan+"</td>";
					stuffingTableBody += "<td "+garis+">"+value.total_actual+"</td>";
					stuffingTableBody += "<td style='line-height:5px; padding:5px'><div class='progress "+cls+"' style='height:"+size+"px; border: 1px solid "+color+";'>";
					stuffingTableBody += "<div class='progress-bar progress-bar-"+style2+" "+cls2+"' role='progressbar' aria-valuenow='"+dif+"' aria-valuemin='0' aria-valuemax='60' style='width: "+progress2+"%;' >";
					stuffingTableBody += "<span style='line-height:"+(size-2)+"px; font-size:"+size+"px'>"+dif+"</span>";
					stuffingTableBody += "</div></div><span  style='color:"+rs_color+"; font-size:15px; line-height: 80%; font-weight: normal; '>"+reason+"</span></td>";

					stuffingTableBody += "<td "+garis+">"+start+"</td>";		
					stuffingTableBody += "<td "+garis+">"+finish+"</td>";
					stuffingTableBody += "<td "+garis+">"+depart+"</td>";
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
					stuffingTableBody += "<td colspan='10'>There is no shipping schedule today</td>";
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