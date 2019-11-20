@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	table.table-bordered{
		border:1px solid black;
		/*background-color: white;*/
		color:white;
	}
	.table-bordered > thead > tr > th, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > tbody > tr > td, .table-bordered > tfoot > tr > td {
		border: 1px solid black;
		/*font-size: 1vw;*/
		font-weight: bold;
	}
	.table > tbody > tr > th {
		padding: 2px;
		text-align: center;
		color: black;
		background-color: white;
	}
	#assyTable > tbody > tr > td {
		text-align: right;
	}
	#detailTabel {
		color: black;
	}
	.stock {
		margin-left:20px;
		width: 12px;
		height: 12px;
		border-radius: 50%;
		background: #a9ff96;
		display: inline-block;
	}
	.lcq {
		margin-left:20px;
		width: 12px;
		height: 12px;
		border-radius: 50%;
		background: #fcf33a;
		display: inline-block;
	}
	.brl {
		margin-left:20px;
		width: 12px;
		height: 12px;
		border-radius: 50%;
		background: #722973;
		display: inline-block;
	}
	.wld {
		margin-left:20px;
		width: 12px;
		height: 12px;
		border-radius: 50%;
		background: #7cb5ec;
		display: inline-block;
	}
	.plt {
		margin-left:20px;
		width: 12px;
		height: 12px;
		border-radius: 50%;
		background: silver;
		display: inline-block;
	}
	.acc {
		background: #c8cfcb;
		color: black;
	}
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0; overflow-y:hidden; overflow-x:scroll;">
	<div class="row">
		<div class="col-xs-12" style="height:100%">
			<table id="assyTable" class="table table-bordered" style="padding: 0px; margin-bottom: 0px; height:100%;">
				<tr id="model">
				</tr>
				<tr id="plan_acc">
					<!-- <th>Plan acc</th> -->
				</tr>
				<tr id="picking_acc">
					<!-- <th>Pick acc</th> -->
				</tr>
				<tr id="return_acc">
					<!-- <th>Return acc</th> -->
				</tr>
				<tr id="plan">
					<!-- <th>Total Plan</th> -->
				</tr>
				<tr id="picking">
					<!-- <th>Picking</th> -->
				</tr>
				<tr id="diff">
					<!-- <th>Diff</th> -->
				</tr>
				<tr style="height: 5px"></tr>
				<tr id="stok">
					
				</tr>
				<tr id="stok_all">

				</tr>
				<tr id="chart" style="text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black; font-size: 12px;">
				</tr>
				<tr id="legend"></tr>
			</table>

			<!-- <table class="table table-bordered" style="padding: 0px; margin-bottom: 10px;">
				
			</table> -->
		</div>
		<div class="col-xs-12">
			<div id="picking_chart" style="width: 100%; margin: auto"></div>
		</div>
		<div class="col-xs-12">
			<!-- <div class="col-xs-3" style="padding: 0px 5px 0px 5px"> -->
				<center><div id="judul" style="color:white; font-weight: bold; font-size: 2vw"></div></center>
				<!-- </div> -->
			</div>
			<div class="col-xs-12" style="margin-top: 10px">
				<form method="GET" action="{{ url('index/display/sub_assy/'.$option) }}">
					<div class="col-xs-2" style="line-height: 1">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border-color: #00a65a">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="tgl" name="date" placeholder="Select Date" style="border-color: #00a65a" <?php if (isset($_GET['date'])): ?>
							<?php echo "value=".$_GET['date']; endif ?>>
						</div>
						<br>
					</div>
					<div class="col-xs-2">
						<select class="form-control select2" multiple="multiple" id="key" onchange="change()" data-placeholder="Select Key">
							@foreach($keys as $key)
							<option value="{{ $key->key }}">{{ $key->key }}</option>
							@endforeach
						</select>
						<input type="text" name="key2" id="dd" hidden>
					</div>
					<div class="col-xs-1">
						<select class="form-control select2" multiple="multiple" id="modelselect" onchange="changeModel()" data-placeholder="Select Model">
							@foreach($models as $model)
							<option value="{{ $model->model }}">{{ $model->model }}</option>
							@endforeach
						</select>
						<input type="text" name="model2" id="model2" hidden>
					</div>

					<!-- JIKA SUB ASSY -->
					@if($option == "assy")
					<div class="col-xs-2">
						<select class="form-control select2" id="surface" multiple="multiple" onchange="changeSurface()" data-placeholder="Select Surface">
							@foreach($surfaces as $surface)
							<option value="{{ $surface[0] }}">{{ $surface[1] }}</option>
							@endforeach
						</select>
					</div>
					@endif
					<input type="text" name="surface2" id="surface2" hidden>

					<div class="col-xs-1">
						<select class="form-control select2" id="hpl" multiple="multiple" onchange="changeHpl()" data-placeholder="Select HPL">
							@foreach($hpls as $hpl)
							<option value="{{ $hpl }}">{{ $hpl }}</option>
							@endforeach
						</select>
						<input type="text" name="hpl2" id="hpl2" hidden>
					</div>

					<div class="col-xs-2">
						<select class="form-control select2" id="order" onchange="changeOrder()" placeholder="Order by">
							<option value="">Diff</option>
							<option value="1" <?php if($_GET['order2'] != '' ) echo "selected"; ?> >Stock Room</option>
						</select>
						<input type="text" name="order2" id="order2" hidden>
					</div>
					<div class="col-xs-1">
						<button class="btn btn-success" type="submit">Cari</button>
					</div>
				</form>
			</div>

			<div class="modal fade" id="myModal">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<h4 style="float: right;" id="modal-title"></h4> 
							<h4 class="modal-title"><b id="titel"></b></h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<table class="table table-bordered table-stripped table-responsive" style="width: 100%" id="detailTabel">
										<thead style="background-color: rgba(126,86,134,.7);">
											<tr>
												<th>Tag</th>
												<th>GMC</th>
												<th>Description</th>
												<th>Quantity</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
										<tfoot style="background-color: rgba(126,86,134,.7);">
											<tr>
												<th colspan="3" style="text-align:right">Total : </th>
												<th></th>
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
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
		</div>

	</section>
	@endsection
	@section('scripts')
	<script src="{{ url("js/highcharts.js")}}"></script>
	<script src="{{ url("js/exporting.js")}}"></script>
	<script src="{{ url("js/export-data.js")}}"></script>
	<script>
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		jQuery(document).ready(function(){
			fill_table();
			setInterval(fill_table, 30000);

			var kunci = "{{$_GET['key2']}}";
			var kuncies = kunci.split(",");
			var kunciFilter = [];
			ctg = "";

			for(var i = 0; i < kuncies.length; i++){
				ctg = kuncies[i].charAt(0);

				if(kunciFilter.indexOf(ctg) === -1){
					kunciFilter[kunciFilter.length] = ctg;
				}
			}
		// alert(kunciFilter);

		$("#judul").text(kunciFilter+"-{{$_GET['model2']}}-{{$_GET['surface2']}}-{{$_GET['hpl2']}}");


		$('.select2').select2();

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});
	});

		function change() {
			$("#dd").val($("#key").val());
		}

		function changeModel() {
			$("#model2").val($("#modelselect").val());
		}
		function changeSurface() {
			$("#surface2").val($("#surface").val());
		}
		function changeHpl() {
			$("#hpl2").val($("#hpl").val());
		}
		function changeOrder() {
			$("#order2").val($("#order").val());
		}

		function fill_table() {
			var data = {
				tanggal:"{{$_GET['date']}}",
				key:"{{$_GET['key2']}}",
				model:"{{$_GET['model2']}}",
				surface:"{{$_GET['surface2']}}",
				hpl:"{{$_GET['hpl2']}}",
				order:"{{$_GET['order2']}}"
			}
			diffs = [];
			plans = [];

		// var values="{{$_GET['key2']}}";
		// $.each(values.split(","), function(i,e){
		// 	$("#key option[value='" + e + "']").prop("selected", true);
		// });
		if ("{{$option}}" == "assy") {
			var url = '{{ url("fetch/display/sub_assy") }}';
		} else {
			var url = '{{ url("fetch/display/welding") }}';
		}

		$.get(url, data, function(result, status, xhr){
			if(result.status){
				$("#model").empty();
				$("#plan").empty();
				$("#picking").empty();
				$("#diff").empty();

				$("#plan_acc").empty();
				$("#picking_acc").empty();
				$("#return_acc").empty();

				$("#stok").empty();
				$("#stok_all").empty();

				model = "<th style='width:45px'>#</th>";
				totplan = "<th>Plan</th>";
				picking = "<th>Pick</th>";

				if ("{{$option}}" == "assy") {
					diff = "<th>Diff</th>";
				} else {
					diff = "<th>Target</th>";
				}

				planAcc = "<th>Plan acc</th>";
				pickAcc = "<th>Pick acc</th>";
				retunAcc = "<th>Return acc</th>";

				if ("{{$option}}" == "assy") {
					stk = "<th style='border: 1px solid white;'>Stock Room</th>";
				} else {
					stk = "<th style='border: 1px solid white;'>Stock All</th>";
				}
				stk_als = "<th style='border: 1px solid white;' rowspan='2'>Availibility</th>";

				var style = "";

				$.each(result.plan, function(index, value){
					var minus = 0;
					// var picking = 0;

					if (value.diff <= 0) {
						style = "style='background-color:#00a65a';";
					} else {
						style = "style='background-color:#f24b4b';";
					}

					if (value.model.charAt(0) == 'A') {
						color = "style='background-color:#80ed5f';";
					} else {
						color = "style='background-color:#f2e127';";
					}

					if (value.surface) {
						srf = value.surface;
					} else {
						srf = "";
					}

					planAcc += "<td class='acc'>"+value.plan_ori+"</td>";
					pickAcc += "<td class='acc'>"+value.plus+"</td>";
					retunAcc += "<td class='acc'>"+value.minus+"</td>";

					model += "<th "+color+">"+value.model+"<br/>"+value.key+"<br/>"+srf+"</th>";
					totplan += "<td>"+value.plan+"</td>";
					picking += "<td>"+value.picking+"</td>";
					if ("{{$option}}" == "assy") {
						diff += "<td "+style+">"+(-value.diff)+"</td>";
					} else {
						diff += "<td "+style+">"+value.diff+"</td>";
					}
					diffs.push(value.diff);
					plans.push(value.plan);
				})

				$("#model").append(model);
				$("#plan").append(totplan);
				$("#picking").append(picking);
				$("#diff").append(diff);

				$("#plan_acc").append(planAcc);
				$("#picking_acc").append(pickAcc);
				$("#return_acc").append(retunAcc);


				// -------- CHART ------------

				var stockroom = 0;
				var barrel = 0;
				var lacquering = 0;
				var plating = 0;
				var welding = 0;
				var legend = 0;

				var categories = [];
				var max_tmp = [];
				var max = 0;
				var sisa = 0;
				var tmp = 0;

				$.each(result.stok, function(index2, value2){

					if ("{{$option}}" != "assy") {
						tmp = (parseInt(value2.barrel) + parseInt(value2.lacquering) + parseInt(value2.plating)+ parseInt(value2.stockroom) + parseInt(value2.welding));
						if (tmp >= diffs[index2]) {
							color2 = "background-color:#00a65a";
						} else {
							color2 = "background-color:#f24b4b";
						}

						if (tmp / plans[index2] >= 3) {
							colour = "background-color:#00a65a";
						} else {
							colour = "background-color:#f24b4b";
						}

					} else {
						if (value2.stockroom >= diffs[index2]) {
							color2 = "background-color:#00a65a";
						} else {
							color2 = "background-color:#f24b4b";
						}

						if (parseInt(value2.stockroom) / plans[index2] >= 1) {
							colour = "background-color:#00a65a";
						} else {
							colour = "background-color:#f24b4b";
						}

					}

					if ("{{$option}}" != "assy") {
						stk += "<td style='border: 1px solid white; "+color2+"'>"+tmp+"</td>";

						stk_als += "<td style='border: 1px solid white; "+colour+"'>"+(tmp / plans[index2]).toFixed(1)+"</td>";
					} else {
						stk += "<td style='border: 1px solid white; "+color2+"'>"+parseInt(value2.stockroom)+"</td>";

						stk_als += "<td style='border: 1px solid white; "+colour+"'>"+(parseInt(value2.stockroom) / plans[index2]).toFixed(1)+"</td>";
					}

					max_tmp.push(parseInt(value2.barrel) + parseInt(value2.lacquering) + parseInt(value2.plating)+ parseInt(value2.stockroom) + parseInt(value2.welding));

					if (value2.surface) {
						srf2 = value2.surface;
					} else {
						srf2 = "";
					}

					categories.push(value2.model+" "+value2.key+" "+srf2);
				})

				$("#stok").append(stk);
				$("#stok_all").append(stk_als);

				// console.table(result.stok);

				max = (Math.max(...max_tmp)) + 10;
				// console.log(max);

				$('#chart').empty();
				$('#legend').empty();

				var chart = "";
				for (var i = 0; i < result.stok.length; i++) {

					chart += '<td style="padding: 0px; height:350px">';

					kosong = (max - (parseInt(result.stok[i].barrel) + parseInt(result.stok[i].lacquering) + parseInt(result.stok[i].plating)
						+ parseInt(result.stok[i].stockroom) + parseInt(result.stok[i].welding))) / max * 100;
					chart += '<div style="margin: 0px 3px 0px 3px; background-color: #3c3c3c; height: '+kosong+'%" id="kosong"></div>';

					welding = parseInt(result.stok[i].welding) / max * 100;
					if (parseInt(result.stok[i].welding) > 0) wld = parseInt(result.stok[i].welding); else wld = '';
					chart += '<div style="line-height: 80%; text-align: center; margin: 0px 3px 0px 3px; background-color: #7cb5ec; height: '+welding+'%" id="welding">'+wld+'</div>';

					barrel = parseInt(result.stok[i].barrel) / max * 100;
					if (parseInt(result.stok[i].barrel) > 0) brl = parseInt(result.stok[i].barrel); else brl = '';
					chart += '<div style="line-height: 80%; text-align: center; margin: 0px 3px 0px 3px; background-color: #722973; height: '+barrel+'%" id="barrel">'+brl+'</div>';

					lacquering = parseInt(result.stok[i].lacquering) / max * 100;
					if (parseInt(result.stok[i].lacquering) > 0) lcq = parseInt(result.stok[i].lacquering); else lcq = '';
					chart += '<div style="line-height: 80%; text-align: center; margin: 0px 3px 0px 3px; background-color: #fcf33a; height: '+lacquering+'%" id="lacquering">'+lcq+'</div>';

					plating = parseInt(result.stok[i].plating) / max * 100;
					if (parseInt(result.stok[i].plating) > 0) plt = parseInt(result.stok[i].plating); else plt = '';
					chart += '<div style="line-height: 80%; text-align: center; margin: 0px 3px 0px 3px; background-color: silver; height: '+plating+'%" id="plating">'+plt+'</div>';

					stockroom = parseInt(result.stok[i].stockroom) / max * 100;
					if (parseInt(result.stok[i].stockroom) > 0) stk = parseInt(result.stok[i].stockroom); else stk = '';
					chart += '<div style="line-height: 80%; text-align: center; margin: 0px 3px 0px 3px; background-color: #a9ff96; height: '+stockroom+'%" id="stockroom">'+stk+'</div>';
					chart += '</td>';
				}

				legend += "<td style='background-color:white; border-color:white'></td>";
				legend += "<td colspan='"+(result.stok.length)+"' style='text-align:left'>";
				legend += "<div class='stock'></div> Stockroom";
				legend += "<div class='plt'></div> Plating";
				legend += "<div class='lcq'></div> Lacquering";
				legend += "<div class='brl'></div> Barrel";
				legend += "<div class='wld'></div> Welding";
				legend += "</td>";

				$('#chart').append(chart);
				$('#legend').append(legend);

				// Highcharts.chart('picking_chart', {
				// 	chart: {
				// 		type: 'column',
				// 		width: $('#assyTable').width(),
				// 		marginLeft: 40
				// 	},
				// 	title: {
				// 		text: null
				// 	},
				// 	xAxis: {
				// 		categories: categories
				// 	},
				// 	yAxis: {
				// 		min: 0,
				// 		title: {
				// 			enabled: false
				// 		},
				// 		stackLabels: {
				// 			enabled: true,
				// 			style: {
				// 				fontWeight: 'bold',
				// 				color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
				// 			}
				// 		},
				// 		labels: {
				// 			useHTML:true,
				// 			style:{
				// 				width:'10px',
				// 				whiteSpace:'normal'
				// 			},
				// 		},
				// 		tickInterval: 10
				// 	},
				// 	tooltip: {
				// 		headerFormat: '<b>{point.x}</b><br/>',
				// 		pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
				// 	},
				// 	plotOptions: {
				// 		column: {
				// 			stacking: 'normal',
				// 			dataLabels: {
				// 				enabled: true,
				// 				color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
				// 			},
				// 			animation: false,
				// 		},
				// 		series: {
				// 			cursor: 'pointer',
				// 			pointPadding: -0.25,
				// 			events: {
				// 				click: function(event) {
				// 					openModal(event.point.category, this.name)
				// 				}
				// 			}
				// 		}
				// 	},
				// 	credits :{
				// 		enabled: false,
				// 	},
				// 	series: [{
				// 		name: 'Welding',
				// 		data: welding
				// 	}, {
				// 		name: 'Barrel',
				// 		data: barrel
				// 	}, {
				// 		name: 'Lacquering',
				// 		data: lacquering
				// 	}, {
				// 		name: 'Plating',
				// 		data: plating
				// 	}, {
				// 		name: 'Stockroom',
				// 		data: stockroom
				// 	}]
				// });
			}
			diffs = [];
		})

}

