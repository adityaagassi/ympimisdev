@extends('layouts.visitor')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
	}
	tbody>tr>th{
		text-align:center;
		background-color: #757575;
		color: white;
	}
	tfoot>tr>th{
		text-align:center;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid rgb(150,150,150) !important;
		font-size: 14px;
		padding: 4px;
		color: white;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
		border-collapse: collapse;
		padding:5px;
		vertical-align: middle;
		color: white;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}

	#loading, #error { display: none; }
	h2{
		font-size: 70px;
		font-weight: bold;
	}

	.td {
		font-size: 30px;
	}

	.th {
		font-size: 20px;
	}

	.td:hover {
		background-color: white !important;
		color: black !important;
	}

	
</style>
@stop
@section('header')
<section class="content-header" style="text-align: center;">

</section>
@stop
@section('content')
<section class="content" style="padding-top: 0px;">
	<div class="row" style="margin-bottom: 1%;">
		<div class="col-xs-3">
			<div class="input-group date">
				<div class="input-group-addon bg-olive" style="border: none;">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control datepicker" id="tanggal" placeholder="Select Date" autocomplete="off">
			</div>
		</div>
		<div class="col-xs-1">
			<button id="search" style="width: 100%" onClick="detailAll($('#tanggal').val())" class="btn bg-olive">Download All</button>
		</div>
		<form method="GET" action="{{ url("export/mirai_mobile/report_location") }}">
			<div class="col-xs-1">
				<button type="submit" style="width: 100%" class="btn btn-success"><i class="fa fa-download"></i> Excel</button>
			</div>
		</form>
		<div class="col-xs-3 pull-right">
			<p class="pull-right" id="last_update"></p>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12 col-md-12" style="margin-left: 0px; padding: 0px;">
			<div class="col-lg-12" style="margin-bottom: 1%;">
				<div class="table-responsive">
					<table class="table table-bordered" style="background-color: #212121">
						<thead style="background-color: #757575">
							<tr>
								<th style="vertical-align: middle;width:5%;font-size: 20px;background-color: white;color: black" id="dept_head">Departemen</th>
							</tr>
						</thead>
						<tbody id="tableBodyResult">
							<tr>
								<th><i class="fa fa-spinner fa-pulse"></i> Loading . . .</th>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="col-lg-12" style="margin-bottom: 1%;">
				<div id="container2" style="width: 100%;"></div>
			</div>  
		</div>
	</div>

	<div class="modal fade in" id="modalDetail" >
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span></button>
						<h4 class="modal-title" id="modalTitle"></h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-xs-12">
								<table class="table" id="tableDetail">
								<thead>
									<tr>
										<th>Tanggal</th>
										<th>ID Karyawan</th>
										<th>Nama Karyawan</th>
										<th>Departemen</th>
										<th>Kota Absensi</th>
										<th>Kota Domisili</th>
									</tr>
								</thead>
								<tbody id="bodyDetail">
								</tbody>
							</table>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
					</div>
				</div>

			</div>

		</div>

	<div class="modal fade in" id="modalDetailAll" >
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span></button>
						<h4 class="modal-title" id="modalTitleAll"></h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-xs-12">
								<table class="table" id="tableDetailAll">
								<thead>
									<tr>
										<th>Tanggal</th>
										<th>ID Karyawan</th>
										<th>Nama Karyawan</th>
										<th>Departemen</th>
										<th>Kota Absensi</th>
										<th>Kota Domisili</th>
									</tr>
								</thead>
								<tbody id="bodyDetailAll">
								</tbody>
							</table>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
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
	<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
	<script src="{{ url("js/highcharts.js")}}"></script>
	<script src="{{ url("js/exporting.js")}}"></script>
	<script src="{{ url("js/export-data.js")}}"></script>
	<script>
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		jQuery(document).ready(function() {
			$('.datepicker').datepicker({
				<?php $tgl_max = date('Y-m-d') ?>
				autoclose: true,
				format: "yyyy-mm-dd",
				todayHighlight: true,	
				endDate: '<?php echo $tgl_max ?>'
			});
			$('#last_update').html('<i class="fa fa-clock-o"></i> Last Seen: '+ getActualFullDate());
			$('#last_update').css('color','white');
			$('#last_update').css('font-weight','bold');

			drawNumber();
		});


		function exp(){
			$.get('{{ url("export/mirai_mobile/report_location") }}', function(result, status, xhr){

			});
		}

		function addZero(i) {
			if (i < 10) {
				i = "0" + i;
			}
			return i;
		}

		function getActualFullDate() {
			var d = new Date();
			var day = addZero(d.getDate());
			var month = addZero(d.getMonth()+1);
			var year = addZero(d.getFullYear());
			var h = addZero(d.getHours());
			var m = addZero(d.getMinutes());
			var s = addZero(d.getSeconds());
			return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
		}

		function drawNumber(){

			var fy = $('#fy').val();

			var data = {
				fy: fy,
			};

			$.get('{{ url("fetch/mirai_mobile/report_location") }}', data, function(result, status, xhr){
				$("#tableBodyResult").empty();
				if(result.status){
					var date = [];
					$("#dept_head").after("<th style='vertical-align: middle;width:2%;font-size: 20px;background-color: white;color: black'>Jumlah</th>");
					$.each(result.period, function(key, value) {
						$("#dept_head").after("<th style='vertical-align: middle;width:2%;font-size: 20px;;background-color: white;color: black'>"+value.week_date+"</th>");
						date.push(value.week_date);
					})
					body = "";

					var arr = [];
					var jumlah = [];
					var jumlahperdate = [];

					$.each(result.emp_location, function(key, value) {
						arr.push(value.department);
					});

					arr = unique(arr);
					var index = 0;
					$.each(arr, function(key, value) {
						if (index%2 == 0) {
							var bgcolor = "#575C57";
						}else{
							var bgcolor = "#3C3C3C";
						}
						index++;
						var a = 0;
						var b = 0;
						body += "<tr>";
						body += "<th class='th'>"+value+"</th>";
						for(var j = date.length-1;j>=0;j--){

							var jml = "-";
							$.each(result.emp_location, function(key, value2) {
								if (value == value2.department) {
									if (date[j] == value2.answer_date) {
										jml = value2.jumlah;
										a = a+jml;
									}
								}
							});
							if (jml == "-") {
								body += "<td class='td' style='color:#4ff05a;background-color:"+bgcolor+";font-weight:bold'>"+jml+"</td>";
							}else{
								body += "<td class='td' style='color:#FFD03A;background-color:"+bgcolor+";font-weight:bold' onClick='detail(\""+value+"\",\""+date[j]+"\")'>"+jml+"</td>";
							}
						}
						var tanggal = "";
						body += "<td class='td' style='background-color:red;font-weight:bold' onClick='detail(\""+value+"\",\""+tanggal+"\")'>"+a+"</td>";
						body += "</tr>";
					})
					for(var j = date.length-1;j>=0;j--){
						var jmldate = 0;
						$.each(result.emp_location, function(key, value2) {
							if (date[j] == value2.answer_date) {
								jmldate = jmldate + value2.jumlah;
							}
						});
						jumlahperdate.push(jmldate);
					}
					body += "<tr>";
					body += "<th class='th'>Jumlah</th>";
					var jmlall = 0;
					jumlahperdate.reverse();
					for(var j = date.length-1;j>=0;j--){
						var value = "";
						body += "<td class='td' style='background-color:red;font-weight:bold' onClick='detail(\""+value+"\",\""+date[j]+"\")'>"+jumlahperdate[j]+"</td>";
						jmlall = jmlall + jumlahperdate[j];
					}
					var value = "";
					var tanggal = "";
					body += "<td class='td' style='background-color:red;font-weight:bold' onClick='detail(\""+value+"\",\""+tanggal+"\")'>"+jmlall+"</td>";
					body += "</tr>";
					$("#tableBodyResult").append(body);
				}

			});

		}

		function detail(department, tanggal) {
			var data = {
				department : department,
				date : tanggal
			}

			if (department === "") {
				$("#modalTitle").text("All Department | "+tanggal);
			}else if(tanggal === ""){
				$("#modalTitle").text(department+" | All Date");
			}else if(department === "" && tanggal === ""){
				$("#modalTitle").text("All Department | All Date");
			}else{
				$("#modalTitle").text(department+" | "+tanggal);
			}

			$.get('{{ url("fetch/mirai_mobile/report_location/detail") }}', data, function(result, status, xhr){
				$("#modalDetail").modal("show");
				$('#tableDetail').DataTable().clear();
	        	$('#tableDetail').DataTable().destroy();
				body = "";
				$("#bodyDetail").html("");
				$.each(result.location_detail, function(key, value) {
					body += "<tr>";
					body += "<td>"+value.answer_date+"</td>";
					body += "<td>"+value.employee_id+"</td>";
					body += "<td>"+value.name+"</td>";
					body += "<td>"+value.department+"</td>";
					body += "<td>"+value.city+"</td>";
					body += "<td>"+value.kota+"</td>";
					body += "</tr>";
				});
				$("#bodyDetail").append(body);

				$('#tableDetail tfoot th').each( function () {
		          var title = $(this).text();
		          $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
		        } );
		        var table = $('#tableDetail').DataTable({
		          'dom': 'Bfrtip',
		          'responsive':true,
		          'lengthMenu': [
		          [ 5, 10, 25, -1 ],
		          [ '5 rows', '10 rows', '25 rows', 'Show all' ]
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
		          'pageLength': 15,
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

		        table.columns().every( function () {
		          var that = this;

		          $( 'input', this.footer() ).on( 'keyup change', function () {
		            if ( that.search() !== this.value ) {
		              that
		              .search( this.value )
		              .draw();
		            }
		          } );
		        } );

		        $('#tableDetail tfoot tr').appendTo('#tableDetail thead');
			});
		}

		function detailAll(tanggal) {
			var data = {
				date : tanggal
			}
			
			$("#modalTitleAll").text("All Department | "+tanggal);

			$.get('{{ url("fetch/mirai_mobile/report_location/detail_all") }}', data, function(result, status, xhr){
				$("#modalDetailAll").modal("show");
				$('#tableDetailAll').DataTable().clear();
	        	$('#tableDetailAll').DataTable().destroy();
				body = "";
				$("#bodyDetailAll").html("");

				$.each(result.location_detail, function(key, value) {
					body += "<tr>";
					body += "<td>"+value.answer_date+"</td>";
					body += "<td>"+value.employee_id+"</td>";
					body += "<td>"+value.name+"</td>";
					body += "<td>"+value.department+"</td>";
					body += "<td>"+value.city+"</td>";
					body += "<td>"+value.kota+"</td>";
					body += "</tr>";
				});

				$("#bodyDetailAll").append(body);

				$('#tableDetailAll tfoot th').each( function () {
		          var title = $(this).text();
		          $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
		        } );
		        var table = $('#tableDetailAll').DataTable({
		          'dom': 'Bfrtip',
		          'responsive':true,
		          'lengthMenu': [
		          [ 5, 10, 25, -1 ],
		          [ '5 rows', '10 rows', '25 rows', 'Show all' ]
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
		          'pageLength': 15,
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

		        table.columns().every( function () {
		          var that = this;

		          $( 'input', this.footer() ).on( 'keyup change', function () {
		            if ( that.search() !== this.value ) {
		              that
		              .search( this.value )
		              .draw();
		            }
		          } );
		        } );

		        $('#tableDetailAll tfoot tr').appendTo('#tableDetailAll thead');
			});
		}

		function unique(list) {
			var result = [];
			$.each(list, function(i, e) {
				if ($.inArray(e, result) == -1) result.push(e);
			});
			return result;
		}
	</script>
	@endsection