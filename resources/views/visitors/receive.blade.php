@extends('layouts.visitor')
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
</style>
@endsection


@section('header')
<section class="content-header">
	<h1>
		<center>	<span style="color: white; font-weight: bold; font-size: 28px; text-align: center;">YMPI Visitor Registration</span></center>
		<br>
	</h1>
	<ol class="breadcrumb" id="last_update">
	</ol>
	{{-- <button class="btn btn-warning btn-lg pull-right" onclick="leavemodal()">Visitor Leave</button><br><br> --}}
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

		<div class="box box-primary  collapsed-box">
			<div class="box-header with-border">
				<h3 class="box-title">Visitor Leave</h3>

				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
					</button>
					
				</div>
			</div>
			<div class="box-body" style="">
				<div class=" col-xs-12">
					<div class="box" style="min-height: 500px">
						<div class="box-header with-border">
							<button type="button" class="btn btn-primary pull-right" onclick="reloadtable();">Clear</button><br><br>

							<div class="form-group">					
								<div  class="col-xs-12">
									<div class="input-group " >

										<div class="input-group-btn" >
											<button type="button" class="btn btn-primary btn-lg" ><i class="glyphicon glyphicon-barcode"></i>&nbsp;</button>
										</div>
										<!-- /btn-group -->
										<input type="text" value="" id="telp" class="form-control btn-lg" placeholder="Scan or Input Tag" style="width: 99%; height: 100%" onkeydown="postvisit()">
									</div>
								</div>
							</div>				

							{{-- <center><b>Visitor List</b></center> --}}<br><br>

							<div class="form-group" >
								<div class="col-sm-2">
									<b style="font-size: 20px">	ID Number </b>
								</div>
								<div class="col-sm-2" >
									<b style="font-size: 20px">	Name </b>
								</div>
								<div class="col-sm-1" >
									<b style="font-size: 20px">	In - Time </b>
								</div>
								<div class="col-sm-2" >
									<b style="font-size: 20px">	Company </b>
								</div>
								<div class="col-sm-2" >
									<b style="font-size: 20px">	Employee </b>
								</div>
								<div class="col-sm-1" >
									<b style="font-size: 20px">	Department </b>
								</div>
								<div class="col-sm-2 " >
									<b style="font-size: 20px">	Reason </b>

								</div>
							</div>


							<div id="apen" >	


							</div>	
						</div>


					</div>
				</div>
			</div>
			<!-- /.box-body -->
		</div>


		<div class="box">
			<div class="box-body">
				<div class="table-responsive">
					<table id="visitorlist" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr id="total">
								<th colspan="7"><b id="totalvi"></b></th>
								<th colspan="2">Employee</th>
								<th colspan="4">Action</th>
							</tr>
							<tr>
								<th >Id</th>
								<th >Date</th>
								<th >Company</th>
								<th >Full Name</th>
								<th >Total</th>
								<th >Purpose</th>
								<th >Status</th>
								<th >Name</th>
								<th >Department</th>
								<th >In Time</th>
								<th >Out Time</th>
								<th >Meet</th>
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
										<button type="button" class="btn btn-primary" onclick="reloadtable();">Save Tag</button>
									</div>
								</div>
								<!-- /.modal-content -->
							</div>
							<!-- /.modal-dialog -->
						</div>
						<!-- /.modal -->



						<div class="modal fade" id="modal-default-leave">
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
											<div class="row">
												
											</div>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
											<button type="button" class="btn btn-primary" onclick="reloadtable();">Save</button>
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
				filllist();

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

			function filllist(){
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
						"url" : "{{ url("visitor_filllist") }}/asd",
					},				
					"columnDefs": [ {
						"targets": [11],
						"createdCell": function (td, cellData, rowData, row, col) {
							if ( cellData == null || cellData == 'Unconfirmed' ) {
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

						var total_diff = api.column(4).data().reduce(function (a, b) {
							return intVal(a)+intVal(b);
						}, 0)
						$('#totalvi').html("Visitor ( "+total_diff.toLocaleString()+" )");
					},

					"columns": [
					{ "data": "id"},
					{ "data": "created_at2"},
					{ "data": "company"},
					{ "data": "full_name"},
					{ "data": "total"},
					{ "data": "purpose"},
					{ "data": "status"},
					{ "data": "name"},
					{ "data": "department"},
					{ "data": "in_time"},
					{ "data": "out_time"},
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
								$('#header').append('<h4 class="modal-title">'+ value.company +'</h4><h4 class="modal-title">'+ value.name +'</h4><h4 class="modal-title">'+ value.department +'</h4>');
							}); 				

							$.each(result.id_list, function(key, value) {
								$('#apenlist').append('<div class="col-sm-2" style="padding-right: 0;"><input type="text" class="form-control" id="visitor_id0" name="visitor_id0" placeholder="No. KTP/SIM" required value="'+ value.id_number +'"></div><div class="col-sm-4" style="padding-left: 1; padding-right: 0;"><input type="text" class="form-control" id="visitor_name0" name="visitor_name0" placeholder="Full Name" required value="'+ value.full_name +'"></div><div class="col-sm-2" style="padding-left: 1; padding-right: 0;"><input type="text" class="form-control" id="status0" name="status0" placeholder="No Hp" value="'+ value.status +'" ></div><div class="col-sm-2" style="padding-left: 1; padding-right: 0;"><input type="text" class="form-control" id="telp0" name="telp0" placeholder="No Hp" value="'+ value.telp +'" ></div><div class="col-sm-2"><input type="text" class="form-control" id="V'+ value.id +'" placeholder="Tag Number" name="'+no+'" value="'+ value.tag +'"  autofocus onkeydown="inputag(this.id,this.name)"" "></div>	<br><br>');
								no++;
							});


							$("[name='1']").focus(); 		

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
				var tag = parseInt(name)+1;
				if (event.keyCode == 13 || event.keyCode == 9) {
					var idtag = $('#'+id).val();
					// alert(idtag);
				// var table = $('#visitorlist').DataTable();
				var data = {
					id:id,
					idtag:idtag                  
				}
				$.post('{{ url("visitor_inputtag") }}', data, function(result, status, xhr){
					console.log(status);
					console.log(result);
					console.log(xhr);
					if(xhr.status == 200){
						if(result.status){
							$("[name='"+(tag)+"']").focus(); 
							openSuccessGritter('Success!', result.message);
							
						}
						else{
							openErrorGritter('Error!', result.message);
						}
					}
					else{
						alert("Disconnected from server");
					}
				});				
			}	
		}

		function reloadtable() {
			$("#apen").empty();	
			$('#visitorlist').DataTable().ajax.reload();
			$('#modal-default').modal('hide');
			$('#modal-default-leave').modal('hide');

		}


		//-----------------leave


		function leavemodal() {
			$("#apen").empty();	
			$('#modal-default-leave').modal({backdrop: 'static', keyboard: false});
			$('#modal-default-leave').modal('show');

		}

		function postvisit(tag){
			if (event.keyCode == 13 || event.keyCode == 9) {

				var id =	$('#telp').val();
				var data = {
					id : id
				}
				$.get('{{ url("visitor_getvisit") }}', data, function(result, status, xhr){
					console.log(status);
					console.log(result);
					console.log(xhr);
					var no =1;
					if(xhr.status == 200){
						if(result.status){
							$('#apen').html();

							$.each(result.ops, function(key, value) {

								if(value.remark == 'Belum Ditemui'){
									$('#apen').append('<div class="col-xs-12"> <div class="col-sm-1" > <br><b style="font-size: 18px" id="visitor_id0" name="visitor_id0" >'+ value.id_number +'</b> </div> <div class="col-sm-2" > <br><b style="font-size: 18px" id="visitor_id0" name="visitor_id0" >'+ value.full_name +'</b> </div> <div class="col-sm-1" > <br><b style="font-size: 18px" id="visitor_id0" name="visitor_id0" >'+ value.in_time +'</b> </div> <div class="col-sm-2" > <br><b style="font-size: 18px" id="visitor_id0" name="visitor_id0" >'+ value.company +'</b> </div> <div class="col-sm-2" > <br><b style="font-size: 18px" id="visitor_id0" name="visitor_id0" >'+ value.name +'</b> </div> <div class="col-sm-2" > <br><b style="font-size: 18px" id="visitor_id0" name="visitor_id0" >'+ value.department +'</b> </div></div>');
									openErrorGritter('Error!', 'Visitor Not Confirmed');
								}
								else{
									out(id);
									openSuccessGritter('Success!', result.message);
								}
							}); 

							if (result.ops=="") {
								openErrorGritter('Error!', 'Visitor Not Found');
							}

							$('#telp').val('');
							$("#telp").focus(); 


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

		}


		function out(idtag) {
			var idt = idtag;

			var reason = $('#'+idt).val();

			var data = {					
				idtag:idtag,
				reason:reason,                  
			}
			$.post('{{ url("visitor_out") }}', data, function(result, status, xhr){
				console.log(status);
				console.log(result);
				console.log(xhr);
				if(xhr.status == 200){
					if(result.status){					
						openSuccessGritter('Success!', result.message);
						window.location.reload();
					}
					else{
						openErrorGritter('Error!', result.message);
					}
				}
				else{
					alert("Disconnected from server");
				}
			});				

		}



	</script>
	@endsection