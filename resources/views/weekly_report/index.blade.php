@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
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
		/*text-align:center;*/
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
						<form role="form" method="post" action="{{url('index/weekly_report/filter_weekly_report/'.$id)}}">
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
										<a href="{{ url('index/activity_list/filter/'.$id_departments.'/13/'.$frequency) }}" class="btn btn-warning">Back</a>
										<a href="{{ url('index/weekly_report/index/'.$id) }}" class="btn btn-danger">Clear</a>
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
						<form target="_blank" role="form" method="post" action="{{url('index/weekly_report/print_weekly_report/'.$id)}}">
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
						<form role="form" method="post" action="{{url('index/weekly_report/sendemail/'.$id)}}">
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
								<div class="box-body" style="overflow-x: scroll;">
									<table id="example1" class="table table-bordered table-striped table-hover">
										<thead style="background-color: rgba(126,86,134,.7);">
											<tr>
												<th>Sub Section</th>
												<th>Date</th>
												<th>Tinjauan 4M</th>
												<th>Problem / Activity</th>
												<th>Report Action</th>
												<th>Foto Aktual</th>
												<th>Send Status</th>
												<th>Approval Status</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											@foreach($weekly_report as $weekly_report)
											<?php $type = [] ?>
											<tr>
												<td>{{$weekly_report->subsection}}</td>
												<td>{{$weekly_report->date}}</td>
												<td><?php $tinjauan = explode(',', $weekly_report->report_type);
												for ($i = 0; $i < count($tinjauan); $i++) {
												 	if($tinjauan[$i] == 1){
												 		$type[] = 'Man';
												 	}elseif ($tinjauan[$i] == 2) {
												 		$type[] = 'Machine';
												 	}elseif ($tinjauan[$i] == 3) {
												 		$type[] = 'Material';
												 	}elseif ($tinjauan[$i] == 4) {
												 		$type[] = 'Method';
												 	}elseif ($tinjauan[$i] == 5) {
												 		$type[] = 'Other';
												 	}
												 }
												 echo implode(' , ', $type);
												 ?></td>
												<td><?php echo $weekly_report->problem ?></td>
												<td><?php echo $weekly_report->action ?></td>
												<td><?php echo $weekly_report->foto_aktual ?></td>
												<td>
													@if($weekly_report->send_status == "")
								                		<label class="label label-danger">Not Yet Sent</label>
								                	@else
								                		<label class="label label-success">Sent</label>
								                	@endif
												</td>
												<td>@if($weekly_report->approval == "")
								                		<label class="label label-danger">Not Approved</label>
								                	@else
								                		<label class="label label-success">Approved</label>
								                	@endif</td>
												<td>
													<center>
														<button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit-modal" onclick="edit_weekly_report('{{ url("index/weekly_report/update") }}','{{ $weekly_report->id }}');">
											               Edit
											            </button>
														<a href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("index/weekly_report/destroy") }}','{{ implode(' , ', $type) }} - {{ $weekly_report->date }}','{{ $id }}', '{{ $weekly_report->id }}');">
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
        <h4 class="modal-title" align="center"><b>Create Weekly Activity Report</b></h4>
      </div>
      <div class="modal-body">
      	<div class="box-body">
        <div>
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	            <div class="form-group">
	              <label for="">Department</label>
				  <input type="text" name="department" id="inputdepartment" class="form-control" value="{{ $departments }}" readonly required="required" title="">
	            </div>
	            <div class="form-group">
	             <label>Sub Section<span class="text-red">*</span></label>
	                <select class="form-control" name="inputsubsection" id="inputsubsection" style="width: 100%;" data-placeholder="Choose a Sub Section..." required>
	                  @foreach($subsection as $subsection)
	                  <option value="{{ $subsection->sub_section_name }}">{{ $subsection->sub_section_name }}</option>
	                  @endforeach
	                </select>
	            </div>
	            <div class="form-group">
	              <label for="">Date</label>
				  <input type="text" name="inputdate" id="inputdate" class="form-control" value="{{ date('Y-m-d') }}" readonly required="required" title="">
	            </div>
	            <div class="form-group">
	             <label>Tinjauan 4M<span class="text-red">*</span></label><br>
	                <label class="checkbox-inline">
		              <input type="checkbox" class="tinjauanCheckbox" name="tinjauan" value="1" id="tinjauan">Man
		            </label>
		            <label class="checkbox-inline">
		              <input type="checkbox" class="tinjauanCheckbox" name="tinjauan" value="2" id="tinjauan">Machine
		            </label>
		            <label class="checkbox-inline">
		              <input type="checkbox" class="tinjauanCheckbox" name="tinjauan" value="3" id="tinjauan">Material
		            </label>
		            <label class="checkbox-inline">
		              <input type="checkbox" class="tinjauanCheckbox" name="tinjauan" value="4" id="tinjauan">Method
		            </label>
		            <label class="checkbox-inline">
		              <input type="checkbox" class="tinjauanCheckbox" name="tinjauan" value="5" id="tinjauan">Other
		            </label>
	            </div>
	            <div class="form-group">
	             <label>Report Type<span class="text-red">*</span></label>
	                <select class="form-control" onchange="inputproblemactivity(this.value)" name="inputproblemactivity" id="inputproblemactivity" style="width: 100%;" data-placeholder="Choose a Report Type..." required>
	                	<option value="Problem">Problem</option>
	                	<option value="Activity">Activity</option>
	                </select>
	            </div>
	            <div class="form-group">
	              <label for="" id="problem">Problem</label>
	              <label for="" id="activity">Activity</label>
	              <textarea name="inputproblem" id="inputproblem" class="form-control" rows="2" required="required"></textarea>
	            </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            	<div class="form-group" id="action">
	              <label for="">Report Action</label>
	              <textarea name="inputaction" id="inputaction" class="form-control" rows="2" required="required"></textarea>
	            </div>
            	<div class="form-group">
	              <label for="">Foto Aktual (Max Width 200) Click Icon <img width="20px" src="{{ url('/images/pic_icon.png') }}"></label>
	              <textarea name="inputfoto_aktual" id="inputfoto_aktual" class="form-control" rows="2" required="required"></textarea>
	            </div>
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
        <h4 class="modal-title" align="center"><b>Edit Weekly Activity Report</b></h4>
      </div>
      <div class="modal-body">
      	<div class="box-body">
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	            <div class="form-group">
	              <input type="hidden" name="url_edit" id="url_edit" class="form-control">
	              <label for="">Department</label>
				  <input type="text" name="department" id="editdepartment" class="form-control" value="{{ $departments }}" readonly required="required" title="">
	            </div>
	            <div class="form-group">
	             <label>Sub Section<span class="text-red">*</span></label>
	                <select class="form-control" name="editsubsection" id="editsubsection" style="width: 100%;" data-placeholder="Choose a Sub Section..." required>
	                  @foreach($subsection2 as $subsection2)
	                  <option value="{{ $subsection2->sub_section_name }}">{{ $subsection2->sub_section_name }}</option>
	                  @endforeach
	                </select>
	            </div>
	            <div class="form-group">
	              <label for="">Date</label>
				  <input type="text" name="editdate" id="editdate" class="form-control" value="{{ date('Y-m-d') }}" readonly required="required" title="">
	            </div>
	            <div class="form-group">
	             <label>Tinjauan 4M<span class="text-red">*</span></label><br>
	                <label class="checkbox-inline">
		              <input type="checkbox" class="edittinjauanCheckbox" name="edittinjauan" value="1" id="edittinjauan">Man
		            </label>
		            <label class="checkbox-inline">
		              <input type="checkbox" class="edittinjauanCheckbox" name="edittinjauan" value="2" id="edittinjauan">Machine
		            </label>
		            <label class="checkbox-inline">
		              <input type="checkbox" class="edittinjauanCheckbox" name="edittinjauan" value="3" id="edittinjauan">Material
		            </label>
		            <label class="checkbox-inline">
		              <input type="checkbox" class="edittinjauanCheckbox" name="edittinjauan" value="4" id="edittinjauan">Method
		            </label>
		            <label class="checkbox-inline">
		              <input type="checkbox" class="edittinjauanCheckbox" name="edittinjauan" value="5" id="edittinjauan">Other
		            </label>
	            </div>
	            <div class="form-group">
	             <label>Report Type<span class="text-red">*</span></label>
	                <select class="form-control" onchange="inputproblemactivity2(this.value)" name="inputproblemactivity2" id="inputproblemactivity2" style="width: 100%;" data-placeholder="Choose a Report Type..." required>
	                	<option value="Problem">Problem</option>
	                	<option value="Activity">Activity</option>
	                </select>
	            </div>
	            <div class="form-group">
	              <label for="" id="problem2">Problem</label>
	              <label for="" id="activity2">Activity</label>
	              <textarea name="editproblem" id="editproblem" class="form-control" rows="2" required="required"></textarea>
	            </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            	<div class="form-group" id="action2">
	              <label for="">Report Action</label>
	              <textarea name="editaction" id="editaction" class="form-control" rows="2" required="required"></textarea>
	            </div>
            	<div class="form-group">
	              <label for="">Foto Aktual (Max Width 200) Click Icon <img width="20px" src="{{ url('/images/pic_icon.png') }}"></label>
	              <textarea name="editfoto_aktual" id="editfoto_aktual" class="form-control" rows="2" required="required"></textarea>
	            </div>
            </div>
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          	<div class="modal-footer">
	            <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
	            <input type="submit" value="Update" onclick="update()" class="btn btn-primary">
	          </div>
          </div>
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
			dropdownParent: $('#create-modal')
		});

		$('.select3').select2({
			dropdownParent: $('#edit-modal')
		});

		CKEDITOR.replace('inputproblem' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

	    CKEDITOR.replace('inputaction' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

		CKEDITOR.replace('inputfoto_aktual' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

	    CKEDITOR.replace('editaction' ,{
	    	filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

	    CKEDITOR.replace('editproblem' ,{
	    	filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

	    CKEDITOR.replace('editfoto_aktual' ,{
	    	filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

	    $('#activity').hide();
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

    function inputproblemactivity(value) {
    	if (value == 'Problem') {
    		$('#activity').hide();
    		$('#problem').show();
    		$('#action').show();
    		$("#inputaction").html(CKEDITOR.instances.inputaction.setData(''));
    	}else{
    		$('#activity').show();
    		$('#problem').hide();
    		$('#action').hide();
    		$("#inputaction").html(CKEDITOR.instances.inputaction.setData('-'));
    	}
    }

    function inputproblemactivity2(value) {
    	if (value == 'Problem') {
    		$('#activity2').hide();
    		$('#problem2').show();
    		$('#action2').show();
    		$("#editaction").html(CKEDITOR.instances.editaction.setData(''));
    	}else{
    		$('#activity2').show();
    		$('#problem2').hide();
    		$('#action2').hide();
    		$("#editaction").html(CKEDITOR.instances.editaction.setData('-'));
    	}
    }

	
</script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>

	$.fn.modal.Constructor.prototype.enforceFocus = function() {
      modal_this = this
      $(document).on('focusin.modal', function (e) {
        if (modal_this.$element[0] !== e.target && !modal_this.$element.has(e.target).length 
        && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_select') 
        && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_text')) {
          modal_this.$element.focus()
        }
      })
    };

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
	function deleteConfirmation(url, name,id,weekly_report_id) {
		jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
		jQuery('#modalDeleteButton').attr("href", url+'/'+id+'/'+weekly_report_id);
	}

	function create(){
		var type = [];
		var tinjauan;
		$("input[name='tinjauan']:checked").each(function (i) {
            type[i] = $(this).val();
        });
		var leader = '{{ $leader }}';
		var foreman = '{{ $foreman }}';
		var department = $('#inputdepartment').val();
		var subsection = $('#inputsubsection').val();
		var date = $('#inputdate').val();
		var report_type = type.join();
		var problem = CKEDITOR.instances.inputproblem.getData();
		var action = CKEDITOR.instances.inputaction.getData();
		var foto_aktual = CKEDITOR.instances.inputfoto_aktual.getData();

		var data = {
			department:department,
			subsection:subsection,
			date:date,
			report_type:report_type,
			problem:problem,
			action:action,
			foto_aktual:foto_aktual,
			leader:leader,
			foreman:foreman
		}
		console.log(data);
		
		if(report_type == ""){
			alert("Semua Data Harus Diisi.");
		}else{
			$.post('{{ url("index/weekly_report/store/".$id) }}', data, function(result, status, xhr){
				if(result.status){
					$("#create-modal").modal('hide');
					// $('#example1').DataTable().ajax.reload();
					// $('#example2').DataTable().ajax.reload();
					openSuccessGritter('Success','New Weekly Activity Report has been created');
					window.location.reload();
				} else {
					audio_error.play();
					openErrorGritter('Error','Create Weekly Activity Report Failed');
				}
			});
		}
	}

	function edit_weekly_report(url,id) {
    	$.ajax({
                url: "{{ route('weekly_report.getweeklyreport') }}?id=" + id,
                method: 'GET',
                success: function(data) {
                  var json = data;
                  // obj = JSON.parse(json);
                  var data = data.data;
                  var tinjauan = [];
                  var type = [];
                  tinjauan = data.report_type.split(",");
					$("input[name='edittinjauan']").each(function (i) {
			            type[i] = $(this).val();
			            $('.edittinjauanCheckbox')[i].checked = false;
			        });
                  for (var i  = 0;i < tinjauan.length; i++) {
                  	for (var j  = 0;j < type.length; j++) {
	                  	if(type[j] == tinjauan[i]){
	                  		$('.edittinjauanCheckbox')[j].checked = true;
	                  	}
	                }
                  }

                  if (data.action == '<p>-</p>') {
                  	$('#activity').show();
		    		$('#problem').hide();
		    		$('#action').hide();
		    		$("#editaction").html(CKEDITOR.instances.editaction.setData('-'));
		    		$("#inputproblemactivity2").val('Activity').trigger('change.select2');
                  }
                  $("#url_edit").val(url+'/'+id);
                  $("#editdepartment").val(data.department);
                  $("#editsubsection").val(data.subsection).trigger('change.select2');
                  $("#editdate").val(data.date);
                  $("#editproblem").html(CKEDITOR.instances.editproblem.setData(data.problem));
                  $("#editaction").html(CKEDITOR.instances.editaction.setData(data.action));
                  $("#editfoto_aktual").html(CKEDITOR.instances.editfoto_aktual.setData(data.foto_aktual));
                }
            });
      // jQuery('#formedit2').attr("action", url+'/'+interview_id+'/'+detail_id);
      // console.log($('#formedit2').attr("action"));
    }

    function update(){
    	var type = [];
		var tinjauan;
		$("input[name='edittinjauan']:checked").each(function (i) {
            type[i] = $(this).val();
        });
		var leader = '{{ $leader }}';
		var foreman = '{{ $foreman }}';
		var department = $('#editdepartment').val();
		var subsection = $('#editsubsection').val();
		var date = $('#editdate').val();
		var report_type = type.join();
		var problem = CKEDITOR.instances.editproblem.getData();
		var action = CKEDITOR.instances.editaction.getData();
		var foto_aktual = CKEDITOR.instances.editfoto_aktual.getData();
		var url = $('#url_edit').val();

		var data = {
			department:department,
			subsection:subsection,
			date:date,
			report_type:report_type,
			problem:problem,
			action:action,
			foto_aktual:foto_aktual,
			leader:leader,
			foreman:foreman
		}
		console.table(data);
		
		$.post(url, data, function(result, status, xhr){
			if(result.status){
				$("#edit-modal").modal('hide');
				// $('#example1').DataTable().ajax.reload();
				// $('#example2').DataTable().ajax.reload();
				openSuccessGritter('Success','Weekly Activity Report has been updated');
				window.location.reload();
			} else {
				audio_error.play();
				openErrorGritter('Error','Update Weekly Activity Report Failed');
			}
		});
	}
</script>
@endsection