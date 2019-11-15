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
    Laporan Aktivitas Audit <span class="text-purple">{{ $departments }}</span><br>
    <small>Berdasarkan Tanggal<span class="text-purple"> </span></small>
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
            <input type="text" class="form-control datepicker" id="week_date" onchange="drawChart()" placeholder="Select Date" style="border-color: #00a65a">
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
          {{-- <a id="link_details" class="btn btn-primary btn-xs pull-right" href="">Activity Chart</a> --}}
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="example2" class="table table-striped table-bordered" style="width: 100%;"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>Activity Name</th>
                    <th>Section</th>
                    <th>Sub Section</th>
                    <th>Date</th>
                    <th>Nama Dokumen</th>
                    <th>Nomor Dokumen</th>
                    <th>Kesesuaian QC Kouteihyo</th>
                    <th>Details</th>
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

  $(function () {
      $('.select2').select2()
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
    var week_date = $('#week_date').val();
    var url = '{{ url("index/production_report/report_by_act_type/".$id) }}'
    var data = {
      week_date: week_date
    };
    $.get('{{ url("index/audit_report_activity/fetchReport/".$id) }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){

          var month = result.monthTitle;
          
          var date = [], jumlah_laporan = [];

          $.each(result.datas, function(key, value) {
            date.push(value.date);
            jumlah_laporan.push(value.jumlah_laporan);
          })

          $('#chart').highcharts({
            title: {
              text: 'Laporan Aktivitas Audit of '+month
            },
            xAxis: {
              type: 'category',
              categories: date
            },
            yAxis: [{
              title: {
                text: 'Total Laporan Aktivitas Audit'
              },
              stackLabels: {
                  enabled: true,
                  style: {
                      fontWeight: 'bold',
                      color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                  }
              }
            },
            { // Secondary yAxis
                title: {
                    text: 'Report',
                    style: {
                        color: Highcharts.getOptions().colors[0]
                    }
                },
                labels: {
                    format: '{value}',
                    style: {
                        color: Highcharts.getOptions().colors[0]
                    }
                },
                opposite: true
              }
            ],
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
                      ShowModal(this.category);
                    }
                  }
                },
                borderWidth: 0,
                dataLabels: {
                  enabled: true,
                  format: '{point.y}'
                }
              }
            },
            credits: {
              enabled: false
            },

            tooltip: {
              formatter:function(){
                return this.series.name+' Laporan Aktivitas Audit <br> Tanggal '+this.key + ' : ' + '<br><b>'+this.y+'</b>';
              }
            },
            series: [
            {
              type: 'column',
              name: 'Jumlah',
              color : '#a9ff97',
              data: jumlah_laporan
            },
            {
              type: 'spline',
              name: 'Jumlah',
              color : '#69d453',
              data: jumlah_laporan
            }
            ]
          })
        } else{
          alert('Attempt to retrieve data failed');
        }
      }
    })
  }

  function ShowModal(week_date) {
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
        "url" : "{{ url("fetch/audit_report_activity/detail_laporan_aktivitas/".$id) }}",
        "data" : {
          week_date : week_date
        }
      },
      "columns": [
      { "data": "activity_name" },
      { "data": "section" },
      { "data": "subsection" },
      { "data": "date" },
      { "data": "nama_dokumen" },
      { "data": "no_dokumen" },
      { "data": "kesesuaian_qc_kouteihyo" },
      { "data": "linkurl",
        "render": function ( data ) {
            return '<a target="_blank" class="btn btn-info btn-xs" href="../../../index/audit_report_activity/print_audit_report_chart/' + data + '">Details</a>';
          } 
      }
      ]
    });
    $('#judul_table').append().empty();
    $('#judul_table').append('<center> Laporan Aktivitas Audit of '+ week_date +'<center>');
    
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