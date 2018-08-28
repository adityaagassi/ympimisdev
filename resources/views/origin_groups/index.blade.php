@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    List of Origin Groups
    <small>it all starts here</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{ url("create/origin_group")}}" class="btn btn-primary btn-sm" style="color:white">Create Origin Group</a></li>
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
                    <th>Origin Group Code</th>
                    <th>Origin Group Name</th>
                    <th>Created By</th>
                    <th>Created At</th>
                    <th>Action</th>
                    {{-- <th>Edit</th>
                      <th>Delete</th> --}}
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($origin_groups as $origin_group)
                    <tr>
                      <td style="font-size: 14">{{$origin_group->origin_group_code}}</td>
                      <td style="font-size: 14">{{$origin_group->origin_group_name}}</td>
                      <td style="font-size: 14">{{$origin_group->user->name}}</td>
                      <td style="font-size: 14">{{$origin_group->created_at}}</td>
                    {{-- <td>
                      <form action="{{ url('destroy/user', $user['id']) }}" method="post">
                                {{ csrf_field() }}
                                <button class="btn btn-xs btn-danger" type="submit">Delete</button>
                      </form>
                    </td> --}}
                    <td>
                      <center>
                      <a class="btn btn-info btn-xs" href="{{url('show/origin_group', $origin_group['id'])}}">View</a>
                      <a href="{{url('edit/origin_group', $origin_group['id'])}}" class="btn btn-warning btn-xs">Edit</a>
                      <a href="javascript:void(0)" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("destroy/origin_group") }}', '{{ $origin_group['origin_group_name'] }}', '{{ $origin_group['id'] }}');">
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