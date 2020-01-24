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
    CPAR <span class="text-purple">Grafik</span>
    <small>Berdasarkan Bulan<span class="text-purple"> </span></small>
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
          <div class="input-group date">
            <div class="input-group-addon bg-green">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" class="form-control datepicker" id="tglfrom" placeholder="Bulan Dari">
          </div>
        </div>
        <div class="col-md-2">
          <div class="input-group date">
            <div class="input-group-addon bg-green">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" class="form-control datepicker" id="tglto" placeholder="Bulan Ke">
          </div>
        </div>
        <div class="col-md-2">
          <div class="input-group">
            <div class="input-group-addon bg-blue">
              <i class="fa fa-search"></i>
            </div>
            <select class="form-control select2" multiple="multiple" id="kategori" data-placeholder="Select Kategori">
                <option value="Eksternal">Eksternal</option>
                <option value="Internal">Internal</option>
                <option value="Supplier">Supplier</option>
            </select>
          </div>
        </div>
        <div class="col-md-2">
            <div class="input-group">
              <div class="input-group-addon bg-blue">
                <i class="fa fa-search"></i>
              </div>
              <select class="form-control select2" multiple="multiple" id="departemen" data-placeholder="Pilih Departemen" style="border-color: #605ca8" >
                  <!-- <option value="" selected>Semua Departemen</option> -->
                  @foreach($departemens as $departemen)
                    <option value="{{ $departemen->id }}">{{ $departemen->department_name }}</option>
                  @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-2">
          <div class="input-group">
            <div class="input-group-addon bg-blue">
              <i class="fa fa-search"></i>
            </div>
            <select class="form-control select2" multiple="multiple" id="status" data-placeholder="Pilih Status" style="border-color: #605ca8" >
                @foreach($status as $status)
                  <option value="{{ $status->status_code }}">{{ $status->status_name }}</option>
                @endforeach
              </select>
          </div>
        </div>

        <div class="col-xs-2">
          <button class="btn btn-success btn-sm" onclick="drawChart()">Update Chart</button>
        </div>
      </div>
      
      <div class="col-md-8" style="margin-top: 5px; padding-right: 0;padding-left: 10px">
          <div id="chart" style="width: 99%"></div>
      </div>
      <div class="col-md-4" style="margin-top: 5px; padding-right: 0;padding-left: 10px">
          <div id="chartresume" style="width: 99%"></div>
      </div>
      <div class="col-md-12" style="padding-right: 0;padding-left: 10px">
          <table id="tabelmonitor" class="table table-bordered" style="margin-top: 5px; width: 99%">
            <thead style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 12px;font-weight: bold">
              <tr>
                <th style="width: 15%; padding: 0;vertical-align: middle;;font-size: 16px;" rowspan="2">No CPAR</th>
                <th style="width: 15%; padding: 0;vertical-align: middle;border-left:3px solid #f44336 !important;font-size: 16px;" rowspan="2">Subject</th>
                <th style="width: 15%; padding: 0;vertical-align: middle;border-left:3px solid #f44336 !important;font-size: 16px;" rowspan="2">Departemen</th>
                <th style="width: 25%; padding: 0;vertical-align: middle;border-left:3px solid #f44336 !important;font-size: 16px;" colspan="6">CPAR</th>
                <th style="width: 25%; padding: 0;vertical-align: middle;border-left:3px solid #f44336 !important;font-size: 16px;" colspan="5">CAR</th>
                <th style="background-color:#448aff;width: 20%; padding: 0;vertical-align: middle;border-left:3px solid #f44336 !important;font-size: 16px;" colspan="3">QA Verification</th>
              </tr>
              <tr>
                <th style="width: 5%; padding: 0;border-left:3px solid #f44336 !important;vertical-align: middle;">Staff / Leader</th>
                <th style="width: 5%; padding: 0;vertical-align: middle;">Chief / Foreman</th>
                <th style="width: 5%; padding: 0;vertical-align: middle;">Manager</th>
                <th style="width: 5%; padding: 0;vertical-align: middle;">DGM</th>
                <th style="width: 5%; padding: 0;vertical-align: middle;">GM</th>
                <th style="width: 5%; padding: 0;vertical-align: middle;">Received By Manager</th>

                <th style="width: 6%; padding: 0;border-left:3px solid #f44336 !important;vertical-align: middle;">Staff / Leader</th>
                <th style="width: 6%; padding: 0;vertical-align: middle;">Chief / Foreman</th>
                <th style="width: 6%; padding: 0;vertical-align: middle;">Manager</th>
                <th style="width: 6%; padding: 0;vertical-align: middle;">DGM</th>
                <th style="width: 6%; padding: 0;vertical-align: middle;">GM</th>

                <th style="width: 6%; padding: 0;border-left:3px solid #f44336 !important;vertical-align: middle;">Staff / Leader</th>
                <th style="width: 6%; padding: 0;vertical-align: middle;">Chief / Foreman</th>
                <th style="width: 6%; padding: 0;vertical-align: middle;">Manager</th>
              </tr>
            </thead>
            <tbody id="tabelisi">
            </tbody>
            <tfoot>
            </tfoot>
          </table>
      </div>
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
                    <th>Manager</th>    
                    <th>Location</th>
                    <th>Request Date</th>
                    <th>Due Date</th>
                    <th>Departement</th>
                    <th>Source Of Complaint</th>
                    <th>Next Verification</th>
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

    drawChart();
    drawChartResume();
    fetchTable();
    setInterval(fetchTable, 300000);
    // drawChartDepartemen();
  });

  // $('#tgl').datepicker({
  //   format: "yyyy-mm-dd",
  //   autoclose: true,
  //   todayHighlight: true
  // });

  $('.datepicker').datepicker({
    format: "yyyy-mm",
    startView: "months", 
    minViewMode: "months",
    autoclose: true,
  });

  // function changekategori() {
  //   $("#kategori").val($("#kategoriselect").val());
  // }

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
          // console.log(i);
          // console.log(bulanfrom.options[i].value);
          $('#bulanto').append($("<option></option>").attr("value",bulanfrom.options[i].value).text(bulanfrom.options[i].text)); 
        }
        // console.log(bulanfrom.options.length);
      }
    }
  }

  function drawChart() {
    fetchTable();
    
    // var tahun = $('#tahun').val();
    var tglfrom = $('#tglfrom').val();
    var tglto = $('#tglto').val();
    var kategori = $('#kategori').val();
    var departemen = $('#departemen').val();
    var status = $('#status').val();

    var data = {
      tglfrom: tglfrom,
      tglto: tglto,
      kategori: kategori,
      departemen: departemen,
      status:status
    };

    $.get('{{ url("index/qc_report/fetchReport") }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){
          // var xAxis = [], productionCount = [], inTransitCount = [], fstkCount = []
          // for (i = 0; i < data.length; i++) {
          //   xAxis.push(data[i].destination);
          //   productionCount.push(data[i].production);
          //   inTransitCount.push(data[i].intransit);
          //   fstkCount.push(data[i].fstk);
          // }
          // console.log(result.tgt);
          var years = result.tahun;
          if(years == null){
            years = "All"
          }

          var month = [], jml = [], statusunverifiedcpar = [], statusunverifiedcar = [], statusverifikasi = [], statusclose = [];

          $.each(result.datas, function(key, value) {
            // departemen.push(value.department_name);
            month.push(value.bulan);
            jml.push(value.jumlah);
            statusunverifiedcpar.push(parseInt(value.UnverifiedCPAR));
            statusunverifiedcar.push(parseInt(value.UnverifiedCAR));
            statusverifikasi.push(parseInt(value.qaverification));
            statusclose.push(parseInt(value.close));
          })

          $('#chart').highcharts({
            chart: {
              type: 'column'
            },
            title: {
              text: 'Digital Complain Report By Month',
              style: {
                fontSize: '30px',
                fontWeight: 'bold'
              }
            },
            subtitle: {
              text: 'CPAR CAR Monitoring',
              style: {
                fontSize: '1vw',
                fontWeight: 'bold'
              }
            },
            xAxis: {
              type: 'category',
              categories: month,
              lineWidth:2,
              lineColor:'#9e9e9e',
              gridLineWidth: 1
            },
            yAxis: {
              lineWidth:2,
              lineColor:'#fff',
              type: 'linear',
              title: {
                text: 'Total Kasus'
              },
              tickInterval: 2,  
              stackLabels: {
                  enabled: true,
                  style: {
                      fontWeight: 'bold',
                      color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                  }
              }
            },
            legend: {
              // align: 'right',
              // x: -30,
              // verticalAlign: 'top',
              // y: 30,
              reversed: true,
              itemStyle:{
                color: "white",
                fontSize: "12px",
                fontWeight: "bold",

              },
              // floating: true,
              // shadow: false
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                point: {
                  events: {
                    click: function () {
                      ShowModal(this.category,this.series.name,result.tglfrom,result.tglto,result.kategori,result.departemen);
                    }
                  }
                },
                borderWidth: 0,
                dataLabels: {
                  enabled: false,
                  format: '{point.y}'
                }
              },
              column: {
                  color:  Highcharts.ColorString,
                  stacking: 'normal',
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
            series: [{
                name: 'Unverified CPAR',
                color: '#ff6666', //ff6666
                data: statusunverifiedcpar
            }, {
                name: 'Unverified CAR',
                data: statusunverifiedcar,
                color : '#f0ad4e' //f5f500
            },
            {
                name: 'QA Verification',
                data: statusverifikasi,
                color : '#448aff' //f5f500
            },
            {
                name: 'Closed',
                data: statusclose,
                color : '#5cb85c' //00f57f
            }
            ]
          })
        } else{
          alert('Attempt to retrieve data failed');
        }
      }
    })
  }

  function drawChartResume() {
    
    var tglfrom = $('#tglfrom').val();
    var tglto = $('#tglto').val();
    var departemen = $('#departemen').val();
    var status = $('#status').val();

    var data = {
      tglfrom: tglfrom,
      tglto: tglto,
      departemen: departemen,
      status:status
    };

    $.get('{{ url("index/qc_report/fetchKategori") }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){
          var fiscal = result.fiscal;
          if(fiscal == null){
            fiscal = "All"
          }

          var kategori = [], jml = [], statusunverifiedcpar = [], statusunverifiedcar = [], statusverifikasi = [], statusclose = [];
          var close = [], cpar = [], car = [], verifikasi = [], datadrill = [], datad = [];

          for (var i = 0; i < result.datas.length; i++) {
            close.push({name:result.datas[i].kategori, y:parseInt(result.datas[i].close), drilldown:result.datas[i].kategori});
            cpar.push({name:result.datas[i].kategori, y:parseInt(result.datas[i].UnverifiedCPAR), drilldown:result.datas[i].kategori});
            car.push({name:result.datas[i].kategori, y:parseInt(result.datas[i].UnverifiedCAR), drilldown:result.datas[i].kategori});
            verifikasi.push({name:result.datas[i].kategori, y:parseInt(result.datas[i].qaverification), drilldown:result.datas[i].kategori});
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
                fontSize: '18px',
                fontWeight: 'bold'
              }
            },
            accessibility: {
              announceNewData: {
                enabled: true
              }
            },
            subtitle: {
              text: 'On '+result.fiscal,
              style: {
                fontSize: '1vw',
                fontWeight: 'bold'
              }
            },
            xAxis: {
              type: 'category',
              lineWidth:2,
              lineColor:'#9e9e9e',
              gridLineWidth: 1
            },
            yAxis: {
              lineWidth:2,
              lineColor:'#fff',
              type: 'linear',
              title: {
                text: 'Total Kasus'
              },
              tickInterval: 4,  
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
                  borderWidth: 0,
                  dataLabels: {
                      enabled: true,
                      style: {
                          color: 'white',
                          textShadow: '0 0 2px black, 0 0 2px black'
                      }
                  },
                  stacking: 'normal'
              },
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
                  enabled: false,
                  format: '{point.y}'
                },
                stacking: 'normal'
              },
              column: {
                  color:  Highcharts.ColorString,
                  stacking: 'normal',
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
            // series: [{
            //     name: 'Unverified CPAR',
            //     color: '#ff6666', //ff6666
            //     data: statusunverifiedcpar
            // }, {
            //     name: 'Unverified CAR',
            //     data: statusunverifiedcar,
            //     color : '#f0ad4e', //f5f500
            // },
            // {
            //     name: 'QA Verification',
            //     data: statusverifikasi,
            //     color : '#448aff', //f5f500
            // },
            // {
            //     name: 'Closed',
            //     data: statusclose,
            //     color : '#5cb85c', //00f57f
            // }
            // ],
            series: [
            {
              name: 'Unverified CPAR',
              color: '#ff6666',
              data: cpar,
            },
            {
              name: 'Unverified CAR',
              color: '#f0ad4e',
              data: car,
            },
            {
              name: 'QA Verification',
              data: verifikasi,
              color : '#448aff', //f5f500
            },
            {
              name: 'Closed',
              color: '#5cb85c',
              data: close,
            }
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

  function fetchTable(){

    var kategori = $('#kategori').val();
    var departemen = $('#departemen').val();
    var status = $('#status').val();

    var data = {
      kategori: kategori,
      departemen: departemen,
      status:status
    }
    $.get('{{ url("index/qc_report/fetchtable") }}', data, function(result, status, xhr){
      if(xhr.status == 200){
        if(result.status){

          $("#tabelisi").find("td").remove();  
          $('#tabelisi').html("");
          var table = "";
          var statusawal = "";
          var statuscf = "";
          var statusm = "";
          var statusdgm = "";
          var statusgm = "";


          $.each(result.datas, function(key, value) {
          var namasl = value.namasl;
          var namasl2 = namasl.split(' ').slice(0,2).join(' ');

          if (value.namacf != null) {
            var namacf = value.namacf;
            var namacf2 = namacf.split(' ').slice(0,2).join(' ');
          }
          var namam = value.namam;
          var namam2 = namam.split(' ').slice(0,2).join(' ');

          var namadgm = value.namadgm;
          var namadgm2 = namadgm.split(' ').slice(0,2).join(' ');

          var namagm = value.namagm;
          var namagm2 = namagm.split(' ').slice(0,2).join(' ');

          var namabagian = value.namabagian;
          var namabagian2 = namabagian.split(' ').slice(0,2).join(' ');

          if (value.namapiccar != null) {
            var namapiccar = value.namapiccar;
            var namapiccar2 = namapiccar.split(' ').slice(0,2).join(' ');            
          }

          if (value.namacfcar != null) {
            var namacfcar = value.namacfcar;
            var namacfcar2 = namacfcar.split(' ').slice(0,2).join(' ');
          }

          var color = "";
          var color2 = "";
          var d = 0;
          var e = 0;

            //CPAR
            var urldetailcpar = '{{ url("index/qc_report/update/") }}';
            var urlverifikasi = '{{ url("index/qc_report/verifikasicpar/") }}';
            var urlverifikasigm = '{{ url("index/qc_report/verifikasigm/") }}';
            var urlprintcpar = '{{ url("index/qc_report/print_cpar/") }}';

            var urldetailcar = '{{ url("index/qc_car/detail/") }}';
            var urlverifikasicar = '{{ url("index/qc_car/verifikasicar/") }}';
            var urlprintcar = '{{ url("index/qc_car/print_car_new/") }}';
            var urlverifikasiqa = '{{ url("index/qc_report/verifikasiqa/") }}';

            if (value.posisi_cpar != "staff" && value.posisi_cpar != "leader") {
              // statusawal = '<img src="{{ url("ok.png")}}" width="40" height="40"';
              statusawal = '<a href="'+urlprintcpar+'/'+value.id+'"><span class="label label-success">'+namasl2+'</span></a>';
              color = 'style="background-color:green"';
            }
            else {
              if (d == 0) {  
                  // statusawal = '<img src="{{ url("nok2.png")}}" width="45" height="45" class="zoom">';
                  statusawal = '<a href="'+urldetailcpar+'/'+value.id+'"><span class="label label-danger zoom">'+namasl2+'</span></a>';
                  color = 'style="background-color:red"';                    
                  d = 1;
                } else {
                  statusawal = '';
                }
            }

              //jika tidak ada chief/foreman
              if (value.namacf != null) {
                //chief / foreman
                if (value.checked_chief == "Checked" || value.checked_foreman == "Checked") {
                    if (value.posisi_cpar == "chief" || value.posisi_cpar == "foreman") {
                        if (d == 0) {  
                            // statuscf = '<img src="{{ url("nok2.png")}}" width="45" height="45" class="zoom">';
                            statuscf = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">'+namacf2+'</span></a>';   
                            color = 'style="background-color:red"';                  
                            d = 1;
                          } else {
                            statuscf = '';
                          }
                    }
                    else{
                        statuscf = '<a href="'+urlprintcpar+'/'+value.id+'"><span class="label label-success">'+namacf2+'</span></a>';
                        color = 'style="background-color:green"'; 
                        // statuscf = '<img src="{{ url("ok.png")}}" width="40" height="40">';
                    }
                }
                else{
                  if (d == 0) {  
                    // statuscf = '<img src="{{ url("nok2.png")}}" width="45" height="45" class="zoom">';
                    statuscf = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">'+namacf2+'</span></a>'; 
                    color = 'style="background-color:red"';                  
                    d = 1;
                  } else {
                    statuscf = '';
                  }
                }
              }
              else{
                statuscf = 'None';
              }


              //manager
              if (value.checked_manager == "Checked") {
                  if (value.posisi_cpar == "manager") {
                    if (d == 0) {  
                        statusm = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">'+namam2+'</span></a>';
                        color = 'style="background-color:red"'; 
                        // statusm = '<img src="{{ url("nok2.png")}}" width="45" height="45" class="zoom">';                    
                        d = 1;
                      } else {
                        statusm = '';
                      }
                  }
                  else {
                    statusm = '<a href="'+urlprintcpar+'/'+value.id+'"><span class="label label-success">'+namam2+'</span></a>';
                    color = 'style="background-color:green"'; 
                    // statusm = '<img src="{{ url("ok.png")}}" width="40" height="40">';
                  } 
              }
              else {
                if (d == 0) {  
                  statusm = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">'+namam2+'</span></a>';
                  color = 'style="background-color:red"'; 
                  // statusm = '<img src="{{ url("nok2.png")}}" width="45" height="45" class="zoom">';                    
                  d = 1;
                } else {
                  statusm = '';
                }
              }

              //dgm
              if (value.approved_dgm == "Checked") {
                  if (value.posisi_cpar == "dgm") {
                      if (d == 0) {  
                        statusdgm = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">'+namadgm2+'</span></a>';
                        color = 'style="background-color:red"'; 
                        // statusdgm = '<img src="{{ url("nok2.png")}}" width="45" height="45" class="zoom">';
                        d = 1;
                      } else {
                        statusdgm = '';
                      }
                  } else {
                    statusdgm = '<a href="'+urlprintcpar+'/'+value.id+'"><span class="label label-success">'+namadgm2+'</span></a>'; 
                    color = 'style="background-color:green"'; 
                      // statusdgm = '<img src="{{ url("ok.png")}}" width="40" height="40">';
                  }
              } 

              else {
                if (d == 0) {  
                  statusdgm = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">'+namadgm2+'</span></a>';
                  color = 'style="background-color:red"'; 
                  // statusdgm = '<img src="{{ url("nok2.png")}}" width="45" height="45" class="zoom">';
                  d = 1;
                } else {
                  statusdgm = '';
                }
              }

              //gm
              if (value.approved_gm == "Checked") {
                  if (value.posisi_cpar == "gm") {
                      if (d == 0) {  
                        statusgm = '<a href="'+urlverifikasigm+'/'+value.id+'"><span class="label label-danger">'+namagm2+'</span></a>'; 
                        color = 'style="background-color:red"';
                        // statusgm = '<img src="{{ url("nok2.png")}}" width="45" height="45" class="zoom">';
                        d = 1;
                      } else {
                        statusgm = '';
                      }
                  } else {
                      statusgm = '<a href="'+urlprintcpar+'/'+value.id+'"><span class="label label-success">'+namagm2+'</span></a>'; 
                      color = 'style="background-color:green"'; 
                      // statusgm = '<img src="{{ url("ok.png")}}" width="40" height="40">';                    
                  }
              }
              else{
                if (d == 0) {  
                  statusgm = '<a href="'+urlverifikasigm+'/'+value.id+'"><span class="label label-danger">'+namagm2+'</span></a>'; 
                  color = 'style="background-color:red"';
                  // statusgm = '<img src="{{ url("nok2.png")}}" width="45" height="45" class="zoom">';
                  d = 1;
                } else {
                  statusgm = '';
                }
              }

              if (value.received_manager == "Received") {
                  if (value.posisi_car == "bagian") {
                      if (d == 0) {  
                        statusbagian = '<a href="'+urldetailcar+'/'+value.id_car+'"><span class="label label-danger">'+namabagian2+'</span></a>';
                        color = 'style="background-color:red"';
                        // statusbagian = '<img src="{{ url("nok2.png")}}" width="45" height="45" class="zoom">';
                        d = 1;
                      } else {
                        statusbagian = '';
                      }
                  } else {
                      statusbagian = '<a href="'+urlprintcpar+'/'+value.id+'"><span class="label label-success">'+namabagian2+'</span></a>';
                      color = 'style="background-color:green"'; 
                      // statusbagian = '<img src="{{ url("ok.png")}}" width="40" height="40">';                    
                  }
              }
              else{
                if (d == 0) {  
                  statusbagian = '<a href="'+urldetailcar+'/'+value.id_car+'"><span class="label label-danger">'+namabagian2+'</span></a>';
                  color = 'style="background-color:red"';
                  // statusbagian = '<img src="{{ url("nok2.png")}}" width="45" height="45" class="zoom">';
                  d = 1;
                } else {
                  statusbagian = '';
                }
              }

              //CAR

              //staff leader
              if (value.posisi_car != null) {
                if (value.posisi_car != "staff" && value.posisi_car != "foreman" && value.posisi_car != "bagian") {
                      statusawalcar = '<a href="'+urlprintcar+'/'+value.id_car+'"><span class="label label-success">'+namapiccar2+'</span></a>'; 
                      color = 'style="background-color:green"'; 
                      // statusawalcar = '<img src="{{ url("ok.png")}}" width="40" height="40">';
                  }
                  else{
                      if (d == 0) {
                        statusawalcar = '<a href="'+urldetailcar+'/'+value.id_car+'"><span class="label label-danger">'+namapiccar2+'</span></a>';
                        color = 'style="background-color:red"'; 
                        // statusawalcar = '<img src="{{ url("nok2.png")}}" width="45" height="45" class="zoom">';
                        d = 1 ;
                      }
                      else {
                        statusawalcar = '';
                      }
                  }                
              }
              else{
                if (d == 0) {
                      statusawalcar = '<a href="'+urldetailcar+'/'+value.id_car+'"><span class="label label-danger">'+namapiccar2+'</span></a>';
                      color = 'style="background-color:red"'; 
                      // statusawalcar = '<img src="{{ url("nok2.png")}}" width="45" height="45" class="zoom">';
                      d = 1 ;
                    }
                    else {
                      statusawalcar = '';
                    }
              }


              //chiefforemancoordinator

              if (value.checked_chief_car == "Checked" || value.checked_foreman_car == "Checked" || value.checked_coordinator_car == "Checked") {
                  if (value.posisi_car == "chief" || value.posisi_car == "foreman2" || value.posisi_car == "coordinator") {
                      if (d == 0) {  
                          statuscfcar = '<a href="'+urlverifikasicar+'/'+value.id_car+'"><span class="label label-danger">'+namacfcar2+'</span></a>';
                          color = 'style="background-color:red"';   
                          d = 1;
                        } else {
                          statuscfcar = '';
                        }
                  }
                  else{
                      statuscfcar = '<a href="'+urlprintcar+'/'+value.id_car+'"><span class="label label-success">'+namacfcar2+'</span></a>';
                      color = 'style="background-color:green"'; 
                      // statuscfcar = '<img src="{{ url("ok.png")}}" width="40" height="40">';
                  }
              }
              else{
                if (d == 0) {   
                  if (namacfcar2 == "Tidak Ada") {
                    statuscfcar = '<a href="'+urlverifikasicar+'/'+value.id_car+'"><span class="label label-danger">None</span></a>';                    
                    statusmcar = '<a href="'+urlverifikasicar+'/'+value.id_car+'"><span class="label label-danger">'+namabagian2+'</span></a>';
                    color = 'style="background-color:red"';
                    // statuscfcar = '<img src="{{ url("nok2.png")}}" width="45" height="45" class="zoom">';                    
                  }
                  else{
                    statuscfcar = '<a href="'+urlverifikasicar+'/'+value.id_car+'"><span class="label label-danger">'+namacfcar2+'</span></a>';
                    color = 'style="background-color:red"';
                    d=1;
                  }


                } else {
                  statuscfcar = '';
                }
              }

              //manager

              if (value.checked_manager_car == "Checked") {
                  if (value.posisi_car == "manager") {
                      if (d == 0) {  
                        statusmcar = '<a href="'+urlverifikasicar+'/'+value.id_car+'"><span class="label label-danger">'+namabagian2+'</span></a>';
                        color = 'style="background-color:red"';
                        // statusmcar = '<img src="{{ url("nok2.png")}}" width="45" height="45" class="zoom">';                    
                        d = 1;
                      } else {
                        statusmcar = '';
                      }
                  }
                  else{
                      statusmcar = '<a href="'+urlprintcar+'/'+value.id_car+'"><span class="label label-success">'+namabagian2+'</span></a>';
                      color = 'style="background-color:green"'; 
                      // statusmcar = '<img src="{{ url("ok.png")}}" width="40" height="40">';
                  }
              }
              else{
                if (d == 0) {  
                  statusmcar = '<a href="'+urlverifikasicar+'/'+value.id_car+'"><span class="label label-danger">'+namabagian2+'</span></a>';
                  color = 'style="background-color:red"';
                  // statusmcar = '<img src="{{ url("nok2.png")}}" width="45" height="45" class="zoom">';                    
                  d = 1;
                } else {
                  statusmcar = '';
                }
              }

              //dgm

              if (value.approved_dgm_car == "Checked") {
                  if (value.posisi_car == "dgm") {
                      if (d == 0) {  
                        statusdgmcar = '<a href="'+urlverifikasicar+'/'+value.id_car+'"><span class="label label-danger">'+namadgm2+'</span></a>';
                        color = 'style="background-color:red"';
                        // statusdgmcar = '<img src="{{ url("nok2.png")}}" width="45" height="45" class="zoom">';                    
                        d = 1;
                      } else {
                        statusdgmcar = '';
                      }
                  }
                  else{
                      statusdgmcar = '<a href="'+urlprintcar+'/'+value.id_car+'"><span class="label label-success">'+namadgm2+'</span></a>';
                      color = 'style="background-color:green"'; 
                      // statusdgmcar = '<img src="{{ url("ok.png")}}" width="40" height="40">';
                  }
              }
              else{
                if (d == 0) {  
                  statusdgmcar = '<a href="'+urlverifikasicar+'/'+value.id_car+'"><span class="label label-danger">'+namadgm2+'</span></a>';
                  color = 'style="background-color:red"';
                  // statusdgmcar = '<img src="{{ url("nok2.png")}}" width="45" height="45" cl  ass="zoom">';                    
                  d = 1;
                } else {
                  statusdgmcar = '';
                }
              }

              //gm

              if (value.approved_gm_car == "Checked") {
                  if (value.posisi_car == "gm") {
                      if (d == 0) {  
                        statusgmcar = '<a href="'+urlverifikasicar+'/'+value.id_car+'"><span class="label label-danger">'+namagm+'</span></a>';
                        color = 'style="background-color:red"';
                        // statusgmcar = '<img src="{{ url("nok2.png")}}" width="45" height="45" class="zoom">';                    
                        d = 1;
                      } else {
                        statusgmcar = '';
                      }
                  }
                  else{
                      statusgmcar = '<a href="'+urlprintcar+'/'+value.id_car+'"><span class="label label-success">'+namagm+'</span></a>';
                      color = 'style="background-color:green"'; 
                      // statusgmcar = '<img src="{{ url("ok.png")}}" width="40" height="40">';
                  }
              }
              else{
                if (d == 0) {  
                  statusgmcar = '<a href="'+urlverifikasicar+'/'+value.id_car+'"><span class="label label-danger">'+namagm+'</span></a>';
                  color = 'style="background-color:red"';
                  // statusgmcar = '<img src="{{ url("nok2.png")}}" width="45" height="45" class="zoom">';                    
                  d = 1;
                } else {
                  statusgmcar = '';
                }
              }

              //QA

              if (value.email_status_car == "SentQA") {
                  if (value.posisi_cpar == "QA") {
                      if (d == 0) {  
                        statusqa = '<a href="'+urlverifikasiqa+'/'+value.id+'"><span class="label label-danger">'+namasl2+'</span></a>';
                        color = 'style="background-color:red"';
                        // statusqa = '<img src="{{ url("nok2.png")}}" width="45" height="45" class="zoom">';                    
                        d = 1;
                      } else {
                        statusqa = '';
                      }
                  }
                  else{
                      statusqa = '<span class="label label-success">'+namasl2+'</span>';
                      color = 'style="background-color:green"'; 
                      // statusqa = '<img src="{{ url("ok.png")}}" width="40" height="40">';
                  }
              }
              else{
                if (d == 0) {  
                  statusqa = '<a href="'+urlverifikasiqa+'/'+value.id+'"><span class="label label-danger">'+namasl2+'</span></a>';
                  color = 'style="background-color:red"';
                  // statusqa = '<img src="{{ url("nok2.png")}}" width="45" height="45" class="zoom">';                    
                  d = 1;
                } else {
                  statusqa = '';
                }
              }

              if (value.email_status_car == "SentQA" && value.email_status == "SentQA2") {
                  if (value.posisi_cpar == "QA2") {
                      if (d == 0) {  
                          statusqa2 = '<a href="'+urlverifikasiqa+'/'+value.id+'"><span class="label label-danger">'+namacf2+'</span></a>';
                          color = 'style="background-color:red"';
                        // statusqa2 = '<img src="{{ url("nok2.png")}}" width="45" height="45" class="zoom">';                    
                        d = 1;
                      } else {
                        statusqa2 = '';
                      }
                  }
                  else{
                      statusqa2 = '<span class="label label-success">'+namacf2+'</span>';
                      color = 'style="background-color:green"'; 
                      // statusqa2 = '<img src="{{ url("ok.png")}}" width="40" height="40">';
                  }
              }
              else {
                if (d == 0) {  
                  statusqa2 = '<a href="'+urlverifikasiqa+'/'+value.id+'"><span class="label label-danger">'+namacf2+'</span></a>';
                  color = 'style="background-color:red"';
                  // statusqa2 = '<img src="{{ url("nok2.png")}}" width="45" height="45" class="zoom">';                    
                  d = 1;
                } else {
                  statusqa2 = '';
                }
              }

              if (value.email_status == "SentQA2" && value.posisi_cpar == "QAFIX") {
                  if (value.posisi_cpar == "QAmanager") {
                      if (d == 0) {  
                          statusqamanager = '<a href="'+urlverifikasiqa+'/'+value.id+'"><span class="label label-danger">'+namam2+'</span></a>';
                          color = 'style="background-color:red"';
                        // statusqamanager = '<img src="{{ url("nok2.png")}}" width="45" height="45" class="zoom">';                    
                        d = 1;
                      } else {
                        statusqamanager = '';
                      }
                  }
                  else{
                      statusqamanager = '<span class="label label-success">'+namam2+'</span>';
                      color = 'style="background-color:green"'; 
                      // statusqamanager = '<img src="{{ url("ok.png")}}" width="40" height="40">';
                  }
              }
              else {
                if (d == 0) {  
                  statusqamanager = '<a href="'+urlverifikasiqa+'/'+value.id+'"><span class="label label-danger">'+namam2+'</span></a>';
                  color = 'style="background-color:red"';
                  // statusqamanager = '<img src="{{ url("nok2.png")}}" width="45" height="45" class="zoom">';                    
                  d = 1;
                } else {
                  statusqamanager = '';
                }
              }


            table += '<tr>';
            table += '<td>'+value.cpar_no+'</td>';
            table += '<td style="border-left:3px solid #f44336">'+value.judul_komplain+'</td>'; 
            table += '<td style="border-left:3px solid #f44336">'+capitalizeFirstLetter(value.department_name)+'</td>';
            table += '<td style="border-left:3px solid #f44336">'+statusawal+'</td>';  
            table += '<td>'+statuscf+'</td>';
            table += '<td>'+statusm+'</td>';
            table += '<td>'+statusdgm+'</td>';
            table += '<td>'+statusgm+'</td>';
            table += '<td>'+statusbagian+'</td>';
            table += '<td style="border-left:3px solid #f44336">'+statusawalcar+'</td>';
            table += '<td>'+statuscfcar+'</td>';
            table += '<td>'+statusmcar+'</td>';
            table += '<td>'+statusdgmcar+'</td>';
            table += '<td>'+statusgmcar+'</td>';
            table += '<td style="border-left:3px solid #f44336">'+statusqa+'</td>';
            table += '<td>'+statusqa2+'</td>';
            table += '<td>'+statusqamanager+'</td>';
            table += '</tr>';

              
          })

          $('#tabelisi').append(table);

        }
      }
    })
  }

  function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

  // function drawChartDepartemen() {

  //   var departemen = $('#departemen').val();

  //   var data = {
  //     departemen: departemen
  //   };

  //   $.get('{{ url("index/qc_report/fetchDept") }}', data, function(result, status, xhr) {
  //     if(xhr.status == 200){
  //       if(result.status){
  //         var departemen = [], jml = [], statusopen = [], statusclose = [], statusprogress = [];

  //         $.each(result.datas, function(key, value) {
  //           departemen.push(value.department_name);
  //           jml.push(value.jumlah);
  //           statusopen.push(parseInt(value.open));
  //           statusclose.push(parseInt(value.close));
  //           statusprogress.push(parseInt(value.progress));
  //         })

  //         $('#chartdept').highcharts({
  //           chart: {
  //             type: 'column'
  //           },
  //           title: {
  //             text: 'CPAR Report By Departement'
  //           },
  //           xAxis: {
  //             type: 'category',
  //             categories: departemen
  //           },
  //           yAxis: {
  //             type: 'linear',
  //             title: {
  //               text: 'Total CPAR'
  //             },
  //             tickInterval: 1,
  //             stackLabels: {
  //                 enabled: true,
  //                 style: {
  //                     fontWeight: 'bold',
  //                     color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
  //                 }
  //             }
  //           },
  //           legend: {
  //             align: 'right',
  //             x: -30,
  //             verticalAlign: 'top',
  //             y: 25,
  //             floating: true,
  //             backgroundColor:
  //                 Highcharts.defaultOptions.legend.backgroundColor || 'white',
  //             borderColor: '#CCC',
  //             borderWidth: 1,
  //             shadow: false
  //           },
  //           plotOptions: {
  //             series: {
  //               cursor: 'pointer',
  //               point: {
  //                 events: {
  //                   click: function () {
  //                     ShowModalDept(this.category,this.series.name);
  //                   }
  //                 }
  //               },
  //               borderWidth: 0,
  //               dataLabels: {
  //                 enabled: false,
  //                 format: '{point.y}'
  //               }
  //             },
  //             column: {
  //                 stacking: 'normal',
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
  //           series: [{
  //               name: 'Open',
  //               color: '#4caf50',
  //               data: statusopen
  //           }, {
  //               name: 'Progress',
  //               data: statusprogress,
  //               color : '#ffeb3b'
  //           }, {
  //               name: 'Closed',
  //               data: statusclose,
  //               color : '#e53935'
  //           }, 
  //           {
  //               type: 'spline',
  //               name: 'Open',
  //               color: '#388e3c',
  //               data: statusopen
  //           },
  //           {
  //               type: 'spline',
  //               name: 'Closed',
  //               color: '#c62828',
  //               data: statusclose
  //           }
  //           ]
  //         })
  //       } else{
  //         alert('Attempt to retrieve data failed');
  //       }
  //     }
  //   })
  // }

  function ShowModal(bulan, status, tglfrom, tglto, kategori, departemen) {
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
          "url" : "{{ url("index/qc_report/detail_cpar") }}",
          "data" : {
            bulan : bulan,
            status : status,
            kategori : kategori,
            departemen : departemen,
            tglfrom : tglfrom,
            tglto : tglto
          }
        },
      "columns": [
          { "data": "cpar_no" },
          { "data": "kategori" },
          { "data": "judul_komplain" },
          { "data": "name" },
          { "data": "lokasi" },
          { "data": "tgl_permintaan" },
          { "data": "tgl_balas" },
          { "data": "department_name" },
          { "data": "sumber_komplain" },
          { "data": "verif", "className": "table-posisi" },
          { "data": "status_name", "className": "table-status"},
          { "data": "action", "width": "15%"}
        ]    });

    $('#judul_table').append().empty();
    $('#judul_table').append('<center><b>'+status+' Bulan '+bulan+'</center></b>');
    
  }

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
        


  // function ShowModalDept(departemen, status) {
  //   tabel = $('#example2').DataTable();
  //   tabel.destroy();

  //   $("#myModal").modal("show");

  //   var table = $('#example2').DataTable({
  //     'dom': 'Bfrtip',
  //     'responsive': true,
  //     'lengthMenu': [
  //     [ 10, 25, 50, -1 ],
  //     [ '10 rows', '25 rows', '50 rows', 'Show all' ]
  //     ],
  //     'buttons': {
  //       buttons:[
  //       {
  //         extend: 'pageLength',
  //         className: 'btn btn-default',
  //         // text: '<i class="fa fa-print"></i> Show',
  //       },
  //       {
  //         extend: 'copy',
  //         className: 'btn btn-success',
  //         text: '<i class="fa fa-copy"></i> Copy',
  //         exportOptions: {
  //           columns: ':not(.notexport)'
  //         }
  //       },
  //       {
  //         extend: 'excel',
  //         className: 'btn btn-info',
  //         text: '<i class="fa fa-file-excel-o"></i> Excel',
  //         exportOptions: {
  //           columns: ':not(.notexport)'
  //         }
  //       },
  //       {
  //         extend: 'print',
  //         className: 'btn btn-warning',
  //         text: '<i class="fa fa-print"></i> Print',
  //         exportOptions: {
  //           columns: ':not(.notexport)'
  //         }
  //       },
  //       ]
  //     },
  //     'paging': true,
  //     'lengthChange': true,
  //     'searching': true,
  //     'ordering': true,
  //     'order': [],
  //     'info': true,
  //     'autoWidth': true,
  //     "sPaginationType": "full_numbers",
  //     "bJQueryUI": true,
  //     "bAutoWidth": false,
  //     "processing": true,
  //     "serverSide": true,
  //     "ajax": {
  //         "type" : "get",
  //         "url" : "{{ url("index/qc_report/detail_cpar_dept") }}",
  //         "data" : {
  //           departemen : departemen,
  //           status : status
  //         }
  //       },
  //     "columns": [
  //         { "data": "cpar_no" },
  //         { "data": "kategori" },
  //         { "data": "name" },
  //         { "data": "lokasi" },
  //         { "data": "tgl_permintaan" },
  //         { "data": "tgl_balas" },
  //         { "data": "via_komplain" },
  //         { "data": "department_name" },
  //         { "data": "sumber_komplain" },
  //         { "data": "status_name" },
  //         { "data": "action", "width": "15%"}
  //       ]    });

  //   $('#judul_table').append().empty();
  //   $('#judul_table').append('<center><b>Departemen '+departemen+'</center></b>');
    
  // }

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