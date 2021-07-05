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
	#tableAudit > tbody > tr > td{
		text-align:center;
	}
	.radio {
		display: inline-block;
		position: relative;
		padding-left: 35px;
		margin-bottom: 12px;
		cursor: pointer;
		font-size: 12px;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
	}
	.radio input {
		position: absolute;
		opacity: 0;
		cursor: pointer;
	}
	.checkmark {
		position: absolute;
		top: 0;
		left: 0;
		height: 25px;
		width: 25px;
		background-color: #ccc;
		border-radius: 50%;
	}

	/* On mouse-over, add a grey background color */
	.radio:hover input ~ .checkmark {
		background-color: #ccc;
	}

	/* When the radio button is checked, add a blue background */
	.radio input:checked ~ .checkmark {
		background-color: #2196F3;
	}

	/* Create the indicator (the dot/circle - hidden when not checked) */
	.checkmark:after {
		content: "";
		position: absolute;
		display: none;
	}

	/* Show the indicator (dot/circle) when checked */
	.radio input:checked ~ .checkmark:after {
		display: block;
	}

	/* Style the indicator (dot/circle) */
	.radio .checkmark:after {
		top: 9px;
		left: 9px;
		width: 8px;
		height: 8px;
		border-radius: 50%;
		background: white;
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		<span style="font-size: 20px">Audit Kanban {{ $remark }} - {{ $leader }} <small class="text-purple">かんばん監査</small></span>
		<button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#create-modal">
	        Buat Audit
	    </button>
	</h1>
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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: white; top: 45%; left: 50%;">
			<span style="font-size: 40px"><i class="fa fa-spinner fa-spin" id="loadingDetail" style="font-size: 80px;"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<div class="box-header">
							<h3 class="box-title">Filter</h3>
						</div>
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="col-md-12 col-md-offset-2">
							<div class="col-md-10">
								<div class="form-group">
									<div class="input-group date">
										<div class="input-group-addon bg-white">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker2" id="date_from" name="date_from" placeholder="Date From" autocomplete="off">
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-md-offset-2">
							<div class="col-md-10">
								<div class="form-group">
									<div class="input-group date">
										<div class="input-group-addon bg-white">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker2" id="date_to" name="date_to" placeholder="Date To" autocomplete="off">
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-md-offset-2">
							<div class="col-md-10">
								<div class="form-group pull-right">
									<a href="{{ url('index/production_report/index/'.$id_departments) }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/audit_kanban/index/'.$id) }}" class="btn btn-danger">Clear</a>
									<button onclick="fetchAuditKanban()" class="btn btn-primary col-sm-14">Search</button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<div class="box-header">
							<h3 class="box-title">Cetak Audit Kanban</h3>
						</div>
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="col-md-12 col-md-offset-2">
							<div class="col-md-10">
								<div class="form-group">
									<div class="input-group date">
										<div class="input-group-addon bg-white">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="tgl_print" name="month" placeholder="Pilih Bulan" required autocomplete="off">
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-md-offset-2">
							<div class="col-md-10">
								<div class="form-group pull-right">
									<button onclick="printPdf('{{$id}}',$('#tgl_print').val())" class="btn btn-primary col-sm-14">Cetak</button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<div class="box-header">
							<h3 class="box-title">Kirim Email ke Foreman</h3>
						</div>
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="col-md-12 col-md-offset-2">
							<div class="col-md-10">
								<div class="form-group">
									<div class="input-group date">
										<div class="input-group-addon bg-white">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker2" id="date_email" name="date_email" placeholder="Pilih Tanggal" required autocomplete="off">
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-md-offset-2">
							<div class="col-md-10">
								<div class="form-group pull-right">
									<button onclick="sendEmail()" class="btn btn-primary col-sm-14">Kirim Email</button>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12" style="overflow-x: scroll;">
							<table id="tableAudit" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%">Check Date</th>
										<th style="width: 1%">Location</th>
										<th style="width: 4%">Point Check</th>
										<th style="width: 1%">Condition</th>
										<th style="width: 1%">Send Status</th>
										<th style="width: 1%">Approval Status</th>
										<th style="width: 2%">Action</th>
									</tr>
								</thead>
								<tbody id="bodyTableAudit">
									
								</tbody>
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
      <div class="modal-header" style="background-color: #ffb03b">
        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button> -->
        <h4 class="modal-title" align="center"><b>Check List Audit Kanban Daily</b><br>かんばん監査のチェックリスト</h4>
      </div>
      <div class="modal-body">
      	<div class="box-body">
        <div>
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
            <div class="col-xs-4 col-md-offset-4">
	            <div class="form-group">
	              <label for="">Check Date</label>
				  <input type="hidden" name="department" id="inputdepartment" class="form-control" value="{{ $departments }}" readonly required="required" title="">
				  <input type="text" name="check_date" id="inputcheck_date" class="form-control" value="{{ date('Y-m-d') }}" readonly required="required" title="">
				  <input type="hidden" name="leader" id="inputleader" class="form-control" value="{{ $leader }}" readonly required="required" title="">
				  <input type="hidden" name="foreman" id="inputforeman" class="form-control" value="{{ $foreman }}" readonly required="required" title="">
	            </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            	<table class="table table-bordered table-striped table-hover">
            		<thead style="background-color: rgba(126,86,134,.7);color: white">
            			<tr>
            				<th style="width: 5%">No.<br>番</th>
            				<th style="width: 60%">Point Check<br>監査箇所</th>
            				<th style="width: 35%">Condition<br>調子</th>
            			</tr>
            		</thead>
            		<tbody>
            			<?php $index = 0;
            			$no = 1;
            			$countpoint = count($point_check) ?>
            			@foreach($point_check as $point_check)
            			<tr>
            				<td style="vertical-align: middle;">{{$no}}</td>
            				<td style="vertical-align: middle;padding: 5px"><input type="hidden" name="inputpoint_check_id_{{$index}}" id="inputpoint_check_id_{{$index}}" value="{{$point_check->id}}">{{$point_check->point_check_name}}<br>{{$point_check->point_check_jp}}</td>
            				<td style="vertical-align: middle;">
            					<label class="radio" style="margin-top: 5px;margin-left: 5px">OK
									<input type="radio" checked="checked" id="condition_{{$index}}" name="condition_{{$index}}" value="OK">
									<span class="checkmark"></span>
								</label>
								&nbsp;&nbsp;
								<label class="radio" style="margin-top: 5px">NG
									<input type="radio" id="condition_{{$index}}" name="condition_{{$index}}" value="NG">
									<span class="checkmark"></span>
								</label>
								<label class="radio" style="margin-top: 5px">Tidak Ada
									<input type="radio" id="condition_{{$index}}" name="condition_{{$index}}" value="Tidak Ada">
									<span class="checkmark"></span>
								</label>
							</td>
            			</tr>
            			<?php $index++;$no++; ?>
            			@endforeach
            		</tbody>
            	</table>
            </div>
          </div>
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          	<!-- <div class="modal-footer"> -->
	            <button type="button" class="btn btn-danger pull-left" style="font-weight: bold;" data-dismiss="modal">Cancel</button>
          	<!-- </div> -->
          </div>
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          	<!-- <div class="modal-footer"> -->
	            <button type="button" class="btn btn-success pull-right" onclick="create()" style="font-weight: bold;">Submit</button>
          	<!-- </div> -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="edit-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #ffb03b">
        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button> -->
        <h4 class="modal-title" align="center"><b>Check List Audit Kanban Daily</b><br>かんばん監査のチェックリスト</h4>
      </div>
      <div class="modal-body">
      	<div class="box-body">
        <div>
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
            <div class="col-xs-4 col-md-offset-4">
	            <div class="form-group">
	              <label for="">Check Date</label>
				  <input type="hidden" name="department" id="editdepartment" class="form-control" value="{{ $departments }}" readonly required="required" title="">
				  <input type="text" name="check_date" id="editcheck_date" class="form-control" value="" readonly required="required" title="">
				  <input type="hidden" name="leader" id="editleader" class="form-control" value="{{ $leader }}" readonly required="required" title="">
				  <input type="hidden" name="foreman" id="editforeman" class="form-control" value="{{ $foreman }}" readonly required="required" title="">
	            </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            	<table class="table table-bordered table-striped table-hover">
            		<thead style="background-color: rgba(126,86,134,.7);color: white">
            			<tr>
            				<th style="width: 1%">No.<br>番</th>
            				<th style="width: 74%">Point Check<br>監査箇所</th>
            				<th style="width: 25%">Condition<br>調子</th>
            			</tr>
            		</thead>
            		<tbody>
            			<?php $index = 0;
            			$no = 1;
            			$countpoint2 = count($point_check2) ?>
            			@foreach($point_check2 as $point_check)
            			<tr>
            				<td style="vertical-align: middle;">{{$no}}</td>
            				<td style="vertical-align: middle;padding: 5px"><input type="hidden" name="edit_id_{{$index}}" id="edit_id_{{$index}}">{{$point_check->point_check_name}}<br>{{$point_check->point_check_jp}}</td>
            				<td style="vertical-align: middle;">
            					<label class="radio" style="margin-top: 5px;margin-left: 5px">OK
									<input type="radio" checked="checked" id="editcondition_{{$index}}" name="editcondition_{{$index}}" value="OK">
									<span class="checkmark"></span>
								</label>
								&nbsp;&nbsp;
								<label class="radio" style="margin-top: 5px">NG
									<input type="radio" id="editcondition_{{$index}}" name="editcondition_{{$index}}" value="NG">
									<span class="checkmark"></span>
								</label>
								<label class="radio" style="margin-top: 5px">Tidak Ada
									<input type="radio" id="editcondition_{{$index}}" name="editcondition_{{$index}}" value="Tidak Ada">
									<span class="checkmark"></span>
								</label>
							</td>
            			</tr>
            			<?php $index++;$no++; ?>
            			@endforeach
            		</tbody>
            	</table>
            </div>
          </div>
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          	<!-- <div class="modal-footer"> -->
	            <button type="button" class="btn btn-danger pull-left" style="font-weight: bold;" data-dismiss="modal">Cancel</button>
          	<!-- </div> -->
          </div>
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          	<!-- <div class="modal-footer"> -->
	            <button type="button" class="btn btn-success pull-right" onclick="update()" style="font-weight: bold;">Update</button>
          	<!-- </div> -->
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.select2').select2({
			dropdownParent: $('#create-modal')
		});

		fetchAuditKanban();

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});

		$('.datepicker2').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			autoclose: true,
			todayHighlight: true
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
	    // console.log('{{$countpoint}}');
	    // for (var i = 0; i < parseInt('{{$countpoint}}'); i++) {
	    // 	$("input[name='condition_"+i+"']").each(function (i) {
	    //         $('.aturan_k3Checkbox')[i].checked = false;
	    //     });
	    // }
	    $('input[type="radio"]').prop('checked', false);
	});

    // function inputproblemactivity(value) {
    // 	if (value == 'Problem') {
    // 		$('#activity').hide();
    // 		$('#problem').show();
    // 		$('#action').show();
    // 		$("#inputaction").html(CKEDITOR.instances.inputaction.setData(''));
    // 	}else{
    // 		$('#activity').show();
    // 		$('#problem').hide();
    // 		$('#action').hide();
    // 		$("#inputaction").html(CKEDITOR.instances.inputaction.setData('-'));
    // 	}
    // }

    // function inputproblemactivity2(value) {
    // 	if (value == 'Problem') {
    // 		$('#activity2').hide();
    // 		$('#problem2').show();
    // 		$('#action2').show();
    // 		$("#editaction").html(CKEDITOR.instances.editaction.setData(''));
    // 	}else{
    // 		$('#activity2').show();
    // 		$('#problem2').hide();
    // 		$('#action2').hide();
    // 		$("#editaction").html(CKEDITOR.instances.editaction.setData('-'));
    // 	}
    // }

    function fetchAuditKanban() {
    	$('#loading').show();
    	var data = {
    		id:'{{$id}}',
    		date_from:$('#date_from').val(),
    		date_to:$('#date_to').val(),
    	}

    	$.get('{{ url("fetch/audit_kanban") }}', data, function(result, status, xhr){
			if(result.status){
				$('#bodyTableAudit').html('');
				$('#tableAudit').DataTable().clear();
				$('#tableAudit').DataTable().destroy();
				var bodyAudit = "";

				for(var i = 0; i< result.audit_kanban.length;i++){
					bodyAudit += '<tr>';
					bodyAudit += '<td>'+result.audit_kanban[i].check_date+'</td>';
					bodyAudit += '<td>'+result.audit_kanban[i].remark+'</td>';
					bodyAudit += '<td>'+result.audit_kanban[i].point_check_name+'<br>'+result.audit_kanban[i].point_check_jp+'</td>';
					if (result.audit_kanban[i].condition == 'OK') {
						bodyAudit += '<td style="background-color:#c5ffb8">'+result.audit_kanban[i].condition+'</td>';
					}else if(result.audit_kanban[i].condition == 'NG'){
						bodyAudit += '<td style="background-color:#ffbcb8">'+result.audit_kanban[i].condition+'</td>';
					}else if(result.audit_kanban[i].condition == 'Tidak Ada'){
						bodyAudit += '<td style="background-color:#b8caff">'+result.audit_kanban[i].condition+'</td>';
					}
					if (result.audit_kanban[i].send_status == null) {
						bodyAudit += '<td><span class="label label-danger">Belum Terkirim</span></td>';
					}else{
						bodyAudit += '<td><span class="label label-success">Terkirim</span></td>';
					}
					if (result.audit_kanban[i].approval == null) {
						bodyAudit += '<td><span class="label label-danger">Not Approved</span></td>';
					}else{
						bodyAudit += '<td><span class="label label-success">Approved</span></td>';
					}
					if (result.audit_kanban[i].send_status == null) {
						bodyAudit += '<td><button class="btn btn-warning" onclick="edit(\''+result.audit_kanban[i].id_audit_kanban+'\')">Edit</button></td>';
					}else{
						bodyAudit += '<td></td>';
					}
					bodyAudit += '</tr>';
				}
				$('#bodyTableAudit').append(bodyAudit);

				var table = $('#tableAudit').DataTable({
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
						}
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching': true	,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});
				$('#loading').hide();
			} else {
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error','Get Data Failed');
			}
		});
    }

    function create(){
		$('#loading').show();
		var condition = [];
		var point_check_id = [];
		for(var i = 0; i< parseInt('{{$countpoint}}');i++){
			$("input[name='condition_"+i+"']:checked").each(function (i) {
	            condition.push($(this).val());
	        });
	        point_check_id.push($('#inputpoint_check_id_'+i).val());
		}
		if (condition.length < parseInt('{{$countpoint}}')) {
			openErrorGritter('Error!','Semua Data Harus Diisi.');
			return false;
		}
		var leader = '{{ $leader }}';
		var foreman = '{{ $foreman }}';
		var department = $('#inputdepartment').val();
		var check_date = $('#inputcheck_date').val();

		var data = {
			id:'{{$id}}',
			department:department,
			check_date:check_date,
			count_point :parseInt('{{$countpoint}}'),
			condition:condition,
			point_check_id:point_check_id,
			leader:leader,
			foreman:foreman
		}
		$.post('{{ url("input/audit_kanban") }}', data, function(result, status, xhr){
			if(result.status){
				fetchAuditKanban();
				$("#create-modal").modal('hide');
				$('#loading').hide();
				openSuccessGritter('Success','Audit Kanban Berhasil Dibuat');
			} else {
				audio_error.play();
				openErrorGritter('Error','Gagal Buat Audit Kanban');
				$('#loading').hide();
			}
		});
	}

	function update(){
		$('#loading').show();
		var condition = [];
		var id = [];
		for(var i = 0; i< parseInt('{{$countpoint2}}');i++){
			$("input[name='editcondition_"+i+"']:checked").each(function (i) {
	            condition.push($(this).val());
	        });
	        id.push($('#edit_id_'+i).val());
		}
		if (condition.length < parseInt('{{$countpoint}}')) {
			openErrorGritter('Error!','Semua Data Harus Diisi.');
			return false;
		}
		var leader = '{{ $leader }}';
		var foreman = '{{ $foreman }}';
		var check_date = $('#editcheck_date').val();

		var data = {
			id:'{{$id}}',
			check_date:check_date,
			count_point :parseInt('{{$countpoint2}}'),
			condition:condition,
			edit_id:id,
			leader:leader,
			foreman:foreman
		}
		$.post('{{ url("update/audit_kanban") }}', data, function(result, status, xhr){
			if(result.status){
				fetchAuditKanban();
				$("#edit-modal").modal('hide');
				$('#loading').hide();
				openSuccessGritter('Success','Update Audit Kanban Berhasil');
			} else {
				audio_error.play();
				openErrorGritter('Error','Gagal Update Audit Kanban');
				$('#loading').hide();
			}
		});
	}

	function edit(id) {
		$('input[type="radio"]').prop('checked', false);
		$('#loading').show();
    	var data = {
			id:'{{$id}}',
			id_audit_kanban:id,
		}
		$.get('{{ url("edit/audit_kanban") }}', data, function(result, status, xhr){
			if(result.status){
				$('#editcheck_date').val(result.audit_kanban[0].check_date);
				for(var i = 0; i< result.audit_kanban.length;i++){
					$('input[id="editcondition_'+i+'"][value="'+result.audit_kanban[i].condition+'"]').prop('checked',true);
					$('#edit_id_'+i).val(result.audit_kanban[i].id);
				}
				$("#edit-modal").modal('show');
				$('#loading').hide();
			} else {
				audio_error.play();
				openErrorGritter('Error','Gagal Edit');
				$('#loading').hide();
			}
		});
    }

	function sendEmail() {
		$('#loading').show();
		if ($('#date_email').val() == "") {
			audio_error.play();
			openErrorGritter('Error','Tanggal Harus Diisi');
			$('#loading').hide();
		}else{
			var data = {
				id:'{{$id}}',
				check_date:$('#date_email').val()
			}
			$.get('{{ url("email/audit_kanban") }}', data, function(result, status, xhr){
				if(result.status){
					fetchAuditKanban();
					$('#loading').hide();
					openSuccessGritter('Success','Berhasil Mengirim Email');
				} else {
					audio_error.play();
					openErrorGritter('Error',result.message);
					$('#loading').hide();
				}
			});
		}
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
	// jQuery(document).ready(function() {
	// 	$('#example1 tfoot th').each( function () {
	// 		var title = $(this).text();
	// 		$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
	// 	} );
	// 	var table = $('#example1').DataTable({
	// 		"order": [],
	// 		'dom': 'Bfrtip',
	// 		'responsive': true,
	// 		'lengthMenu': [
	// 		[ 10, 25, 50, -1 ],
	// 		[ '10 rows', '25 rows', '50 rows', 'Show all' ]
	// 		],
	// 		'buttons': {
	// 			buttons:[
	// 			{
	// 				extend: 'pageLength',
	// 				className: 'btn btn-default',
	// 			},
	// 			{
	// 				extend: 'copy',
	// 				className: 'btn btn-success',
	// 				text: '<i class="fa fa-copy"></i> Copy',
	// 				exportOptions: {
	// 					columns: ':not(.notexport)'
	// 				}
	// 			},
	// 			{
	// 				extend: 'excel',
	// 				className: 'btn btn-info',
	// 				text: '<i class="fa fa-file-excel-o"></i> Excel',
	// 				exportOptions: {
	// 					columns: ':not(.notexport)'
	// 				}
	// 			},
	// 			{
	// 				extend: 'print',
	// 				className: 'btn btn-warning',
	// 				text: '<i class="fa fa-print"></i> Print',
	// 				exportOptions: {
	// 					columns: ':not(.notexport)'
	// 				}
	// 			},
	// 			]
	// 		}
	// 	});

	// 	table.columns().every( function () {
	// 		var that = this;

	// 		$( 'input', this.footer() ).on( 'keyup change', function () {
	// 			if ( that.search() !== this.value ) {
	// 				that
	// 				.search( this.value )
	// 				.draw();
	// 			}
	// 		} );
	// 	} );

	// 	$('#example1 tfoot tr').appendTo('#example1 thead');

	// });
	// $(function () {

	// 	$('#example2').DataTable({
	// 		'paging'      : true,
	// 		'lengthChange': false,
	// 		'searching'   : false,
	// 		'ordering'    : true,
	// 		'info'        : true,
	// 		'autoWidth'   : false
	// 	})
	// })
	// function deleteConfirmation(url, name,id,weekly_report_id) {
	// 	jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
	// 	jQuery('#modalDeleteButton').attr("href", url+'/'+id+'/'+weekly_report_id);
	// }

	

 //    function update(){
 //    	var type = [];
	// 	var tinjauan;
	// 	$("input[name='edittinjauan']:checked").each(function (i) {
 //            type[i] = $(this).val();
 //        });
	// 	var leader = '{{ $leader }}';
	// 	var foreman = '{{ $foreman }}';
	// 	var department = $('#editdepartment').val();
	// 	var subsection = $('#editsubsection').val();
	// 	var date = $('#editdate').val();
	// 	var report_type = type.join();
	// 	var problem = CKEDITOR.instances.editproblem.getData();
	// 	var action = CKEDITOR.instances.editaction.getData();
	// 	var foto_aktual = CKEDITOR.instances.editfoto_aktual.getData();
	// 	var url = $('#url_edit').val();

	// 	var data = {
	// 		department:department,
	// 		subsection:subsection,
	// 		date:date,
	// 		report_type:report_type,
	// 		problem:problem,
	// 		action:action,
	// 		foto_aktual:foto_aktual,
	// 		leader:leader,
	// 		foreman:foreman
	// 	}
	// 	console.table(data);
		
	// 	$.post(url, data, function(result, status, xhr){
	// 		if(result.status){
	// 			$("#edit-modal").modal('hide');
	// 			// $('#example1').DataTable().ajax.reload();
	// 			// $('#example2').DataTable().ajax.reload();
	// 			openSuccessGritter('Success','Weekly Activity Report has been updated');
	// 			window.location.reload();
	// 		} else {
	// 			audio_error.play();
	// 			openErrorGritter('Error','Update Weekly Activity Report Failed');
	// 		}
	// 	});
	// }

	function printPdf(id,month) {
    	if (month == "") {
			alert('Pilih Bulan');
		}else{
			var url = "{{url('index/audit_kanban/print_audit_kanban/')}}";
			// // console.log(url + '/' + id+ '/' + month);
			window.open(url + '/' + id+ '/'+ month,"_blank");
		}
    }
</script>
@endsection