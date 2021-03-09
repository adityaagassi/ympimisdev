@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
table.table-bordered{
    border:1px solid white;
  }
  table.table-bordered > thead > tr > th{
    border:1px solid rgb(54, 59, 56) !important;
    background-color: #212121;
    text-align: center;
    vertical-align: middle;
    color:white;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid rgb(204, 209, 56);
    color: white;
    vertical-align: middle;
    text-align: right;
  }
  table.table-condensed > thead > tr > th{   
    color: black
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid rgb(150,150,150);
    padding:0;
  }

  #example2{
    border:1px solid black;    
  }

  #example2 > tbody > tr > td {
    color: black;
  }

  .dataTables_length {
    color: white;
  }

  .dataTables_filter {
    color: white;
  }
  thead input {
    width: 100%;
    padding: 3px;
    box-sizing: border-box;
  }
  td:hover {
    overflow: visible;
  }
  #tabelmonitor{
    font-size: 0.83vw;
  }

  #tabelisi > tr:hover {
    background-color: #212121;
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

 p > img{
  max-width: 300px;
  height: auto !important;
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
    <span class="text-purple">PO Monitoring</span>
  </h1>
  <br>
</section>
@endsection

@section('content')

<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <div>
      <center>
        <span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i></span>
      </center>
    </div>
  </div>

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0; padding-bottom: 0">
  <div class="row">
    <div class="col-md-12" style="padding: 1px !important">
        <div class="col-md-2">
            <div class="input-group">
              <div class="input-group-addon bg-blue">
                <i class="fa fa-search"></i>
              </div>
              <select class="form-control select2" multiple="multiple" onchange="drawChart()" id="category" data-placeholder="Select Category" style="border-color: #605ca8" >
                  <option value=""></option>
                  <option value="Fixed Asset">Fixed Asset</option>
                  <option value="Expenses">Expenses</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="input-group">
              <div class="input-group-addon bg-blue">
                <i class="fa fa-search"></i>
              </div>
              <select class="form-control select2" multiple="multiple" onchange="drawChart()" id="fiscal_year" data-placeholder="Select Fiscal" style="border-color: #605ca8" >
                  <option value=""></option>
                  <option value="FY197">FY197</option>
                  <option value="FY198">FY198</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="input-group">
              <div class="input-group-addon bg-blue">
                <i class="fa fa-search"></i>
              </div>
              <select class="form-control select2" onchange="drawChart()" id="department" data-placeholder="Select Department" style="border-color: #605ca8" >
                  <option value=""></option>
                  @foreach($department as $dept)
                    <option value="{{$dept->department}}">{{$dept->department}}</option>
                  @endforeach
                </select>
            </div>
        </div>
      </div>

      <div class="col-md-12">
          <div class="col-md-12" style="padding:0">
              <div id="container_resume"></div>
          </div>
      </div>  

      <div class="col-md-12" style="">
        <table id="tabelmonitor" class="table table-bordered" style="margin-top: 5px; width: 99%;border: 1px solid #ddd;">
          <thead style="background-color: rgb(0,0,0); color: rgb(255,255,255); font-size: 12px;font-weight: bold">
            <tr>
              <th style="width: 3%; vertical-align: middle;font-size: 16px;">Budget Name</th>
              <th style="width: 3%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">Account Name</th>
              <th style="width: 3%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">Category</th>
              <th style="width: 3%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">Keterangan</th>
              <th style="width: 2%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">Apr</th>
              <th style="width: 2%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">May</th>
              <th style="width: 2%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">Jun</th>
              <th style="width: 2%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">Jul</th>
              <th style="width: 2%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">Aug</th>
              <th style="width: 2%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">Sep</th>
              <th style="width: 2%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">Oct</th>
              <th style="width: 2%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">Nov</th>
              <th style="width: 2%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">Des</th>
              <th style="width: 2%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">Jan</th>
              <th style="width: 2%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">Feb</th>
              <th style="width: 2%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">Mar</th>
              <th style="width: 2%; vertical-align: middle;border-left:1px solid yellow !important;font-size: 16px;">Total</th>
            </tr>
          </thead>
          <tbody id="tabelisi">
          </tbody>
          <tfoot>
            <tr>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
            </tr>
          </tfoot>
        </table>
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
    fetchTable();
    setInterval(fetchTable, 300000);
  });

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

  $('.datepicker').datepicker({
    format: "yyyy-mm",
    startView: "months", 
    minViewMode: "months",
    autoclose: true,
  });

  function drawChart() {
    fetchTable();


    $("#loading").show();
    var category = $('#category').val();
    var fy = $('#fiscal_year').val();
    var department = $('#department').val();

    var data = {
      category: category,
      fy:fy,
      department:department
    };

    $.get('{{ url("fetch/budget/monthly") }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){

          var bulan = [], awal = [], sisa = [], pr = [], investment = [], po = [];
          var category = [], amount_category = [], act_category = [], pr_category = [], po_category = [], inv_category = [];
          var fy;


          $.each(result.resume, function(key, value) {
            fy = value.periode;
            bulan.push('April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember', 'Januari', 'Februari', 'Maret');
            awal.push(value.apr_simulasi,value.may_simulasi,value.jun_simulasi,value.jul_simulasi,value.aug_simulasi,value.sep_simulasi,value.oct_simulasi,value.nov_simulasi,value.dec_simulasi,value.jan_simulasi,value.feb_simulasi,value.mar_simulasi);
          })

          $.each(result.act, function(key, value) {
            pr.push(value.PR);
            investment.push(value.Investment);
            po.push(value.PO);
            sisa.push(value.Actual);
          })

          $('#container_resume').highcharts({
            chart: {
              type: 'column',
              backgroundColor : null
            },
            title: {
              text: 'Simulasi & Actual Budget '+fy,
              style: {
                fontSize: '24px',
                fontWeight: 'bold'
              }
            },
            xAxis: {
              type: 'category',
              tickInterval: 1,
              gridLineWidth: 1,
              categories: bulan,
              crosshair: true,
              lineWidth:2,
              lineColor:'#9e9e9e'
            },
            yAxis: {
              enabled:false,
              title:""
            },
            legend: {
              itemStyle:{
                color: "white",
                fontSize: "12px",
                fontWeight: "bold",

              },
            },
            tooltip: {
              headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
              pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
              '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
              footerFormat: '</table>',
              shared: true,
              useHTML: true
            },
            plotOptions: {
              column: {
                stacking: 'normal',
                pointPadding: 0.05,
                groupPadding: 0.1,
                borderWidth: 0,
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
            series: [
              {
                name: 'Plan',
                data: awal,
                stack: 'alone'
              },
              {
                name: 'Unrealized PR',
                data: pr,
                color: '#ffeb3b',
                stack: 'all'
              },
              {
                name: 'Unrealized Investment',
                data: investment,
                color: '#795548',
                stack: 'all'
              },
              {
                name: 'Unrealized PO',
                data: po,
                color: '#607d8b',
                stack: 'all'
              },
              {
                name: 'Actual',
                data: sisa,
                color: '#90ee7e',
                stack: 'all'
              }

            ]
          })
          $('#loading').hide();

        } else{
          alert('Attempt to retrieve data failed');
        }

      }
    })
  }

  function fetchTable(){

    var category = $('#category').val();
    var fy = $('#fiscal_year').val();
    var department = $('#department').val();

    var data = {
      category: category,
      fy:fy,
      department:department
    };

    $.get('{{ url("fetch/budget_monthly/table") }}', data, function(result, status, xhr){
      if(result.status){

        // $('#tabelmonitor').DataTable().clear();
        // $('#tabelmonitor').DataTable().destroy();    

        $("#tabelisi").find("td").remove();  
        $('#tabelisi').html("");

        var table = "";

        $.each(result.datas, function(key, value) {


        var budget_awal_total = value.apr_budget_awal + value.may_budget_awal + value.jun_budget_awal + value.jul_budget_awal + value.aug_budget_awal + value.sep_budget_awal + value.oct_budget_awal + value.nov_budget_awal + value.dec_budget_awal + value.jan_budget_awal + value.feb_budget_awal + value.mar_budget_awal;

            table += '<tr>';
            table += '<td rowspan="5" style="text-align:left;">'+value.budget_no+'<br>'+value.description+'</td>';
            table += '<td rowspan="5" style="text-align:left;border-left:1px solid yellow">'+value.account_name+'</span></td>';
            table += '<td rowspan="5" style="text-align:left;border-left:1px solid yellow">'+value.category+'</td>';
            table += '<td style="text-align:left;border-left:1px solid yellow">Budget</td>';
            table += '<td style="border-left:1px solid yellow">'+value.apr_budget_awal+'</td>';
            table += '<td style="border-left:1px solid yellow">'+value.may_budget_awal+'</td>';
            table += '<td style="border-left:1px solid yellow">'+value.jun_budget_awal+'</td>';
            table += '<td style="border-left:1px solid yellow">'+value.jul_budget_awal+'</td>';
            table += '<td style="border-left:1px solid yellow">'+value.aug_budget_awal+'</td>';
            table += '<td style="border-left:1px solid yellow">'+value.sep_budget_awal+'</td>';
            table += '<td style="border-left:1px solid yellow">'+value.oct_budget_awal+'</td>';
            table += '<td style="border-left:1px solid yellow">'+value.nov_budget_awal+'</td>';
            table += '<td style="border-left:1px solid yellow">'+value.dec_budget_awal+'</td>';
            table += '<td style="border-left:1px solid yellow">'+value.jan_budget_awal+'</td>';
            table += '<td style="border-left:1px solid yellow">'+value.feb_budget_awal+'</td>';
            table += '<td style="border-left:1px solid yellow">'+value.mar_budget_awal+'</td>';
            table += '<td style="border-left:1px solid yellow;background-color:green">'+budget_awal_total.toFixed(2)+'</td>';
            table += '</tr>';

        var budget_adj_total = value.apr_after_adj + value.may_after_adj + value.jun_after_adj + value.jul_after_adj + value.aug_after_adj + value.sep_after_adj + value.oct_after_adj + value.nov_after_adj + value.dec_after_adj + value.jan_after_adj + value.feb_after_adj + value.mar_after_adj;

            table += '<tr>';
            table += '<td style="text-align:left;border-left:1px solid yellow">Forecast</td>';
            table += '<td style="border-left:1px solid yellow">'+value.apr_after_adj+'</td>';
            table += '<td style="border-left:1px solid yellow">'+value.may_after_adj+'</td>';
            table += '<td style="border-left:1px solid yellow">'+value.jun_after_adj+'</td>';
            table += '<td style="border-left:1px solid yellow">'+value.jul_after_adj+'</td>';
            table += '<td style="border-left:1px solid yellow">'+value.aug_after_adj+'</td>';
            table += '<td style="border-left:1px solid yellow">'+value.sep_after_adj+'</td>';
            table += '<td style="border-left:1px solid yellow">'+value.oct_after_adj+'</td>';
            table += '<td style="border-left:1px solid yellow">'+value.nov_after_adj+'</td>';
            table += '<td style="border-left:1px solid yellow">'+value.dec_after_adj+'</td>';
            table += '<td style="border-left:1px solid yellow">'+value.jan_after_adj+'</td>';
            table += '<td style="border-left:1px solid yellow">'+value.feb_after_adj+'</td>';
            table += '<td style="border-left:1px solid yellow">'+value.mar_after_adj+'</td>';
            table += '<td style="border-left:1px solid yellow;background-color:green">'+budget_adj_total.toFixed(2)+'</td>';
            table += '</tr>';

            table += '<tr>';
            table += '<td style="text-align:left;border-left:1px solid yellow">Actual</td>';

            var stat = 0;
            var actual_total = 0;
            var actual_apr = 0;
            var actual_may = 0;
            var actual_jun = 0;
            var actual_jul = 0;
            var actual_aug = 0;
            var actual_sep = 0;
            var actual_oct = 0;
            var actual_nov = 0;
            var actual_dec = 0;
            var actual_jan = 0;
            var actual_feb = 0;
            var actual_mar = 0;

            $.each(result.actual, function(key2, value2) {

              if(value2.budget_no == value.budget_no) {

                if (value2.bulan == "apr" || value2.bulan == "Apr") {
                  table += '<td style="border-left:1px solid yellow">'+value2.Actual+'</td>';
                  stat = 1;                 

                  actual_total += value2.Actual;
                  actual_apr += value2.Actual;
                }
                else{
                  if (stat == 0) {
                    table += '<td style="border-left:1px solid yellow">0</td>';
                    stat = 1;
                  }
                }

                if (value2.bulan == "may" || value2.bulan == "May") {
                  table += '<td style="border-left:1px solid yellow">'+value2.Actual+'</td>';
                  stat = 1;            

                  actual_total += value2.Actual;    
                  actual_may += value2.Actual; 
                }
                else{
                  if (stat == 0) {
                    table += '<td style="border-left:1px solid yellow">0</td>';
                    stat = 1;
                  }
                }

                if (value2.bulan == "jun" || value2.bulan == "Jun") {
                  table += '<td style="border-left:1px solid yellow">'+value2.Actual+'</td>';
                  stat = 1;      

                  actual_total += value2.Actual;  
                  actual_jun += value2.Actual;         
                }
                else{
                  if (stat == 0) {
                    table += '<td style="border-left:1px solid yellow">0</td>';
                    stat = 1;
                  }
                }

                if (value2.bulan == "jul" || value2.bulan == "Jul") {
                  table += '<td style="border-left:1px solid yellow">'+value2.Actual+'</td>';
                  stat = 1;     

                  actual_total += value2.Actual;
                  actual_jul += value2.Actual;        
                }
                else{
                  if (stat == 0) {
                    table += '<td style="border-left:1px solid yellow">0</td>';
                    stat = 1;
                  }
                }

                if (value2.bulan == "aug" || value2.bulan == "Aug") {
                  table += '<td style="border-left:1px solid yellow">'+value2.Actual+'</td>';
                  stat = 1;       

                  actual_total += value2.Actual;
                  actual_aug += value2.Actual;          
                }
                else{
                  if (stat == 0) {
                    table += '<td style="border-left:1px solid yellow">0</td>';
                    stat = 1;
                  }
                }

                if (value2.bulan == "sep" || value2.bulan == "Sep") {
                  table += '<td style="border-left:1px solid yellow">'+value2.Actual+'</td>';
                  stat = 1;     

                  actual_total += value2.Actual;
                  actual_sep += value2.Actual;            
                }
                else{
                  if (stat == 0) {
                    table += '<td style="border-left:1px solid yellow">0</td>';
                    stat = 1;
                  }
                }

                if (value2.bulan == "oct" || value2.bulan == "Oct") {
                  table += '<td style="border-left:1px solid yellow">'+value2.Actual+'</td>';
                  stat = 1;   

                  actual_total += value2.Actual;
                  actual_oct += value2.Actual;              
                }
                else{
                  if (stat == 0) {
                    table += '<td style="border-left:1px solid yellow">0</td>';
                    stat = 1;
                  }
                }

                if (value2.bulan == "nov" || value2.bulan == "Nov") {
                  table += '<td style="border-left:1px solid yellow">'+value2.Actual+'</td>';
                  stat = 1;  

                  actual_total += value2.Actual;
                  actual_nov += value2.Actual;               
                }
                else{
                  if (stat == 0) {
                    table += '<td style="border-left:1px solid yellow">0</td>';
                    stat = 1;
                  }
                }

                if (value2.bulan == "dec" || value2.bulan == "Dec") {
                  table += '<td style="border-left:1px solid yellow">'+value2.Actual+'</td>';
                  stat = 1;      

                  actual_total += value2.Actual;
                  actual_dec += value2.Actual;           
                }
                else{
                  if (stat == 0) {
                    table += '<td style="border-left:1px solid yellow">0</td>';
                    stat = 1;
                  }
                }

                if (value2.bulan == "jan" || value2.bulan == "Jan") {
                  table += '<td style="border-left:1px solid yellow">'+value2.Actual+'</td>';
                  stat = 1;    

                  actual_total += value2.Actual;
                  actual_jan += value2.Actual;             
                }
                else{
                  if (stat == 0) {
                    table += '<td style="border-left:1px solid yellow">0</td>';
                    stat = 1;
                  }
                }

                if (value2.bulan == "feb" || value2.bulan == "Feb") {
                  table += '<td style="border-left:1px solid yellow">'+value2.Actual+'</td>';
                  stat = 1; 

                  actual_total += value2.Actual;  
                  actual_feb += value2.Actual;              
                }
                else{
                  if (stat == 0) {
                    table += '<td style="border-left:1px solid yellow">0</td>';
                    stat = 1;
                  }
                }

                if (value2.bulan == "mar" || value2.bulan == "Mar") {
                  table += '<td style="border-left:1px solid yellow">'+value2.Actual+'</td>';
                  stat = 1;  

                  actual_total += value2.Actual;
                  actual_mar += value2.Actual;               
                }
                else{
                  if (stat == 0) {
                    table += '<td style="border-left:1px solid yellow">0</td>';
                    stat = 1;
                  }
                }

              }

            });


            table += '<td style="border-left:1px solid yellow;background-color:green">'+actual_total.toFixed(2)+'</td>';
            table += '</tr>';

            table += '<tr>';
            table += '<td style="text-align:left;border-left:1px solid yellow">Diffbdg</td>';

            var aprdiffbdg = actual_apr - value.apr_budget_awal;
            var maydiffbdg = actual_may - value.may_budget_awal;
            var jundiffbdg = actual_jun - value.jun_budget_awal;
            var juldiffbdg = actual_jul - value.jul_budget_awal;
            var augdiffbdg = actual_aug - value.aug_budget_awal;
            var sepdiffbdg = actual_sep - value.sep_budget_awal;
            var octdiffbdg = actual_oct - value.oct_budget_awal;
            var novdiffbdg = actual_nov - value.nov_budget_awal;
            var decdiffbdg = actual_dec - value.dec_budget_awal;
            var jandiffbdg = actual_jan - value.jan_budget_awal;
            var febdiffbdg = actual_feb - value.feb_budget_awal;
            var mardiffbdg = actual_mar - value.mar_budget_awal;

            var totaldiffbdg = aprdiffbdg + maydiffbdg + jundiffbdg + juldiffbdg + augdiffbdg + sepdiffbdg + octdiffbdg + novdiffbdg + decdiffbdg + jandiffbdg + febdiffbdg + mardiffbdg;

            table += '<td style="border-left:1px solid yellow">'+aprdiffbdg.toFixed(2)+'</td>';
            table += '<td style="border-left:1px solid yellow">'+maydiffbdg.toFixed(2)+'</td>';
            table += '<td style="border-left:1px solid yellow">'+jundiffbdg.toFixed(2)+'</td>';
            table += '<td style="border-left:1px solid yellow">'+juldiffbdg.toFixed(2)+'</td>';
            table += '<td style="border-left:1px solid yellow">'+augdiffbdg.toFixed(2)+'</td>';
            table += '<td style="border-left:1px solid yellow">'+sepdiffbdg.toFixed(2)+'</td>';
            table += '<td style="border-left:1px solid yellow">'+octdiffbdg.toFixed(2)+'</td>';
            table += '<td style="border-left:1px solid yellow">'+novdiffbdg.toFixed(2)+'</td>';
            table += '<td style="border-left:1px solid yellow">'+decdiffbdg.toFixed(2)+'</td>';
            table += '<td style="border-left:1px solid yellow">'+jandiffbdg.toFixed(2)+'</td>';
            table += '<td style="border-left:1px solid yellow">'+febdiffbdg.toFixed(2)+'</td>';
            table += '<td style="border-left:1px solid yellow">'+mardiffbdg.toFixed(2)+'</td>';
            table += '<td style="border-left:1px solid yellow;background-color:green">'+totaldiffbdg.toFixed(2)+'</td>';
            table += '</tr>';

            var aprdifffrc = actual_apr - value.apr_after_adj;
            var maydifffrc = actual_may - value.may_after_adj;
            var jundifffrc = actual_jun - value.jun_after_adj;
            var juldifffrc = actual_jul - value.jul_after_adj;
            var augdifffrc = actual_aug - value.aug_after_adj;
            var sepdifffrc = actual_sep - value.sep_after_adj;
            var octdifffrc = actual_oct - value.oct_after_adj;
            var novdifffrc = actual_nov - value.nov_after_adj;
            var decdifffrc = actual_dec - value.dec_after_adj;
            var jandifffrc = actual_jan - value.jan_after_adj;
            var febdifffrc = actual_feb - value.feb_after_adj;
            var mardifffrc = actual_mar - value.mar_after_adj;

            var totaldifffrc = aprdifffrc + maydifffrc + jundifffrc + juldifffrc + augdifffrc + sepdifffrc + octdifffrc + novdifffrc + decdifffrc + jandifffrc + febdifffrc + mardifffrc;

            table += '<tr>';
            table += '<td style="text-align:left;border-left:1px solid yellow">Difffrc</td>';
            table += '<td style="border-left:1px solid yellow">'+aprdifffrc.toFixed(2)+'</td>';
            table += '<td style="border-left:1px solid yellow">'+maydifffrc.toFixed(2)+'</td>';
            table += '<td style="border-left:1px solid yellow">'+jundifffrc.toFixed(2)+'</td>';
            table += '<td style="border-left:1px solid yellow">'+juldifffrc.toFixed(2)+'</td>';
            table += '<td style="border-left:1px solid yellow">'+augdifffrc.toFixed(2)+'</td>';
            table += '<td style="border-left:1px solid yellow">'+sepdifffrc.toFixed(2)+'</td>';
            table += '<td style="border-left:1px solid yellow">'+octdifffrc.toFixed(2)+'</td>';
            table += '<td style="border-left:1px solid yellow">'+novdifffrc.toFixed(2)+'</td>';
            table += '<td style="border-left:1px solid yellow">'+decdifffrc.toFixed(2)+'</td>';
            table += '<td style="border-left:1px solid yellow">'+jandifffrc.toFixed(2)+'</td>';
            table += '<td style="border-left:1px solid yellow">'+febdifffrc.toFixed(2)+'</td>';
            table += '<td style="border-left:1px solid yellow">'+mardifffrc.toFixed(2)+'</td>';
            table += '<td style="border-left:1px solid yellow;background-color:green">'+totaldifffrc.toFixed(2)+'</td>';
            table += '</tr>';

        })

        $('#tabelisi').append(table);

        // $('#tabelmonitor').DataTable({
        //   'responsive':true,
        //   'paging': true,
        //   'lengthChange': true,
        //   'pageLength': 10,
        //   'searching': true,
        //   'ordering': true,
        //   'order': [],
        //   'info': false,
        //   'autoWidth': true,
        //   "sPaginationType": "full_numbers",
        //   "bJQueryUI": true,
        //   "bAutoWidth": false,
        //   "processing": true
        // });
      }
    })
  }

  function detailBudget(budget,status){

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
          "url" : "{{ url("fetch/budget/detail_table") }}",
          "data" : {
            budget : budget,
            status : status
          }
        },
      "columns": [
          { "data": "budget" },
          { "data": "budget_month" },
          { "data": "category_number" },
          { "data": "no_item" },
          { "data": "amount" },
          { "data": "status" },
        ]    });

    $('#judul_table').append().empty();
    $('#judul_table').append('<center><b>'+status+' Budget '+budget+'</center></b>');
    
  }

  function detail_budget(budget,status){
    $("#myModal").modal("show");


    var data = {
        budget:budget,
        status:status,
    }

    $("#loading").show();
    $.get('{{ url("fetch/budget/detail_table") }}', data, function(result, status, xhr) {


      $("#loading").hide();
      if(result.status){
        $('#tableResult').DataTable().clear();
        $('#tableResult').DataTable().destroy();
        $('#tableBodyResult').html("");

        var tableData = "";
        var total = 0;
        var count = 1;
        
        $.each(result.datas, function(key, value) {
          tableData += '<tr>';
          tableData += '<td>'+ value.budget +'</td>';

          if (value.status == "PR" || value.status == "Investment") {
            tableData += '<td>'+ value.budget_month +'</td>';
            tableData += '<td>'+ value.category_number +'</td>';
            tableData += '<td>'+ value.no_item +'</td>';
            tableData += '<td>'+ value.status+ '</td>';
            tableData += '<td>$ '+ value.amount +'</td>'; 
            total += parseFloat(value.amount);           
          }

          else if(value.status == "PO"){
            tableData += '<td>'+ value.budget_month_po +'</td>';
            tableData += '<td> Nomor PR/Inv : '+ value.category_number+ ' <br> Nomor PO : '+ value.po_number +'</td>';
            tableData += '<td>'+ value.no_item +'</td>';
            tableData += '<td>'+ value.status+ '</td>';
            tableData += '<td>$ '+ value.amount_po +'</td>';
            total += parseFloat(value.amount_po);
          }

          else if(value.status == "Actual"){
            tableData += '<td>'+ value.budget_month_receive +'</td>';
            tableData += '<td> Nomor PR/Inv : '+ value.category_number+ ' <br> Nomor PO : '+ value.po_number +'</td>';
            tableData += '<td>'+ value.no_item +'</td>';
            tableData += '<td>'+ value.status+ '</td>';
            tableData += '<td>$ '+ value.amount_receive +'</td>';
            total += parseFloat(value.amount_receive);
          }

          tableData += '</tr>';
          count += 1;
        });

        $('#tableBodyResult').append(tableData);
        $('#resultTotal').html('');
        $('#resultTotal').append('$ '+total.toFixed(2));

      }
      else{
        alert('Attempt to retrieve data failed');
      }

    });

    $('#judul_table').append().empty();
    $('#judul_table').append('<center><b>'+status+' Budget '+budget+'</center></b>');
    
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