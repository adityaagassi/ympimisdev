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
    FLO Deletion
    <small>it all starts here</small>
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
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="box-body">
          <div class="row">
            <div class="col-md-12">
              <table id="tableFlo" class="table table-bordered table-striped table-hover">
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>FLO Number</th>
                    <th style="width: 4%;">Serial Number</th>
                    <th style="width: 4%;">Material</th>
                    <th style="width: 45%;">Description</th>
                    <th style="width: 4%;">Qty</th>
                    <th style="width: 4%;">Completion</th>
                    <th style="width: 4%;">Transfer</th>
                    <th style="width: 4%;">Status</th>
                    <th style="width: 27%;">Created At</th>
                    <th style="width: 4%;">Act</th>
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
  
  jQuery(document).ready(function() {
    fetchTableFLO();
  });

  function fetchTableFLO(){
    $('#tableFlo tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
    });
    var table = $('#tableFlo').DataTable({
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
        "url" : "{{ url("fetch/flo_deletion") }}",
        // "data" : data,
      },
      "columns": [
      { "data": "flo_number" },
      { "data": "serial_number" },
      { "data": "material_number" },
      { "data": "material_description" },
      { "data": "quantity" },
      { "data": "completion" },
      { "data": "transfer" },
      { "data": "status" },
      { "data": "created_at" },
      { "data": "action" }
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
    });

    $('#tableFlo tfoot tr').appendTo('#tableFlo thead');
  }


  function deleteConfirmation(id){
    var data = {
      id: id,
    };
    if(confirm("Are you sure you want to delete this data?")){
      $.post('{{ url("destroy/flo_deletion") }}', data, function(result, status, xhr){
        console.log(status);
        console.log(result);
        console.log(xhr);
        if(xhr.status == 200){
          if(result.status){
            $('#tableFlo').DataTable().ajax.reload();
            alert(result.message);
          }
          else{
            alert('Attempt to retrieve data failed');
          }
        }
        else{
          alert('Disconnected from server');
        }
      });
    }
    else{
      return false;
    }
  }

</script>

@stop