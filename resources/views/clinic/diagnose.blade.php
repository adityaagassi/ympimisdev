@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		font-size: 16px;
	}
	#tableBodyList > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}
	#loading { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple"> {{ $title_jp }}</span></small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-body">
					<span style="font-size: 20px; font-weight: bold;">Patient List</span>
					<table class="table table-hover" id="tableList">
						<thead>
							<tr>
								<th style="width: 5%;">#</th>
								<th style="width: 15%; text-align: center;">In Time</th>
								<th style="width: 15%; text-align: center;">NIK</th>
								<th style="width: 25%;">Name</th>
								<th style="width: 20%; text-align: center;">Section</th>
							</tr>					
						</thead>
						<tbody id="tableBodyList">
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="col-xs-12">
			<div class="box">
				<div class="box-body">

					<div class="tab-content">
						<div class="row">
							<div class="col-xs-12">
								<label style="font-weight: bold; font-size: 18px;">
									<span><i class="fa fa-fw fa-user"></i> Patient Data</span>
								</label>
							</div>

							<input type="hidden" id="idx">

							<div class="col-xs-4">
								<span style="font-weight: bold; font-size: 16px;">Date Time:</span>
								<input type="text" id="date" style="width: 100%; height: 35px; font-size: 15px;" disabled>
							</div>
							<div class="col-xs-4">
								<span style="font-weight: bold; font-size: 16px;">NIK:</span>
								<input type="text" id="nik" style="width: 100%; height: 35px; font-size: 15px;" disabled>
							</div>
							<div class="col-xs-4">
								<span style="font-weight: bold; font-size: 16px;">Hire Date:</span>
								<input type="text" id="hire_date" style="width: 100%; height: 35px; font-size: 15px;" disabled>
							</div>


							<div class="col-xs-4">
								<span style="font-weight: bold; font-size: 16px;">Name:</span>
								<input type="text" id="name" style="width: 100%; height: 35px; font-size: 15px;" disabled>
							</div>
							<div class="col-xs-4">
								<span style="font-weight: bold; font-size: 16px;">Section:</span>
								<input type="text" id="section" style="width: 100%; height: 35px; font-size: 15px;" disabled>
							</div>

							<div class="col-xs-4" style="color: black;">
								<span style="font-weight: bold; font-size: 16px;">Purpose:<span class="text-red">*</span></span>
								<div class="form-group">
									<select style="width: 100%;" class="form-control select2 purpose" id="purpose"  data-placeholder="Select Purpose">
										<option value=""></option>
										@foreach($purposes as $purpose)
										<option value="{{ $purpose }}">{{ $purpose }}</option>
										@endforeach
									</select>
								</div>
							</div>							

							<div class="col-xs-4" style="color: black;" id="bed-field">
								<div class="col-xs-2" style="padding: 0px;">
									<span style="font-weight: bold; font-size: 16px;">Bed:<span class="text-red">*</span></span>
								</div>
								<div class="col-xs-10" style="padding: 0px;">
									<div class="form-group">
										<input type="radio" name="bed" value="Yes"> Yes<br>
										<input type="radio" name="bed" value="No" checked="checked"> No<br>
									</div>									
								</div>
							</div>

							<div class="col-xs-12" style="color: black;" id="cek-suhu">
								<div class="col-xs-12" style="padding: 0px;">
									<span style="font-weight: bold; font-size: 16px;">Equipment:<span class="text-red">*</span></span>
								</div>
								<div class="col-xs-2" style="padding: 0px;">
									<div class="form-group">
										<div class="col-xs-12" style="padding: 0px;">
											<span style="font-weight: bold; font-size: 16px;">Surgical Masker:</span>
											<input type="number" class="form-control" id="masker" style="text-align:center; width: 100%; height: 35px; font-size: 15px;">
										</div>
										<div class="col-xs-12" style="padding: 0px;">
											<span style="font-weight: bold; font-size: 16px;">Masker:</span>
											<input type="number" class="form-control" id="glove" style="text-align:center; width: 100%; height: 35px; font-size: 15px;">
										</div>
									</div>									
								</div>
							</div>

							<div id='pemeriksaan-kesehatan'>
								<div class="col-xs-12" style="margin-top: 1%; margin-left: 1%;">
									<label>
										<input id="family-check-box" type="checkbox" onchange="familyChange()">
										Family Check Up
									</label>
								</div>
								<div id="family-field">
									<div class="col-xs-4" style="color: black;">
										<span style="font-weight: bold; font-size: 16px;">Family:</span>
										<div class="form-group" style="width: 100%;">
											<select style="width: 100%;" class="form-control select2" id="family" data-placeholder="Select Family">
												<option value="">Select Family</option>
												<option value="suami">Suami</option>
												<option value="istri">Istri</option>
												<option value="anak">Anak</option>
											</select>
										</div>
									</div>
									<div class="col-xs-4">
										<span style="font-weight: bold; font-size: 16px;">Family Name:</span>
										<input type="text" id="family_name" style="width: 100%; height: 35px; font-size: 15px;">
									</div>
								</div>							
								<div class="col-xs-12" style="margin-top: 2%;">
									<label style="font-weight: bold; font-size: 18px;">
										<span><i class="fa fa-stethoscope"></i> Patient Diagnose</span>
									</label>
								</div>
								<div class="col-xs-4" style="color: black;">
									<span style="font-weight: bold; font-size: 16px;">Diagnose:<span class="text-red">*</span></span>
									<select style="width: 100%;" class="form-control select2" multiple="multiple" id="diagnose" data-placeholder="Select Diagnose">
										@foreach($diagnoses as $diagnose)
										<option value="{{ $diagnose }}">{{ $diagnose }}</option>
										@endforeach
									</select>
								</div>
								<div class="col-xs-4" style="color: black;">
									<span style="font-weight: bold; font-size: 16px;">Doctor:</span>
									<div class="form-group" style="width: 100%;">
										<select style="width: 100%;" class="form-control select2" id="doctor" data-placeholder="Select Doctor">
											<option value="">Select Doctor</option>
											@foreach($doctors as $doctor)
											<option value="{{ $doctor }}">{{ $doctor }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="col-xs-12" style="margin-top: 3%; margin-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;">
										<label style="font-weight: bold; font-size: 18px;">
											<span><i class="fa fa-medkit"></i> Medicine</span>
										</label>
									</div>
									<div class="col-xs-1" style="padding: 0px;">
										<button class="btn btn-success" onclick='addMedicine();'><i class='fa fa-plus' ></i></button>
									</div>
								</div>
								<div id='medicine'></div>
							</div>

							
							<div class="col-md-12">
								<br>
								<button class="btn btn-success pull-right" onclick="inputDiagnose()">Submit</button>
								<span class="pull-right">&nbsp;</span>
							</div>

						</div>
					</div>
				</div>

			</div>			
		</div>
	</div>

	{{-- Modal Delete --}}
	<div class="modal modal-default fade" id="delete_modal">
		<div class="modal-dialog modal-xs">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h4 class="modal-title">
						Delete Clinic Visitor
					</h4>
				</div>
				<div class="modal-body">
					<div class="modal-body">
						<h5 style="color: black;" id="delete_confirmation_text"></h5>
					</div>
					<input type="hidden" id="id">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button class="btn btn-danger" onclick="deleteVisitor()"><span><i class="fa fa-trash"></i> Delete</span></button>
				</div>
			</div>
		</div>
	</div>

</section>

@endsection
@section('scripts')
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>



<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('.select2').select2();
		$('body').toggleClass("sidebar-collapse");
		$('#family-field').hide();
		$('#pemeriksaan-kesehatan').hide();
		$('#cek-suhu').hide();
		$('#bed-field').hide();
		document.getElementById("family-check-box").checked = false;

		fillVisitor();
		setInterval(fillVisitor, 10000);

	});

	function familyChange() {
		if(document.getElementById("family-check-box").checked){
			$('#family-field').show();
		}else{
			$('#family-field').hide();
		}
	}

	$('#purpose').on('change', function() {
		if(this.value == 'Pemeriksaan Kesehatan'){
			$('#pemeriksaan-kesehatan').show();
			$('#bed-field').show();
			$('#cek-suhu').hide();
		}else if(this.value == 'Konsultasi Kesehatan'){
			$('#pemeriksaan-kesehatan').hide();
			$('#bed-field').show();
			$('#cek-suhu').hide();
		}else if(this.value == 'Istirahat Sakit'){
			$('#pemeriksaan-kesehatan').hide();
			$('#bed-field').show();
			$('#cek-suhu').hide();
		}else if(this.value == 'Petugas Cek Suhu'){
			$('#pemeriksaan-kesehatan').hide();
			$('#bed-field').hide();
			$('#cek-suhu').show();
		}else{
			$('#pemeriksaan-kesehatan').hide();
			$('#bed-field').hide();
			$('#cek-suhu').hide();
		}
	});

	var med = 0;
	function addMedicine() {
		++med;

		$add = '<div class="col-xs-12" id="add_med_'+ med +'"><div class="col-xs-3" style="color: black; padding: 0px; padding-right: 1%;"><select style="width: 100%;" class="form-control select3" id="med_'+ med +'" data-placeholder="Select Medicine"><option value="">Select Medicine</option>@foreach($medicines as $medicine)<option value="{{ $medicine->medicine_name }}">{{ $medicine->medicine_name }}</option>@endforeach</select></div><div class="col-xs-1" style="color: black; padding: 0px; padding-right: 1%;"><div class="form-group"><input type="number" id="med_qty_'+ med +'" data-placeholder="Qty" style="width: 100%; height: 33px; font-size: 15px; text-align: center;"></div></div><div class="col-xs-1" style="padding: 0px;"><button class="btn btn-danger" onclick="removeMedicine(1)"><i class="fa fa-close"></i></button></div></div>';

		$('#medicine').append($add);

		$(function () {
			$('.select3').select2({
				dropdownParent: $('#medicine')
			});
		})
	}

	function removeMedicine(id) {
		$("#add_med_"+id).remove();

		if(med != id){
			for (var i = id; i < med; i++) {
				document.getElementById("add_med_"+ (i+1)).id = "add_med_"+ i;
				document.getElementById("med_"+ (i+1)).id = "med_"+ i;
				document.getElementById("med_qty_"+ (i+1)).id = "med_qty_"+ i;
			}		
		}
		med--;
	}

	function inputDiagnose(){
		var id = $("#idx").val();
		var date = $("#date").val();
		var nik = $("#nik").val();
		var purpose = $("#purpose").val();
		var family = $("#family").val();
		var family_name = $("#family_name").val();
		var diagnose = $("#diagnose").val();
		var doctor = $("#doctor").val();

		var masker = $("#masker").val();
		var glove = $("#glove").val();


		var radios = document.getElementsByName('bed');
		for (var i = 0, length = radios.length; i < length; i++) {
			if (radios[i].checked) {
				var bed = radios[i].value;
				break;
			}
		}

		var medicines = [];
		for (var i = 1; i <= med; i++) {
			medicines.push({medicine_name : $("#med_"+ i).val(), quantity: $("#med_qty_"+ i).val()});
		}

		$("#loading").show();
		if(purpose == "" || bed == ""){
			openErrorGritter('Error!', '(*) must be filled');
			$("#loading").hide();
			return false;
		}else if(purpose == "Pemeriksaan Kesehatan"){
			if(diagnose == ""){
				openErrorGritter('Error!', '(*) must be filled');
				$("#loading").hide();
				return false;
			}
		}


		var data = {
			id : id,
			date : date,
			nik : nik,
			purpose : purpose,
			bed : bed,
			family : family,
			family_name : family_name,
			diagnose : diagnose,
			doctor : doctor,
			medicine : medicines,
			masker : masker,
			glove : glove,
		}

		$.post('{{ url("input/diagnose") }}', data,  function(result, status, xhr){
			if(result.status){
				$("#date").val("");
				$("#nik").val("");
				$("#hire_date").val("");
				$("#name").val("");
				$("#section").val("");
				$("#family").val("");
				$("#family_name").val("");
				$("#masker").val("");
				$("#glove").val("");

				$('#bed').prop('selectedIndex', 0).change();
				$('#purpose').prop('selectedIndex', 0).change();
				$('#diagnose option:selected').removeAttr('selected');
				$('#doctor').prop('selectedIndex', 0).change();

				$('#medicine').append().empty();
				$('#family-field').hide();
				$('#pemeriksaan-kesehatan').hide();

				med = 0;

				fillVisitor();
				$("#loading").hide();
				openSuccessGritter('Success', result.message);

				location.reload(true);
			}else{
				$('#purpose').prop('selectedIndex', 0).change();
				$('#diagnose').prop('selectedIndex', 0).change();
				$('#doctor').prop('selectedIndex', 0).change();

				$("#loading").hide();
				openErrorGritter('Error!', result.message);
			}
		});

	}

	function deleteVisitor(){
		var id = $("#id").val();

		var data = {
			id : id,
		}

		$("#loading").show();
		$.post('{{ url("delete/diagnose") }}', data,  function(result, status, xhr){
			if(result.status){
				fillVisitor();
				openSuccessGritter('Success', result.message);
				$("#loading").hide();
				$("#delete_modal").modal('hide');
			}else{
				openErrorGritter('Error!', result.message);
				$("#loading").hide();
				$("#delete_modal").modal('hide');
			}
		});

	}

	function showDelete(elem){
		var visitor = $(elem).attr("id");
		var data = visitor.split("+");

		$("#delete_confirmation_text").append().empty();
		$("#delete_confirmation_text").append("Are you sure want to delete Patient <b>"+data[1]+"</b> ?");

		document.getElementById("id").value = data[0];
		$("#delete_modal").modal('show');
	}

	function fillVisitorIdentity(id){
		var data = {
			id : id,
		}

		$.get('{{ url("fetch/diagnose") }}', data,  function(result, status, xhr){
			if(result.status){
				$('#idx').val(result.visitor[0].idx);
				$('#date').val(result.visitor[0].in_time);
				$('#nik').val(result.visitor[0].employee_id);
				$('#hire_date').val(result.visitor[0].hire_date);
				$('#name').val(result.visitor[0].name);
				$('#section').val(result.visitor[0].section);
			}
		});

	}

	function fillVisitor(){
		$.get('{{ url("fetch/diagnose") }}', function(result, status, xhr){
			if(result.status){
				$('#tableList').DataTable().clear();
				$('#tableList').DataTable().destroy();
				$('#tableBodyList').html("");

				var tableData = "";
				var count = 0;
				for (var i = 0; i < result.visitor.length; i++) {
					if(result.visitor[i].employee_id.includes('PI')){
						tableData += '<tr>';
						tableData += '<td onclick="fillVisitorIdentity(\''+result.visitor[i].idx+'\')">'+ ++count +'</td>';
						tableData += '<td onclick="fillVisitorIdentity(\''+result.visitor[i].idx+'\')" style="text-align: center;">'+ result.visitor[i].in_time +'</td>';
						tableData += '<td onclick="fillVisitorIdentity(\''+result.visitor[i].idx+'\')" style="text-align: center;">'+ result.visitor[i].employee_id +'</td>';
						tableData += '<td onclick="fillVisitorIdentity(\''+result.visitor[i].idx+'\')">'+ (result.visitor[i].name || 'Not Found') +'</td>';
						tableData += '<td onclick="fillVisitorIdentity(\''+result.visitor[i].idx+'\')" style="text-align: center;">'+ (result.visitor[i].section || 'Not Found') +'</td>';
						tableData += '</tr>';
					}
					
				}
				$('#tableBodyList').append(tableData);
				$('#tableList').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ -1 ],
					[ 'Show all' ]
					],
					'buttons': {
						buttons:[
						{
							extend: 'pageLength',
							className: 'btn btn-default',
						},
						]
					},
					'paging': false,
					'lengthChange': true,
					// 'pageLength': 5,
					'searching': true,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});
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