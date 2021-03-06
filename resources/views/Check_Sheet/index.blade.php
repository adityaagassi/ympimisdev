@extends('layouts.master')
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
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		List of {{ $page }}s
		<small>it all starts here</small>
	</h1>
	<ol class="breadcrumb">

		<li>
			<a data-toggle="modal" data-target="#importModal" class="btn btn-success btn-sm" style="color:white">Import {{ $page }}s</a>
			&nbsp;
		</li>
	</ol>
</section>
@endsection

@section('content')
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
					<div class="table-responsive">
						<table id="example1" class="table table-bordered table-striped">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>Sheet No.</th>
									<th>Container No.</th>
									<th>Seal No.</th>
									<th>No. Pol</th>
									<th>Dest</th>
									<th>Invoice</th>
									<th>Stuffing Date</th>
									<th>On Or About</th>
									<th>Payment</th>
									<th>To</th>
									<th>Carrier</th>
									<th>Action</th>
									<th >View</th>
									<th>Check</th>
								</tr>
							</thead>
							<tbody>
								@foreach($time as $nomor => $time)
								<tr id="{{$time->id_checkSheet}}">
									<td style="font-size: 14">{{$time->id_checkSheet}}</td>
									<td style="font-size: 14">{{$time->countainer_number}}</td>
									<td style="font-size: 14">{{$time->seal_number}}</td>
									<td style="font-size: 14">{{$time->no_pol}}</td>
									<td style="font-size: 14">{{$time->destination}}</td>
									<td style="font-size: 14">{{$time->invoice}}</td>
									<td style="font-size: 14">{{$time->Stuffing_date}}</td>            
									<td style="font-size: 14">{{$time->etd_sub}}</td>
									<td style="font-size: 14">{{$time->payment}}</td>
									<td style="font-size: 14">{{$time->shipped_to}}</td>
									<td style="font-size: 14">
										@if(isset($time->shipmentcondition->shipment_condition_name)){{$time->shipmentcondition->shipment_condition_name}}@else Not registered @endif
									</td>
									<td>
										@if($time->status == null) 
										<a href="javascript:void(0)" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#editModal" onclick="editConfirmation('{{ url("edit/CheckSheet") }}', '{{ $time['destination'] }}', '{{ $time['id_checkSheet'] }}'); reason('{{ $time['id_checkSheet'] }}');">Edit</a>

										<a data-toggle="modal" data-target="#importModal3" class="btn btn-success btn-xs" style="color:white" onclick="getid('{{ $time['id_checkSheet'] }}');"><i class="fa fa-folder-open-o"></i> Re - Import</a>


										<a href="javascript:void(0)" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("delete/CheckSheet") }}', '{{ $time['destination'] }}', '{{ $time['id'] }}');">Delete</a>
										<p id="id_checkSheet_mastera{{$nomor + 1}}" hidden>{{$time->id_checkSheet}}</p>
										@else
										@endif
									</td>
									<td>
										<a class="btn btn-info btn-xs" href="{{url('show/CheckSheet', $time['id'])}}">View</a>
										<p id="id_checkSheet_master{{$nomor + 1}}" hidden>{{$time->id_checkSheet}}</p>
									</td>
									<td>
										@if($time->status != null)            
										<span data-toggle="tooltip" class="badge bg-green"><i class="fa fa-fw fa-check"></i></span>
										@else
										@if($time->destination != "NINGBO")
										<a class="btn btn-warning btn-xs" href="{{url('check/CheckSheet', $time['id'])}}">Check</a>
										@else
										<a class="btn btn-warning btn-xs" href="{{url('checkmarking/CheckSheet', $time['id'])}}">Check</a>
										@endif
										@endif

									</td>
								</tr>
								@endforeach
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
									<th></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>


	<div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
				</div>
				<div class="modal-body" id="modalDeleteBody">
					Are you sure delete?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
				</div>
			</div>
		</div>
	</div>



	<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<form id ="importForm" method="post" action="{{ url('import/CheckSheet') }}" enctype="multipart/form-data">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">Import Confirmation</h4>
						Format: [Destination][Invoice][GMC][Goods][Marking No][Package Qty][Package Set][Qty Qty][Qty Set]<br>
						Sample: <a href="{{ url('download/manual/import_check_sheet_detail.txt') }}">import_check_sheet_detail.txt</a> Code: #Add
					</div>

					<div class="modal-body col-xs-12">
						<div class="col-xs-12">
							<div class="col-xs-4" style="padding-left: 0px;padding-right: 20px;">
								<label>SHIPMENT PERIOD</label>
								<input type="text" name="period" class="form-control monthpicker" id="period" required>
							</div>
							<div class="col-xs-8" style="padding-left: 10px;padding-right: 0px;">
								<label>YCJ REF. NO.</label>
								<select class="form-control select2" data-placeholder="Select YCJ Ref Number" name="ycj_ref_number" id="ycj_ref_number" style="width: 100%">
									<option value=""></option>
								</select>
							</div>
							<div class="col-xs-4">
							</div>
						</div>

						<div class="col-xs-4">
							<label>CONSIGNEE & ADDRESS</label>
							<input type="text" name="destination" class="form-control" id="destination" required>
							<label>NO POL</label>
							<input type="text" name="nopol" class="form-control" id="nopol" required>
							<label>CONTAINER NO.</label>
							<input type="text" name="countainer_number" class="form-control" id="countainer_number" required>
							<label>SEAL NO</label>
							<input type="text" name="seal_number" class="form-control" id="seal_number">
							<BR>
							<center><input type="file" name="check_sheet_import" id="InputFile" accept="text/plain" required></center>
						</div>

						<div class="col-xs-4">
							<label>SHIPPED FROM</label>
							<input type="text" name="shipped_from" class="form-control" id="shipped_from" value="SURABAYA" readonly>
							<label>SHIPPED TO</label>
							<input type="text" name="shipped_to" class="form-control" id="shipped_to" required>
							<label>CARRIER</label>

							<select class="form-control select2" name="carier" id="carier"  data-placeholder="a" style="width: 100%;" >

								@foreach($carier as $nomor => $carier)
								<option value="{{ $carier->shipment_condition_code }}" > {{$carier->shipment_condition_name}}</option>
								@endforeach
							</select>
							<label>ON OR ABOUT</label>
							<input type="text" name="etd_sub" class="form-control" ID= "etd_sub" autocomplete="off" required>



						</div>

						<div class="col-xs-4">
							<label>INVOICE NO.</label>
							<input type="text" name="invoice" class="form-control" id="invoice" required>

							<label>INVOICE DATE</label>
							<input type="text" name="invoice_date" class="form-control" id="invoice_date" autocomplete="off" required>

							<label>STUFFING DATE</label>
							<input type="text" name="Stuffing_date" class="form-control" id="Stuffing_date" autocomplete="off" required>


							<label>PAYMENT</label>
							<select class="form-control select2" name="payment" id="payment"  data-placeholder="Choose a Payment ..." style="width: 100%;" >

								<option value="T/T REMITTANCE">T/T REMITTANCE</option>
								<option value="D/P AT SIGHT">D/P AT SIGHT</option>
								<option value="D/A 60 DAYS AFTER BL DATE">D/A 60 DAYS AFTER BL DATE</option>
							</select>

							<label>SHIPPER</label>
							<input type="text" name="" class="form-control" value="PT. YMPI" readonly>
						</div>

						<div class="col-xs-8">
							<label>Towards: </label>
							<select class="form-control select2" multiple="multiple" name="toward[]" id="toward"  data-placeholder="Choose a Toward ..." style="width: 100%;" >
								<option value="YAMAHA MUSIC MANUFACTURING JAPAN CORPORATION BO & GD SECTION">YMMJ</option>
								<option value="XIAOSHAN YAMAHA MUSICAL INSTRUMENT CO.,LTD">XY</option>
								<option value="YAMAHA CORPORATION">YCJ/YMJ</option>
								<option value="YAMAHA MUSIC EUROPE">YME</option>
								<option value="YAMAHA MUSIC KOREA LTD.">YMK</option>
								<option value="YAMAHA CORPORATION C/O MOL LOGISTIC S PASIR GUDANG WAREHOUSE">TASCO</option>
								<option value="YCA,BAND&ORCHESTRAL DIV.">YCA</option>
								<option value="SIAM MUSIC YAMAHA CO., LTD">SMY</option>
								<option value="PT. YAMAHA MUSIK INDONESIA DISTRIBUTOR">YMID</option>
								<option value="YAMAHA ELECTRONICS MFG INDONESIA">YEMI</option>
								<option value="YAMAHA DE MEXICO S.A. DE C.V.">YDM</option>
							</select>
						</div>
						<div class="col-xs-4">
							<label>Container Size: </label>
							<select class="form-control select2" name="ct_size" id="ct_size"  data-placeholder="Choose a Size ..." style="width: 100%;" >
								<option value=""></option>
								<option value="20FT">20FT</option>
								<option value="40FT">40FT</option>
								<option value="40FT HC">40FT HC</option>
								<option value="TRUCK">TRUCK</option>
							</select>
						</div>        
					</div>



					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						<button id="modalImportButton" type="button" class="btn btn-success" onclick="cektgl()">Import</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="modal  fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel"><p id="myModalLabelt">Edit Confirmation</p></h4>
				</div>
				<div class="modal-body" id="modalDeleteBody">
					<form id ="Editform" method="post" action="" enctype="multipart/form-data">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="modal-body col-xs-12">
							<div class="col-xs-4">
								<label>CONSIGNEE & ADDRESS</label>
								<input type="text" name="destinationE" class="form-control" id="destinationE" required>
								<label>NO POL</label>
								<input type="text" name="nopolE" class="form-control" id="nopolE" required>
								<label>CONTAINER NO.</label>
								<input type="text" name="countainer_numberE" class="form-control" id="countainer_numberE" required>
								<label>SEAL NO</label>
								<input type="text" name="seal_numberE" class="form-control" id="seal_numberE" required>

							</div>

							<div class="col-xs-4">
								<label>SHIPPED FROM</label>
								<input type="text" name="shipped_from" class="form-control" id="shipped_from" value="SURABAYA" readonly>
								<label>SHIPPED TO</label>
								<input type="text" name="shipped_toE" class="form-control" id="shipped_toE" required>
								<label>CARRIER</label>

								<select class="form-control select2" name="carierE" id="carierE"  data-placeholder="aaaaa" style="width: 100%;" >

									@foreach($carier1 as $nomor => $carier)
									<option value="{{ $carier->shipment_condition_code }}" > {{$carier->shipment_condition_name}}</option>
									@endforeach
								</select>

								<label>ON OR ABOUT</label>
								<input type="text" name="etd_subE" class="form-control" ID= "etd_subE" required>
							</div>

							<div class="col-xs-4">
								<label>INVOICE NO.</label>
								<input type="text" name="invoiceE" class="form-control" id="invoiceE" required>
								<label>INVOICE DATE</label>
								<input type="text" name="invoice_dateE" class="form-control" id="invoice_dateE" required>

								<label>STUFFING DATE</label>
								<input type="text" name="Stuffing_dateE" class="form-control" id="Stuffing_dateE" required>
								<label>PAYMENT</label>
								<select class="form-control select2" name="paymentE" id="paymentE"  data-placeholder="Choose a Payment ..." style="width: 100%;" >

									<option value="T/T REMITTANCE">T/T REMITTANCE</option>
									<option value="D/P AT SIGHT">D/P AT SIGHT</option>
									<option value="D/A 60 DAYS AFTER BL DATE">D/A 60 DAYS AFTER BL DATE</option>
								</select>
								<label>SHIPPER</label>
								<input type="text" name="" class="form-control" value="PT. YMPI" readonly>
							</div>
							<div class="col-xs-12">
								<label>REASON</label>
								<textarea name="reason" class="form-control" id="reason"></textarea>
							</div>
						</div>
						<input type="text" name="id_chek" id="id_chek" hidden>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
							<button id="modaleditButton" type="submit" class="btn btn-success">Edit</button>
						</div>
					</form>
				</div>      
			</div>
		</div>
	</div>


	<div class="modal fade" id="importModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<form id ="importForm2" method="post" action="{{ url('importDetail/CheckSheet') }}" enctype="multipart/form-data">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="modal-header">Re - Import Data</div>
					<div class="">
						<div class="modal-body">
							Are you sure to Re - Import?<br>
							All Data Will be Delete and Re - Import

							<center>
								<i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
							</center>
							<input type="text" name="idcs" id="idcs" hidden="">
							<input type="text" name="master_id" value="{{$time->id_checkSheet}}" hidden>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
							<button id="modalImportButton" type="button" class="btn btn-success" onclick="deleteReimport()">Re - Import</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="modal fade" id="importModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<form id ="importForm2" method="post" action="{{ url('importDetail/CheckSheet') }}" enctype="multipart/form-data">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="modal-header">
						<h4 class="modal-title" id="myModalLabel">Import Confirmation</h4>
						Format: [Destination][Invoice][GMC][Goods][Marking No][Package Qty][Package Set][Qty Qty][Qty Set]<br>
						Sample: <a href="{{ url('download/manual/import_check_sheet_detail.txt') }}">import_check_sheet_detail.txt</a> Code: #Truncate
					</div>
					<div class="">
						<div class="modal-body">
							<center><input type="file" name="check_sheet_import2" id="InputFile" accept="text/plain" required=""></center>
							<input type="text" name="idcs2" id="idcs2" hidden="">
							<input type="text" name="master_id" value="{{$time->id_checkSheet}}" hidden>
						</div>
						<div class="modal-footer">
							<button id="modalImportButton" type="submit" class="btn btn-success" >Import</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>
