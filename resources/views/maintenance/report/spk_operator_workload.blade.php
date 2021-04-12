@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
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
  tfoot>tr>td{
    text-align:center;
  }
  th:hover {
    overflow: visible;
  }
  td:hover {
    overflow: visible;
  }
  table.table-bordered{
    border:1px solid black;
  }
  table.table-bordered > thead > tr > th{
    border:1px solid black;
    background-color: #605ca8;
    color: white;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid black;
    vertical-align: middle;
    padding:0;
    background-color: #fffcb7; 
  }

  table.table-bordered > tbody > tr > th{
    border:1px solid black;
    vertical-align: middle;
    padding:0;
    color: white;
    text-align: center;
    background-color: #605ca8;
  }

  table.table-bordered > tfoot > tr > th{
    border:1px solid black;
    padding:0;
  }
  td{
    overflow:hidden;
    text-overflow: ellipsis;
  }
  .dataTable > thead > tr > th[class*="sort"]:after{
    content: "" !important;
  }

  .inprogress {
    background-color: #6ddb5e !important;
  }

  .pending {
    background-color: #e83e27 !important;
  }

</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
  <div class="row">
    <div class="col-xs-12">
      <div class="row" id="contain">
        <!-- <div class="col-xs-2">
         <table class="table table-bordered">
           <thead>
             <tr>
               <th colspan="2">PI2002021 <br> M. Nasiqul Ibat</th>
             </tr>
           </thead>
           <tbody>
             <tr>
               <th colspan="2">Pending</th>
             </tr>
             <tr>
               <td class="pending" width="50%">SPK1201122</td>
               <td class="pending" width="50%">2021-01-14 12:25:25</td>
             </tr>
             <tr>
               <th colspan="2">Inprogress</th>
             </tr>
             <tr>
               <td class="inprogress" width="50%">SPK1201122</td>
               <td class="inprogress" width="50%">2021-01-14 12:25:25</td>
             </tr>
             <tr>
               <th colspan="2">Listed</th>
             </tr>
             <tr>
               <td>SPK120112</td>
               <td></td>
             </tr>
           </tbody>
         </table>
       </div> -->
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

    getData();
    setInterval(getData, 5000);
  });

  function getData() {
    $.get('{{ url("fetch/maintenance/spk/workload") }}', function(result, status, xhr){
      $("#contain").empty();
      var container = "";

      container += '<table width="100%" border="1">';

      $.each(result.operator, function(index, value){
        if (index == 0) {
          container += '<tr>';
        } 

        if (index % 5 ===  0) {
          container += '</tr>';

          container += '<tr>';
          
          container += '<td>';
          container += '<table class="table table-bordered">';
          container += '<thead>';
          container += '<tr>';
          container += '<th colspan="2">'+value.employee_id+' <br> '+value.name+'</th>';
          container += '</tr>';
          container += '<tbody>';
          container += '<tr id="'+value.employee_id+'_pending"><th colspan="2">Pending</th></tr>';
          container += '<tr id="'+value.employee_id+'_inprogress"><th colspan="2">Inprogress</th></tr>';
          container += '<tr id="'+value.employee_id+'_listed"><th colspan="2">Listed</th></tr>';
          container += '</tbody>';
          container += '</table>';
          container += '</td>';
        } else {
          container += '<td>';
          container += '<table class="table table-bordered">';
          container += '<thead>';
          container += '<tr>';
          container += '<th colspan="2">'+value.employee_id+' <br> '+value.name+'</th>';
          container += '</tr>';
          container += '<tbody>';
          container += '<tr id="'+value.employee_id+'_pending"><th colspan="2">Pending</th></tr>';
          container += '<tr id="'+value.employee_id+'_inprogress"><th colspan="2">Inprogress</th></tr>';
          container += '<tr id="'+value.employee_id+'_listed"><th colspan="2">Listed</th></tr>';
          container += '</tbody>';
          container += '</table>';
          container += '</td>';
        }
      });

      container += '</table>';


      $("#contain").append(container);


      $.each(result.operator, function(index2, value2){
        var pend = "";
        var prog = "";
        var list = "";
        $.each(result.datas, function(index, value){
          if (value.operator_id == value2.employee_id) {
            if (value.remark == '3') {
              pend += '<tr>';
              pend += '<td class="pending" width="50%">'+value.order_no+'</td>';
              pend += '<td class="pending" width="50%">'+(value.start_actual || '')+'</td>';
              pend += '</tr>';
            } else if (value.remark == '4') {
              prog += '<tr>';
              prog += '<td class="inprogress" width="50%">'+value.order_no+'</td>';
              prog += '<td class="inprogress" width="50%">'+(value.start_actual || '')+'</td>';
              prog += '</tr>';
            } else if (value.remark == '5') {
              list += '<tr>';
              list += '<td width="50%">'+value.order_no+'</td>';
              list += '<td width="50%"></td>';
              list += '</tr>';
            }
          }
        })

        $("#"+value2.employee_id+"_pending").after(pend);
        $("#"+value2.employee_id+"_inprogress").after(prog);
        $("#"+value2.employee_id+"_listed").after(list);
      })

// <div class="col-xs-2">
//           <table class="table table-bordered">
//             <thead>
//               <tr>
//                 <th colspan="2">PI2002021 <br> M. Nasiqul Ibat</th>
//               </tr>
//             </thead>
//             <tbody>
//             <tr>
//                 <th colspan="2">Pending</th>
//               </tr>
//               <tr>
//                 <td class="pending" width="50%">SPK1201122</td>
//                 <td class="pending" width="50%">2021-01-14 12:25:25</td>
//               </tr>
//               <tr>
//                 <th colspan="2">Inprogress</th>
//               </tr>
//               <tr>
//                 <td class="inprogress" width="50%">SPK1201122</td>
//                 <td class="inprogress" width="50%">2021-01-14 12:25:25</td>
//               </tr>
//               <tr>
//                 <th colspan="2">Listed</th>
//               </tr>
//               <tr>
//                 <td>SPK120112</td>
//                 <td></td>
//               </tr>
//             </tbody>
//           </table>
//         </div>
})
}

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

Highcharts.createElement('link', {
  href: '{{ url("fonts/UnicaOne.css")}}',
  rel: 'stylesheet',
  type: 'text/css'
}, null, document.getElementsByTagName('head')[0]);

Highcharts.theme = {
  colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
  '#eeaaee', '#55BF3B', '#DF5353', '#c39bd3', '#fdfefe', '#ba4a00', '#ffeb3b', '#b0bec5', '#0288d1', '#ec407a', '#a1887f'],
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
@endsection