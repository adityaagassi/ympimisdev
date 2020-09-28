@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  h5 {
    font-size: 16px;
  }
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

  h1 {
    color: white;
    text-align: center;
  }
</style>
@stop
@section('content')
<section class="content">
  <div class="row" style="padding-top: 0; overflow-y:hidden; overflow-x:auto;">
    <h1>Utility</h1>
    <table border="1">
      <tr id="master_body">
      </tr>
    </table>
  </div>
</section>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
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
    $('body').toggleClass("sidebar-collapse");

    getList();
  });

  function getList() {
    var body = "";

    $.get('{{ url("fetch/maintenance/pic") }}', function(result, status, xhr) {
      $.each(result.datas, function(index, value){
        body += '<td style="vertical-align: top">';
        body += '<div class="col-xs-2">';
        body += '<div class="card" style="width: 18rem; background-color: white; border-radius: 2px; padding: 3px">';
        body += '<img class="card-img-top" src="{{ url("images/avatar")}}/'+value.pic_id+'.JPG" alt="Card image cap" style="width: 100%">';
        body += '<div class="card-body">';
        body += '<center>';
        body += '<h4 class="card-title" style="font-weight: bold">'+value.pic_name+'</h4>';
        body += '<h5 class="card-title">( '+value.pic_id+' )</h5>';
        body += '</center>';
        body += '<div>';

        var items = value.item.split(",");

        var skills = value.skill.split(",");

        $.each(items, function(index2, value2){
          body += '<p class="card-text" style="text-align:left; padding: 0px 3px 0px 3px;">'+value2+' <b> ['+skills[index2]+'] </b> </p>';
          body += '<hr style="margin: 1px 0px 1px 0px">';
        })

        body += '</div></div></div></div>';
        body += '</td>';
      })

      $("#master_body").append(body);
    })

  }

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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