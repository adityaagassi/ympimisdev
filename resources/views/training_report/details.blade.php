@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('/bower_components/qrcode/css/bootstrap.min.css') }}">
<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<script src="{{ url("bower_components/jquery-ui/jquery-ui.min.js")}}"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
<script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
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
.cbx {
  margin: auto;
  -webkit-user-select: none;
  user-select: none;
  cursor: pointer;
}
.cbx span {
  display: inline-block;
  vertical-align: middle;
  transform: translate3d(0, 0, 0);
}
.cbx span:first-child {
  position: relative;
  width: 18px;
  height: 18px;
  border-radius: 3px;
  transform: scale(1);
  vertical-align: middle;
  border: 1px solid #9098A9;
  transition: all 0.2s ease;
}
.cbx span:first-child svg {
  position: absolute;
  top: 3px;
  left: 2px;
  fill: none;
  stroke: #FFFFFF;
  stroke-width: 2;
  stroke-linecap: round;
  stroke-linejoin: round;
  stroke-dasharray: 16px;
  stroke-dashoffset: 16px;
  transition: all 0.3s ease;
  transition-delay: 0.1s;
  transform: translate3d(0, 0, 0);
}
.cbx span:first-child:before {
  content: "";
  width: 100%;
  height: 100%;
  background: #506EEC;
  display: block;
  transform: scale(0);
  opacity: 1;
  border-radius: 50%;
}
.cbx span:last-child {
  padding-left: 8px;
}
.cbx:hover span:first-child {
  border-color: #506EEC;
}

.inp-cbx:checked + .cbx span:first-child {
  background: #506EEC;
  border-color: #506EEC;
  animation: wave 0.4s ease;
}
.inp-cbx:checked + .cbx span:first-child svg {
  stroke-dashoffset: 0;
}
.inp-cbx:checked + .cbx span:first-child:before {
  transform: scale(3.5);
  opacity: 0;
  transition: all 0.6s ease;
}

