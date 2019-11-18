@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}

	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:2px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:2px solid black;
		padding-top: 0;
		padding-bottom: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:2px solid black;
	}
	#loading, #error { display: none; }
	.bar {
		height:100px;
		display:inline-block;
		float:left;
		border: 1px solid black;
	}
	.text-rotasi {
		-ms-transform: rotate(-90deg); /* IE 9 */
		-webkit-transform: rotate(-90deg); /* Safari 3-8 */
		transform: rotate(-90deg);
		white-space: nowrap;
		font-size: 12px;
		vertical-align: middle;
		line-height: 100px;
	}
	#mc_head2 > th{
		padding: 0px;
		border-top: 0px;
		border-left: 1px solid black;
		border-right: 1px solid black;
		width: 10px;
		font-size: 1vw;
	}
	#mc_head > th{
		padding: 0px;
		border-bottom: 0px;
	}
</style>
@endsection

@section('content')
<section class="content" style="overflow-y:hidden; overflow-x:scroll; padding-top: 0px">
	<div class="row">
		<div class="col-xs-12" style="padding: 0 5px 0 5px">
			<table id="example1" class="table table-bordered">
				<thead style="background-color: #b89cff;">
					<tr id="mc_head">
					</tr>
					<tr id="mc_head2">
					</tr>
				</thead>
				<tbody id="mc_body" style="color: white">
				</tbody>
			</table>
		</div>
	</div>
</section>

</div>

@stop

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		drawTable();
		// setInterval(drawTable, 1000);
	});

	function drawTable() {
		$.get('{{ url("fetch/schedule") }}',  function(result, status, xhr){
			$("#mc_head").empty();
			$("#mc_body").empty();

			var	head = "<th rowspan=2>#</th>";
			var head2 = "";

			for (var z = 1; z <= 31; z++) {
				head += "<th colspan=24>"+z+"</th>";
				for (var x = 1; x <= 24; x++) {
					if (x % 4 == 0) {
						head2 += "<th>"+x+"</th>";
					} else {
						head2 += "<th>&nbsp;</th>";
					}
				}
			}

			$("#mc_head").append(head);
			$("#mc_head2").append(head2);

			var body = "";
			for (var i = 1; i <= 11; i++) {
				if (i != 10) {
					body += "<tr id='machine_"+i+"'>";
					body += "<td>Machine "+i+"</td>";
					body += "</tr>";
				}
			}

			$("#mc_body").append(body);


			for (var x = 1; x <= 11; x++) {
				var data = "";
				if (x != 10) {
					for (var y = 1; y <= 31; y++) {
					// if () {}
					data += "<td style='padding:0px' colspan=24><div style='width:240px' id='"+x+"_"+y+"'></div></div></td>";
				}
			}
			$("#machine_"+x).append(data);
		}

		console.table(result.schedule);

		$.each(result.schedule, function(index, value){
			var machine = value.mesin.split(" ")[1];
			var due_date = value.due_date.split("-")[2];
			var time = value.qty / value.shoot * value.cycle / 60 / 60;
			var text = "";
			var fontColor = "black";
			var color = "";

			if(isNaN(time)) {
				time = 24;
				text = "OFF";
			} else {
				text = value.color.split(" - ")[0];
			}

			var part_color = value.part.split(" ")[0].charAt(value.part.split(" ")[0].length-1);

			if (part_color == "B") {
				color = "#4a74ff";
			} else if (part_color == "G") {
				color = "#76f562";
			} else if (part_color == "P") {
				color = "#f76fa8";
			}  else if (part_color == "F") {
				color = "white";
			} else {
				if (value.color.split(" - ")[1] == "ivory") {
					color = "#faedbb";
				} else if (value.color.split(" - ")[1] == "BEIGE") {
					color = "#595c59";
					fontColor = "white";
				}
			}

			// if (value.color.split(" - ")[1] == "ivory") {
			// 	color = "#faedbb";
			// }
			// else if (value.color.split(" - ")[1] == "BEIGE") {
			// 	color = "#ddd";
			// } else if (value.color.split(" - ")[1] == "skelton") {
			// 	color = "#de391f";
			// }

			$("#"+machine+"_"+due_date).append("<div class='bar' style='width:"+time.toFixed(1) * 10+"px; background-color:"+color+"; color:"+fontColor+"'><div class='text-rotasi'>"+text+"</div></div>");
				// console.log(machine+"_"+due_date);
			})
	})
	}


</script>

@stop