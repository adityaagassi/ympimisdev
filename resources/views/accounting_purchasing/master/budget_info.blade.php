@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
	  width: 100%;
	  padding: 3px;
	  box-sizing: border-box;
	}
	thead>tr>th{
	  text-align:center;
	  overflow:hidden;
	  padding: 3px;
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
	td{
	    overflow:hidden;
	    text-overflow: ellipsis;
	  }
	#loading { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		Budget Info <span class="text-purple">{{ $title_jp }}</span>
	</h1>
	<ol class="breadcrumb">
		<li>
			<!-- <a href="{{ url("index/budget/create")}}" class="btn btn-md bg-purple" style="color:white"><i class="fa fa-plus"></i> Create New budget</a> -->
		</li>
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if (session('success'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('success') }}
	</div>   
	@endif
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row" style="margin-top: 5px">
		<div class="col-xs-12">
			<div class="box no-border" style="margin-bottom: 5px;">
				<div class="box-header">
					<h3 class="box-title">Detail Filters<span class="text-purple"> フィルター詳細</span></span></h3>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<div class="col-md-2">
							<div class="form-group">
								<label>Periode</label>
								<select class="form-control select2" multiple="multiple" id='periode' data-placeholder="Select Periode" style="width: 100%;">
									<option>FY196</option>
									<option>FY197</option>
								</select>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label>Category</label>
								<select class="form-control select2" multiple="multiple" id='category' data-placeholder="Select Category" style="width: 100%;">
									<option value="Expenses">Expenses</option>
									<option value="Fixed Asset">Fixed Asset</option>
								</select>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<div class="col-md-6" style="padding-right: 0;">
									<label style="color: white;"> x</label>
									<button class="btn btn-primary form-control" onclick="fetchTable()">Search</button>
								</div>
								<div class="col-md-6" style="padding-right: 0;">
									<label style="color: white;"> x</label>
									<button class="btn btn-danger form-control" onclick="clearSearch()">Clear</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="box no-border">
						<!-- <div class="box-header">
							<button class="btn btn-success" data-toggle="modal" data-target="#importModal" style="width: 
							16%">Import</button>
						</div> -->
						<div class="box-body" style="padding-top: 0;">
							<table id="budgetTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width:5%;">Periode</th>
										<th style="width:6%;">Budget No</th>
										<th style="width:5%;">Description</th>
										<th style="width:5%;">Amount</th>
										<th style="width:5%;">Env</th>
										<th style="width:5%;">Purpose</th>
										<th style="width:7%;">PIC</th>
										<th style="width:6%;">Account Name</th>
										<th style="width:6%;">Cateogry</th>
										<th style="width:6%;">Action</th>
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
					              </tr>
					            </tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>


<div class="modal fade" id="ViewModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Detail Budget <span id="budget_no"></span></h4>
      </div>
      <div class="modal-body">
        <div class="box-body">
        	<input type="hidden" value="{{csrf_token()}}" name="_token" />
        	<div class="col-md-12">
	          	<table class="table table-striped text-center">
	          		<tr>
	          			<th>Keterangan</th>
	          			<th>April</th>
	          			<th>Mei</th>
	          			<th>Juni</th>
	          			<th>Juli</th>
	          			<th>Agustus</th>
	          			<th>September</th>
	          			<th>Oktober</th>
	          			<th>November</th>
	          			<th>Desember</th>
	          			<th>Januari</th>
	          			<th>Februari</th>
	          			<th>Maret</th>
	          		</tr>
	          		<tr>
	          			<th>
	          				Budget Awal
	          			</th>
	          			<td>
	          				<label id="budget_awal4" name="budget_awal4"></label>
	          			</td>
	          			<td>
	          				<label id="budget_awal5" name="budget_awal5"></label>
	          			</td>
	          			<td>
	          				<label id="budget_awal6" name="budget_awal6"></label>
	          			</td>
	          			<td>
	          				<label id="budget_awal7" name="budget_awal7"></label>
	          			</td>
	          			<td>
	          				<label id="budget_awal8" name="budget_awal8"></label>
	          			</td>
	          			<td>
	          				<label id="budget_awal9" name="budget_awal9"></label>
	          			</td>
	          			<td>
	          				<label id="budget_awal10" name="budget_awal10"></label>
	          			</td>
	          			<td>
	          				<label id="budget_awal11" name="budget_awal11"></label>
	          			</td>
	          			<td>
	          				<label id="budget_awal12" name="budget_awal12"></label>
	          			</td>
	          			<td>
	          				<label id="budget_awal1" name="budget_awal1"></label>
	          			</td>
	          			<td>
	          				<label id="budget_awal2" name="budget_awal2"></label>
	          			</td>
	          			<td>
	          				<label id="budget_awal3" name="budget_awal3"></label>
	          			</td>
	          		</tr>
	          		<tr>
	          			<th>
	          				Budget After Adjustment
	          			</th>
	          			<td>
	          				<label id="budget_adj4" name="budget_adj4"></label>
	          			</td>
	          			<td>
	          				<label id="budget_adj5" name="budget_adj5"></label>
	          			</td>
	          			<td>
	          				<label id="budget_adj6" name="budget_adj6"></label>
	          			</td>
	          			<td>
	          				<label id="budget_adj7" name="budget_adj7"></label>
	          			</td>
	          			<td>
	          				<label id="budget_adj8" name="budget_adj8"></label>
	          			</td>
	          			<td>
	          				<label id="budget_adj9" name="budget_adj9"></label>
	          			</td>
	          			<td>
	          				<label id="budget_adj10" name="budget_adj10"></label>
	          			</td>
	          			<td>
	          				<label id="budget_adj11" name="budget_adj11"></label>
	          			</td>
	          			<td>
	          				<label id="budget_adj12" name="budget_adj12"></label>
	          			</td>
	          			<td>
	          				<label id="budget_adj1" name="budget_adj1"></label>
	          			</td>
	          			<td>
	          				<label id="budget_adj2" name="budget_adj2"></label>
	          			</td>
	          			<td>
	          				<label id="budget_adj3" name="budget_adj3"></label>
	          			</td>
	          		</tr>
	          		<tr>
	          			<th>
	          				Budget Sisa
	          			</th>
	          			<td>
	          				<label id="budget_sisa4" name="budget_sisa4"></label>
	          			</td>
	          			<td>
	          				<label id="budget_sisa5" name="budget_sisa5"></label>
	          			</td>
	          			<td>
	          				<label id="budget_sisa6" name="budget_sisa6"></label>
	          			</td>
	          			<td>
	          				<label id="budget_sisa7" name="budget_sisa7"></label>
	          			</td>
	          			<td>
	          				<label id="budget_sisa8" name="budget_sisa8"></label>
	          			</td>
	          			<td>
	          				<label id="budget_sisa9" name="budget_sisa9"></label>
	          			</td>
	          			<td>
	          				<label id="budget_sisa10" name="budget_sisa10"></label>
	          			</td>
	          			<td>
	          				<label id="budget_sisa11" name="budget_sisa11"></label>
	          			</td>
	          			<td>
	          				<label id="budget_sisa12" name="budget_sisa12"></label>
	          			</td>
	          			<td>
	          				<label id="budget_sisa1" name="budget_sisa1"></label>
	          			</td>
	          			<td>
	          				<label id="budget_sisa2" name="budget_sisa2"></label>
	          			</td>
	          			<td>
	          				<label id="budget_sisa3" name="budget_sisa3"></label>
	          			</td>
	          		</tr>
	          	</table>
	          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
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
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	// var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('.select2').select2();
		fetchTable();
		$('body').toggleClass("sidebar-collapse");
	});

	function clearSearch(){
		location.reload(true);
	}

	function loadingPage(){
		$("#loading").show();
	}

	function fetchTable(){
		$('#budgetTable').DataTable().destroy();
		
		var periode = $('#periode').val();
		var category = $('#category').val();
		var data = {
			periode:periode,
			category:category
		}
		
		$('#budgetTable tfoot th').each( function () {
	      var title = $(this).text();
	      $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
	    } );

		var table = $('#budgetTable').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			"pageLength": 25,
			'buttons': {
				// dom: {
				// 	button: {
				// 		tag:'button',
				// 		className:''
				// 	}
				// },
				buttons:[
				{
					extend: 'pageLength',
					className: 'btn btn-default',
					// text: '<i class="fa fa-print"></i> Show',
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
			'searching': true,
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/budget/info") }}",
				"data" : data
			},
			"columns": [
				{ "data": "periode", "width":"5%"},
				{ "data": "budget_no", "width":"10%"},
				{ "data": "description", "width":"20%"},
				{ "data": "amount"},
				{ "data": "env"},
				{ "data": "purpose"},
				{ "data": "pic"},
				{ "data": "account_name"},
				{ "data": "category"},
				{ "data": "action"}
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
	      } );
		
      	$('#budgetTable tfoot tr').appendTo('#budgetTable thead');
	}


	function modalView(id) {
	    $("#ViewModal").modal("show");
	    var data = {
	      id:id
	    };

	    $.get('{{ url("budget/detail") }}', data, function(result, status, xhr){
	    	$("#budget_awal4").text('$'+result.datas.apr_budget_awal);
	    	$("#budget_awal5").text('$'+result.datas.may_budget_awal);
	    	$("#budget_awal6").text('$'+result.datas.jun_budget_awal);
	    	$("#budget_awal7").text('$'+result.datas.jul_budget_awal);
	    	$("#budget_awal8").text('$'+result.datas.aug_budget_awal);
	    	$("#budget_awal9").text('$'+result.datas.sep_budget_awal);
	    	$("#budget_awal10").text('$'+result.datas.oct_budget_awal);
	    	$("#budget_awal11").text('$'+result.datas.nov_budget_awal);
	    	$("#budget_awal12").text('$'+result.datas.dec_budget_awal);
	    	$("#budget_awal1").text('$'+result.datas.jan_budget_awal);
	    	$("#budget_awal2").text('$'+result.datas.feb_budget_awal);
	    	$("#budget_awal3").text('$'+result.datas.mar_budget_awal);

	    	$("#budget_adj4").text('$'+result.datas.apr_after_adj);
	    	$("#budget_adj5").text('$'+result.datas.may_after_adj);
	    	$("#budget_adj6").text('$'+result.datas.jun_after_adj);
	    	$("#budget_adj7").text('$'+result.datas.jul_after_adj);
	    	$("#budget_adj8").text('$'+result.datas.aug_after_adj);
	    	$("#budget_adj9").text('$'+result.datas.sep_after_adj);
	    	$("#budget_adj10").text('$'+result.datas.oct_after_adj);
	    	$("#budget_adj11").text('$'+result.datas.nov_after_adj);
	    	$("#budget_adj12").text('$'+result.datas.dec_after_adj);
	    	$("#budget_adj1").text('$'+result.datas.jan_after_adj);
	    	$("#budget_adj2").text('$'+result.datas.feb_after_adj);
	    	$("#budget_adj3").text('$'+result.datas.mar_after_adj);

	    	$("#budget_sisa4").text('$'+result.datas.apr_sisa_budget);
	    	$("#budget_sisa5").text('$'+result.datas.may_sisa_budget);
	    	$("#budget_sisa6").text('$'+result.datas.jun_sisa_budget);
	    	$("#budget_sisa7").text('$'+result.datas.jul_sisa_budget);
	    	$("#budget_sisa8").text('$'+result.datas.aug_sisa_budget);
	    	$("#budget_sisa9").text('$'+result.datas.sep_sisa_budget);
	    	$("#budget_sisa10").text('$'+result.datas.oct_sisa_budget);
	    	$("#budget_sisa11").text('$'+result.datas.nov_sisa_budget);
	    	$("#budget_sisa12").text('$'+result.datas.dec_sisa_budget);
	    	$("#budget_sisa1").text('$'+result.datas.jan_sisa_budget);
	    	$("#budget_sisa2").text('$'+result.datas.feb_sisa_budget);
	    	$("#budget_sisa3").text('$'+result.datas.mar_sisa_budget);
	    })
	  }
</script>
@endsection

