@extends('layouts.visitor')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
thead input {
  width: 100%;
  padding: 5px;
  box-sizing: border-box;
}
thead>tr>th{
  text-align:center;
}
tbody>tr>td{
  text-align:center;
  font-size: 14px;
  vertical-align: middle;
}
tbody>tr>td {
  text-align:center;
  font-size: 14px;
  vertical-align: middle;
  background-color: #eee;
  color: #000;
}
tfoot>tr>th{
  text-align:center;
}
td:hover {
  overflow: visible;
}
table.table-bordered{
  border:1px solid black;
  width: 100%;
}
table.table-bordered > thead > tr > th{
  border:1px solid black;
}
table.table-bordered > tbody > tr > td{
  border:1px solid rgb(211,211,211);
  padding-top: 0;
  padding-bottom: 5px;
}
table.table-bordered > tfoot > tr > th{
  border:1px solid rgb(211,211,211);
}
label {
  font-size: 24px;
}
.content{
  color: white;
}
#loading, #error { display: none; }
</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    List Konfirmasi Pesanan Pantry
    <!-- <small>it all starts here</small> -->
  </h1>
<!--   <ol class="breadcrumb">
    <li><a href="{{ url("index/pantry/create_menu")}}" class="btn btn-primary btn-sm" style="color:white">Create Menu</a></li>
  </ol> -->
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
      <table id="example1" class="table table-bordered table-striped table-hover">
        <h2 style="text-align: center; font-weight: bold;">Pantry Order Confirmation</h2>
        <thead style="background-color: rgba(126,86,134,.7);">
          <tr>
            <th>Pemesan</th>
            <th>Minuman</th>
            <th>Keterangan</th>
            <th>Gula</th>
            <th>Jumlah</th>
            <th>Tempat</th>
            <th>Keterangan</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody id="tableBodyList">
        </tbody>
      </table>
    </div>
  </div>
</section>

<div class="modal modal-success fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Finish Order Confirmation</h4>
      </div>
      <div class="modal-body">
        <p id="adaw" style="display: none"></p>
        Are you sure want to finish the order?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <a id="modalconfirm" href="#" type="button" class="btn btn-success">Confirm</a>
      </div>
    </div>
  </div>
</div>

@stop

@section('scripts')

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

    fetchTable();
    setInterval(fetchTable, 10000);
  });

  function open_Modal(id) {
    $("#myModal").modal('show');
    $("#adaw").text(id);

    jQuery('#modalconfirm').attr("href", '{{ url("index/pantry/selesaikan") }}'+'/'+id);
  }

  function konfirmasi(id) {
    var data = {
      id:id
    }

    $.post('{{ url("index/pantry/konfirmasi") }}', data, function(result, status, xhr){

      if (result.status == true) {
        fetchTable();
        openSuccessGritter("Success","This Orders has been confirmed.");
      } else {
        openErrorGritter("Error","Failed.");
      }
    })
  }

  function fetchTable(){

    var data = {  
    }

    var audio_error = new Audio('{{ url("sounds/alarm_error.mp3") }}');
    $.get('{{ url("fetch/konfirmasi/pesanan") }}', data, function(result, status, xhr){
        if(result.status){
          $('#example1').DataTable().clear();
          $('#example1').DataTable().destroy();
          $('#tableBodyList').html("");
          var tableData = "";
          // console.log(result.pesanan.length);
          // if (result.pesanan.length == 0) {
          //   audio_error.play();
          // }
          // else{
          $.each(result.pesanan, function(key, value) {
            if (result.pesanan.length > 0) {
              if (value.status == "confirmed") {
                audio_error.play();                
              }
            }
            tableData += '<tr>';
            tableData += '<td>'+ value.name +'</td>';
            tableData += '<td>'+ value.minuman +'</td>';
            tableData += '<td>'+ value.keterangan +'</td>';
            tableData += '<td>'+ value.gula +'</td>';
            tableData += '<td>'+ value.jumlah +'</td>';
            tableData += '<td>'+ value.tempat +'</td>';
            if (value.status == "confirmed") {
              tableData += '<td><label class="label label-danger">Menunggu Konfirmasi</label></td><td width=15%><a onclick="konfirmasi('+value.id+')"" class="btn btn-primary btn-xs">Buatkan Pesanan</a></td>';
            } else if (value.status == "proses") {
              tableData += '<td><label class="label label-primary">Sedang Dibuat</label></td><td width=15%><a data-toggle="modal" onclick="open_Modal('+value.id+')" class="btn btn-success btn-xs">Selesaikan Pesanan</a></td>';
            }
            tableData += '</tr>';

          });
          // }
          $('#tableBodyList').append(tableData);
          $('#example1').DataTable({
            'dom': 'Bfrtip',
            'responsive':true,
            'lengthMenu': [
            [ 5, 10, 25, -1 ],
            [ '5 rows', '10 rows', '25 rows', 'Show all' ]
            ],
            'buttons': {
              buttons:[
              {
                extend: 'pageLength',
                className: 'btn btn-default',
              },
              
              ]
            },
            'paging': true,
            'lengthChange': true,
            'pageLength': 10,
            'searching': true,
            'ordering': true,
            'order': [],
            'info': true,
            'autoWidth': true,
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bAutoWidth": false,
            "processing": true
          });
        }
        else{
          alert('Attempt to retrieve data failed');
        }
      });


    // $('#example1').DataTable().destroy();

    // $('#example1 tfoot th').each( function () {
    //   var title = $(this).text();
    //   $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
    // } );
    // var table = $('#example1').DataTable({
    //   "order": [],
    //   'dom': 'Bfrtip',
    //   'responsive': true,
    //   'lengthMenu': [
    //   [ 10, 25, 50, -1 ],
    //   [ '10 rows', '25 rows', '50 rows', 'Show all' ]
    //   ],
    //   'buttons': {
    //     buttons:[
    //     {
    //       extend: 'pageLength',
    //       className: 'btn btn-default',
    //     },
    //     {
    //       extend: 'copy',
    //       className: 'btn btn-success',
    //       text: '<i class="fa fa-copy"></i> Copy',
    //       exportOptions: {
    //         columns: ':not(.notexport)'
    //       }
    //     },
    //     {
    //       extend: 'excel',
    //       className: 'btn btn-info',
    //       text: '<i class="fa fa-file-excel-o"></i> Excel',
    //       exportOptions: {
    //         columns: ':not(.notexport)'
    //       }
    //     },
    //     {
    //       extend: 'print',
    //       className: 'btn btn-warning',
    //       text: '<i class="fa fa-print"></i> Print',
    //       exportOptions: {
    //         columns: ':not(.notexport)'
    //       }
    //     },
    //     ]
    //   },
    //   'paging': true,
    //   'lengthChange': true,
    //   'searching': true,
    //   'ordering': true,
    //   'order': [],
    //   'info': true,
    //   'autoWidth': true,
    //   "sPaginationType": "full_numbers",
    //   "bJQueryUI": true,
    //   "bAutoWidth": false,
    //   "processing": true,
    //     // "serverSide": true,
    //     "ajax": {
    //       "type" : "post",
    //       "url" : "{{ url("fetch/konfirmasi/pesanan") }}",
    //       "data" : data,
    //     },
    //     "columns": [
    //       { "data": "name" },
    //       { "data": "minuman" },
    //       { "data": "keterangan" },
    //       { "data": "gula" },
    //       { "data": "jumlah" },
    //       { "data": "tempat" },
    //       { "data": "status" , "width": "10%"},
    //     ]
    //   }
    //   );
    

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

@stop