@extends('layouts.display')
@section('stylesheets')
<link href="<?php echo e(url("css/jquery.gritter.css")); ?>" rel="stylesheet">
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">
<style type="text/css">
  #bungkustext
  {   width:100%; margin: auto; padding: 10px 20px;
      text-align:center;background-Color:#cf3846; color:#ffff00; font-size:30px; 
  }

  thead input {
    width: 100%;
    padding: 3px;
    box-sizing: border-box;
  }
  thead>tr>th{
    font-size: 16px;
  }
  #tableBodyResume > tr:hover {
    cursor: pointer;
    background-color: #cf3846;
  }

  #receivedBodyResume > tr:hover {
    cursor: pointer;
    background-color: #33FF66;
  }


  table.table-bordered > thead > tr > th{
    border:1px solid rgb(54, 59, 56);
    text-align: center;
    background-color: #ff7f50;  
    color:black;
  }
  table.table-bordered > tbody > tr > td{
    border-collapse: collapse !important;
    border:1px solid rgb(54, 59, 56);
    /*background-color: #ffffff;*/
    color: black;
    vertical-align: middle;
    text-align: center;
    padding:3px;
  }

  table.table-bordered2 > thead > tr > th{
    border:1px solid rgb(54, 59, 56);
    text-align: center;
    background-color: #9ada8e;  
    color:black;
  }
  table.table-bordered2 > tbody > tr > td{
    border-collapse: collapse !important;
    border:1px solid rgb(54, 59, 56);
    /*background-color: #ffffff;*/
    color: black;
    vertical-align: middle;
    text-align: center;
    padding:3px;
  }

  input::-webkit-outer-spin-button,
  input::-webkit-inner-spin-button {
    /* display: none; <- Crashes Chrome on hover */
    -webkit-appearance: none;
    margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
  }

  input[type=number] {
    -moz-appearance:textfield; /* Firefox */
  }

  .nmpd-grid {border: none; padding: 20px;}
  .nmpd-grid>tbody>tr>td {border: none;}
  
  #loading { display: none; }

  .kedip {
      width: 50px;
      height: 50px;
      -webkit-animation: kedip 1s infinite;  /* Safari 4+ */
      -moz-animation: kedip 1s infinite;  /* Fx 5+ */
      -o-animation: kedip 1s infinite;  /* Opera 12+ */
      animation: kedip 1s infinite;  /* IE 10+, Fx 29+ */
    }

    @-webkit-keyframes kedip {
      0%, 49% {
        /*visibility: hidden;*/
        color: #ffff00;
        font-size: 18px;
        font-weight: bold;
      }
      50%, 100% {
        color: rgba(0,0,0,1);
        font-size: 18px;
        font-weight: bold;
        /*visibility: visible;*/
      }
    }

</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('header'); ?>
<section class="content-header">
  <h1>
    <?php echo e($title); ?>
    <span style="font-size: 23px"> Tanggal : <?php echo e(date('d-m-Y')); ?></span>

    <!-- <small><span class="text-purple"> <?php echo e($title_jp); ?></span></small> -->
  </h1>
</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<section class="content" style="padding-top: 0; padding-bottom: 0">
  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 45%; left: 35%;">
      <span style="font-size: 40px">Sedang memproses, tunggu sebentar <i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>
  <input type="hidden" id="location">
  <div class="row">
    <div class="col-md-12">
        <div id="bungkustext">
          <div id="textkedip">Monitoring Scrap WIP</div>
        </div>
        <div class="col-md-5" style="margin-top: 20px; padding:20px !important">
            <div id="chart" style="width: 100%; height: 770px"></div>
        </div>
        <div class="col-xs-7"  style="margin-top: 20px; padding:20px !important">
          <div class="box" style="background-color: #c0d8c7"> 
            <div class="box-body">
              <table class="table table-bordered2" id="receivedResume">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Slip Number</th>
                    <th>Part's Name</th>
                    <!-- <th>Receive Location</th>
                    <th>Location</th>
                    <th>Category</th>
                    <th>Qty</th> -->
                    <th>Creator</th>
                    <th>Created</th>
                    <th>Status</th>
                    <!-- <th>Delete</th> -->
                  </tr>
                </thead>
                <tbody id="receivedBodyResume">
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="col-xs-7"  style="margin-top: 20px; padding:20px !important">
          <div class="box" style="background-color: #ffdfd1">
            <div class="box-body">
              <table class="table table-bordered" id="tableResume">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Slip Number</th>
                    <th>Part's Name</th>
                    <!-- <th>Receive Location</th> -->
                    <!-- <th>Location</th>
                    <th>Category</th>
                    <th>Qty</th> -->
                    <th>Creator</th>
                    <th>Created</th>
                    <th>Status</th>
                    <!-- <th>Delete</th> -->
                  </tr>
                </thead>
                <tbody id="tableBodyResume">
                </tbody>
              </table>
            </div>
          </div>
        </div>
    </div>
  </div>
</section>

