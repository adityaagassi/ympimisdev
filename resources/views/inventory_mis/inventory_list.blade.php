@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
     thead input {
          width: 100%;
          padding: 3px;
          box-sizing: border-box;
     }
     thead>tr>th{
          text-align:center;
     }
     tbody>tr>td{
          text-align:center;
          padding: 2px;
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
          border:1px solid rgb(144,144,144);
          padding-top: 1;
          padding-bottom: 1;
     }
     table.table-bordered > tfoot > tr > th{
          border:1px solid rgb(144,144,144);
     }
     #loading, #error { display: none; }

     /* Chrome, Safari, Edge, Opera */
     input::-webkit-outer-spin-button,
     input::-webkit-inner-spin-button {
          -webkit-appearance: none;
          margin: 0;
     }

     /* Firefox */
     input[type=number] {
          -moz-appearance: textfield;
     }
</style>
@stop
@section('header')
<section class="content-header">
     <h1>
          {{ $title }}<span class="text-purple"> {{ $title_jp }}</span>
     </h1>
     <ol class="breadcrumb">
          <li>
               <button data-toggle="modal" data-target="#modalCreate" class="btn btn-sm bg-purple" style="color:white"><i class="fa fa-plus"></i> New Item</button>
          </li>
     </ol>
</section>
@stop
@section('content')
<section class="content">
     <div class="row">
          <div class="col-xs-12">
               <table id="table_master" class="table table-bordered table-striped table-hover" style="width: 100%">
                    <thead style="background-color: rgba(126,86,134,.7);">
                         <tr>
                              <th>#</th>
                              <th>Category</th>
                              <th>Device</th>
                              <th>Description</th>
                              <th>Project</th>
                              <th>Location</th>
                              <th>Quantity</th>
                              <th>PIC</th>
                              <th>Action</th>
                         </tr>
                    </thead>
                    <tbody id="body_master">
                    </tbody>
                    <tfoot>
                         <th></th>
                         <th></th>
                         <th></th>
                         <th></th>
                         <th></th>
                         <th></th>
                         <th></th>
                         <th></th>
                         <th></th>
                    </tfoot>
               </table>
          </div>
     </div>

     <!-- ------------------------------------ MODAL -->

     <div id="modalCreate" class="modal fade" role="dialog">
          <div class="modal-dialog modal-lg" style="width: 95%">
               <div class="modal-content">
                    <div class="modal-header">
                         <div class="col-xs-12" style="background-color: #605ca8">
                              <h1 style="text-align: center; margin:5px; font-weight: bold; color: white">New Inventory Item</h1>
                         </div>
                    </div>
                    <div class="modal-body">
                         <div class="row">
                              <div class="col-xs-12">
                                   <button class="btn btn-sm btn-success pull-right" onclick="add_item()"><i class="fa fa-plus"></i>&nbsp; Add</button>

                                   <label>Received Date</label>
                                   <input type="text" class="form-control" style="width:20%" id="rcv_date">
                                   <table class="table">
                                        <thead>
                                             <tr>
                                                  <th style="width: 10%">Category</th>
                                                  <th style="width: 15%">Device</th>
                                                  <th>Description</th>
                                                  <th style="width: 15%">Project</th>
                                                  <th style="width: 15%">Location</th>
                                                  <th style="width: 3%">Quantity</th>
                                                  <th style="width: 10%">PIC</th>
                                                  <th>#</th>
                                             </tr>
                                        </thead>
                                        <tbody id="body_add">
                                        </tbody>
                                   </table>
                              </div>
                         </div>
                    </div>
                    <div class="modal-footer">
                         <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                         <button type="button" class="btn btn-success pull-right" onclick="save()"><i class="fa fa-check"></i>&nbsp;Save</button>
                    </div>
               </div>
          </div>
     </div>

     <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
               <div class="modal-content">
                    <div class="modal-header">
                         <div class="col-xs-12" style="background-color: #00a65a;">
                              <h1 style="text-align: center; margin:5px; font-weight: bold;">Edit Item</h1>
                         </div>
                         <form id="udpateForm" method="post" autocomplete="off">
                              <div class="col-xs-12" style="padding-bottom: 1%; padding-top: 2%;">
                                   <div class="col-xs-3" align="right" style="padding: 0px;">
                                        <span style="font-weight: bold; font-size: 16px;">Category<span class="text-red">*</span></span>
                                   </div>
                                   <div class="col-xs-4">
                                        <input type="hidden" name="id_inv" id="id_inv">
                                        <select class="form-control select3" data-placeholder="Pilih Category" id="cat_edit" name="cat_edit" style="width: 100% height: 35px; font-size: 15px;" required>
                                             <option value=""></option>
                                        </select>
                                   </div>
                              </div>                        

                              <div class="col-xs-12" style="padding-bottom: 1%;">
                                   <div class="col-xs-3" align="right" style="padding: 0px;">
                                        <span style="font-weight: bold; font-size: 16px;">Device<span class="text-red">*</span></span>
                                   </div>
                                   <div class="col-xs-4">
                                        <select class="form-control select3" data-placeholder="Device" name="device_edit" id="device_edit" style="width: 100% height: 35px; font-size: 15px;" required>
                                             <option value=""></option>
                                        </select>
                                   </div>
                              </div>

                              <div class="col-xs-12" style="padding-bottom: 1%;">
                                   <div class="col-xs-3" align="right" style="padding: 0px;">
                                        <span style="font-weight: bold; font-size: 16px;">Description<span class="text-red">*</span></span>
                                   </div>
                                   <div class="col-xs-6">
                                        <textarea class="form-control" name="desc_edit" id="desc_edit"></textarea>
                                   </div>
                              </div>

                              <div class="col-xs-12" style="padding-bottom: 1%;">
                                   <div class="col-xs-3" align="right" style="padding: 0px;">
                                        <span style="font-weight: bold; font-size: 16px;">Project<span class="text-red">*</span></span>
                                   </div>
                                   <div class="col-xs-4">
                                        <select class="form-control select3" data-placeholder="Pilih Project" name="proj_edit" id="proj_edit" style="width: 100% height: 35px; font-size: 15px;" required>
                                             <option value=""></option>
                                        </select>
                                   </div>
                              </div>

                              <div class="col-xs-12" style="padding-bottom: 1%;">
                                   <div class="col-xs-3" align="right" style="padding: 0px;">
                                        <span style="font-weight: bold; font-size: 16px;">Location<span class="text-red">*</span></span>
                                   </div>
                                   <div class="col-xs-4">
                                        <select class="form-control select3" data-placeholder="Pilih Location" name="loc_edit" id="loc_edit" style="width: 100% height: 35px; font-size: 15px;" required>
                                             <option value=""></option>
                                        </select>
                                   </div>
                              </div>

                              <div class="col-xs-12" style="padding-bottom: 1%;">
                                   <div class="col-xs-3" align="right" style="padding: 0px;">
                                        <span style="font-weight: bold; font-size: 16px;">Qty<span class="text-red">*</span></span>
                                   </div>
                                   <div class="col-xs-3">
                                        <input class="form-control" type="number" name="qty_edit" id="qty_edit" placeholder="Jumlah Barang" style="width: 100%; height: 33px; font-size: 15px;" min="0" required>
                                   </div>
                              </div>
                              <div class="col-xs-12" style="padding-bottom: 1%;">
                                   <div class="col-xs-3" align="right" style="padding: 0px;">
                                        <span style="font-weight: bold; font-size: 16px;">Used By<span class="text-red">*</span></span>
                                   </div>
                                   <div class="col-xs-4">
                                        <input type="text" name="used_by_edit" id="used_by_edit" class="form-control">
                                   </div>
                              </div>

                              <div class="col-xs-12" style="padding-bottom: 1%;">
                                   <div class="col-xs-3" align="right" style="padding: 0px;">
                                        <span style="font-weight: bold; font-size: 16px;">Receive Date<span class="text-red">*</span></span>
                                   </div>
                                   <div class="col-xs-3">
                                        <input type="text" name="receive_date_edit" id="receive_date_edit" class="form-control">
                                   </div>
                              </div>

                              <div class="col-xs-12" style="padding-bottom: 1%;">
                                   <button class="btn btn-primary pull-right" type="submit" id="update_btn"><i class="fa fa-pencil"></i>&nbsp;Edit</button>
                              </div>
                         </form>
                    </div>
               </div>
          </div>
     </div>
