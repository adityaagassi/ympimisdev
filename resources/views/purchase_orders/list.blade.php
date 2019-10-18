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
          Purchase Order List <span class="text-purple"> japanese</span>
     </h1>
     <ol class="breadcrumb">
          <li>
               <a data-toggle="modal" data-target="#importModal" class="btn btn-primary btn-sm" style="color:white">Import {{ $page }}s</a>
          </li>
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
                         <form method="GET" action="{{ url("export/purchase_order/po_list2") }}">
                              <div class="col-xs-12">
                                   <div class="col-md-4 col-md-offset-2">
                                        <div class="form-group">
                                             <label>Purchase Group</label>
                                             <select class="form-control select2" multiple="multiple" id='pgr' name="pgr" data-placeholder="Select Purchase Group" style="width: 100%;">
                                                  <option></option>
                                                  @foreach($pgrs as $pgr)
                                                  <option value="{{$pgr}}">{{ $pgr }}</option>
                                                  @endforeach
                                             </select>
                                        </div>
                                   </div>
                                   <div class="col-md-4">
                                        <div class="form-group">
                                             <label>Status</label>
                                             <select class="form-control select2" id='status' name='status' data-placeholder="Select Status" style="width: 100%;">
                                                  <option value="All">All</option>
                                                  <option value="1">Converted</option>
                                                  <option value="0">Not Converted</option>
                                             </select>
                                        </div>
                                   </div>
                              </div>
                              <div class="col-xs-12">
                                   <div class="col-md-4 col-md-offset-2">
                                        <div class="form-group">
                                             <label>Order From</label>
                                             <div class="input-group date">
                                                  <div class="input-group-addon">
                                                       <i class="fa fa-calendar"></i>
                                                  </div>
                                                  <input type="text" class="form-control pull-right" id="orderfrom" name="order_date_from">
                                             </div>
                                        </div>
                                   </div>
                                   <div class="col-md-4">
                                        <div class="form-group">
                                             <label>Order To</label>
                                             <div class="input-group date">
                                                  <div class="input-group-addon">
                                                       <i class="fa fa-calendar"></i>
                                                  </div>
                                                  <input type="text" class="form-control pull-right" id="orderto" name="order_date_to">
                                             </div>
                                        </div>
                                   </div>
                              </div>
                              <div class="col-xs-12">
                                   <div class="col-md-4 col-md-offset-2">
                                        <div class="form-group">
                                             <label>Delivery From</label>
                                             <div class="input-group date">
                                                  <div class="input-group-addon">
                                                       <i class="fa fa-calendar"></i>
                                                  </div>
                                                  <input type="text" class="form-control pull-right" id="delivfrom" name="deliv_date_from">
                                             </div>
                                        </div>
                                   </div>
                                   <div class="col-md-4">
                                        <div class="form-group">
                                             <label>Delivery To</label>
                                             <div class="input-group date">
                                                  <div class="input-group-addon">
                                                       <i class="fa fa-calendar"></i>
                                                  </div>
                                                  <input type="text" class="form-control pull-right" id="delivto" name="deliv_date_to">
                                             </div>
                                        </div>
                                   </div>
                              </div>
                              <div class="col-xs-12">
                                   <div class="col-md-4 col-md-offset-2">
                                        <div class="form-group">
                                             <label class="control-label">Purchase Document Number</label>
                                             <textarea id="docNoArea" class="form-control" rows="3"></textarea>
                                             <input id="docNoTags" name="purchdoc" type="text" class="form-control tags"/>
                                        </div>
                                   </div>
                                   <div class="col-md-4">
                                        <div class="form-group">
                                             <label class="control-label">Material Number</label>
                                             <textarea id="materialArea" class="form-control" rows="3"></textarea>
                                             <input id="materialTags" name="material" type="text" class="form-control tags"/>
                                        </div>
                                   </div>
                              </div>
                              <div class="col-xs-12">
                                   <div class="col-md-4 col-md-offset-2">
                                        <div class="form-group">
                                             <label class="control-label">Item</label>
                                             <textarea id="itemArea" class="form-control" rows="3"></textarea>
                                             <input id="itemTags" name="item" type="text" class="form-control tags"/>
                                        </div>
                                   </div>
                                   <div class="col-md-4">
                                        <div class="form-group">
                                             <label class="control-label">Vendor Code</label>
                                             <textarea id="vendorArea" class="form-control" rows="3"></textarea>
                                             <input id="vendorTags" name="vendor" type="text" class="form-control tags"/>
                                        </div>
                                   </div>
                              </div> 
                              <div class="col-xs-12">       
                                   <div class="col-md-4 col-md-offset-6">
                                        <div class="form-group pull-right">
                                             <a href="javascript:void(0)" onClick="clearPoList()" class="btn btn-danger"><i class="fa fa-magic"></i> Clear</a>
                                             <button type="submit" class="btn btn-success"><i class="fa fa-download"></i> Excel</button>
                                             <button id="search" onClick="fetchTable()" class="btn btn-primary" type="button"><i class="fa fa-search"></i> Search</button>
                                        </div>
                                   </div>
                              </div>
                         </form>
                         <div class="row">
                              <div class="col-md-12">
                                   <table id="poListTable" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                        <thead style="background-color: rgba(126,86,134,.7);">
                                             <tr>
                                                  <th style="width:5%;">PGr</th>
                                                  <th style="width:5%;">Vendor</th>
                                                  <th style="width:5%;">Pch. Doc.</th>
                                                  <th style="width:5%;">Item</th>
                                                  <th style="width:5%;">Material</th>
                                                  <th>Desc</th>
                                                  <th style="width:5%;">Order</th>
                                                  <th style="width:5%;">Deliv</th>
                                                  <th style="width:5%;">Qty</th>
                                                  <th style="width:5%;">Price</th>
                                                  <th style="width:5%;">Curr</th>
                                                  <th style="width:5%;">Status</th>
                                                  <th style="width:5%;">Action</th>
                                             </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot style="background-color: rgb(252, 248, 227);">
                                             <tr>
                                                  <th>Total</th>
                                                  <th></th>
                                                  <th></th>
                                                  <th></th>
                                                  <th></th>
                                                  <th></th>
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

