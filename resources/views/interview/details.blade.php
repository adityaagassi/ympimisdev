@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('/bower_components/qrcode/css/font-awesome.css') }}">
<link rel="stylesheet" href="{{ asset('/bower_components/qrcode/css/bootstrap.min.css') }}">
{{-- <script src="{{ asset('/bower_components/qrcode/js/jquery.min.js') }}"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
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
							<h3 class="box-title">Interview Participants <span class="text-purple"></span></h3>
							<button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#create-modal">
						        Create
						    </button>
						</div>
				        <div class="box-body" style="overflow-x: scroll;">
				          <table id="example1" class="table table-bordered table-striped table-hover">
				            <thead style="background-color: rgba(126,86,134,.7);">
				              <tr>
				                <th>Participant</th>
				                <th>Filosofi YAMAHA</th>
				                <th>Aturan K3 YAHAMA</th>
				                <th>10 Komitmen Berkendara</th>
				                <th>Kebijakan Mutu</th>
				                <th>Action</th>
				              </tr>
				            </thead>
				            <tbody>
				              @foreach($interview_detail as $interview_detail)
				              <tr>
				                <td>{{ $interview_detail->participants->name }}</td>
				                <td>@if($interview_detail->filosofi_yamaha == 'OK')
				                		<label class="label label-success">{{ $interview_detail->filosofi_yamaha }}</label>
				                	@elseif($interview_detail->filosofi_yamaha == 'OK (Kurang Lancar)')
				                		<label class="label label-warning">{{ $interview_detail->filosofi_yamaha }}</label>
				                	@else
				                		<label class="label label-danger">{{ $interview_detail->filosofi_yamaha }}</label>
				                	@endif
				            	</td>
				            	<td>@if($interview_detail->aturan_k3 == 'OK')
				                		<label class="label label-success">{{ $interview_detail->aturan_k3 }}</label>
				                	@elseif($interview_detail->aturan_k3 == 'OK (Kurang Lancar)')
				                		<label class="label label-warning">{{ $interview_detail->aturan_k3 }}</label>
				                	@else
				                		<label class="label label-danger">{{ $interview_detail->aturan_k3 }}</label>
				                	@endif
				                </td>
				                <td>@if($interview_detail->komitmen_berkendara == 'OK')
				                		<label class="label label-success">{{ $interview_detail->komitmen_berkendara }}</label>
				                	@elseif($interview_detail->komitmen_berkendara == 'OK (Kurang Lancar)')
				                		<label class="label label-warning">{{ $interview_detail->komitmen_berkendara }}</label>
				                	@else
				                		<label class="label label-danger">{{ $interview_detail->komitmen_berkendara }}</label>
				                	@endif
				                </td>
				                <td>@if($interview_detail->kebijakan_mutu == 'OK')
				                		<label class="label label-success">{{ $interview_detail->kebijakan_mutu }}</label>
				                	@elseif($interview_detail->kebijakan_mutu == 'OK (Kurang Lancar)')
				                		<label class="label label-warning">{{ $interview_detail->kebijakan_mutu }}</label>
				                	@else
				                		<label class="label label-danger">{{ $interview_detail->kebijakan_mutu }}</label>
				                	@endif
				                </td>
				                <td>
				                  <center>
				                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit-modal" onclick="editinterview('{{ url("index/interview/edit_participant") }}','{{ $interview_detail->id }}','{{ $interview_id }}');">
						               <i class="fa fa-edit"></i>
						            </button>
				                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("index/interview/destroy_participant") }}','{{ $interview_detail->participants->name }}','{{ $interview_detail->id }}','{{ $interview_id }}');">
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
				                <td>@if($interview_detail->enam_pasal_keselamatan == 'OK')
				                		<label class="label label-success">{{ $interview_detail->enam_pasal_keselamatan }}</label>
				                	@elseif($interview_detail->enam_pasal_keselamatan == 'OK (Kurang Lancar)')
				                		<label class="label label-warning">{{ $interview_detail->enam_pasal_keselamatan }}</label>
				                	@else
				                		<label class="label label-danger">{{ $interview_detail->enam_pasal_keselamatan }}</label>
				                	@endif
				                </td>
				                <td>@if($interview_detail->budaya_kerja == 'OK')
				                		<label class="label label-success">{{ $interview_detail->budaya_kerja }}</label>
				                	@elseif($interview_detail->budaya_kerja == 'OK (Kurang Lancar)')
				                		<label class="label label-warning">{{ $interview_detail->budaya_kerja }}</label>
				                	@else
				                		<label class="label label-danger">{{ $interview_detail->budaya_kerja }}</label>
				                	@endif
				                </td>
				                <td>@if($interview_detail->budaya_5s == 'OK')
				                		<label class="label label-success">{{ $interview_detail->budaya_5s }}</label>
				                	@elseif($interview_detail->budaya_5s == 'OK (Kurang Lancar)')
				                		<label class="label label-warning">{{ $interview_detail->budaya_5s }}</label>
				                	@else
				                		<label class="label label-danger">{{ $interview_detail->budaya_5s }}</label>
				                	@endif
				                </td>
				                <td>@if($interview_detail->komitmen_hotel_konsep == 'OK')
				                		<label class="label label-success">{{ $interview_detail->komitmen_hotel_konsep }}</label>
				                	@elseif($interview_detail->komitmen_hotel_konsep == 'OK (Kurang Lancar)')
				                		<label class="label label-warning">{{ $interview_detail->komitmen_hotel_konsep }}</label>
				                	@else
				                		<label class="label label-danger">{{ $interview_detail->komitmen_hotel_konsep }}</label>
				                	@endif
				                </td>
				                <td>@if($interview_detail->janji_tindakan_dasar == 'OK')
				                		<label class="label label-success">{{ $interview_detail->janji_tindakan_dasar }}</label>
				                	@elseif($interview_detail->janji_tindakan_dasar == 'OK (Kurang Lancar)')
				                		<label class="label label-warning">{{ $interview_detail->janji_tindakan_dasar }}</label>
				                	@else
				                		<label class="label label-danger">{{ $interview_detail->janji_tindakan_dasar }}</label>
				                	@endif
				                </td>
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

				      <div class="box">
				      	<div class="box-header">
							<h3 class="box-title">Interview Pictures <span class="text-purple"></span></h3>
							<form role="form" method="post" action="{{url('index/interview/insertpicture/'.$interview_id)}}" enctype="multipart/form-data">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />

								<div class="form-group">
									<input type="file" class="btn btn-primary" id="" placeholder="Input field" name="file" onchange="readURL(this);" required>
									<br>
									<img width="200px" id="blah" src="" style="display: none" alt="your image" />
								</div>
								<br>
								<button type="submit" class="btn btn-primary ">Upload</button>
							</form>
						</div>
				        <div class="box-body" style="overflow-x: scroll;">
				          <table id="example3" class="table table-bordered table-striped table-hover">
				            <thead style="background-color: rgba(126,86,134,.7);">
				              <tr>
				                <th>Pictures</th>
				                <th>Action</th>
				              </tr>
				            </thead>
				            <tbody>
				              @foreach($interview_picture as $interview_picture)
				              <tr>
				                <td>
				                	@if($interview_picture->extension == 'jpg' || $interview_picture->extension == 'png')
				                	<a target="_blank" href="{{ url('/data_file/interview/'.$interview_picture->picture) }}" class="btn"><img width="100px" src="{{ url('/data_file/interview/'.$interview_picture->picture) }}"></a>
				                	@else
				                	<a target="_blank" href="{{ url('/data_file/interview/'.$interview_picture->picture) }}" class="btn"><img width="100px" src="{{ url('/images/file.png') }}"></a>
				                	@endif
				                </td>
				                <td>
				                  <center>
				                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit-modal-picture" onclick="editpicture('{{ url("index/interview/editpicture") }}','{{ url('/data_file/interview/') }}', '{{ $interview_picture->picture }}','{{ $interview_id }}', '{{ $interview_picture->id }}');">
						               <i class="fa fa-edit"></i>
						            </button>
				                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation2('{{ url("index/interview/destroypicture") }}', '{{ $interview_picture->picture }}','{{ $interview_id }}', '{{ $interview_picture->id }}');">
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
      	<div class="box-body">
        <div>
        	{{-- <form role="form" method="post" action="{{url('index/interview/create_participant/'.$interview_id)}}" enctype="multipart/form-data"> --}}
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          		<center>
          			<a id="scan" class="btn btn-primary" onclick="scanQrCode()">Scan QR Code</a>
          			<video width="200px" id="preview"></video><br>
          			<div class="form-group" id="inputPeserta">
		              <input type="text" style="width:200px" name="peserta" id="createpeserta" class="form-control" placeholder="NIK">
		            </div>
          			<a id="cancel" class="btn btn-primary" onclick="cancelScan()">Cancel</a>
          		</center>
          	</div>        
          	<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
          	</div>
          	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          		<div class="form-group" id="nik_operator">
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
				    <label><input type="radio" name="filosofi_yamaha" id="filosofi_yamaha_create" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="filosofi_yamaha" id="filosofi_yamaha_create" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="filosofi_yamaha" id="filosofi_yamaha_create" value="Not OK">Not OK</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label for="">Aturan K3 YAMAHA</label>
				  <div class="radio">
				    <label><input type="radio" name="aturan_k3" id="aturan_k3_create" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="aturan_k3" id="aturan_k3_create" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="aturan_k3" id="aturan_k3_create" value="Not OK">Not OK</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label for="">10 Komitmen Berkendara</label>
				  <div class="radio">
				    <label><input type="radio" name="komitmen_berkendara" id="komitmen_berkendara_create" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="komitmen_berkendara" id="komitmen_berkendara_create" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="komitmen_berkendara" id="komitmen_berkendara_create" value="Not OK">Not OK</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label for="">Kebijakan Mutu</label>
				  <div class="radio">
				    <label><input type="radio" name="kebijakan_mutu" id="kebijakan_mutu_create" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="kebijakan_mutu" id="kebijakan_mutu_create" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="kebijakan_mutu" id="kebijakan_mutu_create" value="Not OK">Not OK</label>
				  </div>
	            </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            	<div class="form-group">
	              <label for="">6 Pasal Keselamatan Lalu Lintas YAMAHA</label>
				  <div class="radio">
				    <label><input type="radio" name="enam_pasal_keselamatan" id="enam_pasal_keselamatan_create" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="enam_pasal_keselamatan" id="enam_pasal_keselamatan_create" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="enam_pasal_keselamatan" id="enam_pasal_keselamatan_create" value="Not OK">Not OK</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label for="">Budaya Kerja YMPI</label>
				  <div class="radio">
				    <label><input type="radio" name="budaya_kerja" id="budaya_kerja_create" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="budaya_kerja" id="budaya_kerja_create" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="budaya_kerja" id="budaya_kerja_create" value="Not OK">Not OK</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label for="">5S</label>
				  <div class="radio">
				    <label><input type="radio" name="budaya_5s" id="budaya_5s_create" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="budaya_5s" id="budaya_5s_create" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="budaya_5s" id="budaya_5s_create" value="Not OK">Not OK</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label for="">Komitmen Hotel Concept</label>
				  <div class="radio">
				    <label><input type="radio" name="komitmen_hotel_konsep" id="komitmen_hotel_konsep_create" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="komitmen_hotel_konsep" id="komitmen_hotel_konsep_create" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="komitmen_hotel_konsep" id="komitmen_hotel_konsep_create" value="Not OK">Not OK</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label for="">Janji Tindakan Dasar Hotel Concept</label>
				  <div class="radio">
				    <label><input type="radio" name="janji_tindakan_dasar" id="janji_tindakan_dasar_create" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="janji_tindakan_dasar" id="janji_tindakan_dasar_create" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="janji_tindakan_dasar" id="janji_tindakan_dasar_create" value="Not OK">Not OK</label>
				  </div>
	            </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          	<div class="modal-footer">
            <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            <input type="submit" value="Submit" onclick="create({{ $interview_id }})" class="btn btn-primary">
          </div>
          </div>
        {{-- </form> --}}
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="edit-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" align="center"><b>Edit Interview</b></h4>
      </div>
      <div class="modal-body">
        <form role="form" id="formedit2" method="post" action="" enctype="multipart/form-data">
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
          <div class="box-body">
          	<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
          		
          	</div>
          	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          		<div class="form-group">
	              <label for="exampleInputEmail1">Participant Name</label> 
	              <select class="form-control select4" name="nik" id="nik_edit" style="width: 100%;" data-placeholder="Choose a Participant..." required>
					<option value=""></option>
					@foreach($operator2 as $operator2)
						<option value="{{ $operator2->employee_id }}">{{ $operator2->employee_id }} - {{ $operator2->name }}</option>
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
				    <label><input type="radio" name="filosofi_yamaha" id="filosofi_yamaha" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="filosofi_yamaha" id="filosofi_yamaha" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="filosofi_yamaha" id="filosofi_yamaha" value="Not OK">Not OK</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label for="">Aturan K3 YAMAHA</label>
				  <div class="radio">
				    <label><input type="radio" name="aturan_k3" id="aturan_k3" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="aturan_k3" id="aturan_k3" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="aturan_k3" id="aturan_k3" value="Not OK">Not OK</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label for="">10 Komitmen Berkendara</label>
				  <div class="radio">
				    <label><input type="radio" name="komitmen_berkendara" id="komitmen_berkendara" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="komitmen_berkendara" id="komitmen_berkendara" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="komitmen_berkendara" id="komitmen_berkendara" value="Not OK">Not OK</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label for="">Kebijakan Mutu</label>
				  <div class="radio">
				    <label><input type="radio" name="kebijakan_mutu" id="kebijakan_mutu" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="kebijakan_mutu" id="kebijakan_mutu" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="kebijakan_mutu" id="kebijakan_mutu" value="Not OK">Not OK</label>
				  </div>
	            </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            	<div class="form-group">
	              <label for="">6 Pasal Keselamatan Lalu Lintas YAMAHA</label>
				  <div class="radio">
				    <label><input type="radio" name="enam_pasal_keselamatan" id="enam_pasal_keselamatan" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="enam_pasal_keselamatan" id="enam_pasal_keselamatan" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="enam_pasal_keselamatan" id="enam_pasal_keselamatan" value="Not OK">Not OK</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label for="">Budaya Kerja YMPI</label>
				  <div class="radio">
				    <label><input type="radio" name="budaya_kerja" id="budaya_kerja" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="budaya_kerja" id="budaya_kerja" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="budaya_kerja" id="budaya_kerja" value="Not OK">Not OK</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label for="">5S</label>
				  <div class="radio">
				    <label><input type="radio" name="budaya_5s" id="budaya_5s" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="budaya_5s" id="budaya_5s" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="budaya_5s" id="budaya_5s" value="Not OK">Not OK</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label for="">Komitmen Hotel Concept</label>
				  <div class="radio">
				    <label><input type="radio" name="komitmen_hotel_konsep" id="komitmen_hotel_konsep" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="komitmen_hotel_konsep" id="komitmen_hotel_konsep" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="komitmen_hotel_konsep" id="komitmen_hotel_konsep" value="Not OK">Not OK</label>
				  </div>
	            </div>
	            <div class="form-group">
	              <label for="">Janji Tindakan Dasar Hotel Concept</label>
				  <div class="radio">
				    <label><input type="radio" name="janji_tindakan_dasar" id="janji_tindakan_dasar" value="OK">OK</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="janji_tindakan_dasar" id="janji_tindakan_dasar" value="OK (Kurang Lancar)">OK (Kurang Lancar)</label>
				  </div>
				  <div class="radio">
				    <label><input type="radio" name="janji_tindakan_dasar" id="janji_tindakan_dasar" value="Not OK">Not OK</label>
				  </div>
	            </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="edit-modal-picture">
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
  <script src="{{ url("js/jquery.gritter.min.js") }}"></script>
  <script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
  <script src="{{ url("js/buttons.flash.min.js")}}"></script>
  <script src="{{ url("js/jszip.min.js")}}"></script>
  <script src="{{ url("js/vfs_fonts.js")}}"></script>
  <script src="{{ url("js/buttons.html5.min.js")}}"></script>
  <script src="{{ url("js/buttons.print.min.js")}}"></script>
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
    $(function () {
      $('.select3').select2({
      	dropdownParent: $('#create-modal')
      })
    });
    $(function () {
      $('.select4').select2({
      	dropdownParent: $('#edit-modal')
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
    jQuery(document).ready(function() {
      $('#example3 tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
      } );
      var table = $('#example3').DataTable({
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

      $('#example3 tfoot tr').appendTo('#example3 thead');

    });
    function deleteConfirmation(url, name, detail_id, interview_id) {
      jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
      jQuery('#modalDeleteButton').attr("href", url+'/'+interview_id+'/'+detail_id);
    }

    function deleteConfirmation2(url, name, interview_id,picture_id) {
      jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
      jQuery('#modalDeleteButton').attr("href", url+'/'+interview_id+'/'+picture_id);
    }

    function editinterview(url, detail_id, interview_id) {
    	$.ajax({
                url: "{{ route('interview.getdetail') }}?id=" + detail_id,
                method: 'GET',
                success: function(data) {
                  var json = data;
                  // obj = JSON.parse(json);
                  var data = data.data;
                  $("#nik_edit").val(data.nik).trigger('change.select2');
                  $('input[id="filosofi_yamaha"][value="'+data.filosofi_yamaha+'"]').prop('checked',true);
                  $('input[id="aturan_k3"][value="'+data.aturan_k3+'"]').prop('checked',true);
                  $('input[id="komitmen_berkendara"][value="'+data.komitmen_berkendara+'"]').prop('checked',true);
                  $('input[id="kebijakan_mutu"][value="'+data.kebijakan_mutu+'"]').prop('checked',true);
                  $('input[id="enam_pasal_keselamatan"][value="'+data.enam_pasal_keselamatan+'"]').prop('checked',true);
                  $('input[id="budaya_kerja"][value="'+data.budaya_kerja+'"]').prop('checked',true);
                  $('input[id="budaya_5s"][value="'+data.budaya_5s+'"]').prop('checked',true);
                  $('input[id="komitmen_hotel_konsep"][value="'+data.komitmen_hotel_konsep+'"]').prop('checked',true);
                  $('input[id="janji_tindakan_dasar"][value="'+data.janji_tindakan_dasar+'"]').prop('checked',true);
                }
            });
      jQuery('#formedit2').attr("action", url+'/'+interview_id+'/'+detail_id);
      console.log($('#formedit2').attr("action"));
    }
  </script>
  <script type="text/javascript">
  	function create(interview_id){
		var pesertascan = $('#createpeserta').val();
		var nik = $('#nik').val();
		var filosofi_yamaha = $('input[id="filosofi_yamaha_create"]:checked').val();
		var aturan_k3 = $('input[id="aturan_k3_create"]:checked').val();
		var filosofi_yamaha = $('input[id="komitmen_berkendara_create"]:checked').val();
		var komitmen_berkendara = $('input[id="kebijakan_mutu_create"]:checked').val();
		var kebijakan_mutu = $('input[id="kebijakan_mutu_create"]:checked').val();
		var enam_pasal_keselamatan = $('input[id="enam_pasal_keselamatan_create"]:checked').val();
		var budaya_kerja = $('input[id="budaya_kerja_create"]:checked').val();
		var budaya_5s = $('input[id="budaya_5s_create"]:checked').val();
		var komitmen_hotel_konsep = $('input[id="komitmen_hotel_konsep_create"]:checked').val();
		var janji_tindakan_dasar = $('input[id="janji_tindakan_dasar_create"]:checked').val();

		if (pesertascan == '' && nik == '') {
			alert('Isi Semua Data');
		}else{
			var data = {
				pesertascan:pesertascan,
				nik:nik,
				filosofi_yamaha:filosofi_yamaha,
				aturan_k3:aturan_k3,
				komitmen_berkendara:komitmen_berkendara,
				kebijakan_mutu:kebijakan_mutu,
				enam_pasal_keselamatan:enam_pasal_keselamatan,
				budaya_kerja:budaya_kerja,
				budaya_5s:budaya_5s,
				komitmen_hotel_konsep:komitmen_hotel_konsep,
				janji_tindakan_dasar:janji_tindakan_dasar,
				interview_id:interview_id
			}
			// console.table(data);
			
			$.post('{{ url("index/interview/create_participant") }}', data, function(result, status, xhr){
				if(result.status){
					$("#create-modal").modal('hide');
					openSuccessGritter('Success','New Participant has been created');
					window.location.reload();
				} else {
					audio_error.play();
					openErrorGritter('Error','Create Participant Failed');
				}
			});
		}
	}

  	$(function () {
      $('#preview').hide();
      $('#inputPeserta').hide();
      $('#cancel').hide();
    });
    function cancelScan(){
    	$('#nik_operator').show();
    	$('#preview').hide();
	      $('#inputPeserta').hide();
	      $('#cancel').hide();
	      $('#scan').show();
    };
    function scanQrCode(){
    	$('#preview').show();
      	$('#inputPeserta').show();
      	$('#scan').hide();
      	$('#cancel').show();
      	$('#nik_operator').hide();
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
        $("#createpeserta").val(res)
        

      });

      Instascan.Camera.getCameras().then(function (cameras) {

        if (cameras.length > 0) {

          scanner.start(cameras[1]);

        } else {

          console.error('No cameras found.');

        }

      }).catch(function (e) {

        console.error(e);

      });
    }

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
	function editpicture(url,urlimage, name, id, picture_id) {
      $("#picture").attr("src",urlimage+'/'+name);
      jQuery('#formedit').attr("action", url+'/'+id+'/'+picture_id);
      // console.log($('#formedit').attr("action"));
    }
    </script>
@endsection