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
				<li class="active"><a href="#activity" data-toggle="tab" id="tab1"><i class="fa fa-user"></i>  Update Employee Data</a></li>
			</ul>

			<div class="tab-content">
				<form id="importForm"  name="importForm" method="post" action="{{ url('update/empCreate') }}" enctype="multipart/form-data">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="active tab-pane" id="activity">
						<div class="box-body">
							@foreach($emp as $nomor => $emp)

							<div class="col-md-6">
								<div class="form-group">
									<label for="nik">Employee ID</label>
									<input type="hidden" name="nik2" id="nik2" class="form-control" hidden value="{{$emp->employee_id}}">
									<input type="text" name="nik" id="nik" class="form-control" required value="{{$emp->employee_id}}">
								</div>

								<div class="form-group">
									<label for="nama">Employee Name</label>
									<input type="text" name="nama" id="nama" class="form-control" required value="{{$emp->name}}">
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="tmptL">Place of Birth</label>
											<input type="text" name="tmptL" id="tmptL" class="form-control" value="{{$emp->birth_place}}">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="tglL">Date of Birth</label>
											<input type="text" name="tglL" id="tglL" class="form-control datepicker" value="{{$emp->birth_date}}">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-12"><label>Gender</label></div>
									<div class="form-group">
										<div class="col-md-6">
											<div class="radio">
												<label>
													@if($emp->gender =="L")
													<input type="radio" name="jk" id="laki" value="L" checked>Laki - laki</label>
													@else
													<input type="radio" name="jk" id="laki" value="L" >Laki - laki</label>
													@endif
												</div>
											</div>
											<div class="col-md-6">
												<div class="radio">
													@if($emp->gender =="P")
													<label><input type="radio" name="jk" id="perempuan" value="P" checked>Perempuan</label>
													@else
													<input type="radio" name="jk" id="perempuan" value="P" >Perempuan</label>
													@endif
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="ktp">Identity Number</label>
										<input type="text" name="ktp" id="ktp" class="form-control" value="{{$emp->card_id}}">
									</div>

									<div class="form-group">
										<label for="Alamat">Address</label>
										<input type="text" name="alamat" id="alamat" class="form-control" value="{{$emp->address}}">
									</div>

									<div class="form-group">
										<label for="statusK">Family Status</label>
										<select id="statusK" class="form-control select2" name="statusK">
											@foreach($keluarga as $keluarga)
											@if($keluarga == $emp->family_id)
											<option value="{{ $keluarga }}" selected>{{ $keluarga }}</option> 
											@else
											<option value="{{ $keluarga }}">{{ $keluarga }}</option> 
											@endif
										@endforeach</select>										
										
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

					<div class="tab-pane" id="kerja">
						<div class="box-body">
							<div class="col-md-6">
									{{-- <div class="form-group">
										<label for="statusKar">Status Karyawan</label>
										<select id="statusKar" class="form-control select2" name="statusKar">
											<option value="Kontrak 1">Kontrak 1</option>
											<option value="Kontrak 2">Kontrak 2</option>
											<option value="Percobaan">Percobaan</option>
											<option value="Tetap">Tetap</option>
										</select>
									</div> --}}

									<div class="form-group">
										<label for="pin">Pin</label>
										<input type="text" id="pin" class="form-control" name="pin" value="{{$emp->remark}}">
									</div>

								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label for="tglM">Hire Date</label>
										<input type="text" id="tglM" class="form-control datepicker" placeholder="Select date" name="tglM" required value="{{$emp->hire_date}}">
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
										<input type="text" id="hp" class="form-control" name="hp" value="{{$emp->phone}}">
									</div>

									<div class="form-group">
										<label for="bpjstk">BPJS TK Number</label>
										<input type="text" id="bpjstk" class="form-control" name="bpjstk" value="{{$emp->bpjstk}}">
									</div>

									<div class="form-group">
										<label for="bpjskes">BPJS KES Number</label>
										<input type="text" id="bpjskes" class="form-control" name="bpjskes" value="{{$emp->bpjskes}}">
									</div>

								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label for="no_rek">Account Number</label>
										<input type="text" id="no_rek" class="form-control" name="no_rek" value="{{$emp->account}}">
									</div>

									<div class="form-group">
										<label for="npwp">NPWP Number</label>
										<input type="text" id="npwp" class="form-control" name="npwp" value="{{$emp->npwp}}">
									</div>

									<div class="form-group">
										<label for="jp">JP Number</label>
										<input type="text" id="jp" class="form-control" name="jp" value="{{$emp->jp}}">
									</div>
								</div>
								@endforeach
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






</script>
@endsection