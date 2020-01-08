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
			<div class="box box-primary">
				<div class="box-body">
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<div class="box-header">
							<h3 class="box-title">Filter <span class="text-purple">{{ $activity_name }}</span></h3>
						</div>
						<form role="form" method="post" action="{{url('index/area_check/filter_area_check/'.$id)}}">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<div class="col-md-12 col-md-offset-2">
								<div class="col-md-10">
									<div class="form-group">
										<div class="input-group date">
											<div class="input-group-addon bg-white">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control datepicker" id="tgl"name="month" placeholder="Select Month" autocomplete="off">
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-12 col-md-offset-2">
								<div class="col-md-10">
									<div class="form-group pull-right">
										<a href="{{ url('index/activity_list/filter/'.$id_departments.'/10') }}" class="btn btn-warning">Back</a>
										<a href="{{ url('index/area_check/index/'.$id) }}" class="btn btn-danger">Clear</a>
										<button type="submit" class="btn btn-primary col-sm-14">Search</button>
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<div class="box-header">
							<h3 class="box-title">Cetak <span class="text-purple">{{ $activity_name }}</span></h3>
						</div>
						<form target="_blank" role="form" method="post" action="{{url('index/area_check/print_area_check/'.$id)}}">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<div class="col-md-12 col-md-offset-2">
								<div class="col-md-10">
									<div class="form-group">
										<div class="input-group date">
											<div class="input-group-addon bg-white">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control datepicker2" id="tgl" name="month" placeholder="Select Date" required autocomplete="off">
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-12 col-md-offset-2">
								<div class="col-md-10">
									<div class="form-group pull-right">
										<button type="submit" class="btn btn-primary col-sm-14">Print</button>
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<div class="box-header">
							<h3 class="box-title">Send Email <span class="text-purple">{{ $activity_name }}</span></h3>
						</div>
						<form role="form" method="post" action="{{url('index/area_check/sendemail/'.$id)}}">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<div class="col-md-12 col-md-offset-2">
								<div class="col-md-10">
									<div class="form-group">
										<div class="input-group date">
											<div class="input-group-addon bg-white">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control datepicker2" id="tgl" name="month" placeholder="Select Date" required autocomplete="off">
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-12 col-md-offset-2">
								<div class="col-md-10">
									<div class="form-group pull-right">
										<button type="submit" class="btn btn-primary col-sm-14">Send Email</button>
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<div class="col-md-12">
							<div class="col-md-12">
								<div class="form-group pull-right">
									{{-- <a href="{{ url('index/daily_check_fg/create/'.$id.'/'.$product) }}" class="btn btn-primary">Create {{ $activity_alias }}</a> --}}
									<button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#create-modal">
								        Create
								    </button>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<div class="box">
								<div class="box-body">
									<table id="example1" class="table table-bordered table-striped table-hover">
										<thead style="background-color: rgba(126,86,134,.7);">
											<tr>
												<th>Sub Section</th>
												<th>Point Check</th>
												<th>Date</th>
												<th>Condition</th>
												<th>PIC</th>
												<th>Send Status</th>
												<th>Approval Status</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											@if(count($area_check) != 0)
											@foreach($area_check as $area_check)
											<tr>
												<td>{{$area_check->subsection}}</td>
												<td>{{$area_check->area_check_point->point_check}}</td>
												<td>{{$area_check->date}}</td>
												<td>{{$area_check->condition}}</td>
												<td>{{$area_check->pic}}</td>
												<td>
													@if($area_check->send_status == "")
								                		<label class="label label-danger">Not Yet Sent</label>
								                	@else
								                		<label class="label label-success">Sent</label>
								                	@endif
												</td>
												<td>@if($area_check->approval == "")
								                		<label class="label label-danger">Not Approved</label>
								                	@else
								                		<label class="label label-success">Approved</label>
								                	@endif</td>
												<td>
													<center>
														{{-- <a class="btn btn-info btn-sm" href="{{url('index/area_check/show/'.$id.'/'.$area_check->id)}}">View</a> --}}
														{{-- <a href="{{url('index/daily_check_fg/edit/'.$id.'/'.$daily_check->id)}}" class="btn btn-warning btn-xs">Edit</a> --}}
														<button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit-modal" onclick="edit_area_check('{{ url("index/area_check/update") }}','{{ $area_check->id }}');">
											               Edit
											            </button>
														<a href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("index/area_check/destroy") }}', '{{ $area_check->area_check_point->point_check }} - {{ $area_check->date }}','{{ $id }}', '{{ $area_check->id }}');">
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
        <h4 class="modal-title" align="center"><b>Create Area Check</b></h4>
      </div>
      <div class="modal-body">
      	<div class="box-body">
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	            <div class="form-group">
	              <label for="">Department</label>
				  <input type="text" name="department" id="inputdepartment" class="form-control" value="{{ $departments }}" readonly required="required" title="">
	            </div>
	            <div class="form-group">
	             <label>Sub Section<span class="text-red">*</span></label>
	                <select class="form-control select2" name="inputsubsection" id="inputsubsection" style="width: 100%;" data-placeholder="Choose a Sub Section..." required>
	                  <option value=""></option>
	                  @foreach($subsection as $subsection)
	                  <option value="{{ $subsection->sub_section_name }}">{{ $subsection->sub_section_name }}</option>
	                  @endforeach
	                </select>
	            </div>
	            <div class="form-group">
	              <label for="">Date</label>
				  <input type="text" name="inputdate" id="inputdate" class="form-control" value="{{ date('Y-m-d') }}" readonly required="required" title="">
	            </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            	<div class="form-group">
            		<?php $no = 1 ?>
	              <label>Point Check<span class="text-red">*</span></label>
	                <select class="form-control select2" name="inputpoint_check" id="inputpoint_check" style="width: 100%;" data-placeholder="Choose a Point Check..." required>
	                  <option value=""></option>
	                  @foreach($point_check as $point_check)
	                    <option value="{{ $point_check->id }}">{{ $no }}. {{ $point_check->point_check }}</option>
	                    <?php $no++ ?>
	                  @endforeach
	                </select>
	            </div>
	            <div class="form-group">
	              <label for="">Condition</label>
				  <div class="radio">
				    <label><input type="radio" name="condition" id="inputcondition" value="Good">Good</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="condition" id="inputcondition" value="Not Good">Not Good</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label>PIC<span class="text-red">*</span></label>
	                <select class="form-control select2" name="inputpic" id="inputpic" style="width: 100%;" data-placeholder="Choose a PIC..." required>
	                  <option value=""></option>
	                  @foreach($pic as $pic)
	                    <option value="{{ $pic->name }}">{{ $pic->employee_id }} - {{ $pic->name }}</option>
	                  @endforeach
	                </select>
	            </div>
            </div>
		      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		      	<div class="modal-footer">
		          <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
		          <input type="submit" value="Submit" onclick="create()" class="btn btn-primary">
		        </div>
		      </div>
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
        <h4 class="modal-title" align="center"><b>Edit Area Check</b></h4>
      </div>
      <div class="modal-body">
      	<div class="box-body">
        <div>
        	{{-- <form role="form" method="post" action="{{url('index/interview/create_participant/'.$interview_id)}}" enctype="multipart/form-data"> --}}
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	            <div class="form-group">
	              <label for="">Department</label>
	              <input type="hidden" name="url" id="url_edit" class="form-control" value="">
				  <input type="text" name="department" id="editdepartment" class="form-control" value="" readonly required="required" title="">
	            </div>
	            <div class="form-group">
	             <label>Sub Section<span class="text-red">*</span></label>
	                <select class="form-control select2" name="editsubsection" id="editsubsection" style="width: 100%;" data-placeholder="Choose a Sub Section..." required>
	                  <option value=""></option>
	                  @foreach($subsection2 as $subsection2)
	                  <option value="{{ $subsection2->sub_section_name }}">{{ $subsection2->sub_section_name }}</option>
	                  @endforeach
	                </select>
	            </div>
	            <div class="form-group">
	              <label for="">Date</label>
				  <input type="text" name="editdate" id="editdate" class="form-control" readonly required="required" title="">
	            </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            	<div class="form-group">
	              <label>Point Check<span class="text-red">*</span></label>
	              <?php $no = 1 ?>
	                <select class="form-control select2" name="editpoint_check" id="editpoint_check" style="width: 100%;" data-placeholder="Choose a Point Check..." required>
	                  <option value=""></option>
	                  @foreach($point_check2 as $point_check2)
	                    <option value="{{ $point_check2->id }}">{{ $no }}. {{ $point_check2->point_check }}</option>
	                    <?php $no++ ?>
	                  @endforeach
	                </select>
	            </div>
	            <div class="form-group">
	              <label for="">Condition</label>
				  <div class="radio">
				    <label><input type="radio" name="condition" id="editcondition" value="Good">Good</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="condition" id="editcondition" value="Not Good">Not Good</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label>PIC<span class="text-red">*</span></label>
	                <select class="form-control select2" name="editpic" id="editpic" style="width: 100%;" data-placeholder="Choose a PIC..." required>
	                  <option value=""></option>
	                  @foreach($pic2 as $pic2)
	                    <option value="{{ $pic2->name }}">{{ $pic2->employee_id }} - {{ $pic2->name }}</option>
	                  @endforeach
	                </select>
	            </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          	<div class="modal-footer">
            <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            <input type="submit" value="Update" onclick="update()" class="btn btn-primary">
          </div>
          </div>
        {{-- </form> --}}
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

	$('#inputProductionDate').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
      todayHighlight: true
    });
    
    $('#editProductionDate').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
      todayHighlight: true
    });

	
