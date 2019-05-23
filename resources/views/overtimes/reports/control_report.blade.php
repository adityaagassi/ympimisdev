@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
@endsection
@section('header')
<section class="content-header">
 <h1>
  Overtime Control <span class="text-purple"> jepang </span>
</h1>
<div class="col-md-2 pull-right">
  <div class="input-group date">
    <div class="input-group-addon bg-green" style="border-color: green">
      <i class="fa fa-calendar"></i>
    </div>
    <input type="text" class="form-control datepicker" id="tgl" onchange="drawChart()" placeholder="Select date" style="border-color: green">
  </div>
</div>
<small style="font-size: 15px; color: #88898c"><i class="fa fa-history"></i> Last updated : <?php echo date('d M Y') ?> </small>
<ol class="breadcrumb">
</ol>
</section>
@endsection


@section('content')

<section class="content">
 <div class="row">
  <div class="col-xs-12">
   <div class="box">
    <div class="box-body">
      <div class="box-body">
        <div class="col-md-12">
          <div class="row">
            <div class="col-md-12">
              <div id="container" style="width: 100%; margin: 0px auto; height: 550px;"></div>
            </div>
          </div>
        </div>
        <br><br>
      </div>
    </div>

    <div class="box box-solid">
      <div class="box-body">
        <div class="col-md-12">
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-3">
                <div class="description-block border-right" style="color: #f76111">
                  <h5 class="description-header" style="font-size: 60px;">
                    <span class="description-percentage" id="tot_budget"></span>
                  </h5>      
                  <span class="description-text" style="font-size: 35px;">Total Budget<br><span >総予算</span></span>   
                </div>
              </div>
              <div class="col-md-3">
                <div class="description-block border-right" style="color: #7300ab" >
                  <h5 class="description-header" style="font-size: 60px; ">
                    <span class="description-percentage" id="tot_act"></span>
                  </h5>      
                  <span class="description-text" style="font-size: 35px;">Total Actual<br><span >総実績</span></span>   
                </div>
              </div>
              <div class="col-md-3">
                <div class="description-block border-right text-green" id="diff_text">
                  <h5 class="description-header" style="font-size: 60px;">
                    <span class="description-percentage" id="tot_diff"></span>
                  </h5>      
                  <span class="description-text" style="font-size: 35px;">Difference<br><span >差異</span></span>   
                </div>
              </div>
              <div class="col-md-3">
                <div class="description-block border-right text-yellow">
                  <h5 class="description-header" style="font-size: 60px;">
                    <span class="description-percentage" id="avg"></span>
                  </h5>      
                  <span class="description-text" style="font-size: 35px;">Average<br><span >平均</span></span>   
                </div>
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
           <thead>
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
             <th>
              <td colspan="2" style="font-weight: bold; size: 25px; text-align: right;">TOTAL </td>
              <td id="tot" style="font-weight: bold; size: 25px"></td>
            </th>
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


@stop

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>

 var tot_budget = 0;
 var tot_act = 0;
 var tot_diff = 0;
 $(function () {
  drawChart();

  // setInterval(function(){
  //   drawChart();
  // }, 30000);
})


 $.ajaxSetup({
  headers: {
   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
 }
});

 jQuery(document).ready(function() {
  $('body').toggleClass("sidebar-collapse");

});

 function openErrorGritter(title, message) {
  jQuery.gritter.add({
   title: title,
   text: message,
   class_name: 'growl-danger',
   image: '{{ url("images/image-stop.png") }}',
   sticky: false,
   time: '2000'
 });
}

function openSuccessGritter(title, message){
  jQuery.gritter.add({
   title: title,
   text: message,
   class_name: 'growl-success',
   image: '{{ url("images/image-screen.png") }}',
   sticky: false,
   time: '2000'
 });
}
$('.datepicker').datepicker({
  <?php $tgl_max = date('d-m-Y') ?>
  autoclose: true,
  format: "dd-mm-yyyy",
  endDate: '<?php echo $tgl_max ?>',

});


function drawChart(){
 var tgl = $('#tgl').val();

 var data = {
   tgl:tgl
 }
 $.get('{{ url("fetch/report/overtime_control") }}', data, function(result, status, xhr){
   if(xhr.status == 200){

    if(result.status){
      tot_budget = 0;
      tot_act = 0;
      tot_diff = 0;
      avg = 0;
      total_karyawan = 0;
      var xCategories = [];
      var seriesDataBudget = [];
      var seriesDataAktual = [];
      var budgetHarian = [];
      var cat;

      for(var i = 0; i < result.overtimes.length; i++){
        cat = result.overtimes[i].NAME;
        tot_budget += result.overtimes[i].tot;
        tot_act += result.overtimes[i].act;
        seriesDataBudget.push(Math.round(result.overtimes[i].tot * 100) / 100);
        seriesDataAktual.push(result.overtimes[i].act);
        budgetHarian.push(Math.round(result.overtimes[i].jam_harian * 100) / 100);
        if(xCategories.indexOf(cat) === -1){
         xCategories[xCategories.length] = cat;
       }
     }

     for(var i = 0; i < result.total1.length; i++){
      total_karyawan += result.total1[i].tot_karyawan;
    }

    tot_diff = tot_budget - tot_act;

    tot_budget = Math.round(tot_budget * 100) / 100;
    tot_act = Math.round(tot_act * 100) / 100;
    tot_diff = Math.round(tot_diff * 100) / 100;

    var tot_budget2 = tot_budget.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
    var tot_act2 = tot_act.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");

    var tot_diff2 = tot_diff.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");

    $("#tot_budget").text(tot_budget2);
    $("#tot_act").text(tot_act2);

    if (tot_diff > 0) {
      $('#diff_text').removeClass('text-red').addClass('text-green');
      $("#tot_diff").html(tot_diff2);
    }
    else {
      $('#diff_text').removeClass('text-green').addClass('text-red');
      $("#tot_diff").html(tot_diff2);
    }
    avg = tot_act / total_karyawan;
    avg = Math.round(avg * 100) / 100;
    $("#avg").html(avg);

    var interval = Math.ceil(300/10);

    Highcharts.SVGRenderer.prototype.symbols['c-rect'] = function (x, y, w, h) {
      return ['M', x, y + h / 2, 'L', x + w, y + h / 2];
    };

    Highcharts.chart('container', {
      chart: {
        spacingTop: 10,
        type: 'column'
      },
      title: {
        text: '<span style="font-size: 30pt;">Overtime</span><br><center><span style="color: rgba(96, 92, 168);">'+ result.overtimes[0].tanggal +'</center></span>',
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
        categories: xCategories
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
                modalTampil(this.category, result.overtimes[0].tanggal);
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
        name: 'Day Budget',
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

  }
  else{
   alert('Attempt to retrieve data failed');
 }
}

else{
  alert('Disconnected from server');
}

});
}
</script>

@stop