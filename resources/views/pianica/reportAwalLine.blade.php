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
    <small>it all starts here</small>
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
    
     $('body').toggleClass("sidebar-collapse");
    $('.select2').select2({
      dropdownAutoWidth : true,
      width: '100%',
    });
  });
  
  function ngTotal() {
    $.get('{{ url("index/getKensaAwalALLLine") }}', function(result, status, xhr){
              console.log(status);
              console.log(result);
              console.log(xhr);
              if(xhr.status == 200){
                if(result.status){

                  var nglist = [];
                  var total1 = [];
                  var total2 = [];
                  var total3 = [];
                  var total4 = [];
                  var total5 = [];

                  var total1las = [];
                  var total2las = [];
                  var total3las = [];
                  var total4las = [];
                  var total5las = [];
                  
                  var totalJenisNGA1 = 0;
                  var totalJenisNGA2 = 0;
                  var totalJenisNGA3 = 0;
                  var totalJenisNGA4 = 0;
                  var totalJenisNGA5 = 0;

                  var totalJenisNGA1las = 0;
                  var totalJenisNGA2las = 0;
                  var totalJenisNGA3las = 0;
                  var totalJenisNGA4las = 0;
                  var totalJenisNGA5las = 0;
                  
                  var totalCek1 = result.total[0].total_1;
                  var totalCek2 = result.total[1].total_1;
                  var totalCek3 = result.total[2].total_1;
                  var totalCek4 = result.total[3].total_1;
                  var totalCek5 = result.total[4].total_1;

                  var totalCek1las = result.totallas[0].total_1;
                  var totalCek2las = result.totallas[1].total_1;
                  var totalCek3las = result.totallas[2].total_1;
                  var totalCek4las = result.totallas[3].total_1;
                  var totalCek5las = result.totallas[4].total_1;

                  var totalOK1 = result.total[0].total_1 - result.total[0].ng_1;
                  var totalOK2 = result.total[1].total_1 - result.total[1].ng_1;
                  var totalOK3 = result.total[2].total_1 - result.total[2].ng_1;
                  var totalOK4 = result.total[3].total_1 - result.total[3].ng_1;
                  var totalOK5 = result.total[4].total_1 - result.total[4].ng_1;

                  var totalOK1las = result.totallas[0].total_1 - result.totallas[0].ng_1;
                  var totalOK2las = result.totallas[1].total_1 - result.totallas[1].ng_1;
                  var totalOK3las = result.totallas[2].total_1 - result.totallas[2].ng_1;
                  var totalOK4las = result.totallas[3].total_1 - result.totallas[3].ng_1;
                  var totalOK5las = result.totallas[4].total_1 - result.totallas[4].ng_1;

                  for (var i = 0; i < result.ng.length; i++) {    
                     totalJenisNGA1 += parseInt(result.ng[i].total_1);
                     totalJenisNGA2 += parseInt(result.ng[i].total_2);
                     totalJenisNGA3 += parseInt(result.ng[i].total_3);
                     totalJenisNGA4 += parseInt(result.ng[i].total_4);
                     totalJenisNGA5 += parseInt(result.ng[i].total_5);

                     totalJenisNGA1las += parseInt(result.nglas[i].total_1);
                     totalJenisNGA2las += parseInt(result.nglas[i].total_2);
                     totalJenisNGA3las += parseInt(result.nglas[i].total_3);
                     totalJenisNGA4las += parseInt(result.nglas[i].total_4);
                     totalJenisNGA5las += parseInt(result.nglas[i].total_5);

                    } 
                     var totalJenisNGC1 = totalJenisNGA1 - (totalCek1 - totalOK1);
                     var totalJenisNGC2 = totalJenisNGA2 - (totalCek2 - totalOK2);
                     var totalJenisNGC3 = totalJenisNGA3 - (totalCek3 - totalOK3);
                     var totalJenisNGC4 = totalJenisNGA4 - (totalCek4 - totalOK4);
                     var totalJenisNGC5 = totalJenisNGA5 - (totalCek5 - totalOK5);

                     var totalJenisNGC1las = totalJenisNGA1las - (totalCek1las - totalOK1las);
                     var totalJenisNGC2las = totalJenisNGA2las - (totalCek2las - totalOK2las);
                     var totalJenisNGC3las = totalJenisNGA3las - (totalCek3las - totalOK3las);
                     var totalJenisNGC4las = totalJenisNGA4las - (totalCek4las - totalOK4las);
                     var totalJenisNGC5las = totalJenisNGA5las - (totalCek5las - totalOK5las);

                     var PengurangC1 = Math.round( totalJenisNGC1 / 4);
                     var PengurangC2 = Math.round( totalJenisNGC2 / 4);
                     var PengurangC3 = Math.round( totalJenisNGC3 / 4);
                     var PengurangC4 = Math.round( totalJenisNGC4 / 4);
                     var PengurangC5 = Math.round( totalJenisNGC5 / 4);

                     var PengurangC1las = Math.round( totalJenisNGC1las / 4);
                     var PengurangC2las = Math.round( totalJenisNGC2las / 4);
                     var PengurangC3las = Math.round( totalJenisNGC3las / 4);
                     var PengurangC4las = Math.round( totalJenisNGC4las / 4);
                     var PengurangC5las = Math.round( totalJenisNGC5las / 4);

                    for (var i = 0; i < result.ng.length; i++) {                    
                     nglist.push(result.ng[i].ng_name);
                     total1.push(parseInt(result.ng[i].total_1 - PengurangC1)); 
                     total2.push(parseInt(result.ng[i].total_2 - PengurangC2));
                     total3.push(parseInt(result.ng[i].total_3 - PengurangC3));
                     total4.push(parseInt(result.ng[i].total_4 - PengurangC4));                   
                     total5.push(parseInt(result.ng[i].total_5 - PengurangC5));

                     total1las.push(parseInt(result.nglas[i].total_1 - PengurangC1las)); 
                     total2las.push(parseInt(result.nglas[i].total_2 - PengurangC2las));
                     total3las.push(parseInt(result.nglas[i].total_3 - PengurangC3las));
                     total4las.push(parseInt(result.nglas[i].total_4 - PengurangC4las));                   
                     total5las.push(parseInt(result.nglas[i].total_5 - PengurangC5las));
                     
                    } 

                    
    Highcharts.chart('container', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'TOTAL NG RATE KENSA AWAL'
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
        name: 'Line 1 yesterday',
        color: 'rgba(165,170,217,1)',
        data: total1las,
        pointPadding: 0.3,
        pointPlacement: -0.4,
        pointWidth: 20,

    }, {
        name: 'Line 1 to day',
        color: 'rgba(126,86,134,.5)',
        data: total1,
        pointPadding: 0.4,
        pointPlacement: -0.4
    },
    {
        name: 'Line 2 yesterday',
        color: 'rgba(186,60,61,.9)',
        data:  total2las,
        pointPadding: 0.3,
        pointPlacement: -0.2,
        pointWidth: 20
    }, {
        name: 'Line 2 to day',
        color: 'rgba(186,60,61,.5)',
        data: total2,
        pointPadding: 0.4,
        pointPlacement: -0.2
    },
    {
        name: 'Line 3 yesterday',
        color: 'rgba(248,161,63,1)',
        data: total3las,
        pointPadding: 0.3,
        pointPlacement: 0,
        pointWidth: 20
    }, {
        name: 'Line 3 to day',
        color: 'rgba(248,161,63,.5)',
        data: total3,
        pointPadding: 0.4,
        pointPlacement: 0
    },
    {
        name: 'Line 4 yesterday',
        color: 'rgba(2, 125, 27,1)',
        data:  total4las,
        pointPadding: 0.3,
        pointPlacement: 0.2,
        pointWidth: 20
    }, {
        name: 'Line 4 to day',
        color: 'rgba(2, 125, 27,.5)',
        data: total4 ,
        pointPadding: 0.4,
        pointPlacement: 0.2
    },
    {
        name: 'Line 5 yesterday',
        color: 'rgba(0,0,0,1)',
        data:  total5las,
        pointPadding: 0.3,
        pointPlacement: 0.4,
        pointWidth: 20
    }, {
        name: 'Line 5 to day',
        color: 'rgba(0,0,0,0.5)',
        data: total5 ,
        pointPadding: 0.4,
        pointPlacement: 0.4
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


</script>

@stop