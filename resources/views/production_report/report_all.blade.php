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
    Production Report <span class="text-purple">{{ $departments }}</span><br>
    <small>Berdasarkan Jenis Aktivitas<span class="text-purple"> </span></small>
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
      {{-- <div class="col-md-12">
        <div class="col-md-3 pull-right">
          <select class="form-control select2" name="activity_type" style="width: 100%;" data-placeholder="Choose an Activity Type..." required id='activity_type' onchange="drawChart()">
              <option value=""></option>
              <option value="Semua">Semua</option>
              @foreach($data as $data)
                <option value="{{ $data->activity_type }}">{{ $data->activity_type }}</option>
              @endforeach
          </select>
          <br>
          <br>
        </div>
      </div> --}}
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
          <a id="link_details" class="btn btn-primary btn-xs pull-right" href="">Activity Chart</a>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="example2" class="table table-striped table-bordered" style="width: 100%;"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>Activity Name</th>
                    <th>Activity Alias</th>
                    <th>Frequency</th>
                    <th>Department</th>
                    <th>Activity Type</th>
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

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

  function drawChart() {
    var activity_type = $('#activity_type').val();
    var url = '{{ url("index/production_report/report_by_act_type/".$id) }}'
    var data = {
      activity_type: activity_type
    };
    $.get('{{ url("index/production_report/fetchReport/".$id) }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){

          // var xAxis = [], productionCount = [], inTransitCount = [], fstkCount = []
          // for (i = 0; i < data.length; i++) {
          //   xAxis.push(data[i].destination);
          //   productionCount.push(data[i].production);
          //   inTransitCount.push(data[i].intransit);
          //   fstkCount.push(data[i].fstk);
          // }
          
          var departemen = [], jml = [];

          $.each(result.datas, function(key, value) {
            departemen.push(value.activity_type);
            jml.push(value.jumlah_activity);
            // statusopen.push(value.open);
            // statusclose.push(value.close);
          })

          $('#chart').highcharts({
            chart: {
              type: 'column'
            },
            title: {
              text: 'Report of {{ $departments }}'
            },
            xAxis: {
              type: 'category',
              categories: departemen
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
              enabled: false
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                point: {
                  events: {
                    click: function () {
                      ShowModal(this.category);
                      jQuery('#link_details').attr("href", url+'/'+this.category);
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
                return this.series.name+' '+this.key + ' : ' + '<b>'+this.y+'</b>';
              }
            },
            "series": [
            {
              "name": 'Jumlah',
              "colorByPoint": true,
              "data": jml
            }
            ]
          })
        } else{
          alert('Attempt to retrieve data failed');
        }
      }
    })
  }

  function ShowModal(activity_type) {
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
        "url" : "{{ url("fetch/production_report/detail_stat/".$id) }}",
        "data" : {
          activity_type : activity_type
        }
      },
      "columns": [
      { "data": "activity_name" },
      { "data": "activity_alias" },
      { "data": "frequency" },
      { "data": "department_name" },
      { "data": "activity_type" },
      { "data": "id_activity",
      	"render": function ( data ) {
      		return '<a class="btn btn-info btn-xs" href="../../../index/production_report/activity/' + data + '">Details</a>';
    	  } 
      }
      ]
    });
    $('#judul_table').append().empty();
    $('#judul_table').append('<center> Report of '+ activity_type +'<center>');
    
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