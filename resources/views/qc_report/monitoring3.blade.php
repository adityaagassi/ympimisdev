@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="https://code.highcharts.com/gantt/highcharts-gantt.js"></script>
<style type="text/css">
thead input {
  width: 100%;
  padding: 3px;
  box-sizing: border-box;
}
thead>tr>th{
  text-align:center;
}
tfoot>tr>th{
  text-align:center;
}
td:hover {
  overflow: visible;
}
table > thead > tr > th{
  border:2px solid #f4f4f4;
}

table.table-bordered{
  border:1px solid black;
}


table.table-bordered > thead > tr > th{
  /*border:1px solid black;*/
  border:1px solid #607d8b;
  font-size: 23px;
}
table.table-bordered > tbody > tr > td{
  border-collapse: collapse;
  padding:10px;
  vertical-align: middle;
}
table.table-bordered > tfoot > tr > th{
  border:1px solid rgb(211,211,211);
}
#loading, #error { display: none; }

</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    CPAR <span class="text-purple">Grafik</span>
    <small>Berdasarkan Bulan<span class="text-purple"> </span></small>
  </h1>
  <ol class="breadcrumb" id="last_update">
  </ol>
</section>
@endsection


@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
  <input type="hidden" value="<?= date('Y-m-d') ?>" id="tgl">
  <div class="row">
    <div class="col-md-12">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
              <h3 class="box-title"><b style="font-size: 18pt">Monitoring <span class="text-purple">Digital Komplain</span></b></h3>
          </div>
          <div class="box-body">
              <!-- <div id="container"></div> -->

              <div class="table-responsive">
                <table class="table no-margin" id="tabelmonitor">
                  <thead>
                    <tr>
                      <th rowspan="2" style="vertical-align: middle;width: 10%">Bagian</th>
                      <th rowspan="2" style="vertical-align: middle;width: 25%">Pembuatan CPAR</th>
                      <th rowspan="2" style="vertical-align: middle;width: 25%">Penanganan Komplain</th>
                      <th rowspan="2" style="vertical-align: middle;width: 25%">Verifikasi</th>
                      <th rowspan="2" style="vertical-align: middle;width: 15%">Sudah Ditangani</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td style="vertical-align: middle;text-align: center;font-size: 20px;font-weight: bold">Assembly (WI-A)</td>
                      <td>
                        <div id="container" style="min-width: 210px; height: 300px; max-width: 600px; margin: 0 auto"></div>
                      </td>
                      <td><div id="container2" style="min-width: 210px; height: 300px; max-width: 600px; margin: 0 auto"></div></td>
                      <td><div id="container3" style="min-width: 210px; height: 300px; max-width: 600px; margin: 0 auto"></div></td>
                      <td style="vertical-align: middle;text-align: center;font-size: 40px;font-weight: bold">
                        30 Komplain
                      </td>
                    </tr>
                    <tr>
                      <td style="vertical-align: middle;text-align: center;font-size: 20px;font-weight: bold">Educational Instrument (EI)</td>
                      <td>
                        <div id="container4" style="min-width: 210px; height: 300px; max-width: 600px; margin: 0 auto"></div>
                      </td>
                      <td><div id="container5" style="min-width: 210px; height: 300px; max-width: 600px; margin: 0 auto"></div></td>
                      <td><div id="container6" style="min-width: 210px; height: 300px; max-width: 600px; margin: 0 auto"></div></td>
                      <td style="vertical-align: middle;text-align: center;font-size: 40px;font-weight: bold">
                        30 Komplain
                      </td>
                    </tr>
                    <tr>
                      <td>Part Process (PP)</td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                    </tr>
                    <tr>
                      <td>Welding Surface Treatment (WST)</td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                    </tr>
                    <tr>
                      <td>Procurement</td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                    </tr>
                    <tr>
                      <td>Logistic</td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                    </tr>
                  </tbody>
                </table>
            </div>

<!--             <div class="box-footer">
              <div class="row">
                <div class="col-sm-3 col-xs-6">
                  <div class="description-block border-right">
                    <span class="description-percentage text-green"><i class="fa fa-caret-up"></i> 17%</span>
                    <h5 class="description-header">$35,210.43</h5>
                    <span class="description-text">TOTAL REVENUE</span>
                  </div>
                  
                </div>
                <div class="col-sm-3 col-xs-6">
                  <div class="description-block border-right">
                    <span class="description-percentage text-yellow"><i class="fa fa-caret-left"></i> 0%</span>
                    <h5 class="description-header">$10,390.90</h5>
                    <span class="description-text">TOTAL COST</span>
                  </div>
                  
                </div>
                <div class="col-sm-3 col-xs-6">
                  <div class="description-block border-right">
                    <span class="description-percentage text-green"><i class="fa fa-caret-up"></i> 20%</span>
                    <h5 class="description-header">$24,813.53</h5>
                    <span class="description-text">TOTAL PROFIT</span>
                  </div>
                  
                </div>
                <div class="col-sm-3 col-xs-6">
                  <div class="description-block">
                    <span class="description-percentage text-red"><i class="fa fa-caret-down"></i> 18%</span>
                    <h5 class="description-header">1200</h5>
                    <span class="description-text">GOAL COMPLETIONS</span>
                  </div>
                  
                </div>
              </div>
            </div> -->

            

        </div>
      </div>
    </div>
  </div>

