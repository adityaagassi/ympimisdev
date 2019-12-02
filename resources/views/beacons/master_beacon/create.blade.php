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
    Create {{ $page }}
    <small>it all starts here</small>
  </h1>
  <ol class="breadcrumb">
   {{--  <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="#">Examples</a></li>
    <li class="active">Blank page</li> --}}
  </ol>
</section>
@endsection
@section('content')
<section class="content">


  @if ($errors->has('password'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
    {{ $errors->first() }}
  </div>   
  @endif
  @if (session('error'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Error!</h4>
    {{ session('error') }}
  </div>   
  @endif


  <!-- SELECT2 EXAMPLE -->
<!-- Button trigger modal -->
<button type="button" class="btn-sm btn-info" data-toggle="modal" data-target="#exampleModal">
  Daftar Master Cubeacon
</button>
<br><br>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <center><h1 class="modal-title" id="exampleModalLabel">Daftar Master</h1></center>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

      <form role="form" method="post" action="{{url('index/master_beacon/daftar')}}">
      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row" align="right">
          <label class="col-sm-4">UUID<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="UUID" placeholder="Enter UUID Reader" required>
          </div>
        </div>

        <div class="form-group row" align="right">
          <label class="col-sm-4">Lokasi<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="lokasi" placeholder="Enter Lokasi" required>
          </div>
        </div>

        <div class="form-group row" align="right">
          <label class="col-sm-4">Distance<span class="text-red">*</span></label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="distance" placeholder="Enter Distance" required>
          </div>
        </div>
        
        <div class="col-sm-4 col-sm-offset-6">
          <div class="btn-group">
            <button type="submit" class="btn btn-primary col-sm-14">Submit</button>
          </div>
        </div>
      </div>
    </form>
    </div>
      
    </div>
  </div>
</div>


  <div class="box box-primary">
    <div class="box-header with-border">
      {{-- <h3 class="box-title">Create Master Cubeacon</h3> --}}
    </div>
    
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-body">
             <table id="example1" class="table table-bordered table-striped table-hover">
              <thead style="background-color: rgba(126,86,134,.7);">
                <tr>
                  <!-- <th>ID</th> -->
                  <th>UUID</th>
                  <th>Lokasi</th> 
                  <th>Distance</th>        
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($cr as $beacon)
                <tr>
                    <!-- <td>{{ $beacon->id }}</td> -->
                    <td>{{ $beacon->uuid }}</td>
                    <td>{{ $beacon->lokasi }}</td>
                    <td>{{ $beacon->distance }}</td>
                    <td style="width: 10%">
                    <center>
                      <button class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit" onclick="modalEdit({{ $beacon->id }})">Edit</button>
                      <a href="javascript:void(0)" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("index/master_beacon/delete") }}', '{{ $beacon['name'] }}', '{{ $beacon['id'] }}');">
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
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="EditModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <center><h1 class="modal-title" id="exampleModalLabel">Edit Master</h1></center>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">

            <form role="form" method="post" action="{{url('index/master_beacon/edit')}}">
              <div class="box-body">
                <input type="hidden" value="{{csrf_token()}}" name="_token" />
                <div class="form-group row" align="right">
                  <label class="col-sm-4">UUID</label>
                  <div class="col-sm-4">
                    <input type="text" class="form-control" name="UUID"  id="uuid_edit">
                  </div>
                </div>

                <div class="form-group row" align="right">
                  <label class="col-sm-4">Lokasi</label>
                  <div class="col-sm-4">
                    <input type="text" class="form-control" name="lokasi"  id="lokasi_edit">
                  </div>
                </div>

                <div class="form-group row" align="right">
                  <label class="col-sm-4">Distance</label>
                  <div class="col-sm-4">
                    <input type="text" class="form-control" name="distance"  id="distance_edit">
                  </div>
                </div>

                <div class="col-sm-4 col-sm-offset-6">
                  <div class="btn-group">
                    <button type="submit" class="btn btn-primary col-sm-14">Submit</button>
                  </div>
                </div>
              </div>
            </form>
          </div>

        </div>
      </div>
    </div>


<div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
      </div>
      <div class="modal-body">
        Are you sure want to delete this?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
      </div>
    </div>
  </div>
</div>
  </div>



  @endsection

  @section('scripts')
  <script>
    $(function () {
      $('.select2').select2()
    });

    jQuery(document).ready(function() {
      $('#email').val('');
      $('#password').val('');
    });
  </script>    
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
    $('#tgl_permintaan').datepicker({
        format: "dd/mm/yyyy",
        autoclose: true,
        todayHighlight: true
      });
      $('#tgl_balas').datepicker({
        format: "dd/mm/yyyy",
        autoclose: true,
        todayHighlight: true
      });
      $('.select2').select2({
        language : {
          noResults : function(params) {
            return "There is no cpar with status 'close'";
          }
        }
      });

    $('#example1 tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
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

      $('#example1 tfoot tr').appendTo('#example1 thead');

    });

    $(function () {
      $('#example2').DataTable({
        'paging'      : true,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false
      })
    })

    function modalEdit(id) {
      $('#EditModal').modal("show");
      var data = {
        id:id
      };
            $.ajax({
                url: "{{ route('admin.beaconedit') }}?id=" + id,
                method: 'GET',
                success: function(data) {
                  var json = data;
              
                  console.log(data.uuid);
             
                  $("#uuid_edit").val(data.uuid);
                  $("#lokasi_edit").val(data.lokasi);
                  $("#distance_edit").val(data.distance);
                  
                }
            });
    }

    function deleteConfirmation(url, name, id) {
      jQuery('.modal-body').text("Are you sure want to delete '" + name + "'");
      jQuery('#modalDeleteButton').attr("href", url+'/'+id);
    }

  function clearConfirmation(){
    location.reload(true);
  }
  </script>
  @stop

