@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style type="text/css">
	.switch {
		position: relative;
		display: inline-block;
		width: 60px;
		height: 34px;
	}

	.switch input { 
		opacity: 0;
		width: 0;
		height: 0;
	}

	.slider {
		position: absolute;
		cursor: pointer;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background-color: #ccc;
		-webkit-transition: .4s;
		transition: .4s;
	}

	.slider:before {
		position: absolute;
		content: "";
		height: 26px;
		width: 26px;
		left: 4px;
		bottom: 4px;
		background-color: white;
		-webkit-transition: .4s;
		transition: .4s;
	}

	input:checked + .slider {
		background-color: #2196F3;
	}

	input:focus + .slider {
		box-shadow: 0 0 1px #2196F3;
	}

	input:checked + .slider:before {
		-webkit-transform: translateX(26px);
		-ms-transform: translateX(26px);
		transform: translateX(26px);
	}

	/* Rounded sliders */
	.slider.round {
		border-radius: 34px;
	}

	.slider.round:before {
		border-radius: 50%;
	}

	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<input type="hidden" id="green">
	<h1>
		{{ $page }}
	</h1>
</section>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 0px">
					<thead>
						<tr>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 18px;" colspan="2">General Information</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">Check Date</td>
							<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 20px;" id="tanggal"></td>
						</tr>
						<tr>
							<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">Day</td>
							<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 20px;" id="hari"></td>
						</tr>
						<tr>
							<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">PIC</td>
							<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 20px;" id="pic">{{ $name }}</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="col-xs-12" style="padding-right: 0; padding-left: 0;">
				<table class="table table-bordered" style="width: 100%; color: white;" id="table_cek">
					<thead style="font-weight: bold; color: black; background-color: #ddd;">
						<tr>
							<th>Department</th>
							<th>Location</th>
							<th>Project Name</th>
							<th>Item Check</th>
							<th>OK</th>
							<th>Picture</th>
						</tr>
					</thead>
					<tbody id="body_cek"></tbody>
				</table>
				<br>
				<button class="btn btn-success" style="width: 100%" onclick="cek()"><i class="fa fa-check"></i>Check</button>
			</div>
		</div>
	</div>
</section>

@endsection
@section('scripts')
<script src="{{ url("js/bootstrap-toggle.min.js") }}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>

<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var cek_point = 0;

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		get_check();
	})

	function get_check() {
		$.get('{{ url("fetch/audit_mis/check") }}', function(result, status, xhr){
			var body = "";
			$("#body_cek").empty();

			$.each(result.check_list, function(index, value){
				body += "<tr>";
				body += "<td>"+value.department+"</td>";
				body += "<td>"+value.location+"</td>";
				body += "<td>"+value.system_name+"</td>";
				body += "<td>"+value.item_check+"</td>";
				body += "<td><label class='switch'><input type='checkbox' id='check_"+value.id+"' class='cekbox'><span class='slider round'></span></label></td>";
				body += "<td><input type='file' id='file_"+value.id+"' class='ng'></td>";

				body += "</tr>";
				cek_point += 1;
			})

			$("#body_cek").append(body);

			var table = $('#table_cek').DataTable( {
				responsive: true,
				paging: false,
				searching: false,
				bInfo : false
			} );
			console.log(cek_point);
		})

	}

	function cek() {
		if (confirm('Apakah anda yakin')) {
			// var arr_ng = [];
			// $('.ng').each(function(index, value) {
			// 	if ($(this).get(0).files.length === 0) {

			// 	} else {
			// 		var id = $(this).attr("id");
			// 		var ids = id.split('_')[1];

			// 		var fileInput = document.getElementById(id);

			// 		var reader = new FileReader();
			// 		reader.readAsDataURL(fileInput.files[0]);

			// 		reader.onload = function () {
			// 			arr_ng.push(reader.result);
			// 		};

			// 	}
			// });

			// var arr_ng2 = [];

			// $('.cekbox').each(function(index, value) {
			// 	if ($(this).is(':checked')) {

			// 	} else {
			// 		var id = $(this).attr("id");
			// 		var ids = id.split('_')[1];

			// 		arr_ng2.push(ids);
			// 	}
			// })

			// console.log(arr_ng);

			// data = {
			// 	ng_list : arr_ng2,
			// 	ng_gambar : arr_ng
			// };

			var stat = 0;

			// for(var i = 0; i < countpoint; i++){
				$('.ng').each(function(index, value) {
					var id = $(this).attr("id");
					var ids = id.split('_')[1];

					if ($("#check_"+ids).is(':checked')) {

					} else {
						var fileData  = $('#file_'+ids).prop('files')[0];

						var file=$('#file_'+ids).val().replace(/C:\\fakepath\\/i, '').split(".");

						var formData = new FormData();
						formData.append('fileData', fileData);
						formData.append('ng_list', ids);
						formData.append('extension', file[1]);
						formData.append('foto_name', file[0]);

						$.ajax({
							url:"{{ url('post/audit_mis/check') }}",
							method:"POST",
							data:formData,
							dataType:'JSON',
							contentType: false,
							cache: false,
							processData: false,
							success:function(data)
							{
								openSuccessGritter('Success', 'Check Audit Has Been Saved.');
							}
						})
					}

				})
			}
		}

		function openSuccessGritter(title, message){
			jQuery.gritter.add({
				title: title,
				text: message,
				class_name: 'growl-success',
				image: '{{ url("images/image-screen.png") }}',
				sticky: false,
				time: '2000'
			});
		}

		function openErrorGritter(title, message) {
			jQuery.gritter.add({
				title: title,
				text: message,
				class_name: 'growl-danger',
				image: '{{ url("images/image-stop.png") }}',
				sticky: false,
				time: '2000'
			});
		}

	</script>
	@endsection