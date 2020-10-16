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
  /*text-align:center;*/
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
		{{ $activity_name }} Proses {{ $proses }} - {{ $leader }}
		<!-- <a href="{{ url('index/production_audit/create/'.$id.'/'.$product.'/'.$proses) }}" class="btn btn-primary pull-right">Buat Audit</a> -->
		<a class="btn btn-info pull-right" style="color:white" onclick="pointCheck()">Buat Audit</a>
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
	@if (session('error'))
		<div class="alert alert-warning alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4> Warning!</h4>
			{{ session('error') }}
		</div>   
	@endif
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, Please Wait. <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-xs-4 col-sm-4 col-md-6 col-lg-4">
						<div class="box-header">
							<h3 class="box-title">Filter Audit NG Jelas</h3>
						</div>
						<form role="form" method="post" action="{{url('index/production_audit/filter_audit/'.$id.'/'.$product.'/'.$proses)}}">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="col-md-12 col-md-offset-2">
							<div class="col-md-8">
								<div class="form-group">
									<label>Date</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="date" name="date" autocomplete="off" placeholder="Select Month">
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-md-offset-2">
							<div class="col-md-10">
								<div class="form-group pull-right">
									<a href="{{ url('index/production_audit/details/'.$id) }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/production_audit/index/'.$id.'/'.$product.'/'.$proses) }}" class="btn btn-danger">Clear</a>
									<button type="submit" class="btn btn-primary col-sm-14">Search</button>
								</div>
							</div>
						</div>
						</form>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<div class="box-header">
							<h3 class="box-title">Cetak {{ $activity_name }}</h3>
						</div>
						<form target="_blank" role="form" method="post" action="{{url('index/production_audit/print_audit/'.$id)}}">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="col-md-12 col-md-offset-2">
							<div class="col-md-8">
								<div class="form-group">
									<label>Date</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="date-print" name="date" required autocomplete="off" placeholder="Select Month">
									</div>
								</div>
							</div>
						</div>
						<input type="hidden" class="form-control" name="product" value="{{ $product }}" readonly>
						<input type="hidden" class="form-control" name="proses" value="{{ $proses }}" readonly>
						<div class="col-md-12 col-md-offset-2">
							<div class="col-md-8">
								<div class="form-group pull-right">
									<button type="submit" class="btn btn-primary col-sm-14">Print</button>
								</div>
							</div>
						</div>
						</form>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<div class="box-header">
							<h3 class="box-title">Kirim Email ke Foreman</span></h3>
						</div>
						<form role="form" method="post" action="{{url('index/production_audit/sendemail/'.$id)}}">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="col-md-12 col-md-offset-2">
							<div class="col-md-8">
								<div class="form-group">
									<label>Date</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="date-email" name="date" required autocomplete="off" placeholder="Select Month">
									</div>
								</div>
							</div>
						</div>
						<input type="hidden" class="form-control" name="product" value="{{ $product }}" readonly>
						<input type="hidden" class="form-control" name="proses" value="{{ $proses }}" readonly>
						<div class="col-md-12 col-md-offset-2">
							<div class="col-md-8">
								<div class="form-group pull-right">
									<button type="submit" class="btn btn-primary col-sm-14">Kirim Email</button>
								</div>
							</div>
						</div>
						</form>
					</div>
				  <div class="row">
				    <div class="col-xs-12" style="overflow-x:scroll;">
			          <table id="example1" class="table table-bordered table-striped table-hover">
			            <thead style="background-color: rgba(126,86,134,.7);">
			              <tr>
			                <th>Date</th>
			                <th>Point Pengecekan</th>
			                <th>Cara Cek</th>
			                <th>Foto Kondisi Aktual</th>
			                <th>Kondisi</th>
			                <th>PIC</th>
			                <th>Auditor</th>
			                <th>Send Status</th>
			                <th>Approve Status</th>
			                <th>Action</th>
			              </tr>
			            </thead>
			            <tbody>
			              @if(count($production_audit) != 0)
			              @foreach($production_audit as $production_audit)
			              <tr>
			                <td>{{$production_audit->date}}</td>
			                <td><?php echo $production_audit->point_check_audit->point_check?></td>
			                <td><?php echo $production_audit->point_check_audit->cara_cek?></td>
			                <td>
			                	<img width="100px" src="{{ url('/data_file/'.$production_audit->foto_kondisi_aktual) }}">
			                </td>
			                <td>@if($production_audit->kondisi == "Good")
			                		<label class="label label-success">{{$production_audit->kondisi}}</label>
			                	@else
			                		<label class="label label-danger">{{$production_audit->kondisi}}</label>
			                	@endif
			                </td>
			                <td>{{$production_audit->employee_pic->name}}</td>
			                <td>{{$production_audit->employee_auditor->name}}</td>
			                <td>@if($production_audit->send_status == "")
			                		<label class="label label-danger">Not Yet Sent</label>
			                	@else
			                		<label class="label label-success">Sent</label>
			                	@endif
			        		</td>
			                <td>@if($production_audit->approval == "")
			                		<label class="label label-danger">Not Approved</label>
			                	@else
			                		<label class="label label-success">Approved</label>
			                	@endif</td>
			                <td>
			                  <center>
			                    <a class="btn btn-info btn-sm" href="{{url('index/production_audit/show/'.$id.'/'.$production_audit->id)}}">View</a>
			                    <a href="{{url('index/production_audit/edit/'.$id.'/'.$production_audit->id.'/'.$product.'/'.$proses)}}" class="btn btn-warning btn-sm">Edit</a>
			                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("index/production_audit/destroy") }}', '{{ $production_audit->point_check_audit->product }} - {{ $production_audit->point_check_audit->proses }} - {{ $production_audit->date }}','{{ $id }}', '{{ $production_audit->id }}','{{ $product }}','{{ $proses }}');">
			                      Delete
			                    </a>
			                  </center>
			                </td>
			              </tr>
			              @endforeach
			              @endif
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

