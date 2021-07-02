@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	#listTableBody > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}
	table.table-bordered{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;
		text-align: center;
	}

	.container {
	  display: block;
	  position: relative;
	  padding-left: 35px;
	  margin-bottom: 12px;
	  cursor: pointer;
	  font-size: 16px;
	  -webkit-user-select: none;
	  -moz-user-select: none;
	  -ms-user-select: none;
	  user-select: none;
	}

	/* Hide the browser's default checkbox */
	.container input {
	  position: absolute;
	  opacity: 0;
	  cursor: pointer;
	  height: 0;
	  width: 0;
	}

	/* Create a custom checkbox */
	.checkmark {
	  position: absolute;
	  top: 0;
	  left: 0;
	  height: 25px;
	  width: 25px;
	  background-color: #eee;
	}

	/* On mouse-over, add a grey background color */
	.container:hover input ~ .checkmark {
	  background-color: #ccc;
	}

	/* When the checkbox is checked, add a blue background */
	.container input:checked ~ .checkmark {
	  background-color: #2196F3;
	}

	/* Create the checkmark/indicator (hidden when not checked) */
	.checkmark:after {
	  content: "";
	  position: absolute;
	  display: none;
	}

	/* Show the checkmark when checked */
	.container input:checked ~ .checkmark:after {
	  display: block;
	}

	/* Style the checkmark/indicator */
	.container .checkmark:after {
	  left: 10px;
	  top: 5px;
	  width: 5px;
	  height: 12px;
	  border: solid white;
	  border-width: 0 3px 3px 0;
	  -webkit-transform: rotate(45deg);
	  -ms-transform: rotate(45deg);
	  transform: rotate(45deg);
	}
	#loading { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <span class="text-purple"> {{ $title_jp }} </span>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i></span>
			</center>
		</div>
	</div>
	<div class="box">
		<div class="box-header">
			<input type="hidden" value="{{csrf_token()}}" name="_token" />
			<form method="GET" action="{{ url("export/payment_request") }}">
				<div class="col-md-2" style="padding: 0">
					<div class="form-group">
						<label>Date From</label>
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control pull-right datepicker" id="datefrom" name="datefrom">
						</div>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<label>Date To</label>
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control pull-right datepicker" id="dateto" name="dateto">
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<!-- <div class="form-group">
						<div class="col-md-4" style="padding-right: 0;">
							<label style="color: white;"> x</label>
							<button type="submit" class="btn btn-primary form-control"><i class="fa fa-download"></i> Export Suspense Payment</button>
						</div>
					</div> -->
				</div>

				<div class="col-md-2" style="padding-right: 0;">
					<div class="form-group">
							<label style="color: white;"> x</label>
							<a class="btn btn-success pull-right" style="width: 100%" onclick="newData('new')"><i class="fa fa-plus"></i> &nbsp;Create Suspense Payment</a>
					</div>
				</div>
			</form>
		</div>
		<div class="box-body">
			<div class="row">
				
			</div>

			<table id="listTable" class="table table-bordered table-striped table-hover">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th>#</th>
						<th>Submission Date</th>
						<th>Category</th>
						<th>Remark</th>
						<th>Amount</th>
						<th>Document Attach</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody id="listTableBody">
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
</section>

