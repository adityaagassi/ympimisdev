@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

  .morecontent span {
   display: none;
 }
 .morelink {
   display: block;
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
 }
 table.table-bordered > tbody > tr > td{
   border:1px solid black;
   vertical-align: middle;
   padding:0;
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
 #queueTable.dataTable {
   margin-top: 0px!important;
 }
 #loading, #error { display: none; }

 #parent { 
   position: relative; 
   width: 720px; 
   height:500px;
   margin-right: auto;
   margin-left: auto; 
   border: solid 0px red; 
   font-size: 24px; 
   text-align: center; 
 }

 #spotWelding { 
   position: absolute; 
   right: 46px; 
   top: 250px; 
   width: 180px;
   height: 400px; 
   border: solid 0px red; 
   font-size: 24px; 
   text-align: center; 
 }

 #benkuri { 
   position: absolute; 
   right: 250px; 
   top: 90px; 
   width: 200px;
   height: 162px; 
   border: solid 0px red; 
   font-size: 24px; 
   text-align: center; 
 }

 #bennuki { 
   position: absolute; 
   right: 240px; 
   top: 330px; 
   width: 170px;
   height: 122px; 
   border: solid 0px red; 
   font-size: 24px; 
   text-align: center; 
 }

 #pressReedplate { 
   position: absolute; 
   right: 420px; 
   top: 330px; 
   width: 250px;
   height: 122px; 
   border: solid 0px red; 
   font-size: 24px; 
   text-align: center; 
 }

 #spotWelding > div, 
 #benkuri > div,
 #bennuki > div,
 #pressReedplate > div {
  border-radius: 20%;
}

.square {
  opacity: 0.8;
}


</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-left: 0px; padding-right: 0px;">
	<div class="row">
    <div class="col-md-6">
     <div class="col-md-12">
      <div class="box box-solid">
       <div class="box-body">
        <div class="col-md-12" style="height: 600px;">
         <h2><center><a style="font-size: 30px; font-weight: bold;" class="text-red"> Smart Tracking Operator Reedplate</a></center></h2>
         <h3><center><a style="font-size: 20px; font-weight: bold;" class="text-yellow">リードプレート作業者の位置把握スマートシステム</a></center></h3>
         <div id="parent" style="">
          <img src="{{ url("images/maps_reedplate.png") }}" width="700">
          <div id="spotWelding" class="square"></div>
          <div id="benkuri" class="square"></div>
          <div id="bennuki" class="square"></div>
          <div id="pressReedplate" class="square"></div>
          <div id="ind" class="square"></div>
          <div id="la" class="square"></div>
          <div id="sc" class="square"></div>
        </div>
      </div>
    </div>
  </div>
</div>
<div></div>
</div>
<div class="col-md-6">
  <div class="col-md-12">
    <div class="box box-solid">
      <div class="box-body">
        <div class="col-md-12" style="height: 600px;">
          <h2><center><a style="font-size: 30px; font-weight: bold;" class="text-Lime"> Working Time / Day</a></center></h2>
          <h3><center><a style="font-size: 20px; font-weight: bold;" class="text-yellow">日次作業時間</a></center></h3>
          <figure class="highcharts-figure">
            <div id="container">

            </div>
          </figure>
        </div>
      </div>
    </div>
  </div>
  <div></div>
</div>

</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>


