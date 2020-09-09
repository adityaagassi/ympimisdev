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
  background-color: #f0f0ff;  
  color:black;
}
table.table-bordered > tbody > tr > td{
  border:1px solid rgb(54, 59, 56);
  background-color: #f0f0ff;
  color: black;
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
    <span class="text-purple">Purchase Requisition Monitoring & Control</span>
  </h1>
  <br>
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
            <input type="text" class="form-control datepicker" id="datefrom" placeholder="Select Date From" onchange="drawChart()">
          </div>
        </div>
        <div class="col-xs-2">
          <div class="input-group date">
            <div class="input-group-addon bg-green" style="border: none;">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" class="form-control datepicker" id="dateto" placeholder="Select Date To" onchange="drawChart()">
          </div>
        </div>

        @if(Auth::user()->role_code == "MIS")
        <div class="col-md-2">
            <div class="input-group">
              <div class="input-group-addon bg-blue">
                <i class="fa fa-search"></i>
              </div>
              <select class="form-control select2" multiple="multiple" onchange="drawChart()" id="department" data-placeholder="Select Department" style="border-color: #605ca8" >
                  @foreach($department as $dept)
                    <option value="{{ $dept->department }}">{{ $dept->department }}</option>
                  @endforeach
                </select>
            </div>
        </div>
        @else
           <select class="form-control select2 hideselect" multiple="multiple" onchange="drawChart()" id="department" data-placeholder="Select Department" style="border-color: #605ca8">
             <option value="{{$emp_dept->department}}" selected="">{{$emp_dept->department}}</option>
           </select>
        @endif 
      </div>

      <div class="col-md-12">

        <div class="col-md-12" style="margin-top: 5px;background-color: #e0e0e0;text-align: center;">
            <span style="font-size: 24px;font-weight: bold;">Outstanding PR (Sign Verification)</span>
        </div>

        <div class="col-md-5" style="margin-top: 5px; padding:0 !important">
            <div id="chart" style="width: 99%"></div>
        </div>

        <div class="col-md-7" style="padding-right: 0;padding-left: 10px">
          <table id="tabelmonitor" class="table table-bordered" style="margin-top: 5px; width: 99%">
            <thead style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 12px;font-weight: bold">
              <tr>
                <th style="width: 15%; padding: 0;vertical-align: middle;font-size: 16px;background-color: #ffeb3b;" rowspan="2">No PR</th>
                <th style="width: 15%; padding: 0;vertical-align: middle;border-left:3px solid #000 !important;font-size: 16px;background-color: #ffeb3b;" rowspan="2">Submission Date</th>
                <th style="width: 15%; padding: 0;vertical-align: middle;border-left:3px solid #000 !important;font-size: 16px;background-color: #ffeb3b;" rowspan="2">Department</th>
                <th style="width: 55%; padding: 0;vertical-align: middle;border-left:3px solid #000 !important;font-size: 16px;background-color: #ffeb3b;" colspan="5">Progress PR</th>
              </tr>
              <tr>
                <th style="width: 5%; padding: 0;border-left:3px solid #000 !important;vertical-align: middle;background-color: #ffeb3b;">Staff</th>
                <th style="width: 5%; padding: 0;vertical-align: middle;background-color: #ffeb3b;">Manager</th>
                <th style="width: 5%; padding: 0;vertical-align: middle;background-color: #ffeb3b;">DGM</th>
                <th style="width: 5%; padding: 0;vertical-align: middle;background-color: #ffeb3b;">GM</th>
                <th style="width: 5%; padding: 0;vertical-align: middle;background-color: #ffeb3b;">Received By Purchasing</th>
              </tr>
            </thead>
            <tbody id="tabelisi">
            </tbody>
            <tfoot>
            </tfoot>
          </table>
        </div>
      </div>

      <div class="col-md-12">

        <div class="col-md-12" style="margin-top: 5px;background-color: #e0e0e0;text-align: center;">
            <span style="font-size: 24px;font-weight: bold;">Outstanding PR Yang Belum PO (Per Item)</span>
        </div>

        <div class="col-md-5" style="margin-top: 5px; padding:0 !important">
            <div id="chartundone" style="width: 99%"></div>
        </div>

        <div class="col-md-7" style="padding-right: 0;padding-left: 10px">
          <table id="tabelmonitor" class="table table-bordered" style="margin-top: 5px; width: 99%">
            <thead style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 12px;font-weight: bold">
              <tr>
                <th style="width: 10%; padding: 5;vertical-align: middle;font-size: 16px;background-color: #ffeb3b;">No PR</th>
                <th style="width: 6%; padding: 5;vertical-align: middle;border-left:3px solid #000 !important;font-size: 16px;background-color: #ffeb3b;">Department</th>
                <th style="width: 6%; padding: 5;vertical-align: middle;border-left:3px solid #000 !important;font-size: 16px;background-color: #ffeb3b;">Kode Item</th>
                <th style="width: 15%; padding: 5;vertical-align: middle;border-left:3px solid #000 !important;font-size: 16px;background-color: #ffeb3b;">Deskripsi</th>
                <th style="width: 10%; padding: 5;vertical-align: middle;border-left:3px solid #000 !important;font-size: 16px;background-color: #ffeb3b;">Request Date</th>
              </tr>
            </thead>
            <tbody id="tabelisipo_undone">
            </tbody>
            <tfoot>
            </tfoot>
          </table>
        </div>
      </div>

    </div>
  </div>

  <br>

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
                    <th>Nomor PR</th>
                    <th>Department</th>
                    <th>Submission Date</th>
                    <th>User</th>
                    <th>Nomor Budget</th>
                    <th>Att</th>
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


  <div class="modal fade" id="modalPO">
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
              <table id="tabelPO" class="table table-striped table-bordered table-hover" style="width: 100%;"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>Nomor PR</th>
                    <th>Kode Item</th>
                    <th>Deskripsi</th>
                    <th>Spesifikasi</th>
                    <th>Stock</th>
                    <th>Request Date</th>
                    <th>Mata Uang</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>UOM</th>
                    <th>Total</th>
                    <th>Status</th>
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

    $('.hideselect').next(".select2-container").hide();

    drawChart();
    fetchTable();
  });

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

  $('.datepicker').datepicker({
    autoclose: true,
    format: "yyyy-mm",
    todayHighlight: true,
    startView: "months", 
    minViewMode: "months",
    autoclose: true,
  });

  function drawChart() {
    
    fetchTable();

    var datefrom = $('#datefrom').val();
    var dateto = $('#dateto').val();
    var department = $('#department').val();


    var data = {
      datefrom: datefrom,
      dateto: dateto,
      department: department, //.split(';')
    };

    $.get('{{ url("fetch/purchase_requisition/monitoring") }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){

          var bulan = [], jml = [], dept = [], jml_dept = [], not_sign = [], sign = [], no_pr = [], belum_po = [], sudah_po = [];

          $.each(result.datas, function(key, value) {
            bulan.push(value.bulan);
            not_sign.push(parseInt(value.NotSigned));
            sign.push(parseInt(value.Signed));
          })

          $.each(result.data_pr_belum_po, function(key, value) {
            no_pr.push(value.no_pr);
            belum_po.push(parseInt(value.belum_po));
            sudah_po.push(parseInt(value.sudah_po));
          })

          $('#chart').highcharts({
            chart: {
              type: 'column',
              height: 350
            },
            title: {
              text: 'PR List By Month',
              style: {
                fontSize: '24px',
                fontWeight: 'bold'
              }
            },
            xAxis: {
              type: 'category',
              categories: bulan,
              lineWidth:2,
              lineColor:'#9e9e9e',
              gridLineWidth: 1
            },
            yAxis: {
              lineWidth:2,
              lineColor:'#fff',
              type: 'linear',
                title: {
                  enabled:false
                },
              tickInterval: 5,  
              stackLabels: {
                  enabled: true,
                  style: {
                      fontWeight: 'bold',
                      color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                  }
              }
            },
            legend: {
              enabled:false,
              reversed: true,
              itemStyle:{
                color: "white",
                fontSize: "12px",
                fontWeight: "bold",

              },
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                point: {
                  events: {
                    click: function () {
                      ShowModalPR(this.category,this.series.name,result.datefrom,result.dateto,result.department);
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
                name: 'Sign Not Completed',
                color: '#ff7043', //ff6666
                data: not_sign
              },
              {
                name: 'Sign Completed',
                color: '#00a65a',
                data: sign
              }
              // {
              //   name: 'Sign Not Completed',
              //   color: 'rgba(255, 0, 0, 0.25)',
              //   data: not_sign,
              //   type: 'spline'
              // },
              // {
              //   name: 'Sign Completed',
              //   color: '#5cb85c',
              //   data: sign,
              //   type: 'spline'
              // }
            ]
          })


          $('#chartundone').highcharts({
            chart: {
              type: 'column',
              height: 350
            },
            title: {
              text: 'Outstanding PR Belum PO (Per Item)',
              style: {
                fontSize: '24px',
                fontWeight: 'bold'
              }
            },
            xAxis: {
              type: 'category',
              categories: no_pr,
              lineWidth:2,
              lineColor:'#9e9e9e',
              gridLineWidth: 1
            },
            yAxis: {
              lineWidth:2,
              lineColor:'#fff',
              type: 'linear',
              title: {
                enabled:false
              },
              tickInterval: 3,  
              stackLabels: {
                  enabled: true,
                  style: {
                      fontWeight: 'bold',
                      color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                  }
              }
            },
            legend: {
              enabled:false,
              reversed: true,
              itemStyle:{
                color: "white",
                fontSize: "12px",
                fontWeight: "bold",

              },
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                point: {
                  events: {
                    click: function () {
                      ShowModalPO(this.category,this.series.name,result.datefrom,result.dateto,result.department);
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
                name: 'Belum PO',
                color: '#ff7043', //ff6666
                data: belum_po
              },
              {
                name: 'Sudah PO',
                color: '#00a65a',
                data: sudah_po
              }
            ]
          })


        } else{
          alert('Attempt to retrieve data failed');
        }



      }
    })
  }

  

  function fetchTable(){

    var datefrom = $('#datefrom').val();
    var dateto = $('#dateto').val();
    var department = $('#department').val();

    var data = {
      datefrom: datefrom,
      dateto: dateto,
      department: department,
    };

    $.get('{{ url("purchase_requisition/table") }}', data, function(result, status, xhr){
      if(xhr.status == 200){
        if(result.status){

          $('#tabelmonitor').DataTable().clear();
          $('#tabelmonitor').DataTable().destroy();

          $("#tabelisi").find("td").remove();  
          $('#tabelisi').html("");
          
          var table = "";
          var user = "";
          var manager = "";
          var dgm = "";
          var gm = "";
          var pch = "";


          $.each(result.datas, function(key, value) {

          var emp_name = value.emp_name;
          var username = emp_name.split(' ').slice(0,2).join(' ');

          if (value.manager_name != null) {
            var manager_name = value.manager_name;
            var managername = manager_name.split(' ').slice(0,2).join(' ');
          }

          var dgm_name = value.dgm;
          var dgmname = dgm_name.split(' ').slice(0,2).join(' ');

          var gm_name = value.gm;
          var gmname = gm_name.split(' ').slice(0,2).join(' ');

          var color = "";
          var color2 = "";
          var d = 0;
          var e = 0;

            //CPAR
            var urldetail = '{{ url("purchase_requisition") }}';
            var urlreport = '{{ url("purchase_requisition/report/") }}';
            var urlverifikasi = '{{ url("purchase_requisition/verifikasi/") }}';


            if (value.posisi != "user") {
              user = '<a href="'+urlreport+'/'+value.id+'"><span class="label label-success">'+username+'</span></a>';
              color = 'style="background-color:green"';
            }
            else {
              if (d == 0) {  
                  user = '<a href="'+urldetail+'"><span class="label label-danger zoom">'+username+'</span></a>';
                  color = 'style="background-color:red"';                    
                  d = 1;
                } else {
                  user = '';
                }
            }

              //jika manager
              if (value.manager_name != null) {
                //manager
                if (value.approvalm == "Approved") {
                    if (value.posisi == "manager") {
                        if (d == 0) {  
                            manager = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">'+managername+'</span></a>';   
                            color = 'style="background-color:red"';                  
                            d = 1;
                          } else {
                            manager = '';
                          }
                    }
                    else{
                        manager = '<a href="'+urlreport+'/'+value.id+'"><span class="label label-success">'+managername+'</span></a>';
                        color = 'style="background-color:green"'; 
                    }
                }
                else{
                  if (d == 0) {  
                    manager = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">'+managername+'</span></a>'; 
                    color = 'style="background-color:red"';                  
                    d = 1;
                  } else {
                    manager = '';
                  }
                }
              }
              else{
                manager = '<span class="label label-warning">None</span>'; 
              }


              //DGM
              if (value.approvaldgm == "Approved") {
                  if (value.posisi == "dgm") {
                    if (d == 0) {  
                        dgm = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">'+dgmname+'</span></a>';
                        color = 'style="background-color:red"';              
                        d = 1;
                      } else {
                        dgm = '';
                      }
                  }
                  else {
                    dgm = '<a href="'+urlreport+'/'+value.id+'"><span class="label label-success">'+dgmname+'</span></a>';
                    color = 'style="background-color:green"'; 
                  } 
              }
              else {
                if (d == 0) {  
                  dgm = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">'+dgmname+'</span></a>';
                  color = 'style="background-color:red"';                   
                  d = 1;
                } else {
                  dgm = '';
                }
              }

              //GM
              if (value.approvalgm == "Approved") {
                  if (value.posisi == "gm") {
                      if (d == 0) {  
                        gm = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">'+gmname+'</span></a>';
                        color = 'style="background-color:red"'; 
                        d = 1;
                      } else {
                        gm = '';
                      }
                  } else {
                    gm = '<a href="'+urlreport+'/'+value.id+'"><span class="label label-success">'+gmname+'</span></a>'; 
                    color = 'style="background-color:green"'; 
                  }
              } 

              else {
                if (d == 0) {  
                  gm = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">'+gmname+'</span></a>';
                  color = 'style="background-color:red"'; 
                  d = 1;
                } else {
                  gm = '';
                }
              }

              //receive
              if (value.receive_date != null) {
                  if (value.status == "approval_acc") {
                      if (d == 0) {  
                        pch = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">Purchasing</span></a>'; 
                        color = 'style="background-color:red"';
                        d = 1;
                      } else {
                        pch = '';
                      }
                  } else {
                      pch = '<a href="'+urlreport+'/'+value.id+'"><span class="label label-success">'+value.receive_date+'</span></a>'; 
                      color = 'style="background-color:green"';       
                  }
              }
              else{
                if (d == 0) {  
                  pch = '<a href="'+urlverifikasi+'/'+value.id+'"><span class="label label-danger">Purchasing</span></a>'; 
                  color = 'style="background-color:red"';
                  d = 1;
                } else {
                  pch = '';
                }
              }

            table += '<tr>';
            table += '<td>'+value.no_pr+'</td>';
            table += '<td style="border-left:3px solid #000">'+value.submission_date+'</td>';
            table += '<td style="border-left:3px solid #000">'+value.department_shortname+'</td>';
            table += '<td style="border-left:3px solid #000">'+user+'</td>';  
            table += '<td>'+manager+'</td>';
            table += '<td>'+dgm+'</td>';
            table += '<td>'+gm+'</td>';
            table += '<td>'+pch+'</td>';
            table += '</tr>';
          })


          $('#tabelisi').append(table);

          $('#tabelmonitor').DataTable({
            'responsive':true,
            'paging': true,
            'lengthChange': false,
            'pageLength': 10,
            'searching': false,
            'ordering': false,
            'order': [],
            'info': false,
            'autoWidth': true,
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bAutoWidth": false,
            "processing": true
          });


          $("#tabelisipo_undone").find("td").remove();  
          $('#tabelisipo_undone').html("");
          
          var table_belum_po = "";

          $.each(result.data_pr_belum_po, function(key, value) {
            table_belum_po += '<tr>';
            table_belum_po += '<td>'+value.no_pr+'</td>';
            table_belum_po += '<td style="border-left:3px solid #000">'+value.department_shortname+'</td>';
            table_belum_po += '<td style="border-left:3px solid #000">'+value.item_code+'</td>';
            table_belum_po += '<td style="border-left:3px solid #000">'+value.item_desc+'</td>';
            table_belum_po += '<td style="border-left:3px solid #000">'+value.item_request_date+'</td>';
            table_belum_po += '</tr>';
          })

          $('#tabelisipo_undone').append(table_belum_po);

        }
      }
    })
  }

  function ShowModalPR(bulan, status, datefrom, dateto, department) {
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
          "url" : "{{ url("purchase_requisition/detail") }}",
          "data" : {
            bulan : bulan,
            status : status,
            department : department,
            datefrom : datefrom,
            dateto : dateto
          }
        },
      "columns": [
          { "data": "no_pr" },
          { "data": "department" },
          { "data": "submission_date" },
          { "data": "emp_name" },
          { "data": "no_budget" },
          { "data": "file" },
          { "data": "status" },
          { "data": "action", "width": "15%"}
        ]    });

    $('#judul_table').append().empty();
    $('#judul_table').append('<center><b>'+status+' Bulan '+bulan+'</center></b>');
    
  }

  function ShowModalPO(pr, status, datefrom, dateto, department) {
    tabel = $('#tabelPO').DataTable();
    tabel.destroy();

    $("#modalPO").modal("show");

    var table = $('#tabelPO').DataTable({
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
          "url" : "{{ url("purchase_requisition/detailPO") }}",
          "data" : {
            pr : pr,
            status : status,
            department : department,
            datefrom : datefrom,
            dateto : dateto
          }
        },
      "columns": [
          { "data": "no_pr" },
          { "data": "item_code" },
          { "data": "item_desc" },
          { "data": "item_spec" },
          { "data": "item_stock" },
          { "data": "item_request_date" },
          { "data": "item_currency" },
          { "data": "item_price" },
          { "data": "item_qty" },
          { "data": "item_uom" },
          { "data": "item_amount" },
          { "data": "status", "width": "15%"}
        ]    });

    $('#judul_table').append().empty();
    $('#judul_table').append('<center><b>'+status+' No PR '+pr+'</center></b>');
    
  }

  Highcharts.createElement('link', {
          href: '{{ url("fonts/UnicaOne.css")}}',
          rel: 'stylesheet',
          type: 'text/css'
        }, null, document.getElementsByTagName('head')[0]);

        Highcharts.theme = {
          colors: ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572',   
                   '#FF9655', '#FFF263', '#6AF9C4'],
          chart: {
              backgroundColor: {
                  linearGradient: [500, 500, 500, 500],
                  stops: [
                      [0, 'rgb(255, 255, 255)'],
                      [1, 'rgb(240, 240, 255)']
                  ]
              },
          },
          title: {
              style: {
                  color: '#000',
                  font: 'bold 16px "Trebuchet MS", Verdana, sans-serif'
              }
          },
          subtitle: {
              style: {
                  color: '#666666',
                  font: 'bold 12px "Trebuchet MS", Verdana, sans-serif'
              }
          },
          legend: {
              itemStyle: {
                  font: '9pt Trebuchet MS, Verdana, sans-serif',
                  color: 'black'
              },
              itemHoverStyle:{
                  color: 'gray'
              }   
          }
};
// Apply the theme
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