</section>

@endsection
@section('scripts')
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
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

     var no = 1;
     var arr_ctg = [];
     var arr_dev = [];
     var arr_proj = [];
     var arr_loc = [];

     jQuery(document).ready(function() {
          $('body').toggleClass("sidebar-collapse");
          get_inv();
     });

     $('.select2').select2({
          dropdownAutoWidth : true,
          dropdownParent: $("#modalCreate"),
          allowClear:true,
          tags: true
     });

     $('.select3').select2({
          dropdownAutoWidth : true,
          dropdownParent: $("#updateModal"),
          allowClear:true,
          tags: true
     });

     $('#rcv_date').datepicker({
          autoclose: true,
          format: "yyyy-mm-dd",
          todayHighlight: true
     });

     $('#receive_date_edit').datepicker({
          autoclose: true,
          format: "yyyy-mm-dd",
          todayHighlight: true
     });     

     function get_inv() {
          var data = "";

          $.get('{{ url("fetch/inventory_mis/list") }}', data, function(result, status, xhr) {
               $('#table_master').DataTable().clear();
               $('#table_master').DataTable().destroy();
               $('#body_master').html("");

               var body = "";

               $.each(result.inventory, function(index, value){
                    body += "<tr>";
                    body += "<td>"+(index+1)+"</td>";
                    body += "<td>"+value.category+"</td>";
                    body += "<td>"+value.device+"</td>";
                    body += "<td>"+(value.description || '')+"</td>";
                    body += "<td>"+value.project+"</td>";
                    body += "<td>"+value.location+"</td>";
                    body += "<td>"+value.qty+"</td>";
                    body += "<td>"+value.used_by+"</td>";
                    body += "<td><button class='btn btn-primary btn-sm' onclick='openModalUpdate("+value.id+")'>Edit</button>&nbsp;<button class='btn btn-danger btn-sm' onclick='deleting("+value.id+")'>Delete</button>&nbsp;<button class='btn btn-warning btn-sm' onclick='printing("+value.id+")'><i class='fa fa-print'></i> Print</button></td>";
                    body += "</tr>";

                    arr_ctg.push(value.category);
                    arr_dev.push(value.device);
                    arr_proj.push(value.project);
                    arr_loc.push(value.location);
               })

               arr_ctg = unique(arr_ctg);
               arr_dev = unique(arr_dev);
               arr_proj = unique(arr_proj);
               arr_loc = unique(arr_loc);

               $("#body_master").append(body);

               $('#table_master tfoot th').each( function () {
                    var title = $(this).text();
                    $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
               } );

               var table = $('#table_master').DataTable({
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
                    'searching': true,
                    'ordering': true,
                    'info': true,
                    'autoWidth': true,
                    "sPaginationType": "full_numbers",
                    "bJQueryUI": true,
                    "bAutoWidth": false,
                    "processing": true,
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

               $('#table_master tfoot tr').appendTo('#table_master thead');
          })
     }

     function add_item() {
          var bodi = "";
          var ctg = "";
          var dev = "";
          var proj = "";
          var loc = "";

          ctg += "<option value=''></option>";
          dev += "<option value=''></option>";
          proj += "<option value=''></option>";
          loc += "<option value=''></option>";

          bodi += '<tr id="'+no+'" class="item">';
          bodi += '<td>';
          bodi += '<select class="select2" style="width: 100%" data-placeholder="Category" id="cat_'+no+'"></select>';
          bodi += '</td>';

          bodi += '<td>';
          bodi += '<select class="select2" style="width: 100%" data-placeholder="Device" id="dev_'+no+'"></select>';
          bodi += '</td>';

          bodi += '<td><textarea class="form-control" id="desc_'+no+'" placeholder="description"></textarea></td>';

          bodi += '<td>';
          bodi += '<select class="select2" style="width: 100%" data-placeholder="Project" id="proj_'+no+'"></select>';
          bodi += '</td>';

          bodi += '<td>';
          bodi += '<select class="select2" style="width: 100%" data-placeholder="Location" id="loc_'+no+'"></select>';
          bodi += '</td>';

          bodi += '<td><input type="number" class="form-control" placeholder="qty" id="qty_'+no+'"></td>';
          bodi += '<td><input type="text" class="form-control" placeholder="pic" id="pic_'+no+'"></td>';
          bodi += '<td><button class="btn btn-sm btn-danger" onclick="delete_item('+no+')"><i class="fa fa-trash"></i></button></td>';

          bodi += '</tr>';

          $("#body_add").append(bodi);

          $.each(arr_ctg, function(index, value){
               ctg += "<option value='"+value+"'>"+value+"</option>";
          })

          $.each(arr_dev, function(index, value){
               dev += "<option value='"+value+"'>"+value+"</option>";
          })

          $.each(arr_proj, function(index, value){
               proj += "<option value='"+value+"'>"+value+"</option>";
          })

          $.each(arr_loc, function(index, value){
               loc += "<option value='"+value+"'>"+value+"</option>";
          })

          $("#cat_"+no).append(ctg);
          $("#dev_"+no).append(dev);
          $("#proj_"+no).append(proj);
          $("#loc_"+no).append(loc);

          no++;

          $('.select2').select2({
               dropdownAutoWidth : true,
               dropdownParent: $("#modalCreate"),
               allowClear:true,
               tags: true
          });
     }

     function delete_item(no) {
          $("#"+no).remove();
     }

     function save() {
          arr_params = [];

          $('.item').each(function(index, value) {
               var ido = $(this).attr('id');

               if ($("#cat_"+ido).val() != "" && $("#dev_"+ido).val() != "") {
                    arr_params.push({'category' : $("#cat_"+ido).val(), 'device' : $("#dev_"+ido).val(), 'description' : $("#desc_"+ido).val(), 'project' : $("#proj_"+ido).val(), 'location' : $("#loc_"+ido).val(), 'quantity' : $("#qty_"+ido).val(), 'pic' : $("#pic_"+ido).val()});
               } else {

               }
          });

          var data = {
               item : arr_params,
               receive_date : $('#rcv_date').val()
          }

          $.post('{{ url("post/inventory_mis/item") }}', data, function(result, status, xhr) {
               openSuccessGritter('Success','New Item Added');
               $('#modalCreate').modal('hide');
               get_inv();
          })
     }

     function openModalUpdate(id) {
          $("#id_inv").val(id);
          var ctg = "";
          var dev = "";
          var proj = "";
          var loc = "";

          $.each(arr_ctg, function(index, value){
               ctg += "<option value='"+value+"'>"+value+"</option>";
          })

          $.each(arr_dev, function(index, value){
               dev += "<option value='"+value+"'>"+value+"</option>";
          })

          $.each(arr_proj, function(index, value){
               proj += "<option value='"+value+"'>"+value+"</option>";
          })

          $.each(arr_loc, function(index, value){
               loc += "<option value='"+value+"'>"+value+"</option>";
          })

          $("#cat_edit").append(ctg);
          $("#device_edit").append(dev);
          $("#proj_edit").append(proj);
          $("#loc_edit").append(loc);

          var data = {
               id : id
          }
          $.get('{{ url("fetch/inventory_mis") }}', data, function(result, status, xhr) {
               $("#updateModal").modal('show');

               $("#cat_edit").val(result.inventory.category).trigger('change');
               $("#device_edit").val(result.inventory.device).trigger('change');
               $("#desc_edit").val(result.inventory.description);
               $("#proj_edit").val(result.inventory.project).trigger('change');
               $("#loc_edit").val(result.inventory.location).trigger('change');
               $("#qty_edit").val(result.inventory.qty);
               $("#used_by_edit").val(result.inventory.used_by);
               $("#receive_date_edit").val(result.inventory.receive_date);
          })
     }


     $("form#udpateForm").submit(function(e){
          $("#update_btn").attr("disabled", true);
          e.preventDefault();
          var formData = new FormData(this);

          $.ajax({
               url: '{{ url("update/inventory_mis/data") }}',
               type: 'POST',
               data: formData,
               processData: false,
               cache: false,
               contentType: false,
               success: function (result, status, xhr) {
                    $("#updateModal").modal('hide');
                    $("#update_btn").prop("disabled", false);
                    openSuccessGritter("Success", "Success Update Item");

                    get_inv();
               },
               function (xhr, ajaxOptions, thrownError) {
                    $("#update_btn").prop("disabled", false);
                    openErrorGritter(xhr.status, thrownError);
               }
          })

     });

     function deleting(id) {
          if (confirm("Are you sure want to delete this item ?")) {
               var data = {
                    id : id
               }

               $.post('{{ url("delete/inventory_mis") }}', data, function(result, status, xhr) {
                    openSuccessGritter("Success", "Success Delete Item");

                    get_inv();
               })
          }
     }

     function unique(list) {
          var result = [];
          $.each(list, function(i, e) {
               if ($.inArray(e, result) == -1) result.push(e);
          });
          return result;
     }

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