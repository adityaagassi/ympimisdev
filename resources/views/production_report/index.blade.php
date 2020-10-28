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
		Activity Lists of {{$dept}}<span class="text-purple"> 活動リスト</span>
		<span class="pull-right">
		<button class="btn btn-default" style="font-size: 15px;background-color:white;color: black">Kondisional</button>
		<button class="btn btn-primary" style="font-size: 15px;background-color:#2A3E79;color:white">Harian</button>
		<button class="btn btn-success" style="font-size: 15px;background-color:#90EE7E;color: black">Bulanan</button>
		<button class="btn btn-danger" style="font-size: 15px;background-color:#B93A2B;color:white">Mingguan</button>
		</span>
	</h1>
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
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<?php $no = 1 ?>
			<?php if ($role_code == 'MIS'){ ?>
				@foreach($activity_list as $activity_list)
					@if($activity_list->frequency == "Daily")
						<?php $bgcolor = "background-color:#2A3E79;color:white" ?>
						
					@elseif($activity_list->frequency == "Weekly")
						<?php $bgcolor = "background-color:#B93A2B;color:white" ?>
						
					@elseif($activity_list->frequency == "Monthly")
						<?php $bgcolor = "background-color:#90EE7E" ?>
						
					@else($activity_list->frequency == "Conditional")
						<?php $bgcolor = "background-color:white" ?>
					@endif
					
					@if($activity_list->activity_type == "Pengecekan Foto")
						<button onclick="activityList('{{$id}}','{{$activity_list->no}}','{{$activity_list->frequency}}')" class="btn btn-default btn-block" style="font-size: 20px; border-color: green;{{$bgcolor}}">Cek FG / KD</button>

					@elseif($activity_list->activity_type == "Laporan Aktivitas")
						<button onclick="activityList('{{$id}}','{{$activity_list->no}}','{{$activity_list->frequency}}')" class="btn btn-default btn-block" style="font-size: 20px; border-color: green;{{$bgcolor}}">Laporan Audit IK</button>

					@elseif($activity_list->activity_type == "Pengecekan")
						<button onclick="activityList('{{$id}}','{{$activity_list->no}}','{{$activity_list->frequency}}')" class="btn btn-default btn-block" style="font-size: 20px; border-color: green;{{$bgcolor}}">Audit Produk Pertama</button>

					@elseif($activity_list->activity_type == "Labelisasi")
						<button onclick="activityList('{{$id}}','{{$activity_list->no}}','{{$activity_list->frequency}}')" class="btn btn-default btn-block" style="font-size: 20px; border-color: green;{{$bgcolor}}">Audit Label Safety</button>

					@elseif($activity_list->activity_type == "Audit")
						<button onclick="activityList('{{$id}}','{{$activity_list->no}}','{{$activity_list->frequency}}')" class="btn btn-default btn-block" style="font-size: 20px; border-color: green;{{$bgcolor}}">Audit NG Jelas</button>

					@elseif($activity_list->activity_type == "Pemahaman Proses")
						<button onclick="activityList('{{$id}}','{{$activity_list->no}}','{{$activity_list->frequency}}')" class="btn btn-default btn-block" style="font-size: 20px; border-color: green;{{$bgcolor}}">Audit Pemahaman Proses</button>

					@elseif($activity_list->activity_type == "Jishu Hozen")
						<button onclick="activityList('{{$id}}','{{$activity_list->no}}','{{$activity_list->frequency}}')" class="btn btn-default btn-block" style="font-size: 20px; border-color: green;{{$bgcolor}}">Audit Jishu Hozen</button>

					@elseif($activity_list->activity_type == "Interview")
						<button onclick="activityList('{{$id}}','{{$activity_list->no}}','{{$activity_list->frequency}}')" class="btn btn-default btn-block" style="font-size: 20px; border-color: green;{{$bgcolor}}">Interview Pointing Call</button>

					@else
						<button onclick="activityList('{{$id}}','{{$activity_list->no}}','{{$activity_list->frequency}}')" class="btn btn-default btn-block" style="font-size: 20px; border-color: green;{{$bgcolor}}">{{ $activity_list->activity_type }}</button>
					@endif
				<?php $no++ ?>
				@endforeach
			<?php }else { ?>
				@foreach($activity_list as $activity_list)
					@if($activity_list->frequency == "Daily")
						<?php $bgcolor = "background-color:#2A3E79;color:white" ?>
						
					@elseif($activity_list->frequency == "Weekly")
						<?php $bgcolor = "background-color:#B93A2B;color:white" ?>
						
					@elseif($activity_list->frequency == "Monthly")
						<?php $bgcolor = "background-color:#90EE7E" ?>
						
					@else($activity_list->frequency == "Conditional")
						<?php $bgcolor = "background-color:white" ?>
						
					@endif
					@if($activity_list->leader_dept == $name)
						@if($activity_list->activity_type == "Pengecekan Foto")
							<button onclick="activityList('{{$id}}','{{$activity_list->no}}','{{$activity_list->frequency}}')" class="btn btn-default btn-block" style="font-size: 20px; border-color: green;{{$bgcolor}}">Cek FG / KD</button>

						@elseif($activity_list->activity_type == "Laporan Aktivitas")
							<button onclick="activityList('{{$id}}','{{$activity_list->no}}','{{$activity_list->frequency}}')" class="btn btn-default btn-block" style="font-size: 20px; border-color: green;{{$bgcolor}}">Laporan Audit IK</button>

						@elseif($activity_list->activity_type == "Pengecekan")
							<button onclick="activityList('{{$id}}','{{$activity_list->no}}','{{$activity_list->frequency}}')" class="btn btn-default btn-block" style="font-size: 20px; border-color: green;{{$bgcolor}}">Audit Produk Pertama</button>

						@elseif($activity_list->activity_type == "Labelisasi")
							<button onclick="activityList('{{$id}}','{{$activity_list->no}}','{{$activity_list->frequency}}')" class="btn btn-default btn-block" style="font-size: 20px; border-color: green;{{$bgcolor}}">Audit Label Safety</button>

						@elseif($activity_list->activity_type == "Audit")
							<button onclick="activityList('{{$id}}','{{$activity_list->no}}','{{$activity_list->frequency}}')" class="btn btn-default btn-block" style="font-size: 20px; border-color: green;{{$bgcolor}}">Audit NG Jelas</button>

						@elseif($activity_list->activity_type == "Pemahaman Proses")
							<button onclick="activityList('{{$id}}','{{$activity_list->no}}','{{$activity_list->frequency}}')" class="btn btn-default btn-block" style="font-size: 20px; border-color: green;{{$bgcolor}}">Audit Pemahaman Proses</button>

						@elseif($activity_list->activity_type == "Jishu Hozen")
							<button onclick="activityList('{{$id}}','{{$activity_list->no}}','{{$activity_list->frequency}}')" class="btn btn-default btn-block" style="font-size: 20px; border-color: green;{{$bgcolor}}">Audit Jishu Hozen</button>

						@elseif($activity_list->activity_type == "Interview")
							<button onclick="activityList('{{$id}}','{{$activity_list->no}}','{{$activity_list->frequency}}')" class="btn btn-default btn-block" style="font-size: 20px; border-color: green;{{$bgcolor}}">Interview Pointing Call</button>

						@else
							<button onclick="activityList('{{$id}}','{{$activity_list->no}}','{{$activity_list->frequency}}')" class="btn btn-default btn-block" style="font-size: 20px; border-color: green;{{$bgcolor}}">{{ $activity_list->activity_type }}</button>
						@endif
					@endif
				<?php $no++ ?>
				@endforeach
			<?php } ?>
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/production_report/report_all/'.$id) }}" class="btn btn-default btn-block" style="font-size: 20px; border-color: red;">Leader Task Monitoring</a>
			<!-- <a href="{{ url('index/production_report/report_by_task/'.$id) }}" class="btn btn-default btn-block" style="font-size: 20px; border-color: red;">Leader Tasks</a> -->
		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<?php if($role_code == "PROD-SPL" || $role_code == "F-SPL" || $role_code == "MIS" || $role_code == "S"){ ?>
				<a href="{{ url('index/production_report/approval/'.$id) }}" class="btn btn-default btn-block" style="font-size: 20px; border-color: purple;">Approval</a>
			<?php } ?>
				<a href="{{ url('index/leader_task_report/index/'.$id) }}" class="btn btn-default btn-block" style="font-size: 20px; border-color: purple;">Leader Task Report</a>
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
											<span style="font-weight: bold; font-size: 18px;">Pilih Aktivitas</span>
										</div>
									</div>
								</div>
								<div class="col-xs-12" style="padding-top: 10px">
									<div class="row" id="aktivitas">
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
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	
	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function activityList(id,no,frequency) {
		$('#loading').show();
		var url = '{{ url("index/activity_list/filter/") }}';
		var urlnew = url + '/' + id + '/' + no + '/' + frequency;
		$.get(urlnew, function(result, status, xhr){
			if(result.status){
				$('#aktivitas').empty();
				var aktivitas = "";
				$.each(result.activity_list, function(key, value) {
					aktivitas += '<div class="col-xs-4">';
					aktivitas += '<a class="btn btn-primary" href="{{url("index/production_report/activity/")}}/'+value.id+'" style="margin-bottom: 10px;white-space: normal;width: 100%;font-size: 17px">'+value.activity_name+'<br><b style="font-size: 15px">'+value.leader_dept+'</b></a>';
					aktivitas += '</div>';
			        if(value.activity_type == "Laporan Aktivitas"){
			        	aktivitas += '<div class="col-xs-4">';
			          aktivitas += '<a class="btn btn-info" style="margin-bottom: 10px;white-space: normal;width: 100%;font-size: 17px" href="{{url("index/audit_guidance/index/")}}/'+value.id+'">Schedule Audit IK <br><b style="font-size: 15px">'+value.leader_dept+'</b></a>';
			          aktivitas += '</div>';
			        }
			        if(value.activity_type == "Cek Area"){
			        	aktivitas += '<div class="col-xs-4">';
			          aktivitas += '<a class="btn btn-info" style="margin-bottom: 10px;white-space: normal;width: 100%;font-size: 17px" href="{{url("index/area_check_point/index/")}}/'+value.id+'">Point Check <br><b style="font-size: 15px">'+value.leader_dept+'</b></a>';
			          aktivitas += '</div>';
			        }
			        if(value.activity_type == "Jishu Hozen"){
			        	aktivitas += '<div class="col-xs-4">';
			          aktivitas += '<a class="btn btn-info" style="margin-bottom: 10px;white-space: normal;width: 100%;font-size: 17px" href="{{url("index/jishu_hozen_point/index/")}}/'+value.id+'">Master Pengecekan <br><b style="font-size: 15px">'+value.leader_dept+'</b></a>';
			          aktivitas += '</div>';
			        }
				});
				$('#aktivitas').append(aktivitas);
				$('#loading').hide();
				$("#activity-modal").modal('show');
			} else {
				audio_error.play();
				$('#loading').show();
			}
		});
	}
</script>
@endsection