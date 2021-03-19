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
/*  padding-top: 0;
  padding-bottom: 0;*/
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

  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <p style="text-align: center; position: absolute; color: white; top: 45%; left: 40%;">
      <span style="font-size: 50px;">Please wait ... </span><br>
      <span style="font-size: 50px;"><i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>


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
                <th>Bulan</th>
                <th>Section</th>
                <th>Group</th>
                <th>Judul</th>
                <th>Grup Kejadian</th>
                <th>Mesin / Equipment</th>
                <th>Target Sosialiasi</th>
                <th>Jumlah Sosialiasi</th>
                <th>Sosialisasi</th>
                <th>File</th>
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


  <div class="modal fade" id="modalInput">
    <div class="modal-dialog modal-lg" style="width: 90%;">
      <div class="modal-content">
        <div class="modal-header">
          <div class="modal-body table-responsive no-padding" style="min-height: 100px">
            <table class="table table-hover table-bordered table-striped" id="tableInput">
              <thead style="background-color: rgba(126,86,134,.7);">
                <tr>
                  <th>Bulan</th>
                  <th>Lokasi Kejadian</th>
                  <th>Equipment</th>
                  <th>Grup Kejadian</th>
                  <th>Kategori</th>
                  <th>Judul</th>
                  <th>Sosialisasi</th>
                  <th>Jumlah Sosialisasi</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody id="bodyInput">
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal">Close&nbsp;&nbsp;<i class="fa fa-close"></i></button>
        </div>
      </div>
    </div>
  </div>

