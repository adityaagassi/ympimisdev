@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	table.table-bordered{
		border:1px solid black;
		/*background-color: white;*/
		color:white;
	}
	.table-bordered > thead > tr > th, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > tbody > tr > td, .table-bordered > tfoot > tr > td {
		border: 1px solid black;
		/*font-size: 1vw;*/
		font-weight: bold;
	}
	.table > tbody > tr > th {
		padding: 2px;
		text-align: center;
		color: black;
		background-color: white;
	}
	#assyTable > tbody > tr > td {
		text-align: right;
	}
	#detailTabel {
		color: black;
	}
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0; overflow-y:hidden; overflow-x:scroll;">
	<div class="row">
		<div class="col-xs-12">
			<form method="GET" action="{{ action('AssyProcessController@indexDisplayAssy') }}">
				<div class="col-xs-2" style="line-height: 1">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border-color: #00a65a">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="tgl" name="date" placeholder="Select Date" style="border-color: #00a65a" <?php if (isset($_GET['date'])): ?>
						<?php echo "value=".$_GET['date']; endif ?>>
					</div>
					<br>
				</div>
				<div class="col-xs-2">
					<select class="form-control select2" multiple="multiple" id="key" onchange="change()" data-placeholder="Select Key">
						@foreach($keys as $key)
						<option value="{{ $key->key }}">{{ $key->key }}</option>
						@endforeach
					</select>
					<input type="text" name="key2" id="dd" hidden>
				</div>
				<div class="col-xs-1">
					<select class="form-control select2" multiple="multiple" id="modelselect" onchange="changeModel()" data-placeholder="Select Model">
						@foreach($models as $model)
						<option value="{{ $model->model }}">{{ $model->model }}</option>
						@endforeach
					</select>
					<input type="text" name="model2" id="model2" hidden>
				</div>
				<div class="col-xs-2">
					<select class="form-control select2" id="surface" multiple="multiple" onchange="changeSurface()" data-placeholder="Select Surface">
						@foreach($surfaces as $surface)
						<option value="{{ $surface[0] }}">{{ $surface[1] }}</option>
						@endforeach
					</select>
					<input type="text" name="surface2" id="surface2" hidden>
				</div>

				<div class="col-xs-1">
					<select class="form-control select2" id="hpl" multiple="multiple" onchange="changeHpl()" data-placeholder="Select HPL">
						@foreach($hpls as $hpl)
						<option value="{{ $hpl }}">{{ $hpl }}</option>
						@endforeach
					</select>
					<input type="text" name="hpl2" id="hpl2" hidden>
				</div>
				<div class="col-xs-1">
					<button class="btn btn-success" type="submit">Cari</button>
				</div>
				<div class="col-xs-3">
					<center><div id="judul" style="color:white; font-weight: bold; font-size: 2vw"></div></center>
				</div>
			</form>
		</div>
		<div class="col-xs-12">
			<table id="assyTable" class="table table-bordered" style="padding: 0px; margin-bottom: 0px;">
				<tr id="model">
				</tr>
				<tr id="plan">
					<!-- <th>Total Plan</th> -->
				</tr>
				<tr id="picking">
					<!-- <th>Picking</th> -->
				</tr>
				<tr id="diff">
					<!-- <th>Diff</th> -->
				</tr>
				<tr style="height: 5px"></tr>
				<tr id="stok">
					
				</tr>
			</table>

			<!-- <table class="table table-bordered" style="padding: 0px; margin-bottom: 10px;">
				
			</table> -->
		</div>
		<div class="col-xs-12">
			<div id="picking_chart" style="width: 100%; margin: auto"></div>
		</div>

		<div class="modal fade" id="myModal">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h4 style="float: right; " id="modal-title"></h4> 
						<h4 class="modal-title"><b id="titel"></b></h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<table class="table table-bordered table-stripped table-responsive" style="width: 100%" id="detailTabel">
									<thead style="background-color: rgba(126,86,134,.7);">
										<tr>
											<th>Tag</th>
											<th>GMC</th>
											<th>Description</th>
											<th>Quantity</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
									<tfoot style="background-color: rgba(126,86,134,.7);">
										<tr>
											<th colspan="3" style="text-align:right">Total : </th>
											<th></th>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
					</div>
				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
	</div>

