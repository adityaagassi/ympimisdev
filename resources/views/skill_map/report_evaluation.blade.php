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
		{{ $title }} <small><span class="text-purple">{{$title_jp}}</span></small>
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
			<div class="box box-solid">
				<div class="box-body">
				  <div class="row">
				  	<div class="col-xs-12">
						<div class="box-header">
							<h3 class="box-title">Print Hasil Evaluasi</h3>
						</div>
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="col-md-12 col-md-offset-3">
							<div class="col-md-3">
								<div class="form-group">
									<label>Bulan</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="date_from" name="date_from" autocomplete="off" placeholder="Choose a Date">
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
					</div>
				  </div>
				  <div class="row">
				    <div class="col-xs-12">
			          <table class="table table-bordered table-striped table-hover" id="tableEvaluation">
			            <thead style="background-color: rgba(126,86,134,.7);">
			              <tr>
			              	<th>#</th>
			                <th>Employee</th>
			                <th>Name</th>
			                <th>Skill Code</th>
			                <th>Skill Name</th>
			                <th>Process</th>
			                <th>Value From</th>
			                <th>Value To</th>
			                <th>Average</th>
			                <th>Action</th>
			              </tr>
			            </thead>
			            <tbody id="bodyTableEvaluation">
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

		$('body').toggleClass("sidebar-collapse");

		fetchData();
	});


	function fetchData(){
		// var tanggal_from = $('#tanggal_from').val();
		// var tanggal_to = $('#tanggal_to').val();
		var location = '{{$location}}';

		var data = {
			// tanggal_from:tanggal_from,
			// tanggal_to:tanggal_to,
			location:location
		}
		$.get('{{ url("fetch/report/skill_map_evaluation") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableEvaluation').DataTable().clear();
				$('#tableEvaluation').DataTable().destroy();
				$('#bodyTableEvaluation').html("");
				var tableData = "";
				var index = 1;
				$.each(result.datas, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ index +'</td>';
					tableData += '<td>'+ value.employee_id +'</td>';
					tableData += '<td>'+ value.name +'</td>';
					tableData += '<td>'+ value.skill_code +'</td>';
					tableData += '<td>'+ value.skill +'</td>';
					tableData += '<td>'+ value.process +'</td>';
					tableData += '<td>'+ value.from_value +'</td>';
					tableData += '<td>'+ value.to_value +'</td>';
					tableData += '<td>'+ value.average +'</td>';
					tableData += '<td><a href="{{ url("print/report/skill_map_evaluation") }}/'+location+'/'+value.evaluation_code+'" target="_blank" class="btn btn-primary"><i class="fa fa-print"></i> Print</a></td>';
					tableData += '</tr>';
					index++;
				});
				$('#bodyTableEvaluation').append(tableData);

				$('#tableEvaluation tfoot th').each( function () {
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
				});

				var table = $('#tableEvaluation').DataTable({
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

				$('#tableEvaluation tfoot tr').appendTo('#tableEvaluation thead');
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '2000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '2000'
		});
	}

	
</script>
  <script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
  <script src="{{ url("js/buttons.flash.min.js")}}"></script>
  <script src="{{ url("js/jszip.min.js")}}"></script>
  <script src="{{ url("js/vfs_fonts.js")}}"></script>
  <script src="{{ url("js/buttons.html5.min.js")}}"></script>
  <script src="{{ url("js/buttons.print.min.js")}}"></script>
@endsection
			