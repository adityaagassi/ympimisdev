@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $page }} <span class="text-purple">??</span>
	</h1>
	<ol class="breadcrumb">
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
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-body">
					<div class="col-xs-4">
						<div class="box-header">
							<h3 class="box-title">Filter</h3>
						</div>
						<form role="form" method="post" action="{{url('index/press/filter_report_kanagata_lifetime')}}">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="col-md-12">
							<div class="col-md-6">
								<div class="form-group">
									<label>Date From</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="date_from" name="date_from" autocomplete="off" placeholder="Choose a Date">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Date To</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="date_to" name="date_to" autocomplete="off" placeholder="Choose a Date">
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-md-offset-3">
							<div class="col-md-9">
								<div class="form-group pull-right">
									<a href="{{ url('index/initial/press') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/press/report_kanagata_lifetime') }}" class="btn btn-danger">Clear</a>
									<button type="submit" class="btn btn-primary col-sm-14">Search</button>
								</div>
							</div>
						</div>
						</form>
					</div>
					@if($role_code == 'PROD' || $role_code == 'MIS')
					<div class="col-xs-4">
						<div class="box-header">
							<h3 class="box-title">Edit Kanagata Lifetime</h3>
						</div>
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="col-md-12">
							<div class="col-md-6">
								<div class="form-group">
									<label>Kanagata</label>
									<select class="form-control select2" name="kanagata" id="kanagata" style="width: 100%;" data-placeholder="Choose a Kanagata..." required>
					                  <option value=""></option>
					                  <option value="Punch">Punch</option>
					                  <option value="Dies">Dies</option>
					                </select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Kanagata Number</label>
									<input type="text" class="form-control pull-right" id="kanagata_number" name="kanagata_number" autocomplete="off" placeholder="Enter Kanagata Number">
								</div>
							</div>
						</div>
						<div class="col-md-9 pull-right">
							<!-- <div class="col-md-6"> -->
								<div class="form-group pull-right">
									<!-- <button type="submit" class="btn btn-primary col-sm-14">Edit</button> -->
									<button type="button" class="btn btn-warning col-sm-14" onclick="edit_kanagata('{{ url("index/kanagata/update") }}',$('#kanagata').val(),$('#kanagata_number').val());">
						               Edit
						            </button>
								</div>
							<!-- </div> -->
						</div>
					</div>
					<div class="col-xs-4">
						<div class="box-header">
							<h3 class="box-title">Reset Kanagata Lifetime</h3>
						</div>
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="col-md-12">
							<div class="col-md-6">
								<div class="form-group">
									<label>Kanagata</label>
									<select class="form-control select2" name="kanagata_reset" id="kanagata_reset" style="width: 100%;" data-placeholder="Choose a Kanagata..." required>
					                  <option value=""></option>
					                  <option value="Punch">Punch</option>
					                  <option value="Dies">Dies</option>
					                </select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>GMC Number</label>
									<input type="text" class="form-control pull-right" id="gmc_number" name="gmc_number" autocomplete="off" placeholder="Enter GMC Number">
								</div>
							</div>
						</div>
						<div class="col-md-9 pull-right">
							<!-- <div class="col-md-6"> -->
								<div class="form-group pull-right">
									<!-- <button type="submit" class="btn btn-primary col-sm-14">Edit</button> -->
									<button type="button" class="btn btn-danger col-sm-14" onclick="reset_kanagata('{{ url("index/kanagata/reset") }}',$('#kanagata_reset').val(),$('#gmc_number').val());">
						               Reset
						            </button>
								</div>
							<!-- </div> -->
						</div>
					</div>
					@endif
				  <div class="row">
				    <div class="col-xs-12">
				      <div class="box">
				        <div class="box-body" style="overflow-x: scroll;">
				          <table class="table table-bordered table-striped table-hover" id="example1">
				            <thead style="background-color: rgba(126,86,134,.7);">
				              <tr>
				              	<th>No</th>
				                <th>Employee</th>
				                <th>Date</th>
				                <th>Shift</th>
				                <th>Product</th>
				                <th>Material</th>
				                <th>Part</th>
				                <th>Process</th>
				                <th>Machine</th>
				                <th>Punch Number</th>
				                <th>Dies Number</th>
				                <th>Punch Value</th>
				                <th>Dies Value</th>
				                <th>Running Punch</th>
				                <th>Running Dies</th>
				                <th>Punch Status</th>
				                <th>Dies Status</th>
				              </tr>
				            </thead>
				            <tbody id="tableTroubleList">
				            <?php $no = 1 ?>
				              @foreach($kanagata_lifetime as $kanagata_lifetime)
				              <tr>
				              	<td>{{ $no }}</td>
				                <td>{{$kanagata_lifetime->name}}</td>
				                <td>{{$kanagata_lifetime->date}}</td>
				                <td>{{$kanagata_lifetime->shift}}</td>
				                <td>{{$kanagata_lifetime->product}}</td>
				                <td>{{$kanagata_lifetime->material_number}}</td>
				                <td>{{$kanagata_lifetime->material_name}}</td>
				                <td>{{$kanagata_lifetime->process}}</td>
				                <td>{{$kanagata_lifetime->machine}}</td>
				                <td>{{$kanagata_lifetime->punch_number}}</td>
				                <td>{{$kanagata_lifetime->die_number}}</td>
				                <td>{{$kanagata_lifetime->punch_value}}</td>
				                <td>{{$kanagata_lifetime->die_value}}</td>
				                <td>{{$kanagata_lifetime->punch_total}}</td>
				                <td>{{$kanagata_lifetime->die_total}}</td>
				                <td>{{$kanagata_lifetime->punch_status}}</td>
				                <td>{{$kanagata_lifetime->die_status}}</td>
				              </tr>
				              <?php $no++ ?>
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
	</div>
	<div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
		          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		            <span aria-hidden="true">&times;</span>
		          </button>
		          <h4 class="modal-title" align="center"><b>Edit Kanagata Lifetime</b></h4>
		        </div>
				<div class="modal-body">
			      	<div class="box-body">
			          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
			            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			            	<input type="hidden" name="url_edit" id="url_edit" class="form-control" value="" readonly required="required" title="">
				            <div class="form-group">
				              <label for="">Date</label>
							  <input type="text" name="editdate" id="editdate" class="form-control" value="" readonly required="required" title="">
				            </div>
				            <div class="form-group">
				              <label for="">PIC</label>
							  <input type="text" name="editpic" id="editpic" class="form-control" value="" readonly required="required" title="">
				            </div>
				            <div class="form-group">
				              <label>Machine</label>
				              <input type="text" name="editmachine" id="editmachine" class="form-control" value="" readonly required="required" title="">
				            </div>
				            <div class="form-group">
				              <label>Product</label>
				              <input type="text" name="editproduct" id="editproduct" class="form-control" value="" readonly required="required" title="">
				            </div>
				            <div class="form-group">
				              <label>Material</label>
				              <input type="text" name="editmaterial_number" id="editmaterial_number" class="form-control" value="" readonly required="required" title="">
				            </div>
				            <div class="form-group">
				              <label>Part</label>
				              <input type="text" name="editpart" id="editpart" class="form-control" value="" readonly required="required" title="">
				            </div>
			            </div>
			            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			            	
				            <div class="form-group">
				              <label>Punch Number</label>
				              <input type="text" name="editpunch_number" id="editpunch_number" class="form-control" value="" readonly required="required" title="">
				            </div>
				            <div class="form-group">
				              <label>Punch Value</label>
				              <input type="text" name="editpunch_value" id="editpunch_value" class="form-control" value="" readonly required="required" title="" placeholder="Enter Punch Value">
				            </div>
				            <div class="form-group">
				              <label>Punch Total</label>
				              <input type="text" name="editpunch_total" id="editpunch_total" class="form-control" value="" required="required" title="" placeholder="Enter Punch Total">
				            </div>
				            <div class="form-group">
				              <label>Dies Number</label>
				              <input type="text" name="editdies_number" id="editdies_number" class="form-control" value="" readonly required="required" title="">
				            </div>
				            <div class="form-group">
				              <label>Dies Value</label>
				              <input type="text" name="editdies_value" id="editdies_value" class="form-control" value="" required="required" readonly title="" placeholder="Enter Dies Value">
				            </div>
				            <div class="form-group">
				              <label>Dies Total</label>
				              <input type="text" name="editdies_total" id="editdies_total" class="form-control" value="" required="required" title="" placeholder="Enter Dies Total">
				            </div>
			            </div>
				          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				          	<div class="modal-footer">
				              <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
				              <input type="submit" value="Update" onclick="update()" class="btn btn-primary">
				            </div>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('#date_from').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			todayHighlight: true
		});
		$('#date_to').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			todayHighlight: true
		});
		$('.select2').select2({
			language : {
				noResults : function(params) {
					return "There is no date";
				}
			}
		});
	});

	function edit_kanagata(url,kanagata,kanagata_number) {
		if (kanagata_number === '' || kanagata === '') {
			alert('Isi Jenis Kanagata dan Kanagata Number.')
			$("#edit-modal").modal('hide');
		}else{
			$("#edit-modal").modal('show');
			$.ajax({
                url: "{{ route('kanagata_lifetime.getkanagatalifetime') }}?kanagata=" + kanagata +"&kanagata_number="+kanagata_number,
                method: 'GET',
                success: function(data) {
                  var json = data;
                  // obj = JSON.parse(json);
                  // console.log(data.data);
                  var data = data.data;
                  $("#url_edit").val(url+'/'+data.kanagata_log_id);
                  $("#editdate").val(data.date);
                  $("#editpic").val(data.pic_name);
                  $("#editmachine").val(data.machine);
                  $("#editproduct").val(data.product);
                  $("#editmaterial_number").val(data.material_number);
                  $("#editpart").val(data.part);
                  $("#editpunch_number").val(data.punch_number);
                  $("#editpunch_total").val(data.punch_total);
                  $("#editpunch_value").val(data.punch_value);
                  $("#editdies_number").val(data.die_number);
                  $("#editdies_value").val(data.die_value);
                  $("#editdies_total").val(data.die_total);
                }
            });
		}
    	
      // jQuery('#formedit2').attr("action", url+'/'+interview_id+'/'+detail_id);
      // console.log($('#formedit2').attr("action"));
    }

    function reset_kanagata(url,kanagata,gmc_number) {
		if (gmc_number === '' || kanagata === '') {
			alert('Isi Jenis Kanagata dan GMC Number.')
			// $("#edit-modal").modal('hide');
		}else{
			if (confirm('Apakah Anda ingin RESET Kanagata?')) {
				var data = {
					kanagata:kanagata,
					gmc_number:gmc_number
				}
				$.post('{{ url("index/kanagata/reset") }}', data, function(result, status, xhr){
					if(result.status){
						// $("#edit-modal").modal('hide');
						// $('#example1').DataTable().ajax.reload();
						// $('#example2').DataTable().ajax.reload();
						openSuccessGritter('Success','Kanagata Lifetime has been reset');
						window.location.reload();
					} else {
						audio_error.play();
						openErrorGritter('Error','Reset Kanagata Lifetime Failed');
					}
				});
		    }
		}
    	
      // jQuery('#formedit2').attr("action", url+'/'+interview_id+'/'+detail_id);
      // console.log($('#formedit2').attr("action"));
    }

    function update(){
		var punch_total = $('#editpunch_total').val();
		var die_total = $('#editdies_total').val();
		var url = $('#url_edit').val();

		var data = {
			punch_total:punch_total,
			die_total:die_total
		}
		console.table(data);
		
		$.post(url, data, function(result, status, xhr){
			if(result.status){
				$("#edit-modal").modal('hide');
				// $('#example1').DataTable().ajax.reload();
				// $('#example2').DataTable().ajax.reload();
				openSuccessGritter('Success','Kanagata Lifetime has been updated');
				window.location.reload();
			} else {
				audio_error.play();
				openErrorGritter('Error','Update Kanagata Lifetime Failed');
			}
		});
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
			time: '3000'
		});
	}

	jQuery(document).ready(function() {
		$('#example1 tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
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

	
</script>
  <script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
  <script src="{{ url("js/buttons.flash.min.js")}}"></script>
  <script src="{{ url("js/jszip.min.js")}}"></script>
  
  <script src="{{ url("js/buttons.html5.min.js")}}"></script>
  <script src="{{ url("js/buttons.print.min.js")}}"></script>
  <script>
    jQuery(document).ready(function() {
    	$('body').toggleClass("sidebar-collapse");
    });
  </script>
@endsection
			