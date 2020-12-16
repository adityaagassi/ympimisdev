@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

table.table-bordered{
  border:1px solid rgb(150,150,150);
}
table.table-bordered > thead > tr > th{
  border:1px solid rgb(54, 59, 56) !important;
  text-align: center;
  background-color: #212121;  
  color:white;
}
table.table-bordered > tbody > tr > td{
  border:1px solid rgb(54, 59, 56);
  background-color: #212121;
  color: white;
  vertical-align: middle;
  text-align: center;
  padding:3px;
}
table.table-condensed > thead > tr > th{   
  color: black
}
table.table-bordered > tfoot > tr > th{
  border:1px solid rgb(150,150,150);
  padding:0;
}

table.table-striped > thead > tr > th{
  border:1px solid black !important;
  text-align: center;
  background-color: rgba(126,86,134,.7) !important;  
}

table.table-striped > tbody > tr > td{
  border: 1px solid #eeeeee !important;
  border-collapse: collapse;
  color: black;
  padding: 3px;
  vertical-align: middle;
  text-align: center;
  background-color: white;
}

thead input {
  width: 100%;
  padding: 3px;
  box-sizing: border-box;
}
thead>tr>th{
  text-align:center;
}
tfoot>tr>th{
  text-align:center;
}
td:hover {
  overflow: visible;
}
table > thead > tr > th{
  border:2px solid #f4f4f4;
  color: white;
}
#tabelmonitor{
  font-size: 0.83vw;
}

.zoom{
   -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  -webkit-animation: zoomin 5s ease-in infinite;
  animation: zoomin 5s ease-in infinite;
  transition: all .5s ease-in-out;
  overflow: hidden;
}
@-webkit-keyframes zoomin {
  0% {transform: scale(0.7);}
  50% {transform: scale(1);}
  100% {transform: scale(0.7);}
}
@keyframes zoomin {
  0% {transform: scale(0.7);}   
  50% {transform: scale(1);}
  100% {transform: scale(0.7);}
} /*End of Zoom in Keyframes */

/* Zoom out Keyframes */
@-webkit-keyframes zoomout {
  0% {transform: scale(0);}
  50% {transform: scale(0.5);}
  100% {transform: scale(0);}
}
@keyframes zoomout {
    0% {transform: scale(0);}
  50% {transform: scale(0.5);}
  100% {transform: scale(0);}
}/*End of Zoom out Keyframes */


#loading, #error { display: none; }

</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    Form Ketidaksesuaian <span class="text-purple">Grafik</span>
    <small>Berdasarkan Tanggal<span class="text-purple"> </span></small>
  </h1>
  <ol class="breadcrumb" id="last_update">
  </ol>
