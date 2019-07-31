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
    <span class="text-purple"> 弁付きリポート</span>
  </h1>
  <div class="col-xs-2 input-group date pull-right ">
                  <div class="input-group-addon bg-blue">
                    <i class="fa fa-calendar  "></i>
                  </div>
                  <input type="text" onchange="recall();" class="form-control pull-right" id="datefrom2" name="datefrom2">
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
          <div id="container">
            
          </div>
        </div>
      </div>
    </div>

      <div class="col-xs-12">
      <div class="box">
        <div class="box-body">
          <div id="container2">
            
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
                <th>Posisi</th>
                <th>NG Name</th>
                <th>Total</th>   
                <th>Detail</th>              
              </tr>
            </thead>
            <tbody id="modalProgressBody">
            </tbody>
            <tfoot style="background-color: RGB(252, 248, 227);">
              <th colspan="2">Total</th>
              <th id="totalP"></th>
              <th></th>              
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
    ngMesin();
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
            ngMesin();
            setTimeout(recall, 7000);
          }
  
  function ngTotal() {
     var datep = $('#datefrom2').val();    
    
    var data = {
      datep:datep,
      
    }
    $.get('{{ url("index/getTotalNG") }}',data, function(result, status, xhr){
              console.log(status);
              console.log(result);
              console.log(xhr);
              if(xhr.status == 200){
                if(result.status){

                  var nglist = [];
                  var total = [];
                  var totalH = [];
                  var totalL = [];

                  var total2 = [];
                  var totalH2 = [];
                  var totalL2 = [];
                 
                  for (var i = 0; i < result.ng.length; i++) {                    
                     nglist.push(result.ng[i].ngH);
                     total.push(parseInt(result.ng[i].total));
                     totalH.push(parseInt(result.ngH[i].totalH));
                     totalL.push(parseInt(result.ngL[i].totalL));

                      total2.push(parseInt(result.ng[i].total2)-1);
                     totalH2.push(parseInt(result.ngH[i].totalH2)-1);
                     totalL2.push(parseInt(result.ngL[i].totalL2)-1);
                    } 

                    var tgl ="";
                    
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
        text: 'TOTAL NG RATE MESIN SPOT WELDING'
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
        name: 'Previous Month Total Avg',
        color: 'rgba(165,170,217,1)',
        data: [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        pointPadding: 0.3,
        pointPlacement: -0.3
    }, {
      animation: false,
        name: 'Total to day',
        color: 'rgba(126,86,134,.9)',
        data: total,
        pointPadding: 0.4,
        pointPlacement: -0.3
    }, {
      animation: false,
        name: 'Previous Month High Avg',
        color: 'rgba(248,161,63,1)',
        data: [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        
        pointPadding: 0.3,
        pointPlacement: 0,
       
    }, {
      animation: false,
        name: 'High to day',
        color: 'rgba(186,60,61,.9)',
        data: totalH,
        
        pointPadding: 0.4,
        pointPlacement: 0,
       
    },{
      
      animation: false,
        name: 'Previous Month Low Avg',
        color: 'rgba(166, 247, 67,.9)',
        data: [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        
        pointPadding: 0.3,
        pointPlacement: 0.3,
        
    }, {
      animation: false,
        name: 'Low to day',
        color: 'rgba(2, 125, 27,1)',
        data: totalL,
        
        pointPadding: 0.4,
        pointPlacement: 0.3,
        
    }

    ]
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


function ngMesin() {
  var datep = $('#datefrom2').val();    
    
    var data = {
      datep:datep,
      
    }
    $.get('{{ url("index/getMesinNg") }}',data, function(result, status, xhr){
              console.log(status);
              console.log(result);
              console.log(xhr);
              if(xhr.status == 200){
                if(result.status){

                  var nglist = [];                 
                 
                  for (var i = 0; i < result.ng.length; i++) {                    
                     nglist.push(result.ng[i].ng);
                     
                    } 

                     var tgl ="";
                    
                    if (result.tgl == "") {
                      tgl = " No Data";
                    }else{
                      tgl =result.tgl[0].tgl;
                    }


    Highcharts.chart('container2', {
    
    title: {
        text: 'TOTAL NG RATE MESIN SPOT WELDING'
    },
    subtitle: {
        text: 'Last Update '+tgl
    },
    xAxis: {
        categories: [
          'Mesin 1',
          'Mesin 2',
          'Mesin 3',
          'Mesin 4',
          'Mesin 5',
          'Mesin 6',
        ],
        crosshair: true
    },
    yAxis: {
        min: 0,
        title: {
            text: 'NG TOTAL'
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:.1f} </b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    plotOptions: {
        column: {
            // pointPadding: 0.2,
            grouping: false,
            shadow: false,
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
       type: 'column',
        name: 'Previous Month Total NG Avg',
        data: [15,15,15,15,15,15],
         pointPadding: 0.3,
        pointPlacement: 0,

    }, {
      animation: false,
       type: 'column',
        name: 'Total NG TO DAY',
        data: nglist,
         pointPadding: 0.4,
        pointPlacement: 0,
        point: {
                events: {
                  click: function () {
                    fillModal(this.category , tgl);
                  }
                }
              }

    },{
      animation: false,
        type: 'spline',
        name: 'Maximum NG',
        data: [20,20,20,20,20,20],
        color: 'rgba(248,161,63,1)',
        marker: {
            lineWidth: 2,
            lineColor: 'red',
            fillColor: 'white'
        }
    },]
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
    $.get('{{ url("index/getKensaBensuki2") }}', data, function(result, status, xhr){
      if(result.status){
        // $('#tableModal').DataTable().destroy();
        // $('#modalProgressTitle').html('');
        // $('#modalProgressTitle').html(hpl +' Export Date: '+ date);
        $('#modalProgressBody').html('');
        var resultData = '';
        var total = 0;
        
        $.each(result.ng, function(key, value) {         
          resultData += '<tr id='+ value.posisi+value.ng +'>';
          resultData += '<td style="width: 40%">'+ value.posisi +'</td>';
          resultData += '<td style="width: 40%">'+ value.ng +'</td>';
          resultData += '<td style="width: 20%">'+ value.total +'</td>'; 
          resultData += '<td style="width: 20%"> <button class="btn btn-xs btn-primary" id="expand'+value.posisi+value.ng+'"  onclick="detail(\''+ value.id +'\',this,\''+ value.posisi+value.ng +'\')" style="display:block">Detail</button> <button  class="btn btn-xs btn-primary" id="collapse'+value.posisi+value.ng+'" onclick="collapse(\''+ value.posisi+value.ng +'\',this)" style="display:none">Hide</button></td>';         
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

  function detail(id,element,nik) {

      var data = {
      id:id,      
    }

    $.get('{{ url("index/getKensaBensuki3") }}', data, function(result, status, xhr){
      if(result.status){
        var resultData = '';
        
        var tr_id =  $(element).closest('tr').attr('id');
        $.each(result.ng, function(key, value) {         
          resultData += '<tr>';
          resultData += '<td style="width: 5%">'+ value.model +'</td>';
          resultData += '<td style="width: 10%">'+ value.opbennik +'</td>';
          resultData += '<td style="width: 20%">'+ value.opbennama +'</td>'; 
          resultData += '<td style="width: 5%">'+ value.line +'</td>';
          resultData += '<td style="width: 10%">'+ value.opplatenik +'</td>';
          resultData += '<td style="width: 20%">'+ value.opplatenama +'</td>'; 
          resultData += '<td style="width: 20%">'+ value.shift +'</td>';        
          resultData += '</tr>';                  
        }); 

        // $(element).hide();
        $('#'+tr_id).after('<tr id="col'+tr_id+'"><td colspan="4"><table style="margin: 5px 0 5px 0" width="100%" border="1"> <tr><td>Model</td><td>Op Bensuki</td> <td>Op Bensuki Name</td> <td>Line</td> <td>Op Reed Plate</td> <td>Op Reed Plate Name</td> <td>Op Reed Shift</td><tr>'+resultData+
          '</table></td></tr>');
        $('#expand'+nik).css({'display' : 'none'}); 
        $('#collapse'+nik).css({'display' : 'block'}); 
        
      }
      else{
        alert('Attempt to retrieve data failed');
      }
    });

  }

  function collapse(nik,element) {

  // $(element).hide();
  var tr_id =  $(element).closest('tr').attr('id');
  $("#col"+tr_id).remove();
  $('#collapse'+nik).css({'display' : 'none'});
   $('#expand'+nik).css({'display' : 'block'});  
}
</script>

@stop