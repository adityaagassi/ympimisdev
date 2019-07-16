@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style>
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
	#loading, #error { display: none; }

	.dataTables_filter {
		display: none;
	} 
</style>
@endsection


@section('header')
<section class="content-header">
	<h1><br><br>
		<center>	<span style="color: white; font-weight: bold; font-size: 28px; text-align: center;">YMPI Visitor Confirmation</span></center>
		{{-- <small>By Shipment Schedule <span class="text-purple">??????</span></small> --}}
	</h1>
	<ol class="breadcrumb" id="last_update">
	</ol>
</section>
@endsection

@section('content')
@if (session('status'))
<div class="alert alert-success alert-dismissible">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
	{{ session('status') }}
</div>   
@endif
@if (session('error'))
<div class="alert alert-danger alert-dismissible">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<h4><i class="icon fa fa-ban"></i> Error!</h4>
	{{ session('error') }}
</div>   
@endif

<div class="row">
	<div class="col-xs-12">
		{{-- <div class="box"> --}}
			
			{{-- TELP --}}
			<div class="col-xs-6">
				<div class="row">
					<div class="col-xs-12">
						<div class="box">
							<div class="box-body">
								<div class="form-group">					
									<div  class="col-xs-11">
										<div class="input-group ">
											<div class="input-group-btn">
												<button type="button" class="btn btn-warning"><i class="fa fa-search"></i>&nbsp;Search</button>
											</div>
											<!-- /btn-group -->
											<input type="text" id="telp" class="form-control" placeholder="Search Line Telephone" >
										</div>
										<!-- /input-group -->
										
									</div>

								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row" id="telpon">
					<div class="col-xs-12">
						<div class="box">
							<div class="box-body">
								<div class="table-responsive">
									<table id="telponlist" class="table table-bordered table-striped table-hover">
										<thead style="background-color: rgba(126,86,134,.7);">											
											<tr>
												<th >Person</th>
												<th >Department</th>
												<th >Telephone</th>												
											</tr>
										</thead>
										<tbody>
										</tbody>
										<tfoot>
											<tr>
												<th></th>
												<th></th>
												<th></th>								
											</tr>
										</tfoot>
									</table>									
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			{{-- CONFIRMATION --}}
			<div class="col-xs-6">
				<div class="row">
					<div class="col-xs-12">
						<div class="box">
							<div class="box-body">
								<div class="form-group">					
									<div  class="col-xs-9">
										<input type="text" id="nikkaryawan" class="form-control" placeholder="Input or Scan NIK">
									</div>
									<div  class="col-xs-3">
										<button class="btn btn-primary"  onclick="inputnik()">Submit</button>
										<button class="btn btn-warning" onclick="hide()">Clear</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row" style="display: none" id="tabelvisior">
					<div class="col-xs-12">
						<div class="box">
							<div class="box-body">
								<div class="table-responsive">
									<table id="visitorlist" class="table table-bordered table-striped table-hover">
										<thead style="background-color: rgba(126,86,134,.7);">
											<tr id="total">
												<th colspan="6"><b id="totalvi"></b></th>								
												<th colspan="2">Action</th>
											</tr>
											<tr>
												<th >Id</th>
												<th >Company</th>
												<th >Full Name</th>
												<th >Total</th>
												<th >Purpose</th>
												<th >Status</th>								
												<th >Remark</th>
												<th>Input Tag</th>
											</tr>
										</thead>
										<tbody>
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
												<th></th>								
											</tr>
										</tfoot>
									</table>
									<div class="modal fade" id="modal-default">
										<div class="modal-dialog modal-lg">
											<div class="modal-content">
												<div class="modal-header" style="background-color: rgba(126,86,134,.7)">
													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">&times;</span></button>
														<h4 class="modal-title">Visitor List</h4>
														<div id="header">

														</div>
													</div>
													<div class="modal-body">
														<div class="form-group">
															<center><input type="text" id="tagvisit" name="tagvisit" placeholder="Input or Scan Visitor Id Card" style="width: 80%" class="form-control " onkeydown="inputag(this.id,this.name)"></center>
														</div>
														<div  class="form-group">

															<input type="text" name="lop" id="lop" value="1" hidden>

															<div class="col-sm-2" style="padding-right: 0;">
																No. KTP/SIM												
															</div>
															<div class="col-sm-4" style="padding-left: 1; padding-right: 0;">
																Full Name												
															</div>
															<div class="col-sm-2" style="padding-left: 1; padding-right: 0;">
																Status										
															</div>
															<div class="col-sm-2">												
																No Hp	
															</div>
															<div class="col-sm-2">
																Tag Number												
															</div><br>
															<div id="apenlist"><br>		
															</div>

														</div>
													</div>
													<div class="modal-footer">
														<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
														<button type="button" class="btn btn-primary" onclick="reloadtable(); ">Save Tag</button>
													</div>
												</div>
												<!-- /.modal-content -->
											</div>
											<!-- /.modal-dialog -->
										</div>
										<!-- /.modal -->
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	@endsection


	@section('scripts')

	<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
	<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
	<script src="{{ url("js/buttons.flash.min.js")}}"></script>
	<script src="{{ url("js/jszip.min.js")}}"></script>
	<script src="{{ url("js/vfs_fonts.js")}}"></script>
	<script src="{{ url("js/buttons.html5.min.js")}}"></script>
	<script src="{{ url("js/buttons.print.min.js")}}"></script>
	<script >
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		jQuery(document).ready(function() { 
			// $('#nikkaryawan').val('asd');
			filltelpon();

			$('.select2').select2({
				dropdownAutoWidth : true,
				width: '100%',
			});
			
		});


		function openSuccessGritter(title, message){
			jQuery.gritter.add({
				title: title,
				text: message,
				class_name: 'growl-success',
				image: '{{ url("images/image-screen.png") }}',
				sticky: false,
				time: '3000'
			});
		}

		function openErrorGritter(title, message) {
			jQuery.gritter.add({
				title: title,
				text: message,
				class_name: 'growl-danger',
				image: '{{ url("images/image-stop.png") }}',
				sticky: false,
				time: '3000'
			});
		}

		function filllist(nik){

			$('#visitorlist tfoot th').each( function () {
				var title = $(this).text();
				$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
			});
			var table = $('#visitorlist').DataTable({
				'dom': 'Bfrtip',
				'responsive': true,
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
					// {
					// 	extend: 'copy',
					// 	className: 'btn btn-success',
					// 	text: '<i class="fa fa-copy"></i> Copy',
					// 	exportOptions: {
					// 		columns: ':not(.notexport)'
					// 	}
					// },
					// {
					// 	extend: 'excel',
					// 	className: 'btn btn-info',
					// 	text: '<i class="fa fa-file-excel-o"></i> Excel',
					// 	exportOptions: {
					// 		columns: ':not(.notexport)'
					// 	}
					// },
					// {
					// 	extend: 'print',
					// 	className: 'btn btn-warning',
					// 	text: '<i class="fa fa-print"></i> Print',
					// 	exportOptions: {
					// 		columns: ':not(.notexport)'
					// 	}
					// },
					]
				},
				'paging'        : true,
				'lengthChange'  : true,
				'searching'     : true,
				'ordering'      : true,
				'info'        : true,
				'order'       : [],
				'autoWidth'   : true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true,
				"serverSide": true,
				"ajax": {
					"type" : "get",
					"url" : "{{ url("visitor_filllist") }}/"+nik+"",
				},				
				"columnDefs": [ {
					"targets": [6],
					"createdCell": function (td, cellData, rowData, row, col) {
						if ( cellData =='Unconfirmed' ) {
							$(td).css('background-color', 'RGB(255,204,255)')
						}
						else
						{
							$(td).css('background-color', 'RGB(204,255,255)')
						}
					}
				}],

				"footerCallback": function (tfoot, data, start, end, display) {
					var intVal = function ( i ) {
						return typeof i === 'string' ?
						i.replace(/[\$,]/g, '')*1 :
						typeof i === 'number' ?
						i : 0;
					};
					var api = this.api();

					var total_diff = api.column(3).data().reduce(function (a, b) {
						return intVal(a)+intVal(b);
					}, 0)
					$('#totalvi').html("Visitor ( "+total_diff.toLocaleString()+" )");
				},

				"columns": [
				{ "data": "id"},
				{ "data": "company"},
				{ "data": "full_name"},
				{ "data": "total"},
				{ "data": "purpose"},
				{ "data": "status"},
				{ "data": "remark"},
				{ "data": "edit"},

			// { "data": "action"}
			]
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
			});

			$('#visitorlist tfoot tr').appendTo('#visitorlist thead');
		}

		function filltelpon(){

			$('#telponlist tfoot th').each( function () {
				var title = $(this).text();
				$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
			});
			var table = $('#telponlist').DataTable({
				'dom': 'Bfrtip',
				'responsive': true,
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
					
					]
				},
				'paging'        : true,
				'lengthChange'  : true,
				'searching'     : true,
				'ordering'      : true,
				'info'        : true,
				'order'       : [],
				'autoWidth'   : true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true,
				"serverSide": true,
				"ajax": {
					"type" : "get",
					"url" : "{{ url("visitor_telpon") }}",
				},			

				"columns": [
				{ "data": "person"},
				{ "data": "dept"},
				{ "data": "nomor"},

				]
			});
			$('#telp').on( 'keyup', function () {
				table.search( this.value ).draw();
			} );
			table.columns().every( function () {
				var that = this;

				$( 'input', this.footer() ).on( 'keyup change', function () {
					if ( that.search() !== this.value ) {
						that
						.search( this.value )
						.draw();
					}
				} );
			});

			$('#telponlist tfoot tr').appendTo('#telponlist thead');
		}


		function editop(id){
			$('#header').empty();
			$("#apenlist").empty();						
			$('#modal-default').modal({backdrop: 'static', keyboard: false});

			var data = {
				id : id
			}
			$.get('{{ url("visitor_getlist") }}', data, function(result, status, xhr){
				console.log(status);
				console.log(result);
				console.log(xhr);
				var no =1;
				if(xhr.status == 200){
					if(result.status){
						$('#header').html();
						$('#apenlist').html();

						$.each(result.header_list, function(key, value) { 
							$('#header').append('<b id="idhead" hidden>'+ value.id +'</b><h4 class="modal-title">'+ value.company +'</h4><h4 class="modal-title">'+ value.name +'</h4><h4 class="modal-title">'+ value.department +'</h4>');
						}); 				

						$.each(result.id_list, function(key, value) {
							if (value.remark =="Confirmed") {
								$bg = "background-color: rgb(204, 255, 255);";
							}else{
								$bg = "background-color: rgb(255, 204, 255);";
							}
							$('#apenlist').append('<div id="'+ value.tag +'" style="'+$bg+'height:20px"><div class="col-sm-2" style="padding-right: 0;"><input readonly type="text" class="form-control" id="visitor_id0" name="visitor_id0" placeholder="No. KTP/SIM" required value="'+ value.id_number +'"></div><div class="col-sm-4" style="padding-left: 1; padding-right: 0;"><input readonly type="text" class="form-control" id="visitor_name0" name="visitor_name0" placeholder="Full Name" required value="'+ value.full_name +'"></div><div class="col-sm-2" style="padding-left: 1; padding-right: 0;"><input readonly type="text" class="form-control" id="status0" name="status0" placeholder="No Hp" value="'+ value.status +'" ></div><div class="col-sm-2" style="padding-left: 1; padding-right: 0;"><input readonly type="text" class="form-control" id="telp0" name="telp0" placeholder="No Hp" value="'+ value.telp +'" ></div><div class="col-sm-2"><input readonly type="text" class="form-control" id="'+ value.id +'" placeholder="Tag Number" name="'+no+'" value="'+ value.tag +'"  autofocus " "></div></div>	<br><br>');
							no++;
						});


						$("[name='tagvisit']").focus(); 		

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

		function inputag(id,name) {

			if (event.keyCode == 13 || event.keyCode == 9) {
				var id = $('#idhead').text();
				var idtag = $('#tagvisit').val();
				// var table = $('#visitorlist').DataTable();
				var data = {
					id:id,
					idtag:idtag                  
				}
				$.post('{{ url("visitor_updateremark") }}', data, function(result, status, xhr){
					console.log(status);
					console.log(result);
					console.log(xhr);
					if(xhr.status == 200){
						if(result.status){							
							openSuccessGritter('Success!', result.message);
							$('#tagvisit').val('');
							$('#'+idtag).css({'background-color':'rgb(204, 255, 255)'})							
						}
						else{
							openErrorGritter('Error!', result.message);
							$('#tagvisit').val('');
						}
					}
					else{
						alert("Disconnected from server");
					}
				});				
			}	
		}

		function reloadtable() {

			$('#visitorlist').DataTable().ajax.reload();
			$('#modal-default').modal('hide');

		}

		function inputnik() {
			
			$('#visitorlist').DataTable().destroy();
			var nik = $('#nikkaryawan').val();
			$('#tabelvisior').css({'display':'block'})
			filllist(nik);
			// alert(nik);
		}

		function hide() {
			$('#tabelvisior').css({'display':'none'})
			$('#nikkaryawan').val('');

		}

		

	</script>
	@endsection