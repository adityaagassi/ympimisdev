@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  thead input {
    width: 100%;
    padding: 3px;
    box-sizing: border-box;
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
  td{
    overflow:hidden;
    text-overflow: ellipsis;
  }
  #loading, #error { display: none; }
</style>
@endsection
@section('header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content-header">
  <h1>
    List of {{ $page }}
    <small>We Always Care To You</small>
  </h1>
  <ol class="breadcrumb">
    <!-- <li><a href="{{ url("index/form_experience/create")}}" class="btn btn-success btn-sm" style="color:white"><i class="fa fa-plus"></i>Buat {{ $page }}</a></li> -->
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

        <div class="box-body" style="overflow-x: scroll;">
          <table id="tableResult" class="table table-bordered table-striped table-hover" >
            <thead style="background-color: rgba(126,86,134,.7);">
              <tr>
                <!-- <th>Nama</th> -->
                <th>Tanggal</th>
                <th>Nama</th>
                <th>Jam Masuk</th>
                <th>Location Masuk</th>
                <th>Jam Keluar</th>
                <th>Location Keluar</th>
                <!-- <th>Latitude</th>
                <th>Longitude</th> -->
                <!-- <th>Action</th> -->
              </tr>
            </thead>
            <tbody id="tableBodyResult">
            </tbody>
            <tfoot>
              <tr>
                <!-- <th></th> -->
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
    </div>
  </div>
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
    fillTable();
    $("#navbar-collapse").text('');
    $('.select2').select2({
      language : {
        noResults : function(params) {

        }
      }
    });
  });


  function clearConfirmation(){
    location.reload(true);
  }

  function fillTable(tanggal) {

    var data = {
      tanggal:tanggal
    }

    $.get('{{ url("fetch/mirai_mobile/report") }}', data, function(result, status, xhr){
      if(result.status){
        $('#tableResult').DataTable().clear();
        $('#tableResult').DataTable().destroy();
        $('#tableBodyResult').html("");
        var tableData = "";
        var count = 1;

        $.each(result.lists, function(key, value) {

          var d = new Date(value.answer_date);
          var day = d.getDate();
          var months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
          var month = months[d.getMonth()];
          var year = d.getFullYear();      

          tableData += '<tr>';
          tableData += '<td>'+ day +' '+month+' '+year +'</td>';
          tableData += '<td>'+ value.name +'</td>';
          tableData += '<td>'+ value.time_in +'</td>';
          tableData += '<td><a target="_blank" href="https://172.17.128.87/miraidev/public/trial3?lat='+value.lat_in+'&long='+value.lng_in+'" class="btn btn-warning btn-sm"><i class="fa fa-map-marker"></i>&nbsp;&nbsp;Location</a></td>';
          // var data2 = {
          //   lat : value.lat_in,
          //   lng : value.lng_in
          // }
          // $.get('{{ url("fetch/location_employee") }}', data2, function(result, status, xhr){
          //   if(result.status){

          //     console.log(value.data);

          //     $.each(result.data, function(key, value) {
          //       console.log(value.village);
          //     });
          //   }
          //   else{
          //     alert('Attempt to retrieve data failed');
          //   }

          // });
          tableData += '<td>'+ value.time_out +'</td>';
          tableData += '<td><a target="_blank" href="https://172.17.128.87/miraidev/public/trial3?lat='+value.lat_out+'&long='+value.lng_out+'" class="btn btn-warning btn-sm"><i class="fa fa-map-marker"></i>&nbsp;&nbsp;Location</a></td>';
          tableData += '</tr>';
          count += 1;
        });

        $('#tableBodyResult').append(tableData);

        $('#tableResult tfoot th').each( function () {
          var title = $(this).text();
          $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
        } );
        var table = $('#tableResult').DataTable({
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
          'pageLength': 15,
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
      table.columns().every( function () {
        var that = this;

        $( 'input', this.footer() ).on( 'keyup change', function () {
          if ( that.search() !== this.value ) {
            that
            .search( this.value )
            .draw();
          }
        } );
      } );

      $('#tableResult tfoot tr').appendTo('#tableResult thead');

    });

    



      // $('#judul_table').append().empty();
      // $('#judul_table').append('<center>Pengecekan Tanggal <b>'+tanggal+'</b> dengan Judgement <b>'+jdgm+'</b> (<b>'+remark+'</b>)</center>');
      
    }

    function deleteConfirmation(id) {
      jQuery('#modalDeleteButton').attr("href", '{{ url("index/qc_report/delete") }}'+'/'+id);
    }



  </script>

  @stop