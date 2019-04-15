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
                              <td>{{number_format($project->total_investment, 2)}}</td>
                              <td>
                                   <a href="javascript:void(0)" onclick="modalDetail('{{ $project->project }}');" class="btn btn-info btn-xs">Details</a>
                              </td>
                         </tr>
                         @endforeach
                    </tbody>
                    <tfoot>
                    </tfoot>
               </table>
          </div>
     </div>
</section>

<div class="modal fade" id="modalDetail">
     <div class="modal-dialog modal-lg">
          <div class="modal-content">
               <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="modalDetailTitle"></h4>
                    <div class="modal-body table-responsive no-padding">
                         <table class="table table-hover table-bordered table-striped" id="tableDetail">
                              <thead style="background-color: rgba(126,86,134,.7);">
                                   <tr>
                                        <th>Category</th>
                                        <th>Type</th>
                                        <th>Code</th>
                                        <th>Description</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Amount</th>
                                   </tr>
                              </thead>
                              <tbody id="tableDetailBody">
                              </tbody>
                              <tfoot style="background-color: RGB(252, 248, 227);">
                                   <th>Total</th>
                                   <th></th>
                                   <th></th>
                                   <th></th>
                                   <th></th>
                                   <th></th>
                                   <th></th>
                              </tfoot>
                         </table>
                    </div>
               </div>
          </div>
     </div>
</div>

@endsection
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

     function modalDetail(project){
          var data = {
               project:project
          }
          $.get('{{ url("fetch/mis_investment") }}', data, function(result, status, xhr){
               console.log(status);
               console.log(result);
               console.log(xhr);
               if(xhr.status == 200){
                    if(result.status){
                         $('#tableDetail').DataTable().destroy();
                         $('#modalDetailTitle').html('');
                         $('#modalDetailTitle').html(project + ' Project Details');
                         detailData = '';
                         $.each(result.project_details, function(key, value) {
                              detailData += '<tr>';
                              detailData += '<td style="width: 5%;">' + value.category + '</td>';
                              detailData += '<td style="width: 15%;">' + value.type + '</td>';
                              detailData += '<td style="width: 5%;">' + value.item_code + '</td>';
                              detailData += '<td style="width: 35%;">' + value.description + '</td>';
                              detailData += '<td style="width: 10%;">' + value.qty + '</td>';
                              detailData += '<td style="width: 15%;">' + value.price.toLocaleString() + '</td>';
                              detailData += '<td style="width: 15%;">' + (value.price*value.qty).toLocaleString() + '</td>';
                              detailData += '</tr>';
                         });
                         $('#tableDetailBody').html('');
                         $('#tableDetailBody').append(detailData);

                         var table = $('#tableDetail').DataTable({
                              'dom': 'Bfrtip',
                              'responsive': true,
                              'lengthMenu': [
                              [ 10, 25, 50, -1 ],
                              [ '10 rows', '25 rows', '50 rows', 'Show all' ]
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
                              'paging'        : true,
                              'lengthChange'  : true,
                              'searching'     : true,
                              'ordering'      : true,
                              'info'        : true,
                              'order'       : [],
                              'autoWidth'   : true,
                              "sPaginationType": "full_numbers",
                              "bJQueryUI": true,
                              "footerCallback": function (tfoot, data, start, end, display) {
                                   var intVal = function ( i ) {
                                        return typeof i === 'string' ?
                                        i.replace(/[\$,]/g, '')*1 :
                                        typeof i === 'number' ?
                                        i : 0;
                                   };
                                   var api = this.api();
                                   var total_qty = api.column(4).data().reduce(function (a, b) {
                                        return intVal(a)+intVal(b);
                                   }, 0)
                                   $(api.column(4).footer()).html(total_qty.toLocaleString());

                                   var total_price = api.column(5).data().reduce(function (a, b) {
                                        return intVal(a)+intVal(b);
                                   }, 0)
                                   $(api.column(5).footer()).html(total_price.toLocaleString());

                                   var total_amount = api.column(6).data().reduce(function (a, b) {
                                        return intVal(a)+intVal(b);
                                   }, 0)
                                   $(api.column(6).footer()).html(total_amount.toLocaleString());
                              },
                              "bAutoWidth": false
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
});

$('#modalDetail').modal('show');
}
else{
     alert('Attempt to retrieve data failed');
}
}
else{
     alert('Disconnected from server');
}
});
}
</script>
@endsection