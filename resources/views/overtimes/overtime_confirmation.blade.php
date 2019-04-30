@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
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
     .tes {
          background: #00a65a !important;
          border-color: #000000 !important;
          color: #FFFFFF !important;
          font-weight: bold !important;
     }
</style>
@endsection
@section('header')
<section class="content-header">
     <h1>
          Overtime Confirmation <span class="text-purple"> jepang </span>
     </h1>
     <ol class="breadcrumb">
     </ol>
</section>
@endsection


@section('content')

<section class="content">
     <div class="row">
          <div class="col-xs-12">
               <div class="box">
                    <div class="box-body">
                         <table id="overtimeConfirmationTable" class="table table-bordered table-striped table-hover">
                              <thead style="background-color: rgba(126,86,134,.7);">
                                   <tr>
                                        <th>OT No.</th>
                                        <th>OT Date</th>
                                        <th>NIK</th>
                                        <th>Name</th>
                                        <th>Section</th>
                                        <th>In</th>
                                        <th>Out</th>
                                        <th>OT Plan</th>
                                        <th>#</th>
                                        <th>OT Log</th>
                                        <th>#</th>
                                        <th>Diff</th>
                                        <th>OT</th>
                                        <th>Action</th>
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
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                   </tr>
                              </tfoot>
                         </table>
                         <br>
                         <button class="btn btn-success btn-lg btn-block" onclick="confirm_all()" style="font-size: 25px"><i class="fa fa-check"></i> Confirm</button>
                    </div>
               </div>
          </div>
     </div>

     <div class="modal fade" id="editModal">
          <div class="modal-dialog">
               <div class="modal-content">
                    <div class="modal-header">
                         <h4 class="modal-title"><b>Edit Overtime Hour(s)</b><input type="text" id="id_ot" class="pull-right" style="text-align: center; width: 100px;" disabled></h4>
                    </div>
                    <div class="modal-body">
                         <div class="row">
                              <div class=" col-md-12">
                                   <div class="row">
                                        <div class="col-md-4" style="padding-right: 0;">
                                             <label>Tanggal</label><input type="text" id="tgl" class="form-control" disabled>
                                             <input type="hidden" id="tgl2">
                                             <input type="hidden" id="hari">
                                        </div>
                                        <div class="col-md-3">
                                             <label>NIK</label><input type="text" id="nik" class="form-control" disabled>
                                        </div>
                                        <div class="col-md-5" style="padding-left: 0;">
                                             <label>Nama</label><input type="text" id="nama" class="form-control" disabled>
                                        </div>
                                   </div>
                                   <div class="row">
                                        <div class="col-md-3" style="padding-right: 0;">
                                             <label>Masuk</label><input type="text" id="masuk" class="form-control" disabled>
                                        </div>
                                        <div class="col-md-3" style="padding-right: 0;">
                                             <label>Keluar</label><input type="text" id="keluar" class="form-control" disabled>
                                        </div>
                                        <div class="col-md-2" style="padding-right: 0;">
                                             <label>OT by Log</label><input type="text" id="ot-log" class="form-control" disabled>
                                        </div>
                                        <div class="col-md-2" style="padding-right: 0;">
                                             <label>OT by SPL</label><input type="text" id="ot-spl" class="form-control" disabled>
                                        </div>
                                        <div class="col-md-2">
                                             <label>Diff</label><input type="text" id="diff" class="form-control" disabled>
                                        </div>
                                   </div>
                                   <div class="row">
                                        <br>
                                        <div class="col-md-12">
                                             <center>
                                                  <b><label style="font-size: 30px">Change Overtime</label></b>
                                             </center>
                                        </div>
                                        <div class="col-md-offset-4 col-md-4">
                                             <input type="text" id="ot-final" class="form-control timepicker" style="text-align: center; font-size: 42px; height: 60px; border-color: red">
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </div>
                    <div class="modal-footer">
                         <button type="button" class="btn btn-primary pull-right" onclick="edit_ot()">Confirm</button>
                         <button type="button" class="btn btn-danger pull-right" onclick="delete_ot()" style="margin-right: 5px;">Delete</button>
                         <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    </div>
               </div>
               <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
     </div>

</section>


@stop

@section('scripts')
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
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

          $('#overtimeConfirmationTable tfoot th').each(function(){
               var title = $(this).text();
               $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="4"/>' );
          });

          var table = $('#overtimeConfirmationTable').DataTable({
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
                    }
                    ]
               },
               "columnDefs": [ {
                    "targets": [7, 8],
                    "createdCell": function (td, cellData, rowData, row, col) {
                         $(td).css('background-color', 'RGB(204,255,255,0.50)')
                    }
               },
               {
                    "targets": [9, 10],
                    "createdCell": function (td, cellData, rowData, row, col) {
                         $(td).css('background-color', 'RGB(255,255,204,0.50)')
                    }
               },
               {
                    "targets": [12],
                    "createdCell": function (td, cellData, rowData, row, col) {
                         $(td).css('background-color', 'RGB(255,204,255,0.50)')
                    }
               }],
               'paging': true,
               'lengthChange': true,
               'searching': true,
               'ordering': false,
               'order': [],
               'info': true,
               'autoWidth': true,
               "sPaginationType": "full_numbers",
               "bJQueryUI": true,
               "bAutoWidth": false,
               "processing": true,
               "ajax": {
                    "type" : "get",
                    "url" : "{{ url("fetch/overtime_confirmation") }}"
               },
               "columns": [
               { "data": "id"},
               { "data": "tanggal", "width": "7%" },
               { "data": "nik" },
               { "data": "name", "width": "35%"},
               { "data": "section" },
               { "data": "masuk" },
               { "data": "keluar" },
               { "data": "plan_ot" },
               { "data": "ot", "width": "1%" },
               { "data": "act_log" },
               { "data": "log", "width": "1%" },
               { "data": "diff" },
               { "data": "act_log" },
               { "data": "edit" }
               ]
          });

          $('#overtimeConfirmationTable').find("thead th").removeClass("sorting_asc");

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

          $('#overtimeConfirmationTable tfoot tr').appendTo('#overtimeConfirmationTable thead');
     });

