@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<style type="text/css">
	.isi{
    background-color: #f5f5f5;
    color: black;
    padding: 10px;
  }

  #signArea{
    width: 504px;
    margin: 15px auto;
  }
  .sign-container {
    width: 90%;
    margin: auto;
  }
  .sign-preview {
    width: 150px;
    height: 50px;
    border: solid 1px #CFCFCF;
    margin: 10px 5px;
  }
  .tag-ingo {
    font-family: cursive;
    font-size: 12px;
    text-align: left;
    font-style: oblique;
  }
  .center-text {
    text-align: center;
  }

  @media print {
  .table {-webkit-print-color-adjust: exact;}
  }

    table tr td,
    table tr th{
      font-size: 12pt;
      border: 1px solid black !important;
      border-collapse: collapse;
    }
    .centera{
      text-align: center;
      vertical-align: middle !important;
    }
    .square {
      height: 5px;
      width: 5px;
      border: 1px solid black;
      background-color: transparent;
    }
    table {
      page-break-inside: avoid;
    }
  #loading, #error { display: none; }
</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    Verifikasi CAR Oleh General Manager
    <!-- <small>Verification</small> -->
  </h1>
  <ol class="breadcrumb">
   {{--  <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
   <li><a href="#">Examples</a></li>
   <li class="active">Blank page</li> --}}
 </ol>
