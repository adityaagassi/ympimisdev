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
  overflow:hidden;
  padding: 3px;
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
td{
    overflow:hidden;
    text-overflow: ellipsis;
  }
#loading, #error { display: none; }
</style>
@endsection
@section('header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content-header">
  <h1>
    List of {{ $page }}
    <small>Form Permasalahan & Kegagalan</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{ url("index/form_experience/create")}}" class="btn btn-success btn-sm" style="color:white"><i class="fa fa-plus"></i>Buat {{ $page }}</a></li>
  </ol>
</section>
@endsection


@section('content')
<section class="content">
  @if (session('status'))
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
    {{ session('status') }}
  </div>   
  @endif

  @if (session('error'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Error!</h4>
    {{ session('error') }}
  </div>   
  @endif
  <div class="row">

    <div class="col-xs-12">
        <div id="container"></div>
    </div>

    <div class="col-xs-12">
      <div class="box">
        <!-- <div class="box-header">
          <h3 class="box-title">Filter <span class="text-purple">Form Kegagalan & Permasalahan</span></h3>
        </div>
        <div class="box-body">
          <input type="hidden" value="{{csrf_token()}}" name="_token" />
          <div class="col-md-12">
            <div class="col-md-4">
              <div class="form-group">
                <select class="form-control select2" data-placeholder="Pilih Departemen" name="department_id" id="department_id" style="width: 100%;">
                  <option></option>
                  @foreach($departments as $department)
                  <option value="{{ $department->id }}">{{ $department->department_name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          <div class="col-md-12 col-md-offset-5">
            <div class="form-group">
              <a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
              <button id="search" onClick="fillForm()" class="btn btn-primary">Search</button>
            </div>
          </div> -->


          <div class="box-body" style="overflow-x: scroll;">
          <table id="example1" class="table table-bordered table-striped table-hover" >
            <thead style="background-color: rgba(126,86,134,.7);">
              <tr>
                <!-- <th>Nama</th> -->
                <th>Tanggal Kejadian</th>
                <th>Lokasi Kejadian</th>
                <th>Judul</th>
                <th>Mesin / Equipment</th>
                <th>Grup</th>
                <th>Kategori</th>
                <th>Loss</th>
                <th>Estimasi Kerugian</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
              <tr>
                <!-- <th></th> -->
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
      </div>
    </div>
  </div>
</section>

<div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
      </div>
      <div class="modal-body">
        Are you sure want to delete this Data?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highstock.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
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
    fillForm();
    fillChart();
    $("#navbar-collapse").text('');
      $('.select2').select2({
        language : {
          noResults : function(params) {
            // return "There is no cpar with status 'close'";
          }
        }
      });
    });


  function clearConfirmation(){
    location.reload(true);
  }

  
  function fillForm(){
    $('#example1').DataTable().destroy();
    var department_id = $('#department_id').val();
    var data = {
      department_id:department_id,
    }
    $('#example1 tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
    } );
    var table = $('#example1').DataTable({
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
        // "serverSide": true,
        "ajax": {
          "type" : "post",
          "url" : "{{ url("index/form_experience/filter") }}",
          "data" : data,
        },
        "columns": [
          // { "data": "employee_name", "width": "10%"},
          { "data": "tanggal_kejadian", "width": "10%"},
          { "data": "lokasi_kejadian", "width": "10%"},
          { "data": "judul", "width": "15%"},
          { "data": "equipment", "width": "10%"},
          { "data": "grup_kejadian", "width": "10%"},
          { "data": "kategori", "width": "10%"},
          { "data": "loss", "width": "10%"},
          { "data": "kerugian", "width": "15%"},
          { "data": "action", "width": "10%"}
        ]
      });
  

    table.columns().every( function () {
        var that = this;

        $( 'input', this.footer() ).on( 'keyup change', function () {
          if ( that.search() !== this.value ) {
            that
            .search( this.value )
            .draw();
          }
        } );
      } );

      $('#example1 tfoot tr').appendTo('#example1 thead');
  }


  function fillChart() {

    $.get('{{ url("fetch/form_experience/chart") }}', function(result, status, xhr){
      if(result.status){

        var department = [];
        var total = [];

        for (var i = 0; i < result.detail.length; i++) {
          department.push(result.detail[i].department);
          total.push(parseInt(result.detail[i].total));
        }

        Highcharts.chart('container', {
          chart: {
            height: 300,
            type: 'column'
          },
          title: {
            text: 'Form Permasalahan & Kegagalan By Department'
          },  
          legend:{
            enabled: false
          },
          credits:{ 
            enabled:false
          },
          xAxis: {
            categories: department,
            type: 'category'
          },
          yAxis: {
            title: {
              enabled:false,
            },
            labels: {
              enabled:false
            }
          },
          tooltip: {
            formatter: function () {
              return '<b>' + this.x + '</b><br/>' +
              'Total: ' + this.y;
            }
          },
          plotOptions: {
            column: {
              stacking: 'percent',
            },
            series:{
              animation: false,
              pointPadding: 0.93,
              groupPadding: 0.93,
              borderWidth: 0.93,
              cursor: 'pointer',
              stacking: 'percent',
              dataLabels: {
                enabled: true,
                formatter: function() {
                  return this.y;
                },
                style: {
                  fontWeight: 'bold',
                }
              },
              point: {
                events: {
                  click: function () {
                    fillInputModal(this.category, this.series.name);
                  }
                }
              }
            }
          },
          series: [
          {
            name: 'Fill',
            data: total,
            color: '#00a65a'
          }]
        });
      }
    });
  }


  function deleteConfirmation(id) {
    jQuery('#modalDeleteButton').attr("href", '{{ url("index/qc_report/delete") }}'+'/'+id);
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
          [0, '#2a2a2b'],
          [1, '#3e3e40']
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
        itemStyle: {
          color: '#E0E0E3'
        },
        itemHoverStyle: {
          color: '#FFF'
        },
        itemHiddenStyle: {
          color: '#606063'
        }
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
  

</script>

@stop