</section>


@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<!-- <script src="{{ url("js/highcharts-3d.js")}}"></script> -->
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>

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
    // drawgantt();
      // fetchTable();
      // setInterval(fetchTable, 10000);
  });

  function fetchTable(){
      var data = {
        tgl : $('#tgl').val()
      }
      $.get('{{ url("index/qc_report/fetchMonitoring") }}', data, function(result, status, xhr){
        if(xhr.status == 200){
          if(result.status){

            // $("#tabelmonitor").html("");
            $("#tabelisi").find("td").remove();  

            // foreach()

            $.each(result.datas, function(key, value) {
                if (value.cpar_no) {
                  $("#tabelisi").append("<tr><td>"+value.cpar_no+"</td><td>"+value.detail_problem+"</td></tr>");
                }
            })

          }
        }
      })
  }

  Highcharts.chart('container', {
    chart: {
        type: 'pie'
    },
    title: {
        text: ''
    },
    // tooltip: {
    //     pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    // },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                distance: '-30%',
                format: '<b>{point.name}</b> '
                // : {point.percentage:.1f%}
            },
            showInLegend: true
        }
    },
    credits:{
      enabled:false
    },
    series: [{
        name: 'Brands',
        colorByPoint: true,
        data: [{
            name: 'CPAR 1 <br>(30%)',
            y: 33.3,
            // sliced: true,
            // selected: true
        }, {
            name: 'CPAR 2 <br>(40%)',
            y: 33.33
        }, {
            name: 'CPAR 3 <br>(70%)',
            y: 33.33
        }]
    }]
});

  Highcharts.chart('container2', {
    chart: {
        type: 'pie'
    },
    title: {
        text: ''
    },
    // tooltip: {
    //     pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    // },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                distance: '-30%',
                format: '<b>{point.name}</b> '
                // : {point.percentage:.1f%}
            },
            showInLegend: true
        }
    },
    credits:{
      enabled:false
    },
    series: [{
        name: 'Brands',
        colorByPoint: true,
        data: [{
            name: 'CPAR 4 <br>(30%)',
            y: 50,
            // sliced: true,
            // selected: true
        }, {
            name: 'CPAR 5 <br>(60%)',
            y: 50
        }]
    }]
});

  Highcharts.chart('container3', {
    chart: {
        type: 'pie'
    },
    title: {
        text: ''
    },
    // tooltip: {
    //     pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    // },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                distance: '-30%',
                format: '<b>{point.name}</b> '
                // : {point.percentage:.1f%}
            },
            showInLegend: true
        }
    },
    credits:{
      enabled:false
    },
    series: [{
        name: 'Brands',
        colorByPoint: true,
        data: [{
            name: 'CPAR 5<br>',
            y: 100
        }]
    }]
});

  Highcharts.chart('container4', {
    chart: {
        type: 'pie'
    },
    title: {
        text: ''
    },
    // tooltip: {
    //     pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    // },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                distance: '-30%',
                format: '<b>{point.name}</b> '
                // : {point.percentage:.1f%}
            },
            showInLegend: true
        }
    },
    credits:{
      enabled:false
    },
    series: [{
        name: 'Brands',
        colorByPoint: true,
        data: [{
            name: 'CPAR 1 <br>(30%)',
            y: 33.3,
            // sliced: true,
            // selected: true
        }, {
            name: 'CPAR 2 <br>(40%)',
            y: 33.33
        }, {
            name: 'CPAR 3 <br>(70%)',
            y: 33.33
        }]
    }]
});

Highcharts.chart('container5', {
    chart: {
        type: 'pie'
    },
    title: {
        text: ''
    },
    // tooltip: {
    //     pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    // },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                distance: '-30%',
                format: '<b>{point.name}</b> '
                // : {point.percentage:.1f%}
            },
            showInLegend: true
        }
    },
    credits:{
      enabled:false
    },
    series: [{
        name: 'Brands',
        colorByPoint: true,
        data: [{
            name: 'CPAR 4 <br>(30%)',
            y: 50,
            // sliced: true,
            // selected: true
        }, {
            name: 'CPAR 5 <br>(60%)',
            y: 50
        }]
    }]
});

Highcharts.chart('container6', {
    chart: {
        type: 'pie'
    },
    title: {
        text: ''
    },
    // tooltip: {
    //     pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    // },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                distance: '-30%',
                format: '<b>{point.name}</b> '
                // : {point.percentage:.1f%}
            },
            showInLegend: true
        }
    },
    credits:{
      enabled:false
    },
    series: [{
        name: 'Brands',
        colorByPoint: true,
        data: [{
            name: 'CPAR 5<br>',
            y: 100
        }]
    }]
});

  $('.datepicker').datepicker({
    format: "yyyy-mm",
    startView: "months", 
    minViewMode: "months",
    autoclose: true
  });  

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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
@stop