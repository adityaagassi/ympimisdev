@extends('layouts.master')
@section('stylesheets')
<style type="text/css">
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
		margin-bottom: 0px;
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
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">Filters <span class="text-purple"></span></h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-3">
							<div class="form-group">
								<label>Prod. Date</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker" id="date" name="date">
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Selct Loacation</label>
								<select class="form-control select2" multiple="multiple" data-placeholder="Select Location" name="location" id="location" style="width: 100%;">
									@foreach($locations as $location) 
									<option value="{{ $location }}">{{ $location }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>

					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-6">
							
							<div class="form-group pull-right">
								<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
								<button id="search" onClick="fillTable()" class="btn btn-primary">Search</button>
							</div>
						</div>
					</div>			
					<div class="row">
						<div class="col-md-12">
							<div style="overflow-x:auto; margin-bottom: 1%;">
								<table id="shift3" class="table table-bordered table-striped" style="overflow: auto;">
									<thead id="head3" style="background-color: rgba(126,86,134,.7);"></thead>
									<tbody id="body3"></tbody>
								</table>
							</div>
							<div style="overflow-x:auto; margin-bottom: 1%;">
								<table id="shift1" class="table table-bordered table-striped" style="overflow: auto;">
									<thead id="head1" style="background-color: rgba(126,86,134,.7);"></thead>
									<tbody id="body1"></tbody>
								</table>
							</div>
							<div style="overflow-x:auto; margin-bottom: 1%;">
								<table id="shift2" class="table table-bordered table-striped" style="overflow: auto;">
									<thead id="head2" style="background-color: rgba(126,86,134,.7);"></thead>
									<tbody id="body2"></tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
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
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('#date').datepicker({
			autoclose: true
		});
		$('.select2').select2({
		});

		fillTable();
	});

	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		autoclose: true,
		format: "yyyy-mm-dd",
		endDate: '<?php echo $tgl_max ?>'
	});

	function clearConfirmation(){
		location.reload(true);		
	}

	function fillTable(){

		var date = $('#date').val();
		var location = $('#location').val();

		var data = {
			date:date,
			location:location
		}

		$.get('{{ url("fetch/middle/report_hourly_lcq") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					$('#head3').append().empty();
					$('#body3').append().empty();
					$('#head1').append().empty();
					$('#body1').append().empty();
					$('#head2').append().empty();
					$('#body2').append().empty();
					
					var jam = [ ['01.00','03.00','05.00','07.00'],
					['09.00','11.00','14.00','16.00'],
					['18.00','20.00','22.00','24.00'] ];


					//Start Shift 3
					var head = '';
					head += '<tr>';
					head += '<th colspan=2>Shift 3</th>';
					for (var i = 0; i < result.key.length; i++) {
						head += '<th colspan=2>'+result.key[i].kunci+'</th>';
					}
					head += '<th>82Z</th>';
					head += '<th colspan=2>Total</th>';
					head += '</tr>';
					head += '<tr>';
					head += '<th style="width:11%;">Tanggal</th>';
					head += '<th style="width:7%;">Jam</th>';
					for (var i = 0; i < result.key.length; i++) {
						head += '<th style="width:4%;" valign=middle>AS</th>';
						head += '<th style="width:4%;" valign=middle>TS</th>';	
					}
					head += '<th style="width:4%;" valign=middle>AS</th>';
					head += '<th style="width:4%;" valign=middle>AS</th>';
					head += '<th style="width:4%;" valign=middle>TS</th>';
					head += '</tr>';

					var body = '';
					for (var i = 0; i < result.dataShift3.length; i++) {
						body += '<tr>';

						body += '<td>'+result.tanggal+'</td>';
						body += '<td>'+jam[0][i]+'</td>';

						var sum_as = 0;
						var sum_ts = 0;

						for (var j = 0; j < result.key.length; j++) {
							//Alto
							var isAsEmpty =  true;
							for (var k = 0; k < result.dataShift3[i].length; k++) {
								if((result.dataShift3[i][k].hpl == 'ASKEY') && (result.key[j].kunci == result.dataShift3[i][k].kunci)){
									body += '<td style="background-color: #ffff66;color:black;">'+result.dataShift3[i][k].jml+'</td>';
									sum_as += result.dataShift3[i][k].jml;
									isAsEmpty = false;
								}
							}
							if(isAsEmpty){
								body += '<td style="background-color: #ffff66;color:black;">0</td>';
							}

							//Tenor
							var isTsEmpty =  true;
							for (var k = 0; k < result.dataShift3[i].length; k++) {
								if((result.dataShift3[i][k].hpl == 'TSKEY') && (result.key[j].kunci == result.dataShift3[i][k].kunci)){
									body += '<td style="background-color: rgb(157, 255, 105);color:black;">'+result.dataShift3[i][k].jml+'</td>';
									sum_ts += result.dataShift3[i][k].jml;
									isTsEmpty = false;
								}
							}
							if(isTsEmpty){
								body += '<td style="background-color: rgb(157, 255, 105);color:black;">0</td>';
							}

						}

						if(result.z3[i].length > 0){
							body += '<td style="background-color: #434348;color:white;">'+result.z3[i][0].jml+'</td>';
							sum_as += result.z3[i][0].jml;
						}else{
							body += '<td style="background-color: #434348;color:white;">0</td>';
						}

						body += '<td>'+sum_as+'</td>';
						body += '<td>'+sum_ts+'</td>';
						body += '</tr>';
					}

					$('#head3').append(head);
					$('#body3').append(body);
					//End Shift 3
					

					//Start Shift 1
					var head = '';
					head += '<tr>';
					head += '<th colspan=2>Shift 1</th>';
					for (var i = 0; i < result.key.length; i++) {
						head += '<th colspan=2>'+result.key[i].kunci+'</th>';
					}
					head += '<th>82Z</th>';
					head += '<th colspan=2>Total</th>';
					head += '</tr>';
					head += '<tr>';
					head += '<th style="width:11%;">Tanggal</th>';
					head += '<th style="width:7%;">Jam</th>';
					for (var i = 0; i < result.key.length; i++) {
						head += '<th style="width:4%;" valign=middle>AS</th>';
						head += '<th style="width:4%;" valign=middle>TS</th>';	
					}
					head += '<th style="width:4%;" valign=middle>AS</th>';
					head += '<th style="width:4%;" valign=middle>AS</th>';
					head += '<th style="width:4%;" valign=middle>TS</th>';
					head += '</tr>';

					var body = '';
					for (var i = 0; i < result.dataShift1.length; i++) {
						body += '<tr>';

						body += '<td>'+result.tanggal+'</td>';
						body += '<td>'+jam[1][i]+'</td>';

						var sum_as = 0;
						var sum_ts = 0;

						for (var j = 0; j < result.key.length; j++) {
							//Alto
							var isAsEmpty =  true;
							for (var k = 0; k < result.dataShift1[i].length; k++) {
								if((result.dataShift1[i][k].hpl == 'ASKEY') && (result.key[j].kunci == result.dataShift1[i][k].kunci)){
									body += '<td style="background-color: #ffff66;color:black;">'+result.dataShift1[i][k].jml+'</td>';
									sum_as += result.dataShift1[i][k].jml;
									isAsEmpty = false;
								}
							}
							if(isAsEmpty){
								body += '<td style="background-color: #ffff66;color:black;">0</td>';
							}

							//Tenor
							var isTsEmpty =  true;
							for (var k = 0; k < result.dataShift1[i].length; k++) {
								if((result.dataShift1[i][k].hpl == 'TSKEY') && (result.key[j].kunci == result.dataShift1[i][k].kunci)){
									body += '<td style="background-color: rgb(157, 255, 105);color:black;">'+result.dataShift1[i][k].jml+'</td>';
									sum_ts += result.dataShift1[i][k].jml;
									isTsEmpty = false;
								}
							}
							if(isTsEmpty){
								body += '<td style="background-color: rgb(157, 255, 105);color:black;">0</td>';
							}

						}

						if(result.z1[i].length > 0){
							body += '<td style="background-color: #434348;color:white;">'+result.z1[i][0].jml+'</td>';
							sum_as += result.z1[i][0].jml;
						}else{
							body += '<td style="background-color: #434348;color:white;">0</td>';
						}

						body += '<td>'+sum_as+'</td>';
						body += '<td>'+sum_ts+'</td>';
						body += '</tr>';
					}

					$('#head1').append(head);
					$('#body1').append(body);
					//End Shift 1
					

					//Start Shift 2
					var head = '';
					head += '<tr>';
					head += '<th colspan=2>Shift 2</th>';
					for (var i = 0; i < result.key.length; i++) {
						head += '<th colspan=2>'+result.key[i].kunci+'</th>';
					}
					head += '<th>82Z</th>';
					head += '<th colspan=2>Total</th>';
					head += '</tr>';
					head += '<tr>';
					head += '<th style="width:11%;">Tanggal</th>';
					head += '<th style="width:7%;">Jam</th>';
					for (var i = 0; i < result.key.length; i++) {
						head += '<th style="width:4%;" valign=middle>AS</th>';
						head += '<th style="width:4%;" valign=middle>TS</th>';	
					}
					head += '<th style="width:4%;" valign=middle>AS</th>';
					head += '<th style="width:4%;" valign=middle>AS</th>';
					head += '<th style="width:4%;" valign=middle>TS</th>';
					head += '</tr>';

					var body = '';
					for (var i = 0; i < result.dataShift2.length; i++) {
						body += '<tr>';

						body += '<td>'+result.tanggal+'</td>';
						body += '<td>'+jam[2][i]+'</td>';

						var sum_as = 0;
						var sum_ts = 0;

						for (var j = 0; j < result.key.length; j++) {
							//Alto
							var isAsEmpty =  true;
							for (var k = 0; k < result.dataShift2[i].length; k++) {
								if((result.dataShift2[i][k].hpl == 'ASKEY') && (result.key[j].kunci == result.dataShift2[i][k].kunci)){
									body += '<td style="background-color: #ffff66;color:black;">'+result.dataShift2[i][k].jml+'</td>';
									sum_as += result.dataShift2[i][k].jml;
									isAsEmpty = false;
								}
							}
							if(isAsEmpty){
								body += '<td style="background-color: #ffff66;color:black;">0</td>';
							}

							//Tenor
							var isTsEmpty =  true;
							for (var k = 0; k < result.dataShift2[i].length; k++) {
								if((result.dataShift2[i][k].hpl == 'TSKEY') && (result.key[j].kunci == result.dataShift2[i][k].kunci)){
									body += '<td style="background-color: rgb(157, 255, 105);color:black;">'+result.dataShift2[i][k].jml+'</td>';
									sum_ts += result.dataShift2[i][k].jml;
									isTsEmpty = false;
								}
							}
							if(isTsEmpty){
								body += '<td style="background-color: rgb(157, 255, 105);color:black;">0</td>';
							}

						}

						if(result.z2[i].length > 0){
							body += '<td style="background-color: #434348;color:white;">'+result.z2[i][0].jml+'</td>';
							sum_as += result.z2[i][0].jml;
						}else{
							body += '<td style="background-color: #434348;color:white;">0</td>';
						}

						body += '<td>'+sum_as+'</td>';
						body += '<td>'+sum_ts+'</td>';
						body += '</tr>';
					}

					$('#head2').append(head);
					$('#body2').append(body);
					//End Shift 2
				}
			}

		});

}

</script>
@endsection