@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
		overflow:hidden;
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
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding: 0px;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
		vertical-align: middle;
		background-color: rgb(126,86,134);
		color: #FFD700;
	}
	thead {
		background-color: rgb(126,86,134);
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	#ngList {
		height:480px;
		overflow-y: scroll;
	}
	#loading, #error { display: none; }

</style>
@stop
@section('header')
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0;">

	<input type="hidden" id="loc" value="{{ $title }} {{$title_jp}} }">
	
	<div class="row" style="margin-left: 1%; margin-right: 1%;">
		<div class="col-xs-6" style="padding-right: 0; padding-left: 0">
			
			<div id="gauge">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Shooting Counter</th>
							
						</tr>
						
					</thead>
					<tbody>
						<tr>
							<td style="width: 10px; background-color: rgb(220,220,220); padding:0;font-size: 20px;" id="gaugechart"></td>
						</tr>
						<tr>
							<td style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;background-color: rgb(220,220,220);color: black"><b id="statusLog">Running</b></td>
						</tr>
						<tr>							
							<td style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;background-color: rgb(204,255,255);color: black;">MJB Skelton</td>
						</tr>
						<tr>							
							<td style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;background-color: rgb(204,255,255);color: black;">YRS20BG BLOCK INJECTION</td>
						</tr>
						<tr>							
							<td style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(255,255,102);">1 h : 22 m : 32 s</td>
						</tr>
						
					</tbody>
				</table>
			</div>

			<div style="padding-top: 20px;">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
					<thead>
						<tr>
							<th colspan="5" style="background-color: rgb(220,220,220); text-align: center; color: black; font-weight: bold; font-size:2vw;">Target</th>
						</tr>
						<tr>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Color</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Part</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Qty</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Actual</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Diff</th>
						</tr>
					</thead>
					<tbody id="planTableBody">
						
					</tbody>
				</table>
			</div>

			
		</div>

		<div class="col-xs-6" style="padding-right: 0;">
			<div id="ngList">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th style="width: 20%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Status</th>
							<th style="width: 50%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Reason</th>
							<th style="width: 15%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Start</th>
							<th style="width: 15%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >End</th>
						</tr>
					</thead>
					<tbody id="MesinStatus">
												
					</tbody>
				</table>
			</div>
			<div>
				<center>
					<button id="conf1" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%" onclick="showScan()" class="btn btn-success">RUNNING</button>	
					<button style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%" onclick="showModalStatus('SETUP')" class="btn btn-info">SETUP</button>
					<button id="rework" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%" onclick="showModalStatus('IDDLE')" class="btn btn-warning">IDDLE</button>
					<button style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%" onclick="showModalStatus('TROUBLE')" class="btn btn-danger">TROUBLE</button>
					
				</center>
			</div>

			<div class="input-group" style="padding-top: 10px;">
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
					<i class="glyphicon glyphicon-qrcode"></i>
				</div>
				<input type="text" style="text-align: center; border-color: black;" class="form-control" id="tag" name="tag" placeholder="Tap RFID ..." required disabled>
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
					<i class="glyphicon glyphicon-qrcode"></i>
				</div>
			</div>
			<div style="padding-top: 5px;">
				<table style="width: 100%;" border="1">
					<tbody>
						<tr>
							<td style="width: 1%; font-weight: bold; font-size: 25px; background-color: rgb(220,220,220);">Part</td>
							<td id="model" style="width: 4%; font-size: 25px; font-weight: bold; background-color: rgb(100,100,100); color: yellow;"></td>
							<td style="width: 1%; font-weight: bold; font-size: 25px; background-color: rgb(220,220,220);">Qty</td>
							<td id="key" style="width: 4%; font-weight: bold; font-size: 25px; background-color: rgb(100,100,100); color: yellow;"></td>
							<input type="hidden" id="material_tag">
							<input type="hidden" id="material_number">
							<input type="hidden" id="material_quantity">
							<input type="hidden" id="employee_id">
						</tr>
					</tbody>
				</table>
			</div>

			<div style="padding-top: 5px;">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
					<thead>
						<tr>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;" colspan="2">Operator</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:2vw; width: 30%;" id="op">-</td>
							<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 2vw;" id="op2">-</td>
						</tr>
						<tr>
							<td></td>
							<td><button class="btn btn-success btn-lg pull-right" style='padding-right:10px;padding-left:10px;margin-right: 10px;margin-top: 10px;'>Finish</button>

								<button class="btn btn-warning btn-lg pull-right" style='padding-right:10px;padding-left:10px;margin-right: 10px;margin-top: 10px;'>Start</button>
								<button class="btn btn-danger btn-lg pull-right" style='padding-right:10px;padding-left:10px;margin-right: 10px;margin-top: 10px;'>Cancel</button></td>

						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalOperator">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<label for="exampleInputEmail1">Employee ID</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID Card" required>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalStatus">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header"><center> <b id="statusa" style="font-size: 2vw"></b> </center>
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<label for="exampleInputEmail1">Reason</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="Reason" placeholder="Reason" required><br>
						<button class="btn btn-warning pull-left" data-dismiss="modal">Cancel</button>
						<button class="btn btn-success pull-right" onclick="saveStatus()">Confirm</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-more.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});


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

	jQuery(document).ready(function() {
	// 	$('#modalOperator').modal({
	// 		backdrop: 'static',
	// 		keyboard: false
	// 	});
	// 	$('#operator').val('');
	// 	$('#tag').val('');
	getDataMesinStatusLog();
	getDataMesinShootLog();
	chart();
	});

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#operator').focus();
	});

	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator").val().length >= 8){
				var data = {
					employee_id : $("#operator").val()
				}
				
				$.get('{{ url("scan/middle/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#modalOperator').modal('hide');
						$('#op').html(result.employee.employee_id);
						$('#op2').html(result.employee.name);
						$('#employee_id').val(result.employee.employee_id);
						fillResult(result.employee.employee_id);
						$('#tag').focus();
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator").val("");
			}			
		}
	});

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag").val().length >= 11){
				scanTag($("#tag").val());
			}
			else{
				openErrorGritter('Error!', 'ID Slip Invalid');
				audio_error.play();
				$("#tag").val("");
				$("#tag").focus();
			}			
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	
	function chart() {

				Highcharts.chart('gaugechart', {			        
			    chart: {
			        type: 'gauge',
			        plotBackgroundColor: null,
			        plotBackgroundImage: null,
			        plotBorderWidth: 0,
			        plotShadow: false,
			        height: 250
			    },

			    title: {
			        text: ''
			    },

			    pane: {
			        startAngle: -150,
			        endAngle: 150,
			        background: [{
			            backgroundColor: {
			                linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
			                stops: [
			                    [0, '#FFF'],
			                    [1, '#333']
			                ]
			            },
			            borderWidth: 0,
			            outerRadius: '109%'
			        }, {
			            backgroundColor: {
			                linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
			                stops: [
			                    [0, '#333'],
			                    [1, '#FFF']
			                ]
			            },
			            borderWidth: 1,
			            outerRadius: '107%'
			        }, {
			            // default background
			        }, {
			            backgroundColor: '#DDD',
			            borderWidth: 0,
			            outerRadius: '105%',
			            innerRadius: '103%'
			        }]
			    },

			    // the value axis
			    yAxis: {
			        min: 0,
			        max: 200,

			        minorTickInterval: 'auto',
			        minorTickWidth: 1,
			        minorTickLength: 10,
			        minorTickPosition: 'inside',
			        minorTickColor: '#666',

			        tickPixelInterval: 30,
			        tickWidth: 2,
			        tickPosition: 'inside',
			        tickLength: 10,
			        tickColor: '#666',
			        labels: {
			            step: 2,
			            rotation: 'auto'
			        },
			        title: {
			            text: 'km/h'
			        },
			        plotBands: [{
			            from: 0,
			            to: 120,
			            color: '#55BF3B' // green
			        }, {
			            from: 120,
			            to: 160,
			            color: '#DDDF0D' // yellow
			        }, {
			            from: 160,
			            to: 200,
			            color: '#DF5353' // red
			        }]
			    },

			    series: [{
			        name: 'Speed',
			        data: [80],
			        tooltip: {
			            valueSuffix: ' km/h'
			        }
			    }]

			});
	}

	function getDataMesinShootLog(){
		
		$.get('{{ url("get/getDataMesinShootLog") }}',  function(result, status, xhr){
			if(result.status){
				var BodyMESIN = '';
				$('#planTableBody').html("");
				$.each(result.target, function(key, value) {
					BodyMESIN += '<tr>';
					BodyMESIN += '<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 1vw;" >'+value.color+'</td>';
					BodyMESIN += '<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 1vw;" >'+value.part+'</td>';
					BodyMESIN += '<td style="background-color: rgb(255,255,102); text-align: center; color: #000000; font-size: 1vw;" >'+value.target+'</td>';
					BodyMESIN += '<td style="background-color: rgb(255,255,102); text-align: center; color: #000000; font-size: 1vw;" >'+value.act+'</td>';
					BodyMESIN += '<td style="background-color: rgb(255,204,255); text-align: center; color: #000000; font-size: 1vw;" >'+value.minus+'</td>';
					BodyMESIN += '</tr>';				
				
				});
				$('#planTableBody').append(BodyMESIN);

				openSuccessGritter('Success!', result.message);
				
			}
			else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
				
			}
		});
	}

	function getDataMesinStatusLog(){
		
		$.get('{{ url("get/getDataMesinStatusLog") }}',  function(result, status, xhr){
			if(result.status){

				var BodyMESIN2 = '';
				$('#MesinStatus').html("");
				var no = 1;
				var color ="";
				$.each(result.log, function(key, value) {
					if (no % 2 === 0 ) {
							color = 'style="background-color: #fffcb7;font-size: 20px;"';
						} else {
							color = 'style="background-color: #ffd8b7;font-size: 20px;"';
						}
					BodyMESIN2 += '<tr>';
					BodyMESIN2 += '<td  '+color+'>'+value.status+'</td>';
					BodyMESIN2 += '<td '+color+'>'+value.reason+'</td>';
					BodyMESIN2 += '<td '+color+'>'+value.start_time+'</td>';
					BodyMESIN2 += '<td '+color+'>'+value.end_time+'</td>';
					
					BodyMESIN2 += '</tr>';				
				no++;
				});
				$('#MesinStatus').append(BodyMESIN2);

				$('#statusLog').text(result.log[0].status);
				

				openSuccessGritter('Success!', result.message);
				
			}
			else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
				
			}
		});
	}

	function showModalStatus(status) {
		$("#modalStatus").modal('show');
		$("#statusa").text(status);
	}

	function showScan() {
		$("#tag").removeAttr('disabled');
		$("#tag").val("");
		$("#tag").focus();

		
	}

	

	function saveStatus() {
		var statusa = $("#statusa").text();
		var Reason = $("#Reason").val();

		var data = {
			mesin:"Mesin 1",
			statusa:statusa,
			Reason:Reason
		} 

		if (Reason === "") {
			alert('Reason Must be Filled')
		}else{
			$.get('{{ url("input/statusmesin") }}', data, function(result, status, xhr){
			if(result.status){

				$("#statusa").text('');
				$("#Reason").val('');
				$("#modalStatus").modal('hide');
				openSuccessGritter('Success!', result.message);
				getDataMesinStatusLog();
				getDataMesinShootLog();
				chart();
			}
			else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
				
			}
		});
		}

		
	}

	function getStatusMesin() {
		
			$.get('{{ url("get/statusmesin") }}', data, function(result, status, xhr){
			if(result.status){

				
				openSuccessGritter('Success!', result.message);
			}
			else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
				
			}
		});
		

		
	}

	function conf(){
		if($('#tag').val() == ""){
			openErrorGritter('Error!', 'Tag is empty');
			audio_error.play();
			$("#tag").val("");
			$("#tag").focus();

			return false;
		}

		var tag = $('#tag_material').val();
		var loop = $('#loop').val();
		// var total = 0;
		var count_ng = 0;
		var ng = [];
		var count_text = [];
		for (var i = 1; i <= loop; i++) {
			if($('#count'+i).text() > 0){
				ng.push([$('#ng'+i).text(), $('#count'+i).text()]);
				count_text.push('#count'+i);
				// total += parseInt($('#count'+i).text());
				count_ng += 1;
			}
		}

		var data = {
			loc: $('#loc').val(),
			tag: $('#material_tag').val(),
			material_number: $('#material_number').val(),
			quantity: $('#material_quantity').val(),
			employee_id: $('#employee_id').val(),
			started_at: $('#started_at').val(),
			ng: ng,
			count_text: count_text,
			// total_ng: total,
		}
		

		$.post('{{ url("input/middle/kensa") }}', data, function(result, status, xhr){
			if(result.status){
				var btn = document.getElementById('conf1');
				btn.disabled = false;
				btn.innerText = 'CONFIRM';
				openSuccessGritter('Success!', result.message);
				for (var i = 1; i <= loop; i++) {
					$('#count'+i).text(0);
				}
				$('#model').text("");
				$('#key').text("");
				$('#material_tag').val("");
				$('#material_number').val("");
				$('#material_quantity').val("");
				$('#tag').val("");
				$('#tag').prop('disabled', false);
				fillResult($('#employee_id').val());
				$('#tag').focus();				
			}
			else{
				var btn = document.getElementById('conf1');
				btn.disabled = false;
				btn.innerText = 'CONFIRM';
				audio_error.play();
				openErrorGritter('Error!', result.message);
				$("#tag").val("");
				$("#tag").focus();
			}
		});
	}

	function canc(){
		var loop = $('#loop').val();
		for (var i = 1; i <= loop; i++) {
			$('#count'+i).text(0);
		};
		$('#model').text("");
		$('#key').text("");
		$('#material_tag').val("");
		$('#material_number').val("");
		$('#material_quantity').val("");
		$('#tag').val("");
		$('#tag').prop('disabled', false);
		$('#tag').focus();

	}


	function scanTag(tag){
		$('#tag').prop('disabled', true);
		var data = {
			tag:tag,
			loc:$('#loc').val()
		}
		$.get('{{ url("scan/middle/kensa") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
				$('#model').text(result.middle_inventory.model);
				$('#key').text(result.middle_inventory.key);
				$('#material_tag').val(result.middle_inventory.tag);
				$('#material_number').val(result.middle_inventory.material_number);
				$('#material_quantity').val(result.middle_inventory.quantity);
				$('#started_at').val(result.started_at);
			}
			else{
				$('#tag').prop('disabled', false);
				openErrorGritter('Error!', result.message);
				audio_error.play();
				$("#tag").val("");
				$("#tag").focus();
			}
		});
	}

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '3000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '3000'
		});
	}

	$.date = function(dateObject) {
		var d = new Date(dateObject);
		var day = d.getDate();
		var month = d.getMonth() + 1;
		var year = d.getFullYear();
		if (day < 10) {
			day = "0" + day;
		}
		if (month < 10) {
			month = "0" + month;
		}
		var date = day + "/" + month + "/" + year;

		return date;
	};

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
		return year + "-" + month + "-" + day + " " + h + ":" + m + ":" + s;
	}
</script>
@endsection