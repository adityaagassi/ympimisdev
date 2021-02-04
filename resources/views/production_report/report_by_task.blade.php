@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  table.table-bordered{
  border:1px solid rgb(150,150,150);
}
table.table-bordered > thead > tr > th{
  border:1px solid white !important;
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
  border:1px solid white;
  padding:0;
}
table.table-bordered > tbody > tr > td > p{
  color: #abfbff;
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
#tableActivityWeekly{
  font-size: 1vw;
}
#tableActivityMonthly{
  font-size: 1vw;
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
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0; padding-bottom: 0">
  <div class="row">
      <div class="col-md-12" style="padding: 1px !important">
        <div class="col-md-2">
          <div class="input-group date">
            <div class="input-group-addon bg-green">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" class="form-control datepicker" id="month" placeholder="Pilih Bulan">
          </div>
        </div>
        <div class="col-md-2">
            <div class="input-group">
              <div class="input-group-addon bg-blue">
                <i class="fa fa-search"></i>
              </div>
              <select class="form-control select2" id="activity_type" data-placeholder="Pilih Aktivitas" style="border-color: #605ca8" >
                  <option value=""></option>
                  @foreach($activity_type as $activity_type)
                    @if($activity_type == 'Laporan Aktivitas')
                      <option value="{{ $activity_type }}">Audit IK</option>
                    @elseif($activity_type == 'Audit')
                      <option value="{{ $activity_type }}">Audit CAR NG Jelas</option>
                    @elseif($activity_type == 'Pengecekan Foto')
                      <option value="{{ $activity_type }}">Daily Check FG / KD</option>
                    @elseif($activity_type == 'Pengecekan')
                      <option value="{{ $activity_type }}">Cek Produk Pertama</option>
                    @else
                      <option value="{{ $activity_type }}">{{ $activity_type }}</option>
                    @endif
                  @endforeach
                </select>
            </div>
        </div>

        <div class="col-xs-2">
          <button class="btn btn-success btn-sm" onclick="fillTable()">Update Chart</button>
        </div>
      </div>
      <div class="col-md-12" id="weekly">
        
      </div>

      <div class="col-md-12" id="monthly">
        
      </div>

      <div class="col-md-12" id="daily">
        
      </div>
  </div>
</section>


@endsection

@section('scripts')
<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<!-- <script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script> -->

