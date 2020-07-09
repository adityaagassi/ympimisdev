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
		Exchange Rate <span class="text-purple">{{ $title_jp }}</span>
	</h1>
	<ol class="breadcrumb">
		<li>
			<a data-toggle="modal" data-target="#createModal" class="btn btn-md bg-purple" style="color:white"><i class="fa fa-plus"></i> Create New Exchange Rate</a>
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
						
						<div class="col-md-3">
							<div class="form-group">
								<label>Tanggal</label>
								<input type="text" class="form-control datepicker" id="tanggal" name="tanggal" placeholder="Masukkan tanggal">
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
						<div class="box-body">
							<table id="rateTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width:5%;">Periode</th>
										<th style="width:5%;">Currency</th>
										<th style="width:5%;">Rate</th>
										<th style="width:5%;">Action</th>
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
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="createModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	    <div class="modal-dialog modal-lg" style="width: 1100px">
	      <div class="modal-content">
	        <div class="modal-header">
	          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	          <h4 class="modal-title" id="myModalLabel"><center>Create Exchange Rate<b></b></center></h4>
	        </div>
	        <div class="modal-body">
	          <div class="box-body">
	            <input type="hidden" value="{{csrf_token()}}" name="_token" />
	            <div class="form-group row" align="left">
	              <div class="col-sm-1"></div>
	              <label class="col-sm-2">Periode<span class="text-red">*</span></label>
	              <div class="col-sm-8">
	              	<div class="input-group date">
		              <div class="input-group-addon">
		                <i class="fa fa-calendar"></i>
		              </div>
		              <input type="text" class="form-control datepicker" id="periode" placeholder="Masukkan Periode">
		            </div>
	             </div>
	           </div>
	           <div class="form-group row" align="left">
		            <div class="col-sm-1"></div>
		            <label class="col-sm-2">Currency<span class="text-red">*</span></label>
		            <div class="col-sm-8">
		              <select class="form-control select2" id="currency" name="currency" style="width: 100%;" data-placeholder="Pilih Currency" onchange="currency()" required>
		                <option></option>
		                <option value="USD">USD</option>
		                <option value="JPY">JPY</option>
		                <option value="IDR">IDR</option>
		              </select>
		            </div>
		          </div>

		         <div class="form-group row" align="left">
		            <div class="col-sm-1"></div>
		            <label class="col-sm-2">Rate<span class="text-red">*</span></label>
		            <div class="col-sm-8">
		            	<div class="input-group"> 
			            	<span class="input-group-addon" id="ket_harga">?</span>
			                <input type="text" class="form-control" id="rate">
		            	</div>
		            </div>
		        </div>
	        </div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
	        <button type="button" onclick="create()" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-plus"></i> Create</button>
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
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	// var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	$('.datepicker').datepicker({
		format: "yyyy-mm",
        startView: "months", 
        minViewMode: "months",
		autoclose: true
	});

	jQuery(document).ready(function() {
		$('.select2').select2();
		fetchTable();
	});

	function clearSearch(){
		location.reload(true);
	}

	function loadingPage(){
		$("#loading").show();
	}

	function fetchTable(){
		$('#rateTable').DataTable().destroy();

		var tanggal = $('#tanggal').val();

		var data = {
			tanggal:tanggal,
		}
		
		$('#rateTable tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input id="search" style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
		} );

		var table = $('#rateTable').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			"pageLength": 10,
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
				"url" : "{{ url("fetch/exchange_rate") }}",
				"data" : data
			},
			"columns": [
			{ "data": "periode"},
			{ "data": "currency"},
			{ "data": "rate"},
			{ "data": "action"}
			]
		});

		table.columns().every( function () {
			var that = this;

			$('#search', this.footer() ).on( 'keyup change', function () {
				if ( that.search() !== this.value ) {
					that
					.search( this.value )
					.draw();
				}
			} );
		} );
		
		$('#rateTable tfoot tr').appendTo('#rateTable thead');
	}

	function currency(){

	    var mata_uang = $('#currency').val();

	    if (mata_uang == "USD") {
	        $('#ket_harga').text("$");       
	    }
	    else if (mata_uang == "IDR") {
          	$('#ket_harga').text("Rp. ");
	    }

	    else if (mata_uang == "JPY") {
	        $('#ket_harga').text("¥");
	    }

	    var harga = document.getElementById("rate");
        
        harga.addEventListener("keyup", function(e) {
         	harga.value = formatUang(this.value, "");
        });
  	}

  	/* Fungsi formatUang */
    function formatUang(angka, prefix) {
      var number_string = angka.replace(/[^,\d]/g, "").toString(),
        split = number_string.split(","),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

      // tambahkan titik jika yang di input sudah menjadi angka ribuan
      if (ribuan) {
        separator = sisa ? "." : "";
        rupiah += separator + ribuan.join(".");
      }

      rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
      return prefix == undefined ? rupiah : rupiah ? "" + rupiah : "";
    }

  	function create() {

      var data = {
        periode: $("#periode").val(),
        currency: $("#currency").val(),
        rate: $("#rate").val().replace(/\D/g, ""),
      };

      // console.log(data);

      $.post('{{ url("create/exchange_rate") }}', data, function(result, status, xhr){
        if (result.status == true) {
          $('#rateTable').DataTable().ajax.reload(null, false);
          openSuccessGritter("Success","New Exchange Rate has been created.");
        } else {
          openErrorGritter("Error",result.datas);
        }
      })
    }

    function modalDelete(id) {
      var data = {
        id: id
      };

      if (!confirm("Apakah anda yakin ingin menghapus ini?")) {
        return false;
      }

      $.post('{{ url("delete/exchange_rate") }}', data, function(result, status, xhr){
        $('#rateTable').DataTable().ajax.reload(null, false);
        openSuccessGritter("Success","Berhasil Hapus Exchange Rate");
      })
    }


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
          time: '2000'
        });
      }
</script>
@endsection

