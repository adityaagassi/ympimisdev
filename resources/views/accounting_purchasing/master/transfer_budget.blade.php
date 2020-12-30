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
		Transfer Budget <span class="text-purple">{{ $title_jp }}</span>
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
					<h3 class="box-title">Do Your Transfer Here</h3>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<div class="col-md-3">
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Budget From</label>
								<select class="form-control select2" id='budget_from' data-placeholder="Select Budget From" style="width: 100%;" onchange="budgetChange()">
						            <option value=""></option>
						            @foreach($budgets as $budget)
						            <option value="{{ $budget->budget_no }}">{{ $budget->budget_no }} - {{ $budget->description }}</option>
						            @endforeach
								</select>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label>Budget To</label>
								<select class="form-control select2" id='budget_to' data-placeholder="Select Budget To" style="width: 100%;" onchange="budgetChange()">
									<option value=""></option>
						            @foreach($budgets as $budget)
						            <option value="{{ $budget->budget_no }}">{{ $budget->budget_no }} - {{ $budget->description }}</option>
						            @endforeach
								</select>
							</div>
						</div>
					</div>

					<div class="col-xs-12" id="amount_hide">
						<div class="col-md-2">
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Amount ($)</label>
								<input type="text" id="amount" name="amount" class="form-control" placeholder="Jumlah">
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label>Note</label>
								<textarea id="note" name="note" class="form-control" placeholder="Masukkan Catatan"></textarea>
							</div>
						</div>
					</div>

					<div class="col-xs-12"  id="transfer_hide">
						<div class="col-md-5">
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<button type="button" class="btn btn-success" style="width: 100%" onclick="transfer()"><i class="fa fa-arrow-right"></i> Transfer Budget</button>
							</div>
						</div>
					</div>
					<div class="col-xs-12">
				          <hr style="border: 1px solid red;background-color: red">
				      </div>

					<div class="col-xs-12" style="margin-top: 30px">
						<div class="box-body" style="padding-top: 0;">
							<table id="fromTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width:5%;">Request Date</th>
										<th style="width:5%;">Budget From - To</th>
										<th style="width:5%;">Amount ($)</th>
										<th style="width:5%;">Note</th>
									</tr>
								</thead>
								<tbody id="tablefrom">
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

	jQuery(document).ready(function() {
		$('.select2').select2({
			dropdownAutoWidth : true,
			allowClear:true
		});
		fetchTable();
		$('body').toggleClass("sidebar-collapse");

		$('#amount_hide').hide();
		$('#transfer_hide').hide();

	});

	function clearSearch(){
		location.reload(true);
	}

	function loadingPage(){
		$("#loading").show();
	}

	function budgetChange(){

		var budget_from = $('#budget_from').val();
		var budget_to = $('#budget_to').val();

		if (budget_from != "" && budget_to != "") {
			$('#amount_hide').show();	
			$('#transfer_hide').show();
		}
		else{
			$('#amount_hide').hide();
			$('#transfer_hide').hide();
		}
	}

	function transfer(){
		var amount = $('#amount').val();
		var budget_from = $('#budget_from').val();
		var budget_to = $('#budget_to').val();
		var note = $('#note').val();

		$("#loading").show();

		if (amount == "") {
			$("#loading").hide();
		    alert("Kolom Jumlah Harap diisi");
		    $("html").scrollTop(0);
		    return false;
		}

		if (note == "") {
			$("#loading").hide();
		    alert("Kolom Catatan Harap diisi");
		    $("html").scrollTop(0);
		    return false;
		}

		if (budget_from == "" || budget_to == "") {
			$("#loading").hide();
		    alert("Kolom Budget Harap diisi");
		    $("html").scrollTop(0);
		    return false;
		}

		var data = {
		    amount: amount,
		    budget_from: budget_from,
		    budget_to: budget_to,
		    note: note
	    };

	    $.post('{{ url("transfer/budget/new") }}', data, function(result, status, xhr){
		    if(result.status == true){
		    	$("#loading").hide();
		        openSuccessGritter("Success","Budget Berhasil Di Transfer");
		        location.reload();
		    }
		    else {
		        $("#loading").hide();
		        openErrorGritter('Error!', result.datas);
		    }
	    });
	}

	function fetchTable(){
		// var periode = $('#periode').val();
		var data = {
			// periode:periode
		}

		 $.get('{{ url("fetch/transfer") }}', data, function(result, status, xhr){
	      if(xhr.status == 200){
	        if(result.status){

	       	  $('#fromTable').DataTable().clear();
			  $('#fromTable').DataTable().destroy();

	          $("#tablefrom").find("td").remove();  
	          $('#tablefrom').html("");

	          var table = "";

	          $.each(result.datas, function(key, value) {

	              table += '<tr>';
	              table += '<td>'+value.request_date+'</td>';
	              table += '<td>'+value.budget_from+' -> '+value.budget_to+'</td>';
	              
	    //           if (value.date_approval_from == null) {
		   //          table += '<td><label class="label label-danger"><i class="fa fa-close"> Unverified</label></td>';	              	
	    //           }
	    //           else{
	    //           	table += '<td><label class="label label-success"><i class="fa fa-check"> Verified</label></td>';
	    //           }


	    //           if (value.date_approval_to == null) {
		   //          table += '<td><label class="label label-danger"><i class="fa fa-close"> Unverified</label></td>';	              	
	    //           }
	    //           else{
	    //           	table += '<td><label class="label label-success"><i class="fa fa-check"> Verified</label></td>';
	    //           }
	                
	    //           if (value.date_approval_to == null) {
		   //          table += '<td></td>';	              	
	    //           }
	    //           else{
					// var approval = value.date_approval_to.split("_");
	    //           	table += '<td>'+approval[1]+'</td>';
	    //           }

	              table += '<td>$ '+value.amount+'</td>';
	              table += '<td>'+value.note+'</td>';
	              table += '</tr>';
	          })

	          $('#tablefrom').append(table);

	          $('#fromTable tfoot th').each( function () {
		        var titlefrom = $(this).text();
			        $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+titlefrom+'" size="20"/>' );
			      } );
			    var tablefrom = $('#fromTable').DataTable({
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
			        'paging': false,
			        'lengthChange': true,
			        'pageLength': 10,
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
	        }
	        else{
		      alert('Attempt to retrieve data failed');
		    }
	      }

		    tablefrom.columns().every( function () {
		      var that = this;

		      $( 'input', this.footer() ).on( 'keyup change', function () {
		        if ( that.search() !== this.value ) {
		          that
		          .search( this.value )
		          .draw();
		        }
		      } );
		    } );

		    $('#fromTable tfoot tr').appendTo('#fromTable thead');
	    });

	}

	function modalDelete(id) {
      var data = {
        id: id
      };

      if (!confirm("Apakah anda yakin ingin menghapus ini?")) {
        return false;
      }

      $.post('{{ url("delete/actual/transaksi") }}', data, function(result, status, xhr){
        $('#TranskasiTable').DataTable().ajax.reload(null, false);
        openSuccessGritter("Success","Berhasil Hapus Data Actual");
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

