@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

  .morecontent span {
    display: none;
  }
  .morelink {
    display: block;
  }

  thead>tr>th{
    text-align:center;
    overflow:hidden;
    padding: 3px;
  }
  tbody>tr>td{
    text-align:center;
  }
  tfoot>tr>th{
    text-align:center;
  }
  th:hover {
    overflow: visible;
  }
  td:hover {
    overflow: visible;
  }
  table.table-bordered{
    border:1px solid black;
  }
  table.table-bordered > thead > tr > th{
    border:1px solid black;
    background-color: #a488aa;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid black;
    vertical-align: middle;
    padding:0;
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid black;
    padding:0;
  }
  td{
    overflow:hidden;
    text-overflow: ellipsis;
  }
</style>
@stop
@section('header')
<section class="content-header">
  <h1>
    GA - Report<span class="text-purple"> </span>
  </h1>
</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-body">
          <div class="col-xs-12">

            <div class="box box-solid">
              <div class="box-body">
                <form class="form-horizontal">
                  <div class="form-group">
                    <label for="datepicker" class="col-sm-2 control-label">Tanggal</label>

                    <div class="col-sm-3">
                      <input type="text" class="form-control datepicker" id="datepicker" placeholder="Select date" onchange="changeTanggal(); ">
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label">Total Makan</label>
                    <div class="col-sm-2">
                      <table class="table table-bordered table-striped text-center" id="shf1">
                        <thead>
                          <tr><th>Shift 1</th></tr>
                        </thead>
                        <tbody>
                          <tr><td id='makan1' onclick="makan(1,'Shift 1')">0</td></tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="col-sm-2">
                      <table class="table table-bordered table-striped text-center" id="shf1">
                        <thead>
                          <tr><th>Shift 2</th></tr>
                        </thead>
                        <tbody>
                          <tr><td id='makan2' onclick="makan(2,'Shift 2')">0</td></tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="col-sm-2">
                      <table class="table table-bordered table-striped text-center" id="shf1">
                        <thead>
                          <tr><th>Shift 3</th></tr>
                        </thead>
                        <tbody>
                          <tr><td id='makan3' onclick="makan(3,'Shift 3')">0</td></tr>
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <!-- ali -->
                  <div class="form-group">
                    <label class="col-sm-2 control-label">Total Extra Food</label>
                    <div class="col-sm-2">
                      <table class="table table-bordered table-striped text-center" id="shf1">
                        <thead>
                          <tr><th>Shift 1</th></tr>
                        </thead>
                        <tbody>
                          <tr><td id='extmakan1' onclick="extmakan(1, 'Shift 1')">0</td></tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="col-sm-2">
                      <table class="table table-bordered table-striped text-center" id="shf1">
                        <thead>
                          <tr><th>Shift 2</th></tr>
                        </thead>
                        <tbody>
                          <tr><td id='extmakan2' onclick="extmakan(2, 'Shift 2')">0</td></tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="col-sm-2">
                      <table class="table table-bordered table-striped text-center" id="shf1">
                        <thead>
                          <tr><th>Shift 3</th></tr>
                        </thead>
                        <tbody>
                          <tr><td id='extmakan3' onclick="extmakan(3, 'Shift 3')">0</td></tr>
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label">Transport</label>
                    <div class="col-sm-6">
                      <table class="table table-bordered table-striped table-hover text-center" id="trs">
                        <thead>
                          <tr>
                            <th></th>
                            <th scope="col" width="30%">Bangil</th>
                            <th scope="col" width="30%">Pasuruan</th>
                          </tr>
                        </thead>
                        <tbody id="trans">

                        </tbody>
                      </table>
                    </div>
                  </div>

                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
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

  });

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

  function changeTanggal() {
    var data = {
      tanggal : $("#datepicker").val()
    }

    arr = [];

    $.get('{{ url("fetch/report/ga_report") }}', data, function(result, status, xhr){
      $.each(result.datas, function(index, value){
        arr.push(value.ovtplanfrom+","+value.ovtplanto);
      })

      var arr2 = arr.filter(function(elem, index, self) {

        return index === self.indexOf(elem);

      });

      arr3 = [];

      for (var z = 0; z < arr2.length; z++) {
        var bgl = 0, psr = 0;
        for (var i = 0; i < result.datas.length; i++) {
          var base = arr2[z].split(",");
          if (result.datas[i].ovtplanfrom == base[0] && result.datas[i].ovtplanto == base[1]) {
            if (result.datas[i].ovttrans == "TRNPSR") {
              psr += 1;
            } else if (result.datas[i].ovttrans == "TRNBGL") {
              bgl += 1;
            }
          }
        }

        arr3.push([arr2[z], bgl, psr]);
      }

      var trans_body = "";

      $.each(arr3, function(index, value){
        trans_body +="<tr>";
        trans_body +="<td>"+value[0]+"</td>";
        trans_body +="<td>"+value[1]+"</td>";
        trans_body +="<td>"+value[2]+"</td>";
        trans_body +="</tr>";
      })

      $("#trans").append(trans_body);
    })
  }

  $('#datepicker').datepicker({
   autoclose: true,
   format: "dd-mm-yyyy",
 });

  function openSuccessGritter(title, message){
   jQuery.gritter.add({
    title: title,
    text: message,
    class_name: 'growl-success',
    image: '{{ url("images/image-screen.png") }}',
    sticky: false,
    time: '3000'
  });
 }

 function openErrorGritter(title, message) {
   jQuery.gritter.add({
    title: title,
    text: message,
    class_name: 'growl-danger',
    image: '{{ url("images/image-stop.png") }}',
    sticky: false,
    time: '3000'
  });
 }	
</script>
@endsection