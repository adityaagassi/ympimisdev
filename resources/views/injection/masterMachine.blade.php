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
@endsection
@section('header')
<section class="content-header">
  <h1>
    {{ $page }}s
    <span class="text-purple"> ???</span>
  </h1>
  <ol class="breadcrumb">
    <li></li>
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
  <div class="row">
    <div class="col-xs-12">

      <div id="container">
        
      </div>
      
    </div>
    <div class="col-xs-12">
      <div class="box">
        <div class="box-body">
          
        <a onclick="addOP()" class="btn btn-primary btn-sm pull-right" style="color:white">Create {{ $page }}</a>
          <table id="example1" class="table table-bordered table-striped table-hover">
            <thead style="background-color: rgba(126,86,134,.7);">
              <tr>
                <th>Machine</th>
                <th>Part</th>
                <th>Color</th>
                <th>Model</th>
                <th>Action</th>
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
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="modal fade in" id="modalEdit">
  <form id ="importForm" name="importForm" method="post" action="{{ url('fetch/updateMasterMachine') }}">
  <input type="hidden" value="{{csrf_token()}}" name="_token" />
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Edit Machine</h4>
        <br>
        <h4 class="modal-title" id="modalDetailTitle"></h4>
        <div class="row">
          <div class="col-md-12">
            <div class="col-md-10">
              <div class="form-group" id="modalDetailBodyEditHeader">
                
              </div>
            </div>
        
          </div>
        </div>

        <div id="tambah2">
        <input type="text" name="lop2" id="lop2" value="1" hidden="">
        </div>
        
      </div>
      <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-warning">Update</button>
              </div>
    </div>
  </div>
</form>
</div>

<div class="modal fade in" id="modalAdd">
  <form id ="importForm" name="importForm" method="post" action="{{ url('fetch/addMasterMachine') }}">
  <input type="hidden" value="{{csrf_token()}}" name="_token" />
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Add Operator</h4>
        <br>
        <h4 class="modal-title" id="modalDetailTitle"></h4>
        <div class="row">
          <div class="col-md-12">
            <div class="col-md-10">
              <div class="form-group" id="modalDetailBodyEditHeaders">
                <label>Machine<span class="text-red">*</span></label>

                <select class="form-control select2" style="width: 100%;" id="mesin3" name="mesin3" data-placeholder="Choose a Machine ..." required>@foreach($mesin as $mesins)<option value="{{ $mesins }}">{{ $mesins }}</option> @endforeach</select>

                <label>Part<span class="text-red">*</span></label>

                <select class="form-control select2" style="width: 100%;" id="part3" name="part3" data-placeholder="Choose a Part ..." required>@foreach($part as $parts)<option value="{{ $parts }}">{{ $parts }}</option> @endforeach</select>

                <label>Color<span class="text-red">*</span></label>

                <select class="form-control select2" style="width: 100%;" id="color3" name="color3" data-placeholder="Choose a color ..." required>@foreach($color as $colors)<option value="{{ $colors }}">{{ $colors }}</option> @endforeach</select>

                <label>Model<span class="text-red">*</span></label>

                <select class="form-control select2" style="width: 100%;" id="model3" name="model3" data-placeholder="Choose a model ..." required>@foreach($model as $models)<option value="{{ $models }}">{{ $models }}</option> @endforeach</select>

              </div>
            </div>
        
          </div>
        </div>

        <div id="tambah2">
        <input type="text" name="lop2" id="lop2" value="1" hidden="">
        </div>
        
      </div>
      <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-warning">Add</button>
              </div>
    </div>
  </div>
</form>
</div>

<div class="modal fade" id="modalProgress">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <div class="modal-body table-responsive no-padding" style="min-height: 100px">
          <center>
            <i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
          </center>
          <table class="table table-hover table-bordered table-striped" id="tableModal">
            <thead style="background-color: rgba(126,86,134,.7);">
              <tr>
                <th>Part</th>
                <th>Color</th>
                <th>Model</th>               
              </tr>
            </thead>
            <tbody id="modalProgressBody">
            </tbody>
          
          </table>
        </div>
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
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  jQuery(document).ready(function() { 
    $('body').toggleClass("sidebar-collapse");
    percenMesin();
    fillexample1();
    $('.select2').select2({
      dropdownAutoWidth : true,
      width: '100%',
    });
  });
  
  function deleteConfirmation(url, name, id) {
    jQuery('.modal-body').text("Are you sure want to delete '" + name + "'");
    jQuery('#modalDeleteButton').attr("href", url+'/'+id);
  }

