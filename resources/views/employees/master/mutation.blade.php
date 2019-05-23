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
  	Mutation<span class="text-purple"> </span>
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
							<button type="button" class="btn btn-primary btn-flat" onclick="get_mutation_by_id();"><i class="fa fa-search"></i></button>
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
									<label class="col-sm-4">Division</label>
									<div class="col-sm-6" align="left">
										<input type="text" id="division_old" class="form-control" disabled>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Department</label>
									<div class="col-sm-6" align="left">
										<input type="text" id="department_old" class="form-control" disabled>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Section</label>
									<div class="col-sm-6" align="left">
										<input type="text" id="section_old" class="form-control" disabled>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Sub Section</label>
									<div class="col-sm-6" align="left">
										<input type="text" id="subsection_old" class="form-control" disabled>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Group</label>
									<div class="col-sm-6" align="left">
										<input type="text" id="group_old" class="form-control" disabled>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Cost Center</label>
									<div class="col-sm-6" align="left">
										<input type="text" id="cc_old" class="form-control" disabled>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Valid From</label>
									<div class="col-sm-6">
										<input type="text" class="form-control datepicker" disabled id="valid_from_old">
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Valid To<span class="text-red">*</span></label>
									<div class="col-sm-6">
										<input type="text" class="form-control datepicker" id="valid_to" required="">
									</div>
								</div>
							</div>

							{{-- NEW --}}
							<div class="col-md-6">
								<center>NEW</center>
								<br>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Division<span class="text-red">*</span></label>
									<div class="col-sm-6" align="left">
										<select class="form-control select2" style="width: 100%;" data-placeholder="Choose Division" id="division" required>
										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Department<span class="text-red">*</span></label>
									<div class="col-sm-6" align="left">
										<select class="form-control select2" style="width: 100%;" data-placeholder="Choose Department" id="department" required>
										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Section<span class="text-red">*</span></label>
									<div class="col-sm-6" align="left">
										<select class="form-control select2" style="width: 100%;" data-placeholder="Choose Section" id="section" required>
										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Sub Section<span class="text-red">*</span></label>
									<div class="col-sm-6" align="left">
										<select class="form-control select2" style="width: 100%;" data-placeholder="Choose Sub Section" id="subsection" required>
										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Group<span class="text-red">*</span></label>
									<div class="col-sm-6" align="left">
										<select class="form-control select2" style="width: 100%;" data-placeholder="Choose Group" id="group" required>
										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Cost Center<span class="text-red">*</span></label>
									<div class="col-sm-6" align="left">
										<select class="form-control select2" style="width: 100%;" data-placeholder="Choose Cost Center" id="cc" required>
										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Valid From<span class="text-red">*</span></label>
									<div class="col-sm-6">
										<input type="text" class="form-control datepicker" required="" id="valid_from">
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Reason<span class="text-red">*</span></label>
									<div class="col-sm-6">
										<textarea id="reason" class="form-control" required id="reason"></textarea>
									</div>
								</div>

								<div class="col-sm-2 col-sm-offset-6">
									<div class="btn-group pull-right">
										<button class="btn btn-success" onclick="cek()"><i class="fa fa-check"></i> Save</button>
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
	var main_cc = "";
	var main_division = "";
	var main_department = "";
	var main_section = "";
	var main_subsection = "";
	var main_group = "";

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	jQuery(document).ready(function() {
		// $('body').toggleClass("sidebar-collapse");
		$('.select2').select2();	
	});

	$('#emp_id').keypress(function(event){

		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
			var len = $("#emp_id").val().length;
			if (len < 8) {
				alert('Input not Valid');
			}
			else {
				get_mutation_by_id();
			}
		}
	});

	function get_mutation_by_id() {
		var emp_id = $('#emp_id').val();

		var data = {
			emp_id:emp_id
		}
		$.get('{{ url("fetch/mutation") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					$('#isi').css({"visibility":"visible"});
					main_cc = result.mutation_logs.cost_center;
					main_division = result.mutation_logs.division;
					main_department = result.mutation_logs.department;
					main_section = result.mutation_logs.section;
					main_subsection = result.mutation_logs.sub_section;
					main_group = result.mutation_logs.group;

					$("#id").val(result.mutation_logs.employee_id);	

					$("#name").val(result.mutation_logs.name);
					$("#division_old").val(main_division);
					$("#department_old").val(main_department);
					$("#section_old").val(main_section);
					$("#subsection_old").val(main_subsection);
					$("#group_old").val(main_group);
					$("#cc_old").val(main_cc);
					$("#valid_from_old").val(result.mutation_logs.valid_from);

					$("#division").empty();
					$.each(result.devision, function(key, value) {
						var txt_division =  capitalize_Words(value.child_code);
						if(value.child_code == main_division)
							$("#division").append("<option value='"+value.child_code+"' selected>"+txt_division+"</option>");
						else
							$("#division").append("<option value='"+value.child_code+"'>"+txt_division+"</option>");
					});

					$("#department").empty();
					$.each(result.department, function(key, value) {
						var txt_department =  capitalize_Words(value.child_code);
						if(value.child_code == main_department)
							$("#department").append("<option value='"+value.child_code+"' selected>"+txt_department+"</option>");
						else
							$("#department").append("<option value='"+value.child_code+"'>"+txt_department+"</option>");
					});

					$("#section").empty();
					$.each(result.section, function(key, value) {
						var txt_section =  capitalize_Words(value.child_code);
						if(value.child_code == main_section)
							$("#section").append("<option value='"+value.child_code+"' selected>"+txt_section+"</option>");
						else
							$("#section").append("<option value='"+value.child_code+"'>"+txt_section+"</option>");
					});

					$("#subsection").empty();
					$.each(result.sub_section, function(key, value) {
						var txt_section =  capitalize_Words(value.child_code);
						if(value.child_code == main_subsection)
							$("#subsection").append("<option value='"+value.child_code+"' selected>"+txt_section+"</option>");
						else
							$("#subsection").append("<option value='"+value.child_code+"'>"+txt_section+"</option>");
					});

					$("#group").empty();
					$.each(result.group, function(key, value) {
						var txt_group =  capitalize_Words(value.child_code);
						if(value.child_code == main_group)
							$("#group").append("<option value='"+value.child_code+"' selected>"+txt_group+"</option>");
						else
							$("#group").append("<option value='"+value.child_code+"'>"+txt_group+"</option>");
					});

					$("#cc").empty();
					$.each(result.cost_center, function(key, value) {
						if(value.cost_center == main_cc)
							$("#cc").append("<option value='"+value.cost_center+"' selected>"+value.cost_center+"</option>");
						else
							$("#cc").append("<option value='"+value.cost_center+"'>"+value.cost_center+"</option>");
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

	function capitalize_Words(str)
	{
		return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
	}


	function cek() {
		var emp_id = $('#id').val();
		var reason = $('#reason').val();
		var valid_from = $('#valid_from').val();
		var valid_to = $('#valid_to').val();
		var cc =  $("#cc option:selected").val();
		var division =  $("#division option:selected").val();
		var department =  $("#department option:selected").val();
		var section =  $("#section option:selected").val();
		var subsection =  $("#subsection option:selected").val();
		var group =  $("#group option:selected").val();

		if(main_cc == cc && main_division == division && main_department == department && main_section == section && main_subsection == subsection && main_group == group) {
			openDangerGritter('Invalid','Nothing Changed');
		}
		else if (reason == "") {
			openDangerGritter('Invalid','Reason Cannot Empty');
		}
		else if (valid_from == "") {
			openDangerGritter('Invalid','Valid From Cannot Empty');
		}
		else if (valid_to == "") {
			openDangerGritter('Invalid','Valid To Cannot Empty');
		}
		else {
			main_cc = cc;
			main_division = division;
			main_department = department;
			main_section = section;
			main_subsection = subsection;
			main_group = group;

			do_mutation(emp_id, cc, division, department, section, subsection, group, valid_from, valid_to);
		}
	}

	function do_mutation(emp_id, cc, division, department, section, subsection, group, valid_from, valid_to) {
		var reason = $.trim($("#reason").val());

		var data = {
			emp_id:emp_id,
			cc:cc,
			division:division,
			department:department,
			section:section,
			subsection:subsection,
			group:group,
			reason:reason,
			valid_from:valid_from,
			valid_to:valid_to
		}

		$.get('{{ url("change/mutation") }}', data, function(result, status, xhr){
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