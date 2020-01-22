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
}

table.table-bordered{
  border:1px solid black;
}


table.table-bordered > thead > tr > th{
  /*border:1px solid black;*/
  border:1px solid #607d8b;
  font-size: 23px;
}
table.table-bordered > tbody > tr > td{
  border-collapse: collapse;
  padding:10px;
  vertical-align: middle;
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
  <input type="hidden" value="<?= date('Y-m-d') ?>" id="tgl">
  <div class="row">
    <div class="col-md-12">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
              <h3 class="box-title"><b style="font-size: 18pt">Monitoring <span class="text-purple">Eksternal Komplain</span></b></h3>
          </div>
          <div class="box-body">
              <!-- <div id="container"></div> -->
              <div class="table-responsive">
                <table class="table no-margin" id="tabelmonitor">
                  <thead>
                    <tr>
                      <th rowspan="3" style="vertical-align: middle;width: 100px">CPAR</th>
                      <th rowspan="3" style="vertical-align: middle;">Komplain</th>
                      <th width="35%" colspan="48" style="">{{$fy[0]->fiscal_year}}</th>
                    </tr>
                    <tr>
                      <?php foreach ($bulan as $bul) { ?>
                        <th colspan="{{$bul->colspan}}" style="">{{$bul->bulan}}</th>                          
                      <?php } ?>
                    </tr>
                    <tr>
                      <?php foreach ($week as $minggu) { ?>
                        <th width="5%">{{$minggu->week_name}}</th>                          
                      <?php } ?>
                    </tr>
                  </thead>
                  <tbody id="tabelisi">
                    <tr>
                      <td style="">
                        01/196.E/XI/2019   
                      </td>
                      <td style="">
                        Didalam Case Ditemukan Sekat
                      </td>
                      <td colspan="4">
                        <div class="progress progress-md active" style="height: 40px">
                          <div class="progress-bar" role="progressbar" aria-valuenow="30%" aria-valuemin="0" aria-valuemax="100" style="width: 30%">
                          </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td style="">
                        02/196.E/XI/2019   
                      </td>
                      <td style="">
                        Cover Case Lepas
                      </td>
                      <td colspan="5">
                        
                      </td>
                      <td colspan="4">
                        <div class="progress progress-md active" style="height: 40px">
                          <div class="progress-bar" role="progressbar" aria-valuenow="60%" aria-valuemin="0" aria-valuemax="100" style="width: 60%">
                          </div>
                        </div>
                      </td>
                      <td colspan="70">
                      </td>
                    </tr>
                    
                    <tr style=""><td colspan="70"><h3><b>Monitoring <span class="text-purple">Supplier Komplain</span></b></h3></td></tr>
                    
                    <tr style="border-top: 1px orange">
                      <td style="">
                        01/196.S/XI/2019   
                      </td>
                      <td style="">
                        Material Awal Error
                      </td>
                      <td colspan="10">
                        
                      </td>
                      <td colspan="4">
                        <div class="progress progress-md active" style="height: 40px">
                          <div class="progress-bar" role="progressbar" aria-valuenow="60%" aria-valuemin="0" aria-valuemax="100" style="width: 60%">
                          </div>
                        </div>
                      </td>
                      <td colspan="70">
                      </td>
                    </tr>

                    <tr style="border-top: none">
                      <td style="">
                        02/196.S/XI/2019   
                      </td>
                      <td style="">
                        Material Tergores
                      </td>
                      <td colspan="8">
                      </td>
                      <td colspan="4">
                        <div class="progress progress-md active" style="height: 40px">
                          <div class="progress-bar" role="progressbar" aria-valuenow="60%" aria-valuemin="0" aria-valuemax="100" style="width: 60%">
                          </div>
                        </div>
                      </td>
                      <td colspan="70">
                      </td>
                    </tr>

                    <tr><td colspan="70"><h3><b>Monitoring <span class="text-purple">Internal Komplain</span></b></h3></td></tr>
                    
                    <tr>
                      <td style="">
                        01/196.I/XI/2019   
                      </td>
                      <td style="">
                        A23 bell Packed Kizu Inside 19 %
                      </td>
                      <td colspan="13">
                        
                      </td>
                      <td colspan="4">
                        <div class="progress progress-md active" style="height: 40px">
                          <div class="progress-bar" role="progressbar" aria-valuenow="60%" aria-valuemin="0" aria-valuemax="100" style="width: 60%">
                          </div>
                        </div>
                      </td>
                      <td colspan="70">
                      </td>
                    </tr>
                  </tbody>
                </table>
            </div>

            <div class="box-footer">
              <div class="row">
                <div class="col-sm-3 col-xs-6">
                  <div class="description-block border-right">
                    <span class="description-percentage text-green"><i class="fa fa-caret-up"></i> 17%</span>
                    <h5 class="description-header">$35,210.43</h5>
                    <span class="description-text">TOTAL REVENUE</span>
                  </div>
                  
                </div>
                <div class="col-sm-3 col-xs-6">
                  <div class="description-block border-right">
                    <span class="description-percentage text-yellow"><i class="fa fa-caret-left"></i> 0%</span>
                    <h5 class="description-header">$10,390.90</h5>
                    <span class="description-text">TOTAL COST</span>
                  </div>
                  
                </div>
                <div class="col-sm-3 col-xs-6">
                  <div class="description-block border-right">
                    <span class="description-percentage text-green"><i class="fa fa-caret-up"></i> 20%</span>
                    <h5 class="description-header">$24,813.53</h5>
                    <span class="description-text">TOTAL PROFIT</span>
                  </div>
                  
                </div>
                <div class="col-sm-3 col-xs-6">
                  <div class="description-block">
                    <span class="description-percentage text-red"><i class="fa fa-caret-down"></i> 18%</span>
                    <h5 class="description-header">1200</h5>
                    <span class="description-text">GOAL COMPLETIONS</span>
                  </div>
                  
                </div>
              </div>
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