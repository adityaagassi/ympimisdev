@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style type="text/css">
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
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small>
			<span> {{ $title_jp }}</span>
		</small>
	</h1>
</section>
@endsection

@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-xs-12">
						<table id="table_resume" class="table table-bordered">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>Employee_Id</th>
									<th>Employee_Name</th>
									<th>Position</th>
									<th>Department</th>
									<th>Section</th>
									<th>Unverified Kaizen</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($datas as $key): ?>
									<tr>
										<td><?php echo $key->employee_id ?></td>
										<td><?php echo $key->name ?></td>
										<td><?php echo $key->position ?></td>
										<td><?php echo $key->department ?></td>
										<td><?php echo $key->section ?></td>
										<td><a href="{{ url('index/kaizen/'.$key->section) }}"><?php echo $key->count ?></a></td>
									</tr>
								<?php endforeach ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</section>
	@endsection

	@section('scripts')
	<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
	<script src="{{ url("js/bootstrap-toggle.min.js") }}"></script>
	<script>

		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		var total1 = 0, total2 = 0, total3 = 0;
		var kode = "";

		jQuery(document).ready(function() {
			$('body').toggleClass("sidebar-collapse");

			$('#table_resume').DataTable({
				'dom': 'Bfrtip',
				'responsive': true,
				'lengthMenu': [
				[ 10, 25, 50, -1 ],
				[ '10 rows', '25 rows', '50 rows', 'Show all' ]
				],
				'paging': true,
				'lengthChange': true,
				'searching': true,
				'ordering': true,
				'order': [],
				'info': true,
				'autoWidth': true,
				"sPaginationType": "full_numbers"
			});

			var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
		});


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

	</script>
	@endsection
