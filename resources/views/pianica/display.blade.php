@extends('layouts.master')
@section('stylesheets')
<style type="text/css">
  .kiri{
    text-align: center;
    font-size: 0.8vw;
    background-color: rgba(126,86,134,.7);
    color: white;  
    border-color: white
    margin:0;
  }
  .tengah{
    text-align: center;
    font-size: 1vw;
    background-color: #F0FFF0;
    border-color: white;
    margin:0;
  }
  .tengah2{
    text-align: center;
    font-size: 1vw;
    
    background-color: pink;
    border-color: white;
    margin:0;
  }
  .tengah3{
    text-align: center;
    font-size: 1vw;
    background-color: rgba(126,86,134,.7);
    border-color: white;
    margin:0;
  }

  .tengah4{
    text-align: center;
    font-size: 0.8vw;
    background-color: rgba(126,86,134,.7);
    border-color: white;
    margin:0;
  }
  .tengah5{
    text-align: center;
    font-size: 0.8vw;
    background-color: pink;
    border-color: white;
    margin:0;
  }
  .tengah6{
    text-align: center;
    font-size: 0.8vw;
    background-color: #F0FFF0;
    border-color: white;
    margin:0;
  }
  .gambar{
    border-color: white;
    margin:0;
  }
  .gambar2{
    text-align: center;
    font-size: 1vw;
    border-color: white; 
    margin:0;
    /*background-color: rgba(126,86,134,.7);*/
 /* border-top-color: white;
  border-bottom-color: white;
  border-left-color: red;
  border-right-color: red;*/
}
.judul{
  text-align: center;
  font-size: 1vw;
}
</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    {{ $page }}
   <span class="text-purple"> 生産結果</span>
  </h1>
  <ol class="breadcrumb">

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
  @php
  $bensuki = 'images/pianica/bensuki.png';
  $kakuning_visual = 'images/pianica/kakuning_visual.png';
  $kensa_akhir = 'images/pianica/kensa_akhir.png';
  $kensa_awal = 'images/pianica/kensa_awal.png';
  $no = 'images/pianica/no.png';
  $pureto = 'images/pianica/pureto.png';
  @endphp

  <div class="row">
    <div class="col-xs-12" >
      <div class="row"> 
        <div class="col-xs-12"> 
          <div class="box"> 
            <div class="box-body" >
              <table border="1" style="border-color: white;" id="tabelapp">
                <tr>    
                  <td class="gambar2"></td>           
                  <td colspan="2" class="gambar2">Bentsuki</td>
                  <td colspan="4"class="gambar2" >Pureto</td>
                  <td colspan="2"class="gambar2" >Kensa Awal</td>
                  <td colspan="2"class="gambar2" >Kensa Akhir</td>
                  <td colspan="2"class="gambar2" >Kakunin Visual</td>
                </tr>
                <tr> 
                  <td class="gambar2"></td> 
                  <td colspan="2" class="gambar"><img src="{{ url($bensuki) }}" width="100%" height="100"></td>
                  <td colspan="2"class="gambar" ><img src="{{ url($no) }}" width="100%" height="100"></td>
                  <td colspan="2"class="gambar" ><img src="{{ url($pureto) }}" width="100%" height="100"></td>
                  <td colspan="2"class="gambar" ><img src="{{ url($kensa_awal) }}" width="100%" height="100"></td>
                  <td colspan="2"class="gambar" ><img src="{{ url($kensa_akhir) }}" width="100%" height="100"></td>
                  <td colspan="2"class="gambar" ><img src="{{ url($kakuning_visual) }}" width="100%" height="100"></td>
                </tr>
                <tr> 
                  <td class="tengah" style="text-align: center; font-size: 1vw; background-color: rgba(126,86,134,.7); border-color: white;">LINE</td>
                  <td class="kiri" width="5%">TARGET</td>
                  <td class="tengah3" id="tbensukiok0">1</td> 
                  <td colspan="2"class="kiri" width="5%">TARGET</td>
                  <td colspan="2"class="tengah3" id="tpuretook0">0</td>
                  <td class="kiri" width="5%">TARGET</td>
                  <td class="tengah3" id="tawalok0">0</td> <td class="kiri" width="5%">TARGET</td>
                  <td class="tengah3"  id="takhirok0">0</td> <td class="kiri" width="5%">TARGET</td>
                  <td class="tengah3"  id="tvisualok0">0</td> 
                </tr> 
                <tr> 
                  <td class="tengah" rowspan="2" style="font-size: 4vw; line-height: 2px">1</td>
                  <td class="kiri">ACT</td> 
                  <td class="tengah" id="tbensukitot0">0</td> 
                  <td colspan="2" class="kiri">ACT</td>
                  <td colspan="2" class="tengah" id="tpuretotot0">0</td>
                  <td class="kiri">ACT</td> 
                  <td class="tengah" id="tawaltot0">0</td> 
                  <td class="kiri">ACT</td> 
                  <td class="tengah" id="takhirtot0">0</td> 
                  <td class="kiri">ACT</td> 
                  <td class="tengah" id="tvisualtot0">0</td> </tr> 
                  <tr> <td class="kiri">NG</td>
                    <td class="tengah2" id="tbensuking0" >1</td> 
                    <td colspan="2" bgcolor="black"></td>
                    <td colspan="2"  id="tpuretong0" bgcolor="black">0</td>
                    <td class="kiri">NG</td>
                    <td class="tengah2" id="tawalng0">0</td> 
                    <td class="kiri">NG</td>
                    <td class="tengah2" id="takhirng0">0</td> 
                    <td class="kiri">NG</td>
                    <td class="tengah2" id="tvisualng0">0</td> </tr>


                  </table>
                </div> 
              </div> 
            </div> 
          </div>    
          <!-- SEBELAH KANAN -->

        </div>        
          </div> 

          <div class="row">
            <div class="col-xs-12">
              
                <!-- SEBELAH KANAN -->
       
          <div class="box">
            <div class="box-body">
              <div class="col-xs-2 col-xs-offset-1">               
              
              <center>
                <B class="judul">ALL BENSUKI</B>
                <div id="container" style="  margin: 0;height: 200px"> 
                </div></center>
                <div>
                  <table width="100%" border="1" style="border-color:  white" >
                    <tr>
                      <td class="tengah6">TARGET</td>
                      <td class="tengah6">TOTAL</td>
                      <td class="tengah4">OK</td>
                      <td class="tengah5">NG</td>
                      <td class="tengah5">% NG</td>
                    </tr>
                    <tr>
                      <td class="tengah6" id="bensukitarget">133 (pc)</td>
                      <td class="tengah6" id="bensukitot">133 (pc)</td>
                      <td class="tengah4" id="bensukiok">111 (pc)</td>
                      <td class="tengah5" id="bensuking">22 (pc)</td>
                      <td class="tengah5" id="bensukipersen">15%</td>
                    </tr>
                  </table>
                </div>
                </div>
                 <div class="col-xs-2">
                <center>
                  <B class="judul">ALL PURETO</B>
                  <div id="container2" style="  margin: 0 auto;height: 200px"> 
                  </div>
                </center>
                <div>
                  <table width="100%" border="1" style="border-color:  white" >
                    <tr>
                      <td class="tengah6">TARGET</td>
                      <td class="tengah6">TOTAL</td>
                      <!-- <td class="tengah4">OK</td> -->
                      <!-- <td class="tengah5">NG</td> -->
                      <!-- <td class="tengah5">% NG</td> -->
                    </tr>
                    <tr>
                      <td class="tengah6" id="puretotarget">133 (pc)</td>
                      <td class="tengah6" id="puretotot">133 (pc)</td>
                      <!-- <td class="tengah4" id="puretook">111 (pc)</td> -->
                      <!-- <td class="tengah5" id="puretong">22 (pc)</td> -->
                      <!-- <td class="tengah5" id="puretopersen">15%</td> -->
                    </tr>
                  </table>
                </div>
              </div>
               <div class="col-xs-2">
                <center>
                  <B class="judul">ALL KENSA AWAL</B>
                  <div id="container3" style="  margin: 0 auto;height: 200px"> 
                  </div></center>
                  <div>
                    <table width="100%" border="1" style="border-color:  white" >
                      <tr>
                        <td class="tengah6">TARGET</td>
                        <td class="tengah6">TOTAL</td>
                        <td class="tengah4">OK</td>
                        <td class="tengah5">NG</td>
                        <td class="tengah5">% NG</td>
                      </tr>
                      <tr>
                        <td class="tengah6" id="awaltarget">133 (pc)</td>
                        <td class="tengah6" id="awaltot">133 (pc)</td>
                        <td class="tengah4" id="awalok">111 (pc)</td>
                        <td class="tengah5" id="awalng">22 (pc)</td>
                        <td class="tengah5" id="awalpersen">15%</td>
                      </tr>
                    </table>
                  </div>
                </div>
                 <div class="col-xs-2">
                  <center>
                    <B class="judul">ALL KENSA AKHIR</B>
                    <div id="container4" style="  margin: 0 auto;height: 200px"> 
                    </div>
                  </center>
                  <div>
                    <table width="100%" border="1" style="border-color:  white" >
                      <tr>
                        <td class="tengah6">TARGET</td>
                        <td class="tengah6">TOTAL</td>
                        <td class="tengah4">OK</td>
                        <td class="tengah5">NG</td>
                        <td class="tengah5">% NG</td>
                      </tr>
                      <tr>
                        <td class="tengah6" id="akhirtarget">133 (pc)</td>
                        <td class="tengah6" id="akhirtot">133 (pc)</td>
                        <td class="tengah4" id="akhirok">111 (pc)</td>
                        <td class="tengah5" id="akhirng">22 (pc)</td>
                        <td class="tengah5" id="akhirpersen">15%</td>
                      </tr>
                    </table>
                  </div>
                </div>
                 <div class="col-xs-2" >
                  <center>
                    <B class="judul">ALL KAKUNIN VISUAL</B>
                    <div id="container5" style="  margin: 0 auto; height: 200px"> 
                    </div>
                  </center>
                  <div>
                    <table width="100%" border="1" style="border-color:  white;" >
                      <tr>
                         <td class="tengah6">TARGET</td>
                        <td class="tengah6">TOTAL</td>
                        <td class="tengah4">OK</td>
                        <td class="tengah5">NG</td>
                        <td class="tengah5">% NG</td>
                      </tr>
                      <tr>
                        <td class="tengah6" id="visualtarget">133 (pc)</td>
                        <td class="tengah6" id="visualtot">133 (pc)</td>
                        <td class="tengah4" id="visualok">111 (pc)</td>
                        <td class="tengah5" id="visualng">22 (pc)</td>
                        <td class="tengah5" id="visualpersen">15%</td>
                      </tr>
                    </table>
                  </div>
                </div>

                <div class=" col-xs-offset-1"></div>


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
            $('body').toggleClass("sidebar-collapse");
            app();
            recall();



          });

          function recall() {
            getTarget()
            bensuki();
            pureto();
            awal();
            akhir();
            visual();
            getDataAllLinepureto()
            getDataAllLineawal();
            getDataAllLineakhir();
            getDataAllLinevisual();
            getDataAllLinebensuki()
            setTimeout(recall, 6000);
          }

          function getTarget() { 
           
            $.get('{{ url("index/getTarget") }}',  function(result, status, xhr){
              console.log(status);
              console.log(result);
              console.log(xhr);
              if(xhr.status == 200){
                if(result.status){
                  var targetall = 0; 
                  var targetallAsli = 0; 
                  for (var i = 0; i < result.target.length; i++) {
                    targetall += (result.target[i].plan)-(result.target[i].debt);
                    targetallAsli += (result.target[i].plan)-(result.target[i].debt);
                  }

               var target = targetall / 5;
               var targetallline = ((result.target[0].plan)-(result.target[0].debt));
               var dt = new Date();
              var time = dt.getHours() + ":" + dt.getMinutes();
              var s = time.split(':');
              var h = parseInt(s[0]) - 7 ;
              var hs = parseInt(s[1]) - 15 ;
              var minut = (h * 60) + hs;

              if (h >= 12 ) {
                minut - 60;
              }
              
              var targetMinut = Math.round(target/minut);

              var targetLine = (Math.round(target / 480))*minut;
              var targetall = ((Math.round(target / 480))*minut) * 5;

              if (targetall >= targetallAsli ) {
                  targetall = targetallAsli;
                  targetLine = target;

              }
              for (var i = 0; i < 5; i++) {
                $('#tbensukiok'+i).text(targetLine);
               $('#tpuretook'+i).text(targetLine);
               $('#tawalok'+i).text(targetLine);
               $('#takhirok'+i).text(targetLine);
               $('#tvisualok'+i).text(targetLine);
               $('#tpuretook'+i).text(targetLine);
              }
              
              $('#bensukitarget').text(targetall+" pcs");
              $('#puretotarget').text(targetall+" pcs");
               $('#awaltarget').text(targetall+" pcs");
               $('#akhirtarget').text(targetall+" pcs");
               $('#visualtarget').text(targetall+" pcs");
               $('#puretotarget').text(targetall+" pcs");
              // alert((Math.round(target / 480))*minut)
                // openSuccessGritter('Success!', result.message);
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


          function getDataAllLinebensuki() { 
           
            $.get('{{ url("index/GetNgBensuki") }}',  function(result, status, xhr){
              console.log(status);
              console.log(result);
              console.log(xhr);
              if(xhr.status == 200){
                if(result.status){
                  for (var i = 0; i < 5; i++) {
                    
                    // $('#tpuretook'+i).text((result.total[i].total - result.total[i].ng));
                    $('#tbensuking'+i).text((result.total[i].total));
                  }
                // openSuccessGritter('Success!', result.message);
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


          function getDataAllLinepureto() { 
            var data ={
              location:'PN_Pureto'                     
            } 
            $.get('{{ url("index/TotalNgAllLine") }}', data, function(result, status, xhr){
              console.log(status);
              console.log(result);
              console.log(xhr);
              if(xhr.status == 200){
                if(result.status){
                  for (var i = 0; i < 5; i++) {
                    $('#tpuretotot'+i).text(result.total[i].total);
                    $('#tbensukitot'+i).text(result.total[i].total);
                    // $('#tpuretook'+i).text((result.total[i].total - result.total[i].ng));
                    // $('#tpuretong'+i).text((result.total[i].ng));
                    $('#tpuretong'+i).text('');

                    // $('#tpuretong').css({"bgcolor":"black"});


                  }
                // openSuccessGritter('Success!', result.message);
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

          function getDataAllLineawal() { 
            var data ={
              location:'PN_Kensa_Awal'                     
            } 
            $.get('{{ url("index/TotalNgAllLine") }}', data, function(result, status, xhr){
              console.log(status);
              console.log(result);
              console.log(xhr);
              if(xhr.status == 200){
                if(result.status){
                  for (var i = 0; i < 5; i++) {
                    $('#tawaltot'+i).text(result.total[i].total);
                    // $('#tawalok'+i).text((result.total[i].total - result.total[i].ng));
                    $('#tawalng'+i).text((result.total[i].ng));
                  }
                // openSuccessGritter('Success!', result.message);
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

          function getDataAllLineakhir() { 
            var data ={
              location:'PN_Kensa_Akhir'                     
            } 
            $.get('{{ url("index/TotalNgAllLine") }}', data, function(result, status, xhr){
              console.log(status);
              console.log(result);
              console.log(xhr);
              if(xhr.status == 200){
                if(result.status){
                  for (var i = 0; i < 5; i++) {
                    $('#takhirtot'+i).text(result.total[i].total);
                    // $('#takhirok'+i).text((result.total[i].total - result.total[i].ng));
                    $('#takhirng'+i).text((result.total[i].ng));
                  }
                // openSuccessGritter('Success!', result.message);
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

          function getDataAllLinevisual() { 
            var data ={
              location:'PN_Kakuning_Visual'                     
            } 
            $.get('{{ url("index/TotalNgAllLine") }}', data, function(result, status, xhr){
              console.log(status);
              console.log(result);
              console.log(xhr);
              if(xhr.status == 200){
                if(result.status){
                  for (var i = 0; i < 5; i++) {
                    $('#tvisualtot'+i).text(result.total[i].total);
                    // $('#tvisualok'+i).text((result.total[i].total - result.total[i].ng));
                    $('#tvisualng'+i).text((result.total[i].ng));
                  }
                // openSuccessGritter('Success!', result.message);
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


          function bensuki() {
            $.get('{{ url("index/GetNgBensukiAll") }}', function(result, status, xhr){
              console.log(status);
              console.log(result);
              console.log(xhr);
              if(xhr.status == 200){
                if(result.status){
                  $('#bensukitot').text(result.totalAll[0].total+" pcs");
                  $('#bensukiok').text((result.totalAll[0].total - result.total[0].total)+" pcs");
                  $('#bensuking').text((result.total[0].total)+" pcs");
                  $('#bensukipersen').text(Math.round((result.total[0].total / result.totalAll[0].total)*100)/100+"%");       
            Highcharts.chart('container', {
              chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie',
                options3d: {
                  enabled: true,
                  alpha: 45,
                  beta: 0,
                  maintainAspectRatio: false
                }
              },
              title: {
                text: ''
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
                      fontSize: '0.8vw'
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
                  name: 'OK',
                  y: result.totalAll[0].total - result.total[0].total,
                  color: 'rgba(126,86,134,.7)'
                }, {
                  name: 'NG',
                  y: result.total[0].total,
                  color: 'PINK'
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

          function pureto() {
            var target = $('#puretotarget').text();
            var data ={
              location:'PN_Pureto'

            }
            $.get('{{ url("index/TotalNgAll") }}', data, function(result, status, xhr){
              console.log(status);
              console.log(result);
              console.log(xhr);
              if(xhr.status == 200){
                if(result.status){
                  $('#puretotot').text(result.total[0].total+" pcs");
                  $('#puretook').text((result.total[0].total - result.total[0].ng)+" pcs");
                  // $('#puretong').text((result.total[0].ng)+" pcs");
                  // $('#puretopersen').text(Math.round((result.total[0].ng / result.total[0].total)*100)/100+"%");  
                  $('#puretong').text('-');
                  $('#puretopersen').text('-');      
                // openSuccessGritter('Success!', result.message);
                // Highcharts.chart('container2', {
                //   chart: {
                //     plotBackgroundColor: null,
                //     plotBorderWidth: null,
                //     plotShadow: false,
                //     type: 'pie',
                //     options3d: {
                //       enabled: true,
                //       alpha: 45,
                //       beta: 0,
                //       maintainAspectRatio: false
                //     }
                //   },
                //   title: {
                //     text: ''
                //   },
                //   tooltip: {
                //     pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                //   },
                //   plotOptions: {
                //     pie: {
                //       allowPointSelect: true,
                //       cursor: 'pointer',
                //       depth: 35,
                //       dataLabels: {
                //         enabled: true,
                //         format: '<b>{point.name}</b><br>{point.percentage:.1f} %',
                //         style: {
                //       fontSize: '0.8vw'
                //     },
                //         distance: -50,
                //         filter: {
                //           property: 'percentage',
                //           operator: '>',
                //           value: 4
                //         },
                        
                //       },
                //       showInLegend: false
                //     }
                //   },
                //   credits:{
                //     enabled:false,
                //   },
                //   exporting:{
                //     enabled:false,
                //   },
                //   series: [{

                //     animation: false,
                //     name: 'Percentage',
                //     colorByPoint: true,
                //     data: [{
                //       name: 'OK',
                //       y: parseInt(result.total[0].total - result.total[0].ng),
                //       color: 'rgba(126,86,134,.7)'
                //     }, {
                //       name: 'NG',
                //       y: parseInt(result.total[0].ng),
                //       color: 'PINK'
                //     }]
                //   }]
                // });

                 Highcharts.chart('container2', {
    chart: {
        type: 'column'
    },
    
     title: {
        text: ''
    },
    xAxis: {
        categories: ['Production'],
    },
    yAxis: {
        min: 0,
        title: {
            text: ''
        }
    },

     exporting:{
                enabled:false,
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
        name: 'Target',
        color: 'rgba(165,170,217,1)',
        data: [parseInt(target)],
        pointPadding: 0.4,
        pointPlacement: -0.1
    }, {
      animation: false,
        name: 'Total',
        color: 'rgba(126,86,134,.9)',
        data: [parseInt(result.total[0].total)],
        pointPadding: 0.4,
        pointPlacement: 0.1
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

          function awal() {
            var data ={
              location:'PN_Kensa_Awal'

            }
            $.get('{{ url("index/TotalNgAll") }}', data, function(result, status, xhr){
              console.log(status);
              console.log(result);
              console.log(xhr);
              if(xhr.status == 200){
                if(result.status){
                  $('#awaltot').text(result.total[0].total+" pcs");
                  $('#awalok').text((result.total[0].total - result.total[0].ng)+" pcs");
                  $('#awalng').text((result.total[0].ng)+" pcs");
                  $('#awalpersen').text(Math.round((result.total[0].ng / result.total[0].total)*100)/100+"%");       
                // openSuccessGritter('Success!', result.message);

                Highcharts.chart('container3', {
                  chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie',
                    options3d: {
                      enabled: true,
                      alpha: 45,
                      beta: 0,
                      maintainAspectRatio: false
                    }
                  },
                  title: {
                    text: ''
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
                      fontSize: '0.8vw'
                    },
                        distance: -50,
                        filter: {
                          property: 'percentage',
                          operator: '>',
                          value: 4
                        },
                        
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
                      name: 'OK',
                      y: parseInt(result.total[0].total - result.total[0].ng),
                      color: 'rgba(126,86,134,.7)'
                    }, {
                      name: 'NG',
                      y: parseInt(result.total[0].ng),
                      color: 'PINK'
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

          function akhir() {
            var data ={
              location:'PN_Kensa_Akhir'

            }
            $.get('{{ url("index/TotalNgAll") }}', data, function(result, status, xhr){
              console.log(status);
              console.log(result);
              console.log(xhr);
              if(xhr.status == 200){
                if(result.status){
                  $('#akhirtot').text(result.total[0].total+" pcs");
                  $('#akhirok').text((result.total[0].total - result.total[0].ng)+" pcs");
                  $('#akhirng').text((result.total[0].ng)+" pcs");
                  $('#akhirpersen').text(Math.round((result.total[0].ng / result.total[0].total)*100)/100+"%");       
                // openSuccessGritter('Success!', result.message);
                Highcharts.chart('container4', {
                  chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie',
                    options3d: {
                      enabled: true,
                      alpha: 45,
                      beta: 0,
                      maintainAspectRatio: false
                    }
                  },
                  title: {
                    text: ''
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
                      fontSize: '0.8vw'
                    },
                        distance: -50,
                        filter: {
                          property: 'percentage',
                          operator: '>',
                          value: 4
                        },
                        
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
                      name: 'OK',
                      y: parseInt(result.total[0].total - result.total[0].ng),
                      color: 'rgba(126,86,134,.7)'
                    }, {
                      name: 'NG',
                      y: parseInt(result.total[0].ng),
                      color: 'PINK'
                    }]
                  }]
                });}
                else{                
                // openErrorGritter('Error!', result.message);
              }
            }
            else{

              alert("Disconnected from server");
            }
          });
          }

          function visual() {

            var data ={
              location:'PN_Kakuning_Visual'

            }
            $.get('{{ url("index/TotalNgAll") }}', data, function(result, status, xhr){
              console.log(status);
              console.log(result);
              console.log(xhr);
              if(xhr.status == 200){
                if(result.status){
                  $('#visualtot').text(result.total[0].total+" pcs");
                  $('#visualok').text((result.total[0].total - result.total[0].ng)+" pcs");
                  $('#visualng').text((result.total[0].ng)+" pcs");
                  $('#visualpersen').text(Math.round((result.total[0].ng / result.total[0].total)*100)/100+"%");       
                // openSuccessGritter('Success!', result.message);
                Highcharts.chart('container5', {
                  chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie',
                    options3d: {
                      enabled: true,
                      alpha: 45,
                      beta: 0,
                      maintainAspectRatio: false
                    }
                  },
                  title: {
                    text: ''
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
                      fontSize: '0.8vw'
                    },
                        distance: -50,
                        filter: {
                          property: 'percentage',
                          operator: '>',
                          value: 4
                        },
                        
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
                      name: 'OK',
                      y: parseInt(result.total[0].total - result.total[0].ng),
                      color: 'rgba(126,86,134,.7)'
                    }, {
                      name: 'NG',
                      y: parseInt(result.total[0].ng),
                      color: 'PINK'
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



          function app() {
            for (var i = 1; i < 5; i++) {     

              $("#tabelapp").append('<tr><td colspan="3" class="gambar2" style="color: white"></td><td colspan="4"class="gambar2" style="color: white"></td><td colspan="2"class="gambar2" style="color: white"></td> <td colspan="2"class="gambar2" style="color: white"></td><td colspan="2"class="gambar2" style="color: white"></td></tr><tr> <td class="tengah" style="text-align: center; font-size: 1vw; background-color: rgba(126,86,134,.7); border-color: white;">LINE</td><td class="kiri" width="5%">TARGET</td><td class="tengah3" id="tbensukiok'+i+'">1</td> <td colspan="2"class="kiri" width="5%">TARGET</td><td colspan="2"class="tengah3" id="tpuretook'+i+'">0</td><td class="kiri" width="5%">TARGET</td><td class="tengah3" id="tawalok'+i+'">0</td> <td class="kiri" width="5%">TARGET</td><td class="tengah3" id="takhirok'+i+'">0</td> <td class="kiri" width="5%">TARGET</td><td class="tengah3" id="tvisualok'+i+'">0</td> </tr> <tr> <td class="tengah" rowspan="2" style="font-size: 4vw; line-height: 2px" id="lineno'+i+'">'+i+'</td><td class="kiri">ACT</td> <td class="tengah" id="tbensukitot'+i+'">0</td> <td colspan="2" class="kiri">ACT</td><td colspan="2" class="tengah" id="tpuretotot'+i+'">0</td><td class="kiri">ACT</td> <td class="tengah" id="tawaltot'+i+'">0</td> <td class="kiri">ACT</td> <td class="tengah" id="takhirtot'+i+'">0</td> <td class="kiri">ACT</td> <td class="tengah" id="tvisualtot'+i+'">0</td> </tr> <tr> <td class="kiri">NG</td><td class="tengah2" id="tbensuking'+i+'">1</td> <td bgcolor="black" colspan="2" ></td><td colspan="2"  id="tpuretong'+i+'" bgcolor="black">0</td><td class="kiri">NG</td><td class="tengah2" id="tawalng'+i+'">0</td> <td class="kiri">NG</td><td class="tengah2" id="takhirng'+i+'">0</td> <td class="kiri">NG</td><td class="tengah2" id="tvisualng'+i+'">0</td> </tr>    ');
            }
            for (var i = 0; i < 5; i++) {
              $('#lineno'+i).text(i+1)
            }

          }

        </script>

        @stop