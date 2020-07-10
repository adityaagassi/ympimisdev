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
		{{$page}} - {{ $remark }} <small><span class="text-purple">?? ～ @if($remark == 'After Injection')
			成形上がり
		@else
			初物検査
		@endif </span></small>
		<!-- <small> <span class="text-purple">??</span></small> -->
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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Updating, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-body">
					<div class="col-xs-12">
						<div class="col-xs-3">
						</div>
						<div class="col-xs-6">
							<div class="box-header">
								<h3 class="box-title">Filter</h3>
							</div>
							<form role="form" method="post" action="{{url('index/recorder/filter_report_torque_check/'.$remark)}}">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="col-md-12">
									<div class="col-md-6">
										<div class="form-group">
											<label for="">Check Date From</label>
											<div class="input-group date">
												<div class="input-group-addon bg-white">
													<i class="fa fa-calendar"></i>
												</div>
												<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From" autocomplete="off">
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="">Check Date To</label>
											<div class="input-group date">
												<div class="input-group-addon bg-white">
													<i class="fa fa-calendar"></i>
												</div>
												<input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Date To" autocomplete="off">
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-12">
									<div class="col-md-6">
										<div class="form-group">
											<label for="">Mesin Middle</label>
											<select class="form-control select2" name="mesin_middle" data-placeholder="Select Mesin Middle" style="width: 100%;">
												<option value=""></option>
												@foreach($mesin as $mesin)
							                		<option value="{{$mesin}}">{{$mesin}}</option>
							                	@endforeach
											</select>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="">Mesin Head / Foot</label>
											<select class="form-control select2" name="mesin_head_foot" data-placeholder="Select Mesin Head / Foot" style="width: 100%;">
												<option value=""></option>
												@foreach($mesin2 as $mesin2)
							                		<option value="{{$mesin2}}">{{$mesin2}}</option>
							                	@endforeach
											</select>
										</div>
									</div>
								</div>
								<div class="col-md-12">
									<div class="col-md-6">
										<div class="form-group">
											<label for="">Judgement</label>
											<select class="form-control select2" multiple="multiple" id='judgementSelect' onchange="changeJudgement()" data-placeholder="Select Judgement" style="width: 100%;">
												<option value="OK">OK</option>
												<option value="NG">NG</option>
											</select>
											<input type="text" name="judgement" id="judgement" hidden>			
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="">Check Type</label>
											<select class="form-control select2" name='check_type' data-placeholder="Select Check Type" style="width: 100%;">
												<option value=""></option>
												<option value="HJ-MJ">HJ-MJ</option>
												<option value="MJ-FJ">MJ-FJ</option>
											</select>
										</div>
									</div>
								</div>
								<div class="col-md-12">
									<div class="col-md-12">
										<div class="form-group pull-right">
											@if($remark == 'After Injection')
											<a href="{{ url('index/recorder_process') }}" class="btn btn-warning">Back</a>
											@else
											<a href="{{ url('index/injeksi') }}" class="btn btn-warning">Back</a>
											@endif
											<a href="{{ url('index/recorder/report_torque_check/'.$remark) }}" class="btn btn-danger">Clear</a>
											<button type="submit" class="btn btn-primary col-sm-14">Search</button>
										</div>
									</div>
								</div>
							</form>
						</div>
						@if($role == 'F-SPL' || $role == 'L-Molding' || $role == 'L-RC' || $role == 'MIS' || $role == 'S')
						<div class="col-xs-3">
							<div class="box-header">
								<h3 class="box-title">Edit</h3>
							</div>
							<div class="col-md-12">
								<div class="form-group">
									<label for="">Check Code</label>
									<select class="form-control select2" name='check_code' id='check_code' data-placeholder="Select Check Code" style="width: 100%;">
										<option value=""></option>
										@foreach($id_gen as $id_gen)
											<option value="{{$id_gen->push_block_id_gen}}">{{$id_gen->push_block_id_gen}}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-md-12">
								<button class="btn btn-warning pull-right" onclick="edit_torque_all()">
									Edit
								</button>
							</div>
						</div>
						@endif
					</div>
					<div class="row">
						<div class="col-xs-12">
							<div class="box">
								<div class="box-body" style="overflow-x: scroll;">
									<table id="example1" class="table table-bordered table-striped table-hover">
										<thead style="background-color: rgba(126,86,134,.7);">
											<tr>
												<th>Check Date</th>
												<th>Check Type</th>
												<th>Injection Date Middle</th>
												<th>Mesin Middle</th>
												<th>Injection Date Head / Foot</th>
												<th>Mesin Head / Foot</th>
												<th>Product</th>
												<th>Middle</th>
												<th>Head / Foot</th>
												<th>Torque 1</th>
												<th>Torque 2</th>
												<th>Torque 3</th>
												<th>Average</th>
												<th>Judgement</th>
												<th>PIC</th>									
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php if (ISSET($torque_check)): ?>
												@foreach($torque_check as $torque_check)
												<tr>
													<td>{{ $torque_check->check_date }}</td>
													<td>{{ $torque_check->check_type }}</td>
													<td>{{ $torque_check->injection_date_middle }}</td>
													<td>{{ $torque_check->mesin_middle }}</td>
													<td>{{ $torque_check->injection_date_head_foot }}</td>
													<td>{{ $torque_check->mesin_head_foot }}</td>
													<td>{{ $torque_check->product_type }}</td>
													<td>{{ $torque_check->middle }}</td>
													<td>{{ $torque_check->head_foot }}</td>
													<td>{{ $torque_check->torque1 }}</td>
													<td>{{ $torque_check->torque2 }}</td>
													<td>{{ $torque_check->torque3 }}</td>
													<td>{{ $torque_check->torqueavg }}</td>
													<td>@if($torque_check->judgement == 'OK')
														<label class='label label-success'>{{ $torque_check->judgement }}</label>
													@else
														<label class='label label-danger'>{{ $torque_check->judgement }}</label>
													@endif</td>
													<td>{{ $torque_check->pic_check }}</td>
													<td><center>
															<button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit-modal" onclick="edit_torque('{{ url("index/recorder/update_torque") }}','{{ $torque_check->id }}');">
												               <i class="fa fa-edit"></i>
												            </button>
														</center>
													</td>
												</tr>
												@endforeach
											<?php endif ?>
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

