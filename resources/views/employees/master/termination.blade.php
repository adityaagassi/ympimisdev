@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
@stop
@section('header')
<section class="content-header">
{{-- 	 @if (session('status'))
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
    {{ session('status') }}
  </div>   
  @endif --}}
  <h1>
  	Termination<span class="text-purple"> </span>
  	{{-- <small>WIP Control <span class="text-purple"> 仕掛品管理</span></small> --}}
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
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-body">
					<div class="input-group input-group-lg" style="margin: 10px 300px 0 300px">
						<input type="text" class="form-control text-center" placeholder="Please Enter Employee ID" id="emp_id">
						<span class="input-group-btn">
							<button type="button" class="btn btn-primary btn-flat" onclick="get_position_by_id();"><i class="fa fa-search"></i></button>
						</span>
					</div>
					<hr>
					<div style="visibility: hidden;" id="isi">
						<div class="row">
							<div class="col-md-5">
								<div class="form-group row" align="right">
									<label class="col-sm-6">Employee ID</label>
									<div class="col-sm-6">
										<input type="text" id="id" class="form-control" readonly="">
									</div>
								</div>
							</div>
							<div class="col-md-5">
								<div class="form-group row" align="right">
									<label class="col-sm-6">Employee Name</label>
									<div class="col-sm-6">
										<input type="text" id="name" class="form-control" readonly="">
									</div>
								</div>
							</div>
						</div>
						<br>
						<div class="row">
							{{-- OLD --}}
							<div class="col-md-6">
								<center>OLD</center>
								<br>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Grade</label>
									<div class="col-sm-6"  align="left">
										<input type="text" id="grade_old" class="form-control" readonly="">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Position</label>
									<div class="col-sm-6" align="left">
										<input type="text" id="position_old" class="form-control" readonly="">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Valid From</label>
									<div class="col-sm-6">
										<input type="text" id="valid_from_old" class="form-control" readonly="">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Valid To<span class="text-red">*</span></label>
									<div class="col-sm-6">
										<input type="text" id="valid_to" class="form-control" required="" readonly="">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<center>NEW</center>
								<br>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Grade<span class="text-red">*</span></label>
									<div class="col-sm-6"  align="left">
										<select id="grade" class="form-control select2">
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Position<span class="text-red">*</span></label>
									<div class="col-sm-6" align="left">
										<select id="position" class="form-control select2">
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Valid From<span class="text-red">*</span></label>
									<div class="col-sm-6">
										<input type="text" id="valid_from" class="form-control datepicker"
										onchange="valid_change()">
									</div>
								</div>
								<br>
								<br>
								<div class="col-sm-2 col-sm-offset-6">
									<div class="btn-group pull-right">
										<button class="btn btn-success pull-right" onclick="cek()"><i class="fa fa-check"></i> Save</button>
									</div>
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
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	var main_grade_code = "";
	var main_grade_name = "";
	var main_position = "";

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	jQuery(document).ready(function() {
		// $('body').toggleClass("sidebar-collapse");
		$('.select2').select2();
	});

	function pad (str, max) {
		str = str.toString();
		return str.length < max ? pad("0" + str, max) : str;
	}

	$('#emp_id').keypress(function(event){

		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
			var len = $("#emp_id").val().length;
			if (len < 8) {
				alert('Input not Valid');
			}
			else {
				get_position_by_id();
			}
		}
	});

	function valid_change() {
		var valid_from = $('#valid_from').val();
		var valid_to = new Date(valid_from);
		valid_to.setDate(valid_to.getDate() - 1);
		$('#valid_to').val(valid_to.getFullYear()+"-"+pad(valid_to.getMonth() + 1, 2)+"-"+pad(valid_to.getDate(), 2))
	}

	function get_position_by_id() {
		var emp_id = $('#emp_id').val();

		var data = {
			emp_id:emp_id
		}
		$.get('{{ url("fetch/promotion") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					$('#isi').css({"visibility":"visible"});
					main_grade_code = result.promotion_logs.grade_code;
					main_grade_name = result.promotion_logs.grade_name;
					main_position = result.promotion_logs.position;

					$("#id").val(result.promotion_logs.employee_id);
					$("#name").val(result.promotion_logs.name);
					$("#grade_old").val("[ "+main_grade_code+" ] "+main_grade_name);
					$("#position_old").val(main_position);
					$("#valid_from_old").val(result.promotion_logs.valid_from);

					$("#grade").empty();
					$.each(result.grades, function(key, value) {
						if(value.grade_code == main_grade_code && value.grade_name == main_grade_name)
							$("#grade").append("<option value='"+value.grade_code+"#"+value.grade_name+"' selected>[ "+value.grade_code+" ] "+value.grade_name+"</option>");
						else
							$("#grade").append("<option value='"+value.grade_code+"#"+value.grade_name+"'>[ "+value.grade_code+" ] "+value.grade_name+"</option>");
					});

					$("#position").empty();
					$.each(result.positions, function(key, value) {
						if(main_position == value.position)
							$("#position").append("<option value='"+value.position+"' selected>"+value.position+"</option>");
						else
							$("#position").append("<option value='"+value.position+"'>"+value.position+"</option>");
					});
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				alert('Disconnected from server');
			}
		});
	}

	function cek() {
		var emp_id = $('#id').val();
		var posisi =  $("#position option:selected").val();
		var grade =  $("#grade option:selected").val();
		var valid_from =  $("#valid_from").val();
		var valid_to =  $("#valid_to").val();
		var mainGrade = main_grade_code+"#"+main_grade_name;
		if(main_position == posisi && grade == mainGrade) {
			openDangerGritter('Invalid','Nothing Changed');
		}
		else if (valid_from == ""){
			openDangerGritter('Invalid','Valid From Cannot Changed');
		}
		else if (valid_to == ""){
			openDangerGritter('Invalid','Valid To Cannot Changed');
		}
		else {
			main_position = posisi;
			var str = grade.split('#');
			main_grade_code = str[0];
			main_grade_name = str[1];
			change_position(emp_id, grade, posisi);
		}
	}

	function change_position(emp_id, grade, posisi) {
		var valid_from = $("#valid_from").val();
		var valid_to = $("#valid_to").val();
		var data = {
			emp_id:emp_id,
			grade:grade,
			position:posisi,
			valid_from:valid_from,
			valid_to:valid_to
		}

		$.get('{{ url("change/promotion") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){					
					openSuccessGritter('Success','Promotion Success');
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				alert('Disconnected from server');
			}
		});
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
		format: 'yyyy-mm-dd',
		todayHighlight: true
	});

</script>
@endsection