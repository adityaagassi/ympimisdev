@extends('layouts.visitor')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("css/jqbtk.css")}}">
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
		<h1>
		<center><span style="color: white; font-weight: bold; font-size: 28px; text-align: center;" id="judul">YMPI Visitor Confirmation By Department</span></center>
		</h1>

		<div class="row" style="display: none;padding-left: 20px;padding-right: 20px" id="tabelvisitor">
			<div class="col-xs-12">
				<div class="box">
					<div class="box-body">
						<div class="table-responsive">
							<table id="visitorList" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>Incoming Date</th>
										<th>Employee</th>
										<th>Department</th>
										<th>Company</th>
										<th>Visitor Name</th>
										<th>Total</th>
										<th>Purpose</th>
										<th>Status</th>								
										<th>Remark</th>
										<th>Confirmation</th>
									</tr>
								</thead>
								<tbody id="visitorListBody">
								</tbody>
								<tfoot>
									<tr style="color: black">
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
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

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
				<div  class="form-group">
					<input type="text" name="lop" id="lop" value="1" hidden>

					<div class="col-sm-2" style="padding-right: 0;">
						No. KTP/SIM												
					</div>
					<div class="col-sm-2" style="padding-left: 1; padding-right: 0;">
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
					</div>
					<div class="col-sm-2">
						Remark										
					</div><br>
					<div id="apenlist"><br>		
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-success" onclick="inputag2()">Confirm</button>
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
	<script src="{{ url("js/jqbtk.js")}}"></script>
	<script >
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});




		jQuery(document).ready(function() { 
			$('#nikkaryawan').keyboard();
			$('#telp').keyboard();
			// $('#nikkaryawan').val('asd');
			// filltelpon();
			fillList();
			$('#tabelvisitor').css({'display':'block'})
			// $('.select2').select2();
			$('.select3').select2({
				dropdownParent: $('#modal-default')
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

		function fillList(){
			// var data = {
			// 	process : 'Forging'
			// }
			var department;
			$.get('{{ url("fetch/visitor/fetchVisitorByManager") }}', function(result, status, xhr){
				if(result.status){
					department = result.name;
					$('#visitorList').DataTable().clear();
					$('#visitorList').DataTable().destroy();
					$('#visitorListBody').html("");
					var tableData = "";
					$.each(result.lists, function(key, value) {
						if (value.remark == "") {
							var bg = "background-color: rgb(204, 255, 255);";
						}else{
							var bg = "background-color: rgb(255, 204, 255);";
						}
						tableData += '<tr>';
						tableData += '<td>'+ value.created_at +'</td>';
						tableData += '<td>'+ value.name +'</td>';
						tableData += '<td>'+ value.department +'</td>';
						tableData += '<td>'+ value.company +'</td>';
						tableData += '<td>'+ value.full_name +'</td>';
						if (value.total1 == null) {
							tableData += '<td>1 Orang</td>';
						}else{
							tableData += '<td>'+ value.total1 +' Orang</td>';
						}
						tableData += '<td>'+ value.purpose +'</td>';
						tableData += '<td>'+ value.status +'</td>';
						if (value.remark == null) {
							tableData += '<td style="'+bg+'"></td>';
							tableData += '<td><a href="javascript:void(0)" data-toggle="modal-default" class="btn btn-sm btn-success" onClick="inputag2(id)" id="'+ value.id + '">Sudah Ditemui</a><a href="javascript:void(0)" data-toggle="modal-default" class="btn btn-sm btn-danger" onClick="inputag3(id)" id="'+ value.id + '">Belum Ditemui</a></td>';
						}
						else{
							tableData += '<td style="'+bg+'">'+ value.remark +'</td>';
						}
						tableData += '</tr>';
					});
					$('#judul').html('YMPI Visitor Confirmation of '+department);
					$('#visitorListBody').append(tableData);

					$('#visitorList tfoot th').each(function(){
						var title = $(this).text();
						$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
					});
						
					var table = $('#visitorList').DataTable({
						'dom': 'Bfrtip',
						'responsive':true,
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
							}
							]
						},
						'paging': true,
						'lengthChange': true,
						'pageLength': 10,
						'searching': true	,
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

					$('#visitorList tfoot tr').appendTo('#visitorList thead');
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			});
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
							$('#apenlist').append('<div id="'+ value.tag +'" style="'+$bg+'height:20px"><div class="col-sm-2" style="padding-right: 0;"><input readonly type="text" class="form-control" id="visitor_id0" name="visitor_id0" placeholder="No. KTP/SIM" required value="'+ value.id_number +'"></div><div class="col-sm-2" style="padding-left: 1; padding-right: 0;"><input readonly type="text" class="form-control" id="visitor_name0" name="visitor_name0" placeholder="Full Name" required value="'+ value.full_name +'"></div><div class="col-sm-2" style="padding-left: 1; padding-right: 0;"><input readonly type="text" class="form-control" id="status0" name="status0" placeholder="No Hp" value="'+ value.status +'" ></div><div class="col-sm-2" style="padding-left: 1; padding-right: 0;"><input readonly type="text" class="form-control" id="telp0" name="telp0" placeholder="No Hp" value="'+ value.telp +'" ></div><div class="col-sm-2"><input readonly type="text" class="form-control" id="'+ value.id +'" placeholder="Tag Number" name="'+no+'" value="'+ value.tag +'"  autofocus " "></div><div class="col-sm-2" style="padding-left:0px"><select class="form-control select3" data-placeholder="Select Remark" name="remark0" id="remark0" style="width: 100%"><option value="Sudah Ditemui">Sudah Ditemui</option><option value="Belum Ditemui">Belum Ditemui</option></select></div></div>	<br><br>');
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

		// update all remark

		function inputag2(id) {
			
				var id = id;
				var remark = 'Sudah Ditemui';
				var data = {
					id:id,
					remark:remark
				}
				$.post('{{ url("visitor_updateremarkall") }}', data, function(result, status, xhr){
					if(xhr.status == 200){
						if(result.status){
							reloadtable();						
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

		function inputag3(id) {
			
				var id = id;
				var remark = 'Belum Ditemui';
				var data = {
					id:id,
					remark:remark
				}
				$.post('{{ url("visitor_updateremarkall") }}', data, function(result, status, xhr){
					if(xhr.status == 200){
						if(result.status){
							reloadtable();						
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

		function reloadtable() {
			// $('#visitorlist').DataTable().ajax.reload();
			$('#modal-default').modal('hide');
			fillList();
		}

		// $(function () {

	 //    })
	</script>
	@endsection