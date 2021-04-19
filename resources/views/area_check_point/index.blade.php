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
		Point Cek Area - {{ $leader }}
    <a href="{{ url('index/production_report/index/'.$id_departments) }}" class="btn btn-warning pull-right">Kembali</a>&nbsp;
    <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#create-modal" style="margin-right: 5px">
      Tambahkan Point Check
    </button>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
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
			<div class="box box-solid">
				<div class="box-body">
				  <div class="row">
				    <div class="col-xs-12">
		          <table id="example1" class="table table-bordered table-striped table-hover">
		            <thead style="background-color: rgba(126,86,134,.7);">
		              <tr>
		                <th>Point Check</th>
                    <th>Location</th>
		                <th>Action</th>
		              </tr>
		            </thead>
		            <tbody>
		              @if(count($area_check_point) != 0)
		              @foreach($area_check_point as $area_check_point)
		              <tr>
		                <td>{{$area_check_point->point_check}}</td>
                    <td>{{$area_check_point->location}}</td>
		                <td>
		                  <center>
							     <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit-modal" onclick="edit('{{ url("index/area_check_point/update") }}','{{ $id }}','{{ $area_check_point->id }}');">
				               Edit
				            </button>
		                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("index/area_check_point/destroy") }}', '{{ $area_check_point->point_check }}','{{ $id }}', '{{ $area_check_point->id }}');">
		                      Delete
		                    </a>
		                  </center>
		                </td>
		              </tr>
		              @endforeach
		              @endif
		            </tbody>
		            <tfoot>
		              <tr>
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
          Are you sure delete?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
        </div>
      </div>
    </div>
  </div>

 <div class="modal fade" id="create-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" align="center"><b>Tambahkan Point Check</b></h4>
      </div>
      <div class="modal-body">
      	<div class="box-body">
        <form role="form" method="post" action="{{url('index/area_check_point/store/'.$id)}}" enctype="multipart/form-data">
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	            <div class="form-group">
	              <label for="">Point Check</label>
				        <input type="text" class="form-control" name="inputpoint_check" id="inputpoint_check" placeholder="Masukkan Point Check" required>
	            </div>
              <div class="form-group">
                <label for="">Lokasi</label>
                <select class="form-control select2" name="inputlocation" id="inputlocation" style="width: 100%;" data-placeholder="Pilih Lokasi" required>
                  <option value=""></option>
                  @foreach($area_code as $area_code)
                    <option value="{{ $area_code->area }}">{{ $area_code->area }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	            <div class="form-group">
	              <label for="">Leader</label>
				        <input type="text" class="form-control" name="inputleader" id="inputleader" placeholder="Masukkan Leader" value="{{ $leader }}" readonly>
	            </div>
	            <div class="form-group">
	              <label for="">Foreman</label>
				        <input type="text" class="form-control" name="inputforeman" id="inputforeman" placeholder="Masukkan Leader" value="{{ $foreman }}" readonly>
	            </div>
            </div>
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          	<div class="modal-footer">
            <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            <input type="submit" value="Submit" class="btn btn-primary">
          </div>
          </div>
        </form>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="edit-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" align="center"><b>Update Point Check</b></h4>
      </div>
      <div class="modal-body">
      	<div class="box-body">
        <form role="form" id="formedit" method="post" action="" enctype="multipart/form-data">
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	            <div class="form-group">
	              <label for="">Point Check</label>
				  <input type="text" class="form-control" name="editpoint_check" id="editpoint_check" placeholder="Masukkan Point Check" required>
	            </div>
              <div class="form-group">
                <label for="">Lokasi</label>
                <select class="form-control select2" name="editlocation" id="editlocation" style="width: 100%;" data-placeholder="Pilih Lokasi ..." required>
                  <option value=""></option>
                  @foreach($area_code2 as $area_code2)
                    <option value="{{ $area_code2->area }}">{{ $area_code2->area }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	            <div class="form-group">
	              <label for="">Leader</label>
				  <input type="text" class="form-control" name="editleader" id="editleader" placeholder="Masukkan Leader" value="{{ $leader }}" readonly>
	            </div>
	            <div class="form-group">
	              <label for="">Foreman</label>
				  <input type="text" class="form-control" name="editforeman" id="editforeman" placeholder="Masukkan Leader" value="{{ $foreman }}" readonly>
	            </div>
            </div>
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          	<div class="modal-footer">
            <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            <input type="submit" value="Update" class="btn btn-primary">
          </div>
          </div>
        </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection


@section('scripts')
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('#date').datepicker({
			autoclose: true,
			format: 'yyyy-mm',
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});

		$('#inputmonth').datepicker({
			autoclose: true,
			format: 'yyyy-mm',
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});

		$('#editmonth').datepicker({
			autoclose: true,
			format: 'yyyy-mm',
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});
		
		$('.select2').select2();
	});

	
</script>
  <script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
  <script src="{{ url("js/buttons.flash.min.js")}}"></script>
  <script src="{{ url("js/jszip.min.js")}}"></script>
  <script src="{{ url("js/vfs_fonts.js")}}"></script>
  <script src="{{ url("js/buttons.html5.min.js")}}"></script>
  <script src="{{ url("js/buttons.print.min.js")}}"></script>
  <script>
    jQuery(document).ready(function() {
      $('#example1 tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
      } );
      var table = $('#example1').DataTable({
        "order": [],
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
        }
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

      $('#example1 tfoot tr').appendTo('#example1 thead');

    });
    $(function () {

      $('#example2').DataTable({
        'paging'      : true,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false
      })
    })
    function deleteConfirmation(url, name,id,area_check_point_id) {
      console.log(url);
      jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
      jQuery('#modalDeleteButton').attr("href", url+'/'+id+'/'+area_check_point_id);
    }

    function edit(url, id,area_check_point_id) {
    	$.ajax({
                url: "{{ route('area_check_point.getdetail') }}?id=" + area_check_point_id,
                method: 'GET',
                success: function(data) {
                  var json = data;
                  var data = data.data;
                  // console.log(data);
                  $("#editpoint_check").val(data.point_check);
                  $("#editlocation").val(data.location).trigger('change');
                }
            });
      jQuery('#formedit').attr("action", url+'/'+id+'/'+area_check_point_id);
      console.log($('#formedit').attr("action"));
    }
  </script>
@endsection