@keyframes wave {
  50% {
    transform: scale(0.9);
  }
}

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $activity_name }} <span class="text-purple">{{ $departments }}</span>
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
				                <td>
				                	@if($training_picture->extension == 'jpg' || $training_picture->extension == 'png')
				                	<a target="_blank" href="{{ url('/data_file/training/'.$training_picture->picture) }}" class="btn"><img width="100px" src="{{ url('/data_file/training/'.$training_picture->picture) }}"></a>
				                	@else
				                	<a target="_blank" href="{{ url('/data_file/training/'.$training_picture->picture) }}" class="btn"><img width="100px" src="{{ url('/images/file.png') }}"></a>
				                	@endif
				                </td>
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
							<!-- <a class="btn btn-primary pull-right" href="{{ secure_url('index/training_report/scan_employee/'.$id) }}">Scan Barcode</a> -->
							<button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#participant-modal" onclick="importparticipant('{{ url("index/training_report/importparticipant") }}','{{ $id }}');">
				               Import Participant
				            </button>
							<div class="panel-body text-center" >
				              <video width="200px" id="preview"></video>
				            </div>
							<form role="form" method="post" action="{{url('index/training_report/insertparticipant/'.$id)}}" enctype="multipart/form-data">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />

								<div class="form-group" align="right">
									<input type="hidden" value="{{ $id }}" id="id_training">
									<select class="form-control select2" name="participant_id" style="width: 100%;" data-placeholder="Choose a Participant..." required>
						                <option value=""></option>
						                @foreach($operator as $operator)
						                  <option value="{{ $operator->employee_id }}">{{ $operator->employee_id }} - {{ $operator->name }}</option>
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
				              	@if($jml_null > 0)
				              	<th>Attendance Checklist</th>
				              	@endif
				                <th>Name</th>
				                <th>Attendance</th>
				                <th>Action</th>
				              </tr>
				            </thead>
				            <tbody>
				              @foreach($training_participant as $training_participant)
				              <tr>
				              	@if($jml_null > 0)
								<td id="approval2">
									<input type="hidden" value="{{csrf_token()}}" name="_token" />
									@if($training_participant->participant_absence == Null)
									    <input type="checkbox" name="ips[]" onchange="getValue(this.value)" value="{{ $training_participant->participant_id }}">
									    <!-- <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#ttd-modal" onclick="getValue('{{ $training_participant->id }}','{{ $training_participant->participant_name->name }}')">
							               Kehadiran
							            </button> -->
									@endif
								</td>
								@endif
				                <td>
				                	{{ $training_participant->participant_name->name }}
				                </td>
				                <td>
				                	@if($training_participant->participant_absence == Null)
				                		Belum Hadir
				                	@else
				                		{{ $training_participant->participant_absence }}
				                	@endif
				                </td>
				                <td>
				                  <center>
				                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit-modal2" onclick="editparticipant('{{ url("index/training_report/editparticipant") }}','{{ $training_participant->participant_id }}','{{ $id }}', '{{ $training_participant->id }}');">
						               <i class="fa fa-edit"></i>
						            </button>
				                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal2" onclick="deleteConfirmation2('{{ url("index/training_report/destroyparticipant") }}', '{{ $training_participant->participant_id }}','{{ $id }}', '{{ $training_participant->id }}');">
				                      <i class="fa fa-trash"></i>
				                    </a>
				                  </center>
				                </td>
				              </tr>
				              @endforeach
				            </tbody>
				            <tfoot>
				              <tr>
				              	@if($jml_null > 0)
				                <th></th>
				                @endif
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
        <div class="modal-body" id="body-delete">
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
        <div class="modal-body" id="body-delete">
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
              		<option value="{{ $operator2->employee_id }}">{{ $operator2->employee_id }} - {{ $operator2->name }}</option>
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
<div class="modal fade" id="participant-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" align="center"><b>Import Participant</b></h4><br>
        <button class="btn btn-default"><input type="checkbox" onclick="checkAll(this.checked)">Check All</button>
      </div>
      <div class="modal-body">
        <form role="form" method="post" enctype="multipart/form-data" id="formimport" action="#">
          <input type="hidden" value="{{csrf_token()}}" name="_token" />
          <div class="box-body">
            <table class="table table-hover table-striped" id="tableImport">
				<thead>
					<tr>
						<th style="width: 1%;">#</th>
						<th style="width: 2%;">Employee ID</th>
						<th style="width: 5%;">Employee Name</th>
						<th style="width: 5%;">Sub Section</th>
					</tr>					
				</thead>
				<tbody id="tableImportList">
					@foreach($operator3 as $operator3)
						<tr>
							<td><input type="checkbox" name="empid[]" value="{{ $operator3->employee_id }}"></td>
							<td>{{ $operator3->employee_id }}</td>
							<td>{{ $operator3->name }}</td>
							<td>{{ $operator3->sub_section }}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Import</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="ttd-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" align="center"><b>TTD Peserta</b></h4>
      </div>
      <div class="modal-body">
        <!-- <form role="form" method="post" enctype="multipart/form-data" id="formimport" action="#"> -->
          <input type="hidden" value="{{csrf_token()}}" name="_token" />
          <div class="box-body">
            <div class="panel panel-default">
	            <input type="hidden" value="{{csrf_token()}}" name="_token" />
	            <div class="panel-heading" id="panel-heading">Digital Signature : </div>
	            <div class="panel-body center-text"  style="padding: 0">
	              <div id="signArea">
	                <!-- <h2 class="tag-ingo">Put signature here,</h2> -->
	                <div class="sig sigWrapper" style="height:204px;">
	                  <div class="typed"></div>
	                  <canvas class="sign-pad" id="sign-pad" width="500" height="190"></canvas>
	                </div>
	              </div>
	              
	              <input type="hidden" name="id_peserta" id="id_peserta">
	              <input type="hidden" name="id_training" id="id_training" value="{{$id}}">
	              <button class="btn btn-success pull-right" onclick="saveSign()">HADIR</button>
	              <a href="{{ url('index/training_report/details/'.$id.'/view') }}" class="btn btn-danger">Clear</a>
	              <!-- <button onclick="clearSign()" class="btn btn-danger">Clear</button> -->
	              <!-- <button class="clearButton" href="#clear">Clear</button> -->
	            </div>
	        </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            <!-- <button type="submit" class="btn btn-primary">Import</button> -->
          </div>
        <!-- </form> -->
      </div>
    </div>
  </div>
</div>
@endsection


@section('scripts')
<link rel="stylesheet" href="{{ url("css/jquery.signaturepad.css")}}">
<script src="{{ url("js/numeric-1.2.6.min.js")}}"></script>
<script src="{{ url("js/bezier.js")}}"></script>
<script src="{{ url("js/jquery.signaturepad.js")}}"></script>
<script>
	function getValue(value,name){
    	var url = '{{ url("index/training_report/cek_employee") }}';
    	var conf = confirm('Are you sure you want to attend?');
    	if(conf){
    		window.location.href = url+'/'+value+'/{{ $id }}';
    	}else{

    	}
    	// $("#id_peserta").val(value);
    	// $("#panel-heading").html('Digital Signature of '+name);
	}

	function checkAll(isChecked){
		if(isChecked){
			$(':checkbox').attr('checked',true);
		}
		else{
			$(':checkbox').attr('checked',false);
		}
	}
