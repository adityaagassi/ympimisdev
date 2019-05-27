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
	.disabledTab{
		pointer-events: none;
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		List of Double Overtime	<span class="text-purple"> </span>
		{{-- <small>WIP Control <span class="text-purple"> 仕掛品管理</span></small> --}}
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-body">
					<div class="col-md-2 pull-right">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border-color: green">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="bulan" onchange="createTable()" placeholder="Select date" style="border-color: green" value="{{date('Y-m')}}">
						</div>
					</div>
					<br>
					<br>

					<table class="table table-hover table-bordered" style="width: 100%" id="overtime_double">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th>OT ID</th>
								<th>Date</th>
								<th>Employee ID</th>
								<th>Name</th>
								<th>Section</th>
								<th>Sub Section</th>
								<th>OT Start</th>
								<th>OT End</th>
								<th>OT Hour</th>
								<th>Status</th>
								<th>Action</th>	
							</tr>
						</thead>
						<tbody>
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
</section>



@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var table = $('#overtime_double').DataTable();

	jQuery(document).ready(function() {
		createTable();
	});

	function createTable(){
		table.destroy();
		var bulan = $("#bulan").val();
		$('#overtime_double tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
		});
		table = $('#overtime_double').DataTable({
			'dom': 'rtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			'paging'        : true,
			'lengthChange'  : true,
			'searching'     : true,
			'ordering'      : false,
			'info'        : true,
			'order'       : [],
			'autoWidth'   : true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"ajax": {
				"type" : "post",
				"url" : "{{ url("fetch/double") }}",
				"data" : {'bulan': bulan}
			},
			"columns": [
			{ "data": "id_ot"},
			{ "data": "tanggal" ,"width": "10%"},
			{ "data": "nik"},
			{ "data": "namaKaryawan", "width": "30%"},
			{ "data": "section"},
			{ "data": "sub_sec"},
			{ "data": "dari"},
			{ "data": "sampai"},
			{ "data": "jam", "width": "1%" },
			{ "data": "stat"},
			{ "data": "action"}
			]
		});

		table.columns().every( function () {
			var that = this;

			$( 'input', this.footer() ).on( 'keyup change', function () {
				if ( that.search() !== this.value ) {
					that
					.search( this.value )
					.draw();
				}
			});
		});

		$('#overtime_double tfoot tr').appendTo('#overtime_double thead');
	}

	function delete_emp(id) {
		var str = id.split("+");
		if(confirm('Are you sure want to delete employee id \''+str[1]+'\' from Overtime \''+str[0]+'\' ?')){

			var data = {
				id_ot: str[0],
				nik: str[1]
			}

			$.post('{{ url("delete/overtime_confirmation") }}', data, function(result, status, xhr){
				if(xhr.status == 200){
					if(result.status){
						openSuccessGritter('Success', result.message);
						$('#overtime_double').DataTable().ajax.reload();
					}
					else {
						audio_error.play();
						openErrorGritter('Error!', result.message);
					}
				}
				else{
					audio_error.play();
					alert("Disconnected from server");
				}
			});
		}
	}

	function openDangerGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '3000'
		});
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

	$('.datepicker').datepicker({
		autoclose: true,
		format: "yyyy-mm",
		viewMode: "months", 
		minViewMode: "months",
		todayHighlight: true
	});

</script>
@endsection