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
		{{ $activity_name }} <span class="text-purple">{{ $departments }} - {{ $jishu_hozen_point->nama_pengecekan }}</span>
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
					<div class="col-xs-12">
						<div class="box-header">
							<h3 class="box-title">Filter <span class="text-purple">{{ $activity_name }}</span></h3>
						</div>
						<form role="form" method="post" action="{{url('index/jishu_hozen/filter_jishu_hozen/'.$id.'/'.$jishu_hozen_point_id)}}">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<div class="col-md-12 col-md-offset-4">
								<div class="col-md-4">
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
							<div class="col-md-12 col-md-offset-4">
								<div class="col-md-4">
									<div class="form-group pull-right">
										<a href="{{ url('index/jishu_hozen/nama_pengecekan/'.$id) }}" class="btn btn-warning">Back</a>
										<a href="{{ url('index/jishu_hozen/index/'.$id.'/'.$jishu_hozen_point_id) }}" class="btn btn-danger">Clear</a>
										<button type="submit" class="btn btn-primary col-sm-14">Search</button>
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
								<div class="box-body" style="overflow-x: scroll;">
									<table id="example1" class="table table-bordered table-striped table-hover">
										<thead style="background-color: rgba(126,86,134,.7);">
											<tr>
												<th>Sub Section</th>
												<th>Nama Pengecekan</th>
												<th>Date</th>
												<th>Month</th>
												<th>Foto Aktual</th>
												<th>PIC</th>
												<th>Send Status</th>
												<th>Approval Status</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											@foreach($jishu_hozen as $jishu_hozen)
											<tr>
												<td>{{$jishu_hozen->subsection}}</td>
												<td>{{$jishu_hozen->jishu_hozen_point->nama_pengecekan}}</td>
												<td>{{$jishu_hozen->date}}</td>
												<td>{{$jishu_hozen->month}}</td>
												<td><?php echo $jishu_hozen->foto_aktual ?></td>
												<td>{{$jishu_hozen->pic}}</td>
												<td>
													@if($jishu_hozen->send_status == "")
								                		<label class="label label-danger">Not Yet Sent</label>
								                	@else
								                		<label class="label label-success">Sent</label>
								                	@endif
												</td>
												<td>@if($jishu_hozen->approval == "")
								                		<label class="label label-danger">Not Approved</label>
								                	@else
								                		<label class="label label-success">Approved</label>
								                	@endif</td>
												<td>
													<center>
														<a target="_blank" class="btn btn-info btn-sm" href="{{url('index/jishu_hozen/print_jishu_hozen/'.$id.'/'.$jishu_hozen->id.'/'.$jishu_hozen->month)}}">Print</a>
														@if($jishu_hozen->send_status == "")
														<a class="btn btn-primary btn-sm" href="{{url('index/jishu_hozen/sendemail/'.$jishu_hozen->id.'/'.$jishu_hozen_point_id)}}">Send Email</a>
														@endif
														<button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit-modal" onclick="edit_jishu_hozen('{{ url("index/jishu_hozen/update") }}','{{ $id }}','{{ $jishu_hozen_point_id }}','{{ $jishu_hozen->id }}');">
											               Edit
											            </button>
														<a href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("index/jishu_hozen/destroy") }}','{{ $jishu_hozen->jishu_hozen_point->nama_pengecekan }} - {{ $jishu_hozen->month }}','{{ $id }}','{{ $jishu_hozen_point_id }}' ,'{{ $jishu_hozen->id }}');">
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
        <h4 class="modal-title" align="center"><b>Create Jishu Hozen</b></h4>
      </div>
      <div class="modal-body">
      	<div class="box-body">
        <div>
        	{{-- <form role="form" method="post" action="{{url('index/interview/create_participant/'.$interview_id)}}" enctype="multipart/form-data"> --}}
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				  <input type="hidden" name="jishu_hozen_point_id" id="jishu_hozen_point_id" class="form-control" value="{{ $jishu_hozen_point_id }}" readonly required="required" title="">
	            <div class="form-group">
	              <label for="">Department</label>
				  <input type="text" name="inputdepartment" id="inputdepartment" class="form-control" value="{{ $departments }}" readonly required="required" title="">
	            </div>
	            <div class="form-group">
	              <label for="">Nama Pengecekan</label>
				  <input type="text" class="form-control" name="inputnamapengecekan" id="inputnamapengecekan" placeholder="Enter Leader" value="{{ $jishu_hozen_point->nama_pengecekan }}" readonly>
	            </div>
	            <div class="form-group">
	              <label for="">Date</label>
				  <input type="text" class="form-control" name="date" id="inputdate" placeholder="Enter Leader" value="{{ date('Y-m-d') }}" readonly>
	            </div>
	            <div class="form-group">
	            	<label for="">Month</label>
					<div class="input-group date">
						<div class="input-group-addon bg-white">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="inputmonth"name="inputmonth" placeholder="Select Month" autocomplete="off">
					</div>
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
	              <label for="">PIC</label>
		              <select class="form-control select2" name="inputpic" id="inputpic" style="width: 100%;" data-placeholder="Choose a PIC..." required>
		                <option value=""></option>
		                @foreach($pic as $pic)
		                <option value="{{ $pic->name }}">{{ $pic->employee_id }} - {{ $pic->name }}</option>
		                @endforeach
		              </select>
	            </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            	 <div class="form-group">
	              <label for="">Image (Max Width 800) Click Icon <img width="20px" src="{{ url('/images/pic_icon.png') }}"></label>
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
        {{-- </form> --}}
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
        <h4 class="modal-title" align="center"><b>Edit Jishu Hozen</b></h4>
      </div>
      <div class="modal-body">
      	<div class="box-body">
        <div>
        	{{-- <form role="form" method="post" action="{{url('index/interview/create_participant/'.$interview_id)}}" enctype="multipart/form-data"> --}}
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<input type="hidden" name="jishu_hozen_point_id" id="jishu_hozen_point_id" class="form-control" value="{{ $jishu_hozen_point_id }}" readonly required="required" title="">
				<input type="hidden" name="url" id="url_edit" class="form-control" value="">
	            <div class="form-group">
	              <label for="">Department</label>
				  <input type="text" name="editdepartment" id="editdepartment" class="form-control" value="{{ $departments }}" readonly required="required" title="">
	            </div>
	            <div class="form-group">
	              <label for="">Nama Pengecekan</label>
				  <input type="text" class="form-control" name="editnamapengecekan" id="editnamapengecekan" placeholder="Enter Leader" value="{{ $jishu_hozen_point->nama_pengecekan }}" readonly>
	            </div>
	            <div class="form-group">
	              <label for="">Date</label>
				  <input type="text" class="form-control" name="editdate" id="editdate" placeholder="Enter Leader" readonly>
	            </div>
	            <div class="form-group">
	            	<label for="">Month</label>
					<div class="input-group date">
						<div class="input-group-addon bg-white">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="editmonth"name="editmonth" placeholder="Select Month" autocomplete="off">
					</div>
				</div>
	            <div class="form-group">
	             <label>Sub Section<span class="text-red">*</span></label>
	                <select class="form-control select3" name="editsubsection" id="editsubsection" style="width: 100%;" data-placeholder="Choose a Sub Section..." required>
	                  <option value=""></option>
	                  @foreach($subsection2 as $subsection2)
	                  <option value="{{ $subsection2->sub_section_name }}">{{ $subsection2->sub_section_name }}</option>
	                  @endforeach
	                </select>
	            </div>
	            <div class="form-group">
	              <label for="">PIC</label>
		              <select class="form-control select3" name="editpic" id="editpic" style="width: 100%;" data-placeholder="Choose a PIC..." required>
		                <option value=""></option>
		                @foreach($pic2 as $pic2)
		                <option value="{{ $pic2->name }}">{{ $pic2->employee_id }} - {{ $pic2->name }}</option>
		                @endforeach
		              </select>
	            </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            	 <div class="form-group">
	              <label for="">Image (Max Width 800) Click Icon <img width="20px" src="{{ url('/images/pic_icon.png') }}"></label>
	              <textarea name="editfoto_aktual" id="editfoto_aktual" class="form-control" rows="2" required="required"></textarea>
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
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('.select2').select2({
			dropdownParent: $("#create-modal")
		});

		$('.select3').select2({
			dropdownParent: $("#edit-modal")
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

	$('#inputdate').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
      todayHighlight: true
    });
    
    $('#editdate').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
      todayHighlight: true
    });

    CKEDITOR.replace('inputfoto_aktual' ,{
      filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });
    CKEDITOR.replace('editfoto_aktual' ,{
      filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
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
	function deleteConfirmation(url, name,id,jishu_hozen_point_id,jishu_hozen_id) {
		jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
		jQuery('#modalDeleteButton').attr("href", url+'/'+id+'/'+jishu_hozen_point_id+'/'+jishu_hozen_id);
	}

	function create(){
		var leader = '{{ $leader }}';
		var foreman = '{{ $foreman }}';
		var department = $('#inputdepartment').val();
		var subsection = $('#inputsubsection').val();
		var jishu_hozen_point_id = $('#jishu_hozen_point_id').val();
		var date = $('#inputdate').val();
		var month = $('#inputmonth').val();
		var foto_aktual = CKEDITOR.instances.inputfoto_aktual.getData();
		var pic = $('#inputpic').val();

		var data = {
			department:department,
			subsection:subsection,
			date:date,
			month:month,
			foto_aktual:foto_aktual,
			pic:pic,
			leader:leader,
			foreman:foreman
		}
		console.table(data);
		
		$.post('{{ url("index/jishu_hozen/store/".$id."/".$jishu_hozen_point_id) }}', data, function(result, status, xhr){
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

	function edit_jishu_hozen(url,id,jishu_hozen_point_id,jishu_hozen_id) {
    	$.ajax({
                url: "{{ route('jishu_hozen.getjishuhozen') }}?id=" + jishu_hozen_id,
                method: 'GET',
                success: function(data) {
                  var json = data;
                  // obj = JSON.parse(json);
                  var data = data.data;
                  $("#url_edit").val(url+'/'+id+'/'+jishu_hozen_point_id+'/'+jishu_hozen_id);
                  $("#editdepartment").val(data.department);
                  $("#editsubsection").val(data.subsection).trigger('change.select2');
                  $("#editdate").val(data.date);
                  $("#editmonth").val(data.month);
                  $("#editfoto_aktual").html(CKEDITOR.instances.editfoto_aktual.setData(data.foto_aktual));
                  $("#editpic").val(data.pic).trigger('change.select2');
                }
            });
      // jQuery('#formedit2').attr("action", url+'/'+interview_id+'/'+detail_id);
      // console.log($('#formedit2').attr("action"));
    }

    function update(){
		var department = $('#editdepartment').val();
		var subsection = $('#editsubsection').val();
		var month = $('#editmonth').val();
		var foto_aktual = CKEDITOR.instances.editfoto_aktual.getData();
		var pic = $('#editpic').val();
		var url = $('#url_edit').val();

		var data = {
			department:department,
			subsection:subsection,
			month:month,
			foto_aktual:foto_aktual,
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