</script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<!-- <script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script> -->
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
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
	function deleteConfirmation(url, name,id,area_check_id) {
		jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
		jQuery('#modalDeleteButton').attr("href", url+'/'+id+'/'+area_check_id);
	}

	function create(){
		var leader = '{{ $leader }}';
		var foreman = '{{ $foreman }}';
		var department = $('#inputdepartment').val();
		var subsection = $('#inputsubsection').val();
		var point_check = $('#inputpoint_check').val();
		var date = $('#inputdate').val();
		var condition = $('input[id="inputcondition"]:checked').val();
		var pic = $('#inputpic').val();

		var data = {
			department:department,
			subsection:subsection,
			date:date,
			point_check:point_check,
			condition:condition,
			pic:pic,
			leader:leader,
			foreman:foreman
		}
		console.table(data);
		
		$.post('{{ url("index/area_check/store/".$id) }}', data, function(result, status, xhr){
			if(result.status){
				$("#create-modal").modal('hide');
				// $('#example1').DataTable().ajax.reload();
				// $('#example2').DataTable().ajax.reload();
				openSuccessGritter('Success','New Area Check has been created');
				window.location.reload();
			} else {
				audio_error.play();
				openErrorGritter('Error','Create Area Check Failed');
			}
		});
	}

	function edit_area_check(url,id) {
    	$.ajax({
                url: "{{ route('area_check.getareacheck') }}?id=" + id,
                method: 'GET',
                success: function(data) {
                  var json = data;
                  // obj = JSON.parse(json);
                  var data = data.data;
                  $("#url_edit").val(url+'/'+id);
                  $("#editdepartment").val(data.department);
                  $("#editsubsection").val(data.subsection).trigger('change.select2');
                  $("#editpoint_check").val(data.area_check_point_id).trigger('change.select2');
                  $("#editdate").val(data.date);
                  $('input[id="editcondition"][value="'+data.condition+'"]').prop('checked',true);
                  $("#editpic").val(data.pic).trigger('change.select2');
                }
            });
      // jQuery('#formedit2').attr("action", url+'/'+interview_id+'/'+detail_id);
      // console.log($('#formedit2').attr("action"));
    }

    function update(){
		var department = $('#editdepartment').val();
		var subsection = $('#editsubsection').val();
		var area_check_point_id = $('#editpoint_check').val();
		var date = $('#editdate').val();
		var condition = $('input[id="editcondition"]:checked').val();
		var pic = $('#editpic').val();
		var url = $('#url_edit').val();

		var data = {
			department:department,
			subsection:subsection,
			area_check_point_id:area_check_point_id,
			date:date,
			condition:condition,
			pic:pic,
		}
		console.table(data);
		
		$.post(url, data, function(result, status, xhr){
			if(result.status){
				$("#edit-modal").modal('hide');
				// $('#example1').DataTable().ajax.reload();
				// $('#example2').DataTable().ajax.reload();
				openSuccessGritter('Success','Area Check has been updated');
				window.location.reload();
			} else {
				audio_error.play();
				openErrorGritter('Error','Update Area Check Failed');
			}
		});
	}
</script>
@endsection