@extends('layouts.visitor')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("css/jqbtk.css")}}">
<style>
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
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }

	.nav-tabs-custom > ul.nav.nav-tabs {
		display: table;
		width: 100%;
		table-layout: fixed;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li {
		float: none;
		display: table-cell;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li > a {
		text-align: center;
	}
	.vendor-tab{
		width:100%;
	}
	.content-wrapper{
		padding-top: 0px
	}
</style>
@endsection


@section('header')
<section class="content-header">
	<h1>
		<center>	<span style="color: white; font-weight: bold; font-size: 28px; text-align: center;">YMPI Visitor Confirmation</span></center>
		
	</h1><br>
	<ol class="breadcrumb" id="last_update">
	</ol>
</section>
@endsection

@section('content')
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

<input type="text" id="tag_visitor" class="form-control" style="background-color: #3c3c3c;border: none;padding-top: 0px">
<div class="box box-solid" style="padding-top: 0px">
	<div class="box-body" style="padding-top: 0px">
		
		<div class="row" id="telpon">
			<div class="col-md-12">
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px;">
						<?php $index = 1; ?>
						@foreach($division as $division)
							<?php if ($index == 1){ ?>
								<li class="vendor-tab active"><a href="#tab_<?php echo $index ?>" data-toggle="tab" id="tab_header_<?php echo $index ?>">{{ $division->division }}<br><span class="text-purple"></span></a></li>
							<?php }else{ ?>
								<li class="vendor-tab"><a href="#tab_<?php echo $index ?>" data-toggle="tab" id="tab_header_<?php echo $index ?>">{{ $division->division }}<br><span class="text-purple"></span></a></li>
							<?php } ?>
							<?php $index++ ?>
						@endforeach
					</ul>
					<div class="tab-content">
						<div class="tab-pane active" id="tab_1" style="height: 580px;">
							<div class="col-md-12">
								<div class="box box-solid">
									<div class="box-body">
										<div class="table-responsive">
											<table id="example1" class="table table-bordered table-striped table-hover">
												<thead style="background-color: rgba(126,86,134,.7);">	
													<tr>
														<th >Person</th>
														<th >Department</th>
														<th >Telephone</th>
													</tr>
												</thead>
												<tbody>
													@foreach($japan as $japan)
													<tr>
														<td>{{ $japan->person }}</td>
														<td>{{ $japan->dept }}</td>
														<td>{{ $japan->nomor }}</td>
													</tr>
													@endforeach
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="tab-pane" id="tab_2" style="height: 580px;">
							<div class="col-md-12">
								<div class="box box-solid">
									<div class="box-body">
										<div class="table-responsive">
											<table id="example2" class="table table-bordered table-striped table-hover">
												<thead style="background-color: rgba(126,86,134,.7);">	
													<tr>
														<th >Person</th>
														<th >Department</th>
														<th >Telephone</th>
													</tr>
												</thead>
												<tbody>
													@foreach($hrga as $hrga)
													<tr>
														<td>{{ $hrga->person }}</td>
														<td>{{ $hrga->dept }}</td>
														<td>{{ $hrga->nomor }}</td>
													</tr>
													@endforeach
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="tab-pane" id="tab_3" style="height: 580px;">
							<div class="col-md-12">
								<div class="box box-solid">
									<div class="box-body">
										<div class="table-responsive">
											<table id="example3" class="table table-bordered table-striped table-hover">
												<thead style="background-color: rgba(126,86,134,.7);">	
													<tr>
														<th >Person</th>
														<th >Department</th>
														<th >Telephone</th>
													</tr>
												</thead>
												<tbody>
													@foreach($production as $production)
													<tr>
														<td>{{ $production->person }}</td>
														<td>{{ $production->dept }}</td>
														<td>{{ $production->nomor }}</td>
													</tr>
													@endforeach
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="tab-pane" id="tab_4" style="height: 580px;">
							<div class="col-md-12">
								<div class="box box-solid">
									<div class="box-body">
										<div class="table-responsive">
											<table id="example4" class="table table-bordered table-striped table-hover">
												<thead style="background-color: rgba(126,86,134,.7);">	
													<tr>
														<th >Person</th>
														<th >Department</th>
														<th >Telephone</th>
													</tr>
												</thead>
												<tbody>
													@foreach($finance as $finance)
													<tr>
														<td>{{ $finance->person }}</td>
														<td>{{ $finance->dept }}</td>
														<td>{{ $finance->nomor }}</td>
													</tr>
													@endforeach
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="tab-pane" id="tab_5" style="height: 580px;">
							<div class="col-md-12">
								<div class="box box-solid">
									<div class="box-body">
										<div class="table-responsive">
											<table id="example5" class="table table-bordered table-striped table-hover">
												<thead style="background-color: rgba(126,86,134,.7);">	
													<tr>
														<th >Person</th>
														<th >Department</th>
														<th >Telephone</th>
													</tr>
												</thead>
												<tbody>
													@foreach($ps as $ps)
													<tr>
														<td>{{ $ps->person }}</td>
														<td>{{ $ps->dept }}</td>
														<td>{{ $ps->nomor }}</td>
													</tr>
													@endforeach
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="tab-pane" id="tab_6" style="height: 580px;">
							<div class="col-md-12">
								<div class="box box-solid">
									<div class="box-body">
										<div class="table-responsive">
											<table id="example6" class="table table-bordered table-striped table-hover">
												<thead style="background-color: rgba(126,86,134,.7);">	
													<tr>
														<th >Person</th>
														<th >Department</th>
														<th >Telephone</th>
													</tr>
												</thead>
												<tbody>
													@foreach($room as $room)
													<tr>
														<td>{{ $room->person }}</td>
														<td>{{ $room->dept }}</td>
														<td>{{ $room->nomor }}</td>
													</tr>
													@endforeach
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
		</div>
	</div>
</div>

<div class="modal modal-default fade" id="modal_tamu" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<center><h4 class="modal-title" id="myModalLabel">Information</h4></center>
			</div>
			<div class="modal-body">
				<h1 id="tamu"></h1>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				<!-- <a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a> -->
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
	<script src="{{ url("js/jqbtk.js") }}"></script>
	<script >
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		jQuery(document).ready(function() {
			$('#tag_visitor').focus();
			// $('#nikkaryawan').val('asd');

			// setTimeout(function(){
			//       $("#tag_visitor").focus();
			//     },60000);
			setInterval(focusTag,60000);
			$('.select2').select2({
				dropdownAutoWidth : true,
				width: '100%',
			});
			reloadTable();
		});

		function focusTag() {
			$('#tag_visitor').focus();
		}

		$(function() {
			$(document).keydown(function(e) {
				switch(e.which) {
					case 48:
					location.reload(true);
					break;
					case 49:
					$("#tab_header_1").click()
					break;
					case 50:
					$("#tab_header_2").click()
					break;
					case 51:
					$("#tab_header_3").click()
					break;
					case 52:
					$("#tab_header_4").click()
					break;
					case 53:
					$("#tab_header_5").click()
					break;
					case 54:
					$("#tab_header_6").click()
					break;
				}
			});
		});


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

		$('#tag_visitor').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#tag_visitor").val().length >= 8){
					var data = {
						tag_visitor : $("#tag_visitor").val()
					}

					$.get('{{ url("scan/visitor/lobby") }}', data, function(result, status, xhr){
						if(result.status){
							$('#tag_visitor').val('');
							$('#modal_tamu').modal('show');
							if (result.location == 'Security' && result.destination == 'Office') {
								$('#tamu').html('<center><b>Tunggu Sebentar, '+result.visitor.name+' Akan menemui Anda.</b></center>');
							}else if(result.location == 'Lobby' && result.destination == 'Office'){
								$('#tamu').html('<center><b>Tunggu Sebentar, '+result.visitor.name+' Akan menemui Anda.</b></center>');
							}else if(result.location == 'Security' && result.destination != 'Office'){
								$('#tamu').html('<center><b>Mohon Maaf, tujuan Anda adalah '+result.destination+'. Silahkan menghubungi '+result.visitor.name+' untuk informasi lebih lanjut.</b></center>');
							}
							openSuccessGritter('Success!', result.message);
						}
						else{
							openErrorGritter('Error', result.message);
							$('#tag_visitor').val('');
							$('#tag_visitor').focus();
						}
					});
				}
				else{
					$('#modal_tamu').modal('show');
					$('#tamu').html('<center><b>Tag Anda Tidak Ditemukan</b></center>');
					openErrorGritter('Error!', 'Tag Invalid.');
					$("#tag_visitor").val("");
					$('#tag_visitor').focus();
				}
			}
		});

		function reloadTable() {			
			$('#example1').DataTable().destroy();
			var table = $('#example1').DataTable({
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
				'paging': true,
				'lengthChange': true,
				'pageLength': -1,
				'searching': false,
				'ordering': false,
				'info': true,
				'autoWidth': true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true,
			});

			$('#example2').DataTable().destroy();
			var table = $('#example2').DataTable({
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
				'paging': true,
				'lengthChange': true,
				'pageLength': -1,
				'searching': false,
				'ordering': false,
				'info': true,
				'autoWidth': true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true,
			});

			$('#example1_filter').keyboard();

			$('#example3').DataTable().destroy();
			var table = $('#example3').DataTable({
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
				'paging': true,
				'lengthChange': true,
				'pageLength': -1,
				'searching': false,
				'ordering': false,
				'info': true,
				'autoWidth': true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true,
			});

			$('#example4').DataTable().destroy();
			var table = $('#example4').DataTable({
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
				'paging': true,
				'lengthChange': true,
				'pageLength': -1,
				'searching': false,
				'ordering': false,
				'info': true,
				'autoWidth': true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true,
			});

			$('#example5').DataTable().destroy();
			var table = $('#example5').DataTable({
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
				'paging': true,
				'lengthChange': true,
				'pageLength': -1,
				'searching': false,
				'ordering': false,
				'info': true,
				'autoWidth': true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true,
			});

			$('#example6').DataTable().destroy();
			var table = $('#example6').DataTable({
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
				'paging': true,
				'lengthChange': true,
				'pageLength': -1,
				'searching': false,
				'ordering': false,
				'info': true,
				'autoWidth': true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true,
			});
		}
	</script>
	@endsection