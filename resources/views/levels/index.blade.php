@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    List of Levels
    <small>it all starts here</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{ url("create/level")}}" class="btn btn-primary btn-sm" style="color:white">Create Level</a></li>
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
      <div class="box">
        <div class="box">
           {{--  <div class="box-header">
              <h3 class="box-title">Data Table With Full Features</h3>
            </div> --}}
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Level Name</th>
                    <th>Action</th>
                    {{-- <th>Edit</th>
                      <th>Delete</th> --}}
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($levels as $level)
                    <tr>
                      <td style="font-size: 14">{{$level->id}}</td>
                      <td style="font-size: 14">{{$level->level_name}}</td>
                    {{-- <td>
                      <form action="{{ url('destroy/user', $user['id']) }}" method="post">
                                {{ csrf_field() }}
                                <button class="btn btn-xs btn-danger" type="submit">Delete</button>
                      </form>
                    </td> --}}
                    <td>
                      <center>
                      <a class="btn btn-info btn-xs" href="{{url('show/level', $level['id'])}}">View</a>
                      <a href="{{url('edit/level', $level['id'])}}" class="btn btn-warning btn-xs">Edit</a>
                      <a href="javascript:void(0)" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("destroy/level") }}', '{{ $level['level_name'] }}', '{{ $level['id'] }}');">
                        Delete
                      </a>
                    </center>
                    </td>
                  </tr>
                  @endforeach
                </table>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->

      </section>
      <!-- /.content -->

    <div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
          </div>
          <div class="modal-body">
            Are you sure delete?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
          </div>
        </div>
      </div>
    </div>

    @stop

    @section('scripts')
    <script>
      $(function () {
        $('#example1').DataTable()
        $('#example2').DataTable({
          'paging'      : true,
          'lengthChange': false,
          'searching'   : false,
          'ordering'    : true,
          'info'        : true,
          'autoWidth'   : false
        })
      })
      function deleteConfirmation(url, name, id) {
        jQuery('.modal-body').text("Are you sure want to delete '" + name + "'");
        jQuery('#modalDeleteButton').attr("href", url+'/'+id);
      }
    </script>

    @stop