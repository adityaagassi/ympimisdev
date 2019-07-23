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
										<input type="text" class="form-control" id="valid_to" required="" readonly="">
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
										<select class="form-control select2" style="width: 100%;" data-placeholder="Choose Division" id="division" required onchange="changeDivision()">
										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Department<span class="text-red">*</span></label>
									<div class="col-sm-6" align="left">
										<select class="form-control select2" style="width: 100%;" data-placeholder="Choose Department" id="department" required onchange="changeDepartment()">
										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Section<span class="text-red">*</span></label>
									<div class="col-sm-6" align="left">
										<select class="form-control select2" style="width: 100%;" data-placeholder="Choose Section" id="section" required onchange="changeSection()">
										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Sub Section</label>
									<div class="col-sm-6" align="left">
										<select class="form-control select2" style="width: 100%;" data-placeholder="Choose Sub Section" id="subsection">
										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Group</label>
									<div class="col-sm-6" align="left">
										<select class="form-control select2" style="width: 100%;" data-placeholder="Choose Group" id="group">
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
										<input type="text" class="form-control datepicker" required="" id="valid_from" onchange="valid_change()">
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
	var division = [], department = [], section = [], sub_section = [], group = [], cost_center = [];

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
				get_mutation_by_id();
			}
		}
	});

	function valid_change() {
		var valid_from = $('#valid_from').val();
		var valid_to = new Date(valid_from);
		valid_to.setDate(valid_to.getDate() - 1);
		$('#valid_to').val(valid_to.getFullYear()+"-"+pad(valid_to.getMonth() + 1, 2)+"-"+pad(valid_to.getDate(), 2));
	}

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

					department = result.department;
					section = result.section;
					sub_section = result.sub_section;
					group = result.group;
					cost_center = result.cost_center;

					$("#division").append("<option disabled selected value=''>Choose Division</option>");
					$.each(result.devision, function(key, value) {
						var txt_division =  capitalize_Words(value.child_code);

						$("#division").append("<option value='"+value.child_code+"' name='"+value.status+"'>"+txt_division+"</option>");
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

	function changeDivision() {
		var cat = $("#division").find('option:selected').attr("name");

		$("#department").empty();
		$("#section").empty();
		$("#subsection").empty();
		$("#group").empty();
		$("#cc").empty();

		$("#department").append("<option disabled selected value=''>Choose Department</option>");

		$.each(department, function(key, value) {
			var txt_department =  capitalize_Words(value.child_code);
			if (value.parent_name == cat) {
				$("#department").append("<option value='"+value.child_code+"' name='"+value.status+"'>"+txt_department+"</option>");
			}
		});

		get_cost_center();
	}

	function changeDepartment() {
		var cat = $("#department").find('option:selected').attr("name");

		$("#section").empty();
		$("#subsection").empty();
		$("#group").empty();
		$("#cc").empty();

		$("#section").append("<option selected disabled value=''>Choose Section</option>");

		$.each(section, function(key, value) {
			var txt_section = capitalize_Words(value.child_code);		

			if (value.parent_name == cat) {
				$("#section").append("<option value='"+value.child_code+"' name='"+value.status+"'>"+txt_section+"</option>");
			}
		});

		get_cost_center();
	}

	function changeSection() {
		var cat = $("#section").find('option:selected').attr("name");

		$("#subsection").empty();
		$("#group").empty();
		$("#cc").empty();

		$("#subsection").append("<option selected disabled value=''>Choose Sub Section</option>");

		$.each(sub_section, function(key, value) {
			var txt_sub_section = capitalize_Words(value.child_code);		

			if (value.parent_name == cat) {
				$("#subsection").append("<option value='"+value.child_code+"' name='"+value.status+"'>"+txt_sub_section+"</option>");
			}
		});

		get_cost_center();
	}

	function changeSubSection() {
		var cat = $("#subsection").find('option:selected').attr("name");

		$("#group").empty();
		$("#cc").empty();

		$("#group").append("<option selected disabled value=''>Choose Group</option>");

		$.each(group, function(key, value) {
			var txt_group = capitalize_Words(value.child_code);		

			if (value.parent_name == cat) {
				$("#group").append("<option value='"+value.child_code+"' name='"+value.status+"'>"+txt_group+"</option>");
			}
		});

		get_cost_center();
	}

	function changeGroup() {
		var cat = $("#subsection").find('option:selected').attr("name");

		get_cost_center();
	}


	function get_cost_center() {
		$("#cc").empty();

		$("#cc").append("<option selected disabled value=''>Choose Cost Center</option>");
		$.each(cost_center, function(key, value) {
			$("#cc").append("<option value='"+value.cost_center+"'>"+value.cost_center+"</option>");
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
		var cc = $("#cc option:selected").val();
		var division =  $("#division option:selected").val();
		var department =  $("#department option:selected").val();
		var section =  $("#section option:selected").val();
		var subsection =  $("#subsection option:selected").val();
		var group =  $("#group option:selected").val();

		if(main_cc == cc && main_division == division && main_department == department && main_section == section) {
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
		else if (cc === "" || division === "" || section === "" || department === "") {
			openDangerGritter('Invalid','There\'s Empty Field');
			// return false;
		}
		else {
			console.log(division+" d "+department);
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
					openSuccessGritter('Success','Mutation Success');
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