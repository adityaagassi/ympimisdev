@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	.content{
		color: white;
		font-weight: bold;
	}

	thead>tr>th{
		text-align:center;
		overflow:hidden;
		padding: 3px;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	th:hover {
		overflow: visible;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid white;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		color: black;
		background-color: white;
	}
	table.table-bordered > tbody > tr > th{
		border:1px solid black;
		vertical-align: middle;
		text-align: center;
		padding:2px;
		background-color: white;
		color: black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid white;
		vertical-align: middle;
		padding:2px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid white;
		padding:2px;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	.color {
		-webkit-animation: colors 1s infinite;  /* Safari 4+ */
		-moz-animation: colors 1s infinite;  /* Fx 5+ */
		-o-animation: colors 1s infinite;  /* Opera 12+ */
		animation: colors 1s infinite;  /* IE 10+, Fx 29+ */
	}
	
	@-webkit-keyframes colors {
		0%, 49% {
			background: rgba(0, 0, 0, 0);
			/*opacity: 0;*/
		}
		50%, 100% {
			background-color: #f55656;
		}
	}
	#quantity_kanban, #quantity, #diff {
		font-size: 2vw;
	}
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0; overflow-y:hidden; overflow-x:scroll;">
	<div class="row">
		<div class="col-xs-12" style="margin-top: 5px;">
			<div id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
			
			<div id="chart"></div>

		
			<table id="assyTable" class="table table-bordered" style="padding: 0px; margin-bottom: 0px;">
				<tr id="modelAll" style="font-size: 1.8vw"></tr>
				{{-- <tr id="quantity_kanban" style="font-size: 1.5vw; border-top: 4px solid #f44336 !important"></tr> --}}
				<tr id="quantity" style="font-size: 1.5vw; border-top: 4px solid #f44336 !important"></tr>
				<tr id="solder" style="font-size: 1.5vw; border-top: 4px solid #f44336 !important"></tr>
				<tr id="cuci" style="font-size: 1.5vw"></tr>
				<tr id="kensa" style="font-size: 1.5vw"></tr>
				<tr id="diff" style="font-size: 1.5vw"></tr>
				<tr id="total" style="font-size: 1.5vw"></tr>				
				<tr id="chart2" style="font-size: 1vw; border-top: 4px solid #f44336 !important"></tr>
			</table>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/highstock.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		drawTable();
		setInterval(drawTable, 2000);
	});

	function getActualFullDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
	}

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}

	function drawTable() {
		var filter = '{{$_GET["filter"]}}';
		var data = {
			origin_group_code:"{{$option}}",
			filter: filter
		}

		$.get('{{ url("fetch/middle/request/") }}', data, function(result, status, xhr){
			if(result.status){
				// $('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');


				$("#modelAll").empty();
				$("#quantity").empty();
				$("#quantity_kanban").empty();
				$("#solder").empty();
				$("#cuci").empty();
				$("#kensa").empty();
				$("#diff").empty();
				$("#total").empty();
				$("#chart2").empty();


				var material_req = [];
				var cat = [];
				var limit = [];
				var chart = "";
				var isi = 0;
				var isi2 = "";
				var kosong = 0;
				var max = 0;

				model = "<th style='width:45px'>#</th>";
				quantity_kanban = "<th>Kanban</th>";
				quantity = "<th>PC(s)</th>";

				solder = "<th>HSA</th>";
				cuci = "<th>Cuci</th>";
				kensa = "<th>Kensa</th>";
				diff = "<th>Stock</th>";
				total = "<th>Total</th>";

				
				chart = "<th style='vertical-align: text-top;'><p><i class='fa fa-fw fa-clock-o'></i> Last Updated: "+ getActualFullDate() +"</p></th>";

				$.each(result.datas, function(index, value){
					if(value.model[0] == 'A'){
						if(value.model == 'A82'){
							model += "<th style='background-color: #e5e5df;'>"+value.model+" "+value.key+"</th>";
						}else{
							model += "<th style='background-color: #ffff66;'>"+value.model+" "+value.key+"</th>";
						}
					}else if(value.model[0] == 'T'){
						model += "<th style='background-color: #1565C0; color: #e5e5df;'>"+value.model+" "+value.key+"</th>";
					}

					if (parseInt(value.inventory_quantity) - value.quantity < 0) {
						color2 = "style='background-color:#eb2b21'";
					} else {
						color2 = "style='background-color:#12b522'";
					}

					sum_total = parseInt(value.solder) + parseInt(value.cuci) + parseInt(value.kensa) + parseInt(value.inventory_quantity);
					if (sum_total - value.quantity < 0) {
						color3 = "style='background-color: rgb(255,204,255); color: #333;'";
					} else {
						color3 = "style='background-color: rgb(204, 255, 255); color: #333;'";
					}

					quantity += "<td>"+value.quantity+"</td>";
					quantity_kanban += "<td>"+value.kanban+"</td>";

					solder += "<td>"+value.solder+"</td>";
					cuci += "<td>"+value.cuci+"</td>";
					kensa += "<td>"+value.kensa+"</td>";
					
					diff += "<td "+color2+">"+parseInt(value.inventory_quantity)+"</td>";
					total += "<td "+color3+">"+sum_total+"</td>";



					limit.push(value.kanban);
				})

				max = (Math.max(...limit));

				$.each(result.datas, function(index, value){

					high = value.kanban / max * 100;

					if (value.kanban >= 4) {
						color = "#e0391f";
					} else if (value.kanban >= 2) {
						color = "#facf23";
					}
					else {
						color = "lime";
					}

					chart += "<td style='height:300px'><div style='height:"+(100 - high)+"%'></div><div style='margin: 10px 3px 0px 3px; background-color: "+color+"; height:"+high+"%; font-size:1.5vw'>"+value.kanban+"</div></td>";

				})

				$("#modelAll").append(model);
				$("#quantity").append(quantity);
				$("#quantity_kanban").append(quantity_kanban);

				$("#solder").append(solder);
				$("#cuci").append(cuci);
				$("#kensa").append(kensa);
				$("#diff").append(diff);
				$("#total").append(total);

				$("#chart2").append(chart);


			}
		})
	}

</script>
@endsection