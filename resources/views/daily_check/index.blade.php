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
		{{ $activity_name }} - {{ $leader }} - {{ $product }}
		<button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#create-modal">
	        Buat Cek FG / KD
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
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<div class="box-header">
							<h3 class="box-title">Filter {{ $activity_name }}</h3>
						</div>
						<form role="form" method="post" action="{{url('index/daily_check_fg/filter_daily_check/'.$id.'/'.$product)}}">
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
										<a href="{{ url('index/production_report/index/'.$id_departments) }}" class="btn btn-warning">Back</a>
										<a href="{{ url('index/daily_check_fg/index/'.$id.'/'.$product) }}" class="btn btn-danger">Clear</a>
										<button type="submit" class="btn btn-primary col-sm-14">Search</button>
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<div class="box-header">
							<h3 class="box-title">Cetak {{ $activity_name }}</h3>
						</div>
						<!-- <form target="_blank" role="form" method="post" action="{{url('index/daily_check_fg/print_daily_check/'.$id)}}"> -->
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<div class="col-md-12 col-md-offset-2">
								<div class="col-md-10">
									<div class="form-group">
										<div class="input-group date">
											<div class="input-group-addon bg-white">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control datepicker2" id="tgl_print" name="month" placeholder="Select Month" required autocomplete="off">
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-12 col-md-offset-2">
								<div class="col-md-10">
									<div class="form-group pull-right">
										<button onclick="printPdf('{{$id}}',$('#tgl_print').val())" class="btn btn-primary col-sm-14">Print</button>
									</div>
								</div>
							</div>
						<!-- </form> -->
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<div class="box-header">
							<h3 class="box-title">Kirim Email ke Foreman</span></h3>
						</div>
						<form role="form" method="post" action="{{url('index/daily_check_fg/sendemail/'.$id)}}">
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
										<button type="submit" class="btn btn-primary col-sm-14">Kirim Email</button>
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="row">
						<div class="col-xs-12" style="overflow-x: scroll;">
							<table id="example1" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>Production Date</th>
										<th>Check Date</th>
										<th>Serial Number</th>
										<th>Condition</th>
										<th>Keterangan</th>
										<th>Send Status</th>
										<th>Approval Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									@foreach($daily_check as $daily_check)
									<tr>
										<td>{{$daily_check->production_date}}</td>
										<td>{{$daily_check->check_date}}</td>
										<td>{{$daily_check->serial_number}}</td>
										<td>{{$daily_check->condition}}</td>
										<td>{{$daily_check->keterangan}}</td>
										<td>
											@if($daily_check->send_status == "")
						                		<label class="label label-danger">Not Yet Sent</label>
						                	@else
						                		<label class="label label-success">Sent</label>
						                	@endif
										</td>
										<td>@if($daily_check->approval == "")
						                		<label class="label label-danger">Not Approved</label>
						                	@else
						                		<label class="label label-success">Approved</label>
						                	@endif</td>
										<td>
											<center>
												<a class="btn btn-info btn-sm" href="{{url('index/daily_check_fg/show/'.$id.'/'.$daily_check->id)}}">View</a>
												{{-- <a href="{{url('index/daily_check_fg/edit/'.$id.'/'.$daily_check->id)}}" class="btn btn-warning btn-xs">Edit</a> --}}
												<button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit-modal" onclick="edit_daily('{{ url("index/daily_check_fg/update") }}','{{ $daily_check->id }}','{{ $daily_check->product }}');">
									               Edit
									            </button>
												<a href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("index/daily_check_fg/destroy") }}', '{{ $daily_check->product }} - {{ $daily_check->production_date }} - {{ $daily_check->serial_number }}','{{ $id }}', '{{ $daily_check->id }}');">
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
        <h4 class="modal-title" align="center"><b>Create Daily Check</b></h4>
      </div>
      <div class="modal-body">
      	<div class="box-body">
        <div>
        	{{-- <form role="form" method="post" action="{{url('index/interview/create_participant/'.$interview_id)}}" enctype="multipart/form-data"> --}}
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	            <div class="form-group">
	              <label for="">Department</label>
				  <input type="text" name="department" id="inputDepartment" class="form-control" value="{{ $departments }}" readonly required="required" title="">
	            </div>
	            <div class="form-group">
	              <label for="">Product</label>
				  <input type="text" name="product" id="inputProduct" class="form-control" value="{{ $product }}" readonly required="required" title="">
	            </div>
	            <div class="form-group">
	              <label for="">Production Date</label>
				  {{-- <input type="text" name="production_date" id="inputProductionDate" class="form-control" value="{{ date('Y-m-d') }}" readonly required="required" title=""> --}}
				  <input type="text" class="form-control pull-right" id="inputProductionDate" name="production_date"  placeholder="Select Date">
	            </div>
	            <div class="form-group">
	              <label for="">Check Date</label>
				  <input type="text" name="check_date" id="inputCheckDate" class="form-control" value="{{ date('Y-m-d') }}" readonly required="required" title="">
	            </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            	<div class="form-group">
	              <label for="">Serial Number / Photo Number</label>
				  <input type="text" name="serial_number" id="inputSerialNumber" class="form-control" placeholder="Masukkan Serial Number / Photo Number" required="required" title="">
	            </div>
	            <div class="form-group">
	              <label for="">Condition</label>
				  <div class="radio">
				    <label><input type="radio" name="condition" id="inputCondition" value="Good">Good</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="condition" id="inputCondition" value="Not Good">Not Good</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label for="">Keterangan</label>
				  <input type="text" name="keterangan" id="inputKeterangan" class="form-control" placeholder="Masukkan Keterangan" required="required" title="" value="-">
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
        <h4 class="modal-title" align="center"><b>Edit Daily Check</b></h4>
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
				  <input type="text" name="department" id="editDepartment" class="form-control" value="" readonly required="required" title="">
	            </div>
	            <div class="form-group">
	              <label for="">Product</label>
				  <input type="text" name="product" id="editProduct" class="form-control" value="" readonly required="required" title="">
	            </div>
	            <div class="form-group">
	              <label for="">Production Date</label>
				  {{-- <input type="text" name="production_date" id="editProductionDate" class="form-control" value="" readonly required="required" title=""> --}}
				  <input type="text" class="form-control pull-right" id="editProductionDate" name="production_date"  placeholder="Select Date">
	            </div>
	            <div class="form-group">
	              <label for="">Check Date</label>
				  <input type="text" name="check_date" id="editCheckDate" class="form-control" value="" readonly required="required" title="">
	            </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            	<div class="form-group">
	              <label for="">Serial Number / Photo Number</label>
				  <input type="text" name="serial_number" id="editSerialNumber" class="form-control" placeholder="Masukkan Serial Number / Photo Number" required="required" title="">
	            </div>
	            <div class="form-group">
	              <label for="">Condition</label>
				  <div class="radio">
				    <label><input type="radio" name="condition" id="editCondition" value="Good">Good</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="condition" id="editCondition" value="Not Good">Not Good</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label for="">Keterangan</label>
				  <input type="text" name="keterangan" id="editKeterangan" class="form-control" placeholder="Masukkan Keterangan" required="required" title="" value="">
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
	function deleteConfirmation(url, name, sampling_check_id,id) {
		jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
		jQuery('#modalDeleteButton').attr("href", url+'/'+sampling_check_id+'/'+id);
	}

	function create(){
		var leader = '{{ $leader }}';
		var foreman = '{{ $foreman }}';
		var department = $('#inputDepartment').val();
		var product = $('#inputProduct').val();
		var production_date = $('#inputProductionDate').val();
		var check_date = $('#inputCheckDate').val();
		var serial_number = $('#inputSerialNumber').val();
		var condition = $('input[id="inputCondition"]:checked').val();
		var keterangan = $('#inputKeterangan').val();

		var data = {
			department:department,
			product:product,
			production_date:production_date,
			check_date:check_date,
			serial_number:serial_number,
			condition:condition,
			keterangan:keterangan,
			leader:leader,
			foreman:foreman
		}
		console.table(data);
		
		$.post('{{ url("index/daily_check_fg/store/".$id."/".$product) }}', data, function(result, status, xhr){
			if(result.status){
				$("#create-modal").modal('hide');
				// $('#example1').DataTable().ajax.reload();
				// $('#example2').DataTable().ajax.reload();
				openSuccessGritter('Success','New Daily Check has been created');
				window.location.reload();
			} else {
				audio_error.play();
				openErrorGritter('Error','Create Daily Check Failed');
			}
		});
	}

	function edit_daily(url,id,product) {
    	$.ajax({
                url: "{{ route('daily_check_fg.getdetail') }}?id=" + id,
                method: 'GET',
                success: function(data) {
                  var json = data;
                  // obj = JSON.parse(json);
                  var data = data.data;
                  $("#url_edit").val(url+'/'+id+'/'+product);
                  $("#editDepartment").val(data.department);
                  $("#editProduct").val(data.product);
                  $("#editProductionDate").val(data.production_date);
                  $("#editCheckDate").val(data.check_date);
                  $("#editSerialNumber").val(data.serial_number);
                  $('input[id="editCondition"][value="'+data.condition+'"]').prop('checked',true);
                  $("#editKeterangan").val(data.keterangan);
                }
            });
      // jQuery('#formedit2').attr("action", url+'/'+interview_id+'/'+detail_id);
      // console.log($('#formedit2').attr("action"));
    }

    function update(){
		var department = $('#editDepartment').val();
		var product = $('#editProduct').val();
		var production_date = $('#editProductionDate').val();
		var check_date = $('#editCheckDate').val();
		var serial_number = $('#editSerialNumber').val();
		var condition = $('input[id="editCondition"]:checked').val();
		var keterangan = $('#editKeterangan').val();
		var url = $('#url_edit').val();

		var data = {
			department:department,
			product:product,
			production_date:production_date,
			check_date:check_date,
			serial_number:serial_number,
			condition:condition,
			keterangan:keterangan,
		}
		console.table(data);
		
		$.post(url, data, function(result, status, xhr){
			if(result.status){
				$("#edit-modal").modal('hide');
				// $('#example1').DataTable().ajax.reload();
				// $('#example2').DataTable().ajax.reload();
				openSuccessGritter('Success','Daily Check has been updated');
				window.location.reload();
			} else {
				audio_error.play();
				openErrorGritter('Error','Update Daily Check Failed');
			}
		});
	}

	function printPdf(id,month) {
		if (month == "") {
			alert('Pilih Bulan');
		}else{
			var url = "{{url('index/daily_check_fg/print_daily_check/')}}";
			// console.log(url + '/' + id+ '/' + month);
			window.open(url + '/' + id+ '/' + month,"_blank");
		}
	}
</script>
@endsection