</section>
@endsection
@section('content')
<section class="content">
	@if (session('status'))
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
    {{ session('status') }}
  </div>   
  @endif
  @if ($errors->has('password'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
    {{ $errors->first() }}
  </div>   
  @endif
  @if (session('error'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Not Verified!</h4>
    {{ session('error') }}
  </div>   
  @endif
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary">
    {{-- <div class="box-header with-border">
      <h3 class="box-title">Detail User</h3>
    </div>   --}}

    @if(Auth::user()->username == $car->car_cpar->gm || Auth::user()->role_code == "S" || Auth::user()->role_code == "MIS" || Auth::user()->role_code == "QA" || Auth::user()->role_code == "QA-SPL" || Auth::user()->username == $car->car_cpar->staff || Auth::user()->username == $car->car_cpar->leader) 

      <div class="box-body">
      	<table class="table" style="border: 1px solid black;">
		@foreach($cars as $car)
		<thead>
			<tr>
				<td colspan="2" rowspan="3" class="centera">
					<!-- <img width="80px" src="{{ asset('images/logo_yamaha2.png') }}" alt=""> -->
					<img width="200px" src="{{ asset('waves.jpg') }}" alt="">
				</td>
				<td colspan="6" rowspan="3" class="centera" style="font-size: 25px;font-weight: bold">CORRECTIVE ACTION REPORT</td>
				<td class="centera" width="10%">Approved By</td>
				<td class="centera" width="10%">Approved By</td>
				<td class="centera" width="10%">Approved By</td>
			</tr>
			<tr>
				<td class="centera">
					@if($car->approved_gm == "Checked")
						<img width="100" src="{{url($carss[0]['ttd_car'])}}" width="150px">
						{{$car->gmname}}
					@else
						&nbsp;
					@endif
				</td>
				<td class="centera">
					@if($car->approved_dgm == "Checked")
						{{$car->dgmname}}
					@else
						&nbsp;
					@endif
				</td>
				<td class="centera">
					@if($car->checked_manager == "Checked")
						{{$car->managername}}
					@else
						&nbsp;
					@endif
				</td>
			</tr>
			<tr>
				<td class="centera">GM</td>
				<td class="centera">DGM</td>
				<td class="centera">Manager</td>
			</tr>
		</thead>
		<tbody>
			 <?php 
	          $tinjauan = $car->tinjauan; 
	          
	          if($tinjauan != NULL){
	            $split = explode(",", $tinjauan);
	            $hitungsplit = count($split);
	          }else{
	            $split = 0;
	          }
	        ?>
			<tr>
				<td colspan="2" width="20%">
					Kategori Komplain : {{ $car->kategori }}
				</td>
				<td colspan="2" width="20%">
					Departemen : {{ $car->department_name }}
				</td>
				<td colspan="2" width="20%">
					Section : {{ $car->section }}
				</td>
				<td colspan="2" width="20%">
					Date : <?php echo date('d F Y', strtotime($car->tgl_car)) ?>
				</td>
				<td colspan="3" width="20%">
					Location : {{ $car->lokasi }}			
				</td>
			</tr>
			<tr>
				<td colspan="2" width="20%">Tinjauan 4M : </td>
				<td colspan="2" width="20%" class="" style="font-size: 16px">Man <input type="checkbox" class="centera" style="font-size: 14px;margin: 0" 
				<?php
					foreach ($split as $key) {
		                if ($key == 1) {
		                  echo 'checked';
		                }
		            } ?>>
				</td>
				<td colspan="2" width="20%" class="" style="font-size: 16px">Material <input type="checkbox" class="centera" style="font-size: 14px;margin: 0" 
				<?php
					foreach ($split as $key) {
		                if ($key == 2) {
		                  echo 'checked';
		                }
		            } ?>>
				</td>
				<td colspan="2" width="20%" class="" style="font-size: 16px">Machine <input type="checkbox" class="centera" style="font-size: 14px;margin: 0" 
				<?php
					foreach ($split as $key) {
		                if ($key == 3) {
		                  echo 'checked';
		                }
		            } ?>>
				</td>
				<td colspan="3" width="20%" class="" style="font-size: 16px">Method <input type="checkbox" class="centera" style="font-size: 14px;margin: 0" 
				<?php
					foreach ($split as $key) {
		                if ($key == 4) {
		                  echo 'checked';
		                }
		            } ?>>
		        </td>	
			</tr>
			<tr style="page-break-inside:avoid">
				<td colspan="11"><b style="font-size: 20px">Description</b> : <?= $car->deskripsi ?></td>
				<!-- <td rowspan="2" colspan="3" class="centera" style="font-weight: bold;font-size: 12px">Tinjauan 4M </td> -->
			</tr>
			<tr style="page-break-inside:avoid">
				<td colspan="11"><b style="font-size: 20px">A. Immediately Action</b> : <?= $car->tindakan ?></td>
			</tr>
			<tr style="page-break-inside:avoid">
				<td colspan="11"><b style="font-size: 20px">B. Possibility Cause</b> : <?= $car->penyebab ?></td>
			</tr>
			<tr style="page-break-inside:avoid">
				<td colspan="11"><b style="font-size: 20px">C. Corrective Action</b> : <?= $car->perbaikan ?></td>
			</tr>
			 <?php if ($car->file != null){ ?>
			<tr style="page-break-inside:avoid">
				<td colspan="11"><b style="font-size: 20px">Terlampir</b> : 
					<?php $data = json_decode($car->file);
                    for ($i = 0; $i < count($data); $i++) { ?>
                            <a href="{{ url('/files/car/'.$data[$i]) }}" class="fa fa-paperclip"> {{$data[$i]}}</a> &nbsp;
                  <?php } ?>         
              </td>
			</tr>
			<?php } ?>
			<tr>
				<td colspan="9"></td>
				<td class="centera">Prepared</td>
				<td class="centera">Checked</td>
			</tr>
			<tr>
				<td rowspan="2" colspan="9"></td>
				<td rowspan="2" class="centera">
					@if($car->pic != null)
						{{$car->picname}}
					@else
						&nbsp;
					@endif
				</td>
				<td rowspan="2" class="centera">
					@if($car->checked_chief == "Checked")
						{{$car->chiefname}}
					@elseif($car->checked_foreman == "Checked")
						{{$car->foremanname}}
					@elseif($car->checked_coordinator == "Checked")
						{{$car->coordinatorname}}
					@else
						&nbsp;
					@endif
				</td>
			</tr>
			<tr></tr>
			<tr>
				<td colspan="9"></td>			
				@if($car->kategori == "Internal")
					<td class="centera">Leader</td>
					<td class="centera">Foreman</td>
				@else
					<td class="centera">Staff</td>
					<td class="centera">Chief</td>				
				@endif
			</tr>
			<tr style="page-break-inside:avoid">
				<td colspan="11">
					<b style="font-size: 20px">D. QA Verification</b> 
				</td>
			</tr>

			<?php for ($i=0; $i < count($verifikasi); $i++) { ?>
			
			<tr>
				<td colspan="2">
					<p style="font-size: 18px">Tanggal : <?= $verifikasi[$i]->tanggal ?></p>
					<p style="font-size: 18px">Status : <?= $verifikasi[$i]->status ?></p>
				</td>
				<td colspan="9">
					<p style="font-size: 18px">Verifikasi <?= $i+1 ?> : <?= $verifikasi[$i]->keterangan ?></p>
				</td>
			</tr>

			<?php } ?>

			<tr>
				<td colspan="8"></td>
				<td>Prepared By</td>
				<td>Checked By</td>
				<td>Checked By</td>
			</tr>
			<tr>
				<td colspan="8" rowspan="2"></td>
				<td rowspan="2">
					@if($car->posisi_cpar == "QA" || $car->posisi_cpar == "QA2" || $car->posisi_cpar == "QAmanager" || $car->posisi_cpar == "QAFIX")
						@if($car->staff != null)
							{{$car->staffqaname}}
						@elseif($car->leader != null)
							{{$car->leaderqaname}}
						@else
							&nbsp;
						@endif
					@endif
				</td>
				<td rowspan="2">
					@if($car->posisi_cpar == "QA2" || $car->posisi_cpar == "QAmanager" || $car->posisi_cpar == "QAFIX")
						@if($car->staff != null)
							{{$car->chiefqaname}}
						@elseif($car->leader != null)
							{{$car->foremanqaname}}
						@else
							&nbsp;
						@endif
					@endif
				</td>
				<td rowspan="2">
					@if($car->posisi_cpar == "QAmanager" || $car->posisi_cpar == "QAFIX")
						{{$car->managerqaname}}
					@else
						&nbsp;
					@endif
				</td>
			</tr>
			<tr></tr>
			<tr>
				<td colspan="8"></td>
				<td>Staff</td>
				<td>Chief</td>
				<td>Manager</td>
			</tr>
		</tbody>
		@endforeach
		</table>
		<div class="col-md-12" style="text-align: right;">
			<span style="font-size: 20px">No FM : YMPI/QA/FM/899</span>
		</div>

		@if($car->posisi == "gm")

		<?php foreach ($carss as $carss): ?>

		<?php if ($carss->ttd_car == null) { ?>

        <table class="table table-hover">
            <thead>
              <tr>
                <th colspan="6" style="background-color: green; color: white; font-size: 20px;border: none"><b>VERIFIKASI CAR {{ $carss->cpar_no }} </b></th>
              </tr>
            </thead>
        </table>
      
       <div class="box box-primary" style="padding: 0;border: 0">
          <div class="all-content-wrapper">
            <!-- #END# Top Bar -->
              <div class="form-group custom-input-space has-feedback">
                <div class="page-heading">
                  <h3 class="post-title">Tanda Tangan</h3>
                </div>
                <div class="page-body clearfix">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="panel panel-default">
                        <input type="hidden" value="{{csrf_token()}}" name="_token" />
                        <div class="panel-heading">Digital Signature : </div>
                        <div class="panel-body center-text"  style="padding: 0">
                          <div id="signArea">
                            <h2 class="tag-ingo">Put signature here,</h2>
                            <div class="sig sigWrapper" style="height:204px;">
                              <div class="typed"></div>
                              <canvas class="sign-pad" id="sign-pad" width="500" height="190"></canvas>
                            </div>
                          </div>
                          
                          <input type="hidden" name="cpar_no" id="cpar_no" value="{{$carss->cpar_no}}">
                          <input type="hidden" name="id_verif" id="id_verif" value="{{$carss->id}}">
                          <button class="btn btn-success" id="btnSaveSign" >Verify CAR</button>
                          <a href="{{ url('index/qc_car/verifikasigm', $carss->id) }}" class="btn btn-danger">Clear</a>
                          <!-- <button onclick="clearSign()" class="btn btn-danger">Clear</button> -->
                          <!-- <button class="clearButton" href="#clear">Clear</button> -->
                      		<br><br>
                        </div>

                      </div>

                         <center><a data-toggle="modal" data-target="#notapproved{{$carss->id}}" class="btn btn-danger col-sm-12" href="" style="width: 100%; font-weight: bold; font-size: 20px;margin-top: 10px">Reject CAR</a></center>
                    </div>
                  </div>
                </div>
              </div>

          </div>
        </div>

        <div class="modal modal-danger fade" id="notapproved{{$carss->id}}" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	      <div class="modal-dialog">
	        <div class="modal-content">
	          <form role="form" method="post" action="{{url('index/qc_car/uncheckedGM/'.$carss->id)}}">
	            <div class="modal-header">
	              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	              <h4 class="modal-title" id="myModalLabel">Not Approved</h4>
	            </div>
	            <div class="modal-body">
	              <div class="box-body">
	                  <input type="hidden" value="{{csrf_token()}}" name="_token" />
	                  <h4>Berikan alasan tidak menyetujui CAR ini</h4>
	                  <textarea class="form-control" required="" name="alasan" id="detail" style="height: 250px;"></textarea>
	                  *CAR Akan Dikirim kembali lagi ke Foreman / Staff masing - masing departemen
	              </div>    
	            </div>
	            <div class="modal-footer">
	              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
	              <button type="submit" class="btn btn-outline">Not Approved</a>
	            </div>
	          </form>
	        </div>
	      </div>
	    </div>


      <?php } ?>
      <?php endforeach; ?>
      @endif
	</div>
  @endif
  </div>
  @endsection
<style>

table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
  font-family:"Arial";
  padding: 5px;
}

@media print {
	body {-webkit-print-color-adjust: exact;}
}

</style>


@section('scripts')

<script src="{{ url("js/jquery.gritter.min.js") }}"></script>

<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<script src="{{ url("bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>
<script src="{{ url("bower_components/select2/dist/js/select2.full.min.js")}}"></script>
<script src="{{ url("bower_components/jquery-ui/jquery-ui.min.js")}}"></script>

<link rel="stylesheet" href="{{ url("css/jquery.signaturepad.css")}}">
<script src="{{ url("js/numeric-1.2.6.min.js")}}"></script>
<script src="{{ url("js/bezier.js")}}"></script>
<script src="{{ url("js/jquery.signaturepad.js")}}"></script>

<script src="{{ url("js/html2canvas.js")}}"></script>
  <!-- <script src="./js/json2.min.js"></script> -->
<script>
	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$("#navbar-collapse").text('');


	});

	$.ajaxSetup({
	    headers: {
	      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	    }
	  });

    function myFunction() {
	  window.print();
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
</script>
 <script>
  $(document).ready(function(e){
    $(document).ready(function() {
      $('#signArea').signaturePad({drawOnly:true, drawBezierCurves:true, lineTop:190});
    });
    
    $("#btnSaveSign").click(function(e){
      html2canvas([document.getElementById('sign-pad')], {
        onrendered: function (canvas) {
          var canvas_img_data = canvas.toDataURL('image/png');
          var img_data = canvas_img_data.replace(/^data:image\/(png|jpg);base64,/, "");
          var cpar_no = $("#cpar_no").val();
          var id = $("#id_verif").val();
        //ajax call to save image inside folder
        $.ajax({
          url: '{{ url('index/qc_car/save_sign') }}',
          data: { 
            id:id,
            img_data:img_data,
            cpar_no:cpar_no
          },
          type: 'post',
          dataType: 'json',
          success: function (response) {
            window.location.reload();
          }
        });
      }
      });
    });

  });

  function clearSign() {
    $('#signArea').siganturePad(clearCanvas());
  }

  CKEDITOR.replace('detail' ,{
      filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });
</script>

  @stop