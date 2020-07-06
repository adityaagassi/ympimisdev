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
  tfoot>tr>td{
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
    background-color: #605ca8;
    color: white;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid black;
    vertical-align: middle;
    padding:0;
    background-color: #fffcb7; 
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
  .description-block {
    margin-top: 0px
  }

  .panel {
    margin-bottom: 0px !important;
    border-top-color: #605ca8;
  }
  .box-header:hover {
    cursor: pointer;
    /*background-color: #3c3c3c;*/
  }

  .alert2 {
    -webkit-animation: alerts 1s infinite;  /* Safari 4+ */
    -moz-animation: alerts 1s infinite;  /* Fx 5+ */
    -o-animation: alerts 1s infinite;  /* Opera 12+ */
    animation: alerts 1s infinite;  /* IE 10+, Fx 29+ */
  }
  
  @-webkit-keyframes alerts {
    0%, 49% {
      background: #fffcb7; 
      color: #333;
    }
    50%, 100% {
      background-color: #262626;
      color: #fff;
    }
  }
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
  <div class="row">
    <div class="col-xs-2 pull-left">
      <button id="btn_hydrant" class="btn btn-primary" onclick="change_mode('hydrant')"><i class="fa fa-tint"></i>&nbsp; HYDRANT</button>
      <button id="btn_apar" class="btn btn-success" onclick="change_mode('apar')"><i class="fa fa-fire-extinguisher"></i>&nbsp; APAR</button>
    </div>

    <div class="col-xs-2 pull-right">
      <div class="input-group date">
        <div class="input-group-addon bg-purple" style="border: none;">
          <i class="fa fa-calendar"></i>
        </div>
        <input type="text" class="form-control datepicker" id="bulan" onchange="drawTable()" placeholder="Pilih Bulan">
      </div>
    </div>
    <div class="col-xs-12">
      <h2 style="color: white; text-align: center" id="judul"></h2>
      <div class="col-sm-10 col-xs-6 col-xs-offset-1">
        <div class="description-block border-right">
          <span class="description-text">
            <span style="color: #f55359; font-weight: bold; font-size: 20pt" id="datas"> 0 ITEM MUST CHECKED</span>
          </span>
        </div>
      </div>
      <table class="table table-bordered" width="100%">
        <thead>
          <tr>
            <th>APAR CODE</th>
            <th>APAR NAME</th>
            <th>LOCATION</th>
            <th>LAST CHECK</th>
            <th>MUST CHECK BEFORE</th>
          </tr>
        </thead>
        <tbody id='body'>
        </tbody>
        <tfoot id="hasil">
        </tfoot>
      </table>
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
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script type="text/javascript">
  Date.prototype.getWeek = function (dowOffset) {
    /*getWeek() was developed by Nick Baicoianu at MeanFreePath: http://www.meanfreepath.com */

    dowOffset = typeof(dowOffset) == 'int' ? dowOffset : 0; //default dowOffset to zero
    var newYear = new Date(this.getFullYear(),0,1);
    var day = newYear.getDay() - dowOffset; //the day of week the year begins on
    day = (day >= 0 ? day : day + 7);
    var daynum = Math.floor((this.getTime() - newYear.getTime() - 
      (this.getTimezoneOffset()-newYear.getTimezoneOffset())*60000)/86400000) + 1;
    var weeknum;
    //if the year starts before the middle of a week
    if(day < 4) {
      weeknum = Math.floor((daynum+day-1)/7) + 1;
      if(weeknum > 52) {
        nYear = new Date(this.getFullYear() + 1,0,1);
        nday = nYear.getDay() - dowOffset;
        nday = nday >= 0 ? nday : nday + 7;
            /*if the next year starts before the middle of
            the week, it is week #1 of that year*/
            weeknum = nday < 4 ? 1 : 53;
          }
        }
        else {
          weeknum = Math.floor((daynum+day-1)/7);
        }
        return weeknum;
      };
    </script>

    <script>

      color_arr = ['#fc6042', '#fcb941', '#eee657', '#2cc990', '#2c82c9'];
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $("#btn_apar").hide();
      var modes = "apar";
      var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

      jQuery(document).ready(function() {
        $('body').toggleClass("sidebar-collapse");
        var now = new Date();
        get_apar(now);
      });

      function get_apar(dt_param) {
        mon = dt_param.getMonth()+1;

        yr      = dt_param.getFullYear(),
        month   = (dt_param.getMonth()+1) < 10 ? '0' + (dt_param.getMonth()+1) : (dt_param.getMonth()+1),
        day     = dt_param.getDate()  < 10 ? '0' + dt_param.getDate()  : dt_param.getDate(),
        newDate = yr + '-' + month + '-' + day;

        var checked = 0;
        var all_check = 0;

        $("#judul").text("APAR Check on "+ dt_param.toLocaleString('default', { month: 'long' }));

        $("#body").empty();
        $('#hasil').empty();
        var body = "";
        var hasil_body = "";

        var data = {
          mon: mon,
          dt: newDate
        }

        $.get('{{ url("fetch/maintenance/apar/list/monitoring") }}', data, function(result, status, xhr){
          checked = 0;

          $.each(result.check_list, function(index, value){
            bg = "";

            var nowdate = new Date();
            var entrydate = new Date(value.entry);

            // if (value.cek == 1) {
            //   bg = "style='background-color:#54f775'";
            //   checked++;
            // }

            body += "<tr>";
            body += "<td style='background-color:"+color_arr[value.wek - 1]+"' >"+value.utility_code+"</td>";
            body += "<td style='background-color:"+color_arr[value.wek - 1]+"' >"+value.utility_name+"</td>";
            body += "<td style='background-color:"+color_arr[value.wek - 1]+"' >"+value.location+"</td>";
            body += "<td style='background-color:"+color_arr[value.wek - 1]+"' >"+(value.last_check || '-')+"</td>";
            body += "<td style='background-color:"+color_arr[value.wek - 1]+"' >"+value.cek_before+"</td>";
            body += "</tr>";
          })

          // if (result.check_list.length == 0) {
            body += "<tr>";
            body += "<td style='background: transparent'>&nbsp;</td>";
            body += "<td style='background: transparent'>&nbsp;</td>";
            body += "<td style='background: transparent'>&nbsp;</td>";
            body += "<td style='background: transparent'>&nbsp;</td>";
            body += "<td style='background: transparent'>&nbsp;</td>";
            body += "</tr>";
          // }


          hasil_body = '<tr>';
          hasil_body = '<td style="border: 0px !important">&nbsp;</td>';
          hasil_body = '<td style="border: 0px !important">&nbsp;</td>';
          hasil_body = '<td style="border: 0px !important">&nbsp;</td>';
          hasil_body = '<td style="border: 0px !important">&nbsp;</td>';
          hasil_body = '<td style="border: 0px !important">&nbsp;</td>';
          hasil_body = '</tr>';

          $.each(result.hasil_check, function(index, value){
            bg = "style='background-color: #98f25c'";            

            hasil_body += "<tr>";
            hasil_body += "<td "+bg+">"+value.utility_code+"</td>";
            hasil_body += "<td "+bg+">"+value.utility_name+"</td>";
            hasil_body += "<td "+bg+">"+value.location+"</td>";
            hasil_body += "<td "+bg+">"+(value.last_check || '-')+"</td>";
            hasil_body += "<td "+bg+">-</td>";
            hasil_body += "</tr>";
          })

          $("#datas").text(result.check_list.length+" ITEM MUST CHECKED");

          $("#body").append(body);
          $("#hasil").append(hasil_body);
        })
      }

      function drawTable() {
        if (modes == "apar") {
          mon = $("#bulan").val();
          mon = mon.split("-");

          var dt = new Date(mon[1], mon[0] - 1, '01');

          if(isValidDate(dt)) {
            get_apar(dt);
          } else {
            get_apar(new Date());
          }
        } else {
          drawHydrant();
        }
      }


      function drawHydrant() {
       mon = $("#bulan").val();
       mon = mon.split("-");

       var dt = new Date(mon[1], mon[0] - 1, '01');

       if(isValidDate(dt)) {
       } else {
        dt = new Date();
      }

      mon = dt.getMonth()+1;

      var checked = 0;
      var all_check = 0;

      $("#judul").text("HYDRANT Check on "+ dt.toLocaleString('default', { month: 'long' }));

      $("#body").empty();
      var body = "";

      var data = {
        mon: mon
      }

      $.get('{{ url("fetch/maintenance/hydrant/list/monitoring") }}', data, function(result, status, xhr){
        $.each(result.check_list, function(index, value){
          bg = "";

          var nowdate = new Date();

          if (value.cek == 1) {
            bg = "style='background-color:#54f775'";
            checked++;
          } else {
            if (value.week <= Math.floor(nowdate.getDate() / 7)) {
              bg = "class='alert'";
            } else {
              bg = "";
            }
          }

          body += "<tr>";
          body += "<td "+bg+">"+value.utility_code+"</td>";
          body += "<td "+bg+">"+value.utility_name+"</td>";
          body += "<td "+bg+">"+value.location+"</td>";
          body += "<td "+bg+">"+(value.last_check || '-')+"</td>";
          body += "<td "+bg+">"+(value.exp_date2 || '-')+"</td>";
          body += "</tr>";

        })


        // $("#datas_check").text("CHECKED "+checked);
        $("#datas").text(result.check_list.length+" ITEM MUST CHECKED");

        $("#body").append(body);
      })

    }

    function change_mode(mode) {
      modes = mode;
      console.log(mode);
      if (mode == "hydrant") {
        $("#btn_hydrant").hide();
        $("#btn_apar").show();

        drawHydrant();
      } else {
        $("#btn_apar").hide();
        $("#btn_hydrant").show();

        drawTable();
      }
    }

    var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

    function isValidDate(d) {
      return d instanceof Date && !isNaN(d);
    }

    function pad(num, size) {
      var s = num+"";
      while (s.length < size) s = "0" + s;
      return s;
    }

    $(".datepicker").datepicker( {
      autoclose: true,
      format: "mm-yyyy",
      viewMode: "months", 
      minViewMode: "months"
    });

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
  @endsection