function openModal(kunci, lokasi) {
	$("#myModal").modal("show");
	$("#titel").text(kunci+" ("+lokasi+")");

	$('#detailTabel').DataTable().destroy();

	var data = {
		model:kunci.split(" ")[0],
		key:kunci.split(" ")[1],
		surface:kunci.split(" ")[2],
		location:lokasi
	}

	var table = $('#detailTabel').DataTable({
		'dom': 'Bfrtip',
		'responsive': true,
		'lengthMenu': [
		[ 10, 25, 50, -1 ],
		[ '10 rows', '25 rows', '50 rows', 'Show all' ]
		],
		'paging': true,
		'lengthChange': true,
		'searching': true,
		'ordering': true,
		'order': [],
		'info': true,
		'autoWidth': true,
		"sPaginationType": "full_numbers",
		"bJQueryUI": false,
		"bAutoWidth": false,
		"processing": true,
		"serverSide": false,
		"ajax": {
			"type" : "get",
			"url" : "{{ url("fetch/detail/sub_assy") }}",
			"data" : data
		},
		"columns": [
		{ "data": "tag", "width" : "10%" },
		{ "data": "material_number", "width" : "10%" },
		{ "data": "material_description", "width" : "70%" },
		{ "data": "quantity", "width" : "10%", "className": "text-right"}
		],
		"footerCallback": function ( row, data, start, end, display ) {
			var api = this.api(), data;

            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
            	return typeof i === 'string' ?
            	i.replace(/[\$,]/g, '')*1 :
            	typeof i === 'number' ?
            	i : 0;
            };

            // Total over all pages
            total = api
            .column( 3 )
            .data()
            .reduce( function (a, b) {
            	return intVal(a) + intVal(b);
            }, 0 );

            // Total over this page
            pageTotal = api
            .column( 3, { page: 'current'} )
            .data()
            .reduce( function (a, b) {
            	return intVal(a) + intVal(b);
            }, 0 );

            // Update footer
            $( api.column( 3 ).footer() ).html(
            	total
            	);
        }
    });

}

