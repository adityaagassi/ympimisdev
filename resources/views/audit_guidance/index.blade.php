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
		Schedule {{ $activity_name }} <span class="text-purple">{{ $leader }}</span>
		{{-- <small> <span class="text-purple">??</span></small> --}}
	</h1>
	<ol class="breadcrumb">
		{{-- <li>
			<button href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#reprintModal">
				<i class="fa fa-print"></i>&nbsp;&nbsp;Reprint FLO
			</button>
		</li> --}}
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
			<div class="box box-primary">
				<div class="box-body">
					<div class="col-xs-12">
						<div class="box-header">
							<h3 class="box-title">Filter Schedule <span class="text-purple">{{ $activity_name }}</span></h3>
						</div>
						<form role="form" method="post" action="{{url('index/audit_guidance/filter_guidance/'.$id)}}">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="col-md-12 col-md-offset-4">
							<div class="col-md-3">
								<div class="form-group">
									<label>Month</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="date" name="month" autocomplete="off" placeholder="Choose a Month">
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-md-offset-4">
							<div class="col-md-3">
								<div class="form-group pull-right">
									<a href="{{ url('index/activity_list/filter/'.$id_departments.'/4') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/audit_guidance/index/'.$id) }}" class="btn btn-danger">Clear</a>
									<button type="submit" class="btn btn-primary col-sm-14">Search</button>
								</div>
							</div>
						</div>
						</form>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<div class="form-group pull-right">
							{{-- <a href="{{ url('index/audit_guidance/create/'.$id) }}" class="btn btn-primary">Create Schedule {{ $activity_alias }}</a> --}}
							<button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#create-modal">
								Create Schedule {{ $activity_alias }}
							</button>
						</div>
					</div>
				  <div class="row">
				    <div class="col-xs-12">
				      <div class="box">
				        <div class="box-body">
				          <table id="example1" class="table table-bordered table-striped table-hover">
				            <thead style="background-color: rgba(126,86,134,.7);">
				              <tr>
				                <th>Nama Dokumen</th>
				                <th>Nomor Dokumen</th>
				                <th>Bulan</th>
				                <th>Periode</th>
				                <th>Status</th>
				                <th>Action</th>
				              </tr>
				            </thead>
				            <tbody>
				              @if(count($audit_guidance) != 0)
				              @foreach($audit_guidance as $audit_guidance)
				              <tr>
				                <td>{{$audit_guidance->nama_dokumen}}</td>
				                <td>{{$audit_guidance->no_dokumen}}</td>
				                <td>{{$monthTitle = date("F Y", strtotime($audit_guidance->month))}}</td>
				                <td>{{$audit_guidance->periode}}</td>
				                <td>@if($audit_guidance->status == "Belum Dikerjakan")
				                		<label class="label label-danger">Belum Dikerjakan</label>
				                	@else
				                		<label class="label label-success">Sudah Dikerjakan</label>
				                	@endif
				        		</td>
				                <td>
				                  <center>
				                    <a class="btn btn-info btn-sm" href="{{url('index/audit_guidance/show/'.$id.'/'.$audit_guidance->id)}}">View</a>
				                    {{-- <a href="{{url('index/audit_guidance/edit/'.$id.'/'.$audit_guidance->id)}}" class="btn btn-warning btn-sm">Edit</a> --}}
									<button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit-modal" onclick="edit('{{ url("index/audit_guidance/update") }}','{{ $id }}','{{ $audit_guidance->id }}');">
						               Edit
						            </button>
				                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("index/audit_guidance/destroy") }}', '{{ $audit_guidance->nama_dokumen }} - {{ $audit_guidance->no_dokumen }} - {{ $audit_guidance->month }}','{{ $id }}', '{{ $audit_guidance->id }}');">
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
        <h4 class="modal-title" align="center"><b>Create Schecule Audit</b></h4>
      </div>
      <div class="modal-body">
      	<div class="box-body">
        <form role="form" method="post" action="{{url('index/audit_guidance/store/'.$id)}}" enctype="multipart/form-data">
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            	<input type="hidden" class="form-control" name="inputactivity_list_id" id="inputactivity_list_id" placeholder="Enter Leader" value="{{ $id }}" readonly>
	            <div class="form-group">
	              <label for="">Nama Dokumen</label>
				  <input type="text" class="form-control" name="inputnama_dokumen" id="inputnama_dokumen" placeholder="Enter Nama Dokumen" required>
	            </div>
	            <div class="form-group">
	              <label for="">No. Dokumen</label>
				  <input type="text" class="form-control" name="inputno_dokumen" id="inputno_dokumen" placeholder="Enter No. Dokumen" required>
	            </div>
	            <div class="form-group">
	              <label for="">Month</label>
	              <div class="input-group date">
					  <div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					  </div>
					  <input type="text" class="form-control pull-right" id="inputmonth" name="inputmonth" autocomplete="off" placeholder="Choose a Month" required>
				  </div>
	            </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	            <div class="form-group">
	              <label for="">Periode</label>
	              <input type="text" class="form-control" name="inputperiode" id="inputperiode" placeholder="Enter No. Dokumen" value="{{ $fy }}" readonly>
	            </div>
	            <div class="form-group">
	              <label for="">Leader</label>
				  <input type="text" class="form-control" name="inputleader" id="inputleader" placeholder="Enter Leader" value="{{ $leader }}" readonly>
	            </div>
	            <div class="form-group">
	              <label for="">Foreman</label>
				  <input type="text" class="form-control" name="inputforeman" id="inputforeman" placeholder="Enter Leader" value="{{ $foreman }}" readonly>
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
        <h4 class="modal-title" align="center"><b>Update Schecule Audit</b></h4>
      </div>
      <div class="modal-body">
      	<div class="box-body">
        <form role="form" id="formedit" method="post" action="" enctype="multipart/form-data">
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            	<input type="hidden" class="form-control" name="inputactivity_list_id" id="inputactivity_list_id" placeholder="Enter Leader" value="{{ $id }}" readonly>
	            <div class="form-group">
	              <label for="">Nama Dokumen</label>
				  <input type="text" class="form-control" name="editnama_dokumen" id="editnama_dokumen" placeholder="Enter Nama Dokumen" required>
	            </div>
	            <div class="form-group">
	              <label for="">No. Dokumen</label>
				  <input type="text" class="form-control" name="editno_dokumen" id="editno_dokumen" placeholder="Enter No. Dokumen" required>
	            </div>
	            <div class="form-group">
	              <label for="">Month</label>
	              <div class="input-group date">
					  <div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					  </div>
					  <input type="text" class="form-control pull-right" id="editmonth" name="editmonth" autocomplete="off" placeholder="Choose a Month" required>
				  </div>
	            </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	            <div class="form-group">
	              <label for="">Periode</label>
	              <input type="text" class="form-control" name="editperiode" id="editperiode" placeholder="Enter No. Dokumen" value="{{ $fy }}" readonly>
	            </div>
	            <div class="form-group">
	              <label for="">Leader</label>
				  <input type="text" class="form-control" name="editleader" id="editleader" placeholder="Enter Leader" value="{{ $leader }}" readonly>
	            </div>
	            <div class="form-group">
	              <label for="">Foreman</label>
				  <input type="text" class="form-control" name="editforeman" id="editforeman" placeholder="Enter Leader" value="{{ $foreman }}" readonly>
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
		
		$('.select2').select2({
			language : {
				noResults : function(params) {
					return "There is no date";
				}
			}
		});
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
    function deleteConfirmation(url, name,id,audit_guidance_id) {
      console.log(url);
      jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
      jQuery('#modalDeleteButton').attr("href", url+'/'+id+'/'+audit_guidance_id);
    }

    function edit(url, id,audit_guidance_id) {
    	$.ajax({
                url: "{{ route('audit_guidance.getdetail') }}?id=" + audit_guidance_id,
                method: 'GET',
                success: function(data) {
                  var json = data;
                  var data = data.data;
                  // console.log(data);
                  $("#editnama_dokumen").val(data.nama_dokumen);
                  $("#editno_dokumen").val(data.no_dokumen);
                  $("#editmonth").val(data.month);
                }
            });
      jQuery('#formedit').attr("action", url+'/'+id+'/'+audit_guidance_id);
      console.log($('#formedit').attr("action"));
    }
  </script>
@endsection