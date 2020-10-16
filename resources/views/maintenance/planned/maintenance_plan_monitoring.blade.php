@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/fixedHeader.dataTables.min.css") }}" rel="stylesheet">
<style type="text/css">
	html {
		transition: color 300ms, background-color 300ms;
	}


	thead>tr>th{
		text-align:center;
		color:white;
		font-weight: bold;
		font-size: 12pt;
		border-bottom: 1px solid white;
		background-color: #955da8;
		padding: 2px;
		border-right: 1px solid white;
	}
	tbody>tr>td{
		text-align:center;
		color:black;
		/*border-top: 1px solid #333333 !important;*/
		/*font-weight: bold;*/
		font-size: 16px;
	}

	tbody>tr>th{
		text-align:left;
		color:black;
		/*border-top: 1px solid #333333 !important;*/
		font-weight: bold;
		font-size: 16px;
	}

	.datepicker table tr td span.focused, .datepicker table tr td span:hover {
		background: #955da8;
	}
	tfoot>tr>th{
		text-align:center;
		color:white;
	}
	td:hover {
		overflow: visible;
	}
	table {
		/*background-color: #212121;*/
	}

	#master>tbody>tr>td {
		padding: 2px;
	}

	.card-title {
		font-family: inherit;
		font-weight: 500;
		line-height: 1.2;
		font-size: 25px;
	}

	/*table.fixedHeader-floating{
		background-color: #212121 !important;
		color: white;
		}*/

		#loading, #error { display: none; }
	</style>
	@stop
	@section('header')
	<section class="content-header">
		<input type="hidden" id="green">
		<h1>
			{{ $page }}
		</h1>
	</section>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="col-xs-2 pull-right">
				<div class="input-group date">
					<div class="input-group-addon bg-purple" style="border: none;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="tgl" onchange="change_date(this)" placeholder="Pilih Bulan">
				</div>
			</div>
		</div>
		<div class="col-xs-12" style="padding-top: 10px;">
			<div class="card" style="background-color: white; padding: 15px; border-radius: 2px;">
				<div class="card-body">
					{{-- <h5 class="card-title">Daily</h5> --}}
					<table style="width: 100%" border="1" id="master">
						<tr>
							<th style="width: 1%">Daily</th>
							<td style="width: 1%">4</td>
							<td style="width: 1%">8</td>
							<td style="width: 1%">12</td>
						</tr>
						<tr>
							<td>1</td>
							<td>5</td>
							<td>9</td>
						</tr>
						<tr>
							<td>2</td>
							<td>6</td>
							<td>10</td>
						</tr>
						<tr>
							<td>3</td>
							<td>7</td>
							<td>11</td>
						</tr>
					</table>
				</div>
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
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.fixedHeader.min.js") }}"></script>
<script src="{{ url("js/dataTables.responsive.min.js") }}"></script>

<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var mons = ['april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december', 'january', 'february', 'march']

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		getData();
	})

	function getData() {
		var data = {

		}

		$.get('{{ url("fetch/maintenance/pm/schedule") }}', data, function(result, status, xhr){
			var len = result.datas.length / 4;
			len = Math.floor(len);

			// var body = "";
			body += "<tr><th style='width: 1%'>Daily</th>";
			for (var i = 0; i < result.datas; i++) {
				body += "<th style='width: 1%'>"+result.datas[i].machine_name+"</th>";
			}
		})
	}

	$('#tgl').datepicker({
		autoclose: true,
		format: "yyyy-mm",
		startView: "months", 
		minViewMode: "months",
	});

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

</script>
@endsection