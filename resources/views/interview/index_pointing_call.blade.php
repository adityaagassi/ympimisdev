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
		<?php if ($status == 'leader') { ?>
			{{ $activity_name }} - {{ $leader }}
		<?php }else{?>
			{{ $activity_name }} - {{ $departments }}
		<?php } ?>
		<!-- <button href="{{ url('index/interview/create/'.$id.'/'.$status) }}" class="btn btn-primary pull-right">Buat Interview</button> -->
		<button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#create-modal">
	        Buat Interview
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
	@if (session('error'))
		<div class="alert alert-warning alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4> Warning!</h4>
			{{ session('error') }}
		</div>   
	@endif
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<?php if ($status == 'leader'): ?>
						<div class="col-md-6 col-md-offset-3">
							<div class="box-header">
								<h3 class="box-title">Filter</h3>
							</div>
							<form role="form" method="post" action="{{url('index/interview/filter_interview/'.$id)}}">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="col-md-12">
									<div class="col-md-6">
										<div class="form-group">
											<label>Group</label>
											<select class="form-control select2" name="subsection" style="width: 100%;" data-placeholder="Pilih Group...">
												<option value=""></option>
												@foreach($subsection as $subsection)
												<option value="{{ $subsection->sub_section_name }}">{{ $subsection->sub_section_name }}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Date</label>
											<div class="input-group date">
												<div class="input-group-addon bg-white">
													<i class="fa fa-calendar"></i>
												</div>
												<input type="text" class="form-control datepicker" id="tgl"name="month" placeholder="Select Date" autocomplete="off">
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-12 col-md-offset-2">
									<div class="col-md-6">
										<div class="form-group pull-right">
											<a href="{{ url('index/production_report/index/'.$id_departments) }}" class="btn btn-warning">Back</a>
											<a href="{{ url('index/interview/index/'.$id) }}" class="btn btn-danger">Clear</a>
											<button type="submit" class="btn btn-primary col-sm-14">Search</button>
										</div>
									</div>
								</div>
							</form>
						</div>
					<?php endif ?>
					<div class="row">
						<div class="col-xs-12" style="overflow-x: scroll;">
							<table id="example1" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>Section</th>
										<th>Date</th>
										<?php if ($status == 'leader'){ ?>
											<th>Leader</th>
											<th>Foreman</th>
										<?php }else{ ?>
											<th>Chief / Staff</th>
											<th>Manager</th>
										<?php }?>
										<th>Send Status</th>
										<th>Approval Status</th>
										<th>Details</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									@foreach($interview as $interview)
									<tr>
										<td>{{$interview->section}}</td>
										<td>{{$interview->date}}</td>
										<td>{{$interview->leader}}</td>
										<td>{{$interview->foreman}}</td>
										<td>
											@if($interview->send_status == "")
						                		<label class="label label-danger">Belum Dikirim</label>
						                	@else
						                		<label class="label label-success">Terkirim</label>
						                	@endif
										</td>
										<td>@if($interview->approval == "")
						                		<label class="label label-danger">Not Approved</label>
						                	@else
						                		<label class="label label-success">Approved</label>
						                	@endif</td>
										<td>
											<center>
												<a class="btn btn-primary btn-sm" href="{{url('index/interview/pointing_call/details/'.$interview->id)}}"><b>Peserta Interview</b></a>
												<a target="_blank" class="btn btn-success btn-sm" href="{{url('index/interview/print_interview/'.$interview->id)}}"><b>Cetak</b></a>
												@if($interview->send_status == "")
							                		<a class="btn btn-info btn-sm" href="{{url('index/interview/sendemail/'.$interview->id.'/chief')}}"><b>Kirim Email</b></a>
							                	@endif
											</center>
										</td>
										<td>
											<center>
												<a href="{{url('index/interview/pointing_call/edit/'.$id.'/'.$interview->id)}}" class="btn btn-warning"><b>Edit</b></a>
												<a href="javascript:void(0)" class="btn btn-danger" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("index/interview/destroy") }}', '{{ $interview->activity_lists->activity_name }} - {{ $interview->date }}','{{ $id }}', '{{ $interview->id }}','{{ $status }}');">
													<b>Delete</b>
												</a>
											</center>
										</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
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
        <h4 class="modal-title" align="center"><b>Buat Interview</b></h4>
      </div>
      <div class="modal-body">
      	<div class="row">
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			  	<input type="hidden" name="department" id="department" class="form-control" value="{{ $departments }}" readonly required="required" title="">
	            <div class="form-group">
	             <label>Section<span class="text-red">*</span></label>
	                <select class="form-control" name="section" id="section" style="width: 100%;" data-placeholder="Pilih Section..." required>
	                  @foreach($section as $section)
	                  <option value="{{ $section->section_name }}">{{ $section->section_name }}</option>
	                  @endforeach
	                </select>
	            </div>
	            <input type="hidden" name="subsection" id="subsection" value="-">
	            <div class="form-group">
	              <label for="">Date</label>
				  <input type="text" name="date" id="date" class="form-control" value="{{ date('Y-m-d') }}" readonly required="required" title="">
				  <input type="hidden" name="periode" id="periode" class="form-control" value="{{ $fy }}" readonly required="required" title="">
	            </div>
	        </div>
	        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	            <div class="form-group">
	              <label for="">Chief / Staff</label>
				  <input type="text" name="leader" id="leader" class="form-control" value="{{ $leader }}" readonly required="required" title="">
	            </div>
	            <div class="form-group">
	              <label for="">Manager</label>
				  <input type="text" name="foreman" id="foreman" class="form-control" value="{{ $foreman }}" readonly required="required" title="">
	            </div>
            </div>
          	
        </div>
      </div>
      <div class="modal-footer">
	            <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
	            <input type="submit" value="Submit" onclick="create()" class="btn btn-primary">
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

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.select2').select2({
			language : {
				noResults : function(params) {
					return "There is no date";
				}
			}
		});
	});
	$('.datepicker').datepicker({
		autoclose: true,
		format: "yyyy-mm",
		startView: "months", 
		minViewMode: "months",
		autoclose: true,
	});

	$('.datepicker2').datepicker({
		autoclose: true,
		format: "yyyy-mm",
		startView: "months", 
		minViewMode: "months",
		autoclose: true,
	});
</script>
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
	function deleteConfirmation(url, name, training_id,id,status) {
		jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
		jQuery('#modalDeleteButton').attr("href", url+'/'+training_id+'/'+id+'/'+status);
	}

	function create() {
		var data = {
			department:$('#department').val(),
			subsection:$('#subsection').val(),
			section:$('#section').val(),
			date:$('#date').val(),
			periode:$('#periode').val(),
			leader:$('#leader').val(),
			foreman:$('#foreman').val(),
			status:'chief',
		}

		$.post("{{url('index/interview/store/'.$id)}}", data, function(result, status, xhr){
			if (result.status) {
				openSuccessGritter('Success');
				location.reload();
			}
		});
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
@endsection