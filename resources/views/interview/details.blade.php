@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
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
label {
		cursor: pointer;
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $activity_name }} - {{ $leader }}
		<a class="btn btn-warning pull-right" href="{{ url('index/interview/index/'.$activity_id) }}">Kembali</a>
		<button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#create-modal" style="margin-right: 5px">
	        Tambah Peserta
	    </button>
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
					<h3 class="box-title">Detail Interview <span class="text-purple"></span></h3>
				</div>
				<div class="box-body">
				  <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				  	<table class="table table-bordered">
				  		<tr>
							<td><b>Dept</b></td>
							<td>{{strtoupper($interview->department)}}</td>
						</tr>
						<tr>
							<td><b>Section</b></td>
							<td>{{strtoupper($interview->section)}}</td>
						</tr>
						<tr>
							<td><b>Subsection</b></td>
							<td>{{$interview->subsection}}</td>
						</tr>
						<tr>
							<td><b>Periode</b></td>
							<td>{{$interview->periode}}</td>
						</tr>
				  	</table>
				  </div>
				  <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				  	<table class="table table-bordered">
				  		<tr>
							<td><b>Tanggal</b></td>
							<td>{{$interview->date}}</td>
						</tr>
				  		<tr>
							<td><b>Leader</b></td>
							<td>{{$interview->leader}}</td>
						</tr>
						<tr>
							<td><b>Foreman</b></td>
							<td>{{strtoupper($interview->foreman)}}</td>
						</tr>
				  	</table>
				  </div>
				  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			        <div class="row">
			        	<h4 class="box-title">Peserta Interview Pointing Call <span class="text-purple"></span></h4>
			        	<table id="example1" class="table table-bordered table-striped table-hover">
			            <thead style="background-color: rgba(126,86,134,.7);">
			              <tr>
			                <th>Peserta</th>
			                <th>Filosofi YAMAHA</th>
			                <th>Aturan K3 YAHAMA</th>
			                <th>10 Komitmen Berkendara</th>
			                <th>Slogan Mutu</th>
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
			                	@elseif($interview_detail->filosofi_yamaha == 'Not OK')
			                		<label class="label label-danger">{{ $interview_detail->filosofi_yamaha }}</label>
			                	@else
			                		<?php $detail = explode("_",$interview_detail->filosofi_yamaha);
			                		// echo "Nilai = ".$detail[1]."%<br>";
			                		// echo "Point = ".$detail[0];
			                		 ?>
			                		 @if($detail[1] == 0)
				                		<label class="label label-danger" onclick="detailNilai('{{$detail[1]}}','0','filosofi_yamaha')">Not OK</label>
				                	@elseif($detail[1] > 0 && $detail[1] < 100)
				                		<label class="label label-warning" onclick="detailNilai('{{$detail[1]}}','{{$detail[0]}}','filosofi_yamaha')">OK Kurang Lancar</label>
				                	@elseif($detail[1] == 100)
				                		<label class="label label-success" onclick="detailNilai('{{$detail[1]}}','{{$detail[0]}}','filosofi_yamaha')">OK</label>
				                	@endif
			                	@endif
			            	</td>
			            	<td>@if($interview_detail->aturan_k3 == 'OK')
			                		<label class="label label-success">{{ $interview_detail->aturan_k3 }}</label>
			                	@elseif($interview_detail->aturan_k3 == 'OK (Kurang Lancar)')
			                		<label class="label label-warning">{{ $interview_detail->aturan_k3 }}</label>
			                	@elseif($interview_detail->aturan_k3 == 'Not OK')
			                		<label class="label label-danger">{{ $interview_detail->aturan_k3 }}</label>
			                	@else
			                		<?php $detail = explode("_",$interview_detail->aturan_k3);
			                		// echo "Nilai = ".$detail[1]."%<br>";
			                		// echo "Point = ".$detail[0];
			                		 ?>
			                		 @if($detail[1] == 0)
				                		<label class="label label-danger" onclick="detailNilai('{{$detail[1]}}','0','aturan_k3')">Not OK</label>
				                	@elseif($detail[1] > 0 && $detail[1] < 100)
				                		<label class="label label-warning" onclick="detailNilai('{{$detail[1]}}','{{$detail[0]}}','aturan_k3')">OK Kurang Lancar</label>
				                	@elseif($detail[1] == 100)
				                		<label class="label label-success" onclick="detailNilai('{{$detail[1]}}','{{$detail[0]}}','aturan_k3')">OK</label>
				                	@endif
			                	@endif
			                </td>
			                <td>@if($interview_detail->komitmen_berkendara == 'OK')
			                		<label class="label label-success">{{ $interview_detail->komitmen_berkendara }}</label>
			                	@elseif($interview_detail->komitmen_berkendara == 'OK (Kurang Lancar)')
			                		<label class="label label-warning">{{ $interview_detail->komitmen_berkendara }}</label>
			                	@elseif($interview_detail->komitmen_berkendara == 'Not OK')
			                		<label class="label label-danger">{{ $interview_detail->komitmen_berkendara }}</label>
			                	@else
			                		<?php $detail = explode("_",$interview_detail->komitmen_berkendara);
			                		// echo "Nilai = ".$detail[1]."%<br>";
			                		// echo "Point = ".$detail[0];
			                		 ?>
			                		 @if($detail[1] == 0)
				                		<label class="label label-danger" onclick="detailNilai('{{$detail[1]}}','0','komitmen_berkendara')">Not OK</label>
				                	@elseif($detail[1] > 0 && $detail[1] < 100)
				                		<label class="label label-warning" onclick="detailNilai('{{$detail[1]}}','{{$detail[0]}}','komitmen_berkendara')">OK Kurang Lancar</label>
				                	@elseif($detail[1] == 100)
				                		<label class="label label-success" onclick="detailNilai('{{$detail[1]}}','{{$detail[0]}}','komitmen_berkendara')">OK</label>
				                	@endif
			                	@endif
			                </td>
			                <td>@if($interview_detail->kebijakan_mutu == 'OK')
			                		<label class="label label-success">{{ $interview_detail->kebijakan_mutu }}</label>
			                	@elseif($interview_detail->kebijakan_mutu == 'OK (Kurang Lancar)')
			                		<label class="label label-warning">{{ $interview_detail->kebijakan_mutu }}</label>
			                	@elseif($interview_detail->kebijakan_mutu == 'Not OK')
			                		<label class="label label-danger">{{ $interview_detail->kebijakan_mutu }}</label>
			                	@else
			                		<?php $detail = explode("_",$interview_detail->kebijakan_mutu);
			                		// echo "Nilai = ".$detail[1]."%<br>";
			                		// echo "Point = ".$detail[0];
			                		 ?>
			                		 @if($detail[1] == 0)
				                		<label class="label label-danger" onclick="detailNilai('{{$detail[1]}}','0','kebijakan_mutu')">Not OK</label>
				                	@elseif($detail[1] > 0 && $detail[1] < 100)
				                		<label class="label label-warning" onclick="detailNilai('{{$detail[1]}}','{{$detail[0]}}','kebijakan_mutu')">OK Kurang Lancar</label>
				                	@elseif($detail[1] == 100)
				                		<label class="label label-success" onclick="detailNilai('{{$detail[1]}}','{{$detail[0]}}','kebijakan_mutu')">OK</label>
				                	@endif
			                	@endif
			                </td>
			                <td>
			                  <center>
			                    <!-- <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit-modal" onclick="editinterview('{{ url("index/interview/edit_participant") }}','{{ $interview_detail->id }}','{{ $interview_id }}');">
					               <i class="fa fa-edit"></i>
					            </button> -->
			                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('{{ url("index/interview/destroy_participant") }}','{{ $interview_detail->participants->name }}','{{ $interview_detail->id }}','{{ $interview_id }}');">
			                      <i class="fa fa-trash"></i>
			                    </a>
			                  </center>
			                </td>
			              </tr>
			              @endforeach
			            </tbody>
			          </table>

			          <table id="example2" class="table table-bordered table-striped table-hover">
			            <thead style="background-color: rgba(126,86,134,.7);">
			              <tr>
			                <th>Peserta</th>
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
			                <td><?php $detail = explode("_",$interview_detail2->enam_pasal_keselamatan);
			                		// echo "Nilai = ".$detail[1]."%<br>";
			                		// echo "Point = ".$detail[0];
			                		 ?>
			                	@if($interview_detail2->enam_pasal_keselamatan == 'OK')
			                		<label class="label label-success">{{ $interview_detail2->enam_pasal_keselamatan }}</label>
			                	@elseif($interview_detail2->enam_pasal_keselamatan == 'OK (Kurang Lancar)')
			                		<label class="label label-warning">{{ $interview_detail2->enam_pasal_keselamatan }}</label>
			                	@elseif($interview_detail2->enam_pasal_keselamatan == 'Not OK')
			                		<label class="label label-danger">{{ $interview_detail2->enam_pasal_keselamatan }}</label>
			                	@else
				                	@if($detail[1] == 0)
				                		<label class="label label-danger" onclick="detailNilai('{{$detail[1]}}','0','enam_pasal_keselamatan')">Not OK</label>
				                	@elseif($detail[1] > 0 && $detail[1] < 100)
				                		<label class="label label-warning" onclick="detailNilai('{{$detail[1]}}','{{$detail[0]}}','enam_pasal_keselamatan')">OK Kurang Lancar</label>
				                	@elseif($detail[1] == 100)
				                		<label class="label label-success" onclick="detailNilai('{{$detail[1]}}','{{$detail[0]}}','enam_pasal_keselamatan')">OK</label>
				                	@endif
			                	@endif
			                </td>
			                <td>@if($interview_detail2->budaya_kerja == 'OK')
			                		<label class="label label-success">{{ $interview_detail2->budaya_kerja }}</label>
			                	@elseif($interview_detail2->budaya_kerja == 'OK (Kurang Lancar)')
			                		<label class="label label-warning">{{ $interview_detail2->budaya_kerja }}</label>
			                	@elseif($interview_detail2->budaya_kerja == 'Not OK')
			                		<label class="label label-danger">{{ $interview_detail2->budaya_kerja }}</label>
			                	@else
			                		<?php $detail = explode("_",$interview_detail2->budaya_kerja);
			                		// echo "Nilai = ".$detail[1]."%<br>";
			                		// echo "Point = ".$detail[0];
			                		 ?>
			                		@if($detail[1] == 0)
				                		<label class="label label-danger" onclick="detailNilai('{{$detail[1]}}','0','budaya_kerja')">Not OK</label>
				                	@elseif($detail[1] > 0 && $detail[1] < 100)
				                		<label class="label label-warning" onclick="detailNilai('{{$detail[1]}}','{{$detail[0]}}','budaya_kerja')">OK Kurang Lancar</label>
				                	@elseif($detail[1] == 100)
				                		<label class="label label-success" onclick="detailNilai('{{$detail[1]}}','{{$detail[0]}}','budaya_kerja')">OK</label>
				                	@endif
			                	@endif
			                </td>
			                <td>@if($interview_detail2->budaya_5s == 'OK')
			                		<label class="label label-success">{{ $interview_detail2->budaya_5s }}</label>
			                	@elseif($interview_detail2->budaya_5s == 'OK (Kurang Lancar)')
			                		<label class="label label-warning">{{ $interview_detail2->budaya_5s }}</label>
			                	@elseif($interview_detail2->budaya_5s == 'Not OK')
			                		<label class="label label-danger">{{ $interview_detail2->budaya_5s }}</label>
			                	@else
			                		<?php $detail = explode("_",$interview_detail2->budaya_5s);
			                		// echo "Nilai = ".$detail[1]."%<br>";
			                		// echo "Point = ".$detail[0];
			                		 ?>
			                		  @if($detail[1] == 0)
				                		<label class="label label-danger" onclick="detailNilai('{{$detail[1]}}','0','budaya_5s')">Not OK</label>
				                	@elseif($detail[1] > 0 && $detail[1] < 100)
				                		<label class="label label-warning" onclick="detailNilai('{{$detail[1]}}','{{$detail[0]}}','budaya_5s')">OK Kurang Lancar</label>
				                	@elseif($detail[1] == 100)
				                		<label class="label label-success" onclick="detailNilai('{{$detail[1]}}','{{$detail[0]}}','budaya_5s')">OK</label>
				                	@endif
			                	@endif
			                </td>
			                <td>@if($interview_detail2->komitmen_hotel_konsep == 'OK')
			                		<label class="label label-success">{{ $interview_detail2->komitmen_hotel_konsep }}</label>
			                	@elseif($interview_detail2->komitmen_hotel_konsep == 'OK (Kurang Lancar)')
			                		<label class="label label-warning">{{ $interview_detail2->komitmen_hotel_konsep }}</label>
			                	@elseif($interview_detail2->komitmen_hotel_konsep == 'Not OK')
			                		<label class="label label-danger">{{ $interview_detail2->komitmen_hotel_konsep }}</label>
			                	@else
			                		<?php $detail = explode("_",$interview_detail2->komitmen_hotel_konsep);
			                		// echo "Nilai = ".$detail[1]."%<br>";
			                		// echo "Point = ".$detail[0];
			                		 ?>
			                		 @if($detail[1] == 0)
				                		<label class="label label-danger" onclick="detailNilai('{{$detail[1]}}','0','komitmen_hotel_konsep')">Not OK</label>
				                	@elseif($detail[1] > 0 && $detail[1] < 100)
				                		<label class="label label-warning" onclick="detailNilai('{{$detail[1]}}','{{$detail[0]}}','komitmen_hotel_konsep')">OK Kurang Lancar</label>
				                	@elseif($detail[1] == 100)
				                		<label class="label label-success" onclick="detailNilai('{{$detail[1]}}','{{$detail[0]}}','komitmen_hotel_konsep')">OK</label>
				                	@endif
			                	@endif
			                </td>
			                <td>@if($interview_detail2->janji_tindakan_dasar == 'OK')
			                		<label class="label label-success">{{ $interview_detail2->janji_tindakan_dasar }}</label>
			                	@elseif($interview_detail2->janji_tindakan_dasar == 'OK (Kurang Lancar)')
			                		<label class="label label-warning">{{ $interview_detail2->janji_tindakan_dasar }}</label>
			                	@elseif($interview_detail2->janji_tindakan_dasar == 'Not OK')
			                		<label class="label label-danger">{{ $interview_detail2->janji_tindakan_dasar }}</label>
			                	@else
			                		<?php $detail = explode("_",$interview_detail2->janji_tindakan_dasar);
			                		// echo "Nilai = ".$detail[1]."%<br>";
			                		// echo "Point = ".$detail[0];
			                		 ?>
			                		 @if($detail[1] == 0)
				                		<label class="label label-danger" onclick="detailNilai('{{$detail[1]}}','0','janji_tindakan_dasar')">Not OK</label>
				                	@elseif($detail[1] > 0 && $detail[1] < 100)
				                		<label class="label label-warning" onclick="detailNilai('{{$detail[1]}}','{{$detail[0]}}','janji_tindakan_dasar')">OK Kurang Lancar</label>
				                	@elseif($detail[1] == 100)
				                		<label class="label label-success" onclick="detailNilai('{{$detail[1]}}','{{$detail[0]}}','janji_tindakan_dasar')">OK</label>
				                	@endif
			                	@endif
			                </td>
			                <td>
			                </td>
			              </tr>
			              @endforeach
			            </tbody>
			          </table>
			        </div>
				  </div>
				</div>
			</div>

			<div class="box box-solid">
		      	<div class="box-header">
					<h3 class="box-title">Foto Interview<span class="text-purple"></span></h3>
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
		        <div class="box-body">
		          <table id="example3" class="table table-bordered table-striped table-hover">
		            <thead style="background-color: rgba(126,86,134,.7);">
		              <tr>
		                <th>Pictures / Files</th>
		                <th>Action</th>
		              </tr>
		            </thead>
		            <tbody>
		              @foreach($interview_picture as $interview_picture)
		              <tr>
		                <td>
		                	@if($interview_picture->extension == 'jpg' || $interview_picture->extension == 'png' || $interview_picture->extension == 'jpeg' || $interview_picture->extension == 'JPG')
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
		          </table>
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
        <h4 class="modal-title" align="center"><b>Tambah Peserta</b></h4>
      </div>
      <div class="modal-body">
      	<div class="box-body">
        <div>
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
          	<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
          	</div>
          	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          		<div class="form-group" id="nik_operator">
	              <center style="background-color: #a142f5;color:white;padding: 1px;font-size: 20px"><label>Nama Karyawan</label></center>
	              <select class="form-control select3" name="nik" id="nik" style="width: 100%;" data-placeholder="Pilih Karyawan..." required>
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
	              <center style="background-color: #a142f5;color:white;padding: 1px;font-size: 20px"><label>Filosofi Yamaha</label></center>
	              
	              @foreach($pointing_call as $point)
	              <?php if ($point->point_title == 'diamond'): ?>
	              	<label class="checkbox-inline" style="padding-bottom: 4px">
		              <input type="checkbox" class="filosofi_yamahaCheckbox" name="filosofi_yamaha_create" value="{{$point->point_no}}" id="filosofi_yamaha_create"><?php echo $point->point_description ?>
		            </label><br>
	              <?php endif ?>
	              @endforeach
	            </div>
	        </div>
	        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	            <div class="form-group">
	              <center style="background-color: #a142f5;color:white;padding: 1px;font-size: 20px"><label>Slogan Mutu YMPI</label></center>
				  
	              @foreach($pointing_call as $point)
	              <?php if ($point->point_title == 'slogan_mutu'): ?>
	              	<label class="checkbox-inline" style="padding-bottom: 4px">
		              <input type="checkbox" class="kebijakan_mutuCheckbox" name="kebijakan_mutu_create" value="{{$point->point_no}}" id="kebijakan_mutu_create"><?php echo $point->point_description ?>
		            </label><br>
	              <?php endif ?>
	              @endforeach
	              <label class="checkbox-inline" style="padding-bottom: 4px">
		              <input type="checkbox" class="kebijakan_mutuCheckbox" name="kebijakan_mutu_create" value="2" id="kebijakan_mutu_create">Hafal Sebagian
		            </label><br>
	            </div>
	            <div class="form-group">
	              <center style="background-color: #a142f5;color:white;padding: 1px;font-size: 20px"><label>5S</label></center>
				  
	              @foreach($pointing_call as $point)
	              <?php if ($point->point_title == '5s'): ?>
	              	<label class="checkbox-inline" style="padding-bottom: 4px">
		              <input type="checkbox" class="budaya_5sCheckbox" name="budaya_5s_create" value="{{$point->point_no}}" id="budaya_5s_create"><?php echo $point->point_no ?>. <?php echo $point->point_description ?>
		            </label><br>
	              <?php endif ?>
	              @endforeach
	            </div>
	            <div class="form-group">
	              <center style="background-color: #a142f5;color:white;padding: 1px;font-size: 20px"><label>Aturan K3 YAMAHA</label></center>
				  
	              @foreach($pointing_call as $point)
	              <?php if ($point->point_title == 'k3'): ?>
	              	<label class="checkbox-inline" style="padding-bottom: 4px">
		              <input type="checkbox" class="aturan_k3Checkbox" name="aturan_k3_create" value="{{$point->point_no}}" id="aturan_k3_create"><?php echo $point->point_no ?>. <?php echo $point->point_description ?>
		            </label><br>
	              <?php endif ?>
	              @endforeach
	            </div>
	        </div>
	        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	            
	            <div class="form-group">
	              <center style="background-color: #a142f5;color:white;padding: 1px;font-size: 20px"><label>10 Komitmen Berkendara</label></center>
				  
	              @foreach($pointing_call as $point)
	              <?php if ($point->point_title == '10_komitmen'): ?>
	              	<label class="checkbox-inline" style="padding-bottom: 4px">
		              <input type="checkbox" class="komitmen_berkendaraCheckbox" name="komitmen_berkendara_create" value="{{$point->point_no}}" id="komitmen_berkendara_create"><?php echo $point->point_no ?>. <?php echo $point->point_description ?>
		            </label><br>
	              <?php endif ?>
	              @endforeach
	            </div>
	            
            <!-- </div> -->
            <!-- <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"> -->
            	<div class="form-group">
	              <center style="background-color: #a142f5;color:white;padding: 1px;font-size: 20px"><label>6 Pasal Keselamatan Lalu Lintas YAMAHA</label></center>
				  
	              @foreach($pointing_call as $point)
	              <?php if ($point->point_title == '6_pasal'): ?>
	              	<label class="checkbox-inline" style="padding-bottom: 4px">
		              <input type="checkbox" class="enam_pasal_keselamatanCheckbox" name="enam_pasal_keselamatan_create" value="{{$point->point_no}}" id="enam_pasal_keselamatan_create"><?php echo $point->point_no ?>. <?php echo $point->point_description ?>
		            </label><br>
	              <?php endif ?>
	              @endforeach
	            </div>
	            <div class="form-group">
	              <center style="background-color: #a142f5;color:white;padding: 1px;font-size: 20px"><label>Budaya Kerja YMPI</label></center>
				  
	              @foreach($pointing_call as $point)
	              <?php if ($point->point_title == 'budaya'): ?>
	              	<label class="checkbox-inline" style="padding-bottom: 4px">
		              <input type="checkbox" class="budaya_kerjaCheckbox" name="budaya_kerja_create" value="{{$point->point_no}}" id="budaya_kerja_create"><?php echo $point->point_no ?>. <?php echo $point->point_description ?>
		            </label><br>
	              <?php endif ?>
	              @endforeach
	            </div>
	            
	            <div class="form-group">
	              <center style="background-color: #a142f5;color:white;padding: 1px;font-size: 20px"><label>Komitmen Hotel Concept</label></center>
				  
	              @foreach($pointing_call as $point)
	              <?php if ($point->point_title == 'komitmen'): ?>
	              	<label class="checkbox-inline" style="padding-bottom: 4px">
		              <input type="checkbox" class="komitmen_hotel_konsepCheckbox" name="komitmen_hotel_konsep_create" value="{{$point->point_no}}" id="komitmen_hotel_konsep_create"><?php echo $point->point_no ?>. <?php echo $point->point_description ?>
		            </label><br>
	              <?php endif ?>
	              @endforeach
	            </div>
	            <div class="form-group">
	              <center style="background-color: #a142f5;color:white;padding: 1px;font-size: 20px"><label>Janji Tindakan Dasar Hotel Concept</label></center>
				  
	              @foreach($pointing_call as $point)
	              <?php if ($point->point_title == 'janji'): ?>
	              	<label class="checkbox-inline" style="padding-bottom: 4px">
		              <input type="checkbox" class="janji_tindakan_dasarCheckbox" name="janji_tindakan_dasar_create" value="{{$point->point_no}}" id="janji_tindakan_dasar_create"><?php echo $point->point_no ?>. <?php echo $point->point_description ?>
		            </label><br>
	              <?php endif ?>
	              @endforeach
	            </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          	<div class="modal-footer">
            <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
            <input type="submit" value="Tambah" onclick="create({{ $interview_id }})" class="btn btn-primary">
          </div>
          </div>
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
        <h4 class="modal-title" align="center"><b>Edit Peserta</b></h4>
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
	              <label for="">Slogan Mutu</label>
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

