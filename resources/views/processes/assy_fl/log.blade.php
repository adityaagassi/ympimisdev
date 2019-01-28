@extends('layouts.master')
@section('stylesheets')
<style type="text/css">
thead>tr>th{
	text-align:center;
}
tbody>tr>td{
	text-align:center;
}
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		Log Process Flute <span class="text-purple">???</span>
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
			<table id="logTable" style="width: 100%;" class="table table-bordered table-hover table-striped">
				<thead>
					<tr>
						<th style="width: 10%;">Serial Number</th>
						<th style="width: 10%;">Model</th>
						<th style="width: 20%;">Stamp-Kariawase</th>
						<th style="width: 20%;">Tanpoawase</th>
						<th style="width: 20%;">Yuge</th>
						<th style="width: 20%;">Chousei</th>
						<th style="width: 20%;">Status</th>
					</tr>
				</thead>
				<tbody id="logTableBody">
				</tbody>
				<tfoot>
					<tr>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
{{-- <script src="{{ url("js/pdfmake.min.js")}}"></script> --}}
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
		fetchTableLog();
	});

	function fetchTableLog(){
		$.get('{{ url("fetch/logTableFl") }}', function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#logTableBody').html("");
					var tableBody = '';
					$.each(result.logs, function(key, value) {
						tableBody += '<tr>';
						tableBody += '<td>'+value.serial_number+'</td>';
						tableBody += '<td>'+value.model+'</td>';
						tableBody += '<td>'+value.kariawase+'</td>';
						tableBody += '<td>'+value.tanpoawase+'</td>';
						tableBody += '<td>'+value.yuge+'</td>';
						tableBody += '<td>'+value.chousei+'</td>';
						tableBody += '<td>'+value.status+'</td>';
						tableBody += '</tr>';
					});
					$('#logTableBody').append(tableBody);
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
</script>
@endsection