<div class="modal fade" id="edit-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" align="center"><b>Edit Torque Check Recorder</b></h4>
      </div>
      <div class="modal-body">
      	<div class="box-body">
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	            <div class="form-group">
	              <input type="hidden" name="url_edit" id="url_edit" class="form-control">
	              <label for="">Check Date</label>
				  <input type="text" name="check_date" id="check_date" class="form-control" readonly required="required" title="" readonly>
	            </div>
	            <div class="form-group">
	              <label for="">Check Type</label>
				  <input type="text" name="check_type" id="check_type" class="form-control" readonly required="required" title="" readonly>
	            </div>
	            <div class="form-group">
	              <label for="">Injection Date Middle</label>
				  <input type="text" name="injection_date_middle" id="injection_date_middle" class="form-control" required="required" title="" readonly>
	            </div>
	            <div class="form-group">	              
	              <label for="">Mesin Middle</label>
				  <input type="text" name="mesin_middle" id="mesin_middle" class="form-control" readonly required="required" title="" readonly>
	            </div>
	            <div class="form-group">	              
	              <label for="">Injection Date Head / Foot</label>
				  <input type="text" name="injection_date_head_foot" id="injection_date_head_foot" class="form-control" readonly required="required" title="" readonly>
	            </div>
	            <div class="form-group">	              
	              <label for="">Mesin Head / Foot</label>
				  <input type="text" name="mesin_head_foot" id="mesin_head_foot" class="form-control" readonly required="required" title="" readonly>
	            </div>
	            <div class="form-group">	              
	              <label for="">Middle</label>
				  <input type="text" name="middle" id="middle" class="form-control" readonly required="required" title="" readonly>
	            </div>
	            <div class="form-group">	              
	              <label for="">Head / Foot</label>
				  <input type="text" name="head_foot" id="head_foot" class="form-control" readonly required="required" title="" readonly>
	            </div>	            
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            	<div class="form-group">	              
	              <label for="">PIC</label>
				  <input type="text" name="pic" id="pic" class="form-control" readonly required="required" title="" readonly>
	            </div>
	            <div class="form-group">	              
	              <label for="">Product</label>
				  <input type="text" name="product_type" id="product_type" class="form-control" readonly required="required" title="" readonly>
	            </div>
	            <div class="form-group">	              
	              <label for="">Torque 1</label>
				  <input type="text" name="torque1" id="torque1" class="form-control" required="required" title="" onkeyup="torque()">
	            </div>
	            <div class="form-group">	              
	              <label for="">Torque 2</label>
				  <input type="text" name="torque2" id="torque2" class="form-control" required="required" title="" onkeyup="torque()">
	            </div>
	            <div class="form-group">	              
	              <label for="">Torque 3</label>
				  <input type="text" name="torque3" id="torque3" class="form-control" required="required" title="" onkeyup="torque()">
	            </div>
	            <div class="form-group">	              
	              <label for="">Average</label>
				  <input type="text" name="torqueavg" id="torqueavg" class="form-control" required="required" title="" readonly>
	            </div>
	            <div class="form-group">	              
	              <label for="">Judgement</label>
				  <input type="text" name="judgementedit" id="judgementedit" class="form-control" readonly required="required" title="">
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

