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
					<h3 class="box-title">Training Details <span class="text-purple"></span></h3>
				</div>
				<div class="box-body">
				  <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			          <div class="form-group row" align="right">
			            <label class="col-sm-5">Department</label>
			            <div class="col-sm-5" align="left">
			              {{$training_report->department}}
			            </div>
			          </div>
			          <div class="form-group row" align="right">
			            <label class="col-sm-5">Section</label>
			            <div class="col-sm-5" align="left">
			              {{$training_report->section}}
			            </div>
			          </div>
			          <div class="form-group row" align="right">
			            <label class="col-sm-5">Product</label>
			            <div class="col-sm-5" align="left">
			              {{$training_report->product}}
			            </div>
			          </div>
			          <div class="form-group row" align="right">
			            <label class="col-sm-5">Periode</label>
			            <div class="col-sm-5" align="left">
			              {{$training_report->periode}}
			            </div>
			          </div>
			          <div class="form-group row" align="right">
			            <label class="col-sm-5">Tanggal</label>
			            <div class="col-sm-5" align="left">
			              {{$training_report->date}}
			            </div>
			          </div>
			          <div class="form-group row" align="right">
			            <label class="col-sm-5">Waktu</label>
			            <div class="col-sm-5" align="left">
			              <?php 
			                $timesplit=explode(':',$training_report->time);
			                $min=($timesplit[0]*60)+($timesplit[1])+($timesplit[2]>30?1:0); ?>
			              {{$min.' Min'}}
			            </div>
			          </div>
			          <div class="form-group row" align="right">
			            <label class="col-sm-5">Trainer</label>
			            <div class="col-sm-5" align="left">
			              {{$training_report->trainer}}
			            </div>
			          </div>
			          <div class="form-group row" align="right">
			            <label class="col-sm-5">Theme</label>
			            <div class="col-sm-5" align="left">
			              {{$training_report->theme}}
			            </div>
			          </div>
			          @if($session_training == "view")
			          	<a href="{{ url('index/training_report/index/'.$activity_id) }}" class="btn btn-warning">Back</a>
			          @endif
			        </div>
			        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			          <div class="form-group row" align="right">
			            <label class="col-sm-5">Isi Training</label>
			            <div class="col-sm-5" align="left">
			              <?php echo $training_report->isi_training ?>
			            </div>
			          </div>
			          <div class="form-group row" align="right">
			            <label class="col-sm-5">Tujuan</label>
			            <div class="col-sm-5" align="left">
			              {{$training_report->tujuan}}
			            </div>
			          </div>
			          <div class="form-group row" align="right">
			            <label class="col-sm-5">Standard</label>
			            <div class="col-sm-5" align="left">
			              {{$training_report->standard}}
			            </div>
			          </div>
			          <div class="form-group row" align="right">
			            <label class="col-sm-5">Leader</label>
			            <div class="col-sm-5" align="left">
			              {{$training_report->leader}}
			            </div>
			          </div>
			          <div class="form-group row" align="right">
			            <label class="col-sm-5">Foreman</label>
			            <div class="col-sm-5" align="left">
			              {{$training_report->foreman}}
			            </div>
			          </div>
			          <div class="form-group row" align="right">
			            <label class="col-sm-5">Catatan</label>
			            <div class="col-sm-5" align="left">
			              <?php echo $training_report->notes?>
			            </div>
			          </div>
			          <div class="form-group row" align="right">
			            <label class="col-sm-5">Created By</label>
			            <div class="col-sm-5" align="left">
			              {{$training_report->user->name}}
			            </div>
			          </div>
			          <div class="form-group row" align="right">
			            <label class="col-sm-5">Created At</label>
			            <div class="col-sm-5" align="left">
			              {{$training_report->created_at}}
			            </div>
			          </div>
			          <div class="form-group row" align="right">
			            <label class="col-sm-5">Last Update</label>
			            <div class="col-sm-5" align="left">
			              {{$training_report->updated_at}}
			            </div>
			          </div>
			          {{-- <input type="text" id="textnama"> --}}
			      </div>
				  <div class="row">
				    <div class="col-xs-6">
				      <div class="box">
				      	<div class="box-header">
							<h3 class="box-title">Training Pictures <span class="text-purple"></span></h3>
							<form role="form" method="post" action="{{url('index/training_report/insertpicture/'.$id)}}" enctype="multipart/form-data">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />

								<div class="form-group">
									<input type="file" class="btn btn-primary pull-right" id="" placeholder="Input field" name="file" onchange="readURL(this);" required>
									<br>
									<img width="200px" id="blah" src="" style="display: none" alt="your image" />
								</div>
								<br>
								<button type="submit" class="btn btn-primary pull-right">Upload</button>
							</form>
						</div>
				        <div class="box-body">
				          <table id="example1" class="table table-bordered table-striped table-hover">
				            <thead style="background-color: rgba(126,86,134,.7);">
				              <tr>
				                <th>Pictures</th>
				                <th>Action</th>
				              </tr>
				            </thead>
				            <tbody>
				              @foreach($training_picture as $training_picture)
				              <tr>
				                <td><img width="100px" src="{{ url('/data_file/training/'.$training_picture->picture) }}"></td>
				                <td>
				                  <center>
				                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit-modal" onclick="editpicture('{{ url("index/training_report/editpicture") }}','{{ url('/data_file/training/') }}', '{{ $training_picture->picture }}','{{ $id }}', '{{ $training_picture->id }}');">
						               <i class="fa fa-edit"></i>
						            </button>
				                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("index/training_report/destroypicture") }}', '{{ $training_picture->picture }}','{{ $id }}', '{{ $training_picture->id }}');">
				                      <i class="fa fa-trash"></i>
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
				              </tr>
				            </tfoot>
				          </table>
				        </div>
				      </div>
				    </div>
				    <div class="col-xs-6">
				      <div class="box">
				      	<div class="box-header">
							<h3 class="box-title">Training Participants <span class="text-purple"></span></h3>
							{{-- <a class="btn btn-xs btn-primary pull-right" href="{{ secure_url('index/training_report/scan_employee/'.$id) }}" target="_blank">Scan Employee</a>
							<div class="panel-body text-center" >
				              <canvas></canvas>
				              <hr>
				              <select></select>
				            </div> --}}
							<form role="form" method="post" action="{{url('index/training_report/insertparticipant/'.$id)}}" enctype="multipart/form-data">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />

								<div class="form-group" align="right">
									<input type="hidden" value="{{ $id }}" id="id_training">
									<select class="form-control select2" name="participant_name" style="width: 100%;" data-placeholder="Choose a Participant..." required>
						                <option value=""></option>
						                @foreach($operator as $operator)
						                  <option value="{{ $operator->name }}">{{ $operator->employee_id }} - {{ $operator->name }}</option>
						                @endforeach
						              </select>
								</div>
								<button type="submit" class="btn btn-primary pull-right">Submit</button>
							</form>
						</div>
				        <div class="box-body">
				          <table id="example2" class="table table-bordered table-striped table-hover">
				            <thead style="background-color: rgba(126,86,134,.7);">
				              <tr>
				                <th>Name</th>
				                <th>Attendance</th>
				                <th>Action</th>
				              </tr>
				            </thead>
				            <tbody>
				              @foreach($training_participant as $training_participant)
				              <tr>
				                <td>
				                	{{ $training_participant->participant_name }}
				                </td>
				                <td>
				                	{{ $training_participant->participant_absence }}
				                </td>
				                <td>
				                  <center>
				                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit-modal2" onclick="editparticipant('{{ url("index/training_report/editparticipant") }}','{{ $training_participant->participant_name }}','{{ $id }}', '{{ $training_participant->id }}');">
						               <i class="fa fa-edit"></i>
						            </button>
				                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal2" onclick="deleteConfirmation2('{{ url("index/training_report/destroyparticipant") }}', '{{ $training_participant->participant_name }}','{{ $id }}', '{{ $training_participant->id }}');">
				                      <i class="fa fa-trash"></i>
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
  <div class="modal modal-danger fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
          <a id="modalDeleteButton2" href="#" type="button" class="btn btn-danger">Delete</a>
        </div>
      </div>
    </div>
  </div>
 <div class="modal fade" id="edit-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" align="center"><b>Edit Picture</b></h4>
      </div>
      <div class="modal-body">
        <form role="form" method="post" enctype="multipart/form-data" id="formedit" action="#">
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
          <div class="box-body">
            <div class="form-group">
              <label for="exampleInputEmail1">Picture</label> 
              <br>
              <img width="100px" id="picture" src="" />
              <input type="file" class="form-control" name="file" placeholder="File" onchange="readEdit(this)">
              <br>
			  <img width="100px" id="blah2" src="" style="display: none" alt="your image" />
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="edit-modal2">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" align="center"><b>Edit Participant</b></h4>
      </div>
      <div class="modal-body">
        <form role="form" method="post" enctype="multipart/form-data" id="formedit2" action="#">
          <input type="hidden" value="{{csrf_token()}}" name="_token" />
          <div class="box-body">
            <div class="form-group">
              <label for="exampleInputEmail1">Participant Name</label>
              <select class="form-control select2" name="participant_name" id="participant_name" style="width: 100%;" data-placeholder="Choose a Participant..." required>
              	@foreach($operator2 as $operator2)
              	<option value="{{ $operator2->name }}">{{ $operator2->employee_id }} - {{ $operator2->name }}</option>
              	@endforeach
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Update</button>
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
    // $(function () {

    //   $('#example2').DataTable({
    //     'paging'      : true,
    //     'lengthChange': false,
    //     'searching'   : false,
    //     'ordering'    : true,
    //     'info'        : true,
    //     'autoWidth'   : false
    //   })
    // })
    function deleteConfirmation(url, name, id, picture_id) {
      jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
      jQuery('#modalDeleteButton').attr("href", url+'/'+id+'/'+picture_id);
    }
    function deleteConfirmation2(url, name, id, participant_id) {
      jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
      jQuery('#modalDeleteButton2').attr("href", url+'/'+id+'/'+participant_id);
    }
    function editpicture(url,urlimage, name, id, picture_id) {
      $("#picture").attr("src",urlimage+'/'+name);
      jQuery('#formedit').attr("action", url+'/'+id+'/'+picture_id);
      // console.log($('#formedit').attr("action"));
    }
    function editparticipant(url, name, id, participant_id) {
    	$.ajax({
                url: "{{ route('admin.participantedit') }}?id=" + participant_id,
                method: 'GET',
                success: function(data) {
                  var json = data;
                  // obj = JSON.parse(json);
                  var participant = data.participant_name;
                  $("#participant_name").val(participant).trigger('change.select2');
                  // console.log(data.participant_name);
                }
            });
      jQuery('#formedit2').attr("action", url+'/'+id+'/'+participant_id);
      // console.log($('#formedit2').attr("action"));
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
    {{-- <script type="text/javascript" src="{{ asset('/bower_components/qrcode/js/jquery.js') }}"></script> --}}
  <script type="text/javascript" src="{{ asset('/bower_components/qrcode/js/qrcodelib.js') }}"></script>
  <script type="text/javascript" src="{{ asset('/bower_components/qrcode/js/webcodecamjquery.js') }}"></script>
  <script type="text/javascript">
      var arg = {
          resultFunction: function(result) {
              //$('.hasilscan').append($('<input name="noijazah" value=' + result.code + ' readonly><input type="submit" value="Cek"/>'));
             // $.post("../cek.php", { noijazah: result.code} );
             
             {{-- url: "{{ url('index/training_report/cek_employee/') }}"; --}}
             // $("#textnama").val(result.code);
             // window.location.href = url+'/'+result.code;
             
             window.location.href = "https://172.17.128.87/miraidev/public/index/training_report/cek_employee/"+result.code;
             // console.log(result.code);
             
              // var redirect = '../materials/cek';
              // $.redirectPost(redirect, {materials_code: result.code});
          }
      };
      var decoder = $("canvas").WebCodeCamJQuery(arg).data().plugin_WebCodeCamJQuery;
      decoder.buildSelectMenu("select");
      decoder.play();
      /*  Without visible select menu
          decoder.buildSelectMenu(document.createElement('select'), 'environment|back').init(arg).play();
      */
      $('select').on('change', function(){
          decoder.stop().play();
      });

      // jquery extend function
      $.extend(
      {
          redirectPost: function(location, args)
          {
              var form = '';
              $.each( args, function( key, value ) {
                  form += '<input type="hidden" name="'+key+'" value="'+value+'">';
              });
              $('<form action="'+location+'" method="POST">'+form+'</form>').appendTo('body').submit();
          }
      });

  </script>
  <script type="text/javascript">
  	$("#textnama").on("input", function(e) {
	  var input = $(this);
	  var val = input.val();

	  // if (input.data("lastval") != val) {
	  //   input.data("lastval", val);

	  //   //your change action goes here 
	  //   console.log(val);
	  // }
	  alert(val);

	});
  </script>
@endsection