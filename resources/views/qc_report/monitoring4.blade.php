@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="https://code.highcharts.com/gantt/highcharts-gantt.js"></script>
<style type="text/css">
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

table.table-bordered{
  /*border:1px solid black;*/
}

table.table-bordered{
  /*border:1px solid black;*/
}

.rotate{
  writing-mode: vertical-rl;
  text-orientation: upright;
  vertical-align: middle;
  text-align: center;
}

.none{
  text-align: center;
  color: black;
  background-color: #fafafa;
}

.center{
  text-align: center;
}

.box{
  background-color: transparent;
  border-top: none;
}

.box-header{
  color: #fff;
}

.day1{
  background-color: #689f38;
  color: white;
}

.day2{
  background-color: #ffeb3b;
}

.day3{
  background-color: #d32f2f;
  color: white;
}

table.table-bordered > thead > tr > th{
  /*border:1px solid black;*/
  /*border:1px solid #607d8b;*/
  font-size: 14px;
  padding: 4px;
}
table.table-bordered > tbody > tr > td{
  border-collapse: collapse;
  padding:5px;
  vertical-align: middle;
  color: white;
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
<section class="content" style="padding-top:0">
  <input type="hidden" value="<?= date('Y-m-d') ?>" id="tgl">
  <div class="row">
    <div class="col-md-12">
      <div class="col-md-12">
        <div class="box">
          <!-- <div class="box-header"> -->
              <!-- <h3 class="box-title"><b style="font-size: 20pt">Monitoring <span class="text-purple">Kasus Digital</span></b></h3> -->
          <!-- </div> -->
          <div class="box-body">
              <!-- <div id="container"></div> -->
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th rowspan="2" style="vertical-align: middle;width:2%;font-size: 18px">Kategori</th>
                      <th rowspan="2" style="vertical-align: middle;width:14%;font-size: 18px">Kasus</th>
                      <?php foreach ($dept as $dep) { ?>
                        <th colspan="{{$dep->colspan}}" style="">{{$dep->department_name}}</th>                          
                      <?php } ?>
                      <!-- <th rowspan="1" colspan="2" style="vertical-align: middle;">Departemen</th> -->
                    </tr>
                    <tr>
                      <?php foreach ($dept as $dep) { ?>
                        <th style="width: 6%">Need Verification</th>
                        <th style="width: 6%">Verified</th>
                      <?php } ?>
                    </tr>
                  </thead>
                  <tbody id="tabelisi">
                    <tr>
                      <td rowspan="7" style="vertical-align: middle;text-align: center;font-size: 26px" class="rotate">
                        CPAR
                      </td>
                    </tr>
                    <tr>
                      <td>
                        CPAR Release (Staff/Leader)
                      </td>
                      <td class="none">
                        
                      </td>
                      <td class="center">
                        2
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center day1">
                        1
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        2
                      </td>
                      <td class="center">
                        1
                      </td>
                      <td class="center">
                        3
                      </td>
                      <td class="center">
                        4
                      </td>
                      <td class="center">
                        2
                      </td>
                    </tr>
                    <tr>
                      <td>
                        Chief/Foreman Verification  
                      </td>
                      <td class="center day3">
                        2
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        1
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        1
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center day2">
                        1
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        1
                      </td>
                    </tr>
                    <tr>
                      <td>
                        Manager Verification  
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        1
                      </td>
                      <td class="center">
                        2
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center day3">
                        1
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        1
                      </td>
                      <td class="center">
                        2
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        1
                      </td>
                    </tr>
                    <tr>
                      <td>
                        DGM Verification  
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        1
                      </td>
                      <td class="center day3">
                        2
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        1
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        1
                      </td>
                    </tr>
                    <tr>
                      <td>
                        GM Verification  
                      </td>
                      <td class="center day3">
                        2
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        1
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        1
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center day2">
                        1
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        1
                      </td>
                    </tr>
                    <tr>
                      <td>
                        Diterima Oleh Bagian 
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        1
                      </td>
                      <td class="center">
                        2
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center day3">
                        1
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        1
                      </td>
                      <td class="center">
                        2
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        1
                      </td>
                    </tr>
                    <tr>
                      <td rowspan="6" style="vertical-align: middle;text-align: center;font-size: 26px" class="rotate">
                        CAR
                      </td>
                    </tr>
                    <tr>
                      <td>
                        Corrective Action
                      </td>
                      <td class="center day3">
                        2
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        1
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        1
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        1
                      </td>
                    </tr>
                    <tr>
                      <td>
                        Chief / Foreman Verification
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        1
                      </td>
                      <td class="center day3">
                        2
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        1
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        1
                      </td>
                    </tr>
                    <tr>
                      <td>
                        Dept Manager Verification
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        2
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                    </tr>
                    <tr>
                      <td>
                        DGM Verification
                      </td>
                      <td class="center day3">
                        2
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        1
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        1
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center day2">
                        1
                      </td>
                      <td class="center">
                        1
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                    </tr>
                    <tr>
                      <td>
                        GM Verification
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center day3">
                        1
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                    </tr>
                    <tr>
                      <td rowspan="2" style="vertical-align: middle;text-align: center;font-size: 20px">
                        QA Ver.
                      </td>
                    </tr>
                    <tr>
                      <td>
                        Check Corrective Action
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2" style="text-align: center;font-size: 20px">
                        Total
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                      <td class="center">
                        0
                      </td>
                    </tr>
                  </tbody>
                </table>
            </div>
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
    // drawgantt();
      // fetchTable();
      // setInterval(fetchTable, 10000);
  });

  function fetchTable(){
      var data = {
        tgl : $('#tgl').val()
      }
      $.get('{{ url("index/qc_report/fetchMonitoring") }}', data, function(result, status, xhr){
        if(xhr.status == 200){
          if(result.status){

            // $("#tabelmonitor").html("");
            $("#tabelisi").find("td").remove();  

            // foreach()

            $.each(result.datas, function(key, value) {
                if (value.cpar_no) {
                  $("#tabelisi").append("<tr><td>"+value.cpar_no+"</td><td>"+value.detail_problem+"</td></tr>");
                }
            })

          }
        }
      })
  }


  $('.datepicker').datepicker({
    format: "yyyy-mm",
    startView: "months", 
    minViewMode: "months",
    autoclose: true
  });  

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
@stop