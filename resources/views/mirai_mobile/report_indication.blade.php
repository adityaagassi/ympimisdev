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
    <small>Employee Indication</small>
  </h1>
  <ol class="breadcrumb">
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

  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
    <p style="position: absolute; color: White; top: 45%; left: 45%;">
      <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
    </p>
  </div>

  <div class="row">
    <div class="col-xs-12">
      <div class="box">

        <div class="box-body" style="overflow-x: scroll;">
          <div class="col-xs-2">
            <div class="row">
              <div class="input-group date" style="padding-bottom: 10px;">
                <div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control datepicker" id="tanggal" name="tanggal" placeholder="Select Date">
              </div>
            </div>
          </div>
          <div class="col-xs-2">
            <button class="btn btn-success" onclick="fillTable()">Search</button>
          </div>
          <table id="tableResult" class="table table-bordered table-striped table-hover" >
            <thead style="background-color: rgba(126,86,134,.7);">
              <tr>
                <th width="5%">Tanggal</th>
                <th width="5%">NIK</th>
                <th width="10%">Nama</th>
                <th width="10%">Departemen</th>
                @foreach($question as $qs)
                <th width="8%">{{ $qs->question }}</th>
                @endforeach
                <!-- <th width="10%">Gejala Sakit</th> -->
              </tr>
            </thead>
            <tbody id="tableBodyResult">
            </tbody>
            <tfoot>
              <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                @foreach($question as $qs)
                <th></th>
                @endforeach
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
     $('#tanggal').datepicker({
      autoclose: true,
      todayHighlight: true
    });

    $('body').toggleClass("sidebar-collapse");
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

  function fillTable() {
    $('#loading').show();
    var tanggal = $('#tanggal').val();
    var data = {
      tanggal:tanggal
    }

    $.get('{{ url("fetch/mirai_mobile/report_indication") }}', data, function(result, status, xhr){
      if(result.status){
        $('#tableResult').DataTable().clear();
        $('#tableResult').DataTable().destroy();
        $('#tableBodyResult').html("");
        var tableData = "";
        var count = 1;

        $.each(result.lists, function(key, value) {

          var d = new Date(value.date);
          var day = d.getDate();
          var months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
          var month = months[d.getMonth()];
          var year = d.getFullYear();      

          tableData += '<tr>';     
          tableData += '<td>'+ day +' '+month+' '+year +'</td>';
          tableData += '<td>'+ value.employee_id +'</td>';     
          tableData += '<td>'+ value.name +'</td>';
          tableData += '<td>'+ value.department +'</td>';
          if (value.Demam > 0) {
            tableData += '<td style="background-color:RGB(255,204,255)">Iya</td>';    
          }
          else{
            tableData += '<td>Tidak</td>';
          }

          if (value.Batuk > 0) {
            tableData += '<td style="background-color:RGB(255,204,255)">Iya</td>';    
          }
          else{
            tableData += '<td>Tidak</td>';
          }

          if (value.Pusing > 0) {
            tableData += '<td style="background-color:RGB(255,204,255)">Iya</td>';    
          }
          else{
            tableData += '<td>Tidak</td>';
          }

          if (value.Tenggorokan > 0) {
            tableData += '<td style="background-color:RGB(255,204,255)">Iya</td>';    
          }
          else{
            tableData += '<td>Tidak</td>';
          }

          if (value.Sesak > 0) {
            tableData += '<td style="background-color:RGB(255,204,255)">Iya</td>';    
          }
          else{
            tableData += '<td>Tidak</td>';
          }

          if (value.Indera > 0) {
            tableData += '<td style="background-color:RGB(255,204,255)">Iya</td>';    
          }
          else{
            tableData += '<td>Tidak</td>';
          }

          if(value.Suhu != null) {
            if (value.Suhu == 0) {
              tableData += '<td>-</td>';
            }
            else{
              tableData += '<td>'+value.Suhu+'</td>';    
            }
          }
          else {
            tableData += '<td>-</td>';
          }

          if (value.Kontak > 0) {
            tableData += '<td style="background-color:RGB(255,204,255)">Iya</td>';    
          }
          else{
            tableData += '<td>Tidak</td>';
          }


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
            {
              extend: 'copy',
              className: 'btn btn-success',
              text: '<i class="fa fa-copy"></i> Copy',
              exportOptions: {
                columns: ':not(.notexport)'
              }
            },
            {
              extend: 'excel',
              className: 'btn btn-info',
              text: '<i class="fa fa-file-excel-o"></i> Excel',
              exportOptions: {
                columns: ':not(.notexport)'
              }
            },
            {
              extend: 'print',
              className: 'btn btn-warning',
              text: '<i class="fa fa-print"></i> Print',
              exportOptions: {
                columns: ':not(.notexport)'
              }
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
        $('#loading').hide();
      }
      else{
        $('#loading').hide();
        alert('Attempt to retrieve data failed');
      }

    });


      // $('#judul_table').append().empty();
      // $('#judul_table').append('<center>Pengecekan Tanggal <b>'+tanggal+'</b> dengan Judgement <b>'+jdgm+'</b> (<b>'+remark+'</b>)</center>');
      
    }


  </script>

  @stop