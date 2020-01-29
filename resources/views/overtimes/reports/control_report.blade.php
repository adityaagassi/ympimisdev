@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
     input {
          line-height: 22px;
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
     }
     table.table-bordered > tfoot > tr > th{
          border:1px solid rgb(211,211,211);
     }
     #loading, #error { 
          display: none;
     }
     #tableBodyList > tr:hover {
          cursor: pointer;
          background-color: #7dfa8c;
     }
     .urgent{
          background-color: red;
     }
</style>
@stop
@section('header')
<section class="content-header">
     <h1>
          Overtime Report<span class="text-purple"> </span>
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
               <div class="col-md-2">
                    <div class="form-group">
                         <label>Tanggal Mulai</label>
                         <div class="input-group date" style="width: 100%;">
                              <input type="text" placeholder="Pilih Tanggal" class="form-control pull-right" name="dateFrom" id="dateFrom">
                         </div>
                    </div>
               </div>
               <div class="col-md-2">
                    <div class="form-group">
                         <label>Tanggal Sampai</label>
                         <div class="input-group date" style="width: 100%;">
                              <input type="text" placeholder="Pilih Tanggal" class="form-control pull-right" name="dateTo" id="dateTo">
                         </div>
                    </div>
               </div>
               <div class="col-md-2">
                    <div class="form-group">
                         <label>&nbsp;</label>
                         <div class="input-group date" style="width: 100%;">
                              <button class="btn btn-primary" onclick="fetchTable()">Search</button>
                         </div>
                    </div>
               </div>
          </div>
          <div class="col-xs-12">
               <table id="tableList" class="table table-bordered table-striped table-hover" style="width: 100%;">
                    <thead style="background-color: rgba(126,86,134,.7);">
                         <tr>
                              <th style="width: 5%;">No</th>
                              <th style="width: 1%;">ID</th>
                              <th style="width: 7%;">Nama</th>
                              <th style="width: 1%;">Tanggal</th>
                              <th style="width: 1%;">Plan Mulai</th>
                              <th style="width: 1%;">Plan Sampai</th>
                              <th style="width: 1%;">Plan OT</th>
                              <th style="width: 1%;">Hari</th>
                              <th style="width: 1%;">Log Mulai</th>
                              <th style="width: 1%;">Log Sampai</th>
                              <th style="width: 1%;">Log OT</th>
                         </tr>
                    </thead>
                    <tbody id="tableBodyList">
                    </tbody>
               </table>
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

          $('#dateFrom').datepicker({
               autoclose: true,
               todayHighlight: true
          });
          $('#dateTo').datepicker({
               autoclose: true,
               todayHighlight: true
          });
     });

     function fetchTable(){
          if($('#dateFrom').val().length <= 0 || $('#dateTo').val().length <= 0 ){
               alert('Pilih range tanggal yang diinginkan');
               return false;
          }
          var dateFrom = $('#dateFrom').val();
          var dateTo = $('#dateTo').val();
          var data = {
               dateFrom:dateFrom,
               dateTo:dateTo
          }

          $('#tableList').DataTable().destroy();

          var table = $('#tableList').DataTable({
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
               // "serverSide": true,
               "ajax": {
                    "type" : "get",
                    "url" : "{{ url("fetch/report/overtime_control") }}",
                    "data" : data
               },
               "columns": [
               { "data": "requestno" },
               { "data": "emp_no" },
               { "data": "full_name" },
               { "data": "date" },
               { "data": "ovt_from" },
               { "data": "ovt_to" },
               { "data": "ot_plan" },
               { "data": "daytype" },
               { "data": "log_from" },
               { "data": "log_to" },
               { "data": "ot_actual" }
               ]
          });
     }
</script>
@endsection