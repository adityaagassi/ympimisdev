@extends('layouts.master')
@section('stylesheets')
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
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
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
		padding-top: 0;
		padding-bottom: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
	.disabledTab{
		pointer-events: none;
	}
</style>
@stop
@section('header')
<section class="content-header">
	{{-- 	 @if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('status') }}
	</div>   
	@endif --}}
	<h1>
		Daily Reports<span class="text-purple"> </span>
		{{-- <small>WIP Control <span class="text-purple"> 仕掛品管理</span></small> --}}
	</h1>
	<ol class="breadcrumb">
		<li>
			<a href="javascript:void(0)" onclick="openModalCreate()" class="btn btn-sm bg-purple" style="color:white">Create {{ $page }}</a>
		</li>
	</ol>
</section>
@stop
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
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
			<div class="box">
				<div class="box-body">
					<table id="dailyReportTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%;">Dept</th>
								<th style="width: 3%;">PIC</th>
								<th style="width: 4%;">Category</th>
								<th style="width: 12%;">Description</th>
								<th style="width: 1%;">Duration</th>
								<th style="width: 7%;">Location</th>
								<th style="width: 1%;">Begin Date</th>
								<th style="width: 1%;">Target Date</th>
								<th style="width: 1%;">Finished Date</th>
								<th style="width: 1%;">Att</th>
								<th style="width: 1%;">Edit</th>
								{{-- <th style="width: 10%;">Act</th> --}}
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
								{{-- <th></th> --}}
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>
<form id ="importForm" name="importForm" method="post" action="{{ url('create/daily_report') }}" enctype="multipart/form-data">
	<input type="hidden" value="{{csrf_token()}}" name="_token" />
	<div class="modal fade" id="modalCreate">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Create Daily Report</h4>
					<br>
					<div class="nav-tabs-custom tab-danger">
						<ul class="nav nav-tabs">
							<li class="vendor-tab active disabledTab"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Report Informations</a></li>
							<li class="vendor-tab disabledTab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">Report Details</a></li>
						</ul>
					</div>
					<div class="tab-content">
						<div class="tab-pane active" id="tab_1">
							<div class="row">
								<div class="col-md-12">
									<div class="col-md-6">
										<div class="form-group">
											<label>Category<span class="text-red">*</span></label>
											<select class="form-control select2" style="width: 100%;" id="category" name="category" data-placeholder="Choose a Category..." required>
												<option></option>
												<option value="Network Maintenance">Network Maintenance</option>
												<option value="Hardware Maintenance">Hardware Maintenance</option>
												<option value="Hardware Installation">Hardware Installation</option>
												<option value="Hardware Installation">Software Installation</option>
												<option value="Design">Design</option>
												<option value="System Analysis">System Analysis</option>
												<option value="System Programming">System Programming</option>
												<option value="Bug & Error">Bug & Error</option>
												<option value="Trial & Training">Trial & Training</option>
											</select>
										</div>
										<div class="form-group">
											<label>Related Department<span class="text-red">*</span></label>
											<select class="form-control select2" style="width: 100%;" id="location" name="location" data-placeholder="Choose a Location..." required>
												<option></option>
												<option value='Management Information System'>Management Information System</option>
												<option value='Accounting'>Accounting</option>
												<option value='Assembly (WI-A)'>Assembly (WI-A)</option>
												<option value='Educational Instrument (EI)'>Educational Instrument (EI)</option>
												<option value='General Affairs'>General Affairs</option>
												<option value='Human Resources'>Human Resources</option>
												<option value='Logistic'>Logistic</option>
												<option value='Maintenance'>Maintenance</option>
												<option value='Parts Process (WI-PP)'>Parts Process (WI-PP)</option>
												<option value='Procurement'>Procurement</option>
												<option value='Production Control'>Production Control</option>
												<option value='Production Engineering'>Production Engineering</option>
												<option value='Purchasing'>Purchasing</option>
												<option value='Quality Assurance'>Quality Assurance</option>
												<option value='Welding-Surface Treatment (WI-WST)'>Welding-Surface Treatment (WI-WST)</option>
											</select>
										</div>
										<div class="form-group">
											<label>Attachment</label>
											<input type="file" id="reportAttachment" name="reportAttachment[]" multiple="">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Date Begin<span class="text-red">*</span></label>
											<div class="input-group date">
												<div class="input-group-addon">
													<i class="fa fa-calendar"></i>
												</div>
												<input type="text" class="form-control pull-right" id="datebegin" name="datebegin">
											</div>
										</div>
										<div class="form-group">
											<label>Date Target<span class="text-red">*</span></label>
											<div class="input-group date">
												<div class="input-group-addon">
													<i class="fa fa-calendar"></i>
												</div>
												<input type="text" class="form-control pull-right" id="datetarget" name="datetarget">
											</div>
										</div>
										<div class="form-group">
											<label>Date Finished</label>
											<div class="input-group date">
												<div class="input-group-addon">
													<i class="fa fa-calendar"></i>
												</div>
												<input type="text" class="form-control pull-right" id="datefinished" name="datefinished">
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-12">
									<a class="btn btn-primary btnNext pull-right">Next</a>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="tab_2">
							<div class="row">
								<div class="col-md-12" style="margin-bottom : 5px">
									<input type="text" name="lop" id="lop" value="1" hidden>
									<div class="col-xs-8" style="padding:0;">
										<input type="text" class="form-control" id="description1" name="description1" placeholder="Enter Description" required>
									</div>
									<div class="col-xs-2" style="padding:0;">
										<input type="text" id="duration" name="duration1" class="form-control timepicker" value="01:00">
									</div>
									<div class="col-xs-2" style="padding:0;">
										<button class="btn btn-success" onclick='tambah("tambah","lop");'><i class='fa fa-plus' ></i></button>
									</div>	
								</div>
								<div id="tambah"></div>
								<div class="col-md-12">
									<br>
									<button class="btn btn-success pull-right" onclick="$('[name=importForm]').submit();">Confirm</button>
									<span class="pull-right">&nbsp;</span>
									<a class="btn btn-primary btnPrevious pull-right">Previous</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<div class="modal fade in" id="modalEdit">
	<form id ="importForm" name="importForm" method="post" action="{{ url('update/daily_report') }}">
		<input type="hidden" value="{{csrf_token()}}" name="_token" />
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Edit Daily Report</h4>
					<br>
					<h4 class="modal-title" id="modalDetailTitle"></h4>

					<div class="row">
						<div class="col-md-12">
							<div class="col-md-6">
								<div class="form-group" id="modalDetailBodyEditHeader">
									

								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group" id="modalDetailBodyEditHeader2">

								</div>
							</div>
						</div>
					</div>

					<div  id="modalDetailBodyEditHeader"></div>

					<div  id="modalDetailBodyEdit"></div><br>
					{{-- <center><button type="button" class="btn btn-success" onclick='tambah("tambah2","lop2");'><i class='fa fa-plus' ></i></button></center><br> --}}
					<div id="tambah2">
						<input type="text" name="lop2" id="lop2" value="1" hidden="">
					</div>
					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-warning">Update</button>
				</div>
			</div>
		</div>
	</form>