</script>
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
		$('body').toggleClass("sidebar-collapse");

		$('#signArea').signaturePad({drawOnly:true, drawBezierCurves:true, lineTop:190});		
	});

	function saveSign() {
		var img_data;
		html2canvas([document.getElementById('sign-pad')], {
	        onrendered: function (canvas) {
	          var canvas_img_data = canvas.toDataURL('image/png');
	          img_data = canvas_img_data.replace(/^data:image\/(png|jpg);base64,/, "");
	          var data = {
				img_data:img_data,				
			  }
			  var id_peserta = $("#id_peserta").val();
		        var url = '{{ url("index/training_report/cek_employee2") }}';

		        $.ajax({
		          url: url+'/'+id_peserta+'/'+'{{ $id }}',
		          data: { 
		            img_data:img_data,
		          },
		          type: 'post',
		          dataType: 'json',
		          success: function (response) {

		          }
		        });
		        $("#ttd-modal").modal('hide');
				openSuccessGritter('Success','Participant has been attend');
	            window.location.reload();
		  //       $.post(url+'/'+id_peserta+'/'+'{{ $id }}', data, function(result, status, xhr){
				// 	if(result.status){
				// 		// $('#example1').DataTable().ajax.reload();
				// 		// $('#example2').DataTable().ajax.reload();
				// 	} else {
				// 		audio_error.play();
				// 		openErrorGritter('Error','Attendance Failed');
				// 	}
				// });
				// $("#ttd-modal").modal('hide');
				// openSuccessGritter('Success','Participant has been attend');
				// window.location.reload();
	      	}
	      });

		// $("#ttd-modal").modal('hide');
		// openSuccessGritter('Success','Participant has been attend');
		// window.location.reload();
	}

	
</script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
  <script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
  <script src="{{ url("js/buttons.flash.min.js")}}"></script>
  <script src="{{ url("js/jszip.min.js")}}"></script>
  <script src="{{ url("js/vfs_fonts.js")}}"></script>
  <script src="{{ url("js/buttons.html5.min.js")}}"></script>
  <script src="{{ url("js/buttons.print.min.js")}}"></script>

<script src="{{ url("js/html2canvas.js")}}"></script>
  <script>
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

	$('#tableImport').DataTable({
		'dom': 'Bfrtip',
		'responsive':true,
		// 'lengthMenu': [
		// [ 10, 25, 50, -1 ],
		// [ '10 rows', '25 rows', '50 rows', 'Show all' ]
		// ],
		// 'buttons': {
		// 	buttons:[
		// 	{
		// 		extend: 'pageLength',
		// 		className: 'btn btn-default',
		// 	},
			
		// 	]
		// },
		'paging': false,
		// 'lengthChange': true,
		// 'pageLength': 10,
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

    function deleteConfirmation(url, name, id, picture_id) {
      jQuery('#body-delete').html("Are you sure want to delete '" + name + "'?");
      jQuery('#modalDeleteButton').attr("href", url+'/'+id+'/'+picture_id);
    }
    function deleteConfirmation2(url, name, id, participant_id) {
      jQuery('#body-delete').html("Are you sure want to delete '" + name + "'?");
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
                  var participant = data.participant_id;
                  $("#participant_name").val(participant).trigger('change.select2');
                  console.log(participant);
                }
            });
      jQuery('#formedit2').attr("action", url+'/'+id+'/'+participant_id);
      // console.log($('#formedit2').attr("action"));
    }

    function importparticipant(url, id) {
    	// $.ajax({
     //            url: "{{ route('admin.participantedit') }}?id=" + participant_id,
     //            method: 'GET',
     //            success: function(data) {
     //              var json = data;
     //              // obj = JSON.parse(json);
     //              var participant = data.participant_id;
     //              $("#participant_name").val(participant).trigger('change.select2');
     //              console.log(participant);
     //            }
     //        });
      jQuery('#formimport').attr("action", url+'/'+id);
      console.log($('#formimport').attr("action"));
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
  <script type="text/javascript">
  	let opts = {
		  // Whether to scan continuously for QR codes. If false, use scanner.scan() to manually scan.
		  // If true, the scanner emits the "scan" event when a QR code is scanned. Default true.
		  continuous: true,
		  
		  // The HTML element to use for the camera's video preview. Must be a <video> element.
		  // When the camera is active, this element will have the "active" CSS class, otherwise,
		  // it will have the "inactive" class. By default, an invisible element will be created to
		  // host the video.
		  video: document.getElementById('preview'),
		  
		  // Whether to horizontally mirror the video preview. This is helpful when trying to
		  // scan a QR code with a user-facing camera. Default true.
		  mirror: false,
		  
		  // Whether to include the scanned image data as part of the scan result. See the "scan" event
		  // for image format details. Default false.
		  captureImage: false,
		  
		  // Only applies to continuous mode. Whether to actively scan when the tab is not active.
		  // When false, this reduces CPU usage when the tab is not active. Default true.
		  backgroundScan: true,
		  
		  // Only applies to continuous mode. The period, in milliseconds, before the same QR code
		  // will be recognized in succession. Default 5000 (5 seconds).
		  refractoryPeriod: 5000,
		  
		  // Only applies to continuous mode. The period, in rendered frames, between scans. A lower scan period
		  // increases CPU usage but makes scan response faster. Default 1 (i.e. analyze every frame).
		  scanPeriod: 1
		};
      let scanner = new Instascan.Scanner(opts);

      scanner.addListener('scan', function (content) {

        
        var res = content.substring(0, 9);
        // alert(res);
        window.location.href = "https://172.17.128.87/miraidev/public/index/training_report/cek_employee/"+res+"/{{ $id }}";

      });

      Instascan.Camera.getCameras().then(function (cameras) {

        if (cameras.length > 0) {

          scanner.start(cameras[0]);

        } else {

          console.error('No cameras found.');

        }

      }).catch(function (e) {

        console.error(e);

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