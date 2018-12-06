@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<style type="text/css">

input {
	line-height: 24px;
}

</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		Completion <span class="text-purple">????</span>
		{{-- <small>Material stock details <span class="text-purple">????</span></small> --}}
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">Detail Filters<span class="text-purple"> フィルター詳細</span></span></h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="col-md-4 col-md-offset-2">
						<div class="form-group">
							<label>Prod. Date From</label>
							<div class="input-group date">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control pull-right" id="dateFrom" name="dateFrom">
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label>Prod. Date To</label>
							<div class="input-group date">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control pull-right" id="dateTo" name="dateTo">
							</div>
						</div>
					</div>
					<div class="col-md-4 col-md-offset-2">
						<div class="form-group">
							<label>Completion Status</label>
							<select class="form-control select2" name="transactionStatus" id='transactionStatus' data-placeholder="Select Status" style="width: 100%;">
								<option value="All">All</option>
								@foreach($transaction_statuses as $transaction_status)
								<option value="{{ $transaction_status }}">{{ $transaction_status }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label>Origin Group</label>
							<select class="form-control select2" multiple="multiple" name="originGroup" id='originGroup' data-placeholder="Select Origin Group" style="width: 100%;">
								<option></option>
								@foreach($origin_groups as $origin_group)
								<option value="{{ $origin_group->origin_group_code }}">{{ $origin_group->origin_group_code }} - {{ $origin_group->origin_group_name }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-4 col-md-offset-2">
						<div class="form-group">
							<label class="control-label">Material Number</label>
							<textarea id="materialArea" class="form-control" rows="3" placecholder="Paste barcode number from excel here"></textarea>
							<input id="materialTags" type="text" placeholder="Material Number" class="form-control tags" name="material_numbers" />
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Storage Location</label>
							<textarea id="locationArea" class="form-control" rows="3" placecholder="Paste location from excel here"></textarea>
							<input id="locationTags" type="text" placeholder="Material Number" class="form-control tags" name="locations" />
						</div>
					</div>			
					<div class="col-md-4 col-md-offset-6">
						<div class="form-group pull-right">
							<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
							<button id="search" onClick="fetchTable()" class="btn btn-primary">Search</button>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<table id="completionTable" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th style="font-size: 14">Material</th>
										<th style="font-size: 14">Description</th>
										<th style="font-size: 14">SLoc</th>
										<th style="font-size: 14">Quantity</th>
										<th style="font-size: 14">Dest.</th>
										<th style="font-size: 14">Upload</th>
										<th style="font-size: 14">Completed At</th>
									</tr>
								</thead>
								<tbody id="completionTableBody">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
									<th>Total</th>
									<th></th>
									<th></th>
									<th id="totalQty"></th>
									<th></th>
									<th></th>
									<th></th>
								</tfoot>
							</table>
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
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
{{-- <script src="{{ url("js/pdfmake.min.js")}}"></script> --}}
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('#dateFrom').datepicker({
			autoclose: true
		});
		$('#dateTo').datepicker({
			autoclose: true
		});
		jQuery('.tags').tagsInput({ width: 'auto' });
		$('.select2').select2();
		$('#materialTags').hide();
		$('#materialTags_tagsinput').hide();
		$('#locationTags').hide();
		$('#locationTags_tagsinput').hide();
		initKeyDown();
	});

	function initKeyDown() {
		$('#materialArea').keydown(function(event) {
			if (event.keyCode == 13) {
				convertMaterialToTags();
				return false;
			}
		});
		$('#locationArea').keydown(function(event) {
			if (event.keyCode == 13) {
				convertLocationToTags();
				return false;
			}
		});
	}

	function convertMaterialToTags() {
		var data = $('#materialArea').val();
		if (data.length > 0) {
			var rows = data.split('\n');
			if (rows.length > 0) {
				for (var i = 0; i < rows.length; i++) {
					var barcode = rows[i].trim();
					if (barcode.length > 0) {
						$('#materialTags').addTag(barcode);
					}
				}
				$('#materialTags').hide();
				$('#materialTags_tagsinput').show();
				$('#materialArea').hide();
			}
		}
	}

	function convertLocationToTags() {
		var data = $('#locationArea').val();
		if (data.length > 0) {
			var rows = data.split('\n');
			if (rows.length > 0) {
				for (var i = 0; i < rows.length; i++) {
					var barcode = rows[i].trim();
					if (barcode.length > 0) {
						$('#locationTags').addTag(barcode);
					}
				}
				$('#locationTags').hide();
				$('#locationTags_tagsinput').show();
				$('#locationArea').hide();
			}
		}
	}

	function clearConfirmation(){
		location.reload(true);
	}

	function fetchTable(){
		$('#completionTable').DataTable().destroy();
		var dateFrom = $('#dateFrom').val();
		var dateTo = $('#dateTo').val();
		var originGroup = $('#originGroup').val();
		var transactionStatus = $('#transactionStatus').val();
		var materialNumber = $('#materialTags').val();
		var storageLocation = $('#locationTags').val();
		var data = {
			dateFrom:dateFrom,
			dateTo:dateTo,
			originGroup:originGroup,
			transactionStatus:transactionStatus,
			materialNumber:materialNumber,
			storageLocation:storageLocation,
		}
		$.get('{{ url("fetch/tr_completion") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#completionTableBody').html("");
					var tableData = '';
					var totalQty = 0;
					$.each(result.tableData, function(key, value) {
						totalQty += value.quantity;
						tableData += '<tr>';
						tableData += '<td>'+ value.material_number +'</td>';
						tableData += '<td>'+ value.material_description +'</td>';
						tableData += '<td>'+ value.issue_storage_location +'</td>';
						tableData += '<td>'+ value.quantity +'</td>';
						tableData += '<td>'+ value.destination +'</td>';
						if(value.completion != '-'){
							tableData += '<td><a href="javascript:void(0)" id="'+ value.completion +'" onClick="downloadFile(id)">'+ value.completion +'</a></td>';
						}
						else{
							tableData += '<td>'+ value.completion +'</td>';
						}
						tableData += '<td>'+ value.created_at +'</td>';
						tableData += '</tr>';
					});
					$('#completionTableBody').append(tableData);
					$('#totalQty').html('');
					$('#totalQty').append(totalQty.toLocaleString());

					$('#completionTable').DataTable({
						'dom': 'Bfrtip',
						"scrollX": true,
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

	function downloadFile(id){
		var data = {
			referenceFile : id
		}
		$.get('{{ url("download/tr_completion") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				var file_path = result.file_path;
				var a = document.createElement('A');
				a.href = file_path;
				a.download = file_path.substr(file_path.lastIndexOf('/') + 1);
				document.body.appendChild(a);
				a.click();
				document.body.removeChild(a);

			}
			else{
				alert('Disconnected from server');
			}
		});
	}

</script>
@endsection

