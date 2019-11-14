@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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
    CPAR <span class="text-purple">Grafik</span>
    <small>Berdasarkan Bulan<span class="text-purple"> </span></small>
  </h1>
  <ol class="breadcrumb" id="last_update">
  </ol>
</section>
@endsection


@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="col-md-12">
        <div class="col-md-2">
          <div class="input-group date">
            <div class="input-group-addon bg-green" style="border-color: #00a65a">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" class="form-control datepicker" id="tglfrom" placeholder="Bulan Dari" style="border-color: #00a65a">
          </div>
        </div>

        <div class="col-md-2">
          <div class="input-group date">
            <div class="input-group-addon bg-green" style="border-color: #00a65a">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" class="form-control datepicker" id="tglto" placeholder="Bulan Ke" style="border-color: #00a65a">
          </div>
        </div>

        <div class="col-md-3" style="width:230px">
          <div class="input-group">
            <div class="input-group-addon bg-blue" style="border-color: #00a65a">
              <i class="fa fa-search"></i>
            </div>
            <select class="form-control select2" multiple="multiple" id="kategori" data-placeholder="Select Kategori">
                <option value="Eksternal">Eksternal</option>
                <option value="Internal">Internal</option>
                <option value="Supplier">Supplier</option>
              </select>
          </div>

            <!-- <div class="form-group">
              <select class="form-control select2" multiple="multiple" id="kategori" data-placeholder="Select Kategori">
                <option value="Eksternal">Eksternal</option>
                <option value="Internal">Internal</option>
                <option value="Supplier">Supplier</option>
              </select>
            -->
          </div>

         <div class="col-md-3">
            <div class="input-group">
              <div class="input-group-addon bg-blue" style="border-color: #00a65a">
                <i class="fa fa-search"></i>
              </div>
              <select class="form-control select2" multiple="multiple" id="departemen" data-placeholder="Pilih Departemen" style="border-color: #605ca8" >
                  <!-- <option value="" selected>Semua Departemen</option> -->
                  @foreach($departemens as $departemen)
                    <option value="{{ $departemen->id }}">{{ $departemen->department_name }}</option>
                  @endforeach
                </select>
            </div>
          </div>

        <div class="col-xs-2">
          <button class="btn btn-success" onclick="drawChart()">Update Chart</button>
        </div>
        <br>
        <br>

        <!-- <div class="col-md-2 pull-right">
          <select class="form-control" id="fq" data-placeholder="Pilih Fiscal year" onchange="drawChart()" style="border-color: #605ca8" >
              <option value="" selected>Semua Fiscal Year</option>
              <option value="196">FY196</option>
              <option value="195">FY195</option>
            </select>
          <br>
        </div> -->

        <!-- <div class="col-md-2 pull-right">
              <select class="form-control select2" multiple="multiple" id="fySelect" data-placeholder="Select Fiscal Year" onchange="drawChart()">
                @foreach($fys as $fy)
                <option value="{{ $fy->fiscal_year }}">{{ $fy->fiscal_year }}</option>
                @endforeach
              </select>
              <input type="text" name="fy" id="fy" hidden>
          </div> -->
       
        

        <!-- <div class="col-md-2">
          <select class="form-control" id="bulanfrom" data-placeholder="Bulan Dari" style="border-color: #605ca8" onchange="getbulanke()">
              <option value="">Bulan Dari</option>
              @foreach($bulans as $bulan)
                <option value="{{ $bulan->bulan }}">{{ $bulan->namabulan }}</option>
                @endforeach
            </select>
        </div> -->

        <!-- <div class="col-md-2">
          <select class="form-control" id="bulanto" data-placeholder="Bulan Ke" style="border-color: #605ca8" >
              <option value="">Bulan Ke</option>
             
            </select>
        </div>

        <div class="col-md-2">
          <select class="form-control" id="tahun" data-placeholder="Pilih Tahun" style="border-color: #605ca8" >
              <option value="">Semua Tahun</option>
               @foreach($years as $year)
                <option value="{{ $year->tahun }}"
                  <?php if($year->tahun == date('Y')){ echo "selected";}?>
                  >{{ $year->tahun }}</option>
                @endforeach
            </select>
        </div>
 -->


      </div>
      <div class="col-md-12" style="margin-top: 20px">
        <div class="box">
          <!-- <div class="box-header with-border" id="boxTitle">Tes</div> -->
          <div class="nav-tabs-custom">
            <div class="tab-content">
              <div class="tab-pane active" id="tab_1">
                <div id="chart" style="width: 99%;"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-12" style="margin-top: 20px">
        <div class="box">
          <!-- <div class="box-header with-border" id="boxTitle">Tes</div> -->
          <div class="nav-tabs-custom">
            <div class="tab-content">
              <div class="tab-pane active" id="tab_1">
                <div id="chartdept" style="width: 99%;"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="myModal">
    <div class="modal-dialog" style="width:1000px;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="float: right;" id="modal-title"></h4>
          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
          <br><h4 class="modal-title" id="judul_table"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="example2" class="table table-striped table-bordered" style="width: 100%;"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>No CPAR</th>
                    <th>Kategori</th> 
                    <th>Manager</th>    
                    <th>Lokasi</th>
                    <th>Tgl Permintaan</th>
                    <th>Tgl Balas</th>
                    <th>Via Komplain</th>
                    <th>Departemen</th>
                    <th>Sumber Komplain</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
        </div>
      </div>
    </div>
  </div>

