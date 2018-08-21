@extends('layouts.master')
@section('header')
<section class="content-header">
      <h1>
        Blank page
        <small>it all starts here</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Examples</a></li>
        <li class="active">Blank page</li>
      </ol>
    </section>
@endsection


@section('content')

<section class="content">
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
                  <th>Full Name</th>
                  <th>Username</th>
                  <th>E-mail</th>
                  <th>Created By</th>
                  <th>Created At</th>
                </tr>
                </thead>
                <tbody>
                  @foreach($users as $users)
                <tr>
                  <td>{{$users['name']}}</td>
                  <td>{{$users['username']}}</td>
                  <td>{{$users['email']}}</td>
                  <td>{{$users['created_by']}}</td>
                  <td>{{$users['created_at']}}</td>
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
  </div>
  <!-- /.content-wrapper -->

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
</script>

@stop