<div class="modal fade" id="edit-all-modal">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" align="center"><b>Edit Torque Recorder</b></h4>
      </div>
      <div class="modal-body">
      	<div class="box-body">
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            	<div class="form-group">
	              <label for="">Check Code</label>
				  <input type="text" name="check_code_all" id="check_code_all" class="form-control" readonly required="required" title="" readonly>
	            </div>
	            <div class="form-group">
	              <input type="hidden" name="url_edit" id="url_edit" class="form-control">
	              <label for="">Check Date</label>
				  <input type="text" name="check_date_all" id="check_date_all" class="form-control" readonly required="required" title="" readonly>
	            </div>
	            <div class="form-group">
	              <label for="">Check Type</label>
				  <input type="text" name="check_type_all" id="check_type_all" class="form-control" readonly required="required" title="" readonly>
	            </div>
	            <div class="form-group">	              
	              <label for="">Product</label>
				  <select class="form-control select2" name='product_type_all' id='product_type_all' data-placeholder="Select Product" style="width: 100%;">
					<option value=""></option>
					@foreach($product_type as $product_type)
						<option value="{{$product_type}}">{{$product_type}}</option>
					@endforeach
				  </select>
	            </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            	<div class="form-group">
	              <label for="">Injection Date Middle</label>
				  <input type="text" name="injection_date_middle_all" id="injection_date_middle_all" class="form-control datepicker" required="required" title="" placeholder="Injection Date Middle">
	            </div>
	            <div class="form-group">	              
	              <label for="">Mesin Middle</label>
	              <select class="form-control select2" name='mesin_middle_all' id='mesin_middle_all' data-placeholder="Select Mesin Middle" style="width: 100%;">
					<option value=""></option>
					@foreach($mesin3 as $mesin3)
						<option value="{{$mesin3}}">{{$mesin3}}</option>
					@endforeach
				  </select>
	            </div>
	            <div class="form-group">	              
	              <label for="">Injection Date Head / Foot</label>
				  <input type="text" name="injection_date_head_foot_all" id="injection_date_head_foot_all" class="form-control datepicker" required="required" title="" placeholder="Injection Date Head / Foot">
	            </div>
	            <div class="form-group">	              
	              <label for="">Mesin Head / Foot</label>
				  <select class="form-control select2" name='mesin_head_foot_all' id='mesin_head_foot_all' data-placeholder="Select Mesin Head / Foot" style="width: 100%;">
					<option value=""></option>
					@foreach($mesin4 as $mesin4)
						<option value="{{$mesin4}}">{{$mesin4}}</option>
					@endforeach
				  </select>
	            </div>
            </div>
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          	<div class="modal-footer">
	            <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
	            <input type="submit" value="Update" onclick="update_all()" class="btn btn-primary">
	          </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</section>
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
		$('body').toggleClass("sidebar-collapse");
	});
	$('.datepicker').datepicker({
		autoclose: true,
		format: "yyyy-mm-dd",
		autoclose: true,
		todayHighlight: true
	});

	function changeJudgement() {
		$("#judgement").val($("#judgementSelect").val());
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

	function edit_torque(url,id) {
    	$.ajax({
                url: "{{ route('recorder.get_torque') }}?id=" + id,
                method: 'GET',
                success: function(data) {
                  var json = data;
                  var data = data.data;
                  $("#url_edit").val(url+'/'+id);
                  $("#check_date").val(data.check_date);
                  $("#check_type").val(data.check_type);
                  $("#injection_date_middle").val(data.injection_date_middle);
                  $("#mesin_middle").val(data.mesin_middle);
                  $("#injection_date_head_foot").val(data.injection_date_head_foot);
                  $("#mesin_head_foot").val(data.mesin_head_foot);
                  $("#product_type").val(data.product_type);
                  $("#middle").val(data.middle);
                  $("#head_foot").val(data.head_foot);
                  $("#torque1").val(data.torque1);
                  $("#torque2").val(data.torque2);
                  $("#torque3").val(data.torque3);
                  $("#torqueavg").val(data.torqueavg);
                  $("#judgementedit").val(data.judgement);
                  $("#pic").val(data.pic_check);
                  if (data.judgement == 'NG') {
                  	document.getElementById('judgementedit').style.backgroundColor = "#ff4f4f";
                  	document.getElementById('judgementedit').style.color = "#fff";
                  }
                  else{
                  	document.getElementById('judgementedit').style.backgroundColor = "#7fff6e";
                  	document.getElementById('judgementedit').style.color = "#000";
                  }
                }
            });
      // jQuery('#formedit2').attr("action", url+'/'+interview_id+'/'+detail_id);
      // console.log($('#formedit2').attr("action"));
    }

    function torque() {
		var batas_bawah_hm = 15;
		var batas_atas_hm = 73;
		var batas_bawah_mf = 15;
		var batas_atas_mf = 78;

		var torque1 = document.getElementById('torque1').value;
		var torque2 = document.getElementById('torque2').value;
		var torque3 = document.getElementById('torque3').value;

		if (torque1 == "") {
			torque1 = 0;
		}
		if (torque2 == "") {
			torque2 = 0;
		}
		if (torque3 == "") {
			torque3 = 0;
		}

		var avg = (parseFloat(torque1)+parseFloat(torque2)+parseFloat(torque3))/3;

		var avg_id = '#torqueavg';
		var avg_id2 = 'torqueavg';
		var judgement_id = '#judgementedit';
		var judgement_id2 = 'judgementedit';

		if (parseFloat(avg) < parseFloat(batas_bawah_hm) || parseFloat(avg) > parseFloat(batas_atas_hm)) {
			document.getElementById(judgement_id2).style.backgroundColor = "#ff4f4f"; //red
			$(judgement_id).val('NG');
		}else{
			document.getElementById(judgement_id2).style.backgroundColor = "#7fff6e"; //green
			$(judgement_id).val('OK');
		}
		$(avg_id).val(avg.toFixed(2));
	}

	function update(){
		$('#loading').show();
		var torque1 = $('#torque1').val();
		var torque2 = $('#torque2').val();
		var torque3 = $('#torque3').val();
		var average = $('#torqueavg').val();
		var judgement = $('#judgement').val();
		var url = $('#url_edit').val();

		var data = {
			torque1:torque1,
			torque2:torque2,
			torque3:torque3,
			average:average,
			judgement:judgement,
		}
		// console.table(data);
		
		$.post(url, data, function(result, status, xhr){
			if(result.status){
				$("#edit-modal").modal('hide');
				$('#loading').hide();
				// $('#example1').DataTable().ajax.reload();
				// $('#example2').DataTable().ajax.reload();
				openSuccessGritter('Success','Torque Recorder Check has been updated');
				window.location.reload();
			} else {
				audio_error.play();
				openErrorGritter('Error','Update Torque Recorder Check Failed');
			}
		});
	}

	function edit_torque_all() {
		var id_gen = $('#check_code').val();
		var push_block_code = '{{$remark}}';
		if (id_gen == "") {
			alert('Pilih Kode Pengecekan');
		}else{
			$('#edit-all-modal').modal('show');
			var data = {
				push_block_id_gen:id_gen,
				push_block_code:push_block_code
			}

			$.get('{{ url("index/recorder/get_torque_all") }}', data, function(result, status, xhr){
				if(result.status){
					$('#check_code_all').val(result.data.push_block_id_gen);
					$('#check_date_all').val(result.data.check_date);
					$('#check_type_all').val(result.data.check_type);
					$('#injection_date_middle_all').val(result.data.injection_date_middle);
					$('#injection_date_head_foot_all').val(result.data.injection_date_head_foot);
					$('#mesin_middle_all').val(result.data.mesin_middle).trigger('change.select2');
					$('#mesin_head_foot_all').val(result.data.mesin_head_foot).trigger('change.select2');
					$('#product_type_all').val(result.data.product_type).trigger('change.select2');
				} else {
					audio_error.play();
					openErrorGritter('Error','Update Torque Recorder Check Failed');
				}
			});
		}
	}

	function update_all(){
		$('#loading').show();
		var remark = "{{$remark}}";
		var push_block_id_gen = $('#check_code_all').val();
		var injection_date_middle = $('#injection_date_middle_all').val();
		var injection_date_head_foot = $('#injection_date_head_foot_all').val();
		var mesin_middle = $('#mesin_middle_all').val();
		var mesin_head_foot = $('#mesin_head_foot_all').val();
		var product_type = $('#product_type_all').val();

		var data = {
			remark:remark,
			push_block_id_gen:push_block_id_gen,
			injection_date_middle:injection_date_middle,
			injection_date_head_foot:injection_date_head_foot,
			mesin_middle:mesin_middle,
			mesin_head_foot:mesin_head_foot,
			product_type:product_type,
		}
		// console.table(data);
		
		$.post('{{ url("index/recorder/update_torque_all") }}', data, function(result, status, xhr){
			if(result.status){
				$("#edit-all-modal").modal('hide');
				$('#loading').hide();
				// $('#example1').DataTable().ajax.reload();
				// $('#example2').DataTable().ajax.reload();
				openSuccessGritter('Success','Torque Recorder Check has been updated');
				window.location.reload();
			} else {
				audio_error.play();
				openErrorGritter('Error','Update Torque Recorder Check Failed');
			}
		});
	}
</script>
@endsection