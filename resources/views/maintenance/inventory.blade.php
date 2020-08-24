@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link type='text/css' rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<style type="text/css">

  input {
    line-height: 22px;
  }
  thead>tr>th{
    text-align:center;
    padding: 3px;
    color: white;
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
    border:1px solid white;
  }
  table.table-bordered > thead > tr > th{
    border:1px solid white;
    color: white;
  }
  table.table-bordered > thead > tr > th > input{
    color: black;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid rgb(211,211,211);
    padding: 3px;
    background-color: #fffcb7;
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
  .dataTables_wrapper .dataTables_filter {
    float: right;
    text-align: right;
    visibility: hidden;
  }

</style>
@stop
@section('header')
<section class="content-header">
  <h1>
    {{ $title }}
    <small><span class="text-purple"> {{ $title_jp }}</span></small>
  </h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
  <div class="row">
    <div class="col-md-12" style="overflow-x: auto;">
      <table style="width: 100%; border: 1px solid white" class="table">
        <thead>
          <tr>
            <th colspan="6">SPARE PART OUT</th>
          </tr>
          <tr>
            <td width="12%">
              <input type="text" class="form-control" id="emp_id" placeholder="Scan ID Card...">
              <input type="hidden" id="emp_id2">
            </td>
            <td width="12%">
              <input type="text" class="form-control" id="barcode_scan" placeholder="Scan Part...">
            </td>
            <th width="12%">Sparepart Number</th>
            <th>Sparepart Name</th>
            <th width="7%">Stock</th>
            <th width="7%">Qty</th>
            <!-- <th width="5%"></th> -->
          </tr>
        </thead>
        <tbody id="body_out" style="color: white"></tbody>
      </table>
      <br>
      <div class="pull-right">
        @if($permission == 1)
        <button class="btn bg-purple" onclick="modalNew()"><b><i class="fa fa-plus"></i>&nbsp;New Spare Part</b></button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <a href="{{ url("/index/maintenance/inventory/in") }}" target="_blank" class="btn btn-success"><b><i class="fa fa-arrow-down"></i>&nbsp;IN</b></a>
        @endif
      </div>

      <table id="tableList" class="table table-bordered table-striped table-hover" style="width: 100%;">
        <thead style="background-color: rgba(126,86,134,.7);">
          <tr>
            <th style="width: 3%;">Part Number</th>
            <th style="width: 10%;">Part Name</th>
            <th >Specification</th>
            <th style="width: 3%;">Category</th>
            <th style="width: 3%;">Location</th>
            <th style="width: 1%;">Min Stock</th>
            <th style="width: 1%;">Stock</th>
            <th style="width: 1%;">Max Stock</th>
            <th style="width: 1%;">UOM</th>
            <th style="width: 1%;">Status</th>
            <th style="width: 7%;">User</th>
            <th style="width: 10%;">Last Update</th>
            <th style="width: 1%;"></th>
          </tr>
        </thead>
        <tbody id="tableBodyList">
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
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <div class="modal fade in" id="modalBaru">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <div class="col-xs-12" style="background-color: #605ca8;">
            <h1 class="modal-title" id="modalTitle" style="text-align: center; margin:5px; font-weight: bold; color: white">New Spare Part</h1>
          </div>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-size: 16px;">Part Number </span>
              </div>
              <div class="col-xs-6">
                <input type="text" class="form-control" id="new_part_number" placeholder="Part Number">
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-size: 16px;">Item Number </span>
              </div>
              <div class="col-xs-6">
                <input type="text" class="form-control" id="new_item_number" placeholder="Item Number (Purchase Code)">
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-size: 16px;">Part Name </span>
              </div>
              <div class="col-xs-6">
                <input type="text" class="form-control" id="new_part_name" placeholder="Part Name">
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-size: 16px;">Category</span>
              </div>
              <div class="col-xs-6">
                <select class="form-control" id="new_category" data-placeholder="Select Category" style="width: 100%">
                  <option value=""></option>
                  @foreach($category_list as $ctg)
                  <option value="{{ $ctg->category }}">{{ $ctg->category }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-size: 16px;">Specification</span>
              </div>
              <div class="col-xs-6">
                <textarea class="form-control" id="new_specification" placeholder="Specification"></textarea>
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-size: 16px;">Maker</span>
              </div>
              <div class="col-xs-6">
                <input type="text" class="form-control" id="new_maker" placeholder="Maker">
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-size: 16px;">Location</span>
              </div>
              <div class="col-xs-6">
                <select class="form-control" id="new_location" data-placeholder="Rack Location" style="width: 100%">
                  <option value=""></option>
                  @foreach($rack_list as $rack)
                  <option value="{{ $rack->location }}">{{ $rack->location }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-size: 16px;">Stock</span>
              </div>
              <div class="col-xs-6">
                <input type="text" class="form-control" id="new_stock" placeholder="Stock" onkeypress="return isNumber(event)">
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-size: 16px;">Min. Stock</span>
              </div>
              <div class="col-xs-6">
                <input type="text" class="form-control" id="new_min_stock" placeholder="Minimum Stock" onkeypress="return isNumber(event)">
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-size: 16px;">Max. Stock</span>
              </div>
              <div class="col-xs-6">
                <input type="text" class="form-control" id="new_max_stock" placeholder="Maximum stock" onkeypress="return isNumber(event)">
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-size: 16px;">UOM</span>
              </div>
              <div class="col-xs-6">
                <select class="form-control select2" id="new_uom" data-placeholder="UOM" style="width: 100%">
                  <option value=""></option>
                  @foreach($uom_list as $uom)
                  <option value="{{ $uom }}">{{ $uom }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-size: 16px;">User</span>
              </div>
              <div class="col-xs-6">
                <input type="text" class="form-control" id="new_user" placeholder="User">
              </div>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success pull-left" onclick="saving()"><i class="fa fa-check"></i> Save</button>
          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade in" id="modalEdit">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <div class="col-xs-12" style="background-color: #605ca8;">
            <h1 class="modal-title" id="modalTitle" style="text-align: center; margin:5px; font-weight: bold; color: white">Edit Spare Part</h1>
          </div>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-size: 16px;">Part Number </span>
              </div>
              <div class="col-xs-6">
                <input type="text" class="form-control" id="edit_part_number" placeholder="Part Number" readonly>
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-size: 16px;">Item Number </span>
              </div>
              <div class="col-xs-6">
                <input type="text" class="form-control" id="edit_item_number" placeholder="Item Number (Purchase Code)">
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-size: 16px;">Part Name </span>
              </div>
              <div class="col-xs-6">
                <input type="text" class="form-control" id="edit_part_name" placeholder="Part Name">
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-size: 16px;">Category</span>
              </div>
              <div class="col-xs-6">
                <select class="form-control" id="edit_category" data-placeholder="Select Category" style="width: 100%">
                  <option value=""></option>
                  @foreach($category_list as $ctg)
                  <option value="{{ $ctg->category }}">{{ $ctg->category }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-size: 16px;">Specification</span>
              </div>
              <div class="col-xs-6">
                <textarea class="form-control" id="edit_specification" placeholder="Specification"></textarea>
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-size: 16px;">Maker</span>
              </div>
              <div class="col-xs-6">
                <input type="text" class="form-control" id="edit_maker" placeholder="Maker">
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-size: 16px;">Location</span>
              </div>
              <div class="col-xs-6">
                <select class="form-control" id="edit_location" data-placeholder="Rack Location" style="width: 100%">
                  <option value=""></option>
                  @foreach($rack_list as $rack)
                  <option value="{{ $rack->location }}">{{ $rack->location }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-size: 16px;">Stock</span>
              </div>
              <div class="col-xs-6">
                <input type="text" class="form-control" id="edit_stock" placeholder="Stock" onkeypress="return isNumber(event)" readonly>
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-size: 16px;">Min. Stock</span>
              </div>
              <div class="col-xs-6">
                <input type="text" class="form-control" id="edit_min_stock" placeholder="Minimum Stock" onkeypress="return isNumber(event)">
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-size: 16px;">Max. Stock</span>
              </div>
              <div class="col-xs-6">
                <input type="text" class="form-control" id="edit_max_stock" placeholder="Maximum stock" onkeypress="return isNumber(event)">
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-size: 16px;">UOM</span>
              </div>
              <div class="col-xs-6">
                <select class="form-control" id="edit_uom" data-placeholder="UOM" style="width: 100%">
                  <option value=""></option>
                  @foreach($uom_list as $uom)
                  <option value="{{ $uom }}">{{ $uom }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-size: 16px;">User</span>
              </div>
              <div class="col-xs-6">
                <input type="text" class="form-control" id="edit_user" placeholder="User">
              </div>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success pull-left" onclick="editing()"><i class="fa fa-check"></i> Save</button>
          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
        </div>
      </div>
    </div>
  </div>

</section>

@endsection
@section('scripts')
<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
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

  var op = '<?php echo json_encode($op_mtc) ?>';
  ops = op.split('},{');
  var op2 = [];
  var scan_arr = [];

  $.each(ops, function(index, value){
    // var tmp = value.split(":")[1].substr(1, 9);
    tmp = value.replace(/\"/g, ""); 
    tmp = tmp.replace(/\[/g, ""); 
    tmp = tmp.replace(/\{/g, ""); 
    tmp = tmp.replace(/\}/g, ""); 
    tmp = tmp.replace(/\]/g, ""); 
    tmp = tmp.replace("tag:", ""); 
    tmp = tmp.replace("name:", ""); 
    tmp = tmp.replace("employee_id:", ""); 

    tmp = tmp.split(',');

    if (tmp[0] != "null") {
      op2.push({'tag': tmp[0], 'name': tmp[1], 'employee_id': tmp[2]});
    }

  })

  console.log(tmp);

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");

    $("#emp_id").focus();

    $('.select2').select2({
      dropdownParent: $('#modalBaru'),
    });

    $("#new_category").select2({
      dropdownParent: $('#modalBaru'),
      tags: true
    });

    $("#new_location").select2({
      dropdownParent: $('#modalBaru'),
      tags: true
    });

    $('#edit_uom').select2({
      dropdownParent: $('#modalEdit'),
    });

    $("#edit_category").select2({
      dropdownParent: $('#modalEdit'),
      tags: true
    });

    $("#edit_location").select2({
      dropdownParent: $('#modalEdit'),
      tags: true
    });

    get_datas();
  });

  function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
      return false;
    }
    return true;
  }

  function get_datas() {
    $.get('{{ url("fetch/maintenance/inven/list") }}', function(result, status, xhr){
      var body = "";
      if (result.inventory) {
        $.each(result.inventory, function(index, value){
          body += "<tr>";
          body += "<td>"+value.part_number+"</td>";
          body += "<td>"+value.part_name+"</td>";
          body += "<td>"+value.specification+"</td>";
          body += "<td>"+value.category+"</td>";
          body += "<td>"+value.location+"</td>";
          body += "<td style='background-color:#ffccff'>"+value.min_stock+"</td>";
          body += "<td>"+value.stock+"</td>";
          body += "<td style='background-color:#ffccff'>"+value.max_stock+"</td>";
          body += "<td>"+value.uom+"</td>";

          if (value.stock <= value.min_stock) {
            cls = 'label label-danger';
            txt = 'ASAP ORDER';
          } else if (value.stock <= (value.min_stock * 1.5)) {
            cls = 'label label-warning';
            txt = 'ORDER';
          } else {
            cls = 'label label-success';
            txt = 'READY';
          }

          body += "<td><span class='"+cls+"'>"+txt+"</span></td>";

          body += "<td>"+value.user+"</td>";
          body += "<td>"+value.updated_at+"</td>";

          if ('{{$permission}}' == 1) {
            body += "<td><button class='btn btn-warning btn-xs' onclick='modalEdit(\""+value.part_number+"\")'><i class='fa fa-pencil'></i></button></td>";
          } else {
            body += "<td>-</td>";
          }
          body += "</tr>";
        })

        $("#tableBodyList").append(body);

        var table = $('#tableList').DataTable({
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
          'pageLength': 25,
          'paging': true,
          'lengthChange': true,
          'searching': true,
          'ordering': true,
          'info': true,
          "sPaginationType": "full_numbers",
          "bJQueryUI": true,
          "bAutoWidth": false,
          "processing": true,
        });

        $('#tableList tfoot th').each( function () {
          var title = $(this).text();
          $(this).html( '<input style="text-align: center; width: 100%" type="text" placeholder="Search '+title+'" size="3" class="search"/>' );
        });

        table.columns().every( function () {
          var that = this;
          $( '.search', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
              that
              .search( this.value )
              .draw();
            }
          });
        });
        $('#tableList tfoot tr').appendTo('#tableList thead'); 
      }
    })
  }

  function modalNew() {
    $("#modalBaru").modal('show');
  }

  function saving() {
    var data = {
      part_number : $("#new_part_number").val(),
      item_number : $("#new_item_number").val(),
      part_name : $("#new_part_name").val(),
      category : $("#new_category").val(),
      specification : $("#new_specification").val(),
      maker : $("#new_maker").val(),
      location : $("#new_location").val(),
      stock : $("#new_stock").val(),
      min : $("#new_min_stock").val(),
      max : $("#new_max_stock").val(),
      uom : $("#new_uom").val(),
      user : $("#new_user").val(),
    }

    $.post('{{ url("post/maintenance/inven/list/save") }}', data, function(result, status, xhr){
      openSuccessGritter("Success", "Spare parts Added Successfully");
    })
  }

  function modalEdit(id) {
    var data = {
      part_number : id
    }

    $.get('{{ url("fetch/maintenance/inven/list/item") }}', data, function(result, status, xhr){
      $("#edit_part_number").val(result.datas.part_number);
      $("#edit_item_number").val(result.datas.item_number);
      $("#edit_part_name").val(result.datas.part_name);
      $("#edit_category").val(result.datas.category).trigger("change");;
      $("#edit_specification").val(result.datas.specification);
      $("#edit_maker").val(result.datas.maker);
      $("#edit_location").val(result.datas.location).trigger("change");;
      $("#edit_stock").val(result.datas.stock);
      $("#edit_min_stock").val(result.datas.min_stock);
      $("#edit_max_stock").val(result.datas.max_stock);
      $("#edit_uom").val(result.datas.uom).trigger("change");
      $("#edit_user").val(result.datas.user);

    })

    $("#modalEdit").modal('show');
  }

  function editing() {
    var data = {
      part_number : $("#edit_part_number").val(),
      item_number : $("#edit_item_number").val(),
      part_name : $("#edit_part_name").val(),
      category : $("#edit_category").val(),
      specification : $("#edit_specification").val(),
      maker : $("#edit_maker").val(),
      location : $("#edit_location").val(),
      stock : $("#edit_stock").val(),
      min : $("#edit_min_stock").val(),
      max : $("#edit_max_stock").val(),
      uom : $("#edit_uom").val(),
      user : $("#edit_user").val()
    }

    $.post('{{ url("post/maintenance/inven/list/edit") }}', data, function(result, status, xhr){
      openSuccessGritter("Success", "Spare parts Added Successfully");
    })
  }

  $('#emp_id').keyup(function(e){
    if(e.keyCode == 13)
    {
      var stat = 0;
      var name = "";
      var emp_id = "";
      var vals = $(this).val();
      $.each(op2, function(index, value){
        if (vals == value.tag) {
          stat = 1;
          name = value.name;
          emp_id = value.employee_id;
        }
      })

      if (stat == 1) {
        $(this).val(name);
        $("#emp_id2").val(emp_id);
        $(this).attr("readonly","true");
        $("#barcode_scan").focus();
        openSuccessGritter('Success', '');
      } else {
        $(this).val("");
        openErrorGritter('Error', 'Karyawan Tidak Terdaftar');
      }
    }
  });

  $('#barcode_scan').keyup(function(e){
    if(e.keyCode == 13)
    {
      var body = "";
      var vals = $(this).val();

      var data = {
        code: vals
      }

      $(this).val("");
      $.get('{{ url("fetch/maintenance/inven/code") }}', data, function(result, status, xhr){
        if (result.status) {
          if (result.datas.stock < 1) {
            openErrorGritter('Gagal', 'Stok '+result.datas.part_number+' tidak Mencukupi');
            return false;
          }

          if (scan_arr.length == 0) {
            scan_arr.push({'item_number' : result.datas.part_number, 'part_name' : result.datas.part_name+" - "+result.datas.specification, 'stock' : result.datas.stock, 'qty' : 1});
            minus_stok(result.datas.part_number, 'out');
          } else {
            var qty = 0;
            var stat = 0;

            $.each(scan_arr, function(index, value){
              if (typeof value.item_number !== 'undefined') {
                if (value.item_number == result.datas.part_number) {
                  qty = parseInt(value.qty);
                  stat = 1;

                  if (result.datas.stock < qty+1) {
                    openErrorGritter('Gagal', 'Stok '+result.datas.part_number+' tidak Mencukupi');
                    return false;
                  }

                  value.qty = qty + 1;
                  minus_stok(result.datas.part_number, 'out');
                }
              }
            })

            if (stat == 0) {
              scan_arr.push({'item_number' : result.datas.part_number, 'part_name' : result.datas.part_name+" - "+result.datas.specification, 'stock' : result.datas.stock, 'qty' : 1});
              openSuccessGritter('Sukses', 'Sparepart Berhasil discan');
            }
          }

          $("#body_out").empty();

          $.each(scan_arr, function(index, value){
            body += "<tr>";
            body += "<td></td>";
            body += "<td></td>";
            body += "<td>"+value.item_number+"</td>";
            body += "<td>"+value.part_name+"</td>";
            body += "<td>"+value.stock+"</td>";
            body += "<td id='"+value.item_number+"'>"+value.qty+"</td>";
            body += "</tr>";
          })

          $("#body_out").append(body);
        } else {
          openErrorGritter('Gagal', 'Kode Part '+vals+' Tidak Terdaftar');
        }
      })
    }
  })

  function minus_stok(material_number, stat) {
    var data = {
      material_number : material_number,
      status : stat,
      employee_id: $("#emp_id2").val()
    }
    $.post('{{ url("post/maintenance/inven/transaction") }}', data, function(result, status, xhr){
      if (result.status) {
        openSuccessGritter('Sukses', 'Sparepart berhasil dikurangi');
      } else {
        openErrorGritter('Gagal', result.message);
      }
    })

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
