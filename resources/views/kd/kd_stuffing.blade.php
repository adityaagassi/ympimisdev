@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style type="text/css">
     thead input {
          width: 100%;
          padding: 3px;
          box-sizing: border-box;
     }
     thead>tr>th {
          text-align:center;
     }
     tbody>tr>td {
          text-align:center;
     }
     tfoot>tr>th {
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
     #loading, #error { display: none; }

</style>
@stop
@section('header')
<section class="content-header">
     <h1>
          Knock Down Outputs <span class="text-purple"> ???</span>
          <small>Containers Stuffing <span class="text-purple">コンテナ積み込</span></small>
     </h1>
</section>
@endsection

@section('content')
<section class="content">
     <div class="row">
          <div class="col-xs-12" id="main">
               <div class="box">
                    <div class="box-header">
                         <h3 class="box-title">Knock Downs Stuffing <span class="text-purple">??</span></h3>
                    </div>
                    <div class="box-body" style="padding-bottom: 30px;">
                         <div class="row">
                              <div class="col-md-12">
                                   <div class="col-md-6 col-md-offset-3 resize">
                                        <div class="col-md-10">
                                             <div class="input-group">
                                                  <div class="input-group-addon" id="icon-serial" style="font-weight: bold">
                                                       <i class="glyphicon glyphicon-list-alt"></i>
                                                  </div>
                                                  <input type="text" class="form-control" id="invoice_number" name="invoice_number" placeholder="Invoice Number" required>
                                             </div>
                                             <br>
                                        </div>
                                   </div>
                                   <div class="col-md-6 col-md-offset-3 resize">
                                        <div class="col-md-10">
                                             <div class="input-group">
                                                  <div class="input-group-addon" id="icon-serial" style="font-weight: bold">
                                                       <i class="fa fa-bus"></i>
                                                  </div>
                                                  <select class="form-control select2" id="container_id" name="container_id" style="width: 100%;" data-placeholder="Choose a Container ID" required>
                                                       <option></option>
                                                       @foreach($container_schedules as $container_schedule)
                                                       <option value="{{ $container_schedule->id_checkSheet }}">
                                                            {{ $container_schedule->id_checkSheet. ' | ' .$container_schedule->invoice. ' | ' .date('d-M-Y', strtotime($container_schedule->Stuffing_date)). ' | ' .$container_schedule->shipment_condition_name. ' | ' .$container_schedule->destination }}
                                                       </option>
                                                       @endforeach
                                                  </select>
                                             </div>
                                             <br>
                                        </div>
                                        <div class="col-md-2">
                                             <input id="toggle_lock" data-toggle="toggle" data-on="Lock" data-off="Open" type="checkbox">
                                        </div>
                                   </div>
                              </div>
                              <div class="col-md-12">
                                   <div class="col-md-2 col-md-offset-2 resize-bottom">
                                        <div class="input-group">
                                             <div class="input-group-addon" id="icon-serial" style="font-weight: bold">
                                                  <i class="fa fa-cubes"></i>
                                             </div>
                                             <select class="form-control select2" id="marking" name="marking" style="width: 100%;" data-placeholder="No.Pallet" required>
                                             </select>
                                        </div>
                                        <br>
                                   </div>

                                   <div class="col-md-7">
                                        <div class="input-group">
                                             <div class="input-group-addon" id="icon-serial" style="font-weight: bold">
                                                  <i class="glyphicon glyphicon-barcode"></i>
                                             </div>
                                             <input type="text" style="text-align: center; font-size: 22" class="form-control" id="kdo_number_settlement" name="kdo_number_settlement" placeholder="Scan KDO Here..." required>
                                             <div class="input-group-addon" id="icon-serial">
                                                  <i class="glyphicon glyphicon-ok"></i>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </div>
               </div>

               <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
                         <li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">KDO Stuffing Detail</a></li>
                         <li class="vendor-tab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">KDO Stuffing</a></li>
                    </ul>

                    <div class="tab-content">
                         <div class="tab-pane active" id="tab_1">
                              <div class="box-body">                                   
                                   <div class="col-md-12" style="overflow-x: auto; padding: 0px;">
                                        <table id="kdo_detail" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                             <thead style="background-color: rgba(126,86,134,.7);">
                                                  <tr>
                                                       <th style="width: 10%">KD Number</th>
                                                       <th style="width: 10%">Ship. Date</th>
                                                       <th style="width: 10%">Material Number</th>
                                                       <th style="width: 50%">Material Description</th>
                                                       <th style="width: 10%">Location</th>
                                                       <th style="width: 10%">I/V</th>
                                                       <th style="width: 10%">Container ID</th>
                                                       <th style="width: 10%">Stuff. Date</th>
                                                       <th style="width: 10%">Quantity</th>
                                                  </tr>
                                             </thead>
                                             <tbody>
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
                                                       <th></th>
                                                       <th></th>
                                                  </tr>
                                             </tfoot>
                                        </table>
                                   </div>
                              </div>
                         </div>

                         <div class="tab-pane" id="tab_2">
                              <table id="kdo_table" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                   <thead style="background-color: rgba(126,86,134,.7);">
                                        <tr>
                                             <th style="width: 10%">KDO</th>
                                             <th style="width: 10%">Ship. Date</th>
                                             <th style="width: 10%">Count Item</th>
                                             <th style="width: 20%">Location</th>
                                             <th style="width: 10%">I/V</th>
                                             <th style="width: 10%">Container ID</th>
                                             <th style="width: 20%">Stuff. Date </th>
                                             <th style="width: 5%">Cancel</th>
                                        </tr>
                                   </thead>
                                   <tbody>
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
                                             <th></th>
                                        </tr>
                                   </tfoot>
                              </table>
                         </div>
                    </div>
               </div>
               
          </div>

          <div class="col-xs-5" id="resume" style="padding-left: 0px;">
               <div class="box" style="overflow-x: auto;">
                    <div class="box-header">
                         <h3 style="font-weight: bold;" class="box-title" id="container_id_text">A  </h3>
                         <br>

                         <span style="font-weight: bold; font-size: 18px;">Actual Stuffing: </span>
                         <span id="stuffing" style="font-weight: bold; font-size: 24px; color: red;">0</span>
                         <span style="font-weight: bold; font-size: 16px; color: red;">of</span>
                         <span id="total" style="font-weight: bold; font-size: 16px; color: red;">0</span>
                         <span style="font-weight: bold; font-size: 16px; color: red;">Box</span>

                    </div>
                    <div class="box-body" style="padding-bottom: 30px;">
                         <table id="resume" class="table table-bordered table-striped table-hover" style="width: 100%;">
                              <thead style="background-color: rgba(126,86,134,.7);">
                                   <tr>
                                        <th style="width: 10%">Marking</th>
                                        <th style="width: 20%">Material Number</th>
                                        <th style="width: 50%">Material Description</th>
                                        <th style="width: 10%">Category</th>
                                        <th style="width: 10%">Qty Checksheet</th>
                                        <th style="width: 10%">Qty Stuffing</th>
                                   </tr>
                              </thead>
                              <tbody id="tableBodyResume">
                              </tbody>
                         </table>
                    </div>
               </div>
          </div>
     </div>

</section>
@stop

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/bootstrap-toggle.min.js") }}"></script>
<script type="text/javascript">
     $.ajaxSetup({
          headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
     });

     jQuery(document).ready(function() {
          $('body').toggleClass("sidebar-collapse");

          $("#kdo_number_delivery").focus();
          $('#marking').prop('disabled', true);

          fetchKDO();
          fetchKDODetail();
          refresh();

          $("#resume").css({"display":"none"});

          $('#toggle_lock').change(function(){        
               if(this.checked){
                    $('#invoice_number').prop('disabled', true);
                    $('#container_id').prop('disabled', true);
                    $('#kdo_number_settlement').prop('disabled', false);
                    $('#kdo_number_settlement').focus();
                    $('#marking').prop('disabled', false);
                    

                    $('#main').removeClass('col-xs-12');
                    $('#main').addClass('col-xs-7');
                    $("#resume").css({"display":"block"});

                    $('.resize').removeClass('col-md-6');
                    $('.resize').removeClass('col-md-offset-3');
                    $('.resize').addClass('col-md-8');
                    $('.resize').addClass('col-md-offset-2');


                    $('.resize-bottom').removeClass('col-md-2');
                    $('.resize-bottom').removeClass('col-md-offset-2');
                    $('.resize-bottom').addClass('col-md-3');
                    $('.resize-bottom').addClass('col-md-offset-1');

                    fillResume();

               }
               else{
                    $('#invoice_number').prop('disabled', false);
                    $('#invoice_number').val('');
                    $('#container_id').prop('disabled', false);
                    $("#container_id").val('').change();
                    $('#marking').prop('disabled', true);
                    $("#marking").val('').change();
                    $('#kdo_number_settlement').prop('disabled', true);
                    $('#invoice_number').focus();

                    $('#main').removeClass('col-xs-7');
                    $('#main').addClass('col-xs-12');
                    $("#resume").css({"display":"none"});


                    $('.resize').removeClass('col-md-8');
                    $('.resize').removeClass('col-md-offset-2');
                    $('.resize').addClass('col-md-6');
                    $('.resize').addClass('col-md-offset-3');


                    $('.resize-bottom').removeClass('col-md-3');
                    $('.resize-bottom').removeClass('col-md-offset-1');
                    $('.resize-bottom').addClass('col-md-2');
                    $('.resize-bottom').addClass('col-md-offset-2');


               }
          });

          $(function () {
               $('.select2').select2();
          });

          $('#kdo_number_settlement').keydown(function(event) {
               if (event.keyCode == 13 || event.keyCode == 9) {
                    if($("#invoice_number").val().length == 6 && $("#container_id").val() != ""){
                         if($("#kdo_number_settlement").val().length >= 10){
                              scanKdoNumber();
                              return false;
                         }
                         else{
                              openErrorGritter('Error!', 'KDO number invalid.');
                              audio_error.play();
                              $("#kdo_number_settlement").val("");
                         }
                    }
                    else{
                         openErrorGritter('Error!', 'Invoice number invalid or container ID required.');
                         audio_error.play();
                         refresh();
                    }
               }
          });

     });

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

     function openInfoGritter(title, message){
          jQuery.gritter.add({
               title: title,
               text: message,
               class_name: 'growl-info',
               image: '{{ url("images/image-unregistered.png") }}',
               sticky: false,
               time: '2000'
          });
     }

     function refresh(){
          $('#invoice_number').val('');
          $('#container_id').val('').change();
          $('#toggle_lock').prop('checked', false).change();
          $('#kdo_number_settlement').prop('disabled', true);
          $('#kdo_number_settlement').val('');
          $('#invoice_number').focus();     
     }

     var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

     function deleteKDO(id){
          if(confirm("Are you sure you want to delete this data?")){
               var data = {
                    kd_number:id
               }
               $.post('{{ url("delete/kdo_stuffing") }}', data, function(result, status, xhr){
                    if(result.status){
                         $('#kdo_table').DataTable().ajax.reload();
                         $('#kdo_detail').DataTable().ajax.reload();
                         openSuccessGritter('Success!', result.message);
                         $('#kdo_number_settlement').val('');
                         $('#kdo_number_settlement').focus();
                    }
                    else{
                         openErrorGritter('Error!', result.message);
                         $('#kdo_number_settlement').val('');
                         $('#kdo_number_settlement').focus();
                         audio_error.play();
                    }
               });
          }
          else{
               $('#kdo_number_settlement').val('');
               $('#kdo_number_settlement').focus();
          }
     }

     function fetchKDO(){
          var data = {
               status : 3,
          }
          $('#kdo_table tfoot th').each( function () {
               var title = $(this).text();
               $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
          });
          var table = $('#kdo_table').DataTable( {
               'paging'        : true,
               'dom': 'Bfrtip',
               'responsive': true,
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
               'lengthChange'  : true,
               'searching'     : true,
               'ordering'      : true,
               'info'        : true,
               'order'       : [],
               'autoWidth'   : true,
               "sPaginationType": "full_numbers",
               "bJQueryUI": true,
               "bAutoWidth": false,
               "processing": true,
               "serverSide": true,
               "ajax": {
                    "type" : "get",
                    "url" : "{{ url("fetch/kdo") }}",
                    "data" : data,
               },
               "columns": [
               { "data": "kd_number" },
               { "data": "st_date" },
               { "data": "actual_count" },
               { "data": "remark" },
               { "data": "invoice_number" },
               { "data": "container_id" },
               { "data": "updated_at" },
               { "data": "deleteKDO" }
               ]
          });

          table.columns().every( function () {
               var that = this;

               $( 'input', this.footer() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                         that
                         .search( this.value )
                         .draw();
                    }
               });
          });

          $('#kdo_table tfoot tr').appendTo('#kdo_table thead');
     }

     function fetchKDODetail(){
          var data = {
               status : 3,
          }

          $('#kdo_detail tfoot th').each( function () {
               var title = $(this).text();
               $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
          });
          var table = $('#kdo_detail').DataTable( {
               'paging'        : true,
               'dom': 'Bfrtip',
               'responsive': true,
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
               'lengthChange'  : true,
               'searching'     : true,
               'ordering'      : true,
               'info'        : true,
               'order'       : [],
               'autoWidth'   : true,
               "sPaginationType": "full_numbers",
               "bJQueryUI": true,
               "bAutoWidth": false,
               "processing": true,
               "serverSide": true,
               "ajax": {
                    "type" : "get",
                    "url" : "{{ url("fetch/kdo_detail") }}",
                    "data" : data,
               },
               "columns": [
               { "data": "kd_number" },
               { "data": "st_date" },
               { "data": "material_number" },
               { "data": "material_description" },
               { "data": "location" },
               { "data": "invoice_number" },
               { "data": "container_id" },
               { "data": "updated_at" },
               { "data": "quantity" }
               ]
          });

          table.columns().every( function () {
               var that = this;

               $( 'input', this.footer() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                         that
                         .search( this.value )
                         .draw();
                    }
               });
          });

          $('#kdo_detail tfoot tr').appendTo('#kdo_detail thead');
     }



     function updateResume(knock_down_details, marking){

          for (var i = 0; i < knock_down_details.length; i++) {
               console.log(marking+'_'+knock_down_details[i].material_number);


               var curr_qty = $('#'+marking+'_'+knock_down_details[i].material_number).find('td').eq(5).text();               
               
               $('#'+marking+'_'+knock_down_details[i].material_number).find('td').eq(5).text(parseInt(curr_qty) + parseInt(knock_down_details[i].quantity));


               var ck_qty = $('#'+marking+'_'+knock_down_details[i].material_number).find('td').eq(4).text();
               var st_qty = $('#'+marking+'_'+knock_down_details[i].material_number).find('td').eq(5).text();


               if(ck_qty != st_qty){
                    $('#'+marking+'_'+knock_down_details[i].material_number).css({"background-color" : "rgb(255, 204, 255)"});
               }else{
                    $('#'+marking+'_'+knock_down_details[i].material_number).css({"background-color" : "rgb(204, 255, 255)"});
               }

               stuffing++;
               $("#stuffing").text(stuffing);

          }

     }

     var stuffing = 0;


     function fillResume(){
          var container_id = $("#container_id").val();

          if(container_id != ''){

               var data = {
                    container_id : container_id
               }

               $.get('{{ url("fetch/container_resume") }}', data, function(result, status, xhr){
                    if(result.status){
                         $('#tableBodyResume').html("");
                         $('#container_id_text').html("");
                         $('#container_id_text').text("Resume Container ID: "+container_id);

                         var tableData = "";
                         $.each(result.resume, function(key, value) {

                              var background = '';
                              if(value.ck_qty != value.st_qty){
                                   background = 'style="background-color: rgb(255, 204, 255)"';
                              }else{
                                   background = 'style="background-color: rgb(204, 255, 255)"';
                              }

                              tableData += '<tr '+background+' id="'+value.marking+'_'+value.material_number+'">';
                              tableData += '<td>'+ value.marking +'</td>';
                              tableData += '<td>'+ value.material_number +'</td>';
                              tableData += '<td>'+ value.material_description +'</td>';
                              tableData += '<td>'+ value.category +'</td>';
                              tableData += '<td>'+ value.ck_qty +'</td>';
                              tableData += '<td>'+ value.st_qty +'</td>';
                              tableData += '</tr>';
                         });

                         $('#tableBodyResume').append(tableData);


                         $("#marking").html('');                                                   
                         var select = ' <option></option>';
                         $.each(result.pallet, function(key, value) {
                              select += '<option value="'+value.marking+'">'+value.marking+'</option>';
                         });
                         $("#marking").html(select);

                         var total = 0;
                         $.each(result.checksheet, function(key, value) {
                              total += value.box;
                         });
                         $("#total").text(total);


                         stuffing = result.stuffing.length;
                         $("#stuffing").text(result.stuffing.length);


                    }
               });
          }
     }


     function scanKdoNumber(){
          var kdo_number = $("#kdo_number_settlement").val();
          var invoice_number = $("#invoice_number").val();
          var container_id = $("#container_id").val();
          var marking = $("#marking").val();
          if(marking == ''){
               openErrorGritter('Error!', "Pilih Pallet Terlebih Dahulu");
               return false;
          }
          var data = {
               kd_number : kdo_number,
               status : '3',
               invoice_number : invoice_number,
               container_id : container_id,
               marking : marking
          }
          $.post('{{ url("scan/kd_stuffing") }}', data, function(result, status, xhr){
               if(xhr.status == 200){
                    if(result.status){
                         openSuccessGritter('Success!', result.message);

                         updateResume(result.knock_down_details, marking);

                         $('#kdo_table').DataTable().ajax.reload();
                         $('#kdo_detail').DataTable().ajax.reload();
                         $('#kdo_number_settlement').val('');
                    }
                    else{
                         openErrorGritter('Error!', result.message);
                         $('#kdo_number_settlement').val('');
                         audio_error.play();
                    }
               }
               else{
                    openErrorGritter('Error', 'Disconnected from server');
                    audio_error.play();
                    $('#kdo_number_settlement').val('');
               }
          });
     }
</script>

@stop