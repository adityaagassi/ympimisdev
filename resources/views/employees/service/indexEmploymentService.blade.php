@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	th:hover {
		overflow: visible;
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
		border:1px solid black;
		vertical-align: middle;
		padding:5px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:5px;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	.dataTable > thead > tr > th[class*="sort"]:after{
		content: "" !important;
	}
	#queueTable.dataTable {
		margin-top: 0px!important;
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
{{-- <section class="content-header" style="padding-top: 0; padding-bottom: 0;"> --}}
	<h1>
		<span class="text-yellow">
			{{ $title }}
		</span>
		<small>
			<span style="color: #FFD700;"> {{ $title_jp }}</span>
		</small>
	</h1>
	<br>
{{-- </section> --}}
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px;">
	<div class="row">
		<div class="col-md-3">

			<!-- Profile Image -->
			<div class="box">
				<div class="box-body box-profile">
					<img class="profile-user-img img-responsive img-circle" src="../../dist/img/user4-128x128.jpg" alt="User profile picture">

					<h3 class="profile-username text-center">{{ $profil[0]->name }}</h3>

					<p class="text-muted text-center">{{ $emp_id }}</p>

					<ul class="list-group list-group-unbordered">
						@if ($profil[0]->position != "-")
						<li class="list-group-item" style="background-color: #605ca8">
							<center>
								<h4 style="margin:0px; font-weight: bold; color:white">{{$profil[0]->position}}</h4>
							</center>
						</li>
						@endif
						<li class="list-group-item">
							<b>Personal Leave Left</b> <a class="pull-right">
								<span class="label label-danger">5 / 12</span>
							</a>
						</li>
					</ul>
				</div>
				<!-- /.box-body -->
			</div>
			<!-- /.box -->

			<!-- About Me Box -->
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">About Me</h3>
				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<strong><i class="fa fa-briefcase margin-r-5"></i> Division</strong>

					<p class="text-muted">
						{{ $profil[0]->division }} - {{$profil[0]->department}} - {{$profil[0]->section}} - {{$profil[0]->sub_section}} - {{$profil[0]->group}}
					</p>

					<hr>

					<strong><i class="fa fa-cc margin-r-5"></i> Cost Center</strong>

					<p class="text-muted">{{$profil[0]->cost_center}}</p>

					<hr>

					<strong><i class="fa fa-calendar margin-r-5"></i> Join Date</strong>

					<p class="text-muted">{{$profil[0]->hire_date}}</p>

					<hr>

					<strong><i class="fa fa-star margin-r-5"></i> Grade</strong>

					<p class="text-muted">{{$profil[0]->grade_code}} - {{$profil[0]->grade_name}}</p>

				</div>
				<!-- /.box-body -->
			</div>
			<!-- /.box -->
		</div>
		<!-- /.col -->
		<div class="col-md-9">
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">Attendance and Overtime</h3>
					<div class="pull-right">
						<select class="form-control select2">
							<option>2019</option>
						</select>
					</div>
				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<table class="table table-bordered table-striped">
						<thead style="background-color: rgb(126,86,134); color: #FFD700;">
							<tr>
								<th style="width: 10%">Period</th>
								<th style="width: 10%">Absent</th>
								<th style="width: 10%">Permit</th>
								<th style="width: 10%">Sick</th>
								<th style="width: 10%">Late</th>
								<th style="width: 10%">Home Early</th>
								<th style="width: 10%">Personal Leave</th>
								<th style="width: 10%">Disiplinary Allowance</th>
								<th style="width: 10%">Overtime (hour)</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($absences as $data)
							<tr>
								<td>{{$data->period}}</td>
								<td>
									@if ($data->absent > 0) 
									<span class="badge bg-yellow">{{$data->absent}}</span>
									@else 
									- 
									@endif
								</td>
								<td>
									@if ($data->permit > 0) 
									<span class="badge bg-yellow">{{$data->permit}}</span>
									@else 
									- 
									@endif
								</td>
								<td>
									@if ($data->sick > 0)
									<span class="badge bg-yellow">{{$data->sick}}</span>
									@else 
									- 
									@endif
								</td>
								<td>@if ($data->late > 0)
									<span class="badge bg-yellow">{{$data->late}}</span>
									@else 
									- 
								@endif</td>
								<td>
									@if ($data->pc > 0)
									<span class="badge bg-yellow">{{$data->pc}}</span>
									@else 
									- 
									@endif
								</td>
								<td>
									@if ($data->personal_leave > 0) 
									<span class="badge bg-yellow">{{$data->personal_leave}}</span>
									@else 
									- 
									@endif
								</td>
								<td>
									@if ($data->dicipline > 0)
									<i class="fa fa-close" style="color: red"></i>
									@else 
									<i class="fa fa-check" style="color: #18c40c"></i>
									@endif
								</td>
								<td>
									@if ($data->overtime > 0)
									<span class="badge bg-yellow">{{$data->overtime}}</span>
									@else 
									- 
									@endif
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<!-- /.box-body -->
			</div>
		</div>
		<!-- /.col -->
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

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.select2').select2({
			language : {
				noResults : function(params) {
					return "There is no data";
				}
			}
		});

	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');


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