@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    List of {{ $page }}s
    <small>it all starts here</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{ url("create/batch_setting")}}" class="btn btn-primary btn-sm" style="color:white">Create {{ $page }}</a></li>
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
           {{--  <div class="box-header">
              <h3 class="box-title">Data Table With Full Features</h3>
            </div> --}}
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Batch Time</th>
                    <th>Upload</th>    
                    <th>Download</th>
                    <th>Remark</th>
                    <th>Action</th>
                    {{-- <th>Edit</th>
                      <th>Delete</th> --}}
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($batch_settings as $batch_setting)
                    <tr>
                      <td style="font-size: 14">{{ date('H:i', strtotime($batch_setting->batch_time))}}</td>
                      <td style="font-size: 14">
                        @if($batch_setting->upload == 1)
                        ON
                        @else
                        OFF
                        @endif
                      </td>
                      <td style="font-size: 14">
                        @if($batch_setting->download == 1)
                        ON
                        @else
                        OFF
                      @endif</td>
                      <td style="font-size: 14">{{$batch_setting->remark}}
                      </td>
                      <td>
                        <center>
                          <a class="btn btn-info btn-xs" href="{{url('show/batch_setting', $batch_setting['id'])}}">View</a>
                          <a href="{{url('edit/batch_setting', $batch_setting['id'])}}" class="btn btn-warning btn-xs">Edit</a>
                          <a href="javascript:void(0)" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("destroy/batch_setting") }}', '{{ $batch_setting['remark'] }}', '{{ $batch_setting['id'] }}');">
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