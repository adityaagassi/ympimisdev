@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Leader Task Report &nbsp;<small><span class="text-purple">リーダータスリポート</span></small>
		<a href="{{ url('index/production_report/index/'.$id)}}" class="btn btn-warning pull-right" style="color:white">Kembali</a>
	</h1>
	<ol class="breadcrumb">
  	</ol>
</section>
@stop
@section('content')
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, Please Wait.<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
		</div>
		<div class="col-xs-4" style="text-align: center; color: green;">
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Nama Leader <i class="fa fa-angle-double-down"></i></span>
			@foreach($leader as $leader)
				<!-- <a href="{{ url('index/leader_task_report/leader_task_list/'.$id."/".$leader->leader_dept) }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">{{ $leader->leader_dept }}</a> -->
				<button onclick="activityList('{{$id}}','{{$leader->leader_dept}}')" class="btn btn-default btn-block" style="font-size: 20px; border-color: green;">{{$leader->leader_dept}}</button>
			@endforeach
		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
		</div>
	</div>

	<div class="modal fade" id="activity-modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div class="col-xs-12">
							<div class="row">
								<div class="col-xs-12">
									<div class="row">
										<div class="col-xs-12">
											<span style="font-weight: bold; font-size: 18px;margin-right: 20px">Pilih Aktivitas</span>
											<div class="col-md-4 pull-right" id="tanggalAktivitas">
												<div class="form-group">
													<div class="input-group date">
														<div class="input-group-addon bg-white">
															<i class="fa fa-calendar"></i>
														</div>
														<input type="text" class="form-control datepicker2" id="tgl" name="month" placeholder="Pilih Bulan" required autocomplete="off">
													</div>
												</div>
											</div>
											<button id="backAktivitas" onclick="backAktivitas()" class="btn btn-warning pull-right">Kembali</button>
										</div>
										<div class="col-xs-12">
											
										</div>
									</div>
								</div>
								<div class="col-xs-12" style="padding-top: 10px" id="divAktivitas">
									<table id="tableAktivitas" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
										<thead style="background-color: rgb(126,86,134); color: #FFD700;">
											<tr style="text-align: center;">
												<th>Aktivitas</th>
												<th>Frekuensi</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody id="bodyTableAktivitas">
											
										</tbody>
									</table>
								</div>
								<div class="col-xs-12" style="padding-top: 10px" id="divDetail">
									<center><span style="font-weight: bold; font-size: 18px;" id="titleDetail">Aktivitas</span></center>
									<table id="tableDetailAktivitas" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
										<thead style="background-color: rgb(126,86,134); color: #FFD700;">
											<tr style="text-align: center;">
												<th>Aktivitas</th>
												<th>Frekuensi</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody id="bodyTableDetailAktivitas">
										</tbody>
									</table>
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
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	
	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('#divDetail').hide();
		$('#backAktivitas').hide();
		$('#tanggalAktivitas').show();

		$('.datepicker2').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});
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

	function activityList(id,leader) {
		$('#loading').show();
		var url = '{{ url("index/leader_task_report/filter") }}';
		var data = {
			id:id,
			leader:leader
		}
		$.get(url, data,function(result, status, xhr){
			if(result.status){
				$('#bodyTableAktivitas').empty();
				var aktivitas = "";
				$('#tableAktivitas').DataTable().clear();
				$('#tableAktivitas').DataTable().destroy();
				$.each(result.activity, function(key, value) {
					aktivitas += '<tr>';
					aktivitas += '<td>'+value.activity_name+'</td>';
					aktivitas += '<td>'+value.frequency+'</td>';
					aktivitas += '<td><button class="btn btn-info" onclick="activityDetail(\''+id+'\',\''+value.id+'\',\''+value.activity_type+'\')">Lihat Aktivitas</button></td>';
					aktivitas += '</tr>';
				});
				$('#bodyTableAktivitas').append(aktivitas);

				var table = $('#tableAktivitas').DataTable({
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
						}
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
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

				$('#loading').hide();
				$("#activity-modal").modal('show');
				$('#divDetail').hide();
			} else {
				audio_error.play();
				$('#loading').hide();
			}
		});
	}

	function activityDetail(id,activity_list_id,activity_type) {
		$('#titleDetail').html("");
		$('#divDetail').hide();
		$('#backAktivitas').hide();
		var month = $('#tgl').val();
		var url = '{{ url("index/leader_task_report/filter_detail") }}';
		var data = {
			id:id,
			activity_list_id:activity_list_id,
			activity_type:activity_type,
			month:month,
		}
		$.get(url, data,function(result, status, xhr){
			if(result.status){
				$('#bodyTableDetailAktivitas').empty();
				var aktivitas = "";
				var activity_name = "";
				var leader = "";
				$('#tableDetailAktivitas').DataTable().clear();
				$('#tableDetailAktivitas').DataTable().destroy();
				if (result.activity.length > 0) {
					$.each(result.activity, function(key, value) {
						aktivitas += '<tr>';
						if (activity_type === 'Jishu Hozen') {
							aktivitas += '<td>'+value.activity_name_detail+'</td>';
							aktivitas += '<td>'+value.frequency+'</td>';
							aktivitas += '<td><a target="_blank" class="btn btn-info" href="{{ url("") }}'+value.link+'">Detail Aktivitas</button></td>';
							aktivitas += '</tr>';
							activity_name = value.activity_name;
							leader = value.leader_dept;
						}else{
							aktivitas += '<td>'+value.activity_name+'</td>';
							aktivitas += '<td>'+value.frequency+'</td>';
							aktivitas += '<td><a target="_blank" class="btn btn-info" href="{{ url("") }}'+value.link+'">Detail Aktivitas</button></td>';
							aktivitas += '</tr>';
							activity_name = value.activity_name;
							leader = value.leader_dept;
						}
					});
					$('#bodyTableDetailAktivitas').append(aktivitas);

					var table = $('#tableDetailAktivitas').DataTable({
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
							}
							]
						},
						'paging': true,
						'lengthChange': true,
						'pageLength': 10,
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

					$('#divDetail').show();
					$('#titleDetail').html(activity_name+' of '+leader+' on '+result.monthTitle);
					$('#divAktivitas').hide();
					$('#backAktivitas').show();
					$('#tanggalAktivitas').hide();
				}else{
					openErrorGritter('Error!','Data Tidak Tersedia');
					$('#tanggalAktivitas').show();
				}
			} else {
				openErrorGritter('Error!',result.message);
				audio_error.play();
				$('#tanggalAktivitas').show();
			}
		});
	}

	function backAktivitas() {
		$('#divDetail').hide();
		$('#divAktivitas').show();
		$('#backAktivitas').hide();
		$('#tanggalAktivitas').show();
	}
</script>
@endsection