</div>
<div class="modal modal-danger fade in" id="modaledit" >
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span></button>
					<h4 class="modal-title">Danger Modal</h4>
				</div>
				<div class="modal-body" id="modalDeleteBody">
					<p>One fine body…</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
					<a id="a" name="modalDeleteButton" href="#" type="button" onclick="hapus(this.id)" class="btn btn-danger">Delete</a>
				</div>
			</div>
			
		</div>
		
	</div>

	@endsection
	@section('scripts')
	<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
	<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
	<script src="{{ url("js/buttons.flash.min.js")}}"></script>
	<script src="{{ url("js/jszip.min.js")}}"></script>
	<script src="{{ url("js/vfs_fonts.js")}}"></script>
	<script src="{{ url("js/buttons.html5.min.js")}}"></script>
	<script src="{{ url("js/buttons.print.min.js")}}"></script>
	<script>
		var no = 2;
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		jQuery(document).ready(function() {
		// $('body').toggleClass("sidebar-collapse");
		$('#datebegin').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			todayHighlight: true
		});
		
		$('#datetarget').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			todayHighlight: true
		});
		$('#datefinished').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			todayHighlight: true
		});
		$('.timepicker').timepicker({
			showInputs: false,
			showMeridian: false,
			defaultTime: '0:00',
		});
		$('.select2').select2({
			dropdownAutoWidth : true,
			width: '100%',
		});
		$('.btnNext').click(function(){
			var category = $('#category').val();
			var location = $('#location').val();
			var datebegin = $('#datebegin').val();
			var datetarget = $('#datetarget').val();
			var datefinished = $('#datefinished').val();
			if(category == '' || location == '' || datebegin == '' || datetarget == ''){
				alert('All field must be filled');	
			}
			else{
				$('.nav-tabs > .active').next('li').find('a').trigger('click');
			}
		});
		$('.btnPrevious').click(function(){
			$('.nav-tabs > .active').prev('li').find('a').trigger('click');
		});
		fillDailyReportTable();
	});

		function editReport(id){
			var data = {
				report_code : id
			}
			$.get('{{ url("edit/daily_report") }}', data, function(result, status, xhr){
				console.log(status);
				console.log(result);
				console.log(xhr);
				if(xhr.status == 200){
					if(result.status){
						$('#modalDetailBodyEdit').html('');
						$('#modalDetailBodyEditHeader').html('');
						$('#modalDetailBodyEditHeader2').html('');
						
						$.each(result.daily_reportsHead, function(key, value) {
							
							$('#modalDetailBodyEditHeader').append('<input type="text" name="cat" id="cat" value="'+ value.category +'" hidden><input type="text" name="loc" id="loc" value="'+ value.location +'" hidden><input type="text" name="report_code" value="'+ value.report_code +'" hidden><label>Category<span class="text-red">*</span></label><select class="form-control select2" style="width: 100%;" id="category123" name="category" data-placeholder="Choose a Category..." required>@foreach($cat as $cat)<option value="{{ $cat }}">{{ $cat }}</option> @endforeach</select></div><div class="form-group"><label>Location<span class="text-red">*</span></label><select class="form-control select2" style="width: 100%;" id="location123" name="location" data-placeholder="Choose a Location..." required>>@foreach($loc as $loc)<option value="{{ $loc }}">{{ $loc }}</option>@endforeach</select></div><div class="form-group">').find('.select2').select2();

							$('#modalDetailBodyEditHeader2').append('<label>Date Begin<span class="text-red">*</span></label><div class="input-group date"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" class="datepicker form-control pull-right" id="datebegin" name="begindate" value="'+ value.begin_date +'"></div></div><div class="form-group"><label>Date Target<span class="text-red">*</span></label><div class="input-group date"><div class="input-group-addon" ><i class="fa fa-calendar"></i></div><input type="text" class="form-control pull-right datepicker" id="datetarget" name="targetdate" value="'+ value.target_date +'"></div></div><div class="form-group"><label>Date Finished</label><div class="input-group date"><div class="input-group-addon" ><i class="fa fa-calendar"></i></div><input type="text" class="datepicker form-control pull-right" id="datefinished" name="finisheddate" value="'+ value.finished_date +'">').find('.datepicker').datepicker({
								autoclose: true,
								format: 'yyyy-mm-dd',
							});

						});
						$.each(result.daily_reports, function(key, value) {
							var tambah2 = "tambah2";
							var	lop2 = "lop2";
							$('#modalDetailBodyEdit').append('<div class="col-md-12" style="margin-bottom : 5px" id="ibu'+ value.id +'"><div class="col-xs-8" style="padding:0;"><input type="text" name="report_id[]" value="'+ value.id +'" hidden><input type="text" class="form-control" id="description'+ value.id +'" name="description'+ value.id +'"  value="'+ value.description +'" required></div><div class="col-xs-2" style="padding:0;"><input type="text" name="duration'+ value.id +'" class="form-control timepicker" value="'+ value.duration +'" id="duration'+ value.id +'"></div><div class="col-xs-2" style="padding:0;"">&nbsp;<a href="javascript:void(0);" id="b'+ value.id +'" onclick="deleteConfirmation(\''+ value.description +'\','+value.id +');" class="btn btn-danger" data-toggle="modal" data-target="#modaledit"><i class="fa fa-close"></i> </a> <button type="button" class="btn btn-success" onclick="tambah(\''+ tambah2 +'\',\''+ lop2 +'\');"><i class="fa fa-plus" ></i></button></div></div>').find('.timepicker').timepicker({
								showInputs: false,
								showMeridian: false,

							});
						});

						var cat = $('#cat').val();;
						var loc = $('#loc').val();;
						$("#category123").val(cat).trigger("change");
						$("#location123").val(loc).trigger("change");
						$('#modalEdit').modal('show');
						
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

		function openModalCreate(){
			$('#modalCreate').modal('show');
		}

		function deleteConfirmation(name, id) {
			jQuery('#modalDeleteBody').text("Are you sure want to delete ' " + name + " '");
			jQuery('[name=modalDeleteButton]').attr("id",id);
		}


		function downloadAtt(id){
			var data = {
				report_code:id
			}
			$.get('{{ url("download/daily_report") }}', data, function(result, status, xhr){
				console.log(status);
				console.log(result);
				console.log(xhr);
				if(xhr.status == 200){
					if(result.status){
						document.location.href = (result.file_path);
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

		function fillDailyReportTable(){
			$('#dailyReportTable tfoot th').each( function () {
				var title = $(this).text();
				$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
			});
			var table = $('#dailyReportTable').DataTable({
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
					"url" : "{{ url("fetch/daily_report") }}",
				},
				"columns": [
				{ "data": "role_code"},
				{ "data": "name"},
				{ "data": "category"},
				{ "data": "description"},
				{ "data": "duration"},
				{ "data": "location"},
				{ "data": "begin_date"},
				{ "data": "target_date"},
				{ "data": "finished_date"},
				{ "data": "attach"},
				{ "data": "action"}
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

			$('#dailyReportTable tfoot tr').appendTo('#dailyReportTable thead');
		}

		function detailReport(id){
			var data = {
				report_code:id
			}
			$.get('{{ url("fetch/daily_report_detail") }}', data, function(result, status, xhr){
				console.log(status);
				console.log(result);
				console.log(xhr);
				if(xhr.status == 200){
					if(result.status){
						$('#modalDetailBody').html('');
						var detailData = '';
						$.each(result.daily_reports, function(key, value) {
							detailData += '<tr>';
							detailData += '<td>'+ value.description +'</td>';
							detailData += '<td>'+ value.duration +'</td>';
							detailData += '</tr>';
						});
						$('#modalDetailBody').append(detailData);
						$('#modalDetail').modal('show');
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

		function tambah(id,lop) {
			var id = id;

			var lop = "";

			if (id == "tambah"){
				lop = "lop";
			}else{
				lop = "lop2";
			}

			var divdata = $("<div id='"+no+"' class='col-md-12' style='margin-bottom : 5px'><div class='col-xs-8' style='padding:0;''><input type='text' class='form-control' id='description"+no+"' name='description"+no+"' placeholder='Enter Description' required></div><div class='col-xs-2' style='padding:0;''><input type='text' id='duration"+no+"' name='duration"+no+"' class='form-control timepicker'></div><div class='col-xs-2' style='padding:0;'>&nbsp;<button onclick='kurang(this,\""+lop+"\");' class='btn btn-danger'><i class='fa fa-close'></i> </button> <button type='button' onclick='tambah(\""+id+"\",\""+lop+"\"); ' class='btn btn-success'><i class='fa fa-plus' ></i></button></div></div>");

			$("#"+id).append(divdata).find('.timepicker').timepicker({
				showInputs: false,
				showMeridian: false,
				defaultTime: '0:00',
			});;
			document.getElementById(lop).value = no;
			no+=1;

		}

		function kurang(elem,lop) {
			var lop = lop;
			var ids = $(elem).parent('div').parent('div').attr('id');
			var oldid = ids;
			$(elem).parent('div').parent('div').remove();
			var newid = parseInt(ids) + 1;
			jQuery("#"+newid).attr("id",oldid);
			jQuery("#description"+newid).attr("name","description"+oldid);
			jQuery("#duration"+newid).attr("name","duration"+oldid);

			jQuery("#description"+newid).attr("id","description"+oldid);
			jQuery("#duration"+newid).attr("id","duration"+oldid);

			no-=1;
			var a = no -1;

			for (var i =  ids; i <= a; i++) {	
				var newid = parseInt(i) + 1;
				var oldid = newid - 1;
				jQuery("#"+newid).attr("id",oldid);
				jQuery("#description"+newid).attr("name","description"+oldid);
				jQuery("#duration"+newid).attr("name","duration"+oldid);

				jQuery("#description"+newid).attr("id","description"+oldid);
				jQuery("#duration"+newid).attr("id","duration"+oldid);

			// alert(i)
		}

		document.getElementById(lop).value = a;
	}

	function hapus(id) {
		var data = {
			id:id,
		}
		
		$.post('{{ url("delete/daily_report") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
		});
		$('#modaledit').modal('hide');
		
		$('#ibu'+id).css("display","none");
      // $('#description'+id).attr("name","description");
  }
  


</script>
@endsection