@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	th:hover {
		overflow: visible;
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
		border:1px solid black;
		vertical-align: middle;
		padding:5px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:5px;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	#fill_kaizen > thead > tr > th[class*="sort"]:after{
		content: "" !important;
	}
	#queueTable.dataTable {
		margin-top: 0px!important;
	}
	#loading, #error { display: none; }
	.post .user-block {
		margin-bottom: 5px
	}
	#chat {
		height:480px;
		overflow-y: scroll;
	}
	#kz_detail_1 > tbody > tr > td, #kz_detail_2 > tbody > tr > td, #kz_detail_3 > tbody > tr > td, #kz_detail_4 > tbody > tr > td {
		text-align: left;
	}
	#kz_detail_1, #kz_detail_2, #kz_detail_3, #kz_detail_4{
		margin-bottom: 10px
	}
	#kz_sekarang > p > img, #kz_perbaikan > p > img {
	/*	max-width: 25%;
	max-height: 25%;*/
}

</style>
@stop
@section('header')
{{-- <section class="content-header" style="padding-top: 0; padding-bottom: 0;"> --}}
	<h1>
		<span class="text-yellow">
			{{ $title }}
		</span>
		<small>
			<span style="color: #FFD700;"> {{ $title_jp }}</span>
		</small>
	</h1>
	<br>
{{-- </section> --}}
@endsection
@section('content')
@php
$avatar = 'images/avatar/'.Auth::user()->avatar;
@endphp
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px;">
	<div class="row">
		<div class="col-md-3">
			<!-- Profile Image -->
			<div class="box">
				<div class="box-body box-profile">
					<img class="profile-user-img img-responsive img-circle" src="{{ url($avatar) }}" alt="User profile picture">

					<h3 class="profile-username text-center">{{ $profil[0]->name }}</h3>

					<p class="text-muted text-center" style="text-transform: lowercase;">{{ $emp_id }}</p>

					<ul class="list-group list-group-unbordered">
						<li class="list-group-item">
							<b>Sisa Cuti</b> <a class="pull-right">
								<span class="label label-warning">{{ $sisa_cuti[0]->sisa_cuti }} / {{ $sisa_cuti[0]->cuti }}</span>
								<!-- <span class="label label-danger">-</span>/
									<span class="label label-danger">-</span> -->
								</a>
							</li>
							<li class="list-group-item">
								<b>Penugasan</b> <a class="pull-right">
									<span class="label label-success">{{ $profil[0]->position }}</span>
								</a>
							</li>
						</ul>
					</div>
					<!-- /.box-body -->
				</div>
				<!-- /.box -->

				<!-- About Me Box -->
				<div class="box">
					<div class="box-header with-border">
						<h3 class="box-title">Tentang Saya</h3>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<strong><i class="fa fa-briefcase margin-r-5"></i> Bagian</strong>

						<p class="text-muted">
							{{ $profil[0]->division }} - {{$profil[0]->department}} - {{$profil[0]->section}} - {{$profil[0]->sub_section}} - {{$profil[0]->group}}
						</p>

						<hr>

						<strong><i class="fa fa-cc margin-r-5"></i> Cost Center</strong>

						<p class="text-muted">{{$profil[0]->cost_center}}</p>

						<hr>

						<strong><i class="fa fa-calendar margin-r-5"></i> Tanggal Masuk</strong>

						<p class="text-muted">{{$profil[0]->hire_date}}</p>

						<hr>

						<strong><i class="fa fa-star margin-r-5"></i> Grade</strong>

						<p class="text-muted">{{$profil[0]->grade_code}} - {{$profil[0]->grade_name}}</p>

						<hr>

						<strong><i class="fa fa-phone margin-r-5"></i> Nomor Telepon</strong>
						<div class="pull-right"><button class="btn btn-sm btn-primary" style="padding: 2px 5px 2px 5px" data-toggle="modal" data-target="#editModal"><u><i class="fa fa-pencil"></i> Edit</button></u></div>

						<p class="text-muted"><i class="fa fa-mobile-phone margin-r-5"></i>&nbsp;&nbsp; {{$profil[0]->phone}}<br>
							<i class="fa fa-whatsapp margin-r-5"></i> {{$profil[0]->wa_number}}</p>

						</div>
						<!-- /.box-body -->
					</div>
					<!-- /.box -->
				</div>
				<!-- /.col -->
				<div class="col-md-9" style="margin-bottom: 10px">
					<button class="btn btn-success" onclick="questionForm()" id="btnTanya"><i class="fa fa-question-circle"></i>&nbsp; Tanya HR &nbsp;<i class="fa fa-angle-double-right"></i></button>
					<button class="btn btn-default" onclick="kembali()" style="display: none" id="btnKembali"><i class="fa fa-angle-double-left"></i>&nbsp; Kembali</button>

					<!-- <button class="btn btn-primary" onclick="ekaizen()" id="btnKaizen"><i class="fa  fa-bullhorn"></i>&nbsp; E - Kaizen &nbsp;<i class="fa fa-angle-double-right"></i></button> -->
				</div>
				<div class="col-md-9">
					<div class="box" id="boxing">
						<div class="box-header">
							<h3 class="box-title">Resume Absensi & Lembur</h3>
							<div class="pull-right">
								<select class="form-control select2">
									<option>2019</option>
								</select>
							</div>
						</div>
						<!-- /.box-header -->
						<div class="box-body">
							<table class="table table-bordered table-striped" id="history">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th style="width: 10%">Periode</th>
										<th style="width: 10%">Mangkir</th>
										<th style="width: 10%">Izin</th>
										<th style="width: 10%">Sakit</th>
										<th style="width: 10%">Terlambat</th>
										<th style="width: 10%">Pulang Cepat</th>
										<th style="width: 10%">Cuti</th>
										<th style="width: 10%">Tunjangan Disiplin</th>
										<th style="width: 10%">Lembur (Jam)</th>
									</tr>
								</thead>
								<tbody>
									@foreach ($absences as $data)
									<tr>
										<td>{{$data->period}}</td>
										<td>
											@if ($data->absent > 0) 
											<span class="badge bg-yellow">{{$data->absent}}</span>
											@else 
											- 
											@endif
										</td>
										<td>
											@if ($data->permit > 0) 
											<span class="badge bg-yellow">{{$data->permit}}</span>
											@else 
											- 
											@endif
										</td>
										<td>
											@if ($data->sick > 0)
											<span class="badge bg-yellow">{{$data->sick}}</span>
											@else 
											- 
											@endif
										</td>
										<td>@if ($data->late > 0)
											<span class="badge bg-yellow">{{$data->late}}</span>
											@else 
											- 
										@endif</td>
										<td>
											@if ($data->pc > 0)
											<span class="badge bg-yellow">{{$data->pc}}</span>
											@else 
											- 
											@endif
										</td>
										<td>
											@if ($data->personal_leave > 0) 
											<span class="badge bg-yellow">{{$data->personal_leave}}</span>
											@else 
											- 
											@endif
										</td>
										<td>
											@if ($data->dicipline > 0)
											<i class="fa fa-close" style="color: red"></i>
											@else 
											<i class="fa fa-check" style="color: #18c40c"></i>
											@endif
										</td>
										<td>
											@if ($data->overtime > 0)
											<span class="badge bg-yellow">{{$data->overtime}}</span>
											@else 
											- 
											@endif
										</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
						<!-- /.box-body -->
					</div>

					<!-- QUESTION & ANSWER -->

					<div class="box" id="question" style="display: none;">
						<div class="box-header">
							<h3 class="box-title">Question & Answer</h3>
						</div>
						<div class="box-body">
							<div class="col-xs-12">
								<div class="row">
									<div class="col-xs-2">
										<select class="form-control select2" style="width: 100%" id="category">
											<option disabled selected value="">Category</option>
											<option value="Absensi">Absensi</option>
											<option value="Lembur">Lembur</option>
											<option value="Cuti">Cuti</option>
											<option value="PKB">PKB</option>
											<option value="Penggajian">Penggajian</option>
											<option value="BPJS Kes">BPJS Kes</option>
											<option value="BPJS TK">BPJS TK</option>
										</select>
									</div>
									<div class="col-xs-10">
										<div class="input-group input-group">
											<input type="text" class="form-control" id="msg" placeholder="Write a Message...">
											<span class="input-group-btn">
												<button type="button" class="btn btn-success btn-flat" onclick="posting()"><i class="fa fa-send-o"></i>&nbsp; Post</button>
											</span>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xs-12">
								<hr>
								<div id="chat">
								</div>
							</div>
						</div>
					</div>

					<!-- E-KAIZEN -->

					<div class="box" id="kaizen" style="display: none;">
						<div class="box-header">
							<h3 class="box-title">E-Kaizen</h3>
							<a class="btn btn-primary pull-right" href="{{ url("create/ekaizen/".$emp_id."/".$profil[0]->name) }}"><i class="fa fa-bullhorn"></i>&nbsp; Buat Kaizen</a>
						</div>
						<div class="box-body">
							<div class="row">
								<div class="col-xs-4 col-xs-offset-2">
									<label>Tanggal Dari :</label>
									<input type="text" id="bulanAwal" class="form-control datepicker" placeholder="Tanggal dari..">
								</div>
								<div class="col-xs-4">
									<label>Tanggal Sampai :</label>
									<input type="text" id="bulanAkhir" class="form-control datepicker" placeholder="Tanggal sampai..">
								</div>
							</div>
							<hr>
							<table class="table table-bordered" id="tableKaizen" width="100%">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th>Id</th>
										<th>Tanggal</th>
										<th>Usulan</th>
										<th>Status</th>
										<th>Posisi</th>
										<th>Aplikasi</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>

				</div>
				<!-- /.col -->
			</div>
			<div id="editModal" class="modal fade" role="dialog">
				<div class="modal-dialog">

					<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Edit Nomor yang bisa Dihubungi</h4>
						</div>
						<div class="modal-body">
							<div class="row" style="margin-bottom: 2%">
								<div class="col-xs-5" align="right"><label>Nomor Telepon<span class="text-red">*</span></label></div>
								<div class="col-xs-6"><input type="text" class="form-control" placeholder="Active Phone Number" id="phone_number"></div>
							</div>
							<div class="row">
								<div class="col-xs-5" align="right"><label>Nomor WhatsApp<span class="text-red">*</span></label></div>
								<div class="col-xs-6"><input type="text" class="form-control" placeholder="WhatsApp Number" id="wa_number"></div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
							<button type="button" class="btn btn-primary" id="editData"><i class="fa fa-pencil"></i> Edit</button>
						</div>
					</div>

				</div>
			</div>

			<!-- DETAIL -->

			<div class="modal fade" id="modalKaizenDetail" role="dialog" aria-hidden="true">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<center><b><h5 class="modal-title" style="font-weight: bold;">Kaizen Teian</h5>(Usulan Perbaikan)</b></center>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-xs-12">
									<table border="1" class="table table-bordered" width="100%" id="kz_detail_1">
										<tr>
											<td>Nama :</td>
											<td id="kz_nama"> Muhammad Nasiqul Ibat</td>
											<td>Tgl. :</td>
											<td id="kz_tanggal"> 12-11-2019</td>
											<td>Nama Sub Leader</td>
										</tr>
										<tr>
											<td>NIK :</td>
											<td id="kz_nik"> 19014987</td>
											<td>Bagian :</td>
											<td id="kz_bagian"> Management Information System</td>
											<td id="kz_leader">Agus Y</td>
										</tr>
									</table>

									<table class="table table-bordered" width="100%" id="kz_detail_2">
										<tr>
											<td>
												<label>Judul Usulan</label>
												<div id="kz_judul"></div>
											</td>
										</tr>
									</table>

									<table class="table table-bordered" width="100%" id="kz_detail_3">
										<tr>
											<td>
												<label>Kondisi Sekarang</label>
												<div id="kz_sekarang"></div>
											</td>
										</tr>
									</table>

									<table class="table table-bordered" width="100%" id="kz_detail_4">
										<tr>
											<td>
												<label>Usulan Perbaikan</label>
												<div id="kz_perbaikan"></div>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>

		</section>
		@endsection
		@section('scripts')
		<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
		<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
		<script>
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});			

			var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
			var chat = 0;
			var name = "";

			jQuery(document).ready(function() {
				$('body').toggleClass("sidebar-collapse");

				$('.select2').select2({
					language : {
						noResults : function(params) {
							return "There is no data";
						}
					}
				});

				// $('#kz_sub_leader').select2({ width: 'resolve' });

				name = "{{ $profil[0]->name }}";
				name = name.replace('&#039;','');

				$("#phone_number").val("{{$profil[0]->phone}}");
				$("#wa_number").val("{{$profil[0]->wa_number}}");

				fill_chat();

				$('.datepicker').datepicker({
					autoclose: true,
					format: 'yyyy-mm-dd',
				})
			});

			$(window).on('pageshow', function(){
				fill_kaizen();
			});

			$("#editData").click(function() {
				var data = {
					employee_id: "{{ $emp_id }}",
					phone_number: $("#phone_number").val(),
					wa_number: $("#wa_number").val()
				}

				if ($("#phone_number").val() == "{{$profil[0]->phone}}" && $("#wa_number").val() == "{{$profil[0]->wa_number}}") {
					$('#editModal').modal('hide');
					openSuccessGritter('Success','Tidak Ada Data yang Berubah');
				} else {
					$.get('{{ url("update/employee/number") }}', data, function(result, status, xhr){
						if (result.status) {
							$('#editModal').modal('hide');
							openSuccessGritter('Success','Nomor Telepon Berhasil Diubah');
						} else {
							openErrorGritter('Error!', result.datas);
						}
					})
				}

			});

			function check_chart() {
				if (!$(".komen").is(':focus') && chat == 1) {
					fill_chat();
				}
			}

			function fill_chat() {
				var data = {
					employee_id: '{{ $emp_id }}_'+name.split(' ').slice(0,2).join('-')
				}

				$.get('{{ url("fetch/chat/hrqa") }}', data, function(result, status, xhr){
					if(result.status){
						$("#chat").empty();
						var xCategories2 = [];

						for(var i = 0; i < result.chats.length; i++){
							ctg = result.chats[i].id+"_"+result.chats[i].message+"_"+result.chats[i].category+"_"+result.chats[i].created_at_new;

							if(xCategories2.indexOf(ctg) === -1){
								xCategories2[xCategories2.length] = ctg;
							}
						}


						$.each(xCategories2, function(index, value){
							var chat_history = "";
							var chats = value.split("_");
							chat_history += '<div class="post">';
							chat_history += '<div class="user-block">'
							chat_history += '<img class="img-circle img-bordered-sm" src="'+result.base_avatar+'/{{ $emp_id }}.jpg" alt="image">';
							chat_history += '<span class="username">{{ $emp_id }}_'+name.split(' ').slice(0,2).join('-')+'</span>';
							chat_history += '<span class="description">'+chats[3]+'</span></div>';
							chat_history += '<p>'+chats[1]+'</p>';

							var stat = 0;
							var rev = 0;

							$.each(result.chats, function(index2, value2){
								if (chats[0] == value2.id) { 
									if (value2.message_detail) {
										if (stat == 0) {
											chat_history += '<div style="margin-left: 30px">';
										} else {
											chat_history += '<div>';
										}

										chat_history += '<div class="post">'
										chat_history += '<div class="user-block">';
										chat_history += '<img class="img-circle img-bordered-sm" src="'+result.base_avatar+'/'+value2.avatar+'.jpg" alt="image">';
										chat_history += '<span class="username">'+value2.dari+' &nbsp; ';
										chat_history += '<span style="color:#999; font-size:13px">'+value2.created_at_new+'</span></span>';
										chat_history += '<span class="description" style="color:#666">'+value2.message_detail+'</span></div>';
								// chat_history += '<p>'+value2.message_detail+'</p>';

								stat = 1;

								if (typeof result.chats[index2+1] === 'undefined') {
									rev = 1;
									chat_history += '<input class="form-control input-sm komen" type="text" placeholder="Type a comment" id="comment_'+value2.id+'"></div>';
								} else {
									if (result.chats[index2].id != result.chats[index2+1].id) {
										rev = 1;
										chat_history += '<input class="form-control input-sm komen" type="text" placeholder="Type a comment" id="comment_'+value2.id+'"></div>';
									}
								}
							} else {
								if (rev == 0) {
									chat_history += '<input class="form-control input-sm komen" type="text" placeholder="Type a comment" id="comment_'+value2.id+'">';	
								}
							}
						}

					})
							chat_history += '</div>';

							$("#chat").append(chat_history);
						})

						$(".komen").keypress(function() {
							var keycode = (event.keyCode ? event.keyCode : event.which);
							if(keycode == '13'){
								if (this.value != "") {
									var id2 = this.id.split("_")[1];
							// alert(id+" "+this.value+" HR");
							var data = {
								id:id2,
								message:this.value,
								from:"{{ $emp_id }}_"+name.split(' ').slice(0,2).join('-')
							}

							$.post('{{ url("post/chat/comment") }}', data, function(result, status, xhr){
								fill_chat();
							})
						} else {
							alert('Komentar tidak boleh kosong'); 
						}
					}
				});
					}
				})
			}

			function posting() {
				var msg = $("#msg").val();
				var cat = $("#category").val();

				if (msg == "" && cat == "") {
					openErrorGritter('Error!','Pesan harus diisi');
					return false;
				}

				var data = {
					message:msg,
					category:cat,
					from:"{{ $emp_id }}_"+name.split(' ').slice(0,2).join('-')
				}

				$.post('{{ url("post/hrqa") }}', data, function(result, status, xhr){
					openSuccessGritter('Success','');
					$("#msg").val("");
					fill_chat();
				})
			}

			function fill_kaizen() {
				var data = {
					employee_id : "{{ $emp_id }}"
				}
				$('#tableKaizen').DataTable().destroy();
				var table2 = $('#tableKaizen').DataTable({
					'dom': 'Bfrtip',
					'responsive': true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'paging': true,
					'lengthChange': true,
					'searching': true,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"processing": true,
					"serverSide": true,
					"ajax": {
						"type" : "get",
						"url" : "{{ url("fetch/report/kaizen") }}",
						"data" : data
					},
					"columns": [
					{ "data": "id" },
					{ "data": "propose_date" },
					{ "data": "title" },
					{ "data": "section" },
					{ "data": "posisi" },
					{ "data": "application" },
					{ "data": "action" }
					]
				});

				$('#tableKaizen tfoot th').each( function () {
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="3"/>' );
				});

				table2.columns().every( function () {
					var that = this;
					$( 'input', this.footer() ).on( 'keyup change', function () {
						if ( that.search() !== this.value ) {
							that
							.search( this.value )
							.draw();
						}
					});
				});
				$('#tableKaizen tfoot tr').appendTo('#tableKaizen thead');
			}


			$("#kz_buat").click( function() {
				var data = {
					employee_id: $("#kz_nik").val(),
					employee_name: $("#kz_nama").val(),
					propose_date: $("#kz_tanggal").val(),
					section: 'dd',
					sub_leader: $("#kz_sub_leader").val(),
					title: $("#kz_judul").val(),
					condition: CKEDITOR.instances.kz_sekarang.getData(),
					improvement: CKEDITOR.instances.kz_perbaikan.getData()
				};

				// if ($("kz_sub_leader").val() != '' && $("kz_judul").val() != '') {
					$.post('{{ url("post/ekaizen") }}', data, function(result, status, xhr){
						console.log(result.datas);
					})
				// }

			});

			function detail(id) {
				var data = {
					id : id
				};

				$.get('{{ url("get/ekaizen") }}', data, function(result, status, xhr){
					$("#modalKaizenDetail").modal('show');
					$("#kz_nama").text(result.employee_name);
					$("#kz_tanggal").text(result.propose_date);
					$("#kz_nik").text(result.employee_id);
					$("#kz_bagian").text(result.section);
					$("#kz_leader").text(result.leader);
					$("#kz_judul").text(result.title);
					$("#kz_sekarang").html(result.condition);
					$("#kz_perbaikan").html(result.improvement);
				})
			}

			function load_leader() {
				$.get('{{ url("fetch/sub_leader") }}', function(result, status, xhr){
					
					fill_chat();
				})
			}

			function questionForm() {
				$("#boxing").hide();
				$("#question").show();
				$("#btnTanya").hide();
				$("#btnKembali").show();
				$("#btnKaizen").hide();
				chat = 1;
			}

			function kembali() {
				$("#boxing").show();
				$("#question").hide();
				$("#kaizen").hide();
				$("#btnKembali").hide();
				$("#btnTanya").show();
				$("#btnKaizen").show();
				chat = 0;
			}

			function ekaizen() {
				$("#boxing").hide();
				$("#kaizen").show();
				$("#btnTanya").hide();
				$("#btnKaizen").hide();
				$("#btnKembali").show();
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
		@endsection