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
		{{ $page }} <span class="text-purple">プレス機トラブルリポート</span>
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
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-body">
					<div class="col-xs-12">
						<div class="box-header">
							<h3 class="box-title">Filter</h3>
						</div>
						<form role="form" method="post" action="{{url('index/press/filter_report_trouble')}}">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="col-md-12 col-md-offset-3">
							<div class="col-md-3">
								<div class="form-group">
									<label>Date From</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="date_from" name="date_from" autocomplete="off" placeholder="Choose a Date">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>Date To</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="date_to" name="date_to" autocomplete="off" placeholder="Choose a Date">
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-md-offset-4">
							<div class="col-md-3">
								<div class="form-group pull-right">
									<a href="{{ url('index/initial/press') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/press/report_trouble') }}" class="btn btn-danger">Clear</a>
									<button type="submit" class="btn btn-primary col-sm-14">Search</button>
								</div>
							</div>
						</div>
						</form>
					</div>
				  <div class="row">
				    <div class="col-xs-12">
				      <div class="box">
				        <div class="box-body">
				          <table class="table table-bordered table-striped table-hover" id="example1">
				            <thead style="background-color: rgba(126,86,134,.7);">
				              <tr>
				              	<th>No</th>
				                <th>Employee</th>
				                <th>Date</th>
				                <th>Product</th>
				                <th>Material</th>
				                <th>Part</th>
				                <th>Process</th>
				                <th>Machine</th>
				                <th>Start Time</th>
				                <th>End Time</th>
				                <th>Reason</th>
				              </tr>
				            </thead>
				            <tbody id="tableTroubleList">
				            <?php $no = 1 ?>
				              @foreach($report_trouble as $report_trouble)
				              <tr>
				              	<td>{{ $no }}</td>
				                <td>{{$report_trouble->name}}</td>
				                <td>{{$report_trouble->date}}</td>
				                <td>{{$report_trouble->product}}</td>
				                <td>{{$report_trouble->material_number}}</td>
				                <td>{{$report_trouble->material_name}}</td>
				                <td>{{$report_trouble->process}}</td>
				                <td>{{$report_trouble->machine}}</td>
				                <td>{{$report_trouble->start_time}}</td>
				                <td>{{$report_trouble->end_time}}</td>
				                <td>{{$report_trouble->reason}}</td>
				              </tr>
				              <?php $no++ ?>
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
		$('#date_from').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			todayHighlight: true
		});
		$('#date_to').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			todayHighlight: true
		});
		$('.select2').select2({
			language : {
				noResults : function(params) {
					return "There is no date";
				}
			}
		});
	});

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
  <script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
  <script src="{{ url("js/buttons.flash.min.js")}}"></script>
  <script src="{{ url("js/jszip.min.js")}}"></script>
  <script src="{{ url("js/vfs_fonts.js")}}"></script>
  <script src="{{ url("js/buttons.html5.min.js")}}"></script>
  <script src="{{ url("js/buttons.print.min.js")}}"></script>
  <script>
    jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");
    });
    
    
  </script>
@endsection
			