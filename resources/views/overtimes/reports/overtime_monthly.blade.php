@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

  .morecontent span {
    display: none;
  }
  .morelink {
    display: block;
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
	.dataTable > thead > tr > th[class*="sort"]:after{
		content: "" !important;
	}
	#queueTable.dataTable {
		margin-top: 0px!important;
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-left: 0px; padding-right: 0px;">
	<div class="row">
		<div class="col-md-12">
			<div class="col-md-12">
				<div class="col-md-2 pull-right">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border-color: #00a65a">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="tgl" onchange="drawChart()" placeholder="Select Date" style="border-color: #00a65a">
					</div>
					<br>
				</div>
			</div>
			<div class="col-md-12">
				<div id="over_control" style="width: 100%; height: 550px;"></div>
			</div>
			<div class="col-md-12">
				<div class="box box-solid">
					<div class="box-body">
						<div class="col-md-12">
							<div class="col-md-2">
								<div class="description-block border-right" style="color: #f76111">
									<h5 class="description-header" style="font-size: 50px;">
										<span class="description-percentage" id="tot_budget"></span>
									</h5>      
									<span class="description-text" style="font-size: 35px;">Total Budget<br><span >総予算</span></span>   
								</div>
							</div>

							<div class="col-md-3">
								<div class="description-block border-right" style="color: #02ff17">
									<h5 class="description-header" style="font-size: 50px;">
										<span class="description-percentage" id="tot_day_budget"></span>
									</h5>      
									<span class="description-text" style="font-size: 35px;">Total Forecast<br><span >累計見込み</span></span>   
								</div>
							</div>

							<div class="col-md-3">
								<div class="description-block border-right" style="color: #7300ab" >
									<h5 class="description-header" style="font-size: 50px; ">
										<span class="description-percentage" id="tot_act"></span>
									</h5>      
									<span class="description-text" style="font-size: 35px;">Total Actual<br><span >総実績</span></span>   
								</div>
							</div>
							<div class="col-md-2">
								<div class="description-block border-right text-green" id="diff_text">
									<h5 class="description-header" style="font-size: 50px;">
										<span class="description-percentage" id="tot_diff"></span>
									</h5>      
									<span class="description-text" style="font-size: 35px;">Difference</span>
									<br><span class="description-text" style="font-size: 18px">(FQ-ACT)</span>
									<br><span class="description-text" style="font-size: 35px;">差異</span>   
								</div>
							</div>
							<div class="col-md-2">
								<div class="description-block border-right text-yellow">
									<h5 class="description-header" style="font-size: 50px;">
										<span class="description-percentage" id="avg"></span>
									</h5>      
									<span class="description-text" style="font-size: 35px;">Average<br><span >平均</span></span>   
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="modal fade" id="myModal">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<h4 style="float: right; " id="modal-title"></h4> 
							<h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<div id="progressbar2">
										<center>
											<i class="fa fa-refresh fa-spin" style="font-size: 6em;"></i> 
											<br><h4>Loading ...</h4>
										</center>
									</div>
									<table class="table table-bordered table-stripped table-responsive" style="width: 100%" id="example2">
										<thead style="background-color: rgba(126,86,134,.7);">
											<tr>
												<th>No</th>
												<th>NIK</th>
												<th>Nama</th>
												<th>Total Lembur (jam)</th>
												<th>Keperluan</th>
											</tr>
										</thead>
										<tbody id="tabelDetail"></tbody>
										<tfoot>
											
												<th colspan="3" style="font-weight: bold; size: 25px; text-align: center;">TOTAL </th>
												<th id="tot" style="font-weight: bold; size: 25px"></th>
                        <th  style="font-weight: bold; size: 25px"></th>
											
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
	</div>

</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		drawChart();
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function drawChart() {

		var tanggal = $('#tgl').val();

		var data = {
			tgl:tanggal
		}

		$.get('{{ url("fetch/report/overtime_report_control") }}', data, function(result) {

    // -------------- CHART OVERTIME REPORT CONTROL ----------------------

    var xCategories2 = [];
    var seriesDataBudget = [];
    var seriesDataAktual = [];
    var budgetHarian = [];
    var ctg, tot_act = 0, avg = 0, tot_budget = 0;
    var tot_day_budget = 0, tot_diff;

    for(var i = 0; i < result.report_control.length; i++){
    	ctg = result.report_control[i].cost_center_name;
    	tot_act += result.report_control[i].act;
    	tot_budget += result.report_control[i].tot;
    	tot_day_budget += result.report_control[i].jam_harian;

    	seriesDataBudget.push(Math.round(result.report_control[i].tot * 100) / 100);
    	seriesDataAktual.push(Math.round(result.report_control[i].act * 100) / 100);
    	budgetHarian.push(Math.round(result.report_control[i].jam_harian * 100) / 100);
    	if(xCategories2.indexOf(ctg) === -1){
    		xCategories2[xCategories2.length] = ctg;
    	}
    }

    tot_diff = tot_day_budget - tot_act;

    tot_budget = Math.round(tot_budget * 100) / 100;
    tot_day_budget = Math.round(tot_day_budget * 100) / 100;
    tot_act = Math.round(tot_act * 100) / 100;
    tot_diff = Math.round(tot_diff * 100) / 100;

    var tot_budget2 = tot_budget.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
    var tot_day_budget2 = tot_day_budget.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
    var tot_act2 = tot_act.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
    var tot_diff2 = tot_diff.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");

    $("#tot_budget").text(tot_budget);
    $("#tot_day_budget").text(tot_day_budget2);
    $("#tot_act").text(tot_act2);

    if (tot_diff > 0) {
    	$('#diff_text').removeClass('text-red').addClass('text-green');
    	$("#tot_diff").html(tot_diff2);
    }
    else {
    	$('#diff_text').removeClass('text-green').addClass('text-red');
    	$("#tot_diff").html(tot_diff2);
    }

    avg = tot_act / result.emp_total[0].jml;
    avg = Math.round(avg * 100) / 100;
    $("#avg").html(avg);

    Highcharts.SVGRenderer.prototype.symbols['c-rect'] = function (x, y, w, h) {
    	return ['M', x, y + h / 2, 'L', x + w, y + h / 2];
    };

    Highcharts.chart('over_control', {
    	chart: {
    		spacingTop: 10,
    		type: 'column'
    	},
    	title: {
    		text: '<span style="font-size: 18pt;">Overtime Control</span><br><center><span style="color: rgba(96, 92, 168);">'+ result.report_control[0].tanggal +'</center></span>',
    		useHTML: true
    	},
    	credits:{
    		enabled:false
    	},
    	legend: {
    		itemStyle: {
    			color: '#000000',
    			fontWeight: 'bold',
    			fontSize: '20px'
    		}
    	},
    	yAxis: {
    		tickInterval: 10,
    		min:0,
    		allowDecimals: false,
    		title: {
    			text: 'Amount of Overtime (hours)'
    		}
    	},
    	xAxis: {
    		labels: {
    			style: {
    				color: 'rgba(75, 30, 120)',
    				fontSize: '12px',
    				fontWeight: 'bold'
    			}
    		},
    		categories: xCategories2
    	},
    	tooltip: {
    		formatter: function () {
    			return '<b>' + this.series.name + '</b><br/>' +
    			this.point.y + ' ' + this.series.name.toLowerCase();
    		}
    	},
    	plotOptions: {
    		column: {
    			pointPadding: 0.93,
    			cursor: 'pointer',
    			point: {
    				events: {
    					click: function () {
    						modalTampil(this.category, result.report_control[0].tanggal);
    					}
    				}
    			},
    			minPointLength: 3,
    			dataLabels: {
    				allowOverlap: true,
    				enabled: true,
    				y: -25,
    				style: {
    					color: 'black',
    					fontSize: '13px',
    					textOutline: false,
    					fontWeight: 'bold',
    				},
    				rotation: -90
    			},
    			pointWidth: 15,
    			pointPadding: 0,
    			borderWidth: 0,
    			groupPadding: 0.1,
    			animation: false,
    			opacity: 0.2
    		},
    		scatter : {
    			dataLabels: {
    				enabled: false
    			},
    			animation: false
    		}
    	},
    	series: [{
    		name: 'Budget Accumulative',
    		data: seriesDataBudget,
    		color: "#f76111"
    	}, {
    		name: 'Actual Accumulative',
    		data: seriesDataAktual,
    		color: "#7300ab"
    	},
    	{
    		name: 'Forecast Production',
    		marker: {
    			symbol: 'c-rect',
    			lineWidth:4,
    			lineColor: '#02ff17',
    			radius: 10,
    		},
    		type: 'scatter',
    		data: budgetHarian
    	}]
    });
});
}

function total_budget(costCenter, date) {
	$.ajax({
		type: "GET",
		url: '{{url("fetch/cc/budget")}}',
		data: {
			cc : costCenter,
			tgl : date
		},
		dataType: 'json',
		success: function(data) {
			$("#modal-title").html(costCenter+" ( &Sigma; Budget "+data.datas[0].budget+" )");
		}
	})

}

function modalTampil(costCenter, date) {
	$("#myModal").modal('show');
      var showChar = 100;  // How many characters are shown by default
      var ellipsestext = "...";
      var moretext = "Show more >";
      var lesstext = "< Show less";

      total_budget(costCenter, date);

      $.ajax({
      	type: "GET",
      	url: "{{url('fetch/chart/control/detail')}}",
      	data: {
      		cc : costCenter,
      		tgl : date
      	},
      	dataType: 'json',
      	beforeSend: function () {
      		$('#progressbar2').show();
      		$('#example2').hide();
      	},
      	complete: function () {
      		$('#progressbar2').hide();
      		$('#example2').show();
      	},
      	success: function(data) {
      		$("#tabelDetail").empty();
      		var no = 1;
      		var jml = 0;

          console.log(data);
          var dataT = '';
          var no = 1;

          for (var i = 0; i <   data.datas.length; i++) {
            
            dataT += '<tr>';
            dataT += '<td>'+ no++; +'</td>';
            dataT += '<td>'+ data.datas[i].nik +'</td>';
            dataT += '<td>'+ data.datas[i].namaKaryawan +'</td>';           
            dataT += '<td>'+ data.datas[i].jam +'</td>';
            dataT += '<td style="text-align:left"> <span class="more">'+ data.datas[i].kep +'</span></td>';
            dataT += '</tr>';
            jml += data.datas[i].jam;
          }
          $("#tabelDetail").append(dataT);

            

      		// $.each(data, function(i, item) {
      		// 	if (item[0] != ""){
      		// 		var newdiv1 = $( "<tr>"+                  
      		// 			"<td>"+no+"</td><td>"+item[0]+"</td>"+
      		// 			"<td>"+item[1]+"</td><td>"+item[2]+"</td><td><span class='more'>"+item[3]+"</span></td>"+
      		// 			"</tr>");
      		// 		no++;
      		// 		jml += item[2];

      		// 		$("#tabelDetail").append(newdiv1);
      		// 	}
      		// });

      

      $('.more').each(function() {
          var content = $(this).html();



          if(content.length > showChar) {

            var c = content.substr(0, showChar);
            var h = content.substr(showChar, content.length - showChar);

            var html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';

            $(this).html(html);
          }

        });

        $(".morelink").click(function(){
          if($(this).hasClass("less")) {
            $(this).removeClass("less");
            $(this).html(moretext);
          } else {
            $(this).addClass("less");
            $(this).html(lesstext);
          }
          $(this).parent().prev().toggle();
          $(this).prev().toggle();
          return false;
        });

      		$("#tot").text(jml);
      	}
      })
  }

  $('#tgl').datepicker({
  	<?php $tgl_max = date('d-m-Y') ?>
  	autoclose: true,
  	format: "dd-mm-yyyy",
  	endDate: '<?php echo $tgl_max ?>',
  });

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
</script>
@endsection