<script>
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

  var arr_user = [];
  var stat = 0;

  jQuery(document).ready(function() {
    call_data_user();
    setInterval(call_data, 2000);
    drawChart();
    setInterval(drawChart, 10000);
  })

  function call_data_user() {
    $.get('{{ url("fetch/reedplate/user") }}', function(result, status, xhr){
      if (result.status) {
        arr_user = result.data;
        stat = 1;
      }

    })
  }

  function call_data() {
    if (stat == 1) {
      $.ajax({
        url: 'http://172.17.128.87:82/reader/data',
        type: 'GET',
        data: {_token: CSRF_TOKEN},
        success: function (data) {
          var color = 'black';
          var address = '';
          var name = '';
          for (i = 0; i < data.length; i++) {
            if (data[i].distance > 0 ) {

              $.each(arr_user, function(index2, value2){
                if (data[i].major == value2.major && data[i].minor == value2.minor) {
                  name = value2.kode;
                }
              })

              if (data[i].major == '111' && data[i].minor == '1903') {
                color = 'salmon';

              } else if (data[i].major == '111' && data[i].minor == '1905') {
                color = 'green';

              }
              else if (data[i].major == '111' && data[i].minor == '1901') {
                color = 'red';
              }

              else if (data[i].major == '111' && data[i].minor == '1900') {
                color = 'aqua';
              }

              else if (data[i].major == '111' && data[i].minor == '1902') {
                color = 'maroon';
              }

              else if (data[i].major == '111' && data[i].minor == '1906') {
                color = 'fuchsia';
              }

              else if (data[i].major == '111' && data[i].minor == '1907') {
                color = 'olive';
              }

              else if (data[i].major == '111' && data[i].minor == '1908') {
                color = 'teal';
              }

              else if (data[i].major == '111' && data[i].minor == '1909') {
                color = 'purple';
              }

              else if (data[i].major == '111' && data[i].minor == '1904') {
                color = 'silver';
              }


//Reader//------------
address = data[i].major+"_"+data[i].minor; 
if (data[i].reader == '4c66d0') {
  $( "."+address ).remove();
  $("#spotWelding").append('<div style="background-color: '+color+';width: 20px; height: 20px; display:inline-block; font-size:12px; color:black" class="'+address+'">'+name+'</div>');
} else if(data[i].reader == '4c67db') 
{
  $( "."+address ).remove();
  $("#benkuri").append('<div style="background-color: '+color+';width: 20px; height: 20px; display:inline-block; font-size:12px; color:black" class="'+address+'">'+name+'</div>');
}
name = ' ';
}
}
}
});
    }
  }

  function drawChart() {
    var week_date = $('#week_date').val();
    
    var data = {
      week_date: week_date
    };
    $.get('{{ url("fetch/reedplate/log") }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){
          var month = result.monthTitle;
          var name = [];
          var jam_kerja = [];
          var all_datas = [];
          var z = 0;
          var d = [];      
          
          for (var i = 0; i < result.data.length; i++) {
            if (typeof result.data[i+1] == 'undefined'){
              all_datas.push({name: result.data[i].lokasi+'   '+'('+'Reader :'+result.data[i].reader+')', data: d});
            }
            else {
              if(result.data[i].lokasi != result.data[i+1].lokasi){
                d.push(parseFloat(result.data[i].jam_kerja));
                all_datas.push({name: result.data[i].lokasi+'   '+'('+'Reader :'+result.data[i].reader+')', data: d});
                d = [];
              } else {
                d.push(parseFloat(result.data[i].jam_kerja));
              }
            } if(jQuery.inArray(result.data[i].name, name) != -1) {

            } else {
              name.push(result.data[i].name+'   '+'('+result.data[i].kode+')');
            }
          }


          // $.each(result.data, function(key, value) {
          //   if (typeof result.data[key+1] == 'undefined') {
          //     all_datas.push({name: value.lokasi+'   '+'('+'Reader :'+value.reader+')', data: d});
          
          //   } else {
          //     if (result.data[key].lokasi != result.data[key+1].lokasi) {
          //       d.push(parseFloat(value.jam_kerja));
          //       all_datas.push({name: value.lokasi+'   '+'('+'Reader :'+value.reader+')', data: d});
          //       d = [];
          //     } else {
          //       d.push(parseFloat(value.jam_kerja));
          //     }
          //   }

          //   if(jQuery.inArray(value.name, name) != -1) {

          //   } else {
          //     name.push(value.name+'   '+'('+value.kode+')');
          //   }
          // })

          console.table(all_datas);

          Highcharts.chart('container', {
            chart: {
              type: 'bar'
            },
            title: {
              text: 'Operator Reedplate'
            },
            xAxis: {
              categories: name
            },
            yAxis: {
              min: 0,
              title: {
                text: 'Minute'
              }
            },
            legend: {
              reversed: true
            },
            plotOptions: {
              series: {
                stacking: 'normal'
              }
            },
            series: all_datas
          });
        } else{
          alert('Attempt to retrieve data failed');
        }
      }
    })
  }

</script>



@endsection