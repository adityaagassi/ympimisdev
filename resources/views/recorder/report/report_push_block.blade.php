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
						<div class="col-xs-6">
							<div class="box-header">
								<h3 class="box-title">Filter</h3>
							</div>
							<form role="form" method="post" action="{{url('index/recorder/filter_report_push_block/'.$remark)}}">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="col-md-12">
									<div class="col-md-6">
										<div class="form-group">
											<label for="">Date From</label>
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
											<label for="">Date To</label>
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
									<div class="col-md-12">
										<div class="form-group">
											<label for="">Judgement</label>
											<select class="form-control select2" multiple="multiple" id='judgementSelect' onchange="changeJudgement()" data-placeholder="Select Judgement" style="width: 100%;">
												<option value="OK">OK</option>
												<option value="NG">NG</option>
											</select>
											<input type="text" name="judgement" id="judgement" hidden>			
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
											<a href="{{ url('index/recorder/report_push_block/'.$remark) }}" class="btn btn-danger">Clear</a>
											<button type="submit" class="btn btn-primary col-sm-14">Search</button>
										</div>
									</div>
								</div>
							</form>
						</div>
						<div class="col-xs-6">
							<!-- <div class="box-header">
								<h3 class="box-title">Save PDF</h3>
							</div>
							<form role="form" method="post" action="{{url('index/recorder/print_report_push_block/'.$remark)}}">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="col-md-12">
									<div class="col-md-6">
										<div class="form-group">
											<label for="">Date From</label>
											<div class="input-group date">
												<div class="input-group-addon bg-white">
													<i class="fa fa-calendar"></i>
												</div>
												<input type="text" class="form-control datepicker" id="date_from_print" name="date_from_print" placeholder="Select Date From" autocomplete="off" required>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="">Date To</label>
											<div class="input-group date">
												<div class="input-group-addon bg-white">
													<i class="fa fa-calendar"></i>
												</div>
												<input type="text" class="form-control datepicker" id="date_to_print" name="date_to_print" placeholder="Select Date To" autocomplete="off" required>
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-12">
								</div>
								<div class="col-md-12">
									<div class="col-md-12">
										<div class="form-group pull-right">
											<button type="submit" class="btn btn-primary col-sm-14">Save PDF</button>
										</div>
									</div>
								</div>
							</form> -->
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<div class="box">
								<div class="box-body">
									<table id="example1" class="table table-bordered table-striped table-hover">
										<thead style="background-color: rgba(126,86,134,.7);">
											<tr>
												<th>Check Date</th>
												<th>Injection Date</th>
												<th>Product</th>
												<th>Head</th>
												<th>Block</th>
												<th>Push Pull</th>
												<th>Judgement</th>
												<th>Ketinggian</th>
												<th>Judgement Ketinggian</th>
												<th>PIC</th>
												<th>Acrtion</th>
											</tr>
										</thead>
										<tbody>
											@foreach($push_block_check as $push_block_check)
											<tr>
												<td>{{ $push_block_check->check_date }}</td>
												<td>{{ $push_block_check->injection_date }}</td>
												<td>{{ $push_block_check->product_type }}</td>
												<td>{{ $push_block_check->head }}</td>
												<td>{{ $push_block_check->block }}</td>
												<td>{{ $push_block_check->push_pull }}</td>
												<td>{{ $push_block_check->judgement }}</td>
												<td>{{ $push_block_check->ketinggian }}</td>
												<td>{{ $push_block_check->judgement2 }}</td>
												<td>{{ $push_block_check->pic_check }}</td>
												<td><center>
														<button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit-modal" onclick="edit_push_block('{{ url("index/recorder/update") }}','{{ $push_block_check->id }}');">
											               <i class="fa fa-edit"></i>
											            </button>
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

