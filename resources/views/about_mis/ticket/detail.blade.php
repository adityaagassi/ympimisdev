@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<style type="text/css">
table{
	border:1px solid black !important;
}
thead>tr>th{
	vertical-align: middle !important;
	text-align:center !important;
	border:1px solid black !important;
}
tbody>tr>td{
	border:1px solid black !important;
}
tfoot>tr>th{
	border:1px solid black !important;
}
.select2-container.select2-container--default.select2-container--open  {
	z-index: 5000;
}
.crop2 {
	overflow: hidden;
}
.crop2 img {
	height: 70px;
	margin: -2.7% 0 0 0 !important;
}
#tableTimelineBody > tr:hover {
	/*cursor: pointer;*/
	background-color: #7dfa8c;
}
#loading { display: none; }
#alert { display: none; }
</style>
@endsection

{{-- @section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
	</h1>
</section>
@endsection --}}

@section('content')
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<input type="hidden" id="ticket_approver" value=" {{ $ticket_approver }}">
	<input type="hidden" id="ticket_attachment" value=" {{ $ticket_attachment }}">
	<input type="hidden" id="ticket_costdown" value=" {{ $ticket_costdown }}">
	<input type="hidden" id="ticket_timeline" value=" {{ $ticket_timeline }}">
	<input type="hidden" id="ticket_equipment" value=" {{ $ticket_equipment }}">
	<input type="hidden" id="ticket" value=" {{ $ticket  }}">
	<div class="row">
		<div class="col-xs-12">
			<div class="alert alert-info alert-dismissible" id="alert">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				<h4><i class="icon fa fa-info"></i> Info!</h4>
				Data telah diperbaharui, refresh halaman untuk memberpaharui.&nbsp;&nbsp;&nbsp;
				<button class="btn btn-warning btn-xs" onclick="refreshAll()">Refresh</button>&nbsp;&nbsp;&nbsp;
				<button data-dismiss="alert" class="btn btn-danger btn-xs">Nanti Saja</button>
			</div>
			<div class="box box-solid" style="border: 1px solid black;">
				<div class="box-header" style="border-bottom: 1px solid black;">
					<h3 class="box-title">Ticket Information</h3>
					@if(Auth::user()->role_code == 'MIS')
					<button onclick="modalEdit('{{ $ticket->ticket_id }}')" class="btn btn-success pull-right" style="margin-left: 5px; width: 10%;"><i class="fa fa-pencil"></i>&nbsp;&nbsp;&nbsp;Edit</button>
					@endif
					<a href="{{ url("fetch/ticket/pdf/$ticket->ticket_id") }}" class="btn btn-info pull-right" style="margin-left: 5px; width: 15%;"><i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;&nbsp;Form Request PDF</a>
				</div>
				<div class="box-body" style="display: block;">
					<div class="col-xs-8" style="padding-left: 0;">
						<div class="box box-widget">
							<?php $total_costdown = 0;
							$total_spent = 0;
							$total_duration = 0; ?>
							@if(count($ticket_costdown) > 0)
							@foreach($ticket_costdown as $cd)
							<?php $total_costdown += $cd->cost_amount ?>
							@endforeach
							@endif
							@if(count($ticket_equipment) > 0)
							@foreach($ticket_equipment as $eq)
							<?php $total_spent += $eq->quantity*$eq->item_price ?>
							@endforeach
							@endif
							@if(count($ticket_timeline) > 0)
							@foreach($ticket_timeline as $tl)
							<?php $time = explode(':', $tl->duration);
							$minute = ($time[0]*60) + ($time[1]) + ($time[2]/60);
							$total_duration += $minute; ?>
							@endforeach
							@endif
							<div class="box-footer" style="border: 1px solid black;">
								<div class="row">
									<div class="col-sm-4">
										<div class="description-block" style="">	
											<h5 class="description-header" style="font-size: 1.3vw;">
												<span style="color: green;" id="project_costdown">{{ $total_costdown }}</span>
											</h5>
											<span class="description-text" style="font-size: 1.3vw;">Target/Month<br>(USD)<br>
											</span>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="description-block" style="">
											<h5 class="description-header" style="font-size: 1.3vw;">
												<span style="color: red;" id="project_cost">{{ $total_spent }}</span>
											</h5>
											<span class="description-text" style="font-size: 1.3vw;">Amount Spent<br>(USD)<br>
											</span>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="description-block" style="">
											<h5 class="description-header" style="font-size: 1.3vw;">
												<span style="color: blue;" id="project_duration">{{ $total_duration }}</span>
											</h5>
											<span class="description-text" style="font-size: 1.3vw;">Duration<br>(Minute)<br>
											</span>
										</div>	
									</div>
								</div>
							</div>
						</div>
						<table class="table table-bordered table-responsive" width="100%">
							<thead>
								<tr>
									<th style="width: 50%; text-align: center;">BEFORE</th>
									<th style="width: 50%; text-align: center;">AFTER</th>						
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style="vertical-align: top;"><?= $ticket->case_before ?></td>
									<td style="vertical-align: top;"><?= $ticket->case_after ?></td>
								</tr>
							</tbody>
						</table>
						<table class="table table-bordered table-responsive" width="100%">
							<thead>
								<tr>
									<th style="width: 1%; text-align: center;">Category</th>
									<th style="width: 6%; text-align: center;">Target Description</th>
									<th style="width: 1%; text-align: center;">Amount Target</th>							
								</tr>
							</thead>
							<tbody>
								@foreach($ticket_costdown as $cd)
								<tr>
									<td>{{ $cd->category }}</td>
									<td>{{ $cd->cost_description }}</td>
									<td style="text-align: right;">{{ $cd->cost_amount }}</td>
								</tr>
								@endforeach
							</tbody>
						</table>
						<table class="table table-bordered table-responsive" width="100%">
							<thead>
								<tr>
									<th style="width: 0.1%; text-align: center;">ID</th>
									<th style="width: 8%; text-align: center;">Description</th>
									<th style="width: 0.5%; text-align: center;">Qty</th>
									<th style="width: 0.5%; text-align: center;">Price USD</th>
									<th style="width: 0.5%; text-align: center;">Amount</th>
								</tr>
							</thead>
							<tbody>
								@if(count($ticket_equipment) > 0)
								@foreach($ticket_equipment as $eq)
								<tr>
									<td>{{ $eq->item_id }}</td>
									<td>{{ $eq->item_description }}</td>
									<td style="text-align: right;">{{ $eq->quantity }}</td>
									<td style="text-align: right;">{{ $eq->item_price }}</td>
									<td style="text-align: right;">{{ $eq->item_price*$eq->quantity }}</td>
								</tr>
								@endforeach
								@else
								<tr>
									<td>-</td>
									<td>-</td>
									<td style="text-align: right;">0</td>
									<td style="text-align: right;">0</td>
									<td style="text-align: right;">0</td>
								</tr>
								@endif
							</tbody>
						</table>
					</div>
					<div class="col-xs-4" style="border: 1px solid black; padding-right: 0;">
						<h3 class="text-primary">({{ $ticket->ticket_id }}) {{ $ticket->case_title }}</h3>
						<p class="text-muted">
							{{ $ticket->case_description }}
						</p>
						<br>
						<div class="text-muted">
							<p class="text-sm">Category<br>
								<b>{{ $ticket->category }}</b>
							</p>
							<p class="text-sm">Status & Priority<br>
								<b>{{ $ticket->status }} - {{ $ticket->priority }}</b>
							</p>
							@if(strlen($ticket->priority_reason) > 0)
							<p class="text-sm">Priority Reason<br>
								<b>{{ $ticket->priority_reason }}</b>
							</p>
							@endif
							<p class="text-sm">Applicant<br>
								<b>{{ $ticket->user->username }} - {{ $ticket->user->name }}</b>
							</p>
							<p class="text-sm">Department<br>
								<b class="d-block">{{ $ticket->department }}</b>
							</p>
							<p class="text-sm">Requested Due Date<br>
								<b class="d-block">{{ date('d F Y', strtotime($ticket->due_date_from)) }} - {{ date('d F Y', strtotime($ticket->due_date_to)) }}</b>
							</p>
							<p class="text-sm">Estimated Due Date<br>
								@if(strlen($ticket->estimated_due_date_from) > 0)
								<b class="d-block">{{ date('d F Y', strtotime($ticket->estimated_due_date_from)) }} - {{ date('d F Y', strtotime($ticket->estimated_due_date_to)) }}</b>
								@else
								<b class="d-block">-</b>
								@endif
							</p>
							<p class="text-sm" style="margin-bottom: 0;">Handled By<br>
								@php
								$foto = strtoupper($ticket->pic_id);
								$avatar = 'images/avatar/'.$foto.'.jpg';
								@endphp
								<div class="crop2">
									<img src="{{ url($avatar) }}">
									<b class="d-block">{{ $ticket->pic_id }} - {{ $ticket->pic_name }}</b>
								</div>
							</p>
							<p class="text-sm">Difficulty & Project Name<br>
								@if(strlen($ticket->difficulty) > 0)
								<b class="d-block">{{ $ticket->difficulty }} {{ $ticket->project }}</b>
								@else
								<b class="d-block">-</b>
								@endif
							</p>
							<p class="text-sm">Progress
								<div class="progress progress-sm active" style="margin-bottom: 0;">
									<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-volumenow="{{ $ticket->progress }}" aria-volumemin="0" aria-volumemax="100" style="width: {{ $ticket->progress }}%">
									</div>
								</div>
								<b class="d-block">{{ $ticket->progress }}% Complete</b>
							</p>
						</div>
						<h5 class="mt-5 text-muted">Project files</h5>
						<ul class="list-unstyled">
							@if(count($ticket_attachment) > 0)
							@foreach($ticket_attachment as $att)
							<?php 
							$fn1 = explode('(', $att->file_name);
							$fn2 = explode(')', $fn1[1]);
							$filename = $fn2[0].$fn2[1];
							$ext = 'fa-file';
							if($att->file_extension == 'docx'){
								$ext = 'fa-file-word-o';
							}
							if($att->file_extension == 'xlsx'){
								$ext = 'fa-file-excel-o';
							}
							if($att->file_extension == 'pdf'){
								$ext = 'fa-file-pdf-o';
							}
							if($att->file_extension == 'JPG'){
								$ext = 'fa-file-image-o';
							}
							if($att->file_extension == 'PNG'){
								$ext = 'fa-file-image-o';
							}
							if($att->file_extension == 'txt'){
								$ext = 'fa-file-text-o';
							}?>
							<li>
								<a href="{{ url('files/mis_ticket/') }}/{{ $att->file_name }}" class="btn-link text-secondary"><i class="fa {{ $ext }}"></i> {{ $filename }}</a>
							</li>
							@endforeach
							@else
							-
							@endif
						</ul>
						<br>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid" style="border: 1px solid black;">
				<div class="box-header with-border" style="border-bottom: 1px solid black;">
					<h3 class="box-title">
						Ticket Timeline
					</h3>
					@if(Auth::user()->role_code == 'MIS')
					<button class="btn btn-success pull-right" style="margin-left: 5px; width: 10%;" onclick="modalTimeline('{{ $ticket->ticket_id }}')">
						<i class="fa fa-plus-square"></i>&nbsp;&nbsp;&nbsp;Add Timeline
					</button>
					@endif
				</div>
				<div class="box-body" style="display: block;">
					<table class="table table-bordered table-responsive" width="100%" id="tableTimeline">
						<thead>
							<tr>
								<th style="width: 0.1%; text-align: center;">Date</th>
								{{-- <th style="width: 0.1%; text-align: center;">#</th> --}}
								<th style="width: 0.1%; text-align: center;">ID</th>
								<th style="width: 2%; text-align: center;">Name</th>
								<th style="width: 1%; text-align: center;">Category</th>
								<th style="width: 8%; text-align: center;">Description</th>
								<th style="width: 0.1%; text-align: center;">Att</th>
								<th style="width: 0.1%; text-align: center;">Duration (Min)</th>
								<th style="width: 0.1%; text-align: center;">Progress (%)</th>					
							</tr>
						</thead>
						<tbody id="tableTimelineBody">
							@foreach($ticket_timeline as $tl)
							<tr>
								<td>{{ date('Y-m-d', strtotime($tl->timeline_date)) }}</td>
								{{-- <td style="text-align: center;">
									@php
									$foto2 = strtoupper($tl->pic_id);
									$avatar2 = 'images/avatar/'.$foto2.'.jpg';
									@endphp
									<div class="crop2">
										<img src="{{ url($avatar2) }}">
									</div>
								</td> --}}
								<td>{{ $tl->pic_id }}</td>
								<td>{{ $tl->pic_name }}</td>
								<td>{{ $tl->timeline_category }}</td>
								<td>{{ $tl->timeline_description }}</td>
								<td style="text-align: center;">
									@if(strlen($tl->timeline_attachment) > 0)
									<a href="{{ url('files/mis_ticket/') }}/{{ $tl->timeline_attachment }}" class="btn-link text-secondary">Open</a>
									@else
									-
									@endif
								</td>
								<?php $time = explode(':', $tl->duration);
								$minute = ($time[0]*60) + ($time[1]) + ($time[2]/60); ?>
								<td style="text-align: right;">{{ $minute }}</td>
								<td style="text-align: right;">{{ $tl->progress_update }}%</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalEdit" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
						Edit Ticket<br>
					</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<div class="col-md-12">
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Ticket ID<span class="text-red"></span> :</label>
								<div class="col-sm-3">
									<input type="text" class="form-control" placeholder="Enter Ticket ID" id="editTicketId" value="{{ $ticket->ticket_id }}" disabled="">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Status<span class="text-red"></span> :</label>
								<div class="col-sm-3">
									<select class="form-control select2" id="editStatus" data-placeholder="Select Status" style="width: 100%;">
										<option></option>
										@foreach($statuses as $status)
										@if($status == $ticket->status)
										<option value="{{ $status }}" selected="">{{ $status }}</option>
										@else
										<option value="{{ $status }}">{{ $status }}</option>
										@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Department<span class="text-red"></span> :</label>

								<div class="col-sm-7">
									<select class="form-control select2" id="editDepartment" data-placeholder="Select Department" style="width: 100%;">
										<option></option>
										@foreach($departments as $department)
										@if($department->department_name == $ticket->department)
										<option value="{{ $department->department_name }}" selected="">{{ $department->department_name }}</option>
										@else
										<option value="{{ $department->department_name }}">{{ $department->department_name }}</option>
										@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Category<span class="text-red"></span> :</label>
								<div class="col-sm-4">
									<select class="form-control select2" id="editCategory" data-placeholder="Select Category" style="width: 100%;">
										<option></option>
										@foreach($categories as $category)
										@if($category == $ticket->category)
										<option value="{{ $category }}" selected="">{{ $category }}</option>
										@else
										<option value="{{ $category }}">{{ $category }}</option>
										@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Priority<span class="text-red"></span> :</label>
								<div class="col-sm-4">
									<select class="form-control select2" id="editPriority" data-placeholder="Select Priority" style="width: 100%;">
										<option></option>
										@foreach($priorities as $priority)
										@if($priority == $ticket->priority)
										<option value="{{ $priority }}" selected="">{{ $priority }}</option>
										@else
										<option value="{{ $priority }}">{{ $priority }}</option>
										@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group" id="createGroupReason">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Priority Reason<span class="text-red"></span> :</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="3" placeholder="Enter Reason" id="editReason">{{ $ticket->priority_reason }}</textarea>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Title<span class="text-red"></span> :</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" placeholder="Enter Title" id="editTitle" value="{{ $ticket->case_title }}">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Description<span class="text-red"></span> :</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="3" placeholder="Enter Description" id="editDescription">{{ $ticket->case_description }}</textarea>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Condition Before<span class="text-red"></span> :</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="3" placeholder="Enter Description" id="editBefore"><?= $ticket->case_before ?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Condition After<span class="text-red"></span> :</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="3" placeholder="Enter Description" id="editAfter"><?= $ticket->case_after ?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Document Number<span class="text-red"></span> :</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="3" placeholder="Enter Document Number" id="editDocument">{{ $ticket->document }}</textarea>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Due Date From<span class="text-red"></span> :</label>
								<div class="col-sm-3">
									<input type="text" class="form-control datepicker" id="editDueFrom" placeholder="   Select Date" value="{{ $ticket->due_date_from }}">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Due Date To<span class="text-red"></span> :</label>
								<div class="col-sm-3">
									<input type="text" class="form-control datepicker" id="editDueTo" placeholder="   Select Date" value="{{ $ticket->due_date_to }}">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Estimated Due Date From<span class="text-red"></span> :</label>
								<div class="col-sm-3">
									<input type="text" class="form-control datepicker" id="editEstimatedDueFrom" placeholder="   Select Date" value="{{ $ticket->estimated_due_date_from }}">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Estimated Due Date To<span class="text-red"></span> :</label>
								<div class="col-sm-3">
									<input type="text" class="form-control datepicker" id="editEstimatedDueTo" placeholder="   Select Date" value="{{ $ticket->estimated_due_date_to }}">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">PIC<span class="text-red"></span> :</label>
								<div class="col-sm-7" id="selectPic">
									<select class="form-control selectPic" id="editPic" data-placeholder="Select PIC" style="width: 100%;">
										<option></option>
										@foreach($mis_members as $mis_member)
										@if($mis_member->employee_id == $ticket->pic_id)
										<option value="{{ $mis_member->employee_id }}~{{ $mis_member->name }}" selected="">{{ $mis_member->employee_id }} - {{ $mis_member->name }}</option>
										@else
										<option value="{{ $mis_member->employee_id }}~{{ $mis_member->name }}">{{ $mis_member->employee_id }} - {{ $mis_member->name }}</option>
										@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Difficulty<span class="text-red"></span> :</label>
								<div class="col-sm-3" id="selectDifficulty">
									<select class="form-control selectDifficulty" id="editDifficulty" data-placeholder="Select Difficulty" style="width: 100%;">
										<option></option>
										@foreach($difficulties as $difficulty)
										@if($difficulty == $ticket->difficulty)
										<option value="{{ $difficulty }}" selected="">{{ $difficulty }}</option>
										@else
										<option value="{{ $difficulty }}">{{ $difficulty }}</option>
										@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Project Name<span class="text-red"></span> :</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" placeholder="Enter Project Name" id="editProjectName" value="{{ $ticket->project_name }}">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Target<span class="text-red"></span> :</label>
								<div class="col-sm-3">
									<a class="btn btn-primary" style="font-weight: bold;" onclick="addCostDown()"><i class="fa fa-plus"></i> Target</a>
								</div>
							</div>
							<span style="font-weight: bold; font-size: 1vw;"><i class="fa fa-list"></i> List Target</span>
							<table class="table table-bordered table-striped" id="tableCostDown">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 4%;">Category<span class="text-red"></span></th>
										<th style="width: 8%;">Description<span class="text-red"></span></th>
										<th style="width: 4%;">Amount (USD)<span class="text-red"></span></th>
										<th style="width: 1%;">Action</th>
									</tr>
								</thead>
								<tbody id="tableCostDownBody">
									@foreach($ticket_costdown as $cd)
									<tr id="cd_{{ $cd->id }}">
										<td>{{ $cd->category }}</td>
										<td>{{ $cd->cost_description }}</td>
										<td>{{ $cd->cost_amount }}</td>
										<td><center><a href="javascript:void(0)" onclick="delCostDown(id)" id="{{ $cd->id }}" class="btn btn-danger btn-sm" style="margin-right:5px;"><i class="fa fa-trash"></i></a></center></td>
									</tr>
									@endforeach
								</tbody>
							</table>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Equipment<span class="text-red"></span> :</label>
								<div class="col-sm-3">
									<a class="btn btn-primary" style="font-weight: bold;" onclick="addEquipment()"><i class="fa fa-plus"></i> Equipment</a>
								</div>
							</div>
							<span style="font-weight: bold; font-size: 1vw;"><i class="fa fa-list"></i> List Equipment</span>
							<table class="table table-bordered table-striped" id="tableEquipment">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 4%;">Item<span class="text-red"></span></th>
										<th style="width: 1%;">Qty<span class="text-red"></span></th>
										<th style="width: 1%;">Price (USD)<span class="text-red"></span></th>
										<th style="width: 1%;">Action</th>
									</tr>
								</thead>
								<tbody id="tableEquipmentBody">
									@foreach($ticket_equipment as $eq)
									<tr id="eq_{{ $eq->item_id }}">
										<td>{{ $eq->item_id }} - {{ $eq->item_description }}</td>
										<td>{{ $eq->quantity }}</td>
										<td>{{ $eq->item_price }}</td>
										<td><center><a href="javascript:void(0)" onclick="delEquipment(id)" id="'{{ $eq->id }}'" class="btn btn-danger btn-sm" style="margin-right:5px;"><i class="fa fa-trash"></i></a></center></td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</form>
					<div class="col-md-12">
						<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 30%;">CANCEL </button>
						<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;" onclick="editTicket()">CONFIRM </i></button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalTimeline" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
						Add Timeline<br>
					</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<div class="col-md-12">
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Ticket ID<span class="text-red">*</span> :</label>
								<div class="col-sm-3">
									<input type="text" class="form-control" placeholder="Enter Ticket ID" id="addTicketId" disabled="">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">PIC ID<span class="text-red">*</span> :</label>
								<div class="col-sm-3">
									<input type="text" class="form-control" placeholder="Enter PIC ID" id="addPicId" disabled="">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">PIC Name<span class="text-red">*</span> :</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" placeholder="Enter PIC Name" id="addPicName" disabled="">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Date<span class="text-red">*</span> :</label>
								<div class="col-sm-3">
									<input type="text" class="form-control datepicker" id="addDate" placeholder="   Select Date">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Category<span class="text-red">*</span> :</label>
								<div class="col-sm-4">
									<select class="form-control select2" id="addCategory" data-placeholder="Select Category" style="width: 100%;">
										<option></option>
										@foreach($timeline_categories as $timeline_category)
										<option value="{{ $timeline_category }}">{{ $timeline_category }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Description<span class="text-red">*</span> :</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="3" placeholder="Enter Description" id="addDescription"></textarea>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Duration<span class="text-red">*</span> :</label>
								<div class="col-sm-2">
									<input type="text" id="addDuration" class="form-control timepicker" value="00:15">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Progress %<span class="text-red">*</span> :</label>
								<div class="col-sm-2">
									<input type="text" class="form-control" placeholder="Enter Progress %" id="addProgress">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Attachment<span class="text-red"></span> :</label>
								<div class="col-sm-5">
									<input type="file" id="addAttachment" multiple="">
								</div>
							</div>
						</div>
					</form>
					<div class="col-md-12">
						<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 30%;">CANCEL </button>
						<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;" onclick="addTimeline()">ADD TIMELINE </i></button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
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
		// drawTable();
		// $('.select2').select2();
		$('#addDate').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});
		$('#editDueFrom').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});
		$('#editDueTo').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});
		$('#editEstimatedDueFrom	').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});
		$('#editEstimatedDueTo').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});
		$('.timepicker').timepicker({
			showInputs: false,
			showMeridian: false,
			defaultTime: '0:15',
		});
	});

	$(function () {
		$('#addCategory').select2({
			dropdownParent: $('#modalTimeline')
		});
		$('#editStatus').select2({
			dropdownParent: $('#modalEdit'),
			minimumResultsForSearch: -1
		});
		$('#editCategory').select2({
			dropdownParent: $('#modalEdit'),
			minimumResultsForSearch: -1
		});
		$('#editPriority').select2({
			dropdownParent: $('#modalEdit'),
			minimumResultsForSearch: -1
		});
		$('#editDepartment').select2({
			dropdownParent: $('#modalEdit'),
			minimumResultsForSearch: -1
		});
		$('.selectPic').select2({
			dropdownParent: $('#selectPic'),
			minimumResultsForSearch: -1
		});
		$('.selectDifficulty').select2({
			dropdownParent: $('#selectDifficulty'),
			minimumResultsForSearch: -1
		});
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var ticket_approvers = JSON.parse($('#ticket_approver').val());
	var ticket_attachments = JSON.parse($('#ticket_attachment').val());
	var ticket_costdowns = JSON.parse($('#ticket_costdown').val());
	var ticket_timelines = JSON.parse($('#ticket_timeline').val());
	var ticket_equipments = JSON.parse($('#ticket_equipment').val());
	var ticket = JSON.parse($('#ticket').val());
	var costdowns = [];
	var costdown_count = 0;
	var equipments = [];
	var equipment_count = 0;

	function editTicket(){
		if(confirm("Apakah anda yakin akan merubah tiket ini?")){
			var ticket_id = $('#editTicketId').val();
			var status = $('#editStatus').val();
			var department = $('#editDepartment').val();
			var category = $('#editCategory').val();
			var priority = $('#editPriority').val();
			var reason = $('#editReason').val();
			var title = $('#editTitle').val();
			var description = $('#editDescription').val();
			var before = CKEDITOR.instances.editBefore.getData();
			var after = CKEDITOR.instances.editAfter.getData();
			var doc = $('#editDocument').val();
			var due_from = $('#editDueFrom').val();
			var due_to = $('#editDueTo').val();
			var estimated_due_from = $('#editEstimatedDueFrom').val();
			var estimated_due_to = $('#editEstimatedDueTo').val();
			var pic = $('#editPic').val().split('~');
			var pic_id = pic[0];
			var pic_name = pic[1];
			var difficulty = $('#editDifficulty').val();
			var project_name = $('#editProjectName').val();

			var formData = new FormData();

			$.each(costdowns, function(key, value){
				var costdown_category = $('#costdown_category_'+value).val();
				var costdown_description = $('#costdown_description_'+value).val();
				var costdown_amount =  $('#costdown_amount_'+value).val();
				formData.append('costdown['+key+']', costdown_category+'~'+costdown_description+'~'+costdown_amount);
			});
			$.each(equipments, function(key, value){
				var item = $('#equipment_id_'+value).val().split('~');
				var item_id = item[0];
				var item_description = item[1];
				var quantity = $('#equipment_quantity_'+value).val();
				var price = $('#equipment_price_'+value).val();
				formData.append('equipment['+key+']', item_id+'~'+item_description+'~'+quantity+'~'+price);
			});

			formData.append('ticket_id', ticket_id);
			formData.append('status', status);
			formData.append('department', department);
			formData.append('category', category);
			formData.append('priority', priority);
			formData.append('reason', reason);
			formData.append('title', title);
			formData.append('description', description);
			formData.append('before', before);
			formData.append('after', after);
			formData.append('doc', doc);
			formData.append('due_from', due_from);
			formData.append('due_to', due_to);
			formData.append('estimated_due_from', estimated_due_from);
			formData.append('estimated_due_to', estimated_due_to);
			formData.append('pic_id', pic_id);
			formData.append('pic_name', pic_name);
			formData.append('difficulty', difficulty);
			formData.append('project_name', project_name);

			$.ajax({
				url:"{{ url('edit/ticket') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status) {
						clearAll();
						$('#loading').hide();
						openSuccessGritter('Success!',data.message);
						audio_ok.play();
						location.reload(true);
					}
					else{
						$('#loading').hide();
						openErrorGritter('Error!',data.message);
						audio_error.play();
					}

				}
			});

		}
		else{
			return false;
		}
	}

	function modalEdit(ticket_id){
		$('#modalEdit').modal('show');
	}

	function addEquipment(){
		var equipment_list = <?php echo json_encode($equipments); ?>;

		var tableEquipmentBody = "";
		equipment_count += 1;

		tableEquipmentBody += '<tr id="equipment_'+equipment_count+'">';
		tableEquipmentBody += '<td>';
		tableEquipmentBody += '<div id="select_equipment_id_'+equipment_count+'">';
		tableEquipmentBody += '<select style="width: 100%;" class="select_equipment_id_'+equipment_count+'" id="equipment_id_'+equipment_count+'" onchange="selectedEquipment(value)">';
		tableEquipmentBody += '<option></option>';
		$.each(equipment_list, function(key, value){
			tableEquipmentBody += '<option value="'+value.kode_item+'~'+value.deskripsi+'~'+value.price_usd+'~'+equipment_count+'">'+value.kode_item+' - '+value.deskripsi+'</option>';
		});
		tableEquipmentBody += '</select>';
		tableEquipmentBody += '</div>';
		tableEquipmentBody += '</td>';
		tableEquipmentBody += '<td><input type="text" class="form-control" id="equipment_quantity_'+equipment_count+'" value="0"></td>';
		tableEquipmentBody += '<td><input type="text" class="form-control" id="equipment_price_'+equipment_count+'" value="0" disabled></td>';
		tableEquipmentBody += '<td><center><a href="javascript:void(0)" onclick="remEquipment(id)" id="'+equipment_count+'" class="btn btn-danger btn-sm" style="margin-right:5px;"><i class="fa fa-trash"></i></a></center></td>';
		tableEquipmentBody += '</tr>';

		$('#tableEquipmentBody').append(tableEquipmentBody);
		equipments.push(equipment_count);

		// $('.select2').select2();
		$(function () {
			$('.select_equipment_id_'+equipment_count).select2({
				dropdownParent: $('#select_equipment_id_'+equipment_count),
				minimumInputLength: 2
			});
		})
	}

	function selectedEquipment(val){
		var str = val.split('~');
		console.log(val);
		$('#equipment_price_'+str[3]).val(str[2]);
	}

	function remEquipment(id){
		equipments = jQuery.grep(equipments, function(value) {
			return value != id;
		});
		$('#equipment_'+id).remove();
	}

	function delEquipment(id){
		console.log('del '+id);
	}

	function addCostDown(){
		var costdown_list = <?php echo json_encode($costdowns); ?>;

		var tableCostDownBody = "";
		costdown_count += 1;

		tableCostDownBody += '<tr id="costdown_'+costdown_count+'">';
		tableCostDownBody += '<td>';
		tableCostDownBody += '<select style="width: 100%;" class="select2" id="costdown_category_'+costdown_count+'">';
		tableCostDownBody += '<option></option>';
		$.each(costdown_list, function(key, value){
			tableCostDownBody += '<option value="'+value+'">'+value+'</option>';
		});
		tableCostDownBody += '</select>';
		tableCostDownBody += '</td>';
		tableCostDownBody += '<td><textarea class="form-control" rows="2" id="costdown_description_'+costdown_count+'"></textarea></td>';
		tableCostDownBody += '<td><input type="text" class="form-control" id="costdown_amount_'+costdown_count+'" value="0"></td>';
		tableCostDownBody += '<td><center><a href="javascript:void(0)" onclick="remCostDown(id)" id="'+costdown_count+'" class="btn btn-danger btn-sm" style="margin-right:5px;"><i class="fa fa-trash"></i></a></center></td>';
		tableCostDownBody += '</tr>';

		$('#tableCostDownBody').append(tableCostDownBody);
		costdowns.push(costdown_count);

		$('.select2').select2();
	}

	function remCostDown(id){
		costdowns = jQuery.grep(costdowns, function(value) {
			return value != id;
		});
		$('#costdown_'+id).remove();
	}

	function delCostDown(id){
		console.log('del '+id);
	}

	function addTimeline(){
		var ticket_id = $('#addTicketId').val();
		var pic_id = $('#addPicId').val();
		var pic_name = $('#addPicName').val();
		var category = $('#addCategory').val();
		var date = $('#addDate').val();
		var description = $('#addDescription').val();
		var duration = $('#addDuration').val();
		var progress = $('#addProgress').val();
		var attachment = $('#addAttachment').prop('files')[0];
		var file = $('#addAttachment').val().replace(/C:\\fakepath\\/i, '').split(".");

		if(date == '' || description == '' || duration == '00:00' || progress == ''){
			audio_error.play();
			openErrorGritter('Error!', 'Semua kolom dengan bintang merah harus terisi.');
			return false;
		}

		var formData = new FormData();

		formData.append('ticket_id', ticket_id);
		formData.append('pic_id', pic_id);
		formData.append('pic_name', pic_name);
		formData.append('category', category);
		formData.append('date', date);
		formData.append('description', description);
		formData.append('duration', duration);
		formData.append('progress', progress);
		formData.append('attachment', attachment);
		formData.append('extension', file[1]);
		formData.append('file_name', file[0]);

		$.ajax({
			url:"{{ url('input/ticket/timeline') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				if (data.status) {
					window.scrollTo(0,0);
					clearAll();
					openSuccessGritter('Success!',data.message);
					audio_ok.play();
					$('#alert').show();
					$('#modalTimeline').modal('hide');
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!',data.message);
					audio_error.play();
				}
			}
		});
	}

	function modalTimeline(ticket_id){
		clearAll();
		$('#addPicId').val('{{ Auth::user()->username }}');
		$('#addPicName').val('{{ Auth::user()->name }}');
		$('#addProgress').val(ticket.progress);
		console.log(ticket.progress);
		$('#modalTimeline').modal('show');
	}

	function clearAll(){
		$('#loading').hide();
		$('#addTicketId').val(ticket.ticket_id);
		$('#addPicId').val('');
		$('#addPicName').val('');
		$('#addDate').val('');
		$('#addDescription').val('');
		$('#addDuration').val('00:15');
		$('#addProgress').val(ticket.progress);
		$('#addCategory').prop('selectedIndex', 0).change();
		$('#addAttachment').val('');
	}

	function drawTable(){
		$('#tableTimeline').DataTable({
			'dom': 'Bfrtip',
			'responsive':true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			'buttons': {
				buttons:[
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
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': false,
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true
		});
	}

	function refreshAll(){
		location.reload(true);
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

	function truncate(str, n){
		return (str.length > n) ? str.substr(0, n-1) + '&hellip;' : str;
	};

	function replaceNull(s) {
		return s == null ? "-" : s;
	}

	CKEDITOR.replace('editBefore' ,{
		filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}'
	});

	CKEDITOR.replace('editAfter' ,{
		filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}'
	});
</script>

@endsection