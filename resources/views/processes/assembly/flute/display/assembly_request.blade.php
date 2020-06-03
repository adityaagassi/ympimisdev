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
				<!-- <tr id="quantity" style="font-size: 1.5vw; border-top: 4px solid #f44336 !important"></tr> -->
				<tr id="stamp" style="font-size: 1.5vw; border-top: 4px solid #f44336 !important"></tr>
				<tr id="perakitan" style="font-size: 1.5vw"></tr>
				<tr id="kariawase" style="font-size: 1.5vw"></tr>
				<tr id="tanpoire" style="font-size: 1.5vw"></tr>
				<tr id="tanpoawase" style="font-size: 1.5vw"></tr>
				<tr id="seasoning" style="font-size: 1.5vw"></tr>
				<tr id="kango" style="font-size: 1.5vw"></tr>
				<tr id="renraku" style="font-size: 1.5vw"></tr>
				<tr id="fukiage1" style="font-size: 1.5vw"></tr>
				<tr id="fukiage2" style="font-size: 1.5vw"></tr>
				<tr id="qa" style="font-size: 1.5vw"></tr>
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
		var data = {
			origin_group_code:"{{$origin_group_code}}",
		}

		$.get('{{ url("fetch/assembly/request") }}', data, function(result, status, xhr){
			if(result.status){
				// $('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');


				$("#modelAll").empty();
				// $("#quantity").empty();
				// $("#quantity_kanban").empty();
				$("#stamp").empty();
				$("#perakitan").empty();
				$("#kariawase").empty();
				$("#tanpoire").empty();
				$("#tanpoawase").empty();
				$("#seasoning").empty();
				$("#kango").empty();
				$("#renraku").empty();
				$("#fukiage1").empty();
				$("#fukiage2").empty();
				$("#qa").empty();
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
				// quantity_kanban = "<th>Kanban</th>";
				// quantity = "<th>PC(s)</th>";

				stamp = "<th>Stamp</th>";
				perakitan = "<th>Perakitan</th>";
				kariawase = "<th>Kariawase</th>";
				tanpoire = "<th>Tanpoire</th>";
				tanpoawase = "<th>Tanpo Awase</th>";
				seasoning = "<th>Seasoning</th>";
				kango = "<th>Kango</th>";
				renraku = "<th>Renraku</th>";
				fukiage1 = "<th>Fukiage 1</th>";
				fukiage2 = "<th>Fukiage 2</th>";
				qa = "<th>QA</th>";
				// diff = "<th>Stock</th>";
				total = "<th>Total</th>";

				
				chart = "<th style='vertical-align: text-top;'><p><i class='fa fa-fw fa-clock-o'></i> Last Updated: "+ getActualFullDate() +"</p></th>";

				$.each(result.datas, function(index, value){
					// console.log();
					// if (value.model.indexOf('//J') > -1) {
					// 	model += "<th style='background-color: #fff;color: #eb2b21;'>"+value.model.replace("YFL", "")+"</th>";
					// }else{
						model += "<th style='background-color: #1565C0;color: #e5e5df;'>"+value.model.replace("YFL", "")+"</th>";
					// }
					sum_total = parseInt(value.stamp) + parseInt(value.perakitan) + parseInt(value.kariawase) + parseInt(value.tanpoire) + parseInt(value.tanpoawase) + parseInt(value.seasoning) + parseInt(value.kango) + parseInt(value.renraku) + parseInt(value.fukiage1) + parseInt(value.fukiage2) + parseInt(value.qa);
					if (sum_total <= 0) {
						color3 = "style='background-color: rgb(255,204,255); color: #333;'";
					} else {
						color3 = "style='background-color: rgb(204, 255, 255); color: #333;'";
					}

					stamp += "<td>"+value.stamp+"</td>";
					perakitan += "<td>"+value.perakitan+"</td>";
					kariawase += "<td>"+value.kariawase+"</td>";
					tanpoire += "<td>"+value.tanpoire+"</td>";
					tanpoawase += "<td>"+value.tanpoawase+"</td>";
					seasoning += "<td>"+value.seasoning+"</td>";
					kango += "<td>"+value.kango+"</td>";
					renraku += "<td>"+value.renraku+"</td>";
					fukiage1 += "<td>"+value.fukiage1+"</td>";
					fukiage2 += "<td>"+value.fukiage2+"</td>";
					qa += "<td>"+value.qa+"</td>";
					total += "<td "+color3+">"+sum_total+"</td>";
				})

				$("#modelAll").append(model);
				$("#stamp").append(stamp);
				$("#perakitan").append(perakitan);
				$("#kariawase").append(kariawase);
				$("#tanpoire").append(tanpoire);
				$("#tanpoawase").append(tanpoawase);
				$("#seasoning").append(seasoning);
				$("#kango").append(kango);
				$("#renraku").append(renraku);
				$("#fukiage1").append(fukiage1);
				$("#fukiage2").append(fukiage2);
				$("#qa").append(qa);
				$("#total").append(total);

			}
		})
	}

</script>
@endsection