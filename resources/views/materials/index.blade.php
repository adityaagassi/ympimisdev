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
    List of {{ $page }}s
    <small>it all starts here</small>
  </h1>
  <ol class="breadcrumb">
    <li>
      <a data-toggle="modal" data-target="#importModal" class="btn btn-success btn-sm" style="color:white">Import {{ $page }}s</a>
      &nbsp;
      <a data-toggle="modal" data-target="#createModal" class="btn btn-primary btn-sm" style="color:white">Create {{ $page }}</a>
    </li>
  </ol>
</section>
@endsection


@section('content')
<section class="content">
  @if (session('status'))
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
    {{ session('status') }}
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
        <div class="box-body">
          <table id="example1" class="table table-bordered table-striped table-hover">
            <thead style="background-color: rgba(126,86,134,.7);">
              <tr>
                <th>Material Number</th>
                <th>Description</th>
                <th>BUn</th>
                <th>SLoc</th>
                <th>MRPC</th>
                <th>ValCl</th>
                <th>Origin Group</th>
                <th>HPL</th>
                <th>Cat.</th>
                <th>Mod.</th>
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
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="modal fade" id="createModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Create {{$page}}</h4>
      </div>
      <div class="modal-body">
        <div class="box-body">
          <input type="hidden" value="{{csrf_token()}}" name="_token" />
          <div class="form-group row" align="right">
            <label class="col-sm-4">Material Number<span class="text-red">*</span></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="material_number" placeholder="Enter Material Number" required>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Material Description<span class="text-red">*</span></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="material_description" placeholder="Enter Material Description" required>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Base Unit<span class="text-red">*</span></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="base_unit" placeholder="Enter Base Unit" required>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Issue Storage Location<span class="text-red">*</span></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="issue_storage_location" placeholder="Enter Issue Storage Location" required>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">MRPC<span class="text-red">*</span></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="mrpc" placeholder="MRPC" required>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Valuation Class<span class="text-red">*</span></label>
            <div class="col-sm-6" align="left">
              <select class="form-control select2" id="valcl" style="width: 100%;" data-placeholder="Choose a Valuation Class..." required>
                <option value=""></option>
                @foreach($valcls as $valcl)
                <option value="{{ $valcl }}">{{ $valcl }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Origin Group<span class="text-red">*</span></label>
            <div class="col-sm-6" align="left">
              <select class="form-control select2" id="origin_group_code" style="width: 100%;" data-placeholder="Choose an Origin Group..." required>
                <option value=""></option>
                @foreach($origin_groups as $origin_group)
                <option value="{{ $origin_group->origin_group_code }}">{{ $origin_group->origin_group_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">HPL<span class="text-red">*</span></label>
            <div class="col-sm-6" align="left">
              <select class="form-control select2" id="hpl" style="width: 100%;" data-placeholder="Choose a HPL..." required>
                <option value=""></option>
                @foreach($hpls as $hpl)
                <option value="{{ $hpl }}">{{ $hpl }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Category<span class="text-red">*</span></label>
            <div class="col-sm-6" align="left">
              <select class="form-control select2" id="category" style="width: 100%;" data-placeholder="Choose a Category..." required>
                <option value=""></option>
                @foreach($categories as $category)
                <option value="{{ $category }}">{{ $category }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Model<span class="text-red">*</span></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="model" placeholder="Enter Model" required>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
        <button type="button" onclick="create()" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-plus"></i> Create</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="ViewModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Detail {{$page}}</h4>
      </div>
      <div class="modal-body">
        <div class="box-body">
          <input type="hidden" value="{{csrf_token()}}" name="_token" />
          <div class="form-group row" align="right">
            <label class="col-sm-6">Material Number : </label>
            <div class="col-sm-6" align="left" id="material_number_view"></div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-6">Material Description : </label>
            <div class="col-sm-6" align="left" id="material_description_view"></div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-6">Base Unit : </label>
            <div class="col-sm-6" align="left" id="base_unit_view"></div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-6">Storage Location : </label>
            <div class="col-sm-6" align="left" id="storage_location_view"></div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-6">MRPC : </label>
            <div class="col-sm-6" align="left" id="mrpc_view"></div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-6">Valuation Class : </label>
            <div class="col-sm-6" align="left" id="valuation_class_view"></div>
          </div>  
          <div class="form-group row" align="right">
            <label class="col-sm-6">Origin Group : </label>
            <div class="col-sm-6" align="left" id="origin_group_view"></div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-6">HPL : </label>
            <div class="col-sm-6" align="left" id="hpl_view"></div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-6">Category : </label>
            <div class="col-sm-6" align="left" id="category_view"></div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-6">Created By : </label>
            <div class="col-sm-6" align="left" id="created_by_view"></div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-6">Last Update : </label>
            <div class="col-sm-6" align="left" id="last_updated_view"></div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-6">Created At : </label>
            <div class="col-sm-6" align="left" id="created_at_view"></div>
          </div>
        </div>    
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="EditModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Edit {{$page}}</h4>
      </div>
      <div class="modal-body">
        <div class="box-body">
          <input type="hidden" value="{{csrf_token()}}" name="_token" />
          <div class="form-group row" align="right">
            <label class="col-sm-4">Material Number<span class="text-red">*</span></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="material_number_edit" placeholder="Enter Material Number" required readonly>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Material Description<span class="text-red">*</span></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="material_description_edit" placeholder="Enter Material Description" required>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Base Unit<span class="text-red">*</span></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="base_unit_edit" placeholder="Enter Base Unit" required>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Issue Storage Location<span class="text-red">*</span></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="issue_storage_location_edit" placeholder="Enter Issue Storage Location" required>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">MRPC<span class="text-red">*</span></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="mrpc_edit" placeholder="MRPC" required>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Valuation Class<span class="text-red">*</span></label>
            <div class="col-sm-6" align="left">
              <select class="form-control select2" id="valcl_edit" style="width: 100%;" data-placeholder="Choose a Valuation Class..." required>
                <option value=""></option>
                @foreach($valcls as $valcl)
                <option value="{{ $valcl }}">{{ $valcl }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Origin Group<span class="text-red">*</span></label>
            <div class="col-sm-6" align="left">
              <select class="form-control select2" id="origin_group_code_edit" style="width: 100%;" data-placeholder="Choose an Origin Group..." required>
                <option value=""></option>
                @foreach($origin_groups as $origin_group)
                <option value="{{ $origin_group->origin_group_code }}">{{ $origin_group->origin_group_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">HPL<span class="text-red">*</span></label>
            <div class="col-sm-6" align="left">
              <select class="form-control select2" id="hpl_edit" style="width: 100%;" data-placeholder="Choose a HPL..." required>
                <option value=""></option>
                @foreach($hpls as $hpl)
                <option value="{{ $hpl }}">{{ $hpl }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Category<span class="text-red">*</span></label>
            <div class="col-sm-6" align="left">
              <select class="form-control select2" id="category_edit" style="width: 100%;" data-placeholder="Choose a Category..." required>
                <option value=""></option>
                @foreach($categories as $category)
                <option value="{{ $category }}">{{ $category }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Model<span class="text-red">*</span></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="model_edit" placeholder="Enter Model" required>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
        <input type="hidden" id="id_edit">
        <button type="button" onclick="edit()" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-pencil"></i> Edit</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id ="importForm" method="post" action="{{ url('import/material') }}" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Import Confirmation</h4>
          Format: [Material Number][Description][Uom][SLoc][Mrpc][ValCl][Origin Group][HPL][Category]<br>
          Sample: <a href="{{ url('download/manual/import_material.txt') }}">import_material.txt</a> Code: #Truncate
        </div>
        <div class="">
          <div class="modal-body">
            <center><input type="file" name="material" id="InputFile" accept="text/plain"></center>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button id="modalImportButton" type="submit" class="btn btn-success">Import</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@stop

@section('scripts')
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
    $('.select2').select2();
    drawTable();
  });

  function drawTable() {
    $('#example1 tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
    } );
    var table = $('#example1').DataTable({
     "order": [],
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
    "serverSide": true,
    "ajax": {
      "type" : "get",
      "url" : "{{ url("fetch/material") }}"
    },
    "columns": [
    { "data": "material_number", "width" : "2%" },
    { "data": "material_description", "width" : "10%"},
    { "data": "base_unit", "width" : "2%" },
    { "data": "issue_storage_location", "width" : "2%" },
    { "data": "mrpc", "width" : "2%" },
    { "data": "valcl", "width" : "2%" },
    { "data": "origin_group_name", "width" : "2%" },
    { "data": "hpl", "width" : "2%" },
    { "data": "category", "width" : "2%" },
    { "data": "model", "width" : "2%" },
    { "data": "action", "width" : "5%" }
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
      } );
    } );

    $('#example1 tfoot tr').appendTo('#example1 thead');
  }

  function create() {
    var data = {
      material_number: $("#material_number").val(),
      material_description: $("#material_description").val(),
      base_unit: $("#base_unit").val(),
      issue_storage_location : $("#issue_storage_location").val(),
      mrpc : $("#mrpc").val(),
      valcl : $("#valcl").val(),
      origin_group_code : $("#origin_group_code").val(),
      hpl : $("#hpl").val(),
      category : $("#category").val(),
      model : $("#model").val()   
    };

    $.post('{{ url("create/material") }}', data, function(result, status, xhr){
      if (result.status == true) {
        $('#example1').DataTable().ajax.reload(null, false);
        openSuccessGritter("Success","New Material has been created.");
      } else {
        openErrorGritter("Error","Material not created.");
      }
    })
  }

  function modalView(id) {
    $("#ViewModal").modal("show");
    var data = {
      id:id
    };

    $.get('{{ url("view/material") }}', data, function(result, status, xhr){
      $("#material_number_view").text(result.datas[0].material_number);
      $("#material_description_view").text(result.datas[0].material_description);
      $("#base_unit_view").text(result.datas[0].base_unit);
      $("#storage_location_view").text(result.datas[0].issue_storage_location);
      $("#mrpc_view").text(result.datas[0].mrpc);
      $("#valuation_class_view").text(result.datas[0].valcl);
      $("#origin_group_view").text(result.datas[0].origin_group_name);
      $("#hpl_view").text(result.datas[0].hpl);
      $("#category_view").text(result.datas[0].category);
      $("#created_by_view").text(result.datas[0].name);
      $("#last_updated_view").text(result.datas[0].updated_at);
      $("#created_at_view").text(result.datas[0].created_at);
    })
  }

  function modalEdit(id) {
    $('#EditModal').modal("show");

    var data  = {
      id:id
    };

    $.get('{{ url("edit/material") }}', data, function(result, status, xhr){
      $("#id_edit").val(id);
      $('#material_number_edit').val(result.datas.material_number);
      $("#material_description_edit").val(result.datas.material_description);
      $("#base_unit_edit").val(result.datas.base_unit);
      $("#issue_storage_location_edit").val(result.datas.issue_storage_location);
      $("#mrpc_edit").val(result.datas.mrpc);
      $("#valcl_edit").val(result.datas.valcl).trigger('change.select2');
      $("#origin_group_code_edit").val(result.datas.origin_group_code).trigger('change.select2');
      $("#hpl_edit").val(result.datas.hpl).trigger('change.select2');
      $("#category_edit").val(result.datas.category).trigger('change.select2');
      $("#model_edit").val(result.datas.model);
    })
  }

  function modalDelete(id, material_number) {
    var data = {
      id: id
    };

    if (!confirm("Are you sure want to delete Material ' "+material_number+" ' ?")) {
      return false;
    }

    $.post('{{ url("delete/material") }}', data, function(result, status, xhr){
      $('#example1').DataTable().ajax.reload(null, false);
      openSuccessGritter("Success","Material ' "+material_number+" ' has been deleted.");
    })
  }

  function edit() {
   var data = {
    id: $("#id_edit").val(),
    material_description: $("#material_description_edit").val(),
    base_unit: $("#base_unit_edit").val(),
    issue_storage_location: $("#issue_storage_location_edit").val(),
    mrpc: $("#mrpc_edit").val(),
    valcl: $("#valcl_edit").val(),
    origin_group_code: $("#origin_group_code_edit").val(),
    hpl: $("#hpl_edit").val(),
    category: $("#category_edit").val(),
    model: $("#model_edit").val(),
  };

  $.post('{{ url("edit/material") }}', data, function(result, status, xhr){
    if (result.status == true) {
      $('#example1').DataTable().ajax.reload(null, false);
      openSuccessGritter("Success","Material has been edited.");
    } else {
      openErrorGritter("Error","Failed to edit material.");
    }
  })
}

function deleteConfirmation(url, name, id) {
  jQuery('#modalDeleteBody').text("Are you sure want to delete '" + name + "'");
  jQuery('#modalDeleteButton').attr("href", url+'/'+id);
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

@stop