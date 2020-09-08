@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
		color:white;
		font-weight: bold;
		font-size: 12pt;
	}
	tbody>tr>td{
		text-align:center;
		color:white;
		border-top: 1px solid #333333 !important;
	}
	tfoot>tr>th{
		text-align:center;
		color:white;
	}
	td:hover {
		overflow: visible;
	}
	table {
		background-color: #212121;
	}

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
	<div class="col-md-12" style="padding-top: 10px;">
		<div class="row">
			<table id="masterTable" class="table">
				<thead>
					<tr>
						<th rowspan='2' style="width: 3%">ORDER NO.</th>
						<th rowspan='2' style="border-left: 3px solid #f44336; width: 12%">REQUESTER</th>
						<th rowspan='2' style="border-left: 3px solid #f44336; width: 5%">PRIORITY</th>
						<th rowspan='2' style="border-left: 3px solid #f44336;">JOB TYPE</th>
						<th rowspan='2' style="border-left: 3px solid #f44336; width: 12%">PIC</th>
						<th colspan="3" style="border-left: 3px solid #f44336; width: 5%">STATUS</th>
						<th rowspan='2' style="border-left: 3px solid #f44336; width: 25%">ESTIMATED TIME</Tth>
						</tr>
						<tr>
							<th style="border-left: 3px solid #f44336">REQUESTED</th>
							<th>TARGET</th>
							<th>START</th>
						</tr>
					</thead>
					<tbody id="tableBody">
					</tbody>
					<tfoot>
					</tfoot>
				</table>
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

<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		get_data('all');

		setInterval( function() { get_data('all'); }, 10000 );
	})

	function get_data(param) {
		var data = {
			status:param
		}
		$.get('{{ url("fetch/maintenance/spk/monitoring") }}', data, function(result, status, xhr){
			$('#tableBody').html("");

			var tableData = "";

			$.each(result.datas, function(index, value){
				var stat = 0;
				var progress = "0%";
				var cls_prog = "progress-bar-success";

				$.each(result.progress, function(index2, value2){
					if (value.order_no == value2.order_no) {
						stat = 1;

						tmp = (value2.act_time / value2.plan_time * 100).toFixed(0);

						if (tmp == 'Infinity') {
							progress = "500%";
							cls_prog = "progress-bar-danger";
						} else {
							progress = tmp+"%";
							cls_prog = "progress-bar-success";
						}
					}
				})

				tableData += '<tr>';
				tableData += '<td>'+ value.order_no +'</td>';
				tableData += '<td style="border-left: 3px solid #f44336">'+ value.requester +'</td>';

				if(value.priority == 'Urgent'){
					var priority = '<span style="font-size: 13px;" class="label label-danger">Urgent</span>';
				}else{
					var priority = '<span style="font-size: 13px;" class="label label-default">Normal</span>';
				}

				tableData += '<td style="border-left: 3px solid #f44336">'+ priority +'</td>';
				tableData += '<td style="border-left: 3px solid #f44336">'+ value.type +' - '+ value.category +'</td>';
				tableData += '<td style="border-left: 3px solid #f44336">'+ (value.pic || '-') +'</td>';
				tableData += '<td style="border-left: 3px solid #f44336"><span class="label label-success">'+ value.request_date +'</span></td>';
				tableData += '<td><span class="label label-success">'+ (value.target_date || '-') +'</span></td>';

				if (value.inprogress) {
					tableData += '<td><span class="label label-success">'+ (value.inprogress || '-') +'</span></td>';
				} else {
					tableData += '<td>-</td>';
				}

				tableData += '<td style="border-left: 3px solid #f44336">';
				tableData += '<div class="progress active" style="background-color: #212121; height: 25px; border: 1px solid; padding: 0px; margin: 0px;">';
				tableData += '<div class="progress-bar '+cls_prog+' progress-bar-striped" id="progress_bar_'+index+'" style="font-size: 12px; padding-top: 0.5%; width: '+progress+'" aria-valuemin="0" aria-valuemax="100">'+progress+'</div>';
				tableData += '</td>';

				tableData += '</tr>';	
			})


			$('#tableBody').append(tableData);
		})
	}

	function showDetail(order_no) {
		$("#detailModal").modal("show");

		var data = {
			order_no : order_no
		}

		$.get('{{ url("fetch/maintenance/detail") }}', data,  function(result, status, xhr){
			$("#spk_detail").val(result.detail.order_no);
			$("#pengaju_detail").val(result.detail.name);
			$("#tanggal_detail").val(result.detail.date);
			$("#bagian_detail").val(result.detail.section);

			if (result.detail.priority == "Normal") {
				$("#prioritas_detail").addClass("label-default");
			} else {
				$("#prioritas_detail").addClass("label-danger");
			}
			$("#prioritas_detail").text(result.detail.priority);

			$("#workType_detail").val(result.detail.type);
			$("#kategori_detail").val(result.detail.category);
			$("#mesin_detail").val(result.detail.machine_condition);
			$("#bahaya_detail").val(result.detail.danger);
			$("#uraian_detail").val(result.detail.description);
			$("#keamanan_detail").val(result.detail.safety_note);
			$("#target_detail").val(result.detail.target_date);
			$("#status_detail").val(result.detail.process_name);
		})
	}

	function insert() {
		$("#tanggal").val();
		$("#bagian").val();
		$("#prioritas").val();
		$("#jenis_pekerjaan").val();
		$("#kondisi_mesin").val();
		$("#bahaya").val();
		$("#detail").val();
		$("#target").val();
		$("#safety").val();
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