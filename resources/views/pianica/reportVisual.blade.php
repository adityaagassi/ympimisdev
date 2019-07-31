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
  <div class="col-xs-2 input-group date pull-right ">
                  <div class="input-group-addon bg-blue">
                    <i class="fa fa-calendar  "></i>
                  </div>
                  <input type="text" onchange="ngTotal();" class="form-control pull-right" id="datefrom2" name="datefrom2">
                </div><br><br>
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

  <div class="modal fade" id="modalProgress">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modalProgressTitle"></h4>
         <h4 class="modal-title" id="modalProgressTitle2"></h4>
         <h4 class="modal-title" id="modalProgressTitle3"></h4>
        <div class="modal-body table-responsive no-padding" style="min-height: 100px">
          <center>
            <i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
          </center>
          <table class="table table-hover table-bordered table-striped" id="tableModal">
            <thead style="background-color: rgba(126,86,134,.7);">
              <tr>
                <th>NG Name</th>
                <th>Total</th>                
              </tr>
            </thead>
            <tbody id="modalProgressBody">
            </tbody>
            <tfoot style="background-color: RGB(252, 248, 227);">
              <th>Total</th>
              <th id="totalP"></th>              
            </tfoot>
          </table>
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

      $('#datefrom2').datepicker({
      autoclose: true, 
      format :'yyyy-mm-dd',
    });
  });

  function recall() {
            ngTotal();
            setTimeout(recall, 6000);
          }
  
  function ngTotal() {

    var datep = $('#datefrom2').val();    
    
    var data = {
      datep:datep,
      
    }
    $.get('{{ url("index/getKensaVisualALL") }}',data, function(result, status, xhr){
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

                    var tgl ="";
                    var tgly ="";

                    if (result.tgly == "") {
                      tgly = " No Data";
                    }else{
                      tgly =result.tgly[0].tgl;
                    }

                    if (result.tgl == "") {
                      tgl = " No Data";
                    }else{
                      tgl =result.tgl[0].tgl;
                    }

                    
    Highcharts.chart('container', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'TOTAL NG RATE KAKUNIN VISUAL'
    },
    subtitle: {
        text: 'Last Update '+tgl
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
        shared: false,

    },
    plotOptions: {
        column: {
            grouping: false,
            shadow: false,
            cursor: 'pointer',
            borderWidth: 0,
             dataLabels: {
                enabled: true
            }
        },        

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
         point: {
                events: {
                  click: function () {
                    fillModal(this.category , tgly);
                  }
                }
              }
        // pointPlacement: -0.3
    }, {
      animation: false,
        name: 'Total to day',
        color: 'rgba(126,86,134,.9)',
        data: total,
        pointPadding: 0.4,
         point: {
                events: {
                  click: function () {
                    fillModal(this.category , tgl);
                  }
                }
              }
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
        text: 'Last Update '+tgly
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
        text: 'Last Update '+tgl
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

  function fillModal(ng, tgl){
    $('#modalProgress').modal('show');
    $('#loading').show();
    $('#modalProgressTitle').hide();
    $('#tableModal').hide();

    var data = {
      ng:ng,
      tgl:tgl
    }
    $.get('{{ url("index/getKensaVisualALL2") }}', data, function(result, status, xhr){
      if(result.status){
        // $('#tableModal').DataTable().destroy();
        // $('#modalProgressTitle').html('');
        // $('#modalProgressTitle').html(hpl +' Export Date: '+ date);
        $('#modalProgressBody').html('');
        var resultData = '';
        var total = 0;
        
        $.each(result.ng, function(key, value) {         
          resultData += '<tr>';
          resultData += '<td style="width: 80%">'+ value.ng +'</td>';
          resultData += '<td style="width: 20%">'+ value.total +'</td>';          
          resultData += '</tr>';   
          total += value.total;       
        });
        
        $('#loading').hide();
        $('#modalProgressBody').append(resultData);
        $('#totalP').text(total);
        $('#modalProgressTitle2').text(ng);
        $('#modalProgressTitle3').text(tgl);
        
        // $('#modalProgressTitle').show();
        $('#tableModal').show();
      }
      else{
        alert('Attempt to retrieve data failed');
      }
    });
  }


</script>

@stop