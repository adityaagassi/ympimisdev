@extends('layouts.master')
@section('stylesheets')
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
     {{ $page }}
   <span class="text-purple"> 外観確認リポート</span>
  </h1>
  <ol class="breadcrumb">
    <!-- <li><a onclick="addOP()" class="btn btn-primary btn-sm" style="color:white">Create {{ $page }}</a></li> -->
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
  <div class="row">

       <div class="col-xs-12">
      <div class="box">
        <div class="box-body">
          <div class="col-xs-6" id="container2">
            
          </div>

          <div class="col-xs-6" id="container3">
            
          </div>
        </div>
      </div>
    </div>

    
    <div class="col-xs-12">
      <div class="box">
        <div class="box-body">
          <div id="container">
            
          </div>
        </div>
      </div>
    </div>

   


  </div>
</section>



@stop

@section('scripts')
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
        <script src="{{ url("js/exporting.js")}}"></script>
        <script src="{{ url("js/export-data.js")}}"></script>
        <script src="{{ url("js/highcharts-3d.js")}}"></script>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  jQuery(document).ready(function() { 
    ngTotal();
    recall();
    
     $('body').toggleClass("sidebar-collapse");
    $('.select2').select2({
      dropdownAutoWidth : true,
      width: '100%',
    });
  });

  function recall() {
            ngTotal();
            setTimeout(recall, 6000);
          }
  
  function ngTotal() {
    $.get('{{ url("index/getKensaVisualALL") }}', function(result, status, xhr){
              console.log(status);
              console.log(result);
              console.log(xhr);
              if(xhr.status == 200){
                if(result.status){

                  var nglist = [];
                  var total = [];
                  var totallas = [];
                    for (var i = 0; i < result.ng.length; i++) {                    
                     nglist.push(result.ng[i].location.replace("PN_Kakuning_Visual_", ""));
                     total.push(parseInt(result.ng[i].tot));
                     totallas.push(parseInt(result.nglas[i].tot));
                     
                    } 

                    
    Highcharts.chart('container', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'TOTAL NG RATE KAKUNIN VISUAL'
    },
    subtitle: {
        text: 'Last Update '+result.tgl[0].tgl
    },
    xAxis: {
        categories: nglist
    },
    yAxis: {
        min: 0,
        title: {
            text: 'TOTAL NG'
        }
    },
    
    tooltip: {
        shared: false
    },
    plotOptions: {
        column: {
            grouping: false,
            shadow: false,
            borderWidth: 0,
             dataLabels: {
                enabled: true
            }
        }

    },
    credits:{
                enabled:false,
              },
    series: [{
      animation: false,
        name: 'Total yesterday',
        color: 'rgba(165,170,217,1)',
        data: totallas,
        pointPadding: 0.3,
        // pointPlacement: -0.3
    }, {
      animation: false,
        name: 'Total to day',
        color: 'rgba(126,86,134,.9)',
        data: total,
        pointPadding: 0.4,
        // pointPlacement: -0.3
    }

    ]
});


    //pie 1
    Highcharts.chart('container2', {
              chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie',
                options3d: {
                  enabled: true,
                  alpha: 45,
                  beta: 0
                }
              },
              title: {
                text: 'TOTAL NG RATE KAKUNIN VISUAL YESTERDAY'
              },
              subtitle: {
        text: 'Last Update '+result.tgl[0].tgl
    },
              tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
              },
              plotOptions: {
                pie: {
                  allowPointSelect: true,
                  cursor: 'pointer',
                  depth: 35,
                  dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b><br>{point.percentage:.1f} %',
                    style: {
                      fontSize: '1vw'
                    },
                    distance: -50,
                    filter: {
                      property: 'percentage',
                      operator: '>',
                      value: 4
                    }
                  },
                  showInLegend: false
                }
              },
              credits:{
                enabled:false,
              },
              exporting:{
                enabled:false,
              },

              series: [{

                animation: false,
                name: 'Percentage',
                colorByPoint: true,
                data: [{
                  name: 'Button',
                  y: parseInt(result.nglas[0].tot)
                  // y: 1                 
                },
                {
                  name: 'Cover Lower',
                  y: parseInt(result.nglas[1].tot) 
                  // y: 1                 
                },
                {
                  name: 'Cover R/L',
                  y: parseInt(result.nglas[2].tot) 
                  // y: 1                 
                }
                ,
                {
                  name: 'Frame Assy',
                  y: parseInt(result.nglas[3].tot) 
                  // y: 1                 
                },{
                  name: 'Handle',
                  y: parseInt(result.nglas[4].tot) 
                  // y: 1                 
                },{
                  name: 'Pianica',
                  y: parseInt(result.nglas[5].tot) 
                  // y: 1                 
                }

                ]
              }]
            });


    //pie 2
    Highcharts.chart('container3', {
              chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie',
                options3d: {
                  enabled: true,
                  alpha: 45,
                  beta: 0
                }
              },
              title: {
                text: 'TOTAL NG RATE KAKUNIN VISUAL TO DAY'
              },
              subtitle: {
        text: 'Last Update '+result.tgl[0].tgl
    },
              tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
              },
              plotOptions: {
                pie: {
                  allowPointSelect: true,
                  cursor: 'pointer',
                  depth: 35,
                  dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b><br>{point.percentage:.1f} %',
                    style: {
                      fontSize: '1vw'
                    },
                    distance: -50,
                    filter: {
                      property: 'percentage',
                      operator: '>',
                      value: 4
                    }
                  },
                  showInLegend: false
                }
              },
              credits:{
                enabled:false,
              },
              exporting:{
                enabled:false,
              },

              series: [{

                animation: false,
                name: 'Percentage',
                colorByPoint: true,
                data: [{
                  name: 'Button',
                  y: parseInt(result.ng[0].tot)
                  // y: 1                 
                },
                {
                  name: 'Cover Lower',
                  y: parseInt(result.ng[1].tot) 
                  // y: 1                 
                },
                {
                  name: 'Cover R/L',
                  y: parseInt(result.ng[2].tot) 
                  // y: 1                 
                }
                ,
                {
                  name: 'Frame Assy',
                  y: parseInt(result.ng[3].tot) 
                  // y: 1                 
                },{
                  name: 'Handle',
                  y: parseInt(result.ng[4].tot) 
                  // y: 1                 
                },{
                  name: 'Pianica',
                  y: parseInt(result.ng[5].tot) 
                  // y: 1                 
                }]
              }]
            });
    }
              else{                
                // openErrorGritter('Error!', result.message);
              }
            }
            else{

              alert("Disconnected from server");
            }
          });
  }


</script>

@stop