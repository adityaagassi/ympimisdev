@extends('layouts.display')
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
    text-align:left;
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
  .dataTable > thead > tr > th[class*="sort"]:after{
    content: "" !important;
  }
  #queueTable.dataTable {
    margin-top: 0px!important;
  }
  #loading, #error { display: none; }
  .description-block {
    margin-top: 0px
  }
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<section class="content" style="padding-top: 0; color: #ecf0f1">
  <div class="main" style="text-align: center;">
    <h1><i class="fa fa-file-text-o"></i> {{ $head }}</h1><br>

    <?php if ($status == 1) { ?>
      <table align="center">
        <tr>
          <td>Jenis Pekerjaan</td>
          <td>&nbsp; :&nbsp; {{$data->type}}</td>
        </tr>
        <tr>
          <td>Kategori</td>
          <td>&nbsp; :&nbsp; {{$data->category}}</td>
        </tr>
        <tr>
          <td>Kondisi Mesin</td>
          <td>&nbsp; :&nbsp; {{$data->machine_condition}}</td>
        </tr>
        <tr>
          <td>Sumber Bahaya</td>
          <td>&nbsp; :&nbsp; {{$data->danger}}</td>
        </tr>
        <tr>
          <td>Penjelasan Pekerjaan</td>
          <td>&nbsp; :&nbsp; {{$data->description}}</td>
        </tr>
      </table><br>

    <?php } ?>

    <p style="font-weight: bold;">
      {{ $message }}<br>
      {{ $message2 }}
    </p>

    <?php if ($status == 1) { ?>
      <center>
        <textarea class="form-control" style="width: 40%;" id="danger_note" placeholder="tuliskan catatan bahaya..."></textarea><br>
        <button id="ok_button" class="btn btn-success" onclick="post()"><i class="fa fa-check"></i>&nbsp; OK</button>
      </center>
    <?php } ?>
    
  </div>
  <div class="sub_main" style="text-align: center; display: none">
    <h1><i class="fa fa-file-text-o"></i> {{ $head }}</h1><br>
    <p style="font-weight: bold;">
      SPK dengan Order No. {{ $head }}<br>
      <i class="fa fa-check-circle" style="color: #00a65a"></i>&nbsp;Berhasil diverifikasi
    </p>
  </div>
</section>
@endsection 
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");
  });

  function post() {
    var data = {
      order_no : '{{$head}}',
      danger_note : $('#danger_note').val()
    }

    $.post('{{ url("verify/maintenance/spk/danger_note") }}', data, function(result, status, xhr){
      if (result.status) {
        $(".sub_main").show(); 
        $(".main").hide();
        openSuccessGritter("Success", result.message);
      } else {
        openErrorGritter("Error", result.message);
      }
    })

  }

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