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
    Form Ketidaksesuaian <span class="text-purple">Grafik</span>
    <small>Berdasarkan Tanggal<span class="text-purple"> </span></small>
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

        <div class="col-xs-2">
          <div class="input-group date">
            <div class="input-group-addon bg-green" style="border: none;">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" class="form-control datepicker" id="datefrom" placeholder="Select Date From">
          </div>
        </div>
        <div class="col-xs-2">
          <div class="input-group date">
            <div class="input-group-addon bg-green" style="border: none;">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" class="form-control datepicker" id="dateto" placeholder="Select Date To">
          </div>
        </div>
        <div class="col-xs-2">
          <div class="input-group">
            <div class="input-group-addon bg-blue">
              <i class="fa fa-search"></i>
            </div>
            <select class="form-control select2" multiple="multiple" id="section_from" data-placeholder="Pilih Section From" style="border-color: #605ca8" >
              @foreach($sections as $sec)
                <option value="{{ $sec->section }}">{{ $sec->section }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-xs-2">
          <div class="input-group">
            <div class="input-group-addon bg-blue">
              <i class="fa fa-search"></i>
            </div>
            <select class="form-control select2" multiple="multiple" id="section_to" data-placeholder="Pilih Section To" style="border-color: #605ca8" >
                @foreach($sections as $sec)
                  <option value="{{ $sec->section }}">{{ $sec->section }}</option>
                @endforeach
            </select>
          </div>
        </div>
        <div class="col-xs-2">
          <div class="input-group">
            <div class="input-group-addon bg-blue">
              <i class="fa fa-search"></i>
            </div>
            <select class="form-control select2" multiple="multiple" id="status" data-placeholder="Pilih Status" style="border-color: #605ca8" >
                <option value="cpar">CPAR</option>
                <option value="car">Penanganan</option>
                <option value="verification">Verifikasi</option>
                <option value="close">Close</option>
            </select>
          </div>
        </div>

        <div class="col-xs-1">
          <button class="btn btn-success" onclick="drawChart()">Update Chart</button>
        </div>

      </div>

      
      
      <div class="col-md-12" style="margin-top: 5px; padding-right: 0;padding-left: 10px">
          <div id="chart" style="width: 99%; height: 300px;"></div>
      </div>
      
      <div class="col-md-12" style="padding-right: 0;padding-left: 10px">
          <table id="tabelmonitor" class="table table-bordered" style="margin-top: 5px; width: 99%">
            <thead style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 12px;font-weight: bold">
              <tr>
                <th style="width: 12%; padding: 0;vertical-align: middle;;font-size: 16px;" rowspan="2">Subject</th>
                <th style="width: 8%; padding: 0;vertical-align: middle;border-left:3px solid #f44336 !important;font-size: 16px;" rowspan="2">Date</th>
                <th style="width: 8%; padding: 0;vertical-align: middle;border-left:3px solid #f44336 !important;font-size: 16px;" rowspan="2">From</th>
                <th style="width: 8%; padding: 0;vertical-align: middle;border-left:3px solid #f44336 !important;font-size: 16px;" rowspan="2">To</th>
                <th style="width: 27%; padding: 0;vertical-align: middle;border-left:3px solid #f44336 !important;font-size: 16px;" colspan="3">Form Ketidaksesuaian</th>
                <th style="width: 27%; padding: 0;vertical-align: middle;border-left:3px solid #f44336 !important;font-size: 16px;" colspan="3">Penanganan</th>
                <th style="width: 10%; padding: 0;vertical-align: middle;border-left:3px solid #f44336 !important;font-size: 16px;background-color:#448aff;">Verification</th>
              </tr>
              <tr>
                <th style="width: 7%; padding: 0;border-left:3px solid #f44336 !important;vertical-align: middle;">Staff / Leader</th>
                <th style="width: 7%; padding: 0;vertical-align: middle;">Chief / Foreman</th>
                <th style="width: 7%; padding: 0;vertical-align: middle;">Manager</th>

                <th style="width: 7%; padding: 0;border-left:3px solid #f44336 !important;vertical-align: middle;">Staff / Leader</th>
                <th style="width: 7%; padding: 0;vertical-align: middle;">Chief / Foreman</th>
                <th style="width: 7%; padding: 0;vertical-align: middle;">Manager</th>

                <th style="width: 5%; padding: 0;border-left:3px solid #f44336 !important;vertical-align: middle;vertical-align: middle;">Chief / Foreman / Manager</th>
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
                    <th>Kategori</th> 
                    <th>Judul</th>
                    <th>Tanggal</th>    
                    <th>Section From</th>
                    <th>Section To</th>
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
    $('.select2').select2();
    drawChart();
    fetchTable();
    setInterval(fetchTable, 300000);
  });

  $('.datepicker').datepicker({
    autoclose: true,
    format: "dd-mm-yyyy",
    todayHighlight: true,
  });

  function drawChart() {    
    fetchTable();

    var datefrom = $('#datefrom').val();
    var dateto = $('#dateto').val();
    var status = $('#status').val();
    var section_from = $('#section_from').val();
    var section_to = $('#section_to').val();

    var data = {
      datefrom: datefrom,
      dateto: dateto,
      status: status,
      section_from: section_from,
      section_to: section_to
    };

    $.get('{{ url("fetch/form_ketidaksesuaian/monitoring") }}', data, function(result, status, xhr) {
        if(result.status){

          var month = []; 
          var tgl = [];
          var cpar = [];
          var car = [];
          var verif = [];
          var close = [];

          $.each(result.datas, function(key, value) {
            tgl.push(value.week_date);
            cpar.push(parseInt(value.cpar));
            car.push(parseInt(value.car));
            verif.push(parseInt(value.verification));
            close.push(parseInt(value.close));
          })

          $('#chart').highcharts({
            chart: {
              type: 'column'
            },
            title: {
              text: 'Monitoring Form Ketidaksesuaian',
              style: {
                fontSize: '25px',
                fontWeight: 'bold'
              }
            },
            subtitle: {
              text: 'On '+result.year+' Last 30 Days',
              style: {
                fontSize: '1vw',
                fontWeight: 'bold'
              }
            },
            xAxis: {
              type: 'category',
              categories: tgl,
              lineWidth:2,
              lineColor:'#9e9e9e',
              gridLineWidth: 1,
              labels: {
                style: {
                    fontWeight:'Bold'
                  }
              }
            },
            yAxis: {
              lineWidth:2,
              lineColor:'#fff',
              type: 'linear',
                title: {
                  text: 'Total Kasus'
                },
              tickInterval: 1,  
              stackLabels: {
                  enabled: true,
                  style: {
                      fontWeight: 'bold',
                      color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                  }
              }
            },
            legend: {
              align: 'right',
              x: -30,
              verticalAlign: 'top',
              y: 20,
              reversed: true,
              itemStyle:{
                color: "white",
                fontSize: "12px",
                fontWeight: "bold",

              },
              floating: true,
              shadow: false
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                point: {
                  events: {
                    click: function () {
                      ShowModal(this.category,this.series.name,result.datefrom,result.dateto,result.section_from,result.section_to);
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
            series: [
              {
                  name: 'Close',
                  data: close,
                  color : '#5cb85c' //00f57f
              },
              {
                  name: 'Verifikasi',
                  data: verif,
                  color : '#448aff' //f5f500
              },
              {
                  name: 'Penanganan',
                  data: car,
                  color : '#f0ad4e' //f5f500
              },
              {
                  name: 'CPAR',
                  data: cpar,
                  color: '#ff6666', //ff6666
              },
            ]
          })
        } else{
          alert('Attempt to retrieve data failed');
        }
    })
  }

  function ShowModal(tgl, status, datefrom, dateto, sf, st) {
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
          "url" : "{{ url("index/form_ketidaksesuaian/detail") }}",
          "data" : {
            tgl : tgl,
            status : status,
            datefrom : datefrom,
            dateto : dateto,
            sf : sf,
            st : st,
          }
        },
      "columns": [
          { "data": "kategori", "width": "5%"},
          { "data": "judul" },
          { "data": "tanggal" , "width": "10%"},
          { "data": "section_from"},
          { "data": "section_to"},
          { "data": "status", "width": "7%"},
          { "data": "action", "width": "5%"}
        ]    });

    $('#judul_table').append().empty();
    $('#judul_table').append('<center><b>List Form Ketidaksesuaian Tanggal '+tgl+'</b></center>'); 
  }



  function fetchTable(){

    var datefrom = $('#datefrom').val();
    var dateto = $('#dateto').val();
    var status = $('#status').val();
    var section_from = $('#section_from').val();
    var section_to = $('#section_to').val();

    var data = {
      datefrom: datefrom,
      dateto: dateto,
      status: status,
      section_from: section_from,
      section_to: section_to
    };

    $.get('{{ url("index/form_ketidaksesuaian/table") }}', data, function(result, status, xhr){
      if(result.status){
          $("#tabelisi").find("td").remove();  
          $('#tabelisi').html("");
          var table = "";
          var statussl = "";
          var statuscf = "";
          var statusm = "";
          var reject = "";

          $.each(result.datas, function(key, value) {

            var sf = value.section_from.split('_');
            var st = value.section_to.split('_');

            var namasl = value.pelapor;
            var namasl2 = namasl.split(' ').slice(0,2).join(' ');

            if (value.namacf != null) {
              var namacf = value.namacf;
              var namacf2 = namacf.split(' ').slice(0,2).join(' ');
            }

            var namam = value.namam;
            var namam2 = namam.split(' ').slice(0,2).join(' ');

            if (value.namapiccar != null) {
              var namapiccar = value.namapiccar;
              var namapiccar2 = namapiccar.split(' ').slice(0,2).join(' ');            
            }

            if (value.namacfcar != null) {
              var namacfcar = value.namacfcar;
              var namacfcar2 = namacfcar.split(' ').slice(0,2).join(' ');
            }

            if (value.namamcar != null) {
              var namamcar = value.namamcar;
              var namamcar2 = namamcar.split(' ').slice(0,2).join(' ');
            }

            var color = "";
            var color2 = "";
            var d = 0;
            var e = 0;

            var urldetailcpar = '{{ url("index/form_ketidaksesuaian/detail/") }}';
            var urlverifikasi = '{{ url("index/form_ketidaksesuaian/verifikasicpar/") }}';
            var urlprint = '{{ url("index/form_ketidaksesuaian/print/") }}';
            
            var urldetailcar = '{{ url("index/form_ketidaksesuaian/response/") }}';
            var urlverifikasicar = '{{ url("index/form_ketidaksesuaian/verifikasicar/") }}';

            var urlverifikasibagian = '{{ url("index/form_ketidaksesuaian/verifikasibagian/") }}';

            if (value.posisi != "sl") {
              statusawal = '<a href="'+urlprint+'/'+value.id+'"><span class="label label-success">'+namasl2+'</span></a>';
              color = 'style="background-color:green"';
            }

            else {
              if (d == 0) {  
                  statusawal = '<a href="'+urldetailcpar+'/'+value.id+'"><span class="label label-danger zoom">'+namasl2+'</span></a>';
                  color = 'style="background-color:red"';                    
                  d = 1;
                } else {
                  statusawal = '';
                }
            }

            //jika cf tidak kosong
            if (value.namacf != null) {
              //Jika ter-approved
              if (value.approvalcf == "Approved") {
                if (value.posisi == "cf") {
                  if (d == 0) {
                    statuscf = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">'+namacf2+'</span></a>';   
                    color = 'style="background-color:red"';                  
                    d = 1;
                  } else {
                    statuscf = '';
                  }
                }
                else{
                    statuscf = '<a href="'+urlprint+'/'+value.id+'"><span class="label label-success">'+namacf2+'</span></a>';
                    color = 'style="background-color:green"'; 
                }
              }

              //Jika belum approved
              else{
                if (d == 0) {  
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
            if (value.approvalm == "Approved") {
              if (value.posisi == "m") {
                if (d == 0) {  
                    statusm = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">'+namam2+'</span></a>';
                    color = 'style="background-color:red"'; 
                    d = 1;
                  } else {
                    statusm = '';
                  }
              }
              else {
                statusm = '<a href="'+urlprint+'/'+value.id+'"><span class="label label-success">'+namam2+'</span></a>';
                color = 'style="background-color:green"'; 
              } 
            }
            else {
              if (d == 0) {  
                statusm = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">'+namam2+'</span></a>';
                color = 'style="background-color:red"';                   
                d = 1;
              } else {
                statusm = '';
              }
            }

            //Diterima Oleh Section Terkait Dan PIC nya kosong

            if (value.namapiccar != null) {
              if (value.posisi == "dept") {
                  if (d == 0) {  
                    statuspic = '<a href="'+urldetailcar+'/'+value.id+'"><span class="label label-danger">'+namapiccar2+'</span></a>';
                    color = 'style="background-color:red"';
                    d = 1;
                  } else {
                    statuspic = '';
                  }
              } else {
                  statuspic = '<a href="'+urlprint+'/'+value.id+'"><span class="label label-success">'+namapiccar2+'</span></a>';
                  color = 'style="background-color:green"';                    
              }
            }
            else{
              if (d == 0) {  
                statuspic = '<a href="'+urldetailcar+'/'+value.id+'"><span class="label label-danger">'+st[1]+'</span></a>';
                color = 'style="background-color:red"';
                d = 1;
              } else {
                statuspic = '';
              }
            }

            //jika cf car tidak kosong
            if (value.namacfcar != null) {
              //Jika ter-approved
              if (value.approvalcf_car == "Approved") {
                if (value.posisi == "deptcf") {
                  if (d == 0) {
                    statuscfcar = '<a href="'+urlverifikasicar+'/'+value.id+'"><span class="label label-danger">'+namacfcar2+'</span></a>';   
                    color = 'style="background-color:red"';                  
                    d = 1;
                  } else {
                    statuscfcar = '';
                  }
                }
                else{
                    statuscfcar = '<a href="'+urlprint+'/'+value.id+'"><span class="label label-success">'+namacfcar2+'</span></a>';
                    color = 'style="background-color:green"'; 
                }
              }

              //Jika belum approved
              else{
                if (d == 0) {  
                  statuscfcar = '<a href="'+urlverifikasicar+'/'+value.id+'"><span class="label label-danger">'+namacfcar2+'</span></a>'; 
                  color = 'style="background-color:red"';                  
                  d = 1;
                } else {
                  statuscfcar = '';
                }
              }
            }
            else{
              statuscfcar = 'None';
            }

            //manager CAR
            if (value.approvalm_car == "Approved") {
              if (value.posisi == "deptm") {
                if (d == 0) {  
                    statusmcar = '<a href="'+urlverifikasicar+'/'+value.id+'"><span class="label label-danger">'+namamcar2+'</span></a>';
                    color = 'style="background-color:red"'; 
                    d = 1;
                  } else {
                    statusmcar = '';
                  }
              }
              else {
                statusmcar = '<a href="'+urlprint+'/'+value.id+'"><span class="label label-success">'+namamcar2+'</span></a>';
                color = 'style="background-color:green"'; 
              } 
            }
            else {
              if (d == 0) {  
                statusmcar = '<a href="'+urlverifikasicar+'/'+value.id+'"><span class="label label-danger">'+namamcar2+'</span></a>';
                color = 'style="background-color:red"';                   
                d = 1;
              } else {
                statusmcar = '';
              }
            }


            //Diterima Oleh Bagian Pengirim

            if (value.status != "car" && value.status != "cpar") {
              if (value.posisi == "verif") {
                if (d == 0) {  
                    statusverifikasi = '<a href="'+urlverifikasibagian+'/'+value.id+'"><span class="label label-danger">'+sf[1]+'</span></a>';
                    color = 'style="background-color:red"'; 
                    d = 1;
                  } else {
                    statusverifikasi = '';
                  }
              }
              else {
                statusverifikasi = '<a href="'+urlprint+'/'+value.id+'"><span class="label label-success">'+sf[1]+'</span></a>';
                color = 'style="background-color:green"'; 
              } 
            }
            else {
              if (d == 0) {  
                statusverifikasi = '<a href="'+urlverifikasibagian+'/'+value.id+'"><span class="label label-danger">'+sf[1]+'</span></a>';
                color = 'style="background-color:red"';                   
                d = 1;
              } else {
                statusverifikasi = '';
              }
            }

            if (value.reject_all != null && value.status != "close") {
              reject = 'style="background-color:red"';
              var stat = ' - rejected';
            }
            else{
              reject = ""
              var stat = "";
            }

            if (value.posisi == "qa" && value.status == "cpar") {
              qa = 'style="background-color:red"';
              ketqa = 'Proses Verifikasi oleh QA'; 
            }
            else if (value.posisi == "qa" && value.status == "close"){
              qa = 'style="background-color:green"';
              ketqa = 'Masuk Kategori Diterbitkan CPAR oleh QA'; 
            }


            table += '<tr>';
            table += '<td '+reject+'>'+value.judul+''+stat+'</td>';
            table += '<td style="border-left:3px solid #f44336"><span class="label label-warning">'+value.tanggal+'</span></td>';
            table += '<td style="border-left:3px solid #f44336">'+capitalizeFirstLetter(sf[1])+'</td>';
            table += '<td style="border-left:3px solid #f44336">'+capitalizeFirstLetter(st[1])+'</td>';
            table += '<td style="border-left:3px solid #f44336">'+statusawal+'</td>';
            if (value.posisi != "qa") {
              table += '<td>'+statuscf+'</td>';
              table += '<td>'+statusm+'</td>';
              table += '<td style="border-left:3px solid #f44336">'+statuspic+'</td>';
              table += '<td>'+statuscfcar+'</td>';
              table += '<td>'+statusmcar+'</td>';
              table += '<td style="border-left:3px solid #f44336">'+statusverifikasi+'</td>';
            }else{
              table += '<td colspan="6" '+qa+'>'+ketqa+'</td>';
            }
          
            table += '</tr>';

              
          })

          $('#tabelisi').append(table);
      }
    })
  }

  function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
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