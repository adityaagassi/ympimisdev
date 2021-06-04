@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
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
	padding-top: 0px;
	padding-bottom: 0px;
}
table.table-bordered > tfoot > tr > th{
	border:1px solid rgb(211,211,211);
}
#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header" >
	<h1>
		{{ $title }}<span class="text-purple"> {{ $title_jp }}</span>
	</h1>
</section>
@stop
@section('content')
<section class="content" >
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12" style="text-align: center;">
			<div class="row">
				<div class="col-xs-8 col-xs-offset-1" style="padding-right: 0px;padding-left: 0px">
					<input type="text" id="tag_product" placeholder="Scan Kanban Here . . ." style="width: 100%;font-size: 20px;text-align:center;padding: 10px">
				</div>
				<div class="col-xs-1" style="padding-right: 0px;padding-left: 0px">
					<button class="btn btn-danger" onclick="cancel()" style="width:100%;font-size: 20px;height:50px;font-weight: bold;">
						CANCEL
					</button>
				</div>
				<div class="col-xs-1" style="padding-right: 0px;padding-left: 0px">
					<button class="btn btn-warning" onclick="location.reload()" style="width:100%;font-size: 20px;height:50px;font-weight: bold;">
						REFRESH
					</button>
				</div>
			</div>
		</div>

		<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;padding-top: 10px">
			<div class="col-md-12">
				<div class="box box-solid">
					<div class="box-body">
						<center><span style="font-size: 25px;text-align: center;font-weight: bold;">KANBAN CLEANING HISTORY (<?php echo strtoupper(date('01 M Y')) ?> - <?php echo strtoupper(date('d M Y')) ?>)</span> </center>
						<table id="tableHistory" class="table table-bordered table-striped table-hover" style="width: 100%">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 1%">Material</th>
									<th style="width: 2%">Desc</th>
									<th style="width: 2%">No. Kanban</th>
									<th style="width: 1%">Part</th>
									<th style="width: 1%">Color</th>
									<th style="width: 1%">Cavity</th>
									<th style="width: 1%">Qty</th>
									<th style="width: 2%">At</th>
								</tr>
							</thead>
							<tbody id="tableHistoryBody">
							</tbody>
						</table>
					</div>
				</div>
			</div>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var counter = 0;
	var arrPart = [];
	var intervalCheck;

	jQuery(document).ready(function() {
		fetchKanbanHistory();
		$('#resultScanBody').html("");
      	$('body').toggleClass("sidebar-collapse");
		$("#tag_product").val("");
		$('#tag_product').focus();
	});

	$('#tag_product').keyup(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if ($('#tag_product').val().length == 10) {
				$('#tag_product').prop('disabled',true);
				if (confirm('Apakah Anda yakin akan menghapus Kanban? Pastikan tidak ada material tersisa.')) {
					var tag = $('#tag_product').val();
					var data = {
						tag:tag
					}
					$.post('{{ url("remove/injection/tag") }}', data, function(result, status, xhr){
						if (result.status) {
							openSuccessGritter('Success',result.message);
							$('#tag_product').removeAttr('disabled');
							$('#tag_product').val('');
							$('#tag_product').focus();
							$('#loading').hide();
							fetchKanbanHistory();
						}else{
							$('#tag_product').removeAttr('disabled');
							$('#tag_product').val('');
							$('#tag_product').focus();
							openErrorGritter('Error!',result.message);
							$('#loading').hide();
						}
					});
				}else{
					$('#tag_product').removeAttr('disabled');
					$('#tag_product').val('');
					$('#tag_product').focus();
					$('#loading').hide();
				}
			}else{
				$('#tag_product').removeAttr('disabled');
				$('#tag_product').val('');
				$('#tag_product').focus();
				openErrorGritter('Error!','Tag Invalid');
				$('#loading').hide();
			}
		}
	});

	function cancel() {
		$('#tag_product').removeAttr('disabled');
		$('#tag_product').val('');
		$('#tag_product').focus();
	}

	function fetchKanbanHistory() {
		$.get('{{ url("fetch/injection/clean_kanban") }}',  function(result, status, xhr){
			if (result.status) {
				$('#tableHistoryBody').html("");
				$('#tableHistory').DataTable().clear();
				$('#tableHistory').DataTable().destroy();
				var table = "";
				$.each(result.datas, function(key, value) {
					table += '<tr>';
					table += '<td>'+value.material_number+'</td>';
					table += '<td>'+value.mat_desc+'</td>';
					table += '<td>'+value.no_kanban+'</td>';
					table += '<td>'+value.part_type+'</td>';
					table += '<td>'+value.color+'</td>';
					table += '<td>'+value.cavity+'</td>';
					table += '<td>'+value.shot+'</td>';
					table += '<td>'+value.created+'</td>';
					table += '</tr>';
				});
				$('#tableHistoryBody').append(table);
				$('#tableHistory').DataTable({
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
						},
						]
					},
					'paging': true,
					'lengthChange': true,
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
			}else{
				openErrorGritter('Error!',result.message);
			}
		});
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '2000'
		});
	}
</script>
@endsection