</section>


@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<!-- <script src="{{ url("js/highcharts-3d.js")}}"></script> -->
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>

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

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");
    $('#myModal').on('hidden.bs.modal', function () {
      $('#example2').DataTable().clear();
    });
    $('.select2').select2();

    drawChart();
    drawChartDepartemen();
  });

  // $('#tgl').datepicker({
  //   format: "yyyy-mm-dd",
  //   autoclose: true,
  //   todayHighlight: true
  // });

  $('.datepicker').datepicker({
    format: "yyyy-mm",
    startView: "months", 
    minViewMode: "months",
    autoclose: true,

  });

  // function changekategori() {
  //   $("#kategori").val($("#kategoriselect").val());
  // }

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

  function getbulanke(){
    var bulanfrom = document.getElementById("bulanfrom");
    var bulanto = document.getElementById("bulanto");
    var getbulanfrom = bulanfrom.options[bulanfrom.selectedIndex].value;

    // console.log(bulanfrom.options[10].value);
    var txt;
    var i;
    if (getbulanfrom != "") {
      for (i = 1; i < bulanfrom.options.length; i++) {
        if (getbulanfrom < i) 
        {
          // console.log(i);
          // console.log(bulanfrom.options[i].value);
          $('#bulanto').append($("<option></option>").attr("value",bulanfrom.options[i].value).text(bulanfrom.options[i].text)); 
        }
        // console.log(bulanfrom.options.length);
      }
    }
  }

  function drawChart() {
    // var tahun = $('#tahun').val();
    var tglfrom = $('#tglfrom').val();
    var tglto = $('#tglto').val();
    var kategori = $('#kategori').val();
    var departemen = $('#departemen').val();

    var data = {
      // tahun: tahun,
      tglfrom: tglfrom,
      tglto: tglto,
      kategori: kategori,
      departemen: departemen
    };


    $.get('{{ url("index/qc_report/fetchReport") }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){
          // var xAxis = [], productionCount = [], inTransitCount = [], fstkCount = []
          // for (i = 0; i < data.length; i++) {
          //   xAxis.push(data[i].destination);
          //   productionCount.push(data[i].production);
          //   inTransitCount.push(data[i].intransit);
          //   fstkCount.push(data[i].fstk);
          // }
          // console.log(result.tgt);
          var years = result.tahun;
          if(years == null){
            years = "All"
          }

          var month = [], jml = [], statusopen = [], statusclose = [];

          $.each(result.datas, function(key, value) {
            // departemen.push(value.department_name);
            month.push(value.bulan);
            jml.push(value.jumlah);
            statusopen.push(parseInt(value.open));
            statusclose.push(parseInt(value.close));
          })

          $('#chart').highcharts({
            chart: {
              type: 'column'
            },
            title: {
              text: 'CPAR Report'
            },
            xAxis: {
              type: 'category',
              categories: month
            },
            yAxis: {
              type: 'linear',
              title: {
                text: 'Total CPAR'
              },
              tickInterval: 1,
              stackLabels: {
                  enabled: true,
                  style: {
                      fontWeight: 'bold',
                      color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                  }
              }
            },
            legend: {
              align: 'right',
              x: -30,
              verticalAlign: 'top',
              y: 25,
              floating: true,
              backgroundColor:
                  Highcharts.defaultOptions.legend.backgroundColor || 'white',
              borderColor: '#CCC',
              borderWidth: 1,
              shadow: false
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                point: {
                  events: {
                    click: function () {
                      ShowModal(this.category,this.series.name,result.tglfrom,result.tglto,result.kategori,result.departemen);
                    }
                  }
                },
                borderWidth: 0,
                dataLabels: {
                  enabled: false,
                  format: '{point.y}'
                }
              },
              column: {
                  stacking: 'normal',
                  dataLabels: {
                      enabled: true
                  }
              }
            },
            credits: {
              enabled: false
            },

            tooltip: {
              formatter:function(){
                return this.series.name+' : ' + this.y;
              }
            },
            series: [{
                name: 'Open',
                color: '#388e3c',
                data: statusopen
            }, {
                name: 'Closed',
                data: statusclose,
                color : '#c62828'
            },
            {
                type: 'spline',
                name: 'Open',
                color: '#388e3c',
                data: statusopen
            }
            ]
          })
        } else{
          alert('Attempt to retrieve data failed');
        }
      }
    })
  }

  function drawChartDepartemen() {

    var departemen = $('#departemen').val();

    var data = {
      departemen: departemen
    };

    $.get('{{ url("index/qc_report/fetchDept") }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){
          var departemen = [], jml = [], statusopen = [], statusclose = [];

          $.each(result.datas, function(key, value) {
            departemen.push(value.department_name);
            jml.push(value.jumlah);
            statusopen.push(parseInt(value.open));
            statusclose.push(parseInt(value.close));
          })

          $('#chartdept').highcharts({
            chart: {
              type: 'column'
            },
            title: {
              text: 'CPAR Report By Departement'
            },
            xAxis: {
              type: 'category',
              categories: departemen
            },
            yAxis: {
              type: 'linear',
              title: {
                text: 'Total CPAR'
              },
              tickInterval: 1,
              stackLabels: {
                  enabled: true,
                  style: {
                      fontWeight: 'bold',
                      color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                  }
              }
            },
            legend: {
              align: 'right',
              x: -30,
              verticalAlign: 'top',
              y: 25,
              floating: true,
              backgroundColor:
                  Highcharts.defaultOptions.legend.backgroundColor || 'white',
              borderColor: '#CCC',
              borderWidth: 1,
              shadow: false
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                point: {
                  events: {
                    click: function () {
                      ShowModalDept(this.category,this.series.name);
                    }
                  }
                },
                borderWidth: 0,
                dataLabels: {
                  enabled: false,
                  format: '{point.y}'
                }
              },
              column: {
                  stacking: 'normal',
                  dataLabels: {
                      enabled: true
                  }
              }
            },
            credits: {
              enabled: false
            },

            tooltip: {
              formatter:function(){
                return this.series.name+' : ' + this.y;
              }
            },
            series: [{
                name: 'Open',
                color: '#388e3c',
                data: statusopen
            }, {
                name: 'Closed',
                data: statusclose,
                color : '#c62828'
            },
            {
                type: 'spline',
                name: 'Open',
                color: '#388e3c',
                data: statusopen
            }
            ]
          })
        } else{
          alert('Attempt to retrieve data failed');
        }
      }
    })
  }

  function ShowModal(bulan, status, tglfrom, tglto, kategori, departemen) {
    tabel = $('#example2').DataTable();
    tabel.destroy();

    $("#myModal").modal("show");

    var table = $('#example2').DataTable({
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
          // text: '<i class="fa fa-print"></i> Show',
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
      "serverSide": true,
      "ajax": {
          "type" : "get",
          "url" : "{{ url("index/qc_report/detail_cpar") }}",
          "data" : {
            bulan : bulan,
            status : status
          }
        },
      "columns": [
          { "data": "cpar_no" },
          { "data": "kategori" },
          { "data": "name" },
          { "data": "lokasi" },
          { "data": "tgl_permintaan" },
          { "data": "tgl_balas" },
          { "data": "via_komplain" },
          { "data": "department_name" },
          { "data": "sumber_komplain" },
          { "data": "status_name" },
          { "data": "action", "width": "15%"}
        ]    });

    $('#judul_table').append().empty();
    $('#judul_table').append('<center><b>Bulan '+bulan+'</center></b>');
    
  }


  function ShowModalDept(departemen, status) {
    tabel = $('#example2').DataTable();
    tabel.destroy();

    $("#myModal").modal("show");

    var table = $('#example2').DataTable({
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
          // text: '<i class="fa fa-print"></i> Show',
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
      "serverSide": true,
      "ajax": {
          "type" : "get",
          "url" : "{{ url("index/qc_report/detail_cpar_dept") }}",
          "data" : {
            departemen : departemen,
            status : status
          }
        },
      "columns": [
          { "data": "cpar_no" },
          { "data": "kategori" },
          { "data": "name" },
          { "data": "lokasi" },
          { "data": "tgl_permintaan" },
          { "data": "tgl_balas" },
          { "data": "via_komplain" },
          { "data": "department_name" },
          { "data": "sumber_komplain" },
          { "data": "status_name" },
          { "data": "action", "width": "15%"}
        ]    });

    $('#judul_table').append().empty();
    $('#judul_table').append('<center><b>Departemen '+departemen+'</center></b>');
    
  }

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