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
   <span class="text-purple"> ???</span>
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
         <input type="hidden" value="{{csrf_token()}}" name="_token" />
         <div class="box-body">
          <div class="col-md-12 ">
            <div class="col-md-2">
              <div class="form-group">
                <label>Prod. Date From</label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="datefrom2" name="datefrom2">
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label>Prod. Date To</label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="dateto2" name="dateto2">
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <label>Process</label>
              <div class="form-group">
                <select class="form-control select2" data-placeholder="Select Process Code" name="code2" id="code2" style="width: 100%;">                 
                  <option value="PN_Kensa_Awal">Kensa Awal</option>
                  <option value="PN_Kensa_Akhir">Kensa Akhir</option> 
                  <option value="PN_Kakuning_Visual">Kakunin Visual</option>
                  <option value="bentsuki">Bentsuki</option>                             
                </select>
              </div>
              
            </div>
            <div class="col-md-4">
              <label>&nbsp;</label>
              <div class="form-group">
                <a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
                <button id="search" onClick="ngTotal()" class="btn btn-primary">Search</button>
              </div>
            </div>
          </div>
          
          
          <div id="container">

          </div>
        </div>
      </div>
    </div>

<div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-body">
          <div class="row">
            <div class="col-xs-12">      
              <input type="hidden" value="{{csrf_token()}}" name="_token" />
              <div class="box-body">
                <div class="col-md-12">
                 <div class="col-md-2">
                  <div class="form-group">
                    <label>Prod. Date From</label>
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <input type="text" class="form-control pull-right" id="datefrom" name="datefrom">
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label>Prod. Date To</label>
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <input type="text" class="form-control pull-right" id="dateto" name="dateto">
                    </div>
                  </div>
                </div>

                <div class="col-md-4">
                  <label>Process</label>
                  <div class="form-group">
                    <select class="form-control select2" data-placeholder="Select Process Code" name="code" id="code" style="width: 100%;">                 
                      <option value="PN_Kensa_Awal">Kensa Awal</option>
                      <option value="PN_Kensa_Akhir">Kensa Akhir</option>  
                      <option value="PN_Kakuning_Visual">Kakunin Visual</option>
                      <option value="Bentsuki">Bentsuki</option>                            
                    </select>
                  </div>

                </div>
                <div class="col-md-4">
                  <label>&nbsp;</label>
                  <div class="form-group">
                    <a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
                    <button id="search" onClick="Fillrecord()" class="btn btn-primary">Search</button>
                  </div>
                </div>
              </div>


              <div class="row">
                <div class="col-md-12" id="flo_detail_tablediv">
                  <table id="flo_detail_table" class="table table-bordered table-striped table-hover" >
                    <thead style="background-color: rgba(126,86,134,.7);">
                      <tr>
                        <!-- <th>Serial Number</th> -->
                        <th>Date</th>
                        <th>Biri</th>
                        <th>Oktaf</th>
                        <th>T. Tinggi</th>
                        <th>T. Rendah</th>

                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot style="background-color: RGB(252, 248, 227);">
                      <tr>
                        <th>Total</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                      </tr>
                    </tfoot>
                  </table>


                </div>
              </div>

              <div class="row">
                <div class="col-md-12" id="flo_detail_table2div">             

                 <table id="flo_detail_table2" class="table table-bordered table-striped table-hover" >
                  <thead style="background-color: rgba(126,86,134,.7);">
                    <tr>
                      <!-- <th>Serial Number</th> -->
                      <th>Date</th>
                      <th>Frame Assy</th>
                      <th>Cover R/L</th>
                      <th>Cover Lower</th>
                      <th>Handle</th>
                      <th>Button</th>
                      <th>Pianica</th>

                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                  <tfoot style="background-color: RGB(252, 248, 227);">
                    <tr>
                      <th>Total</th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>

            <div class="row table-responsive">
                <div class="col-md-12 " id="flo_detail_table3div">             

                 <table id="flo_detail_table3" class="table table-bordered table-striped table-hover " >
                  <thead style="background-color: rgba(126,86,134,.7);"> 
                    <tr>
                      <th rowspan="2" style="vertical-align: middle;">Date</th>
                      <th colspan="2">Celah Lebar</th>
                      <th colspan="2">Celah Sempit</th>
                      <th colspan="2">Kepala Rusak</th>
                      <th colspan="2">Kotor</th>
                      <th colspan="2">Lekukan</th>
                      <th colspan="2">Lengkung</th>
                      <th colspan="2">Lepas</th>
                      <th colspan="2">Longgar</th>
                      <th colspan="2">Melekat</th>
                      <th colspan="2">Pangkal Menempel</th>
                      <th colspan="2">Panjang</th>
                      <th colspan="2">Patah</th>
                      <th colspan="2">Salah Posisi</th>
                      <th colspan="2">Terbalik</th>
                      <th colspan="2">Ujung Menempel</th>
                      <th colspan="2">Double</th>
                    </tr>
                    <tr>                     
                      <th>Low</th>
                      <th>High</th>
                      <th>Low</th>
                      <th>High</th>
                      <th>Low</th>
                      <th>High</th>
                      <th>Low</th>
                      <th>High</th>
                      <th>Low</th>
                      <th>High</th>
                      <th>Low</th>
                      <th>High</th>
                      <th>Low</th>
                      <th>High</th>
                      <th>Low</th>
                      <th>High</th>
                      <th>Low</th>
                      <th>High</th>
                      <th>Low</th>
                      <th>High</th>
                      <th>Low</th>
                      <th>High</th>
                      <th>Low</th>
                      <th>High</th>
                      <th>Low</th>
                      <th>High</th>
                      <th>Low</th>
                      <th>High</th>
                      <th>Low</th>
                      <th>High</th>
                      <th>Low</th>
                      <th>High</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                  <tfoot style="background-color: RGB(252, 248, 227);">
                    <tr>
                      <th>Total</th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>

          </div>
        </div>
      </div>
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
    Fillrecord();
    // recall();
    
    $('body').toggleClass("sidebar-collapse");
    $('.select2').select2({
      dropdownAutoWidth : true,
      width: '100%',
    });

    $('#datefrom').datepicker({
      autoclose: true, 
      format :'yyyy-mm-dd',
    });
    $('#dateto').datepicker({
      autoclose: true,
      format :'yyyy-mm-dd',
    });
    
    $('#datefrom2').datepicker({
      autoclose: true, 
      format :'yyyy-mm-dd',
    });
    $('#dateto2').datepicker({
      autoclose: true,
      format :'yyyy-mm-dd',
    });

  });
  
  function recall() {
    ngTotal();
    setTimeout(recall, 1000);
  }

  function ngTotal() {
    var datefrom = $('#datefrom2').val(); 
    var dateto = $('#dateto2').val();
    var code = $('#code2').val();
    
    var data = {
      datefrom:datefrom,
      dateto:dateto,
      code:code,
    }
    $.get('{{ url("index/reportDayAwalDataGrafik") }}',data, function(result, status, xhr){
      console.log(status);
      console.log(result);
      console.log(xhr);
      if(xhr.status == 200){
        if(result.status){

          var tgl = [];
          var biri = [];
          var oktaf = [];
          var rendah = [];
          var tinggi = [];
          var target =[];
          var frame = [];
          var rl = [];
          var lower = [];
          var handle = [];
          var button = [];
          var pianica = [];

          var CLebar = [];
          var CSempit = [];
          var KRusak = [];
          var Kotor = [];
          var Lekukan = [];
          var Lengkung = [];
          var Lepas = [];
          var Melekat = [];
          var Longgar = [];
          var PMenempel = [];
          var Panjang = [];
          var Patah = [];
          var SPosisi = [];
          var Terbalik = [];
          var UMenempel = [];
          var Doble = [];


                  // alert(result.record[0].pro);
                  for (var i = 0; i < result.record.length; i++) { 
                    CLebar.push(parseInt(result.record[i].CLebar));
                    CSempit.push(parseInt(result.record[i].CSempit));
                    KRusak.push(parseInt(result.record[i].KRusak));
                    Kotor.push(parseInt(result.record[i].Kotor));
                    Lekukan.push(parseInt(result.record[i].Lekukan));
                    Lengkung.push(parseInt(result.record[i].Lengkung));
                    Lepas.push(parseInt(result.record[i].Lepas));
                    Longgar.push(parseInt(result.record[i].Longgar));
                    Melekat.push(parseInt(result.record[i].Melekat));
                    PMenempel.push(parseInt(result.record[i].PMenempel));
                    Panjang.push(parseInt(result.record[i].Panjang));
                    Patah.push(parseInt(result.record[i].Patah));
                    SPosisi.push(parseInt(result.record[i].SPosisi));
                    Terbalik.push(parseInt(result.record[i].Terbalik));
                    UMenempel.push(parseInt(result.record[i].UMenempel));
                    
                    Doble.push(parseInt(result.record[i].Doble));
                    

                   tgl.push(result.record[i].tgl);

                   biri.push(parseInt(result.record[i].biri)); 
                   oktaf.push(parseInt(result.record[i].oktaf));
                   rendah.push(parseInt(result.record[i].rendah));
                   tinggi.push(parseInt(result.record[i].tinggi)); 

                   frame.push(parseInt(result.record[i].frame)); 
                   rl.push(parseInt(result.record[i].rl));
                   lower.push(parseInt(result.record[i].lower));
                   handle.push(parseInt(result.record[i].handle));
                   button.push(parseInt(result.record[i].button));
                   pianica.push(parseInt(result.record[i].pianica));

                   target.push(parseInt(result.record[i].target)); 
                 } 

                 (function (H) {
    // Pass error messages
    H.Axis.prototype.allowNegativeLog = true;

    // Override conversions
    H.Axis.prototype.log2lin  = function (num) {
      var isNegative = num < 0,
      adjustedNum = Math.abs(num),
      result;
      if (adjustedNum < 10) {
        adjustedNum += (10 - adjustedNum) / 10;
      }
      result = Math.log(adjustedNum) / Math.LN10;
      return isNegative ? -result : result;
    };
    H.Axis.prototype.lin2log = function (num) {
      var isNegative = num < 0,
      absNum = Math.abs(num),
      result = Math.pow(10, absNum);
      if (result < 10) {
        result = (10 * (result - 1)) / (10 - 1);
      }
      return isNegative ? -result : result;
    };
  }(Highcharts));

                 if (result.record[0].pro =="awal") { 
                   Highcharts.chart('container', {
                    chart: {
                      type: 'line'
                    },
                    title: {
                      text: 'Monthly Report'
                    },
                    subtitle: {
                      text: ''
                    },
                    xAxis: {
                      tickWidth: 0,
                      gridLineWidth: 1,
                      categories: tgl
                    },
                    yAxis: {
                      type: 'logarithmic',
                      title: {
                        text: 'Total NG'
                      }
                    },
                    tooltip: {
                      shared: true,
                      crosshairs: true
                    },
                    plotOptions: {
                      line: {
                        dataLabels: {
                          enabled: true
                        },
                        enableMouseTracking: true
                      }
                    },
                    series: [{
                      name: 'Biri',
                      data: biri
                    }, {
                      name: 'Oktaf',
                      data: oktaf
                    }, {
                      name: 'T. Tinggi',
                      data: tinggi
                    }
                    , {
                      name: 'T. Rendah',
                      data: rendah
                    }, {
                      name: 'Total Production',
                      data: target,
                      
                      color:'red'
                      
                    }]
                  });
                 }else if (result.record[0].pro =="visual"){
                  Highcharts.chart('container', {
                    chart: {
                      type: 'line'
                    },
                    title: {
                      text: 'Monthly Report Kakunin Visual'
                    },
                    subtitle: {
                      text: ''
                    },
                    xAxis: {
                      tickWidth: 0,
                      gridLineWidth: 1,
                      categories: tgl
                    },
                    yAxis: {
                      type: 'logarithmic',
                      title: {
                        text: 'Total NG'
                      }
                    },
                    tooltip: {
                      shared: true,
                      crosshairs: true
                    },
                    plotOptions: {
                      series: {
                        minPointLength: 5
                      },
                      line: {
                        dataLabels: {
                          enabled: true
                        },
                        enableMouseTracking: true
                      }
                    },
                    series: [{
                      name: 'Frame Assy',
                      data: frame
                    }, {
                      name: 'Cover R/L',
                      data: rl
                    }, {
                      name: 'Cover Lower',
                      data: lower
                    }
                    , {
                      name: 'handle',
                      data: handle
                    },{
                      name: 'Button',
                      data: button
                    },
                    {
                      name: 'Pianica',
                      data: pianica
                    },

                    {
                      name: 'Total Production',
                      data: target,
                      
                      color:'red'
                      
                    }]
                  });
                }else{
                  Highcharts.chart('container', {
                    chart: {
                      type: 'line'
                    },
                    title: {
                      text: 'Monthly Report Bentsuki'
                    },
                    subtitle: {
                      text: ''
                    },
                    xAxis: {
                      tickWidth: 0,
                      gridLineWidth: 1,
                      categories: tgl
                    },
                    yAxis: {
                      type: 'logarithmic',
                      title: {
                        text: 'Total NG'
                      }
                    },
                    tooltip: {
                      shared: true,
                      crosshairs: true
                    },
                    plotOptions: {
                      series: {
                        minPointLength: 5
                      },
                      line: {
                        dataLabels: {
                          enabled: true
                        },
                        enableMouseTracking: true
                      }
                    },
                    series: [{
                      name: 'Celah Lebar',
                      data: CLebar
                    }, {
                      name: 'Celah Sempit',
                      data: CSempit
                    }, {
                      name: 'Kepala Rusak',
                      data: KRusak
                    }
                    , {
                      name: 'Kotor',
                      data: Kotor
                    },{
                      name: 'Lekukan',
                      data: Lekukan
                    },
                    {
                      name: 'Lengkung',
                      data: Lengkung
                    },

                    {
                      name: 'Lepas',
                      data: Lepas
                    },

                    {
                      name: 'Longgar',
                      data: Longgar
                    },

                    {
                      name: 'Melekat',
                      data: Melekat
                    },

                    {
                      name: 'Pangkal Menempel',
                      data: PMenempel
                    },

                    {
                      name: 'Panjang',
                      data: Panjang
                    },

                    {
                      name: 'Patah',
                      data: Patah
                    },

                    {
                      name: 'Salah Posisi',
                      data: SPosisi
                    },

                    {
                      name: 'Terbalik',
                      data: Terbalik
                    },

                    {
                      name: 'Ujung Menempel',
                      data: UMenempel
                    },
                    {
                      name: 'Double',
                      data: Doble
                    },

                    {
                      name: 'Total Production',
                      data: target,
                      
                      color:'red'
                      
                    }]
                  });
                }

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


function Fillrecord(){
  $('#flo_detail_table').DataTable().destroy();
  $('#flo_detail_table2').DataTable().destroy();
  $('#flo_detail_table3').DataTable().destroy();
  var datefrom = $('#datefrom').val();
  var dateto = $('#dateto').val();
  var code = $('#code').val();

  var data = {
    datefrom:datefrom,
    dateto:dateto,
    code:code
  }
  if (code == "PN_Kensa_Akhir" || code == "PN_Kensa_Awal") {
    $('#flo_detail_tablediv').css({"display":"block"});
    $('#flo_detail_table2div').css({"display":"none"});
    $('#flo_detail_table3div').css({"display":"none"});
    $('#flo_detail_table').DataTable({
      'dom': 'Bfrtip',
      'responsive': true,
      'lengthMenu': [
      [ 10, 25, 50, -1 ],
      [ '10 rows', '25 rows', '50 rows', 'Show all' ]
      ],
      'buttons': {

        buttons:[
        {
          extend: 'pageLength',
          className: 'btn btn-default',
        },
        {
          extend: 'copy',
          className: 'btn btn-success',
          text: '<i class="fa fa-copy"></i> Copy',
          exportOptions: {
            columns: ':not(.notexport)'
          }
        },
        {
          extend: 'excel',
          className: 'btn btn-info',
          text: '<i class="fa fa-file-excel-o"></i> Excel',
          exportOptions: {
            columns: ':not(.notexport)'
          }
        },
        {
          extend: 'print',
          className: 'btn btn-warning',
          text: '<i class="fa fa-print"></i> Print',
          exportOptions: {
            columns: ':not(.notexport)'
          }
        },
        ]
      },
      "footerCallback": function (tfoot, data, start, end, display) {
        var intVal = function ( i ) {
          return typeof i === 'string' ?
          i.replace(/[\$%,]/g, '')*1 :
          typeof i === 'number' ?
          i : 0;
        };
        var api = this.api();
        var biri = api.column(1).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var oktaf = api.column(2).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var tinggi = api.column(3).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah = api.column(4).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        $(api.column(1).footer()).html(biri.toLocaleString());
        $(api.column(2).footer()).html(oktaf.toLocaleString());
        $(api.column(3).footer()).html(tinggi.toLocaleString());
        $(api.column(4).footer()).html(rendah.toLocaleString());
      },
      
      targets : "_all",
      render: function (data, type, row ) {
       data_replace = data.location.replace("/PN_/g", "a");

     },


     'paging': true,
     'lengthChange': true,
     'searching': true,
     'ordering': true,
     'order': [],
     'info': true,
     'autoWidth': true,
     "sPaginationType": "full_numbers",
     "bJQueryUI": true,
     "bAutoWidth": false,
     "processing": true,
     "ajax": {
      "type" : "post",
      "url" : "{{ url("index/reportDayAwalData") }}",
      "data" : data,
    },
    "columns": [
    { "data": "tgl" },
    { "data": "biri" },
    { "data": "oktaf" },
    { "data": "tinggi" },
    { "data": "rendah" }

    ]
  });
  }
  else if(code == "PN_Kakuning_Visual"){
    $('#flo_detail_table2div').css({"display":"block"});
    $('#flo_detail_tablediv').css({"display":"none"});
    $('#flo_detail_table3div').css({"display":"none"});

    $('#flo_detail_table2').DataTable({
      'dom': 'Bfrtip',
      'responsive': true,
      'lengthMenu': [
      [ 10, 25, 50, -1 ],
      [ '10 rows', '25 rows', '50 rows', 'Show all' ]
      ],
      'buttons': {

        buttons:[
        {
          extend: 'pageLength',
          className: 'btn btn-default',
        },
        {
          extend: 'copy',
          className: 'btn btn-success',
          text: '<i class="fa fa-copy"></i> Copy',
          exportOptions: {
            columns: ':not(.notexport)'
          }
        },
        {
          extend: 'excel',
          className: 'btn btn-info',
          text: '<i class="fa fa-file-excel-o"></i> Excel',
          exportOptions: {
            columns: ':not(.notexport)'
          }
        },
        {
          extend: 'print',
          className: 'btn btn-warning',
          text: '<i class="fa fa-print"></i> Print',
          exportOptions: {
            columns: ':not(.notexport)'
          }
        },
        ]
      },
      "footerCallback": function (tfoot, data, start, end, display) {
        var intVal = function ( i ) {
          return typeof i === 'string' ?
          i.replace(/[\$%,]/g, '')*1 :
          typeof i === 'number' ?
          i : 0;
        };
        var api = this.api();
        var biri = api.column(1).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var oktaf = api.column(2).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var tinggi = api.column(3).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah = api.column(4).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var tinggi2 = api.column(5).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah2 = api.column(6).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        $(api.column(1).footer()).html(biri.toLocaleString());
        $(api.column(2).footer()).html(oktaf.toLocaleString());
        $(api.column(3).footer()).html(tinggi.toLocaleString());
        $(api.column(4).footer()).html(rendah.toLocaleString());
        $(api.column(5).footer()).html(tinggi2.toLocaleString());
        $(api.column(6).footer()).html(rendah2.toLocaleString());
      },
      
      targets : "_all",
      render: function (data, type, row ) {
       data_replace = data.location.replace("/PN_/g", "a");

     },


     'paging': true,
     'lengthChange': true,
     'searching': true,
     'ordering': true,
     'order': [],
     'info': true,
     'autoWidth': true,
     "sPaginationType": "full_numbers",
     "bJQueryUI": true,
     "bAutoWidth": false,
     "processing": true,
     "ajax": {
      "type" : "post",
      "url" : "{{ url("index/reportDayAwalData") }}",
      "data" : data,
    },
    "columns": [
    { "data": "tgl" },
    { "data": "frame" },
    { "data": "rl" },
    { "data": "lower" },
    { "data": "handle" },
    { "data": "button" },
    { "data": "pianica" }

    ]
  });
  }
  else {
    $('#flo_detail_table2div').css({"display":"none"});
    $('#flo_detail_tablediv').css({"display":"none"});
    $('#flo_detail_table3div').css({"display":"block"});

    $('#flo_detail_table3').DataTable({
      'dom': 'Bfrtip',
      'responsive': true,
      'lengthMenu': [
      [ 10, 25, 50, -1 ],
      [ '10 rows', '25 rows', '50 rows', 'Show all' ]
      ],
      'buttons': {

        buttons:[
        {
          extend: 'pageLength',
          className: 'btn btn-default',
        },
        {
          extend: 'copy',
          className: 'btn btn-success',
          text: '<i class="fa fa-copy"></i> Copy',
          exportOptions: {
            columns: ':not(.notexport)'
          }
        },
        {
          extend: 'excel',
          className: 'btn btn-info',
          text: '<i class="fa fa-file-excel-o"></i> Excel',
          exportOptions: {
            columns: ':not(.notexport)'
          }
        },
        {
          extend: 'print',
          className: 'btn btn-warning',
          text: '<i class="fa fa-print"></i> Print',
          exportOptions: {
            columns: ':not(.notexport)'
          }
        },
        ]
      },
      "footerCallback": function (tfoot, data, start, end, display) {
        var intVal = function ( i ) {
          return typeof i === 'string' ?
          i.replace(/[\$%,]/g, '')*1 :
          typeof i === 'number' ?
          i : 0;
        };
        var api = this.api();
        var biri = api.column(1).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var oktaf = api.column(2).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var tinggi = api.column(3).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah = api.column(4).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var tinggi2 = api.column(5).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah2 = api.column(6).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah7 = api.column(7).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah8 = api.column(8).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah9 = api.column(9).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah10 = api.column(10).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah11 = api.column(11).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah12 = api.column(12).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah13 = api.column(13).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah14 = api.column(14).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah15 = api.column(15).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah16 = api.column(16).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah17 = api.column(17).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah18 = api.column(18).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah19 = api.column(19).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah20 = api.column(20).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah21 = api.column(21).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah22 = api.column(22).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah23 = api.column(23).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah24 = api.column(24).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah25 = api.column(25).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah26 = api.column(26).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah27 = api.column(27).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah28 = api.column(28).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah29 = api.column(29).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah30 = api.column(30).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah31 = api.column(31).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        var rendah32 = api.column(32).data().reduce(function (a, b) {
          return intVal(a)+intVal(b);
        }, 0)
        $(api.column(1).footer()).html(biri.toLocaleString());
        $(api.column(2).footer()).html(oktaf.toLocaleString());
        $(api.column(3).footer()).html(tinggi.toLocaleString());
        $(api.column(4).footer()).html(rendah.toLocaleString());
        $(api.column(5).footer()).html(tinggi2.toLocaleString());
        $(api.column(6).footer()).html(rendah2.toLocaleString());

        $(api.column(7).footer()).html(rendah7.toLocaleString());
        $(api.column(8).footer()).html(rendah8.toLocaleString());
        $(api.column(9).footer()).html(rendah9.toLocaleString());
        $(api.column(10).footer()).html(rendah10.toLocaleString());
        $(api.column(11).footer()).html(rendah11.toLocaleString());
        $(api.column(12).footer()).html(rendah12.toLocaleString());
        $(api.column(13).footer()).html(rendah13.toLocaleString());
        $(api.column(14).footer()).html(rendah14.toLocaleString());
        $(api.column(15).footer()).html(rendah15.toLocaleString());
        $(api.column(16).footer()).html(rendah16.toLocaleString());

        $(api.column(17).footer()).html(rendah17.toLocaleString());
        $(api.column(18).footer()).html(rendah18.toLocaleString());
        $(api.column(19).footer()).html(rendah19.toLocaleString());
        $(api.column(20).footer()).html(rendah20.toLocaleString());
        $(api.column(21).footer()).html(rendah21.toLocaleString());
        $(api.column(22).footer()).html(rendah22.toLocaleString());
        $(api.column(23).footer()).html(rendah23.toLocaleString());
        $(api.column(24).footer()).html(rendah24.toLocaleString());
        $(api.column(25).footer()).html(rendah25.toLocaleString());
        $(api.column(26).footer()).html(rendah26.toLocaleString());

        $(api.column(27).footer()).html(rendah27.toLocaleString());
        $(api.column(28).footer()).html(rendah28.toLocaleString());
        $(api.column(29).footer()).html(rendah29.toLocaleString());
        $(api.column(30).footer()).html(rendah30.toLocaleString());
        $(api.column(31).footer()).html(rendah31.toLocaleString());
        $(api.column(32).footer()).html(rendah32.toLocaleString());
        
        

      },
      
      targets : "_all",
      render: function (data, type, row ) {
       data_replace = data.location.replace("/PN_/g", "a");

     },


     'paging': true,
     'lengthChange': true,
     'searching': true,
     'ordering': true,
     'order': [],
     'info': true,
     'autoWidth': true,
     "sPaginationType": "full_numbers",
     "bJQueryUI": true,
     "bAutoWidth": true,
     "processing": true,
     "ajax": {
      "type" : "post",
      "url" : "{{ url("index/reportDayAwalData") }}",
      "data" : data,
    },
    "columns": [
    { "data": "week_date" },
    { "data": "CLebarL" },
    { "data": "CLebarH" },
    { "data": "CSempitL" },
    { "data": "CSempitH" },
    { "data": "KRusakL" },
    { "data": "KRusakH" },
    { "data": "KotorL" },
    { "data": "KotorH" },
    { "data": "LekukanL" },
    { "data": "LekukanH" },
    { "data": "LengkungL" },
    { "data": "LengkungH" },
    { "data": "LepasL" },
    { "data": "LepasH" },
    { "data": "LonggarL" },
    { "data": "LonggarH" },
    { "data": "MelekatL" },
    { "data": "MelekatH" },
    { "data": "PMenempelL" },
    { "data": "PMenempelH" },
    { "data": "PanjangL" },
    { "data": "PanjangH" },
    { "data": "PatahL" },
    { "data": "PatahH" },
    { "data": "SPosisiL" },
    { "data": "SPosisiH" },
    { "data": "TerbalikL" },
    { "data": "TerbalikH" },
    { "data": "UMenempelL" },
    { "data": "UMenempelH" },
    { "data": "DoubleL" },
     { "data": "DoubleH" },

    ]
  });
  }
}

function clearConfirmation(){
  $("#datefrom").val("");
  $("#dateto").val("");

  $("#datefrom2").val("");
  $("#dateto2").val("");
}
</script>

@stop