</section>
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0; padding-bottom: 0">
    <div class="row">
      <div class="col-md-12" style="padding: 1px !important">
        <div class="col-xs-2">
          <div class="input-group date">
            <div class="input-group-addon bg-green" style="border: none;">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" class="form-control datepicker" id="datefrom" placeholder="Select Date From">
          </div>
        </div>
        <div class="col-xs-2">
          <div class="input-group date">
            <div class="input-group-addon bg-green" style="border: none;">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" class="form-control datepicker" id="dateto" placeholder="Select Date To">
          </div>
        </div>
        <!-- <div class="col-xs-2">
          <div class="input-group">
            <div class="input-group-addon bg-blue">
              <i class="fa fa-search"></i>
            </div>
            <select class="form-control select2" multiple="multiple" id="status" data-placeholder="Pilih Status" style="border-color: #605ca8" >
                <option value="Belum Ditangani">Belum Ditangani</option>
                <option value="Sudah Ditangani">Sudah Ditangani</option>
            </select>
          </div>
        </div> -->

        <div class="col-xs-1">
          <button class="btn btn-success" onclick="drawChart()">Update Chart</button>
        </div>

      </div>

      
      
      <div class="col-md-12" style="margin-top: 5px; padding-right: 0;padding-left: 10px">
          <div id="chart" style="width: 99%; height: 300px;"></div>
      </div>
      
      <div class="col-md-12" style="padding-right: 0;padding-left: 10px">
          <table id="tabelmonitor" class="table table-bordered" style="margin-top: 5px; width: 99%">
            <thead style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 12px;font-weight: bold">
              <tr>
                <th style="width: 10%; vertical-align: middle;;font-size: 16px;">Kategori Audit</th>
                <th style="width: 10%; vertical-align: middle;border-left:3px solid yellow !important;font-size: 16px;">Tanggal</th>
                <th style="width: 10%; vertical-align: middle;border-left:3px solid yellow !important;font-size: 16px;">Lokasi</th>
                <th style="width: 10%; vertical-align: middle;border-left:3px solid yellow !important;font-size: 16px;">Auditor</th>
                <th style="width: 10%; vertical-align: middle;border-left:3px solid yellow !important;font-size: 16px;">Auditee</th>
                <th style="width: 20%; vertical-align: middle;border-left:3px solid yellow !important;font-size: 16px;">Note</th>
                <!-- <th style="width: 25%; vertical-align: middle;border-left:3px solid yellow !important;font-size: 16px;">Foto</th> -->
                <th style="width: 20%; vertical-align: middle;border-left:3px solid yellow !important;font-size: 16px;">Penanganan</th>
              </tr>
            </thead>
            <tbody id="tabelisi">
            </tbody>
            <tfoot>
                <tr>
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

  <div class="modal fade" id="myModal">
    <div class="modal-dialog modal-lg" style="width:1250px;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="float: right;" id="modal-title"></h4>
          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
          <br><h4 class="modal-title" id="judul_table"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="example2" class="table table-striped table-bordered table-hover" style="width: 100%;"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>Kategori</th>
                    <th>Tanggal</th>
                    <th>Lokasi</th>
                    <th>Auditee</th>
                    <th>Poin Judul</th>
                    <th>Note</th>
                    <th>Foto</th>
                    <th>Penanganan</th>
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

   <div class="modal fade" id="modalEdit" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Edit Temuan Audit</h4>
          </div>
          <div class="modal-body">
            <div class="box-body">
              <input type="hidden" value="{{csrf_token()}}" name="_token" />
              <div class="row">
                <div class="col-md-6">
                  <div class="col-md-12">
                    <label for="tanggal_edit">Tanggal</label>
                    : <span name="tanggal_edit" id="tanggal_edit"> </span>
                  </div>
                  <div class="col-md-12">
                    <label for="lokasi_edit">Lokasi</label>
                    : <span name="lokasi_edit" id="lokasi_edit"> </span>
                  </div>
                  <div class="col-md-12">
                    <label for="poin_edit">Kategori Patrol</label>
                    : <span name="poin_edit" id="poin_edit"> </span>
                  </div>
                  <div class="col-md-12">
                    <label for="pic_edit">PIC</label>
                    : <span name="pic_edit" id="pic_edit"> </span>
                  </div>
                  <div class="col-md-12">
                    <label for="note_edit">Note</label>
                    <textarea class="form-control" id="note_edit" name="note_edit"></textarea>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="col-md-12">
                    <label for="image_edit">Temuan</label>
                    : <div name="image_edit" id="image_edit"></div>
                  </div>
                </div>
              </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
          <input type="hidden" id="id_penanganan_edit">
          <button type="button" onclick="post_edit()" class="btn btn-success" data-dismiss="modal"><i class="fa fa-pencil"></i> Update Audit</button>
        </div>
      </div>
  </div>
</div>

  <div class="modal fade" id="modalPenanganan" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Detail Temuan Audit</h4>
          </div>
          <div class="modal-body">
            <div class="box-body">
              <input type="hidden" value="{{csrf_token()}}" name="_token" />
              <div class="row">
                <div class="col-md-5">
                  <div class="col-md-12">
                    <label for="lokasi">Lokasi</label>
                    : <span name="lokasi" id="lokasi"> </span>
                  </div>
                  <div class="col-md-12">
                    <label for="tanggal">Tanggal</label>
                    : <span name="tanggal" id="tanggal"> </span>
                  </div>
                  <div class="col-md-12">
                    <label for="note">Note</label>
                    : <span name="note" id="note"> </span>
                  </div>
                  <div class="col-md-12">
                    <label for="image">Temuan</label>
                    : <div name="image" id="image"></div>
                  </div>
                </div>
                <div class="col-md-7">
                  <h4>Bukti Penanganan</h4>
                  <textarea class="form-control" required="" name="penanganan" style="height: 250px;"></textarea> 
                </div>
              </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
          <input type="hidden" id="id_penanganan">
          <button type="button" onclick="update_penanganan()" class="btn btn-success" data-dismiss="modal"><i class="fa fa-pencil"></i> Submit Penanganan Audit</button>
        </div>
      </div>
  </div>
</div>

