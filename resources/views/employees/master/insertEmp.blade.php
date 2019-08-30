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
  	{{ $page }}<span class="text-purple"> </span>
  	{{-- <small>WIP Control <span class="text-purple"> 仕掛品管理</span></small> --}}
  </h1>
  <ol class="breadcrumb">
  	<li>
  		{{-- <a href="javascript:void(0)" onclick="openModalCreate()" class="btn btn-sm bg-purple" style="color:white">Create {{ $page }}</a> --}}
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
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#activity" data-toggle="tab" id="tab1"><i class="fa fa-user"></i>  Insert New Employee</a></li>
			</ul>

			<div class="tab-content">
				<form id="importForm"  name="importForm" method="post" action="{{ url('create/empCreate') }}" enctype="multipart/form-data">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="active tab-pane" id="activity">
						<div class="box-body">
							<div class="col-md-6">
								<div class="form-group">
									<label for="nik">Employee ID<span class="text-red">*</span></label>
									<input type="text" name="nik" id="nik" class="form-control" placeholder="Input Employee ID (NIK)" required>
								</div>

								<div class="form-group">
									<label for="nama">Employee Name<span class="text-red">*</span></label>
									<input type="text" name="nama" id="nama" class="form-control" placeholder="Input Employee Name" required>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="tmptL">Place of Birth</label>
											<input type="text" name="tmptL" id="tmptL" placeholder="Input place of birth" class="form-control">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="tglL">Date of Birth</label>
											<input type="text" name="tglL" id="tglL" class="form-control datepicker" placeholder="Select date of birth">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-12"><label>Gender</label></div>
									<div class="form-group">
										<div class="col-md-6">											
											<br>
											<label><input type="radio" class="minimal" id="laki" name="jk" value="L" checked> <i class="fa fa-male"></i>  Male</label>
										</div>
										<div class="col-md-6">
											<br>
											<label><input type="radio" class="minimal" id="perempuan" name="jk" value="P"> <i class="fa fa-female"></i> Female</label>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="ktp">Identity Number</label>
									<input type="text" name="ktp" id="ktp" class="form-control" placeholder="Input Identity Number (KTP)">
								</div>

								<div class="form-group">
									<label for="Alamat">Address</label>
									<input type="text" name="alamat" id="alamat" class="form-control" placeholder="Input Domicile Address">
								</div>

								<div class="form-group">
									<label for="statusK">Family Status</label>
									<select id="statusK" class="form-control select2" name="statusK">
										@foreach($keluarga as $k)
										<option value="{{ $k }}">{{ $k }}</option>
										@endforeach
									</select>
								</div>

								<div class="form-group">
									<label for="foto">Photo</label>
									<input type="file" name="foto[]" id="foto"  multiple="">
								</div>

							</div>
							<div class="col-md-12">
								<hr>
							</div>
						</div>
					</div>

					<div class="tab-pane" id="devisiT">
						<div class="box-body">
							<div class="col-md-6">
								<div class="form-group">
									<label for="devisi">Division<span class="text-red">*</span></label>
									<select id="devisi" name="devisi" class="form-control select2" >
										<option value="" disabled selected>Select Devisi</option>
										@foreach($dev as $nomor => $dev)
										<option value="{{ $dev->child_code }}" > {{$dev->child_code}}</option>
										@endforeach
									</select>
								</div>

								<div class="form-group">
									<label for="departemen">Department<span class="text-red">*</span></label>
									<select id="departemen" name="departemen" class="form-control select2">
										<option value="" disabled selected>Select Department</option>
										@foreach($dep as $nomor => $dep)
										<option value="{{ $dep->child_code }}" > {{$dep->child_code}}</option>
										@endforeach
									</select>
								</div>

								<div class="form-group">
									<label for="section">Section</label>
									<select id="section" name="section" class="form-control select2" onchange="getCostCenter()">
										<option value="" disabled selected>Select Section</option>
										@foreach($sec as $nomor => $sec)
										<option value="{{ $sec->child_code }}" > {{$sec->child_code}}</option>
										@endforeach
									</select>
								</div>

								<div class="form-group">
									<label for="subsection">Sub Section</label>
									<select id="subsection" name="subsection" class="form-control select2" onchange="getCostCenter()">
										<option value="" disabled selected>Select Sub Section</option>
										@foreach($sub as $nomor => $sub)
										<option value="{{ $sub->child_code }}" > {{$sub->child_code}}</option>
										@endforeach
									</select>
								</div>

								<div class="form-group">
									<label for="group">Group</label>
									<select id="group" class="form-control select2" name="group" onchange="getCostCenter()">
										<option value="" disabled selected>Select Group</option>
										@foreach($grup as $nomor => $grup)
										<option value="{{ $grup->child_code }}" > {{$grup->child_code}}</option>
										@endforeach
									</select>
								</div>

							</div>

							<div class="col-md-6">

								<div class="form-group">
									<label for="grade">Grade<span class="text-red">*</span></label>
									<select id="grade" class="form-control select2" name="grade" required>
										<option value="" disabled selected>Select Grade</option>
										@foreach($grade as $nomor => $grade)
										<option value="{{$grade->grade_code}}#{{$grade->grade_name}}" >[ {{$grade->grade_code}} ] - {{$grade->grade_name}}</option>
										@endforeach
									</select>
								</div>

								<div class="form-group">
									<label for="jabatan">Position<span class="text-red">*</span></label>
									<select id="jabatan" class="form-control select2" name="jabatan" required>
										@foreach($position as $nomor => $position)
										<option value="{{ $position->position }}" > {{$position->position}}</option>
										@endforeach
									</select>
								</div>

								<div class="form-group">
									<label for="kode">Kode</label>
									<select id="kode" class="form-control select2" name="kode">
										<option value="" disabled selected>Select Kode</option>
										@foreach($kode as $kd)
										<option value="{{ $kd->code }}" > {{$kd->code}}</option>
										@endforeach
									</select>
								</div>

								<div class="form-group">
									<label for="leader">Leader Employee ID</label>
									<input type="text" name="leader" id="leader" class="form-control" placeholder="Input Leader ID (NIK)">
								</div>

							</div>
							<div class="col-md-12">
								<hr>
							</div>
						</div>
					</div>

					<div class="tab-pane" id="kerja">
						<div class="box-body">
							<div class="col-md-6">
								<div class="form-group">
									<label for="statusKar">Status Karyawan</label>
									<select id="statusKar" class="form-control select2" name="statusKar">
										<option value="Kontrak 1">Kontrak 1</option>
										<option value="Kontrak 2">Kontrak 2</option>
										<option value="Percobaan">Percobaan</option>
										<option value="Tetap">Tetap</option>
									</select>
								</div>

								<div class="form-group">
									<label for="pin">Pin</label>
									<input type="text" id="pin" class="form-control" name="pin" placeholder="Input Pin">
								</div>

							</div>

							<div class="col-md-6">
								<div class="form-group">
									<label for="tglM">Hire Date<span class="text-red">*</span></label>
									<input type="text" id="tglM" class="form-control datepicker" placeholder="Select hire date" name="tglM" required>
								</div>

								<div class="form-group">
									<label for="cs">Cost Center<span class="text-red">*</span></label>
									<input type="text" name="cs" class="form-control" id="cs" required>
									{{-- <select id="cs" class="form-control select2" name="cs" required>
										@foreach($cc as $nomor => $cc)
										<option value="{{ $cc->cost_center }}" > {{$cc->cost_center}}</option>
										@endforeach
									</select> --}}
								</div>
							</div>
							<div class="col-md-12">
								<hr>
							</div>
						</div>
					</div>


					<div class="tab-pane" id="admin">
						<div class="box-body">
							<div class="col-md-6">
								<div class="form-group">
									<label for="hp">Mobile Phone Number</label>
									<input type="text" id="hp" class="form-control" name="hp" placeholder="Input Phone Number">
								</div>

								<div class="form-group">
									<label for="bpjstk">BPJS TK Number</label>
									<input type="text" id="bpjstk" class="form-control" name="bpjstk" placeholder="Input BPJS Ketenagakerjaan">
								</div>

								<div class="form-group">
									<label for="bpjskes">BPJS KES Number</label>
									<input type="text" id="bpjskes" class="form-control" name="bpjskes" placeholder="Input BPJS Kesehatan">
								</div>

							</div>

							<div class="col-md-6">
								<div class="form-group">
									<label for="no_rek">Account Number</label>
									<input type="text" id="no_rek" class="form-control" name="no_rek" placeholder="Input Account Number (Rekening)">
								</div>

								<div class="form-group">
									<label for="npwp">NPWP Number</label>
									<input type="text" id="npwp" class="form-control" name="npwp" placeholder="Input NPWP Number">
								</div>

								<div class="form-group">
									<label for="jp">JP Number</label>
									<input type="text" id="jp" class="form-control" name="jp" placeholder="Input JP Number">
								</div>
							</div>
							<div class="col-md-12">
								<button class="btn btn-success pull-right" type="submit" onclick="$('[name=importForm]').submit();">Save <i class="fa fa-check"></i></button>
							</div>
						</div>
					</div>
				</form>
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
		$('.select2').select2({	});
		$('.datepicker').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
		})
		
	});

	$('input[type="radio"].minimal').iCheck({
		radioClass   : 'iradio_minimal-blue'
	})


	function getCostCenter() {
		var data = {
			section: $("#section").val(),
			subsection: $("#subsection").val(),
			group: $("#group").val()
		}

		$.get('{{ url("fetch/cost_center") }}', data, function(result) {
			$("#cs").val(result.cost_center[0].cost_center);
		})
	}

</script>
@endsection