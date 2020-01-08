@extends('layouts.master')
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
<section class="content">
     <div class="row">
          <div class="col-xs-12">
               <div class="box box-solid">
                    <div class="box-body">
                         <form class="form-horizontal">
                              <div class="form-group">
                                   <label for="datepicker" class="col-sm-2 control-label">Tanggal</label>

                                   <div class="col-sm-3">
                                        <input type="text" class="form-control datepicker" id="datepicker" placeholder="Select date" onchange="changeTanggal(); ">
                                   </div>
                              </div>

                              <div class="form-group">
                                   <label class="col-sm-2 control-label">Total Makan</label>
                                   <div class="col-sm-2">
                                        <table class="table table-bordered table-striped text-center" id="shf1">
                                             <thead>
                                                  <tr><th>Shift 1</th></tr>
                                             </thead>
                                             <tbody>
                                                  <tr><td id='makan1' onclick="makan(1,'Shift 1')" style="font-size: 3vw; font-weight: bold;">0</td></tr>
                                             </tbody>
                                        </table>
                                   </div>
                                   <div class="col-sm-2">
                                        <table class="table table-bordered table-striped text-center" id="shf1">
                                             <thead>
                                                  <tr><th>Shift 2</th></tr>
                                             </thead>
                                             <tbody>
                                                  <tr><td id='makan2' onclick="makan(2,'Shift 2')" style="font-size: 3vw; font-weight: bold;">0</td></tr>
                                             </tbody>
                                        </table>
                                   </div>
                                   <div class="col-sm-2">
                                        <table class="table table-bordered table-striped text-center" id="shf1">
                                             <thead>
                                                  <tr><th>Shift 3</th></tr>
                                             </thead>
                                             <tbody>
                                                  <tr><td id='makan3' onclick="makan(3,'Shift 3')" style="font-size: 3vw; font-weight: bold;">0</td></tr>
                                             </tbody>
                                        </table>
                                   </div>
                              </div>

                              <!-- ali -->
                              <div class="form-group">
                                   <label class="col-sm-2 control-label">Total Extra Food</label>
                                   <div class="col-sm-2">
                                        <table class="table table-bordered table-striped text-center" id="shf1">
                                             <thead>
                                                  <tr><th>Shift 1</th></tr>
                                             </thead>
                                             <tbody>
                                                  <tr><td id='extra1' onclick="extmakan(1, 'Shift 1')" style="font-size: 3vw; font-weight: bold;">0</td></tr>
                                             </tbody>
                                        </table>
                                   </div>
                                   <div class="col-sm-2">
                                        <table class="table table-bordered table-striped text-center" id="shf1">
                                             <thead>
                                                  <tr><th>Shift 2</th></tr>
                                             </thead>
                                             <tbody>
                                                  <tr><td id='extra2' onclick="extmakan(2, 'Shift 2')" style="font-size: 3vw; font-weight: bold;">0</td></tr>
                                             </tbody>
                                        </table>
                                   </div>
                                   <div class="col-sm-2">
                                        <table class="table table-bordered table-striped text-center" id="shf1">
                                             <thead>
                                                  <tr><th>Shift 3</th></tr>
                                             </thead>
                                             <tbody>
                                                  <tr><td id='extra3' onclick="extmakan(3, 'Shift 3')" style="font-size: 3vw; font-weight: bold;">0</td></tr>
                                             </tbody>
                                        </table>
                                   </div>
                              </div>

                              <div class="form-group">
                                   <label class="col-sm-2 control-label">Transport</label>
                                   <div class="col-sm-6">
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
               </div>
          </div>
     </div>

</section>
@endsection
@section('scripts')
<script>
     $.ajaxSetup({
          headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
     });

     jQuery(document).ready(function() {
     });

     var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

     function changeTanggal() {
          var tanggal = $('#datepicker').val();
          var data = {
               tanggal:tanggal
          }
          $.get('{{ url("fetch/report/ga_report") }}', data, function(result, status, xhr){
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

               $('#trans').append(tableData);
               $('#makan1').text(makan1);
               $('#makan2').text(makan2);
               $('#makan3').text(makan3);
               $('#extra1').text(extra1);
               $('#extra2').text(extra2);
               $('#extra3').text(extra3);

          });
     }

     $('#datepicker').datepicker({
          autoclose: true,
          format: "dd-mm-yyyy",
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