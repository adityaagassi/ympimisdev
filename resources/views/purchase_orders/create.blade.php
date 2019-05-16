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
     #loading { display: none; }
</style>
@endsection
@section('header')
<section class="content-header">
     <h1>
          Create Purchase Order <span class="text-purple"> japanese</span>
     </h1>
     <ol class="breadcrumb">
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
          <div class="col-xs-6">
               <div class="box box-solid">
                    <div class="box-header">
                         <h3 class="box-title">By Selected PO<span class="text-purple"> KD Part</span></h3>
                    </div>
                    <div class="box-body">
                         <div class="row">
                              <div class="col-xs-6">
                                   <div class="form-group">
                                        <label class="control-label">Purchase Document Number</label><span class="text-red">*</span>
                                        <textarea id="createDocNoArea" class="form-control" rows="3"></textarea>
                                        <input id="createDocNoTags" type="text" class="form-control tags"/>
                                   </div>
                              </div>
                              <div class="col-xs-6">
                                   <div class="form-group" style="margin-bottom: 0;">
                                        <label>Defined Delivery Date</label><span class="text-red">*</span>
                                        <div class="input-group date">
                                             <div class="input-group-addon">
                                                  <i class="fa fa-calendar"></i>
                                             </div>
                                             <input type="text" class="form-control pull-right" id="createDelivDate">
                                        </div>
                                   </div>
                                   <div class="form-group">
                                        <label>Shipment Condition</label><span class="text-red">*</span>
                                        <select id="shipment_condition" class="form-control select2" style="width: 100%;" data-placeholder="Select a Shipment Condition">
                                             <option></option>
                                             @foreach($shipment_conditions as $shipment_condition)
                                             <option value="{{ $shipment_condition->shipment_condition_code }}">{{ $shipment_condition->shipment_condition_code }} - {{ $shipment_condition->shipment_condition_name }}</option>
                                             @endforeach
                                        </select>
                                   </div>
                              </div>
                              <div class="col-xs-12">
                                   <button id="generatePo1" onClick="generatePo1()" class="btn btn-primary pull-right"><i class="fa fa-download"></i> Generate PO</button>                              
                              </div>
                         </div>
                    </div>
               </div>
          </div>
          <div class="col-xs-6">
               <div class="box box-solid">
                    <div class="box-header">
                         <h3 class="box-title">By Imported File<span class="text-purple"> Direct Material</span></h3><br>
                         Sample: <a href="{{ url('download/manual/import_po.txt') }}">import_po.txt</a> Code: #truncate
                    </div>
                    <div class="box-body">
                         <form id="form_generate_po" method="post" action="upload" enctype="multipart/form-data">
                              <input type="hidden" value="{{csrf_token()}}" name="_token" />
                              <input type="file" name="filePurchaseOrder" id="filePurchaseOrder" accept="text/plain">
                              <button type="submit" id="generatePo2" class="btn btn-primary pull-right"><i class="fa fa-download"></i> Generate PO</button>
                         </form>
                    </div>
               </div>
               <div class="box box-solid">
                    <div class="box-header">
                         <h3 class="box-title">By Selected PO<span class="text-purple"> Direct Material</span></h3>
                    </div>
                    <div class="box-body">
                         <div class="row">
                              <div class="col-xs-6">
                                   <div class="form-group">
                                        <label class="control-label">Purchase Document Number</label><span class="text-red">*</span>
                                        <textarea id="createDocNo2Area" class="form-control" rows="3"></textarea>
                                        <input id="createDocNo2Tags" type="text" class="form-control tags"/>
                                   </div>
                              </div>
                              <div class="col-xs-6">
                                   <div class="form-group" style="margin-bottom: 0;">
                                        <label>Defined Delivery Date</label><span class="text-red">*</span>
                                        <div class="input-group date">
                                             <div class="input-group-addon">
                                                  <i class="fa fa-calendar"></i>
                                             </div>
                                             <input type="text" class="form-control pull-right" id="createDelivDate2">
                                        </div>
                                   </div>
                                   <div class="form-group">
                                        <label>Shipment Condition</label><span class="text-red">*</span>
                                        <select id="shipment_condition2" class="form-control select2" style="width: 100%;" data-placeholder="Select a Shipment Condition">
                                             <option></option>
                                             @foreach($shipment_conditions as $shipment_condition)
                                             <option value="{{ $shipment_condition->shipment_condition_code }}">{{ $shipment_condition->shipment_condition_code }} - {{ $shipment_condition->shipment_condition_name }}</option>
                                             @endforeach
                                        </select>
                                   </div>
                              </div>
                              <div class="col-xs-12">
                                   <button id="generatePo3" onClick="generatePo3()" class="btn btn-primary pull-right"><i class="fa fa-download"></i> Generate PO</button>                              
                              </div>
                         </div>
                    </div>
               </div>
          </div>
     </div>
