@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

     thead input {
          width: 100%;
          padding: 3px;
          box-sizing: border-box;
     }
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
          background-color: #a488aa;
     }
     table.table-bordered > tbody > tr > td{
          border:1px solid black;
          vertical-align: middle;
          padding:0;
     }
     table.table-bordered > tfoot > tr > th{
          border:1px solid black;
          padding:0;
     }
     td{
          overflow:hidden;
          text-overflow: ellipsis;
     }
     #loading { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
     <h1>
          GA - Report<span class="text-purple"> </span>
     </h1>
</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
     <p style="position: absolute; color: White; top: 45%; left: 35%;">
          <span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-spinner"></i></span>
     </p>
</div>
<section class="content">
     <div class="row">
          <div class="col-xs-12">
               <div class="box box-solid">
                    <div class="box-body">
                         <div class="row">
                              <div class="col-xs-7">
                                   <form class="form-horizontal">
                                        <div class="form-group">
                                             <label for="datepicker" class="col-sm-2 control-label">Tanggal</label>
                                             <div class="col-sm-3">
                                                  <input type="text" class="form-control datepicker" id="datepicker" placeholder="Select date" onchange="changeTanggal(); ">
                                             </div>
                                        </div>

                                        <div class="form-group">
                                             <label class="col-sm-2 control-label">Total Makan</label>
                                             <div class="col-sm-3">
                                                  <table class="table table-bordered table-striped text-center" id="shf1">
                                                       <thead>
                                                            <tr><th>Shift 1</th></tr>
                                                       </thead>
                                                       <tbody>
                                                            <tr><td id='makan1' style="font-size: 3vw; font-weight: bold;">0</td></tr>
                                                       </tbody>
                                                  </table>
                                             </div>
                                             <div class="col-sm-3">
                                                  <table class="table table-bordered table-striped text-center" id="shf1">
                                                       <thead>
                                                            <tr><th>Shift 2</th></tr>
                                                       </thead>
                                                       <tbody>
                                                            <tr><td id='makan2' style="font-size: 3vw; font-weight: bold;">0</td></tr>
                                                       </tbody>
                                                  </table>
                                             </div>
                                             <div class="col-sm-3">
                                                  <table class="table table-bordered table-striped text-center" id="shf1">
                                                       <thead>
                                                            <tr><th>Shift 3</th></tr>
                                                       </thead>
                                                       <tbody>
                                                            <tr><td id='makan3' style="font-size: 3vw; font-weight: bold;">0</td></tr>
                                                       </tbody>
                                                  </table>
                                             </div>
                                        </div>

                                        <!-- ali -->
                                        <div class="form-group">
                                             <label class="col-sm-2 control-label">Total Extra Food</label>
                                             <div class="col-sm-3">
                                                  <table class="table table-bordered table-striped text-center" id="shf1">
                                                       <thead>
                                                            <tr><th>Shift 1</th></tr>
                                                       </thead>
                                                       <tbody>
                                                            <tr><td id='extra1' style="font-size: 3vw; font-weight: bold;">0</td></tr>
                                                       </tbody>
                                                  </table>
                                             </div>
                                             <div class="col-sm-3">
                                                  <table class="table table-bordered table-striped text-center" id="shf1">
                                                       <thead>
                                                            <tr><th>Shift 2</th></tr>
                                                       </thead>
                                                       <tbody>
                                                            <tr><td id='extra2' style="font-size: 3vw; font-weight: bold;">0</td></tr>
                                                       </tbody>
                                                  </table>
                                             </div>
                                             <div class="col-sm-3">
                                                  <table class="table table-bordered table-striped text-center" id="shf1">
                                                       <thead>
                                                            <tr><th>Shift 3</th></tr>
                                                       </thead>
                                                       <tbody>
                                                            <tr><td id='extra3' style="font-size: 3vw; font-weight: bold;">0</td></tr>
                                                       </tbody>
                                                  </table>
                                             </div>
                                        </div>

                                        <div class="form-group">
                                             <label class="col-sm-2 control-label">Transport</label>
                                             <div class="col-sm-10">
                                                  <table class="table table-bordered table-striped table-hover text-center" id="trs">
                                                       <thead>
                                                            <tr>
                                                                 <th>Jam</th>
                                                                 <th scope="col" width="30%">Bangil</th>
                                                                 <th scope="col" width="30%">Pasuruan</th>
                                                            </tr>
                                                       </thead>
                                                       <tbody id="trans">

                                                       </tbody>
                                                  </table>
                                             </div>
                                        </div>

                                   </form>
                              </div>
                              <div class="col-xs-5" style="padding-left: 0;">
                                   <table id="tableDetailEmp" class="table table-bordered table-striped table-hover">
                                        <thead style="background-color: rgba(126,86,134,.7);">
                                             <tr>
                                                  <th style="width: 1%;">Shift</th>
                                                  <th style="width: 3%">Jam</th>
                                                  <th style="width: 3%">ID</th>
                                                  <th style="width: 10%">Nama</th>
                                                  <th style="width: 5%">Section</th>
                                                  <th style="width: 1%">Trn</th>
                                                  <th style="width: 1%">Mkn</th>
                                             </tr>
                                        </thead>
                                        <tbody id="tableBodyEmp">
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
               </div>
          </div>
     </div>
