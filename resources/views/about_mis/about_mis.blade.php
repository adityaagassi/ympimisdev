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
                        <a class="users-list-name" href="#">Romy</a>
                    </li>
                    <li>
                        <img src="{{ url('/dist/img/R14122906.jpg') }}" alt="User Image">
                        <a class="users-list-name" href="#">Agassi</a>
                    </li>
                    <li>
                        <img src="{{ url('/dist/img/E01030740.jpg') }}" alt="User Image">
                        <a class="users-list-name" href="#">Agus</a>
                    </li>
                    <li>
                        <img src="{{ url('/dist/img/J06021069.jpg') }}" alt="User Image">
                        <a class="users-list-name" href="#">Buyung</a>
                    </li>
                    <li>
                        <img src="{{ url('/dist/img/M09061339.jpg') }}" alt="User Image">
                        <a class="users-list-name" href="#">Anton</a>
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
                </tbody>
                <tfoot>
                    <tr>
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
</section>
@endsection
@section('scripts')
<script>
</script>
@endsection