</section>

<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
     <p style="position: absolute; color: White; top: 45%; left: 35%;">
          <span style="font-size: 40px">Please wait a moment...<i class="fa fa-spin fa-refresh"></i></span>
     </p>
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
          $('#createDocNoTags').hide();
          $('#createDocNoTags_tagsinput').hide();
          $('#createDocNo2Tags').hide();
          $('#createDocNo2Tags_tagsinput').hide();
          $('.select2').select2();
          $('#orderfrom').datepicker({
               autoclose: true,
               todayHighlight: true
          });
          $('#createDelivDate').datepicker({
               autoclose: true,
               todayHighlight: true
          });
          $('#createDelivDate2').datepicker({
               autoclose: true,
               todayHighlight: true
          });
          initKeyDown();
     });

     var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

     $('#form_generate_po').on('submit', function(event){
          $("#loading").show();
          event.preventDefault();
          var formdata = new FormData(this);

          $.ajax({
               url:"{{url('generate/purchase_order/po_create2')}}",
               method:'post',
               data:formdata,
               dataType:"json",
               processData: false,
               contentType: false,
               cache: false,
               success:function(data){
                    if(data.status){
                         openSuccessGritter('Success!', data.message);
                         $("#loading").hide();
                         download_files(data.file_paths);
                    }
                    else{
                         $("#loading").hide();
                         audio_error.play();
                         openErrorGritter('Error!', data.message);
                    }
               }
          });
     });

     function generatePo3(){
          $("#loading").show();
          var purchdoc = $('#createDocNo2Tags').val();
          var delivDate = $('#createDelivDate2').val();
          var shipmentCondition = $('#shipment_condition2').val();
          var data = {
               purchdoc:purchdoc,
               delivDate:delivDate,
               shipmentCondition:shipmentCondition
          }
          $.post('{{ url("generate/purchase_order/po_create3") }}', data, function(result, status, xhr){
               console.log(status);
               console.log(result);
               console.log(xhr);
               if(xhr.status == 200){
                    if(result.status){
                         openSuccessGritter('Success!', result.message);
                         $("#loading").hide();
                         download_files(result.file_paths);
                    }
                    else{
                         $("#loading").hide();
                         audio_error.play();
                         openErrorGritter('Error!', result.message);
                    }
               }
               else{
                    $("#loading").hide();
                    audio_error.play();
                    alert('Disconnected from server.');
               }
          });
     }

     function generatePo1(){
          $("#loading").show();
          var purchdoc = $('#createDocNoTags').val();
          var delivDate = $('#createDelivDate').val();
          var shipmentCondition = $('#shipment_condition').val();
          var data = {
               purchdoc:purchdoc,
               delivDate:delivDate,
               shipmentCondition:shipmentCondition
          }
          $.post('{{ url("generate/purchase_order/po_create") }}', data, function(result, status, xhr){
               console.log(status);
               console.log(result);
               console.log(xhr);
               if(xhr.status == 200){
                    if(result.status){
                         openSuccessGritter('Success!', result.message);
                         $("#loading").hide();
                         download_files(result.file_paths);
                    }
                    else{
                         $("#loading").hide();
                         audio_error.play();
                         openErrorGritter('Error!', result.message);
                    }
               }
               else{
                    $("#loading").hide();
                    audio_error.play();
                    alert('Disconnected from server.');
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

     function initKeyDown() {
          $('#createDocNo2Area').keydown(function(event) {
               if (event.keyCode == 13) {
                    convertCreateDocNo2ToTags();
                    return false;
               }
          });
          $('#createDocNoArea').keydown(function(event) {
               if (event.keyCode == 13) {
                    convertCreateDocNoToTags();
                    return false;
               }
          });
     }

     function convertCreateDocNo2ToTags() {
          var data = $('#createDocNo2Area').val();
          if (data.length > 0) {
               var rows = data.split('\n');
               if (rows.length > 0) {
                    for (var i = 0; i < rows.length; i++) {
                         var barcode = rows[i].trim();
                         if (barcode.length > 0) {
                              $('#createDocNo2Tags').addTag(barcode);
                         }
                    }
                    $('#createDocNo2Tags').hide();
                    $('#createDocNo2Tags_tagsinput').show();
                    $('#createDocNo2Area').hide();
               }
          }
     }

     function convertCreateDocNoToTags() {
          var data = $('#createDocNoArea').val();
          if (data.length > 0) {
               var rows = data.split('\n');
               if (rows.length > 0) {
                    for (var i = 0; i < rows.length; i++) {
                         var barcode = rows[i].trim();
                         if (barcode.length > 0) {
                              $('#createDocNoTags').addTag(barcode);
                         }
                    }
                    $('#createDocNoTags').hide();
                    $('#createDocNoTags_tagsinput').show();
                    $('#createDocNoArea').hide();
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