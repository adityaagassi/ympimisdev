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


@section('header')
<section class="content-header">
	<h1>
		<center>	<span style="color: white; font-weight: bold; font-size: 28px; text-align: center;">YMPI Visitor Confirmation</span></center>
		
	</h1><br>
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
			<div class="col-xs-12">
				<div class="row">
					<div class="col-xs-12">
						<div class="box">
							<div class="box-body">
								<div class="form-group">					
									<div  class="col-xs-12">
										<div class="input-group ">
											<div class="input-group-btn">
												<button type="button" class="btn btn-warning"><i class="fa fa-search"></i>&nbsp;Search</button>
											</div>
											<input type="text" id="telp" class="form-control" placeholder="Search Telephone" onclick="emptytlp()">
										</div>
									</div>

								</div>
							</div>
						</div>
					</div>
				</div>
				<input type="text" id="tag_visitor" class="form-control" style="background-color: #3c3c3c;border: none">
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
									</table>									
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
			$('#tag_visitor').focus();
			// $('#nikkaryawan').val('asd');
			filltelpon();
			setTimeout(function(){
			      location = ''
			    },60000);
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

		$('#tag_visitor').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#tag_visitor").val().length >= 8){
					var data = {
						tag_visitor : $("#tag_visitor").val()
					}
					// alert($("#tag_visitor").val());

					$.get('{{ url("scan/visitor/lobby") }}', data, function(result, status, xhr){
						if(result.status){
							$('#tag_visitor').val('');
							openSuccessGritter('Success!', result.message);
						}
						else{
							// audio_error.play();
							openErrorGritter('Error', result.message);
							$('#tag_visitor').val('');
							$('#tag_visitor').focus();
						}
					});
				}
				else{
					openErrorGritter('Error!', 'Tag Invalid.');
					// audio_error.play();
					$("#tag_visitor").val("");
					$('#tag_visitor').focus();
				}
			}
		});

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

		// update all remark

		function inputag2(id) {
			
				var id = $('#idhead').text();
				var data = {
					id:id,            
				}
				$.post('{{ url("visitor_updateremarkall") }}', data, function(result, status, xhr){
					console.log(status);
					console.log(result);
					console.log(xhr);
					if(xhr.status == 200){
						if(result.status){							
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

		function cari(a){
			if (a=="telp"){
			 var table = $('#telponlist').DataTable();
			 var telp = $('#telp').val();
			 table.search( telp ).draw();
			}
		}

		function emptytlp() {
			$("#telp").val('');
		}

		

	</script>
	@endsection