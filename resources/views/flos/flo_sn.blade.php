@extends('layouts.master')
@section('stylesheets')
<style>
table {
	table-layout:fixed;
}
td{
	overflow:hidden;
	text-overflow: ellipsis;
}

td:hover {
	overflow: visible;
}
/*table {*/
	/*margin: 0 auto;*/
	/*width: 100%;*/
	/*clear: both;*/
	/*border-collapse: collapse;*/
	/*table-layout: fixed;         // add this */
	/*word-wrap:break-word;        // add this */
	/*}*/
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Final Line Outputs
		<small>Band Instrument</small>
	</h1>
	<ol class="breadcrumb">
		{{-- <li><a href="{{ url("create/destination")}}" class="btn btn-primary btn-sm" style="color:white">Create {{ $page }}</a></li> --}}
	</ol>
</section>
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-danger">
				<div class="box-header">
					<h3 class="box-title">FLO <i class="fa fa-angle-right"></i> Print</h3>
				</div>
				<!-- /.box-header -->
				<form class="form-horizontal" role="form" method="post" action="{{url('print/flo_sn')}}">
					<div class="box-body">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="form-group">
							<label for="material_number" class="col-sm-1 control-label">Material</label>

							<div class="col-md-4">
								<select class="form-control select2" name="material_number" style="width: 100%;" data-placeholder="Choose a Material..." id="material_number" required>
									<option value=""></option>
									@foreach($materials as $material)
									<option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
									@endforeach
								</select>

							</div>
							<button type="submit" class="btn btn-danger col-sm-14"><i class="fa fa-print"></i>&nbsp;&nbsp;Print FLO</button>
						</div>
						<!-- /.box-body -->
					</div>
				</form>
				<!-- /.box -->
			</div>
			<!-- /.col -->
		</div>

		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">FLO <i class="fa fa-angle-right"></i> Fulfillment</h3>
				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="row">
						<div class="col-md-4">
							<br>
							<label>Scan FLO Number</label>
							<div class="input-group col-md-12">
								
								<div class="input-group-addon">
									<i class="glyphicon glyphicon-barcode"></i>
								</div>
								<input style="text-align: center" type="text" class="form-control" id="flo_number" name="flo_number" placeholder="Enter FLO Number" required>
							</div>
							<div class="input-group col-md-12">
								<hr id="line-flo" style="border: 1px solid #3498DB">
							</div>
							<div class="input-group col-md-12">
								<div class="input-group-addon" id="icon-material">
									<i class="glyphicon glyphicon-barcode"></i>
								</div>
								<input type="text" class="form-control" id="material" name="material" placeholder="Enter Material Number" required>
							</div>
							&nbsp;
							<div class="input-group col-md-12">
								<div class="input-group-addon" id="icon-serial">
									<i class="glyphicon glyphicon-barcode"></i>
								</div>
								<input type="text" class="form-control" id="serial" name="serial" placeholder="Enter Serial Number" required>
							</div>
							<br>
							<div class="input-group col-md-12">
								<center><button id="finish" class="btn btn-danger col-sm-14"><i class="fa fa-minus-circle"></i>&nbsp;&nbsp;Finish</button></center>
							</div>
						</div>
						<div class="col-md-8">
							<div class="form-group">
								<table id="flo_table" class="table table-bordered table-striped">
									<thead>
										<tr>
											<th>#</th>
											<th>Material</th>
											<th>Description</th>
											<th>Serial</th>
											<th>Del.</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
									{{-- @foreach($destinations as $destination) --}}
{{-- 										<tr>
											<td style="font-size: 14">999</td>
											<td style="font-size: 14">WWWWWWW</td>
											<td style="font-size: 14">WWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWW</td>
											<td style="font-size: 14">99999999999</td>
											<td>
												<button class="btn btn-danger btn-sm">
													<i class="glyphicon glyphicon-trash"></i>
													
												</button>
											</td>
										</tr> --}}
										{{-- @endforeach --}}
									</table>
								</div>
							</div>
						</div>
					</div>
					<!-- /.box-body -->
				</div>
				<!-- /.box -->
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->

	</section>