<div class="modal fade" id="detail-nilai-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modal-title-nilai" style="font-weight: bold;" align="center"></h4>
      </div>
      <div class="modal-body">
          <div class="box-body">
            <div class="col-xs-12">
            	<div class="row">
            		<table class="table table-hover table-bordered table-striped" id="table-detail-nilai">
            			<thead  style="background-color: rgba(126,86,134,.7);color: white">
            				<th style="width: 1%">No.</th>
            				<th style="width: 3%">Point</th>
            				<th style="width: 2%">Check</th>
            			</thead>
            			<tbody id="table-detail-nilai-body">
            				
            			</tbody>
            		</table>
            	</div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger pull-right" data-dismiss="modal">Close</button>
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

    });

    jQuery(document).ready(function() {
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

    });
    jQuery(document).ready(function() {
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
  		var index_filosofi_yamaha = 0;
  		var index_aturan_k3 = 0;
  		var index_komitmen_berkendara = 0;
  		var index_kebijakan_mutu = 0;
  		var index_enam_pasal_keselamatan = 0;
  		var index_budaya_kerja = 0;
  		var index_budaya_5s = 0;
  		var index_komitmen_hotel_konsep = 0;
  		var index_janji_tindakan_dasar = 0;

  		var checked_filosofi_yamaha = 0;
  		var checked_aturan_k3 = 0;
  		var checked_komitmen_berkendara = 0;
  		var checked_kebijakan_mutu = 0;
  		var checked_enam_pasal_keselamatan = 0;
  		var checked_budaya_kerja = 0;
  		var checked_budaya_5s = 0;
  		var checked_komitmen_hotel_konsep = 0;
  		var checked_janji_tindakan_dasar = 0;

		// var pesertascan = $('#createpeserta').val();
		var nik = $('#nik').val();
		var type_filosofi_yamaha = [];
		var type_aturan_k3 = [];
		var type_komitmen_berkendara = [];
		var type_kebijakan_mutu = [];
		var type_enam_pasal_keselamatan = [];
		var type_budaya_kerja = [];
		var type_budaya_5s = [];
		var type_komitmen_hotel_konsep = [];
		var type_janji_tindakan_dasar = [];
		$("input[name='filosofi_yamaha_create']:checked").each(function (i) {
            type_filosofi_yamaha[i] = $(this).val();
            checked_filosofi_yamaha++;
        });
        $("input[name='aturan_k3_create']:checked").each(function (i) {
            type_aturan_k3[i] = $(this).val();
            checked_aturan_k3++;
        });
        $("input[name='komitmen_berkendara_create']:checked").each(function (i) {
            type_komitmen_berkendara[i] = $(this).val();
            checked_komitmen_berkendara++;
        });
        $("input[name='kebijakan_mutu_create']:checked").each(function (i) {
            type_kebijakan_mutu[i] = $(this).val();
            checked_kebijakan_mutu++;
        });
        $("input[name='enam_pasal_keselamatan_create']:checked").each(function (i) {
            type_enam_pasal_keselamatan[i] = $(this).val();
            checked_enam_pasal_keselamatan++;
        });
        $("input[name='budaya_kerja_create']:checked").each(function (i) {
            type_budaya_kerja[i] = $(this).val();
            checked_budaya_kerja++;
        });
        $("input[name='budaya_5s_create']:checked").each(function (i) {
            type_budaya_5s[i] = $(this).val();
            checked_budaya_5s++;
        });
        $("input[name='komitmen_hotel_konsep_create']:checked").each(function (i) {
            type_komitmen_hotel_konsep[i] = $(this).val();
            checked_komitmen_hotel_konsep++;
        });
        $("input[name='janji_tindakan_dasar_create']:checked").each(function (i) {
            type_janji_tindakan_dasar[i] = $(this).val();
            checked_janji_tindakan_dasar++;
        });

        $("input[name='filosofi_yamaha_create']").each(function (i) {
            index_filosofi_yamaha++;
        });
        $("input[name='aturan_k3_create']").each(function (i) {
            index_aturan_k3++;
        });
        $("input[name='komitmen_berkendara_create']").each(function (i) {
            index_komitmen_berkendara++;
        });
        $("input[name='kebijakan_mutu_create']").each(function (i) {
            index_kebijakan_mutu++;
        });
        $("input[name='enam_pasal_keselamatan_create']").each(function (i) {
            index_enam_pasal_keselamatan++;
        });
        $("input[name='budaya_kerja_create']").each(function (i) {
            index_budaya_kerja++;
        });
        $("input[name='budaya_5s_create']").each(function (i) {
            index_budaya_5s++;
        });
        $("input[name='komitmen_hotel_konsep_create']").each(function (i) {
            index_komitmen_hotel_konsep++;
        });
        $("input[name='janji_tindakan_dasar_create']").each(function (i) {
            index_janji_tindakan_dasar++;
        });

		if (nik == '') {
			alert('Isi Semua Data');
		}else{
			var data = {
				// pesertascan:pesertascan,
				nik:nik,
				filosofi_yamaha:type_filosofi_yamaha.join(),
				aturan_k3:type_aturan_k3.join(),
				komitmen_berkendara:type_komitmen_berkendara.join(),
				kebijakan_mutu:type_kebijakan_mutu.join(),
				enam_pasal_keselamatan:type_enam_pasal_keselamatan.join(),
				budaya_kerja:type_budaya_kerja.join(),
				budaya_5s:type_budaya_5s.join(),
				komitmen_hotel_konsep:type_komitmen_hotel_konsep.join(),
				janji_tindakan_dasar:type_janji_tindakan_dasar.join(),
				index_filosofi_yamaha:index_filosofi_yamaha,
				index_aturan_k3:index_aturan_k3,
				index_komitmen_berkendara:index_komitmen_berkendara,
				index_kebijakan_mutu:index_kebijakan_mutu,
				index_enam_pasal_keselamatan:index_enam_pasal_keselamatan,
				index_budaya_kerja:index_budaya_kerja,
				index_budaya_5s:index_budaya_5s,
				index_komitmen_hotel_konsep:index_komitmen_hotel_konsep,
				index_janji_tindakan_dasar:index_janji_tindakan_dasar,
				checked_filosofi_yamaha:checked_filosofi_yamaha,
				checked_aturan_k3:checked_aturan_k3,
				checked_komitmen_berkendara:checked_komitmen_berkendara,
				checked_kebijakan_mutu:checked_kebijakan_mutu,
				checked_enam_pasal_keselamatan:checked_enam_pasal_keselamatan,
				checked_budaya_kerja:checked_budaya_kerja,
				checked_budaya_5s:checked_budaya_5s,
				checked_komitmen_hotel_konsep:checked_komitmen_hotel_konsep,
				checked_janji_tindakan_dasar:checked_janji_tindakan_dasar,
				interview_id:interview_id
			}
			// console.table(data);
			
			$.post('{{ url("index/interview/create_participant") }}', data, function(result, status, xhr){
				if(result.status){
					$("#create-modal").modal('hide');
					openSuccessGritter('Success','Berhasil Tambah Peserta');
					window.location.reload();
				} else {
					audio_error.play();
					openErrorGritter('Error','Gagal Tambah Peserta');
				}
			});
		}
	}

  	$(function () {
      $('#preview').hide();
      $('#inputPeserta').hide();
      $('#cancel').hide();
    });
    

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

    function detailNilai(nilai,point,type) {
    	if (parseInt(nilai) !== 0) {
    		var data = {
    			point:point,
    			type:type
    		}

    		$.get('{{ url("index/interview/detail_nilai") }}', data, function(result, status, xhr){
				if(result.status){
					$('#detail-nilai-modal').modal('show');
					$('#modal-title-nilai').html('Detail Nilai '+result.judul);
					$('#table-detail-nilai-body').html('');
					var table = "";
					$.each(result.pointtitle, function(key, value){
						$.each(result.pointtitlecheck, function(key2, value2){
							if (value2.point_description == value.point_description) {
								checks = 'OK';
								color = '#b1ff75';
							}else{
								checks = 'Not OK';
								color = '#ffa89e';
							}
						});
						table += '<tr style="background-color:'+color+'">';
						table += '<td>'+value.point_no+'</td>';
						table += '<td>'+value.point_description+'</td>';
						table += '<td>'+checks+'</td>';
						table += '</tr>';
					});
					$('#table-detail-nilai-body').append(table);
				} else {
					audio_error.play();
					openErrorGritter('Error','Gagal');
				}
			});
    	}
    }
    </script>
@endsection