// Highcharts.createElement('link', {
// 	href: '{{ url("fonts/UnicaOne.css")}}',
// 	rel: 'stylesheet',
// 	type: 'text/css'
// }, null, document.getElementsByTagName('head')[0]);

// Highcharts.theme = {
// 	colors: ['#7cb5ec', '#722973' , '#fcf33a', '#94908f', '#90ed7d'],
// 	chart: {
// 		backgroundColor: {
// 			linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
// 			stops: [
// 			[0, '#2a2a2b'],
// 			[1, '#3e3e40']
// 			]
// 		},
// 		style: {
// 			fontFamily: 'sans-serif'
// 		},
// 		plotBorderColor: '#606063'
// 	},
// 	title: {
// 		style: {
// 			color: '#E0E0E3',
// 			textTransform: 'uppercase',
// 			fontSize: '20px'
// 		}
// 	},
// 	subtitle: {
// 		style: {
// 			color: '#E0E0E3',
// 			textTransform: 'uppercase'
// 		}
// 	},
// 	xAxis: {
// 		gridLineColor: '#707073',
// 		labels: {
// 			style: {
// 				color: '#E0E0E3'
// 			}
// 		},
// 		lineColor: '#707073',
// 		minorGridLineColor: '#505053',
// 		tickColor: '#707073',
// 		title: {
// 			style: {
// 				color: '#A0A0A3'

