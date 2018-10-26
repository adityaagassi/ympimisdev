@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    List of {{ $page }}s
    <small>it all starts here</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{ url("create/container")}}" class="btn btn-primary btn-sm" style="color:white">Create {{ $page }}</a></li>
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
        <div class="box-body">
          <table id="example1" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Container Code</th>
                <th>Container Name</th>
                <th>Capacity (m&sup3;)</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($containers as $container)
              <tr>
                <td style="font-size: 14">{{$container->container_code}}</td>
                <td style="font-size: 14">{{$container->container_name}}</td>
                <td style="font-size: 14">{{ round($container->capacity,4)}}</td>
                <td>
                  <center>
                    <a class="btn btn-info btn-xs" href="{{url('show/container', $container['id'])}}">View</a>
                    <a href="{{url('edit/container', $container['id'])}}" class="btn btn-warning btn-xs">Edit</a>
                    <a href="javascript:void(0)" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("destroy/container") }}', '{{ $container['container_name'] }}', '{{ $container['id'] }}');">
                      Delete
                    </a>
                  </center>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>

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
    $('#example1').DataTable({
      "order": []
    })
  })
  function deleteConfirmation(url, name, id) {
    jQuery('.modal-body').text("Are you sure want to delete '" + name + "'");
    jQuery('#modalDeleteButton').attr("href", url+'/'+id);
  }
</script>

@stop