</section>
@endsection
@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		fill_table();
		setInterval(fill_table, 20000);

		var kunci = "{{$_GET['key2']}}";
		var kuncies = kunci.split(",");
		var kunciFilter = [];
		ctg = "";

		for(var i = 0; i < kuncies.length; i++){
			ctg = kuncies[i].charAt(0);

			if(kunciFilter.indexOf(ctg) === -1){
				kunciFilter[kunciFilter.length] = ctg;
			}
		}
		// alert(kunciFilter);

		$("#judul").text(kunciFilter+"-{{$_GET['model2']}}-{{$_GET['surface2']}}-{{$_GET['hpl2']}}");


		$('.select2').select2();

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});
	});

	function change() {
		$("#dd").val($("#key").val());
	}

	function changeModel() {
		$("#model2").val($("#modelselect").val());
	}
	function changeSurface() {
		$("#surface2").val($("#surface").val());
	}
	function changeHpl() {
		$("#hpl2").val($("#hpl").val());
	}

	function fill_table() {
		var data = {
			tanggal:"{{$_GET['date']}}",
			key:"{{$_GET['key2']}}",
			model:"{{$_GET['model2']}}",
			surface:"{{$_GET['surface2']}}",
			hpl:"{{$_GET['hpl2']}}"
		}

		// var values="{{$_GET['key2']}}";
		// $.each(values.split(","), function(i,e){
		// 	$("#key option[value='" + e + "']").prop("selected", true);
		// });

		$.get('{{ url("fetch/display/sub_assy") }}', data, function(result, status, xhr){
			if(result.status){
				$("#model").empty();
				$("#plan").empty();
				$("#picking").empty();
				$("#diff").empty();

				$("#stok").empty();

				model = "<th style='width:45px'>#</th>";
				totplan = "<th>Plan</th>";
				picking = "<th>Pick</th>";
				diff = "<th>Diff</th>";

				stk = "<th style='border: 1px solid white'>Stok room</th>";

				var style = "";

				$.each(result.plan, function(index, value){
					var minus = 0;
					// var picking = 0;

					if (value.diff < 0) {
						style = "style='background-color:#00a65a';";
					} else {
						style = "style='background-color:#f24b4b';";
					}

					if (value.model.charAt(0) == 'A') {
						color = "style='background-color:#80ed5f';";
					} else {
						color = "style='background-color:#f2e127';";
					}

					if (value.diff >= (-value.stock)) {
						color2 = "background-color:#00a65a";
					} else {
						color2 = "background-color:#f24b4b";
					}

					model += "<th "+color+">"+value.model+"<br/>"+value.key+"<br/>"+value.surface+"</th>";
					totplan += "<td>"+value.plan+"</td>";
					picking += "<td>"+value.picking+"</td>";
					diff += "<td "+style+">"+(-value.diff)+"</td>";

					stk += "<td style='border: 1px solid white;"+color2+"'>"+value.stock+"</td>";
				})

				$("#model").append(model);
				$("#plan").append(totplan);
				$("#picking").append(picking);
				$("#diff").append(diff);

				$("#stok").append(stk);
				

				// -------- CHART ------------

				var stockroom = [];
				var middle = [];
				var welding = [];

				var categories = [];

				$.each(result.stok, function(index2, value2){
					middle.push(parseInt(value2.middle));
					stockroom.push(parseInt(value2.stockroom));
					welding.push(parseInt(value2.welding));

					categories.push(value2.model+" "+value2.key+" "+value2.surface);
				})

				// console.table(result.stok);

				Highcharts.chart('picking_chart', {
					chart: {
						type: 'column',
						width: $('#assyTable').width(),
						marginLeft: 40
					},
					title: {
						text: null
					},
					xAxis: {
						categories: categories
					},
					yAxis: {
						min: 0,
						title: {
							enabled: false
						},
						stackLabels: {
							enabled: true,
							style: {
								fontWeight: 'bold',
								color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
							}
						},
						labels: {
							useHTML:true,
							style:{
								width:'10px',
								whiteSpace:'normal'
							},
						},
						tickInterval: 10
					},
					tooltip: {
						headerFormat: '<b>{point.x}</b><br/>',
						pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
					},
					plotOptions: {
						column: {
							stacking: 'normal',
							dataLabels: {
								enabled: true,
								color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
							},
							animation: false,
						},
						series: {
							cursor: 'pointer',
							pointPadding: -0.25,
							events: {
								click: function(event) {
									openModal(event.point.category, this.name)
								}
							}
						}
					},
					credits :{
						enabled: false,
					},
					series: [{
						name: 'Welding',
						data: welding
					}, {
						name: 'Middle',
						data: middle
					}, {
						name: 'Stockroom',
						data: stockroom
					}]
				});
			}
		})
	}

	function openModal(kunci, lokasi) {
		$("#myModal").modal("show");
		$("#titel").text(kunci+" ("+lokasi+")");

		$('#detailTabel').DataTable().destroy();

		var data = {
			model:kunci.split(" ")[0],
			key:kunci.split(" ")[1],
			surface:kunci.split(" ")[2],
			location:lokasi
		}

		var table = $('#detailTabel').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": false,
			"bAutoWidth": false,
			"processing": true,
			"serverSide": false,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/detail/sub_assy") }}",
				"data" : data
			},
			"columns": [
			{ "data": "tag", "width" : "10%" },
			{ "data": "material_number", "width" : "10%" },
			{ "data": "material_description", "width" : "70%" },
			{ "data": "quantity", "width" : "10%", "className": "text-right"}
			],
			"footerCallback": function ( row, data, start, end, display ) {
				var api = this.api(), data;

            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
            	return typeof i === 'string' ?
            	i.replace(/[\$,]/g, '')*1 :
            	typeof i === 'number' ?
            	i : 0;
            };

            // Total over all pages
            total = api
            .column( 3 )
            .data()
            .reduce( function (a, b) {
            	return intVal(a) + intVal(b);
            }, 0 );

            // Total over this page
            pageTotal = api
            .column( 3, { page: 'current'} )
            .data()
            .reduce( function (a, b) {
            	return intVal(a) + intVal(b);
            }, 0 );

            // Update footer
            $( api.column( 3 ).footer() ).html(
            	total
            	);
        }
    });

	}

</script>
@endsection