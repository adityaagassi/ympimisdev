@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    List of Materials
    <small>it all starts here</small>
  </h1>
  <ol class="breadcrumb">

    <li>
      <a data-toggle="modal" data-target="#importModal" class="btn btn-success btn-sm" style="color:white">Upload Material</a>
      &nbsp;
      <a href="{{ url("create/material")}}" class="btn btn-primary btn-sm" style="color:white">Create Material</a>
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
        <div class="box">
           {{--  <div class="box-header">
              <h3 class="box-title">Data Table With Full Features</h3>
            </div> --}}
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Material Number</th>
                    <th>Description</th>
                    <th>Base Unit</th>
                    <th>SLoc</th>
                    <th>Origin Group</th>
                    <th>Created By</th>
                    <th>Created At</th>
                    <th>Action</th>
                    {{-- <th>Edit</th>
                      <th>Delete</th> --}}
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($materials as $material)
                    <tr>
                      <td style="font-size: 14">{{$material->material_number}}</td>
                      <td style="font-size: 14">{{$material->material_description}}</td>
                      <td style="font-size: 14">{{$material->base_unit}}</td>
                      <td style="font-size: 14">{{$material->issue_storage_location}}</td>
                      <td style="font-size: 14">{{$material->origingroup->origin_group_name}}</td>
                      <td style="font-size: 14">{{$material->user->name}}</td>
                      <td style="font-size: 14">{{$material->created_at}}</td>
                    {{-- <td>
                      <form action="{{ url('destroy/user', $user['id']) }}" method="post">
                                {{ csrf_field() }}
                                <button class="btn btn-xs btn-danger" type="submit">Delete</button>
                      </form>
                    </td> --}}
                    <td>
                      <center>
                        <a class="btn btn-info btn-xs" href="{{url('show/material', $material['id'])}}">View</a>
                        <a href="{{url('edit/material', $material['id'])}}" class="btn btn-warning btn-xs">Edit</a>
                        <a href="javascript:void(0)" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("destroy/material") }}', '{{ $material['material_description'] }}', '{{ $material['id'] }}');">
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
            <div class="modal-body" id="modalDeleteBody">
              Are you sure delete?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
              <a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
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
          jQuery('#modalDeleteBody').text("Are you sure want to delete '" + name + "'");
          jQuery('#modalDeleteButton').attr("href", url+'/'+id);
        }
      </script>

      @stop