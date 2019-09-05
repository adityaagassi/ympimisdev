@extends('layouts.visitor')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">

@endsection


@section('header')
<section class="content-header">
	<h1><br><br>
		<center><span style="color: white; font-weight: bold; font-size: 28px; text-align: center;">YMPI Visitor Leave</span></center>
		{{-- <small>By Shipment Schedule <span class="text-purple">??????</span></small> --}}
	</h1>
	<ol class="breadcrumb" id="last_update">
	</ol>
</section>
@endsection

@section('content')
@if (session('status'))
<div class="alert alert-success alert-dismissible">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
	{{ session('status') }}
</div>   
@endif
@if (session('error'))
<div class="alert alert-danger alert-dismissible">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<h4><i class="icon fa fa-ban"></i> Error!</h4>
	{{ session('error') }}
</div>   
@endif
<div class="row">
	<div class=" col-xs-12">
		<div class="box" style="min-height: 500px">
			<div class="box-header with-border">
				{{-- <h3 class="box-title">Registration Form</h3> --}}

				<div class="form-group">					
					<div  class="col-xs-offset-2  col-xs-8">
						<div class="input-group " >

							<div class="input-group-btn" >
								<button type="button" class="btn btn-primary btn-lg" ><i class="glyphicon glyphicon-barcode"></i>&nbsp;</button>
							</div>
							<!-- /btn-group -->
							<input type="text" value="" id="telp" class="form-control btn-lg" placeholder="Scan or Input Tag" style="width: 99%; height: 100%" onkeydown="postvisit()">
						</div>
					</div>
				</div>				

				{{-- <center><b>Visitor List</b></center> --}}<br><br>

				<div class="form-group" >
					<div class="col-sm-1">
						<b style="font-size: 20px">	ID Number </b>
					</div>
					<div class="col-sm-2" >
						<b style="font-size: 20px">	Name </b>
					</div>
					<div class="col-sm-1" >
						<b style="font-size: 20px">	In - Time </b>
					</div>
					<div class="col-sm-2" >
						<b style="font-size: 20px">	Company </b>
					</div>
					<div class="col-sm-2" >
						<b style="font-size: 20px">	Employee </b>
					</div>
					<div class="col-sm-2" >
						<b style="font-size: 20px">	Department </b>
					</div>
					<div class="col-sm-2 " >
						<b style="font-size: 20px">	Reason </b>

					</div>
				</div>


				<div id="apen" >	


				</div>	
			</div>


		</div>
	</div>
</div>
@endsection


@section('scripts')

<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script >
	var no = 1;
	jQuery(document).ready(function() {   
		$('#lop2').val(1);        
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$('.select2').select2({
			dropdownAutoWidth : true,
			width: '100%',
		});
	});


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

	function postvisit(tag){
		if (event.keyCode == 13 || event.keyCode == 9) {

			var id =	$('#telp').val();
			var data = {
				id : id
			}
			$.get('{{ url("visitor_getvisit") }}', data, function(result, status, xhr){
				console.log(status);
				console.log(result);
				console.log(xhr);
				var no =1;
				if(xhr.status == 200){
					if(result.status){
						$('#apen').html();

						$.each(result.ops, function(key, value) {

							if(value.remark =="Unconfirmed"){												 
								$('#apen').append('<div class="col-xs-12" > <div class="col-sm-1" > <br><b style="font-size: 18px" id="visitor_id0" name="visitor_id0" >'+ value.id_number +'</b> </div> <div class="col-sm-2" > <br><b style="font-size: 18px" id="visitor_id0" name="visitor_id0" >'+ value.full_name +'</b> </div> <div class="col-sm-1" > <br><b style="font-size: 18px" id="visitor_id0" name="visitor_id0" >'+ value.in_time +'</b> </div> <div class="col-sm-2" > <br><b style="font-size: 18px" id="visitor_id0" name="visitor_id0" >'+ value.company +'</b> </div> <div class="col-sm-2" > <br><b style="font-size: 18px" id="visitor_id0" name="visitor_id0" >'+ value.name +'</b> </div> <div class="col-sm-2" > <br><b style="font-size: 18px" id="visitor_id0" name="visitor_id0" >'+ value.department +'</b> </div> <div class="col-sm-2 " > <div class="input-group margin"> <input type="text" class="form-control" id="'+ value.tag +'" name="visitor_id0" placeholder="Reschedule" required value=""> <span class="input-group-btn"> <button type="button" class="btn btn-success btn-flat" onclick="out('+ id +')"><i class="glyphicon glyphicon-calendar"> Input</i> </button> </span> </div> </div> </div>');
								openErrorGritter('Error!', 'Meet Unconfirmed');
							}
							else if(value.remark =="Confirmed"){
								$('#apen').append('<div class="col-xs-12"> <div class="col-sm-1" > <br><b style="font-size: 18px" id="visitor_id0" name="visitor_id0" >'+ value.id_number +'</b> </div> <div class="col-sm-2" > <br><b style="font-size: 18px" id="visitor_id0" name="visitor_id0" >'+ value.full_name +'</b> </div> <div class="col-sm-1" > <br><b style="font-size: 18px" id="visitor_id0" name="visitor_id0" >'+ value.in_time +'</b> </div> <div class="col-sm-2" > <br><b style="font-size: 18px" id="visitor_id0" name="visitor_id0" >'+ value.company +'</b> </div> <div class="col-sm-2" > <br><b style="font-size: 18px" id="visitor_id0" name="visitor_id0" >'+ value.name +'</b> </div> <div class="col-sm-2" > <br><b style="font-size: 18px" id="visitor_id0" name="visitor_id0" >'+ value.department +'</b> </div></div>');
								out(id);
								openSuccessGritter('Success!', result.message);
							}
							else{
								openErrorGritter('Error!', 'Visitor Not Found');
							}	
						}); 

						if (result.ops=="") {
							openErrorGritter('Error!', 'Visitor Not Found');
						}
						
						

						$('#telp').val('');
						$("#telp").focus(); 
						

					}
					else{
						alert('Attempt to retrieve data failed');
					}
				}
				else{
					alert('Disconnected from server');
				}
			});
			
		}

	}


	function out(idtag) {

		var reason = $('#'+idtag).val();

		var data = {					
			idtag:idtag,
			reason:reason                  
		}
		$.post('{{ url("visitor_out") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){					
					openSuccessGritter('Success!', result.message);

				}
				else{
					openErrorGritter('Error!', result.message);
				}
			}
			else{
				alert("Disconnected from server");
			}
		});				

	}


</script>
@endsection