<div class="modal fade" id="create_modal">
	<div class="modal-dialog modal-lg">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	        <h4 class="modal-title" align="center"><b>Pilih Point Check</b></h4>
	      </div>
	      <div class="modal-body">
	      	<div class="row" id="point_check">
	      		
	      	</div>
	      </div>
	  </div>
	</div>
</div>
</section>

<div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
        </div>
        <div class="modal-body">
          Are you sure delete?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
        </div>
      </div>
    </div>
  </div>
@endsection


@section('scripts')
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		// $('#date').datepicker({
		// 	autoclose: true,
		// 	format: 'yyyy-mm',
		// 	todayHighlight: true
		// });
		$('#date').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});
		$('#date-print').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});
		$('#date-email').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});
		$('.select2').select2({
			language : {
				noResults : function(params) {
					return "There is no date";
				}
			}
		});
	});

	function pointCheck() {
		$('#loading').show();
		var id = '{{$id}}';
		var proses = '{{$proses}}';
		var product = '{{$product}}';

		var data = {
			id:'{{$id}}',
			proses:'{{$proses}}',
			product:'{{$product}}',
		}

		$.get('{{ url("fetch/production_audit/point_check") }}', data, function(result, status, xhr){
			if (result.status) {
				var point = '';
				$('#point_check').empty();

				$.each(result.point_check_audit, function(key, value) {
					var url = "{{ url('index/production_audit/create_by_point_check/') }}";
					urlall = url + '/' + id + '/' + product+ '/' + proses + '/' + value.id;
					point += '<div class="col-xs-6" style="padding-bottom:10px">';
					point += '<a class="btn btn-info" href="'+urlall+'" style="text-decoration:none;width: 100%;">';
					point += value.point_check+'</a>';
					point += '</div>';
				});
				$('#point_check').append(point);
				$('#loading').hide();
				$('#create_modal').modal('show');
			}
		});
	}

	
</script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
  <script src="{{ url("js/buttons.flash.min.js")}}"></script>
  <script src="{{ url("js/jszip.min.js")}}"></script>
  <script src="{{ url("js/vfs_fonts.js")}}"></script>
  <script src="{{ url("js/buttons.html5.min.js")}}"></script>
  <script src="{{ url("js/buttons.print.min.js")}}"></script>
  <script>
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
    function deleteConfirmation(url, name, audit_id,id,product,proses) {
      jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
      jQuery('#modalDeleteButton').attr("href", url+'/'+audit_id+'/'+id+'/'+product+'/'+proses);
    }
  </script>
@endsection