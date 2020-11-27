@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

  #loading { display: none; }
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
        @if(Auth::user()->role_code == "MIS" || Auth::user()->role_code == "ACC-SPL")
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
          <div class="col-md-12" style="padding:0">
              <div id="container_resume"></div>
          </div>

          <div class="col-md-12" style="padding:0">
              <div id="container_type"></div>
          </div>

          <div class="col-md-12" style="margin-top:20px;padding: 0">
              <div id="container"></div>
          </div>

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
    // fetchTable();


    $("#loading").show();
    
    var tglfrom = $('#tglfrom').val();
    var tglto = $('#tglto').val();
    var department = $('#department').val();

    var data = {
      tglfrom: tglfrom,
      tglto: tglto,
      department: department,
    };

    $.get('{{ url("fetch/budget/summary") }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){

          var category = [], type = [], amount_category = [], amount_type = [], bulan = [], awal = [], sisa = [];
          var fy;

          $.each(result.cat_budget, function(key, value) {
            category.push(value.category);
            amount_category.push(parseFloat(value.amount));
          })

          $.each(result.type_budget, function(key, value) {
            type.push(value.account_name);
            amount_type.push(parseFloat(value.amount));
          })

          $.each(result.resume, function(key, value) {
            fy = value.periode;
            bulan.push('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
            awal.push(value.jan_simulasi,value.feb_simulasi,value.mar_simulasi,value.apr_simulasi,value.may_simulasi,value.jun_simulasi,value.jul_simulasi,value.aug_simulasi,value.sep_simulasi,value.oct_simulasi,value.nov_simulasi,value.des_simulasi);
          })

          $.each(result.act, function(key, value) {
            sisa.push(value.Actual);
          })

          $('#container_resume').highcharts({
            chart: {
              type: 'column',
              backgroundColor : null
            },
            title: {
              text: 'Simulasi x Actual Budget '+fy,
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
              reversed: true,
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
                name: 'Simulasi Budget',
                data: awal
              },
              {
                name: 'Actual Budget',
                data: sisa
              }
            ]
          })


          $('#container').highcharts({
            chart: {
              type: 'column',
              height: '250px'
            },
            title: {
              text: 'Summary By Category',
              style: {
                fontSize: '24px',
                fontWeight: 'bold'
              }
            },
            xAxis: {
              type: 'category',
              categories: category,
              lineWidth:2,
              lineColor:'#9e9e9e',
              gridLineWidth: 1
            },
            yAxis: {
              enabled:false,
              title:""
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
                name: 'Amount',
                color: '#9c27b0',
                data: amount_category
              }
            ]
          })

          $('#container_type').highcharts({
            chart: {
              type: 'column',
              height: '400px'
            },
            title: {
              text: 'Summary By Type (Account Name)',
              style: {
                fontSize: '24px',
                fontWeight: 'bold'
              }
            },
            xAxis: {
              type: 'category',
              categories: type,
              lineWidth:2,
              lineColor:'#9e9e9e',
              gridLineWidth: 1
            },
            yAxis: {
              enabled:false,
              title:""
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
                name: 'Amount',
                color: '#00a65a',
                data: amount_type
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

    var department = $('#department').val();

    var data = {
      department: department,
    };

    $.get('{{ url("fetch/budget/summary") }}', data, function(result, status, xhr){
      if(xhr.status == 200){
        if(result.status){
          $("#tablecategory").find("td").remove();  
          $('#tablecategory').html("");

          var table = "";

          $.each(result.category, function(key, value) {
              table += '<tr>';
              table += '<td>'+value.category+'</td>';
              table += '<td style="border-left:2 solid #000;">$ '+value.amount.toLocaleString()+'</td>';
              table += '</tr>';
          })

          $('#tablecategory').append(table);


          $("#tabletype").find("td").remove();  
          $('#tabletype').html("");
          var table2 = "";

          $.each(result.type, function(key, value) {
              table2 += '<tr>';
              table2 += '<td>'+value.account_name+'</td>';
              table2 += '<td style="border-left:2 solid #000;">$ '+value.amount.toLocaleString()+'</td>';
              table2 += '</tr>';
          })

          $('#tabletype').append(table2);


        }
      }
    });
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