@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

table.table-bordered{
  border:1px solid rgb(150,150,150);
}
table.table-bordered > thead > tr > th{
  border:1px solid rgb(54, 59, 56) !important;
  text-align: center;
  background-color: #212121;  
  color:white;
}
table.table-bordered > tbody > tr > td{
  border:1px solid rgb(54, 59, 56);
  background-color: #212121;
  color: white;
  vertical-align: middle;
  text-align: center;
  padding:3px;
}
table.table-condensed > thead > tr > th{   
  color: black
}
table.table-bordered > tfoot > tr > th{
  border:1px solid rgb(150,150,150);
  padding:0;
}
table.table-bordered > tbody > tr > td > p{
  color: #abfbff;
}

table.table-striped > thead > tr > th{
  border:1px solid black !important;
  text-align: center;
  background-color: rgba(126,86,134,.7) !important;  
}

table.table-striped > tbody > tr > td{
  border: 1px solid #eeeeee !important;
  border-collapse: collapse;
  color: black;
  padding: 3px;
  vertical-align: middle;
  text-align: center;
  background-color: white;
}

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
  color: white;
}
#tabelmonitor{
  font-size: 0.83vw;
}

.zoom{
   -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  -webkit-animation: zoomin 5s ease-in infinite;
  animation: zoomin 5s ease-in infinite;
  transition: all .5s ease-in-out;
  overflow: hidden;
}
@-webkit-keyframes zoomin {
  0% {transform: scale(0.7);}
  50% {transform: scale(1);}
  100% {transform: scale(0.7);}
}
@keyframes zoomin {
  0% {transform: scale(0.7);}   
  50% {transform: scale(1);}
  100% {transform: scale(0.7);}
} /*End of Zoom in Keyframes */

/* Zoom out Keyframes */
@-webkit-keyframes zoomout {
  0% {transform: scale(0);}
  50% {transform: scale(0.5);}
  100% {transform: scale(0);}
}
@keyframes zoomout {
    0% {transform: scale(0);}
  50% {transform: scale(0.5);}
  100% {transform: scale(0);}
}/*End of Zoom out Keyframes */


#loading, #error { display: none; }

</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    CPAR <span class="text-purple">Display</span>
    <!-- <small>Berdasarkan Bulan<span class="text-purple"> </span></small> -->
  </h1>
  <ol class="breadcrumb" id="last_update">
  </ol>
</section>
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0; padding-bottom: 0">
  <div class="row">
    <div class="col-md-12" style="padding: 1px !important">
        <div class="col-md-2">
          <select class="form-control select2" id="fy" name="fy" placeholder="Select Fiscal Year" style="border-color: #605ca8" onchange="changefy()">
            <option value="">Select Fiscal Year</option>
            @foreach($fys as $fiscal)
              <option value="{{ $fiscal->fiscal_year }}">{{ $fiscal->fiscal_year }}</option>
            @endforeach
          </select>
        </div>
        <!-- <div class="col-xs-2">
          <button class="btn btn-success btn-sm" onclick="">Update Chart</button>
        </div> -->
      </div>
      
      <div class="col-md-12" style="margin-top: 5px; padding-right: 0;padding-left: 10px">
          <div id="chartresume" style="width: 99%"></div>
      </div>
      <div class="col-md-6" style="margin-top: 5px; padding-right: 0;padding-left: 10px">
          <div id="charteksternal" style="width: 99%"></div>
      </div>
      <div class="col-md-6" style="margin-top: 5px; padding-right: 0;padding-left: 10px">
          <div id="chartsupplier" style="width: 99%"></div>
      </div>
