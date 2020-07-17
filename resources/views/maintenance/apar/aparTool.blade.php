@extends('layouts.master')
@section('stylesheets')
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
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
	.progress {
		height: 50px;
		margin-bottom: 0px;
	}

	.progress-bar {
		line-height: 50px;
		font-size: 3vw;

	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}<span class="text-purple"> </span>
	</h1>
</section>
@stop
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-xs-12" style="display: none">
			<div class="box box-solid">
				<div class="box-body">
					<div class="row">
						<div class="col-xs-12">
							<h3 class="text-center pull-left" style="width: 85%" id="judul"></h3>
							<div class="input-group date pull-right" style="width: 15%">
								<div class="input-group-addon bg-purple" style="border: none;">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control" id="cek_date" onchange="checkedData(this.value)" placeholder="Pilih Bulan Cek">
							</div>
							<!-- <input type="text" class="form-control pull-right" style="width: 25%"> -->
						</div>
						<div class="col-xs-12">
							<div class="progress progress-sm active" id="progress">
								
							</div>
							<div class="text-center" style="font-size: 20px; font-weight: bold;" id="all_info">0 / 0</div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-xs-12">
						<div class="box box-primary box-solid">
							<div class="box-body">
								<div class="col-xs-3">
									<div class="form-group">
										<label>Jenis Pemadam</label>
										<select class="form-control select2" data-placeholder='Pilih Jenis Pemadam' id="type">
											<option></option>
											<option value="APAR">APAR</option>
											<option value="HYDRANT">Hydrant</option>
										</select>
									</div>
								</div>
								<div class="col-xs-3">
									<div class="form-group">
										<label>Area</label>
										<select class="form-control select2" data-placeholder='Pilih Area Pemadam' id="area" onchange="location_change(this,'#location')">
											<option></option>
											<option value="Factory I">Factory I</option>
											<option value="Factory II">Factory II</option>
										</select>
									</div>
								</div>
								<div class="col-xs-3">
									<div class="form-group">
										<label>Lokasi</label>
										<select class="form-control select2" data-placeholder='Pilih Lokasi Pemadam' id="location">
											<option></option>
											
										</select>
									</div>
								</div>
								<div class="col-xs-3">
									<div class="form-group">
										<label>Bulan Kadaluarsa</label>
										<div class="input-group date" style="width: 100%;">
											<input type="text" placeholder="Pilih Bulan" class="form-control datepicker" id="expMon">
										</div>
									</div>
								</div>
								<div class="col-xs-12">
									<div class="pull-left">
										<a href="" data-toggle="modal" data-target="#modalCreate" class="btn btn-md bg-purple" style="color:white"><i class="fa fa-plus"></i> New Extinguisher</a>
									</div>
									<div class="pull-right">
										<button class="btn btn-danger" onclick="clearing()"><i class="fa fa-refresh"></i> Clear</button>
										<button class="btn btn-primary" onclick="searching()"><i class="fa fa-search"></i> Search</button>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-xs-12">
						<table id="toolTable" class="table table-bordered table-striped table-hover">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 2%;">ID</th>
									<th style="width: 10%;">Nama</th>
									<th style="width: 2%;">Jenis</th>
									<th style="width: 1%;">Kapasitas (Kg)</th>
									<th style="width: 5%;">Location</th>
									<th style="width: 1%;">Item</th>
									<th style="width: 1%;">Tanggal Kadaluarsa</th>
									<th style="width: 3%;">Aksi</th>
								</tr>
							</thead>
							<tbody id="body_tool">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalCreate" style="color: black;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #605ca8;">
						<h1 style="text-align: center; margin:5px; font-weight: bold; color: white">New Extinguisher</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="form-group row" align="right">
								<label class="col-xs-4" style="margin-top: 1%;">Extinguisher Category</label>
								<div class="col-xs-4" align="left">
									<select class="form-control select2" data-placeholder="Pilih Kategori Pemadam" id="extinguisher_category" onchange="type_change(this)">
										<option></option>
										<option value="APAR">APAR</option>
										<option value="HYDRANT">Hydrant</option>
									</select>
								</div>
							</div>
						</div>
						<div class="col-xs-6">
							<div class="form-group row" align="right">
								<label class="col-xs-4" style="margin-top: 1%;">Extinguisher ID</label>
								<div class="col-xs-8" align="left">
									<input type="text" class="form-control" id="extinguisher_id" placeholder="ID Pemadam">
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-4" style="margin-top: 1%;">Extinguisher Name</label>
								<div class="col-xs-8" align="left">
									<input type="text" class="form-control" id="extinguisher_name" placeholder="Nama Pemadam">
								</div>
							</div>

							<div class="form-group row" align="right" id="div_type">
								<label class="col-xs-4" style="margin-top: 1%;">Type</label>
								<div class="col-xs-8" align="left">
									<select class="form-control select2" data-placeholder="Pilih Jenis Pemadam" id="extinguisher_type">
										<option></option>
										@foreach($types as $type)
										<option value="{{ $type->type }}">{{ $type->type }}</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="form-group row" align="right" id="div_capacity">
								<label class="col-xs-4" style="margin-top: 1%;">Extinguisher Capacity</label>
								<div class="col-xs-8" align="left">
									<div class="input-group">
										<input type="text" class="form-control" id="extinguisher_capacity" placeholder="Kapasitas Pemadam">
										<span class="input-group-addon">Kg</span>
									</div>
								</div>
							</div>
						</div>

						<div class="col-xs-6">

							<div class="form-group row" align="right">
								<label class="col-xs-4" style="margin-top: 1%;">Extinguisher Location</label>
								<div class="col-xs-8" align="left">
									<select class="form-control select2" data-placeholder="Pilih Lokasi" id="extinguisher_location1" onchange="location_change(this,'#extinguisher_location2', null)">
										<option></option>
										<option value="Factory I">Factory I</option>
										<option value="Factory II">Factory II</option>
									</select>
								</div>

								<label class="col-xs-4"></label>
								<div class="col-xs-8" align="left">
									<select class="form-control select2" data-placeholder="Pilih Lokasi" id="extinguisher_location2">
										<option></option>
									</select>
								</div>
							</div>

							<div class="form-group row" align="right" id="div_exp">
								<label class="col-xs-4" style="margin-top: 1%;">Expired Date</label>
								<div class="col-xs-8" align="left">
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										<input type="text" class="form-control" id="extinguisher_exp" placeholder="Pilih Tanggal Kadaluarsa">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success pull-left" onclick="saveExtinguisher()"><i class="fa fa-check"></i> Save</button>
					<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade in" id="editModal" >
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #f39c12;">
					<h1 style="text-align: center; margin:5px; font-weight: bold; color: white">Edit Extinguisher</h1>
				</div>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
						<div class="form-group row" align="right">
							<label class="col-xs-4" style="margin-top: 1%;">Extinguisher Code</label>
							<div class="col-xs-4" align="left">
								<input type="text" class="form-control" id="edit_code" disabled>
							</div>
						</div>

						<div class="form-group row" align="right">
							<label class="col-xs-4" style="margin-top: 1%;">Extinguisher Name</label>
							<div class="col-xs-6" align="left">
								<input type="text" class="form-control" id="edit_name">
							</div>
						</div>

						<div class="form-group row" align="right">
							<label class="col-xs-4" style="margin-top: 1%;">Type</label>
							<div class="col-xs-4" align="left">
								<select class="form-control select3" data-placeholder="Pilih Jenis Pemadam" id="edit_type">
								</select>
							</div>
						</div>

						<div class="form-group row" align="right">
							<label class="col-xs-4" style="margin-top: 1%;">Capacity</label>
							<div class="col-xs-4" align="left">
								<div class="input-group">
									<input type="text" class="form-control" id="edit_capacity">
									<span class="input-group-addon">Kg</span>
								</div>
							</div>
						</div>

						<div class="form-group row" align="right" style="margin-bottom: 5px !important">
							<label class="col-xs-4" style="margin-top: 1%;">Location</label>
							<div class="col-xs-4" align="left">
								<select class="form-control select3" data-placeholder="Pilih Lokasi" id="edit_location1">
									<option></option>
									<option value="Factory I">Factory I</option>
									<option value="Factory II">Factory II</option>
								</select>
							</div>
						</div>

						<div class="form-group row" align="right">
							<label class="col-xs-4" style="margin-top: 1%;"> </label>
							<div class="col-xs-4" align="left">
								<select class="form-control select3" data-placeholder="Pilih Lokasi" id="edit_location2">
									<option></option>
								</select>
							</div>
							
						</div>

						<div class="form-group row" align="right">
							<label class="col-xs-4" style="margin-top: 1%;">Exp. Date</label>
							<div class="col-xs-4" align="left">
								<input type="text" class="form-control" id="edit_exp">
							</div>
						</div>

						<div class="form-group row" align="right">
							<label class="col-xs-4" style="margin-top: 1%;">APAR Type</label>
							<div class="col-xs-8" align="left">
								<div class="input-group">
									<select class="form-control select3" id="edit_type_apar">
										<option></option>
										<option value="A">APAR</option>
										<option value="T">Termathic</option>
									</select>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-success pull-left" onclick="EditExtinguisher()"><i class="fa fa-check"></i> Edit</button>
				<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
			</div>
		</div>

	</div>

