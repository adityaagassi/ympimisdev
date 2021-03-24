@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
<!-- 	<h1>
		List Patrol<small class="text-purple"></small>
	</h1> -->
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
        <div class="col-md-7">
          <div id="chart_kategori" style="width: 99%; height: 300px;"></div>
        </div>

        <div class="col-md-5">
          <div id="chart_type" style="width: 99%; height: 300px;"></div>
        </div>

		<div class="col-xs-12" style="text-align: center;margin-bottom: 10px">
			<h3 class="box-title" style="color: white;margin-top: 10px;font-size: 28px;font-weight: bold;background-color: purple;padding: 10px">List Patrol<span class="text-purple"></span>
			</h3>
		</div>
		
		<div class="col-xs-6" style="text-align: center;">
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/audit_patrol') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">5S Patrol GM & Presdir (社長パトロール)</a>
			<a href="{{ url('index/audit_patrol_std') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">EHS & 5S Monthly Patrol</a>
			<!-- <a href="{{ url('') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Patrol Daily Shift 1 dan Shift 2</a>
			<a href="{{ url('') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Patrol Daily Covid</a> -->
			<!-- <hr style="border: 1px solid red"> -->
			
		</div>
		<div class="col-xs-6" style="text-align: center;">
			<span style="font-size: 30px; color: red;"><i class="fa fa-angle-double-down"></i> Monitoring <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/audit_patrol/monitoring') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">GM & Presdir Patrol Monitoring (パトロール監視)</a>
			<a href="{{ url('index/audit_patrol_monitoring/monthly_patrol') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Monthly Patrol Monitoring (パトロール監視)</a>
		</div>

	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/pattern-fill.js")}}"></script>

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
		$('body').toggleClass("sidebar-collapse");
		drawChart();
	});

	function drawChart() {    

	    var datefrom = $('#datefrom').val();
	    var dateto = $('#dateto').val();
	    var status = $('#status').val();

	    var data = {
	      datefrom: datefrom,
	      dateto: dateto,
	      status: status,
	    };

	    $.get('{{ url("fetch/patrol") }}', data, function(result, status, xhr) {
	      if(result.status){

	        var kategori = [];
	        var belum_ditangani_all = [];
	        var sudah_ditangani_all = [];

	        var point_judul = [];
	        var belum_ditangani_type = [];
	        var sudah_ditangani_type = [];

	        $.each(result.data_all, function(key, value) {
	        	if (value.kategori == "EHS & 5S Patrol"){
	        		value.kategori = "EHS 5S Monthly Patrol"
	        	}
	          kategori.push(value.kategori);
	          belum_ditangani_all.push(parseInt(value.jumlah_belum));
	          sudah_ditangani_all.push(parseInt(value.jumlah_sudah));
	        });

	        $.each(result.data_type_all, function(key, value) {
	          point_judul.push(value.point_judul);
	          belum_ditangani_type.push(parseInt(value.jumlah_belum));
	          sudah_ditangani_type.push(parseInt(value.jumlah_sudah));
	        });


	        $('#chart_kategori').highcharts({
	          chart: {
	            type: 'column',
	            backgroundColor: "#fff"
	          },
	          title: {
	            text: "Resume Patrol By Cases",
	            style:{
	              	color : '#000',
	                fontWeight:'Bold'
	            }
	          },
	          xAxis: {
	            type: 'category',
	            categories: kategori,
	            lineWidth:2,
	            lineColor:'#9e9e9e',
	            gridLineWidth: 1,
	            labels: {
	              style: {
	              	color : '#000',
	              	fontSize : '14px',
	                fontWeight:'Bold'
	              }
	            }
	          },
	          yAxis: {
	            lineWidth:2,
	            lineColor:'#fff',
	            type: 'linear',
	            title: {
	              text: 'Total Temuan',
	              style : {
	              	color : '#000'
	              }
	            },
	            stackLabels: {
	              enabled: true,
	              style: {
	                fontWeight: 'bold',
	                color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
	              }
	            }
	          },
	          legend: {
	            itemStyle:{
	              color: "#111",
	              fontSize: "12px"
	            }
	          },
	          plotOptions: {
	            series: {
	              cursor: 'pointer',
	              point: {
	                events: {
	                  click: function () {
	                    ShowModalCategory(this.category,this.series.name);
	                  }
	                }
	              },
	              dataLabels: {
	                enabled: false,
	                format: '{point.y}',
	                style:{
	                	color:"#000"
	                }
	              }
	            },
	            column: {
	              color:  Highcharts.ColorString,
	              stacking: 'percent',
	              pointPadding: 0.93,
	              groupPadding: 0.93,
	              borderWidth: 1,
	              dataLabels: {
	                enabled: true
	              }
	            }
	          },
	          credits: {
	            enabled: false
	          },

	          tooltip: {
	            formatter:function(){
	              return this.series.name+' : ' + this.y;
	            }
	          },
	          series: [
	          {
	            name: 'Temuan Belum Ditangani',
	            data: belum_ditangani_all,
	            color:"#dd4b39"
	          },
	          {
	            name: 'Temuan Sudah Ditangani',
	            data: sudah_ditangani_all,
	            color: "#357a38"
	          }
	          ]
	        })

	        $('#chart_type').highcharts({
	          chart: {
	            type: 'column',
	            backgroundColor: "#fff"
	          },
	          title: {
	            text: "Resume Patrol By Type",
	            style:{
	              	color : '#000',
	                fontWeight:'Bold'
	            }
	          },
	          xAxis: {
	            type: 'category',
	            categories: point_judul,
	            lineWidth:2,
	            lineColor:'#9e9e9e',
	            gridLineWidth: 1,
	            labels: {
	              style: {
	              	color : '#000',
	              	fontSize : '14px',
	                fontWeight:'Bold'
	              }
	            }
	          },
	          yAxis: {
	            lineWidth:2,
	            lineColor:'#fff',
	            type: 'linear',
	            title: {
	              text: 'Total Temuan',
	              style : {
	              	color : '#000'
	              }
	            },
	            stackLabels: {
	              enabled: true,
	              style: {
	                fontWeight: 'bold',
	                color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
	              }
	            }
	          },
	          legend: {
	            itemStyle:{
	              color: "#111",
	              fontSize: "12px"
	            }
	          },
	          plotOptions: {
	            series: {
	              cursor: 'pointer',
	              point: {
	                events: {
	                  click: function () {
	                    ShowModalType(this.category,this.series.name);
	                  }
	                }
	              },
	              dataLabels: {
	                enabled: false,
	                format: '{point.y}',
	                style:{
	                	color:"#000"
	                }
	              }
	            },
	            column: {
	              color:  Highcharts.ColorString,
	              stacking: 'percent',
	              pointPadding: 0.93,
	              groupPadding: 0.93,
	              borderWidth: 1,
	              dataLabels: {
	                enabled: true
	              }
	            }
	          },
	          credits: {
	            enabled: false
	          },

	          tooltip: {
	            formatter:function(){
	              return this.series.name+' : ' + this.y;
	            }
	          },
	          series: [
	          {
	            name: 'Temuan Belum Ditangani',
	            data: belum_ditangani_type,
	            color:"#dd4b39"
	          },
	          {
	            name: 'Temuan Sudah Ditangani',
	            data: sudah_ditangani_type,
	            color: "#357a38"
	          }
	          ]
	        })
	      } else{
	        alert('Attempt to retrieve data failed');
	      }
	    })
	  }

</script>
@endsection