// 			}
// 		}
// 	},
// 	yAxis: {
// 		gridLineColor: '#707073',
// 		labels: {
// 			style: {
// 				color: '#E0E0E3'
// 			}
// 		},
// 		lineColor: '#707073',
// 		minorGridLineColor: '#505053',
// 		tickColor: '#707073',
// 		tickWidth: 1,
// 		title: {
// 			style: {
// 				color: '#A0A0A3'
// 			}
// 		}
// 	},
// 	tooltip: {
// 		backgroundColor: 'rgba(0, 0, 0, 0.85)',
// 		style: {
// 			color: '#F0F0F0'
// 		}
// 	},
// 	plotOptions: {
// 		series: {
// 			dataLabels: {
// 				color: '#FFF'
// 			},
// 			marker: {
// 				lineColor: '#333'
// 			}
// 		},
// 		boxplot: {
// 			fillColor: '#505053'
// 		},
// 		candlestick: {
// 			lineColor: 'black'
// 		},
// 		errorbar: {
// 			color: 'white'
// 		}
// 	},
// 	legend: {
// 		itemStyle: {
// 			color: '#E0E0E3'
// 		},
// 		itemHoverStyle: {
// 			color: '#FFF'
// 		},
// 		itemHiddenStyle: {
// 			color: '#606063'
// 		}
// 	},
// 	credits: {
// 		style: {
// 			color: '#666'
// 		}
// 	},
// 	labels: {
// 		style: {
// 			color: '#707073'
// 		}
// 	},

