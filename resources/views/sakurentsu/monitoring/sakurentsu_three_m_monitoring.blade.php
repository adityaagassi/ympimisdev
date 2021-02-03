@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

  #main_table {
    color: white;
    border: 1px solid white;
  }

  #main_table > thead > tr > th {
    color: white;
    text-align: center;
    padding: 2px;
  }

  #main_table > tbody > tr > td {
    padding: 2px;
  }

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
    color: white;
  }

  #loading, #error { display: none; }

</style>
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0; padding-bottom: 0">
  <div class="row">
    <div class="col-xs-12" style="padding: 1px !important">
      <div class="col-xs-2">
        <div class="input-group date">
          <div class="input-group-addon bg-green">
            <i class="fa fa-calendar"></i>
          </div>
          <input type="text" class="form-control datepicker" id="tglfrom" placeholder="Month From" style="width: 100%;">
        </div>
      </div>

      <div class="col-xs-2">
        <div class="input-group date">
          <div class="input-group-addon bg-green">
            <i class="fa fa-calendar"></i>
          </div>
          <input type="text" class="form-control datepicker" id="tglto" placeholder="Month To" style="width: 100%;">
        </div>
      </div>


      <div class="col-xs-2">
        <button class="btn btn-success btn-sm" onclick="drawChart()">Update Chart</button>
      </div>
    </div>

    <div class="col-xs-12" style="margin-top: 5px; padding-right: 0;padding-left: 10px">
      <div id="chart" style="width: 99%"></div>
    </div>

    <div class="col-xs-12" style="padding-right: 0;padding-left: 10px;">
      <table style="width: 100%" id="main_table" border="1">
        <thead>
          <tr>
            <th colspan="5" style="background-color: #e36e14; font-weight: bold; padding: 2px; border-right: 2px solid red; border-left: 2px solid red">Sakurentsu</th>
            <th colspan="5" style="background-color: #0c42ad; font-weight: bold; padding: 2px; border-right: 2px solid red">3M Form</th>
            <th colspan="3" style="background-color: #0c42ad; font-weight: bold; padding: 2px">Sign 3M</th>
            <th style="background-color: #0c42ad; font-weight: bold; padding: 2px; border-right: 2px solid red">STD Receive</th>
            <th style="background-color: #930cad; font-weight: bold; padding: 2px">3M Imp Form</th>
            <th colspan="2" style="background-color: #930cad; font-weight: bold; padding: 2px">Sign 3M Imp</th>
            <th style="background-color: #930cad; font-weight: bold; padding: 2px; border-right: 2px solid red">STD Receive</th>
          </tr>
          <tr>
            <th style="border-left: 2px solid red">No</th>
            <th>Title</th>
            <th>Target Date</th>
            <th>Interpreter</th>
            <th style="border-right: 2px solid red">Proposer</th>
            <th>Proposer</th>
            <th>Interpreter</th>
            <th>PreMeeting</th>
            <th>Document</th>
            <th style="border-right: 2px solid red">Final Meeting</th>
            <th>Related Dept</th>
            <th>DGM / GM</th>
            <th>Presdir</th>
            <th style="border-right: 2px solid red">STD</th>
            <th>Implement Check</th>
            <th>Related Dept.</th>
            <th>DGM / GM</th>
            <th style="border-right: 2px solid red">STD</th>
          </tr>
        </thead>
        <tbody id="body_main"></tbody>
      </table>
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

  var arr_option = [];

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");

    $('.select2').select2({
      dropdownAutoWidth : true,
      allowClear:true,
    });

    getData();
  });

  function getData() {
    data = {

    }

    var body_master = "";
    $("#body_main").empty();

    $.get('{{ url("fetch/sakurentsu/monitoring/3m") }}', data, function(result, status, xhr) {
      $(result.data_sakurentsu).each(function(index, value) {
        body_master += "<tr>";
        body_master += "<td style='border-left: 2px solid red'>"+(value.sakurentsu_number || '')+"</td>";
        body_master += "<td>"+(value.title || '')+"</td>";
        body_master += "<td><label class='label label-primary'>"+(value.target_dt || '')+"</label></td>";
        body_master += "<td><span class='label label-success'>"+(value.trans_sk || '')+"</span></td>";
        body_master += "<td style='border-right: 2px solid red'><center><span class='label label-success'>"+(value.department_shortname || '')+"</span></center></td>";

        if (value.name) {
          body_master += "<td><center><span class='label label-success'>"+value.name+"</span></center></td>";
        } else {
          body_master += "<td></td>";
        }


        if (value.trans_m) {
          body_master += "<td><center><span class='label label-success'>"+value.trans_m+"</span></center></td>";
        } else {
          body_master += "<td></td>";
        }


        //PREMEETING
        if (value.remark > 2) {
          body_master += "<td><center><span class='label label-success'>Done</span></center></td>";
        } else {
          body_master += "<td></td>";
        }


        //DOC
        if (value.remark >= 4) {
          body_master += "<td><center><span class='label label-success'>Done</span></center></td>";
        } else if (value.remark == 3) {
          $(result.data_doc).each(function(index2, value2) {
            if (value2.form_id == value.id_tiga_em) {
              body_master += "<td><center><span class='label label-warning'>"+value2.doc_uploaded+" / "+value2.doc_all+"</span></center></td>";
            }
          })
        } else {
          body_master += "<td></td>";
        }


        //FINAL MEETING
        if (value.remark >= 5) {
          body_master += "<td style='border-right: 2px solid red'><center><span class='label label-success'>Done</span></center></td>";
        } else {
          body_master += "<td style='border-right: 2px solid red'></td>";
        }


        //TTD Departemen
        if (value.related_department) {
          var count_rel = value.related_department.split(",");

          var stat_apr = 0;
          $(result.data_approve).each(function(index3, value3) {
            if (value3.form_id == value.id_tiga_em) {
              if (count_rel.length > value3.dpt) {
                body_master += "<td><center><span class='label label-warning'>"+value3.dpt+" / "+count_rel.length+"</span></center></td>";
                stat_apr = 1;
              } else {
                body_master += "<td><center><span class='label label-success'>Done</span></center></td>";
                stat_apr = 1;
              }
            }
          })

          if (stat_apr == 0) {
            body_master += "<td></td>";
          }
        } else {
          body_master += "<td></td>";
        }
        

        // TTD DGM / GM
        var stat_gm = 0;
        $(result.data_approve).each(function(index4, value4) {
          if (value4.form_id == value.id_tiga_em) {
            if (value4.dgm == 1 && value4.gm == 2) {
              body_master += "<td><center><span class='label label-success'>Done</span></center></td>";
              stat_gm = 1;
            } else if (value4.dgm == 0 && value4.gm == 0) {
              body_master += "<td></td>";
            } else {
              body_master += "<td><center><span class='label label-warning'>progress</span></center></td>";
              stat_gm = 1;
            }
          }
        })

        if (stat_gm == 0) {
          body_master += "<td></td>";
        }


        // PRESDIR
        var stat_presdir = 0;
        $(result.data_approve).each(function(index5, value5) {
          if (value5.form_id == value.id_tiga_em) {
            if (value5.presdir == 1) {
              body_master += "<td><center><span class='label label-success'>Done</span></center></td>";
              stat_presdir = 1;
            }
          }
        })

        if (stat_presdir == 0) {
          body_master += "<td></td>";
        }


        // STD Receive
        if (value.remark == 6) {
          body_master += "<td style='border-right: 2px solid red'><center><span class='label label-danger'>waiting</span></center></td>";
        } else if (value.remark >= 7) {
          body_master += "<td style='border-right: 2px solid red'><center><span class='label label-success'>Done</span></center></td>";
        } else {
          body_master += "<td style='border-right: 2px solid red'></td>";
        }


        // Implement Check
        if (value.remark == 7) {
          body_master += "<td><center><span class='label label-danger'>waiting</span></center></td>";
        } else if (value.remark >= 8) {
          body_master += "<td><center><span class='label label-success'>Done</span></center></td>";
        } else {
          body_master += "<td></td>";
        }


        // Implement Sign Dept
        var stat_imp_dpt = 0;
        $(result.data_sign_imp).each(function(index5, value5) {
          if (value5.form_id == value.id_tiga_em) {
            var count_imp_rel = value.related_department.split(",");
            if (count_imp_rel.length > value5.imp_dpt) {
              body_master += "<td><center><span class='label label-warning'>"+value5.imp_dpt+" / "+count_imp_rel.length+"</span></center></td>";
              stat_imp_dpt = 1;
            } else {
              body_master += "<td><center><span class='label label-success'>Done</span></center></td>";
              stat_imp_dpt = 1;
            }
          }
        })

        if (stat_imp_dpt == 0) {
          body_master += "<td><span class='label label-danger'></span></td>";
        }


        // Implement Sign DGM GM
        var stat_imp_gm = 0;
        $(result.data_sign_imp).each(function(index6, value6) {
          if (value6.form_id == value.id_tiga_em) {
            if (value6.imp_dgm == 1 && value6.imp_gm == 1) {
              body_master += "<td><center><span class='label label-success'>Done</span></center></td>";
              stat_imp_gm = 1;
            } else {
              body_master += "<td><center><span class='label label-warning'>progress</span></center></td>";
              stat_imp_gm = 1;
            }
          }
        })

        if (stat_imp_gm == 0) {
          body_master += "<td></td>";
        }


        // STD Close
        if (value.remark == 9) {
          body_master += "<td style='border-right: 2px solid red'><center><span class='label label-primary'>Waiting</span></center></td>";
        } else if(value.remark == 10) {
          body_master += "<td style='border-right: 2px solid red'><center><span class='label label-success'>Close</span></center></td>";
        } else {
          body_master += "<td style='border-right: 2px solid red'></td>";

        }


        body_master += "</tr>";
      })

$("#body_main").append(body_master);
})

}


$('.datepicker').datepicker({
  format: "yyyy-mm",
  startView: "months", 
  minViewMode: "months",
  autoclose: true,
});


var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

function drawChart() {    
  var tglfrom = $('#tglfrom').val();
  var tglto = $('#tglto').val();
  var kategori = $('#kategori').val();
  var departemen = $('#departemen').val();
  var status = $('#status').val();
  var sumber = $('#sumber').val();

  var data = {
    tglfrom: tglfrom,
    tglto: tglto,
    kategori: kategori,
    departemen: departemen,
    status:status,
    sumber:sumber
  };

  $.get('{{ url("index/qc_report/fetchReport") }}', data, function(result, status, xhr) {
    if(xhr.status == 200){
      if(result.status){

      }
    }
  })
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