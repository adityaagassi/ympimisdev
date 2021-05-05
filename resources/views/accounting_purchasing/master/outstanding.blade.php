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
          Outstanding <span class="text-purple"> PR PO Investment</span>
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
                         <h3 class="box-title">By Selected Date<span class="text-purple"> Purchase Requisition</span></h3>
                    </div>
                    <div class="box-body">
                         <div class="row">
						<form method="GET" action="{{ url("export/outstanding_purchase_requisition") }}">
                                   <div class="col-xs-8">
                                        <div class="form-group" style="margin-bottom: 0;">
                                             <label>Submission Date</label><span class="text-red">*</span>
                                             <div class="input-group date">
                                                  <div class="input-group-addon">
                                                       <i class="fa fa-calendar"></i>
                                                  </div>
                                                  <input type="text" class="form-control pull-right datepicker" id="date_pr" name="date_pr">
                                             </div>    
                                        </div>
                                   </div>
                                   <div class="col-xs-4">  
                                         <div class="form-group" style="margin-bottom: 0;">
     	                                    <label style="color: white">Act</label>
     	                              	   <button class="btn btn-warning pull-right" style="width: 100%" type="submit"><i class="fa fa-download"></i> Generate PR Data</button>
                                   	</div>                        
                                   </div>
                          	</form>
                         </div>
                    </div>
               </div>
          </div>


          <div class="col-xs-6">
               <div class="box box-solid">
                    <div class="box-header">
                         <h3 class="box-title">By Selected Date<span class="text-purple"> Investment</span></h3>
                    </div>
                    <div class="box-body">
                         <div class="row">
							<form method="GET" action="{{ url("export/outstanding_investment") }}">
                              	<div class="col-xs-8">
                                   <div class="form-group" style="margin-bottom: 0;">
                                        <label>Investment Date</label><span class="text-red">*</span>
                                        <div class="input-group date">
                                             <div class="input-group-addon">
                                                  <i class="fa fa-calendar"></i>
                                             </div>
                                             <input type="text" class="form-control pull-right datepicker" id="date_inv" name="date_inv">
                                        </div>    
                                   </div>
                              	</div>
                              	<div class="col-xs-4">  
                                    <div class="form-group" style="margin-bottom: 0;">
	                                    <label style="color: white">Act</label>
	                              		<button type="submit" class="btn btn-success pull-right" style="width: 100%"><i class="fa fa-download"></i> Generate Inv Data</button>
                              		</div>                        
                              	</div>
                            </form>
                         </div>
                    </div>
               </div>
          </div>

          <div class="col-xs-6">
               <div class="box box-solid">
                    <div class="box-header">
                         <h3 class="box-title">By Selected Date<span class="text-purple"> Purchase Order</span></h3>
                    </div>
                    <div class="box-body">
                         <div class="row">
							<form method="GET" action="{{ url("export/outstanding_purchase_order") }}">
                              <div class="col-xs-8">
                                   <div class="form-group" style="margin-bottom: 0;">
                                        <label>PO Date</label><span class="text-red">*</span>
                                        <div class="input-group date">
                                             <div class="input-group-addon">
                                                  <i class="fa fa-calendar"></i>
                                             </div>
                                             <input type="text" class="form-control pull-right datepicker" id="date_po" name="date_po">
                                        </div>    
                                   </div>
                              </div>
                              <div class="col-xs-4">  
                                    <div class="form-group" style="margin-bottom: 0;">
	                                    <label style="color: white">Act</label>
	                              		<button id="generatePo1" onClick="generatePo1()" class="btn btn-primary pull-right" style="width: 100%"><i class="fa fa-download"></i> Generate PO Data</button>
                              		</div>                        
                              </div>
                          	</form>
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
          $('.select2').select2();
          $('.datepicker').datepicker({
               autoclose: true,
               todayHighlight: true
          });
     });

     var audio_error = new Audio('{{ url("sounds/error.mp3") }}');


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