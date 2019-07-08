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
    <span class="text-purple"> 全ラインの最終検査リポート</span>
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
    $.get('{{ url("index/getKensaAkhirALLLine") }}', function(result, status, xhr){
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
                  
                 

                    for (var i = 0; i < result.ng.length; i++) {                    
                     nglist.push(result.ng[i].ng_name);
                     total1.push(parseInt(result.ng[i].total_1)); 
                     total2.push(parseInt(result.ng[i].total_2));
                     total3.push(parseInt(result.ng[i].total_3));
                     total4.push(parseInt(result.ng[i].total_4));                   
                     total5.push(parseInt(result.ng[i].total_5));

                     total1las.push(parseInt(result.nglas[i].total_1)); 
                     total2las.push(parseInt(result.nglas[i].total_2));
                     total3las.push(parseInt(result.nglas[i].total_3));
                     total4las.push(parseInt(result.nglas[i].total_4));                   
                     total5las.push(parseInt(result.nglas[i].total_5));
                     
                    } 

                    
    Highcharts.chart('container', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'TOTAL NG RATE KENSA AKHIR'
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
        name: 'Line 2 yesterday',
        color: 'rgba(165,170,217,1)',
        data: total1las,
        pointPadding: 0.3,
        pointPlacement: -0.4,
        pointWidth: 20,

    }, {
        name: 'Line 2 to day',
        color: 'rgba(126,86,134,.5)',
        data: total1,
        pointPadding: 0.4,
        pointPlacement: -0.4
    },
    {
        name: 'Line 3 yesterday',
        color: 'rgba(186,60,61,.9)',
        data:  total2las,
        pointPadding: 0.3,
        pointPlacement: -0.2,
        pointWidth: 20
    }, {
        name: 'Line 3 to day',
        color: 'rgba(186,60,61,.5)',
        data: total2,
        pointPadding: 0.4,
        pointPlacement: -0.2
    },
    {
        name: 'Line 4 yesterday',
        color: 'rgba(248,161,63,1)',
        data: total3las,
        pointPadding: 0.3,
        pointPlacement: 0,
        pointWidth: 20
    }, {
        name: 'Line 4 to day',
        color: 'rgba(248,161,63,.5)',
        data: total3,
        pointPadding: 0.4,
        pointPlacement: 0
    },
    {
        name: 'Line 5 yesterday',
        color: 'rgba(2, 125, 27,1)',
        data:  total4las,
        pointPadding: 0.3,
        pointPlacement: 0.2,
        pointWidth: 20
    }, {
        name: 'Line 5 to day',
        color: 'rgba(2, 125, 27,.5)',
        data: total4 ,
        pointPadding: 0.4,
        pointPlacement: 0.2
    },
    {
        name: 'Line 6 yesterday',
        color: 'rgba(0,0,0,1)',
        data:  total5las,
        pointPadding: 0.3,
        pointPlacement: 0.4,
        pointWidth: 20
    }, {
        name: 'Line 6 to day',
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