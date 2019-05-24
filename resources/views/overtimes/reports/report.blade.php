@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
 thead input {
  width: 100%;
  padding: 3px;
  box-sizing: border-box;
}
thead>tr>th{
  text-align:center;
}
tbody>tr>td{
  text-align:center;
}
tfoot>tr>th{
  text-align:center;
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
  border:1px solid rgb(211,211,211);
  padding-top: 0;
  padding-bottom: 0;
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
    Overtime Report <span class="text-purple"> jepang </span>
  </h1>
  <div class="col-md-2 pull-right">
    <div class="input-group date">
      <div class="input-group-addon bg-green" style="border-color: green">
        <i class="fa fa-calendar"></i>
      </div>
      <input type="text" class="form-control datepicker" id="date2" onchange="drawChart()" placeholder="Select date" style="border-color: green">
    </div>
  </div>
  <small style="font-size: 15px; color: #88898c">&nbsp; </small>
  <ol class="breadcrumb">
  </ol>
</section>
@endsection


@section('content')

<section class="content container-fluid">
 <div class="row">
  <div class="col-md-12">
   <!-- Custom Tabs -->
   <div class="box">
    <div class="box-body">
     <div class="col-md-12">
      <div class="row">
       <div class="col-md-12">
        <div id="gender_chart" style="width: 100%; height: 550px;"></div>
        <br>
        <br>
        <div id="over" style="width: 100%; margin: 0px auto; height: 550px;"></div>
        <br>
        <br>
        <div id="over" style="width: 100%; margin: 0px auto; height: 550px;"></div>
      </div>
    </div>
  </div>
  <br><br>
</div>
</div>
</div>
</div>

<div class="modal fade" id="myModal2">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><b id="head">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <table class="table table-bordered table-striped table-hover" style="width: 100%;"> 
              <thead style="background-color: rgba(126,86,134,.7);">
                <tr>
                  <th>NIK</th>
                  <th>Nama karyawan</th>
                  <th>Departemen</th>
                  <th>Section</th>
                  <th>Kode</th>
                  <th>Avg (jam)</th>
                </tr>
              </thead>
              <tbody id="details">
              </tbody>
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

</section>


@stop

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
  $(function () {
    drawChartGender();
    drawChart();
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

  function drawChartGender(){
    $.get('{{ url("fetch/report/gender") }}', function(result, status, xhr){
      if(xhr.status == 200){

        if(result.status){
          var xCategories = [];
          var seriesLaki = [];
          var seriesPerempuan = [];
          var cat;

          for(var i = 0; i < result.manpower_by_gender.length; i++){
            cat = result.manpower_by_gender[i].mon;

            if(result.manpower_by_gender[i].jk == 'L')
              seriesLaki.push(result.manpower_by_gender[i].tot_karyawan);
            else
              seriesPerempuan.push(result.manpower_by_gender[i].tot_karyawan);

            if(xCategories.indexOf(cat) === -1){
              xCategories[xCategories.length] = cat;
            }
          }

          console.log(seriesLaki);


          Highcharts.chart('gender_chart', {
            chart: {
              type: 'column'
            },
            title: {
              text: 'Total Manpower by Gender <br> Fiscal 196'
            },
            xAxis: {
              categories: xCategories
            },
            yAxis: {
              min: 0,
              title: {
                text: 'Total Manpower'
              }
            },
            tooltip: {
              useHTML: true
            },
            credits: {
              enabled: false
            },
            plotOptions: {
              column: {
                dataLabels: {
                  enabled: true,
                  crop: false,
                  overflow: 'none'
                }
              }
            },
            series: [{
              name: 'Laki - laki',
              data: seriesLaki

            }, {
              name: 'Perempuan',
              data: seriesPerempuan

            }]
          });

        }
        else{
          alert('Attempt to retrieve data failed');
        }
      }
    })
  }

  function drawChart() {
    var tanggal = $('#date2').val();
    var cat = new Array();
    var tiga_jam = new Array();
    var per_minggu = new Array();
    var per_bulan = new Array();
    var manam_bulan = new Array();

    var data = {
     tanggal:tanggal
   }

   $.get('{{ url("fetch/overtime_report") }}', data, function(result) {

     for (i = 0; i < result.report.length; i++){
       cat.push(result.report[i].code);
       tiga_jam.push(parseInt(result.report[i].tiga_jam));
       per_minggu.push(parseInt(result.report[i].emptblas_jam));
       per_bulan.push(parseInt(result.report[i].tiga_patblas_jam));
       manam_bulan.push(parseInt(result.report[i].limanam_jam));
     }

     tgl = result.report[0].month_name;

     var date = new Date(tgl+'-01');

     var month = new Array();
     month[0] = "January";
     month[1] = "February";
     month[2] = "March";
     month[3] = "April";
     month[4] = "May";
     month[5] = "June";
     month[6] = "July";
     month[7] = "August";
     month[8] = "September";
     month[9] = "October";
     month[10] = "November";
     month[11] = "December";

     title = month[date.getMonth()]+" "+date.getFullYear();

     console.log(title);

     $('#over').highcharts({
       chart: {
         type: 'line'
       },
       legend: {
         enabled: true,
      },
      exporting : {
       enabled : true,
       buttons: {
         contextButton: {
           align: 'right',
           x: -25
         }
       }
     },
     title: {
      text: 'Overtime <br><span style="font-size:12pt">'+title+'</span>',
    },
    xAxis: {
      categories: cat,
      labels: {
        rotation: -60,
      }
    },
    yAxis: {
      min:0,
      title: {
        text: 'Number of Employee',
      }
    },
    plotOptions: {
      line: {
        dataLabels: {
          enabled: true,
        },
        enableMouseTracking: true
      },
      series: {
       cursor: 'pointer',
       point: {
         events: {
           click: function(e) {  
             show2(tgl, this.category, this.series.name);
           }
         }
       }
     }
   },
   credits: {
    enabled: false
  },
  series: [{
    name: '3 hour(s) / day',
    color: '#2598db',
    shadow: {
      color: '#2598db',
      width: 7,
      offsetX: 0,
      offsetY: 0
    },
    data: tiga_jam
  }, {
    name: '14 hour(s) / week',
    color: '#f78a1d',
    shadow: {
      color: '#f78a1d',
      width: 7,
      offsetX: 0,
      offsetY: 0
    },
    data: per_minggu
  },
  {
    name: '3 & 14 hour(s) / week',
    color: '#f90031',
    shadow: {
      color: '#f90031',
      width: 7,
      offsetX: 0,
      offsetY: 0
    },
    data: per_bulan
  },
  {
    name: '56 hour(s) / month',
    color: '#d756f7',
    shadow: {
      color: '#d756f7',
      width: 7,
      offsetX: 0,
      offsetY: 0
    },
    data: manam_bulan
  }]

});
   });
 }

 function show2(tgl, code, ctg) {
  tabel = $('#example3').DataTable();
  tabel.destroy();

  $('#myModal2').modal('show');

  var data = {
    tanggal : tgl,
    code : code,
    category: ctg
  }

  $.get('{{ url("fetch/overtime_report_detail") }}', data, function(result){
    $("#details").empty();
    $("#head").html('Overtime of More than '+result.head);
    $.each(result.datas, function(key, value) {
     $("#details").append(
      "<tr><td>"+value.nik+"</td><td>"+value.name+"</td><td>"+value.department+"</td><td>"+value.section+"</td><td>"+value.code+"</td><td>"+value.avg+"</td></tr>"
      );
   })
  })
}

$('.datepicker').datepicker({
  autoclose: true,
  format: "mm-yyyy",
  viewMode: "months", 
  minViewMode: "months"
});

</script>

@stop
