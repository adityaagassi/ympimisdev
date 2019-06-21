@extends('layouts.master')
@section('stylesheets')
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
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

	.thumbnail {
		position: relative;
		width: 200px;
		height: 200px;
		overflow: hidden;
		border: none;
	}
	.thumbnail img {
		position: absolute;
		left: 50%;
		top: 50%;
		height: 100%;
		width: auto;
		-webkit-transform: translate(-50%,-50%);
		-ms-transform: translate(-50%,-50%);
		transform: translate(-50%,-50%);
	}
	.thumbnail img.portrait {
		width: 100%;
		height: auto;
	}
</style>
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
  	Master Employees<span class="text-purple"> </span>
  	{{-- <small>WIP Control <span class="text-purple"> 仕掛品管理</span></small> --}}
  </h1>

  <ol class="breadcrumb">
  	<li>
  		<a href="{{ url("index/insertEmp") }}"  class="btn btn-sm bg-purple" style="color:white">Create {{ $page }}</a>
  	</li>
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
					<form name="importForm" method="post" action="{{ url('import/importEmp') }}" enctype="multipart/form-data">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="form-group">
							<label for="nik">Import Presence</label>
							<input type="file" name="import" required><br>
							<button class="btn btn-success pull-right" type="submit" onclick="$('[name=importForm]').submit();">Import <i class="fa fa-check"></i></button>
						</div>	
					</form>
				</div>
			</div>
		</div>

		
		<div class="col-xs-12">
				{{-- @foreach ($asd as $key => $value)
				Key: {{ $key }}    
				Value: {{ $value }} 
				@endforeach --}}
				<div class="box">
					<div class="box-body">
						<table id="masteremp" class="table table-bordered table-striped table-hover">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th width="12%">Employee ID</th>
									<th width="25%">Name</th>
									<th>Division</th>
									<th>Department</th>
									<th width="10%">Entry Date</th>
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
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>


			<div class="modal fade" id="myModal">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-body">
							<section class="content-header">
								<h1>
									Employee Details
								</h1>
							</section>
							<section class="content container-fluid">
								<div class="row">
									<div class="col-md-4">
										<!-- general form elements -->
										<div class="box box-primary">
											<div class="box-body box-profile">

												<div class="thumbnail">
													<img class="profile-user-img img-responsive img-circle" id="foto" alt="User profile picture" src="">
												</div>

												<h3 class="profile-username text-center" id="nama"></h3>

												<p class="text-muted text-center" id="nik"></p>
												<p class="text-center" id="status"></p>
												<label style="display: none" id="textout">Tanggal Terminasi</label>
												<input type="text" name="tglout" id="tglout" class="form-control datepicker" style="display: none"><br>
												<button class="btn btn-danger btn-md" style="width:100%;display: none" id="btnout" onclick="getConfirmation()">Terminasi</button>
											</div>
											<!-- /.box-body -->
										</div>
										<!-- /.box -->
									</div>
									<div class="col-md-8">
										<div class="nav-tabs-custom">
											<ul class="nav nav-tabs">
												<li class="active"><a href="#activity" data-toggle="tab"><i class="fa fa-user"></i>  Privacy</a></li>
												<li><a href="#devisi" data-toggle="tab"><i class="fa fa-building"></i> Devision</a></li>
												<li><a href="#kerja" data-toggle="tab"><i class="fa fa-briefcase"></i> Employement</a></li>
												<li><a href="#admin" data-toggle="tab"><i class="fa fa-briefcase"></i> Administration</a></li>
											</ul>
											<div class="tab-content">
												<div class="active tab-pane" id="activity">
													<div class="box-header"><i class="fa fa-user"></i> DATA PRIBADI</div>

													<div class="box-body">
														<div class="col-md-6">
															<p class="text-muted">Tempat Lahir</p>
															<p id="tempatLahir"></p>


															<p class="text-muted">Tanggal Lahir</p>
															<p id="tanggalLahir"></p>

															<p class="text-muted">Jenis Kelamin</p>
															<p id="jk"></p>
														</div>
														<div class="col-md-6">
															<p class="text-muted">Alamat</p>
															<p id="alamat"></p>

															<p class="text-muted">Status Keluarga</p>
															<p id="sKeluarga"></p>
														</div>
													</div>
												</div>

												<div class="tab-pane" id="devisi">
													<div class="box-header"><i class="fa fa-building"></i> DATA DEVISI</div>

													<div class="box-body">
														<div class="col-md-6">
															<p class="text-muted">Devisi</p>
															<p id="dev"></p>

															<p class="text-muted">Departemen</p>
															<p id="dep"></p>

															<p class="text-muted">Section</p>
															<p id="sec">cc</p>

															<p class="text-muted">Sub Section</p>
															<p id="sub-sec">scc</p>

														</div>
														<div class="col-md-6">
															<p class="text-muted">Group</p>
															<p id="group">gr</p>

															<p class="text-muted">Kode</p>
															<p id="kode"></p>

															<p class="text-muted">Grade</p>
															<p id="grade"></p>
															<!-- <span id="namaGrade"></span> -->

															<p class="text-muted">Jabatan</p>
															<p id="jabatan"></p>

															<p class="text-muted">Leader</p>
															<p id="atasan"></p>

														</div>
													</div>
												</div>

												<div class="tab-pane" id="kerja">
													<div class="box-header"><i class="fa fa-briefcase"></i> DATA KERJA</div>

													<div class="box-body">
														<div class="col-md-6">
															<p class="text-muted">Status Karyawan</p>
															<p id="statKaryawan"></p>

															<p class="text-muted">Tanggal Masuk</p>
															<p id="tglMasuk"></p>

														</div>

														<div class="col-md-6">
															<p class="text-muted">Pin</p>
															<p id="pin"></p>

															<p class="text-muted">Cost Center</p>
															<p id="costC"></p>

														</div>
													</div>
												</div>


												<div class="tab-pane" id="admin">
													<div class="box-header"><i class="fa fa-briefcase"></i> DATA ADMINISTRASI</div>

													<div class="box-body">
														<div class="col-md-6">
															<p class="text-muted">Nomor Handphone</p>
															<p id="hp"></p>

															<p class="text-muted">Nomor Rekening</p>
															<p id="rek"></p>

															<p class="text-muted">Nomor KTP</p>
															<p id="ktp"></p>

															<p class="text-muted">Nomor NPWP</p>
															<p id="npwp"></p>
														</div>

														<div class="col-md-6">
															<p class="text-muted">No. BPJS TK</p>
															<p id="bpjstk"></p>

															<p class="text-muted">No. BPJS KES</p>
															<p id="bpjskes"></p>

															<p class="text-muted">No. JP</p>
															<p id="jp"></p>

														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</section>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>

						</div>
					</div>
					<!-- /.modal-content -->
				</div>
			</div>

			<div class="modal fade" id="modalUpgrade">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							Update Status
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-6">
									<div class="text-right" id="employee_id" style="font-size: 18pt">emp_id</div>
								</div>
								<div class="col-md-6">
									<div class="text-left" id="name" style="font-size: 18pt">nama</div>
								</div>
							</div>

							<br>
							<br>

							<div class="row">
								<div class="col-md-5">
									{{-- <div class="pull-right">
										<input type="text" id="status_old" class="form-control" readonly>
									</div>
									--}}
									<div class="text-right" style="font-size: 15pt" id="stat">status</div>
								</div>

								<div class="col-md-2">
									<div class="text-center" style="font-size: 15pt"><i class="fa fa-arrow-right"></i></div>
								</div>

								<div class="col-lg-4">
									<select id="statusK" class="form-control select2" name="statusK">
										@foreach($status as $stat)
										<option value="{{ $stat }}">{{ $stat }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button class="btn btn-success">Update</button>
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
		<script>

			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			jQuery(document).ready(function() {
		// $('body').toggleClass("sidebar-collapse");
		$('#datebegin').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			todayHighlight: true
		});
		
		fillmasteremp();
	});

			function fillmasteremp(){
				$('#masteremp tfoot th').each( function () {
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
				});
				var table = $('#masteremp').DataTable({
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
					},
					'paging'        : true,
					'lengthChange'  : true,
					'searching'     : true,
					'ordering'      : true,
					'info'        : true,
					'order'       : [],
					'autoWidth'   : true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true,
					"serverSide": true,
					"ajax": {
						"type" : "get",
						"url" : "{{ url("fetch/masteremp") }}",
					},
					"columns": [
					{ "data": "employee_id"},
					{ "data": "name"},
					{ "data": "division"},
					{ "data": "department"},
					{ "data": "hire_date"},
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
					} );
				});

				$('#masteremp tfoot tr').appendTo('#masteremp thead');
			}

			function detail(nik) {
				var link = $(location).attr('href'); 

				var data = {
					nik:nik
				}
				$.get('{{ url("fetch/masterempdetail") }}', data, function(result, status, xhr){
						// console.log(status);
						// console.log(result);
						// console.log(xhr);
						if(xhr.status == 200){
							if(result.status){
								var path = "{{asset('uploads/employee_photos')}}";
								$('#tempatLahir').text(result.detail[0].birth_place);
								$('#tanggalLahir').text(result.detail[0].birth_date);
								$('#jk').text(result.detail[0].gender);
								$('#alamat').text(result.detail[0].address);
								$('#sKeluarga').text(result.detail[0].family_id);
								$('#tglMasuk').text(result.detail[0].hire_date);
								$('#pin').text(result.detail[0].remark);
								$('#hp').text(result.detail[0].phone);
								$('#rek').text(result.detail[0].account);
								$('#ktp').text(result.detail[0].card_id);
								$('#npwp').text(result.detail[0].npwp);
								$('#bpjstk').text(result.detail[0].bpjstk);
								$('#jp').text(result.detail[0].jp);
								$('#bpjskes').text(result.detail[0].bpjskes);
								$('#dev').text(result.detail[0].division);
								$('#dep').text(result.detail[0].department);
								$('#sec').text(result.detail[0].section);
								$('#sub-sec').text(result.detail[0].subsection);
								$('#group').text(result.detail[0].group);
								$('#grade').text(result.detail[0].grade_code+" - "+result.detail[0].grade_name);
								$('#jabatan').text(result.detail[0].position);
								$('#atasan').text(result.detail[0].direct_superior);
								$("#foto").attr("src",path+"/"+result.detail[0].avatar);	
								$('#myModal').modal('show');



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

			function modalUpgrade(emp_id, name, status) {
				$('#employee_id').text(emp_id);
				$('#name').text(name);
				$('#stat').text(status);
				$('#modalUpgrade').modal('show');
			}
		</script>
		@endsection