<div class="modal fade" id="modalNew">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header" style="padding-top: 0;">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: 10px">
					<span aria-hidden="true">&times;</span>
				</button>
				<center><h3 style="font-weight: bold; padding: 3px;" id="modalNewTitle"></h3></center>
				<div class="row">
					<input type="hidden" id="id_edit">
					<div class="col-md-12">

						<div class="col-md-12" style="margin-bottom: 5px;">
							<label for="submission_date" class="col-sm-3 control-label">Req. Date<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<div class="input-group date">
									<div class="input-group-addon">	
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right datepicker"  value="<?= date('d-M-Y') ?>" disabled="">
									<input type="hidden" class="form-control pull-right"  value="{{date('Y-m-d')}}" id="submission_date" name="submission_date">
								</div>
							</div>
						</div>

						<div class="col-md-12" style="margin-bottom: 5px">
							<label for="category" class="col-sm-3 control-label">Category<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<select class="form-control select4" id="category" name="category" data-placeholder='Category' style="width: 100%">
									<option value="">&nbsp;</option>
									<option value="Regular">Regular Payment</option>
									<option value="Irregular">Irregular Payment</option>
								</select>
							</div>
						</div>

						<div class="col-md-12" style="margin-bottom: 5px">
							<label for="Currency" class="col-sm-3 control-label">Currency<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<select class="form-control select4" id="currency" name="currency" data-placeholder='Currency' style="width: 100%">
									<option value="">&nbsp;</option>
									<option value="USD">USD</option>
									<option value="IDR">IDR</option>
									<option value="JPY">JPY</option>
								</select>
							</div>
						</div>

						<div class="col-md-12" style="margin-bottom: 5px;">
							<label for="amount" class="col-sm-3 control-label">Amount<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<input type="number" class="form-control" id="amount" name="amount" placeholder="Total Amount">
							</div>
						</div>

						<div class="col-md-12" style="margin-bottom: 5px">
							<label for="remark" class="col-sm-3 control-label">Remark<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<input type="text" class="form-control" id="remark" name="remark" placeholder="Reason / remark">
							</div>
						</div>

						<!-- <div class="col-md-12" style="margin-bottom: 5px">
							<label for="created_for" class="col-sm-3 control-label">Created For<span class="text-red">*</span></label>
						</div> -->

						<div class="col-md-12" style="margin-bottom: 5px">
							<label for="file" class="col-sm-3 control-label">File Attachment</label>
							<div class="col-sm-9">
								<input type="file" id="file_attach" name="file_attach">
							</div>
						</div>
					</div>
						
				</div>
					<div class="col-md-12">
						<a class="btn btn-success pull-right" onclick="Save('new')" style="width: 100%; font-weight: bold; font-size: 1.5vw;" id="newButton">CREATE</a>
						<a class="btn btn-info pull-right" onclick="Save('update')" style="width: 100%; font-weight: bold; font-size: 1.5vw;" id="updateButton">UPDATE</a>
					</div>
				</div>
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
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {

    	$('body').toggleClass("sidebar-collapse");
		fetchTable();
	});

	$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
		});

	$('.select2').select2({
		dropdownAutoWidth : true,
		allowClear: true
	});

	$(function () {
		$('.select4').select2({
			allowClear:true,
			dropdownAutoWidth : true,
			tags: true,
	        dropdownParent: $('#modalNew')
		});
	})

	// $("#amount").change(function(){
	// 	var output = parseFloat($('#amount').val()); 
	// 	var output2 = output.toLocaleString(undefined,{'minimumFractionDigits':2,'maximumFractionDigits':2});
	// 	$('#amount').val(output2);
 //  	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function newData(id){

		if(id == 'new'){
			$('#modalNewTitle').text('Create Suspense Payment');
			$('#newButton').show();
			$('#updateButton').hide();
			clearNew();
			$('#modalNew').modal('show');
		}
		else{
			$('#newButton').hide();
			$('#updateButton').show();
			var data = {
				id:id
			}
			$.get('{{ url("detail/suspend") }}', data, function(result, status, xhr){
				if(result.status){

					$('#category').html('');
					$('#currency').html('');

					var category = "";
					var currency = "";

					$('#submission_date').val(result.suspend.submission_date);

					if(result.suspend.category == "Regular"){
						category += '<option value="Regular" selected>Regular Payment</option>';
						category += '<option value="Irregular">Irregular Payment</option>';
					}
					else if (result.suspend.category == "Irregular"){
						category += '<option value="Regular">Regular Payment</option>';
						category += '<option value="Irregular" selected>Irregular Payment</option>';
					}

					$('#category').append(category);

					if(result.suspend.currency == "USD"){
						currency += '<option value="USD" selected>USD</option>';
						currency += '<option value="IDR">IDR</option>';
						currency += '<option value="JPY">JPY</option>';
					}
					else if (result.suspend.currency == "IDR"){
						currency += '<option value="USD">USD</option>';
						currency += '<option value="IDR" selected>IDR</option>';
						currency += '<option value="JPY">JPY</option>';
					}
					else if (result.suspend.currency == "JPY"){
						currency += '<option value="USD">USD</option>';
						currency += '<option value="IDR">IDR</option>';
						currency += '<option value="JPY" selected>JPY</option>';
					}

					$('#currency').append(currency);
					$('#amount').val(result.suspend.amount);
					$('#remark').val(result.suspend.remark);
					$('#id_edit').val(result.suspend.id);

					$('#modalNewTitle').text('Update Suspense Payment');
					$('#loading').hide();
					$('#modalNew').modal('show');
				}
				else{
					openErrorGritter('Error', result.message);
					$('#loading').hide();
					audio_error.play();
				}
			});
		}
	}

	function Save(id){	
		$('#loading').show();

		if(id == 'new'){
			if($("#submission_date").val() == "" || $('#category').val() == null || $('#currency').val() == "" || $('#amount').val() == "" || $('#remark').val() == ""){
				
				$('#loading').hide();
				openErrorGritter('Error', "Please fill field with (*) sign.");
				return false;
			}

			var formData = new FormData();
			
			formData.append('submission_date', $("#submission_date").val());
			formData.append('category', $("#category").val());
			formData.append('currency', $("#currency").val());
			formData.append('amount', $("#amount").val());
			formData.append('remark', $("#remark").val());
			formData.append('file_attach', $('#file_attach').prop('files')[0]);

			$.ajax({
				url:"{{ url('create/suspend') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status) {
						openSuccessGritter('Success', data.message);
						audio_ok.play();
						$('#loading').hide();
						$('#modalNew').modal('hide');
						clearNew();
						fetchTable();
					}else{
						openErrorGritter('Error!',data.message);
						$('#loading').hide();
						audio_error.play();
					}

				}
			});
		}
		else{
			if($("#submission_date").val() == "" || $('#category').val() == null || $('#currency').val() == "" || $('#amount').val() == "" || $('#remark').val() == ""){
				
				$('#loading').hide();
				openErrorGritter('Error', "Please fill field with (*) sign.");
				return false;
			}
			var formData = new FormData();
			
			formData.append('id_edit', $("#id_edit").val());
			formData.append('submission_date', $("#submission_date").val());
			formData.append('category', $("#category").val());
			formData.append('currency', $("#currency").val());
			formData.append('amount', $("#amount").val());
			formData.append('remark', $("#remark").val());
			formData.append('file_attach', $("#file_attach").prop('files')[0]);

			$.ajax({
				url:"{{ url('edit/suspend') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status) {
						openSuccessGritter('Success', data.message);
						audio_ok.play();
						$('#loading').hide();
						$('#modalNew').modal('hide');
						clearNew();
						fetchTable();
					}else{
						openErrorGritter('Error!',data.message);
						$('#loading').hide();
						audio_error.play();
					}
				}
			});
		}
	}

	function clearNew(){
		$('#id_edit').val('');
		$("#category").val('').trigger('change');
		$('#currency').val('').trigger('change');
		$("#amount").val('');
		$('#remark').val('');
	}

	function fetchTable(){
		$('#loading').show();
		$.get('{{ url("fetch/suspend") }}', function(result, status, xhr){
			if(result.status){
				$('#listTable').DataTable().clear();
				$('#listTable').DataTable().destroy();				
				$('#listTableBody').html("");
				var listTableBody = "";
				var count_all = 0;

				$.each(result.suspend, function(key, value){
					listTableBody += '<tr>';
					listTableBody += '<td style="width:0.1%;">'+parseInt(key+1)+'</td>';
					listTableBody += '<td style="width:1%;">'+value.submission_date+'</td>';
					listTableBody += '<td style="width:3%;">'+value.category+'</td>';
					listTableBody += '<td style="width:2%;">'+value.remark+'</td>';
					listTableBody += '<td style="width:2%;">'+value.amount.toLocaleString()+'</td>';

					if (value.file != null) {
						listTableBody += '<td style="width:0.1%;"><a target="_blank" href="{{ url("files/suspend") }}/'+value.file+'"><i class="fa fa-paperclip"></i></td>';
					}
					else{
						listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:0.1%;"> - </td>';
					}

					if (value.posisi == 'user') {
						listTableBody += '<td style="width:0.1%;"><span class="label label-danger">Not Sent</span></td>';
					}
					else if (value.posisi == 'manager'){
						listTableBody += '<td style="width:0.1%;"><span class="label label-warning">Approval Manager</span></td>';
					}
					else if (value.posisi == 'direktur'){
						listTableBody += '<td style="width:0.1%;"><span class="label label-warning">Approval Director</span></td>';
					}
					else if (value.posisi == 'presdir'){
						listTableBody += '<td style="width:0.1%;"><span class="label label-warning">Approval Presdir</span></td>';
					}
					else if (value.posisi == 'acc'){
						listTableBody += '<td style="width:0.1%;"><span class="label label-warning">Diverifikasi Accounting</span></td>';
					}
					else{
						listTableBody += '<td style="width:0.1%;"><span class="label label-success">Diterima Accounting</span></td>';
					}

					if (value.posisi == "user")
					{
						listTableBody += '<td style="width:2%;"><center><button class="btn btn-md btn-primary" onclick="newData(\''+value.id+'\')"><i class="fa fa-edit"></i> </button>  <a class="btn btn-md btn-danger" target="_blank" href="{{ url("report/suspend") }}/'+value.id+'"><i class="fa fa-file-pdf-o"></i> </a> <button class="btn btn-md btn-success" data-toggle="tooltip" title="Send Email" style="margin-right:5px;" onclick="sendEmail(\''+value.id+'\')"><i class="fa fa-envelope"></i></button></center></td>';
					}

					else{
						listTableBody += '<td style="width:2%;"><a class="btn btn-md btn-danger" target="_blank" href="{{ url("report/suspend") }}/'+value.id+'"><i class="fa fa-file-pdf-o"></i> </a></center></td>';
					}

					listTableBody += '</tr>';

					count_all += 1;
				});

				$('#listTableBody').append(listTableBody);

				$('#listTable tfoot th').each( function () {
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				} );

				var table = $('#listTable').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
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
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 20,
					'searching': true,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
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

				$('#listTable tfoot tr').appendTo('#listTable thead');

				$('#loading').hide();

			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);
				$('#loading').hide();
			}
		});
	}

	function sendEmail(id) {
      var data = {
        id:id
      };

      if (!confirm("Apakah anda yakin ingin mengirim Suspense Payment ini ke Manager Accounting?")) {
        return false;
      }
      else{
      	$("#loading").show();
      }

      $.get('{{ url("email/suspend") }}', data, function(result, status, xhr){
        openSuccessGritter("Success","Email Berhasil Terkirim");
      	$("#loading").hide();
        setTimeout(function(){  window.location.reload() }, 2500);
      })
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

