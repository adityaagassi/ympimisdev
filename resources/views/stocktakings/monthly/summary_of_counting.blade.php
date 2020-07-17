@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<style type="text/css">
	input {
		line-height: 24px;
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
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <span class="text-purple">{{ $title_jp }}</span>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">Detail Filters<span class="text-purple"> フィルター詳細</span></span></h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					
					<div class="row">
						<div class="col-xs-6 col-xs-offset-3">					
							<div class="form-group">
								<label class="control-label">Store</label>
								<textarea id="receiveStoreArea" class="form-control" rows="3" placecholder="Paste location from excel here"></textarea>
								<input id="receiveStoreTags" type="text" placeholder="Material Number" class="form-control tags" name="receiveStoreTags" />
							</div>
							<div class="form-group pull-right">
								<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
								<button id="search" onClick="print()" class="btn btn-success">Print</button>
								<button id="search" onClick="fetchTable()" class="btn btn-primary">Search</button>
							</div>
						</div>
					</div>

					

					<div class="row">
						<div class="col-md-12">
							<table id="storeTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width:5%;">Group</th>
										<th style="width:5%;">Location</th>
										<th style="width:5%;">Store</th>
										<th style="width:5%;">Category</th>
										<th style="width:10%;">Material Number</th>
										<th style="width:25%;">Material Desc</th>
										<th style="width:5%;">Key</th>
										<th style="width:5%;">Model</th>
										<th style="width:5%;">Surface</th>
										<th style="width:5%;">UoM</th>
										<th style="width:5%;">Lot</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
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
		$('body').toggleClass("sidebar-collapse");

		jQuery('.tags').tagsInput({ width: 'auto' });

		$('#issueStoreTags').hide();
		$('#issueStoreTags_tagsinput').hide();
		$('#issueStoreTags').hide();
		$('#receiveStoreTags_tagsinput').hide();
		initKeyDown();
	});

	function initKeyDown() {
		
		$('#receiveStoreArea').keydown(function(event) {
			if (event.keyCode == 13) {
				convertReceiveStoreToTags();
				return false;
			}
		});
	}

	function convertReceiveStoreToTags() {
		var data = $('#receiveStoreArea').val();
		if (data.length > 0) {
			var rows = data.split('\n');
			if (rows.length > 0) {
				for (var i = 0; i < rows.length; i++) {
					var barcode = rows[i].trim();
					if (barcode.length > 0) {
						$('#receiveStoreTags').addTag(barcode);
					}
				}
				$('#receiveStoreTags').hide();
				$('#receiveStoreTags_tagsinput').show();
				$('#receiveStoreArea').hide();
			}
		}		
	}

	function clearConfirmation(){
		location.reload(true);
	}

	function print() {
		var store = $("#receiveStoreTags").val();

		var data = {
			store:store
		}

		$("#loading").show();

		$.get('{{ url("print/stocktaking/summary_of_counting") }}', data, function(result, status, xhr){
			if(result.status){
				$("#loading").hide();
				openSuccessGritter('Success', result.message);

			} else {
				$("#loading").hide();
				audio_error.play();
				openErrorGritter('Error', result.message);
			}

		});
	}

	function fetchTable() {
		$('#storeTable').DataTable().destroy();

		var store = $("#receiveStoreTags").val();

		var data = {
			store:store
		}

		$('#storeTable').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			"pageLength": 25,
			'buttons': {
				buttons:[
				{
					extend: 'pageLength',
					className: 'btn btn-default',
				}
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
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/stocktaking/summary_of_counting") }}",
				"data" : data
			},
			"columns": [
			{ "data": "area"},
			{ "data": "location"},
			{ "data": "store"},
			{ "data": "category"},
			{ "data": "material_number"},
			{ "data": "material_description"},
			{ "data": "key"},
			{ "data": "model"},
			{ "data": "surface"},
			{ "data": "bun"},
			{ "data": "lot"}
			]
		});

	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '4000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '4000'
		});
	}

</script>
@endsection

