@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		font-size: 0.93vw;
		border:1px solid black;
		padding-top: 5px;
		padding-bottom: 5px;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding-top: 3px;
		padding-bottom: 3px;
		padding-left: 2px;
		padding-right: 2px;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		font-size: 0.8vw;
		border:1px solid black;
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
	}	
	#loading, #error { display: none; }
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
	</h1>
</section>
@endsection

@section('content')
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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		{{-- <input type="hidden" id="order_id" value="{{ $order_id }}"> --}}
		<div class="col-xs-12">
			<table class="table table-hover table-bordered table-striped" id="tableConfirm">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th style="width: 1%;">No.<br>数</th>
						<th style="width: 1%;">Order By ID<br>予約者</th>
						<th style="width: 3%;">Order By Name<br>予約者</th>
						<th style="width: 1%;">Date<br>日付</th>
						<th style="width: 1%;">Order For ID<br>予約対象者</th>
						<th style="width: 3%;">Order For Name<br>予約対象者</th>
						<th style="width: 1%;">Action</th>
					</tr>
				</thead>
				<tbody>
					{{-- @if(count($bentos)>0) --}}
					@foreach($bentos as $bento)
					@if($bento->status == 'Waiting')
					<tr id="list_{{ $bento->id }}" style="background-color: #ccff90">
					@elseif($bento->status == 'Approved')
					<tr id="list_{{ $bento->id }}" style="background-color: #ccff90">
					@elseif($bento->status == 'Rejected')
					<tr id="list_{{ $bento->id }}" style="background-color: #ff6090">
					@endif
						<td style="font-weight: bold;">{{ $bento->order_id }}</td>
						<td style="font-weight: bold;">{{ $bento->order_by }}</td>
						<td style="font-weight: bold;">{{ $bento->order_by_name }}</td>
						<td style="font-weight: bold;">{{ date("D, d M Y", strtotime($bento->due_date)) }}</td>
						<td style="font-weight: bold;">{{ $bento->employee_id }}</td>
						<td style="font-weight: bold;">{{ $bento->employee_name }}</td>
						<td style="font-weight: bold;">
							@if($bento->status == 'Waiting')
							<select class="form-control select2" id="status_{{ $bento->id }}" style="width: 100%;" onchange="statusChange(value)">
								<option value="{{ $bento->id }}_Approved">Approve</option>
								<option value="{{ $bento->id }}_Rejected">Reject</option>
							</select>
							@else
							{{ $bento->status }}
							@endif
						</td>
					</tr>
					@endforeach
					{{-- @endif --}}
				</tbody>
			</table>
			@if(count($bentos) > 0)
			<button class="btn btn-success" style="width: 100%; font-weight: bold; font-size: 2vw;" onclick="confirmOrder()">
				CONFIRM ORDER
			</button>
			@else
			<button class="btn btn-success" style="width: 100%; font-weight: bold; font-size: 2vw;" disabled>
				CONFIRMED
			</button>
			@endif
		</div>
	</div>
</section>
@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('.select2').select2({
			minimumResultsForSearch: -1
		});	
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function statusChange(val){
		str = val.split('_');

		if(str[1] == 'Approved'){
			$('#list_'+str[0]).css('background-color', '#ccff90');
		}
		else{
			$('#list_'+str[0]).css('background-color', '#ff6090');
		}
	}

	function confirmOrder(){
		$('#loading').show();
		if(confirm("Are you sure want to make this order? この予約内容でよろしいですか。")){
			var rejected = [];
			var approved = [];
			$('#tableConfirm tbody tr').each(function() {
				str = this.id.split('_');
				status = $('#status_'+str[1]).val();
				str2 = status.split('_');
				if(str2[1] == 'Rejected'){
					rejected.push(parseInt(str2[0]));
				}
				else{
					approved.push(parseInt(str2[0]));
				}
			});

			var order_id = $('#order_id').val();
			var data = {
				approved:approved,
				rejected:rejected,
				order_id:order_id
			}

			$.post('{{ url("approve/ga_control/bento") }}', data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					audio_ok.play();
					openSuccessGritter('Success!', result.message);
					location.reload(true);
					return false;
				}
				else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!', result.message);
					return false;
				}
			});
		}
		else{
			$('#loading').hide();
			return false;
		}
	}

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '5000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '5000'
		});
	}
</script>

@endsection