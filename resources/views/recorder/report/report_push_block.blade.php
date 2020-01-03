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
		Report Push Block Check - {{ $remark }} <span class="text-purple">??</span>
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
</script>
@endsection