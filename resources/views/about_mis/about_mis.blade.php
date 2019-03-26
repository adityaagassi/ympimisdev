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
      border:1px solid rgb(144,144,144);
      padding-top: 1;
      padding-bottom: 1;
  }
  table.table-bordered > tfoot > tr > th{
      border:1px solid rgb(144,144,144);
  }
  #loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
    <h1>
        About MIS<span class="text-purple"> ???</span>
        {{-- <small>WIP Control <span class="text-purple"> 仕掛品管理</span></small> --}}
    </h1>
</section>
@stop
@section('content')
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box-body no-padding">
                <ul class="users-list clearfix">
                    <li>
                        <img src="{{ url('/dist/img/C99020314.jpg') }}" alt="User Image">
                        <a class="users-list-name" href="#">Romy (1160)</a>
                    </li>
                    <li>
                        <img src="{{ url('/dist/img/R14122906.jpg') }}" alt="User Image">
                        <a class="users-list-name" href="#">Agassi (1188)</a>
                    </li>
                    <li>
                        <img src="{{ url('/dist/img/E01030740.jpg') }}" alt="User Image">
                        <a class="users-list-name" href="#">Agus (1189)</a>
                    </li>
                    <li>
                        <img src="{{ url('/dist/img/J06021069.jpg') }}" alt="User Image">
                        <a class="users-list-name" href="#">Buyung (1168)</a>
                    </li>
                    <li>
                        <img src="{{ url('/dist/img/M09061339.jpg') }}" alt="User Image">
                        <a class="users-list-name" href="#">Anton (1168)</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <table id="projectTable" class="table table-bordered table-striped table-hover">
                <thead style="background-color: rgba(126,86,134,.7);">
                    <tr>
                        <th>Project</th>
                        <th>Description</th>
                        <th>Start</th>
                        <th>Finish</th>
                        <th>Total Investment</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projects as $project)
                    <tr>
                        <td>{{$project->project}}</td>
                        <td>{{$project->description}}</td>
                        <td>{{$project->start_date}}</td>
                        <td>{{$project->finish_date}}</td>
                        <td>{{$project->total_investment}}</td>
                        <td><a href="{{url('show/mis_investment', $project->project)}}" class="btn btn-info btn-xs">Details</a></td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                </tfoot>
            </table>
        </div>
    </div>
</section>
@endsection
@section('scripts')
<script>
</script>
@endsection