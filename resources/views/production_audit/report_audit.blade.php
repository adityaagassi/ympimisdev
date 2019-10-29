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
    Production Audit Report <span class="text-purple">{{ $departments }}</span><br>
    <small>Berdasarkan Kondisi<span class="text-purple"> </span></small>
  </h1>
  <ol class="breadcrumb" id="last_update">
  </ol>
</section>
@endsection


@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="col-md-12">
        <div class="col-md-2 pull-right">
          <div class="input-group date">
            <div class="input-group-addon bg-green" style="border-color: #00a65a">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" class="form-control datepicker" id="tgl" onchange="drawChart()" placeholder="Select Date" style="border-color: #00a65a">
          </div>
          <br>
        </div>
      </div>
      <div class="col-md-12">
        <div class="box">
          <div class="nav-tabs-custom">
            <div class="tab-content">
              <div class="tab-pane active" id="tab_1">
                <div id="chart" style="width: 99%;"></div>
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
          <h4 style="float: right;" id="modal-title"></h4>
          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
          <br><h4 class="modal-title" id="judul_table"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="example2" class="table table-striped table-bordered" style="width: 100%;"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>Activity Name</th>
                    <th>Audit Date</th>
                    <th>Product</th>
                    <th>Process</th>
                    <th>Point Check Details</th>
                    <th>Kondisi</th>
                    <th>PIC</th>
                    <th>Auditor</th>
                    <th>Foreman</th>
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
    $('body').toggleClass("sidebar-collapse");
    $('#myModal').on('hidden.bs.modal', function () {
      $('#example2').DataTable().clear();
    });

    drawChart();
  });

  $('.datepicker').datepicker({
    // <?php $tgl_max = date('m-Y') ?>
    autoclose: true,
    format: "yyyy-mm",
    startView: "months", 
    minViewMode: "months",
    autoclose: true,
    
    // endDate: '<?php echo $tgl_max ?>'

  });

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

  function drawChart() {
    var tgl = $('#tgl').val();
    // var url = '{{ url("index/production_report/report_by_act_type/".$id) }}'
    var data = {
      tgl: tgl
    };
    $.get('{{ url("index/production_audit/fetchReport/".$id) }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){

          // var xAxis = [], productionCount = [], inTransitCount = [], fstkCount = []
          // for (i = 0; i < data.length; i++) {
          //   xAxis.push(data[i].destination);
          //   productionCount.push(data[i].production);
          //   inTransitCount.push(data[i].intransit);
          //   fstkCount.push(data[i].fstk);
          // }
          var month = result.monthTitle;
          
          var activity_name = [], jml = [], jumlahgood = [], jumlahnotgood = [],activity_list_id = [];
          $.each(result.datas, function(key, value) {
            activity_name.push(value.activity_name);
            jml.push(value.jumlah_semua);
            jumlahgood.push(parseInt(value.jumlah_good));
            jumlahnotgood.push(parseInt(value.jumlah_not_good));
            activity_list_id.push(value.activity_list_id);
          })
          $('#chart').highcharts({
            chart: {
              type: 'column'
            },
            title: {
              text: 'Report Audit of'+month
            },
            xAxis: {
              type: 'category',
              categories: activity_name,
            },
            yAxis: {
              type: 'linear',
              title: {
                text: 'Total Report'
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
              align: 'right',
              x: -30,
              verticalAlign: 'top',
              y: 25,
              floating: true,
              backgroundColor:
                  Highcharts.defaultOptions.legend.backgroundColor || 'white',
              borderColor: '#CCC',
              borderWidth: 1,
              shadow: false
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                point: {
                  events: {
                    click: function () {
                      ShowModal(this.category,this.series.name);
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
                  stacking: 'normal',
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
                name: 'Good',
                color: '#00CC00',
                data: jumlahgood
            }, {
                name: 'Not Good',
                data: jumlahnotgood,
                color : '#006600'
            }
            ]
          })
        } else{
          alert('Attempt to retrieve data failed');
        }
      }
    })
  }

  function ShowModal(activity_name,kondisi,activity_list_id) {
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
        "url" : "{{ url("fetch/production_audit/detail_stat/".$id) }}",
        "data" : {
          activity_name : activity_name,
          kondisi : kondisi,
        }
      },
      "columns": [
      { "data": "activity_name" },
      { "data": "date" },
      { "data": "product" },
      { "data": "proses" },
      { "data": "point_check_audit_id",
        "render": function ( data ) {
          return '<a class="btn btn-info btn-xs" href="../../../index/point_check_audit/show2/'+ data + '">Details</a>';
        } 
      },
      { "data": "kondisi" },
      { "data": "pic_name" },
      { "data": "auditor_name" },
      { "data": "foreman" },
      ]
    });
    $('#judul_table').append().empty();
    $('#judul_table').append('<center> Report of '+ activity_name+ ' in ' + kondisi +' Condition<center>');
    
  }

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