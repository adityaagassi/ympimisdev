@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
/*thead input {
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
}*/
table.table-bordered{
  border:1px solid rgb(150,150,150);
}
table.table-bordered > thead > tr > th{
  border:1px solid rgb(150,150,150);
  text-align: center;
}
table.table-bordered > tbody > tr > td{
  border:1px solid rgb(150,150,150);
  border-top: 2px solid white;
  vertical-align: middle;
  text-align: center;
  padding:1px;
}
table.table-bordered > tfoot > tr > th{
  border:1px solid rgb(150,150,150);
  padding:0;
}
table.table-bordered > tbody > tr > td > p{
  color: #abfbff;
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
<section class="content" style="padding: 0px;">
  <div class="row">
    <div class="col-xs-12">
      <table id="tablemonitor" class="table table-bordered">
        <thead style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;">
          <tr>
            <th style="width: 10%; padding: 0;vertical-align: middle" rowspan="2">CPAR</th>
            <th style="width: 40%; padding: 0;" colspan="7">CPAR</th>
            <th style="width: 40%; padding: 0;" colspan="8">CAR</th>
          </tr>
          <tr>
            <th style="width: 6%; padding: 0;">Staff</th>
            <th style="width: 6%; padding: 0;">Leader</th>
            <th style="width: 6%; padding: 0;">Chief</th>
            <th style="width: 6%; padding: 0;">Foreman</th>
            <th style="width: 6%; padding: 0;">Manager</th>
            <th style="width: 6%; padding: 0;">DGM</th>
            <th style="width: 6%; padding: 0;">GM</th>

            <th style="width: 6%; padding: 0;">Manager</th>
            <th style="width: 6%; padding: 0;">Staff</th>
            <th style="width: 6%; padding: 0;">Leader</th>
            <th style="width: 6%; padding: 0;">Chief</th>
            <th style="width: 6%; padding: 0;">Foreman</th>
            <th style="width: 6%; padding: 0;">Manager</th>
            <th style="width: 6%; padding: 0;">DGM</th>
            <th style="width: 6%; padding: 0;">GM</th>
          </tr>
        </thead>
        <tbody id="tablebody">
        </tbody>
        <tfoot>
        </tfoot>
      </table>
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
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  jQuery(document).ready(function() {
      fetchTable();
      setInterval(fetchTable, 10000);
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