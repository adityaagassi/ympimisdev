@extends('layouts.display')
@section('stylesheets')
<link href="<?php echo e(url("css/jquery.gritter.css")); ?>" rel="stylesheet">
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
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
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}

	.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}
	#loading, #error { display: none; }
</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('header'); ?>
<section class="content-header">
	<h1>
		<?php echo e($title); ?>
		<span style="font-size: 23px"> Tanggal : <?php echo e(date('d-m-Y')); ?></span>

		<!-- <small><span class="text-purple"> <?php echo e($title_jp); ?></span></small> -->
	</h1>
</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Sedang memproses, tunggu sebentar <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<input type="hidden" id="location">
	<div class="row" style="text-align: center;margin-left: 5px;margin-right: 5px">
		<div class="col-xs-12" style="margin-left: 0px;margin-right: 0px;padding-bottom: 0px;padding-left: 0px">
			<div class="row" style="text-align: center;margin-left: 5px;margin-right: 5px">
				<div class="col-md-12" style="margin-left: 16px;margin-right: 0px;padding-bottom: 0px;padding-left: 0px">
			         <div class="col-xs-2" style="padding-left: 0;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From">
						</div>
					</div>
					<div class="col-xs-2" style="padding-left: 0;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Date To" onchange="fetchResume()">
						</div>
					</div> 
					<!-- <div class="col-xs-2" style="padding-left: 0;">
						<button class="btn btn-success pull-left" onclick="fetchResume()" style="font-weight: bold;">
							Search
						</button>
					</div> -->
			    </div>
			    
				<!-- <div class="col-md-12" style="margin-top: 5px; padding:0 !important">
	            	<div id="chart" style="width: 100%; height: 650px"></div>
	        	</div> -->	
	        	<div class="col-xs-6" style="margin-top: 5px; padding:16px !important">
					<div class="box">
						<div class="box-body">
							<span style="font-size: 20px; font-weight: bold;">Belum Diterima Warehouse</span>
							<table class="table table-hover table-striped table-bordered" id="tableResume">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th>No</th>
										<th>Slip Number</th>
										<th>Part's Name</th>
										<!-- <th>Receive Location</th> -->
										<!-- <th>Location</th>
										<th>Category</th>
										<th>Qty</th> -->
										<th>Creator</th>
										<th>Sent</th>
										<th>Status</th>
										<!-- <th>Delete</th> -->
									</tr>
								</thead>
								<tbody id="tableBodyResume">
								</tbody>
							</table>
						</div>
					</div>
				</div>			
				<div class="col-xs-6" style="margin-top: 5px; padding:16px !important">
					<div class="box">
						<div class="box-body">
							<span style="font-size: 20px; font-weight: bold;">Diterima Warehouse</span>
							<table class="table table-hover table-striped table-bordered" id="receivedResume">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th>No</th>
										<th>Slip Number</th>
										<th>Part's Name</th>
										<!-- <th>Receive Location</th>
										<th>Location</th>
										<th>Category</th>
										<th>Qty</th> -->
										<th>Creator</th>
										<th>Received</th>
										<th>Status</th>
										<!-- <th>Delete</th> -->
									</tr>
								</thead>
								<tbody id="receivedBodyResume">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalLocation">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #00a65a;">Pilih Lokasi Anda</h3></center>
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<select class="form-control select2" onchange="fetchResume()" data-placeholder="Pilih Lokasi Anda..." style="width: 100%; font-size: 20px;" id="select_loc">
							<option></option>
							@foreach($storage_locations as $storage_location)
							<option value="{{ $storage_location }}">{{ $storage_location }}</option>
							@endforeach
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script src="<?php echo e(url("js/jquery.gritter.min.js")); ?>"></script>
<script src="<?php echo e(url("js/dataTables.buttons.min.js")); ?>"></script>
<script src="<?php echo e(url("js/buttons.flash.min.js")); ?>"></script>
<script src="<?php echo e(url("js/jszip.min.js")); ?>"></script>
<script src="<?php echo e(url("js/vfs_fonts.js")); ?>"></script>
<script src="<?php echo e(url("js/buttons.html5.min.js")); ?>"></script>
<script src="<?php echo e(url("js/buttons.print.min.js")); ?>"></script>
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script src="<?php echo e(url("js/jsQR.js")); ?>"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		
		$('.select2').select2();
		$('#modalLocation').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		setInterval(function(){
	      fetchResume();
	      // receivedResume($('#location').val());
	      // fetchScrapList($('#location').val());
	    }, 15000);

	    $('.datepicker').datepicker({
		    autoclose: true,
		    format: "yyyy-mm-dd",
		    todayHighlight: true,
		    // startView: "months", 
		    // minViewMode: "months",
		    autoclose: true,
		   });
	  	$('#tanggal').datepicker({
		    autoclose: true,
		    format: 'yyyy-mm-dd',
		    todayHighlight: true
		   });
		
	});
	function fetchScrapDate() {
		var data = {
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val()
		}
		$.get('{{ url("fetch/qa/display/incoming/ng_rate") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					$('#last_update').html('<span><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</span>');
				}
			}
		});
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

	function plusCount(){
		$('#quantity').val(parseInt($('#quantity').val())+1);
	}

	function minusCount(){
		$('#quantity').val(parseInt($('#quantity').val())-1);
	}


	function fetchResume(){
		$('#location').val($("#select_loc").val());
		var loc = $("#location").val();
		
		// receivedResume($('#location').val());
		// fetchScrapList($('#location').val());
		var data = {
			loc:loc,
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val()
		}
		$.get('<?php echo e(url("scrap/resume/list/wip")); ?>', data, function(result, status, xhr){
			if(result.status){
				$('#tableResume').DataTable().clear();
				$('#tableResume').DataTable().destroy();
				var tableData = '';
				$('#tableBodyResume').html("");
				$('#tableBodyResume').empty();
				
				var count = 1;
				$.each(result.resumes, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ count +'</td>';
					tableData += '<td>'+ value.slip +'-SC</td>';
					tableData += '<td>'+ value.material_description +'</td>';
					// tableData += '<td>'+ value.issue_location +'</td>';
					// tableData += '<td>'+ value.receive_location +'</td>';
					// tableData += '<td>'+ value.category +'</td>';
					// tableData += '<td>'+ value.quantity +'</td>';
					tableData += '<td>'+ value.name +'</td>';
					tableData += '<td>'+ value.tanggal +'</td>';
					tableData += '<td style="background-color: #FF9966;">'+ 'Dikirim Ke Gudang' +'</td>';
					// tableData += '<td><center><button class="btn btn-danger" onclick="deleteScrap('+value.id+')"><i class="fa fa-trash"></i></button></center></td>';
					tableData += '</tr>';
					count += 1;
				});

				$('#tableBodyResume').append(tableData);
				var tableResume = $('#tableResume').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
						buttons:[
						{
							extend: 'pageLength',
							className: 'btn btn-default',
						}
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching': true,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});

				$('#receivedResume').DataTable().clear();
				$('#receivedResume').DataTable().destroy();
				var tableData1 = '';
				$('#receivedBodyResume').html("");
				$('#receivedBodyResume').empty();

				var count1 = 1;
				$.each(result.resumes1, function(key, value) {
					tableData1 += '<tr>';
					tableData1 += '<td>'+ count1 +'</td>';
					tableData1 += '<td>'+ value.slip +'-SC</td>';
					tableData1 += '<td>'+ value.material_description +'</td>';
					// tableData1 += '<td>'+ value.issue_location +'</td>';
					// tableData1 += '<td>'+ value.receive_location +'</td>';
					// tableData1 += '<td>'+ value.category +'</td>';
					// tableData1 += '<td>'+ value.quantity +'</td>';
					tableData1 += '<td>'+ value.name +'</td>';
					tableData1 += '<td>'+ value.tanggal +'</td>';
					tableData1 += '<td style="background-color: #33FF66;">'+ 'Diterima Ke Gudang' +'</td>';
					// tableData1 += '<td><center><button class="btn btn-danger" onclick="deleteScrap('+value.id+')"><i class="fa fa-trash"></i></button></center></td>';
					tableData1 += '</tr>';
					count1 += 1;
				});

				$('#receivedBodyResume').append(tableData1);
				var receivedResume = $('#receivedResume').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
						buttons:[
						{
							extend: 'pageLength',
							className: 'btn btn-default',
						}
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching': true,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});

				// openSuccessGritter('Success!', result.message);
				$('#modalLocation').modal('hide');
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function receivedResume(loc){
		$('#location').val(loc);
		var data = {
			loc:loc
		}
		$.get('<?php echo e(url("scrap/resume/list/wh")); ?>', data, function(result, status, xhr){
			if(result.status){
				$('#receivedResume').DataTable().clear();
				$('#receivedResume').DataTable().destroy();
				var tableData = '';
				$('#receivedBodyResume').html("");
				$('#receivedBodyResume').empty();
				
				var count = 1;
				$.each(result.resumes, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ count +'</td>';
					tableData += '<td>'+ value.slip +'-SC</td>';
					tableData += '<td>'+ value.material_description +'</td>';
					// tableData += '<td>'+ value.issue_location +'</td>';
					// tableData += '<td>'+ value.receive_location +'</td>';
					// tableData += '<td>'+ value.category +'</td>';
					// tableData += '<td>'+ value.quantity +'</td>';
					tableData += '<td>'+ value.name +'</td>';
					tableData += '<td>'+ value.tanggal +'</td>';
					tableData += '<td style="background-color: #33FF66;">'+ 'Diterima Ke Gudang' +'</td>';
					// tableData += '<td><center><button class="btn btn-danger" onclick="deleteScrap('+value.id+')"><i class="fa fa-trash"></i></button></center></td>';
					tableData += '</tr>';
					count += 1;
				});

				$('#receivedBodyResume').append(tableData);
				var receivedResume = $('#receivedResume').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
						buttons:[
						{
							extend: 'pageLength',
							className: 'btn btn-default',
						}
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching': true,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});

				// openSuccessGritter('Success!', result.message);
				$('#modalLocation').modal('hide');
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	
	function deleteScrap(id){

		if(confirm("Apa anda yakin mendelete slip scrap?")){
			var data = {
				id:id
			}
			$.post('{{ url("delete/scrap") }}', data, function(result, status, xhr){
				if(result.status){
					var loc = $('#location').val();	

					fetchResume();
					openSuccessGritter('Success!', result.message);
					// console.log(result);
				}
				else{
					openErrorGritter('Error!', result.message);
				}

			});
		}
		else{
			return false;
		}
	}

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '<?php echo e(url("images/image-screen.png")); ?>',
			sticky: false,
			time: '2000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '<?php echo e(url("images/image-stop.png")); ?>',
			sticky: false,
			time: '2000'
		});
	}

	function fetchScrapList(loc){
		$('#location').val(loc);
		var data = {
			loc:loc
		}
		$.get('<?php echo e(url("scrap/data/monitoring/wip")); ?>', data, function(result, status, xhr){
			if(result.status){

				var jml = [], dept = [], jml_dept = [], list = [], receive = [];
          		var category = [];

          $.each(result.datas, function(key, value) {
            category.push(value.issue_location);
            list.push(parseInt(value.LScrap));
            receive.push(parseInt(value.RScrap));
          });

          var today = new Date();
          var dd = String(today.getDate()).padStart(2, '0');
          var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
          var yyyy = today.getFullYear();

          today = dd + '-' + mm + '-' + yyyy;


          $('#chart').highcharts({

            chart: {
            type: 'column'
            },

            title: {
                text: 'Monitoring Scrap WIP'
            },
            subtitle: {
                text: today
            }, 
            xAxis: {
                categories: category
            },

            credits: {
                enabled: false
            },

            yAxis: {
                allowDecimals: false,
                min: 0,
                title: {
                    text: 'Total Scrap'
                }
            },

            plotOptions: {
              series: {
              cursor: 'pointer',
              dataLabels: {
                enabled: true,
                format: '{point.y}'
              }
            },
                // column: {
                //     stacking: 'normal'
                // }
            },

            series: [{
                name: 'Diterima WH',
                data: receive
            }, {
                name: 'Belum Diterima WH',  
                data: list
            }]
          })
				// openSuccessGritter('Success!', result.message);
				$('#modalLocation').modal('hide');
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}
	Highcharts.createElement('link', {
          href: '{{ url("fonts/UnicaOne.css")}}',
          rel: 'stylesheet',
          type: 'text/css'
        }, null, document.getElementsByTagName('head')[0]);

        Highcharts.theme = {
          colors: ['#2b908f', '#f45b5b', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
          '#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
          chart: {
            backgroundColor: {
              linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
              stops: [
              [0, '#2a2a2b']
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
</script>
@stop