<!--       <div class="col-md-4" style="margin-top: 5px; padding-right: 0;padding-left: 10px">
          <div id="chartresume" style="width: 99%"></div>
      </div> -->
      <!-- <div class="col-md-12" style="padding-right: 0;padding-left: 10px">
          <table id="tabelmonitor" class="table table-bordered" style="margin-top: 5px; width: 99%">
            <thead style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 12px;font-weight: bold">
              <tr>
                <th style="width: 5%; padding: 0;vertical-align: middle;font-size: 16px;" rowspan="2">No</th>
                <th style="width: 5%; padding: 0;vertical-align: middle;font-size: 16px;" rowspan="2">Date</th>
                <th style="width: 10%; padding: 0;vertical-align: middle;font-size: 16px;" rowspan="2">Subject</th>
                <th style="width: 5%; padding: 0;vertical-align: middle;font-size: 16px;">Location</th>
                <th style="width: 5%; padding: 0;vertical-align: middle;font-size: 16px;">Invoice Number</th>
                <th style="width: 5%; padding: 0;vertical-align: middle;font-size: 16px;">Qty Cek</th>
                <th style="width: 5%; padding: 0;vertical-align: middle;font-size: 16px;">Qty NG</th>
                <th style="width: 5%; padding: 0;vertical-align: middle;font-size: 16px;">Action</th>
                <th style="width: 5%; padding: 0;vertical-align: middle;font-size: 16px;">File</th>
              </tr>
              <tr>
               
              </tr>
            </thead>
            <tbody id="tabelisi">
            </tbody>
            <tfoot>
            </tfoot>
          </table>
      </div> -->
    </div>
  </div>

  <div class="modal fade" id="myModal">
    <div class="modal-dialog" style="width:1250px;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="float: right;" id="modal-title"></h4>
          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
          <br><h4 class="modal-title" id="judul_table"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="example2" class="table table-striped table-bordered table-hover" style="width: 100%;"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>No CPAR</th>
                    <th>Category</th> 
                    <th>Complain</th>
                    <th>Location</th>
                    <th>Request Date</th>
                    <th>Due Date</th>
                    <th>Departement</th>
                    <th>Source Of Complaint</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
        </div>
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
<script src="{{ url("js/accessibility.js")}}"></script>
<script src="{{ url("js/drilldown.js")}}"></script>

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
    $('#myModal').on('hidden.bs.modal', function () {
      $('#example2').DataTable().clear();
    });
    $('.select2').select2();

    drawChartResume();
    drawChartEksternal();
    drawChartSupplier();
    // drawChart();
  });

  function changefy(){
     drawChartResume();
     drawChartEksternal();
     drawChartSupplier();
     // drawChart();
  }

  $('.datepicker').datepicker({
    format: "yyyy-mm",
    startView: "months", 
    minViewMode: "months",
    autoclose: true,
  });


  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

  function getbulanke(){
    var bulanfrom = document.getElementById("bulanfrom");
    var bulanto = document.getElementById("bulanto");
    var getbulanfrom = bulanfrom.options[bulanfrom.selectedIndex].value;

    // console.log(bulanfrom.options[10].value);
    var txt;
    var i;
    if (getbulanfrom != "") {
      for (i = 1; i < bulanfrom.options.length; i++) {
        if (getbulanfrom < i) 
        {
          $('#bulanto').append($("<option></option>").attr("value",bulanfrom.options[i].value).text(bulanfrom.options[i].text)); 
        }
      }
    }
  }

  function drawChartResume() {
    
    var fy = $('#fy').val();

      var data = {
        fy: fy,
      };

    $.get('{{ url("index/qc_report/fetchKategori") }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){
          var fiscal = result.fiscal;
          if(fiscal == null){
            fiscal = "All Years"
          }

          var kategori = [], jml = [], statusunverifiedcpar = [], statusunverifiedcar = [], statusverifikasi = [], statusclose = [];
          var close = [], cpar = [], car = [], verifikasi = [], datadrill = [], datad = [], jumlah = [];

          for (var i = 0; i < result.datas.length; i++) {
            jumlah.push({name:result.datas[i].kategori, y:parseInt(result.datas[i].jumlah), drilldown:result.datas[i].kategori});
            // cpar.push({name:result.datas[i].kategori, y:parseInt(result.datas[i].UnverifiedCPAR), drilldown:result.datas[i].kategori});
            // car.push({name:result.datas[i].kategori, y:parseInt(result.datas[i].UnverifiedCAR), drilldown:result.datas[i].kategori});
            // verifikasi.push({name:result.datas[i].kategori, y:parseInt(result.datas[i].qaverification), drilldown:result.datas[i].kategori});
          }

          for (var z = 0; z < result.eksternal.length; z++) {            
            datad.push([result.eksternal[z].destination_shortname, result.eksternal[z].jumlah]);
            datadrill.push({id:result.eksternal[z].kategori, name:result.eksternal[z].kategori, data: datad});
          }

          // console.table(datadrill);

          // $.each(result.datas, function(key, value) {
          //   kategori.push(value.kategori);
          //   jml.push(value.jumlah);
          //   statusunverifiedcpar.push(parseInt(value.UnverifiedCPAR));
          //   statusunverifiedcar.push(parseInt(value.UnverifiedCAR));
          //   statusverifikasi.push(parseInt(value.qaverification));
          //   statusclose.push(parseInt(value.close));
          // })

          $('#chartresume').highcharts({
            chart: {
              type: 'column'
            },
            title: {
              text: 'Complain Report By Category',
              style: {
                fontSize: '2vw',
                fontWeight: 'bold'
              }
            },
            accessibility: {
              announceNewData: {
                enabled: true
              }
            },
            subtitle: {
              text: 'On '+fiscal,
              style: {
                fontSize: '1vw',
                fontWeight: 'bold'
              }
            },
            xAxis: {
              type: 'category',
              lineWidth:2,
              lineColor:'#9e9e9e',
              gridLineWidth: 1,
              labels: {
                style: {
                  fontSize: '16px',
                  fontWeight: 'bold'
                }
              },
            },
            yAxis: {
              lineWidth:2,
              lineColor:'#fff',
              type: 'linear',
              title: {
                text: 'Total Kasus',
                style: {
                  color: '#eee',
                  fontSize: '20px',
                  fontWeight: 'bold',
                  fill: '#6d869f'
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
              enabled: false
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                point: {
                  events: {
                    click: function () {
                      // ShowModal(this.category,this.series.name,result.tglfrom,result.tglto,result.kategori,result.departemen);
                    }
                  }
                },
                borderWidth: 0,
                dataLabels: {
                  enabled: true,
                  format: '{point.y}',
                  style: {
                      color: 'white',
                      fontSize: '1vw'
                  }
                },
              },
              column: {
                  color:  Highcharts.ColorString,
                  borderRadius: 1,
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
              name: 'Total Kasus',
              colorByPoint : true,
              data: jumlah,
            },
            ],
            drilldown: {
              series: datadrill
            }
          })
        } else{
          alert('Attempt to retrieve data failed');
        }
      }
    })
  }

  function drawChartEksternal() {
    var fy = $('#fy').val();

    var data = {
      fy: fy,
    };

    $.get('{{ url("index/qc_report/fetchEksternal") }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){
          var fiscal = result.fiscal;
          if(fiscal == null){
            fiscal = "All Years"
          }

          var kat = [], jml = [];

          $.each(result.datas, function(key, value) {
            kat.push(value.kategori_komplain);
            jml.push(value.jumlah);
          })

          $('#charteksternal').highcharts({
            chart: {
              type: 'column'
            },
            title: {
              text: 'Complain Report By Type',
              style: {
                fontSize: '1.5vw',
                fontWeight: 'bold'
              }
            },
            subtitle: {
              text: 'Eksternal Complaint On '+fiscal,
              style: {
                fontSize: '1.2vw',
                fontWeight: 'bold'
              }
            },
            xAxis: {
              type: 'category',
              categories: kat,
              lineWidth:2,
              lineColor:'#9e9e9e',
              gridLineWidth: 1,
              labels: {
                style: {
                  fontSize: '16px',
                  fontWeight: 'bold'
                }
              },
            },
            yAxis: {
              lineWidth:2,
              lineColor:'#fff',
              type: 'linear',
              title: {
                text: 'Total Kasus',
                style: {
                  color: '#eee',
                  fontSize: '20px',
                  fontWeight: 'bold',
                  fill: '#6d869f'
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
              reversed: true,
              itemStyle:{
                color: "white",
                fontSize: "12px",
                fontWeight: "bold",
              },
              enabled:false
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                point: {
                  events: {
                    click: function () {
                      ShowModal(this.category,fiscal);
                    }
                  }
                },
                borderWidth: 0,
                dataLabels: {
                  enabled: true,
                  format: '{point.y}',
                  style: {
                      color: 'white',
                      fontSize: '1vw'
                  }
                },
              },
              column: {
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
            series: [{
                name: 'List',
                data: jml,
                colorByPoint : true
            }
            ]
          })
        } else{
          alert('Attempt to retrieve data failed');
        }
      }
    })
  }

  function drawChartSupplier() {
    var fy = $('#fy').val();

    var data = {
      fy: fy,
    };

    $.get('{{ url("index/qc_report/fetchSupplier") }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){
          var fiscal = result.fiscal;
          if(fiscal == null){
            fiscal = "All Years"
          }

          var kat = [], jml = [];

          $.each(result.datas, function(key, value) {
            kat.push(value.kategori_komplain);
            jml.push(value.jumlah);
          })

          $('#chartsupplier').highcharts({
            chart: {
              type: 'column'
            },
            title: {
              text: 'Complain Report By Type',
              style: {
                fontSize: '1.5vw',
                fontWeight: 'bold'
              }
            },
            subtitle: {
              text: 'Supplier On '+fiscal,
              style: {
                fontSize: '1.2vw',
                fontWeight: 'bold'
              }
            },
            xAxis: {
              type: 'category',
              categories: kat,
              lineWidth:2,
              lineColor:'#9e9e9e',
              gridLineWidth: 1,
              labels: {
                style: {
                  fontSize: '16px',
                  fontWeight: 'bold'
                }
              },
            },
            yAxis: {
              lineWidth:2,
              lineColor:'#fff',
              type: 'linear',
              title: {
                text: 'Total Kasus',
                style: {
                  color: '#eee',
                  fontSize: '20px',
                  fontWeight: 'bold',
                  fill: '#6d869f'
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
              reversed: true,
              itemStyle:{
                color: "white",
                fontSize: "12px",
                fontWeight: "bold",
              },
              enabled:false
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                point: {
                  events: {
                    click: function () {
                      ShowModal(this.category,fiscal);
                    }
                  }
                },
                borderWidth: 0,
                dataLabels: {
                  enabled: true,
                  format: '{point.y}',
                  style: {
                      color: 'white',
                      fontSize: '1vw'
                  }
                },
              },
              column: {
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
            series: [{
                name: 'List',
                data: jml,
                colorByPoint : true
            }
            ]
          })
        } else{
          alert('Attempt to retrieve data failed');
        }
      }
    })
  }

  function drawChart() {
    var fy = $('#fy').val();

    var data = {
      fy: fy,
    };

    $.get('{{ url("index/qc_report/fetchSource") }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){
          var fiscal = result.fiscal;
          if(fiscal == null){
            fiscal = "All Years"
          }

          var kat = [], jml = [];

          $.each(result.datas, function(key, value) {
            kat.push(value.kategori_komplain);
            jml.push(value.jumlah);
          })

          $('#chart').highcharts({
            chart: {
              type: 'column'
            },
            title: {
              text: 'Complain Report By Sources',
              style: {
                fontSize: '2vw',
                fontWeight: 'bold'
              }
            },
            subtitle: {
              text: 'On '+fiscal,
              style: {
                fontSize: '1vw',
                fontWeight: 'bold'
              }
            },
            xAxis: {
              type: 'category',
              categories: kat,
              lineWidth:2,
              lineColor:'#9e9e9e',
              gridLineWidth: 1,
              labels: {
                style: {
                  fontSize: '16px',
                  fontWeight: 'bold'
                }
              },
            },
            yAxis: {
              lineWidth:2,
              lineColor:'#fff',
              type: 'linear',
              title: {
                text: 'Total Kasus',
                style: {
                  color: '#eee',
                  fontSize: '20px',
                  fontWeight: 'bold',
                  fill: '#6d869f'
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
              reversed: true,
              itemStyle:{
                color: "white",
                fontSize: "12px",
                fontWeight: "bold",
              },
              enabled:false
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                point: {
                  events: {
                    click: function () {
                      ShowModal(this.category,fiscal);
                    }
                  }
                },
                borderWidth: 0,
                dataLabels: {
                  enabled: true,
                  format: '{point.y}',
                  style: {
                      color: 'white',
                      fontSize: '1vw'
                  }
                },
              },
              column: {
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
            series: [{
                name: 'List',
                data: jml,
                colorByPoint : true
            }
            ]
          })
        } else{
          alert('Attempt to retrieve data failed');
        }
      }
    })
  }

  function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }

  function ShowModal(kategori, fy) {
    tabel = $('#example2').DataTable();
    tabel.destroy();

    $("#myModal").modal("show");

    var table = $('#example2').DataTable({
      'dom': 'Bfrtip',
      'responsive': true,
      'lengthMenu': [
      [ 10, 25, 50, -1 ],
      [ '10 rows', '25 rows', '50 rows', 'Show all' ]
      ],
      'buttons': {
        buttons:[
        {
          extend: 'pageLength',
          className: 'btn btn-default',
          // text: '<i class="fa fa-print"></i> Show',
        },
        {
          extend: 'copy',
          className: 'btn btn-success',
          text: '<i class="fa fa-copy"></i> Copy',
          exportOptions: {
            columns: ':not(.notexport)'
          }
        },
        {
          extend: 'excel',
          className: 'btn btn-info',
          text: '<i class="fa fa-file-excel-o"></i> Excel',
          exportOptions: {
            columns: ':not(.notexport)'
          }
        },
        {
          extend: 'print',
          className: 'btn btn-warning',
          text: '<i class="fa fa-print"></i> Print',
          exportOptions: {
            columns: ':not(.notexport)'
          }
        },
        ]
      },
      'paging': true,
      'lengthChange': true,
      'searching': true,
      'ordering': true,
      'order': [],
      'info': true,
      'autoWidth': true,
      "sPaginationType": "full_numbers",
      "bJQueryUI": true,
      "bAutoWidth": false,
      "processing": true,
      "serverSide": true,
      "ajax": {
          "type" : "get",
          "url" : "{{ url("index/qc_report/detail_kategori") }}",
          "data" : {
            kategori : kategori,
            fy : fy
          }
        },
      "columns": [
          { "data": "cpar_no" },
          { "data": "kategori" },
          { "data": "judul_komplain" },
          { "data": "lokasi" },
          { "data": "tgl_permintaan" },
          { "data": "tgl_balas" },
          { "data": "department_name" },
          { "data": "sumber_komplain" },
          { "data": "status_name", "className": "table-status"},
          { "data": "action", "width": "15%"}
        ]    });

    $('#judul_table').append().empty();
    $('#judul_table').append('<center><b>List Komplain Kategori '+kategori+' On '+fy+'</center></b>');
    
  }

  Highcharts.createElement('link', {
          href: '{{ url("fonts/UnicaOne.css")}}',
          rel: 'stylesheet',
          type: 'text/css'
        }, null, document.getElementsByTagName('head')[0]);

        Highcharts.theme = {
          colors: ['#2b908f', '#90ee7e', '#7798BF', '#eeaaee', '#f45b5b', '#ff0066',
          '#eeaaee', '#55BF3B', '#DF5353', '#f45b5b', '#aaeeee'],
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
            // itemStyle: {
            //   color: '#E0E0E3'
            // },
            // itemHoverStyle: {
            //   color: '#FFF'
            // },
            // itemHiddenStyle: {
            //   color: '#606063'
            // }
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

  // function drawChartResume() {
    
  //   var tglfrom = $('#tglfrom').val();
  //   var tglto = $('#tglto').val();
  //   var departemen = $('#departemen').val();
  //   var status = $('#status').val();

  //   var data = {
  //     tglfrom: tglfrom,
  //     tglto: tglto,
  //     departemen: departemen,
  //     status:status
  //   };

  //   $.get('{{ url("index/qc_report/fetchKategori") }}', data, function(result, status, xhr) {
  //     if(xhr.status == 200){
  //       if(result.status){
  //         var fiscal = result.fiscal;
  //         if(fiscal == null){
  //           fiscal = "All"
  //         }

  //         var kategori = [], jml = [], statusunverifiedcpar = [], statusunverifiedcar = [], statusverifikasi = [], statusclose = [];
  //         var close = [], cpar = [], car = [], verifikasi = [], datadrill = [], datad = [];

  //         for (var i = 0; i < result.datas.length; i++) {
  //           close.push({name:result.datas[i].kategori, y:parseInt(result.datas[i].close), drilldown:result.datas[i].kategori});
  //           cpar.push({name:result.datas[i].kategori, y:parseInt(result.datas[i].UnverifiedCPAR), drilldown:result.datas[i].kategori});
  //           car.push({name:result.datas[i].kategori, y:parseInt(result.datas[i].UnverifiedCAR), drilldown:result.datas[i].kategori});
  //           verifikasi.push({name:result.datas[i].kategori, y:parseInt(result.datas[i].qaverification), drilldown:result.datas[i].kategori});
  //         }

  //         for (var z = 0; z < result.eksternal.length; z++) {            
  //           datad.push([result.eksternal[z].destination_shortname, result.eksternal[z].jumlah]);
  //           datadrill.push({id:result.eksternal[z].kategori, name:result.eksternal[z].kategori, data: datad});
  //         }

  //         // console.table(datadrill);

  //         // $.each(result.datas, function(key, value) {
  //         //   kategori.push(value.kategori);
  //         //   jml.push(value.jumlah);
  //         //   statusunverifiedcpar.push(parseInt(value.UnverifiedCPAR));
  //         //   statusunverifiedcar.push(parseInt(value.UnverifiedCAR));
  //         //   statusverifikasi.push(parseInt(value.qaverification));
  //         //   statusclose.push(parseInt(value.close));
  //         // })

  //         $('#chartresume').highcharts({
  //           chart: {
  //             type: 'column'
  //           },
  //           title: {
  //             text: 'Complain Report By Category',
  //             style: {
  //               fontSize: '18px',
  //               fontWeight: 'bold'
  //             }
  //           },
  //           accessibility: {
  //             announceNewData: {
  //               enabled: true
  //             }
  //           },
  //           subtitle: {
  //             text: 'Eksternal Internal Supplier',
  //             style: {
  //               fontSize: '1vw',
  //               fontWeight: 'bold'
  //             }
  //           },
  //           xAxis: {
  //             type: 'category',
  //             lineWidth:2,
  //             lineColor:'#9e9e9e',
  //             gridLineWidth: 1
  //           },
  //           yAxis: {
  //             lineWidth:2,
  //             lineColor:'#fff',
  //             type: 'linear',
  //             title: {
  //               text: 'Total Kasus'
  //             },
  //             stackLabels: {
  //                 enabled: true,
  //                 style: {
  //                     fontWeight: 'bold',
  //                     color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
  //                 }
  //             }
  //           },
  //           legend: {
  //             enabled: false
  //           },
  //           plotOptions: {
  //             series: {
  //                 borderWidth: 0,
  //                 dataLabels: {
  //                     enabled: true,
  //                     style: {
  //                         color: 'white',
  //                         textShadow: '0 0 2px black, 0 0 2px black'
  //                     }
  //                 },
  //                 stacking: 'normal'
  //             },
  //             series: {
  //               cursor: 'pointer',
  //               point: {
  //                 events: {
  //                   click: function () {
  //                     // ShowModal(this.category,this.series.name,result.tglfrom,result.tglto,result.kategori,result.departemen);
  //                   }
  //                 }
  //               },
  //               borderWidth: 0,
  //               dataLabels: {
  //                 enabled: false,
  //                 format: '{point.y}'
  //               },
  //               stacking: 'normal'
  //             },
  //             column: {
  //                 color:  Highcharts.ColorString,
  //                 stacking: 'normal',
  //                 borderRadius: 1,
  //                 dataLabels: {
  //                     enabled: true
  //                 }
  //             }
  //           },
  //           credits: {
  //             enabled: false
  //           },

  //           tooltip: {
  //             formatter:function(){
  //               return this.series.name+' : ' + this.y;
  //             }
  //           },
  //           // series: [{
  //           //     name: 'Unverified CPAR',
  //           //     color: '#ff6666', //ff6666
  //           //     data: statusunverifiedcpar
  //           // }, {
  //           //     name: 'Unverified CAR',
  //           //     data: statusunverifiedcar,
  //           //     color : '#f0ad4e', //f5f500
  //           // },
  //           // {
  //           //     name: 'QA Verification',
  //           //     data: statusverifikasi,
  //           //     color : '#448aff', //f5f500
  //           // },
  //           // {
  //           //     name: 'Closed',
  //           //     data: statusclose,
  //           //     color : '#5cb85c', //00f57f
  //           // }
  //           // ],
  //           series: [
  //           {
  //             name: 'Unverified CPAR',
  //             color: '#ff6666',
  //             data: cpar,
  //           },
  //           {
  //             name: 'Unverified CAR',
  //             color: '#f0ad4e',
  //             data: car,
  //           },
  //           {
  //             name: 'QA Verification',
  //             data: verifikasi,
  //             color : '#448aff', //f5f500
  //           },
  //           {
  //             name: 'Closed',
  //             color: '#5cb85c',
  //             data: close,
  //           }
  //           ],
  //           drilldown: {
  //             series: datadrill
  //           }
  //         })
  //       } else{
  //         alert('Attempt to retrieve data failed');
  //       }
  //     }
  //   })
  // }

</script>
@stop