@stop

@section('scripts')
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	$('#etd_sub').datepicker({
		autoclose: true,
		format: 'yyyy-mm-dd',
		todayHighlight: true
	})
	$('#Stuffing_date').datepicker({
		autoclose: true,
		format: 'yyyy-mm-dd',
		todayHighlight: true
	})

	$('#invoice_date').datepicker({
		autoclose: true,
		format: 'yyyy-mm-dd',
		todayHighlight: true
	})

	$('#invoice_dateE').datepicker({
		autoclose: true,
		format: 'yyyy-mm-dd',
		todayHighlight: true
	})

	$('#etd_subE').datepicker({
		autoclose: true,
		format: 'yyyy-mm-dd',
		todayHighlight: true
	})
	$('#Stuffing_dateE').datepicker({
		autoclose: true,
		format: 'yyyy-mm-dd',
		todayHighlight: true
	})
	jQuery(document).ready(function() {
		$('.monthpicker').datepicker({
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
			todayHighlight: true
		});

		$(function () {
			$('.select2').select2({
				dropdownParent: $('#importModal'),
				allowClear: true
			});
		})

		$(document).ready(function () {
			$('body').toggleClass("sidebar-collapse");
		})
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$('#example1 tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="3"/>' );
		} );
		var table = $('#example1').DataTable({
			"order": [],
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
			}
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

		$('#example1 tfoot tr').appendTo('#example1 thead');

	});
	$(function () {

		$('#example2').DataTable({
			'paging'      : true,
			'lengthChange': false,
			'searching'   : false,
			'ordering'    : true,
			'info'        : true,
			'autoWidth'   : false
		})
	})

	function deleteConfirmation(url, name, id) {
		jQuery('#modalDeleteBody').text("Are you sure want to delete '" + name + "'");
		jQuery('#modalDeleteButton').attr("href", url+'/'+id);
	}

	function editConfirmation(url, name, id) {


		jQuery('#modaleditButton').attr("href", url+'/'+id);

		var cont;
		var seal;
		var pol;
		var dest;
		var inv;
		var invd;
		var date;
		var pay;
		var to;
		var carier;
		var id_chek;
		id_chek = $("#"+id+" td:nth-child(1)").text();
		cont = $("#"+id+" td:nth-child(2)").text();
		seal = $("#"+id+" td:nth-child(3)").text();
		pol = $("#"+id+" td:nth-child(4)").text();
		dest = $("#"+id+" td:nth-child(5)").text();
		inv = $("#"+id+" td:nth-child(6)").text();
		invd = $("#"+id+" td:nth-child(7)").text();
		date = $("#"+id+" td:nth-child(8)").text();
		pay = $("#"+id+" td:nth-child(9)").text();
		to = $("#"+id+" td:nth-child(10)").text();
		carier = $("#"+id+" td:nth-child(11)").text();
		if (carier =="SEA"){
			carier = "C1";
		}else if(carier =="AIR"){
			carier = "C2";
		}else{
			carier = "TR";
		}

		document.getElementById("countainer_numberE").value = cont; 
		document.getElementById("seal_numberE").value = seal;
		document.getElementById("nopolE").value = pol;
		document.getElementById("invoiceE").value = inv;
		document.getElementById("destinationE").value = dest;
		document.getElementById("shipped_toE").value = to;
		document.getElementById("Stuffing_dateE").value = invd;
		document.getElementById("etd_subE").value = date;
		document.getElementById("id_chek").value = id_chek;
		document.getElementById("myModalLabelt").innerHTML = "Edit Confirmation "+id_chek;


		$("#carierE option[value='"+carier+"']").prop('selected', true);
		$("#paymentE option[value='"+pay+"']").prop('selected', true);
		$('#Editform').attr('action', url+'/'+id);



	}

	function reason(id) {  
		var data = {
			id:id
		}
		$.get('{{ url("fill/reason") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#reason').val(result.reason.reason);
					$('#invoice_dateE').val(result.reason.invoice_date);
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


	function addInspection(id){
		var a = id;
		var id =document.getElementById("id_checkSheet_master"+a).innerHTML;

		var data = {
			id:id,
		}

		$.post('{{ url("add/CheckSheet") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
		});
	}

	function cektgl() {
		var date =  $('#Stuffing_date').val();
		var on_or = $('#etd_sub').val();

		var start = new Date(date),
		end   = new Date(on_or),
		diff  = new Date(end - start),
		days  = diff/1000/60/60/24;

		var carier =  $('#carier').val();
		var ycj_ref_number =  $('#ycj_ref_number').val();
		var period =  $('#period').val();

		if (days >= 0) {
			document.getElementById("importForm").submit();
		}else{
			alert('Please Check Stuffing Date And Date ON OR ABOUT');

		}
	}

	function getid(id) {
		var id_chek;
		id_chek = $("#"+id+" td:nth-child(1)").text();
		$('#idcs').val(id_chek);
		$('#idcs2').val(id_chek);
		$('#loading').hide();
	}

	function deleteReimport() {  
		var id = $('#idcs').val();

		var data = {
			id:id
		}

		$.get('{{ url("delete/deleteReimport") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#loading').show();
					setTimeout(function() {
						$('#importModal2').modal({backdrop: 'static', keyboard: false});
						$('#importModal2').modal('show');
					}, 2000);
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

	$("#period").change(function(){
		$("#loading").show();

		var period = $(this).val(); 
		var data = {
			period : period
		}
		$.ajax({
			type: "GET",
			dataType: "html",
			url: "{{ url("fetch/get_ref_number") }}",
			data: data,
			success: function(message){
				$("#ycj_ref_number").html(message);                                                   
				$("#loading").hide();                                                        
			}
		});                    
	});
</script>

@stop