@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<style type="text/css">
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
          padding-top: 0;
          padding-bottom: 0;
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
          Purchase Order Archives <span class="text-purple"> japanese</span>
     </h1>
     <ol class="breadcrumb">
         {{--  <li>
               <a data-toggle="modal" data-target="#importModal" class="btn btn-primary btn-sm" style="color:white">Import {{ $page }}s</a>
          </li> --}}
     </ol>
</section>
@endsection

@section('content')
<section class="content">
     @if (session('success'))
     <div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
          {{ session('success') }}
     </div>
     @endif
     @if (session('error'))
     <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-ban"></i> Error!</h4>
          {{ session('error') }}
     </div>
     @endif
     <div class="row">
          <div class="col-xs-12">
               <div class="box">
                    <div class="box-header">
                         <h3 class="box-title">Detail Filters<span class="text-purple"> フィルター詳細</span></h3>
                    </div>
                    <div class="box-body">
                         <div class="col-xs-12">
                              <div class="col-md-4 col-md-offset-2">
                                   <div class="form-group">
                                        <label>Created From</label>
                                        <div class="input-group date">
                                             <div class="input-group-addon">
                                                  <i class="fa fa-calendar"></i>
                                             </div>
                                             <input type="text" class="form-control pull-right" id="createdfrom">
                                        </div>
                                   </div>
                              </div>
                              <div class="col-md-4">
                                   <div class="form-group">
                                        <label>Created To</label>
                                        <div class="input-group date">
                                             <div class="input-group-addon">
                                                  <i class="fa fa-calendar"></i>
                                             </div>
                                             <input type="text" class="form-control pull-right" id="createdto">
                                        </div>
                                   </div>
                              </div>
                         </div>
                         <div class="col-xs-12">       
                              <div class="col-md-4 col-md-offset-6">
                                   <div class="form-group pull-right">
                                        <a href="javascript:void(0)" onClick="clearPoList()" class="btn btn-danger"><i class="fa fa-magic"></i> Clear</a>
                                        <button id="search" onClick="fetchTable()" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                                   </div>
                              </div>
                         </div>
                         <div class="row">
                              <div class="col-xs-12">
                                   <table id="archiveTable" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                        <thead style="background-color: rgba(126,86,134,.7);">
                                             <tr>
                                                  <th>Purchdoc</th>
                                                  <th>Order Code</th>
                                                  <th>File Name</th>
                                                  <th>Created By</th>
                                                  <th>Created At</th>
                                             </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
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
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
     $.ajaxSetup({
          headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
     });

     jQuery(document).ready(function() {
          $('#createdfrom').datepicker({
               autoclose: true,
               todayHighlight: true
          });
          $('#createdto').datepicker({
               autoclose: true,
               todayHighlight: true
          });
          fetchTable();
     });

     function fetchTable(){
          var createdfrom = $('#createdfrom').val();
          var createdto = $('#createdto').val();
          var data = {
               createdto:createdto,
               createdfrom:createdfrom,
          }
          var table = $('#archiveTable').DataTable({
               'dom': 'Bfrtip',
               'responsive': true,
               'lengthMenu': [
               [ 10, 25, 50, -1 ],
               [ '10 rows', '25 rows', '50 rows', 'Show all' ]
               ],
               "pageLength": 25,
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
                    }
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
                    "url" : "{{ url("fetch/purchase_order/po_archive") }}",
                    "data" : data,
               },
               "columns": [
               { "data": "purchdoc" },
               { "data": "order_no" },
               { "data": "filename" },
               { "data": "username" },
               { "data": "created_at" },
               ]
          });
     }

     function downloadPo(id){
          var file_name = id;
          var data = {
               file_name:file_name
          }

          $.get('{{ url("download/purchase_order/download_po") }}', data, function(result, status, xhr){
               console.log(status);
               console.log(result);
               console.log(xhr);
               if(xhr.status == 200){
                    if(result.status){
                         download_files(result.file_paths);
                    }
                    else{
                         audio_error.play();
                         openErrorGritter('Error!', result.message);
                    }
               }
               else{
                    alert('Disconnected from server');
               }
          });
     }

     function download_files(files) {
          function download_next(i) {
               if (i >= files.length) {
                    return;
               }
               var a = document.createElement('a');
               a.href = files[i].download;
               a.target = '_parent';
               if ('download' in a) {
                    a.download = files[i].filename;
               }
               (document.body || document.documentElement).appendChild(a);
               if (a.click) {
                    a.click();
               } else {
                    $(a).click();
               }
               a.parentNode.removeChild(a);
               setTimeout(function() {
                    download_next(i + 1);
               }, 500);
          }
          download_next(0);
     }

     function openErrorGritter(title, message) {
          jQuery.gritter.add({
               title: title,
               text: message,
               class_name: 'growl-danger',
               image: '{{ url("images/image-stop.png") }}',
               sticky: false,
               time: '2000'
          });
     }

     function openSuccessGritter(title, message){
          jQuery.gritter.add({
               title: title,
               text: message,
               class_name: 'growl-success',
               image: '{{ url("images/image-screen.png") }}',
               sticky: false,
               time: '2000'
          });
     }

</script>

@stop