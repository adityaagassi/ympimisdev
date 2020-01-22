@extends('layouts.display')
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
<!--           <div class="box-header with-border">
            <h3 class="box-title"><b style="font-size: 18pt">Monitoring <span class="text-purple">Eksternal Komplain</span></b></h3>
          </div> -->
          <div class="box-body" style="background-color: transparent;">
            <div id="container"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
<div class="modal fade" id="modal">
    <div class="modal-dialog" style="width:1200px;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="float: right;" id="modal-title"></h4>
          <!-- <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4> -->
          <br><h4 class="modal-title" id="judul_table"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
                <div class="col-md-4">Tes</div>
                <div class="col-md-4">Tes</div>
                <div class="col-md-4">Tes</div>

                 <div class="col-sm-12">
                    <a href="{{ url('index/qc_report/print_cpar/1') }}" target="_blank" class="btn btn-success btn-sm" style="width: 100%; font-weight: bold; font-size: 16px">Preview Report CPAR</a>
                  </div>                
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
  <!-- <script src="{{ url("js/highcharts.js")}}"></script> -->
  <!-- <script src="{{ url("js/highcharts-3d.js")}}"></script> -->
  <script src="{{ url("js/highcharts-gantt.js")}}"></script>
  <script src="{{ url("js/exporting.js")}}"></script>
  <script src="{{ url("js/export-data.js")}}"></script>

  <script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
  <script src="{{ url("js/buttons.print.min.js")}}"></script>
  <script>
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    jQuery(document).ready(function() {
      drawgantt();
    });

    function drawgantt() {
      var tgl = $('#tgl').val();

      var data = {
        tgl: tgl
      };

      $.get('{{ url("index/qc_report/fetchGantt") }}', data, function(result, status, xhr) {
        if(xhr.status == 200){
          if(result.status){

            var data = [];

            // for(var i = 0; i < result.eksternals.length; i++){
            // }

            // console.log(data);
            // console.log(result.rangefys[0].bulan_awal);

            $.each(result.eksternals, function(key, value) {
                cpar_no = result.eksternals[key].cpar_no;              
                tahun_permintaan = result.eksternals[key].tahun_permintaan;
                bulan_permintaan = result.eksternals[key].bulan_permintaan-1;
                tanggal_permintaan = result.eksternals[key].tanggal_permintaan;
                tahun_balas = result.eksternals[key].tahun_balas;
                bulan_balas = result.eksternals[key].bulan_balas-1;
                tanggal_balas = result.eksternals[key].tanggal_balas;
                detail_problem = result.eksternals[key].detail_problem;
                progress = result.eksternals[key].progress/100;

                data.push({name:cpar_no,no:cpar_no,komplain:detail_problem,start: Date.UTC(tahun_permintaan, bulan_permintaan, tanggal_permintaan),end: Date.UTC(tahun_balas, bulan_balas, tanggal_balas),y: key,completed:{amount:progress,fill:'#fa0'} });

                // console.log(data);
            })

            $.each(result.suppliers, function(key, value) {
                cpar_no = result.suppliers[key].cpar_no;              
                tahun_permintaan = result.suppliers[key].tahun_permintaan;
                bulan_permintaan = result.suppliers[key].bulan_permintaan-1;
                tanggal_permintaan = result.suppliers[key].tanggal_permintaan;
                tahun_balas = result.suppliers[key].tahun_balas;
                bulan_balas = result.suppliers[key].bulan_balas-1;
                tanggal_balas = result.suppliers[key].tanggal_balas;
                detail_problem = result.suppliers[key].detail_problem;
                progress = result.eksternals[key].progress/100;

                data.push({name:cpar_no,no:cpar_no,komplain:detail_problem,start: Date.UTC(tahun_permintaan, bulan_permintaan, tanggal_permintaan),end: Date.UTC(tahun_balas, bulan_balas, tanggal_balas),y: key+result.eksternals.length,completed:{amount:progress,fill:'#fa0'} });

                // console.log(data);
            })

            $.each(result.internals, function(key, value) {
                cpar_no = result.internals[key].cpar_no;              
                tahun_permintaan = result.internals[key].tahun_permintaan;
                bulan_permintaan = result.internals[key].bulan_permintaan-1;
                tanggal_permintaan = result.internals[key].tanggal_permintaan;
                tahun_balas = result.internals[key].tahun_balas;
                bulan_balas = result.internals[key].bulan_balas-1;
                tanggal_balas = result.internals[key].tanggal_balas;
                detail_problem = result.internals[key].detail_problem;
                progress = result.eksternals[key].progress/100;

                data.push({name:cpar_no,no:cpar_no,komplain:detail_problem,start: Date.UTC(tahun_permintaan, bulan_permintaan, tanggal_permintaan),end: Date.UTC(tahun_balas, bulan_balas, tanggal_balas),y: key+result.eksternals.length+result.suppliers.length,completed:{amount:progress,fill:'#fa0'} });
            })

            //plotband

            var fromeks,toeks,fromsupp,tosupp,fromint,toint;
            fromeks = -0.4;
            toeks = fromeks + result.eksternals.length;
            fromsupp = toeks;
            tosupp = toeks + result.suppliers.length;
            fromint = tosupp;
            toint = tosupp + result.internals.length



            // var day = 1000 * 60 * 60 * 24;
            // THE CHART
            Highcharts.ganttChart('container', {
              title: {
                text: 'Monitoring <span style="text-purple">Digital Komplain</span>'
              },
              chart:{
                backgroundColor:'#ffffff'
              },
              tooltip: {
                 formatter: function (tooltip) {
                      // if (this.point.isNull) {
                      //     return 'Null';
                      // }
                      // If not null, use the default formatter
                      return tooltip.defaultFormatter.call(this, tooltip);
                  }
              },
              xAxis: {
                // tickInterval: day * 7,
                currentDateIndicator: true,
                min: Date.UTC(2019, 9, 1),
                max: Date.UTC(2019, 11, 30)
              },
              credits:{
                enabled:false
              },
              plotOptions: {
                series: {
                  cursor: 'pointer',
                  point: {
                    events: {
                      click: function (e) {
                        ShowModal(e.point.no);
                      }
                    }
                  },
                }
              },
              yAxis: {
                type: 'category',
                plotBands: [{
                      from: -0.4,
                      to: toeks,
                      color: 'rgba(65, 252, 18, .2)'
                  },
                  { 
                      from: fromsupp,
                      to: tosupp,
                      color: 'rgba(68, 170, 213, .2)'
                  },
                  { 
                      from: fromint,
                      to: toint,
                      color: 'rgba(252, 3, 206, .2)'
                  }
                ],
                grid: {
                  enabled: true,
                  borderColor: 'rgba(0,0,0,0.3)',
                  borderWidth: 0,
                  columns: [{
                    title: {
                      text: 'CPAR'
                    },
                    labels: {
                      format: '{point.no}'
                    }
                  }, {
                    title: {
                      text: 'Komplain'
                    },
                    labels: {
                      format: '{point.komplain}'
                    }
                  }]
                }
              },
              series: [{
                name: 'CPAR CAR Monitoring',
                data: data,
                colorByPoint: false,
                color: '#ffcc80'
              }]
            });
          }
        }
      })
    }

    function ShowModal(cpar) {
      tabel = $('#example2').DataTable();
      tabel.destroy();

      $("#modal").modal("show");

      var data = {
        cpar:cpar
      };

      $.get('{{ url("index/qc_report/detail_monitoring") }}', data, function(result, status, xhr){
        $("#komplain").val(result.datas[0].kategori);
      });

      $('#judul_table').append().empty();
      $('#judul_table').append('<center><b>CPAR '+cpar+'</center></b>');
      
    }

  // function fetchTable(){
  //     var data = {
  //       tgl : $('#tgl').val()
  //     }
  //     $.get('{{ url("index/qc_report/fetchMonitoring") }}', data, function(result, status, xhr){
  //       if(xhr.status == 200){
  //         if(result.status){

  //           // $("#tabelmonitor").html("");
  //           $("#tabelisi").find("td").remove();  

  //           // foreach()

  //           $.each(result.datas, function(key, value) {
  //               if (value.cpar_no) {
  //                 $("#tabelisi").append("<tr><td>"+value.cpar_no+"</td><td>"+value.detail_problem+"</td></tr>");
  //               }
  //           })

  //         }
  //       }
  //     })
  // }


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