</section>
@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/accessibility.js")}}"></script>
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>

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
    $('.select2').select2();
    drawChart();
    fetchTable();
    setInterval(fetchTable, 300000);
  });

  CKEDITOR.replace('penanganan' ,{
        filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
        height: '250px'
    });

  $('.datepicker').datepicker({
    autoclose: true,
    format: "dd-mm-yyyy",
    todayHighlight: true,
  });

  function drawChart() {    
    fetchTable();

    var datefrom = $('#datefrom').val();
    var dateto = $('#dateto').val();
    var status = $('#status').val();

    var data = {
      datefrom: datefrom,
      dateto: dateto,
      status: status,
    };

    $.get('{{ url("fetch/audit_patrol/monitoring") }}', data, function(result, status, xhr) {
        if(result.status){

          var tgl = [];
          var belum_ditangani_gm = [];
          var sudah_ditangani_gm = [];
          var belum_ditangani_presdir = [];
          var sudah_ditangani_presdir = [];

          $.each(result.datas, function(key, value) {
            tgl.push(value.tanggal);
            belum_ditangani_gm.push(parseInt(value.jumlah_belum_gm));
            sudah_ditangani_gm.push(parseInt(value.jumlah_sudah_gm));
            belum_ditangani_presdir.push(parseInt(value.jumlah_belum_presdir));
            sudah_ditangani_presdir.push(parseInt(value.jumlah_sudah_presdir));
          })

          $('#chart').highcharts({
            chart: {
              type: 'column'
            },
            title: {
              text: 'Monitoring Patrol',
              style: {
                fontSize: '25px',
                fontWeight: 'bold'
              }
            },
            xAxis: {
              type: 'category',
              categories: tgl,
              lineWidth:2,
              lineColor:'#9e9e9e',
              gridLineWidth: 1,
              labels: {
                style: {
                    fontWeight:'Bold'
                  }
              }
            },
            yAxis: {
              lineWidth:2,
              lineColor:'#fff',
              type: 'linear',
                title: {
                  text: 'Total Temuan'
                },
              tickInterval: 5,  
              stackLabels: {
                  enabled: true,
                  style: {
                      fontWeight: 'bold',
                      color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                  }
              }
            },
            legend: {
              reversed: true,
              itemStyle:{
                color: "white",
                fontSize: "12px",
                fontWeight: "bold",

              }
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                point: {
                  events: {
                    click: function () {
                      ShowModal(this.category,this.series.name,result.datefrom,result.dateto);
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
                  color:  Highcharts.ColorString,
                  stacking: 'normal',
                  borderRadius: 1,
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
            series: [
              {
                  name: 'Belum Ditangani Patrol Presdir',
                  data: belum_ditangani_presdir,
                  color : '#f15c80',
                  stack : 'presdir'
              },
              {
                  name: 'Belum Ditangani GM Patrol',
                  data: belum_ditangani_gm,
                  color : '#c04f50',
                  stack : 'gm'
              },
              {
                  name: 'Sudah Ditangani Patrol Presdir',
                  data: sudah_ditangani_presdir,
                  color : '#90ed7d',
                  stack : 'presdir'
              },
              {
                  name: 'Sudah Ditangani GM Patrol',
                  data: sudah_ditangani_gm,
                  color : '#00c853',
                  stack : 'gm'
              }
            ]
          })
        } else{
          alert('Attempt to retrieve data failed');
        }
    })
  }

  function ShowModal(tgl, status, datefrom, dateto) {
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
          "url" : "{{ url("index/audit_patrol/detail") }}",
          "data" : {
            tgl : tgl,
            status : status,
            datefrom : datefrom,
            dateto : dateto,
          }
        },
      "columns": [
          {"data": "kategori", "width": "5%"},
          {"data": "tanggal" , "width": "5%"},
          {"data": "lokasi" , "width": "5%"},
          {"data": "auditee_name" , "width": "5%"},
          {"data": "point_judul", "width": "5%"},
          {"data": "note", "width": "15%"},
          {"data": "foto", "width": "20%"},
          {"data": "penanganan", "width": "25%"}
        ]    
      });

    $('#judul_table').append().empty();
    $('#judul_table').append('<center><b>Temuan Patrol Tanggal '+tgl+'</b></center>'); 
  }



  function fetchTable(){

    var datefrom = $('#datefrom').val();
    var dateto = $('#dateto').val();
    var status = $('#status').val();

    var data = {
      datefrom: datefrom,
      dateto: dateto,
      status: status
    };

    $.get('{{ url("index/audit_patrol/table") }}', data, function(result, status, xhr){
      if(result.status){

          $('#tabelmonitor').DataTable().clear();
          $('#tabelmonitor').DataTable().destroy();


          $("#tabelisi").find("td").remove();  
          $('#tabelisi').html("");
          var table = "";

          $.each(result.datas, function(key, value) {

            table += '<tr>';
            table += '<td>'+value.kategori+'</td>';
            table += '<td style="border-left:3px solid yellow">'+value.tanggal+'</span></td>';
            table += '<td style="border-left:3px solid yellow">'+value.lokasi+'</td>';
            table += '<td style="border-left:3px solid yellow">'+value.auditor_name+'</td>';
            table += '<td style="border-left:3px solid yellow">'+value.auditee_name+'</span></td>';
            table += '<td style="border-left:3px solid yellow">'+value.note+'</td>';
            // table += "<td style='border-left:3px solid yellow'><img src='"+"{{ url('files/patrol') }}/"+value.foto+"' width='150'></td>";  
            table += '<td style="border-left:3px solid yellow"><button style="width: 25%; height: 100%;" onclick="edit(\''+value.id+'\')" class="btn btn-xs btn-primary form-control"><span>Edit Audit</span></button>&nbsp;&nbsp;&nbsp;<button style="width: 50%; height: 100%;" onclick="penanganan(\''+value.id+'\')" class="btn btn-xs btn-warning form-control"><span>Lakukan Penanganan</span></button></td>';            
            table += '</tr>';
          })

          $('#tabelisi').append(table);

          $('#tabelmonitor').DataTable({
            'responsive':true,
            'paging': true,
            'lengthChange': false,
            'pageLength': 10,
            'searching': false,
            'ordering': true,
            'order': [],
            'info': false,
            'autoWidth': true,
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bAutoWidth": false,
            "processing": true
          });
      }
    })
  }

  function penanganan(id) {

    $('#modalPenanganan').modal("show");
    
    var data = {
      id : id
    }

    $.get('{{ url("index/audit_patrol/detail_penanganan") }}', data, function(result, status, xhr){

      var images = "";
      $("#image").html("");

      if (result.status) {
        $("#id_penanganan").val(id);
        $("#lokasi").text(result.audit[0].lokasi);
        $("#tanggal").text(result.audit[0].tanggal);
        $("#note").text(result.audit[0].note);
        images += '<img src="{{ url("files/patrol") }}/'+result.audit[0].foto+'" width="300">';
        $("#image").append(images);

      } else {
        openErrorGritter('Error');
      }

    }); 
  }

  function update_penanganan() {

    var data = {
      id: $("#id_penanganan").val(),
      penanganan : CKEDITOR.instances.penanganan.getData()
    };

    if (CKEDITOR.instances.penanganan.getData() == null || CKEDITOR.instances.penanganan.getData() == "") {
      openErrorGritter("Error","Penanganan Harus Diisi");
      return false;
    }

    $.post('{{ url("post/audit_patrol/penanganan") }}', data, function(result, status, xhr){
      if (result.status == true) {
        openSuccessGritter("Success","Audit Berhasil Ditangani");
        fetchTable();
        drawChart();
      } else {
        openErrorGritter("Error",result.datas);
      }
    })
  }

  function edit(id) {

    $('#modalEdit').modal("show");
    
    var data = {
      id : id
    }

    $.get('{{ url("index/audit_patrol/detail_penanganan") }}', data, function(result, status, xhr){

      var images = "";
      $("#image").html("");

      if (result.status) {
        $("#id_penanganan_edit").val(id);
        $("#tanggal_edit").text(result.audit[0].tanggal);
        $("#lokasi_edit").text(result.audit[0].lokasi);
        $("#poin_edit").text(result.audit[0].point_judul);
        $("#pic_edit").text(result.audit[0].auditee_name);
        $("#note_edit").val(result.audit[0].note);

        images += '<img src="{{ url("files/patrol") }}/'+result.audit[0].foto+'" width="300">';
        $("#image_edit").append(images);

      } else {
        openErrorGritter('Error');
      }

    }); 
  }

  
  function post_edit() {

      var data = {
        id: $("#id_penanganan_edit").val(),
        note : $("#note_edit").val(),
      };

      $.post('{{ url("post/audit_patrol/edit") }}', data, function(result, status, xhr){
        if (result.status == true) {
          openSuccessGritter("Success","Audit Berhasil Diedit");
          fetchTable();
        } else {
          openErrorGritter("Error",result.datas);
        }
      })
    }


  function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }

  Highcharts.createElement('link', {
      href: '{{ url("fonts/UnicaOne.css")}}',
      rel: 'stylesheet',
      type: 'text/css'
    }, null, document.getElementsByTagName('head')[0]);

    Highcharts.theme = {
      colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
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
        // itemStyle: {
        //   color: '#E0E0E3'
        // },
        // itemHoverStyle: {
        //   color: '#FFF'
        // },
        // itemHiddenStyle: {
        //   color: '#606063'
        // }
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