function fillexample1(){
  $('#example1 tfoot th').each( function () {
    var title = $(this).text();
    $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
  });
  var table = $('#example1').DataTable({
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
    'searching'     : false,
    'ordering'      : true,
    'info'        : true,
    'order'       : [],
    'autoWidth'   : true,
    "sPaginationType": "full_numbers",
    "bJQueryUI": true,
    "bAutoWidth": false,
    "processing": true,
    "serverSide": true,
    "ajax": {
      "type" : "get",
      "url" : "{{ url("fetch/fillMasterMachine") }}",
    },
    "columns": [    
    { "data": "mesin"},
    { "data": "part"},
    { "data": "color"},
    { "data": "model"},   
    { "data": "edit"}
      ]
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

  $('#example1 tfoot tr').appendTo('#example1 thead');
}

function addOP() {
  $('#modalAdd').modal('show');
}

function editop(id){
    var data = {
      id : id
    }
    $.get('{{ url("fetch/editMasterMachine") }}', data, function(result, status, xhr){
      console.log(status);
      console.log(result);
      console.log(xhr);
      if(xhr.status == 200){
        if(result.status){
          $('#modalDetailBodyEdit').html('');
          $('#modalDetailBodyEditHeader').html('');
          
          $.each(result.id_op, function(key, value) {
            
            $('#modalDetailBodyEditHeader').append('<input type="text" name="id" value="'+ value.id +'" hidden><input type="text" id="mesin2" value="'+ value.mesin +'" hidden><input type="text" id="part2" value="'+ value.part +'" hidden><input type="text" id="color2" value="'+ value.color +'" hidden><input type="text" id="model2" value="'+ value.model +'" hidden><label>Machine<span class="text-red">*</span></label><select class="form-control select2" style="width: 100%;" id="mesin" name="mesin" data-placeholder="Choose a Machine ..." required>@foreach($mesin as $mesin)<option value="{{ $mesin }}">{{ $mesin }}</option> @endforeach</select><label>Part<span class="text-red">*</span></label><select class="form-control select2" style="width: 100%;" id="part" name="part" data-placeholder="Choose a Part ..." required>@foreach($part as $part)<option value="{{ $part }}">{{ $part }}</option> @endforeach</select><label>Color<span class="text-red">*</span></label><select class="form-control select2" style="width: 100%;" id="color" name="color" data-placeholder="Choose a color ..." required>@foreach($color as $color)<option value="{{ $color }}">{{ $color }}</option> @endforeach</select><label>Model<span class="text-red">*</span></label><select class="form-control select2" style="width: 100%;" id="color" name="model" data-placeholder="Choose a model ..." required>@foreach($model as $model)<option value="{{ $model }}">{{ $model }}</option> @endforeach</select></div><div class="form-group">').find('.select2').select2();
           
          });    

          var mesin2 = $('#mesin2').val();
          var part2 = $('#part2').val();
          var color2 = $('#color2').val();
          var model2 = $('#model2').val();
          $("#mesin").val(mesin2).trigger("change");
          $("#part").val(part2).trigger("change");
          $("#color").val(color2).trigger("change");
          $("#model").val(model2).trigger("change");
          
          $('#modalEdit').modal('show');
          
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

function percenMesin() {

  $.get('{{ url("fetch/chartMasterMachine") }}',  function(result, status, xhr) {
      console.log(status);
      console.log(result);
      console.log(xhr);

      var part = [];
      var mesin = [];
      
      
      if(xhr.status == 200){
        if(result.status){

          for (var i = 0; i < result.part.length; i++) {
              mesin.push(result.part[i].mesin);
              part.push(parseInt(result.part[i].working));
          }

            
          
          Highcharts.chart('container', {
          chart: {
              type: 'column'
          },
          title: {
              text: 'Working Machine'
          },
          subtitle: {
              text: ''
          },
          xAxis: {
              categories: mesin,
              crosshair: true
          },
          yAxis: {
            type: 'logarithmic',
              title: {
                  text: ''
              },

          },
          tooltip: {
              headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
              pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                  '<td style="padding:0"><b>{point.y:.1f} </b></td></tr>',
              footerFormat: '</table>',
              shared: true,
              useHTML: true
          },
          plotOptions: {
              column: {
                  pointPadding: 0.2,
                  borderWidth: 0,
                  dataLabels: {
                        enabled: true
                    }
              },

          },
          series: [{
              name: 'Working',
              data: part,
              point: {
                events: {
                  click: function () {
                    workingPart(this.category );
                  }
                }
              

    }

          }]
      });

          
        }
      }
    })
}

function workingPart(mesin) {

    $('#modalProgress').modal('show');

    var data = {
      mesin:mesin,
    }
    $.get('{{ url("get/workingPartMesin") }}', data, function(result, status, xhr){
      if(result.status){
        $('#modalProgressBody').html('');
        var resultData = '';
        
        $.each(result.part, function(key, value) {  
          resultData += '<tr>';          
          resultData += '<td style="width: 40%">'+ value.part +'</td>';
          resultData += '<td style="width: 40%">'+ value.color +'</td>';
          resultData += '<td style="width: 20%">'+ value.model +'</td>';          
          resultData += '</tr>';     
        });
        
        $('#modalProgressBody').append(resultData);
          
        
      }
      else{
        alert('Attempt to retrieve data failed');
      }
    });
  
}
</script>

@stop