// 	drilldown: {
// 		activeAxisLabelStyle: {
// 			color: '#F0F0F3'
// 		},
// 		activeDataLabelStyle: {
// 			color: '#F0F0F3'
// 		}
// 	},

// 	navigation: {
// 		buttonOptions: {
// 			symbolStroke: '#DDDDDD',
// 			theme: {
// 				fill: '#505053'
// 			}
// 		}
// 	},

// 	rangeSelector: {
// 		buttonTheme: {
// 			fill: '#505053',
// 			stroke: '#000000',
// 			style: {
// 				color: '#CCC'
// 			},
// 			states: {
// 				hover: {
// 					fill: '#707073',
// 					stroke: '#000000',
// 					style: {
// 						color: 'white'
// 					}
// 				},
// 				select: {
// 					fill: '#000003',
// 					stroke: '#000000',
// 					style: {
// 						color: 'white'
// 					}
// 				}
// 			}
// 		},
// 		inputBoxBorderColor: '#505053',
// 		inputStyle: {
// 			backgroundColor: '#333',
// 			color: 'silver'
// 		},
// 		labelStyle: {
// 			color: 'silver'
// 		}
// 	},

// 	navigator: {
// 		handles: {
// 			backgroundColor: '#666',
// 			borderColor: 'black'
// 		},
// 		outlineColor: '#CCC',
// 		maskFill: 'rgba(255,255,255,0.1)',
// 		series: {
// 			color: '#7798BF',
// 			lineColor: '#A6C7ED'
// 		},
// 		xAxis: {
// 			gridLineColor: '#505053'
// 		}
// 	},

// 	scrollbar: {
// 		barBackgroundColor: '#808083',
// 		barBorderColor: '#808083',
// 		buttonArrowColor: '#CCC',
// 		buttonBackgroundColor: '#606063',
// 		buttonBorderColor: '#606063',
// 		rifleColor: '#FFF',
// 		trackBackgroundColor: '#404043',
// 		trackBorderColor: '#404043'
// 	},

// 	legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
// 	background2: '#505053',
// 	dataLabelsColor: '#fff',
// 	textColor: '#C0C0C0',
// 	contrastTextColor: '#F0F0F3',
// 	maskColor: 'rgba(255,255,255,0.3)'
// };
// Highcharts.setOptions(Highcharts.theme);

</script>
@endsection