<div class="modal fade" id="importModal">
     <div class="modal-dialog">
          <div class="modal-content">
               <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Import Purchase Order List</h4>
                    Sample: <a href="{{ url('download/manual/import_po_list.txt') }}">import_po_list.txt</a> Code: #updateOrCreate
               </div>
               <form id="formImportPoList" method="post" action="{{url('import/purchase_order/po_list')}}" enctype="multipart/form-data">
                    <input type="hidden" value="{{csrf_token()}}" name="_token"/>
                    <div class="modal-body">
                         <center><input type="file" name="filePoList" id="filePoList" accept="text/plain"></center>
                    </div>
                    <div class="modal-footer">
                         <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                         <button type="submit" class="btn btn-primary">Import</button>
                    </div>
               </form>
          </div>
     </div>
</div>

<div class="modal fade" id="modalDownload">
     <div class="modal-dialog modal-sm">
          <div class="modal-content">
               <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Download Created PO</h4>
               </div>
               <div class="modal-body">
                    <center>
                         <div class="form-group">
                              <label>Select File(s) to Download</label>
                              <select multiple class="form-control" style="height: 180px;" id="selectDownload">
                              </select>
                         </div>
                    </center>
               </div>
               <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="downloadPO()">Download</button>
               </div>
          </div>
     </div>
</div>

<div class="modal fade" id="modalEdit">
     <div class="modal-dialog">
          <div class="modal-content">
               <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit Purchase Order List</h4>
               </div>
               <div class="modal-body">

               </div>
               <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="editPoList()">Confirm</button>
               </div>
          </div>
     </div>
