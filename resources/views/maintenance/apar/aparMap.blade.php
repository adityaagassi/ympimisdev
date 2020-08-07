@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
		color:white;
		font-weight: bold;
		font-size: 12pt;
	}
	tbody>tr>td{
		text-align:center;
		color:white;
		border-top: 1px solid #333333 !important;
	}
	tfoot>tr>th{
		text-align:center;
		color:white;
	}
	td:hover {
		overflow: visible;
	}
	table {
		background-color: #212121;
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
	<div class="col-xs-12">
		<center>
			<button class="btn btn-default btn-lg" onclick="change_map(this.id)" style="border: 2px solid #7e5686; color: #7e5686; font-weight: bold;" id="btn_RCD-INJ-PRESS">RCD+INJ+PRESS</button>
			<button class="btn btn-default btn-lg" onclick="change_map(this.id)" style="border: 2px solid #7e5686; color: #7e5686; font-weight: bold;" id="btn_WKS-MTC">WKS+MTC</button>
			<button class="btn btn-default btn-lg" onclick="change_map(this.id)" style="border: 2px solid #7e5686; color: #7e5686; font-weight: bold;" id="btn_T-PRO-1">T-PRO Lt 1</button>
			<button class="btn btn-default btn-lg" onclick="change_map(this.id)" style="border: 2px solid #7e5686; color: #7e5686; font-weight: bold;" id="btn_T-PRO-2">T-PRO Lt 2</button>
			<button class="btn btn-default btn-lg" onclick="change_map(this.id)" style="border: 2px solid #7e5686; color: #7e5686; font-weight: bold;" id="btn_WH-1">WH Lt 1</button>
			<button class="btn btn-default btn-lg" onclick="change_map(this.id)" style="border: 2px solid #7e5686; color: #7e5686; font-weight: bold;" id="btn_WH-2">WH Lt 2</button>
			<button class="btn btn-default btn-lg" onclick="change_map(this.id)" style="border: 2px solid #7e5686; color: #7e5686; font-weight: bold;" id="btn_KNT-OFC">KNT+OFC</button>
		</center>
		<center style="margin-top: 5px">
			<button class="btn btn-default btn-lg" onclick="change_map(this.id)" style="border: 2px solid #7e5686; color: #7e5686; font-weight: bold;" id="btn_WWT">WWT</button>
			<button class="btn btn-default btn-lg" onclick="change_map(this.id)" style="border: 2px solid #7e5686; color: #7e5686; font-weight: bold;" id="btn_PLT-PNT">PLT+PNT</button>
			<button class="btn btn-default btn-lg" onclick="change_map(this.id)" style="border: 2px solid #7e5686; color: #7e5686; font-weight: bold;" id="btn_BUFF-TUMB">BUFF+TUMB</button>
			<button class="btn btn-default btn-lg" onclick="change_map(this.id)" style="border: 2px solid #7e5686; color: #7e5686; font-weight: bold;" id="btn_ASSY">ASSY</button>
			<button class="btn btn-default btn-lg" onclick="change_map(this.id)" style="border: 2px solid #7e5686; color: #7e5686; font-weight: bold;" id="btn_SLD">SLD</button>
			<button class="btn btn-default btn-lg" onclick="change_map(this.id)" style="border: 2px solid #7e5686; color: #7e5686; font-weight: bold;" id="btn_C-PRO">C-PRO</button>
			<button class="btn btn-default btn-lg" onclick="change_map(this.id)" style="border: 2px solid #7e5686; color: #7e5686; font-weight: bold;" id="btn_RPL-PNC">RPL+PNC</button>
		</center>
		<br>
	</div>

	<div class="col-xs-12">
		<div class="box box-solid">
			<div class="box-head">
				<h3 style="margin-top: 3px; text-align: center;" id="judul">RCD-INJ-PRESS</h3>
			</div>
			<div class="box-body">
				<img src='{{ asset("/maintenance/apar_map/1. RCD+INJ+PRESS.jpg") }}' type='image/jpg' width='100%' height='100%' id="RCD-INJ-PRESS" class="img"></img>
				<img src='{{ asset("/maintenance/apar_map/2. WKS+MTC.jpg") }}' type='image/jpg' width='100%' height='100%' style="display: none" id="WKS-MTC" class="img"></img>
				<img src='{{ asset("/maintenance/apar_map/3. T-PRO Lt 1.jpg") }}' type='image/jpg' width='100%' height='100%' style="display: none" id="T-PRO-1" class="img"></img>
				<img src='{{ asset("/maintenance/apar_map/4. T-PRO Lt 2.jpg") }}' type='image/jpg' width='100%' height='100%' style="display: none" id="T-PRO-2" class="img"></img>
				<img src='{{ asset("/maintenance/apar_map/5. WH Lt 1.jpg") }}' type='image/jpg' width='100%' height='100%' style="display: none" id="WH-1" class="img"></img>
				<img src='{{ asset("/maintenance/apar_map/6. WH Lt 2.jpg") }}' type='image/jpg' width='100%' height='100%' style="display: none" id="WH-2" class="img"></img>
				<img src='{{ asset("/maintenance/apar_map/7. KNT+OFC.jpg") }}' type='image/jpg' width='100%' height='100%' style="display: none" id="KNT-OFC" class="img"></img>
				<img src='{{ asset("/maintenance/apar_map/8. WWT.jpg") }}' type='image/jpg' width='100%' height='100%' style="display: none" id="WWT" class="img"></img>
				<img src='{{ asset("/maintenance/apar_map/9. PLT+PNT.jpg") }}' type='image/jpg' width='100%' height='100%' style="display: none" id="PLT-PNT" class="img"></img>
				<img src='{{ asset("/maintenance/apar_map/10. BUFF+TUMB.jpg") }}' type='image/jpg' width='100%' height='100%' style="display: none" id="BUFF-TUMB" class="img"></img>
				<img src='{{ asset("/maintenance/apar_map/11. ASSY.jpg") }}' type='image/jpg' width='100%' height='100%' style="display: none" id="ASSY" class="img"></img>
				<img src='{{ asset("/maintenance/apar_map/12. SLD.jpg") }}' type='image/jpg' width='100%' height='100%' style="display: none" id="SLD" class="img"></img>
				<img src='{{ asset("/maintenance/apar_map/13. C-PRO.jpg") }}' type='image/jpg' width='100%' height='100%' style="display: none" id="C-PRO" class="img"></img>
				<img src='{{ asset("/maintenance/apar_map/14. RPL+PNC.jpg") }}' type='image/jpg' width='100%' height='100%' style="display: none" id="RPL-PNC" class="img"></img>
			</div>
		</div>
	</div>
</div>

</section>

@endsection
@section('scripts')
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

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
	})


	function change_map(id) {
		ido = id.split("_")[1];
		console.log(ido);

		$("#judul").text(ido);

		$(".img").hide();
		$("#"+ido).show();
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