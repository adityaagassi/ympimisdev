@extends('layouts.master')
@section('stylesheets')
<style type="text/css">
thead input {
  width: 100%;
  padding: 3px;
  box-sizing: border-box;
}
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
  @if (session('status'))
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
    {{ session('status') }}
  </div>   
  @endif
  <div class="row">
    <div class="col-xs-12">
      <div class="box box-primary">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="box-body">
          <div class="row">
            <div class="col-md-12">
              <table id="tableFlo" class="table table-bordered table-striped table-hover">
                <thead>
                  <tr>
                    <th style="font-size: 14">FLO Number</th>
                    <th style="font-size: 14">Serial Number</th>
                    <th style="font-size: 14">Material</th>
                    <th style="font-size: 14">Description</th>
                    <th style="font-size: 14">Quantity</th>
                    <th style="font-size: 14">Completion</th>
                    <th style="font-size: 14">Transfer</th>
                    <th style="font-size: 14">Status</th>
                    <th style="font-size: 14">Created At</th>
                    <th style="font-size: 14">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($flo_details as $flo_detail)
                  <tr>
                    <td style="font-size: 14">{{$flo_detail->flo_number}}</td>
                    <td style="font-size: 14">{{$flo_detail->serial_number}}</td>
                    <td style="font-size: 14">{{$flo_detail->material_number}}</td>
                    <td style="font-size: 14">{{$flo_detail->material_description}}</td>
                    <td style="font-size: 14">{{$flo_detail->quantity}}</td>
                    <td style="font-size: 14">{{$flo_detail->completion}}</td>
                    <td style="font-size: 14">{{$flo_detail->transfer}}</td>
                    <th style="font-size: 14">{{$flo_detail->status}}</th>
                    <td style="font-size: 14">{{$flo_detail->created_at}}</td>
                    <td>
                      <center>
                        <a href="javascript:void(0)" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#modalDelete" onclick="deleteConfirmation('{{ url("destroy/flo_deletion") }}', '{{ $flo_detail['serial_number'] }} | {{ $flo_detail['material_number'] }} | {{ $flo_detail['material_description'] }}', '{{ $flo_detail['id'] }}');">
                          Delete
                        </a>
                      </center>
                    </td>
                  </tr>
                  @endforeach
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

<div class="modal modal-danger fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
      </div>
      <div class="modal-body" id="modalDeleteBody">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
  jQuery(document).ready(function() {
    $('#tableFlo tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
    } );
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
      }
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

    $('#tableFlo tfoot tr').appendTo('#tableFlo thead');

  });

  function deleteConfirmation(url, name, id) {
    jQuery('#modalDeleteBody').text("Are you sure want to delete '" + name + "'");
    jQuery('#modalDeleteButton').attr("href", url+'/'+id);
  }
</script>

@stop