</div>

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
          // $('body').toggleClass("sidebar-collapse");
          $('.tags').tagsInput({ width: 'auto' });
          $('#docNoTags').hide();
          $('#docNoTags_tagsinput').hide();
          $('#materialTags').hide();
          $('#materialTags_tagsinput').hide();
          $('#itemTags').hide();
          $('#itemTags_tagsinput').hide();
          $('#vendorTags').hide();
          $('#vendorTags_tagsinput').hide();
          $('.select2').select2();
          $('#orderfrom').datepicker({
               autoclose: true,
               todayHighlight: true
          });
          $('#orderto').datepicker({
               autoclose: true,
               todayHighlight: true
          });
          $('#delivfrom').datepicker({
               autoclose: true,
               todayHighlight: true
          });
          $('#delivto').datepicker({
               autoclose: true,
               todayHighlight: true
          });
          initKeyDown();
     });

     var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

     function fetchTable(){
          $('#poListTable').DataTable().destroy();
          var pgr = $('#pgr').val();
          var vendor = $('#vendorTags').val();
          var material = $('#materialTags').val();
          var purchdoc = $('#docNoTags').val();
          var item = $('#item').val();
          var order_date_from = $('#orderfrom').val();
          var order_date_to = $('#orderto').val();
          var deliv_date_from = $('#delivfrom').val();
          var deliv_date_to = $('#delivto').val();
          var status = $('#status').val();

          var data = {
               pgr: pgr,
               vendor: vendor,
               material: material,
               purchdoc: purchdoc,
               item: item,
               order_date_from: order_date_from,
               order_date_to: order_date_to,
               deliv_date_from: deliv_date_from,
               deliv_date_to: deliv_date_to,
               status: status
          }
          var table = $('#poListTable').DataTable({
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
                    "url" : "{{ url("fetch/purchase_order/po_list") }}",
                    "data" : data,
               },
               "columns": [
               { "data": "pgr" },
               { "data": "vendor" },
               { "data": "purchdoc" },
               { "data": "item" },
               { "data": "material" },
               { "data": "description" },
               { "data": "order_date" },
               { "data": "deliv_date" },
               { "data": "order_qty" },
               { "data": "price" },
               { "data": "curr" },
               { "data": "status" },
               { "data": "action" }
               ]
          });
     }

     function exportPoList() {
          var pgr = $('#pgr').val();
          var vendor = $('#vendorTags').val();
          var material = $('#materialTags').val();
          var purchdoc = $('#docNoTags').val();
          var item = $('#item').val();
          var order_date_from = $('#orderfrom').val();
          var order_date_to = $('#orderto').val();
          var deliv_date_from = $('#delivfrom').val();
          var deliv_date_to = $('#delivto').val();
          var status = $('#status').val();

          var data = {
               pgr: pgr,
               vendor: vendor,
               material: material,
               purchdoc: purchdoc,
               item: item,
               order_date_from: order_date_from,
               order_date_to: order_date_to,
               deliv_date_from: deliv_date_from,
               deliv_date_to: deliv_date_to,
               status: status
          }

          $.get('{{ url("export/purchase_order/po_list2") }}', data, function(result, status, xhr) {
               if(xhr.status == 200){
                    if(result.status){

                    }
               }
          })
     }

     function modalDownload(id){
          var data = {
               purchdoc:id
          }
          $.get('{{ url("fetch/purchase_order/download_po") }}', data, function(result, status, xhr){
               console.log(status);
               console.log(result);
               console.log(xhr);
               if(xhr.status == 200){
                    if(result.status){

                         $('#selectDownload').html('');
                         var optionData = '';
                         $.each(result.files, function(key, value) {
                              optionData += '<option value="' + value.file_name + '">' + value.file_name + '</option>';
                         });
                         $('#selectDownload').append(optionData);
                         $('#modalDownload').modal('show');

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

     function downloadPO(){
          var file_name = $('#selectDownload').val();
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

     function editPoList(id){
          $('#modalEdit').modal('show');
     }

     function clearPoList(){
          location.reload(true);
     }

     function initKeyDown() {
          $('#docNoArea').keydown(function(event) {
               if (event.keyCode == 13) {
                    convertDocNoToTags();
                    return false;
               }
          });
          $('#materialArea').keydown(function(event) {
               if (event.keyCode == 13) {
                    convertMaterialToTags();
                    return false;
               }
          });
          $('#itemArea').keydown(function(event) {
               if (event.keyCode == 13) {
                    convertItemToTags();
                    return false;
               }
          });
          $('#vendorArea').keydown(function(event) {
               if (event.keyCode == 13) {
                    convertVendorToTags();
                    return false;
               }
          });
     }

     function convertMaterialToTags() {
          var data = $('#materialArea').val();
          if (data.length > 0) {
               var rows = data.split('\n');
               if (rows.length > 0) {
                    for (var i = 0; i < rows.length; i++) {
                         var barcode = rows[i].trim();
                         if (barcode.length > 0) {
                              $('#materialTags').addTag(barcode);
                         }
                    }
                    $('#materialTags').hide();
                    $('#materialTags_tagsinput').show();
                    $('#materialArea').hide();
               }
          }
     }

     function convertDocNoToTags() {
          var data = $('#docNoArea').val();
          if (data.length > 0) {
               var rows = data.split('\n');
               if (rows.length > 0) {
                    for (var i = 0; i < rows.length; i++) {
                         var barcode = rows[i].trim();
                         if (barcode.length > 0) {
                              $('#docNoTags').addTag(barcode);
                         }
                    }
                    $('#docNoTags').hide();
                    $('#docNoTags_tagsinput').show();
                    $('#docNoArea').hide();
               }
          }
     }

     function convertItemToTags() {
          var data = $('#itemArea').val();
          if (data.length > 0) {
               var rows = data.split('\n');
               if (rows.length > 0) {
                    for (var i = 0; i < rows.length; i++) {
                         var barcode = rows[i].trim();
                         if (barcode.length > 0) {
                              $('#itemTags').addTag(barcode);
                         }
                    }
                    $('#itemTags').hide();
                    $('#itemTags_tagsinput').show();
                    $('#itemArea').hide();
               }
          }
     }

     function convertVendorToTags() {
          var data = $('#vendorArea').val();
          if (data.length > 0) {
               var rows = data.split('\n');
               if (rows.length > 0) {
                    for (var i = 0; i < rows.length; i++) {
                         var barcode = rows[i].trim();
                         if (barcode.length > 0) {
                              $('#vendorTags').addTag(barcode);
                         }
                    }
                    $('#vendorTags').hide();
                    $('#vendorTags_tagsinput').show();
                    $('#vendorArea').hide();
               }
          }
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