<div class="modal fade" id="modalLocation">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <center><h3 style="background-color: #00a65a;">Pilih Lokasi Anda</h3></center>
        <div class="modal-body table-responsive no-padding">
          <div class="form-group">
            <select class="form-control select2" onchange="fetchResume(value)" data-placeholder="Pilih Lokasi Anda..." style="width: 100%; font-size: 20px;">
              <option></option>
              @foreach($storage_locations as $storage_location)
              <option value="{{ $storage_location }}">{{ $storage_location }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script src="<?php echo e(url("js/jquery.gritter.min.js")); ?>"></script>
<script src="<?php echo e(url("js/dataTables.buttons.min.js")); ?>"></script>
<script src="<?php echo e(url("js/buttons.flash.min.js")); ?>"></script>
<script src="<?php echo e(url("js/jszip.min.js")); ?>"></script>
<script src="<?php echo e(url("js/vfs_fonts.js")); ?>"></script>
<script src="<?php echo e(url("js/buttons.html5.min.js")); ?>"></script>
<script src="<?php echo e(url("js/buttons.print.min.js")); ?>"></script>
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script src="<?php echo e(url("js/jsQR.js")); ?>"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>

<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
  $.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
  $.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
  $.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
  $.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
  $.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");
    
    $('.select2').select2();
    $('#modalLocation').modal({
      backdrop: 'static',
      keyboard: false
    });
    $('.numpad').numpad({
      hidePlusMinusButton : true,
      decimalSeparator : '.'
    });

    var kedipan = 300; 
    var dumet = setInterval(function () {
        var ele = document.getElementById('textkedip');
        ele.style.visibility = (ele.style.visibility == 'hidden' ? '' : 'hidden');
    }, kedipan);

    setInterval(function(){
        fetchResume($('#location').val());
        receivedResume($('#location').val());
        fetchScrapList($('#location').val());
      }, 15000);
    
  });


  function plusCount(){
    $('#quantity').val(parseInt($('#quantity').val())+1);
  }

  function minusCount(){
    $('#quantity').val(parseInt($('#quantity').val())-1);
  }


  function fetchResume(loc){
    $('#location').val(loc);
    receivedResume($('#location').val());
    fetchScrapList($('#location').val());
    var data = {
      loc:loc
    }
    $.get('<?php echo e(url("scrap/resume/list/wip")); ?>', data, function(result, status, xhr){
      if(result.status){
        $('#tableResume').DataTable().clear();
        $('#tableResume').DataTable().destroy();
        var tableData = '';
        $('#tableBodyResume').html("");
        $('#tableBodyResume').empty();
        
        var count = 1;
        $.each(result.resumes, function(key, value) {
          tableData += '<tr>';
          tableData += '<td>'+ count +'</td>';
          tableData += '<td>'+ value.slip +'-SC</td>';
          tableData += '<td>'+ value.material_description +'</td>';
          // tableData += '<td>'+ value.issue_location +'</td>';
          // tableData += '<td>'+ value.receive_location +'</td>';
          // tableData += '<td>'+ value.category +'</td>';
          // tableData += '<td>'+ value.quantity +'</td>';
          tableData += '<td>'+ value.name +'</td>';
          tableData += '<td>'+ value.tanggal +'</td>';
          tableData += '<td class="kedip" style="background-color: #cf3846;">'+ 'Dikirim Ke Gudang' +'</td>';
          // tableData += '<td><center><button class="btn btn-danger" onclick="deleteScrap('+value.id+')"><i class="fa fa-trash"></i></button></center></td>';
          tableData += '</tr>';
          count += 1;
        });

        $('#tableBodyResume').append(tableData);
        var tableResume = $('#tableResume').DataTable({
          'dom': 'Bfrtip',
          'responsive':true,
          'lengthMenu': [
          [ 10, 25, 50, -1 ],
          [ '10 rows', '25 rows', '50 rows', 'Show all' ]
          ],
          'buttons': {
            buttons:[
            {
              extend: 'pageLength',
              className: 'btn btn-default',
            }
            ]
          },
          'paging': true,
          'lengthChange': true,
          'pageLength': 3,
          'searching': true,
          'ordering': true,
          'order': [],
          'info': true,
          'autoWidth': true,
          "sPaginationType": "full_numbers",
          "bJQueryUI": true,
          "bAutoWidth": false,
          "processing": true
        });

        // openSuccessGritter('Success!', result.message);
        $('#modalLocation').modal('hide');
      }
      else{
        openErrorGritter('Error!', result.message);
      }
    });
  }

  function receivedResume(loc){
    $('#location').val(loc);
    var data = {
      loc:loc
    }
    $.get('<?php echo e(url("scrap/resume/list/wh")); ?>', data, function(result, status, xhr){
      if(result.status){
        $('#receivedResume').DataTable().clear();
        $('#receivedResume').DataTable().destroy();
        var tableData = '';
        $('#receivedBodyResume').html("");
        $('#receivedBodyResume').empty();
        
        var count = 1;
        $.each(result.resumes, function(key, value) {
          tableData += '<tr>';
          tableData += '<td>'+ count +'</td>';
          tableData += '<td>'+ value.slip +'-SC</td>';
          tableData += '<td>'+ value.material_description +'</td>';
          // tableData += '<td>'+ value.issue_location +'</td>';
          // tableData += '<td>'+ value.receive_location +'</td>';
          // tableData += '<td>'+ value.category +'</td>';
          // tableData += '<td>'+ value.quantity +'</td>';
          tableData += '<td>'+ value.name +'</td>';
          tableData += '<td>'+ value.tanggal +'</td>';
          tableData += '<td class="kedip" style="background-color: #33FF66">'+ 'Diterima Gudang' +'</td>';
          // tableData += '<td><center><button class="btn btn-danger" onclick="deleteScrap('+value.id+')"><i class="fa fa-trash"></i></button></center></td>';
          tableData += '</tr>';
          count += 1;
        });

        $('#receivedBodyResume').append(tableData);
        var receivedResume = $('#receivedResume').DataTable({
          'dom': 'Bfrtip',
          'responsive':true,
          'lengthMenu': [
          [ 10, 25, 50, -1 ],
          [ '10 rows', '25 rows', '50 rows', 'Show all' ]
          ],
          'buttons': {
            buttons:[
            {
              extend: 'pageLength',
              className: 'btn btn-default',
            }
            ]
          },
          'paging': true,
          'lengthChange': true,
          'pageLength': 3,
          'searching': true,
          'ordering': true,
          'order': [],
          'info': true,
          'autoWidth': true,
          "sPaginationType": "full_numbers",
          "bJQueryUI": true,
          "bAutoWidth": false,
          "processing": true
        });

        // openSuccessGritter('Success!', result.message);
        $('#modalLocation').modal('hide');
      }
      else{
        openErrorGritter('Error!', result.message);
      }
    });
  }

  
  function deleteScrap(id){

    if(confirm("Apa anda yakin mendelete slip scrap?")){
      var data = {
        id:id
      }
      $.post('{{ url("delete/scrap") }}', data, function(result, status, xhr){
        if(result.status){
          var loc = $('#location').val(); 

          fetchResume(loc);
          openSuccessGritter('Success!', result.message);
          // console.log(result);
        }
        else{
          openErrorGritter('Error!', result.message);
        }

      });
    }
    else{
      return false;
    }
  }

  function openSuccessGritter(title, message){
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-success',
      image: '<?php echo e(url("images/image-screen.png")); ?>',
      sticky: false,
      time: '2000'
    });
  }

  function openErrorGritter(title, message) {
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-danger',
      image: '<?php echo e(url("images/image-stop.png")); ?>',
      sticky: false,
      time: '2000'
    });
  }

  function fetchScrapList(loc){
    $('#location').val(loc);
    var data = {
      loc:loc
    }
    $.get('<?php echo e(url("scrap/data/monitoring/wip")); ?>', data, function(result, status, xhr){
      if(result.status){

        var jml = [], dept = [], jml_dept = [], list = [], receive = [];
              var category = [];

          $.each(result.datas, function(key, value) {
            category.push(value.issue_location);
            list.push(parseInt(value.LScrap));
            receive.push(parseInt(value.RScrap));
          });

          var today = new Date();
          var dd = String(today.getDate()).padStart(2, '0');
          var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
          var yyyy = today.getFullYear();

          today = dd + '-' + mm + '-' + yyyy;


          $('#chart').highcharts({

            chart: {
            type: 'column'
            },

            title: {
                text: today
            },
            xAxis: {
                categories: category
            },

            credits: {
                enabled: false
            },

            yAxis: {
                allowDecimals: false,
                min: 0,
                title: {
                    text: 'Total Scrap'
                }
            },

            plotOptions: {
              series: {
              cursor: 'pointer',
              dataLabels: {
                enabled: true,
                format: '{point.y}',
                style: {
                    fontSize: '22px'   
                  }
              }
            },
                // column: {
                //     stacking: 'normal'
                // }
            },

            series: [{
                name: 'Diterima WH',
                data: receive,
                borderWidth : 2,
                dataLabels: {
                  style: {
                      fontSize: '22px'   
                  }
                }
            }, {
                name: 'Belum Diterima WH',  
                data: list,
                borderWidth : 2,
                dataLabels: {
                  style: {
                      fontSize: '22px'   
                  }
                }
            }]
          })
        // openSuccessGritter('Success!', result.message);
        $('#modalLocation').modal('hide');
      }
      else{
        openErrorGritter('Error!', result.message);
      }
    });
  }
  Highcharts.createElement('link', {
          href: '{{ url("fonts/UnicaOne.css")}}',
          rel: 'stylesheet',
          type: 'text/css'
        }, null, document.getElementsByTagName('head')[0]);

        Highcharts.theme = {
          colors: ['#33FF66', '#cf3846', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
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
@stop