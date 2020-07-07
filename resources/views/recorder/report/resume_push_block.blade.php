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
		{{$page}} - {{ $remark }} <small><span class="text-purple">プッシュブロック検査のまとめ ～ @if($remark == 'After Injection')
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
							<form role="form" method="post" action="{{url('index/recorder/filter_resume_push_block/'.$remark)}}">
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
											<label for="">Mesin Head</label>
											<select class="form-control select2" id='mesin_head' name="mesin_head" data-placeholder="Select Mesin Head" style="width: 100%;">
												<option value=""></option>
												@foreach($mesin3 as $mesin3)
							                		<option value="{{$mesin3}}">{{$mesin3}}</option>
							                	@endforeach
											</select>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="">Mesin Block</label>
											<select class="form-control select2" name="mesin_block" id='mesin_block'data-placeholder="Select Mesin Block" style="width: 100%;">
												<option value=""></option>
												@foreach($mesin4 as $mesin4)
							                		<option value="{{$mesin4}}">{{$mesin4}}</option>
							                	@endforeach
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
											<a href="{{ url('index/recorder/resume_push_block/'.$remark) }}" class="btn btn-danger">Clear</a>
											<button type="submit" class="btn btn-primary col-sm-14">Search</button>
										</div>
									</div>
								</div>
							</form>
						</div>
						<div class="col-xs-3">
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<div class="box">
								<div class="box-body" style="overflow-x: scroll;">
									<table id="example1" class="table table-bordered table-striped table-hover">
										<thead style="background-color: rgba(126,86,134,.7);">
											<tr>
												<th>Check Date</th>
												<th>Injection Date Head</th>
												<th>Mesin Head</th>
												<th>Injection Date Block</th>
												<th>Mesin Block</th>
												<th>Product</th>
												<th>Head</th>
												<th>Block</th>
												<th>Push Pull Judgement</th>
												<th>Height Judgement</th>
												<th>PIC</th>
												<th>Notes</th>
												@if($auth == 'F-SPL' || $auth == 'L-Molding' || $auth == 'MIS' || $auth == 'S')
												<th>Action</th>
												@endif
											</tr>
										</thead>
										<tbody>
											@foreach($push_block_check as $push_block_check)
											<tr>
												<td>{{ $push_block_check->check_date }}</td>
												<td>{{ $push_block_check->injection_date_head }}</td>
												<td>{{ $push_block_check->mesin_head }}</td>
												<td>{{ $push_block_check->injection_date_block }}</td>
												<td>{{ $push_block_check->mesin_block }}</td>
												<td>{{ $push_block_check->product_type }}</td>
												<td>{{ $push_block_check->head }}</td>
												<td>{{ $push_block_check->block }}</td>
												<td><?php if($push_block_check->push_pull_ng_name != 'OK'){
														$push_pull_ng_name = explode(',', $push_block_check->push_pull_ng_name);
														$push_pull_ng_value = explode(',', $push_block_check->push_pull_ng_value);
														for ($i=0; $i < count($push_pull_ng_name); $i++) { 
															echo "Head-Block = ".$push_pull_ng_name[$i]." Memiliki Nilai NG = <label class='label label-danger' readonly>".$push_pull_ng_value[$i]."</label><br>";
														}
													}else{
														echo "<label class='label label-success'>".$push_block_check->push_pull_ng_name."</label>";
													} ?>
												</td>
												<td>
													<?php if($push_block_check->height_ng_name != 'OK'){
														$height_ng_name = explode(',', $push_block_check->height_ng_name);
														$height_ng_value = explode(',', $push_block_check->height_ng_value);
														for ($i=0; $i < count($height_ng_name); $i++) { 
															echo "Head-Block = ".$height_ng_name[$i]." Memiliki Nilai NG = <label class='label label-danger' readonly>".$height_ng_value[$i]."</label><br>";
														}
													}else{
														echo "<label class='label label-success'>".$push_block_check->height_ng_name."</label>";
													} ?>
												</td>
												<td>{{ $push_block_check->pic_check }}</td>
												<td>{{ $push_block_check->notes }}</td>
												@if($auth == 'F-SPL' || $auth == 'L-Molding' || $auth == 'MIS' || $auth == 'S')
												<td>
													<center>
														<button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit-modal" onclick="edit_resume('{{ url("index/recorder/update_resume") }}','{{ $push_block_check->id }}','{{ $push_block_check->push_block_id_gen }}');">
											               Edit
											            </button>
													</center>
												</td>
												@endif
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
												@if($auth == 'F-SPL' || $auth == 'L-Molding' || $auth == 'MIS' || $auth == 'S')
												<th></th>
												@endif
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
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" align="center"><b>Edit Resume Push Block Check - {{$remark}}</b></h4>
      </div>
      <div class="modal-body">
      	<div class="box-body">
        <div>
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            	<input type="hidden" name="url_edit" id="url_edit" class="form-control">
            	<input type="hidden" name="push_block_id_gen" id="push_block_id_gen" class="form-control">
	            <div class="form-group">
	              <label for="">Check Date</label>
				  <input type="text" name="check_date" id="check_date" class="form-control" value="" readonly required="required" title="" placeholder="Check Date">
	            </div>
	            <div class="form-group">
	              <label for="">Injection Date Head</label>
				  <input type="text" name="injection_date_head" id="injection_date_head" class="form-control datepicker" required="required" title="" placeholder="Injection Date Head">
	            </div>
	            <div class="form-group">
	             <label>Mesin Head<span class="text-red">*</span></label>
	                <select class="form-control" name="mesin_head" id="mesin_head" style="width: 100%;" data-placeholder="Choose a Mesin Head..." required>
	                	@foreach($mesin as $mesin)
	                		<option value="{{$mesin}}">{{$mesin}}</option>
	                	@endforeach
	                </select>
	            </div>
	            <div class="form-group">
	              <label for="">Injection Date Block</label>
				  <input type="text" name="injection_date_block" id="injection_date_block" class="form-control datepicker" required="required" title="" placeholder="Injection Date Block">
	            </div>
	            <div class="form-group">
	             <label>Mesin Block<span class="text-red">*</span></label>
	                <select class="form-control" name="mesin_block" id="mesin_block" style="width: 100%;" data-placeholder="Choose a Mesin Block..." required>
	                	@foreach($mesin2 as $mesin)
	                		<option value="{{$mesin}}">{{$mesin}}</option>
	                	@endforeach
	                </select>
	            </div>
	            <div class="form-group">
	             <label>Product<span class="text-red">*</span></label>
	                <select class="form-control" name="product_type" id="product_type" style="width: 100%;" data-placeholder="Choose a Mesin Block..." required>
	                	@foreach($product_type as $product_type)
	                		<option value="{{$product_type}}">{{$product_type}}</option>
	                	@endforeach
	                </select>
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

	function edit_resume(url,id,push_block_id_gen) {
    	$.ajax({
                url: "{{ url('index/recorder/get_resume') }}?id=" + id,
                method: 'GET',
                success: function(data) {
                  var json = data;
                  var datas = data.data;
                  $("#url_edit").val(url+'/'+id);
                  $("#push_block_id_gen").val(push_block_id_gen);
                  $("#check_date").val(datas[0].check_date);
                  $("#injection_date_head").val(datas[0].injection_date_head);
                  $("#injection_date_block").val(datas[0].injection_date_block);
                  $("#mesin_head").val(datas[0].mesin_head).trigger('change.select');
                  $("#mesin_block").val(datas[0].mesin_block).trigger('change.select');
                  $("#product_type").val(datas[0].product_type).trigger('change.select');
                }
            });
    }

    function update(){
		var injection_date_head = $('#injection_date_head').val();
		var injection_date_block = $('#injection_date_block').val();
		var mesin_head = $('#mesin_head').val();
		var mesin_block = $('#mesin_block').val();
		var product_type = $('#product_type').val();
		var url = $('#url_edit').val();
		var push_block_id_gen = $('#push_block_id_gen').val();
		var check_date = $('#check_date').val();
		var remark = '{{$remark}}';

		var data = {
			injection_date_head:injection_date_head,
			injection_date_block:injection_date_block,
			mesin_head:mesin_head,
			mesin_block:mesin_block,
			product_type:product_type,
			push_block_id_gen:push_block_id_gen,
			remark:remark,
			check_date:check_date
		}
		
		$.post(url, data, function(result, status, xhr){
			if(result.status){
				$("#edit-modal").modal('hide');
				openSuccessGritter('Success','Resume Push Block Check has been updated');
				window.location.reload();
			} else {
				audio_error.play();
				openErrorGritter('Error','Update Resume Push Block Check Failed');
			}
		});
	}
</script>
@endsection