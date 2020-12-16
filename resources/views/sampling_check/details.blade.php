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
		{{ $activity_name }} - {{ $leader }}
		<a class="btn btn-warning pull-right" href="{{ url('index/sampling_check/index/'.$sampling_check->activity_list_id) }}" style="margin-left: 5px">Kembali</a>
		<a class="btn btn-primary pull-right" href="{{ url('index/sampling_check/createdetails/'.$sampling_id) }}">Buat Point</a>
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
			<div class="box box-solid">
				<div class="box-header">
					<h3 class="box-title">Detail</h3>
				</div>
				<div class="box-body">
				  <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				  	<table class="table table-bordered">
						<tr>
							<td><b>Dept</b></td>
							<td>{{strtoupper($sampling_check->department)}}</td>
						</tr>
						<tr>
							<td><b>Section</b></td>
							<td>{{strtoupper($sampling_check->section)}}</td>
						</tr>
						<tr>
							<td><b>Group</b></td>
							<td>{{$sampling_check->subsection}}</td>
						</tr>
						<tr>
							<td><b>Bulan</b></td>
							<td>{{$sampling_check->month}}</td>
						</tr>
						<tr>
							<td><b>Tanggal</b></td>
							<td>{{$sampling_check->date}}</td>
						</tr>
						<tr>
							<td><b>Product</b></td>
							<td>{{$sampling_check->product}}</td>
						</tr>
					</table>
			        </div>
			        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			        	<table class="table table-bordered">
						<tr>
							<td><b>No. Seri / Part</b></td>
							<td>{{$sampling_check->no_seri_part}}</td>
						</tr>
						<tr>
							<td><b>Jumlah Cek</b></td>
							<td>{{$sampling_check->jumlah_cek}}</td>
						</tr>
						<tr>
							<td><b>Subsection</b></td>
							<td>{{$sampling_check->subsection}}</td>
						</tr>
						<tr>
							<td><b>Bulan</b></td>
							<td>{{$sampling_check->month}}</td>
						</tr>
						<tr>
							<td><b>Tanggal</b></td>
							<td>{{$sampling_check->date}}</td>
						</tr>
						<tr>
							<td><b>Product</b></td>
							<td>{{$sampling_check->product}}</td>
						</tr>
					</table>
			      </div>
				  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="overflow-x: scroll;">
			          <table id="example1" class="table table-bordered table-striped table-hover">
			            <thead style="background-color: rgba(126,86,134,.7);">
			              <tr>
			                <th>Point Check</th>
			                <th>Hasil Check</th>
			                <th>Picture Check</th>
			                <th>PIC Check</th>
			                <th>Sampling By</th>
			                <th>Action</th>
			              </tr>
			            </thead>
			            <tbody>
			              @foreach($sampling_check_details as $sampling_check_details)
			              <tr>
			                <td><?php echo $sampling_check_details->point_check ?></td>
			                <td><?php echo $sampling_check_details->hasil_check ?></td>
			                <td><img width="200px" src="{{ url('/data_file/sampling_check/'.$sampling_check_details->picture_check) }}"></td>
			                <td>{{ $sampling_check_details->pic_check }}</td>
			                <td>{{ $sampling_check_details->sampling_by }}</td>
			                <td>
			                  <center>
			                    <a type="button" class="btn btn-warning" href="{{ url('index/sampling_check/editdetails/'.$sampling_id.'/'.$sampling_check_details->id) }}">
					                  Edit
					                </a>
			                    <a href="javascript:void(0)" class="btn btn-danger" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("index/sampling_check/destroydetails") }}','{{ $sampling_id }}', '{{ $sampling_check_details->id }}');">
			                      Delete
			                    </a>
			                  </center>
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
			              </tr>
			            </tfoot>
			          </table>
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
        <div class="modal-body" id="body_delete">
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
		$('#date').datepicker({
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
  	$(function () {
      $('.select2').select2()
    });
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
    function deleteConfirmation(url, id, sampling_check_id) {
      // $('#body_delete').html("Are you sure want to delete '" + name + "'?");
      $('#modalDeleteButton').attr("href", url+'/'+id+'/'+sampling_check_id);
    }
  </script>
  <script language="JavaScript">
      function readURL(input) {
              if (input.files && input.files[0]) {
                  var reader = new FileReader();

                  reader.onload = function (e) {
                    $('#blah').show();
                      $('#blah')
                          .attr('src', e.target.result);
                  };

                  reader.readAsDataURL(input.files[0]);
              }
          }

        function readEdit(input) {
              if (input.files && input.files[0]) {
                  var reader = new FileReader();

                  reader.onload = function (e) {
                    $('#blah2').show();
                      $('#blah2')
                          .attr('src', e.target.result);
                  };

                  reader.readAsDataURL(input.files[0]);
              }
          }
    </script>
@endsection