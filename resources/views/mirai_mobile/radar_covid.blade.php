@extends('layouts.display_2')

@section('stylesheets')
<style type="text/css">
	content, html, body {
		height: 100%;
	}
	body {
		margin: 0px;
		background-color: #333333;
	}
	.navbare {
		overflow: hidden;
		position: fixed;
		top: 0;
		width: 100%;
		background-color: #605ca8;
		z-index: 100;
		padding: 5px 0 5px 0;
	}

	#schedule {
		overflow-y: scroll;
		height: 610px;
		width: 1000px;
		/*margin-top: -60px;*/
		/*zoom: 2;
		-moz-transform: scale(2);
		-moz-transform-origin: 0 0;*/
		position:absolute; 
		clip:rect(0px,1110px,800px,50px);
		top:-50px; left:-50px;
	}
	
</style>
@endsection
		
		<div class="col-md-12 col-xs-12" style="margin-left: 0px; padding: 0px;">
			<div class="col-xs-12" style="margin-bottom: 1%;">
				<embed src="https://radarcovid19.jatimprov.go.id/" width="100%" height="100%" id="covid"></embed>
			</div>
		</div>

		<div class="col-lg-12" style="position: fixed;">
			<div class="row" style="margin-top: 40px;margin-right: 20px">
				<div class="col-xs-9" style="padding-bottom: 5px;">
					<div id="container1" class="container1" style="width: 100%;"></div>
				</div>
				<div class="col-xs-3" style="padding-left: 0;">
					<div class="box box-solid">
						<div class="box-header" style="background-color: #3f51b5;">
							<center><span style="font-size: 22px; font-weight: bold; color: white;">Jumlah Kasus Indonesia</span></center>
						</div>
						<ul class="nav nav-pills nav-stacked">
							<li>
								<a href="#" style="font-size: 18px; font-weight: bold; padding-top: 5px; padding-bottom: 5px;">Kasus Sembuh
									<span class="pull-right text-green" id="sembuh_indo" style="font-size: 1.5vw;">0</span>
								</a>
							</li>
							<li>
								<a href="#" style="font-size: 18px; font-weight: bold; padding-top: 5px; padding-bottom: 5px;">Kasus Meninggal
									<span class="pull-right text-red" id="meninggal_indo" style="font-size: 1.5vw;">0</span>
								</a>
							</li>
							<li>
								<a href="#" style="font-size: 18px; font-weight: bold;">Total Kasus Positif
									<span class="pull-right text-red" id="total_indo" style="font-size: 2.5vw;">0</span>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>

			<div class="row" style="margin-right: 20px">
				<div class="col-xs-9" style="padding-bottom: 5px;">
					<div id="container1" class="container1" style="width: 100%;"></div>
				</div>
				<div class="col-xs-3" style="padding-left: 0;">
					<div class="box box-solid">
						<div class="box-header" style="background-color: #00a65a;">
							<center><span style="font-size: 22px; font-weight: bold; color: white;">Jumlah Kasus Jawa Timur</span></center>
						</div>
						<ul class="nav nav-pills nav-stacked">
							<li>
								<a href="#" style="font-size: 18px; font-weight: bold; padding-top: 5px; padding-bottom: 5px;">Kasus Sembuh
									<span class="pull-right text-green" id="sembuh" style="font-size: 1.5vw;">0</span>
								</a>
							</li>
							<li>
								<a href="#" style="font-size: 18px; font-weight: bold; padding-top: 5px; padding-bottom: 5px;">Kasus Meninggal
									<span class="pull-right text-red" id="meninggal" style="font-size: 1.5vw;">0</span>
								</a>
							</li>
							<li>
								<a href="#" style="font-size: 18px; font-weight: bold;">Total Kasus Positif
									<span class="pull-right text-red" id="total" style="font-size: 2.5vw;">0</span>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>


<!-- <div class="navbare">
	<center><span style="color: white; font-size: 4vw; font-weight: bold;">RADAR COVID 19</span></center>
</div> -->

<script type="text/javascript">
	// window.setInterval("reloadIFrame();", 1800000);

	function reloadIFrame() {
		document.getElementById("covid").src="https://radarcovid19.jatimprov.go.id/";	
	}

		//Indonesia

		var request2 = new XMLHttpRequest()
		// Open a new connection, using the GET request on the URL endpoint
		request2.open('GET', 'https://api.kawalcorona.com/indonesia/', true)

		request2.onload = function() {
		  var data2 = JSON.parse(this.response)

			if (request2.status >= 200 && request2.status < 400) {
				$('#total_indo').html(data2[0].positif+ '<span style="font-size: 20px"> kasus</span>');
				$('#sembuh_indo').html(data2[0].sembuh+ '<span style="font-size: 20px"> kasus</span>');
				$('#meninggal_indo').html(data2[0].meninggal+ '<span style="font-size: 20px"> kasus</span>');
				console.log();
			} else {
			  console.log('error')
			}
		}
		// Send request
		request2.send();


		//Jawa Timur

		var request = new XMLHttpRequest()
		// Open a new connection, using the GET request on the URL endpoint
		request.open('GET', 'https://api.kawalcorona.com/indonesia/provinsi/', true)

		request.onload = function() {
		  var data = JSON.parse(this.response)

			if (request.status >= 200 && request.status < 400) {
				for(var i = 0; i < data.length; i++){
					if(data[i].attributes.Provinsi == "Jawa Timur"){
						$('#total').html(data[i].attributes.Kasus_Posi+ '<span style="font-size: 20px"> kasus</span>');
						$('#sembuh').html(data[i].attributes.Kasus_Semb+ '<span style="font-size: 20px"> kasus</span>');
						$('#meninggal').html(data[i].attributes.Kasus_Meni+ '<span style="font-size: 20px"> kasus</span>');
					}
				}
			} else {
			  console.log('error')
			}
		}
		// Send request
		request.send();

</script>