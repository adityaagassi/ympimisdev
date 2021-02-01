@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	.content{
		color: white;
		font-weight: bold;
	}
	#loading, #error { display: none; }

	.loading {
		margin-top: 8%;
		position: absolute;
		left: 50%;
		top: 50%;
		-ms-transform: translateY(-50%);
		transform: translateY(-50%);
	}
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="row" style="margin:0px;">
				<div class="col-xs-2">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border: none;">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="tanggal" placeholder="Select Date">
					</div>
				</div>
				<div class="col-xs-2" style="padding-right: 0; color:black;">
					<select class="form-control select2" multiple="multiple" id='origin_group' data-placeholder="Select Products" style="width: 100%;">
						@foreach($origin_groups as $origin_group)
						<option value="{{ $origin_group->origin_group_code }}-{{ $origin_group->origin_group_name }}">{{ $origin_group->origin_group_name }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-xs-2">
					<button class="btn btn-success" onclick="fillChart()">Update Chart</button>
				</div>
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
			</div>
			<div class="col-xs-12" style="margin-top: 5px;">
				<div id="container1" style="width: 100%;"></div>
				<div id="container2" style="width: 100%;"></div>
			</div>
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
		$('.select2').select2();
		fillChart();
		// setInterval(fillChart, 10000);
		
	});

	$('.datepicker').datepicker({
		<?php $tgl_max = date('d-m-Y') ?>
		autoclose: true,
		format: "dd-mm-yyyy",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	Highcharts.theme = {
		colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
		'#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
		chart: {
			backgroundColor: {
				linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
				stops: [
				[0, '#2a2a2b'],
				[1, '#3e3e40']
				]
			},
			style: {
				fontFamily: 'sans-serif'
			},
			plotBorderColor: '#606063'
		},
		title: {
			style: {
				color: '#E0E0E3',
				textTransform: 'uppercase',
				fontSize: '20px'
			}
		},
		subtitle: {
			style: {
				color: '#E0E0E3',
				textTransform: 'uppercase'
			}
		},
		xAxis: {
			gridLineColor: '#707073',
			labels: {
				style: {
					color: '#E0E0E3'
				}
			},
			lineColor: '#707073',
			minorGridLineColor: '#505053',
			tickColor: '#707073',
			title: {
				style: {
					color: '#A0A0A3'

				}
			}
		},
		yAxis: {
			gridLineColor: '#707073',
			labels: {
				style: {
					color: '#E0E0E3'
				}
			},
			lineColor: '#707073',
			minorGridLineColor: '#505053',
			tickColor: '#707073',
			tickWidth: 1,
			title: {
				style: {
					color: '#A0A0A3'
				}
			}
		},
		tooltip: {
			backgroundColor: 'rgba(0, 0, 0, 0.85)',
			style: {
				color: '#F0F0F0'
			}
		},
		plotOptions: {
			series: {
				dataLabels: {
					color: 'white'
				},
				marker: {
					lineColor: '#333'
				}
			},
			boxplot: {
				fillColor: '#505053'
			},
			candlestick: {
				lineColor: 'white'
			},
			errorbar: {
				color: 'white'
			}
		},
		legend: {
			itemStyle: {
				color: '#E0E0E3'
			},
			itemHoverStyle: {
				color: '#FFF'
			},
			itemHiddenStyle: {
				color: '#606063'
			}
		},
		credits: {
			style: {
				color: '#666'
			}
		},
		labels: {
			style: {
				color: '#707073'
			}
		},

		drilldown: {
			activeAxisLabelStyle: {
				color: '#F0F0F3'
			},
			activeDataLabelStyle: {
				color: '#F0F0F3'
			}
		},

		navigation: {
			buttonOptions: {
				symbolStroke: '#DDDDDD',
				theme: {
					fill: '#505053'
				}
			}
		},

		rangeSelector: {
			buttonTheme: {
				fill: '#505053',
				stroke: '#000000',
				style: {
					color: '#CCC'
				},
				states: {
					hover: {
						fill: '#707073',
						stroke: '#000000',
						style: {
							color: 'white'
						}
					},
					select: {
						fill: '#000003',
						stroke: '#000000',
						style: {
							color: 'white'
						}
					}
				}
			},
			inputBoxBorderColor: '#505053',
			inputStyle: {
				backgroundColor: '#333',
				color: 'silver'
			},
			labelStyle: {
				color: 'silver'
			}
		},

		navigator: {
			handles: {
				backgroundColor: '#666',
				borderColor: '#AAA'
			},
			outlineColor: '#CCC',
			maskFill: 'rgba(255,255,255,0.1)',
			series: {
				color: '#7798BF',
				lineColor: '#A6C7ED'
			},
			xAxis: {
				gridLineColor: '#505053'
			}
		},

		scrollbar: {
			barBackgroundColor: '#808083',
			barBorderColor: '#808083',
			buttonArrowColor: '#CCC',
			buttonBackgroundColor: '#606063',
			buttonBorderColor: '#606063',
			rifleColor: '#FFF',
			trackBackgroundColor: '#404043',
			trackBorderColor: '#404043'
		},

		legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
		background2: '#505053',
		dataLabelsColor: '#B0B0B3',
		textColor: '#C0C0C0',
		contrastTextColor: '#F0F0F3',
		maskColor: 'rgba(255,255,255,0.3)'
	};
	Highcharts.setOptions(Highcharts.theme);

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}
	
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

	function fillChart() {
		var hpl = $('#origin_group').val();
		var tanggal = $('#tanggal').val();
		$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

		var location_title = "";
		if(hpl.length > 1){
			for(var i = 0; i < hpl.length; i++){
				location_title += hpl[i].replace('-',' ');
				if(i == hpl.length-2){
					location_title += " & ";
				}else if(i != hpl.length-1){
					location_title += ", ";
				}
			}
		}else if(hpl.length == 1){ 
			location_title += hpl[0].replace('-',' ');
		}
		
		var data = {
			tanggal:tanggal,
			code:hpl
		}

		$.get('{{ url("fetch/middle/buffing_ic_atokotei_subassy") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					//CHart By NG Name
					var ng;
					var jml;
					var color;
					var series = [];
					var other = 0;

					for (var i = 0; i < result.ng_name.length; i++) {
						if(result.ng_name[i].ng_name == 'Kizu before'){
							ng = result.ng_name[i].ng_name;
							jml = [result.ng_name[i].jml];
							color = '#2b908f';
							series.push({name : ng, data: jml, color: color});

						}else if(result.ng_name[i].ng_name == 'Hokori debu'){
							ng = result.ng_name[i].ng_name;
							jml = [result.ng_name[i].jml];
							color = '#9c4dcc';
							series.push({name : ng, data: jml, color: color});

						}else if(result.ng_name[i].ng_name == 'Hokori benang'){
							ng = result.ng_name[i].ng_name;
							jml = [result.ng_name[i].jml];
							color = '#90ee7e';
							series.push({name : ng, data: jml, color: color});

						}else if(result.ng_name[i].ng_name == 'Kizu after'){
							ng = result.ng_name[i].ng_name;
							jml = [result.ng_name[i].jml];
							color = '#f45b5b';
							series.push({name : ng, data: jml, color: color});

						}else if(result.ng_name[i].ng_name == 'Scrath'){
							ng = result.ng_name[i].ng_name;
							jml = [result.ng_name[i].jml];
							color = '#7798BF';
							series.push({name : ng, data: jml, color: color});

						}else if(result.ng_name[i].ng_name == 'Buff tarinai'){
							ng = result.ng_name[i].ng_name;
							jml = [result.ng_name[i].jml];
							color = '#aaeeee';
							series.push({name : ng, data: jml, color: color});

						}else if(result.ng_name[i].ng_name == 'Toso usui'){
							ng = result.ng_name[i].ng_name;
							jml = [result.ng_name[i].jml];
							color = '#ff0066';
							series.push({name : ng, data: jml, color: color});

						}else if(result.ng_name[i].ng_name == 'Tare'){
							ng = result.ng_name[i].ng_name;
							jml = [result.ng_name[i].jml];
							color = '#FF8F00';
							series.push({name : ng, data: jml, color: color});

						}else if(result.ng_name[i].ng_name == 'Yogore'){
							ng = result.ng_name[i].ng_name;
							jml = [result.ng_name[i].jml];
							color = '#cfd8dc';
							series.push({name : ng, data: jml, color: color});

						}else if(result.ng_name[i].ng_name == 'Enthol'){
							ng = result.ng_name[i].ng_name;
							jml = [result.ng_name[i].jml];
							color = '#a1887f';
							series.push({name : ng, data: jml, color: color});

						}else if(result.ng_name[i].ng_name == 'Black shimi'){
							ng = result.ng_name[i].ng_name;
							jml = [result.ng_name[i].jml];
							color = '#212121';
							series.push({name : ng, data: jml, color: color});

						}else if(result.ng_name[i].ng_name == 'Buff tidak rata'){
							ng = result.ng_name[i].ng_name;
							jml = [result.ng_name[i].jml];
							color = '#FFEB3B';
							series.push({name : ng, data: jml, color: color});

						}else{
							other += result.ng_name[i].jml;
						}
					}

					series.push({name : 'Others', data: [other], color: '#ffffff'});

					series.sort(function(a, b){return b.data[0] - a.data[0]});


					Highcharts.chart('container1', {
						chart: {
							type: 'column'
						},
						title: {
							text: 'NG I.C. SubAssy by NG Name',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: 'on '+result.date,
							style: {
								fontSize: '1vw',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: ng,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							reversed: true,
							labels: {
								enabled: false
							},
						},
						yAxis: {
							title: {
								text: 'Total Not Good'
							},
							type: 'logarithmic'
						},
						legend: {
							layout: 'vertical',
							align: 'right',
							verticalAlign: 'top',
							x: 1,
							y: 0,
							floating: true,
							borderWidth: 1,
							backgroundColor:
							Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
							shadow: true
						},
						tooltip: {
							headerFormat: '<span>NG Name</span><br/>',
							pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y}</b> <br/>',
						},
						plotOptions: {
							series:{
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										textOutline: false,
										fontSize: '1vw'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								cursor: 'pointer'
							}
						},credits: {
							enabled: false
						},
						series: series
					});

					//Chart By Key
					
					var key = [];
					
					var kizu_before = [];
					var hokori_debu = [];
					var hokori_benang = [];
					var kizu_after = [];
					var scrath = [];
					var buff_tarinai = [];
					var toso_usui = [];
					var tare = [];
					var yogore = [];
					var enthol = [];
					var black_shimi = [];
					var buff_tidak_rata = [];
					var other = [];

					for (var i = 0; i < result.key.length; i++) {
						key.push(result.key[i].key);

						for (var j = 0; j < result.detail_key.length; j++) {
							if(result.key[i].key == result.detail_key[j].key){
								if(result.detail_key[j].ng_name == 'Kizu before'){
									kizu_before.push(result.detail_key[j].jml);									
								}else if(result.detail_key[j].ng_name == 'Hokori debu'){
									hokori_debu.push(result.detail_key[j].jml);									
								}else if(result.detail_key[j].ng_name == 'Hokori benang'){
									hokori_benang.push(result.detail_key[j].jml);									
								}else if(result.detail_key[j].ng_name == 'Kizu after'){
									kizu_after.push(result.detail_key[j].jml);									
								}else if(result.detail_key[j].ng_name == 'Scrath'){
									scrath.push(result.detail_key[j].jml);									
								}else if(result.detail_key[j].ng_name == 'Buff tarinai'){
									buff_tarinai.push(result.detail_key[j].jml);									
								}else if(result.detail_key[j].ng_name == 'Toso usui'){
									toso_usui.push(result.detail_key[j].jml);									
								}else if(result.detail_key[j].ng_name == 'Tare'){
									tare.push(result.detail_key[j].jml);									
								}else if(result.detail_key[j].ng_name == 'Yogore'){
									yogore.push(result.detail_key[j].jml);									
								}else if(result.detail_key[j].ng_name == 'Enthol'){
									enthol.push(result.detail_key[j].jml);									
								}else if(result.detail_key[j].ng_name == 'Black shimi'){
									black_shimi.push(result.detail_key[j].jml);									
								}else if(result.detail_key[j].ng_name == 'Buff tidak rata'){
									buff_tidak_rata.push(result.detail_key[j].jml);
								}else{
									if(typeof other[i] == 'undefined'){
										other.push(result.detail_key[j].jml);
									}else{
										other[i] += result.detail_key[j].jml;
									}
								}
							}
						}
					}

					Highcharts.chart('container2', {
						chart: {
							type: 'column'
						},
						title: {
							text: '10 Highest NG I.C. SubAssy by Key',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: 'on '+result.date,
							style: {
								fontSize: '1vw',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: key,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							labels: {
								// rotation: -65,
								style: {
									fontSize: '26px'
								}
							},
						},
						yAxis: {
							title: {
								text: 'Total Not Good'
							},
							stackLabels: {
								enabled: true,
								style: {
									fontWeight: 'bold',
									color: 'white',
									fontSize: '1vw'
								}
							},
						},
						legend: {
							layout: 'vertical',
							align: 'right',
							verticalAlign: 'top',
							x: 1,
							y: 0,
							floating: true,
							borderWidth: 1,
							backgroundColor:
							Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
							shadow: true
						},
						tooltip: {
							headerFormat: '<span>{point.category}</span><br/>',
							pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
						},
						plotOptions: {
							column: {
								stacking: 'normal',
								// dataLabels: {
								// 	enabled: true,
								// }
							},
							series:{
								// dataLabels: {
								// 	enabled: true,
								// 	format: '{point.y}',
								// 	style:{
								// 		textOutline: false,
								// 		fontSize: '1vw'
								// 	}
								// },
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								cursor: 'pointer'
							}
						},credits: {
							enabled: false
						},
						series: [
						{
							name : 'Kizu before',
							data : kizu_before,
							color : '#2b908f'
						},{
							name : 'Hokori debu',
							data : hokori_debu,
							color : '#9c4dcc',
						},{
							name : 'Hokori benang',
							data : hokori_benang,
							color : '#90ee7e',
						},{
							name : 'Kizu after',
							data : kizu_after,
							color : '#f45b5b',
						},{
							name : 'Scrath',
							data : scrath,
							color : '#7798BF',
						},{
							name : 'Buff tarinai',
							data : buff_tarinai,
							color : '#aaeeee',
						},{
							name : 'Toso usui',
							data : toso_usui,
							color : '#ff0066',
						},{
							name : 'Tare',
							data : tare,
							color : '#FF8F00',

						},{
							name : 'Yogore',
							data : yogore,
							color : '#cfd8dc'

						},{
							name : 'Enthol',
							data : enthol,
							color : '#a1887f'

						},{
							name : 'Black shimi',
							data : black_shimi,
							color : '#212121'

						},{
							name : 'Buff tidak rata',
							data : buff_tidak_rata,
							color : '#FFEB3B'
						},{
							name : 'Others',
							data : other,
							color : '#ffffff'
						}
						]
					});

				}
			}
		});

}



</script>
@endsection