<div class="modal fade" id="modalDetail">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="modalDetailTitle"></h4>
        <div class="modal-body table-responsive no-padding" style="min-height: 100px">
          <center>
            <i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
          </center>
          <form class="form-horizontal">
            <div class="col-xs-12">
              <input type="hidden" id="form_id">
              <table id="resumeTable" class="table table-bordered table-striped table-hover" style="margin-bottom: 20px;">
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th style="width: 20%; text-align: center; font-size: 1vw;">Total Employee</th>
                    <th style="width: 80%; text-align: center; font-size: 1vw;">Title</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td id="count_all" style="text-align: center; font-size: 1.8vw; font-weight: bold;"></td>
                    <td id="judul" style="text-align: center; font-size: 1.8vw; font-weight: bold;">
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </form>
          <div class="col-xs-12">
            <div class="input-group" style="padding-bottom: 5px;">
              <input type="text" style="text-align: center; border-color: black;" class="form-control input-lg" id="tag" name="tag" placeholder="Scan ID Card Here..." required>
              <div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
                <i class="glyphicon glyphicon-credit-card"></i>
              </div>
            </div>
          </div>
          <div class="col-xs-12" style="padding-top: 10px;">
            <table class="table table-hover table-bordered table-striped" id="tableDetail">
              <thead style="background-color: rgba(126,86,134,.7);">
                <tr>
                  <th style="width: 1%;">#</th>
                  <th style="width: 2%;">ID</th>
                  <th style="width: 5%;">Name</th>
                  <th style="width: 5%;">Dept</th>
                  <!-- <th style="width: 1%;">Action</th> -->
                </tr>
              </thead>
              <tbody id="tableDetailBody">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

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
    $('#tag').val('');
    
    // setInterval(foc, 20000);
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


  $('#modalDetail').on('shown.bs.modal', function () {
    $('#tag').focus();
  }) 

  function clearConfirmation(){
    location.reload(true);
  }

  function foc(){
    $('#tag').focus();
  }

  $('#tag').keydown(function(event) {
    if (event.keyCode == 13 || event.keyCode == 9) {
      if(this.value.length > 7){
        scanTag(this.value);
      }
      else{
        $('#tag').val("");
        $('#tag').focus();
        openErrorGritter('Error!', 'ID Card invalid');
      }
    }
  });

  function scanTag(id){
    var form_id = $('#form_id').val();
    var data = {
      tag:id,
      form_id:form_id
    }

    $.post('{{ url("scan/form_experience/attendance") }}', data, function(result, status, xhr){
      if(result.status){
        $('#tag').val("");
        $('#tag').focus();
        fetchAttendance(form_id);
        openSuccessGritter('Success!', result.message);
      }
      else{
        $('#tag').val("");
        $('#tag').focus();
        openErrorGritter('Error!', result.message);
      }
    });
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
          { "data": "section", "width": "10%"},
          { "data": "grup", "width": "10%"},
          { "data": "judul", "width": "20%"},
          { "data": "grup_kejadian", "width": "10%"},
          { "data": "equipment", "width": "10%"},
          { "data": "target_sosialisasi", "width": "10%"},
          { "data": "jumlah_sosialisasi", "width": "10%"},
          { "data": "sosialisasi", "width": "10%"},
          { "data": "file", "width": "10%"},
          { "data": "action", "width": "10%"},
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
            text: 'Resume Form By Department'
          },  
          legend:{
            enabled: false
          },
          credits:{ 
            enabled:false
          },
          xAxis: {
            categories: department,
            type: 'category',
            labels: {
              style: {
                fontSize: '14px',
                textTransform: 'uppercase'
              }
            },
            // min: 0,
            // max:21,
            scrollbar: {
              enabled: false
            }
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
              return '<b style="text-transform:uppercase">' + this.x + '</b><br/>' +
              'Total: ' + this.y;
            }
          },
          plotOptions: {
            column: {
              stacking: 'normal',
            },
            series:{
              animation: false,
              pointPadding: 0.93,
              groupPadding: 0.93,
              borderWidth: 0.93,
              cursor: 'pointer',
              stacking: 'normal',
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
                    fillInputModal(this.category);
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


  function fillInputModal(group, series) {

    $('#loading').show();
    $('#tableInput').hide();

    var data = {
      group : group
    }

    $.get('{{ url("fetch/form_experience/detail_chart") }}', data, function(result, status, xhr){
      if(result.status){
        $('#tableInput').DataTable().clear();
        $('#tableInput').DataTable().destroy();
        $('#bodyInput').html('');
        $('#loading').hide();

        var body = '';
        for (var i = 0; i < result.data.length; i++) {
          body += '<tr>';
          body += '<td style="width: 5%">'+ result.data[i].tanggal_kejadian +'</td>';
          body += '<td style="width: 5%">'+ result.data[i].lokasi_kejadian +'</td>';
          body += '<td style="width: 5%">'+ result.data[i].equipment +'</td>';
          body += '<td style="width: 5%">'+ result.data[i].grup_kejadian +'</td>';
          body += '<td style="width: 5%">'+ result.data[i].kategori +'</td>';
          body += '<td style="width: 10%">'+ result.data[i].judul +'</td>';
          body += '<td style="width: 1%"><a href="javascript:void(0)" data-toggle="modal" class="btn btn-md btn-success" onClick="sosialisasi('+ result.data[i].id +')"><i class="fa fa-user"></i> <i class="fa fa-exchange"></i> <i class="fa fa-users"></i></a> </td>';
          body += '<td style="width: 1%">'+ result.data[i].jumlah +'</td>';
          body += '<td style="width: 1%; font-weight: bold;"><a href="{{url("index/form_experience/print")}}/'+ result.data[i].id +'" type="button" class="btn btn-warning btn-md" target="_blank"><i class="fa fa-file-pdf-o"></i></a></td>';
          body += '</tr>';
        }

        $('#bodyInput').append(body);

        var table = $('#tableInput').DataTable({
          'dom': 'Bfrtip',
          'responsive':true,
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
            ]
          },
          'paging': false,
          'lengthChange': true,
          'searching': true,
          'ordering': true,
          'info': true,
          'autoWidth': true,
          'sPaginationType': 'full_numbers',
          'bJQueryUI': true,
          'bAutoWidth': false,
          'processing': true,
          'bPaginate': false
        });

        $('#modalInput').modal('show');
        $('#tableInput').show();
      }
    });
  }


  function deleteConfirmation(id) {
    jQuery('#modalDeleteButton').attr("href", '{{ url("index/qc_report/delete") }}'+'/'+id);
  }

  function sosialisasi(id){
    var data = {
      id:id
    }

    $.get('{{ url("fetch/form_experience/attendance") }}', data, function(result, status, xhr) {
      $('#modalDetail').modal('show');
      foc();
      $('#loading').show();
        if(result.status){
          $('#loading').hide();
          $('#judul').text(result.form.judul);
          $('#form_id').val(result.form.id);
          
          foc();

          var tableData = "";
          var count = 1;
          var count_all = 0;
          $('#tableDetailBody').html("");

          $.each(result.form_details, function(key, value) {
            tableData += "<tr id='"+value.id+"''>";
            tableData += "<td>"+count+"</td>";
            tableData += "<td>"+value.employee_id+"</td>";
            tableData += "<td>"+value.name+"</td>";
            tableData += "<td>"+value.department+"</td>";
            tableData += "</tr>";
            count += 1;
            count_all += 1;
          });
          $('#count_all').text(count_all);
          $('#tableDetailBody').append(tableData);
        }
        else{
          audio_error.play();
          $('#loading').hide();
          $('#modalDetail').modal('hide');
          openErrorGritter('Error!', 'Attempt to retrieve data failed');
        }

      });
  }


  function fetchAttendance(id){
    var data = {
      id:id
    }

    $.get('{{ url("fetch/form_experience/attendance") }}', data, function(result, status, xhr) {
        
        if(result.status){
          var tableData = "";
          var count = 1;
          var count_all = 0;
          $('#tableDetailBody').html("");

          $.each(result.form_details, function(key, value) {
            tableData += "<tr id='"+value.id+"''>";
            tableData += "<td>"+count+"</td>";
            tableData += "<td>"+value.employee_id+"</td>";
            tableData += "<td>"+value.name+"</td>";
            tableData += "<td>"+value.department+"</td>";
            tableData += "</tr>";
            count += 1;
            count_all += 1;
          });
          $('#count_all').text(count_all);
          $('#tableDetailBody').append(tableData);
        }
        else{
          audio_error.play();
          $('#loading').hide();
          $('#modalDetail').modal('hide');
          openErrorGritter('Error!', 'Attempt to retrieve data failed');
        }

      });
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