<div class="modal fade" id="edit-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" align="center"><b>Edit Push Pull & Height Check Recorder</b></h4>
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
	              <label for="">Injection Date</label>
				  <input type="text" name="injection_date" id="injection_date" class="form-control" readonly required="required" title="" readonly>
	            </div>
	            <div class="form-group">	              
	              <label for="">Product</label>
				  <input type="text" name="product_type" id="product_type" class="form-control" readonly required="required" title="" readonly>
	            </div>
	            <div class="form-group">	              
	              <label for="">Head</label>
				  <input type="text" name="head" id="head" class="form-control" readonly required="required" title="" readonly>
	            </div>
	            <div class="form-group">	              
	              <label for="">Block</label>
				  <input type="text" name="block" id="block" class="form-control" readonly required="required" title="" readonly>
	            </div>	            
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            	<div class="form-group">	              
	              <label for="">PIC</label>
				  <input type="text" name="pic" id="pic" class="form-control" readonly required="required" title="" readonly>
	            </div>
	            <div class="form-group">	              
	              <label for="">Push Pull</label>
				  <input type="text" name="push_pull" id="push_pull" class="form-control" required="required" title="" onkeyup="push_pull()">
	            </div>
	            <div class="form-group">	              
	              <label for="">Judgement</label>
				  <input type="text" name="judgement_push_pull" id="judgement_push_pull" class="form-control" readonly required="required" title="" readonly>
	            </div>
	            <div class="form-group">	              
	              <label for="">Ketinggian</label>
				  <input type="text" name="ketinggian" id="ketinggian" class="form-control" required="required" title="" onkeyup="ketinggian()">
	            </div>
	            <div class="form-group">	              
	              <label for="">Judgement Ketinggian</label>
				  <input type="text" name="judgement2" id="judgement2" class="form-control" readonly required="required" title="" readonly>
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

	function edit_push_block(url,id) {
    	$.ajax({
                url: "{{ route('recorder.get_push_pull') }}?id=" + id,
                method: 'GET',
                success: function(data) {
                  var json = data;
                  var data = data.data;
                  $("#url_edit").val(url+'/'+id);
                  $("#check_date").val(data.check_date);
                  $("#injection_date").val(data.injection_date);
                  $("#product_type").val(data.product_type);
                  $("#head").val(data.head);
                  $("#block").val(data.block);
                  $("#push_pull").val(data.push_pull);
                  $("#judgement_push_pull").val(data.judgement);
                  $("#ketinggian").val(data.ketinggian);
                  $("#judgement2").val(data.judgement2);
                  $("#pic").val(data.pic_check);
                  if (data.judgement == 'NG') {
                  	document.getElementById('judgement_push_pull').style.backgroundColor = "#ff4f4f";
                  	document.getElementById('judgement_push_pull').style.color = "#fff";
                  }
                  else{
                  	document.getElementById('judgement_push_pull').style.backgroundColor = "#7fff6e";
                  	document.getElementById('judgement_push_pull').style.color = "#000";
                  }
                  if (data.judgement2 == 'NG') {
                  	document.getElementById('judgement2').style.backgroundColor = "#ff4f4f";
                  	document.getElementById('judgement2').style.color = "#fff";
                  }
                  else{
                  	document.getElementById('judgement2').style.backgroundColor = "#7fff6e";
                  	document.getElementById('judgement2').style.color = "#000";
                  }
                }
            });
      // jQuery('#formedit2').attr("action", url+'/'+interview_id+'/'+detail_id);
      // console.log($('#formedit2').attr("action"));
    }

    function push_pull() {
		var batas_bawah = '3';
		var batas_atas = '17';

		var x = document.getElementById('push_pull').value;
		if(x == ''){
			document.getElementById('push_pull').style.backgroundColor = "#ff4f4f";
		}
		else{
			document.getElementById('push_pull').style.backgroundColor = "#7fff6e";
		}
		if(parseFloat(x) < parseFloat(batas_bawah) || parseFloat(x) > parseFloat(batas_atas)){
			$('#judgement_push_pull').val('NG');
			document.getElementById('judgement_push_pull').style.backgroundColor = "#ff4f4f";
			document.getElementById('judgement_push_pull').style.color = "#fff";
		}
		else{
			$('#judgement_push_pull').val('OK');
			document.getElementById('judgement_push_pull').style.backgroundColor = "#7fff6e";
			document.getElementById('judgement_push_pull').style.color = "#000";
		}
	}

	function ketinggian() {
		var batas_tinggi = '0.2';

		var x = document.getElementById('ketinggian').value;
		if(x == ''){
			document.getElementById('ketinggian').style.backgroundColor = "#ff4f4f";
		}
		else{
			document.getElementById('ketinggian').style.backgroundColor = "#7fff6e";
		}
		if(parseFloat(x) > parseFloat(batas_tinggi)){
			$('#judgement2').val('NG');
			document.getElementById('judgement2').style.backgroundColor = "#ff4f4f";
			document.getElementById('judgement2').style.color = "#fff";
		}
		else{
			$('#judgement2').val('OK');
			document.getElementById('judgement2').style.backgroundColor = "#7fff6e";
			document.getElementById('judgement2').style.color = "#000";
		}
	}

	function update(){
		var push_pull = $('#push_pull').val();
		var judgement = $('#judgement_push_pull').val();
		var ketinggian = $('#ketinggian').val();
		var judgement2 = $('#judgement2').val();
		var url = $('#url_edit').val();

		var data = {
			push_pull:push_pull,
			judgement:judgement,
			ketinggian:ketinggian,
			judgement2:judgement2
		}
		// console.table(data);
		
		$.post(url, data, function(result, status, xhr){
			if(result.status){
				$("#edit-modal").modal('hide');
				// $('#example1').DataTable().ajax.reload();
				// $('#example2').DataTable().ajax.reload();
				openSuccessGritter('Success','Push Pull Recorder Check has been updated');
				window.location.reload();
			} else {
				audio_error.play();
				openErrorGritter('Error','Update Push Pull Recorder Check Failed');
			}
		});
	}
</script>
@endsection