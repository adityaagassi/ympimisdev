@extends('layouts.master')
@section('stylesheets')
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
    padding-top: 5px;
    padding-bottom: 5px;
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
    List of Overtime Forms <span class="text-purple">Japanese</span>
  </h1>
  <ol class="breadcrumb">
    <li>
      <a href="{{ url("create/overtime/overtime_form")}}" class="btn btn-success btn-sm" style="color:white"><i class="fa fa-plus"></i> Create {{ $page }}</a>
    </li>
  </ol>
</section>
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-body">
          <table id="overtimeTable" class="table table-bordered table-striped">
            <thead style="background-color: rgba(126,86,134,.7);">
              <tr>
                <th>OT Date</th>
                <th>OT ID</th>
                <th>Division</th>
                <th>Departement</th>
                <th>Section</th>
                <th>Sub Section</th>
                <th>Group</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modal_detail">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 style="float: right;" id="modal-title"></h4>
            <h4 class="modal-title"><b id="head">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="col-md-4">
                  <p>Hari</p>
                  <p>Tanggal</p>
                  <p>Bagian</p>
                </div>
                <div class="col-md-8">
                  <p>: <c id="hari"></c></p>
                  <p>: <c id="tgl"></c></p>
                  <p>: <c id="dep"></c> - <c id="sec"></c> - <c id="subsec"></c> - <c id="group"></p>
                  </div>
                </div>

                <div class="col-md-6">
                  <p>Purpose : </p>
                  <input type="text" class="form-control" readonly id="kep" style="height:70px;">
                </div>

                <div class="col-md-12">
                  <table class="table table-bordered table-striped table-hover" style="margin-top: 5px;width: 100%;">
                    <thead style="background-color: rgba(126,86,134,.7);">
                      <tr>
                        <th>No</th>
                        <th>Employee ID</th>
                        <th>Nama karyawan</th>
                        <th>OT Start</th>
                        <th>OT End</th>
                        <th>Hour</th>
                        <th>Transport</th>
                        <th>Food</th>
                        <th>E-Food</th>
                      </tr>
                    </thead>
                    <tbody id="details">
                    </tbody>
                    <tfoot>
                      <tr>
                        <th colspan="4"><p align="left">B = Bangil, P = Pasuruan</p></th>
                        <th>Total : </th>
                        <th><p id="total" align="center"></p></th>
                        <th colspan="4"></th>
                      </tr>
                    </tfoot>
                  </table>
                </div>

                <div class="col-md-12">
                  Notes :
                  <textarea class="form-control" readonly id="note"></textarea>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
              <button type="button" class="btn btn-primary pull-right" onclick="print()"><i class="fa fa-print"></i> Print</button>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
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

    var overtime_id;
    var table;

    $(document).ready(function() {
      drawTable();
    })

    function drawTable() {
      table = $('#overtimeTable').DataTable({
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
        'paging'        : true,
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
          "url" : "{{ url("fetch/overtime") }}",
        },
        "columns": [
        { "data": "overtime_date"},
        { "data": "overtime_id"},
        { "data": "division"},
        { "data": "department"},
        { "data": "section"},
        { "data": "subsection"},
        { "data": "group"},
        { "data": "action"}
        ]
      });
    }

    function details(id) {
      $("#modal_detail").modal("show");
      overtime_id = id;

      var data = {
        overtime_id:id
      };

      var weekday = new Array(7);
      weekday[0] =  "Minggu";
      weekday[1] = "Senin";
      weekday[2] = "Selasa";
      weekday[3] = "Rabu";
      weekday[4] = "Kamis";
      weekday[5] = "Jum'at";
      weekday[6] = "Sabtu";      

      $.get('{{ url("fetch/overtime/detail") }}',data, function(result, status, xhr){
        var purposeArr = new Array();
        var noteArr = new Array();
        var total = 0;
        var no = 1;

        var tgl_new = result.data_details[0].overtime_date.split('-');
        var dateObject = new Date(+tgl_new[2], tgl_new[1] - 1, +tgl_new[0]);

        var hari = weekday[dateObject.getDay()];

        $("#modal-title").text("ID SPL : "+result.data_details[0].overtime_id);
        $("#hari").text(hari);
        $("#tgl").text(result.data_details[0].overtime_date);
        $("#dep").text(result.data_details[0].department);
        $("#sec").text(result.data_details[0].section);
        $("#subsec").text(result.data_details[0].subsection);
        $("#group").text(result.data_details[0].group);

        $("#details").empty();

        $.each(result.data_details, function(index, value) {
          var food = "";
          var ext_food = "";

          if (value.food == 1) {
            food = "&#10004;";
          }

          if (value.ext_food) {
            ext_food = "&#10004;";
          }

          $("#details").append("<tr><td>"+no+"</td><td>"+value.employee_id+"</td><td>"+value.name+"</td><td>"+value.start_time+"</td><td>"+value.end_time+"</td><td>"+value.final_hour+"</td><td>"+value.transport+"</td><td>"+food+"</td><td>"+ext_food+"</td></tr>");
          purposeArr.push(value.purpose);
          total += parseInt(value.final_hour);
          no++;
          noteArr.push(value.remark);
        })

        var myNewArray = purposeArr.filter(function(elem, index, self) {

          return index === self.indexOf(elem);

        });

        var newNoteArr = noteArr.filter(function(elem, index, self) {

          return index === self.indexOf(elem);

        });

        $("#kep").val(myNewArray);
        $("#total").html(total+"&nbsp;&nbsp; Jam");
        $("#note").val(newNoteArr);

      })
    }

    function delete_ot(id) {
      var data = {
        id : id
      };

      if (confirm("Are you sure want to delete SPL "+id+"?")) {
        $.post('{{ url("delete/overtime") }}', data, function(result, status, xhr){
          table.ajax.reload();
        })
      }
    }

    function edit(id) {
      var wndw = '{{ url("index/overtime/edit/") }}/'+id;
      window.location.href = wndw;
    }

    function print() {
      var wndw = '{{ url("index/overtime/print/") }}/'+overtime_id;
      window.open(wndw , '_blank');
    }
  </script>
  @endsection