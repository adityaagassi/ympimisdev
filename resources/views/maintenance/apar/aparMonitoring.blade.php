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

  .alert {
    /*width: 50px;
    height: 50px;*/
    -webkit-animation: alert 1s infinite;  /* Safari 4+ */
    -moz-animation: alert 1s infinite;  /* Fx 5+ */
    -o-animation: alert 1s infinite;  /* Opera 12+ */
    animation: alert 1s infinite;  /* IE 10+, Fx 29+ */
  }
  
  @-webkit-keyframes alert {
    0%, 49% {
      /*background: rgba(0, 0, 0, 0);*/
      background: #fffcb7; 
      /*opacity: 0;*/
    }
    50%, 100% {
      background-color: rgb(240, 46, 49);
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
    <div class="col-xs-12">
      <h2 style="color: white; text-align: center">APAR Check on April</h2>

      <table class="table table-bordered" width="100%">
        <thead>
          <tr>
            <th>APAR CODE</th>
            <th>APAR NAME</th>
            <th>LOCATION</th>
            <th>LAST CHECK</th>
            <th>EXP. DATE</th>
          </tr>
        </thead>
        <tbody id='body'>
        </tbody>
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
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      jQuery(document).ready(function() {
        $('body').toggleClass("sidebar-collapse");
        get_apar()
      });

      function get_apar() {
        var dt = new Date();
        mon = dt.getMonth()+1;

        $("#body").empty();
        var body = "";

        var data = {
          order: 'exp_date2',
          order2: 'asc',
        }

        $.get('{{ url("fetch/maintenance/apar/list") }}', data, function(result, status, xhr){

          $.each(result.apar, function(index, value){
            if (value.remark == 'APAR') {
              var dt = value.exp_date.split('-').join('/');
              if (value.last_check) {
                var dt_check = value.last_check.split(" ")[0].split('-').join('/');
              } else {
                var dt_check = "1999/01/01";
              }

              var mydate = new Date(dt);
              var cekdate = new Date(dt_check);
              var nowdate = new Date();

              if (mydate.getWeek() <= nowdate.getWeek()) {
                bg = "class='alert'";
              } else {
                bg = "";
              }

              if (value.location == "Factory I" && mon % 2 === 0) {
                body += "<tr>";
                body += "<td "+bg+">"+value.utility_code+"</td>";
                body += "<td "+bg+">"+value.utility_name+"</td>";
                body += "<td "+bg+">"+value.location+"</td>";
                body += "<td "+bg+">"+(value.last_check || '-')+"</td>";
                body += "<td "+bg+">"+value.exp_date2+"</td>";
                body += "</tr>";
              } else {
                if (value.location == "Factory II" && mon % 2 === 1) {
                  body += "<tr>";
                  body += "<td "+bg+">"+value.utility_code+"</td>";
                  body += "<td "+bg+">"+value.utility_name+"</td>";
                  body += "<td "+bg+">"+value.location+"</td>";
                  body += "<td "+bg+">"+(value.last_check || '-')+"</td>";
                  body += "<td "+bg+">"+value.exp_date2+"</td>";
                  body += "</tr>";

                }
              }
            }
          })

          $("#body").append(body);
        })
      }

      var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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