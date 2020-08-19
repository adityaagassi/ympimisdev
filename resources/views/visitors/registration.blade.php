@extends('layouts.visitor')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style>
	#loading, #error { display: none; }
</style>
@endsection


@section('header')
<section class="content-header">
	<h1>
		<center><span style="color: white; font-weight: bold; font-size: 28px; text-align: center;">YMPI Visitor Registration</span></center><br>
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
<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
	<p style="position: absolute; color: White; top: 45%; left: 35%;">
		<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
	</p>
</div>
<div class="row">
	<div class="col-xs-offset-1 col-xs-10">
		<div class="box">
			<div class="box-header with-border">
				<h3 class="box-title">Registration Form</h3>
			</div>
			<form class="form-horizontal" name="regisForm" id="regisForm" method="get" action="{{ url('simpan') }}">				
				<div class="box-body">
					<div class="form-group">
						<label for="inputEmail3" class="col-sm-2 control-label">Asal Perusahaan</label>
						<div class="col-sm-9">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<select class="form-control select2" id="company2" name="company2">
								<option style="color:grey;" value="">Pilih Asal Perusahaan</option>
								@foreach($company as $company)
									<option value="{{ $company->company }}">{{ $company->company }}</option>
								@endforeach
							</select>
							<br><br>
							<input type="text" class="form-control" id="company" name="company" placeholder="Input Asal Perusahaan (Jika Tidak Ada di List)">
						</div>
					</div>
					<div class="form-group">
						<label for="inputEmail3" class="col-sm-2 control-label">Tujuan</label>
						<div class="col-sm-9">
							<textarea class="form-control" id="purpose" name="purpose" placeholder="Input Tujuan" required></textarea>
						</div>
					</div>
					<div class="form-group">
						<label for="inputEmail3" class="col-sm-2 control-label">Jenis Kendaraan</label>
						<div class="col-sm-9">
							<select class="form-control select2" id="kendaraan" name="kendaraan" required>
								<option style="color:grey;" value="">Pilih Kendaraan</option>					
								<option value="Motorcycle">Motorcycle</option>
								<option value="Truck">Truck</option>
								<option value="Bus">Bus</option>
								<option value="SUV / MPV">SUV / MPV</option>
								<option value="Container">Container</option>
								<option value="Ojek Online">Grab / Gojek</option>
								<option value="No Vehicle">No Vehicle</option>
								<option value="Other">Other</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="inputEmail3" class="col-sm-2 control-label">Nomor Kendaraan</label>
						<div class="col-sm-9">
							<input class="form-control" id="pol" name="pol" placeholder="Input Nomor Kendaraan (Kosongi Jika Tidak Ada)"></input>
						</div>
					</div>
					<div class="form-group">
						<label for="inputEmail3" class="col-sm-2 control-label">Status</label>
						<div class="col-sm-9">
							<input type="text" name="lop2" id="lop2" hidden>
							<select class="form-control select2" id="status" name="status" placeholder="Pilih Status" required>
								<option style="color:grey;" value="">Pilih Status</option>
								<option value="Visitor">Visitor</option>
								<option value="Vendor">Vendor</option>
								<option value="Officer">Officer</option>
								<option value="VIP">VIP / VVIP</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="inputEmail3" class="col-sm-2 control-label">Destination</label>
						<div class="col-sm-9">
							<select class="form-control select2" id="destination" name="destination" placeholder="Pilih Destination" required>
								<option style="color:grey;" value="">Pilih Destination</option>
								<option value="Office">Office</option>
								<option value="Warehouse">Warehouse</option>
								<option value="Kantin">Kantin</option>
								<option value="Omi">Omi</option>
								<option value="Other">Other</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="inputEmail3" class="col-sm-2 control-label">Karyawan YMPI</label>
						<div class="col-sm-9">
							<input type="text" name="lop" id="lop" hidden>
							<select class="form-control select2" id="status" name="employee" required>
								<option style="color:grey;" value="">Pilih Karyawan YMPI</option>
								@foreach($employee as $nomor => $employee)
								<option value="{{$employee->empid}}">{{$employee->name}} - ( {{$employee->department}} )</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="inputEmail3" class="col-sm-2 control-label">ID/Nama <b id="total"></b></label>
						<input type="text" name="lop" id="lop" value="1" hidden>

						<div class="col-sm-2" style="padding-right: 0;">
							<input type="text" class="form-control" id="visitor_id0" name="visitor_id0" placeholder="No. KTP/SIM" required onchange="getdata(this.id)">
						</div>
						<div class="col-sm-4" style=" padding-right: 0;">
							<input type="text" class="form-control" id="visitor_name0" name="visitor_name0" placeholder="Full Name" required>
						</div>
						<div class="col-sm-3" style=" padding-right: 0;">
							<input type="text" class="form-control" id="telp0" name="telp0" placeholder="No Hp" >
						</div>
						<!-- <div class="col-sm-2">
							&nbsp;<a class="btn btn-success" onclick='tambah("tambah","lop");' href="javascript:void(0)" style="padding: 6px 12px 6px 12px"><i class='fa fa-plus' ></i></a> 
						</div><br><br> -->

						<div id="tambah"></div>

					</div>
					<div class="form-group">
						<label for="inputEmail3" class="col-sm-2 control-label">Jumlah Orang</label>
						<div class="col-sm-9">
							<input class="form-control" id="jumlah" name="jumlah" placeholder="Input Jumlah Orang" required value="1"></input>
						</div>
					</div>
					<div class="form-group">
						<label for="inputEmail3" class="col-sm-2 control-label">Suhu Tubuh <b id="total_suhu"></b></label>
						<input type="text" name="lop_suhu" id="lop_suhu" value="1" hidden>
						<input type="text" name="lop_suhu2" id="lop_suhu2" value="1" hidden>

						<div class="col-sm-3" style="padding-right: 0;">
							<input type="text" class="form-control" id="nama0" name="nama0" placeholder="Nama" required>
						</div>
						<div class="col-sm-2" style="padding-right: 0;">
							<input type="text" class="form-control" id="kota0" name="kota0" placeholder="Asal Kota" required>
						</div>
						<div class="col-sm-3" style=" padding-right: 0;">
							<input type="text" class="form-control" id="suhu0" name="suhu0" placeholder="Suhu Tubuh" required>
						</div>
						<div class="col-sm-2">
							&nbsp;<a class="btn btn-success" onclick='tambah_suhu("tambah_suhu","lop_suhu");' href="javascript:void(0)" style="padding: 6px 12px 6px 12px"><i class='fa fa-plus' ></i></a> 
						</div><br><br>

						<div id="tambah_suhu"></div>

					</div>
				</div>
				<div class="box-footer">
					<button type="submit" class="btn btn-default" onclick="window.history.go(-1)">Cancel</button>
					<button type="submit" class="btn btn-info pull-right" id="register">Register</button>
				</div>
			</form>
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

	$("#regisForm").submit(function(){
		$('#loading').show();
		$('#register').prop('disabled',true);
		$('#register').hide();
		this.submit();
	});



	function tambah(id,lop) {	


		var divdata = $("<div id='"+no+"'><label for='inputEmail3' class='col-sm-2 control-label' id='nomor'></label><input type='text' name='lop_suhu' id='lop_suhu' value='1' hidden><div class='col-sm-5' style='padding-right: 0;'><input type='text' class='form-control' id='nama"+no+"' name='nama"+no+"' placeholder='Nama' required></div><div class='col-sm-4' style='padding-left: 1; padding-right: 0;'><input type='text' class='form-control' id='suhu"+no+"' name='suhu"+no+"' placeholder='Suhu' required></div><div class='col-sm-2'>&nbsp;<button onclick='kurang(this,\""+lop+"\");' class='btn btn-danger'><i class='fa fa-close'></i> </button> <a class='btn btn-success' onclick='tambah(\""+id+"\",\""+lop+"\");' href='javascript:void(0)''><i class='fa fa-plus' ></i></a></div><br><br></div>");

		$("#"+id).append(divdata);			
		no+=1;
		$('#total_suhu').text("("+no+")");
		document.getElementById("lop").value=no;
		$('#lop2').val(no);

	}

	function tambah_suhu(id,lop) {	


		var divdata = $("<div id='"+no+"'><label for='inputEmail3' class='col-sm-2 control-label' id='nomor'></label><input type='text' name='lop_suhu' id='lop_suhu' value='1' hidden><div class='col-sm-3' style='padding-right: 0;'><input type='text' class='form-control' id='nama"+no+"' name='nama"+no+"' placeholder='Nama' required></div><div class='col-sm-2' style='padding-right: 0;'><input type='text' class='form-control' id='kota"+no+"' name='kota"+no+"' placeholder='Asal Kota' required></div><div class='col-sm-3' style='padding-left: 1; padding-right: 0;'><input type='text' class='form-control' id='suhu"+no+"' name='suhu"+no+"' placeholder='Suhu Tubuh' required></div><div class='col-sm-2'>&nbsp;<button onclick='kurang_suhu(this,\""+lop+"\");' class='btn btn-danger'><i class='fa fa-close'></i> </button> <a class='btn btn-success' onclick='tambah(\""+id+"\",\""+lop+"\");' href='javascript:void(0)''><i class='fa fa-plus' ></i></a></div><br><br></div>");

		$("#"+id).append(divdata);			
		no+=1;
		$('#total_suhu').text("("+no+")");
		document.getElementById("lop_suhu").value=no;
		$('#lop_suhu2').val(no);

	}

	function kurang(elem,lop) {			
		var ids = $(elem).parent('div').parent('div').attr('id');
		var oldid = ids;
		$(elem).parent('div').parent('div').remove();
		var newid = parseInt(ids) + 1;
		jQuery("#"+newid).attr("id",oldid);
		jQuery("#visitor_id"+newid).attr("name","visitor_id"+oldid);
		jQuery("#visitor_name"+newid).attr("name","visitor_name"+oldid);
		jQuery("#telp"+newid).attr("name","telp"+oldid);

		jQuery("#telp"+newid).attr("id","telp"+oldid);
		jQuery("#visitor_id"+newid).attr("id","visitor_id"+oldid);
		jQuery("#visitor_name"+newid).attr("id","visitor_name"+oldid);

		no-=1;
		var a = no -1;

		for (var i =  ids; i <= a; i++) {	
			var newid = parseInt(i) + 1;
			var oldid = newid - 1;
			jQuery("#"+newid).attr("id",oldid);
			jQuery("#visitor_id"+newid).attr("name","visitor_id"+oldid);
			jQuery("#visitor_name"+newid).attr("name","visitor_name"+oldid);
			jQuery("#telp"+newid).attr("name","telp"+oldid);

			jQuery("#telp"+newid).attr("id","telp"+oldid);
			jQuery("#visitor_id"+newid).attr("id","visitor_id"+oldid);
			jQuery("#visitor_name"+newid).attr("id","visitor_name"+oldid);

			// alert(i)
		}

		$('#total').text("("+no+")");
		document.getElementById("lop").value=no;
		$('#lop2').val(no);
	}

	function kurang_suhu(elem,lop) {			
		var ids = $(elem).parent('div').parent('div').attr('id');
		var oldid = ids;
		$(elem).parent('div').parent('div').remove();
		var newid = parseInt(ids) + 1;
		jQuery("#"+newid).attr("id",oldid);
		jQuery("#nama"+newid).attr("name","nama"+oldid);
		jQuery("#suhu"+newid).attr("name","suhu"+oldid);
		jQuery("#kota"+newid).attr("name","kota"+oldid);

		jQuery("#nama"+newid).attr("id","nama"+oldid);
		jQuery("#suhu"+newid).attr("id","suhu"+oldid);
		jQuery("#kota"+newid).attr("id","kota"+oldid);

		no-=1;
		var a = no -1;

		for (var i =  ids; i <= a; i++) {	
			var newid = parseInt(i) + 1;
			var oldid = newid - 1;
			jQuery("#"+newid).attr("id",oldid);
			jQuery("#nama"+newid).attr("name","nama"+oldid);
			jQuery("#suhu"+newid).attr("name","suhu"+oldid);
			jQuery("#kota"+newid).attr("name","kota"+oldid);

			jQuery("#nama"+newid).attr("id","nama"+oldid);
			jQuery("#suhu"+newid).attr("id","suhu"+oldid);
			jQuery("#kota"+newid).attr("id","kota"+oldid);

			// alert(i)
		}

		$('#total_suhu').text("("+no+")");
		document.getElementById("lop_suhu").value=no;
		$('#lop_suhu2').val(no);
	}

	function getdata(id) {
		var id = id;
		var ids = id.substr(10, 1); 
		var ktp = $('#visitor_id'+ids).val();
		var nama = "visitor_name";
		var	telp = "telp";
		
		var data = {					
			ktp:ktp,			                  
		}
		$.get('{{ url("visitor_getdata") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$.each(result.id_list, function(key, value) { 						
						$('#'+nama+ids).val(value.full_name);
						$('#nama0').val(value.full_name);
						$('#'+telp+ids).val(value.telp);
					});					
					// openSuccessGritter('Success!', result.message);
					if (result.id_list ==""){
						$('#'+nama+ids).val('');
						$('#nama0').val('');
						$('#'+telp+ids).val('');					
					// openErrorGritter('Error!', result.message);
				}
			}
		}
		else{
			alert("Disconnected from server");
		}
	});
	}
</script>
@endsection