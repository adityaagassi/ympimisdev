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
	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}
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
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<div class="box-header">
							<h3 class="box-title">Filter <span class="text-purple">{{ $activity_name }}</span></h3>
						</div>
						<form role="form" method="post" action="{{url('index/ng_finding/filter_ng_finding/'.$id)}}">
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
										<a href="{{ url('index/activity_list/filter/'.$id_departments.'/14') }}" class="btn btn-warning">Back</a>
										<a href="{{ url('index/ng_finding/index/'.$id) }}" class="btn btn-danger">Clear</a>
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
						<form target="_blank" role="form" method="post" action="{{url('index/ng_finding/print_ng_finding/'.$id)}}">
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
						<form role="form" method="post" action="{{url('index/ng_finding/sendemail/'.$id)}}">
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
												<th>Date</th>
												<th>Material Number</th>
												<th>Material Desc.</th>
												<th>Qty</th>
												<th>Finder</th>
												<th>Picture</th>
												<th>Defect</th>
												<th>Checked By QA</th>
												<th>Send Status</th>
												<th>Approval Status</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											@foreach($ng_finding as $ng_finding)
											<tr>
												<td>{{$ng_finding->date}}</td>
												<td><?php echo $ng_finding->material_number ?></td>
												<td><?php echo $ng_finding->material_description ?></td>
												<td><?php echo $ng_finding->quantity ?></td>
												<td><?php echo $ng_finding->finder ?></td>
												<td><img width="200px" src="{{ url('/data_file/ng_finding/'.$ng_finding->picture) }}"></td>
												<td><?php echo $ng_finding->defect ?></td>
												<td><?php echo $ng_finding->checked_qa ?></td>
												<td>
													@if($ng_finding->send_status == "")
								                		<label class="label label-danger">Not Yet Sent</label>
								                	@else
								                		<label class="label label-success">Sent</label>
								                	@endif
												</td>
												<td>@if($ng_finding->approval == "")
								                		<label class="label label-danger">Not Approved</label>
								                	@else
								                		<label class="label label-success">Approved</label>
								                	@endif</td>
												<td>
													<center>
														<button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit-modal" onclick="edit_ng_finding('{{ url("index/ng_finding/update") }}','{{$id}}','{{ $ng_finding->ng_finding_id }}');">
											               Edit
											            </button>
														<a href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("index/ng_finding/destroy") }}','{{ $ng_finding->material_number }} - {{ $ng_finding->date }}','{{ $id }}', '{{ $ng_finding->ng_finding_id }}');">
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
        <h4 class="modal-title" align="center"><b>Create Temuan NG</b></h4>
      </div>
      <div class="modal-body">
      	<div class="box-body">
       	<form method="POST" action="{{ url("index/ng_finding/store/".$id) }}" enctype="multipart/form-data">
       	<div>
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	            <div class="form-group">
	              <label for="">Department</label>
				  <input type="text" name="inputdepartment" id="inputdepartment" class="form-control" value="{{ $departments }}" readonly required="required" title="">
	            </div>
	            <div class="form-group">
	              <label for="">Date</label>
				  <input type="text" name="inputdate" id="inputdate" class="form-control" placeholder="Select Date" required="required" title="">
	            </div>
	            <div class="form-group">
	              <label for="">Material Number</label>
				  <input type="text" name="inputmaterialnumber" id="inputmaterialnumber" class="form-control" placeholder="Enter Material Number" value="ZW97290" required="required" title="">
	            </div>
	            <div class="form-group">
	              <label for="">Quantity</label>
				  <input type="number" name="inputquantity" id="inputquantity" class="form-control" placeholder="Enter Quantity" required="required" title="">
	            </div>
	            <div class="form-group">
	             <label>Finder<span class="text-red">*</span></label>
	                <select class="form-control select2" name="inputfinder" id="inputfinder" style="width: 100%;" data-placeholder="Choose a Finder ..." required>
	                	<option value=""></option>
	                	@foreach($operator as $operator)
	                		<option value="{{ $operator->name }}">{{ $operator->employee_id }} - {{ $operator->name }}</option>
	                	@endforeach
	                </select>
	            </div>
	            <div class="form-group">
	              <label for="" id="problem">Picture</label>
	              <input type="file" name="inputfile" id="inputfile" class="form-control" onchange="readURL(this);">
	              <img width="200px" id="blah" src="" style="display: none" alt="your image" />
	            </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            	<div class="form-group">
	              <label for="">Defect</label>
	              <input type="text" name="inputdefect" id="inputdefect" class="form-control" placeholder="Enter Defect" required="required" title="">
	            </div>
            	<div class="form-group">
	              <label for="">Checked By QA <img width="20px" src="{{ url('/images/pic_icon.png') }}"></label>
	              <textarea name="inputcheckedqa" id="inputcheckedqa" class="form-control" rows="2" required="required"></textarea>
	            </div>
	            <div class="form-group">
	              <label for="">Leader</label>
	              <input type="text" name="leader" id="leader" class="form-control" placeholder="Enter Leader" readonly value="{{$leader}}" required="required" title="">
	            </div>
	            <div class="form-group" id="action">
	              <label for="">Foreman</label>
	              <input type="text" name="foreman" id="foreman" class="form-control" placeholder="Enter foreman" readonly value="{{$foreman}}" required="required" title="">
	            </div>
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
        <h4 class="modal-title" align="center"><b>Edit Temuan NG</b></h4>
      </div>
      <div class="modal-body">
      	<div class="box-body">
      	<form method="POST" action="" enctype="multipart/form-data" id="form-edit">
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	            <div class="form-group">
	              <label for="">Department</label>
				  <input type="text" name="editdepartment" id="editdepartment" class="form-control" value="{{ $departments }}" readonly required="required" title="">
	            </div>
	            <div class="form-group">
	              <label for="">Date</label>
				  <input type="text" name="editdate" id="editdate" class="form-control" placeholder="Select Date" required="required" title="">
	            </div>
	            <div class="form-group">
	              <label for="">Material Number</label>
				  <input type="text" name="editmaterialnumber" id="editmaterialnumber" class="form-control" placeholder="Enter Material Number" value="ZW97290" required="required" title="">
	            </div>
	            <div class="form-group">
	              <label for="">Quantity</label>
				  <input type="number" name="editquantity" id="editquantity" class="form-control" placeholder="Enter Quantity" required="required" title="">
	            </div>
	            <div class="form-group">
	             <label>Finder<span class="text-red">*</span></label>
	                <select class="form-control select3" name="editfinder" id="editfinder" style="width: 100%;" data-placeholder="Choose a Finder ..." required>
	                	<option value=""></option>
	                	@foreach($operator2 as $operator2)
	                		<option value="{{ $operator2->name }}">{{ $operator2->employee_id }} - {{ $operator2->name }}</option>
	                	@endforeach
	                </select>
	            </div>
	            <div class="form-group">
	              <label for="" id="problem">Picture</label>
	              <input type="file" name="editfile" id="editfile" class="form-control" onchange="readURL2(this);">
	              <img width="200px" id="blah2" src="" style="display: none" alt="your image" />
	            </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            	<div class="form-group">
	              <label for="">Defect</label>
	              <input type="text" name="editdefect" id="editdefect" class="form-control" placeholder="Enter Defect" required="required" title="">
	            </div>
            	<div class="form-group">
	              <label for="">Checked By QA <img width="20px" src="{{ url('/images/pic_icon.png') }}"></label>
	              <textarea name="editcheckedqa" id="editcheckedqa" class="form-control" rows="2" required="required"></textarea>
	            </div>
	            <div class="form-group">
	              <label for="">Leader</label>
	              <input type="text" name="leader" id="leader" class="form-control" placeholder="Enter Leader" readonly value="{{$leader}}" required="required" title="">
	            </div>
	            <div class="form-group">
	              <label for="">Foreman</label>
	              <input type="text" name="foreman" id="foreman" class="form-control" placeholder="Enter foreman" readonly value="{{$foreman}}" required="required" title="">
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
		$('.select2').select2({
			dropdownParent: $('#create-modal')
		});

		$('.select3').select2({
			dropdownParent: $('#edit-modal')
		});

		CKEDITOR.replace('inputcheckedqa' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

	    CKEDITOR.replace('editcheckedqa' ,{
	    	filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	});
	$('.datepicker').datepicker({
		autoclose: true,
		format: "yyyy-mm",
		startView: "months", 
		minViewMode: "months",
		autoclose: true,
	});

	$('#inputdate').datepicker({
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true
	});

	$('#editdate').datepicker({
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true
	});

	$('.datepicker2').datepicker({
		autoclose: true,
		format: "yyyy-mm",
		startView: "months", 
		minViewMode: "months",
		autoclose: true,
	});


	
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
	function deleteConfirmation(url, name,id,ng_finding_id) {
		jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
		jQuery('#modalDeleteButton').attr("href", url+'/'+id+'/'+ng_finding_id);
	}

	function edit_ng_finding(url,id,ng_finding_id) {
		var urlimage = "{{ url('/data_file/ng_finding/') }}";
    	$.ajax({
                url: "{{ route('ng_finding.getngfinding') }}?id=" + ng_finding_id,
                method: 'GET',
                success: function(data) {
                  var json = data;
                  var data = data.data;
                  $("#editdepartment").val(data.department);
                  $("#editdate").val(data.date);
                  // $("#editmaterialnumber").val(data.material_number).trigger('change.select2');
                  $("#editmaterialnumber").val(data.material_number);
                  $("#editquantity").val(data.quantity);
                  $("#editfinder").val(data.finder).trigger('change.select2');
                  $("#editdefect").val(data.defect);
                  $("#editcheckedqa").html(CKEDITOR.instances.editcheckedqa.setData(data.checked_qa));
                  $('#blah2').show();
                  $('#blah2').attr('src', urlimage+'/'+data.picture);
                  // console.log(urlimage+'/'+data.picture);
                }
            });
      jQuery('#form-edit').attr("action", url+'/'+id+'/'+ng_finding_id);
      // console.log($('#form-edit').attr("action"));
    }

	function readURL(input) {
	      if (input.files && input.files[0]) {
	          var reader = new FileReader();

	          reader.onload = function (e) {
	            $('#blah').show();
	              $('#blah')
	                  .attr('src', e.target.result);
	          };

	          reader.readAsDataURL(input.files[0]);
	      }
	}

	function readURL2(input) {
	      if (input.files && input.files[0]) {
	          var reader = new FileReader();

	          reader.onload = function (e) {
	            $('#blah2').show();
	              $('#blah2')
	                  .attr('src', e.target.result);
	          };

	          reader.readAsDataURL(input.files[0]);
	      }
	}
</script>
@endsection