</section>
@endsection
@section('scripts')
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
     });

     var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

     function changeTanggal() {
          $('#loading').show();
          var tanggal = $('#datepicker').val();
          var data = {
               tanggal:tanggal
          }
          $.get('{{ url("fetch/report/ga_report") }}', data, function(result, status, xhr){
               $('#tableDetailEmp').DataTable().clear();
               $('#tableDetailEmp').DataTable().destroy();
               $('#trans').html('');
               var makan1 = 0;
               var makan2 = 0;
               var makan3 = 0;
               var extra1 = 0;
               var extra2 = 0;
               var extra3 = 0;
               var tableData = "";

               $.each(result.datas, function(key, value) {
                    if(value.trn_bgl+value.trn_psr > 0){
                         tableData += '<tr>';
                         tableData += '<td style="font-size: 2vw; font-weight: bold;">'+value.ot_from +'-'+value.ot_to+'</td>';
                         tableData += '<td style="font-size: 3vw; font-weight: bold;">'+value.trn_bgl+'</td>';
                         tableData += '<td style="font-size: 3vw; font-weight: bold;">'+value.trn_psr+'</td>';
                         tableData += '</tr>';
                    }
                    makan1 += parseFloat(value.makan1);
                    makan2 += parseFloat(value.makan2);
                    makan3 += parseFloat(value.makan3);
                    extra2 += parseFloat(value.extra2);
                    extra3 += parseFloat(value.extra3);
               });

               var tableData2 = "";
               $('#tableBodyEmp').html('');

               $.each(result.details, function(key, value) {
                    tableData2 += '<tr>';
                    tableData2 += '<td style="font-size:12px;">'+value.SHIFT_OVTPLAN+'</td>';
                    tableData2 += '<td style="font-size:12px;">'+value.ot_from+'-'+value.ot_to+'</td>';
                    tableData2 += '<td style="font-size:12px;">'+value.emp_no+'</td>';
                    tableData2 += '<td style="font-size:12px;">'+value.Full_name+'</td>';
                    tableData2 += '<td style="font-size:12px;">'+value.Section+'</td>';
                    tableData2 += '<td style="font-size:12px;">'+value.trans+'</td>';
                    tableData2 += '<td style="font-size:12px;">'+value.food+'</td>';
                    tableData2 += '</tr>';
               });
               $('#tableBodyEmp').append(tableData2);

               $('#tableDetailEmp tfoot th').each(function(){
                    var title = $(this).text();
                    $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="4"/>' );
               });
               var table =  $('#tableDetailEmp').DataTable({
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
                         },
                         ]
                    },
                    'paging': true,
                    'lengthChange': true,
                    'pageLength': 10,
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

               table.columns().every( function () {
                    var that = this;

                    $( 'input', this.footer() ).on( 'keyup change', function () {
                         if ( that.search() !== this.value ) {
                              that
                              .search( this.value )
                              .draw();
                         }
                    } );
               } );

               $('#tableDetailEmp tfoot tr').appendTo('#tableDetailEmp thead');

               $('#trans').append(tableData);
               $('#makan1').text(makan1);
               $('#makan2').text(makan2);
               $('#makan3').text(makan3);
               $('#extra1').text(extra1);
               $('#extra2').text(extra2);
               $('#extra3').text(extra3);

               $('#loading').hide();
          });
}

$('#datepicker').datepicker({
     autoclose: true,
     format: "dd-mm-yyyy",
     todayHighlight: true
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