</div>

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
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script>
	var no = 2;
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var lokasi = <?php echo json_encode($locations) ?>;
	var checkedDataArr = [];

	const monthNames = ["January", "February", "March", "April", "May", "June",
	"July", "August", "September", "October", "November", "December"
	];


	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		fillToolTable("");

		$("#location").empty();
		$("#extinguisher_location2").empty();

		lks = "<option></option>";
		$.each(lokasi, function(index, value){
			lks += "<option value='"+value.group+"'>"+value.group+"</option>";
		})
		$("#location").append(lks);
		$("#extinguisher_location2").append(lks);
	});

	function location_change(elem, target, selected) {
		var area = $(elem).val();

		$(target).empty();
		lks = "<option></option>";
		$.each(lokasi, function(index, value){
			if (area) {
				if (value.location == area) {
					if (selected && selected == value.group) {
						lks += "<option value='"+value.group+"' selected>"+value.group+"</option>";
						console.log('tes');
					} else {
						lks += "<option value='"+value.group+"'>"+value.group+"</option>";
					}
				}
			} else {
				lks += "<option value='"+value.group+"'>"+value.group+"</option>";
			}
		})
		$(target).append(lks);
	}


	$(function () {
		$('.datepicker').datepicker({
			autoclose: true,
			format: "mm-yyyy",
			viewMode: "months", 
			minViewMode: "months"
		});

		$('#cek_date').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			viewMode: "months", 
			minViewMode: "months"
		});


		$("#extinguisher_location1").select2({
			dropdownParent: $('#modalCreate'),
			tags: true
		});

		$('#extinguisher_exp, #edit_exp').datepicker({
			autoclose: true,
			todayHighlight: true,
			format: 'yyyy-mm-dd',
		});

		$('.select2').select2({
			dropdownParent: $('#modalCreate'),
			dropdownAutoWidth : true,
			width: '100%',
		});

		$('.select3').select2({
			dropdownParent: $('#editModal'),
			dropdownAutoWidth : true,
			width: '100%',
		});
	})


	function fillToolTable(data){
		$('#toolTable').DataTable().clear();
		$('#toolTable').DataTable().destroy();

		$('#body_tool').html("");

		var body = "";

		$.get('{{ url("fetch/maintenance/apar/list") }}', data, function(result, status, xhr){
			checkedDataArr = result.apar;
			checkedData("");
			$.each(result.apar, function(index, value){
				body += "<tr>";
				body += "<td>"+value.utility_code+"</td>";
				body += "<td>"+value.utility_name+"</td>";
				body += "<td>"+value.type+"</td>";
				body += "<td>"+value.capacity+"</td>";
				body += "<td>"+value.location+" - "+value.group+"</td>";
				body += "<td>"+value.remark+"</td>";
				body += "<td>"+value.exp_date2+"</td>";
				body += "<td><button class='btn btn-xs btn-warning' onclick='edit(\""+value.utility_code+"\", this)'>Edit</button>&nbsp;<button class='btn btn-xs btn-danger' onclick='delete_item(\""+value.utility_code+"\")'>Delete</button><input type='hidden' id='"+value.utility_code+"' value='"+value.order+"'></td>";
				body += "</tr>";
			})

			$("#body_tool").append(body);

			var table = $('#toolTable').DataTable({
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
					]
				},
				'paging': true,
				'lengthChange': true,
				'searching': true,
				'ordering': true,
				'info': true,
				'autoWidth': true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true,
			});		
		})
	}

	function checkedData(mon_check) {
		aparCheck = [];

		$.each(checkedDataArr, function(index, value){
			if (value.remark == 'APAR') {
				if (value.last_check) {
					var from = value.last_check.split(" ");
					from = from[0].split("-");

					if (mon_check == "") {
						now = new Date();
					} else {
						now = new Date(mon_check+"-01");
					}

					$("#judul").text(monthNames[now.getMonth()]+" "+now.getFullYear());

					if (from[0] == now.getFullYear() && parseInt(from[1]) == (now.getMonth()+1)) {
						check = 1;
					} else {
						check = 0;
					}
				} else {
					check = 0;
				}

				aparCheck.push({'area': value.location, 'apar_code': value.utility_code, 'apar_name': value.utility_name, 'checked' : check});
			}
		})

			// console.log(aparCheck);

			var countsCheck = {};
			var countsAll = {};
			for(var i = 0; i < aparCheck.length; i++){
				var key = aparCheck[i].area.replace(" ","_");

				if(!countsAll[key]) countsAll[key] = 0;
				countsAll[key]++;

				if (aparCheck[i].checked == 1) {
					if(!countsCheck[key]) countsCheck[key] = 0;
					countsCheck[key]++;
				}
			}

			if (Object.keys(countsCheck).length == 0) {
				cek1 = 0;
				cek2 = 0;
			} else {
				cek1 = countsCheck.Factory_I;
				cek2 = countsCheck.Factory_II;
			}


			var all_check = (cek1 + cek2);
			var all = (countsAll.Factory_I+countsAll.Factory_II);

			$("#all_info").text(all_check+" / "+all);
			var persentase = (all_check / all * 100).toFixed(2);
			$("#progress").append('<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="'+persentase+'" aria-valuemin="0" aria-valuemax="100" style="width: '+persentase+'%">'+persentase+'%</div>');
		}

		function searching() {
			var data = {
				type : $("#type").val(),
				area : $("#area").val(),
				location : $("#location").val(),
				expMon : $("#expMon").val(),
			}

			fillToolTable(data);
		}

		function clearing() {
			$('.select2').val('').trigger("change");

			$("#expMon").val('');
		}

		function edit(code, elem) {
			elm = [];
			$( elem ).parent().parent().children().each( function(index) {
				elm.push($( elem ).parent().parent().children().eq(index).text());
			});

			$("#editModal").modal('show');
			
			$("#edit_code").val(elm[0]);
			$("#edit_name").val(elm[1]);

			var type_apar = $('#'+elm[0]).val();
			$("#edit_type_apar").val(type_apar).trigger("change");

			$("#edit_type").empty();
			var tipe = '<option></option>';

			var arr_tipe = <?php echo json_encode($types) ?>;

			$.each(arr_tipe, function(index, value){
				if (elm[2] == value.type) {
					tipe += '<option value="'+value.type+'" selected>'+value.type+'</option>';
				} else {
					tipe += '<option value="'+value.type+'">'+value.type+'</option>';
				}
			})

			$("#edit_type").append(tipe);
			$("#edit_capacity").val(elm[3]);

			$('#edit_location1 > option').each(function() {
				if ($(this).val() == elm[4].split(" - ")[0]) {
					$(this).attr("selected","selected");
				}
			});

			$("#edit_location2").empty();

			$("#edit_location1").on("change", function(event) { 
				location_change(this,'#edit_location2', elm[4].split(" - ")[1]);
			} );

			$('#edit_location1').trigger("change");

			
			var dt = elm[6].split(" ");

			exp_date = dt[2]+"-"+pad((parseInt(monthNames.indexOf(dt[1])) + 1), 2)+"-"+dt[0];

			// $("#edit_exp").val(exp_date);

			var datepicker = $('#edit_exp');
			datepicker.datepicker();
			datepicker.datepicker('setDate', exp_date);
		}


		function type_change(elem) {
			var params = $(elem).val();

			if (params == 'HYDRANT') {
				$("#div_type").hide();
				$("#div_capacity").hide();
				$("#div_exp").hide();

				$("#extinguisher_type").val("-");
				$("#extinguisher_capacity").val("0");
				$("#extinguisher_exp").val("");
			} else {
				$("#div_type").show();
				$("#div_capacity").show();
				$("#div_exp").show();

				$("#extinguisher_type").val("");
				$("#extinguisher_capacity").val("");
				$("#extinguisher_exp").val("");
			}
		}

		function saveExtinguisher() {
			var data = {
				extinguisher_id : $("#extinguisher_id").val(),
				extinguisher_name : $("#extinguisher_name").val(),
				extinguisher_type : $("#extinguisher_type").val(),
				extinguisher_capacity : $("#extinguisher_capacity").val(),
				extinguisher_location1 : $("#extinguisher_location1").val(),
				extinguisher_location2 : $("#extinguisher_location2").val(),
				extinguisher_exp : $("#extinguisher_exp").val(),
				extinguisher_category: $("#extinguisher_category").val()
			}

			$.post('{{ url("post/maintenance/apar/insert") }}', data, function(result, status, xhr){
				if (result.status) {
					openSuccessGritter('Success', 'Inserted Successfully');

					$("#modalCreate").modal('hide');
					searching();
				} else {
					openErrorGritter('Error', result.message);
				}
			})
		}

		function EditExtinguisher() {
			var data = {
				edit_code : $("#edit_code").val(),
				edit_name : $("#edit_name").val(),
				edit_type : $("#edit_type").val(),
				edit_capacity : $("#edit_capacity").val(),
				edit_location1 : $("#edit_location1").val(),
				edit_location2 : $("#edit_location2").val(),
				edit_exp : $("#edit_exp").val(),
			}

			$.post('{{ url("post/maintenance/apar/update") }}', data, function(result, status, xhr){
				if (result.status) {
					openSuccessGritter('Success', 'Updated Successfully');

					$("#editModal").modal('hide');

					searching();
				} else {
					openErrorGritter('Error', result.message);
				}
			})
		}

		function delete_item(kode) {
			if (confirm('Apakah Anda yakin ingin menghapus Item "'+kode+'" ?')) {
				console.log("OK");
			}
		}

		function pad (str, max) {
			str = str.toString();
			return str.length < max ? pad("0" + str, max) : str;
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
