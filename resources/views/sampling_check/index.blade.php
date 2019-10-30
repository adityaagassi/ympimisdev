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
		{{ $activity_name }} <span class="text-purple">{{ $departments }}</span>
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
				<div class="box-header">
					<h3 class="box-title">Filter <span class="text-purple">{{ $activity_name }}</span></h3>
				</div>
				<div class="box-body">
					<form role="form" method="post" action="{{url('index/sampling_check/filter_sampling/'.$id)}}">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="col-md-12 col-md-offset-4">
						<div class="col-md-3">
							<div class="form-group">
								<label>Sub Section</label>
								<select class="form-control select2" name="subsection" style="width: 100%;" data-placeholder="Choose a Sub Section...">
						            <option value=""></option>
						            @foreach($subsection as $subsection)
						                <option value="{{ $subsection->sub_section_name }}">{{ $subsection->sub_section_name }}</option>
						              @endforeach
						        </select>
							</div>
						</div>
					</div>
					<div class="col-md-12 col-md-offset-4">
						<div class="col-md-3">
							<div class="form-group">
								<label>Month</label>
								<select class="form-control select2" name="month" style="width: 100%;" data-placeholder="Choose a Month...">
						            <option value=""></option>
						            <option value="01">January</option>
						            <option value="02">February</option>
						            <option value="03">March</option>
						            <option value="04">April</option>
						            <option value="05">May</option>
						            <option value="06">June</option>
						            <option value="07">July</option>
						            <option value="08">August</option>
						            <option value="09">September</option>
						            <option value="10">October</option>
						            <option value="11">November</option>
						            <option value="12">Desember</option>
						        </select>
							</div>
						</div>
					</div>
					<div class="col-md-12 col-md-offset-4">
						<div class="col-md-3">
							<div class="form-group pull-right">
								<a href="{{ url('index/activity_list/filter/'.$id_departments.'/3') }}" class="btn btn-warning">Back</a>
								<a href="{{ url('index/sampling_check/index/'.$id) }}" class="btn btn-danger">Clear</a>
								<button type="submit" class="btn btn-primary col-sm-14">Search</button>
							</div>
						</div>
					</div>
					<div class="col-md-12 col-md-offset-9">
						<div class="col-md-3">
							<div class="form-group pull-right">
								<a href="{{ url('index/sampling_check/create/'.$id) }}" class="btn btn-primary">Create {{ $activity_alias }}</a>
							</div>
						</div>
					</div>
					</form>
				  <div class="row">
				    <div class="col-xs-12">
				      <div class="box">
				        <div class="box-body">
				          <table id="example1" class="table table-bordered table-striped table-hover">
				            <thead style="background-color: rgba(126,86,134,.7);">
				              <tr>
				                <th>Department</th>
				                <th>Section</th>
				                <th>Sub Section</th>
				                <th>Month</th>
				                <th>Date</th>
				                <th>Product</th>
				                <th>No. Seri / Part</th>
				                <th>Jumlah Cek</th>
				                <th>Leader</th>
				                <th>Foreman</th>
				                <th>Details</th>
				                <th>Action</th>
				              </tr>
				            </thead>
				            <tbody>
				              @foreach($sampling_check as $sampling_check)
				              <tr>
				                <td>{{$sampling_check->department}}</td>
				                <td>{{$sampling_check->section}}</td>
				                <td>{{$sampling_check->subsection}}</td>
				                <td>{{$sampling_check->month}}</td>
				                <td>{{$sampling_check->date}}</td>
				                <td>{{$sampling_check->product}}</td>
				                <td>{{$sampling_check->no_seri_part}}</td>
				                <td>{{$sampling_check->jumlah_cek}}</td>
				                <td>{{$sampling_check->leader}}</td>
				                <td>{{$sampling_check->foreman}}</td>
				                <td>
				                  <center>
				                    <a class="btn btn-primary btn-xs" href="{{url('index/sampling_check/details/'.$sampling_check->id)}}">Details</a>
				                  </center>
				                </td>
				                <td>
				                  <center>
				                    <a class="btn btn-info btn-xs" href="{{url('index/sampling_check/show/'.$id.'/'.$sampling_check->id)}}">View</a>
				                    <a href="{{url('index/sampling_check/edit/'.$id.'/'.$sampling_check->id)}}" class="btn btn-warning btn-xs">Edit</a>
				                    <a href="javascript:void(0)" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("index/sampling_check/destroy") }}', '{{ $sampling_check->activity_lists->activity_name }} - {{ $sampling_check->product }} - {{ $sampling_check->date }}','{{ $id }}', '{{ $sampling_check->id }}');">
				                      Delete
				                    </a>
				                  </center>
				                </td>
				              </tr>
				              @endforeach
				            </tbody>
				            <tfoot>
				              <tr>
				                <th></th>
				                <th></th>
				                <th></th>
				                <th></th>
				                <th></th>
				                <th></th>
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
			format: 'yyyy-mm-dd',
			todayHighlight: true
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
    function deleteConfirmation(url, name, sampling_check_id,id) {
      jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
      jQuery('#modalDeleteButton').attr("href", url+'/'+sampling_check_id+'/'+id);
    }
  </script>
@endsection