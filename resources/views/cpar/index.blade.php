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
td {
  overflow:hidden;
  text-overflow: ellipsis;
}
#tableBodyResult > tr:hover {
  cursor: pointer;
  background-color: #7dfa8c;
}
#loading, #error { display: none; }
</style>
@endsection
@section('header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content-header">
  <h1>
    List {{ $page }}
    <!-- <small>Validate Your CPAR</small> -->
  </h1>
  <ol class="breadcrumb">
      <li><a href="{{ url("index/form_ketidaksesuaian/create")}}" class="btn btn-md bg-purple" style="color:white"><i class="fa fa-plus"></i> Buat {{ $page }}</a></li>
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
        <div class="box-header">
          <h3 class="box-title">Filter <span class="text-purple">Laporan Ketidaksesuaian</span></h3>
        </div>
        <div class="box-body">
          <input type="hidden" value="{{csrf_token()}}" name="_token" />
          <div class="col-md-12" style="padding-left: 0">
            <div class="col-md-2" style="padding: 0">
              <div class="form-group">
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="tgl" name="tgl" placeholder="Tanggal">
                </div>
              </div>
            </div>
            <div class="col-md-3">
                <a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
                <button id="search" onClick="fillTable($('#tgl').val(),$('#section_from').val(),$('#section_to').val())" class="btn btn-primary">Search</button>
            </div>
          </div>
          <div class="col-md-12 col-md-offset-5">
            <div class="form-group">
              
            </div>
          </div>
          <!-- <div class="box-body" style="overflow-x: scroll;"> -->
            <table id="tableResult" class="table table-bordered table-striped table-hover">
              <thead style="background-color: rgba(126,86,134,.7);">
                <tr>
                  <th>Kategori</th>
                  <th>Judul</th> 
                  <th>Tanggal</th>    
                  <th>Section From</th>
                  <th>Section To</th>
                  <th>Action</th>
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
                  <th></th>
                  <th></th>
                </tr>
              </tfoot>
            </table>            
          <!-- </div> -->
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
        Are you sure want to delete this?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
      </div>
    </div>
  </div>
</div>

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
    $('#tgl').datepicker({
        <?php $tgl_max = date('Y-m-d') ?>
        format: "yyyy-mm-dd",
        autoclose: true,
        todayHighlight: true,
        endDate: '<?php echo $tgl_max ?>'
      });
      $('.select2').select2({
        language : {
          noResults : function(params) {
            return "There is no list";
          }
        }
      });
    });


  function clearConfirmation(){
    location.reload(true);
  }

  function fillTable(tanggal,section_from,sec_to) {

      var data = {
        tanggal:tanggal,
        section_from:section_from,
        sec_to:sec_to
    }
      $.get('{{ url("index/form_ketidaksesuaian/fetchDataTable") }}', data, function(result, status, xhr){
      if(result.status){
        $('#tableResult').DataTable().clear();
        $('#tableResult').DataTable().destroy();
        $('#tableBodyResult').html("");
        var tableData = "";
        var count = 1;
        
        $.each(result.lists, function(key, value) {
          var d = new Date(value.tanggal);
          var day = d.getDate();
          var months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
          var month = months[d.getMonth()];
          var year = d.getFullYear();

          var sec_from = value.section_from;
          var sec_f = sec_from.split("_");

          var sec_to = value.section_to;
          var sec_t = sec_to.split("_");

          tableData += '<tr>';
          tableData += '<td>'+ value.kategori +'</td>';
          tableData += '<td>'+ value.judul +'</td>';
          tableData += '<td>'+ day +' '+month+' '+year +'</td>';
          tableData += '<td>'+ sec_f[0] + ' - '+ sec_f[1] +'</td>';
          tableData += '<td>'+ sec_t[0] + ' - '+ sec_t[1] +'</td>';
        
          if("{{Auth::user()->username}}" == value.pelapor || "{{Auth::user()->role_code}}" == "MIS"){
            tableData += '<td><a href="{{ url("index/form_ketidaksesuaian/print") }}/'+value.id+'" class="btn btn-success btn-xs"><i class="fa fa-file-pdf-o"></i> Report PDF</a> <a href="{{ url("index/form_ketidaksesuaian/detail") }}/'+value.id+'" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> Detail</a></td>';            
          }
          else{
            tableData += '<td><a href="{{ url("index/form_ketidaksesuaian/print") }}/'+value.id+'" class="btn btn-success btn-xs"><i class="fa fa-file-pdf-o"></i> Report PDF</a></td>';
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

  // function fillCPARDetail(){
  //   $('#example1').DataTable().destroy();
  //   var bulandari = $('#bulandari').val();
  //   var bulanke = $('#bulanke').val();
  //   var kategori = $('#kategori').val();
  //   var department_id = $('#department_id').val();
  //   var status_code = $('#status_code').val();
  //   var data = {
  //     bulandari:bulandari,
  //     bulanke:bulanke,
  //     department_id:department_id,
  //     status_code:status_code,
  //     kategori:kategori
  //   }
  //   $('#example1 tfoot th').each( function () {
  //     var title = $(this).text();
  //     $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
  //   } );
  //   var table = $('#example1').DataTable({
  //     'dom': 'Bfrtip',
  //     'responsive': true,
  //     'lengthMenu': [
  //       [ 10, 25, 50, -1 ],
  //       [ '10 rows', '25 rows', '50 rows', 'Show all' ]
  //     ],
  //     'buttons': {
  //       buttons:[
  //       {
  //         extend: 'pageLength',
  //         className: 'btn btn-default',
  //       },
  //       {
  //         extend: 'copy',
  //         className: 'btn btn-success',
  //         text: '<i class="fa fa-copy"></i> Copy',
  //         exportOptions: {
  //           columns: ':not(.notexport)'
  //         }
  //       },
  //       {
  //         extend: 'excel',
  //         className: 'btn btn-info',
  //         text: '<i class="fa fa-file-excel-o"></i> Excel',
  //         exportOptions: {
  //           columns: ':not(.notexport)'
  //         }
  //       },
  //       {
  //         extend: 'print',
  //         className: 'btn btn-warning',
  //         text: '<i class="fa fa-print"></i> Print',
  //         exportOptions: {
  //           columns: ':not(.notexport)'
  //         }
  //       },
  //       ]
  //     },
  //     'paging': true,
  //     'lengthChange': true,
  //     'searching': true,
  //     'ordering': true,
  //     'order': [],
  //     'info': true,
  //     'autoWidth': true,
  //     "sPaginationType": "full_numbers",
  //     "bJQueryUI": true,
  //     "bAutoWidth": false,
  //     "processing": true,
  //       // "serverSide": true,
  //       "ajax": {
  //         "type" : "post",
  //         "url" : "{{ url("index/qc_report/filter_cpar") }}",
  //         "data" : data,
  //       },
  //       "columns": [
  //         { "data": "cpar_no" },
  //         { "data": "kategori" },
  //         { "data": "name" },
  //         { "data": "lokasi" },
  //         { "data": "tgl_permintaan" },
  //         { "data": "tgl_balas" },
  //         { "data": "judul_komplain" },
  //         { "data": "department_name" , "width": "15%"},
  //         { "data": "sumber_komplain" },
  //         { "data": "status_name" },
  //         { "data": "action", "width": "10%"}
  //       ]
  //     });

    

  //   table.columns().every( function () {
  //       var that = this;

  //       $( 'input', this.footer() ).on( 'keyup change', function () {
  //         if ( that.search() !== this.value ) {
  //           that
  //           .search( this.value )
  //           .draw();
  //         }
  //       } );
  //     } );

  //     $('#example1 tfoot tr').appendTo('#example1 thead');
  // }

   function deleteConfirmation(id) {
    jQuery('#modalDeleteButton').attr("href", '{{ url("index/qc_report/delete") }}'+'/'+id);
  }

  

</script>

@stop