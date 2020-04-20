@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
</style>
@endsection
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">
</section>
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="padding-top: 0;">
			<ul class="timeline">
				<li class="time-label">
					<span style="background-color: #00a65a; color: white;">
						24 January 2019
					</span>
				</li>
				<li>
					<i class="fa fa-info-circle" style="background-color: #00a65a; color: white;"></i>
					<div class="timeline-item">
						<h3 class="timeline-header" style="color: #00a65a; font-weight: bold;">Yamaha Group Helpline</h3>
						<div class="timeline-body">
							Karyawan dapat menyampaikan informasi terkait tindakan ketidaksesuaian terhadap Kode Etik Kepatuhan (Compliance Code of Conduct) pada link berikut:
							<br>
							<a href="http://ml.helpline.jp/yamahacompliance/"><i class="fa fa-angle-double-right"></i><i class="fa fa-angle-double-right"></i> Link Yamaha Helpline <i class="fa fa-angle-double-left"></i><i class="fa fa-angle-double-left"></i></a>
						</div>
					</div>
				</li>
				<li class="time-label">
					<span style="background-color: #605ca8; color: white;">
						01 January 2020
					</span>
				</li>
				<li>
					<i class="fa fa-info-circle" style="background-color: #605ca8; color: white;"></i>
					<div class="timeline-item">
						<h3 class="timeline-header" style="color: #605ca8; font-weight: bold;">Sunfish</h3>
						<div class="timeline-body">
							Diinformasikan bahwa per tanggal <i style="color: red;">01 Januari 2020</i>, pembuatan <i style="color: red;">form lembur</i> menggunakan <i style="color: red;">Sunfish</i> pada link berikut:
							<br>
							<a href="http://172.17.128.8/sf6/"><i class="fa fa-angle-double-right"></i><i class="fa fa-angle-double-right"></i> Link Sunfish Overtime <i class="fa fa-angle-double-left"></i><i class="fa fa-angle-double-left"></i></a>
							<br>
							<br>
							Data HRq belum terupdate karena sedang dalam masa transisi ke data Sunfish.
							Informasi terkait HR dapat dilihat pada aplikasi GreatDay.
						</div>
					</div>
				</li>
				<li>
					<i class="fa fa-dot-circle-o bg-gray"></i>
				</li>
			</ul>
		</div>
	</div>
</div>
</section>
@endsection
@section('scripts')
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){


	});
</script>
@endsection