</section>
@stop

@section('scripts')

<script>
	$(function () {
    //Initialize Select2 Elements
    $('.select2').select2()
});


	jQuery(document).ready(function() {
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		$("#material").hide();
		$("#serial").hide();
		$("#icon-material").hide();
		$("#icon-serial").hide();
		$("#line-flo").hide();
		$("#finish").hide();
		$("#flo_table").hide();
		$("#flo_number").val("");
		$("#material").val("");
		$("#serial").val("");

		// $('#flo_table').DataTable({
		// 	'paging'      	: false,
		// 	'lengthChange'	: false,
		// 	'searching'   	: false,
		// 	'ordering'    	: false,
		// 	'info'       	: true,
		// 	'autoWidth'		: false,
		// 	"sPaginationType": "full_numbers",
		// 	"bJQueryUI": true,
  //   		"bAutoWidth": false, // Disable the auto width calculation 
  //   		"aoColumns": [
  //     			{ "sWidth": "2%" }, // 1st column width 
  //     			{ "sWidth": "12%" }, // 2nd column width 
  //     			{ "sWidth": "67%" },
  //     			{ "sWidth": "12%" },
  //     			{ "sWidth": "4%" } // 3rd column width and so on 
  //     			],
  //     			"infoCallback": function( settings, start, end, max, total, pre ) {
  //     				return " Total "+ total +" pc(s)";
  //     			}
  //     		});

		// $("#flo_number").on("input", function() {
		// 	delay(function(){
		// 		if ($("#flo_number").val().length < 8) {
		// 			$("#flo_number").val("");
		// 		}
		// 	}, 20 );
		// });


		$('#flo_number').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#flo_number").val().length > 0){
					scanFLO();
					return false;
				}
			}
		})


		$('#material').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#material").val().length > 0){
					scanMaterial();
					return false;
				}
			}
		})

		$('#serial').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#serial").val().length > 0){
					scanSerial();
					return false;
				}
			}
		})

	});

	function scanFLO() {

		$("#material").show();
		$("#serial").show();
		$("#icon-material").show();
		$("#icon-serial").show();
		$("#line-flo").show();
		$("#finish").show();
		$("#flo_table").show();
		$("#flo_number").prop('disabled', true);
		$("#serial").prop('disabled', true);
		
		var token = '{{ Session::token() }}';
		var flo_number = $("#flo_number").val();
		var data = {
			flo_number: flo_number,
			_token: token
		};
		$('#flo_table').DataTable( {
			'paging'      	: false,
			'lengthChange'	: false,
			'searching'   	: false,
			'ordering'    	: false,
			'info'       	: true,
			'autoWidth'		: false,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false, // Disable the auto width calculation 
			"infoCallback": function( settings, start, end, max, total, pre ) {
				return " Total "+ total +" pc(s)";
			},
			"processing": true,
			"serverSide": true,

			"ajax": {
				"type" : "post",
				"url" : "{{ url("scan/flo_number_sn") }}",
				"data": data
			},
			"columns": [
			{ "data": null },
			{ "data": "material_number" },
			{ "data": "material_description" },
			{ "data": "serial_number" },
			{ "data": null },
			],
			success: function(data) {
				console.log(data);
			}
		});
		$("#material").focus();
	}

	function scanMaterial(){
	// create validation of material number here
	$("#serial").prop('disabled', false);
	$("#material").prop('disabled', true);
	$("#serial").focus();
}

function scanSerial(){
	// create content of FLO here
	$("#serial").prop('disabled', true);
	$("#material").prop('disabled', false);
	$("#material").val("");
	$("#serial").val("");
	$("#material").focus();
}

var delay = (function(){
	var timer = 0;
	return function(callback, ms){
		clearTimeout (timer);
		timer = setTimeout(callback, ms);
	};
})();
</script>
@stop