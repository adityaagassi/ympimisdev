@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('/bower_components/qrcode/css/font-awesome.css') }}">
<link rel="stylesheet" href="{{ asset('/bower_components/qrcode/css/bootstrap.min.css') }}">
<script src="{{ asset('/bower_components/qrcode/js/jquery.min.js') }}"></script>
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
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">Interview Details <span class="text-purple"></span></h3>
				</div>
				<div class="box-body">
				  <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			          	<div class="form-group row" align="right">
				          <label class="col-sm-5">Department</label>
				          <div class="col-sm-5" align="left">
				            {{$interview->department}}
				          </div>
				        </div>
				        <div class="form-group row" align="right">
				          <label class="col-sm-5">Section</label>
				          <div class="col-sm-5" align="left">
				            {{$interview->section}}
				          </div>
				        </div>
				        <div class="form-group row" align="right">
				          <label class="col-sm-5">Sub Section</label>
				          <div class="col-sm-5" align="left">
				            {{$interview->subsection}}
				          </div>
				        </div>
				        <div class="form-group row" align="right">
				          <label class="col-sm-5">Date</label>
				          <div class="col-sm-5" align="left">
				            {{$interview->date}}
				          </div>
				        </div>
				        <div class="form-group row" align="right">
				          <label class="col-sm-5">Periode</label>
				          <div class="col-sm-5" align="left">
				            {{$interview->periode}}
				          </div>
				        </div>
			          <a class="btn btn-warning" href="{{ url('index/interview/index/'.$activity_id) }}">Back</a>
			        </div>
			        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			          	<div class="form-group row" align="right">
				          <label class="col-sm-5">Leader</label>
				          <div class="col-sm-5" align="left">
				            {{$interview->leader}}
				          </div>
				        </div>
				        <div class="form-group row" align="right">
				          <label class="col-sm-5">Foreman</label>
				          <div class="col-sm-5" align="left">
				            {{$interview->foreman}}
				          </div>
				        </div>
				        <div class="form-group row" align="right">
				          <label class="col-sm-5">Created By</label>
				          <div class="col-sm-5" align="left">
				            {{$interview->user->name}}
				          </div>
				        </div>
				        <div class="form-group row" align="right">
				          <label class="col-sm-5">Created At</label>
				          <div class="col-sm-5" align="left">
				            {{$interview->created_at}}
				          </div>
				        </div>
				        <div class="form-group row" align="right">
				          <label class="col-sm-5">Last Update</label>
				          <div class="col-sm-5" align="left">
				            {{$interview->updated_at}}
				          </div>
				        </div>
			      </div>
				  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				      <div class="box">
				      	<div class="box-header">
							<h3 class="box-title">Interview Details <span class="text-purple"></span></h3>
							<button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#create-modal">
						        Create
						    </button>
						</div>
				        <div class="box-body">
				          <table id="example1" class="table table-bordered table-striped table-hover">
				            <thead style="background-color: rgba(126,86,134,.7);">
				              <tr>
				                <th>Participant</th>
				                <th>Filosofi YAMAHA</th>
				                <th>Aturan K3 YAHAMA</th>
				                <th>10 Komitmen Berkendara</th>
				                <th>Kebijakan Mutu</th>
				                <th>5 Dasar Tindakan Bekerja</th>
				                <th>Action</th>
				              </tr>
				            </thead>
				            <tbody>
				              @foreach($interview_detail as $interview_detail)
				              <form role="form" method="post" action="{{url('index/interview/checklist/'.$interview_detail->id)}}">
				              <tr>
				                <td>{{ $interview_detail->participants->name }}</td>
				                <td>{{ $interview_detail->filosofi_yamaha }}</td>
				            	<td>{{ $interview_detail->aturan_k3 }}</td>
				            	<td>{{ $interview_detail->komitmen_berkendara }}</td>
				            	<td>{{ $interview_detail->kebijakan_mutu }}</td>
				            	<td>{{ $interview_detail->dasar_tindakan_bekerja }}</td>
				                <td>
				                  
				                </td>
				              </tr>
				              </form>
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
				              </tr>
				            </tfoot>
				          </table>

				          <table id="example2" class="table table-bordered table-striped table-hover">
				            <thead style="background-color: rgba(126,86,134,.7);">
				              <tr>
				                <th>Participant</th>
				                <th>6 Pasal Keselamatan Lalu Lintas</th>
				                <th>Budaya Kerja YMPI</th>
				                <th>5S</th>
				                <th>Komitmen Hotel Concept</th>
				                <th>Janji Tindakan Dasar Hotel Concept</th>
				                <th>Action</th>
				              </tr>
				            </thead>
				            <tbody>
				              @foreach($interview_detail2 as $interview_detail2)
				              <tr>
				                <td>{{ $interview_detail2->participants->name }}</td>
				                <td>{{ $interview_detail->enam_pasal_keselamatan }}</td>
				            	<td>{{ $interview_detail->budaya_kerja }}</td>
				            	<td>{{ $interview_detail->budaya_5s }}</td>
				            	<td>{{ $interview_detail->komitmen_hotel_konsep }}</td>
				            	<td>{{ $interview_detail->janji_tindakan_dasar }}</td>
				                <td>
				                  
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
 <div class="modal fade" id="create-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" align="center"><b>Create Interview</b></h4>
      </div>
      <div class="modal-body">
        <form role="form" method="post" action="{{url('index/interview/create_participant/'.$interview_id)}}" enctype="multipart/form-data">
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
          <div class="box-body">
          	<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
          		
          	</div>
          	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          		<div class="form-group">
	              <label for="exampleInputEmail1">Participant Name</label> 
	              <select class="form-control select3" name="nik" id="nik" style="width: 100%;" data-placeholder="Choose a Participant..." required>
					<option value=""></option>
					@foreach($operator as $operator)
						<option value="{{ $operator->employee_id }}">{{ $operator->employee_id }} - {{ $operator->name }}</option>
					@endforeach
				  </select>
	            </div>
          	</div>
          	<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
          		
          	</div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	            <div class="form-group">
	              <label for="">Filosofi Yamaha</label>
				  <div class="radio">
				    <label><input type="radio" name="filosofi_yamaha" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="filosofi_yamaha" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="filosofi_yamaha" value="Not OK">Not OK</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label for="">Aturan K3 YAMAHA</label>
				  <div class="radio">
				    <label><input type="radio" name="aturan_k3" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="aturan_k3" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="aturan_k3" value="Not OK">Not OK</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label for="">10 Komitmen Berkendara</label>
				  <div class="radio">
				    <label><input type="radio" name="komitmen_berkendara" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="komitmen_berkendara" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="komitmen_berkendara" value="Not OK">Not OK</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label for="">Kebijakan Mutu</label>
				  <div class="radio">
				    <label><input type="radio" name="kebijakan_mutu" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="kebijakan_mutu" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="kebijakan_mutu" value="Not OK">Not OK</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label for="">5 Dasar Tindakan Bekerja</label>
				  <div class="radio">
				    <label><input type="radio" name="dasar_tindakan_bekerja" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="dasar_tindakan_bekerja" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="dasar_tindakan_bekerja" value="Not OK">Not OK</label>
				  </div>
	            </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            	<div class="form-group">
	              <label for="">6 Pasal Keselamatan Lalu Lintas YAMAHA</label>
				  <div class="radio">
				    <label><input type="radio" name="enam_pasal_keselamatan" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="enam_pasal_keselamatan" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="enam_pasal_keselamatan" value="Not OK">Not OK</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label for="">Budaya Kerja YMPI</label>
				  <div class="radio">
				    <label><input type="radio" name="budaya_kerja" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="budaya_kerja" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="budaya_kerja" value="Not OK">Not OK</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label for="">5S</label>
				  <div class="radio">
				    <label><input type="radio" name="budaya_5s" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="budaya_5s" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="budaya_5s" value="Not OK">Not OK</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label for="">Komitmen Hotel Concept</label>
				  <div class="radio">
				    <label><input type="radio" name="komitmen_hotel_konsep" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="komitmen_hotel_konsep" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="komitmen_hotel_konsep" value="Not OK">Not OK</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label for="">Janji Tindakan Dasar Hotel Concept</label>
				  <div class="radio">
				    <label><input type="radio" name="janji_tindakan_dasar" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="janji_tindakan_dasar" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="janji_tindakan_dasar" value="Not OK">Not OK</label>
				  </div>
	            </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </form>
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
    $(function () {
      $('.select3').select2({
      	dropdownParent: $('#create-modal')
      })
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

    jQuery(document).ready(function() {
      $('#example2 tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
      } );
      var table = $('#example2').DataTable({
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

      $('#example2 tfoot tr').appendTo('#example2 thead');

    });
    function deleteConfirmation(url, name, id, sampling_check_id) {
      jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
      jQuery('#modalDeleteButton').attr("href", url+'/'+id+'/'+sampling_check_id);
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