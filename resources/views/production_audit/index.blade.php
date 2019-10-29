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
		{{ $activity_name }} <span class="text-purple">{{ $departments }}</span>
		{{-- <small> <span class="text-purple">??</span></small> --}}
	</h1>
	<ol class="breadcrumb">
		{{-- <li>
			<button href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#reprintModal">
				<i class="fa fa-print"></i>&nbsp;&nbsp;Reprint FLO
			</button>
		</li> --}}
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
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				{{-- <div class="box-header">
					<h3 class="box-title">Filter <span class="text-purple">{{ $activity_name }}</span></h3>
				</div> --}}
				<div class="box-body">
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
						<div class="box-header">
							<h3 class="box-title">Filter <span class="text-purple">{{ $activity_name }}</span></h3>
						</div>
						<form role="form" method="post" action="{{url('index/production_audit/filter_audit/'.$id)}}">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="col-md-12 col-md-offset-2">
							<div class="col-md-6">
								<div class="form-group">
									<label>Date</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="date" name="date">
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-md-offset-2">
							<div class="col-md-6">
								<div class="form-group">
									<label>Origin Group</label>
									<select class="form-control select2" name="product" style="width: 100%;" data-placeholder="Choose an Origin Group...">
							            <option value=""></option>
							              @foreach($product as $product)
							                <option value="{{ $product->origin_group_name }}">{{ $product->origin_group_name }}</option>
							              @endforeach
							        </select>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-md-offset-2">
							<div class="col-md-6">
								<div class="form-group pull-right">
									<a href="{{ url('index/activity_list/filter/'.$id_departments.'/1') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/production_audit/index/'.$id) }}" class="btn btn-danger">Clear</a>
									<button type="submit" class="btn btn-primary col-sm-14">Search</button>
								</div>
							</div>
						</div>
						</form>
					</div>
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
						<div class="box-header">
							<h3 class="box-title">Cetak <span class="text-purple">{{ $activity_name }}</span></h3>
						</div>
						<form target="_blank" role="form" method="post" action="{{url('index/production_audit/print_audit/'.$id)}}">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="col-md-12 col-md-offset-2">
							<div class="col-md-6">
								<div class="form-group">
									<label>Date</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="date-print" name="date" required>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-md-offset-2">
							<div class="col-md-6">
								<div class="form-group">
									<label>Origin Group</label>
									<select class="form-control select2" name="product" style="width: 100%;" data-placeholder="Choose an Origin Group..." required>
							            <option value=""></option>
							              @foreach($product2 as $product2)
							                <option value="{{ $product2->origin_group_name }}">{{ $product2->origin_group_name }}</option>
							              @endforeach
							        </select>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-md-offset-2">
							<div class="col-md-6">
								<div class="form-group">
									<label>Proses</label>
									<select class="form-control select2" name="proses" style="width: 100%;" data-placeholder="Choose a Proses..." required>
							            <option value=""></option>
							              @foreach($proses as $proses)
							                <option value="{{ $proses->proses }}">{{ $proses->product }} - {{ $proses->proses }}</option>
							              @endforeach
							        </select>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-md-offset-2">
							<div class="col-md-6">
								<div class="form-group pull-right">
									<button type="submit" class="btn btn-primary col-sm-14">Print</button>
								</div>
							</div>
						</div>
						</form>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						{{-- <div class="col-md-12 col-md-offset-9">
							<div class="col-md-3"> --}}
								<div class="form-group pull-right">
									<a href="{{ url('index/production_audit/create/'.$id) }}" class="btn btn-primary">Create {{ $activity_alias }}</a>
								</div>
							{{-- </div>
						</div> --}}
					</div>
				  <div class="row">
				    <div class="col-xs-12">
				      <div class="box">
				        <div class="box-body">
				          <table id="example1" class="table table-bordered table-striped table-hover">
				            <thead style="background-color: rgba(126,86,134,.7);">
				              <tr>
				                <th>Product</th>
				                <th>Proses</th>
				                <th>Date</th>
				                <th>Point Pengecekan</th>
				                <th>Cara Cek</th>
				                <th>Foto Kondisi Aktual</th>
				                <th>Kondisi</th>
				                <th>PIC</th>
				                <th>Auditor</th>
				                <th>Action</th>
				              </tr>
				            </thead>
				            <tbody>
				              @if(count($production_audit) != 0)
				              @foreach($production_audit as $production_audit)
				              <tr>
				                <td>{{$production_audit->point_check_audit->product}}</td>
				                <td>{{$production_audit->point_check_audit->proses}}</td>
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
				                <td>
				                  <center>
				                    <a class="btn btn-info btn-xs" href="{{url('index/production_audit/show/'.$id.'/'.$production_audit->id)}}">View</a>
				                    <a href="{{url('index/production_audit/edit/'.$id.'/'.$production_audit->id)}}" class="btn btn-warning btn-xs">Edit</a>
				                    <a href="javascript:void(0)" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("index/production_audit/destroy") }}', '{{ $production_audit->point_check_audit->product }} - {{ $production_audit->point_check_audit->proses }} - {{ $production_audit->date }}','{{ $id }}', '{{ $production_audit->id }}');">
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
		$('#date').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			todayHighlight: true
		});
		$('#date-print').datepicker({
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
    function deleteConfirmation(url, name, audit_id,id) {
      jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
      jQuery('#modalDeleteButton').attr("href", url+'/'+audit_id+'/'+id);
    }
  </script>
@endsection