@extends('layouts.master')
@section('stylesheets')
<script src="{{ asset('/ckeditor-full/ckeditor.js') }}"></script>
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
		Daily Evidence {{ $activity_name }} <span class="text-purple">{{ $departments }}</span>
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
							<h3 class="box-title">Filter <span class="text-purple">{{ $proses }}</span></h3>
						</div>
						<form role="form" method="post" action="{{url('index/first_product_audit/filter_first_product_daily/'.$id.'/'.$first_product_audit_id)}}">
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
										<a href="{{ url('index/first_product_audit/list_proses/'.$id) }}" class="btn btn-warning">Back</a>
										<a href="{{ url('index/first_product_audit/daily/'.$id.'/'.$first_product_audit_id) }}" class="btn btn-danger">Clear</a>
										<button type="submit" class="btn btn-primary col-sm-14">Search</button>
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<div class="box-header">
							<h3 class="box-title">Cetak <span class="text-purple">{{ $proses }}</span></h3>
						</div>
						<form target="_blank" role="form" method="post" action="{{url('index/first_product_audit/print_first_product_audit_daily/'.$id.'/'.$first_product_audit_id)}}">
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
							<h3 class="box-title">Send Email <span class="text-purple">{{ $proses }}</span></h3>
						</div>
						<form role="form" method="post" action="{{url('index/first_product_audit/sendemail_daily/'.$id.'/'.$first_product_audit_id)}}">
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
									{{-- <a href="{{ url('index/first_product_audit/create_details/'.$first_product_audit_id) }}" class="btn btn-primary">Create {{ $proses }}</a> --}}
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
								<div class="box-body" style="overflow-x: scroll">
									<table id="example1" class="table table-bordered table-striped table-hover">
										<thead style="background-color: rgba(126,86,134,.7);">
											<tr>
												<th>Proses</th>
												<th>Jenis</th>
												<th>Date</th>
												<th>Auditor</th>
												<th>Judgement</th>
												<th>Note</th>
												<th>PIC</th>
												<th>Leader</th>
												<th>Foreman</th>
												<th>Send Status</th>
												<th>Approval Status</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											@foreach($first_product_audit_daily as $first_product_audit_daily)
											<tr>
												<td>{{$first_product_audit_daily->first_product_audit->proses}}</td>
												<td>{{$first_product_audit_daily->first_product_audit->jenis}}</td>
												<td>{{$first_product_audit_daily->date}}</td>
												<td>{{$first_product_audit_daily->auditor}}</td>
												<td>
													@if($first_product_audit_daily->judgement == "")
								                		<label class="label label-danger">{{ $first_product_audit_daily->judgement }}</label>
								                	@else
								                		<label class="label label-success">{{ $first_product_audit_daily->judgement }}</label>
								                	@endif
												</td>
												<td><?php echo $first_product_audit_daily->note ?></td>
												<td>{{$first_product_audit_daily->pic}}</td>
												<td>{{$first_product_audit_daily->leader}}</td>
												<td>{{$first_product_audit_daily->foreman}}</td>
												<td>
													@if($first_product_audit_daily->send_status == "")
								                		<label class="label label-danger">Not Yet Sent</label>
								                	@else
								                		<label class="label label-success">Sent</label>
								                	@endif
												</td>
												<td>@if($first_product_audit_daily->approval == "")
								                		<label class="label label-danger">Not Approved</label>
								                	@else
								                		<label class="label label-success">Approved</label>
								                	@endif</td>
												<td>
													<center>
														<button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit-modal" onclick="editdetails('{{ url("index/first_product_audit/update_daily") }}','{{ $id }}','{{ $first_product_audit_daily->id }}');">
											               <i class="fa fa-edit"></i>
											            </button>
									                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("index/first_product_audit/destroy_daily") }}','{{ $first_product_audit_daily->first_product_audit->proses }} - {{ $first_product_audit_daily->date }}','{{ $id }}','{{ $first_product_audit_daily->id }}');">
									                      <i class="fa fa-trash"></i>
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
<div class="modal fade" id="create-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" align="center"><b>Create Daily Audit Cek Produk Pertama</b></h4>
      </div>
      <div class="modal-body">
      	<div class="box-body">
        <form role="form" method="post" action="{{url('index/first_product_audit/store_daily/'.$id.'/'.$first_product_audit_id)}}" enctype="multipart/form-data">
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            	<input type="hidden" class="form-control" name="first_product_audit_id" id="inputfirst_product_audit_id" placeholder="Enter Leader" value="{{ $first_product_audit_id }}" readonly>
            	<input type="hidden" class="form-control" name="activity_list_id" id="inputactivity_list_id" placeholder="Enter Leader" value="{{ $id }}" readonly>
	            <div class="form-group">
	              <label for="">Proses</label>
				  <input type="text" class="form-control" name="proses" id="inputproses" placeholder="Enter Leader" value="{{ $proses }}" readonly>
	            </div>
	            <div class="form-group">
	              <label for="">Jenis</label>
				  <input type="text" class="form-control" name="jenis" id="inputjenis" placeholder="Enter Leader" value="{{ $jenis }}" readonly>
	            </div>
	            <div class="form-group">
	              <label for="">Date</label>
				  <input type="text" class="form-control" name="date" id="inputdate" placeholder="Enter Leader" value="{{ date('Y-m-d') }}" readonly>
	            </div>
	            <div class="form-group">
	              <label for="">PIC</label>
		              <select class="form-control select2" name="pic" id="inputpic" style="width: 100%;" data-placeholder="Choose a PIC..." required>
		                <option value=""></option>
		                @foreach($operator as $operator)
		                <option value="{{ $operator->name }}">{{ $operator->employee_id }} - {{ $operator->name }}</option>
		                @endforeach
		              </select>
	            </div>
	            <div class="form-group">
	              <label for="">Judgement</label>
				  <div class="radio">
				    <label><input type="radio" name="judgement" id="inputjudgement" value="Good">Good</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="judgement" id="inputjudgement" value="Not Good">Not Good</label>
				  </div>
	            </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	            <div class="form-group">
	              <label for="">Note</label>
				  <textarea name="note" id="inputnote" class="form-control" rows="2" required="required"></textarea>
	            </div>
	            <div class="form-group">
	              <label for="">Auditor</label>
				  <input type="text" class="form-control" name="auditor" id="inputauditor" placeholder="Enter Leader" value="{{ $leader2 }}" readonly>
	            </div>
	            <div class="form-group">
	              <label for="">Leader</label>
				  <input type="text" class="form-control" name="leader" id="inputleader" placeholder="Enter Leader" value="{{ $leader }}" readonly>
	            </div>
	            <div class="form-group">
	              <label for="">Foreman</label>
				  <input type="text" class="form-control" name="foreman" id="inputforeman" placeholder="Enter Leader" value="{{ $foreman }}" readonly>
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
        <h4 class="modal-title" align="center"><b>Edit Daily Audit Cek Produk Pertama</b></h4>
      </div>
      <div class="modal-body">
      	<div class="box-body">
        <form role="form" id="formedit" method="post" action="" enctype="multipart/form-data">
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            	<input type="hidden" class="form-control" name="editfirst_product_audit_id" id="editfirst_product_audit_id" placeholder="Enter Leader" value="{{ $first_product_audit_id }}" readonly>
            	<input type="hidden" class="form-control" name="editactivity_list_id" id="editactivity_list_id" placeholder="Enter Leader" value="{{ $id }}" readonly>
	            <div class="form-group">
	              <label for="">Proses</label>
				  <input type="text" class="form-control" name="editproses" id="editproses" placeholder="Enter Leader" value="{{ $proses }}" readonly>
	            </div>
	            <div class="form-group">
	              <label for="">Jenis</label>
				  <input type="text" class="form-control" name="editjenis" id="editjenis" placeholder="Enter Leader" value="{{ $jenis }}" readonly>
	            </div>
	            <div class="form-group">
	              <label for="">Date</label>
				  <input type="text" class="form-control" name="editdate" id="editdate" placeholder="Enter Leader" readonly>
	            </div>
	            <div class="form-group">
	              <label for="">PIC</label>
		              <select class="form-control select3" name="editpic" id="editpic" style="width: 100%;" data-placeholder="Choose a PIC..." required>
		                <option value=""></option>
		                @foreach($operator2 as $operator2)
		                <option value="{{ $operator2->name }}">{{ $operator2->employee_id }} - {{ $operator2->name }}</option>
		                @endforeach
		              </select>
	            </div>
	            <div class="form-group">
	              <label for="">Judgement</label>
				  <div class="radio">
				    <label><input type="radio" name="editjudgement" id="editjudgement" value="Good">Good</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="editjudgement" id="editjudgement" value="Not Good">Not Good</label>
				  </div>
	            </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	            <div class="form-group">
	              <label for="">Note</label>
				  <textarea name="editnote" id="editnote" class="form-control" rows="2" required="required"></textarea>
	            </div>
	            <div class="form-group">
	              <label for="">Auditor</label>
				  <input type="text" class="form-control" name="editauditor" id="editauditor" placeholder="Enter Leader" value="{{ $leader2 }}" readonly>
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

	CKEDITOR.replace('note' ,{
      filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });
    CKEDITOR.replace('editnote' ,{
      filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });

    CKEDITOR.replace('foto_aktual' ,{
      filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });
    CKEDITOR.replace('editfoto_aktual' ,{
      filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });

    function readInput(input) {
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
    function readEdit(input) {
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
	function deleteConfirmation(url, name, id,first_product_audit_details_id) {
		jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
		jQuery('#modalDeleteButton').attr("href", url+'/'+id+'/'+first_product_audit_details_id);
	}

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

	function create(){
		var first_product_audit_id = $('#inputfirst_product_audit_id').val();
		var activity_list_id = $('#inputactivity_list_id').val();
		var proses = $('#inputproses').val();
		var jenis = $('#inputjenis').val();
		var date = $('#inputdate').val();
		var no_seri = $('#inputno_seri').val();
		var judgement = $('input[id="inputjudgement"]:checked').val();
		var pic = $('#inputpic').val();
		var keterangan = CKEDITOR.instances.inputketerangan.getData();
		var leader = $('#inputleader').val();
		var foreman = $('#inputforeman').val();

		var data = {
			first_product_audit_id:first_product_audit_id,
			activity_list_id:activity_list_id,
			proses:proses,
			jenis:jenis,
			date:date,
			no_seri:no_seri,
			judgement:judgement,
			pic:pic,
			keterangan:keterangan,
			leader:leader,
			foreman:foreman
		}
		console.table(data);
		$.post('{{ url("index/first_product_audit/create_participant") }}', data, function(result, status, xhr){
			if(result.status){
				$("#create-modal").modal('hide');
				// $('#example1').DataTable().ajax.reload();
				// $('#example2').DataTable().ajax.reload();
				openSuccessGritter('Success','New Participant has been created');
				window.location.reload();
			} else {
				audio_error.play();
				openErrorGritter('Error','Create Participant Failed');
			}
		});
	}
	function editdetails(url, id,first_product_audit_detail_id) {
    	$.ajax({
                url: "{{ route('first_product_audit.getdaily') }}?id=" + first_product_audit_detail_id,
                method: 'GET',
                success: function(data) {
                  var json = data;
                  // obj = JSON.parse(json);
                  var urlimage = '{{ url('/data_file/cek_produk_pertama/') }}';
                  var data = data.data;
                  console.log(data);
                  $("#editpic").val(data.pic).trigger('change.select2');
                  $("#editnote").html(CKEDITOR.instances.editnote.setData(data.note));
                  $("#editdate").val(data.date);
                  $('input[id="editjudgement"][value="'+data.judgement+'"]').prop('checked',true);
                }
            });
    	console.log(first_product_audit_detail_id);
      jQuery('#formedit').attr("action", url+'/'+id+'/'+first_product_audit_detail_id);
      console.log($('#formedit').attr("action"));
    }
</script>
@endsection