function delete_ot(){
     var id_ot = $("#id_ot").val();
     var nik = $("#nik").val();
     var data = {
          id_ot: id_ot,
          nik: nik
     }
     if(confirm("Are you sure to delete overtime of this person?")){
          $.post('{{ url("delete/overtime_confirmation") }}', data, function(result, status, xhr){
               console.log(status);
               console.log(result);
               console.log(xhr);
               if(xhr.status == 200){
                    if(result.status){
                         openSuccessGritter('Success', result.message);
                         $('#overtimeConfirmationTable').DataTable().ajax.reload();                         
                    }
                    else{
                         audio_error.play();
                         openErrorGritter('Error!', result.message);
                    }
               }
               else{
                    audio_error.play();
                    alert("Disconnected from server");
               }
          });
     }
     else{
          return false;
     }
}

function confirmation(id, jam, nama) {
     var str = id.split("+");
     if (str[0] = 'ot_log')
          var txt = "Are you sure to confirm "+nama+" , with Actual Check Log "+jam+" hour(s) ?";
     else
          var txt = "Are you sure to confirm "+nama+" , with Plan OT "+jam+" hour(s) ?";

     if(confirm(txt)){

          var data = {
               tanggal : str[2],
               nik : str[1],
               id_ot : str[3],
               jam : jam,
               hari : str[4]
          }
          $.post('{{ url("confirm/overtime_confirmation") }}', data, function(result, status, xhr){
               console.log(status);
               console.log(result);
               console.log(xhr);
               if(xhr.status == 200){
                    if(result.status){
                         openSuccessGritter('Success', result.message);
                         $('#overtimeConfirmationTable').DataTable().ajax.reload();
                    }
                    else{
                         audio_error.play();
                         openErrorGritter('Error', result.message);
                    }
               }
               else{
                    audio_error.play();
                    alert('Disconnected from server');
               }
          });
     }
}

function confirm_all() {
     if(confirm("Are you sure you want to confirm data?")){
          var datas = [];

          $("input[type=radio]:checked").each(function() {
               if(this.checked == true)
               {
                    var str = this.name.split("+");
                    var data = [
                    str[1], 
                    str[2],
                    str[3],
                    this.value,
                    str[4]
                    ];

                    datas.push(data);
               }
          });

          var data = {
               confirm : datas
          }

          $.post('{{ url("confirm/overtime_confirmation") }}', data, function(result, status, xhr){
               console.log(status);
               console.log(result);
               console.log(xhr);
               if(xhr.status == 200){
                    if(result.status){
                         openSuccessGritter('Success', result.message);
                         $('#overtimeConfirmationTable').DataTable().ajax.reload();
                    }
                    else{
                         audio_error.play();
                         openErrorGritter('Error', result.message);
                    }
               }
               else{
                    audio_error.play();
                    alert('Disconnected from server');
               }

          });
     }
     else{
          return false;
     }
}

function edit_ot() {
     var jam_act = $("#ot-log").val();
     var jam_final = $("#ot-final").val();
     var tgl = $("#tgl2").val();
     var nik = $("#nik").val();
     var final_dec = timeStringToFloat(jam_final);
     var hari = $("#hari").val();

     if(confirm("Are you sure to change overtime hour(s) \nfrom "+jam_act+" to "+final_dec+" ?"))
     {
          console.log(final_dec,nik,tgl);
          var data = {
               jam : final_dec,
               nik : nik,
               tgl : tgl,
               hari : hari
          }

          $.post('{{ url("edit/overtime_confirmation") }}', data, function(result, status, xhr){
               console.log(status);
               console.log(result);
               console.log(xhr);
               if(xhr.status == 200){
                    if(result.status){
                         openSuccessGritter('Success', result.message);
                         $('#overtimeConfirmationTable').DataTable().ajax.reload();
                    }
                    else{
                         audio_error.play();
                         openErrorGritter('Error', result.message);
                    }
               }
               else{
                    audio_error.play();
                    alert('Disconnected from server');
               }

          });

          $("#editModal").modal("hide");
     }
}


function timeStringToFloat(time) {
     var hoursMinutes = time.split(/[.:]/);
     var hours = parseInt(hoursMinutes[0], 10);
     var minutes = hoursMinutes[1] ? parseInt(hoursMinutes[1], 10) : 0;
     return hours + minutes / 60;
}

function editModal(id, masuk, keluar, nama, diff, tanggal, tgl2, id_ot) {
     var str = id.split("+");
     var nik = str[1];
     var id_ot = str[2];
     var jam_plan = str[3];
     var jam_act = str[4];
     var hari = str[5];

     $("#editModal").modal("show");
     $("#tgl").val(tanggal);
     $("#tgl2").val(tgl2);
     $("#hari").val(hari);
     $("#nik").val(nik);
     $("#nama").val(nama);
     $("#masuk").val(masuk);
     $("#keluar").val(keluar);
     $("#ot-log").val(jam_act);
     $("#ot-spl").val(jam_plan);
     $("#diff").val(diff);
     $("#id_ot").val(id_ot);
}

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

$('.timepicker').timepicker({
     use24hours: true,
     showInputs: false,
     showMeridian: false,
     minuteStep: 30,
     defaultTime: '00:00'
})

</script>

@stop