<!-- <script src="{{ url("js/dataTables.buttons.min.js")}}"></script> -->
<!-- <script src="{{ url("js/buttons.flash.min.js")}}"></script> -->
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<!-- <script src="{{ url("js/buttons.html5.min.js")}}"></script> -->
<!-- <script src="{{ url("js/buttons.print.min.js")}}"></script> -->

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

    $('.datepicker').datepicker({
      // <?php $tgl_max = date('Y') ?>
      autoclose: true,
      format: "yyyy-mm",
      startView: "months", 
      minViewMode: "months",
      autoclose: true,
      
      // endDate: '<?php echo $tgl_max ?>'

    });

    $('.select2').select2({
      language : {
        noResults : function(params) {
          return "There is no date";
        }
      }
    });

    fillTable();
  });

  function fillTable() {
    var month = $('#month').val();
    var activity_type = $('#activity_type').val();

    var data = {
      month : month,
      activity_type : activity_type
    }
    // $('#bodyActivityWeekly').empty();
    // $('#headActivityWeekly').empty();

    $('#bodyActivityMonthly').empty();
    $('#judul_table').empty();

    $.get('{{ url("index/production_report/fetchReportByTask/".$id) }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){

          var headWeekly = '';
          var tableWeekly = [];
          var isiWeekly = '';

          var headMonthly = '';
          var bodyMonthly = [];
          var isiMonthly = '';

          var headDaily = '';
          var tableDaily = [];
          var isiDaily = '';

          var judul,judul2;
          var judul_table = [];

          var frequency2 = [];

          var hasil = '';

          var leader = [], hasil = [], frequency,week = [], day = [];

          //HEAD WEEKLY
          headWeekly += "<tr style='background-color: #757575; color: rgb(0,0,0); font-size: 12px;font-weight: bold'>";
          headWeekly += "<th style='border:1px solid white;width: 15%; padding: 0;vertical-align: middle;font-size: 16px;'>Leader</th>";
          $.each(result.week,function(key,value2){
            headWeekly += "<th style='border:1px solid white;width: 5%; padding: 0;vertical-align: middle;font-size: 16px;'>"+value2.week_name+"</th>";
            week.push(value2.week_name);
          });
          headWeekly += "<th style='border:1px solid white;width: 15%; padding: 0;vertical-align: middle;font-size: 16px;'>Plan</th>";
          headWeekly += "<th style='border:1px solid white;width: 15%; padding: 0;vertical-align: middle;font-size: 16px;'>Actual</th>";
          headWeekly += "<th style='border:1px solid white;width: 15%; padding: 0;vertical-align: middle;font-size: 16px;'>% Actual</th>";
          headWeekly += "</tr>";

          //HEAD MONTHLY
          headMonthly += "<tr style='background-color: #757575; color: rgb(0,0,0); font-size: 12px;font-weight: bold'>";
          headMonthly += "<th style='border:1px solid white;width: 15%; padding: 0;vertical-align: middle;font-size: 16px;'>Leader</th>";
          headMonthly += "<th style='border:1px solid white;width: 15%; padding: 0;vertical-align: middle;font-size: 16px;'>Plan</th>";
          headMonthly += "<th style='border:1px solid white;width: 15%; padding: 0;vertical-align: middle;font-size: 16px;'>Actual</th>";
          headMonthly += "<th style='border:1px solid white;width: 15%; padding: 0;vertical-align: middle;font-size: 16px;'>% Actual</th>";
          headMonthly += "</tr>";

          //HEAD DAILY
          headDaily += "<tr style='background-color: #757575; color: rgb(0,0,0); font-size: 12px;font-weight: bold'>";
          headDaily += "<th rowspan='2' style='border:1px solid white;width: 15%; padding: 0;vertical-align: middle;font-size: 16px;'>Leader</th>";
          headDaily += "<th colspan='"+result.day.length+"' style='border:1px solid white;width: 15%; padding: 0;vertical-align: middle;font-size: 16px;'>Tanggal</th>";
          headDaily += "<th rowspan='2' style='border:1px solid white;width: 15%; padding: 0;vertical-align: middle;font-size: 16px;'>Plan</th>";
          headDaily += "<th rowspan='2' style='border:1px solid white;width: 15%; padding: 0;vertical-align: middle;font-size: 16px;'>Actual</th>";
          headDaily += "<th rowspan='2' style='border:1px solid white;width: 15%; padding: 0;vertical-align: middle;font-size: 16px;'>% Actual</th>";
          headDaily += "</tr>";

          headDaily += "<tr style='background-color: #757575; color: rgb(0,0,0); font-size: 12px;font-weight: bold'>";
          $.each(result.day,function(key,value3){
            headDaily += "<th style='border:1px solid white;width: 2%; padding: 0;vertical-align: middle;font-size: 16px;'>"+value3.week_date.substr(8, 2);+"</th>";
            day.push(value3.week_date);
          });
          headDaily += "</tr>";

          $.each(result.datas,function(key,valuejudul){
            judul = valuejudul.activity_name;
              if (judul != judul2) {
                if (valuejudul.frequency != 'Conditional') {
                  judul_table.push(valuejudul.activity_name);
                  judul2 = valuejudul.activity_name;
                }
              }
          })

          $.each(result.datas, function(key, value) {
            var a = 0;

            frequency = value.frequency;
            hasil = value.hasil;

            //WEEKLY
            if (frequency == 'Weekly') {
              $('#weekly').show();
              $('#monthly').hide();
              $('#daily').hide();

              var jumlah = hasil.split(',');

              frequency2.push(value.frequency);

              for(var jdl = 0; jdl < judul_table.length; jdl++){
                if (value.activity_name == judul_table[jdl]) {
                  tableWeekly[jdl] += '<tr>';
                  tableWeekly[jdl] += '<td style="border:1px solid white">'+value.leader_dept+'</td>';
                  for(var i = 0;i < week.length; i++){
                    for(var j = 0;j < jumlah.length; j++){
                      if (jumlah[j] == week[i]) {
                        tableWeekly[jdl] += '<td style="border:1px solid white;background-color:#00a65a">'+jumlah[j]+'</td>';
                        a++;
                        break;
                      }
                    }
                    if(jumlah[j] != week[i]){
                      tableWeekly[jdl] += '<td style="border:1px solid white;background-color:#dd4b39 ">0</td>';
                    }
                  }
                  tableWeekly[jdl] += '<td style="border:1px solid white">'+value.plan+'</td>';
                  tableWeekly[jdl] += '<td style="border:1px solid white">'+a+'</td>';
                  tableWeekly[jdl] += '<td style="border:1px solid white">'+parseInt(((a/value.plan)*100))+' %</td>';
                  tableWeekly[jdl] += '</tr>';
                }
              }
            }

            //MONTHLY
            if (frequency == 'Monthly') {

              $('#weekly').hide();
              $('#monthly').show();
              $('#daily').hide();

              for(var jdl = 0; jdl < judul_table.length; jdl++){
                if (frequency == 'Monthly') {
                  frequency2.push(value.frequency);
                }
                if (value.activity_name == judul_table[jdl]) {
                  bodyMonthly[jdl] += '<tr>';
                  bodyMonthly[jdl] += '<td style="border:1px solid white">'+value.leader_dept+'</td>';
                  bodyMonthly[jdl] += '<td style="border:1px solid white">'+value.plan+'</td>';
                  bodyMonthly[jdl] += '<td style="border:1px solid white">'+value.hasil+'</td>';
                  bodyMonthly[jdl] += '<td style="border:1px solid white">'+parseInt(((value.hasil/value.plan)*100))+' %</td>';
                  bodyMonthly[jdl] += '</tr>';
                }
              }
            }

            if (frequency == 'Daily') {
              $('#weekly').hide();
              $('#monthly').hide();
              $('#daily').show();

              var jumlahdaily = hasil.split(',');
              console.log(hasil);

              frequency2.push(value.frequency);

              for(var jdl = 0; jdl < judul_table.length; jdl++){
                if (value.activity_name == judul_table[jdl]) {
                  tableDaily[jdl] += '<tr>';
                  tableDaily[jdl] += '<td style="border:1px solid white">'+value.leader_dept+'</td>';
                  for(var i = 0;i < day.length; i++){
                    for(var j = 0;j < jumlahdaily.length; j++){
                      if (jumlahdaily[j] == day[i]) {
                        tableDaily[jdl] += '<td style="border:1px solid white;background-color:#00a65a;width: 1%;">'+jumlahdaily[j].substr(8, 2);+'</td>';
                        a++;
                        break;
                      }
                    }
                    if(jumlahdaily[j] != day[i]){
                      tableDaily[jdl] += '<td style="border:1px solid white;background-color:#dd4b39;width: 1%; ">0</td>';
                    }
                  }
                  tableDaily[jdl] += '<td style="border:1px solid white">'+value.plan+'</td>';
                  tableDaily[jdl] += '<td style="border:1px solid white">'+a+'</td>';
                  tableDaily[jdl] += '<td style="border:1px solid white">'+parseInt(((a/value.plan)*100))+' %</td>';
                  tableDaily[jdl] += '</tr>';
                }
              }
            }
          });

               

          //isi weekly
          for(var weekly = 0; weekly < judul_table.length ; weekly++){
            if (frequency2[weekly] == 'Weekly') {
              isiWeekly += "<center><h3 style='color: white' id='judul_table'>"+judul_table[weekly]+" <br> "+result.monthTitle+"</h3></center>";
              isiWeekly += '<table id="tableActivityWeekly" class="table table-bordered" style="margin-top: 5px; width: 99%">';
              isiWeekly += '<thead id="headActivityWeekly'+weekly+'">';
              isiWeekly += headWeekly+'</thead>';
              isiWeekly += '<tbody id="bodyActivityWeekly'+weekly+'">';
              isiWeekly += tableWeekly[weekly];
              isiWeekly += '</tbody></table>';
            }
          }
          $('#weekly').html(isiWeekly);

          //isi monthly
          for(var monthly = 0; monthly < judul_table.length ; monthly++){
            if (frequency2[monthly] == 'Monthly') {
              isiMonthly += "<center><h3 style='color: white' id='judul_table'>"+judul_table[monthly]+" <br> "+result.monthTitle+"</h3></center>";
              isiMonthly += '<table id="tableActivitymonthly" class="table table-bordered" style="margin-top: 5px; width: 99%">';
              isiMonthly += '<thead id="headActivitymonthly'+monthly+'">';
              isiMonthly += headMonthly;
              isiMonthly += '</thead><tbody id="bodyActivitymonthly'+monthly+'">';
              isiMonthly += bodyMonthly[monthly];
              isiMonthly += '</tbody></table>';
            }
          }
          $('#monthly').html(isiMonthly);

          for(var daily = 0; daily < judul_table.length ; daily++){
            if (frequency2[daily] == 'Daily') {
              isiDaily += "<center><h3 style='color: white' id='judul_table'>"+judul_table[daily]+" <br> "+result.monthTitle+"</h3></center>";
              isiDaily += '<table id="tableActivitymonthly" class="table table-bordered" style="margin-top: 5px; width: 99%">';
              isiDaily += '<thead id="headActivityDaily'+daily+'">';
              isiDaily += headDaily;
              isiDaily += '</thead><tbody id="bodyActivityDaily'+daily+'">';
              isiDaily += tableDaily[daily];
              isiDaily += '</tbody></table>';
            }
          }
          $('#daily').html(isiDaily);
        }
      }
    });
  }

</script>
